<?php
	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}

	$dbversion = 66;
	$version = "1.6.15";

	// Tracker #1466 - Add index to PlayerPlayerActions table
	$result = $db->query("SHOW INDEX FROM hlstats_Events_PlayerPlayerActions WHERE Key_name = 'victimId'", 0, 1);
	$result = $db->calc_rows($result);
	if ($result < 1)
	{
	print "Adding additional database indexes.<br />";
		$db->query("
			ALTER IGNORE TABLE
				hlstats_Events_PlayerPlayerActions
				ADD KEY `victimId` (`victimId`);
		");
	}

	
	// Tracker #1439/1447/1462 - New TF2 weapons - Victory Pack and Manno-Technology pack
	$tfgames = array();
	$result = $db->query("SELECT code FROM hlstats_Games WHERE realgame = 'tf'");
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($tfgames, $db->escape($rowdata[0]));
	}

	// Tracker #1439/1447/1462 - New TF2 weapons - Victory Pack and Manno-Technology pack

	// Actions
	// Reward Player is points to give to the player triggering the action
	// Reward Team rewards points to the team the action triggers for
	// PlayerActions should generally be used, and is for actions a player performs (captured the flag, for instance)
	// PlayerPlayerActions are when a player performs an action against another, and takes the point from the victim
	// TeamActions indicates that a team performs the action
	// WorldActions are triggered by the environment
	// Team deteremines which team performs the action (applicable to CS only)
	// Award Name sets the name of the award -- if left blank no award will be added
	// Award verb sets the "action" described on the award
	// Award Type sets the type of award -- see the Award section below
	$actions = array(
		array(
			"description" => "Player Penetration",
			"code" => "player_penetration",
			"reward_player" => 4,
			"reward_team" => 0,
			"for_PlayerActions" => '1',
			"for_PlayerPlayerActions" => '0',
			"for_TeamActions" => '0',
			"for_WorldActions" => '0',
			"award_name" => "In and Out",
			"award_verb" => "players penetrated",
			"award_type" => "O",
			"team" => '')
	);

	// Awards for Weapons and Actions are handed by above logic -- this only inserts awards/ribbons for missing items.
	// Type:
	// W = Weapons/Special actions
	// O = Player Action
	// P = PlayerPlayer Action
	// V = Victim action
	$awards = array(
		array(
			"code" => "builtobject_obj_teleporter",
			"award_name" => "Proceed to android hell",
			"award_verb" => "teleporters built",
			"type" => "O"),
		array(
			"code" => "death_sawblade",
			"award_name" => "Blades of Glory",
			"award_verb" => "deaths to a sawblade",
			"type" => "O"),
		array(
			"code" => "killedobject_obj_attachment_sapper",
			"award_name" => "Sap This!",
			"award_verb" => "sappers removed",
			"type" => "O"),
		array(
			"code" => "killedobject_obj_teleporter",
			"award_name" => "Take a walk!",
			"award_verb" => "teleporters killed",
			"type" => "O")
	);
	
	// Weapons
	// Name is the name of the weapon
	// Verb is the "action" described on the award
	// Modifier is used to adjust points given for a kill with the weapon
	// Award name sets the name of the award
	$weapons = array(
		array(
			"weapon_code" => "cow_mangler",
			"weapon_name" => "Cow Mangler 5000",
			"award_verb" => "Cow Mangler 5000 kills",
			"modifier" => "1.00",
			"award_name" => "Cows Mangled"),
		array(
			"weapon_code" => "righteous_bison",
			"weapon_name" => "Righteous Bison",
			"award_verb" => "Righteous Bison kills",
			"modifier" => "2.00",
			"award_name" => "Bisons Blasted"),
		array(
			"weapon_code" => "tf_projectile_energy_ball",
			"weapon_name" => "Deflected Cow Mangler Shot",
			"award_verb" => "Deflected Cow Mangler Shot kills",
			"modifier" => "5.00",
			"award_name" => "Save A Cow, Eat mor Chikin"),
		array(
			"weapon_code" => "machina",
			"weapon_name" => "Machina",
			"award_verb" => "Machina kills",
			"modifier" => "2.00",
			"award_name" => "Problems Solved"),
		array(
			"weapon_code" => "diamondback",
			"weapon_name" => "The Diamondback",
			"award_verb" => "Diamondback kills",
			"modifier" => "2.00",
			"award_name" => "Souls Rattled"),
		array(
			"weapon_code" => "widowmaker",
			"weapon_name" => "The Widowmaker",
			"award_verb" => "Widowmaker kills",
			"modifier" => "2.00",
			"award_name" => "Widows Made"),
		array(
			"weapon_code" => "short_circuit",
			"weapon_name" => "The Short Circuit",
			"award_verb" => "Short Circuit kills",
			"modifier" => "2.00",
			"award_name" => "Circuits Shorted"),
		array(
			"weapon_code" => "quake_rl",
			"weapon_name" => "Original",
			"award_verb" => "Original kills",
			"modifier" => "2.00",
			"award_name" => "Boots Quaked"),
		array(
			"weapon_code" => "scotland_shard",
			"weapon_name" => "Scottish Handshake",
			"award_verb" => "Scottish Handshake kills",
			"modifier" => "2.00",
			"award_name" => "Hands Shook"),
		array(
			"weapon_code" => "nonnonviolent_protest",
			"weapon_name" => "Conscientious Objector",
			"award_verb" => "Conscientious Objector kills",
			"modifier" => "2.00",
			"award_name" => "Signed Petitioners"),
		array(
			"weapon_code" => "deflect_flare_detonator",
			"weapon_name" => "Deflected Flare (Detonator)",
			"award_verb" => "Deflected Flare (Detonator) kills",
			"modifier" => "2.00",
			"award_name" => "Reflected Detonation"),
		array(
			"weapon_code" => "deflect_huntsman_flyingburn",
			"weapon_name" => "Deflect Huntsman Burning Arrow",
			"award_verb" => "Deflect Huntsman Burning Arrow kills",
			"modifier" => "2.00",
			"award_name" => "Reflected Burn"),
		array(
			"weapon_code" => "unarmed_combat",
			"weapon_name" => "Unarmed Combat",
			"award_verb" => "Unarmed Combat kills",
			"modifier" => "2.00",
			"award_name" => "Armed Robbery")
	);

	foreach ($tfgames as $game)
	{
		// Get list of all Team Fortress 2 servers
		$tfservers = array();
		$result = $db->query("SELECT serverId FROM hlstats_Servers WHERE game = '$game'");
		while ($rowdata = $db->fetch_row($result))
		{
			array_push($tfservers, $db->escape($rowdata[0]));
		}
		if (count($tfservers) > 0)
		{
			$serverstring = implode (',', $tfservers);
		}
		
		// Perform additional modifications to database before inserting new lines
		if (!empty($serverstring))
		{
			// Change all player_penetration weapon kill lines in event Frags to machina before doing our calculation.
			print "Updating player_penetration weapon kills to machina kills for game $game.<br />";
			$db->query("
				UPDATE IGNORE
					hlstats_Events_Frags
				SET
					`weapon` = 'machina'
				WHERE
					`weapon` = 'player_penetration'
				AND
					`serverId` in ($serverstring)");
		}

		
		// Insert actions
		print "Adding new actions for game $game.<br />";
		if (isset($actions) && count($actions) > 0)
		{
			$action_query = "INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES ";
			$award_query = "INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES ";
			$ribbon_query = "INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES ";
			// Insert actions
			foreach ($actions as $key => $action)
			{
				// Insert actions into Actions table
				$action_query .= "(
					'$game',
					'".$db->escape($action['code'])."',
					'".$db->escape($action['reward_player'])."',
					'".$db->escape($action['reward_team'])."',
					'".$db->escape($action['team'])."',
					'".$db->escape($action['description'])."',
					'".$db->escape($action['for_PlayerActions'])."',
					'".$db->escape($action['for_PlayerPlayerActions'])."',
					'".$db->escape($action['for_TeamActions'])."',
					'".$db->escape($action['for_WorldActions'])."')" .
					// Check to see if we're on the last key -- if so finish the SQL statement, otherwise leave it open to append
					($key == count($actions)-1 ? ";" : ",");
				
				if ($action['award_name'] != "")
				{
					$award_query .= "(
						'".$db->escape($action['award_type'])."',
						'$game',
						'".$db->escape($action['code'])."',
						'".$db->escape($action['award_name'])."',
						'".$db->escape($action['award_verb'])."')" .
						// Check to see if we're on the last key -- if so finish the SQL statement, otherwise leave it open to append
						($key == count($actions)-1 ? ";" : ",");
					
						// Insert actions into Ribbons table
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
						$ribbon_query .= "(
						'".$db->escape($action['code'])."',
						$award_count,
						0,
						'$game',
						'".$ribbon_count."_".$db->escape($action['code']).".png',
						'$color " .$db->escape($action['description']) . "')" .
						// Check to see if we're on the last key -- if so finish the SQL statement, otherwise leave it open to append
						($key == count($actions)-1 && $ribbon_count == 3 ? ";" : ",");
						}
				}
			}
			$db->query($action_query);
			$db->query($award_query);
			$db->query($ribbon_query);
			unset($action_query);
			unset($award_query);
			unset($ribbon_query);
		}

		// Insert awards
		print "Adding new awards for game $game.<br />";
		if (isset($awards) && count($awards) > 0)
		{
			$award_query = "INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES ";
			$ribbon_query = "INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES ";
			
			foreach ($awards as $key => $award)
			{
				// Insert awards into Awards table
				$award_query .= "(
					'".$db->escape($award['type'])."',
					'$game',
					'".$db->escape($award['code'])."',
					'".$db->escape($award['award_name'])."',
					'".$db->escape($award['award_verb'])."')" .
					// Check to see if we're on the last key -- if so finish the SQL statement, otherwise leave it open to append
					($key == count($awards)-1 ? ";" : ",");
				
				// Insert awards into Ribbons table
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
					$ribbon_query .= "(
					'".$db->escape($award['code'])."',
					$award_count,
					0,
					'$game',
					'".$ribbon_count."_".$db->escape($award['code']).".png',
					'$color " .$db->escape($award['award_name']) . "')" .
					// Check to see if we're on the last key -- if so finish the SQL statement, otherwise leave it open to append
					($key == count($awards)-1 && $ribbon_count == 3 ? ";" : ",");
				}
			}
			$db->query($award_query);
			$db->query($ribbon_query);
			unset($award_query);
			unset($ribbon_query);
		}

		// Insert weapons
		print "Adding new weapons for game $game.<br />";
		if (isset($weapons) && count($weapons) > 0)
		{
			$award_query = "INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES ";
			$ribbon_query = "INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES ";
			$weapon_query = "INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES ";
			foreach ($weapons as $key => $weapon)
			{
				// Insert weapons into Weapons table
				$weapon_query .= "(
					'$game',
					'".$db->escape($weapon['weapon_code'])."',
					'".$db->escape($weapon['weapon_name'])."',
					'".$db->escape($weapon['modifier'])."')" .
					// Check to see if we're on the last key -- if so finish the SQL statement, otherwise leave it open to append
					($key == count($weapons)-1 ? ";" : ",");

					
				// Insert weapons into Awards table
				$award_query .= "(
					'W',
					'$game',
					'".$db->escape($weapon['weapon_code'])."',
					'".$db->escape($weapon['weapon_name'])."',
					'".$db->escape($weapon['award_verb'])."')" .
					// Check to see if we're on the last key -- if so finish the SQL statement, otherwise leave it open to append
					($key == count($weapons)-1 ? ";" : ",");
				
				// Insert weapons into Ribbons table
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
					$ribbon_query .= "(
					'".$db->escape($weapon['weapon_code'])."',
					$award_count,
					0,
					'$game',
					'".$ribbon_count."_".$db->escape($weapon['weapon_code']).".png',
					'$color ".$db->escape($weapon['weapon_name']) . "')" .
					// Check to see if we're on the last key -- if so finish the SQL statement, otherwise leave it open to append
					($key == count($weapons)-1 && $ribbon_count == 3 ? ";" : ",");
				}
				
				// Update kill count for any weapons just added
				print "Updating weapon count for ".$db->escape($weapon['weapon_code'])." in game $game<br />";
				if (!empty($serverstring))
				{
					$db->query("
						UPDATE IGNORE
							hlstats_Weapons
						SET
							`kills` = `kills` + (
								IFNULL((
									SELECT count(weapon)
										FROM
											hlstats_Events_Frags
										WHERE
											`weapon` = '".$db->escape($weapon['weapon_code'])."'
										AND
											`serverId` IN ($serverstring)
									),0)
							)
						WHERE
							`code` = '".$db->escape($weapon['weapon_code'])."'
						AND
							`game` = '$game';");
				}
			}
			$db->query($weapon_query);
			$db->query($award_query);
			$db->query($ribbon_query);
			unset($weapon_query);
			unset($award_query);
			unset($ribbon_query);
		}
	}
	// Tracker #1439/1447 - End

	// Perform database schema update notification
	print "Updating database and verion schema numbers.<br />";
	$db->query("UPDATE hlstats_Options SET `value` = '$version' WHERE `keyname` = 'version'");
	$db->query("UPDATE hlstats_Options SET `value` = '$dbversion' WHERE `keyname` = 'dbversion'");
?>