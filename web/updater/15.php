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
				('W', '$game', 'sniper_awp','AWP','kills with awp'),
				('W', '$game', 'smg_mp5','MP5 Navy','kills with mp5'),
				('W', '$game', 'sniper_scout','Scout Elite','kills with scout'),
				('W', '$game', 'rifle_sg552','SG 552','kills with sg552'),
				('W', '$game', 'gnome', 'GET OFF MY LAWN', 'gnome smash kills')
			");
			
		$db->query("	
			INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
				('$game', 'adrenaline', 'Adrenaline Smash', 1.5),
				('$game', 'cola_bottles', 'Cola Bottles Smash', 1.5),
				('$game', 'env_explosion', 'Explosion', 1.5),
				('$game', 'env_fire', 'Fire', 1.5),
				('$game', 'fire_cracker_blast', 'Fire Cracker Blast', 1.5),
				('$game', 'fireworkcrate', 'Fireworks Crate Smash', 1.5),
				('$game', 'gnome', 'Gnome Smash', 1.5),
				('$game', 'rifle_sg552', 'Sig Sauer SG-552 Commando', 1),
				('$game', 'smg_mp5', 'H&K MP5-Navy', 1),
				('$game', 'sniper_awp', 'Arctic Warfare Magnum (Police)', 1),
				('$game', 'sniper_scout', 'Steyr Scout', 1),
				('$game', 'upgradepack_explosive', 'Explosive Pack', 1.5),
				('$game', 'world', 'World', 1)
			");
	}
	
	$db->query("
		UPDATE hlstats_Options SET `value` = '15' WHERE `keyname` = 'dbversion';
		");
?>