ALTER TABLE `hlstats_Actions`
DROP KEY `gamecode`,
ADD UNIQUE KEY `gamecode` (`code`,`game`,`object`,`event`,`team`);

INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`, `count`, `object`, `event`) VALUES
('ff', 'headshot', 1, 0, '', 'Headshot kill', '1', '', '', '', 0, NULL, NULL),
('hidden', 'kill_streak_2', 1, 0, '', 'Double Kill (2 kills)', '1', '', '', '', 0, NULL, NULL),
('hidden', 'kill_streak_3', 2, 0, '', 'Triple Kill (3 kills)', '1', '', '', '', 0, NULL, NULL),
('hidden', 'kill_streak_4', 3, 0, '', 'Domination (4 kills)', '1', '', '', '', 0, NULL, NULL),
('hidden', 'kill_streak_5', 4, 0, '', 'Rampage (5 kills)', '1', '', '', '', 0, NULL, NULL),
('hidden', 'kill_streak_6', 5, 0, '', 'Mega Kill (6 kills)', '1', '', '', '', 0, NULL, NULL),
('hidden', 'kill_streak_7', 6, 0, '', 'Ownage (7 kills)', '1', '', '', '', 0, NULL, NULL),
('hidden', 'kill_streak_8', 7, 0, '', 'Ultra Kill (8 kills)', '1', '', '', '', 0, NULL, NULL),
('hidden', 'kill_streak_9', 8, 0, '', 'Killing Spree (9 kills)', '1', '', '', '', 0, NULL, NULL),
('hidden', 'kill_streak_10', 9, 0, '', 'Monster Kill (10 kills)', '1', '', '', '', 0, NULL, NULL),
('hidden', 'kill_streak_11', 10, 0, '', 'Unstoppable (11 kills)', '1', '', '', '', 0, NULL, NULL),
('hidden', 'kill_streak_12', 11, 0, '', 'God Like (12+ kills)', '1', '', '', '', 0, NULL, NULL);

UPDATE `hlstats_Actions` SET `for_PlayerActions`='0', `for_PlayerPlayerActions`='0', `for_TeamActions`='1', `for_WorldActions`='0' WHERE `game`='tf' AND `code` IN ('Round_Win','Mini_Round_Win');


INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`, `d_winner_id`, `d_winner_count`) VALUES
('W', 'hidden', 'fn2000', 'FN2000 Assault Rifle', 'kills with FN2000 Assault Rifle', NULL, NULL),
('W', 'hidden', 'p90', 'FN P90 Sub Machine Gun', 'kills with FN P90 Sub Machine Gun', NULL, NULL),
('W', 'hidden', 'shotgun', 'Remington 870 MCS Shotgun', 'kills with Remington 870 MCS Shotgun', NULL, NULL),
('W', 'hidden', 'fn303', 'FN303 Less Lethal Launcher', 'kills with FN303 Less Lethal Launcher', NULL, NULL),
('W', 'hidden', 'pistol', 'FN FiveSeven Pistol', 'kills with FN FiveSeven Pistol', NULL, NULL),
('W', 'hidden', 'pistol2', 'FNP-9 Pistol', 'kills with FNP-9 Pistol', NULL, NULL),
('W', 'hidden', 'knife', 'Kabar D2 Knife', 'kills with Kabar D2 Knife', NULL, NULL),
('W', 'hidden', 'grenade_projectile', 'Pipe Bomb', 'kills with Pipe Bomb', NULL, NULL),
('W', 'hidden', 'physics', 'Physics', 'kills with Physics', NULL, NULL);

INSERT IGNORE INTO `hlstats_Games` (`code`, `name`, `hidden`) VALUES
('hidden','The Hidden: Source','1');

INSERT IGNORE INTO `hlstats_Options` (`keyname`, `value`) VALUES
('gamehome_show_awards', '0');

INSERT IGNORE INTO `hlstats_Ranks` (`image`, `minKills`, `maxKills`, `rankName`, `game`) VALUES
('recruit',0,49,'Recruit','hidden'),
('private',50,99,'Private','hidden'),
('private_firstclass',100,149,'Private First Class','hidden'),
('second_lieutenant',150,249,'2nd Lieutenant','hidden'),
('first_lieutenant',250,499,'1st Lieutenant','hidden'),
('captain',500,749,'Captain','hidden'),
('major',750,999,'Major','hidden'),
('lieutenant_colonel',1000,1249,'Lieutenant Colonel','hidden'),
('colonel',1250,1749,'Colonel','hidden'),
('brigadier_general',1750,2499,'Brigardier General','hidden'),
('major_general',2500,4999,'Major General','hidden'),
('lieutenant_general',5000,7499,'Lieutenant General','hidden'),
('general',7500,9999,'General','hidden'),
('general_5Star',10000,14999,'5 Star General','hidden'),
('ubersoldat',15000,999999999,'The Ubersoldat','hidden');

INSERT IGNORE INTO `hlstats_Teams` (`game`, `code`, `name`, `hidden`, `playerlist_bgcolor`, `playerlist_color`, `playerlist_index`) VALUES
('hidden','Hidden','Subject 617', '0', '#F7FF89', '#808700', 1),
('hidden','IRIS','I.R.I.S.', '0', '#D2E8F7','#0080C0', 2),
('hidden','Spectator','Spectator','0', '#D5D5D5','#050505', 0);

UPDATE `hlstats_Teams` SET `code`='#FF_TEAM_UNASSIGNED' WHERE `code`='SPECTATOR' and `game`='ff';

INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`, `kills`, `headshots`) VALUES
('ff', 'weapon_umbrella','Umbrella', 10.00, 0, 0),
('ff', 'grenade_gas','Gas Grenade', 1.00, 0, 0),
('ff', 'weapon_tommygun', 'Tommygun', 1.00, 0, 0),
('ff', 'weapon_nailgun', 'Nailgun', 1.00, 0, 0),
('hidden', 'fn2000','FN2000 Assault Rifle',1.50, 0, 0),
('hidden', 'p90','FN P90 Sub Machine Gun',2.00, 0, 0),
('hidden', 'shotgun','Remington 870 MCS Shotgun',2.00, 0, 0),
('hidden', 'fn303','FN303 Less Lethal Launcher',2.00, 0, 0),
('hidden', 'pistol','FN FiveSeven Pistol',3.00, 0, 0),
('hidden', 'pistol2','FNP-9 Pistol',3.00, 0, 0),
('hidden', 'knife','Kabar D2 Knife',2.50, 0, 0),
('hidden', 'grenade_projectile','Pipe Bomb',2.00, 0, 0),
('hidden', 'physics','Physics',3.00, 0, 0);