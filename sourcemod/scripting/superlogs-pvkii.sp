/**
 * HLstatsX Community Edition - SourceMod plugin to generate advanced weapon logging
 * http://www.hlxcommunity.com
 * Copyright (C) 2009-2010 Nicholas Hastings (psychonic)
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

#define NAME "SuperLogs: PVKII"
#define VERSION "1.0.1"

#define MAX_LOG_WEAPONS 6
#define MAX_WEAPON_LEN 14


new g_weapon_stats[MAXPLAYERS+1][MAX_LOG_WEAPONS][15];
new const String:g_weapon_list[MAX_LOG_WEAPONS][MAX_WEAPON_LEN] = { 
									"blunderbuss",
									"flintlock",
									"arrow",
									"crossbow_bolt",
									"throwaxe",
									"javalin"
								};
								
new Handle:g_cvar_ktraj = INVALID_HANDLE;

new bool:g_logktraj = true;

new bool:g_bHasChest[MAXPLAYERS+1] = {false,...};
new bool:g_bHasGrail[MAXPLAYERS+1] = {false,...};

new g_iLastClass = -1;

new const String:g_szClassNames[][] = {
	"Skirmisher",
	"Captain",
	"",
	"Berserker",
	"Huscarl",
	"Gestir",
	"Heavy Knight",
	"Archer"
};

#define PVKII

#include <loghelper>
#include <wstatshelper>


public Plugin:myinfo = {
	name = NAME,
	author = "psychonic",
	description = "Advanced logging for PVKII. Generates auxilary logging for use with log parsers such as HLstatsX and Psychostats",
	version = VERSION,
	url = "http://www.hlxce.com"
};

#if SOURCEMOD_V_MAJOR >= 1 && SOURCEMOD_V_MINOR >= 3
public APLRes:AskPluginLoad2(Handle:myself, bool:late, String:error[], err_max)
#else
public bool:AskPluginLoad(Handle:myself, bool:late, String:error[], err_max)
#endif
{
	decl String:szGameDesc[64];
	GetGameDescription(szGameDesc, sizeof(szGameDesc), true);
	if (StrContains(szGameDesc, "PVKII", false) == -1)
	{
		decl String:szGameDir[64];
		GetGameFolderName(szGameDir, sizeof(szGameDir));
		if (StrContains(szGameDir, "pvkii", false) == -1)
		{
			strcopy(error, err_max, "This plugin is only supported on Pirate, Vikings, and Knights");
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
	
	g_cvar_ktraj = CreateConVar("superlogs_ktraj", "0", "Enable Psychostats \"KTRAJ\" logging (default off)", 0, true, 0.0, true, 1.0);
	HookConVarChange(g_cvar_ktraj, OnCvarKtrajChange);
	CreateConVar("superlogs_pvkii_version", VERSION, NAME, FCVAR_SPONLY|FCVAR_REPLICATED|FCVAR_NOTIFY);
	
	HookEvent("player_ranged_impact", Event_PlayerRangedImpact);
	HookEvent("player_death", Event_PlayerDeath);
	HookEvent("player_spawn", Event_PlayerSpawn);
	HookEvent("update_mvp_panel", Event_RoundEnd);
	HookEvent("player_disconnect", Event_PlayerDisconnect, EventHookMode_Pre);
	
	HookEvent("player_nemesis", Event_PlayerNemesis);
	HookEvent("player_revenge", Event_PlayerRevenge);
	HookEvent("player_objective", Event_PlayerObjective);
	HookEvent("grail_pickup", Event_GrailPickup);
	HookEvent("grail_drop", Event_GrailDrop);
	HookEvent("chest_pickup", Event_ChestPickup);
	HookEvent("chest_drop", Event_ChestDrop);
	HookEvent("chest_capture", Event_ChestCapture);
		
	CreateTimer(1.0, LogMap);
}

public OnMapStart()
{
	GetTeams();
}

public OnClientPutInServer(client)
{
	g_iLastClass = -1;
	reset_player_stats(client);
	g_bHasChest[client] = false;
	g_bHasGrail[client] = false;
}

public Event_PlayerNemesis(Handle:event, const String:name[], bool:dontBroadcast)
{
	LogPlyrPlyrEvent(GetClientOfUserId(GetEventInt(event, "userid")), GetClientOfUserId(GetEventInt(event, "victim")), "triggered", "domination");
}

public Event_PlayerRevenge(Handle:event, const String:name[], bool:dontBroadcast)
{
	LogPlyrPlyrEvent(GetClientOfUserId(GetEventInt(event, "userid")), GetClientOfUserId(GetEventInt(event, "victim")), "triggered", "revenge");
}

public Event_PlayerObjective(Handle:event, const String:name[], bool:dontBroadcast)
{
	LogPlayerEvent(GetClientOfUserId(GetEventInt(event, "userid")), "triggered", "obj_complete");
}

public Event_ChestPickup(Handle:event, const String:name[], bool:dontBroadcast)
{
	g_bHasChest[GetClientOfUserId(GetEventInt(event, "userid"))] = true;
}

public Event_ChestDrop(Handle:event, const String:name[], bool:dontBroadcast)
{
	g_bHasChest[GetClientOfUserId(GetEventInt(event, "userid"))] = false;
}

public Event_GrailPickup(Handle:event, const String:name[], bool:dontBroadcast)
{
	g_bHasGrail[GetClientOfUserId(GetEventInt(event, "userid"))] = true;
}

public Event_GrailDrop(Handle:event, const String:name[], bool:dontBroadcast)
{
	g_bHasGrail[GetClientOfUserId(GetEventInt(event, "userid"))] = false;
}

public Event_ChestCapture(Handle:event, const String:name[], bool:dontBroadcast)
{
	LogPlayerEvent(GetClientOfUserId(GetEventInt(event, "userid")), "triggered", "chest_capture");
}

public Event_PlayerRangedImpact(Handle:event, const String:name[], bool:dontBroadcast)
{
	// "userid"	"short"		// user ID of player who fired
	// "victim"	"short"		// entindex of entity that was hit (if any)
	// "weapon"	"string"	// weapon that was fired
	// "damage"	"float"		// how much damage was dealt, if 0 obviously missed or blocked by shield if victim is set
	
	new attacker = GetClientOfUserId(GetEventInt(event, "userid"));
	if (attacker == 0 || !IsClientInGame(attacker))
		return;
	
	decl String:weapon[MAX_WEAPON_LEN];
	GetEventString(event, "weapon", weapon, sizeof(weapon));
	new weapon_index = get_weapon_index(weapon);
	if (weapon_index == -1)
		return;
	
	if (!strcmp(weapon, "blunderbuss"))
	{
		// buckshot of 8
		g_weapon_stats[attacker][weapon_index][LOG_HIT_SHOTS] += 8;
	}
	else
	{
		g_weapon_stats[attacker][weapon_index][LOG_HIT_SHOTS]++;
	}
	
	new victim = GetEventInt(event, "victim");
	if (victim < 1 || victim > MaxClients || !IsClientInGame(victim))
		return;
	
	g_weapon_stats[attacker][weapon_index][LOG_HIT_HITS]++;
	g_weapon_stats[attacker][weapon_index][LOG_HIT_DAMAGE] += RoundToNearest(GetEventFloat(event, "damage"));
}

public Event_PlayerDeath(Handle:event, const String:name[], bool:dontBroadcast)
{
	// this extents the original player_death by a new fields
	// "userid"        "short"         // user ID who died                             
	// "attacker"      "short"         // user ID who killed
	// "weapon"        "string"        // weapon name killer used 
	
	new victim   = GetClientOfUserId(GetEventInt(event, "userid"));
	new attacker = GetClientOfUserId(GetEventInt(event, "attacker"));
	decl String: weapon[MAX_WEAPON_LEN];
	GetEventString(event, "weapon", weapon, sizeof(weapon));
	
	if (victim > 0 && attacker > 0)
	{
		new weapon_index = get_weapon_index(weapon);
		if (weapon_index > -1)
		{
			g_weapon_stats[attacker][weapon_index][LOG_HIT_KILLS]++;
			g_weapon_stats[victim][weapon_index][LOG_HIT_DEATHS]++;
			if (GetClientTeam(attacker) == GetClientTeam(victim))
			{
				g_weapon_stats[attacker][weapon_index][LOG_HIT_TEAMKILLS]++;
			}
		}
		dump_player_stats(victim);
		
		LogPlyrPlyrEvent(GetEventInt(event, "assistid"), victim, "triggered", "kill assist", true);
		
		if (g_bHasGrail[victim])
		{
			LogPlayerEvent(attacker, "triggered", "grail_defend");
		}
		if (g_bHasChest[victim])
		{
			LogPlayerEvent(attacker, "triggered", "chest_defend");
		}
		g_bHasGrail[victim] = false;
		g_bHasChest[victim] = false;
	}
	
	if (g_logktraj)
	{
		LogPSKillTraj(attacker, victim, weapon);
	}
}

public Event_PlayerSpawn(Handle:event, const String:name[], bool:dontBroadcast)
{
	// "userid"        "short"         // user ID on server          

	new client = GetClientOfUserId(GetEventInt(event, "userid"));
	if (client > 0)
	{
		reset_player_stats(client);
		if (IsClientInGame(client))
		{
			new iCurrentClass = GetEntProp(client, Prop_Send, "m_iPlayerClass");
			if (iCurrentClass > -1 && iCurrentClass != g_iLastClass)
			{
				LogRoleChange(client, g_szClassNames[iCurrentClass]);
			}
			g_iLastClass = iCurrentClass;
		}
	}
}

public Event_RoundEnd(Handle:event, const String:name[], bool:dontBroadcast)
{
	new winner = GetEventInt(event, "winner");
	if (winner > 1)
	{
		LogTeamEvent(winner, "triggered", "Round_Win");
	}
	
	LogPlayerEvent(GetEventInt(event, "pid_1"), "triggered", "mvp1");
	LogPlayerEvent(GetEventInt(event, "pid_2"), "triggered", "mvp2");
	LogPlayerEvent(GetEventInt(event, "pid_3"), "triggered", "mvp3");
	
	for (new i = 1; i <= MaxClients; i++)
	{
		g_bHasChest[i] = false;
		g_bHasGrail[i] = false;
	}
	
	WstatsDumpAll();
}

public Action:Event_PlayerDisconnect(Handle:event, const String:name[], bool:dontBroadcast)
{
	new client = GetClientOfUserId(GetEventInt(event, "userid"));
	OnPlayerDisconnect(client);
	return Plugin_Continue;
}


public Action:LogMap(Handle:timer)
{
	// Called 1 second after OnPluginStart since srcds does not log the first map loaded. Idea from Stormtrooper's "mapfix.sp" for psychostats
	LogMapLoad();
}

public OnCvarKtrajChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	g_logktraj = GetConVarBool(g_cvar_ktraj);
}