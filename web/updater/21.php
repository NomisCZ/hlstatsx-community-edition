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
				('W','$game','taunt_scout', 'Not even winded', 'scout taunt kills'),
				('W','$game','sandman', 'Batter Up!', 'kills with the Sandman');
		");
	}	
	
	$db->query("
		UPDATE hlstats_Options SET `value` = '1.6.4 ALPHA' WHERE `keyname` = 'version'
	");	
	
	$db->query("
		UPDATE hlstats_Options SET `value` = '21' WHERE `keyname` = 'dbversion'
	");
	
	
?>