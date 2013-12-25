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

if (!defined('IN_HLSTATS'))
{
	die('Do not access this file directly.');
}

/**
 * getOptions()
 * 
 * @return Array All the options from the options/perlconfig table
 */
function getOptions()
{
	global $db;
	$result = $db->query("SELECT `keyname`,`value` FROM hlstats_Options WHERE opttype >= 1");
	while ($rowdata = $db->fetch_row($result))
	{
		$options[$rowdata[0]] = $rowdata[1];
	}
	if ( !count($options) )
	{
		error('Warning: Could not find any options in table <b>hlstats_Options</b>, database <b>' .
			DB_NAME . '</b>. Check HLstats configuration.');
	}
	$options['MinActivity'] = $options['MinActivity'] * 86400;
	return $options;
}

// Test if flags exists
/**
 * getFlag()
 * 
 * @param string $flag
 * @param string $type
 * @return string Either the flag or default flag if none exists
 */
function getFlag($flag, $type='url')
{
	$image = getImage('/flags/'.strtolower($flag));
	if ($image)
		return $image[$type];
	else
		return IMAGE_PATH.'/flags/0.gif';
}

/**
 * valid_request()
 * 
 * @param string $str
 * @param boolean $numeric
 * @return mixed request
 */
function valid_request($str, $numeric = false)
{
	$search_pattern = array("/[^A-Za-z0-9\[\]*.,=()!\"$%&^`´':;ß²³#+~_\-|<>\/\\\\@{}äöüÄÖÜ ]/");
	$replace_pattern = array('');
	$str = preg_replace($search_pattern, $replace_pattern, $str);
	if ( $numeric == false )
	{
		if ( get_magic_quotes_gpc() )
			return $str = htmlspecialchars(stripslashes($str), ENT_QUOTES);
		else
			return $str = htmlspecialchars($str, ENT_QUOTES);
	}
	else
	{
		if ( is_numeric($str) )
			return intval($str);
		else
			return -1;
	}
}

/**
 * timestamp_to_str()
 * 
 * @param integer $timestamp
 * @return string Formatted Timestamp
 */
function timestamp_to_str($timestamp)
{
	if ($timestamp != '')
	{
		return sprintf('%dd&nbsp;%02d:%02d:%02dh', $timestamp / 86400, $timestamp / 3600 % 24, $timestamp /
			60 % 60, $timestamp % 60);
	}
	return '-';
}

/**
 * error()
 * Formats and outputs the given error message. Optionally terminates script
 * processing.
 * 
 * @param mixed $message
 * @param bool $exit
 * @return void
 */
function error($message, $exit = true)
{
	global $g_options;
?>
<table border="1" cellspacing="0" cellpadding="5">
<tr>
<td class="errorhead">ERROR</td>
</tr>
<tr>
<td class="errortext"><?php echo $message; ?></td>
</tr>
</table>
<?php if ($exit)
		exit;
}


//
// string makeQueryString (string key, string value, [array notkeys])
//
// Generates an HTTP GET query string from the current HTTP GET variables,
// plus the given 'key' and 'value' pair. Any current HTTP GET variables
// whose keys appear in the 'notkeys' array, or are the same as 'key', will
// be excluded from the returned query string.
//

/**
 * makeQueryString()
 * 
 * @param mixed $key
 * @param mixed $value
 * @param mixed $notkeys
 * @return
 */
function makeQueryString($key, $value, $notkeys = array())
{
	if (!is_array($notkeys))
		$notkeys = array();
	
	$querystring = '';
	foreach ($_GET as $k => $v)
	{
		$v = valid_request($v, 0);
		if ($k && $k != $key && !in_array($k, $notkeys))
		{
			$querystring .= urlencode($k) . '=' . rawurlencode($v) . '&amp;';
		}
	}

	$querystring .= urlencode($key) . '=' . urlencode($value);

	return $querystring;
}

//
// void pageHeader (array title, array location)
//
// Prints the page heading.
//

/**
 * pageHeader()
 * 
 * @param mixed $title
 * @param mixed $location
 * @return
 */
function pageHeader($title = '', $location = '')
{
	global $db, $g_options;
	if ( defined('PAGE') && PAGE == 'HLSTATS' )
		include (PAGE_PATH . '/header.php');
	elseif ( defined('PAGE') && PAGE == 'INGAME' )
		include (PAGE_PATH . '/ingame/header.php');
}


//
// void pageFooter (void)
//
// Prints the page footer.
//

/**
 * pageFooter()
 * 
 * @return
 */
