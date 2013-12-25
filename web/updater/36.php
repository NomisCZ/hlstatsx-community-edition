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
			INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
				('$game', 'tribalkukri', 'The Tribalman''s Shiv', '2.00'),
				('$game', 'battleaxe', 'The Scotsman''s Skullcutter', '2.00');
		");
		
		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('W','$game','tribalkukri', 'The Tribalman''s Shiv', 'kills with The Tribalman''s Shiv'),
				('W','$game','battleaxe', 'The Scotsman''s Skullcutter', 'kills with The Scotsman''s Skullcutter');
		");

		for ($h = 1; $h<4; $h++) {
			switch ($h) {
			case 1:
				$level = "Bronze";
				$awardCount = 1;
			break;

			case 2:
				$level = "Silver";
				$awardCount = 5;
			break;

			case 3:
				$level = "Gold";
				$awardCount = 10;
			break;
			}

			$db->query(" 
				INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES
					('tribalkukri', $awardCount, 0, '$game', '{$h}_tribalkukri.png', '$level Tribalman''s Shiv'),
					('battleaxe', $awardCount, 0, '$game', '{$h}_battleaxe.png', '$level Scotsman''s Skullcutter');
			");
		}
	}
	$game = "";
	
	$cssgames = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'css'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($cssgames, $db->escape($rowdata[0]));
	}
	
	foreach($cssgames as $game)
	{		
		$db->query("
			INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES
				('$game', 'domination', 5, 0, '', 'Domination', '0', '1', '0', '0'),
				('$game', 'revenge', 3, 0, '', 'Revenge', '0', '1', '0', '0');
		");
	}	
	
	$db->query("UPDATE hlstats_Options SET `value` = '36' WHERE `keyname` = 'dbversion'");	
?>
