INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`)
(SELECT code, 'killed_charged_medic', 2, 0, '', 'Killed charged medic', '1', '0', '0', '0' FROM hlstats_Games WHERE `realgame` = 'tf');

UPDATE hlstats_Options SET opttype = 2 WHERE keyname = 'Proxy_Daemons';

INSERT IGNORE INTO `hlstats_Options` (`keyname`, `value`, `opttype`) VALUES
('Proxy_Key', '', 1),
('Proxy_Daemons', '', 2);

INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'O',code,'capblock','Capture Blocked','flags blocked' FROM hlstats_Games WHERE `realgame` = 'dods');
INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'W',code,'30cal','.30 Caliber Machine Gun','kills with .30 Caliber Machine Gun' FROM hlstats_Games WHERE `realgame` = 'dods');
INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'W',code,'c96','Pistol c96','kills with Pistol c96' FROM hlstats_Games WHERE `realgame` = 'dods');
INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'W',code,'k98','Mauser Kar 98k','kills with Mauser Kar 98k' FROM hlstats_Games WHERE `realgame` = 'dods');
INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'W',code,'k98_scoped','Mauser Karbiner k98 Sniper Rifle','kills with Mauser Karbiner k98 Sniper Rifle' FROM hlstats_Games WHERE `realgame` = 'dods');
INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'W',code,'m1carbine','M1 Carbine','kills with M1 Carbine' FROM hlstats_Games WHERE `realgame` = 'dods');
INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'W',code,'p38','Pistol 38','kills with Pistol 38' FROM hlstats_Games WHERE `realgame` = 'dods');
INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'W',code,'riflegren_ger','German Rifle Grenade','kills with German Rifle Grenade' FROM hlstats_Games WHERE `realgame` = 'dods');
INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'W',code,'riflegren_us','M1 Garand Rifle','kills with M1 Garand Rifle' FROM hlstats_Games WHERE `realgame` = 'dods');
INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'W',code,'smoke_us','U.S. Smoke Grenade','kills with the U.S. Smoke Grenade' FROM hlstats_Games WHERE `realgame` = 'dods');
INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'W',code,'smoke_ger','German Smoke Grenade','kills with the German Smoke Grenade' FROM hlstats_Games WHERE `realgame` = 'dods');

INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'W',code,'backstab', 'Backstabber', 'backstab kills' FROM hlstats_Games WHERE `realgame` = 'ff');


DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_airport01_greenhouse";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_airport01_greenhouse";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_airport03_offices";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_airport04_terminal";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_airport05_runway";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_farm01_hilltop";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_farm02_traintunnel";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_farm03_bridge";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_farm04_barn";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_farm05_cornfield";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_hospital01_apartment";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_hospital02_subway";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_hospital03_sewers";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_hospital04_interior";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_hospital05_rooftop";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_smalltown01_caves";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_smalltown02_drainage";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_smalltown03_ranchhouse";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_smalltown04_mainstreet";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_smalltown05_houseboat";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_vs_airport01_greenhouse";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_vs_airport01_greenhouse";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_vs_airport03_offices";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_vs_airport04_terminal";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_vs_airport05_runway";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_vs_farm01_hilltop";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_vs_farm02_traintunnel";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_vs_farm03_bridge";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_vs_farm04_barn";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_vs_farm05_cornfield";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_vs_hospital01_apartment";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_vs_hospital02_subway";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_vs_hospital03_sewers";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_vs_hospital04_interior";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_vs_hospital05_rooftop";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_vs_smalltown01_caves";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_vs_smalltown02_drainage";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_vs_smalltown03_ranchhouse";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_vs_smalltown04_mainstreet";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_vs_smalltown05_houseboat";
DELETE IGNORE FROM `hlstats_Heatmap_Config` WHERE `game` = "l4d" AND `map` = "l4d_sv_lighthouse";


INSERT IGNORE INTO `hlstats_Heatmap_Config` (`map`, `game`, `xoffset`, `yoffset`, `flipx`, `flipy`, `days`, `brush`, `scale`, `font`, `thumbw`, `thumbh`, `cropx1`, `cropy1`, `cropx2`, `cropy2`) VALUES
('koth_viaduct','tf', 7074, 3773, 0, 1, 30, 'small', 8, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('koth_sawmill','tf', 4604, 4094, 0, 1, 30, 'small', 8, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('koth_nucleus','tf', 3156, 2520, 0, 1, 30, 'small', 5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('ctf_sawmill','tf', 4603, 4073, 0, 1, 30, 'small', 8, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('arena_offblast_final','tf', 1920, 1536, 0, 1, 30, 'small', 3, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('cp_yukon_final','tf', 6602, 5123, 0, 1, 30, 'small', 10, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('2tpl_mine_alpine','tf', 1238, 1462, 0, 1, 30, 'small', 4, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('cp_blackmesa','tf', 4110, 1755, 0, 1, 30, 'small', 5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('cp_bloodstained','tf', 7182, 5447, 0, 1, 30, 'small', 12, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('cp_corporation_b2','tf', 5272, 3113, 0, 1, 30, 'small', 7, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('cp_frontline_a1','tf', 6534, 6439, 0, 1, 30, 'small', 9, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('cp_furnace_b3','tf', 6114, 5236, 0, 1, 30, 'small', 9, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('fy_twotowers','css',591,1561,1,1,30,'small',2.5,10,0.170312,0.170312,0,0,0,0),
('fy_twotowers32','css',591,1561,1,1,30,'small',2.5,10,0.170312,0.170312,0,0,0,0),
('fy_twotowers2009','css',591,1561,1,1,30,'small',2.5,10,0.170312,0.170312,0,0,0,0),
('de_alberta', 'css', 4187, 2071, 0, 1, 30, 'small', 5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('de_alivemetal', 'css', 1158, 1221, 0, 1, 30, 'small', 2.9, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('de_boston', 'css', 3116, 1189, 0, 1, 30, 'small', 4.4, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('de_carpediem_arena', 'css', 1961, 1262, 0, 1, 30, 'small', 3.2, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('de_deltamill2', 'css', 2388, 652, 0, 1, 30, 'small', 4, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('de_gristmill', 'css', 1724, -72, 0, 1, 30, 'small', 2.7, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('de_losttemple_pro', 'css', 2495, 1838, 0, 1, 30, 'small', 5.1, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('de_nightfever', 'css', 3262, 4871, 0, 1, 30, 'small', 6, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('de_pira_legos', 'css', 2964, 3027, 0, 1, 30, 'small', 4.3, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('de_rumpeldust2', 'css', 3678, 3925, 0, 1, 30, 'small', 5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('de_sandland', 'css', 2795, 2344, 0, 1, 30, 'small', 4.6, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('de_snowcapped', 'css', 2248, 2633, 0, 1, 30, 'small', 4, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('de_toxin', 'css', 3394, 2023, 0, 1, 30, 'small', 5.4, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('de_winery_final', 'css', 2911, 2014, 0, 1, 30, 'small', 4, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dod_anzio', 'dod', 50.97, -82.75, 0, 1, 30, 'small', 1.01, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dod_avalanche', 'dod', 424, 160, 0, 1, 30, 'small', 1.58, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dod_charlie', 'dod', 32, 0, 1, 1, 30, 'small', 0.77, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dod_chemille', 'dod', 692, -568, 1, 1, 30, 'small', 1.19, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dod_donner', 'dod', 192, -1248, 1, 1, 30, 'small', 1.11, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dod_escape', 'dod', 176, 410, 1, 1, 30, 'small', 1.26, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dod_falaise', 'dod', 36.5, 227, 0, 1, 30, 'small', 0.82, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dod_flash', 'dod', 295.99, -504, 1, 1, 30, 'small', 1.19, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dod_flugplatz', 'dod', 199.5, -33, 0, 1, 30, 'small', 0.85, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dod_forest', 'dod', 492.5, 361, 1, 1, 30, 'small', 0.9, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dod_glider', 'dod', 426, 308, 0, 1, 30, 'small', 1.14, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dod_jagd', 'dod', 64, 40, 0, 1, 30, 'small', 0.86, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dod_kalt', 'dod', 0, -504, 0, 1, 30, 'small', 1.22, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dod_kraftstoff', 'dod', 656, -308, 0, 1, 30, 'small', 1.1, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dod_merderet', 'dod', 532, 409, 1, 1, 30, 'small', 0.93, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dod_northbound', 'dod', 83.5, -10.5, 1, 1, 30, 'small', 1.01, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dod_saints', 'dod', 704, 196, 1, 1, 30, 'small', 1.25, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dod_sturm', 'dod', 210, 546, 1, 1, 30, 'small', 1.06, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dod_switch', 'dod', 1146, 582.5, 0, 1, 30, 'small', 1.19, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dod_vicenza', 'dod', 48, -48, 1, 1, 30, 'small', 1.05, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dod_zalec', 'dod', 8, -48, 1, 1, 30, 'small', 0.77, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dod_cean', 'dod', 540, 259, 0, 1, 30, 'small', 0.87, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('co_angst', 'ns', 828, 376, 1, 1, 30, 'small', 1.42, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('co_core', 'ns', 146, -472, 1, 1, 30, 'small', 2.25, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('co_daimos', 'ns', 968, -128, 1, 1, 30, 'small', 1.17, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('co_faceoff', 'ns', 1184, -368, 0, 1, 30, 'small', 1.48, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('co_kestrel', 'ns', 962, 1648, 1, 1, 30, 'small', 1.42, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('co_niveus', 'ns', 384, 1728, 0, 1, 30, 'small', 1.27, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('co_pulse', 'ns', 764, -268, 1, 1, 30, 'small', 1.83, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('co_sava', 'ns', 310, 974, 0, 1, 30, 'small', 1.09, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('co_ulysses', 'ns', 688, -1056, 1, 1, 30, 'small', 1.64, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('co_umbra', 'ns', 1760, 192, 0, 1, 30, 'small', 1.21, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('ns_altair', 'ns', 360, 24, 1, 1, 30, 'small', 0.92, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('ns_ayumi', 'ns', 260, -512, 0, 1, 30, 'small', 1.21, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('ns_bast', 'ns', 152.5, 63, 1, 1, 30, 'small', 0.9, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('ns_caged', 'ns', 256, 456, 1, 1, 30, 'small', 0.95, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('ns_eclipse', 'ns', 164, -40, 1, 1, 30, 'small', 0.91, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('ns_eon', 'ns', 23, 91.5, 1, 1, 30, 'small', 0.92, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('ns_hera', 'ns', 180, -24, 1, 1, 30, 'small', 0.83, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('ns_lost', 'ns', 12, 256, 0, 1, 30, 'small', 1.28, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('ns_lucid', 'ns', 380, -632, 1, 1, 30, 'small', 1.04, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('ns_machina', 'ns', 112, -404, 0, 1, 30, 'small', 0.86, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('ns_metal', 'ns', 164, 388, 0, 1, 30, 'small', 0.91, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('ns_nancy', 'ns', 170, 0, 1, 1, 30, 'small', 0.84, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('ns_nothing', 'ns', 212, 176, 1, 1, 30, 'small', 0.88, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('ns_origin', 'ns', 20, -344, 0, 1, 30, 'small', 0.97, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('ns_shiva', 'ns', 40, 480, 0, 1, 30, 'small', 0.88, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('ns_tanith', 'ns', 44, -4, 0, 1, 30, 'small', 1.03, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('ns_veil', 'ns', 160, 144, 0, 1, 30, 'small', 0.91, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('tls_abydos', 'sgtls', 13395, 7390, 1, 1, 30, 'small', 16, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('tls_erebus', 'sgtls', 17564, 11518, 1, 1, 30, 'small', 24, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('tls_lockdown', 'sgtls', 989, 1517, 1, 1, 30, 'small', 4, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('2fort', 'tfc', 90.63, 0, 0, 1, 30, 'small', 1.03, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('avanti', 'tfc', 304, 192, 0, 1, 30, 'small', 1.17, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('badlands', 'tfc', 8, 0, 1, 1, 30, 'small', 0.95, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('casbah', 'tfc', 552, -124, 0, 1, 30, 'small', 1.1, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('crossover2', 'tfc', 0, 0, 0, 1, 30, 'small', 1.11, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('cz2', 'tfc', 144, 256, 0, 1, 30, 'small', 1.15, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('dustbowl', 'tfc', 448, 288, 1, 1, 30, 'small', 0.98, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('epicenter', 'tfc', 416, 0, 0, 1, 30, 'small', 1.32, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('flagrun', 'tfc', 0, 704, 0, 1, 30, 'small', 1.07, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('hunted', 'tfc', 432, 76, 1, 1, 30, 'small', 1.42, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('push', 'tfc', 0, 0, 0, 1, 30, 'small', 1.11, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('ravelin', 'tfc', 0, 0, 0, 1, 30, 'small', 1.04, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('rock2', 'tfc', 0, 0, 0, 1, 30, 'small', 0.98, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('warpath', 'tfc', 112, 0, 0, 1, 30, 'small', 1.02, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('well', 'tfc', 0, 0, 0, 1, 30, 'small', 0.94, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_airport01_greenhouse', 'l4d', 175, 5272, 0, 1, 30, 'small', 10, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_airport02_offices', 'l4d', -1723, 8693, 0, 1, 30, 'small', 9, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_airport03_garage', 'l4d', 11413, 6781, 0, 1, 30, 'small', 12, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_airport04_terminal', 'l4d', 2659, 6795, 0, 1, 30, 'small', 7, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_airport05_runway', 'l4d', 8197, 12894, 0, 1, 30, 'small', 5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_farm01_hilltop', 'l4d', 16423, -3193, 0, 1, 30, 'small', 13, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_farm02_traintunnel', 'l4d', 12106, -2749, 0, 1, 30, 'small', 10, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_farm03_bridge', 'l4d', 2307, -5227, 0, 1, 30, 'small', 12, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_farm04_barn', 'l4d', 1584, 2778, 0, 1, 30, 'small', 17, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_farm05_cornfield', 'l4d', -1851, 7619, 0, 1, 30, 'small', 13, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_hospital01_apartment', 'l4d', 662, 5571, 0, 1, 30, 'small', 5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_hospital02_subway', 'l4d', -1621, 8552, 0, 1, 30, 'small', 8, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_hospital03_sewers', 'l4d', -7045, 14101, 0, 1, 30, 'small', 10, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_hospital04_interior', 'l4d', -9513, 16235, 0, 1, 30, 'small', 5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_hospital05_rooftop', 'l4d', -4450, 10005, 0, 1, 30, 'small', 3, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_smalltown01_caves', 'l4d', 20542, -3156, 0, 1, 30, 'small', 13, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_smalltown03_ranchhouse', 'l4d', 16026, 2930, 0, 1, 30, 'small', 14, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_smalltown05_houseboat', 'l4d', 6554, 5155, 0, 1, 30, 'small', 12, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_sv_lighthouse', 'l4d', 4681, 2143, 0, 1, 30, 'small', 7, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_vs_airport01_greenhouse', 'l4d', 175, 5272, 0, 1, 30, 'small', 10, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_vs_airport02_offices', 'l4d', -1723, 8693, 0, 1, 30, 'small', 9, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_vs_airport03_garage', 'l4d', 11413, 6781, 0, 1, 30, 'small', 12, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_vs_airport04_terminal', 'l4d', 2659, 6795, 0, 1, 30, 'small', 7, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_vs_airport05_runway', 'l4d', 8197, 12894, 0, 1, 30, 'small', 5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_vs_farm01_hilltop', 'l4d', 16423, -3193, 0, 1, 30, 'small', 13, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_vs_farm02_traintunnel', 'l4d', 12106, -2749, 0, 1, 30, 'small', 10, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_vs_farm03_bridge', 'l4d', 2307, -5227, 0, 1, 30, 'small', 12, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_vs_farm04_barn', 'l4d', 1584, 2778, 0, 1, 30, 'small', 17, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_vs_farm05_cornfield', 'l4d', -1851, 7619, 0, 1, 30, 'small', 13, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_vs_hospital01_apartment', 'l4d', 662, 5571, 0, 1, 30, 'small', 5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_vs_hospital02_subway', 'l4d', -1621, 8552, 0, 1, 30, 'small', 8, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_vs_hospital03_sewers', 'l4d', -7045, 14101, 0, 1, 30, 'small', 10, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_vs_hospital04_interior', 'l4d', -9513, 16235, 0, 1, 30, 'small', 5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_vs_hospital05_rooftop', 'l4d', -4450, 10005, 0, 1, 30, 'small', 3, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_vs_smalltown01_caves', 'l4d', 20542, -3156, 0, 1, 30, 'small', 13, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_vs_smalltown03_ranchhouse', 'l4d', 16026, 2930, 0, 1, 30, 'small', 14, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('l4d_vs_smalltown05_houseboat', 'l4d', 6554, 5155, 0, 1, 30, 'small', 12, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('cp_freight','tf', 4470, 3520, 0, 1, 30, 'small', 7, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('cp_freight_final','tf', 4477, 3517, 0, 1, 30, 'small', 7, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('fy_iceworld','tf', 1600, 1279, 0, 1, 30, 'small', 2.5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
('cp_orange_x3','tf', 5321, 7321, 0, 1, 30, 'small', 7, 10, 0.170312, 0.170312, 0, 0, 0, 0);

ALTER TABLE `hlstats_Players`
	DROP INDEX `clan`,
	ADD INDEX `skill` (`skill`),
	ADD INDEX `game` (`game`),
	ADD INDEX `kills` (`kills`);

ALTER TABLE `hlstats_Events_Frags`
	ADD INDEX `weapon16` (`weapon`(16)),
	ADD INDEX `killerRole` (`killerRole`(8));

ALTER TABLE `hlstats_Events_PlayerPlayerActions`
	ADD INDEX `actionId` (`actionId`);
	
ALTER TABLE `hlstats_Players_History`
	ADD INDEX `playerId` (`playerId`);
	
ALTER TABLE `hlstats_Ranks`
	ADD INDEX `game` (`game`(8));

ALTER TABLE `hlstats_Servers_VoiceComm`
	DROP INDEX `address`,
	ADD UNIQUE `address` ( `addr` , `queryPort` );
	
ALTER TABLE `hlstats_PlayerNames`
	ADD INDEX `name16` (`name`(16));

ALTER TABLE  `hlstats_Heatmap_Config` ADD  `rotate` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `flipy` ;

INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`)
(SELECT code, 'death_sawblade', 0, 0, '', 'LOL SAW''D', '1', '0', '0', '0' FROM hlstats_Games WHERE `realgame` = 'tf');

INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`)
(SELECT 'deflect_arrow',1,0,code,'1_deflect_arrow.png','Bronze Deflected Arrow' FROM hlstats_Games WHERE `realgame` = 'tf');
INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`)
(SELECT 'deflect_arrow',5,0,code,'2_deflect_arrow.png','Silver Deflected Arrow' FROM hlstats_Games WHERE `realgame` = 'tf');
INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`)
(SELECT 'deflect_arrow',10,0,code,'3_deflect_arrow.png','Gold Deflected Arrow' FROM hlstats_Games WHERE `realgame` = 'tf');

INSERT INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`)
(SELECT code, 'backstab', 'Backstab', 1 FROM hlstats_Games WHERE realgame = 'ff');

INSERT INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`)
(SELECT code, 'telefrag', 'Telefrag', 2 FROM hlstats_Games WHERE realgame = 'tf');

INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`)
(SELECT 'W',code,'telefrag', 'Lucky Duck', 'kills by telefrag' FROM hlstats_Games WHERE `realgame` = 'tf');

INSERT IGNORE INTO `hlstats_Options_Choices` (`keyname`, `value`, `text`, `isDefault`) VALUES 
('google_map_region', 'ROMANIA', 'Romania', 0);

UPDATE `hlstats_Options` SET `value` = '1.6.1' WHERE `keyname` = 'version';