<?php

	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}
	
	$db->query("
		INSERT INTO `hlstats_Options_Choices` (`keyname`, `value`, `text`, `isDefault`) VALUES
		('google_map_region', 'TAIWAN', 'Taiwan', 0)
		");
		
		
	$l4dgames = array();
	$result = "SELECT code FROM hlstats_Games WHERE realgame = 'l4d'";
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($l4dgames, $db->escape($rowdata[0]));
	}
	
	foreach($l4dgames as $game)
	{
		$db->query("
			UPDATE hlstats_Awards SET verb = 'teammate protections' WHERE game='$game' AND code = 'protect_teammate' AND verb = 'hunter punts'
			");
	}
	
	$db->query("
		UPDATE hlstats_Options SET `value` = '12' WHERE `keyname` = 'dbversion'
		");
?>