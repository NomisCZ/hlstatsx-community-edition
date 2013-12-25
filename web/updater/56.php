<?php
	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$dbversion = 56;
	$version = "1.6.11-beta3";

	$tfgames = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'tf'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($tfgames, $db->escape($rowdata[0]));
	}
	
	foreach ($tfgames as $game)
	{
		$db->query("
			INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
				('$game', 'bushwacka', 'The Bushwacka', 2.00),
				('$game', 'glovesurgent', 'Gloves of Running Urgently', 2.00),
				('$game', 'blackbox', 'The Black Box', 1.00),
				('$game', 'sydneysleeper', 'The Sydney Sleeper', 1.00);

		");

		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('W','$game','bushwacka', 'George Bushwacka', 'kills with The Bushwacka'),
				('W','$game','glovesurgent', 'It''s Urgent', 'kills with the Gloves of Running Urgently'),
				('W','$game','blackbox', 'What''s in the box?', 'kills with The Black Box'),
				('W','$game','sydneysleeper', 'Down Under', 'kills with The Sydney Sleeper');
		");
		
		$db->query("
			INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES
				('bushwacka', 1, 0, '$game', '1_bushwacka.png', 'Bronze Bushwacka'),
				('bushwacka', 5, 0, '$game', '2_bushwacka.png', 'Silver Bushwacka'),
				('bushwacka', 10, 0, '$game', '3_bushwacka.png', 'Gold Bushwacka'),
				('glovesurgent', 1, 0, '$game', '1_gloves_urgent.png', 'Bronze Gloves of Running Urgently'),
				('glovesurgent', 5, 0, '$game', '2_gloves_urgent.png', 'Silver Gloves of Running Urgently'),
				('glovesurgent', 10, 0, '$game', '3_gloves_urgent.png', 'Gold Gloves of Running Urgently'),
				('blackbox', 1, 0, '$game', '1_blackbox.png', 'Bronze Black Box'),
				('blackbox', 5, 0, '$game', '2_blackbox.png', 'Silver Black Box'),
				('blackbox', 10, 0, '$game', '3_blackbox.png', 'Gold Black Box'),
				('sydneysleeper', 1, 0, '$game', '1_sydney_sleeper.png', 'Bronze Sydney Sleeper'),
				('sydneysleeper', 5, 0, '$game', '2_sydney_sleeper.png', 'Silver Sydney Sleeper'),
				('sydneysleeper', 10, 0, '$game', '3_sydney_sleeper.png', 'Gold Sydney Sleeper');
		");
	}
	
	$db->query("UPDATE hlstats_Options SET `value` = '$version' WHERE `keyname` = 'version'");
	$db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>
