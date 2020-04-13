#!/usr/bin/php
<?php

require_once('./config.inc.php');
require_once('./heatmap.class.php');

$heatMap = new HeatMap();

foreach (Env::get('mapinfo') as $game => $gameConf) {

	foreach ($gameConf as $map => $data) {

		$heatMap->generate($game, $map, "kill");
	}
}

Show::Event("CREATE", "Heatmap creation done.", 1);