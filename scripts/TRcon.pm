package TRcon;
#
# TRcon Perl Module - execute commands on a remote Half-Life2 server using remote console.
#
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

use Sys::Hostname;
use IO::Socket;
use IO::Select;
use bytes;
use Scalar::Util;

do "$::opt_libdir/HLstats_GameConstants.plib";

my $VERSION = "1.00";
my $TIMEOUT = 1.0;

my $SERVERDATA_EXECCOMMAND        = 2;
my $SERVERDATA_AUTH               = 3;
my $SERVERDATA_RESPONSE_VALUE     = 0;
my $SERVERDATA_AUTH_RESPONSE      = 2;
my $REFRESH_SOCKET_COUNTER_LIMIT  = 100;
my $AUTH_PACKET_ID                = 1;
my $SPLIT_END_PACKET_ID           = 2;

#
# Constructor
#

sub new
{
  my ($class_name, $server_object) = @_;
  my ($self) = {};
  bless($self, $class_name);
  
  $self->{"rcon_socket"}            = 0;
  $self->{"server_object"}          = $server_object;
  Scalar::Util::weaken($self->{"server_object"});
  $self->{"auth"}                   = 0;
  $self->{"refresh_socket_counter"} = 0;
  $self->{"packet_id"}              = 10;
  return $self;
}

sub execute
{
  my ($self, $command, $splitted_answer) = @_;
  if ($::g_stdin == 0) {
    my $answer = $self->sendrecv($command, $splitted_answer);
    if ($answer =~ /bad rcon_password/i) {
      &::printEvent("TRCON", "Bad Password");
    }
    return $answer;
  }  
}

sub get_auth_code
{
  my ($self, $id) = @_;
  my $auth = 0;
  
  if ($id == $AUTH_PACKET_ID) {
    &::printEvent("TRCON", "Rcon password accepted");
    $auth = 1;
    $self->{"auth"} = 1;
  } elsif( $id == -1) {
    &::printEvent("TRCON", "Rcon password refused");
    $self->{"auth"} = 0;
    $auth           = 0;
  } else {
    &::printEvent("TRCON", "Bad password response id=$id");
    $self->{"auth"} = 0;
    $auth           = 0;
  }
  return $auth;

}


sub sendrecv
{
  my ($self, $msg, $splitted_answer) = @_;
  
  my $rs_counter = $self->{"refresh_socket_counter"};
  if ($rs_counter % $REFRESH_SOCKET_COUNTER_LIMIT == 0)  {
    if ($self->{"rcon_socket"} > 0) {
      shutdown($self->{"rcon_socket"}, 2);
      $self->{"rcon_socket"} = 0;
    }
    my $server_object = $self->{"server_object"};
    $self->{"rcon_socket"}   =  IO::Socket::INET->new(
                                      		Proto=>"tcp",
                                            PeerAddr=>$server_object->{address}, 
                                            PeerPort=>$server_object->{port}, 
                            	);
    if (!$self->{"rcon_socket"}) {
      &::printEvent("TRCON", "Cannot setup TCP socket on ".$server_object->{address}.":".$server_object->{port}.": $!");
    } 
    $self->{"refresh_socket_counter"} = 0;
    $self->{"auth"} = 0;
  }                          	


  my $r_socket  = $self->{"rcon_socket"};
  my $server    = $self->{"server_object"};

  my $auth      = $self->{"auth"};
  my $response  = "";
  my $packet_id = $self->{"packet_id"};
  
  if (($r_socket) && ($r_socket->connected() )) {
    if ($auth == 0)  {
      &::printEvent("TRCON", "Trying to get rcon access (auth)");
      if ($self->send_rcon($AUTH_PACKET_ID, $SERVERDATA_AUTH, $server->{rcon}, "")) {
        &::printEvent("TRCON", "Couldn't send password");
        return;
      }
      my ($id, $command, $response) = $self->recieve_rcon($AUTH_PACKET_ID);
      if($command == $SERVERDATA_AUTH_RESPONSE) {
        $auth = $self->get_auth_code($id);
      } elsif (($command == $SERVERDATA_RESPONSE_VALUE) && ($id == $AUTH_PACKET_ID)) {  
         #Source servers sends one junk packet during the authentication step, before it responds 
         # with the correct authentication response.  
         &::printEvent("TRCON", "Junk packet from Source Engine");
         my ($id, $command, $response) = $self->recieve_rcon($AUTH_PACKET_ID);
         $auth = $self->get_auth_code($id);
      }  
    }
    
    if ($auth == 1)  {
      $self->{"refresh_socket_counter"}++;
      $self->send_rcon($packet_id, $SERVERDATA_EXECCOMMAND, $msg);
      if ($splitted_answer > 0) {
        $self->send_rcon($SPLIT_END_PACKET_ID, $SERVERDATA_EXECCOMMAND, "");
      }  
      my ($id, $command, $response) = $self->recieve_rcon($packet_id, $splitted_answer);
      $self->{"packet_id"}++;
      if ($self->{"packet_id"} > 32767) {
        $self->{"packet_id"} = 10;
      }
      return $response;
    }
  } else {
    $self->{"refresh_socket_counter"} = 0;
  } 
  return;
  
}

