INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`)
(SELECT code, 'pyro_extinguish', 1, 0, '', 'Extinguished Teammate (Pyro)', '1', '0', '0', '0' FROM hlstats_Games WHERE `realgame` = 'tf');

INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`)
(SELECT code, 'sniper_extinguish', 1, 0, '', 'Extinguished Teammate (Sniper)', '1', '0', '0', '0' FROM hlstats_Games WHERE `realgame` = 'tf');

UPDATE `hlstats_Awards` SET `code` = 'sandman' WHERE `code` = 'bat_wood';

INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'O', code, 'pyro_extinguish', 'Give It a Little Blow', 'extinguishes with Flamethrower' FROM hlstats_Games WHERE `realgame` = 'tf');

INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'O', code, 'sniper_extinguish', 'Douser', 'extinguishes with Jarate' FROM hlstats_Games WHERE `realgame` = 'tf');

INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'W', code, 'force_a_nature', 'Who wants some of this?', 'kills with the Force-A-Nature' FROM hlstats_Games WHERE `realgame` = 'tf');

INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'W', code, 'ambassador', 'Diplomatic Immunity', 'kills with the Ambassador' FROM hlstats_Games WHERE `realgame` = 'tf');

INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'W', code, 'tf_projectile_arrow', 'Quivering Fool', 'kills with the Huntsman' FROM hlstats_Games WHERE `realgame` = 'tf');

INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'W', code, 'taunt_spy', 'Dangerous Crab', 'spy taunt kills' FROM hlstats_Games WHERE `realgame` = 'tf');

INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'W', code, 'taunt_sniper', 'Robin Hood', 'sniper taunt kills' FROM hlstats_Games WHERE `realgame` = 'tf');

UPDATE `hlstats_Events_Frags` SET `weapon` = 'sandman' WHERE `weapon` = 'bat_wood';
UPDATE `hlstats_Events_Suicides` SET `weapon` = 'sandman' WHERE `weapon` = 'bat_wood';
UPDATE `hlstats_Events_Teamkills` SET `weapon` = 'sandman' WHERE `weapon` = 'bat_wood';


UPDATE `hlstats_Options` SET `value` = '1.5.6' WHERE `keyname` = 'version';
UPDATE `hlstats_Options` SET `value` = '6' WHERE `keyname` = 'dbversion';


UPDATE `hlstats_Weapons` SET `code` = 'sandman' WHERE `code` = 'bat_wood';

INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`)
(SELECT code, 'force_a_nature', 'Force-A-Nature', 1 FROM hlstats_Games WHERE `realgame` = 'tf');

INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`)
(SELECT code, 'ambassador', 'Ambassador', 1 FROM hlstats_Games WHERE `realgame` = 'tf');

INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`)
(SELECT code, 'tf_projectile_arrow', 'Huntsman', 1 FROM hlstats_Games WHERE `realgame` = 'tf');

INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`)
(SELECT code, 'taunt_spy', 'Spy Taunt', 3 FROM hlstats_Games WHERE `realgame` = 'tf');

INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`)
(SELECT code, 'taunt_sniper', 'Sniper Taunt', 3 FROM hlstats_Games WHERE `realgame` = 'tf');