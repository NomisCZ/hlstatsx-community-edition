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

	if ( !defined('IN_HLSTATS') ) { die('Do not access this file directly.'); }

	$server_id = 1;
	if ((isset($_GET['server_id'])) && (is_numeric($_GET['server_id'])))
		$server_id = valid_request($_GET['server_id'], 1);

    $query= "
			SELECT
				count(*)
			FROM
				hlstats_Players
			WHERE 
				game='$game'
	";
	$result = $db->query($query);
	list($total_players) = $db->fetch_row($result);

    $query= "
			SELECT
				SUM(kills),
				SUM(headshots),
				count(serverId)		
			FROM
				hlstats_Servers
			WHERE 
				game='$game'
	";
	$result = $db->query($query);
	list($total_kills, $total_headshots, $total_servers) = $db->fetch_row($result);
?>
		
    <table class="data-table">
		<tr class="data-table-head">
			<td class="fSmall"><?php
				if ($total_kills>0)
					$hpk = sprintf('%.2f', ($total_headshots/$total_kills)*100);
				else
					$hpk = sprintf('%.2f', 0);
				echo 'Tracking <strong>'.number_format($total_players).'</strong> players with <strong>'.number_format($total_kills).'</strong> kills and <strong>'.number_format($total_headshots)."</strong> headshots (<strong>$hpk%</strong>) on <strong>$total_servers</strong> servers"; ?>
			</td>
		</tr>	
	</table>
	<table class="data-table" >
        <tr class="data-table-head">
			<td style="text-align:center;padding:0px;">
				<img src="show_graph.php?type=0&amp;width=870&amp;height=200&amp;server_id=<?php echo $server_id ?>&amp;bgcolor=<?php echo $g_options['graphbg_load']; ?>&amp;color=<?php echo $g_options['graphtxt_load']; ?>" style="border:0px;">
			</td>
		</tr>
	</table>
