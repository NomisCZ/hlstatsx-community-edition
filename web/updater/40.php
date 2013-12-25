<?php
	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$tfgames = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'tf'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($tfgames, $db->escape($rowdata[0]));
	}

	$cssserverids = array();
	$result = $db->query("SELECT hlstats_Servers.serverId FROM hlstats_Servers JOIN hlstats_Games ON hlstats_Servers.game = hlstats_Games.code WHERE hlstats_Games.realgame = 'css'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($cssserverids, $db->escape($rowdata[0]));
	}
	
	foreach($tfgames as $game)
	{

		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('W','$game','frontier_justice', 'Justified Kills', 'kills with the Frontier Justice'),
				('W','$game','obj_sentrygun_mini', 'It''s Little and Cute and Ben''s Sister Likes It', 'Mini Sentry Gun kills'),
				('W','$game','robot_arm', 'Slings Slung', 'kills with the Gunslinger'),
				('W','$game','robot_arm_combo_kill', 'Mixed ya right up!', 'Engineer taunt kills (Gunslinger)'),
				('W','$game','southern_hospitality', 'Southern This!', 'kills with the Southern Hospitality'),
				('W','$game','taunt_guitar_kill', 'Despite all my rage there''s a Guitar on your head', 'Engineer taunt kills (Guitar)'),
				('W','$game','wrangler_kill', 'Rustlers Wrangled', 'kills with the Wrangler');
		");	
	
		$db->query("
			INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES
				('allsentrykills', 1, 0, '$game', '1_allsentrykills.png', 'Bronze Sentry Kills'),
				('allsentrykills', 5, 0, '$game', '2_allsentrykills.png', 'Silver Sentry Kills'),
				('allsentrykills', 10, 0, '$game', '3_allsentrykills.png', 'Gold Sentry Kills'),
				('ball', 1, 0, '$game', '1_ball.png', 'Bronze Baseball'),
				('ball', 5, 0, '$game', '2_ball.png', 'Silver Baseball'),
				('ball', 10, 0, '$game', '3_ball.png', 'Gold Baseball'),
				('engineer_extinguish', 1, 0, '$game', '1_engineer_extinguish.png', 'Bronze Engineer Extinguish'),
				('engineer_extinguish', 5, 0, '$game', '2_engineer_extinguish.png', 'Silver Engineer Extinguish'),
				('engineer_extinguish', 10, 0, '$game', '3_engineer_extinguish.png', 'Engineer Extinguish'),
				('force_a_nature', 1, 0, '$game', '1_force_a_nature.png', 'Bronze Force-A-Nature'),
				('force_a_nature', 5, 0, '$game', '2_force_a_nature.png', 'Silver Force-A-Nature'),
				('force_a_nature', 10, 0, '$game', '3_force_a_nature.png', 'Gold Force-A-Nature'),
				('frontier_justice', 1, 0, '$game', '1_frontier_justice.png', 'Bronze Frontier Justice'),
				('frontier_justice', 5, 0, '$game', '2_frontier_justice.png', 'Silver Frontier Justice'),
				('frontier_justice', 10, 0, '$game', '3_frontier_justice.png', 'Gold Frontier Justice'),
				('killedobject_obj_dispenser', 1, 0, '$game', '1_killedobject_obj_dispenser.png', 'Bronze Dispensers Destroyed'),
				('killedobject_obj_dispenser', 5, 0, '$game', '2_killedobject_obj_dispenser.png', 'Silver Dispensers Destroyed'),
				('killedobject_obj_dispenser', 10, 0, '$game', '3_killedobject_obj_dispenser.png', 'Gold Dispensers Destroyed'),
				('killedobject_obj_sentrygun', 1, 0, '$game', '1_killedobject_obj_sentrygun.png', 'Bronze Sentry Guns Destroyed'),
				('killedobject_obj_sentrygun', 5, 0, '$game', '2_killedobject_obj_sentrygun.png', 'Silver Sentry Guns Destroyed'),
				('killedobject_obj_sentrygun', 10, 0, '$game', '3_killedobject_obj_sentrygun.png', 'Gold Sentry Guns Destroyed'),
				('medic_extinguish', 1, 0, '$game', '1_medic_extinguish.png', 'Bronze Medic Entinguish'),
				('medic_extinguish', 5, 0, '$game', '2_medic_extinguish.png', 'Silver Medic Entinguish'),
				('medic_extinguish', 10, 0, '$game', '3_medic_extinguish.png', 'Gold Medic Entinguish'),
				('mvp1', 1, 0, '$game', '1_mvp1.png', 'Bronze Most Valuable Player'),
				('mvp1', 5, 0, '$game', '2_mvp1.png', 'Silver Most Valuable Player'),
				('mvp1', 10, 0, '$game', '3_mvp1.png', 'Gold Most Valuable Player'),
				('obj_sentrygun_mini', 1, 0, '$game', '1_obj_sentrygun_mini.png', 'Bronze Mini Sentrygun'),
				('obj_sentrygun_mini', 5, 0, '$game', '2_obj_sentrygun_mini.png', 'Silver Mini Sentrygun'),
				('obj_sentrygun_mini', 10, 0, '$game', '3_obj_sentrygun_mini.png', 'Gold Mini Sentrygun'),
				('paintrain', 1, 0, '$game', '1_paintrain.png', 'Bronze Paintrain'),
				('paintrain', 5, 0, '$game', '2_paintrain.png', 'Silver Paintrain'),
				('paintrain', 10, 0, '$game', '3_paintrain.png', 'Gold Paintrain'),
				('pyro_extinguish', 1, 0, '$game', '1_pyro_extinguish.png', 'Bronze Pyro Extinguish'),
				('pyro_extinguish', 5, 0, '$game', '2_pyro_extinguish.png', 'Silver Pyro Extinguish'),
				('pyro_extinguish', 10, 0, '$game', '3_pyro_extinguish.png', 'Gold Pyro Extinguish'),
				('robot_arm', 1, 0, '$game', '1_robot_arm.png', 'Bronze Gunslinger'),
				('robot_arm', 5, 0, '$game', '2_robot_arm.png', 'Silver Gunslinger'),
				('robot_arm', 10, 0, '$game', '3_robot_arm.png', 'Gold Gunslinger'),
				('robot_arm_blender_kill', 1, 0, '$game', '1_robot_arm_blender_kill.png', 'Bronze Engineer Taunt (Gunslinger)'),
				('robot_arm_blender_kill', 5, 0, '$game', '2_robot_arm_blender_kill.png', 'Silver Engineer Taunt (Gunslinger)'),
				('robot_arm_blender_kill', 10, 0, '$game', '3_robot_arm_blender_kill.png', 'Gold Engineer Taunt (Gunslinger)'),
				('sledgehammer', 1, 0, '$game', '1_sledgehammer.png', 'Bronze Homewrecker'),
				('sledgehammer', 5, 0, '$game', '2_sledgehammer.png', 'Silver Homewrecker'),
				('sledgehammer', 10, 0, '$game', '3_sledgehammer.png', 'Gold Homewrecker'),
				('sniper_extinguish', 1, 0, '$game', '1_sniper_extinguish.png', 'Bronze Sniper Extinguish'),
				('sniper_extinguish', 5, 0, '$game', '2_sniper_extinguish.png', 'Silver Sniper Extinguish'),
				('sniper_extinguish', 10, 0, '$game', '3_sniper_extinguish.png', 'Gold Sniper Extinguish'),
				('southern_hospitality', 1, 0, '$game', '1_southern_hospitality.png', 'Bronze Southern Hospitality'),
				('southern_hospitality', 5, 0, '$game', '2_southern_hospitality.png', 'Silver Southern Hospitality'),
				('southern_hospitality', 10, 0, '$game', '3_southern_hospitality.png', 'Gold Southern Hospitality'),
				('taunt_guitar_kill', 1, 0, '$game', '1_taunt_guitar_kill.png', 'Bronze Engineer Taunt (Guitar)'),
				('taunt_guitar_kill', 5, 0, '$game', '2_taunt_guitar_kill.png', 'Silver Engineer Taunt (Guitar)'),
				('taunt_guitar_kill', 10, 0, '$game', '3_taunt_guitar_kill.png', 'Gold Engineer Taunt (Guitar)'),
				('telefrag', 1, 0, '$game', '1_telefrag.png', 'Bronze Telefrags'),
				('telefrag', 5, 0, '$game', '2_telefrag.png', 'Silver Telefrags'),
				('telefrag', 10, 0, '$game', '3_telefrag.png', 'Gold Telefrags'),
				('wrangler_kill', 1, 0, '$game', '1_wrangler_kill.png', 'Bronze Wrangler'),
				('wrangler_kill', 5, 0, '$game', '2_wrangler_kill.png', 'Silver Wrangler'),
				('wrangler_kill', 10, 0, '$game', '3_wrangler_kill.png', 'Gold Wrangler');

		");	
		
		$db->query("
			INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
				('$game', 'frontier_justice', 'Frontier Justice', '1.00'),
				('$game', 'obj_sentrygun_mini', 'Sentry Gun (Mini)', '1.00'),
				('$game', 'robot_arm', 'Gunslinger', '2.00'),
				('$game', 'robot_arm_blender_kill', 'Engineer Taunt (Gunslinger)', '5.00'),
				('$game', 'southern_hospitality', 'Southern Hospitality', '2.00'),
				('$game', 'taunt_guitar_kill', 'Engineer Taunt (Guitar)', '2.00'),
				('$game', 'wrangler_kill', 'Wrangler', '1.00');
		");
	}
	
	foreach ($cssserverids as $serverid)
	{
		$db->query("UPDATE hlstats_Servers_Config SET `value` = '3' WHERE `parameter` = 'GameEngine' AND `serverId` = '$serverid'");
	}

	$db->query("UPDATE hlstats_Games_Defaults SET `value` = '3' WHERE `parameter` = 'GameEngine' AND `code` = 'css'");
	
	$db->query("UPDATE hlstats_Options SET `value` = '1.6.10' WHERE `keyname` = 'version'");
	$db->query("UPDATE hlstats_Options SET `value` = '40' WHERE `keyname` = 'dbversion'");
?>
