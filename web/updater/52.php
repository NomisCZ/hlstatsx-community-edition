<?php
	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$dbversion = 52;
	$version = "1.6.11-beta1";

	$tfgames = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'tf'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($tfgames, $db->escape($rowdata[0]));
	}
	
	foreach ($tfgames as $game)
	{
		$db->query("
			INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES
				('robot_arm_combo_kill', 1, 0, '$game', '1_robot_arm_combo_kill.png', 'Bronze Gunslinger Combo Kill'),
				('robot_arm_combo_kill', 5, 0, '$game', '2_robot_arm_combo_kill.png', 'Silver Gunslinger Combo Kill'),
				('robot_arm_combo_kill', 10, 0, '$game', '3_robot_arm_combo_kill.png', 'Gold Gunslinger Combo Kill'),
				('bleed_kill', 1, 0, '$game', '1_bleed_kill.png', 'Bronze Bleed Kills'),
				('bleed_kill', 5, 0, '$game', '2_bleed_kill.png', 'Silver Bleed Kills'),
				('bleed_kill', 10, 0, '$game', '3_bleed_kill.png', 'Gold Bleed Kills');
		");
	}
	
	$db->query("UPDATE hlstats_Options SET `value` = '$version' WHERE `keyname` = 'version'");
	$db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>
