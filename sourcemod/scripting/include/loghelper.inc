#define LOGHELPER_VERSION 3

#include <sourcemod>
#include <sdktools>

new String:g_team_list[16][64];

// Call this on map start to cache team names in g_team_list

stock GetTeams(bool:insmod = false)
{
	if (!insmod)
	{
		new max_teams_count = GetTeamCount();
		for (new team_index = 0; (team_index < max_teams_count); team_index++)
		{
			decl String: team_name[64];
			GetTeamName(team_index, team_name, sizeof(team_name));

			if (strcmp(team_name, "") != 0)
			{
				g_team_list[team_index] = team_name;
			}
		}
	}
	else
	{
		// they really need to get their act together... GetTeamName() would be awesome since they can't even keep their team indexes consistent
		decl String:mapname[64];
		GetCurrentMap(mapname, sizeof(mapname));
		if (strcmp(mapname, "ins_karam") == 0 || strcmp(mapname, "ins_baghdad") == 0)
		{
			g_team_list[1] = "Iraqi Insurgents";
			g_team_list[2] = "U.S. Marines";
		}
		else
		{
			g_team_list[1] = "U.S. Marines";
			g_team_list[2] = "Iraqi Insurgents";
		}
		g_team_list[0] = "Unassigned";
		g_team_list[3] = "SPECTATOR";
	}
}

stock LogPlayerEvent(client, const String:verb[], const String:event[], bool:display_location = false, const String:properties[] = "")
{
	if (IsValidPlayer(client))
	{
		decl String:player_authid[32];
		if (!GetClientAuthString(client, player_authid, sizeof(player_authid)))
		{
			strcopy(player_authid, sizeof(player_authid), "UNKNOWN");
		}

		if (display_location)
		{
			decl Float:player_origin[3];
			GetClientAbsOrigin(client, player_origin);
			LogToGame("\"%N<%d><%s><%s>\" %s \"%s\"%s (position \"%d %d %d\")", client, GetClientUserId(client), player_authid, g_team_list[GetClientTeam(client)], verb, event, properties, RoundFloat(player_origin[0]), RoundFloat(player_origin[1]), RoundFloat(player_origin[2])); 
		}
		else
		{
			LogToGame("\"%N<%d><%s><%s>\" %s \"%s\"%s", client, GetClientUserId(client), player_authid, g_team_list[GetClientTeam(client)], verb, event, properties); 
		}
	}
}

stock LogPlyrPlyrEvent(client, victim, const String:verb[], const String:event[], bool:display_location = false, const String:properties[] = "")
{
	if (IsValidPlayer(client) && IsValidPlayer(victim))
	{
		decl String:player_authid[32];
		if (!GetClientAuthString(client, player_authid, sizeof(player_authid)))
		{
			strcopy(player_authid, sizeof(player_authid), "UNKNOWN");
		}
		decl String:victim_authid[32];
		if (!GetClientAuthString(victim, victim_authid, sizeof(victim_authid)))
		{
			strcopy(victim_authid, sizeof(victim_authid), "UNKNOWN");
		}
		
		if (display_location)
		{
			decl Float:player_origin[3];
			GetClientAbsOrigin(client, player_origin);
			
			decl Float:victim_origin[3];
			GetClientAbsOrigin(victim, victim_origin);
			
			LogToGame("\"%N<%d><%s><%s>\" %s \"%s\" against \"%N<%d><%s><%s>\"%s (position \"%d %d %d\") (victim_position \"%d %d %d\")", client, GetClientUserId(client), player_authid, g_team_list[GetClientTeam(client)], verb, event, victim, GetClientUserId(victim), victim_authid, g_team_list[GetClientTeam(victim)], properties, RoundFloat(player_origin[0]), RoundFloat(player_origin[1]), RoundFloat(player_origin[2]), RoundFloat(victim_origin[0]), RoundFloat(victim_origin[1]), RoundFloat(victim_origin[2])); 
		}
		else
		{
			LogToGame("\"%N<%d><%s><%s>\" %s \"%s\" against \"%N<%d><%s><%s>\"%s", client, GetClientUserId(client), player_authid, g_team_list[GetClientTeam(client)], verb, event, victim, GetClientUserId(victim), victim_authid, g_team_list[GetClientTeam(victim)], properties); 
		}
	}
}

