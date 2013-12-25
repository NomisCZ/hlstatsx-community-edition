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

    function image_dashed_line($image, $x1, $y1, $x2, $y2, $style)
	{
		if (count($style)>0) {
			$temp_x1 = $x1;
			$temp_y1 = $y1;
			$count   = 0;
			while (($temp_x1<$x2) || ($temp_y1<$y2)) {
				$my_style = $style[$count % count($style)];
				$step = 0;
				while ($my_style != $style[(($count+$step) % count($style))]) {
					$step++;
				}
				if ($step==0)
					$step++;

				if ($x1 != $x2) {
					if ($my_style != -1)
						imageline($image, $temp_x1, $y1, $temp_x1+$step, $y2, $my_style);
					$temp_x1 = $temp_x1 + $step;
				}
				if ($y1!=$y2) {
					if ($my_style != -1)
						imageline($image, $x1, $temp_y1, $x2, $temp_y1+$step, $my_style);
					$temp_y1 = $temp_y1 + $step;
				}
				$count = $count + $step;
			}
		} else {
			imageline($image, $x1, $y1, $x2, $y2, $style[0]);
		}
    }

//TSGK
    $first_entry = 0;
//TSGK

    function drawItems($image, $bounds, $data_array, $max_index, $name, $dot, $make_grid, $write_timestamp, $write_legend, $color)
	{
		global $max_pos_y;
		global $legend_x;
		global $bar_type;
		global $deletedays;
		global $server_load_type; 
//TSGK
		global $first_entry;
		$first_entry++;
//TSGK
    
		if (!isset($max_pos_y[$max_index])) {
			$max_pos_y[$max_index] = 0; 
		}  



		foreach ($data_array as $entry) {
			if ($entry[$name] >= $max_pos_y[$max_index]) {
				if ($entry[$name] < 100)
					$max_pos_y[$max_index] = $entry[$name] + 2;
				else if ($entry[$name] < 200) 
					$max_pos_y[$max_index] = $entry[$name] + 10;
				else if ($entry[$name] < 10000)
					$max_pos_y[$max_index] = $entry[$name] + ($entry[$name] * 0.3);
				else
					$max_pos_y[$max_index] = $entry[$name] - ($entry[$name] % 50000) + 100000;


				if ($make_grid > 0) {
					if ($max_pos_y[$max_index] % 2 != 0)
						$max_pos_y[$max_index]++;
					$i = 0;
					while (($i < 10) && ($max_pos_y[$max_index] % 4 != 0)) {
						$max_pos_y[$max_index]++;
						$i++;
					}
				}

				if ($max_pos_y[$max_index] == 0)
					$max_pos_y[$max_index] = 1;
                
			}
		}
      
      
		if ($write_legend > 0) {
			if ($legend_x == 0)
				$legend_x += $bounds['indent_x'][0] + 10;
			imagesetpixel($image, $legend_x,   $bounds['indent_y'][0]-7, $color[0]);
			imagesetpixel($image, $legend_x+1, $bounds['indent_y'][0]-7, $color[0]);
			imagesetpixel($image, $legend_x+2, $bounds['indent_y'][0]-7, $color[0]);
			imagesetpixel($image, $legend_x,   $bounds['indent_y'][0]-8, $color[0]);
			imagesetpixel($image, $legend_x+1, $bounds['indent_y'][0]-8, $color[0]);
			imagesetpixel($image, $legend_x+2, $bounds['indent_y'][0]-8, $color[0]);
			imagesetpixel($image, $legend_x,   $bounds['indent_y'][0]-9, $color[0]);
			imagesetpixel($image, $legend_x+1, $bounds['indent_y'][0]-9, $color[0]);
			imagesetpixel($image, $legend_x+2, $bounds['indent_y'][0]-9, $color[0]);
			$legend_x += 7;
			imagestring($image, 1, $legend_x, $bounds['indent_y'][0]-11, $name , $color[2]);
			$legend_x += (imagefontwidth(1) * strlen($name)) + 7;
		}
      
		$start_pos = array("x" => $bounds['width']-$bounds['indent_x'][1], "y" => $bounds['indent_y'][1]);
       
		$pos   = $start_pos;
		$cache = array("x" => 0, "y" => 0);
      
		$step_y = ($bounds['height']-$bounds['indent_y'][0]-$bounds['indent_y'][1]) / 10;
		if ($step_y < 15)
			$step_y = 15;

// TSGK MAP TEXT
		$last_map          = ""; 
		$last_map_posx     = 0;
		$bk_color          = 0;

		global $gray_border;
        
		if (($first_entry == 1) && ($make_grid > 0) && ($server_load_type == 1)) {
			foreach ($data_array as $key => $entry)  {
				if (($entry['map'] !== $last_map) || ($pos['x'] <= ($bounds['indent_x'][0]+1) )) {
					if ($last_map == "") {
						$last_map      = $entry['map'];
						$last_map_posx = $pos['x']; 
					} else {    
						$last_map = $entry['map'];
						while ($last_map_posx > $pos['x']) {
							if (($bk_color % 2) == 0) {
								imageline($image, $last_map_posx, $bounds['indent_y'][0]+1, $last_map_posx, $bounds['height']-$bounds['indent_y'][1]-1, $color[4]);
							}
							$last_map_posx--;
						}
						$bk_color++;
						imageline($image, $pos['x']+1, $bounds['indent_y'][0]+1, $pos['x']+1, $bounds['height']-$bounds['indent_y'][1]-1, $gray_border);
						$last_map_posx = $pos['x']; 
					}
				}
				$pos['x'] -= 3;
				if ($pos['x'] < $bounds['indent_x'][0])
					break;
			}
        }
// END TSGK MAP TEXT
        
		if ($make_grid > 0) {
			$step_diff  = 0;
			$step_width = 0;
			while ($step_diff < 15) {
				$step_width++;
				if ($max_pos_y[$max_index] % $step_width == 0) {
					$steps = $max_pos_y[$max_index] / $step_width;
					if ($steps > 0)
						$step_diff = ($bounds['height']-$bounds['indent_y'][0]-$bounds['indent_y'][1]) / $steps;
					else
						$step_diff = 15;
				} else {
					$step_diff = 0;
				}
			}
        
			for ($i=1; $i<$steps; $i++) {
				$temp_y = (($bounds['height']-$bounds['indent_y'][0]-$bounds['indent_y'][1]) - ((($bounds['height']-$bounds['indent_y'][0]-$bounds['indent_y'][1]) / $max_pos_y[$max_index]) * ($i*$step_width))) + $bounds['indent_y'][0];
				if ($temp_y > $bounds['indent_y'][0]+5)
					image_dashed_line($image, $bounds['indent_x'][0]+1, 
						$temp_y, 
						($bounds['width']-$bounds['indent_x'][1]-1), 
						$temp_y, 
						array($color[4], $color[4], $color[4], -1, -1, -1));
				imageline($image, $bounds['indent_x'][0]+1, $temp_y, $bounds['indent_x'][0]+4, $temp_y, $color[3]);

				if ($max_pos_y[$max_index] > 10000) {
					$str     = sprintf("%.0fk", ($i*$step_width) / 1000);
				} else {
					$str     = sprintf("%.0f", $i*$step_width);
				}  
				$str_pos = $bounds['indent_x'][0] - (imagefontwidth(1) * strlen($str)) - 2;
				imagestring($image, 1, $str_pos, $temp_y-3, $str, $color[2]);
			}
        
			if ($max_pos_y[$max_index] > 10000)
				$str     = sprintf("%.0fk", $max_pos_y[$max_index] / 1000);
			else  
				$str     = sprintf("%.0f", $max_pos_y[$max_index]);
			$str_pos = $bounds['indent_x'][0] - (imagefontwidth(1) * strlen($str)) - 2;
			imagestring($image, 1, $str_pos, $bounds['indent_y'][0]-3, $str, $color[2]);
		}
      
		$last_month           = 0;
		$last_month_timestamp = 0;
		$last_day             = 0;
		$last_day_timestamp   = 0;
		$first_timestamp      = 0;
		$first_day            = 0;

		switch ($server_load_type)  {
			case 1 : $mov_avg_precision = 5;
				break; 
			case 2 : $mov_avg_precision = 5;
				break; 
			case 3 : $mov_avg_precision = 10;
				break; 
			case 4 : $mov_avg_precision = 10;
				break; 
			default: $mov_avg_precision = 1;
				break; 
		}
      
		$mov_avg_array        = array();
		$mov_avg_value        = 0;
		$mov_avg_display_value = array();

		$i = 0;
		while (($i < count($data_array)) && ($i < $mov_avg_precision / 2)) {
			$entry = $data_array[$i];
			$mov_avg_array[] = (($bounds['height']-$bounds['indent_y'][0]-$bounds['indent_y'][1]) - ((($bounds['height']-$bounds['indent_y'][0]-$bounds['indent_y'][1]) / $max_pos_y[$max_index]) * $entry[$name])) + $bounds['indent_y'][0];
			$mov_avg_display_value[] = $entry[$name];
			$i++;
		}

// TSGK
		$last_map          = ""; 
		$last_map_posx     = 0;
		$bk_color          = 0;
// TSGK

		foreach ($data_array as $key => $entry) {

			$pos['y'] = (($bounds['height']-$bounds['indent_y'][0]-$bounds['indent_y'][1]) - ((($bounds['height']-$bounds['indent_y'][0]-$bounds['indent_y'][1]) / $max_pos_y[$max_index]) * $entry[$name])) + $bounds['indent_y'][0];


// TSGK
			$maptext = imagecolorallocate($image, 96, 96, 96);
			if(!isset($entry['map']))
				$entry['map']="";
			if (($first_entry == 2) && ($server_load_type == 1)) {
				if ($entry['map'] !== $last_map) {
					if ($last_map == "") {
						$last_map      = $entry['map'];
						$last_map_posx = $pos['x'];
						if ($entry['map'] != "") { 
							$str_height = $bounds['indent_y'][0] + (imagefontwidth(1) * strlen($entry['map'])) + 2;
							imagestringup($image, 1, $pos['x']-8, $str_height, $entry['map'], $maptext);
						}
					} else {
						$last_map = $entry['map'];
						$str_height = $bounds['indent_y'][0] + (imagefontwidth(1) * strlen($entry['map'])) + 2;
						imagestringup($image, 1, $pos['x']-8, $str_height, $entry['map'], $maptext);
						$last_map_posx = $pos['x'];
					}
				}
			}
// TSGK

			$mov_avg_array[] = $pos['y'];
			$mov_avg_value   = $pos['y'];
        
			if (count($mov_avg_array) > $mov_avg_precision)
				array_shift($mov_avg_array);
			$mov_avg_sum         = 0;
			$mov_avg_display_sum = 0;
			foreach ($mov_avg_array as $mov_avg_entry)
				$mov_avg_sum += $mov_avg_entry;
			$mov_avg_value = sprintf("%d", ($mov_avg_sum / count($mov_avg_array)));
			$pos['y']   = $mov_avg_value;
        
			if ($key > 0) {
				imageline($image, $cache['x'], $cache['y'], $pos['x'], $pos['y'], $color[0]);  
			}
        
			if ($key == 0) {
				foreach ($mov_avg_display_value as $mov_avg_display_entry)
					$mov_avg_display_sum += $mov_avg_display_entry;
				$display_value = sprintf("%d", ($mov_avg_display_sum / count($mov_avg_display_value)));
				if ($display_value > 10000)
					$str     = sprintf("%.1fk", $display_value / 1000);
				else
					if ($name !== "act_players")
						$str     = sprintf("%.0f", $display_value);
					else  
						$str     = sprintf("%.1f", $display_value);
				imagestring($image, 1, $pos['x']+2, $pos['y']-4, $str, $color[2]);
			}
        
			if ($first_timestamp == 0)
				$first_timestamp = $entry['timestamp'];
			$this_month = date("m", $entry['timestamp']);
			if ($this_month > $last_month+1)
				$last_month = $this_month+1;
			if ($last_month == 0) {
				$last_month           = $this_month;
				$last_month_timestamp = $entry['timestamp'];
			}
			if ($last_month == $this_month)
				$last_month_timestamp = $entry['timestamp'];
          
			$this_day = date("d", $entry['timestamp']);
			if ($this_day > $last_day+1)
				$last_day = $this_day+1;
			if ($last_day == 0) {
				$last_day           = $this_day;
				$last_day_timestamp = $entry['timestamp'];
			}
			if ($last_day == $this_day)
				$last_day_timestamp = $entry['timestamp'];

			switch ($server_load_type)  {
				case 1:
					if (($write_timestamp > 0) && ($key > 0 && $key % 12 == 0))  {
						image_dashed_line($image, $pos['x'], $pos['y'], $pos['x'], $bounds['height']-$bounds['indent_y'][1], array($color[1], $color[1], $color[1], -1, -1, -1));
						$str = date("H:i", $entry['timestamp']);
						imagestring($image, 1, $pos['x']-10, $bounds['height']-$bounds['indent_y'][1]+3, $str, $color[2]);
					}
					break;
					
				case 2:
					if (($write_timestamp > 0) && ($last_day > $this_day)) {
						$last_day = $this_day; 
						if ($bounds['width']-$bounds['indent_x'][1]-$pos['x'] > 120)
							$first_day++;
						if ($first_day > 0) { 
							image_dashed_line($image, $pos['x'], $pos['y'], $pos['x'], $bounds['height']-$bounds['indent_y'][1], array($color[1], $color[1], $color[1], -1, -1, -1));
							$first_day++;  
							if ($last_day_timestamp == 0)
								$last_day_timestamp = $first_timestamp;
							$str = date("l", $last_day_timestamp);
							imagestring($image, 1, $pos['x']-15, $bounds['height']-$bounds['indent_y'][1]+3, $str, $color[2]);
						}
						$first_day++;
					}
                    break;
					
				case 3:
					if (($write_timestamp > 0) && ($last_day > $this_day)) {
						$last_day = $this_day; 
						if ($bounds['width']-$bounds['indent_x'][1]-$pos['x'] > 0)
							$first_day++;
						if ($first_day > 0) { 
							image_dashed_line($image, $pos['x'], $pos['y'], $pos['x'], $bounds['height']-$bounds['indent_y'][1], array($color[1], $color[1], $color[1], -1, -1, -1));
							if ($last_day_timestamp == 0)
								$last_day_timestamp = $first_timestamp;
							$str = date("d", $last_day_timestamp);
							imagestring($image, 1, $pos['x']-5, $bounds['height']-$bounds['indent_y'][1]+3, $str, $color[2]);
						}
                    }
                    break;
					
				case 4:
					if (($write_timestamp > 0) && ($last_month > $this_month)) {
						$last_month = $this_month; 
						if ($bounds['width']-$bounds['indent_x'][1]-$pos['x'] > 30)
							$first_day++;
						if ($first_day > 0) { 
							image_dashed_line($image, $pos['x'], $pos['y'], $pos['x'], $bounds['height']-$bounds['indent_y'][1], array($color[1], $color[1], $color[1], -1, -1, -1));
							$first_day++;  
							if ($last_month_timestamp == 0)
								$last_month_timestamp = $first_timestamp;
							$str = date("M", $last_month_timestamp);
							imagestring($image, 1, $pos['x']-5, $bounds['height']-$bounds['indent_y'][1]+3, $str, $color[2]);
						}
						$first_day++;
                    }
                    break;
					
				default:
					if (($write_timestamp > 0) && ($key > 0 && $key % 12 == 0)) {
						image_dashed_line($image, $pos['x'], $pos['y'], $pos['x'], $bounds['height']-$bounds['indent_y'][1], array($color[1], $color[1], $color[1], -1, -1, -1));
						$str = date("H:i", $entry['timestamp']);
						imagestring($image, 1, $pos['x']-10, $bounds['height']-$bounds['indent_y'][1]+3, $str, $color[2]);
                    }
                    break;
			}
                        
			if ($dot > 0) {
				imagesetpixel($image, $pos['x'],   $pos['y'],   $color[0]);
				imagesetpixel($image, $pos['x']-1, $pos['y'],   $color[0]);
				imagesetpixel($image, $pos['x']-1, $pos['y']-1, $color[0]);
				imagesetpixel($image, $pos['x']-1, $pos['y']+1, $color[0]);
				imagesetpixel($image, $pos['x']+1, $pos['y'],   $color[0]);
				imagesetpixel($image, $pos['x']+1, $pos['y']-1, $color[0]);
				imagesetpixel($image, $pos['x']+1, $pos['y']+1, $color[0]);
				imagesetpixel($image, $pos['x'],   $pos['y']-1, $color[0]);
				imagesetpixel($image, $pos['x'],   $pos['y']+1, $color[0]);
			} else {
				imagesetpixel($image, $pos['x'],   $pos['y'],   $color[0]);
			}
        
			$cache['x'] = $pos['x'];
			$cache['y'] = $pos['y'];
        
			$step_x = 3;
        
			if ($bar_type==2) {
				// skalieren auf anzahl Tage
				$step_x = round( ($bounds['width']-$bounds['indent_x'][1]-$bounds['indent_x'][0]) / $deletedays );
			}

			if ($bar_type==3 || $bar_type==4) {
				// skalieren 
				$step_x = 5;
			}

			$pos['x'] -= $step_x;
			if ($pos['x'] < $bounds['indent_x'][0])
				break;
		}
    }
?>