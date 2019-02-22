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
	
	$edlist = new EditList("awardId", "hlstats_Awards", "award", false);
	$edlist->columns[] = new EditListColumn("game", "Game", 0, true, "hidden", $gamecode);
	$edlist->columns[] = new EditListColumn("awardType", "Type", 0, true, "hidden", "W");
	$edlist->columns[] = new EditListColumn("code", "Weapon", 0, true, "select", "hlstats_Weapons.name/code/game='$gamecode';latency/*Latency;mostkills/*Most Kills;bonuspoints/*Bonus Points;suicide/*Suicides;teamkills/*Team Kills;connectiontime/*Connection Time;killstreak/*Kill Streak;deathstreak/*Death Streak;allsentrykills/*All Sentry Kills (TF2)");
	$edlist->columns[] = new EditListColumn("name", "Award Name", 20, true, "text", "", 128);
	$edlist->columns[] = new EditListColumn("verb", "Verb Plural", 20, true, "text", "", 64);
	
	
	if ($_POST)
	{
		if ($edlist->update())
			message("success", "Operation successful.");
		else
			message("warning", $edlist->error());
	}
	
	
	$result = $db->query("
		SELECT
			awardId,
			code,
			name,
			verb
		FROM
			hlstats_Awards
		WHERE
			game='$gamecode'
			AND awardType='W'
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

