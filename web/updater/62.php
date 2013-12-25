<?php
	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$dbversion = 62;
	$version = "1.6.12";
	
	$hl2mpgames = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'hl2mp'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($hl2mpgames, $db->escape($rowdata[0]));
	}
	
	foreach ($hl2mpgames as $game)
	{
		$db->query("INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
			('O','$game','headshot','Headshot King','headshot kills')");
		
		$db->query("INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES
			('$game', 'headshot', 0, 0, '', 'Headshot Kill', '1', '0', '0', '0')");
		
		$db->query("INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES
			('headshot',3,0,'$game','1_headshotaward.png','Award of Most Headshots'),
			('headshot',7,0,'$game','2_headshotaward.png','Most Headshots - Bronze'),
			('headshot',12,0,'$game','3_headshotaward.png','Most Headshots - Silver'),
			('headshot',17,0,'$game','x_headshotaward.png','Supreme Headshotter')");
	}
	
	$db->query("UPDATE hlstats_Options SET `value` = '$version' WHERE `keyname` = 'version'");
	$db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>
