/*
:? Reason: Some changes taken from another version ...
:i Info: Add mmrank column for CS:GO (csgo_elorank plugin)
:! Change: Add new column 'mmrank'
*/
ALTER TABLE `hlstats_Players` ADD `mmrank` TINYINT NULL DEFAULT NULL AFTER `icq`;

/*
:? Reason: Update old version number
:i Info: New PHP 7.x version needs new version number
:! Change: Update version option
*/
UPDATE `hlstats_Options` SET `value` = '1.7.0' WHERE `keyname` = 'version';