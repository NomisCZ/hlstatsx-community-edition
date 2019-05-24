/*
:? Reason: Some changes taken from another version ...
:i Info: Add mmrank column for CS:GO (csgo_elorank plugin)
:! Change: Add new column 'mmrank'
*/
ALTER TABLE `hlstats_Players` ADD `mmrank` TINYINT NULL DEFAULT NULL AFTER `icq`;