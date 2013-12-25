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
	
	foreach($tfgames as $game)
	{

		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('W','$game','bleed_kill', 'We''ve got a bleeder', 'bleed kills');
		");
		
		$db->query("
			INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
				('$game', 'samrevolver', 'Big Kill', 1.00),
				('$game', 'wrench_golden', 'Golden Wrench', 2.00),
				('$game', 'maxgun', 'Lugermorph', 1.50),
				('$game', 'robot_arm_combo_kill', 'Gunslinger Combo', 2.00),
				('$game', 'bleed_kill', 'Bleed Kill', 1.80)
		");
	}
	
	$dysserverids = array();
	$result = $db->query("SELECT hlstats_Servers.serverId FROM hlstats_Servers JOIN hlstats_Games ON hlstats_Servers.game = hlstats_Games.code WHERE hlstats_Games.realgame = 'dystopia'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($dysserverids, $db->escape($rowdata[0]));
	}
	
	foreach ($dysserverids as $serverid)
	{
		$db->query("UPDATE hlstats_Servers_Config SET `value` = '3' WHERE `parameter` = 'GameEngine' AND `serverId` = '$serverid'");
	}

	$db->query("UPDATE hlstats_Games_Defaults SET `value` = '3' WHERE `parameter` = 'GameEngine' AND `code` = 'dystopia'");
	
	$db->query("UPDATE hlstats_Options SET `value` = '44' WHERE `keyname` = 'dbversion'");
?>
