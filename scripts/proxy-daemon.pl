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
use DBI;
use IO::Socket;
use IO::Select;
use Getopt::Long;
use Time::Local;

no strict 'vars';

##
## Settings
##

# $opt_configfile - Absolute path and filename of configuration file.
$opt_configfile = "./hlstats.conf";

# $opt_libdir - Directory to look in for local required files
#               (our *.plib, *.pm files).
$opt_libdir = "./";
$heartbeat = 30;

##
##
################################################################################
## No need to edit below this line
##
require "$opt_libdir/ConfigReaderSimple.pm";
do "$opt_libdir/HLstats.plib";

$|=1;
Getopt::Long::Configure ("bundling");

binmode STDIN, ":utf8";
binmode STDOUT, ":utf8";

# Variables
my %srv_list = ();
my ($datagram,$flags);
my $oldtime = (time + $heartbeat);

$usage = <<EOT
Usage: hlstats.pl [OPTION]...
Collect statistics from one or more Half-Life2 servers for distribution
to sub-daemons (hlstats.pl).

  -h, --help                      display this help and exit  
  -d, --debug                     enable debugging output (-dd for more)
  -c, --configfile                Specific configfile to use, settings in this file can't
                                    be overided with commandline settings.

HLstatsX: Community Edition http://www.hlxcommunity.com
EOT
;

# Read Config File

if ($opt_configfile && -r $opt_configfile) {
	$conf = ConfigReaderSimple->new($opt_configfile);
	$conf->parse();
	
	%directives = (
		"DBHost",				"db_host",
		"DBUsername",				"db_user",
		"DBPassword",				"db_pass",
		"DBName",				"db_name",
		"BindIP",				"s_ip",
		"Port",					"proxy_port",
		"DebugLevel",				"g_debug",
	);

	&doConf($conf, %directives);
} else {
	&printEvent("CONFIG", "-- Warning: unable to open configuration file '$opt_configfile'", 1);
}

# Read Command Line Arguments
GetOptions(
	"help|h"			=> \$opt_help,
	"configfile|c=s"		=> \$configfile,
	"debug|d+"			=> \$g_debug
) or die($usage);

if ($opt_help) {
	print $usage;
	exit(0);
}

if ($configfile && -r $configfile) {
	$conf = '';
	$conf = ConfigReaderSimple->new($configfile);
	$conf->parse();
	&doConf($conf, %directives);
}

#
# assignDaemon(string ipaddr, string ipport, hash daemon, hash srv_list)
#
# Round-Robin kind of way of spreading the load to different daemons.
#
sub assignDaemon
{
	my ($ipaddr, $ipport, $daemon, $srv_list) = @_;
	my $next = "";

	if (defined($$srv_list{'rr-next'})) {
		$next = $$srv_list{'rr-next'};
	} else {
		$next = 0;
	}

        my $max = keys %$daemon;

        if (!defined($$srv_list{$ipaddr}{$ipport})) {
                if ($next eq $max) {
                        $next = 1;
                } else {
                        $next++;
                }
		$$srv_list{'rr-next'} = $next;

                $$srv_list{$ipaddr}{$ipport}{'dest_ip'} = $$daemon{$next}{'ip'};
                $$srv_list{$ipaddr}{$ipport}{'dest_port'} = $$daemon{$next}{'port'};
        }
	return;
}

