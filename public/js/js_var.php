<?php
//===========================================================================
// INITIALIZE DATA
//===========================================================================

define("in_app", true);

require_once("../../init.inc.php");

//Load language
$CMS->class->language->auto_run();
$CMS->class->date->auto_run();

header("Content-type: text/javascript;  charset=utf-8");
$data = <<<EOF

	var site_is_login = "{$CMS->vars['is_login']}";
	var lang_alert_delete = "{$CMS->lang['alert_delete']}";
	var lang_alert_empty = "{$CMS->lang['alert_empty']}";
	var lang_required_field = "{$CMS->lang['required_field']}";
	var lang_error_comment = "An error occurred during the comment!";
	var lang_invalid_email = "Please enter a valid email address!";
	var lang_invalid_email_format = "Email is malformed!";
	var lang_invalid_phone = "Please enter a valid phone number";
	var lang_invalid_fax = "Please enter a valid fax number";
	var lang_invalid_fullname = "Please enter your name!";
	var lang_invalid_content = "Please enter the text comment!";
	var lang_limit_time_comment = "You may not post multiple comments in a short time!";
	var lang_comment_success = "Your comment has been successfully posted! Comments are pending approval stage."; 
	var lang_create_random_password = "{$CMS->lang['create_random_password']}";
	var lang_close = "{$CMS->lang['close']}";
	var site_language = "{$CMS->vars['default_language']}";
	var site_currency = "{$CMS->vars['currency_type']}";
	var lang_login_require = "{$CMS->lang['login_require']}";
	var allow_customer = "{$CMS->vars['allow_customer']}";
	var lang_validate_free_subdomain = "{$CMS->lang['validate_free_subdomain']}";
	
	var lang_loading = "{$CMS->lang['loading']}";
	var lang_adding = "{$CMS->lang['loading']}";
	var title_check_color = "{$CMS->lang['title_check_color']}";
	var processing_fee = "{$CMS->vars['processing_fee']}";

	var SUCCESS="{$CMS->lang['success']}";
	var ERROR="{$CMS->lang['error']}";
	
	var lang_title_header_notify = "{$CMS->lang['lang_title_header_notify']}";
	var lang_loading = "Loading...";
	var lang_no_result = "No data";
	var filemanager_access_key = "{$_SESSION['RF']['access_key']}";
	var once = "{$CMS->lang['cycle_once']}";
	var monthly = "{$CMS->lang['cycle_monthly']}";
	var yearly = "{$CMS->lang['cycle_yearly']}";

EOF;
print $CMS->class->security->compress($CMS->class->input->trimall($data));

?>