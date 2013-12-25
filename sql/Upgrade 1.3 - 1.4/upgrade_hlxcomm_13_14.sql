ALTER TABLE `hlstats_Actions`
CHANGE COLUMN `game` `game` varchar(32) NOT NULL default 'valve';

INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`, `count`, `object`, `event`) VALUES
('aoc', 'Round_Win', 0, 2, '', 'Round Win', '', '', '1', '', 0, NULL, NULL),
('cstrike','Begin_Bomb_Defuse_Without_Kit',0,0,'CT','Start Defusing the Bomb Without a Defuse Kit','1','0','0','0', 0, NULL, NULL),
('cstrike','Begin_Bomb_Defuse_With_Kit',0,0,'CT','Start Defusing the Bomb With a Defuse Kit','1','0','0','0', 0, NULL, NULL),
('cstrike','Assassinated_The_VIP',10,0,'TERRORIST','Assassinate the VIP','1','0','0','0', 0, NULL, NULL),
('cstrike','Planted_The_Bomb',15,2,'TERRORIST','Plant the Bomb','1','0','0','0', 0, NULL, NULL),
('cstrike','Defused_The_Bomb',15,0,'CT','Defuse the Bomb','1','0','0','0', 0, NULL, NULL),
('cstrike','Touched_A_Hostage',2,0,'CT','Touch a Hostage','1','0','0','0', 0, NULL, NULL),
('cstrike','Rescued_A_Hostage',5,1,'CT','Rescue a Hostage','1','0','0','0', 0, NULL, NULL),
('cstrike','Killed_A_Hostage',-25,1,'CT','Kill a Hostage','1','0','0','0', 0, NULL, NULL),
('cstrike','Became_VIP',1,0,'CT','Become the VIP','1','0','0','0', 0, NULL, NULL),
('cstrike','Spawned_With_The_Bomb',2,0,'TERRORIST','Spawn with the Bomb','1','0','0','0', 0, NULL, NULL),
('cstrike','Got_The_Bomb',2,0,'TERRORIST','Pick up the Bomb','1','0','0','0', 0, NULL, NULL),
('cstrike','Dropped_The_Bomb',-2,0,'TERRORIST','Drop the Bomb','1','0','0','0', 0, NULL, NULL),
('cstrike','CTs_Win',0,2,'CT','All Terrorists eliminated','0','0','1','0', 0, NULL, NULL),
('cstrike','Terrorists_Win',0,2,'TERRORIST','All Counter-Terrorists eliminated','0','0','1','0', 0, NULL, NULL),
('cstrike','All_Hostages_Rescued',0,10,'CT','Counter-Terrorists rescued all the hostages','0','0','1','0', 0, NULL, NULL),
('cstrike','Target_Bombed',0,10,'TERRORIST','Terrorists bombed the target','0','0','1','0', 0, NULL, NULL),
('cstrike','VIP_Assassinated',0,6,'TERRORIST','Terrorists assassinated the VIP','0','0','1','0', 0, NULL, NULL),
('cstrike','Bomb_Defused',0,6,'CT','Counter-Terrorists defused the bomb','0','0','1','0', 0, NULL, NULL),
('cstrike','VIP_Escaped',0,10,'CT','VIP escaped','0','0','1','0', 0, NULL, NULL),
('cstrike', 'kill_streak_2', 1, 0, '', 'Double Kill (2 kills)', '1', '', '', '', 0, NULL, NULL),
('cstrike', 'kill_streak_3', 2, 0, '', 'Triple Kill (3 kills)', '1', '', '', '', 0, NULL, NULL),
('cstrike', 'kill_streak_4', 3, 0, '', 'Domination (4 kills)', '1', '', '', '', 0, NULL, NULL),
('cstrike', 'kill_streak_5', 4, 0, '', 'Rampage (5 kills)', '1', '', '', '', 0, NULL, NULL),
('cstrike', 'kill_streak_6', 5, 0, '', 'Mega Kill (6 kills)', '1', '', '', '', 0, NULL, NULL),
('cstrike', 'kill_streak_7', 6, 0, '', 'Ownage (7 kills)', '1', '', '', '', 0, NULL, NULL),
('cstrike', 'kill_streak_8', 7, 0, '', 'Ultra Kill (8 kills)', '1', '', '', '', 0, NULL, NULL),
('cstrike', 'kill_streak_9', 8, 0, '', 'Killing Spree (9 kills)', '1', '', '', '', 0, NULL, NULL),
('cstrike', 'kill_streak_10', 9, 0, '', 'Monster Kill (10 kills)', '1', '', '', '', 0, NULL, NULL),
('cstrike', 'kill_streak_11', 10, 0, '', 'Unstoppable (11 kills)', '1', '', '', '', 0, NULL, NULL),
('cstrike', 'kill_streak_12', 11, 0, '', 'God Like (12+ kills)', '1', '', '', '', 0, NULL, NULL),
('tfc','rock2_bcave1',2,3,'0','(rock2) Blow Red Cave','1','0','0','0', 0, NULL, NULL),
('tfc','rock2_rcave1',2,3,'0','(rock2) Blow Blue Cave','1','0','0','0', 0, NULL, NULL),
('tfc','rock2_rholedet',2,3,'0','(rock2) Blow Blue Yard','1','0','0','0', 0, NULL, NULL),
('tfc','rock2_bholedet',2,3,'0','(rock2) Blow Red Yard','1','0','0','0', 0, NULL, NULL),
('tfc','Team 2 dropoff',7,0,'2','Captured Blue Flag','1','0','0','0', 0, NULL, NULL),
('tfc','Team 1 dropoff',7,0,'1','Captured Red Flag','1','0','0','0', 0, NULL, NULL),
('tfc','Medic_Infection',2,0,'2','Infected Enemy','0','1','0','0', 0, NULL, NULL),
('tfc','Medic_Cured_Infection',2,0,'2','Cured Infection','0','1','0','0', 0, NULL, NULL),
('tfc','Hallucination_Grenade',1,0,'0','Hallucination','0','1','0','0', 0, NULL, NULL),
('tfc','Concussion_Grenade',1,0,'0','Concussion','0','1','0','0', 0, NULL, NULL),
('tfc','Teleporter_Entrance_Dismantle',-2,0,'0','Teleporter Entrance Dismantled','1','0','0','0', 0, NULL, NULL),
('tfc','Teleporter_Entrance_Destroyed',1,0,'0','Teleporter Entrance Destroyed','0','1','0','0', 0, NULL, NULL),
('tfc','Teleporter_Entrance_Finished',2,0,'0','Teleporter Entrance Build','1','0','0','0', 0, NULL, NULL),
('tfc','Teleporter_Exit_Dismantle',-2,0,'0','Teleporter Exit Dismantled','1','0','0','0', 0, NULL, NULL),
('tfc','Teleporter_Exit_Destroyed',1,0,'0','Teleporter Exit Destroyed','0','1','0','0', 0, NULL, NULL),
('tfc','Teleporter_Exit_Finished',2,0,'0','Teleporter Exit Build','1','0','0','0', 0, NULL, NULL),
('tfc','Teleporter_Exit_Finished',2,0,'0','Teleporter Exit Build','1','0','0','0', 0, NULL, NULL),
('tfc','Sentry_Built_Level_1',2,0,'0','Built Sentry','1','0','0','0', 0, NULL, NULL),
('tfc','Sentry_Upgrade_Level_2',1,0,'0','Upgraded Sentry to Lvl 2','1','0','0','0', 0, NULL, NULL),
('tfc','Sentry_Upgrade_Level_3',1,0,'0','Upgraded Sentry to Lvl 3','1','0','0','0', 0, NULL, NULL),
('tfc','Sentry_Destroyed',3,0,'0','Upgraded Sentry to Lvl 3','0','1','0','0', 0, NULL, NULL),
('tfc','Discovered_Spy',2,0,'0','Discovered a Spy','1','0','0','0', 0, NULL, NULL),
('tfc','Dispenser_Destroyed',5,0,'0','Dispenser Destroyed','1','0','0','0', 0, NULL, NULL),
('tfc','Built_Dispenser',8,0,'0','Built Dispenser','1','0','0','0', 0, NULL, NULL),
('tfc', 'kill_streak_2', 1, 0, '', 'Double Kill (2 kills)', '1', '', '', '', 0, NULL, NULL),
('tfc', 'kill_streak_3', 2, 0, '', 'Triple Kill (3 kills)', '1', '', '', '', 0, NULL, NULL),
('tfc', 'kill_streak_4', 3, 0, '', 'Domination (4 kills)', '1', '', '', '', 0, NULL, NULL),
('tfc', 'kill_streak_5', 4, 0, '', 'Rampage (5 kills)', '1', '', '', '', 0, NULL, NULL),
('tfc', 'kill_streak_6', 5, 0, '', 'Mega Kill (6 kills)', '1', '', '', '', 0, NULL, NULL),
('tfc', 'kill_streak_7', 6, 0, '', 'Ownage (7 kills)', '1', '', '', '', 0, NULL, NULL),
('tfc', 'kill_streak_8', 7, 0, '', 'Ultra Kill (8 kills)', '1', '', '', '', 0, NULL, NULL),
('tfc', 'kill_streak_9', 8, 0, '', 'Killing Spree (9 kills)', '1', '', '', '', 0, NULL, NULL),
('tfc', 'kill_streak_10', 9, 0, '', 'Monster Kill (10 kills)', '1', '', '', '', 0, NULL, NULL),
('tfc', 'kill_streak_11', 10, 0, '', 'Unstoppable (11 kills)', '1', '', '', '', 0, NULL, NULL),
('tfc', 'kill_streak_12', 11, 0, '', 'God Like (12+ kills)', '1', '', '', '', 0, NULL, NULL),
('dod','dod_control_point',6,1,'','Control Points Captured','1','0','1','0', 0, NULL, NULL),
('dod','dod_capture_area',6,1,'','Areas Captured','1','0','1','0', 0, NULL, NULL),
('dod','dod_object_goal',4,0,'','Objectives Achieved','1','0','0','0', 0, NULL, NULL),
('dod', 'kill_streak_2', 1, 0, '', 'Double Kill (2 kills)', '1', '', '', '', 0, NULL, NULL),
('dod', 'kill_streak_3', 2, 0, '', 'Triple Kill (3 kills)', '1', '', '', '', 0, NULL, NULL),
('dod', 'kill_streak_4', 3, 0, '', 'Domination (4 kills)', '1', '', '', '', 0, NULL, NULL),
('dod', 'kill_streak_5', 4, 0, '', 'Rampage (5 kills)', '1', '', '', '', 0, NULL, NULL),
('dod', 'kill_streak_6', 5, 0, '', 'Mega Kill (6 kills)', '1', '', '', '', 0, NULL, NULL),
('dod', 'kill_streak_7', 6, 0, '', 'Ownage (7 kills)', '1', '', '', '', 0, NULL, NULL),
('dod', 'kill_streak_8', 7, 0, '', 'Ultra Kill (8 kills)', '1', '', '', '', 0, NULL, NULL),
('dod', 'kill_streak_9', 8, 0, '', 'Killing Spree (9 kills)', '1', '', '', '', 0, NULL, NULL),
('dod', 'kill_streak_10', 9, 0, '', 'Monster Kill (10 kills)', '1', '', '', '', 0, NULL, NULL),
('dod', 'kill_streak_11', 10, 0, '', 'Unstoppable (11 kills)', '1', '', '', '', 0, NULL, NULL),
('dod', 'kill_streak_12', 11, 0, '', 'God Like (12+ kills)', '1', '', '', '', 0, NULL, NULL),
('ns','structure_built',1,0,'','Structures Built','1','0','0','0', 0, NULL, NULL),
('ns','structure_destroyed',2,0,'','Structures Destroyed','1','0','0','0', 0, NULL, NULL),
('ns','research_start',1,0,'','Researches Performed','1','0','0','0', 0, NULL, NULL),
('ns','recycle',-3,0,'','Structures Recycled','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_alienresourcetower',1,0,'','Built Alien Resource Tower','1','0','0','0', 0, NULL, NULL),
('ns','structure_destroyed_alienresourcetower',2,0,'','Destroyed Alien Resource Tower','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_phasegate',1,0,'','Built Phasegate','1','0','0','0', 0, NULL, NULL),
('ns','structure_destroyed_phasegate',2,0,'','Destroyed Phasegate','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_resourcetower',1,0,'','Built Resource Tower','1','0','0','0', 0, NULL, NULL),
('ns','structure_destroyed_resourcetower',2,0,'','Destroyed Resource Tower','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_siegeturret',1,0,'','Built Siege Turret','1','0','0','0', 0, NULL, NULL),
('ns','structure_destroyed_siegeturret',2,0,'','Destroyed Siege Turret','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_team_advturretfactory',1,0,'','Built Advanced Turret Factory','1','0','0','0', 0, NULL, NULL),
('ns','structure_destroyed_team_advturretfactory',2,0,'','Destroyed Advanced Turret Factory','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_team_armory',1,0,'','Built Armory','1','0','0','0', 0, NULL, NULL),
('ns','structure_destroyed_team_armory',2,0,'','Destroyed Armory','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_team_turretfactory',1,0,'','Built Turret Factory','1','0','0','0', 0, NULL, NULL),
('ns','structure_destroyed_team_turretfactory',2,0,'','Destroyed Turret Factory','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_turret',1,0,'','Built Turret','1','0','0','0', 0, NULL, NULL),
('ns','structure_destroyed_turret',2,0,'','Destroyed Turret','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_team_infportal',1,0,'','Built INF Portal','1','0','0','0', 0, NULL, NULL),
('ns','structure_destroyed_team_infportal',2,0,'','Destroyed INF Portal','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_defensechamber',1,0,'','Built Defense Chamber','1','0','0','0', 0, NULL, NULL),
('ns','structure_destroyed_defensechamber',2,0,'','Destroyed Defense Chamber','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_offensechamber',1,0,'','Built Offense Chamber','1','0','0','0', 0, NULL, NULL),
('ns','structure_destroyed_offensechamber',2,0,'','Destroyed Offense Chamber','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_movementchamber',1,0,'','Built Movement Chamber','1','0','0','0', 0, NULL, NULL),
('ns','structure_destroyed_movementchamber',2,0,'','Destroyed Movement Chamber','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_team_hive',1,0,'','Built Alien Hive','1','0','0','0', 0, NULL, NULL),
('ns','structure_destroyed_team_hive',2,0,'','Destroyed Alien Hive','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_team_armslab',1,0,'','Built Arms Lab','1','0','0','0', 0, NULL, NULL),
('ns','structure_destroyed_team_armslab',2,0,'','Destroyed Arms Lab','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_sensorychamber',1,0,'','Built Sensory Chamber','1','0','0','0', 0, NULL, NULL),
('ns','structure_destroyed_sensorychamber',2,0,'','Destroyed Sensory Chamber','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_team_prototypelab',1,0,'','Built Prototype Lab','1','0','0','0', 0, NULL, NULL),
('ns','structure_destroyed_team_prototypelab',2,0,'','Destroyed Prototype Lab','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_team_command',1,0,'','Built Command Unit','1','0','0','0', 0, NULL, NULL),
('ns','structure_destroyed_team_command',2,0,'','Destroyed Command Unit','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_team_observatory',1,0,'','Built Observatory','1','0','0','0', 0, NULL, NULL),
('ns','structure_destroyed_team_observatory',1,0,'','Destroyed Observatory','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_team_advarmory',1,0,'','Built Advanced Armory','1','0','0','0', 0, NULL, NULL),
('ns','structure_destroyed_team_advarmory',1,0,'','Destroyed Advanced Armory','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_scan',1,0,'','Built Scanner','1','0','0','0', 0, NULL, NULL),
('ns','structure_destroyed_scan',1,0,'','Destroyed Scanner','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_weapon_grenadegun',1,0,'','Created a Grenade Gun','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_weapon_heavymachinegun',1,0,'','Created a Heavy Machine Gun','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_weapon_shotgun',1,0,'','Created a Shotgun','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_weapon_welder',1,0,'','Created a Welder','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_weapon_mine',1,0,'','Created a Mine','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_item_heavyarmour',1,0,'','Created Heavy Armour','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_item_catalyst',1,0,'','Created a Catalyst','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_item_genericammo',1,0,'','Created Generic Ammo','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_item_health',1,0,'','Created a Healthpack','1','0','0','0', 0, NULL, NULL),
('ns','structure_built_item_jetpack',1,0,'','Created a Jetpack','1','0','0','0', 0, NULL, NULL),
('ns','research_cancel',-1,0,'','Stopped Researching ','1','0','0','0', 0, NULL, NULL),
('ns', 'kill_streak_2', 1, 0, '', 'Double Kill (2 kills)', '1', '', '', '', 0, NULL, NULL),
('ns', 'kill_streak_3', 2, 0, '', 'Triple Kill (3 kills)', '1', '', '', '', 0, NULL, NULL),
('ns', 'kill_streak_4', 3, 0, '', 'Domination (4 kills)', '1', '', '', '', 0, NULL, NULL),
('ns', 'kill_streak_5', 4, 0, '', 'Rampage (5 kills)', '1', '', '', '', 0, NULL, NULL),
('ns', 'kill_streak_6', 5, 0, '', 'Mega Kill (6 kills)', '1', '', '', '', 0, NULL, NULL),
('ns', 'kill_streak_7', 6, 0, '', 'Ownage (7 kills)', '1', '', '', '', 0, NULL, NULL),
('ns', 'kill_streak_8', 7, 0, '', 'Ultra Kill (8 kills)', '1', '', '', '', 0, NULL, NULL),
('ns', 'kill_streak_9', 8, 0, '', 'Killing Spree (9 kills)', '1', '', '', '', 0, NULL, NULL),
('ns', 'kill_streak_10', 9, 0, '', 'Monster Kill (10 kills)', '1', '', '', '', 0, NULL, NULL),
('ns', 'kill_streak_11', 10, 0, '', 'Unstoppable (11 kills)', '1', '', '', '', 0, NULL, NULL),
('ns', 'kill_streak_12', 11, 0, '', 'God Like (12+ kills)', '1', '', '', '', 0, NULL, NULL);

ALTER TABLE `hlstats_Awards`
ADD COLUMN `g_winner_id` int(10) unsigned default NULL,
ADD COLUMN `g_winner_count` int(10) unsigned default NULL,
CHANGE COLUMN `game` `game` varchar(32) NOT NULL default 'valve',
CHANGE COLUMN `code` `code` varchar(64) NOT NULL default '',
CHANGE COLUMN `name` `name` varchar(128) NOT NULL default '',
CHANGE COLUMN `verb` `verb` varchar(128) NOT NULL default '';
  
UPDATE `hlstats_Awards` SET `code` = 'Flamberge' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'flamberge';
UPDATE `hlstats_Awards` SET `code` = 'Longsword' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'longsword';
UPDATE `hlstats_Awards` SET `code` = 'Glaive', `name` = 'Glaive', `verb` = 'kills with Glaive' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'halberd';
UPDATE `hlstats_Awards` SET `code` = 'Dual Daggers' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'dagger';
UPDATE `hlstats_Awards` SET `code` = 'Flamberge & Kite Shield' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'flamberge_kiteshield';
UPDATE `hlstats_Awards` SET `code` = 'Warhammer' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'warhammer';
UPDATE `hlstats_Awards` SET `code` = 'Mace' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'mace';
UPDATE `hlstats_Awards` SET `code` = 'Mace & Buckler' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'mace_buckler';
UPDATE `hlstats_Awards` SET `code` = 'Broadsword & Evil Shield' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'sword01_evil_shield';
UPDATE `hlstats_Awards` SET `code` = 'Crossbow' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'crossbow';
UPDATE `hlstats_Awards` SET `code` = 'Longbow' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'longbow';
UPDATE `hlstats_Awards` SET `code` = 'Longsword & Kite Shield' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'longsword_kiteshield';
UPDATE `hlstats_Awards` SET `code` = 'Broadsword & Good Shield' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'sword01_good_shield';
UPDATE `hlstats_Awards` SET `code` = 'Hatchet' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'onehandaxe';
UPDATE `hlstats_Awards` SET `code` = 'Double Axe' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'doubleaxe';
UPDATE `hlstats_Awards` SET `code` = 'Flail & Evil Shield' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'flail_evil_shield';
UPDATE `hlstats_Awards` SET `code` = 'Flail & Good Shield' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'flail_good_shield';
UPDATE `hlstats_Awards` SET `code` = 'Javelin' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'thrown_spear';
UPDATE `hlstats_Awards` SET `code` = 'Spiked Mace' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'shortsword';
UPDATE `hlstats_Awards` SET `code` = 'Shortsword' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'sword2';
DELETE FROM `hlstats_Awards` WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'spear_buckler2';
UPDATE `hlstats_Awards` SET `code` = 'Spear & Buckler', `name` = 'Spear & Buckler', `verb` = 'kills with Spear & Buckler' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'spear_buckler';
UPDATE `hlstats_Awards` SET `code` = 'Spiked Mace & Buckler' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'spikedmace_buckler';
UPDATE `hlstats_Awards` SET `code` = 'Dagger' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'dagger2';
UPDATE `hlstats_Awards` SET `code` = 'Broadsword', `name` = 'Broadsword', `verb` = 'kills with Broadsword' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'mtest';
UPDATE `hlstats_Awards` SET `code` = 'Throwing Knife', `name` = 'Throwing Knife', `verb` = 'kills with Throwing Knives' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'thrown_dagger2';
UPDATE `hlstats_Awards` SET `code` = 'Halberd', `name` = 'Halberd', `verb` = 'kills with Halberd' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'evil_halberd';
UPDATE `hlstats_Awards` SET `code` = 'Oilpot' WHERE `game` LIKE 'aoc%' AND `awardType` = 'W' AND `code` = 'oilpot';

INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
('W', 'ff','latency','Best Latency','ms average connection'),
('W', 'hidden','latency','Best Latency','ms average connection'),
('W', 'zps', 'latency','Best Latency','ms average connection'),
('W', 'aoc','latency','Best Latency','ms average connection'),
('O','cstrike','Defused_The_Bomb','Top Defuser','bomb defusions'),
('O','cstrike','Planted_The_Bomb','Top Demolitionist','bomb plantings'),
('O','cstrike','Rescued_A_Hostage','Top Hostage Rescuer','hostages rescued'),
('O','cstrike','Assassinated_The_VIP','Top Assassin','assassinations'),
('W','cstrike','elite','Dual Berretta Elites','kills with elite'),
('W','cstrike','knife','Knife Maniac','knifings'),
('W','cstrike','awp','AWP','snipings with awp'),
('W','cstrike','p90','P90','kills with p90'),
('W','cstrike','deagle','Desert Eagle','kills with deagle'),
('W','cstrike','m3','Shotgun','kills with m3 shotgun'),
('W','cstrike','usp','USP Master','kills with usp'),
('W','cstrike','m4a1','Colt M4A1 Carbine','kills with m4a1'),
('W','cstrike','glock18','Glock','kills with glock18'),
('W','cstrike','ak47','AK47','kills with ak47'),
('W','cstrike','famas','Fusil Automatique','kills with famas'),
('W','cstrike','galil','Galil','kills with galil'),
('W','cstrike','latency','Best Latency','ms average connection'),
('W','tfc','axe','Crowbar Maniac','murders with crowbar'),
('W','tfc','spanner','Evil Engie','bludgeonings with spanner'),
('W','tfc','rocket','Rocketeer','kills with rocket'),
('W','tfc','ac','HWGuy Extraordinaire','ownings with ac'),
('W','tfc','sniperrifle','Red Dot Special','snipings'),
('W','tfc','flames','Fire Man','roastings'),
('W','tfc','latency','Best Latency','ms average connection'),
('W','dod','amerknife','Backstabbing Beotch','kills with the American Knife'),
('W','dod','luger','Luger Freak','kills with the Luger 08 Pistol'),
('W','dod','kar','KarMeister','kills with the Mauser Kar 98k'),
('W','dod','mp40','MP40 Hor','kills with the MP40 Machine Pistol'),
('W','dod','spade','Shovel God','kills with the spade'),
('W','dod','mp44','MP44 Hor','kills with the MP44 Assault Rifle'),
('W','dod','colt','Colt Freak','kills with the Colt .45 model 1911'),
('W','dod','garand','GarandMeister','kills with the M1 Garand Rifle'),
('W','dod','thompson','Thompson Hor','kills with the Thompson Submachine Gun'),
('W','dod','spring','Spring Sniper','snipings with the Springfield 03 Rifle'),
('W','dod','bar','Bar Browning Hor','kills with the BAR Browning Automatic Rifle'),
('W','dod','grenade','McVeigh Alert','bombings with the Grenade'),
('W','dod','garandbutt','Headsmasher','kills with Garand Butt Stock'),
('W','dod','bazooka','Bazooka Joe','kills with the Bazooka'),
('W','dod','pschreck','Panzerschreck Hans','kills with the Panzerschreck'),
('W','dod','latency','Best Latency','ms average connection'),
('W','ns','slash','Vicious Kitty','killings by le Swipe'),
('W','ns','shotgun','Buckshot Masta','killings with the shotty'),
('W','ns','pistol','Harold Handgun Alert','asskickings by pistola'),
('W','ns','knife','Iron Chef Alert','vicious stabbings'),
('W','ns','grenade','absolute n00b','pathetic killings by n00b grenades'),
('W','ns','bitegun','Teething Tommy','killings with le jaw'),
('W','ns','bite2gun','Mouth Full','killings with le big jaw'),
('W','ns','leap','Tigger Alert','crushings by leap'),
('W','ns','divinewind','Silent but Violent','slayings by recal relief'),
('W','ns','sporegunprojectile','Left Feet Larry','killings with Lerk'),
('W','ns','devour','Hungry Hungry Hippo','killings by Ingestion'),
('W','ns','spitgunspit','Masta Fatty','Marines too dumb to kill a gorge'),
('W','ns','latency','Best Latency','ms average connection');

DROP TABLE IF EXISTS `hlstats_Awards_Global`;

ALTER TABLE `hlstats_Clans`
CHANGE COLUMN `tag` `tag` varchar(64) NOT NULL default '',
CHANGE COLUMN `name` `name` varchar(128) NOT NULL default '',
CHANGE COLUMN `homepage` `homepage` varchar(64) NOT NULL default '',
CHANGE COLUMN `game` `game` varchar(32) NOT NULL default '';

ALTER TABLE `hlstats_ClanTags`
CHANGE COLUMN `pattern` `pattern` varchar(64) NOT NULL;

UPDATE hlstats_Countries SET `flag` = 'GB' WHERE `flag`='UK';


UPDATE `hlstats_Events_Frags` SET `weapon` = 'Flamberge' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'flamberge';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Longsword' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'longsword';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Glaive' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'halberd';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Dual Daggers' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'dagger';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Flamberge & Kite Shield' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'flamberge_kiteshield';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Warhammer' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'warhammer';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Mace' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'mace';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Mace & Buckler' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'mace_buckler';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Broadsword & Evil Shield' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'sword01_evil_shield';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Crossbow' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'crossbow';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Longbow' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'longbow';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Longsword & Kite Shield' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'longsword_kiteshield';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Broadsword & Good Shield' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'sword01_good_shield';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Hatchet' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'onehandaxe';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Double Axe' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'doubleaxe';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Flail & Evil Shield' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'flail_evil_shield';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Flail & Good Shield' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'flail_good_shield';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Javelin' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'thrown_spear';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Spiked Mace' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'shortsword';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Shortsword' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'sword2';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Spear & Buckler' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` IN ('spear_buckler','spear_buckler2');
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Spiked Mace & Buckler' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'spikedmace_buckler';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Dagger' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'dagger2';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Broadsword' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'mtest';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Throwing Knife' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'thrown_dagger2';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Halberd' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'evil_halberd';
UPDATE `hlstats_Events_Frags` SET `weapon` = 'Oilpot' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'oilpot';


UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Flamberge' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'flamberge';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Longsword' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'longsword';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Glaive' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'halberd';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Dual Daggers' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'dagger';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Flamberge & Kite Shield' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'flamberge_kiteshield';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Warhammer' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'warhammer';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Mace' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'mace';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Mace & Buckler' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'mace_buckler';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Broadsword & Evil Shield' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'sword01_evil_shield';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Crossbow' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'crossbow';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Longbow' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'longbow';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Longsword & Kite Shield' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'longsword_kiteshield';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Broadsword & Good Shield' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'sword01_good_shield';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Hatchet' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'onehandaxe';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Double Axe' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'doubleaxe';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Flail & Evil Shield' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'flail_evil_shield';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Flail & Good Shield' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'flail_good_shield';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Javelin' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'thrown_spear';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Spiked Mace' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'shortsword';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Shortsword' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'sword2';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Spear & Buckler' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` IN ('spear_buckler','spear_buckler2');
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Spiked Mace & Buckler' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'spikedmace_buckler';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Dagger' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'dagger2';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Broadsword' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'mtest';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Throwing Knife' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'thrown_dagger2';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Halberd' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'evil_halberd';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'Oilpot' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'oilpot';



UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Flamberge' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'flamberge';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Longsword' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'longsword';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Glaive' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'halberd';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Dual Daggers' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'dagger';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Flamberge & Kite Shield' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'flamberge_kiteshield';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Warhammer' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'warhammer';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Mace' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'mace';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Mace & Buckler' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'mace_buckler';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Broadsword & Evil Shield' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'sword01_evil_shield';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Crossbow' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'crossbow';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Longbow' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'longbow';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Longsword & Kite Shield' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'longsword_kiteshield';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Broadsword & Good Shield' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'sword01_good_shield';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Hatchet' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'onehandaxe';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Double Axe' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'doubleaxe';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Flail & Evil Shield' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'flail_evil_shield';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Flail & Good Shield' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'flail_good_shield';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Javelin' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'thrown_spear';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Spiked Mace' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'shortsword';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Shortsword' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'sword2';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Spear & Buckler' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` IN ('spear_buckler','spear_buckler2');
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Spiked Mace & Buckler' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'spikedmace_buckler';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Dagger' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'dagger2';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Broadsword' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'mtest';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Throwing Knife' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'thrown_dagger2';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Halberd' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'evil_halberd';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'Oilpot' WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` LIKE 'aoc%') AND `weapon` = 'oilpot';

