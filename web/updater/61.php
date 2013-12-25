<?php
	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$dbversion = 61;
	$version = "1.6.12";
	
	$changed_weapons = array(
		'glovesurgent'               => 'gloves_running_urgently',
		'sydneysleeper'              => 'sydney_sleeper',
		'lochnload'                  => 'loch_n_load',
		'brassbeast'                 => 'brass_beast',
		'bear_claws'                 => 'warrior_spirit',
		'obj_sentrygun_mini'         => 'obj_minisentry',
		'tf_projectile_healing_bolt' => 'crusaders_crossbow'
	);
	
	$tfgames = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'tf'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($tfgames, $db->escape($rowdata[0]));
	}
	
	foreach ($tfgames as $game)
	{
		$tfservers = array();
		
		$result = $db->query("SELECT serverId FROM hlstats_Servers WHERE game = '$game'");
		while ($rowdata = $db->fetch_row($result))
		{ 
			array_push($tfservers, $db->escape($rowdata[0]));
		}
		$cnt = count($tfservers);
		if ($cnt == 0)
		{
			break;
		}
		
		if ($cnt == 1)
		{
			$serverclause = 'serverId='.$tfservers[0];
		}
		else if ($cnt > 1)
		{
			$serverclause = 'serverId IN ('.implode (',', $tfservers).')';
		}
		
		foreach ($changed_weapons as $old => $new)
		{
			$db->query("UPDATE hlstats_Awards SET `code`='$new' WHERE game='$game' AND `code`='$old' AND awardType='W'");
			$db->query("UPDATE hlstats_Ribbons SET awardCode='$new' WHERE game='$game' AND awardCode='$old'");
			
			$db->query("SELECT COUNT(`code`) FROM hlstats_Weapons WHERE game='$game' AND `code`='$new'");
			list($exists) = $db->fetch_row();
			if (!$exists)
			{
				$db->query("UPDATE hlstats_Weapons SET `code`='$new', `kills` = `kills` + (IFNULL((SELECT count(weapon) FROM hlstats_Events_Frags WHERE `weapon` = '$old' AND $serverclause),0)) WHERE `code` = '$old' AND `game` = '$game'");
			}
			
			$db->query("UPDATE hlstats_Events_Frags SET weapon='$new' WHERE weapon = '$old' AND $serverclause");
			$db->query("UPDATE hlstats_Events_Statsme SET weapon='$new' WHERE weapon = '$old' AND $serverclause");
			$db->query("UPDATE hlstats_Events_Statsme2 SET weapon='$new' WHERE weapon = '$old' AND $serverclause");
			$db->query("UPDATE hlstats_Events_Suicides SET weapon='$new' WHERE weapon = '$old' AND $serverclause");
			$db->query("UPDATE hlstats_Events_Teamkills SET weapon='$new' WHERE weapon = '$old' AND $serverclause");
		}
		
		$db->query("INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
			('W', '$game', 'ullapool_caber', 'Boom Sticka', 'Caber BOOM kills')");
		
		$db->query("INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`, `kills`) VALUES
			('$game', 'ullapool_caber_explosion', 'The Ullapool Caber BOOM', 2.0, (IFNULL((SELECT count(weapon) FROM hlstats_Events_Frags WHERE `weapon` = 'ullapool_caber_explosion' AND $serverclause),0)))");
		
		$db->query("INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES
			('ullapool_caber_explosion', 1, 0, '$game', '1_ullapool_caber_explosion.png', 'Bronze Ullapool Caber BOOM'),
			('ullapool_caber_explosion', 5, 0, '$game', '2_ullapool_caber_explosion.png', 'Silver Ullapool Caber BOOM'),
			('ullapool_caber_explosion', 10, 0, '$game', '3_ullapool_caber_explosion.png', 'Gold Ullapool Caber BOOM')");
	}
	
	$db->query("UPDATE hlstats_Options SET `value` = '$version' WHERE `keyname` = 'version'");
	$db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>
