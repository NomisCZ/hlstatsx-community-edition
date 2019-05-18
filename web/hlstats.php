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
require('config.php');
$historical_cache=0;
if(defined('HISTORICAL_CACHE'))
{
	$historical_cache=constant('HISTORICAL_CACHE');
}

if($historical_cache==1)
{
	$rawmd5=md5(http_build_query($_REQUEST));
	$dir1=substr($rawmd5,0,1);
	$dir2=substr($rawmd5,1,1);
	$cachetarget=sprintf("cache/%s/%s/%s", $dir1, $dir2, $rawmd5);

	@mkdir("cache/$dir1");
	@mkdir("cache/$dir1/$dir2");

	if(file_exists($cachetarget))
	{
		file_put_contents("cache/cachehit",$cachetarget . "\n", FILE_APPEND);
		echo file_get_contents($cachetarget);
		die;
	}
}

session_start();

if((!empty($_GET['logout'])) && $_GET['logout'] == '1') {
        unset($_SESSION['loggedin']);
        header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']);
        die;
}

// Several stuff added by Malte Bayer
global $scripttime, $siteurlneo;
$scripttime = microtime(true);
$siteurlneo='http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'],strrchr($_SERVER['PHP_SELF'],'/'))+1);
$siteurlneo=str_replace('\\','/',$siteurlneo);

// Several Stuff end

foreach ($_SERVER as $key => $entry) {
	if ($key !== 'HTTP_COOKIE') {
		$search_pattern  = array('/<script>/', '/<\/script>/', '/[^A-Za-z0-9.\-\/=:;_?#&~]/');
		$replace_pattern = array('', '', '');
		$entry = preg_replace($search_pattern, $replace_pattern, $entry);
  
		if ($key == "PHP_SELF") {
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
				header("Location: http://$siteurlneo/hlstats.php");    
				exit;
			}    
		}
		$_SERVER[$key] = $entry;
	}
}

@header('Content-Type: text/html; charset=utf-8');

// do not report NOTICE warnings
@error_reporting(E_ALL ^ E_NOTICE);

////
//// Initialisation
////

define('PAGE', 'HLSTATS');

///
/// Classes
///

// Load required files
require(INCLUDE_PATH . '/class_db.php');
require(INCLUDE_PATH . '/class_table.php');
require(INCLUDE_PATH . '/functions.php');

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

if (!isset($g_options['scripturl'])) {
	$g_options['scripturl'] = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : getenv('PHP_SELF');
}

////
//// Main
////

$game = valid_request(isset($_GET['game'])?$_GET['game']:'', 0);

if (!$game)
{
	$game = isset($_SESSION['game'])?$_SESSION['game']:'';
}
else
{
	$_SESSION['game'] = $game;
}

if (!$realgame && $game)
{
	$realgame = getRealGame($game);
	$_SESSION['realgame'] = $realgame;
}

$mode = isset($_GET['mode'])?$_GET['mode']:'';

$valid_modes = array(
	'players',
	'clans',
	'weapons',
	'roles',
	'rolesinfo',
	'maps',
	'actions',
	'claninfo',
	'playerinfo',
	'weaponinfo',
	'mapinfo',
	'actioninfo',
	'playerhistory',
	'playersessions',
	'playerawards',
	'search',
	'admin',
	'help',
	'bans',
	'servers',
	'chathistory',
	'ranks',
	'rankinfo',
	'ribbons',
	'ribboninfo',
	'chat',
	'globalawards',
	'awards',
	'dailyawardinfo',
	'countryclans',
	'countryclansinfo',
	'teamspeak',
	'ventrilo',
	'updater',
	'profile'
);
   
if (file_exists('./updater') && $mode != 'updater')
{
	pageHeader(array('Update Notice'), array('Update Notice' => ''));
	echo "<div class=\"warning\">\n" . 
	"<span class=\"warning-heading\"><img src=\"".IMAGE_PATH."/warning.gif\" alt=\"Warning\"> Warning:</span><br />\n" .
	"<span class=\"warning-text\">The updater folder was detected in your web directory.<br />
	To perform a Database Update, please go to <strong><a href=\"{$g_options['scripturl']}?mode=updater\">HLX:CE Database Updater</a></strong> to perform the database update.<br /><br />
	<strong>If you have already performed the database update, <strong>you must delete the \"updater\" folder from your web folder.</span>\n</div>";
	pageFooter();
	die();
}
   
if ( !in_array($mode, $valid_modes) )
{
	$mode = 'contents';
}

if ( file_exists(PAGE_PATH . "/$mode.php") )
{
	@include(PAGE_PATH . "/$mode.php");
	pageFooter();
}
else
{
	header('HTTP/1.1 404 File Not Found', false, 404);
	error('Unable to find ' . PAGE_PATH . "/$mode.php");
	pageFooter();
}

?>
