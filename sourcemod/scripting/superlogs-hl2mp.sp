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
#include <sdkhooks>

#define NAME "SuperLogs: HL2MP"
#define VERSION "1.1.3"

#define MAX_LOG_WEAPONS 6
#define MAX_WEAPON_LEN 14
#define PREFIX_LEN 7
#define CROSSBOW 0


new g_weapon_stats[MAXPLAYERS+1][MAX_LOG_WEAPONS][15];
new const String:g_weapon_list[MAX_LOG_WEAPONS][MAX_WEAPON_LEN] = { 
									"crossbow_bolt",
									"pistol",
									"357",
									"smg1",
									"ar2",
									"shotgun"
								};

new Handle:g_cvar_headshots = INVALID_HANDLE;
new Handle:g_cvar_locations = INVALID_HANDLE;
new Handle:g_cvar_teamplay = INVALID_HANDLE;

new bool:g_logheadshots = true;
new bool:g_loglocations = true;

new g_iNextHitgroup[MAXPLAYERS+1];
new g_iNextBowHitgroup[MAXPLAYERS+1];

new g_bTeamPlay;

new g_iCrossBowOwnerOffs = -1;

new Handle:g_hBoltChecks = INVALID_HANDLE;

#include <loghelper>
#include <wstatshelper>


public Plugin:myinfo = {
	name = NAME,
	author = "psychonic",
	description = "Advanced logging for HL2DM & Sourceforts. Generates auxilary logging for use with log parsers such as HLstatsX and Psychostats",
	version = VERSION,
	url = "http://www.hlxcommunity.com"
};

