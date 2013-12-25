INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES
('insmod', 'headshot', 1, 0, '', 'Headshot Kill', '1', '', '', ''),
('insmod', 'Round_Win', 0, 10, '', 'Round Win', '', '', '1', ''),
('ges', 'GE_AWARD_DEADLY', 10, 0, '', 'Most Deadly', '1', '', '', ''),
('ges', 'GE_AWARD_HONORABLE', 5, 0, '', 'Most Honorable', '1', '', '', ''),
('ges', 'GE_AWARD_PROFESSIONAL', 10, 0, '', 'Most Professional', '1', '', '', ''),
('ges', 'GE_AWARD_MARKSMANSHIP', 1, 0, '', 'Marksmanship Award', '1', '', '', ''),
('ges', 'GE_AWARD_AC10', 2, 0, '', 'AC-10 Award', '1', '', '', ''),
('ges', 'GE_AWARD_FRANTIC', 2, 0, '', 'Most Frantic', '1', '', '', ''),
('ges', 'GE_AWARD_WTA', 1, 0, '', 'Where''s the Ammo?', '1', '', '', ''),
('ges', 'GE_AWARD_LEMMING', -1, 0, '', 'Lemming (suicide)', '1', '', '', ''),
('ges', 'GE_AWARD_LONGIN', 1, 0, '', 'Longest Innings', '1', '', '', ''),
('ges', 'GE_AWARD_SHORTIN', -1, 0, '', 'Shortest Innings', '1', '', '', ''),
('ges', 'GE_AWARD_DISHONORABLE', -10, 0, '', 'Most Dishonorable', '1', '', '', ''),
('ges', 'GE_AWARD_NOTAC10', 4, 0, '', 'Where''s the Armor?', '1', '', '', ''),
('ges', 'GE_AWARD_MOSTLYHARMLESS', -1, 0, '', 'Mostly Harmless', '1', '', '', '');

INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
('W', 'ges', 'mostkills', 'Bond, James Bond', 'kills');

INSERT IGNORE INTO `hlstats_Games_Defaults` (`code`, `parameter`, `value`) VALUES
('bg2', 'PlayerEventsCommandHint', ''),
('dystopia', 'PlayerEventsCommandHint', ''),
('ff', 'PlayerEventsCommandHint', ''),
('fof', 'PlayerEventsCommandHint', ''),
('ges', 'PlayerEventsCommandHint', ''),
('hidden', 'PlayerEventsCommandHint', ''),
('insmod', 'PlayerEventsCommandHint', ''),
('l4d', 'PlayerEventsCommandHint', ''),
('sgtls', 'PlayerEventsCommandHint', ''),
('zps', 'PlayerEventsCommandHint', '');

INSERT IGNORE INTO `hlstats_Mods_Defaults` (`code`, `parameter`, `value`) VALUES
('', 'PlayerEventsCommandHint', ''),
('AMXX', 'PlayerEventsCommandHint', 'hlx_amx_hint'),
('BEETLE', 'PlayerEventsCommandHint', ''),
('MANI', 'PlayerEventsCommandHint', 'ma_hlx_hint'),
('MINISTATS', 'PlayerEventsCommandHint', ''),
('SOURCEMOD', 'PlayerEventsCommandHint', 'hlx_sm_hint');

INSERT IGNORE INTO `hlstats_Servers_Config` (`serverId`, `parameter`, `value`)
(SELECT `serverId`, 'PlayerEventsCommandHint', '' FROM hlstats_Servers_Config WHERE `parameter`='MOD' and `value` IN ('', 'BEETLE', 'MINISTATS'));

INSERT IGNORE INTO `hlstats_Servers_Config` (`serverId`, `parameter`, `value`)
(SELECT `serverId`, 'PlayerEventsCommandHint', 'hlx_amx_hint' FROM hlstats_Servers_Config WHERE `parameter`='MOD' and `value` = 'AMXX');

INSERT IGNORE INTO `hlstats_Servers_Config` (`serverId`, `parameter`, `value`)
(SELECT `serverId`, 'PlayerEventsCommandHint', 'ma_hlx_hint' FROM hlstats_Servers_Config WHERE `parameter`='MOD' and `value` = 'MANI');

INSERT IGNORE INTO `hlstats_Servers_Config` (`serverId`, `parameter`, `value`)
(SELECT `serverId`, 'PlayerEventsCommandHint', 'hlx_sm_hint' FROM hlstats_Servers_Config WHERE `parameter`='MOD' and `value` = 'SOURCEMOD');

UPDATE `hlstats_Servers_Config` SET `value` = '' WHERE `parameter` = 'PlayerEventsCommandHint' AND `serverId` IN (SELECT `serverId` FROM `hlstats_Servers` WHERE `game` IN (SELECT `code` FROM `hlstats_Games` WHERE `realgame` IN ('bg2', 'dystopia', 'ff', 'fof', 'ges', 'hidden', 'insmod', 'l4d', 'sgtls', 'zps')));

UPDATE `hlstats_Servers_Config` SET `value` = 'amx_chat' WHERE `parameter` = 'PlayerEventsAdminCommand' AND `value` = '' AND serverId IN (SELECT serverId FROM (SELECT serverId FROM `hlstats_Servers_Config` WHERE `parameter` = 'MOD' AND `value` = 'AMXX') AS x);

UPDATE `hlstats_Servers_Config` SET `value` = 'admin_chat' WHERE `parameter` = 'PlayerEventsAdminCommand' AND `value` = '' AND serverId IN (SELECT serverId FROM (SELECT serverId FROM `hlstats_Servers_Config` WHERE `parameter` = 'MOD' AND `value` = 'BEETLE') AS x);

UPDATE `hlstats_Servers_Config` SET `value` = 'ma_chat' WHERE `parameter` = 'PlayerEventsAdminCommand' AND `value` = '' AND serverId IN (SELECT serverId FROM (SELECT serverId FROM `hlstats_Servers_Config` WHERE `parameter` = 'MOD' AND `value` = 'MANI') AS x);

UPDATE `hlstats_Servers_Config` SET `value` = 'sm_chat' WHERE `parameter` = 'PlayerEventsAdminCommand' AND `value` = '' AND serverId IN (SELECT serverId FROM (SELECT serverId FROM `hlstats_Servers_Config` WHERE `parameter` = 'MOD' AND `value` = 'SOURCEMOD') AS x);

INSERT INTO `hlstats_Servers_Config_Default` (`parameter`, `value`, `description`) VALUES
('PlayerEventsCommandHint', 'hlx_sm_hint', 'The command to display "hint" style messages for ATB switches. Default is "".');

UPDATE `hlstats_Options` SET `value` = '1.5.5' WHERE `keyname` = 'version';
UPDATE `hlstats_Options` SET `value` = '5' WHERE `keyname` = 'dbversion';