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
	
	foreach($tf2games as $game)
	{
		$db->query("
			UPDATE `hlstats_Awards` SET `awardType`='P' WHERE `code`='steal_sandvich' AND `game`='$game'
		");
		// These were in install from a few versions ago, but never made it in an upgrade
		$db->query("
			INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
				('$game', 'telefrag', 'Telefrag', 2)
		");
		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('W','$game','telefrag', 'Lucky Duck', 'kills by telefrag'),
				('P','$game','steal_sandvich', 'Mmm, Ham', 'stolen Sandviches')
		");
		$db->query("
			UPDATE `hlstats_Actions` SET `for_PlayerActions`='0', `for_PlayerPlayerActions`='1'
				WHERE `code` = 'steal_sandvich' AND `game` = '$game'
		");
	}

	$db->query("
		UPDATE hlstats_Options SET `value` = '1.6.5' WHERE `keyname` = 'version'
	");
	
	$db->query("
		UPDATE hlstats_Options SET `value` = '25' WHERE `keyname` = 'dbversion'
	");
?>