<?php

/**
* This class holds Environmental vars that can be used anywhere in the code
* For example the database object to perform mysql stuff
*/
class Env {
        private static $data = array();

        /**
        * Add the specified key=>value to the environmental data array
        *
        * @param string $key this is the identifier of the value you are adding
        * @param string $value The value to add into the array
        */
        public static function set($key, $value) {
                self::$data[$key] = $value;
        }

        /**
        * Gets the current value from the data array
        *
        * @param string $key The key to lookup in the array
        * @throws SteambansException if the variable cannot be found
        * @return mixed null if the key cannot be found, or the value that was stored in the array
        */
        public static function get($key) {
                return array_key_exists($key, self::$data) ? self::$data[$key] : null;
        }
}

/**
* Heatmap API to generate/add/update/delete content for heatmaps.
*
*/

class Heatmap {
        var $version = "0.1";

        /**
        * Stuff we want to fire when we start.
        *
        */
        public static function init () {
        		global $argv;
				DB::connect();
				self::mapinfo();
				self::parseArguments($argv);
        }

        /**
        * Function that builds the maplist from db elements.
        *
        */
        public static function mapinfo () {
                $query = 'SELECT
                                g.code,
								hc.game,
                                hc.map,
                                hc.xoffset,
                                hc.yoffset,
                                hc.flipx,
                                hc.flipy,
				hc.rotate,
                                hc.days,
                                hc.brush,
                                hc.scale,
                                hc.font,
                                hc.thumbw,
                                hc.thumbh,
                                hc.cropx1,
                                hc.cropx2,
                                hc.cropy1,
                                hc.cropy2
                        FROM
                                ' . DB_PREFIX . '_Games AS g
                        INNER JOIN
                                ' . DB_PREFIX . '_Heatmap_Config AS hc
                        ON
                                hc.game = g.realgame
                        WHERE 1=1
                        ORDER BY code ASC, game ASC, map ASC';

                $result = DB::doQuery($query);
                if (DB::numRows($result)) {
                        while ($row = DB::getAssoc($result)) {
                                foreach ($row as $key => $val) {
                                        $mapinfo[$row['code']][$row['map']][$key] = $val;
                                }
                        }

                        Env::set('mapinfo', $mapinfo);
                }
        }

        private static function printHeatDot($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){
                if(!isset($pct)){
                        return false;
                }
                $pct /= 100;

                // Get image width and height
                $w = imagesx( $src_im );
                $h = imagesy( $src_im );

                // Turn alpha blending off
                imagealphablending( $src_im, false );

                // Find the most opaque pixel in the image (the one with the smallest alpha value)
                $minalpha = 127;
                for( $x = 0; $x < $w; $x++ ) {
                        for( $y = 0; $y < $h; $y++ ){
                                $alpha = ( imagecolorat( $src_im, $x, $y ) >> 24 ) & 0xFF;
                                if( $alpha < $minalpha ){
                                        $minalpha = $alpha;
                                }
                        }
                }

                //loop through image pixels and modify alpha for each
                for( $x = 0; $x < $w; $x++ ){
                        for( $y = 0; $y < $h; $y++ ){
                                //get current alpha value (represents the TANSPARENCY!)
                                $colorxy = imagecolorat( $src_im, $x, $y );
                                $alpha = ( $colorxy >> 24 ) & 0xFF;

                                //calculate new alpha
                                if( $minalpha !== 127 ){
                                        $alpha = 127 + 127 * $pct * ( $alpha - 127 ) / ( 127 - $minalpha );
                                } else {
                                        $alpha += 127 * $pct;
                                }

                                //get the color index with new alpha
                                $alphacolorxy = imagecolorallocatealpha( $src_im, ( $colorxy >> 16 ) & 0xFF, ( $colorxy >> 8 ) & 0xFF, $colorxy & 0xFF, $alpha );

                                //set pixel with the new color + opacity
                                if( !imagesetpixel( $src_im, $x, $y, $alphacolorxy ) ){
                                        return false;
                                }
                        }
                }

                // The image copy
                imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
        }