ALTER TABLE `hlstats_Games`
CHANGE COLUMN `code` `code` varchar(32) NOT NULL default '',
CHANGE COLUMN `name` `name` varchar(128) NOT NULL default '',
ADD COLUMN `realgame` varchar(32) NOT NULL default 'hl2mp' AFTER `name`;

UPDATE `hlstats_Games` SET `realgame` = 'css' WHERE `code` LIKE 'css%';
UPDATE `hlstats_Games` SET `realgame` = 'hl2mp' WHERE `code` LIKE 'hl2mp%';
UPDATE `hlstats_Games` SET `realgame` = 'tf' WHERE `code` LIKE 'tf%';
UPDATE `hlstats_Games` SET `realgame` = 'hl2mp' WHERE `code` LIKE 'hl2ctf%';
UPDATE `hlstats_Games` SET `realgame` = 'dods' WHERE `code` LIKE 'dods%';
UPDATE `hlstats_Games` SET `realgame` = 'insmod' WHERE `code` LIKE 'ins%';
UPDATE `hlstats_Games` SET `realgame` = 'ff' WHERE `code` LIKE 'ff%';
UPDATE `hlstats_Games` SET `realgame` = 'hidden' WHERE `code` LIKE 'hidden%';
UPDATE `hlstats_Games` SET `realgame` = 'zps' WHERE `code` LIKE 'zps%';
UPDATE `hlstats_Games` SET `realgame` = 'aoc' WHERE `code` LIKE 'aoc%';

INSERT IGNORE INTO `hlstats_Games` (`code`, `name`, `realgame`, `hidden`) VALUES
('cstrike','Counter-Strike','cstrike','1'),
('tfc','Team Fortress Classic','tfc','1'),
('dod','Day of Defeat','dod','1'),
('ns','Natural Selection','ns','1');

