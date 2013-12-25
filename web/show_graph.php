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

	define('IN_HLSTATS', true);

	// Load database classes
	require ('config.php');
	require (INCLUDE_PATH . '/class_db.php');
	require (INCLUDE_PATH . '/functions.php');
	require (INCLUDE_PATH . '/functions_graph.php');

	$db_classname = 'DB_' . DB_TYPE;
	if (class_exists($db_classname))
	{
		$db = new $db_classname(DB_ADDR, DB_USER, DB_PASS, DB_NAME, DB_PCONNECT);
	}
	else
	{
		error('Database class does not exist.  Please check your config.php file for DB_TYPE');
	}

	$g_options = getOptions();

	$width = 500;
	if ((isset($_GET['width'])) && (is_numeric($_GET['width'])))
		$width = valid_request($_GET['width'], 1);
	$server_id = 1;
	if ((isset($_GET['server_id'])) && (is_numeric($_GET['server_id'])))
		$server_id = valid_request($_GET['server_id'], 1);
	$height = 125;
	if ((isset($_GET['height'])) && (is_numeric($_GET['height'])))
		$height = valid_request($_GET['height'], 1);
	$player = 1;
	if ((isset($_GET['player'])) && (is_numeric($_GET['player'])))
		$player = valid_request($_GET['player'], 1);
	$game = "unset";
	if (isset($_GET['game']))
		$game = valid_request($_GET['game'], 0);

	$game_escaped=$db->escape($game);

	$bar_type = 0; // 0 == serverinfo last 100 entries
	// 1 == ?!
	// 2 == player trend history
	// 3 == masterserver load

	if ((isset($_GET['type'])) && (is_numeric($_GET['type'])))
		$bar_type = valid_request($_GET['type'], 1);

		
	$selectedStyle = (isset($_COOKIE['style']) && $_COOKIE['style']) ? $_COOKIE['style'] : $g_options['style'];

	
	// Determine if we have custom nav images available
	$selectedStyle = preg_replace('/\.css$/','',$selectedStyle);
	
	$iconpath = IMAGE_PATH . "/icons";
	if (file_exists($iconpath . "/" . $selectedStyle)) {
		$iconpath = $iconpath . "/" . $selectedStyle;
	}		

	$bg_color = array('red' => 171, 'green' => 204, 'blue' => 214);
	if ((isset($_GET['bgcolor'])) && (is_string($_GET['bgcolor'])))
		$bg_color = hex2rgb(valid_request($_GET['bgcolor'], 0));

	$color = array('red' => 255, 'green' => 255, 'blue' => 255);
	if ((isset($_GET['color'])) && (is_string($_GET['color'])))
		$color = hex2rgb(valid_request($_GET['color'], 0));

	$bg_id = $bg_color['red'] + $bg_color['green'] + $bg_color['blue'];

	$server_load_type = 1;
	if ((isset($_GET['range'])) && (is_numeric($_GET['range'])))
		$server_load_type = valid_request($_GET['range'], 1);

	switch ($server_load_type)
	{
		case 1:
			$avg_step = 1;
			$update_interval = IMAGE_UPDATE_INTERVAL;
			break;
		case 2:
			$avg_step = 7;
			$update_interval = 60 * 60 * 6; // 6 Hours
			break;
		case 3:
			$avg_step = 33;
			$update_interval = 60 * 60 * 12; // 12 Hours
			break;
		case 4:
			$avg_step = 400;
			$update_interval = 60 * 60 * 24; // 24 Hours
			break;
		default:
			$avg_step = 1;
			$update_interval = IMAGE_UPDATE_INTERVAL;
			break;
	}

	if ($bar_type != 2)
	{
		$cache_image = IMAGE_PATH . '/progress/server_' . $width . '_' . $height . '_' . $bar_type . '_' . $game . '_' . $server_id . '_' . $bg_id . '_' . $server_load_type . '.png';
		if (file_exists($cache_image))
		{
			$file_timestamp = filemtime($cache_image);
			if ($file_timestamp + $update_interval > time())
			{
				if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
				{
					if ( strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) + $update_interval > time() )
					{
						header('HTTP/1.0 304 Not Modified');
						exit;
					}
				}
				$mod_date = date('D, d M Y H:i:s \G\M\T', $file_timestamp);
				header('Last-Modified:' . $mod_date);
				$image = imagecreatefrompng(IMAGE_PATH . '/progress/server_' . $width . '_' . $height . '_' . $bar_type . '_' . $game . '_' . $server_id . '_' . $bg_id . '_' . $server_load_type . '.png');
				imagepng($image);
				imagedestroy($image);
				exit;
			}
		}
	}

	$legend_x = 0;
	$max_pos_y = array();
	// array("width" => $width, "height" => $height, "indent_x" => array(20, 20), "indent_y" => array(10,15))
	// defined: function drawItems($image, $bounds, $data_array, $max_index, $name, $dot, $make_grid, $write_timestamp, $write_legend, $color)

	$image = imagecreatetruecolor($width, $height);
	imagealphablending($image, false);

	if (function_exists('imageantialias'))
		imageantialias($image, true);


	// load bgimage if exists...
	$drawbg = true;

	$normal_color = imagecolorallocate($image, 0xEF, 0xEF, 0xEF);
	$light_color = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
	$dark_color = imagecolorallocate($image, 0x99, 0xAA, 0xAA);

	$font_color = imagecolorallocate($image, $color['red'], $color['green'], $color['blue']);
	$main_color = imagecolorallocate($image, $bg_color['red'], $bg_color['green'], $bg_color['blue']);

	$blue = imagecolorallocate($image, 0, 0, 255);
	$black = imagecolorallocate($image, 0, 0, 0);
	$red = imagecolorallocate($image, 255, 0, 0);
	$white = imagecolorallocate($image, 255, 255, 255);
	$orange = imagecolorallocate($image, 255, 165, 0);
	$gray = imagecolorallocate($image, 105, 105, 105);
	$light_gray = imagecolorallocate($image, 0xEF, 0xEF, 0xEF);
	$green = imagecolorallocate($image, 255, 0, 255);
	$gray_border = imagecolorallocate($image, 0xE0, 0xE0, 0xE0);

	if ($bar_type == 0)
	{
		$indent_x = array(30, 30);
		$indent_y = array(15, 15);

		// background
		if ($drawbg)
		{
			imagefilledrectangle($image, 0, 0, $width, $height, $main_color); // background color
			imagerectangle($image, $indent_x[0], $indent_y[0], $width - $indent_x[1], $height - $indent_y[1], $dark_color);
			imagefilledrectangle($image, $indent_x[0] + 1, $indent_y[0] + 1, $width - $indent_x[1] - 1, $height - $indent_y[1] - 1, $light_color);
		}

		$limit = '';
		if ($avg_step < 10)
			$limit = ' LIMIT 0, 2500';

		// entries
		$data_array = array();
		$result = $db->query("SELECT timestamp, act_players, min_players, max_players, map, uptime, fps FROM hlstats_server_load WHERE server_id=$server_id ORDER BY timestamp DESC$limit");
		// TSGK
		$last_map = 0;
		// TSGK

		$i = 0;
		$avg_values = array();
		while ($rowdata = $db->fetch_array($result))
		{
			$i++;
			$avg_values[] = array('timestamp' => $rowdata['timestamp'], 'act_players' => $rowdata['act_players'], 'min_players' => $rowdata['min_players'], 'max_players' => $rowdata['max_players'], 'uptime' => $rowdata['uptime'], 'fps' => $rowdata['fps'], 'map' => $rowdata['map']);
			
			if ($i == $avg_step)
			{
				$insert_values = array();
				$insert_values['timestamp'] = $avg_values[ceil($avg_step / 2) - 1]['timestamp'];
				$insert_values['act_players'] = 0;
				$insert_values['min_players'] = 0;
				$insert_values['max_players'] = 0;
				$insert_values['uptime'] = 0;
				$insert_values['fps'] = 0;
				$insert_values['map'] = "";

				foreach ($avg_values as $entry)
				{
					$insert_values['act_players'] += $entry['act_players'];
					$insert_values['min_players'] += $entry['min_players'];
					$insert_values['max_players'] += $entry['max_players'];
					$insert_values['uptime'] += $entry['uptime'];
					$insert_values['fps'] += $entry['fps'];
					$insert_values['map'] = $entry['map'];
				}
				$insert_values['act_players'] = round($insert_values['act_players'] / $avg_step);
				$insert_values['uptime'] = round($insert_values['uptime'] / $avg_step);
				$insert_values['fps'] = round($insert_values['fps'] / $avg_step);
				$insert_values['min_players'] = round($insert_values['min_players'] / $avg_step);
				$insert_values['max_players'] = round($insert_values['max_players'] / $avg_step);
				$data_array[] = array('timestamp' => $insert_values['timestamp'], 'act_players' => $insert_values['act_players'], 'min_players' => $insert_values['min_players'], 'max_players' => $insert_values['max_players'], 'uptime' => $insert_values['uptime'], 'fps' => $insert_values['fps'], 'map' => $insert_values['map']);
				$avg_values = array();
				$i = 0;
			}

		}
		//print_r($data_array);

		$last_map = '';
		if ($avg_step == 1)
		{
			$result = $db->query("SELECT act_players, max_players FROM hlstats_Servers WHERE serverId=$server_id");
			$rowdata = $db->fetch_array($result);
			$rowdata['uptime'] = 0;
			array_unshift($data_array, array('timestamp' => time(), 'act_players' => $rowdata['act_players'], 'min_players' => $data_array[0]['min_players'], 'max_players' => $rowdata['max_players'], 'uptime' => $rowdata['uptime'], 'fps' => $rowdata['uptime'], 'map' => $last_map));
		}

		if (count($data_array) > 1)
		{
			drawItems($image, array('width' => $width, 'height' => $height, 'indent_x' => $indent_x, 'indent_y' => $indent_y), $data_array, 0, 'max_players', 0, 1, 0, 1, array($gray, $red, $font_color, $dark_color, $light_gray));
			drawItems($image, array('width' => $width, 'height' => $height, 'indent_x' => $indent_x, 'indent_y' => $indent_y), $data_array, 0, 'min_players', 0, 0, 0, 1, array($dark_color, $red, $font_color, $dark_color));
			drawItems($image, array('width' => $width, 'height' => $height, 'indent_x' => $indent_x, 'indent_y' => $indent_y), $data_array, 0, 'act_players', 0, 0, 1, 1, array($blue, $red, $font_color, $dark_color));
			drawItems($image, array('width' => $width, 'height' => $height, 'indent_x' => $indent_x, 'indent_y' => $indent_y), $data_array, 2, 'uptime', 0, 0, 1, 1, array($orange, $red, $font_color, $dark_color));
			drawItems($image, array('width' => $width, 'height' => $height, 'indent_x' => $indent_x, 'indent_y' => $indent_y), $data_array, 2, 'fps', 0, 0, 1, 1, array($red, $red, $font_color, $dark_color));

			drawItems($image, array('width' => $width, 'height' => $height, 'indent_x' => $indent_x, 'indent_y' => $indent_y), $data_array, 0, 'max_players', 0, 1, 0, 0, array($gray, $red, $font_color, $dark_color, $light_gray));
		}

		if ($width >= 800)
		{
			if ($avg_step == 1)
			{
				$result = $db->query("SELECT avg(act_players) as players FROM hlstats_server_load WHERE server_id=$server_id AND timestamp>=" . (time() - 3600));
				$rowdata = $db->fetch_array($result);
				$players_last_hour = sprintf("%.1f", $rowdata['players']);

				$result = $db->query("SELECT avg(act_players) as players FROM hlstats_server_load WHERE server_id=$server_id AND timestamp>=" . (time() - 86400));
				$rowdata = $db->fetch_array($result);
				$players_last_day = sprintf("%.1f", $rowdata['players']);

				$str = 'Average Players Last 24h: ' . $players_last_day . ' Last 1h: ' . $players_last_hour;
				$str_width = (imagefontwidth(1) * strlen($str)) + 2;
				imagestring($image, 1, $width - $indent_x[1] - $str_width, $indent_y[0] - 11, $str, $font_color);
			}
		}

	} elseif ($bar_type == 1)
	{
		$indent_x = array(35, 35);
		$indent_y = array(15, 15);

		// background
		if ($drawbg)
		{
			imagefilledrectangle($image, 0, 0, $width, $height, $main_color); // background color
			imagerectangle($image, $indent_x[0], $indent_y[0], $width - $indent_x[1], $height - $indent_y[1], $dark_color);
			imagefilledrectangle($image, $indent_x[0] + 1, $indent_y[0] + 1, $width - $indent_x[1] - 1, $height - $indent_y[1] - 1, $light_color);
		}

		// entries
        $data_array = array();
        $result = $db->query("SELECT timestamp, players, kills, headshots, act_slots, max_slots FROM hlstats_Trend WHERE game='{$game_escaped}' ORDER BY timestamp DESC LIMIT 0, 350");
		while ($rowdata = $db->fetch_array($result))
		{
			$data_array[] = array('timestamp' => $rowdata['timestamp'], 'players' => $rowdata['players'], 'kills' => $rowdata['kills'], 'headshots' => $rowdata['headshots'], 'act_slots' => $rowdata['act_slots'], 'max_slots' => $rowdata['max_slots']);
		}

		$players_data = $db->query("SELECT count(playerId) as player_count FROM hlstats_Players WHERE game='{$game_escaped}'");
		$rowdata = $db->fetch_array($players_data);
		$total_players = $rowdata['player_count'];

		if (count($data_array) > 1)
		{
			drawItems($image, array('width' => $width, 'height' => $height, 'indent_x' => $indent_x, 'indent_y' => $indent_y), $data_array, 0, 'kills', 0, 0, 0, 0, array($orange, $red, $font_color, $dark_color, $light_gray));
			drawItems($image, array('width' => $width, 'height' => $height, 'indent_x' => $indent_x, 'indent_y' => $indent_y), $data_array, 0, 'headshots', 0, 0, 0, 1, array($dark_color, $red, $font_color, $dark_color, $light_gray));
			drawItems($image, array('width' => $width, 'height' => $height, 'indent_x' => $indent_x, 'indent_y' => $indent_y), $data_array, 0, 'players', 0, 0, 0, 1, array($red, $red, $font_color, $dark_color, $light_gray));
			drawItems($image, array('width' => $width, 'height' => $height, 'indent_x' => $indent_x, 'indent_y' => $indent_y), $data_array, 2, 'max_slots', 0, 0, 0, 1, array($gray, $red, $font_color, $dark_color, $light_gray));
			drawItems($image, array('width' => $width, 'height' => $height, 'indent_x' => $indent_x, 'indent_y' => $indent_y), $data_array, 2, 'act_slots', 0, 0, 1, 1, array($blue, $red, $font_color, $dark_color));

			drawItems($image, array('width' => $width, 'height' => $height, 'indent_x' => $indent_x, 'indent_y' => $indent_y), $data_array, 0, 'kills', 0, 1, 0, 1, array($orange, $red, $font_color, $dark_color, $light_gray));
		}

		if ($width >= 800)
		{
			$result = $db->query("SELECT players FROM hlstats_Trend WHERE game='{$game_escaped}' AND timestamp<=" . (time() - 3600) . " ORDER by timestamp DESC LIMIT 0,1");
			$rowdata = $db->fetch_array($result);
			$players_last_hour = $total_players - $rowdata['players'];

			$result = $db->query("SELECT players FROM hlstats_Trend WHERE game='{$game_escaped}' AND timestamp<=" . (time() - 86400) . " ORDER by timestamp DESC LIMIT 0,1");
			$rowdata = $db->fetch_array($result);
			$players_last_day = $total_players - $rowdata['players'];

			$str = 'New Players Last 24h: ' . $players_last_day . ' Last 1h: ' . $players_last_hour;
			$str_width = (imagefontwidth(1) * strlen($str)) + 2;
			imagestring($image, 1, $width - $indent_x[1] - $str_width, $indent_y[0] - 11, $str, $font_color);
		}

	} elseif ($bar_type == 2)
	{
		// PLAYER HISTORY GRAPH
		$indent_x = array(35, 35);
		$indent_y = array(15, 15);
		
		if (file_exists($iconpath . "/trendgraph.png")) {
			$trendgraph_bg = $iconpath . "/trendgraph.png";
		} else {
			$trendgraph_bg = IMAGE_PATH . "/graph/trendgraph.png";
		}

		$background_img = imagecreatefrompng($trendgraph_bg);
		if ($background_img)
			{
				imagecopy($image, $background_img, 0, 0, 0, 0, 400, 152);
				imagedestroy($background_img);
				$drawbg = false;
			}

		// background
		if ($drawbg)
		{
			imagefilledrectangle($image, 0, 0, $width, $height, $main_color); // background color
			imagerectangle($image, $indent_x[0], $indent_y[0], $width - $indent_x[1], $height - $indent_y[1], $dark_color);
			imagefilledrectangle($image, $indent_x[0] + 1, $indent_y[0] + 1, $width - $indent_x[1] - 1, $height - $indent_y[1] - 1, $light_color);
		}

		// entries
		$deletedays = $g_options['DeleteDays'];
		if ($deletedays == 0)
			$deletedays = 14;

		// define first day's timestamp range
		$ts = strtotime(date('Y-m-d'));
		$data_array = array();
		$arcount = 0;
		$result = $db->query("SELECT eventTime, skill, kills, deaths, headshots, connection_time, UNIX_TIMESTAMP(eventTime) AS ts FROM hlstats_Players_History WHERE playerId=" . $player . " ORDER BY eventTime DESC LIMIT 0, " . $deletedays);
		while (($rowdata = $db->fetch_array($result)) && ($arcount < $deletedays))
		{
			//echo $rowdata['eventTime']." - ".date("Y-m-d", $ts)."\n";
			while (($rowdata['eventTime'] != date("Y-m-d", $ts)) && ($arcount < $deletedays))
			{
				// insert null value
				$data_array[] = array('timestamp' => $ts, 'skill' => $rowdata['skill'], 'kills' => 0, 'headshots' => 0, 'deaths' => 0, 'time' => 0);
				$ts -= 86400;
				$arcount++;
			}

			$data_array[] = array('timestamp' => $rowdata['ts'], 'skill' => $rowdata['skill'], 'kills' => $rowdata['kills'], 'headshots' => $rowdata['headshots'], 'deaths' => $rowdata['deaths'], 'time' => $rowdata['connection_time']);
			$arcount++;
			$ts -= 86400;
		}

		while (($arcount < $deletedays))
		{
			// insert null value
			$data_array[] = array('timestamp' => $ts, 'skill' => $rowdata['skill'], 'kills' => 0, 'headshots' => 0, 'deaths' => 0, 'time' => 0);
			$ts -= 86400;
			$arcount++;
		}

		$deletedays = count($data_array);

		$first_entry = 10; // disable tsgk map function

		if (count($data_array) > 1)
		{
			drawItems($image, array('width' => $width, 'height' => $height, 'indent_x' => $indent_x, 'indent_y' => $indent_y), $data_array, 0, 'kills', 0, 1, 0, 1, array($red, $red, $font_color, $dark_color, $light_gray));
			drawItems($image, array('width' => $width, 'height' => $height, 'indent_x' => $indent_x, 'indent_y' => $indent_y), $data_array, 0, 'headshots', 0, 0, 0, 1, array($dark_color, $red, $font_color, $dark_color, $light_gray));
			drawItems($image, array('width' => $width, 'height' => $height, 'indent_x' => $indent_x, 'indent_y' => $indent_y), $data_array, 0, 'skill', 0, 0, 0, 1, array($orange, $red, $font_color, $dark_color, $light_gray));
			drawItems($image, array('width' => $width, 'height' => $height, 'indent_x' => $indent_x, 'indent_y' => $indent_y), $data_array, 2, 'deaths', 0, 0, 0, 1, array($gray, $red, $font_color, $dark_color, $light_gray));
		}

		$str = $deletedays . ' days Trend';
		$str_width = (imagefontwidth(1) * strlen($str)) + 2;
		imagestring($image, 1, $width - $indent_x[1] - $str_width, $indent_y[0] - 11, $str, $font_color);
	}

	imageTrueColorToPalette($image, 0, 65535);

	// $bar_type=2;

	// achtung, hier ist noch ein pfad hardcoded!!!
	header('Content-Type: image/png');

	if ($bar_type != 2)
	{
		@imagepng($image, IMAGE_PATH . '/progress/server_' . $width . '_' . $height . '_' . $bar_type . '_' . $game . '_' . $server_id . '_' . $bg_id . '_' . $server_load_type . '.png');
		$mod_date = date('D, d M Y H:i:s \G\M\T', time());
		header('Last-Modified:'.$mod_date);
		imagepng($image);
		imagedestroy($image);
		//Opera doesn't like the redirect
		/*$mod_date = date('D, d M Y H:i:s \G\M\T', time());
		header('Last-Modified:'.$mod_date);
		header("Location: ".IMAGE_PATH."/progress/server_".$width."_".$height."_".$bar_type."_".$game."_".$server_id."_".$bg_id."_".$server_load_type.".png");*/
	}
	else
	{
		imagepng($image);
		imagedestroy($image);
	}
?>
