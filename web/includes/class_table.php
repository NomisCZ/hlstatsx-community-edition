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


 * Table
 * Generates an HTML table.
 * 
 * @package HLstatsX Community Edition
 * @author Nicholas Hastings (nshastings@gmail.com)
 * @copyright HLstatsX Community Edition
 * @version 2008
 * @access public
 */
class Table
{
	var $columns;
	var $keycol;
	var $sort;
	var $sortorder;
	var $sort2;
	var $page;
	var $showranking;
	var $numperpage;
	var $var_page;
	var $var_sort;
	var $var_sortorder;
	var $sorthash;
	var $ajax;

	var $columnlist;
	var $startitem;

	var $maxpagenumbers = 20;


	function Table ($columns, $keycol, $sort_default, $sort_default2,
	                $showranking=false, $numperpage=50, $var_page='page',
	                $var_sort='sort', $var_sortorder='sortorder', $sorthash='',
	                $sort_default_order='desc', $ajax = false)
	{

		global $g_options;
		
		$this->columns = $columns;
		$this->keycol  = $keycol;
		$this->showranking = $showranking;
		$this->numperpage  = $numperpage;
		$this->var_page = $var_page;
		$this->var_sort = $var_sort;
		$this->var_sortorder = $var_sortorder;
		$this->sorthash = $sorthash;
		$this->sort_default_order = $sort_default_order;
		$this->ajax = ( $g_options['playerinfo_tabs'] ) ? $ajax : false;

		$this->page = valid_request(intval($_GET[$var_page]), 1);
		$this->sort = valid_request($_GET[$var_sort], 0);
		$this->sortorder = valid_request($_GET[$var_sortorder], 0);


		if ($this->page < 1)
		{
			$this->page = 1;
		}
		$this->startitem = ($this->page - 1) * $this->numperpage;

		foreach ($columns as $col)
		{
			if ($col->sort != 'no')
			{
				$this->columnlist[] = $col->name;
			}
		}


		if (!is_array($this->columnlist) || !in_array($this->sort, $this->columnlist))
		{
			$this->sort = $sort_default;
		}

		if ($this->sortorder != 'asc' && $this->sortorder != 'desc')
		{
			$this->sortorder = $this->sort_default_order;
		}

		if ($this->sort == $sort_default2)
		{
			$this->sort2 = $sort_default;
		}
		else
		{
			$this->sort2 = $sort_default2;
		}
	}
	
	function start($numitems, $width=100, $align='center')
	{
		global $g_options, $game, $realgame, $db;
		$numpages = ceil($numitems / $this->numperpage);
?>

<div class="subblock">

<table class="data-table">

	<tr class="data-table-head">
<?php
		$totalwidth = 0;

		if ($this->showranking)
		{
			$totalwidth += 5;
			echo "<td style=\"width:5%;text-align:right;\" class=\"fSmall\">Rank</td>\n";
		}

		foreach ($this->columns as $col)
		{
			$totalwidth += $col->width;
			echo "<td style=\"width:$col->width%;text-align:$col->align;\" class=\"fSmall\">";
			if ($col->sort != 'no')
			{
				echo getSortArrow($this->sort, $this->sortorder, $col->name,
					$col->title, $this->var_sort, $this->var_sortorder,
					$this->sorthash);
			}
			else
			{
				echo $col->title;
			}
			echo "</td>\n";
		}
?>
	</tr>

<?php
		if ($totalwidth != 100)
		{
			error("Warning: Column widths do not add to 100%! (=$totalwidth%)", false);
		}

		$rank = ($this->page - 1) * $this->numperpage + 1;

	}
	
