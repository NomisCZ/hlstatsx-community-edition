<?php
	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$dbversion = 60;
	$version = "1.6.12";

	$insgames = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'insmod'");
	while ($rowdata = $db->fetch_row($result))
	{
		array_push($insgames, $db->escape($rowdata[0]));
	}
	
	foreach ($insgames as $game)
	{
		$db->query("
			INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES		
				('$game', 'captured_a', 0, 5, '', 'Captured Objective A', '0', '0', '1', '0'),
				('$game', 'captured_b', 0, 5, '', 'Captured Objective B', '0', '0', '1', '0'),
				('$game', 'captured_c', 0, 5, '', 'Captured Objective C', '0', '0', '1', '0'),
				('$game', 'captured_d', 0, 5, '', 'Captured Objective D', '0', '0', '1', '0'),
				('$game', 'captured_e', 0, 5, '', 'Captured Objective E', '0', '0', '1', '0');
		");
	}
	
	$tfgames = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'tf'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($tfgames, $db->escape($rowdata[0]));
	}
	
	foreach ($tfgames as $game)
	{
	
		$db->query("
			INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES
				('$game', 'steak', 0, 0, '', 'Ate a Buffalo Steak Sandvich', '1', '0', '0', '0'),
				('$game', 'madmilk', 0, 0, '', 'Mad Milk', '0', '1', '0', '0');
		");
		
		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('O', '$game', 'steak', 'Don''t Run, Its Just Steak', 'Buffalo Steak Sandviches eaten'),
				('W', '$game', 'claidheamohmor', 'Claidheamohmor', 'Claidheamohmor kills'),
				('W', '$game', 'back_scratcher', 'Rakes Wrecked', 'Back Scratcher kills'),
				('W', '$game', 'boston_basher', 'Boston Basher', 'Boston Basher kills'),
				('W', '$game', 'steel_fists', 'My Fists They Are Made Of Steel!', 'kills with the Fists of Steel'),
				('W', '$game', 'amputator', 'Amputator', 'Amputator kills'),
				('W', '$game', 'tf_projectile_healing_bolt', 'Bolts Blown', 'Crusader''s Crossbow kills'),
				('W', '$game', 'ullapool_caber', 'Caber''s Clubbed', 'Ullapool Caber kills'),
				('W', '$game', 'lochnload', 'Loch-n-Load''d', 'Loch-n-Load kills'),
				('W', '$game', 'brassbeast', 'Brass Beast', 'Brass Beast kills'),
				('W', '$game', 'bear_claws', 'Clawed Candyasses', 'Warrior''s Spirit kills'),
				('W', '$game', 'candy_cane', 'Canes Cracked', 'Candy Cane kills'),
				('W', '$game', 'wrench_jag', 'Jag''d Off', 'Jag kills'),
				('W', '$game', 'iron_curtain', 'In Soviet Russia Iron Curtain Owns You', 'Iron Curtain kills'),
				('W', '$game', 'headtaker', 'Horseless Headless Horseman''s Headtaker', 'Horseless Headless Horseman''s Headtaker kills'),
				('P', '$game', 'madmilk', 'Creamed', 'Mad Milk hits');
		");
		
		$db->query("
			INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
				('$game', 'claidheamohmor', 'The Claidheamohmor', 2.0),
				('$game', 'back_scratcher', 'The Back Scratcher', 2.0),
				('$game', 'boston_basher', 'The Boston Basher', 2.0),
				('$game', 'steel_fists', 'The Fists of Steel', 2.0),
				('$game', 'amputator', 'The Amputator', 1.0),
				('$game', 'tf_projectile_healing_bolt', 'The Crusader''s Crossbow', 1.0),
				('$game', 'ullapool_caber', 'The Ullapool Caber', 2.0),
				('$game', 'lochnload', 'The Loch-n-Load', 1.0),
				('$game', 'brassbeast', 'The Brass Beast', 1.0),
				('$game', 'bear_claws', 'The Warrior''s Spirit', 2.0),
				('$game', 'candy_cane', 'The Candy Cane', 2.0),
				('$game', 'wrench_jag', 'The Jag', 2.0),
				('$game', 'iron_curtain', 'The Iron Curtain', 1.0);
		");
		
		for ($ribbon_count = 1; $ribbon_count <= 3; $ribbon_count++) {
			switch ($ribbon_count) {
				case 1:
					$color = "Bronze";
					$award_count = 1;
					break;
				case 2:
					$color = "Silver";
					$award_count = 5;
					break;
				case 3:
					$color = "Gold";
					$award_count = 10;
					break;
			}
			
			$db->query("
				INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES
					('steak', $award_count, 0, '$game', '" . $ribbon_count . "_steak.png', '$color Buffalo Steak Sandvich'),
					('claidheamohmor', $award_count, 0, '$game', '" . $ribbon_count . "_claidheamohmor.png', '$color Claidheamohmor'),
					('back_scratcher', $award_count, 0, '$game', '" . $ribbon_count . "_back_scratcher.png', '$color Back Scratcher'),
					('boston_basher', $award_count, 0, '$game', '" . $ribbon_count . "_boston_basher.png', '$color Boston Basher'),
					('steel_fists', $award_count, 0, '$game', '" . $ribbon_count . "_steel_fists.png', '$color Steel Fists'),
					('amputator', $award_count, 0, '$game', '" . $ribbon_count . "_amputator.png', '$color Amputator'),
					('tf_projectile_healing_bolt', $award_count, 0, '$game', '" . $ribbon_count . "_tf_projectile_healing_bolt.png', '$color Crusader''s Crossbow'),
					('ullapool_caber', $award_count, 0, '$game', '" . $ribbon_count . "_ullapool_caber.png', '$color Ullapool Caber'),
					('lochnload', $award_count, 0, '$game', '" . $ribbon_count . "_lochnload.png', '$color Loch-n-Load'),
					('brassbeast', $award_count, 0, '$game', '" . $ribbon_count . "_brassbeast.png', '$color Brass Beast'),
					('bear_claws', $award_count, 0, '$game', '" . $ribbon_count . "_bear_claws.png', '$color Warrior''s Spirit'),
					('candy_cane', $award_count, 0, '$game', '" . $ribbon_count . "_candy_cane.png', '$color Candy Cane'),
					('wrench_jag', $award_count, 0, '$game', '" . $ribbon_count . "_wrench_jag.png', '$color Jag'),
					('iron_curtain', $award_count, 0, '$game', '" . $ribbon_count . "_iron_curtain.png', '$color Iron Curtain'),
					('headtaker', $award_count, 0, '$game', '" . $ribbon_count . "_headtaker.png', '$color Horseless Headless Horseman''s Headtaker'),
					('madmilk', $award_count, 0, '$game', '" . $ribbon_count . "_madmilk.png', '$color Mad Milk');
			");	
		}
		
		$weapons = array(
			'claidheamohmor',
			'back_scratcher',
			'boston_basher',
			'steel_fists',
			'amputator',
			'tf_projectile_healing_bolt',
			'ullapool_caber',
			'lochnload',
			'brassbeast',
			'bear_claws',
			'candy_cane',
			'wrench_jag',
			'iron_curtain'
		);
		$tfservers = array();
		
		$result = $db->query("SELECT serverId FROM hlstats_Servers WHERE game = '$game'");
		while ($rowdata = $db->fetch_row($result))
		{ 
			array_push($tfservers, $db->escape($rowdata[0]));
		}
		if (count($tfservers) > 0)
		{
			$serverstring = implode (',', $tfservers);
			foreach ($weapons as $weapon) {
				$db->query("UPDATE hlstats_Weapons SET `kills` = `kills` + (IFNULL((SELECT count(weapon) FROM hlstats_Events_Frags WHERE `weapon` = '$weapon' AND `serverId` IN ($serverstring)),0)) WHERE `code` = '$weapon' AND `game` = '$game'");
			}
		}

	}
	
	$db->query("UPDATE hlstats_Options SET `value` = '$version' WHERE `keyname` = 'version'");
	$db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>
