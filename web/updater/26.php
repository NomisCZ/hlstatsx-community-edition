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
			INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
				('$game', 'ball', 'Baseball', 2.5)
		");
		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('W','$game','ball', 'Fly Ball', 'kills with the Baseball')
		");
	}

	$db->query("
		UPDATE hlstats_Options SET `value` = '26' WHERE `keyname` = 'dbversion'
	");
?>