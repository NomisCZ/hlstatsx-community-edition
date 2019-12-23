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
#include <cstrike>

#define PLUGIN_NAME "SuperLogs: CS:GO"
#define PLUGIN_AUTHOR "psychonic, NomisCZ (-N-)"
#define PLUGIN_VERSION "1.7.1"

#define CSGO_ITEMS_GAME "scripts/items/items_game.txt"
#define MAX_LOG_WEAPONS 63
#define IGNORE_SHOTS_START 35
#define MAX_WEAPON_LEN 32
#define CLANTAG_LEN 192

EngineVersion g_evCurrentVersion;

ConVar g_cvar_wstats;
ConVar g_cvar_headshots;
ConVar g_cvar_actions;
ConVar g_cvar_locations;
ConVar g_cvar_ktraj;
ConVar g_cvar_version;

StringMap g_gameItems = null;

int g_weapon_stats[MAXPLAYERS+1][MAX_LOG_WEAPONS][15];

float g_fPlayerLastClanTagChange[MAXPLAYERS+1];

bool g_logwstats = true;
bool g_logheadshots = true;
bool g_logactions = true;
bool g_loglocations = true;
bool g_logktraj = false;
bool g_bLate;

char g_weapon_list[MAX_LOG_WEAPONS][MAX_WEAPON_LEN] = {
	"ak47", "aug", "awp", "bizon", "deagle", "elite", "famas", "fiveseven", "g3sg1", "galilar",
	"glock", "hkp2000", "usp_silencer", "usp_silencer_off", "m249", "m4a1", "m4a1_silencer", "m4a1_silencer_off", "mac10", "mag7",
	"mp7", "mp9", "negev", "nova", "p250", "cz75a", "p90", "sawedoff", "scar20", "sg556",
	"ssg08", "taser", "tec9", "ump45", "xm1014", "revolver", "inferno", "incgrenade", "hegrenade", "molotov",
	"flashbang", "smokegrenade", "decoy", "knife", "bayonet", "knife_css", "knife_flip", "knife_gut", "knife_karambit", "knife_m9_bayonet", 
	"knife_tactical", "knife_falchion", "knife_survival_bowie", "knife_butterfly", "knife_push", "knife_cord", "knife_canis", "knife_ursus", "knife_gypsy_jackknife", "knife_outdoor",
	"knife_stiletto", "knife_widowmaker", "knife_skeleton",
};
char g_sPlayerClanTag[MAXPLAYERS+1][CLANTAG_LEN];

#include <loghelper>
#include <wstatshelper>

public Plugin myinfo = {
	name = PLUGIN_NAME,
	author = PLUGIN_AUTHOR,
	description = "Advanced logging for CS:GO. Generates auxiliary logging for use with log parsers such as HLstatsX and Psychostats",
	version = PLUGIN_VERSION,
	url = "https://github.com/NomisCZ/hlstatsx-community-edition"
};

public APLRes AskPluginLoad2(Handle myself, bool late, char[] error, int err_max)
{
	LoadGameItems();
	g_bLate = late;
	return APLRes_Success;
}

