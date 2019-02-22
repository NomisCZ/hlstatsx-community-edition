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
// Role Statistics
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
		array ($gamename, 'Role Statistics'),
		array ($gamename => "%s?game=$game", 'Role Statistics' => '')
	);
	$result = $db->query
	("
		SELECT
			hlstats_Roles.code,
			hlstats_Roles.name
		FROM
			hlstats_Roles
		WHERE
			hlstats_Roles.game='$game'
	");
	while ($rowdata = $db->fetch_row($result))
	{ 
		$code = $rowdata[0];
		$fname[$code] = htmlspecialchars($rowdata[1]);
	}
	$tblRoles = new Table
	(
		array
		(
			new TableColumn
			(
				'code',
				'Role',
				'width=24&type=roleimg&align=left&link=' . urlencode("mode=rolesinfo&amp;role=%k&amp;game=$game"),
				$fname
			),
			new TableColumn
			(
				'picked',
				'Picked',
				'width=9&align=right&append=+times'
			),
			new TableColumn
			(
				'ppercent',
				'%',
				'width=6&align=right&append=' . urlencode('%')
			),
			new TableColumn
			(
				'ppercent',
				'Ratio',
				'width=9&sort=no&type=bargraph'
			),
			new TableColumn
			(
				'kills',
				'Kills',
				'width=6&align=right'
			),
			new TableColumn
			(
				'kpercent',
				'%',
				'width=6&align=right&append=' . urlencode('%')
			),
			new TableColumn
			(
				'kpercent',
				'Ratio',
				'width=9&sort=no&type=bargraph'
			),
			new TableColumn
			(
				'deaths',
				'Deaths',
				'width=6&align=right'
			),
			new TableColumn
			(
				'dpercent',
				'%',
				'width=6&sort=no&align=right&append=' . urlencode('%')
			),
			new TableColumn
			(
				'dpercent',
				'Ratio',
				'width=9&sort=no&type=bargraph'
			),
			new TableColumn
			(
				'kpd',
				'K:D',
				'width=5&align=right'
			)
		),
		'code',
		'kills',
		'name',
		true,
		9999,
		'role_page',
		'role_sort',
		'role_sortorder'
	);
	$db->query
	("
		SELECT
			IF(IFNULL(SUM(hlstats_Roles.kills), 0) = 0, 1, SUM(hlstats_Roles.kills)),
			IF(IFNULL(SUM(hlstats_Roles.deaths), 0) = 0, 1, SUM(hlstats_Roles.deaths)),
			IF(IFNULL(SUM(hlstats_Roles.picked), 0) = 0, 1, SUM(hlstats_Roles.picked))
		FROM
			hlstats_Roles
		WHERE
			hlstats_Roles.game = '$game'
			AND hlstats_Roles.hidden = '0'
	");
	list($realkills, $realdeaths, $realpicked) = $db->fetch_row();
	$result = $db->query
	("
		SELECT
			hlstats_Roles.code,
			hlstats_Roles.name,
			hlstats_Roles.picked,
			ROUND(hlstats_Roles.picked / $realpicked * 100, 2) AS ppercent,
			hlstats_Roles.kills,
			ROUND(hlstats_Roles.kills / $realkills * 100, 2) AS kpercent,
			hlstats_Roles.deaths,
			ROUND(hlstats_Roles.deaths / $realdeaths * 100, 2) AS dpercent,
			ROUND(hlstats_Roles.kills / IF(hlstats_Roles.deaths = 0, 1, hlstats_Roles.deaths), 2) AS kpd
		FROM
			hlstats_Roles
		WHERE
			hlstats_Roles.game = '$game' 
			AND hlstats_Roles.kills > 0 
			AND hlstats_Roles.hidden = '0'
		GROUP BY
			hlstats_Roles.roleId
		ORDER BY
			$tblRoles->sort $tblRoles->sortorder,
			$tblRoles->sort2 $tblRoles->sortorder
	");
?>

<div class="block">
	<?php printSectionTitle('Role Statistics'); ?>
	<div class="subblock">
		From a total of <strong><?php echo number_format($realkills); ?></strong> kills with <strong><?php echo number_format($realdeaths); ?></strong> deaths
	</div>
	<br /><br />
	<?php $tblRoles->draw($result, $db->num_rows($result), 95); ?><br /><br />
	<div class="subblock">
		<div style="float:right;">
			Go to: <a href="<?php echo $g_options['scripturl']."?game=$game"; ?>"><?php echo $gamename; ?></a>
		</div>
	</div>
</div>