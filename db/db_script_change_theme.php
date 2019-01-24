<?php
define('ROOTPATH', __DIR__);
$root_path = str_replace("/db","",ROOTPATH);
$str = file_get_contents($root_path.'/db/db_info.txt');
// Load app config
require($root_path. "/config.app.php");
$db_array = explode("|||", $str);

$db_name = explode("=", $db_array[0])[1];
$db_user = explode("=", $db_array[1])[1];
$db_pass = explode("=", $db_array[2])[1];
$url_domain = explode("=", $db_array[3])[1];
$root_domain = explode("=", $db_array[4])[1];
$theme = explode("=", $db_array[5])[1];
$hosting_username = explode("=", $db_array[9])[1];
$acp_username = explode("=", $db_array[6])[1];
$acp_pwdhash = explode("=", $db_array[7])[1];
$acp_pwdsalt = explode("=", $db_array[8])[1];
$email_server = explode("=", $db_array[14])[1];
$email_user = explode("=", $db_array[12])[1];
$email_password = explode("=", $db_array[13])[1];
$site_id = explode("=", $db_array[16])[1];
$is_ssl = explode("=", $db_array[15])[1];
$site_app_secret = explode("=", $db_array[17])[1];
$whmcs_client_id = explode("=", $db_array[18])[1];
$web_free = explode("=", $db_array[19])[1];

$upload_original = explode("=", $db_array[20])[1];

$is_ssl = intval($is_ssl);
$sms_from_free = $CMS->vars['sms_from_nexmo'][array_rand($CMS->vars['sms_from_nexmo'])];
if($is_ssl == 1)
{
	$url_www_domain_rp = "https://www.".$root_domain;
}else
{
	$url_www_domain_rp = "http://www.".$root_domain;
}

//read the entire string
$route_content =file_get_contents($root_path.'/db/config.route.txt');
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
file_put_contents($root_path.'/db/config.route_bk.txt', $route_content);
unlink($root_path.'/config.route.php'); 
rename($root_path.'/db/config.route_bk.txt', $root_path.'/config.route.php'); 

file_put_contents($root_path.'/uploads/'.$hosting_username.'/db.txt', '');

$conn = @mysqli_connect('localhost:3306', $db_user, $db_pass, $db_name);
// Error connection
if (mysqli_connect_errno()) {
   echo "connection_error";exit;
}
$conn->query("SET NAMES 'utf8'");



// $query_string_4 = "UPDATE nh_conf_settings  SET conf_value='$email_user' WHERE conf_key LIKE 'smtp_email_display' ";
// $conn->query($query_string_4 );

// $query_string_5 = "UPDATE nh_conf_settings  SET conf_value='$email_user' WHERE conf_key LIKE 'smtp_user' ";
// $conn->query($query_string_5 );

// $query_string_6 = "UPDATE nh_conf_settings  SET conf_value='$email_password' WHERE conf_key LIKE 'smtp_password' ";
// $conn->query($query_string_6 );



// $query_string_7 = "UPDATE nh_conf_settings  SET conf_value='$email_server' WHERE conf_key LIKE 'smtp_server' ";
// $conn->query($query_string_7);

// $query_string_8 = "UPDATE nh_conf_settings  SET conf_value='465' WHERE conf_key LIKE 'smtp_port' ";
// $conn->query($query_string_8 );

$sql_s = $conn->query("SELECT * FROM nh_conf_settings WHERE conf_key = 'site_id' ");
if($sql_s->num_rows  > 0)
{
	 $query_string_8 = "UPDATE nh_conf_settings  SET conf_value='$site_id' WHERE conf_key LIKE 'site_id' ";
	 $conn->query($query_string_8 );
}
else
{
	$query_string_10 = "INSERT INTO nh_conf_settings(conf_title,conf_key,conf_value,conf_group) VALUES ('Site ID','site_id','$site_id','1')";
	$conn->query($query_string_10 );
}


$sql_whmcs = $conn->query("SELECT * FROM nh_conf_settings WHERE conf_key = 'whmcs_client_id' ");
if($sql_whmcs->num_rows  > 0)
{
	 $query_string_whmcs = "UPDATE nh_conf_settings  SET conf_value='$whmcs_client_id' WHERE conf_key LIKE 'whmcs_client_id' ";
	 $conn->query($query_string_whmcs);
}
else
{
	$query_string_whmcs = "INSERT INTO nh_conf_settings(conf_title,conf_key,conf_value,conf_group) VALUES ('WHMCS Client ID','whmcs_client_id','$whmcs_client_id','1')";
	$conn->query($query_string_whmcs);
}


 



