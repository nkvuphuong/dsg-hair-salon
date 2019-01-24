<?php
// exit("<script>setTimeout(function(){window.location.reload()},3000);</script>");
// Namespace
use \core\ezy;

// Set app's defined
define("in_app", true);

// Load init
if ( defined("root_path") == false )
{
    include_once("../init.inc.php");
}

// Only accept site that have whm permission (set "app" => "whm" to allow sites access to this folder)
if ( $site_routed['app'] != "appweb" )
{
    header("location: {$site_routed['domain']}");
    exit;
}

// default language
\lib\language::$default = $CMS->vars['default_language'] = isset($_SESSION['current_lang']) ? $_SESSION['current_lang'] : 'vn';
$CMS->class->language->load("application");
// Set app's name
ezy::$app_dir = "appweb";
ezy::$site_default = "idx";
ezy::$asset_dir = "/appweb/assets/"; // Force asset dir
ezy::$web_assets = "/appweb/assets/"; // Force asset dir

$CMS->vars['id_nhanhoa'] = "https://id.nhanhoa.com";
// Home controllers
ezy::$routes = array(
	"idx" 					=>	"board",
	"templates" 			=>	"templates",
	"pricing" 				=>	"pricing",
	"features"	 			=>	"features",
	"register" 				=>	"register",
	"news" 					=>	"news",
	"contact" 				=>	"contact",
	"login" 				=>	"login",
	"client" 				=>	"client",
	"payment" 				=>	"payment",
	"order" 				=>	"order",
	"page404"				=>  "page404", 
	"information" 			=>	"information", 
	"templates_by_industry" =>	"templates_by_industry", 
	"pricing_general"   	=> 	"pricing_general", 
	"templates_travel" 		=>	"templates_travel", 
	"pages" 				=>	"pages", 

	// seo
	"gioi-thieu-web4s.html"			=>	"features",
	"tinh-nang-noi-bat.html"			=>	"features",
	"kho-giao-dien-thiet-ke-moi.html"	=>	"templates",
	"kho-giao-dien-thiet-ke-moi"		=>	"templates",
	"bao-gia-thiet-ke-web.html"			=>	"pricing",
	"bao-gia-thiet-ke-web"				=>	"pricing",
	"tin-tuc.html"						=>	"news",
	"tin-tuc"							=>	"news",
	"thong-tin-lien-he.html"			=>	"contact",
	"tai-khoan.html"					=>	"client",
	"tai-khoan"							=>	"client",
	"dang-ky-dung-thu-website.html"		=>	"register",
	"dang-ky-dung-thu.html"				=>	"register",
	"dang-ky-dung-thu"					=>	"register",

	"page404.html"						=>  "page404",
	"thong-tin.html" 					=>	"information", 
	"thong-tin" 						=>	"information", 
	"thiet-ke-website-da-nganh"			=> 	"templates_by_industry", 
	"thiet-ke-website-da-nganh.html"	=> 	"templates_by_industry", 
	"bang-bao-gia-thiet-ke-web"			=>	"pricing_general", 
	"bang-bao-gia-thiet-ke-web.html"	=>	"pricing_general", 
	"thiet-ke-website-du-lich"			=> 	"templates_travel",
	"thiet-ke-website-du-lich.html"		=> 	"templates_travel",
	"trang"								=> 	"pages",
	"trang.html"						=> 	"pages",

);
 
ezy::$seo_routes = array(
    "n" => ["news", "detail"],
 
);
// Init Home
ezy::init_index();