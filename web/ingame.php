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
	if ($key !== "HTTP_COOKIE") {
		$search_pattern  = array("/<script>/", "/<\/script>/", "/[^A-Za-z0-9.\-\/=:;_?#&~]/");
		$replace_pattern = array("", "", "");
		$entry = preg_replace($search_pattern, $replace_pattern, $entry);
  
		if ($key == "PHP_SELF") {
			if ((strrchr($entry, "/") !== "/hlstats.php") &&
				(strrchr($entry, "/") !== "/ingame.php") &&
				(strrchr($entry, "/") !== "/show_graph.php") &&
				(strrchr($entry, "/") !== "/sig.php") &&
				(strrchr($entry, "/") !== "/sig2.php") &&
				(strrchr($entry, "/") !== "/index.php") &&
				(strrchr($entry, "/") !== "/status.php") &&
				(strrchr($entry, "/") !== "/top10.php") &&
				(strrchr($entry, "/") !== "/config.php") &&
				(strrchr($entry, "/") !== "/") &&
				($entry !== "")) {
				header("Location: http://".$_SERVER['HTTP_HOST']."/hlstats.php");    
				exit;
			}    
		}
		$_SERVER[$key] = $entry;
	}
}

// Several Stuff end
@header("Content-Type: text/html; charset=utf-8");

// do not report NOTICE warnings
@error_reporting(E_ALL ^ E_NOTICE);

////
//// Initialisation
////

define('IN_HLSTATS', true);
define('PAGE', 'INGAME');

///
/// Classes
///

// Load required files
require("config.php");
require(INCLUDE_PATH . "/class_db.php");
require(INCLUDE_PATH . "/class_table.php");
require(INCLUDE_PATH . "/functions.php");

$db_classname = "DB_" . DB_TYPE;
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
	$g_options['scripturl'] = str_replace('\\','/',$_SERVER['PHP_SELF']);


////
//// Main
////

if ( isset($_GET["game"]) )
{
	$game = valid_request($_GET["game"], 0);
}

$mode = isset($_GET["mode"])?$_GET["mode"]:"";

$valid_modes = array(
    "pro",
    "motd",
    "status",
    "load",
    "help",
    "players",
    "clans",
    "statsme",
    "kills",
    "targets",
    "accuracy",
    "actions", 
    "weapons",
    "maps",
    "servers",
    "bans",
    "claninfo",
    "weaponinfo",
    "mapinfo",
    "actioninfo"
);

if (!in_array($mode, $valid_modes))
{
	$mode = "status";
}

pageHeader();

if ( file_exists(PAGE_PATH . "/ingame/$mode.php") )
	@include(PAGE_PATH . "/ingame/$mode.php");
else
	error('Unable to find ' . PAGE_PATH . "/ingame/$mode.php");

pageFooter();

?>