stock LogKill(attacker, victim, const String:weapon[], bool:display_location = false, const String:properties[] = "")
{
	if (IsValidPlayer(attacker) && IsValidPlayer(victim))
	{
		decl String:attacker_authid[32];
		if (!GetClientAuthString(attacker, attacker_authid, sizeof(attacker_authid)))
		{
			strcopy(attacker_authid, sizeof(attacker_authid), "UNKNOWN");
		}
		decl String:victim_authid[32];
		if (!GetClientAuthString(victim, victim_authid, sizeof(victim_authid)))
		{
			strcopy(victim_authid, sizeof(victim_authid), "UNKNOWN");
		}
			
		if (display_location)
		{
			decl Float:attacker_origin[3];
			GetClientAbsOrigin(attacker, attacker_origin);
			decl Float:victim_origin[3];
			GetClientAbsOrigin(victim, victim_origin);
			
			LogToGame("\"%N<%d><%s><%s>\" killed \"%N<%d><%s><%s>\" with \"%s\"%s (attacker_position \"%d %d %d\") (victim_position \"%d %d %d\")", attacker, GetClientUserId(attacker), attacker_authid, g_team_list[GetClientTeam(attacker)], victim, GetClientUserId(victim), victim_authid, g_team_list[GetClientTeam(victim)], weapon, properties, RoundFloat(attacker_origin[0]), RoundFloat(attacker_origin[1]), RoundFloat(attacker_origin[2]), RoundFloat(victim_origin[0]), RoundFloat(victim_origin[1]), RoundFloat(victim_origin[2])); 
		}
		else
		{
			LogToGame("\"%N<%d><%s><%s>\" killed \"%N<%d><%s><%s>\" with \"%s\"%s", attacker, GetClientUserId(attacker), attacker_authid, g_team_list[GetClientTeam(attacker)], victim, GetClientUserId(victim), victim_authid, g_team_list[GetClientTeam(victim)], weapon, properties); 
		}
	}
}

// For Psychostats "KTRAJ" kill trajectory log lines
stock LogPSKillTraj(attacker, victim, const String:weapon[])
{
	if (IsValidPlayer(attacker) && IsValidPlayer(victim))
	{
		decl String:attacker_authid[32];
		if (!GetClientAuthString(attacker, attacker_authid, sizeof(attacker_authid)))
		{
			strcopy(attacker_authid, sizeof(attacker_authid), "UNKNOWN");
		}
		decl String:victim_authid[32];
		if (!GetClientAuthString(victim, victim_authid, sizeof(victim_authid)))
		{
			strcopy(victim_authid, sizeof(victim_authid), "UNKNOWN");
		}
		
		decl Float:attacker_origin[3];
		GetClientAbsOrigin(attacker, attacker_origin);
		decl Float:victim_origin[3];
		GetClientAbsOrigin(victim, victim_origin);
		
		LogToGame("[KTRAJ] \"%N<%d><%s><%s>\" killed \"%N<%d><%s><%s>\" with \"%s\" (attacker_position \"%d %d %d\") (victim_position \"%d %d %d\")", attacker, GetClientUserId(attacker), attacker_authid, g_team_list[GetClientTeam(attacker)], victim, GetClientUserId(victim), victim_authid, g_team_list[GetClientTeam(victim)], weapon, RoundFloat(attacker_origin[0]), RoundFloat(attacker_origin[1]), RoundFloat(attacker_origin[2]), RoundFloat(victim_origin[0]), RoundFloat(victim_origin[1]), RoundFloat(victim_origin[2]));
	}
}

// Verb should always be "triggered" for this.
stock LogTeamEvent(team, const String:verb[], const String:event[], const String:properties[] = "")
{
	if (team > -1)
	{
		LogToGame("Team \"%s\" %s \"%s\"%s", g_team_list[team], verb, event, properties);
	}
}

stock LogKillLoc(attacker, victim)
{
	if (attacker > 0 && victim > 0)
	{
		decl Float:attacker_origin[3];
		GetClientAbsOrigin(attacker, attacker_origin);
		decl Float:victim_origin[3];
		GetClientAbsOrigin(victim, victim_origin);
				
		LogToGame("World triggered \"killlocation\" (attacker_position \"%d %d %d\") (victim_position \"%d %d %d\")", RoundFloat(attacker_origin[0]), RoundFloat(attacker_origin[1]), RoundFloat(attacker_origin[2]), RoundFloat(victim_origin[0]), RoundFloat(victim_origin[1]), RoundFloat(victim_origin[2]));
	}
}

stock LogRoleChange(client, const String:role[], const String:properties[] = "")
{
	LogPlayerEvent( client, "changed role to", role, false, properties );
}

stock LogMapLoad()
{
	decl String:map[64];
	GetCurrentMap(map, sizeof(map));
	LogToGame("Loading map \"%s\"", map);
}

stock IsValidPlayer(client)
{
	if (client > 0 && client <= MaxClients && IsClientInGame(client))
	{
		return true;
	}
	return false;
}