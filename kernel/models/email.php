<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->email = new class_email;

class class_email {
	public $CMS = "";
	
	/**
	 * @param $send_email
	 *		true->send, false->not
	 */
	public $send_email = true;
	
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
	 * @param $email_template
	 *		Use for quick send Email
	 */
	 
	public $email_template = "";
	
	/**
	 * @param $email_priority
	 *		Use for quick send Email
	 */
	 
	public $email_priority = 3;
	
	/**
	 * @param $email_from
	 *		Use for quick send Email
	 */
	 
	public $email_from = "";
	
	/**
	 * @param $email_fromname
	 *		Use for quick send Email
	 */
	 
	public $email_fromname = "";
	
	/**
	 * @param $email_to
	 *		Use for quick send Email
	 */
	 
	public $email_to = "";
	
	/**
	 * @param $email_cc
	 *		Use for quick send Email
	 */
	 
	var $email_cc = "";
	
	/**
	 * @param $email_bcc
	 *		Use for quick send Email
	 */
	 
	var $email_bcc = "";
	
	/**
	 * @param $email_toname
	 *		Use for quick send Email
	 */
	 
	public $email_toname = "";
	
	/**
	 * @param $email_header
	 *		Header Email (User Input)
	 */
	 
	public $email_header = "";
	public $email_header_text = "";
	public $email_attachment = "";

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
	 *		Delete after send email
	 */
	 
	public $delete_after_send = 0;
	
	/**
	 * @param $ord_id
	 *		Order ID, use for promotion (Admin -> Config)
	 */
	 
	public $ord_id = 0;
	
	public $task_id = 0;
	
	public $task_send_without_cron = 0;
	
	public $limit = 50000;
	
	public $get_list_limit = 0;
	
	public $data = array();  
    public $check_duplicate_email = 1;  
	
	public $is_action_suspend = 0;
	public $shopify_order_id = "";

