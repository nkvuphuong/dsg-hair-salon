<?php
// Namespace
use \core\ezy;

// Set app's defined
define("in_app", true);

//Load vendor
include_once("../vendor/autoload.php");

// Load init
if ( defined("root_path") == false )
{
    include_once("../init.inc.php");
}

// default language
\lib\language::$default = $CMS->vars['default_language'] = isset($_SESSION['current_lang']) ? $_SESSION['current_lang'] : 'vn';
$CMS->class->language->load("application");
// Set app's name
ezy::$app_dir = "booking_data";
ezy::$site_default = "idx";
ezy::$asset_dir = "/booking_data/assets/"; // Force asset dir
ezy::$web_assets = "/booking_data/assets/"; // Force asset dir
//ezy::$secret_key = "ZbioRz1oub"; // key for check security

// Home controllers
ezy::$routes = array(
    "main" =>  "main",
    "city" =>  "city",
    "store" =>  "store",
    "product" =>  "product",
    "staff" =>  "staff",
    "work_schedule" =>  "work_schedule",
    "shift_work" =>  "shift_work",
    "order" =>  "order",
    "price" =>  "price",
    "rating" =>  "rating",
);

//Check auth api
ezy::$api_auth = false;

ezy::init_api();

