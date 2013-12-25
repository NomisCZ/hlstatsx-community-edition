<?php

	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$tf2games = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'tf'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($tf2games, $db->escape($rowdata[0]));
	}
	$cstrikegames = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'cstrike'");
	while ($rowdata = $db->fetch_row($result))
	{
		array_push($cstrikegames, $db->escape($rowdata[0]));
	}
	
	foreach($cstrikegames as $game)
	{
		$db->query("
			INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES
				('$game', 'headshot', 1, 0, '', 'Headshot', '1', '', '', '');
		");
		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('O', '$game','headshot', 'Headshot King', 'headshots');
		");
	}
	
	foreach($tf2games as $game)
	{
		$db->query("
			INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES
				
				('$game','airshot_rocket', 0, 0, '', 'Airshot Rocket', '1', '', '', ''),
				('$game','airshot_headshot', 0, 0, '', 'Airshot Headshot', '1', '', '', '');
		");				
		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('W','$game','taunt_medic', 'Oktoberfest!', 'medic taunt kills');
		");
		$db->query("
			INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `game`, `image`, `ribbonName`, `awardCount`) VALUES
				('taunt_medic', '$game', '1_taunt_medic.png', 'Bronze Medic Taunt', 1),
				('taunt_medic', '$game', '2_taunt_medic.png', 'Silver Medic Taunt', 5),
				('taunt_medic', '$game', '3_taunt_medic.png', 'Gold Medic Taunt', 10);
		");		
		$db->query("
			INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
				('$game','taunt_medic', 'Medic Taunt', 5.0);
		");
	}

	$db->query("UPDATE hlstats_Options SET `value` = '1.6.7' WHERE `keyname` = 'version'");
	$db->query("UPDATE hlstats_Options SET `value` = '31' WHERE `keyname` = 'dbversion'");	
?>