#include <sourcemod>
#include <loghelper>

#define VERSION "2.2"

public Plugin:myinfo =
{
        name = "SuperLogs: CSpromod",
        author = "NeoCortex, psychonic",
        description = "Rewrites the logs from CSProMod so HLStatsX:CE will understand them",
        version = VERSION,
        url = "http://www.sourcemod.net/"
};
 
public OnPluginStart()
{
	CreateConVar("superlogs_cspromod_version", VERSION, "SuperLogs: CSpromod", FCVAR_NOTIFY);
	
	HookEvent("player_death", Event_PlayerDeath)
	HookEvent("player_connect", Event_PlayerConnect)
	HookEvent("player_disconnect", Event_PlayerDisconnect)
	HookEvent("player_team", Event_PlayerTeam)

	HookEvent("round_start", Event_RoundStart)
	HookEvent("round_end", Event_RoundEnd)

	CreateTimer(1.0, LogMap);
}

public Action:LogMap(Handle:timer)
{
	// Taken from superlogs-generic.sp by psychonic
	// Called 1 second after OnPluginStart since srcds does not log the first map loaded. Idea from Stormtrooper's "mapfix.sp" for psychostats
	LogMapLoad();
}

public OnMapStart()
{
        // For loghelper
        GetTeams();
}

public Event_PlayerConnect(Handle:event, const String:name[], bool:dontBroadcast)
{
	decl String:cname[MAX_NAME_LENGTH];
	GetEventString(event, "name", cname, sizeof(cname));
	decl String:steamid[24];
	GetEventString(event, "networkid", steamid, sizeof(steamid));
	decl String:ip[32];
	GetEventString(event, "address", ip, sizeof(ip));
		
	LogToGame("\"%s<%d><%s><>\" connected, address \"%s\"", cname, GetEventInt(event, "userid"), steamid, ip);
}

public Event_PlayerDisconnect(Handle:event, const String:name[], bool:dontBroadcast)
{
	new client = GetClientOfUserId(GetEventInt(event, "userid"));
	decl String:cname[MAX_NAME_LENGTH];
	GetClientName(client, cname, sizeof(cname));
	decl String:cauth[32];
	GetClientAuthString(client, cauth, 32);
	decl String:creason[128];
	GetEventString(event, "reason", creason, sizeof(creason));

	LogToGame("\"%s<%d><%s><>\" disconnected (reason \"%s\")", cname, client, cauth, creason);
}

public Event_PlayerDeath(Handle:event, const String:name[], bool:dontBroadcast)
{
	// Version 2 of this subroutine (using loghelper) is written by psychonic
	new victim = GetClientOfUserId(GetEventInt(event, "userid"));
	new attacker = GetClientOfUserId(GetEventInt(event, "attacker"));
	decl String:AWeapon[64];
	GetEventString(event, "weapon", AWeapon, sizeof(AWeapon));

	decl String:properties[12] = "";
	if (GetEventBool(event, "headshot"))
	{
		strcopy(properties, sizeof(properties), " (headshot)");
	}

	// Player_Suicide
	if (attacker == victim)
	{
		LogSuicide(victim, AWeapon, true, properties);
		return;
	}

	LogKill(attacker, victim, AWeapon, true, properties);
}

public Event_RoundStart(Handle:event, const String:name[], bool:dontBroadcast)
{
	LogToGame("World triggered \"Round_Start\"");
}

public Event_RoundEnd(Handle:event, const String:name[], bool:dontBroadcast)
{
	new winner = GetEventInt(event, "winner");
	if (winner == 2 || winner == 3)
	{
		LogTeamEvent(winner, "triggered", "Round_Win");
	}
	
	LogToGame("World triggered \"Round_End\"");
}

public Event_PlayerTeam(Handle:event, const String:name[], bool:dontBroadcast)
{
	new client = GetClientOfUserId(GetEventInt(event, "userid"));
	new NTeam = GetEventInt(event, "team");
	decl String:STeam[32];
	GetTeamName(NTeam, STeam, sizeof(STeam));

	LogPlayerEvent(client, "joined team", STeam, true)
}
