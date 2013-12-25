<?php
    if ( !defined('IN_UPDATER') )
    {
        die('Do not access this file directly.');
    }

    $dbversion = 75;
    $version = "1.6.19-pre1";

    // Tracker #1602 - Add some waiting-for-ever heatmaps.
    // Get list of NTS duplicated games
    $ntsgames = array();
    $result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'nts'");
    while ($rowdata = $db->fetch_row($result))
    { 
        array_push($ntsgames, $db->escape($rowdata[0]));
    }
    
    // Get list of CSS duplicated games
    $cssgames = array();
    $result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'css'");
    while ($rowdata = $db->fetch_row($result))
    { 
        array_push($cssgames, $db->escape($rowdata[0]));
    }
    
    // Insert heatmap config for each NTS game
    print "Adding some NTS Heatmap configurations. (<a href=\"http://tracker.hlxce.com/issues/1602\">#1602</a>)<br />";
    foreach ($ntsgames as $ntsgame)
    {
        $db->query("
            INSERT IGNORE INTO `hlstats_Heatmap_Config` (`map`, `game`, `xoffset`, `yoffset`, `flipx`, `flipy`, `days`, `brush`, `scale`, `font`, `thumbw`, `thumbh`, `cropx1`, `cropy1`, `cropx2`, `cropy2`) VALUES
                ('nt_bullet_tdm', '$ntsgame', 4279, 2090, 0, 1, 30, 'small', 4.3, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('nt_dusk_ctg', '$ntsgame', 3805, 5832, 0, 1, 30, 'small', 6, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('nt_oilstain_ctg', '$ntsgame', 1953, 2544, 0, 1, 30, 'small', 3, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('nt_pissalley_ctg', '$ntsgame', 3629, 3788, 0, 1, 30, 'small', 6, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('nt_vtol_ctg', '$ntsgame', 6278, 4861, 0, 1, 30, 'small', 9, 10, 0.170312, 0.170312, 0, 0, 0, 0);
        ");
    }
    
    // Insert heatmap config for each CSS game
    print "Adding some CSS Heatmap configurations. (<a href=\"http://tracker.hlxce.com/issues/1602\">#1602</a>)<br />";
    foreach ($cssgames as $cssgame)
    {
        $db->query("
            INSERT IGNORE INTO `hlstats_Heatmap_Config` (`map`, `game`, `xoffset`, `yoffset`, `flipx`, `flipy`, `days`, `brush`, `scale`, `font`, `thumbw`, `thumbh`, `cropx1`, `cropy1`, `cropx2`, `cropy2`) VALUES
                ('de_dust2_unlimited', '$cssgame', 4238, 4440, 0, 1, 30, 'small', 6.10, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_mediterrano', '$cssgame', -3446, 3391, 0, 1, 30, 'small', 4.5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_middleages', '$cssgame', -3068, 2137, 0, 1, 30, 'small', 4, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_newportbeach', '$cssgame', -2405, 2317, 0, 1, 30, 'small', 3.6, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_pariah', '$cssgame', -2675, 1906, 0, 1, 30, 'small', 5.25, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_residentevil', '$cssgame', -3629, 1910, 0, 1, 30, 'small', 3.75, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_rimini', '$cssgame', -11146, 11378, 0, 1, 30, 'small', 5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_roma_aimstyle', '$cssgame', -3766, 4159, 0, 1, 30, 'small', 7, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_losttemple2', '$cssgame', -3205, 3312, 0, 1, 30, 'small', 5.5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_keidas', '$cssgame', -3607, 2473, 0, 1, 30, 'small', 5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_hiddencity', '$cssgame', -2488, 2344, 0, 1, 30, 'small', 3.75, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_gallery', '$cssgame', -2931, 2470, 0, 1, 30, 'small', 4.75, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_dustanesi', '$cssgame', -1477, 1113, 0, 1, 30, 'small', 4, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_dust2_mariostyle', '$cssgame', -3910, 3788, 0, 1, 30, 'small', 5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_cross_strike', '$cssgame', -3740, 3799, 0, 1, 30, 'small', 5.5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_cpl_fire', '$cssgame', -2841, 4296, 0, 1, 30, 'small', 7, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_corse', '$cssgame', -2231, 1216, 0, 1, 30, 'small', 4.5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_coldfire', '$cssgame', -2776, 3783, 0, 1, 30, 'small', 7.25, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_cefalu', '$cssgame', -5556, 3045, 0, 1, 30, 'small', 5.5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_asia', '$cssgame', -4408, 4349, 0, 1, 30, 'small', 6.5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('cs_office2008pro', '$cssgame', -2270, 1640, 0, 1, 30, 'small', 4, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('cs_office07', '$cssgame', -3938, 1579, 0, 1, 30, 'small', 4, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('cs_occupation', '$cssgame', -3857, 2621, 0, 1, 30, 'small', 5.8, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('cs_meatlight', '$cssgame', -2143, 1768, 0, 1, 30, 'small', 3.7, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('cs_italy_reloaded_final', '$cssgame', -3463, 2775, 0, 1, 30, 'small', 3.75, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('cs_classified', '$cssgame', -2988, 3396, 0, 1, 30, 'small', 4.9, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('fy_pool_day_reloaded', '$cssgame', -1204, 838, 0, 1, 30, 'small', 1.75, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_westcoast', '$cssgame', -3162, 2684, 0, 1, 30, 'small', 5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_wanda', '$cssgame', -3284, 5861, 0, 1, 30, 'small', 10, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_vine2', '$cssgame', -3929, 1482, 0, 1, 30, 'small', 6.5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_vegas_css', '$cssgame', -5085, 1646, 0, 1, 30, 'small', 7.25, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_varasto_v2', '$cssgame', -3520, 2665, 0, 1, 30, 'small', 8, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_strata', '$cssgame', -14440, 9998, 0, 1, 30, 'small', 15, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_spirits', '$cssgame', -4494, 3157, 0, 1, 30, 'small', 10, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_siena', '$cssgame', -4234, 4210, 0, 1, 30, 'small', 6, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_season', '$cssgame', -1772, 3421, 0, 1, 30, 'small', 5.75, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_scud', '$cssgame', -5001, 3479, 0, 1, 30, 'small', 4.5, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_scorch_rc1', '$cssgame', -7897, 3704, 0, 1, 30, 'small', 8, 10, 0.170312, 0.170312, 0, 0, 0, 0),
                ('de_rush_v2', '$cssgame', -2365, 2391, 0, 1, 30, 'small', 9, 10, 0.170312, 0.170312, 0, 0, 0, 0);
    ");
    }
    // Perform database schema update notification
    print "Updating database and verion schema numbers.<br />";
    $db->query("UPDATE hlstats_Options SET `value` = '$version' WHERE `keyname` = 'version'");
    $db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>