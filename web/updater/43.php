<?php

	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$result = $db->query("SELECT `value` FROM hlstats_Options WHERE `keyname` = 'Proxy_Key'");
	if ($db->num_rows() == 0)
	{
		$db->query("INSERT INTO `hlstats_Options` (`keyname`, `value`, `opttype`) VALUES ('Proxy_Key', SUBSTRING(MD5(RAND()) FROM 1 FOR 24), 1)");
	}
	else
	{
		list($k) = $db->fetch_row($result);
		if ($k == "")
		{
			$db->query("UPDATE hlstats_Options SET `value` = SUBSTRING(MD5(RAND()) FROM 1 FOR 24) WHERE `keyname` = 'Proxy_Key'");
		}
	}

	$db->query("UPDATE hlstats_Options SET `value` = '43' WHERE `keyname` = 'dbversion'");	
?>