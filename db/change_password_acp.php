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

$conn = @mysqli_connect('localhost:3306', $db_user, $db_pass, $db_name);
// Error connection
if (mysqli_connect_errno()) {
   echo "connection_error";exit;
}
$conn->query("SET NAMES 'utf8'");

//Set default language web
$query_string = "SELECT * FROM nh_user WHERE user_name='admin' AND user_deleted = 0 ";
$conn->query($query_string );
if (!$result = $conn->query($query_string )) {
	echo "no_account_admin";exit;
}
else
{
	
    $query_string_dl = "UPDATE nh_user  SET  user_hash = '$acp_pwdhash', user_salt = '$acp_pwdsalt' WHERE user_name='admin' AND user_deleted = 0 ";
    if (!$result = $conn->query($query_string_dl )) {
		
		echo "update_password_acp_faild";exit;
	}
	else
	{
		echo "update_password_acp_success";exit;
	}	

}

?>