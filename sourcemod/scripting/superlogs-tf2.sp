/*
 * HLstatsX Community Edition - SourceMod plugin to generate advanced weapon logging
 * http://www.hlxcommunity.com
 * Copyright (C) 2009-2010 Nicholas Hastings (psychonic)
 * Copyright (C) 2010 Thomas "CmptrWz" Berezansky
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
#include <tf2_stocks> // NOTE: Gives us tf2 AND sdktools
#include <loghelper> // http://forums.alliedmods.net/showthread.php?t=100084
#undef REQUIRE_EXTENSIONS
#include <sdkhooks> // http://forums.alliedmods.net/showthread.php?t=106748
#define REQUIRE_EXTENSIONS

#define VERSION "2.0.32"
#define NAME "SuperLogs: TF2"

#define UNLOCKABLE_BIT (1<<30)
#define MAX_LOG_WEAPONS 28
#define MAX_WEAPON_LEN 29
#define MAX_BULLET_WEAPONS 14
#define MAX_UNLOCKABLE_WEAPONS 6
#define MAX_LOADOUT_SLOTS 8
#define WEAPON_PREFIX_LENGTH 10
#define WEAPON_FULL_LENGTH (WEAPON_PREFIX_LENGTH + MAX_WEAPON_LEN)
#define TELEPORT_AGAIN_TIME 10.0
#define OBJ_DISPENSER 0
#define OBJ_TELEPORTER_ENTRANCE 1
#define OBJ_TELEPORTER_EXIT 2
#define OBJ_SENTRYGUN 3
#define OBJ_ATTACHMENT_SAPPER 4
#define OBJ_SENTRYGUN_MINI 20
#define ITEMINDEX_DEMOSHIELD 131
#define ITEMINDEX_GUNBOATS 133
#define JUMP_NONE 0
#define JUMP_ROCKET_START 1
#define JUMP_ROCKET 2
#define JUMP_STICKY 3
#define LOG_SHOTS 0
#define LOG_HITS 1
#define LOG_KILLS 2
#define LOG_HEADSHOTS 3
#define LOG_TEAMKILLS 4
#define LOG_DAMAGE 5
#define LOG_DEATHS 6
#define LUNCHBOX_CHOCOLATE 159
#define LUNCHBOX_STEAK 311

public Plugin:myinfo = {
	name = NAME,
	author = "Thomas \"CmptrWz\" Berezansky & psychonic",
	description = "Advanced logging for TF2. Generates auxilary logging for use with log parsers such as HLstatsX and Psychostats",
	version = VERSION,
	url = "http://www.hlxcommunity.com"
};

// Convars we need to monitor
new Handle:cvar_crits; // tf_weapon_criticals - Needs to be on for weapon stats

// Our convars
new Handle:cvar_actions;
new Handle:cvar_teleports;
new Handle:cvar_teleports_again;
new Handle:cvar_headshots;
new Handle:cvar_backstabs;
new Handle:cvar_sandvich;
new Handle:cvar_fire;
new Handle:cvar_wstats;
new Handle:cvar_heals;
new Handle:cvar_rolelogfix;
new Handle:cvar_objlogfix;

// Booleans to keep track of them
new bool:b_actions;
new bool:b_teleports;
new bool:b_teleports_again;
new bool:b_headshots;
new bool:b_backstabs;
new bool:b_sandvich;
new bool:b_fire;
new bool:b_wstats;
new bool:b_heals;
new bool:b_rolelogfix;
new bool:b_objlogfix;

// Monitoring outside libraries/cvars
new bool:b_sdkhookloaded = false;

// Weapon trie
new Handle:h_weapontrie;
// Weapon Stats
new weaponStats[MAXPLAYERS+1][MAX_LOG_WEAPONS][7];
new nextHurt[MAXPLAYERS+1] = {-1, ...};
// Loadout Info
new playerLoadout[MAXPLAYERS+1][MAX_LOADOUT_SLOTS][2];
new bool:playerLoadoutUpdated[MAXPLAYERS+1];
new Handle:itemsKv;
new Handle:slotsTrie;
// Stunball id (so we aren't looking it up in gameframe)
new stunBallId = -1;
// Stacks for "object destroyed at spawn"
new Handle:h_objList[MAXPLAYERS+1];
// Time storage for the same
new Float:f_objRemoved[MAXPLAYERS+1];
// Stunball Stack
new Handle:h_stunBalls;
// Wearables Stack
new Handle:h_wearables;

// Teleporter Stat-Padding Fix: Keep track of last use of teleporter
new Float:f_lastTeleport[MAXPLAYERS+1][MAXPLAYERS+1];
// Heals
new healPoints[MAXPLAYERS+1];
// Rocket/Sticky Jump Status
new jumpStatus[MAXPLAYERS+1];
// Last dalokohs eaten
new Float:dalokohs[MAXPLAYERS+1];

// Last known class of players
// Likely less intensive to keep track of this for sound hooks
new TFClassType:playerClass[MAXPLAYERS+1];

// Is player carrying a building
new bool:g_bCarryingObject[MAXPLAYERS+1] = {false,...};
new g_iCarryingOffs = -1;
new bool:g_bBlockLog = false;

// Bullet Weapons Variable
new bulletWeapons = 0;

// Arrays
// ONLY RULE OF THIS WEAPON LIST:
// Unlockable variants of a weapon go AFTER the original variant
// This will need to be tweaked if they make a second alt version of a weapon we track
new const String:weaponList[MAX_LOG_WEAPONS][MAX_WEAPON_LEN] = {
	"ball",
	"flaregun",
	"minigun",
	"natascha",
	"pistol",
	"pistol_scout",
	"revolver",
	"ambassador",
	"scattergun",
	"force_a_nature",
	"shotgun_hwg",
	"shotgun_primary",
	"shotgun_pyro",
	"shotgun_soldier",
	"smg",
	"sniperrifle",
	"syringegun_medic",
	"blutsauger",
	"tf_projectile_arrow",
	"tf_projectile_pipe",
	"tf_projectile_pipe_remote",
	"sticky_resistance",
	"tf_projectile_rocket",
	"rocketlauncher_directhit",
	"deflect_rocket",
	"deflect_promode",
	"deflect_flare",
	"deflect_arrow"
};
// This list has none of the above rules
new const String:weaponBullet[MAX_BULLET_WEAPONS][MAX_WEAPON_LEN] = {
	"ambassador",
	"force_a_nature",
	"minigun",
	"natascha",
	"pistol",
	"pistol_scout",
	"revolver",
	"scattergun",
	"shotgun_hwg",
	"shotgun_primary",
	"shotgun_pyro",
	"shotgun_soldier",
	"smg",
	"sniperrifle"
};
// This list is the list of weapons with an alternate in the next slot in the first list
new const String:weaponUnlockables[MAX_UNLOCKABLE_WEAPONS][MAX_WEAPON_LEN] = {
	"minigun",
	"revolver",
	"scattergun",
	"syringegun_medic",
	"tf_projectile_pipe_remote",
	"tf_projectile_rocket"
};

public APLRes:AskPluginLoad2(Handle:myself, bool:late, String:error[], err_max)
{
	MarkNativeAsOptional("SDKHook"); // This needs to be marked optional for a number of reasons
	return APLRes_Success;
}

public OnPluginStart()
{
	CreateConVar("superlogs_tf_version", VERSION, NAME, FCVAR_PLUGIN|FCVAR_NOTIFY);

	cvar_crits = FindConVar("tf_weapon_criticals");
	cvar_actions = CreateConVar("superlogs_actions", "1", "Enable logging of most player actions, such as \"stun\" (default on)", 0, true, 0.0, true, 1.0);
	cvar_teleports = CreateConVar("superlogs_teleports", "1", "Enable logging of teleports (default on)", 0, true, 0.0, true, 1.0);
	cvar_teleports_again = CreateConVar("superlogs_teleports_again", "1", "Repeated use of same teleporter in 10 seconds adds _again to event (default on)", 0, true, 0.0, true, 1.0);
	cvar_headshots = CreateConVar("superlogs_headshots", "0", "Enable logging of headshot player action (default off)", 0, true, 0.0, true, 1.0);
	cvar_backstabs = CreateConVar("superlogs_backstabs", "1", "Enable logging of backstab player action (default on)", 0, true, 0.0, true, 1.0);
	cvar_sandvich = CreateConVar("superlogs_sandvich", "1", "Enable logging of sandvich eating (default on)", 0, true, 0.0, true, 1.0);
	cvar_fire = CreateConVar("superlogs_fire", "1", "Enable logging of fiery arrows as a separate weapon from regular arrows (default on)", 0, true, 0.0, true, 1.0);
	cvar_wstats = CreateConVar("superlogs_wstats", "1", "Enable logging of weapon stats (default on, only works when tf_weapon_criticals is 1)", 0, true, 0.0, true, 1.0);
	cvar_heals = CreateConVar("superlogs_heals", "1", "Enable logging of healpoints upon death (default on)", 0, true, 0.0, true, 1.0);
	cvar_rolelogfix = CreateConVar("superlogs_rolelogfix", "1", "Enable logging of healpoints upon death (default on)", 0, true, 0.0, true, 1.0);
	cvar_objlogfix = CreateConVar("superlogs_objlogfix", "1", "Enable logging of owner object destruction on team/class change (default on)", 0, true, 0.0, true, 1.0);

	HookConVarChange(cvar_crits,OnConVarStatsChange);
	HookConVarChange(cvar_actions,OnConVarActionsChange);
	HookConVarChange(cvar_teleports,OnConVarTeleportsChange);
	HookConVarChange(cvar_teleports_again,OnConVarTeleportsAgainChange);
	HookConVarChange(cvar_headshots,OnConVarHeadshotsChange);
	HookConVarChange(cvar_backstabs,OnConVarBackstabsChange);
	HookConVarChange(cvar_sandvich,OnConVarSandvichChange);
	HookConVarChange(cvar_fire,OnConVarFireChange);
	HookConVarChange(cvar_wstats,OnConVarStatsChange);
	HookConVarChange(cvar_heals,OnConVarHealsChange);
	HookConVarChange(cvar_rolelogfix,OnConVarRolelogfixChange);
	HookConVarChange(cvar_objlogfix,OnConVarObjlogfixChange);

	h_stunBalls = CreateStack();
	h_wearables = CreateStack();
	itemsKv = CreateKeyValues("items_game");
	if(FileToKeyValues(itemsKv, "scripts/items/items_game.txt"))
		KvJumpToKey(itemsKv, "items");
	slotsTrie = CreateTrie();
	SetTrieValue(slotsTrie, "primary", 0);
	SetTrieValue(slotsTrie, "secondary", 1);
	SetTrieValue(slotsTrie, "melee", 2);
	SetTrieValue(slotsTrie, "pda", 3);
	SetTrieValue(slotsTrie, "pda2", 4);
	SetTrieValue(slotsTrie, "building", 5);
	SetTrieValue(slotsTrie, "head", 6);
	SetTrieValue(slotsTrie, "misc", 7);

	// Populate the Weapon Trie
	// Creates it too, technically
	PopulateWeaponTrie();

	// Populate stacks
	for(new i = 0; i <= MAXPLAYERS; i++)
		h_objList[i] = CreateStack();

	// Hook Events
	HookEvent("player_death", Event_PlayerDeath);
	HookEvent("player_death", Event_PlayerDeathPre, EventHookMode_Pre);
	HookEvent("object_destroyed", Event_ObjectDestroyed);
	HookEvent("object_destroyed", Event_ObjectDestroyedPre, EventHookMode_Pre);
	HookEvent("player_builtobject", Event_PlayerBuiltObject);
	HookEvent("player_builtobject", Event_PlayerBuiltObjectPre, EventHookMode_Pre);
	HookEvent("player_hurt", Event_PlayerHurt);
	HookEvent("player_spawn", Event_PlayerSpawn);
	HookEvent("player_disconnect", Event_PlayerDisconnect, EventHookMode_Pre);
	HookEvent("post_inventory_application", Event_PostInventoryApplication);

	HookEvent("arena_win_panel", Event_WinPanel);
	HookEvent("teamplay_win_panel", Event_WinPanel);
	
	g_iCarryingOffs = FindSendPropInfo("CTFPlayer", "m_bCarryingObject");

	//AutoExecConfig(); // Create/request load of config file
	AutoExecConfig(false); // Dont auto-make, but load if you find it.

	CreateTimer(1.0, LogMap);
	
	AddGameLogHook(OnGameLog);
}

public OnMapStart()
{
	GetTeams(); // Loghelper says to put this here. Who am I to argue?
}

public OnConfigsExecuted()
{
	OnConVarStatsChange(cvar_wstats, "", "");
	OnConVarActionsChange(cvar_actions, "", "");
	OnConVarTeleportsChange(cvar_teleports, "", "");
	OnConVarTeleportsAgainChange(cvar_teleports_again, "", "");
	OnConVarHeadshotsChange(cvar_headshots, "", "");
	OnConVarBackstabsChange(cvar_backstabs, "", "");
	OnConVarSandvichChange(cvar_sandvich, "", "");
	OnConVarFireChange(cvar_fire, "", "");
	OnConVarHealsChange(cvar_heals, "", "");
	OnConVarRolelogfixChange(cvar_rolelogfix, "", "");
	OnConVarObjlogfixChange(cvar_objlogfix, "", "");
}

public Action:TF2_CalcIsAttackCritical(attacker, weapon, String:weaponname[], &bool:result)
{
	if(b_wstats && attacker > 0 && attacker <= MaxClients)
	{
		new weapon_index = GetWeaponIndex(weaponname[WEAPON_PREFIX_LENGTH], attacker);
		if(weapon_index != -1)
		{
			weaponStats[attacker][weapon_index][LOG_SHOTS]++;
			if((1<<weapon_index) & bulletWeapons)
				nextHurt[attacker] = weapon_index;
		}
	}
	
	return Plugin_Continue;
}

public OnAllPluginsLoaded()
{
	b_sdkhookloaded = GetExtensionFileStatus("sdkhooks.ext") == 1;
	if (b_sdkhookloaded)
		HookAllClients();
}

public OnEntityCreated(entity, const String:className[])
{
	if(b_wstats && StrEqual(className, "tf_projectile_stun_ball"))
		PushStackCell(h_stunBalls, EntIndexToEntRef(entity));
	else if(StrEqual(className, "tf_wearable_item_demoshield") || StrEqual(className, "tf_wearable_item"))
	{
		PushStackCell(h_wearables, EntIndexToEntRef(entity));
	}
}

public Action:OnGameLog(const String:message[])
{
	if (g_bBlockLog)
		return Plugin_Handled;
	
	return Plugin_Continue;
}

public Action:OnTakeDamage(victim, &attacker, &inflictor, &Float:damage, &damagetype)
{
	if(b_actions && attacker > 0 && attacker <= MaxClients && attacker != victim && inflictor > MaxClients && damage > 0.0 && IsValidEntity(inflictor) && (GetEntityFlags(victim) & (FL_ONGROUND | FL_INWATER)) == 0)
	{
		decl String:weapon[WEAPON_FULL_LENGTH];
		GetEdictClassname(inflictor, weapon, sizeof(weapon));
		if(weapon[3] == 'p' && weapon[4] == 'r') // Eliminate pumpkin bomb with the r
		{
			switch(weapon[14])
			{
				case 'r':
				{
					LogPlayerEvent(attacker, "triggered", "airshot_rocket");
					if(jumpStatus[attacker] == JUMP_ROCKET)
							LogPlayerEvent(attacker, "triggered", "air2airshot_rocket");
				}
				case 'p':
				{
					if(weapon[18] != 0)
					{
						LogPlayerEvent(attacker, "triggered", "airshot_sticky");
						if(jumpStatus[attacker] == JUMP_STICKY)
							LogPlayerEvent(attacker, "triggered", "air2airshot_sticky");
					}
					else
					{
						LogPlayerEvent(attacker, "triggered", "airshot_pipebomb");
						if(jumpStatus[attacker] == JUMP_STICKY)
							LogPlayerEvent(attacker, "triggered", "air2airshot_pipebomb");
					}
				}
				case 'a':
					LogPlayerEvent(attacker, "triggered", "airshot_arrow");
				case 'f':
					if(damage > 10.0)
						LogPlayerEvent(attacker, "triggered", "airshot_flare");
			}
		}
	}
	return Plugin_Continue;
}

public OnTakeDamage_Post(victim, attacker, inflictor, Float:damage, damagetype)
{
	if(b_wstats && attacker > 0 && attacker <= MaxClients)
	{
		new weapon_index = -1;
		new idamage = RoundFloat(damage);
		decl String:weapon[WEAPON_FULL_LENGTH];
		if (inflictor <= MaxClients) // Inflictor is a player
		{
			if (damagetype & DMG_BURN)
				return;
			if(inflictor == attacker && damagetype & 1 && damage == 1000.0) // Telefrag
				return;
			GetClientWeapon(attacker, weapon, sizeof(weapon));
			weapon_index = GetWeaponIndex(weapon[WEAPON_PREFIX_LENGTH], attacker);
		}
		else if (IsValidEdict(inflictor))
		{
			GetEdictClassname(inflictor, weapon, sizeof(weapon));
			if (weapon[WEAPON_PREFIX_LENGTH] == 'g')
				return; // grenadelauncher, but the projectile does damage, not the weapon. So this must be Charge N Targe.
			else if(weapon[3] == 'p')
			{
				weapon_index = GetWeaponIndex(weapon, attacker, inflictor);
			}
			else
			{
				// Baseballs are funky.
				// Inflictor is the BAT
				// But the melee Crush damage (and the nevergib I don't check here) aren't set
				// Still has CLUB damage though, and on a melee strike the inflictor is the PLAYER, not the weapon
				// Just in case either way, forcing it to get the index of "ball"
				if(!(damagetype & DMG_CRUSH) && (damagetype & DMG_CLUB) && StrEqual(weapon, "tf_weapon_bat_wood"))
					weapon_index = GetWeaponIndex("ball", attacker);
				else
					weapon_index = GetWeaponIndex(weapon[WEAPON_PREFIX_LENGTH], attacker);
			}
		}
		if(b_wstats && weapon_index > -1)
		{
			weaponStats[attacker][weapon_index][LOG_DAMAGE] += idamage;
			weaponStats[attacker][weapon_index][LOG_HITS]++;
		}
	}
}

public OnGameFrame()
{
	new entity;
	new owner;
	new itemindex, slot;
	decl String:tempstring[15];
	if(stunBallId > -1)
	{
		while(PopStackCell(h_stunBalls, entity))
		{
			if(IsValidEntity(entity))
			{
				owner = GetEntPropEnt(entity, Prop_Send, "m_hThrower");
				if(owner > 0 && owner <= MaxClients)
					weaponStats[owner][stunBallId][LOG_SHOTS]++;
			}
		}
	}
	while(PopStackCell(h_wearables, entity))
	{
		if(IsValidEntity(entity))
		{
			owner = GetEntPropEnt(entity, Prop_Send, "m_hOwnerEntity");
			if(owner > 0 && owner <= MaxClients)
			{
				itemindex = GetEntProp(entity, Prop_Send, "m_iItemDefinitionIndex");
				Format(tempstring, sizeof(tempstring), "%d", itemindex);
				if(KvJumpToKey(itemsKv, tempstring))
				{
					KvGetString(itemsKv, "item_slot", tempstring, sizeof(tempstring));
					if(GetTrieValue(slotsTrie, tempstring, slot))
					{
						if(slot == 0 && playerClass[owner] == TFClass_DemoMan)
							slot++;
						if(playerLoadout[owner][slot][0] != itemindex)
						{
							playerLoadout[owner][slot][0] = itemindex;
							playerLoadoutUpdated[owner] = true;
						}
						playerLoadout[owner][slot][1] = entity;
					}
					KvGoBack(itemsKv);
				}
			}
		}
	}
	
	new cnt = GetClientCount();
	for (new i = 1; i <= cnt; i++)
	{
		if (IsClientInGame(i) && GetEntData(i, g_iCarryingOffs, 1))
			g_bCarryingObject[i] = true;
	}
}

public OnClientPutInServer(client)
{
	if (b_sdkhookloaded)
	{
		SDKHook(client, SDKHook_OnTakeDamagePost, OnTakeDamage_Post);
		SDKHook(client, SDKHook_OnTakeDamage, OnTakeDamage);
	}
	playerLoadoutUpdated[client] = true;
	
	g_bCarryingObject[client] = false;
	
	for(new i = 0; i <= MaxClients; i++)
	{
		// Clear both "we built" (client first) and "we used" (i first)
		f_lastTeleport[client][i] = 0.0;
		f_lastTeleport[i][client] = 0.0;
	}
	f_objRemoved[client] = 0.0;
	playerClass[client] = TFClass_Unknown;
	healPoints[client] = 0;
	for(new i = 0; i < MAX_LOADOUT_SLOTS; i++)
		playerLoadout[client][i] = {-1, -1};
	ResetWeaponStats(client);
}

public Action:Event_PlayerDisconnect(Handle:event, const String:name[], bool:dontBroadcast)
{
	new client = GetClientOfUserId(GetEventInt(event, "userid"));
	if(client > 0 && IsClientInGame(client))
	{
		if(b_wstats) DumpWeaponStats(client);
		if(b_heals) DumpHeals(client, " (disconnect)");
	}
	
	return Plugin_Continue;
}

HookAllClients()
{
	for (new i = 1; i <= MaxClients; i++)
		if (IsClientInGame(i))
		{
			SDKHook(i, SDKHook_OnTakeDamagePost, OnTakeDamage_Post);
			SDKHook(i, SDKHook_OnTakeDamage, OnTakeDamage);
		}
}

public Action:Event_PlayerChangeclassPre(Handle:event, const String:name[], bool:dontBroadcast)
{
	// Stop log entry!
	return Plugin_Handled;
}

public Event_PlayerSpawn(Handle:event, const String:name[], bool:dontBroadcast)
{
	new Float:time = GetGameTime();
	new userid = GetEventInt(event, "userid");
	new client = GetClientOfUserId(userid);
	new TFClassType:spawnClass = TFClassType:GetEventInt(event, "class");
	jumpStatus[client] = JUMP_NONE; // Play it safe
	if(b_wstats) DumpWeaponStats(client); // Changed class without death, dump and reset stats
	//if(b_wstats) ResetWeaponStats(client);
	if(b_heals) DumpHeals(client, " (spawn)");
	if(time == f_objRemoved[client])
	{
		decl String:owner[96];
		decl String:player_authid[32];
		decl String:objname[24];
		if (!GetClientAuthString(client, player_authid, sizeof(player_authid)))
			strcopy(player_authid, sizeof(player_authid), "UNKNOWN");
		Format(owner, sizeof(owner), "\"%N<%d><%s><%s>\"", client, userid, player_authid, g_team_list[GetClientTeam(client)]);
		new objecttype;
		while(PopStackCell(h_objList[client], objecttype))
		{
			switch(objecttype)
			{
				case OBJ_DISPENSER:
					objname = "OBJ_DISPENSER";
				case OBJ_TELEPORTER_ENTRANCE:
					objname = "OBJ_TELEPORTER_ENTRANCE";
				case OBJ_TELEPORTER_EXIT:
					objname = "OBJ_TELEPORTER_EXIT";
				case OBJ_SENTRYGUN:
					objname = "OBJ_SENTRYGUN";
				case OBJ_ATTACHMENT_SAPPER:
					objname = "OBJ_ATTACHMENT_SAPPER";
				case OBJ_SENTRYGUN_MINI:
					objname = "OBJ_SENTRYGUN_MINI";
				default:
					continue;
			}
			LogToGame("%s triggered \"killedobject\" (object \"%s\") (weapon \"pda_engineer\") (objectowner %s) (spawn)", owner, objname, owner);
		}
	}
	if(b_rolelogfix && playerClass[client] != spawnClass)
	{
		switch(spawnClass)
		{
			case TFClass_Scout:
				LogRoleChange(client, "scout");
			case TFClass_Sniper:
				LogRoleChange(client, "sniper");
			case TFClass_Soldier:
				LogRoleChange(client, "soldier");
			case TFClass_DemoMan:
				LogRoleChange(client, "demoman");
			case TFClass_Medic:
				LogRoleChange(client, "medic");
			case TFClass_Heavy:
				LogRoleChange(client, "heavyweapons");
			case TFClass_Pyro:
				LogRoleChange(client, "pyro");
			case TFClass_Spy:
				LogRoleChange(client, "spy");
			case TFClass_Engineer:
				LogRoleChange(client, "engineer");
			default:
				LogRoleChange(client, "unknown");
		}
	}
	playerClass[client] = spawnClass;
	dalokohs[client] = -30.0;
}

public Event_ObjectRemoved(Handle:event, const String:name[], bool:dontBroadcast)
{
	new Float:time = GetGameTime();
	new client = GetClientOfUserId(GetEventInt(event, "userid"));
	if(time != f_objRemoved[client])
	{
		f_objRemoved[client] = time;
		while(PopStack(h_objList[client]))
			continue;
	}
	new objtype = GetEventInt(event, "objecttype");
	new objindex = GetEventInt(event, "index");
	if (IsValidEdict(objindex) && GetEntProp(GetEventInt(event, "index"), Prop_Send, "m_bMiniBuilding", 1))
	{
		objtype = OBJ_SENTRYGUN_MINI;
	}
	PushStackCell(h_objList[client], objtype);
}

public Event_PlayerStealsandvich(Handle:event, const String:name[], bool:dontBroadcast)
{
	LogPlyrPlyrEvent(GetClientOfUserId(GetEventInt(event, "target")), GetClientOfUserId(GetEventInt(event, "owner")), "triggered", "steal_sandvich", true);
}

public Event_PlayerStunned(Handle:event, const String:name[], bool:dontBroadcast)
{
	new String: properties[33];
	new stunner = GetClientOfUserId(GetEventInt(event, "stunner"));
	if(stunner > 0) // Stunner == 0 would be map stun (ghost/trigger), natascha stun (slowdown), taunt kill stun (medic, sniper)
	{
		new victim = GetClientOfUserId(GetEventInt(event, "victim"));
		if(GetEventBool(event, "victim_capping")) StrCat(properties, sizeof(properties), " (victim_capping)");
		if(GetEventBool(event, "big_stun")) StrCat(properties, sizeof(properties), " (big_stun)");
		LogPlyrPlyrEvent(stunner, victim, "triggered", "stun", true);
		if((GetEntityFlags(victim) & (FL_ONGROUND | FL_INWATER)) == 0)
			LogPlayerEvent(stunner, "triggered", "airshot_stun");
	}
}

public Event_PlayerTeleported(Handle:event, const String:name[], bool:dontBroadcast)
{
	new builderid = GetClientOfUserId(GetEventInt(event, "builderid"));
	new userid = GetClientOfUserId(GetEventInt(event, "userid"));
	new Float:curTime = GetGameTime();
	if(b_teleports_again && f_lastTeleport[builderid][userid] > curTime - TELEPORT_AGAIN_TIME)
	{
		if(userid == builderid)
		{
			LogPlayerEvent(userid, "triggered", "teleport_self_again");
		}
		else
		{
			LogPlayerEvent(builderid, "triggered", "teleport_again");
			LogPlayerEvent(userid, "triggered", "teleport_used_again");
		}
	}
	else
	{
		if(userid == builderid)
		{
			LogPlayerEvent(userid, "triggered", "teleport_self");
		}
		else
		{
			LogPlayerEvent(builderid, "triggered", "teleport");
			LogPlayerEvent(userid, "triggered", "teleport_used");
		}
	}
	f_lastTeleport[builderid][userid] = curTime;
}

public Event_DeployBuffBanner(Handle:event, const String:name[], bool:dontBroadcast)
{
	LogPlayerEvent(GetClientOfUserId(GetEventInt(event, "buff_owner")), "triggered", "buff_deployed");
}

public Event_MedicDefended(Handle:event, const String:name[], bool:dontBroadcast)
{
	LogPlayerEvent(GetClientOfUserId(GetEventInt(event, "userid")), "triggered", "defended_medic");
}

public Event_PlayerEscortScore(Handle:event, const String:name[], bool:dontBroadcast)
{
	LogPlayerEvent(GetEventInt(event, "player"), "triggered", "escort_score");
}

public Event_MedicDeath(Handle:event, const String:name[], bool:dontBroadcast)
{
	new client = GetClientOfUserId(GetEventInt(event, "userid"));
	healPoints[client] = GetEntProp(client, Prop_Send, "m_iHealPoints");
}

public Event_PlayerHurt(Handle:event, const String:name[], bool:dontBroadcast)
{
	// NOTE: The weaponid in this event is the weapon the player is HOLDING as of the event happening
	// Thus, only if we don't have SDK Hooks do we use it for airshot detection.
	if(!b_sdkhookloaded)
	{
		new attacker = GetClientOfUserId(GetEventInt(event, "attacker"));
		if(b_wstats)
		{
			if(nextHurt[attacker] > -1)
				weaponStats[attacker][nextHurt[attacker]][LOG_HITS]++;
			nextHurt[attacker] = -1;
		}
		if(b_actions)
		{
			new client = GetClientOfUserId(GetEventInt(event, "userid"));
			if(client != attacker && client > 0 && client <= MaxClients && IsClientInGame(client)
				&& IsPlayerAlive(client) && (GetEntityFlags(client) & (FL_ONGROUND | FL_INWATER)) == 0)
			{
				switch(GetEventInt(event, "weaponid"))
				{
					case TF_WEAPON_ROCKETLAUNCHER, TF_WEAPON_DIRECTHIT:
					{
						LogPlayerEvent(attacker, "triggered", "airshot_rocket");
						if(jumpStatus[attacker] == JUMP_ROCKET)
							LogPlayerEvent(attacker, "triggered", "air2airshot_rocket");
					}
					case TF_WEAPON_GRENADELAUNCHER:
					{
						LogPlayerEvent(attacker, "triggered", "airshot_pipebomb");
						if(jumpStatus[attacker] == JUMP_STICKY)
							LogPlayerEvent(attacker, "triggered", "air2airshot_pipebomb");
					}
					case TF_WEAPON_PIPEBOMBLAUNCHER:
					{
						LogPlayerEvent(attacker, "triggered", "airshot_sticky");
						if(jumpStatus[attacker] == JUMP_STICKY)
							LogPlayerEvent(attacker, "triggered", "air2airshot_sticky");
					}
					case TF_WEAPON_FLAREGUN:
					{
						if(GetEventInt(event, "damageamount") > 10)
						{
							LogPlayerEvent(attacker, "triggered", "airshot_flare");
						}
					}
					case TF_WEAPON_COMPOUND_BOW:
					{
						LogPlayerEvent(attacker, "triggered", "airshot_arrow");
					}
				}
			}
		}
	}
}

public Action:Event_ObjectDestroyedPre(Handle:event, const String:name[], bool:dontBroadcast)
{
	if (GetEntProp(GetEventInt(event, "index"), Prop_Send, "m_bMiniBuilding", 1))
	{
		g_bBlockLog = true;
	}
	
	return Plugin_Continue;
}

public Event_ObjectDestroyed(Handle:event, const String:name[], bool:dontBroadcast)
{
	if (g_bBlockLog)
	{
		g_bBlockLog = false;
		decl String:weapon[64];
		decl String:team[64];
		decl String:auth[32];
		decl String:properties[255];
		GetEventString(event, "weapon", weapon, sizeof(weapon));
		new victimuid = GetEventInt(event, "userid");
		new victim = GetClientOfUserId(victimuid);
		if (victim == 0 || !IsClientInGame(victim))
			return;
		GetClientAuthString(victim, auth, sizeof(auth));
		GetTeamName(GetClientTeam(victim), team, sizeof(team));
		Format(properties, sizeof(properties), " (object \"OBJ_SENTRYGUN_MINI\") (weapon \"%s\") (objectowner \"%N<%d><%s><%s>\")", weapon, victim, victimuid, auth, team);
		LogPlayerEvent(GetClientOfUserId(GetEventInt(event, "attacker")), "triggered", "killedobject", true, properties);
	}
}

public Action:Event_PlayerBuiltObjectPre(Handle:event, const String:name[], bool:dontBroadcast)
{
	if (g_bCarryingObject[GetClientOfUserId(GetEventInt(event, "userid"))] || GetEntProp(GetEventInt(event, "index"), Prop_Send, "m_bMiniBuilding", 1))
	{
		g_bBlockLog = true;
	}
	
	return Plugin_Continue;
}

public Event_PlayerBuiltObject(Handle:event, const String:name[], bool:dontBroadcast)
{
	new client = GetClientOfUserId(GetEventInt(event, "userid"));
	
	if (g_bBlockLog)
	{
		g_bBlockLog = false;
		if (!g_bCarryingObject[client] && GetEntProp(GetEventInt(event, "index"), Prop_Send, "m_bMiniBuilding", 1))
		{
			LogPlayerEvent(client, "triggered", "builtobject", true, " (object \"OBJ_SENTRYGUN_MINI\")");
		}
	}
	
	g_bCarryingObject[client] = false;
}

public Event_PlayerDeath(Handle:event, const String:name[], bool:dontBroadcast)
{
	new death_flags = GetEventInt(event, "death_flags");
	if((death_flags & TF_DEATHFLAG_DEADRINGER) == TF_DEATHFLAG_DEADRINGER) // Not a dead ringer death?
	{
		return;
	}
	
	new client = GetClientOfUserId(GetEventInt(event, "userid"));
	new attacker = GetClientOfUserId(GetEventInt(event, "attacker"));
	new customkill = GetEventInt(event, "customkill");
	new bits = GetEventInt(event, "damagebits");
	if(b_heals && playerClass[client] != TFClass_Medic) // medic_death event handles this for dead medics
		DumpHeals(client);
	jumpStatus[client] = JUMP_NONE; // Not jumping
	g_bCarryingObject[client] = false;
	if(b_actions)
	{
		if(attacker == client && customkill == TF_CUSTOM_SUICIDE)
			LogPlayerEvent(client, "triggered", "force_suicide");
		else
		{
			switch(jumpStatus[client])
			{
				case 2:
				{
					LogPlayerEvent(client, "triggered", "rocket_failjump");
					if(attacker > 0 && attacker != client)
						LogPlayerEvent(attacker, "triggered", "rocket_jumper_kill");
				}
				case 3:
				{
					LogPlayerEvent(client, "triggered", "sticky_failjump");
					if(attacker > 0 && attacker != client)
						LogPlayerEvent(attacker, "triggered", "sticky_jumper_kill");
				}
			}
			if(bits & DMG_DROWN)
			{
				LogPlayerEvent(client, "triggered", "drowned");
			}
			else if(attacker != client)
			{
				switch(jumpStatus[attacker]) // Don't need to check attacker != 0 here as world will never rocket/sticky jump
				{
					case 2:
						LogPlayerEvent(attacker, "triggered", "rocket_jump_kill");
					case 3:
						LogPlayerEvent(attacker, "triggered", "sticky_jump_kill");
				}
				if ((bits & DMG_ACID) && attacker > 0 && customkill != TF_CUSTOM_HEADSHOT)
					LogPlayerEvent(attacker, "triggered", "crit_kill");
				else if((death_flags & TF_DEATHFLAG_FIRSTBLOOD) == TF_DEATHFLAG_FIRSTBLOOD)
					LogPlayerEvent(attacker, "triggered", "first_blood");
				if (customkill == TF_CUSTOM_HEADSHOT && client > 0 && client <= MaxClients
					&& IsClientInGame(client) && (GetEntityFlags(client) & (FL_ONGROUND | FL_INWATER)) == 0)
					LogPlayerEvent(attacker, "triggered", "airshot_headshot");
			}
		}
	}
	if(b_wstats && client > 0 && attacker > 0 && attacker <= MaxClients)
	{
		decl String:weaponlogname[MAX_WEAPON_LEN];
		GetEventString(event, "weapon_logclassname", weaponlogname, sizeof(weaponlogname));
		new weapon_index = GetWeaponIndex(weaponlogname, attacker);
		if(weapon_index != -1)
		{
			weaponStats[attacker][weapon_index][LOG_KILLS]++;
			if(customkill == TF_CUSTOM_HEADSHOT)
				weaponStats[attacker][weapon_index][LOG_HEADSHOTS]++;
			weaponStats[client][weapon_index][LOG_DEATHS]++;
			if(GetClientTeam(client) == GetClientTeam(attacker))
				weaponStats[attacker][weapon_index][LOG_TEAMKILLS]++;
		}
		DumpWeaponStats(client);
	}
}

public Action:Event_PlayerDeathPre(Handle:event, const String:name[], bool:dontBroadcast)
{
	new attacker = GetClientOfUserId(GetEventInt(event, "attacker"));
	new victim = GetClientOfUserId(GetEventInt(event, "userid"));
	new customkill = GetEventInt(event, "customkill");
	new inflictor = GetEventInt(event, "inflictor_entindex");
	if (!IsValidEdict(inflictor))
	{
		inflictor = 0;
	}
	
	switch (customkill)
	{
		case TF_CUSTOM_HEADSHOT:
			if(b_headshots)
			{
				LogPlyrPlyrEvent(attacker, victim, "triggered", "headshot");
			}
		case TF_CUSTOM_BACKSTAB:
			if(b_backstabs)
			{
				LogPlyrPlyrEvent(attacker, victim, "triggered", "backstab");
			}
		case TF_CUSTOM_BURNING_ARROW, TF_CUSTOM_FLYINGBURN:
			if(b_fire)
			{
				decl String:logweapon[64];
				GetEventString(event, "weapon_logclassname", logweapon, sizeof(logweapon));
				if(logweapon[0] != 'd') // No changing reflects - was 'r' but it is deflects
				{
					SetEventString(event, "weapon_logclassname", "tf_projectile_arrow_fire");
				}
			}
		case TF_CUSTOM_TAUNT_UBERSLICE:
			{
				if(GetEventInt(event, "weaponid") == TF_WEAPON_BONESAW)
				{
					SetEventString(event, "weapon_logclassname", "taunt_medic");
					
					// Might as well fix the kill icon, too, as long as we're here
					// Courtesy of FlaminSarge
					SetEventString(event, "weapon", "taunt_medic");
				}
			}
		
		case TF_CUSTOM_DECAPITATION_BOSS:
			{
				LogPlayerEvent(attacker, "triggered", "killed_by_horseman", true);
			}
	}
	
	return Plugin_Continue;
}

public Event_WinPanel(Handle:event, const String:name[], bool:dontBroadcast)
{
	if(b_actions)
	{
		LogPlayerEvent(GetEventInt(event, "player_1"), "triggered", "mvp1");
		LogPlayerEvent(GetEventInt(event, "player_2"), "triggered", "mvp2");
		LogPlayerEvent(GetEventInt(event, "player_3"), "triggered", "mvp3");
	}
	if(b_wstats)
		DumpAllWeaponStats();
}

public Event_RocketJump(Handle:event, const String:name[], bool:dontBroadcast)
{
	new client = GetClientOfUserId(GetEventInt(event, "userid"));
	new status = jumpStatus[client];
	if(status == JUMP_ROCKET_START) // Taunt kills trigger one event, rocket jumps two
	{
		jumpStatus[client] = JUMP_ROCKET;
		LogPlayerEvent(client, "triggered", "rocket_jump");
	}
	else if(status != JUMP_ROCKET)
		jumpStatus[client] = JUMP_ROCKET_START;
}

public Event_StickyJump(Handle:event, const String:name[], bool:dontBroadcast)
{
	new client = GetClientOfUserId(GetEventInt(event, "userid"));
	if(jumpStatus[client] != JUMP_STICKY)
	{
		jumpStatus[client] = JUMP_STICKY;
		LogPlayerEvent(client, "triggered", "sticky_jump");
	}
}

public Event_JumpLanded(Handle:event, const String:name[], bool:dontBroadcast)
{
	jumpStatus[GetClientOfUserId(GetEventInt(event, "userid"))] = JUMP_NONE;
}

public Event_ObjectDeflected(Handle:event, const String:name[], bool:dontBroadcast)
{
	new client = GetClientOfUserId(GetEventInt(event, "userid"));
	new owner = GetClientOfUserId(GetEventInt(event, "ownerid"));
	switch(GetEventInt(event, "weaponid"))
	{
		case TF_WEAPON_NONE:
		{
			LogPlyrPlyrEvent(client, owner, "triggered", "airblast_player", true);
		}
		case TF_WEAPON_ROCKETLAUNCHER:
		{
			LogPlyrPlyrEvent(client, owner, "triggered", "deflected_rocket", true);
			if(b_wstats && b_sdkhookloaded)
			{
				new weapon_index = GetWeaponIndex("deflect_rocket");
				if(weapon_index > -1)
					weaponStats[client][weapon_index][LOG_SHOTS]++;
			}
		}
		case TF_WEAPON_GRENADE_DEMOMAN:
		{
			LogPlyrPlyrEvent(client, owner, "triggered", "deflected_pipebomb", true);
			if(b_wstats && b_sdkhookloaded)
			{
				new weapon_index = GetWeaponIndex("deflect_promode");
				if(weapon_index > -1)
					weaponStats[client][weapon_index][LOG_SHOTS]++;
			}
		}
		case TF_WEAPON_FLAREGUN:
		{
			LogPlyrPlyrEvent(client, owner, "triggered", "deflected_flare", true);
			if(b_wstats && b_sdkhookloaded)
			{
				new weapon_index = GetWeaponIndex("deflect_flare");
				if(weapon_index > -1)
					weaponStats[client][weapon_index][LOG_SHOTS]++;
			}
		}
		case TF_WEAPON_JAR:
		{
			LogPlyrPlyrEvent(client, owner, "triggered", "deflected_jarate", true);
		}
		case TF_WEAPON_COMPOUND_BOW:
		{
			LogPlyrPlyrEvent(client, owner, "triggered", "deflected_arrow", true);
			if(b_wstats && b_sdkhookloaded)
			{
				new weapon_index = GetWeaponIndex("deflect_arrow");
				if(weapon_index > -1)
					weaponStats[client][weapon_index][LOG_SHOTS]++;
			}
		}
		case TF_WEAPON_DIRECTHIT:
		{
			LogPlyrPlyrEvent(client, owner, "triggered", "deflected_rocket_dh", true);
			if(b_wstats && b_sdkhookloaded)
			{
				new weapon_index = GetWeaponIndex("deflect_rocket");
				if(weapon_index > -1)
					weaponStats[client][weapon_index][LOG_SHOTS]++;
			}
		}
		case TF_WEAPON_GRENADE_STUNBALL:
		{
			LogPlyrPlyrEvent(client, owner, "triggered", "deflected_baseball", true);
		}
	}
}

public Event_PostInventoryApplication(Handle:event, const String:name[], bool:dontBroadcast)
{
	CreateTimer(0.2, CheckPlayerLoadout, GetEventInt(event, "userid"));
}

public Action:CheckPlayerLoadout(Handle:timer, any:userid)
{
	new client = GetClientOfUserId(userid);
	if (client == 0 || !IsClientInGame(client))
	{
		return Plugin_Stop;
	}
	
	new ent = -1;
	new bool:newLoadout = false;
	new TFClassType:pClass = playerClass[client];
	for(new checkslot = 0; checkslot <=5; checkslot++)
	{
		if(playerLoadout[client][checkslot][1] != 0 && IsValidEntity(playerLoadout[client][checkslot][1]))
		{
			continue;
		}
		ent = GetPlayerWeaponSlot(client, checkslot);
		if(ent == -1)
		{
			// Nothing in slot?
			if(b_sdkhookloaded && checkslot < 3 && (pClass == TFClass_Soldier || pClass == TFClass_DemoMan)) // Maybe gunboats? Or charge n targe?
			{
				playerLoadout[client][checkslot][1] = -1;
				continue;
			}
			if(playerLoadout[client][checkslot][0] == -1)
				continue;
			playerLoadout[client][checkslot] = {-1, -1};
			newLoadout = true;
		}
		else
		{
			new itemindex = GetEntProp(ent, Prop_Send, "m_iItemDefinitionIndex");
			if(playerLoadout[client][checkslot][0] != itemindex)
			{
				playerLoadout[client][checkslot][0] = itemindex;
				newLoadout = true;
			}
			playerLoadout[client][checkslot][1] = EntIndexToEntRef(ent);
		}
	}
	if(b_sdkhookloaded)
	{
		if(newLoadout) // Just in case we already updated it due to a hat spawning or being a new client
			playerLoadoutUpdated[client] = true;
		CreateTimer(0.2, LogWeaponLoadout, userid);
		return Plugin_Stop;
	}
	if (newLoadout)
		LogWeaponLoadout(INVALID_HANDLE, userid);
		
	return Plugin_Stop;
}

public Action:LogWeaponLoadout(Handle:timer, any:userid)
{
	new client = GetClientOfUserId(userid);
	if (client > 0 && IsClientInGame(client))
	{
		for (new i = 0; i < MAX_LOADOUT_SLOTS; i++)
		{
			if(playerLoadout[client][i][0] != -1 && !IsValidEntity(playerLoadout[client][i][1]) || playerLoadout[client][i][1] == 0)
			{
				playerLoadout[client][i] = {-1, -1};
				playerLoadoutUpdated[client] = true;
			}
		}
		if (playerLoadoutUpdated[client] == false)
			return Plugin_Stop;
		playerLoadoutUpdated[client] = false;
		
		decl String:logString[255];
		Format(logString, sizeof(logString), " (primary \"%d\") (secondary \"%d\") (melee \"%d\") (pda \"%d\") (pda2 \"%d\") (building \"%d\") (head \"%d\") (misc \"%d\")", playerLoadout[client][0][0], playerLoadout[client][1][0], playerLoadout[client][2][0], playerLoadout[client][3][0], playerLoadout[client][4][0], playerLoadout[client][5][0], playerLoadout[client][6][0], playerLoadout[client][7][0]);
		
		LogPlayerEvent(client, "triggered", "player_loadout", _, logString);
	}
	return Plugin_Stop;
}

public Action:Event_PlayerJarated(UserMsg:msg_id, Handle:bf, const players[], playersNum, bool:reliable, bool:init)
{
	new client = BfReadByte(bf);
	new victim = BfReadByte(bf);
	
	if (!victim || !IsClientInGame(victim))
	{
		return Plugin_Continue;
	}
	

	if (TF2_IsPlayerInCondition(victim, TFCond_Jarated))
	{
		LogPlyrPlyrEvent(client, victim, "triggered", "jarate", true);
	}
	else if (TF2_IsPlayerInCondition(victim, TFCond_Milked))
	{
		LogPlyrPlyrEvent(client, victim, "triggered", "madmilk", true);
	}
	
	return Plugin_Continue;
}

public Action:Event_PlayerShieldBlocked(UserMsg:msg_id, Handle:bf, const players[], playersNum, bool:reliable, bool:init)
{
	new victim = BfReadByte(bf);
	new client = BfReadByte(bf);

	LogPlyrPlyrEvent(client, victim, "triggered", "shield_blocked", true);
	return Plugin_Continue;
}

// Modified Octo's method a bit to try and reduce checking of sound strings
public Action:SoundHook(clients[64], &numClients, String:sample[PLATFORM_MAX_PATH], &entity, &channel, &Float:volume, &level, &pitch, &flags)
{
	if(entity <= MaxClients && clients[0] == entity && playerClass[entity] == TFClass_Heavy && StrEqual(sample,"vo/SandwichEat09.wav"))
	{
		switch(playerLoadout[entity][1][0])
		{
			case LUNCHBOX_CHOCOLATE:
			{
				LogPlayerEvent(entity, "triggered", "dalokohs");
				new Float:time = GetGameTime();
				if(time - dalokohs[entity] > 30)
					LogPlayerEvent(entity, "triggered", "dalokohs_healthboost");
				dalokohs[entity] = time;
				if(GetClientHealth(entity) < 350)
					LogPlayerEvent(entity, "triggered", "dalokohs_healself");
			}
			case LUNCHBOX_STEAK:
			{
				LogPlayerEvent(entity, "triggered", "steak");
			}
			default:
			{
				LogPlayerEvent(entity, "triggered", "sandvich");
				if(GetClientHealth(entity) < 300)
					LogPlayerEvent(entity, "triggered", "sandvich_healself");
			}
		}
	}
	return Plugin_Continue;
}

public Action:LogMap(Handle:timer)
{
	// Called 1 second after OnPluginStart since srcds does not log the first map loaded. Idea from Stormtrooper's "mapfix.sp" for psychostats
	LogMapLoad();
}

PopulateWeaponTrie()
{
	// Create a Trie
	h_weapontrie = CreateTrie();
	
	// Initial populate
	for(new i = 0; i < MAX_LOG_WEAPONS; i++)
		SetTrieValue(h_weapontrie, weaponList[i], i);

	// Figure out the Bullet Weapon ids (based on the list of bullet weapons)
	new index;
	bulletWeapons = 0;
	for(new i = 0; i < MAX_BULLET_WEAPONS; i++)
		if(GetTrieValue(h_weapontrie, weaponBullet[i], index))
			bulletWeapons |= (1<<index);
	
	// Flag unlockable enabled weapons as such
	for(new i = 0; i < MAX_UNLOCKABLE_WEAPONS; i++)
		if(GetTrieValue(h_weapontrie, weaponUnlockables[i], index))
			SetTrieValue(h_weapontrie, weaponUnlockables[i], index | UNLOCKABLE_BIT);

	// Aux entries, based on the position of other entries in the list
	// This is last so that we inherit unlockable weapon flags.
	// Note: As of this writing, none of the below have unlockable variants anyway.
	if(GetTrieValue(h_weapontrie, "tf_projectile_arrow", index))
	{
		SetTrieValue(h_weapontrie, "compound_bow", index);
		SetTrieValue(h_weapontrie, "tf_projectile_arrow_fire", index);
	}
	if(GetTrieValue(h_weapontrie, "tf_projectile_rocket", index))
		SetTrieValue(h_weapontrie, "rocketlauncher", index);
	if(GetTrieValue(h_weapontrie, "rocketlauncher_directhit", index))
		SetTrieValue(h_weapontrie, "tf_projectile_rocket_dh", index);
	if(GetTrieValue(h_weapontrie, "tf_projectile_pipe", index))
		SetTrieValue(h_weapontrie, "grenadelauncher", index);
	if(GetTrieValue(h_weapontrie, "tf_projectile_pipe_remote", index))
		SetTrieValue(h_weapontrie, "pipebomblauncher", index);
	if(GetTrieValue(h_weapontrie, "sticky_resistance", index))
		SetTrieValue(h_weapontrie, "tf_projectile_pipe_remote_sr", index);
	if(GetTrieValue(h_weapontrie, "flaregun", index))
		SetTrieValue(h_weapontrie, "tf_projectile_flare", index);
	if(GetTrieValue(h_weapontrie, "ball", index))
	{
		SetTrieValue(h_weapontrie, "tf_projectile_stun_ball", index);
		stunBallId = index;
	}
}

GetWeaponIndex(const String:weaponname[], client = 0, weapon = -1)
{
	new index;
	new bool:unlockable;
	new reflectindex = -1;
	
	if (strlen(weaponname) < 15)
		return -1;
	
	if(GetTrieValue(h_weapontrie, weaponname, index))
	{
		if(index & UNLOCKABLE_BIT)
		{
			index &= ~UNLOCKABLE_BIT;
			unlockable = true;
			
		}
		if(weaponname[3] == 'p' && weapon > -1) // Projectile?
		{
			if(client == GetEntProp(weapon, Prop_Send, "m_iDeflected"))
			{
				switch(weaponname[14])
				{
					case 'a':
						reflectindex = GetWeaponIndex("deflect_arrow");
					case 'f':
						reflectindex = GetWeaponIndex("deflect_flare");
					case 'p':
						if(weaponname[19] == 0) // we aren't a _remote
							reflectindex = GetWeaponIndex("deflect_promode");
					case 'r':
						reflectindex = GetWeaponIndex("deflect_rocket");
				}
			}
		}
		if(reflectindex > -1)
			return reflectindex;
		if(unlockable && client > 0)
		{
			new slot = 0;
			if(playerClass[client] == TFClass_DemoMan)
				slot = 1;
			new itemindex = playerLoadout[client][slot][0];
			switch(itemindex) // Hell of a lot easier than a set of ifs. <_<
			{
				case 36, 41, 45, 61, 127, 130:
					index++;
			}
		}
		return index;
	}
	else
		return -1;
}

DumpHeals(client, String:addProp[] = "")
{
	new curHeals = GetEntProp(client, Prop_Send, "m_iHealPoints");
	new lifeHeals = curHeals - healPoints[client];
	if(lifeHeals > 0)
	{
		decl String:szProperties[32];
		Format(szProperties, sizeof(szProperties), " (healing \"%d\")%s", lifeHeals, addProp);
		LogPlayerEvent(client, "triggered", "healed", _, szProperties);
	}
	healPoints[client] = curHeals;
}

DumpAllWeaponStats()
{
	for(new i = 1; i <= MaxClients; i++)
		DumpWeaponStats(i);
}

DumpWeaponStats(client)
{
	if(IsClientInGame(client))
	{
		decl String:player_authid[64];
		if(!GetClientAuthString(client, player_authid, sizeof(player_authid)))
			strcopy(player_authid, sizeof(player_authid), "UNKNOWN");
		new player_team = GetClientTeam(client);
		new player_userid = GetClientUserId(client);
		for (new i = 0; i < MAX_LOG_WEAPONS; i++)
			if(weaponStats[client][i][LOG_SHOTS] > 0 || weaponStats[client][i][LOG_DEATHS] > 0)
				LogToGame("\"%N<%d><%s><%s>\" triggered \"weaponstats\" (weapon \"%s\") (shots \"%d\") (hits \"%d\") (kills \"%d\") (headshots \"%d\") (tks \"%d\") (damage \"%d\") (deaths \"%d\")", client, player_userid, player_authid, g_team_list[player_team], weaponList[i], weaponStats[client][i][LOG_SHOTS], weaponStats[client][i][LOG_HITS], weaponStats[client][i][LOG_KILLS], weaponStats[client][i][LOG_HEADSHOTS], weaponStats[client][i][LOG_TEAMKILLS], weaponStats[client][i][LOG_DAMAGE], weaponStats[client][i][LOG_DEATHS]);
	}
	ResetWeaponStats(client);
}

ResetWeaponStats(client)
{
	for(new i = 0; i < MAX_LOG_WEAPONS; i++)
		weaponStats[client][i] = {0,0,0,0,0,0,0};
}

public OnConVarActionsChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	new bool:newval = GetConVarBool(cvar_actions);
	if(newval != b_actions)
	{
		if(newval)
		{
			HookEvent("player_escort_score", Event_PlayerEscortScore);
			HookEvent("player_stealsandvich", Event_PlayerStealsandvich);
			HookEvent("player_stunned", Event_PlayerStunned);
			HookEvent("deploy_buff_banner", Event_DeployBuffBanner);
			HookEvent("medic_defended", Event_MedicDefended);
			HookEvent("rocket_jump", Event_RocketJump);
			HookEvent("rocket_jump_landed", Event_JumpLanded);
			HookEvent("sticky_jump", Event_StickyJump);
			HookEvent("sticky_jump_landed", Event_JumpLanded);
			HookEvent("object_deflected", Event_ObjectDeflected);
			HookUserMessage(GetUserMessageId("PlayerJarated"), Event_PlayerJarated);
			HookUserMessage(GetUserMessageId("PlayerShieldBlocked"), Event_PlayerShieldBlocked);
		}
		else
		{
			UnhookEvent("player_escort_score", Event_PlayerEscortScore);
			UnhookEvent("player_stealsandvich", Event_PlayerStealsandvich);
			UnhookEvent("player_stunned", Event_PlayerStunned);
			UnhookEvent("deploy_buff_banner", Event_DeployBuffBanner);
			UnhookEvent("medic_defended", Event_MedicDefended);
			UnhookEvent("rocket_jump", Event_RocketJump);
			UnhookEvent("rocket_jump_landed", Event_JumpLanded);
			UnhookEvent("sticky_jump", Event_StickyJump);
			UnhookEvent("sticky_jump_landed", Event_JumpLanded);
			UnhookEvent("object_deflected", Event_ObjectDeflected);
			UnhookUserMessage(GetUserMessageId("PlayerJarated"), Event_PlayerJarated);
			UnhookUserMessage(GetUserMessageId("PlayerShieldBlocked"), Event_PlayerShieldBlocked);
			for(new i = 1; i <= MaxClients; i++)
				jumpStatus[i] = 0;
		}
		b_actions = newval;
	}
}


public OnConVarTeleportsChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	new bool:newval = GetConVarBool(cvar_teleports);
	if(newval != b_teleports)
	{
		if(newval)
			HookEvent("player_teleported", Event_PlayerTeleported);
		else
			UnhookEvent("player_teleported", Event_PlayerTeleported);
		b_teleports = newval;
	}
}

public OnConVarTeleportsAgainChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	b_teleports_again = GetConVarBool(cvar_teleports_again);
}

public OnConVarHeadshotsChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	b_headshots = GetConVarBool(cvar_headshots);
}

public OnConVarBackstabsChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	b_backstabs = GetConVarBool(cvar_backstabs);
}

public OnConVarSandvichChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	new bool:newval = GetConVarBool(cvar_sandvich);
	if(newval != b_sandvich)
	{
		if(newval)
			AddNormalSoundHook(SoundHook);
		else
			RemoveNormalSoundHook(SoundHook);
		b_sandvich = newval;
	}
}

public OnConVarFireChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	b_fire = GetConVarBool(cvar_fire);
}

public OnConVarStatsChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	new bool:newval = GetConVarBool(cvar_crits) && GetConVarBool(cvar_wstats);
	if(newval != b_wstats)
	{
		if(!newval)
		{
			DumpAllWeaponStats();
		}
		b_wstats = newval;
	}
}

public OnConVarHealsChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	new bool:newval = GetConVarBool(cvar_heals);
	if(newval != b_heals)
	{
		if(newval)
		{
			HookEvent("medic_death", Event_MedicDeath);
			for(new i = 1; i <= MaxClients; i++)
				if(IsClientInGame(i))
					healPoints[i] = GetEntProp(i, Prop_Send, "m_iHealPoints");
		}
		else
		{
			UnhookEvent("medic_death", Event_MedicDeath);
		}
		b_heals = newval;
	}
}

public OnConVarRolelogfixChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	new bool:newval = GetConVarBool(cvar_rolelogfix);
	if(newval != b_rolelogfix)
	{
		if(newval)
			HookEvent("player_changeclass", Event_PlayerChangeclassPre, EventHookMode_Pre);
		else
			UnhookEvent("player_changeclass", Event_PlayerChangeclassPre, EventHookMode_Pre);
		b_rolelogfix = newval;
	}
}

public OnConVarObjlogfixChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	new bool:newval = GetConVarBool(cvar_objlogfix);
	if(newval != b_objlogfix)
	{
		if(newval)
			HookEvent("object_removed", Event_ObjectRemoved);
		else
			UnhookEvent("object_removed", Event_ObjectRemoved);
		b_objlogfix = newval;
	}
}