		public static function generate($code, $map, $mode) {
			// generatemap($map, $code, "Total Kills", "HlstatsX:CE")
			self::buildQuery($code, $map);
			
			
			$mapinfo = Env::get('mapinfo');
			$map_query = Env::get('map_query');
			$disable_cache = Env::get('disable_cache');

			// See if we need to rotate the map or not.
			$rotate = $mapinfo[$code][$map]['rotate'];

			
			$timestamp = time();

			// Fix the last part of your path.
			$path = HLXCE_WEB . "/hlstatsimg/games/" . $mapinfo[$code][$map]['code'] . "/heatmaps";
			show::Event("PATH", $path, 3);

			// Does the source image exists? else there is no idea to spend resources on it.
			if (!file_exists(dirname(__FILE__) . "/src/" . $mapinfo[$code][$map]['game'] . "/" . $map . ".jpg")) {
				show::Event("FILE", dirname(__FILE__) . "/src/" . $mapinfo[$code][$map]['game'] . "/" . $map . ".jpg doesn't exists", 3);
				return false;
			}
									
			// Check that the dir exists, else try to create it.
			if (!is_dir($path)) {
				if (!@mkdir($path)) {
					show::Event("PREPARE", "Couln't create outputfolder: $path", 1);
				}
			}

			// Check if we have cached info, then we should work from that instead.
			if (is_dir(CACHE_DIR . "/$code")) {
			if ($handle = opendir(CACHE_DIR. "/$code")) {
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != ".." && preg_match(str_replace("\$","\\\$","/${map}_(\d+).png/i"), $file, $matches)) {
					$cache_file = CACHE_DIR . "/$code/$file";
					$oldtimestamp = $matches[1];

					// unless it's over 30 days old cache file, then we delete it and go from 0 again.
					if (floor((time() - $oldtimestamp) / 86400 > 30)) {
						$obsolite_cache = true;
						show::Event("CACHE", "Cache file is obsolite, " . floor((time() - $oldtimestamp) / 86400) . " days old. Generating from scratch", 1);
					}
					
					// If we called with --disable-cache we want to clean up and then regen from our start.
					if ($disable_cache || isset($obsolite_cache)) {
						$disable_cache = true;
						if (file_exists($cache_file)) {
							unlink($cache_file);
						}
					} else {
						show::Event("CACHE","Found cached file ($file), we will use timestamp $oldtimestamp instead", 1);
						$find = '/.*AND hef.eventTime >= FROM_UNIXTIME\([0-9]+\).*/i';
						$replace = '			AND hef.eventTime > FROM_UNIXTIME(' . $oldtimestamp . ')';
						$map_query = preg_replace($find, $replace, $map_query);
					}
					}
				}
				closedir($handle);
				}
			} else {
				if (!@mkdir(CACHE_DIR . "/$code")) {
					show::Event("CACHE", "Can't create cache_dir: " . CACHE_DIR . "/$code", 1);
				}
			}

			$result = DB::doQuery($map_query);
			$num_kills = DB::numRows($result);

			if (!$num_kills) {
				show::Event("IGNORE", "Game: $code, Map: $map, Kills: $num_kills, (to few kills)", 1);
				return false;
			}

			$firstdata = time();

			$img = imagecreatefromjpeg("./src/" . $mapinfo[$code][$map]['game'] . "/" . $map . ".jpg");
			imagealphablending($img, true);
			imagesavealpha($img, true);

			if (isset($cache_file) && !$disable_cache) {
				$overlay = imagecreatefrompng($cache_file);
			} else {
				$overlay = imagecreatetruecolor(imagesx($img), imagesy($img));
			}

			imagealphablending($overlay, true);
			imagesavealpha($overlay, true);

			$brush = imagecreatefrompng("./src/brush_" . $mapinfo[$code][$map]['brush'] . ".png");
			$brushsize = ($mapinfo[$code][$map]['brush'] == "large") ? 33 : 17;

			$white = imagecolorallocate($overlay, 255, 255, 255);
			$black = imagecolorallocate($overlay, 0, 0, 0);

			imagefill($overlay, 0, 0, $black);
			imagecolortransparent($overlay, $black);

			$num_kills = ($num_kills) ? $num_kills : 1;

			show::Event("CREATE", "Game: $code, Map: $map, Kills: $num_kills", 1);
			$opacity = intval((500 / $num_kills) * 100);

      
      if ($opacity > 40) $opacity = 40;
      if ($opacity < 1) $opacity = 2;


      $max_red = 0;
      $i = 0;
      while ($row = DB::getAssoc($result)) {
              if ($row['eventTime'] < $firstdata) $firstdata = $row['eventTime'];

              if ($mapinfo[$code][$map]['flipx']) $row['pos_x'] = $row['pos_x'] * -1;
              if ($mapinfo[$code][$map]['flipy']) $row['pos_y'] = $row['pos_y'] * -1;

              $x = ($row['pos_x'] + $mapinfo[$code][$map]['xoffset']) / $mapinfo[$code][$map]['scale'];
              $y = ($row['pos_y'] + $mapinfo[$code][$map]['yoffset']) / $mapinfo[$code][$map]['scale'];

              $rgb = imagecolorat($overlay, $x, $y);
              $colors = imagecolorsforindex($overlay, $rgb);

              if ($colors['red'] > $max_red) $max_red = $colors['red'];

              if ($colors['red'] <= 200) {
                      // Rotate the image
                      if ($rotate) {
                              self::printHeatDot($overlay, $brush, $y - ($brushsize / 2), $x - ($brushsize / 2), 0, 0, $brushsize, $brushsize, $opacity);
                      } else {
                              self::printHeatDot($overlay, $brush, $x - ($brushsize / 2), $y - ($brushsize / 2), 0, 0, $brushsize, $brushsize, $opacity);
                      }
              }
      }

			imagedestroy($brush);

			$colorarr = array();
			$colors = array(0, 0, 255);

			for ($line = 0; $line < 128; ++$line) {
				$colors = array(0, $colors[1] + 2, $colors[2] -2);
				$colorarr[$line] = $colors;
			}

			for ($line = 128; $line < 255; ++$line) {
				$colors = array($colors[0] + 2, $colors[1] -2, 0);
				$colorarr[$line] = $colors;
			}

			for ($x = 0; $x < imagesx($overlay); ++$x) {
				for ($y = 0; $y < imagesy($overlay); ++$y) {
					$index = imagecolorat($overlay, $x, $y);
					$rgb = imagecolorsforindex($overlay, $index);
					$alpha = ( imagecolorat( $overlay, $x, $y ) >> 24 ) & 0xFF;

					$color = imagecolorallocatealpha($img, $colorarr[$rgb['red']][0], $colorarr[$rgb['red']][1], $colorarr[$rgb['red']][2], 127 - ($rgb['red'] / 2));
				if (!imagesetpixel($img, $x, $y, $color)) echo ".";
				}
			}

			if ($mapinfo[$code][$map]['cropy2'] > 0 && $mapinfo[$code][$map]['cropy2'] > 0) {
				$temp = imagecreatetruecolor($mapinfo[$code][$map]['cropx2'], $mapinfo[$code][$map]['cropy2']);
				imagecopy($temp, $img, 0, 0, $mapinfo[$code][$map]['cropx1'], $mapinfo[$code][$map]['cropy1'], $mapinfo[$code][$map]['cropx2'], $mapinfo[$code][$map]['cropy2']);
				imagedestroy($img);

				$img = imagecreatetruecolor(imagesx($temp), imagesy($temp));
				imagecopy($img, $temp, 0, 0, 0, 0, imagesx($temp), imagesy($temp));
				imagedestroy($temp);
			}

			if ($mapinfo[$code][$map]['thumbw'] > 0 && $mapinfo[$code][$map]['thumbh'] > 0) {
				$thumb = imagecreatetruecolor(imagesx($img) * $mapinfo[$code][$map]['thumbw'], imagesy($img) * $mapinfo[$code][$map]['thumbh']);
				imagecopyresampled($thumb, $img, 0, 0, 0, 0, imagesx($thumb), imagesy($thumb), imagesx($img), imagesy($img));
				imagejpeg($thumb, $path . "/" . $map . "-" . $mode . "-thumb.jpg", 100);
				imagedestroy($thumb);
			}

			$img = self::drawHud($img, $map,  "HLX:CE", "Total Kills", $num_kills, $firstdata);
						
			if (imagejpeg($img, $path . "/" . $map . "-" . $mode . ".jpg", 100)) $return = true;
			if (imagepng($overlay, CACHE_DIR . "/$code/${map}_${timestamp}.png", 9)) $return = true;
			imagedestroy($overlay);

			// Clean upc cache file
			if (isset($cache_file) && file_exists($cache_file)) {
				unlink(CACHE_DIR . "/$code/${map}_${oldtimestamp}.png");
			}
			
			
			imagedestroy($img);

			return $return;
		}
		
		public static function buildQuery ($code, $map) {
			$mapinfo = Env::get('mapinfo');
			Env::set('code', $code);
			$ignore_infected = Env::get('ignore_infected');
			$timescope = (time() - 60*60*24*$mapinfo[$code][$map]['days']);

			$map_query = 'SELECT
							"frag" AS killtype,
					 hef.id,
							hef.map,
							hs.game,
							hef.eventTime,
							hef.pos_x,
							hef.pos_y
					FROM
							hlstats_Events_Frags as hef,
							hlstats_Servers as hs
					WHERE 1=1
					AND hef.map = "' . $map . '"
					AND hs.serverId = hef.serverId
					AND hs.game = "' . $code. '"
					AND hef.pos_x IS NOT NULL
					AND hef.pos_y IS NOT NULL
					AND hef.eventTime >= FROM_UNIXTIME(' . $timescope . ')
';
			if ($ignore_infected) {
					$map_query.= '                      AND hef.victimRole != "infected"
';
			}
					$map_query.= '                      LIMIT ' . KILL_LIMIT . '

					UNION ALL

					SELECT
							"teamkill" AS killtype,
							hef.id,
							hef.map,
							hs.game,
							hef.eventTime,
							hef.pos_x,
							hef.pos_y
					FROM
							hlstats_Events_Teamkills as hef,
							hlstats_Servers as hs
					WHERE 1=1
					AND hef.map = "' . $map . '"
					AND hs.serverId = hef.serverId
					AND hs.game = "' . $code. '"
					AND hef.pos_x IS NOT NULL
					AND hef.pos_y IS NOT NULL
					AND hef.eventTime >= FROM_UNIXTIME(' . $timescope . ')
					LIMIT ' . KILL_LIMIT . '
			';
			
			Env::set('map_query', $map_query);
			show::Event("SQL", $map_query, 3);

		}

		private static function drawHud ($img, $map, $heatmapname, $method, $num_kills, $firstdata) {
			$mapinfo = Env::get('mapinfo');
			$code = Env::get('code');
			
			
			// Resize the image according to your  settings
			$img = self::resize($img);

			$hudText = array(
					strtoupper($map) . " - " . strtoupper($heatmapname) . " HEATMAP - " . strtoupper($method),
					date("m/d/y", intval(time() - 60*60*24*30)) . " - " . date("m/d/y", time()),
					"Generated: " . date("Y-m-d H:i:s"),
					HUD_URL
				);
				
			show::Event("HUD", "Creating Overlay HUD", 2);

			$hudx = imagesx($img);
			$hudy = intval(intval($mapinfo[$code][$map]['font'] + 4) * intval(count($hudText) + 1) + 8);

			$hud = imagecreatetruecolor(imagesx($img), imagesy($img)) or die('Cannot Initialize new GD image stream');
			imagesavealpha($hud, true);

			$trans_colour = imagecolorallocatealpha($hud, 0, 0, 0, 127);
			$black = imagecolorallocatealpha($hud, 0, 0, 0, 90);

			imagefill($hud, 0, 0, $trans_colour);
			imagefilledrectangle($hud, 0, 0, imagesx($img) - 1, imagesy($img) - 1, $black);

			$font = "./DejaVuSans.ttf";

			// Copy the hud to the top of the image.
			imagecopy($img, $hud, 0, 0, 0, 0, $hudx, $hudy);

			//array imagettftext  ( resource $image  , float $size  , float $angle  , int $x  , int $y  , int $color  , string $fontfile  , string $text  )
			$i = 1;
			foreach ($hudText as $text) {
			imagettftext(	$img, 
					$mapinfo[$code][$map]['font'], 
					0, 
					10, 
					intval(intval($mapinfo[$code][$map]['font'] + 4) * $i + 8), 
					imagecolorallocate($img, 255, 255, 255), 
					$font, 
					$text
				);
			$i++;
			}

			imagedestroy($hud);

			show::Event("HUD", "Done...", 2);
			return $img;
		}
		
		private static function resize ($img) {
			switch (OUTPUT_SIZE) {
				case "small":
					$newwidth = 800;
					$newheight = 600;
					break;
				case "medium":
					$newwidth = 1024;
					$newheight = 768;
					break;
				case "large":
					$newwidth = 1280;
					$newheight = 1024;
					// As for now we don't do anything since this is default size
					return $img;
					break;
				default:
					$newwidth = 1024;
					$newheight = 768;
			}
			
			show::Event("RESIZE", "Adjusting Heatmap to current setting: " . OUTPUT_SIZE, 2);

			$resized = imagecreatetruecolor($newwidth, $newheight);
			imagecopyresized($resized, $img, 0, 0, 0, 0, $newwidth, $newheight, imagesx($img), imagesy($img));

			imagedestroy($img);

			show::Event("RESIZE", "Done...", 2);
			return $resized;
		}		

		private static function arguments($argv) {
			$_ARG = array();
			foreach ($argv as $arg) {
				if (preg_match('/\-\-[a-zA-Z0-9]*=.*/', $arg)) {
					$str = explode('=', $arg);
					$arg = '';
					$key = preg_replace('/\-\-/', '', $str[0]);
					for ( $i = 1; $i < count($str); $i++ ) {
						$arg .= $str[$i];
					}
					$_ARG[$key] = $arg;
				} elseif(preg_match('/\-[a-zA-Z0-9]/', $arg)) {
					$arg = preg_replace('/\-/', '', $arg);
					$_ARG[$arg] = 'true';
				}
			}
			return $_ARG;
		}
		
		public static function parseArguments($argv) {
			$mapinfo = Env::get('mapinfo');
			$cache = false;
			$args = self::arguments($argv);
			
			if (isset($args['game'])) {
				if (!isset($mapinfo[$args['game']])) {
					show::Event("ERROR", "Game: " . $args['game'] . " doesn't exists, escaping", 1);
					exit;
				}
				
				if (isset($args['map'])) {
					if (!isset($mapinfo[$args['game']][$args['map']])) {
						show::Event("ERROR", "Game: " . $args['game'] . " Map: " . $args['map'] . " doesn't exists, escaping", 1);
						exit;
					}
					
					$tmp[$args['game']][$args['map']] = $mapinfo[$args['game']][$args['map']];
					show::Event("ARGS", "--game=" . $args['game'], 2);
					show::Event("ARGS", "--map=" . $args['map'], 2);
				} else {
					$tmp[$args['game']] = $mapinfo[$args['game']];
					show::Event("ARGS", "--game=" . $args['game'], 2);
				}
			} else {
				$visible = '';
				$query = "SELECT code FROM hlstats_Games WHERE hidden='0'";
				$result = DB::doQuery($query);
				if (DB::numRows($result)) {
					while ($row = DB::getAssoc($result)) {
						foreach ($row as $key => $val) {
							if (isset($mapinfo[$val])) {
								$visible .= "$val, ";	
								$tmp[$val] = $mapinfo[$val];
							}
						}
					}
				}
				show::Event("GAMES", substr($visible, 0, -2), 2);
			}
			
			if (isset($tmp)) { 
				$mapinfo = $tmp; 
			}

			if (isset($args['disablecache'])) {
				$cache = true;
				show::Event("ARGS", "--disable-cache=true", 2);
			} else {
				$cache = false;
				show::Event("ARGS", "--disable-cache=false", 2);
			}
			
			if (isset($args['ignoreinfected'])) {
				$ignore_infected = true;
				show::Event("ARGS", "--ignore-infected=true", 2);
			} else {
				$ignore_infected = false;
				show::Event("ARGS", "--ignore-infected=false", 2);
			}

			Env::set('mapinfo', $mapinfo);
			Env::set('disable_cache', $cache);
			Env::set('ignore_infected', $ignore_infected);
		}

// End of Heat Class
}



class DB {
	public static function connect () {
		mysql_connect(DB_HOST, DB_USER, DB_PASS);
		mysql_select_db(DB_NAME);
		show::Event("DB", "Connected to " . DB_NAME . " as " . DB_USER . "@" . DB_HOST, 1);
	}

	public static function doQuery ($query) {
		return mysql_query($query);
	}

	public static function getAssoc ($result) {
		return  mysql_fetch_assoc($result);
	}

	public static function numRows ($result) {
		return mysql_num_rows($result);
	}
}

class show {
	public static function Event ($type, $text, $runlevel) {
		if ($runlevel <= DEBUG) {	
			print date("Y-m-d H:i:s") . "\t\t$type: $text\n";
		}     
	}
}

?>
