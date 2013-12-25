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
	
	if ( count($_POST) > 0 )
	{
		$db->query("SELECT * FROM `hlstats_Servers` WHERE `address` = '" . $db->escape(clean_data($_POST['server_address'])) . "' AND `port` = '" . $db->escape(clean_data($_POST['server_port'])) . "'");
		
		if ( $row = $db->fetch_array() )
			message("warning", "Server [" . $row['name'] . "] already exists");
		else
		{
			$db->query("SELECT `realgame` FROM `hlstats_Games` WHERE `code` = '" . $db->escape($selGame) . "'");
			if ( list($game) = $db->fetch_row() )
			{
				$script_path = (isset($_SERVER['SSL']) || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on")) ? 'https://' : 'http://';
				$script_path .= $_SERVER['HTTP_HOST'];
				$script_path .= str_replace("\\","/",dirname($_SERVER["PHP_SELF"]));
				$db->query(sprintf("INSERT INTO `hlstats_Servers` (`address`, `port`, `name`, `game`, `publicaddress`, `rcon_password`) VALUES ('%s', '%d', '%s', '%s', '%s', '%s')",
					$db->escape(clean_data($_POST['server_address'])),
					$db->escape(clean_data($_POST['server_port'])),
					$db->escape(clean_data($_POST['server_name'])),
					$db->escape($selGame),
					$db->escape(clean_data($_POST['public_address'])),
					$db->escape(mystripslashes($_POST['server_rcon']))
				));
				$insert_id = $db->insert_id();
				$db->query("INSERT INTO `hlstats_Servers_Config` (`serverId`, `parameter`, `value`)
						SELECT '" . $insert_id . "', `parameter`, `value`
						FROM `hlstats_Mods_Defaults` WHERE `code` = '" . $db->escape(mystripslashes($_POST['game_mod'])) . "';");
				$db->query("INSERT INTO `hlstats_Servers_Config` (`serverId`, `parameter`, `value`) VALUES
						('" . $insert_id . "', 'Mod', '" . $db->escape(mystripslashes($_POST['game_mod'])) . "');");
				$db->query("INSERT INTO `hlstats_Servers_Config` (`serverId`, `parameter`, `value`)
						SELECT '" . $insert_id . "', `parameter`, `value`
						FROM `hlstats_Games_Defaults` WHERE `code` = '" . $db->escape($game) . "'
						ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);");
				$db->query("UPDATE hlstats_Servers_Config
							SET `value` = '" . $db->escape($script_path) . "'
							WHERE serverId = '" . $insert_id . "' AND `parameter` = 'HLStatsURL'");
				$_POST = array();
				
				// psychonic - worst. redirect. ever.
				//   but we can't just use header() since admin.php already started part of the page and hacking it in before would be even messier
				echo "<script type=\"text/javascript\"> window.location.href=\"".$g_options['scripturl']."?mode=admin&game=$selGame&task=serversettings&key=$insert_id#startsettings\"; </script>";
				exit;
			}
		}
	}
	
	function clean_data($data)
	{
		return trim(htmlspecialchars(mystripslashes($data)));
	}
	
?>
Enter the address of a server that you want to accept data from.<br /><br />
The "Public Address" should be the address you want shown to users. If left blank, it will be generated from the IP Address and Port. If you are using any kind of log relaying utility (i.e. hlstats.pl will not be receiving data directly from the game servers), you will want to set the IP Address and Port to the address of the log relay program, and set the Public Address to the real address of the game server. You will need a separate log relay for each game server. You can specify a hostname (or anything at all) in the Public Address.<p>

<table width="100%" border="0" cellspacing="0" cellpadding="0">

<tr valign="top" class="table_border">
	<td>
		<script type="text/javascript">
		function checkMod() {
			if (!document.newserverform.server_address.value.match(/^\b(?:[0-9]{1,3}\.){3}[0-9]{1,3}\b$/)) {
				alert('Server address must be a valid IP address');
				return false;
			}
			if (document.newserverform.game_mod.value == 'PLEASESELECT') {
				alert('You must make a selection for Admin Mod');
				return false;
			}
			document.newserverform.submit();
		}
		</script>
		<table width="100%" border=0 cellspacing=1 cellpadding=4>
			<tr valign="bottom" class="head">
				<td class='fSmall'>Server IP Address</td>
				<td class='fSmall'><input type="text" name="server_address" maxlength="15" size="15" value="<?php echo clean_data($_POST['server_address']); ?>" /></td>
			</tr>
			<tr valign="bottom" class="head">
				<td class='fSmall'>Server Port</td>
				<td class='fSmall'><input type="text" name="server_port" maxlength="5" size="5" value="<?php echo clean_data($_POST['server_port']); ?>" /></td>
			</tr>
			<tr valign="bottom" class="head">
				<td class='fSmall'>Server Name</td>
				<td class='fSmall'><input type="text" name="server_name" maxlength="255" size="35" value="<?php echo clean_data($_POST['server_name']); ?>" /></td>
			</tr>
			<tr valign="bottom" class="head">
				<td class='fSmall'>Rcon Password</td>
				<td class='fSmall'><input type="text" name="server_rcon" maxlength="128" size="15" value="<?php echo clean_data($_POST['server_rcon']); ?>" /></td>
			</tr>
			<tr valign="bottom" class="head">
				<td class='fSmall'>Public Address</td>
				<td class='fSmall'><input type="text" name="public_address" maxlength="128" size="15" value="<?php echo clean_data($_POST['public_address']); ?>" /></td>
			</tr>
			<tr valign="bottom" class="head">
				<td class='fSmall'>Admin Mod</td>
				<td class='fSmall'>
					<select name="game_mod">
					<option value="PLEASESELECT">PLEASE SELECT</option>
					<?php
					$db->query("SELECT code, name FROM `hlstats_Mods_Supported`");
					while ( $row = $db->fetch_array() )
					{
						echo '<option value="' . $row['code'] . '">' . $row['name'] . '</option>';
					}
					?>
					</select>
				</td>
			</tr>
		</table>
	</td>
</tr>
	<table width="75%" border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td align="center"><input type="submit" value="  Add Server  " class="submit" onclick="checkMod();return false;"></td>
	</tr>
	</table>
