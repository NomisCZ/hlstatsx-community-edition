<?php
	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$dbversion = 63;
	$version = "1.6.12";
	
	$db->query("ALTER TABLE `hlstats_Players` 
		ADD COLUMN `teamkills` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `hits`;");
	$db->query("ALTER TABLE `hlstats_Players_History` 
		ADD COLUMN `teamkills` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `game`,
		ADD COLUMN `kill_streak` INT(6) UNSIGNED NOT NULL DEFAULT '0' AFTER `teamkills`,
		ADD COLUMN `death_streak` INT(6) UNSIGNED NOT NULL DEFAULT '0' AFTER `kill_streak`;");
	
	$db->query("UPDATE `hlstats_Players` SET `teamkills` = IFNULL((SELECT COUNT(`hlstats_Events_Teamkills`.`id`) FROM `hlstats_Events_Teamkills` WHERE `hlstats_Events_Teamkills`.`killerId` = `hlstats_Players`.`playerId`),0)");
	
	$db->query("UPDATE hlstats_Options SET `value` = '$version' WHERE `keyname` = 'version'");
	$db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>
