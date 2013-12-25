<?php

	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		
		
	$db->query("
		UPDATE hlstats_Options SET `opttype` = '2' WHERE `keyname` = 'display_style_selector';
		");
	
	$db->query("
		UPDATE hlstats_Options SET `value` = '17' WHERE `keyname` = 'dbversion';
		");
?>