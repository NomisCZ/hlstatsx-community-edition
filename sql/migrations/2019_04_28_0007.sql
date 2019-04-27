/*
:? Reason: MySQL 5.7 and later - strict mode 
:i Info: Strict mode affects whether the server permits '0000-00-00' as a valid date
:! Change: Change default value of DATETIME/DATE fields to NULL
*/
ALTER TABLE `hlstats_Events_Admin` CHANGE `eventTime` `eventTime` DATETIME NULL DEFAULT NULL;
ALTER TABLE `hlstats_Events_ChangeName` CHANGE `eventTime` `eventTime` DATETIME NULL DEFAULT NULL;
ALTER TABLE `hlstats_Events_ChangeRole` CHANGE `eventTime` `eventTime` DATETIME NULL DEFAULT NULL;
ALTER TABLE `hlstats_Events_ChangeTeam` CHANGE `eventTime` `eventTime` DATETIME NULL DEFAULT NULL;
ALTER TABLE `hlstats_Events_Chat` CHANGE `eventTime` `eventTime` DATETIME NULL DEFAULT NULL;
ALTER TABLE `hlstats_Events_Connects` CHANGE `eventTime` `eventTime` DATETIME NULL DEFAULT NULL;
ALTER TABLE `hlstats_Events_Disconnects` CHANGE `eventTime` `eventTime` DATETIME NULL DEFAULT NULL;
ALTER TABLE `hlstats_Events_Entries` CHANGE `eventTime` `eventTime` DATETIME NULL DEFAULT NULL;
ALTER TABLE `hlstats_Events_Frags` CHANGE `eventTime` `eventTime` DATETIME NULL DEFAULT NULL;
ALTER TABLE `hlstats_Events_Latency` CHANGE `eventTime` `eventTime` DATETIME NULL DEFAULT NULL;
ALTER TABLE `hlstats_Events_PlayerActions` CHANGE `eventTime` `eventTime` DATETIME NULL DEFAULT NULL;
ALTER TABLE `hlstats_Events_PlayerPlayerActions` CHANGE `eventTime` `eventTime` DATETIME NULL DEFAULT NULL;
ALTER TABLE `hlstats_Events_Rcon` CHANGE `eventTime` `eventTime` DATETIME NULL DEFAULT NULL;
ALTER TABLE `hlstats_Events_Statsme` CHANGE `eventTime` `eventTime` DATETIME NULL DEFAULT NULL;
ALTER TABLE `hlstats_Events_Statsme2` CHANGE `eventTime` `eventTime` DATETIME NULL DEFAULT NULL;
ALTER TABLE `hlstats_Events_StatsmeLatency` CHANGE `eventTime` `eventTime` DATETIME NULL DEFAULT NULL;
ALTER TABLE `hlstats_Events_StatsmeTime` CHANGE `eventTime` `eventTime` DATETIME NULL DEFAULT NULL;
ALTER TABLE `hlstats_Events_Suicides` CHANGE `eventTime` `eventTime` DATETIME NULL DEFAULT NULL;
ALTER TABLE `hlstats_Events_TeamBonuses` CHANGE `eventTime` `eventTime` DATETIME NULL DEFAULT NULL;
ALTER TABLE `hlstats_Events_Teamkills` CHANGE `eventTime` `eventTime` DATETIME NULL DEFAULT NULL;
ALTER TABLE `hlstats_PlayerNames` CHANGE `lastuse` `lastuse` DATETIME NULL DEFAULT NULL;
ALTER TABLE `hlstats_Players_History` CHANGE `eventTime` `eventTime` DATE NULL DEFAULT NULL;

/*
:? Reason: CS:GO - add MP5-SD weapon
:i Info: https://blog.counter-strike.net/index.php/2018/08/20849/
:! Change: Insert new row
*/
INSERT INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES ('csgo', 'mp5sd', 'MP5-SD', '1.00');