UPDATE `hlstats_Actions` SET `code` = 'flagevent_defended' WHERE `code` = 'flagevent' AND `event` = 'defended' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'tf');
UPDATE `hlstats_Actions` SET `code` = 'flagevent_captured' WHERE `code` = 'flagevent' AND `event` = 'captured' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'tf');
UPDATE `hlstats_Actions` SET `code` = 'flagevent_dropped', `description` = 'Dropped the flag (while alive)' WHERE `code` = 'flagevent' AND `event` = 'dropped' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'tf');
UPDATE `hlstats_Actions` SET `code` = 'flagevent_picked_up' WHERE `code` = 'flagevent' AND `event` = 'picked up' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'tf');
UPDATE `hlstats_Actions` SET `code` = 'killedobject_obj_teleporter_exit' WHERE `code` = 'killedobject' AND `object` = 'OBJ_TELEPORTER_EXIT' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'tf');
UPDATE `hlstats_Actions` SET `code` = 'killedobject_obj_teleporter_entrance' WHERE `code` = 'killedobject' AND `object` = 'OBJ_TELEPORTER_ENTRANCE' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'tf');
UPDATE `hlstats_Actions` SET `code` = 'killedobject_obj_dispenser' WHERE `code` = 'killedobject' AND `object` = 'OBJ_DISPENSER' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'tf');
UPDATE `hlstats_Actions` SET `code` = 'killedobject_obj_sentrygun' WHERE `code` = 'killedobject' AND `object` = 'OBJ_SENTRYGUN' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'tf');
UPDATE `hlstats_Actions` SET `code` = 'killedobject_obj_attachment_sapper' WHERE `code` = 'killedobject' AND `object` = 'OBJ_ATTACHMENT_SAPPER' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'tf');
UPDATE `hlstats_Actions` SET `code` = 'builtobject_obj_teleporter_exit' WHERE `code` = 'builtobject' AND `object` = 'OBJ_TELEPORTER_EXIT' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'tf');
UPDATE `hlstats_Actions` SET `code` = 'builtobject_obj_teleporter_entrance' WHERE `code` = 'builtobject' AND `object` = 'OBJ_TELEPORTER_ENTRANCE' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'tf');
UPDATE `hlstats_Actions` SET `code` = 'builtobject_obj_dispenser' WHERE `code` = 'builtobject' AND `object` = 'OBJ_DISPENSER' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'tf');
UPDATE `hlstats_Actions` SET `code` = 'builtobject_obj_sentrygun' WHERE `code` = 'builtobject' AND `object` = 'OBJ_SENTRYGUN' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'tf');
UPDATE `hlstats_Actions` SET `code` = 'builtobject_obj_attachment_sapper' WHERE `code` = 'builtobject' AND `object` = 'OBJ_ATTACHMENT_SAPPER' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'tf');
UPDATE `hlstats_Actions` SET `code` = 'owner_killedobject_obj_teleporter_exit' WHERE `code` = 'owner_killedobject' AND `object` = 'OBJ_TELEPORTER_EXIT' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'tf');
UPDATE `hlstats_Actions` SET `code` = 'owner_killedobject_obj_teleporter_entrance' WHERE `code` = 'owner_killedobject' AND `object` = 'OBJ_TELEPORTER_ENTRANCE' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'tf');
UPDATE `hlstats_Actions` SET `code` = 'owner_killedobject_obj_dispenser' WHERE `code` = 'owner_killedobject' AND `object` = 'OBJ_DISPENSER' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'tf');
UPDATE `hlstats_Actions` SET `code` = 'owner_killedobject_obj_sentrygun' WHERE `code` = 'owner_killedobject' AND `object` = 'OBJ_SENTRYGUN' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'tf');

ALTER TABLE `hlstats_Actions`
DROP KEY `gamecode`,
DROP COLUMN `object`,
DROP COLUMN `event`,
ADD UNIQUE KEY `gamecode` (`code`,`game`,`team`);

INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES
('tf', 'flagevent_dropped_death', 0, 0, '', 'Dropped the flag (on death)', '1', '', '', ''),
('tf', 'crit_kill', -1, 0, '', 'Critical Kill', '1', '', '', ''),
('tf', 'force_suicide', 0, 0, '', 'Force Suicide', '1', '', '', ''),
('tf', 'hit_by_train', 0, 0, '', 'LOL TRAIN\'D', '1', '', '', ''),
('tf', 'drowned', 0, 0, '', 'Drowned', '1', '', '', ''),
('tf', 'owner_killedobject_obj_attachment_sapper', -2, 0, '', 'Console-killed sapper', '1', '', '', ''),
('tfc','Sentry_Dismantle',3,0,'0','Dismantled Sentry Gun','0','1','0','0'),
('tfc','Dispenser_Dismantle',3,0,'0','Dismantled Dispenser','0','1','0','0'),
('l4d', 'headshot', 0, 0, '', 'Headshot Kill', '1', '', '', ''),
('l4d', 'rescued_survivor', 2, 0, '', 'Rescued Teammate', '1', '0', '0', '0'),
('l4d', 'healed_teammate', 5, 0, '', 'Healed Teammate', '1', '0', '0', '0'),
('l4d', 'revived_teammate', 3, 0, '', 'Revived Teammate', '1', '0', '0', '0'),
('l4d', 'startled_witch', -5, 0, '', 'Startled the Witch', '1', '0', '0', '0'),
('l4d', 'pounce', 6, 0, '', '(Hunter) Pounced on Survivor', '0', '1', '0', '0'),
('l4d', 'tongue_grab', 6, 0, '', '(Smoker) Tongue Grabbed Survivor', '0', '1', '0', '0'),
('l4d', 'vomit', 6, 0, '', '(Boomer) Vomited on Survivor', '0', '1', '0', '0'),
('l4d', 'killed_gas', 1, 0, '', 'Killed a Smoker', '1', '0', '0', '0'),
('l4d', 'killed_exploding', 1, 0, '', 'Killed a Boomer', '1', '0', '0', '0'),
('l4d', 'killed_hunter', 1, 0, '', 'Killed a Hunter', '1', '0', '0', '0'),
('l4d', 'killed_tank', 3, 0, '', 'Killed a Tank', '1', '0', '0', '0'),
('l4d', 'killed_witch', 3, 0, '', 'Killed a Witch', '1', '0', '0', '0'),
('l4d', 'killed_survivor', 25, 0, '', 'Incapacitated/Killed Survivor', '0', '1', '0', '0'),
('l4d', 'friendly_fire', -10, 0, '', 'Friendly Fire', '1', '0', '0', '0'),
('ff', 'sentrygun_upgraded', 1, 0, '', 'Upgraded Sentry Gun', '1', '', '', ''),
('ff', 'build_sentrygun', 1, 0, '', 'Built Sentry Gun', '1', '', '', ''),
('ff', 'build_dispenser', 1, 0, '', 'Built Dispenser', '1', '', '', ''),
('ff', 'dispenser_detonated', -1, 0, '', 'Dispenser Detonated', '1', '', '', ''),
('ff', 'sentry_detonated', -1, 0, '', 'Sentry Gun Detonated', '1', '', '', ''),
('ff', 'sentry_dismantled', -1, 0, '', 'Sentry Gun Dismantled', '1', '', '', ''),
('ff', 'dispenser_dismantled', -1, 0, '', 'Dispenser Dismantled', '1', '', '', ''),
('ff', 'build_mancannon', 1, 0, '', 'Built Jump Pad', '1', '', '', ''),
('ff', 'mancannon_detonated', -1, 0, '', 'Detonated Jump Pad', '1', '', '', ''),
('ff', 'build_detpack', 1, 0, '', 'Placed Detpack', '1', '', '', ''),
('ff', 'flag_touch', 3, 0, '', 'Flag Picked Up', '1', '', '', ''),
('ff', 'flag_capture', 3, 0, '', 'Flag Captured', '1', '', '', ''),
('ff', 'flag_dropped', -3, 0, '', 'Flag Dropped', '1', '', '', ''),
('ff', 'flag_thrown', -3, 0, '', 'Flag Thrown', '1', '', '', ''),
('ff', 'disguise_lost', 1, 0, '', 'Uncovered Enemy', '', '1', '', ''),
('fof', 'kill_streak_2', 1, 0, '', 'Double Kill (2 kills)', '1', '', '', ''),
('fof', 'kill_streak_3', 2, 0, '', 'Triple Kill (3 kills)', '1', '', '', ''),
('fof', 'kill_streak_4', 3, 0, '', 'Domination (4 kills)', '1', '', '', ''),
('fof', 'kill_streak_5', 4, 0, '', 'Rampage (5 kills)', '1', '', '', ''),
('fof', 'kill_streak_6', 5, 0, '', 'Mega Kill (6 kills)', '1', '', '', ''),
('fof', 'kill_streak_7', 6, 0, '', 'Ownage (7 kills)', '1', '', '', ''),
('fof', 'kill_streak_8', 7, 0, '', 'Ultra Kill (8 kills)', '1', '', '', ''),
('fof', 'kill_streak_9', 8, 0, '', 'Killing Spree (9 kills)', '1', '', '', ''),
('fof', 'kill_streak_10', 9, 0, '', 'Monster Kill (10 kills)', '1', '', '', ''),
('fof', 'kill_streak_11', 10, 0, '', 'Unstoppable (11 kills)', '1', '', '', ''),
('fof', 'kill_streak_12', 11, 0, '', 'God Like (12+ kills)', '1', '', '', ''),
('fof', 'loot_drop', -2, 0, '', 'Dropped the loot', '1', '', '', ''),
('fof', 'loot_capture', 8, 0, '', 'Captured the loot', '1', '', '', ''),
('fof', 'carrier_protect', 5, 0, '', 'Protected the carrier', '1', '', '', ''),
('fof', 'headshot', 1, 0, '', 'Headshot Kill', '1', '', '', ''),
('ges', 'kill_streak_2', 1, 0, '', 'Double Kill (2 kills)', '1', '', '', ''),
('ges', 'kill_streak_3', 2, 0, '', 'Triple Kill (3 kills)', '1', '', '', ''),
('ges', 'kill_streak_4', 3, 0, '', 'Domination (4 kills)', '1', '', '', ''),
('ges', 'kill_streak_5', 4, 0, '', 'Rampage (5 kills)', '1', '', '', ''),
('ges', 'kill_streak_6', 5, 0, '', 'Mega Kill (6 kills)', '1', '', '', ''),
('ges', 'kill_streak_7', 6, 0, '', 'Ownage (7 kills)', '1', '', '', ''),
('ges', 'kill_streak_8', 7, 0, '', 'Ultra Kill (8 kills)', '1', '', '', ''),
('ges', 'kill_streak_9', 8, 0, '', 'Killing Spree (9 kills)', '1', '', '', ''),
('ges', 'kill_streak_10', 9, 0, '', 'Monster Kill (10 kills)', '1', '', '', ''),
('ges', 'kill_streak_11', 10, 0, '', 'Unstoppable (11 kills)', '1', '', '', ''),
('ges', 'kill_streak_12', 11, 0, '', 'God Like (12+ kills)', '1', '', '', ''),
('ges', 'Round_Win', 5, 0, '', 'Round Win', '1', '', '', ''),
('ges', 'Round_Win_Team', 0, 3, '', 'Team Round Win', '', '', '1', '');

