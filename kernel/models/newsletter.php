<?php

use \core\ezy;

$CMS->newsletter = new class_newsletter;

class class_newsletter {

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
	 * @param $newsletter_template
	 *		Use for quick send Email
	 */
	 
	public $newsletter_template = "";
	
	/**
	 * @param $newsletter_priority
	 *		Use for quick send Email
	 */
	 
	public $newsletter_priority = 3;
	
	/**
	 * @param $newsletter_from
	 *		Use for quick send Email
	 */
	 
	public $newsletter_from = "";
	
	/**
	 * @param $newsletter_fromname
	 *		Use for quick send Email
	 */
	 
	public $newsletter_fromname = "";
	
	/**
	 * @param $newsletter_to
	 *		Use for quick send Email
	 */
	 
	public $newsletter_to = "";
	
	/**
	 * @param $newsletter_cc
	 *		Use for quick send Email
	 */
	 
	var $newsletter_cc = "";
	
	/**
	 * @param $newsletter_bcc
	 *		Use for quick send Email
	 */
	 
	var $newsletter_bcc = "";
	
	/**
	 * @param $newsletter_toname
	 *		Use for quick send Email
	 */
	 
	public $newsletter_toname = "";
	
	/**
	 * @param $newsletter_header
	 *		Header Email (User Input)
	 */
	 
	public $newsletter_header = "";
	public $newsletter_header_text = "";
	
	/**
	 * @param $per_page
	 *		Records per page
	 */
	 
	public $per_page = 20;
	
	/**
	 * @param $show_page
	 *		Pages html
	 */
	 
	public $show_page = "";
	
	/**
	 * @param $group_time
	 *		Group Time
	 */
	 
	public $group_time = "";
	
	/**
	 * @param $sql_table
	 *		Additional SQL for table
	 */
	 
	public $sql_table = "";
	
	/**
	 * @param $logkey
	 *		Logkey to define what they are belong to
	 */
	 
	public $logkey = "";
	
	/**
	 * @param $js_id
	 *		Javascript element id for tabs
	 */
	 
	public $js_id = "";
	
	/**
	 * @param $delete_after_send
	 *		Delete after send newsletter
	 */
	 
	public $delete_after_send = 0;
	
	/**
	 * @param $ord_id
	 *		Order ID, use for promotion (Admin -> Config)
	 */
	 
	public $ord_id = 0;
	
	public $limit = 50000;
	
	public $get_list_limit = 0;

