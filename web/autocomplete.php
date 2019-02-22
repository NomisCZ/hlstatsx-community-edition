<?php
define('IN_HLSTATS', true);

// Load required files
require('config.php');
require(INCLUDE_PATH . '/class_db.php');
require(INCLUDE_PATH . '/functions.php');

$db_classname = 'DB_' . DB_TYPE;
if (class_exists($db_classname))
{
        $db = new $db_classname(DB_ADDR, DB_USER, DB_PASS, DB_NAME, DB_PCONNECT);
}
else
{
        error('Database class does not exist.  Please check your config.php file for DB_TYPE');
}

$game = valid_request($_GET['game']);
$search = valid_request($_POST['value']);

$game_escaped = $db->escape($game);
$search_escaped = $db->escape($search);
 
if (is_string($search) && strlen($search) >= 3 && strlen($search) < 64) {
	// Building the query
	$sql = "SELECT hlstats_PlayerNames.name FROM hlstats_PlayerNames INNER JOIN hlstats_Players ON hlstats_PlayerNames.playerId = hlstats_Players.playerId WHERE game = '{$game_escaped}' AND name LIKE '{$search_escaped}%'";
	$result = $db->query($sql);
	while($row=$db->fetch_row($result)) {
		print "<li class=\"playersearch\">" . $row[0] . "</li>\n";
	}
}
?>
