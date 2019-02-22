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
	
	// Awards Info Page
	
	$db->query("SELECT name FROM hlstats_Games WHERE code='$game'");
	if ($db->num_rows() < 1) error("No such game '$game'.");
	
	list($gamename) = $db->fetch_row();
	$db->free_result();
	
	$type = valid_request($_GET['type']);
	$tab = valid_request($_GET['tab']);
	
	if ($type == 'ajax' )
	{
		$tabs = explode('|', preg_replace('[^a-z]', '', $tab));
		
		foreach ( $tabs as $tab )
		{
			if ( file_exists(PAGE_PATH . '/awards_' . $tab . '.php') )
			{
				@include(PAGE_PATH . '/awards_' . $tab . '.php');
			}
		}
		exit;
	}
	pageHeader(
		array($gamename, 'Awards Info'),
		array($gamename=>"%s?game=$game", 'Awards Info'=>'')
	);
?>

<?php if ($g_options['playerinfo_tabs']=='1') { ?>

<div id="main">
	<ul class="subsection_tabs" id="tabs_submenu">
		<li><a href="#" id="tab_daily">Daily&nbsp;Awards</a></li>
		<li><a href="#" id="tab_global">Global&nbsp;Awards</a></li>
		<li><a href="#" id="tab_ranks">Ranks</a></li>
		<li><a href="#" id="tab_ribbons">Ribbons</a></li>
	</ul>
<br />
<div id="main_content"></div>
<?php
if ($tab)
{
	$defaulttab = $tab;
}
else
{
	$defaulttab = 'daily';
}
echo "<script type=\"text/javascript\">
	new Tabs($('main_content'), $$('#main ul.subsection_tabs a'), {
		'mode': 'awards',
		'game': '$game',
		'loadingImage': '".IMAGE_PATH."/ajax.gif',
		'defaultTab': '$defaulttab'
	});"
?>
</script>

</div>


<?php } else {

	echo "\n<div id=\"daily\">\n";
	include PAGE_PATH.'/awards_daily.php';
	echo "\n</div>\n";

	echo "\n<div id=\"global\">\n";
	include PAGE_PATH.'/awards_global.php'; 
	echo "\n</div>\n";

	echo "\n<div id=\"ranks\">\n";
	include PAGE_PATH.'/awards_ranks.php';
	echo "\n</div>\n";

	echo "\n<div id=\"ribbons\">\n";
	include PAGE_PATH.'/awards_ribbons.php';
	echo "\n</div>\n";

}
?>