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
				('O','$game','airshot_rocket', 'Fly me to the moon!', 'air rocket hits'),
				('O','$game','airshot_headshot', 'Duck Hunt', 'air headshots');
		");
	}
	$db->query("UPDATE hlstats_Options SET `value` = '32' WHERE `keyname` = 'dbversion'");	
?>