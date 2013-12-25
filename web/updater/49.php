<?php
	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$dbversion = 49;


	$cssgames = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'css'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($cssgames, $db->escape($rowdata[0]));
	}
	
	foreach($cssgames as $game)
	{	
		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('W', '$game', 'mostkills', 'Most Kills', 'kills'),
				('W', '$game', 'suicide', 'Suicides', 'suicides'),
				('W', '$game', 'teamkills', 'Team Killer', 'team kills');
		");
		
		$db->query("
			INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES
				('famas', 1, 0, '$game', '1_famas.png', 'Award of Famas'),
				('famas', 5, 0, '$game', '2_famas.png', 'Award of Famas'),
				('famas', 12, 0, '$game', '3_famas.png', 'Award of Famas'),
				('famas', 20, 0, '$game', '4_famas.png', 'Award of Famas'),
				('famas', 30, 0, '$game', '5_famas.png', 'Award of Famas'),
				('famas', 50, 0, '$game', '6_famas.png', 'Award of Famas'),
				('teamkills', 1, 0, '$game', '1_teamkills.png', 'Award of Team Kills'),
				('teamkills', 5, 0, '$game', '2_teamkills.png', 'Bronze Team Kills'),
				('teamkills', 12, 0, '$game', '3_teamkills.png', 'Silver Team Kills'),
				('teamkills', 20, 0, '$game', '4_teamkills.png', 'Gold Team Kills'),
				('teamkills', 30, 0, '$game', '5_teamkills.png', 'Platinum Team Kills'),
				('teamkills', 50, 0, '$game', '6_teamkills.png', 'Supreme Team Kills'),
				('mostkills', 1, 0, '$game', '1_mostkills.png', 'Award of Most Kills'),
				('mostkills', 5, 0, '$game', '2_mostkills.png', 'Bronze Most Kills'),
				('mostkills', 12, 0, '$game', '3_mostkills.png', 'Silver Most Kills'),
				('mostkills', 20, 0, '$game', '4_mostkills.png', 'Gold Most Kills'),
				('mostkills', 30, 0, '$game', '5_mostkills.png', 'Platinum Most Kills'),
				('mostkills', 50, 0, '$game', '6_mostkills.png', 'Supreme Most Kills'),
				('suicide', 1, 0, '$game', '1_suicide.png', 'Award of Suicides'),
				('suicide', 5, 0, '$game', '2_suicide.png', 'Bronze Suicides'),
				('suicide', 12, 0, '$game', '3_suicide.png', 'Silver Suicides'),
				('suicide', 20, 0, '$game', '4_suicide.png', 'Gold Suicides'),
				('suicide', 30, 0, '$game', '5_suicide.png', 'Platinum Suicides'),
				('suicide', 50, 0, '$game', '6_suicide.png', 'Supreme Suicides');
		");
	}
	$db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>
