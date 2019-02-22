<?php
/*
HLstatsX Community Edition - Real-time player and clan rankings and statistics
Copyleft (L) 2008-20XX Nicholas Hastings (nshastings@gmail.com)
http://www.hlxcommunity.com

HLstatsX Community Edition is a continuation of 
ELstatsNEO - Real-time player and clan rankings and statistics
Copyleft (L) 2008-20XX Malte Bayer (steam@neo-soft.org)
http://ovrsized.neo-soft.org/

ELstatsNEO is an very improved & enhanced - so called Ultra-Humongus Edition of HLstatsX
HLstatsX - Real-time player and clan rankings and statistics for Half-Life 2
http://www.hlstatsx.com/
Copyright (C) 2005-2007 Tobias Oetzel (Tobi@hlstatsx.com)

HLstatsX is an enhanced version of HLstats made by Simon Garner
HLstats - Real-time player and clan rankings and statistics for Half-Life
http://sourceforge.net/projects/hlstats/
Copyright (C) 2001  Simon Garner
            
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

For support and installation notes visit http://www.hlxcommunity.com
*/

if ( !defined('IN_HLSTATS') ) { die('Do not access this file directly.'); }
	if ($auth->userdata["acclevel"] < 80) die ("Access denied!");

	$edlist = new EditList("id", "hlstats_Actions", "game", false);
	$edlist->columns[] = new EditListColumn("game", "Game", 0, true, "hidden", $gamecode);
	$edlist->columns[] = new EditListColumn("code", "Action Code", 15, true, "text", "", 64);
	$edlist->columns[] = new EditListColumn("for_PlayerActions", "Player Action", 0, false, "checkbox");
	$edlist->columns[] = new EditListColumn("for_PlayerPlayerActions", "PlyrPlyr Action", 0, false, "checkbox");
	$edlist->columns[] = new EditListColumn("for_TeamActions", "Team Action", 0, false, "checkbox");
	$edlist->columns[] = new EditListColumn("for_WorldActions", "World Action", 0, false, "checkbox");
	$edlist->columns[] = new EditListColumn("reward_player", "Player Points Reward", 4, false, "text", "0");
	$edlist->columns[] = new EditListColumn("reward_team", "Team Points Reward", 4, false, "text", "0");
	$edlist->columns[] = new EditListColumn("team", "Team", 0, false, "select", "hlstats_Teams.name/code/game='$gamecode'");
	$edlist->columns[] = new EditListColumn("description", "Action Description", 23, true, "text", "", 128);
	
	
	if ($_POST)
	{
		if ($edlist->update())
			message("success", "Operation successful.");
		else
			message("warning", $edlist->error());
	}
	
?>

You can make an action map-specific by prepending the map name and an underscore to the Action Code. For example, if the map "<b>rock2</b>" has an action "<b>goalitem</b>" then you can either make the action code just "<b>goalitem</b>" (in which case it will match all maps) or you can make it "<b>rock2_goalitem</b>" to match only on the "rock2" map.<p>

<?php
	
	$result = $db->query("
		SELECT
			id,
			code,
			reward_player,
			reward_team,
			team,
			description,
			for_PlayerActions,
			for_PlayerPlayerActions,
			for_TeamActions,
			for_WorldActions
		FROM
			hlstats_Actions
		WHERE
			game='$gamecode'
		ORDER BY
			code ASC
	");
	
	$edlist->draw($result);
?>

<table width="75%" border=0 cellspacing=0 cellpadding=0>
<tr>
	<td align="center"><input type="submit" value="  Apply  " class="submit"></td>
</tr>
</table>

