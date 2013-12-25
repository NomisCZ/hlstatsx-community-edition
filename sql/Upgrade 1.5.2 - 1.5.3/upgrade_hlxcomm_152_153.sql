INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES
('tf', 'first_blood', 1, 0, '', 'First Blood', '1', '', '', ''),
('tf', 'steal_sandvich', 2, 0, '', 'Steal Sandvich', '', '1', '', ''),
('tf', 'stun', 0, 0, '', 'Stun', '', '1', '', ''),
('tfc','Capture Point 1',2,5,'1','Captured Point 1','1','0','0','0'),
('tfc','Capture Point 2',2,5,'1','Captured Point 2','1','0','0','0'),
('tfc','Capture Point 3',2,5,'1','Captured Point 3','1','0','0','0'),
('tfc','headshot',1,0,'0','Headshot Kill','1','0','0','0'),
('ges', 'headshot', 1, 0, '', 'Headshot Kill', '1', '', '', '');

INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'W',`code`,'taunt_scout','Home Run King','grand slams' FROM `hlstats_Games` WHERE `realgame` = 'tf');

INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'W',`code`,'bat_wood','Mr. Sandman','kills with the Sandman' FROM `hlstats_Games` WHERE `realgame` = 'tf');

INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'O',`code`,'stun','Absolutely Stunning','stuns' FROM `hlstats_Games` WHERE `realgame` = 'tf');

UPDATE IGNORE `hlstats_Awards` SET `code` = 'builtobject_obj_attachment_sapper' WHERE `code` = 'builtobject_obj_attachement_sapper';
DELETE FROM `hlstats_Awards` WHERE `code` = 'builtobject_obj_attachement_sapper';

ALTER TABLE `hlstats_Events_Entries`
ADD KEY `playerId` (`playerId`);

INSERT IGNORE INTO `hlstats_Games_Defaults` (`code`, `parameter`, `value`)
(SELECT `code`, 'ConnectAnnounce','1' FROM `hlstats_Games`);

INSERT IGNORE INTO `hlstats_Games_Defaults` (`code`, `parameter`, `value`)
(SELECT `code`, 'UpdateHostname','1' FROM `hlstats_Games`);

INSERT IGNORE INTO `hlstats_Games_Defaults` (`code`, `parameter`, `value`)
(SELECT `code`, 'DefaultDisplayEvents','1' FROM `hlstats_Games`);

UPDATE `hlstats_Games_Defaults` SET `value` = 0 WHERE `code` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'l4d');

ALTER TABLE `hlstats_Livestats`
CHANGE `cli_city` `cli_city` varchar(64) NOT NULL default '',
CHANGE `cli_country` `cli_country` varchar(64) NOT NULL default '',
CHANGE `cli_state` `cli_state` varchar(64) NOT NULL default '',
CHANGE `steam_id` `steam_id` varchar(64) NOT NULL default '',
CHANGE `name` `name` varchar(64) NOT NULL;

INSERT IGNORE INTO `hlstats_Options` (`keyname`, `value`, `opttype`) VALUES
('dbversion', '1.5.3', 2);

ALTER TABLE `hlstats_PlayerNames`
CHANGE `name` `name` varchar(64) NOT NULL default '';

UPDATE IGNORE `hlstats_Ribbons` SET `awardCode` = 'builtobject_obj_attachment_sapper' WHERE `awardCode` = 'builtobject_obj_attachement_sapper';
UPDATE IGNORE `hlstats_Ribbons` SET `image` = replace(image, 'attachement','attachment') WHERE `image` LIKE '%attachement%';

ALTER TABLE `hlstats_Roles`
CHANGE `name` `name` varchar(64) NOT NULL default '';

INSERT IGNORE INTO `hlstats_Roles` (`game`, `code`, `name`, `hidden`) VALUES
('ges', 'jaws', 'Jaws', '0'),
('ges', 'bond', 'Bond', '0'),
('ges', 'boris', 'Boris', '0'),
('ges', 'Mayday', 'May Day', '0'),
('ges', 'Mishkin', 'Mishkin', '0'),
('ges', 'oddjob', 'Oddjob', '0'),
('ges', 'ourumov', 'Ourumov', '0'),
('ges', 'samedi', 'Samedi', '0'),
('ges', 'valentin', 'Valentin', '0');

ALTER TABLE `hlstats_Servers`
CHANGE `city` `city` varchar(64) NOT NULL default '',
CHANGE `country` `country` varchar(64) NOT NULL default '';

INSERT IGNORE INTO `hlstats_Servers_Config` (`serverid`, `parameter`, `value`)
(SELECT `serverId`, 'ConnectAnnounce','1' FROM `hlstats_Servers`);

INSERT IGNORE INTO `hlstats_Servers_Config` (`serverid`, `parameter`, `value`)
(SELECT `serverId`, 'UpdateHostname','1' FROM `hlstats_Servers`);

INSERT IGNORE INTO `hlstats_Servers_Config` (`serverid`, `parameter`, `value`)
(SELECT `serverId`, 'DefaultDisplayEvents','1' FROM `hlstats_Servers`);
  
INSERT IGNORE INTO `hlstats_Servers_Config_Default` (`parameter`, `value`, `description`) VALUES
('ConnectAnnounce', '1', 'Toggle display of message upon each player connect showing player points/kills, rank, and country of origin. 1=on(default) 0=off'),
('UpdateHostname', '1', 'Toggles auto-update of server name from hostname cvar 1=on(default) 0=off'),
('DefaultDisplayEvents', '1', 'Toggle players default option to see kill/event messages on server. 1=on(default) 0=off');

UPDATE `hlstats_Weapons` SET `name` = 'Sentry Gun (Level 1)' WHERE `code` = 'obj_sentrygun' AND `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` = 'tf');

INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`)
(SELECT `code`, 'taunt_scout', 'Grand Slam', 3 FROM `hlstats_Games` WHERE `realgame` = 'tf');

INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`)
(SELECT `code`, 'bat_wood', 'The Sandman', 1.75 FROM `hlstats_Games` WHERE `realgame` = 'tf');

INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`)
(SELECT `code`, 'obj_sentrygun2', 'Sentry Gun (Level 2)', 3 FROM `hlstats_Games` WHERE `realgame` = 'tf');

INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`)
(SELECT `code`, 'obj_sentrygun3', 'Sentry Gun (Level 3)', 3 FROM `hlstats_Games` WHERE `realgame` = 'tf');