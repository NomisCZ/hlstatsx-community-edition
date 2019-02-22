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
	$id=-1;
	if ((isset($_GET['id'])) && (is_numeric($_GET['id'])))
		$id = valid_request($_GET['id'], 1);
	
	$result = $db->query("SELECT `value` FROM hlstats_Options_Choices WHERE `keyname` = 'google_map_region' ORDER BY `value`");
    while ($rowdata = $db->fetch_row($result))
    {
        $mapselect.=";".$rowdata[0]."/".ucwords(strtolower($rowdata[0]));
    }
	$mapselect.=";";   
?>

&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo IMAGE_PATH; ?>/downarrow.gif" width="9" height="6" class="imageformat" alt="" /><b>&nbsp;<a href="<?php echo $g_options['scripturl']; ?>?mode=admin&amp;task=tools_editdetails">Edit Player or Clan Details</a></b><br />

<img src="<?php echo IMAGE_PATH; ?>/spacer.gif" width="1" height="8" border="0"><br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo IMAGE_PATH; ?>/downarrow.gif" width="9" height="6" class="imageformat" alt="" /><b>&nbsp;<?php echo "Edit Clan #$id"; ?></b><br /><br />

<form method="post" action="<?php echo $g_options['scripturl'] . "?mode=admin&amp;task=$selTask&amp;id=$id&" . strip_tags(SID); ?>">
<?php
	$proppage = new PropertyPage("hlstats_Clans", "clanId", $id, array(
		new PropertyPage_Group("Profile", array(
			new PropertyPage_Property("name", "Clan Name", "text"),
			new PropertyPage_Property("homepage", "Homepage URL", "text"),
			new PropertyPage_Property("mapregion", "Map Region", "select", $mapselect),
			new PropertyPage_Property("hidden", "1 = Hide from clan list", "text")
		))
	));
	
	
	if (isset($_POST['name']))
	{
		$proppage->update();
		message("success", "Profile updated successfully.");
	}
	
	
	$result = $db->query("
		SELECT
			*
		FROM
			hlstats_Clans
		WHERE
			clanId='$id'
	");
	if ($db->num_rows() < 1) die("No clan exists with ID #$id");
	
	$data = $db->fetch_array($result);
	
	echo "<span class='fTitle'>";
	echo $data['tag'];
	echo "</span>";
	
	echo "<span class='fNormal'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
		. "<a href=\"" . $g_options['scripturl'] . "?mode=claninfo&amp;clan=$id&amp;" . strip_tags(SID) . "\">"
		. "(View Clan Details)</a></span>";
?><br /><br />

<table width="60%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td class="fNormal"><?php
		$proppage->draw($data);
?>
	<center><input type="submit" value="  Apply  " class="submit"></center></td>
</tr>
</table>
</form>