public void OnPluginStart()
{
	g_evCurrentVersion = GetEngineVersion();

	if (g_evCurrentVersion != Engine_CSGO) {
		SetFailState("This plugin is only for CS:GO.");
	}

	CreatePopulateWeaponTrie();
	
	g_cvar_version = CreateConVar("superlogs_csgo_version", PLUGIN_VERSION, PLUGIN_NAME, FCVAR_REPLICATED | FCVAR_SPONLY | FCVAR_DONTRECORD | FCVAR_NOTIFY);

	g_cvar_wstats = CreateConVar("superlogs_wstats", "1", "Enable logging of weapon stats (default on)", 0, true, 0.0, true, 1.0);
	g_cvar_wstats.AddChangeHook(OnConVarChanged);

	g_cvar_headshots = CreateConVar("superlogs_headshots", "1", "Enable logging of headshot player action (default on)", 0, true, 0.0, true, 1.0);
	g_cvar_headshots.AddChangeHook(OnConVarChanged);

	g_cvar_actions = CreateConVar("superlogs_actions", "1", "Enable logging of player actions (default on)", 0, true, 0.0, true, 1.0);
	g_cvar_actions.AddChangeHook(OnConVarChanged);

	g_cvar_locations = CreateConVar("superlogs_locations", "1", "Enable logging of location on player death (default on)", 0, true, 0.0, true, 1.0);
	g_cvar_locations.AddChangeHook(OnConVarChanged);

	g_cvar_ktraj = CreateConVar("superlogs_ktraj", "0", "Enable Psychostats \"KTRAJ\" logging (default off)", 0, true, 0.0, true, 1.0);
	g_cvar_ktraj.AddChangeHook(OnConVarChanged);

	HookEvent("player_death", Event_PlayerDeathPre, EventHookMode_Pre);
	HookEvent("player_death", Event_PlayerDeath);
	HookEvents_Wstats();
	HookEvents_Actions();
		
	CreateTimer(1.0, LogMap);
	
	GetTeams();

	if (g_bLate) for (int i = 1; i <= MaxClients; i++) if (IsClientInGame(i)) OnClientPostAdminCheck(i);
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

void HookEvents_Wstats()
{
	HookEvent("weapon_fire", Event_PlayerShoot);
	HookEvent("player_hurt", Event_PlayerHurt);
	HookEvent("player_spawn", Event_PlayerSpawn);
	HookEvent("round_end", Event_RoundEnd, EventHookMode_PostNoCopy);
	HookEvent("player_disconnect", Event_PlayerDisconnect, EventHookMode_Pre);
}

void UnhookEvents_Wstats()
{
	UnhookEvent("weapon_fire", Event_PlayerShoot);
	UnhookEvent("player_hurt", Event_PlayerHurt);
	UnhookEvent("player_spawn", Event_PlayerSpawn);
	UnhookEvent("round_end", Event_RoundEnd, EventHookMode_PostNoCopy);
	UnhookEvent("player_disconnect", Event_PlayerDisconnect, EventHookMode_Pre);
}

void HookEvents_Actions()
{
	HookEvent("round_mvp", Event_RoundMVP);
}

void UnhookEvents_Actions()
{
	UnhookEvent("round_mvp", Event_RoundMVP);
}

public void OnClientPutInServer(int client)
{
	reset_player_stats(client);
}

public void OnClientPostAdminCheck(int client)
{
	if (IsValidClient(client, false)) {
		
		char sClan[CLANTAG_LEN];

		CS_GetClientClanTag(client, sClan, sizeof(sClan));
		LogPlayerProps(client, "triggered", "clantag", sClan);

		g_sPlayerClanTag[client] = sClan;
		g_fPlayerLastClanTagChange[client] = GetGameTime();
	}
}

public void Event_PlayerShoot(Event event, const char[] name, bool dontBroadcast)
{
	// "userid"        "short"
	// "weapon"        "string"        // weapon name used

	int iClient = GetClientOfUserId(event.GetInt("userid"));

	if (IsValidClient(iClient)) {

		char sPlayerWeapon[MAX_WEAPON_LEN];
		GetClientItemDefName(iClient, sPlayerWeapon, sizeof(sPlayerWeapon));

		int iPlayerWeaponIndex = get_weapon_index(sPlayerWeapon);

		if (iPlayerWeaponIndex > -1 && iPlayerWeaponIndex < IGNORE_SHOTS_START) {
			g_weapon_stats[iClient][iPlayerWeaponIndex][LOG_HIT_SHOTS]++;
		}
	}
}


public void Event_PlayerHurt(Event event, const char[] name, bool dontBroadcast)
{
	//	"userid"        "short"         // player index who was hurt
	//	"attacker"      "short"         // player index who attacked
	//	"health"        "byte"          // remaining health points
	//	"armor"         "byte"          // remaining armor points
	//	"weapon"        "string"        // weapon name attacker used, if not the world
	//	"dmg_health"    "byte"  		// damage done to health
	//	"dmg_armor"     "byte"          // damage done to armor
	//	"hitgroup"      "byte"          // hitgroup that was damaged

	int iAttacker = GetClientOfUserId(event.GetInt("attacker"));
	int iVictim = GetClientOfUserId(event.GetInt("userid"));

	if (IsValidClient(iAttacker) && iAttacker != iVictim) {

		char sPlayerWeapon[MAX_WEAPON_LEN];
		GetClientItemDefName(iAttacker, sPlayerWeapon, sizeof(sPlayerWeapon));

		int iPlayerWeaponIndex = get_weapon_index(sPlayerWeapon);
		
		if (iPlayerWeaponIndex > -1) {

			g_weapon_stats[iAttacker][iPlayerWeaponIndex][LOG_HIT_HITS]++;
			g_weapon_stats[iAttacker][iPlayerWeaponIndex][LOG_HIT_DAMAGE] += event.GetInt("dmg_health");
			
			int hitgroup = event.GetInt("hitgroup");

			if (hitgroup < 8) {
				g_weapon_stats[iAttacker][iPlayerWeaponIndex][hitgroup + LOG_HIT_OFFSET]++;
			}
		}
	}
}

public Action Event_PlayerDeathPre(Event event, const char[] name, bool dontBroadcast)
{
	// "userid"        "short"         // user ID who died                             
	// "attacker"      "short"         // user ID who killed
	// "weapon"        "string"        // weapon name killer used 
	// "headshot"      "bool"          // signals a headshot
	
	int iAttacker = GetClientOfUserId(event.GetInt("attacker"));
	int iVictim = GetClientOfUserId(event.GetInt("userid"));

	if (IsValidClient(iAttacker) && IsValidClient(iVictim)) {

		if (g_loglocations) {
			LogKillLoc(iAttacker, iVictim);
		}

		if (g_logheadshots && GetEventBool(event, "headshot")) {
			LogPlayerEvent(iAttacker, "triggered", "headshot");
		}
	}
}

public void Event_PlayerDeath(Event event, const char[] name, bool dontBroadcast)
{
	// this extents the original player_death by a new fields
	// "userid"        "short"         // user ID who died                             
	// "attacker"      "short"         // user ID who killed
	// "weapon"        "string"        // weapon name killer used 
	// "headshot"      "bool"          // signals a headshot
	// "dominated"    "short"        // did killer dominate victim with this kill
	// "revenge"    "short"        // did killer get revenge on victim with this kill
	
	int iAttacker = GetClientOfUserId(event.GetInt("attacker"));
	int iVictim = GetClientOfUserId(event.GetInt("userid"));

	if (!IsValidClient(iAttacker) || !IsValidClient(iVictim)) {
		return;
	}

	char sPlayerWeapon[MAX_WEAPON_LEN];
	GetClientItemDefName(iAttacker, sPlayerWeapon, sizeof(sPlayerWeapon));

	if (g_logwstats) {

		int iPlayerWeaponIndex = get_weapon_index(sPlayerWeapon);

		if (iPlayerWeaponIndex > -1) {

			g_weapon_stats[iAttacker][iPlayerWeaponIndex][LOG_HIT_KILLS]++;

			if (GetEventBool(event, "headshot")) {
				g_weapon_stats[iAttacker][iPlayerWeaponIndex][LOG_HIT_HEADSHOTS]++;
			}

			g_weapon_stats[iVictim][iPlayerWeaponIndex][LOG_HIT_DEATHS]++;

			if (GetClientTeam(iAttacker) == GetClientTeam(iVictim)) {
				g_weapon_stats[iAttacker][iPlayerWeaponIndex][LOG_HIT_TEAMKILLS]++;
			}

			dump_player_stats(iVictim);
		}
	}

	if (g_logktraj) {
		LogPSKillTraj(iAttacker, iVictim, sPlayerWeapon);
	}

	if (g_logactions) {
		// these are only in Orangebox CS:S. These properties won't exist on ep1 css and should eval to 0/false.
		if (event.GetInt("dominated")) {
			LogPlyrPlyrEvent(iAttacker, iVictim, "triggered", "domination");
		} else if (event.GetInt("revenge")){
			LogPlyrPlyrEvent(iAttacker, iVictim, "triggered", "revenge");
		}
	}
}

public void Event_PlayerSpawn(Event event, const char[] name, bool dontBroadcast)
{
	// "userid"        "short"         // user ID on server          

	int iClient = GetClientOfUserId(event.GetInt("userid"));

	if (IsValidClient(iClient)) {
		reset_player_stats(iClient);
	}
}

public void Event_RoundEnd(Event event, const char[] name, bool dontBroadcast)
{
	WstatsDumpAll();
}

public void Event_RoundMVP(Event event, const char[] name, bool dontBroadcast)
{
	LogPlayerEvent(GetClientOfUserId(event.GetInt("userid")), "triggered", "round_mvp");
}

public Action Event_PlayerDisconnect(Event event, const char[] name, bool dontBroadcast)
{
	OnPlayerDisconnect(GetClientOfUserId(event.GetInt("userid")));
}

public Action LogMap(Handle timer)
{
	// Called 1 second after OnPluginStart since srcds does not log the first map loaded. Idea from Stormtrooper's "mapfix.sp" for psychostats
	LogMapLoad();
}

public void OnConVarChanged(ConVar convar, const char[] oldValue, const char[] newValue)
{
	if (StrEqual(oldValue, newValue)) {
		return; 
	}

	if (convar == g_cvar_wstats) {

		g_logwstats = g_cvar_wstats.BoolValue;

		if (g_logwstats) {

			HookEvents_Wstats();

			if (!g_logktraj && !g_logactions) {
				HookEvent("player_death", Event_PlayerDeath);
			}

		} else {

			UnhookEvents_Wstats();

			if (!g_logktraj && !g_logactions) {
				UnhookEvent("player_death", Event_PlayerDeath);
			}
		}

	} else if (convar == g_cvar_headshots) {

		g_logheadshots = g_cvar_headshots.BoolValue;

		if (g_logheadshots && !g_loglocations) {
			HookEvent("player_death", Event_PlayerDeathPre, EventHookMode_Pre);
		} else if (!g_loglocations) {
			UnhookEvent("player_death", Event_PlayerDeathPre, EventHookMode_Pre);
		}

	} else if (convar == g_cvar_actions) {

		g_logactions = g_cvar_actions.BoolValue;
		
		if (g_logactions) {
			
			HookEvents_Actions();
			
			if (!g_logktraj && !g_logwstats) {
				HookEvent("player_death", Event_PlayerDeath);
			}

		} else {

			UnhookEvents_Actions();
			
			if (!g_logktraj && !g_logwstats) {
				UnhookEvent("player_death", Event_PlayerDeath);
			}
		}

	} else if (convar == g_cvar_locations) {

		g_loglocations = g_cvar_locations.BoolValue;

		if (g_loglocations && !g_logheadshots) {
			HookEvent("player_death", Event_PlayerDeathPre, EventHookMode_Pre);
		} else if (!g_logheadshots) {
			UnhookEvent("player_death", Event_PlayerDeathPre, EventHookMode_Pre);
		}		

	} else if (convar == g_cvar_ktraj) {

		g_logktraj = g_cvar_ktraj.BoolValue;

		if (g_logktraj && !g_logwstats && !g_logactions) {
			HookEvent("player_death", Event_PlayerDeath);
		} else if (!g_logwstats && !g_logactions) {
			UnhookEvent("player_death", Event_PlayerDeath);
		}
	}
}

public Action OnClientCommandKeyValues(int client, KeyValues kv) 
{ 
	char sKey[64]; 
	
	if (!kv.GetSectionName(sKey, sizeof(sKey)))
		return Plugin_Continue;


	if (StrEqual(sKey, "ClanTagChanged")) {
		
		char sClan[CLANTAG_LEN];
		float fCurTime = GetGameTime();
		float fNewTime = fCurTime + 10.0;

		kv.GetString("tag", sClan, sizeof(sClan), "");

		// Flood protection 10s + ignore same tag
		if ((g_fPlayerLastClanTagChange[client] <= fCurTime) && strcmp(sClan, "") != 0 && !StrEqual(g_sPlayerClanTag[client], sClan, false)) {
			
			g_sPlayerClanTag[client] = sClan;
			LogPlayerProps(client, "triggered", "clantag", sClan);
		}

		g_fPlayerLastClanTagChange[client] = fNewTime;
	} 

	return Plugin_Continue;
}

bool IsValidClient(int client, bool allowBots = true)
{
	if (!(1 <= client <= MaxClients) || !IsClientInGame(client) || (IsFakeClient(client) && !allowBots)
	|| IsClientSourceTV(client) || IsClientReplay(client) ) {
		return false;
	}
	return true;
}

void LogPlayerProps(int client, char[] verb, const char[] event, const char[] properties)
{
	if (IsValidClient(client, false)) {

		char sAuthId[32];
		char sTeamName[32];

		GetClientAuthId(client, AuthId_Steam2, sAuthId, sizeof(sAuthId));
		GetTeamName(GetClientTeam(client), sTeamName, sizeof(sTeamName));

		LogToGame("\"%N<%d><%s><%s>\" %s \"%s\" (value \"%s\")", client, GetClientUserId(client), sAuthId, sTeamName, verb, event, properties); 
	}
}

void LoadGameItems()
{
	if (!FileExists(CSGO_ITEMS_GAME, true)) {
		SetFailState("Couldn't find %s.", CSGO_ITEMS_GAME);
	}

	KeyValues kv = new KeyValues("items_game");
	
	if (!kv.ImportFromFile(CSGO_ITEMS_GAME)) {
		SetFailState("Couldn't import %s file.", CSGO_ITEMS_GAME);
	}

	if (!kv.JumpToKey("items", false) || !kv.GotoFirstSubKey(true)) {
		SetFailState("Couldn't load items in %s file.", CSGO_ITEMS_GAME);
	}

	g_gameItems = new StringMap();

	char sItemId[16];
	char sItemName[MAX_WEAPON_LEN];

	do {
		kv.GetSectionName(sItemId, sizeof(sItemId));
		kv.GetString("name", sItemName, sizeof(sItemName), "default");

		g_gameItems.SetString(sItemId, sItemName);

		// Ignore non-weapon items 
		if (StringToInt(sItemId) >= 600) {
			break;
		}

	} while (kv.GotoNextKey(true));

	kv.Rewind();
	delete kv;
}

public bool GetClientItemDefName(int client, char[] weaponName, int weaponNameSize)
{
	int activeWeapon = GetEntPropEnt(client, Prop_Send, "m_hActiveWeapon");
	int itemDefIndex = GetEntProp(activeWeapon, Prop_Send, "m_iItemDefinitionIndex");
	bool itemHasSilencer = (!!GetEntProp(activeWeapon, Prop_Send, "m_bSilencerOn"));

	switch (itemDefIndex) {

		case 60: {
			strcopy(weaponName, weaponNameSize, itemHasSilencer ? "m4a1_silencer" : "m4a1_silencer_off");
		} case 61: {
			strcopy(weaponName, weaponNameSize, itemHasSilencer ? "usp_silencer" : "usp_silencer_off");
		} default: {
			GetItemDefNameByIndex(itemDefIndex, weaponName, weaponNameSize);
		}
	}
}

public void GetItemDefNameByIndex(int itemDefinitionIndex, char[] weaponName, int weaponNameSize)
{
	if (!IsValidStringMap(g_gameItems)) {
		ThrowError("Items not loaded/valid.");
	}

	char sItemDefinitionIndex[4];
	IntToString(itemDefinitionIndex, sItemDefinitionIndex, sizeof(sItemDefinitionIndex));

	if (g_gameItems.GetString(sItemDefinitionIndex, weaponName, weaponNameSize)) {
		ReplaceString(weaponName, weaponNameSize, "weapon_", "", false);	
	}
}

public bool IsValidStringMap(StringMap &stringMap)
{
	return (stringMap != null && stringMap != INVALID_HANDLE);
}