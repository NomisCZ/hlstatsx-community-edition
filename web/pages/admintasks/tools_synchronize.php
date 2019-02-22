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

&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo IMAGE_PATH; ?>/downarrow.gif" width=9 height=6 class="imageformat"><b>&nbsp;<?php echo $task->title; ?></b><p>

<?php

   $servers[0]["name"] = "ELstatsNEO Masterserver";
   $servers[0]["host"] = "master.elstatsneo.de"; 
   $servers[0]["port"] = 27801;
   $servers[0]["packet"] = chr(255).chr(255)."Z".chr(255)."1.00".chr(255).chr(255).chr(255);

   $servers[1]["name"] = "HLstatsX Masterserver (doesn't work anymore)";
   $servers[1]["host"] = "master.hlstatsx.com"; 
   $servers[1]["port"] = 27501;
   $servers[1]["packet"] = chr(255).chr(255)."Z".chr(255);
  
   

   function hide_cheaters($query)  {
     global $db;
     $result      = $db->query($query);
     $cheater     = array();
     $query       = "UPDATE hlstats_Players SET last_event = IF(hideranking <> 2, UNIX_TIMESTAMP(), last_event), hideranking = 2 WHERE playerId IN ";
     $insert_part = "";
     $first       = 0;
     while (list($player_id) = $db->fetch_row($result))  {
        if ($first == 0)
          $insert_part = "(".$player_id;
        else
          $insert_part .= ",".$player_id;
        $first++;  
     }
     if ($first > 0) {
       echo "<li>Updating <b>$first</b> cheaters...";
       $insert_part .= ")";
       $update_query = $query.$insert_part;
       $db->query($update_query);
       echo "<b>OK</b></li>";
     }  
   }  


    if (isset($_POST['confirm']))
    {
      echo "<ul>\n";
      $s_id = $_POST['masterserver'];
      $host = $servers[$s_id]["host"];
      $port = $servers[$s_id]["port"];
      
      echo "<li>Requesting cheaterlist from <b>$host:$port</b>...";
      $host = gethostbyname($host);
      $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
      $packet = $servers[$s_id]["packet"];
      $bytes_sent = socket_sendto($socket, $packet, strlen($packet), 0, $host, $port);
      echo "<b>".$bytes_sent."</b> bytes <b>OK</b></li>";

      echo "<li>Retrieving data from masterserver...";
      $recv_bytes = 0;
      $buffer     = "";
      $timeout    = 30;
      $answer     = "";
      $packets    = 0;
      $read       = array($socket);
      while (socket_select($read, $write = NULL, $except = NULL, &$timeout) > 0) {
        $recv_bytes += socket_recvfrom($socket, &$buffer, 2000, 0, &$host, &$port);
        if (($buffer[0] == chr(255)) && ($buffer[1] == chr(255)) && ($buffer[2] == "Z") && ($buffer[3] == chr(255)) && 
            ($buffer[4] == "1") && ($buffer[5] == ".") && ($buffer[6] == "0") && ($buffer[7] == "0") && ($buffer[8] == chr(255))) { 
          $answer     .= substr($buffer, 9, strlen($buffer));
        }  
        $buffer     = "";
        $timeout    = "1";
        $packets++;
      }   
      $steam_ids = explode(chr(255), $answer);
      array_pop($steam_ids);
      echo "recieving <b>$recv_bytes</b> bytes in <b>$packets</b> packets...<b>".count($steam_ids)."</b> cheaters...<b>OK</b></li>";
      $query       = "SELECT playerId FROM hlstats_PlayerUniqueIds WHERE uniqueId in ";
      $insert_part = "";
      $first       = 0; 
      foreach ($steam_ids as $entry) {
//        temporary: used to transfer current cheaters to elstatsneo masterserver ~~ 30000 cheaters transferred :)
//        $db->query("INSERT INTO vac_ids VALUES ('$entry')");
        if ($first == 0)
          $insert_part = "('".$entry."'";
        else
          $insert_part .= ",'".$entry."'";
        $first++;
        if ($first % 50 == 0)  {
          $insert_part .= ")";
          $select_query = $query.$insert_part;
          hide_cheaters($select_query);
          $insert_part = "";
          $first       = 0;
        }    
      }
      if ($first > 0) {
        $insert_part .= ")";
        $select_query = $query.$insert_part;
        hide_cheaters($select_query);
      }
      
      echo "<li>Closing connection to masterserver...";
      
      
      socket_close($socket);
      echo "<b>OK</b></li>";
      echo "</ul>\n";
    } else {
        
?>        

<form method="POST">
<table width="60%" align="center" border=0 cellspacing=0 cellpadding=0 class="border">

<tr>
    <td>
        <table width="100%" border=0 cellspacing=1 cellpadding=10>
        
        <tr class="bg1">
            <td class="fNormal">

If you synchronize with one of the selected master servers, some players may be marked as cheater. You will see them on your VAC Cheater list!<br>
Choose preferred masterserver: 
<SELECT NAME="masterserver">

<?php
  $i = 0;
  foreach ($servers as $server) {
   echo "<OPTION VALUE=\"$i\">".$server["name"];
   $i++;
  } 
?>   

</SELECT>

<p>

<input type="hidden" name="confirm" value="1">
<center><input type="submit" value="  Synchronize Stats  "></center>
</td>
        </tr>
        
        </table></td>
</tr>

</table>
</form>

<?php
    }
?>    
    