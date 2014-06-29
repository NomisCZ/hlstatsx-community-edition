/**
 * HLstatsX Community Edition - SourceMod plugin to generate advanced weapon logging
 * http://www.hlxcommunity.com
 * Copyright (C) 2009-2012 Nicholas Hastings (psychonic)
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

#define NAME "SuperLogs: Dino D-Day"
#define VERSION "1.1.1"

#define MAX_LOG_WEAPONS 47
#define MAX_WEAPON_LEN 12
#define PREFIX_LEN 7

// Some DDD Things
#define WEAPON_MG42	18
#define WEAPON_TREX	37
#define PLAYERCLASS_TREX	8
#define PLAYERCLASS_DILOPHOSAURUS	5
#define CUSTOMKILL_GOAT	5
#define GOAT_BITE	19
#define DDD_TEAM_ALLIES	2
#define DDD_TEAM_AXIS	3

new g_weapon_stats[MAXPLAYERS+1][MAX_LOG_WEAPONS][15];
new const String:g_weapon_list[MAX_LOG_WEAPONS][MAX_WEAPON_LEN] = { 
									"",
									"mp40",
									"thompson",
									"shotgun",
									"",
									"pistol",
									"",
									"",
									"garand",
									"bar",
									"luger",
									"",
									"piat",
									"",
									"mosin",
									"k98",
									"",
									"",
									"mg42",
									"",
									"k98sniper",
									"",
									"",
									"",
									"flechette",
									"",
									"",
									"",
									"",
									"mp44",
									"",
									"",
									"sten",
									"p38",
									"nagant",
									"",
									"",
									"trex",
									"",
									"",
									"trigger",
									"stygimoloch",
									"",
									"",
									"",
									"carbine",
									"greasegun"
								};

new g_iNextHitgroup[MAXPLAYERS+1];
new bool:g_DiloGoatBite[MAXPLAYERS+1];

new g_LastClass[MAXPLAYERS+1] = { -1, ... };
new g_LastTeam[MAXPLAYERS+1] = { -1, ... };

new bool:g_bLate;
new bool:g_bIgnoreLog;

PauseLogging() { g_bIgnoreLog = true; }
ResumeLogging() { g_bIgnoreLog = false; }

#include <loghelper>
#include <wstatshelper>


public Plugin:myinfo = {
	name = NAME,
	author = "psychonic and FeuerSturm",
	description = "Advanced logging for Dino D-Day. Generates auxilary logging for use with log parsers such as HLstatsX and Psychostats",
	version = VERSION,
	url = "http://www.hlxce.com"
};

public APLRes:AskPluginLoad2( Handle:myself, bool:late, String:error[], err_max )
{
	decl String:szGameDir[64];
	GetGameFolderName( szGameDir, sizeof(szGameDir) );
	if ( !!strcmp( szGameDir, "dinodday" ) )
	{
		strcopy( error, err_max, "This plugin is only supported on Dino D-Day" );
		return APLRes_Failure;
	}
	
	g_bLate = late;
	
	return APLRes_Success;
}

public OnPluginStart()
{
	CreatePopulateWeaponTrie();
	new Handle:hVersion = CreateConVar( "superlogs_dinodday_version", VERSION, NAME, FCVAR_NOTIFY|FCVAR_DONTRECORD );
	SetConVarString(hVersion, VERSION, false, true);
	
	HookEvent( "player_death",       Event_PlayerDeathPre,       EventHookMode_Pre        );
	HookEvent( "player_death",       Event_PlayerDeath,          EventHookMode_Post       );
	HookEvent( "player_spawn",       Event_PlayerSpawn,          EventHookMode_Post       );
	HookEvent( "update_roundscore",  Event_RoundEnd,             EventHookMode_Post       );
	HookEvent( "player_changeclass", Event_PlayerChangeClassPre, EventHookMode_Pre        );
	HookEvent( "player_changeclass", Event_PlayerChangeClass,    EventHookMode_Post       );
	HookEvent( "teamplay_point_captured", Event_PointCaptured,    EventHookMode_Post       );
	
	AddGameLogHook( OnGameLog );
	AddTempEntHook( "Shotgun Shot", OnFireBullets );
}

public OnAllPluginsLoaded()
{
	for ( new i = 1; i <= MaxClients; i++ )
	{
		if ( IsClientInGame( i ) )
		{
			OnClientPutInServer( i );
		}
	}
}

public OnMapStart()
{
	static bool:bLoggedMap = false;
	if( g_bLate && !bLoggedMap )
	{
		LogMapLoad();
	}
	
	bLoggedMap = true;
	
	GetTeams();
}

public OnClientPutInServer( client )
{
	SDKHook( client, SDKHook_TraceAttackPost, OnTraceAttack );
	SDKHook( client, SDKHook_OnTakeDamagePost, OnTakeDamage );
	reset_player_stats( client );
	
	g_LastTeam[client] = -1;
	g_LastClass[client] = -1;
	g_DiloGoatBite[client] = false;
}

public Action:OnFireBullets( const String:szName[], const clients[], clientCount, Float:flDelay )
{
	new client = TE_ReadNum( "m_iPlayer" ) + 1;
	new weapon_index = TE_ReadNum( "m_iWeaponID" );
	
	if( weapon_index >= 0 )
	{
		if( GetEntProp(client, Prop_Send, "m_iPlayerClass") == PLAYERCLASS_TREX && weapon_index == WEAPON_MG42 )
		{
			weapon_index = WEAPON_TREX;
		}
		g_weapon_stats[client][weapon_index][LOG_HIT_SHOTS]++;
	}
	
	return Plugin_Continue;
}

public OnTraceAttack( victim, attacker, inflictor, Float:damage, damagetype, ammotype, hitbox, hitgroup )
{
	if ( hitgroup > 0 && attacker > 0 && attacker <= MaxClients && victim > 0 && victim <= MaxClients )
	{
		g_iNextHitgroup[victim] = hitgroup;
	}
}

public OnTakeDamage( victim, attacker, inflictor, Float:damage, damagetype )
{	
	if ( attacker > 0 && attacker <= MaxClients && victim > 0 && victim <= MaxClients )
	{
		decl String: weapon[MAX_WEAPON_LEN + PREFIX_LEN];
		GetClientWeapon( attacker, weapon, sizeof(weapon) );
		
		new weapon_index = get_weapon_index(weapon[PREFIX_LEN]);
		new hitgroup = g_iNextHitgroup[victim];
		if ( hitgroup < 8 )
		{
			hitgroup += LOG_HIT_OFFSET;
		}
		
		new bool:headshot = ( !IsPlayerAlive( victim ) && g_iNextHitgroup[victim] == HITGROUP_HEAD );
		if ( weapon_index > -1 )
		{
			g_weapon_stats[attacker][weapon_index][LOG_HIT_HITS]++;
			g_weapon_stats[attacker][weapon_index][LOG_HIT_DAMAGE] += RoundToNearest( damage );
			g_weapon_stats[attacker][weapon_index][hitgroup]++;
			if ( headshot )
			{
				g_weapon_stats[attacker][weapon_index][LOG_HIT_HEADSHOTS]++;
				LogPlayerEvent( attacker, "triggered", "headshot" );
			}
		}

		g_iNextHitgroup[victim] = 0;
	}
}

public OnEntityCreated(entity, const String:classname[])
{
	if(entity > MaxClients && IsValidEdict(entity) && StrEqual(classname, "npc_goat", true))
	{
		SDKHook(entity, SDKHook_ThinkPost, OnGoatThinkPost);
	}
}

public OnGoatThinkPost(npc_goat)
{
	if(npc_goat > MaxClients && IsValidEdict(npc_goat))
	{
		new client = GetEntPropEnt(npc_goat, Prop_Send, "moveparent");
		if(client >= 1 && client <= MaxClients && IsClientInGame(client) && IsPlayerAlive(client) && GetClientTeam(client) == DDD_TEAM_AXIS && GetEntProp(client, Prop_Send, "m_iPlayerClass") == PLAYERCLASS_DILOPHOSAURUS)
		{
			if(GetEntProp(client, Prop_Send, "m_nSequence") == GOAT_BITE)
			{
				g_DiloGoatBite[client] = true;
			}
		}
	}	
}

public Action:OnGameLog( const String:szMessage[] )
{
	if( g_bIgnoreLog )
		return Plugin_Handled;
	
	return Plugin_Continue;
}

public Action:Event_PointCaptured( Handle:event, const String:name[], bool:dontBroadcast )
{
	new client;
	decl String:cappers[256];
	GetEventString(event, "cappers", cappers, sizeof(cappers));
	for (new i = 0 ; i < strlen(cappers); i++)
	{
		client = cappers[i];
		if(client > 0 && client <= MaxClients && IsClientInGame(client))
		{
			LogPlayerEvent(client, "triggered", "point_captured");
		}
	}
	
	return Plugin_Continue;
}

public Action:Event_PlayerChangeClassPre( Handle:event, const String:name[], bool:dontBroadcast )
{
	PauseLogging();
	
	return Plugin_Continue;
}

public Event_PlayerChangeClass( Handle:event, const String:name[], bool:dontBroadcast )
{
	ResumeLogging();
}

public Action:Event_PlayerDeathPre( Handle:event, const String:name[], bool:dontBroadcast )
{
	PauseLogging();
	
	new customkill = GetEventInt(event, "customkill");
	if( customkill == CUSTOMKILL_GOAT )
	{
		new attacker = GetClientOfUserId( GetEventInt( event, "attacker" ) );
		new victim = GetClientOfUserId( GetEventInt( event, "userid" ) );
		if(attacker == victim && g_DiloGoatBite[attacker])
		{
			SetEventBroadcast(event, true);
			return Plugin_Continue;
		}
	}
	
	return Plugin_Continue;
}

public Event_PlayerDeath( Handle:event, const String:name[], bool:dontBroadcast )
{
	ResumeLogging();
	
	new victim = GetClientOfUserId( GetEventInt( event, "userid" ) );
	
	if ( victim > 0 )
	{
		new attacker = GetClientOfUserId( GetEventInt( event, "attacker" ) );

		decl String:weapon[32];
		GetEventString( event, "weapon", weapon, sizeof(weapon) );
		if( attacker > 0 && attacker != victim )
		{
			new weapon_index = get_weapon_index( weapon );
			if ( weapon_index > -1 )
			{
				g_weapon_stats[attacker][weapon_index][LOG_HIT_KILLS]++;		
				g_weapon_stats[victim][weapon_index][LOG_HIT_DEATHS]++;
				if ( GetClientTeam( attacker ) == GetClientTeam( victim ) )
				{
					g_weapon_stats[attacker][weapon_index][LOG_HIT_TEAMKILLS]++;
				}	
			}
			
			LogKill( attacker, victim, weapon, true );
			
			new dominated = GetEventInt(event, "dominated");
			new revenge = GetEventInt(event, "revenge");
			if(dominated == 1)
			{
				LogPlyrPlyrEvent(attacker, victim, "triggered", "dominated");
			}
			else if(revenge == 1)
			{
				LogPlyrPlyrEvent(attacker, victim, "triggered", "revenge");
			}

			new assisteruid = GetEventInt(event, "assister");
			if(assisteruid != -1)
			{
				new assister = GetClientOfUserId(assisteruid);
				if(assister > 0 && attacker != assister)
				{
					LogPlayerEvent(assister, "triggered", "assister");
					if(GetEventInt(event, "assister_revenge") == 1)
					{
						LogPlyrPlyrEvent(assister, victim, "triggered", "assister_revenge");
					}
					else if(GetEventInt(event, "assister_dominated") == 1)
					{
						LogPlyrPlyrEvent(assister, victim, "triggered", "assister_dominated");
					}
				}
			}
			
		}
		else
		{
			new customkill = GetEventInt(event, "customkill");
			if( customkill == CUSTOMKILL_GOAT )
			{
				if(g_DiloGoatBite[attacker])
				{
					g_DiloGoatBite[attacker] = false;
					return;
				}
				LogPlayerEvent(attacker, "triggered", "kill_goat");
				return;
			}
			
			LogPlayerEvent(victim, "committed suicide with", weapon);
		}
		
		dump_player_stats( victim );
	}
}

public Event_PlayerSpawn( Handle:event, const String:name[], bool:dontBroadcast )
{
	new client = GetClientOfUserId( GetEventInt( event, "userid" ) );
	if( client == 0 || !IsClientInGame(client) )
		return;
	
	reset_player_stats( client );
	
	new currentTeam = GetClientTeam( client );
	if( currentTeam != DDD_TEAM_ALLIES && currentTeam != DDD_TEAM_AXIS )
		return;
	
	new currentClass = GetEntProp( client, Prop_Send, "m_iPlayerClass" );
	if( g_LastClass[client] != currentClass || g_LastTeam[client] != currentTeam )
	{
		decl String:szRoleString[32];
		if( currentTeam == DDD_TEAM_ALLIES )
			Format( szRoleString, sizeof(szRoleString), "#class_blue_class%d", currentClass+1 );
		else // == DDD_TEAM_AXIS
			Format( szRoleString, sizeof(szRoleString), "#class_red_class%d", currentClass+1 );
		
		LogRoleChange( client, szRoleString );
		g_LastTeam[client] = currentTeam;
		g_LastClass[client] = currentClass;
	}
	g_DiloGoatBite[client] = false;
}

public Event_RoundEnd( Handle:event, const String:name[], bool:dontBroadcast )
{
	new winner = GetEventInt( event, "team_that_won" );
	if( winner == DDD_TEAM_ALLIES || winner == DDD_TEAM_AXIS )
	{
		decl String:round_win[32];
		Format(round_win, sizeof(round_win), "%s", winner == DDD_TEAM_ALLIES ? "roundwin_allies" : "roundwin_axis");
		LogTeamEvent(winner, "triggered", round_win);
		WstatsDumpAll();
	}
}

public OnClientDisconnect( client )
{
	OnPlayerDisconnect( client );
}