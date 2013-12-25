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
		
	// Player Rankings
	$db->query("SELECT name FROM hlstats_Games WHERE code='$game'");
	if ($db->num_rows() < 1)
		error("No such game '$game'.");
	
	list($gamename) = $db->fetch_row();
	$db->free_result();
	
	$minkills = 1;
	
	if ($g_options['rankingtype'] != 'kills')
	{
		$table = new Table
		(
			array
			(
				new TableColumn
				(
					'lastName',
					'Player',
					'width=30&flag=1&link=' . urlencode('mode=statsme&amp;player=%k')
				),
				new TableColumn
				(
					'skill',
					'Points',
					'width=7&align=right&skill_change=1'
				),
				new TableColumn
				(
					'activity',
					'Activity',
					'width=10&sort=no&type=bargraph'
				),
				new TableColumn
				(
					'connection_time',
					'Connection Time',
					'width=10&align=right&type=timestamp'
				),
				new TableColumn
				(
					'kills',
					'Kills',
					'width=7&align=right'
				),
				new TableColumn
				(
					'deaths',
					'Deaths',
					'width=7&align=right'
				),
				new TableColumn
				(
					'kpd',
					'K:D',
					'width=6&align=right'
				),
				new TableColumn
				(
					'headshots',
					'Headshots',
					'width=6&align=right'
				),
				new TableColumn
				(
					'hpk',
					'HS:K',
					'width=6&align=right'
				),
				new TableColumn
				(
					'acc',
					'Accuracy',
					'width=6&align=right&append=' . urlencode('%')
				)
			),
			'playerId',
			$g_options['rankingtype'],
			'kpd',
			true
		);
	}
	else
	{
		$table = new Table
		(
			array
			(
				new TableColumn
				(
					'lastName',
					'Player',
					'width=30&flag=1&link=' . urlencode('mode=statsme&amp;player=%k')
				),
				new TableColumn
				(
					'activity',
					'Activity',
					'width=10&sort=no&type=bargraph'
					),
				new TableColumn
				(
					'kills',
					'Kills',
					'width=7&align=right'
				),
				new TableColumn
				(
					'deaths',
					'Deaths',
					'width=7&align=right'
				),
				new TableColumn
				(
					'kpd',
					'K:D',
					'width=6&align=right'
				),
				new TableColumn
				(
					'headshots',
					'Headshots',
					'width=6&align=right'
				),
				new TableColumn
				(
					'hpk',
					'HS:K',
					'width=6&align=right'
				),
				new TableColumn
				(
					'acc',
					'Accuracy',
					'width=6&align=right&append=' . urlencode('%')
				),
				new TableColumn
				(
					'skill',
					'Points',
					'width=7&align=right&skill_change=1'
				),
				new TableColumn
				(
					'connection_time',
					'Connection Time',
					'width=10&align=right&type=timestamp'
				)
			),
		'playerId',
		$g_options['rankingtype'],
		'kpd',
		true
		);
	}
	
	$day_interval = 28;  
	$result = $db->query("
		SELECT
			playerId,
			connection_time,
			lastName,
			flag,
			country,
			skill,
			kills,
			deaths,
			IFNULL(kills/deaths, '-') AS kpd,
			headshots,
			IFNULL(headshots/kills, '-') AS hpk,
			IFNULL(ROUND((hits / shots * 100), 1), 0.0) AS acc,
			activity,
			last_skill_change
		FROM
			hlstats_Players
		WHERE
			game='$game'
			AND hideranking=0
			AND kills >= $minkills
		ORDER BY
			$table->sort $table->sortorder,
			$table->sort2 $table->sortorder,
			lastName ASC
		LIMIT
			$table->startitem,
			$table->numperpage
	");
	
	$resultCount = $db->query("
		SELECT
			COUNT(*)
		FROM
			hlstats_Players
		WHERE
			game='$game'
			AND hideranking=0
			AND kills >= $minkills
	");
	
	list($numitems) = $db->fetch_row($resultCount);
	
	$table->draw($result, 25, 100);
?>
