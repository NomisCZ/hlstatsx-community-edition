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
     // Daily Awards - WeaponImages und auch als Icondarstellung

	$resultAwards = $db->query("
		SELECT
			hlstats_Awards.awardId,
			hlstats_Awards.awardType,
			hlstats_Awards.code,
			hlstats_Awards.name,
			hlstats_Awards.verb,
			hlstats_Awards.d_winner_id,
			hlstats_Awards.d_winner_count,
			hlstats_Players.lastName AS d_winner_name,
			hlstats_Players.flag AS flag,
			hlstats_Players.country AS country
		FROM
			hlstats_Awards
		LEFT JOIN hlstats_Players ON
			hlstats_Players.playerId = hlstats_Awards.d_winner_id
		WHERE
			hlstats_Awards.game='$game'
		ORDER BY
			hlstats_Awards.name
	");
	
	$result = $db->query("
		SELECT
			IFNULL(value, 1)
		FROM
			hlstats_Options
		WHERE
			keyname='awards_numdays'
	");

	if ($db->num_rows($result) == 1)
		list($awards_numdays) = $db->fetch_row($result);
	else
		$awards_numdays = 1;

	$result = $db->query("
		SELECT
			DATE_FORMAT(value, '%W %e %b'),
			DATE_FORMAT( DATE_SUB( value, INTERVAL $awards_numdays DAY ) , '%W %e %b' )
		FROM
			hlstats_Options
		WHERE
			keyname='awards_d_date'
	");
	list($awards_d_date, $awards_s_date) = $db->fetch_row($result);

?>
<div class="block">
	<?php printSectionTitle((($awards_numdays == 1) ? 'Daily' : $awards_numdays.'Day')." Awards ($awards_d_date)"); ?>
	<div class="subblock">
		<table class="data-table">

<?php
	// draw the daily award info table (5 columns)
	$i = 0;
	$cols = $g_options['awarddailycols'];
	if ($cols < 1 || $cols > 10)
	{
		$cols = 5;
	}
	$colwidth = round(100 / $cols);
	while ($r = $db->fetch_array($resultAwards))
	{
		if ($i == $cols)
		{
			echo '</tr>';
			$i = 0;
		}
		if ($i==0)
		{
			echo '<tr class="bg1">';
		}

		if ($image = getImage("/games/$game/dawards/".strtolower($r['awardType'].'_'.$r['code'])))
		{
			$img = $image['url'];
		}
		elseif ($image = getImage("/games/$realgame/dawards/".strtolower($r['awardType'].'_'.$r['code'])))
		{
			$img = $image['url'];
		}
		else
		{
			$img = IMAGE_PATH.'/award.png';
		}
		$weapon = '<a href="hlstats.php?mode=dailyawardinfo&amp;award='.$r['awardId']."&amp;game=$game\"><img src=\"$img\" alt=\"".$r['code'].'" /></a>';
		if ($r['d_winner_id'] > 0) {
			if ($g_options['countrydata'] == 1)	{
				$imagestring = '<img src="'.getFlag($r['flag']).'" alt="'.$r['flag'].'" />&nbsp;&nbsp;';
			} else {
				$imagestring = '';
			}
			$winnerstring = '<strong>'.htmlspecialchars($r['d_winner_name'], ENT_COMPAT).'</strong>';
			$achvd = "{$imagestring} <a href=\"hlstats.php?mode=playerinfo&amp;player={$r['d_winner_id']}&amp;game={$game}\">{$winnerstring}</a>";
			$wincount = $r['d_winner_count'];
		} else {
			$achvd = "<em>No Award Winner</em>";
			$wincount= "0";
		}
			
		echo "<td style=\"text-align:center;vertical-align:top;width:$colwidth%;\">
			<strong>".$r['name'].'</strong><br /><br />'
			."$weapon<br /><br />"
			."$achvd<br />"
			.'<span class="fSmall">'.$wincount. ' ' . htmlspecialchars($r['verb']).'</span>
			</td>';
		$i++;
	}
	if ($i != 0)
	{
		for ($i = $i; $i < $cols; $i++)
		{
			echo '<td class="bg1">&nbsp;</td>';
		}
		echo '</tr>';
	} 
  
?>
		</table>
	</div>
</div>