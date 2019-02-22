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

&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo IMAGE_PATH; ?>/downarrow.gif" width=9 height=6 class="imageformat"><b>&nbsp;<?php
	if (isset($_GET['hostgroup']))
	{
    $hostgroup = $_GET['hostgroup'];
        
?><a href="<?php echo $g_options["scripturl"]; ?>?mode=admin&task=<?php echo $selTask; ?>"><?php
	}
	echo $task->title;
  if (isset($_GET['hostgroup']))
	{
		echo "</a>";
	}
?></b> (Last <?php echo $g_options["DeleteDays"]; ?> Days)<?php
  if (isset($_GET['hostgroup']))
	{
?><br>
<img src="<?php echo IMAGE_PATH; ?>/spacer.gif" width=1 height=8 border=0><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo IMAGE_PATH; ?>/downarrow.gif" width=9 height=6 class="imageformat"><b>&nbsp;<?php echo $hostgroup; ?></b><p>
<?php
	}
	else
	{
		echo "<p>";
	}
?>

<?php
  if (isset($_GET['hostgroup']))
	{
         
		$table = new Table(
			array(
				new TableColumn(
					"host",
					"Host",
					"width=41"
				),
				new TableColumn(
					"freq",
					"Connects",
					"width=12&align=right"
				),
				new TableColumn(
					"percent",
					"Percentage of Connects",
					"width=30&sort=no&type=bargraph"
				),
				new TableColumn(
					"percent",
					"%",
					"width=12&sort=no&align=right&append=" . urlencode("%")
				)
			),
			"host",			// keycol
			"freq",			// sort
			"host",			// sort2
			true,			// showranking
			50				// numperpage
		);
		
		if ($hostgroup == "(Unresolved IP Addresses)")
			$hostgroup = "";
		
		$result = $db->query("
			SELECT
				COUNT(*),
				COUNT(DISTINCT ipAddress)
			FROM
				hlstats_Events_Connects
			WHERE
				hostgroup='".mysql_real_escape_string($hostgroup)."'
		");
		
		list($totalconnects, $numitems) = $db->fetch_row($result);
		
		$result = $db->query("
			SELECT
				IF(hostname='', ipAddress, hostname) AS host,
				COUNT(hostname) AS freq,
				(COUNT(hostname) / $totalconnects) * 100 AS percent
			FROM
				hlstats_Events_Connects
			WHERE
				hostgroup='".mysql_real_escape_String($hostgroup)."'
			GROUP BY
				host
			ORDER BY
				$table->sort $table->sortorder,
				$table->sort2 $table->sortorder
			LIMIT
				$table->startitem,$table->numperpage
		");
		
		$table->draw($result, $numitems, 95, "center");
	}
	else
	{
		$table = new Table(
			array(
				new TableColumn(
					"hostgroup",
					"Host",
					"width=41&icon=server&link=" . urlencode("mode=admin&task=tools_ipstats&hostgroup=%k")
				),
				new TableColumn(
					"freq",
					"Connects",
					"width=12&align=right"
				),
				new TableColumn(
					"percent",
					"Percentage of Connects",
					"width=30&sort=no&type=bargraph"
				),
				new TableColumn(
					"percent",
					"%",
					"width=12&sort=no&align=right&append=" . urlencode("%")
				)
			),
			"hostgroup",	// keycol
			"freq",			// sort
			"hostgroup",	// sort2
			true,			// showranking
			50				// numperpage
		);
		
		$result = $db->query("
			SELECT
				COUNT(*),
				COUNT(DISTINCT hostgroup)
			FROM
				hlstats_Events_Connects
		");
		
		list($totalconnects, $numitems) = $db->fetch_row($result);
		
		$result = $db->query("
			SELECT
				IF(hostgroup='', '(Unresolved IP Addresses)', hostgroup) AS hostgroup,
				COUNT(hostgroup) AS freq,
				(COUNT(hostgroup) / $totalconnects) * 100 AS percent
			FROM
				hlstats_Events_Connects
			GROUP BY
				hostgroup
			ORDER BY
				$table->sort $table->sortorder,
				$table->sort2 $table->sortorder
			LIMIT
				$table->startitem,$table->numperpage
		");
		
		$table->draw($result, $numitems, 95, "center");
	}
?>