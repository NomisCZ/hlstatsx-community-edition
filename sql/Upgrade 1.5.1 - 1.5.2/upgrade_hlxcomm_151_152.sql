INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
('W','cstrike','grenade','Grenadier','kills with grenade');

UPDATE `hlstats_Options` SET `opttype` = 2 WHERE `keyname` IN ('awards_d_date','awards_numdays');

INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES
('killed_a_hostage',5,0,'cstrike','1_killed_a_hostage.png','Award of Hostage Killing'),
('rescued_a_hostage',5,0,'cstrike','1_rescued_a_hostage.png','Award of Hostage Rescue'),
('planted_the_bomb',5,0,'cstrike','1_planted_the_bomb.png','Award of Planting the Bomb'),
('grenade',5,0,'cstrike','1_grenade.png','Award of Grenade'),
('defused_the_bomb',5,0,'cstrike','1_defused_the_bomb.png','Award of Defusing');

UPDATE `hlstats_Servers_Config_Default` SET `description` = 'If enabled, bots are not tracked 1=on 0=off(default).' WHERE `parameter` = 'IgnoreBots';
UPDATE `hlstats_Servers_Config_Default` SET `description` = 'Valid values are SOURCEMOD, MINISTATS, BEETLE, MANI, and AMXX if one of such plugins are installed.' WHERE `parameter` = 'Mod';