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

#define NAME "SuperLogs: Generic"
#define VERSION "1.0"

new Handle:g_cvar_enable = INVALID_HANDLE;

new bool:g_log = true;

#include <loghelper>


public Plugin:myinfo = {
	name = NAME,
	author = "psychonic",
	description = "Advanced logging. Generates auxilary logging for use with log parsers such as HLstatsX and Psychostats",
	version = VERSION,
	url = "http://www.hlxcommunity.com"
};


public OnPluginStart()
{
	g_cvar_enable = CreateConVar("superlogs_locations", "1", "Enable logging of kill coordinates (default on)", 0, true, 0.0, true, 1.0);

	HookConVarChange(g_cvar_enable, OnCvarEnableChange);

	CreateConVar("superlogs_generic_version", VERSION, NAME, FCVAR_SPONLY|FCVAR_REPLICATED|FCVAR_NOTIFY);
		
	HookEvent("player_death", Event_PlayerDeath, EventHookMode_Pre);
	
	CreateTimer(1.0, LogMap);
}


public Action:Event_PlayerDeath(Handle:event, const String:name[], bool:dontBroadcast)
{
	LogKillLoc(GetClientOfUserId(GetEventInt(event, "attacker")), GetClientOfUserId(GetEventInt(event, "userid")));	
	
	return Plugin_Continue;
}

public OnCvarEnableChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	new bool:old_value = g_log;
	g_log = GetConVarBool(g_cvar_enable);
	
	if (old_value != g_log)
	{
		if (g_log)
		{
			HookEvent("player_death", Event_PlayerDeath, EventHookMode_Pre);
		}
		else
		{
			UnhookEvent("player_death", Event_PlayerDeath, EventHookMode_Pre);
		}
	}
}

public Action:LogMap(Handle:timer)
{
	// Called 1 second after OnPluginStart since srcds does not log the first map loaded. Idea from Stormtrooper's "mapfix.sp" for psychostats
	LogMapLoad();
}
