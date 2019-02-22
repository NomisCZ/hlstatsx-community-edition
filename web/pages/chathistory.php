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
// Player Chat History
	$player = valid_request(intval($_GET['player']), 1)
		or error('No player ID specified.');
	$db->query
	("
		SELECT
			unhex(replace(hex(hlstats_Players.lastName), 'E280AE', '')) as lastName,
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
		array ($gamename, 'Chat History', $pl_name),
		array
		(
			$gamename=>$g_options['scripturl'] . "?game=$game",
			'Player Rankings'=>$g_options['scripturl'] . "?mode=players&game=$game",
			'Player Details'=>$g_options['scripturl'] . "?mode=playerinfo&player=$player",
			'Chat History'=>''
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
				'width=16'
			),

			new TableColumn
			(
				'message',
				'Message',
				'width=44&sort=no&append=.&embedlink=yes'
			),
			new TableColumn
			(
				'serverName',
				'Server',
				'width=24'
			),
			new TableColumn
			(
				'map',
				'Map',
				'width=16'
			)
		),
		'eventTime',
		'eventTime',
		'serverName',
		false,
		50,
		'page',
		'sort',
		'sortorder'
	);
	$surl = $g_options['scripturl'];
	
	$whereclause="hlstats_Events_Chat.playerId = $player ";
	$filter=isset($_REQUEST['filter'])?$_REQUEST['filter']:"";
	if(!empty($filter))
	{
				$whereclause.="AND MATCH (hlstats_Events_Chat.message) AGAINST ('" . $db->escape($filter) . "' in BOOLEAN MODE)";
	}
	
	$result = $db->query
	("
		SELECT
			hlstats_Events_Chat.eventTime,
			IF(hlstats_Events_Chat.message_mode=2, CONCAT('(Team) ', hlstats_Events_Chat.message), IF(hlstats_Events_Chat.message_mode=3, CONCAT('(Squad) ', hlstats_Events_Chat.message), hlstats_Events_Chat.message)) AS message,
			hlstats_Servers.name AS serverName,
			hlstats_Events_Chat.map
		FROM
			hlstats_Events_Chat
		LEFT JOIN 
			hlstats_Servers
		ON
			hlstats_Events_Chat.serverId = hlstats_Servers.serverId
		WHERE
			$whereclause	
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
			hlstats_Events_Chat
		LEFT JOIN 
			hlstats_Servers
		ON
			hlstats_Events_Chat.serverId = hlstats_Servers.serverId
		WHERE
			$whereclause
	");
			
	list($numitems) = $db->fetch_row($resultCount);
	
?>
<div class="block">
<?php
	printSectionTitle('Player Chat History (Last '.$g_options['DeleteDays'].' Days)');
?>
	<div class="subblock">
		<div style="float:left;">
			<span>
			<form method="get" action="<?php echo $g_options['scripturl']; ?>" style="margin:0px;padding:0px;">
				<input type="hidden" name="mode" value="chathistory" />
				<input type="hidden" name="player" value="<?php echo $player; ?>" />
				<strong>&#8226;</strong>
				Filter: <input type="text" name="filter" value="<?php echo htmlentities($filter); ?>" /> 
				<input type="submit" value="View" class="smallsubmit" />
			</form>
			</span>
		</div>
	</div>
	<div style="clear: both; padding-top: 20px;"></div>
<?php
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
