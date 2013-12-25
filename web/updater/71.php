<?php
    if ( !defined('IN_UPDATER') )
    {
        die('Do not access this file directly.');
    }

    $dbversion = 71;
    $version = "1.6.17";
    $tfgames = array();
    $result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'tf'");
    while ($rowdata = $db->fetch_row($result))
    { 
    array_push($tfgames, $db->escape($rowdata[0]));
    }
    
    // 1558 - Fix TF2 award names on existing sites
    foreach ($tfgames as $game)
    {
        print "Fixing award names for TF2 gamecode $game.<br />";
        $db->query("UPDATE IGNORE `hlstats_Awards` SET `name` = 'Eureka!' WHERE `game` = '$game' AND `name` = 'Eureka Effect'");
        $db->query("UPDATE IGNORE `hlstats_Awards` SET `name` = 'The Gift of Punch' WHERE `game` = '$game' AND `name` = 'Holiday Punch'");
        $db->query("UPDATE IGNORE `hlstats_Awards` SET `name` = 'Melted Men' WHERE `game` = '$game' AND `name` = 'Manmelter'");
        $db->query("UPDATE IGNORE `hlstats_Awards` SET `name` = 'Phlogged' WHERE `game` = '$game' AND `name` = 'Phlogistinator'");
        $db->query("UPDATE IGNORE `hlstats_Awards` SET `name` = 'Convenient Radiation' WHERE `game` = '$game' AND `name` = 'Pomson 6000'");
        $db->query("UPDATE IGNORE `hlstats_Awards` SET `name` = 'Cold as ice' WHERE `game` = '$game' AND `name` = 'Spy-cicle'");
        $db->query("UPDATE IGNORE `hlstats_Awards` SET `name` = 'Ooooh burn!' WHERE `game` = '$game' AND `name` = 'Third Degree'");
        $db->query("UPDATE IGNORE `hlstats_Awards` SET `name` = 'Wrapping Machine' WHERE `game` = '$game' AND `name` = 'Wrap Assassin'");
    }
    // End 1558
     
    // Perform database schema update notification
    print "Updating database and verion schema numbers.<br />";
    $db->query("UPDATE hlstats_Options SET `value` = '$version' WHERE `keyname` = 'version'");
    $db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>