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
$mode_debug = explode("=", $db_array[14])[1];

//read the entire string
$str_content =file_get_contents($root_path.'/db/config.inc.txt');
$is_ssl = explode("=", $db_array[15])[1];
$is_ssl = intval($is_ssl);
if($is_ssl == 1)
{
	$url_www_domain_rp = "https://www.".$root_domain;
}else
{
	$url_www_domain_rp = "http://www.".$root_domain;
}
//replace something in the file string 
$str_content=str_replace("db_name_rp", $db_name, $str_content);
$str_content=str_replace("db_username_rp",$db_user,$str_content);
$str_content=str_replace("db_password_rp",$db_pass, $str_content);
$str_content=str_replace("url_domain_rp",$url_domain, $str_content);
$str_content=str_replace("root_domain_rp",$root_domain, $str_content);
$str_content=str_replace("mode_debug",$mode_debug, $str_content);
//write the entire string
file_put_contents($root_path.'/db/config.inc_bk.txt', $str_content);
unlink($root_path.'/config.inc.php'); 
rename($root_path.'/db/config.inc_bk.txt', $root_path.'/config.inc.php'); 
echo "done_change_mode_debug";exit;
?>