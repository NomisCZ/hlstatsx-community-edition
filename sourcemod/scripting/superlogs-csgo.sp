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
#pragma newdecls required
 
#include <sourcemod>
#include <sdktools>
#include <csgo_items>

#define NAME "SuperLogs: CS:GO"
#define VERSION "1.3.0"

#define MAX_LOG_WEAPONS 41
#define IGNORE_SHOTS_START 35
#define MAX_WEAPON_LEN 32


int g_weapon_stats[MAXPLAYERS+1][MAX_LOG_WEAPONS][15];
char g_weapon_list[MAX_LOG_WEAPONS][MAX_WEAPON_LEN] = {
									"ak47", 
									"aug",
									"awp", 
									"bizon",
									"deagle",
									"elite",
									"famas",
									"fiveseven",
									"g3sg1",
									"galilar",
									"glock",
									"hpk2000",
									"usp_silencer",
									"usp_silencer_off",
									"m249",
									"m4a1",
									"m4a1_silencer",
									"m4a1_silencer_off",
									"mac10",
									"mag7",
									"mp7",
									"mp9",
									"negev",
									"nova",
									"p250",
									"cz75a",
									"p90",
									"sawedoff",
									"scar20",
									"sg556",
									"ssg08",
									"taser",
									"tec9",
									"ump45",
									"xm1014",
									"incgrenade",
									"hegrenade",
									"molotov",
									"flashbang",
									"smokegrenade",
									"decoy" 
								};

Handle g_cvar_wstats = INVALID_HANDLE;
Handle g_cvar_headshots = INVALID_HANDLE;
Handle g_cvar_actions = INVALID_HANDLE;
Handle g_cvar_locations = INVALID_HANDLE;
Handle g_cvar_ktraj = INVALID_HANDLE;
Handle g_cvar_version = INVALID_HANDLE;
EngineVersion CurrentVersion;

bool g_logwstats = true;
bool g_logheadshots = true;
bool g_logactions = true;
bool g_loglocations = true;
bool g_logktraj = false;

#include <loghelper>
#include <wstatshelper>

public Plugin myinfo = {
	name = NAME,
	author = "psychonic",
	description = "Advanced logging for CS:GO. Generates auxiliary logging for use with log parsers such as HLstatsX and Psychostats",
	version = VERSION,
	url = "http://www.hlxcommunity.com"
};

public APLRes AskPluginLoad2(Handle myself, bool late, char[] error, int err_max)
{
	CurrentVersion = GetEngineVersion();
	if (CurrentVersion != Engine_CSGO)
	{
		strcopy(error, err_max, "This plugin is only supported on CS:GO");
		return APLRes_Failure;
	}
	return APLRes_Success;
}


public void OnPluginStart()
{
	CreatePopulateWeaponTrie();
	
	g_cvar_wstats = CreateConVar("superlogs_wstats", "1", "Enable logging of weapon stats (default on)", 0, true, 0.0, true, 1.0);
	g_cvar_headshots = CreateConVar("superlogs_headshots", "1", "Enable logging of headshot player action (default on)", 0, true, 0.0, true, 1.0);
	g_cvar_actions = CreateConVar("superlogs_actions", "1", "Enable logging of player actions (default on)", 0, true, 0.0, true, 1.0);
	g_cvar_locations = CreateConVar("superlogs_locations", "1", "Enable logging of location on player death (default on)", 0, true, 0.0, true, 1.0);
	g_cvar_ktraj = CreateConVar("superlogs_ktraj", "0", "Enable Psychostats \"KTRAJ\" logging (default off)", 0, true, 0.0, true, 1.0);
	
	// cvars will have already existed if plugin was reloaded and might be set to non-default values
	g_logwstats = GetConVarBool(g_cvar_wstats);
	g_logheadshots = GetConVarBool(g_cvar_headshots);
	g_logactions = GetConVarBool(g_cvar_actions);
	g_loglocations = GetConVarBool(g_cvar_locations);
	g_logktraj = GetConVarBool(g_cvar_ktraj);
	
	HookConVarChange(g_cvar_wstats, OnCvarWstatsChange);
	HookConVarChange(g_cvar_headshots, OnCvarHeadshotsChange);
	HookConVarChange(g_cvar_actions, OnCvarActionsChange);
	HookConVarChange(g_cvar_locations, OnCvarLocationsChange);
	HookConVarChange(g_cvar_ktraj, OnCvarKtrajChange);
	
	g_cvar_version = CreateConVar("superlogs_csgo_version", VERSION, NAME, FCVAR_SPONLY|FCVAR_REPLICATED|FCVAR_NOTIFY);
		
	hook_wstats();
	hook_actions();
	HookEvent("player_death", Event_PlayerDeathPre, EventHookMode_Pre);
	HookEvent("player_death", Event_PlayerDeath);
		
	CreateTimer(1.0, LogMap);
	
	GetTeams();
}


