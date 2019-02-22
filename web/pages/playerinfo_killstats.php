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
// Player Kill Statistics
	flush();
	$tblPlayerKillStats = new Table
	(
		array
		(
			new TableColumn
			(
				'name',
				'Victim',
				'width=21&flag=1&link=' . urlencode('mode=playerinfo&amp;player=%k')
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
				'width=7&sort=no&align=right&append=' . urlencode('%')
			),
			new TableColumn
			(
				'kpercent',
				'Ratio',
				'width=7&sort=no&type=bargraph'
			),
			new TableColumn
			(
				'deaths',
				'Deaths',
				'width=6&align=right'
			),
			new TableColumn
			(
				'dpercent',
				'%',
				'width=7&sort=no&align=right&append=' . urlencode('%')
			),
			new TableColumn
			(
				'dpercent',
				'Ratio',
				'width=7&sort=no&type=bargraph'
			),
			new TableColumn
			(
				'kpd',
				'K:D',
				'width=7&align=right'
			),
			new TableColumn
			(
				'headshots',
				'Headshots',
				'width=6&align=right'
			),
			new TableColumn
			(
				'hpercent',
				'%',
				'width=7&sort=no&align=right&append=' . urlencode('%')
			),
			new TableColumn
			(
				'hpercent',
				'Ratio',
				'width=7&sort=no&type=bargraph'
			),
			new TableColumn
			(
				'hpk',
				'HS:K',
				'width=7&align=right'
			)
			
		),
		'victimId',
		'kills',
		'deaths',
		true,
		50,
		'playerkills_page',
		'playerkills_sort',
		'playerkills_sortorder',
		'tabkills',
		'desc',
		true
	);
// This would be better done with a UNION query, I think, but MySQL doesn't
// support them yet. (NOTE you need MySQL 3.23 for temporary table support.)
	$db->query
	("
		DROP TABLE IF EXISTS
			hlstats_Frags_Kills
	");
	$db->query
	("
		CREATE TEMPORARY TABLE
			hlstats_Frags_Kills
			(
				playerId INT(10),
				kills INT(10),
				deaths INT(10),
				headshot INT(10),
				country varchar(128),
				flag char(2)
			)
	");
	$db->query
	("
		INSERT INTO
			hlstats_Frags_Kills
			(
				playerId,
				kills,
				headshot
			)
		SELECT
			hlstats_Events_Frags.victimId,
			hlstats_Events_Frags.killerId,
			hlstats_Events_Frags.headshot
		FROM
			hlstats_Events_Frags
		WHERE
			hlstats_Events_Frags.killerId = $player
		GROUP BY
			hlstats_Events_Frags.id
	");
	$db->query
	("
		INSERT INTO
			hlstats_Frags_Kills
			(
				playerId,
				deaths
			)
		SELECT
			hlstats_Events_Frags.killerId,
			hlstats_Events_Frags.victimId
		FROM
			hlstats_Events_Frags
		WHERE
			hlstats_Events_Frags.victimId = $player
	");
	$result = $db->query
	("
			SELECT
				SUM(hlstats_Frags_Kills.headshot) AS headshots
			FROM
				hlstats_Frags_Kills
			GROUP BY
				hlstats_Frags_Kills.playerId
			HAVING
				COUNT(hlstats_Frags_Kills.kills) >= $killLimit
	");
	$realheadshots = 0;
	while ($rowdata = $db->fetch_array($result))
	{
		$realheadshots += $rowdata['headshots'];
	}	
	$db->query
	("
		SELECT
			hlstats_Players.lastName AS name
		FROM
			hlstats_Frags_Kills,
			hlstats_Players
		WHERE
			hlstats_Frags_Kills.playerId = hlstats_Players.playerId
		GROUP BY
			hlstats_Frags_Kills.playerId
		HAVING
			COUNT(hlstats_Frags_Kills.kills) >= $killLimit
	");
	$numitems = $db->num_rows();
	$result = $db->query
	("
		SELECT
			hlstats_Players.lastName AS name,
			hlstats_Players.flag AS flag,
			hlstats_Players.country AS country,
			COUNT(hlstats_Frags_Kills.kills) AS kills,
			COUNT(hlstats_Frags_Kills.deaths) AS deaths,
			ROUND(COUNT(hlstats_Frags_Kills.kills) / $realkills * 100, 2) AS kpercent,
			ROUND(COUNT(hlstats_Frags_Kills.deaths) / $realdeaths * 100, 2) AS dpercent,
			hlstats_Frags_Kills.playerId AS victimId,
			ROUND(COUNT(hlstats_Frags_Kills.kills) / IF(COUNT(hlstats_Frags_Kills.deaths) = 0, 1, COUNT(hlstats_Frags_Kills.deaths)), 2) AS kpd,
			SUM(hlstats_Frags_Kills.headshot = 1) AS headshots,
			ROUND(SUM(hlstats_Frags_Kills.headshot = 1) / IF(COUNT(hlstats_Frags_Kills.kills) = 0, 1, COUNT(hlstats_Frags_Kills.kills)), 2) AS hpk,
			ROUND(SUM(hlstats_Frags_Kills.headshot = 1) / $realheadshots * 100, 2) AS hpercent
		FROM
			hlstats_Frags_Kills,
			hlstats_Players
		WHERE
			hlstats_Frags_Kills.playerId = hlstats_Players.playerId
		GROUP BY
			hlstats_Frags_Kills.playerId
		HAVING
			Count(hlstats_Frags_Kills.kills) >= $killLimit
		ORDER BY
			$tblPlayerKillStats->sort $tblPlayerKillStats->sortorder,
			$tblPlayerKillStats->sort2 $tblPlayerKillStats->sortorder
		LIMIT $tblPlayerKillStats->startitem,$tblPlayerKillStats->numperpage
	");
	if ($numitems > 0)
	{
		printSectionTitle('Player Kill Statistics *');
		$tblPlayerKillStats->draw($result, $numitems, 95); ?>
	<br /><br />
	<div class="subblock">
	<form method="get" action="<?php echo $g_options['scripturl']; ?>">
		<strong>&#8226;</strong> Show only victims this person has killed
		<select name="killLimit" onchange="Tabs.refreshTab({'killLimit': this.options[this.selectedIndex].value, 'playerkills_page': 1})">
			<?php
				for($j = 0; $j < 16; $j++)
				{
					echo "<option value=\"$j\"";
					if ($killLimit == $j)
					{
						echo ' selected="selected"';
					}
					echo ">$j</option>";
				}
			?>
		</select>
		or more times
	</form>
	</div>
	<br /><br />
<?php
	}
?>
