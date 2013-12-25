<?php
	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$dbversion = 59;
	$version = "1.6.12-dev";

	$tfgames = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'tf'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($tfgames, $db->escape($rowdata[0]));
	}
	
	foreach ($tfgames as $game)
	{
		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('O','$game','hit_by_train', 'Flattened', 'deaths from train'),
				('O','$game','headshot', 'Headache', 'headshots'),
				('W','$game','suicide', 'Doctor Assited Suicide', 'suicides');
		");
		$db->query("
			INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
				('$game', 'headtaker', 'Horseless Headless Horsemann''s Headtaker', 2.0);
		");
		$db->query("UPDATE IGNORE `hlstats_Ribbons` SET `image` = '1_glovesurgent.png' WHERE `game` = '$game' AND `image` = '1_gloves_urgent.png';");
		$db->query("UPDATE IGNORE `hlstats_Ribbons` SET `image` = '2_glovesurgent.png' WHERE `game` = '$game' AND `image` = '2_gloves_urgent.png';");
		$db->query("UPDATE IGNORE `hlstats_Ribbons` SET `image` = '3_glovesurgent.png' WHERE `game` = '$game' AND `image` = '3_gloves_urgent.png';");
		$db->query("UPDATE IGNORE `hlstats_Ribbons` SET `image` = '1_sydneysleeper.png' WHERE `game` = '$game' AND `image` = '1_sydney_sleeper.png';");
		$db->query("UPDATE IGNORE `hlstats_Ribbons` SET `image` = '2_sydneysleeper.png' WHERE `game` = '$game' AND `image` = '2_sydney_sleeper.png';");
		$db->query("UPDATE IGNORE `hlstats_Ribbons` SET `image` = '3_sydneysleeper.png' WHERE `game` = '$game' AND `image` = '3_sydney_sleeper.png';");	
	}
	
	$db->query("UPDATE hlstats_Options SET `value` = '$version' WHERE `keyname` = 'version'");
	$db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>
