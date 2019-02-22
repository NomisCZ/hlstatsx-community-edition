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
	$tblAliases = new Table
	(
		array
		(
			new TableColumn
			(
				'name',
				'Name',
				'width=21'
			),
			new TableColumn
			(
				'connection_time',
				'Time',
				'width=8&align=right&type=timestamp'
			),
			new TableColumn
			(
				'lastuse',
				'Last Use',
				'width=15'
			),
			new TableColumn
			(
				'kills',
				'Kills',
				'width=7&align=right'
			),
			new TableColumn
			(
				'deaths',
				'Deaths',
				'width=7&align=right'
			),
			new TableColumn
			(
				'kpd',
				'K:D',
				'width=11&align=right'
			),
			new TableColumn
			(
				'headshots',
				'Headshots',
				'width=8&align=right'
			),
			new TableColumn
			(
				'hpk',
				'HS:K',
				'width=6&align=right'
			),
			new TableColumn
			(
				'suicides',
				'Suicides',
				'width=6&align=right'
			),
			new TableColumn
			(
				'acc',
				'Accuracy',
				'width=6&align=right&append=' . urlencode('%')
			)
		),
		'name',
		'lastuse',
		'name',
		true,
		20,
		'aliases_page',
		'aliases_sort',
		'aliases_sortorder',
		'tabteams',
		'desc',
		true
	);
	$result = $db->query
	("
		SELECT
			hlstats_PlayerNames.name,
			hlstats_PlayerNames.connection_time,
			hlstats_PlayerNames.lastuse,
			hlstats_PlayerNames.numuses,
			hlstats_PlayerNames.kills,
			hlstats_PlayerNames.deaths,
			IFNULL(ROUND(hlstats_PlayerNames.kills / IF(hlstats_PlayerNames.deaths = 0, 1, hlstats_PlayerNames.deaths), 2), '-') AS kpd,
			hlstats_PlayerNames.headshots,
			IFNULL(ROUND(hlstats_PlayerNames.headshots / hlstats_PlayerNames.kills, 2), '-') AS hpk,
			hlstats_PlayerNames.suicides,
			IFNULL(ROUND(hlstats_PlayerNames.hits / hlstats_PlayerNames.shots * 100, 1), 0.0) AS acc
		FROM
			hlstats_PlayerNames
		WHERE
			hlstats_PlayerNames.playerId = $player
		ORDER BY
			$tblAliases->sort $tblAliases->sortorder
		LIMIT
			$tblAliases->startitem,
			$tblAliases->numperpage
	");
	$resultCount = $db->query
	("
		SELECT
			COUNT(*)
		FROM
			hlstats_PlayerNames
		WHERE
			hlstats_PlayerNames.playerId = $player
	");
	list($numitems) = $db->fetch_row($resultCount);
	if ($numitems > 1)
	{
?>

<div style="clear:both;padding-top:24px;"></div>
<?php
		printSectionTitle('Aliases');
		if ($numitems > 0)
		{
			$tblAliases->draw($result, $numitems, 95);
		}
?>
<br /><br />

<?php
	}
?>