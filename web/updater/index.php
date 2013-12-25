<?php
	$url = str_replace(array("/updater", '/index.php'), array('',''), isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : getenv('PHP_SELF'));
	header("Location: $url/hlstats.php?mode=updater");
	exit;
?>