<?php

	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		
		
	$db->query("
		ALTER TABLE hlstats_Servers_VoiceComm
			DROP KEY `address`,
			ADD UNIQUE KEY `address` (`addr`,`UDPPort`,`queryPort`)
		", false);
	
	
	$db->query("
		UPDATE hlstats_Options SET `value` = '19' WHERE `keyname` = 'dbversion';
		");
?>