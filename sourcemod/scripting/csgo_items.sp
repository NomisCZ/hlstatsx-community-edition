


public Plugin:myinfo = 
{
	name = "CSGO items",
	author = "Bacardi",
	description = "Collection of items from \"scripts/items/items_game.txt\" ",
	version = "28.06.2014",
	url = "http://www.sourcemod.net/"
};

new Handle:items_game = INVALID_HANDLE;

public OnPluginStart()
{
	RegServerCmd("sm_csgo_items_game_show_items", test, "Show list items created by plugin in server console");
}

public APLRes:AskPluginLoad2(Handle:myself, bool:late, String:error[], err_max)
{
	CreateKV();
	CreateNative("CSGO_GetItemDefinitionNameByIndex", __Native_CSGO_GetItemDefinitionNameByIndex);
	CreateNative("CSGO_GetItemDefinitionIndexByName", __Native_CSGO_GetItemDefinitionIndexByName);
	return APLRes_Success;
}

public __Native_CSGO_GetItemDefinitionNameByIndex(Handle:plugin, numParams)
{
	if(items_game == INVALID_HANDLE)
	{
		ThrowNativeError(SP_ERROR_NATIVE, "CSGO_GetItemDefinitionNameByIndex KeyValue:items_game is INVALID_HANDLE");
		return;
	}

	if( KvJumpToKey(items_game, "items", false) && KvGotoFirstSubKey(items_game, true) )
	{
		new index = GetNativeCell(1);
		new String:section[20], String:value[100];

		do
		{
			KvGetSectionName(items_game, section, sizeof(section));
			KvGetString(items_game, "name", value, sizeof(value), "default");

			if( StringToInt(section) == index )
			{
				SetNativeString(2, value, GetNativeCell(3));
				break;
			}
		}
		while( KvGotoNextKey(items_game, true) )
		{
			while( KvGoBack(items_game) )
			{
			}
		}
	}
}


public __Native_CSGO_GetItemDefinitionIndexByName(Handle:plugin, numParams)
{
	if(items_game == INVALID_HANDLE)
	{
		ThrowNativeError(SP_ERROR_NATIVE, "CSGO_GetItemDefinitionIndexByName KeyValue:items_game is INVALID_HANDLE");
		return -1;
	}

	new index;

	if( KvJumpToKey(items_game, "items", false) && KvGotoFirstSubKey(items_game, true) )
	{
		new len;
		GetNativeStringLength(1, len);

		new String:buffer[len + 1];
		GetNativeString(1, buffer, len + 1);

		new String:section[20], String:value[100];

		do
		{
			KvGetSectionName(items_game, section, sizeof(section));
			KvGetString(items_game, "name", value, sizeof(value), "default");

			if( StrEqual(buffer, value) )
			{
				index = StringToInt(section);
				break;
			}
		}
		while( KvGotoNextKey(items_game, true) )
		{
			while( KvGoBack(items_game) )
			{
			}
		}
	}
	return index;
}

public Action:test(args)
{
	if( KvJumpToKey(items_game, "items", false) && KvGotoFirstSubKey(items_game, true) )
	{
		new String:section[20], String:value[100];

		do
		{
			KvGetSectionName(items_game, section, sizeof(section));
			KvGetString(items_game, "name", value, sizeof(value), "default");

			PrintToServer("Section %s, name %s", section, value);
		}
		while( KvGotoNextKey(items_game, true) )
		{
			while( KvGoBack(items_game) )
			{
			}
		}
	}
	return Plugin_Handled;
}

CreateKV()
{
	if( !FileExists("scripts/items/items_game.txt", true) )
	{
		SetFailState("!FileExists(\"scripts/items/items_game.txt\", true)");
	}

	new Handle:kv = CreateKeyValues("items_game");

	if( FileToKeyValues(kv, "scripts/items/items_game.txt") )
	{
		if( KvJumpToKey(kv, "items", false) && KvGotoFirstSubKey(kv, true) )
		{
			new String:section[20], String:value[100];

			items_game = CreateKeyValues("items_game");
			
			do
			{
				KvGetSectionName(kv, section, sizeof(section));
				KvGetString(kv, "name", value, sizeof(value), "default");

				KvJumpToKey(items_game, "items", true);
				KvJumpToKey(items_game, section, true);
				KvSetString(items_game, "name", value);
				KvRewind(items_game);
			}
			while( KvGotoNextKey(kv, true) )
			{
				CloseHandle(kv);
				//KeyValuesToFile(items_game, "output.kv");
			}
		}
	}
	else
	{
		CloseHandle(kv);
		SetFailState("FileToKeyValues(kv, \"scripts/items/items_game.txt\")");
	}
}