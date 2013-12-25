<?php

	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$tf2games = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'tf'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($tf2games, "'".$db->escape($rowdata[0])."'");
	}
	if (count($tf2games) > 0)
	{
		$gamestring = implode (',', $tf2games);
		$db->query("
			UPDATE `hlstats_Actions` SET `for_PlayerActions`='1', `for_PlayerPlayerActions`='0'
				WHERE `code` IN ('buff_deployed','defended_medic') AND `game` IN ($gamestring)
		");
	}

	$db->query("
		UPDATE hlstats_Options SET `value` = '24' WHERE `keyname` = 'dbversion'
	");
?>