<?php

	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$aocgames = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'aoc'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($aocgames, $db->escape($rowdata[0]));
	}
	
	$gamestring="";
	
	foreach($aocgames as $game)
	{
		$db->query("INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
		('$game','Fists','Fists',2.0),
		('$game','Throwing Axe','Throwing Axe',1.0)");
		
		$db->query("INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
		('W','$game','Fists','Fists','kills with fists'),
		('W','$game','Throwing Axe','Throwing Axe','kills with throwing axes')");
		
		$gamestring.="'$game',";
	}
	
	if ($gamestring)
	{
		$gamestring = '('.preg_replace('/,$/', '', $gamestring).')';
		
		$db->query("UPDATE IGNORE hlstats_Awards SET code='Oil Pot' WHERE game IN $gamestring AND awardType='W' AND code='oilpot'");
		$db->query("UPDATE IGNORE hlstats_Awards SET code='chivalry' WHERE game IN $gamestring AND awardType='W' AND code='env_explosion'");		
		$db->query("UPDATE IGNORE hlstats_Weapons SET code='Oil Pot' WHERE game IN $gamestring AND code='oilpot'");
		$db->query("UPDATE IGNORE hlstats_Weapons SET code='chivalry' WHERE game IN $gamestring AND code='env_explosion'");
		
		$serveridstring = "";
		
		$aocservers = array();
		$result = $db->query("SELECT serverId FROM hlstats_Servers WHERE game IN $gamestring");
		while ($rowdata = $db->fetch_row($result))
		{ 
			array_push($aocservers, $db->escape($rowdata[0]));
		}
		
		foreach($aocservers as $server)
		{
			$serveridstring.="$server,";
		}
		if ($serveridstring)
		{
			$serveridstring = '('.preg_replace('/,$/', '', $serveridstring).')';
			
			$db->query("UPDATE hlstats_Events_Frags SET weapon='Oil Pot' WHERE serverId IN $serveridstring AND weapon='oilpot'");
			$db->query("UPDATE hlstats_Events_Statsme SET weapon='Oil Pot' WHERE serverId IN $serveridstring AND weapon='oilpot'");
			$db->query("UPDATE hlstats_Events_Statsme2 SET weapon='Oil Pot' WHERE serverId IN $serveridstring AND weapon='oilpot'");
			$db->query("UPDATE hlstats_Events_Suicides SET weapon='Oil Pot' WHERE serverId IN $serveridstring AND weapon='oilpot'");
			$db->query("UPDATE hlstats_Events_Teamkills SET weapon='Oil Pot' WHERE serverId IN $serveridstring AND weapon='oilpot'");
			
			$db->query("UPDATE hlstats_Events_Frags SET weapon='chivalry' WHERE serverId IN $serveridstring AND weapon='env_explosion'");
			$db->query("UPDATE hlstats_Events_Statsme SET weapon='chivalry' WHERE serverId IN $serveridstring AND weapon='env_explosion'");
			$db->query("UPDATE hlstats_Events_Statsme2 SET weapon='chivalry' WHERE serverId IN $serveridstring AND weapon='env_explosion'");
			$db->query("UPDATE hlstats_Events_Suicides SET weapon='chivalry' WHERE serverId IN $serveridstring AND weapon='env_explosion'");
			$db->query("UPDATE hlstats_Events_Teamkills SET weapon='chivalry' WHERE serverId IN $serveridstring AND weapon='env_explosion'");
		}
	}

	$db->query("UPDATE hlstats_Options SET `value` = '42' WHERE `keyname` = 'dbversion'");	
?>