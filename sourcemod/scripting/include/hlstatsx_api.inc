#if defined _hlstatsx_api_included
  #endinput
#endif
#define _hlstatsx_api_included

#define MAX_INT_VALUE 					65535
#define HLX_CALLBACK_TYPE_PLAYER_INFO	101

public SharedPlugin __pl_hlstatsx_api = 
{
	name = "hlstatsx_api",
	file = "hlstatsx_api.smx",
	#if defined REQUIRE_PLUGIN
	required = 1,
	#else
	required = 0,
	#endif
}

public void __pl_hlstatsx_api_SetNTVOptional()
{
	MarkNativeAsOptional("HLStatsX_Api_GetStats");
}

typedef HLStatsX_Api_StatsCallback = function void(int command, int payload, int client, DataPack &dataPack);

/**
 * Query HLStatsX Stats data from a client
 * 
 * @param request		Request type (only 'playerinfo' available)
 * @param client		Client index.
 * @param func			Callback function.
 * @param payload		Payload to distinct queries.
 * @noreturn
 */
native bool HLStatsX_Api_GetStats(char[] request, int client, HLStatsX_Api_StatsCallback callback, int payload = 0);

/**
* Check if for a valid client
*
*
* @param client				Client Index
* @param allowDead			Allow Dead players?
* @param allowBots			Allow Bots?
* @noreturn
*/
stock bool IsValidClient(int client, bool allowDead = true, bool allowBots = false)
{
	if (!(1 <= client <= MaxClients) || !IsClientInGame(client) || (IsFakeClient(client) && !allowBots) || IsClientSourceTV(client) || IsClientReplay(client) || (!allowDead && !IsPlayerAlive(client))) {
		return false;
	}
	return true;
}