$sql_sms = $conn->query("SELECT * FROM nh_conf_settings WHERE conf_key = 'sms_from_free' ");
if($sql_sms->num_rows  > 0)
{
	// $sql_sms2 = $conn->query("SELECT * FROM nh_conf_settings WHERE conf_key = 'sms_subscription' ");
	// if($sql_sms2->num_rows  > 0)
	// {
	// 	$data_sms2 = $sql_sms2->fetch_array();
	// 	if(intval($data_sms2['conf_value']) == 0)
	// 	{
	// 		$query_string_12 = "UPDATE nh_conf_settings  SET conf_value='$sms_from_free' WHERE conf_key LIKE 'sms_from_free' ";
	// 	 	$conn->query($query_string_12 );
	// 	}
	// }	 
}
else
{
	 
	$query_string_11 = "INSERT INTO nh_conf_settings(conf_title,conf_key,conf_value,conf_group) VALUES ('SMS From Toll Free','sms_from_free','$sms_from_free','1')";
	$conn->query($query_string_11);
}



$sql_usr = $conn->query("SELECT * FROM nh_user WHERE user_name = 'support' AND user_deleted =0 ");
if($sql_usr->num_rows  == 0)
{
	//Check exits acc Support for insert new
	$user_joined = time();
$user_permission= 'a:389:{s:16:"dashboard_is_all";i:1;s:12:"login_is_all";i:1;s:16:"myaccount_is_all";i:1;s:13:"search_is_all";i:1;s:23:"config_general_is_admin";i:1;s:19:"config_general_read";i:1;s:18:"config_general_add";i:1;s:19:"config_general_edit";i:1;s:21:"config_general_delete";i:1;s:14:"email_is_admin";i:1;s:10:"email_read";i:1;s:9:"email_add";i:1;s:10:"email_edit";i:1;s:12:"email_delete";i:1;s:12:"email_search";i:1;s:17:"emailtpl_is_admin";i:1;s:13:"emailtpl_read";i:1;s:12:"emailtpl_add";i:1;s:13:"emailtpl_edit";i:1;s:15:"emailtpl_delete";i:1;s:15:"emailtpl_search";i:1;s:12:"user_is_root";i:1;s:9:"user_read";i:1;s:8:"user_add";i:1;s:9:"user_edit";i:1;s:11:"user_delete";i:1;s:11:"user_search";i:1;s:22:"user_config_commission";i:1;s:18:"user_group_is_root";i:1;s:15:"user_group_read";i:1;s:14:"user_group_add";i:1;s:15:"user_group_edit";i:1;s:17:"user_group_delete";i:1;s:14:"config_is_root";i:1;s:12:"logs_is_root";i:1;s:9:"logs_read";i:1;s:11:"logs_search";i:1;s:15:"comment_is_root";i:1;s:12:"comment_read";i:1;s:11:"comment_add";i:1;s:12:"comment_edit";i:1;s:14:"comment_delete";i:1;s:12:"comment_hide";i:1;s:15:"comment_approve";i:1;s:16:"accounts_is_root";i:1;s:13:"accounts_read";i:1;s:12:"accounts_add";i:1;s:13:"accounts_edit";i:1;s:15:"accounts_delete";i:1;s:15:"accounts_search";i:1;s:16:"accounts_arrange";i:1;s:21:"accounts_type_is_root";i:1;s:18:"accounts_type_read";i:1;s:17:"accounts_type_add";i:1;s:18:"accounts_type_edit";i:1;s:20:"accounts_type_delete";i:1;s:20:"accounts_type_search";i:1;s:21:"accounts_type_arrange";i:1;s:13:"store_is_root";i:1;s:10:"store_read";i:1;s:9:"store_add";i:1;s:10:"store_edit";i:1;s:12:"store_delete";i:1;s:12:"store_search";i:1;s:13:"store_arrange";i:1;s:14:"store_transfer";i:1;s:19:"manufacture_is_root";i:1;s:16:"manufacture_read";i:1;s:15:"manufacture_add";i:1;s:16:"manufacture_edit";i:1;s:18:"manufacture_delete";i:1;s:21:"manufacture_deleteall";i:1;s:18:"manufacture_search";i:1;s:19:"manufacture_arrange";i:1;s:21:"product_group_is_root";i:1;s:18:"product_group_read";i:1;s:17:"product_group_add";i:1;s:18:"product_group_edit";i:1;s:20:"product_group_delete";i:1;s:20:"product_group_search";i:1;s:21:"product_group_arrange";i:1;s:20:"product_group_import";i:1;s:20:"product_group_export";i:1;s:15:"product_is_root";i:1;s:12:"product_read";i:1;s:11:"product_add";i:1;s:12:"product_edit";i:1;s:14:"product_delete";i:1;s:14:"product_search";i:1;s:16:"product_hide_all";i:1;s:15:"product_arrange";i:1;s:24:"product_get_barcode_info";i:1;s:23:"product_preview_barcode";i:1;s:14:"product_import";i:1;s:14:"product_export";i:1;s:14:"assets_is_root";i:1;s:11:"assets_read";i:1;s:10:"assets_add";i:1;s:11:"assets_edit";i:1;s:13:"assets_delete";i:1;s:13:"assets_search";i:1;s:23:"assets_get_barcode_info";i:1;s:22:"assets_preview_barcode";i:1;s:21:"assets_print_to_excel";i:1;s:20:"config_price_is_root";i:1;s:17:"config_price_read";i:1;s:16:"config_price_add";i:1;s:17:"config_price_edit";i:1;s:19:"config_price_delete";i:1;s:19:"config_price_search";i:1;s:17:"inventory_is_root";i:1;s:14:"inventory_read";i:1;s:13:"inventory_add";i:1;s:14:"inventory_edit";i:1;s:16:"inventory_delete";i:1;s:16:"inventory_search";i:1;s:17:"inventory_approve";i:1;s:16:"customer_is_root";i:1;s:13:"customer_read";i:1;s:12:"customer_add";i:1;s:13:"customer_edit";i:1;s:15:"customer_delete";i:1;s:15:"customer_search";i:1;s:16:"customer_arrange";i:1;s:22:"group_customer_is_root";i:1;s:19:"group_customer_read";i:1;s:18:"group_customer_add";i:1;s:19:"group_customer_edit";i:1;s:21:"group_customer_delete";i:1;s:21:"group_customer_search";i:1;s:13:"order_is_root";i:1;s:10:"order_read";i:1;s:9:"order_add";i:1;s:10:"order_edit";i:1;s:12:"order_delete";i:1;s:12:"order_search";i:1;s:13:"order_arrange";i:1;s:11:"order_renew";i:1;s:12:"order_rating";i:1;s:20:"transactions_is_root";i:1;s:17:"transactions_read";i:1;s:16:"transactions_add";i:1;s:17:"transactions_edit";i:1;s:20:"transactions_preview";i:1;s:18:"transactions_print";i:1;s:19:"transactions_delete";i:1;s:19:"transactions_search";i:1;s:20:"transactions_arrange";i:1;s:16:"supplier_is_root";i:1;s:13:"supplier_read";i:1;s:12:"supplier_add";i:1;s:13:"supplier_edit";i:1;s:15:"supplier_delete";i:1;s:15:"supplier_search";i:1;s:16:"supplier_arrange";i:1;s:24:"partner_delivery_is_root";i:1;s:21:"partner_delivery_read";i:1;s:20:"partner_delivery_add";i:1;s:21:"partner_delivery_edit";i:1;s:23:"partner_delivery_delete";i:1;s:23:"partner_delivery_search";i:1;s:24:"partner_delivery_arrange";i:1;s:16:"shipment_is_root";i:1;s:13:"shipment_read";i:1;s:12:"shipment_add";i:1;s:13:"shipment_edit";i:1;s:15:"shipment_delete";i:1;s:15:"shipment_search";i:1;s:16:"shipment_arrange";i:1;s:21:"store_request_is_root";i:1;s:26:"store_request_read_request";i:1;s:25:"store_request_add_request";i:1;s:26:"store_request_edit_request";i:1;s:28:"store_request_delete_request";i:1;s:28:"store_request_search_request";i:1;s:29:"store_request_approve_request";i:1;s:28:"store_request_cancel_request";i:1;s:29:"store_request_read_request_ei";i:1;s:28:"store_request_add_request_ei";i:1;s:29:"store_request_edit_request_ei";i:1;s:31:"store_request_delete_request_ei";i:1;s:31:"store_request_search_request_ei";i:1;s:32:"store_request_approve_request_ei";i:1;s:31:"store_request_cancel_request_ei";i:1;s:30:"store_request_read_request_eis";i:1;s:29:"store_request_add_request_eis";i:1;s:25:"transaction_terms_is_root";i:1;s:22:"transaction_terms_read";i:1;s:21:"transaction_terms_add";i:1;s:22:"transaction_terms_edit";i:1;s:24:"transaction_terms_delete";i:1;s:24:"transaction_terms_search";i:1;s:15:"returns_is_root";i:1;s:12:"returns_read";i:1;s:11:"returns_add";i:1;s:12:"returns_edit";i:1;s:14:"returns_delete";i:1;s:14:"returns_search";i:1;s:15:"returns_approve";i:1;s:14:"returns_cancel";i:1;s:12:"news_is_root";i:1;s:9:"news_read";i:1;s:8:"news_add";i:1;s:9:"news_edit";i:1;s:11:"news_delete";i:1;s:11:"news_search";i:1;s:12:"news_approve";i:1;s:26:"config_parent_news_is_root";i:1;s:23:"config_parent_news_read";i:1;s:22:"config_parent_news_add";i:1;s:23:"config_parent_news_edit";i:1;s:25:"config_parent_news_delete";i:1;s:25:"config_parent_news_search";i:1;s:26:"config_parent_news_arrange";i:1;s:14:"videos_is_root";i:1;s:11:"videos_read";i:1;s:10:"videos_add";i:1;s:11:"videos_edit";i:1;s:13:"videos_delete";i:1;s:13:"videos_search";i:1;s:14:"videos_approve";i:1;s:28:"config_parent_videos_is_root";i:1;s:25:"config_parent_videos_read";i:1;s:24:"config_parent_videos_add";i:1;s:25:"config_parent_videos_edit";i:1;s:27:"config_parent_videos_delete";i:1;s:27:"config_parent_videos_search";i:1;s:28:"config_parent_videos_arrange";i:1;s:17:"giftcards_is_root";i:1;s:14:"giftcards_read";i:1;s:13:"giftcards_add";i:1;s:14:"giftcards_edit";i:1;s:16:"giftcards_delete";i:1;s:16:"giftcards_search";i:1;s:17:"giftcards_arrange";i:1;s:15:"coupons_is_root";i:1;s:12:"coupons_read";i:1;s:11:"coupons_add";i:1;s:12:"coupons_edit";i:1;s:14:"coupons_delete";i:1;s:14:"coupons_search";i:1;s:15:"coupons_arrange";i:1;s:11:"sms_is_root";i:1;s:8:"sms_read";i:1;s:7:"sms_add";i:1;s:8:"sms_edit";i:1;s:10:"sms_delete";i:1;s:10:"sms_search";i:1;s:11:"sms_arrange";i:1;s:14:"smstpl_is_root";i:1;s:11:"smstpl_read";i:1;s:10:"smstpl_add";i:1;s:11:"smstpl_edit";i:1;s:13:"smstpl_search";i:1;s:14:"smstpl_arrange";i:1;s:22:"config_gallery_is_root";i:1;s:19:"config_gallery_read";i:1;s:18:"config_gallery_add";i:1;s:19:"config_gallery_edit";i:1;s:21:"config_gallery_delete";i:1;s:21:"config_gallery_search";i:1;s:22:"config_gallery_arrange";i:1;s:13:"pages_is_root";i:1;s:10:"pages_read";i:1;s:9:"pages_add";i:1;s:10:"pages_edit";i:1;s:12:"pages_delete";i:1;s:12:"pages_search";i:1;s:16:"position_is_root";i:1;s:13:"position_read";i:1;s:12:"position_add";i:1;s:13:"position_edit";i:1;s:15:"position_delete";i:1;s:15:"position_search";i:1;s:13:"logos_is_root";i:1;s:10:"logos_read";i:1;s:9:"logos_add";i:1;s:10:"logos_edit";i:1;s:12:"logos_delete";i:1;s:12:"logos_search";i:1;s:22:"logo_positions_is_root";i:1;s:19:"logo_positions_read";i:1;s:18:"logo_positions_add";i:1;s:19:"logo_positions_edit";i:1;s:21:"logo_positions_delete";i:1;s:21:"logo_positions_search";i:1;s:18:"newsletter_is_root";i:1;s:15:"newsletter_read";i:1;s:14:"newsletter_add";i:1;s:15:"newsletter_edit";i:1;s:20:"newsletter_send_mail";i:1;s:17:"newsletter_delete";i:1;s:17:"newsletter_search";i:1;s:14:"addons_is_root";i:1;s:11:"addons_read";i:1;s:10:"addons_add";i:1;s:11:"addons_edit";i:1;s:13:"addons_delete";i:1;s:15:"gallery_is_root";i:1;s:12:"gallery_read";i:1;s:11:"gallery_add";i:1;s:12:"gallery_edit";i:1;s:14:"gallery_delete";i:1;s:14:"gallery_search";i:1;s:15:"gallery_arrange";i:1;s:14:"report_is_root";i:1;s:11:"report_read";i:1;s:12:"report_sales";i:1;s:12:"report_order";i:1;s:14:"report_product";i:1;s:15:"report_customer";i:1;s:14:"report_finance";i:1;s:16:"report_inventory";i:1;s:13:"report_assets";i:1;s:14:"redeem_is_root";i:1;s:11:"redeem_read";i:1;s:10:"redeem_add";i:1;s:11:"redeem_edit";i:1;s:13:"redeem_delete";i:1;s:11:"seo_is_root";i:1;s:8:"seo_read";i:1;s:7:"seo_add";i:1;s:8:"seo_edit";i:1;s:10:"seo_delete";i:1;s:10:"seo_search";i:1;s:16:"discount_is_root";i:1;s:13:"discount_read";i:1;s:12:"discount_add";i:1;s:13:"discount_edit";i:1;s:15:"discount_delete";i:1;s:15:"discount_search";i:1;s:15:"discount_import";i:1;s:15:"discount_export";i:1;s:22:"discount_items_is_root";i:1;s:19:"discount_items_read";i:1;s:18:"discount_items_add";i:1;s:19:"discount_items_edit";i:1;s:21:"discount_items_delete";i:1;s:21:"discount_items_search";i:1;s:21:"discount_items_import";i:1;s:21:"discount_items_export";i:1;s:16:"facebook_is_root";i:1;s:13:"facebook_read";i:1;s:12:"facebook_add";i:1;s:13:"facebook_edit";i:1;s:15:"facebook_delete";i:1;s:13:"embed_is_root";i:1;s:10:"embed_read";i:1;s:9:"embed_add";i:1;s:10:"embed_edit";i:1;s:12:"embed_delete";i:1;s:12:"embed_search";i:1;s:12:"embed_import";i:1;s:12:"embed_export";i:1;s:17:"interface_is_root";i:1;s:14:"interface_read";i:1;s:13:"interface_add";i:1;s:14:"interface_edit";i:1;s:16:"interface_delete";i:1;s:15:"contact_is_root";i:1;s:12:"contact_read";i:1;s:14:"contact_delete";i:1;s:14:"contact_search";i:1;s:14:"rating_is_root";i:1;s:11:"rating_read";i:1;s:10:"rating_add";i:1;s:11:"rating_edit";i:1;s:13:"rating_delete";i:1;s:13:"rating_search";i:1;s:13:"rating_import";i:1;s:13:"rating_export";i:1;s:15:"service_is_root";i:1;s:12:"service_read";i:1;s:11:"service_add";i:1;s:12:"service_edit";i:1;s:14:"service_delete";i:1;s:14:"service_search";i:1;s:16:"service_hide_all";i:1;s:15:"service_arrange";i:1;s:24:"service_get_barcode_info";i:1;s:23:"service_preview_barcode";i:1;s:14:"service_import";i:1;s:14:"service_export";i:1;s:13:"staff_is_root";i:1;s:10:"staff_read";i:1;s:9:"staff_add";i:1;s:10:"staff_edit";i:1;s:12:"staff_delete";i:1;s:12:"staff_search";i:1;}';
 
	$user_joined = time();
	$query_string_usr = "INSERT INTO nh_user ( user_name, user_display_name, userg_id,user_joined,user_login_key, user_permission,user_hash, user_salt)VALUES ( 'support','Supporter','1','$user_joined','2ca98d5cbca60481f81b70a7deac0897','$user_permission','9d09a06fef465ef38c4ceda1390e5de2', 'uT$6%')";

	 $conn->query($query_string_usr );


}