#
# Send a package
#
sub send_rcon
{
  my ($self, $id, $command, $string1, $string2) = @_;
  my $data = pack("VVZ*Z*", $id, $command, $string1, $string2);
  my $size = length($data);
  if($size > 4096) {
    &::printEvent("TRCON", "Command to long to send!");
    return 1;
  }
  $data = pack("V", $size).$data;

  my $r_socket = $self->{"rcon_socket"};
  if ($r_socket && $r_socket->connected() && $r_socket->peeraddr()) {
    $r_socket->send($data, 0);
    return 0;
  } else {
    $self->{"refresh_socket_counter"} = 0;
  }
  return 1;
}

#
#  Recieve a package
#
sub recieve_rcon
{
  my ($self, $packet_id, $splitted_answer) = @_;
  my ($size, $id, $command, $msg);
  my $tmp = "";

  my $r_socket  = $self->{"rcon_socket"};
  my $server    = $self->{"server_object"};
  my $auth      = $self->{"auth"};
  my $packet_id = $self->{"packet_id"};
  
  if (($r_socket) && ($r_socket->connected() )) {
    if(IO::Select->new($r_socket)->can_read($TIMEOUT)) {  # $TIMEOUT seconds timeout
      $r_socket->recv($tmp, 1500);
      $size    = unpack("V",  substr($tmp, 0, 4));
	  if ($size == 0) {
		$self->{"refresh_socket_counter"} = 0;
		return (-1, -1, -1);
	  }
      $id      = unpack("V",  substr($tmp, 4, 4));
      $command = unpack("V",  substr($tmp, 8, 4));
      if ($id == $packet_id)  {
        $tmp     = substr($tmp, 12, length($tmp)-12);
        if ($splitted_answer > 0) {
          my $last_packet_id = $id;
          while ($last_packet_id != $SPLIT_END_PACKET_ID) {
            if(IO::Select->new($r_socket)->can_read($TIMEOUT)) {
              $r_socket->recv($split_data, 1500);
              my $split_size    = unpack("V",  substr($split_data, 0, 4));
              my $split_id      = unpack("V",  substr($split_data, 4, 4));
              my $split_command = unpack("V",  substr($split_data, 8, 4));
              if ($split_id == $last_packet_id) {
                $split_data = substr($split_data, 12, length($split_data)-12);
              }
			  if (!defined($split_id)){
				$last_packet_id = $SPLIT_END_PACKET_ID;
			  } else {
				$last_packet_id = $split_id;
			  }
              $tmp .= $split_data;
            } else {
              &::printNotice("TRCON", "Multiple packet error");
              $last_packet_id = $SPLIT_END_PACKET_ID;
            }
          }
        }
        if (length($tmp) > 0)  {
          $tmp .= "\x00";
          my ($string1, $string2) = unpack("Z*Z*", $tmp);
          $msg = $string1.$string2;
        } else {
          $msg = "";
        }  
      }
      return ($id, $command, $msg);
    } else {
      $self->{"refresh_socket_counter"} = 0;
      return (-1, -1, -1);
    }
  } else {
    $self->{"refresh_socket_counter"} = 0;
    return (-1, -1, -1);
  }
}

#
# Get error message
#

sub error
{
  my ($self) = @_;
  return $self->{"rcon_error"};
}



#
# Parse "status" command output into player information
#