public void OnMapStart()
{
	GetTeams();
}

public void OnConfigsExecuted()
{
	char version[255];
	GetConVarString(g_cvar_version, version, sizeof(version));
	SetConVarString(g_cvar_version, version);
}

void hook_wstats()
{
	HookEvent("weapon_fire", Event_PlayerShoot);
	HookEvent("player_hurt", Event_PlayerHurt);
	HookEvent("player_spawn", Event_PlayerSpawn);
	HookEvent("round_end", Event_RoundEnd, EventHookMode_PostNoCopy);
	HookEvent("player_disconnect", Event_PlayerDisconnect, EventHookMode_Pre);
}

void unhook_wstats()
{
	UnhookEvent("weapon_fire", Event_PlayerShoot);
	UnhookEvent("player_hurt", Event_PlayerHurt);
	UnhookEvent("player_spawn", Event_PlayerSpawn);
	UnhookEvent("round_end", Event_RoundEnd, EventHookMode_PostNoCopy);
	UnhookEvent("player_disconnect", Event_PlayerDisconnect, EventHookMode_Pre);
}

void hook_actions()
{
	HookEvent("round_mvp", Event_RoundMVP);
}

void unhook_actions()
{
	UnhookEvent("round_mvp", Event_RoundMVP);
}

public void OnClientPutInServer(int client)
{
	reset_player_stats(client);
}

stock char get_ItemDef(int client)
{
	char real_weapon[MAX_WEAPON_LEN];
	if (client < 1)
	{
		real_weapon = "world";
		return real_weapon;
	}
	int active = GetEntPropEnt(client, Prop_Send, "m_hActiveWeapon");
	if (active == -1) 
	{
		real_weapon = "world";
		return real_weapon;
	}
	int itemdef = GetEntProp(active, Prop_Send, "m_iItemDefinitionIndex");
	switch(itemdef)
	{
		case 60: // weapon_m4a1_silencer
		{
			if (GetEntProp(active, Prop_Send, "m_bSilencerOn") == 0)
			{
				real_weapon = "m4a1_silencer_off";
			} 
			else if (GetEntProp(active, Prop_Send, "m_bSilencerOn") == 1)
			{
				real_weapon = "m4a1_silencer";
			}
		}
		case 61: // weapon_usp_silencer
		{
			if (GetEntProp(active, Prop_Send, "m_bSilencerOn") == 0)
			{
				real_weapon = "usp_silencer_off"; 
			} 
			else if (GetEntProp(active, Prop_Send, "m_bSilencerOn") == 1)
			{
				real_weapon = "usp_silencer";
			}
		}
		case 42, 59, 500, 505, 506, 507, 508, 509, 512, 514, 515, 516:
		{
			real_weapon = "knife";
			//TODO: add all different knives to stats? needs graphics etc.
		}
		default:
		{
			CSGO_GetItemDefinitionNameByIndex(itemdef, real_weapon, sizeof(real_weapon));
			ReplaceString(real_weapon, sizeof(real_weapon), "weapon_", "", false);
		}
	}	
	return real_weapon;
}
public void Event_PlayerShoot(Handle event, const char[] name, bool dontBroadcast)
{
	// "userid"        "short"
	// "weapon"        "string"        // weapon name used

	int attacker   = GetClientOfUserId(GetEventInt(event, "userid"));
	if (attacker > 0)
	{
		//char weapon[MAX_WEAPON_LEN];
		//GetEventString(event, "weapon", weapon, sizeof(weapon));
		//ReplaceString(weapon, sizeof(weapon), "weapon_", "", false);
		
		char real_weapon[MAX_WEAPON_LEN];
		real_weapon = get_ItemDef(attacker);
		
		int weapon_index = get_weapon_index(real_weapon);
				
		//PrintToServer("SHOOT old_weapon: %s real_weapon: %s", weapon, real_weapon);
		if (weapon_index > -1 && weapon_index < IGNORE_SHOTS_START)
		{
			g_weapon_stats[attacker][weapon_index][LOG_HIT_SHOTS]++;
		}
	}
}


