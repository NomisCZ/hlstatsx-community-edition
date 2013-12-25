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

define('IN_HLSTATS', true);

foreach ($_SERVER as $key => $entry) {
	if ($key !== 'HTTP_COOKIE') {
		$search_pattern  = array('/<script>/', '/<\/script>/', '/[^A-Za-z0-9.\-\/=:;_?#&~]/');
		$replace_pattern = array('', '', '');
		$entry = preg_replace($search_pattern, $replace_pattern, $entry);

		if ($key == 'PHP_SELF') {
			if ((strrchr($entry, '/') !== '/hlstats.php') &&
				(strrchr($entry, '/') !== '/ingame.php') &&
				(strrchr($entry, '/') !== '/show_graph.php') &&
				(strrchr($entry, '/') !== '/sig.php') &&
				(strrchr($entry, '/') !== '/sig2.php') &&
				(strrchr($entry, '/') !== '/index.php') &&
				(strrchr($entry, '/') !== '/status.php') &&
				(strrchr($entry, '/') !== '/top10.php') &&
				(strrchr($entry, '/') !== '/config.php') &&
				(strrchr($entry, '/') !== '/') &&
				($entry !== '')) {
				header('Location: http://'.$_SERVER['HTTP_HOST'].'/hlstats.php');    
				exit;
			} 
		}
		$_SERVER[$key] = $entry;
	}
}
 
require('config.php');
header('Content-Type: text/html; charset=utf-8');

// Check PHP configuration

if (version_compare(phpversion(), "4.1.0", "<"))
{
	error("HLstats requires PHP version 4.1.0 or newer (you are running PHP version " . phpversion() . ").");
}

// do not report NOTICE warnings
error_reporting(E_ALL ^ E_NOTICE);

///
/// Classes
///

// Load database classes
require(INCLUDE_PATH . "/class_db.php");
require(INCLUDE_PATH . "/functions.php");

////
//// Initialisation
////

$db_classname = 'DB_' . DB_TYPE;
if ( class_exists($db_classname) )
{
	$db = new $db_classname(DB_ADDR, DB_USER, DB_PASS, DB_NAME, DB_PCONNECT);
}
else
{
	error('Database class does not exist.  Please check your config.php file for DB_TYPE');
}

$g_options = getOptions();

if (!isset($g_options['scripturl']))
	$g_options['scripturl'] = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : getenv('PHP_SELF');

$g_options['scriptbase'] = str_replace('/status.php', '', $g_options['scripturl']);

////
//// Main Config
////

$game = 'css';  
if ((isset($_GET['game'])) && (is_string($_GET['game'])))
	$game = valid_request($_GET['game'], 0);
$game_escaped = $db->escape($game);
$server_id = '1';
if ((isset($_GET['server_id'])) && (is_numeric($_GET['server_id'])))
	$server_id = valid_request($_GET['server_id'], 1);
$width = '218';
if ((isset($_GET['width'])) && (is_numeric($_GET['width'])))
	$width = valid_request($_GET['width'], 1);
$body_color = 'ECF8FF';
if ((isset($_GET['body_color'])) && (is_string($_GET['body_color'])))
	$body_color = valid_request($_GET['body_color'], 0);
$background_color = 'ABCCD6';
if (isset($_GET['bg_color']))
	$background_color = valid_request($_GET['bg_color'], 0);
$color = '000000';
if ((isset($_GET['color'])) && (is_string($_GET['color'])))
	$color = valid_request($_GET['color'], 0);
$border_width = '1';
if (isset($_GET['border_width']))
	$border_width = valid_request($_GET['border_width'], 1);
$border_color = 'ABCCD6';
if (isset($_GET['border_color']))
	$border_color = valid_request($_GET['border_color'], 0);
$show_logo = '1';
if ((isset($_GET['show_logo'])) && (is_string($_GET['show_logo'])))
	$show_logo = valid_request($_GET['show_logo'], 1);
$small_fonts = '1';
if ((isset($_GET['small_fonts'])) && (is_numeric($_GET['small_fonts'])))
	$small_fonts = valid_request($_GET['small_fonts'], 1);
$server_name = '1';
if ((isset($_GET['server_name'])) && (is_string($_GET['server_name'])))
	$server_name = valid_request($_GET['server_name'], 1);
$server_url = '1';
if ((isset($_GET['server_url'])) && (is_string($_GET['server_url'])))
	$server_url = valid_request($_GET['server_url'], 1);
