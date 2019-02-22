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
	
	// Weapon Details
	
	$weapon = valid_request($_GET['weapon'], 0)
		or error('No weapon ID specified.');
	
	$db->query("
		SELECT
			name
		FROM
			hlstats_Weapons
		WHERE
			code='$weapon'
			AND game='$game'
	");
	
	if ($db->num_rows() != 1)
	{
		$wep_name = ucfirst($weapon);
	}
	else
	{
		$weapondata = $db->fetch_array();
		$db->free_result();
		$wep_name = $weapondata['name'];
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
		array($gamename, 'Weapon Details', htmlspecialchars($wep_name)),
		array(
			$gamename=>$g_options['scripturl']."?game=$game",
			'Weapon Statistics' => $g_options['scripturl']."?mode=weapons&game=$game",
			'Weapon Details' => ''
		),
		$wep_name
	);

	$table = new Table(
		array(
			new TableColumn(
				'killerName',
				'Player',
				'width=60&align=left&flag=1&link=' . urlencode('mode=playerinfo&amp;player=%k') 
			),
			new TableColumn(
				'frags',
				ucfirst($weapon) . ' kills',
				'width=15&align=right'
			),
			new TableColumn(
				'headshots',
				'Headshots',
				'width=15&align=right'
			),
			new TableColumn(
				'hpk',
				'Hpk',
				'width=5&align=right'
			),
		),
		'killerId', // keycol
		'frags', // sort_default
		'killerName', // sort_default2
		true, // showranking
		50 // numperpage
	);
	
	$result = $db->query("
		SELECT
			hlstats_Events_Frags.killerId,
			hlstats_Players.lastName AS killerName,
			hlstats_Players.flag as flag,
			COUNT(hlstats_Events_Frags.weapon) AS frags,
			SUM(hlstats_Events_Frags.headshot=1) as headshots,
			IFNULL(SUM(hlstats_Events_Frags.headshot=1) / Count(hlstats_Events_Frags.weapon), '-') AS hpk
		FROM
			hlstats_Events_Frags,
			hlstats_Players
		WHERE
			hlstats_Players.playerId = hlstats_Events_Frags.killerId
			AND hlstats_Events_Frags.weapon='$weapon'
			AND hlstats_Players.game='$game'
			AND hlstats_Players.hideranking = 0
		GROUP BY
			hlstats_Events_Frags.killerId
		ORDER BY
			$table->sort $table->sortorder,
			$table->sort2 $table->sortorder
		LIMIT $table->startitem,$table->numperpage
	");
	
	$resultCount = $db->query("
		SELECT
			COUNT(DISTINCT hlstats_Events_Frags.killerId),
			SUM(hlstats_Events_Frags.weapon='$weapon'),
			SUM(hlstats_Events_Frags.weapon='$weapon' AND hlstats_Events_Frags.headshot=1)
		FROM
			hlstats_Events_Frags,
			hlstats_Servers
		WHERE
			hlstats_Servers.serverId = hlstats_Events_Frags.serverId
			AND hlstats_Events_Frags.weapon='$weapon'
			AND hlstats_Servers.game='$game'
	");
	
	list($numitems, $totalkills, $totalheadshots) = $db->fetch_row($resultCount);
?>

<div class="block">
	<?php printSectionTitle('Weapon Details'); ?>
    <div class="subblock">
	<?php // figure out URL and absolute path of image
		$image = getImage("/games/$game/weapons/$weapon");
		if ($image)
		{
			$wep_content = '<img src="' . $image['url'] . "\"  alt=\"$weapon\" />";   
		}
		else
		{
			$wep_content = "<strong>$wep_name</strong>: ";
		}
?>
		<div style="float:left;">
			<?php echo $wep_content ?>&nbsp;From a total of <b><?php echo number_format(intval($totalkills)); ?></b> kills with <b><?php echo number_format($totalheadshots); ?></b> headshots (Last <?php echo $g_options['DeleteDays']; ?> Days)
		</div>
		<div style="float:right;">
			Back to <a href="<?php echo $g_options['scripturl']. "?mode=weapons&amp;game=$game"; ?>">Weapon Statistics</a>
		</div>
		<div style="clear:both;padding:2px;"></div>
	</div>
	<br /><br />
	<?php $table->draw($result, $numitems, 95, 'center'); ?>
</div>
