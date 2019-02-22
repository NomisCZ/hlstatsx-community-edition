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
	
	
	// Ribbon Statistics

	$ribbon =  valid_request($_GET['ribbon'], true)
		or error('No ribbon ID specified.');


	$db->query("
		SELECT
			ribbonName,
			image,
			awardCode,
			awardCount
		FROM
			hlstats_Ribbons
		WHERE
			hlstats_Ribbons.ribbonId=$ribbon
	");
	
	$actiondata = $db->fetch_array();
	$db->free_result();
	$act_name = $actiondata['ribbonName'];
	$awardmin = $actiondata['awardCount'];
	$awardcode = $actiondata['awardCode'];
	$image = $actiondata['image'];

	
	$db->query("SELECT name FROM hlstats_Games WHERE code='$game'");
	if ($db->num_rows() < 1)
	{
		error("No such game '$game'.");
	}
	
	list($gamename) = $db->fetch_row();
	$db->free_result();
	
	pageHeader(
		array($gamename, 'Ribbon Details', $act_name),
		array(
			$gamename => $g_options['scripturl']."?game=$game",
			'Ribbons' => $g_options['scripturl']."mode=awards&game=$game&tab=ribbons",
			'Ribbon Details' => ''
		),
		$act_name
	);


	$table = new Table(
		array(
			new TableColumn
			(
				'playerName',
				'Player',
				'width=45&align=left&flag=1&link=' . urlencode('mode=playerinfo&amp;player=%k')
			),
			new TableColumn
			(
				'numawards',
				'Daily awards',
				'width=10&align=right&append=' . urlencode(' times')
			),
			new TableColumn
			(
				'awardName',
				'',
				'width=40&align=left'
			)
		),
		'playerId',
		'numawards',
		'playerName',
		true,
		50
	);


	$result = $db->query("
		SELECT
			flag,
			lastName AS playerName,
			hlstats_Players.playerId,
			hlstats_Awards.name as awardName,
			COUNT(hlstats_Awards.name) AS numawards
		FROM
			hlstats_Players
		INNER JOIN
			hlstats_Players_Awards
			ON (
			    hlstats_Players_Awards.playerId=hlstats_Players.playerId AND
			    hlstats_Players_Awards.game=hlstats_Players.game			    
			    )
		INNER JOIN
			hlstats_Awards 
			ON (
			    hlstats_Players_Awards.awardId=hlstats_Awards.awardId AND
			    hlstats_Players_Awards.game=hlstats_Awards.game			    
			    )
		WHERE
			hlstats_Awards.code = '$awardcode' AND
			hlstats_Players.game = '$game' AND
			hlstats_Players.hideranking<>'1'
		GROUP BY
			flag,
			lastName,
			hlstats_Players.playerId
		HAVING
			COUNT(hlstats_Awards.name) >= $awardmin  	
		ORDER BY
			$table->sort $table->sortorder,
			$table->sort2 $table->sortorder
		LIMIT $table->startitem,$table->numperpage
	");


	$resultCount = $db->query("
		SELECT
			flag,
			lastName AS playerName,
			hlstats_Players.playerId,
			hlstats_Awards.name as awardName,
			COUNT(hlstats_Awards.name) AS numawards
		FROM
			hlstats_Players
		INNER JOIN
			hlstats_Players_Awards
			ON (
			    hlstats_Players_Awards.playerId=hlstats_Players.playerId AND
			    hlstats_Players_Awards.game=hlstats_Players.game			    
			    )
		INNER JOIN
			hlstats_Awards 
			ON (
			    hlstats_Players_Awards.awardId=hlstats_Awards.awardId AND
			    hlstats_Players_Awards.game=hlstats_Awards.game			    
			    )
		WHERE
			hlstats_Awards.code = '$awardcode' AND
			hlstats_Players.game = '$game' AND
			hlstats_Players.hideranking<>'1'
		GROUP BY
			flag,
			lastName,
			hlstats_Players.playerId
		HAVING
			COUNT(hlstats_Awards.name) >= $awardmin  	
	");
	$numitems = mysql_num_rows($resultCount);

?>

<div class="block">
	<?php printSectionTitle('Ribbon Details'); ?>
	<div class="subblock">
		<div style="float:right;">
			Back to <a href="<?php echo $g_options['scripturl'] . "?mode=awards&amp;game=$game&tab=ribbons"; ?>">Ribbons</a>
		</div>
		<div style="clear:both;"></div>
	</div>
	<br /><br />
<?php
  echo '<img src="'.IMAGE_PATH."/games/$game/ribbons/$image\" alt=\"\" /> <b>$act_name</b>";
	$table->draw($result, $numitems, 95, 'center');
?>
</div>