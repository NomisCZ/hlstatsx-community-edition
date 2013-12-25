<?php
	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$dbversion = 51;
	$version = "1.6.11-beta1";

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
				('$game', 'killedobject_obj_sentrygun_mini', 4, 0, '', 'Destroyed a mini sentry gun', '1', '', '', ''),
				('$game', 'builtobject_obj_sentrygun_mini', 3, 0, '', 'Built a mini sentry gun', '1', '', '', ''),
				('$game', 'owner_killedobject_obj_sentrygun_mini', -3, 0, '', 'Disassembled a mini sentry gun', '1', '', '', '');
		");
		
		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('O','$game','killedobject_obj_sentrygun_mini','Say no to mini sentries','mini sentry guns destroyed'),
				('O','$game','builtobject_obj_sentrygun_mini','Mini Bob the Builder','mini sentry guns built');
		");
		
		$db->query("
			INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES
				('builtobject_obj_sentrygun_mini', 1, 0, '$game', '1_builtobject_obj_sentrygun_mini.png', 'Bronze Built Mini Sentry Gun'),
				('builtobject_obj_sentrygun_mini', 5, 0, '$game', '2_builtobject_obj_sentrygun_mini.png', 'Silver Built Mini Sentry Gun'),
				('builtobject_obj_sentrygun_mini', 10, 0, '$game', '3_builtobject_obj_sentrygun_mini.png', 'Gold Built Mini Sentry Gun'),
				('killedobject_obj_sentrygun_mini', 1, 0, '$game', '1_killedobject_obj_sentrygun_mini.png', 'Bronze Mini Sentry Guns Destroyed'),
				('killedobject_obj_sentrygun_mini', 5, 0, '$game', '2_killedobject_obj_sentrygun_mini.png', 'Silver Mini Sentry Guns Destroyed'),
				('killedobject_obj_sentrygun_mini', 10, 0, '$game', '3_killedobject_obj_sentrygun_mini.png', 'Gold Mini Sentry Guns Destroyed');
		");
	}
	
	$db->query("UPDATE hlstats_Options SET `value` = '$version' WHERE `keyname` = 'version'");
	$db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>
