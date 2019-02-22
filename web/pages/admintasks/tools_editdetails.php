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

<span style="padding-left:35px;">You can enter a player or clan ID number directly, or you can search for a player or clan.</span><p>

<table border="0" width="95%" align="center" border=0 cellspacing=0 cellpadding=0>

<tr valign="top">
	<td width="100%" class="fNormal">&nbsp;<img src="<?php echo IMAGE_PATH; ?>/downarrow.gif" width=9 height=6 class="imageformat"><b>&nbsp;Jump Direct</b><p>
	
		<form method="GET" action="<?php echo $g_options["scripturl"]; ?>">
		<input type="hidden" name="mode" value="admin">
		<table width="100%" border=0 cellspacing=0 cellpadding=0>
		
		<tr>
			<td width="5%">&nbsp;</td>
			<td width="95%">
				<table width="40%" border=0 cellspacing=0 cellpadding=0 class="border">
				
				<tr valign="top" >
					<td>
						<table width="100%" border=0 cellspacing=1 cellpadding=4>
                   
            <?php print_r($this); ?>            
					
						<tr valign="middle" class="bg1">
							<td nowrap width="45%" class="fNormal">Type:</td>
							<td width="55%">
								<?php
									echo getSelect("task",
										array(
											"tools_editdetails_player"=>"Player",
											"tools_editdetails_clan"=>"Clan"
										)
									);
								?></td>
						</tr>
						
						<tr valign="middle" class="bg1">
							<td nowrap width="45%" class="fNormal">ID Number:</td>
							<td width="55%"><input type="text" name="id" size=15 maxlength=12 class="textbox"></td>
						</tr>
						
						</table></td>
					<td align="right">
						<table border=0 cellspacing=0 cellpadding=10>
						<tr>
							<td><input type="submit" value=" Edit &gt;&gt; " class="submit"></td>
						</tr>
						</table></td>
				</tr>
				
				</table></td>
		</tr>
		
		</table>
		
		</form></td>
</tr>

</table><p>

<?php
	require(PAGE_PATH . "/search-class.php");
	
	$sr_query = $_GET["q"];
    $search_pattern  = array("/script/i", "/;/", "/%/");
    $replace_pattern = array("", "", "");
    $sr_query = preg_replace($search_pattern, $replace_pattern, $sr_query);

	$sr_type  = valid_request($_GET["st"], 0) or "player";
	$sr_game  = valid_request($_GET["game"], 0);
	
	$search = new Search($sr_query, $sr_type, $sr_game);
	
	$search->drawForm(array(
		"mode"=>"admin",
		"task"=>$selTask
	));
	
	if ($sr_query)
	{
		$search->drawResults(
			"mode=admin&task=tools_editdetails_player&id=%k",
			"mode=admin&task=tools_editdetails_clan&id=%k"
		);
	}
?>