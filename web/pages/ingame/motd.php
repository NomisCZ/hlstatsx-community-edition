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

 motd.inc - showing ingame a statistic summary on connect
 ------------------------------------------------------------------------------------
 Created by Gregor Haenel (webmaster@flashman1986.de)

 Enhanced by Tobias Oetzel

 For support and installation notes visit http://ovrsized.neo-soft.org!
*/

	if ( !defined('IN_HLSTATS') ) { die('Do not access this file directly.'); }
	//
	// Message of the day
	//
  
	//
	// General
	//
  
	$db->query("SELECT name FROM hlstats_Games WHERE code='$game'");
	if ($db->num_rows() < 1) error("No such game '$game'.");
	
	list($gamename) = $db->fetch_row();
	$db->free_result();
	
	$minkills = 1;
	$minmembers = 3;
  
	$players = 10;  
	if ((isset($_GET['players'])) && (is_numeric($_GET['players'])))
		$players = valid_request($_GET['players'], 1);
  
	$clans = 3;  
	if ((isset($_GET['clans'])) && (is_numeric($_GET['clans'])))
		$clans = valid_request($_GET['clans'], 1);
  
	$servers = 9001;  
	if ((isset($_GET['servers'])) && (is_numeric($_GET['servers'])))
		$servers = valid_request($_GET['servers'], 1);

	//
	// Top 10 Players
	//
	if($players > 0) {  
		$table_players = new Table(
			array(
				new TableColumn(
					'lastName',
					'Playername',
					'width=50&flag=1&link=' . urlencode('mode=statsme&amp;player=%k')
				),
				new TableColumn(
					'skill',
					'Points',
					'width=10&align=right'
				),
				new TableColumn(
					'activity',
					'Activity',
					'width=10&sort=no&type=bargraph'
				),
				new TableColumn(
					'connection_time',
					'Time',
					'width=15&align=right&type=timestamp'
				),
				new TableColumn(
					'kpd',
					'Kpd',
					'width=10&align=right'
				),
			),
			'playerId',
			'skill',
			'kpd',
			true,
			10
		);
  	  
		$result_players = $db->query("
			SELECT
				playerId,
				lastName,
				connection_time,
				skill,
				flag,
				country,
				IFNULL(kills/deaths, '-') AS kpd,
				IFNULL(headshots/kills, '-') AS hpk,
				activity
			FROM
				hlstats_Players
			WHERE
				game='$game'
				AND hideranking=0
				AND kills >= $minkills
			ORDER BY
				$table_players->sort $table_players->sortorder
			LIMIT 0,$players
		");
		$table_players->draw($result_players, 10, 100);
	}
  
	//
	// Top 3 Clans
	//
	if($clans > 0) {
		$table_clans = new Table(
			array(
				new TableColumn(
					'name',
					'Clanname',
					'width=50&link=' . urlencode('mode=claninfo&amp;clan=%k')
				),
				new TableColumn(
					'tag',
					'Tag',
					'width=25&align=center'
				),
				new TableColumn(
					'skill',
					'Points',
					'width=10&align=right'
				),
				new TableColumn(
					'nummembers',
					'Members',
					'width=10&align=right'
				),
			),
			'clanId',
			'skill',
			'kpd',
			true,
			3
		);
	  
		$result_clans = $db->query("
			SELECT
				hlstats_Clans.clanId,
				hlstats_Clans.name,
				hlstats_Clans.tag,
				COUNT(hlstats_Players.playerId) AS nummembers,
				ROUND(AVG(hlstats_Players.skill)) AS skill,
				TRUNCATE(AVG(IF(".$g_options['MinActivity']." > (UNIX_TIMESTAMP() - hlstats_Players.last_event), ((100/".$g_options['MinActivity'].") * (".$g_options['MinActivity']." - (UNIX_TIMESTAMP() - hlstats_Players.last_event))), -1)),2) as activity
			FROM
				hlstats_Clans
			LEFT JOIN hlstats_Players ON
				hlstats_Players.clan=hlstats_Clans.clanId
			WHERE
				hlstats_Clans.game='$game'
				AND hlstats_Clans.hidden <> 1
				AND hlstats_Players.hideranking = 0
				AND IF(".$g_options['MinActivity']." > (UNIX_TIMESTAMP() - hlstats_Players.last_event), ((100/".$g_options['MinActivity'].") * (".$g_options['MinActivity']." - (UNIX_TIMESTAMP() - hlstats_Players.last_event))), -1) >= 0
			GROUP BY
				hlstats_Clans.clanId
			HAVING
				activity >= 0 AND
				nummembers >= $minmembers
			ORDER BY
				$table_clans->sort $table_clans->sortorder
			LIMIT 0,$clans
		");
		$table_clans->draw($result_clans, 3, 100);
	}
  
	//
	// Servers
	//
	if ($servers > 0) { ?>
		<table class="data-table" >
			<tr class="data-table-head">
				<td style="width:50%;" class="fSmall">&nbsp;Participating Servers</td>
				<td style="width:20%;" class="fSmall">&nbsp;Address</td>
				<td style="width:10%;text-align:center;" class="fSmall">&nbsp;Map</td>
				<td style="width:10%;text-align:center;" class="fSmall">&nbsp;Played</td>
				<td style="width:10%;" class="fSmall">&nbsp;Players</td>
			</tr>
        
<?php
	$query= "
            SELECT
                serverId,
                name,
                IF(publicaddress != '',
                    publicaddress,
                    concat(address, ':', port)
                ) AS addr,
				kills,
                headshots,              
                act_players,                                
                max_players,
                act_map,
                map_started,
                map_ct_wins,
                map_ts_wins                 
            FROM
                hlstats_Servers
            WHERE
                game='$game'
            ORDER BY
                serverId
			LIMIT 0, $servers
        ";
	$db->query($query);
	$this_server = array();
	$servers = array();
	while ($rowdata = $db->fetch_array()) {
		$servers[] = $rowdata;
		if ($rowdata['serverId'] == $server_id)
			$this_server = $rowdata;
	}
          
	$i=0;
	for ($i=0; $i<count($servers); $i++)
	{
		$rowdata = $servers[$i]; 
		$server_id = $rowdata['serverId'];
		$c = ($i % 2) + 1;
		$addr = $rowdata['addr'];
		$kills     = $rowdata['kills'];
		$headshots = $rowdata['headshots'];
		$player_string = $rowdata['act_players']."/".$rowdata['max_players'];
		$map_ct_wins = $rowdata['map_ct_wins'];
		$map_ts_wins = $rowdata['map_ts_wins'];

?>

			<tr class="bg<?php echo $c; ?>">
				<td style="width:35%;" class="fSmall"><?php
					echo '<strong>'.$rowdata['name'].'</strong>';
				?></td>
				<td style="width:20%;" class="fSmall"><?php
					echo $addr;
				?></td>
				<td style="text-align:center;width:15%;" class="fSmall"><?php
					echo $rowdata['act_map'];
				?></td>
				<td style="text-align:center;width:15%;" class="fSmall"><?php
					$stamp = time()-$rowdata['map_started'];
					$hours = sprintf('%02d', floor($stamp / 3600));
					$min   = sprintf('%02d', floor(($stamp % 3600) / 60));
					$sec   = sprintf('%02d', floor($stamp % 60)); 
					echo "$hours:$min:$sec";
				?></td>
				<td style="text-align:center;width:15%;" class="fSmall"><?php
					echo $player_string;
				?></td>
			</tr>
<?php } ?>
        </table>        

<?php  } ?>
