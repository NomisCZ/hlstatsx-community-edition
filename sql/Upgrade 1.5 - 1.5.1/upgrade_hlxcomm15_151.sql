ALTER TABLE `hlstats_Actions`
CHANGE `description` `description` varchar(128) default NULL;

ALTER TABLE `hlstats_Events_Admin`
CHANGE `type` `type` varchar(64) NOT NULL default 'Unknown',
CHANGE `message` `message` varchar(128) NOT NULL default '',
CHANGE `playerName` `playerName` varchar(64) NOT NULL default '';

ALTER TABLE `hlstats_Events_ChangeName`
CHANGE `oldName` `oldName` varchar(64) NOT NULL default '',
CHANGE `newName` `newName` varchar(64) NOT NULL default '',
ADD KEY `playerId` (`playerId`);

ALTER TABLE `hlstats_Events_ChangeRole`
ADD KEY `playerId` (`playerId`);

ALTER TABLE `hlstats_Events_ChangeTeam`
ADD KEY `playerId` (`playerId`);

ALTER TABLE `hlstats_Events_Chat`
CHANGE `message` `message` varchar(128) NOT NULL default '';

ALTER TABLE `hlstats_Events_PlayerPlayerActions`
ADD KEY `playerId` (`playerId`);

ALTER TABLE `hlstats_Events_Statsme2`
ADD KEY `weapon` (`weapon`);

ALTER TABLE `hlstats_Events_Suicides`
ADD KEY `playerId` (`playerId`);

ALTER TABLE `hlstats_Events_Teamkills`
ADD KEY `killerId` (`killerId`);

ALTER TABLE `hlstats_Options`
CHANGE `keyname` `keyname` varchar(32) NOT NULL default '',
CHANGE `value` `value` varchar(128) NOT NULL default '';

ALTER TABLE `hlstats_Options_Choices`
CHANGE `keyname` `keyname` varchar(32) NOT NULL;

ALTER TABLE `hlstats_Players`
CHANGE `lastName` `lastName` varchar(64) NOT NULL default '';

ALTER TABLE `hlstats_PlayerUniqueIds`
CHANGE `uniqueId` `uniqueId` varchar(64) NOT NULL default '';