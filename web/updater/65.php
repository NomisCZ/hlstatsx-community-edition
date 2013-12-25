<?php
  if ( !defined('IN_UPDATER') )
  {
    die('Do not access this file directly.');
  }    

  $dbversion = 65;
  $version = "1.6.14";

  // Tracker #1421 - New TF2 weapons - Uber Update
  $tfgames = array();
  $result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'tf'");
  while ($rowdata = $db->fetch_row($result))
  { 
    array_push($tfgames, $db->escape($rowdata[0]));
  }

  $weapons = array(
    array(
      "code" => "atomizer",
      "name" => "Atomizer",
      "verb" => "Atomizer kills",
      "modifier" => "2.00",
      "award_name" => "Atoms Split"),
    array(
      "code" => "bazaar_bargain",
      "name" => "Bazaar Bargain",
      "verb" => "Bazaar Bargain kills",
      "modifier" => "1.00",
      "award_name" => "Bargins Earned"),
    array(
      "code" => "demokatana",
      "name" => "Half-Zatoichi",
      "verb" => "Half-Zatoichi kills",
      "modifier" => "2.00",
      "award_name" => "Samurai Swipes"),
    array(
      "code" => "detonator",
      "name" => "Detonator",
      "verb" => "Detonator kills",
      "modifier" => "2.00",
      "award_name" => "Detonations Served"),
    array(
      "code" => "disciplinary_action",
      "name" => "Disciplinary Action",
      "verb" => "Disciplinary Action kills",
      "modifier" => "2.00",
      "award_name" => "Disciplined to Death"),
    array(
      "code" => "enforcer",
      "name" => "Enforcer",
      "verb" => "Enforcer kills",
      "modifier" => "2.00",
      "award_name" => "Deaths Enforced"),
    array(
      "code" => "eviction_notice",
      "name" => "Eviction Notice",
      "verb" => "Eviction Notice kills",
      "modifier" => "2.00",
      "award_name" => "Souls Evicted"),
    array(
      "code" => "family_business",
      "name" => "Family Business",
      "verb" => "Family Business kills",
      "modifier" => "1.00",
      "award_name" => "Cement Shoes Dispensed"),
    array(
      "code" => "kunai",
      "name" => "Conniver's Kunai",
      "verb" => "Conniver's Kunai kills",
      "modifier" => "2.00",
      "award_name" => "Spikes Implanted"),
    array(
      "code" => "lava_axe",
      "name" => "Sharpened Volcano Fragment",
      "verb" => "Sharpened Volcano Fragment kills",
      "modifier" => "2.00",
      "award_name" => "Fragments Fragged"),
    array(
      "code" => "lava_bat",
      "name" => "Sun-on-a-Stick",
      "verb" => "Sun-on-a-Stick kills",
      "modifier" => "2.00",
      "award_name" => "Afterlives Brightened"),
    array(
      "code" => "liberty_launcher",
      "name" => "Liberty Launcher",
      "verb" => "Liberty Launcher kills",
      "modifier" => "1.00",
      "award_name" => "Lives Liberated"),
    array(
      "code" => "mantreads",
      "name" => "Mantreads",
      "verb" => "Mantreads kills",
      "modifier" => "2.00",
      "award_name" => "Goombas Stomped"),
    array(
      "code" => "market_gardener",
      "name" => "Market Gardener",
      "verb" => "Market Gardener kills",
      "modifier" => "2.00",
      "award_name" => "Garden Graves Dug"),
    array(
      "code" => "persian_persuader",
      "name" => "Persian Persuader",
      "verb" => "Persian Persuader kills",
      "modifier" => "2.00",
      "award_name" => "Persons Persuaded"),
    array(
      "code" => "proto_syringe",
      "name" => "Overdose",
      "verb" => "Overdose kills",
      "modifier" => "2.00",
      "award_name" => "Patients Overdosed"),
    array(
      "code" => "reserve_shooter",
      "name" => "Reserve Shooter",
      "verb" => "Reserve Shooter kills",
      "modifier" => "2.00",
      "award_name" => "Reservations Made"),
    array(
      "code" => "scout_sword",
      "name" => "Three-Rune Blade",
      "verb" => "Three-Rune Blade kills",
      "modifier" => "2.00",
      "award_name" => "Rune Slices Served"),
    array(
      "code" => "shahanshah",
      "name" => "Shahanshah",
      "verb" => "Shahanshah kills",
      "modifier" => "2.00",
      "award_name" => "Shahaha UR Dead"),
    array(
      "code" => "soda_popper",
      "name" => "Soda Popper",
      "verb" => "Soda Popper kills",
      "modifier" => "1.25",
      "award_name" => "Souls Popped"),
    array(
      "code" => "solemn_vow",
      "name" => "Solemn Vow",
      "verb" => "Solemn Vow kills",
      "modifier" => "2.00",
      "award_name" => "Busts Busted"),
    array(
      "code" => "the_maul",
      "name" => "Maul",
      "verb" => "Maul kills",
      "modifier" => "2.00",
      "award_name" => "Men Mauled"),
    array(
      "code" => "the_winger",
      "name" => "Winger",
      "verb" => "Winger kills",
      "modifier" => "1.00",
      "award_name" => "Warriors Winged"),
    array(
      "code" => "tomislav",
      "name" => "Tomislav",
      "verb" => "Tomislav kills",
      "modifier" => "1.00",
      "award_name" => "Tomis Tapped"),
    array(
      "code" => "warfan",
      "name" => "Fan O'War",
      "verb" => "Fan O'War kills",
      "modifier" => "2.00",
      "award_name" => "Fans Made"),
    array(
      "code" => "big_earner",
      "name" => "Big Earner",
      "verb" => "Big Earner kills",
      "modifier" => "2.00",
      "award_name" => "Suckers Stuck"),
    array(
      "code" => "saxxy",
      "name" => "Saxxy",
      "verb" => "Saxxy kills",
      "modifier" => "2.00",
      "award_name" => "Hale No"),
    array(
      "code" => "splendid_screen",
      "name" => "Splendid Screen",
      "verb" => "Splendid Screen kills",
      "modifier" => "2.00",
      "award_name" => "Full Speed Ahead"),
    array(
      "code" => "taunt_soldier_lumbricus",
      "name" => "Kamikaze (Lumbricus Lid)",
      "verb" => "Kamikaze (Lumbricus Lid) kills",
      "modifier" => "5.00",
      "award_name" => "Hallelujah!"),
    array(
      "code" => "nessieclub",
      "name" => "Nessie's Nine Iron",
      "verb" => "Nessie's Nine Iron kills",
      "modifier" => "2.00",
      "award_name" => "Hole in One"),
    array(
      "code" => "mailbox",
      "name" => "Postal Pummeler",
      "verb" => "Postal Pummeler kills",
      "modifier" => "2.00",
      "award_name" => "Mail's Here")
  );
  
  foreach ($tfgames as $game)
  {
  
    // Insert new awards
    $query = "INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES ";
    foreach ($weapons as $key => $weapon)
    {
      $code = $db->escape($weapon['code']);
      $award_name = $db->escape($weapon['award_name']);
      $verb = $db->escape($weapon['verb']);
      $query .= "('W', '$game', '$code', '$award_name', '$verb')" .
      // Finish query line -- Check if this is the last index.  If so, add a semi-colon.  Otherwise, colon.
      ($key == count($weapons)-1 ? ";" : ",");
    }
    $db->query($query);
    unset($weapon);
    unset($query);
    
    // Insert new weapons
    $query = "INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES ";
    foreach ($weapons as $key => $weapon)
    {
      $code = $db->escape($weapon['code']);
      $name = $db->escape($weapon['name']);
      $modifier = $db->escape($weapon['modifier']);
      $query .= "('$game', '$code', '$name', $modifier)" .
      // Finish query line -- Check if this is the last index.  If so, add a semi-colon.  Otherwise, colon.
      ($key == count($weapons)-1 ? ";" : ",");      
    }
    $db->query($query);
    unset($weapon);
    unset($query);
    
    // Insert new ribbons
    $query = "INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES ";
    foreach ($weapons as $key => $weapon)
    {
      $code = $db->escape($weapon['code']);
      $name = $db->escape($weapon['name']);
      for ($ribbon_count = 1; $ribbon_count <= 3; $ribbon_count++)
      {
        switch ($ribbon_count) {
          case 1:
            $color = "Bronze";
            $award_count = 1;
            break;
          case 2:
            $color = "Silver";
            $award_count = 5;
            break;
          case 3:
            $color = "Gold";
            $award_count = 10;
            break;
        }    
        $query .= "('$code', $award_count, 0, '$game', '" . $ribbon_count . "_" . $code . ".png', '$color $name')" .
          // Finish query line -- Check if this is the last index.  If so, add a semi-colon.  Otherwise, colon.
          ($key == count($weapons)-1 && $ribbon_count == 3 ? ";" : ",");
      }
    }
    $db->query($query);
    unset($weapon);
    unset($query);
    
    // Update kill count for new weapons
    $tfservers = array();
    
    $result = $db->query("SELECT serverId FROM hlstats_Servers WHERE game = '$game'");
    while ($rowdata = $db->fetch_row($result))
    { 
      array_push($tfservers, $db->escape($rowdata[0]));
    }
    if (count($tfservers) > 0)
    {
      $serverstring = implode (',', $tfservers);
      foreach ($weapons as $weapon) {
        $code = $db->escape($weapon['code']);
        $db->query("UPDATE hlstats_Weapons SET `kills` = `kills` + (IFNULL((SELECT count(weapon) FROM hlstats_Events_Frags WHERE `weapon` = '$code' AND `serverId` IN ($serverstring)),0)) WHERE `code` = '$code' AND `game` = '$game'");
      }
      unset($weapon);
    }
  }
  
  // Tracker #1421 - End
  
  
  // Tracker #1423 - Update weapon names in TF2
  $db->query("UPDATE hlstats_Weapons SET `name` = 'Grenade Launcher' WHERE `code` = 'tf_projectile_pipe' AND `name` = 'Pipe'");
  $db->query("UPDATE hlstats_Weapons SET `name` = 'Rocket Launcher' WHERE `code` = 'tf_projectile_rocket' AND `name` = 'Rocket'");
  $db->query("UPDATE hlstats_Weapons SET `name` = 'Kukri' WHERE `code` = 'club' AND `name` = 'Club'");
  $db->query("UPDATE hlstats_Weapons SET `name` = 'Organ Grinder' WHERE `code` = 'robot_arm_blender_kill' AND `name` = 'Engineer Taunt (Gunslinger)'");    
  $db->query("UPDATE hlstats_Weapons SET `name` = 'Decapitation' WHERE `code` = 'taunt_demoman' AND `name` = 'Demoman Taunt'");
  $db->query("UPDATE hlstats_Weapons SET `name` = 'Dischord' WHERE `code` = 'taunt_guitar_kill' AND `name` = 'Engineer Taunt (Guitar)'");
  $db->query("UPDATE hlstats_Weapons SET `name` = 'Showdown' WHERE `code` = 'taunt_heavy' AND `name` = 'Heavy taunt'");
  $db->query("UPDATE hlstats_Weapons SET `name` = 'Spinal Tap' WHERE `code` = 'taunt_medic' AND `name` = 'Medic Taunt'");
  $db->query("UPDATE hlstats_Weapons SET `name` = 'Home Run' WHERE `code` = 'taunt_scout' AND `name` = 'Grand Slam'");
  $db->query("UPDATE hlstats_Weapons SET `name` = 'Skewer' WHERE `code` = 'taunt_sniper' AND `name` = 'Sniper Taunt'");
  $db->query("UPDATE hlstats_Weapons SET `name` = 'Kamikaze' WHERE `code` = 'taunt_soldier' AND `name` = 'Soldier Taunt'");
  $db->query("UPDATE hlstats_Weapons SET `name` = 'Fencing' WHERE `code` = 'taunt_spy' AND `name` = 'Spy Taunt'");
  $db->query("UPDATE hlstats_Weapons SET `name` = 'Stickybomb Launcher' WHERE `code` = 'tf_projectile_pipe_remote' AND `name` = 'Remote Pipe'");
  $db->query("UPDATE hlstats_Weapons SET `name` = 'Flamethower' WHERE `code` = 'flamethrower' AND `name` = 'Flame'");
  // Tracker #1423 - End
  
  // Tracker #1397 - Add an extinguished teammate for scout action
  foreach ($tfgames as $game)
  {
			$db->query("INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES ('$game', 'scout_extinguish', 1, 0, '', 'Extinguished Teammate (Scout)', '1', '', '', '');");
      $db->query("INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES ('O','$game','scout_extinguish', 'Milk - does a body good', 'extinguishes with Mad Milk');");
      $db->query("INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES 
        ('scout_extinguish', 1, 0, '$game', '1_scout_extinguish.png', 'Bronze Scout Extinguish'),
        ('scout_extinguish', 5, 0, '$game', '2_scout_extinguish.png', 'Silver Scout Extinguish'),
        ('scout_extinguish', 10, 0, '$game', '3_scout_extinguish.png', 'Gold Scout Extinguish');
      ");
  }
  
  // Tracker #1397 - End
  $db->query("UPDATE hlstats_Options SET `value` = '$version' WHERE `keyname` = 'version'");
  $db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>
