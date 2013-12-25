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
	
	
/* Profile support:

To see SQL run counts and run times, set the $profile variable below to something
that evaluates as true.

Add the following table to your database:
CREATE TABLE IF NOT EXISTS `hlstats_sql_web_profile` (
`queryid` int(11) NOT NULL AUTO_INCREMENT,
`source` tinytext NOT NULL,
`run_count` int(11) NOT NULL,
`run_time` float NOT NULL,
PRIMARY KEY (`queryid`),
UNIQUE KEY `source` (`source`(64))
) ENGINE=MyISAM;
*/

if ( !defined('IN_HLSTATS') ) { die('Do not access this file directly.'); }

class DB_mysql
{
	var $db_addr;
	var $db_user;
	var $db_pass;
	var $db_name;
	
	var $link;
	var $last_result;
	var $last_query;
	var $last_insert_id;
	var $profile = 0;
	var $querycount = 0;
	var $last_calc_rows = 0;
	
	function DB_mysql($db_addr, $db_user, $db_pass, $db_name, $use_pconnect = false)
	{
		$this->db_addr = $db_addr;
		$this->db_user = $db_user;
		$this->db_pass = $db_pass;
		
		$this->querycount = 0;
		
		if ( $use_pconnect )
		{
			$this->link = @mysql_pconnect($db_addr, $db_user, $db_pass);
		}
		else
		{
			$this->link = @mysql_connect($db_addr, $db_user, $db_pass);
		}
		
		if ( $this->link )
		{
			$q = @mysql_query("SET NAMES 'utf8'", $this->link);
			@mysql_free_result($q);
			if ( $db_name != '' )
			{
				$this->db_name = $db_name;
				if ( !@mysql_select_db($db_name, $this->link) )
				{
					@mysql_close($this->db_connect_id);
					$this->error("Could not select database '$db_name'. Check that the value of DB_NAME in config.php is set correctly.");
				}
			}
			return $this->link;
		}
		else
		{
			$this->error('Could not connect to database server. Check that the values of DB_ADDR, DB_USER and DB_PASS in config.php are set correctly.');
		}
	}
    
	function data_seek($row_number, $query_id = 0)
	{
		if ( !$query_id )
		{
			$result = $this->last_result;
		}
		if ( $query_id )
		{
			return @mysql_data_seek($query_id, $row_number);
		}
		return false;
	}
	
	function fetch_array($query_id = 0)
	{
		if ( !$query_id )
		{
			$query_id = $this->last_result;
		}
		if ( $query_id )
		{
			return @mysql_fetch_array($query_id);
		}
		return false;
	}
	
	function fetch_row($query_id = 0)
	{
		if ( !$query_id )
		{
			$query_id = $this->last_result;
		}
		if ( $query_id )
		{
			return @mysql_fetch_row($query_id);
		}
		return false;
	}
	
	function fetch_row_set($query_id = 0)
	{
		if ( !$query_id )
		{
			$query_id = $this->last_result;
		}
		if ( $query_id )
		{
			$rowset = array();
			while ( $row = $this->fetch_array($query_id) )
				$rowset[] = $row;
			
			return $rowset;
		}
		return false;
	}
	
	function free_result($query_id = 0)
	{
		if ( !$query_id )
		{
			$query_id = $this->last_result;
		}
		if ( $query_id )
		{
			return @mysql_free_result($query_id);
		}
		return false;
	}
	
	function insert_id()
	{
		return $this->last_insert_id;
	}
	
	function num_rows($query_id = 0)
	{
		if ( !$query_id )
		{
			$query_id = $this->last_result;
		}
		if ( $query_id )
		{
			return @mysql_num_rows($query_id);
		}
		return false;
	}
	
	function calc_rows() 
	{
		return $this->last_calc_rows;
	}
	
	function query($query, $showerror=true, $calcrows=false)
	{
		$this->last_query = $query;
		$starttime = microtime(true);
		if($calcrows == true) 
		{
			/* Add sql_calc_found_rows to this query */
			$query = preg_replace('/select/i', 'select sql_calc_found_rows', $query, 1);
		}
		$this->last_result = @mysql_query($query, $this->link);
		$endtime = microtime(true);
		
		$this->last_insert_id = @mysql_insert_id($this->link);
		
		if($calcrows == true) 
		{
			$calc_result = @mysql_query("select found_rows() as rowcount");
			if($row = mysql_fetch_assoc($calc_result)) 
			{
				$this->last_calc_rows = $row['rowcount'];
			}
		}
		
		$this->querycount++;
		
		if ( defined('DB_DEBUG') && DB_DEBUG == true )
		{
			echo "<p><pre>$query</pre><hr /></p>";
		}
		
		if ( $this->last_result )
		{
			if($this->profile) 
			{
				$backtrace = debug_backtrace();
				$profilequery = "insert into hlstats_sql_web_profile (source, run_count, run_time) values ".
					"('".basename($backtrace[0]['file']).':'.$backtrace[0]['line']."',1,'".($endtime-$starttime)."')"
					."ON DUPLICATE KEY UPDATE run_count = run_count+1, run_time=run_time+".($endtime-$starttime);
				@mysql_query($profilequery, $this->link);
			}
			return $this->last_result;
		}
		else
		{
			if ($showerror)
			{
				$this->error('Bad query.');
			}
			else
			{
				return false;
			}
		}
	}
	
	function result($row, $field, $query_id = 0)
	{
		if ( !$query_id )
		{
			$query_id = $this->last_result;
		}
		if ( $query_id )
		{
			return @mysql_result($query_id, $row, $field);
		}
		return false;
	}
	
	function escape($string)
	{
		if ( $this->link )
		{
			return @mysql_real_escape_string($string, $this->link);
		}
		else
		{
			return @mysql_real_escape_string($string);
		}
	}
	
	function error($message, $exit=true)
	{
		error(
			"<b>Database Error</b><br />\n<br />\n" .
			"<i>Server Address:</i> $this->db_addr<br />\n" .
			"<i>Server Username:</i> $this->db_user<br /><br />\n" .
			"<i>Error Diagnostic:</i><br />\n$message<br /><br />\n" .
			"<i>Server Error:</i> (" . @mysql_errno() . ") " . @mysql_error() . "<br /><br />\n" .
			"<i>Last SQL Query:</i><br />\n<pre style=\"font-size:2px;\">$this->last_query</pre>",
			$exit
		);
	}
}
?>
