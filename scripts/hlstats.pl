#!/usr/bin/perl
# HLstatsX Community Edition - Real-time player and clan rankings and statistics
# Copyleft (L) 2008-20XX Nicholas Hastings (nshastings@gmail.com)
# http://www.hlxcommunity.com
#
# HLstatsX Community Edition is a continuation of 
# ELstatsNEO - Real-time player and clan rankings and statistics
# Copyleft (L) 2008-20XX Malte Bayer (steam@neo-soft.org)
# http://ovrsized.neo-soft.org/
# 
# ELstatsNEO is an very improved & enhanced - so called Ultra-Humongus Edition of HLstatsX
# HLstatsX - Real-time player and clan rankings and statistics for Half-Life 2
# http://www.hlstatsx.com/
# Copyright (C) 2005-2007 Tobias Oetzel (Tobi@hlstatsx.com)
#
# HLstatsX is an enhanced version of HLstats made by Simon Garner
# HLstats - Real-time player and clan rankings and statistics for Half-Life
# http://sourceforge.net/projects/hlstats/
# Copyright (C) 2001  Simon Garner
#             
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
# 
# For support and installation notes visit http://www.hlxcommunity.com

use strict;
no strict 'vars';

$SIG{HUP} = 'HUP_handler';
$SIG{INT} = 'INT_handler';  # unix
$SIG{INT2} = 'INT_handler';  # windows

##
## Settings
##

# $opt_configfile - Absolute path and filename of configuration file.
$opt_configfile = "./hlstats.conf";

# $opt_libdir - Directory to look in for local required files
#               (our *.plib, *.pm files).
$opt_libdir = "./";


##
##
################################################################################
## No need to edit below this line
##

use Getopt::Long;
use Time::Local;
use IO::Socket;
use IO::Select;
use DBI;
use Digest::MD5;
use Encode;
use bytes;

require "$opt_libdir/ConfigReaderSimple.pm";
require "$opt_libdir/TRcon.pm";
require "$opt_libdir/BASTARDrcon.pm";
require "$opt_libdir/HLstats_Server.pm";
require "$opt_libdir/HLstats_Player.pm";
require "$opt_libdir/HLstats_Game.pm";
do "$opt_libdir/HLstats_GameConstants.plib";
do "$opt_libdir/HLstats.plib";
do "$opt_libdir/HLstats_EventHandlers.plib";

$|=1;
Getopt::Long::Configure ("bundling");

$last_trend_timestamp = 0;

binmode STDIN, ":utf8";
binmode STDOUT, ":utf8";

##
## Functions
##

sub lookupPlayer
{
	my ($saddr, $id, $uniqueid) = @_;
	if (defined($g_servers{$saddr}->{"srv_players"}->{"$id/$uniqueid"}))
	{
		return $g_servers{$saddr}->{"srv_players"}->{"$id/$uniqueid"};
	}
	return undef;
}

sub removePlayer
{
	my ($saddr, $id, $uniqueid, $dontUpdateCount) = @_;
	my $deleteplayer = 0;
	if(defined($g_servers{$saddr}->{"srv_players"}->{"$id/$uniqueid"}))
	{
		$deleteplayer = 1;
	}
	else
	{
		&::printEvent("400", "Bad attempted delete ($saddr) ($id/$uniqueid)");
	}

	if ($deleteplayer == 1) {
		$g_servers{$saddr}->{"srv_players"}->{"$id/$uniqueid"}->playerCleanup();
		delete($g_servers{$saddr}->{"srv_players"}->{"$id/$uniqueid"});
		if (!$dontUpdateCount)  # double negative, i know...
		{
			$g_servers{$saddr}->updatePlayerCount();
		}
	}
}

sub checkBonusRound
{
   if ($g_servers{$s_addr}->{bonusroundtime} > 0 && ($::ev_remotetime > ($g_servers{$s_addr}->{bonusroundtime_ts} + $g_servers{$s_addr}->{bonusroundtime}))) {
		if ($g_servers{$s_addr}->{bonusroundtime_state} == 1) {
			&printEvent("SERVER", "Bonus Round Expired",1);
		}
		$g_servers{$s_addr}->set("bonusroundtime_state",0);
	}
	
	if($g_servers{$s_addr}->{bonusroundignore} == 1 && $g_servers{$s_addr}->{bonusroundtime_state} == 1) {
		return 1;
	}
	return 0;
}

sub is_number ($) { ( $_[0] ^ $_[0] ) eq '0' }


#
# void printNotice (string notice)
#
# Prins a debugging notice to stdout.
#

sub printNotice
{
	my ($notice) = @_;
	
	if ($g_debug > 1) {
		print ">> $notice\n";
	}
}

