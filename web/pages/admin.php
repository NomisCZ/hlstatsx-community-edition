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

if (!defined('IN_HLSTATS'))
{
	die('Do not access this file directly.');
}

if ( empty($game) )
{
	$resultGames = $db->query("
        SELECT
            code,
            name
        FROM
            hlstats_Games
        WHERE
            hidden='0'
        ORDER BY
            name ASC 
        LIMIT 0,1

	");
	list($game) = $db->fetch_row($resultGames);
}

class Auth
{
	var $ok = false;
	var $error = false;

	var $username, $password, $savepass;
	var $sessionStart, $session;

	var $userdata = array();

	function Auth()
	{
		//@session_start();

		if (valid_request($_POST['authusername'], 0))
		{
			$this->username = valid_request($_POST['authusername'], 0);
			$this->password = valid_request($_POST['authpassword'], 0);
			$this->savepass = valid_request($_POST['authsavepass'], 0);
			$this->sessionStart = 0;

	
			# clear POST vars so as not to confuse the receiving page
			$_POST = array();

			$this->session = false;

			if($this->checkPass()==true)
			{
				// if we have success, save it in this users SESSION
				$_SESSION['username']=$this->username;
				$_SESSION['password']=$this->password;
				$_SESSION['authsessionStart']=time();
				$_SESSION['acclevel'] = $this->userdata['acclevel'];
			}
		}
		elseif (isset($_SESSION['loggedin']))
		{
			$this->username = $_SESSION['username'];
			$this->password = $_SESSION['password'];
			$this->savepass = 0;
			$this->sessionStart = $_SESSION['authsessionStart'];
			$this->ok = true;
			$this->error = false;
			$this->session = true;
			
			if(!$this->checkPass())
			{
				unset($_SESSION['loggedin']);
			}
		}
		else
		{
			$this->ok = false;
			$this->error = false;

			$this->session = false;

			$this->printAuth();
		}
	}

	function checkPass()
	{
		global $db;

		$db->query("
				SELECT
					*
				FROM
					hlstats_Users
				WHERE
					username='$this->username'
				LIMIT 1
			");

		if ($db->num_rows() == 1)
		{
			// The username is OK

			$this->userdata = $db->fetch_array();
			$db->free_result();

			if (md5($this->password) == $this->userdata["password"])
			{
				// The username and the password are OK

				$this->ok = true;
				$this->error = false;
				$_SESSION['loggedin']=1;
				if ($this->sessionStart > (time() - 3600))
				{
					// Valid session, update session time & display the page
					$this->doCookies();
					return true;
				}
				elseif ($this->sessionStart)
				{
					// A session exists but has expired
					if ($this->savepass)
					{
						// They selected 'Save my password' so we just
						// generate a new session and show the page.
						$this->doCookies();
						return true;
					}
					else
					{
						$this->ok = false;
						$this->error = 'Your session has expired. Please try again.';
						$this->password = '';

						$this->printAuth();
						return false;
					}
				}
				elseif (!$this->session)
				{
					// No session and no cookies, but the user/pass was
					// POSTed, so we generate cookies.
					$this->doCookies();
					return true;
				}
				else
				{
					// No session, user/pass from a cookie, so we force auth
					$this->printAuth();
					return false;
				}
			}
			else
			{
				// The username is OK but the password is wrong

				$this->ok = false;
				if ($this->session)
				{
					// Cookie without 'Save my password' - not an error
					$this->error = false;
				}
				else
				{
					$this->error = 'The password you supplied is incorrect.';
				}
				$this->password = '';
				$this->printAuth();
			}
		}
		else
		{
			// The username is wrong
			$this->ok = false;
			$this->error = 'The username you supplied is not valid.';
			$this->printAuth();
		}
	}

	function doCookies()
	{
		return;
		setcookie('authusername', $this->username, time() + 31536000, '', '', 0);

		if ($this->savepass)
		{
			setcookie('authpassword', $this->password, time() + 31536000, '', '', 0);
		}
		else
		{
			setcookie('authpassword', $this->password, 0, '', '', 0);
		}
		setcookie('authsavepass', $this->savepass, time() + 31536000, '', '', 0);
		setcookie('authsessionStart', time(), 0, '', '', 0);
	}

	function printAuth()
	{
		global $g_options;

		include (PAGE_PATH . '/adminauth.php');
	}
}


class AdminTask
{
	var $title = '';
	var $acclevel = 0;
	var $type = '';
	var $description = '';

	function AdminTask($title, $acclevel, $type = 'general', $description = '', $group = '')
	{
		$this->title = $title;
		$this->acclevel = $acclevel;
		$this->type = $type;
		$this->description = $description;
		$this->group = $group;
	}
}


class EditList
{
	var $columns;
	var $keycol;
	var $table;
	var $deleteCallback;
	var $icon;
	var $showid;
	var $drawDetailsLink;
	var $DetailsLink;

	var $errors;
	var $newerror;

	var $helpTexts;
	var $helpKey;
	var $helpDIV;

	function EditList($keycol, $table, $icon, $showid = true, $drawDetailsLink = false, $DetailsLink = '', $deleteCallback = null)
	{
		$this->keycol = $keycol;
		$this->table = $table;
		$this->icon = $icon;
		$this->showid = $showid;
		$this->drawDetailsLink = $drawDetailsLink;
		$this->DetailsLink = $DetailsLink;
		$this->helpKey = '';
		$this->deleteCallback = $deleteCallback;
	}

	function setHelp($div, $key, $texts)
	{
		$this->helpDIV = $div;
		$this->helpKey = $key;
		$this->helpTexts = $texts;

		$returnstr = '';

		if ($this->helpKey != '')
		{
			$returnstr .= "<script type='text/javascript'>\n";
			$returnstr .= "var texts = new Array();\n";
			foreach (array_keys($this->helpTexts) as $key)
			{
				$value = $this->helpTexts[$key];
				// $value = nl2br(htmlspecialchars($this->helpTexts[$key]));
				$value = str_replace('"', "'", $value);
				//$value = preg_replace("/\"/", "'", $value);
				$value = preg_replace("/[\r\n]/", " ", $value);
				$returnstr .= "texts[\"" . $key . "\"] = \"" . $value . "\";\n";
			}

			$returnstr .= "\n\nfunction showHelp (keyname) {\n";
			$returnstr .= "document.getElementById('" . $this->helpDIV . "').innerHTML=texts[keyname];\n";
			$returnstr .= "document.getElementById('" . $this->helpDIV . "').style.visibility='visible';\n";
			$returnstr .= "}\n";
			$returnstr .= "\n\nfunction hideHelp () {\n";
			$returnstr .= "document.getElementById('" . $this->helpDIV . "').style.visibility='hidden';\n";
			$returnstr .= "}\n";
			$returnstr .= "</script>\n";


			$returnstr .= '<div class="helpwindow" ID="' . $this->helpDIV . '">No help text available</div>';

		}
		return $returnstr;
	}

	function update()
	{
		global $db;

		$okcols = 0;
		foreach ($this->columns as $col)
		{
			$value = mystripslashes($_POST["new_$col->name"]);
			//  legacy code that should have never been here. these should never be html-escaped in the db.
			//  if there's a problem with removing this, it needs to be fixed on the web/display end
			//  -psychonic
			//
			/*
			if ( $col->name != 'rcon_password' && $col->type != 'password' && $col->name != 'pattern')
			{
				$value = htmlspecialchars($value);
			}
			*/

			if ($value != '')
			{
				if ($col->type == 'ipaddress' && !preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $value))
				{
					$this->errors[] = "Column '$col->title' requires a valid IP address for new row";
					$this->newerror = true;
					$okcols++;
				}
				else
				{
					if ($qcols)
					{
						$qcols .= ', ';
					}
					$qcols .= $col->name;

					if ($qvals)
					{
						$qvals .= ', ';
					}

					if ($col->type == 'password' && $col->name != 'rcon_password')
					{
						$value = md5($value);
					}
					$qvals .= "'" . $db->escape($value) . "'";

					if ($col->type != 'select' && $col->type != 'hidden' && $value != $col->datasource)
					{
						$okcols++;
					}
				}
			}
			elseif ($col->required)
			{
				$this->errors[] = "Required column '$col->title' must have a value for new row";
				$this->newerror = true;
			}
		}

		if ($okcols > 0 && !$this->errors)
		{
			$db->query("
					INSERT INTO
						$this->table
						(
							$qcols
						)
					VALUES
					(
						$qvals
					)");
		}
		elseif ($okcols == 0)
		{
			$this->errors = array();
			$this->newerror = false;
		}

		if (!is_array($_POST['rows']))
		{
			return true;
		}
		
		foreach ($_POST['rows'] as $row)
		{
			if ($_POST[$row . '_delete'])
			{
				if ( !empty($this->deleteCallback) && is_callable($this->deleteCallback) )
				{
					call_user_func($this->deleteCallback, $row);
				}
				$db->query("
					DELETE FROM
						$this->table
					WHERE
						$this->keycol='" . $db->escape($row) . "'
				");
			}
			else
			{
				$rowerror = false;

				$query = "UPDATE $this->table SET ";
				$i = 0;
				foreach ($this->columns as $col)
				{
					if ($col->type == 'readonly')
					{
						continue;
					}

					$value = mystripslashes($_POST[$row . "_" . $col->name]);
					
					//  legacy code that should have never been here. these should never be html-escaped in the db.
					//  if there's a problem with removing this, it needs to be fixed on the web/display end
					//  -psychonic
					//
					/*
					if ( $col->name != 'rcon_password' && $col->type != 'password' && $col->name != 'pattern')
					{
						$value = htmlspecialchars($value);
					}
					*/

					if ($col->type == 'checkbox' && $value == ('' || null))
					{
						$value = '0';
					}

					if ($col->type == 'password' && $value == '(encrypted)')
					{
						continue;
					}

					if ($value == '' && $col->required)
					{
						$this->errors[] = "Required column '$col->title' must have a value for row '$row'";
						$rowerror = true;
					}
					elseif ($col->type == "ipaddress" && !preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $value))
					{
						$this->errors[] = "Column '$col->title' requires a valid IP address for row '$row'";
						$rowerror = true;
					}

					if ($i > 0)
					{
						$query .= ', ';
					}

					if ($col->type == 'password' && $col->name != 'rcon_password')
					{
						$query .= $col->name . "='" . md5($value) . "'";
					}
					else
					{
						$query .= $col->name . "='" . $db->escape($value) . "'";
					}
					$i++;
				}
				$query .= " WHERE $this->keycol='" . $db->escape($row) . "'";

				if (!$rowerror)
				{
					$db->query($query);
				}
			}
		}

		if ($this->error())
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	function draw($result, $draw_new = true)
	{
		global $g_options, $db;


?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">

<tr valign="top" class="table_border">
	<td><table width="100%" border="0" cellspacing="1" cellpadding="4">

		<tr valign="bottom" class="head">
<?php
		echo '<td></td>';

		if ($this->showid)
		{
?>
			<td align="right" class="fSmall"><?php
			echo 'ID';
?></td>
<?php
		}

		foreach ($this->columns as $col)
		{
			if ($col->type == 'hidden')
			{
				continue;
			}
			echo '<td class="fSmall">' . $col->title . "</td>\n";
		}

		if ($this->drawDetailsLink)
		{
?>
			<td align="right" class="fSmall"><?php
			echo '';
?></td>
<?php
		}


?>
			<td align="center" class="fSmall"><?php
		echo 'Delete';
?></td>
		</tr>

<?php
		while ($rowdata = $db->fetch_array($result))
		{
			echo "\n<tr>\n";
			echo '<td align="center" class="bg1">';
			if  (file_exists(IMAGE_PATH . "/$this->icon.gif"))
			{
				echo '<img src="' . IMAGE_PATH . "/$this->icon.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" />";
			} 
			else 
			{
				echo '<img src="' . IMAGE_PATH . "/server.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" />";
			}
			echo "</td>\n";
			
			if ($this->showid)
			{
				echo '<td align="right" class="bg2 fSmall">' . $rowdata[$this->keycol] . "</td>\n";
			}

			$this->drawfields($rowdata, false, false);

			if ($this->drawDetailsLink)
			{
				global $gamecode;
?>
			<td align="center" class="bg2 fSmall"><?php
				echo "<a href='" . $g_options["scripturl"] . "?mode=admin&amp;game=$gamecode&amp;task=" . $this->DetailsLink . "&amp;key=" . $rowdata[$this->keycol] . "'><b>CONFIGURE</b></a>";
?></td>
<?php
			}

?>
<td align="center" class="bg2"><input type="checkbox" name="<?php echo $rowdata[$this->keycol]; ?>_delete" value="1" /></td>
<?php echo "</tr>\n\n";
		}
?>

<tr>
<?php
		if ( $draw_new )
		{
			echo "<td class=\"bg1 fSmall\" align=\"center\">" . "new</td>\n";

			if ($this->showid)
				echo "<td class=\"bg2 fSmall\" align=\"right\">" . "&nbsp;</td>\n";
	
			if ($this->newerror)
			{
				$this->drawfields($_POST, true, true);
			}
			else
			{
				$this->drawfields(array(), true);
			}

			echo "<td class=\"bg1\"></td>\n";
		}
?>
</tr>

		</table></td>
</tr>

</table><br /><br />
<?php
	}


	function drawfields($rowdata = array(), $new = false, $stripslashes = false)
	{
		global $g_options, $db;

		$i = 0;
		foreach ($this->columns as $col)
		{
			if ($new)
			{
				$keyval = 'new';
				$rowdata[$col->name] = $rowdata["new_$col->name"];
				if ($stripslashes)
					$rowdata[$col->name] = mystripslashes($rowdata[$col->name]);
			}
			else
			{
				$keyval = $rowdata[$this->keycol];
				if ($stripslashes)
					$keyval = mystripslashes($keyval);

			}

			if ($col->type != 'hidden')
			{
				echo '<td class="bg1">';
			}

			if ($i == 0 && !$new)
			{
				echo '<input type="hidden" name="rows[]" value="' . htmlspecialchars($keyval) . '" />';
			}

			if ($col->maxlength < 1)
			{
				$col->maxlength = '';
			}

			switch ($col->type)
			{
				case 'select':
					unset($coldata);

					// for manual datasource in format "key/value;key/value" or "key;key"
					foreach (explode(';', $col->datasource) as $v)
					{
						$sections = preg_match_all('/\//', $v, $dsaljfdsaf);
						if ($sections == 2)
						{
							// for SQL datasource in format "table.column/keycolumn/where"
							list($col_table, $col_col) = explode('.', $v);
							list($col_col, $col_key, $col_where) = explode('/', $col_col);
							if ($col_where)
							{
								$col_where = "WHERE $col_where";
							}
							$col_result = $db->query("SELECT $col_key, $col_col FROM $col_table $col_where ORDER BY $col_col");
							$coldata = array();
							while (list($a, $b) = $db->fetch_row($col_result))
							{
								$coldata[$a] = $b;
							}
						}
						else if ($sections > 0)
						{
							list($a, $b) = explode('/', $v);
							$coldata[$a] = $b;
						}
						else
						{
							$coldata[$v] = $v;
						}
					}

					if ($col->width)
					{
						$width = ' style="width:' . $col->width * 5 . 'px"';
					}
					else
					{
						$width = '';
					}

					echo "<select name=\"" . $keyval . "_$col->name\"$width>\n";

					if (!$col->required)
					{
						echo "<option value=\"\"></option>\n";
					}

					$gotcval = false;

					foreach ($coldata as $k => $v)
					{
						if ($rowdata[$col->name] == $k)
						{
							$selected = ' selected="selected"';
							$gotcval = true;
						}
						else
						{
							$selected = '';
						}

						echo "<option value=\"$k\"$selected>$v</option>\n";
					}

					if (!$gotcval)
					{
						echo '<option value="' . $rowdata[$col->name] . '" selected="selected">' . $rowdata[$col->name] . "</option>\n";
					}

					echo '</select>';
					break;

				case 'checkbox':
					$selectedval = '1';
					$value = $rowdata[$col->name];

					if ($value == $selectedval)
					{
						$selected = ' checked="checked"';
					}
					else
					{
						$selected = '';
					}

					echo '<center><input type="checkbox" name="' . $keyval . "_$col->name\" value=\"$selectedval\"$selected /></center>";
					break;
					
				case 'hidden':
					echo '<input type="hidden" name="' . $keyval . "_$col->name\" value=\"" . htmlspecialchars($col->datasource) . '" />';
					break;
					
				case 'readonly':
					if (!$new)
					{
						echo html_entity_decode($rowdata[$col->name]);
						break;
					}
					/* else fall through to default */

				default:
					if ($col->type == 'password')
					{
						$onclick = " onclick=\"if (this.value == '(encrypted)') this.value='';\"";
					}
					if ($col->datasource != '' && !isset($rowdata[$col->name]))
					{
						$value = $col->datasource;
					}
					else
					{
						$value = $rowdata[$col->name];
					}

					$onClick = '';
					if ($this->helpKey != '')
					{
						$onClick = "onmouseover=\"javascript:showHelp('" . strtolower($rowdata[$this->helpKey]) . "')\" onmouseout=\"javascript:hideHelp()\"";
					}

					echo "<input $onClick type=\"text\" name=\"" . $keyval . "_$col->name\" size=$col->width " . "value=\"" . htmlentities(html_entity_decode($value), ENT_COMPAT, 'UTF-8') . "\" class=\"textbox\"" . " maxlength=\"$col->maxlength\"$onclick />";
// doing htmlentities on something that we just decoded is because we need to encode them when we fill out a form, but we don't want to double encode them (some items like rcon are not encoded at all - but server names are)
			}

			if ($col->type != 'hidden')
			{
				echo "</td>\n";
			}

			$i++;
		}
	}

	function error()
	{
		if (is_array($this->errors))
		{
			return implode("<br /><br />\n\n", $this->errors);
		}
		else
		{
			return false;
		}
	}
}

class EditListColumn
{
	var $name;
	var $title;
	var $width;
	var $required;
	var $type;
	var $datasource;
	var $maxlength;

	function EditListColumn($name, $title, $width = 20, $required = false, $type = 'text', $datasource = '', $maxlength = 0)
	{
		$this->name = $name;
		$this->title = $title;
		$this->width = $width;
		$this->required = $required;
		$this->type = $type;
		$this->datasource = $datasource;
		$this->maxlength = intval($maxlength);
	}
}


class PropertyPage
{
	var $table;
	var $keycol;
	var $keyval;
	var $propertygroups = array();

	function PropertyPage($table, $keycol, $keyval, $groups)
	{
		$this->table = $table;
		$this->keycol = $keycol;
		$this->keyval = $keyval;
		$this->propertygroups = $groups;
	}

	function draw($data)
	{
		foreach ($this->propertygroups as $group)
		{
			$group->draw($data);
		}
	}

	function update()
	{
		global $db;

		$setstrings = array();
		foreach ($this->propertygroups as $group)
		{
			foreach ($group->properties as $prop)
			{
				if ($prop->name == 'name')
				{
					$value = $_POST[$prop->name];
					$search_pattern = array('/script/i', '/;/', '/%/');
					$replace_pattern = array('', '', '');
					$value = preg_replace($search_pattern, $replace_pattern, $value);
					$setstrings[] = $prop->name . "='" . $value . "'";
				}
				else
				{
					$setstrings[] = $prop->name . "='" . valid_request($_POST[$prop->name], 0) . "'";
				}
			}
		}

		$db->query("
				UPDATE
					" . $this->table . "
				SET
					" . implode(",\n", $setstrings) . "
				WHERE
					" . $this->keycol . "='" . mysql_real_escape_string($this->keyval) . "'
			");
	}
}

class PropertyPage_Group
{
	var $title = '';
	var $properties = array();

	function PropertyPage_Group($title, $properties)
	{
		$this->title = $title;
		$this->properties = $properties;
	}

	function draw($data)
	{
		global $g_options;
?>
<b><?php echo $this->title; ?></b><br />
<table width="100%" border="0" cellspacing="0" cellpadding="0">

<tr valign="top">
	<td><table width="100%" border="0" cellspacing="1" cellpadding="4">
<?php
		foreach ($this->properties as $prop)
		{
			$prop->draw($data[$prop->name]);
		}
?>
		</table></td>
</tr>

</table><br /><br />
<?php
	}
}

class PropertyPage_Property
{
	var $name;
	var $title;
	var $type;

	function PropertyPage_Property($name, $title, $type, $datasource = '')
	{
		$this->name = $name;
		$this->title = $title;
		$this->type = $type;
		$this->datasource = $datasource;
	}

	function draw($value)
	{
		global $g_options;
?>
<tr style="vertical-align:middle;">
	<td class="bg1" style="width:45%;"><?php
		echo $this->title . ':';
?></td>
	<td class="bg1" style="width:55%;"><?php
		switch ($this->type)
		{
			case 'textarea':
				echo "<textarea name=\"$this->name\" cols=35 rows=4 wrap=\"virtual\">" . htmlspecialchars($value) . '</textarea>';
				break;

			case 'select':
				// for manual datasource in format "key/value;key/value" or "key;key"
				foreach (explode(';', $this->datasource) as $v)
				{
					if (preg_match('/\//', $v))
					{
						list($a, $b) = explode('/', $v);
						$coldata[$a] = $b;
					}
					else
					{
						$coldata[$v] = $v;
					}
				}

				echo getSelect($this->name, $coldata, $value);
				break;

			default:
				echo "<input type=\"text\" name=\"$this->name\" size=35 value=\"" . htmlspecialchars($value) . "\" class=\"textbox\" />";
				break;
		}
?>
</td>
</tr>
<?php
	}
}

function message($icon, $msg)
{
	global $g_options;
?>
		<table width="60%" border="0" cellspacing="0" cellpadding="0">

		<tr valign="top">
			<td width="40"><img src="<?php echo IMAGE_PATH . "/$icon"; ?>.gif" width="16" height="16" border="0" hspace="5" alt="" /></td>
			<td width="100%"><?php
	echo "<b>$msg</b>";
?></td>
		</tr>

		</table><br /><br />
<?php
}


$auth = new Auth;
if($auth->ok===false)
{
	return;
}

pageHeader(array('Admin'), array('Admin' => ''));

$selTask = valid_request($_GET['task'], 0);
$selGame = valid_request($_GET['game'], 0);
?>

<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">

<tr valign="top">
	<td><?php

// General Settings
$admintasks['options'] = new AdminTask('HLstatsX:CE Settings', 80);
$admintasks['adminusers'] = new AdminTask('Admin Users', 100);
$admintasks['games'] = new AdminTask('Games', 80);
$admintasks['hostgroups'] = new AdminTask('Host Groups', 100);
$admintasks['clantags'] = new AdminTask('Clan Tag Patterns', 80);
$admintasks['voicecomm'] = new AdminTask('Manage Voice Servers', 80);

// Game Settings
$admintasks['newserver'] = new AdminTask('Add Server', 80, 'game');
$admintasks['servers'] = new AdminTask('Edit Servers', 80, 'game');
$admintasks['serversettings'] = new AdminTask('&nbsp;&nbsp;&nbsp;&gt;&gt;&nbsp;Server Details', 80, 'game');
$admintasks['actions'] = new AdminTask('Actions', 80, 'game');
$admintasks['teams'] = new AdminTask('Teams', 80, 'game');
$admintasks['roles'] = new AdminTask('Roles', 80, 'game');
$admintasks['weapons'] = new AdminTask('Weapons', 80, 'game');
$admintasks['awards_weapons'] = new AdminTask('Weapon Awards', 80, 'game');
$admintasks['awards_plyractions'] = new AdminTask('Plyr Action Awards', 80, 'game');
$admintasks['awards_plyrplyractions'] = new AdminTask('PlyrPlyr Action Awards', 80, 'game');
$admintasks['awards_plyrplyractions_victim'] = new AdminTask('PlyrPlyr Action Awards (Victim)', 80, 'game');
$admintasks['ranks'] = new AdminTask('Ranks (triggered by Kills)', 80, 'game');
$admintasks['ribbons'] = new AdminTask('Ribbons (triggered by Awards)', 80, 'game');

// Tools
$admintasks['tools_perlcontrol'] = new AdminTask('HLstatsX: CE Daemon Control', 80, 'tool', 'Reload or stop your HLX: CE Daemons');
$admintasks['tools_editdetails'] = new AdminTask('Edit Player or Clan Details', 80, 'tool', 'Edit a player or clan\'s profile information.');
$admintasks['tools_adminevents'] = new AdminTask('Admin-Event History', 80, 'tool', 'View event history of logged Rcon commands and Admin Mod messages.');
$admintasks['tools_ipstats'] = new AdminTask('Host Statistics', 80, 'tool', 'See which ISPs your players are using.');
$admintasks['tools_optimize'] = new AdminTask('Optimize Database', 100, 'tool', 'This operation tells the MySQL server to clean up the database tables, optimizing them for better performance. It is recommended that you run this at least once a month.');
//$admintasks['tools_synchronize'] = new AdminTask('Synchronize Statistics', 80, 'tool', 'Sychronize all players with the offical global ELstatsNEO banlist with catched VAC cheaters.');
$admintasks['tools_resetdbcollations'] = new AdminTask('Reset All DB Collations to UTF8', 100, 'tool', 'Reset DB Collations to UTF-8 if you receive collation errors after an upgrade from another HLstats(X)-based system.');

// Sub-Tools
$admintasks['tools_editdetails_player'] = new AdminTask('Edit Player Details', 80, 'subtool', 'Edit a player\'s profile information.');
$admintasks['tools_editdetails_clan'] = new AdminTask('Edit Clan Details', 80, 'subtool', 'Edit a clan\'s profile information.');

// Reset Tools
$admintasks['tools_reset'] = new AdminTask('Full or Partial Reset', 100, 'tool', 'Resets chosen data globally or for selected game', 'reset');
$admintasks['tools_reset_2'] = new AdminTask('Clean up Statistics', 100, 'tool', 'Delete all inactive players, clans and corresponding events from the database.', 'reset');

// Game Settings Tools
$admintasks['tools_settings_copy'] = new AdminTask('Duplicate Game settings', 80, 'tool', 'Duplicate a whole game settings tree to split servers of same gametype', 'settingstool');


// Show Tool
if ($admintasks[$selTask] && ($admintasks[$selTask]->type == 'tool' || $admintasks[$selTask]->type == 'subtool'))
{
	$task = $admintasks[$selTask];

	$code = $selTask;
?>
&nbsp;<img src="<?php echo IMAGE_PATH; ?>/downarrow.gif" width="9" height="6" alt="" /><b>&nbsp;<a href="<?php echo $g_options['scripturl']; ?>?mode=admin">Tools</a></b><br />
<img src="<?php echo IMAGE_PATH; ?>/spacer.gif" width="1" height="8" border="0" alt="" /><br />

<?php
	include (PAGE_PATH . "/admintasks/$code.php");
}
else
{
	// General Settings

?>
&nbsp;<img src="<?php echo IMAGE_PATH; ?>/downarrow.gif" width="9" height="6" alt="" /><b>&nbsp;General Settings</b><br /><br />
<?php
	foreach ($admintasks as $code => $task)
	{
		if ($auth->userdata['acclevel'] >= $task->acclevel && $task->type == 'general')
		{
			if ($selTask == $code)
			{
?>
&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo IMAGE_PATH; ?>/downarrow.gif" width="9" height="6" alt="" /><b>&nbsp;<a href="<?php echo $g_options['scripturl']; ?>?mode=admin" name="<?php echo $code; ?>"><?php echo $task->title; ?></a></b><br /><br />

<form method="post" action="<?php echo $g_options['scripturl']; ?>?mode=admin&amp;task=<?php echo $code; ?>#<?php echo $code; ?>">

<table width="100%" border="0" cellspacing="0" cellpadding="0">

<tr>
	<td width="2%">&nbsp;</td>
	<td width="98%"><?php
				include (PAGE_PATH . "/admintasks/$code.php");
?></td>
</tr>

</table><br /><br />
</form>
<?php
			}
			else
			{
?>
&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo IMAGE_PATH; ?>/rightarrow.gif" width="6" height="9" alt="" /><b>&nbsp;<a href="<?php echo $g_options['scripturl']; ?>?mode=admin&amp;task=<?php echo $code; ?>#<?php echo $code;
?>"><?php echo $task->title; ?></a></b><br /><br /> <?php
			}
		}
	}
?>
	
&nbsp;<img src="<?php echo IMAGE_PATH; ?>/downarrow.gif" width="9" height="6" alt="" /><b>&nbsp;Game Settings</b><br /><br />
<?php
	$gamesresult = $db->query("
			SELECT
				name,
				code
			FROM
				hlstats_Games
			WHERE
				hidden = '0'
			ORDER BY
				name ASC
			;
		");

	while ($gamedata = $db->fetch_array($gamesresult))
	{
		$gamename = $gamedata['name'];
		$gamecode = $gamedata['code'];

		if ($gamecode == $selGame)
		{
?>
&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo IMAGE_PATH; ?>/downarrow.gif" width="9" height="6" alt="" /><b>&nbsp;<a href="<?php echo $g_options['scripturl']; ?>?mode=admin" name="game_<?php echo $gamecode; ?>"><?php echo $gamename; ?></a></b> (<?php echo $gamecode; ?>)<br /><br /> <?php
			foreach ($admintasks as $code => $task)
			{
				if ($auth->userdata['acclevel'] >= $task->acclevel && $task->type == 'game')
				{
					if ($selTask == $code)
					{
?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo IMAGE_PATH; ?>/downarrow.gif" width="9" height="6" alt="" /><b>&nbsp;<a href="<?php echo $g_options['scripturl']; ?>?mode=admin&amp;game=<?php echo $gamecode; ?>" name="<?php echo $code; ?>"><?php echo $task->title; ?></a></b><br /><br />

<form method="post" name="<?php echo $code; ?>form" action="<?php echo $g_options['scripturl']; ?>?mode=admin&amp;game=<?php echo $gamecode; ?>&task=<?php echo $code; ?>#<?php echo $code; ?>">

<table width="100%" border="0" cellspacing="0" cellpadding="0">

<tr>
	<td width="10%">&nbsp;</td>
	<td width="90%"><?php
						include (PAGE_PATH . "/admintasks/$code.php");
?></td>
</tr>

</table><br /><br />
</form>
<?php
					}
					elseif ($code != 'serversettings')
					{
	?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo IMAGE_PATH; ?>/rightarrow.gif" width="6" height="9" alt="" /><b>&nbsp;<a href="<?php echo $g_options['scripturl']; ?>?mode=admin&amp;game=<?php echo $gamecode; ?>&task=<?php echo $code; ?>#<?php echo $code; ?>"><?php echo $task->title; ?></a></b><br /><br /> <?php
					}
				}
			}
		}
		else
		{
?>
&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo IMAGE_PATH; ?>/rightarrow.gif" width="6" height="9" alt="" /><b>&nbsp;<a href="<?php echo $g_options['scripturl']; ?>?mode=admin&amp;game=<?php echo $gamecode; ?>#game_<?php echo $gamecode; ?>"><?php echo $gamename; ?></a></b> (<?php echo $gamecode; ?>)<br /><br /> <?php
		}
	}
}
echo "</td>\n";

if (!$selTask || !$admintasks[$selTask])
{
	echo '<td width="50%">';
?>
&nbsp;<img src="<?php echo IMAGE_PATH; ?>/downarrow.gif" width="9" height="6" alt="" /><b>&nbsp;Tools</b>

<ul>
<?php
	foreach ($admintasks as $code => $task)
	{
		if ($auth->userdata['acclevel'] >= $task->acclevel && $task->type == 'tool')
		{
?>	<li><b><a href="<?php echo $g_options['scripturl']; ?>?mode=admin&amp;task=<?php echo $code; ?>"><?php echo $task->title; ?></a></b><br />
		<?php echo $task->description; ?><br /><br />
	</li>
<?php
		}
	}
?>
	<li><strong>Version Check</strong><br />
	<div id="updatecheck">Checking for update... <img src="<?php echo IMAGE_PATH."/../css/spinner.gif"; ?>" /></div>
	</li>
</ul>
<?php
	echo '</td>';
}
?>
</tr>

</table>

<?php
if (!$selTask || !$admintasks[$selTask])
{
?>
<script type="text/javascript">
/* <![CDATA[ */

	xmlhttp = false;
	currentver = '<?php echo $g_options['version']; ?>';
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function()
		{
			if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
				latestver = xmlhttp.responseText.replace(/\s+$/,"");
				if (latestver == currentver)
				{
					document.getElementById("updatecheck").innerHTML =
						'Your version is <span style="font-weight:bold">'+currentver+'</span><br />\n'
						+'You are up to date';
				}
				else
				{
					document.getElementById("updatecheck").innerHTML =
						'Your version is <span style="color:#C40000;font-weight:bold">' + currentver + '</span><br />\n'
						+ 'Current version is <span style="color:#007F0E;font-size:125%;font-weight:bold">' + latestver + '</span>. Updating is recommended.<br />\n'
						+ 'Please go to <a href="http://www.hlxce.com" target="_blank">hlxce.com</a> for releases and info.';
				}
			}
		}
	xmlhttp.open("GET","updatecheck_helper.php",true);
	xmlhttp.send();

/* ]]> */
</script>
<?php
}

if (isset($footerscript))
{
    echo $footerscript;
}
?>
