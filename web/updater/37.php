<?php

	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$db->query("
		UPDATE `hlstats_Games_Defaults`
		SET `value` = 0
		WHERE `code`='tf' AND `parameter` = 'TKPenalty'
	");
	
	$db->query("UPDATE hlstats_Options SET `value` = '37' WHERE `keyname` = 'dbversion'");	
?>
