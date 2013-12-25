<?php

	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		
	
	$db->query("
		ALTER TABLE hlstats_Events_Chat ADD KEY `playerId` (`playerId`)
		", false);
		
	$db->query("
		ALTER TABLE hlstats_Events_Chat ADD KEY `serverId` (`serverId`)
		", false);
	
	$db->query("
		UPDATE hlstats_Options SET `value` = '14' WHERE `keyname` = 'dbversion';
		");
?>