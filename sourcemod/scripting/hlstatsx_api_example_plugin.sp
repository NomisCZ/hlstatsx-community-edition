#pragma semicolon 1
#pragma newdecls required

#include <sourcemod>

#undef REQUIRE_PLUGIN
#undef REQUIRE_EXTENSIONS
#include <hlstatsx_api>
#define REQUIRE_EXTENSIONS
#define REQUIRE_PLUGIN

#define PLUGIN_VERSION "1.0.0"

bool g_bHLStatsApi;

public Plugin myinfo = 
{
	name = "HLStatsX API Test Plugin",
	author = "NomisCZ (-N-)",
	description = "HLStatsX API test plugin",
	version = PLUGIN_VERSION,
	url = "https://github.com/NomisCZ/hlstatsx-community-edition"
};

public void OnPluginStart()
{
	RegConsoleCmd("sm_stats_test", Command_ApiGetTest);
}

public void OnAllPluginsLoaded()
{	
	g_bHLStatsApi = LibraryExists("hlstatsx_api");
}

public void OnLibraryAdded(const char[] name)
{
	if (StrEqual(name, "hlstatsx_api")) {
		g_bHLStatsApi = true;
	}
}

public void OnLibraryRemoved(const char[] name)
{
	if (StrEqual(name, "hlstatsx_api")) {
		g_bHLStatsApi = false;
	}
}

public Action Command_ApiGetTest(int client, int args)
{
	if (!g_bHLStatsApi) {

		PrintToConsole(client, "HLStatsX API plugin is not loaded!");
		return;
	}

	bool request = HLStatsX_Api_GetStats("playerinfo", client, _GetTest_Response, 0);

	if (request) {
		PrintToConsole(client, "[DONE] API request: GetStats > %N", client);
	} else {
		PrintToConsole(client, "[FAILED] API request: GetStats > %N", client);
	}
}

public void _GetTest_Response(int command, int payload, int client, DataPack &datapack)
{
	if (IsValidClient(client)) {

		// https://sm.alliedmods.net/new-api/datapack/DataPack
		DataPack pack = view_as<DataPack>(CloneHandle(datapack));

		// You can also set position eg. to 5 and get values from that position - kd and others
		// pack.Position = view_as<DataPackPos>(5);

		int rank = pack.ReadCell();
		int skill = pack.ReadCell();
		int kills = pack.ReadCell();
		int deaths = pack.ReadCell();
		float kd = pack.ReadFloat();
		
		delete datapack;
		delete pack;

		PrintToConsole(client, "[DONE] API response: Stats > %N (%i): %i, %i, %i, %i, %f", client, client, rank, skill, kills, deaths, kd);
	}
}