sub track_hlstats_trend
{
	if ($last_trend_timestamp > 0) {
		if ($last_trend_timestamp+299 < $ev_daemontime) {
			my $query = "
				SELECT 
					COUNT(*),
					a.game
				FROM
					hlstats_Players a
				INNER JOIN
					(
						SELECT
							game
						FROM
							hlstats_Servers
						GROUP BY
							game
					) AS b 
				ON
					a.game = b.game
				GROUP BY
					a.game
			";
			my $result = &execCached("get_total_player_counts", $query);
			my $insvalues = "";
			while ( my($total_players, $game) = $result->fetchrow_array) {
				my $query = "
					SELECT
						SUM(kills),
						SUM(headshots),
						COUNT(serverId),
						SUM(act_players),
						SUM(max_players)
					FROM
						hlstats_Servers
					WHERE
						game=?
				";
				my $data = &execCached("get_game_stat_counts", $query, &quoteSQL($game));
				my ($total_kills, $total_headshots, $total_servers, $act_slots, $max_slots) = $data->fetchrow_array;
				if ($max_slots > 0) {
					if ($act_slots > $max_slots) {
						$act_slots = $max_slots;
					}
				}
				if ($insvalues ne "") {
					$insvalues .= ",";
				}
				$insvalues .= "
					(
						$ev_daemontime,
						'".&quoteSQL($game)."',
						$total_players,
						$total_kills,
						$total_headshots,
						$total_servers,
						$act_slots,
						$max_slots
					)
				";
			}
			if ($insvalues ne "") {
				&execNonQuery("
					INSERT INTO
						hlstats_Trend
						(
							timestamp,
							game,
							players,
							kills,
							headshots,
							servers,
							act_slots,
							max_slots
						)
						VALUES $insvalues
				");
			}
			$last_trend_timestamp = $ev_daemontime;
			&::printEvent("HLSTATSX", "Insert new server trend timestamp", 1);
		}
	} else {
		$last_trend_timestamp = $ev_daemontime;
	}  
}

sub send_global_chat
{
	my ($message) = @_;
	while( my($server) = each(%g_servers))
	{	
		if ($server ne $s_addr && $g_servers{$server}->{"srv_players"})
		{
			my @userlist;
			my %players_temp=%{$g_servers{$server}->{"srv_players"}};
			my $pcount = scalar keys %players_temp;
			
			if ($pcount > 0) {
				while ( my($pl, $b_player) = each(%players_temp) ) {
					my $b_userid  = $b_player->{userid};
					if ($g_global_chat == 2)  {
						my $b_steamid = $b_player->{uniqueid};
						if ($g_servers{$server}->is_admin($b_steamid) == 1) {
							if (($b_player->{display_events} == 1) && ($b_player->{display_chat} == 1)) {
								push(@userlist, $b_player->{userid});
							} 
						}
					} else {  
						if (($b_player->{display_events} == 1) && ($b_player->{display_chat} == 1)) {
							push(@userlist, $b_player->{userid});
						}
					}
				}
				$g_servers{$server}->messageMany($message, 0, @userlist);
			}
		}
	}
}

#
# void buildEventInsertData ()
#
# Ran at startup to init event table queues, build initial queries, and set allowed-null columns
#

my %g_eventtable_data = ();

sub buildEventInsertData
{
	my $insertType = "";
	$insertType = " DELAYED" if ($db_lowpriority);
	while ( my ($table, $colsref) = each(%g_eventTables) )
	{
		$g_eventtable_data{$table}{queue} = [];
		$g_eventtable_data{$table}{nullallowed} = 0;
		$g_eventtable_data{$table}{lastflush} = $ev_daemontime;
		$g_eventtable_data{$table}{query} = "
		INSERT$insertType INTO
			hlstats_Events_$table
			(
				eventTime,
				serverId,
				map"
				;
		my $j = 0;
		foreach $i (@{$colsref})
		{
			$g_eventtable_data{$table}{query} .= ",\n$i";
			if (substr($i, 0, 4) eq 'pos_') {
				$g_eventtable_data{$table}{nullallowed} |= (1 << $j);
			}
			$j++;
		}
		$g_eventtable_data{$table}{query} .= ") VALUES\n";
	}
}

#
# void recordEvent (string table, array cols, bool getid, [mixed eventData ...])
#
# Queues an event for addition to an Events table, flushing when hitting table queue limit.
#

sub recordEvent
{
	my $table = shift;
	my $unused = shift;
	my @coldata = @_;
	
	my $value = "(FROM_UNIXTIME($::ev_unixtime),".$g_servers{$s_addr}->{'id'}.",'".quoteSQL($g_servers{$s_addr}->get_map())."'";
	$j = 0;
	for $i (@coldata) {
		if ($g_eventtable_data{$table}{nullallowed} & (1 << $j) && (!defined($i) || $i eq "")) {
			$value .= ",NULL";
		} elsif (!defined($i)) {
			$value .= ",''";
		} else {
			$value .= ",'".quoteSQL($i)."'";
		}
		$j++;
	}
	$value .= ")";
	
	push(@{$g_eventtable_data{$table}{queue}}, $value);
	
	if (scalar(@{$g_eventtable_data{$table}{queue}}) > $g_event_queue_size)
	{
		flushEventTable($table);
	}
}

sub flushEventTable
{
	my ($table) = @_;
	
	if (scalar(@{$g_eventtable_data{$table}{queue}}) == 0)
	{
		return;
	}
	
	my $query = $g_eventtable_data{$table}{query};
	foreach (@{$g_eventtable_data{$table}{queue}})
	{
		$query .= $_.",";
	}
	$query =~ s/,$//;
	execNonQuery($query);
	$g_eventtable_data{$table}{lastflush} = $ev_daemontime;
	$g_eventtable_data{$table}{queue} = [];
}


#
# array calcSkill (int skill_mode, int killerSkill, int killerKills, int victimSkill, int victimKills, string weapon)
#
# Returns an array, where the first index contains the killer's new skill, and
# the second index contains the victim's new skill. 
#

sub calcSkill
{
  my ($skill_mode, $killerSkill, $killerKills, $victimSkill, $victimKills, $weapon, $killerTeam) = @_;
  my @newSkill;
  
  # ignored bots never do a "comeback"
  return ($g_skill_minchange, $victimSkill) if ($killerSkill < 1);
  return ($killerSkill + $g_skill_minchange, $victimSkill) if ($victimSkill < 1);
  
  if ($g_debug > 2) {
    &printNotice("Begin calcSkill: killerSkill=$killerSkill");
    &printNotice("Begin calcSkill: victimSkill=$victimSkill");
  }

  my $modifier = 1.00;
  # Look up the weapon's skill modifier
  if (defined($g_games{$g_servers{$s_addr}->{game}}{weapons}{$weapon})) {
    $modifier = $g_games{$g_servers{$s_addr}->{game}}{weapons}{$weapon}{modifier};
  }

  # Calculate the new skills
  
  my $killerSkillChange = 0;
  if ($g_skill_ratio_cap > 0) {
    # SkillRatioCap, from *XYZ*SaYnt
    #
    # dgh...we want to cap the ratio between the victimkill and killerskill.  For example, if the number 1 player
    # kills a newbie, he gets 1000/5000 * 5 * 1 = 1 points.  If gets killed by the newbie, he gets 5000/1000 * 5 *1
    # = -25 points.   Not exactly fair.  To fix this, I'm going to cap the ratio to 1/2 and 2/1.
    # these numbers are designed such that an excellent player will have to get about a 2:1 ratio against noobs to
    # hold steady in points.
    my $lowratio = 0.7;
    my $highratio = 1.0 / $lowratio;
    my $ratio = ($victimSkill / $killerSkill);
    if ($ratio < $lowratio) { $ratio = $lowratio; }
    if ($ratio > $highratio) { $ratio = $highratio; }
    $killerSkillChange = $ratio * 5 * $modifier;
  } else {
    $killerSkillChange = ($victimSkill / $killerSkill) * 5 * $modifier;
  }

  if ($killerSkillChange > $g_skill_maxchange) {
    &printNotice("Capping killer skill change of $killerSkillChange to $g_skill_maxchange") if ($g_debug > 2);
    $killerSkillChange = $g_skill_maxchange;
  }
  
  my $victimSkillChange = $killerSkillChange;

  if ($skill_mode == 1)
  {
    $victimSkillChange = $killerSkillChange * 0.75;
  }
  elsif ($skill_mode == 2)
  {
    $victimSkillChange = $killerSkillChange * 0.5;
  }
  elsif ($skill_mode == 3)
  {
    $victimSkillChange = $killerSkillChange * 0.25;
  }
  elsif ($skill_mode == 4)
  {
    $victimSkillChange = 0;
  }
  elsif ($skill_mode == 5)
  {
    #Zombie Panic: Source only
    #Method suggested by heimer. Survivor's lose half of killer's gain when dying, but Zombie's only lose a quarter. 
    if ($killerTeam eq "Undead")
    {
      $victimSkillChange = $killerSkillChange * 0.5;
    }
    elsif ($killerTeam eq "Survivor")
    {
      $victimSkillChange = $killerSkillChange * 0.25;
    }
  }
  
  if ($victimSkillChange > $g_skill_maxchange) {
    &printNotice("Capping victim skill change of $victimSkillChange to $g_skill_maxchange") if ($g_debug > 2);
    $victimSkillChange = $g_skill_maxchange;
  }
  
  if ($g_skill_maxchange >= $g_skill_minchange) {
    if ($killerSkillChange < $g_skill_minchange) {
      &printNotice("Capping killer skill change of $killerSkillChange to $g_skill_minchange") if ($g_debug > 2);
      $killerSkillChange = $g_skill_minchange;
    } 
  
    if (($victimSkillChange < $g_skill_minchange) && ($skill_mode != 4)) {
      &printNotice("Capping victim skill change of $victimSkillChange to $g_skill_minchange") if ($g_debug > 2);
      $victimSkillChange = $g_skill_minchange;
    }
  }
  if (($killerKills < $g_player_minkills ) || ($victimKills < $g_player_minkills )) {
    $killerSkillChange = $g_skill_minchange;
    if ($skill_mode != 4) {
      $victimSkillChange = $g_skill_minchange;
    } else {
      $victimSkillChange = 0;
    }  
  }
  
  $killerSkill += $killerSkillChange;
  $victimSkill -= $victimSkillChange;
  
  # we want int not float
  $killerSkill = sprintf("%d", $killerSkill + 0.5);
  $victimSkill = sprintf("%d", $victimSkill + 0.5);
  
  if ($g_debug > 2) {
    &printNotice("End calcSkill: killerSkill=$killerSkill");
    &printNotice("End calcSkill: victimSkill=$victimSkill");
  }

  return ($killerSkill, $victimSkill);
}

sub calcL4DSkill
{
	my ($killerSkill, $weapon, $difficulty) = @_;
	
	# ignored bots never do a "comeback"
	#return ($killerSkill, $victimSkill) if ($killerSkill < 1);
	#return ($killerSkill, $victimSkill)	if ($victimSkill < 1);
	
	if ($g_debug > 2) {
		&printNotice("Begin calcSkill: killerSkill=$killerSkill");
		&printNotice("Begin calcSkill: victimSkill=$victimSkill");
	}

	my $modifier = 1.00;
	# Look up the weapon's skill modifier
	if (defined($g_games{$g_servers{$s_addr}->{game}}{weapons}{$weapon})) {
		$modifier = $g_games{$g_servers{$s_addr}->{game}}{weapons}{$weapon}{modifier};
	}
	
	# Calculate the new skills
	
	$diffweight=0.5;
	if ($difficulty > 0) {
			$diffweight = $difficulty / 2;
	}	
	
	my $killerSkillChange = $pointvalue * $diffweight;

	if ($killerSkillChange > $g_skill_maxchange) {
		&printNotice("Capping killer skill change of $killerSkillChange to $g_skill_maxchange") if ($g_debug > 2);
		$killerSkillChange = $g_skill_maxchange;
	}

	if ($g_skill_maxchange >= $g_skill_minchange) {
		if ($killerSkillChange < $g_skill_minchange) {
			&printNotice("Capping killer skill change of $killerSkillChange to $g_skill_minchange") if ($g_debug > 2);
			$killerSkillChange = $g_skill_minchange;
		} 
	}
	
	$killerSkill += $killerSkillChange;
	# we want int not float
	$killerSkill = sprintf("%d", $killerSkill + 0.5);
	
	if ($g_debug > 2) {
		&printNotice("End calcSkill: killerSkill=$killerSkill");
	}
	
	return $killerSkill;
}


# Gives members of 'team' an extra 'reward' skill points. Members of the team
# who have been inactive (no events) for more than 2 minutes are not rewarded.
#

sub rewardTeam
{
	my ($team, $reward, $actionid, $actionname, $actioncode) = @_;
	$rcmd = $g_servers{$s_addr}->{broadcasting_command};
	
	my $player;
	
	&printNotice("Rewarding team \"$team\" with \"$reward\" skill for action \"$actionid\" ...");
	my @userlist;
	foreach $player (values(%g_players)) {
		my $player_team      = $player->{team};
		my $player_timestamp = $player->{timestamp};
		if (($g_servers{$s_addr}->{ignore_bots} == 1) && (($player->{is_bot} == 1) || ($player->{userid} <= 0))) {
			$desc = "(IGNORED) BOT: ";
		} else {
			if ($player_team eq $team) {
				if ($g_debug > 2) {
					&printNotice("Rewarding " . $player->getInfoString() . " with \"$reward\" skill for action \"$actionid\"");
				}
				
				&recordEvent(
					"TeamBonuses", 0,
					$player->{playerid},
					$actionid,
					$reward
					);
				$player->increment("skill", $reward, 1);
				$player->increment("session_skill", $reward, 1);
				$player->updateDB();
			}
			if ($player->{is_bot} == 0 && $player->{userid} > 0 && $player->{display_events} == 1) {
				push(@userlist, $player->{userid});
			}    
		}
	}
	if (($g_servers{$s_addr}->{broadcasting_events} == 1) && ($g_servers{$s_addr}->{broadcasting_player_actions} == 1)) {
		my $coloraction = $g_servers{$s_addr}->{format_action};
		my $verb = "got";
		if ($reward < 0) {
			$verb = "lost";
		}
		my $msg = sprintf("%s %s %s points for %s%s", $team, $verb, abs($reward), $coloraction, $actionname);
		$g_servers{$s_addr}->messageMany($msg, 0, @userlist);
	}
}


#
# int getPlayerId (uniqueId)
#
# Looks up a player's ID number, from their unique (WON) ID. Returns their PID.
#

sub getPlayerId
{
	my ($uniqueId) = @_;

	my $query = "
		SELECT
			playerId
		FROM
			hlstats_PlayerUniqueIds
		WHERE
			uniqueId='" . &::quoteSQL($uniqueId) . "' AND
			game='" . $g_servers{$s_addr}->{game} . "'
	";
	my $result = &doQuery($query);

	if ($result->rows > 0) {
		my ($playerId) = $result->fetchrow_array;
		$result->finish;
		return $playerId;
	} else {
		$result->finish;
		return 0;
	}
}


#
# int updatePlayerProfile (object player, string field, string value)
#
# Updates a player's profile information in the database.
#

sub updatePlayerProfile
{
	my ($player, $field, $value) = @_;
	$rcmd = $g_servers{$s_addr}->{player_command};
	
	unless ($player) {
		&printNotice("updatePlayerInfo: Bad player");
		return 0;
	}

	$value = &quoteSQL($value);
	if ($value eq "none" || $value eq " ") {
		$value = "";
	}
	
	my $playerName = &abbreviate($player->{name});
	my $playerId   = $player->{playerid};

	&execNonQuery("
		UPDATE
			hlstats_Players
		SET
			$field='$value'
		WHERE
			playerId=$playerId
	");
	
	if ($g_servers{$s_addr}->{player_events} == 1) {
		my $p_userid  = $g_servers{$s_addr}->format_userid($player->{userid});
		my $p_is_bot  = $player->{is_bot};
		$cmd_str = $rcmd." $p_userid ".$g_servers{$s_addr}->quoteparam("SET command successful for '$playerName'.");
		$g_servers{$s_addr}->dorcon($cmd_str);
	}
	return 1;
}


#
# mixed getClanId (string name)
#
# Looks up a player's clan ID from their name. Compares the player's name to tag
# patterns in hlstats_ClanTags. Patterns look like:  [AXXXXX] (matches 1 to 6
# letters inside square braces, e.g. [ZOOM]Player)  or  =\*AAXX\*= (matches
# 2 to 4 letters between an equals sign and an asterisk, e.g.  =*RAGE*=Player).
#
# Special characters in the pattern:
#    A    matches one character  (i.e. a character is required)
#    X    matches zero or one characters  (i.e. a character is optional)
#    a    matches literal A or a
#    x    matches literal X or x
#
# If no clan exists for the tag, it will be created. Returns the clan's ID, or
# 0 if the player is not in a clan.
#

sub getClanId
{
	my ($name) = @_;
	my $clanTag  = "";
	my $clanName = "";
	my $clanId   = 0;
	my $result = &doQuery("
		SELECT
			pattern,
			position,
			LENGTH(pattern) AS pattern_length
		FROM
			hlstats_ClanTags
		ORDER BY
			pattern_length DESC,
			id
	");
	
	while ( my($pattern, $position) = $result->fetchrow_array) {
		my $regpattern = quotemeta($pattern);
		$regpattern =~ s/([A-Za-z0-9]+[A-Za-z0-9_-]*)/\($1\)/; # to find clan name from tag
		$regpattern =~ s/A/./g;
		$regpattern =~ s/X/.?/g;
		
		if ($g_debug > 2) {
			&printNotice("regpattern=$regpattern");
		}
		
		if ((($position eq "START" || $position eq "EITHER") && $name =~ /^($regpattern).+/i) ||
			(($position eq "END"   || $position eq "EITHER") && $name =~ /.+($regpattern)$/i)) {
			
			if ($g_debug > 2) {
				&printNotice("pattern \"$regpattern\" matches \"$name\"! 1=\"$1\" 2=\"$2\"");
			}
			
			$clanTag  = $1;
			$clanName = $2;
			last;
		}
	}
	
	unless ($clanTag) {
		return 0;
	}

	my $query = "
		SELECT
			clanId
		FROM
			hlstats_Clans
		WHERE
			tag='" . &quoteSQL($clanTag) . "' AND
			game='$g_servers{$s_addr}->{game}'
		";
	$result = &doQuery($query);

	if ($result->rows) {
		($clanId) = $result->fetchrow_array;
		$result->finish;
		return $clanId;
	} else {
		# The clan doesn't exist yet, so we create it.
		$query = "
			REPLACE INTO
				hlstats_Clans
				(
					tag,
					name,
					game
				)
			VALUES
			(
				'" . &quoteSQL($clanTag)  . "',
				'" . &quoteSQL($clanName) . "',
				'".&quoteSQL($g_servers{$s_addr}->{game})."'
			)
		";
		&execNonQuery($query);
		
		$clanId = $db_conn->{'mysql_insertid'};

		&printNotice("Created clan \"$clanName\" <C:$clanId> with tag "
				. "\"$clanTag\" for player \"$name\"");
		return $clanId;
	}
}


#
# object getServer (string address, int port)
#
# Looks up a server's ID number in the Servers table, by searching for a
# matching IP address and port. NOTE you must specify IP addresses in the
# Servers table, NOT hostnames.
#
# Returns a new "Server object".
#

sub getServer
{
	my ($address, $port) = @_;

	my $query = "
		SELECT
			a.serverId,
			a.game,
			a.name,
			a.rcon_password,
			a.publicaddress,
			IFNULL(b.`value`,3) AS game_engine,
			IFNULL(c.`realgame`, 'hl2mp') AS realgame,
			IFNULL(a.max_players, 0) AS maxplayers
			
		FROM
			hlstats_Servers a LEFT JOIN hlstats_Servers_Config b on a.serverId = b.serverId AND b.`parameter` = 'GameEngine' LEFT JOIN `hlstats_Games` c ON a.game = c.code
		WHERE
			address=? AND
			port=? LIMIT 1
		";
	my @vals = (
		$address,
		$port
	);
	my $result = &execCached("get_server_information", $query, @vals);

	if ($result->rows) {
		my ($serverId, $game, $name, $rcon_pass, $publicaddress, $gameengine, $realgame, $maxplayers) = $result->fetchrow_array;
		$result->finish;
		if (!defined($g_games{$game})) {
			$g_games{$game} = new HLstats_Game($game);
		}
		# l4d code should be reused for l4d2
		# trying first using l4d as "realgame" code for l4d2 in db. if default server config settings won't work, will leave as own "realgame" code in db but uncomment line.
		#$realgame = "l4d" if $realgame eq "l4d2";
		
		return new HLstats_Server($serverId, $address, $port, $name, $rcon_pass, $game, $publicaddress, $gameengine, $realgame, $maxplayers);
	} else {
		$result->finish;
		return 0;
	}
}

#
# 
#
#
#

sub queryServer
{
	my ($iaddr, $iport, @query)            = @_;
	my $game = "";
	my $timeout=2;
	my $message = IO::Socket::INET->new(Proto=>"udp",Timeout=>$timeout,PeerPort=>$iport,PeerAddr=>$iaddr) or die "Can't make UDP socket: $@";
	$message->send("\xFF\xFF\xFF\xFFTSource Engine Query\x00");
	my ($datagram,$flags);
	my $end = time + $timeout;
	my $rin = '';
	vec($rin, fileno($message), 1) = 1;

	my %hash = ();

	while (1) {
		my $timeleft = $end - time;
		last if ($timeleft <= 0);
		my ($nfound, $t) = select(my $rout = $rin, undef, undef, $timeleft);
		last if ($nfound == 0); # either timeout or end of file
		$message->recv($datagram,1024,$flags);
		@hash{qw/key type netver hostname mapname gamedir gamename id numplayers maxplayers numbots dedicated os passreq secure gamever edf port/} = unpack("LCCZ*Z*Z*Z*vCCCCCCCZ*Cv",$datagram);
	}

	return @hash{@query};
}


sub getServerMod
{
	my ($address, $port) = @_;
	my ($playgame);

	&printEvent ("DETECT", "Querying $address".":$port for gametype");

	my @query = (
			'gamename',
			'gamedir',
			'hostname',
			'numplayers',
			'maxplayers',
			'mapname'
			);

	my ($gamename, $gamedir, $hostname, $numplayers, $maxplayers, $mapname) = &queryServer($address, $port, @query);

	if ($gamename =~ /^Counter-Strike$/i) {
		$playgame = "cstrike";
	} elsif ($gamename =~ /^Counter-Strike/i) {
		$playgame = "css";
	} elsif ($gamename =~ /^Team Fortress C/i) {
		$playgame = "tfc";
	} elsif ($gamename =~ /^Team Fortress/i) {
		$playgame = "tf";
	} elsif ($gamename =~ /^Day of Defeat$/i) {
		$playgame = "dod";
	} elsif ($gamename =~ /^Day of Defeat/i) {
		$playgame = "dods";
	} elsif ($gamename =~ /^Insurgency/i) {
		$playgame = "insmod";
	} elsif ($gamename =~ /^Neotokyo/i) {
		$playgame = "nts";
	} elsif ($gamename =~ /^Fortress Forever/i) {
		$playgame = "ff";
	} elsif ($gamename =~ /^Age of Chivalry/i) {
		$playgame = "aoc";
	} elsif ($gamename =~ /^Dystopia/i) {
		$playgame = "dystopia";
	} elsif ($gamename =~ /^Stargate/i) {
		$playgame = "sgtls";
	} elsif ($gamename =~ /^Battle Grounds/i) {
		$playgame = "bg2";
	} elsif ($gamename =~ /^Hidden/i) {
		$playgame = "hidden";
	} elsif ($gamename =~ /^L4D /i) {
		$playgame = "l4d";
	} elsif ($gamename =~ /^Left 4 Dead 2/i) {
		$playgame = "l4d2";
	} elsif ($gamename =~ /^ZPS /i) {
		$playgame = "zps";
	} elsif ($gamename =~ /^NS /i) {
		$playgame = "ns";
	} elsif ($gamename =~ /^pvkii/i) {
		$playgame = "pvkii";
	} elsif ($gamename =~ /^CSPromod/i) {
		$playgame = "csp";
	} elsif ($gamename eq "Half-Life") {
		$playgame = "valve";
	} elsif ($gamename eq "Nuclear Dawn") {
		$playgame = "nucleardawn";
    
	# We didn't found our mod, trying secondary way. This is required for some games such as FOF and GES and is a fallback for others
	} elsif ($gamedir =~ /^ges/i) {
		$playgame = "ges";
	} elsif ($gamedir =~ /^fistful_of_frags/i || $gamedir =~ /^fof/i) {
		$playgame = "fof";
	} elsif ($gamedir =~ /^hl2mp/i) {
		$playgame = "hl2mp";
	} elsif ($gamedir =~ /^tfc/i) {
		$playgame = "tfc";
	} elsif ($gamedir =~ /^tf/i) {
		$playgame = "tf";
	} elsif ($gamedir =~ /^ins/i) {
		$playgame = "insmod";
	} elsif ($gamedir =~ /^neotokyo/i) {
		$playgame = "nts";
	} elsif ($gamedir =~ /^fortressforever/i) {
		$playgame = "ff";
	} elsif ($gamedir =~ /^ageofchivalry/i) {
		$playgame = "aoc";
	} elsif ($gamedir =~ /^dystopia/i) {
		$playgame = "dystopia";
	} elsif ($gamedir =~ /^sgtls/i) {
		$playgame = "sgtls";
	} elsif ($gamedir =~ /^hidden/i) {
		$playgame = "hidden";
	} elsif ($gamedir =~ /^left4dead/i) {
		$playgame = "l4d";
	} elsif ($gamedir =~ /^left4dead2/i) {
		$playgame = "l4d2";
	} elsif ($gamedir =~ /^zps/i) {
		$playgame = "zps";
	} elsif ($gamedir =~ /^ns/i) {
		$playgame = "ns";
	} elsif ($gamedir =~ /^bg/i) {
		$playgame = "bg2";
	} elsif ($gamedir =~ /^pvkii/i) {
		$playgame = "pvkii";
	} elsif ($gamedir =~ /^cspromod/i) {
		$playgame = "csp";
	} elsif ($gamedir =~ /^valve$/i) {
		$playgame = "valve";
    } elsif ($gamedir =~ /^nucleardawn$/i) {
		$playgame = "nucleardawn";
	} elsif ($gamedir =~ /^dinodday$/i) {
		$playgame = "dinodday";
	} else {
		# We didn't found our mod, giving up.
		&printEvent("DETECT", "Failed to get Server Mod");
		return 0;
	}
	&printEvent("DETECT", "Saving server " . $address . ":" . $port . " with gametype " . $playgame);
	&addServerToDB($address, $port, $hostname, $playgame, $numplayers, $maxplayers, $mapname);
	return $playgame;
}

sub addServerToDB
{
	my ($address, $port, $name, $game, $act_players, $max_players, $act_map) = @_;
	my $sql = "INSERT INTO hlstats_Servers (address, port, name, game, act_players, max_players, act_map) VALUES ('$address', $port, '".&quoteSQL($name)."', '".&quoteSQL($game)."', $act_players, $max_players, '".&quoteSQL($act_map)."')";
	&execNonQuery($sql);
   
	my $last_id = $db_conn->{'mysql_insertid'};
	&execNonQuery("DELETE FROM `hlstats_Servers_Config` WHERE serverId = $last_id");
	&execNonQuery("INSERT INTO `hlstats_Servers_Config` (`serverId`, `parameter`, `value`)
				SELECT $last_id, `parameter`, `value`
				FROM `hlstats_Mods_Defaults` WHERE `code` = '';");
	&execNonQuery("INSERT INTO `hlstats_Servers_Config` (`serverId`, `parameter`, `value`) VALUES
				($last_id, 'Mod', '');");
	&execNonQuery("INSERT INTO `hlstats_Servers_Config` (`serverId`, `parameter`, `value`)
				SELECT $last_id, `parameter`, `value`
				FROM `hlstats_Games_Defaults` WHERE `code` = '".&quoteSQL($game)."'
				ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);");   
	&readDatabaseConfig();

	return 1;
}

#
# boolean sameTeam (string team1, string team2)
#
# This should be expanded later to allow for team alliances (e.g. TFC-hunted).
#

sub sameTeam
{
	my ($team1, $team2) = @_;
	
	if (($team1 eq $team2) && (($team1 ne "Unassigned") || ($team2 ne "Unassigned"))) {
		return 1;
	} else {
		return 0;
	}
}


#
# string getPlayerInfoString (object player, string ident)
#

sub getPlayerInfoString
{
	my ($player) = shift;
	my @ident = @_;
	
	if ($player) {
		return $player->getInfoString();
	} else {
		return "(" . join(",", @ident) . ")";
	}
}



#
# array getPlayerInfo (string player, string $ipAddr)
#
# Get a player's name, uid, wonid and team from "Name<uid><wonid><team>".
#

sub getPlayerInfo
{
	my ($player, $create_player, $ipAddr) = @_;

	if ($player =~ /^(.*?)<(\d+)><([^<>]*)><([^<>]*)>(?:<([^<>]*)>)?.*$/) {
		my $name		= $1;
		my $userid		= $2;
		my $uniqueid	= $3;
		my $team		= $4;
		my $role		= $5;
		my $bot			= 0;
		my $haveplayer  = 0;
		
		$plainuniqueid = $uniqueid;
		$uniqueid =~ s/^STEAM_[0-9]+?\://;
		
		if (($uniqueid eq "Console") && ($team eq "Console")) {
		  return 0;
		}
		if ($g_servers{$s_addr}->{play_game} == L4D()) {
		#for l4d, create meta player object for each role
			if ($uniqueid eq "") {
				#infected & witch have blank steamid
				if ($name eq "infected") {
					$uniqueid = "BOT-Horde";
					$team = "Infected";
					$userid = -9;
				} elsif ($name eq "witch") {
					$uniqueid = "BOT-Witch";
					$team = "Infected";
					$userid = -10;
				} else {
					return 0;
				}
			} elsif ($uniqueid eq "BOT") {
				#all other bots have BOT for steamid
				if ($team eq "Survivor") {
					if ($name eq "Nick") {
						$userid = -11;
					} elsif ($name eq "Ellis") {
						$userid = -13;
					} elsif ($name eq "Rochelle") {
						$userid = -14;
					} elsif ($name eq "Coach") {
						$userid = -12;
					} elsif ($name eq "Louis") {
						$userid = -4;
					} elsif ($name eq "Zoey") {
						$userid = -1;
					} elsif ($name eq "Francis") {
						$userid = -2;
					} elsif ($name eq "Bill") {
						$userid = -3;
					} else {
						&printEvent("ERROR", "No survivor match for $name",0,1);
						$userid = -4;
					}
				} else {
					if ($name eq "Smoker") {
						$userid = -5;
					} elsif ($name eq "Boomer") {
						$userid = -6;
					} elsif ($name eq "Hunter") {
						$userid = -7;
					} elsif ($name eq "Spitter") {
						$userid = -15;
					} elsif ($name eq "Jockey") {
						$userid = -16;
					} elsif ($name eq "Charger") {
						$userid = -17;
					} elsif ($name eq "Tank") {
						$userid = -8;
					} else {
						&printEvent("DEBUG", "No infected match for $name",0,1);
						$userid = -8;
					}
				}
				$uniqueid = "BOT-".$name;
				$name = "BOT-".$name;
			}
		}

		if ($ipAddr eq "none") {
			$ipAddr = "";
		}
		
		$bot = botidcheck($uniqueid);
		
		if ($g_mode eq "NameTrack") {
			$uniqueid = $name;
		} else {
			if ($g_mode eq "LAN" && !$bot && $userid > 0) {
				if ($ipAddr ne "") {
					$g_lan_noplayerinfo->{"$s_addr/$userid/$name"} = {
						ipaddress => $ipAddr,
						userid => $userid,
						name => $name,
						server => $s_addr
						};
					$uniqueid = $ipAddr;
				} else {
					while ( my($index, $player) = each(%g_players) ) {
						if (($player->{userid} eq $userid) &&
							($player->{name}   eq $name)) {
						
							$uniqueid = $player->{uniqueid}; 
							$haveplayer = 1;
							last;
						}   
					}
					if (!$haveplayer) {
						while ( my($index, $player) = each(%g_lan_noplayerinfo) ) {
							if (($player->{server} eq $s_addr) &&
								($player->{userid} eq $userid) &&
								($player->{name}   eq $name)) {
						
								$uniqueid = $player->{ipaddress}; 
								$haveplayer = 1;
							}    
						}  
					}
					if (!$haveplayer) {
						$uniqueid = "UNKNOWN";
					}
				}
			} else {
				# Normal (steamid) mode player and bot, as well as lan mode bots
				if ($bot) {
					$md5 = Digest::MD5->new;
					$md5->add($name);
					$md5->add($s_addr);
					$uniqueid = "BOT:" . $md5->hexdigest;
					$unique_id = $uniqueid if ($g_mode eq "LAN");
				}
			
				if ($uniqueid eq "UNKNOWN"
					|| $uniqueid eq "STEAM_ID_PENDING" || $uniqueid eq "STEAM_ID_LAN"
					|| $uniqueid eq "VALVE_ID_PENDING" || $uniqueid eq "VALVE_ID_LAN"
				) {
					return {
						name     => $name,
						userid   => $userid,
						uniqueid => $uniqueid,
						team     => $team
					};
				}
			}
		}
		
		if (!$haveplayer)
		{
			while ( my ($index, $player) = each(%g_players) ) {
				# Cannot exit loop early as more than one player can exist with same uniqueid
				# (bug? or just bad logging)
				# Either way, we disconnect any that don't match the current line
				if ($player->{uniqueid} eq $uniqueid) {
					$haveplayer = 1;
					# Catch players reconnecting without first disconnecting
					if ($player->{userid} != $userid) {
					
						&doEvent_Disconnect(
							$player->{"userid"},
							$uniqueid,
							""
						);
						$haveplayer = 0;
					}
				}
			}
		}
		
		if ($haveplayer) {
			my $player = lookupPlayer($s_addr, $userid, $uniqueid);
			if ($player) {
				#  The only time team should go /back/ to unassigned ("") is on mapchange
				#  (which is already handled in the ChangeMap handler)
				#  So ignore when team is blank (<>) from lazy log lines
				if ($team ne "" && $player->{team} ne $team) {
					&doEvent_TeamSelection(
						$userid,
						$uniqueid,
						$team
					);
				}
				if ($role ne "" && $role ne $player->{role}) {
					&doEvent_RoleSelection(
						$player->{"userid"},
						$player->{"uniqueid"},
						$role
					);
				}
				
				$player->updateTimestamp();
			}  
		} else {
			if ($userid != 0) {
				if ($create_player > 0) {
					my $preIpAddr = "";
					if ($g_preconnect->{"$s_addr/$userid/$name"}) {
						$preIpAddr = $g_preconnect->{"$s_addr/$userid/$name"}->{"ipaddress"};
					}
					# Add the player to our hash of player objects
					$g_servers{$s_addr}->{"srv_players"}->{"$userid/$uniqueid"} = new HLstats_Player(
						server => $s_addr,
						server_id => $g_servers{$s_addr}->{id},
						userid => $userid,
						uniqueid => $uniqueid,
						plain_uniqueid => $plainuniqueid,
						game => $g_servers{$s_addr}->{game},
						name => $name,
						team => $team,
						role => $role,
						is_bot => $bot,
						display_events => $g_servers{$s_addr}->{default_display_events},
						address => (($preIpAddr ne "") ? $preIpAddr : $ipAddr)
					);
					
					if ($preIpAddr ne "") {
						&printEvent("SERVER", "LATE CONNECT [$name/$userid] - steam userid validated");
						&doEvent_Connect($userid, $uniqueid, $preIpAddr);
						delete($g_preconnect->{"$s_addr/$userid/$name"});
					}
					# Increment number of players on server
					$g_servers{$s_addr}->updatePlayerCount();
				}  
			} elsif (($g_mode eq "LAN") && (defined($g_lan_noplayerinfo{"$s_addr/$userid/$name"}))) {
				if ((!$haveplayer) && ($uniqueid ne "UNKNOWN") && ($create_player > 0)) {
					$g_servers{$s_addr}->{srv_players}->{"$userid/$uniqueid"} = new HLstats_Player(
						server => $s_addr,
						server_id => $g_servers{$s_addr}->{id},
						userid => $userid,
						uniqueid => $uniqueid,
						plain_uniqueid => $plainuniqueid,
						game => $g_servers{$s_addr}->{game},
						name => $name,
						team => $team,
						role => $role,
						is_bot => $bot
					);
					delete($g_lan_noplayerinfo{"$s_addr/$userid/$name"});
					# Increment number of players on server
					
					$g_servers{$s_addr}->updatePlayerCount();
				} 
			} else {
				&printNotice("No player object available for player \"$name\" <U:$userid>");
			}
		}
		
		return {
			name     => $name,
			userid   => $userid,
			uniqueid => $uniqueid,
			team     => $team,
			is_bot   => $bot
		};
	} elsif ($player =~ /^(.+)<([^<>]+)>$/) {
		my $name     = $1;
		my $uniqueid = $2;
		my $bot      = 0;
		
		if (&botidcheck($uniqueid)) {
			$md5 = Digest::MD5->new;
			$md5->add($ev_daemontime);
			$md5->add($s_addr);
			$uniqueid = "BOT:" . $md5->hexdigest;
			$bot = 1;
		}
		return {
			name     => $name,
			uniqueid => $uniqueid,
			is_bot   => $bot
		};
	} elsif ($player =~ /^<><([^<>]+)><>$/) {
		my $uniqueid = $1;
		my $bot      = 0;
		if (&botidcheck($uniqueid)) {
			$md5 = Digest::MD5->new;
			$md5->add($ev_daemontime);
			$md5->add($s_addr);
			$uniqueid = "BOT:" . $md5->hexdigest;
			$bot = 1;
		}
		return {
			uniqueid => $uniqueid,
			is_bot   => $bot
		};
	} else {
		return 0;
	}
}


#
# hash getProperties (string propstring)
#
# Parse (key "value") properties into a hash.
#

sub getProperties
{
	my ($propstring) = @_;
	my %properties;
	my $dods_flag = 0;
	
	while ($propstring =~ s/^\s*\((\S+)(?:(?: "(.+?)")|(?: ([^\)]+)))?\)//) {
		my $key = $1;
		if (defined($2)) {
			if ($key eq "player") {
				if ($dods_flag == 1) {
					$key = "player_a";
					$dods_flag++;
				} elsif ($dods_flag == 2) {
					$key = "player_b";
				}
			}
			$properties{$key} = $2;
		} elsif (defined($3)) {
			$properties{$key} = $3;
		} else {
			$properties{$key} = 1; # boolean property
		}
		if ($key eq "flagindex") {
			$dods_flag++;
		}
	}
	
	return %properties;
}


# 
# boolean like (string subject, string compare)
#
# Returns true if 'subject' equals 'compare' with optional whitespace.
#

sub like
{
	my ($subject, $compare) = @_;
	
	if ($subject =~ /^\s*\Q$compare\E\s*$/) {
		return 1;
	} else {
		return 0;
	}
}


# 
# boolean botidcheck (string uniqueid)
#
# Returns true if 'uniqueid' is that of a bot.
#

sub botidcheck
{
	# needs cleaned up
	# added /^00000000\:\d+\:0$/ check for "whichbot"
	my ($uniqueid) = @_;
	if ($uniqueid eq "BOT" || $uniqueid eq "0" || $uniqueid =~ /^00000000\:\d+\:0$/) {
		return 1
	}
	return 0;
}

sub isTrackableTeam
{
	my ($team) = @_;
	#if ($team =~ /spectator/i || $team =~ /unassigned/i || $team eq "") {
	if ($team =~ /spectator/i || $team eq "") {
		return 0;
	}
	return 1;
}

sub reloadConfiguration
{
	&flushAll;
	&readDatabaseConfig;
}


sub flushAll
{
	# we only need to flush events if we're about to shut down. they are unaffected by server/player deletion
	my ($flushevents) = @_;
	if ($flushevents)
	{
		while ( my ($table, $colsref) = each(%g_eventTables) )
		{
			flushEventTable($table);
		}
	}
	
	while( my($se, $server) = each(%g_servers))
	{	
		while ( my($pl, $player) = each(%{$server->{"srv_players"}}) )
		{
			if ($player)
			{
				$player->playerCleanup();
			}
		}
		$server->flushDB();
	}
}


##
## MAIN
##

# Options

$opt_help = 0;
$opt_version = 0;

$db_host = "localhost";
$db_user = "";
$db_pass = "";
$db_name = "hlstats";
$db_lowpriority = 1;

$s_ip = "";
$s_port = "27500";

$g_mailto = "";
$g_mailpath = "/bin/mail";
$g_mode = "Normal";
$g_deletedays = 5;
$g_requiremap = 0;
$g_debug = 1;
$g_nodebug = 0;
$g_rcon = 1;
$g_rcon_ignoreself = 0;
$g_rcon_record = 1;
$g_stdin = 0;
$g_server_ip = "";
$g_server_port = 27015;
$g_timestamp = 0;
$g_cpanelhack = 0;
$g_event_queue_size = 10;
$g_dns_resolveip = 1;
$g_dns_timeout = 5;
$g_skill_maxchange = 100;
$g_skill_minchange = 2;
$g_skill_ratio_cap = 0;
$g_geoip_binary = 0;
$g_player_minkills = 50;
$g_onlyconfig_servers = 1;
$g_track_stats_trend = 0;
%g_lan_noplayerinfo = ();
%g_preconnect = ();
$g_global_banning = 0;
$g_log_chat = 0;
$g_log_chat_admins = 0;
$g_global_chat = 0;
$g_ranktype = "skill";
$g_gi = undef;

my %dysweaponcodes = (
	"1" => "Light Katana",
	"2" => "Medium Katana",
	"3" => "Fatman Fist",
	"4" => "Machine Pistol",
	"5" => "Shotgun",
	"6" => "Laser Rifle",
	"7" => "BoltGun",
	"8" => "SmartLock Pistols",
	"9" => "Assault Rifle",
	"10" => "Grenade Launcher",
	"11" => "MK-808 Rifle",
	"12" => "Tesla Rifle",
	"13" => "Rocket Launcher",
	"14" => "Minigun",
	"15" => "Ion Cannon",
	"16" => "Basilisk",
	"17" => "Frag Grenade",
	"18" => "EMP Grenade",
	"19" => "Spider Grenade",
	"22" => "Cortex Bomb"
);

# Usage message

$usage = <<EOT
Usage: hlstats.pl [OPTION]...
Collect statistics from one or more Half-Life2 servers for insertion into
a MySQL database.

  -h, --help                      display this help and exit  
  -v, --version                   output version information and exit
  -d, --debug                     enable debugging output (-dd for more)
  -n, --nodebug                   disables above; reduces debug level
  -m, --mode=MODE                 player tracking mode (Normal, LAN or NameTrack)  [$g_mode]
      --db-host=HOST              database ip or ip:port  [$db_host]
      --db-name=DATABASE          database name  [$db_name]
      --db-password=PASSWORD      database password (WARNING: specifying the
                                    password on the command line is insecure.
                                    Use the configuration file instead.)
      --db-username=USERNAME      database username
      --dns-resolveip             resolve player IP addresses to hostnames
                                    (requires working DNS)
   -c,--configfile                Specific configfile to use, settings in this file can now
                                    be overidden with commandline settings.
      --nodns-resolveip           disables above
      --dns-timeout=SEC           timeout DNS queries after SEC seconds  [$g_dns_timeout]
  -i, --ip=IP                     set IP address to listen on for UDP log data
  -p, --port=PORT                 set port to listen on for UDP log data  [$s_port]
  -r, --rcon                      enables rcon command exec support (the default)
      --norcon                    disables rcon command exec support
  -s, --stdin                     read log data from standard input, instead of
                                    from UDP socket. Must specify --server-ip
                                    and --server-port to indicate the generator
                                    of the inputted log data (implies --norcon)
      --nostdin                   disables above
      --server-ip                 specify data source IP address for --stdin
      --server-port               specify data source port for --stdin  [$g_server_port]
  -t, --timestamp                 tells HLstatsX:CE to use the timestamp in the log
                                    data, instead of the current time on the
                                    database server, when recording events
      --notimestamp               disables above
      --event-queue-size=SIZE     manually set event queue size to control flushing
                                    (recommend 100+ for STDIN)

Long options can be abbreviated, where such abbreviation is not ambiguous.
Default values for options are indicated in square brackets [...].

Most options can be specified in the configuration file:
  $opt_configfile
Note: Options set on the command line take precedence over options set in the
configuration file. The configuration file name is set at the top of hlstats.pl.

HLstatsX: Community Edition http://www.hlxcommunity.com
EOT
;

%g_config_servers = ();


sub readDatabaseConfig()
{
	&printEvent("CONFIG", "Reading database config...", 1);
	%g_config_servers = ();
	%g_servers = ();
	%g_games = ();

	# elstatsneo: read the servers portion from the mysql database
	my $srv_id = &doQuery("SELECT serverId,CONCAT(address,':',port) AS addr FROM hlstats_Servers");
	while ( my($serverId,$addr) = $srv_id->fetchrow_array) {
		$g_config_servers{$addr} = ();
		my $serverConfig = &doQuery("SELECT parameter,value FROM hlstats_Servers_Config WHERE serverId=$serverId");
		while ( my($p,$v) = $serverConfig->fetchrow_array) {
			$g_config_servers{$addr}{$p} = $v;
		}
	}
	$srv_id->finish;
	# hlxce: read the global settings from the database!
	my $gsettings = &doQuery("SELECT keyname,value FROM hlstats_Options WHERE opttype <= 1");
	while ( my($p,$v) = $gsettings->fetchrow_array) {
		if ($g_debug > 1) {
			print "Config parameter '$p' = '$v'\n";
		}
		$tmp = "\$".$directives_mysql{$p}." = '$v'";
		#print " -> setting ".$tmp."\n";
		eval $tmp;
	}
	$gsettings->finish;
	# setting defaults

	&printEvent("DAEMON", "Proxy_Key DISABLED", 1) if ($proxy_key eq "");
	while (my($addr, $server) = each(%g_config_servers)) {
		
		if (!defined($g_config_servers{$addr}{"MinPlayers"})) {
			$g_config_servers{$addr}{"MinPlayers"}						= 6;
		}  
		if (!defined($g_config_servers{$addr}{"DisplayResultsInBrowser"})) {
			$g_config_servers{$addr}{"DisplayResultsInBrowser"}			= 0;
		}  
		if (!defined($g_config_servers{$addr}{"BroadCastEvents"})) {
			$g_config_servers{$addr}{"BroadCastEvents"}					= 0;
		}
		if (!defined($g_config_servers{$addr}{"BroadCastPlayerActions"})) {
			$g_config_servers{$addr}{"BroadCastPlayerActions"}			= 0;
		}
		if (!defined($g_config_servers{$addr}{"BroadCastEventsCommand"})) {
			$g_config_servers{$addr}{"BroadCastEventsCommand"}			= "say";
		}
		if (!defined($g_config_servers{$addr}{"BroadCastEventsCommandAnnounce"})) {
			$g_config_servers{$addr}{"BroadCastEventsCommandAnnounce"}	= "say";
		}
		if (!defined($g_config_servers{$addr}{"PlayerEvents"})) {
			$g_config_servers{$addr}{"PlayerEvents"}					= 1;
		}
		if (!defined($g_config_servers{$addr}{"PlayerEventsCommand"})) {
			$g_config_servers{$addr}{"PlayerEventsCommand"}				= "say";
		}
		if (!defined($g_config_servers{$addr}{"PlayerEventsCommandOSD"})) {
			$g_config_servers{$addr}{"PlayerEventsCommandOSD"}			= "";
		}
		if (!defined($g_config_servers{$addr}{"PlayerEventsCommandHint"})) {
			$g_config_servers{$addr}{"PlayerEventsCommandHint"}			= "";
		}
		if (!defined($g_config_servers{$addr}{"PlayerEventsAdminCommand"})) {
			$g_config_servers{$addr}{"PlayerEventsAdminCommand"}		= "";
		}
		if (!defined($g_config_servers{$addr}{"ShowStats"})) {
			$g_config_servers{$addr}{"ShowStats"}						= 1;
		}
		if (!defined($g_config_servers{$addr}{"AutoTeamBalance"})) {
			$g_config_servers{$addr}{"AutoTeamBalance"}					= 0;
		}
		if (!defined($g_config_servers{$addr}{"AutoBanRetry"})) {
			$g_config_servers{$addr}{"AutoBanRetry"}					= 0;
		}
		if (!defined($g_config_servers{$addr}{"TrackServerLoad"})) {
			$g_config_servers{$addr}{"TrackServerLoad"}					= 0;
		}
		if (!defined($g_config_servers{$addr}{"MinimumPlayersRank"})) {
			$g_config_servers{$addr}{"MinimumPlayersRank"}				= 0;
		}
		if (!defined($g_config_servers{$addr}{"Admins"})) {
			$g_config_servers{$addr}{"Admins"}							= "";
		}
		if (!defined($g_config_servers{$addr}{"SwitchAdmins"})) {
			$g_config_servers{$addr}{"SwitchAdmins"}					= 0;
		}
		if (!defined($g_config_servers{$addr}{"IgnoreBots"})) {
			$g_config_servers{$addr}{"IgnoreBots"}						= 1;
		}
		if (!defined($g_config_servers{$addr}{"SkillMode"})) {
			$g_config_servers{$addr}{"SkillMode"}						= 0;
		}
		if (!defined($g_config_servers{$addr}{"GameType"})) {
			$g_config_servers{$addr}{"GameType"}						= 0;
		}
		if (!defined($g_config_servers{$addr}{"BonusRoundTime"})) {
			$g_config_servers{$addr}{"BonusRoundTime"}					= 0;
		}
		if (!defined($g_config_servers{$addr}{"BonusRoundIgnore"})) {
			$g_config_servers{$addr}{"BonusRoundIgnore"}				= 0;
		}
		if (!defined($g_config_servers{$addr}{"Mod"})) {
			$g_config_servers{$addr}{"Mod"}								= "";
		}
		if (!defined($g_config_servers{$addr}{"EnablePublicCommands"})) {
			$g_config_servers{$addr}{"EnablePublicCommands"}			= 1;
		}
		if (!defined($g_config_servers{$addr}{"ConnectAnnounce"})) {
			$g_config_servers{$addr}{"ConnectAnnounce"}					= 1;
		}
		if (!defined($g_config_servers{$addr}{"UpdateHostname"})) {
			$g_config_servers{$addr}{"UpdateHostname"}					= 0;
		}
		if (!defined($g_config_servers{$addr}{"DefaultDisplayEvents"})) {
			$g_config_servers{$addr}{"DefaultDisplayEvents"}			= 1;
		}
	}

	&printEvent("CONFIG", "I have found the following server configs in database:", 1);
	while (my($addr, $server) = each(%g_config_servers)) {
		&printEvent("S_CONFIG", $addr, 1);
	}
	
	my $geotell = ((!defined($g_gi)) ? -1 : tell $g_gi{fh});
	
	if ($g_geoip_binary > 0 && $geotell == -1) {
		my $geoipfile = "$opt_libdir/GeoLiteCity/GeoLiteCity.dat";
		if (-r $geoipfile) {
			eval "use Geo::IP::PurePerl"; my $hasGeoIP = $@ ? 0 : 1;
			if ($hasGeoIP) {
				$g_gi = Geo::IP::PurePerl->open($geoipfile, "GEOIP_STANDARD");
			} else {
				&printEvent("ERROR", "GeoIP method set to binary file lookup but Geo::IP::PurePerl module NOT FOUND", 1);
				$g_gi = undef;
			}
		} else {
			&printEvent("ERROR", "GeoIP method set to binary file lookup but $geoipfile NOT FOUND", 1);
			$g_gi = undef;
		}
	} elsif ($g_geoip_binary == 0 && $geotell > -1) {
		close($g_gi{fh});
		$g_gi = undef;
	}
} 

# Read Config File

if ($opt_configfile && -r $opt_configfile) {
	$conf = ConfigReaderSimple->new($opt_configfile);
	$conf->parse();
	%directives = (
		"DBHost",					"db_host",
		"DBUsername",			"db_user",
		"DBPassword",			"db_pass",
		"DBName",					"db_name",
		"DBLowPriority",		"db_lowpriority",
		"BindIP",					"s_ip",
		"Port",						"s_port",
		"DebugLevel",			"g_debug",
		"CpanelHack",			"g_cpanelhack",
		"EventQueueSize",		"g_event_queue_size"
	);

	%directives_mysql = (
		"version",					"g_version",
		"MailTo",					"g_mailto",
		"MailPath",					"g_mailpath",
		"Mode",						"g_mode",
		"DeleteDays",				"g_deletedays",
		"UseTimestamp",				"g_timestamp",
		"DNSResolveIP",				"g_dns_resolveip",
		"DNSTimeout",				"g_dns_timeout",
		"RconIgnoreSelf",			"g_rcon_ignoreself",
		"Rcon",						"g_rcon",
		"RconRecord",				"g_rcon_record",
		"MinPlayers",				"g_minplayers",
		"SkillMaxChange",			"g_skill_maxchange",
		"SkillMinChange",			"g_skill_minchange",
		"PlayerMinKills",			"g_player_minkills",
		"AllowOnlyConfigServers",	"g_onlyconfig_servers",
		"TrackStatsTrend",			"g_track_stats_trend",
		"GlobalBanning",			"g_global_banning",
		"LogChat",					"g_log_chat",
		"LogChatAdmins",			"g_log_chat_admins",
		"GlobalChat",				"g_global_chat",
		"SkillRatioCap",			"g_skill_ratio_cap",
		"rankingtype",				"g_ranktype",
		"UseGeoIPBinary",			"g_geoip_binary",
		"Proxy_Key",				"proxy_key"
	);

#		"Servers",                "g_config_servers"
	&doConf($conf, %directives);

} else {
	print "-- Warning: unable to open configuration file '$opt_configfile'\n";
}

# Read Command Line Arguments

%copts = ();

GetOptions(
	"help|h"			=> \$copts{opt_help},
	"version|v"			=> \$copts{opt_version},
	"debug|d+"			=> \$copts{g_debug},
	"nodebug|n+"		=> \$copts{g_nodebug},
	"mode|m=s"			=> \$copts{g_mode},
	"configfile|c=s"	=> \$copts{configfile},
	"db-host=s"			=> \$copts{db_host},
	"db-name=s"			=> \$copts{db_name},
	"db-password=s"		=> \$copts{db_pass},
	"db-username=s"		=> \$copts{db_user},
	"dns-resolveip!"	=> \$copts{g_dns_resolveip},
	"dns-timeout=i"		=> \$copts{g_dns_timeout},
	"ip|i=s"			=> \$copts{s_ip},
	"port|p=i"			=> \$copts{s_port},
	"rcon!"				=> \$copts{g_rcon},
	"r"					=> \$copts{g_rcon},
	"stdin!"			=> \$copts{g_stdin},
	"s"					=> \$copts{g_stdin},
	"server-ip=s"		=> \$copts{g_server_ip},
	"server-port=i"		=> \$copts{g_server_port},
	"timestamp!"		=> \$copts{g_timestamp},
	"t"					=> \$copts{g_timestamp},
	"event-queue-size"  => \$copts{g_event_queue_size}
) or die($usage);


if ($configfile && -r $configfile) {
	$conf = '';
    $conf = ConfigReaderSimple->new($configfile);
    $conf->parse();
	&doConf($conf, %directives);
}

# these are set above, we then reload them to override values in the actual config
setOptionsConf(%copts);

if ($g_cpanelhack) {
	my $home_dir = $ENV{ HOME };
	my $base_module_dir = (-d "$home_dir/perl" ? "$home_dir/perl" : ( getpwuid($>) )[7] . '/perl/');
	unshift @INC, map { $base_module_dir . $_ } @INC;
}

eval {
  require Geo::IP::PurePerl;
};
import Geo::IP::PurePerl;

if ($opt_help) {
	print $usage;
	exit(0);
}

if ($opt_version) {
	&doConnect;
	my $result = &doQuery("
		SELECT
			value
		FROM
			hlstats_Options
		WHERE
			keyname='version'
	");

	if ($result->rows > 0) {
		$g_version = $result->fetchrow_array;
	}
	$result->finish;
	print "\nhlstats.pl (HLstatsX Community Edition) Version $g_version\n"
		. "Real-time player and clan rankings and statistics for Half-Life 2\n"
		. "Modified (C) 2008-20XX  Nicholas Hastings (nshastings@gmail.com)\n"
		. "Copyleft (L) 2007-2008  Malte Bayer\n"
		. "Modified (C) 2005-2007  Tobias Oetzel (Tobi@hlstatsx.com)\n"
		. "Original (C) 2001 by Simon Garner \n\n";
	
	print "Using ConfigReaderSimple module version $ConfigReaderSimple::VERSION\n";
	if ($g_rcon) {
		print "Using rcon module\n";
	}
	
	print "\nThis is free software; see the source for copying conditions.  There is NO\n"
		. "warranty; not even for MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.\n\n";
	exit(0);
}

# Connect to the database

&doConnect;
&readDatabaseConfig;
&buildEventInsertData;

if ($g_mode ne "Normal" && $g_mode ne "LAN" && $g_mode ne "NameTrack") {
	$g_mode = "Normal";
}

$g_debug -= $g_nodebug;
$g_debug = 0 if ($g_debug < 0);


# Init Timestamp
my ($sec,$min,$hour,$mday,$mon,$year) = localtime(time());
$ev_timestamp = sprintf("%04d-%02d-%02d %02d:%02d:%02d", $year+1900, $mon+1, $mday, $hour, $min, $sec);
$ev_unixtime  = time();
$ev_daemontime = $ev_unixtime;

# Startup

&printEvent("HLSTATSX", "HLstatsX:CE $g_version starting...", 1);

# Create the UDP & TCP socket

if ($g_stdin) {
	$g_rcon = 0;
	&printEvent("UDP", "UDP listen socket disabled, reading log data from STDIN.", 1);
	if (!$g_server_ip || !$g_server_port) {
		&printEvent("UDP", "ERROR: You must specify source of STDIN data using --server-ip and --server-port", 1);
		&printEvent("UDP", "Example: ./hlstats.pl --stdin --server-ip 12.34.56.78 --server-port 27015", 1);
		exit(255);
	} else {
		&printEvent("UDP", "All data from STDIN will be allocated to server '$g_server_ip:$g_server_port'.", 1);
		$s_peerhost = $g_server_ip;
		$s_peerport = $g_server_port;
		$s_addr = "$s_peerhost:$s_peerport";
	}
} else {
	if ($s_ip) { $ip = $s_ip . ":"; } else { $ip = "port "; }
	$s_socket = IO::Socket::INET->new(
		Proto=>"udp",
		LocalAddr=>"$s_ip",
		LocalPort=>"$s_port"
	) or die ("\nCan't setup UDP socket on $ip$s_port: $!\n");
	
	&printEvent("UDP", "Opening UDP listen socket on $ip$s_port ... ok", 1);
}

if ($g_track_stats_trend > 0) {
	&printEvent("HLSTATSX", "Tracking Trend of the stats are enabled", 1);
}

if ($g_global_banning > 0) {
	&printEvent("HLSTATSX", "Global Banning on all servers is enabled", 1);
}

&printEvent("HLSTATSX", "Maximum Skill Change on all servers are ".$g_skill_maxchange." points", 1);
&printEvent("HLSTATSX", "Minimum Skill Change on all servers are ".$g_skill_minchange." points", 1);
&printEvent("HLSTATSX", "Minimum Players Kills on all servers are ".$g_player_minkills." kills", 1);

if ($g_log_chat > 0) {
	&printEvent("HLSTATSX", "Players chat logging is enabled", 1);
	if ($g_log_chat_admins > 0) {
		&printEvent("HLSTATSX", "Admins chat logging is enabled", 1);
	}
}

if ($g_global_chat == 1) {
	&printEvent("HLSTATSX", "Broadcasting public chat to all players is enabled", 1);
} elsif ($g_gloabl_chat == 2) {
	&printEvent("HLSTATSX", "Broadcasting public chat to admins is enabled", 1);
} else {
	&printEvent("HLSTATSX", "Broadcasting public chat is disabled", 1);
}

&printEvent("HLSTATSX", "Event queue size is set to ".$g_event_queue_size, 1);


%g_servers = ();

&printEvent("HLSTATSX", "HLstatsX:CE is now running ($g_mode mode, debug level $g_debug)", 1);

$start_time    = time();
if ($g_stdin) {
  $g_timestamp       = 1;
  $start_parse_time  = time();
  $import_logs_count = 0;
  &printEvent("IMPORT", "Start importing logs. Every dot signs 500 parsed lines", 1, 1);
}

# Main data loop
$c = 0;

sub getLine
{
	if ($g_stdin) {
		return <STDIN>;
	} else {
		return 1;
	}
}


&execNonQuery("TRUNCATE TABLE hlstats_Livestats");
$timeout    = 0;
$s_output = "";
my ($proxy_s_peerhost, $proxy_s_peerport);
while ($loop = &getLine()) {

    my ($sec,$min,$hour,$mday,$mon,$year) = localtime(time());
    $ev_timestamp = sprintf("%04d-%02d-%02d %02d:%02d:%02d", $year+1900, $mon+1, $mday, $hour, $min, $sec);
	$ev_unixtime  = time();
	$ev_daemontime = $ev_unixtime; #time()

	if ($g_stdin) {
		$s_output = $loop;
		if (($import_logs_count > 0) && ($import_logs_count % 500 == 0)) {
			$parse_time = $ev_unixtime - $start_parse_time;
			if ($parse_time == 0) {
				$parse_time++;
			}
			print ". [".($parse_time)." sec (".sprintf("%.3f", (500 / $parse_time)).")]\n";
			$start_parse_time = $ev_unixtime;
		}
	} else {
		if(IO::Select->new($s_socket)->can_read(2)) {  # 2 second timeout
			$s_socket->recv($s_output, 1024);
			$s_output = decode( 'utf8', $s_output );
			$timeout = 0;
		} else {
			$timeout++;
			if ($timeout % 60 == 0) {
				&printEvent("HLSTATSX", "No data since 120 seconds");
			}    
		}

		if (($s_output =~ /^.*PROXY Key=(.+) (.*)PROXY.+/) && $proxy_key ne "") {
			$rproxy_key = $1;
			$s_addr = $2;

			if ($s_addr ne "") {
				($s_peerhost, $s_peerport) = split(/:/, $s_addr);
			}

			$proxy_s_peerhost = $s_socket->peerhost;
			$proxy_s_peerport  = $s_socket->peerport;
			&printEvent("PROXY", "Detected proxy call from $proxy_s_peerhost:$proxy_s_peerport") if ($d_debug > 2);


			if ($proxy_key eq $rproxy_key) {
				$s_output =~ s/PROXY.*PROXY //;
				if ($s_output =~ /^C;HEARTBEAT;/) {
					&printEvent("PROXY, Heartbeat request from $proxy_s_peerhost:$proxy_s_peerport");
				} elsif ($s_output =~ /^C;RELOAD;/) {
					&printEvent("PROXY, Reload request from $proxy_s_peerhost:$proxy_s_peerport");
				} elsif ($s_output =~ /^C;KILL;/) {
					&printEvent("PROXY, Kill request from $proxy_s_peerhost:$proxy_s_peerport");
				} else {
					&printEvent("PROXY", $s_output);
				}
			} else {
				&printEvent("PROXY", "proxy_key mismatch, dropping package");
				&printEvent("PROXY", $s_output) if ($g_debug > 2);
				$s_output = "";
				next;
			}
		} else {
			# Reset the proxy stuff and use it as "normal"
			$rproxy_key = "";
			$proxy_s_peerhost = "";
			$proxy_s_peerport = "";

			$s_peerhost  = $s_socket->peerhost;
			$s_peerport  = $s_socket->peerport;

			$s_addr = "$s_peerhost:$s_peerport";
		}
	}

	if ($timeout == 0) {
		my ($address, $port);
		my @data = split ";", $s_output;
		$cmd = $data[0];
		if ($cmd eq "C" && ($s_peerhost eq "127.0.0.1" || (($proxy_key eq $rproxy_key) && $proxy_key ne ""))) {
			&printEvent("CONTROL", "Command received: ".$data[1], 1);
			if ($proxy_s_peerhost ne "" && $proxy_s_peerport ne "") {
				$address = $proxy_s_peerhost;
				$port = $proxy_s_peerport;
			} else {
				$address = $s_peerhost;
				$port = $s_peerport;
			}

			$s_addr = "$address:$port";

			my $dest = sockaddr_in($port, inet_aton($address));
			if ($data[1] eq "HEARTBEAT") {
				my $msg = "Heartbeat OK";
				$bytes = send($::s_socket, $msg, 0, $dest);
				&printEvent("CONTROL", "Send heartbeat status to frontend at '$address:$port'", 1);
			} else {
				my $msg = "OK, EXECUTING COMMAND: ".$data[1];
				$bytes = send($::s_socket, $msg, 0, $dest);
				&printEvent("CONTROL", "Sent $bytes bytes to frontend at '$address:$port'", 1);
			}

			if ($data[1] eq "RELOAD") {
				&printEvent("CONTROL", "Re-Reading Configuration by request from Frontend...", 1);
				&reloadConfiguration;
			} 

			if ($data[1] eq "KILL") {
				&printEvent("CONTROL", "SHUTTING DOWN SCRIPT", 1);
				&flushAll;
				die "Exit script by request";
			} 
			
			next;
		}  
		$s_output =~ s/[\r\n\0]//g;	# remove naughty characters
		$s_output =~ s/\[No.C-D\]//g;	# remove [No C-D] tag
		$s_output =~ s/\[OLD.C-D\]//g;	# remove [OLD C-D] tag
		$s_output =~ s/\[NOCL\]//g;	# remove [NOCL] tag

		# Get the server info, if we know the server, otherwise ignore the data
		if (!defined($g_servers{$s_addr})) {
			if (($g_onlyconfig_servers == 1) && (!defined($g_config_servers{$s_addr}))) {
				# HELLRAISER disabled this for testing
				&printEvent(997, "NOT ALLOWED SERVER: " . $s_output);
				next;
			} elsif (!defined($g_config_servers{$s_addr})) { # create std cfg.
				my %std_cfg;
				$std_cfg{"MinPlayers"}						= 6;
				$std_cfg{"HLStatsURL"}						= "";
				$std_cfg{"DisplayResultsInBrowser"}			= 0;
				$std_cfg{"BroadCastEvents"}					= 0;
				$std_cfg{"BroadCastPlayerActions"}			= 0;
				$std_cfg{"BroadCastEventsCommand"}			= "say";
				$std_cfg{"BroadCastEventsCommandAnnounce"}	= "say",
				$std_cfg{"PlayerEvents"}					= 1;
				$std_cfg{"PlayerEventsCommand"}				= "say";
				$std_cfg{"PlayerEventsCommandOSD"}			= "",
				$std_cfg{"PlayerEventsCommandHint"}			= "",
				$std_cfg{"PlayerEventsAdminCommand"}		= "";
				$std_cfg{"ShowStats"}						= 1;
				$std_cfg{"TKPenalty"}						= 50;
				$std_cfg{"SuicidePenalty"}					= 5;
				$std_cfg{"AutoTeamBalance"}					= 0;
				$std_cfg{"AutobanRetry"}					= 0;
				$std_cfg{"TrackServerLoad"}					= 0;
				$std_cfg{"MinimumPlayersRank"}				= 0;
				$std_cfg{"EnablePublicCommands"}			= 1;
				$std_cfg{"Admins"}							= "";
				$std_cfg{"SwitchAdmins"}					= 0;
				$std_cfg{"IgnoreBots"}						= 1;
				$std_cfg{"SkillMode"}						= 0;
				$std_cfg{"GameType"}						= 0;
				$std_cfg{"Mod"}								= "";
				$std_cfg{"BonusRoundIgnore"}				= 0;
				$std_cfg{"BonusRoundTime"}					= 20;
				$std_cfg{"UpdateHostname"}					= 0;
				$std_cfg{"ConnectAnnounce"}					= 1;
				$std_cfg{"DefaultDisplayEvents"}			= 1;
				%{$g_config_servers{$s_addr}}				= %std_cfg;
				&printEvent("CFG", "Created default config for unknown server [$s_addr]");
				&printEvent("DETECT", "New server with game: " . &getServerMod($s_peerhost, $s_peerport));
			}
			
			if ($g_config_servers{$s_addr}) {
				my $tempsrv = &getServer($s_peerhost, $s_peerport);
				next if ($tempsrv == 0);
				$g_servers{$s_addr} = $tempsrv;
				my %s_cfg = %{$g_config_servers{$s_addr}};
				$g_servers{$s_addr}->set("minplayers", $s_cfg{"MinPlayers"});
				$g_servers{$s_addr}->set("hlstats_url", $s_cfg{"HLStatsURL"});
				if ($s_cfg{"DisplayResultsInBrowser"} > 0) {
					$g_servers{$s_addr}->set("use_browser",  1);
					&printEvent("SERVER", "Query results will displayed in valve browser", 1); 
				} else { 
					$g_servers{$s_addr}->set("use_browser",  0);
					&printEvent("SERVER", "Query results will not displayed in valve browser", 1); 
				}
				if ($s_cfg{"ShowStats"} == 1) {
					$g_servers{$s_addr}->set("show_stats",  1);
					&printEvent("SERVER", "Showing stats is enabled", 1); 
				} else {
					$g_servers{$s_addr}->set("show_stats",  0);
					&printEvent("SERVER", "Showing stats is disabled", 1); 
				}
				if ($s_cfg{"BroadCastEvents"} == 1) {  
					$g_servers{$s_addr}->set("broadcasting_events",  1);
					$g_servers{$s_addr}->set("broadcasting_player_actions",  $s_cfg{"BroadCastPlayerActions"});
					$g_servers{$s_addr}->set("broadcasting_command", $s_cfg{"BroadCastEventsCommand"});
					if ($s_cfg{"BroadCastEventsCommandAnnounce"} eq "ma_hlx_csay") {
						$s_cfg{"BroadCastEventsCommandAnnounce"} = $s_cfg{"BroadCastEventsCommandAnnounce"}." #all";
					}
					$g_servers{$s_addr}->set("broadcasting_command_announce", $s_cfg{"BroadCastEventsCommandAnnounce"});

					&printEvent("SERVER", "Broadcasting Live-Events with \"".$s_cfg{"BroadCastEventsCommand"}."\" is enabled", 1); 
					if ($s_cfg{"BroadCastEventsCommandAnnounce"} ne "") {
						&printEvent("SERVER", "Broadcasting Announcements with \"".$s_cfg{"BroadCastEventsCommandAnnounce"}."\" is enabled", 1); 
					}  
				} else {
					$g_servers{$s_addr}->set("broadcasting_events",               0);
					&printEvent("SERVER", "Broadcasting Live-Events is disabled", 1); 
				}
				if ($s_cfg{"PlayerEvents"} == 1) {
					$g_servers{$s_addr}->set("player_events",  1);
					$g_servers{$s_addr}->set("player_command", $s_cfg{"PlayerEventsCommand"});
					$g_servers{$s_addr}->set("player_command_osd", $s_cfg{"PlayerEventsCommandOSD"});
					$g_servers{$s_addr}->set("player_command_hint", $s_cfg{"PlayerEventsCommandHint"});
					$g_servers{$s_addr}->set("player_admin_command", $s_cfg{"PlayerEventsAdminCommand"});
					&printEvent("SERVER", "Player Event-Handler with \"".$s_cfg{"PlayerEventsCommand"}."\" is enabled", 1); 
					if ($s_cfg{"PlayerEventsCommandOSD"} ne "") {
						&printEvent("SERVER", "Displaying amx style menu with \"".$s_cfg{"PlayerEventsCommandOSD"}."\" is enabled", 1); 
					}
				} else {
					$g_servers{$s_addr}->set("player_events",                 0);
					&printEvent("SERVER", "Player Event-Handler is disabled", 1); 
				}
				if ($s_cfg{"DefaultDisplayEvents"} > 0) {
					$g_servers{$s_addr}->set("default_display_events", "1");
					&printEvent("SERVER", "New Players defaulting to show event messages", 1);
				} else {
					$g_servers{$s_addr}->set("default_display_events", "0");
					&printEvent("SERVER", "New Players defaulting to NOT show event messages", 1);
				}
				if ($s_cfg{"TrackServerLoad"} > 0) {
					$g_servers{$s_addr}->set("track_server_load", "1");
					&printEvent("SERVER", "Tracking server load is enabled", 1);
				} else {
					$g_servers{$s_addr}->set("track_server_load", "0");
					&printEvent("SERVER", "Tracking server load is disabled", 1);
				}

				if ($s_cfg{"TKPenalty"} > 0) {
					$g_servers{$s_addr}->set("tk_penalty", $s_cfg{"TKPenalty"});
					&printEvent("SERVER", "Penalty team kills with ".$s_cfg{"TKPenalty"}." points", 1);
				}  
				if ($s_cfg{"SuicidePenalty"} > 0) {
					$g_servers{$s_addr}->set("suicide_penalty", $s_cfg{"SuicidePenalty"});
					&printEvent("SERVER", "Penalty suicides with ".$s_cfg{"SuicidePenalty"}." points", 1);
				}  
				if ($s_cfg{"BonusRoundTime"} > 0) {
					$g_servers{$s_addr}->set("bonusroundtime", $s_cfg{"BonusRoundTime"});
					&printEvent("SERVER", "Bonus Round time set to: ".$s_cfg{"BonusRoundTime"}, 1);
				} 
				if ($s_cfg{"BonusRoundIgnore"} > 0) {
					$g_servers{$s_addr}->set("bonusroundignore", $s_cfg{"BonusRoundIgnore"});
					&printEvent("SERVER", "Bonus Round is being ignored. Length: (".$s_cfg{"BonusRoundTime"}.")", 1);
				}
				if ($s_cfg{"AutoTeamBalance"} > 0) {
					$g_servers{$s_addr}->set("ba_enabled", "1");
					&printEvent("TEAMS", "Auto-Team-Balancing is enabled", 1);
				} else {
					$g_servers{$s_addr}->set("ba_enabled", "0");
					&printEvent("TEAMS", "Auto-Team-Balancing is disabled", 1);
				}
				if ($s_cfg{"AutoBanRetry"} > 0) {
					$g_servers{$s_addr}->set("auto_ban", "1");
					&printEvent("TEAMS", "Auto-Retry-Banning is enabled", 1);
				} else {
					$g_servers{$s_addr}->set("auto_ban", "0");
					&printEvent("TEAMS", "Auto-Retry-Banning is disabled", 1);
				}
				
				if ($s_cfg{"MinimumPlayersRank"} > 0) {
					$g_servers{$s_addr}->set("min_players_rank", $s_cfg{"MinimumPlayersRank"});
					&printEvent("SERVER", "Requires minimum players rank is enabled [MinPos:".$s_cfg{"MinimumPlayersRank"}."]", 1);
				} else {
					$g_servers{$s_addr}->set("min_players_rank", "0");
					&printEvent("SERVER", "Requires minimum players rank is disabled", 1);
				}
				
				if ($s_cfg{"EnablePublicCommands"} > 0) {
					$g_servers{$s_addr}->set("public_commands", $s_cfg{"EnablePublicCommands"});
					&printEvent("SERVER", "Public chat commands are enabled", 1);
				} else {
					$g_servers{$s_addr}->set("public_commands", "0");
					&printEvent("SERVER", "Public chat commands are disabled", 1);
				}

				if ($s_cfg{"Admins"} ne "") {
					@{$g_servers{$s_addr}->{admins}} = split(/,/, $s_cfg{"Admins"});
					foreach(@{$g_servers{$s_addr}->{admins}})
					{
						$_ =~ s/^STEAM_[0-9]+?\://i;
					}
					&printEvent("SERVER", "Admins: ".$s_cfg{"Admins"}, 1);
				}

				if ($s_cfg{"SwitchAdmins"} > 0) {
					$g_servers{$s_addr}->set("switch_admins", "1");
					&printEvent("TEAMS", "Switching Admins on Auto-Team-Balance is enabled", 1);
				} else {
					$g_servers{$s_addr}->set("switch_admins", "0");
					&printEvent("TEAMS", "Switching Admins on Auto-Team-Balance is disabled", 1);
				}
				
				if ($s_cfg{"IgnoreBots"} > 0) {
					$g_servers{$s_addr}->set("ignore_bots", "1");
					&printEvent("SERVER", "Ignoring bots is enabled", 1);
				} else {
					$g_servers{$s_addr}->set("ignore_bots", "0");
					&printEvent("SERVER", "Ignoring bots is disabled", 1);
				}
				$g_servers{$s_addr}->set("skill_mode", $s_cfg{"SkillMode"});
				&printEvent("SERVER", "Using skill mode ".$s_cfg{"SkillMode"}, 1);
				
				if ($s_cfg{"GameType"} == 1) {
					$g_servers{$s_addr}->set("game_type", $s_cfg{"GameType"});
					&printEvent("SERVER", "Game type: Counter-Strike: Source - Deathmatch", 1);
				} else {
					$g_servers{$s_addr}->set("game_type", "0");
					&printEvent("SERVER", "Game type: Normal", 1);
				}

				$g_servers{$s_addr}->set("mod", $s_cfg{"Mod"});
				
				if ($s_cfg{"Mod"} ne "") {
					&printEvent("SERVER", "Using plugin ".$s_cfg{"Mod"}." for internal functions!", 1);
				}
				if ($s_cfg{"ConnectAnnounce"} == 1) {
					$g_servers{$s_addr}->set("connect_announce", $s_cfg{"ConnectAnnounce"});
					&printEvent("SERVER", "Connect Announce is enabled", 1);
				} else {
					$g_servers{$s_addr}->set("connect_announce", "0");
					&printEvent("SERVER", "Connect Announce is disabled", 1);
				}
				if ($s_cfg{"UpdateHostname"} == 1) {
					$g_servers{$s_addr}->set("update_hostname", $s_cfg{"UpdateHostname"});
					&printEvent("SERVER", "Auto-updating hostname is enabled", 1);
				} else {
					$g_servers{$s_addr}->set("update_hostname", "0");
					&printEvent("SERVER", "Auto-updating hostname is disabled", 1);
				}
				$g_servers{$s_addr}->get_game_mod_opts();
			}
		}

		if (!$g_servers{$s_addr}->{"srv_players"})
		{
			$g_servers{$s_addr}->{"srv_players"} = ();
			%g_players=();
		}
		else
		{	
			%g_players=%{$g_servers{$s_addr}->{"srv_players"}};
		}
		
		# Get the datestamp (or complain)
		#if ($s_output =~ s/^.*L (\d\d)\/(\d\d)\/(\d{4}) - (\d\d):(\d\d):(\d\d):\s*//)
		
		#$is_streamed = 0;
		#$test_for_date = 0;
		#$is_streamed = ($s_output !~ m/^L\s*/);

		#if ( !$is_streamed ) {
		# $test_for_date = ($s_output =~ s/^L (\d\d)\/(\d\d)\/(\d{4}) - (\d\d):(\d\d):(\d\d):\s*//);
		#} else {
		# $test_for_date = ($s_output =~ s/^\S*L (\d\d)\/(\d\d)\/(\d{4}) - (\d\d):(\d\d):(\d\d):\s*//);
		#}
		
		#if ($test_for_date)
		
		# EXPLOIT FIX
		
		if ($s_output =~ s/^(?:.*?)?L (\d\d)\/(\d\d)\/(\d{4}) - (\d\d):(\d\d):(\d\d):\s*//) {
			$ev_month = $1;
			$ev_day   = $2;
			$ev_year  = $3;
			$ev_hour  = $4;
			$ev_min   = $5;
			$ev_sec   = $6;
			$ev_time  = "$ev_hour:$ev_min:$ev_sec";
			$ev_remotetime  = timelocal($ev_sec,$ev_min,$ev_hour,$ev_day,$ev_month-1,$ev_year);
			
			if ($g_timestamp) {
				$ev_timestamp = "$ev_year-$ev_month-$ev_day $ev_time";
				$ev_unixtime  = $ev_remotetime;
				if ($g_stdin)
				{
					$ev_daemontime = $ev_unixtime;
				}
			}
		} else {
			&printEvent(998, "MALFORMED DATA: " . $s_output);
			next;
		}
		
		if ($g_debug >= 4) {
			print $s_addr.": \"".$s_output."\"\n";
		}
		
		if (($g_stdin == 0) && ($g_servers{$s_addr}->{last_event} > 0) && ( ($ev_unixtime - $g_servers{$s_addr}->{last_event}) > 299) ) {
			$g_servers{$s_addr}->set("map", "");
			$g_servers{$s_addr}->get_map();
		}
		
		$g_servers{$s_addr}->set("last_event", $ev_unixtime);

		# Now we parse the events.
		
		my $ev_type   = 0;
		my $ev_status = "";
		my $ev_team   = "";
		my $ev_player = 0;
		my $ev_verb   = "";
		my $ev_obj_a  = "";
		my $ev_obj_b  = "";
		my $ev_obj_c  = "";
		my $ev_properties = "";
		my %ev_properties = ();
		my %ev_player = ();
	
		# pvkii parrot log lines also fit the death line parsing
		if ($g_servers{$s_addr}->{play_game} == PVKII()
			&& $s_output =~ /^
				"(.+?(?:<[^>]*>){3})"		# player string
				\s[a-z]{6}\s				# 'killed'
				"npc_parrot<.+?>"			# parrot string
				\s[a-z]{5}\s[a-z]{2}\s		# 'owned by'
				"(.+?(?:<[^>]*>){3})"		# owner string
				\s[a-z]{4}\s				# 'with'
				"([^"]*)"				#weapon
				(.*)					#properties
				/x)
		{
			$ev_player = $1; # player
			$ev_obj_b  = $2; # victim
			$ev_obj_c  = $3; # weapon
			$ev_properties = $4;
			%ev_properties_hash = &getProperties($ev_properties);
			
			my $playerinfo = &getPlayerInfo($ev_player, 1);
			my $victiminfo = &getPlayerInfo($ev_obj_b, 1);
			$ev_type = 10;

			if ($playerinfo) {
				if ($victiminfo) {
					$ev_status = &doEvent_PlayerPlayerAction(
						$playerinfo->{"userid"},
						$playerinfo->{"uniqueid"},
						$victiminfo->{"userid"},
						$victiminfo->{"uniqueid"},
						"killed_parrot",
						undef,
						undef,
						undef,
						undef,
						undef,
						undef,
						&getProperties($ev_properties)
					);
				}

				$ev_type = 11;
				
				$ev_status = &doEvent_PlayerAction(
					$playerinfo->{"userid"},
					$playerinfo->{"uniqueid"},
					"killed_parrot",
					undef,
					undef,
					undef,
					&getProperties($ev_properties)
				);
			}
		} elsif ($s_output =~ /^
				(?:\(DEATH\))?		# l4d prefix, such as (DEATH) or (INCAP)
				"(.+?(?:<.+?>)*?
				(?:<setpos_exact\s(-?\d+?\.\d\d)\s(-?\d+?\.\d\d)\s(-?\d+?\.\d\d);[^"]*)?
				)"						# player string with or without l4d-style location coords
				(?:\s\[(-?\d+)\s(-?\d+)\s(-?\d+)\])?
				\skilled\s			# verb (ex. attacked, killed, triggered)
				"(.+?(?:<.+?>)*?
				(?:<setpos_exact\s(-?\d+?\.\d\d)\s(-?\d+?\.\d\d)\s(-?\d+?\.\d\d);[^"]*)?
				)"						# player string as above or action name
				(?:\s\[(-?\d+)\s(-?\d+)\s(-?\d+)\])?
				\swith\s				# (ex. with, against)
				"([^"]*)"
				(.*)					#properties
				/x)
		{

			# Prototype: "player" verb "obj_a" ?... "obj_b"[properties]
			# Matches:
			#  8. Kills
			
			$ev_player = $1;
			$ev_Xcoord = $2; # attacker/player coords (L4D)
			$ev_Ycoord = $3;
			$ev_Zcoord = $4;
			if( !defined($ev_Xcoord) ) {
				# if we didn't get L4D style, overwrite with CSGO style (which we may still not have)
				$ev_Xcoord = $5;
				$ev_Ycoord = $6;
				$ev_Zcoord = $7;
			}
			$ev_obj_a  = $8; # victim
			$ev_XcoordKV = $9; # kill victim coords (L4D)
			$ev_YcoordKV = $10;
			$ev_ZcoordKV = $11;
			if( !defined($ev_XcoordKV) ) {
				$ev_XcoordKV = $12; # kill victim coords (CSGO)
				$ev_YcoordKV = $13;
				$ev_ZcoordKV = $14;
			}
			$ev_obj_b  = $15; # weapon
			$ev_properties = $16;
			%ev_properties_hash = &getProperties($ev_properties);
			
			my $killerinfo = &getPlayerInfo($ev_player, 1);
			my $victiminfo = &getPlayerInfo($ev_obj_a, 1);
			$ev_type = 8;
			
			$headshot = 0;
			if ($ev_properties =~ m/headshot/) {
				$headshot = 1;
			}
			
			if ($killerinfo && $victiminfo) {
				my $killerId       = $killerinfo->{"userid"};
				my $killerUniqueId = $killerinfo->{"uniqueid"};
				my $killer         = lookupPlayer($s_addr, $killerId, $killerUniqueId);
				
				# octo
				if($killer->{role} eq "scout") {
					$ev_status = &doEvent_PlayerAction(
						$killerinfo->{"userid"},
						$killerinfo->{"uniqueid"},
						"kill_as_scout",
						"kill_as_scout"
					);
				}
				if($killer->{role} eq "spy") {
					$ev_status = &doEvent_PlayerAction(
						$killerinfo->{"userid"},
						$killerinfo->{"uniqueid"},
						"kill_as_spy",
						"kill_as_spy"
					);
				}

				my $victimId       = $victiminfo->{"userid"};
				my $victimUniqueId = $victiminfo->{"uniqueid"};
				my $victim         = lookupPlayer($s_addr, $victimId, $victimUniqueId);

				$ev_status = &doEvent_Frag(
					$killerinfo->{"userid"},
					$killerinfo->{"uniqueid"},
					$victiminfo->{"userid"},
					$victiminfo->{"uniqueid"},
					$ev_obj_b,
					$headshot,
					$ev_Xcoord,
					$ev_Ycoord,
					$ev_Zcoord,
					$ev_XcoordKV,
					$ev_YcoordKV,
					$ev_ZcoordKV,
					%ev_properties_hash
				);
			} 
		} elsif ($g_servers{$s_addr}->{play_game} == L4D() && $s_output =~ /^
				\(INCAP\)		# l4d prefix, such as (DEATH) or (INCAP)
				"(.+?(?:<.+?>)*?
				<setpos_exact\s(-?\d+?\.\d\d)\s(-?\d+?\.\d\d)\s(-?\d+?\.\d\d);[^"]*
				)"						# player string with or without l4d-style location coords
				\swas\sincapped\sby\s			# verb (ex. attacked, killed, triggered)
				"(.+?(?:<.+?>)*?
				<setpos_exact\s(-?\d+?\.\d\d)\s(-?\d+?\.\d\d)\s(-?\d+?\.\d\d);[^"]*
				)"						# player string as above or action name
				\swith\s				# (ex. with, against)
				"([^"]*)"					# weapon name
				(.*)					#properties
				/x)
		{
			#  800. L4D Incapacitation
			
			$ev_player = $1;
			$ev_l4dXcoord = $2; # attacker/player coords (L4D)
			$ev_l4dYcoord = $3;
			$ev_l4dZcoord = $4;
			$ev_obj_a  = $5; # victim
			$ev_l4dXcoordKV = $6; # kill victim coords (L4D)
			$ev_l4dYcoordKV = $7;
			$ev_l4dZcoordKV = $8;
			$ev_obj_b  = $9; # weapon
			$ev_properties = $10;
			%ev_properties_hash = &getProperties($ev_properties);
			
			# reverse killer/victim (x was incapped by y = y killed x)
			my $killerinfo = &getPlayerInfo($ev_obj_a, 1);
			my $victiminfo = &getPlayerInfo($ev_player, 1);
			
			if ($victiminfo->{team} eq "Infected") {
				$victiminfo = undef;
			}
			$ev_type = 800;
							
			$headshot = 0;
			if ($ev_properties =~ m/headshot/) {
				$headshot = 1;
			}
			if ($killerinfo && $victiminfo) {
				my $killerId       = $killerinfo->{"userid"};
				my $killerUniqueId = $killerinfo->{"uniqueid"};
				my $killer         = lookupPlayer($s_addr, $killerId, $killerUniqueId);

				my $victimId       = $victiminfo->{"userid"};
				my $victimUniqueId = $victiminfo->{"uniqueid"};
				my $victim         = lookupPlayer($s_addr, $victimId, $victimUniqueId);

				$ev_status = &doEvent_Frag(
					$killerinfo->{"userid"},
					$killerinfo->{"uniqueid"},
					$victiminfo->{"userid"},
					$victiminfo->{"uniqueid"},
					$ev_obj_b,
					$headshot,
					$ev_l4dXcoord,
					$ev_l4dYcoord,
					$ev_l4dZcoord,
					$ev_l4dXcoordKV,
					$ev_l4dYcoordKV,
					$ev_l4dZcoordKV,
					&getProperties($ev_properties)
				);
			}
		} elsif ($g_servers{$s_addr}->{play_game} == L4D() && $s_output =~ /^\(TONGUE\)\sTongue\sgrab\sstarting\.\s+Smoker:"(.+?(?:<.+?>)*?(?:|<setpos_exact ((?:|-)\d+?\.\d\d) ((?:|-)\d+?\.\d\d) ((?:|-)\d+?\.\d\d);.*?))"\.\s+Victim:"(.+?(?:<.+?>)*?(?:|<setpos_exact ((?:|-)\d+?\.\d\d) ((?:|-)\d+?\.\d\d) ((?:|-)\d+?\.\d\d);.*?))".*/) {
			# Prototype: (TONGUE) Tongue grab starting.  Smoker:"player". Victim:"victim".
			# Matches:
			# 11. Player Action
			
			$ev_player = $1;
			$ev_l4dXcoord = $2;
			$ev_l4dYcoord = $3;
			$ev_l4dZcoord = $4;
			$ev_victim = $5;
			$ev_l4dXcoordV = $6;
			$ev_l4dYcoordV = $7;
			$ev_l4dZcoordV = $8;
			
			$playerinfo = &getPlayerInfo($ev_player, 1);
			$victiminfo = &getPlayerInfo($ev_victim, 1);

			$ev_type = 11;
				
			if ($playerinfo) {
				$ev_status = &doEvent_PlayerAction(
					$playerinfo->{"userid"},
					$playerinfo->{"uniqueid"},
					"tongue_grab"
				);
			}
			if ($playerinfo && $victiminfo) {
					$ev_status = &doEvent_PlayerPlayerAction(
						$playerinfo->{"userid"},
						$playerinfo->{"uniqueid"},
						$victiminfo->{"userid"},
						$victiminfo->{"uniqueid"},
						"tongue_grab",
						$ev_l4dXcoord,
						$ev_l4dYcoord,
						$ev_l4dZcoord,
						$ev_l4dXcoordV,
						$ev_l4dYcoordV,
						$ev_l4dZcoordV
					);
			}
		} elsif ($s_output =~ /^
				"(.+?(?:<.+?>)*?
				)"						# player string
				\s(triggered(?:\sa)?)\s			# verb (ex. attacked, killed, triggered)
				"(.+?(?:<.+?>)*?
				)"						# player string as above or action name
				\s[a-zA-Z]+\s				# (ex. with, against)
				"(.+?(?:<.+?>)*?
				)"						# player string as above or weapon name
				(?:\s[a-zA-Z]+\s"(.+?)")?	# weapon name on plyrplyr actions
				(.*)					#properties
				/x)
		{			
		
			# 10. Player-Player Actions
			
			# no l4d/2 actions are logged with the locations (in fact, very few are logged period) so the l4d/2 location parsing can be skipped
			
			$ev_player = $1;
			$ev_verb   = $2; # triggered or triggered a
			$ev_obj_a  = $3; # action
			$ev_obj_b  = $4; # victim
			$ev_obj_c  = $5; # weapon (optional)
			$ev_properties = $6;
			%ev_properties_hash = &getProperties($ev_properties);
			

			if ($ev_verb eq "triggered") {  # it's either 'triggered' or 'triggered a'
			
				my $playerinfo = &getPlayerInfo($ev_player, 1);
				my $victiminfo = &getPlayerInfo($ev_obj_b, 1);
				$ev_type = 10;

				if ($playerinfo) {
					if ($victiminfo) {
						$ev_status = &doEvent_PlayerPlayerAction(
							$playerinfo->{"userid"},
							$playerinfo->{"uniqueid"},
							$victiminfo->{"userid"},
							$victiminfo->{"uniqueid"},
							$ev_obj_a,
							undef,
							undef,
							undef,
							undef,
							undef,
							undef,
							&getProperties($ev_properties)
						);
					}

					$ev_type = 11;
					
					$ev_status = &doEvent_PlayerAction(
						$playerinfo->{"userid"},
						$playerinfo->{"uniqueid"},
						$ev_obj_a,
						undef,
						undef,
						undef,
						&getProperties($ev_properties)
					);
				}
			} else {
				my $playerinfo = &getPlayerInfo($ev_player, 1);
				$ev_type = 11;
				
				if ($playerinfo) {
					$ev_status = &doEvent_PlayerAction(
						$playerinfo->{"userid"},
						$playerinfo->{"uniqueid"},
						$ev_obj_a,
						undef,
						undef,
						undef,
				        &getProperties($ev_properties)
					);
				}
			}
		} elsif ( $s_output =~ /^(?:\[STATSME\] )?"(.+?(?:<.+?>)*)" triggered "(weaponstats\d{0,1})"(.*)$/ ) {
			# Prototype: [STATSME] "player" triggered "weaponstats?"[properties]
			# Matches:
			# 501. Statsme weaponstats
			# 502. Statsme weaponstats2
	
			$ev_player = $1;
			$ev_verb   = $2; # weaponstats; weaponstats2
			$ev_properties = $3;
			%ev_properties = &getProperties($ev_properties);
	
			if (like($ev_verb, "weaponstats")) {
				$ev_type = 501;
				my $playerinfo = &getPlayerInfo($ev_player, 0);
				
				if ($playerinfo) {
					my $playerId = $playerinfo->{"userid"};
					my $playerUniqueId = $playerinfo->{"uniqueid"};
					my $ingame = 0;
					
					$ingame = 1 if (lookupPlayer($s_addr, $playerId, $playerUniqueId));
					
					if (!$ingame) {
						&getPlayerInfo($ev_player, 1);
					}
					
					$ev_status = &doEvent_Statsme(
						$playerId,
						$playerUniqueId,
						$ev_properties{"weapon"},
						$ev_properties{"shots"},
						$ev_properties{"hits"},
						$ev_properties{"headshots"},
						$ev_properties{"damage"},
						$ev_properties{"kills"},
						$ev_properties{"deaths"}
					);

					if (!$ingame) {
						&doEvent_Disconnect(
							$playerId,
							$playerUniqueId,
							""
						);
					}
				}
			} elsif (like($ev_verb, "weaponstats2")) {
				$ev_type = 502;
				my $playerinfo = &getPlayerInfo($ev_player, 0);
				
				if ($playerinfo) {
					my $playerId = $playerinfo->{"userid"};
					my $playerUniqueId = $playerinfo->{"uniqueid"};
					my $ingame = 0;
					
					$ingame = 1 if (lookupPlayer($s_addr, $playerId, $playerUniqueId));
					
					if (!$ingame) {
						&getPlayerInfo($ev_player, 1);
					}
					
					$ev_status = &doEvent_Statsme2(
						$playerId,
						$playerUniqueId,
						$ev_properties{"weapon"},
						$ev_properties{"head"},
						$ev_properties{"chest"},
						$ev_properties{"stomach"},
						$ev_properties{"leftarm"},
						$ev_properties{"rightarm"},
						$ev_properties{"leftleg"},
						$ev_properties{"rightleg"}
					);
					
					if (!$ingame) {
						&doEvent_Disconnect(
							$playerId,
							$playerUniqueId,
							""
						);
					}
				}
			}
		} elsif ( $s_output =~ /^(?:\[STATSME\] )?"(.+?(?:<.+?>)*)" triggered "(latency|time)"(.*)$/ ) {
			# Prototype: [STATSME] "player" triggered "latency|time"[properties]
			# Matches:
			# 503. Statsme latency
			# 504. Statsme time
	
			$ev_player = $1;
			$ev_verb   = $2; # latency; time
			$ev_properties = $3;
			%ev_properties = &getProperties($ev_properties);
	
			if ($ev_verb eq "time") {
				$ev_type = 504;
				my $playerinfo = &getPlayerInfo($ev_player, 0);
	
				if ($playerinfo) {
					my ($min, $sec) = split(/:/, $ev_properties{"time"});
					my $hour = sprintf("%d", $min / 60);
	
					if ($hour) {
						$min = $min % 60;
					}
	
					$ev_status = &doEvent_Statsme_Time(
						$playerinfo->{"userid"},
						$playerinfo->{"uniqueid"},
						"$hour:$min:$sec"
					);
				}
			} else { # latency
				$ev_type = 503;
				my $playerinfo = &getPlayerInfo($ev_player, 0);
	
				if ($playerinfo) {
					$ev_status = &doEvent_Statsme_Latency(
						$playerinfo->{"userid"},
						$playerinfo->{"uniqueid"},
						$ev_properties{"ping"}
					);
				}
			}
		} elsif ($s_output =~ /^"(.+?(?:<.+?>)*?)" ([a-zA-Z,_\s]+) "(.+?)"(.*)$/) {
			# Prototype: "player" verb "obj_a"[properties]
			# Matches:
			#  1. Connection
			#  4. Suicides
			#  5. Team Selection
			#  6. Role Selection
			#  7. Change Name
			# 11. Player Objectives/Actions
			# 14. a) Chat; b) Team Chat
			
			$ev_player = $1;
			$ev_verb   = $2;
			$ev_obj_a  = $3;
			$ev_properties = $4;
			%ev_properties = &getProperties($ev_properties);
			
			if ($ev_verb eq "connected, address") {
				my $ipAddr = $ev_obj_a;
				my $playerinfo;
				
				if ($ipAddr =~ /([\d\.]+):(\d+)/) {
					$ipAddr = $1;
				}
				
				$playerinfo = &getPlayerInfo($ev_player, 1, $ipAddr);
				
				$ev_type = 1;
				
				if ($playerinfo) {
					if (($playerinfo->{"uniqueid"} =~ /UNKNOWN/) || ($playerinfo->{"uniqueid"} =~ /PENDING/) || ($playerinfo->{"uniqueid"} =~ /VALVE_ID_LAN/)) {
						$ev_status = "(DELAYING CONNECTION): $s_output";
	
	                    if ($g_mode ne "LAN")  {
							my $p_name   = $playerinfo->{"name"};
							my $p_userid = $playerinfo->{"userid"};
							&printEvent("SERVER", "LATE CONNECT [$p_name/$p_userid] - STEAM_ID_PENDING");
							$g_preconnect->{"$s_addr/$p_userid/$p_name"} = {
								ipaddress => $ipAddr,
								name => $p_name,
								server => $s_addr,
								timestamp => $ev_daemontime
							};
						}   
					} else {
						$ev_status = &doEvent_Connect(
							$playerinfo->{"userid"},
							$playerinfo->{"uniqueid"},
							$ipAddr
						);
					}
				}
			} elsif ($ev_verb eq "committed suicide with") {
				my $playerinfo = &getPlayerInfo($ev_player, 1);
				
				$ev_type = 4;
				
				if ($playerinfo) {
					$ev_status = &doEvent_Suicide(
						$playerinfo->{"userid"},
						$playerinfo->{"uniqueid"},
						$ev_obj_a,
						%ev_properties
					);
				}
			} elsif ($ev_verb eq "joined team") {
				my $playerinfo = &getPlayerInfo($ev_player, 1);
				
				$ev_type = 5;
				
				if ($playerinfo) {
					$ev_status = &doEvent_TeamSelection(
						$playerinfo->{"userid"},
						$playerinfo->{"uniqueid"},
						$ev_obj_a
					);
				}
			} elsif ($ev_verb eq "changed role to") {
				my $playerinfo = &getPlayerInfo($ev_player, 1);
				
				$ev_type = 6;
				
				if ($playerinfo) {
					$ev_status = &doEvent_RoleSelection(
						$playerinfo->{"userid"},
						$playerinfo->{"uniqueid"},
						$ev_obj_a
					);
				}
			} elsif ($ev_verb eq "changed name to") {
				my $playerinfo = &getPlayerInfo($ev_player, 1);
				
				$ev_type = 7;
				
				if ($playerinfo) {
					$ev_status = &doEvent_ChangeName(
						$playerinfo->{"userid"},
						$playerinfo->{"uniqueid"},
						$ev_obj_a
					);
				}
			} elsif ($ev_verb eq "triggered") {

			    # in cs:s players dropp the bomb if they are the only ts
			    # and disconnect...the dropp the bomb after they disconnected :/
			    if ($ev_obj_a eq "Dropped_The_Bomb") {
				  $playerinfo = &getPlayerInfo($ev_player, 0);
			    } else {
				  $playerinfo = &getPlayerInfo($ev_player, 1);
				}
				if ($playerinfo) {
					if ($ev_obj_a eq "player_changeclass" && defined($ev_properties{newclass})) {
						
						$ev_type = 6;
						
						$ev_status = &doEvent_RoleSelection(
							$playerinfo->{"userid"},
							$playerinfo->{"uniqueid"},
							$ev_properties{newclass}
						);
					} else {					
						if ($g_servers{$s_addr}->{play_game} == TFC())
						{
							if ($ev_obj_a eq "Sentry_Destroyed")
							{
								$ev_obj_a = "Sentry_Dismantle";
							}
							elsif ($ev_obj_a eq "Dispenser_Destroyed")
							{
								$ev_obj_a = "Dispenser_Dismantle";
							}
							elsif ($ev_obj_a eq "Teleporter_Entrance_Destroyed")
							{
								$ev_obj_a = "Teleporter_Entrance_Dismantle"
							}
							elsif ($ev_obj_a eq "Teleporter_Exit_Destroyed")
							{
								$ev_obj_a = "Teleporter_Exit_Dismantle"
							}
						}
						
						$ev_type = 11;
				
						$ev_status = &doEvent_PlayerAction(
							$playerinfo->{"userid"},
							$playerinfo->{"uniqueid"},
							$ev_obj_a,
							undef,
							undef,
							undef,
							%ev_properties
						);
					}
				}
			} elsif ($ev_verb eq "triggered a") {
				my $playerinfo = &getPlayerInfo($ev_player, 1);
				
				$ev_type = 11;
				
				if ($playerinfo)
				{
					$ev_status = &doEvent_PlayerAction(
						$playerinfo->{"userid"},
						$playerinfo->{"uniqueid"},
						$ev_obj_a,
						undef,
						undef,
						undef,
						%ev_properties
					);
				}
			} elsif ($ev_verb eq "say" || $ev_verb eq "say_team" || $ev_verb eq "say_squad") {
				my $playerinfo = &getPlayerInfo($ev_player, 1);
				
				$ev_type = 14;
				
				if ($playerinfo) {
					$ev_status = &doEvent_Chat(
						$ev_verb,
						$playerinfo->{"userid"},
						$playerinfo->{"uniqueid"},
						$ev_obj_a
					);
				}
			}
		} elsif ($s_output =~ /^(?:Kick: )?"(.+?(?:<.+?>)*)" ([^\(]+)(.*)$/) {
			# Prototype: "player" verb[properties]
			# Matches:
			#  2. Enter Game
			#  3. Disconnection
			
			$ev_player = $1;
			$ev_verb   = $2;
			$ev_properties = $3;
			%ev_properties = &getProperties($ev_properties);
			
			if (like($ev_verb, "entered the game")) {
				my $playerinfo = &getPlayerInfo($ev_player, 1);
				
				if ($playerinfo) {
					$ev_type = 2;
					$ev_status = &doEvent_EnterGame($playerinfo->{"userid"}, $playerinfo->{"uniqueid"}, $ev_obj_a);
				}
			} elsif (like($ev_verb, "disconnected") || like($ev_verb, "was kicked")) {
				my $playerinfo = &getPlayerInfo($ev_player, 0);
				
				if ($playerinfo) {
					$ev_type = 3;
	
					$userid   = $playerinfo->{userid};
					$uniqueid = $playerinfo->{uniqueid};
	
					$ev_status = &doEvent_Disconnect(
						$playerinfo->{userid},
						$playerinfo->{uniqueid},
						$ev_properties
					);
				}
			}
			elsif (like($ev_verb, "STEAM USERID validated") || like($ev_verb, "VALVE USERID validated")) {               
				my $playerinfo = &getPlayerInfo($ev_player, 0);
	
				if ($playerinfo) {                       
					$ev_type = 1;
					
				}
			}
		} elsif ($s_output =~ /^Team "(.+?)" ([^"\(]+) "([^"]+)"(.*)$/) {
			# Prototype: Team "team" verb "obj_a"[properties]
			# Matches:
			# 12. Team Objectives/Actions
			# 1200. Team Objective With Players involved
			# 15. Team Alliances
			
			$ev_team   = $1;
			$ev_verb   = $2;
			$ev_obj_a  = $3;
			$ev_properties = $4;
			%ev_properties_hash = &getProperties($ev_properties);
			
			if ($ev_obj_a eq "pointcaptured") {
				$numcappers = $ev_properties_hash{numcappers};
				if ($g_debug > 1) {
					print "NumCappers = ".$numcappers."\n";
				}
				foreach ($i = 1; $i <= $numcappers; $i++) {
					# reward each player involved in capturing
					$player = $ev_properties_hash{"player".$i};
					if ($g_debug > 1) {
						print $i." -> ".$player."\n";
					}
					#$position = $ev_properties_hash{"position".$i};
					my $playerinfo = &getPlayerInfo($player, 1);
					if ($playerinfo) {
						$ev_status = &doEvent_PlayerAction(
							$playerinfo->{"userid"},
							$playerinfo->{"uniqueid"},
							$ev_obj_a,
							"",
							"",
							"",
							&getProperties($ev_properties)
						);
					}
				}
			}
 			if ($ev_obj_a eq "captured_loc") {
			#	$flag_name = $ev_properties_hash{flagname};
				$player_a  = $ev_properties_hash{player_a};
				$player_b  = $ev_properties_hash{player_b};
  			  
				my $playerinfo_a = &getPlayerInfo($player_a, 1);
				if ($playerinfo_a) {
					$ev_status = &doEvent_PlayerAction(
						$playerinfo_a->{"userid"},
						$playerinfo_a->{"uniqueid"},
						$ev_obj_a,
						"",
						"",
						"",
						&getProperties($ev_properties)
					);
				}

				my $playerinfo_b = &getPlayerInfo($player_b, 1);
				if ($playerinfo_b) {
					$ev_status = &doEvent_PlayerAction(
						$playerinfo_b->{"userid"},
						$playerinfo_b->{"uniqueid"},
						$ev_obj_a,
						"",
						"",
						"",
						&getProperties($ev_properties)
					);
				}
			}  
			
			if (like($ev_verb, "triggered")) {
				if ($ev_obj_a ne "captured_loc") {
					$ev_type = 12;
					$ev_status = &doEvent_TeamAction(
						$ev_team,
						$ev_obj_a,
						&getProperties($ev_properties)
					);
				}
			} elsif (like($ev_verb, "triggered a")) {
				$ev_type = 12;
				$ev_status = &doEvent_TeamAction(
					$ev_team,
					$ev_obj_a
				);
			}
		} elsif ($s_output =~ /^(Rcon|Bad Rcon): "rcon [^"]+"([^"]+)"\s+(.+)" from "([0-9\.]+?):(\d+?)"(.*)$/) {
			# Prototype: verb: "rcon ?..."obj_a" obj_b" from "obj_c"[properties]
			# Matches:
		    # 20. HL1 a) Rcon; b) Bad Rcon
			
			$ev_verb   = $1;
			$ev_obj_a  = $2; # password
			$ev_obj_b  = $3; # command
			$ev_obj_c  = $4; # ip
			$ev_obj_d  = $5; # port
			$ev_properties = $6;
			%ev_properties = &getProperties($ev_properties);
			if ($g_rcon_ignoreself == 0 || $ev_obj_c ne $s_ip) {
				$ev_obj_b = substr($ev_obj_b, 0, 255);
				if (like($ev_verb, "Rcon")) {
					$ev_type = 20;
					$ev_status = &doEvent_Rcon(
						"OK",
						$ev_obj_b,
						"",
						$ev_obj_c
					);
				} elsif (like($ev_verb, "Bad Rcon")) {
					$ev_type = 20;
					$ev_status = &doEvent_Rcon(
						"BAD",
						$ev_obj_b,
						$ev_obj_a,
						$ev_obj_c
					);
				}
			} else {
				$ev_status = "(IGNORED) Rcon from \"$ev_obj_a:$ev_obj_b\": \"$ev_obj_c\"";
			}
		} elsif ($s_output =~ /^rcon from "(.+?):(.+?)": (?:command "(.*)".*|(Bad) Password)$/) {
			# Prototype: verb: "rcon ?..."obj_a" obj_b" from "obj_c"[properties]
			# Matches:
		    # 20. a) Rcon;
			
			$ev_obj_a  = $1; # ip
			$ev_obj_b  = $2; # port
			$ev_obj_c  = $3; # command
			$ev_isbad  = $4; # if bad, "Bad"
			if ($g_rcon_ignoreself == 0 || $ev_obj_a ne $s_ip) {
				if ($ev_isbad ne "Bad") {
					$ev_type = 20;
					@cmds = split(/;/,$ev_obj_c);
					foreach(@cmds)
					{
						$ev_status = &doEvent_Rcon(
							"OK",
							substr($_, 0, 255),
							"",
							$ev_obj_a
						);
					}
				} else {
					$ev_type = 20;
					$ev_status = &doEvent_Rcon(
						"BAD",
						"",
						"",
						$ev_obj_a
					);
				}
			} else {
				$ev_status = "(IGNORED) Rcon from \"$ev_obj_a:$ev_obj_b\": \"$ev_obj_c\"";
			}
		} elsif ($s_output =~ /^\[(.+)\.(smx|amxx)\]\s*(.+)$/i) {
			# Prototype: Cmd:[SM] obj_a
			# Matches:
		    # Admin Mod messages
			
			my $ev_plugin = $1;
			my $ev_adminmod = $2;
			$ev_obj_a  = $3;
			$ev_type = 500;
			$ev_status = &doEvent_Admin(
				(($ev_adminmod eq "smx")?"Sourcemod":"AMXX")." ($ev_plugin)",
				substr($ev_obj_a, 0, 255)
			);
		} elsif ($s_output =~ /^([^"\(]+) "([^"]+)"(.*)$/) {
			# Prototype: verb "obj_a"[properties]
			# Matches:
			# 13. World Objectives/Actions
			# 19. a) Loading map; b) Started map
			# 21. Server Name
			
			$ev_verb   = $1;
			$ev_obj_a  = $2;
			$ev_properties = $3;
			%ev_properties = &getProperties($ev_properties);
			
			if (like($ev_verb, "World triggered")) {
				$ev_type = 13;
				if ($ev_obj_a eq "killlocation") {
					$ev_status = &doEvent_Kill_Loc(
						%ev_properties
					);
				} else {
					$ev_status = &doEvent_WorldAction(
						$ev_obj_a
					);
					if ($ev_obj_a eq "Round_Win" || $ev_obj_a eq "Mini_Round_Win") {
						$ev_team = $ev_properties{"winner"};
						$ev_status = &doEvent_TeamAction(
						$ev_team,
						$ev_obj_a
						);
					}
				}
			} elsif (like($ev_verb, "Loading map")) {
				$ev_type = 19;
				$ev_status = &doEvent_ChangeMap(
					"loading",
					$ev_obj_a
				);
			} elsif (like($ev_verb, "Started map")) {
				$ev_type = 19;
				$ev_status = &doEvent_ChangeMap(
					"started",
					$ev_obj_a
				);
			}
		} elsif ($s_output =~ /^\[MANI_ADMIN_PLUGIN\]\s*(.+)$/) {
			# Prototype: [MANI_ADMIN_PLUGIN] obj_a
			# Matches:
		    # Mani-Admin-Plugin messages
			
			$ev_obj_a  = $1;
			$ev_type = 500;
			$ev_status = &doEvent_Admin(
				"Mani Admin Plugin",
				substr($ev_obj_a, 0, 255)
			);
		} elsif ($s_output =~ /^\[BeetlesMod\]\s*(.+)$/) {
			# Prototype: Cmd:[BeetlesMod] obj_a
			# Matches:
		    # Beetles Mod messages
			
			$ev_obj_a  = $1;
			$ev_type = 500;
			$ev_status = &doEvent_Admin(
				"Beetles Mod",
				substr($ev_obj_a, 0, 255)
			);
		} elsif ($s_output =~ /^\[ADMIN:(.+)\] ADMIN Command: \1 used command (.+)$/) {
			# Prototype: [ADMIN] obj_a
			# Matches:
		    # Admin Mod messages
			
			$ev_obj_a  = $1;
			$ev_obj_b  = $2;
			$ev_type = 500;
			$ev_status = &doEvent_Admin(
				"Admin Mod",
				substr($ev_obj_b, 0, 255),
				$ev_obj_a
			);
		} elsif ($g_servers{$s_addr}->{play_game} == DYSTOPIA()) {
			if ($s_output =~ /^weapon { steam_id: 'STEAM_\d+:(.+?)', weapon_id: (\d+), class: \d+, team: \d+, shots: \((\d+),(\d+)\), hits: \((\d+),(\d+)\), damage: \((\d+),(\d+)\), headshots: \((\d+),(\d+)\), kills: \(\d+,\d+\) }$/ && $g_mode eq "Normal") {
			
				# Prototype: weapon { steam_id: 'STEAMID', weapon_id: X, class: X, team: X, shots: (X,X), hits: (X,X), damage: (X,X), headshots: (X,X), kills: (X,X) }
				# Matches:
				# 501. Statsme weaponstats (Dystopia)
		
				my $steamid = $1;
				my $weapon = $2;
				my $shots = $3 + $4;
				my $hits = $5 + $6;
				my $damage = $7 + $8;
				my $headshots = $9 + $10;
				my $kills = $11 + $12;
				
				$ev_type = 501;
				
				my $weapcode = $dysweaponcodes{$weapon};
				
				foreach $player (values(%g_players)) {
					if ($player->{uniqueid} eq $steamid) {
						$ev_status = &doEvent_Statsme(
							$player->{"userid"},
							$steamid,
							$weapcode,
							$shots,
							$hits,
							$headshots,
							$damage,
							$kills,
							0
						);
						last;
					}
				}
			} elsif ($s_output =~ /^(?:join|change)_class { steam_id: 'STEAM_\d+:(.+?)', .* (?:new_|)class: (\d+), .* }$/ && $g_mode eq "Normal") {
				# Prototype: join_class { steam_id: 'STEAMID', team: X, class: Y, time: ZZZZZZZZZ }
				# Matches:
				#  6. Role Selection (Dystopia)
				
				my $steamid = $1;
				my $role = $2;
				$ev_type = 6;
				
				foreach $player (values(%g_players)) {
					if ($player->{uniqueid} eq $steamid) {
						$ev_status = &doEvent_RoleSelection(
							$player->{"userid"},
							$steamid,
							$role
						);
						last;
					}
				}
			} elsif ($s_output =~ /^objective { steam_id: 'STEAM_\d+:(.+?)', class: \d+, team: \d+, objective: '(.+?)', time: \d+ }$/ && $g_mode eq "Normal") {
				# Prototype: objective { steam_id: 'STEAMID', class: X, team: X, objective: 'TEXT', time: X }
				# Matches:
				# 11. Player Action (Dystopia Objectives)
				
				my $steamid = $1;
				my $action = $2;
				foreach $player (values(%g_players)) {
					if ($player->{uniqueid} eq $steamid) {
						$ev_status = &doEvent_PlayerAction(
							$player->{"userid"},
							$steamid,
							$action
						);
						last;
					}
				}
			}
		}

		if ($ev_type) {
			if ($g_debug > 2) {
				print <<EOT
					type   = "$ev_type"
					team   = "$ev_team"
					player = "$ev_player"
					verb   = "$ev_verb"
					obj_a  = "$ev_obj_a"
					obj_b  = "$ev_obj_b"
					obj_c  = "$ev_obj_c"
					properties = "$ev_properties"
EOT
;
				while (my($key, $value) = each(%ev_properties)) {
					print "property: \"$key\" = \"$value\"\n";
				}
				
				while (my($key, $value) = each(%ev_player)) {
					print "player $key = \"$value\"\n";
				}
			}
			
			if ($ev_status ne "") {
				&printEvent($ev_type, $ev_status);
			} else {
				&printEvent($ev_type, "BAD DATA: $s_output");
			}
		} elsif (($s_output =~ /^Banid: "(.+?(?:<.+?>)*)" was (?:kicked and )?banned "for ([0-9]+).00 minutes" by "Console"$/) ||
				($s_output =~ /^Banid: "(.+?(?:<.+?>)*)" was (?:kicked and )?banned "(permanently)" by "Console"$/)) {
			
			# Prototype: "player" verb[properties]
    		# Banid: huaaa<1804><STEAM_0:1:10769><>" was kicked and banned "permanently" by "Console"
			
			$ev_player  = $1;
			$ev_bantime = $2;
			my $playerinfo = &getPlayerInfo($ev_player, 1);

			if ($ev_bantime eq "5") {
				&printEvent("BAN", "Auto Ban - ignored");
			} elsif ($playerinfo) {
				if (($g_global_banning > 0) && ($g_servers{$s_addr}->{ignore_nextban}->{$playerinfo->{"uniqueid"}} == 1)) {
					delete($g_servers{$s_addr}->{ignore_nextban}->{$playerinfo->{"uniqueid"}});
					&printEvent("BAN", "Global Ban - ignored");
				} elsif (!$g_servers{$s_addr}->{ignore_nextban}->{$playerinfo->{"uniqueid"}}) {
					my $p_steamid  = $playerinfo->{"uniqueid"};
					my $player_obj = lookupPlayer($s_addr, $playerId, $p_steamid);
					&printEvent("BAN", "Steamid: ".$p_steamid);

					if ($player_obj) {
						$player_obj->{"is_banned"} = 1;
					}  
					if (($p_steamid ne "") && ($playerinfo->{"is_bot"} == 0) && ($playerinfo->{"userid"} > 0)) {
						if ($g_global_banning > 0) {
							if ($ev_bantime eq "permanently") {
								&printEvent("BAN", "Hide player!");
								&execNonQuery("UPDATE hlstats_Players SET hideranking=2 WHERE playerId IN (SELECT playerId FROM hlstats_PlayerUniqueIds WHERE uniqueId='".&quoteSQL($p_steamid)."')");
								$ev_bantime = 0;
							}
							my $pl_steamid  = $playerinfo->{"plain_uniqueid"};
							while (my($addr, $server) = each(%g_servers)) {
								if ($addr ne $s_addr) {
									&printEvent("BAN", "Global banning on ".$addr);
									$server->{ignore_nextban}->{$p_steamid} = 1;
									$server->dorcon("banid ".$ev_bantime." $pl_steamid");
									$server->dorcon("writeid");
								}  
							}
						} 
					}  
				}  
			} else {
				&printEvent("BAN", "No playerinfo");
			}
			&printEvent("BAN", $s_output);
		} else {
			# Unrecognized event
			# HELLRAISER
			if ($g_debug > 1) {
				&printEvent(999, "UNRECOGNIZED: " . $s_output);
			}
		}
		
		if (!$g_stdin && defined($g_servers{$s_addr}) && $ev_daemontime > $g_servers{$s_addr}->{next_plyr_flush}) {
			&printEvent("MYSQL", "Flushing player updates to database...",1);
			if ($g_servers{$s_addr}->{"srv_players"}) {
				while ( my($pl, $player) = each(%{$g_servers{$s_addr}->{"srv_players"}}) ) {
					if ($player->{needsupdate}) {
						$player->flushDB();
					}
				}
			}
			&printEvent("MYSQL", "Flushing player updates to database is complete.",1);
			
			$g_servers{$s_addr}->{next_plyr_flush} = $ev_daemontime + 15+int(rand(15));
		}

		if (($g_stdin == 0) && defined($g_servers{$s_addr})) {
			$s_lines = $g_servers{$s_addr}->{lines};
			# get ping from players
			if ($s_lines % 1000 == 0) {
				$g_servers{$s_addr}->update_players_pings();
			}

			if ($g_servers{$s_addr}->{show_stats} == 1) {
				# show stats
				if ($s_lines % 2500 == 40) {
					$g_servers{$s_addr}->dostats();
				}
			}
		
			if ($s_lines > 500000)	{
				$g_servers{$s_addr}->set("lines", 0);	
			}  else	{
				$g_servers{$s_addr}->increment("lines");	
			} 
		}
	} else {
		$s_addr = "";
	}

	while( my($server) = each(%g_servers))
	{	
		if($g_servers{$server}->{next_timeout}<$ev_daemontime)
		{
			#print "checking $ev_unixtime\n";
			# Clean up
			# look
			if($g_servers{$server}->{"srv_players"})
			{
				my %players_temp=%{$g_servers{$server}->{"srv_players"}};
				while ( my($pl, $player) = each(%players_temp) ) {
					my $timeout = 250; # 250;
					if ($g_mode eq "LAN")  {
						$timeout = $timeout * 2;
					}
					#if (($player->{is_bot} == 0) && (($ev_unixtime - $player->get("timestamp")) > ($timeout - 20))) {
					#	&printEvent(400, $player->getInfoString()." is ".($ev_unixtime - $player->get("timestamp"))."s idle");
					#}  
					my $userid = $player->{userid};
					my $uniqueid = $player->{uniqueid};
					if ( ($ev_daemontime - $player->{timestamp}) > $timeout ) {
						#printf("%s - %s %s\n",$server, $player->{userid}, $player->{uniqueid});
						# we delete any player who is inactive for over $timeout sec
						# - they probably disconnected silently somehow.
						if (($player->{is_bot} == 0) || ($g_stdin)) {
							&printEvent(400, "Auto-disconnecting " . $player->getInfoString() ." for idling (" . ($ev_daemontime - $player->{timestamp}) . " sec) on server (".$server.")");
							removePlayer($server, $userid, $uniqueid);
						}
					}
				}
			}
			$g_servers{$server}->{next_timeout}=$ev_daemontime+30+rand(30);
		}
		
		if ($ev_daemontime > $g_servers{$server}->{next_flush}
			&& $g_servers{$server}->{needsupdate}
			)
		{
			$g_servers{$server}->flushDB();
			$g_servers{$server}->{next_flush} = $ev_daemontime + 20;
		}
	}

	while ( my($pl, $player) = each(%g_preconnect) ) {
		my $timeout = 600;
		if ( ($ev_unixtime - $player->{"timestamp"}) > $timeout ) {
			&printEvent(401, "Clearing pre-connect entry with key ".$pl);
			delete($g_preconnect{$pl});
		}
	}
	
	if ($g_stdin == 0) {
		# Track the Trend
		if ($g_track_stats_trend > 0) {
			track_hlstats_trend();
		}  
		while (my($addr, $server) = each(%g_servers)) {
			if (defined($server)) {
				$server->track_server_load();
			}
		}
		
		while( my($table) = each(%g_eventtable_data))
		{
			if ($g_eventtable_data{$table}{lastflush} + 30 < $ev_daemontime)
			{
				flushEventTable($table);
			}
		}
		
		if (defined($g_servers{$s_addr})) {
			if (($g_servers{$s_addr}->{map} eq "") && (($timeout > 0) && ($timeout % 60 == 0))) {
				$g_servers{$s_addr}->get_map();
			}
		}
	}  
	
	$c++;
	$c = 1 if ($c > 500000);
	$import_logs_count++ if ($g_stdin);
}

$end_time = time();
if ($g_stdin) {
	if ($import_logs_count > 0) {
		print "\n";
	}
	
	&flushAll(1);
	&execNonQuery("UPDATE hlstats_Players SET last_event=UNIX_TIMESTAMP();");
	&printEvent("IMPORT", "Import of log file complete. Scanned ".$import_logs_count." lines in ".($end_time-$start_time)." seconds", 1, 1);
}

sub INT_handler
{
	print "SIGINT received. Flushing data and shutting down...\n";
	flushAll(1);
	exit(0);
}

sub HUP_handler
{
	print "SIGHUP received. Flushing data and reloading configuration...\n";
	&reloadConfiguration;
}
