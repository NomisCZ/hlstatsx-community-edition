<?php
/***************
** Deactivate HLstatsX ranking for banned players
** and reactivate them if unbanned
** Supports SourceBans, AMXBans, Beetlesmod, Globalban, MySQL Banning*
** by Jannik 'Peace-Maker' Hartung
** http://www.sourcebans.net/, http://www.wcfan.de/

** This program is free software; you can redistribute it and/or
** modify it under the terms of the GNU General Public License
** as published by the Free Software Foundation; either version 2
** of the License, or (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
**
**
** Version 1.3: Added MySQL Banning support
** Version 1.2: Added more error handling
** Version 1.1: Fixed banned players not marked as banned, if a previous ban was unbanned
** Version 1.0: Initial Release
***************/

/*****************************
/***MAKE YOUR CONFIGURATION***
/********SETTINGS IN**********
/******hlstatsxban.cfg********
/***** DON'T EDIT BELOW ******
/*****************************/

require("hlstatsxban.cfg");

if (!extension_loaded('mysqli')) {
	die("This script requires the MySQLi extension to be enabled.  Consult your administrator, or edit your php.ini file, to enable this extension.");
}

$usesb = (SB_HOST == ""||SB_PORT == ""||SB_USER == ""||SB_PASS == ""||SB_NAME == ""||SB_PREFIX == ""?false:true);
$useamx = (AMX_HOST == ""||AMX_PORT == ""||AMX_USER == ""||AMX_PASS == ""||AMX_NAME == ""||AMX_PREFIX == ""?false:true);
$usebm = (BM_HOST == ""||BM_PORT == ""||BM_USER == ""||BM_PASS == ""||BM_NAME == ""||BM_PREFIX == ""?false:true);
$usegb = (GB_HOST == ""||GB_PORT == ""||GB_USER == ""||GB_PASS == ""||GB_NAME == ""||GB_PREFIX == ""?false:true);
$usemb = (MB_HOST == ""||MB_PORT == ""||MB_USER == ""||MB_PASS == ""||MB_NAME == ""||MB_PREFIX == ""?false:true);
$hlxready = (HLX_HOST == ""||HLX_PORT == ""||HLX_USER == ""||HLX_PASS == ""||empty($hlxdbs)||HLX_PREFIX == ""?false:true);

if (!$hlxready || (!$usesb && !$useamx && !$usebm && !$usegb && !$usemb))
    die('[-] Please type your database information for HLstatsX and at least for one other ban database.');

$bannedplayers = array();
$unbannedplayers = array();

