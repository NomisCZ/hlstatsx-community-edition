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

#define NAME "SuperLogs: Age of Chivalry"
#define VERSION "1.0.2"

new Handle:g_cvar_headshots = INVALID_HANDLE;
new Handle:g_cvar_locations = INVALID_HANDLE;
new Handle:g_cvar_classchanges = INVALID_HANDLE;
new Handle:g_cvar_actions = INVALID_HANDLE;

new bool:g_logheadshots = true;
new bool:g_loglocations = true;
new bool:g_logclasschanges = true;
new bool:g_logactions = true;

new bool:g_bLogClassNextSpawn[MAXPLAYERS+1];

#include <loghelper>

public Plugin:myinfo = {
	name = NAME,
	author = "psychonic",
	description = "Advanced logging for Age of Chivalry. Generates auxilary logging for use with log parsers such as HLstatsX and Psychostats",
	version = VERSION,
	url = "http://www.hlxcommunity.com"
};


public OnPluginStart()
{
	g_cvar_locations = CreateConVar("superlogs_locations", "1", "Enable logging of kill coordinates (default on)", 0, true, 0.0, true, 1.0);
	g_cvar_headshots = CreateConVar("superlogs_headshots", "1", "Enable logging of headshot and decapitation actions (default on)", 0, true, 0.0, true, 1.0);
	g_cvar_classchanges = CreateConVar("superlogs_classchanges", "1", "Enable logging of character changes (default on)", 0, true, 0.0, true, 1.0);
	g_cvar_actions = CreateConVar("superlogs_actions", "1", "Enable logging of actions, such as \"Round_Win\" (default on)", 0, true, 0.0, true, 1.0);

	HookConVarChange(g_cvar_locations, OnCvarLocationsChange);
	HookConVarChange(g_cvar_headshots, OnCvarHeadshotsChange);
	HookConVarChange(g_cvar_classchanges, OnCvarClasschangesChange);

	CreateConVar("superlogs_aoc_version", VERSION, NAME, FCVAR_SPONLY|FCVAR_REPLICATED|FCVAR_NOTIFY);
		
	HookEvent("player_death", Event_PlayerDeathPre, EventHookMode_Pre);
	HookEvent("round_end", Event_RoundEnd);
	hook_classchanges();
	
	CreateTimer(1.0, LogMap);
	
	GetTeams();
}

hook_classchanges()
{
	HookUserMessage(GetUserMessageId("ClassChanged"), Event_ClassChanged);
	HookEvent("player_spawn", Event_Spawn, EventHookMode_Pre);
	
	for (new i = 1; i <= MAXPLAYERS; i++)
	{
		g_bLogClassNextSpawn[i] = false;
	}
}

unhook_classchanges()
{
	UnhookUserMessage(GetUserMessageId("ClassChanged"), Event_ClassChanged);
	UnhookEvent("player_spawn", Event_Spawn, EventHookMode_Pre);
}


public OnMapStart()
{
	GetTeams();
}

public Action:Event_ClassChanged(UserMsg:msg_id, Handle:bf, const players[], playersNum, bool:reliable, bool:init)
{
	g_bLogClassNextSpawn[players[0]] = true;
	return Plugin_Continue;
}

public Action:Event_Spawn(Handle:event, const String:name[], bool:dontBroadcast)
{
	new client = GetClientOfUserId(GetEventInt(event, "userid"));
	if (client > 0 && g_bLogClassNextSpawn[client])
	{
		switch (GetEntProp(client, Prop_Send, "m_iClass"))
		{
			case 0:
				LogRoleChange(client, "Longbowman");
			case 1:
				LogRoleChange(client, "Crossbowman");
			case 2:
				LogRoleChange(client, "Javelineer");
			case 3:
				LogRoleChange(client, "Man at Arms");
			case 4:
				LogRoleChange(client, "Sergeant");
			case 5:
				LogRoleChange(client, "Guardsman");
			case 6:
				LogRoleChange(client, "Crusader");
			case 7:
				LogRoleChange(client, "Knight");
			case 8:
				LogRoleChange(client, "Heavy Knight");
		}
		g_bLogClassNextSpawn[client] = false;
	}
	
	return Plugin_Continue;
}


