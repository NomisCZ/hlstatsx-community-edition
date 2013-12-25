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
?>

	<?php printSectionTitle('Player Information'); ?>
	<div class="subblock">
		<div style="float:left;vertical-align:top;width:48.5%;">
			<table class="data-table">
				<tr class="data-table-head">
					<td style="vertical-align:top;">Player Profile<br /><br /></td>
					<td style="text-align:center; vertical-align:middle;" rowspan="7" id="player_avatar">
						<?php
							$db->query
							("
								SELECT
									hlstats_PlayerUniqueIds.uniqueId,
									CAST(LEFT(hlstats_PlayerUniqueIds.uniqueId,1) AS unsigned) + CAST('76561197960265728' AS unsigned) + CAST(MID(hlstats_PlayerUniqueIds.uniqueId, 3,10)*2 AS unsigned) AS communityId
								FROM
									hlstats_PlayerUniqueIds
								WHERE
									hlstats_PlayerUniqueIds.playerId = '$player'
							");
							list($uqid, $coid) = $db->fetch_row();
							function fetchpage($page)
							{
								$domain="steamcommunity.com";
								$indata="";
						//		$data=file_get_contents($page);
								$fsock=fsockopen($domain, 80, $errno, $errstr,2);
								if(!$fsock)
								{
									echo "Error: $errstr";
								}
								else
								{
									$request=sprintf("GET %s HTTP/1.1\r\nHost: %s\r\nConnection: Close\r\n\r\n",$page,$domain);
									fwrite($fsock, $request);
									while(!feof($fsock))
									{
										$indata.=fgets($fsock,1024);
									}
									fclose($fsock);
									return $indata;
								}
							}
							$page = "/profiles/$coid?xml=1";
							$pagedata=fetchpage($page);
							if( preg_match('/Location: (.*)/', $pagedata, $location) )
							{
								$page = trim($location[1]) . "?xml=1";
								$pagedata = fetchpage($page);
							}
							preg_match('/<onlineState>(.*?)<\/onlineState>/', $pagedata, $results);
							preg_match('/<avatarFull><!\[CDATA\[(.*?)\]\]><\/avatarFull>/', $pagedata, $results2);
							$status = ucwords($results[1]);
							$avatar_full = $results2[1];
							$avimg = getImage("/avatars/$player");
							if ($avimg)
							{
								$avatar_full = $avimg['url'];
							}
							else if ($avatar_full == '' || $playerdata['blockavatar'] == '1')
							{
								$avatar_full = IMAGE_PATH."/unknown.jpg";
							}
							if ($status == '')
								$status = '(Unknown)';
							echo("<img src=\"$avatar_full\" style=\"height:158px;width:158px;\" alt=\"Steam Community Avatar\" />");
						?>
					</td>
				</tr>
				<tr class="bg2">
					<td>
						<?php
							echo '<img src="'.getFlag($playerdata['flag']).'" alt="'.$playerdata['country'].'" title="'.$playerdata['country'].'" />&nbsp;';
							echo '<strong>' . htmlspecialchars($playerdata['lastName'], ENT_COMPAT) . ' </strong>';
						?>
					</td>
				</tr>
				<tr class="bg1">
					<td>
						<?php
							if ($playerdata['country'])
							{
								echo 'Location: ';
								if ($playerdata['city']) {
									echo htmlspecialchars($playerdata['city'], ENT_COMPAT) . ', ';
								}
								echo '<a href="'.$g_options['scripturl'].'?mode=countryclansinfo&amp;flag='.$playerdata['flag']."&amp;game=$game\">" . $playerdata['country'] . '</a>';
							}
							else
							{
								echo 'Location: (Unknown)';
							}
						?>
					</td>
				</tr>
				<tr class="bg2">
					<td>
						<?php 
							$prefix = ((!preg_match('/^BOT/i',$uqid)) && $g_options['Mode'] == 'Normal') ? 'STEAM_0:' : '';
							echo "Steam: <a href=\"http://steamcommunity.com/profiles/$coid\" target=\"_blank\">$prefix" . "$uqid</a>";
						?>
					</td>
				</tr>
				<tr class="bg1">
					<td>Status: <strong><?php echo $status; ?></strong></td>
				</tr>
				<tr class="bg2">
					<td>
						<a href="steam://friends/add/<?php echo($coid); ?>" target="_blank">Click here to add as friend</a>
					</td>
				</tr>
				<tr class="bg1">
					<td><?php echo "Karma: $statusmsg"; ?></td>
				</tr>
				<tr class="bg2">
					<td style="width:50%;">Member of Clan:</td>
					<td style="width:50%;">
						<?php
							if ($playerdata['clan'])
							{
								echo '&nbsp;<a href="' . $g_options['scripturl'] . '?mode=claninfo&amp;clan=' . $playerdata['clan'] . '">' . htmlspecialchars($playerdata['clan_name'], ENT_COMPAT) . '</a>';
							}
							else
								echo '(None)';
						?>
					</td>
				</tr>
				<tr class="bg1">
					<td>Real Name:</td>
					<td>
						<?php
							if ($playerdata['fullName'])
							{
								echo '<b>' . htmlspecialchars($playerdata['fullName'], ENT_COMPAT) . '</b>';
							}
							else
								echo "(<a href=\"" . $g_options['scripturl'] . '?mode=help#set"><em>Not Specified</em></a>)';
						?>
					</td>
				</tr>
				<tr class="bg2">
					<td>E-mail Address:</td>
					<td>
						<?php
							if ($email = getEmailLink($playerdata['email']))
							{
								echo $email;
							}
							else
								echo "(<a href=\"" . $g_options['scripturl'] . '?mode=help#set"><em>Not Specified</em></a>)';
						?>
					</td>
				</tr>
				<tr class="bg1">
					<td>Home Page:</td>
					<td>
						<?php
							if ($playerdata['homepage'])
							{
								echo getLink($playerdata['homepage']);
							}
							else
								echo "(<a href=\"" . $g_options['scripturl'] . '?mode=help#set"><em>Not Specified</em></a>)';
						?>
					</td>
				</tr>
				<tr class="bg2">
					<td>Last Connect:*</td>
					<td>
						<?php
							$db->query
							("
								SELECT
									DATE_FORMAT(eventTime, '%a. %b. %D, %Y @ %T')
								FROM
									hlstats_Events_Connects
								WHERE
									hlstats_Events_Connects.playerId = '$player'
								ORDER BY
									id desc
								LIMIT
									1
							");
							list($lastevent) = $db->fetch_row();
							if ($lastevent)
								echo $lastevent;
							else
								echo '(Unknown)';
						?>
					</td>
				</tr>
				<tr class="bg1">
					<td>Total Connection Time:</td>
					<td>
						<?php echo timestamp_to_str($playerdata['connection_time']); ?>
					</td>
				</tr>
				<tr class="bg2">
					<td>Average Ping:*</td>
					<td>
						<?php
							$db->query
							("
								SELECT
									ROUND(SUM(hlstats_Events_Latency.ping) / COUNT(hlstats_Events_Latency.ping), 0) AS av_ping,
									ROUND(ROUND(SUM(hlstats_Events_Latency.ping) / COUNT(ping), 0) / 2, 0) AS av_latency
								FROM
									hlstats_Events_Latency
								WHERE 
									hlstats_Events_Latency.playerId = '$player'
							");
							list($av_ping, $av_latency) = $db->fetch_row();
							if ($av_ping)
								echo $av_ping." ms (Latency: $av_latency ms)";
							else
								echo '-';
						?>
					</td>
				</tr>
				<tr class="bg1">
					<td>Favorite Server:*</td>
					<td>
						<?php
							// leave this one
							$db->query
							("
								SELECT
									hlstats_Events_Entries.serverId,
									hlstats_Servers.name,
									COUNT(hlstats_Events_Entries.serverId) AS cnt
								FROM
									hlstats_Events_Entries
								INNER JOIN
									hlstats_Servers
								ON
									hlstats_Servers.serverId = hlstats_Events_Entries.serverId
								WHERE 
									hlstats_Events_Entries.playerId = '$player'
								GROUP BY
									hlstats_Events_Entries.serverId
								ORDER BY
									cnt DESC
								LIMIT
									1
							");
							list($favServerId, $favServerName) = $db->fetch_row();
							echo "<a href='hlstats.php?game=$game&amp;mode=servers&amp;server_id=$favServerId'> $favServerName </a>";
						?>
					</td>
				</tr>
				<tr class="bg2">
					<td>Favorite Map:*</td>
					<td>
						<?php
							$db->query
							("
								SELECT
									hlstats_Events_Entries.map,
									COUNT(map) AS cnt
								FROM
									hlstats_Events_Entries
								WHERE
									hlstats_Events_Entries.playerId = '$player'
								GROUP BY
									hlstats_Events_Entries.map
								ORDER BY
									cnt DESC
								LIMIT
									1
							");
							list($favMap) = $db->fetch_row();
							echo "<a href=\"hlstats.php?game=$game&amp;mode=mapinfo&amp;map=$favMap\"> $favMap </a>";
						?>
					</td>
				</tr>
				<tr class="bg1">
					<td>Favorite Weapon:*</td>
						<?php
							$result = $db->query("
								SELECT
									hlstats_Events_Frags.weapon,
									hlstats_Weapons.name,
									COUNT(hlstats_Events_Frags.weapon) AS kills,
									SUM(hlstats_Events_Frags.headshot=1) as headshots
								FROM
									hlstats_Events_Frags
								LEFT JOIN
									hlstats_Weapons
								ON
									hlstats_Weapons.code = hlstats_Events_Frags.weapon
								WHERE
									hlstats_Events_Frags.killerId=$player
								GROUP BY
									hlstats_Events_Frags.weapon
								ORDER BY
									kills desc, headshots desc
								LIMIT
									1
							");
							while ($rowdata = $db->fetch_row($result))
							{ 
								$fav_weapon = $rowdata[0];
								$weap_name = htmlspecialchars($rowdata[1]);
							}
							if ($fav_weapon == '')
								$fav_weapon = 'Unknown';
							$image = getImage("/games/$game/weapons/$fav_weapon");
						// Check if image exists
							$weaponlink = "<a href=\"hlstats.php?mode=weaponinfo&amp;weapon=$fav_weapon&amp;game=$game\">";
							if ($image)
							{
								$cellbody = "\t\t\t\t\t<td style=\"text-align: center\">$weaponlink<img src=\"" . $image['url'] . "\" alt=\"$weap_name\" title=\"$weap_name\" />";
							}
							else
							{
								$cellbody = "\t\t\t\t\t<td><strong> $weaponlink$weap_name</strong>";
							}
							$cellbody .= "</a>";
							echo $cellbody;
						?>
					</td>
				</tr>
			</table><br />
		</div>

		<div style="float:right;vertical-align:top;width:48.5%;">
			<table class="data-table">
				<tr class="data-table-head">
					<td style="vertical-align:top;" colspan="3">Statistics Summary<br /><br /></td>
				</tr>
				<tr class="bg1">
					<td style="width:50%;">Activity:</td>
					<td style="width:35%;">
						<?php
							$width = sprintf("%d%%", $playerdata['activity'] + 0.5);
							$bar_type = 1;
							if ($playerdata['activity'] > 40)
								$bar_type = "6";
							elseif ($playerdata['activity'] > 30)
								$bar_type = "5";
							elseif ($playerdata['activity'] > 20)
								$bar_type = "4";
							elseif ($playerdata['activity'] > 10)
								$bar_type = "3";
							elseif ($playerdata['activity'] > 5)
								$bar_type = "2";
							echo "<img src=\"" . IMAGE_PATH . "/bar$bar_type.gif\" style=\"width:$width;height:10px;border:0;\" alt=\"".$playerdata['activity'].'%" />';
						?>
					</td>
					<td style="width:15%;"><?php echo $playerdata['activity'].'%'; ?></td>
				</tr>
				<tr class="bg2">
					<td>Points:</td>
					<td style="width:55%;" colspan="2">
						<?php
							echo '<b>' . number_format($playerdata['skill']) . '</b>';
						?>
					</td>
				</tr>
				<tr class="bg1">
					<td style="width:45%;">Rank:</td>
					<td style="width:55%;" colspan="2">
						<?php
							if (($playerdata['activity'] > 0) && ($playerdata['hideranking'] == 0))
							{
								$rank = get_player_rank($playerdata);
							}
							else
							{
								if ($playerdata['hideranking'] == 1)
								{
									$rank = "Hidden";
								}
								elseif ($playerdata['hideranking'] == 2)
								{
									$rank = "<span style=\"color:red;\">Banned</span>";
								}
								else
								{
									$rank = 'Not active';
								}
							} 
							if (is_numeric($rank))
							{
								echo '<b>' . number_format($rank) . '</b>';
							}
							else
							{
								echo "<b> $rank</b>";
							}
						?>
					</td>
				</tr>
				<tr class="bg2">
					<td style="width:45%;">Kills per Minute:</td>
					<td style="width:55%;" colspan="2">
						<?php
							if ($playerdata['connection_time'] > 0)
							{
								echo sprintf('%.2f', ($playerdata['kills'] / ($playerdata['connection_time'] / 60)));
							}
							else
							{
								echo '-'; 
							} 
						?>
					</td>
				</tr>
				<tr class="bg1">
					<td style="width:45%;">Kills per Death:</td>
					<td style="width:55%;" colspan="2">
						<?php
							$db->query
							("
								SELECT
									IFNULL(ROUND(SUM(hlstats_Events_Frags.killerId = '$player') / IF(SUM(hlstats_Events_Frags.victimId = '$player') = 0, 1, SUM(hlstats_Events_Frags.victimId = '$player')), 2), '-')
								FROM
									hlstats_Events_Frags
								WHERE
									(
										hlstats_Events_Frags.killerId = '$player'
										OR hlstats_Events_Frags.victimId = '$player'
									)
							");
							list($realkpd) = $db->fetch_row();
							echo $playerdata['kpd'];
							echo " ($realkpd*)";
						?>
					</td>
				</tr>
				<tr class="bg2">
					<td style="width:45%;">Headshots per Kill:</td>
					<td style="width:55%;" colspan="2">
						<?php
							$db->query
							("
								SELECT
									IFNULL(SUM(hlstats_Events_Frags.headshot=1) / COUNT(*), '-')
								FROM
									hlstats_Events_Frags
								WHERE
									hlstats_Events_Frags.killerId = '$player'
							");
							list($realhpk) = $db->fetch_row();
							echo $playerdata['hpk'];
							echo " ($realhpk*)";
						?>
					</td>
				</tr>
				<tr class="bg1">
					<td style="width:45%;">Shots per Kill:</td>
					<td style="width:55%;" colspan="2">
						<?php
							$db->query
							("
								SELECT
									IFNULL(ROUND((SUM(hlstats_Events_Statsme.hits) / SUM(hlstats_Events_Statsme.shots) * 100), 2), 0.0) AS accuracy,
									SUM(hlstats_Events_Statsme.shots) AS shots,
									SUM(hlstats_Events_Statsme.hits) AS hits,
									SUM(hlstats_Events_Statsme.kills) AS kills
								FROM
									hlstats_Events_Statsme
								WHERE
									hlstats_Events_Statsme.playerId='$player'
							");
							list($playerdata['accuracy'], $sm_shots, $sm_hits, $sm_kills) = $db->fetch_row();
							if ($sm_kills > 0)
							{
								echo sprintf('%.2f', ($sm_shots / $sm_kills));
							}
							else
							{
								echo '-';
							}
						?>
					</td>
				</tr>
				<tr class="bg2">
					<td style="width:45%;">Weapon Accuracy:</td>
					<td style="width:55%;" colspan="2">
						<?php
							echo $playerdata['acc'] . '%';
							echo " (".sprintf('%.0f', $playerdata['accuracy']).'%*)';
						?>
					</td>
				</tr>
				<tr class="bg1">
					<td style="width:45%;">Headshots:</td>
					<td style="width:55%;" colspan="2">
						<?php
							if ($playerdata['headshots']==0) 
								echo number_format($realheadshots);
							else
								echo number_format($playerdata['headshots']);
								echo ' ('.number_format($realheadshots).'*)';
						?>
					</td>
				</tr>
				<tr class="bg2">
					<td style="width:45%;">Kills:</td>
					<td style="width:55%;" colspan="2">
						<?php
							echo number_format($playerdata['kills']);
							echo ' ('.number_format($realkills).'*)';
						?>
					</td>
				</tr>
				<tr class="bg1">
					<td style="width:45%;">Deaths:</td>
					<td style="width:55%;" colspan="2">
						<?php
							echo number_format($playerdata['deaths']);
							echo ' ('.number_format($realdeaths).'*)';
						?>
					</td>
				</tr>
				<tr class="bg2">
					<td style="width:45%;">Longest Kill Streak:</td>
					<td style="width:55%;" colspan="2">
						<?php
							$db->query
							("
								SELECT
									hlstats_Players.kill_streak
								FROM
									hlstats_Players
								WHERE
									hlstats_Players.playerId = '$player'
							");
							list($kill_streak) = $db->fetch_row();
							echo number_format($kill_streak);
						?>
					</td>
				</tr>
				<tr class="bg1">
					<td style="width:45%;">Longest Death Streak:</td>
					<td style="width:55%;" colspan="2">
						<?php
							$db->query
							("
								SELECT
									hlstats_Players.death_streak
								FROM
									hlstats_Players
								WHERE
									hlstats_Players.playerId = '$player'
							");
							list($death_streak) = $db->fetch_row();
							echo number_format($death_streak);
						?>
					</td>
				</tr>
				<tr class="bg2">
					<td style="width:45%;">Suicides:</td>
					<td style="width:55%;" colspan="2">
						<?php echo number_format($playerdata['suicides']); ?>
					</td>
				</tr>
				<tr class="bg1">
					<td style="width:45%;">Teammate Kills:</td>
					<td style="width:55%;" colspan="2">
						<?php
							echo number_format($playerdata['teamkills']);
							echo ' ('.number_format($realteamkills).'*)';
						?>
					</td>
				</tr>
			</table><br />
			<?php
				echo '&nbsp;&nbsp;<img src="' . IMAGE_PATH . '/history.gif" style="padding-left:3px;padding-right:3px;" alt="History" />&nbsp;<b>'
					. htmlspecialchars($playerdata['lastName'], ENT_COMPAT) . '</b>\'s History:<br />';
				echo '&nbsp;&nbsp;<a href="' . $g_options['scripturl'] . "?mode=playerhistory&amp;player=$player\">Events</a>&nbsp;|&nbsp;";
				echo '<a href="' . $g_options['scripturl'] . "?mode=playersessions&amp;player=$player\">Sessions</a>&nbsp;|&nbsp;";
				$resultCount = $db->query
				("
					SELECT
						COUNT(*)
					FROM
						hlstats_Players_Awards
					WHERE
						hlstats_Players_Awards.playerId = $player
				");
				list($numawards) = $db->fetch_row($resultCount);
				echo "<a href=\"" . $g_options['scripturl'] . "?mode=playerawards&amp;player=$player\">Awards&nbsp;($numawards)</a>&nbsp;|&nbsp;";
				if ($g_options["nav_globalchat"] == 1)
				{
					echo "<a href=\"" . $g_options['scripturl'] . "?mode=chathistory&amp;player=$player\">Chat</a>";
				}
			?>
			<br />&nbsp;&nbsp;<a href="<?php echo $g_options['scripturl']; ?>?mode=search&amp;st=player&amp;q=<?php echo $pl_urlname; ?>"><img src="<?php echo IMAGE_PATH; ?>/search.gif" style="margin-left:3px;margin-right:3px;" alt="Search" />&nbsp;Find other players with the same name</a>
		</div>
	</div>
	<br /><br />
	<div style="clear:both;padding-top:24px;"></div>
	<?php printSectionTitle('Miscellaneous Statistics'); ?>
	<div class="subblock">
		<div style="float:left;vertical-align:top;width:48.5%;">
			<table class="data-table">
				<tr class="data-table-head">
					<td>Player Trend</td>
				</tr>
				<tr class="bg1">
					<td style="text-align:center;">
						<?php echo "<img src=\"trend_graph.php?bgcolor=".$g_options['graphbg_trend'].'&amp;color='.$g_options['graphtxt_trend']."&amp;player=$player\" alt=\"Player Trend Graph\" />"; ?>
					</td>
				</tr>
			</table>
		</div>
		<div style="float:right;vertical-align:top;width:48.5%;">
			<table class="data-table">
				<tr class="data-table-head">
					<td colspan="2">Forum Signature</td>
				</tr>
				<tr class="bg1">
					<td style="text-align:center;">
						<br /><br />
						<?php
							if ($g_options['modrewrite'] == 0)
							{
								$imglink = $siteurlneo.'sig.php?player_id='.$player.'&amp;background='.$g_options['sigbackground'];
								$jimglink = $siteurlneo.'sig.php?player_id='.$player.'&background='.$g_options['sigbackground'];
							}
							else
							{
								$imglink = $siteurlneo.'sig-'.$player.'-'.$g_options['sigbackground'].'.png';
								$jimglink = $imglink;
							}
							
							echo "<img src=\"$imglink\" title=\"Copy &amp; Paste the whole URL below in your forum signature\" alt=\"forum sig image\"/>";
							$script_path = (isset($_SERVER['SSL']) || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on")) ? 'https://' : 'http://';
							$script_path .= $_SERVER['HTTP_HOST'];
							$script_path .= str_replace('\\','/',dirname($_SERVER['PHP_SELF']));
							$script_path = preg_replace('/\/$/','',$script_path);
						?>
						<br /><br />
						<script type="text/javascript">
							/* <![CDATA[ */
							function setForumText(val)
							{
								var txtArea = document.getElementById('siglink');
								switch(val)
								{
									case 0:
										<?php echo "txtArea.value = '$jimglink'\n"; ?>
										break;	
									case 1:
										<?php echo "txtArea.value = '[url=$script_path/hlstats.php?mode=playerinfo&player=$player"."][img]$jimglink"."[/img][/url]'\n"; ?>
										break;
									case 2:
										<?php echo "txtArea.value = '[url=\"$script_path/hlstats.php?mode=playerinfo&player=$player\"][img]$jimglink"."[/img][/url]'\n"; ?>
										break;
								}
							}
							/* ]]> */
						</script>
						<a href="" onclick="setForumText(1);return false">
							bbCode 1 (phpBB, SMF)</a>&nbsp;|&nbsp;<a href="" onclick="setForumText(2);return false">bbCode 2 (IPB)</a>&nbsp;|&nbsp;<a href="" onclick="setForumText(0);return false">Direct Image
						</a>
						<?php echo '<textarea style="width: 95%; height: 50px;" rows="2" cols="70" id="siglink" readonly="readonly" onclick="document.getElementById(\'siglink\').select();">[url='."$script_path/hlstats.php?mode=playerinfo&amp;player=$player"."][img]$imglink".'[/img][/url]</textarea>'; ?>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<br /><br />
<?php
// Current rank & rank history
	$db->query
	("
		SELECT
			hlstats_Ranks.rankName,
			hlstats_Ranks.image,
			hlstats_Ranks.minKills
		FROM
			hlstats_Ranks
		WHERE
			hlstats_Ranks.minKills <= ".$playerdata['kills']."
			AND hlstats_Ranks.game = '$game'
		ORDER BY
			hlstats_Ranks.minKills DESC
		LIMIT
			1
	");
	$result = $db->fetch_array();
	$rankimage = getImage('/ranks/'.$result['image']);
	$rankName = $result['rankName'];
	$rankCurMinKills = $result['minKills']; 
	$db->query
	("
		SELECT
			hlstats_Ranks.rankName,
			hlstats_Ranks.minKills
		FROM
			hlstats_Ranks
		WHERE
			hlstats_Ranks.minKills > ".$playerdata['kills']."
			AND hlstats_Ranks.game = '$game'
		ORDER BY
			hlstats_Ranks.minKills
		LIMIT
			1
	");
	if ($db->num_rows() == 0)
	{
		$rankKillsNeeded = 0;
		$rankPercent = 0;
	}
	else
	{
		$result = $db->fetch_array();
		$rankKillsNeeded = $result['minKills'] - $playerdata['kills'];
		$rankPercent = ($playerdata['kills'] - $rankCurMinKills) * 100 / ($result['minKills'] - $rankCurMinKills);
	}
	$db->query
	("
		SELECT
			hlstats_Ranks.rankName,
			hlstats_Ranks.image
		FROM
			hlstats_Ranks
		WHERE
			hlstats_Ranks.minKills <= ".$playerdata['kills']."
			AND hlstats_Ranks.game = '$game'
		ORDER BY
			hlstats_Ranks.minKills
	");
	for ($i=1;$i<($db->num_rows());$i++)
	{
		$result = $db->fetch_array();
		$histimage = getImage('/ranks/'.$result['image'].'_small');
		$rankHistory .= '<img src="'.$histimage['url'].'" title="'.$result['rankName'].'" alt="'.$result['rankName'].'" /> ';
	} 
?>

	<div style="clear:both;padding-top:24px;"></div>
	<?php printSectionTitle('Ranks'); ?>
	<div class="subblock">
		<div style="float:left;vertical-align:top;width:48.5%;">
			<table class="data-table">
				<tr class="data-table-head">
					<td colspan="2">
						Current rank: <b><?php echo htmlspecialchars($rankName, ENT_COMPAT); ?></b>
					</td>
				</tr>
				<tr class="bg1">
					<td style="text-align:center;" colspan="2">
						<?php echo '<img src="'.$rankimage['url']."\" alt=\"$rankName\" title=\"$rankName\" />"; ?>
					</td>
				</tr>
				<tr class="data-table-head">
					<td style="width:60%;">
						<?php
							$cellbody = '<img src="' . IMAGE_PATH . '/bar6.gif" width="';
							if ($rankPercent < 1)
								$cellbody .= '1%';
							elseif ($rankPercent > 100)
								$cellbody .= '100%';
							else
								$cellbody .= sprintf('%d%%', $rankPercent + 0.5);
							$cellbody .= "\" style=\"height:10px;border:0;\" alt=\"$rankPercent%\" />";
							echo $cellbody;
						?>
					</td>
					<td style="width:40%;">
						Kills needed: <b><?php echo "$rankKillsNeeded (".number_format($rankPercent, 0, '.', '');?>%)</b>
					</td>
				</tr>
			</table>
		</div>
		<div style="float:right;vertical-align:top;width:48.5%;">
			<table class="data-table">
				<tr class="data-table-head">
					<td>Rank history</td>
				</tr>
				<tr class="bg1">
					<td style="text-align:center;"><?php echo $rankHistory; ?></td>
				</tr>
			</table>
		</div>
	</div>
	<br /><br />

<?php
// Awards
	$numawards = $db->query
	("
		SELECT
			hlstats_Ribbons.awardCode,
			hlstats_Ribbons.image
		FROM
			hlstats_Ribbons
		WHERE
			hlstats_Ribbons.game = '$game'
			AND
			(
				hlstats_Ribbons.special = 0
				OR hlstats_Ribbons.special = 2
			)
		GROUP BY
			hlstats_Ribbons.awardCode
	");
	$res = $db->query
	("
		SELECT
			hlstats_Ribbons.awardCode AS ribbonCode,
			hlstats_Ribbons.ribbonName AS ribbonName,
			IF(ISNULL(hlstats_Players_Ribbons.playerId), 'noaward.png', hlstats_Ribbons.image) AS image,
			hlstats_Ribbons.special,
			hlstats_Ribbons.image AS imagefile,
			hlstats_Ribbons.awardCount
		FROM
			hlstats_Ribbons
		LEFT JOIN
		(
			SELECT
				hlstats_Players_Ribbons.playerId,
				hlstats_Ribbons.awardCode,
				hlstats_Players_Ribbons.ribbonId
			FROM
				hlstats_Players_Ribbons
			INNER JOIN
				hlstats_Ribbons 
			ON
				hlstats_Ribbons.ribbonId = hlstats_Players_Ribbons.ribbonId
				AND hlstats_Ribbons.game = hlstats_Players_Ribbons.game 
			WHERE
				hlstats_Players_Ribbons.playerId = ".$playerdata['playerId']."
				AND hlstats_Players_Ribbons.game = '$game'
			ORDER BY
				hlstats_Ribbons.awardCount DESC
		) AS hlstats_Players_Ribbons
		ON
			hlstats_Players_Ribbons.ribbonId = hlstats_Ribbons.ribbonId
		WHERE
			hlstats_Ribbons.game = '$game'
			AND
			(
				ISNULL(hlstats_Players_Ribbons.playerId)
				OR hlstats_Players_Ribbons.playerId = ".$playerdata['playerId']."
			)
		ORDER BY
			hlstats_Ribbons.awardCode,
			hlstats_Players_Ribbons.playerId DESC,
			hlstats_Ribbons.special,
			hlstats_Ribbons.awardCount DESC
	");
	$ribbonList = '';
	$lastImage = '';
	$awards_done = array ();
	while ($result = $db->fetch_array($res))
	{
		$ribbonCode=$result['ribbonCode'];
		$ribbonName=$result['ribbonName'];
		if(!isset($awards_done[$ribbonCode]))
		{
			if (file_exists(IMAGE_PATH."/games/$game/ribbons/".$result['image']))
			{
				$image = IMAGE_PATH."/games/$game/ribbons/".$result['image'];
			}
			elseif (file_exists(IMAGE_PATH."/games/$realgame/ribbons/".$result['image']))
			{
				$image = IMAGE_PATH."/games/$realgame/ribbons/".$result['image'];
			}
			else
			{
				$image = IMAGE_PATH."/award.png";
			}		
			$ribbonList .= '<img src="'.$image.'" style="border:0px;" alt="'.$result['ribbonName'].'" title="'.$result["ribbonName"].'" /> ';
			$awards_done[$ribbonCode]=$ribbonCode;
		}
	}
	$awards = array ();
	$res = $db->query
	("
		SELECT
			hlstats_Awards.awardType,
			hlstats_Awards.code,
			hlstats_Awards.name
		FROM
			hlstats_Awards
		WHERE
			hlstats_Awards.game = '$game'
			AND hlstats_Awards.g_winner_id = $player
		ORDER BY
			hlstats_Awards.name;
	");
	while ($r1=$db->fetch_array())
	{
		unset($tmp);
		$tmp->aType = $r1['awardType'];
		$tmp->code = $r1['code'];
		$tmp->ribbonName = $r1['name'];
		if ($id == 0)
		{
			$tmp->playerName = $r1['lastname'];
			$tmp->flag = $r1['flag'];
			$tmp->playerId = $r1['g_winner_id'];
			$tmp->kills = $r1['g_winner_count'];
			$tmp->verb = $r1['verb'];
		}
		array_push($awards,$tmp); 
	}
	$GlobalAwardsList = '';
	foreach ($awards as $a)
	{
		if ($image = getImage("/games/$game/gawards/".strtolower($a->aType."_$a->code")))
		{
			$image = $image['url'];
		}
		elseif ($image = getImage("/games/$realgame/gawards/".strtolower($a->aType."_$a->code")))
		{
			$image = $image['url'];
		}
		else
		{
			$image = IMAGE_PATH."/award.png";
		}		
		$GlobalAwardsList .= "<img src=\"$image\" alt=\"$a->ribbonName\" title=\"$a->ribbonName\" /> ";
	}
	if ($ribbonList != '' || $GlobalAwardsList != '')
	{
?>

	<div style="clear:both;padding-top:24px;"></div>
	<?php printSectionTitle('Awards (hover over image to see name)'); ?>
	<div class="subblock">
		<div style="float:left;vertical-align:top;width:68.5%;">
			<table class="data-table">
				<tr class="data-table-head">
					<td>Ribbons</td>
				</tr>
				<tr class="bg1">
					<td style="text-align:center;"><?php echo $ribbonList; ?></td>
				</tr>
			</table>
		</div>
		<div style="float:right;vertical-align:top;width:28.5%;">
			<table class="data-table">
				<tr class="data-table-head">
					<td colspan="2">Global Awards</td>
				</tr>
				<tr class="bg1">
					<td style="text-align:center;"><?php echo $GlobalAwardsList; ?></td>
				</tr>
			</table>
		</div>
	</div>
	<br /><br />
<?php
	}
?> 
