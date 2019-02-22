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

	// select the available ribbons
	$result = $db->query("
		SELECT
			hlstats_Ribbons.ribbonId,
			ribbonName,
			image,
			name as awardName,
			awardCount,
			count(playerId) as achievedcount
		FROM
			hlstats_Ribbons	
		INNER JOIN
			hlstats_Awards 
		ON (
			awardCode=code
			AND hlstats_Ribbons.game=hlstats_Awards.game			    
			)
		LEFT JOIN
			hlstats_Players_Ribbons
		ON (
			hlstats_Ribbons.ribbonId=hlstats_Players_Ribbons.ribbonId
			)	    
		WHERE
			hlstats_Ribbons.game='$game'
			AND hlstats_Ribbons.special=0
		GROUP BY
			hlstats_Ribbons.ribbonId
		ORDER BY
			awardCount,
			ribbonName,
			awardCode
	");
?>

<div class="block">
	<?php printSectionTitle('Ribbons'); ?>
	<div class="subblock">
		<table class="data-table">
<?php
	// draw the rank info table (5 columns)
	$i = 0;
	$i1 = 0;
	$cnt = -1;
 
	$cols = $g_options['awardribbonscols'];
	if ($cols < 1 || $cols > 10)
	{
		$cols = 5;
	}
	$colwidth = round(100 / $cols);
 
	while ($r = $db->fetch_array())
	{
		if ($cnt != $r['awardCount'])
		{
			$cnt = $r['awardCount'];
			$i1++;
			if ($i == $cols)
			{
				echo '</tr>';
			}
			$i = 0;
			echo "<tr class=\"head\"><td colspan=\"5\"><strong>Ribbon Class #$i1 ($cnt awards required)</strong></td></tr>";
		}

		if ($i == $cols)
		{
			echo '</tr>';
			$i = 0;
		}
		if ($i == 0)
		{
			echo '<tr class="bg1">';
		}
   
		$link = '<a href="hlstats.php?mode=ribboninfo&amp;ribbon='.$r['ribbonId']."&amp;game=$game\">";
		if (file_exists(IMAGE_PATH."/games/$game/ribbons/".$r['image']))
		{
			$image = IMAGE_PATH."/games/$game/ribbons/".$r['image'];
		}
		elseif (file_exists(IMAGE_PATH."/games/$realgame/ribbons/".$r['image']))
		{
			$image = IMAGE_PATH."/games/$realgame/ribbons/".$r['image'];
		}
		else
		{
			$image = IMAGE_PATH."/award.png";
		}
		$image = '<img src="'.$image.'" alt="'.$r['ribbonName'].'" />';
		$achvd = '';
		if ($r['achievedcount'] > 0)
		{
			$image = "$link$image</a>";
			$achvd = 'Achieved by '.$r['achievedcount'].' players';
		}

		echo "<td style=\"text-align:center;vertical-align:top;width:$colwidth%;\">
			<strong>".$r['ribbonName'].'</strong><br /><br /><span class="fSmall">'
			."$achvd</span><br />$image
			</td>";
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