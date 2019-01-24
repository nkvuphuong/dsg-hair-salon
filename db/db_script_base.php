<?php
define('ROOTPATH', __DIR__);
$root_path = str_replace("/db","",ROOTPATH);
$str = file_get_contents($root_path.'/db/db_info.txt');
$db_array = explode("|||", $str);

$db_name = explode("=", $db_array[0])[1];
$db_user = explode("=", $db_array[1])[1];
$db_pass = explode("=", $db_array[2])[1];
$url_domain = explode("=", $db_array[3])[1];
$root_domain = explode("=", $db_array[4])[1];
$theme = explode("=", $db_array[5])[1];
$hosting_username = explode("=", $db_array[9])[1];


$upload_original = explode("=", $db_array[20])[1];

$url_www_domain_rp = "http://www.".$root_domain;
//read the entire string
$str_content =file_get_contents($root_path.'/config.inc.php');
  
//replace something in the file string 
$str_content=str_replace("db_name_rp", $db_name, $str_content);
$str_content=str_replace("db_username_rp",$db_user,$str_content);
$str_content=str_replace("db_password_rp",$db_pass, $str_content);
$str_content=str_replace("url_domain_rp",$url_domain, $str_content);
$str_content=str_replace("root_domain_rp",$root_domain, $str_content);
//write the entire string
file_put_contents($root_path.'/config.inc.php', $str_content);

$route_content =file_get_contents($root_path.'/config.route.php');
$route_content=str_replace("root_domain_rp", $root_domain, $route_content);
$route_content=str_replace("url_www_domain_rp",$url_www_domain_rp, $route_content);
$route_content=str_replace("url_domain_rp", $url_domain, $route_content);
$route_content=str_replace("db_name_rp",$db_name,$route_content);
$route_content=str_replace("theme_rp",$theme,$route_content);

if($upload_original != "")
{
	$route_content=str_replace("hosting_username",$upload_original,$route_content);
}else
{
	$route_content=str_replace("hosting_username",$hosting_username,$route_content);
}

//write the entire string
file_put_contents($root_path.'/config.route.php', $route_content);
//Check base folder upload and rename folder
$base_uploads =  $root_path.'/uploads/demo3f'; 
$base_uploads_ibe =  $root_path.'/uploads/ibe'; 


function rrmdir($dir) {
  if (is_dir($dir)) {
    $objects = scandir($dir);
    foreach ($objects as $object) {
      if ($object != "." && $object != "..") {
        if (filetype($dir."/".$object) == "dir") 
           rrmdir($dir."/".$object); 
        else unlink   ($dir."/".$object);
      }
    }
    reset($objects);
    rmdir($dir);
  }
 }

if($upload_original != "")
{
	// @rename ($root_path.'/uploads/'.$hosting_username, $root_path.'/uploads/'.$upload_original);
	// if(@is_dir($root_path.'/uploads/'.$hosting_username)){    rrmdir ( $root_path.'/uploads/'.$hosting_username); }
} 


if(@is_dir($base_uploads)){    rrmdir ( $base_uploads); }
if(@is_dir($base_uploads_ibe)){ rrmdir ( $base_uploads_ibe); }
echo "done_db_script_base";exit;
?>