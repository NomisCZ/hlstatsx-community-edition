#pragma semicolon 1
#pragma newdecls required

#include <sourcemod>
#include <sdktools>

#undef REQUIRE_PLUGIN
#undef REQUIRE_EXTENSIONS
#include <hlstatsx_api>
#define REQUIRE_EXTENSIONS
#define REQUIRE_PLUGIN

#define PLUGIN_VERSION "1.7.0"

int g_iRequestId = 0;
ArrayList g_hRequestCallbacks;

enum Api_CallbackData {
	Id,
	Float:Time,
	Client,
	Handle:Plugin,
	Function:Function,
	Payload,
	Limit
};

public Plugin myinfo = 
{
	name = "HLStatsX API",
	author = "NomisCZ (-N-)",
	description = "HLStatsX API module",
	version = PLUGIN_VERSION,
	url = "https://github.com/NomisCZ/hlstatsx-community-edition"
};

public void OnPluginStart()
{
    RegServerCmd("hlx_api_response", _Api_Response);

    g_hRequestCallbacks = new ArrayList(7);
}

public APLRes AskPluginLoad2(Handle myself, bool late, char[] error, int err_max)
{
    RegPluginLibrary("hlstatsx_api");
    
    CreateNative("HLStatsX_Api_GetStats", Api_GetStats);

    return APLRes_Success;
}

public void OnPluginEnd()
{
    if (g_hRequestCallbacks != null && g_hRequestCallbacks != INVALID_HANDLE) {
        delete g_hRequestCallbacks;
    }
}

public int Api_GetStats(Handle plugin, int numParams)
{
    if (numParams < 4) {
        return false;
    }

    char callbackType[255];
    GetNativeString(1, callbackType, sizeof(callbackType));

    int callbackClient = GetNativeCell(2);
    Function callbackFunction = GetNativeCell(3);
    int callbackPayload = GetNativeCell(4);
    int callbackLimit = 1;

    if (numParams >= 5) {
        callbackLimit = GetNativeCell(5);
    }

    if (!IsValidClient(callbackClient)) {
        return false;
    }

    int requestId = GetRequestId();
    int data[Api_CallbackData];

    data[Id] = requestId;
    data[Time] = GetGameTime();
    data[Client] = callbackClient;
    data[Plugin] = plugin;
    data[Function] = callbackFunction;
    data[Payload] = callbackPayload;
    data[Limit] = callbackLimit;

    if (g_hRequestCallbacks != null && g_hRequestCallbacks != INVALID_HANDLE) {

        g_hRequestCallbacks.PushArray(data[0]);

        char queryPayload[32];
        IntToString(requestId, queryPayload, sizeof(queryPayload));

        Api_SendRequest(callbackClient, "api_request", callbackType, queryPayload);

        return true;
    }

    return false;
}

public void Api_SendRequest(int client, char[] verb, const char[] event, const char[] properties)
{
	if (IsValidClient(client)) {

		char authId[32];
		char teamName[32];

		GetClientAuthId(client, AuthId_Steam2, authId, sizeof(authId));
		GetTeamName(GetClientTeam(client), teamName, sizeof(teamName));

		LogToGame("\"%N<%d><%s><%s>\" %s \"%s\" (value \"%s\")", client, GetClientUserId(client), authId, teamName, verb, event, properties); 
	}
}

public Action _Api_Response(int args)
{
    if (args < 1) {
        return Plugin_Handled;
    }

    int type = GetArgumentParam(1, args);

    switch (type) {

        case HLX_CALLBACK_TYPE_PLAYER_INFO: {

            if (args < 14) {
                return Plugin_Handled;
            }

            int requestId = GetArgumentParam(2, args);
            int requestCallbackIndex = FindRequestById(requestId);

            int userId = GetArgumentParam(3, args);
            int client = GetClientOfUserId(userId);

            if (requestCallbackIndex < 0 || !IsValidClient(client)) {
                return Plugin_Handled;
            }

            DataPack pack = new DataPack();
            Action result;
            char kdParam[16], hpkParam[16], accParam[16], countryCode[4];

            pack.WriteCell(GetArgumentParam(4, args)); // Player rank
            pack.WriteCell(GetArgumentParam(5, args)); // Skill
            pack.WriteCell(GetArgumentParam(6, args)); // Kills
            pack.WriteCell(GetArgumentParam(7, args)); // Deaths

            // Kd
            GetCmdArg(8, kdParam, sizeof(kdParam));
            pack.WriteFloat(StringToFloat(kdParam));

            pack.WriteCell(GetArgumentParam(9, args)); // Suicides
            pack.WriteCell(GetArgumentParam(10, args)); // Headshots

            // Hpk
            GetCmdArg(11, hpkParam, sizeof(hpkParam));
            pack.WriteFloat(StringToFloat(hpkParam));

            // Accuracy
            GetCmdArg(12, accParam, sizeof(accParam));
            pack.WriteFloat(StringToFloat(accParam));

            // Connection time
            pack.WriteCell(GetArgumentParam(13, args));									

            // Country code
            GetCmdArg(14, countryCode, sizeof(countryCode));
            pack.WriteString(countryCode);

            // Done, so reset position to start (for other plugins)
            pack.Reset();

            int data[Api_CallbackData];
            g_hRequestCallbacks.GetArray(requestCallbackIndex, data[0], sizeof(data));

            if ((data[Plugin] != INVALID_HANDLE) && (data[Function] != INVALID_FUNCTION)) {

                Call_StartFunction(data[Plugin], data[Function]);
                Call_PushCell(HLX_CALLBACK_TYPE_PLAYER_INFO);

                Call_PushCell(data[Payload]);
                Call_PushCell(client);

                Call_PushCellRef(pack);
                Call_Finish(view_as<int>(result));

                if (data[Limit] == 1) {
                    g_hRequestCallbacks.Erase(requestCallbackIndex);
                }
            }

            delete pack;
        }
    }

    return Plugin_Handled;
}

public int GetArgumentParam(int index, int argsCount)
{
	char param[128];

	if (index <= argsCount) {

		GetCmdArg(index, param, sizeof(param));
		return StringToInt(param);
	}

	return -1;
}

public int GetRequestId()
{
	g_iRequestId++;

	if (g_iRequestId > MAX_INT_VALUE) {
		g_iRequestId = 1;
	}

	return g_iRequestId;
}

public int FindRequestById(int requestId)
{
    int index = -1;

    if (requestId <= 0) {
        return index;
    }

    int size = g_hRequestCallbacks.Length;

    for (int i = 0; i < size; i++) {

        int data[Api_CallbackData];
        g_hRequestCallbacks.GetArray(i, data[0], sizeof(data));

        if (data[Id] == requestId && (data[Plugin] != INVALID_HANDLE) && (data[Function] != INVALID_FUNCTION)) {

            index = i;
            break;
        }
    }

    return index;
}

/**
* Check if for a valid client
*
*
* @param client				Client Index
* @param allowDead			Allow Dead players?
* @param allowBots			Allow Bots?
* @noreturn
*/
bool IsValidClient(int client, bool allowDead = true, bool allowBots = false)
{
	if (!(1 <= client <= MaxClients) || !IsClientInGame(client) || (IsFakeClient(client) && !allowBots) || IsClientSourceTV(client) || IsClientReplay(client) || (!allowDead && !IsPlayerAlive(client))) {
		return false;
	}
	return true;
}