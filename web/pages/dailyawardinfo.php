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
	
	
	// Daily Award Statistics

	$award = valid_request($_GET['award'], true)
		or error('No award ID specified.');

	$db->query("
		SELECT
			awardType,
			code,
			name,
			verb
		FROM
			hlstats_Awards
		WHERE
			hlstats_Awards.awardid=$award
	");
	
	$awarddata = $db->fetch_array();
	$db->free_result();
	$awardname = $awarddata['name'];
	$awardverb = $awarddata['verb'];
	$awardtype = $awarddata['awardType'];
	$awardcode = $awarddata['code'];
	
	$db->query("SELECT name FROM hlstats_Games WHERE code='$game'");
	if ($db->num_rows() < 1)
	{
		error("No such game '$game'.");
	}
	
	list($gamename) = $db->fetch_row();
	$db->free_result();
	
	pageHeader(
		array($gamename, 'Award Details', $awardname),
		array(
			$gamename=>$g_options['scripturl'] . "?game=$game",
			'Awards Statistics' => $g_options['scripturl'] . "?mode=awards&game=$game",
			'Awards Details' => ''
		),
		$awardname
	);

	$table = new Table(
		array(
			new TableColumn(
				'awardTime',
				'Day',
				'width=20&align=left'
			),
			new TableColumn(
				'lastName',
				'Player',
				'width=40&align=left&flag=1&link=' . urlencode('mode=playerinfo&amp;player=%k') 
			),
			new TableColumn(
				'count',
				'Count for the Day',
				'width=35&align=right&append=' . urlencode(" $awardverb")
			)
		),
		'playerId',
		'awardTime',
		'lastName',
		true,
		30
	);


	$result = $db->query("
		SELECT
			hlstats_Players_Awards.playerId,
			awardTime,
			lastName,
			flag,
			count
		FROM
			hlstats_Players_Awards
		LEFT JOIN
			hlstats_Players
		ON
			hlstats_Players_Awards.playerId = hlstats_Players.playerId
		WHERE
			awardid=$award
		ORDER BY
			$table->sort $table->sortorder,
			$table->sort2 $table->sortorder
		LIMIT $table->startitem,$table->numperpage
	");


	$resultCount = $db->query("
		SELECT
			awardTime
		FROM
			hlstats_Players_Awards
		WHERE
			awardid=$award	
	");

	$numitems = mysql_num_rows($resultCount);

?>

<div class="block">
	<?php printSectionTitle('Daily Award Details'); ?>
	<div class="subblock">
		<div style="float:right;">
			Back to <a href="<?php echo $g_options['scripturl'] . "?mode=awards&amp;game=$game"; ?>">Daily Awards</a>
		</div>
		<div style="clear:both;"></div>
	</div>
	<br /><br />
	<?php
	$img = IMAGE_PATH."/games/$game/dawards/".strtolower($awardtype).'_'.strtolower($awardcode).'.png';
	if (!is_file($img))
	{
		$img = IMAGE_PATH.'/award.png';
	}
	echo "<img src=\"$img\" alt=\"$awardcode\" /> <strong>$awardname</strong>";
	$table->draw($result, $numitems, 95, 'center');
?>
</div>