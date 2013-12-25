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
	
	// Player Rankings
	$db->query("SELECT name FROM hlstats_Games WHERE code='$game'");
	if ($db->num_rows() < 1) error("No such game '$game'.");
	
	list($gamename) = $db->fetch_row();
	$db->free_result();
	
	$minkills = 0;

	$table = new Table(
		array(
			new TableColumn(
				"lastName",
				"Name",
				"width=40&flag=1&link=" . urlencode("mode=statsme&amp;player=%k")
			),
			new TableColumn(
				"ban_date",
				"BanDate",
				"width=25&align=right"
			),
			new TableColumn(
				"skill",
				"Points",
				"width=5&align=right"
			),
			new TableColumn(
				"kills",
				"Kills",
				"width=5&align=right"
			),
			new TableColumn(
				"deaths",
				"Deaths",
				"width=5&align=right"
			),
			new TableColumn(
				"headshots",
				"Headshots",
				"width=5&align=right"
			),
			new TableColumn(
				"hpk",
				"HS:K",
				"width=5&align=right"
			),
			new TableColumn(
				"kpd",
				"KPD",
				"width=5&align=right"
			),
		),
		"playerId",
		"last_event",
		"skill",
		true,
		25
	);
    
	$result = $db->query("
		SELECT
			FROM_UNIXTIME(last_event,'%Y.%m.%d %T') as ban_date,
			playerId,
			lastName,
			country,
			flag,
			skill,
			kills,
			deaths,
			IFNULL(kills/deaths, '-') AS kpd,
			headshots,
			IFNULL(headshots/kills, '-') AS hpk
		FROM
			hlstats_Players
		WHERE
			game='$game'
			AND hideranking=2
			AND kills >= $minkills
		ORDER BY
			$table->sort $table->sortorder,
			$table->sort2 $table->sortorder,
			lastName ASC
		LIMIT $table->startitem,$table->numperpage
	");
	
	$resultCount = $db->query("
		SELECT
			COUNT(*)
		FROM
			hlstats_Players
		WHERE
			game='$game'
			AND hideranking=2
			AND kills >= $minkills
	");
	
	list($numitems) = $db->fetch_row($resultCount);
	
	$table->draw($result, 25, 100);
?>