#
# checkHeartbeat (hash daemon, string proxy_key)
#
# Prints and update the state of the perl daemons, if they are up or not.
#
sub checkHeartbeat
{
	my ($daemon, $proxy_key) = @_;

	my $state = '';
	foreach my $key (keys(%$daemon)) {
        	my $value = $$daemon{$key};
		my $socket = IO::Socket::INET->new(	Proto=>"udp",
							PeerHost=>$$daemon{$key}{'ip'},
							PeerPort=>$$daemon{$key}{'port'}
						);
		$packet = "C;HEARTBEAT;";
		$socket->send("PROXY Key=$proxy_key PROXY $packet");

		if(IO::Select->new($socket)->can_read(4)) {  # 4 second timeout
		        $socket->recv($msg,1024);
			if ($msg =~ /Heartbeat OK/) {
				$state = "up";
			} else {
				$state = "down";
			}
		}
		if ($$daemon{$key}{'curstate'} eq "") {
			$$daemon{$key}{'curstate'} = "n/a";
		}	

		$$daemon{$key}{'oldstate'} = $$daemon{$key}{'curstate'};
		$$daemon{$key}{'curstate'} = $state;

		&printEvent("HEARTBEAT", "Sending HB to $$daemon{$key}{'ip'}:$$daemon{$key}{'port'}... state: $$daemon{$key}{'curstate'} (old: $$daemon{$key}{'oldstate'})", 1);		
		$state = '';
    	}
	return;
}

#
# string retunrServerList(hash srv_list)
#
# Return a list of servers to requestor (udp package C;SERVERLIST).
#
sub returnServerList
{
	my ($srv_list) = @_;
	#$srv_list{$ipaddr}{$ipport}{'dest_ip'}

	for my $ip (keys(%srv_list)) {
		for my $port (keys(%{$srv_list{$ip}})) {
			$msg = $msg . "$ip:$port -> $srv_list{$ip}{$port}{'dest_ip'}:$srv_list{$ip}{$port}{'dest_port'}\n";
		}
	} 

	return $msg;	
}

#
# string theTime(int sec, int min, int hour, int mday, int year, int wday, int yday, int isdst)
#
# Makes a pretty timestampformat to output
#
sub theTime
{
	my ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
	$year = $year + 1900;
	$mon = $mon + 1;
	
	if ($mon <= 9) { $mon = "0$mon"; }
	if ($mday <= 9) { $mday = "0$mday"; }
	if ($hour <= 9) { $hour = "0$hour"; }
	if ($min <= 9) { $min = "0$min"; }
	if ($sec <= 9) { $sec = "0$sec"; }

	my $time = "[$year-$mon-$mday $hour:$min:$sec] ";

	return $time;
}

#
# string reloadDaemon(hash daemon, string proxy_key)
#
# Sends reload package to all daemons specified in hlstats.Options.Proxy_Daemons
#
sub reloadDaemon
{
	my ($daemon, $proxy_key) = @_;
	my $fake_ip = "127.0.0.1";
	my $fake_port = "30000";
	my $msg = '';
	$packet = "C;RELOAD;";

	foreach my $key (keys(%$daemon)) {
		if ($$daemon{$key}{'curstate'} eq "up") {
			&printEvent("CONTROL", "Sending RELOAD packet to $$daemon{$key}{'ip'}:$$daemon{$key}{'port'}", 1);
			$msg = $msg . &theTime() . "Sending RELOAD packet to $daemon{$key}{'ip'}:$daemon{$key}{'port'}\n";

        		# Sedning actual message to the daemon.
        		my $cmd = IO::Socket::INET->new(	Proto=>"udp",
								PeerHost=>$$daemon{$key}{'ip'},
								PeerPort=>$$daemon{$key}{'port'}
							);
        		$cmd->send("PROXY Key=$proxy_key PROXY $packet");
		}
	}

	return $msg;
}

#
# string getProxyKey ()
#
# Get the value for Proxy_Key
#
sub getProxyKey
{
	my $query = "SELECT `value` FROM hlstats_Options WHERE `keyname` = 'Proxy_Key'";
	my $result = &doQuery($query);
	my ($proxy_key) = $result->fetchrow_array;
	$result->finish;

	return $proxy_key;
}

sub is_number ($) { ( $_[0] ^ $_[0] ) eq '0' }


############## Main program ##############
$g_stdin = 0;

# Connect yo mysql DB to get required settings
&doConnect();

my $proxy_key = &getProxyKey();

# Get the daemons you will use
$query = "SELECT `value` FROM hlstats_Options WHERE `keyname` = 'Proxy_Daemons'";
$result = &doQuery($query);