	function draw ($result, $numitems, $width=100, $align='center')
	{
		global $g_options, $game, $realgame, $db;
		$numpages = ceil($numitems / $this->numperpage);
?>

<div class="subblock" style="width:<?php echo $width; ?>%;text-align:<?php echo $align; ?>;">

<table class="data-table">

		<tr class="data-table-head">
<?php
		$totalwidth = 0;

		if ($this->showranking)
		{
			$totalwidth += 5;
			echo "<td style=\"width:5%;text-align=:right;\" class=\"fSmall\">Rank</td>\n";
		}

		foreach ($this->columns as $col)
		{
			$totalwidth += $col->width;
			echo "<td style=\"width:$col->width%;text-align:$col->align;\" class=\"fSmall\">";
			if ($col->sort != 'no')
			{
				echo getSortArrow($this->sort, $this->sortorder, $col->name,
					$col->title, $this->var_sort, $this->var_sortorder,
					$this->sorthash, $this->ajax);
			}
			else
			{
				echo $col->title;
			}
			echo "</td>\n";
		}
?>
		</tr>

<?php
		if ($totalwidth != 100)
		{
			error("Warning: Column widths do not add to 100%! (=$totalwidth%)", false);
		}

		$rank = ($this->page - 1) * $this->numperpage + 1;

		while ($rowdata = $db->fetch_array($result))
		{
			echo "<tr>\n";
			$i = 0;

			if ($this->showranking)
			{
				$c = ($i % 2) + 1;
				$i++;
				echo "<td style=\"text-align:right;\" class=\"bg$c\">$rank</td>\n";
			}

			foreach ($this->columns as $col)
			{
				$c = ($i % 2) + 1;
				$class="";          

				$cellbody = '';
				$colval = $rowdata[$col->name];

				if ($col->align != 'left')
				{
					$colalign = " style=\"text-align:$col->align;\"";
				}
				else
				{
					$colalign = "";
				}

				$class = "bg$c";

				if (($col->icon) || ($col->flag))
				{
					$cellbody = '&nbsp;';
				}

				if ($col->link)
				{
					if (strpos($col->link, 'javascript:') === false) {
						$link = str_ireplace('%k', urlencode($rowdata[$this->keycol]), $col->link);
						$cellbody .= "<a href=\"" . $g_options['scripturl'] . "?$link\">";
					}
					else
					{              
						$col->link = str_replace('\\\\', '', $col->link);
						$link      = str_ireplace('%k', $rowdata[$this->keycol], $col->link);
						$cellbody .= "<a href=\"$link\">";
					}  
				}

				if ($col->icon)
				{
					$image = getImage("/$col->icon");
					if ($image)
					{
						$cellbody .= '<img src="'.$image['url']. "\" class=\"tableicon\" alt=\"$col->icon\" />";
					}
				}
				elseif ($col->flag)
				{
					#$link = ereg_replace("%f", $col->link);
					if ($g_options['countrydata'] == 1) { 
						if ($rowdata['flag'] == '') {
							$rowdata['flag'] = '0';
							$alt_text        = 'No Country';
						} else {
							$alt_text        = ucfirst(strtolower($rowdata['country']));
						}

						$cellbody .= '<img src="' . getFlag($rowdata['flag'])."\" class=\"tableicon\" alt=\"$alt_text\" title=\"$alt_text\" />";
					}
					else
					{
						$col->flag = 'player';
						$cellbody .= '<img src="' . IMAGE_PATH 	. "/$col->flag.gif\" class=\"tableicon\" alt=\"$col->icon.gif\" />";
					}                
				}  
				
				switch ($col->type)
				{
					case 'timestamp':
						$cellbody  = timestamp_to_str($colval);
						break;           

					case 'roleimg':
						$image = getImage("/games/$game/roles/".strtolower($colval));
						// check if image exists for game -- otherwise check realgame
						if ($image)
						{
							$cellbody .= '<img src="' . $image['url'] . '" alt="' . $col->fname[$colval] . '" title="' . $col->fname[$colval] . '" />&nbsp;';
						}
						elseif ($image = getImage("/games/$realgame/roles/".strtolower($colval)))
						{
							$cellbody .= '<img src="' . $image['url'] . '" alt="' . $col->fname[$colval] . '" title="' . $col->fname[$colval] . '" />&nbsp;';
						}
						
						if ($col->fname[$colval] != '')
						{
							$cellbody .= '<b>'.$col->fname[$colval].'</b>';
						}
						else
						{
							$cellbody .= '<b>'.ucwords(preg_replace('/_/', ' ', $colval)).'</b>';
						}
						break;
					  
					case 'weaponimg':
						// Check if game has the image -- if not, failback to real game.  If not, no image.
						$image = getImage("/games/$realgame/weapons/".strtolower($colval));
						if ($image)
						{
							$cellbody .= '<img src="' . $image['url'] . '" ' . $image['size'] . ' alt="'.$col->fname[$colval].'" title="'.$col->fname[$colval].'" />';
						}
						elseif ($image = getImage("/games/$realgame/weapons/".strtolower($colval)))
						{
							$cellbody .= '<img src="' . $image['url'] . '" ' . $image['size'] . ' alt="'.$col->fname[$colval].'" title="'.$col->fname[$colval].'" />';
						}
						else
						{
							$cellbody .= '<b>' . (($col->fname[$colval] != '') ? $col->fname[$colval] : ucwords(preg_replace('/_/', ' ', $colval))) . '</b>';
						}
						break;

					case 'bargraph':
						$cellbody .= '<img src="' . IMAGE_PATH . '/bar';
						if ($colval > 40)
							$cellbody .= '6';
						elseif ($colval > 30)
							$cellbody .= '5';
						elseif ($colval > 20)
							$cellbody .= '4';
						elseif ($colval > 10)
							$cellbody .= '3';
						elseif ($colval > 5)
							$cellbody .= '2';
						else
							$cellbody .= '1';

						$cellbody .= '.gif" style="width:';
						if ($colval < 1)
							$cellbody .= '1';
						elseif ($colval > 100)
							$cellbody .= '100';
						else
							$cellbody .= sprintf("%d", $colval + 0.5);

						$cellbody .= "%;\" class=\"bargraph\" alt=\"$colval%\" />";
						break;
					case 'heatmap':
						$heatmap = getImage("/games/$game/heatmaps/$colval-kill");
						$heatmapthumb = getImage("/games/$game/heatmaps/$colval-kill-thumb");

						if ($heatmap) {
							$cellbody .= "<span style=\"text-align: center;\"><a href=\"" . $heatmap['url'] . "\" rel=\"boxed\"><img width=\"20\" height=\"16\" src=\"" . $heatmapthumb['url'] . "\" /></a></span>";
						} else {
							$cellbody .= "&nbsp;";
						}
						break;
					default:
						if ($this->showranking && $rank == 1 && $i == 1)
							$cellbody .= '<b>';
						if ((is_numeric($colval)) && ($colval >= 1000))
							$colval = number_format($colval);
						$colval = nl2br(htmlspecialchars($colval, ENT_COMPAT));

						if ($col->embedlink == 'yes')
							{
								$colval = preg_replace(array('/%A%([^ %]+)%/','/%\/A%/'), array("<a href=\"$1\">", '</a>'), $colval);
							}

						$cellbody .= $colval;
							if ($this->showranking && $rank == 1 && $i == 1)
								$cellbody .= '</b>';
							break;
				}

				if ($col->link)
				{
					$cellbody .= '</a>';
				}

				if ($col->append)
				{
					$cellbody .= $col->append;
				}
				
				if ($col->skill_change) {
					if ($rowdata['last_skill_change'] == '')
						$rowdata['last_skill_change'] = 0;
					if ($rowdata['last_skill_change'] == 0)
						$cellbody .= "&nbsp;<img src=\"" . IMAGE_PATH
							. "/t1.gif\" alt=\"".$rowdata['last_skill_change']." Points\" />";
					elseif ($rowdata['last_skill_change'] > 0)
						$cellbody .= "&nbsp;<img src=\"" . IMAGE_PATH
							. "/t0.gif\" alt=\"".$rowdata['last_skill_change']." Points\" />";
					elseif ($rowdata['last_skill_change'] < 0)
						$cellbody .= "&nbsp;<img src=\"" . IMAGE_PATH
							. "/t2.gif\" alt=\"".$rowdata['last_skill_change']." Points\" />";
				}
				
				echo "<td$colalign class=\"$class\">"
						. $cellbody
						. "</td>\n";
				$i++;
			}

			echo "</tr>\n\n";

			$rank++;
		}
?>
		</table>
</div><br /><br />
<?php
		if ($numpages > 1)
		{
?>
<div class="subblock" style="text-align:right;">
	<span style="text-align:right;">
<?php
			echo 'Page: ';

			$start = $this->page - intval($this->maxpagenumbers / 2);
			if ($start < 1) $start=1;

			$end = $numpages;
			if ($end > $this->maxpagenumbers + $start-1)
				$end = $this->maxpagenumbers + $start-1;

			if ($end - $start + 1 < $this->maxpagenumbers)
				$start = $end - $this->maxpagenumbers + 1;

			if ($start < 1) $start=1;

			if ($start > 1)
			{
				if ($start > 2)
					$this->_echoPageNumber(1, "First page", "", " ...");
				else
					$this->_echoPageNumber(1, 1);
			}

			for ($i=$start; $i <= $end; $i++)
			{
				if ($i == $this->page)
				{
					echo "<b>$i</b> ";
				}
				else
				{
					$this->_echoPageNumber($i, $i);
				}

				if ($i == $end && $i < $numpages)
				{
					if ($i < $numpages - 1)
						$this->_echoPageNumber($numpages, "Last page", "... ");
					else
						$this->_echoPageNumber($numpages, 10);
				}
			}
		?>
	</span>
</div><br /><br />
<?php
		}
	}

