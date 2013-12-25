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
// Player Details
	$player = valid_request(intval($_GET['player']), 1);
	$uniqueid = valid_request(strval($_GET['uniqueid']), 0);
	$game = valid_request(strval($_GET['game']), 0);
	if (!$player && $uniqueid)
	{
		if (!$game)
		{
			header("Location: " . $g_options['scripturl'] . "&mode=search&st=uniqueid&q=$uniqueid");
			exit;
		}
		$uniqueid = preg_replace('/^STEAM_\d+?\:/i','',$uniqueid);
		$db->query
		("
			SELECT
				hlstats_PlayerUniqueIds.playerId
			FROM
				hlstats_PlayerUniqueIds
			WHERE
				hlstats_PlayerUniqueIds.uniqueId = '$uniqueid'
		");
		if ($db->num_rows() > 1)
		{
			header("Location: " . $g_options['scripturl'] . "&mode=search&st=uniqueid&q=$uniqueid&game=$game");
			exit;
		}
		elseif ($db->num_rows() < 1)
		{
			error("No players found matching uniqueId '$uniqueid'");
		}
		else
		{
			list($player) = $db->fetch_row();
			$player = intval($player);
		}
	}
	elseif (!$player && !$uniqueid)
	{
		error("No player ID specified.");
	}
	$db->query
	("
		SELECT
			hlstats_Players.playerId,
			hlstats_Players.connection_time,
			unhex(replace(hex(hlstats_Players.lastName), 'E280AE', '')) as lastName,
			hlstats_Players.country,
			hlstats_Players.city,
			hlstats_Players.flag,
			hlstats_Players.clan,
			hlstats_Players.fullName,
			hlstats_Players.email,
			hlstats_Players.homepage,
			hlstats_Players.icq,
			hlstats_Players.game,
			hlstats_Players.hideranking,
			hlstats_Players.blockavatar,
			hlstats_Players.skill,
			hlstats_Players.kills,
			hlstats_Players.deaths,
			IFNULL(kills / deaths, '-') AS kpd,
			hlstats_Players.suicides,
			hlstats_Players.headshots,
			IFNULL(headshots / kills, '-') AS hpk,
			hlstats_Players.shots,
			hlstats_Players.hits,
			hlstats_Players.teamkills,
			IFNULL(ROUND((hits / shots * 100), 1), 0) AS acc,
			CONCAT(hlstats_Clans.name) AS clan_name,
			activity
		FROM
			hlstats_Players
		LEFT JOIN
			hlstats_Clans
		ON
			hlstats_Clans.clanId = hlstats_Players.clan
		WHERE
			hlstats_Players.playerId = '$player'
		LIMIT
			1
	");
	if ($db->num_rows() != 1)
	{
		error("No such player '$player'.");
	}
	$playerdata = $db->fetch_array();
	$db->free_result();
	$pl_name = $playerdata['lastName'];
	if (strlen($pl_name) > 10)
	{
		$pl_shortname = substr($pl_name, 0, 8) . '...';
	} else {
		$pl_shortname = $pl_name;
	}
	$pl_name = htmlspecialchars($pl_name, ENT_COMPAT);
	$pl_shortname = htmlspecialchars($pl_shortname, ENT_COMPAT);
	$pl_urlname = urlencode($playerdata['lastName']);
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
	$hideranking = $playerdata['hideranking'];
	if( $hideranking == 2 )
	{
		$statusmsg = '<span style="color:red;font-weight:bold;">Banned</span>';
	}
	else
	{
		$statusmsg = '<span style="color:green;font-weight:bold;">In good standing</span>';
	}
// Required on a few pages, just decided to add it here
// May get moved in the future
	$db->query
	("
		SELECT
			COUNT(hlstats_Events_Frags.killerId)
		FROM
			hlstats_Events_Frags
		WHERE
			hlstats_Events_Frags.killerId = '$player'
			AND hlstats_Events_Frags.headshot = 1
	");
	list($realheadshots) = $db->fetch_row();
	$db->query
	("
		SELECT
			COUNT(hlstats_Events_Frags.killerId)
		FROM
			hlstats_Events_Frags
		WHERE
			hlstats_Events_Frags.killerId = '$player'
	");
	list($realkills) = $db->fetch_row();
	$db->query
	("
		SELECT
			COUNT(hlstats_Events_Frags.victimId)
		FROM
			hlstats_Events_Frags
		WHERE
			hlstats_Events_Frags.victimId = '$player'
	");
	list($realdeaths) = $db->fetch_row();
	$db->query
	("
		SELECT
			COUNT(hlstats_Events_Teamkills.killerId)
		FROM
			hlstats_Events_Teamkills
		WHERE
			hlstats_Events_Teamkills.killerId = '$player'
	");
	list($realteamkills) = $db->fetch_row();
	if(!isset($_GET['killLimit']))
		$killLimit = 5;
	else 
		$killLimit = valid_request($_GET['killLimit'], 1);
	if ( $_GET['type'] == 'ajax' )
	{
		$tabs = explode('_', preg_replace('[^a-z]', '', $_GET['tab']));
		foreach ( $tabs as $tab )
		{
			if ( file_exists(PAGE_PATH . "/playerinfo_$tab.php") )
			{
				@include(PAGE_PATH . "/playerinfo_$tab.php");
			}
		}
		exit;
	}
	pageHeader
	(
		array ($gamename, 'Player Details', $pl_name),
		array
		(
			$gamename=>$g_options['scripturl'] . "?game=$game",
			'Player Rankings'=>$g_options['scripturl'] . "?mode=players&game=$game",
			'Player Details'=>""
		),
		$pl_name
	);
?>
<div class="block" id="main">
<?php	
	if ($g_options['playerinfo_tabs']=='1')
	{
?>
	<ul class="subsection_tabs" id="tabs_playerinfo">
		<li>
			<a href="#" id="tab_general_aliases">General</a>
		</li>
		<li>
			<a href="#" id="tab_playeractions_teams">Teams &amp; Actions</a>
		</li>
		<li>
			<a href="#" id="tab_weapons">Weapons</a>
		</li>
		<li>
			<a href="#" id="tab_mapperformance_servers">Maps &amp; Servers</a>
		</li>
		<li>
			<a href="#" id="tab_killstats">Killstats</a>
		</li>
	</ul><br />
	<div id="main_content"></div>
	<script type="text/javascript">
		var Tabs = new Tabs
		(
			$('main_content'), $$('#main ul.subsection_tabs a'),
			{
				'mode': 'playerinfo',
				'game': '<?php echo $game; ?>',
				'loadingImage': '<?php echo IMAGE_PATH; ?>/ajax.gif',
				'defaultTab': 'general_aliases',
				'extra':
				{
					'player': '<?php echo $player; ?>', 'killLimit': '<?php echo $killLimit; ?>'
				}
			}
		);
	</script>
<?php
	}
	else
	{
		echo "\n<div id=\"tabgeneral\" class=\"tab\">\n";
			require_once PAGE_PATH.'/playerinfo_general.php';
			require_once PAGE_PATH.'/playerinfo_aliases.php';
		echo '</div>';
		echo "\n<div id=\"tabteams\" class=\"tab\">\n";
			require_once PAGE_PATH.'/playerinfo_playeractions.php';
			require_once PAGE_PATH.'/playerinfo_teams.php';
		echo '</div>';
		echo "\n<div id=\"tabweapons\" class=\"tab\">\n";
			require_once PAGE_PATH.'/playerinfo_weapons.php';
		echo '</div>';
		echo "\n<div id=\"tabmaps\" class=\"tab\">\n";
			require_once PAGE_PATH.'/playerinfo_mapperformance.php';
			require_once PAGE_PATH.'/playerinfo_servers.php';
		echo '</div>';
		echo "\n<div id=\"tabkills\" class=\"tab\">\n";
			require_once PAGE_PATH.'/playerinfo_killstats.php';
		echo '</div>';
	}
?>
</div>
<div class="block" style="clear:both;padding-top:12px;">
	<div class="subblock">
		<div style="float:left;">
			Items marked "*" above are generated from the last <?php echo $g_options['DeleteDays']; ?> days.
		</div>
		<div style="float:right;">
			<?php
				if (isset($_SESSION['loggedin']))
				{
					echo 'Admin Options: <a href="'.$g_options['scripturl']."?mode=admin&amp;task=tools_editdetails_player&amp;id=$player\">Edit Player Details</a><br />";
				}
			?>
			Go to: <a href="<?php echo $g_options['scripturl'] . "?mode=players&amp;game=$game"; ?>">Player Rankings</a>
		</div>
	</div>
</div>
