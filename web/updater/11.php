<?php

	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}
	
	$db->query("
		UPDATE hlstats_Actions a INNER JOIN hlstats_Games b on a.game = b.code SET for_PlayerPlayerActions = '0', for_PlayerActions = '1' WHERE realgame='tf' AND a.code LIKE '%_extinguish'
		");
		
	$db->query("
		UPDATE hlstats_Options SET `value` = '11' WHERE `keyname` = 'dbversion'
		");
?>