	function _echoPageNumber ($number, $label, $prefix='', $postfix='')
	{
		global $g_options;

		echo "$prefix<a href=\"" . $g_options['scripturl'] . '?'
			. makeQueryString($this->var_page, $number);
		if ($this->sorthash)
			echo "#$this->sorthash";
		
		if ($this->ajax)
			echo "\" onclick=\"Tabs.refreshTab({'" . $this->var_page . "': " . $number . "}); return false;";
		echo "\">$label</a>$postfix ";
	}
}


//
// TableColumn
//
// Data structure for the properties of a column in a Table
//

class TableColumn
{
	var $name;
	var $title;

	var $align = 'left';
	var $width = 20;
	var $icon;
	var $link;
	var $sort = 'yes';
	var $type = 'text';
	var $embedlink = 'no';
	var $flag;    

	function TableColumn ($name, $title, $attrs="", $fname=null)
	{
		$this->name = $name;
		$this->title= $title;

		$allowed_attrs = array(
			'align',
			'width',
			'icon',
			'link',
			'sort',
			'append',
			'type',
			'embedlink',
			'flag',
			'skill_change',
			'heatmap'
		);

		parse_str($attrs);

		foreach ($allowed_attrs as $a)
		{
			if (isset($$a))
			{
				$this->$a = mystripslashes($$a);
			}
		}
		$this->fname = $fname;
	}
}
