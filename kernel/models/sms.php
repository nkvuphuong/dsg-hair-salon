<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->sms = new class_sms;

class class_sms {

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
	 * @param $html_data
	 *		HTML of records
	 */
	
	public $html_data = 0;
	
	/**
	 * @param $html
	 *		The templates
	 */
	 
	 public $html;

	 /**
	  * @param $checkspam_time
	  *     Time to check spam
	  * */
	 public $checkspam_time = 120;

	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	
	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			$this->html = $CMS->class->template->load_template("skin_sms");		
		}
	}
	
	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("sms_id,sms_from,sms_to,sms_time");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "sms_time";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "DESC";
		
		// SQL Condition
		$this->sql_add .= " sms_deleted=0 AND ";

		// Create SQL Query for listing Data
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."sms WHERE {$this->sql_add} 1=1  ORDER BY {$default_field} {$default_order}");
		$this->pages_cnt = $DB->num_rows( $this->sql_query );
	}
	
	public function listing_whm()
	{
		global $CMS, $DB;
		
		$sms_survey = intval($CMS->input['sms_survey']);
		// Update Arrange Data
		$this->arrange_data = trim("sms_id,sms_from,sms_to,sms_time");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "sms_time";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "DESC";
		
		// SQL Condition
		$this->sql_add .= " sms_deleted=0 AND sms_survey = '{$sms_survey}' AND  ";

		// Create SQL Query for listing Data
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."sms WHERE {$this->sql_add} 1=1  ORDER BY {$default_field} {$default_order}");
		$this->pages_cnt = $DB->num_rows( $this->sql_query );
	}

	//===========================================================================
	//  LISTING DATA CATEGORY HOME
	//===========================================================================
	
	public function listing_home()
	{
		global $CMS, $DB;
		
			
		// Update Arrange Data
		$this->arrange_data = trim("cat_time,cat_id,cat_name,cat_status");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "cat_order_home";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "ASC";
		
		// SQL Condition
		$this->sql_add .= " cat_deleted=0 AND ";

		// Create SQL Query for listing Data
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."gallery_category WHERE cat_display_home =1  AND {$this->sql_add}  1=1  ORDER BY {$default_field} {$default_order}");
		$this->pages_cnt = $DB->num_rows( $this->sql_query );
	}
	
	
	public function html()
	{
		global $CMS, $DB, $member;
		
		$this->loadhtml();
		
		// Display Header
		$output = $this->html->header();
		
		if ( $DB->num_rows( $CMS->sms->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->sms->sql_query ) )
			{
				// Convert info
				$result = $CMS->sms->convertvalue($result);
				
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
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sms");
		}
		
		return $output;
	}



	public function html_home()
	{
		global $CMS, $DB, $member;
		
		$this->loadhtml();
		
		// Display Header
		$output .= $this->html->header_home();
		
		if ( $DB->num_rows( $CMS->sms->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->sms->sql_query ) )
			{
				// Convert info
				$result = $CMS->sms->convertvalue($result);
				
				// Display Middle
				$output .= $this->html->middle_home($result);
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
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sms");
		}
		
		return $output;
	}


	public function data()
	{
		global $CMS, $DB;
		
		$sql = $DB->query("SELECT * FROM ".root_table."gallery_category WHERE cat_deleted=0");
		
		$output = "";
		
		while ( $cat = $DB->fetch_array() )
		{
			$output .= "<option value='{$cat['cat_id']}'>{$cat['cat_name']}</option>";
		}
		
		$this->html_data = $output;
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
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_sms_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_sms_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";

			// Check permission to Delete
			if ( $CMS->permit["sms_delete"] == true )
			{
				$data .= "<option value='delete_all'>{$CMS->lang['action_delete']}</option>";
				$this->control = 1;
			}
			// Check permission to Arrange
			$data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

			$CMS->class->cache->save("user_{$member['user_id']}_sms_controller_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}
		
		if ( $CMS->vars['action_controller'] )
		{
			$this->action_control = $this->html->control();
		}
	}

	//===========================================================================
	//  DATA
	//===========================================================================

	public function defaultvalue($data)
	{
		global $CMS;

		return $data;
	}
	
	public function convertvalue($data)
	{
		global $CMS, $DB;

        $data['data_bk'] = $data;

		// Replace search content
		$data = $CMS->class->search->convertvalue($data);

		// Convert Register to GMT
		$data['sms_time'] = $CMS->class->date->date_format( $data['sms_time'], 1 );
                $data['sms_update_time'] = $data['sms_update_time'] > 0 ? $CMS->class->date->date_format( $data['sms_update_time'], 1 ) : "";

		// Check permission to read Info
		if ( $CMS->permit["sms_read"] == true )
		{
			$data['sms_id_bk'] = "<a href='{$CMS->vars['root_domain']}/?site=sms&act=show&id={$data['sms_id']}'>{$data['sms_id']}</a>";
		}
                
        $data['sms_status'] = $CMS->lang["sms_status_{$data['sms_status']}_color"];

		if($data['data_bk']['sms_api_method'] == 'curl')
        {
            $data['sms_api_response'] = @json_decode($data['sms_api_response'], 1);

            $api_err = [];

            foreach ($data['sms_api_response']['messages'] as $api_message)
            {
                if(isset($api_message['error-text']))
                {
                    $api_err[] = $api_message['error-text'];
                }
            }

            $data['sms_status'] = $data['data_bk']['sms_status'] == 2 && $api_err ? "<span data-toggle='tooltip' title='".implode(', ', $api_err)."'>{$data['sms_status']}</span>" : $data['sms_status'];
        }
        elseif ($data['data_bk']['sms_api_method'] == 'nexmo')
        {
            $data['sms_status'] = $data['data_bk']['sms_status'] == 2 && $data['sms_api_response'] ? "<span data-toggle='tooltip' title='{$data['sms_api_response']}'>{$data['sms_status']}</span>" : $data['sms_status'];
        }

                
		// Bgcolor
		$data['bgcolor'] = $this->record_cnt % 2 != 0 ? "#FFFFFF" : "#F6F6F6";
		
		// Count
		$data['record_cnt'] = $this->record_cnt;
		
		$this->record_cnt++;
		
		return $data;
	}
	
	public function searchvalue($data)
	{
		global $CMS, $DB;

		// Convert Register to GMT
		$data['cat_time'] = $CMS->class->date->date_format( $data['cat_time'], 0 );

		// Replace the Status
		$data['cat_status'] = $CMS->lang["display_{$data['cat_status']}"];

		return $data;
	}
	
	//===========================================================================
	//  INFO
	//===========================================================================
	
	public function get_info( $record_id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;

		if ( ! $record_id )
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
		
		$sql = $DB->query("SELECT * FROM ".root_table."sms WHERE sms_id='{$record_id}' AND sms_deleted=0 ORDER BY sms_id DESC LIMIT 1");

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
			$DB->query("SELECT * FROM ".root_table."gallery_category WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND cat_deleted=0");
		}
		else
		{
			$DB->query("SELECT * FROM ".root_table."gallery_category WHERE {$field}='{$value}' AND cat_deleted=0");
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
	
	public function add($data=[])
	{
            global $CMS, $DB, $member;
 
            if(!$CMS->vars['sms_enabled'])
            {
                $_SESSION['msg'] .= isset($CMS->lang['sms_not_yet_enable']) ? "{$CMS->lang['sms_not_yet_enable']}" : ''; return false;
            }
            // Bỏ cái check có đầu số đc cấu hình. Vì đang sử dụng list sms free
            if($CMS->vars['sms_number'] == '')
            {
               // $_SESSION['msg'] .= "{$CMS->lang['sms_number_not_found']}"; return false;
            }

            $CMS->input = array_merge($CMS->input, $data);
            
            // User input
           
            //Nếu sử dụng sms subscription thì dung sms number dc cấu hình
            $CMS->vars['sms_subscription'] = intval($CMS->vars['sms_subscription']);
            
            if($CMS->vars['sms_subscription'] == 1)
            { 	
            	$sms_from = $CMS->input['sms_from'];
            }
           	else
           	{
           		// 09102017 Quy trình mới:get dau số sms ngẫu nhiên
           		//$sms_from = $this->get_sms_from();
           		// Thay null = "18448805051" de test local
           		$sms_from = isset($CMS->vars['sms_from_free']) ? $CMS->vars['sms_from_free'] : null;
           	}
 				
            $sms_to_number = isset($CMS->input['sms_to'])?$CMS->input['sms_to']:null;
            // Format number
            $sms_to_number = preg_replace("/(?!([0-9,]))./", "", $sms_to_number);// Remove những ký tự không phải số

            $sms_reply = isset($CMS->input['sms_reply'])?$CMS->input['sms_reply']:null;
            $sms_content = trim($CMS->class->editor->input(isset($CMS->input['sms_content']) ? $CMS->input['sms_content'] : null, "text"));
            $sms_content = $CMS->class->seo->remove_vietnamese($sms_content);
            $time = time();
		
            // Check input
            if ( ! $sms_from ) { $_SESSION['msg'] = isset($CMS->lang['sms_incomplete_from']) ? "{$CMS->lang['sms_incomplete_from']}" : null; return false; }
            if ( ! $sms_to_number ) { $_SESSION['msg'] = isset($CMS->lang['sms_incomplete_to']) ? "{$CMS->lang['sms_incomplete_to']}" : null; return false; }
            if ( ! $sms_content ) { $_SESSION['msg'] = isset($CMS->lang['sms_incomplete_content']) ? "{$CMS->lang['sms_incomplete_content']}" : null; return false; }
            // Check multil number phone receive
            $list_number = explode(",", $sms_to_number);
            foreach ($list_number as $sms_to) 
            {
	            // Auto add +1 if that's an US number & Prevent 0 in the first number
	            if ( strlen($sms_to) <= 10 && substr($sms_to,0,1) != "0" )
	            {
	                $sms_to = "1".$sms_to;
	            }
	        
	            if($CMS->class->input->is_nan($sms_to) == true || strlen($sms_to) < 9 || strlen($sms_to) > 12)
	            {
	                $CMS->errormsg = "{$CMS->lang['sms_invalid_to']}"; return false;
	            }

	            if($check_spam = $this->checkspam($sms_from, $sms_to, $sms_content))
	            {
	                $sms_status = 3; //merged
	            }
	            else
	            {
	                $sms_status = 0; //pending
	            }

	            // Insert data
	            $DB->query("INSERT INTO ".root_table."sms (sms_from, sms_to, sms_content, sms_time, sms_reply, sms_status) VALUES ('{$sms_from}', '{$sms_to}', '{$sms_content}', '{$time}', '{$sms_reply}', '{$sms_status}')");
			
	            // Get data
	            $data = $DB->fetch_array($DB->query("SELECT * FROM ".root_table."sms ORDER BY sms_id DESC LIMIT 1"));
	                
	            // Create log
	            $CMS->lang['sms_added'] = isset($CMS->lang['sms_added']) ? $CMS->lang['sms_added'] : "Send sms";
	            $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['sms_added']} <b>#{$data['sms_id']}</b>")."<br />";
 
	            if(!$CMS->vars['sms_send_by_cron'] && $sms_status == 0) //Gui lap tuc (k qua cron)
	            {
	                $result = \api\Nexmo\sms::send($sms_from, $sms_to, $sms_content);

	                $data['status_sms'] = $result['status'];
	                if ( $result['status'] == 'success' )
	                {
	                    $DB->query("UPDATE ".root_table."sms SET sms_status=1, sms_api_response='{$result['response']}', sms_api_method='{$CMS->vars['sms_method']}' WHERE sms_id={$data['sms_id']}");
	                }
	                else
	                {
	                    $DB->query("UPDATE ".root_table."sms SET sms_status=2, sms_api_response='{$result['response']}', sms_api_method='{$CMS->vars['sms_method']}' WHERE sms_id={$data['sms_id']}");
	                }
	            }

	        }// End for
 
            return $data;
	}

	//===========================================================================
	//  ADD
	//===========================================================================
	
	public function add_whm($data=[])
	{
            global $CMS, $DB, $member;
 			
 			$sms_survey = intval($data['sms_survey']);
            if(!$CMS->vars['sms_enabled'])
            {
                $_SESSION['msg'] .= isset($CMS->lang['sms_not_yet_enable']) ? "{$CMS->lang['sms_not_yet_enable']}" : ''; return false;
            }
            // Bỏ cái check có đầu số đc cấu hình. Vì đang sử dụng list sms free
            if($CMS->vars['sms_number'] == '')
            {
               // $_SESSION['msg'] .= "{$CMS->lang['sms_number_not_found']}"; return false;
            }

            $CMS->input = array_merge($CMS->input, $data);
            
            // User input
           
            //Nếu sử dụng sms subscription thì dung sms number dc cấu hình
            $CMS->vars['sms_subscription'] = intval($CMS->vars['sms_subscription']);
            
            if($CMS->vars['sms_subscription'] == 1)
            { 	
            	$sms_from = $CMS->input['sms_from'];
            }
           	else
           	{
           		// 09102017 Quy trình mới:get dau số sms ngẫu nhiên
           		//$sms_from = $this->get_sms_from();
           		// Thay null = "18448805051" de test local
           		$sms_from = isset($CMS->vars['sms_from_free']) ? $CMS->vars['sms_from_free'] : null;
           	}
 				
            $sms_to_number = isset($CMS->input['sms_to'])?$CMS->input['sms_to']:null;
            // Format number
            $sms_to_number = preg_replace("/(?!([0-9,]))./", "", $sms_to_number);// Remove những ký tự không phải số

            $sms_reply = isset($CMS->input['sms_reply'])?$CMS->input['sms_reply']:null;
            $sms_content = trim($CMS->class->editor->input(isset($CMS->input['sms_content']) ? $CMS->input['sms_content'] : null, "text"));
            $sms_content = $CMS->class->seo->remove_vietnamese($sms_content);
            $time = time();
		
            // Check input
            if ( ! $sms_from ) { $_SESSION['msg'] = isset($CMS->lang['sms_incomplete_from']) ? "{$CMS->lang['sms_incomplete_from']}" : null; return false; }
            if ( ! $sms_to_number ) { $_SESSION['msg'] = isset($CMS->lang['sms_incomplete_to']) ? "{$CMS->lang['sms_incomplete_to']}" : null; return false; }
            if ( ! $sms_content ) { $_SESSION['msg'] = isset($CMS->lang['sms_incomplete_content']) ? "{$CMS->lang['sms_incomplete_content']}" : null; return false; }

            // Check multil number phone receive
            $list_number = explode(",", $sms_to_number);
            foreach ($list_number as $sms_to) 
            {
	            // Auto add +1 if that's an US number & Prevent 0 in the first number
	            if ( strlen($sms_to) <= 10 && substr($sms_to,0,1) != "0" )
	            {
	                $sms_to = "1".$sms_to;
	            }
	 
	            if($CMS->class->input->is_nan($sms_to) == true || strlen($sms_to) < 9 || strlen($sms_to) > 12)
	            {
	                $CMS->errormsg = "{$CMS->lang['sms_invalid_to']}"; return false;
	            }

	            if($check_spam = $this->checkspam($sms_from, $sms_to, $sms_content))
	            {
	                $sms_status = 3; //merged
	            }
	            else
	            {
	                $sms_status = 0; //pending
	            }
 
	            // Insert data
	            $DB->query("INSERT INTO ".root_table."sms (sms_from, sms_to, sms_content, sms_time, sms_reply, sms_status,sms_survey) VALUES ('{$sms_from}', '{$sms_to}', '{$sms_content}', '{$time}', '{$sms_reply}', '{$sms_status}', '{$sms_survey}')");
			
	            // Get data
	            $data = $DB->fetch_array($DB->query("SELECT * FROM ".root_table."sms ORDER BY sms_id DESC LIMIT 1"));
	                
	            // Create log
	            $CMS->lang['sms_added'] = isset($CMS->lang['sms_added']) ? $CMS->lang['sms_added'] : "Send sms";
	            $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['sms_added']} <b>#{$data['sms_id']}</b>")."<br />";
 
	            if(!$CMS->vars['sms_send_by_cron'] && $sms_status == 0) //Gui lap tuc (k qua cron)
	            {
	                $result = \api\Nexmo\sms::send($sms_from, $sms_to, $sms_content);

	                $data['status_sms'] = $result['status'];
	                if ( $result['status'] == 'success' )
	                {
	                    $DB->query("UPDATE ".root_table."sms SET sms_status=1, sms_api_response='{$result['response']}', sms_api_method='{$CMS->vars['sms_method']}' WHERE sms_id={$data['sms_id']}");
	                }
	                else
	                {
	                    $DB->query("UPDATE ".root_table."sms SET sms_status=2, sms_api_response='{$result['response']}', sms_api_method='{$CMS->vars['sms_method']}' WHERE sms_id={$data['sms_id']}");
	                }
	            }

	        }// End for
 
            return $data;
	}

	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
            global $CMS, $DB, $member;
		
            // Get info
            $data = $this->get_info();

            // User input
            $sms_from = trim($CMS->input['sms_from']);
            $sms_to = trim($CMS->input['sms_to']);
            $sms_content = trim($CMS->class->editor->input("sms_content"));
            $sms_content = $CMS->class->seo->remove_vietnamese($sms_content);
            $sms_update_time = time();
		
		// Check input
		if ( ! $sms_from ) { $_SESSION['msg'] = "{$CMS->lang['sms_incomplete_from']}"; return false; }
                if ( ! $sms_to ) { $_SESSION['msg'] = "{$CMS->lang['sms_incomplete_to']}"; return false; }
		if ( ! $sms_content ) { $_SESSION['msg'] = "{$CMS->lang['sms_incomplete_content']}"; return false; }
                
                // Check number
                if($CMS->class->input->is_nan($sms_from) == true || strlen($sms_from) < 9 || strlen($sms_from) > 12)
                {
                    $_SESSION['msg'] = "{$CMS->lang['sms_invalid_from']}"; return false;
                }

                if($CMS->class->input->is_nan($sms_to) == true || strlen($sms_to) < 9 || strlen($sms_to) > 12)
                {
                    $_SESSION['msg'] = "{$CMS->lang['sms_invalid_to']}"; return false;
                }
                
		// Step 1: Save detail logs
		$CMS->class->logs->old_data = $data;
		
		// Update info
		$DB->query("UPDATE ".root_table."sms SET sms_from='{$sms_from}', sms_to='{$sms_to}', sms_content='{$sms_content}', sms_update_time='{$sms_update_time}' WHERE sms_id='{$data['sms_id']}'");
		
		$CMS->class->logs->key = "sms_{$data['sms_id']}";
                
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['sms_edited']} <b>#{$data['sms_id']}</b>")."<br />";
		
		// Get info
		$data = $this->get_info();
                
		// Step 2: Save detail logs
		$CMS->class->logs->key = "sms_{$data['sms_id']}";
		$CMS->class->logs->save_detail("sms",$data['sms_id'],$data);
		
		
		return $data;
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
		$DB->query("UPDATE ".root_table."sms SET sms_deleted=1 WHERE sms_id={$data['sms_id']}");
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['sms_deleted']} <b>{$data['sms_id']}</b>")."<br />";

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sms&page={$CMS->input['page']}");
		
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

				$DB->query("UPDATE ".root_table."sms SET sms_deleted=1 WHERE sms_id={$id}");
				
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['sms_deleted']} <b>{$data['sms_id']}</b>")."<br />";
				
				$deleted = 1;
			}
		}
		
		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['sms_delete_failed']}";
		}

		return true;
	}
	
	//===========================================================================
	//  ARRANGE
	//===========================================================================

	public function arrange()
	{
		global $CMS, $DB;
		
		$_SESSION["msg"] .= "";
		$sql = $DB->query("SELECT * FROM ".root_table."gallery_category WHERE cat_deleted=0 ORDER BY cat_id ASC");
		
		while ( $data = $DB->fetch_array( $sql ) )
		{ 
			$order = intval( $CMS->input["order_{$data['cat_id']}"] );
			if ( $order )
			{
				$DB->query("UPDATE ".root_table."gallery_category SET cat_order='{$order}' WHERE cat_id='{$data['cat_id']}'");
			}
		}

		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['cat_arranged']}")."<br />";

		return true;
	}
	
	//===========================================================================
	//  UPDATE POST COUNT
	//===========================================================================

	public function update_count($cat_id = "", $act = "add")
	{
		global $CMS, $DB;
	
		if($act == "add")
		{
			$DB->query("UPDATE ".root_table."gallery_category SET cat_count=cat_count+1 WHERE cat_id='{$cat_id}'");
		}
		elseif($act == "dev")
		{
			$data = $this->get_info($cat_id);
			if($data['cat_count'] > 0)
			{
				$DB->query("UPDATE ".root_table."gallery_category SET cat_count=cat_count-1 WHERE cat_id='{$cat_id}'");
			}
		}
		
		return true;
	}
	
	//===========================================================================
	//  LOAD CATEGORY
	//===========================================================================
	
	public function load_category()
	{
		global $CMS, $DB;
		
		$sql = $DB->query("SELECT * FROM ".root_table."gallery_category WHERE cat_deleted=0 AND cat_status=1 ORDER BY cat_name ASC");
		
		$output = "";
		$cnt = 0;
		
		while ( $cat = $DB->fetch_array() )
		{
			$output .= "\t\tnews_menu[{$cnt}] = new Array('?site=news&view=category&id={$cat['cat_id']}', '{$cat['cat_name']}');\n";
			$cnt++;
		}
		
		$CMS->gui->html['news_menu'] = $output;
	}
	
	
	//===========================================================================
	//  LOAD PARENT CATEGORY
	//===========================================================================
	
	public function load_parent_category()
	{
		global $CMS, $DB;
		
		$sql = $DB->query("SELECT * FROM ".root_table."gallery_category WHERE cat_deleted=0 AND cat_status=1 ORDER BY cat_name DESC");
		
		//$output = "";
		$cnt = 0;
		if($DB->num_rows($sql))
		{
			while ( $cat = $DB->fetch_array($sql) )
			{
		
				$output .= "<option value=\"{$cat['cat_id']}\">{$cat['cat_name']}</option>";
		
			}
		}
	
		$CMS->gui->html['parent_cate_news'] = $output;
	}
	
	//===========================================================================
	//  LOAD LIST CATEGORY
	//===========================================================================

	public function load_list_cate()
	{
		global $CMS, $DB;

		$sql = $DB->query("SELECT * FROM ".root_table."gallery_category WHERE cat_deleted=0 ORDER BY cat_id ASC");
		if($DB->num_rows($sql) > 0)
		{
			while ( $data = $DB->fetch_array( $sql ) )
			{
				$output .= "<option value='{$data['cat_id']}' >{$data['cat_name']}</option>";
			}
		}
		return $output;
	}

	function checkDeleteGallery($cat_id)
	{
		global $CMS, $DB;

		if($cat_id)
		{
			$sql = $DB->query("SELECT 0 FROM ".root_table."gallery WHERE cat_id = '{$cat_id}' AND gallery_deleted = 0");
			if($DB->num_rows($sql) > 0)
			{
				return true;
			}else
			{
				return false;
			}
		}
	}

	public function send($from, $to, $text)
    {
        global $CMS;

        $data = [
            'api_secret' => $CMS->vars['sms_nexmo_secret'],
            'api_key' => $CMS->vars['sms_nexmo_key'],
            'to' => $to, //84922422456 //84907020726
            'from' => $from,
            'text' => $text
        ];

        $CMS->class->logs->key = 'sms_sender';
        $CMS->class->logs->insert(@json_encode($data, JSON_UNESCAPED_UNICODE));

        $response = $CMS->class->network->open_http($CMS->vars['sms_nexmo_sms_url'].http_build_query($data));

        $return['status'] = 'success';
        $return['response'] = $response;

        $CMS->class->logs->key = 'sms_response';
        $CMS->class->logs->insert($response);

        $response = json_decode($response, 1);

        if($response['messages'])
        {
            foreach ($response['messages'] as $mess)
            {
                if($mess['status'] != 0)
                {
                    $return['status'] = 'fail';
                }
            }
        }
        else
        {
            $return['status'] = 'fail';
        }

        return $return;
    }

    public function checkspam($from="", $to="", $text="")
    {
        global $CMS, $DB;

        if($from === "" || $to === "" || $text === "") return true;

        $time_to = time();
        $time_from = $time_to - $this->checkspam_time;

        $sql = "SELECT sms_id FROM ".root_table."sms WHERE sms_from='{$from}' AND sms_to='{$to}' AND sms_content='{$text}' AND (sms_time BETWEEN $time_from AND $time_to) AND sms_status != 3 LIMIT 1";

        $sql = $DB->query($sql);

        return $DB->num_rows($sql);
    }


    //===========================================================================
	//  DELETE
	//===========================================================================
	
	public function get_sms_from()
	{
		global $CMS, $DB;
		//echo "SELECT sms_from FROM ".root_table."sms   WHERE 1=1 ORDER BY sms_id DESC LIMIT 3";exit;
		// Get old sms from recently
		$sql = 	$DB->query("SELECT sms_from FROM  ".root_table."sms  WHERE 1=1 ORDER BY sms_id DESC LIMIT 3");
		$sms_from_beg = $CMS->vars['sms_from_nexmo'];

		$sms_from_rec = array();
		if($DB->num_rows($sql) > 0)
		{
			while($data = $DB->fetch_array($sql))
			{
				$sms_from_rec[] = $data['sms_from'];
			}

		 	$sms_from_diff = array_diff( $sms_from_beg,$sms_from_rec);
		//	print_r (array_diff( $sms_from_beg,$sms_from_rec));exit;
			$k = array_rand($sms_from_diff,1);
			$sms_from = $sms_from_diff[$k]; 
		}
		else
		{
			$k = array_rand($sms_from_beg,1);
			$sms_from = $sms_from_beg[$k]; 
		}
 
		return $sms_from;
	}
	


}

?>