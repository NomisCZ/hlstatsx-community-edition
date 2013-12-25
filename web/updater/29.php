<?php

	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$tf2games = array();
	$tf2servers = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'tf'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($tf2games, "'".$db->escape($rowdata[0])."'");
	}
	if (count($tf2games) > 0)
	{
		$gamestring = implode (',', $tf2games);
		$db->query("UPDATE `hlstats_Awards` SET `code` = 'unique_pickaxe' WHERE `code` = 'pickaxe' AND `game` IN ($gamestring)");
		$db->query("UPDATE `hlstats_Weapons` SET `code` = 'unique_pickaxe' WHERE `code` = 'pickaxe' AND `game` IN ($gamestring)");
		$db->query("UPDATE `hlstats_Ribbons` SET `awardCode` = 'unique_pickaxe' WHERE `awardCode` = 'pickaxe' AND `game` IN ($gamestring)");
		
		$result = $db->query("SELECT serverId FROM hlstats_Servers WHERE game IN ($gamestring)");
		while ($rowdata = $db->fetch_row($result))
		{ 
			array_push($tf2servers, $db->escape($rowdata[0]));
		}
		if (count($tf2servers) > 0)
		{
			$serverstring = implode (',', $tf2servers);
			$db->query("UPDATE `hlstats_Events_Frags` SET `weapon` = 'unique_pickaxe' WHERE `weapon` = 'pickaxe'");
			$db->query("UPDATE `hlstats_Events_Suicides` SET `weapon` = 'unique_pickaxe' WHERE `weapon` = 'pickaxe'");
			$db->query("UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'unique_pickaxe' WHERE `weapon` = 'pickaxe'");
		}
	}

	$db->query("
		UPDATE hlstats_Options SET `value` = '29' WHERE `keyname` = 'dbversion'
	");
?>