$map_image = '1';
if ((isset($_GET['map_image'])) && (is_numeric($_GET['map_image'])))
	$map_image = valid_request($_GET['map_image'], 1);
$show_summary = '1';
if ((isset($_GET['show_summary'])) && (is_numeric($_GET['show_summary'])))
	$show_summary = valid_request($_GET['show_summary'], 1);
$map_name = '1';
if ((isset($_GET['map_name'])) && (is_numeric($_GET['map_name'])))
	$map_name = valid_request($_GET['map_name'], 1);
$show_flags = '1';  
if ((isset($_GET['show_flags'])) && (is_numeric($_GET['show_flags'])))
	$show_flags = valid_request($_GET['show_flags'], 1);
$show_players = '1';
if ((isset($_GET['show_players'])) && (is_numeric($_GET['show_players'])))
	$show_players = valid_request($_GET['show_players'], 1);
$show_teams = '1';
if ((isset($_GET['show_teams'])) && (is_numeric($_GET['show_teams'])))
	$show_teams = valid_request($_GET['show_teams'], 1);
$show_team_wins = '1';
if ((isset($_GET['show_team_wins'])) && (is_numeric($_GET['show_team_wins'])))
	$show_team_wins = valid_request($_GET['show_team_wins'], 1);
$show_map_wins = '1';
if ((isset($_GET['show_map_wins'])) && (is_numeric($_GET['show_map_wins'])))
	$show_map_wins = valid_request($_GET['show_map_wins'], 1);
$top_players = '10';
if ((isset($_GET['top_players'])) && (is_numeric($_GET['top_players'])))
	$top_players = valid_request($_GET['top_players'], 1);
$players_images = '1';
if ((isset($_GET['players_images'])) && (is_numeric($_GET['players_images'])))
	$players_images = valid_request($_GET['players_images'], 1);
$show_password = '';
if ((isset($_GET['show_password'])) && (is_string($_GET['show_password'])))
	$show_password = valid_request($_GET['show_password'], 1);

