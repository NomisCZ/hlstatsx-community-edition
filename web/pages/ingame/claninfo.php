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

if ( !defined('IN_HLSTATS') ) { die('Do not access this file directly.'); }
	
	// Clan Details
	
	$clan = valid_request(intval($_GET['clan']),1)
		or error('No clan ID specified.');
	
	$db->query("
		SELECT
			hlstats_Clans.tag,
			hlstats_Clans.name,
			hlstats_Clans.homepage,
			hlstats_Clans.game,
			SUM(hlstats_Players.kills) AS kills,
			SUM(hlstats_Players.deaths) AS deaths,
			SUM(hlstats_Players.connection_time) AS connection_time,
			COUNT(hlstats_Players.playerId) AS nummembers,
			ROUND(AVG(hlstats_Players.skill)) AS avgskill,
			TRUNCATE(AVG(activity),2) as activity
		FROM
			hlstats_Clans
		LEFT JOIN hlstats_Players ON
			hlstats_Players.clan = hlstats_Clans.clanId
		WHERE
			hlstats_Clans.clanId=$clan
			AND hlstats_Players.hideranking = 0
		GROUP BY
			hlstats_Clans.clanId
	");
	if ($db->num_rows() != 1)
		error("No such clan '$clan'.");
	
	$clandata = $db->fetch_array();
	$db->free_result();
	
	$cl_name = preg_replace(' ', '&nbsp;', htmlspecialchars($clandata['name']));
	$cl_tag  = preg_replace(' ', '&nbsp;', htmlspecialchars($clandata['tag']));
	$cl_full = "$cl_tag $cl_name";
	
	$game = $clandata['game'];
	$db->query("SELECT name FROM hlstats_Games WHERE code='$game'");
	if ($db->num_rows() != 1)
		$gamename = ucfirst($game);
	else
		list($gamename) = $db->fetch_row();
	
?>

	<table class="data-table">
		<tr class="data-table-head">
			<td colspan="3" class="fSmall"><?php echo 'Clan Profile Stats Summary' ?></td>
        </tr>
		<tr class="bg1">
			<td class="fSmall"><?php echo 'Clan Name' ?>:</td>
            <td colspan="2" class="fSmall"><?php
				echo '<strong>'.$clandata['name'].'</strong>';
			?></td>
		</tr>
		<tr class="bg2">
			<td class="fSmall"><?php echo 'Home Page' ?>:</td>
			<td colspan="2" class="fSmall"><?php
				if ($url = getLink($clandata['homepage']))
				{
					echo $url;
				}
				else
				{
					echo '(Not specified.)';
				}
			?></td>
		</tr>				
		<tr class="bg1">
			<td class="fSmall"><?php echo 'Number Of Members' ?>:</td>
			<td colspan="2" class="fSmall"><?php
				echo $clandata['nummembers'];
			?></td>
		</tr>
		<tr class="bg2">
			<td class="fSmall"><?php echo 'Avg. Member Points' ?>:</td>
			<td colspan="2" class="fSmall"><?php
				echo number_format($clandata['avgskill']);
			?></td>
		</tr>
        <tr class="bg1">
			<td style="width:45%;" class="fSmall"><?php echo 'Activity' ?>:</td>
			<td style="width:40%;"><?php
				$width = sprintf('%d%%', $clandata['activity'] + 0.5);
				$bar_type = 1;
				if ($clandata['activity'] > 40)
					$bar_type = '6';
				elseif ($clandata['activity'] > 30)
					$bar_type = '5';
				elseif ($clandata['activity'] > 20)
					$bar_type = '4';
				elseif ($clandata['activity'] > 10)
					$bar_type = '3';
				elseif ($clandata['activity'] > 5)
					$bar_type = '2';
				echo "<img src=\"" . IMAGE_PATH . "/bar$bar_type.gif\" class=\"bargraph\" style=\"width:$width%\" alt=\"".$clandata['activity'].'%">';            
            ?></td>
			<td style="width:15%;" class="fSmall"><?php
				echo sprintf('%0.2f', $clandata['activity']).'%';
			?></td>
		</tr>
		<tr class="bg2">
			<td class="fSmall"><?php echo 'Total Kills' ?>:</td>
			<td colspan="2" class="fSmall"><?php
				echo number_format($clandata['kills']);
			?></td>
		</tr>
		<tr class="bg1">
			<td class="fSmall"><?php echo 'Total Deaths' ?>:</td>
			<td colspan="2" class="fSmall"><?php
				echo number_format($clandata['deaths']);
			?></td>
		</tr>
		<tr class="bg2">
			<td class="fSmall"><?php echo 'Kills per Death' ?>:</td>
			<td colspan="2" class="fSmall"><?php
				if ($clandata['deaths'] != 0)
				{
					printf('%0.2f', $clandata['kills'] / $clandata['deaths']);
				}
				else
				{
					echo '-';
				}
			?></td>
		</tr>
		<tr class="bg1">
			<td class="fSmall"><?php echo 'Total Connection Time' ?>:</td>
			<td colspan="2" class="fSmall"><?php
				echo timestamp_to_str($clandata['connection_time']);
			?></td>
		</tr>
	</table>
<?php
	flush();
	
	$tblMembers = new Table(
		array(
			new TableColumn(
				'lastName',
				'Name',
				'width=32&flag=1&link=' . urlencode('mode=statsme&amp;player=%k')
			),
			new TableColumn(
				'skill',
				'Points',
				'width=7&align=right'
			),
			new TableColumn(
				'activity',
				'Activity',
				'width=9&sort=no&type=bargraph'
			),
			new TableColumn(
				'connection_time',
				'Time',
				'width=14&align=right&type=timestamp'
			),
			new TableColumn(
				'kills',
				'Kills',
				'width=7&align=right'
			),
			new TableColumn(
				'percent',
				'Clan Kills',
				'width=5&sort=no&type=bargraph'
			),
			new TableColumn(
				'percent',
				'%',
				'width=7&sort=no&align=right&append=' . urlencode('%')
			),
			new TableColumn(
				'deaths',
				'Deaths',
				'width=7&align=right'
			),
			new TableColumn(
				'kpd',
				'KPD',
				'width=7&align=right'
			),
		),
		'playerId',
		'skill',
		'kpd',
		true,
		20,
		'members_page',
		'members_sort',
		'members_sortorder',
		'members'
	);

	$result = $db->query("
		SELECT
			playerId,
			lastName,
			country,
			flag,
			skill,
			connection_time,
			kills,
			deaths,
			IFNULL(kills/deaths, '-') AS kpd,
			(kills/" . $clandata["kills"] . ") * 100 AS percent,
			activity
		FROM
			hlstats_Players
		WHERE
			clan=$clan
			AND hlstats_Players.hideranking = 0
		ORDER BY
			$tblMembers->sort $tblMembers->sortorder,
			$tblMembers->sort2 $tblMembers->sortorder,
			lastName ASC
		LIMIT $tblMembers->startitem,$tblMembers->numperpage
	");
	
	$resultCount = $db->query("
		SELECT
			COUNT(*)
		FROM
			hlstats_Players
		WHERE
			clan=$clan
			AND hlstats_Players.hideranking = 0
	");
	
	list($numitems) = $db->fetch_row($resultCount);

	$tblMembers->draw($result, $numitems, 100);
?>
