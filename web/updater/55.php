<?php
	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$dbversion = 55;
	$version = "1.6.11-beta3";

	$l4d2games = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'l4d' AND (code LIKE 'l4d2%' OR code LIKE 'l4dii%')");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($l4d2games, $db->escape($rowdata[0]));
	}

	foreach($l4d2games as $game)
	{			
		$db->query("
			INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
				('$game','golfclub', 'Golf Club', 1.5),
				('$game','rifle_m60', 'M60', 1);
		");
		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('W', '$game','golfclub', 'Golf Club', 'kills with the Golf Club'),
				('W', '$game','rifle_m60', 'M60', 'kills with M60');
		");
		$db->query("
			INSERT IGNORE INTO `hlstats_Roles` (`game`, `code`, `name`, `hidden`) VALUES
				('$game', 'NamVet', 'Bill', '0'),
				('$game', 'TeenGirl', 'Zoey', '0'),
				('$game', 'Biker', 'Francis', '0'),
				('$game', 'Manager', 'Louis', '0');
		");
	}
	
	$db->query("UPDATE hlstats_Options SET `value` = '$version' WHERE `keyname` = 'version'");
	$db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>
