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
			INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES
				('$game','defended_medic', 1, 0, '', 'Defended a Medic', 1, 0, 0, 0),
				('$game','buff_deployed', 1, 0, '', 'Deployed Buff Flag', 1, 0, 0, 0);
		");		
		$db->query("
			INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
				('$game','rocketlauncher_directhit', 'Direct Hit', 1.0),
				('$game','pickaxe', 'Equalizer', 2.0),
				('$game','sword', 'Eyelander', 2.0),
				('$game','sticky_resistance', 'Scottish Resistance', 1.0),
				('$game','taunt_demoman', 'Demoman Taunt', 5.0),				
				('$game','taunt_soldier', 'Soldier Taunt', 5.0);
		");		
		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('W','$game','rocketlauncher_directhit', 'Skill Rockets', 'kills with the Direct Hit'),
				('W','$game','pickaxe', 'Prospectors Piercings', 'kills with the Equalizer'),
				('W','$game','sword', 'Swords a Slicing', 'kills with the Eyelander'),
				('W','$game','sticky_resistance', 'Skill Stickies', 'kills with the Scottish Resistance'),
				('W','$game','taunt_demoman', 'Sword Swallowers', 'Demoman taunt kills'),
				('W','$game','taunt_soldier', 'Good-bye Cruel Worlds', 'Soldier taunt kills'),
				('O','$game','buff_deployed', 'Rootin Tootin Shootin', 'Buffs deployed'),
				('O','$game','defended_medic', 'Get behind me doctor!', 'Medics defended');
		");
	}	
	
	$db->query("
		UPDATE hlstats_Options SET `value` = '22' WHERE `keyname` = 'dbversion'
	");
	
	
?>