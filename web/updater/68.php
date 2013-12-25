<?php
    if ( !defined('IN_UPDATER') )
    {
        die('Do not access this file directly.');
    }

    $dbversion = 68;
    $version = "1.6.16";

    // Tracker #1487 - Add Nuclear Dawn support
    print "#1487 - Updating Nuclear Dawn game support.<br />";
    $db->query("DELETE FROM `hlstats_Ranks` WHERE `game` = 'nd';");
    $db->query("OPTIMIZE TABLE `hlstats_Ranks`;");
    $db->query("
        INSERT INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`) VALUES
            ('nd', 'nd_01', 0, 40, 'Survivor'),
            ('nd', 'nd_02', 41, 131, 'Private I'),
            ('nd', 'nd_03', 132, 272, 'Private II'),
            ('nd', 'nd_04', 273, 483, 'Private III'),
            ('nd', 'nd_05', 484, 764, 'Private First Class I'),
            ('nd', 'nd_06', 765, 1125, 'Private First Class II'),
            ('nd', 'nd_07', 1126, 1576, 'Private First Class III'),
            ('nd', 'nd_08', 1577, 2117, 'Lance Corporal I'),
            ('nd', 'nd_09', 2118, 2768, 'Lance Corporal II'),
            ('nd', 'nd_10', 2769, 3529, 'Lance Corporal III'),
            ('nd', 'nd_11', 3530, 4410, 'Corporal I'),
            ('nd', 'nd_12', 4411, 5421, 'Corporal II'),
            ('nd', 'nd_13', 5422, 6562, 'Corporal III'),
            ('nd', 'nd_14', 6563, 7853, 'Sergeant I'),
            ('nd', 'nd_15', 7854, 9294, 'Sergeant II'),
            ('nd', 'nd_16', 9295, 10895, 'Sergeant III'),
            ('nd', 'nd_17', 10896, 12666, 'Staff Sergeant I'),
            ('nd', 'nd_18', 12667, 14607, 'Staff Sergeant II'),
            ('nd', 'nd_19', 14608, 16738, 'Staff Sergeant III'),
            ('nd', 'nd_20', 16739, 19059, 'Gunnery Sergeant I'),
            ('nd', 'nd_21', 19060, 21580, 'Gunnery Sergeant II'),
            ('nd', 'nd_22', 21581, 24311, 'Gunnery Sergeant III'),
            ('nd', 'nd_23', 24312, 27252, 'Master Sergeant I'),
            ('nd', 'nd_24', 27253, 30423, 'Master Sergeant II'),
            ('nd', 'nd_25', 30424, 33824, 'Master Sergeant III'),
            ('nd', 'nd_26', 33825, 37465, 'First Sergeant I'),
            ('nd', 'nd_27', 37466, 41356, 'First Sergeant II'),
            ('nd', 'nd_28', 41357, 45497, 'First Sergeant III'),
            ('nd', 'nd_29', 45498, 49928, 'Master Gunnery Sergeant I'),
            ('nd', 'nd_30', 49929, 54669, 'Master Gunnery Sergeant II'),
            ('nd', 'nd_31', 54670, 59750, 'Master Gunnery Sergeant III'),
            ('nd', 'nd_32', 59751, 65201, 'Sergeant Major I'),
            ('nd', 'nd_33', 65202, 71062, 'Sergeant Major II'),
            ('nd', 'nd_34', 71063, 77343, 'Sergeant Major III'),
            ('nd', 'nd_35', 77344, 84054, 'Elite Sergeant Major'),
            ('nd', 'nd_36', 84055, 91215, 'Field Lieutenant'),
            ('nd', 'nd_37', 91216, 98826, 'Second Lieutenant'),
            ('nd', 'nd_38', 98827, 106907, 'First Lieutenant'),
            ('nd', 'nd_39', 106908, 115478, 'Field Captain'),
            ('nd', 'nd_40', 115479, 124549, 'Captain'),
            ('nd', 'nd_41', 124550, 134130, 'Vanguard Captain'),
            ('nd', 'nd_42', 134131, 144231, 'Field Major'),
            ('nd', 'nd_43', 144232, 154862, 'Major'),
            ('nd', 'nd_44', 154863, 166043, 'Lieutenant Colonel'),
            ('nd', 'nd_45', 166044, 177794, 'Colonel'),
            ('nd', 'nd_46', 177795, 190115, 'Vanguard Colonel'),
            ('nd', 'nd_47', 190116, 203026, 'Commander'),
            ('nd', 'nd_48', 203027, 216537, 'Vanguard Commander'),
            ('nd', 'nd_49', 216538, 230658, 'Elite Commander'),
            ('nd', 'nd_50', 230659, 245409, 'Brigadier General Third Class'),
            ('nd', 'nd_51', 245410, 260800, 'Brigadier General Second Class'),
            ('nd', 'nd_52', 260801, 276841, 'Brigadier General First Class'),
            ('nd', 'nd_53', 276842, 293552, 'Major General Third Class'),
            ('nd', 'nd_54', 293553, 310943, 'Major General Second Class'),
            ('nd', 'nd_55', 310944, 329024, 'Major General First Class'),
            ('nd', 'nd_56', 329025, 347815, 'Lieutenant General Third Class'),
            ('nd', 'nd_57', 347816, 367316, 'Lieutenant General Second Class'),
            ('nd', 'nd_58', 367317, 387547, 'Lieutenant General First Class'),
            ('nd', 'nd_59', 387548, 408528, 'General'),
            ('nd', 'nd_60', 408529, 9999999, 'Vanguard General');
    ");
  
  $db->query("UPDATE `hlstats_Awards` SET `name` = 'M-95 L.A.W.S.', `verb` = 'kills with M-95 L.A.W.S.' WHERE `name` = 'M-95 L.A.W.S'");
  $db->query("
        INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
            ('W', 'nd', 'm95', 'M-95 L.A.W.S.', 'kills with M-95 L.A.W.S.'),
            ('W', 'nd', 'sonic turret', 'SONIC TURRET', 'kills with Sonic Turret');
    ");
  
  $db->query("UPDATE `hlstats_Ribbons` SET `ribbonName` = 'Young: M-95 L.A.W.S.' WHERE ribbonName = 'Young: M-95 L.A.W.S'");
  $db->query("UPDATE `hlstats_Ribbons` SET `ribbonName` = 'Bronze: M-95 L.A.W.S.' WHERE ribbonName = 'Bronze: M-95 L.A.W.S'");
  $db->query("UPDATE `hlstats_Ribbons` SET `ribbonName` = 'Silver: M-95 L.A.W.S.' WHERE ribbonName = 'Silver: M-95 L.A.W.S'");
  $db->query("UPDATE `hlstats_Ribbons` SET `ribbonName` = 'Golden: M-95 L.A.W.S.' WHERE ribbonName = 'Golden: M-95 L.A.W.S'");
  $db->query("UPDATE `hlstats_Ribbons` SET `ribbonName` = 'Platinum: M-95 L.A.W.S.' WHERE ribbonName = 'Platinum: M-95 L.A.W.S'");
  $db->query("UPDATE `hlstats_Ribbons` SET `ribbonName` = 'Bloody: M-95 L.A.W.S.' WHERE ribbonName = 'Bloody: M-95 L.A.W.S'");
  $db->query("
        INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES
            ('sonic turret', 1, 0, 'nd', '1_sonic turret.png', 'Young: Sonic Turret'),
            ('sonic turret', 5, 0, 'nd', '2_sonic turret.png', 'Bronze: Sonic Turret'),
            ('sonic turret', 15, 0, 'nd', '3_sonic turret.png', 'Silver: Sonic Turret'),
            ('sonic turret', 30, 0, 'nd', '4_sonic turret.png', 'Golden: Sonic Turret'),
            ('sonic turret', 50, 0, 'nd', '5_sonic turret.png', 'Platinum: Sonic Turret'),
            ('sonic turret', 75, 0, 'nd', '6_sonic turret.png', 'Bloody: Sonic Turret');
    ");
  
    $db->query("UPDATE `hlstats_Weapons` SET `modifier`=1 WHERE `game`='nd' AND `code`='armblade'");
    $db->query("UPDATE `hlstats_Weapons` SET `modifier`=1 WHERE `game`='nd' AND `code`='armknives'");
    $db->query("UPDATE `hlstats_Weapons` SET `modifier`=0.7 WHERE `game`='nd' AND `code`='artillery'");
    $db->query("UPDATE `hlstats_Weapons` SET `modifier`=0.6 WHERE `game`='nd' AND `code`='commander damage'");
    $db->query("UPDATE `hlstats_Weapons` SET `modifier`=1.5 WHERE `game`='nd' AND `code`='env_explosion'");
    $db->query("UPDATE `hlstats_Weapons` SET `modifier`=0.4 WHERE `game`='nd' AND `code`='flamethrower turret'");
    $db->query("UPDATE `hlstats_Weapons` SET `modifier`=1.5 WHERE `game`='nd' AND `code`='grenade launcher'");
    $db->query("UPDATE `hlstats_Weapons` SET `modifier`=1 WHERE `game`='nd' AND `code`='m95'");
    $db->query("UPDATE `hlstats_Weapons` SET `modifier`=0.4 WHERE `game`='nd' AND `code`='mg turret'");
    $db->query("UPDATE `hlstats_Weapons` SET `modifier`=1 WHERE `game`='nd' AND `code`='paladin'");
    $db->query("UPDATE `hlstats_Weapons` SET `modifier`=1 WHERE `game`='nd' AND `code`='psg'");
    $db->query("UPDATE `hlstats_Weapons` SET `modifier`=1.5 WHERE `game`='nd' AND `code`='R.E.D.'");
    $db->query("UPDATE `hlstats_Weapons` SET `modifier`=5 WHERE `game`='nd' AND `code`='repair tool'");
    $db->query("UPDATE `hlstats_Weapons` SET `modifier`=0.4 WHERE `game`='nd' AND `code`='rocket turret'");
    $db->query("
        INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
            ('nd', 'sonic turret', 'Sonic Turrent', 0.40),
            ('nd', 'world', 'World', 1.00);
    ");
    
    // Tracker #1456 - Change all instances of server_id to INT(10) to standardize across database.
    print "#1456 - Updating server_id columns<br />";
    $db->query("ALTER IGNORE TABLE `hlstats_server_load` MODIFY `server_id` INTEGER(10);");
    $db->query("ALTER IGNORE TABLE `hlstats_Livestats` MODIFY `server_id` INTEGER(10);");
    
    // Perform database schema update notification
    print "Updating database and verion schema numbers.<br />";
    $db->query("UPDATE hlstats_Options SET `value` = '$version' WHERE `keyname` = 'version'");
    $db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>