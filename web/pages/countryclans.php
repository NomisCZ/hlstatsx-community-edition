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
// Country Clan Rankings
	$db->query
	("
		SELECT
			hlstats_Games.name
		FROM
			hlstats_Games
		WHERE
			hlstats_Games.code = '$game'
	");
	if ($db->num_rows() < 1) error("No such game '$game'.");
	list($gamename) = $db->fetch_row();
	$db->free_result();
	if (isset($_GET['minmembers']))
	{
		$minmembers = valid_request(intval($_GET["minmembers"]),1);
	}
	else
	{
		$minmembers = 3;
	}
	pageHeader
	(
		array ($gamename, 'Country Rankings'),
		array ($gamename=>"%s?game=$game", 'Country Rankings' => '')
	);
	$table = new Table
	(
		array
		(
			new TableColumn
			(
				'name',
				'Country',
				'width=40&flag=1&link=' . urlencode('mode=countryclansinfo&amp;flag=%k&amp;game='.$game)
			),
			new TableColumn
			(
				'skill',
				'Avg. Points',
				'width=8&skill_change=1&align=right'
			),
			new TableColumn
			(
				"nummembers",
				"Members",
				"width=5&align=right"
			),
			new TableColumn
			(
				'activity',
				'Activity',
				'width=8&type=bargraph'
			),
			new TableColumn
			(
				'connection_time',
				'Connection Time',
				'width=13&align=right&type=timestamp'
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
				'width=7&align=right'
			)
		),
		'flag',
		'skill',
		'kpd',
		true
	);
	$result = $db->query
	("
		SELECT
			hlstats_Countries.flag,
			hlstats_Countries.name,
			COUNT(hlstats_Players.playerId) AS nummembers,
			SUM(hlstats_Players.kills) AS kills,
			SUM(hlstats_Players.deaths) AS deaths,
			SUM(hlstats_Players.connection_time) AS connection_time,
			ROUND(AVG(hlstats_Players.skill)) AS skill,
			ROUND(AVG(hlstats_Players.last_skill_change)) AS last_skill_change,
			ROUND(SUM(hlstats_Players.kills) / IF(SUM(hlstats_Players.deaths) = 0, 1, SUM(hlstats_Players.deaths)), 2) AS kpd,
			TRUNCATE(AVG(activity), 2) AS activity
		FROM
			hlstats_Countries 
		LEFT JOIN
			hlstats_Players
		ON
			hlstats_Players.flag = hlstats_Countries.flag
		WHERE
			hlstats_Players.game = '$game'
			AND hlstats_Players.hideranking = 0
			AND IF(".$g_options['MinActivity']." > (UNIX_TIMESTAMP() - hlstats_Players.last_event), ((100 / ".$g_options['MinActivity'].") * (".$g_options['MinActivity']." - (UNIX_TIMESTAMP() - hlstats_Players.last_event))), -1) >= 0
		GROUP BY
			hlstats_Countries.flag
		HAVING
			activity >= 0
			AND nummembers >= $minmembers
		ORDER BY
			$table->sort $table->sortorder,
			$table->sort2 $table->sortorder,
			hlstats_Countries.name ASC
		LIMIT
			$table->startitem,
			$table->numperpage
	");
	$resultCount = $db->query
	("
		SELECT
			hlstats_Countries.flag,
			SUM(activity) AS activity
		FROM
			hlstats_Countries
		LEFT JOIN
			hlstats_Players
		ON
			hlstats_Players.flag = hlstats_Countries.flag
		WHERE
			hlstats_Players.game = '$game'
			AND hlstats_Players.hideranking = 0
		GROUP BY
			hlstats_Countries.flag
		HAVING
			activity >= 0
			AND COUNT(hlstats_Players.playerId) >= $minmembers
	");
?>

<div class="block">
<?php
	printSectionTitle('Country Rankings');
	$table->draw($result, $db->num_rows($resultCount), 95);
?><br /><br />
	<div class="subblock">
		<div style="float:left;">
			<form method="get" action="<?php echo $g_options['scripturl']; ?>">
<?php
	$db->query
	("
		SELECT
			COUNT(DISTINCT flag) AS total_countrys
		FROM
			hlstats_Players
		WHERE
			hlstats_Players.flag NOT LIKE ''
			AND hlstats_Players.game = '$game'
			AND hlstats_Players.hideranking = 0
	");
	
	list($total_countrys) = $db->fetch_row();
	
	foreach ($_GET as $k=>$v)
	{
	$v = valid_request($v, 0); 
		if ($k != 'minmembers')
		{
			echo "<input type=\"hidden\" name=\"" . htmlspecialchars($k) . "\" value=\"" . htmlspecialchars($v) . "\" />\n";
		}
	}
?>
				<strong>&#8226;</strong> Show only clans with
					<input type="text" name="minmembers" size="4" maxlength="2" value="<?php echo $minmembers; ?>" class="textbox" /> or more members from a total of <b><?php echo number_format($total_countrys); ?></b> countrys
					<input type="submit" value="Apply" class="smallsubmit" />
			</form>
		</div>
		<div style="float:right;">
			Go to: <a href="<?php echo $g_options['scripturl'] . "?game=$game"; ?>"><?php echo $gamename; ?></a>
		</div>
		<div style="clear:both;"></div>
	</div>
</div>