#if SOURCEMOD_V_MAJOR >= 1 && SOURCEMOD_V_MINOR >= 3
public APLRes:AskPluginLoad2(Handle:myself, bool:late, String:error[], err_max)
#else
public bool:AskPluginLoad(Handle:myself, bool:late, String:error[], err_max)
#endif
{
	decl String:szGameDesc[64];
	GetGameDescription(szGameDesc, sizeof(szGameDesc), true);
	if (StrContains(szGameDesc, "Half-Life 2 Deathmatch", false) == -1 && StrContains(szGameDesc, "SourceForts", false) == -1)
	{
		decl String:szGameDir[64];
		GetGameFolderName(szGameDir, sizeof(szGameDir));
		if (StrContains(szGameDir, "hl2mp", false) == -1 && StrContains(szGameDir, "sourceforts", false) == -1)
		{
			strcopy(error, err_max, "This plugin is only supported on HL2MP & SourceForts");
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
	
	g_cvar_headshots = CreateConVar("superlogs_headshots", "1", "Enable logging of headshot player action (default on)", 0, true, 0.0, true, 1.0);
	g_cvar_locations = CreateConVar("superlogs_locations", "1", "Enable logging of location on player death (default on)", 0, true, 0.0, true, 1.0);
	HookConVarChange(g_cvar_headshots, OnCvarHeadshotsChange);
	HookConVarChange(g_cvar_locations, OnCvarLocationsChange);
	CreateConVar("superlogs_hl2mp_version", VERSION, NAME, FCVAR_SPONLY|FCVAR_REPLICATED|FCVAR_NOTIFY);
	
	g_iCrossBowOwnerOffs = FindSendPropInfo("CCrossbowBolt", "m_hOwnerEntity");
		
	hook_wstats();
	HookEvent("player_death", Event_PlayerDeathPre, EventHookMode_Pre);
		
	CreateTimer(1.0, LogMap);
	
	g_cvar_teamplay = FindConVar("mp_teamplay");
	if (g_cvar_teamplay != INVALID_HANDLE)
	{
		g_bTeamPlay = GetConVarBool(g_cvar_teamplay);
		HookConVarChange(g_cvar_teamplay, OnTeamPlayChange);
	}
	
	g_hBoltChecks = CreateStack();
}

public OnAllPluginsLoaded()
{
	if (GetExtensionFileStatus("sdkhooks.ext") != 1)
	{
		SetFailState("SDK Hooks v1.3 or higher is required for SuperLogs: HL2MP");
	}
	for (new i = 1; i <= MaxClients; i++)
	{
		if (IsClientInGame(i))
		{
			SDKHook(i, SDKHook_FireBulletsPost, OnFireBullets);
			SDKHook(i, SDKHook_TraceAttackPost, OnTraceAttack);
			SDKHook(i, SDKHook_OnTakeDamagePost, OnTakeDamage);
		}
	}
}

public OnMapStart()
{
	GetTeams();
}

hook_wstats()
{
	HookEvent("player_death", Event_PlayerDeath);
	HookEvent("player_spawn", Event_PlayerSpawn);
	HookEvent("round_end", Event_RoundEnd, EventHookMode_PostNoCopy);
	HookEvent("player_disconnect", Event_PlayerDisconnect, EventHookMode_Pre);
}

public OnClientPutInServer(client)
{
	SDKHook(client, SDKHook_FireBulletsPost, OnFireBullets);
	SDKHook(client, SDKHook_TraceAttackPost, OnTraceAttack);
	SDKHook(client, SDKHook_OnTakeDamagePost, OnTakeDamage);
	reset_player_stats(client);
}

public OnEntityCreated(entity, const String:classname[])
{
	if (strcmp(classname, "crossbow_bolt") == 0)
	{
		PushStackCell(g_hBoltChecks, entity);
	}
}

public OnGameFrame()
{
	new bowent;
	while (PopStackCell(g_hBoltChecks, bowent))
	{
		if (!IsValidEntity(bowent))
			continue;
			
		new owner = GetEntDataEnt2(bowent, g_iCrossBowOwnerOffs);
		if (owner < 0 || owner > MaxClients)
			continue;
			
		g_weapon_stats[owner][CROSSBOW][LOG_HIT_SHOTS]++;
	}
}

public OnFireBullets(attacker, shots, String:weaponname[])
{
	if (attacker > 0 && attacker <= MaxClients)
	{
		new weapon_index = get_weapon_index(weaponname[PREFIX_LEN]);
		if (weapon_index > -1)
		{
			g_weapon_stats[attacker][weapon_index][LOG_HIT_SHOTS]++;
		}
	}
}

public OnTraceAttack(victim, attacker, inflictor, Float:damage, damagetype, ammotype, hitbox, hitgroup)
{
	if (hitgroup > 0 && attacker > 0 && attacker <= MaxClients && victim > 0 && victim <= MaxClients)
	{
		if (IsValidEntity(inflictor))
		{
			decl String:inflictorclsname[64];
			if (GetEntityNetClass(inflictor, inflictorclsname, sizeof(inflictorclsname)) && strcmp(inflictorclsname, "CCrossbowBolt") == 0)
			{
				g_iNextBowHitgroup[victim] = hitgroup;
				return;
			}
		}
		g_iNextHitgroup[victim] = hitgroup;
	}
}

public OnTakeDamage(victim, attacker, inflictor, Float:damage, damagetype)
{	
	if (attacker > 0 && attacker <= MaxClients && victim > 0 && victim <= MaxClients)
	{
		decl String: weapon[MAX_WEAPON_LEN + PREFIX_LEN];
		GetClientWeapon(attacker, weapon, sizeof(weapon));
		new weapon_index = -1;
		if (IsValidEntity(inflictor))
		{
			decl String:inflictorclsname[64];
			if (GetEntityNetClass(inflictor, inflictorclsname, sizeof(inflictorclsname)) && strcmp(inflictorclsname, "CCrossbowBolt") == 0)
			{
				weapon_index = CROSSBOW;
			}
		}
		if (weapon_index == -1)
		{
			weapon_index = get_weapon_index(weapon[PREFIX_LEN]);
		}
		new hitgroup = ((weapon_index == CROSSBOW)?g_iNextBowHitgroup[victim]:g_iNextHitgroup[victim]);
		if (hitgroup < 8)
		{
			hitgroup += LOG_HIT_OFFSET;
		}
		new bool:headshot = (GetClientHealth(victim) <= 0 && hitgroup == HITGROUP_HEAD);
		if (weapon_index > -1)
		{
			g_weapon_stats[attacker][weapon_index][LOG_HIT_HITS]++;
			g_weapon_stats[attacker][weapon_index][LOG_HIT_DAMAGE] += RoundToNearest(damage);
			g_weapon_stats[attacker][weapon_index][hitgroup]++;
			if (headshot)
			{
				g_weapon_stats[attacker][weapon_index][LOG_HIT_HEADSHOTS]++;
			}
		}
		if (weapon_index == CROSSBOW)
		{
			g_iNextBowHitgroup[victim] = 0;
		}
		else
		{
			g_iNextHitgroup[victim] = 0;
		}
	}
}


public Action:Event_PlayerDeathPre(Handle:event, const String:name[], bool:dontBroadcast)
{
	// "userid"        "short"         // user ID who died                             
	// "attacker"      "short"         // user ID who killed
	// "weapon"        "string"        // weapon name killer used 
	
	new attacker = GetClientOfUserId(GetEventInt(event, "attacker"));
	new victim = GetClientOfUserId(GetEventInt(event, "userid"));
	
	if (g_loglocations)
	{
		LogKillLoc(attacker, victim);
	}
	if (g_logheadshots)
	{
		decl String:weapon[64];
		GetEventString(event, "weapon", weapon, sizeof(weapon));
		if (strcmp(weapon, "crossbow_bolt") == 0)
		{
			if (g_iNextBowHitgroup[victim] == HITGROUP_HEAD)
			{
				LogPlayerEvent(attacker, "triggered", "headshot");
			}
		}
		else if (g_iNextHitgroup[victim] == HITGROUP_HEAD)
		{
			LogPlayerEvent(attacker, "triggered", "headshot");
		}		
	}
	
	return Plugin_Continue;
}

public Event_PlayerDeath(Handle:event, const String:name[], bool:dontBroadcast)
{
	// this extents the original player_death by a new fields
	// "userid"        "short"         // user ID who died                             
	// "attacker"      "short"         // user ID who killed
	// "weapon"        "string"        // weapon name killer used 
	
	new victim   = GetClientOfUserId(GetEventInt(event, "userid"));
	
	if (victim > 0)
	{
		new attacker = GetClientOfUserId(GetEventInt(event, "attacker"));
		if (attacker != victim && attacker > 0)
		{
			decl String: weapon[MAX_WEAPON_LEN];
			GetEventString(event, "weapon", weapon, sizeof(weapon));
			new weapon_index = get_weapon_index(weapon);
			if (weapon_index > -1)
			{
				g_weapon_stats[attacker][weapon_index][LOG_HIT_KILLS]++;		
				g_weapon_stats[victim][weapon_index][LOG_HIT_DEATHS]++;
				if (g_bTeamPlay && GetClientTeam(attacker) == GetClientTeam(victim))
				{
					g_weapon_stats[attacker][weapon_index][LOG_HIT_TEAMKILLS]++;
				}	
			}
		}
		dump_player_stats(victim);
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

public OnCvarHeadshotsChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	new bool:old_value = g_logheadshots;
	g_logheadshots = GetConVarBool(g_cvar_headshots);
	
	if (old_value != g_logheadshots)
	{
		if (g_logheadshots && !g_loglocations)
		{
			HookEvent("player_death", Event_PlayerDeathPre, EventHookMode_Pre);
		}
		else if (!g_loglocations)
		{
			UnhookEvent("player_death", Event_PlayerDeathPre, EventHookMode_Pre);
		}
	}
}

public OnCvarLocationsChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	new bool:old_value = g_loglocations;
	g_loglocations = GetConVarBool(g_cvar_locations);
	
	if (old_value != g_loglocations)
	{
		if (g_loglocations && !g_logheadshots)
		{
			HookEvent("player_death", Event_PlayerDeathPre, EventHookMode_Pre);
		}
		else if (!g_logheadshots)
		{
			UnhookEvent("player_death", Event_PlayerDeathPre, EventHookMode_Pre);
		}
	}
}

public OnTeamPlayChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	g_bTeamPlay = GetConVarBool(g_cvar_teamplay);
}