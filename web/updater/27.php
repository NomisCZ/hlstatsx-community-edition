<?php

	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$l4d2games = array();
	$l4d2servers = array();
	// L4D2 shares the same "realgame" as L4D so we'll lookup codes via another method
	$result = $db->query("SELECT `game` FROM `hlstats_Weapons` WHERE `code` = 'shotgun_spas'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($l4d2games, "'".$db->escape($rowdata[0])."'");
	}
	if (count($l4d2games) > 0)
	{
		$gamestring = implode (',', $l4d2games);
		$db->query("UPDATE `hlstats_Actions` SET `code` = 'killed_smoker' WHERE `code` = 'killed_gas' AND `game` IN ($gamestring)");
		$db->query("UPDATE `hlstats_Actions` SET `code` = 'killed_boomer' WHERE `code` = 'killed_exploding' AND `game` IN ($gamestring)");
		$db->query("UPDATE `hlstats_Awards` SET `code` = 'killed_smoker' WHERE `code` = 'killed_gas' AND `game` IN ($gamestring)");
		$db->query("UPDATE `hlstats_Awards` SET `code` = 'killed_boomer' WHERE `code` = 'killed_exploding' AND `game` IN ($gamestring)");
		$db->query("UPDATE `hlstats_Roles` SET `code` = 'SMOKER' WHERE `code` = 'GAS' AND `game` IN ($gamestring)");
		$db->query("UPDATE `hlstats_Roles` SET `code` = 'BOOMER' WHERE `code` = 'EXPLODING' AND `game` IN ($gamestring)");
		
		$result = $db->query("SELECT serverId FROM hlstats_Servers WHERE game IN ($gamestring)");
		while ($rowdata = $db->fetch_row($result))
		{ 
			array_push($l4d2servers, $db->escape($rowdata[0]));
		}
		if (count($l4d2servers) > 0)
		{
			$serverstring = implode (',', $l4d2servers);
			$db->query("UPDATE `hlstats_Events_ChangeRole` SET `role` = 'SMOKER' WHERE serverId IN ($serverstring) AND `role` = 'GAS'");
			$db->query("UPDATE `hlstats_Events_ChangeRole` SET `role` = 'BOOMER' WHERE serverId IN ($serverstring) AND `role` = 'EXPLODING'");
			$db->query("UPDATE `hlstats_Events_Frags` SET `killerRole` = 'SMOKER' WHERE serverId IN ($serverstring) AND `killerRole` = 'GAS'");
			$db->query("UPDATE `hlstats_Events_Frags` SET `killerRole` = 'BOOMER' WHERE serverId IN ($serverstring) AND `killerRole` = 'EXPLODING'");
			$db->query("UPDATE `hlstats_Events_Frags` SET `victimRole` = 'SMOKER' WHERE serverId IN ($serverstring) AND `victimRole` = 'GAS'");
			$db->query("UPDATE `hlstats_Events_Frags` SET `victimRole` = 'BOOMER' WHERE serverId IN ($serverstring) AND `victimRole` = 'EXPLODING'");
		}
	}

	$db->query("
		UPDATE hlstats_Options SET `value` = '27' WHERE `keyname` = 'dbversion'
	");
?>