<?php
set_time_limit(120);
// Namespace
use \core\ezy;

// Set app's defined
define("in_app", true);



// Load init
if ( defined("root_path") == false )
{
    include_once("../init.inc.php");
}


// Set app's name
ezy::$app_dir = "checkin";

// Detect /acp/ path
$CMS->vars['root_domain'] .= "/".ezy::$app_dir;

// Core
$CMS->vars['is_admin_module'] = 1;
$CMS->vars['img_url'] = "/checkin/images";
$CMS->vars['js_url'] = "/checkin/javascript";
$CMS->vars['css_url'] = "/checkin/css";
$CMS->vars['public_url'] = "/public";
$CMS->vars['npm_url'] = "/node_modules";

// Home controllers
ezy::$routes = array(
    "home" =>  "home",
);

if (!\lib\input::vars('checkin_enabled')) {
   header('location: ' .  \lib\input::vars('parent_domain'));
   exit;
}

ezy::init_checkin();