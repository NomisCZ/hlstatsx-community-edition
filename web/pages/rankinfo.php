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
	// Action Details

	
	$rank = valid_request($_GET['rank'], 1)
		or error('No rank ID specified.');
	
	$db->query("
		SELECT
			rankName
		FROM
			hlstats_Ranks
		WHERE
			rankId=$rank
	");
	
	if ($db->num_rows() != 1)
	{
		$act_name = ucfirst($action);
	}
	else
	{
		$actiondata = $db->fetch_array();
		$db->free_result();
		$act_name = $actiondata['description'];
	}
	
	
	$db->query("SELECT name FROM hlstats_Games WHERE code='$game'");
	if ($db->num_rows() != 1)
	{
		error('Invalid or no game specified.');
	}
	else
	{
		list($gamename) = $db->fetch_row();
	}
		
	pageHeader(
		array($gamename, 'Rank Details', $act_name),
		array(
			$gamename => $g_options['scripturl']."?game=$game",
			'Ranks' => $g_options['scripturl']."?mode=awards&game=$game&tab=ranks",
			'Rank Details'=>''
		),
		$act_name
	);
    
	$table = new Table(
		array(
			new TableColumn(
				'playerName',
				'Player',
				'width=45&align=left&flag=1&link=' . urlencode('mode=playerinfo&amp;player=%k') 
			),
			new TableColumn(
				'kills',
				'Kills',
				'width=25&align=right'
			),
			new TableColumn(
				'skill',
				'Skill',
				'width=25&align=right'
			)
		),
		'playerId',
		'skill',
		'playerName',
		true,
		50
	);

	
	$result = $db->query("
		SELECT
			skill,
			kills,
			flag,
			lastName AS playerName,
			playerId
		FROM
			hlstats_Players,
			hlstats_Ranks
		WHERE
			rankId=$rank AND
			kills>=minKills AND
			kills<=maxKills AND
			hlstats_Players.game = '$game' AND
			hlstats_Players.hideranking<>'1'
		ORDER BY
			$table->sort $table->sortorder,
			$table->sort2 $table->sortorder
		LIMIT $table->startitem,$table->numperpage
	");
	
	$resultCount = $db->query("
		SELECT
			count(playerId)
		FROM
			hlstats_Players,
			hlstats_Ranks
		WHERE
			rankId=$rank AND
			kills>=minKills AND
			kills<=maxKills AND
			hlstats_Players.game = '$game' AND
			hlstats_Players.hideranking<>'1'
	");

	list($numitems) = $db->fetch_array();

	$resultRank = $db->query("
		SELECT
			image,
			rankName
		FROM
			hlstats_Ranks
		WHERE
      rankId=$rank;");

	$rankrow = $db->fetch_array();
    
?>

<div class="block">
    <?php printSectionTitle('Rank Details'); ?>
	<div class="subblock">
		<div style="float:right;">
			Back to <a href="<?php echo $g_options['scripturl'] . "?mode=awards&amp;game=$game&tab=ranks"; ?>">Ranks</a>
		</div>
		<div style="clear:both;"></div>
	</div>
	<br /><br />
<?php
	$image = getImage('/ranks/'.$rankrow['image']);
	if ($image)
		echo '<img src="'.$image['url'].'" alt="" />';
	echo '<b>'.$rankrow['rankName'].'</b>';
	$table->draw($result, $numitems, 95, 'center');
?>
</div>