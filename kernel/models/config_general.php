<?php

use lib\date;
use lib\input;

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->config_general = new class_config_general;

class class_config_general {

	public $CMS = "";
	
	/**
	 * @param @record_cnt
	 *		The order number of Data
	 */
	
	public $record_cnt = 0;
	
	/**
	 * @param @arrange_data
	 *		Arrange Data, using for re-order the listing
	 */
	
	public $arrange_data = "";
	
	/**
	 * @param @sql_query
	 *		The SQL Query for listing Data
	 */
	 
	public $sql_query = "";

	/**
	 * @param @sql_add
	 *		The additional SQL for $sql_query
	 */

	public $sql_add = "";
	
	/**
	 * @param $control
	 *		0 for no control, 1 for has control, DONT CHANGE the default value
	 */
	
	public $control = 0;
	
	/**
	 * @param $action_control
	 *		HTML action control
	 */
	
	public $action_control = "";

	/**
	 * @param $html
	 *		The templates
	 */
	 
	 public $html;

    /**
     * @var string $cache_prefix
     */
	 public $cache_prefix = 'conf_settings';

	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	
	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			$this->html = $CMS->class->template->load_template("skin_config_general");		
		}
	}
	
	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("pos_id,pos_name,pos_time,pos_status");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "pos_time";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "desc";
		
		// SQL Condition
		$this->sql_add .= " pos_deleted=0 AND ";	
		
		// Create SQL Query for listing Data
		// list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."config_general WHERE {$this->sql_add} 1=1 ORDER BY {$default_field} {$default_order}");
	}

	public function html()
	{
		global $CMS, $DB, $member;

		$this->loadhtml();

		// Display Header
		$output .= $this->html->header();
		
		if ( $DB->num_rows( $CMS->config_general->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->config_general->sql_query ) )
			{
				// Convert info
				$result = $CMS->config_general->convertvalue($result);
				
				// Display Middle
				$output .= $this->html->middle($result);
			}
		}
		else
		{
			// Display No data
			$output .= $this->html->none();

			// No data
			$CMS->is_error = 1;
		}
		
		// Display
		$output .= $this->html->footer();
		
		// If the page is giving no data
		if ( $CMS->input['page'] > 1 && $CMS->is_error == 1)
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_general");
		}
		
		return $output;
	}
	
	//===========================================================================
	//  AUTO RUN
	//===========================================================================

	public function auto_run()
	{
		global $CMS, $DB, $member;

		$this->setDefaultOpenHours();

		$this->loadhtml();

		//-----------------------------------------------------------
		// config_general
		//-----------------------------------------------------------
		
		// if ( $CMS->class->cache->check("banner_pos_{$CMS->vars['default_language']}") )
		// {
		// 	$CMS->vars['banner_config_general'] = $CMS->class->cache->load("banner_pos_{$CMS->vars['default_language']}");
		// }
		// else
		// {
		// 	$data = "";
			
		// 	$sql = $DB->query("SELECT * FROM ".root_table."config_general ORDER BY pos_id ASC");
			
		// 	while ( $config_general = $DB->fetch_array($sql) )
		// 	{
		// 		$config_general['pos_width'] = $config_general['pos_width'] ? $config_general['pos_width'] : "Unlimited";
		// 		$config_general['pos_height'] = $config_general['pos_height'] ? $config_general['pos_height'] : "Unlimited";
				
		// 		$data .= "<option value='{$config_general['pos_id']}'>{$config_general['pos_name']} - {$config_general['pos_width']} x {$config_general['pos_height']}</option>";
		// 	}
			
		// 	$CMS->class->cache->save("banner_pos_{$CMS->vars['default_language']}", $data);
		// 	$CMS->vars['banner_config_general'] = $data;
		// }

		// //-----------------------------------------------------------
		// // ACTION CONTROLLER
		// //-----------------------------------------------------------
		
		// if ( $CMS->class->cache->check("user_{$member['user_id']}_pos_controller_{$CMS->vars['default_language']}") )
		// {
		// 	$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_pos_controller_{$CMS->vars['default_language']}");
		// }
		// else
		// {
		// 	$data = "";
			
		// 	$data .= "<option value=''>{$CMS->lang['select_action']}</option>";
			
		// 	// Check permission to Delete
		// 	if ( $CMS->permit["pos_delete"] == true )
		// 	{
		// 		$data .= "<option value='delete_all'>{$CMS->lang['pos_action_delete']}</option>";
		// 		$this->control = 1;
		// 	}

		// 	$CMS->class->cache->save("user_{$member['user_id']}_pos_controller_{$CMS->vars['default_language']}", $data);
		// 	$CMS->vars['action_controller'] = $data;
		// }

		// if ( $CMS->vars['action_controller'] OR $CMS->permit["pos_search"] == 1 )
		// {
		// 	$this->action_control = $this->html->control();
		// }
	}

	//===========================================================================
	//  DATA
	//===========================================================================

	public function defaultvalue($data)
	{
		global $CMS;

		$data['pos_status'] = $data['pos_status'] ? $data['pos_status'] : 1;

		return $data;
	}
	
	public function convertvalue($data)
	{
		global $CMS, $DB;

		$data['data_bk'] = $data;

		// Replace search content
		$data = $CMS->class->search->convertvalue($data);
		
		// Convert unix time to GMT time
		$data['pos_time'] = $CMS->class->date->date_format( $data['pos_time'], 1 );

		// Check permission to read Info
		if ( $CMS->permit["pos_read"] == true )
		{
			$data['pos_name'] = "<a href='{$CMS->vars['root_domain']}/?site=config_general&act=show&id={$data['pos_id']}'>{$data['pos_name']}</a>";
		}


		// Bgcolor
		$data['bgcolor'] = $this->record_cnt % 2 != 0 ? "#FFFFFF" : "#F6F6F6";
		
		// User
		$data['user_id'] = $CMS->user->get_display_name($data['user_id']);
		
		$data['pos_status'] = $CMS->lang["display_{$data['pos_status']}"];

		// Count
		$data['record_cnt'] = $this->record_cnt;

		$this->record_cnt++;
		
		return $data;
	}
	
	public function editvalue($data)
	{
		global $CMS, $DB;

		return $data;
	}
	
	public function searchvalue($data)
	{
		global $CMS, $DB;

		// Convert unix time to GMT time
		$data['pos_time'] = $CMS->class->date->date_format( $data['pos_time'], 1 );

		// User
		$data['user_id'] = $CMS->user->get_display_name($data['user_id']);

		// Display
		$data['pos_status'] = $CMS->lang["answer_{$data['pos_status']}"];

		return $data;
	}
	
	//===========================================================================
	//  INFO
	//===========================================================================
	
	public function get_info( $record_id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "config_general" )
		{
			$record_id = intval($CMS->input['id']);
		}

		// Clear record
		$record_id = strip_tags($record_id);
		
		// Check record		
		if ( ! $record_id )
		{
			return false;
		}

		$sql = $DB->query("SELECT * FROM ".root_table."config_general WHERE (pos_id='{$record_id}' OR pos_name = '{$record_id}') AND pos_deleted=0 ORDER BY pos_id DESC LIMIT 1");

		if ( $DB->num_rows($sql) > 0 )
		{
			$data = $DB->fetch_array($sql);
		
			if ( $field_name )
			{
				if ( $data[$field_name] )
				{
					return $data[$field_name];
				}
				else
				{
					return false;
				}
			}

			return $data;
		}
		else
		{
			return false;
		}
	}
	
	
	
	public function check_exist( $field, $value = "", $except_value = "" )
	{
		global $CMS, $DB, $member;
		
		if ( ! $field )
		{
			return true;
		}
		
		if ( $except_value )
		{
		    $sql = "SELECT * FROM ".root_table."conf_settings WHERE {$field}='{$value}' AND {$field}!='{$except_value}'";
		}
		else
		{
            $sql = "SELECT * FROM ".root_table."conf_settings WHERE {$field}='{$value}'";
		}

		$sql = $DB->query($sql);

		if ( $DB->num_rows($sql) == 0 )
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	//===========================================================================
	//  ADD
	//===========================================================================
	
	public function add()
	{
		global $CMS, $DB, $member;

		// User input
		// print "<pre>";
		// print_r($CMS->input);exit;
		$pos_name = $CMS->input['pos_name'];
		$pos_status = intval($CMS->input['pos_status']);
		$user_id = $member['user_id'];
		$pos_time = time();

		// Check input
		if ( ! $pos_name ) { $CMS->errormsg = "{$CMS->lang['config_general_incomplete_name']}"; return false; }
	
		// Insert data
		$DB->query("INSERT INTO ".root_table."config_general (pos_name, pos_time, user_id, pos_status) VALUES ('{$pos_name}', '{$pos_time}', '{$user_id}', '{$pos_status}')");
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['config_general_added']} <b>{$pos_name}</b>")."<br />";

		// Get info
		$config_general = $this->get_info($pos_name);
		// print_r($config_general);exit;
		// Delete cache
		$CMS->class->cache->delete("config_general");
		$CMS->class->cache->mdelete("config_general");

		return $config_general;
	}
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;

		// Lấy thông tin trước khi thay đổi
		$conf_sql = $DB->query("SELECT * FROM ".root_table."conf_settings ORDER BY conf_key ASC");
		$conf_before = [];
		while($re_bk = $DB->fetch_assoc($conf_sql))
		{
			$conf_before[] = $re_bk;
		}

		// xoá cache
		$CMS->class->cache->deletesql("config");
		$CMS->input['config'] = $_POST['config'];



		// Defaultvalue 
		$CMS->input['config']['yes_no']['payment_active'] = isset($CMS->input['config']['yes_no']['payment_active']) ? intval($CMS->input['config']['yes_no']['payment_active']) : 0;
        $CMS->input['config']['yes_no']['nl_active'] = isset($CMS->input['config']['yes_no']['nl_active']) ? intval($CMS->input['config']['yes_no']['nl_active']) : 0;
        $CMS->input['config']['yes_no']['bk_active'] = isset($CMS->input['config']['yes_no']['bk_active']) ? intval($CMS->input['config']['yes_no']['bk_active']) : 0;
        $CMS->input['config']['yes_no']['ck_active'] = isset($CMS->input['config']['yes_no']['ck_active']) ? intval($CMS->input['config']['yes_no']['ck_active']) : 0;
        $CMS->input['config']['yes_no']['is_live'] = isset($CMS->input['config']['yes_no']['is_live']) ? intval($CMS->input['config']['yes_no']['is_live']) : 0;
        $CMS->input['config']['yes_no']['is_cache'] = isset($CMS->input['config']['yes_no']['is_cache']) ? intval($CMS->input['config']['yes_no']['is_cache']) : 0;
        $CMS->input['config']['yes_no']['negative_sale'] = isset($CMS->input['config']['yes_no']['negative_sale']) ? intval($CMS->input['config']['yes_no']['negative_sale']) : 0;
        $CMS->input['config']['yes_no']['enabled_commission'] = isset($CMS->input['config']['yes_no']['enabled_commission']) ? intval($CMS->input['config']['yes_no']['enabled_commission']) : 0;
        // Authorize
        $CMS->input['config']['yes_no']['authorize_active'] = isset($CMS->input['config']['yes_no']['authorize_active']) ? intval($CMS->input['config']['yes_no']['authorize_active']) : 0;
        $CMS->input['config']['yes_no']['authorize_is_live'] = isset($CMS->input['config']['yes_no']['authorize_is_live']) ? intval($CMS->input['config']['yes_no']['authorize_is_live']) : 0;


		// Check Info paypal
		if($CMS->input['config']['yes_no']['payment_active'] == 1 and (!$CMS->input['config']['input']['paypal_client_id'] or !$CMS->input['config']['input']['paypal_client_secret']))
		{
			$_SESSION['error_msg'] = $CMS->lang['title_error_payment_paypal']; 
			return false;
		}

		// Check Info Authorize
		if($CMS->input['config']['yes_no']['authorize_is_live'] == 1 and (!$CMS->input['config']['input']['authorize_login_id'] or !$CMS->input['config']['input']['authorize_transaction_key']))
		{
			$_SESSION['error_msg'] = $CMS->lang['title_error_payment_authorize']; 
			return false;
		}

        $CMS->input['config']['input']['translations'] = $CMS->input['config']['input']['translations'] ? $CMS->input['config']['input']['translations'] : [];
        $CMS->input['config']['input']['app_printing_bill'] = $CMS->input['config']['input']['app_printing_bill'] ? $CMS->input['config']['input']['app_printing_bill'] : [];

        $CMS->input['config']['yes_no']['sms_enabled'] = intval($CMS->input['config']['yes_no']['sms_enabled']);
        $CMS->input['config']['yes_no']['sms_subscription'] = intval($CMS->input['config']['yes_no']['sms_subscription']);
 
        if($CMS->input['config']['yes_no']['sms_subscription'] == 1)
        {
            if($CMS->input['config']['input']['sms_number'] == '')
            {
                $_SESSION['error_msg'] = $CMS->lang['title_error_sms_number'];
                return false;
            }
        }

        $CMS->input['config']['yes_no']['optimize_image'] = intval($CMS->input['config']['yes_no']['optimize_image']);

        $CMS->input['config']['yes_no']['discount_code'] = intval($CMS->input['config']['yes_no']['discount_code']);

		// insert bien trong config trước
		foreach ($CMS->input['config'] as $type => $arr_input) 
		{
			foreach ($arr_input as $key => $value) 
			{
				if($key == "booking_hours_morning" OR $key == "booking_hours_afternoon")
				{
					// Json encode multi selected
					$value = json_encode($value);
				}
				else if($key == 'translations') //Multi language
                {
                    $translations = [];
                    foreach ($value as $langCode)
                    {
                        $translations[$langCode] = $CMS->vars['default_language_data'][$langCode];
                    }
                    $value = @json_encode($translations, JSON_UNESCAPED_UNICODE);
                }
                else if($key == 'app_printing_bill')
                {
                    $value = @json_encode($value, JSON_UNESCAPED_UNICODE);
                }
                // filter var
                $value = $CMS->class->editor->input($value, "text");


				if($this->checkKey($key))
				{
					$DB->query("UPDATE ".root_table."conf_settings SET conf_value = '{$value}', conf_type = '{$type}' WHERE conf_key = '{$key}'");
				}else
				{
					$conf_title = $CMS->lang['title_'.$key];
					 
					$DB->query("INSERT INTO ".root_table."conf_settings (conf_title, conf_key, conf_value, conf_type, conf_group) VALUES ('{$conf_title}', '{$key}', '{$value}', '{$type}', 1)");
				}
			}
		}

		// Check domain
		if($CMS->input['check_domain'] == 1)
		{
			$website_domain = $CMS->input['free_domain'].$CMS->input['domain_free'];
		}elseif($CMS->input['check_domain'] == 2)
		{
			$website_domain = $CMS->input['subdomain'];
		}else
		{
			$website_domain = $CMS->vars['website_domain'];
		}

		if($website_domain)
		{
			if($this->checkKey('website_domain'))
			{
				$DB->query("UPDATE ".root_table."conf_settings SET conf_value = '{$website_domain}' WHERE conf_key = 'website_domain'");
			}else
			{
				$conf_title = $CMS->lang['title_website_domain'];
				$DB->query("INSERT INTO ".root_table."conf_settings (conf_title, conf_key, conf_value, conf_type, conf_group) VALUES ('{$conf_title}', 'website_domain', '{$website_domain}', 'input', 1)");
			}
			
		}

        //Openhours
        $openHours = $_POST['open_hours'];

        // LHL-2018-06-07: Fix invalid value foreach when $openHours is unset
        if ( isset($openHours) )
        {
            foreach ($openHours as $day => $openHour)
            {
                if(!$openHour['checked'])
                {
                    $openHours[$day]['checked'] = 0;
                }
                else
                {
                    $openHours[$day]['checked'] = 1;
                }
            }

            $openHours = @json_encode($openHours, JSON_UNESCAPED_UNICODE);

            if($this->checkKey('open_hours'))
            {
                $DB->query("UPDATE ".root_table."conf_settings SET conf_value = '{$openHours}' WHERE conf_key = 'open_hours'");
            }
            else
            {
                $conf_title = 'Open hours';
                $DB->query("INSERT INTO ".root_table."conf_settings (conf_title, conf_key, conf_value, conf_type, conf_group) VALUES ('{$conf_title}', 'open_hours', '{$openHours}', 'input', 1)");
            }
        }


        //Custom status
        $customStatus = $_POST['custom_status'] ? $_POST['custom_status'] : [["0","10","20","30"],["11","12","13","14","15"],["21","22","23"],["31","32","33","34","35"]];

        if ( isset($customStatus) )
        {
            $customStatus = @json_encode($customStatus, JSON_UNESCAPED_UNICODE);

            if($this->checkKey('custom_status'))
            {
                $DB->query("UPDATE ".root_table."conf_settings SET conf_value = '{$customStatus}' WHERE conf_key = 'custom_status'");
            }
            else
            {
                $conf_title = 'Custom status';
                $DB->query("INSERT INTO ".root_table."conf_settings (conf_title, conf_key, conf_value, conf_type, conf_group) VALUES ('{$conf_title}', 'custom_status', '{$customStatus}', 'input', 1)");
            }
        }

		//Upload hinh va insert bien check upload luon để duoi cung
		// Check upload
		if($_FILES['logo_website']['tmp_name'])
		{
			$file_tmp = isset($_FILES['logo_website']['tmp_name']) ? $_FILES['logo_website']['tmp_name'] : "";
			$file_name = isset($_FILES['logo_website']['name']) ? $_FILES['logo_website']['name'] : "";
			$file_type = isset($_FILES['logo_website']['type']) ? $_FILES['logo_website']['type'] : "";
			$file_size = isset($_FILES['logo_website']['size']) ? $_FILES['logo_website']['size'] : "";
			$file_error = isset($_FILES['logo_website']['error']) ? $_FILES['logo_website']['error'] : "";
			
			$file_ext = $CMS->class->attachment->get_ext( $file_name );

			// Check dung luong file upload
			$max = 5;
			$max_file_upload = 1024*1024*$max;
			if($file_size > $max_file_upload )
			{
				$_SESSION['error_msg'] = $CMS->lang['msg_maxfile_upload'].$max."MB";
				return false;
			}

            $file_name = $CMS->class->seo->cleanurl($file_name);
			$file_name = str_replace($file_ext, "", $file_name );
			$file_name = "{$file_name}.{$file_ext}";

			$file_location = strtolower(time()."_".$file_name);
			$product_image = "";
			if ( $file_name )
			{
				if ( $CMS->class->attachment->is_image($file_name, $file_ext) == false )
				{
					$_SESSION['error_msg'] = $CMS->lang['invalid_upload_file'];
					return false;
				}

				$CMS->class->image->check_folder_img("attach","",0);
				$check = @copy($file_tmp, "{$CMS->vars['upload_dir']}/attach/{$file_location}");
				if(!$check)
				{
					$_SESSION['error_msg'] = $CMS->lang['msg_error_upload'];
					return false;
				}
				
				$logo_website = $logo_image = $file_location;
			}else
			{
				$logo_website = $logo_image = $CMS->vars['logo_website'];
			}

			if($this->checkKey('logo_website'))
			{
				$DB->query("UPDATE ".root_table."conf_settings SET conf_value = '{$logo_image}' WHERE conf_key = 'logo_website'");
			}else
			{
				$conf_title = $CMS->lang['title_logo_website'];
				$DB->query("INSERT INTO ".root_table."conf_settings (conf_title, conf_key, conf_value, conf_type, conf_group) VALUES ('{$conf_title}', 'logo_website', '{$logo_image}', 'input', 1)");
			}
		} // End upload logo website
		else{ $logo_website =   $CMS->vars['logo_website']; }

        if($_FILES['logo_website_mobile']['tmp_name'])
        {
            $file_tmp = isset($_FILES['logo_website_mobile']['tmp_name']) ? $_FILES['logo_website_mobile']['tmp_name'] : "";
            $file_name = isset($_FILES['logo_website_mobile']['name']) ? $_FILES['logo_website_mobile']['name'] : "";
            $file_type = isset($_FILES['logo_website_mobile']['type']) ? $_FILES['logo_website_mobile']['type'] : "";
            $file_size = isset($_FILES['logo_website_mobile']['size']) ? $_FILES['logo_website_mobile']['size'] : "";
            $file_error = isset($_FILES['logo_website_mobile']['error']) ? $_FILES['logo_website_mobile']['error'] : "";

            $file_ext = $CMS->class->attachment->get_ext( $file_name );

            // Check dung luong file upload
            $max = 5;
            $max_file_upload = 1024*1024*$max;
            if($file_size > $max_file_upload )
            {
                $_SESSION['error_msg'] = $CMS->lang['msg_maxfile_upload'].$max."MB";
                return false;
            }


            $file_name = $CMS->class->seo->cleanurl($file_name);
            $file_name = str_replace($file_ext, "", $file_name );
            $file_name = "{$file_name}.{$file_ext}";

            $file_location = strtolower(time()."_".$file_name);
            $product_image = "";
            if ( $file_name )
            {
                if ( $CMS->class->attachment->is_image($file_name, $file_ext) == false )
                {
                    $_SESSION['error_msg'] = $CMS->lang['invalid_upload_file'];
                    return false;
                }

                $CMS->class->image->check_folder_img("attach","",0);
                $check = @copy($file_tmp, "{$CMS->vars['upload_dir']}/attach/{$file_location}");
                if(!$check)
                {
                    $_SESSION['error_msg'] = $CMS->lang['msg_error_upload'];
                    return false;
                }

                $logo_website_mobile = $logo_image = $file_location;
            }else
            {
                $logo_website_mobile = $logo_image = $CMS->vars['logo_website_mobile'];
            }

            if($this->checkKey('logo_website_mobile'))
            {
                $DB->query("UPDATE ".root_table."conf_settings SET conf_value = '{$logo_image}' WHERE conf_key = 'logo_website_mobile'");
            }else
            {
                $conf_title = $CMS->lang['label_logo_website_mobile'];
                $DB->query("INSERT INTO ".root_table."conf_settings (conf_title, conf_key, conf_value, conf_type, conf_group) VALUES ('{$conf_title}', 'logo_website_mobile', '{$logo_image}', 'input', 1)");
            }
        } // End upload logo website
        else{ $logo_website_mobile =   $CMS->vars['logo_website_mobile']; }

        if($_FILES['avatar_website']['tmp_name'])
		{
			$file_tmp = isset($_FILES['avatar_website']['tmp_name']) ? $_FILES['avatar_website']['tmp_name'] : "";
			$file_name = isset($_FILES['avatar_website']['name']) ? $_FILES['avatar_website']['name'] : "";
			$file_type = isset($_FILES['avatar_website']['type']) ? $_FILES['avatar_website']['type'] : "";
			$file_size = isset($_FILES['avatar_website']['size']) ? $_FILES['avatar_website']['size'] : "";
			$file_error = isset($_FILES['avatar_website']['error']) ? $_FILES['avatar_website']['error'] : "";
			
			$file_ext = $CMS->class->attachment->get_ext( $file_name );

			// Check dung luong file upload
			$max = 5;
			$max_file_upload = 1024*1024*$max;
			if($file_size > $max_file_upload )
			{
				$_SESSION['error_msg'] = $CMS->lang['msg_maxfile_upload'].$max."MB";
				return false;
			}

            $file_name = $CMS->class->seo->cleanurl($file_name);
			$file_name = str_replace($file_ext, "", $file_name );
			$file_name = "{$file_name}.{$file_ext}";

			$file_location = strtolower(time()."_".$file_name);
			$product_image = "";
			if ( $file_name )
			{
				if ( $CMS->class->attachment->is_image($file_name, $file_ext) == false )
				{
					$_SESSION['error_msg'] = $CMS->lang['invalid_upload_file'];
					return false;
				}

				$CMS->class->image->check_folder_img("attach","",0);
				$check = @copy($file_tmp, "{$CMS->vars['upload_dir']}/attach/{$file_location}");
				if(!$check)
				{
					$_SESSION['error_msg'] = $CMS->lang['msg_error_upload'];
					return false;
				}
				
				$avatar_website = $logo_image = $file_location;
			}else
			{
				$avatar_website = $logo_image = $CMS->vars['avatar_website'];
			}

			if($this->checkKey('avatar_website'))
			{
				$DB->query("UPDATE ".root_table."conf_settings SET conf_value = '{$logo_image}' WHERE conf_key = 'avatar_website'");
			}else
			{
				$conf_title = $CMS->lang['title_avatar_website'];
				$DB->query("INSERT INTO ".root_table."conf_settings (conf_title, conf_key, conf_value, conf_type, conf_group) VALUES ('{$conf_title}', 'avatar_website', '{$logo_image}', 'input', 1)");
			}
		} // End upload logo website
		else{ $avatar_website =   $CMS->vars['avatar_website']; }

        if($_FILES['logo_checkin']['tmp_name'])
        {
            $file_tmp = isset($_FILES['logo_checkin']['tmp_name']) ? $_FILES['logo_checkin']['tmp_name'] : "";
            $file_name = isset($_FILES['logo_checkin']['name']) ? $_FILES['logo_checkin']['name'] : "";
            $file_type = isset($_FILES['logo_checkin']['type']) ? $_FILES['logo_checkin']['type'] : "";
            $file_size = isset($_FILES['logo_checkin']['size']) ? $_FILES['logo_checkin']['size'] : "";
            $file_error = isset($_FILES['logo_checkin']['error']) ? $_FILES['logo_checkin']['error'] : "";

            $file_ext = $CMS->class->attachment->get_ext( $file_name );

            // Check dung luong file upload
            $max = 5;
            $max_file_upload = 1024*1024*$max;
            if($file_size > $max_file_upload )
            {
                $_SESSION['error_msg'] = $CMS->lang['msg_maxfile_upload'].$max."MB";
                return false;
            }

            $file_name = $CMS->class->seo->cleanurl($file_name);
            $file_name = str_replace($file_ext, "", $file_name );
            $file_name = "{$file_name}.{$file_ext}";

            $file_location = strtolower(time()."_".$file_name);
            $product_image = "";
            if ( $file_name )
            {
                if ( $CMS->class->attachment->is_image($file_name, $file_ext) == false )
                {
                    $_SESSION['error_msg'] = $CMS->lang['invalid_upload_file'];
                    return false;
                }

                $CMS->class->image->check_folder_img("attach","",0);
                $check = @copy($file_tmp, "{$CMS->vars['upload_dir']}/attach/{$file_location}");
                if(!$check)
                {
                    $_SESSION['error_msg'] = $CMS->lang['msg_error_upload'];
                    return false;
                }

                $logo_checkin = $logo_image = $file_location;
            }else
            {
                $logo_checkin = $logo_image = $CMS->vars['logo_checkin'];
            }

            if($this->checkKey('logo_checkin'))
            {
                $DB->query("UPDATE ".root_table."conf_settings SET conf_value = '{$logo_image}' WHERE conf_key = 'logo_checkin'");
            }else
            {
                $conf_title = "Logo checkin";
                $DB->query("INSERT INTO ".root_table."conf_settings (conf_title, conf_key, conf_value, conf_type, conf_group) VALUES ('{$conf_title}', 'logo_checkin', '{$logo_image}', 'input', 1)");
            }
        } // End upload logo website
        else{ $logo_checkin =   $CMS->vars['logo_checkin']; }


		 // Check key google auth file
		// Check upload
		if($_FILES['gg_analytics_account']['tmp_name'])
		{
            $file_gg_tmp = isset($_FILES['gg_analytics_account']['tmp_name']) ? $_FILES['gg_analytics_account']['tmp_name'] : "";
			$file_gg_name = isset($_FILES['gg_analytics_account']['name']) ? $_FILES['gg_analytics_account']['name'] : "";
			$file_gg_type = isset($_FILES['gg_analytics_account']['type']) ? $_FILES['gg_analytics_account']['type'] : "";
			$file_gg_size = isset($_FILES['gg_analytics_account']['size']) ? $_FILES['gg_analytics_account']['size'] : "";
			$file_gg_error = isset($_FILES['gg_analytics_account']['error']) ? $_FILES['gg_analytics_account']['error'] : "";
			$file_gg_ext = $CMS->class->attachment->get_ext( $file_gg_name );
 
	 
			if ( $file_gg_name )
			{
				if ( $file_gg_ext != "json")
				{
					$_SESSION['error_msg'] = $CMS->lang['invalid_upload_file'];
					return false;
				}

				$CMS->class->image->check_folder_ggauth();
				$root_folder = root_path."db/googleauth/".$file_gg_name;
				 
				$check = copy($file_gg_tmp, $root_folder);
 
				if(!$check)
				{
					$_SESSION['error_msg'] = $CMS->lang['msg_error_upload'];
					return false;
				}
				
				$file_gg_key = $file_gg_name;
			}else
			{
				$file_gg_key = $CMS->vars['gg_analytics_account'];
			}

			if($this->checkKey('gg_analytics_account'))
			{
				$DB->query("UPDATE ".root_table."conf_settings SET conf_value = '{$file_gg_key}' WHERE conf_key = 'gg_analytics_account'");
			}else
			{
				$conf_title = $CMS->lang['title_gg_analytics_account'];
				$DB->query("INSERT INTO ".root_table."conf_settings (conf_title, conf_key, conf_value, conf_type, conf_group) VALUES ('{$conf_title}', 'gg_analytics_account', '{$file_gg_key}', 'input', 1)");
			}

        }

		// Check upload
		if($_FILES['site_map']['tmp_name'])
		{
			$file_tmp = isset($_FILES['site_map']['tmp_name']) ? $_FILES['site_map']['tmp_name'] : "";
			$file_name = isset($_FILES['site_map']['name']) ? $_FILES['site_map']['name'] : "";
			$file_type = isset($_FILES['site_map']['type']) ? $_FILES['site_map']['type'] : "";
			$file_size = isset($_FILES['site_map']['size']) ? $_FILES['site_map']['size'] : "";
			$file_error = isset($_FILES['site_map']['error']) ? $_FILES['site_map']['error'] : "";

			$file_ext = $CMS->class->attachment->get_ext( $file_name );

			// Check dung luong file upload
			$max = 7;
			$max_file_upload = 1024*1024*$max;
			if($file_size > $max_file_upload )
			{
				$_SESSION['error_msg'] = $CMS->lang['msg_maxfile_upload'].$max."MB";
				return false;
			}


			$file_name = str_replace( " ", "_", $file_name );
//			$file_location = strtolower(time()."_".$file_name);
			$file_location = 'sitemap.xml';
			$product_image = "";
			if ( $file_name )
			{
				if ( $file_ext !== "xml")
				{
					$_SESSION['error_msg'] = $CMS->lang['invalid_upload_file'];
					return false;
				}

				$CMS->class->image->check_folder_img("sitemap","",0);
				$check = @copy($file_tmp, "{$CMS->vars['upload_dir']}/sitemap/{$file_location}");
				if(!$check)
				{
					$_SESSION['error_msg'] = $CMS->lang['msg_error_upload'];
					return false;
				}

				$sitemap_name = $file_location;
			}else
			{
				$sitemap_name = $file_location;
			}

			if($this->checkKey('sitemap'))
			{
				$DB->query("UPDATE ".root_table."conf_settings SET conf_value = '{$sitemap_name}' WHERE conf_key = 'sitemap'");
			}else
			{
				$conf_title = $CMS->lang['title_site_map'];
				$DB->query("INSERT INTO ".root_table."conf_settings (conf_title, conf_key, conf_value, conf_type, conf_group) VALUES ('{$conf_title}', 'sitemap', '{$sitemap_name}', 'input', 1)");
			}
		} // End upload logo website


        // Check upload favicon
        if($_FILES['favicon']['tmp_name'])
        {
            $file_tmp = isset($_FILES['favicon']['tmp_name']) ? $_FILES['favicon']['tmp_name'] : "";
            $file_name = isset($_FILES['favicon']['name']) ? $_FILES['favicon']['name'] : "";
            $file_type = isset($_FILES['favicon']['type']) ? $_FILES['favicon']['type'] : "";
            $file_size = isset($_FILES['favicon']['size']) ? $_FILES['favicon']['size'] : "";
            $file_error = isset($_FILES['favicon']['error']) ? $_FILES['favicon']['error'] : "";

            $file_ext = $CMS->class->attachment->get_ext( $file_name );

            // Check dung luong file upload
            $max = 0.5;
            $max_file_upload = 1024*1024*$max;
            if($file_size > $max_file_upload )
            {
                $_SESSION['error_msg'] = $CMS->lang['msg_maxfile_upload'].$max."MB";
                return false;
            }


            $file_name = str_replace( " ", "_", $file_name );
            $file_location = strtolower(time()."_".$file_name);

            if ( $file_name )
            {
                // if ( $file_ext !== "ico")
                // {
                //     $_SESSION['error_msg'] = $CMS->lang['invalid_upload_file'];
                //     return false;
                // }

                $CMS->class->image->check_folder_img("attach","",0);
                $check = @copy($file_tmp, "{$CMS->vars['upload_dir']}/attach/{$file_location}");
                if(!$check)
                {
                    $_SESSION['error_msg'] = $CMS->lang['msg_error_upload'];
                    return false;
                }
                if(!$check)
                {
                    $_SESSION['error_msg'] = $CMS->lang['msg_error_upload'];
                    return false;
                }

                $favicon = $file_location;

                @unlink("{$CMS->vars['upload_dir']}/attach/{$CMS->vars['favicon']}");
            }else
            {
                $favicon = $CMS->vars['favicon'];
            }

            if($this->checkKey('favicon'))
            {
                $DB->query("UPDATE ".root_table."conf_settings SET conf_value = '{$favicon}' WHERE conf_key = 'favicon'");
            }else
            {
                $conf_title = $CMS->lang['title_site_map'];
                $DB->query("INSERT INTO ".root_table."conf_settings (conf_title, conf_key, conf_value, conf_type, conf_group) VALUES ('{$conf_title}', 'favicon', '{$sitemap_name}', 'input', 1)");
            }
        } // End upload logo website


        // Check upload
        if($_FILES['og_image']['tmp_name'])
        {

            $file_tmp = isset($_FILES['og_image']['tmp_name']) ? $_FILES['og_image']['tmp_name'] : "";
            $file_name = isset($_FILES['og_image']['name']) ? $_FILES['og_image']['name'] : "";
            $file_type = isset($_FILES['og_image']['type']) ? $_FILES['og_image']['type'] : "";
            $file_size = isset($_FILES['og_image']['size']) ? $_FILES['og_image']['size'] : "";
            $file_error = isset($_FILES['og_image']['error']) ? $_FILES['logo_website']['error'] : "";

            $file_ext = $CMS->class->attachment->get_ext( $file_name );

            // Check dung luong file upload
            $max = 5;
            $max_file_upload = 1024*1024*$max;
            if($file_size > $max_file_upload )
            {
                $_SESSION['error_msg'] = $CMS->lang['msg_maxfile_upload'].$max."MB";
                return false;
            }


            $file_name = str_replace( " ", "_", $file_name );
            $file_location = strtolower(time()."_".$file_name);

            if ( $file_name )
            {
                if ( $CMS->class->attachment->is_image($file_name, $file_ext) == false )
                {
                    $_SESSION['error_msg'] = $CMS->lang['invalid_upload_file'];
                    return false;
                }

                $CMS->class->image->check_folder_img("attach","",0);
                $check = @copy($file_tmp, "{$CMS->vars['upload_dir']}/attach/{$file_location}");
                if(!$check)
                {
                    $_SESSION['error_msg'] = $CMS->lang['msg_error_upload'];
                    return false;
                }

                $og_image = $file_location;
            }else
            {
                $og_image = $CMS->vars['og_image'];
            }

            if($this->checkKey('og_image'))
            {
                $DB->query("UPDATE ".root_table."conf_settings SET conf_value = '{$og_image}' WHERE conf_key = 'og_image'");
            }else
            {
                $conf_title = "OG image";
                $DB->query("INSERT INTO ".root_table."conf_settings (conf_title, conf_key, conf_value, conf_type, conf_group) VALUES ('{$conf_title}', 'og_image', '{$og_image}', 'input', 1)");
            }
        } // End upload og_image

        //Check neu chuyen qua lai giua 2 dang cache thi clear het truoc khi chuyen qua - nkvp - 2017.11.03k
        if($CMS->input['config']['select']['cache_type'] != $CMS->vars['cache_type'])
        {
            $CMS->vars['cache_type'] = $CMS->input['config']['select']['cache_type'];
            $CMS->class->cache->mdelete('');
        }

        // Lấy thông tin sau khi thay đổi
		$conf_sql = $DB->query("SELECT * FROM ".root_table."conf_settings ORDER BY conf_key ASC");
		$conf_after = [];
		while($re_bk = $DB->fetch_assoc($conf_sql))
		{
			$conf_after[] = $re_bk;
		}

		$conf_new = [];
		// Check biến để lưu thông tin config
		foreach ($conf_before as $key => $value) 
		{

			if($value['conf_value'] != $conf_after[$key]['conf_value'] and $value['conf_key'] == $conf_after[$key]['conf_key'])
			{
				$conf_new[$value['conf_key']]['old'] = $value['conf_value'];
				$conf_new[$value['conf_key']]['new'] = $conf_after[$key]['conf_value'];
			}
		}

 

		   if($CMS->vars['web_free']== 1 AND intval($CMS->vars['ibe_synced']) == 1)
	       {
				//print_r ($CMS->input['config']);exit; 
	    	
	    		$data_api['logo_website'] = $logo_website;
				if($_FILES['logo_website']['tmp_name'])
				{
					$data_api['logo_website_link'] = "{$CMS->vars['upload_url']}/attach/{$logo_website}";
				}
				$data_api['logo_website_mobile'] = $logo_website_mobile;
				if($_FILES['logo_website_mobile']['tmp_name'])
				{
					$data_api['logo_website_mobile_link'] = "{$CMS->vars['upload_url']}/attach/{$logo_website_mobile}";
				}
  				$data_api['avatar_website'] = $avatar_website;
  				if($_FILES['avatar_website']['tmp_name'])
				{
					$data_api['avatar_website_link'] = "{$CMS->vars['upload_url']}/attach/{$avatar_website}";
				}

				$data_api['company_name'] = $CMS->input['config']['input']['company_name'];
				$data_api['company_address'] = $CMS->input['config']['input']['company_address'];
				$data_api['company_address2'] = $CMS->input['config']['input']['company_address2'];
				$data_api['company_email'] = $CMS->input['config']['input']['company_email'];
			
				$data_api['google_lat'] = $CMS->input['config']['input']['google_lat'];
				$data_api['google_lng'] = $CMS->input['config']['input']['google_lng'];

				$data_api['company_email2'] = $CMS->input['config']['input']['company_email2'];
				$data_api['google_maps_iframe'] = $CMS->input['config']['input']['google_maps_iframe'];
				  
				$data_api['company_email_cc'] = $CMS->input['config']['input']['company_email_cc'];
				$data_api['company_mobile'] = $CMS->input['config']['input']['company_mobile'];
				$data_api['company_phone'] = $CMS->input['config']['input']['company_phone'];
				$data_api['company_phone2'] = $CMS->input['config']['input']['company_phone2'];

				$data_api['company_fax'] = $CMS->input['config']['input']['company_fax'];
				$data_api['company_intro'] = $CMS->input['config']['textarea']['company_intro'];
				$data_api['booking_open_hours'] = intval($CMS->input['config']['yes_no']['booking_open_hours']);
				$data_api['openhours'] =  json_decode($openHours,true);
	    		$data_api['site_id']  = "{$CMS->vars['site_id']}";
	    		$CMS->api->whm->execute('config_general_sync', $data_api); 
	    	}



		// Lưu logs
		$CMS->class->logs->insert("You have changed info table conf_settings successful!", json_encode($conf_new, JSON_UNESCAPED_UNICODE));

        //del cache
        $DB->query("TRUNCATE ".root_table."cache");
 
		$_SESSION['msg'] = $CMS->lang['update_config_vars_success'];
		return true;

	}
	

	function checkKey($key="")
	{
		global $CMS, $DB;

		if($key)
		{
			$sql = $DB->query("SELECT 0 FROM ".root_table."conf_settings WHERE conf_key = '{$key}'");
			if($DB->num_rows($sql) > 0)
			{
				return true;
			}else
			{
				return false;
			}
		}
	}

	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB;
		
		// Get info
		$data = $this->get_info();
		
		// Check existing
		if ( ! $data ) { return false; }
		
		// Update info
		$DB->query("UPDATE ".root_table."config_general SET pos_deleted=1 WHERE pos_id={$data['pos_id']}");
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['config_general_deleted']} <b>{$data['pos_name']}</b>")."<br />";

		// Delete cache
		$CMS->class->cache->delete("config_general");
		$CMS->class->cache->mdelete("config_general");

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_general&page={$CMS->input['page']}");
		
		return true;
	}
	
	public function mdelete()
	{
		global $CMS, $DB;
		
		$deleted = 0;
		
		$_SESSION["msg"] .= "";
		
		for ( $i = 0; $i < intval( $CMS->input["data_cnt"] ); $i++ )
		{
			$id = intval( $CMS->input["id_{$i}"] );
				
			if ( $id )
			{
				$data = $this->get_info($id);

				$DB->query("UPDATE ".root_table."config_general SET pos_deleted=1 WHERE pos_id={$id}");
				
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['pos_deleted']} <b>{$data['pos_name']}</b>")."<br />";
				
				$deleted = 1;
			}
		}
		
		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['pos_delete_failed']}";
		}
		
		// Delete cache
		$CMS->class->cache->delete("config_general");
		$CMS->class->cache->mdelete("config_general");

		return true;
	}
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB;

		// Setup data
		$CMS->class->search->table_name = "config_general";
		$CMS->class->search->ignored_fields = array();
		$CMS->class->search->changed_fields = array();
		$CMS->class->search->fields_type = array("pos_time" => "time");
		$CMS->class->search->search_type = 0;
		
		// Output
		$data = $CMS->class->search->get_info();
		
		// Update SQL Query
		$this->sql_add .= $data;
		
		// Get List
		$this->listing();
	}
	
	//===========================================================================
	//  Load list
	//===========================================================================
	
	public function get_list( $pos_key = "", $type = 0 )
	{
		global $CMS, $DB;
		
		if ( ! $pos_key )
		{
			return false;
		}
		
		$sql = $DB->query("SELECT L.*, P.* FROM ".root_table."config_general AS L, ".root_table."config_general AS P WHERE P.pos_id=L.pos_id AND P.pos_key='{$pos_key}' AND L.pos_deleted=0");
		
		$output = "";
		
		while ( $data = $DB->fetch_array( $sql ) )
		{
			$data = $this->convertvalue($data);
		
			$output .= $data['pos_path']."<br />";
		}
		
		return $output;
	}
	
	//===========================================================================
	//  Load config_general list
	//===========================================================================
	public function getconfig_general()
	{
		global $CMS, $DB;
		
		$output = "<option value=''>{$CMS->lang['select_config_general']}</option>";
		
		$DB->query("SELECT * FROM ".root_table."config_general ORDER BY pos_name ASC");
		
		while($result = $DB->fetch_array())
		{
			$output .= "<option value='{$result['pos_id']}'>{$result['pos_name']}</option>";
		}
		return $output;
	}
	
	//===========================================================================
	//  Load config_general
	//===========================================================================
	public function load_config_general($pos_id)
	{
		global $CMS, $DB;
		
		$output = "";
		
		$DB->query("SELECT * FROM ".root_table."config_general WHERE pos_id = '{$pos_id}' AND pos_deleted = 0");
		
		while($result = $DB->fetch_array())
		{
			$result['pos_path'] = substr($result['pos_path'],11);
			
			$output .= "<a href=\"{$result['pos_website']}\"><img src=\"{$CMS->vars['upload_url']}/config_general/{$result['pos_path']}\"></a>";
		}
		return $output;
	}

	public function load_display_type_html()
	{
		global $CMS;

		$output = "";

		$data = array(0,1);

		foreach ($data as $v)
		{
			$output .= <<<EOF
			<option value="{$v}">{$CMS->lang['pos_display_type_'.$v]}</option>
EOF;

		}

		return $output;
	}


	public function option_hours_morning($selected_hours = "")
	{
		global $CMS;
		$selected_hours = json_decode($selected_hours,true);
		$array_hours = array("8:00","8:15","8:30","8:45","9:00","9:15","9:30","9:45","10:00","10:15","10:30","10:45","11:00","11:15","11:30","11:45");
		$output = "";
		 
		foreach ($array_hours as $key => $value) 
		{
			if (in_array($value, $selected_hours)) 
			{
				if($CMS->vars['hours_time_format'] == 12)
				{

					$new_value = $date_show = date("g:i A",strtotime($value));
					$output .= '<option value="'.$value.'" selected>'.$new_value.'</option> ';  
				}
				else
				{
					$output .= '<option value="'.$value.'" selected>'.$value.'</option> '; 
				}
			}
			else
			{
				if($CMS->vars['hours_time_format'] == 12)
				{
					$new_value = $date_show = date("g:i A",strtotime($value));
					$output .= '<option value="'.$value.'">'.$new_value.'</option> ';  
				}
				else
				{
					$output .= '<option value="'.$value.'">'.$value.'</option> '; 
				}
				 
			}
		}
		return $output;
	}

	public function option_hours_afternoon($selected_hours = "")
	{
		global $CMS;
		$selected_hours = json_decode($selected_hours,true);

		$array_hours = array("12:00","12:15","12:30","12:45","13:00","13:15","13:30","13:45","14:00","14:15","14:30","14:45","15:00","15:15","15:30","15:45","16:00","16:15","16:30","16:45","17:00","17:15","17:30","17:45","18:00","18:15","18:30","18:45","19:00","19:15","19:30","19:45","20:00","20:15","20:30","20:45","21:00","21:15","21:30","21:45","22:00","22:15","22:30","22:45","23:00","23:15","23:30","23:45");
		$output = "";
		foreach ($array_hours as $key => $value) {
			# code...
			
			if (in_array($value, $selected_hours)) {
				if($CMS->vars['hours_time_format'] == 12)
				{
					$new_value = $date_show = date("g:i A",strtotime($value));
					$output .= '<option value="'.$value.'" selected>'.$new_value.'</option> ';  
				}
				else
				{
					$output .= '<option value="'.$value.'" selected>'.$value.'</option> '; 
				}
			}
			else
			{
				if($CMS->vars['hours_time_format'] == 12)
				{
					$new_value = $date_show = date("g:i A",strtotime($value));
					$output .= '<option value="'.$value.'">'.$new_value.'</option> ';  
				}
				else
				{
					$output .= '<option value="'.$value.'">'.$value.'</option> '; 
				}
			}
		}
		return $output;
	}

    /**
     * Export to excel file
     * @return string
     */
    public function exportToExcel()
    {
        global $CMS, $DB, $member;

        $setTitle = [
            'Title',
            'Key',
            'Type',
            'Protected',
            'Value',
            'Value(s) for select options'
        ];

        \models\report::excel_header();

        // Set style
        $style = array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation'   => 0,
            'wrap'       => TRUE
        );

        /**
         * Fields to export
         * Note: muste by match with header cols (order and numbers)
         */
        $fields = 'conf_title,conf_key,conf_type,conf_protected,conf_value,conf_data';

        // Query data
        $sql = "SELECT {$fields} FROM ".root_table."conf_settings ORDER BY conf_id DESC";

        $sql = $DB->query($sql);

        $count_row = $DB->num_rows($sql) + 1;// + 1 row title

        // Set style excel
        \models\report::excel_title($setTitle, $count_row, $style);

        // Loop data
        $i = 2;

        //Range char A-Z
        $rangeChar = range('A', 'Z') ;
        $fields = explode(',', $fields);

        while ($result = $DB->fetch_assoc($sql))
        {
            $result['conf_protected'] = $result['conf_protected'] ? 'Yes' : 'No';

            foreach ($rangeChar as $charKey => $char)
            {
                /**
                 * Set values to cell by chars(A-Z) and fields from database
                 */
                $field = $fields[$charKey];
                $val = $result[$field];

                if($CMS->class->attachment->is_image($val))
                {
                    $path = "{$CMS->vars['upload_dir']}/attach/{$val}";

                    if(is_file($path))
                    {
                        $objDrawing = new \PHPExcel_Worksheet_Drawing();
                        $objDrawing->setPath($path);
                        $objDrawing->setCoordinates($char.$i);
                        $objDrawing->setWorksheet(\models\report::$dataExcel->getActiveSheet());
                        $objDrawing->setHeight(120);

                        \models\report::$dataExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(120);
                        \models\report::$dataExcel->getActiveSheet()->getColumnDimension($char)->setAutoSize(false);
                        \models\report::$dataExcel->getActiveSheet()->getColumnDimension($char)->setWidth("50"); //$colWidth
                    }
                    else
                    {
                        \models\report::$dataExcel->getActiveSheet()->setCellValue($char.$i, $val);
                    }
                }
                else
                {
                    $val = html_entity_decode($val);
                    \models\report::$dataExcel->getActiveSheet()->setCellValue($char.$i, $val);
                }
            };

            $i++;
        }

        // Set name file
        $file_name = "config_general_u{$member['user_id']}.xls";

        // Create file and return link download
        return \models\report::excel_output($file_name);
    }

    /**
     * Import data from excel file
     * @return bool
     */
    function importFromExcel()
    {
        global $CMS, $DB, $member;

        // require file
        require_once root_path."vendor/autoload.php";

        //Clear cache
        $CMS->class->cache->deletesql('config');

        //Range char A-Z
        $rangeChar = range('A', 'Z') ;

        // Input
        $file_tmp = isset($_FILES['upload_file']['tmp_name']) ? $_FILES['upload_file']['tmp_name'] : "";
        $file_name = isset($_FILES['upload_file']['name']) ? $_FILES['upload_file']['name'] : "";

        // Check file allow
        $file_ext = $CMS->class->attachment->get_ext( $file_name );
        $arr_allow = array("xls","xlsx");

        if(!in_array($file_ext, $arr_allow))
        {
            $_SESSION['error_msg'] = $CMS->lang['error_ext_file_upload'];
            return false;
        }

        // Khoi tao
        $objPHPExcel = \PHPExcel_IOFactory::load($file_tmp);

        $highestColumn = $objPHPExcel->getActiveSheet()->getHighestColumn(); //Cột cuối cùng
        $highestRow         = $objPHPExcel->getActiveSheet()->getHighestRow(); // e.g. 10

        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);

        //Reset style
        $styleDefault = array(
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_NONE,
            ),
            'font'  => array(
                'bold'  => false,
                'color' => array('rgb' => '000000'))
        );

        $objPHPExcel->getActiveSheet()->getStyle("A2:{$highestColumn}{$highestRow}")->applyFromArray($styleDefault);

        //Danh sách thứ tự các field tương ứng với thứ tự cột từ file excel
        $fields_text = 'conf_title,conf_key,conf_type,conf_protected,conf_value,conf_data';
        $fields = explode(',', $fields_text);

        $errStyleCell = array(
//            'borders' => array(
//                'allborders' => array(
//                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
//                    'color' => array('rgb' => 'FF0000')
//                )
//            ),
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'E0E0E0')
            )
        ); //Style đánh dấu ô có lỗi

        $errStyleRow = array(
            'font'  => array(
//                'bold'  => true,
                'color' => array('rgb' => 'FF0000'),
            )); //Style đánh dấu dòng có lỗi

        $sql_values = ""; //values for multi insert

        $validData = []; //Mang chua cac dong hop le

        /**
         * Fields is required
         */
        $checkRequired = ['conf_key'];

        /**
         * Marked error position
         */
        $errorPositions = [];

        /**
         * Tpl key list to check existed
         */
        $tplKeyList = [];

        /**
         * Loop to check validate
         */
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
        {
            // Loop row
            for ($row = 1; $row <= $highestRow; ++ $row)
            {
                if($row == 1) { continue;}
                $data = [];

                // Loop col
                for ($col = 0; $col < $highestColumnIndex; ++$col)
                {
                    $cell = $worksheet->getCellByColumnAndRow($col, $row);

                    if($fields[$col])
                    {
                        $data[$fields[$col]] = $CMS->class->editor->input($cell->getValue(), "text"); //Gan du lieu lai theo giong field trong DB

                        /**
                         * Check required
                         */
                        if(in_array($fields[$col], $checkRequired))
                        {
                            if($data[$fields[$col]] == '')
                            {
                                $errorPositions[$row][] = $col;
                            }
                        }

                        if($fields[$col] == 'conf_key')
                        {
                            if($data[$fields[$col]]!='')
                            {
                                $tplKeyList[$data[$fields[$col]]][] = ['col' => $col, 'row' => $row];
                            }
                        }
                    }
                }

                if(!isset($errorPositions[$row]))
                {
                    $validData[$row] = $data;
                }
            }
        }

        //Duyet danh sach key de check trung
        foreach ($tplKeyList as $tplCode => $errorPos)
        {

            /*if(!$CMS->input['is_overwrite'])
            {
                if(count($errorPos)>1 || $this->checkExist('conf_key', $tplCode))
                {
                    foreach ($errorPos as $errColRows)
                    {
                        $errorPositions[$errColRows['row']][] = $errColRows['col'];
                    }
                }
            }*/

            //Xóa bỏ các dòng trung key, chỉ giữ lại dòng cuối
            if(($cnt = count($errorPos)) >= 2)
            {
                for ($i=0; $i<$cnt-1; $i++)
                {
                    unset($validData[$errorPos[$i]['row']]);
                }
            }
        }

        if($errorPositions)
        {
            foreach ($errorPositions as $errRow => $errCols)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension();

                //Đánh dấu dòng lỗi
                $objPHPExcel->getActiveSheet()->getStyle("A{$errRow}:{$highestColumn}{$errRow}")->applyFromArray($errStyleRow);

                foreach ($errCols as $errCol)
                {
                    //Đánh dấu các ô bị lỗi
                    $colName = $rangeChar[$errCol];
                    $objPHPExcel->getActiveSheet()->getStyle("{$colName}{$errRow}")->applyFromArray($errStyleCell);
                }
            }

            /**
             * Trả về file highlight các dòng bị lỡi cho khách sửa lại
             */
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $tmpFile = "import_config_general_C{$member['user_id']}.xls";
            $objWriter->save("{$CMS->vars['upload_dir']}/excel/{$tmpFile}");
            $_SESSION['error_msg'] = "{$CMS->lang['invalid_import_data']} <a href='{$CMS->vars['upload_url']}/excel/{$tmpFile}'>Download file</a>";
            $CMS->global->redirectReferer();
        }

        /**
         * get images from file
         */
        foreach ($objPHPExcel->getActiveSheet()->getDrawingCollection() as $drawing) {
            if ($drawing instanceof \PHPExcel_Worksheet_MemoryDrawing) {
                ob_start();
                call_user_func(
                    $drawing->getRenderingFunction(),
                    $drawing->getImageResource()
                );

                $imageContents = ob_get_contents();
                ob_end_clean();
                $extension = 'jpg';

                switch ($drawing->getMimeType()) {
                    case \PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG :
                        $extension = 'png'; break;
                    case \PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_GIF:
                        $extension = 'gif'; break;
                    case \PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_JPEG :
                        $extension = 'jpg'; break;
                }
            } else {
                $zipReader = fopen($drawing->getPath(),'r');
                $imageContents = '';

                while (!feof($zipReader)) {
                    $imageContents .= fread($zipReader,1024);
                }
                fclose($zipReader);
                $extension = $drawing->getExtension();
            }

            //Lưu tam source hinh va vi tri cua cell chua hinh lai
            $fileImages[$drawing->getCoordinates()]['src'] = $imageContents;
            $fileImages[$drawing->getCoordinates()]['ext'] = $extension;
        }

        /**
         * IGNORED KEYS
         */
        $ignoredKeys = ['logo_website','og_image'];

        /**
         * Loop dữ liệu để vào DB
         */
        foreach ($validData as $row => $data)
        {
            $time = time();
            $random = rand(0,10000);

            //UPLOAD IMAGE
            $imageColName =  $rangeChar[array_search('conf_value', $fields)];
            $fileToUpload = $fileImages["{$imageColName}{$row}"];

            if($fileToUpload)
            {
                $fileName = "{$time}_{$random}_".$CMS->class->seo->cleanurl("{$data['conf_key']}").".{$fileToUpload['ext']}";

                @file_put_contents("{$CMS->vars['upload_dir']}/attach/{$fileName}", $fileToUpload['src']);

                $data['conf_value'] = $fileName;
            }

            $data['conf_protected'] = strtolower($data['conf_protected']) == 'yes' ? 1 : 0;

            //Check exist
            if($oldData = $this->check_exist('conf_key', $data['conf_key']))
            {
                /**
                 * Multi update
                 */
                $sql_update = "UPDATE ".root_table."conf_settings SET ";

                foreach ($data as $field => $value)
                {
                    if(!in_array($data['conf_key'], $ignoredKeys) || $field != 'conf_value')
                    {
                        //Create values sql
                        $sql_update .= "{$field} = '{$value}',";
                    }
                }

                $sql_update = trim($sql_update,',');

                $sql_update .= " WHERE conf_key='{$data['conf_key']}' ";

                $DB->query($sql_update);
            }
            else
            {
                /**
                 * Multi insert
                 */
                $sql_values .= "(";
                foreach ($data as $field => $value)
                {
                    if(!in_array($data['conf_key'], $ignoredKeys) || $field != 'conf_value')
                    {
                        //Create values sql
                        $sql_values .= "'{$value}',";
                    }
                    else
                    {
                        $sql_values .= "'',";
                    }
                }
                $sql_values = trim($sql_values,',');
                $sql_values .= "),";
            }
        }

        $sql_values = trim($sql_values,',');

        /**
         * Lay danh sach field de insert
         */
        $field_list = array_keys($data);
        $field_list = implode(',',$field_list);

        if($sql_values)
        {
            $sql = "INSERT INTO ".root_table."conf_settings ({$field_list}) VALUES {$sql_values}";

            $DB->query($sql);
        }

        $_SESSION['msg'] = $CMS->lang['import_file_success'];

        $CMS->global->redirectReferer();

    }

    public function setDefaultOpenHours()
    {
        global $CMS, $DB;


        if(!$this->checkKey('open_hours'))
        {
            $conf_value =  '{"monday":{"checked":1,"open":"10:00 am","close":"8:00 pm"},"tuesday":{"checked":1,"open":"10:00 am","close":"8:00 pm"},"wednesday":{"checked":1,"open":"10:00 am","close":"8:00 pm"},"thursday":{"checked":1,"open":"10:00 am","close":"8:00 pm"},"friday":{"checked":1,"open":"10:00 am","close":"8:00 pm"},"saturday":{"open":"10:00 am","close":"7:00 pm","checked":0},"sunday":{"open":"11:00 am","close":"5:00 pm","checked":0}}';

            $CMS->vars['open_hours'] = $conf_value;
        }
    }

    public function updateTimezoneId()
    {
        global $CMS, $DB;

       if($CMS->vars['timezone_id']) return true;

       //get old timezone offset
        $sql_get_old = "SELECT conf_value FROM ".root_table."conf_settings WHERE conf_key='timezone'";
        $sql_get_old =  $DB->query($sql_get_old);
        $old_data = $sql_get_old->fetch_assoc();
        $old_data['conf_value'] = intval($old_data['conf_value']);

       if($old_data['conf_value'] == 7)
        {
            $timezone_id = 'Asia/Ho_Chi_Minh';
        }
        else if($old_data['conf_value'] == -7)
        {
            $timezone_id = 'America/Los_Angeles';
        }
        else if($old_data['conf_value'] == -5)
        {
            $timezone_id = 'America/Chicago';
        }
        else if($old_data['conf_value'] == -6)
        {
            $timezone_id = 'America/Denver';
        }
        else if($old_data['conf_value'] == -4)
        {
            $timezone_id = 'America/New_York';
        }
        else
        {
            $timezone = date::getTimezoneByOffet($old_data['conf_value']);
            $timezone_id = $timezone['timezone_id'];
        }

       $timezone_id = $timezone_id ? $timezone_id : date_default_timezone_get();

       //insert
        $sql = "INSERT INTO ".root_table."conf_settings (conf_title, conf_key, conf_value, conf_type, conf_group) VALUES ('Timezone', 'timezone_id', '{$timezone_id}', 'select', 1)";

       $DB->query($sql);

       //clear cache
        $sql_cache = "TRUNCATE ".root_table."cache";

       $DB->query($sql_cache);

   }


   function del_favicon()
   {
   		global $CMS, $DB;

   		// Lấy thông tin trước khi thay đổi
		$conf_sql = $DB->query("SELECT * FROM ".root_table."conf_settings ORDER BY conf_key ASC");
		$conf_before = [];
		while($re_bk = $DB->fetch_assoc($conf_sql))
		{
			$conf_before[] = $re_bk;
		}
 
		// xoá cache
		$CMS->class->cache->deletesql("config");

		// Update thông tin favicon
		$DB->query("UPDATE ".root_table."conf_settings SET conf_value='' WHERE conf_key='favicon'");
		// Unlink image
		@unlink("{$CMS->vars['upload_dir']}/attach/{$CMS->vars['favicon']}");

		// Lấy thông tin sau khi thay đổi
		$conf_sql = $DB->query("SELECT * FROM ".root_table."conf_settings ORDER BY conf_key ASC");
		$conf_after = [];
		while($re_bk = $DB->fetch_assoc($conf_sql))
		{
			$conf_after[] = $re_bk;
		}

		// Check biến để lưu thông tin config
		foreach ($conf_before as $key => $value) 
		{

			if($value['conf_value'] != $conf_after[$key]['conf_value'] and $value['conf_key'] == $conf_after[$key]['conf_key'])
			{
				$conf_new[$value['conf_key']]['old'] = $value['conf_value'];
				$conf_new[$value['conf_key']]['new'] = $conf_after[$key]['conf_value'];
			}
		}

		// Lưu logs
		$CMS->class->logs->insert("You have changed info table conf_settings successful!", json_encode($conf_new, JSON_UNESCAPED_UNICODE));

        //del cache
        $DB->query("TRUNCATE ".root_table."cache");
   }

    /**
     * Copy from web/models/application.php
     * @return array
     */
    function getOpenhours(){
        global $CMS;

        $openHours = @json_decode($CMS->vars['open_hours'], true);

        $openHoursShort = [];

        // Use for openHoursShort
        $previousDay = "";
        $previousHour = "";

        // Check data
        if($openHours)
        {
            foreach ($openHours as $dayOpen => $openHour)
            {
                // Check for openHourShort
                $openHourStr = $openHour['checked'] ? $openHour['open'].$openHour['close'] : "closed";

                if ( $previousHour == $openHourStr )
                {
                    // Remove duplicate openHourStr
                    unset($openHoursShort[$previousDay]);

                    // Set new day with syntax: Monday-Tuesday
                    // $dayOpen = explode(" - ", $previousDay)[0]." - ".ucfirst($dayOpen);
                    $dayOpen = ucfirst(substr(explode(" - ", $previousDay)[0], 0, 3))." - ".ucfirst(substr($dayOpen, 0, 3));
                }
                // else
                // {
                //     $dayOpen = substr($dayOpen,0,3);
                // }

                // Set time for this Day
                $openHoursShort[$dayOpen] = $openHour;

                // Save current Day
                $previousDay = $dayOpen;
                $previousHour = $openHourStr;
            }

            return [$openHours,$openHoursShort];
        }
        else
        {
            return [null,null];
        }
    }

    function getOpenhoursHtml()
    {
        $data = $this->getOpenhours();
        $openHours = $data[0];
        $openHoursShort = $data[1];
        $openHoursHtml = "";
        $openHoursShortHtml = "";

        foreach($openHours as $day => $detail)
        {
            $openHoursHtml .= "<li><strong>".ucfirst($day).": </strong>" . ($detail['checked'] ? "{$detail['open']} - {$detail['close']}" : "closed") . "</li>";
        }

        foreach($openHoursShort as $day => $detail)
        {
            $openHoursShortHtml .= "<li><strong>".ucfirst($day).": </strong>" . ($detail['checked'] ? "{$detail['open']} - {$detail['close']}" : "closed") . "</li>";
        }

        $openHoursHtml = !empty($openHoursHtml) ? "<ul>{$openHoursHtml}</ul>" : "";
        $openHoursShortHtml = !empty($openHoursShortHtml) ? "<ul>{$openHoursShortHtml}</ul>" : "";

        return [$openHoursHtml,$openHoursShortHtml];
    }
    function  update_config_general($key='',$value){
        global $CMS,$DB;
        if(empty($key)){return false;}

        $sql = $DB->query("SELECT conf_value FROM ".root_table."conf_settings  WHERE  conf_key = '{$key}' ORDER BY conf_id DESC LIMIT 1");
        if($DB->num_rows($sql) > 0)
        {
            $info = $DB->fetch_array($sql);
            if($info['conf_value']!=$value) {
                $DB->query("UPDATE " . root_table . "conf_settings SET conf_value = '{$value}' WHERE conf_key = '{$key}'");
                //del cache
                $DB->query("TRUNCATE " . root_table . "cache");
                $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['config_general_update_row']} <b>{$key}:</b>{$info['conf_value']}=>{$value}") . "<br />";
            }
        }


    }

    /**
    * Update shipping default
    */
    function  updateShippingDefault( $data=[] )
    {
        global $CMS, $DB;

        // Inputs
        $default_shiping_key = ['default_shiping_location', 'default_shiping_free'];
        $log_content = [];
        $updated = 0;

        // insert bien trong config trước
		foreach ($CMS->input['config'] as $type => $arr_input) 
		{
			foreach ($arr_input as $key => $value) 
			{

				if( in_array($key, $default_shiping_key) )
				{
					if( $this->checkKey($key) )
					{
						$sql_update = "
						UPDATE ".root_table."conf_settings 
						SET conf_value = '{$value}', conf_type = '{$type}' 
						WHERE conf_key = '{$key}'
						";
						$query_update = $DB->query($sql_update);
					}
					else
					{
						$conf_title = $CMS->lang['title_'.$key];
						$sql_add = "
						INSERT INTO ".root_table."conf_settings 
						(conf_title, conf_key, conf_value, conf_type, conf_group) 
						VALUES ('{$conf_title}', '{$key}', '{$value}', '{$type}', 1)
						";
						$query_add = $DB->query($sql_add);
					}

					// Logs
					if( !isset($CMS->vars[$key]) OR $CMS->vars[$key] != $value )
					{
						$log_content[$key] = $CMS->vars[$key];
						$log_content[$key.'_new'] = $value;
					}

					$updated = 1;
				}
			}
		}

		$log_content = $log_content ? serialize($log_content) : '';
		if( !$updated )
		{
			$_SESSION["error_msg"] .= $CMS->lang['default_shiping_update_failure']."<br />";
			return false;
		}

		// Delete cache
		$CMS->class->cache->delete("config_general");
		$CMS->class->cache->deletesql("config");
		
		// Create log
		$CMS->class->logs->key = 'default_shiping';
		$_SESSION["msg"] .= $CMS->class->logs->insert($CMS->lang['default_shiping_update_success'], $log_content)."<br />";

		return true;
    }

}

?>