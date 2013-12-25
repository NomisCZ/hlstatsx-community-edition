<?php
	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}		

	$dbversion = 48;
	
	$db->query("
		INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES
			('csp', 'headshot', 1, 0, '', 'Headshot', '1', '', '', ''),
			('csp', 'kill_streak_2', 1, 0, '', 'Double Kill (2 kills)', '1', '', '', ''),
			('csp', 'kill_streak_3', 2, 0, '', 'Triple Kill (3 kills)', '1', '', '', ''),
			('csp', 'kill_streak_4', 3, 0, '', 'Domination (4 kills)', '1', '', '', ''),
			('csp', 'kill_streak_5', 4, 0, '', 'Rampage (5 kills)', '1', '', '', ''),
			('csp', 'kill_streak_6', 5, 0, '', 'Mega Kill (6 kills)', '1', '', '', ''),
			('csp', 'kill_streak_7', 6, 0, '', 'Ownage (7 kills)', '1', '', '', ''),
			('csp', 'kill_streak_8', 7, 0, '', 'Ultra Kill (8 kills)', '1', '', '', ''),
			('csp', 'kill_streak_9', 8, 0, '', 'Killing Spree (9 kills)', '1', '', '', ''),
			('csp', 'kill_streak_10', 9, 0, '', 'Monster Kill (10 kills)', '1', '', '', ''),
			('csp', 'kill_streak_11', 10, 0, '', 'Unstoppable (11 kills)', '1', '', '', ''),
			('csp', 'kill_streak_12', 11, 0, '', 'God Like (12+ kills)', '1', '', '', '');
	");
		
	$db->query("
		INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
			('W','csp','awp','AWP','kills with awp'),
			('W','csp','galil','Galil','kills with galil'),
			('W','csp','famas','Fusil Automatique','kills with famas'),
			('W','csp','ak47','AK47','kills with ak47'),
			('W','csp','glock','Glock','kills with glock'),
			('W','csp','m4a1','Colt M4A1 Carbine','kills with m4a1'),
			('W','csp','usp','USP Master','kills with usp'),
			('W','csp','deagle','Desert Eagle','kills with deagle'),
			('W','csp','knife','Knife Maniac','knifings'),
			('W','csp','mp5navy','MP5 Navy','kills with mp5'),
			('W','csp','hegrenade','Top grenadier','kills with grenade'),
			('O','csp','headshot','Headshot King','shots in the head'),
			('W','csp','latency','Best Latency','ms average connection'),
			('W','csp','mostkills','Most Kills','kills'),
			('W','csp','suicide','Suicides','suicides'),
			('W','csp','teamkills','Team Killer','team kills');
	");
	
	$db->query("
		INSERT IGNORE INTO `hlstats_Games` (`code`, `name`, `realgame`, `hidden`) VALUES
			('csp', 'CSPromod', 'csp', '1')
	");
	
	$db->query("
		INSERT IGNORE INTO `hlstats_Games_Defaults` (`code`, `parameter`, `value`) VALUES
			('csp', 'Admins', ''),
			('csp', 'AutoBanRetry', '0'),
			('csp', 'AutoTeamBalance', '0'),
			('csp', 'BonusRoundIgnore', '0'),
			('csp', 'BonusRoundTime', '0'),
			('csp', 'BroadCastEvents', '1'),
			('csp', 'BroadCastPlayerActions', '1'),
			('csp', 'ConnectAnnounce', '1'),
			('csp', 'DefaultDisplayEvents', '1'),
			('csp', 'DisplayResultsInBrowser', '1'),
			('csp', 'EnablePublicCommands', '1'),
			('csp', 'GameEngine', '2'),
			('csp', 'GameType', '0'),
			('csp', 'HLStatsURL', 'http://yoursite.com/hlstats'),
			('csp', 'IgnoreBots', '1'),
			('csp', 'MinimumPlayersRank', '0'),
			('csp', 'MinPlayers', '4'),
			('csp', 'PlayerEvents', '1'),
			('csp', 'ShowStats', '1'),
			('csp', 'SkillMode', '0'),
			('csp', 'SuicidePenalty', '5'),
			('csp', 'SwitchAdmins', '0'),
			('csp', 'TKPenalty', '25'),
			('csp', 'TrackServerLoad', '1'),
			('csp', 'UpdateHostname', '1');
	");
	
	$db->query("
		INSERT IGNORE INTO `hlstats_Games_Supported` (`code`, `name`) VALUES
			('csp', 'CSPromod')
	");
	
	$db->query("
		INSERT IGNORE INTO `hlstats_Teams` (`game`, `code`, `name`, `hidden`, `playerlist_bgcolor`, `playerlist_color`, `playerlist_index`) VALUES
			('csp','Terrorists','Terrorists','0','#FFD5D5','#FF2D2D',1),
			('csp','Counter-Terrorists','Counter-Terrorists','0','#D2E8F7','#0080C0',2);
	");
	
	$db->query("
		INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
			('csp','knife','Bundeswehr Advanced Combat Knife',2.00),
			('csp','usp','H&K USP .45 Tactical',1.40),
			('csp','glock','Glock 18 Select Fire',1.40),
			('csp','deagle','Desert Eagle .50AE',1.20),
			('csp','mp5navy','H&K MP5-Navy',1.20),
			('csp','m4a1','Colt M4A1 Carbine',1.00),
			('csp','ak47','Kalashnikov AK-47',1.00),
			('csp','awp','Arctic Warfare Magnum (Police)',1.00),
			('csp','hegrenade','High Explosive Grenade',1.80),
			('csp','famas','Fusil Automatique',1.00),
			('csp','galil','Galil',1.10);
	");

	$db->query("
		INSERT IGNORE INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`) VALUES
			('csp', 'recruit', '0', '49', 'Recruit'),
			('csp', 'private', '50', '99', 'Private'),
			('csp', 'private-first-class', '100', '199', 'Private First Class'),
			('csp', 'lance-corporal', '200', '299', 'Lance Corporal'),
			('csp', 'corporal', '300', '399', 'Corporal'),
			('csp', 'sergeant', '400', '499', 'Sergeant'),
			('csp', 'staff-sergeant', '500', '599', 'Staff Sergeant'),
			('csp', 'gunnery-sergeant', '600', '699', 'Gunnery Sergeant'),
			('csp', 'master-sergeant', '700', '799', 'Master Sergeant'),
			('csp', 'first-sergeant', '800', '899', 'First Sergeant'),
			('csp', 'master-chief', '900', '999', 'Master Chief'),
			('csp', 'sergeant-major', '1000', '1199', 'Sergeant Major'),
			('csp', 'ensign', '1200', '1399', 'Ensign'),
			('csp', 'third-lieutenant', '1400', '1599', 'Third Lieutenant'),
			('csp', 'second-lieutenant', '1600', '1799', 'Second Lieutenant'),
			('csp', 'first-lieutenant', '1800', '1999', 'First Lieutenant'),
			('csp', 'captain', '2000', '2249', 'Captain'),
			('csp', 'group-captain', '2250', '2499', 'Group Captain'),
			('csp', 'senior-captain', '2500', '2749', 'Senior Captain'),
			('csp', 'lieutenant-major', '2750', '2999', 'Lieutenant Major'),
			('csp', 'major', '3000', '3499', 'Major'),
			('csp', 'group-major', '3500', '3999', 'Group Major'),
			('csp', 'lieutenant-commander', '4000', '4499', 'Lieutenant Commander'),
			('csp', 'commander', '4500', '4999', 'Commander'),
			('csp', 'group-commander', '5000', '5749', 'Group Commander'),
			('csp', 'lieutenant-colonel', '5750', '6499', 'Lieutenant Colonel'),
			('csp', 'colonel', '6500', '7249', 'Colonel'),
			('csp', 'brigadier', '7250', '7999', 'Brigadier'),
			('csp', 'brigadier-general', '8000', '8999', 'Brigadier General'),
			('csp', 'major-general', '9000', '9999', 'Major General'),
			('csp', 'lieutenant-general', '10000', '12499', 'Lieutenant General'),
			('csp', 'general', '12500', '14999', 'General'),
			('csp', 'commander-general', '15000', '17499', 'Commander General'),
			('csp', 'field-vice-marshal', '17500', '19999', 'Field Vice Marshal'),
			('csp', 'field-marshal', '20000', '22499', 'Field Marshal'),
			('csp', 'vice-commander-of-the-army', '22500', '24999', 'Vice Commander of the Army'),
			('csp', 'commander-of-the-army', '25000', '27499', 'Commander of the Army'),
			('csp', 'high-commander', '27500', '29999', 'High Commander'),
			('csp', 'supreme-commander', '30000', '34999', 'Supreme Commander'),
			('csp', 'terminator', '35000', '9999999', 'Terminator');
	");
	
	$db->query("
		INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES
			('*total connection hours*', 25, 2, 'csp', '1_connect.png', 'Connection Time 25 Hours'),
			('*total connection hours*', 50, 2, 'csp', '2_connect.png', 'Connection Time 50 Hours'),
			('*total connection hours*', 75, 2, 'csp', '3_connect.png', 'Connection Time 75 Hours'),
			('*total connection hours*', 100, 2, 'csp', '4_connect.png', 'Connection Time 100 Hours'),
			('*total connection hours*', 125, 2, 'csp', '5_connect.png', 'Connection Time 125 Hours'),
			('*total connection hours*', 150, 2, 'csp', '6_connect.png', 'Connection Time 150 Hours'),
			('ak47', 1, 0, 'csp', '1_ak47.png', 'Award of AK47'),
			('ak47', 5, 0, 'csp', '2_ak47.png', 'Bronze AK47'),
			('ak47', 12, 0, 'csp', '3_ak47.png', 'Silver AK47'),
			('ak47', 20, 0, 'csp', '4_ak47.png', 'Gold AK47'),
			('ak47', 30, 0, 'csp', '5_ak47.png', 'Platinum AK47'),
			('ak47', 50, 0, 'csp', '6_ak47.png', 'Supreme AK47'),
			('awp', 1, 0, 'csp', '1_awp.png', 'Award of AWP Sniper'),
			('awp', 5, 0, 'csp', '2_awp.png', 'Bronze AWP Sniper'),
			('awp', 12, 0, 'csp', '3_awp.png', 'Silver AWP Sniper'),
			('awp', 20, 0, 'csp', '4_awp.png', 'Gold AWP Sniper'),
			('awp', 30, 0, 'csp', '5_awp.png', 'Platinum AWP Sniper'),
			('awp', 50, 0, 'csp', '6_awp.png', 'Supreme AWP Sniper'),
			('deagle', 1, 0, 'csp', '1_deagle.png', 'Award of Desert Eagle'),
			('deagle', 5, 0, 'csp', '2_deagle.png', 'Bronze Desert Eagle'),
			('deagle', 12, 0, 'csp', '3_deagle.png', 'Silver Desert Eagle'),
			('deagle', 20, 0, 'csp', '4_deagle.png', 'Gold Desert Eagle'),
			('deagle', 30, 0, 'csp', '5_deagle.png', 'Platinum Desert Eagle'),
			('deagle', 50, 0, 'csp', '6_deagle.png', 'Supreme Desert Eagle'),
			('glock', 1, 0, 'csp', '1_glock.png', 'Award of Glock'),
			('glock', 5, 0, 'csp', '2_glock.png', 'Bronze Glock'),
			('glock', 12, 0, 'csp', '3_glock.png', 'Silver Glock'),
			('glock', 20, 0, 'csp', '4_glock.png', 'Gold Glock'),
			('glock', 30, 0, 'csp', '5_glock.png', 'Platinum Glock'),
			('glock', 50, 0, 'csp', '6_glock.png', 'Supreme Glock'),
			('hegrenade', 1, 0, 'csp', '1_hegrenade.png', 'Award of HE Grenades'),
			('hegrenade', 5, 0, 'csp', '2_hegrenade.png', 'Bronze HE Grenades'),
			('hegrenade', 12, 0, 'csp', '3_hegrenade.png', 'Silver HE Grenades'),
			('hegrenade', 20, 0, 'csp', '4_hegrenade.png', 'Gold HE Grenades'),
			('hegrenade', 30, 0, 'csp', '5_hegrenade.png', 'Platinum HE Grenades'),
			('hegrenade', 50, 0, 'csp', '6_hegrenade.png', 'Supreme HE Grenades'),
			('knife', 1, 0, 'csp', '1_knife.png', 'Award of Combat Knife'),
			('knife', 5, 0, 'csp', '2_knife.png', 'Bronze Combat Knife'),
			('knife', 12, 0, 'csp', '3_knife.png', 'Silver Combat Knife'),
			('knife', 20, 0, 'csp', '4_knife.png', 'Gold Combat Knife'),
			('knife', 30, 0, 'csp', '5_knife.png', 'Platinum Combat Knife'),
			('knife', 50, 0, 'csp', '6_knife.png', 'Supreme Combat Knife'),
			('usp', 1, 0, 'csp', '1_usp.png', 'Award of USP'),
			('usp', 5, 0, 'csp', '2_usp.png', 'Bronze USP'),
			('usp', 12, 0, 'csp', '3_usp.png', 'Silver USP'),
			('usp', 20, 0, 'csp', '4_usp.png', 'Gold USP'),
			('usp', 30, 0, 'csp', '5_usp.png', 'Platinum USP'),
			('usp', 50, 0, 'csp', '6_usp.png', 'Supreme USP'),
			('m4a1', 1, 0, 'csp', '1_m4a1.png', 'Award of Colt M4A1'),
			('m4a1', 5, 0, 'csp', '2_m4a1.png', 'Bronze Colt M4A1'),
			('m4a1', 12, 0, 'csp', '3_m4a1.png', 'Silver Colt M4A1'),
			('m4a1', 20, 0, 'csp', '4_m4a1.png', 'Gold Colt M4A1'),
			('m4a1', 30, 0, 'csp', '5_m4a1.png', 'Platinum Colt M4A1'),
			('m4a1', 50, 0, 'csp', '6_m4a1.png', 'Supreme Colt M4A1'),
			('mp5navy', 1, 0, 'csp', '1_mp5navy.png', 'Award of MP5 Navy'),
			('mp5navy', 5, 0, 'csp', '2_mp5navy.png', 'Bronze MP5 Navy'),
			('mp5navy', 12, 0, 'csp', '3_mp5navy.png', 'Silver MP5 Navy'),
			('mp5navy', 20, 0, 'csp', '4_mp5navy.png', 'Gold MP5 Navy'),
			('mp5navy', 30, 0, 'csp', '5_mp5navy.png', 'Platinum MP5 Navy'),
			('mp5navy', 50, 0, 'csp', '6_mp5navy.png', 'Supreme MP5 Navy'),
			('galil', 1, 0, 'csp', '1_galil.png', 'Award of Galil'),
			('galil', 5, 0, 'csp', '2_galil.png', 'Bronze Galil'),
			('galil', 12, 0, 'csp', '3_galil.png', 'Silver Galil'),
			('galil', 20, 0, 'csp', '4_galil.png', 'Gold Galil'),
			('galil', 30, 0, 'csp', '5_galil.png', 'Platinum Galil'),
			('galil', 50, 0, 'csp', '6_galil.png', 'Supreme Galil'),
			('famas', 1, 0, 'csp', '1_famas.png', 'Award of Famas'),
			('famas', 5, 0, 'csp', '2_famas.png', 'Award of Famas'),
			('famas', 12, 0, 'csp', '3_famas.png', 'Award of Famas'),
			('famas', 20, 0, 'csp', '4_famas.png', 'Award of Famas'),
			('famas', 30, 0, 'csp', '5_famas.png', 'Award of Famas'),
			('famas', 50, 0, 'csp', '6_famas.png', 'Award of Famas'),
			('latency', 1, 0, 'csp', '1_latency.png', 'Award of Lowpinger'),
			('latency', 5, 0, 'csp', '2_latency.png', 'Bronze Lowpinger'),
			('latency', 12, 0, 'csp', '3_latency.png', 'Silver Lowpinger'),
			('latency', 20, 0, 'csp', '4_latency.png', 'Gold Lowpinger'),
			('latency', 30, 0, 'csp', '5_latency.png', 'Platinum Lowpinger'),
			('latency', 50, 0, 'csp', '6_latency.png', 'Supreme Lowpinger'),
			('headshot', 1, 0, 'csp', '1_headshot.png', 'Award of Headshots'),
			('headshot', 5, 0, 'csp', '2_headshot.png', 'Bronze Headshots'),
			('headshot', 12, 0, 'csp', '3_headshot.png', 'Silver Headshots'),
			('headshot', 20, 0, 'csp', '4_headshot.png', 'Gold Headshots'),
			('headshot', 30, 0, 'csp', '5_headshot.png', 'Platinum Headshots'),
			('headshot', 50, 0, 'csp', '6_headshot.png', 'Supreme Headshots');
	");
	
	$db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>