$user_permission_2 = 'a:389:{s:16:"dashboard_is_all";i:1;s:12:"login_is_all";i:1;s:16:"myaccount_is_all";i:1;s:13:"search_is_all";i:1;s:23:"config_general_is_admin";i:1;s:19:"config_general_read";i:1;s:18:"config_general_add";i:1;s:19:"config_general_edit";i:1;s:21:"config_general_delete";i:1;s:14:"email_is_admin";i:1;s:10:"email_read";i:0;s:9:"email_add";i:0;s:10:"email_edit";i:0;s:12:"email_delete";i:0;s:12:"email_search";i:0;s:17:"emailtpl_is_admin";i:1;s:13:"emailtpl_read";i:0;s:12:"emailtpl_add";i:0;s:13:"emailtpl_edit";i:0;s:15:"emailtpl_delete";i:0;s:15:"emailtpl_search";i:0;s:12:"user_is_root";i:1;s:9:"user_read";i:0;s:8:"user_add";i:0;s:9:"user_edit";i:0;s:11:"user_delete";i:0;s:11:"user_search";i:0;s:22:"user_config_commission";i:0;s:18:"user_group_is_root";i:1;s:15:"user_group_read";i:0;s:14:"user_group_add";i:0;s:15:"user_group_edit";i:0;s:17:"user_group_delete";i:0;s:14:"config_is_root";i:1;s:12:"logs_is_root";i:1;s:9:"logs_read";i:1;s:11:"logs_search";i:1;s:15:"comment_is_root";i:1;s:12:"comment_read";i:0;s:11:"comment_add";i:0;s:12:"comment_edit";i:0;s:14:"comment_delete";i:0;s:12:"comment_hide";i:0;s:15:"comment_approve";i:0;s:16:"accounts_is_root";i:1;s:13:"accounts_read";i:0;s:12:"accounts_add";i:0;s:13:"accounts_edit";i:0;s:15:"accounts_delete";i:0;s:15:"accounts_search";i:0;s:16:"accounts_arrange";i:0;s:21:"accounts_type_is_root";i:1;s:18:"accounts_type_read";i:0;s:17:"accounts_type_add";i:0;s:18:"accounts_type_edit";i:0;s:20:"accounts_type_delete";i:0;s:20:"accounts_type_search";i:0;s:21:"accounts_type_arrange";i:0;s:13:"store_is_root";i:1;s:10:"store_read";i:0;s:9:"store_add";i:0;s:10:"store_edit";i:0;s:12:"store_delete";i:0;s:12:"store_search";i:0;s:13:"store_arrange";i:0;s:14:"store_transfer";i:0;s:19:"manufacture_is_root";i:1;s:16:"manufacture_read";i:0;s:15:"manufacture_add";i:0;s:16:"manufacture_edit";i:0;s:18:"manufacture_delete";i:0;s:21:"manufacture_deleteall";i:0;s:18:"manufacture_search";i:0;s:19:"manufacture_arrange";i:0;s:21:"product_group_is_root";i:1;s:18:"product_group_read";i:1;s:17:"product_group_add";i:1;s:18:"product_group_edit";i:1;s:20:"product_group_delete";i:1;s:20:"product_group_search";i:1;s:21:"product_group_arrange";i:1;s:20:"product_group_import";i:1;s:20:"product_group_export";i:1;s:15:"product_is_root";i:1;s:12:"product_read";i:0;s:11:"product_add";i:0;s:12:"product_edit";i:0;s:14:"product_delete";i:0;s:14:"product_search";i:0;s:16:"product_hide_all";i:0;s:15:"product_arrange";i:0;s:24:"product_get_barcode_info";i:0;s:23:"product_preview_barcode";i:0;s:14:"product_import";i:0;s:14:"product_export";i:0;s:14:"assets_is_root";i:1;s:11:"assets_read";i:0;s:10:"assets_add";i:0;s:11:"assets_edit";i:0;s:13:"assets_delete";i:0;s:13:"assets_search";i:0;s:23:"assets_get_barcode_info";i:0;s:22:"assets_preview_barcode";i:0;s:21:"assets_print_to_excel";i:0;s:20:"config_price_is_root";i:1;s:17:"config_price_read";i:0;s:16:"config_price_add";i:0;s:17:"config_price_edit";i:0;s:19:"config_price_delete";i:0;s:19:"config_price_search";i:0;s:17:"inventory_is_root";i:1;s:14:"inventory_read";i:0;s:13:"inventory_add";i:0;s:14:"inventory_edit";i:0;s:16:"inventory_delete";i:0;s:16:"inventory_search";i:0;s:17:"inventory_approve";i:0;s:16:"customer_is_root";i:1;s:13:"customer_read";i:1;s:12:"customer_add";i:1;s:13:"customer_edit";i:1;s:15:"customer_delete";i:1;s:15:"customer_search";i:1;s:16:"customer_arrange";i:1;s:22:"group_customer_is_root";i:1;s:19:"group_customer_read";i:1;s:18:"group_customer_add";i:1;s:19:"group_customer_edit";i:1;s:21:"group_customer_delete";i:1;s:21:"group_customer_search";i:1;s:13:"order_is_root";i:1;s:10:"order_read";i:1;s:9:"order_add";i:1;s:10:"order_edit";i:1;s:12:"order_delete";i:1;s:12:"order_search";i:1;s:13:"order_arrange";i:1;s:11:"order_renew";i:1;s:12:"order_rating";i:1;s:20:"transactions_is_root";i:1;s:17:"transactions_read";i:0;s:16:"transactions_add";i:0;s:17:"transactions_edit";i:0;s:20:"transactions_preview";i:0;s:18:"transactions_print";i:0;s:19:"transactions_delete";i:0;s:19:"transactions_search";i:0;s:20:"transactions_arrange";i:0;s:16:"supplier_is_root";i:1;s:13:"supplier_read";i:0;s:12:"supplier_add";i:0;s:13:"supplier_edit";i:0;s:15:"supplier_delete";i:0;s:15:"supplier_search";i:0;s:16:"supplier_arrange";i:0;s:24:"partner_delivery_is_root";i:1;s:21:"partner_delivery_read";i:0;s:20:"partner_delivery_add";i:0;s:21:"partner_delivery_edit";i:0;s:23:"partner_delivery_delete";i:0;s:23:"partner_delivery_search";i:0;s:24:"partner_delivery_arrange";i:0;s:16:"shipment_is_root";i:1;s:13:"shipment_read";i:0;s:12:"shipment_add";i:0;s:13:"shipment_edit";i:0;s:15:"shipment_delete";i:0;s:15:"shipment_search";i:0;s:16:"shipment_arrange";i:0;s:21:"store_request_is_root";i:1;s:26:"store_request_read_request";i:0;s:25:"store_request_add_request";i:0;s:26:"store_request_edit_request";i:0;s:28:"store_request_delete_request";i:0;s:28:"store_request_search_request";i:0;s:29:"store_request_approve_request";i:0;s:28:"store_request_cancel_request";i:0;s:29:"store_request_read_request_ei";i:0;s:28:"store_request_add_request_ei";i:0;s:29:"store_request_edit_request_ei";i:0;s:31:"store_request_delete_request_ei";i:0;s:31:"store_request_search_request_ei";i:0;s:32:"store_request_approve_request_ei";i:0;s:31:"store_request_cancel_request_ei";i:0;s:30:"store_request_read_request_eis";i:0;s:29:"store_request_add_request_eis";i:0;s:25:"transaction_terms_is_root";i:1;s:22:"transaction_terms_read";i:0;s:21:"transaction_terms_add";i:0;s:22:"transaction_terms_edit";i:0;s:24:"transaction_terms_delete";i:0;s:24:"transaction_terms_search";i:0;s:15:"returns_is_root";i:1;s:12:"returns_read";i:0;s:11:"returns_add";i:0;s:12:"returns_edit";i:0;s:14:"returns_delete";i:0;s:14:"returns_search";i:0;s:15:"returns_approve";i:0;s:14:"returns_cancel";i:0;s:12:"news_is_root";i:1;s:9:"news_read";i:0;s:8:"news_add";i:0;s:9:"news_edit";i:0;s:11:"news_delete";i:0;s:11:"news_search";i:0;s:12:"news_approve";i:0;s:26:"config_parent_news_is_root";i:1;s:23:"config_parent_news_read";i:0;s:22:"config_parent_news_add";i:0;s:23:"config_parent_news_edit";i:0;s:25:"config_parent_news_delete";i:0;s:25:"config_parent_news_search";i:0;s:26:"config_parent_news_arrange";i:0;s:14:"videos_is_root";i:1;s:11:"videos_read";i:0;s:10:"videos_add";i:0;s:11:"videos_edit";i:0;s:13:"videos_delete";i:0;s:13:"videos_search";i:0;s:14:"videos_approve";i:0;s:28:"config_parent_videos_is_root";i:1;s:25:"config_parent_videos_read";i:0;s:24:"config_parent_videos_add";i:0;s:25:"config_parent_videos_edit";i:0;s:27:"config_parent_videos_delete";i:0;s:27:"config_parent_videos_search";i:0;s:28:"config_parent_videos_arrange";i:0;s:17:"giftcards_is_root";i:1;s:14:"giftcards_read";i:0;s:13:"giftcards_add";i:0;s:14:"giftcards_edit";i:0;s:16:"giftcards_delete";i:0;s:16:"giftcards_search";i:0;s:17:"giftcards_arrange";i:0;s:15:"coupons_is_root";i:1;s:12:"coupons_read";i:1;s:11:"coupons_add";i:1;s:12:"coupons_edit";i:1;s:14:"coupons_delete";i:1;s:14:"coupons_search";i:1;s:15:"coupons_arrange";i:1;s:11:"sms_is_root";i:1;s:8:"sms_read";i:0;s:7:"sms_add";i:0;s:8:"sms_edit";i:0;s:10:"sms_delete";i:0;s:10:"sms_search";i:0;s:11:"sms_arrange";i:0;s:14:"smstpl_is_root";i:1;s:11:"smstpl_read";i:0;s:10:"smstpl_add";i:0;s:11:"smstpl_edit";i:0;s:13:"smstpl_search";i:0;s:14:"smstpl_arrange";i:0;s:22:"config_gallery_is_root";i:1;s:19:"config_gallery_read";i:1;s:18:"config_gallery_add";i:1;s:19:"config_gallery_edit";i:1;s:21:"config_gallery_delete";i:1;s:21:"config_gallery_search";i:1;s:22:"config_gallery_arrange";i:1;s:13:"pages_is_root";i:1;s:10:"pages_read";i:0;s:9:"pages_add";i:0;s:10:"pages_edit";i:0;s:12:"pages_delete";i:0;s:12:"pages_search";i:0;s:16:"position_is_root";i:1;s:13:"position_read";i:1;s:12:"position_add";i:1;s:13:"position_edit";i:1;s:15:"position_delete";i:1;s:15:"position_search";i:1;s:13:"logos_is_root";i:1;s:10:"logos_read";i:1;s:9:"logos_add";i:1;s:10:"logos_edit";i:1;s:12:"logos_delete";i:1;s:12:"logos_search";i:1;s:22:"logo_positions_is_root";i:1;s:19:"logo_positions_read";i:1;s:18:"logo_positions_add";i:1;s:19:"logo_positions_edit";i:1;s:21:"logo_positions_delete";i:1;s:21:"logo_positions_search";i:1;s:18:"newsletter_is_root";i:1;s:15:"newsletter_read";i:0;s:14:"newsletter_add";i:0;s:15:"newsletter_edit";i:0;s:20:"newsletter_send_mail";i:0;s:17:"newsletter_delete";i:0;s:17:"newsletter_search";i:0;s:14:"addons_is_root";i:1;s:11:"addons_read";i:0;s:10:"addons_add";i:0;s:11:"addons_edit";i:0;s:13:"addons_delete";i:0;s:15:"gallery_is_root";i:1;s:12:"gallery_read";i:1;s:11:"gallery_add";i:1;s:12:"gallery_edit";i:1;s:14:"gallery_delete";i:1;s:14:"gallery_search";i:1;s:15:"gallery_arrange";i:1;s:14:"report_is_root";i:1;s:11:"report_read";i:0;s:12:"report_sales";i:0;s:12:"report_order";i:0;s:14:"report_product";i:0;s:15:"report_customer";i:0;s:14:"report_finance";i:0;s:16:"report_inventory";i:0;s:13:"report_assets";i:0;s:14:"redeem_is_root";i:1;s:11:"redeem_read";i:0;s:10:"redeem_add";i:0;s:11:"redeem_edit";i:0;s:13:"redeem_delete";i:0;s:11:"seo_is_root";i:1;s:8:"seo_read";i:0;s:7:"seo_add";i:0;s:8:"seo_edit";i:0;s:10:"seo_delete";i:0;s:10:"seo_search";i:0;s:16:"discount_is_root";i:1;s:13:"discount_read";i:0;s:12:"discount_add";i:0;s:13:"discount_edit";i:0;s:15:"discount_delete";i:0;s:15:"discount_search";i:0;s:15:"discount_import";i:0;s:15:"discount_export";i:0;s:22:"discount_items_is_root";i:1;s:19:"discount_items_read";i:0;s:18:"discount_items_add";i:0;s:19:"discount_items_edit";i:0;s:21:"discount_items_delete";i:0;s:21:"discount_items_search";i:0;s:21:"discount_items_import";i:0;s:21:"discount_items_export";i:0;s:16:"facebook_is_root";i:1;s:13:"facebook_read";i:0;s:12:"facebook_add";i:0;s:13:"facebook_edit";i:0;s:15:"facebook_delete";i:0;s:13:"embed_is_root";i:1;s:10:"embed_read";i:0;s:9:"embed_add";i:0;s:10:"embed_edit";i:0;s:12:"embed_delete";i:0;s:12:"embed_search";i:0;s:12:"embed_import";i:0;s:12:"embed_export";i:0;s:17:"interface_is_root";i:1;s:14:"interface_read";i:0;s:13:"interface_add";i:0;s:14:"interface_edit";i:0;s:16:"interface_delete";i:0;s:15:"contact_is_root";i:1;s:12:"contact_read";i:1;s:14:"contact_delete";i:1;s:14:"contact_search";i:1;s:14:"rating_is_root";i:1;s:11:"rating_read";i:0;s:10:"rating_add";i:0;s:11:"rating_edit";i:0;s:13:"rating_delete";i:0;s:13:"rating_search";i:0;s:13:"rating_import";i:0;s:13:"rating_export";i:0;s:15:"service_is_root";i:1;s:12:"service_read";i:1;s:11:"service_add";i:1;s:12:"service_edit";i:1;s:14:"service_delete";i:1;s:14:"service_search";i:1;s:16:"service_hide_all";i:1;s:15:"service_arrange";i:1;s:24:"service_get_barcode_info";i:0;s:23:"service_preview_barcode";i:0;s:14:"service_import";i:1;s:14:"service_export";i:1;s:13:"staff_is_root";i:1;s:10:"staff_read";i:1;s:9:"staff_add";i:1;s:10:"staff_edit";i:1;s:12:"staff_delete";i:1;s:12:"staff_search";i:1;}';

$sql_usr_cp = $conn->query("SELECT * FROM nh_user WHERE user_name = 'admincp' AND user_deleted =0 ");
if($sql_usr_cp->num_rows  > 0)
{
	$query_string_cp = "UPDATE nh_user  SET user_permission='$user_permission_2' WHERE user_name = 'admincp' AND user_deleted =0  ";

	 $conn->query($query_string_cp );
}
else
{
	$user_joined = time();
	$query_string_cp = "INSERT INTO nh_user ( user_name, user_display_name, userg_id,user_joined,user_login_key, user_permission,user_hash, user_salt)VALUES ( 'admincp','Admin CP','2','$user_joined','e98e9e021d171b89d9c484ebba46da7d','$user_permission_2','$acp_pwdhash', '$acp_pwdsalt')";

	 $conn->query($query_string_cp );
}


$conn->query("TRUNCATE nh_cache");
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
 
if(@is_dir($base_uploads)){    rrmdir ( $base_uploads); }
if(@is_dir($base_uploads_ibe)){ rrmdir ( $base_uploads_ibe); }

echo "done_db_script_change_theme";exit;
?>