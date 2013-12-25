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
use IO::Socket;
use DBI;

require "$opt_libdir/ConfigReaderSimple.pm";
do "$opt_libdir/HLstats.plib";

$|=1;
Getopt::Long::Configure ("bundling");


##
## Functions
##


sub is_number ($) { ( $_[0] ^ $_[0] ) eq '0' }


#
# void printEvent (int code, string description)
#
# Logs event information to stdout.
#

sub printEvent
{
	my ($code, $description, $update_timestamp) = @_;
	
	if ($g_debug > 0)
	{
	    if ($update_timestamp > 0)
	    {
  		  my ($sec,$min,$hour,$mday,$mon,$year) = localtime(time());
		  my $timestamp = sprintf("%04d-%02d-%02d %02d:%02d:%02d", $year+1900, $mon+1, $mday, $hour, $min, $sec);
	    } else {
	      my $timestamp = $ev_timestamp;
	    }
		print localtime(time) . "" unless ($timestamp);
		if (is_number($code))
		{
  		  printf("%s: %21s - E%03d: %s\n", $timestamp, $s_addr, $code, $description);
  		} else {
  		  printf("%s: %21s - %s: %s\n", $timestamp, $s_addr, $code, $description);
  		}  
	}
}



##
## MAIN
##

# Options

$opt_help = 0;
$opt_version = 0;
$opt_regroup = 0;

$db_host = "localhost";
$db_user = "";
$db_pass = "";
$db_name = "hlstats";

$g_dns_timeout = 5;
$g_debug = 0;

# Usage message

$usage = <<EOT
Usage: hlstats-resolve.pl [OPTION]...
Resolve player IP addresses to hostnames.

  -h, --help                      display this help and exit
  -v, --version                   output version information and exit
  -d, --debug                     enable debugging output (-dd for more)
  -n, --nodebug                   disables above; reduces debug level
      --db-host=HOST              database ip:port
      --db-name=DATABASE          database name
      --db-password=PASSWORD      database password (WARNING: specifying the
                                    password on the command line is insecure.
                                    Use the configuration file instead.)
      --db-username=USERNAME      database username
      --dns-timeout=SEC           timeout DNS queries after SEC seconds  [$g_dns_timeout]
  -r, --regroup                   only re-group hostnames--don't resolve any IPs

Long options can be abbreviated, where such abbreviation is not ambiguous.

Most options can be specified in the configuration file:
  $opt_configfile
Note: Options set on the command line take precedence over options set in the
configuration file.

HLstats: http://www.hlstats.org
EOT
;

# Read Config File

if ($opt_configfile && -r $opt_configfile)
{
	$conf = ConfigReaderSimple->new($opt_configfile);
	$conf->parse();
	
	%directives = (
		"DBHost",			"db_host",
		"DBUsername",		"db_user",
		"DBPassword",		"db_pass",
		"DBName",			"db_name",
		"DNSTimeout",		"g_dns_timeout",
		"DebugLevel",		"g_debug"
	);
	
	&doConf($conf, %directives);
}
else
{
	print "-- Warning: unable to open configuration file '$opt_configfile'\n";
}

# Read Command Line Arguments

GetOptions(
	"help|h"			=> \$opt_help,
	"version|v"			=> \$opt_version,
	"debug|d+"			=> \$g_debug,
	"nodebug|n+"		=> \$g_nodebug,
	"db-host=s"			=> \$db_host,
	"db-name=s"			=> \$db_name,
	"db-password=s"		=> \$db_pass,
	"db-username=s"		=> \$db_user,
	"dns-timeout=i"		=> \$g_dns_timeout,
	"regroup|r"			=> \$opt_regroup
) or die($usage);

if ($opt_help)
{
	print $usage;
	exit(0);
}

