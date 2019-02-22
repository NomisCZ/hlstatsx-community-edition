package BASTARDrcon;
#
# BASTARDrcon Perl Module - execute commands on a remote Half-Life 1 server using Rcon.
# A merge of the KKrcon library into HLstatsX
#  Copyright (C) 2008-20XX  Nicholas Hastings (nshastings@gmail.com)

# KKrcon Perl Module - execute commands on a remote Half-Life server using Rcon.
# http://kkrcon.sourceforge.net
#
# TRcon Perl Module - execute commands on a remote Half-Life2 server using remote console.
# http://www.hlstatsx.com
#
# Copyright (C) 2000, 2001  Rod May
# Enhanced in 2005 by Tobi (Tobi@gameme.de)
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

use strict;
use sigtrap;
use Socket;
use Sys::Hostname;
use bytes;

##
## Main
##

#
# Constructor
#
sub new
{
    my ($class_name, $server_object) = @_;
    my ($self) = {};
	bless($self, $class_name);
	
	# Initialise properties
	$self->{server_object} = $server_object;
	$self->{rcon_password} = $server_object->{rcon}  or die("BASTARDrcon: a Password is required\n");
	$self->{server_host}   = $server_object->{address};
	$self->{server_port}   = int($server_object->{port}) or die("BASTARDrcon: invalid Port \"" . $server_object->{port} . "\"\n");
	
	$self->{socket} = undef;
	$self->{error} = "";
	
	# Set up socket parameters
	$self->{_ipaddr} = gethostbyname($self->{server_host}) or die("BASTARDrcon: could not resolve Host \"" . $self->{server_host} . "\"\n");
	
	return $self;
}

#
# Execute an Rcon command and return the response
#
sub execute
{
	my ($self, $command) = @_;
	my $msg;
	my $ans;

	# version x.1.0.6+ HL1 server
	$msg = "\xFF\xFF\xFF\xFFchallenge rcon\n\0";
	$ans = $self->_sendrecv($msg);
	
	if ($ans =~ /challenge +rcon +(\d+)/)
	{
		$msg = "\xFF\xFF\xFF\xFFrcon $1 \"" . $self->{"rcon_password"} . "\" $command\0";
		$ans = $self->_sendrecv($msg);
	}
	elsif (!$self->error())
	{
		$ans = "";
		$self->{"error"} = "No challenge response";
	}
	
	if ($ans =~ /bad rcon_password/i)
	{
		$self->{"error"} = "Bad Password";
	}
	return $ans;
}

sub _sendrecv
{
	my ($self, $msg) = @_;
	my $host   = $self->{"server_host"};
	my $port   = $self->{"server_port"};
	my $ipaddr = $self->{"_ipaddr"};
	my $proto  = $self->{"_proto"};
	
	# Open socket
	socket($self->{"socket"}, PF_INET, SOCK_DGRAM, $proto) or die("BASTARDrcon(141): socket: $!\n");
	my $hispaddr = sockaddr_in($port, $ipaddr);
	
	die("BASTARDrcon: send $ipaddr:$port : $!") unless(defined(send($self->{"socket"}, $msg, 0, $hispaddr)));

	my $rin = "";
	vec($rin, fileno($self->{"socket"}), 1) = 1;
	my $ans = "TIMEOUT";
	if (select($rin, undef, undef, 0.5))
	{
		$ans = "";
		$hispaddr = recv($self->{"socket"}, $ans, 8192, 0);
		$ans =~ s/\x00+$//;					# trailing crap
		$ans =~ s/^\xFF\xFF\xFF\xFFl//;		# HL response
		$ans =~ s/^\xFF\xFF\xFF\xFFn//;		# QW response
		$ans =~ s/^\xFF\xFF\xFF\xFF//;		# Q2/Q3 response
		$ans =~ s/^\xFE\xFF\xFF\xFF.....//;	# old HL bug/feature
	}
	# Close socket
	close($self->{"socket"});
	
	if ($ans eq "TIMEOUT")
	{
		$ans = "";
		$self->{"error"} = "Rcon timeout";
	}
	return $ans;
}