function pageFooter()
{
	global $g_options;
	if ( defined('PAGE') && PAGE == 'HLSTATS' )
		include (PAGE_PATH . '/footer.php');
	elseif ( defined('PAGE') && PAGE == 'INGAME' )
		include (PAGE_PATH . '/ingame/footer.php');
}

/**
 * getSortArrow()
 * 
 * @param mixed $sort
 * @param mixed $sortorder
 * @param mixed $name
 * @param mixed $longname
 * @param string $var_sort
 * @param string $var_sortorder
 * @param string $sorthash
 * @return string Returns the code for a sort arrow <IMG> tag.
 */
function getSortArrow($sort, $sortorder, $name, $longname, $var_sort = 'sort', $var_sortorder =
	'sortorder', $sorthash = '', $ajax = false)
{
	global $g_options;

	if ($sortorder == 'asc')
	{
		$sortimg = 'sort-ascending.gif';
		$othersortorder = 'desc';
	}
	else
	{
		$sortimg = 'sort-descending.gif';
		$othersortorder = 'asc';
	}
	
	$arrowstring = '<a href="' . $g_options['scripturl'] . '?' . makeQueryString($var_sort, $name,
		array($var_sortorder));

	if ($sort == $name)
	{
		$arrowstring .= "&amp;$var_sortorder=$othersortorder";
		$jsarrow = "'" . $var_sortorder . "': '" . $othersortorder . "'";
	}
	else
	{
		$arrowstring .= "&amp;$var_sortorder=$sortorder";
		$jsarrow = "'" . $var_sortorder . "': '" . $sortorder . "'";
	}

	if ($sorthash)
	{
		$arrowstring .= "#$sorthash";
	}

	$arrowstring .= '" class="head"';
	
	if ( $ajax )
	{
		$arrowstring .= " onclick=\"Tabs.refreshTab({'$var_sort': '$name', $jsarrow}); return false;\"";
	}
	
	$arrowstring .= ' title="Change sorting order">' . "$longname</a>";

	if ($sort == $name)
	{
		$arrowstring .= '&nbsp;<img src="' . IMAGE_PATH . "/$sortimg\"" .
			" style=\"padding-left:4px;padding-right:4px;\" alt=\"$sortimg\" />";
	}


	return $arrowstring;
}

/**
 * getSelect()
 * Returns the HTML for a SELECT box, generated using the 'values' array.
 * Each key in the array should be a OPTION VALUE, while each value in the
 * array should be a corresponding descriptive name for the OPTION.
 * 
 * @param mixed $name
 * @param mixed $values
 * @param string $currentvalue
 * @return The 'currentvalue' will be given the SELECTED attribute.
 */
function getSelect($name, $values, $currentvalue = '')
{
	$select = "<select name=\"$name\" style=\"width:300px;\">\n";

	$gotcval = false;

	foreach ($values as $k => $v)
	{
		$select .= "\t<option value=\"$k\"";

		if ($k == $currentvalue)
		{
			$select .= ' selected="selected"';
			$gotcval = true;
		}

		$select .= ">$v</option>\n";
	}

	if ($currentvalue && !$gotcval)
	{
		$select .= "\t<option value=\"$currentvalue\" selected=\"selected\">$currentvalue</option>\n";
	}

	$select .= '</select>';

	return $select;
}

/**
 * getLink()
 * 
 * @param mixed $url
 * @param integer $maxlength
 * @param string $type
 * @param string $target
 * @return
 */
 
function getLink($url, $type = 'http://', $target = '_blank')
{
	$urld=parse_url($url);

	if(!isset($urld['scheme']) && (!isset($urld['host']) && isset($urld['path'])))
	{
			$urld['scheme']=str_replace('://', '', $type);
			$urld['host']=$urld['path'];
			unset($urld['path']);
	}

	if($urld['scheme']!='http' && $urld['scheme']!='https')
	{
			return 'Invalid Url :(';
	}

	if(!isset($urld['path']))
	{
			$urld['path']='';
	}

	if(!isset($urld['query']))
	{
			$urld['query']='';
	}
	else
	{
			$urld['query']='?' . urlencode($urld['query']);
	}

	if(!isset($urld['fragment']))
	{
			$urld['fragment']='';
	}
	else
	{
			$urld['fragment']='#' . urlencode($urld['fragment']);
	}

	$uri=sprintf("%s%s%s", $urld['path'], $urld['query'], $urld['fragment']);
	$host_uri=$urld['host'] . $uri;
	return sprintf('<a href="%s://%s%s" target="%s">%s</a>',$urld['scheme'], $urld['host'], $uri, $target, htmlspecialchars($host_uri, ENT_COMPAT));
}

/**
 * getEmailLink()
 * 
 * @param string $email
 * @param integer $maxlength
 * @return string Formatted email tag
 */
