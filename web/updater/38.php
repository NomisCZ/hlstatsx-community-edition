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
	
	foreach($ntsgames as $game)
	{
		$db->query("UPDATE hlstats_Weapons SET `name` = 'Detpack' WHERE game='$game' AND `code` = 'grenade_detapack'");
		$db->query("UPDATE hlstats_Awards SET `name` = 'Detpack', `verb`='kills with Detpack' WHERE game='$game' AND awardType='W' AND `code` = 'grenade_detapack'");
		$db->query("UPDATE hlstats_Ribbons SET `awardCode` = 'supa7' WHERE game='$game' AND `image` LIKE '%supa7%'");
		
		for ($h = 1; $h<4; $h++) {
			switch ($h) {
			case 1:
				$level = "Bronze";
				$awardCount = 1;
			break;

			case 2:
				$level = "Silver";
				$awardCount = 5;
			break;

			case 3:
				$level = "Gold";
				$awardCount = 10;
			break;
			}

			$db->query(" 
				INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES
					('grenade_detapack', $awardCount, 0, '$game', '{$h}_rdp.png', '$level Detpack'),
					('aa13', $awardCount, 0, '$game', '{$h}_aa13.png', '$level AA13'),
					('pz', $awardCount, 0, '$game', '{$h}_pz252.png', '$level PZ252'),
					('srm', $awardCount, 0, '$game', '{$h}_srm7.png', '$level SRM'),
					('srm_s', $awardCount, 0, '$game', '{$h}_srms7.png', '$level SRM-S'),
					('srs', $awardCount, 0, '$game', '{$h}_srs.png', '$level SRS'),
					('jitte', $awardCount, 0, '$game', '{$h}_np-721.png', '$level Jitte'),
					('jittescoped', $awardCount, 0, '$game', '{$h}_np-721s.png', '$level Jitte (Scoped)');
			");
		}
	}
	
	$db->query("UPDATE hlstats_Options SET `value` = '38' WHERE `keyname` = 'dbversion'");	
?>