sub getPlayers
{
  my ($self) = @_;
  my $status = $self->execute("status", 1);
  if (!$status)
  {
  	return ("", -1, "", 0);
  }
  
  my @lines = split(/[\r\n]+/, $status);

  my %players;

# HL2 standard
# userid name uniqueid connected ping loss state adr
# 187 ".:[SoV]:.Evil Shadow" STEAM_0:1:6200412 13:48 97 0 active 213.10.196.229:24085

# L4D
# userid name uniqueid connected ping loss state rate adr
#  2 1 "psychonic" STEAM_1:1:4153990 00:45 68 1 active 20000 192.168.5.115:27006

  foreach my $line (@lines)
  {
    if ($line =~ /^\#\s*
                (\d+)\s+		# userid
				(?:\d+\s+|)     # extra number in L4D, not sure what this is??
                "(.+)"\s+		# name
                (.+)\s+		    # uniqueid
                ([\d:]+)\s+		# time
                (\d+)\s+		# ping
                (\d+)\s+		# loss
                ([A-Za-z]+)\s+	# state
				(?:\d+\s+|)		# rate (L4D only)
                ([^:]+):    	# addr
                (\S+)           # port
                $/x)
    {
      my $userid   = $1;
      my $name     = $2;
      my $uniqueid = $3;
      my $time     = $4;
      my $ping     = $5;
      my $loss     = $6;
      my $state    = $7;
      my $address  = $8;
      my $port     = $9;

	  $uniqueid =~ s/^STEAM_[0-9]+?\://i;
	  
      # &::printEvent("DEBUG", "USERID: '$userid', NAME: '$name', UNIQUEID: '$uniqueid', TIME: '$time', PING: '$ping', LOSS: '$loss', STATE: '$state', ADDRESS:'$address', CLI_PORT: '$port'", 1);

      if ($::g_mode eq "NameTrack") {
        $players{$name}    = { 
                             "Name"       => $name,
                             "UserID"     => $userid,
                             "UniqueID"   => $uniqueid,
                             "Time"       => $time,
                             "Ping"       => $ping,
                             "Loss"       => $loss,
                             "State"      => $state,
                             "Address"    => $address,
                             "ClientPort" => $port
                           };
      } elsif ($::g_mode eq "LAN") {
        $players{$address} = { 
                             "Name"       => $name,
                             "UserID"     => $userid,
                             "UniqueID"   => $uniqueid,
                             "Time"       => $time,
                             "Ping"       => $ping,
                             "Loss"       => $loss,
                             "State"      => $state,
                             "Address"    => $address,
                             "ClientPort" => $port
                           };
      } else {
        $players{$uniqueid} = { 
                             "Name"       => $name,
                             "UserID"     => $userid,
                             "UniqueID"   => $uniqueid,
                             "Time"       => $time,
                             "Ping"       => $ping,
                             "Loss"       => $loss,
                             "State"      => $state,
                             "Address"    => $address,
                             "ClientPort" => $port
                            };
      }
      
    }
  }
  return %players;
}

sub getServerData
{
  my ($self) = @_;
  my $status = $self->execute("status", 1);
  
  my $server_object = $self->{server_object};
  my $game = $server_object->{play_game};  

  my @lines = split(/[\r\n]+/, $status);

  my $servhostname         = "";
  my $map         = "";
  my $max_players = 0;
  my $difficulty = 0;

  foreach my $line (@lines)
  {
    if ($line =~ /^\s*hostname\s*:\s*([\S].*)$/)
    {
      $servhostname   = $1;
    }
    elsif ($line =~ /^\s*map\s*:\s*([\S]+).*$/)
    {
      $map   = $1;
    }
    elsif ($line =~ /^\s*players\s*:\s*\d+.+\((\d+)\smax.*$/)
    {
      $max_players = $1;
    }
  }
  if ($game == L4D()) {
	  $difficulty = $self->getDifficulty();
  }
  return ($servhostname, $map, $max_players, $difficulty);
}


sub getVisiblePlayers
{
  my ($self) = @_;
  my $status = $self->execute("sv_visiblemaxplayers");
  
  my @lines = split(/[\r\n]+/, $status);
  

  my $max_players = -1;
  foreach my $line (@lines)
  {
   # "sv_visiblemaxplayers" = "-1"
   #       - Overrides the max players reported to prospective clients
    if ($line =~ /^\s*"sv_visiblemaxplayers"\s*=\s*"([-0-9]+)".*$/x)
    {
      $max_players   = $1;
    }
  }
  return ($max_players);
}

my %l4d_difficulties = (
	'Easy'       => 1,
	'Normal'     => 2,
	'Hard'       => 3,
	'Impossible' => 4
);

sub getDifficulty
{
	#z_difficulty
	#"z_difficulty" = "Normal"
	# game replicated
	# - Difficulty of the current game (Easy, Normal, Hard, Impossible)
	
  my ($self) = @_;
  my $zdifficulty = $self->execute("z_difficulty");
	
  my @lines = split(/[\r\n]+/, $zdifficulty);
  
  foreach my $line (@lines)
  {
    if ($line =~ /^\s*"z_difficulty"\s*=\s*"([A-Za-z]+)".*$/x)
    {
		if (exists($l4d_difficulties{$1}))
		{
			return $l4d_difficulties{$1};
		}
    }
  }
  return 0;
}


#
# Get information about a player by userID
#

sub getPlayer
{
  my ($self, $uniqueid) = @_;
  my %players = $self->getPlayers();
  
  if (defined($players{$uniqueid}))
  {
    return $players{$uniqueid};
  }
  else
  {
    $self->{"error"} = "No such player # $uniqueid";
    return 0;
  }
}

1;
# end
