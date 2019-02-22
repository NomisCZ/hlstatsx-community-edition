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
	if ($auth->userdata['acclevel'] < 80)
	{
		die ('Access denied!');
	}
?>

&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo IMAGE_PATH; ?>/downarrow.gif" width="9" height="6" class="imageformat" alt="" /><strong>&nbsp;<?php echo $task->title; ?></strong><br /><br />

<?php

	if (isset($_POST['confirm']))
	{
		echo "<ul>\n";

		$gamefilter = '';
		if (isset($_POST['game']) && $_POST['game'] != '')
		{
			$gamefilter = " WHERE game='".$db->escape($_POST['game'])."'";
		}
		
		$clearAll = isset($_POST['clear_all']);
		$clearAllDelete = isset($_POST['clear_all_delete']);
		$clearAllEvents = isset($_POST['clear_all_events']);
		
		if (isset($_POST['clear_awards']) || $clearAll || $clearAllDelete)
		{
			echo "<li>Clearing awards ... ";
			$db->query("UPDATE hlstats_Awards SET d_winner_id=NULL, d_winner_count=NULL, g_winner_id=NULL, g_winner_count=NULL $gamefilter");
			if ($gamefilter == '')
			{
				
				$db->query("TRUNCATE TABLE `hlstats_Players_Awards`");
				$db->query("TRUNCATE TABLE `hlstats_Players_Ribbons`");
			}
			else
			{
				$db->query("DELETE FROM `hlstats_Players_Awards` $gamefilter");
				$db->query("DELETE FROM `hlstats_Players_Ribbons` $gamefilter");
			}
			echo "OK</li>\n";
		}
		if (isset($_POST['clear_sessions']) || $clearAll || $clearAllDelete)
		{
			echo "<li>Removing players' session history ... ";
			if ($gamefilter == '')
			{
				$SQL = "TRUNCATE TABLE `hlstats_Players_History`";
			}
			else
			{
				$SQL = "DELETE FROM `hlstats_Players_History` $gamefilter";
			}
			if ($db->query($SQL))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}	
		}
		if (isset($_POST['clear_names']) || $clearAll || $clearAllDelete)
		{
			echo "<li>Removing players' names history ... ";
			if ($gamefilter == '')
			{
				$SQL = "TRUNCATE TABLE `hlstats_PlayerNames`";
			}
			else
			{
				$SQL = "DELETE FROM `hlstats_PlayerNames` WHERE playerId IN (SELECT playerId FROM hlstats_Players $gamefilter)";
			}
			if ($db->query($SQL))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_names_counts']) || $clearAll || $clearAllDelete)
		{
			echo "<li>Resetting players' names' counts ... ";
			$SQL = "UPDATE `hlstats_PlayerNames` SET connection_time=0, numuses=0, kills=0, deaths=0, suicides=0, headshots=0, shots=0, hits=0 WHERE playerId IN (SELECT playerId FROM hlstats_Players $gamefilter)";
			if ($db->query($SQL))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_skill']) || $clearAll || $clearAllDelete)
		{
			echo "<li>Resetting all Players' Skill ... ";
			$SQL = "UPDATE hlstats_Players SET skill=1000 $gamefilter";
			if ($db->query($SQL))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_pcounts']) || $clearAll || $clearAllDelete)
		{
			echo "<li>Resetting all Players' Counts ... ";
			$SQL = "UPDATE hlstats_Players SET connection_time=0, kills=0, deaths=0, suicides=0, shots=0, hits=0, headshots=0, last_skill_change=0, kill_streak=0, death_streak=0 $gamefilter";
			if ($db->query($SQL))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_scounts']) || $clearAll || $clearAllDelete)
		{
			echo "<li>Resetting Servers' Counts ... ";
			$db->query("UPDATE hlstats_Servers SET kills=0, players=0, rounds=0, suicides=0, ".
						"headshots=0, bombs_planted=0, bombs_defused=0, ct_wins=0, ts_wins=0, ".
						"ct_shots=0, ct_hits=0, ts_shots=0, ts_hits=0, ".
						"map_ct_shots=0, map_ct_hits=0, map_ts_shots=0, map_ts_hits=0, ".
						"map_rounds=0, map_ct_wins=0, map_ts_wins=0, map_started=0, map_changes=0, ".
						"act_map='', act_players=0 $gamefilter");
			echo "OK</li>\n";
		}
		if (isset($_POST['clear_wcounts']) || $clearAll || $clearAllDelete)
		{
			echo "<li>Resetting Weapons' Counts ... ";
			$SQL = "UPDATE hlstats_Weapons SET kills=0, headshots=0 $gamefilter";
			if ($db->query($SQL))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_acounts']) || $clearAll || $clearAllDelete)
		{
			echo "<li>Resetting Actions' Counts ... ";
			$SQL = "UPDATE hlstats_Actions SET `count`=0 $gamefilter";
			if ($db->query($SQL))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_mcounts']) || $clearAll || $clearAllDelete)
		{
			echo "<li>Resetting Maps' Counts ... ";
			$SQL = "UPDATE hlstats_Maps_Counts SET `kills`=0, `headshots`=0 $gamefilter";
			if ($db->query($SQL))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_rcounts']) || $clearAll || $clearAllDelete)
		{
			echo "<li>Resetting Roles' Counts ... ";
			$SQL = "UPDATE hlstats_Roles SET picked=0, kills=0, deaths=0 $gamefilter";
			if ($db->query($SQL))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_events_admin']) || $clearAll || $clearAllDelete)
		{
			echo "<li>Deleting Admin Events ... ";
			if ($gamefilter == '')
			{
				$SQL = "TRUNCATE TABLE `hlstats_Events_Admin`";
			}
			else
			{
				$SQL = "DELETE FROM `hlstats_Events_Admin` USING `hlstats_Events_Admin` INNER JOIN hlstats_Servers ON (hlstats_Events_Admin.serverId=hlstats_Servers.serverId) $gamefilter";
			}
			if ($db->query($SQL))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_events_changename']) || $clearAll || $clearAllDelete)
		{
			echo "<li>Deleting Name Change Events ... ";
			if ($gamefilter == '')
			{
				$SQL = "TRUNCATE TABLE `hlstats_Events_ChangeName`";
			}
			else
			{
				$SQL = "DELETE FROM `hlstats_Events_ChangeName` USING `hlstats_Events_ChangeName` INNER JOIN hlstats_Servers ON (hlstats_Events_ChangeName.serverId=hlstats_Servers.serverId) $gamefilter";
			}
			if ($db->query($SQL))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_events_changerole']) || $clearAll || $clearAllDelete || $clearAllEvents)
		{
			echo "<li>Deleting Role Change Events ... ";
			if ($gamefilter == '')
			{
				$SQL = "TRUNCATE TABLE `hlstats_Events_ChangeRole`";
			}
			else
			{
				$SQL = "DELETE FROM `hlstats_Events_ChangeRole` USING `hlstats_Events_ChangeRole` INNER JOIN hlstats_Servers ON (hlstats_Events_ChangeRole.serverId=hlstats_Servers.serverId) $gamefilter";
			}
			if ($db->query($SQL))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_events_changeteam']) || $clearAll || $clearAllDelete || $clearAllEvents)
		{
			echo "<li>Deleting Team Change Events ... ";
			if ($gamefilter == '')
			{
				$SQL = "TRUNCATE TABLE `hlstats_Events_ChangeTeam`";
			}
			else
			{
				$SQL = "DELETE FROM `hlstats_Events_ChangeTeam` USING `hlstats_Events_ChangeTeam` INNER JOIN hlstats_Servers ON (hlstats_Events_ChangeTeam.serverId=hlstats_Servers.serverId) $gamefilter";
			}
			if ($db->query($SQL))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_events_chat']) || $clearAll || $clearAllDelete || $clearAllEvents)
		{
			echo "<li>Deleting Chat Events ... ";
			if ($gamefilter == '')
			{
				$SQL = "TRUNCATE TABLE `hlstats_Events_Chat`";
			}
			else
			{
				$SQL = "DELETE FROM `hlstats_Events_Chat` USING `hlstats_Events_Chat` INNER JOIN hlstats_Servers ON (hlstats_Events_Chat.serverId=hlstats_Servers.serverId) $gamefilter";
			}
			if ($db->query($SQL))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_events_connects']) || $clearAll || $clearAllDelete || $clearAllEvents)
		{
			echo "<li>Deleting Connect Events ... ";
			if ($gamefilter == '')
			{
				$SQL = "TRUNCATE TABLE `hlstats_Events_Connects`";
			}
			else
			{
				$SQL = "DELETE FROM `hlstats_Events_Connects` USING `hlstats_Events_Connects` INNER JOIN hlstats_Servers ON (hlstats_Events_Connects.serverId=hlstats_Servers.serverId) $gamefilter";
			}
			if ($db->query($SQL))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_events_disconnects']) || $clearAll || $clearAllDelete || $clearAllEvents)
		{
			echo "<li>Deleting Disconnect Events ... ";
			if ($gamefilter == '')
			{
				$SQL = "TRUNCATE TABLE `hlstats_Events_Disconnects`";
			}
			else
			{
				$SQL = "DELETE FROM `hlstats_Events_Disconnects` USING `hlstats_Events_Disconnects` INNER JOIN hlstats_Servers ON (hlstats_Events_Disconnects.serverId=hlstats_Servers.serverId) $gamefilter";
			}
			if ($db->query($SQL))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_events_entries']) || $clearAll || $clearAllDelete || $clearAllEvents)
		{
			echo "<li>Deleting Entry Events ... ";
			if ($gamefilter == '')
			{
				$SQL = "TRUNCATE TABLE `hlstats_Events_Entries`";
			}
			else
			{
				$SQL = "DELETE FROM `hlstats_Events_Entries` USING `hlstats_Events_Entries` INNER JOIN hlstats_Servers ON (hlstats_Events_Entries.serverId=hlstats_Servers.serverId) $gamefilter";
			}
			if ($db->query($SQL))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_events_frags']) || $clearAll || $clearAllDelete || $clearAllEvents)
		{
			echo "<li>Deleting Frag Events ... ";
			if ($gamefilter == '')
			{
				$SQL = "TRUNCATE TABLE `hlstats_Events_Frags`";
			}
			else
			{
				$SQL = "DELETE FROM `hlstats_Events_Frags` USING `hlstats_Events_Frags` INNER JOIN hlstats_Servers ON (hlstats_Events_Frags.serverId=hlstats_Servers.serverId) $gamefilter";
			}
			if ($db->query($SQL))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_events_latency']) || $clearAll || $clearAllDelete || $clearAllEvents)
		{
			echo "<li>Deleting Latency Events ... ";
			if ($gamefilter == '')
			{
				$SQL = "TRUNCATE TABLE `hlstats_Events_Latency`";
				$SQL2 = "TRUNCATE TABLE `hlstats_Events_StatsmeLatency`";
			}
			else
			{
				$SQL = "DELETE FROM `hlstats_Events_Latency` USING `hlstats_Events_Latency` INNER JOIN hlstats_Servers ON (hlstats_Events_Latency.serverId=hlstats_Servers.serverId) $gamefilter";
				$SQL2 = "DELETE FROM `hlstats_Events_StatsmeLatency` USING `hlstats_Events_StatsmeLatency` INNER JOIN hlstats_Servers ON (hlstats_Events_StatsmeLatency.serverId=hlstats_Servers.serverId) $gamefilter";
			}
			if ($db->query($SQL) && $db->query($SQL2))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_events_actions']) || $clearAll || $clearAllDelete || $clearAllEvents)
		{
			echo "<li>Deleting Action Events ... ";
			if ($gamefilter == '')
			{
				$SQL = "TRUNCATE TABLE `hlstats_Events_PlayerActions`";
				$SQL2 = "TRUNCATE TABLE `hlstats_Events_PlayerPlayerActions`";
				$SQL3 = "TRUNCATE TABLE `hlstats_Events_TeamBonuses`";
			}
			else
			{
				$SQL = "DELETE FROM `hlstats_Events_PlayerActions` USING `hlstats_Events_PlayerActions` INNER JOIN hlstats_Servers ON (hlstats_Events_PlayerActions.serverId=hlstats_Servers.serverId) $gamefilter";
				$SQL2 = "DELETE FROM `hlstats_Events_PlayerPlayerActions` USING `hlstats_Events_PlayerPlayerActions` INNER JOIN hlstats_Servers ON (hlstats_Events_PlayerPlayerActions.serverId=hlstats_Servers.serverId) $gamefilter";
				$SQL3 = "DELETE FROM `hlstats_Events_TeamBonuses` USING `hlstats_Events_TeamBonuses` INNER JOIN hlstats_Servers ON (hlstats_Events_TeamBonuses.serverId=hlstats_Servers.serverId) $gamefilter";
			}
			if ($db->query($SQL) && $db->query($SQL2) && $db->query($SQL3))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_events_rcon']) || $clearAll || $clearAllDelete || $clearAllEvents)
		{
			echo "<li>Deleting Rcon Events ... ";
			if ($gamefilter == '')
			{
				$SQL = "TRUNCATE TABLE `hlstats_Events_Rcon`";
			}
			else
			{
				$SQL = "DELETE FROM `hlstats_Events_Rcon` USING `hlstats_Events_Rcon` INNER JOIN hlstats_Servers ON (hlstats_Events_Rcon.serverId=hlstats_Servers.serverId) $gamefilter";
			}
			if ($db->query($SQL))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_events_statsme']) || $clearAll || $clearAllDelete || $clearAllEvents)
		{
			echo "<li>Deleting Weapon Stats Events ... ";
			if ($gamefilter == '')
			{
				$SQL = "TRUNCATE TABLE `hlstats_Events_Statsme`";
				$SQL2 = "TRUNCATE TABLE `hlstats_Events_Statsme2`";
			}
			else
			{
				$SQL = "DELETE FROM `hlstats_Events_Statsme` USING `hlstats_Events_Statsme` INNER JOIN hlstats_Servers ON (hlstats_Events_Statsme.serverId=hlstats_Servers.serverId) $gamefilter";
				$SQL2 = "DELETE FROM `hlstats_Events_Statsme2` USING `hlstats_Events_Statsme2` INNER JOIN hlstats_Servers ON (hlstats_Events_Statsme2.serverId=hlstats_Servers.serverId) $gamefilter";
			}
			if ($db->query($SQL) && $db->query($SQL2))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_events_statsmetime']) || $clearAll || $clearAllDelete || $clearAllEvents)
		{
			echo "<li>Deleting Statsme Time Events ... ";
			if ($gamefilter == '')
			{
				$SQL = "TRUNCATE TABLE `hlstats_Events_StatsmeTime`";
			}
			else
			{
				$SQL = "DELETE FROM `hlstats_Events_StatsmeTime` USING `hlstats_Events_StatsmeTime` INNER JOIN hlstats_Servers ON (hlstats_Events_StatsmeTime.serverId=hlstats_Servers.serverId) $gamefilter";
			}
			if ($db->query($SQL))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_events_suicides']) || $clearAll || $clearAllDelete || $clearAllEvents)
		{
			echo "<li>Deleting Suicide Events ... ";
			if ($gamefilter == '')
			{
				$SQL = "TRUNCATE TABLE `hlstats_Events_Suicides`";
			}
			else
			{
				$SQL = "DELETE FROM `hlstats_Events_Suicides` USING `hlstats_Events_Suicides` INNER JOIN hlstats_Servers ON (hlstats_Events_Suicides.serverId=hlstats_Servers.serverId) $gamefilter";
			}
			if ($db->query($SQL))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if (isset($_POST['clear_events_teamkills']) || $clearAll || $clearAllDelete || $clearAllEvents)
		{
			echo "<li>Deleting Teamkill Events ... ";
			if ($gamefilter == '')
			{
				$SQL = "TRUNCATE TABLE `hlstats_Events_Teamkills`";
			}
			else
			{
				$SQL = "DELETE FROM `hlstats_Events_Teamkills` USING `hlstats_Events_Teamkills` INNER JOIN hlstats_Servers ON (hlstats_Events_Teamkills.serverId=hlstats_Servers.serverId) $gamefilter";
			}
			if ($db->query($SQL))
			{
				echo "OK</li>\n";
			}
			else
			{
				echo "ERROR</li>\n";
			}
		}
		if ($clearAllDelete)
		{
			$dbtables = array(
				'hlstats_Clans',
				'hlstats_PlayerUniqueIds',
				'hlstats_Players'
			);
			
			foreach ($dbtables as $dbt)
			{
				echo "<li>Clearing $dbt ... ";
				if ($gamefilter == '')
				{
					$db->query("TRUNCATE TABLE $dbt");
				}
				else
				{
					$db->query("DELETE FROM $dbt $gamefilter");
				}
				echo "OK</li>\n";
			}
		}

		echo "</ul>\n";		
		echo "Done.<br /><br />";
	}
	else
	{
		$result = $db->query("SELECT code, name, hidden FROM `hlstats_Games` ORDER BY hidden, name, code;");
		unset($games);
		$games[] = '<option value="" selected="selected" />All games';
		while (list($code, $name, $hidden) = $db->fetch_row($result))
		{
			$disabled_flag = "";
			if ($hidden == 1) {
				$disabled_flag = "* ";
			}
			$games[] = "<option value=\"$code\" />$disabled_flag$name - $code\n";
		}
		
?>
<script type="text/javascript">
function clear_all_delete_checked()
{
	if (document.resetform.clear_all_delete.checked) {
		document.resetform.clear_all.disabled = true;
		document.resetform.clear_all_events.disabled = true;
		document.resetform.clear_awards.disabled = true;
		document.resetform.clear_sessions.disabled = true;
		document.resetform.clear_names.disabled = true;
		document.resetform.clear_names_counts.disabled = true;
		document.resetform.clear_skill.disabled = true;
		document.resetform.clear_pcounts.disabled = true;
		document.resetform.clear_scounts.disabled = true;
		document.resetform.clear_wcounts.disabled = true;
		document.resetform.clear_acounts.disabled = true;
		document.resetform.clear_mcounts.disabled = true;
		document.resetform.clear_rcounts.disabled = true;
		document.resetform.clear_events_admin.disabled = true;
		document.resetform.clear_events_rcon.disabled = true;
		document.resetform.clear_events_connects.disabled = true;
		document.resetform.clear_events_disconnects.disabled = true;
		document.resetform.clear_events_entries.disabled = true;
		document.resetform.clear_events_chat.disabled = true;
		document.resetform.clear_events_changename.disabled = true;
		document.resetform.clear_events_changerole.disabled = true;
		document.resetform.clear_events_changeteam.disabled = true;
		document.resetform.clear_events_frags.disabled = true;
		document.resetform.clear_events_suicides.disabled = true;
		document.resetform.clear_events_teamkills.disabled = true;
		document.resetform.clear_events_statsme.disabled = true;
		document.resetform.clear_events_actions.disabled = true;
		document.resetform.clear_events_latency.disabled = true;
		document.resetform.clear_events_statsmetime.disabled = true;
		document.resetform.clear_all.checked = true;
		document.resetform.clear_all_events.checked = true;
		document.resetform.clear_awards.checked = true;
		document.resetform.clear_sessions.checked = true;
		document.resetform.clear_names.checked = true;
		document.resetform.clear_names_counts.checked = true;
		document.resetform.clear_skill.checked = true;
		document.resetform.clear_pcounts.checked = true;
		document.resetform.clear_scounts.checked = true;
		document.resetform.clear_wcounts.checked = true;
		document.resetform.clear_acounts.checked = true;
		document.resetform.clear_mcounts.checked = true;
		document.resetform.clear_rcounts.checked = true;
		document.resetform.clear_events_admin.checked = true;
		document.resetform.clear_events_rcon.checked = true;
		document.resetform.clear_events_connects.checked = true;
		document.resetform.clear_events_disconnects.checked = true;
		document.resetform.clear_events_entries.checked = true;
		document.resetform.clear_events_chat.checked = true;
		document.resetform.clear_events_changename.checked = true;
		document.resetform.clear_events_changerole.checked = true;
		document.resetform.clear_events_changeteam.checked = true;
		document.resetform.clear_events_frags.checked = true;
		document.resetform.clear_events_suicides.checked = true;
		document.resetform.clear_events_teamkills.checked = true;
		document.resetform.clear_events_statsme.checked = true;
		document.resetform.clear_events_actions.checked = true;
		document.resetform.clear_events_latency.checked = true;
		document.resetform.clear_events_statsmetime.checked = true;
	}
	else
	{
		document.resetform.clear_all.disabled = false;
		document.resetform.clear_all_events.disabled = false;
		document.resetform.clear_awards.disabled = false;
		document.resetform.clear_sessions.disabled = false;
		document.resetform.clear_names.disabled = false;
		document.resetform.clear_names_counts.disabled = false;
		document.resetform.clear_skill.disabled = false;
		document.resetform.clear_pcounts.disabled = false;
		document.resetform.clear_scounts.disabled = false;
		document.resetform.clear_wcounts.disabled = false;
		document.resetform.clear_acounts.disabled = false;
		document.resetform.clear_mcounts.disabled = false;
		document.resetform.clear_rcounts.disabled = false;
		document.resetform.clear_events_admin.disabled = false;
		document.resetform.clear_events_rcon.disabled = false;
		document.resetform.clear_events_connects.disabled = false;
		document.resetform.clear_events_disconnects.disabled = false;
		document.resetform.clear_events_entries.disabled = false;
		document.resetform.clear_events_chat.disabled = false;
		document.resetform.clear_events_changename.disabled = false;
		document.resetform.clear_events_changerole.disabled = false;
		document.resetform.clear_events_changeteam.disabled = false;
		document.resetform.clear_events_frags.disabled = false;
		document.resetform.clear_events_suicides.disabled = false;
		document.resetform.clear_events_teamkills.disabled = false;
		document.resetform.clear_events_statsme.disabled = false;
		document.resetform.clear_events_actions.disabled = false;
		document.resetform.clear_events_latency.disabled = false;
		document.resetform.clear_events_statsmetime.disabled = false;
		document.resetform.clear_all.checked = false;
		document.resetform.clear_all_events.checked = false;
		document.resetform.clear_awards.checked = false;
		document.resetform.clear_sessions.checked = false;
		document.resetform.clear_names.checked = false;
		document.resetform.clear_names_counts.checked = false;
		document.resetform.clear_skill.checked = false;
		document.resetform.clear_pcounts.checked = false;
		document.resetform.clear_scounts.checked = false;
		document.resetform.clear_wcounts.checked = false;
		document.resetform.clear_acounts.checked = false;
		document.resetform.clear_mcounts.checked = false;
		document.resetform.clear_rcounts.checked = false;
		document.resetform.clear_events_admin.checked = false;
		document.resetform.clear_events_rcon.checked = false;
		document.resetform.clear_events_connects.checked = false;
		document.resetform.clear_events_disconnects.checked = false;
		document.resetform.clear_events_entries.checked = false;
		document.resetform.clear_events_chat.checked = false;
		document.resetform.clear_events_changename.checked = false;
		document.resetform.clear_events_changerole.checked = false;
		document.resetform.clear_events_changeteam.checked = false;
		document.resetform.clear_events_frags.checked = false;
		document.resetform.clear_events_suicides.checked = false;
		document.resetform.clear_events_teamkills.checked = false;
		document.resetform.clear_events_statsme.checked = false;
		document.resetform.clear_events_actions.checked = false;
		document.resetform.clear_events_latency.checked = false;
		document.resetform.clear_events_statsmetime.checked = false;
	}
}

function clear_all_checked()
{
	if (document.resetform.clear_all.checked) {
		document.resetform.clear_all_events.disabled = true;
		document.resetform.clear_awards.disabled = true;
		document.resetform.clear_sessions.disabled = true;
		document.resetform.clear_names.disabled = true;
		document.resetform.clear_names_counts.disabled = true;
		document.resetform.clear_skill.disabled = true;
		document.resetform.clear_pcounts.disabled = true;
		document.resetform.clear_scounts.disabled = true;
		document.resetform.clear_wcounts.disabled = true;
		document.resetform.clear_acounts.disabled = true;
		document.resetform.clear_mcounts.disabled = true;
		document.resetform.clear_rcounts.disabled = true;
		document.resetform.clear_events_admin.disabled = true;
		document.resetform.clear_events_rcon.disabled = true;
		document.resetform.clear_events_connects.disabled = true;
		document.resetform.clear_events_disconnects.disabled = true;
		document.resetform.clear_events_entries.disabled = true;
		document.resetform.clear_events_chat.disabled = true;
		document.resetform.clear_events_changename.disabled = true;
		document.resetform.clear_events_changerole.disabled = true;
		document.resetform.clear_events_changeteam.disabled = true;
		document.resetform.clear_events_frags.disabled = true;
		document.resetform.clear_events_suicides.disabled = true;
		document.resetform.clear_events_teamkills.disabled = true;
		document.resetform.clear_events_statsme.disabled = true;
		document.resetform.clear_events_actions.disabled = true;
		document.resetform.clear_events_latency.disabled = true;
		document.resetform.clear_events_statsmetime.disabled = true;
		document.resetform.clear_all_events.checked = true;
		document.resetform.clear_awards.checked = true;
		document.resetform.clear_sessions.checked = true;
		document.resetform.clear_names.checked = true;
		document.resetform.clear_names_counts.checked = true;
		document.resetform.clear_skill.checked = true;
		document.resetform.clear_pcounts.checked = true;
		document.resetform.clear_scounts.checked = true;
		document.resetform.clear_wcounts.checked = true;
		document.resetform.clear_acounts.checked = true;
		document.resetform.clear_mcounts.checked = true;
		document.resetform.clear_rcounts.checked = true;
		document.resetform.clear_events_admin.checked = true;
		document.resetform.clear_events_rcon.checked = true;
		document.resetform.clear_events_connects.checked = true;
		document.resetform.clear_events_disconnects.checked = true;
		document.resetform.clear_events_entries.checked = true;
		document.resetform.clear_events_chat.checked = true;
		document.resetform.clear_events_changename.checked = true;
		document.resetform.clear_events_changerole.checked = true;
		document.resetform.clear_events_changeteam.checked = true;
		document.resetform.clear_events_frags.checked = true;
		document.resetform.clear_events_suicides.checked = true;
		document.resetform.clear_events_teamkills.checked = true;
		document.resetform.clear_events_statsme.checked = true;
		document.resetform.clear_events_actions.checked = true;
		document.resetform.clear_events_latency.checked = true;
		document.resetform.clear_events_statsmetime.checked = true;
	}
	else
	{
		document.resetform.clear_all_events.disabled = false;
		document.resetform.clear_awards.disabled = false;
		document.resetform.clear_sessions.disabled = false;
		document.resetform.clear_names.disabled = false;
		document.resetform.clear_names_counts.disabled = false;
		document.resetform.clear_skill.disabled = false;
		document.resetform.clear_pcounts.disabled = false;
		document.resetform.clear_scounts.disabled = false;
		document.resetform.clear_wcounts.disabled = false;
		document.resetform.clear_acounts.disabled = false;
		document.resetform.clear_mcounts.disabled = false;
		document.resetform.clear_rcounts.disabled = false;
		document.resetform.clear_events_admin.disabled = false;
		document.resetform.clear_events_rcon.disabled = false;
		document.resetform.clear_events_connects.disabled = false;
		document.resetform.clear_events_disconnects.disabled = false;
		document.resetform.clear_events_entries.disabled = false;
		document.resetform.clear_events_chat.disabled = false;
		document.resetform.clear_events_changename.disabled = false;
		document.resetform.clear_events_changerole.disabled = false;
		document.resetform.clear_events_changeteam.disabled = false;
		document.resetform.clear_events_frags.disabled = false;
		document.resetform.clear_events_suicides.disabled = false;
		document.resetform.clear_events_teamkills.disabled = false;
		document.resetform.clear_events_statsme.disabled = false;
		document.resetform.clear_events_actions.disabled = false;
		document.resetform.clear_events_latency.disabled = false;
		document.resetform.clear_events_statsmetime.disabled = false;
		document.resetform.clear_all_events.checked = false;
		document.resetform.clear_awards.checked = false;
		document.resetform.clear_sessions.checked = false;
		document.resetform.clear_names.checked = false;
		document.resetform.clear_names_counts.checked = false;
		document.resetform.clear_skill.checked = false;
		document.resetform.clear_pcounts.checked = false;
		document.resetform.clear_scounts.checked = false;
		document.resetform.clear_wcounts.checked = false;
		document.resetform.clear_acounts.checked = false;
		document.resetform.clear_mcounts.checked = false;
		document.resetform.clear_rcounts.checked = false;
		document.resetform.clear_events_admin.checked = false;
		document.resetform.clear_events_rcon.checked = false;
		document.resetform.clear_events_connects.checked = false;
		document.resetform.clear_events_disconnects.checked = false;
		document.resetform.clear_events_entries.checked = false;
		document.resetform.clear_events_chat.checked = false;
		document.resetform.clear_events_changename.checked = false;
		document.resetform.clear_events_changerole.checked = false;
		document.resetform.clear_events_changeteam.checked = false;
		document.resetform.clear_events_frags.checked = false;
		document.resetform.clear_events_suicides.checked = false;
		document.resetform.clear_events_teamkills.checked = false;
		document.resetform.clear_events_statsme.checked = false;
		document.resetform.clear_events_actions.checked = false;
		document.resetform.clear_events_latency.checked = false;
		document.resetform.clear_events_statsmetime.checked = false;
	}
}

function clear_all_events_checked()
{
	if (document.resetform.clear_all_events.checked) {
		document.resetform.clear_events_admin.disabled = true;
		document.resetform.clear_events_rcon.disabled = true;
		document.resetform.clear_events_connects.disabled = true;
		document.resetform.clear_events_disconnects.disabled = true;
		document.resetform.clear_events_entries.disabled = true;
		document.resetform.clear_events_chat.disabled = true;
		document.resetform.clear_events_changename.disabled = true;
		document.resetform.clear_events_changerole.disabled = true;
		document.resetform.clear_events_changeteam.disabled = true;
		document.resetform.clear_events_frags.disabled = true;
		document.resetform.clear_events_suicides.disabled = true;
		document.resetform.clear_events_teamkills.disabled = true;
		document.resetform.clear_events_statsme.disabled = true;
		document.resetform.clear_events_actions.disabled = true;
		document.resetform.clear_events_latency.disabled = true;
		document.resetform.clear_events_statsmetime.disabled = true;
		document.resetform.clear_events_admin.checked = true;
		document.resetform.clear_events_rcon.checked = true;
		document.resetform.clear_events_connects.checked = true;
		document.resetform.clear_events_disconnects.checked = true;
		document.resetform.clear_events_entries.checked = true;
		document.resetform.clear_events_chat.checked = true;
		document.resetform.clear_events_changename.checked = true;
		document.resetform.clear_events_changerole.checked = true;
		document.resetform.clear_events_changeteam.checked = true;
		document.resetform.clear_events_frags.checked = true;
		document.resetform.clear_events_suicides.checked = true;
		document.resetform.clear_events_teamkills.checked = true;
		document.resetform.clear_events_statsme.checked = true;
		document.resetform.clear_events_actions.checked = true;
		document.resetform.clear_events_latency.checked = true;
		document.resetform.clear_events_statsmetime.checked = true;
	}
	else
	{
		document.resetform.clear_events_admin.disabled = false;
		document.resetform.clear_events_rcon.disabled = false;
		document.resetform.clear_events_connects.disabled = false;
		document.resetform.clear_events_disconnects.disabled = false;
		document.resetform.clear_events_entries.disabled = false;
		document.resetform.clear_events_chat.disabled = false;
		document.resetform.clear_events_changename.disabled = false;
		document.resetform.clear_events_changerole.disabled = false;
		document.resetform.clear_events_changeteam.disabled = false;
		document.resetform.clear_events_frags.disabled = false;
		document.resetform.clear_events_suicides.disabled = false;
		document.resetform.clear_events_teamkills.disabled = false;
		document.resetform.clear_events_statsme.disabled = false;
		document.resetform.clear_events_actions.disabled = false;
		document.resetform.clear_events_latency.disabled = false;
		document.resetform.clear_events_statsmetime.disabled = false;
		document.resetform.clear_events_admin.checked = false;
		document.resetform.clear_events_rcon.checked = false;
		document.resetform.clear_events_connects.checked = false;
		document.resetform.clear_events_disconnects.checked = false;
		document.resetform.clear_events_entries.checked = false;
		document.resetform.clear_events_chat.checked = false;
		document.resetform.clear_events_changename.checked = false;
		document.resetform.clear_events_changerole.checked = false;
		document.resetform.clear_events_changeteam.checked = false;
		document.resetform.clear_events_frags.checked = false;
		document.resetform.clear_events_suicides.checked = false;
		document.resetform.clear_events_teamkills.checked = false;
		document.resetform.clear_events_statsme.checked = false;
		document.resetform.clear_events_actions.checked = false;
		document.resetform.clear_events_latency.checked = false;
		document.resetform.clear_events_statsmetime.checked = false;
	}
}

function name_history_checked()
{
	if (document.resetform.clear_names.checked) {
		document.resetform.clear_names_counts.disabled = true;
		document.resetform.clear_names_counts.checked = true;
	}
	else
	{
		document.resetform.clear_names_counts.disabled = false;
		document.resetform.clear_names_counts.checked = false;
	}
}

</script>
<form name="resetform" method="post">
<table width="600" align="center" border="0" cellspacing="0" cellpadding="0" class="border">

<tr>
	<td>
		<table width="100%" border="0" cellspacing="1" cellpadding="10">

<tr class="bg1">
	<td class="fNormal" align="middle">
<select name="game">
<?php foreach ($games as $g) echo $g; ?>
</select><br />
<em>* indicates game is currently disabled</em>
<table width="350" align="middle" border="0"><tr class="bg1"><td class="fNormal" align="left">
<ul style="list-style-type:none;">
	<li style="font-weight:bold;"><input type="checkbox" name="clear_all_delete" onclick="clear_all_delete_checked()" /> Reset/Clear All and Delete Players and Clans</li><br />
	<li style="font-weight:bold;"><input type="checkbox" name="clear_all" onclick="clear_all_checked()" /> Reset/Clear All</li>
	<li><input type="checkbox" name="clear_awards" /> Clear Players' Awards History and Ribbons</li>
	<li><input type="checkbox" name="clear_sessions" /> Clear Players' Session History</li>
	<li><input type="checkbox" name="clear_names" onclick="name_history_checked()" /> Clear Players' Name History</li>
	<li><input type="checkbox" name="clear_names_counts" /> Reset Players' Names' Counts</li>
	<li><input type="checkbox" name="clear_skill" /> Reset Players' Skill</li>
	<li><input type="checkbox" name="clear_pcounts" /> Reset Players' Counts</li>
	<li><input type="checkbox" name="clear_scounts" /> Reset Servers' Counts</li>
	<li><input type="checkbox" name="clear_wcounts" /> Reset Weapons' Counts</li>
	<li><input type="checkbox" name="clear_acounts" /> Reset Actions' Counts</li>
	<li><input type="checkbox" name="clear_mcounts" /> Reset Maps' Counts</li>
	<li><input type="checkbox" name="clear_rcounts" /> Reset Roles' Counts</li>
	<br />
	<li style="font-weight:bold;"><input type="checkbox" name="clear_all_events" onclick="clear_all_events_checked()" /> Delete All Events</li>
	<li><input type="checkbox" name="clear_events_admin" /> Delete Admin Events</li>
	<li><input type="checkbox" name="clear_events_rcon" /> Delete Rcon Events</li>
	<li><input type="checkbox" name="clear_events_connects" /> Delete Connect Events</li>
	<li><input type="checkbox" name="clear_events_disconnects" /> Delete Disconnect Events</li>
	<li><input type="checkbox" name="clear_events_entries" /> Delete Entry Events</li>
	<li><input type="checkbox" name="clear_events_chat" /> Delete Chat Events</li>
	<li><input type="checkbox" name="clear_events_changename" /> Delete Name Change Events</li>
	<li><input type="checkbox" name="clear_events_changerole" /> Delete Role Change Events</li>
	<li><input type="checkbox" name="clear_events_changeteam" /> Delete Team Change Events</li>
	<li><input type="checkbox" name="clear_events_frags" /> Delete Frags Events</li>
	<li><input type="checkbox" name="clear_events_suicides" /> Delete Suicide Events</li>
	<li><input type="checkbox" name="clear_events_teamkills" /> Delete Teamkill Events</li>
	<li><input type="checkbox" name="clear_events_statsme" /> Delete Weapon Stats Events</li>
	<li><input type="checkbox" name="clear_events_actions" /> Delete Action Events</li>
	<li><input type="checkbox" name="clear_events_latency" /> Delete Latency Events</li>
	<li><input type="checkbox" name="clear_events_statsmetime" /> Delete Statsme Time Events</li>
</ul>
<br />
</td></tr></table>
<p align="middle" />
		Are you sure you want to reset the above? (All other admin settings will be retained.)<br /><br />

<strong>Note</strong> You should <a href="<?php echo $g_options['scripturl'] . "?mode=admin&amp;task=tools_perlcontrol"; ?>" style="text-decoration:underline;font-weight:bold">stop the HLX:CE daemon</a> before resetting the stats. You can restart it after the reset completes.<br /><br />

<input type="hidden" name="confirm" value="1" />

 <input type="submit" value="  Click here to confirm Reset  " />
</td>
		</tr>
		
		</table></td>
</tr>

</table>
</form>
<?php
	}
?>
