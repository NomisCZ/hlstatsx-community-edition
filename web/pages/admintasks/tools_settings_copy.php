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
		die('Do not access this file directly.');
	if ($auth->userdata['acclevel'] < 80)
		die ('Access denied!');
?>

&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo IMAGE_PATH; ?>/downarrow.gif" width=9 height=6 class="imageformat"><b>&nbsp;<?php echo $task->title; ?></b><p>

<?php

function check_writable() {

	$ok = '';
	$f = IMAGE_PATH."/games/";
	if (!is_writable($f)) 
		$ok .= "<li>I have no permission to write to '$f'";
	
	if ($ok != '') {
		echo 'FATAL:<br><UL>';
		echo $ok;
		echo '</UL><br>Correct this before continuing';
		die();
	}
	return true; 
}

function getTableFields($table,$auto_increment) {
   // get a field array of specified table
   global $db;

   $db->query("SHOW COLUMNS FROM $table;");
   $res = array();
   while ($r=$db->fetch_array())
   {  
      if ((!$auto_increment) && ($r['Extra']=='auto_increment'))
      {  
         continue;
      }
      else
      {  
         array_push($res,$r['Field']);
      }
   }
   return $res;
}

function copySettings($table,$game1,$game2) {
	global $db;
	
	$db->query("SELECT game FROM $table WHERE game='$game2' LIMIT 1;");
	if ($db->num_rows()!=0)
		$ret = 'Target gametype exists, nothing done!';
	else {
		$db->query("SELECT count(game) AS cnt FROM $table WHERE game='$game1';");
		$r = $db->fetch_array();
		if ($r['cnt']==0)
			$ret = 'No data existent for source gametype.';
		else {
			$ret = $r['cnt'].' entries copied!';
			$fields = '';
			$ignoreFields = array('game','id','d_winner_id','d_winner_count','g_winner_id','g_winner_count','count','picked','kills','deaths','headshots');
			foreach (getTableFields($table,0) AS $field) {
				if (!in_array($field, $ignoreFields)) {
					if ($fields!='')
						$fields .= ', ';
					$fields .= $field;
				}
			}
			$SQL = "INSERT INTO $table ($fields,game) SELECT $fields,'$game2' FROM $table WHERE game='$game1';";
			$db->query($SQL);
		}
	}  
	return $ret."</li>";
}

function mkdir_recursive($pathname) {
	is_dir(dirname($pathname)) || mkdir_recursive(dirname($pathname));
	return is_dir($pathname) || @mkdir($pathname);
}

function copyFile($source,$dest) {
	if ($source != '') {
		$source = IMAGE_PATH."/games/$source";
		$dest = IMAGE_PATH."/games/$dest";
		
		if (!is_file($source))
			$ret = "File not found $source (dest: $dest)<br>";
		else {
			mkdir_recursive(dirname($dest));
			if (!copy($source,$dest))
				$ret = 'FAILED';
			else
				$ret = 'OK';
		}
		return "Copying '$source' to '$dest': $ret</li>";
	}
	return '';
}

function scanCopyFiles($source,$dest) {
	global $files;
	$d = dir(IMAGE_PATH.'/games/'.$source);

	if ($d !== false) {
		while (($entry=$d->read()) !== false) {
			if (is_file(IMAGE_PATH.'/games/'.$source.'/'.$entry) && ($entry != '.') && ($entry != '..'))
				$files[] = array($source.'/'.$entry,$dest.'/'.$entry);
			if (is_dir(IMAGE_PATH.'/games/'.$source.'/'.$entry) && ($entry != '.') && ($entry != '..'))
				scanCopyFiles($source.'/'.$entry,$dest.'/'.$entry); 
		}
		$d->close();
	}
}

	if (isset($_POST['confirm'])) {
		$game1 = '';
		if (isset($_POST['game1']))
			if ($_POST['game1']!='')
				$game1 = $_POST['game1'];
		
		$game2 = '';
		if (isset($_POST['game2']))
			if ($_POST['game2']!='')
				$game2 = $_POST['game2'];
		
		$game2name = '';
		if (isset($_POST['game2name']))
			if ($_POST['game2name']!='')
				$game2name = $_POST['game2name'];
		
		echo '<ul><br />';
		check_writable();
		$game2 = valid_request($game2, 0);
		$game2name = valid_request($game2name, 0);
		echo '<li>hlstats_Games ...';
		$db->query("SELECT code FROM hlstats_Games WHERE code='$game2' LIMIT 1;");
		if ($db->num_rows()!=0) {
			echo '</ul><br /><br /><br />';
			echo '<b>Target gametype exists, nothing done!</b><br /><br />';
		} else {
			$db->query("INSERT INTO hlstats_Games (code,name,hidden,realgame) SELECT '$game2', '$game2name', '0', realgame FROM hlstats_Games WHERE code='$game1'");
			echo 'OK</li>';
			
			$dbtables = array();
			array_push($dbtables,
				'hlstats_Actions',
				'hlstats_Awards',
				'hlstats_Ribbons',
				'hlstats_Ranks',
				'hlstats_Roles',
				'hlstats_Teams',
				'hlstats_Weapons'
				);

			foreach ($dbtables as $dbt) {
				echo "<li>$dbt ... ";
				echo copySettings($dbt,$game1,$game2);
			}

			echo '</ul><br /><br /><br />';	
			echo '<ul>';
				
			$files = array(
				array(
				'',
				''
				)
			);

			scanCopyFiles("$game1/","$game2/");

			foreach ($files as $f) {
				echo '<li>';
				echo copyFile($f[0],$f[1]);
			}
			echo '</ul><br /><br /><br />';
			echo 'Done.<br /><br />';
		}
	} else {
		$result = $db->query("SELECT code, name FROM hlstats_Games ORDER BY code;");
		unset($games);
		$games[] = '<option value="" selected="selected">Please select</option>';
		while ($rowdata = $db->fetch_row($result))
		{
			$games[] = "<option value=\"$rowdata[0]\">$rowdata[0] - $rowdata[1]</option>";
		}

?>

<form method="post">
<table width="60%" align="center" border="0" cellspacing="0" cellpadding="0" class="border">

<tr>
	<td>
		<table width="100%" border="0" cellspacing="1" cellpadding="10">
		
		<tr class="bg1">
			<td class="fNormal" style="text-align:center;">

Are you sure to copy all settings from the selected gametype to the new gametype name?<br>
All existing images will be copied also to the new gametype!<p>

<input type="hidden" name="confirm" value="1" />
 Existing gametype: 
 <select Name="game1">
 <?php foreach ($games as $g) echo $g; ?>
 </select><br />
 New gametype code: 
 <input type="text" size="10" value="newcode" name="game2"><br />
 New gametype name: 
 <input type="text" size="26" value="New Game" name="game2name"><br />
 <input type="submit" value="  Copy selected gametype to the new name " />
</td>
		</tr>
		</table></td>
</tr>

</table>
</form>
<?php
	}
?>