CREATE TABLE IF NOT EXISTS `hlstats_Games_Defaults` (
  `code` varchar(32) NOT NULL,
  `parameter` varchar(50) NOT NULL,
  `value` varchar(128) NOT NULL,
  PRIMARY KEY  (`code`,`parameter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `hlstats_Games_Defaults` (`code`, `parameter`, `value`) VALUES
('aoc', 'DisplayResultsInBrowser', '1'),
('css', 'DisplayResultsInBrowser', '1'),
('cstrike', 'DisplayResultsInBrowser', '1'),
('dod', 'DisplayResultsInBrowser', '1'),
('dods', 'DisplayResultsInBrowser', '1'),
('ff', 'DisplayResultsInBrowser', '1'),
('hidden', 'DisplayResultsInBrowser', '1'),
('hl2mp', 'DisplayResultsInBrowser', '1'),
('insmod', 'DisplayResultsInBrowser', '1'),
('ns', 'DisplayResultsInBrowser', '0'),
('tf', 'DisplayResultsInBrowser', '1'),
('tfc', 'DisplayResultsInBrowser', '0'),
('zps', 'DisplayResultsInBrowser', '1'),
('aoc', 'GameEngine', '3'),
('css', 'GameEngine', '2'),
('cstrike', 'GameEngine', '1'),
('dod', 'GameEngine', '2'),
('dods', 'GameEngine', '3'),
('ff', 'GameEngine', '2'),
('hidden', 'GameEngine', '2'),
('hl2mp', 'GameEngine', '2'),
('insmod', 'GameEngine', '2'),
('ns', 'GameEngine', '1'),
('tf', 'GameEngine', '3'),
('tfc', 'GameEngine', '1'),
('zps', 'GameEngine', '3'),
('aoc', 'AddressPort', '0.0.0.0:27015'),
('css', 'AddressPort', '0.0.0.0:27015'),
('cstrike', 'AddressPort', '0.0.0.0:27015'),
('dod', 'AddressPort', '0.0.0.0:27015'),
('dods', 'AddressPort', '0.0.0.0:27015'),
('ff', 'AddressPort', '0.0.0.0:27015'),
('hidden', 'AddressPort', '0.0.0.0:27015'),
('hl2mp', 'AddressPort', '0.0.0.0:27015'),
('insmod', 'AddressPort', '0.0.0.0:27015'),
('ns', 'AddressPort', '0.0.0.0:27015'),
('tf', 'AddressPort', '0.0.0.0:27015'),
('tfc', 'AddressPort', '0.0.0.0:27015'),
('zps', 'AddressPort', '0.0.0.0:27015'),
('aoc', 'AdminContact', 'user@yourdomain.com'),
('css', 'AdminContact', 'user@yourdomain.com'),
('cstrike', 'AdminContact', 'user@yourdomain.com'),
('dod', 'AdminContact', 'user@yourdomain.com'),
('dods', 'AdminContact', 'user@yourdomain.com'),
('ff', 'AdminContact', 'user@yourdomain.com'),
('hidden', 'AdminContact', 'user@yourdomain.com'),
('hl2mp', 'AdminContact', 'user@yourdomain.com'),
('insmod', 'AdminContact', 'user@yourdomain.com'),
('ns', 'AdminContact', 'user@yourdomain.com'),
('tf', 'AdminContact', 'user@yourdomain.com'),
('tfc', 'AdminContact', 'user@yourdomain.com'),
('zps', 'AdminContact', 'user@yourdomain.com'),
('aoc', 'Admins', ''),
('css', 'Admins', ''),
('cstrike', 'Admins', ''),
('dod', 'Admins', ''),
('dods', 'Admins', ''),
('ff', 'Admins', ''),
('hidden', 'Admins', ''),
('hl2mp', 'Admins', ''),
('insmod', 'Admins', ''),
('ns', 'Admins', ''),
('tf', 'Admins', ''),
('tfc', 'Admins', ''),
('zps', 'Admins', ''),
('aoc', 'AutoBanRetry', '0'),
('css', 'AutoBanRetry', '0'),
('cstrike', 'AutoBanRetry', '0'),
('dod', 'AutoBanRetry', '0'),
('dods', 'AutoBanRetry', '0'),
('ff', 'AutoBanRetry', '0'),
('hidden', 'AutoBanRetry', '0'),
('hl2mp', 'AutoBanRetry', '0'),
('insmod', 'AutoBanRetry', '0'),
('ns', 'AutoBanRetry', '0'),
('tf', 'AutoBanRetry', '0'),
('tfc', 'AutoBanRetry', '0'),
('zps', 'AutoBanRetry', '0'),
('aoc', 'AutoTeamBalance', '0'),
('css', 'AutoTeamBalance', '0'),
('cstrike', 'AutoTeamBalance', '0'),
('dod', 'AutoTeamBalance', '0'),
('dods', 'AutoTeamBalance', '0'),
('ff', 'AutoTeamBalance', '0'),
('hidden', 'AutoTeamBalance', '0'),
('hl2mp', 'AutoTeamBalance', '0'),
('insmod', 'AutoTeamBalance', '0'),
('ns', 'AutoTeamBalance', '0'),
('tf', 'AutoTeamBalance', '0'),
('tfc', 'AutoTeamBalance', '0'),
('zps', 'AutoTeamBalance', '0'),
('aoc', 'BonusRoundIgnore', '0'),
('css', 'BonusRoundIgnore', '0'),
('cstrike', 'BonusRoundIgnore', '0'),
('dod', 'BonusRoundIgnore', '0'),
('dods', 'BonusRoundIgnore', '0'),
('ff', 'BonusRoundIgnore', '0'),
('hidden', 'BonusRoundIgnore', '0'),
('hl2mp', 'BonusRoundIgnore', '0'),
('insmod', 'BonusRoundIgnore', '0'),
('ns', 'BonusRoundIgnore', '0'),
('tf', 'BonusRoundIgnore', '0'),
('tfc', 'BonusRoundIgnore', '0'),
('zps', 'BonusRoundIgnore', '0'),
('aoc', 'BonusRoundTime', '0'),
('css', 'BonusRoundTime', '0'),
('cstrike', 'BonusRoundTime', '0'),
('dod', 'BonusRoundTime', '0'),
('dods', 'BonusRoundTime', '0'),
('ff', 'BonusRoundTime', '0'),
('hidden', 'BonusRoundTime', '0'),
('hl2mp', 'BonusRoundTime', '0'),
('insmod', 'BonusRoundTime', '0'),
('ns', 'BonusRoundTime', '0'),
('tf', 'BonusRoundTime', '0'),
('tfc', 'BonusRoundTime', '0'),
('zps', 'BonusRoundTime', '0'),
('aoc', 'BroadCastEvents', '1'),
('css', 'BroadCastEvents', '1'),
('cstrike', 'BroadCastEvents', '1'),
('dod', 'BroadCastEvents', '1'),
('dods', 'BroadCastEvents', '1'),
('ff', 'BroadCastEvents', '1'),
('hidden', 'BroadCastEvents', '1'),
('hl2mp', 'BroadCastEvents', '1'),
('insmod', 'BroadCastEvents', '1'),
('ns', 'BroadCastEvents', '1'),
('tf', 'BroadCastEvents', '1'),
('tfc', 'BroadCastEvents', '1'),
('zps', 'BroadCastEvents', '1'),
('aoc', 'BroadCastPlayerActions', '1'),
('css', 'BroadCastPlayerActions', '1'),
('cstrike', 'BroadCastPlayerActions', '1'),
('dod', 'BroadCastPlayerActions', '1'),
('dods', 'BroadCastPlayerActions', '1'),
('ff', 'BroadCastPlayerActions', '1'),
('hidden', 'BroadCastPlayerActions', '1'),
('hl2mp', 'BroadCastPlayerActions', '1'),
('insmod', 'BroadCastPlayerActions', '1'),
('ns', 'BroadCastPlayerActions', '1'),
('tf', 'BroadCastPlayerActions', '1'),
('tfc', 'BroadCastPlayerActions', '1'),
('zps', 'BroadCastPlayerActions', '1'),
('aoc', 'EnablePublicCommands', '1'),
('css', 'EnablePublicCommands', '1'),
('cstrike', 'EnablePublicCommands', '1'),
('dod', 'EnablePublicCommands', '1'),
('dods', 'EnablePublicCommands', '1'),
('ff', 'EnablePublicCommands', '1'),
('hidden', 'EnablePublicCommands', '1'),
('hl2mp', 'EnablePublicCommands', '1'),
('insmod', 'EnablePublicCommands', '1'),
('ns', 'EnablePublicCommands', '1'),
('tf', 'EnablePublicCommands', '1'),
('tfc', 'EnablePublicCommands', '1'),
('zps', 'EnablePublicCommands', '1'),
('aoc', 'GameType', '0'),
('css', 'GameType', '0'),
('cstrike', 'GameType', '0'),
('dod', 'GameType', '0'),
('dods', 'GameType', '0'),
('ff', 'GameType', '0'),
('hidden', 'GameType', '0'),
('hl2mp', 'GameType', '0'),
('insmod', 'GameType', '0'),
('ns', 'GameType', '0'),
('tf', 'GameType', '0'),
('tfc', 'GameType', '0'),
('zps', 'GameType', '0'),
('aoc', 'HLStatsURL', 'http://yoursite.com/hlstats'),
('css', 'HLStatsURL', 'http://yoursite.com/hlstats'),
('cstrike', 'HLStatsURL', 'http://yoursite.com/hlstats'),
('dod', 'HLStatsURL', 'http://yoursite.com/hlstats'),
('dods', 'HLStatsURL', 'http://yoursite.com/hlstats'),
('ff', 'HLStatsURL', 'http://yoursite.com/hlstats'),
('hidden', 'HLStatsURL', 'http://yoursite.com/hlstats'),
('hl2mp', 'HLStatsURL', 'http://yoursite.com/hlstats'),
('insmod', 'HLStatsURL', 'http://yoursite.com/hlstats'),
('ns', 'HLStatsURL', 'http://yoursite.com/hlstats'),
('tf', 'HLStatsURL', 'http://yoursite.com/hlstats'),
('tfc', 'HLStatsURL', 'http://yoursite.com/hlstats'),
('zps', 'HLStatsURL', 'http://yoursite.com/hlstats'),
('aoc', 'IgnoreBots', '1'),
('css', 'IgnoreBots', '1'),
('cstrike', 'IgnoreBots', '1'),
('dod', 'IgnoreBots', '1'),
('dods', 'IgnoreBots', '1'),
('ff', 'IgnoreBots', '1'),
('hidden', 'IgnoreBots', '1'),
('hl2mp', 'IgnoreBots', '1'),
('insmod', 'IgnoreBots', '1'),
('ns', 'IgnoreBots', '1'),
('tf', 'IgnoreBots', '1'),
('tfc', 'IgnoreBots', '1'),
('zps', 'IgnoreBots', '1'),
('aoc', 'MinimumPlayersRank', '0'),
('css', 'MinimumPlayersRank', '0'),
('cstrike', 'MinimumPlayersRank', '0'),
('dod', 'MinimumPlayersRank', '0'),
('dods', 'MinimumPlayersRank', '0'),
('ff', 'MinimumPlayersRank', '0'),
('hidden', 'MinimumPlayersRank', '0'),
('hl2mp', 'MinimumPlayersRank', '0'),
('insmod', 'MinimumPlayersRank', '0'),
('ns', 'MinimumPlayersRank', '0'),
('tf', 'MinimumPlayersRank', '0'),
('tfc', 'MinimumPlayersRank', '0'),
('zps', 'MinimumPlayersRank', '0'),
('aoc', 'MinPlayers', '4'),
('css', 'MinPlayers', '4'),
('cstrike', 'MinPlayers', '4'),
('dod', 'MinPlayers', '4'),
('dods', 'MinPlayers', '4'),
('ff', 'MinPlayers', '4'),
('hidden', 'MinPlayers', '4'),
('hl2mp', 'MinPlayers', '4'),
('insmod', 'MinPlayers', '4'),
('ns', 'MinPlayers', '4'),
('tf', 'MinPlayers', '4'),
('tfc', 'MinPlayers', '4'),
('zps', 'MinPlayers', '4'),
('aoc', 'PlayerEvents', '1'),
('css', 'PlayerEvents', '1'),
('cstrike', 'PlayerEvents', '1'),
('dod', 'PlayerEvents', '1'),
('dods', 'PlayerEvents', '1'),
('ff', 'PlayerEvents', '1'),
('hidden', 'PlayerEvents', '1'),
('hl2mp', 'PlayerEvents', '1'),
('insmod', 'PlayerEvents', '1'),
('ns', 'PlayerEvents', '1'),
('tf', 'PlayerEvents', '1'),
('tfc', 'PlayerEvents', '1'),
('zps', 'PlayerEvents', '1'),
('aoc', 'ShowStats', '1'),
('css', 'ShowStats', '1'),
('cstrike', 'ShowStats', '1'),
('dod', 'ShowStats', '1'),
('dods', 'ShowStats', '1'),
('ff', 'ShowStats', '1'),
('hidden', 'ShowStats', '1'),
('hl2mp', 'ShowStats', '1'),
('insmod', 'ShowStats', '1'),
('ns', 'ShowStats', '1'),
('tf', 'ShowStats', '1'),
('tfc', 'ShowStats', '1'),
('zps', 'ShowStats', '1'),
('aoc', 'SkillMode', '0'),
('css', 'SkillMode', '0'),
('cstrike', 'SkillMode', '0'),
('dod', 'SkillMode', '0'),
('dods', 'SkillMode', '0'),
('ff', 'SkillMode', '0'),
('hidden', 'SkillMode', '0'),
('hl2mp', 'SkillMode', '0'),
('insmod', 'SkillMode', '0'),
('ns', 'SkillMode', '0'),
('tf', 'SkillMode', '0'),
('tfc', 'SkillMode', '0'),
('zps', 'SkillMode', '0'),
('aoc', 'SuicidePenalty', '5'),
('css', 'SuicidePenalty', '5'),
('cstrike', 'SuicidePenalty', '5'),
('dod', 'SuicidePenalty', '5'),
('dods', 'SuicidePenalty', '5'),
('ff', 'SuicidePenalty', '5'),
('hidden', 'SuicidePenalty', '5'),
('hl2mp', 'SuicidePenalty', '5'),
('insmod', 'SuicidePenalty', '5'),
('ns', 'SuicidePenalty', '5'),
('tf', 'SuicidePenalty', '5'),
('tfc', 'SuicidePenalty', '5'),
('zps', 'SuicidePenalty', '5'),
('aoc', 'SwitchAdmins', '0'),
('css', 'SwitchAdmins', '0'),
('cstrike', 'SwitchAdmins', '0'),
('dod', 'SwitchAdmins', '0'),
('dods', 'SwitchAdmins', '0'),
('ff', 'SwitchAdmins', '0'),
('hidden', 'SwitchAdmins', '0'),
('hl2mp', 'SwitchAdmins', '0'),
('insmod', 'SwitchAdmins', '0'),
('ns', 'SwitchAdmins', '0'),
('tf', 'SwitchAdmins', '0'),
('tfc', 'SwitchAdmins', '0'),
('zps', 'SwitchAdmins', '0'),
('aoc', 'TKPenalty', '25'),
('css', 'TKPenalty', '25'),
('cstrike', 'TKPenalty', '25'),
('dod', 'TKPenalty', '25'),
('dods', 'TKPenalty', '25'),
('ff', 'TKPenalty', '25'),
('hidden', 'TKPenalty', '25'),
('hl2mp', 'TKPenalty', '25'),
('insmod', 'TKPenalty', '25'),
('ns', 'TKPenalty', '25'),
('tf', 'TKPenalty', '25'),
('tfc', 'TKPenalty', '25'),
('zps', 'TKPenalty', '25'),
('aoc', 'TrackServerLoad', '1'),
('css', 'TrackServerLoad', '1'),
('cstrike', 'TrackServerLoad', '1'),
('dod', 'TrackServerLoad', '1'),
('dods', 'TrackServerLoad', '1'),
('ff', 'TrackServerLoad', '1'),
('hidden', 'TrackServerLoad', '1'),
('hl2mp', 'TrackServerLoad', '1'),
('insmod', 'TrackServerLoad', '1'),
('ns', 'TrackServerLoad', '1'),
('tf', 'TrackServerLoad', '1'),
('tfc', 'TrackServerLoad', '1'),
('zps', 'TrackServerLoad', '1');

CREATE TABLE IF NOT EXISTS `hlstats_Games_Supported` (
  `code` varchar(32) NOT NULL,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY  (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `hlstats_Games_Supported` (`code`, `name`) VALUES
('css', 'Counter-Strike: Source'),
('hl2mp', 'Half-Life 2 Multiplayer'),
('tf', 'Team Fortress 2'),
('dods', 'Day of Defeat: Source'),
('insmod', 'Insurgency'),
('ff', 'Fortress Forever'),
('hidden', 'Hidden: Source'),
('zps', 'Zombie Panic: Source'),
('aoc', 'Age of Chivalry'),
('cstrike', 'Counter-Strike 1.6'),
('tfc', 'Team Fortress Classic'),
('dod', 'Day of Defeat'),
('ns', 'Natural Selection');

ALTER TABLE `hlstats_Livestats`
CHANGE COLUMN `name` `name` varchar(128) NOT NULL;

CREATE TABLE IF NOT EXISTS `hlstats_Mods_Defaults` (
  `code` varchar(32) NOT NULL,
  `parameter` varchar(50) NOT NULL,
  `value` varchar(128) NOT NULL,
  PRIMARY KEY  (`code`,`parameter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `hlstats_Mods_Defaults` (`code`, `parameter`, `value`) VALUES
('', 'BroadCastEventsCommand', ''),
('AMXX', 'BroadCastEventsCommand', 'hlx_amx_psay'),
('BEETLE', 'BroadCastEventsCommand', 'hlx_psay'),
('MANI', 'BroadCastEventsCommand', 'ma_hlx_psay'),
('MINISTATS', 'BroadCastEventsCommand', 'ms_psay'),
('SOURCEMOD', 'BroadCastEventsCommand', 'hlx_sm_psay'),
('', 'BroadCastEventsCommandAnnounce', 'say'),
('AMXX', 'BroadCastEventsCommandAnnounce', 'hlx_amx_csay'),
('BEETLE', 'BroadCastEventsCommandAnnounce', 'hlx_csay'),
('MANI', 'BroadCastEventsCommandAnnounce', 'ma_hlx_csay'),
('MINISTATS', 'BroadCastEventsCommandAnnounce', 'ms_csay'),
('SOURCEMOD', 'BroadCastEventsCommandAnnounce', 'hlx_sm_csay'),
('', 'PlayerEventsAdminCommand', ''),
('AMXX', 'PlayerEventsAdminCommand', ''),
('BEETLE', 'PlayerEventsAdminCommand', ''),
('MANI', 'PlayerEventsAdminCommand', ''),
('MINISTATS', 'PlayerEventsAdminCommand', ''),
('SOURCEMOD', 'PlayerEventsAdminCommand', ''),
('', 'PlayerEventsCommand', ''),
('AMXX', 'PlayerEventsCommand', 'hlx_amx_psay'),
('BEETLE', 'PlayerEventsCommand', 'hlx_psay'),
('MANI', 'PlayerEventsCommand', 'ma_hlx_psay'),
('MINISTATS', 'PlayerEventsCommand', 'ms_psay'),
('SOURCEMOD', 'PlayerEventsCommand', 'hlx_sm_psay'),
('', 'PlayerEventsCommandOSD', ''),
('AMXX', 'PlayerEventsCommandOSD', 'hlx_amx_msay'),
('BEETLE', 'PlayerEventsCommandOSD', 'hlx_msay'),
('MANI', 'PlayerEventsCommandOSD', 'ma_hlx_msay'),
('MINISTATS', 'PlayerEventsCommandOSD', 'ms_msay'),
('SOURCEMOD', 'PlayerEventsCommandOSD', 'hlx_sm_msay');

CREATE TABLE IF NOT EXISTS `hlstats_Mods_Supported` (
  `code` varchar(32) NOT NULL,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY  (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `hlstats_Mods_Supported` (`code`, `name`) VALUES
('', '(none)'),
('SOURCEMOD', 'Sourcemod'),
('MANI', 'Mani Admin Mod >= 1.2'),
('BEETLE', 'BeetlesMod'),
('MINISTATS', 'MiniStats'),
('AMXX', 'AMX Mod X');


DELETE FROM hlstats_Options WHERE `keyname` in ('nav_countryclans','freetypeenabled','imgdir');
UPDATE hlstats_Options SET `keyname` = 'countrydata' WHERE `keyname` = 'show_flags';

INSERT IGNORE INTO `hlstats_Options` (`keyname`, `value`) VALUES
('show_google_map', '1'),
('google_map_key', ''),
('google_map_region', 'NORTH AMERICA'),
('google_map_type', 'HYBRID');


CREATE TABLE IF NOT EXISTS `hlstats_Options_Choices` (
  `keyname` varchar(128) NOT NULL,
  `value` varchar(128) NOT NULL,
  `text` varchar(128) NOT NULL default '',
  `isDefault` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`keyname`,`value`),
  KEY `keyname` (`keyname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT IGNORE INTO `hlstats_Options_Choices` (`keyname`, `value`, `text`, `isDefault`) VALUES
('rankingtype', '1', 'Skill', 1),
('rankingtype', '2', 'Kills', 0),
('bannerdisplay', '0', 'None', 1),
('bannerdisplay', '1', 'All Pages', 0),
('bannerdisplay', '2', 'Contents Page Only', 0),
('playerinfo_tabs', '1', 'New Style (hide sections by default)', 1),
('playerinfo_tabs', '0', 'Old Style (show all at once)', 0),
('nav_globalchat', '1', 'Show', 1),
('nav_globalchat', '0', 'Hide', 0),
('nav_cheaters', '0', 'Hide', 1),
('nav_cheaters', '1', 'Show', 0),
('show_weapon_target_flash', '1', 'Flash hitbox', 1),
('show_weapon_target_flash', '0', 'HTML Table', 0),
('show_server_load_image', '1', 'Show', 0),
('show_server_load_image', '0', 'Hide', 1),
('countrydata', '1', 'Show', 1),
('countrydata', '0', 'Hide', 0),
('gamehome_show_awards', '1', 'Show', 0),
('gamehome_show_awards', '0', 'Hide', 1),
('show_google_map', '0', 'Hide', 0),
('show_google_map', '1', 'Show', 1),
('google_map_region', 'NORTH AMERICA', 'North America', 1),
('google_map_region', 'SOUTH AMERICA', 'South America', 0),
('google_map_region', 'NORTH AFRICA', 'North Africa', 0),
('google_map_region', 'SOUTH AFRICA', 'South Africa', 0),
('google_map_region', 'NORTH EUROPE', 'North Europe', 0),
('google_map_region', 'EAST EUROPE', 'East Europe', 0),
('google_map_region', 'GERMANY', 'Germany', 0),
('google_map_region', 'FRANCE', 'France', 0),
('google_map_region', 'SPAIN', 'Spain', 0),
('google_map_region', 'UNITED KINGDOM', 'United Kingdom', 0),
('google_map_region', 'DENMARK', 'Denmark', 0),
('google_map_region', 'SWEDEN', 'Sweden', 0),
('google_map_region', 'NORWAY', 'Norway', 0),
('google_map_region', 'FINLAND', 'Finland', 0),
('google_map_region', 'NETHERLANDS', 'Netherlands', 0),
('google_map_region', 'BELGIUM', 'Belgium', 0),
('google_map_region', 'POLAND', 'Poland', 0),
('google_map_region', 'SUISSE', 'Suisse', 0),
('google_map_region', 'AUSTRIA', 'Austria', 0),
('google_map_region', 'ITALY', 'Italy', 0),
('google_map_region', 'TURKEY', 'Turkey', 0),
('google_map_region', 'BRAZIL', 'Brazil', 0),
('google_map_region', 'ARGENTINA', 'Argentina', 0),
('google_map_region', 'RUSSIA', 'Russia', 0),
('google_map_region', 'ASIA', 'Asia', 0),
('google_map_region', 'CHINA', 'China', 0),
('google_map_region', 'JAPAN', 'Japan', 0),
('google_map_region', 'SOUTH KOREA', 'South Korea', 0),
('google_map_region', 'AUSTRALIA', 'Australia', 0),
('google_map_region', 'WORLD', 'World', 0),
('google_map_type', 'HYBRID', 'Hybrid', 1),
('google_map_type', 'SATELLITE', 'Satellite', 0),
('google_map_type', 'MAP', 'Normal', 0),
('google_map_type', 'PHYSICAL', 'Physical', 0);

INSERT INTO `hlstats_PerlConfig` (`parameter`, `value`) VALUES
('SkillRatioCap', '0');

CREATE TABLE IF NOT EXISTS `hlstats_PerlConfig_Choices` (
  `keyname` varchar(128) NOT NULL,
  `value` varchar(128) NOT NULL,
  `text` varchar(128) NOT NULL,
  `isDefault` tinyint(1) default '0',
  PRIMARY KEY  (`keyname`,`value`),
  KEY `keyname` (`keyname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `hlstats_PerlConfig_Choices` (`keyname`, `value`, `text`, `isDefault`) VALUES
('DNSResolveIP', '1', 'Yes', 1),
('DNSResolveIP', '0', 'No', 0),
('Rcon', '1', 'Yes', 1),
('Rcon', '0', 'No', 0),
('RconIgnoreSelf', '0', 'No', 1),
('RconIgnoreSelf', '1', 'Yes', 0),
('RconRecord', '0', 'No', 1),
('RconRecord', '1', 'Yes', 0),
('Mode', 'Normal', 'Steam ID (recommended)', 1),
('Mode', 'NameTrack', 'Player Name', 0),
('Mode', 'LAN', 'IP Address', 0),
('UseTimestamp', '0', 'No', 1),
('UseTimestamp', '1', 'Yes', 0),
('AllowOnlyConfigServers', '0', 'No', 0),
('AllowOnlyConfigServers', '1', 'Yes', 1),
('TrackStatsTrend', '0', 'No', 0),
('TrackStatsTrend', '1', 'Yes', 1),
('GlobalBanning', '0', 'No', 1),
('GlobalBanning', '1', 'Yes', 0),
('LogChat', '0', 'No', 0),
('LogChat', '1', 'Yes', 1),
('LogChatAdmins', '0', 'No', 0),
('LogChatAdmins', '1', 'Yes', 1),
('GlobalChat', '0', 'None', 1),
('GlobalChat', '1', 'Broadcast to all', 0),
('GlobalChat', '2', 'Broadcast to admins', 0),
('SkillRatioCap', '0', 'No', 1),
('SkillRatioCap', '1', 'Yes', 0);

ALTER TABLE `hlstats_Players`
CHANGE COLUMN `lastName` `lastName` varchar(128) NOT NULL default '',
CHANGE COLUMN `city` `city` varchar(64) NOT NULL default '',
CHANGE COLUMN `state` `state` varchar(64) NOT NULL default '',
CHANGE COLUMN `country` `country` varchar(64) NOT NULL default '',
CHANGE COLUMN `fullName` `fullName` varchar(128) default NULL,
CHANGE COLUMN `email` `email` varchar(64) default NULL,
CHANGE COLUMN `homepage` `homepage` varchar(64) default NULL,
CHANGE COLUMN `game` `game` varchar(32) NOT NULL;

ALTER TABLE `hlstats_Players_Awards`
CHANGE COLUMN `game` `game` varchar(32) NOT NULL;

ALTER TABLE `hlstats_Players_History`
CHANGE COLUMN `game` `game` varchar(32) NOT NULL default '';

ALTER TABLE `hlstats_Players_Ribbons`
CHANGE COLUMN `game` `game` varchar(32) NOT NULL;

DROP TABLE `hlstats_Players_Sametime`;

ALTER TABLE `hlstats_PlayerUniqueIds`
CHANGE COLUMN `game` `game` varchar(32) NOT NULL default '';

TRUNCATE TABLE hlstats_Ranks;

ALTER TABLE `hlstats_Ranks`
CHANGE COLUMN `game` `game` varchar(32) NOT NULL;

INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'recruit', 0, 49, 'Recruit' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'private', 50, 99, 'Private' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'private-first-class', 100, 199, 'Private First Class' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'lance-corporal', 200, 299, 'Lance Corporal' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'corporal', 300, 399, 'Corporal' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'sergeant', 400, 499, 'Sergeant' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'staff-sergeant', 500, 599, 'Staff Sergeant' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'gunnery-sergeant', 600, 699, 'Gunnery Sergeant' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'master-sergeant', 700, 799, 'Master Sergeant' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'first-sergeant', 800, 899, 'First Sergeant' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'master-chief', 900, 999, 'Master Chief' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'sergeant-major', 1000, 1199, 'Sergeant Major' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'ensign', 1200, 1399, 'Ensign' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'third-lieutenant', 1400, 1599, 'Third Lieutenant' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'second-lieutenant', 1600, 1799, 'Second Lieutenant' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'first-lieutenant', 1800, 1999, 'First Lieutenant' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'captain', 2000, 2249, 'Captain' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'group-captain', 2250, 2499, 'Group Captain' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'senior-captain', 2500, 2749, 'Senior Captain' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'lieutenant-major', 2750, 2999, 'Lieutenant Major' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'major', 3000, 3499, 'Major' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'group-major', 3500, 3999, 'Group Major' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'lieutenant-commander', 4000, 4499, 'Lieutenant Commander' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'commander', 4500, 4999, 'Commander' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'group-commander', 5000, 5749, 'Group Commander' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'lieutenant-colonel', 5750, 6499, 'Lieutenant Colonel' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'colonel', 6500, 7249, 'Colonel' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'brigadier', 7250, 7999, 'Brigadier' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'brigadier-general', 8000, 8999, 'Brigadier General' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'major-general', 9000, 9999, 'Major General' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'lieutenant-general', 10000, 12499, 'Lieutenant General' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'general', 12500, 14999, 'General' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'commander-general', 15000, 17499, 'Commander General' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'field-vice-marshal', 17500, 19999, 'Field Vice Marshal' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'field-marshal', 20000, 22499, 'Field Marshal' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'vice-commander-of-the-army', 22500, 24999, 'Vice Commander of the Army' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'commander-of-the-army', 25000, 27499, 'Commander of the Army' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'high-commander', 27500, 29999, 'High Commander' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'supreme-commander', 30000, 34999, 'Supreme Commander' FROM `hlstats_Games`);
INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`)
(SELECT `code`, 'terminator', 35000, 9999999, 'Terminator' FROM `hlstats_Games`);

ALTER TABLE `hlstats_Ribbons`
CHANGE COLUMN `game` `game` varchar(32) NOT NULL;

DELETE FROM `hlstats_Ribbons` WHERE `special` = 3 OR `game` = 'insmod' OR (`game`='css' AND `special`='0');

INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES
('*total connection hours*',168,2,'insmod','1waward.png','Connection Time 1 Week '),
('weapon_makarov',5,0,'insmod','rs_makarov.png','Silver Makarov'),
('weapon_m9',5,0,'insmod','rs_m9.png','Silver 9mm Beretta'),
('world',5,0,'insmod','rs_world.png','Silver RPG or Grenade'),
('weapon_sks',5,0,'insmod','rs_sks.png','Silver Simonov SKS carbine'),
('weapon_m1014',5,0,'insmod','rs_m1014.png','Silver M1014 Shotgun'),
('weapon_toz',5,0,'insmod','rs_toz.png','Silver TOZ Rifle'),
('weapon_svd',5,0,'insmod','rs_svd.png','Silver Dragunov Sniper Rifle'),
('weapon_rpk',5,0,'insmod','rs_rpk.png','Silver RPK'),
('weapon_m249',5,0,'insmod','rs_m249.png','Silver M249 SAW'),
('weapon_m16m203',5,0,'insmod','rs_m16m203.png','Silver M16 & M203'),
('weapon_l42a1',5,0,'insmod','rs_l42a1.png','Silver Enfield L42A1 Sniper Rifle'),
('weapon_m4med',5,0,'insmod','rs_m4med.png','Silver M4 Medium Range Rifle'),
('weapon_m4',5,0,'insmod','rs_m4.png','Silver M4 Carbine Rifle'),
('weapon_m16a4',5,0,'insmod','rs_m16a4.png','Silver M16A4 Infantry Rifle'),
('weapon_m14',5,0,'insmod','rs_m14.png','Silver M14 Infantry Rifle'),
('weapon_fnfa1',5,0,'insmod','rs_fnfal.png','Silver FN FAL Automatic Rifle'),
('weapon_aks74u',5,0,'insmod','rs_aks74u.png','Silver AKS-74U'),
('weapon_ak47',5,0,'insmod','rs_ak47.png','Silver AK-47'),
('weapon_makarov',10,0,'insmod','rg_makarov.png','Gold Makarov'),
('weapon_m9',10,0,'insmod','rg_m9.png','Gold 9mm Beretta'),
('world',10,0,'insmod','rg_world.png','Gold RPG or Grenade'),
('weapon_sks',10,0,'insmod','rg_sks.png','Gold Simonov SKS carbine'),
('weapon_m1014',10,0,'insmod','rg_m1014.png','Gold M1014 Shotgun'),
('weapon_toz',10,0,'insmod','rg_toz.png','Gold TOZ Rifle'),
('weapon_svd',10,0,'insmod','rg_svd.png','Gold Dragunov Sniper Rifle'),
('weapon_rpk',10,0,'insmod','rg_rpk.png','Gold RPK'),
('weapon_m249',10,0,'insmod','rg_m249.png','Gold M249 SAW'),
('weapon_m16m203',10,0,'insmod','rg_m16m203.png','Gold M16 & M203'),
('weapon_l42a1',10,0,'insmod','rg_l42a1.png','Gold Enfield L42A1 Sniper Rifle'),
('weapon_m4med',10,0,'insmod','rg_m4med.png','Gold M4 Medium Range Rifle'),
('weapon_m4',10,0,'insmod','rg_m4.png','Gold M4 Carbine Rifle'),
('weapon_m16a4',10,0,'insmod','rg_m16a4.png','Gold M16A4 Infantry Rifle'),
('weapon_m14',10,0,'insmod','rg_m14.png','Gold M14 Infantry Rifle'),
('weapon_fnfa1',10,0,'insmod','rg_fnfal.png','Gold FN FAL Automatic Rifle'),
('weapon_aks74u',10,0,'insmod','rg_aks74u.png','Gold AKS-74U'),
('weapon_ak47',10,0,'insmod','rg_ak47.png','Gold AK-47'),
('ak47',1,0,'css','1_ak47.png','Award of AK47'),
('awp',1,0,'css','1_awp.png','Award of AWP Sniper'),
('deagle',1,0,'css','1_deagle.png','Award of Desert Eagle'),
('elite',1,0,'css','1_elite.png','Award of Dual Beretta Elites'),
('glock',1,0,'css','1_glock.png','Award of Glock'),
('hegrenade',1,0,'css','1_hegrenade.png','Award of HE Grenades'),
('knife',1,0,'css','1_knife.png','Award of Combat Knife'),
('p90',1,0,'css','1_p90.png','Award of P90'),
('scout',1,0,'css','1_scout.png','Award of Scout Elite'),
('usp',1,0,'css','1_usp.png','Award of USP'),
('aug',1,0,'css','1_aug.png','Award of Steyr Aug'),
('fiveseven',1,0,'css','1_fiveseven.png','Award of Five-Seven'),
('g3sg1',1,0,'css','1_g3sg1.png','Award of G3 SG1'),
('m3',1,0,'css','1_m3.png','Award of M3 Shotgun'),
('m4a1',1,0,'css','1_m4a1.png','Award of Colt M4A1'),
('mac10',1,0,'css','1_mac10.png','Award of MAC 10'),
('mp5navy',1,0,'css','1_mp5navy.png','Award of MP5 Navy'),
('m249',1,0,'css','1_m249.png','Award of M249 Para'),
('sg550',1,0,'css','1_sg550.png','Award of SG 550'),
('sg552',1,0,'css','1_sg552.png','Award of SG 552'),
('p228',1,0,'css','1_p228.png','Award of Sig P228'),
('tmp',1,0,'css','1_tmp.png','Award of TMP'),
('ump45',1,0,'css','1_ump45.png','Award of UMP 45'),
('xm1014',1,0,'css','1_xm1014.png','Award of XM Shotgun'),
('ak47',5,0,'css','2_ak47.png','Bronze AK47'),
('awp',5,0,'css','2_awp.png','Bronze AWP Sniper'),
('deagle',5,0,'css','2_deagle.png','Bronze Desert Eagle'),
('elite',5,0,'css','2_elite.png','Bronze Dual Beretta Elites'),
('glock',5,0,'css','2_glock.png','Bronze Glock'),
('hegrenade',5,0,'css','2_hegrenade.png','Bronze HE Grenades'),
('knife',5,0,'css','2_knife.png','Bronze Combat Knife'),
('p90',5,0,'css','2_p90.png','Bronze P90'),
('scout',5,0,'css','2_scout.png','Bronze Scout Elite'),
('usp',5,0,'css','2_usp.png','Bronze USP'),
('aug',5,0,'css','2_aug.png','Bronze Steyr Aug'),
('fiveseven',5,0,'css','2_fiveseven.png','Bronze Five-Seven'),
('g3sg1',5,0,'css','2_g3sg1.png','Bronze G3 SG1'),
('m3',5,0,'css','2_m3.png','Bronze M3 Shotgun'),
('m4a1',5,0,'css','2_m4a1.png','Bronze Colt M4A1'),
('mac10',5,0,'css','2_mac10.png','Bronze MAC 10'),
('mp5navy',5,0,'css','2_mp5navy.png','Bronze MP5 Navy'),
('m249',5,0,'css','2_m249.png','Bronze M249 Para'),
('sg550',5,0,'css','2_sg550.png','Bronze SG 550'),
('sg552',5,0,'css','2_sg552.png','Bronze SG 552'),
('p228',5,0,'css','2_p228.png','Bronze Sig P228'),
('tmp',5,0,'css','2_tmp.png','Bronze TMP'),
('ump45',5,0,'css','2_ump45.png','Bronze UMP 45'),
('xm1014',5,0,'css','2_xm1014.png','Bronze XM Shotgun'),
('ak47',12,0,'css','3_ak47.png','Silver AK47'),
('awp',12,0,'css','3_awp.png','Silver AWP Sniper'),
('deagle',12,0,'css','3_deagle.png','Silver Desert Eagle'),
('elite',12,0,'css','3_elite.png','Silver Dual Beretta Elites'),
('glock',12,0,'css','3_glock.png','Silver Glock'),
('hegrenade',12,0,'css','3_hegrenade.png','Silver HE Grenades'),
('knife',12,0,'css','3_knife.png','Silver Combat Knife'),
('p90',12,0,'css','3_p90.png','Silver P90'),
('scout',12,0,'css','3_scout.png','Silver Scout Elite'),
('usp',12,0,'css','3_usp.png','Silver USP'),
('aug',12,0,'css','3_aug.png','Silver Steyr Aug'),
('fiveseven',12,0,'css','3_fiveseven.png','Silver Five-Seven'),
('g3sg1',12,0,'css','3_g3sg1.png','Silver G3 SG1'),
('m3',12,0,'css','3_m3.png','Silver M3 Shotgun'),
('m4a1',12,0,'css','3_m4a1.png','Silver Colt M4A1'),
('mac10',12,0,'css','3_mac10.png','Silver MAC 10'),
('mp5navy',12,0,'css','3_mp5navy.png','Silver MP5 Navy'),
('m249',12,0,'css','3_m249.png','Silver M249 Para'),
('sg550',12,0,'css','3_sg550.png','Silver SG 550'),
('sg552',12,0,'css','3_sg552.png','Silver SG 552'),
('p228',12,0,'css','3_p228.png','Silver Sig P228'),
('tmp',12,0,'css','3_tmp.png','Silver TMP'),
('ump45',12,0,'css','3_ump45.png','Silver UMP 45'),
('xm1014',12,0,'css','3_xm1014.png','Silver XM Shotgun'),
('ak47',20,0,'css','4_ak47.png','Gold AK47'),
('awp',20,0,'css','4_awp.png','Gold AWP Sniper'),
('deagle',20,0,'css','4_deagle.png','Gold Desert Eagle'),
('elite',20,0,'css','4_elite.png','Gold Dual Beretta Elites'),
('glock',20,0,'css','4_glock.png','Gold Glock'),
('hegrenade',20,0,'css','4_hegrenade.png','Gold HE Grenades'),
('knife',20,0,'css','4_knife.png','Gold Combat Knife'),
('p90',20,0,'css','4_p90.png','Gold P90'),
('scout',20,0,'css','4_scout.png','Gold Scout Elite'),
('usp',20,0,'css','4_usp.png','Gold USP'),
('aug',20,0,'css','4_aug.png','Gold Steyr Aug'),
('fiveseven',20,0,'css','4_fiveseven.png','Gold Five-Seven'),
('g3sg1',20,0,'css','4_g3sg1.png','Gold G3 SG1'),
('m3',20,0,'css','4_m3.png','Gold M3 Shotgun'),
('m4a1',20,0,'css','4_m4a1.png','Gold Colt M4A1'),
('mac10',20,0,'css','4_mac10.png','Gold MAC 10'),
('mp5navy',20,0,'css','4_mp5navy.png','Gold MP5 Navy'),
('m249',20,0,'css','4_m249.png','Gold M249 Para'),
('sg550',20,0,'css','4_sg550.png','Gold SG 550'),
('sg552',20,0,'css','4_sg552.png','Gold SG 552'),
('p228',20,0,'css','4_p228.png','Gold Sig P228'),
('tmp',20,0,'css','4_tmp.png','Gold TMP'),
('ump45',20,0,'css','4_ump45.png','Gold UMP 45'),
('xm1014',20,0,'css','4_xm1014.png','Gold XM Shotgun'),
('ak47',30,0,'css','5_ak47.png','Platinum AK47'),
('awp',30,0,'css','5_awp.png','Platinum AWP Sniper'),
('deagle',30,0,'css','5_deagle.png','Platinum Desert Eagle'),
('elite',30,0,'css','5_elite.png','Platinum Dual Beretta Elites'),
('glock',30,0,'css','5_glock.png','Platinum Glock'),
('hegrenade',30,0,'css','5_hegrenade.png','Platinum HE Grenades'),
('knife',30,0,'css','5_knife.png','Platinum Combat Knife'),
('p90',30,0,'css','5_p90.png','Platinum P90'),
('scout',30,0,'css','5_scout.png','Platinum Scout Elite'),
('usp',30,0,'css','5_usp.png','Platinum USP'),
('aug',30,0,'css','5_aug.png','Platinum Steyr Aug'),
('fiveseven',30,0,'css','5_fiveseven.png','Platinum Five-Seven'),
('g3sg1',30,0,'css','5_g3sg1.png','Platinum G3 SG1'),
('m3',30,0,'css','5_m3.png','Platinum M3 Shotgun'),
('m4a1',30,0,'css','5_m4a1.png','Platinum Colt M4A1'),
('mac10',30,0,'css','5_mac10.png','Platinum MAC 10'),
('mp5navy',30,0,'css','5_mp5navy.png','Platinum MP5 Navy'),
('m249',30,0,'css','5_m249.png','Platinum M249 Para'),
('sg550',30,0,'css','5_sg550.png','Platinum SG 550'),
('sg552',30,0,'css','5_sg552.png','Platinum SG 552'),
('p228',30,0,'css','5_p228.png','Platinum Sig P228'),
('tmp',30,0,'css','5_tmp.png','Platinum TMP'),
('ump45',30,0,'css','5_ump45.png','Platinum UMP 45'),
('xm1014',30,0,'css','5_xm1014.png','Platinum XM Shotgun'),
('ak47',50,0,'css','6_ak47.png','Supreme AK47'),
('awp',50,0,'css','6_awp.png','Supreme AWP Sniper'),
('deagle',50,0,'css','6_deagle.png','Supreme Desert Eagle'),
('elite',50,0,'css','6_elite.png','Supreme Dual Beretta Elites'),
('glock',50,0,'css','6_glock.png','Supreme Glock'),
('hegrenade',50,0,'css','6_hegrenade.png','Supreme HE Grenades'),
('knife',50,0,'css','6_knife.png','Supreme Combat Knife'),
('p90',50,0,'css','6_p90.png','Supreme P90'),
('scout',50,0,'css','6_scout.png','Supreme Scout Elite'),
('usp',50,0,'css','6_usp.png','Supreme USP'),
('aug',50,0,'css','6_aug.png','Supreme Steyr Aug'),
('fiveseven',50,0,'css','6_fiveseven.png','Supreme Five-Seven'),
('g3sg1',50,0,'css','6_g3sg1.png','Supreme G3 SG1'),
('m3',50,0,'css','6_m3.png','Supreme M3 Shotgun'),
('m4a1',50,0,'css','6_m4a1.png','Supreme Colt M4A1'),
('mac10',50,0,'css','6_mac10.png','Supreme MAC 10'),
('mp5navy',50,0,'css','6_mp5navy.png','Supreme MP5 Navy'),
('m249',50,0,'css','6_m249.png','Supreme M249 Para'),
('sg550',50,0,'css','6_sg550.png','Supreme SG 550'),
('sg552',50,0,'css','6_sg552.png','Supreme SG 552'),
('p228',50,0,'css','6_p228.png','Supreme Sig P228'),
('tmp',50,0,'css','6_tmp.png','Supreme TMP'),
('ump45',50,0,'css','6_ump45.png','Supreme UMP 45'),
('xm1014',50,0,'css','6_xm1014.png','Supreme XM Shotgun'),
('Defused_The_Bomb',1,0,'css','1_defused_the_bomb.png','Award of Bomb Defuser'),
('Planted_The_Bomb',1,0,'css','1_planted_the_bomb.png','Award of Bomb Planter'),
('Rescued_A_Hostage',1,0,'css','1_rescued_a_hostage.png','Award of Hostage Rescuer'),
('Killed_A_Hostage',1,0,'css','1_killed_a_hostage.png','Award of Hostage Killer'),
('latency',1,0,'css','1_latency.png','Award of Lowpinger'),
('headshot',1,0,'css','1_headshot.png','Award of Headshots'),
('Defused_The_Bomb',5,0,'css','2_defused_the_bomb.png','Bronze Bomb Defuser'),
('Planted_The_Bomb',5,0,'css','2_planted_the_bomb.png','Bronze Bomb Planter'),
('Rescued_A_Hostage',5,0,'css','2_rescued_a_hostage.png','Bronze Hostage Rescuer'),
('Killed_A_Hostage',5,0,'css','2_killed_a_hostage.png','Bronze Hostage Killer'),
('latency',5,0,'css','2_latency.png','Bronze Lowpinger'),
('headshot',5,0,'css','2_headshot.png','Bronze Headshots'),
('Defused_The_Bomb',12,0,'css','3_defused_the_bomb.png','Silver Bomb Defuser'),
('Planted_The_Bomb',12,0,'css','3_planted_the_bomb.png','Silver Bomb Planter'),
('Rescued_A_Hostage',12,0,'css','3_rescued_a_hostage.png','Silver Hostage Rescuer'),
('Killed_A_Hostage',12,0,'css','3_killed_a_hostage.png','Silver Hostage Killer'),
('latency',12,0,'css','3_latency.png','Silver Lowpinger'),
('headshot',12,0,'css','3_headshot.png','Silver Headshots'),
('Defused_The_Bomb',20,0,'css','4_defused_the_bomb.png','Gold Bomb Defuser'),
('Planted_The_Bomb',20,0,'css','4_planted_the_bomb.png','Gold Bomb Planter'),
('Rescued_A_Hostage',20,0,'css','4_rescued_a_hostage.png','Gold Hostage Rescuer'),
('Killed_A_Hostage',20,0,'css','4_killed_a_hostage.png','Gold Hostage Killer'),
('latency',20,0,'css','4_latency.png','Gold Lowpinger'),
('headshot',20,0,'css','4_headshot.png','Gold Headshots'),
('Defused_The_Bomb',30,0,'css','5_defused_the_bomb.png','Platinum Bomb Defuser'),
('Planted_The_Bomb',30,0,'css','5_planted_the_bomb.png','Platinum Bomb Planter'),
('Rescued_A_Hostage',30,0,'css','5_rescued_a_hostage.png','Platinum Hostage Rescuer'),
('Killed_A_Hostage',30,0,'css','5_killed_a_hostage.png','Platinum Hostage Killer'),
('latency',30,0,'css','5_latency.png','Platinum Lowpinger'),
('headshot',30,0,'css','5_headshot.png','Platinum Headshots'),
('Defused_The_Bomb',50,0,'css','6_defused_the_bomb.png','Supreme Bomb Defuser'),
('Planted_The_Bomb',50,0,'css','6_planted_the_bomb.png','Supreme Bomb Planter'),
('Rescued_A_Hostage',50,0,'css','6_rescued_a_hostage.png','Supreme Hostage Rescuer'),
('Killed_A_Hostage',50,0,'css','6_killed_a_hostage.png','Supreme Hostage Killer'),
('latency',50,0,'css','6_latency.png','Supreme Lowpinger'),
('headshot',50,0,'css','6_headshot.png','Supreme Headshots');

ALTER TABLE `hlstats_Roles`
CHANGE COLUMN `game` `game` varchar(32) NOT NULL default 'valve',
CHANGE COLUMN `code` `code` varchar(64) NOT NULL default '',
CHANGE COLUMN `name` `name` varchar(128) NOT NULL default '';

INSERT IGNORE INTO `hlstats_Roles` (`game`, `code`, `name`, `hidden`) VALUES
('tfc','Scout','Scout','0'),
('tfc','Sniper','Sniper','0'),
('tfc','Soldier','Soldier','0'),
('tfc','Demoman','Demoman','0'),
('tfc','Medic','Medic','0'),
('tfc','HWGuy','HWGuy','0'),
('tfc','Pyro','Pyro','0'),
('tfc','Spy','Spy','0'),
('tfc','Engineer','Engineer','0'),
('tfc','Civilian','The Hunted','0'),
('dod','Random','Random','0'),
('dod','#class_allied_garand','American Rifleman','0'),
('dod','#class_allied_carbine','American Staff Sergeant','0'),
('dod','#class_allied_thompson','American Master Sergeant','0'),
('dod','#class_allied_grease','American Sergeant','0'),
('dod','#class_allied_sniper','American Sniper','0'),
('dod','#class_allied_heavy','American Support Infantry','0'),
('dod','#class_allied_mg','American Machine Gunner','0'),
('dod','#class_alliedpara_garand','American Para Rifleman','0'),
('dod','#class_alliedpara_carbine','American Para Staff Sergeant','0'),
('dod','#class_alliedpara_thompson','American Para Master Sergeant','0'),
('dod','#class_alliedpara_grease','American Para Sergeant','0'),
('dod','#class_alliedpara_spring','American Para Sniper','0'),
('dod','#class_alliedpara_bar','American Para Support Infantry','0'),
('dod','#class_alliedpara_30cal','American Para Machine Gunner','0'),
('dod','#class_brit_light','British Rifleman','0'),
('dod','#class_brit_medium','British Sergeant Major','0'),
('dod','#class_brit_sniper','British Marksman','0'),
('dod','#class_brit_heavy','British Gunner','0'),
('dod','#class_axis_kar98','Axis Grenadier','0'),
('dod','#class_axis_k43','Axis Stosstruppe','0'),
('dod','#class_axis_mp40','Axis Unteroffizier','0'),
('dod','#class_axis_mp44','Axis Sturmtruppe','0'),
('dod','#class_axis_sniper','Axis Scharfschütze','0'),
('dod','#class_axis_mg34','Axis MG34-Schütze','0'),
('dod','#class_axis_mg42','Axis MG42-Schütze','0'),
('dod','#class_axispara_kar98','Axis Para Grenadier','0'),
('dod','#class_axispara_k43','Axis Para Stosstruppe','0'),
('dod','#class_axispara_mp40','Axis Para Unteroffizier','0'),
('dod','#class_axispara_mp44','Axis Para Sturmtruppe','0'),
('dod','#class_axispara_scopedkar','Axis Para Scharfschütze','0'),
('dod','#class_axispara_fg42bipod','Axis Para Fg42-Zweinbein','0'),
('dod','#class_axispara_fg42scope','Axis Para Fg42-Zielfernrohr','0'),
('dod','#class_axispara_mg34','Axis Para MG34-Schütze','0'),
('dod','#class_axispara_mg42','Axis Para MG42-Schütze','0'),
('ns','soldier','Soldier','0'),
('ns','commander','Commander','0'),
('ns','skulk','Skulk','0'),
('ns','gorge','Gorge','0'),
('ns','lerk','Lerk','0'),
('ns','fade','Fade','0'),
('ns','onos','Onos','0'),
('ns','gestate','Gestate','1');

ALTER TABLE `hlstats_Servers`
CHANGE COLUMN `game` `game` varchar(32) NOT NULL default 'valve',
CHANGE COLUMN `publicaddress` `publicaddress` varchar(128) NOT NULL default '',
CHANGE COLUMN `rcon_password` `rcon_password` varchar(128) NOT NULL default '';

DELETE FROM `hlstats_Servers_Config` WHERE `parameter` in ('BroadCastEventsCommandSteamid','PlayerEventsCommandSteamid');
INSERT IGNORE INTO `hlstats_Servers_Config` (`serverId`, `parameter`, `value`)
(SELECT serverId, 'GameEngine','2' FROM hlstats_Servers);

DELETE FROM `hlstats_Servers_Config_Default` WHERE `parameter` in ('BroadCastEventsCommandSteamid','PlayerEventsCommandSteamid');
INSERT IGNORE INTO `hlstats_Servers_Config_Default` (`parameter`, `value`, `description`) VALUES
('GameEngine', '3', 'Game engine of game on this server:<UL>\r\n<LI>1 = HL1 (GoldSource).\r\n<LI>2 = HL2 (Source original).\r\n<LI>3 = HL2ep2 (Source OrangeBox)(default).</UL>');
UPDATE `hlstats_Servers_Config_Default` SET `value`='If enabled (1=on 0=off(default)) the player queries will displayed in the valve browser as small html files. (Not support in all game and admin mod combinations)' WHERE `parameter`='DisplayResultsInBrowser';
UPDATE `hlstats_Servers_Config_Default` SET `value`='If set to 1 (default) periodically stats are shown ingame with the broadcast-command.' WHERE `parameter`='ShowStats';

ALTER TABLE `hlstats_Teams`
CHANGE COLUMN `game` `game` varchar(32) NOT NULL default 'valve',
CHANGE COLUMN `code` `code` varchar(64) NOT NULL default '',
CHANGE COLUMN `name` `name` varchar(64) NOT NULL default '';

UPDATE hlstats_Teams SET `code` = 'Iraqi Insurgents' WHERE `code` = 'iraqi';
UPDATE hlstats_Teams SET `code` = 'U.S. Marines' WHERE `code` = 'usmarines';

INSERT IGNORE INTO `hlstats_Teams` (`game`, `code`, `name`, `hidden`, `playerlist_bgcolor`, `playerlist_color`, `playerlist_index`) VALUES
('cstrike','TERRORIST','Terrorist','0','#FFD5D5','#FF2D2D',1),
('cstrike','CT','Counter-Terrorist','0','#D2E8F7','#0080C0',2),
('cstrike','SPECTATOR','Spectator','0','#D5D5D5','#050505',0),
('tfc','1','Blue','0','#D2E8F7','#0080C0',1),
('tfc','2','Red','0','#FFD5D5','#FF2D2D',2),
('tfc','3','Yellow','0', '#F7FF89', '#808700', 3),
('tfc','4','Green','0', '#93FF89', '#4B8246', 4),
('tfc','#Hunted_team1','(Hunted) VIP','0','#FFD5D5','#FF2D2D',5),
('tfc','#Hunted_team2','(Hunted) Bodyguards','0','#FFD5D5','#FF2D2D',6),
('tfc','#Hunted_team3','(Hunted) Assassins','0','#D2E8F7','#0080C0',7),
('tfc','#Dustbowl_team1','Attackers','0','#D2E8F7','#0080C0',8),
('tfc','#Dustbowl_team2','Defenders','0','#FFD5D5','#FF2D2D',9),
('tfc','SPECTATOR','Spectator','0','#D5D5D5','#050505',0),
('dod','Allies','Allies','0','#C1FFC1','#006600',2),
('dod','Axis','Axis','0','#FFD5D5','#FF2D2D',1),
('dod','Spectators','Spectators','0','#D5D5D5','#050505',0),
('ns','alien1team','Aliens','0','#F7FF89','#808700',2),
('ns','marine1team','Marines','0','#D2E8F7','#0080C0',1);

ALTER TABLE `hlstats_Trend`
CHANGE COLUMN `game` `game` varchar(32) NOT NULL default '';

ALTER TABLE `hlstats_Weapons`
CHANGE COLUMN `game` `game` varchar(32) NOT NULL default 'valve',
CHANGE COLUMN `code` `code` varchar(64) NOT NULL default '',
CHANGE COLUMN `name` `name` varchar(128) NOT NULL default '';

UPDATE `hlstats_Weapons` SET `code` = 'Flamberge' WHERE `game` LIKE 'aoc%' AND `code` = 'flamberge';
UPDATE `hlstats_Weapons` SET `code` = 'Longsword' WHERE `game` LIKE 'aoc%' AND `code` = 'longsword';
UPDATE `hlstats_Weapons` SET `code` = 'Glaive', `name` = 'Glaive' WHERE `game` LIKE 'aoc%' AND `code` = 'halberd';
UPDATE `hlstats_Weapons` SET `code` = 'Dual Daggers' WHERE `game` LIKE 'aoc%' AND `code` = 'dagger';
UPDATE `hlstats_Weapons` SET `code` = 'Flamberge & Kite Shield' WHERE `game` LIKE 'aoc%' AND `code` = 'flamberge_kiteshield';
UPDATE `hlstats_Weapons` SET `code` = 'Warhammer' WHERE `game` LIKE 'aoc%' AND `code` = 'warhammer';
UPDATE `hlstats_Weapons` SET `code` = 'Mace' WHERE `game` LIKE 'aoc%' AND `code` = 'mace';
UPDATE `hlstats_Weapons` SET `code` = 'Mace & Buckler' WHERE `game` LIKE 'aoc%' AND `code` = 'mace_buckler';
UPDATE `hlstats_Weapons` SET `code` = 'Broadsword & Evil Shield' WHERE `game` LIKE 'aoc%' AND `code` = 'sword01_evil_shield';
UPDATE `hlstats_Weapons` SET `code` = 'Crossbow' WHERE `game` LIKE 'aoc%' AND `code` = 'crossbow';
UPDATE `hlstats_Weapons` SET `code` = 'Longbow' WHERE `game` LIKE 'aoc%' AND `code` = 'longbow';
UPDATE `hlstats_Weapons` SET `code` = 'Longsword & Kite Shield' WHERE `game` LIKE 'aoc%' AND `code` = 'longsword_kiteshield';
UPDATE `hlstats_Weapons` SET `code` = 'Broadsword & Good Shield' WHERE `game` LIKE 'aoc%' AND `code` = 'sword01_good_shield';
UPDATE `hlstats_Weapons` SET `code` = 'Hatchet' WHERE `game` LIKE 'aoc%' AND `code` = 'onehandaxe';
UPDATE `hlstats_Weapons` SET `code` = 'Double Axe' WHERE `game` LIKE 'aoc%' AND `code` = 'doubleaxe';
UPDATE `hlstats_Weapons` SET `code` = 'Flail & Evil Shield' WHERE `game` LIKE 'aoc%' AND `code` = 'flail_evil_shield';
UPDATE `hlstats_Weapons` SET `code` = 'Flail & Good Shield' WHERE `game` LIKE 'aoc%' AND `code` = 'flail_good_shield';
UPDATE `hlstats_Weapons` SET `code` = 'Javelin' WHERE `game` LIKE 'aoc%' AND `code` = 'thrown_spear';
UPDATE `hlstats_Weapons` SET `code` = 'Spiked Mace' WHERE `game` LIKE 'aoc%' AND `code` = 'shortsword';
UPDATE `hlstats_Weapons` SET `code` = 'Shortsword' WHERE `game` LIKE 'aoc%' AND `code` = 'sword2';
DELETE FROM `hlstats_Weapons` WHERE `game` LIKE 'aoc%' AND `code` = 'spear_buckler2';
UPDATE `hlstats_Weapons` a SET `code` = 'Spear & Buckler', `kills` = (SELECT count(*) FROM `hlstats_Events_Frags` WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` b WHERE b.game =a.game) AND `weapon` = 'Spear & Buckler'), `headshots` = (SELECT count(*) FROM `hlstats_Events_Frags` WHERE serverId IN (SELECT serverId FROM `hlstats_Servers` c WHERE a.game=c.game) AND `weapon` = 'Spear & Buckler' AND `headshot` = 1), `name` = 'Spear & Buckler' WHERE `game` LIKE 'aoc%' AND `code` = 'spear_buckler';
UPDATE `hlstats_Weapons` SET `code` = 'Spiked Mace & Buckler' WHERE `game` LIKE 'aoc%' AND `code` = 'spikedmace_buckler';
UPDATE `hlstats_Weapons` SET `code` = 'Dagger' WHERE `game` LIKE 'aoc%' AND `code` = 'dagger2';
UPDATE `hlstats_Weapons` SET `code` = 'Broadsword', `name` = 'Broadsword' WHERE `game` LIKE 'aoc%' AND `code` = 'mtest';
UPDATE `hlstats_Weapons` SET `code` = 'Throwing Knife', `name` = 'Throwing Knife' WHERE `game` LIKE 'aoc%' AND `code` = 'thrown_dagger2';
UPDATE `hlstats_Weapons` SET `code` = 'Halberd', `name` = 'Halberd' WHERE `game` LIKE 'aoc%' AND `code` = 'evil_halberd';
UPDATE `hlstats_Weapons` SET `code` = 'Oilpot' WHERE `game` LIKE 'aoc%' AND `code` = 'oilpot';

INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
('cstrike','knife','Bundeswehr Advanced Combat Knife',1.80),
('cstrike','usp','H&K USP .45 Tactical',1.50),
('cstrike','glock18','Glock 18 Select Fire',1.50),
('cstrike','deagle','Desert Eagle .50AE',1.50),
('cstrike','p228','Sig Sauer P-228',1.50),
('cstrike','m3','Benelli M3 Super 90 Combat',1.40),
('cstrike','xm1014','Benelli/H&K M4 Super 90 XM1014',1.40),
('cstrike','mp5navy','H&K MP5-Navy',1.25),
('cstrike','tmp','Steyr Tactical Machine Pistol',1.25),
('cstrike','p90','FN P90',1.25),
('cstrike','m4a1','Colt M4A1 Carbine',1.00),
('cstrike','ak47','Kalashnikov AK-47',1.00),
('cstrike','sg552','Sig Sauer SG-552 Commando',1.00),
('cstrike','scout','Steyr Scout',1.60),
('cstrike','awp','Arctic Warfare Magnum (Police)',1.40),
('cstrike','g3sg1','H&K G3/SG1 Sniper Rifle',1.40),
('cstrike','m249','M249 PARA Light Machine Gun',0.80),
('cstrike','grenade','High Explosive Grenade',1.80),
('cstrike','elite','Dual Beretta 96G Elite',1.50),
('cstrike','aug','Steyr Aug',1.00),
('cstrike','mac10','Ingram MAC-10',1.25),
('cstrike','fiveseven','FN Five-Seven',1.50),
('cstrike','ump45','H&K UMP45',1.25),
('cstrike','sg550','Sig SG-550 Sniper',1.70),
('cstrike','famas','Fusil Automatique',1.00),
('cstrike','galil','Galil',1.00),
('tfc','sniperrifle','Sniper Rifle',1.00),
('tfc','normalgrenade','Normal Grenade',1.10),
('tfc','ac','Autocannon',1.00),
('tfc','rocket','Rocket Launcher',1.00),
('tfc','sentrygun','Sentry Gun',1.00),
('tfc','supershotgun','Super Shotgun',1.15),
('tfc','autorifle','Sniper Rifle (Auto Mode)',1.20),
('tfc','empgrenade','EMP Grenade',1.25),
('tfc','mirvgrenade','MIRV Grenade',1.25),
('tfc','gl_grenade','Grenade Launcher',1.35),
('tfc','pipebomb','Pipebomb',1.35),
('tfc','timer','Infection Timer',0.00),
('tfc','infection','Infection',1.50),
('tfc','flames','Flame Thrower',1.60),
('tfc','shotgun','Shotgun',1.60),
('tfc','nails','Nail Gun',1.70),
('tfc','nailgrenade','Nail Grenade',1.70),
('tfc','supernails','Super Nail Gun',1.65),
('tfc','axe','Crowbar',1.80),
('tfc','medikit','Medikit',1.85),
('tfc','napalmgrenade','Napalm Grenade',1.70),
('tfc','detpack','Detpack',1.80),
('tfc','gasgrenade','Gas Grenade',1.90),
('tfc','spanner','Spanner',2.00),
('tfc','caltrop','Caltrops',2.00),
('tfc','railgun','Rail Gun',1.85),
('tfc','building_dispenser','Dispenser',2.00),
('dod', 'k43', 'Karbiner 43', '1.50'),
('dod', 'luger', 'Luger 08 Pistol', '1.50'),
('dod', 'kar', 'Mauser Kar 98k', '1.30'),
('dod', 'mp40', 'MP40 Machine Pistol', '1.25'),
('dod', 'scopedkar', 'Mauser Karbiner 98k Sniper Rifle', '1.50'),
('dod', 'mp44', 'MP44 Assault Rifle', '1.35'),
('dod', 'colt', 'Colt .45 model 1911', '1.60'),
('dod', 'garand', 'M1 Garand Rifle', '1.30'),
('dod', 'thompson', 'Thompson Submachine Gun', '1.25'),
('dod', 'spring', 'Springfield Rifle with Scope', '1.50'),
('dod', 'bar', 'BAR Browning Automatic Rifle', '1.20'),
('dod', 'grenade', 'U.S. Grenade', '1.00'),
('dod', 'enf_bayonet', 'Enfield Bayonet', '2.50'),
('dod', 'bren', 'Bren Machine Gun', '1.25'),
('dod', 'm1carbine', 'M1 Carbine', '1.20'),
('dod', 'greasegun', 'Greasegun', '1.30'),
('dod', '30cal', '.30 Caliber Machine Gun', '1.25'),
('dod', 'mg42', 'MG42 Machine Gun', '1.20'),
('dod', 'grenade2', 'German Grenade', '1.00'),
('dod', 'spade', 'Spade Entrenchment Tool', '3.00'),
('dod', 'gerknife', 'German Knife', '3.00'),
('dod', 'fg42', 'FG42 Paratroop Rifle', '1.25'),
('dod', 'world', 'worldspawn', '0.00'),
('dod', 'amerknife', 'U.S. Issue Knife', '3.00'),
('dod', 'bayonet', 'Karbiner Bayonet', '2.40'),
('dod', 'mg34', 'MG34 Machine Gun', '1.20'),
('dod', 'brit_knife', 'British Knife', '3.00'),
('dod', 'mortar', 'Mortar', '1.00'),
('dod', 'fcarbine', 'F1 Carbine', '1.35'),
('dod', 'scoped_fg42', 'Scoped FG42', '1.30'),
('dod', 'bazooka', 'Bazooka', '2.25'),
('dod', 'enfield', 'Enfield Rifle', '1.35'),
('dod', 'garandbutt', 'Butt Stock Hit', '3.00'),
('dod', 'mills_bomb', 'British Grenade', '1.00'),
('dod', 'piat', 'Piat', '2.25'),
('dod', 'pschreck', 'Panzerschreck', '2.25'),
('dod', 'scoped_enfield', 'Scoped Enfield', '1.50'),
('dod', 'sten', 'Sten Submachine Gun', '1.25'),
('dod', 'webley', 'Webley Revolver', '1.60'),
('ns','welder','Marine Welder','3.00'),
('ns','item_mine','Marine Mine','1.00'),
('ns','handgrenade','Marine Hand Grenade','1.00'),
('ns','grenade','Marine Grenade Launcher','1.00'),
('ns','knife','Marine Knife','4.00'),
('ns','pistol','Marine Pistol','2.00'),
('ns','machinegun','Marine Light Machine Gun','1.25'),
('ns','shotgun','Marine Shotgun','1.00'),
('ns','heavymachinegun','Marine Heavy Machine Gun','1.00'),
('ns','turret','Marine Turret','.75'),
('ns','siegeturret','Marine Siege Turret','1.00'),
('ns','resourcetower','Electrified Marine Resource Tower','2.00'),
('ns','team_turretfactor','Electric Marine Turret Factory','2.00'),
('ns','team_advturretfactor','Electrified Marine Advance Turret Factory','2.00'),
('ns','acidrocket','Fade Acid Rocket','1.00'),
('ns','bitegun','Skulk Bite','1.25'),
('ns','charge','Onos Charge','1.00'),
('ns','claws','Onos Gore','1.00'),
('ns','divinewind','Skulk Xenocide','1.00'),
('ns','leap','Skulk Leap','2.00'),
('ns','bite2gun','Lerk Bite','2.00'),
('ns','spitgunspit','Gorge Spit','2.00'),
('ns','sporegunprojectile','Lerk Spores','1.00'),
('ns','swipe','Fade Slash','1.00'),
('ns','healingspray','Gorge Health Spray','3.00'),
('ns','parasite','Skulk Parasite','3.00'),
('ns','devour','Onos Devour','2.00'),
('ns','offensechamber','Offense Chamber','1.00');