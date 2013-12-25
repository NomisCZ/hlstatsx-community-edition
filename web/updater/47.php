<?php
	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$tfgames = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'tf'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($tfgames, $db->escape($rowdata[0]));
	}
	
	foreach($tfgames as $game)
	{
		$db->query("
			UPDATE hlstats_Awards SET `name`='One, Two, Punch!', `verb`='Gunslinger Combo Kills' WHERE game='$game' AND code='robot_arm_combo_kill';
		");
		
		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('W','$game','robot_arm_blender_kill', 'Mixed ya right up!', 'Engineer taunt kills (Gunslinger)')
		");
		
		$db->query("
			INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
				('$game', 'robot_arm_blender_kill', 'Gunslinger Taunt Kill', 5.0)
		");
	}
	
	$db->query("UPDATE hlstats_Options SET `value` = '47' WHERE `keyname` = 'dbversion'");
?>
