<?php

	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		
		
	$db->query("
		UPDATE hlstats_Options SET `value` = '20' WHERE `keyname` = 'dbversion'
	");
	
	$db->query("
		UPDATE hlstats_Options SET `value` = '1.6.3' WHERE `keyname` = 'version'
	");
?>