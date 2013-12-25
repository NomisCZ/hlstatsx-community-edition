<?php
	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$tfcgames = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'tfc'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($tfcgames, $db->escape($rowdata[0]));
	}
	
	$gamestring="";
	
	foreach($tfcgames as $game)
	{
		$gamestring.="'$game',";
	}
	
	if ($gamestring)
	{
		$gamestring = '('.preg_replace('/,$/', '', $gamestring).')';
		$db->query("UPDATE `hlstats_Actions` SET `team` = '' WHERE `team` IN ('0','1','2') AND `game` IN $gamestring");
		$db->query("UPDATE `hlstats_Actions` SET `for_PlayerPlayerActions` = '0', `for_PlayerActions`='1' WHERE `code` = 'Medic_Cured_Infection' AND `game` IN $gamestring");
	}
	
	$db->query("UPDATE hlstats_Options SET `value` = '41' WHERE `keyname` = 'dbversion'");
?>