public void Event_PlayerHurt(Handle event, const char[] name, bool dontBroadcast)
{
	//	"userid"        "short"         // player index who was hurt
	//	"attacker"      "short"         // player index who attacked
	//	"health"        "byte"          // remaining health points
	//	"armor"         "byte"          // remaining armor points
	//	"weapon"        "string"        // weapon name attacker used, if not the world
	//	"dmg_health"    "byte"  		// damage done to health
	//	"dmg_armor"     "byte"          // damage done to armor
	//	"hitgroup"      "byte"          // hitgroup that was damaged

	int attacker  = GetClientOfUserId(GetEventInt(event, "attacker"));
	
	if (attacker > 0) {
		//char weapon[MAX_WEAPON_LEN];
		//GetEventString(event, "weapon", weapon, sizeof(weapon));
		
		char real_weapon[MAX_WEAPON_LEN];
		real_weapon = get_ItemDef(attacker);		
		
		int weapon_index = get_weapon_index(real_weapon);
		
		//PrintToServer("HURT old_weapon: %s real_weapon: %s", weapon, real_weapon);
		if (weapon_index > -1)
		{
			g_weapon_stats[attacker][weapon_index][LOG_HIT_HITS]++;
			g_weapon_stats[attacker][weapon_index][LOG_HIT_DAMAGE] += GetEventInt(event, "dmg_health");
			int hitgroup  = GetEventInt(event, "hitgroup");
			if (hitgroup < 8)
			{
				g_weapon_stats[attacker][weapon_index][hitgroup + LOG_HIT_OFFSET]++;
			}
		}
	}
}


public Action Event_PlayerDeathPre(Handle event, const char[] name, bool dontBroadcast)
{
	// "userid"        "short"         // user ID who died                             
	// "attacker"      "short"         // user ID who killed
	// "weapon"        "string"        // weapon name killer used 
	// "headshot"      "bool"          // signals a headshot
	
	int attacker = GetEventInt(event, "attacker");
	if (g_loglocations)
	{
		LogKillLoc(GetClientOfUserId(attacker), GetClientOfUserId(GetEventInt(event, "userid")));
	}

	if (g_logheadshots && GetEventBool(event, "headshot"))
	{
		LogPlayerEvent(GetClientOfUserId(attacker), "triggered", "headshot");
	}
	
	return Plugin_Continue;
}

