/**
 * 2008 - Modified by Nicholas Hastings (psychonic) for used with HLstatsX Community Edition
 * http://www.hlxcommunity.com
 *
 * HLstatsX - AMX Mod X plugin to display ingame messages
 * http://www.hlstatsx.com/
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

#pragma dynamic 16000

#include <amxmodx>
#include <amxmisc>

#include <engine>
#include <tfcx>

#include <cellarray>
#include <fakemeta>

#define VERSION "1.6.19 (HL1)"


new g_hlx_block_commands
new g_hlx_message_prefix
new blocked_commands[][] = { "rank", "skill", "points", "place", "session", "session_data", 
                             "kpd", "kdratio", "kdeath", "next", "load", "status", "servers", 
                             "top20", "top10", "top5", "clans", "cheaters", "statsme", "weapons", 
                             "weapon", "action", "actions", "accuracy", "targets", "target", "kills", 
                             "kill", "player_kills", "cmd", "cmds", "command", "hlx_display 0", 
                             "hlx_display 1", "hlx_teams 0", "hlx_teams 1", "hlx_hideranking", 
                             "hlx_chat 0", "hlx_chat 1", "hlx_menu", "servers 1", "servers 2", 
                             "servers 3", "hlx", "hlstatsx", "help" }


new g_msgSayText
new g_HLstatsX_MainMenu
new g_HLstatsX_AutoMenu
new g_HLstatsX_EventsMenu

new logmessage_ignore[512]
new display_menu_keys = MENU_KEY_0|MENU_KEY_1|MENU_KEY_2|MENU_KEY_3|MENU_KEY_4|MENU_KEY_5|MENU_KEY_6|MENU_KEY_7|MENU_KEY_8|MENU_KEY_9


public plugin_init()
{
	register_plugin("HLstatsX CE Ingame Plugin", VERSION, "psychonic")
	register_srvcmd("hlx_amx_psay",   "hlx_amx_psay",   0, "<name or #userid><message> - sends private message")
	register_srvcmd("hlx_amx_bulkpsay",   "hlx_amx_bulkpsay",   0, "<userid list><message> - sends private message to many")
	register_srvcmd("hlx_amx_psay2",  "hlx_amx_psay2",  0, "<name or #userid><colored><message> - sends green colored private message")
	register_srvcmd("hlx_amx_say",    "hlx_amx_say",    0, "<message> - sends message to all players")
	register_srvcmd("hlx_amx_csay",   "hlx_amx_csay",   0, "<message> - sends center hud message to all players")
	register_srvcmd("hlx_amx_hint",   "hlx_amx_hint",   0, "<name or #userid><message> - sends hint message")
	register_srvcmd("hlx_amx_msay",   "hlx_amx_msay",   0, "<delay><userid><message> - displays advanced information")
	register_srvcmd("hlx_amx_browse", "hlx_amx_browse", 0, "<userid><url> - displays internal browser")
	register_srvcmd("hlx_amx_swap",   "hlx_amx_swap",   0, "<userid> - swaps players to the opposite team")

	register_cvar("hlxce_plugin_version", VERSION, FCVAR_SPONLY|FCVAR_SERVER)
	register_cvar("hlxce_version", "", FCVAR_SPONLY|FCVAR_SERVER)
	register_cvar("hlxce_webpage", "http://www.hlxcommunity.com", FCVAR_SPONLY|FCVAR_SERVER)
	g_hlx_block_commands = register_cvar("hlx_block_commands", "1")
	g_hlx_message_prefix = register_cvar("hlx_message_prefix", "")

	// building the menus only once
	g_HLstatsX_MainMenu = menu_create("HLstatsX - Main Menu", "mainmenu_handle")
	menu_additem(g_HLstatsX_MainMenu, "Display Rank",            "1")
	menu_additem(g_HLstatsX_MainMenu, "Next Players",            "2")
	menu_additem(g_HLstatsX_MainMenu, "Top10 Players",           "3")
	menu_additem(g_HLstatsX_MainMenu, "Clans Ranking",           "4")
	menu_additem(g_HLstatsX_MainMenu, "Server Status",           "5")
	menu_additem(g_HLstatsX_MainMenu, "Statsme",                 "6")
	menu_additem(g_HLstatsX_MainMenu, "Auto Ranking",            "7")
	menu_additem(g_HLstatsX_MainMenu, "Console Events",          "8")
	menu_additem(g_HLstatsX_MainMenu, "Weapon Usage",            "9")
	menu_additem(g_HLstatsX_MainMenu, "Weapons Accuracy",       "10")
	menu_additem(g_HLstatsX_MainMenu, "Weapons Targets",        "11")
	menu_additem(g_HLstatsX_MainMenu, "Player Kills",           "12")
	menu_additem(g_HLstatsX_MainMenu, "Toggle Ranking Display", "13")
	menu_additem(g_HLstatsX_MainMenu, "VAC Cheaterlist",        "14")
	menu_additem(g_HLstatsX_MainMenu, "Display Help",           "15")
	menu_setprop(g_HLstatsX_MainMenu, MPROP_PERPAGE, 6)

	g_HLstatsX_AutoMenu = menu_create("HLstatsX - Auto-Ranking", "automenu_handle")
	menu_additem(g_HLstatsX_AutoMenu, "Enable on round-start",   "1")
	menu_additem(g_HLstatsX_AutoMenu, "Enable on round-end",     "2")
	menu_additem(g_HLstatsX_AutoMenu, "Enable on player death",  "3")
	menu_additem(g_HLstatsX_AutoMenu, "Disable",                 "4")
	menu_setprop(g_HLstatsX_AutoMenu, MPROP_PERPAGE, 0)

	g_HLstatsX_EventsMenu = menu_create("HLstatsX - Console Events", "eventsmenu_handle")
	menu_additem(g_HLstatsX_EventsMenu, "Enable Events",         "1")
	menu_additem(g_HLstatsX_EventsMenu, "Disable Events",        "2")
	menu_additem(g_HLstatsX_EventsMenu, "Enable Global Chat",    "3")
	menu_additem(g_HLstatsX_EventsMenu, "Disable Global Chat",   "4")
	menu_setprop(g_HLstatsX_EventsMenu, MPROP_PERPAGE, 0)

	register_menucmd(register_menuid("Display Menu"), display_menu_keys, "handle_internal_menu")

	register_clcmd("say",		"hlx_block_commands")
	register_clcmd("say_team",	"hlx_block_commands")

	g_msgSayText = get_user_msgid("SayText") 
	
}


public log_player_event(client, verb[32], player_event[192], display_location)
{
	if ((client > 0) && (is_user_connected(client))) {
		new player_userid = get_user_userid(client)

		static player_authid[32]
		get_user_authid(client, player_authid, 31)

		static player_name[32]
		get_user_name(client, player_name, 31)

		static player_team[16]
		get_user_team(client, player_team, 15)

		if (display_location > 0) {
			new player_origin[3]
			get_user_origin (client, player_origin)

			format(logmessage_ignore, 511, "^"%s<%d><%s><%s>^" %s ^"%s^"", player_name, player_userid, player_authid, player_team, verb, player_event)
			log_message("^"%s<%d><%s><%s>^" %s ^"%s^" (position ^"%d %d %d^")", player_name, player_userid, player_authid, player_team, verb, player_event, player_origin[0], player_origin[1], player_origin[2])
		} else {
			log_message("^"%s<%d><%s><%s>^" %s ^"%s^"", player_name, player_userid, player_authid, player_team, verb, player_event)
		}
	}
}


public game_log_hook(AlertType: type, message[])
{
	if (type != at_logged ) {
		return FMRES_IGNORED
	}
	if ((strcmp("", logmessage_ignore) != 0) && (contain(message, logmessage_ignore) != -1)) {
		if (contain(message, "position") == -1) {
			logmessage_ignore = ""
			return FMRES_SUPERCEDE
		}
	}
	return FMRES_IGNORED
}


public client_death(killer, victim, wpnindex, hitplace, TK)
{
	new id = victim

	if ((id > 0) && (is_user_connected(id))) {
		new iStats[8], iHits[8]
		static szTeam[16], szName[32], szAuthid[32], szWeapon[24]
		new iUserid = get_user_userid(id)
		new _max = TFCMAX_WEAPONS
	
		get_user_team(id, szTeam, 15)
		get_user_name(id, szName, 31)
		get_user_authid(id, szAuthid, 31)

		for (new i = 1; i < _max; ++i) {
			if (get_user_wstats(id, i, iStats, iHits)) {
				xmod_get_wpnlogname(i, szWeapon, 23)
			
				log_message("^"%s<%d><%s><%s>^" triggered ^"weaponstats^" (weapon ^"%s^") (shots ^"%d^") (hits ^"%d^") (kills ^"%d^") (headshots ^"%d^") (tks ^"%d^") (damage ^"%d^") (deaths ^"%d^")", 
							szName, iUserid, szAuthid, szTeam, szWeapon, iStats[4], iStats[5], iStats[0], iStats[2], iStats[3], iStats[6], iStats[1])
				log_message("^"%s<%d><%s><%s>^" triggered ^"weaponstats2^" (weapon ^"%s^") (head ^"%d^") (chest ^"%d^") (stomach ^"%d^") (leftarm ^"%d^") (rightarm ^"%d^") (leftleg ^"%d^") (rightleg ^"%d^")", 
							szName, iUserid, szAuthid, szTeam, szWeapon, iHits[1], iHits[2], iHits[3], iHits[4], iHits[5], iHits[6], iHits[7])
			}
		}

		reset_user_wstats(id)
	}
}

stock ExplodeString( Output[][], Max, Size, Input[], Delimiter )
{
    new Idx, l = strlen(Input), Len;
    do Len += (1 + copyc( Output[Idx], Size, Input[Len], Delimiter ));
    while( (Len < l) && (++Idx < Max) )
    return Idx;
}

psay (client_id[], client_message[192])
{
	new client = str_to_num(client_id)

	if (client > 0) {
		new Players[32]
		new player_count, temp_player_index, player_index
		get_players(Players, player_count, "ch")
		for (temp_player_index = 0; temp_player_index < player_count; temp_player_index++) {
			new player = Players[temp_player_index] 
			new temp_user_id = get_user_userid(player)
			if (temp_user_id == client) {
			   player_index = player
			   break
			}
		}
		
		if ((player_index > 0) && (!is_user_bot(player_index)) && (is_user_connected(player_index))) {

			static display_message[192]
			static message_prefix[64]
			get_pcvar_string(g_hlx_message_prefix, message_prefix, 64)
			if (strcmp(message_prefix, "") == 0) {
				format(display_message, 192, "%s", client_message)
			} else {
				format(display_message, 192, "%s %s", message_prefix, client_message)
			}

			message_begin(MSG_ONE, g_msgSayText, {0,0,0}, player_index)
			write_byte(player_index)
			write_string(display_message)
			message_end()
		}	
	}
}

public hlx_amx_bulkpsay(id, level, cid)
{
	new argument_count = read_argc()
	if (argument_count < 3) {
		return PLUGIN_HANDLED
	}

	new client_id_list[48]
	read_argv(1, client_id_list, 47)
	new client_ids[8][6];
	ExplodeString(client_ids, 7, 5, client_id_list, ',');

	static colored_param[32]
	read_argv(2, colored_param, 31)

	new ignore_param = 0
	if (strcmp(colored_param, "1") == 0) {
		ignore_param = 1
	}
	if (strcmp(colored_param, "0") == 0) {
		ignore_param = 1
	}

	new client_message[192]
	for(new i = (1 + ignore_param); i < argument_count; i++) {
		static temp_argument[192]
		read_argv(i + 1, temp_argument, 191)
		if (i > (1 + ignore_param)) {
			if ((191 - strlen(client_message)) > strlen(temp_argument)) {
				if ((temp_argument[0] == 41) || (temp_argument[0] == 125)) {
					copy(client_message[strlen(client_message)], 191, temp_argument)
				} else if ((strlen(client_message) > 0) && (client_message[strlen(client_message)-1] != 40) && (client_message[strlen(client_message)-1] != 123) && (client_message[strlen(client_message)-1] != 58) && (client_message[strlen(client_message)-1] != 39) && (client_message[strlen(client_message)-1] != 44)) {
					if ((strcmp(temp_argument, ":") != 0) && (strcmp(temp_argument, ",") != 0) && (strcmp(temp_argument, "'") != 0)) {
						client_message[strlen(client_message)] = 32
					}
					copy(client_message[strlen(client_message)], 191, temp_argument)
				} else {
					copy(client_message[strlen(client_message)], 191, temp_argument)
				}
			}
		} else {
			if ((192 - strlen(client_message)) > strlen(temp_argument)) {
				copy(client_message[strlen(client_message)], 191, temp_argument)
			}
		}
	}

	for (new i = 0; i < 8; i++)
	{
		psay(client_ids[i], client_message);
	}
	
	return PLUGIN_HANDLED
}

public hlx_amx_psay(id, level, cid)
{
	new argument_count = read_argc()
	if (argument_count < 3) {
		return PLUGIN_HANDLED
	}

	static client_id[32]
	read_argv(1, client_id, 31)
	copy(client_id, 30, client_id[1]) 

	static colored_param[32]
	read_argv(2, colored_param, 31)

	new ignore_param = 0
	if (strcmp(colored_param, "1") == 0) {
		ignore_param = 1
	}
	if (strcmp(colored_param, "0") == 0) {
		ignore_param = 1
	}

	new client_message[192]
	for(new i = (1 + ignore_param); i < argument_count; i++) {
		static temp_argument[192]
		read_argv(i + 1, temp_argument, 191)
		if (i > (1 + ignore_param)) {
			if ((191 - strlen(client_message)) > strlen(temp_argument)) {
				if ((temp_argument[0] == 41) || (temp_argument[0] == 125)) {
					copy(client_message[strlen(client_message)], 191, temp_argument)
				} else if ((strlen(client_message) > 0) && (client_message[strlen(client_message)-1] != 40) && (client_message[strlen(client_message)-1] != 123) && (client_message[strlen(client_message)-1] != 58) && (client_message[strlen(client_message)-1] != 39) && (client_message[strlen(client_message)-1] != 44)) {
					if ((strcmp(temp_argument, ":") != 0) && (strcmp(temp_argument, ",") != 0) && (strcmp(temp_argument, "'") != 0)) {
						client_message[strlen(client_message)] = 32
					}
					copy(client_message[strlen(client_message)], 191, temp_argument)
				} else {
					copy(client_message[strlen(client_message)], 191, temp_argument)
				}
			}
		} else {
			if ((192 - strlen(client_message)) > strlen(temp_argument)) {
				copy(client_message[strlen(client_message)], 191, temp_argument)
			}
		}
	}
	
	psay(client_id, client_message)
	
	return PLUGIN_HANDLED
}

public hlx_amx_psay2(id, level, cid)
{
	new argument_count = read_argc()
	if (argument_count < 3) {
		return PLUGIN_HANDLED
	}

	static client_id[32]
	read_argv(1, client_id, 31)
	copy(client_id, 30, client_id[1]) 

	static colored_param[32]
	read_argv(2, colored_param, 31)
	new ignore_param = 0
	if (strcmp(colored_param, "1") == 0) {
		ignore_param = 1
	}
	if (strcmp(colored_param, "0") == 0) {
		ignore_param = 1
	}

	new client_message[192]
	for(new i = (1 + ignore_param); i < argument_count; i++) {
		static temp_argument[192]
		read_argv(i + 1, temp_argument, 191)
		if (i > (1 + ignore_param)) {
			if ((191 - strlen(client_message)) > strlen(temp_argument)) {
				if ((temp_argument[0] == 41) || (temp_argument[0] == 125)) {
					copy(client_message[strlen(client_message)], 191, temp_argument)
				} else if ((strlen(client_message) > 0) && (client_message[strlen(client_message)-1] != 40) && (client_message[strlen(client_message)-1] != 123) && (client_message[strlen(client_message)-1] != 58) && (client_message[strlen(client_message)-1] != 39) && (client_message[strlen(client_message)-1] != 44)) {
					if ((strcmp(temp_argument, ":") != 0) && (strcmp(temp_argument, ",") != 0) && (strcmp(temp_argument, "'") != 0)) {
						client_message[strlen(client_message)] = 32
					}
					copy(client_message[strlen(client_message)], 191, temp_argument)
				} else {
					copy(client_message[strlen(client_message)], 191, temp_argument)
				}
			}
		} else {
			if ((192 - strlen(client_message)) > strlen(temp_argument)) {
				copy(client_message[strlen(client_message)], 191, temp_argument)
			}
		}
	}
	
	new client = str_to_num(client_id)

	if (client > 0) {
		new Players[32]
		new player_count, temp_player_index, player_index
		get_players(Players, player_count, "ch")
		for (temp_player_index = 0; temp_player_index < player_count; temp_player_index++) {
			new player = Players[temp_player_index] 
			new temp_user_id = get_user_userid(player)
			if (temp_user_id == client) {
			   player_index = player
			   break
			}
		}
		
		if ((player_index > 0) && (!is_user_bot(player_index)) && (is_user_connected(player_index))) {
			new color_index = player_index

			static display_message[192]
			static message_prefix[64]
			get_pcvar_string(g_hlx_message_prefix, message_prefix, 64)
			if (strcmp(message_prefix, "") == 0) {
				format(display_message, 192, "%s", client_message)
			} else {
				format(display_message, 192, "%s %s", message_prefix, client_message)
			}

			message_begin(MSG_ONE, g_msgSayText, {0,0,0}, player_index)
			write_byte(color_index)
			write_string(display_message)
			message_end()
		}	
	}
	
	return PLUGIN_HANDLED
}

public hlx_amx_say(id, level, cid)
{
	if (!cmd_access(id, level, cid, 2))
		return PLUGIN_HANDLED
	
	static message[192]
	read_args(message, 191)
	remove_quotes(message)

	static message_prefix[64]
	get_pcvar_string(g_hlx_message_prefix, message_prefix, 64)
	if (strcmp(message_prefix, "") == 0) {
		client_print(0, print_chat, "%s", message)
	} else {
		client_print(0, print_chat, "%s %s", message_prefix, message)
	}

	return PLUGIN_HANDLED
}


public hlx_amx_csay(id, level, cid)
{
	if (!cmd_access(id, level, cid, 2))
		return PLUGIN_HANDLED
	
	static message[192]
	read_args(message, 191)
	remove_quotes(message)
	
	new color3[0][] = {255, 255, 255}
	new Float:verpos = 0.3
	
	set_hudmessage(color3[0][0], color3[0][1], color3[0][2], -1.0, verpos, 0, 6.0, 6.0, 0.5, 0.15, -1)
	show_hudmessage(0, "%s", message)

	return PLUGIN_HANDLED
}


public hlx_amx_hint(id, level, cid)
{
	if (!cmd_access(id, level, cid, 2))
		return PLUGIN_HANDLED

	new argument_count = read_argc()

	static client_id[32]
	read_argv(1, client_id, 31)
	copy(client_id, 30, client_id[1]) 

	new client_message[192]
	for(new i = 1; i < argument_count; i++) {
		static temp_argument[192]
		read_argv(i + 1, temp_argument, 191)
		if (i > 1) {
			if ((191 - strlen(client_message)) > strlen(temp_argument)) {
				if ((temp_argument[0] == 41) || (temp_argument[0] == 125)) {
					copy(client_message[strlen(client_message)], 191, temp_argument)
				} else if ((strlen(client_message) > 0) && (client_message[strlen(client_message)-1] != 40) && (client_message[strlen(client_message)-1] != 123) && (client_message[strlen(client_message)-1] != 58) && (client_message[strlen(client_message)-1] != 39) && (client_message[strlen(client_message)-1] != 44)) {
					if ((strcmp(temp_argument, ":") != 0) && (strcmp(temp_argument, ",") != 0) && (strcmp(temp_argument, "'") != 0)) {
						client_message[strlen(client_message)] = 32
					}
					copy(client_message[strlen(client_message)], 191, temp_argument)
				} else {
					copy(client_message[strlen(client_message)], 191, temp_argument)
				}
			}
		} else {
			if ((192 - strlen(client_message)) > strlen(temp_argument)) {
				copy(client_message[strlen(client_message)], 191, temp_argument)
			}
		}
	}
	
	new client = str_to_num(client_id)

	if (client > 0) {
		new Players[32]
		new player_count, temp_player_index, player_index
		get_players(Players, player_count, "ch")
		for (temp_player_index = 0; temp_player_index < player_count; temp_player_index++) {
			new player = Players[temp_player_index] 
			new temp_user_id = get_user_userid(player)
			if (temp_user_id == client) {
			   player_index = player
			   break
			}
		}
		
		if ((player_index > 0) && (!is_user_bot(player_index)) && (is_user_connected(player_index))) {
			new color3[0][] = {255, 128, 0}
			new Float:verpos = 0.80
	
			set_hudmessage(color3[0][0], color3[0][1], color3[0][2], -1.0, verpos, 0, 6.0, 6.0, 0.5, 0.15, -1)
			show_hudmessage(player_index, "%s", client_message)
		}	
	}
	
	return PLUGIN_HANDLED
}


public hlx_amx_msay(id, level, cid)
{
	if (!cmd_access(id, level, cid, 3))
		return PLUGIN_HANDLED

	static delay[8]
	read_argv(1, delay, 7)
	remove_quotes(delay)

	static name[32]
	read_argv(2, name, 31)
	copy(name, 30, name[1]) 
	new raw_user_id = str_to_num(name)
	
	static handler_param[32]
	read_argv(3, handler_param, 31)
	new ignore_param = 0
	new need_handler = 0
	if (strcmp(handler_param, "1") == 0) {
		need_handler = 1
		ignore_param = 1
	}
	if (strcmp(handler_param, "0") == 0) {
		need_handler = 0
		ignore_param = 1
	}
	
	static message[1024]
	new userid
	new Players[32]
	new player_count, player_index
	get_players(Players, player_count, "ch")
	for (player_index = 0; player_index < player_count; player_index++) {
		new player = Players[player_index] 
		new temp_user_id = get_user_userid(player)
		if (temp_user_id == raw_user_id) {
		   userid = player
		   break
		}
	}

	read_args(message, 1023)
	
	new find_pattern[] = "#"
	new find_pos = strfind(message, find_pattern)
	new text_pos = find_pos + strlen(name) + 2
	if (ignore_param == 1) {
		text_pos += 3
	}
	
	static menu_text[1024]
	copy(menu_text, 1023, message[text_pos])
	remove_quotes(menu_text)
	
	new menu_display[1024]

	new i, start = 0
	new nLen = 0
	new buffer[1024]

	for(i = 0; i < strlen(menu_text); i++) {
		if (i > 0) {
			if ((menu_text[i-1] == '\') && (menu_text[i] == 'n')) {
				buffer = ""
				copy(buffer, (i - start)-1 , menu_text[start])
				nLen += format(menu_display[nLen], (1023 - nLen), "%s^n", buffer)
				i += 1
				start = i
			}  
		}
	}

	if ((userid > 0) && (!is_user_bot(userid)) && (is_user_connected(userid))) {
		if (need_handler == 0) {
			show_menu(userid, display_menu_keys, menu_display, 15)
		} else {
			show_menu(userid, display_menu_keys, menu_display, 15, "Display Menu")
		}
	}
	
	return PLUGIN_HANDLED

}


public handle_internal_menu(id, key)
{
	new client = id
	if (is_user_connected(client)) {
		if (key < 9) {
			static player_event[192]
			new slot = key
			slot++
			num_to_str(slot, player_event, 192)
			log_player_event(client, "selected", player_event, 0)
		} else {
			new player_event[192] = "cancel"
			log_player_event(client, "selected", player_event, 0)
		}
	}
}


public hlx_amx_browse(id, level, cid)
{
	if (!cmd_access(id, level, cid, 2))
		return PLUGIN_HANDLED

	static name[32]
	read_argv(1, name, 31)
	copy(name, 30, name[1]) 
	new raw_user_id = str_to_num(name)
	
	static message[1024]
	new userid
	new Players[32]
	new player_count, player_index
	get_players(Players, player_count, "ch")
	for (player_index = 0; player_index < player_count; player_index++) {
		new player = Players[player_index] 
		new temp_user_id = get_user_userid(player)
		if (temp_user_id == raw_user_id) {
		   userid = player
		   break
		}
	}

	read_args(message, 1023)
	
	new find_pattern[] = "#"
	new find_pos = strfind(message, find_pattern)
	
	static url[1024]
	copy(url, 1023, message[find_pos + strlen(name) + 2])
	remove_quotes(url)
	
	if ((userid > 0) && (!is_user_bot(userid)) && (is_user_connected(userid))) {
		show_motd(userid, url, "HLstatsX CE")
	}
	
	return PLUGIN_HANDLED
}

public hlx_amx_swap(id, level, cid)
{
	if (!cmd_access(id, level, cid, 1))
		return PLUGIN_HANDLED

	static client_id[32]
	read_argv(1, client_id, 31)
	copy(client_id, 30, client_id[1]) 
	new client = str_to_num(client_id)
	
	if (client > 0) {
		new userid = 0
		new Players[32]
		new player_count, player_index
		get_players(Players, player_count, "h")
		for (player_index = 0; player_index < player_count; player_index++) {
			new player = Players[player_index] 
			new temp_user_id = get_user_userid(player)
			if (temp_user_id == client) {
			   userid = player
			   break
			}
		}

		static player_team[32]
		get_user_team(userid, player_team, 31)

		if ((userid > 0) && (is_user_connected(userid))) {

		}
	}
	return PLUGIN_HANDLED
}

stock make_player_command(client, player_command[192]) 
{
	if (client > 0) {
		log_player_event(client, "say", player_command, 0)
	}
}

public display_menu(menu, id)
{
	menu_display(id, menu, 0)
}

public display_mainmenu(id)
{
	display_menu(g_HLstatsX_MainMenu, id)
	return PLUGIN_HANDLED
}

public display_automenu(id)
{
	display_menu(g_HLstatsX_AutoMenu, id)
	return PLUGIN_HANDLED
}

public display_eventsmenu(id)
{
	display_menu(g_HLstatsX_EventsMenu, id)
	return PLUGIN_HANDLED
}

public mainmenu_handle(id, menu, item)
{
	if (item < 0) {
		return PLUGIN_CONTINUE
	}
 
	static command[16], name[64]
	new access, callback
	menu_item_getinfo(menu, item, access, command, 15, name, 63, callback)
 
	new choice = str_to_num(command)
	switch (choice) {
		case 1 : 
			make_player_command(id, "/rank")
		case 2 : 
			make_player_command(id, "/next")
		case 3 : 
			make_player_command(id, "/top10")
		case 4 : 
			make_player_command(id, "/clans")
		case 5 : 
			make_player_command(id, "/status")
		case 6 : 
			make_player_command(id, "/statsme")
		case 7 : 
			display_automenu(id)
		case 8 : 
			display_eventsmenu(id)
		case 9 : 
			make_player_command(id, "/weapons")
		case 10 : 
			make_player_command(id, "/accuracy")
		case 11 : 
			make_player_command(id, "/targets")
		case 12 : 
			make_player_command(id, "/kills")
		case 13 : 
			make_player_command(id, "/hlx_hideranking")
		case 14 : 
			make_player_command(id, "/cheaters")
		case 15 : 
			make_player_command(id, "/help")
	}
  
	return PLUGIN_HANDLED
}

public automenu_handle(id, menu, item)
{
	if (item < 0) {
		return PLUGIN_CONTINUE
	}
 
	static command[16], name[64]
	new access, callback
	menu_item_getinfo(menu, item, access, command, 15, name, 63, callback)
 
	new choice = str_to_num(command)
	switch (choice) {
		case 1 : 
			make_player_command(id, "/hlx_auto start rank")
		case 2 : 
			make_player_command(id, "/hlx_auto end rank")
		case 3 : 
			make_player_command(id, "/hlx_auto kill rank")
		case 4 : 
			make_player_command(id, "/hlx_auto clear")
	}
  
	return PLUGIN_HANDLED
}

public eventsmenu_handle(id, menu, item)
{
	if (item < 0) {
		return PLUGIN_CONTINUE
	}
 
	static command[16], name[64]
	new access, callback
	menu_item_getinfo(menu, item, access, command, 15, name, 63, callback)
 
	new choice = str_to_num(command)
	switch (choice) {
		case 1 : 
			make_player_command(id, "/hlx_display 1")
		case 2 : 
			make_player_command(id, "/hlx_display 0")
		case 3 : 
			make_player_command(id, "/hlx_chat 1")
		case 4 : 
			make_player_command(id, "/hlx_chat 0")
	}
  
	return PLUGIN_HANDLED
}

stock is_command_blocked(command[192])
{
	new command_blocked = 0
	new command_index = 0
	while ((command_blocked == 0) && (command_index < sizeof(blocked_commands))) {
		if (strcmp(command, blocked_commands[command_index]) == 0) {
			command_blocked++
		}
		command_index++
	}
	if (command_blocked > 0) {
		return 1
	}
	return 0
}


public hlx_block_commands(client)
{
	if (client) {
		if (client == 0) {
			return PLUGIN_CONTINUE
		}
		
		new block_chat_commands = get_pcvar_num(g_hlx_block_commands)
		
		static user_command[192]
		read_args(user_command, 192)
		static origin_command[192]
		
		new start_index = 0
		new command_length = strlen(user_command)
		if (command_length > 0) {
			if (user_command[start_index] == 34)	{
				start_index = start_index + 1
				if (user_command[command_length - 1] == 34)	{
					user_command[command_length - 1] = 0
				}
			}
		
			if (user_command[start_index] == 47)	{
				start_index++
			}

			copy(origin_command, 192, user_command[start_index])
		}
		
		if (command_length > 0) {
			if (block_chat_commands > 0) {

				new command_type[32] = "say"
				new command_blocked = is_command_blocked(origin_command)
				
				if (command_blocked > 0) {

					if (is_user_connected(client)) {
						if ((strcmp("hlx_menu", user_command[start_index]) == 0) ||
							(strcmp("hlx", user_command[start_index]) == 0) ||
							(strcmp("hlstatsx", user_command[start_index]) == 0)) {

							display_mainmenu(client)
						}
						log_player_event(client, command_type, origin_command, 0)
					}
					return PLUGIN_HANDLED
				}
			} else {
				if (is_user_connected(client)) {
					if ((strcmp("hlx_menu", user_command[start_index]) == 0) ||
						(strcmp("hlx", user_command[start_index]) == 0) ||
						(strcmp("hlstatsx", user_command[start_index]) == 0)) {
						display_mainmenu(client)
					}
				}
				return PLUGIN_CONTINUE
			}
		}
	}
 
	return PLUGIN_CONTINUE
}