public Action:Event_PlayerDeathPre(Handle:event, const String:name[], bool:dontBroadcast)
{
	//	"userid"	"short"   	// user ID who died				
	//	"attacker"	"short"	 	// user ID who killed
	//	"weapon"	"string" 	// weapon name killed used
	//	"printweapon"	"string" 	// full print weapon name killed used
	//	"hitgroup"	"long"		// Part of body that was hit
	//	"damagetype" "long"		// CDamageInfo.GetAOCDamageType()
	//	"weaponid"	"short"		// Weapon ID
	//	"team"		"short"		// victim's team
	
	new attacker = GetEventInt(event, "attacker");
	new victim = GetEventInt(event, "userid");
	
	if (attacker > 0 && victim > 0)
	{
		if (g_loglocations)
		{
			LogKillLoc(GetClientOfUserId(attacker), GetClientOfUserId(victim));
		}
		
		if (g_logheadshots)
		{
			new bool:headshot = GetEventBool(event, "headshot");
			new bool:decapped = GetEventBool(event, "decapped");
			new bool:headexplodie = GetEventBool(event, "headexplodie");
			if (decapped)
			{
				//decapitation
				LogPlayerEvent(GetClientOfUserId(attacker), "triggered", "headshot", true, " (hstype \"decap\")");
			}
			else if (headshot)
			{
				//headshot
				LogPlayerEvent(GetClientOfUserId(attacker), "triggered", "headshot", true, " (hstype \"headshot\")");
			}
			else if (headexplodie)
			{
				// head "explodie"
				LogPlayerEvent(GetClientOfUserId(attacker), "triggered", "headshot", true, " (hstype \"headexplodie\")");
			}
		}
	}
	
	return Plugin_Continue;
}

public OnClientPutInGame(client)
{
	g_bLogClassNextSpawn[client] = false;
}

public Event_RoundEnd(Handle:event, const String:name[], bool:dontBroadcast)
{
	LogTeamEvent(GetEventInt(event, "winner"), "triggered", "Round_Win");
}

public OnCvarLocationsChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	new bool:old_value = g_loglocations;
	g_loglocations = GetConVarBool(g_cvar_locations);
	
	if (old_value != g_loglocations)
	{
		if (g_loglocations & !g_logheadshots)
		{
			HookEvent("player_death", Event_PlayerDeathPre, EventHookMode_Pre);
		}
		else if (!g_logheadshots)
		{
			UnhookEvent("player_death", Event_PlayerDeathPre, EventHookMode_Pre);
		}
	}
}

public OnCvarActionsChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	new bool:old_value = g_logactions;
	g_logactions = GetConVarBool(g_cvar_actions);
	
	if (old_value != g_logactions)
	{
		if (g_logactions)
		{
			HookEvent("round_end", Event_RoundEnd);
		}
		else
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
		if (g_logheadshots & !g_loglocations)
		{
			HookEvent("player_death", Event_PlayerDeathPre, EventHookMode_Pre);
		}
		else if (!g_loglocations)
		{
			UnhookEvent("player_death", Event_PlayerDeathPre, EventHookMode_Pre);
		}
	}
}

public OnCvarClasschangesChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	new bool:old_value = g_logclasschanges;
	g_logclasschanges = GetConVarBool(g_cvar_classchanges);
	
	if (old_value != g_logclasschanges)
	{
		if (g_logclasschanges)
		{
			hook_classchanges();
		}
		else
		{
			unhook_classchanges();
		}
	}
}

public Action:LogMap(Handle:timer)
{
	// Called 1 second after OnPluginStart since srcds does not log the first map loaded. Idea from Stormtrooper's "mapfix.sp" for psychostats
	LogMapLoad();
}