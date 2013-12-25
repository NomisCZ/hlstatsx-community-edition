<?php
	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$dbversion = 50;
	$version = "1.6.11-dev";

	$db->query("
		INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES
			('valve', 'kill_streak_2', 1, 0, '', 'Double Kill (2 kills)', '1', '', '', ''),
			('valve', 'kill_streak_3', 2, 0, '', 'Triple Kill (3 kills)', '1', '', '', ''),
			('valve', 'kill_streak_4', 3, 0, '', 'Domination (4 kills)', '1', '', '', ''),
			('valve', 'kill_streak_5', 4, 0, '', 'Rampage (5 kills)', '1', '', '', ''),
			('valve', 'kill_streak_6', 5, 0, '', 'Mega Kill (6 kills)', '1', '', '', ''),
			('valve', 'kill_streak_7', 6, 0, '', 'Ownage (7 kills)', '1', '', '', ''),
			('valve', 'kill_streak_8', 7, 0, '', 'Ultra Kill (8 kills)', '1', '', '', ''),
			('valve', 'kill_streak_9', 8, 0, '', 'Killing Spree (9 kills)', '1', '', '', ''),
			('valve', 'kill_streak_10', 9, 0, '', 'Monster Kill (10 kills)', '1', '', '', ''),
			('valve', 'kill_streak_11', 10, 0, '', 'Unstoppable (11 kills)', '1', '', '', ''),
			('valve', 'kill_streak_12', 11, 0, '', 'God Like (12+ kills)', '1', '', '', ''),
			('valve', 'headshot', 1, 0, '', 'Headshot Kill', '1', '0', '0', '0');
		");
		
	$db->query("
		INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
			('W', 'valve', '357', '357', 'kills with 357'),
			('W', 'valve', '9mmAR', 'MP5', 'kills with 9mmAR'),
			('W', 'valve', '9mmhandgun', 'Glock', 'kills with 9mmhandgun'),
			('W', 'valve', 'crossbow', 'Crossbow Sniper', 'kills with crossbow'),
			('W', 'valve', 'crowbar', 'Crowbar Maniac', 'murders with crowbar'),
			('W', 'valve', 'gluon gun', 'Gauss King', 'kills with gluon gun'),
			('W', 'valve', 'tau_cannon', 'Egon', 'kills with tau_cannon'),
			('W', 'valve', 'grenade', 'Grenadier', 'kills with grenade'),
			('W', 'valve', 'hornet', 'Hornet Master', 'kills with hornet'),
			('W', 'valve', 'rpg_rocket', 'Rocketeer', 'kills with rocket'),
			('W', 'valve', 'satchel', 'Lord Satchel', 'kills with satchel'),
			('W', 'valve', 'shotgun', 'Redneck', 'kills with shotgun'),
			('W', 'valve', 'snark', 'Snark Master', 'kills with snark'),
			('W', 'valve', 'tripmine', 'Shady Assassin', 'kills with tripmine'),
			('O', 'valve', 'headshot', 'Headshot King', 'shots in the head'),
			('W', 'valve', 'latency', 'Latency', 'ms average connection'),
			('W', 'valve', 'mostkills', 'Most Kills', 'kills'),
			('W', 'valve', 'suicide', 'Suicides', 'suicides');
		");
	
	$db->query("
		INSERT IGNORE INTO `hlstats_Games` (`code`, `name`, `realgame`, `hidden`) VALUES 
			('valve', 'Half-Life 1 Multiplayer', 'valve', '1');
	");	
		
	$db->query("
		INSERT IGNORE INTO `hlstats_Games_Defaults` (`code`, `parameter`, `value`) VALUES
			('valve', 'Admins', ''),
			('valve', 'AutoBanRetry', '0'),
			('valve', 'AutoTeamBalance', '0'),
			('valve', 'BonusRoundIgnore', '0'),
			('valve', 'BonusRoundTime', '0'),
			('valve', 'BroadCastEvents', '1'),
			('valve', 'BroadCastPlayerActions', '1'),
			('valve', 'ConnectAnnounce', '1'),
			('valve', 'DefaultDisplayEvents', '1'),
			('valve', 'DisplayResultsInBrowser', '1'),
			('valve', 'EnablePublicCommands', '1'),
			('valve', 'GameEngine', '1'),
			('valve', 'GameType', '0'),
			('valve', 'HLStatsURL', 'http://yoursite.com/hlstats'),
			('valve', 'IgnoreBots', '1'),
			('valve', 'MinimumPlayersRank', '0'),
			('valve', 'MinPlayers', '4'),
			('valve', 'PlayerEvents', '1'),
			('valve', 'ShowStats', '1'),
			('valve', 'SkillMode', '0'),
			('valve', 'SuicidePenalty', '5'),
			('valve', 'SwitchAdmins', '0'),
			('valve', 'TKPenalty', '25'),
			('valve', 'TrackServerLoad', '1'),
			('valve', 'UpdateHostname', '1');	
	");	

	$db->query("
		INSERT IGNORE INTO `hlstats_Games_Supported` (`code`, `name`) VALUES
			('valve', 'Half-Life 1 Multiplayer');
	");	

	$db->query("
		INSERT IGNORE INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`) VALUES
			('valve', 'recruit', '0', '49', 'Recruit'),
			('valve', 'private', '50', '99', 'Private'),
			('valve', 'private-first-class', '100', '199', 'Private First Class'),
			('valve', 'lance-corporal', '200', '299', 'Lance Corporal'),
			('valve', 'corporal', '300', '399', 'Corporal'),
			('valve', 'sergeant', '400', '499', 'Sergeant'),
			('valve', 'staff-sergeant', '500', '599', 'Staff Sergeant'),
			('valve', 'gunnery-sergeant', '600', '699', 'Gunnery Sergeant'),
			('valve', 'master-sergeant', '700', '799', 'Master Sergeant'),
			('valve', 'first-sergeant', '800', '899', 'First Sergeant'),
			('valve', 'master-chief', '900', '999', 'Master Chief'),
			('valve', 'sergeant-major', '1000', '1199', 'Sergeant Major'),
			('valve', 'ensign', '1200', '1399', 'Ensign'),
			('valve', 'third-lieutenant', '1400', '1599', 'Third Lieutenant'),
			('valve', 'second-lieutenant', '1600', '1799', 'Second Lieutenant'),
			('valve', 'first-lieutenant', '1800', '1999', 'First Lieutenant'),
			('valve', 'captain', '2000', '2249', 'Captain'),
			('valve', 'group-captain', '2250', '2499', 'Group Captain'),
			('valve', 'senior-captain', '2500', '2749', 'Senior Captain'),
			('valve', 'lieutenant-major', '2750', '2999', 'Lieutenant Major'),
			('valve', 'major', '3000', '3499', 'Major'),
			('valve', 'group-major', '3500', '3999', 'Group Major'),
			('valve', 'lieutenant-commander', '4000', '4499', 'Lieutenant Commander'),
			('valve', 'commander', '4500', '4999', 'Commander'),
			('valve', 'group-commander', '5000', '5749', 'Group Commander'),
			('valve', 'lieutenant-colonel', '5750', '6499', 'Lieutenant Colonel'),
			('valve', 'colonel', '6500', '7249', 'Colonel'),
			('valve', 'brigadier', '7250', '7999', 'Brigadier'),
			('valve', 'brigadier-general', '8000', '8999', 'Brigadier General'),
			('valve', 'major-general', '9000', '9999', 'Major General'),
			('valve', 'lieutenant-general', '10000', '12499', 'Lieutenant General'),
			('valve', 'general', '12500', '14999', 'General'),
			('valve', 'commander-general', '15000', '17499', 'Commander General'),
			('valve', 'field-vice-marshal', '17500', '19999', 'Field Vice Marshal'),
			('valve', 'field-marshal', '20000', '22499', 'Field Marshal'),
			('valve', 'vice-commander-of-the-army', '22500', '24999', 'Vice Commander of the Army'),
			('valve', 'commander-of-the-army', '25000', '27499', 'Commander of the Army'),
			('valve', 'high-commander', '27500', '29999', 'High Commander'),
			('valve', 'supreme-commander', '30000', '34999', 'Supreme Commander'),
			('valve', 'terminator', '35000', '9999999', 'Terminator');
	");
	
	$db->query("
		INSERT IGNORE INTO `hlstats_Teams` (`game`, `code`, `name`, `hidden`, `playerlist_bgcolor`, `playerlist_color`, `playerlist_index`) VALUES
			('valve', 'scientist', 'Team robo', '0', '#D2E8F7', '#0080C0',1),
			('valve', 'hgrunt', 'Team hgrunt', '0', '#FFD5D5', '#FF2D2D',2);	
	");	
	
	$db->query("
		INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
			('valve', '357', '357 Revolver', '1.60'),
			('valve', '9mmAR', '9mm Automatic Rifle', '1.00'),
			('valve', '9mmhandgun', '9mm Handgun', '1.50'),
			('valve', 'bolt', 'Crossbow Bolt', '1.70'),
			('valve', 'crossbow', 'Crossbow', '1.40'),
			('valve', 'crowbar', 'Crowbar', '1.90'),
			('valve', 'tau_cannon', 'Egon Tau Cannon / Rail Gun', '1.00'),
			('valve', 'gluon gun', 'Gluon / Gauss Gun', '1.00'),
			('valve', 'grenade', 'Grenade', '1.00'),
			('valve', 'hornet', 'Hornet', '1.30'),
			('valve', 'rpg_rocket', 'Rocket Propelled Grenade', '1.00'),
			('valve', 'satchel', 'Satchel Charge', '1.50'),
			('valve', 'shotgun', 'Shotgun', '1.20'),
			('valve', 'snark', 'Snark', '1.80'),
			('valve', 'tripmine', 'Trip Mine', '1.60');	
	");	

	$db->query("UPDATE hlstats_Options SET `value` = '$version' WHERE `keyname` = 'version'");
	$db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>
