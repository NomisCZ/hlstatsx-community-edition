package HLstats_Game;
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

#
# Constructor
#

sub new
{
	my $class_name = shift;
	my $game = shift;
	
	my $self = {};
	bless($self, $class_name);
	
	# Initialise Properties
	$self->{game}			= $game;
	$self->{weapons}		= ();
	$self->{actions}		= ();
	
	# Set Property Values
	
	die("HLstats_Game->new(): must specify game's game code\n")	if ($game eq "");
	#&::printEvent("DEBUG","game is $game");
	my $weaponlist = &::doQuery("SELECT code, name, modifier FROM hlstats_Weapons WHERE game='".&::quoteSQL($game)."'");
	while ( my($code,$name,$modifier) = $weaponlist->fetchrow_array) {
		$self->{weapons}{$code}{name} = $name;
		$self->{weapons}{$code}{modifier} = $modifier;
		#&::printEvent("DEBUG","Weapon: name is \"$name\"; modifier is $modifier");
	}
	
	my $actionlist = &::doQuery("SELECT id, code, reward_player, reward_team, team, description, for_PlayerActions, for_PlayerPlayerActions, for_TeamActions, for_WorldActions FROM hlstats_Actions WHERE game='".&::quoteSQL($game)."'");
	while ( my($id, $code, $reward_player,$reward_team,$team, $descr, $paction, $ppaction, $taction, $waction) = $actionlist->fetchrow_array) {
		$self->{actions}{$code}{id} = $id;
		$self->{actions}{$code}{descr} = $descr;
		$self->{actions}{$code}{reward_player} = $reward_player;
		$self->{actions}{$code}{reward_team} = $reward_team;
		$self->{actions}{$code}{team} = $team;
		$self->{actions}{$code}{paction} = $paction;
		$self->{actions}{$code}{ppaction} = $ppaction;
		$self->{actions}{$code}{taction} = $taction;
		$self->{actions}{$code}{waction} = $waction;
	}
	$actionlist->finish;

	&::printNotice("Created new game object " . $game);
	return $self;
}

sub getTotalPlayers
{
	my ($self) = @_;
	
	my $query = "
		SELECT 
			COUNT(*) 
		FROM 
			hlstats_Players
		WHERE
			game=?
			AND hideranking = 0
			AND kills >= 1
	";
	my $resultTotalPlayers = &::execCached("get_game_total_players", $query, &::quoteSQL($self->{game}));
	my ($totalplayers) = $resultTotalPlayers->fetchrow_array;
	$resultTotalPlayers->finish;
	
	return $totalplayers;
}

1;
