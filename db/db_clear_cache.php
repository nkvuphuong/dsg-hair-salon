<?php
define('ROOTPATH', __DIR__);
$root_path = str_replace("/db","",ROOTPATH);
define( 'is_api', true ); 
// Init
require_once $root_path."/init.inc.php";

// Clear cache files
$CMS->vars['cache_type'] = 'files'; 
$CMS->class->cache->clear();

// Clear cache redis
$CMS->vars['cache_type'] = 'redis'; 
$CMS->class->cache->clear();

print "Clear cache successs";exit;
?>