function getEmailLink($email, $maxlength = 40)
{
	if (preg_match('/(.+)@(.+)/', $email, $regs))
	{
		if (strlen($email) > $maxlength)
		{
			$email_title = substr($email, 0, $maxlength - 3) . '...';
		}
		else
		{
			$email_title = $email;
		}

		$email = str_replace('"', urlencode('"'), $email);
		$email = str_replace('<', urlencode('<'), $email);
		$email = str_replace('>', urlencode('>'), $email);

		return "<a href=\"mailto:$email\">" . htmlspecialchars($email_title, ENT_COMPAT) . '</a>';
	}

	else
	{
		return '';
	}
}

/**
 * getImage()
 * 
 * @param string $filename
 * @return mixed Either the image if exists, or false otherwise
 */
function getImage($filename)
{
	preg_match('/^(.*\/)(.+)$/', $filename, $matches);
	$relpath = $matches[1];
	$realfilename = $matches[2];
	
	$path = IMAGE_PATH . $filename;
	$url = IMAGE_PATH . $relpath . rawurlencode($realfilename);

	// check if image exists
	if (file_exists($path . '.png'))
	{
		$ext = 'png';
	} elseif (file_exists($path . '.gif'))
	{
		$ext = 'gif';
	} elseif (file_exists($path . '.jpg'))
	{
		$ext = 'jpg';
	}
	else
	{
		$ext = '';
	}

	if ($ext)
	{
		$size = getImageSize("$path.$ext");

		return array('url' => "$url.$ext", 'path' => "$path.$ext", 'width' => $size[0], 'height' => $size[1],
			'size' => $size[3]);
	}
	else
	{
		return false;
	}
}

function mystripslashes($text)
{
	return get_magic_quotes_gpc() ? stripslashes($text) : $text;
}

function getRealGame($game)
{
	global $db;
	$result = $db->query("SELECT realgame from hlstats_Games WHERE code='$game'");
	list($realgame) = $db->fetch_row($result);
	return $realgame;
}

function printSectionTitle($title)
{
	echo '<span class="fHeading">&nbsp;<img src="'.IMAGE_PATH."/downarrow.gif\" alt=\"\" />&nbsp;$title</span><br /><br />\n";
}

function getStyleText($style)
{
	return "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/$style.css\" />\n";
}

function getJSText($js)
{
	return "\t<script type=\"text/javascript\" src=\"".INCLUDE_PATH."/js/$js.js\"></script> \n";
}

function get_player_rank($playerdata) {
	global $db, $g_options;
	
	$rank = 0;
	$tempdeaths = $playerdata['deaths'];
	if ($tempdeaths == 0)
		$tempdeaths = 1;

	$query = "
		SELECT
			COUNT(*)
		FROM
			hlstats_Players
		WHERE
			game='".$playerdata['game']."'
			AND hideranking = 0
			AND kills >= 1
			AND (
					(".$g_options['rankingtype']." > '".$playerdata[$g_options['rankingtype']]."') OR (
						(".$g_options['rankingtype']." = '".$playerdata[$g_options['rankingtype']]."') AND (kills/IF(deaths=0,1,deaths) > ".($playerdata['kills']/$tempdeaths).")
					)
			)
	";
	$db->query($query);
	list($rank) = $db->fetch_row();
	$rank++;

	return $rank;
}

if (!function_exists('file_get_contents')) {
      function file_get_contents($filename, $incpath = false, $resource_context = null)
      {
          if (false === $fh = fopen($filename, 'rb', $incpath)) {
              trigger_error('file_get_contents() failed to open stream: No such file or directory', E_USER_WARNING);
              return false;
          }
  
          clearstatcache();
          if ($fsize = @filesize($filename)) {
              $data = fread($fh, $fsize);
          } else {
              $data = '';
              while (!feof($fh)) {
                  $data .= fread($fh, 8192);
              }
          }
  
          fclose($fh);
          return $data;
      }
}

/**
 * Convert colors Usage:  color::hex2rgb("FFFFFF")
 * 
 * @author      Tim Johannessen <root@it.dk>
 * @version    1.0.1
 */
function hex2rgb($hexVal = '')
{
	$hexVal = preg_replace('[^a-fA-F0-9]', '', $hexVal);
	if (strlen($hexVal) != 6)
	{
		return 'ERR: Incorrect colorcode, expecting 6 chars (a-f, 0-9)';
	}
	$arrTmp = explode(' ', chunk_split($hexVal, 2, ' '));
	$arrTmp = array_map('hexdec', $arrTmp);
	return array('red' => $arrTmp[0], 'green' => $arrTmp[1], 'blue' => $arrTmp[2]);
}

?>
