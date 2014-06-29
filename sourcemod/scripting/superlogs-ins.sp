/**
 * HLstatsX Community Edition - SourceMod plugin to generate advanced weapon logging
 * http://www.hlxcommunity.com
 * Copyright (C) 2009 Nicholas Hastings (psychonic)
 * Copyright (C) 2007-2008 TTS Oetzel & Goerz GmbH
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

#pragma semicolon 1
 
#include <sourcemod>
#include <sdktools>

#define NAME "SuperLogs: Insurgency"
#define VERSION "1.1.4"

#define MAX_LOG_WEAPONS 19
#define MAX_WEAPON_LEN 8

#define PREFIX_LEN 7

#define INS

new g_weapon_stats[MAXPLAYERS+1][MAX_LOG_WEAPONS][15];
new const String: g_weapon_list[MAX_LOG_WEAPONS][MAX_WEAPON_LEN] = {
									"makarov",
									"m9",
									"sks",
									"m1014",
									"toz",
									"svd",
									"rpk",
									"m249",
									"m16m203",
									"l42a1",
									"m4med",
									"m4",
									"m16a4",
									"m14",
									"fnfal",
									"aks74u",
									"ak47",
									"kabar",
									"bayonet"
								};
								
new Handle:g_cvar_wstats = INVALID_HANDLE;
new Handle:g_cvar_actions = INVALID_HANDLE;
new Handle:g_cvar_headshots = INVALID_HANDLE;
new Handle:g_cvar_chat = INVALID_HANDLE;
new Handle:g_cvar_captures = INVALID_HANDLE;
new Handle:g_cvar_locations = INVALID_HANDLE;
new Handle:g_cvar_ktraj = INVALID_HANDLE;

new bool:g_logwstats = true;
new bool:g_logheadshots = true;
new bool:g_logactions = true;
new bool:g_logchat = true;
new bool:g_logcaptures = true;
new bool:g_loglocations = true;
new bool:g_logktraj = false;

new g_client_last_weapon[MAXPLAYERS+1] = {-1, ...};
new String:g_client_last_weaponstring[MAXPLAYERS+1][64];

#include <loghelper>
#include <wstatshelper>


public Plugin:myinfo = {
	name = NAME,
	author = "psychonic",
	description = "Advanced logging for Insurgency. Generates auxilary logging for use with log parsers such as HLstatsX and Psychostats",
	version = VERSION,
	url = "http://www.hlxcommunity.com"
};

#if SOURCEMOD_V_MAJOR >= 1 && SOURCEMOD_V_MINOR >= 3
public APLRes:AskPluginLoad2(Handle:myself, bool:late, String:error[], err_max)
#else
public bool:AskPluginLoad(Handle:myself, bool:late, String:error[], err_max)
#endif
{
	decl String:game_description[64];
	GetGameDescription(game_description, sizeof(game_description), true);
	if (StrContains(game_description, "Insurgency", false) == -1)
	{
		decl String:game_folder[64];
		GetGameFolderName(game_folder, sizeof(game_folder));
		if (StrContains(game_folder, "insurgency", false) == -1)
		{
			#if SOURCEMOD_V_MAJOR >= 1 && SOURCEMOD_V_MINOR >= 3
				return APLRes_Failure;
			#else
				return false;
			#endif
		}
	}
#if SOURCEMOD_V_MAJOR >= 1 && SOURCEMOD_V_MINOR >= 3
	return APLRes_Success;
#else
	return true;
#endif
}

public OnPluginStart()
{
	CreatePopulateWeaponTrie();
	
	g_cvar_wstats = CreateConVar("superlogs_wstats", "1", "Enable logging of weapon stats (default on)", 0, true, 0.0, true, 1.0);
	g_cvar_actions = CreateConVar("superlogs_actions", "1", "Enable logging of actions, such as \"Round_Win\" (default on)", 0, true, 0.0, true, 1.0);
	g_cvar_headshots = CreateConVar("superlogs_headshots", "1", "Enable logging of headshot player action (default on)", 0, true, 0.0, true, 1.0);
	g_cvar_chat = CreateConVar("superlogs_chat", "1", "Enable logging of chat (default on)", 0, true, 0.0, true, 1.0);
	g_cvar_captures = CreateConVar("superlogs_captures", "1", "Enable logging of capturing objectives (default on)", 0, true, 0.0, true, 1.0);
	g_cvar_locations = CreateConVar("superlogs_locations", "1", "Enable logging of location on player death (default on)", 0, true, 0.0, true, 1.0);
	g_cvar_ktraj = CreateConVar("superlogs_ktraj", "0", "Enable Psychostats \"KTRAJ\" logging (default off)", 0, true, 0.0, true, 1.0);
	HookConVarChange(g_cvar_wstats, OnCvarWstatsChange);
	HookConVarChange(g_cvar_actions, OnCvarActionsChange);
	HookConVarChange(g_cvar_headshots, OnCvarHeadshotsChange);
	HookConVarChange(g_cvar_chat, OnCvarChatChange);
	HookConVarChange(g_cvar_captures, OnCvarCapturesChange);
	HookConVarChange(g_cvar_locations, OnCvarLocationsChange);
	HookConVarChange(g_cvar_ktraj, OnCvarKtrajChange);
	CreateConVar("superlogs_ins_version", VERSION, NAME, FCVAR_SPONLY|FCVAR_REPLICATED|FCVAR_NOTIFY);
		
	hook_wstats();
	HookUserMessage(GetUserMessageId("ObjMsg"), objmsg);
	HookEvent("player_hurt", Event_PlayerHurt);
	HookEvent("round_end", Event_RoundEnd);
	HookEvent("player_death", Event_PlayerDeathPre, EventHookMode_Pre);
	HookEvent("player_death", Event_PlayerDeath);
	
	RegConsoleCmd("say2", Command_Chat);
		
	CreateTimer(1.0, LogMap);
	
	GetTeams(true);
}


public OnMapStart()
{
	GetTeams(true);
}


hook_wstats()
{
	HookEvent("player_spawn", Event_PlayerSpawn);
	HookEvent("player_disconnect", Event_PlayerDisconnect, EventHookMode_Pre);
}

unhook_wstats()
{
	UnhookEvent("player_spawn", Event_PlayerSpawn);
	UnhookEvent("player_disconnect", Event_PlayerDisconnect, EventHookMode_Pre);
}

public OnClientPutInServer(client)
{
	reset_player_stats(client);
}

public Event_PlayerHurt(Handle:event, const String:name[], bool:dontBroadcast)
{
	//  "userid"		"short"		// user ID on server
	//  "attacker"		"short"		// CLIENT INDEX! on server of the attacker
	//  "dmg_health"		"short"		// lost health points
	//  "hitgroup"		"short"			// Hit groups
	//  "weapon"		"string"		// Weapon name, like WEAPON_AK47

	new attacker  = GetEventInt(event, "attacker");
	new victim = GetEventInt(event, "userid");

	if (attacker > 0 && attacker != victim)
	{
		// ... wtf insurgency... userid is userid and attacker is client index?
		//attacker = GetClientOfUserId(attacker);
		
		new hitgroup  = GetEventInt(event, "hitgroup");
		if (hitgroup < 8)
		{
			hitgroup += LOG_HIT_OFFSET;
		}
		
		if (g_logwstats)
		{
			decl String:weapon[MAX_WEAPON_LEN+PREFIX_LEN];
			GetEventString(event, "weapon", weapon, sizeof(weapon));

			new weapon_index = get_weapon_index(weapon[PREFIX_LEN]);

			if (weapon_index > -1)  {
				g_weapon_stats[attacker][weapon_index][LOG_HIT_HITS]++;
				g_weapon_stats[attacker][weapon_index][LOG_HIT_DAMAGE]  += GetEventInt(event, "dmg_health");
				g_weapon_stats[attacker][weapon_index][hitgroup]++;
				
				if (hitgroup == (HITGROUP_HEAD+LOG_HIT_OFFSET))
				{
					g_weapon_stats[attacker][weapon_index][LOG_HIT_HEADSHOTS]++;
				}
				g_client_last_weapon[attacker] = weapon_index;
				g_client_last_weaponstring[attacker] = weapon;
			}
		}
		
		if (g_logheadshots && hitgroup == (HITGROUP_HEAD+LOG_HIT_OFFSET))
		{
			LogPlayerEvent(attacker, "triggered", "headshot");
		}
	}
}

public Action:Event_PlayerDeathPre(Handle:event, const String:name[], bool:dontBroadcast)
{
	LogKillLoc(GetClientOfUserId(GetEventInt(event, "attacker")), GetClientOfUserId(GetEventInt(event, "userid")));
	
	return Plugin_Continue;
}

public Event_PlayerDeath(Handle:event, const String:name[], bool:dontBroadcast)
{
	//  "userid"	"short"   	// user ID who died
	//  "attacker"	"short"	 	// user ID who killed
	//  "type"		"byte"		// type of death
	//  "nodeath"	"bool"		// true if death messages were off when player died

	new victim   = GetClientOfUserId(GetEventInt(event, "userid"));
	new attacker = GetClientOfUserId(GetEventInt(event, "attacker"));
	if (attacker == 0 || victim == 0 || attacker == victim)
	{
		return;
	}
	
	if (g_logwstats)
	{
		new weapon_index = g_client_last_weapon[attacker];
		if (weapon_index > -1)
		{
			g_weapon_stats[attacker][weapon_index][LOG_HIT_KILLS]++;
			g_weapon_stats[victim][weapon_index][LOG_HIT_DEATHS]++;
			if (GetClientTeam(attacker) == GetClientTeam(victim))
			{
				g_weapon_stats[attacker][weapon_index][LOG_HIT_TEAMKILLS]++;
			}
			dump_player_stats(victim);
		}
	}
	
	if (g_logktraj)
	{
		LogPSKillTraj(attacker, victim, g_client_last_weaponstring[attacker]);
	}
}

public Event_PlayerSpawn(Handle:event, const String:name[], bool:dontBroadcast)
{
	// "userid"        "short"         // user ID on server          

	new client = GetClientOfUserId(GetEventInt(event, "userid"));
	if (client > 0)
	{
		reset_player_stats(client);
	}
}

public Event_RoundEnd(Handle:event, const String:name[], bool:dontBroadcast)
{
	if (g_logwstats)
	{
		WstatsDumpAll();
	}

	if (g_logactions)
	{
		LogTeamEvent(GetEventInt(event, "winner"), "triggered", "Round_Win");
	}
}

public Action:Event_PlayerDisconnect(Handle:event, const String:name[], bool:dontBroadcast)
{
	new client = GetClientOfUserId(GetEventInt(event, "userid"));
	OnPlayerDisconnect(client);
	return Plugin_Continue;
}


public Action:objmsg(UserMsg:msg_id, Handle:bf, const players[], playersNum, bool:reliable, bool:init)
{
	new point = BfReadByte(bf); // Objective Point: 1 = point A, 2 = point B, 3 = point C, etc.
	new capstatus = BfReadByte(bf); // Capture Status: 1 on starting capture, 2 on finished capture
	new team = BfReadByte(bf); // Team Index: 1 = Marines, 2 = Insurgents
	
	if (capstatus == 2)
	{
		switch (point)
		{
			case 1:
				LogTeamEvent(team, "triggered", "captured_a");
			case 2:
				LogTeamEvent(team, "triggered", "captured_b");
			case 3:
				LogTeamEvent(team, "triggered", "captured_c");
			case 4:
				LogTeamEvent(team, "triggered", "captured_d");
			case 5:
				LogTeamEvent(team, "triggered", "captured_e");
		}
	}
	
	return Plugin_Continue;
}


public Action:Command_Chat(client, args)
{
	// method partially taken from "Insurgency Chat" by "Stevo.TVR"

	if (g_logchat)
	{
		new String:message[192];
		new startidx = 4;
		
		if (GetCmdArgString(message, sizeof(message)) < 1 || client == 0)
		{
			return Plugin_Continue;
		}
		
		new lastchar = strlen(message) - 1;
		if (message[lastchar] == '"')
		{
			message[lastchar] = '\0';
			startidx += 1;
		}
		
		if (message[0] == '1')
		{
			LogPlayerEvent(client, "say", message[startidx], false);
		}
		else
		{
			LogPlayerEvent(client, "say_team", message[startidx], false);
		}
	}
	return Plugin_Continue;
}


public Action:LogMap(Handle:timer)
{
	// Called 1 second after OnPluginStart since srcds does not log the first map loaded. Idea from Stormtrooper's "mapfix.sp" for psychostats
	LogMapLoad();
}


public OnCvarWstatsChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	new bool:old_value = g_logwstats;
	g_logwstats = GetConVarBool(g_cvar_wstats);
	
	if (old_value != g_logwstats)
	{
		if (g_logwstats)
		{
			hook_wstats();
			if (!g_logheadshots)
			{
				HookEvent("player_hurt", Event_PlayerHurt);
			}
			if (!g_logactions)
			{
				HookEvent("round_end", Event_RoundEnd);
			}
			if (!g_logktraj)
			{
				HookEvent("player_death", Event_PlayerDeath);
			}
		}
		else
		{
			unhook_wstats();
			if (!g_logheadshots)
			{
				UnhookEvent("player_hurt", Event_PlayerHurt);
			}
			if (!g_logactions)
			{
				UnhookEvent("round_end", Event_RoundEnd);
			}
			if (!g_logktraj)
			{
				UnhookEvent("player_death", Event_PlayerDeath);
			}
		}
	}
}


public OnCvarActionsChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	new bool:old_value = g_logactions;
	g_logactions = GetConVarBool(g_cvar_actions);
	
	if (old_value != g_logactions)
	{
		if (g_logactions && !g_logwstats)
		{
			HookEvent("round_end", Event_RoundEnd);
		}
		else if (!g_logwstats)
		{
			UnhookEvent("round_end", Event_RoundEnd);
		}
	}
}


public OnCvarHeadshotsChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	new bool:old_value = g_logheadshots;
	g_logheadshots = GetConVarBool(g_cvar_headshots);
	
	if (old_value != g_logheadshots)
	{
		if (g_logheadshots && !g_logwstats)
		{
			HookEvent("player_hurt", Event_PlayerHurt);
		}
		else if (!g_logwstats)
		{
			UnhookEvent("player_hurt", Event_PlayerHurt);
		}
	}
}


public OnCvarCapturesChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	new bool:old_value = g_logcaptures;
	g_logcaptures = GetConVarBool(g_cvar_captures);
	
	if (old_value != g_logcaptures)
	{
		if (g_logcaptures)
		{
			HookUserMessage(GetUserMessageId("ObjMsg"), objmsg);
		}
		else
		{
			UnhookUserMessage(GetUserMessageId("ObjMsg"), objmsg);
		}
	}
}


public OnCvarChatChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	g_logchat = GetConVarBool(g_cvar_chat);
}

public OnCvarLocationsChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	new bool:old_value = g_loglocations;
	g_loglocations = GetConVarBool(g_cvar_locations);
	
	if (old_value != g_loglocations)
	{
		if (g_loglocations)
		{
			HookEvent("player_death", Event_PlayerDeathPre, EventHookMode_Pre);
		}
		else
		{
			UnhookEvent("player_death", Event_PlayerDeathPre, EventHookMode_Pre);
		}
	}
}

public OnCvarKtrajChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	new bool:old_value = g_logktraj;
	g_logktraj = GetConVarBool(g_cvar_ktraj);
	
	if (old_value != g_logktraj)
	{
		if (g_logktraj && !g_logwstats)
		{
			HookEvent("player_death", Event_PlayerDeath);
		}
		else if (!g_logwstats)
		{
			UnhookEvent("player_death", Event_PlayerDeath);
		}
	}
}