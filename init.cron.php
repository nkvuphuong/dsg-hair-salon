<?php

use core\ezy;

/**
 * Init
 */

// Set max execution
ini_set('max_execution_time', 300);

// Is defined cron mode
define( "is_cron", true );

// Load init
include_once "init.inc.php";

/**
 * Firewall
 */

$CMS->vars['whitelist_cron_enable'] = false; // set tam de pass qua

// Get white list
if ( isset($CMS->vars['whitelist_cron_enable']) && $CMS->vars['whitelist_cron_enable'] == true )
{
    // Get host name
    $url = parse_url($CMS->vars['root_domain']);

    // Prevent if not in white list
    if ( ezy::$ip_address != gethostbyname($url['host']) AND !in_array(ezy::$ip_address, explode(",", $CMS->vars['whitelist_cron'])))
    {
        header("location: {$CMS->vars['root_domain']}");
    }
}