if ($opt_version)
{
	print "hlstats-resolve.pl (HLstats) $g_version\n"
		. "Real-time player and clan rankings and statistics for Half-Life\n\n"
		. "Copyright (C) 2001  Simon Garner\n"
		. "This is free software; see the source for copying conditions.  There is NO\n"
		. "warranty; not even for MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.\n";
	exit(0);
}

$g_debug -= $g_nodebug;
$g_debug = 0 if ($g_debug < 0);

if ($g_debug >= 2)
{
	$opt_quiet = 0;
}
else
{
	$opt_quiet = 1;	# quiet name resolution
}

$g_dns_resolveip = 1;


# Startup

print "++ HLstats Resolve $g_version starting...\n\n";

# Connect to the database

print "-- Connecting to MySQL database '$db_name' on '$db_host' as user '$db_user' ... ";

$db_conn = DBI->connect(
	"DBI:mysql:$db_name:$db_host",
	$db_user, $db_pass
) or die ("Can't connect to MySQL database '$db_name' on '$db_host'\n" .
	"$DBI::errstr\n");

print "connected OK\n";

# Print configuration

print "-- DNS timeout is $g_dns_timeout seconds. Debug level is $g_debug.\n";


# Main data routine

if ($opt_regroup)
{
	my $result = &doQuery("
		SELECT
			id,
			hostname
		FROM
			hlstats_Events_Connects
		WHERE
			hostname != ''
	");
	
	my $total = $result->rows;
	
	if ($total > 0) {
		print "\n++ Re-grouping hosts (total $total hostnames) ... ";
	
		my $resultHG = &queryHostGroups();
	
		if ($g_debug > 0)
		{
			print "\n\n";
		}
		else
		{
			print "    ";
		}
	
		my $p = 1;
		while( my($id, $hostname) = $result->fetchrow_array )
		{
			my $percent = ($p / $total) * 100;
		
			my $hostgroup = &getHostGroup($hostname, $resultHG);
		
			&execNonQuery("
				UPDATE
					hlstats_Events_Connects
				SET
					hostgroup='" . &quoteSQL($hostgroup) . "'
				WHERE
					id=$id
			");
	
			if ($g_debug > 0)
			{
				printf("-> (%3d%%) %50s  =  %s\n", $percent, $hostname, $hostgroup);
			}
			else
			{
				printf("\b\b\b\b%3d%%", $percent);
			}
		
			$p++;
		}
	
		print "\n" unless ($g_debug > 0);
	} else {
		print "\n++ No Connects found!\n";
	}
}
else
{
	my $result = &doQuery("
		SELECT
			DISTINCT ipAddress,
			hostname
		FROM
			hlstats_Events_Connects
	");
	
	my $total = $result->rows;
	if ($total > 0) {
		print "\n++ Resolving IPs and re-grouping hosts (total $total connects) ... ";
	
		my $resultHG = &queryHostGroups();
	
		if ($g_debug > 0)
		{
			print "\n\n";
		}
		else
		{
			print "    ";
		}
	
		my $p = 1;
		while( my($ipAddress, $hostname) = $result->fetchrow_array )
		{
			my $percent = ($p / $total) * 100;
			
			if ($hostname eq "")
			{
				$hostname = &resolveIp($ipAddress, $opt_quiet);
			}
		
			my $hostgroup = &getHostGroup($hostname, $resultHG);
		
			&execNonQuery("
				UPDATE
					hlstats_Events_Connects
				SET
					hostname='$hostname',
					hostgroup='" . &quoteSQL($hostgroup) . "'
				WHERE
					ipAddress='$ipAddress'
			");
		
			if ($g_debug > 0)
			{
				printf("-> (%3d%%) %15s  =  %50s  =  %s\n", $percent, $ipAddress, $hostname, $hostgroup);
			}
			else
			{
				printf("\b\b\b\b%3d%%", $percent);
			}
		
			$p++;
		}
	
		print "\n" unless ($g_debug > 0);
	} else {
		print "\n++ No Connects found!\n";
	}
}

print "\n++ Operation complete.\n";
exit(0);