//------------------------------
// SourceBans Part
//------------------------------
if ($usesb)
{
    // Connect to the SourceBans database.
    $con = new mysqli(SB_HOST, SB_USER, SB_PASS, SB_NAME, SB_PORT);
    if (mysqli_connect_error()) die('[-] Can\'t connect to SourceBans Database (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    
    print("[+] Successfully connected to SourceBans database. Retrieving bans now.\n");
    
    // Get permanent banned players
    $bcnt = 0;
    if ($bans = $con->query("SELECT `authid` FROM `".SB_PREFIX."_bans` WHERE `RemoveType` IS NULL AND `length` = 0")) {
         while ($banned = $bans->fetch_array(MYSQL_ASSOC)) {
             if(!in_array($banned["authid"], $bannedplayers)) {
                  $bannedplayers[] = $banned["authid"];
                  ++$bcnt;
             }
         }
    }
    else {
		die('[-] Error retrieving banned players: ' . $con->error);
    }

		
    
    // Read unbanned players
    $ubcnt = 0;
    if ($unbans = $con->query("SELECT `authid` FROM `".SB_PREFIX."_bans` WHERE `RemoveType` IS NOT NULL AND `RemovedOn` IS NOT NULL")) {
        while ($unbanned = $unbans->fetch_array(MYSQL_ASSOC)) {
             if(!in_array($unbanned["authid"], $bannedplayers) && !in_array($unbanned["authid"], $unbannedplayers)) {
                  $unbannedplayers[] = $unbanned["authid"];
                  ++$ubcnt;
             }
        }
    }
    else {
		die('[-] Error retrieving unbanned players: ' . $con->error);
    }

    $con->close();
    print("[+] Retrieved ".$bcnt." banned and ".$ubcnt." unbanned players from SourceBans.\n");
}

//------------------------------
// AMXBans Part
//------------------------------
if ($useamx)
{
    // Connect to the AMXBans database.
    $con = new mysqli(AMX_HOST, AMX_USER, AMX_PASS, AMX_NAME, AMX_PORT);
    if (mysqli_connect_error()) die('[-] Can\'t connect to AMXBans Database (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());

    print("[+] Successfully connected to AMXBans database. Retrieving bans now.\n");
    
    // Get permanent banned players
    $bcnt = 0;
    if ($bans = $con->query("SELECT `player_id` FROM `".AMX_PREFIX."_bans` WHERE `ban_length` = 0")) {
		while ($banned = $bans->fetch_array(MYSQL_ASSOC)) {
			if(!in_array($banned["player_id"], $bannedplayers))
			{
				$bannedplayers[] = $banned["player_id"];
				++$bcnt;
			}
		}
	} else {
		die('[-] Error retrieving banned players: ' . $con->error);
    }
    

    // Read unbanned players
    $ubcnt = 0;
	// Handles (apparently) pre-6.0 version DB or lower
    if ($unbans = $con->query("SELECT `player_id` FROM `".AMX_PREFIX."_banhistory` WHERE `ban_length` = 0")) {
		while ($unbanned = $unbans->fetch_array(MYSQL_ASSOC)) {
			if(!in_array($unbanned["player_id"], $bannedplayers) && !in_array($unbanned["player_id"], $unbannedplayers))
			{
				$unbannedplayers[] = $unbanned["player_id"];
				++$ubcnt;
			}
		}		
	}
	// Handles (apparently) 6.0 version DB or higher
	else if ($unbans = $con->query("SELECT `player_id` FROM `".AMX_PREFIX."_bans` WHERE `expired` = 1")) {
		while ($unbanned = $unbans->fetch_array(MYSQL_ASSOC)) {
			if(!in_array($unbanned["player_id"], $bannedplayers) && !in_array($unbanned["player_id"], $unbannedplayers))
			{
				$unbannedplayers[] = $unbanned["player_id"];
				++$ubcnt;
			}
		}
	} else {
		die('[-] Error retrieving unbanned players: ' . $con->error);
	}
	

    $con->close();
    print("[+] Retrieved ".$bcnt." banned and ".$ubcnt." unbanned players from AMXBans.\n");
}

//------------------------------
// Beetlesmod Part
//------------------------------
if ($usebm)
{
    // Connect to the Beetlesmod database.
    $con = new mysqli(BM_HOST, BM_USER, BM_PASS, BM_NAME, BM_PORT);
    if (mysqli_connect_error()) die('[-] Can\'t connect to Beetlesmod Database (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());

    print("[+] Successfully connected to Beetlesmod database. Retrieving bans now.\n");

    // Get permanent banned players
    $bcnt = 0;
    if ($bans = $con->query("SELECT `steamid` FROM `".BM_PREFIX."_bans` WHERE `Until` IS NULL")) {
		while ($banned = $bans->fetch_array(MYSQL_ASSOC)) {
			if(!in_array($banned["steamid"], $bannedplayers))
			{
				$bannedplayers[] = $banned["steamid"];
				++$bcnt;
			}
		}
	} else {
		die('[-] Error retrieving banned players: ' . $con->error);
    }
	

    // Read unbanned players
    $ubcnt = 0;
    if ($unbans = $con->query("SELECT `steamid` FROM `".BM_PREFIX."_bans` WHERE `Until` IS NULL AND `Remove` = 0")) {
		while ($unbanned = $unbans->fetch_array(MYSQL_ASSOC)) {
			if(!in_array($unbanned["steamid"], $bannedplayers) && !in_array($unbanned["steamid"], $unbannedplayers))
			{
				$unbannedplayers[] = $unbanned["steamid"];
				++$ubcnt;
			}
		}
	} else {
		die('[-] Error retrieving unbanned players: ' . $con->error);
    }
	

    $con->close();
    print("[+] Retrieved ".$bcnt." banned and ".$ubcnt." unbanned players from Beetlesmod.\n");
}

//------------------------------
// Globalban Part
//------------------------------
if ($usegb)
{
    // Connect to the Globalban database.
    $con = new mysqli(GB_HOST, GB_USER, GB_PASS, GB_NAME, GB_PORT);
    if (mysqli_connect_error()) die('[-] Can\'t connect to Globalban Database (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());

    print("[+] Successfully connected to Globalban database. Retrieving bans now.\n");

    // Get permanent banned players
    $bcnt = 0;
    if ($bans = $con->query("SELECT `steam_id` FROM `".GB_PREFIX."_ban` WHERE `active` = 1 AND `pending` = 0 AND `length` = 0")) {
		while ($banned = $bans->fetch_array(MYSQL_ASSOC)) {
			if(!in_array($banned["steam_id"], $bannedplayers))
			{
				$bannedplayers[] = $banned["steam_id"];
				++$bcnt;
			}
		}
	} else {
		die('[-] Error retrieving banned players: ' . $con->error);
    }
	

    // Read unbanned players
    $ubcnt = 0;
    if ($unbans = $con->query("SELECT `steam_id` FROM `".GB_PREFIX."_ban` WHERE `active` = 0 AND `pending` = 0 AND `length` = 0")) {
		while ($unbanned = $unbans->fetch_array(MYSQL_ASSOC)) {
			if(!in_array($unbanned["steam_id"], $bannedplayers) && !in_array($unbanned["steam_id"], $unbannedplayers))
			{
				$unbannedplayers[] = $unbanned["steam_id"];
				++$ubcnt;
			}
		}
	} else {
		die('[-] Error retrieving unbanned players: ' . $con->error);
    }
	

    $con->close();
    print("[+] Retrieved ".$bcnt." banned and ".$ubcnt." unbanned players from Globalban.\n");
}

//------------------------------
// MySQL Banning Part
//------------------------------
if ($usemb)
{
    // Connect to the MySQL Banning database.
    $con = new mysqli(MB_HOST, MB_USER, MB_PASS, MB_NAME, MB_PORT);
    if (mysqli_connect_error()) die('[-] Can\'t connect to MySQL Banning Database (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());

    print("[+] Successfully connected to MySQL Banning database. Retrieving bans now.\n");

    // Get permanent banned players
    $bcnt = 0;
    if ($bans = $con->query("SELECT `steam_id` FROM `".MB_PREFIX."_bans` WHERE `ban_length` = 0")) {
		while ($banned = $bans->fetch_array(MYSQL_ASSOC)) {
			if(!in_array($banned["steam_id"], $bannedplayers))
			{
				$bannedplayers[] = $banned["steam_id"];
				++$bcnt;
			}
		}
	} else {
		die('[-] Error retrieving banned players: ' . $con->error);
    }
	

    /****** SM MySQL Banning doesn't provide a ban history AFAIK ******/
    
    // Read unbanned players
    // $ubcnt = 0;
    // if ($unbans = $con->query("SELECT `steam_id` FROM `".MB_PREFIX."_bans` WHERE `ban_length` = 0")) {
		// while ($unbanned = $unbans->fetch_array(MYSQL_ASSOC)) {
			// if(!in_array($unbanned["steam_id"], $bannedplayers) && !in_array($unbanned["steam_id"], $unbannedplayers))
			// {
				// $unbannedplayers[] = $unbanned["steam_id"];
				// ++$ubcnt;
			// }
		// }
	// } else {
	// die('[-] Error retrieving unbanned players: ' . $con->error);
    //}

    $con->close();
    //print("[+] Retrieved ".$bcnt." banned and ".$ubcnt." unbanned players from MySQL Banning.\n");
    print("[+] Retrieved ".$bcnt." banned players from MySQL Banning.\n");
}

//------------------------------
// HLstatsX Part
//------------------------------

if(empty($bannedplayers) && empty($unbannedplayers))
    die('[-] Nothing to change. Exiting.');

$bannedsteamids="''";
$unbannedsteamids="''";

if(!empty($bannedplayers))
{
	$bannedsteamids = "'";
	foreach ($bannedplayers as $steamid)
	{
		$steamid = preg_replace('/^STEAM_[0-9]+?\:/i','',$steamid);
		$bannedsteamids .= $steamid."','";
	}
	$bannedsteamids .= preg_replace('/\,\'$/','',$steamid);
	$bannedsteamids .= "'";
}

if(!empty($unbannedplayers))
{
	$unbannedsteamids = "'";
	foreach ($unbannedplayers as $steamid)
	{
		$steamid = preg_replace('/^STEAM_[0-9]+?\:/i','',$steamid);
		$unbannedsteamids .= $steamid."','";
	}
	$unbannedsteamids .= preg_replace('/\,\'$/','',$steamid);
	$unbannedsteamids .= "'";
}

// Connection to DB
$hlxcon = new mysqli(HLX_HOST, HLX_USER, HLX_PASS, '', HLX_PORT);
if (mysqli_connect_error()) die('[-] Can\'t connect to HLstatsX Database (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());

print("[+] Successfully connected to HLstatsX database server. Updating players...\n");
// Loop through all hlstatsx databases
foreach ($hlxdbs as $hlxdb)
{
    $unbancnt = $bancnt = 0;
    $hlxcon->select_db($hlxdb);
    // Hide all banned players
    if ($hlxban = $hlxcon->query("UPDATE `".HLX_PREFIX."_Players` SET `hideranking` = 2 WHERE `hideranking` < 2 AND `playerId` IN (SELECT `playerId` FROM `".HLX_PREFIX."_PlayerUniqueIds` WHERE `uniqueId` IN (".$bannedsteamids."));")) {
		$bancnt = ($hlxcon->affected_rows?$hlxcon->affected_rows:0);
    }
    else {
	die('[-] Error hiding banned players: ' . $hlxcon->error);
    }

    // Show all unbanned players
    if ($hlxunban = $hlxcon->query("UPDATE `".HLX_PREFIX."_Players` SET `hideranking` = 0 WHERE `hideranking` = 2 AND `playerId` IN (SELECT `playerId` FROM `".HLX_PREFIX."_PlayerUniqueIds` WHERE `uniqueId` IN (".$unbannedsteamids."));")) {
	    $unbancnt = ($hlxcon->affected_rows?$hlxcon->affected_rows:0);
		
        if ($bancnt>0||$unbancnt>0) {
             print("[+] ".$hlxdb.": ".$bancnt." players were marked as banned, ".$unbancnt." players were reenabled again.\n");
        }
        else {
             print("[-] ".$hlxdb.": No player changed.\n");
        }
    }
    else {
         die('[-] Error showing unbanned players: ' . $hlxcon->error);
    }
}
$hlxcon->close();
?>