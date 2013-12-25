#!/usr/bin/perl
################################################################################################################ 
#	hlstats-ftp.pl
################################################################################################################ 
#	Feed HLstatsX with log data via FTP
#	(w) 2009 by Woody (http://woodystree.net)
################################################################################################################ 

use strict;
use Getopt::Long;
use Net::FTP;

use constant VERSION => "0.42";
use constant USAGE => <<EOT

Feed HLstatsX with log data via FTP.

Usage:
   hlstats-ftp.pl --help
   hlstats-ftp.pl --version
   hlstats-ftp.pl --gs-ip=<IP> --gs-port=<PORT> [--ftp-ip=<IP>] --ftp-usr=<USR> --ftp-pwd=<PWD> --ftp-dir=<DIR> [--quiet]

Options:
   --help            display this help and exit  
   --version         output version information and exit
   --gs-ip=IP        game server IP address
   --gs-port=PORT    game server port
   --ftp-ip=IP       ftp log server IP address, if different from
                     game server IP address (--gs-ip)
   --ftp-usr=USR     ftp log server user name
   --ftp-pwd=PWD     ftp log server password
   --ftp-dir=DIR     ftp log server directory
   --quiet           quiet operation, i.e. no output

(w)2009 by Woody (http://woodystree.net)


EOT
;



################################################################################################################ 
#	define output subroutine
################################################################################################################ 
sub output {
	if (!(our $opt_quiet)) {
		my $text = shift;
		print $text;
	}
}



################################################################################################################ 
#	get & parse command line options
################################################################################################################ 
my $opt_help; my $opt_version; my $gs_ip; my $gs_port; my $ftp_ip; my $ftp_usr; my $ftp_pwd; my $ftp_dir; our $opt_quiet;
GetOptions(
	"help"			=>	\$opt_help,
	"version"		=>	\$opt_version,
	"gs-ip=s"		=>	\$gs_ip,
	"gs-port=i"		=>	\$gs_port,
	"ftp-ip=s"		=>	\$ftp_ip,
	"ftp-usr=s"		=>	\$ftp_usr,
	"ftp-pwd=s"		=>	\$ftp_pwd,
	"ftp-dir=s"		=>	\$ftp_dir,
	"quiet"			=>	\$opt_quiet
) or die(USAGE);

if ($opt_help) {
	print USAGE;
	exit(0);
}

if ($opt_version) {
	print "\nhlstats-ftp.pl - Version " . VERSION . "\n";
	print "Feed HLstatsX with log data via FTP.\n";
	print "(w)2009 by Woody (http://woodystree.net)\n\n";
	exit(0);
}

if (!(defined $gs_ip) && !(defined $gs_port) && !(defined $ftp_usr) && !(defined $ftp_pwd) && !(defined $ftp_dir))  {
	die(USAGE);
}

if (!(defined $ftp_ip)) {
	$ftp_ip = $gs_ip;
}



################################################################################################################ 
#	OK, lets go...
################################################################################################################ 
output("\nStarting hlstats-ftp.pl for IP $gs_ip, Port $gs_port...\n\n");



################################################################################################################ 
#	create tmp directory
################################################################################################################ 
output(" - creating tmp directory... ");

my $tmp_dir = "hlstats-ftp-$gs_ip-$gs_port.tmp";

if (-e $tmp_dir) {
	if (!(-w $tmp_dir)) {
		die "Writing to tmp directory is not possible.";
	}
} else {
	mkdir($tmp_dir, 0775) || die "Make tmp directory \"$tmp_dir\" failed: $!";
}

output("OK.\n");



################################################################################################################ 
#	get last mtime info, if any
################################################################################################################ 
output(" - getting last mtime info... ");

my $last_mtime_filename = "hlstats-ftp-$gs_ip-$gs_port.last";
my $last_mtime = 0;

if (-e $last_mtime_filename) {
	open(LASTMTIME, "<$last_mtime_filename") || die "Open file \"$last_mtime_filename\" failed: $!";
	$last_mtime = <LASTMTIME>;
	close(LASTMTIME);
	output("OK: last mtime $last_mtime.\n");
} else {
	output("none: using default 0.\n");
}



################################################################################################################ 
#	establish ftp connection
################################################################################################################ 
output(" - establishing FTP connection... ");

my $ftp = Net::FTP->new($ftp_ip) || die "FTP connect to \"$ftp_ip\" failed: $!";
$ftp->login($ftp_usr,$ftp_pwd) || die "FTP login for user \"$ftp_usr\" failed: $!";
$ftp->binary() || die "FTP binary mode failed: $!";
$ftp->cwd($ftp_dir) || die "FTP chdir to \"$ftp_dir\" failed: $!";

output("OK.\n");



################################################################################################################ 
#	get complete list of log files
################################################################################################################ 
output(" - getting complete list of log files... ");

my @files = $ftp->ls("-t *.log"); # get list of log files sorted by mtime
$#files = $#files - 1; # skip last file, i.e. the latest file, which is possibly still in use by the game server

output("OK.\n");



################################################################################################################ 
#	transfer log files to tmp directory, if todo
################################################################################################################ 
output(" + transfering log files, if todo:\n");

my @todo_files = ();
my @todo_mtimes = ();

foreach my $file (@files) {
	output("    - \"$file\" ");
	my $mtime = $ftp->mdtm($file) || die "FTP mtdm failed: $!";
	output("(mtime $mtime): ");
	if ($mtime > $last_mtime) {
		output("transferring... ");
		$ftp->get($file, "$tmp_dir/$file") || die "FTP get with \"$file\" failed: $!";
		push(@todo_files, $file);
		push(@todo_mtimes, $mtime);
		output("OK.\n");
	} else {
		output("skipping.\n");
	}
}



################################################################################################################ 
#	close ftp connection
################################################################################################################ 
output(" - closing FTP connection... ");

$ftp->close() || die "FTP close failed: $!";

output("OK.\n");



################################################################################################################ 
#	process log files in tmp directory
################################################################################################################ 
output(" + parsing log files:\n");

for (my $i = 0; $i <= $#todo_files; $i++) {
	my $progress = "(" . ($i+1) . "/" . ($#todo_files+1) . ")";
	output("    - \"" . $todo_files[$i] . "\" " .  $progress . ": parsing... ");
	system("./hlstats.pl --stdin --server-ip $gs_ip --server-port $gs_port < $tmp_dir/" . $todo_files[$i] . " > /dev/null");
	output("updating last mtime...");
	open(LASTMTIME, ">$last_mtime_filename") || die "Open file \"$last_mtime_filename\" failed: $!";
	print LASTMTIME $todo_mtimes[$i];
	close(LASTMTIME);
	output("OK.\n");    
}



################################################################################################################ 
#	delete tmp log files and directory
################################################################################################################ 
output(" - delete tmp log files and directory... ");

foreach my $file (<$tmp_dir/*>) {
    unlink($file) || die "Delete tmp log files failed: $!";    
}
rmdir($tmp_dir) || die "Delete tmp directory failed: $!";

output("OK.\n");



################################################################################################################ 
#	the end
################################################################################################################ 
output("\nSo Long, and Thanks for all the Fish.\n\n");
