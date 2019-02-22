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
	$tblServers = new Table
	(
		array
		(
			new TableColumn
			(
				'server',
				'Server',
				'width=26&align=left'
			),
			new TableColumn
			(
				'kills',
				'Kills',
				'width=6&align=right'
			),
			new TableColumn
			(
				'kpercent',
				'%',
				'width=5&sort=no&align=right&append=' . urlencode('%')
			),
			new TableColumn
			(
				'kpercent',
				'Ratio',
				'width=15&sort=no&type=bargraph'
			),
			new TableColumn
			(
				'deaths',
				'Deaths',
				'width=6&align=right'
			),
			new TableColumn(
				'kpd',
				'K:D',
				'width=5&align=right'
			),
			new TableColumn(
				'headshots',
				'Headshots',
				'width=6&align=right'
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
				'HS:K',
				'width=5&align=right'
			)
			
		),
		'server',
		'kills',
		'kills',
		true,
		9999,
		'server_page',
		'server_sort',
		'server_sortorder',
		'tabmaps',
		'desc',
		true
	);

	// leave the join on this one, we do groupings..
	$result = $db->query("
		SELECT
			hlstats_Servers.name AS server,
			SUM(hlstats_Events_Frags.killerId = $player) AS kills,
			SUM(hlstats_Events_Frags.victimId = $player) AS deaths,
			SUM(hlstats_Events_Frags.killerId = $player) / IF(SUM(hlstats_Events_Frags.victimId = $player) = 0, 1, SUM(hlstats_Events_Frags.victimId = $player)) AS kpd,
			ROUND(SUM(hlstats_Events_Frags.killerId = $player) / $realkills * 100, 2) AS kpercent,
			ROUND(SUM(hlstats_Events_Frags.victimId = $player) / $realdeaths * 100, 2) AS dpercent,
			SUM(hlstats_Events_Frags.killerId = $player AND hlstats_Events_Frags.headshot = 1) AS headshots,
			IFNULL(SUM(hlstats_Events_Frags.killerId = $player AND hlstats_Events_Frags.headshot = 1) / SUM(hlstats_Events_Frags.killerId = $player), '-') AS hpk,
			ROUND(SUM(hlstats_Events_Frags.killerId = $player AND hlstats_Events_Frags.headshot = 1) / $realheadshots * 100, 2) AS hpercent
		FROM
			hlstats_Events_Frags
		LEFT JOIN
			hlstats_Servers
		ON
			hlstats_Servers.serverId = hlstats_Events_Frags.serverId
		WHERE
			hlstats_Servers.game = '$game'
			AND hlstats_Events_Frags.killerId = '$player'
			OR hlstats_Events_Frags.victimId = '$player'
		GROUP BY
			hlstats_Servers.name
		ORDER BY
			$tblServers->sort $tblServers->sortorder,
			$tblServers->sort2 $tblServers->sortorder
	");
	$numitems = $db->num_rows($result);
	if ($numitems > 0)
	{
		printSectionTitle('Server Activity *');
		$tblServers->draw($result, $numitems, 95);
?>
	<br /><br />
<?php
	}
?>
