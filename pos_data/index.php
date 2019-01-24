<?php
// Namespace
use \core\ezy;

// Set app's defined
define("in_app", true);

// Load init
if ( defined("root_path") == false )
{
    include_once("../init.inc.php");
}

// default language
\lib\language::$default = $CMS->vars['default_language'] = isset($_SESSION['current_lang']) ? $_SESSION['current_lang'] : 'vn';
$CMS->class->language->load("application");
// Set app's name
ezy::$app_dir = "pos_data";
ezy::$site_default = "idx";
ezy::$asset_dir = "/pos_data/assets/"; // Force asset dir
ezy::$web_assets = "/pos_data/assets/"; // Force asset dir
//ezy::$secret_key = "ZbioRz1oub"; // key for check security

// Home controllers
ezy::$routes = array(
    "main" =>  "main",
    "product" =>  "product",
    "product_category" =>  "product_category",
    "staff" =>  "staff",
    "customer" =>  "customer",
    "city" =>  "city",
    "district" =>  "district",
    "ward" =>  "ward",
    "bill" =>  "bill",
    "order" =>  "order",
);

//Check auth api
ezy::$api_auth = true;

ezy::init_api();

