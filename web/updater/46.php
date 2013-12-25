<?php
	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}
	
	$dbversion = 46;

	$db->query("
		INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES
			('pvkii', 'kill_streak_2', 1, 0, '', 'Double Kill (2 kills)', '1', '', '', ''),
			('pvkii', 'kill_streak_3', 2, 0, '', 'Triple Kill (3 kills)', '1', '', '', ''),
			('pvkii', 'kill_streak_4', 3, 0, '', 'Domination (4 kills)', '1', '', '', ''),
			('pvkii', 'kill_streak_5', 4, 0, '', 'Rampage (5 kills)', '1', '', '', ''),
			('pvkii', 'kill_streak_6', 5, 0, '', 'Mega Kill (6 kills)', '1', '', '', ''),
			('pvkii', 'kill_streak_7', 6, 0, '', 'Ownage (7 kills)', '1', '', '', ''),
			('pvkii', 'kill_streak_8', 7, 0, '', 'Ultra Kill (8 kills)', '1', '', '', ''),
			('pvkii', 'kill_streak_9', 8, 0, '', 'Killing Spree (9 kills)', '1', '', '', ''),
			('pvkii', 'kill_streak_10', 9, 0, '', 'Monster Kill (10 kills)', '1', '', '', ''),
			('pvkii', 'kill_streak_11', 10, 0, '', 'Unstoppable (11 kills)', '1', '', '', ''),
			('pvkii', 'kill_streak_12', 11, 0, '', 'God Like (12+ kills)', '1', '', '', ''),
			('pvkii', 'kill assist', 2, 0, '', 'Kill Assist', '1', '0', '0', '0'),
			('pvkii', 'mvp1', 5, 0, '', 'Most Valuable Player #1', '1', '0', '0', '0'),
			('pvkii', 'mvp2', 0, 0, '', 'Most Valuable Player #2', '1', '0', '0', '0'),
			('pvkii', 'mvp3', 0, 0, '', 'Most Valuable Player #3', '1', '0', '0', '0'),
			('pvkii', 'chest_capture', 3, 0, '', 'Chest Capture', '1', '0', '0', '0'),
			('pvkii', 'chest_defend', 2, 0, '', 'Defended Chest', '1', '0', '0', '0'),
			('pvkii', 'obj_complete', 3, 0, '', 'Complted Objective', '1', '0', '0', '0'),
			('pvkii', 'grail_defend', 2, 0, '', 'Defended Grail', '1', '0', '0', '0'),
			('pvkii', 'killed_parrot', 1, 0, '', 'Killed Parrot', '1', '0', '0', '0'),
			('pvkii', 'domination', 5, 0, '', 'Domination', '0', '1', '0', '0'),
			('pvkii', 'revenge', 3, 0, '', 'Revenge', '0', '1', '0', '0');
	");
		
	$db->query("
		INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
			('W', 'pvkii', 'latency', 'Best Latency', 'ms average connection'),
			('W', 'pvkii', 'mostkills', 'Most Kills', 'kills'),
			('W', 'pvkii', 'teamkills', 'Most Team Kills', 'team kills'),
			('W', 'pvkii', 'bonuspoints', 'Most bonus points', 'bonus points'),
			('W', 'pvkii', 'latency', 'Best Latency', 'ms average connection'),
			('W', 'pvkii', 'zerking', 'Zerking', 'kills with Zerking'),
			('W', 'pvkii', 'slidingdoor', 'Sliding Door', 'kills with Sliding Door'),
			('W', 'pvkii', 'seaxshield', 'Langseax & Shield', 'kills with Langseax & Shield'),
			('W', 'pvkii', 'longbow', 'Long Bow', 'kills with Long Bow'),
			('W', 'pvkii', 'chest', 'Chest', 'kills with Chest'),
			('W', 'pvkii', 'parrot', 'Parrot', 'kills with Parrot'),
			('W', 'pvkii', 'rocks', 'Rocks', 'kills with Rocks'),
			('W', 'pvkii', 'bigaxe', 'Berserker Axe', 'kills with Berserker Axe'),
			('W', 'pvkii', 'gatecrush', 'Gate Crush', 'kills with Gate Crush'),
			('W', 'pvkii', 'cutlass', 'Cutlass', 'kills with Cutlass'),
			('W', 'pvkii', 'twoaxe', 'Huscarl Axe', 'kills with Huscarl Axe'),
			('W', 'pvkii', 'spear', 'Spear', 'kills with Spear'),
			('W', 'pvkii', 'freeze', 'Freeze', 'kills with Freeze'),
			('W', 'pvkii', 'plfall', 'Falling', 'kills with Falling'),
			('W', 'pvkii', 'steam', 'Steam', 'kills with Steam'),
			('W', 'pvkii', 'vulture', 'Vultures', 'kills with Vultures'),
			('W', 'pvkii', 'shuriken', 'shuriken', 'kills with shuriken'),
			('W', 'pvkii', 'huscshieldbash', 'Huscarl Shield Bash', 'kills with Huscarl Shield Bash'),
			('W', 'pvkii', 'spike', 'Spike', 'kills with Spike'),
			('W', 'pvkii', 'blunderbuss', 'Blunderbuss', 'kills with Blunderbuss'),
			('W', 'pvkii', 'punch_cpt', 'Punch', 'kills with Punch'),
			('W', 'pvkii', 'crossbow', 'Crossbow', 'kills with Crossbow'),
			('W', 'pvkii', 'env_explosion', 'Explosion', 'kills with Explosion '),
			('W', 'pvkii', 'flintlock', 'Flintlock Pistols', 'kills with Flintlock Pistols'),
			('W', 'pvkii', 'throwaxe', 'Throwing Axes', 'kills with Throwing Axes'),
			('W', 'pvkii', 'powderkeg', 'Powderkeg', 'kills with Powderkeg'),
			('W', 'pvkii', 'physics', 'Physics', 'kills with Physics'),
			('W', 'pvkii', 'boulder', 'Boulder', 'kills with Boulder'),
			('W', 'pvkii', 'drowned', 'Drowned', 'kills with Drowned'),
			('W', 'pvkii', 'barrel', 'Barrel', 'kills with Barrel'),
			('W', 'pvkii', 'axesword', 'Sword & Axe', 'kills with Sword & Axe'),
			('W', 'pvkii', 'cutlass2', 'Captain''s Cutlass', 'kills with Captain''s Cutlass'),
			('W', 'pvkii', 'flames', 'Flames', 'kills with Flames'),
			('W', 'pvkii', 'vikingshield', 'Huscarl Sword & Shield', 'kills with Huscarl Sword & Shield '),
			('W', 'pvkii', 'thrownkeg', 'Thrown Powderkeg', 'kills with Thrown Powderkeg '),
			('W', 'pvkii', 'archersword', 'Short Sword', 'kills with Short Sword'),
			('W', 'pvkii', 'player', 'Player', 'kills with Player'),
			('W', 'pvkii', 'worldspawn', 'World', 'kills with World'),
			('W', 'pvkii', 'twosword', '2 Handed Sword', 'kills with 2 Handed Sword'),
			('W', 'pvkii', 'hook', 'Hook', 'kills with Hook'),
			('W', 'pvkii', 'hkshieldbash', 'Heavy Knight Bash', 'kills with Heavy Knight Bash'),
			('W', 'pvkii', 'javelin', 'Javelins', 'kills with Javelins'),
			('W', 'pvkii', 'swordshield', 'Heavy Knight Sword/Shield', 'kills with Heavy Knight Sword/Shield'),
			('W', 'pvkii', 'crusher', 'Crusher', 'kills with Crusher'),
			('O', 'pvkii', 'kill assist', 'Kill Assists', 'kill assists'),
			('O', 'pvkii', 'mvp1', 'Most Valuable Player', 'times MVP'),
			('O', 'pvkii', 'chest_capture', 'Chest Captures', 'chest captures'),
			('O', 'pvkii', 'obj_complete', 'Completed Objectives', 'completed objectives'),
			('O', 'pvkii', 'chest_defend', 'Chest Defends', 'chests defends'),
			('O', 'pvkii', 'grail_defend', 'Grail Defends', 'grail defends'),
			('O', 'pvkii', 'killed_parrot', 'Parrot Killer', 'parrots killed'),
			('P', 'pvkii', 'domination', 'Dominations', 'dominations'),
			('P', 'pvkii', 'revenge', 'Revenge', 'revenges');
	");
	
	$db->query("
		INSERT IGNORE INTO `hlstats_Games` (`code`, `name`, `realgame`, `hidden`) VALUES
			('pvkii', 'Pirates, Vikings, & Knights II', 'pvkii', '1')
	");
	
	$db->query("
		INSERT IGNORE INTO `hlstats_Games_Defaults` (`code`, `parameter`, `value`) VALUES
			('pvkii', 'Admins', ''),
			('pvkii', 'AutoBanRetry', '0'),
			('pvkii', 'AutoTeamBalance', '0'),
			('pvkii', 'BonusRoundIgnore', '0'),
			('pvkii', 'BonusRoundTime', '0'),
			('pvkii', 'BroadCastEvents', '1'),
			('pvkii', 'BroadCastPlayerActions', '1'),
			('pvkii', 'ConnectAnnounce', '1'),
			('pvkii', 'DefaultDisplayEvents', '1'),
			('pvkii', 'DisplayResultsInBrowser', '1'),
			('pvkii', 'EnablePublicCommands', '1'),
			('pvkii', 'GameEngine', '3'),
			('pvkii', 'GameType', '0'),
			('pvkii', 'HLStatsURL', 'http://yoursite.com/hlstats'),
			('pvkii', 'IgnoreBots', '1'),
			('pvkii', 'MinimumPlayersRank', '0'),
			('pvkii', 'MinPlayers', '4'),
			('pvkii', 'PlayerEvents', '1'),
			('pvkii', 'ShowStats', '1'),
			('pvkii', 'SkillMode', '0'),
			('pvkii', 'SuicidePenalty', '5'),
			('pvkii', 'SwitchAdmins', '0'),
			('pvkii', 'TKPenalty', '25'),
			('pvkii', 'TrackServerLoad', '1'),
			('pvkii', 'UpdateHostname', '1');
	");
	
	$db->query("
		INSERT IGNORE INTO `hlstats_Games_Supported` (`code`, `name`) VALUES
			('pvkii', 'Pirates, Vikings, & Knights II')
	");
	
	$db->query("
		INSERT IGNORE INTO `hlstats_Roles` (`game`, `code`, `name`, `hidden`) VALUES
			('pvkii', 'Berserker', 'Berserker', '0'),
			('pvkii', 'Gestir', 'Gestir', '0'),
			('pvkii', 'Huscarl', 'Huscarl', '0'),
			('pvkii', 'Captain', 'Captain', '0'),
			('pvkii', 'Skirmisher', 'Skirmisher', '0'),
			('pvkii', 'Archer', 'Archer', '0'),
			('pvkii', 'Heavy Knight', 'Heavy Knight', '0');
	");
	
	$db->query("
		INSERT IGNORE INTO `hlstats_Teams` (`game`, `code`, `name`, `hidden`, `playerlist_bgcolor`, `playerlist_color`, `playerlist_index`) VALUES
			('pvkii', 'Pirates', 'Pirates', '0', '#FFD5D5', '#FF2D2D', 1),
			('pvkii', 'Vikings', 'Vikings', '0', '#93FF89', '#4B8246', 2),
			('pvkii', 'Knights', 'Knights', '0', '#D2E8F7', '#0080C0', 3);
	");
	
	$db->query("
		INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
			('pvkii', 'zerking', 'Zerking', 1.00),
			('pvkii', 'slidingdoor', 'Sliding Door', 1.00),
			('pvkii', 'seaxshield', 'Langseax & Shield', 1.00),
			('pvkii', 'longbow', 'Long Bow', 1.00),
			('pvkii', 'chest', 'Chest', 1.00),
			('pvkii', 'parrot', 'Parrot', 1.00),
			('pvkii', 'rocks', 'Rocks', 1.00),
			('pvkii', 'bigaxe', 'Berserker Axe', 1.00),
			('pvkii', 'gatecrush', 'Gate Crush', 1.00),
			('pvkii', 'cutlass', 'Cutlass', 1.00),
			('pvkii', 'twoaxe', 'Huscarl Axe', 1.00),
			('pvkii', 'spear', 'Spear', 1.00),
			('pvkii', 'freeze', 'Freeze', 1.00),
			('pvkii', 'plfall', 'Falling', 1.00),
			('pvkii', 'steam', 'Steam', 1.00),
			('pvkii', 'vulture', 'Vultures', 1.00),
			('pvkii', 'shuriken', 'shuriken', 1.00),
			('pvkii', 'huscshieldbash', 'Huscarl Shield Bash', 1.00),
			('pvkii', 'spike', 'Spike', 1.00),
			('pvkii', 'blunderbuss', 'Blunderbuss', 1.00),
			('pvkii', 'punch_cpt', 'Punch', 1.00),
			('pvkii', 'crossbow', 'Crossbow', 1.00),
			('pvkii', 'env_explosion', 'Explosion', 1.00),
			('pvkii', 'flintlock', 'Flintlock Pistols', 1.00),
			('pvkii', 'throwaxe', 'Throwing Axes', 1.00),
			('pvkii', 'powderkeg', 'Powderkeg', 1.00),
			('pvkii', 'physics', 'Physics', 1.00),
			('pvkii', 'boulder', 'Boulder', 1.00),
			('pvkii', 'drowned', 'Drowned', 1.00),
			('pvkii', 'barrel', 'Barrel', 1.00),
			('pvkii', 'axesword', 'Sword & Axe', 1.00),
			('pvkii', 'cutlass2', 'Captain''s Cutlass', 1.00),
			('pvkii', 'flames', 'Flames', 1.00),
			('pvkii', 'vikingshield', 'Huscarl Sword & Shield', 1.00),
			('pvkii', 'thrownkeg', 'Thrown Powderkeg', 1.00),
			('pvkii', 'archersword', 'Short Sword', 1.00),
			('pvkii', 'player', 'Player', 1.00),
			('pvkii', 'worldspawn', 'World', 1.00),
			('pvkii', 'twosword', '2 Handed Sword', 1.00),
			('pvkii', 'hook', 'Hook', 1.00),
			('pvkii', 'hkshieldbash', 'Heavy Knight Bash', 1.00),
			('pvkii', 'javelin', 'Javelins', 1.00),
			('pvkii', 'swordshield', 'Heavy Knight Sword/Shield', 1.00),
			('pvkii', 'crusher', 'Crusher', 1.00);
	");

	$db->query("
		INSERT IGNORE INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`) VALUES
			('pvkii', 'recruit', '0', '49', 'Recruit'),
			('pvkii', 'private', '50', '99', 'Private'),
			('pvkii', 'private-first-class', '100', '199', 'Private First Class'),
			('pvkii', 'lance-corporal', '200', '299', 'Lance Corporal'),
			('pvkii', 'corporal', '300', '399', 'Corporal'),
			('pvkii', 'sergeant', '400', '499', 'Sergeant'),
			('pvkii', 'staff-sergeant', '500', '599', 'Staff Sergeant'),
			('pvkii', 'gunnery-sergeant', '600', '699', 'Gunnery Sergeant'),
			('pvkii', 'master-sergeant', '700', '799', 'Master Sergeant'),
			('pvkii', 'first-sergeant', '800', '899', 'First Sergeant'),
			('pvkii', 'master-chief', '900', '999', 'Master Chief'),
			('pvkii', 'sergeant-major', '1000', '1199', 'Sergeant Major'),
			('pvkii', 'ensign', '1200', '1399', 'Ensign'),
			('pvkii', 'third-lieutenant', '1400', '1599', 'Third Lieutenant'),
			('pvkii', 'second-lieutenant', '1600', '1799', 'Second Lieutenant'),
			('pvkii', 'first-lieutenant', '1800', '1999', 'First Lieutenant'),
			('pvkii', 'captain', '2000', '2249', 'Captain'),
			('pvkii', 'group-captain', '2250', '2499', 'Group Captain'),
			('pvkii', 'senior-captain', '2500', '2749', 'Senior Captain'),
			('pvkii', 'lieutenant-major', '2750', '2999', 'Lieutenant Major'),
			('pvkii', 'major', '3000', '3499', 'Major'),
			('pvkii', 'group-major', '3500', '3999', 'Group Major'),
			('pvkii', 'lieutenant-commander', '4000', '4499', 'Lieutenant Commander'),
			('pvkii', 'commander', '4500', '4999', 'Commander'),
			('pvkii', 'group-commander', '5000', '5749', 'Group Commander'),
			('pvkii', 'lieutenant-colonel', '5750', '6499', 'Lieutenant Colonel'),
			('pvkii', 'colonel', '6500', '7249', 'Colonel'),
			('pvkii', 'brigadier', '7250', '7999', 'Brigadier'),
			('pvkii', 'brigadier-general', '8000', '8999', 'Brigadier General'),
			('pvkii', 'major-general', '9000', '9999', 'Major General'),
			('pvkii', 'lieutenant-general', '10000', '12499', 'Lieutenant General'),
			('pvkii', 'general', '12500', '14999', 'General'),
			('pvkii', 'commander-general', '15000', '17499', 'Commander General'),
			('pvkii', 'field-vice-marshal', '17500', '19999', 'Field Vice Marshal'),
			('pvkii', 'field-marshal', '20000', '22499', 'Field Marshal'),
			('pvkii', 'vice-commander-of-the-army', '22500', '24999', 'Vice Commander of the Army'),
			('pvkii', 'commander-of-the-army', '25000', '27499', 'Commander of the Army'),
			('pvkii', 'high-commander', '27500', '29999', 'High Commander'),
			('pvkii', 'supreme-commander', '30000', '34999', 'Supreme Commander'),
			('pvkii', 'terminator', '35000', '9999999', 'Terminator');
	");
	
	$db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>
