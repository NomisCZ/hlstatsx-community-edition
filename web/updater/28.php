<?php

	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$db->query("
		UPDATE hlstats_Options SET `value` = '1.6.6' WHERE `keyname` = 'version'
	");
	
	$db->query("
		UPDATE hlstats_Options SET `value` = '28' WHERE `keyname` = 'dbversion'
	");
?>