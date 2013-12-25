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
			('$game', 'escort_score', 1, 0, '', 'Cart Escort', '1', '0', '0', '0');
		");
		$db->query("
			INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
				('$game','demoshield', 'Chargin'' Targe', 2.0);
		");		
		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('W','$game','demoshield', 'Got something for ya!', 'kills with the Chargin'' Targe'),
				('O','$game','builtobject_obj_dispenser', 'Dispenser Here!', 'Dispensers built'),
				('O','$game','escort_score', 'Forward glorious cart!', 'Cart Escorts'),
				('O','$game','killed_charged_medic', 'Juice Loosener', 'Charged Medics killed'),
				('O','$game','steal_sandvich', 'Mmm, Ham', 'stolen Sandviches'),
				('O','$game','teleport', 'One to beam up!', 'players Teleported'),
				('W','$game','deflect_arrow', 'Deflected Arrow', 'kills with a deflected arrow'),
				('W','$game','tf_projectile_arrow_fire', 'Fire Flight', 'kills with the Flaming Huntsman');
		");
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
                INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `game`, `image`, `ribbonName`, `awardCount`) VALUES
					('ambassador', '$game', '{$h}_ambassador.png', '$level Ambassador', '$awardCount'),
					('buff_deployed', '$game', '{$h}_buff_deployed.png', '$level Buff Deploy', '$awardCount'),
					('builtobject_obj_dispenser', '$game', '{$h}_builtobject_obj_dispenser.png', '$level Built Dispenser', '$awardCount'),
					('builtobject_obj_sentrygun', '$game', '{$h}_builtobject_obj_sentrygun.png', '$level Built Sentry Gun', '$awardCount'),
					('defended_medic', '$game', '{$h}_defended_medic.png', '$level Defended Medic', '$awardCount'),
					('demoshield', '$game', '{$h}_demoshield.png', '$level Chargin'' Targe', '$awardCount'),
					('escort_score', '$game', '{$h}_escort_score.png', '$level Cart Escort', '$awardCount'),
					('jarate', '$game', '{$h}_jarate.png', '$level Jarate', '$awardCount'),
					('killed_charged_medic', '$game', '{$h}_killed_charged_medic.png', '$level Charged Medic Kills', '$awardCount'),
					('latency', '$game', '{$h}_latency.png', '$level Latency', '$awardCount'),
					('pickaxe', '$game', '{$h}_pickaxe.png', '$level Equalizer', '$awardCount'),
					('rocketlauncher_directhit', '$game', '{$h}_rocketlauncher_directhit.png', '$level Direct Hit', '$awardCount'),
					('sandman', '$game', '{$h}_sandman.png', '$level Sandman', '$awardCount'),
					('shield_blocked', '$game', '{$h}_shield_blocked.png', '$level Broken Razorbacks', '$awardCount'),					
					('steal_sandvich', '$game', '{$h}_steal_sandvich.png', '$level Steal Sandvich', '$awardCount'),
                    ('sticky_resistance', '$game', '{$h}_sticky_resistance.png', '$level Scottish Resistance', '$awardCount'),
					('stun', '$game', '{$h}_stun.png', '$level Stun', '$awardCount'),
                    ('sword', '$game', '{$h}_sword.png', '$level Eyelander', '$awardCount'),
                    ('taunt_demoman', '$game', '{$h}_taunt_demoman.png', '$level Demoman Taunt', '$awardCount'),
					('taunt_scout', '$game', '{$h}_taunt_scout.png', '$level Scout Taunt', '$awardCount'),
					('taunt_sniper', '$game', '{$h}_taunt_sniper.png', '$level Sniper Taunt', '$awardCount'),
                    ('taunt_soldier', '$game', '{$h}_taunt_soldier.png', '$level Soldier Taunt', '$awardCount'),
					('taunt_spy', '$game', '{$h}_taunt_spy.png', '$level Spy Taunt', '$awardCount'),
					('teleport', '$game', '{$h}_teleport.png', '$level Teleport', '$awardCount'),
					('tf_projectile_arrow', '$game', '{$h}_tf_projectile_arrow.png', '$level Huntsman', '$awardCount'),
					('tf_projectile_arrow_fire', '$game', '{$h}_tf_projectile_arrow_fire.png', '$level Flaming Huntsman', '$awardCount');
            ");        
        }
		
	}	

	$db->query("
		UPDATE hlstats_Options SET `value` = '1.6.4' WHERE `keyname` = 'version'
	");
	
	$db->query("
		UPDATE hlstats_Options SET `value` = '23' WHERE `keyname` = 'dbversion'
	");
	
	
?>