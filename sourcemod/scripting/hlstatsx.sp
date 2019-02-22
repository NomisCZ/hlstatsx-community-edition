/**
* HLstatsX Community Edition - SourceMod plugin to display ingame messages
* http://www.hlxcommunity.com/
* Copyright (C) 2008-2009 Nicholas Hastings
* Copyright (C) 2007-2009 TTS Oetzel & Goerz GmbH
* Modified by Nicholas Hastings (psychonic) for use with HLstatsX Community Edition
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

#define REQUIRE_EXTENSIONS 
#include <sourcemod>
#include <sdktools>
#include <cstrike>
#include <clientprefs>

#define VERSION "1.6.19"
#define HLXTAG "HLstatsX:CE"

new Handle: hlx_block_chat_commands;
new Handle: hlx_message_prefix;
new Handle: hlx_protect_address;
new Handle: hlx_server_tag;
new Handle: sv_tags;
new Handle: message_recipients;
new const String: blocked_commands[][] = { "rank", "skill", "points", "place", "session", "session_data", 
	"kpd", "kdratio", "kdeath", "next", "load", "status", "servers", 
	"top20", "top10", "top5", "clans", "bans", "cheaters", "statsme", "weapons", 
	"weapon", "action", "actions", "accuracy", "targets", "target", "kills", 
	"kill", "player_kills", "cmd", "cmds", "command", "hlx_display 0", 
	"hlx_display 1", "hlx_teams 0", "hlx_teams 1", "hlx_hideranking", 
	"hlx_chat 0", "hlx_chat 1", "hlx_menu", "servers 1", "servers 2", 
	"servers 3", "hlx", "hlstatsx", "help" };
	
new Handle:HLstatsXMenuMain;
new Handle:HLstatsXMenuAuto;
new Handle:HLstatsXMenuEvents;

new Handle: PlayerColorArray;
new ColorSlotArray[] = { -1, -1, -1, -1, -1, -1 };

new String: message_prefix[32];
new bool:g_bPlyrCanDoMotd[MAXPLAYERS+1];
new bool:g_bGameCanDoMotd = true;
new bool:g_bTrackColors4Chat;
new bool:g_bLateLoad = false;
new bool:g_bIgnoreNextTagChange = false;
new Handle:g_hCustomTags;
new EngineVersion:CurrentVersion;

#define SVTAGSIZE 128

public Plugin:myinfo = {
	name = "[CSGO] HLstatsX plugin",
	author = "psychonic",
	description = "Provides ingame functionality for interaction from an HLstatsX CE installation",
	version = VERSION,
	url = "http://www.hlxcommunity.com"
};


public APLRes:AskPluginLoad2(Handle:myself, bool:late, String:error[], err_max)
{
	g_bLateLoad = late;	
	return APLRes_Success;
}


public OnPluginStart() 
{
	CurrentVersion = GetEngineVersion();
	if (CurrentVersion != Engine_CSGO)
	{
		SetFailState("Only CSGO");
	}

	CreateHLstatsXMenuMain(HLstatsXMenuMain);
	CreateHLstatsXMenuAuto(HLstatsXMenuAuto);
	CreateHLstatsXMenuEvents(HLstatsXMenuEvents);

	RegServerCmd("hlx_sm_psay",          hlx_sm_psay);
	RegServerCmd("hlx_sm_psay2",         hlx_sm_psay2);
	RegServerCmd("hlx_sm_bulkpsay",      hlx_sm_psay);
	RegServerCmd("hlx_sm_csay",          hlx_sm_csay);
	RegServerCmd("hlx_sm_msay",          hlx_sm_msay);
	RegServerCmd("hlx_sm_tsay",          hlx_sm_tsay);
	RegServerCmd("hlx_sm_hint",          hlx_sm_hint);
	RegServerCmd("hlx_sm_browse",        hlx_sm_browse);
	RegServerCmd("hlx_sm_swap",          hlx_sm_swap);
	RegServerCmd("hlx_sm_redirect",      hlx_sm_redirect);
	RegServerCmd("hlx_sm_player_action", hlx_sm_player_action);
	RegServerCmd("hlx_sm_team_action",   hlx_sm_team_action);
	RegServerCmd("hlx_sm_world_action",  hlx_sm_world_action);
	
	AddCommandListener(hlx_block_commands, "say");
	AddCommandListener(hlx_block_commands, "say_team");
	
	g_bTrackColors4Chat = true;
	HookEvent("player_team",  HLstatsX_Event_PlyTeamChange, EventHookMode_Pre);
	
	CreateConVar("hlxce_plugin_version", VERSION, "HLstatsX:CE Ingame Plugin", FCVAR_NOTIFY);
	CreateConVar("hlxce_version", "", "HLstatsX:CE", FCVAR_NOTIFY);
	CreateConVar("hlxce_webpage", "http://www.hlxcommunity.com", "http://www.hlxcommunity.com", FCVAR_NOTIFY);
	
	hlx_block_chat_commands = CreateConVar("hlx_block_commands", "1", "If activated HLstatsX commands are blocked from the chat area");
	hlx_message_prefix = CreateConVar("hlx_message_prefix", "", "Define the prefix displayed on every HLstatsX ingame message");
	hlx_protect_address = CreateConVar("hlx_protect_address", "", "Address to be protected for logging/forwarding");
	hlx_server_tag = CreateConVar("hlx_server_tag", "1", "If enabled, adds \"HLstatsX:CE\" to server tags on supported games. 1 = Enabled (default), 0 = Disabled", _, true, 0.0, true, 1.0);
	
	g_hCustomTags = CreateArray(SVTAGSIZE);
	sv_tags = FindConVar("sv_tags");
	
	if (g_bLateLoad)
	{
		GetConVarString(hlx_message_prefix, message_prefix, sizeof(message_prefix));
		decl String:protaddr[24];
		GetConVarString(hlx_protect_address, protaddr, sizeof(protaddr));
		OnProtectAddressChange(hlx_protect_address, "", protaddr);
	}
	
	MyAddServerTag(HLXTAG);
	
	HookConVarChange(hlx_message_prefix, OnMessagePrefixChange);
	HookConVarChange(hlx_protect_address, OnProtectAddressChange);
	HookConVarChange(hlx_server_tag, OnServerTagChange);
	if (sv_tags != INVALID_HANDLE)
	{
		HookConVarChange(sv_tags, OnSVTagsChange);
	}
	
	RegServerCmd("log", ProtectLoggingChange);
	RegServerCmd("logaddress_del", ProtectForwardingChange);
	RegServerCmd("logaddress_delall", ProtectForwardingDelallChange);
	RegServerCmd("hlx_message_prefix_clear", MessagePrefixClear);

	PlayerColorArray = CreateArray();
	message_recipients = CreateStack();
}


public OnAllPluginsLoaded()
{
	if (LibraryExists("clientprefs"))
	{
		SetCookieMenuItem(HLXSettingsMenu, 0, "HLstatsX:CE Settings");
	}
}

public HLXSettingsMenu(client, CookieMenuAction:action, any:info, String:buffer[], maxlen)
{
	if (action == CookieMenuAction_SelectOption)
	{
		DisplayMenu(HLstatsXMenuMain, client, MENU_TIME_FOREVER);
	}
}


public OnMapStart()
{
	if (g_bTrackColors4Chat)
	{
		find_player_team_slot(2);
		find_player_team_slot(3);
	}
}

stock MyAddServerTag(const String:tag[])
{
	if (!GetConVarBool(hlx_server_tag))
	{
		return;
	}
	
	if (FindStringInArray(g_hCustomTags, tag) == -1)
	{
		PushArrayString(g_hCustomTags, tag);
	}
	
	decl String:current_tags[SVTAGSIZE];
	GetConVarString(sv_tags, current_tags, sizeof(current_tags));
	if (StrContains(current_tags, tag) > -1)
	{
		// already have tag
		return;
	}
	
	decl String:new_tags[SVTAGSIZE];
	Format(new_tags, sizeof(new_tags), "%s%s%s", current_tags, (current_tags[0]!=0)?",":"", tag);
	
	new flags = GetConVarFlags(sv_tags);
	SetConVarFlags(sv_tags, flags & ~FCVAR_NOTIFY);
	g_bIgnoreNextTagChange = true;
	SetConVarString(sv_tags, new_tags);
	g_bIgnoreNextTagChange = false;
	SetConVarFlags(sv_tags, flags);
}

stock MyRemoveServerTag(const String:tag[])
{
	new idx = FindStringInArray(g_hCustomTags, tag);
	if (idx > -1)
	{
		RemoveFromArray(g_hCustomTags, idx);
	}
	
	decl String:current_tags[SVTAGSIZE];
	GetConVarString(sv_tags, current_tags, sizeof(current_tags));
	if (StrContains(current_tags, tag) == -1)
	{
		// tag isn't on here, just bug out
		return;
	}
	
	ReplaceString(current_tags, sizeof(current_tags), tag, "");
	ReplaceString(current_tags, sizeof(current_tags), ",,", "");
	
	new flags = GetConVarFlags(sv_tags);
	SetConVarFlags(sv_tags, flags & ~FCVAR_NOTIFY);
	g_bIgnoreNextTagChange = true;
	SetConVarString(sv_tags, current_tags);
	g_bIgnoreNextTagChange = false;
	SetConVarFlags(sv_tags, flags);
}

public OnClientPostAdminCheck(client)
{
	if (g_bGameCanDoMotd && !IsFakeClient(client))
	{
		QueryClientConVar(client, "cl_disablehtmlmotd", motdQuery);
	}
}


public motdQuery(QueryCookie:cookie, client, ConVarQueryResult:result, const String:cvarName[], const String:cvarValue[])
{
	if (result == ConVarQuery_Okay && StringToInt(cvarValue) == 0 || result != ConVarQuery_Okay)
	{
		g_bPlyrCanDoMotd[client] = true;
	}
}


public OnServerTagChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	if (GetConVarBool(hlx_server_tag))
	{
		MyAddServerTag(HLXTAG);
	}
	else
	{
		MyRemoveServerTag(HLXTAG);
	}
}

public OnSVTagsChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	if (g_bIgnoreNextTagChange)
	{
		// we fired this callback, no need to reapply tags
		return;
	}
	
	// reapply each custom tag
	new cnt = GetArraySize(g_hCustomTags);
	for (new i = 0; i < cnt; i++)
	{
		decl String:tag[SVTAGSIZE];
		GetArrayString(g_hCustomTags, i, tag, sizeof(tag));
		MyAddServerTag(tag);
	}
}


public OnProtectAddressChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	if (newVal[0] > 0)
	{
		decl String: log_command[192];
		Format(log_command, sizeof(log_command), "logaddress_add %s", newVal);
		LogToGame("Command: %s", log_command);
		ServerCommand(log_command);
	}
}

public Action:ProtectLoggingChange(args)
{
	if (hlx_protect_address != INVALID_HANDLE)
	{
		decl String: protect_address[192];
		GetConVarString(hlx_protect_address, protect_address, sizeof(protect_address));
		if (strcmp(protect_address, "") != 0)
		{
			if (args >= 1)
			{
				decl String: log_action[192];
				GetCmdArg(1, log_action, sizeof(log_action));
				if ((strcmp(log_action, "off") == 0) || (strcmp(log_action, "0") == 0))
				{
					LogToGame("HLstatsX address protection active, logging reenabled!");
					ServerCommand("log 1");
				}
			}
		}
	}
	return Plugin_Continue;
}

public Action:ProtectForwardingChange(args)
{
	if (hlx_protect_address != INVALID_HANDLE)
	{
		decl String: protect_address[192];
		GetConVarString(hlx_protect_address, protect_address, sizeof(protect_address));
		if (strcmp(protect_address, "") != 0)
		{
			if (args == 1)
			{
				decl String: log_action[192];
				GetCmdArg(1, log_action, sizeof(log_action));
				if (strcmp(log_action, protect_address) == 0)
				{
					decl String: log_command[192];
					Format(log_command, sizeof(log_command), "logaddress_add %s", protect_address);
					LogToGame("HLstatsX address protection active, logaddress readded!");
					ServerCommand(log_command);
				}
			}
			else if (args > 1)
			{
				new String: log_action[192];
				for(new i = 1; i <= args; i++)
				{
					decl String: temp_argument[192];
					GetCmdArg(i, temp_argument, sizeof(temp_argument));
					strcopy(log_action[strlen(log_action)], sizeof(log_action), temp_argument);
				}
				if (strcmp(log_action, protect_address) == 0)
				{
					decl String: log_command[192];
					Format(log_command, sizeof(log_command), "logaddress_add %s", protect_address);
					LogToGame("HLstatsX address protection active, logaddress readded!");
					ServerCommand(log_command);
				}
				
			}
		}
	}
	return Plugin_Continue;
}


public Action:ProtectForwardingDelallChange(args)
{
	if (hlx_protect_address != INVALID_HANDLE)
	{
		decl String: protect_address[192];
		GetConVarString(hlx_protect_address, protect_address, sizeof(protect_address));
		if (strcmp(protect_address, "") != 0)
		{
			decl String: log_command[192];
			Format(log_command, sizeof(log_command), "logaddress_add %s", protect_address);
			LogToGame("HLstatsX address protection active, logaddress readded!");
			ServerCommand(log_command);
		}
	}
	return Plugin_Continue;
}


public OnMessagePrefixChange(Handle:cvar, const String:oldVal[], const String:newVal[])
{
	strcopy(message_prefix, sizeof(message_prefix), newVal);
}


public Action:MessagePrefixClear(args)
{
	message_prefix = "";
}


find_player_team_slot(team_index) 
{
	if (team_index > -1)
	{
		ColorSlotArray[team_index] = -1;
		for(new i = 1; i <= MaxClients; i++)
		{
			if (IsClientInGame(i) && GetClientTeam(i) == team_index)
			{
				ColorSlotArray[team_index] = i;
				break;
			}
		}
	}
}


stock validate_team_colors() 
{
	for (new i = 0; i < sizeof(ColorSlotArray); i++)
	{
		new color_client = ColorSlotArray[i];
		if (color_client > 0)
		{
			if (IsClientInGame(color_client) && GetClientTeam(color_client) != color_client)
			{
				find_player_team_slot(i);
			}
		}
		else
		{
			if (i == 2 || i == 3 )
			{
				find_player_team_slot(i);
			}
		}
	}
}

public OnClientDisconnect(client)
{
	if (g_bTrackColors4Chat && client > 0 && IsClientInGame(client))
	{
		new team_index = GetClientTeam(client);
		if (client == ColorSlotArray[team_index])
		{
			ColorSlotArray[team_index] = -1;
		}
	}
	
	g_bPlyrCanDoMotd[client] = false;
}

color_player(color_type, player_index, String: client_message[192]) 
{
	new color_player_index = -1;
	if (g_bTrackColors4Chat)
	{
		decl String: client_name[192];
		GetClientName(player_index, client_name, sizeof(client_name));
		if ((strcmp(client_message, "") != 0) && (strcmp(client_name, "") != 0))
		{
			if (color_type == 1)
			{
				decl String: search_client_name[192];
				Format(search_client_name, sizeof(search_client_name), "%s ", client_name);
				decl String: colored_player_name[192];
				Format(colored_player_name, sizeof(colored_player_name), "\x04%s\x01 ", client_name);

				if (ReplaceString(client_message, sizeof(client_message), search_client_name, colored_player_name) > 0)
				{
					return player_index;
				}
			}
			else
			{
				decl String: search_client_name[192];
				Format(search_client_name, sizeof(search_client_name), " %s ", client_name);
				decl String: colored_player_name[192];
				Format(colored_player_name, sizeof(colored_player_name), " \x01\x0B\x04%s\x01 ", client_name);
				ReplaceString(client_message, sizeof(client_message), search_client_name, colored_player_name);
			}
		}
	}
	return color_player_index;
}


color_all_players(String: message[192]) 
{
	new color_index = -1;
	if (g_bTrackColors4Chat && (PlayerColorArray != INVALID_HANDLE))
	{
		if (strcmp(message, "") != 0)
		{
			ClearArray(PlayerColorArray);

			new lowest_matching_pos = 192;
			new lowest_matching_pos_client = -1;

			for(new i = 1; i <= MaxClients; i++)
			{
				new client = i;
				if (IsClientInGame(client))
				{
					decl String: client_name[32];
					GetClientName(client, client_name, sizeof(client_name));

					if (strcmp(client_name, "") != 0)
					{
						new message_pos = StrContains(message, client_name);
						if (message_pos > -1)
						{
							if (lowest_matching_pos > message_pos)
							{
								lowest_matching_pos = message_pos;
								lowest_matching_pos_client = client;
							}
							new TempPlayerColorArray[1];
							TempPlayerColorArray[0] = client;
							PushArrayArray(PlayerColorArray, TempPlayerColorArray);
						}
					}
				}
			}
			new size = GetArraySize(PlayerColorArray);
			for (new i = 0; i < size; i++)
			{
				new temp_player_array[1];
				GetArrayArray(PlayerColorArray, i, temp_player_array);
				new temp_client = temp_player_array[0];
				if (temp_client == lowest_matching_pos_client)
				{
					new temp_color_index = color_player(1, temp_client, message);
					color_index = temp_color_index;
				}
				else
				{
					color_player(0, temp_client, message);
				}
			}
			ClearArray(PlayerColorArray);
		}
	}
	return color_index;
}



color_team_entities(String:message[192])
{
	if (strcmp(message, "") != 0)
	{
		if (ColorSlotArray[2] > -1)
		{
			if (ReplaceString(message, sizeof(message), "TERRORIST ", "\x03TERRORIST\x01 ") > 0)
			{
				return ColorSlotArray[2];
			}
		}
		if (ColorSlotArray[3] > -1)
		{
			if (ReplaceString(message, sizeof(message), "CT ", "\x03CT\x01 ") > 0)
			{
				return ColorSlotArray[3];
			}
		}
	}
	return -1;
}


display_menu(player_index, time, String: full_message[1024], need_handler = 0)
{
	ReplaceString(full_message, sizeof(full_message), "\\n", "\10");
	if (need_handler == 0)
	{
		InternalShowMenu(player_index, full_message, time);
	}
	else
	{
		InternalShowMenu(player_index, full_message, time, (1<<0)|(1<<1)|(1<<2)|(1<<3)|(1<<4)|(1<<5)|(1<<6)|(1<<7)|(1<<8)|(1<<9), InternalMenuHandler);
	}
}


public InternalMenuHandler(Handle:menu, MenuAction:action, param1, param2)
{
	new client = param1;
	if (IsClientInGame(client))
	{
		if (action == MenuAction_Select)
		{
			decl String: player_event[192];
			IntToString(param2, player_event, sizeof(player_event));
			LogPlayerEvent(client, "selected", player_event);
		}
		else if (action == MenuAction_Cancel)
		{
			LogPlayerEvent(client, "selected", "cancel");
		}
	}
}


public Action:hlx_sm_psay(args)
{
	if (args < 2)
	{
		PrintToServer("Usage: hlx_sm_psay <userid><colored><message> - sends private message");
		return Plugin_Handled;
	}

	decl String: client_list[192];
	GetCmdArg(1, client_list, sizeof(client_list));
	BuildClientList(client_list);

	decl String: colored_param[32];
	GetCmdArg(2, colored_param, sizeof(colored_param));
	new is_colored = 1;//must be 1 if you want names highlighted.
	new ignore_param = 0;
	
	if (strcmp(colored_param, "1") == 0)
	{
		is_colored = 1;
		ignore_param = 1;
	}
	else if (strcmp(colored_param, "2") == 0)
	{
		is_colored = 2;
		ignore_param = 1;
	}
	else if (strcmp(colored_param, "0") == 0)
	{
		ignore_param = 1;
	}

	new String: client_message[192];
	GetCmdArg((ignore_param + 2), client_message, sizeof(client_message));
	
	if (IsStackEmpty(message_recipients))
	{
		return Plugin_Handled;
	}
	
	new color_index = -1;
	decl String: display_message[192];
	if (is_colored > 0)
	{
		if (is_colored == 1)
		{
			new player_color_index = color_all_players(client_message);
			if (player_color_index > -1)
			{
				color_index = player_color_index;
			}
			else
			{
				if (g_bTrackColors4Chat)
				{
					validate_team_colors();
				}
				color_index = color_team_entities(client_message);
			}
		}
	}		
	if (strcmp(message_prefix, "") == 0)
	{
		Format(display_message, sizeof(display_message), " \x01%s", client_message);
	}
	else
	{
		Format(display_message, sizeof(display_message), " \x01\x0B\x04%s\x01 %s", message_prefix, client_message);
	}
	new bool: setupColorForRecipients = false;
	if (color_index == -1)
	{
		setupColorForRecipients = true;
	}		
	if (g_bTrackColors4Chat && is_colored != 2)
	{
		while (IsStackEmpty(message_recipients) == false)
		{
			new recipient_client = -1;
			PopStackCell(message_recipients, recipient_client);
			new player_index = GetClientOfUserId(recipient_client);
			if (player_index > 0 && !IsFakeClient(player_index) && IsClientInGame(player_index))
			{
				if (setupColorForRecipients == true)
				{
					color_index = player_index;
				}
				PbSayText2(player_index, color_index, false, display_message);
			}
		}
	}
	else
	{
		PrintToChatRecipients(display_message);
	}
	return Plugin_Handled;
}


public Action:hlx_sm_psay2(args)
{
	if (args < 2)
	{
		PrintToServer("Usage: hlx_sm_psay2 <userid><colored><message> - sends green colored private message");
		return Plugin_Handled;
	}
	
	decl String: client_list[192];
	GetCmdArg(1, client_list, sizeof(client_list));
	BuildClientList(client_list);

	decl String: colored_param[32];
	GetCmdArg(2, colored_param, sizeof(colored_param));
	
	new ignore_param = 0;
	if (strcmp(colored_param, "2") == 0 || strcmp(colored_param, "1") == 0 || strcmp(colored_param, "0") == 0)
	{
		ignore_param = 1;
	}

	new String: client_message[192];
	GetCmdArg((ignore_param + 2), client_message, sizeof(client_message));

	if (IsStackEmpty(message_recipients)) {
		return Plugin_Handled;
	}
	
	// Strip color control codes
	decl String:buffer_message[192];
	new j = 0;
	for (new i = 0; i < sizeof(client_message); i++)
	{
		new character = client_message[i];
		if (character < 5 && character > 0)
		{
			continue;
		}
		buffer_message[j] = client_message[i];
		if (character == 0)
		{
			break;
		}
		j++;
	}
	
	if (strcmp(message_prefix, "") == 0)
	{
		Format(client_message, sizeof(client_message), " \x01\x0B\x04%s", buffer_message);
	}
	else
	{
		Format(client_message, sizeof(client_message), " \x01\x0B\x04%s %s", message_prefix, buffer_message);
	}
	PrintToChatRecipients(client_message);
	return Plugin_Handled;
}


public Action:hlx_sm_csay(args)
{
	if (args < 1)
	{
		PrintToServer("Usage: hlx_sm_csay <message> - display center message");
		return Plugin_Handled;
	}

	new String: display_message[192];
	GetCmdArg(1, display_message, sizeof(display_message));

	if (strcmp(display_message, "") != 0)
	{
		PrintCenterTextAll("%s", display_message);
	}		
	return Plugin_Handled;
}


public Action:hlx_sm_msay(args)
{
	if (args < 3)
	{
		PrintToServer("Usage: hlx_sm_msay <time><userid><message> - sends hud message");
		return Plugin_Handled;
	}
	
	decl String: display_time[16];
	GetCmdArg(1, display_time, sizeof(display_time));
	
	decl String: client_id[32];
	GetCmdArg(2, client_id, sizeof(client_id));
	
	decl String: handler_param[32];
	GetCmdArg(3, handler_param, sizeof(handler_param));
	
	new ignore_param = 0;
	new need_handler = 0;
	if (handler_param[1] == 0 && (handler_param[0] == '1' || handler_param[0] == '0'))
	{
		need_handler = 1;
		ignore_param = 1;
	}

	new String: client_message[1024];
	GetCmdArg((ignore_param + 3), client_message, 1024);

	new time = StringToInt(display_time);
	if (time <= 0)
	{
		time = 10;
	}

	new client = StringToInt(client_id);
	if (client > 0)
	{
		new player_index = GetClientOfUserId(client);
		if (player_index > 0 && !IsFakeClient(player_index) && IsClientInGame(player_index) && strcmp(client_message, "") != 0)
		{
			display_menu(player_index, time, client_message, need_handler);
		}	
	}
	
	return Plugin_Handled;
}

public Action:hlx_sm_tsay(args)
{
	if (args < 3)
	{
		PrintToServer("Usage: hlx_sm_tsay <time><userid><message> - sends hud message");
		return Plugin_Handled;
	}

	decl String: display_time[16];
	GetCmdArg(1, display_time, sizeof(display_time));
	
	decl String: client_id[32];
	GetCmdArg(2, client_id, sizeof(client_id));

	new String: client_message[192];
	GetCmdArg(3, client_message, sizeof(client_message));
	
	new client = StringToInt(client_id);
	if ((client > 0) && (strcmp(client_message, "") != 0))
	{
		new player_index = GetClientOfUserId(client);
		if (player_index > 0 && !IsFakeClient(player_index) && IsClientInGame(player_index))
		{
			new Handle:values = CreateKeyValues("msg");
			KvSetString(values, "title", client_message);
			KvSetNum(values, "level", 1); 
			KvSetString(values, "time", display_time); 
			CreateDialog(player_index, values, DialogType_Msg);
			CloseHandle(values);
		}	
	}
	
	return Plugin_Handled;
}


public Action:hlx_sm_hint(args)
{
	if (args < 2)
	{
		PrintToServer("Usage: hlx_sm_hint <userid><message> - send hint message");
		return Plugin_Handled;
	}

	decl String: client_list[192];
	GetCmdArg(1, client_list, sizeof(client_list));
	BuildClientList(client_list);

	new String: client_message[192];
	GetCmdArg(2, client_message, sizeof(client_message));

	if (IsStackEmpty(message_recipients) == false && strcmp(client_message, "") != 0)
	{
		while (IsStackEmpty(message_recipients) == false)
		{
			new recipient_client = -1;
			PopStackCell(message_recipients, recipient_client);
			
			new player_index = GetClientOfUserId(recipient_client);
			if (player_index > 0 && !IsFakeClient(player_index) && IsClientInGame(player_index) && IsClientInGame(player_index))
			{
				PrintHintText(player_index, "%s", client_message);
			}
		}
	}
	return Plugin_Handled;
}


public Action:hlx_sm_browse(args)
{
	if (args < 2)
	{
		PrintToServer("Usage: hlx_sm_browse <userid><url> - open client ingame browser");
		return Plugin_Handled;
	}

	decl String: client_list[192];
	GetCmdArg(1, client_list, sizeof(client_list));
	BuildClientList(client_list);

	new String: client_url[512];
	GetCmdArg(2, client_url, sizeof(client_url));

	if (IsStackEmpty(message_recipients) == false && strcmp(client_url, "") != 0)
	{
		while (IsStackEmpty(message_recipients) == false)
		{
			new recipient_client = -1;
			PopStackCell(message_recipients, recipient_client);

			new player_index = GetClientOfUserId(recipient_client);
			if (player_index > 0 && !IsFakeClient(player_index) && IsClientInGame(player_index))
			{
				if (g_bGameCanDoMotd)
				{
					if (g_bPlyrCanDoMotd[player_index])
					{
						FixMotdCSGO(client_url);
						ShowMOTDPanel(player_index, "HLstatsX:CE", client_url, MOTDPANEL_TYPE_URL);
					}
					else
					{
						PrintToChat(player_index, "HTML MOTD needs to be enabled in your game options to use this command");
					}
				}
				else
				{
					PrintToChat(player_index, "This game does not support the HTML MOTD window required for this command");
				}
			}
		}
	}
	
	return Plugin_Handled;
}


public Action:hlx_sm_swap(args)
{
	if (args < 1)
	{
		PrintToServer("Usage: hlx_sm_swap <userid> - swaps players to the opposite team (css only)");
		return Plugin_Handled;
	}
	
	decl String:client_id[32];
	GetCmdArg(1, client_id, sizeof(client_id));

	new client = StringToInt(client_id);
	if (client > 0)
	{
		new player_index = GetClientOfUserId(client);
		if (player_index > 0 && IsClientInGame(player_index))
		{
			swap_player(player_index);
		}
	}
	return Plugin_Handled;
}


public Action:hlx_sm_redirect(args)
{
	if (args < 3)
	{
		PrintToServer("Usage: hlx_sm_redirect <time><userid><address><reason> - asks player to be redirected to specified gameserver");
		return Plugin_Handled;
	}

	decl String: display_time[16];
	GetCmdArg(1, display_time, sizeof(display_time));

	decl String: client_list[192];
	GetCmdArg(2, client_list, sizeof(client_list));
	BuildClientList(client_list);
	
	new String: server_address[192];
	GetCmdArg(3, server_address, sizeof(server_address));

	new String: redirect_reason[192];
	GetCmdArg(4, redirect_reason, sizeof(redirect_reason));

	if (IsStackEmpty(message_recipients) == false && strcmp(server_address, "") != 0)
	{
		while (IsStackEmpty(message_recipients) == false)
		{
			new recipient_client = -1;
			PopStackCell(message_recipients, recipient_client);

			new player_index = GetClientOfUserId(recipient_client);
			if (player_index > 0 && !IsFakeClient(player_index) && IsClientInGame(player_index))
			{
				new Handle:top_values = CreateKeyValues("msg");
				KvSetString(top_values, "title", redirect_reason);
				KvSetNum(top_values, "level", 1); 
				KvSetString(top_values, "time", display_time); 
				CreateDialog(player_index, top_values, DialogType_Msg);
				CloseHandle(top_values);
				
				new Float: display_time_float;
				display_time_float = StringToFloat(display_time);
				DisplayAskConnectBox(player_index, display_time_float, server_address);
			}
		}
	}
	
	return Plugin_Handled;
}


public Action:hlx_sm_player_action(args)
{
	if (args < 2)
	{
		PrintToServer("Usage: hlx_sm_player_action <clientid><action> - trigger player action to be handled from HLstatsX");
		return Plugin_Handled;
	}

	decl String: client_id[32];
	GetCmdArg(1, client_id, sizeof(client_id));

	decl String: player_action[64];
	GetCmdArg(2, player_action, sizeof(player_action));

	new client = StringToInt(client_id);

	LogPlayerEvent(client, "triggered", player_action);

	return Plugin_Handled;
}


public Action:hlx_sm_team_action(args)
{
	if (args < 2)
	{
		PrintToServer("Usage: hlx_sm_team_action <team_name><action> - trigger team action to be handled from HLstatsX");
		return Plugin_Handled;
	}

	decl String: team_name[64];
	GetCmdArg(1, team_name, sizeof(team_name));

	decl String: team_action[64];
	GetCmdArg(2, team_action, sizeof(team_action));

	LogToGame("Team \"%s\" triggered \"%s\"", team_name, team_action); 

	return Plugin_Handled;
}


public Action:hlx_sm_world_action(args)
{
	if (args < 1)
	{
		PrintToServer("Usage: hlx_sm_world_action <action> - trigger world action to be handled from HLstatsX");
		return Plugin_Handled;
	}

	decl String: world_action[64];
	GetCmdArg(1, world_action, sizeof(world_action));

	LogToGame("World triggered \"%s\"", world_action); 

	return Plugin_Handled;
}


is_command_blocked(String: command[])
{
	new command_blocked = 0;
	new command_index = 0;
	while ((command_blocked == 0) && (command_index < sizeof(blocked_commands)))
	{
		if (strcmp(command, blocked_commands[command_index]) == 0)
		{
			command_blocked++;
		}
		command_index++;
	}
	if (command_blocked > 0)
	{
		return 1;
	}
	return 0;
}


public Action:hlx_block_commands(client, const String:command[], args)
{
	if (client)
	{
		if (client == 0)
		{
			return Plugin_Continue;
		}
		new block_chat_commands = GetConVarInt(hlx_block_chat_commands);

		decl String: user_command[192];
		GetCmdArgString(user_command, sizeof(user_command));

		decl String: origin_command[192];
		new start_index = 0;
		new command_length = strlen(user_command);
		if (command_length > 0)
		{
			if (user_command[0] == 34)
			{
				start_index = 1;
				if (user_command[command_length - 1] == 34)
				{
					user_command[command_length - 1] = 0;
				}
			}
			strcopy(origin_command, sizeof(origin_command), user_command[start_index]);
			
			if (user_command[start_index] == 47)
			{
				start_index++;
			}
		}

		new String: command_type[32] = "say";

		if (command_length > 0)
		{
			if (block_chat_commands > 0)
			{
				new command_blocked = is_command_blocked(user_command[start_index]);
				if (command_blocked > 0)
				{
					if (IsClientInGame(client))
					{
						if ((strcmp("hlx_menu", user_command[start_index]) == 0) ||
								(strcmp("hlx", user_command[start_index]) == 0) ||
								(strcmp("hlstatsx", user_command[start_index]) == 0))
						{
							DisplayMenu(HLstatsXMenuMain, client, MENU_TIME_FOREVER);
						}
						LogPlayerEvent(client, command_type, origin_command);
					}
					return Plugin_Stop;
				}
			}
			else
			{
				if (IsClientInGame(client) &&
						(strcmp("hlx_menu", user_command[start_index]) == 0
							|| strcmp("hlx", user_command[start_index]) == 0
							|| strcmp("hlstatsx", user_command[start_index]) == 0))
				{
					DisplayMenu(HLstatsXMenuMain, client, MENU_TIME_FOREVER);
				}
				
				return Plugin_Continue;
			}
		}
		
	}
	return Plugin_Continue;
}


public Action: HLstatsX_Event_PlyTeamChange(Handle:event, const String:name[], bool:dontBroadcast)
{
	new client = GetClientOfUserId(GetEventInt(event, "userid"));
	if (client > 0)
	{
		for (new i = 0; (i < sizeof(ColorSlotArray)); i++)
		{
			new color_client = ColorSlotArray[i];
			if (color_client > -1)
			{
				if (color_client == client)
				{
					ColorSlotArray[i] = -1;
				}
			}
		}
	}

	return Plugin_Continue;
}



swap_player(player_index)
{
	if (IsClientInGame(player_index))
	{
		switch (GetClientTeam(player_index))
		{
		case CS_TEAM_CT:
			{
				if (IsPlayerAlive(player_index))
				{
					CS_SwitchTeam(player_index, CS_TEAM_T);
					CS_RespawnPlayer(player_index);
				}
				else
				{
					CS_SwitchTeam(player_index, CS_TEAM_T);
				}
			}
		case CS_TEAM_T:
			{
				if (IsPlayerAlive(player_index))
				{
					CS_SwitchTeam(player_index, CS_TEAM_CT);
					CS_RespawnPlayer(player_index);
				}
				else
				{
					CS_SwitchTeam(player_index, CS_TEAM_CT);
				}
			}
		}
	}
}


public CreateHLstatsXMenuMain(&Handle: MenuHandle)
{
	MenuHandle = CreateMenu(HLstatsXMainCommandHandler, MenuAction_Select|MenuAction_Cancel);

	if (!g_bGameCanDoMotd)
	{
		SetMenuTitle(MenuHandle, "HLstatsX - Main Menu");
		AddMenuItem(MenuHandle, "", "Display Rank");
		AddMenuItem(MenuHandle, "", "Next Players");
		AddMenuItem(MenuHandle, "", "Top10 Players");
		AddMenuItem(MenuHandle, "", "Auto Ranking");
		AddMenuItem(MenuHandle, "", "Toggle Point Msgs");
		AddMenuItem(MenuHandle, "", "Toggle Ranking Display");
	}
	else
	{
		SetMenuTitle(MenuHandle, "HLstatsX - Main Menu");
		AddMenuItem(MenuHandle, "", "Display Rank");
		AddMenuItem(MenuHandle, "", "Next Players");
		AddMenuItem(MenuHandle, "", "Top10 Players");
		AddMenuItem(MenuHandle, "", "Clans Ranking");
		AddMenuItem(MenuHandle, "", "Server Status");
		AddMenuItem(MenuHandle, "", "Statsme");
		AddMenuItem(MenuHandle, "", "Auto Ranking");
		AddMenuItem(MenuHandle, "", "Toggle Point Msgs");
		AddMenuItem(MenuHandle, "", "Weapon Usage");
		AddMenuItem(MenuHandle, "", "Weapons Accuracy");
		AddMenuItem(MenuHandle, "", "Weapons Targets");
		AddMenuItem(MenuHandle, "", "Player Kills");
		AddMenuItem(MenuHandle, "", "Toggle Ranking Display");
		AddMenuItem(MenuHandle, "", "Ban and Cheater List");
		AddMenuItem(MenuHandle, "", "Display Help");
	}

	SetMenuPagination(MenuHandle, 8);
}


public CreateHLstatsXMenuAuto(&Handle: MenuHandle)
{
	MenuHandle = CreateMenu(HLstatsXAutoCommandHandler, MenuAction_Select|MenuAction_Cancel);

	SetMenuTitle(MenuHandle, "HLstatsX - Auto-Ranking");
	AddMenuItem(MenuHandle, "", "Enable on round-start");
	AddMenuItem(MenuHandle, "", "Enable on round-end");
	AddMenuItem(MenuHandle, "", "Enable on player death");
	AddMenuItem(MenuHandle, "", "Disable");

	SetMenuPagination(MenuHandle, 8);
}

public CreateHLstatsXMenuEvents(&Handle: MenuHandle)
{
	MenuHandle = CreateMenu(HLstatsXEventsCommandHandler, MenuAction_Select|MenuAction_Cancel);

	SetMenuTitle(MenuHandle, "HLstatsX - Console Events");
	AddMenuItem(MenuHandle, "", "Enable Events");
	AddMenuItem(MenuHandle, "", "Disable Events");
	AddMenuItem(MenuHandle, "", "Enable Global Chat");
	AddMenuItem(MenuHandle, "", "Disable Global Chat");

	SetMenuPagination(MenuHandle, 8);
}


make_player_command(client, String: player_command[192]) 
{
	LogPlayerEvent(client, "say", player_command);
}


public HLstatsXMainCommandHandler(Handle:menu, MenuAction:action, param1, param2)
{
	if (action == MenuAction_Select)
	{
		if (IsClientInGame(param1))
		{
			if (!g_bGameCanDoMotd)
			{
				switch (param2)
				{
				case 0 : 
					make_player_command(param1, "/rank");
				case 1 : 
					make_player_command(param1, "/next");
				case 2 : 
					make_player_command(param1, "/top10");
				case 3 : 
					DisplayMenu(HLstatsXMenuAuto, param1, MENU_TIME_FOREVER);
				case 4 : 
					DisplayMenu(HLstatsXMenuEvents, param1, MENU_TIME_FOREVER);
				case 5 : 
					make_player_command(param1, "/hlx_hideranking");
				}
			}
			else
			{
				switch (param2)
				{
				case 0 : 
					make_player_command(param1, "/rank");
				case 1 : 
					make_player_command(param1, "/next");
				case 2 : 
					make_player_command(param1, "/top10");
				case 3 : 
					make_player_command(param1, "/clans");
				case 4 : 
					make_player_command(param1, "/status");
				case 5 : 
					make_player_command(param1, "/statsme");
				case 6 : 
					DisplayMenu(HLstatsXMenuAuto, param1, MENU_TIME_FOREVER);
				case 7 : 
					DisplayMenu(HLstatsXMenuEvents, param1, MENU_TIME_FOREVER);
				case 8 : 
					make_player_command(param1, "/weapons");
				case 9 : 
					make_player_command(param1, "/accuracy");
				case 10 : 
					make_player_command(param1, "/targets");
				case 11 : 
					make_player_command(param1, "/kills");
				case 12 : 
					make_player_command(param1, "/hlx_hideranking");
				case 13 : 
					make_player_command(param1, "/bans");
				case 14 : 
					make_player_command(param1, "/help");
				}
			}
		}
	}
}


public HLstatsXAutoCommandHandler(Handle:menu, MenuAction:action, param1, param2)
{
	if (action == MenuAction_Select)
	{
		if (IsClientInGame(param1))
		{
			switch (param2)
			{
			case 0 : 
				make_player_command(param1, "/hlx_auto start rank");
			case 1 : 
				make_player_command(param1, "/hlx_auto end rank");
			case 2 : 
				make_player_command(param1, "/hlx_auto kill rank");
			case 3 : 
				make_player_command(param1, "/hlx_auto clear");
			}
		}
	}
}

public HLstatsXEventsCommandHandler(Handle:menu, MenuAction:action, param1, param2)
{
	if (action == MenuAction_Select)
	{
		if (IsClientInGame(param1))
		{
			switch (param2)
			{
			case 0 : 
				make_player_command(param1, "/hlx_display 1");
			case 1 : 
				make_player_command(param1, "/hlx_display 0");
			case 2 : 
				make_player_command(param1, "/hlx_chat 1");
			case 3 : 
				make_player_command(param1, "/hlx_chat 0");
			}
		}
	}
}

stock BuildClientList(const String:client_list[])
{
	if (StrContains(client_list, ",") > -1)
	{
		decl String:MessageRecipients[MaxClients][8];
		new recipient_count = ExplodeString(client_list, ",", MessageRecipients, MaxClients, 8);
		for (new i = 0; (i < recipient_count); i++)
		{
			PushStackCell(message_recipients, StringToInt(MessageRecipients[i]));
		}
	}
	else
	{
		PushStackCell(message_recipients, StringToInt(client_list));
	}
}

stock PrintToChatRecipients(const String:message[])
{
	while (IsStackEmpty(message_recipients) == false)
	{
		new recipient_client = -1;
		PopStackCell(message_recipients, recipient_client);

		new client = GetClientOfUserId(recipient_client);
		if (client > 0 && !IsFakeClient(client) && IsClientInGame(client))
		{
			PrintToChat(client, "%s", message);
		}
	}
}

stock PrintToChatRecipientsFF(const String:message[])
{
	while (IsStackEmpty(message_recipients) == false)
	{
		new recipient_client = -1;
		PopStackCell(message_recipients, recipient_client);

		new client = GetClientOfUserId(recipient_client);
		if (client > 0 && !IsFakeClient(client) && IsClientInGame(client))
		{	
			PbSayText(client, 0, true, message);
		}
	}
}

stock PbSayText2(client, author = 0, bool:bWantsToChat = false, const String:szFormat[], any:...)
{
	decl String:szSendMsg[192];
	VFormat(szSendMsg, sizeof(szSendMsg), szFormat, 5);
	StrCat(szSendMsg, sizeof(szSendMsg), "\n");

	new Handle:pb = StartMessageOne("SayText2", client);

	if (GetUserMessageType() == UM_Protobuf)
	{
		PbSetInt(pb, "ent_idx", author);
		// process as chat (removes color if enabled)
		PbSetBool(pb, "chat", bWantsToChat);
		PbSetString(pb, "msg_name", szSendMsg );
		PbAddString(pb, "params", "");
		PbAddString(pb, "params", "");
		PbAddString(pb, "params", "");
		PbAddString(pb, "params", "");
	}
	else
	{
		BfWriteByte(pb, author); 
		BfWriteByte(pb, bWantsToChat);
		BfWriteString(pb, szFormat);
	}
	
	EndMessage();
}

stock PbSayText(client, author = 0, bool:bWantsToChat = false, const String:szFormat[], any:...)
{
	decl String:szSendMsg[192];
	VFormat(szSendMsg, sizeof(szSendMsg), szFormat, 5);
	StrCat(szSendMsg, sizeof(szSendMsg), "\n");

	new Handle:pb = StartMessageOne("SayText", client);

	if (GetUserMessageType() == UM_Protobuf)
	{
		PbSetInt(pb, "ent_idx", author);
		PbSetString(pb, "text", szSendMsg);
		PbSetBool(pb, "chat", bWantsToChat);
		
	}
	else
	{
		BfWriteByte(pb, author); // send as console
		BfWriteString(pb, szFormat);
		BfWriteByte(pb, bWantsToChat); // 1 to enable color parsing, 0 to not
	}
	
	EndMessage();
}

stock FixMotdCSGO(String:web[512])
{
	Format(web, sizeof(web), "javascript: var x = screen.width * 0.90;var y = screen.height * 0.90;window.open(\"%s\", \"Really boomix, JS?\",\"scrollbars=yes, width='+x+',height='+y+'\");", web);
}

/////////////////////////////////////////////////////////logege 

#define LOGHELPER_VERSION 3
new String:g_team_list[16][64];

stock LogPlayerEvent(client, const String:verb[], const String:event[], bool:display_location = false, const String:properties[] = "")
{
	if (IsValidPlayer(client))
	{
		decl String:player_authid[32];
		if (!GetClientAuthId(client, AuthId_Steam2, player_authid, sizeof(player_authid)))
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

// Verb should always be triggered" for this.
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