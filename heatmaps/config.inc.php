<?php
error_reporting(E_ALL);
ini_set("memory_limit", "32M");
ini_set("max_execution_time", "0");

define('DB_HOST',	'localhost');
define('DB_USER',	'');
define('DB_PASS',	'');
define('DB_NAME',	'');
define('HLXCE_WEB',	'/path/to/where/you/have/your/hlstats/web');
define('HUD_URL',	'http://www.hlxcommunity.com');
define('OUTPUT_SIZE',	'medium');

define('DB_PREFIX',	'hlstats');
define('KILL_LIMIT',	10000);
define('DEBUG', 1);

// No need to change this unless you are on really low disk.
define('CACHE_DIR',	dirname(__FILE__) . '/cache');

