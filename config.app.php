<?php

// Overwrite internal config
if ( $CMS->vars['ezy_env'] != "production" )
{
    $CMS->vars['db_debug'] = 1;
}

define("root_table", "nh_");


// First day of Week
$CMS->vars['site_firstdayofweek'] = "Monday";

// Email
$CMS->vars['time_prevent_duplicate_email'] = 60; // Thời gian tối thiểu cho phép gửi trùng email (phút)
$CMS->vars['email_send_by_cron'] = 0; // Email send by cron


// Bulk Email
$CMS->vars['mass_email_cycle'] = 60; // Mass Email Cycle (Seconds)
$CMS->vars['mass_email_hit'] = 50; // Mass Email Per Hit

// Currency
$CMS->vars['currency_type'] = "đ";
$CMS->vars['currency_separate'] = ",";

// Cron
$CMS->vars['cron_default_limit'] = 1;

// SEcurity
$CMS->vars['system_key'] = ""; // FU*TxcjgQUKac*f0

// SMS Settings
$CMS->vars['sms_nexmo_key'] = "9e03611c";
$CMS->vars['sms_nexmo_secret'] = "6f07944e74f7fbd6";
$CMS->vars['sms_nexmo_sms_url'] = "https://rest.nexmo.com/sms/json?";
$CMS->vars['sms_nexmo_number2'] = "12028525423";//  "12028525423"
$CMS->vars['sms_nexmo_number'] = "12028525423";// "12014645852"
$CMS->vars['sms_send_by_cron'] = 0; // Sms send by cron
$CMS->vars['sms_method'] = 'curl'; // nexmo, curl // Cau hinh đe gui bang thu vien cua nexmo hoac curl
$CMS->vars['sms_from_nexmo'] = array("18448805051", "18447981776", "18558215313", "18882829877", "18884793701", "18889899053", "18889899107", "18889899125", "18889899148", "18889899167");
//$CMS->vars['sms_from_nexmo'] = "18882829877";

// Old config
$CMS->vars['ticket_pick_timeout'] = 600; // Thời gian giữ phiên làm việc khi trả lời
$CMS->vars['upload_file_size'] = "62914560"; // User max upload per file:
$CMS->vars['session_time_out'] = "14400"; // Admin Session (Seconds)
$CMS->vars['upload_allow_ext'] = "gif,jpg,jpeg,png,doc,docx,zip,rar,htm,html,bmp,xls,pdf,ppt,flv,odt,pptx,swf,flv,xlsx"; // Allow upload exentions:

// Format date for booking javascript
$CMS->vars['option_date_format'] = "<option value='YYYY/MM/DD'>YYYY/MM/DD</option><option value='MM/DD/YYYY'>MM/DD/YYYY</option><option value='DD/MM/YYYY'>DD/MM/YYYY</option>";
$CMS->vars['position_date_format'] = array("YYYY/MM/DD" => "2,1,0", "MM/DD/YYYY" => "1,0,2", "DD/MM/YYYY" => "0,1,2");// chuan theo: ngay/thang/nam
$CMS->vars['dateformat_php'] = array("YYYY/MM/DD" => "Y/m/d", "MM/DD/YYYY" => "m/d/Y", "DD/MM/YYYY" => "d/m/Y");
//$CMS->vars['storename_domain'] = "fastboydemo.com";
$CMS->vars['is_http'] = "http://";
$CMS->vars['rsa_key'] = "whm-rsa-key";


//Zendesk Config

$CMS->vars['zdesk_subdomain'] = "fastboysupport";
$CMS->vars['zdesk_username'] = "lamdang@fastboy.net";
$CMS->vars['zdesk_token'] = "7SApywtVKHwb7DTlKk0q9XQn7S97c8pOgzbBIke2";

// PAYPAL
//Whether Sandbox environment is being used, Keep it true for testing
define("SANDBOX_FLAG", "sandbox"); // sandbox or live


