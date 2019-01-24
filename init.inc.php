<?php
 
use \core\ezy;
use \lib\cookie;

/**
 * Fix date time
 */
//print_r(DateTimeZone::listIdentifiers()); exit;
//date_default_timezone_set('Asia/Ho_Chi_Minh');

/**
 * Declare main path
 */

define( 'root_path', dirname( __FILE__ ) ."/" );
define( 'IN_ROOT', 1 );
define( 'is_init', true );

$info = [];

require(root_path. "config.inc.php");

/**
 * Display errors for each environment
 */

if ( isset($info["ezy_env"]) ) {
    if ($info["ezy_env"] == "raw") {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
    } else if ($info["ezy_env"] == "test") {
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
        ini_set("display_errors", 1);
    } else if ($info["ezy_env"] == "development" ) {
        error_reporting(E_ERROR | E_PARSE);
        ini_set("display_errors", 1);
    } else if ( $info['ezy_env'] == "production" ) {
        error_reporting(E_ERROR | E_PARSE);
        ini_set("display_errors", 1);
    }
    else {
        error_reporting(E_ERROR | E_PARSE);
        ini_set("display_errors", 1);
    }
}  //

/**
 * Start a session
 */
 
if ( ! session_id() )
{
	session_start();
}
  
/**
 * Main vars
 */

$CMS = new stdClass();
$CMS->class = new stdClass();
$CMS->api = new stdClass();
$CMS->core = new stdClass();

$CMS->input = array();
$CMS->vars = &$info;
$CMS->lang = &$lang;
$CMS->errormsg = isset($_SESSION['msg'])?$_SESSION['msg']:'';
$CMS->is_error = 0;

$tpl = new stdClass();

// Load app config
require(root_path. "config.app.php");

// Load site config
require(root_path. "config.route.php");

/**
 * Load core files.
 */

$ezy_path = array(
    root_path."kernel/models/",
    root_path."kernel/api/",
    root_path."kernel/lib/",
    root_path."app/views/layouts/"
);

foreach ( $ezy_path as $dir ) {
    $dir_content = \scandir($dir);
    foreach ($dir_content as $file) {
        if (substr(strtolower($file), -3, 3) == "php" && substr(strtolower($file), -9, 9) != ".html.php" ) {
            require_once $dir . $file;
        }
    }
}
   
require root_path."kernel/core.php";

// Old vars
$CMS->core->page_title = ezy::$title;

/**
 * EzyWeb Detector
 */

$host = $_SERVER['HTTP_HOST'];
$site_routed = $site_routes[$host];

// Root vars
if ( isset($site_routed) == true )
{
    // Vars
    $CMS->vars['root_domain'] = $site_routed['domain'];
    $CMS->vars['parent_domain'] = $site_routed['domain'];
    $CMS->vars['theme'] = $site_routed['theme'];
    $temp_parse = parse_url($site_routed['domain']);
    $info['db_name'] = isset($site_routed['db']) ? $site_routed['db'] : $info['db_name']; // Set Database name
    if(!empty($site_routed['is_web_us']))
    {     
        define("is_web_us", $site_routed['is_web_us'] );  
    }
    if(!empty($site_routed['is_web_vn']))
    {
        define("is_web_vn", $site_routed['is_web_vn'] );
    } 

    // Use form 1: nails; 2: mer; 3: bds
    if(!empty($site_routed['type_web']))
    {
        define("type_web", $site_routed['type_web'] );
    }   
       

    // Cookie
    cookie::$domain = ".".$temp_parse["host"]; // Set cookie domain

    // User dir
    ezy::$user_dir = $site_routed['upload'];
}

// Check cron
if ( defined("is_cron") == true OR defined("is_api") == true)
{
    // Init core
    new ezy;
}

/**
 * Routes, find the app match with the hostname
 */

else if ( isset($site_routed) == true )
{
    // Check https
    if( !isset($_SERVER["HTTPS"]) && $temp_parse["scheme"] == "https" ) // && $_SERVER["HTTPS"] != "on"
    {
        header("location: {$CMS->vars['root_domain']}");
        exit();
    }
    
   
    // Init core
    new ezy;

    date_default_timezone_set(isset($CMS->vars['timezone_id']) ? $CMS->vars['timezone_id'] : date_default_timezone_get());

    // Prevent load except web
    // Tạm thời comment lại vì chưa dùng - nkvp - 2017.11.23
    /*if ( !defined("is_web") ) {
        $CMS->vars['siteInfo'] = $CMS->sites->get_info($host);
        $CMS->vars['siteInfo']['site_license_package'] = $CMS->vars['siteInfo']['site_license_package'] ? $CMS->vars['siteInfo']['site_license_package'] : $CMS->subscription->getPackageList()[0]['package_name'];
    }*/

    // Check if app_dir == web
    if ( $site_routed['app'] == ezy::$web_name )
    {
        // Load theme init
        $file_theme_init = root_path."/themes/".$site_routed['theme']."/theme.inc.php";
        if ( file_exists($file_theme_init) )
        {
            include $file_theme_init;
        }
 
        // Start init some necessary vars
        ezy::$web_views = "/themes/".$site_routed['theme']."/views/";
        ezy::$web_assets = "/themes/".$site_routed['theme']."/assets/";
        ezy::$web_theme = $site_routed['theme'];

        // Using for custom form that belongs to career
        ezy::$theme_key = substr(ezy::$web_theme,0,3);
    }

    // If detected an app & Prevent app reloading if the app is loaded
    if ( isset($site_routed['app']) && !defined("in_app") )
    {  
        include_once(root_path.$site_routed['app']."/index.php");
        exit;
    }
}

/**
 * Client, try to load homepage if no route is found.
 */

else
{
    // Init global
    ezy::init_global();

    // Set custom error
    $tpl->msg = $CMS->lang['site_notfound'];

    // Output error page
    echo ezy::load_layout("error_maintenance");
    exit;
}