<?php
/*
 HLstatsX Community Edition - Real-time player and clan rankings and statistics
 Copyleft (L) 2008-20XX Nicholas Hastings (nshastings@gmail.com)
 http://www.hlxcommunity.com

 HLstatsX Community Edition is a continuation of 
 ELstatsNEO - Real-time player and clan rankings and statistics
 http://ovrsized.neo-soft.org/
 Copyleft (L) 2008-20XX Malte Bayer (steam@neo-soft.org)
 
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
 
 For support and installation notes visit http://ovrsized.neo-soft.org!
*/

	if ( !defined('IN_HLSTATS') )
	{
		die('Do not access this file directly.');
	}
	
	// Clan Details
	
	$clan = valid_request(intval($_GET["clan"]), 1)
		or error("No clan ID specified.");

	$db->query("
		SELECT
			hlstats_Clans.tag,
			hlstats_Clans.name,
			hlstats_Clans.homepage,
			hlstats_Clans.game,
			hlstats_Clans.mapregion,
			SUM(hlstats_Players.kills) AS kills,
			SUM(hlstats_Players.deaths) AS deaths,
			SUM(hlstats_Players.headshots) AS headshots,
			SUM(hlstats_Players.connection_time) AS connection_time,
			COUNT(hlstats_Players.playerId) AS nummembers,
			ROUND(AVG(hlstats_Players.skill)) AS avgskill,
			TRUNCATE(AVG(activity),2) as activity
		FROM
			hlstats_Clans
		LEFT JOIN
			hlstats_Players
		ON
			hlstats_Players.clan = hlstats_Clans.clanId
		WHERE
			hlstats_Clans.clanId=$clan
			AND hlstats_Players.hideranking = 0
		GROUP BY
			hlstats_Clans.clanId
	");
	if ($db->num_rows() != 1)
	{
		error("No such clan '$clan'.");
	}
	
	$clandata = $db->fetch_array();

	$realkills = ($clandata['kills'] == 0) ? 1 : $clandata['kills'];
	$realheadshots = ($clandata['headshots'] == 0) ? 1 : $clandata['headshots'];


	$db->query("
		SELECT
			count(playerId)
		FROM
			hlstats_Players
		WHERE
			clan=$clan
		GROUP BY
			clan
    ");	
	list($totalclanplayers) = $db->fetch_array();

	$db->free_result();
	
	$cl_name = preg_replace('/\s/', '&nbsp;', htmlspecialchars($clandata['name']));
	$cl_tag  = preg_replace('/\s/', '&nbsp;', htmlspecialchars($clandata['tag']));
	$cl_full = "$cl_tag $cl_name";
	
	$game = $clandata['game'];
	$db->query("SELECT name FROM hlstats_Games WHERE code='$game'");
	if ($db->num_rows() != 1)
	{
		$gamename = ucfirst($game);
	}
	else
	{
		list($gamename) = $db->fetch_row();
	}	
	
	if ( $_GET['type'] == 'ajax' )
	{
		unset($_GET['type']);
		$tabs = explode('|', preg_replace('[^a-z]', '', $_GET['tab']));
		
		foreach ( $tabs as $tab )
		{
			if ( file_exists(PAGE_PATH . '/claninfo_' . $tab . '.php') )
			{
				@include(PAGE_PATH . '/claninfo_' . $tab . '.php');
			}
		}
		exit;
	}
	pageHeader(
		array($gamename, 'Clan Details', $cl_full),
		array(
			$gamename=>$g_options['scripturl'] . "?game=$game",
			'Clan Rankings'=>$g_options['scripturl'] . "?mode=clans&game=$game",
			'Clan Details'=>''
		),
		$clandata['name']
	);

	if ( $g_options['show_google_map'] == 1 ) {
		echo ('<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>');
	}
?>

<div class="block" id="main">

<?php
	// insert details pages here
	if ($g_options['playerinfo_tabs'] == '1')
	{
?>
	<ul class="subsection_tabs" id="tabs_claninfo">
		<li>
			<a href="#" id="tab_general" id="general">General</a>
		</li>
		<li>
			<a href="#" id="tab_actions|teams">Teams &amp; Actions</a>
		</li>
		<li>
			<a href="#" id="tab_weapons">Weapons</a>
		</li>
		<li>
			<a href="#" id="tab_mapperformance">Maps</a>
		</li>
	</ul><br />
	<div id="main_content"></div>
	<script type="text/javascript">
	var Tabs = new Tabs($('main_content'), $$('#main ul.subsection_tabs a'), {
		'mode': 'claninfo',
		'game': '<?php echo $game; ?>',
		'loadingImage': '<?php echo IMAGE_PATH; ?>/ajax.gif',
		'defaultTab': 'general',
		'extra': {'clan': '<?php echo $clan; ?>','members_page': '<?php echo valid_request($_GET['members_page'], true); ?>'}
	});
	</script>
<?php
	} else {
		echo "\n<div id=\"tabgeneral\">\n";
		require_once PAGE_PATH.'/claninfo_general.php';
		echo '</div>';
	
		echo "\n<div id=\"tabteams\">\n";
		require_once PAGE_PATH.'/claninfo_actions.php';
		require_once PAGE_PATH.'/claninfo_teams.php';
		echo '</div>';

		echo "\n<div id=\"tabweapons\">\n";
		require_once PAGE_PATH.'/claninfo_weapons.php';
		echo '</div>';
 
		echo "\n<div id=\"tabmaps\">\n";
		require_once PAGE_PATH.'/claninfo_mapperformance.php';
		echo '</div>';
	}
?>

<div class="block" style="clear:both;padding-top:12px;">
	<div class="subblock">
		<div style="float:left;">
			Items marked "*" above are generated from the last <?php echo $g_options['DeleteDays']; ?> days.
		</div>
		<div style="float:right;">
			<?php
				if (isset($_SESSION['loggedin']))
				{
					echo 'Admin Options: <a href="'.$g_options['scripturl']."?mode=admin&amp;task=tools_editdetails_clan&amp;id=$clan\">Edit Clan Details</a><br />";
				}
			?>
			Go to: <a href="<?php echo $g_options['scripturl'] . "?mode=players&amp;game=$game"; ?>">Clan Rankings</a>
		</div>
	</div>
</div>
