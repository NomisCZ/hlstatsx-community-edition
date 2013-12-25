<?php
    if ( !defined('IN_UPDATER') )
    {
        die('Do not access this file directly.');
    }

    $dbversion = 76;
    $version = "1.6.19-pre2";

    // Tracker #1599 - Correct CS:GO Actions
    // Get list of CS:GO Duplicated games
    $csgogames = array();
    $result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'csgo'");
    while ($rowdata = $db->fetch_row($result))
    { 
        array_push($csgogames, $db->escape($rowdata[0]));
    }
    
    // Get list of CSS duplicated games
    $cssgames = array();
    $result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'css'");
    while ($rowdata = $db->fetch_row($result))
    { 
        array_push($cssgames, $db->escape($rowdata[0]));
    }
    
    // Insert heatmap config for each NTS game
    print "Correcting CS:GO Actions. (<a href=\"http://tracker.hlxce.com/issues/1599\">#1599</a>)<br />";
    foreach ($csgogames as $csgogame)
    {
        $db->query("UPDATE hlstats_Actions SET `code` = 'SFUI_Notice_All_Hostages_Rescued' WHERE `code` = 'All_Hostages_Rescued' AND `game` = '$csgogame';");
        $db->query("UPDATE hlstats_Actions SET `code` = 'SFUI_Notice_Bomb_Defused' WHERE `code` = 'Bomb_Defused' AND `game` = '$csgogame';");
        $db->query("UPDATE hlstats_Actions SET `code` = 'SFUI_Notice_CTS_Win' WHERE `code` = 'CTS_Win' AND `game` = '$csgogame';");
        $db->query("UPDATE hlstats_Actions SET `code` = 'SFUI_Notice_Target_Bombed' WHERE `code` = 'Target_Bombed' AND `game` = '$csgogame';");
        $db->query("UPDATE hlstats_Actions SET `code` = 'SFUI_Notice_Terrorists_Win' WHERE `code` = 'Terrorists_Win' AND `game` = '$csgogame';");
    }
        
    // Perform database schema update notification
    print "Updating database and verion schema numbers.<br />";
    $db->query("UPDATE hlstats_Options SET `value` = '$version' WHERE `keyname` = 'version'");
    $db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>