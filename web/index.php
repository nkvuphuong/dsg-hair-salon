<?php

// Namespace
use \core\ezy;

// Load init
if ( defined("root_path") == false )
{
    include_once("../init.inc.php");
}

// Default page
ezy::$site_default = "idx";
ezy::$app_dir = "web";

// Home controllers
ezy::$routes = array(
    "home"   => "board",
    "idx" 			=>	"board",
    "alive" => "alive",
    "about" 			=>	"about",
    "about-us" 		=>	"about",
    "service" 			=>	"service",
    "services" 		=>	"service",
    "booking" 			=>	"book",
    "book" 			=>	"book",
    "giftcards" 		=>	"giftcards",
    "contact" 			=>	"contact",
    "gallery" 			=>	"gallery",
    "customer" 			=>	"customer",
    "login"                     => "login",
    "news"                      => "news",
    "register"                  => "register",
    "newsletter"                => "newsletter",
    "sms"                       => "sms",
    "tag"                       => "tag",
    "coupons"                   => "coupons",
    "payment"                   => "payment",
    "cart"                      => "cart",
    "videos"                     => "videos",
    "p"                     => "pages",
    "sitemap"               => "sitemap",
    "product"   => "product",
    "security"   => "security",
    "project"   => "project",
    "error"   => "error",
    "salon"   => "salon",
);

// seo_name => [controller_name, controller_action];
// Ex: controller_name = product; controller_action = detail
ezy::$seo_routes = array(
    "sp" => ["product", "detail"],
    "pc" => ["product", "group"],
    "n" => ["news", "detail"],
    "nc" => ["news", "category"],
    "j" => ["project", "detail"],
    "jc" => ["project", "category"],
    "s" => ["service", "detail"],
    "sc" => ["service", "group"],
    "kh" => ["customer", "detail"],
	"hv" => ["customer", "detail"],
	"vd" => ["videos", "detail"],
    "vc" => ["videos", "category"],
    "sl" => ["salon", "store"],
);
 
if ( $site_routed['app'] == ezy::$web_name )
{
    // Load theme init
    $file_route_init = root_path."/themes/".$site_routed['theme']."/route.php";
   
    if ( file_exists($file_route_init) )
    {
        $route = array();
        include $file_route_init;
        ezy::$routes = array_merge(ezy::$routes,$route);
    }
}

  
// Images
$CMS->vars['img_url'] = $CMS->vars['root_domain']."/themes/".ezy::$web_theme."/assets";

// Init Home
ezy::init_index();

?>