<?php
	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$dbversion = 53;
	$version = "1.6.11-beta2";

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
				('$game', 'battleneedle', 'The Vita-Saw', 1.00),
				('$game', 'powerjack', 'The Powerjack', 1.00),
				('$game', 'degreaser', 'The Degreaser', 1.00),
				('$game', 'short_stop', 'The Shortstop', 1.00),
				('$game', 'holy_mackerel', 'The Holy Mackerel', 1.00),
				('$game', 'letranger', 'L''Etranger', 1.00),
				('$game', 'eternal_reward', 'Your Eternal Reward', 2.00),
				('$game', 'fryingpan', 'Frying Pan', 2.00);
		");

		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('W','$game','battleneedle', 'Take your Vita-Saws', 'kills with The Vita-Saw'),
				('W','$game','powerjack', 'Powerjacking', 'kills with The Powerjack'),
				('W','$game','degreaser', 'Degreased', 'kills with The Degreaser'),
				('W','$game','short_stop', 'Stopping Short', 'kills with The Shortstop'),
				('W','$game','holy_mackerel', 'Something''s Fishy', 'kills with The Holy Mackerel'),
				('W','$game','letranger', 'Ranged', 'kills with the L''Etranger'),
				('W','$game','eternal_reward', 'It goes on and on...', 'kills with Your Eternal Reward'),
				('W','$game','fryingpan', 'Fried Egg', 'kills with the Frying Pan');
		");
		
		$db->query("
			INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES
				('battleneedle', 1, 0, '$game', '1_battleneedle.png', 'Bronze Vita-Saw'),
				('battleneedle', 5, 0, '$game', '2_battleneedle.png', 'Silver Vita-Saw'),
				('battleneedle', 10, 0, '$game', '3_battleneedle.png', 'Gold Vita-Saw'),
				('powerjack', 1, 0, '$game', '1_powerjack.png', 'Bronze Powerjack'),
				('powerjack', 5, 0, '$game', '2_powerjack.png', 'Silver Powerjack'),
				('powerjack', 10, 0, '$game', '3_powerjack.png', 'Gold Powerjack'),
				('degreaser', 1, 0, '$game', '1_degreaser.png', 'Bronze Degreaser'),
				('degreaser', 5, 0, '$game', '2_degreaser.png', 'Silver Degreaser'),
				('degreaser', 10, 0, '$game', '3_degreaser.png', 'Gold Degreaser'),
				('short_stop', 1, 0, '$game', '1_short_stop.png', 'Bronze Short Stop'),
				('short_stop', 5, 0, '$game', '2_short_stop.png', 'Silver Short Stop'),
				('short_stop', 10, 0, '$game', '3_short_stop.png', 'Gold Short Stop'),
				('holy_mackerel', 1, 0, '$game', '1_holy_mackerel.png', 'Bronze Holy Mackerel'),
				('holy_mackerel', 5, 0, '$game', '2_holy_mackerel.png', 'Silver Holy Mackerel'),
				('holy_mackerel', 10, 0, '$game', '3_holy_mackerel.png', 'Gold Holy Mackerel'),
				('letranger', 1, 0, '$game', '1_letranger.png', 'Bronze L''Etranger'),
				('letranger', 5, 0, '$game', '2_letranger.png', 'Silver L''Etranger'),
				('letranger', 10, 0, '$game', '3_letranger.png', 'Gold L''Etranger'),
				('eternal_reward', 1, 0, '$game', '1_eternal_reward.png', 'Bronze Your Eternal Reward'),
				('eternal_reward', 5, 0, '$game', '2_eternal_reward.png', 'Silver Your Eternal Reward'),
				('eternal_reward', 10, 0, '$game', '3_eternal_reward.png', 'Gold Your Eternal Reward'),
				('fryingpan', 1, 0, '$game', '1_fryingpan.png', 'Bronze Frying Pan'),
				('fryingpan', 5, 0, '$game', '2_fryingpan.png', 'Silver Frying Pan'),
				('fryingpan', 10, 0, '$game', '3_fryingpan.png', 'Gold Frying Pan'),
				('robot_arm_combo_kill', 1, 0, '$game', '1_robot_arm_combo_kill.png', 'Bronze Gunslinger Combo Kill'),
				('robot_arm_combo_kill', 5, 0, '$game', '2_robot_arm_combo_kill.png', 'Silver Gunslinger Combo Kill'),
				('robot_arm_combo_kill', 10, 0, '$game', '3_robot_arm_combo_kill.png', 'Gold Gunslinger Combo Kill'),
				('bleed_kill', 1, 0, '$game', '1_bleed_kill.png', 'Bronze Bleed Kills'),
				('bleed_kill', 5, 0, '$game', '2_bleed_kill.png', 'Silver Bleed Kills'),
				('bleed_kill', 10, 0, '$game', '3_bleed_kill.png', 'Gold Bleed Kills');
		");
	}
	
	$db->query("UPDATE hlstats_Options SET `value` = '$version' WHERE `keyname` = 'version'");
	$db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>
