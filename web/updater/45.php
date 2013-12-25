<?php
	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$cssgames = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'css'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($cssgames, $db->escape($rowdata[0]));
	}
	
	foreach($cssgames as $game)
	{

		$db->query("
			INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES
				('$game','round_mvp',0,0,'','Round MVP','1','','','')
		");
		
		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('O','$game','round_mvp','Most Valuable Player','times earning Round MVP')
		");
	}
	
	$db->query("UPDATE hlstats_Options SET `value` = '45' WHERE `keyname` = 'dbversion'");
?>
