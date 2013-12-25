<?php
	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$dbversion = 64;
	$version = "1.6.13";
	
	$db->query("UPDATE hlstats_Options SET `value` = '$version' WHERE `keyname` = 'version'");
	$db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>
