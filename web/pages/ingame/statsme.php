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
	
	// Player Details
	
	$player = valid_request(intval($_GET['player']), 1);
	$uniqueid  = valid_request(strval($_GET['uniqueid']), 0);
	$game = valid_request(strval($_GET['game']), 0);

	
	if (!$player && $uniqueid)
	{
		if (!$game)
		{
			header('Location: ' . $g_options['scripturl'] . "&mode=search&st=uniqueid&q=$uniqueid");
			exit;
		}
		$uniqueid = preg_replace('/^STEAM_\d+?\:/i','',$uniqueid);
		$db->query("
			SELECT
				playerId
			FROM
				hlstats_PlayerUniqueIds
			WHERE
				uniqueId='$uniqueid'
				AND game='$game'
		");
		
		if ($db->num_rows() > 1)
		{
			header('Location: ' . $g_options['scripturl'] . "&mode=search&st=uniqueid&q=$uniqueid&game=$game");
			exit;
		}
		elseif ($db->num_rows() < 1)
		{
			error("No players found matching uniqueId '$uniqueid'");
		}
		else
		{
			list($player) = $db->fetch_row();
			$player = intval($player);
		}
	}
	elseif (!$player && !$uniqueid)
	{
		error('No player ID specified.');
	}
	
	$db->query("
		SELECT
			hlstats_Players.playerId,
			hlstats_Players.connection_time,
			hlstats_Players.lastName,
			hlstats_Players.country,
			hlstats_Players.flag,
			hlstats_Players.clan,
			hlstats_Players.fullName,
			hlstats_Players.email,
			hlstats_Players.homepage,
			hlstats_Players.icq,
			hlstats_Players.game,
			hlstats_Players.skill,
			hlstats_Players.kills,
			hlstats_Players.deaths,
			IFNULL(kills/deaths, '-') AS kpd,
			hlstats_Players.suicides,
			hlstats_Players.headshots,
			IFNULL(headshots/kills, '-') AS hpk,
			hlstats_Players.shots,
			hlstats_Players.hits,
			hlstats_Players.teamkills,
			hlstats_Players.kill_streak,
			hlstats_Players.death_streak,
			IFNULL(ROUND((hits / shots * 100), 1), 0.0) AS acc,
			hlstats_Clans.name AS clan_name,
			activity
		FROM
			hlstats_Players
		LEFT JOIN hlstats_Clans ON
			hlstats_Clans.clanId = hlstats_Players.clan
		WHERE
			playerId='$player'
	");
	if ($db->num_rows() != 1)
		error("No such player '$player'.");
	
	$playerdata = $db->fetch_array();
	$db->free_result();
	
	$pl_name = $playerdata['lastName'];
	if (strlen($pl_name) > 10)
	{
		$pl_shortname = substr($pl_name, 0, 8) . '...';
	}
	else
	{
		$pl_shortname = $pl_name;
	}
	$pl_name = htmlspecialchars($pl_name, ENT_COMPAT);
	$pl_shortname = htmlspecialchars($pl_shortname, ENT_COMPAT);
	$pl_urlname = urlencode($playerdata['lastName']);
	
	
	$game = $playerdata['game'];
	$db->query("SELECT name FROM hlstats_Games WHERE code='$game'");
	if ($db->num_rows() != 1)
		$gamename = ucfirst($game);
	else
		list($gamename) = $db->fetch_row();
	
?>
	<table class="data-table">
		<tr class="data-table-head">
			<td colspan="3" class="fSmall">Statistics Summary</td>
        </tr>
        <tr class="bg1">
            <td class="fSmall">Name:</td>
            <td colspan="2" class="fSmall"><?php
                if ($g_options['countrydata'] == 1)
					echo '<img src="'.getFlag($playerdata['flag']).'" alt="'.strtolower($playerdata['country']).'" title="'.strtolower($playerdata['country']).'">&nbsp;';   
				echo '<strong>' . htmlspecialchars($playerdata['lastName'], ENT_COMPAT) . '</strong>';
            ?></td>
        </tr>
        <tr class="bg2">
			<td class="fSmall">Member of Clan:</td>
			<td colspan="2" class="fSmall"><?php
				if ($playerdata['clan']) {
					echo '&nbsp;<a href="' . $g_options['scripturl']
					. '?mode=claninfo&amp;clan=' . $playerdata['clan']
					. '">'
					. htmlspecialchars($playerdata['clan_name'], ENT_COMPAT) . '</a>';
				} else
					echo '(None)';
			?></td>
		</tr>
		<tr class="bg1">
			<td style="width:45%;" class="fSmall">Rank:</td>
			<td colspan="2" style="width:55%;" class="fSmall"><?php
				if ($playerdata['activity'] > 0) {            
					$rank = get_player_rank($playerdata);
				} else {
					$rank = 'Not active';
				}

				if (is_numeric($rank))
					echo '<strong>' . number_format($rank) . '</strong>';
				else
					echo "<strong>$rank</strong>";
			?></td>
		</tr>
		<tr class="bg2">
			<td class="fSmall">Points:</td>
			<td colspan="2" class="fSmall"><?php
				echo '<strong>' . number_format($playerdata['skill']) . '</strong>';
			?></td>
		</tr>
        <tr class="bg1">
			<td style="width:45%;" class="fSmall">Activity:*</td>
			<td style="width:45%;" class="fSmall"><?php
				$width = sprintf('%d%%', $playerdata['activity'] + 0.5);
				$bar_type = 1;
				if ($playerdata['activity'] > 40)
					$bar_type = '6';
				elseif ($playerdata['activity'] > 30)
					$bar_type = '5';
				elseif ($playerdata['activity'] > 20)
					$bar_type = '4';
				elseif ($playerdata['activity'] > 10)
					$bar_type = '3';
				elseif ($playerdata['activity'] > 5)
					$bar_type = '2';
				echo '<img src="' . IMAGE_PATH . "/bar$bar_type.gif\" style=\"width:$width%;\" class=\"bargraph\" alt=\"".$playerdata['activity'].'%">';            
			?></td>
			<td style="width:10%;" class="fSmall"><?php
				echo $playerdata['activity'].'%';
			?></td>
		</tr>
		<tr class="bg2">
			<td style="width:45%;" class="fSmall">Kills:</td>
			<td colspan="2" style="width:55%;" class="fSmall"><?php
				echo number_format($playerdata['kills']);
				$db->query("
					SELECT
						COUNT(*)
					FROM
						hlstats_Events_Frags
					LEFT JOIN hlstats_Servers ON
						hlstats_Servers.serverId=hlstats_Events_Frags.serverId
					WHERE
						hlstats_Servers.game='$game' AND killerId='$player'
				");
				list($realkills) = $db->fetch_row();
				echo ' ('.number_format($realkills).')';
			?></td>
		</tr>
		<tr class="bg1">
			<td class="fSmall">Deaths:</td>
			<td colspan="2" class="fSmall"><?php
				echo number_format($playerdata['deaths']);
			?></td>
		</tr>
		<tr class="bg2">
			<td class="fSmall">Suicides:</td>
			<td colspan="2" class="fSmall"><?php
				echo number_format($playerdata['suicides']);
			?></td>
		</tr>
		<tr class="bg1">
			<td class="fSmall">Kills per Death:</td>
			<td colspan="2" class="fSmall"><?php
				$db->query("
						SELECT
							IFNULL(SUM(killerId='$player')/SUM(victimId='$player'), '-') AS kpd
						FROM
							hlstats_Events_Frags,
							hlstats_Servers
						WHERE
							hlstats_Servers.serverId=hlstats_Events_Frags.serverId
							AND (hlstats_Events_Frags.killerId='$player' OR hlstats_Events_Frags.victimId='$player')
							AND hlstats_Servers.game='$game'
				");
				list($realkpd) = $db->fetch_row();
				echo $playerdata['kpd'];
				echo " ($realkpd)";
			?></td>
		</tr>
		<tr class="bg2">
			<td class="fSmall">Headshots:</td>
			<td colspan="2" class="fSmall"><?php
				$db->query("
					SELECT
						COUNT(*)
					FROM
						hlstats_Events_Frags
					LEFT JOIN hlstats_Servers ON
						hlstats_Servers.serverId=hlstats_Events_Frags.serverId
					WHERE
						hlstats_Servers.game='$game' AND killerId='$player'
						AND headshot=1		
				");
				list($realheadshots) = $db->fetch_row();
				if ($playerdata['headshots'] == 0) 
					echo number_format($realheadshots);
				else
					echo number_format($playerdata['headshots']);
				echo ' ('.number_format($realheadshots).')';
			?></td>
		</tr>
		<tr class="bg1">
			<td class="fSmall">Headshots per Kill:</td>
			<td colspan="2" class="fSmall"><?php
   				$db->query("
						SELECT
							IFNULL(SUM(headshot=1)/COUNT(*), '-') AS hpk
						FROM
							hlstats_Events_Frags
						LEFT JOIN hlstats_Servers ON
							hlstats_Servers.serverId=hlstats_Events_Frags.serverId
						WHERE
							hlstats_Servers.game='$game' AND killerId='$player'
				");
				list($realhpk) = $db->fetch_row();
				echo $playerdata['hpk'];
				echo " ($realhpk)";
			?></td>
		</tr>
		<tr class="bg2">
			<td class="fSmall">Weapon Accuracy:</td>
			<td colspan="2" class="fSmall"><?php
				$db->query("
					SELECT
						IFNULL(ROUND((SUM(hlstats_Events_Statsme.hits) / SUM(hlstats_Events_Statsme.shots) * 100), 1), 0.0) AS accuracy,
						SUM(hlstats_Events_Statsme.shots) as shots,
						SUM(hlstats_Events_Statsme.hits) as hits
					FROM
						hlstats_Events_Statsme
					LEFT JOIN hlstats_Servers ON
						hlstats_Servers.serverId=hlstats_Events_Statsme.serverId
					WHERE
						hlstats_Servers.game='$game' AND playerId='$player'
				");
				list($playerdata['accuracy'], $sm_shots, $sm_hits) = $db->fetch_row();
				echo $playerdata['acc'] . '%';
				echo ' ('.$playerdata['accuracy'] . '%)';
			?></td>
		</tr>
		<tr class="bg1">
			<td style="width:45%;" class="fSmall">Teammate Kills:</td>
			<td colspan="2" style="width:55%;" class="fSmall"><?php
				echo number_format($playerdata['teamkills']);
				$db->query("
					SELECT
						COUNT(*)
					FROM
						hlstats_Events_Teamkills
					LEFT JOIN hlstats_Servers ON
						hlstats_Servers.serverId=hlstats_Events_Teamkills.serverId
					WHERE
						hlstats_Servers.game='$game' AND killerId='$player'
				");
				list($realteamkills) = $db->fetch_row();
				echo ' ('.number_format($realteamkills).')';
			?></td>
		</tr>
		<tr class="bg2">
			<td class="fSmall">Longest Kill Streak:</td>
			<td colspan="2" class="fSmall"><?php
				echo number_format($playerdata['kill_streak']);
			?></td>
		<tr class="bg1">
			<td class="fSmall">Longest Death Streak:</td>
			<td colspan="2" class="fSmall"><?php
				echo number_format($playerdata['death_streak']);
			?></td>
		<tr class="bg2">
			<td class="fSmall">Total Connection Time:</td>
			<td colspan="2" class="fSmall"><?php
				echo timestamp_to_str($playerdata['connection_time']);
			?></td>
		</tr>
	</table>
