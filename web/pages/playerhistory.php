<?php
/*
HLstatsX Community Edition - Real-time player and clan rankings and statistics
Copyleft (L) 2008-20XX Nicholas Hastings (nshastings@gmail.com)
http://www.hlxcommunity.com

HLstatsX Community Edition is a continuation of 
ELstatsNEO - Real-time player and clan rankings and statistics
Copyleft (L) 2008-20XX Malte Bayer (steam@neo-soft.org)
http://ovrsized.neo-soft.org/

ELstatsNEO is an very improved & enhanced - so called Ultra-Humongus Edition of HLstatsX
HLstatsX - Real-time player and clan rankings and statistics for Half-Life 2
http://www.hlstatsx.com/
Copyright (C) 2005-2007 Tobias Oetzel (Tobi@hlstatsx.com)

HLstatsX is an enhanced version of HLstats made by Simon Garner
HLstats - Real-time player and clan rankings and statistics for Half-Life
http://sourceforge.net/projects/hlstats/
Copyright (C) 2001  Simon Garner
            
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

For support and installation notes visit http://www.hlxcommunity.com
*/

	if ( !defined('IN_HLSTATS') )
	{
		die('Do not access this file directly.');
	}
// Player History
	$player = valid_request(intval($_GET['player']), 1)
		or error('No player ID specified.');
	$db->query
	("
		SELECT
			hlstats_Players.lastName,
			hlstats_Players.game
		FROM
			hlstats_Players
		WHERE
			hlstats_Players.playerId = $player
	");
	if ($db->num_rows() != 1)
	{
		error("No such player '$player'.");
	}
	$playerdata = $db->fetch_array();
	$pl_name = $playerdata['lastName'];
	if (strlen($pl_name) > 10)
	{
		$pl_shortname = substr($pl_name, 0, 8) . '...';
	}
	else
	{
		$pl_shortname = $pl_name;
	}
	$pl_name = htmlspecialchars($pl_name, ENT_COMPAT);
	$pl_shortname = htmlspecialchars($pl_shortname, ENT_COMPAT);
	$game = $playerdata['game'];
	$db->query
	("
		SELECT
			hlstats_Games.name
		FROM
			hlstats_Games
		WHERE
			hlstats_Games.code = '$game'
	");
	if ($db->num_rows() != 1)
	{
		$gamename = ucfirst($game);
	}
	else
	{
		list($gamename) = $db->fetch_row();
	}	
	pageHeader
	(
		array ($gamename, 'Event History', $pl_name),
		array
		(
			$gamename=>$g_options['scripturl'] . "?game=$game",
			'Player Rankings'=>$g_options['scripturl'] . "?mode=players&game=$game",
			'Player Details'=>$g_options['scripturl'] . "?mode=playerinfo&player=$player",
			'Event History'=>''
		),
		$playername
	);
	flush();
	$table = new Table
	(
		array
		(
			new TableColumn
			(
				'eventTime',
				'Date',
				'width=20'
			),
			new TableColumn
			(
				'eventType',
				'Type',
				'width=10&align=center'
			),
			new TableColumn
			(
				'eventDesc',
				'Description',
				'width=40&sort=no&append=.&embedlink=yes'
			),
			new TableColumn
			(
				'serverName',
				'Server',
				'width=20'
			),
			new TableColumn
			(
				'map',
				'Map',
				'width=10'
			)
		),
		'eventTime',
		'eventTime',
		'eventType',
		false,
		50,
		'page',
		'sort',
		'sortorder'
	);
	$surl = $g_options['scripturl'];
// This would be better done with a UNION query, I think, but MySQL doesn't
// support them yet. (NOTE you need MySQL 3.23 for temporary table support.)
	$db->query
	("
		DROP TABLE IF EXISTS
			hlstats_EventHistory
	");
	$db->query
	("
		CREATE TEMPORARY TABLE
			hlstats_EventHistory
			(
				eventType VARCHAR(32) NOT NULL,
				eventTime DATETIME NOT NULL,
				eventDesc VARCHAR(255) NOT NULL,
				serverName VARCHAR(255) NOT NULL,
				map VARCHAR(64) NOT NULL
			) DEFAULT CHARSET=utf8
	");
	function insertEvents ($table, $select)
	{
		global $db;
		$select = str_replace("<table>", "hlstats_Events_$table", $select);
		$db->query
		("
			INSERT INTO
				hlstats_EventHistory
				(
					eventType,
					eventTime,
					eventDesc,
					serverName,
					map
				)
			$select
		");
	}
	insertEvents
	('TeamBonuses', "
		SELECT
			'Team Bonus',
			<table>.eventTime,
			CONCAT('My team received a points bonus of ', bonus, ' for triggering \"', IFNULL(hlstats_Actions.description,'Unknown'), '\"'),
			IFNULL(hlstats_Servers.name, 'Unknown'),
			<table>.map
		FROM
			<table>
		LEFT JOIN
			hlstats_Actions
		ON
			<table>.actionId = hlstats_Actions.id
		LEFT JOIN
			hlstats_Servers
		ON
			hlstats_Servers.serverId = <table>.serverId
		WHERE
			<table>.playerId = $player
		AND
			hlstats_Actions.game = '$game'
	");
	if ($g_options["Mode"] == "LAN")
	{
		$uqIdStr = "IP Address:";
	}
	else
	{
		$uqIdStr = "Unique ID:";
	}
	insertEvents
	('Connects', "
		SELECT
			'Connect',
			<table>.eventTime,
			CONCAT('I connected to the server'),
			IFNULL(hlstats_Servers.name, 'Unknown'),
			<table>.map
		FROM
			<table>
		LEFT JOIN
			hlstats_Servers
		ON
			hlstats_Servers.serverId = <table>.serverId
		WHERE
			<table>.playerId = $player
	");
	insertEvents
	('Disconnects', "
		SELECT
			'Disconnect',
			<table>.eventTime,
			'I left the game',
			IFNULL(hlstats_Servers.name, 'Unknown'),
			<table>.map
		FROM
			<table>
		LEFT JOIN
			hlstats_Servers
		ON
			hlstats_Servers.serverId = <table>.serverId
		WHERE
			<table>.playerId = $player
	");
	insertEvents
	('Entries', "
		SELECT
			'Entry',
			<table>.eventTime,
			'I entered the game',
			IFNULL(hlstats_Servers.name, 'Unknown'),
			<table>.map
		FROM
			<table>
		LEFT JOIN
			hlstats_Servers
		ON
			hlstats_Servers.serverId = <table>.serverId
		WHERE
			<table>.playerId = $player
	");
	insertEvents
	('Frags', "
		SELECT
			'Kill',
			<table>.eventTime,
			CONCAT('I killed %A%$surl?mode=playerinfo&player=', victimId, '%', IFNULL(hlstats_Players.lastName,'Unknown'), '%/A%', ' with ', weapon),
			IFNULL(hlstats_Servers.name, 'Unknown'),
			<table>.map
		FROM
			<table>
		LEFT JOIN
			hlstats_Servers
		ON
			hlstats_Servers.serverId = <table>.serverId
		LEFT JOIN
			hlstats_Players
		ON
			hlstats_Players.playerId = <table>.victimId
		WHERE
			<table>.killerId = $player
			AND <table>.headshot = 0
	");
	insertEvents
	('Frags', "
		SELECT
			'Kill',
			<table>.eventTime,
			CONCAT('I killed %A%$surl?mode=playerinfo&player=', victimId, '%', IFNULL(hlstats_Players.lastName,'Unknown'), '%/A%', ' with a headshot from ', weapon),
			IFNULL(hlstats_Servers.name, 'Unknown'),
			<table>.map
		FROM
			<table>
		LEFT JOIN
			hlstats_Servers
		ON
			hlstats_Servers.serverId = <table>.serverId
		LEFT JOIN
			hlstats_Players
		ON
			hlstats_Players.playerId = <table>.victimId
		WHERE
			<table>.killerId = $player
			AND <table>.headshot = 1
	");
	insertEvents
	('Frags', "
		SELECT
			'Death',
			<table>.eventTime,
			CONCAT('%A%$surl?mode=playerinfo&player=', killerId, '%', IFNULL(hlstats_Players.lastName,'Unknown'), '%/A%', ' killed me with ', weapon),
			IFNULL(hlstats_Servers.name, 'Unknown'),
			<table>.map
		FROM
			<table>
		LEFT JOIN
			hlstats_Servers
		ON
			hlstats_Servers.serverId = <table>.serverId
		LEFT JOIN
			hlstats_Players
		ON
			hlstats_Players.playerId = <table>.killerId
		WHERE
			<table>.victimId = $player
	");
	insertEvents
	('Teamkills', "
		SELECT
			'Team Kill',
			<table>.eventTime,
			CONCAT('I killed teammate %A%$surl?mode=playerinfo&player=', victimId, '%', IFNULL(hlstats_Players.lastName,'Unknown'), '%/A%', ' with ', weapon),
			IFNULL(hlstats_Servers.name, 'Unknown'),
			<table>.map
		FROM
			<table>
		LEFT JOIN
			hlstats_Servers
		ON
			hlstats_Servers.serverId = <table>.serverId
		LEFT JOIN
			hlstats_Players
		ON
			hlstats_Players.playerId = <table>.victimId
		WHERE
			<table>.killerId = $player
	");
	insertEvents
	('Teamkills', "
		SELECT
			'Friendly Fire',
			<table>.eventTime,
			CONCAT('My teammate %A%$surl?mode=playerinfo&player=', killerId, '%', IFNULL(hlstats_Players.lastName, 'Unknown'), '%/A%', ' killed me with ', weapon),
			IFNULL(hlstats_Servers.name, 'Unknown'),
			<table>.map
		FROM
			<table>
		LEFT JOIN
			hlstats_Servers
		ON
			hlstats_Servers.serverId = <table>.serverId
		LEFT JOIN
			hlstats_Players
		ON
			hlstats_Players.playerId = <table>.killerId
		WHERE
			<table>.victimId = $player
	");
	insertEvents
	('ChangeRole', "
		SELECT
			'Role',
			<table>.eventTime,
			CONCAT('I changed role to ', role),
			IFNULL(hlstats_Servers.name, 'Unknown'),
			<table>.map
		FROM
			<table>
		LEFT JOIN
			hlstats_Servers
		ON
			hlstats_Servers.serverId = <table>.serverId
		WHERE
			<table>.playerId = $player
	");
	insertEvents
	('ChangeName', "
		SELECT
			'Name',
			<table>.eventTime,
			CONCAT('I changed my name from \"', oldName, '\" to \"', newName, '\"'),
			IFNULL(hlstats_Servers.name, 'Unknown'),
			<table>.map
		FROM
			<table>
		LEFT JOIN
			hlstats_Servers
		ON
			hlstats_Servers.serverId = <table>.serverId
		WHERE
			<table>.playerId = $player
	");
	insertEvents
	('PlayerActions', "
		SELECT
			'Action',
			<table>.eventTime,
			CONCAT('I received a points bonus of ', bonus, ' for triggering \"', IFNULL(hlstats_Actions.description,'Unknown'), '\"'),
			IFNULL(hlstats_Servers.name, 'Unknown'),
			<table>.map
		FROM
			<table>
		LEFT JOIN
			hlstats_Servers
		ON
			hlstats_Servers.serverId = <table>.serverId
		LEFT JOIN
			hlstats_Actions
		ON
			hlstats_Actions.id = <table>.actionId
		WHERE
			<table>.playerId = $player
		AND
			hlstats_Actions.game = '$game'
	");
	insertEvents
	('PlayerPlayerActions', "
		SELECT
			'Action',
			<table>.eventTime,
			CONCAT('I received a points bonus of ', bonus, ' for triggering \"', IFNULL(hlstats_Actions.description,'Unknown'), '\" against %A%$surl?mode=playerinfo&player=', victimId, '%', IFNULL(hlstats_Players.lastName,'Unknown'), '%/A%'),
			IFNULL(hlstats_Servers.name, 'Unknown'),
			<table>.map
		FROM
			<table>
		LEFT JOIN
			hlstats_Servers
		ON
			hlstats_Servers.serverId = <table>.serverId
		LEFT JOIN
			hlstats_Actions
		ON
			hlstats_Actions.id = <table>.actionId
		LEFT JOIN hlstats_Players ON
			hlstats_Players.playerId = <table>.victimId
		WHERE
			<table>.playerId = $player
		AND
			hlstats_Actions.game = '$game'
	");
	insertEvents
	('PlayerPlayerActions', "
		SELECT
			'Action',
			<table>.eventTime,
			CONCAT('%A%$surl?mode=playerinfo&player=', <table>.playerId, '%', IFNULL(hlstats_Players.lastName,'Unknown'), '%/A% triggered \"', IFNULL(hlstats_Actions.description,'Unknown'), '\" against me'),
			IFNULL(hlstats_Servers.name, 'Unknown'),
			<table>.map
		FROM
			<table>
		LEFT JOIN
			hlstats_Servers
		ON
			hlstats_Servers.serverId = <table>.serverId
		LEFT JOIN
			hlstats_Actions
		ON
			hlstats_Actions.id = <table>.actionId
		LEFT JOIN
			hlstats_Players
		ON
			hlstats_Players.playerId = <table>.playerId
		WHERE
			<table>.victimId = $player
		AND
			hlstats_Actions.game = '$game'
	");
	insertEvents
	('Suicides', "
		SELECT
			'Suicide',
			<table>.eventTime,
			CONCAT('I committed suicide with \"', weapon, '\"'),
			IFNULL(hlstats_Servers.name, 'Unknown'),
			<table>.map
		FROM
			<table>
		LEFT JOIN
			hlstats_Servers
		ON
			hlstats_Servers.serverId = <table>.serverId
		WHERE
			<table>.playerId = $player
	");
	insertEvents
	('ChangeTeam', "
		SELECT
			'Team',
			<table>.eventTime,
			IF(hlstats_Teams.name IS NULL, CONCAT('I joined team \"', team, '\"'), CONCAT('I joined team \"', team, '\" (', hlstats_Teams.name, ')')),
			IFNULL(hlstats_Servers.name, 'Unknown'),
			<table>.map
		FROM
			<table>
		LEFT JOIN
			hlstats_Servers
		ON
			hlstats_Servers.serverId = <table>.serverId
		LEFT JOIN
			hlstats_Teams
		ON
			hlstats_Teams.code = <table>.team
		WHERE
			<table>.playerId = $player
		AND
			hlstats_Teams.game = '$game'
	");
	$result = $db->query
	("
		SELECT
			hlstats_EventHistory.eventTime,
			hlstats_EventHistory.eventType,
			hlstats_EventHistory.eventDesc,
			hlstats_EventHistory.serverName,
			hlstats_EventHistory.map
		FROM
			hlstats_EventHistory
		ORDER BY
			$table->sort $table->sortorder,
			$table->sort2 $table->sortorder
		LIMIT
			$table->startitem,
			$table->numperpage
	");
	$resultCount = $db->query
	("
		SELECT
			COUNT(*)
		FROM
			hlstats_EventHistory
	");
	list($numitems) = $db->fetch_row($resultCount);
?>

<div class="block">
<?php
	printSectionTitle('Player Event History (Last '.$g_options['DeleteDays'].' Days)');
	if ($numitems > 0)
	{
		$table->draw($result, $numitems, 95);
	}
?><br /><br />
	<div class="subblock">
		<div style="float:right;">
			Go to: <a href="<?php echo $g_options['scripturl'] . "?mode=playerinfo&amp;player=$player"; ?>"><?php echo $pl_name; ?>'s Statistics</a>
		</div>
	</div>
</div>
