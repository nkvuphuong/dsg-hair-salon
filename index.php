<?php
// Namespace
use \core\ezy;

// Load init
include_once("init.inc.php");

// Default page
ezy::$site_default = "idx";

// Home controllers
ezy::$routes = array(
	"idx" 			=>	"board",
	"contact" 			=>	"contact",
	"register" 			=>	"register",
    "ezybook-online" 			=>	"register",
);

// Init Home
ezy::init_index();

?>