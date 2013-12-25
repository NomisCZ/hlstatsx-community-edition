<?php

	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$tf2games = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'tf'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($tf2games, $db->escape($rowdata[0]));
	}
	
	foreach($tf2games as $game)
	{		
		$db->query("
			INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES
				('$game', 'air2airshot_pipebomb', 0, 0, '', 'Airshot Pipebomb (Airborne)', '1', '0', '0', '0'),
				('$game', 'air2airshot_rocket', 0, 0, '', 'Airshot Rocket (Airborne)', '1', '0', '0', '0'),
				('$game', 'air2airshot_sticky', 0, 0, '', 'Airshot Sticky (Airborne)', '1', '0', '0', '0'),
				('$game', 'airblast_player', 0, 0, '', 'Airblast Player', '0', '1', '0', '0'),
				('$game', 'airshot_arrow', 0, 0, '', 'Airshot Arrow', '1', '0', '0', '0'),
				('$game', 'airshot_flare', 0, 0, '', 'Airshot Flare', '1', '0', '0', '0'),
				('$game', 'airshot_pipebomb', 0, 0, '', 'Airshot Pipebomb', '1', '0', '0', '0'),
				('$game', 'airshot_sticky', 0, 0, '', 'Airshot Sticky', '1', '0', '0', '0'),
				('$game', 'airshot_stun', 0, 0, '', 'Airshot Stun', '1', '0', '0', '0'),
				('$game', 'deflected_arrow', 0, 0, '', 'Deflected Arrow', '0', '1', '0', '0'),
				('$game', 'deflected_baseball', 0, 0, '', 'Deflected Baseball', '0', '1', '0', '0'),
				('$game', 'deflected_flare', 0, 0, '', 'Deflected Flare', '0', '1', '0', '0'),
				('$game', 'deflected_jarate', 0, 0, '', 'Deflected Jarate', '0', '1', '0', '0'),
				('$game', 'deflected_pipebomb', 0, 0, '', 'Deflected Pipebomb', '0', '1', '0', '0'),
				('$game', 'deflected_rocket', 0, 0, '', 'Deflected Rocket', '0', '1', '0', '0'),
				('$game', 'deflected_rocket_dh', 0, 0, '', 'Deflected Directhit Rocket', '0', '1', '0', '0'),
				('$game', 'rocket_failjump', 0, 0, '', 'Rocket Jump Failure', '1', '0', '0', '0'),
				('$game', 'rocket_jump', 0, 0, '', 'Rocket Jump', '1', '0', '0', '0'),
				('$game', 'rocket_jump_kill', 0, 0, '', 'Rocket Jump Kill', '1', '0', '0', '0'),
				('$game', 'rocket_jumper_kill', 0, 0, '', 'Rocket Jumper Kill', '1', '0', '0', '0'),
				('$game', 'sandvich_healself', 0, 0, '', 'Ate Sandvich for Health', '1', '0', '0', '0'),
				('$game', 'sticky_failjump', 0, 0, '', 'Sticky Jump Failure', '1', '0', '0', '0'),
				('$game', 'sticky_jump', 0, 0, '', 'Sticky Jump', '1', '0', '0', '0'),
				('$game', 'sticky_jump_kill', 0, 0, '', 'Sticky Jump Kill', '1', '0', '0', '0'),
				('$game', 'sticky_jumper_kill', 0, 0, '', 'Sticky Jumper Kill', '1', '0', '0', '0'),
				('$game', 'teleport_again', 0, 0, '', 'Teleported Again (Past 10 Seconds)', '1', '0', '0', '0'),
				('$game', 'teleport_self_again', 0, 0, '', 'Teleported Self Again (Past 10 Seconds)', '1', '0', '0', '0'),
				('$game', 'teleport_used', 0, 0, '', 'Teleporter Used (Not Own)', '1', '0', '0', '0'),
				('$game', 'teleport_used_again', 0, 0, '', 'Teleporter Used Again (Past 10 Seconds) (Not Own)', '1', '0', '0', '0');
		");
		
		$db->query("UPDATE `hlstats_Actions` SET `for_PlayerActions` = '1', `for_PlayerPlayerActions` = '0', `for_TeamActions` = '0', `for_WorldActions` = '0' WHERE `code` = 'airshot_rocket' AND `game` = '$game';");
		$db->query("UPDATE `hlstats_Actions` SET `for_PlayerActions` = '1', `for_PlayerPlayerActions` = '0', `for_TeamActions` = '0', `for_WorldActions` = '0' WHERE `code` = 'airshot_headshot' AND `game` = '$game';");
	}

	$db->query("UPDATE hlstats_Options SET `value` = '34' WHERE `keyname` = 'dbversion'");	
?>