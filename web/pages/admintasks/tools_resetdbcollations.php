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

    if (isset($_POST['confirm']))
    {
		$convert_to   = 'utf8_general_ci';
		$character_set= 'utf8';

		if ($_POST['printonly'] > 0) {
			echo '<strong>Run these statements against your MySql database</strong><br><br>';
			echo "ALTER DATABASE `".DB_NAME."` DEFAULT CHARACTER SET $character_set COLLATE $convert_to;<br>";
			$rs_tables = $db->query('SHOW TABLES') or die(mysql_error());
			while ($row_tables = $db->fetch_row($rs_tables))
			{
				$table = mysql_real_escape_string($row_tables[0]);
				echo "ALTER TABLE `$table` DEFAULT CHARACTER SET $character_set;<br>";
				$rs = $db->query("SHOW FULL FIELDS FROM `$table` WHERE collation is not null AND collation <> 'utf8_general_ci'") or die(mysql_error());
				while ($row=mysql_fetch_assoc($rs))
				{
					if ($row['Collation'] == '')
						continue;
					if ( strtolower($row['Null']) == 'yes' )
						$nullable = ' NULL ';
					else
						$nullable = ' NOT NULL';
					if ( $row['Default'] === NULL && $nullable = ' NOT NULL ')
						$default = " DEFAULT ''";
					else if ( $row['Default'] === NULL )
						$default = ' DEFAULT NULL';
					else if ($row['Default']!='')
						$default = " DEFAULT '".mysql_real_escape_string($row['Default'])."'";
					else
						$default = '';
					
					$field = mysql_real_escape_string($row['Field']);
					echo "ALTER TABLE `$table` CHANGE `$field` `$field` $row[Type] CHARACTER SET $character_set COLLATE $convert_to $nullable $default;<br>";
				}
			}
		} else {
			echo "Converting database, table, and row collations to utf8:<ul>\n";
			set_time_limit(0);	
			echo '<li>Changing '.DB_NAME.' default character set and collation... ';
			$db->query("ALTER DATABASE `".DB_NAME."` DEFAULT CHARACTER SET $character_set COLLATE $convert_to;")or die(mysql_error());
			echo 'OK';
			$rs_tables = $db->query('SHOW TABLES') or die(mysql_error());
			while ($row_tables = $db->fetch_row($rs_tables))
			{
				$table = mysql_real_escape_string($row_tables[0]);
				echo "<li>Converting Table: $table ... ";
				$db->query("ALTER TABLE `$table` DEFAULT CHARACTER SET $character_set;");
				echo 'OK';
				$rs = $db->query("SHOW FULL FIELDS FROM `$table` WHERE collation is not null AND collation <> 'utf8_general_ci'") or die(mysql_error());
				while ($row=mysql_fetch_assoc($rs))
				{
					if ($row['Collation'] == '')
						continue;
					if ( strtolower($row['Null']) == 'yes' )
						$nullable = ' NULL ';
					else
						$nullable = ' NOT NULL';
					if ( $row['Default'] === NULL && $nullable = ' NOT NULL ')
						$default = " DEFAULT ''";
					else if ( $row['Default'] === NULL )
						$default = ' DEFAULT NULL';
					else if ($row['Default']!='')
						$default = " DEFAULT '".mysql_real_escape_string($row['Default'])."'";
					else
						$default = '';
					
					$field = mysql_real_escape_string($row['Field']);
					echo "<li>Converting Table: $table   Column: $field ... ";
					$db->query("ALTER TABLE `$table` CHANGE `$field` `$field` $row[Type] CHARACTER SET $character_set COLLATE $convert_to $nullable $default;");
					echo 'OK';
				}
			}
			echo '</ul>';
			
			echo 'Done.<p>';
		}
		
    } else {
        
?>        

<form method="POST">
<table width="60%" align="center" border=0 cellspacing=0 cellpadding=0 class="border">

<tr>
    <td>
        <table width="100%" border=0 cellspacing=1 cellpadding=10>
        
        <tr class="bg1">
            <td class="fNormal">

Resets DB Collations if you get collation errors after an upgrade from another HLstats(X)-based system. <br><br>
You should not lose any data, but be sure to back up your database before running to be on the safe side.<br><br><br>



<input type="hidden" name="confirm" value="1">
<input type="radio" name="printonly" value="0" checked> Run the commands on the database<br>
<input type="radio" name="printonly" value="1"> Print the commands and I'll run them myself (recommended if you have a very large database likely to hang the script)<br>
<center><input type="submit" value="Generate commands and do the above"></center>
</td>
        </tr>
        
        </table></td>
</tr>

</table>
</form>

<?php
    }
?>    
    