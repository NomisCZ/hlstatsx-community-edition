<?php

	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		
		
	$l4dgames = array();
	// Detect l4d2 since l4d and l4d2 share a "realgame"
	$result = $db->query("SELECT game FROM hlstats_Weapons WHERE code = 'vomitjar'");
	while ($rowdata = $db->fetch_row($result))
	{
		array_push($l4dgames, $db->escape($rowdata[0]));
	}
	
	foreach($l4dgames as $game)
	{
		// This is really grenade launcher smash, not the projectile
		$db->query("	
			UPDATE `hlstats_Awards` SET `name`='More than one way to kill with a Grenade Launcher', verb='Grenade Launcher smash kills' WHERE game='$game' AND code='grenade_launcher'
			");

		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('W', '$game', 'chainsaw', 'Slice and Dice', 'kills with the Chainsaw'),
				('W', '$game', 'fire_cracker_blast', 'Snap Crackle Pop', 'kills with fire crackers'),
				('W', '$game', 'grenade_launcher_projectile', 'Black Scottish Psyclops', 'kills with the Grenade Launcher')
			");
			
		$db->query("
			UPDATE `hlstats_Weapons` SET `name`='Grenade Launcher Smash', modifier=1.5 WHERE game='$game' AND code='grenade_launcher'
			");
		
		$db->query("
			UPDATE `hlstats_Weapons` SET `name`='Insect Swarm' WHERE game='$game' AND name='insect_swarm'
			");
		
		$db->query("	
			INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
				('$game', 'chainsaw', 'Chainsaw', 1),
				('$game', 'fire_cracker_blast', 'Fire Cracker', 1),
				('$game', 'grenade_launcher_projectile', 'Grenade Launcher', 0.75),
				('$game', 'upgradepack_incendiary', 'Incendiary Pack Smash', 1.5)
			");
	}
	
	$db->query("
		UPDATE hlstats_Options SET `value` = '13' WHERE `keyname` = 'dbversion';
		");
?>