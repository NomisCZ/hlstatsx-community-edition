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

	define('IN_HLSTATS', true);

	// Load database classes
	require ('config.php');
	require (INCLUDE_PATH . '/class_db.php');
	require (INCLUDE_PATH . '/functions.php');
	require (INCLUDE_PATH . '/pChart/pData.class');
	require (INCLUDE_PATH . '/pChart/pChart.class');

	$db_classname = 'DB_' . DB_TYPE;
	if (class_exists($db_classname))
	{
		$db = new $db_classname(DB_ADDR, DB_USER, DB_PASS, DB_NAME, DB_PCONNECT);
	}
	else
	{
		error('Database class does not exist.  Please check your config.php file for DB_TYPE');
	}

	$g_options = getOptions();
	
	$bg_color = array('red' => 90, 'green' => 90, 'blue' => 90);
	if ((isset($_GET['bgcolor'])) && (is_string($_GET['bgcolor'])))
		$bg_color = hex2rgb(valid_request($_GET['bgcolor'], 0));

	$color = array('red' => 213, 'green' => 217, 'blue' => 221);
	if ((isset($_GET['color'])) && (is_string($_GET['color'])))
		$color = hex2rgb(valid_request($_GET['color'], 0));
	
	if (isset($_GET['player'])) $player = (int)$_GET['player'];
	if (!$player) exit();
	
	$res = $db->query("SELECT UNIX_TIMESTAMP(eventTime) AS ts, skill, skill_change FROM hlstats_Players_History WHERE playerId = '$player' ORDER BY eventTime DESC LIMIT 30");
	$skill = array();
	$skill_change = array();
	$date = array();
	$rowcnt = $db->num_rows();
	$last_time = 0;
	for ($i = 1; $i <= $rowcnt; $i++)
	{
		$row = $db->fetch_array($res);
		array_unshift($skill, ($row['skill']==0)?0:($row['skill']/1000));
		array_unshift($skill_change, $row['skill_change']);
		if ($i == 1 || $i == round($rowcnt/2) || $i == $rowcnt)
		{
			array_unshift($date, date("M-j", $row['ts']));
			$last_time = $row['ts'];
		}
		else
		{
			array_unshift($date, '');
		}
	}
	
	$cache_image = IMAGE_PATH . "/progress/trend_{$player}_{$last_time}.png";
	if (file_exists($cache_image))
	{
		header('Content-type: image/png');
		readfile($cache_image);
		exit();
	}
	
	$Chart = new pChart(400, 200);
	$Chart->drawBackground($bg_color['red'], $bg_color['green'], $bg_color['blue']);
	
	$Chart->setGraphArea(40, 28, 339, 174);
	$Chart->drawGraphAreaGradient(40, 40, 40, -50);
	
	if (count($date) < 2)
	{
		$Chart->setFontProperties(IMAGE_PATH . '/sig/font/DejaVuSans.ttf', 11);
		$Chart->drawTextBox(100, 90, 180, 110, "Not Enough Session Data", 0, 0, 0, 0, ALIGN_LEFT, FALSE, 255, 255, 255, 0);
	}
	else
	{	
		$DataSet = new pData;
		$DataSet->AddPoint($skill, 'SerieSkill');
		$DataSet->AddPoint($skill_change, 'SerieSession');
		$DataSet->AddPoint($date, 'SerieDate');
		$DataSet->AddSerie('SerieSkill');
		$DataSet->SetAbsciseLabelSerie('SerieDate');
		$DataSet->SetSerieName('Skill', 'SerieSkill');
		$DataSet->SetSerieName('Session', 'SerieSession');

		$Chart->setFontProperties(IMAGE_PATH . '/sig/font/DejaVuSans.ttf', 7);
		$DataSet->SetYAxisName('Skill');
		$DataSet->SetYAxisUnit('K');
		$Chart->setColorPalette(0, 255, 255, 0);
		$Chart->drawRightScale($DataSet->GetData(), $DataSet->GetDataDescription(),
			SCALE_NORMAL, $color['red'], $color['green'], $color['blue'], TRUE, 0, 0);
		$Chart->drawGrid(1, FALSE, 55, 55, 55, 100);
		$Chart->setShadowProperties(3, 3, 0, 0, 0, 30, 4);
		$Chart->drawCubicCurve($DataSet->GetData(), $DataSet->GetDataDescription());
		$Chart->clearShadow();
		$Chart->drawFilledCubicCurve($DataSet->GetData(), $DataSet->GetDataDescription(), .1, 30);
		$Chart->drawPlotGraph($DataSet->GetData(), $DataSet->GetDataDescription(), 1, 1, 255, 255, 255);
		
		$Chart->clearScale();

		$DataSet->RemoveSerie('SerieSkill');
		$DataSet->AddSerie('SerieSession');
		$DataSet->SetYAxisName('Session');
		$DataSet->SetYAxisUnit('');
		$Chart->setColorPalette(1, 255, 0,   0);
		$Chart->setColorPalette(2,   0, 0, 255);
		$Chart->drawScale($DataSet->GetData(), $DataSet->GetDataDescription(),
			SCALE_NORMAL, $color['red'], $color['green'], $color['blue'], TRUE, 0, 0);
		$Chart->setShadowProperties(3, 3, 0, 0, 0, 30, 4);
		$Chart->drawCubicCurve($DataSet->GetData(), $DataSet->GetDataDescription());
		$Chart->clearShadow();
		$Chart->drawPlotGraph($DataSet->GetData(), $DataSet->GetDataDescription(), 1, 1, 255, 255, 255);
		
		$Chart->setFontProperties(IMAGE_PATH . '/sig/font/DejaVuSans.ttf',7);
		$Chart->drawHorizontalLegend(235, -1, $DataSet->GetDataDescription(),
			0, 0, 0, 0, 0, 0, $color['red'], $color['green'], $color['blue'], FALSE);
	}
	
	$cache_image = IMAGE_PATH . "/progress/trend_{$player}_{$last_time}.png";
	$Chart->Render($cache_image);
	header("Location: $cache_image");
?>