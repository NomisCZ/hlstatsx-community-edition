<?php
    if ( !defined('IN_UPDATER') )
    {
        die('Do not access this file directly.');
    }

    $dbversion = 77;
    $version = "1.6.19-pre3";

    // Tracker #844 - Add some L4D2 heatmaps

    print "Adding L4D2 Heatmap Configurations. (<a href=\"http://tracker.hlxce.com/issues/844\">#844</a>)<br />";
    $db->query("
        INSERT INTO `hlstats_Heatmap_Config` (`map`, `game`, `xoffset`, `yoffset`, `flipx`, `flipy`, `rotate`, `days`, `brush`, `scale`, `font`, `thumbw`, `thumbh`, `cropx1`, `cropy1`, `cropx2`, `cropy2`) VALUES
            ('c1m1_hotel', 'l4d', 1829, 8518, 0, 1, 0, 30, 'small', 5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
            ('c1m2_streets', 'l4d', 13470, 7954, 0, 1, 0, 30, 'small', 14, 10, 0.170312, 0.170312, 0, 0, 0, 0),
            ('c1m3_mall', 'l4d', 2976, 1695, 0, 1, 0, 30, 'small', 8, 10, 0.170312, 0.170312, 0, 0, 0, 0),
            ('c1m4_atrium', 'l4d', 7604, -416, 0, 1, 0, 30, 'small', 5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
            ('c2m1_highway', 'l4d', 8585, 13642, 0, 1, 0, 30, 'small', 20, 10, 0.170312, 0.170312, 0, 0, 0, 0),
            ('c2m2_fairgrounds', 'l4d', 8423, 5363, 0, 1, 0, 30, 'small', 14, 10, 0.170312, 0.170312, 0, 0, 0, 0),
            ('c2m3_coaster', 'l4d', 8935, 6928, 0, 1, 0, 30, 'small', 11, 10, 0.170312, 0.170312, 0, 0, 0, 0),
            ('c2m4_barns', 'l4d', 7466, 8596, 0, 1, 0, 30, 'small', 12, 10, 0.170312, 0.170312, 0, 0, 0, 0),
            ('c2m5_concert', 'l4d', 5267, 5568, 0, 1, 0, 30, 'small', 5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
            ('c3m1_plankcountry', 'l4d', 13418, 12468, 0, 1, 0, 30, 'small', 12, 10, 0.170312, 0.170312, 0, 0, 0, 0),
            ('c3m2_swamp', 'l4d', 12741, 13698, 0, 1, 0, 30, 'small', 20, 10, 0.170312, 0.170312, 0, 0, 0, 0),
            ('c3m3_shantytown', 'l4d', 7582, 3647, 0, 1, 0, 30, 'small', 10, 10, 0.170312, 0.170312, 0, 0, 0, 0),
            ('c3m4_plantation', 'l4d', 6625, 4589, 0, 1, 0, 30, 'small', 10, 10, 0.170312, 0.170312, 0, 0, 0, 0),
            ('c4m1_milltown_a', 'l4d', 9457, 10870, 0, 1, 0, 30, 'small', 14, 10, 0.170312, 0.170312, 0, 0, 0, 0),
            ('c4m2_sugarmill_a', 'l4d', 10459, 2124, 0, 1, 0, 30, 'small', 18, 10, 0.170312, 0.170312, 0, 0, 0, 0),
            ('c4m3_sugarmill_b', 'l4d', 10156, 1946, 0, 1, 0, 30, 'small', 18, 10, 0.170312, 0.170312, 0, 0, 0, 0),
            ('c4m4_milltown_b', 'l4d', 11414, 11943, 0, 1, 0, 30, 'small', 16, 10, 0.170312, 0.170312, 0, 0, 0, 0),
            ('c4m5_milltown_escape', 'l4d', 11073, 11978, 0, 1, 0, 30, 'small', 16, 10, 0.170312, 0.170312, 0, 0, 0, 0),
            ('c5m1_waterfront', 'l4d', 7786, 4019, 0, 1, 0, 30, 'small', 9, 10, 0.170312, 0.170312, 0, 0, 0, 0),
            ('c5m2_park', 'l4d', 15435, 2762, 0, 1, 0, 30, 'small', 15, 10, 0.170312, 0.170312, 0, 0, 0, 0),
            ('c5m3_cemetery', 'l4d', 7513, 11200, 0, 1, 0, 30, 'small', 22, 10, 0.170312, 0.170312, 0, 0, 0, 0),
            ('c5m4_quarter', 'l4d', 7494, 5735, 0, 1, 0, 30, 'small', 10, 10, 0.170312, 0.170312, 0, 0, 0, 0),
            ('c5m5_bridge', 'l4d', 14466, 16658, 0, 1, 0, 30, 'small', 22, 10, 0.170312, 0.170312, 0, 0, 0, 0);
    ");
    
    // Perform database schema update notification
    print "Updating database and verion schema numbers.<br />";
    $db->query("UPDATE hlstats_Options SET `value` = '$version' WHERE `keyname` = 'version'");
    $db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>