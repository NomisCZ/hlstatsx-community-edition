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
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('O','$game', 'mvp1', 'Most Valuable Player', 'wins as Most Valuable Player');
			");
			
		$db->query("
			INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES
				('$game', 'mvp1', 5, 0, '', 'MVP #1', '1', '0', '0', '0'),
				('$game', 'mvp2', 3, 0, '', 'MVP #2', '1', '0', '0', '0'),
				('$game', 'mvp3', 2, 0, '', 'MVP #3', '1', '0', '0', '0');
			");
	}
	
	$db->query("
		UPDATE hlstats_Options SET `value` = '18' WHERE `keyname` = 'dbversion';
		");
?>