my ($daemonlist) = $result->fetchrow_array;
$result->finish;
my @proxy_daemons = split(/,/, $daemonlist);
my $total_daemons = scalar(@proxy_daemons);

my %daemon = ();
my $i = 1;

while ($i <= $total_daemons) {
	($daemon{$i}{'ip'}, $daemon{$i}{'port'}) = split(/:/, $proxy_daemons[$i-1]);
	$daemon{$i}{'oldstate'} = "";
	$daemon{$i}{'curstate'} = "";
	$i++;
}

# Setting up the proxy port to listen on.
my $server = IO::Socket::INET->new(	LocalPort=>$proxy_port,
					Proto=>"udp"
				) or die "Can't create UDP server: $@";

# It went ok, lets start recive messages...
&printEvent("DAEMON", "HlstatsX Proxy Daemon up and running on port: $proxy_port, key: $proxy_key", 1);

# Do initial heartbeat check.
&checkHeartbeat(\%daemon, $proxy_key);

# Reload all child daemons config
&reloadDaemon(\%daemon, $proxy_key);

while ($server->recv($datagram,1024,$flags)) {
	my $control = 0;
	# Checks the subdaemons every 30 sec if they are alive.
	# the interval can be changed by modify $heartbeat value in beginning of script.
	if (time > $oldtime) {
		&checkHeartbeat(\%daemon, $proxy_key);
		$oldtime = (time + $heartbeat);
	}

        my $ipaddr = $server->peerhost;
        my $ipport = $server->peerport;

	if ($ipaddr eq "127.0.0.1" && $datagram =~/C;HEARTBEAT;/) {
		$control = 1;
		$msg = '';
		$msg = "Heartbeat OK";
		&printEvent("CONTROL", "Sending Heartbeat to $ipaddr:$ipport", 1);
	} elsif ($ipaddr eq "127.0.0.1" && $datagram =~/C;SERVERLIST;/) {
		$control = 1;
		$msg = '';
		$msg = returnServerList($srv_list);
		$msg = "ServerList\n$msg";
		&printEvent("CONTROL", "Sending Serverlist to $ipaddr:$ipport", 1);
	} elsif ($ipaddr eq "127.0.0.1" && $datagram =~/C;RELOAD;/) {
		$control = 1;
		$msg = '';
		$msg = &reloadDaemon($daemon);
	}

	if ($ipaddr eq "127.0.0.1" && $control == 1) {
		# Sending actual message to the daemon.
		my $dest = sockaddr_in($ipport, inet_aton($ipaddr));
		my $bytes = send($server, $msg, 0, $dest);

		next;
	}
		


	if ($datagram =~ /PROXY Key=(.+) (.*)PROXY (.+)/) {
		if ($proxy_key eq $1) {
			if ($3 =~ /C;HEARTBEAT;/) {
				$msg = '';
				$msg = "Heartbeat OK";
				&printEvent("CONTROL", "Sending Heartbeat to $ipaddr:$ipport", 1);
			} elsif ($3 =~ /C;SERVERLIST;/) {
				$msg = '';
				$msg = returnServerList($srv_list);
				$msg = "ServerList\n$msg";
				&printEvent("CONTROL", "Sending Serverlist to $ipaddr:$ipport", 1);
				&printEvent("CONTROL", $msg, 1);
			} elsif ($3 =~ /C;RELOAD;/) {
				$msg = '';
				$msg = &reloadDaemon($daemon);
			} 
		} else {
			$msg = "FAILED PROXY REQUEST ($ipaddr:$ipport)\n";
			&printEvent("E403", "Sending FAILED PROXY REQUEST to $ipaddr:$ipport", 1);
		}


                # Sedning actual message to the daemon.
		my $dest = sockaddr_in($ipport, inet_aton($ipaddr));
		my $bytes = send($server, $msg, 0, $dest);

		next;
	}

	if (defined($srv_list{$ipaddr}{$ipport})) {
		# Check the oldstate, curstate of your logging daemon
		foreach my $key (keys %daemon) {
				if ($srv_list{$ipaddr}{$ipport}{'dest_ip'} eq $daemon{$key}{'ip'} && $srv_list{$ipaddr}{$ipport}{'dest_port'} eq $daemon{$key}{'port'}) {;
					if ($daemon{$key}{'curstate'} eq "up" && $daemon{$key}{'oldstate'} eq "down") {
						# Recovering, should do a reload of some kind here.
						%srv_list = ();

					} elsif ($daemon{$key}{'curstate'} eq "down" && $daemon{$key}{'oldstate'} eq "up") {
						# Daemon died, assing a new daemon to server

						delete $srv_list{$ipaddr}{$ipport};
						($daemon, $srv_list) = &assignDaemon($ipaddr, $ipport, $daemon, $srv_list);
						&printEvent("BALANCE", "down - up: Re-Assing daemon $srv_list{$ipaddr}{$ipport}{'dest_ip'}:$srv_list{$ipaddr}{$ipport}{'dest_port'} to $ipaddr:$ipport", 1);
					} elsif ($daemon{$key}{'curstate'} eq "down" && $daemon{$key}{'oldstate'} eq "down") {
						# DOWN, should already reassinged the daemon.

						delete $srv_list{$ipaddr}{$ipport};
						($daemon, $srv_list) = &assignDaemon($ipaddr, $ipport, $daemon, $srv_list);
						&printEvent("BALANCE", "down-down: Re-Assing daemon $srv_list{$ipaddr}{$ipport}{'dest_ip'}:$srv_list{$ipaddr}{$ipport}{'dest_port'} to $ipaddr:$ipport", 1);
					} elsif ($daemon{$key}{'curstate'} eq "down" && $daemon{$key}{'oldstate'} eq "n/a") {
						# Daemon down when we started proxy, assing another daemon.

						delete $srv_list{$ipaddr}{$ipport};
						($daemon, $srv_list) = &assignDaemon($ipaddr, $ipport, $daemon, $srv_list);
						&printEvent("BALANCE", "down - na: Assing daemon $srv_list{$ipaddr}{$ipport}{'dest_ip'}:$srv_list{$ipaddr}{$ipport}{'dest_port'} to $ipaddr:$ipport from down/na", 1);
					}
				}
		}
	} else { 
		# Assign a logging daemon for your server:port
		delete $srv_list{$ipaddr}{$ipport};
		&assignDaemon($ipaddr, $ipport, \%daemon, \%srv_list);
		&printEvent("BALANCE", "Assing daemon $srv_list{$ipaddr}{$ipport}{'dest_ip'}:$srv_list{$ipaddr}{$ipport}{'dest_port'} to $ipaddr:$ipport", 1);
	}



	if ($datagram =~ /.*rcon from.*: command "status".*/ || $datagram =~ /.*rcon from.*: command "stats".*/ || $datagram =~ /.*rcon from.*: command "".*/) {
		# skip messages that looks like this, to ease the load on the sub daemons alittle	
		&printEvent("NOTICE", "Skipping message...", 1) if ($g_debug > 1);
	} else {
		if (defined($srv_list{$ipaddr}{$ipport}{'dest_ip'}) && defined($srv_list{$ipaddr}{$ipport}{'dest_port'})) {
			$datagram =~ s/^.*RL /RL /g;

			&printEvent("NOTICE", "Sending $datagram to daemon $srv_list{$ipaddr}{$ipport}{'dest_ip'}:$srv_list{$ipaddr}{$ipport}{'dest_port'}", 1) if ($g_debug > 1);	
			# Sedning actual message to the daemon.
			my $forward = IO::Socket::INET->new(	Proto=>"udp",
								PeerHost=>$srv_list{$ipaddr}{$ipport}{'dest_ip'},
								PeerPort=>$srv_list{$ipaddr}{$ipport}{'dest_port'}
							);
			$forward->send("PROXY Key=$proxy_key $ipaddr:".$ipport."PROXY $datagram");
		}
	}
}

