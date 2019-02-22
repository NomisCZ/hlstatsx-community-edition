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
// Player Awards History 
	$player = valid_request($_GET['player'], 1)
		or error("No player ID specified.");
	$db->query
	("
		SELECT
			hlstats_Players.lastName,
			hlstats_Players.game
		FROM
			hlstats_Players
		WHERE
			hlstats_Players.playerId = $player
	");
	if ($db->num_rows() != 1)
	{
		error("No such player '$player'.");
	}
	$playerdata = $db->fetch_array();
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
	$game = $playerdata['game'];
	$db->query
	("
		SELECT
			hlstats_Games.name
		FROM
			hlstats_Games
		WHERE
			hlstats_Games.code = '$game'
	");
	if ($db->num_rows() != 1)
	{
		$gamename = ucfirst($game);
	}
	else
	{
		list($gamename) = $db->fetch_row();
	}	
	pageHeader
	(
		array ($gamename, 'Awards History', $pl_name),
		array
		(
			$gamename=>$g_options['scripturl'] . "?game=$game",
			'Player Rankings'=>$g_options['scripturl'] . "?mode=players&game=$game",
			'Player Details'=>$g_options['scripturl'] . "?mode=playerinfo&player=$player",
			'Awards History'=>''
		),
		$playername
	);
	flush();
	$cnttext = 'Earned';
	$lnktext = '&link='.urlencode("mode=playerawards&player=".$player."&amp;awardId=%k");
	if (isset($_GET['awardId']))
	{
		$awardId = valid_request($_GET['awardId'], true) or error("No clan ID specified."); 
	}

	$cnttext = 'Kills on Day';
	$lnktext = '';

	$table = new Table
	(
		array
		(
			new TableColumn
			(
				'awardTime',
				(isset($awardId))?'Date':'Date Last Earned',
				'width=17'
			),
			new TableColumn(
				'name',
				'Name',
				'width=23'
			),
			new TableColumn(
				'verb',
				'Description',
				'width=50'.$lnktext
			),
			new TableColumn(
				'count',
				$cnttext,
				'width=10&align=right'
			)
		),
		'awardId',
		'awardTime',
		'name',
		false,
		50,
		'page',
		'sort',
		'sortorder'
	);
	$surl = $g_options['scripturl'];
	if (isset($awardId))
	{
		$result = $db->query
		("
			SELECT
				hlstats_Players_Awards.awardTime,
                hlstats_Awards.Name,
				hlstats_Awards.verb,
				hlstats_Players_Awards.count,
				hlstats_Awards.awardId
			FROM
				hlstats_Players_Awards
			INNER JOIN
				hlstats_Awards
			ON
				hlstats_Awards.awardId = hlstats_Players_Awards.awardId
			WHERE
				hlstats_Players_Awards.playerId = $player
				AND hlstats_Players_Awards.awardId = $awardId
			ORDER BY
				$table->sort $table->sortorder,
				$table->sort2 $table->sortorder
			LIMIT
				$table->startitem,
				$table->numperpage
		");
		$resultCount = $db->query
		("
			SELECT
				COUNT(awardId)
			FROM
				hlstats_Players_Awards
			WHERE
				hlstats_Players_Awards.playerId = $player
				AND hlstats_Players_Awards.awardId = $awardId
		");
	}
	else
	{
		$result = $db->query
		("
			SELECT
				MAX(hlstats_Players_Awards.awardTime) AS awardTime,
				hlstats_Awards.name,
				hlstats_Awards.verb,
				COUNT(verb) AS count,
				hlstats_Awards.awardId
			FROM
				hlstats_Players_Awards
			INNER JOIN
				hlstats_Awards
			ON
				hlstats_Awards.awardId = hlstats_Players_Awards.awardId
			WHERE
				hlstats_Players_Awards.playerId = $player
			GROUP BY
				hlstats_Awards.name,
				hlstats_Awards.verb
			ORDER BY
				$table->sort $table->sortorder,
				$table->sort2 $table->sortorder
			LIMIT
				$table->startitem,$table->numperpage
		");
		$resultCount = $db->query
		("
			SELECT
				COUNT(awardId)
			FROM
				hlstats_Players_Awards
			WHERE
				hlstats_Players_Awards.playerId = $player
			GROUP BY
				hlstats_Players_Awards.awardId
		");
	}
	list($numitems) = $db->fetch_row($resultCount);
?>

<div class="block">
<?php
	printSectionTitle('Player Awards History');
	if ($numitems > 0)
	{
		$table->draw($result, $numitems, 95);
	}
?><br /><br />
	<div class="subblock">
		<div style="float:right;">
			<?php 
				$db->query
				("
					SELECT
						hlstats_Players.lastName
					FROM
						hlstats_Players
					WHERE
						hlstats_Players.playerId = $player
				");
				list($lastName) = $db->fetch_row();
			?>
			Go to: <a href="<?php echo $g_options['scripturl'] . "?mode=playerinfo&amp;player=$player"; ?>"><?php echo $lastName; ?>'s Statistics</a>
		</div>
	</div>
</div>