	public $priority_html;

	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	
	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			if ( $CMS->vars['is_admin_module'] )
			{
				$this->html = $CMS->class->template->load_simple(\core\ezy::$app_dir."/modules/email/templates/skin_email.php");
			}
			else
			{
				$this->html = $CMS->class->template->load_template("skin_email");
			}
		}
	}
	
	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("email_id,email_title,email_time,email_status");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "email_id";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "desc";
		
		// SQL Condition
		$this->sql_add .= " email_deleted=0 AND ";

        if( ($email_to = trim(urldecode($CMS->input['email_to'])) ) !='' )
        {
            $this->sql_add .= " email_to='{$email_to}' AND ";
        }
		
		// Get top email
		$top = $DB->fetch_array($DB->query("SELECT * FROM ".root_table."email WHERE email_deleted = 0 ORDER BY email_id desc LIMIT 1"));
		$limit = $this->get_list_limit == 1 ? 0 :$top['email_id'] - $this->limit;

		// Create SQL Query for listing Data
		list($this->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."email {$this->sql_table} WHERE {$this->sql_add} 1=1 AND email_id >= {$limit} ORDER BY {$default_field} {$default_order}, {$default_field} {$default_order} ", $this->per_page);
	}

	public function html()
	{
		global $CMS, $DB, $member;

		$this->loadhtml();

		// Display Header
		$output .= $this->html->header();
		
		$count = $DB->num_rows( $this->sql_query );
		if ( $count > 0 )
		{
			$cnt = 1;
			while( $result = $DB->fetch_array( $this->sql_query ) )
			{
				// Convert info
				$result = $this->convertvalue($result);
				$result['keyrow'] = $cnt;
				$result['rowtr'] = $cnt == $count ? 'last-row' : '';
				$result['old-even'] = $cnt % 2 == 0? 'even' : 'old';
				$cnt += 1;

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
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=email");
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

        $data = "";
        $data .= "<option value='1'>{$CMS->lang['priority_1']}</option>";
        $data .= "<option value='2'>{$CMS->lang['priority_2']}</option>";
        $data .= "<option value='3'>{$CMS->lang['priority_3']}</option>";
        $data .= "<option value='4'>{$CMS->lang['priority_4']}</option>";
        $data .= "<option value='5'>{$CMS->lang['priority_5']}</option>";
        $this->priority_html = $data;

		//-----------------------------------------------------------
		// ACTION CONTROLLER
		//-----------------------------------------------------------
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_email_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_email_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";
			
			$data .= "<option value=''>{$CMS->lang['select_action']}</option>";
			
			// Check permission to Delete
			if ( $CMS->permit["email_delete"] == true )
			{
				$data .= "<option value='delete_all'>{$CMS->lang['email_action_delete']}</option>";
				$this->control = 1;
			}

			$CMS->class->cache->save("user_{$member['user_id']}_email_controller_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}
		
		if ( $CMS->vars['action_controller']  OR $CMS->permit["email_search"] == 1 )
		{
			$this->action_control = $this->html->control();
		}
		
		//-----------------------------------------------------------
		// LOAD MODELS
		//-----------------------------------------------------------
		
		$CMS->emailtpl->data();
		
		$this->get_email_header();
		
		$this->email_header;
	}

	//===========================================================================
	//  DATA
	//===========================================================================

	public function defaultvalue($data)
	{
		global $CMS, $member;
		
		$data['email_status'] = $data['email_status'] ? $data['email_status'] : 1;
		$data['email_priority'] = $data['email_priority'] ? $data['email_priority'] : $this->email_priority;
		$data['email_from'] = $CMS->vars['smtp_email_display']; // $member['user_email']
		$data['email_fromname'] = $CMS->vars['smtp_user_display']; // trim($member['user_display_name'])
		$data['email_is_send'] = $data['email_is_send'] ? $data['email_is_send'] : 1;
		
		$data['email_header'] = $data['email_header'] ? $data['email_header'] : $CMS->email->email_header;
		
		// Check and Load Customer
		if ( $CMS->input['cus_id'] )
		{
			$customer = $CMS->customer->getInfo( intval($CMS->input['cus_id']) );
			
			if ( $customer['cus_email'] )
			{
				$data['email_to'] = $customer['cus_email'];
				$data['email_toname'] = trim($customer['cus_realname']);
			}
		}
		
		// Check and Load Template
		if ( $CMS->input['emailtpl_id'] )
		{
			$template = $CMS->emailtpl->get_info( $CMS->input['emailtpl_id'] );
			
			$CMS->input['emailtpl_id'] = $template['emailtpl_id'];
			
			if ( $template['emailtpl_title'] )
			{
				$data['email_from'] = $template['emailtpl_from'];
				$data['email_fromname'] = $template['emailtpl_fromname'];
				
				list($temp,$text) = $CMS->email->convert_v1($template['emailtpl_title']);
				
				$data['email_title'] = $CMS->email->convert_v2($template['emailtpl_title'],$temp);
				$data['email_content'] = $CMS->email->convert_v2($template['emailtpl_content'],$temp);
			}
		}
		
		return $data;
	}
	
	public function convertvalue($data)
	{
		global $CMS, $DB, $member;
	
		// Priority
		$data['email_priority'] = $CMS->lang["email_priority_{$data['email_priority']}"];
				
		// Display
		$data['email_status'] = $CMS->lang["email_status_{$data['email_status']}_color"];
	
		// Replace search content
		$data = $CMS->class->search->convertvalue($data);
		
		// Convert unix time to GMT time
		$data['email_time_short'] = $CMS->class->date->date_format( $data['email_time'], 0 );
		$data['email_time'] = $CMS->class->date->date_format( $data['email_time'], 1 );
	
		
		// Check permission to read Info
		if ( $CMS->permit["email_read"] == true )
		{ 
			$data['email_title'] = "<a title='{$data['email_title']}' href='{$CMS->vars['root_domain']}/?site=email&act=show&id={$data['email_id']}'>{$data['email_title']}</a>";
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
			$data['cus_id'] = $data['email_to'];
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
		$data['email_time'] = $CMS->class->date->date_format( $data['email_time'], 1 );

		// Check permission to read Info
		// $customer = $CMS->customer->getInfo($data['cus_id']);
		$customer = $CMS->customer->getInfo($data['cus_id']);
		$data['cus_id'] = $customer['cus_id'] ? "{$customer['cus_realname']}" : $data['cus_id'];
		
		if ( $CMS->permit["customer_read"] == true )
		{
			$data['cus_id'] = "<a href='{$CMS->vars['root_domain']}/?site=customer&act=show&id={$customer['cus_id']}'>{$data['cus_id']}</a>";
		}
		
		// Check permission to read Info
		// $user_name = $CMS->user->get_info_v2($data['user_id'],"user_display_name");
		$user_name = $CMS->user->get_info($data['user_id'],"user_display_name");
		$data['user_id'] = $user['user_id'] ? "{$user_name}" : $data['user_id'];
		
		if ( $CMS->permit["user_read"] == true )
		{
			$data['user_id'] = "<a href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}'>{$user_name}</a>";
		}
		
		// Priority
		$data['email_priority'] = $CMS->lang["email_priority_{$data['email_priority']}"];
		
		// Display
		$data['email_status'] = $CMS->lang["email_status_{$data['email_status']}"];
		
		return $data;
	}
	
	//===========================================================================
	//  INFO
	//===========================================================================
	
	public function get_info( $record_id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "email" )
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
			$sql = "email_key='{$record_id}'";
		}
		else if ( $CMS->class->input->is_nan($record_id) )
		{
			$sql = "email_title='{$record_id}'";
		}
		else
		{
			$sql = "email_id='{$record_id}'";
		}
		// End fix
		
		$sql = $DB->query("SELECT * FROM ".root_table."email WHERE ({$sql}) AND email_deleted=0 ORDER BY email_id DESC LIMIT 1");

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

	
	//===========================================================================
	//  INFO (NEW) Tung
	//  hàm lấy thông tin,đầu vào truyền  
	//  record_id nếu k truyền record_id mặc định lấy input['id']
	//	arr_field_name k truyền thì mặc định lấy tất cả các field
	//===========================================================================
	
	public function get_info_v2( $record_id = 0, $field_name = "" )
	{
		
		global $CMS, $DB, $member;
		if ( !$record_id AND $CMS->input['site'] == "email")
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
			$sql = "email_key='{$record_id}'";
		}
		else if ( $CMS->class->input->is_nan($record_id) )
		{
			$sql = "email_title='{$record_id}'";
		}
		else
		{
			$sql = "email_id='{$record_id}'";
		}
		// End fix
		
		$sql = $DB->query("SELECT {$str_feild} FROM ".root_table."email WHERE ({$sql}) AND email_deleted=0 ORDER BY email_id DESC LIMIT 1");
			
		
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
		
		if ( $except_value )
		{
			$DB->query("SELECT * FROM ".root_table."email WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND email_deleted=0");
		}
		else
		{
			$DB->query("SELECT * FROM ".root_table."email WHERE {$field}='{$value}' AND email_deleted=0");
		}
	
		if ( $DB->num_rows() == 0 )
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
		$email_priority = $CMS->input['email_priority'] ? $CMS->input['email_priority'] : $this->email_priority; // Default is 3 means "normal"
		$user_id = intval($member['user_id']);
		$emailtpl_id = intval($CMS->input['emailtpl_id']);
		$email_header = stripcslashes($_POST['email_header']);
		$email_from = $CMS->input['email_from'] ? $CMS->input['email_from'] : $CMS->vars['smtp_email_display'];
		$email_fromname = $CMS->input['email_fromname'] ? $CMS->input['email_fromname'] : $CMS->vars['website_title'];
		$email_to = trim($CMS->input['email_to']);
		$email_toname = $CMS->input['email_toname'];
		$email_title = trim(strip_tags($CMS->class->editor->input("email_title")));
		$email_content = $CMS->class->editor->input("email_content");
		$email_is_send = $CMS->input['email_is_send'];
		$email_time = time();
		
		// CC
		$email_cc = $CMS->input['email_cc'];
		$email_bcc = $CMS->input['email_bcc'];

		// Get customer ID
		$cus_id = $CMS->customer->getInfo($email_to, "cus_id");
		$cus_id = $cus_id ? $cus_id : 0;
		
		// Convert Header
		$email_header = unserialize($email_header);
		$email_header['emailtpl_id'] = $emailtpl_id;
		$email_header['cus_id'] = $cus_id."abcdef";
		$ord_id = $email_header['ord_id'];
		$email_header = serialize($email_header);
		
		// Generate Email key
		$email_key = md5(time().$user_id.$email_to);
		
		// Generate Email private key, dùng để chặn gửi trùng email trong time nhất định - hvu 7.1.2015
		$email_private_key = md5($email_title.$email_to);
		
		// Order
		if($ord_id)
		{
			// Lấy contract
			$contract_id = $CMS->order->get_info($ord_id,"contract_id");
			$email_logkey = "contract_{$contract_id}";
		}
		
		// Check input
		if ( ! $email_title ) { $CMS->errormsg = "{$CMS->lang['email_incomplete_name']}"; return false; }
		
		if ( ! $email_content ) { $CMS->errormsg = "{$CMS->lang['email_incomplete_content']}"; return false; }
		
		// if ( ! $email_from ) { $CMS->errormsg = "{$CMS->lang['email_incomplete_from']}"; return false; }
		
		if ( ! $email_to ) { $CMS->errormsg = "{$CMS->lang['email_incomplete_to']}"; return false; }

        if($this->check_duplicate_email == 1)
        {
            if ( $this->check_duplicate_email($email_private_key) ) {   $CMS->errormsg = "{$CMS->lang['email_duplicated']}"; return false; }
        }
		
		// Clear content
		$email_content = $CMS->class->mail->clean_content($email_content);
		
		// Check for email server id
		$email_serverid = $this->check_email_server($email_to);
 
		// Insert data
		$DB->query("INSERT INTO ".root_table."email (email_priority, user_id, cus_id, email_header, email_from, email_fromname, email_to, email_toname, email_title, email_content, email_key, email_time, email_cc, email_bcc, email_serverid, email_private_key,email_logkey) VALUES ('{$email_priority}', '{$user_id}', '{$cus_id}', '{$email_header}', '{$email_from}', '{$email_fromname}', '{$email_to}', '{$email_toname}', '{$email_title}', '{$email_content}', '{$email_key}', '{$email_time}', '{$email_cc}', '{$email_bcc}', '{$email_serverid}', '{$email_private_key}', '{$email_logkey}' )");
		
		// Create log
		$_SESSION["msg"] .=  "{$CMS->lang['email_added']} <b>{$email_title}</b>" ;
 		$CMS->class->logs->insert("{$CMS->lang['email_added']}");
		// Get info
		$email = $this->get_info($email_key);

		// Send Email
		if ( $CMS->vars['email_send_by_cron'] == 0)
		{
			$_SESSION["msg"] .= $this->send($email['email_id']);
		}

		return $email;
	}
	
	public function quick_add( $email_priority = 3, $cus_id = 0, $email_from, $email_fromname, $email_to, $email_toname, $email_title, $email_content, $email_is_send=1, $email_time, $email_is_bulkmail, $email_insert_log = 0, $email_cc = "", $email_bcc = "", $is_log = 1, $shopify_order_id = "", $email_attachment="")
	{
		global $CMS, $DB, $member;

		// Generate Email key
		$email_key = md5(time().$cus_id.$email_to);

		// User
		$user_id = isset($member['user_id']) ? $member['user_id'] : 0;
		
		// Clear content
		$email_content = $CMS->class->mail->clean_content($email_content);
		
		// Check for email server id
		$email_serverid = $this->check_email_server($email_to);
		
		// Kiểm tra trùng email trong thời gian tối thiểu cho phép $CMS->vars['time_prevent_duplicate_email'] - hvu 7.1.2015
		$email_title = trim($email_title);
		$email_to = trim($email_to);
		$private_key = md5($email_title.$this->logkey.$email_to.$this->ord_id);

        // Check for default
        $email_from = $this->email_from ? $this->email_from : ($email_from ? $email_from : $CMS->vars['smtp_email_display']);

        $email_fromname = $this->email_fromname ? $this->email_fromname : ($email_fromname ? $email_fromname : $CMS->vars['website_title']);
		
		
        $email_attachment = $email_attachment ? addslashes($email_attachment) : '';

		// Insert data
		$DB->query("INSERT INTO ".root_table."email (email_priority, user_id, cus_id, email_from, email_fromname, email_to, email_toname, email_title, email_content, email_key, email_time, email_is_bulkmail, email_cc, email_bcc, email_logkey, email_serverid, email_private_key, shopify_order_id, email_attachment) VALUES ('{$email_priority}', '{$user_id}', '{$cus_id}', '{$email_from}', '{$email_fromname}', '{$email_to}', '{$email_toname}', '{$email_title}', '{$email_content}', '{$email_key}', '{$email_time}', '{$email_is_bulkmail}', '{$email_cc}', '{$email_bcc}', '{$this->logkey}', '{$email_serverid}', '{$private_key}', '{$shopify_order_id}', '{$email_attachment}')");
		
		// Create log
		if ( $email_insert_log == 1 )
		{
            $CMS->lang['email_added'] =  isset($CMS->lang['email_added']) ? $CMS->lang['email_added'] : '';
			$CMS->class->logs->insert("{$CMS->lang['email_added']} <b>{$email_title}</b>", ($is_log == 1 ? "admin" : "")."---".$email_cc."--".$this->email_cc);
		}

		// Get info
		$email = $this->get_info($email_key);
		 

		if ($CMS->vars['email_send_by_cron'] == 0)
		{
			 
			$this->send($email['email_id']);
		} 
		 
		return $email;
	}
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		// Get info
		$email = $this->get_info($CMS->input['id']);

		$email['email_id']=intval($email['email_id']);
		
		// Check for re-send mode
		if ( $CMS->input['is_resend'] == 1 )
		{
			$CMS->vars['email_send_by_cron'] = 0;
			
			$_SESSION["msg"] .= $this->send($email['email_id']);
			
			return $email;
		}

		// User input
		$emailtpl_id = intval($CMS->input['emailtpl_id']);
		$email_priority = intval($CMS->input['email_priority']);
		$email_header = $CMS->input['email_header'];
		$email_from = $CMS->input['email_from'] ? $CMS->input['email_from'] : $CMS->vars['smtp_email_display'];
		$email_fromname = $CMS->input['email_fromname'] ? $CMS->input['email_fromname'] : $CMS->vars['website_title'];
		$email_to = $CMS->input['email_to'];
		$email_toname = $CMS->input['email_toname'];
		$email_title = $CMS->class->editor->input("email_title");
		$email_content = $CMS->class->editor->input("email_content");
		$email_is_send = $CMS->input['email_is_send'];
		$email_time_update = time();
		
		// CC
		$email_cc = $CMS->input['email_cc'];
		$email_bcc = $CMS->input['email_bcc'];

		// Get customer ID
		$cus_id = $CMS->customer->getInfo($email_to, "cus_id");
		$cus_id = $cus_id ? $cus_id : 0;
		$cus_id=intval($cus_id);
		
		// Convert Header
		$email_header = unserialize($email_header);
		$email_header['emailtpl_id'] = $emailtpl_id;
		$email_header['cus_id'] = $cus_id;
		$email_header = serialize($email_header);

		// Check input
		if ( ! $email_title ) { $CMS->errormsg = "{$CMS->lang['email_incomplete_name']}"; return false; }
		
		if ( ! $email_content ) { $CMS->errormsg = "{$CMS->lang['email_incomplete_content']}"; return false; }
		
		// if ( ! $email_from ) { $CMS->errormsg = "{$CMS->lang['email_incomplete_from']}"; return false; }
		
		if ( ! $email_to ) { $CMS->errormsg = "{$CMS->lang['email_incomplete_to']}"; return false; }
		
		// Step 1: Save detail logs
		$CMS->class->logs->old_data = $email;
		
		// Update info
		$DB->query("UPDATE ".root_table."email SET email_priority='{$email_priority}', cus_id='{$cus_id}', email_header='{$email_header}', email_from='{$email_from}', email_fromname='{$email_fromname}', email_to='{$email_to}', email_toname='{$email_toname}', email_cc='{$email_cc}', email_bcc='{$email_bcc}', email_title='{$email_title}', email_content='{$email_content}', email_time_update='{$email_time_update}' WHERE email_id='{$email['email_id']}'");

		// Create log
		$CMS->class->logs->key = "email_{$email['email_id']}";
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['email_edited']} <b>{$email_title}</b>")."<br />";
		
		// Get info
		$email = $this->get_info();
		
		// Step 2: Save detail logs
		$CMS->class->logs->save_detail("email",$email['email_id'],$email);
		
		// Send Email
		if ($CMS->vars['email_send_by_cron'] == 0    )
		{
			$_SESSION["msg"] .= $this->send($email['email_id']);
		}

		return $email;
	}
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB;
		
		// Get info
		$data = $this->get_info();
		$data['email_id']=intval($data['email_id']);
		// Check existing
		if ( ! $data ) { return false; }
		
		// Update info
		$DB->query("UPDATE ".root_table."email SET email_deleted=1 WHERE email_id={$data['email_id']}");
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['email_deleted']} <b>{$data['email_title']}</b>")."<br />";

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=email&page={$CMS->input['page']}");
		
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

				$DB->query("UPDATE ".root_table."email SET email_deleted=1 WHERE email_id={$id}");
				
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['email_deleted']} <b>{$data['email_title']}</b>")."<br />";
				
				$deleted = 1;
			}
		}
		
		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['email_delete_failed']}";
		}

		return true;
	}
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB;

		// Setup data
		$CMS->class->search->table_name = array("email", "user");
		$CMS->class->search->table_alias = array("email" => "E", "user" => "U");
		$CMS->class->search->table_extend = array("email" =>  " AS E LEFT JOIN ".root_table."user AS U ON U.user_id=E.user_id ");
		$CMS->class->search->fields_type = array("email_time" => "time");
		$CMS->class->search->search_type = 0;
		
		// Output
		$data = $CMS->class->search->get_info();
		
		// Update SQL Query
		$this->sql_add .= $data;
		
		// Get List
		$this->listing();
	}
	
	//===========================================================================
	//  Send Email
	//===========================================================================
	
	public function send( $email_id, $task_user_email="" )
	{
		global $CMS, $DB, $member;
	
		$email_id = intval($CMS->class->filter->md5_cleaner( $email_id ));
		$sql_add = "";
		
		if ( $email_id )
		{	
			$DB->query("SELECT * FROM ".root_table."email WHERE email_id='{$email_id}'"); //  OR email_key='{$email_id}'
			$data = $DB->fetch_array();
			$data['email_id']=intval($data['email_id']);

			// Delete after send
			if ( $this->delete_after_send == 1 )
			{
				$CMS->vars['email_send_by_cron'] = 0;
				$DB->query("DELETE FROM ".root_table."email WHERE email_id='{$data['email_id']}'");
			}
			
			// Convert header
			$data = $this->convert_header($data);
			$data['email_id']=intval($data['email_id']);
			// Use for cron
			$data['email_title'] = $CMS->class->editor->rich_convert($data['email_title']);
			$data['email_content'] = $CMS->class->editor->rich_convert($data['email_content']);
			$DB->query("UPDATE ".root_table."email SET email_title='{$data['email_title']}', email_content='{$data['email_content']}', email_status=0 {$sql_add} WHERE email_id='{$data['email_id']}'");
			
			// Cron
			if ( $CMS->vars['email_send_by_cron'] == 1 && $this->task_send_without_cron == 0)
			{
				$text = "{$CMS->lang['email_sent_ok']} <b>&lt;{$data['email_to']}&gt;</b><br />";
			}
			else
			{
				//Insert Email Content When Send mail Plong 04/06/2013
				$email_content = $this->send_emailcontent($data);
				$data['email_content'] = $email_content['email_content'];
				
			 
				$data['email_to'] = $task_user_email ? $task_user_email : $data['email_to'];
				$data['email_toname'] = $task_user_email ? $CMS->user->get_info_v2($task_user_email,"user_display_name") : $data['email_toname'];
			 
				if ( $CMS->class->mail->sendmail($data['email_to'], $data['email_toname'], $data['email_from'], $data['email_fromname'], html_entity_decode($data['email_title'], ENT_QUOTES), $data['email_content'], $data['email_cc'], $data['email_bcc'], $data['email_attachment']) )
				{
					$DB->query("UPDATE ".root_table."email SET email_status=1 WHERE email_id={$email_id}");

                    $CMS->lang['email_sent_ok'] = isset($CMS->lang['email_sent_ok']) ? $CMS->lang['email_sent_ok'] : '';

					$text = "{$CMS->lang['email_sent_ok']} <b>{$data['email_to']}</b><br />";
				}
				else
				{
					$DB->query("UPDATE ".root_table."email SET email_status=2 WHERE email_id={$email_id}");

                    $CMS->lang['email_sent_failed'] = isset($CMS->lang['email_sent_failed']) ? $CMS->lang['email_sent_failed'] : '';
					$text = "{$CMS->lang['email_sent_failed']} <b>&lt;{$data['email_to']}&gt;</b><br />";
				}
			}

			if(!$task_user_email)
			{
				return $text;
			}
		}
		else
		{
			$text = "{$CMS->lang['email_incomplete_id']} (ID: <b>{$email_id}</b>)<br />";
		
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
		$CMS->class->language->load("service");
		$CMS->class->language->load("payment");
		$CMS->class->language->load("transaction");
		$CMS->class->language->load("ticket");
		$CMS->class->language->load("comment");
		$CMS->class->language->load("cart");
 
		$CMS->class->language->load("web");
		$CMS->class->language->load("task");
 
		
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
		$cloudpcs_id = intval( $CMS->input['cloudpcs_id'] );
		$web_id = intval( $CMS->input['web_id'] );
		$code_id = intval($CMS->input['code_id']);
        $promotion_id = intval($CMS->input['promotion_id']);
        $sharing_id = intval($CMS->input['sharing_id']);

		$log_id = intval($CMS->input['log_id']);
		$post_id = intval($CMS->input['post_id']);
		
		$domainvn_id = intval( $CMS->input['domainvn_id'] );
		
		$request_id = intval($CMS->input['request_id']);
		
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
			$temp['pickup_seller'] = $CMS->input['pickup_seller'];
			$temp['pickup_code'] = $CMS->input['pickup_code'];
			
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
			$temp = $CMS->user->convertvalue($temp);
			
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
		
		// Domainvn
		if ( $domainvn_id )
		{
			$temp = $CMS->domainvn->get_info($domainvn_id);
			$temp = $CMS->domainvn->convertvalue($temp);
			
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
		
		// Cloud PCS Server
		if ( $cloudpcs_id )
		{
			$temp = $CMS->servercloudpcs->get_info($cloudpcs_id);
			$temp = $CMS->servercloudpcs->convertvalue($temp);
		
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
			$temp = $this->logkey == "login" ? $CMS->class->logs->get_login_log($log_id) : $CMS->class->logs->get_info($log_id);
			$temp = $CMS->class->logs->convertvalue($temp);	
			
			// Convert to array
			$data = array_merge( $data, $temp );
		}
		
		// Forum post
		if($post_id)
		{
			$temp = $CMS->forumpost->get_info($post_id);
			$temp = $CMS->forumpost->convertvalue($temp);
		
			// Convert to array
			$data = array_merge( $data, $temp );	
		}
		
		// Request gift exchange
		if($request_id)
		{
			$temp = $CMS->request_gift_exchange->get_info($request_id);
			$temp = $CMS->request_gift_exchange->convertvalue($temp);
		
			// Convert to array
			$data = array_merge( $data, $temp );	
		}
		

        if($promotion_id)
        {
            $temp = $CMS->promotion->get_info($promotion_id);
            $temp = $CMS->promotion->convertvalue($temp);

            // Convert to array
            $data = array_merge( $data, $temp );
        }

        if($sharing_id)
        {
            $temp = $CMS->sharing_coupon->get_info($sharing_id);
            $temp = $CMS->sharing_coupon->convertvalue($temp);

            // Convert to array
            $data = array_merge( $data, $temp );
        }
		
		if(isset($this->data) && !empty($this->data))
		{
			//nkvp - 30/12/2014
			$data = array_merge( $data, $this->data );
		}
		
		// Ord day and ord full day for new email templates
		$data['ord_day'] = date("N");
		$data['ord_day'] = $CMS->lang["ord_day_{$data['ord_day']}"];
		$data['ord_full_day'] = $CMS->class->date->date_format(time());

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
	
	public function quick_send( $is_log = 1, $is_send = 1 )
	{
		// return false;
		global $CMS, $DB, $member;
		// Set auto send mail
		if($CMS->vars['email_send_by_cron'] == 1)
		{
			$is_send = 0;
		}
		else
		{
			$is_send = 1;
		}
		// Load Language
		$CMS->class->language->load("email");
		
		$template = $CMS->emailtpl->get_info($this->email_template);
 
		// Check for SMTP
		$this->email_from = $CMS->class->smtp->smtp_user ? $CMS->class->smtp->smtp_user : $this->email_from;
		$this->email_fromname = $CMS->class->smtp->smtp_user ? $CMS->class->smtp->smtp_user : $this->email_fromname;
		
		// Check for default
		$template['emailtpl_from'] = $this->email_from ? $this->email_from : ($template['emailtpl_from'] ? $template['emailtpl_from'] : $CMS->vars['smtp_email_display']);
		
		$template['emailtpl_fromname'] = $this->email_fromname ? $this->email_fromname : ($template['emailtpl_fromname'] ? $template['emailtpl_fromname'] : $CMS->vars['website_title']);

		// Add promotion text
		// Get emailtpl for promotion
		if(isset($CMS->vars['promotion_emailtpl']) && $CMS->vars['promotion_emailtpl'])
		{
			$tpl = explode(",",$CMS->vars['promotion_emailtpl']);
		}
		list($data, $text) = $this->convert_v1();

        // Send email
        $email = $this->quick_add( $this->email_priority, 0, $template['emailtpl_from'], $template['emailtpl_fromname'], $this->email_to, ($this->email_toname ? $this->email_toname : $this->email_to), $this->convert_v2($template['emailtpl_title'],$data), $this->convert_v2($template['emailtpl_content'],$data), $is_send, time(), 0, 1, $this->email_cc, $this->email_bcc, $is_log ,'', $this->email_attachment);

		return $email;
	}
	
	
	//===========================================================================
	//  GATEWAY
	//===========================================================================
	
	public function quick_send_2( $is_log = 1 )
	{
		global $CMS, $DB, $member;
		 
		$template = $CMS->emailtpl->get_info($this->email_template);

		// Check for SMTP
		$this->email_from = $CMS->class->smtp->smtp_user ? $CMS->class->smtp->smtp_user : $this->email_from;
		$this->email_fromname = $CMS->class->smtp->smtp_user ? $CMS->class->smtp->smtp_user : $this->email_fromname;
		
		// Check for default
		$template['emailtpl_from'] = $this->email_from ? $this->email_from : $template['emailtpl_from'];
		
		$template['emailtpl_fromname'] = $this->email_fromname ? $this->email_fromname : $template['emailtpl_fromname'];
		
		list($data, $text) = $this->convert_v1();
		// Send email
		$email = $this->quick_add( $this->email_priority, 0, $template['emailtpl_from'], $template['emailtpl_fromname'], $this->email_to, ($this->email_toname ? $this->email_toname : $this->email_to), $this->convert_v2($template['emailtpl_title'],$data), $this->convert_v2($template['emailtpl_content'],$data), 0, time(), 0, 1, $this->email_cc, $this->email_bcc, $is_log, $this->shopify_order_id);
		
		// $email = $this->quick_add($this->email_priority, 0, $template['emailtpl_from'], 'aaa', $this->email_to, ($this->email_toname ? $this->email_toname : $this->email_to), 'bbb', 'ddd', 0, time(), 0, 1, $this->email_cc, $this->email_bcc, $is_log );
		
		return $email;
	}
	
	
	
	//===========================================================================
	//  GET EMAIL HEADER (USER INPUT)
	//===========================================================================
	
	public function get_email_header()
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

		$this->email_header = serialize( $data );
	}
	
	//===========================================================================
	//  GET EMAIL HEADER (USER INPUT)
	//===========================================================================
	
	public function convert_header($data)
	{
		global $CMS, $DB;
		
		// Header
		if ( strlen($data['email_header']) > 0 )
		{
			$email_header = unserialize(stripslashes($data['email_header']));
			$email_header_key = array_keys($email_header);
				
			// User Input
			for ( $i = 0; $i < count($email_header); $i++ )
			{
				$CMS->input[$email_header_key[$i]] = $email_header[$email_header_key[$i]];
			}
			
			$data['email_title'] = $this->convert($data['email_title']);
			$data['email_content'] = $this->convert($data['email_content']);
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
		$email_header = unserialize($CMS->class->editor->input('email_header'));
		$email_header_key = array_keys($email_header);
		
		// User Input
		for ( $i = 0; $i < count($email_header); $i++ )
		{
			if ( $email_header[$email_header_key[$i]] AND ! $CMS->input[$email_header_key[$i]] )
			{
				$CMS->input[$email_header_key[$i]] = $email_header[$email_header_key[$i]];
			}
		}

		// Content
		$email_title = $this->convert($CMS->class->editor->input('email_title'));
		$email_content = $this->convert($CMS->class->editor->input('email_content'));

		// Output
		$output = $this->html->preview( "<b>{$email_title}</b><br />{$email_content}" );
		
		return $output;
	}
	
	//===========================================================================
	//  GET EMAIL LIST
	//===========================================================================
	
	public function get_email_list( $data, $contract_email_sent = "" )
	{
		global $CMS, $DB, $member;
		
		// Load language
		$CMS->class->language->load("email");
	
		// Define Condition for Get List
		$CMS->email->sql_add .= " ( ";
		
		for ( $i = 0; $i < count($data); $i++ )
		{
			if ( $i >= 1 )
			{
				$CMS->email->sql_add .= " OR ";
			}
			
			if ( $data[$i] )
			{
				$CMS->email->sql_add .= " email_logkey='{$data[$i]}' ";
			}
		}
		
		// Get email follow email_contract_sent
		if($contract_email_sent)
		{
			// Explode
			$email_sent = explode(",",$contract_email_sent);

			//$CMS->email->sql_add .= " OR ";

			for($j=0;$j<count($email_sent);$j++)
			{
				//if ( $j >= 1 )
				//{
					//$CMS->email->sql_add .= " OR ";
				//}
				
				if($email_sent[$j])
				{
					$CMS->email->sql_add .= " OR email_id={$email_sent[$j]} ";
				}
			}
		}
		// End
		
		$CMS->email->sql_add .= " ) AND ";
		
		$this->get_list_limit = 1;
	
		// Get List
		$CMS->email->listing();
		
		// Write data
		return $CMS->email->html();
	}
	
	public function check_email_server($to)
	{
		global $CMS, $DB;
		
		// Check for send mail to @nhanhoa.com or not
		$email_to = explode("@",$to);
		$email_to = isset($email_to[1]) ? $email_to[1] : '';

        $CMS->vars['smtp2_detect'] = isset($CMS->vars['smtp2_detect']) ? $CMS->vars['smtp2_detect'] : '';

		if(strtolower(substr($email_to,0,strlen($CMS->vars['smtp2_detect']))) != $CMS->vars['smtp2_detect'])
		{
			if($CMS->vars['smtp2_enable'])
			{
				return 1;
			}
		}
		return 0;
	}
	
	public function get_emailcontent($emailc_id = '', $type = 0)
	{
		global $CMS, $DB;
		$emailc_id = intval($emailc_id);
		$sql = $DB->query("SELECT * FROM ".root_table."email_content WHERE emailc_id = '{$emailc_id}' ORDER BY emailc_id DESC LIMIT 1");

		if($DB->num_rows($sql) > 0)
		{
			$data = $DB->fetch_array($sql);
			if($type == 1)
			{
				return $data['emailc_content'];
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
	
	//=====================================================
	// Insert Email Content When Send mail Plong 04/06/2013
	//=====================================================
	public function send_emailcontent($email)
	{
		global $CMS, $DB;
		$email['email_id'] = isset($email['email_id']) ? intval($email['email_id']) : 0;
		$email['emailc_id'] = isset($email['emailc_id']) ? intval($email['emailc_id']) : 0;
		
		$sql_cnt = $DB->query("SELECT * FROM ".root_table."email_content WHERE emailc_id = '{$email['email_id']}' ORDER BY emailc_id DESC ");
		
		if($email['emailc_id'] == 0)
		{  
			if($DB->num_rows($sql_cnt) == 0)
			{
				// Insert email content
				$DB->query("INSERT INTO ".root_table."email_content (emailc_id, emailc_content) VALUES ('{$email['email_id']}', '{$email['email_content']}')" );
			
				// Update Emailc_id and emailcontent
				//$DB->query("UPDATE ".root_table."email SET emailc_id={$email['email_id']} , email_content = NULL WHERE email_id={$email['email_id']}");
			}
			
		}
		else if($email['emailc_id'] != 0 )
		{
			//$sql_cnt = $DB->query("SELECT * FROM ".root_table."email_content WHERE emailc_id = '{$email['email_id']}' ORDER BY emailc_id DESC ");
			$email2 = $DB->fetch_array($sql_cnt);
			$email['email_content'] = $email2['emailc_content'];	
			
		}
		return $email;
	}
	
	public function check_duplicate_email($private_key)
	{
		global $CMS, $DB, $member;
		
		if(!$private_key) { return false; }
		
		// Check duplicate email
		$sql = $DB->query("SELECT email_id FROM ".root_table."email WHERE email_private_key='{$private_key}' AND email_time > '".(time() - ($CMS->vars['time_prevent_duplicate_email']*60) )."' AND email_deleted=0");
		
		if($DB->num_rows($sql) > 0)
		{
			return true;	
		}
		
		return false;
	}
	
	public function convert_v1($text='')
	{
		global $CMS, $DB, $member;
		 
		// Language
		$CMS->class->language->load("customer");
		$CMS->class->language->load("order");
 

		// Backup permission
		$permit = isset($CMS->permit) ? $CMS->permit : [];
		unset($CMS->permit);

		$text = $CMS->class->editor->input( $text, "text" );

		$setting = $CMS->vars;

		$newarray = array();

		// Convert to array
		$setting = array_merge( $setting, $newarray );

		$cus_id = isset($CMS->input['cus_id']) ? intval( $CMS->input['cus_id'] ) : 0;
		$user_id = isset($CMS->input['user_id']) ? intval( $CMS->input['user_id'] ) : 0;
	 
  

		$user_id = isset($CMS->input['user_id']) ? intval( $CMS->input['user_id'] ) : 0;
 
		
		$cart_id = isset($CMS->input['cart_id']) ? intval( $CMS->input['cart_id'] ) : 0;
	 
	 
	 
		$code_id = isset($CMS->input['code_id']) ? intval($CMS->input['code_id']) : 0;
		 
	 
		
		// Convert to array
		$data = array();
		$data = array_merge( $data, $setting );
		

		if(isset($this->data) && !empty($this->data))
		{
			//nkvp - 30/12/2014
			$data = array_merge( $data, $this->data );
		}
		
		return array($data,$text);
	}
	
	public function convert_v2($text,$data)
	{
		global $CMS, $DB;
		
		// Check for delegate
		if ( isset($data['cus_own_type_bk']) && $data['cus_own_type_bk'] == 0 )
		{
			//$text = preg_replace('/\<\!--\[delegate\]--\>(.*)\<\!--\[enddelegate\]--\>/', '', $text);
			//$text = preg_replace("/\<!--\[delegate\]--\>(.+?)\<!--\[enddelegate\]--\>/i", "", $text);
			$text = preg_replace("#(.+?)<!--\[delegate\]-->(.+?)<!--\[enddelegate\]-->(.+?)#is", "\\1\\3", $text);
		}
		
		//---------------------------------------------------------------
		// Rebuild data
		//---------------------------------------------------------------

        //Fix if contain condition commment
        $text = preg_replace("/<\!\[([a-zA-Z0-9\_]+?)\]/i", "{{{\\1}}}", $text);

        $text = preg_replace("/\[([a-zA-Z0-9\_]+?)\]/i", "\$data[\\1]", $text); //Original code

        //Fix if contain condition commment
        $text = preg_replace("/{{{([a-zA-Z0-9\_]+?)}}}/i", "<!--[\\1]", $text);

		// Remove slash
		$text = str_replace("\"", "\\\"", $text);
		
		eval("\$text = \"$text\";");
		
		// Restore Permission
		$CMS->permit = isset($permit) ? $permit : [];

		return $text;
	}
	
	

	public function add_email_status($ord_id=0, $type="")
	{
		global $CMS, $DB;

		$data = $CMS->order->get_info($ord_id);

		if($type == "printing")
		{
		//Config email

		$html_content =<<<EOF
			{$CMS->lang['notice_email_1']} <b>{$data['ord_name']}</b>
			{$CMS->lang['notice_email_1']} printing.
		
EOF;
		}elseif($type == "shipping")
		{
		//Config email

		$html_content =<<<EOF
			{$CMS->lang['notice_email_1']} <b>{$data['ord_name']}</b>
			{$CMS->lang['notice_email_1']} shipping.
		
EOF;
		}elseif($type == "completed")
		{
		//Config email

		$html_content =<<<EOF
			{$CMS->lang['notice_email_1']} <b>{$data['ord_name']}</b>
			{$CMS->lang['notice_email_1']} completed.
		
EOF;
		}elseif($type == "refund")
		{
		//Config email

		$html_content =<<<EOF
			{$CMS->lang['notice_email_1']} <b>{$data['ord_name']}</b>
			{$CMS->lang['notice_email_2']} refund.
		
EOF;
		}


		$this->email_template = "email_status_order";
		$this->email_to= $data['ord_cus_email'];

		$this->data['cus_name'] = $data['ord_cus_name'];
		$this->data['content_print'] = $html_content;
		$this->send_email = false;
		$this->quick_send_2();

		return true;
	}


	public function checkEmailOrderShopify($order_id='')
	{
		global $CMS, $DB;

		$sql = $DB->query("SELECT 0 FROM ".root_table."email WHERE shopify_order_id = '{$order_id}'");
		if($DB->num_rows($sql) > 0)
		{
			return true;
		}else
		{
			return false;
		}
	}

	public function count($email_from='',$email_to='')
    {
        global $DB;
        $sql_add = "";

        if($email_from = trim($email_from))
        {
            $sql_add .= " email_from='{$email_from}' AND ";
        }

        if($email_to = trim($email_to))
        {
            $sql_add .= " email_to='{$email_to}' AND ";
        }

        $sql = "SELECT COUNT(email_id) cnt FROM ".root_table."email WHERE {$sql_add} email_deleted=0";

        return $DB->fetch($sql,'cnt');
    }
}

?>