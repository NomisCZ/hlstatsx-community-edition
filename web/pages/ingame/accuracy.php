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
	
	
	// Player Details
	
	$player = valid_request(intval($_GET['player']), 1);
	$uniqueid  = valid_request(strval($_GET['uniqueid']), 0);
	$game = valid_request(strval($_GET['game']), 0);
	
	if (!$player && $uniqueid)
	{
		if (!$game)
		{
			header('Location: ' . $g_options['scripturl'] . "&mode=search&st=uniqueid&q=$uniqueid");
			exit;
		}
		
		$db->query("
			SELECT
				playerId
			FROM
				hlstats_PlayerUniqueIds
			WHERE
				uniqueId='$uniqueid'
				AND game='$game'
		");
		
		if ($db->num_rows() > 1)
		{
			header('Location: ' . $g_options['scripturl'] . "&mode=search&st=uniqueid&q=$uniqueid&game=$game");
			exit;
		}
		elseif ($db->num_rows() < 1)
		{
			error("No players found matching uniqueId '$uniqueid'");
		}
		else
		{
			list($player) = $db->fetch_row();
			$player = intval($player);
		}
	}
	elseif (!$player && !$uniqueid)
	{
		error('No player ID specified.');
	}
	
	$db->query("
		SELECT
			hlstats_Players.playerId,
			hlstats_Players.lastName,
			hlstats_Players.game
		FROM
			hlstats_Players
		WHERE
			playerId='$player'
	");
	if ($db->num_rows() != 1)
		error("No such player '$player'.");
	
	$playerdata = $db->fetch_array();
	$db->free_result();
	
	$pl_name = $playerdata['lastName'];
	if (strlen($pl_name) > 10)
	{
		$pl_shortname = substr($pl_name, 0, 8) . "...";
	}
	else
	{
		$pl_shortname = $pl_name;
	}
	$pl_name = htmlspecialchars($pl_name, ENT_COMPAT);
	$pl_shortname = htmlspecialchars($pl_shortname, ENT_COMPAT);
	$pl_urlname = urlencode($playerdata['lastName']);
	
	
	$game = $playerdata['game'];
	$db->query("SELECT name FROM hlstats_Games WHERE code='$game'");
	if ($db->num_rows() != 1)
		$gamename = ucfirst($game);
	else
		list($gamename) = $db->fetch_row();
	
	$tblWeaponstats = new Table(
		array(
			new TableColumn(
				'smweapon',
				'Weapon',
				'width=10&type=weaponimg&align=center&link=' . urlencode("mode=weaponinfo&weapon=%k&game=$game")
			),
			new TableColumn(
				'smshots',
				'Shots',
				'width=10&align=right'
			),
			new TableColumn(
				'smhits',
				'Hits',
				'width=10&align=right'
			),
			new TableColumn(
				'smdamage',
				'Damage',
				'width=10&align=right'
			),
			new TableColumn(
				'smheadshots',
				'Headshots',
				'width=9&align=right'
			),
			new TableColumn(
				'smkills',
				'Kills',
				'width=9&align=right'
			),
			new TableColumn(
				'smaccuracy',
				'Accuracy',
				'width=9&align=right&append=' . urlencode('%')
			),
			new TableColumn(
				'smdhr',
				'Damage Per Hit',
				'width=14&align=right'
			),
			new TableColumn(
				'smspk',
				'Shots Per Kill',
				'width=14&align=right'
			)
		),
		'smweapon',
		'smkdr',
		'smweapon',
		true,
		9999,
		'weap_page',
		'weap_sort',
		'weap_sortorder',
		'weaponstats'
	);
	
	$result = $db->query("
        SELECT
            hlstats_Events_Statsme.weapon AS smweapon,
            SUM(hlstats_Events_Statsme.kills) AS smkills,
            SUM(hlstats_Events_Statsme.hits) AS smhits,
            SUM(hlstats_Events_Statsme.shots) AS smshots,
            SUM(hlstats_Events_Statsme.headshots) AS smheadshots,
            SUM(hlstats_Events_Statsme.deaths) AS smdeaths,
            SUM(hlstats_Events_Statsme.damage) AS smdamage,
            ROUND((SUM(hlstats_Events_Statsme.damage) / (IF( SUM(hlstats_Events_Statsme.hits)=0, 1, SUM(hlstats_Events_Statsme.hits) ))), 1) as smdhr,
            SUM(hlstats_Events_Statsme.kills) / IF((SUM(hlstats_Events_Statsme.deaths)=0), 1, (SUM(hlstats_Events_Statsme.deaths))) as smkdr,
            ROUND((SUM(hlstats_Events_Statsme.hits) / SUM(hlstats_Events_Statsme.shots) * 100), 1) as smaccuracy,
            ROUND(( (IF(SUM(hlstats_Events_Statsme.kills)=0, 0, SUM(hlstats_Events_Statsme.shots))) / (IF( SUM(hlstats_Events_Statsme.kills)=0, 1, SUM(hlstats_Events_Statsme.kills) ))), 1) as smspk
        FROM
            hlstats_Events_Statsme
        LEFT JOIN hlstats_Servers ON
            hlstats_Servers.serverId=hlstats_Events_Statsme.serverId
        WHERE
            hlstats_Servers.game='$game' AND hlstats_Events_Statsme.PlayerId=$player
        GROUP BY
            hlstats_Events_Statsme.weapon
        HAVING
            SUM(hlstats_Events_Statsme.shots)>0
        ORDER BY
            $tblWeaponstats->sort $tblWeaponstats->sortorder,
            $tblWeaponstats->sort2 $tblWeaponstats->sortorder
	");	

if ($db->num_rows($result) != 0)
{
	$tblWeaponstats->draw($result, $db->num_rows($result), 100);
}

?>