//// Entries
$result = $db->query("
	SELECT
		IF(publicaddress != '', publicaddress, concat(address, ':', port)) AS addr,
		name, 
		publicaddress, 
		act_map, 
		players, 
		kills, 
		headshots, 
		map_started, 
		act_players, 
		max_players, 
		map_ct_wins, 
		map_ts_wins
	FROM 
		hlstats_Servers
	WHERE 
		serverId=$server_id");
$server_data = $db->fetch_array($result);

if ($small_fonts == 1)
{
	$fsize = 'fSmall';
}
else
{
	$fsize = 'fNormal';
}

if ($server_data['addr'] != '')  {
	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
	echo '<html>';
	echo '<head>';
	echo '<title>'.$g_options["sitename"].'</title>';
	echo '<style type="text/css">{margin:0px;padding:0px;}</style>';
	echo '<link rel="stylesheet" type="text/css" href="hlstats.css">';
	echo '<link rel="stylesheet" type="text/css" href="styles/'.$g_options['style'].'">';
	echo '</head>';
	echo '<body style="background:#'.$body_color.';color:#'.$color.';"  class="'.$fsize.'">';
	echo '<table border="0" cellpadding="0" cellspacing="0" style="border:'.$border_width.'px solid #'.$border_color.';background:#'.$background_color.';color:#'.$color.';width:'.$width.'px;">';

	if ($show_logo == 1)
	{
		echo '<tr style="background-image:url('.IMAGE_PATH.'/icons/title-background.gif);"><td align="center" colspan="2" class="'.$fsize.'">';
		$logo = file_exists(IMAGE_PATH.'/icons/shorttitle-'.$game.'.png');
		if ($logo) {
			if ($server_name == 1)
			{
				echo '<a target="_blank" href="'.$g_options["siteurl"].'"><img src="'.IMAGE_PATH.'/icons/shorttitle-'.$game.'.png" style="width:'.$width.'px;border:0px;" alt="'.$g_options['sitename'].'" title="'.$g_options['sitename'].'" /></a>';
			}
			else
			{
				echo '<a target="_blank" href="'.$g_options["siteurl"].'"><img src="'.IMAGE_PATH.'/icons/shorttitle-'.$game.'.png" style="width:'.$width.'px;border:0px;" alt="'.$server_data['name'].'" title="'.$server_data['name'].'" /></a>';
			}
		}
		else
		{
			echo '<a target="_blank" href="http://www.hlxcommunity.com" style="display:block;"><img src="'.IMAGE_PATH.'/icons/title-short.png" style="width:'.$width.'px;border:0px;" alt="Realtime player statistics for Halflife2 Source Engine" title="Realtime player statistics for Halflife2 Source Engine" /></a>';
		}
	echo '</td></tr>';
	}

	if ($server_name == 1)
	{
		echo '<tr><td align="center" colspan="2" class="'.$fsize.'">';
		echo '<a target="_blank" href="'.$g_options['scriptbase'].'" title="View statistics"><b>'.$server_data['name'].'</b></a>';
		echo '</td></tr>';
	}

	if ($server_url == 1)
	{
		echo '<tr><td align="center" colspan="2" class="'.$fsize.'">';
		echo '<a href="steam://connect/'.$server_data['addr'].'" title="Connent to Server"><b>'.$server_data['addr'].'</b></a>';
		echo '</td></tr>';
	}

	if ($show_password != '')
	{
		echo '<tr><td align="center" colspan="2" class="'.$fsize.'">';
		echo '<b>Password:&nbsp;'.$show_password.'</b>';
		echo '</td></tr>';
	}    

	if ($map_image == 1)
	{
		$mapimg = getImage("/games/{$game}/maps/{$server_data['act_map']}");
		if (!file_exists($mapimg['path'])) {
			$mapimg = getImage("/games/{$game}/maps/default");
			if (!file_exists($mapimg['path'])) {
				$mapimg = getImage("/nomap");
			}
		}
		
		echo '<tr><td align="center" colspan="2">';
		echo '<a target="_blank" href="'.$g_options['scriptbase'].'/hlstats.php?mode=mapinfo&amp;map='.$server_data['act_map'].'&amp;game='.$game.'"><img src="'.$mapimg['url'].'" style="width:'.$width.'px;border:0px" alt="'.$server_data['act_map'].'" title="'.$server_data['act_map'].'" /></a>'; 
		echo '</td></tr>';
	}

	if ($show_summary == 1)
	{
		echo '<tr><td align="left" style="padding-left:2px" class="'.$fsize.'">';
		echo 'Players:'; 
		echo '</td><td align="right" style="padding-right:2px;" class="'.$fsize.'">';
		echo number_format($server_data['players']); 
		echo '</td></tr>';

		echo '<tr><td align="left" style="padding-left:2px" class="'.$fsize.'">';
		echo 'Kills:'; 
		echo '</td><td align="right" style="padding-right:2px;" class="'.$fsize.'">';
		echo number_format($server_data['kills']); 
		echo '</td></tr>';

		if ($server_data['headshots'] > 0)
		{
			echo '<tr><td align="left" style="padding-left:2px" class="'.$fsize.'">';
			echo 'Headshots:'; 
			echo '</td><td align="right" style="padding-right:2px;" class="'.$fsize.'">';
			echo number_format($server_data['headshots']); 
			echo '</td></tr>';
		}
	}

	if ($map_name == 1) {
		echo '<tr><td align="left" style="padding-left:2px" class="'.$fsize.'">';
		echo 'Map:'; 
		echo '</td><td align="right" style="padding-right:2px;" class="'.$fsize.'">';
		echo $server_data['act_map']; 
		echo '</td></tr>';
	}
	$stamp = $server_data['map_started']==0?0:time() - $server_data['map_started'];
	$hours = sprintf("%02d", floor($stamp / 3600));
	$min   = sprintf("%02d", floor(($stamp % 3600) / 60));
	$sec   = sprintf("%02d", floor($stamp % 60)); 
	echo '<tr><td align="left" style="padding-left:2px" class="'.$fsize.'">';
	echo 'Map Time:'; 
	echo '</td><td align="right" style="padding-right:2px;" class="'.$fsize.'">';
	echo $hours.':'.$min.':'.$sec; 
	echo '</td></tr>';
	echo '<tr><td align="left" style="padding-left:2px;border-bottom:1px solid #000000;" class="'.$fsize.'">';
	echo 'Online:'; 
	echo '</td><td align="right" style="padding-right:2px;border-bottom:1px solid #000000;" class="'.$fsize.'">';
	echo $server_data['act_players'].'/'.$server_data['max_players']; 
	echo '</td></tr>';

	if ($show_players == 1)
	{
		echo '<tr><td colspan="2"><table width="100%" border="0" cellpadding="0" cellspacing="0">';

		unset($team_colors);
		$statsdata = $db->query("
			SELECT 
				team, 
				name, 
				teamkills, 
				teamdeaths, 
				teamheadshots, 
				teamping, 
				teamskill, 
				teamshots, 
				teamhits, 
				teamjointime, 
				IFNULL(playerlist_bgcolor,'#D5D5D5') as playerlist_bgcolor, 
				IFNULL(playerlist_color,'#050505') AS playerlist_color, 
				IFNULL(playerlist_index, 99 ) AS playerlist_index
			FROM 
				hlstats_Teams
			RIGHT JOIN
				(SELECT 
					team, 
					sum( kills ) AS teamkills, 
					sum( deaths ) AS teamdeaths, 
					sum( headshots ) AS teamheadshots, 
					avg( ping /2 ) AS teamping, 
					avg( skill ) AS teamskill, 
					sum( shots ) AS teamshots, 
					sum( hits ) AS teamhits, 
					sum( unix_timestamp( NOW( ) ) - connected ) AS teamjointime
				FROM 
					hlstats_Livestats
				WHERE 
					server_id = $server_id
					AND connected >0
				GROUP BY 
					team
				ORDER BY 
					teamkills
				) teaminfo
			ON
				code = team
			AND
				hlstats_Teams.game = '{$game_escaped}'
			ORDER BY 
				playerlist_index
			LIMIT 0 , 30
			");

		$teamdata = array();
		$playerdata = array();
		$teamno = 0;

		while ($thisteam = $db->fetch_array($statsdata))
		{
			$teamdata[$teamno] = $thisteam;
			$thisteam_escaped = $db->escape($thisteam['team']);
			$pldata = $db->query("
				SELECT
					player_id, 
					name, 
					kills, 
					deaths, 
					headshots, 
					ping, 
					skill, 
					shots, 
					hits, 
					connected, 
					skill_change, 
					cli_flag
				FROM 
					hlstats_Livestats 
				WHERE 
					server_id = $server_id 
					AND team = '{$thisteam_escaped}'
				ORDER BY 
					kills DESC
			");

			while ($thisplayer = $db->fetch_array($pldata))
			{
				$playerdata[$teamno][] = $thisplayer;
			}
			$teamno++;
		}
		$curteam = 0;

		while (isset($teamdata[$curteam]))
		{
			$j=0;
			$thisteam = $teamdata[$curteam];
			$teamcolor = 'background:'.$thisteam['playerlist_bgcolor'].';color:'.$thisteam['playerlist_color'];
			$bordercolor = 'background:'.$$thisteam['playerlist_bgcolor'].';color:'.$thisteam['playerlist_color'].';border-top:1px '.$thisteam['playerlist_color'].' solid';
			$team_display_name  = htmlspecialchars($thisteam['name']);

			while (isset($playerdata[$curteam][$j]))
			{
				$thisplayer = $playerdata[$curteam][$j];
			echo '<tr style="'.$teamcolor.'">';
			echo '<td align="left" width="85%" style="'.$teamcolor.';padding-left:3px;" class="'.$fsize.'">';
				if (isset($thisplayer))
				{
					if (strlen($thisplayer['name'])>50)
					{
						$thisplayer['name'] = substr($thisplayer['name'], 0, 50);
					}
					echo '<a target="_blank" style="color:'.$thisteam['playerlist_color'].';" href="'.$g_options['scriptbase'].'/hlstats.php?mode=playerinfo&amp;player='.$thisplayer['player_id'].'" title="Player Details">';
					if ($show_flags == 1)
					{
					echo '<img src="'.getFlag($thisplayer['cli_flag']).'" alt="'.ucfirst(strtolower($thisplayer['cli_country'])).'" title="'.ucfirst(strtolower($thisplayer['cli_country'])).'">&nbsp;';
					}
					echo '<span style="vertical-align:middle;">'.htmlspecialchars($thisplayer['name'], ENT_COMPAT).'</span></a>';
				}
				else
				{
					echo '&nbsp;';
				}
			echo '</td>';
			echo '<td align="right" width="15%" style="'.$teamcolor.';padding-right:3px" class="'.$fsize.'">';
				if (isset($thisplayer))
				{
					echo $thisplayer['kills'];
				}
				else
				{
					echo '&nbsp;';
				}
			echo '&nbsp;:&nbsp;';
				if (isset($thisplayer))
				{
					echo $thisplayer['deaths'];
				}
				else
				{
					echo '&nbsp;';
				}
			echo '</td>';
			echo '</tr>';
			$j++;	
			}

			if ($show_teams == 1)
			{
				if ($team_display_name)
				{
					echo '<tr style="'.$teamcolor.'">';
					echo '<td align="left" width="85%" style="'.$bordercolor.';'.$teamcolor.';padding-left:3px;" class="'.$fsize.'">';
					echo '&nbsp;<b>'.$team_display_name.'</b>';
					if ($show_team_wins == 1) {
						if (($map_teama_wins > 0) || ($map_teamb_wins > 0))
						{
							echo '&nbsp;('.$map_teama_wins.' wins)';
						}
					}
					echo '</td>';
					echo '<td align="right" width="15%" style="'.$bordercolor.';'.$teamcolor.';padding-right:3px" class="'.$fsize.'">';
					if (count($teamdata[$curteam]) > 0)
					{
						echo $teamdata[$curteam]['teamkills'];
					}
					else
					{
						echo '&nbsp;';
					}
					echo '&nbsp;:&nbsp;';
					if (count($teamdata[$curteam]) > 0)
					{
						echo $teamdata[$curteam]['teamdeaths'];
					}
					else
					{
						echo '&nbsp;';
					}
					echo '</td>';
					echo '</tr>';
				}
			}
		$curteam++;
		}
// these variables are not set - so removing it
/*
		if ((count($teama_players) > 0) || (count($teamb_players) > 0))
		{
			if ($show_map_wins == 1) {
				echo '<tr><td align="center" colspan="2" class="'.$fsize.'">';
				echo '<span style="color:'.$teamcolor.';font-weight:bold;">'.$teama_display_name.'&nbsp;'.$server_data['map_ct_wins'].'&nbsp;</span><span style="color:black;">:&nbsp;</span><span style="color:'.$teamcolor.';font-weight:bold;">'.$server_data['map_ts_wins'].'&nbsp;'.$teamb_display_name.'</span>';
				echo '</td></tr>';
			}
		}
*/
		if (count($teamdata) == 0)
		{
			echo '<tr><td colspan="2" align="left" style="background:#EFEFEF;color:black" class="'.$fsize.'">';
			echo '&nbsp;No Players';
			echo '</td></tr>';
		}
	echo '</table></td></tr>';
	}

	if ($top_players > 0)
	{
		$db->query("
			SELECT 
				playerId, 
                                unhex(replace(hex(lastName), 'E280AE', '')) as lastName,
				flag, 
				country, 
				skill, 
				IFNULL(kills/deaths, '-') AS kpd, 
				IFNULL(ROUND((hits / shots * 100), 1), 0.0) AS acc
			FROM 
				hlstats_Players 
			WHERE 
				game='{$game_escaped}'
				AND hideranking=0
			ORDER BY
				skill DESC, 
				kpd DESC 
			LIMIT 0,$top_players
		");
		echo '<tr><td colspan="2"><table border="0" cellpadding="0" cellspacing="0" style="width:100%">';
		echo '<tr><td align="center" colspan="2" style="border:1px solid #000000;" class="'.$fsize.'">';
		echo '<b>TOP '.$top_players.' Players</b>';
		echo '</td></tr>';

		while ($player = $db->fetch_array())
		{
			echo '<tr><td align="left" width="85%" style="padding-left:2px" class="'.$fsize.'">';
			$cut_pos = 15;
			if ($small_fonts == 1)
				$cut_pos += 10;
			$display_name = $player['lastName'];
			if (strlen($player['lastName']) > $cut_pos)
				$display_name = substr($player['lastName'], 0, $cut_pos);
			echo '<a target="_blank" href="'.$g_options["scriptbase"].'/hlstats.php?mode=playerinfo&amp;player='.$player['playerId'].'" title="Player Details">';
			if ($show_flags == 1)
			{
				if ($player['country'] == '')
					$player['country'] = 'Unknown Country';
				echo '<img src="'.getFlag($player['flag']).'" alt="'.ucfirst($player['country']).'" title="'.ucfirst($player['country']).'">&nbsp;';
			}
			else
			{
				if ($players_images == 1)
				{
					echo '<img src="'.IMAGE_PATH.'/player.gif" />&nbsp;';
				}
			}
			echo '<span style="vertical-align:middle;">'.htmlspecialchars($display_name, ENT_COMPAT).'</span></a>';
			echo '</td><td align="right" width="15%" style="padding-right:2px;" class="'.$fsize.'">';
			echo $player['skill'];
			echo '</td></tr>'; 
		}
		echo '</table></td></tr>';
	}
	echo '</table>';
	echo '</body>';
	echo '</html>';
} 
?>