public void Event_PlayerDeath(Handle event, const char[] name, bool dontBroadcast)
{
	// this extents the original player_death by a new fields
	// "userid"        "short"         // user ID who died                             
	// "attacker"      "short"         // user ID who killed
	// "weapon"        "string"        // weapon name killer used 
	// "headshot"      "bool"          // signals a headshot
	// "dominated"    "short"        // did killer dominate victim with this kill
	// "revenge"    "short"        // did killer get revenge on victim with this kill
	
	int victim   = GetClientOfUserId(GetEventInt(event, "userid"));
	int attacker = GetClientOfUserId(GetEventInt(event, "attacker"));
	//char weapon[MAX_WEAPON_LEN];
	//GetEventString(event, "weapon", weapon, sizeof(weapon));
	
	char real_weapon[MAX_WEAPON_LEN];
	real_weapon = get_ItemDef(attacker);
	
	//PrintToServer("DEATH old_weapon: %s real_weapon: %s", weapon, real_weapon);
	
	if (attacker <= 0 || victim <= 0)
	{
		return;
	}
	
	if (g_logwstats)
	{
		int weapon_index = get_weapon_index(real_weapon);		
		if (weapon_index > -1)
		{
			g_weapon_stats[attacker][weapon_index][LOG_HIT_KILLS]++;
			if (GetEventBool(event, "headshot"))
			{
				g_weapon_stats[attacker][weapon_index][LOG_HIT_HEADSHOTS]++;
			}
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
		LogPSKillTraj(attacker, victim, real_weapon);
	}
	if (g_logactions)
	{
		// these are only in Orangebox CS:S. These properties won't exist on ep1 css and should eval to 0/false.
		if (GetEventInt(event, "dominated"))
		{
			LogPlyrPlyrEvent(attacker, victim, "triggered", "domination");
		}
		else if (GetEventInt(event, "revenge"))
		{
			LogPlyrPlyrEvent(attacker, victim, "triggered", "revenge");
		}
	}
}

public void Event_PlayerSpawn(Handle event, const char[] name, bool dontBroadcast)
{
	// "userid"        "short"         // user ID on server          

	int client = GetClientOfUserId(GetEventInt(event, "userid"));
	if (client > 0)
	{
		reset_player_stats(client);
	}
}

public void Event_RoundEnd(Handle event, const char[] name, bool dontBroadcast)
{
	WstatsDumpAll();
}

public void Event_RoundMVP(Handle event, const char[] name, bool dontBroadcast)
{
	LogPlayerEvent(GetClientOfUserId(GetEventInt(event, "userid")), "triggered", "round_mvp");
}

public Action Event_PlayerDisconnect(Handle event, const char[] name, bool dontBroadcast)
{
	int client = GetClientOfUserId(GetEventInt(event, "userid"));
	OnPlayerDisconnect(client);
	return Plugin_Continue;
}


public Action LogMap(Handle timer)
{
	// Called 1 second after OnPluginStart since srcds does not log the first map loaded. Idea from Stormtrooper's "mapfix.sp" for psychostats
	LogMapLoad();
}


public void OnCvarWstatsChange(Handle cvar, const char[] oldVal, const char[] newVal)
{
	bool old_value = g_logwstats;
	g_logwstats = GetConVarBool(g_cvar_wstats);
	
	if (old_value != g_logwstats)
	{
		if (g_logwstats)
		{
			hook_wstats();
			if (!g_logktraj && !g_logactions)
			{
				HookEvent("player_death", Event_PlayerDeath);
			}
		}
		else
		{
			unhook_wstats();
			if (!g_logktraj && !g_logactions)
			{
				UnhookEvent("player_death", Event_PlayerDeath);
			}
		}
	}
}

public void OnCvarActionsChange(Handle cvar, const char[] oldVal, const char[] newVal)
{
	bool old_value = g_logactions;
	g_logactions = GetConVarBool(g_cvar_actions);
	
	if (old_value != g_logactions)
	{
		if (g_logactions)
		{
			hook_actions();
			if (!g_logktraj && !g_logwstats)
			{
				HookEvent("player_death", Event_PlayerDeath);
			}
		}
		else
		{
			unhook_actions();
			if (!g_logktraj && !g_logwstats)
			{
				UnhookEvent("player_death", Event_PlayerDeath);
			}
		}
	}
}

public void OnCvarHeadshotsChange(Handle cvar, const char[] oldVal, const char[] newVal)
{
	bool old_value = g_logheadshots;
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

public void OnCvarLocationsChange(Handle cvar, const char[] oldVal, const char[] newVal)
{
	bool old_value = g_loglocations;
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

public void OnCvarKtrajChange(Handle cvar, const char[] oldVal, const char[] newVal)
{
	bool old_value = g_logktraj;
	g_logktraj = GetConVarBool(g_cvar_ktraj);
	
	if (old_value != g_logktraj)
	{
		if (g_logktraj && !g_logwstats && !g_logactions)
		{
			HookEvent("player_death", Event_PlayerDeath);
		}
		else if (!g_logwstats && !g_logactions)
		{
			UnhookEvent("player_death", Event_PlayerDeath);
		}
	}
}
