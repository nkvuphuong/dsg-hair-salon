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
$acp_username = explode("=", $db_array[6])[1];
$acp_pwdhash = explode("=", $db_array[7])[1];
$acp_pwdsalt = explode("=", $db_array[8])[1];
$default_language = explode("=", $db_array[10])[1];
$default_language = trim($default_language); 
$storename = explode("=", $db_array[11])[1];
$email_user = explode("=", $db_array[12])[1];
$email_password = explode("=", $db_array[13])[1];
$site_id = explode("=", $db_array[16])[1];
$email_user_arr = explode("@", $email_user);
//email_server = "mail.".$email_user_arr[1];
$email_server = explode("=", $db_array[14])[1];

$whmcs_client_id = explode("=", $db_array[18])[1];
$web_free = explode("=", $db_array[19])[1];



$conn = @mysqli_connect('localhost:3306', $db_user, $db_pass, $db_name);
// Error connection
if (mysqli_connect_errno()) {
   echo "connection_error";exit;
}
$conn->query("SET NAMES 'utf8'");

//Set default language web
$query_string_dl_1 = "UPDATE nh_conf_settings  SET conf_value='$default_language' WHERE conf_key LIKE 'default_language' ";
$conn->query($query_string_dl_1 );

$query_string_dl = "UPDATE nh_conf_settings  SET conf_value='$storename' WHERE conf_key LIKE 'website_title' ";
$conn->query($query_string_dl );

$query_string_3 = "UPDATE nh_conf_settings  SET conf_value='$storename' WHERE conf_key LIKE 'smtp_user_display' ";
$conn->query($query_string_3 );

$query_string_4 = "UPDATE nh_conf_settings  SET conf_value='$email_user' WHERE conf_key LIKE 'smtp_email_display' ";
$conn->query($query_string_4 );
$query_string_5 = "UPDATE nh_conf_settings  SET conf_value='$email_user' WHERE conf_key LIKE 'smtp_user' ";
$conn->query($query_string_5 );
$query_string_6 = "UPDATE nh_conf_settings  SET conf_value='$email_password' WHERE conf_key LIKE 'smtp_password' ";
$conn->query($query_string_6 );

$query_string_7 = "UPDATE nh_conf_settings  SET conf_value='$email_server' WHERE conf_key LIKE 'smtp_server' ";
$conn->query($query_string_7 );

$query_string_9 = "UPDATE nh_conf_settings  SET conf_value='465' WHERE conf_key LIKE 'smtp_port' ";
$conn->query($query_string_9 );

$query_string_10 = "INSERT INTO nh_conf_settings(conf_title,conf_key,conf_value,conf_group) VALUES ('Site ID','site_id','$site_id','1')";
$conn->query($query_string_10 );


$query_string_11 = "INSERT INTO nh_conf_settings(conf_title,conf_key,conf_value,conf_group) VALUES ('WHMCS Client ID','whmcs_client_id','$whmcs_client_id','1')";
$conn->query($query_string_11 );

$query_string_sync = "INSERT INTO nh_conf_settings(conf_title,conf_key,conf_value,conf_group) VALUES ('IBE Sync','ibe_synced','0','1')";
$conn->query($query_string_sync );
 

// if (!$result = $conn->query($query_string_dl )) {
// 	//echo "update_default_language_fail";
// }
// else
// {
// 	//echo "update_default_language_success";
// }
// Truncate old data table
$conn->query("TRUNCATE nh_cache");
$conn->query("TRUNCATE nh_logs");
$conn->query("TRUNCATE nh_sessions");
$conn->query("TRUNCATE nh_user_sessions");
//$conn->query("TRUNCATE nh_user");

$user_joined = time();
 
//$query_string = "INSERT INTO nh_user ( user_name, user_display_name, userg_id,user_joined,user_login_key, user_permission,user_hash, user_salt)VALUES ( '$acp_username','$acp_username','1','$user_joined','e98e9e021d171b89d9c484ebba46da7d','$user_permission','$acp_pwdhash', '$acp_pwdsalt')";

//TRUNCATE `nh_cache`
// if (!$result = $conn->query($query_string )) {
// 	echo "create_user_admin_fail";exit;
// }
// else
// {
	echo "create_user_admin_done";exit;
//}	
?>