ALTER TABLE `hlstats_Awards` CHANGE `awardType` `awardType` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'W';

UPDATE `hlstats_Awards` SET `code` = 'weapon_fnfal' WHERE `code`='weapon_fnfa1' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'insmod');

INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
('O', 'l4d', 'headshot', 'Brain Salad', 'headshot kills'),
('O', 'l4d', 'healed_teammate', 'Field Medic', 'healed Survivors'),
('O', 'l4d', 'killed_exploding', 'Stomach Upset', 'killed Boomers'),
('O', 'l4d', 'killed_gas', 'No Smoking Section', 'killed Smokers'),
('O', 'l4d', 'killed_hunter', 'Hunter Punter', 'killed Hunters'),
('P', 'l4d', 'killed_survivor', 'Dead Wreckening', 'downed Survivors'),
('O', 'l4d', 'killed_tank', 'Tankbuster', 'killed Tanks'),
('O', 'l4d', 'killed_witch', 'Inquisitor', 'killed Witches'),
('P', 'l4d', 'pounce', 'Free to Fly', 'pounced Survivors'),
('O', 'l4d', 'rescued_survivor', 'Ground Cover', 'rescued Survivors'),
('O', 'l4d', 'revived_teammate', 'Helping Hand', 'revived Survivors'),
('P', 'l4d', 'tongue_grab', 'Drag &amp; Drop', 'constricted Survivors'),
('P', 'l4d', 'vomit', 'Barf Bagged', 'vomited on Survivors'),
('W', 'l4d', 'autoshotgun', 'Automation', 'kills with Auto Shotgun'),
('W', 'l4d', 'boomer_claw', 'Boom!', 'kills with Boomer\'s Claws'),
('W', 'l4d', 'dual_pistols', 'Akimbo Assassin', 'kills with Dual Pistols'),
('W', 'l4d', 'hunter_claw', 'Open Season', 'kills with Hunter\'s Claws'),
('W', 'l4d', 'hunting_rifle', 'Hawk Eye', 'kills with Hunting Rifle'),
('W', 'l4d', 'inferno', 'Pyromaniac', 'cremated Infected'),
('W', 'l4d', 'pipe_bomb', 'Pyrotechnician', 'blown up Infected'),
('W', 'l4d', 'pistol', 'Ammo Saver', 'kills with Pistol'),
('W', 'l4d', 'prop_minigun', 'No-One Left Behind', 'kills with Mounted Machine Gun'),
('W', 'l4d', 'pumpshotgun', 'Pump It!', 'kills with Pump Shotgun'),
('W', 'l4d', 'rifle', 'Commando', 'kills with M16 Assault Rifle'),
('W', 'l4d', 'smg', 'Safety First', 'kills with Uzi'),
('W', 'l4d', 'smoker_claw', 'Chain Smoker', 'kills with Smoker\'s Claws'),
('W', 'l4d', 'tank_claw', 'Burger Tank', 'kills with Tank\'s Claws'),
('W', 'l4d', 'tank_rock', 'Rock Star', 'kills with Tank\'s Rock'),
('W', 'l4d', 'latency', 'Lowest Ping', 'ms average connection'),
('W', 'fof', 'deringer', 'Deringer', 'kills with Deringer'),
('W', 'fof', 'carbine', 'Carbine', 'kills with Carbine'),
('W', 'fof', 'coltnavy', 'Colt Navy', 'kills with Colt Navy'),
('W', 'fof', 'bow', 'Bow', 'kills with Bow'),
('W', 'fof', 'arrow', 'Arrow', 'kills with Arrow'),
('W', 'fof', 'sharps', 'Sharps', 'kills with Sharps'),
('W', 'fof', 'coachgun', 'Coach Gun', 'kills with Coach Gun'),
('W', 'fof', 'peacemaker', 'Peacemaker', 'kills with Peacemaker'),
('W', 'fof', 'knife', 'Knife', 'kills with Knife'),
('W', 'fof', 'dualderinger', 'Dual Deringers', 'kills with Dual Deringers'),
('W', 'fof', 'thrown_axe', 'Thrown Axe', 'kills with Thrown Axe'),
('W', 'fof', 'arrow_fiery', 'Fire Arrow', 'kills with Fire Arrow'),
('W', 'fof', 'thrown_knife', 'Thrown Knife', 'kills with Thrown Knife'),
('W', 'fof', 'dualnavy', 'Dual Colt Navys', 'kills with Dual Colt Navys'),
('W', 'fof', 'dynamite', 'Dynamite', 'kills with Dynamite'),
('W', 'fof', 'explosive_arrow', 'Explosive Arrow', 'kills with Explosive Arrows'),
('W', 'fof', 'fists', 'Fists', 'kills with Fists'),
('W', 'fof', 'axe', 'Axe', 'kills with Axe'),
('W', 'fof', 'dualpeacemaker', 'Dual Peacemakers', 'kills with Dual Peacemakers'),
('W', 'fof', 'henryrifle', 'Henry Rifle', 'kills with Henry Rifle'),
('W', 'fof', 'whiskey', 'Whiskey', 'kills with Whiskey'),
('O', 'fof', 'loot_drop', 'Butter Fingers', 'Loot Drops'),
('O', 'fof', 'loot_capture', 'Gimme all yo loot', 'Loot Captures'),
('O', 'fof', 'carrier_protect', 'Grand Protector', 'carrier protections'),
('O', 'fof', 'headshot', 'BOOM HEADSHOT','headshot kills'),
('W', 'fof', 'latency', 'Lowest Ping','ms average connection'),
('W', 'ges', '#GE_ProximityMine', 'Proximity Mines', 'kills with Proximity Mines'),
('W', 'ges', '#GE_AutoShotgun', 'Automatic Shotgun', 'kills with Automatic Shotgun'),
('W', 'ges', '#GE_Phantom', 'Phantom', 'kills with Phantom'),
('W', 'ges', '#GE_Knife', 'Knife', 'kills with Hunting Knife'),
('W', 'ges', '#GE_D5K', 'D5K', 'kills with D5K Deutsche'),
('W', 'ges', '#GE_SilverPP7', 'Silver PP7', 'kills with Silver PP7'),
('W', 'ges', '#GE_DD44', 'DD44', 'kills with DD44'),
('W', 'ges', '#GE_Grenade', 'Grenade', 'kills with Grenades'),
('W', 'ges', '#GE_CougarMagnum', 'Cougar Magnum', 'kills with Cougar Magnum'),
('W', 'ges', '#GE_D5K_SILENCED', 'Silenced D5K', 'kills with D5K (Silenced)'),
('W', 'ges', '#GE_Shotgun', 'Shotgun', 'kills with Shotgun'),
('W', 'ges', '#GE_Klobb', 'Klobb', 'kills with Klobb'),
('W', 'ges', '#GE_RCP90', 'RC-P90', 'kills with RC-P90'),
('W', 'ges', '#GE_RemoteMine', 'Remote Mines', 'kills with Remote Mines'),
('W', 'ges', '#GE_KF7Soviet', 'KF7 Soviet', 'kills with KF7 Soviet'),
('W', 'ges', '#GE_ZMG', 'ZMG', 'kills with ZMG'),
('W', 'ges', '#GE_SniperRifle', 'Sniper Rifle', 'kills with Sniper Rifle'),
('W', 'ges', '#GE_GoldPP7', 'Golden PP7', 'kills with Golden PP7'),
('W', 'ges', '#GE_AR33', 'AR33', 'kills with US AR33 Assault'),
('W', 'ges', '#GE_GoldenGun', 'Golden Gun', 'kills with Golden Gun'),
('W', 'ges', '#GE_ThrowingKnife', 'Thorwing Knives', 'kills with Throwing Knives'),
('W', 'ges', '#GE_PP7', 'PP7', 'kills with PP7'),
('W', 'ges', '#GE_PP7_SILENCED', 'Silenced PP7', 'kills with PP7 (Silenced)'),
('W', 'ges', '#GE_TimedMine', 'Timed Mines', 'kills with Timed Mines'),
('W', 'ges', '#GE_MilitaryLaser', 'Military Laser', 'kills with Military Laser'),
('W', 'ges', '#GE_GrenadeLauncher', 'Grenade Launcher', 'kills with Grenade Launcher'),
('W', 'ges', '#GE_Rocket', 'Rocket Launcher', 'kills with Rocket Launcher'),
('W', 'ges', '#GE_Taser', 'Taser', 'kills with Taser'),
('W', 'ges', '#GE_SniperButt', 'Sniper Butt', 'kills with Sniper Butt'),
('W', 'ges', '#GE_Slapper', 'Slapper', 'kills with Slappers'),
('W', 'ges', '#GE_RocketLauncher', ', Rocket Launcher', 'kills with Rocket Launcher'),
('W', 'ges', 'latency', 'Lowest Ping','ms average connection');

INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'O',`code`,'flagevent_defended','Defender of the Flag','flag defenses' FROM `hlstats_Games` WHERE `realgame` = 'tf');
INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'O',`code`,'flagevent_captured','The Mad Capper','flag captures' FROM `hlstats_Games` WHERE `realgame` = 'tf');
INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'O',`code`,'killedobject_obj_dispenser','NO METAL FOR YOU!','dispensers destroyed' FROM `hlstats_Games` WHERE `realgame` = 'tf');
INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'O',`code`,'killedobject_obj_sentrygun','Say no to sentries','sentry guns destoryed' FROM `hlstats_Games` WHERE `realgame` = 'tf');
INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'O',`code`,'builtobject_obj_sentrygun','Bob the Builder','sentry guns built' FROM `hlstats_Games` WHERE `realgame` = 'tf');

ALTER TABLE `hlstats_Events_Admin`
CHANGE `map` `map` VARCHAR( 64 ) NOT NULL default '';

ALTER TABLE `hlstats_Events_ChangeName`
CHANGE `map` `map` VARCHAR( 64 ) NOT NULL default '';

ALTER TABLE `hlstats_Events_ChangeRole`
CHANGE `map` `map` VARCHAR( 64 ) NOT NULL default '',
CHANGE `role` `role` VARCHAR( 64 ) NOT NULL default '';

ALTER TABLE `hlstats_Events_ChangeTeam`
CHANGE `map` `map` VARCHAR( 64 ) NOT NULL default '',
CHANGE `team` `team` VARCHAR( 64 ) NOT NULL default '';

ALTER TABLE `hlstats_Events_Chat`
CHANGE `map` `map` VARCHAR( 64 ) NOT NULL default '';

ALTER TABLE `hlstats_Events_Connects`
CHANGE `map` `map` VARCHAR( 64 ) NOT NULL default '';

ALTER TABLE `hlstats_Events_Disconnects`
CHANGE `map` `map` VARCHAR( 64 ) NOT NULL default '';

ALTER TABLE `hlstats_Events_Entries`
CHANGE `map` `map` VARCHAR( 64 ) NOT NULL default '';

ALTER TABLE `hlstats_Events_Frags`
DROP INDEX `map`,
CHANGE `map` `map` VARCHAR( 64 ) NOT NULL default '',
CHANGE `weapon` `weapon` VARCHAR( 64 ) NOT NULL default '',
CHANGE `killerRole` `killerRole` VARCHAR( 64 ) NOT NULL default '',
CHANGE `victimRole` `victimRole` VARCHAR( 64 ) NOT NULL default '',
ADD INDEX ( `serverId` ),
ADD INDEX ( `headshot` ),
ADD INDEX ( `map` ( 5 ) );

UPDATE `hlstats_Events_Frags` SET `weapon` = 'weapon_fnfal' WHERE `weapon` = 'weapon_fnfa1' AND serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'insmod'));

