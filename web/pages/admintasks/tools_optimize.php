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
	if ($auth->userdata["acclevel"] < 100) die ("Access denied!");
?>

&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo IMAGE_PATH; ?>/downarrow.gif" width="9" height="6" class="imageformat"><b>&nbsp;<?php echo $task->title; ?></b><p>


<span style="padding-left:35px;">Optimizing tables...</span></td>
</tr>
</table><br /><br />

<?php
		flush();
		
		$result = $db->query("SHOW TABLES");
		
		while (list($table) = $db->fetch_row($result))
		{
			if ($dbtables) $dbtables .= ", ";
			$dbtables .= $table;
		}
		
		$tableOptimize = new Table(
			array(
				new TableColumn(
					"Table",
					"Table",
					"width=30&sort=no"
				),
				new TableColumn(
					"Op",
					"Operation",
					"width=12&sort=no"
				),
				new TableColumn(
					"Msg_type",
					"Msg. Type",
					"width=12&sort=no"
				),
				new TableColumn(
					"Msg_text",
					"Message",
					"width=46&sort=no"
				)
			),
			"Table",
			"Table",
			"Msg_type",
			false,
			9999
		);
		
		$result = $db->query("OPTIMIZE TABLE $dbtables");
		
		$tableOptimize->draw($result, mysql_num_rows($result), 80);
?>
<br /><br />

<table style="width:90%;text-align:center;border:0" cellspacing="0" cellpadding="2">

<tr>
	<td class="fNormal">Analyzing tables...</td>
</tr>
</table><br /><br />
	
<?php
		$tableAnalyze = new Table(
			array(
				new TableColumn(
					"Table",
					"Table",
					"width=30&sort=no"
				),
				new TableColumn(
					"Op",
					"Operation",
					"width=12&sort=no"
				),
				new TableColumn(
					"Msg_type",
					"Msg. Type",
					"width=12&sort=no"
				),
				new TableColumn(
					"Msg_text",
					"Message",
					"width=46&sort=no"
				)
			),
			"Table",
			"Table",
			"Msg_type",
			false,
			9999
		);
		
		$result = $db->query("ANALYZE TABLE $dbtables");
		
		$tableAnalyze->draw($result, mysql_num_rows($result), 80);
?>