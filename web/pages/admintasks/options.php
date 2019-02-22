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

	if ($auth->userdata['acclevel'] < 80) die ('Access denied!'); ?>
	
	<div style="width:60%;height:50px;border:0;padding:0;margin:auto;background-color:#F00;text-align:center;color:#FFF;font-size:medium;font-weight:bold;vertical-align:middle;">
		Options with an asterisk (*) beside them require a restart of the perl daemon to fully take effect.</div>
	<br />
<?php

	class OptionGroup
	{
		var $title = '';
		var $options = array();
		
		function OptionGroup ($title)
		{
			$this->title = $title;
		}
		
		function draw ()
		{
			global $g_options;
?>
	<p><strong><?php echo $this->title; ?></strong></p>
	<table class="data-table" style="width:75%">
		<?php
			foreach ($this->options as $opt)
			{
				$opt->draw();
			}
?>
	</table>
<?php
		}
		
		function update ()
		{
			global $db;
			
			foreach ($this->options as $opt)
			{
				if (($this->title == 'Fonts') || ($this->title == 'General')) {
					$optval = $_POST[$opt->name];
					$search_pattern  = array('/script/i', '/;/', '/%/');
					$replace_pattern = array('', '', '');
					$optval = preg_replace($search_pattern, $replace_pattern, $optval);
				} else {
					$optval = valid_request($_POST[$opt->name], 0);
 	 			}
				
				$result = $db->query("
					SELECT
						value
					FROM
						hlstats_Options
					WHERE
						keyname='$opt->name'
				");
				
				if ($db->num_rows($result) == 1)
				{
					$result = $db->query("
						UPDATE
							hlstats_Options
						SET
							value='$optval'
						WHERE
							keyname='$opt->name'
					");
				}
				else
				{
					$result = $db->query("
						INSERT INTO
							hlstats_Options
							(
								keyname,
								value
							)
						VALUES
						(
							'$opt->name',
							'$optval'
						)
					");
				}
			}
		}

	}
	
	class Option
	{
		var $name;
		var $title;
		var $type;
		
		function Option ($name, $title, $type)
		{
			$this->name = $name;
			$this->title = $title;
			$this->type = $type;
		}
		
		function draw ()
		{
			global $g_options, $optiondata, $db;
			
?>
					<tr class="bg1" style="vertical-align:middle";>
						<td class="fNormal" style="width:45%;"><?php
			echo $this->title . ":";
						?></td>
						<td style="width:55%;"><?php
			switch ($this->type)
			{
				case 'textarea':
					echo "<textarea name=\"$this->name\" cols=\"35\" rows=\"4\" wrap=\"virtual\">";
					echo html_entity_decode($optiondata[$this->name]);
					echo '</textarea>';
					break;
					
				case 'styles':
					echo "<select name=\"$this->name\" style=\"width: 226px\">";
					$d = dir('styles');
					while (false !== ($e = $d->read()))  {
						if (is_file("styles/$e") && ($e != '.') && ($e != '..')) {
							$ename = ucwords(strtolower(str_replace(array('_','.css'), array(' ',''), $e)));
							$sel = '';
							if ($e==$g_options['style'])
								$sel = 'selected="selected"';
							echo "<option value=\"$e\"$sel>$ename</option>";
						} 
					}
					$d->close();
					echo '</select>';
					break;
				
				case 'select':
					echo "<select name=\"$this->name\" style=\"width: 226px\">";
					$result = $db->query("SELECT `value`,`text` FROM hlstats_Options_Choices WHERE keyname='$this->name' ORDER BY isDefault desc");
					while ($rowdata = $db->fetch_array($result)) {
						if ($rowdata['value'] == $optiondata[$this->name]) {
							echo '<option value="'.$rowdata['value'].'" selected="selected">'.$rowdata['text'];
						} else {
							echo '<option value="'.$rowdata['value'].'">'.$rowdata['text'];
						}
					}
					echo '</select>';
					break;
					
				default:
					echo "<input type=\"text\" name=\"$this->name\" size=\"35\" value=\"";
					echo html_entity_decode($optiondata[$this->name]);
					echo '" class="textbox" maxlength="255" />';
			}
						?></td>
					</tr>
<?php
		}
	}
	
	$optiongroups = array();

	$optiongroups[0] = new OptionGroup('Site Settings');
	$optiongroups[0]->options[] = new Option('sitename', 'Site Name', 'text');
	$optiongroups[0]->options[] = new Option('siteurl', 'Site URL', 'text');
	$optiongroups[0]->options[] = new Option('contact', 'Contact URL', 'text');
	$optiongroups[0]->options[] = new Option('bannerdisplay', 'Show Banner', 'select');
	$optiongroups[0]->options[] = new Option('bannerfile', 'Banner file name (in hlstatsimg/) or full banner URL', 'text');
	$optiongroups[0]->options[] = new Option('playerinfo_tabs', 'Use tabs in playerinfo to show/hide sections current page or just show all at once', 'select');
	$optiongroups[0]->options[] = new Option('slider', 'Enable AJAX gliding server list (accordion effect) on homepage of each game (only affects games with more than one server)', 'select');
	$optiongroups[0]->options[] = new Option('nav_globalchat', 'Show Chat nav-link', 'select');
	$optiongroups[0]->options[] = new Option('nav_cheaters', 'Show Banned Players nav-link', 'select');
	$optiongroups[0]->options[] = new Option('sourcebans_address', 'SourceBans URL<br />Enter the relative or full path to your SourceBans web site, if you have one. Ex: http://www.yoursite.com/sourcebans/ or /sourcebans/', 'text');
	$optiongroups[0]->options[] = new Option('forum_address', 'Forum URL<br />Enter the relative or full path to your forum/message board, if you have one. Ex: http://www.yoursite.com/forum/ or /forum/', 'text');
	$optiongroups[0]->options[] = new Option('show_weapon_target_flash', 'Show hitbox flash animation instead of plain html table for games with accuracy tracking (on supported games)', 'select');
	$optiongroups[0]->options[] = new Option('show_server_load_image', 'Show load summaries from all monitored servers', 'select');
	$optiongroups[0]->options[] = new Option('showqueries', 'Show "Executed X queries, generated this page in Y Seconds." message in footer?', 'select');
	$optiongroups[0]->options[] = new Option('sigbackground', 'Default background for forum signature(Numbers 1-11 or random)<br />Look in sig folder to see background choices', 'text');
	$optiongroups[0]->options[] = new Option('modrewrite', 'Use modrewrite to make forum signature image compatible with more forum types. (To utilize this, you <strong>must</strong> have modrewrite enabled on your webserver and add the following text to a .htaccess file in the directory of hlstats.php)<br /><br /><textarea rows="3" cols="72" style="overflow:hidden;">
RewriteEngine On
RewriteRule sig-(.*)-(.*).png$ sig.php?player_id=$1&background=$2 [L]</textarea>', 'select');
	
	$optiongroups[1] = new OptionGroup('GeoIP data & Google Map settings');
	$optiongroups[1]->options[] = new Option('countrydata', 'Show features requiring GeoIP data', 'select');
	$optiongroups[1]->options[] = new Option('show_google_map', 'Show Google worldmap', 'select');
	$optiongroups[1]->options[] = new Option('google_map_region', 'Google Maps Region', 'select');
	$optiongroups[1]->options[] = new Option('google_map_type', 'Google Maps Type', 'select');
	$optiongroups[1]->options[] = new Option('UseGeoIPBinary', '*Choose whether to use GeoCityLite data loaded into mysql database or from binary file. (If binary, GeoLiteCity.dat goes in perl/GeoLiteCity and Geo::IP::PurePerl module is required', 'select');

	$optiongroups[2] = new OptionGroup('Awards settings');
	$optiongroups[2]->options[] = new Option('gamehome_show_awards', 'Show daily award winners on Game Frontpage', 'select');
	$optiongroups[2]->options[] = new Option('awarddailycols', 'Daily Awards: columns count', 'text');
	$optiongroups[2]->options[] = new Option('awardglobalcols', 'Global Awards: columns count', 'text');
	$optiongroups[2]->options[] = new Option('awardrankscols', 'Player Ranks: columns count', 'text');
	$optiongroups[2]->options[] = new Option('awardribbonscols', 'Ribbons: columns count', 'text');

	$optiongroups[3] = new OptionGroup('Hit counter settings');
	$optiongroups[3]->options[] = new Option('counter_visit_timeout', 'Visit cookie timeout in minutes', 'text');
	$optiongroups[3]->options[] = new Option('counter_visits', 'Current Visits', 'text');
	$optiongroups[3]->options[] = new Option('counter_hits', 'Current Page Hits', 'text');
	
	$optiongroups[20] = new OptionGroup('Paths');
	$optiongroups[20]->options[] = new Option('map_dlurl', 'Map Download URL<br /><span class="fSmall">(%MAP% = map, %GAME% = gamecode)</span>. Leave blank to suppress download link.', 'text');

	$optiongroups[30] = new OptionGroup('Visual style settings');
	$optiongroups[30]->options[] = new Option('graphbg_load', 'Server Load graph: background color hex# (RRGGBB)', 'text');
	$optiongroups[30]->options[] = new Option('graphtxt_load', 'Server Load graph: text color# (RRGGBB)', 'text');
	$optiongroups[30]->options[] = new Option('graphbg_trend', 'Player Trend graph: background color hex# (RRGGBB)', 'text');
	$optiongroups[30]->options[] = new Option('graphtxt_trend', 'Player Trend graph: text color hex# (RRGGBB)', 'text');
	$optiongroups[30]->options[] = new Option('style', 'Stylesheet filename to use', 'styles');
	$optiongroups[30]->options[] = new Option('display_style_selector', 'Display Style Selector?<br />Allow end users to change the style they are using.', 'select');
	$optiongroups[30]->options[] = new Option('display_gamelist', 'Enable Gamelist icons<br />Enables or Disables the game icons near the top-right of all pages.', 'select');

	
	$optiongroups[35] = new OptionGroup('Ranking settings');
	$optiongroups[35]->options[] = new Option('rankingtype', '*Ranking type', 'select');
	$optiongroups[35]->options[] = new Option('MinActivity', '*HLstatsX will automatically hide players which have no event more days than this value. (Default 28 days)', 'text');
	
	$optiongroups[40] = new OptionGroup('Daemon Settings');
	$optiongroups[40]->options[] = new Option('Mode', '*Sets the player-tracking mode.<br><ul><LI><b>Steam ID</b>     - Recommended for public Internet server use. Players will be tracked by Steam ID.<LI><b>Player Name</b>  - Useful for shared-PC environments, such as Internet cafes, etc. Players will be tracked by nickname. <LI><b>IP Address</b>        - Useful for LAN servers where players do not have a real Steam ID. Players will be tracked by IP Address. </UL>', 'select');
	$optiongroups[40]->options[] = new Option('AllowOnlyConfigServers', '*Allow only servers set up in admin panel to be tracked. Other servers will NOT automatically added and tracked! This is a big security thing', 'select');
	$optiongroups[40]->options[] = new Option('DeleteDays', '*HLstatsX will automatically delete history events from the events tables when they are over this many days old. This is important for performance reasons. Set lower if you are logging a large number of game servers or find the load on the MySQL server is too high', 'text');
	$optiongroups[40]->options[] = new Option('DNSResolveIP', '*Resolve player IP addresses to hostnames. Requires a working DNS setup (on the box running hlstats.pl)', 'select');
	$optiongroups[40]->options[] = new Option('DNSTimeout', '*Time, in seconds, to wait for DNS queries to complete before cancelling DNS resolves. You may need to increase this if on a slow connection or if you find a lot of IPs are not being resolved; however, hlstats.pl cannot be parsing log data while waiting for an IP to resolve', 'text');
	$optiongroups[40]->options[] = new Option('MailTo', '*E-mail address to mail database errors to', 'text');
	$optiongroups[40]->options[] = new Option('MailPath', '*Path to the mail program -- usually /usr/sbin/sendmail on webhosts', 'text');
	$optiongroups[40]->options[] = new Option('Rcon', '*Allow HLstatsX to send Rcon commands to the game servers', 'select');
	$optiongroups[40]->options[] = new Option('RconIgnoreSelf', '*Ignore (do not log) Rcon commands originating from the same IP as the server being rcon-ed (useful if you run any kind of monitoring script which polls the server regularly by rcon)', 'select');
	$optiongroups[40]->options[] = new Option('RconRecord', '*Record Rcon commands to the Admin event table. This can be useful to see what your admins are doing, but if you run programs like PB it can also fill your database up with a lot of useless junk', 'select');
	$optiongroups[40]->options[] = new Option('UseTimestamp', '*If no (default), use the current time on the database server for the timestamp when recording events. If yes, use the timestamp provided on the log data. Unless you are processing old log files on STDIN or your game server is in a different timezone than webhost, you probably want to set this to no', 'select');
	$optiongroups[40]->options[] = new Option('TrackStatsTrend', '*Save how many players, kills etc, are in the database each day and give access to graphical statistics', 'select');
	$optiongroups[40]->options[] = new Option('GlobalBanning', '*Make player bans available on all participating servers. Players who were banned permanently are automatic hidden from rankings', 'select');
	$optiongroups[40]->options[] = new Option('LogChat', '*Log player chat to database', 'select');
	$optiongroups[40]->options[] = new Option('LogChatAdmins', '*Log admin chat to database', 'select');
	$optiongroups[40]->options[] = new Option('GlobalChat', '*Broadcast chat messages through all particapting servers. To all, none, or admins only', 'select');
	
	$optiongroups[50] = new OptionGroup('Point calculation settings');
	$optiongroups[50]->options[] = new Option('SkillMaxChange', '*Maximum number of skill points a player will gain from each frag. Default 25', 'text');
	$optiongroups[50]->options[] = new Option('SkillMinChange', '*Minimum number of skill points a player will gain from each frag. Default 2', 'text');
	$optiongroups[50]->options[] = new Option('PlayerMinKills', '*Number of kills a player must have before receiving regular points. (Before this threshold is reached, the killer and victim will only gain/lose the minimum point value) Default 50', 'text');
	$optiongroups[50]->options[] = new Option('SkillRatioCap', '*Cap killer\'s gained skill with ratio using *XYZ*SaYnt\'s method "designed such that an excellent player will have to get about a 2:1 ratio against noobs to hold steady in points"', 'select');

	$optiongroups[60] = new OptionGroup('Proxy Settings');
	$optiongroups[60]->options[] = new Option('Proxy_Key', '*Key to use when sending remote commands to Daemon, empty for disable', 'text');
	$optiongroups[60]->options[] = new Option('Proxy_Daemons', '*List of daemons to send PROXY events from (used by proxy-daemon.pl), use "," as delimiter, eg &lt;ip&gt;:&lt;port&gt;,&lt;ip&gt;:&lt;port&gt;,... ', 'text');
	
	if (!empty($_POST))
	{
			foreach ($optiongroups as $og)
			{
				$og->update();
			}
			message('success', 'Options updated successfully.');
	}
	
	
	$result = $db->query("SELECT keyname, value FROM hlstats_Options");
	while ($rowdata = $db->fetch_row($result))
	{
		$optiondata[$rowdata[0]] = $rowdata[1];
	}
	
	foreach ($optiongroups as $og)
	{
		$og->draw();
	}
?>
	<tr style="height:50px;">
		<td style="text-align:center;" colspan="2"><input type="submit" value="  Apply  " class="submit" /></td>
	</tr>
</table>