ALTER TABLE `hlstats_Events_Latency`
CHANGE `map` `map` VARCHAR( 64 ) NOT NULL default '';

ALTER TABLE `hlstats_Events_PlayerActions`
CHANGE `map` `map` VARCHAR( 64 ) NOT NULL default '';

ALTER TABLE `hlstats_Events_PlayerPlayerActions`
CHANGE `map` `map` VARCHAR( 64 ) NOT NULL default '';

ALTER TABLE `hlstats_Events_Rcon`
CHANGE `map` `map` VARCHAR( 64 ) NOT NULL default '',
CHANGE `password` `password` VARCHAR( 128 ) NOT NULL default '';

ALTER TABLE `hlstats_Events_Statsme`
CHANGE `map` `map` VARCHAR( 64 ) NOT NULL default '',
CHANGE `weapon` `weapon` VARCHAR( 64 ) NOT NULL default '';

ALTER TABLE `hlstats_Events_Statsme2`
CHANGE `map` `map` VARCHAR( 64 ) NOT NULL default '',
CHANGE `weapon` `weapon` VARCHAR( 64 ) NOT NULL default '';

ALTER TABLE `hlstats_Events_StatsmeLatency`
CHANGE `map` `map` VARCHAR( 64 ) NOT NULL default '';

ALTER TABLE `hlstats_Events_StatsmeTime`
CHANGE `map` `map` VARCHAR( 64 ) NOT NULL default '';

ALTER TABLE `hlstats_Events_Suicides`
CHANGE `map` `map` VARCHAR( 64 ) NOT NULL default '',
CHANGE `weapon` `weapon` VARCHAR( 64 ) NOT NULL default '';

UPDATE `hlstats_Events_Suicides` SET `weapon` = 'weapon_fnfal' WHERE `weapon` = 'weapon_fnfa1' AND serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'insmod'));

ALTER TABLE `hlstats_Events_TeamBonuses`
CHANGE `map` `map` VARCHAR( 64 ) NOT NULL default '';

ALTER TABLE `hlstats_Events_Teamkills`
CHANGE `map` `map` VARCHAR( 64 ) NOT NULL default '',
CHANGE `weapon` `weapon` VARCHAR( 64 ) NOT NULL default '';

UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'weapon_fnfal' WHERE `weapon` = 'weapon_fnfa1' AND serverId IN (SELECT serverId FROM `hlstats_Servers` WHERE `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'insmod'));

INSERT IGNORE INTO `hlstats_Games` (`code`, `name`, `realgame`, `hidden`) VALUES
('l4d', 'Left 4 Dead', 'l4d', '1'),
('fof', 'Fistful of Frags', 'fof', '1'),
('ges', 'GoldenEye: Source', 'ges', '1');

DELETE FROM `hlstats_Games_Defaults` WHERE `parameter` = 'AdminContact';

INSERT IGNORE INTO `hlstats_Games_Defaults` (`code`, `parameter`, `value`) VALUES
('l4d', 'DisplayResultsInBrowser', '1'),
('l4d', 'GameEngine', '3'),
('l4d', 'AddressPort', '0.0.0.0:27015'),
('l4d', 'Admins', ''),
('l4d', 'AutoBanRetry', '0'),
('l4d', 'AutoTeamBalance', '0'),
('l4d', 'BonusRoundIgnore', '0'),
('l4d', 'BonusRoundTime', '0'),
('l4d', 'BroadCastEvents', '0'),
('l4d', 'BroadCastPlayerActions', '0'),
('l4d', 'EnablePublicCommands', '0'),
('l4d', 'GameType', '0'),
('l4d', 'HLStatsURL', 'http://yoursite.com/hlstats'),
('l4d', 'IgnoreBots', '0'),
('l4d', 'MinimumPlayersRank', '0'),
('l4d', 'MinPlayers', '1'),
('l4d', 'PlayerEvents', '1'),
('l4d', 'ShowStats', '1'),
('l4d', 'SkillMode', '0'),
('l4d', 'SuicidePenalty', '5'),
('l4d', 'SwitchAdmins', '0'),
('l4d', 'TKPenalty', '25'),
('l4d', 'TrackServerLoad', '1'),
('fof', 'DisplayResultsInBrowser', '1'),
('fof', 'GameEngine', '3'),
('fof', 'AddressPort', '0.0.0.0:27015'),
('fof', 'Admins', ''),
('fof', 'AutoBanRetry', '0'),
('fof', 'AutoTeamBalance', '0'),
('fof', 'BonusRoundIgnore', '0'),
('fof', 'BonusRoundTime', '0'),
('fof', 'BroadCastEvents', '1'),
('fof', 'BroadCastPlayerActions', '1'),
('fof', 'EnablePublicCommands', '1'),
('fof', 'GameType', '0'),
('fof', 'HLStatsURL', 'http://yoursite.com/hlstats'),
('fof', 'IgnoreBots', '1'),
('fof', 'MinimumPlayersRank', '0'),
('fof', 'MinPlayers', '4'),
('fof', 'PlayerEvents', '1'),
('fof', 'ShowStats', '1'),
('fof', 'SkillMode', '0'),
('fof', 'SuicidePenalty', '5'),
('fof', 'SwitchAdmins', '0'),
('fof', 'TKPenalty', '25'),
('fof', 'TrackServerLoad', '1'),
('ges', 'DisplayResultsInBrowser', '1'),
('ges', 'GameEngine', '3'),
('ges', 'AddressPort', '0.0.0.0:27015'),
('ges', 'Admins', ''),
('ges', 'AutoBanRetry', '0'),
('ges', 'AutoTeamBalance', '0'),
('ges', 'BonusRoundIgnore', '0'),
('ges', 'BonusRoundTime', '0'),
('ges', 'BroadCastEvents', '1'),
('ges', 'BroadCastPlayerActions', '1'),
('ges', 'EnablePublicCommands', '1'),
('ges', 'GameType', '0'),
('ges', 'HLStatsURL', 'http://yoursite.com/hlstats'),
('ges', 'IgnoreBots', '1'),
('ges', 'MinimumPlayersRank', '0'),
('ges', 'MinPlayers', '4'),
('ges', 'PlayerEvents', '1'),
('ges', 'ShowStats', '1'),
('ges', 'SkillMode', '0'),
('ges', 'SuicidePenalty', '5'),
('ges', 'SwitchAdmins', '0'),
('ges', 'TKPenalty', '25'),
('ges', 'TrackServerLoad', '1');

INSERT IGNORE INTO `hlstats_Games_Supported` (`code`, `name`) VALUES
('l4d', 'Left 4 Dead'),
('fof', 'Fistful of Frags'),
('ges', 'GoldenEye: Source');

ALTER TABLE `hlstats_Livestats`
CHANGE `team` `team` VARCHAR( 64 ) NOT NULL default '';

ALTER TABLE `hlstats_Options`
ADD `opttype` TINYINT NOT NULL DEFAULT '1',
ADD INDEX ( `opttype` );

UPDATE `hlstats_Options` SET `opttype` = 2;

INSERT INTO `hlstats_Options` (`keyname`, `value`, `opttype`)
(SELECT `parameter`, `value`, 0 FROM `hlstats_PerlConfig`);

INSERT IGNORE INTO `hlstats_Options` (`keyname`, `value`, `opttype`) VALUES
('slider', '1',2),
('modrewrite','0',2),
('UseGeoIPBinary', '0',0);

UPDATE `hlstats_Options` SET `value` = 'skill' WHERE `keyname` = 'rankingtype' AND `value`=1;
UPDATE `hlstats_Options` SET `value` = 'kills' WHERE `keyname` = 'rankingtype' AND `value`=2;
UPDATE `hlstats_Options` SET `opttype` = 1 WHERE `keyname` IN ('Mode','rankingtype','DeleteDays','MinActivity');

TRUNCATE TABLE `hlstats_Options_Choices`;

INSERT IGNORE INTO `hlstats_Options_Choices` (`keyname`, `value`, `text`, `isDefault`) VALUES
('rankingtype', 'skill', 'Skill', 1),
('rankingtype', 'kills', 'Kills', 0),
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
('google_map_type', 'PHYSICAL', 'Physical', 0),
('slider', '1', 'Enabled', 1),
('slider', '0', 'Disabled', 0),
('modrewrite', '1', 'Enabled', 0),
('modrewrite', '0', 'Disabled', 1),
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
('SkillRatioCap', '1', 'Yes', 0),
('UseGeoIPBinary', '0', 'GeoIP lookup via database', 1),
('UseGeoIPBinary', '1', 'GeoIP lookup via binary file', 0);

DROP TABLE `hlstats_PerlConfig`;
DROP TABLE `hlstats_PerlConfig_Choices`;

ALTER TABLE `hlstats_Players` CHANGE `last_event` `last_event` INT( 11 ) NOT NULL DEFAULT '0';

INSERT IGNORE INTO `hlstats_Ranks` (`image`, `minKills`, `maxKills`, `rankName`, `game`) VALUES
('recruit', 0, 49, 'Recruit', 'l4d'),
('private', 50, 99, 'Private', 'l4d'),
('private-first-class', 100, 199, 'Private First Class', 'l4d'),
('lance-corporal', 200, 299, 'Lance Corporal', 'l4d'),
('corporal', 300, 399, 'Corporal', 'l4d'),
('sergeant', 400, 499, 'Sergeant', 'l4d'),
('staff-sergeant', 500, 599, 'Staff Sergeant', 'l4d'),
('gunnery-sergeant', 600, 699, 'Gunnery Sergeant', 'l4d'),
('master-sergeant', 700, 799, 'Master Sergeant', 'l4d'),
('first-sergeant', 800, 899, 'First Sergeant', 'l4d'),
('master-chief', 900, 999, 'Master Chief', 'l4d'),
('sergeant-major', 1000, 1199, 'Sergeant Major', 'l4d'),
('ensign', 1200, 1399, 'Ensign', 'l4d'),
('third-lieutenant', 1400, 1599, 'Third Lieutenant', 'l4d'),
('second-lieutenant', 1600, 1799, 'Second Lieutenant', 'l4d'),
('first-lieutenant', 1800, 1999, 'First Lieutenant', 'l4d'),
('captain', 2000, 2249, 'Captain', 'l4d'),
('group-captain', 2250, 2499, 'Group Captain', 'l4d'),
('senior-captain', 2500, 2749, 'Senior Captain', 'l4d'),
('lieutenant-major', 2750, 2999, 'Lieutenant Major', 'l4d'),
('major', 3000, 3499, 'Major', 'l4d'),
('group-major', 3500, 3999, 'Group Major', 'l4d'),
('lieutenant-commander', 4000, 4499, 'Lieutenant Commander', 'l4d'),
('commander', 4500, 4999, 'Commander', 'l4d'),
('group-commander', 5000, 5749, 'Group Commander', 'l4d'),
('lieutenant-colonel', 5750, 6499, 'Lieutenant Colonel', 'l4d'),
('colonel', 6500, 7249, 'Colonel', 'l4d'),
('brigadier', 7250, 7999, 'Brigadier', 'l4d'),
('brigadier-general', 8000, 8999, 'Brigadier General', 'l4d'),
('major-general', 9000, 9999, 'Major General', 'l4d'),
('lieutenant-general', 10000, 12499, 'Lieutenant General', 'l4d'),
('general', 12500, 14999, 'General', 'l4d'),
('commander-general', 15000, 17499, 'Commander General', 'l4d'),
('field-vice-marshal', 17500, 19999, 'Field Vice Marshal', 'l4d'),
('field-marshal', 20000, 22499, 'Field Marshal', 'l4d'),
('vice-commander-of-the-army', 22500, 24999, 'Vice Commander of the Army', 'l4d'),
('commander-of-the-army', 25000, 27499, 'Commander of the Army', 'l4d'),
('high-commander', 27500, 29999, 'High Commander', 'l4d'),
('supreme-commander', 30000, 34999, 'Supreme Commander', 'l4d'),
('terminator', 35000, 9999999, 'Terminator', 'l4d'),
('recruit', 0, 49, 'Recruit', 'fof'),
('private', 50, 99, 'Private', 'fof'),
('private-first-class', 100, 199, 'Private First Class', 'fof'),
('lance-corporal', 200, 299, 'Lance Corporal', 'fof'),
('corporal', 300, 399, 'Corporal', 'fof'),
('sergeant', 400, 499, 'Sergeant', 'fof'),
('staff-sergeant', 500, 599, 'Staff Sergeant', 'fof'),
('gunnery-sergeant', 600, 699, 'Gunnery Sergeant', 'fof'),
('master-sergeant', 700, 799, 'Master Sergeant', 'fof'),
('first-sergeant', 800, 899, 'First Sergeant', 'fof'),
('master-chief', 900, 999, 'Master Chief', 'fof'),
('sergeant-major', 1000, 1199, 'Sergeant Major', 'fof'),
('ensign', 1200, 1399, 'Ensign', 'fof'),
('third-lieutenant', 1400, 1599, 'Third Lieutenant', 'fof'),
('second-lieutenant', 1600, 1799, 'Second Lieutenant', 'fof'),
('first-lieutenant', 1800, 1999, 'First Lieutenant', 'fof'),
('captain', 2000, 2249, 'Captain', 'fof'),
('group-captain', 2250, 2499, 'Group Captain', 'fof'),
('senior-captain', 2500, 2749, 'Senior Captain', 'fof'),
('lieutenant-major', 2750, 2999, 'Lieutenant Major', 'fof'),
('major', 3000, 3499, 'Major', 'fof'),
('group-major', 3500, 3999, 'Group Major', 'fof'),
('lieutenant-commander', 4000, 4499, 'Lieutenant Commander', 'fof'),
('commander', 4500, 4999, 'Commander', 'fof'),
('group-commander', 5000, 5749, 'Group Commander', 'fof'),
('lieutenant-colonel', 5750, 6499, 'Lieutenant Colonel', 'fof'),
('colonel', 6500, 7249, 'Colonel', 'fof'),
('brigadier', 7250, 7999, 'Brigadier', 'fof'),
('brigadier-general', 8000, 8999, 'Brigadier General', 'fof'),
('major-general', 9000, 9999, 'Major General', 'fof'),
('lieutenant-general', 10000, 12499, 'Lieutenant General', 'fof'),
('general', 12500, 14999, 'General', 'fof'),
('commander-general', 15000, 17499, 'Commander General', 'fof'),
('field-vice-marshal', 17500, 19999, 'Field Vice Marshal', 'fof'),
('field-marshal', 20000, 22499, 'Field Marshal', 'fof'),
('vice-commander-of-the-army', 22500, 24999, 'Vice Commander of the Army', 'fof'),
('commander-of-the-army', 25000, 27499, 'Commander of the Army', 'fof'),
('high-commander', 27500, 29999, 'High Commander', 'fof'),
('supreme-commander', 30000, 34999, 'Supreme Commander', 'fof'),
('terminator', 35000, 9999999, 'Terminator', 'fof'),
('recruit', 0, 49, 'Recruit', 'ges'),
('private', 50, 99, 'Private', 'ges'),
('private-first-class', 100, 199, 'Private First Class', 'ges'),
('lance-corporal', 200, 299, 'Lance Corporal', 'ges'),
('corporal', 300, 399, 'Corporal', 'ges'),
('sergeant', 400, 499, 'Sergeant', 'ges'),
('staff-sergeant', 500, 599, 'Staff Sergeant', 'ges'),
('gunnery-sergeant', 600, 699, 'Gunnery Sergeant', 'ges'),
('master-sergeant', 700, 799, 'Master Sergeant', 'ges'),
('first-sergeant', 800, 899, 'First Sergeant', 'ges'),
('master-chief', 900, 999, 'Master Chief', 'ges'),
('sergeant-major', 1000, 1199, 'Sergeant Major', 'ges'),
('ensign', 1200, 1399, 'Ensign', 'ges'),
('third-lieutenant', 1400, 1599, 'Third Lieutenant', 'ges'),
('second-lieutenant', 1600, 1799, 'Second Lieutenant', 'ges'),
('first-lieutenant', 1800, 1999, 'First Lieutenant', 'ges'),
('captain', 2000, 2249, 'Captain', 'ges'),
('group-captain', 2250, 2499, 'Group Captain', 'ges'),
('senior-captain', 2500, 2749, 'Senior Captain', 'ges'),
('lieutenant-major', 2750, 2999, 'Lieutenant Major', 'ges'),
('major', 3000, 3499, 'Major', 'ges'),
('group-major', 3500, 3999, 'Group Major', 'ges'),
('lieutenant-commander', 4000, 4499, 'Lieutenant Commander', 'ges'),
('commander', 4500, 4999, 'Commander', 'ges'),
('group-commander', 5000, 5749, 'Group Commander', 'ges'),
('lieutenant-colonel', 5750, 6499, 'Lieutenant Colonel', 'ges'),
('colonel', 6500, 7249, 'Colonel', 'ges'),
('brigadier', 7250, 7999, 'Brigadier', 'ges'),
('brigadier-general', 8000, 8999, 'Brigadier General', 'ges'),
('major-general', 9000, 9999, 'Major General', 'ges'),
('lieutenant-general', 10000, 12499, 'Lieutenant General', 'ges'),
('general', 12500, 14999, 'General', 'ges'),
('commander-general', 15000, 17499, 'Commander General', 'ges'),
('field-vice-marshal', 17500, 19999, 'Field Vice Marshal', 'ges'),
('field-marshal', 20000, 22499, 'Field Marshal', 'ges'),
('vice-commander-of-the-army', 22500, 24999, 'Vice Commander of the Army', 'ges'),
('commander-of-the-army', 25000, 27499, 'Commander of the Army', 'ges'),
('high-commander', 27500, 29999, 'High Commander', 'ges'),
('supreme-commander', 30000, 34999, 'Supreme Commander', 'ges'),
('terminator', 35000, 9999999, 'Terminator', 'ges');

UPDATE `hlstats_Ribbons` SET `awardCode` = 'weapon_fnfal' WHERE `awardCode`='weapon_fnfa1' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'insmod');

INSERT INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES
('ak47',5,0,'cstrike','1_ak47.png','Award of AK47'),
('awp',5,0,'cstrike','1_awp.png','Award of AWP Sniper'),
('deagle',5,0,'cstrike','1_deagle.png','Award of Desert Eagle'),
('elite',5,0,'cstrike','1_elite.png','Award of Dual Beretta Elites'),
('famas',5,0,'cstrike','1_famas.png','Award of Fusil Automatique'),
('galil',5,0,'cstrike','1_galil.png','Award of Galil'),
('glock18',5,0,'cstrike','1_glock.png','Award of Glock'),
('knife',5,0,'cstrike','1_knife.png','Award of Combat Knife'),
('latency',5,0,'cstrike','1_latency.png','Award of Lowpinger'),
('m3',5,0,'cstrike','1_m3.png','Award of M3 Super'),
('m4a1',5,0,'cstrike','1_m4a1.png','Award of Colt M4A1'),
('p90',5,0,'cstrike','1_p90.png','Award of P90'),
('scout',5,0,'cstrike','1_scout.png','Award of Scout Elite'),
('usp',5,0,'cstrike','1_usp.png','Award of USP'),
('boomer_claw', '5', '0', 'l4d', '1_boomer_claw.png', 'Bronze Boom!'),
('headshot', '5', '0', 'l4d', '1_headshot.png', 'Bronze Brain Salad'),
('healed_teammate', '5', '0', 'l4d', '1_healed_teammate.png', 'Bronze Field Medic'),
('hunter_claw', '5', '0', 'l4d', '1_hunter_claw.png', 'Bronze Grim Reaper'),
('inferno', '5', '0', 'l4d', '1_inferno.png', 'Bronze Cremator'),
('killed_exploding', '5', '0', 'l4d', '1_killed_exploding.png', 'Bronze Stomach Upset'),
('killed_gas', '5', '0', 'l4d', '1_killed_gas.png', 'Bronze Tongue Twister'),
('killed_hunter', '5', '0', 'l4d', '1_killed_hunter.png', 'Bronze Hunter Punter'),
('killed_survivor', '5', '0', 'l4d', '1_killed_survivor.png', 'Bronze Dead Wreckening'),
('killed_tank', '5', '0', 'l4d', '1_killed_tank.png', 'Bronze Tankbuster'),
('killed_witch', '5', '0', 'l4d', '1_killed_witch.png', 'Bronze Inquisitor'),
('latency', '5', '0', 'l4d', '1_latency.png', 'Bronze Nothing Special'),
('pipe_bomb', '5', '0', 'l4d', '1_pipe_bomb.png', 'Bronze Pyrotechnician'),
('pounce', '5', '0', 'l4d', '1_pounce.png', 'Bronze Free 2 Fly'),
('rescued_survivor', '5', '0', 'l4d', '1_rescued_survivor.png', 'Bronze Ground Cover'),
('revived_teammate', '5', '0', 'l4d', '1_revived_teammate.png', 'Bronze Helping Hand'),
('smoker_claw', '5', '0', 'l4d', '1_smoker_claw.png', 'Bronze Chain Smoker'),
('tank_claw', '5', '0', 'l4d', '1_tank_claw.png', 'Bronze Lambs 2 Slaughter'),
('tongue_grab', '5', '0', 'l4d', '1_tongue_grab.png', 'Bronze Drag &amp; Drop'),
('vomit', '5', '0', 'l4d', '1_vomit.png', 'Bronze Barf Bagged'),
('boomer_claw', '15', '0', 'l4d', '2_boomer_claw.png', 'Silver Boom!'),
('headshot', '15', '0', 'l4d', '2_headshot.png', 'Silver Brain Salad'),
('healed_teammate', '15', '0', 'l4d', '2_healed_teammate.png', 'Silver Field Medic'),
('hunter_claw', '15', '0', 'l4d', '2_hunter_claw.png', 'Silver Grim Reaper'),
('inferno', '15', '0', 'l4d', '2_inferno.png', 'Silver Cremator'),
('killed_exploding', '15', '0', 'l4d', '2_killed_exploding.png', 'Silver Stomach Upset'),
('killed_gas', '15', '0', 'l4d', '2_killed_gas.png', 'Silver Tongue Twister'),
('killed_hunter', '15', '0', 'l4d', '2_killed_hunter.png', 'Silver Hunter Punter'),
('killed_survivor', '15', '0', 'l4d', '2_killed_survivor.png', 'Silver Dead Wreckening'),
('killed_tank', '15', '0', 'l4d', '2_killed_tank.png', 'Silver Tankbuster'),
('killed_witch', '15', '0', 'l4d', '2_killed_witch.png', 'Silver Inquisitor'),
('latency', '15', '0', 'l4d', '2_latency.png', 'Silver Nothing Special'),
('pipe_bomb', '15', '0', 'l4d', '2_pipe_bomb.png', 'Silver Pyrotechnician'),
('pounce', '15', '0', 'l4d', '2_pounce.png', 'Silver Free 2 Fly'),
('rescued_survivor', '15', '0', 'l4d', '2_rescued_survivor.png', 'Silver Ground Cover'),
('revived_teammate', '15', '0', 'l4d', '2_revived_teammate.png', 'Silver Helping Hand'),
('smoker_claw', '15', '0', 'l4d', '2_smoker_claw.png', 'Silver Chain Smoker'),
('tank_claw', '15', '0', 'l4d', '2_tank_claw.png', 'Silver Lambs 2 Slaughter'),
('tongue_grab', '15', '0', 'l4d', '2_tongue_grab.png', 'Silver Drag &amp; Drop'),
('vomit', '15', '0', 'l4d', '2_vomit.png', 'Silver Barf Bagged'),
('boomer_claw', '30', '0', 'l4d', '3_boomer_claw.png', 'Golden Boom!'),
('headshot', '30', '0', 'l4d', '3_headshot.png', 'Golden Brain Salad'),
('healed_teammate', '30', '0', 'l4d', '3_healed_teammate.png', 'Golden Field Medic'),
('hunter_claw', '30', '0', 'l4d', '3_hunter_claw.png', 'Golden Grim Reaper'),
('inferno', '30', '0', 'l4d', '3_inferno.png', 'Golden Cremator'),
('killed_exploding', '30', '0', 'l4d', '3_killed_exploding.png', 'Golden Stomach Upset'),
('killed_gas', '30', '0', 'l4d', '3_killed_gas.png', 'Golden Tongue Twister'),
('killed_hunter', '30', '0', 'l4d', '3_killed_hunter.png', 'Golden Hunter Punter'),
('killed_survivor', '30', '0', 'l4d', '3_killed_survivor.png', 'Golden Dead Wreckening'),
('killed_tank', '30', '0', 'l4d', '3_killed_tank.png', 'Golden Tankbuster'),
('killed_witch', '30', '0', 'l4d', '3_killed_witch.png', 'Golden Inquisitor'),
('latency', '30', '0', 'l4d', '3_latency.png', 'Golden Nothing Special'),
('pipe_bomb', '30', '0', 'l4d', '3_pipe_bomb.png', 'Golden Pyrotechnician'),
('pounce', '30', '0', 'l4d', '3_pounce.png', 'Golden Free 2 Fly'),
('rescued_survivor', '30', '0', 'l4d', '3_rescued_survivor.png', 'Golden Ground Cover'),
('revived_teammate', '30', '0', 'l4d', '3_revived_teammate.png', 'Golden Helping Hand'),
('smoker_claw', '30', '0', 'l4d', '3_smoker_claw.png', 'Golden Chain Smoker'),
('tank_claw', '30', '0', 'l4d', '3_tank_claw.png', 'Golden Lambs 2 Slaughter'),
('tongue_grab', '30', '0', 'l4d', '3_tongue_grab.png', 'Golden Drag &amp; Drop'),
('vomit', '30', '0', 'l4d', '3_vomit.png', 'Golden Barf Bagged'),
('boomer_claw', '50', '0', 'l4d', '4_boomer_claw.png', 'Bloody Boom!'),
('headshot', '50', '0', 'l4d', '4_headshot.png', 'Bloody Brain Salad'),
('healed_teammate', '50', '0', 'l4d', '4_healed_teammate.png', 'Bloody Field Medic'),
('hunter_claw', '50', '0', 'l4d', '4_hunter_claw.png', 'Bloody Grim Reaper'),
('inferno', '50', '0', 'l4d', '4_inferno.png', 'Bloody Cremator'),
('killed_exploding', '50', '0', 'l4d', '4_killed_exploding.png', 'Bloody Stomach Upset'),
('killed_gas', '50', '0', 'l4d', '4_killed_gas.png', 'Bloody Tongue Twister'),
('killed_hunter', '50', '0', 'l4d', '4_killed_hunter.png', 'Bloody Hunter Punter'),
('killed_survivor', '50', '0', 'l4d', '4_killed_survivor.png', 'Bloody Dead Wreckening'),
('killed_tank', '50', '0', 'l4d', '4_killed_tank.png', 'Bloody Tankbuster'),
('killed_witch', '50', '0', 'l4d', '4_killed_witch.png', 'Bloody Inquisitor'),
('latency', '50', '0', 'l4d', '4_latency.png', 'Bloody Nothing Special'),
('pipe_bomb', '50', '0', 'l4d', '4_pipe_bomb.png', 'Bloody Pyrotechnician'),
('pounce', '50', '0', 'l4d', '4_pounce.png', 'Bloody Free 2 Fly'),
('rescued_survivor', '50', '0', 'l4d', '4_rescued_survivor.png', 'Bloody Ground Cover'),
('revived_teammate', '50', '0', 'l4d', '4_revived_teammate.png', 'Bloody Helping Hand'),
('smoker_claw', '50', '0', 'l4d', '4_smoker_claw.png', 'Bloody Chain Smoker'),
('tank_claw', '50', '0', 'l4d', '4_tank_claw.png', 'Bloody Lambs 2 Slaughter'),
('tongue_grab', '50', '0', 'l4d', '4_tongue_grab.png', 'Bloody Drag &amp; Drop'),
('vomit', '50', '0', 'l4d', '4_vomit.png', 'Bloody Barf Bagged');

UPDATE `hlstats_Roles` SET `code` = 'scout' WHERE `code`='Scout' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'ff');
UPDATE `hlstats_Roles` SET `code` = 'sniper' WHERE `code`='Sniper' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'ff');
UPDATE `hlstats_Roles` SET `code` = 'soldier' WHERE `code`='Soldier' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'ff');
UPDATE `hlstats_Roles` SET `code` = 'pyro' WHERE `code`='Pyro' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'ff');
UPDATE `hlstats_Roles` SET `code` = 'hwguy' WHERE `code`='HWGuy' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'ff');
UPDATE `hlstats_Roles` SET `code` = 'spy' WHERE `code`='Spy' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'ff');
UPDATE `hlstats_Roles` SET `code` = 'demoman' WHERE `code`='Demoman' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'ff');
UPDATE `hlstats_Roles` SET `code` = 'engineer' WHERE `code`='Engineer' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'ff');
UPDATE `hlstats_Roles` SET `code` = 'medic' WHERE `code`='Medic' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'ff');
UPDATE `hlstats_Roles` SET `code` = 'civilian' WHERE `code`='Civilian' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'ff');

INSERT IGNORE INTO `hlstats_Roles` (`game`, `code`, `name`, `hidden`) VALUES
('l4d', 'NamVet', 'Bill', '0'),
('l4d', 'TeenGirl', 'Zoey', '0'),
('l4d', 'Biker', 'Francis', '0'),
('l4d', 'Manager', 'Louis', '0'),
('l4d', 'GAS', 'Smoker', '0'),
('l4d', 'EXPLODING', 'Boomer', '0'),
('l4d', 'HUNTER', 'Hunter', '0'),
('l4d', 'TANK', 'tank', '0'),
('l4d', 'infected', 'Infected Horde', '0'),
('l4d', 'witch', 'Witch', '0');

ALTER TABLE `hlstats_Servers`
CHANGE `act_map` `act_map` varchar(64) NOT NULL default '',
ADD COLUMN `sortorder` tinyint NOT NULL DEFAULT '0' AFTER `name`;

DROP TABLE `hlstats_server_addons`;

ALTER TABLE `hlstats_server_load`
CHANGE `map` `map` varchar(64) default NULL;

DELETE FROM `hlstats_Servers_Config` WHERE `parameter` = 'AdminContact';

DELETE FROM `hlstats_Servers_Config_Default` WHERE `parameter` = 'AdminContact';

UPDATE `hlstats_Servers_Config_Default` SET `description` = 'Mode of skill changes on frags with following options:\r\n<UL>\r\n<LI>0 = Normal (Victims lose all the points which the killer gains).\r\n<LI>1 = Victims lose 3/4 the points which the killer gains.\r\n<LI>2 = Victims lose 1/2 the points which the killer gains.\r\n<LI>3 = Victims lose 1/4 the points which the killer gains.\r\n<LI>4 = Victims lose no points.\r\n<LI>ZPS-only. Survivor victims lose 1/2, Zombie victims lose 1/4\r\n</UL>' WHERE `parameter` = 'SkillMode';
UPDATE `hlstats_Servers_Config_Default` SET `description` = 'Mode of the current gametype:<UL>\r\n<LI>0 = Normal mod standard (default).\r\n<LI>1 = Deathmatch (only need to set if team names are NOT "Unassigned" during deathmatch (ie. in CSS Deathmatch).</UL>' WHERE `parameter` = 'GameType';

DELETE FROM `hlstats_Teams` WHERE `code` IN ('Spectator','Spectators','Unassigned','#FF_TEAM_UNASSIGNED');

UPDATE `hlstats_Teams` SET `code` = 'Spectator' WHERE `code`='Spectators' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'insmod');

INSERT IGNORE INTO `hlstats_Teams` (`game`, `code`, `name`, `hidden`, `playerlist_bgcolor`, `playerlist_color`, `playerlist_index`) VALUES
('l4d', 'Survivor', 'Survivors', '0', '#E0E4E5', '#4B6168', 1),
('l4d', 'Infected', 'Infected', '0', '#E5D5D5', '#68090B', 2),
('fof', 'DESPERADOS', 'Desparados', '0', '#D2E8F7','#0080C0', 1),
('fof', 'VIGILANTES', 'Vigilantes', '0', '#FFD5D5','#FF2D2D', 2),
('ges', 'MI6', 'MI6', '0', '#D2E8F7','#0080C0', 1),
('ges', 'Janus', 'Janus', '0', '#FFD5D5','#FF2D2D', 2);

UPDATE `hlstats_Weapons` SET `code` = 'weapon_fnfal' WHERE `code`='weapon_fnfa1' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'insmod');

INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
('zps', 'winchester','Winchester Double Barreled Shotgun',1),
('l4d', 'rifle', 'M16 Assault Rifle', 1.00),
('l4d', 'autoshotgun', 'Auto Shotgun', 1.00),
('l4d', 'pumpshotgun', 'Pump Shotgun', 1.30),
('l4d', 'smg', 'Uzi', 1.20),
('l4d', 'dual_pistols', 'Dual Pistols', 1.60),
('l4d', 'pipe_bomb', 'Pipe Bomb', 1.00),
('l4d', 'hunting_rifle', 'Hunting Rifle', 1.00),
('l4d', 'pistol', 'Pistol', 2.00),
('l4d', 'prop_minigun', 'Mounted Machine Gun', 1.20),
('l4d', 'tank_claw', 'Tank\'s Claws', 3.00),
('l4d', 'hunter_claw', 'Hunter\'s Claws', 3.00),
('l4d', 'smoker_claw', 'Smoker\'s Claws', 3.00),
('l4d', 'boomer_claw', 'Boomer\'s Claws', 3.00),
('l4d', 'inferno', 'Molotov/Gas Can Fire', 1.20),
('l4d', 'pipe_bomb', 'Pipe Bomb/Explosion', 0.80),
('l4d', 'infected', 'Infected Horde', 1.00),
('l4d', 'witch', 'Witch\'s Claws', 1.00),
('l4d', 'first_aid_kit', 'First Aid Kit Smash', 1.5),
('l4d', 'entityflame', 'Blaze', 3),
('l4d', 'gascan', 'Gas Can Smash', 1.5),
('l4d', 'molotov', 'Molotov Smash', 1.5),
('l4d', 'pain_pills', 'Pain Pills Smash', 1.5),
('l4d', 'player', 'Player', 1),
('l4d', 'propanetank', 'Propane Tank Smash', 1.5),
('l4d', 'tank_rock', 'Tank\'s Rock', 1.5),
('l4d', 'oxygentank', 'Oxygen Tank Smash', 1.5),
('l4d', 'world', 'World', 1),
('l4d', 'prop_physics', 'Prop Physics', 1),
('fof', 'deringer', 'Deringer', 1),
('fof', 'carbine', 'Carbine', 1),
('fof', 'coltnavy', 'Colt Navy', 1),
('fof', 'bow', 'Bow', 1),
('fof', 'arrow', 'Arrow', 1),
('fof', 'sharps', 'Sharps', 1),
('fof', 'coachgun', 'Coach Gun', 1),
('fof', 'peacemaker', 'Peacemaker', 1),
('fof', 'knife', 'Knife', 2),
('fof', 'physics', 'Exploding Barrel', 1),
('fof', 'dualderinger', 'Dual Deringers', 1),
('fof', 'thrown_axe', 'Thrown Axe', 3),
('fof', 'arrow_fiery', 'Fire Arrow', 2),
('fof', 'thrown_knife', 'Thrown Knife', 3),
('fof', 'dualnavy', 'Dual Colt Navys', 1),
('fof', 'dynamite', 'Dynamite', 3),
('fof', 'explosive_arrow', 'Explosive Arrow', 2),
('fof', 'fists', 'Fists', 2),
('fof', 'axe', 'Axe', 2),
('fof', 'dualpeacemaker', 'Dual Peacemakers', 1),
('fof', 'henryrifle', 'Henry Rifle', 1),
('ges', '#GE_ProximityMine', 'Proximity Mines', 1),
('ges', '#GE_AutoShotgun', 'Automatic Shotgun', 1),
('ges', '#GE_Phantom', 'Phantom', 1),
('ges', '#GE_Knife', 'Hunting Knife', 1),
('ges', '#GE_D5K', 'D5K Deutsche', 1),
('ges', '#GE_SilverPP7', 'Silver PP7', 1),
('ges', '#GE_DD44', 'DD44', 1),
('ges', '#GE_Grenade', 'Grenade', 1),
('ges', '#GE_CougarMagnum', 'Cougar Magnum', 1),
('ges', '#GE_D5K_SILENCED', 'D5K (Silenced)', 1),
('ges', '#GE_Shotgun', 'Shotgun', 1),
('ges', '#GE_Klobb', 'Klobb', 1),
('ges', '#GE_RCP90', 'RC-P90', 1),
('ges', '#GE_RemoteMine', 'Remote Mines', 1),
('ges', '#GE_KF7Soviet', 'KF7 Soviet', 1),
('ges', '#GE_ZMG', 'ZMG', 1),
('ges', '#GE_SniperRifle', 'Sniper Rifle', 1),
('ges', '#GE_GoldPP7', 'Golden PP7', 1),
('ges', '#GE_AR33', 'US AR33 Assault', 1),
('ges', '#GE_GoldenGun', 'Golden Gun', 1),
('ges', '#GE_ThrowingKnife', 'Throwing Knives', 1),
('ges', '#GE_PP7', 'PP7', 1),
('ges', '#GE_PP7_SILENCED', 'PP7 (Silenced)', 1),
('ges', '#GE_TimedMine', 'Timed Mines', 1),
('ges', '#GE_MilitaryLaser', 'Military Laser', 1),
('ges', '#GE_GrenadeLauncher', 'Grenade Launcher', 1),
('ges', '#GE_Rocket', 'Rocket Launcher', 1),
('ges', '#GE_Taser', 'Taser', 1),
('ges', '#GE_SniperButt', 'Sniper Butt', 1),
('ges', '#GE_Slapper', 'Slappers', 1),
('ges', '#GE_RocketLauncher', 'Rocket Launcher', 1);