    /**
     * @var string $cache_prefix
     */
	public $cache_prefix = 'newsletter';
	
	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	
	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			// if ( $CMS->vars['is_admin_module'] )
			// {
			// 	$this->html = $CMS->class->template->load_simple("{$CMS->vars['mod_name']}/modules/newsletter/templates/skin_newsletter.php");
			// }
			// else
			// {
				$this->html = $CMS->class->template->load_template("skin_newsletter");
			// }
		}
	}
	
	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("newsletter_id,newsletter_title,newsletter_time,newsletter_status");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "newsletter_id";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "desc";
		
		// SQL Condition
		$this->sql_add .= " newsletter_deleted=0 AND ";
		
		// Get top newsletter
		$top = $DB->fetch_array($DB->query("SELECT * FROM ".root_table."newsletter WHERE newsletter_deleted = 0 ORDER BY newsletter_id desc LIMIT 1"));
		$limit = $this->get_list_limit == 1 ? 0 :$top['newsletter_id'] - $this->limit;

		$sql = "SELECT * FROM ".root_table."newsletter {$this->sql_table} WHERE {$this->sql_add} 1=1 ORDER BY {$default_field} {$default_order} ";

		list($this->show_page, $cacheData) = $DB->fetch_listing($sql, $this->per_page, $this->prefix_html, $this->suffix_html, $CMS->input['page'], $this->cache_prefix);

        return $cacheData;

	}

	public function html($data = [])
	{
		global $CMS, $DB, $member;

		$this->loadhtml();

		// Display Header
		$output .= $this->html->header();
		
		if ( $data )
		{
			foreach( $data as $result )
			{
				// Convert info
				$result = $this->convertvalue($result);
				
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
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=newsletter");
		}
		
		return $output;
	}
	
	//===========================================================================
	//  AUTO RUN
	//===========================================================================

	public function auto_run()
	{
		global $CMS, $DB, $member;

		$this->loadhtml();

		//-----------------------------------------------------------
		// ACTION CONTROLLER
		//-----------------------------------------------------------
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_newsletter_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_newsletter_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";
			
			$data .= "<option value=''>{$CMS->lang['select_action']}</option>";
			
			// Check permission to Delete
			if ( $CMS->permit["newsletter_delete"] == true )
			{
				$data .= "<option value='delete_all'>{$CMS->lang['newsletter_action_delete']}</option>";
				$this->control = 1;
			}

			$CMS->class->cache->save("user_{$member['user_id']}_newsletter_controller_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}
		
		if ( $CMS->vars['action_controller']  OR $CMS->permit["newsletter_search"] == 1 )
		{
			$this->action_control = $this->html->control();
		}
		
		//-----------------------------------------------------------
		// LOAD MODELS
		//-----------------------------------------------------------
		
		//$CMS->newslettertpl->data();
		
		$this->get_newsletter_header();
		
		$this->newsletter_header;
	}

	//===========================================================================
	//  DATA
	//===========================================================================

	public function defaultvalue($data)
	{
		global $CMS, $member;
		
		$data['newsletter_status'] = $data['newsletter_status'] ? $data['newsletter_status'] : 1;
		$data['newsletter_priority'] = $data['newsletter_priority'] ? $data['newsletter_priority'] : $this->newsletter_priority;
		$data['newsletter_from'] = $CMS->vars['smtp_user']; // $member['user_newsletter']
		$data['newsletter_fromname'] = $CMS->vars['smtp_user_display']; // trim($member['user_display_name'])
		$data['newsletter_is_send'] = $data['newsletter_is_send'] ? $data['newsletter_is_send'] : 1;
		
		$data['newsletter_header'] = $data['newsletter_header'] ? $data['newsletter_header'] : $CMS->newsletter->newsletter_header;
		
		// Check and Load Customer
		if ( $CMS->input['cus_id'] )
		{
			$customer = $CMS->customer->getInfo( intval($CMS->input['cus_id']) );
			
			if ( $customer['cus_newsletter'] )
			{
				$data['newsletter_to'] = $customer['cus_newsletter'];
				$data['newsletter_toname'] = trim($customer['cus_realname']);
			}
		}
		
		// Check and Load Template
		if ( $CMS->input['newslettertpl_id'] )
		{
			$template = $CMS->newslettertpl->get_info( $CMS->input['newslettertpl_id'] );
			
			$CMS->input['newslettertpl_id'] = $template['newslettertpl_id'];
			
			if ( $template['newslettertpl_title'] )
			{
				$data['newsletter_from'] = $template['newslettertpl_from'];
				$data['newsletter_fromname'] = $template['newslettertpl_fromname'];
				$data['newsletter_title'] = $CMS->newsletter->convert($template['newslettertpl_title']);
				$data['newsletter_content'] = $CMS->newsletter->convert($template['newslettertpl_content']);
			}
		}
		
		return $data;
	}
	
	public function convertvalue($data)
	{
		global $CMS, $DB;
	
		// Priority
		$data['newsletter_priority'] = $CMS->lang["newsletter_priority_{$data['newsletter_priority']}"];
				
		// Display
		$data['newsletter_status'] = $CMS->lang["newsletter_status_{$data['newsletter_status']}_color"];
	
		// Replace search content
		$data = $CMS->class->search->convertvalue($data);
		
		// Convert unix time to GMT time
		$data['newsletter_time_short'] = $CMS->class->date->date_format( $data['newsletter_time'], 0 );
		$data['newsletter_time'] = $CMS->class->date->date_format( $data['newsletter_time'], 1 );
	
		
		// Check permission to read Info
		if ( $CMS->permit["newsletter_read"] == true )
		{
			$data['newsletter_title'] = "<a href='{$CMS->vars['root_domain']}/?site=newsletter&act=show&id={$data['newsletter_id']}'>{$data['newsletter_title']}</a>";
		}

		// Bgcolor
		$data['bgcolor'] = $this->record_cnt % 2 != 0 ? "#FFFFFF" : "#F6F6F6";

		// Check permission to read Info
		if ( $data['cus_id'] )
		{
			$customer = $CMS->customer->getInfo($data['cus_id']);

			if ( $CMS->permit["customer_read"] == true )
			{
				$data['cus_id'] = "<a href='{$CMS->vars['root_domain']}/?site=customer&act=show&id={$customer['cus_id']}'>{$customer['cus_realname']}</a>";
			}
			else
			{
				$data['cus_id'] = "{$customer['cus_realname']}";
			}
		}
		else
		{
			$data['cus_id'] = $data['newsletter_to'];
		}
		
		// Check permission to read Info
		$user_name = $CMS->user->get_info($data['user_id'],"user_display_name");
		
		if ( $CMS->permit["user_read"] == true )
		{
			$data['user_id'] = "<a href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}'>{$user_name}</a>";
		}
		else
		{
			$data['user_id'] = $user_name;
		}

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
		$data['newsletter_time'] = $CMS->class->date->date_format( $data['newsletter_time'], 1 );

		// Check permission to read Info
		$customer = $CMS->customer->getInfo($data['cus_id']);
		$data['cus_id'] = $customer['cus_id'] ? "{$customer['cus_realname']}" : $data['cus_id'];
		
		if ( $CMS->permit["customer_read"] == true )
		{
			$data['cus_id'] = "<a href='{$CMS->vars['root_domain']}/?site=customer&act=show&id={$customer['cus_id']}'>{$data['cus_id']}</a>";
		}
		
		// Check permission to read Info
		$user_name = $CMS->user->get_info($data['user_id'],"user_display_name");
		$data['user_id'] = $user['user_id'] ? "{$user_name}" : $data['user_id'];
		
		if ( $CMS->permit["user_read"] == true )
		{
			$data['user_id'] = "<a href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}'>{$user_name}</a>";
		}
		
		// Priority
		$data['newsletter_priority'] = $CMS->lang["newsletter_priority_{$data['newsletter_priority']}"];
		
		// Display
		$data['newsletter_status'] = $CMS->lang["newsletter_status_{$data['newsletter_status']}"];
		
		return $data;
	}
	
	//===========================================================================
	//  INFO
	//===========================================================================
	
	public function get_info( $record_id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "newsletter" )
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
		
		// Fix low-query, LHL-02-08-2012
		if ( strlen($record_id) == 32 )
		{
			$sql = "newsletter_key='{$record_id}'";
		}
		else if ( $CMS->class->input->is_nan($record_id) )
		{
			$sql = "newsletter_title='{$record_id}'";
		}
		else
		{
			$sql = "newsletter_id='{$record_id}'";
		}
		// End fix

        $sql = "SELECT * FROM ".root_table."newsletter WHERE ({$sql}) AND newsletter_deleted=0 ORDER BY newsletter_id DESC LIMIT 1";

		$data = $DB->fetch_data($sql, $this->cache_prefix)[0];

        if($data)
        {
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

        return false;
	}
	
	//===========================================================================
	//  INFO (NEW) Tung
	//  hàm lấy thông tin,đầu vào truyền  
	//  record_id nếu k truyền record_id mặc định lấy input['id']
	//	arr_field_name k truyền thì mặc định lấy tất cả các field
	//===========================================================================
	
	public function get_info_v2( $record_id = 0, $field_name = "" )
	{
		
		global $CMS, $DB, $member;
		if ( !$record_id AND $CMS->input['site'] == "newsletter")
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
		$str_feild="";
		
		if($field_name){
			if(is_array($field_name)){//truyền vào dạng mảng
				/*
				foreach($field_name as $feld =>$feild_value){
					//validate tên field
					//end validate tên field
				}
				*/
				$str_feild=implode(",",$field_name);
			}else{//truyền vào dạng chuỗi
				$str_feild=$field_name;
			}
		}else{
			$str_feild="*";//nếu không truyền mặc định nhận tất cả các field
		}
		
		// Fix low-query, LHL-02-08-2012
		if ( strlen($record_id) == 32 )
		{
			$sql = "newsletter_key='{$record_id}'";
		}
		else if ( $CMS->class->input->is_nan($record_id) )
		{
			$sql = "newsletter_title='{$record_id}'";
		}
		else
		{
			$sql = "newsletter_id='{$record_id}'";
		}
		// End fix
		

		$sql = $DB->query("SELECT {$str_feild} FROM ".root_table."newsletter WHERE ({$sql}) AND newsletter_deleted=0 ORDER BY newsletter_id DESC LIMIT 1");
				
		if ( $DB->num_rows($sql) > 0 )
		{
			$data = $DB->fetch_array($sql);
			
			if(strpos($str_feild,",") || $str_feild=="*"){
				//nếu truy vấn lấy ra nhiều feild
				return $data;				
			}else{
				//nếu truy vấn lấy ra 1 feild
				return $data[$str_feild];
			}			
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

		$sql_add = '';

		if ( $except_value )
		{
            $sql_add .= "{$field}!='{$except_value}' AND";
		}

		$sql =  "SELECT count(newsletter_id) cnt FROM ".root_table."newsletter WHERE {$sql_add} {$field}='{$value}' AND newsletter_deleted=0";

        $result = $DB->fetch_data($sql, $this->cache_prefix);

        return $result[0]['cnt'];
	}

	//===========================================================================
	//  ADD
	//===========================================================================
	
	public function create_send_mail()
	{
		global $CMS, $DB, $member;

		// User input
		// print "<pre>"; print_r($CMS->input);exit;

		$send_all = intval($CMS->input['send_all']);
		$count_list = count($CMS->input['email_list']);
		if(!$send_all)
		{
			if($count_list == 0)
			{
				$_SESSION['error_msg'] = $CMS->lang['newsletter_incomplete_email'];
				return false;
			}else
			{
				$email_list = array_values($CMS->input['email_list']);// Chi co email
			}
		}else{
			$email_list = $this->getListEmail();
		}

		$email_title = $CMS->input['email_title'];
		$email_content = $CMS->class->editor->input('email_content');

		if(!$email_title) { $_SESSION['error_msg'] = $CMS->vars['newsletter_incomplete_name']; return false; }
		if(!$email_content) { $_SESSION['error_msg'] = $CMS->vars['newsletter_incomplete_content']; return false; }

		// config email
		$email_priority = 3; // Default is 3 means "normal"
		$user_id = intval($member['user_id']);
		$cus_id = 0;
		
		$email_header = "";//stripcslashes($_POST['email_header']);
		$email_from = $CMS->vars['smtp_email_display'];
		$email_fromname = $CMS->vars['smtp_user_display'];
		// Clear content
		// $email_content = $CMS->class->mail->clean_content($email_content);
		$data['email_title'] = $CMS->class->editor->rich_convert($email_title);
		$data['email_content'] = $CMS->class->editor->rich_convert($email_content);

		$email_time = time();
		$count = 0;
		foreach ($email_list as $email_to) 
		{
			
			$email_toname = $email_to = trim($email_to);
			// $email_toname = $CMS->input['email_toname'];
			
			// // Generate Email key
			$email_key = md5(time().$user_id.$email_to);
			
			// Generate Email private key, dùng để chặn gửi trùng email trong time nhất định - hvu 7.1.2015
			$email_private_key = md5($email_title.$email_to);
			
	  //       if($CMS->email->check_duplicate_email == 1)
	  //       {
	  //           if ( $CMS->email->check_duplicate_email($email_private_key) ) { $CMS->errormsg = "{$CMS->lang['email_duplicated']}"; return false; }
	  //       }
			
			// Check for email server id
			// $email_serverid = $this->check_email_server($email_to);

			// Insert data
			$check = $DB->query("INSERT INTO ".root_table."email (email_priority, user_id, cus_id, email_header, email_from, email_fromname, email_to, email_toname, email_title, email_content, email_key, email_time, email_private_key) VALUES ('{$email_priority}', '{$user_id}', '{$cus_id}', '{$email_header}', '{$email_from}', '{$email_fromname}', '{$email_to}', '{$email_toname}', '{$email_title}', '{$email_content}', '{$email_key}', '{$email_time}', '{$email_private_key}')");
			if($check)
			{
				$count ++;
			}
		}

		if($count > 0)
		{
			// Create log
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['newsletter_added_to_list_send_mail']}")."<br />";
		}else
		{
			$_SESSION["msg"] .= $CMS->lang['empty_list_email_send'];
		}

		return true;
	}
	
	public function quick_add( $newsletter_priority = 3, $cus_id = 0, $newsletter_from, $newsletter_fromname, $newsletter_to, $newsletter_toname, $newsletter_title, $newsletter_content, $newsletter_is_send, $newsletter_time, $newsletter_is_bulkmail, $newsletter_insert_log = 0, $newsletter_cc = "", $newsletter_bcc = "", $is_log = 1 )
	{
		global $CMS, $DB, $member;

		// Generate Email key
		$newsletter_key = md5(time().$cus_id.$newsletter_to);

		// User
		$user_id = isset($member['user_id']) ? $member['user_id'] : 0;
		
		// Clear content
		$newsletter_content = $CMS->class->mail->clean_content($newsletter_content);
		
		// Check for newsletter server id
		$newsletter_serverid = $this->check_newsletter_server($newsletter_to);
		
		// Insert data
		$DB->query("INSERT INTO ".root_table."newsletter (newsletter_priority, user_id, cus_id, newsletter_from, newsletter_fromname, newsletter_to, newsletter_toname, newsletter_title, newsletter_content, newsletter_key, newsletter_time, newsletter_is_bulkmail, newsletter_cc, newsletter_bcc, newsletter_logkey, newsletter_serverid) VALUES ('{$newsletter_priority}', '{$user_id}', '{$cus_id}', '{$newsletter_from}', '{$newsletter_fromname}', '{$newsletter_to}', '{$newsletter_toname}', '{$newsletter_title}', '{$newsletter_content}', '{$newsletter_key}', '{$newsletter_time}', '{$newsletter_is_bulkmail}', '{$newsletter_cc}', '{$newsletter_bcc}', '{$this->logkey}', '{$newsletter_serverid}')");

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

        // Create log
		if ( $newsletter_insert_log == 1 )
		{
			$CMS->class->logs->insert("{$CMS->lang['newsletter_added']} <b>{$newsletter_title}</b>", ($is_log == 1 ? "admin" : ""));
		}

		// Get info
		$newsletter = $this->get_info($newsletter_key);

		// Send Email
		if ( $newsletter_is_send == 1 )
		{
			$this->send($newsletter['newsletter_id']);
		}

		return $newsletter;
	}
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		// Get info
		$newsletter = $this->get_info();

		// Check for re-send mode
		if ( $CMS->input['is_resend'] == 1 )
		{
			$CMS->vars['newsletter_send_by_cron'] = 0;
			
			$_SESSION["msg"] .= $this->send($newsletter['newsletter_id']);
			
			return $newsletter;
		}

		// User input
		$newslettertpl_id = intval($CMS->input['newslettertpl_id']);
		$newsletter_priority = $CMS->input['newsletter_priority'];
		$newsletter_header = $CMS->input['newsletter_header'];
		$newsletter_from = $CMS->input['newsletter_from'];
		$newsletter_fromname = $CMS->input['newsletter_fromname'];
		$newsletter_to = $CMS->input['newsletter_to'];
		$newsletter_toname = $CMS->input['newsletter_toname'];
		$newsletter_title = $CMS->class->editor->input("newsletter_title");
		$newsletter_content = $CMS->class->editor->input("newsletter_content");
		$newsletter_is_send = $CMS->input['newsletter_is_send'];
		$newsletter_time_update = time();
		
		// CC
		$newsletter_cc = $CMS->input['newsletter_cc'];
		$newsletter_bcc = $CMS->input['newsletter_bcc'];

		// Get customer ID
		$cus_id = $CMS->customer->getInfo_v2($newsletter_to, "cus_id");
		$cus_id = $cus_id ? $cus_id : 0;

		// Convert Header
		$newsletter_header = unserialize($newsletter_header);
		$newsletter_header['newslettertpl_id'] = $newslettertpl_id;
		$newsletter_header['cus_id'] = $cus_id;
		$newsletter_header = serialize($newsletter_header);

		// Check input
		if ( ! $newsletter_title ) { $CMS->errormsg = "{$CMS->lang['newsletter_incomplete_name']}"; return false; }
		
		if ( ! $newsletter_content ) { $CMS->errormsg = "{$CMS->lang['newsletter_incomplete_content']}"; return false; }
		
		if ( ! $newsletter_from ) { $CMS->errormsg = "{$CMS->lang['newsletter_incomplete_from']}"; return false; }
		
		if ( ! $newsletter_to ) { $CMS->errormsg = "{$CMS->lang['newsletter_incomplete_to']}"; return false; }
		
		// Step 1: Save detail logs
		$CMS->class->logs->old_data = $newsletter;

		// Update info
		$DB->query("UPDATE ".root_table."newsletter SET newsletter_priority='{$newsletter_priority}', cus_id='{$cus_id}', newsletter_header='{$newsletter_header}', newsletter_from='{$newsletter_from}', newsletter_fromname='{$newsletter_fromname}', newsletter_to='{$newsletter_to}', newsletter_toname='{$newsletter_toname}', newsletter_cc='{$newsletter_cc}', newsletter_bcc='{$newsletter_bcc}', newsletter_title='{$newsletter_title}', newsletter_content='{$newsletter_content}', newsletter_time_update='{$newsletter_time_update}' WHERE newsletter_id='{$newsletter['newsletter_id']}'");

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		// Create log
		$CMS->class->logs->key = "newsletter_{$newsletter['newsletter_id']}";
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['newsletter_edited']} <b>{$newsletter_title}</b>")."<br />";
		
		// Get info
		$newsletter = $this->get_info();
		
		// Step 2: Save detail logs
		$CMS->class->logs->save_detail("newsletter",$newsletter['newsletter_id'],$newsletter);
		
		// Send Email
		if ( $newsletter_is_send == 1 )
		{
			$_SESSION["msg"] .= $this->send($newsletter['newsletter_id']);
		}

		return $newsletter;
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
		$data['newsletter_id']=intval($data['newsletter_id']);
		// Update info
		$DB->query("UPDATE ".root_table."newsletter SET newsletter_deleted=1 WHERE newsletter_id={$data['newsletter_id']}");

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['newsletter_deleted']} <b>{$data['newsletter_email']}</b>")."<br />";

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=newsletter&page={$CMS->input['page']}");
		
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

				$DB->query("UPDATE ".root_table."newsletter SET newsletter_deleted=1 WHERE newsletter_id={$id}");
				
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['newsletter_deleted']} <b>{$data['newsletter_email']}</b>")."<br />";
				
				$deleted = 1;
			}
		}
		
		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['newsletter_delete_failed']}";
		}

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		return true;
	}
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB;

		// Setup data
		$CMS->class->search->table_name = array("newsletter");
		$CMS->class->search->fields_type = array("newsletter_time" => "time");
		$CMS->class->search->search_type = 0;
		
		// Output
		$data = $CMS->class->search->get_info();
		$newsletter_email = $CMS->input['newsletter_email'];
		// Update SQL Query
		$this->sql_add .= " newsletter_email = '{$newsletter_email}' AND ";

		// Get List
		return $this->listing();
	}
	
	//===========================================================================
	//  Send Email
	//===========================================================================
	
	public function send( $newsletter_id )
	{
		global $CMS, $DB;
	
		$newsletter_id = intval($CMS->class->filter->md5_cleaner($newsletter_id));
		$sql_add = "";
		
		if ( $newsletter_id )
		{	
			$DB->query("SELECT * FROM ".root_table."newsletter WHERE newsletter_id='{$newsletter_id}'"); //  OR newsletter_key='{$newsletter_id}'
			$data = $DB->fetch_array();
			
			// Delete after send
			if ( $this->delete_after_send == 1 )
			{
				$CMS->vars['newsletter_send_by_cron'] = 0;
				$DB->query("DELETE FROM ".root_table."newsletter WHERE newsletter_id='{$data['newsletter_id']}'");
				//$sql_add = ", newsletter_auto_delete=1";
			}
			
			// Convert header
			$data = $this->convert_header($data);
			$data['newsletter_id']=intval($data['newsletter_id']);
			// Use for cron
			$data['newsletter_title'] = $CMS->class->editor->rich_convert($data['newsletter_title']);
			$data['newsletter_content'] = $CMS->class->editor->rich_convert($data['newsletter_content']);
			$DB->query("UPDATE ".root_table."newsletter SET newsletter_title='{$data['newsletter_title']}', newsletter_content='{$data['newsletter_content']}', newsletter_status=0 {$sql_add} WHERE newsletter_id='{$data['newsletter_id']}'");
			
			// Cron
			if ( $CMS->vars['newsletter_send_by_cron'] == 1 )
			{
				$text = "{$CMS->lang['newsletter_sent_ok']} <b>&lt;{$data['newsletter_to']}&gt;</b><br />";
				
				$CMS->class->cache->save("newsletter_updated", 1, 1);
			}
			else
			{
				
				//Insert Email Content When Send mail Plong 04/06/2013
				$newsletter_content = $this->send_newslettercontent($data);
				$data['newsletter_content'] = $newsletter_content['newsletter_content'];
				
				if ( $CMS->class->mail->sendmail($data['newsletter_to'], $data['newsletter_toname'], $data['newsletter_from'], $data['newsletter_fromname'], $data['newsletter_title'], $data['newsletter_content'], $data['newsletter_cc'], $data['newsletter_bcc']) )
				{
					$newsletter_id=intval($newsletter_id);
					$DB->query("UPDATE ".root_table."newsletter SET newsletter_status=1 WHERE newsletter_id={$newsletter_id}");
				
					$text = "{$CMS->lang['newsletter_sent_ok']} <b>{$data['newsletter_to']}</b><br />";
				}
				else
				{
					$DB->query("UPDATE ".root_table."newsletter SET newsletter_status=2 WHERE newsletter_id={$newsletter_id}");
					
					$text = "{$CMS->lang['newsletter_sent_failed']} <b>&lt;{$data['newsletter_to']}&gt;</b><br />";
				}
			}

            //Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			return $text;
		}
		else
		{
			$text = "{$CMS->lang['newsletter_incomplete_id']} (ID: <b>{$newsletter_id}</b>)<br />";
		
			return $text;
		}
	}
	
	//===========================================================================
	//  Convert Email content
	//===========================================================================
	
	public function merge( $module_name, $module_id, $command = "get_info" )
	{
		global $data;
		
		return $data;
	}
	
	public function convert( $text )
	{
		global $CMS, $DB, $member;
		// Language
		$CMS->class->language->load("customer");
		$CMS->class->language->load("order");
		$CMS->class->language->load("invoice");
		$CMS->class->language->load("contract");
		$CMS->class->language->load("server");
		$CMS->class->language->load("ip");
		$CMS->class->language->load("service");
		$CMS->class->language->load("payment");
		$CMS->class->language->load("transaction");
		$CMS->class->language->load("ticket");
		$CMS->class->language->load("comment");
		$CMS->class->language->load("cart");
		$CMS->class->language->load("serverhosting");
		$CMS->class->language->load("servercloud");
		$CMS->class->language->load("serverpackage");
		$CMS->class->language->load("domain");
		$CMS->class->language->load("web");
		
		// Backup permission
		$permit = $CMS->permit;
		unset($CMS->permit);
		
		$text = $CMS->class->editor->input( $text, "text" );
	
		$setting = $CMS->vars;

		$newarray = array();

		// Convert to array
		$setting = array_merge( $setting, $newarray );

		$cus_id = intval( $CMS->input['cus_id'] );
		$user_id = intval( $CMS->input['user_id'] );
		$ticket_id = intval( $CMS->input['ticket_id'] );
		$reply_id = intval( $CMS->input['reply_id'] );
		$task_id = intval( $CMS->input['task_id'] );
		
		$service_id = intval( $CMS->input['service_id'] );
		$sv_id = intval( $CMS->input['sv_id'] );
		$ip_id = intval( $CMS->input['ip_id'] );
		
		$ord_id = intval( $CMS->input['ord_id'] );
		$inv_id = intval( $CMS->input['inv_id'] );
		$contract_id = intval( $CMS->input['contract_id'] );
		
		$transaction_id = intval( $CMS->input['transaction_id'] );
		$pay_id = intval( $CMS->input['pay_id'] );

		$user_id = intval( $CMS->input['user_id'] );
		
		$comment_id = intval( $CMS->input['comment_id'] );
		
		$cart_id = intval( $CMS->input['cart_id'] );
		$cartw_id = intval( $CMS->input['cartw_id'] );
		$hosting_id = intval( $CMS->input['hosting_id'] );
		$domain_id = intval( $CMS->input['domain_id'] );
		$cloud_id = intval( $CMS->input['cloud_id'] );
		$web_id = intval( $CMS->input['web_id'] );
		$code_id = intval($CMS->input['code_id']);
		
		$log_id = intval($CMS->input['log_id']);

		// Convert to array
		$data = array();
		$data = array_merge( $data, $setting );
		
		//---------------------------------------------------------------
		// Main modules
		//---------------------------------------------------------------
		// Customer
		if ( $cus_id )
		{
			$temp = $CMS->customer->getInfo($cus_id);
			$temp = $CMS->customer->convertvalue($temp);
			
			// Convert to array
			$data = array_merge( $data, $temp );
		}
		
		// User
		if ( $user_id )
		{
			$user = array();
			$user['user_id'] = $user_id;
			$user['user_name'] = $CMS->user->get_display_name($user_id);
			$data = array_merge( $data, $user );	
		}
		
		// Ticket
		if ( $ticket_id )
		{
			$temp = $CMS->ticket->get_info($ticket_id);
			$temp = $CMS->ticket->convertvalue($temp);
			
			if ( ! $reply_id )
			{
				$temp['reply_id'] = $temp['ticket_id'];
				$temp['reply_content'] = $temp['ticket_content'];
			}
			
			// Convert to array
			$data = array_merge( $data, $temp );
		}
		
		// Reply
		if ( $reply_id )
		{
			$temp = $CMS->ticket->get_reply_info($reply_id);
			$temp = $CMS->ticket->convert_reply($temp);
			
			// Convert to array
			$data = array_merge( $data, $temp );
		}
		
		// Task
		if ( $task_id )
		{
			$temp = $CMS->task->get_info($task_id);
			$temp = $CMS->task->convertvalue($temp);
			
			// Convert to array
			$data = array_merge( $data, $temp );
		}
		// IP Address
		if ( $ip_id )
		{
			$temp = $CMS->ip->get_info($ip_id);
			$temp = $CMS->ip->convertvalue($temp);
			
			// Convert to array
			$data = array_merge( $data, $temp );
		}
		
		// Order
		if ( $ord_id )
		{
			$temp = $CMS->order->get_info($ord_id);
			
			// Create relation
			$inv_id = $inv_id ? $inv_id : $temp['inv_id'];
			$service_id = $service_id ? $service_id : $temp['service_id'];
			$sv_id = $sv_id ? $sv_id : $temp['sv_id'];
			$cloud_id = $temp['cloud_id'];
			
			$temp = $CMS->order->convertvalue($temp);
				
			// Convert to array
			$data = array_merge( $data, $temp );
		}
		
		// Service
		if ( $service_id )
		{
			$temp = $CMS->service->get_info($service_id);
			$temp = $CMS->service->convertvalue($temp);
			
			// Convert to array
			$data = array_merge( $data, $temp );
			
			if($member['user_id'] == 115)
			{
				//print_r($data);exit;	
			}
			unset($temp);
		}
		
		// Server
		if ( $sv_id )
		{
			$temp = $CMS->server->get_info($sv_id);
			$temp = $CMS->server->convertvalue($temp);
			
			// Convert to array
			$data = array_merge( $data, $temp );
		}
		
		// Invoice
		if ( $inv_id )
		{
			$temp = $CMS->invoice->get_info($inv_id);
			$temp = $CMS->invoice->convertvalue($temp);
			
			// Load orders
			$text = $this->load_order_list($text, $temp['inv_order_list']);
			
			// Remove VAT
			if ( $temp['inv_is_vat'] == 0 )
			{
				$text = preg_replace("#(.+?)<!--\[getvat\]-->(.+?)<!--\[endgetvat\]-->(.+?)#is", "\\1\\3", $text);
			}
			
			// Convert to array
			$data = array_merge( $data, $temp );
		}
		
		// Contract
		if ( $contract_id )
		{
			$temp = $CMS->contract->get_info($contract_id);
			
			$user_id = $user_id ? $user_id : $temp['user_id'];
			
			$temp = $CMS->contract->convertvalue($temp);
			
			// Load orders
			$text = $this->load_order_list($text, $temp['contract_order_list']);
			
			// Convert to array
			$data = array_merge( $data, $temp );
		}

		// Transaction
		if ( $transaction_id )
		{
			$temp = $CMS->transaction->get_info($transaction_id);
			$temp = $CMS->transaction->convertvalue($temp);
			
			// Convert to array
			$data = array_merge( $data, $temp );
		}

		// Payment
		if ( $pay_id )
		{
			$temp = $CMS->transactionout->get_info($pay_id);
			$temp = $CMS->transactionout->convertvalue($temp);
			
			// Convert to array
			$data = array_merge( $data, $temp );
		}
		
		// Comment
		if ( $comment_id )
		{
			$temp = $CMS->comment->get_info($comment_id);
			$temp = $CMS->comment->convertvalue($temp);

			// Convert to array
			$data = array_merge( $data, $temp );
		}
		// User
		if ( $user_id )
		{
			$temp = $CMS->user->get_info($user_id);
			//$temp = $CMS->user->convertvalue($temp);
			
			// Convert to array
			$data = array_merge( $data, $temp );
		}
		
		// Cart (Queued order)
		if ( $cart_id )
		{
			$temp = $CMS->cart->get_info($cart_id);
			$temp = $CMS->cart->convertvalue($temp);
			
			// Convert to array
			$data = array_merge( $data, $temp );
		}
		
		// Cart web (Queued order)
		if ( $cartw_id )
		{
			$temp = $CMS->cartweb->get_info($cartw_id);
			$temp = $CMS->cartweb->convertvalue($temp);
			
			// Convert to array
			$data = array_merge( $data, $temp );
		}
		
	
		// Hosting
		if ( $hosting_id )
		{
			$temp = $CMS->serverhosting->get_info($hosting_id);
			$temp = $CMS->serverhosting->convertvalue($temp);
			
			// Convert to array
			$data = array_merge( $data, $temp );
		}
				
		// Domain
		if ( $domain_id )
		{
			$temp = $CMS->domain->get_info($domain_id);
			$temp = $CMS->domain->convertvalue($temp);
			
			// Convert to array
			$data = array_merge( $data, $temp );
		}
		
		// Web
		if ( $web_id )
		{
			$temp = $CMS->web->get_info($web_id);
			$temp = $CMS->web->convertvalue($temp);
			
			// Convert to array
			$data = array_merge( $data, $temp );
		}
		
		// Cloud Server
		if ( $cloud_id )
		{
			$temp = $CMS->servercloud->get_info($cloud_id);
			$temp = $CMS->servercloud->convertvalue($temp);
		
			// Convert to array
			$data = array_merge( $data, $temp );	
					
		
		}
	
		// Promotion code
		if($code_id)
		{
			$temp = $CMS->coupon->get_info($code_id);
			$temp = $CMS->coupon->convertvalue($temp);
		
			// Convert to array
			$data = array_merge( $data, $temp );
		}
		
		// System logs
		if($log_id)
		{
			$temp = $CMS->class->logs->get_info($log_id);
			$temp = $CMS->class->logs->convertvalue($temp);	
			
			// Convert to array
			$data = array_merge( $data, $temp );
		}

		// Check for delegate
		if ( $data['cus_own_type_bk'] == 0 )
		{
			//$text = preg_replace('/\<\!--\[delegate\]--\>(.*)\<\!--\[enddelegate\]--\>/', '', $text);
			//$text = preg_replace("/\<!--\[delegate\]--\>(.+?)\<!--\[enddelegate\]--\>/i", "", $text);
			$text = preg_replace("#(.+?)<!--\[delegate\]-->(.+?)<!--\[enddelegate\]-->(.+?)#is", "\\1\\3", $text);
		}

		//---------------------------------------------------------------
		// Rebuild data
		//---------------------------------------------------------------
		$text = preg_replace("/\[([a-zA-Z0-9\_]+?)\]/i", "\$data[\\1]", $text);

		// Remove slash
		$text = str_replace("\"", "\\\"", $text);
		
		eval("\$text = \"$text\";");
		
		// Restore Permission
		$CMS->permit = $permit;
		
		return $text;
	}
	
	//===========================================================================
	//  LOAD ORDER LIST
	//===========================================================================
	
	public function load_order_list( $text, $list_order )
	{
		global $CMS, $DB;
		
		// Loop
		$html = explode("|", preg_replace("#(.+?)<!--\[startloop\]-->(.+?)<!--\[endloop\]-->(.+?)#is", "\\2|\\3", $text));
		$html = $html[0];
		$html = str_replace("\"", "", $html);
		$html = preg_replace("/\[([a-zA-Z0-9\_]+?)\]/i", "\$result[\\1]", $html);
		
		// Order list
		$ord_list = " ord_id=-1 ";
		$contract_order_list = explode("|", $list_order);
		for ( $i = 1; $i < count($contract_order_list)-1; $i++)
		{
			$ord_list .= " OR ord_id='{$contract_order_list[$i]}' ";
		}

		// Create list
		$sql = $DB->query("SELECT * FROM ".root_table."order WHERE ({$ord_list}) AND ord_status!=3 AND ord_status!=7 AND ord_deleted=0");

$loop = <<<EOF
	\$hehe = "";

	while ( \$result = \$DB->fetch_array( \$sql ) )
	{
		\$result = \$CMS->order->convertvalue(\$result);
		\$hehe .= "$html";
	}

EOF;
			
		eval($loop);

		$text = preg_replace("#(.+?)<!--\[startloop\]-->(.+?)<!--\[endloop\]-->(.+?)#is", "\\1{$hehe}\\3", $text);
			
		return $text;
	}
	
	//===========================================================================
	//  GATEWAY
	//===========================================================================
	
	public function quick_send( $is_log = 1 ) 
	{
		global $CMS, $DB, $member;
		
		// Load Language
		$CMS->class->language->load("newsletter");
		
		$template = $CMS->newslettertpl->get_info($this->newsletter_template);

		// Check for SMTP
		$this->newsletter_from = $CMS->class->smtp->smtp_user ? $CMS->class->smtp->smtp_user : $this->newsletter_from;
		$this->newsletter_fromname = $CMS->class->smtp->smtp_user ? $CMS->class->smtp->smtp_user : $this->newsletter_fromname;
		
		// Check for default
		$template['newslettertpl_from'] = $this->newsletter_from ? $this->newsletter_from : $template['newslettertpl_from'];
		
		
		$template['newslettertpl_fromname'] = $this->newsletter_fromname ? $this->newsletter_fromname : $template['newslettertpl_fromname'];
		
		// Check Domain use for task
		if ($CMS->vars['parent_domain'])
		{
			$domain = $CMS->vars['root_domain'];
			$CMS->vars['root_domain'] = $CMS->vars['parent_domain'];
		}
		// Add promotion text
		// Get newslettertpl for promotion
		if($CMS->vars['promotion_newslettertpl'])
		{
			$tpl = explode(",",$CMS->vars['promotion_newslettertpl']);	
		}
		
		if ( $this->ord_id && $tpl)
		{
			for($i=0;$i<count($tpl);$i++)
			{
				if($template['newslettertpl_code']==$tpl[$i])
				{
					$template['newslettertpl_content'] = $CMS->order->promotion_campaign($this->ord_id).$template['newslettertpl_content'];
				}
			}
			
			$this->ord_id = 0;
		}
		
		// Send newsletter
		$newsletter = $this->quick_add( $this->newsletter_priority, 0, $template['newslettertpl_from'], $template['newslettertpl_fromname'], $this->newsletter_to, ($this->newsletter_toname ? $this->newsletter_toname : $this->newsletter_to), $this->convert($template['newslettertpl_title']), $this->convert($template['newslettertpl_content']), 1, time(), 0, 1, $this->newsletter_cc, $this->newsletter_bcc, $is_log );
		
		if ( $domain )
		{
			$CMS->vars['root_domain'] = $domain;
		}
		
		return $newsletter;
	}
	
	//===========================================================================
	//  GET EMAIL HEADER (USER INPUT)
	//===========================================================================
	
	public function get_newsletter_header()
	{
		$url = explode("&", $_SERVER["REQUEST_URI"]);
		
		$data = array();
		
		for ( $i = 1; $i < count($url); $i++ )
		{
			$url2 = explode( "=", $url[$i] );
			
			if ( preg_match("/(\_id)/", $url2[0]) == true )
			{
				$data[$url2[0]] = $url2[1];
			}
		}

		$this->newsletter_header = serialize( $data );
	}
	
	//===========================================================================
	//  GET EMAIL HEADER (USER INPUT)
	//===========================================================================
	
	public function convert_header($data)
	{
		global $CMS, $DB;
		
		// Header
		if ( strlen($data['newsletter_header']) > 0 )
		{
			$newsletter_header = unserialize(stripslashes($data['newsletter_header']));
			$newsletter_header_key = array_keys($newsletter_header);
				
			// User Input
			for ( $i = 0; $i < count($newsletter_header); $i++ )
			{
				$CMS->input[$newsletter_header_key[$i]] = $newsletter_header[$newsletter_header_key[$i]];
			}
			
			$data['newsletter_title'] = $this->convert($data['newsletter_title']);
			$data['newsletter_content'] = $this->convert($data['newsletter_content']);
		}
			
		return $data;
	}
	
	//===========================================================================
	//  PREVIEW
	//===========================================================================

	public function preview()
	{
		global $CMS, $DB, $member;
	
		// Header
		$newsletter_header = unserialize($CMS->class->editor->input('newsletter_header'));
		$newsletter_header_key = array_keys($newsletter_header);
		
		// User Input
		for ( $i = 0; $i < count($newsletter_header); $i++ )
		{
			if ( $newsletter_header[$newsletter_header_key[$i]] AND ! $CMS->input[$newsletter_header_key[$i]] )
			{
				$CMS->input[$newsletter_header_key[$i]] = $newsletter_header[$newsletter_header_key[$i]];
			}
		}

		// Content
		$newsletter_title = $this->convert($CMS->class->editor->input('newsletter_title'));
		$newsletter_content = $this->convert($CMS->class->editor->input('newsletter_content'));

		// Output
		$output = $this->html->preview( "<b>{$newsletter_title}</b><br />{$newsletter_content}" );
		
		return $output;
	}
	
	//===========================================================================
	//  GET EMAIL LIST
	//===========================================================================
	
	public function get_newsletter_list( $data )
	{
		global $CMS, $DB;
		
		// Load language
		$CMS->class->language->load("newsletter");
	
		// Define Condition for Get List
		$CMS->newsletter->sql_add .= " ( ";
		
		for ( $i = 0; $i < count($data); $i++ )
		{
			if ( $i >= 1 )
			{
				$CMS->newsletter->sql_add .= " OR ";
			}
			
			if ( $data[$i] )
			{
				$CMS->newsletter->sql_add .= " newsletter_logkey='{$data[$i]}' ";
			}
		}
		
		$CMS->newsletter->sql_add .= " ) AND ";
		
		$this->get_list_limit = 1;
	
		// Get List
		$data = $CMS->newsletter->listing();
		
		// Write data
		return $CMS->newsletter->html($data);
	}
	
	public function check_newsletter_server($to)
	{
		global $CMS, $DB;

		$newsletter_to = explode("@",$to);
		$newsletter_to = $newsletter_to[1];
		
		if(strtolower(substr($newsletter_to,0,strlen($CMS->vars['smtp2_detect']))) != $CMS->vars['smtp2_detect'])
		{
			if($CMS->vars['smtp2_enable'])
			{
				return 1;
			}
		}
		return 0;
	}
	
	public function get_newslettercontent($newsletterc_id = '', $type = 0)
	{
		global $CMS, $DB;
		
		$sql = $DB->query("SELECT * FROM ".root_table."newsletter_content WHERE newsletterc_id = '{$newsletterc_id}' ORDER BY newsletterc_id DESC LIMIT 1");
		if($DB->num_rows($sql) > 0)
		{
			$data = $DB->fetch_array($sql);
			if($type == 1)
			{
				return $data['newsletterc_content'];
			}
			else
			{
				return $data;
			}
		}
		else
		{
			return false;
		}
	}
	
	//====================================================
	// Insert Email Content When Send mail Plong 04/06/2013
	//====================================================
	public function send_newslettercontent($newsletter)
	{
		global $CMS, $DB;
		
		$newsletter['newsletterc_id'] = intval($newsletter['newsletterc_id']);
		if($newsletter['newsletterc_id'] == 0)
		{  			
			// Insert newsletter content
			$DB->query("INSERT INTO ".root_table."newsletter_content (newsletterc_id, newsletterc_content) VALUES ('{$newsletter['newsletter_id']}', '{$newsletter['newsletter_content']}')" );
			// Update Emailc_id and newslettercontent
			$DB->query("UPDATE ".root_table."newsletter SET newsletterc_id={$newsletter['newsletter_id']} , newsletter_content = NULL WHERE newsletter_id={$newsletter['newsletter_id']}");

			//Clear cache
//            $CMS->class->cache->mdelete($this->cache_prefix);
		}
		else
		{
			$sql_cnt = $DB->query("SELECT * FROM ".root_table."newsletter_content WHERE newsletterc_id = '{$newsletter['newsletter_id']}' ORDER BY newsletterc_id DESC ");
			$newsletter2 = $DB->fetch_array($sql_cnt);
			$newsletter['newsletter_content'] = $newsletter['newsletter_content'];	
			
		}
		return $newsletter;
	}
	public function insert_newsletter($email)
	{
		global $CMS, $DB, $member;
		$ip_user = ezy::$ip_address;

		$email=strip_tags($email);
		$date=time();
		
		$DB->query("INSERT INTO ".root_table."newsletter (newsletter_email, newsletter_time,newsletter_ip) VALUES ('{$email}', '{$date}','{$ip_user}') ");

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);
	}

	function importNewsletterList()
	{
		global $CMS, $DB;

		// require file
		require_once root_path."vendor/autoload.php";
		// Input
		$file_tmp = isset($_FILES['upload_file']['tmp_name']) ? $_FILES['upload_file']['tmp_name'] : "";
		$file_name = isset($_FILES['upload_file']['name']) ? $_FILES['upload_file']['name'] : "";

		// Check file allow
		$file_ext = $CMS->class->attachment->get_ext( $file_name );
		$arr_allow = array("xls","xlsx");
		$count=0;
		if(!in_array($file_ext, $arr_allow))
		{
			$_SESSION['error_msg'] = $CMS->lang['error_ext_file_upload'];
			return false;
		}
		$newsletter_time = time();
		$ip_address = $_SERVER['REMOTE_ADDR'];	

		// Khoi tao
		$objPHPExcel = PHPExcel_IOFactory::load($file_tmp);
		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) 
		{
			// Get info from file
		    // $worksheetTitle     = $worksheet->getTitle();
		    $highestRow         = $worksheet->getHighestRow(); // e.g. 10
		    $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
		    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		    $nrColumns = ord($highestColumn) - 64;

		    // Loop row
		    for ($row = 1; $row <= $highestRow; ++ $row) 
		    {
		    	if($row == 1) { continue;}
		    	$data = [];

		    	// Loop col
		        for ($col = 0; $col < $highestColumnIndex; ++ $col) 
		        {
		            $cell = $worksheet->getCellByColumnAndRow($col, $row);
		            $data[] = $cell->getValue();
		           
		        }
// print "<pre>";
// print_r($data);exit;
		        // Input
		        $cus_email = $data[1];

		        // check email
		        if($CMS->class->input->is_email($cus_email))
		        {
		        	// Check exist
		        	$newsletter_id = $this->check_exist("newsletter_email", $cus_email);
		        }else
		        {
		        	continue;
		        }
		        
				
				// Check is overwrite
				if($newsletter_id)
				{
					if(intval($CMS->input['is_overwrite']))
					{
						// Update database
						$DB->query("UPDATE ".root_table."newsletter SET newsletter_email='{$cus_email}', newsletter_time='{$newsletter_time}' WHERE newsletter_id='{$newsletter_id}'");
						$count++;
					}else
					{
						continue;
					}
				}else
				{
			        // insert database
			        $DB->query("INSERT INTO ".root_table."newsletter (newsletter_email, newsletter_time, newsletter_ip) VALUES ('{$cus_email}', '{$newsletter_time}', '{$ip_address}')");
			        $count++;
		        }
		    }
		}

		if($count)
		{
            //Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$_SESSION['msg'] = $CMS->lang['import_file_success'];
			return true;
		}else
		{
			$_SESSION['error_msg'] = $CMS->lang['import_file_empty'];
			return false;
		}
	}


	function getListEmail()
	{
		global $CMS, $DB;

		$DB->query("SELECT * FROM ".root_table."newsletter WHERE newsletter_deleted=0");
		$output = [];
		while ($data = $DB->fetch_array()) 
		{
			$output[] = $data;
		}

		return $output;
	}
	
}

?>