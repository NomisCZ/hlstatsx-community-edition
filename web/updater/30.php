<?php

	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$db->query("
		CREATE FULLTEXT INDEX message ON hlstats_Events_Chat (message)
	", false);

	$db->query("
		UPDATE hlstats_Options SET `value` = '30' WHERE `keyname` = 'dbversion'
	");
?>