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
	// Action Details

	// Addon created by Rufus (rufus@nonstuff.de)
	
	$action =  valid_request($_GET['action'], 0)
		or error('No action ID specified.');

	$action_escaped=$db->escape($action);
	$game_escaped=$db->escape($game);
	
	$db->query("
		SELECT
			for_PlayerActions,for_PlayerPlayerActions, description
		FROM
			hlstats_Actions
		WHERE
			code='{$action_escaped}'
			AND game='{$game_escaped}'
	");
	
	if ($db->num_rows() != 1)
	{
		$act_name = ucfirst($action);
		$actiondata['for_PlayerActions']=1; // dummy these out, this should never happen?
		$actiondata['for_PlayerPlayerActions']=0;
	}
	else
	{
		$actiondata = $db->fetch_array();
		$db->free_result();
		$act_name = $actiondata['description'];
	}
	
	$db->query("SELECT name FROM hlstats_Games WHERE code='{$game_escaped}'");
	if ($db->num_rows() != 1)
		error('Invalid or no game specified.');
	else
		list($gamename) = $db->fetch_row();
		
	pageHeader(
		array($gamename, 'Action Details', $act_name),
		array(
			$gamename=>$g_options['scripturl'] . "?game=$game",
			'Action Statistics'=>$g_options['scripturl'] . "?mode=actions&game=$game",
			'Action Details'=>''
		),
		$act_name
	);
    

	$table = new Table(
		array(
			new TableColumn(
				'playerName',
				'Player',
				'width=45&align=left&flag=1&link=' . urlencode('mode=playerinfo&amp;player=%k') 
			),
			new TableColumn(
				'obj_count',
				'Achieved',
				'width=25&align=right'
			),
			new TableColumn(
				'obj_bonus',
				'Skill Bonus Total',
				'width=25&align=right&sort=no'
			)
		),
		'playerId',
		'obj_count',
		'playerName',
		true,
		40
	);

	if ($actiondata['for_PlayerActions']==1)
	{	
		$result = $db->query("
			SELECT
				hlstats_Events_PlayerActions.playerId,
				hlstats_Players.lastName AS playerName,
				hlstats_Players.flag as flag,
				COUNT(hlstats_Events_PlayerActions.id) AS obj_count,
				COUNT(hlstats_Events_PlayerActions.id) * hlstats_Actions.reward_player AS obj_bonus
			FROM
				hlstats_Events_PlayerActions, hlstats_Players, hlstats_Actions
			WHERE
				hlstats_Actions.code = '{$action_escaped}' AND
				hlstats_Players.game = '{$game_escaped}' AND
				hlstats_Players.playerId = hlstats_Events_PlayerActions.playerId AND
				hlstats_Events_PlayerActions.actionId = hlstats_Actions.id AND
				hlstats_Players.hideranking = '0'
			GROUP BY
				hlstats_Events_PlayerActions.playerId
			ORDER BY
				$table->sort $table->sortorder,
				$table->sort2 $table->sortorder
			LIMIT $table->startitem,$table->numperpage
		");
		
		$resultCount = $db->query("
			SELECT
				COUNT(DISTINCT hlstats_Events_PlayerActions.playerId),
				COUNT(hlstats_Events_PlayerActions.Id)
			FROM
				hlstats_Events_PlayerActions, hlstats_Players, hlstats_Actions
			WHERE
				hlstats_Actions.code = '{$action_escaped}' AND
				hlstats_Players.game = '{$game_escaped}' AND
				hlstats_Players.playerId = hlstats_Events_PlayerActions.playerId AND
				hlstats_Events_PlayerActions.actionId = hlstats_Actions.id AND
				hlstats_Players.hideranking = '0'
		");
	}
	if($actiondata['for_PlayerPlayerActions']==1)
	{
		$result = $db->query("
			SELECT
				hlstats_Events_PlayerPlayerActions.playerId,
				hlstats_Players.lastName AS playerName,
				hlstats_Players.flag as flag,
				COUNT(hlstats_Events_PlayerPlayerActions.id) AS obj_count,
				COUNT(hlstats_Events_PlayerPlayerActions.id) * hlstats_Actions.reward_player AS obj_bonus
			FROM
				hlstats_Events_PlayerPlayerActions, hlstats_Players, hlstats_Actions
			WHERE
				hlstats_Actions.code = '{$action_escaped}' AND
				hlstats_Players.game = '{$game_escaped}' AND
				hlstats_Players.playerId = hlstats_Events_PlayerPlayerActions.playerId AND
				hlstats_Events_PlayerPlayerActions.actionId = hlstats_Actions.id AND
				hlstats_Players.hideranking = '0'
			GROUP BY
				hlstats_Events_PlayerPlayerActions.playerId
			ORDER BY
				$table->sort $table->sortorder,
				$table->sort2 $table->sortorder
			LIMIT $table->startitem,$table->numperpage
		");
	
		$resultCount = $db->query("
			SELECT
				COUNT(DISTINCT hlstats_Events_PlayerPlayerActions.playerId),
				COUNT(hlstats_Events_PlayerPlayerActions.Id)
			FROM
				hlstats_Events_PlayerPlayerActions, hlstats_Players, hlstats_Actions
			WHERE
				hlstats_Actions.code = '{$action_escaped}' AND
				hlstats_Players.game = '{$game_escaped}' AND
				hlstats_Players.playerId = hlstats_Events_PlayerPlayerActions.playerId AND
				hlstats_Events_PlayerPlayerActions.actionId = hlstats_Actions.id AND
				hlstats_Players.hideranking = '0'
		");
	}	
		
	list($numitems, $totalact) = $db->fetch_row($resultCount);
  
	if ($totalact == 0)
	{
		$result = $db->query("
			SELECT
				hlstats_Events_TeamBonuses.playerId,
				hlstats_Players.lastName AS playerName,
				hlstats_Players.flag as flag,
				COUNT(hlstats_Events_TeamBonuses.id) AS obj_count,
				COUNT(hlstats_Events_TeamBonuses.id) * hlstats_Actions.reward_player AS obj_bonus
			FROM
				hlstats_Events_TeamBonuses, hlstats_Players, hlstats_Actions
			WHERE
				hlstats_Actions.code = '{$action_escaped}' AND
				hlstats_Players.game = '{$game_escaped}' AND
				hlstats_Players.playerId = hlstats_Events_TeamBonuses.playerId AND
				hlstats_Events_TeamBonuses.actionId = hlstats_Actions.id AND
				hlstats_Players.hideranking = '0'
			GROUP BY
				hlstats_Events_TeamBonuses.playerId
			ORDER BY
				$table->sort $table->sortorder,
				$table->sort2 $table->sortorder
			LIMIT $table->startitem,$table->numperpage
		");
    
		$resultCount = $db->query("
			SELECT
				COUNT(DISTINCT hlstats_Events_TeamBonuses.playerId),
				COUNT(hlstats_Events_TeamBonuses.Id)
			FROM
				hlstats_Events_TeamBonuses, hlstats_Players, hlstats_Actions
			WHERE
				hlstats_Actions.code = '{$action_escaped}' AND
				hlstats_Players.game = '{$game_escaped}' AND
				hlstats_Players.playerId = hlstats_Events_TeamBonuses.playerId AND
				hlstats_Events_TeamBonuses.actionId = hlstats_Actions.id AND
				hlstats_Players.hideranking = '0'
		");
		list($numitems, $totalact) = $db->fetch_row($resultCount);    
	}
?>
<div class="block">
	<?php printSectionTitle('Action Details'); ?>

	<div class="subblock">
		<div style="float:left;">
			<strong><?php echo $act_name; ?></strong> from a total of <strong><?php echo number_format(intval($totalact)); ?></strong> achievements (Last <?php echo $g_options['DeleteDays']; ?> Days)
		</div>
		<div style="float:right;">
			Back to <a href="<?php echo $g_options['scripturl'] . "?mode=actions&amp;game=$game"; ?>">Action Statistics</a>
		</div>
	</div>
	<div style="clear:both;padding:2px;"></div>
</div>
<?php
	$table->draw($result, $numitems, 95, 'center');

	if ($actiondata['for_PlayerPlayerActions'] == 1)
	{
		$table = new Table(
		array(
			new TableColumn(
				'playerName',
				'Player',
				'width=45&align=left&flag=1&link=' . urlencode('mode=playerinfo&amp;player=%k') 
			),
			new TableColumn(
				'obj_count',
				'Times Victimized',
				'width=25&align=right'
			),
			new TableColumn(
				'obj_bonus',
				'Skill Bonus Total',
				'width=25&align=right&sort=no'
			)
		),
		'victimId',
		'obj_count',
		'playerName',
		true,
		40,
		'vpage'
		);
	
		$result = $db->query("
			SELECT
				hlstats_Events_PlayerPlayerActions.victimId,
				hlstats_Players.lastName AS playerName,
				hlstats_Players.flag as flag,
				COUNT(hlstats_Events_PlayerPlayerActions.id) AS obj_count,
				COUNT(hlstats_Events_PlayerPlayerActions.id) * hlstats_Actions.reward_player * -1 AS obj_bonus
			FROM
				hlstats_Events_PlayerPlayerActions, hlstats_Players, hlstats_Actions
			WHERE
				hlstats_Actions.code = '{$action_escaped}' AND
				hlstats_Players.game = '{$game_escaped}' AND
				hlstats_Players.playerId = hlstats_Events_PlayerPlayerActions.victimId AND
				hlstats_Events_PlayerPlayerActions.actionId = hlstats_Actions.id AND
				hlstats_Players.hideranking = '0'
			GROUP BY
				hlstats_Events_PlayerPlayerActions.victimId
			ORDER BY
				$table->sort $table->sortorder,
				$table->sort2 $table->sortorder
			LIMIT $table->startitem,$table->numperpage
		");
	
		$resultCount = $db->query("
			SELECT
				COUNT(DISTINCT hlstats_Events_PlayerPlayerActions.victimId),
				COUNT(hlstats_Events_PlayerPlayerActions.Id)
			FROM
				hlstats_Events_PlayerPlayerActions, hlstats_Players, hlstats_Actions
			WHERE
				hlstats_Actions.code = '{$action_escaped}' AND
				hlstats_Players.game = '{$game_escaped}' AND
				hlstats_Players.playerId = hlstats_Events_PlayerPlayerActions.victimId AND
				hlstats_Events_PlayerPlayerActions.actionId = hlstats_Actions.id AND
				hlstats_Players.hideranking = '0'
		");
	
		list($numitems, $totalact) = $db->fetch_row($resultCount);
?>
<div class="block">
	<a name="victims"><?php printSectionTitle("Action Victim Details"); ?></a>
	<div class="subblock">
		<div style="float:left;">
			<strong>Victims of <?php echo $act_name; ?></strong> (Last <?php echo $g_options['DeleteDays']; ?> Days)
		</div>
	</div>
	<div style="clear:both;padding:2px;"></div>
</div>
<?php		
		$table->draw($result, $numitems, 95, 'center');
	}
?>
</div>
