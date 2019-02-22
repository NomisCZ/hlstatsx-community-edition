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

function printserverstats($server_id)
{
	global $db, $g_options;
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
	$result = $db->query($query);
	$servers   = array();
	$servers[] = $db->fetch_array($result);
?>



<?php

	$i=0;
	for ($i=0; $i<count($servers); $i++)
	{
		$rowdata = $servers[$i]; 
			
		$server_id = $rowdata['serverId'];
		$game = $rowdata['game'];
		$c = ($i % 2) + 1;
            
		$addr = $rowdata["addr"];
		$kills     = $rowdata['kills'];
		$headshots = $rowdata['headshots'];
		$player_string = $rowdata['act_players']."/".$rowdata['max_players'];
		$map_teama_wins = $rowdata['map_ct_wins'];
		$map_teamb_wins = $rowdata['map_ts_wins'];
		$mode = 'playerinfo';
		if (strpos($_SERVER['PHP_SELF'], 'ingame') !== FALSE)
		{
			$mode = 'statsme';
		}
//style="border:1px solid; margin-bottom:40px;margin-left:auto;margin-right:auto;"
?>

<table class="livestats-table">
	<tr class="data-table-head">
		<td class="fSmall" style="width:2%;">&nbsp;#</td>
		<td class="fSmall" style="width:42%;text-align:left;">&nbsp;Player</td>
		<td class="fSmall" colspan="3" style="width:5%;">&nbsp;Kills</td>
		<td class="fSmall" style="width:4%;">&nbsp;Hs</td>
		<td class="fSmall" style="width:8%;">&nbsp;HS:K</td>
		<td class="fSmall" style="width:6%;">&nbsp;Acc</td>
		<td class="fSmall" style="width:6%;">&nbsp;Lat</td>
		<td class="fSmall" style="width:10%;">&nbsp;Time</td>
		<td class="fSmall" style="width:6%;">&nbsp;+/-</td>
		<td class="fSmall" style="width:6%;">&nbsp;Skill</td>
	</tr>

<?php 
		unset($team_colors);
		$statsdata = $db->query("
			SELECT 
				team, 
				name, 
				teamkills, 
				teamdeaths, 
				teamheadshots, 
				teamping, 
				teamskill, 
				teamshots, 
				teamhits, 
				teamjointime, 
				IFNULL(playerlist_bgcolor,'#D5D5D5') as playerlist_bgcolor, 
				IFNULL(playerlist_color,'#050505') AS playerlist_color, 
				IFNULL( playerlist_index, 99 ) AS playerlist_index
			FROM 
				hlstats_Teams
			RIGHT JOIN
				(SELECT 
					team, 
					sum( kills ) AS teamkills, 
					sum( deaths ) AS teamdeaths, 
					sum( headshots ) AS teamheadshots, 
					avg( ping /2 ) AS teamping, 
					avg( skill ) AS teamskill, 
					sum( shots ) AS teamshots, 
					sum( hits ) AS teamhits, 
					sum( unix_timestamp( NOW( ) ) - connected ) AS teamjointime
				FROM 
					hlstats_Livestats
				WHERE 
					server_id = $server_id
					AND connected >0
				GROUP BY 
					team
				ORDER BY 
					teamkills
				) teaminfo
			ON
				code = team
			AND
				hlstats_Teams.game = '$game'
			ORDER BY 
				playerlist_index
			LIMIT 0 , 30
			");
		$teamdata = array();
		$playerdata = array();
		$teamno = 0;
		while ($thisteam = $db->fetch_array($statsdata))
		{
			$teamname = $db->escape($thisteam['team']);
			$teamdata[$teamno] = $thisteam;
			$pldata = $db->query("
								SELECT
									player_id, 
									name, 
									kills, 
									deaths, 
									headshots, 
									ping, 
									skill, 
									shots, 
									hits, 
									connected, 
									skill_change, 
									cli_flag
								FROM 
									hlstats_Livestats 
								WHERE 
									server_id = $server_id 
									AND team = '$teamname'
								ORDER BY 
									kills DESC
				");
			while ($thisplayer = $db->fetch_array($pldata))
			{
				$playerdata[$teamno][] = $thisplayer;
			}
			$teamno++;
		}
		$curteam = 0;
		while (isset($teamdata[$curteam]))
		{
			$j=0;
			$thisteam = $teamdata[$curteam];
			$teamcolor = 'background:'.$thisteam['playerlist_bgcolor'].';color:'.$thisteam['playerlist_color'];
			$bordercolor = 'background:'.$$thisteam['playerlist_bgcolor'].';color:'.$thisteam['playerlist_color'].';border-top:1px '.$thisteam['playerlist_color'].' solid';
			$team_display_name  = htmlspecialchars($thisteam['name']);
			while (isset($playerdata[$curteam][$j]))
			{
				$thisplayer = $playerdata[$curteam][$j];

?>
	<tr style="<?php echo $teamcolor ?>">
		<td class="fSmall"><?php
				if (isset($thisplayer) && $team_display_name)
				{
					echo ($j+1);
				}
				else
				{
					echo '&nbsp;';
				}
?>		</td>
		<td style="text-align:left;<?php echo $teamcolor ?>" class="fSmall"><?php
				if (isset($thisplayer))
				{
					if (strlen($thisplayer['name'])>50)
					{
						$thisplayer['name'] = substr($thisplayer['name'], 0, 50);
					}
					if ($g_options['countrydata'] == 1)
					{
						echo '<img src="'.getFlag($thisplayer['cli_flag']).'" alt="'.ucfirst(strtolower($thisplayer['cli_country'])).'" title="'.ucfirst(strtolower($thisplayer['cli_country'])).'" />&nbsp;';
					}
					echo '<a style="color:'.$thisteam['playerlist_color'].'" href="'.$g_options['scripturl'].'?mode='.$mode.'&amp;player='.$thisplayer['player_id'].'">';
					echo htmlspecialchars($thisplayer['name'], ENT_COMPAT).'</a>';
				}
				else
				{
					echo '&nbsp;';
				}
?>		</td>
		<td style="text-align:right;width:2%;<?php echo $teamcolor ?>" class="fSmall"><?php
				if (isset($thisplayer))
				{
					echo $thisplayer['kills'];
				}
				else
				{
					echo '&nbsp;';
				}
?>		</td>
		<td style="width:1%;<?php echo $teamcolor ?>" class="fSmall"><?php
				if (isset($thisplayer))
				{
					echo ':';
				}
				else
				{
					echo '&nbsp;';
				}
?>		</td>
		<td style="text-align:left;width:2%;<?php echo $teamcolor ?>" class="fSmall"><?php
				if (isset($thisplayer))
				{
					echo $thisplayer['deaths'];
				}
				else
				{
					echo '&nbsp;';
				}
?>		</td>
		<td style="<?php echo $teamcolor ?>" class="fSmall"><?php
				if (isset($thisplayer))
				{
					echo $thisplayer['headshots'];
				}
				else
				{
					echo '&nbsp;';
				}
?>		</td>
		<td style="<?php echo $teamcolor ?>" class="fSmall"><?php
				if (isset($thisplayer))
				{
					$hpk = sprintf('%.2f', 0);
					if ($thisplayer['kills'] > 0)
					{
						$hpk = sprintf('%.2f', $thisplayer['headshots']/$thisplayer['kills']);
					}
					echo $hpk;
				}
				else
				{
					echo '&nbsp;';
				}
?>		</td>
		<td style="<?php echo $teamcolor ?>" class="fSmall"><?php
				if (isset($thisplayer))
				{
					$acc = sprintf('%.0f', 0);
					if ($thisplayer['shots'] > 0)
					{
						$acc = sprintf('%.0f', ($thisplayer['hits']/$thisplayer['shots'])*100);
					}
					echo "$acc%";
				}
				else
				{
					echo '&nbsp;';
				}
?>		</td>
		<td style="<?php echo $teamcolor ?>" class="fSmall"><?php
				if (isset($thisplayer))
				{
					echo sprintf('%.0f', $thisplayer['ping'] / 2);
				}
				else
				{
					echo '&nbsp;';
				}
?>		</td>
		<td style="<?php echo $teamcolor ?>" class="fSmall"><?php
				if (isset($thisplayer))
				{
					if ($thisplayer['connected']>0)
					{
						$stamp = time()-$thisplayer['connected'];
						$hours = sprintf('%02d', floor($stamp / 3600));
						$min   = sprintf('%02d', floor(($stamp % 3600) / 60));
						$sec   = sprintf('%02d', floor($stamp % 60));
						echo $hours.':'.$min.':'.$sec;
					}
					else
					{
						echo 'Unknown';
					}
				}
				else
				{
					echo '&nbsp;';
				}
?>		</td>
		<td style="<?php echo $teamcolor ?>" class="fSmall"><?php
				if (isset($thisplayer))
				{
					echo $thisplayer['skill_change'];
				}
				else
				{
					echo '&nbsp;';
				}
?>		</td>
		<td style="<?php echo $teamcolor ?>" class="fSmall"><?php
				if (isset($thisplayer))
				{
					echo number_format($thisplayer['skill']);
				}
				else
				{
					echo '&nbsp;';
				}
?>		</td>
	</tr>

<?php
			$j++;	
			}
			
			if ($team_display_name)
			{
    ?>
	<tr style="<?php echo $teamcolor ?>">
		<td style="<?php echo $bordercolor ?>" class="fSmall">&nbsp;</td>
		<td style="text-align:left;<?php echo $bordercolor ?>" class="fSmall"><?php
				echo "<strong>$team_display_name</strong>";
				if (($map_teama_wins > 0) || ($map_teamb_wins > 0))
				{
					echo '&nbsp;('.$map_teama_wins.' wins)';
				}
?>		</td>
		<td style="width:2%;text-align:right;<?php echo $bordercolor ?>" class="fSmall"><?php
				if (count($teamdata[$curteam]) > 0)
				{
					echo $teamdata[$curteam]['teamkills'];
				}
				else
				{
					echo '&nbsp;';
				}
?>		</td>
		<td style="width:1%;<?php echo $bordercolor ?>" class="fSmall"><?php
				if (count($teamdata[$curteam]) > 0)
				{
					echo ':';
				}
				else
				{
					echo '&nbsp;';
				}
?>		</td>
		<td style="width:2%;text-align:left;<?php echo $bordercolor ?>" class="fSmall"><?php
				if (count($teamdata[$curteam]) > 0)
				{
					echo $teamdata[$curteam]['teamdeaths'];
				}
				else
				{
					echo '&nbsp;';
				}
?>		</td>
		<td style="<?php echo $bordercolor ?>" class="fSmall"><?php
				if (count($teamdata[$curteam]) > 0)
				{
					echo $teamdata[$curteam]['teamheadshots'];
				}
				else
				{
					echo '&nbsp;';
				}
?>		</td>
		<td style="<?php echo $bordercolor ?>" class="fSmall"><?php
				if (count($teamdata[$curteam]) > 0)
				{
					$hpk = sprintf('%.2f', 0);
					if ($teama_kills > 0)
					{
						$hpk = sprintf('%.2f', $teamdata[$curteam]['headshots']/$teamdata[$curteam]['kills']);
					}
					echo $hpk;
				}
				else
				{
					echo '&nbsp;';
				}
?>		</td>
		<td style="<?php echo $bordercolor ?>" class="fSmall"><?php
				if (count($teamdata[$curteam]) > 0)
				{
					$acc = sprintf('%.0f', 0);
					if ($teama_shots > 0)
					{
						$acc = sprintf('%.0f', ($teamdata[$curteam]['teamhits']/$teamdata[$curteam]['teamshots'])*100);
					}
					echo "$acc%";
				}
				else
				{
					echo '&nbsp;';
				}
                        ?></td>
		<td style="<?php echo $bordercolor ?>" class="fSmall"><?php
				if (count($teamdata[$curteam]) > 0)
				{
					echo sprintf('%.0f', $teamdata[$curteam]['teamping'] / count($teamdata[$curteam]));
				}
				else
				{
					echo '&nbsp;';
				}
?>		</td>
		<td style="<?php echo $bordercolor ?>" class="fSmall"><?php
				if (count($teamdata[$curteam]) > 0)
				{
					if ($teamdata[$curteam]['teamjointime'] > 0)
					{
						$stamp = $teamdata[$curteam]['teamjointime'];
						$hours = sprintf('%02d', floor($stamp / 3600));
						$min   = sprintf('%02d', floor(($stamp % 3600) / 60));
						$sec   = sprintf('%02d', floor($stamp % 60));
						echo $hours.':'.$min.':'.$sec;
					}
					else
					{
						echo 'Unknown';
					}
				}
				else
				{
					echo '&nbsp;';
				}
?>		</td>
		<td style="<?php echo $bordercolor ?>" class="fSmall">-</td>
		<td style="<?php echo $bordercolor ?>" class="fSmall"><?php
				if (count($teamdata[$curteam])>0)
				{
					echo number_format(sprintf('%.0f', $teamdata[$curteam]['teamskill']));
				}
				else
				{
					echo '&nbsp;';
				}
?>		</td>
	</tr>

<?php
			}
			$curteam++;
		} //while i for teams
		if (count($teamdata) == 0)
		{
?>
	<tr>
		<td style="background:#EFEFEF;color:black"><?php 
			echo '&nbsp;';  
?>		</td>
		<td colspan="11" style="text-align:left;background:#EFEFEF;color:black"><?php 
			echo "No Players";  
?>		</td>
	</tr>
<?php
		}
    ?>
</table>
<?php			  	
	}  // for servers
}
?>