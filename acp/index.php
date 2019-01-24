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
ezy::$app_dir = "acp";
 
//Stop if route app is whm
if($site_routed['app'] == 'whm')
{
    header( "HTTP/1.1 404 Not Found" ); exit;
}

// Detect /acp/ path
if ( isset($site_routed['app']) )
{
    // If the app is not 'acp', then insert /acp/ path into root_domain
    if ( $site_routed['app'] != ezy::$app_dir )
    {
        $CMS->vars['root_domain'] .= "/".ezy::$app_dir;
    }
}
// If there's no route
else if ( !isset($site_routed[1]) )
{
    $CMS->vars['root_domain'] .= "/".ezy::$app_dir;
}


// Core
$CMS->vars['is_admin_module'] = 1;
$CMS->vars['img_url'] = "/acp/images";
$CMS->vars['js_acp'] = "/acp/jsacp";
$CMS->vars['js_url'] = "/acp/javascript";
$CMS->vars['css_url'] = "/acp/css";
$CMS->vars['public_url'] = "/public";
$CMS->vars['npm_url'] = "/node_modules";
$CMS->vars['site_routed_app'] = $site_routed['app'];
$CMS->vars['site_routed_domain'] = parse_url($site_routed['domain'])['host'];
// Init javascript body action
$CMS->vars['nav'] = "";

ezy::init_acp();