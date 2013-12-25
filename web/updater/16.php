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
				('$game', 'tf_pumpkin_bomb', 'Pumpkin Bomb', 2);
			");
			
		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('W','$game','tf_pumpkin_bomb', 'Pumpkin Bomber', 'kills with a pumpkin bomb'),
				('O','$game', 'engineer_extinguish', 'Dispensing a little love', 'extinguishes with a Dispensor'),
				('O','$game', 'medic_extinguish', 'You want a second opinion? You''re also ugly! ', 'extinguishes with Medic Gun');
			");
			
		$db->query("
			INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES
				('$game', 'jarate', 1, 0, '', 'Jarated player', '0', '1', '0', '0'),
				('$game', 'shield_blocked', 0, 0, '', 'Blocked with Shield', '0', '1', '0', '0');
			");
	}
	
			
	$zpsgames = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'zps'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($zpsgames, $db->escape($rowdata[0]));
	}
	
	foreach($zpsgames as $game)
	{
		$db->query("
			INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
				('$game', 'bat_aluminum', 'Bat (Aluminum)', 1.5),
				('$game', 'bat_wood', 'Bat (Wood)', 1.5),
				('$game', 'm4', 'M4', 1),
				('$game', 'pipe', 'Pipe', 1),
				('$game', 'slam', 'IED', 1);
			");
			
		$db->query("
			INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES
				('$game', 'headshot', 1, 0, '', 'Headshot Kill', '1', '0', '0', '0')
			");

		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('W', '$game','bat_aluminum','Out of the park!','kills with Bat (Aluminum)'),
				('W', '$game', 'bat_wood','Corked','kills with Bat (Wood)'),
				('W', '$game', 'm4','M4','kills with M4'),
				('W', '$game', 'pipe','Piping hot','kills with Pipe'),
				('W', '$game', 'slam','IEDs','kills with IED'),
				('O', '$game', 'headshot', 'Headshot King', 'headshot kills');
			");
	}

	$ntsgames = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'nts'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($ntsgames, $db->escape($rowdata[0]));
	}
	
	foreach($ntsgames as $game)
	{
		$db->query("
			DELETE FROM hlstats_Awards WHERE `code` = 'mp5' AND `game` = '$game'
			");
	
		$db->query("
			DELETE FROM hlstats_Weapons WHERE `code` = 'mp5' AND `game` = '$game'
			");
			
		$db->query("
			INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES
				('$game', 'headshot', 5, 0, '', 'Headshot Kill', '1', '0', '0', '0'),
				('$game', 'kill_streak_10', 9, 0, '', 'Monster Kill (10 kills)', '1', '0', '0', '0'),
				('$game', 'kill_streak_11', 10, 0, '', 'Unstoppable (11 kills)', '1', '0', '0', '0'),
				('$game', 'kill_streak_12', 15, 0, '', 'God Like (12+ kills)', '1', '0', '0', '0'),
				('$game', 'kill_streak_2', 1, 0, '', 'Double Kill (2 kills)', '1', '0', '0', '0'),
				('$game', 'kill_streak_3', 2, 0, '', 'Triple Kill (3 kills)', '1', '0', '0', '0'),
				('$game', 'kill_streak_4', 3, 0, '', 'Domination (4 kills)', '1', '0', '0', '0'),
				('$game', 'kill_streak_5', 4, 0, '', 'Rampage (5 kills)', '1', '0', '0', '0'),
				('$game', 'kill_streak_6', 5, 0, '', 'Mega Kill (6 kills)', '1', '0', '0', '0'),
				('$game', 'kill_streak_7', 6, 0, '', 'Ownage (7 kills)', '1', '0', '0', '0'),
				('$game', 'kill_streak_8', 7, 0, '', 'Ultra Kill (8 kills)', '1', '0', '0', '0'),
				('$game', 'kill_streak_9', 8, 0, '', 'Killing Spree (9 kills)', '1', '0', '0', '0'),
				('$game', 'Round_Win', 0, 20, '', 'Team Round Win', '0', '0', '1', '0');
			");
			
		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('O', '$game', 'headshot', 'Headshot King', 'headshot kills');
			");
			
		$db->query("
			INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES
				('aa13', 1, 0, '$game', '1_aa13.png', 'Bronze AA13'),
				('aa13', 5, 0, '$game', '2_aa13.png', 'Silver AA13'),
				('aa13', 10, 0, '$game', '3_aa13.png', 'Gold AA13'),
				('grenade_projectile', 1, 0, '$game', '1_grenade.png', 'Bronze Frag Grenade'),
				('grenade_projectile', 5, 0, '$game', '2_grenade.png', 'Silver Frag Grenade'),
				('grenade_projectile', 10, 0, '$game', '3_grenade.png', 'Gold Frag Grenade'),
				('headshot', 1, 0, '$game', '1_headshot.png', 'Bronze Headshot'),
				('headshot', 5, 0, '$game', '2_headshot.png', 'Silver Headshot'),
				('headshot', 10, 0, '$game', '3_headshot.png', 'Gold Headshot'),
				('knife', 1, 0, '$game', '1_knife.png', 'Bronze Knife'),
				('knife', 5, 0, '$game', '2_knife.png', 'Silver Knife '),
				('knife', 10, 0, '$game', '3_knife.png', 'Gold Knife '),
				('kyla', 1, 0, '$game', '1_kyla9.png', 'Bronze Kyla-9'),
				('kyla', 5, 0, '$game', '2_kyla9.png', 'Silver Kyla-9'),
				('kyla', 10, 0, '$game', '3_kyla9.png', 'Gold Kyla-9'),
				('latency', 1, 0, '$game', '1_latency.png', 'Bronze Best Latency'),
				('latency', 5, 0, '$game', '2_latency.png', 'Silver Best Latency'),
				('latency', 10, 0, '$game', '3_latency.png', 'Gold Best Latency'),
				('m41', 1, 0, '$game', '1_m41.png', 'Bronze M41'),
				('m41', 5, 0, '$game', '2_m41.png', 'Silver M41'),
				('m41', 10, 0, '$game', '3_m41.png', 'Gold M41'),
				('m41l', 1, 0, '$game', '1_m41l.png', 'Bronze M41L'),
				('m41l', 5, 0, '$game', '2_m41l.png', 'Silver M41L'),
				('m41l', 10, 0, '$game', '3_m41l.png', 'Gold M41L'),
				('milso', 1, 0, '$game', '1_milso.png', 'Bronze MilSO'),
				('milso', 5, 0, '$game', '2_milso.png', 'Silver MilSO'),
				('milso', 10, 0, '$game', '3_milso.png', 'Gold MilSO'),
				('mostkills', 1, 0, '$game', '1_mostkills.png', 'Bronze Most Kills'),
				('mostkills', 5, 0, '$game', '2_mostkills.png', 'Silver Most Kills'),
				('mostkills', 10, 0, '$game', '3_mostkills.png', 'Gold Most Kills'),
				('mpn', 1, 0, '$game', '1_mpn45.png', 'Bronze MPN45'),
				('mpn', 5, 0, '$game', '2_mpn45.png', 'Silver MPN45'),
				('mpn', 10, 0, '$game', '3_mpn45.png', 'Gold MPN45'),
				('mx', 1, 0, '$game', '1_mx-5.png', 'Bronze MX'),
				('mx', 5, 0, '$game', '2_mx-5.png', 'Silver MX'),
				('mx', 10, 0, '$game', '3_mx-5.png', 'Gold MX'),
				('mx_silenced', 1, 0, '$game', '1_mxs-5.png', 'Bronze MX Silenced'),
				('mx_silenced', 5, 0, '$game', '2_mxs-5.png', 'Silver MX Silenced'),
				('mx_silenced', 10, 0, '$game', '3_mxs-5.png', 'Gold MX Silenced'),
				('pz', 1, 0, '$game', '1_supa7.png', 'Bronze MURATA SUPA 7'),
				('pz', 5, 0, '$game', '2_supa7.png', 'Silver MURATA SUPA 7'),
				('supa7', 10, 0, '$game', '3_supa7.png', 'Gold MURATA SUPA 7'),
				('tachi', 1, 0, '$game', '1_tachi.png', 'Bronze TACHI'),
				('tachi', 5, 0, '$game', '2_tachi.png', 'Silver TACHI'),
				('tachi', 10, 0, '$game', '3_tachi.png', 'Gold TACHI'),
				('zr68c', 1, 0, '$game', '1_zr68c.png', 'Bronze ZR68C'),
				('zr68c', 5, 0, '$game', '2_zr68c.png', 'Silver ZR68C'),
				('zr68c', 10, 0, '$game', '3_zr68c.png', 'Gold ZR68C'),
				('zr68l', 1, 0, '$game', '1_zr68l.png', 'Bronze ZR68L'),
				('zr68l', 5, 0, '$game', '2_zr68l.png', 'Silver ZR68L'),
				('zr68l', 10, 0, '$game', '3_zr68l.png', 'Gold ZR68L'),
				('zr68s', 1, 0, '$game', '1_zr68s.png', 'Bronze ZR68S'),
				('zr68s', 5, 0, '$game', '2_zr68s.png', 'Silver ZR68S'),
				('zr68s', 10, 0, '$game', '3_zr68s.png', 'Gold ZR68S');
			");
	}
	
	$db->query("
		UPDATE hlstats_Options SET `value` = '16' WHERE `keyname` = 'dbversion';
		");
?>