#
# Send a package
#
sub send_rcon
{
	my ($self, $id, $command, $string1, $string2) = @_;
	my $tmp = pack("VVZ*Z*",$id,$command,$string1,$string2);
	my $size = length($tmp);
	if($size > 4096)
	{
		$self->{error} = "Command too long to send!";
		return 1;
	}
	$tmp = pack("V", $size) .$tmp;

	unless(defined(send($self->{"socket"},$tmp,0)))
	{
		die("BASTARDrcon: send $!");
	}
	return 0;
}

#
#  Recieve a package
#
sub recieve_rcon
{
	my $self = shift;
	my ($size, $id, $command, $msg);
	my $rin = "";
	my $tmp = "";
	
	vec($rin, fileno($self->{"socket"}), 1) = 1;
	if(select($rin, undef, undef, 0.5))
	{
		while(length($size) < 4)
		{
			$tmp = "";
			recv($self->{"socket"}, $tmp, (4-length($size)), 0);
			$size .= $tmp;
		}
		$size = unpack("V", $size);
		if($size < 10 || $size > 8192)
		{
			close($self->{"socket"});
			$self->{error} = "illegal size $size ";
			return (-1, -1, -1);
		}
		
		while(length($id)<4)
		{
			$tmp = "";
			recv($self->{"socket"}, $tmp, (4-length($id)), 0);
			$id .= $tmp;
		}
		$id = unpack("V", $id);
		$size = $size - 4;
		while(length($command)<4)
		{
			$tmp ="";
			recv($self->{"socket"}, $tmp, (4-length($command)),0);
			$command.=$tmp;
		}
		$command = unpack("V", $command);
		$size = $size - 4;
		my $msg = "";
		while($size >= 1)
		{
			$tmp = "";
			recv($self->{"socket"}, $tmp, $size, 0);
			$size -= length($tmp);
			$msg .= $tmp;
		}
		my ($string1,$string2) = unpack("Z*Z*",$msg);
		$msg = $string1.$string2;
		return ($id, $command, $msg);
	}
	else
	{
		return (-1, -1, -1);
	}
}

#
# Get error message
#
sub error
{
	my ($self) = @_;
	return $self->{"error"};
}

#
# Parse "status" command output into player information
#
sub getPlayers
{
  my ($self) = @_;
  my $status = $self->execute("status");
  
  my @lines = split(/[\r\n]+/, $status);

  my %players;

# HL1
#      name userid uniqueid frag time ping loss adr
# 1 "psychonic" 1 STEAM_0:1:4153990   0 00:33   13    0 192.168.5.115:27005

  foreach my $line (@lines)
  {
    if ($line =~ /^\#\s*\d+\s+
                "(.+)"\s+					# name
				(\d+)\s+					# userid
                ([^\s]+)\s+\d+\s+			# uniqueid
				([\d:]+)\s+					# time
                (\d+)\s+					# ping
                (\d+)\s+					# loss
                ([^:]+):    				# addr
                (\S+)          				# port
                $/x)	

	{
	  my $name     = $1;
      my $userid   = $2;
      my $uniqueid = $3;
      my $time     = $4;
      my $ping     = $5;
      my $loss     = $6;
      my $state    = "";
      my $address  = $7;
      my $port     = $8;
	  
	  $uniqueid =~ s/^STEAM_[0-9]+?\://i;
	  
      # &::printEvent("DEBUG", "USERID: '$userid', NAME: '$name', UNIQUEID: '$uniqueid', TIME: '$time', PING: '$ping', LOSS: '$loss', ADDRESS:'$address', CLI_PORT: '$port'", 1);

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
  my $status = $self->execute("status");

  my @lines = split(/[\r\n]+/, $status);

  my $servhostname         = "";
  my $map         = "";
  my $max_players = 0;
  foreach my $line (@lines)
  {
    if ($line =~ /^\s*hostname\s*:\s*([\S].*)$/x)
    {
      $servhostname   = $1;
    }
    elsif ($line =~ /^\s*map\s*:\s*([\S]+).*$/x)
    {
      $map   = $1;
    }
    elsif ($line =~ /^\s*players\s*:\s*\d+.+\((\d+)\smax.*$/)
    {
      $max_players = $1;
    }
  }
  return ($servhostname, $map, $max_players, 0);
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
