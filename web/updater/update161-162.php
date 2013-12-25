<?php
	
	if ( !defined('IN_UPDATER') )
	{
		die('Do not access this file directly.');
	}
	
	$db->query("
		ALTER TABLE hlstats_Players ADD KEY `playerclan` (`clan`,`playerId`)
		", false);
		
	$db->query("
		ALTER TABLE hlstats_Players ADD KEY `hideranking` (`hideranking`)
		", false);

	$db->query("
		ALTER TABLE `hlstats_Players` ADD COLUMN `createdate` int(11)
		", false);
		
	$db->query("
		UPDATE `hlstats_Players` SET `createdate` = UNIX_TIMESTAMP()
		");
		
	$db->query("
		INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES
			('l4d2', 'headshot', 0, 0, '', 'Headshot Kill', '1', '', '', ''),
			('l4d2', 'killed_gas', 1, 0, '', 'Killed a Smoker', '1', '0', '0', '0'),
			('l4d2', 'killed_exploding', 1, 0, '', 'Killed a Boomer', '1', '0', '0', '0'),
			('l4d2', 'killed_hunter', 1, 0, '', 'Killed a Hunter', '1', '0', '0', '0'),
			('l4d2', 'killed_tank', 3, 0, '', 'Killed a Tank', '1', '0', '0', '0'),
			('l4d2', 'killed_witch', 3, 0, '', 'Killed a Witch', '1', '0', '0', '0'),
			('l4d2', 'killed_spitter', 3, 0, '', 'Killed a Spitter', '1', '0', '0', '0'),
			('l4d2', 'killed_jockey', 3, 0, '', 'Killed a Jockey', '1', '0', '0', '0'),
			('l4d2', 'killed_charger', 3, 0, '', 'Killed a Charger', '1', '0', '0', '0'),
			('l4d2', 'killed_survivor', 25, 0, '', 'Incapacitated/Killed Survivor', '0', '1', '0', '0'),
			('l4d2', 'tongue_grab', 6, 0, '', '(Smoker) Tongue Grabbed Survivor', '0', '1', '0', '0'),
			('l4d2', 'scavenge_win', 0, 5, '', 'Scavenge Team Win', '', '', '1', ''),
			('l4d2', 'versus_win', 0, 5, '', 'Versus Team Win', '', '', '1', ''),
			('l4d2', 'defibrillated_teammate', 5, 0, '', 'Defibrillated Teammate', '1', '', '', ''),
			('l4d2', 'used_adrenaline', 0, 0, '', 'Used Adrenaline', '1', '', '', ''),
			('l4d2', 'jockey_ride', 5, 0, '', 'Jockey Ride', '1', '', '', ''),
			('l4d2', 'charger_pummel', 5, 0, '', 'Charger Pummeling', '1', '', '', ''),
			('l4d2', 'bilebomb_tank', 5, 0, '', 'Tank Bilebombed', '1', '', '', ''),
			('l4d2', 'spitter_acidbath', 5, 0, '', 'Spitter Acid', '1', '', '', ''),
			('l4d2', 'rescued_survivor', 2, 0, '', 'Rescued Teammate', '1', '0', '0', '0'),
			('l4d2', 'healed_teammate', 5, 0, '', 'Healed Teammate', '1', '0', '0', '0'),
			('l4d2', 'revived_teammate', 3, 0, '', 'Revived Teammate', '1', '0', '0', '0'),
			('l4d2', 'startled_witch', -5, 0, '', 'Startled the Witch', '1', '0', '0', '0'),
			('l4d2', 'pounce', 6, 0, '', '(Hunter) Pounced on Survivor', '0', '1', '0', '0'),
			('l4d2', 'vomit', 6, 0, '', '(Boomer) Vomited on Survivor', '0', '1', '0', '0'),
			('l4d2', 'friendly_fire', -10, 0, '', 'Friendly Fire', '1', '0', '0', '0'),
			('l4d2', 'cr0wned', 0, 0, '', 'Cr0wned (killed witch with single headshot)', '1', '', '', ''),
			('l4d2', 'hunter_punter', 0, 0, '', 'Hunter Punter (melee a Hunter mid-jump)', '1', '', '', ''),
			('l4d2', 'tounge_twister', 0, 0, '', 'Tounge Twister (kill a Smoker while he is dragging you)', '1', '', '', ''),
			('l4d2', 'protect_teammate', 0, 0, '', 'Protected Teammate', '1', '', '', ''),
			('l4d2', 'no_death_on_tank', 0, 0, '', 'No survivors died/incapped from tank', '1', '', '', ''),
			('l4d2', 'killed_all_survivors', 0, 0, '', 'Killed all survivors', '1', '', '', '');
		");
		
	$db->query("
		INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
			('O', 'l4d2', 'headshot', 'Brain Salad', 'headshot kills'),
			('O', 'l4d2', 'killed_exploding', 'Stomach Upset', 'killed Boomers'),
			('O', 'l4d2', 'killed_gas', 'No Smoking Section', 'killed Smokers'),
			('O', 'l4d2', 'killed_hunter', 'Hunter Punter', 'killed Hunters'),
			('O', 'l4d2', 'killed_spitter', 'Spittle Splatter', 'Spitters Splated'),
			('O', 'l4d2', 'killed_charger', 'Bumrush Thwarter', 'killed Chargers'),
			('O', 'l4d2', 'killed_jockey', 'Hunter Punter', 'killed Jockeys'),
			('P', 'l4d2', 'killed_survivor', 'Dead Wreckening', 'downed Survivors'),
			('O', 'l4d2', 'killed_tank', 'Tankbuster', 'killed Tanks'),
			('O', 'l4d2', 'killed_witch', 'Inquisitor', 'killed Witches'),
			('P', 'l4d2', 'tongue_grab', 'Drag & Drop', 'constricted Survivors'),
			('O', 'l4d2', 'healed_teammate', 'Field Medic', 'healed Survivors'),
			('P', 'l4d2', 'pounce', 'Free to Fly', 'pounced Survivors'),
			('O', 'l4d2', 'rescued_survivor', 'Ground Cover', 'rescued Survivors'),
			('O', 'l4d2', 'revived_teammate', 'Helping Hand', 'revived Survivors'),
			('P', 'l4d2', 'vomit', 'Barf Bagged', 'vomited on Survivors'),
			('W', 'l4d2', 'autoshotgun', 'Automation', 'kills with Auto Shotgun'),
			('W', 'l4d2', 'boomer_claw', 'Boom!', 'kills with Boomer''s Claws'),
			('W', 'l4d2', 'dual_pistols', 'Akimbo Assassin', 'kills with Dual Pistols'),
			('W', 'l4d2', 'hunter_claw', 'Open Season', 'kills with Hunter''s Claws'),
			('W', 'l4d2', 'hunting_rifle', 'Hawk Eye', 'kills with Hunting Rifle'),
			('W', 'l4d2', 'inferno', 'Pyromaniac', 'cremated Infected'),
			('W', 'l4d2', 'pipe_bomb', 'Pyrotechnician', 'blown up Infected'),
			('W', 'l4d2', 'pistol', 'Ammo Saver', 'kills with Pistol'),
			('W', 'l4d2', 'prop_minigun', 'No-One Left Behind', 'kills with Mounted Machine Gun'),
			('W', 'l4d2', 'pumpshotgun', 'Pump It!', 'kills with Pump Shotgun'),
			('W', 'l4d2', 'rifle', 'Commando', 'kills with M16 Assault Rifle'),
			('W', 'l4d2', 'smg', 'Safety First', 'kills with Uzi'),
			('W', 'l4d2', 'smoker_claw', 'Chain Smoker', 'kills with Smoker''s Claws'),
			('W', 'l4d2', 'tank_claw', 'Burger Tank', 'kills with Tank''s Claws'),
			('W', 'l4d2', 'tank_rock', 'Rock Star', 'kills with Tank''s Rock'),
			('O', 'l4d2', 'hunter_punter', 'Hunter Punter', 'hunter punts'),
			('O', 'l4d2', 'protect_teammate', 'Protector', 'hunter punts'),
			('W', 'l4d2', 'latency', 'Lowest Ping', 'ms average connection'),
			('O', 'l4d2', 'defibrillated_teammate', 'Dr. Shocker', 'teammates defibrillated'),
			('O', 'l4d2', 'used_adrenaline', 'Adrenaline Junkie', 'adrenaline shots used'),
			('O', 'l4d2', 'jockey_ride', 'Going for a ride!', 'jockey rides'),
			('O', 'l4d2', 'charger_pummel', 'Hulk Smash!', 'pummelings as a charger'),
			('O', 'l4d2', 'bilebomb_tank', 'Green can''t be healthy..', 'tank bilebombs'),
			('O', 'l4d2', 'spitter_acidbath', 'Spit shine', 'spitter acid attacks'),
			('W', 'l4d2', 'jockey_claw', 'Little Man Claws', 'kills with Jockey''s Claws'),
			('W', 'l4d2', 'spitter_claw', 'Those nails could kill', 'kills with Spitter''s Claws'),
			('W', 'l4d2', 'charger_claw', 'TAAN... What is this?!', 'kills with Charger''s Claws'),
			('W', 'l4d2', 'grenade_launcher', 'Black Scottish Psyclops', 'kills with the Grenade Launcher'),
			('W', 'l4d2', 'pistol_magnum', 'Magnum', 'kills with the Magnum'),
			('W', 'l4d2', 'rifle_ak47', 'AK-47', 'kills with the AK-47'),
			('W', 'l4d2', 'rifle_desert', 'Combat Rifle', 'kills with the Combat Rifle'),
			('W', 'l4d2', 'shotgun_chrome', 'Chrome Shotgun', 'kills with the Chrome Shotgun'),
			('W', 'l4d2', 'shotgun_spas', 'Combat Shotgun', 'kills with the Combat Shotgun'),
			('W', 'l4d2', 'smg_silenced', 'Uzi (Silenced)', 'kills with the Uzi (silenced)'),
			('W', 'l4d2', 'sniper_military', 'Sniper Rifle', 'kills with the Sniper Rifle'),
			('W', 'l4d2', 'vomitjar', 'Swine Flu! Now shipping!', 'kills with the Vomit Jar'),
			('W', 'l4d2', 'baseball_bat', 'Batter Up!', 'kills with the Baseball Bat'),
			('W', 'l4d2', 'cricket_bat', 'Cheerio.', 'kills with the Cricket Bat'),
			('W', 'l4d2', 'crowbar', 'Crowbar', 'kills with the Crowbar'),
			('W', 'l4d2', 'electric_guitar', 'Wayne''s world party on!', 'kills with the Electric Guitar'),
			('W', 'l4d2', 'fireaxe', 'Fight fire with an axe', 'kills with the Fireaxe'),
			('W', 'l4d2', 'frying_pan', 'BANG Headshot.', 'kills with the Frying Pan'),
			('W', 'l4d2', 'katana', 'Katana', 'kills with the Katana'),
			('W', 'l4d2', 'knife', 'Knife', 'kills with the Knife'),
			('W', 'l4d2', 'machete', 'Machete', 'kills with the Machete'),
			('W', 'l4d2', 'tonfa', 'Tonfa', 'kills with the Tonfa'),
			('W', 'l4d2', 'melee', 'Fists of RAGGEE', 'melee kills');
		");

	$db->query("
		INSERT IGNORE INTO `hlstats_Roles` (`game`, `code`, `name`, `hidden`) VALUES
			('l4d2', 'Producer', 'Rochelle', '0'),
			('l4d2', 'Mechanic', 'Ellis', '0'),
			('l4d2', 'Coach', 'Coach', '0'),
			('l4d2', 'Gambler', 'Nick', '0'),
			('l4d2', 'GAS', 'Smoker', '0'),
			('l4d2', 'EXPLODING', 'Boomer', '0'),
			('l4d2', 'HUNTER', 'Hunter', '0'),
			('l4d2', 'TANK', 'Tank', '0'),
			('l4d2', 'CHARGER', 'Charger', '0'),
			('l4d2', 'SPITTER', 'Spitter', '0'),
			('l4d2', 'JOCKEY', 'Jockey', '0'),
			('l4d2', 'infected', 'Infected Horde', '0'),
			('l4d2', 'witch', 'Witch', '0');
		");

	$db->query("
		UPDATE IGNORE `hlstats_Roles` SET `name` = 'Tank' WHERE `game` = 'l4d' AND `code` = 'TANK';
		");

	$db->query("
		INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
			('l4d2', 'rifle', 'M16 Assault Rifle', 1.00),
			('l4d2', 'autoshotgun', 'Auto Shotgun', 1.00),
			('l4d2', 'pumpshotgun', 'Pump Shotgun', 1.30),
			('l4d2', 'smg', 'Uzi', 1.20),
			('l4d2', 'dual_pistols', 'Dual Pistols', 1.60),
			('l4d2', 'pipe_bomb', 'Pipe Bomb', 1.00),
			('l4d2', 'hunting_rifle', 'Hunting Rifle', 1.00),
			('l4d2', 'pistol', 'Pistol', 2.00),
			('l4d2', 'prop_minigun', 'Mounted Machine Gun', 1.20),
			('l4d2', 'tank_claw', 'Tank''s Claws', 2.00),
			('l4d2', 'hunter_claw', 'Hunter''s Claws', 3.00),
			('l4d2', 'smoker_claw', 'Smoker''s Claws', 3.00),
			('l4d2', 'boomer_claw', 'Boomer''s Claws', 3.00),
			('l4d2', 'jockey_claw', 'Jockey''s Claws', 3.00),
			('l4d2', 'spitter_claw', 'Spitter''s Claws', 3.00),
			('l4d2', 'charger_claw', 'Charger''s Claws', 3.00),
			('l4d2', 'inferno', 'Molotov/Gas Can Fire', 1.20),
			('l4d2', 'infected', 'Infected Horde', 1.00),
			('l4d2', 'witch', 'Witch''s Claws', 1.00),
			('l4d2', 'entityflame', 'Blaze', 3),
			('l4d2', 'first_aid_kit', 'First Aid Kit Smash', 1.5),
			('l4d2', 'gascan', 'Gas Can Smash', 1.5),
			('l4d2', 'molotov', 'Molotov Smash', 1.5),
			('l4d2', 'pain_pills', 'Pain Pills Smash', 1.5),
			('l4d2', 'player', 'Player', 1),
			('l4d2', 'propanetank', 'Propane Tank Smash', 1.5),
			('l4d2', 'tank_rock', 'Tank''s Rock', 1.5),
			('l4d2', 'oxygentank', 'Oxygen Tank Smash', 1.5),
			('l4d2', 'prop_physics', 'Prop Physics', 1),
			('l4d2', 'defibrillator', 'Defibrillator', 1.5),
			('l4d2', 'grenade_launcher', 'Grenade Launcher', 1),
			('l4d2', 'pistol_magnum', 'Magnum', 1),
			('l4d2', 'rifle_ak47', 'AK-47', 1),
			('l4d2', 'rifle_desert', 'Combat Rifle', 1),
			('l4d2', 'shotgun_chrome', 'Chrome Shotgun', 1),
			('l4d2', 'shotgun_spas', 'Combat Shotgun', 1),
			('l4d2', 'smg_silenced', 'Uzi (silenced)', 1),
			('l4d2', 'sniper_military', 'Sniper Rifle', 1),
			('l4d2', 'vomitjar', 'Vomit Jar Smash', 1.5),
			('l4d2', 'baseball_bat', 'Baseball Bat', 1.5),
			('l4d2', 'cricket_bat', 'Cricket Bat', 1.5),
			('l4d2', 'crowbar', 'Crowbar', 1.5),
			('l4d2', 'electric_guitar', 'Electric Guitar', 1.5),
			('l4d2', 'fireaxe', 'Fireaxe', 1.5),
			('l4d2', 'frying_pan', 'Frying Pan', 1.5),
			('l4d2', 'katana', 'Katana', 1.5),
			('l4d2', 'knife', 'Knife', 1.5),
			('l4d2', 'machete', 'Machete', 1.5),
			('l4d2', 'tonfa', 'Tonfa', 1.5),
			('l4d2', 'insect_swarm', 'Insect Swarm', 1),
			('l4d2', 'melee', 'Melee', 1.5);
		");

	$db->query("
		INSERT IGNORE INTO `hlstats_Games` (`code`, `name`, `realgame`, `hidden`) VALUES
			('l4d2', 'Left 4 Dead 2', 'l4d', '1');
		");

	$db->query("
		UPDATE `hlstats_Games_Supported` SET `name` = 'Left 4 Dead (Orig. & 2)' WHERE `code` = 'l4d';
		");

	$db->query("
		INSERT IGNORE INTO `hlstats_Teams` (`game`, `code`, `name`, `hidden`, `playerlist_bgcolor`, `playerlist_color`, `playerlist_index`) VALUES
			('l4d2', 'Survivor', 'Survivors', '0', '#E0E4E5', '#4B6168', 1),
			('l4d2', 'Infected', 'Infected', '0', '#E5D5D5', '#68090B', 2);
		");

	$db->query("
		INSERT IGNORE INTO `hlstats_Ranks` (`game`, `image`, `minKills`, `maxKills`, `rankName`) VALUES
			('l4d2', 'recruit', 0, 49, 'Recruit'),
			('l4d2', 'private', 50, 99, 'Private'),
			('l4d2', 'private-first-class', 100, 199, 'Private First Class'),
			('l4d2', 'lance-corporal', 200, 299, 'Lance Corporal'),
			('l4d2', 'corporal', 300, 399, 'Corporal'),
			('l4d2', 'sergeant', 400, 499, 'Sergeant'),
			('l4d2', 'staff-sergeant', 500, 599, 'Staff Sergeant'),
			('l4d2', 'gunnery-sergeant', 600, 699, 'Gunnery Sergeant'),
			('l4d2', 'master-sergeant', 700, 799, 'Master Sergeant'),
			('l4d2', 'first-sergeant', 800, 899, 'First Sergeant'),
			('l4d2', 'master-chief', 900, 999, 'Master Chief'),
			('l4d2', 'sergeant-major', 1000, 1199, 'Sergeant Major'),
			('l4d2', 'ensign', 1200, 1399, 'Ensign'),
			('l4d2', 'third-lieutenant', 1400, 1599, 'Third Lieutenant'),
			('l4d2', 'second-lieutenant', 1600, 1799, 'Second Lieutenant'),
			('l4d2', 'first-lieutenant', 1800, 1999, 'First Lieutenant'),
			('l4d2', 'captain', 2000, 2249, 'Captain'),
			('l4d2', 'group-captain', 2250, 2499, 'Group Captain'),
			('l4d2', 'senior-captain', 2500, 2749, 'Senior Captain'),
			('l4d2', 'lieutenant-major', 2750, 2999, 'Lieutenant Major'),
			('l4d2', 'major', 3000, 3499, 'Major'),
			('l4d2', 'group-major', 3500, 3999, 'Group Major'),
			('l4d2', 'lieutenant-commander', 4000, 4499, 'Lieutenant Commander'),
			('l4d2', 'commander', 4500, 4999, 'Commander'),
			('l4d2', 'group-commander', 5000, 5749, 'Group Commander'),
			('l4d2', 'lieutenant-colonel', 5750, 6499, 'Lieutenant Colonel'),
			('l4d2', 'colonel', 6500, 7249, 'Colonel'),
			('l4d2', 'brigadier', 7250, 7999, 'Brigadier'),
			('l4d2', 'brigadier-general', 8000, 8999, 'Brigadier General'),
			('l4d2', 'major-general', 9000, 9999, 'Major General'),
			('l4d2', 'lieutenant-general', 10000, 12499, 'Lieutenant General'),
			('l4d2', 'general', 12500, 14999, 'General'),
			('l4d2', 'commander-general', 15000, 17499, 'Commander General'),
			('l4d2', 'field-vice-marshal', 17500, 19999, 'Field Vice Marshal'),
			('l4d2', 'field-marshal', 20000, 22499, 'Field Marshal'),
			('l4d2', 'vice-commander-of-the-army', 22500, 24999, 'Vice Commander of the Army'),
			('l4d2', 'commander-of-the-army', 25000, 27499, 'Commander of the Army'),
			('l4d2', 'high-commander', 27500, 29999, 'High Commander'),
			('l4d2', 'supreme-commander', 30000, 34999, 'Supreme Commander'),
			('l4d2', 'terminator', 35000, 9999999, 'Terminator');
		");

	$db->query("
		INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES
		('boomer_claw', '5', '0', 'l4d2', '1_boomer_claw.png', 'Bronze Boom!'),
		('headshot', '5', '0', 'l4d2', '1_headshot.png', 'Bronze Brain Salad'),
		('healed_teammate', '5', '0', 'l4d2', '1_healed_teammate.png', 'Bronze Field Medic'),
		('hunter_claw', '5', '0', 'l4d2', '1_hunter_claw.png', 'Bronze Grim Reaper'),
		('inferno', '5', '0', 'l4d2', '1_inferno.png', 'Bronze Cremator'),
		('killed_exploding', '5', '0', 'l4d2', '1_killed_exploding.png', 'Bronze Stomach Upset'),
		('killed_gas', '5', '0', 'l4d2', '1_killed_gas.png', 'Bronze Tongue Twister'),
		('killed_hunter', '5', '0', 'l4d2', '1_killed_hunter.png', 'Bronze Hunter Punter'),
		('killed_survivor', '5', '0', 'l4d2', '1_killed_survivor.png', 'Bronze Dead Wreckening'),
		('killed_tank', '5', '0', 'l4d2', '1_killed_tank.png', 'Bronze Tankbuster'),
		('killed_witch', '5', '0', 'l4d2', '1_killed_witch.png', 'Bronze Inquisitor'),
		('latency', '5', '0', 'l4d2', '1_latency.png', 'Bronze Nothing Special'),
		('pipe_bomb', '5', '0', 'l4d2', '1_pipe_bomb.png', 'Bronze Pyrotechnician'),
		('pounce', '5', '0', 'l4d2', '1_pounce.png', 'Bronze Free 2 Fly'),
		('rescued_survivor', '5', '0', 'l4d2', '1_rescued_survivor.png', 'Bronze Ground Cover'),
		('revived_teammate', '5', '0', 'l4d2', '1_revived_teammate.png', 'Bronze Helping Hand'),
		('smoker_claw', '5', '0', 'l4d2', '1_smoker_claw.png', 'Bronze Chain Smoker'),
		('tank_claw', '5', '0', 'l4d2', '1_tank_claw.png', 'Bronze Lambs 2 Slaughter'),
		('tongue_grab', '5', '0', 'l4d2', '1_tongue_grab.png', 'Bronze Drag &amp; Drop'),
		('vomit', '5', '0', 'l4d2', '1_vomit.png', 'Bronze Barf Bagged'),
		('boomer_claw', '15', '0', 'l4d2', '2_boomer_claw.png', 'Silver Boom!'),
		('headshot', '15', '0', 'l4d2', '2_headshot.png', 'Silver Brain Salad'),
		('healed_teammate', '15', '0', 'l4d2', '2_healed_teammate.png', 'Silver Field Medic'),
		('hunter_claw', '15', '0', 'l4d2', '2_hunter_claw.png', 'Silver Grim Reaper'),
		('inferno', '15', '0', 'l4d2', '2_inferno.png', 'Silver Cremator'),
		('killed_exploding', '15', '0', 'l4d2', '2_killed_exploding.png', 'Silver Stomach Upset'),
		('killed_gas', '15', '0', 'l4d2', '2_killed_gas.png', 'Silver Tongue Twister'),
		('killed_hunter', '15', '0', 'l4d2', '2_killed_hunter.png', 'Silver Hunter Punter'),
		('killed_survivor', '15', '0', 'l4d2', '2_killed_survivor.png', 'Silver Dead Wreckening'),
		('killed_tank', '15', '0', 'l4d2', '2_killed_tank.png', 'Silver Tankbuster'),
		('killed_witch', '15', '0', 'l4d2', '2_killed_witch.png', 'Silver Inquisitor'),
		('latency', '15', '0', 'l4d2', '2_latency.png', 'Silver Nothing Special'),
		('pipe_bomb', '15', '0', 'l4d2', '2_pipe_bomb.png', 'Silver Pyrotechnician'),
		('pounce', '15', '0', 'l4d2', '2_pounce.png', 'Silver Free 2 Fly'),
		('rescued_survivor', '15', '0', 'l4d2', '2_rescued_survivor.png', 'Silver Ground Cover'),
		('revived_teammate', '15', '0', 'l4d2', '2_revived_teammate.png', 'Silver Helping Hand'),
		('smoker_claw', '15', '0', 'l4d2', '2_smoker_claw.png', 'Silver Chain Smoker'),
		('tank_claw', '15', '0', 'l4d2', '2_tank_claw.png', 'Silver Lambs 2 Slaughter'),
		('tongue_grab', '15', '0', 'l4d2', '2_tongue_grab.png', 'Silver Drag &amp; Drop'),
		('vomit', '15', '0', 'l4d2', '2_vomit.png', 'Silver Barf Bagged'),
		('boomer_claw', '30', '0', 'l4d2', '3_boomer_claw.png', 'Golden Boom!'),
		('headshot', '30', '0', 'l4d2', '3_headshot.png', 'Golden Brain Salad'),
		('healed_teammate', '30', '0', 'l4d2', '3_healed_teammate.png', 'Golden Field Medic'),
		('hunter_claw', '30', '0', 'l4d2', '3_hunter_claw.png', 'Golden Grim Reaper'),
		('inferno', '30', '0', 'l4d2', '3_inferno.png', 'Golden Cremator'),
		('killed_exploding', '30', '0', 'l4d2', '3_killed_exploding.png', 'Golden Stomach Upset'),
		('killed_gas', '30', '0', 'l4d2', '3_killed_gas.png', 'Golden Tongue Twister'),
		('killed_hunter', '30', '0', 'l4d2', '3_killed_hunter.png', 'Golden Hunter Punter'),
		('killed_survivor', '30', '0', 'l4d2', '3_killed_survivor.png', 'Golden Dead Wreckening'),
		('killed_tank', '30', '0', 'l4d2', '3_killed_tank.png', 'Golden Tankbuster'),
		('killed_witch', '30', '0', 'l4d2', '3_killed_witch.png', 'Golden Inquisitor'),
		('latency', '30', '0', 'l4d2', '3_latency.png', 'Golden Nothing Special'),
		('pipe_bomb', '30', '0', 'l4d2', '3_pipe_bomb.png', 'Golden Pyrotechnician'),
		('pounce', '30', '0', 'l4d2', '3_pounce.png', 'Golden Free 2 Fly'),
		('rescued_survivor', '30', '0', 'l4d2', '3_rescued_survivor.png', 'Golden Ground Cover'),
		('revived_teammate', '30', '0', 'l4d2', '3_revived_teammate.png', 'Golden Helping Hand'),
		('smoker_claw', '30', '0', 'l4d2', '3_smoker_claw.png', 'Golden Chain Smoker'),
		('tank_claw', '30', '0', 'l4d2', '3_tank_claw.png', 'Golden Lambs 2 Slaughter'),
		('tongue_grab', '30', '0', 'l4d2', '3_tongue_grab.png', 'Golden Drag &amp; Drop'),
		('vomit', '30', '0', 'l4d2', '3_vomit.png', 'Golden Barf Bagged'),
		('boomer_claw', '50', '0', 'l4d2', '4_boomer_claw.png', 'Bloody Boom!'),
		('headshot', '50', '0', 'l4d2', '4_headshot.png', 'Bloody Brain Salad'),
		('healed_teammate', '50', '0', 'l4d2', '4_healed_teammate.png', 'Bloody Field Medic'),
		('hunter_claw', '50', '0', 'l4d2', '4_hunter_claw.png', 'Bloody Grim Reaper'),
		('inferno', '50', '0', 'l4d2', '4_inferno.png', 'Bloody Cremator'),
		('killed_exploding', '50', '0', 'l4d2', '4_killed_exploding.png', 'Bloody Stomach Upset'),
		('killed_gas', '50', '0', 'l4d2', '4_killed_gas.png', 'Bloody Tongue Twister'),
		('killed_hunter', '50', '0', 'l4d2', '4_killed_hunter.png', 'Bloody Hunter Punter'),
		('killed_survivor', '50', '0', 'l4d2', '4_killed_survivor.png', 'Bloody Dead Wreckening'),
		('killed_tank', '50', '0', 'l4d2', '4_killed_tank.png', 'Bloody Tankbuster'),
		('killed_witch', '50', '0', 'l4d2', '4_killed_witch.png', 'Bloody Inquisitor'),
		('latency', '50', '0', 'l4d2', '4_latency.png', 'Bloody Nothing Special'),
		('pipe_bomb', '50', '0', 'l4d2', '4_pipe_bomb.png', 'Bloody Pyrotechnician'),
		('pounce', '50', '0', 'l4d2', '4_pounce.png', 'Bloody Free 2 Fly'),
		('rescued_survivor', '50', '0', 'l4d2', '4_rescued_survivor.png', 'Bloody Ground Cover'),
		('revived_teammate', '50', '0', 'l4d2', '4_revived_teammate.png', 'Bloody Helping Hand'),
		('smoker_claw', '50', '0', 'l4d2', '4_smoker_claw.png', 'Bloody Chain Smoker'),
		('tank_claw', '50', '0', 'l4d2', '4_tank_claw.png', 'Bloody Lambs 2 Slaughter'),
		('tongue_grab', '50', '0', 'l4d2', '4_tongue_grab.png', 'Bloody Drag &amp; Drop'),
		('vomit', '50', '0', 'l4d2', '4_vomit.png', 'Bloody Barf Bagged');
	");
	
	$tf2games = array();
	$result = "SELECT code FROM hlstats_Games WHERE realgame = 'tf'";
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($tf2games, $db->escape($rowdata[0]));
	}
	
	foreach($tf2games as $game)
	{
		$db->query("
			INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
				('$game', 'tf_pumpkin_bomb', 'Pumpkin Bomb', 2);
			");
			
		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('W','$game','tf_pumpkin_bomb', 'Pumpkin Bomber', 'kills with a pumpkin bomb'),
				('O','$game', 'engineer_extinguish', 'Dispensing a little love', 'extinguishes with a Dispensor'),
				('O','$game', 'medic_extinguish', 'You want a second opinion? You''re also ugly! ', 'extinguishes with Medic Gun');
			");
			
		$db->query("
			INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES
				('$game', 'jarate', 1, 0, '', 'Jarated player', '0', '1', '0', '0'),
				('$game', 'shield_blocked', 0, 0, '', 'Blocked with Shield', '0', '1', '0', '0');
			");
	}
	
			
	$zpsgames = array();
	$result = "SELECT code FROM hlstats_Games WHERE realgame = 'zps'";
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($zpsgames, $db->escape($rowdata[0]));
	}
	
	foreach($zpsgames as $game)
	{
		$db->query("
			INSERT IGNORE INTO `hlstats_Weapons` (`game`, `code`, `name`, `modifier`) VALUES
				('$game', 'bat_aluminum', 'Bat (Aluminum)', 1.5),
				('$game', 'bat_wood', 'Bat (Wood)', 1.5),
				('$game', 'm4', 'M4', 1),
				('$game', 'pipe', 'Pipe', 1),
				('$game', 'slam', 'IED', 1);
			");
			
		$db->query("
			INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES
				('$game', 'headshot', 1, 0, '', 'Headshot Kill', '1', '0', '0', '0')
			");

		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('W', '$game','bat_aluminum','Out of the park!','kills with Bat (Aluminum)'),
				('W', '$game', 'bat_wood','Corked','kills with Bat (Wood)'),
				('W', '$game', 'm4','M4','kills with M4'),
				('W', '$game', 'pipe','Piping hot','kills with Pipe'),
				('W', '$game', 'slam','IEDs','kills with IED'),
				('O', '$game', 'headshot', 'Headshot King', 'headshot kills');
			");
	}

	$ntsgames = array();
	$result = "SELECT code FROM hlstats_Games WHERE realgame = 'nts'";
	while ($rowdata = $db->fetch_row($result))
	{ 
		array_push($ntsgames, $db->escape($rowdata[0]));
	}
	
	foreach($ntsgames as $game)
	{
		$db->query("
			DELETE FROM hlstats_Awards WHERE `code` = 'mp5' AND `game` = '$game'
			");
	
		$db->query("
			DELETE FROM hlstats_Weapons WHERE `code` = 'mp5' AND `game` = '$game'
			");
			
		$db->query("
			INSERT IGNORE INTO `hlstats_Actions` (`game`, `code`, `reward_player`, `reward_team`, `team`, `description`, `for_PlayerActions`, `for_PlayerPlayerActions`, `for_TeamActions`, `for_WorldActions`) VALUES
				('$game', 'headshot', 5, 0, '', 'Headshot Kill', '1', '0', '0', '0'),
				('$game', 'kill_streak_10', 9, 0, '', 'Monster Kill (10 kills)', '1', '0', '0', '0'),
				('$game', 'kill_streak_11', 10, 0, '', 'Unstoppable (11 kills)', '1', '0', '0', '0'),
				('$game', 'kill_streak_12', 15, 0, '', 'God Like (12+ kills)', '1', '0', '0', '0'),
				('$game', 'kill_streak_2', 1, 0, '', 'Double Kill (2 kills)', '1', '0', '0', '0'),
				('$game', 'kill_streak_3', 2, 0, '', 'Triple Kill (3 kills)', '1', '0', '0', '0'),
				('$game', 'kill_streak_4', 3, 0, '', 'Domination (4 kills)', '1', '0', '0', '0'),
				('$game', 'kill_streak_5', 4, 0, '', 'Rampage (5 kills)', '1', '0', '0', '0'),
				('$game', 'kill_streak_6', 5, 0, '', 'Mega Kill (6 kills)', '1', '0', '0', '0'),
				('$game', 'kill_streak_7', 6, 0, '', 'Ownage (7 kills)', '1', '0', '0', '0'),
				('$game', 'kill_streak_8', 7, 0, '', 'Ultra Kill (8 kills)', '1', '0', '0', '0'),
				('$game', 'kill_streak_9', 8, 0, '', 'Killing Spree (9 kills)', '1', '0', '0', '0'),
				('$game', 'Round_Win', 0, 20, '', 'Team Round Win', '0', '0', '1', '0');
			");
			
		$db->query("
			INSERT IGNORE INTO `hlstats_Awards` (`awardType`, `game`, `code`, `name`, `verb`) VALUES
				('O', '$game', 'headshot', 'Headshot King', 'headshot kills');
			");
			
		$db->query("
			INSERT IGNORE INTO `hlstats_Ribbons` (`awardCode`, `awardCount`, `special`, `game`, `image`, `ribbonName`) VALUES
				('aa13', 1, 0, '$game', '1_aa13.png', 'Bronze AA13'),
				('aa13', 5, 0, '$game', '2_aa13.png', 'Silver AA13'),
				('aa13', 10, 0, '$game', '3_aa13.png', 'Gold AA13'),
				('grenade_projectile', 1, 0, '$game', '1_grenade.png', 'Bronze Frag Grenade'),
				('grenade_projectile', 5, 0, '$game', '2_grenade.png', 'Silver Frag Grenade'),
				('grenade_projectile', 10, 0, '$game', '3_grenade.png', 'Gold Frag Grenade'),
				('headshot', 1, 0, '$game', '1_headshot.png', 'Bronze Headshot'),
				('headshot', 5, 0, '$game', '2_headshot.png', 'Silver Headshot'),
				('headshot', 10, 0, '$game', '3_headshot.png', 'Gold Headshot'),
				('knife', 1, 0, '$game', '1_knife.png', 'Bronze Knife'),
				('knife', 5, 0, '$game', '2_knife.png', 'Silver Knife '),
				('knife', 10, 0, '$game', '3_knife.png', 'Gold Knife '),
				('kyla', 1, 0, '$game', '1_kyla9.png', 'Bronze Kyla-9'),
				('kyla', 5, 0, '$game', '2_kyla9.png', 'Silver Kyla-9'),
				('kyla', 10, 0, '$game', '3_kyla9.png', 'Gold Kyla-9'),
				('latency', 1, 0, '$game', '1_latency.png', 'Bronze Best Latency'),
				('latency', 5, 0, '$game', '2_latency.png', 'Silver Best Latency'),
				('latency', 10, 0, '$game', '3_latency.png', 'Gold Best Latency'),
				('m41', 1, 0, '$game', '1_m41.png', 'Bronze M41'),
				('m41', 5, 0, '$game', '2_m41.png', 'Silver M41'),
				('m41', 10, 0, '$game', '3_m41.png', 'Gold M41'),
				('m41l', 1, 0, '$game', '1_m41l.png', 'Bronze M41L'),
				('m41l', 5, 0, '$game', '2_m41l.png', 'Silver M41L'),
				('m41l', 10, 0, '$game', '3_m41l.png', 'Gold M41L'),
				('milso', 1, 0, '$game', '1_milso.png', 'Bronze MilSO'),
				('milso', 5, 0, '$game', '2_milso.png', 'Silver MilSO'),
				('milso', 10, 0, '$game', '3_milso.png', 'Gold MilSO'),
				('mostkills', 1, 0, '$game', '1_mostkills.png', 'Bronze Most Kills'),
				('mostkills', 5, 0, '$game', '2_mostkills.png', 'Silver Most Kills'),
				('mostkills', 10, 0, '$game', '3_mostkills.png', 'Gold Most Kills'),
				('mpn', 1, 0, '$game', '1_mpn45.png', 'Bronze MPN45'),
				('mpn', 5, 0, '$game', '2_mpn45.png', 'Silver MPN45'),
				('mpn', 10, 0, '$game', '3_mpn45.png', 'Gold MPN45'),
				('mx', 1, 0, '$game', '1_mx-5.png', 'Bronze MX'),
				('mx', 5, 0, '$game', '2_mx-5.png', 'Silver MX'),
				('mx', 10, 0, '$game', '3_mx-5.png', 'Gold MX'),
				('mx_silenced', 1, 0, '$game', '1_mxs-5.png', 'Bronze MX Silenced'),
				('mx_silenced', 5, 0, '$game', '2_mxs-5.png', 'Silver MX Silenced'),
				('mx_silenced', 10, 0, '$game', '3_mxs-5.png', 'Gold MX Silenced'),
				('pz', 1, 0, '$game', '1_supa7.png', 'Bronze MURATA SUPA 7'),
				('pz', 5, 0, '$game', '2_supa7.png', 'Silver MURATA SUPA 7'),
				('supa7', 10, 0, '$game', '3_supa7.png', 'Gold MURATA SUPA 7'),
				('tachi', 1, 0, '$game', '1_tachi.png', 'Bronze TACHI'),
				('tachi', 5, 0, '$game', '2_tachi.png', 'Silver TACHI'),
				('tachi', 10, 0, '$game', '3_tachi.png', 'Gold TACHI'),
				('zr68c', 1, 0, '$game', '1_zr68c.png', 'Bronze ZR68C'),
				('zr68c', 5, 0, '$game', '2_zr68c.png', 'Silver ZR68C'),
				('zr68c', 10, 0, '$game', '3_zr68c.png', 'Gold ZR68C'),
				('zr68l', 1, 0, '$game', '1_zr68l.png', 'Bronze ZR68L'),
				('zr68l', 5, 0, '$game', '2_zr68l.png', 'Silver ZR68L'),
				('zr68l', 10, 0, '$game', '3_zr68l.png', 'Gold ZR68L'),
				('zr68s', 1, 0, '$game', '1_zr68s.png', 'Bronze ZR68S'),
				('zr68s', 5, 0, '$game', '2_zr68s.png', 'Silver ZR68S'),
				('zr68s', 10, 0, '$game', '3_zr68s.png', 'Gold ZR68S');
			");
	}
	
	$cs16ribbonfix = array(
		'ak47' => '2_ak47.png',
		'awp' => '2_awp.png',
		'deagle' => '2_deagle.png',
		'elite' => '2_elite.png',
		'famas' => '2_famas.png',
		'galil' => '2_galil.png',
		'glock18' => '2_glock.png',
		'knife' => '2_knife.png',
		'latency' => '2_latency.png',
		'm3' => '2_m3.png',
		'm4a1' => '2_m4a1.png',
		'p90' => '2_p90.png',
		'scout' => '2_scout.png',
		'usp' => '2_usp.png',
		'killed_a_hostage' => '2_killed_a_hostage.png',
		'rescued_a_hostage' => '2_rescued_a_hostage.png',
		'planted_the_bomb' => '2_planted_the_bomb.png',
		'grenade' => '2_hegrenade.png',
		'defused_the_bomb' => '2_defused_the_bomb.png'
		);
	foreach ($cs16ribbonfix as $code => $img)
	{
		$db->query("
			UPDATE IGNORE `hlstats_Ribbons` SET `image` = '$img' WHERE `awardCode` = '$code' AND `awardCount` = 5 AND `game` = 'cstrike';
			");
	}
	
	$db->query("
		DELETE FROM `hlstats_Options` WHERE `keyname` IN ('trendgraphfile','google_map_key')
	");
	
	$db->query("
		INSERT IGNORE INTO `hlstats_Options` (`keyname`, `value`, `opttype`) VALUES
			('sourcebans_address', '',  2),
			('forum_address', '',  2),
			('display_gamelist', '1', 2),
			('display_style_selector', '0', 2);
	");		
	
	$db->query("
		INSERT INTO `hlstats_Options_Choices` (`keyname`, `value`, `text`, `isDefault`) VALUES		
			('display_gamelist', '1', 'Yes', 1),
			('display_gamelist', '0', 'No', 0),
			('display_style_selector', '1', 'Yes', 0),
			('display_style_selector', '0', 'No', 1);
	");
	
	$db->query("
		UPDATE hlstats_Options SET `value` = '1.6.2' WHERE `keyname` = 'version'
		");
	
	$db->query("
		UPDATE hlstats_Options SET `value` = '10' WHERE `keyname` = 'dbversion'
		");
?>
