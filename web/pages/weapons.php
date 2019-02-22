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
// Weapon Statistics
	$db->query
	("
		SELECT
			hlstats_Games.name
		FROM
			hlstats_Games
		WHERE
			hlstats_Games.code = '$game'
	");
	if ($db->num_rows() < 1) error("No such game '$game'.");
	list($gamename) = $db->fetch_row();
	$db->free_result();
	pageHeader
	(
		array ($gamename, 'Weapon Statistics'),
		array ($gamename => "%s?game=$game", 'Weapon Statistics' => '')
	);
	$result = $db->query
	("
		SELECT
			hlstats_Weapons.code,
			hlstats_Weapons.name
		FROM
			hlstats_Weapons
		WHERE
			hlstats_Weapons.game = '$game'
	");
	while ($rowdata = $db->fetch_row($result))
	{ 
		$code = $rowdata[0];
		$fname[$code] = $rowdata[1];
	}
	$tblWeapons = new Table
	(
		array
		(
			new TableColumn
			(
				'weapon',
				'Weapon',
				'width=20&type=weaponimg&align=center&link=' . urlencode("mode=weaponinfo&amp;weapon=%k&amp;game=$game"),
				$fname
			),
			new TableColumn
			(
				'modifier',
				'Modifier',
				'width=8&align=right'
			),
			new TableColumn
			(
				'kills',
				'Kills',
				'width=8&align=right'
			),
			new TableColumn
			(
				'kpercent',
				'%',
				'width=5&sort=no&align=right&append=' . urlencode('%')
			),
			new TableColumn
			(
				'kpercent',
				'Ratio',
				'width=18&sort=no&type=bargraph'
			),
			new TableColumn
			(
				'headshots',
				'Headshots',
				'width=8&align=right'
			),
			new TableColumn
			(
				'hpercent',
				'%',
				'width=5&sort=no&align=right&append=' . urlencode('%')
			),
			new TableColumn
			(
				'hpercent',
				'Ratio',
				'width=18&sort=no&type=bargraph'
			),
			new TableColumn
			(
				'hpk',
				'HS:K',
				'width=5&align=right'
			)
			
		),
		'weapon',
		'kills',
		'weapon',
		true,
		9999,
		'weap_page',
		'weap_sort',
		'weap_sortorder'
	);
	$db->query
	("
		SELECT
			IF(IFNULL(SUM(hlstats_Weapons.kills), 0) = 0, 1, SUM(hlstats_Weapons.kills)),
			IF(IFNULL(SUM(hlstats_Weapons.headshots), 0) = 0, 1, SUM(hlstats_Weapons.headshots))
		FROM
			hlstats_Weapons
		WHERE
			hlstats_Weapons.game = '$game'
	");
	list($realkills, $realheadshots) = $db->fetch_row();
	$result = $db->query
	("
		SELECT
			hlstats_Weapons.code AS weapon,
			hlstats_Weapons.kills,
			ROUND(hlstats_Weapons.kills / ".(($realkills==0)?1:$realkills)." * 100, 2) AS kpercent,
			hlstats_Weapons.headshots,
			ROUND(hlstats_Weapons.headshots / IF(hlstats_Weapons.kills = 0, 1, hlstats_Weapons.kills), 2) AS hpk,
			ROUND(hlstats_Weapons.headshots / ".(($realheadshots==0)?1:$realheadshots)." * 100, 2) AS hpercent,
			hlstats_Weapons.modifier
		FROM
			hlstats_Weapons
		WHERE
			hlstats_Weapons.game = '$game'
			AND hlstats_Weapons.kills > 0 
		GROUP BY
			hlstats_Weapons.weaponId
		ORDER BY
			$tblWeapons->sort $tblWeapons->sortorder,
			$tblWeapons->sort2 $tblWeapons->sortorder
	");
?>

<div class="block">
	<?php printSectionTitle('Weapon Statistics'); ?>
	<div class="subblock">
		From a total of <strong><?php echo number_format($realkills); ?></strong> kills with <strong><?php echo number_format($realheadshots); ?></strong> headshots
	</div>
	<br /><br />
	<?php $tblWeapons->draw($result, $db->num_rows($result), 95); ?><br /><br />
	<div class="subblock">
		<div style="float:right;">
			Go to: <a href="<?php echo $g_options["scripturl"] . "?game=$game"; ?>"><?php echo $gamename; ?></a>
		</div>
	</div>
</div>