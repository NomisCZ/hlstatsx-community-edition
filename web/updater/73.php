<?php
    if ( !defined('IN_UPDATER') )
    {
        die('Do not access this file directly.');
    }

    $dbversion = 73;
	
    print "Updating hlstatsx_Events_Chat to add support for more chat types.<br />";
    $db->query("ALTER TABLE `hlstats_Events_Chat` MODIFY `message_mode` tinyint(2) NOT NULL default '0';");
     
    // Perform database schema update notification
    print "Updating database schema number.<br />";
    $db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>