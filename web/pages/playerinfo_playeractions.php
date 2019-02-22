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
	flush();
	$tblPlayerActions = new Table
	(
		array(
			new TableColumn
			(
				'description',
				'Action',
				'width=45&link=' . urlencode("mode=actioninfo&amp;action=%k&amp;game=$game")
			),
			new TableColumn
			(
				'obj_count',
				'Earned',
				'width=25&align=right&append=+times'
			),
			new TableColumn
			(
				'obj_bonus',
				'Accumulated Points',
				'width=25&align=right'
			)
		),
		'code',
		'obj_count',
		'description',
		true,
		9999,
		'obj_page',
		'obj_sort',
		'obj_sortorder',
		'tabteams',
		'desc',
		true
	);
	$result = $db->query
	("
		(
		SELECT
			hlstats_Actions.code,
			hlstats_Actions.description,
			COUNT(hlstats_Events_PlayerActions.id) AS obj_count,
			SUM(hlstats_Events_PlayerActions.bonus) AS obj_bonus
		FROM
			hlstats_Actions
		LEFT JOIN
			hlstats_Events_PlayerActions
		ON
			hlstats_Events_PlayerActions.actionId = hlstats_Actions.id
		WHERE
			hlstats_Events_PlayerActions.playerId = $player
		GROUP BY
			hlstats_Actions.id
		)
		UNION ALL
		(
		SELECT
			hlstats_Actions.code,
			hlstats_Actions.description,
			COUNT(hlstats_Events_PlayerPlayerActions.id) AS obj_count,
			SUM(hlstats_Events_PlayerPlayerActions.bonus) AS obj_bonus
		FROM
			hlstats_Actions
		LEFT JOIN
			hlstats_Events_PlayerPlayerActions
		ON
			hlstats_Events_PlayerPlayerActions.actionId = hlstats_Actions.id
		WHERE
			hlstats_Events_PlayerPlayerActions.playerId = $player
		GROUP BY
			hlstats_Actions.id
		)
		ORDER BY
			$tblPlayerActions->sort $tblPlayerActions->sortorder,
			$tblPlayerActions->sort2 $tblPlayerActions->sortorder
	");
	$numitems = $db->num_rows($result);
	if ($numitems > 0)
	{
?>
		<div style="clear:both;padding-top:20px;"></div>
<?php
		printSectionTitle('Player Actions *');
		$tblPlayerActions->draw($result, $numitems, 95);
?>
		<br /><br />
<?php
	}
	$tblPlayerPlayerActionsV = new Table
	(
		array
		(
			new TableColumn
			(
				'description',
				'Action',
				'width=45&link=' . urlencode("mode=actioninfo&amp;action=%k&amp;game=$game#victims")
			),
			new TableColumn
			(
				'obj_count',
				'Earned Against',
				'width=25&align=right&append=+times'
			),
			new TableColumn
			(
				'obj_bonus',
				'Accumulated Points',
				'width=25&align=right'
			)
		),
		'code',
		'obj_count',
		'description',
		true,
		9999,
		'ppa_page',
		'ppa_sort',
		'ppa_sortorder',
		'tabteams',
		'desc',
		true
	);
	$result = $db->query
	("
		SELECT
			hlstats_Actions.code,
			hlstats_Actions.description,
			COUNT(hlstats_Events_PlayerPlayerActions.id) AS obj_count,
			SUM(hlstats_Events_PlayerPlayerActions.bonus) * -1 AS obj_bonus
		FROM
			hlstats_Actions
		LEFT JOIN
			hlstats_Events_PlayerPlayerActions
		ON
			hlstats_Events_PlayerPlayerActions.actionId = hlstats_Actions.id
		WHERE
			hlstats_Events_PlayerPlayerActions.victimId = $player
		GROUP BY
			hlstats_Actions.id
		ORDER BY
			$tblPlayerPlayerActionsV->sort $tblPlayerPlayerActionsV->sortorder,
			$tblPlayerPlayerActionsV->sort2 $tblPlayerPlayerActionsV->sortorder
	");
	$numitemsv = $db->num_rows($result);
	if ($numitemsv > 0)
	{
		if ($numitems == 0)
		{
?>
		<div style="clear:both;padding-top:20px;"></div>
<?php
		}
		
		printSectionTitle('Victims of Player-Player Actions *');
		$tblPlayerPlayerActionsV->draw($result, $numitems, 95);
?>
		<br /><br />
<?php
	}
?>
