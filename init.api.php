<?php

use core\ezy;

/**
 * Init
 */

// Set max execution
ini_set('max_execution_time', 300);

// Is defined cron mode
define( "is_api", true );

// Load init
include_once "init.inc.php";

?>