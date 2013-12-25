<?php

	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$ntsgames = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'nts'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($ntsgames, $db->escape($rowdata[0]));
	}
	
	$gamestring="";
	
	foreach($ntsgames as $game)
	{		
		$db->query("DELETE FROM hlstats_Awards WHERE game='$game' AND awardType='W' AND code='smac'");
		
		$db->query("UPDATE hlstats_Awards SET code='m41s', name='M41S', verb='kills with M41S' WHERE game='$game' AND awardType='W' AND code='m41l'");
		
		$db->query("UPDATE hlstats_Ribbons SET awardCode='m41s', image='1_m41s.png', ribbonName='Bronze M41S' WHERE game='$game' AND awardCode='m41l' AND image='1_m41l.png'");
		$db->query("UPDATE hlstats_Ribbons SET awardCode='m41s', image='2_m41s.png', ribbonName='Silver M41S' WHERE game='$game' AND awardCode='m41l' AND image='2_m41l.png'");
		$db->query("UPDATE hlstats_Ribbons SET awardCode='m41s', image='3_m41s.png', ribbonName='Gold M41S' WHERE game='$game' AND awardCode='m41l' AND image='3_m41l.png'");
		
		$db->query("UPDATE hlstats_Weapons SET code='m41s', name='M41S' WHERE game='$game' AND code='m41l'");
		
		$gamestring.="'$game',";
	}
	
	if ($gamestring)
	{
		$gamestring = '('.preg_replace('/,$/', '', $gamestring).')';
		$serveridstring = "";
	
		$ntsservers = array();
		$result = $db->query("SELECT serverId FROM hlstats_Servers WHERE game IN $gamestring");
		while ($rowdata = $db->fetch_row($result))
		{ 
			array_push($ntsservers, $db->escape($rowdata[0]));
		}
		
		foreach($ntsservers as $server)
		{
			$serveridstring.="$server,";
		}
		if ($serveridstring)
		{
			$serveridstring = '('.preg_replace('/,$/', '', $serveridstring).')';
			$db->query("UPDATE hlstats_Events_Frags SET weapon='m41s' WHERE serverId IN $serveridstring AND weapon='m41l'");
			$db->query("UPDATE hlstats_Events_Statsme SET weapon='m41s' WHERE serverId IN $serveridstring AND weapon='m41l'");
			$db->query("UPDATE hlstats_Events_Statsme2 SET weapon='m41s' WHERE serverId IN $serveridstring AND weapon='m41l'");
			$db->query("UPDATE hlstats_Events_Suicides SET weapon='m41s' WHERE serverId IN $serveridstring AND weapon='m41l'");
			$db->query("UPDATE hlstats_Events_Teamkills SET weapon='m41s' WHERE serverId IN $serveridstring AND weapon='m41l'");
		}
	}

	$db->query("UPDATE hlstats_Options SET `value` = '35' WHERE `keyname` = 'dbversion'");	
?>