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
	if ($auth->userdata["acclevel"] < 80) die ("Access denied!");
?>

&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo IMAGE_PATH; ?>/downarrow.gif" ><strong>&nbsp;<?php echo $task->title; ?></strong>

<?php

   $commands[0]["name"] = "Reload Configuration";
   $commands[0]["cmd"] = "RELOAD";
   $commands[1]["name"] = "Shut down the Daemon *";
   $commands[1]["cmd"] = "KILL";
    
 
    if (isset($_POST['confirm']))
    {
		$host = $_POST['masterserver'];
		$port = $_POST["port"];
		$command = $commands[$_POST["command"]]["cmd"];
		if (!$command) die ('Invalid command!');
		if ($port==0) $port = "27500";

		// Check if we're contacting a remote host -- if so, need proxy_key configured for this to work (die and throw an error if we're missing it)
		if (($host != "127.0.0.1") && ($host != "localhost")) 
		{
			if ($g_options['Proxy_Key'] == "") 
			{
				echo "<p><strong>Warning:</strong> You are connecting to a remote daemon and do not have a Proxy Key configured.</p>";
				
				echo "<p>Please visit the <a href=\"{$g_options['scripturl']}?mode=admin&task=options#options\">HLstatsX:CE Settings page</a> and configure a Proxy Key.  Once configured, manually restart your daemon.</p>";
				die();
			}
		}
		
		echo "<div style=\"margin-left: 50px;\"><ul>\n";      
		echo "<li>Sending Command to HLstatsX: CE Daemon at $host:$port &mdash; ";
		$host = gethostbyname($host);
		$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		$packet = "";
		if ($g_options['Proxy_Key'])
		{
			$packet = "PROXY Key={$g_options['Proxy_Key']} PROXY C;".$command.";";
		}
		else
		{
			$packet = "C;".$command.";";
		}
		$bytes_sent = socket_sendto($socket, $packet, strlen($packet), 0, $host, $port);
		echo "<strong>".$bytes_sent."</strong> bytes <strong>OK</strong></li>";

		echo "<li>Waiting for Backend Answer...";
		$recv_bytes = 0;
		$buffer     = "";
		$timeout    = 5;
		$answer     = "";
		$packets    = 0;
		$read       = array($socket);
		while (socket_select($read, $write = NULL, $except = NULL, $timeout) > 0) {
			$recv_bytes += socket_recvfrom($socket, $buffer, 2000, 0, $host, $port);
			$answer     .= $buffer;
			$buffer     = "";
			$timeout    = "1";
			$packets++;
		}   


		echo "recieving <strong>$recv_bytes</strong> bytes in <strong>$packets</strong> packets...<strong>OK</strong></li>";
      
		if ($packets>0) {
			echo "<li>Backend Answer: ".$answer."</li>";
		} 
		else 
		{
			echo "<li><em>No packets received &mdash; check if backend dead or not listening on $host:$port</em></li>";
		}
      
		echo "<li>Closing connection to backend...";
		socket_close($socket);
		echo "<strong>OK</strong></li>";
		echo "</ul></div>\n";
		
		echo "<img src=\"".IMAGE_PATH."/rightarrow.gif\" /> <a href=\"{$g_options['scripturl']}?mode=admin\">Return to Administration Center</a>";
		}
		else
		{
        
?>        

<p>After every configuration change made in the Administration Center, you should reload the daemon configuration.  To do so, enter the hostname or IP address of your HLXCE daemon and choose the reload option.  You can also shut down your daemon from this panel.  <strong>NOTE: The daemon can not be restarted through the web interface!</strong></p>

<form method="POST">

	<table class="data-table">
		<tr class="bg1">
			<td width="40%"><label for="masterserver">Daemon IP or Hostname:</label><p>Hostname or IP address of your HLX:CE Daemon<br />Normally the IP or Hostname listed in the "logaddress_add" line on your game server.<br />example: daemon1.hlxce.com <em>or</em> 1.2.3.4</p></td>
			<td><input type="text" name="masterserver" value="localhost"></td>
		</tr>
		<tr class="bg2">
			<td><label for="port">Daemon Port:</label><p>Port number the daemon (or proxy_daemon) is listening on.<br />Normally the port listed in the "logaddress_add" line on your game server configuration.<br />example: 27500</p></td>
			<td><input type="text" name="port" value="27500" size="6"></td>
		</tr>
		<tr class="bg1">
			<td><label for="command">Command:</label><p>Select the operation to perform on the daemon<br /><strong>* Note: If you shut the daemond down through this page it can not be restarted through this interface!</strong></p></td>
			<td><SELECT NAME="command"><?php
  $i = 0;
  foreach ($commands as $cmd) {
   echo "<OPTION VALUE=\"$i\">".$cmd["name"];
   $i++;
  } 
?>
					</SELECT></td>
	</table>
	
	<input type="hidden" name="confirm" value="1">
	<div style="text-align: center; margin-top: 20px;">
		<input type="submit" value="  EXECUTE  ">
	</div>
</form>

<?php
    }
?>    