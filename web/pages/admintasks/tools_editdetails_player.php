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

	if ( !defined('IN_HLSTATS') )
	{
		die('Do not access this file directly.');
	}
	if ($auth->userdata["acclevel"] < 80)
	{
		die ("Access denied!");
	}
	
	$id=-1;
	if ((isset($_GET['id'])) && (is_numeric($_GET['id'])))
	{
		$id = valid_request($_GET['id'], 1);
	}
?>

&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo IMAGE_PATH; ?>/downarrow.gif" width="9" height="6" class="imageformat" alt="" /><b>&nbsp;<a href="<?php echo $g_options['scripturl']; ?>?mode=admin&amp;task=tools_editdetails">Edit Player or Clan Details</a></b><br />

<img src="<?php echo IMAGE_PATH; ?>/spacer.gif" width="1" height="8" border="0" alt=""><br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo IMAGE_PATH; ?>/downarrow.gif" width="9" height="6" class="imageformat" alt="" /><b>&nbsp;<?php echo "Edit Player #$id"; ?></b><br /><br />

<form method="post" action="<?php echo $g_options['scripturl'] . "?mode=admin&amp;task=$selTask&amp;id=$id&amp;" . strip_tags(SID); ?>">
<?php

  // get available country flag files
	$result = $db->query("SELECT `flag`,`name` FROM hlstats_Countries ORDER BY `name`");
    while ($rowdata = $db->fetch_row($result))
    {
        $flagselect.=";".$rowdata[0]."/".$rowdata[1];
    }
	$flagselect.=";";

	$proppage = new PropertyPage("hlstats_Players", "playerId", $id, array(
		new PropertyPage_Group("Profile", array(
			new PropertyPage_Property("fullName", "Real Name", "text"),
			new PropertyPage_Property("email", "E-mail Address", "text"),
			new PropertyPage_Property("homepage", "Homepage URL", "text"),
			new PropertyPage_Property("flag", "Country Flag", "select",$flagselect),
			new PropertyPage_Property("skill", "Points", "text"),
			new PropertyPage_Property("kills", "Kills", "text"),
			new PropertyPage_Property("deaths", "Deaths", "text"),
			new PropertyPage_Property("headshots", "Headshots", "text"),
			new PropertyPage_Property("suicides", "Suicides", "text"),
			new PropertyPage_Property("hideranking", "Hide Ranking", "select", "0/No;1/Yes;2/Flag as Banned;3/Inactive (Automatic);"),
			new PropertyPage_Property("blockavatar", "Force Default Avatar Image (note that this overrides images in hlstatsimg/avatars)", "select", "0/No;1/Yes;"),
		))
	));
	
	if (isset($_POST['fullName']))
	{
		$proppage->update();
		message("success", "Profile updated successfully.");
	}
	$playerId = $db->escape($id);
	$result = $db->query("
		SELECT
			*
		FROM
			hlstats_Players
		WHERE
			playerId='$playerId'
	");
	if ($db->num_rows() < 1) die("No player exists with ID #$id");
	
	$data = $db->fetch_array($result);
	
	echo '<span class="fTitle">';
	echo $data['lastName'];
	echo '</span>';
	
	echo '<span class="fNormal">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
		. '<a href="' . $g_options['scripturl'] . "?mode=playerinfo&amp;player=$id&amp;" . strip_tags(SID) . '">'
		. '(View Player Details)</a></span>';
?><br /><br />

<table width="60%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td class="fNormal"><?php
		$proppage->draw($data);
?>
	<center><input type="submit" value="  Apply  " class="submit" /></center></td>
</tr>
</table>
</form>

<?php
	$tblIps = new Table
	(
		array
		(
			new TableColumn
			(
				'ipAddress',
				'IP Address',
				'width=40'
			),
			new TableColumn
			(
				'eventTime',
				'Last Used',
				'width=60'
			)
		),
		'ipAddress',
		'eventTime',
		'eventTime'
	);
	$result = $db->query
	("
		SELECT
			ipAddress,
			eventTime
		FROM
			hlstats_Events_Connects
		WHERE
			playerId = $playerId
		GROUP BY
			ipAddress
		ORDER BY
			eventTime DESC
	");
?>
<div class="block">
<?php
	printSectionTitle('Player IP Addresses');
	$tblIps->draw($result, 50, 50);
?>
</div><br /><br />
