/*
Allows players to use !mm command which adds their Competitive rank to HLstatsX.
Add your HLstatsX MySQL information to sourcemod/configs/databases.cfg

	"elorank"
	{
		"driver"                        "mysql"
		"host"                          "127.0.0.1"
		"database"                      "hlstatsx"
		"user"                          "hlxuser"
		"pass"                          "password"
	}
	
*/
#pragma semicolon 1
#pragma newdecls required

#include <sourcemod>

int iRank[MAXPLAYERS+1] = {0,...};
Database hDatabase = null;

public Plugin myinfo = 
{
	name = "[CS:GO] Elorank for HLstatsX",
	author = "Laam4",
	description = "Command (!mm) for players to set their Competitive rank to HLstatsX",
	version = "1.0.1",
	url = "https://github.com/laam4/noxus"
};

public void OnPluginStart()
{
	RegConsoleCmd("sm_mm", Command_EloMenu);
	StartSQL();
}

void StartSQL()
{
	Database.Connect(GotDatabase, "elorank");
}

public void GotDatabase(Database db, const char[] error, any data)
{
	if (db == null)
	{
		LogError("Database failure: %s", error);
	} 
	else 
	{
		hDatabase = db;
	}
}

public void T_SetRank(Handle owner, Handle db, const char[] error, any data)
{ 
	if (db == INVALID_HANDLE)
	{ 
		LogError("Query failed! %s", error); 
	} 
	return; 
}

void setRank(int rank, const char[] auth)
{
	char buffer[3][32];
	char set_rank[255];
	//PrintToServer("uniqueid: %s:%s", buffer[1], buffer[2]);
	ExplodeString(auth, ":", buffer, 3, 32);
	Format(set_rank, sizeof(set_rank), "UPDATE hlstats_PlayerUniqueIds LEFT JOIN hlstats_Players ON hlstats_Players.playerId = hlstats_PlayerUniqueIds.playerId SET hlstats_Players.mmrank='%d' WHERE uniqueId='%s:%s'", rank, buffer[1], buffer[2]);
	SQL_TQuery(hDatabase, T_SetRank, set_rank);
}

public Action Command_EloMenu(int client, int args)
{
	if (IsClientInGame(client))
	{
		Menu elo = CreateMenu(EloHandler);
		elo.SetTitle("Your competitive rank?");
		elo.AddItem("0", "No Rank");
		elo.AddItem("1", "Silver I");
		elo.AddItem("2", "Silver II");
		elo.AddItem("3", "Silver III");
		elo.AddItem("4", "Silver IV");
		elo.AddItem("5", "Silver Elite");
		elo.AddItem("6", "Silver Elite Master");
		elo.AddItem("7", "Gold Nova I");
		elo.AddItem("8", "Gold Nova II");
		elo.AddItem("9", "Gold Nova III");
		elo.AddItem("10", "Gold Nova Master");
		elo.AddItem("11", "Master Guardian I");
		elo.AddItem("12", "Master Guardian II");
		elo.AddItem("13", "Master Guardian Elite");
		elo.AddItem("14", "Distinguished Master Guardian");
		elo.AddItem("15", "Legendary Eagle");
		elo.AddItem("16", "Legendary Eagle Master");
		elo.AddItem("17", "Supreme Master First Class");
		elo.AddItem("18", "The Global Elite");
		SetMenuPagination(elo, 8);
		elo.Display(client, 30);
	}
	return Plugin_Handled;
}

public int EloHandler(Handle elo, MenuAction action, int client, int itemNum)
{
	switch(action)
	{
	case MenuAction_Select:
		{
			
			char info[4];
			char auth[64];
			char text[20];
			
			GetMenuItem(elo, itemNum, info, sizeof(info));
			iRank[client] = StringToInt(info);
			GetClientAuthId(client, AuthId_Steam2, auth, sizeof(auth));
			setRank(iRank[client], auth);
			Format(text, sizeof(text), "Your rank is now ");
			switch(iRank[client])
			{
			case 0: PrintToChat(client, "%s\x08No Rank", text);
			case 1: PrintToChat(client, "%s\x0ASilver I", text);
			case 2: PrintToChat(client, "%s\x0ASilver II", text);
			case 3: PrintToChat(client, "%s\x0ASilver III", text);
			case 4: PrintToChat(client, "%s\x0ASilver IV", text);
			case 5: PrintToChat(client, "%s\x0ASilver Elite", text);
			case 6: PrintToChat(client, "%s\x0ASilver Elite Master", text);
			case 7: PrintToChat(client, "%s\x0BGold Nova I", text);
			case 8: PrintToChat(client, "%s\x0BGold Nova II", text);
			case 9: PrintToChat(client, "%s\x0BGold Nova III", text);
			case 10: PrintToChat(client, "%s\x0BGold Nova Master", text);
			case 11: PrintToChat(client, "%s\x0CMaster Guardian I", text);
			case 12: PrintToChat(client, "%s\x0CMaster Guardian II", text);
			case 13: PrintToChat(client, "%s\x0CMaster Guardian Elite", text);
			case 14: PrintToChat(client, "%s\x0CDistinguished Master Guardian", text);
			case 15: PrintToChat(client, "%s\x0ELegendary Eagle", text);
			case 16: PrintToChat(client, "%s\x0ELegandary Eagle Master", text);
			case 17: PrintToChat(client, "%s\x0ESupreme Master First Class", text);
			case 18: PrintToChat(client, "%s\x0FThe Global Elite", text);
			}
		}
	case MenuAction_End:
		{
			delete elo;
		}
	}
}