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
	
	// Roles Details
	
	$role = valid_request($_GET['role'], 0)
		or error('No role ID specified.');
	
	$db->query("
		SELECT
			hlstats_Roles.name,
			hlstats_Roles.code
		FROM
			hlstats_Roles
		WHERE
			hlstats_Roles.code='$role'
			AND hlstats_Roles.game='$game'
	");
	
	if ($db->num_rows() != 1)
	{
		$role_name = ucfirst($role);
		$role_code = ucfirst($role);
	}
	else
	{
		$roledata = $db->fetch_array();
		$db->free_result();
		$role_name = $roledata['name'];
		$role_code = $roledata['code'];
	}

	$db->query("SELECT name FROM hlstats_Games WHERE code='$game'");
	if ($db->num_rows() != 1)
		error('Invalid or no game specified.');
	else
		list($gamename) = $db->fetch_row();
		
	pageHeader(
		array($gamename, 'Roles Details', htmlspecialchars($role_name)),
		array(
			$gamename => $g_options['scripturl']."?game=$game",
			'Roles Statistics' => $g_options['scripturl']."?mode=roles&game=$game",
			'Role Details' => ''
		),
		$role_name
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
				ucfirst($role_name) . ' kills',
				'width=35&align=right'
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
			COUNT(hlstats_Events_Frags.killerRole) AS frags
		FROM
			hlstats_Events_Frags,
			hlstats_Players
		WHERE
			hlstats_Players.playerId = hlstats_Events_Frags.killerId
			AND hlstats_Events_Frags.killerRole='$role'
			AND hlstats_Players.game='$game'
			AND hlstats_Players.hideranking<>'1'
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
			SUM(hlstats_Events_Frags.killerRole='$role'),
			SUM(hlstats_Events_Frags.killerRole='$role' AND hlstats_Events_Frags.headshot=1)
		FROM
			hlstats_Events_Frags,
			hlstats_Servers
		WHERE
			hlstats_Servers.serverId = hlstats_Events_Frags.serverId
			AND hlstats_Events_Frags.killerRole='$role'
			AND hlstats_Servers.game='$game'
	");
	
	list($numitems, $totalkills, $totalheadshots) = $db->fetch_row($resultCount);
?>

<div class="block">
	<?php printSectionTitle('Role Details'); ?>
	<div class="subblock">
<?php // figure out URL and absolute path of image

    $wep_content = "<strong>".htmlspecialchars($role_name)."</strong>: ";
    $image = getImage("/games/$game/roles/$role");
    if ($image)
    {
		$wep_content .= '<img src="' . str_replace('#','%23',$image['url']) ."\" alt=\"".htmlspecialchars($role_name)."\" />";   
    }
?>
		<div style="float:left;">
			<?php echo $wep_content ?>
			&nbsp;From a total of <b><?php echo number_format(intval($totalkills)); ?></b> kills as <?php 
				echo htmlspecialchars($role_name);
				if($totalheadshots > 0 || $role=='sniper')
				{
					echo ' with <b>' . number_format($totalheadshots) . '</b> headshots ';
				}
				?> (Last <?php echo $g_options['DeleteDays']; ?> Days)
		</div>
		<div style="float:right;">
			Back to <a href="<?php echo $g_options['scripturl'] . "?mode=roles&amp;game=$game"; ?>">Roles Statistics</a>
		</div>
		<div style="clear:both;padding:2px;"></div>
	</div>
</div>
<?php $table->draw($result, $numitems, 95, 'center');
?>

</td></tr>
</table>