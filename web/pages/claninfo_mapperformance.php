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

	if ( !defined('IN_HLSTATS') )
	{
		die('Do not access this file directly.');
	}

	flush();
	
	$tblMaps = new Table(
		array(
			new TableColumn(
				'map',
				'Map Name',
				'width=15&align=left&link=' . urlencode("mode=mapinfo&amp;map=%k&amp;game=$game")
			),
			new TableColumn(
				'kills',
				'Kills',
				'width=6&align=right'
			),
			new TableColumn(
				'kpercent',
				'Percentage of Kills',
				'width=15&sort=no&type=bargraph'
			),
			new TableColumn(
				'kpercent',
				'%',
				'width=5&sort=no&align=right&append=' . urlencode('%')
			),
			new TableColumn(
				'deaths',
				'Deaths',
				'width=6&align=right'
			),
			new TableColumn(
				'kpd',
				'Kills per Death',
				'width=13&align=right'
			),
			new TableColumn(
				'headshots',
				'Headshots',
				'width=9&align=right'
			),
			new TableColumn(
				'hpercent',
				'Percentage of Headshots',
				'width=16&sort=no&type=bargraph'
			),
			new TableColumn(
				'hpercent',
				'%',
				'width=5&sort=no&align=right&append=' . urlencode('%')
			),
			new TableColumn(
				'hpk',
				'Hpk',
				'width=5&align=right'
			)
			
		),
		'map',
		'kills',
		'kills',
		true,
		9999,
		'maps_page',
		'maps_sort',
		'maps_sortorder',
		'tabmaps',
		'desc',
		true
	);
	
	$db->query("
		CREATE TEMPORARY TABLE tmp_clan_kills
			SELECT
				IF(map='', '(Unaccounted)', map) AS map,
				COUNT(*) AS kills,
				SUM(headshot=1) AS headshots
			FROM
				hlstats_Events_Frags, hlstats_Players
			WHERE
				hlstats_Players.playerId = hlstats_Events_Frags.killerId
				AND hlstats_Players.clan = $clan
			GROUP BY
				map;
	");
	
	$db->query("
		CREATE TEMPORARY TABLE tmp_clan_deaths
			SELECT
				IF(map='', '(Unaccounted)', map) AS map,
				COUNT(*) AS deaths
			FROM
				hlstats_Events_Frags, hlstats_Players
			WHERE
				hlstats_Players.playerId = hlstats_Events_Frags.victimId
				AND hlstats_Players.clan = $clan
			GROUP BY
				map;
	");

	$result = $db->query("
		SELECT *, 
			IFNULL(kills/deaths, '-') AS kpd,
			IFNULL(headshots / kills, '-') AS hpk,
			ROUND(kills / $realkills * 100, 2) AS kpercent,
			ROUND(headshots / $realheadshots * 100, 2) AS hpercent
		FROM
			tmp_clan_kills, tmp_clan_deaths
		WHERE
			tmp_clan_kills.map = tmp_clan_deaths.map
		ORDER BY
			$tblMaps->sort $tblMaps->sortorder,
			$tblMaps->sort2 $tblMaps->sortorder
	");
	
	$numitems = $db->num_rows($result);
	if ($numitems > 0)
	{
?>
	<div style="clear:both;padding-top:20px;"></div>
<?php
	printSectionTitle('Map Performance *');
	$tblMaps->draw($result, $db->num_rows($result), 95);
?>
<br /><br />
<?php
	}
?>