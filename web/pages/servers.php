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

	require('livestats.php');     
    $db->query("SELECT name FROM hlstats_Games WHERE code='$game'");
    if ($db->num_rows() < 1) error("No such game '$game'.");
    
    list($gamename) = $db->fetch_row();
    $db->free_result();

    pageHeader(array($gamename), array($gamename => ''));
    
?>


<br />
<?php
    $server_id = 1;
    if ((isset($_GET['server_id'])) && (is_numeric($_GET['server_id'])))
    {
      $server_id = valid_request($_GET['server_id'], 1);
    }
    else
    {
        error("Invalid server ID provided.", 0);
        pageFooter();
        die();
    }


    $query= "
            SELECT
                SUM(kills),
                SUM(headshots),
                count(serverId)     
            FROM
                hlstats_Servers
			WHERE 
				serverId='$server_id'
	";
	$result = $db->query($query);
	list($total_kills, $total_headshots) = $db->fetch_row($result);
        
	$query= "
			SELECT
				serverId,
				name,
				IF(publicaddress != '',
					publicaddress,
					concat(address, ':', port)
				) AS addr,
				statusurl,
				kills,
				players,
				rounds, suicides, 
				headshots, 
				bombs_planted, 
				bombs_defused, 
				ct_wins, 
				ts_wins, 
				ct_shots, 
				ct_hits, 
				ts_shots, 
				ts_hits,      
				act_players,                                
				max_players,
				act_map,
				map_started,
				map_ct_wins,
				map_ts_wins,
				game                 
			FROM
				hlstats_Servers
			WHERE
				serverId='$server_id'
	";
	$db->query($query);
	$servers   = array();
	$servers[] = $db->fetch_array();
        
?>

<div class="block">
<?php
	printSectionTitle('Server Live View');
	$i=0;
	for ($i=0; $i<count($servers); $i++)
	{
		$rowdata = $servers[$i]; 
	
		$server_id = $rowdata['serverId'];
		$game = $rowdata['game'];
	
		$addr = $rowdata['addr'];          
		$kills     = $rowdata['kills'];
		$headshots = $rowdata['headshots'];
		$player_string = $rowdata['act_players']."/".$rowdata['max_players'];
		$map_teama_wins = $rowdata['map_ct_wins'];
		$map_teamb_wins = $rowdata['map_ts_wins'];
?>
	<div class="subblock">
		<table class="data-table">
			<tr class="data-table-head">
				<td class="fSmall" style="width:37%;">&nbsp;Server</td>
				<td class="fSmall" style="width:23%;">&nbsp;Address</td>
				<td class="fSmall" style="width:6%;text-align:center;">&nbsp;Map</td>
				<td class="fSmall" style="width:6%;text-align:center;">&nbsp;Played</td>
				<td class="fSmall" style="width:10%;text-align:center;">&nbsp;Players</td>
				<td class="fSmall" style="width:6%;text-align:center;">&nbsp;Kills</td>
				<td class="fSmall" style="width:6%;text-align:center;">&nbsp;Headshots</td>
				<td class="fSmall" style="width:6%;text-align:center;">&nbsp;Hpk</td>
			</tr>
			<tr class="game-table-row">
				<td class="game-table-cell"><?php
		$image = getImage("/games/$game/game");
		echo '<img style="vertical-align:middle;" src="';
		if ($image)
			echo $image['url'];
		else
			echo IMAGE_PATH . '/game.gif';
		echo "\" alt=\"$game\" />&nbsp;";
		echo '<b>'.htmlspecialchars($rowdata['name']).'</b>';
                        ?></td>
			<td class="game-table-cell"><?php
		echo "$addr <a href=\"steam://connect/$addr\" style=\"color:black\">(Join)</a>";
                    ?></td>
			<td class="game-table-cell" style="text-align:center;"><?php
		echo $rowdata['act_map'];
                    ?></td>
			<td class="game-table-cell" style="text-align:center;"><?php
		$stamp = $rowdata['map_started']==0?0:time() - $rowdata['map_started'];
		$hours = sprintf("%02d", floor($stamp / 3600));
		$min   = sprintf("%02d", floor(($stamp % 3600) / 60));
		$sec   = sprintf("%02d", floor($stamp % 60)); 
		echo $hours.":".$min.":".$sec;
                    ?></td>
			<td class="game-table-cell" style="text-align:center;"><?php
		echo $player_string;
                    ?></td>
			<td class="game-table-cell" style="text-align:center;"><?php
		echo number_format($kills);
					?></td>
			<td class="game-table-cell" style="text-align:center;"><?php
		echo number_format($headshots);
					?></td>
			<td class="game-table-cell" style="text-align:center;"><?php
		if ($kills>0)
			echo sprintf("%.4f", ($headshots/$kills));
		else  
			echo sprintf("%.4f", 0);
                    ?></td>
		</tr>
	</table>        
<?php
		printserverstats($server_id);
	}  //for servers
?>	</div>
</div>
<div class="block">
	<?php printSectionTitle('Server Load History'); ?>
	<div class="subblock">
		<table class="data-table">
			<tr class="data-table-head">
				<td class="fSmall">&nbsp;24h View</td>
			</tr>
			<tr class="data-table-row">
				<td style="text-align:center; height: 200px; vertical-align:middle;">
					<img src="show_graph.php?type=0&amp;game=<?php echo $game; ?>&amp;width=870&amp;height=200&amp;server_id=<?php echo $server_id ?>&amp;bgcolor=<?php echo $g_options['graphbg_load']; ?>&amp;color=<?php echo $g_options['graphtxt_load']; ?>&amp;range=1" alt="24h View" />
				</td>
			</tr>
		</table>
		<br /><br />
		<table class="data-table">
			<tr class="data-table-head">
				<td class="fSmall">&nbsp;Last Week</td>
			</tr>
			<tr class="data-table-row">
				<td style="text-align:center; height: 200px; vertical-align:middle;">
					<img src="show_graph.php?type=0&amp;game=<?php echo $game; ?>&amp;width=870&amp;height=200&amp;server_id=<?php echo $server_id ?>&amp;bgcolor=<?php echo $g_options['graphbg_load']; ?>&amp;color=<?php echo $g_options['graphtxt_load']; ?>&amp;range=2" alt="Last Week" />
				</td>
			</tr>
		</table>
		<br /><br />
		<table class="data-table">
			<tr class="data-table-head">
				<td class="fSmall">&nbsp;Last Month</td>
			</tr>
			<tr class="data-table-row">
				<td style="text-align:center; height: 200px; vertical-align:middle;">
					<img src="show_graph.php?type=0&amp;game=<?php echo $game; ?>&amp;width=870&amp;height=200&amp;server_id=<?php echo $server_id ?>&amp;bgcolor=<?php echo $g_options['graphbg_load']; ?>&amp;color=<?php echo $g_options['graphtxt_load']; ?>&amp;range=3" alt="Last Month" />
				</td>
			</tr>
		</table>
		<br /><br />
		<table class="data-table">
			<tr class="data-table-head">
				<td class="fSmall">&nbsp;Last Year</td>
			</tr>
			<tr class="data-table-row">
				<td style="text-align:center; height: 200px; vertical-align:middle;">
					<img src="show_graph.php?type=0&amp;game=<?php echo $game; ?>&amp;width=870&amp;height=200&amp;server_id=<?php echo $server_id ?>&amp;bgcolor=<?php echo $g_options['graphbg_load']; ?>&amp;color=<?php echo $g_options['graphtxt_load']; ?>&amp;range=4" alt="Last Year" />
				</td>
			</tr>
		</table>
	</div>
</div>
