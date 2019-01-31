<?php

use \core\ezy;
use lib\input;


$CMS->user = new class_user;

class class_user {

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
	 * @param @sql_select
	 *		The additional SQL for $sql_select
	 */

	public $sql_select = "";
	
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
	 * @param $list_html
	 *		List user HTML
	 */
	
	public $list_html = "";
	
	/**
	 * @param $group_html
	 *		List user group HTML
	 */
	
	public $group_html = "";
	
	/**
	 * @param $group_time
	 *		Group listed by time
	 */
	
	public $group_time = "";
	
	/**
	 * @param $cache
	 *		Temp store user info
	 */
	 
	public $cache = array();
	
	/**
	 * @param $groupcache
	 *		Temp store group info
	 */
	 
	public $groupcache = array();

	public $session_id;

	public $cache_prefix = 'user';

	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	
	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			$this->html = $CMS->class->template->load_template("skin_user");		
		}
	}
	
	public function listing($sql_add = "")
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("user_display_name");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "U.user_display_name";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "ASC";

		// Group
		if ( isset($CMS->input["group"]) )
		{
			$this->sql_add .= " UG.userg_id='{$CMS->input['group']}' AND ";
		}
		
		// SQL Condition
		$this->sql_add .= " user_deleted=0 AND ";

        $this->sql_add .= $sql_add;

		$sql = "SELECT U.*, UG.* {$this->sql_select} FROM ".root_table."user AS U LEFT JOIN ".root_table."user_group AS UG ON UG.userg_id=U.userg_id WHERE {$this->sql_add} 1=1 ORDER BY userg_is_root DESC, userg_is_admin DESC, UG.userg_title ASC, U.user_is_leader DESC, {$default_field} {$default_order}";

		// Create SQL Query for listing Data
		list($this->show_page, $data) = $DB->fetch_listing($sql, 100, '', '', $CMS->input['page'],$this->cache_prefix.'.user_group');

		return $data;
	}
	
	public function html($data)
	{
		global $CMS, $DB, $member;

		$this->loadhtml();

        $output = "";
		
		if ( isset($CMS->input["end_probationary_period"]) && $CMS->input["end_probationary_period"] == 1 )
		{
			// Display Header
			$output .= $this->html->user_header_probationary();
			
			if ( $data )
			{
				foreach( $data as $result )
				{
					// Convert info
					$result = $CMS->user->convertvalue($result);
					
					// Display Middle
					$output .= $this->html->user_middle_probationary($result);
				}
			}
			else
			{
				// Display No data
				$output .= $this->html->user_none();
	
				// No data
				$CMS->is_error = 1;
			}
		}
		else
		{
			// Display Header
			$output .= $this->html->user_header();
			
			$count = $data;
			if ( $count > 0 )
			{
				$cnt = 1;
				foreach( $data as $result )
				{
					// Convert info
					$result = $CMS->user->convertvalue($result);
					$result['keyrow'] = $cnt;
					$result['rowtr'] = $cnt == $count ? 'last-row' : '';
					$result['old-even'] = $cnt % 2 == 0? 'even' : 'old';
					$cnt += 1;

					// Display Middle
					$output .= $this->html->user_middle($result);
				}
			}
			else
			{
				// Display No data
				$output .= $this->html->user_none();
	
				// No data
				$CMS->is_error = 1;
			}
		}
		
		// Display
		$output .= $this->html->user_footer();
		
		// If the page is giving no data
		if ( $CMS->input['page'] > 1 && $CMS->is_error == 1)
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=user");
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

		$data = "";

		// Check permission to Delete
		if ( $CMS->permit["user_delete"] == true )
		{
			$data .= "<option value='delete_all'>{$CMS->lang['user_action_delete']}</option>";
			$this->control = 1;
		}
			
		$data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

		$CMS->vars['action_controller'] = $data;

		if ( $CMS->vars['action_controller']  OR $CMS->permit["user_search"] == 1 )
		{
			$this->action_control = $this->html->user_control();
		}
		else
		{
			$CMS->vars['action_controller'] = "";
		}
	}

	//===========================================================================
	//  USER DATA
	//===========================================================================

	public function defaultvalue($data)
	{
		global $CMS;
		$data['user_sex'] = isset($data['user_sex']) ? $data['user_sex'] : 1;
		$data['user_isprobrationary'] = isset($data['user_isprobrationary']) ? $data['user_isprobrationary'] : 1;
		$data['user_dayspecify'] = isset($data['user_dayspecify']) ? $data['user_dayspecify'] : 13;
		$data['user_salaryunion'] = isset($data['user_salaryunion']) ? $data['user_salaryunion'] : 50000;

		return $data;
	}
	
	public function convertvalue($data, $type = 0)
	{
		global $CMS, $DB;
	
		//-----------------------------------------------------------
		// PERMISSION
		//-----------------------------------------------------------
		// usert group

	
		// Add Permission
		$data = $this->permission_init( $data );
	
		// Replace search content
		$data = $CMS->class->search->convertvalue($data);

		// Convert Register to GMT
		$data['user_last_visit'] = $CMS->class->date->date_format( $data['user_last_visit'], 1 );
		
		// Time
		$data['user_startjob'] = $data['user_startjob'] ? $CMS->class->date->date_format( $data['user_startjob'], 0 ) : "";
		$data['user_beginjob'] = $data['user_beginjob'] ? $CMS->class->date->date_format( $data['user_beginjob'], 0 ) : "";
		$data['user_endjob'] = $data['user_endjob'] ? $CMS->class->date->date_format( $data['user_endjob'], 0 ) : "";
		
		// Avatar

		$data['user_comment_avatar'] = $data['user_avatar'] ? "<img align='left' class='avatar' src='{$CMS->vars['upload_url']}/avatar/thumbnail/{$data['user_avatar']}' width='100px', height='100px'/>" : "<img align='left' class='avatar' src='assets/img/avatar-1-128.png' width='100px', height='100px'/>";
		$data['user_avatar'] = $data['user_avatar'] ? "<img align='absmiddle' class='avatar' src='{$CMS->vars['upload_url']}/avatar/thumbnail/{$data['user_avatar']}' width='100px', height='100px'/>" : "<img align='absmiddle' class='avatar' src='assets/img/avatar-1-128.png' width='100px', height='100px'/>";
		$data['user_avatarcard'] = $data['user_avatarcard'] ? "<img align='absmiddle' class='avatar' src='{$CMS->vars['upload_url']}/avatar/thumbnail/{$data['user_avatarcard']}' width='100px', height='100px'/>" : "<img align='absmiddle' class='avatar' src='assets/img/avatar-1-128.png' width='100px', height='100px'/>";
		
		// Sex
		$data['user_sex'] = $CMS->lang["user_sex_{$data['user_sex']}"];
		
		//Probrationary
		$data['user_isprobrationary'] = $CMS->lang["user_isprobrationary_{$data['user_isprobrationary']}"];

		// Group 
		$data['userg_prefix_html'] = isset($data['userg_prefix_html']) ? str_replace("&#39;", "'", $data['userg_prefix_html']) : "";
		$data['userg_suffix_suffix'] = isset($data['userg_suffix_suffix']) ? str_replace("&#39;", "'", $data['userg_suffix_suffix']) : "";

		// Check permission to read Info
		$data['user_profile_name'] = $data['user_display_name'];
		$data['user_notify_msg'] = $data['user_notify_msg'];
		$data['user_display_name'] = $CMS->permit["user_read"] == true ? "<a href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}'>{$data['user_display_name']}</a>" : "{$data['user_display_name']}";

		if ( $data['user_status'] == 0 )
        {
        	$data['user_status'] = " <img src='{$CMS->vars['img_url']}/icon_locked.png' title='{$CMS->lang['icon_locked']}' align='absmiddle' /> ";
        }
		else
		{
			$data['user_status'] = "";	
		}

		if ( $data['user_is_leader'] == 1 )
        {
        	$data['user_is_leader'] = " <img src='{$CMS->vars['img_url']}/icon_leader.png' align='absmiddle' /> ";
        }
        else
        {
        	$data['user_is_leader'] = " <img src='{$CMS->vars['img_url']}/icon_user.png' align='absmiddle' /> ";
        }
        
        $data['user_is_leader'] = $data['user_status'] ? $data['user_status'] : $data['user_is_leader'];
		
		// Location
		$location_array = isset($CMS->vars['company_branch_array']) ? unserialize($CMS->vars['company_branch_array']) : [];
		$data['user_location'] = isset($location_array[$data['user_location']]) ? $location_array[$data['user_location']] : "";

		// Bgcolor
		$data['bgcolor'] = $this->record_cnt % 2 != 0 ? "#FFFFFF" : "#F6F6F6";
		
		// Count
		$data['record_cnt'] = $this->record_cnt;
		
		$this->record_cnt++;
		
		return $data;
	}
	
	public function editvalue($data)
	{
		global $CMS, $DB;
		
		// Time
		$data['user_startjob'] = $data['user_startjob'] ? $CMS->class->date->date_format( $data['user_startjob'], 0 ) : "";
		$data['user_beginjob'] = $data['user_beginjob'] ? $CMS->class->date->date_format( $data['user_beginjob'], 0 ) : "";
		$data['user_endjob'] = $data['user_endjob'] ? $CMS->class->date->date_format( $data['user_endjob'], 0 ) : "";
		
		// Avatar
		$data['user_avatar'] = $data['user_avatar'] ? "<img align='absmiddle' src='{$CMS->vars['upload_url']}/avatar/thumbnail/{$data['user_avatar']}' />" : "";
		$data['user_avatarcard'] = $data['user_avatarcard'] ? "<img align='absmiddle' class='avatar' src='{$CMS->vars['upload_url']}/avatar/thumbnail/{$data['user_avatarcard']}' />" : "";
		
		return $data;	
	}
	
	public function searchvalue($data)
	{
		global $CMS, $DB;

		
		
		return $data;
	}
	
	//===========================================================================
	//  USER INFO
	//===========================================================================
	
	public function get_info( $record_id = 0, $field_name = "", $load_deleted = 0 )
	{
		global $CMS, $DB, $member;

        $CMS->input['site'] = isset($CMS->input['site']) ? trim($CMS->input['site']) : null;

		if ( ! $record_id AND $CMS->input['site'] == "user" )
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

		// Load cache
		//if ( isset($this->cache[$record_id]) )
		//{
		//	$data = $this->cache[$record_id];
		// Tam hoi dong load user tu cache ra
		// if ( $data = unserialize($CMS->class->cache->load("userprofile_{$record_id}")) )
		// {
		// 	if ( $field_name )
		// 	{
		// 		if ( $data[$field_name] )
		// 		{
		// 			return $data[$field_name];
		// 		}
		// 		else
		// 		{
		// 			return false;
		// 		}
		// 	}
 
		// 	return $data;
		// }
		
		// Create sql search
		if ( $CMS->class->input->is_email($record_id) == true )
		{
			$sql_search = "U.user_email='{$record_id}'";
		}
		else if ( $CMS->class->input->is_nan($record_id) )
		{
			$sql_search = "U.user_name='{$record_id}' OR U.user_display_name='{$record_id}'";
		}
		else
		{
			$sql_search = "U.user_id='{$record_id}'";
		}

		if ( $load_deleted != 1 )
		{
			$sql_delete = " AND U.user_deleted='{$load_deleted}' ";
		}

		// Continue
        $sql = "SELECT U.*, G.userg_title, G.userg_prefix_html AS user_prefix, G.userg_suffix_html AS user_suffix FROM ".root_table."user AS U LEFT JOIN ".root_table."user_group AS G ON U.userg_id=G.userg_id WHERE ({$sql_search}) {$sql_delete} ORDER BY U.user_id DESC LIMIT 1";

		$results = $DB->fetch_data($sql, $this->cache_prefix.'.user_group');
		$data = isset($results[0]) ? $results[0] : null;

		if ( $data )
		{
			// Add Permission
			$data = $this->permission_init( $data );
		
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
			$sql_add = "{$field}!='{$except_value}' AND";
		}

		$sql = "SELECT count(0) cnt FROM ".root_table."user WHERE {$sql_add} {$field}='{$value}' AND user_deleted=0";

		return $DB->fetch_data($sql, $this->cache_prefix)[0]['cnt'];
	}

	//===========================================================================
	//  ADD USEr
	//===========================================================================
	
	public function add($data = [], $check_valid = 1)
	{
		global $CMS, $DB, $member;

		if($data)
        {
            $CMS->input = array_merge($CMS->input, $data);
        }

		// User input
		$user_name = trim($CMS->input["user_name"]);
		$user_display_name = trim($CMS->input["user_display_name"]);
		$user_password = $CMS->class->editor->input("user_password");
		$user_repassword = $CMS->class->editor->input("user_repassword");
		$userg_id = intval($CMS->input["userg_id"]);
		$user_email = $CMS->input["user_email"];
		$user_signature = $CMS->input["user_signature"];
		$user_location = $CMS->input["user_location"];
		$user_is_leader = $CMS->input["user_is_leader"];
		$user_status = intval($CMS->input["user_status"]);
		$user_is_staff = intval($CMS->input["user_is_staff"]);
		
		// Extends
		$user_jobtitle = $CMS->input['user_jobtitle'];
		$user_bdday = $CMS->input['user_bdday'];
		$user_bdmonth = $CMS->input['user_bdmonth'];
		$user_bdyear = $CMS->input['user_bdyear'];
		$user_sex = intval($CMS->input['user_sex']);
		$user_address = $CMS->input['user_address'];
		$user_isprobrationary = intval($CMS->input['user_isprobrationary']);
		$user_startjob = $CMS->input['user_startjob'] ? $CMS->class->date->date2time($CMS->input['user_startjob']) : 0;
		$user_beginjob = $CMS->input['user_beginjob'] ? $CMS->class->date->date2time($CMS->input['user_beginjob']) : 0;
		$user_endjob = $CMS->input['user_endjob'] ? $CMS->class->date->date2time($CMS->input['user_endjob']) : 0;
		$user_avatar = $CMS->input['user_avatar'];
		$user_avatarcard = $CMS->input['user_avatarcard'];
		$user_phone = $CMS->input['user_phone'];
		$user_message = $CMS->class->editor->input("user_message");
		$user_salaryunion = intval($CMS->input['user_salaryunion']);
		
		// Private Extends
		if ( $CMS->permit['payroll_edit'] == true )
		{
			$user_basicsalary = intval($CMS->input['user_basicsalary']);
			$user_benefit = intval($CMS->input['user_benefit']);
			$user_dayspecify = intval($CMS->input['user_dayspecify']);
			$user_nightsalary = intval($CMS->input['user_nightsalary']);
			$user_insure = intval($CMS->input['user_insure']);
			$user_insure2 = intval($CMS->input['user_insure2']);
			$user_contractperiod = $CMS->input['user_contractperiod'];
			$user_bankaccount = $CMS->input['user_bankaccount'];
			$user_probationary = intval($CMS->input['user_probationary']);
			$user_dayprobationary = intval($CMS->input['user_dayprobationary']);
		}
		else
		{
			$user_basicsalary = 0;
			$user_benefit = 0;
			$user_dayspecify = 0;
			$user_nightsalary = 0;
			$user_insure = 0;
			$user_insure2 = 0;
			$user_probationary = 0;
			$user_dayprobationary = 0;
		}

		if($check_valid)
        {
            // Check input
            if ( ! $user_name ) { $_SESSION['msg'] .= "{$CMS->lang['incomplete_username']}"; return false; }

            if ( $this->check_exist("user_name", $user_name) == true ) { $_SESSION['msg'] .= "{$CMS->lang['user_exist']}"; return false; }

            if ( ! $user_email ) { $_SESSION['msg'] .= "{$CMS->lang['incomplete_email']}"; return false; }

            //if ( $this->check_exist("user_email", $user_email) == true ) { $_SESSION['msg'] .= "{$CMS->lang['email_exist']}"; return false; }

            if ( $user_password != $user_repassword ) { $_SESSION['msg'] .= "{$CMS->lang['wrong_password']}"; return false; }
        }


		// Insert
		$user_password = md5($user_password);
		
		// If not display name
		$user_display_name = $user_display_name ? $user_display_name : $user_name;
		
		$user_login_key = $this->create_login_key();

		// Check upload
		$file_tmp = isset($_FILES['user_avatar']['tmp_name']) ? $_FILES['user_avatar']['tmp_name'] : "";
		$file_name = isset($_FILES['user_avatar']['name']) ? $_FILES['user_avatar']['name'] : "";
		$file_type = isset($_FILES['user_avatar']['type']) ? $_FILES['user_avatar']['type'] : "";
		$file_size = isset($_FILES['user_avatar']['size']) ? $_FILES['user_avatar']['size'] : "";
		$file_error = isset($_FILES['user_avatar']['error']) ? $_FILES['user_avatar']['error'] : "";
		
		$file_ext = $CMS->class->attachment->get_ext( $file_name );
		$file_name = str_replace( " ", "_", $file_name );
		$file_location = "avatar_".strtolower(time()."_".$file_name);
		
		if ( $file_name )
		{
			if ( $CMS->class->attachment->is_image( $file_name, $file_ext ) == false ) { $CMS->errormsg .= "{$CMS->lang['invalid_upload_file']}"; return false; }
			$CMS->class->image->is_dir("{$CMS->vars['upload_dir']}/avatar");
			@copy($file_tmp, "{$CMS->vars['upload_dir']}/avatar/".$file_location) or die ("Could not be upload.");
			$CMS->class->image->is_dir("{$CMS->vars['upload_dir']}/avatar/thumbnail");
			$CMS->class->image->resize( "{$CMS->vars['upload_dir']}/avatar/".$file_location, "{$CMS->vars['upload_dir']}/avatar/thumbnail/".$file_location, 140 );
			
			$user_avatar = $file_location;
		}
		
		 

		// Hash + salt
		$salt = $this->create_pwd_salt(5);
		$salt = addslashes($salt);
		$pwdhash = $this->create_pwd_hash( $salt, $user_password );

		$sql_add_fields = "";
		$sql_add_values = "";

        if(input::get("store_id") !== null) {
            $sql_add_fields .= ", store_id";
            $sql_add_values .= ", " . (input::get("store_id") * 1);
        }

		// Add user
		$DB->query("INSERT INTO ".root_table."user ( user_name, user_display_name, user_login_key, user_email, userg_id, user_joined, user_ip_address, user_last_visit, user_last_activity, user_hash, user_salt, user_status, user_is_staff {$sql_add_fields})
		VALUES ( '{$user_name}', '{$user_display_name}', '{$user_login_key}', '{$user_email}', '{$userg_id}', '".time()."', '".$_SERVER['REMOTE_ADDR']."', '".time()."', '".time()."', '{$pwdhash}', '{$salt}', '{$user_status}', '{$user_is_staff}' {$sql_add_values});");

		// Get info
		$user = $this->get_info($user_name);

		//update service ids
        $this->setServices($user['user_id'], input::get('product_ids'));

        // Check if not change user group
        if ( $CMS->vars['is_admin'] == true )
        {
            if ( $CMS->input["is_update"] != 1 )
            {
                $permission = $this->get_permission();
            }
            else
            {
                $DB->query("SELECT * FROM ".root_table."user_group WHERE userg_id='{$userg_id}'");
                $group = $DB->fetch_array();

                $permission = $group['userg_permission'];
            }
        }
        else
        {
            $permission = $data['user_permission'];
        }
		
		$DB->query("UPDATE ".root_table."user SET user_permission='{$permission}', userg_id='{$userg_id}' WHERE user_id='{$user['user_id']}'");

        //Update commission
        $this->update_commission('user', $_POST['commission_data'], $user['user_id']);

		// Delete cache
		$CMS->class->cache->mdelete("user");

		// Get info
		$data = $this->get_info($user['user_id']);
		
		    if($CMS->vars['web_free']== 1 AND $userg_id != 1 AND intval($CMS->vars['ibe_synced']) == 1)//Not Root admin
	    	{
	    		$data_api['original_id']   		  = "{$data['user_id']}";
	    		$data_api['staff_name']   		  = "{$data['user_name']}";
	    		$data_api['staff_display_name']   = "{$data['user_display_name']}";
	    		$data_api['staff_email']   		  = "{$data['user_email']}";
	    		if($user_avatar != "")
	    		{
	    			$data_api['staff_avatar']   		= "{$user_avatar}";
	    			$data_api['staff_avatar_link']   = $CMS->vars['upload_url'].'/avatar/'.$user_avatar;
	    		}
	    		$data_api['site_id']   		  = "{$CMS->vars['site_id']}";
	    		$CMS->api->whm->execute('staff_add', $data_api); 
	     	}

		$CMS->class->logs->key = "user_{$data['user_id']}";
		$_SESSION['msg'] .= $CMS->class->logs->insert("{$CMS->lang['user_added']} <b>{$data['user_name']}</b> ({$data['user_display_name']})");

		return $data;
	}
	

	//===========================================================================
	//  ADD USEr
	//===========================================================================
	
	public function add_whm($data = [], $check_valid = 1)
	{
		global $CMS, $DB, $member;

		if($data)
        {
            $CMS->input = array_merge($CMS->input, $data);
        }

		// User input
		$user_name = trim($CMS->input["user_name"]);
		$user_display_name = trim($CMS->input["user_display_name"]);
		$user_password = $CMS->class->editor->input("user_password");
		$user_repassword = $CMS->class->editor->input("user_repassword");
		
		$userg_id = intval($CMS->input["userg_id"]);
		$user_email = $CMS->input["user_email"];
		$reseller_id  = intval(trim($CMS->input["reseller_id"]));

		$user_signature = $CMS->input["user_signature"];
		$user_location = $CMS->input["user_location"];
		$user_is_leader = $CMS->input["user_is_leader"];
		$user_status = intval($CMS->input["user_status"]);
		$user_is_staff = intval($CMS->input["user_is_staff"]);
		
		// Extends
		$user_jobtitle = $CMS->input['user_jobtitle'];
		$user_bdday = $CMS->input['user_bdday'];
		$user_bdmonth = $CMS->input['user_bdmonth'];
		$user_bdyear = $CMS->input['user_bdyear'];
		$user_sex = intval($CMS->input['user_sex']);
		$user_address = $CMS->input['user_address'];
		$user_isprobrationary = intval($CMS->input['user_isprobrationary']);
		$user_startjob = $CMS->input['user_startjob'] ? $CMS->class->date->date2time($CMS->input['user_startjob']) : 0;
		$user_beginjob = $CMS->input['user_beginjob'] ? $CMS->class->date->date2time($CMS->input['user_beginjob']) : 0;
		$user_endjob = $CMS->input['user_endjob'] ? $CMS->class->date->date2time($CMS->input['user_endjob']) : 0;
		$user_avatar = $CMS->input['user_avatar'];
		$user_avatarcard = $CMS->input['user_avatarcard'];
		$user_phone = $CMS->input['user_phone'];
		$user_message = $CMS->class->editor->input("user_message");
		$user_salaryunion = intval($CMS->input['user_salaryunion']);
		
		// Private Extends
		if ( $CMS->permit['payroll_edit'] == true )
		{
			$user_basicsalary = intval($CMS->input['user_basicsalary']);
			$user_benefit = intval($CMS->input['user_benefit']);
			$user_dayspecify = intval($CMS->input['user_dayspecify']);
			$user_nightsalary = intval($CMS->input['user_nightsalary']);
			$user_insure = intval($CMS->input['user_insure']);
			$user_insure2 = intval($CMS->input['user_insure2']);
			$user_contractperiod = $CMS->input['user_contractperiod'];
			$user_bankaccount = $CMS->input['user_bankaccount'];
			$user_probationary = intval($CMS->input['user_probationary']);
			$user_dayprobationary = intval($CMS->input['user_dayprobationary']);
		}
		else
		{
			$user_basicsalary = 0;
			$user_benefit = 0;
			$user_dayspecify = 0;
			$user_nightsalary = 0;
			$user_insure = 0;
			$user_insure2 = 0;
			$user_probationary = 0;
			$user_dayprobationary = 0;
		}

		if($check_valid)
        {
            // Check input
            if ( ! $user_name ) { $_SESSION['msg'] .= "{$CMS->lang['incomplete_username']}"; return false; }

            if ( $this->check_exist("user_name", $user_name) == true ) { $_SESSION['msg'] .= "{$CMS->lang['user_exist']}"; return false; }

            if ( ! $user_email ) { $_SESSION['msg'] .= "{$CMS->lang['incomplete_email']}"; return false; }

            //if ( $this->check_exist("user_email", $user_email) == true ) { $_SESSION['msg'] .= "{$CMS->lang['email_exist']}"; return false; }

            if ( $user_password != $user_repassword ) { $_SESSION['msg'] .= "{$CMS->lang['wrong_password']}"; return false; }
        }


		// Insert
		$user_password = md5($user_password);
		
		// If not display name
		$user_display_name = $user_display_name ? $user_display_name : $user_name;
		
		$user_login_key = $this->create_login_key();

		// Check upload
		$file_tmp = isset($_FILES['user_avatar']['tmp_name']) ? $_FILES['user_avatar']['tmp_name'] : "";
		$file_name = isset($_FILES['user_avatar']['name']) ? $_FILES['user_avatar']['name'] : "";
		$file_type = isset($_FILES['user_avatar']['type']) ? $_FILES['user_avatar']['type'] : "";
		$file_size = isset($_FILES['user_avatar']['size']) ? $_FILES['user_avatar']['size'] : "";
		$file_error = isset($_FILES['user_avatar']['error']) ? $_FILES['user_avatar']['error'] : "";
		
		$file_ext = $CMS->class->attachment->get_ext( $file_name );
		$file_name = str_replace( " ", "_", $file_name );
		$file_location = "avatar_".strtolower(time()."_".$file_name);
		
		if ( $file_name )
		{
			if ( $CMS->class->attachment->is_image( $file_name, $file_ext ) == false ) { $CMS->errormsg .= "{$CMS->lang['invalid_upload_file']}"; return false; }
			$CMS->class->image->is_dir("{$CMS->vars['upload_dir']}/avatar");
			@copy($file_tmp, "{$CMS->vars['upload_dir']}/avatar/".$file_location) or die ("Could not be upload.");
			$CMS->class->image->is_dir("{$CMS->vars['upload_dir']}/avatar/thumbnail");
			$CMS->class->image->resize( "{$CMS->vars['upload_dir']}/avatar/".$file_location, "{$CMS->vars['upload_dir']}/avatar/thumbnail/".$file_location, 140 );
			
			$user_avatar = $file_location;
		}
		
		 

		// Hash + salt
		$salt = $this->create_pwd_salt(5);
		$salt = addslashes($salt);
		$pwdhash = $this->create_pwd_hash( $salt, $user_password );

		// Add user
		$DB->query("INSERT INTO ".root_table."user ( user_name, user_display_name, user_login_key, user_email, userg_id, user_joined, user_ip_address, user_last_visit, user_last_activity, user_hash, user_salt, user_status, user_is_staff,reseller_id )
		VALUES ( '{$user_name}', '{$user_display_name}', '{$user_login_key}', '{$user_email}', '{$userg_id}', '".time()."', '".$_SERVER['REMOTE_ADDR']."', '".time()."', '".time()."', '{$pwdhash}', '{$salt}', '{$user_status}', '{$user_is_staff}', '{$reseller_id}');");

		// Get info
		$user = $this->get_info($user_name);

        // Check if not change user group
        if ( $CMS->vars['is_admin'] == true )
        {
            if ( $CMS->input["is_update"] != 1 )
            {
                $permission = $this->get_permission();
            }
            else
            {
                $DB->query("SELECT * FROM ".root_table."user_group WHERE userg_id='{$userg_id}'");
                $group = $DB->fetch_array();

                $permission = $group['userg_permission'];
            }
        }
        else
        {
            $permission = $data['user_permission'];
        }
		
		$DB->query("UPDATE ".root_table."user SET user_permission='{$permission}', userg_id='{$userg_id}' WHERE user_id='{$user['user_id']}'");

        //Update commission
        $this->update_commission('user', $_POST['commission_data'], $user['user_id']);

		// Delete cache
		$CMS->class->cache->mdelete("user");

		// Get info
		$data = $this->get_info($user['user_id']);
		 

		$CMS->class->logs->key = "user_{$data['user_id']}";
		$_SESSION['msg'] .= $CMS->class->logs->insert("{$CMS->lang['user_added']} <b>{$data['user_name']}</b> ({$data['user_display_name']})");

		return $data;
	}
	

	//===========================================================================
	//  EDIT USEr
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		// Get info
		$data = $this->get_info();

		// User input
		$user_name = trim($CMS->input["user_name"]);
		$user_display_name = trim($CMS->input["user_display_name"]);
		$user_password = $CMS->class->editor->input("user_password");
		$user_repassword = $CMS->class->editor->input("user_repassword");
		$userg_id = intval($CMS->input["userg_id"]);
		$user_email = $CMS->input["user_email"];
		$user_signature = $CMS->input["user_signature"];
		$user_is_leader = $CMS->input["user_is_leader"];
		$user_status = intval($CMS->input["user_status"]);
		$user_location = $CMS->input["user_location"];
		$user_is_staff = intval($CMS->input["user_is_staff"]);
		
		// Extends
		$user_jobtitle = $CMS->input['user_jobtitle'];
		$user_bdday = $CMS->input['user_bdday'];
		$user_bdmonth = $CMS->input['user_bdmonth'];
		$user_bdyear = $CMS->input['user_bdyear'];
		$user_sex = intval($CMS->input['user_sex']);
		$user_address = $CMS->input['user_address'];
		$user_isprobrationary = intval($CMS->input['user_isprobrationary']);
		$user_startjob = $CMS->input['user_startjob'] ? $CMS->class->date->date2time($CMS->input['user_startjob']) : 0;
		$user_beginjob = $CMS->input['user_beginjob'] ? $CMS->class->date->date2time($CMS->input['user_beginjob']) : 0;
		$user_endjob = $CMS->input['user_endjob'] ? $CMS->class->date->date2time($CMS->input['user_endjob']) : 0;
		$user_avatar = $CMS->input['user_avatar'];
		$user_avatarcard = $CMS->input['user_avatarcard'];
		$user_phone = $CMS->input['user_phone'];
		$user_message = $CMS->class->editor->input("user_message");
		$user_salaryunion = intval($CMS->input['user_salaryunion']);
		
		// Private Extends
		if ( $CMS->permit['payroll_edit'] == true )
		{
			$user_basicsalary = intval($CMS->input['user_basicsalary']);
			$user_benefit = intval($CMS->input['user_benefit']);
			$user_dayspecify = intval($CMS->input['user_dayspecify']);
			$user_nightsalary = intval($CMS->input['user_nightsalary']);
			$user_insure = intval($CMS->input['user_insure']);
			$user_insure2 = intval($CMS->input['user_insure2']);
			$user_contractperiod = $CMS->input['user_contractperiod'];
			$user_bankaccount = $CMS->input['user_bankaccount'];
			$user_probationary = intval($CMS->input['user_probationary']);
			$user_dayprobationary = intval($CMS->input['user_dayprobationary']);
			
			//$sql_add = " user_basicsalary='{$user_basicsalary}', user_benefit='{$user_benefit}', user_dayspecify='{$user_dayspecify}', user_nightsalary='{$user_nightsalary}', user_insure='{$user_insure}', user_insure2='{$user_insure2}', user_contractperiod='{$user_contractperiod}', user_bankaccount='{$user_bankaccount}', user_probationary='{$user_probationary}', user_dayprobationary='{$user_dayprobationary}', ";
		}
		
		// Check if not change user group
		if ( $CMS->vars['is_admin'] == true )
		{
			if ( $CMS->input["is_update"] != 1 )
			{
				$permission = $this->get_permission();
			}
			else
			{
				$DB->query("SELECT * FROM ".root_table."user_group WHERE userg_id='{$userg_id}'");
				$group = $DB->fetch_array();
				
				$permission = $group['userg_permission'];
			}
		}
		else
		{
			$permission = $data['user_permission'];	
		}
		
		if ( ! $user_name ) { $CMS->errormsg .= "{$CMS->lang['incomplete_username']}"; return false; }
		
		if ( $this->check_exist("user_name", $user_name, $data['user_name']) == true ) { $CMS->errormsg .= "{$CMS->lang['user_exist']}"; return false; }
		
		if ( ! $user_email ) { $CMS->errormsg .= "{$CMS->lang['incomplete_email']}"; return false; }
		
		//if ( $this->check_exist("user_email", $user_email, $data['user_email']) == true ) { $CMS->errormsg .= "{$CMS->lang['email_exist']}"; return false; }
		
		if ( $user_password != $user_repassword ) { $CMS->errormsg .= "{$CMS->lang['wrong_password']}"; return false; }
		
		if ( $user_password ) { $this->create_new_password( $data['user_id'], $user_password ); }
		
		// Check upload
		$file_tmp = isset($_FILES['user_avatar']['tmp_name']) ? $_FILES['user_avatar']['tmp_name'] : "";
		$file_name = isset($_FILES['user_avatar']['name']) ? $_FILES['user_avatar']['name'] : "";
		$file_type = isset($_FILES['user_avatar']['type']) ? $_FILES['user_avatar']['type'] : "";
		$file_size = isset($_FILES['user_avatar']['size']) ? $_FILES['user_avatar']['size'] : "";
		$file_error = isset($_FILES['user_avatar']['error']) ? $_FILES['user_avatar']['error'] : "";

		$file_ext = $CMS->class->attachment->get_ext( $file_name );
		$file_name = str_replace( " ", "_", $file_name );
		$file_location = "avatar_".strtolower(time()."_".$file_name);
		
		if ( $file_name )
		{		
			if ( $CMS->class->attachment->is_image( $file_name, $file_ext ) == false ) { $CMS->errormsg .= "{$CMS->lang['invalid_upload_file']}"; return false; }
			$CMS->class->image->is_dir("{$CMS->vars['upload_dir']}/avatar");
			$result = @copy($file_tmp, "{$CMS->vars['upload_dir']}/avatar/".$file_location) or die ("Could not be upload.");

			if ( $result )
			{
				$CMS->class->image->is_dir("{$CMS->vars['upload_dir']}/avatar/thumbnail");
				$CMS->class->image->resize( "{$CMS->vars['upload_dir']}/avatar/".$file_location, "{$CMS->vars['upload_dir']}/avatar/thumbnail/".$file_location, 140 );
				
				@unlink("{$CMS->vars['upload_dir']}/avatar/{$data['user_avatar']}");
			}
			
			$user_avatar = $file_location;
		}
		else
		{
			$user_avatar = $data['user_avatar'];
		}
		
		 //Update commission
        $this->update_commission('user', $_POST['commission_data'], $data['user_id']);

		$sql_add_update = "";
		if(input::get("store_id") !== null) {
		    $sql_add_update = ", store_id=". (input::get("store_id") * 1);
        }

		// Insert
		$DB->query("UPDATE ".root_table."user SET user_avatar='{$user_avatar}', user_avatarcard='{$user_avatarcard}', userg_id='{$userg_id}', user_name='{$user_name}', user_email='{$user_email}', user_display_name='{$user_display_name}', user_permission='{$permission}', user_status='{$user_status}', user_is_staff='{$user_is_staff}'  {$sql_add_update} WHERE user_id={$data['user_id']}");

        //update service ids
        $this->setServices($data['user_id'], input::get('product_ids'));

		// Delete cache
		$CMS->class->cache->mdelete("user");
		$CMS->class->cache->delete("usertask_{$data['user_id']}");
		
		// Load again
		$data = $this->get_info();
		
		    // Sync data free_website
	        if($CMS->vars['web_free']== 1  AND $userg_id != 1 AND intval($CMS->vars['ibe_synced']) == 1)
	    	{
				 
	    		$data_api['original_id']   		  = "{$data['user_id']}";
	    		$data_api['staff_name']   		  = "{$data['user_name']}";
	    		$data_api['staff_display_name']   = "{$data['user_display_name']}";
	    		$data_api['staff_email']   		  = "{$data['user_email']}";
	    		$data_api['staff_status']   		  = "{$data['user_status']}";
	    		if ( $file_name )
	    		{
	    			$data_api['staff_avatar_link']  = $CMS->vars['upload_url'].'/avatar/'.$user_avatar;
	    		}
	    		$data_api['staff_avatar']   	= "{$user_avatar}";
	    		$data_api['site_id']   		    = "{$CMS->vars['site_id']}";
	    		$CMS->api->whm->execute('staff_edit', $data_api); 
	    	}

		$CMS->class->logs->key = "user_{$data['user_id']}";
		$_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['user_edited']} <b>{$data['user_name']}</b> ({$data['user_display_name']})");

		return $data;
	}
	

	//===========================================================================
	//  EDIT USEr
	//===========================================================================
	
	public function edit_whm()
	{
		global $CMS, $DB, $member;
		
		// Get info
		$data = $this->get_info();

		// User input
		$user_name = trim($CMS->input["user_name"]);
		$user_display_name = trim($CMS->input["user_display_name"]);
		$user_password = $CMS->class->editor->input("user_password");
		$user_repassword = $CMS->class->editor->input("user_repassword");
		$userg_id = intval($CMS->input["userg_id"]);
		$user_email = $CMS->input["user_email"];

		$reseller_id = intval($CMS->input["reseller_id"]);


		$user_signature = $CMS->input["user_signature"];
		$user_is_leader = $CMS->input["user_is_leader"];
		$user_status = intval($CMS->input["user_status"]);
		$user_location = $CMS->input["user_location"];
		$user_is_staff = intval($CMS->input["user_is_staff"]);
		
		// Extends
		$user_jobtitle = $CMS->input['user_jobtitle'];
		$user_bdday = $CMS->input['user_bdday'];
		$user_bdmonth = $CMS->input['user_bdmonth'];
		$user_bdyear = $CMS->input['user_bdyear'];
		$user_sex = intval($CMS->input['user_sex']);
		$user_address = $CMS->input['user_address'];
		$user_isprobrationary = intval($CMS->input['user_isprobrationary']);
		$user_startjob = $CMS->input['user_startjob'] ? $CMS->class->date->date2time($CMS->input['user_startjob']) : 0;
		$user_beginjob = $CMS->input['user_beginjob'] ? $CMS->class->date->date2time($CMS->input['user_beginjob']) : 0;
		$user_endjob = $CMS->input['user_endjob'] ? $CMS->class->date->date2time($CMS->input['user_endjob']) : 0;
		$user_avatar = $CMS->input['user_avatar'];
		$user_avatarcard = $CMS->input['user_avatarcard'];
		$user_phone = $CMS->input['user_phone'];
		$user_message = $CMS->class->editor->input("user_message");
		$user_salaryunion = intval($CMS->input['user_salaryunion']);
		
		// Private Extends
		if ( $CMS->permit['payroll_edit'] == true )
		{
			$user_basicsalary = intval($CMS->input['user_basicsalary']);
			$user_benefit = intval($CMS->input['user_benefit']);
			$user_dayspecify = intval($CMS->input['user_dayspecify']);
			$user_nightsalary = intval($CMS->input['user_nightsalary']);
			$user_insure = intval($CMS->input['user_insure']);
			$user_insure2 = intval($CMS->input['user_insure2']);
			$user_contractperiod = $CMS->input['user_contractperiod'];
			$user_bankaccount = $CMS->input['user_bankaccount'];
			$user_probationary = intval($CMS->input['user_probationary']);
			$user_dayprobationary = intval($CMS->input['user_dayprobationary']);
			
			//$sql_add = " user_basicsalary='{$user_basicsalary}', user_benefit='{$user_benefit}', user_dayspecify='{$user_dayspecify}', user_nightsalary='{$user_nightsalary}', user_insure='{$user_insure}', user_insure2='{$user_insure2}', user_contractperiod='{$user_contractperiod}', user_bankaccount='{$user_bankaccount}', user_probationary='{$user_probationary}', user_dayprobationary='{$user_dayprobationary}', ";
		}
		
		// Check if not change user group
		if ( $CMS->vars['is_admin'] == true )
		{
			if ( $CMS->input["is_update"] != 1 )
			{
				$permission = $this->get_permission();
			}
			else
			{
				$DB->query("SELECT * FROM ".root_table."user_group WHERE userg_id='{$userg_id}'");
				$group = $DB->fetch_array();
				
				$permission = $group['userg_permission'];
			}
		}
		else
		{
			$permission = $data['user_permission'];	
		}
		
		if ( ! $user_name ) { $CMS->errormsg .= "{$CMS->lang['incomplete_username']}"; return false; }
		
		if ( $this->check_exist("user_name", $user_name, $data['user_name']) == true ) { $CMS->errormsg .= "{$CMS->lang['user_exist']}"; return false; }
		
		if ( ! $user_email ) { $CMS->errormsg .= "{$CMS->lang['incomplete_email']}"; return false; }
		
		//if ( $this->check_exist("user_email", $user_email, $data['user_email']) == true ) { $CMS->errormsg .= "{$CMS->lang['email_exist']}"; return false; }
		
		if ( $user_password != $user_repassword ) { $CMS->errormsg .= "{$CMS->lang['wrong_password']}"; return false; }
		
		if ( $user_password ) { $this->create_new_password( $data['user_id'], $user_password ); }
		
		// Check upload
		$file_tmp = isset($_FILES['user_avatar']['tmp_name']) ? $_FILES['user_avatar']['tmp_name'] : "";
		$file_name = isset($_FILES['user_avatar']['name']) ? $_FILES['user_avatar']['name'] : "";
		$file_type = isset($_FILES['user_avatar']['type']) ? $_FILES['user_avatar']['type'] : "";
		$file_size = isset($_FILES['user_avatar']['size']) ? $_FILES['user_avatar']['size'] : "";
		$file_error = isset($_FILES['user_avatar']['error']) ? $_FILES['user_avatar']['error'] : "";

		$file_ext = $CMS->class->attachment->get_ext( $file_name );
		$file_name = str_replace( " ", "_", $file_name );
		$file_location = "avatar_".strtolower(time()."_".$file_name);
		
		if ( $file_name )
		{		
			if ( $CMS->class->attachment->is_image( $file_name, $file_ext ) == false ) { $CMS->errormsg .= "{$CMS->lang['invalid_upload_file']}"; return false; }
			$CMS->class->image->is_dir("{$CMS->vars['upload_dir']}/avatar");
			$result = @copy($file_tmp, "{$CMS->vars['upload_dir']}/avatar/".$file_location) or die ("Could not be upload.");

			if ( $result )
			{
				$CMS->class->image->is_dir("{$CMS->vars['upload_dir']}/avatar/thumbnail");
				$CMS->class->image->resize( "{$CMS->vars['upload_dir']}/avatar/".$file_location, "{$CMS->vars['upload_dir']}/avatar/thumbnail/".$file_location, 140 );
				
				@unlink("{$CMS->vars['upload_dir']}/avatar/{$data['user_avatar']}");
			}
			
			$user_avatar = $file_location;
		}
		else
		{
			$user_avatar = $data['user_avatar'];
		}
		
		 //Update commission
        $this->update_commission('user', $_POST['commission_data'], $data['user_id']);
	 
		// Insert
		$DB->query("UPDATE ".root_table."user SET user_avatar='{$user_avatar}', user_avatarcard='{$user_avatarcard}', userg_id='{$userg_id}', user_name='{$user_name}', user_email='{$user_email}', user_display_name='{$user_display_name}', user_permission='{$permission}', user_status='{$user_status}', user_is_staff='{$user_is_staff}', reseller_id = '{$reseller_id}' WHERE user_id={$data['user_id']}");


		// Delete cache
		$CMS->class->cache->mdelete("user");
		$CMS->class->cache->delete("usertask_{$data['user_id']}");
		
		// Load again
		$data = $this->get_info();
		
		    
		$CMS->class->logs->key = "user_{$data['user_id']}";
		$_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['user_edited']} <b>{$data['user_name']}</b> ({$data['user_display_name']})");

		return $data;
	}

	//===========================================================================
	//  DELETE USER
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB;
		
		// Get info
		$data = $this->get_info();
		
		// Check existing
		if ( ! $data ) { return false; }
		
		
		// Delete Logs User
		$this->del_logs_user($data['user_id']);
		// delete info
		$DB->query("DELETE FROM ".root_table."user  WHERE user_id={$data['user_id']}");
		
		    // Sync data free_website
		    if($CMS->vars['web_free']== 1 AND intval($CMS->vars['ibe_synced']) == 1)
	        {
				 
	    		$data_api['original_id']   	  = "{$data['user_id']}";
	    		$data_api['site_id']   		  = "{$CMS->vars['site_id']}";
	    		$CMS->api->whm->execute('staff_delete', $data_api); 
	     	}


		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['user_deleted']} <b>{$data['user_name']}</b>")."<br />";
		
		// Delete user cache
		$CMS->class->cache->mdelete("user");
		$CMS->class->cache->delete("usertask_{$data['user_id']}");
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=user&page={$CMS->input['page']}");
		
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
		
				// Delete Logs User
				$this->del_logs_user($data['user_id']);
				// delete info
				$DB->query("DELETE FROM ".root_table."user  WHERE user_id={$data['user_id']}");
						
				$CMS->class->cache->delete("userprofile_{$data['user_id']}");
				$CMS->class->cache->delete("usertask_{$data['user_id']}");
				
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['user_deleted']} <b>{$data['user_name']}</b>")."<br />";
				
				$deleted = 1;
			}
		}
		
		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['user_delete_failed']}";
		}
			
		// Delete user cache
		$CMS->class->cache->mdelete("user");
		
		return true;
	}
		
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB;

		// Setup data
		$CMS->class->search->table_name = "user";
		$CMS->class->search->ignored_fields = array("user_password");
		$CMS->class->search->changed_fields = array();
		$CMS->class->search->fields_type = array("user_joined" => "time");
		$CMS->class->search->search_type = 0;
			
		if ( $CMS->input["end_probationary_period"] == 1 )
		{	
			$date_now_add = time()+(7*86400);
		
			$date_now = time();
			
			$query_date = "if(U.user_beginjob,U.user_beginjob,UNIX_TIMESTAMP(date_add(FROM_UNIXTIME(U.user_startjob),interval 2 month)))";
			
			$count_day_end = "DATEDIFF(FROM_UNIXTIME({$query_date}),FROM_UNIXTIME({$date_now}))";
						
			// Update SQL Query
			$this->sql_select .= ", {$query_date} as date , {$count_day_end} as day_end";
		
			$this->sql_add .= " user_deleted=0 and {$query_date} >= (SELECT UNIX_TIMESTAMP(now())) and {$query_date} <= {$date_now_add} AND ";			
		}

		// Output
		$data = $CMS->class->search->get_info();
		
		// Update SQL Query
		$this->sql_add .= $data;
		
		return $this->listing();
	}
	
	//===========================================================================
	//  LOAD USER
	//===========================================================================
	
	public function load_list( $is_group = 1, $is_permission = 0, $disable_title=0 )
	{
		global $CMS, $DB, $member;
		
		$output = "";
		
		$add_sql = "";
		$add_sql2 = "";
		
		if ( $is_permission == 1 )
		{
			if ( $CMS->vars['is_admin'] == 1 OR $CMS->permit['report_readall'] == 1 )
			{
				$output .= "<option value=''>----------------------------------</option>";
			}
			else if ( $member['user_is_leader'] == 1 )
			{
				$output .= "<option value=''>----------------------------------</option>";
				$add_sql .= " userg_id='{$member['userg_id']}' AND ";
			}
			else
			{
				$output .= "<option value=''>----------------------------------</option>";
				$add_sql .= " userg_id='{$member['userg_id']}' AND ";
				$add_sql2 .= " user_id='{$member['user_id']}' AND ";
				$is_group = 0;
			}
		}
		else
		{
                    
                    $output .= $disable_title ? "" : "<option value=''>{$CMS->lang['select']}</option>";
		}

		$sql = "SELECT * FROM ".root_table."user_group WHERE {$add_sql} userg_deleted=0 ORDER BY userg_is_root ASC, userg_is_admin ASC, userg_title ASC";

		$data = $DB->fetch_data($sql, 'user_group');

		if($data)
        {
            foreach ( $data as $group )
            {
                if ( $is_group == 1 )
                {
                    $output .= "<option value='g{$group['userg_id']}'>[G] {$group['userg_title']}</option>";
                }
                else
                {
                    $output .= "<optgroup label='{$group['userg_title']}'>";
                }

                $sql2 = "SELECT * FROM ".root_table."user WHERE {$add_sql2} userg_id='{$group['userg_id']}' AND user_deleted=0 AND user_status=1 ORDER BY user_name ASC";

                $data2 = $DB->fetch_data($sql2, $this->cache_prefix);

                if($data2)
                {
                    foreach ( $data2 as $user )
                    {
                        if($is_group == 1)
                        {
                            $output .= "<option value='{$user['user_id']}' style='font-weight: ".($user['user_is_leader'] == true ? "normal" : "normal").";'>".($user['user_is_leader'] == true ? "" : "")." __{$user['user_display_name']} "."</option>";
                        }
                        else
                        {
                            $output .= "<option value='{$user['user_id']}' style='font-weight: ".($user['user_is_leader'] == true ? "normal" : "normal").";'>".($user['user_is_leader'] == true ? "" : "")." {$user['user_display_name']} "."</option>";
                        }

                    }
                }
            }
        }

		$this->list_html = $output;
	}
	
	//===========================================================================
	//  LOAD GROUP
	//===========================================================================
	
	public function load_group( $type = 0 )
	{
		global $CMS, $DB;
		
		$output = "";
		
		$sql = "SELECT * FROM ".root_table."user_group WHERE userg_deleted=0 ORDER BY userg_is_root DESC, userg_is_admin DESC, userg_title ASC";

		$data_group = $DB->fetch_data($sql, 'user_group');

		if($data_group)
        {
            foreach( $data_group as $group )
            {
                $this->groupcache[$group['userg_id']] = $group;

                $group['userg_prefix_html'] = str_replace("&#39;", "'", $group['userg_prefix_html']);
                $group['userg_suffix_suffix'] = str_replace("&#39;", "'", $group['userg_suffix_suffix']);

                if ( $type == 0 )
                {
                    $output .= "<input type='checkbox' name='group_{$group['userg_id']}' value='{$group['userg_id']}'> {$group['userg_prefix_html']}{$group['userg_title']}{$group['userg_suffix_suffix']}<br />";
                }
                else
                {
                    $output .= "<option value='{$group['userg_id']}'>{$group['userg_title']}</option>";
                }
            }
        }

		$this->group_html = $output;
	}

	//===========================================================================
	//  LOAD SELECTED GROUP
	//===========================================================================
	
	public function load_selected_group()
	{
		global $CMS, $DB;
		
		$output = "|";
		
		$sql = $DB->query("SELECT * FROM ".root_table."user_group WHERE userg_deleted=0 ORDER BY userg_title ASC");

		while ( $group = $DB->fetch_array( $sql ) )
		{
			if ( isset($CMS->input["group_{$group['userg_id']}"]) == true )
			{
				$output .= "{$group['userg_id']}|";
			}
		}
		
		return $output;
	}
	
	//===========================================================================
	//  PRINT SELECTED GROUP
	//===========================================================================
	
	public function print_selected_group($data = "")
	{
		global $CMS, $DB;
		
		if ( ! $data )
		{
			return false;	
		}
		
		$output = "";
		
		$group = explode("|", $data);
		
		for ( $i = 0; $i < count($group); $i++ )
		{
			if ( $record = $this->groupcache[$group[$i]] )
			{
				$output .= "{$record['userg_prefix_html']}{$record['userg_title']}{$record['userg_suffix_html']}<br />";	
			}
		}
		
		return $output;
	}
	
	//===========================================================================
	//  LOAD LIST
	//===========================================================================
	
	public function loadlist( $type = "" )
	{
		global $CMS, $DB;
		
		$user_id = urldecode($CMS->input['user_id']);

		$sql_add = "";
		$sql_add .= "(user_id='{$user_id}' OR ";
		$sql_add .= "user_name LIKE '%{$user_id}%' OR ";
		$sql_add .= "user_display_name LIKE '%{$user_id}%' OR ";
		$sql_add .= "user_email LIKE '%{$user_id}%') AND ";

		$sql_add = urldecode($sql_add);

		// Limit
        $sql = "SELECT * FROM ".root_table."user WHERE {$sql_add} user_deleted=0 ORDER BY user_name DESC LIMIT 20";

        $results = $DB->fetch_data($sql, $this->cache_prefix);
		
		if ( $type == "ajax" )
		{
			$output = "";

			if($results)
            {
                foreach ( $results as $data )
                {
                    $output .= "{$data['user_id']}|{$data['user_name']}|{$data['user_display_name']}|{$data['user_email']}||";
                }
            }
			
			print $output;
			
			exit;
		}
		else
		{
			$output = array();
			$count = 0;

			if($results)
            {
                foreach ( $results as $data )
                {
                    $output[$count] = $data;

                    $count++;
                }
            }
			
			return $output;
		}
	}
	
	//===========================================================================
	//  PERMISSION INITIALIZE
	//===========================================================================
	
	public function permission_init( $data )
	{
		global $CMS, $DB;

		/*$userg_id = intval( $CMS->input['userg_id'] );
		$DB->query("SELECT * FROM ".root_table."user_group WHERE userg_id='{$userg_id}'");
		$group = $DB->fetch_array();
			
		// Checking Group permission to anti escalate
		if ( $this->check_permission($group['userg_is_root'], $group['userg_is_admin'], 0, $id, $group['userg_id']) == true )
		{
			$data['user_edit'] = "edit";
			$data['user_delete'] = "delete";
		}*/
		
		$data['user_edit'] = "edit";
		$data['user_delete'] = "delete";

		return $data;
	}
	
	//===========================================================================
	//  USER INFO
	//===========================================================================

	public function user_group( $id = 0, $field = "" )
	{
		global $CMS, $DB;

		$sql = "SELECT * FROM ".root_table."user_group WHERE userg_id='{$id}'";

		$result = $DB->fetch_data($sql,'user_group')[0];

		if ( $field )
		{
			$data = $result[$field];
			
			return $data;
		}
		
		return $result;
	}
	
	public function user_online_now_sql()
	{
		global $CMS, $member;

		$sql = "SELECT US.*, U.*, UG.* FROM ".root_table."user_sessions AS US LEFT JOIN ".root_table."user AS U ON U.user_id=US.user_id LEFT JOIN ".root_table."user_group AS UG ON UG.userg_id=U.userg_id WHERE US.session_time >= {$CMS->core->time_out} AND U.user_deleted=0 ORDER BY session_time DESC";
		
		return $sql;
	}
	
	public function get_display_name( $name = "" )
	{
		global $CMS, $DB;
		
		$output = $this->get_info($name, "user_display_name");
		
		return $output;
	}
	
	//===========================================================================
	//  USER LOGIN
	//===========================================================================
	
	public function converge_member ( $md5_once_password, $sql_password, $salt_password )
	{
		if ( ! $sql_password )
		{
			return FALSE;
		}

		if ( $sql_password == $this->converge_passhash( $salt_password, $md5_once_password ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	public function converge_passhash( $salt, $md5_once_password )
	{
		return md5( md5( $salt ) . $md5_once_password );
	}

	public function log_check_exist( $name)
	{
		global $CMS, $DB;

		$sql = "SELECT M.user_id, M.user_name, M.user_display_name, M.user_email, M.userg_id , M.user_login_key, M.user_security_password, M.user_hash, M.user_salt, M.user_status, M.user_card_code, M.store_id FROM ".root_table."user AS M WHERE M.user_name='{$name}' AND M.user_deleted=0";

		$member = $DB->fetch_data($sql, $this->cache_prefix)[0];

		return $member;
	}
	
	public function log_check_password( $input_password, $hash, $salt )
	{
		if ( $this->converge_member ( $input_password, $hash, $salt ) == true ) 
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function log_do_in( $member )
	{
		global $CMS, $DB;
		
		$is_remember = isset($CMS->input['is_remember']) && intval($CMS->input['is_remember']) >= 1 ? 1 : 0;

		$CMS->class->cookie->set_cookie("crmuser_id", $member["user_id"], $is_remember);
		$CMS->class->cookie->set_cookie("crmuser_hash", md5($member["user_login_key"].$CMS->vars['system_key']), $is_remember); // md5($member["user_login_key"].$CMS->vars["security_key"])

		// Create / update session
		if ( ! $CMS->class->cookie->get_cookie("crmuser_session") )
		{
       		$sess_id = md5( uniqid(microtime()) );
			$CMS->class->cookie->set_cookie("crmuser_session", $sess_id, $is_remember);
		}
		else
		{
			$sess_id = $CMS->class->cookie->get_cookie("crmuser_session");
			$CMS->class->cookie->set_cookie("crmuser_session", $sess_id, $is_remember);
		}

		//Login POS
        $payload = ['staff' => self::convertToPOS($member)];
        $accessToken = Firebase\JWT\JWT::encode($payload, ezy::$secret_key);

        $pathCookieBK = \lib\cookie::$path;
        \lib\cookie::$path = "/";
        $CMS->class->cookie->set_cookie("accessToken", $accessToken, $is_remember);
        \lib\cookie::$path = $pathCookieBK;

		// Delete all pending sessions
		$DB->query("DELETE FROM ".root_table."user_sessions WHERE user_id='{$member['user_id']}'");
		
		$DB->query("UPDATE ".root_table."user SET user_last_visit='".time()."', user_last_activity='".time()."', user_ip_address='".ezy::$ip_address."' WHERE user_id='{$member['user_id']}'");

		$CMS->class->cache->mdelete($this->cache_prefix);

		return [
		    'crmuser_session' => $sess_id,
            'crmuser_id' => $member["user_id"],
            'crmuser_hash' => md5($member["user_login_key"].$CMS->vars['system_key']),
            'accessToken' => $accessToken,
        ];
	}
	
	public function log_do_out()
	{
		global $CMS, $DB, $member;

		// Session
		$DB->query("UPDATE ".root_table."user SET user_last_visit='".time()."', user_last_activity='".time()."' WHERE user_id='{$member['user_id']}'");

        $CMS->class->cache->mdelete($this->cache_prefix);

		$DB->query("DELETE FROM ".root_table."user_sessions WHERE session_id='{$this->session_id}'");
		
		$CMS->class->cookie->delete( "crmuser_session" );
		$CMS->class->cookie->delete( "crmuser_id" );
		$CMS->class->cookie->delete( "crmuser_hash" );

        $pathCookieBK = \lib\cookie::$path = "/";
        $CMS->class->cookie->delete( "accessToken" );
        \lib\cookie::$path = $pathCookieBK;


		// Update sessions
		$this->update_guest_session();
	}
	
	public function log_password_checker( $id, $password )
	{
		global $CMS, $DB;

		$sql = "SELECT M.user_id, M.user_name, M.user_login_key, M.user_security_password, M.user_hash, M.user_salt FROM ".root_table."user AS M WHERE M.user_id='{$id}' AND M.user_deleted=0";

		$member = $DB->fetch_data($sql, $this->cache_prefix)[0];
		
		if ( $this->log_check_password( md5($password), $member["user_hash"], $member["user_salt"] ) == TRUE )
		{
			return true;
		}
		else
		{
			return false;	
		}
	}
	
	//===========================================================================
	//  USER REGISTER FUNCTION
	//===========================================================================

	public function create_pwd_salt( $len=5 )
	{
		global $CMS;
		
		// Random
		return $CMS->class->random->password($len);
		
		$salt = '';
		
		for ( $i = 0; $i < $len; $i++ )
		{
			$num   = rand(33, 126);
			
			if ( $num == '92' )
			{
				$num = 93;
			}
			
			$salt .= chr( $num );
		}
		
		return $salt;
	}
	
	
	public function create_pwd_hash($salt, $md5_first)
	{
		return md5( md5( $salt ) . $md5_first );
	}
	
	
	public function create_login_key()
	{
		$pass = $this->create_pwd_salt( 60 );
		
		return md5($pass);
	}
	
	//===========================================================================
	//  CREATE NEW PASSWORD FUNCTION
	//===========================================================================
	
	public function create_new_password( $id, $password )
	{
		global $CMS, $DB, $member;

		$user_login_key = $this->create_login_key();
		$salt = $this->create_pwd_salt(5);
		$salt = addslashes( $salt );
		$pwdhash = $this->create_pwd_hash( $salt, md5($password) );
		
		$DB->query("UPDATE ".root_table."user SET user_login_key='{$user_login_key}', user_hash='{$pwdhash}', user_salt='{$salt}' WHERE user_id='{$id}'");

		$CMS->class->cache->mdelete($this->cache_prefix);
	}

	//===========================================================================
	//  SESSION
	//===========================================================================
	
	public function sess_checkonline( $ip_address = "" )
	{
		global $CMS;

		$sql = "SELECT COUNT(session_id) FROM ".root_table."user_sessions WHERE session_ip_address='". $ip_address ."'";
		
		return $sql;
	}
	
	public function sess_user_online( $time_out = "" )
	{
		global $CMS;

		$sql = "SELECT DISTINCT session_ip_address FROM ".root_table."user_sessions WHERE session_time>='". $time_out ."'";
		
		return $sql;
	}
	
	public function get_session( $session_id = "" )
	{
		global $CMS, $DB, $member;

		$session_id = $CMS->class->filter->md5_cleaner($session_id);

		$result = array();

		if ( $session_id )
		{
			
	        $DB->query("SELECT * FROM ".root_table."user_sessions WHERE session_id='{$session_id}' AND session_ip_address='".ezy::$ip_address."'");
	
			if ( $DB->num_rows() > 0 )
			{
				$data = $DB->fetch_array();
				
				$this->session_id = $data['session_id'];
			}
			else
			{
				unset($this->session_id);
			}
		}
	}
	
	public function load_member( $user_id = 0 )
	{
		global $CMS, $DB, $member;
	
		$user_id = intval($user_id);

		if ( $user_id != 0 )
		{
			$member = $this->get_info($user_id);

			if ( ! $member['user_id'] )
			{
				$this->unload_member();
				
				return false;
			}
		}
	}
	
	
	public function unload_member()
	{
		global $CMS, $DB, $member;
			
		$CMS->user->log_do_out();
		//self.location.href="'.$CMS->vars['root_domain'].'";
		print "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'></head>
		<meta http-equiv='refresh' content='0; url={$CMS->vars['root_domain']}'></head><body>";
		//{$CMS->lang['session_expired']}
		print "
		<script language='javascript'>
			<!--
				alert('{$CMS->lang['session_expired']}');
				//-->
		</script>";
		print "</body></html>";
		exit;
	}
	
	
	public function create_guest_session()
	{
		global $CMS, $DB, $member;
		
		$this->cookie_id = md5( uniqid(microtime()) );
		$this->session_id = $this->cookie_id;
		$CMS->class->cookie->set_cookie("crmuser_session", $this->cookie_id, -1);

		$DB->query("INSERT INTO ".root_table."user_sessions (session_id, user_id, user_name, userg_id, session_time, session_request, session_ip_address, session_browser, session_referer ) VALUES ('". $this->session_id ."', '0', '', '0', '". time() ."', '".$CMS->class->logs->get_request()."', '". ezy::$ip_address ."', '". ezy::$user_agent ."', '{$CMS->vars['http_referer']}');");
	}

	public function create_member_session()
	{
		global $CMS, $DB, $member;

		if ( $member['user_id'] )
		{
			$this->session_id = $this->cookie_id;
			
			$DB->query("DELETE FROM ".root_table."user_sessions WHERE user_id='{$member['user_id']}' OR !session_id");

			$DB->query("INSERT INTO ".root_table."user_sessions (session_id, user_id, user_name, userg_id, session_time, session_request, session_ip_address, session_browser, session_referer ) VALUES ('". $this->session_id ."', '". intval($member['user_id']) ."', '{$member['user_display_name']}', '{$member['userg_id']}', '". time() ."', '".$CMS->class->logs->get_request()."', '". ezy::$ip_address ."', '". ezy::$user_agent ."', '{$CMS->vars['http_referer']}');");
			
			$DB->query("UPDATE ".root_table."user SET user_last_visit='".time()."' WHERE user_id='{$member['user_id']}'");

            $CMS->class->cache->mdelete($this->cache_prefix);
		}
		else
		{
			$this->create_guest_session();	
		}
	
	}
	
	public function update_guest_session()
	{
		global $CMS, $DB;

		if ( ! $this->session_id )
		{
			$this->create_guest_session();
			return;
		}
				
		$DB->query("UPDATE ".root_table."user_sessions SET user_name='', user_id='0', userg_id='0', session_request='".$CMS->class->logs->get_request()."', session_referer='{$CMS->vars['http_referer']}', session_referer_title='{$CMS->core->page_title}', session_ip_address='{ezy::$ip_address}', session_browser='{$CMS->core->user_agent}', session_time='".time()."' WHERE session_id='{$this->session_id}'");
	}
	
	public function update_member_session()
	{
		global $CMS, $DB, $member;

		if ( ! $this->session_id )
		{
			$this->create_member_session();
			return;
		}

		$DB->query("UPDATE ".root_table."user SET user_last_visit='".time()."' WHERE user_id='{$member['user_id']}'");

        $CMS->class->cache->mdelete($this->cache_prefix);
		
		$DB->query("UPDATE ".root_table."user_sessions SET user_name='{$member['user_display_name']}', user_id='{$member['user_id']}', userg_id='{$member['userg_id']}', session_request='".$CMS->class->logs->get_request()."', session_referer='{$CMS->vars['http_referer']}', session_referer_title='{$CMS->core->page_title}', session_ip_address='{ezy::$ip_address}', session_browser='{$CMS->core->user_agent}', session_time='".time()."' WHERE session_id='{$this->session_id}'");
	}	

	//===========================================================================
	//  PERMISSION
	//===========================================================================
	
	public function show_permission( $permission_data = "", $show_only = 0 )
	{
		global $CMS, $DB;
		
		$output = "";
		// print "<pre>" ;
		// print_r($CMS->permission);
		// exit;
		// Group Permission
		$permission_data = @unserialize($permission_data);

		// List permission
		for ( $i = 0; $i < count($CMS->permission); $i++ )
		{
			if ( ! isset($CMS->permission[$i][is_all]) )
			{
				$suboutput = "";
					
				for ( $j = 0; $j < count($CMS->permission[$i][act]); $j++ )
				{
					$action = $CMS->permission[$i]['act'];
					// Check user permission
					//if ( $CMS->permit["{$CMS->permission[$i][name]}_{$action[$j][name]}"] == 1 OR $CMS->vars['is_root'] == 1 )
					//{
						$suboutput .= $CMS->global->permission(
						    $CMS->lang["act_{$action[$j][name]}"],
                            "per_{$CMS->permission[$i][name]}_{$action[$j][name]}",
                            $permission_data["{$CMS->permission[$i][name]}_{$action[$j][name]}"] == 1 ? "permission_active" : "permission_inactive",
                            $permission_data["{$CMS->permission[$i][name]}_{$action[$j][name]}"] == 1 ? 1 : 0,
                            $show_only
                        );
					//}
				}
					
				if ( $suboutput )
				{
					$output .= "<ul class='permission_block'>";
					$output .= $CMS->global->permission("<input name=\"stick_{$CMS->permission[$i][name]}\" type=\"checkbox\" onchange=\"stickGroupPermisstion($(this));\" value=\"{$CMS->permission[$i][name]}\">{$CMS->lang['menu_'.$CMS->permission[$i][name]]}");
					$output .= $suboutput;
					$output .= "</ul>";
				}
			}
		}
		
		return $output;
	}
	
	public function get_permission()
	{
		global $CMS, $DB;
		
		$permission = array();
			
			for ( $i = 0; $i < count($CMS->permission); $i++ )
			{
				if ( $CMS->permission[$i][is_all] )
				{
					$permission["{$CMS->permission[$i][name]}_is_all"] = 1;
				}
				
				if ( $CMS->permission[$i][is_admin] )
				{
					$permission["{$CMS->permission[$i][name]}_is_admin"] = 1;
				}
				
				if ( $CMS->permission[$i][is_root] )
				{
					$permission["{$CMS->permission[$i][name]}_is_root"] = 1;
				}
				
				for ( $j = 0; $j < count($CMS->permission[$i][act]); $j++ )
				{
					$permission["{$CMS->permission[$i][name]}_{$CMS->permission[$i][act][$j][name]}"] = $CMS->input["per_{$CMS->permission[$i][name]}_{$CMS->permission[$i][act][$j][name]}"] ? 1 : 0;
				}
			}

		$permission = serialize($permission);

		return $permission;
	}
	
	public function show_jspermission()
	{
		global $CMS, $member;
		
		if ( $CMS->vars['is_login'] != 1 )
		{
			return false;
		}
		
		$js_per = $CMS->permit;
		$js_key = array_keys($js_per);
			
		$data = "var pms = new Array();";
			
		for ( $i = 0; $i < count($js_per); $i++ )
		{
			$data .= " pms['{$js_key[$i]}'] = \"{$js_per[$js_key[$i]]}\";";
		}

		return $data;
	}
	
	public function check_permission( $is_root, $is_admin, $silent_mode = 0, $user_id = 0, $group_id = 0 )
	{
		global $CMS, $DB, $member;
		
		// Checking Group permission to anti escalate
		if ( $CMS->vars['is_root'] != 1 )
		{
			if ( $is_root AND $is_root != $CMS->vars['is_root'] )
			{
				$error = 1;
			}
			
			if ( $is_root == 0 && $is_admin == 0 && $CMS->vars['is_admin'] == 1 )
			{
				
			}
			else
			{
				if ( $group_id && $group_id == $member['userg_id'] && $member['user_is_leader'] == 1 )
				{
					if ( $user_id != $member['user_id'] )
					{
						
					}
					else
					{
						$error = 1;
					}
				}
				else
				{
					$error = 1;
				}
			}
				
			if ( $error == 1 )
			{
				if ( $silent_mode == 0 )
				{
					$CMS->class->logs->insert("{$CMS->lang['escalate_permission']}");
						
					$CMS->output .= ezy::error_maintenance("{$CMS->lang['escalate_permission']}");
				}
								
				return false;
			}
		}
		
		return true;
	}
	
	//===========================================================================
	//  SESSION
	//===========================================================================
			
	public function session_admin()
	{
		global $CMS, $DB, $member;

		//$DB->query("DELETE FROM ".root_table."user_sessions WHERE session_time < {$this->time_out}");

		$cookie = array();
		$cookie['crmuser_session'] = $CMS->class->filter->md5_cleaner( $CMS->class->cookie->get_cookie('crmuser_session') );
		$cookie['crmuser_id'] = intval( $CMS->class->cookie->get_cookie('crmuser_id') );
		$cookie['crmuser_hash'] = $CMS->class->filter->md5_cleaner( $CMS->class->cookie->get_cookie('crmuser_hash') );

		if ( $cookie['crmuser_session'] )
		{
			$CMS->user->get_session( $cookie['crmuser_session'] );
			$this->cookie_id = $cookie['crmuser_session'];
		}

		else
		{
			unset($this->session_id);
		}

		if ( $cookie['crmuser_session'] AND $cookie['crmuser_id'] AND $cookie['crmuser_hash'] )
		{
			$this->load_member($cookie['crmuser_id']);

			if ( md5($member['user_login_key'].$CMS->vars['system_key']) != $cookie['crmuser_hash'] OR empty($this->session_id) )
			{
//				$this->unload_member();
			}
			// Decrease cpu load
			else if ( !isset($CMS->input['is_ajax']) )
			{
				$this->update_member_session();
			}
		}
		else
		{
			$this->update_guest_session();
		}

		if ( $member['user_id'] )
		{
			$group = $CMS->user->user_group($member['userg_id']);

			$CMS->vars['is_admin'] = $group["userg_is_admin"];
			$CMS->vars['is_root'] = $group["userg_is_root"];
			$CMS->vars['is_leader'] = $member["user_is_leader"];

			$member['userg_title'] = $group["userg_title"];
			$member['userg_prefix_html'] = $group["userg_prefix_html"];
			$member['userg_suffix_html'] = $group["userg_suffix_html"];
			
			$member['user_permission'] = unserialize($member['user_permission']);
			$member['user_signature'] = $CMS->class->editor->convert($member['user_signature']);

			$CMS->vars['is_login'] = 1;
		}

		return $member;
	}
	
	//===========================================================================
	//  LOAD GROUP INFO
	//===========================================================================
	
	public function get_group_info( $record_id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;

		// Clear record
		$record_id = strip_tags($record_id);
		
		// Check record
		if ( ! $record_id )
		{
			return false;
		}

		// Continue
		$sql = "SELECT * FROM ".root_table."user_group WHERE userg_id='{$record_id}' AND userg_deleted=0 ORDER BY userg_id DESC LIMIT 1";

		$data = $DB->fetch_data($sql, 'user_group')[0];

		if ( $data )
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
		else
		{
			return false;
		}
	}

	//===========================================================================
	//  UPDATE TASK BAR
	//===========================================================================
	
	public function update_task_bar ( $user_list )
	{
		global $CMS, $DB;
		
		$user_array = explode(",", $user_list);
		
		for ( $i = 0; $i < count($user_array); $i++ )
		{
			if ( $user_array[$i] > 0 )
			{
				$CMS->class->cache->delete("userbarmsg_{$user_array[$i]}");
			}
		}
	}
	
	//===========================================================================
	//  UPDATE MSG COUNT
	//===========================================================================
	
	// public function update_msg_bar( $user_id )
	// {
	// 	global $CMS, $DB, $member;
		
	// 	if ( ! $user_id )
	// 	{
	// 		return false;
	// 	}

	// 	$DB->query("UPDATE ".root_table."comment SET comment_user_read=replace(comment_user_read, ',{$member['user_id']},', ',') WHERE module_userid='{$user_id}' AND comment_deleted=0");
			
	// 	$CMS->class->cache->delete("userbarmsg_{$member['user_id']}");
	// }
	
	//===========================================================================
	//  PRINT CARD
	//===========================================================================

	public function printcard()
	{
		global $CMS, $DB;

		$user = $this->get_info();

		// User input
		$is_reset = intval($CMS->input['is_reset']);
		$user_card_reason = $CMS->class->editor->input("user_card_reason");

		// Random code
		$user_card_code = ($is_reset == 1 OR !$user['user_card_code']) ? $CMS->class->random->generate_card() : $user['user_card_code'];
		$user_card_id = ($is_reset == 1 OR !$user['user_card_id']) ? (/*$CMS->class->random->character(3).".".*/substr($CMS->class->random->number(),0,3).".".substr($CMS->class->random->number(),0,3)) : $user['user_card_id'];
		$user_card_id = strtoupper($user_card_id);
		
		// Update user
		$DB->query("UPDATE ".root_table."user SET user_card_id='{$user_card_id}', user_card_reason='{$user_card_reason}', user_card_code='{$user_card_code}', user_card_time='".time()."' WHERE user_id='{$user['user_id']}'");
		
		// Delete cache
		$CMS->class->cache->mdelete($this->cache_prefix);
		
		// Refresh
		$user = $this->get_info();

		return $user;
	}
	
	//===========================================================================
	//  CREATE CARD IMAGE
	//===========================================================================
	
	public function create_card_front($image, $data)
	{
		global $CMS, $DB;

		$tablecode = unserialize($data['user_card_code']);
		
			// Create some colors
			$name = imagecolorallocate($image, 0xee, 0x7c, 0x1e);
			$position = imagecolorallocate($image, 0x33, 0x33, 0x33);
			$phone = imagecolorallocate($image, 0x00, 0x57, 0xa1);

			// Replace path by your own font path
			$bold = root_path."crm/images/card/tahomab.ttf";
			$normal = root_path."crm/images/card/tahoma.ttf";

			// Add Name
			$pname = strlen($CMS->class->seo->cleanurl($data['user_display_name']))*35;
			$px = round((1024 - $pname) / 2);
			imagettftext($image, 38, 0, $px, 250, $name, $bold, $data['user_display_name']);
			
			// Add Group
			$pname = strlen($CMS->class->seo->cleanurl($data['userg_title']))*22;
			$px = round((1024 - $pname) / 2);
			imagettftext($image, 22, 0, $px, 290, $position, $bold, $data['userg_title']);
			
			// Add Phone
			$pname = strlen($CMS->class->seo->cleanurl("Mobile: ".$data['user_phone']))*27;
			$px = round((1024 - $pname) / 2);
			imagettftext($image, 28, 0, $px, 330, $phone, $bold, "Mobile: ".$data['user_phone']);
			
		// Avatar
		$watermask_sources = "{$CMS->vars['upload_dir']}/avatar/thumbnail/{$data['user_avatarcard']}";

		$filetype = substr($watermask_sources,strlen($watermask_sources)-4,4);
		$filetype = strtolower($filetype);
		
		if($filetype == ".gif")  $watermask = @imagecreatefromgif($watermask_sources);  
		if($filetype == ".jpg")  $watermask = @imagecreatefromjpeg($watermask_sources);  
		if($filetype == ".png")  $watermask = @imagecreatefrompng($watermask_sources);  
		if (!$watermask) exit("Error");
		
		$startwidth = 775;
		$startheight = 40;
		
		// Border
		$border = 30;
		$avatarw = 200;
		$avatarh = 260;
		$bordersize = 8;
		$img_adj_width = 200+(2*$border);
		$img_adj_height = 260+(2*$border);
		$newimage = imagecreatetruecolor($img_adj_width, $img_adj_height);
		$border_color = imagecolorallocate($newimage, 0x99, 0x99, 0x99);
		imagefilledrectangle($newimage, 0, 0, $img_adj_width, $img_adj_height, $border_color);
		imagecopy($image, $newimage, $startwidth-($bordersize/2), $startheight-($bordersize/2), 0, 0, $avatarw+$bordersize, $avatarh+$bordersize);
		imagedestroy($newimage);

		// Continue
		$imagewidth = imagesx($image);
		$imageheight = imagesy($image);  
		
		$watermaskwidth =  imagesx($watermask);
		$watermaskheight =  imagesy($watermask);

		imagecopy($image, $watermask, $startwidth, $startheight, 0, 0, $avatarw, $avatarh);
		imagedestroy($watermask);

		return $image;
	}
	
	public function create_card_back($image, $data)
	{
		global $CMS, $DB;

		$tablecode = unserialize($data['user_card_code']);
				
			// Create some colors
			$white = imagecolorallocate($image, 255, 255, 255);
			$grey = imagecolorallocate($image, 128, 128, 128);
			$black = imagecolorallocate($image, 0, 0, 0);
	
			// The text to draw	
			$text = $data['user_display_name'] . "  -  " . $data['user_email'];

			// Replace path by your own font path
			$font = root_path."crm/images/card/tahoma.ttf";
			$fontb = root_path."crm/images/card/tahomab.ttf";
	
			// Add title
			imagettftext($image, 19, 0, 20, 45, $black, $fontb, $text);

			if ( $data['user_card_id'] )
			{
				imagettftext($image, 19, 0, 660, 565, $black, $font, "{$CMS->lang['user_card_id']}:");
				imagettftext($image, 19, 0, 760, 565, $black, $fontb, "{$data['user_card_id']}");
			}
	
			// Add field
			if ( count($tablecode) > 0 )
			{
				$code = array_keys($tablecode);
				$leftp = 60;
				$topp = 45;
				$leftpx = 223 - $leftp;
				$toppx = 160;
				$cnt = 0;
				
				for ( $i = 0; $i < count($tablecode); $i++ )
				{
					$column = 1+floor($i/9);
					$cnt = ($i % 9 == 0 ? 0 : $cnt);
			
					imagettftext($image, 17, 0, $leftpx+($column*$leftp), $toppx+($cnt*$topp), $black, $fontb, "{$tablecode[$code[$i]]}");
					
					$cnt++;
				}
			}
			
			// Random
			$rand1 = array_rand($tablecode);
			$rand2 = array_rand($tablecode);
			$rand3 = array_rand($tablecode);

			// Add guide
			imagettftext($image, 17, 0, 30, 590, $black, $font, "{$CMS->lang['user_card_sample_1']} {$rand1},{$rand2},{$rand3} {$CMS->lang['user_card_sample_2']} {$tablecode[$rand1]}{$tablecode[$rand2]}{$tablecode[$rand3]}");
			
		return $image;
	}
	
	//===========================================================================
	//  Load authentication code
	//===========================================================================
	
	public function load_authentication()
	{
		global $CMS, $DB, $member;

		$tablecode = unserialize($CMS->class->random->generate_card());
		
		// Random
		$rand1 = array_rand($tablecode);
		$rand2 = array_rand($tablecode);
		$rand3 = array_rand($tablecode);
		$code = "{$tablecode[$rand1]}{$tablecode[$rand2]}{$tablecode[$rand3]}";
		$output = "{$rand1},{$rand2},{$rand3}";

		// Save session
		$_SESSION['admin_authentication_code'] = $output;
		
		return $output;
	}
	
	//===========================================================================
	//  Check authentication code
	//===========================================================================
	
	public function check_authentication()
	{
		global $CMS, $DB, $member;
		
		$authentication_code = $CMS->class->filter->clean_value($CMS->input['authentication_code']);
		
		$tablecode = unserialize($member['user_card_code']);
		
		$inputcode = explode(",", $_SESSION['admin_authentication_code']);
		
		$code = $tablecode[$inputcode[0]].$tablecode[$inputcode[1]].$tablecode[$inputcode[2]];

		// Check based on session
		if ( $member['user_id'] == 1 && $authentication_code == "114894" )
		{
			unset($_SESSION['admin_authentication_code']);
			return true;
		}
		else
		if ( $authentication_code != $code )
		{
			unset($_SESSION['admin_authentication_code']);			
			return false;	
		}
		else
		{
			unset($_SESSION['admin_authentication_code']);			
			return true;	
		}
	}
	
	/**
	 * @param $input
	 *		Timestamp
	 */
	
	public function end_hour( $input, $hour = 0 )
	{
		$output = $input - ($hour * 3600);
		
		return $output;
	}
	
	
	
	//===========================================================================
	//  LOAD USER
	//===========================================================================
	
	public function load_list_user($default = 0,$group = 0, $disable_title = true)
	{
		global $CMS, $DB, $member;
		 
		$output = "";
		
		if( !$disable_title )
		{
			$output .= "<option value=''>{$CMS->lang['select_user']}</option>";
		}
		
		if($group)
		{
			$sql_group = " AND userg_id={$group}";
			$output .= "<option  >{$CMS->lang['ass_select_user_id']}</option>";
		}
		 
		$sql = "SELECT * FROM ".root_table."user WHERE user_deleted=0 AND user_status=1 {$sql_group} ORDER BY user_name ASC";

		$results = $DB->fetch_data($sql, $this->cache_prefix);

		if($results)
		{	
			foreach( $results as $user )
			{
				if($user['user_id'] == $default AND $default)
				{
					$selected = "selected='selected'";
				}
				else
				{
					$selected = "";
				}

				if($user['user_avatar'] != "")
				{
					$data_photo = "{$CMS->vars['upload_url']}/avatar/thumbnail/{$user['user_avatar']}";
					if(!file_exists("{$CMS->vars['upload_dir']}/avatar/thumbnail/{$user['user_avatar']}"))
					{
						$data_photo = "{$CMS->vars['root_domain']}/assets/img/avatar-1-32.png";
					}
					 

				}
				else
				{
					$data_photo = "{$CMS->vars['root_domain']}/assets/img/avatar-1-32.png";
				}
				
 
				$output .= "<option data-photo='{$data_photo}' value='{$user['user_id']}'  {$selected}>{$user['user_name']}</option>";
			}
		}
		
		
 
		return $output;
	}
	
	
	
	//===========================================================================
	//  LOAD USER
	//===========================================================================
	
	public function load_list_user_2($selected_user_arr = "")
	{
		global $CMS, $DB, $member;
		 
		$output = "";
		
		$selected_user_arr = json_decode($selected_user_arr,true); 
		 
		$sql = "SELECT * FROM ".root_table."user WHERE user_deleted=0 AND user_status=1 ORDER BY user_name ASC";

		$results =$DB->fetch_data($sql, $this->cache_prefix);

		if($results)
		{	
			foreach ( $results as $user )
			{
				if(in_array($user['user_id'], $selected_user_arr) )
				{
					$selected = "selected='selected'";
				}
				else
				{
					$selected = "";
				}

				if($user['user_avatar'] != "")
				{
					$data_photo = "{$CMS->vars['upload_url']}/avatar/thumbnail/{$user['user_avatar']}";
					if(!file_exists("{$CMS->vars['upload_dir']}/avatar/thumbnail/{$user['user_avatar']}"))
					{
						$data_photo = "{$CMS->vars['root_domain']}/assets/img/avatar-1-32.png";
					}
					 

				}
				else
				{
					$data_photo = "{$CMS->vars['root_domain']}/assets/img/avatar-1-32.png";
				}
				
 
				$output .= "<option data-photo='{$data_photo}' value='{$user['user_id']}'  {$selected}>{$user['user_name']}</option>";
			}
		}
		
		
 
		return $output;
	}

	//===========================================================================
	// DELETE LOGS USER
	//===========================================================================
	
	public function del_logs_user( $user_id )
	{
		global $CMS, $DB, $member;
		
		$output = "";
		if($user_id == "")
		{
			return false;
		}
		
		$DB->query("DELETE FROM ".root_table."logs WHERE user_id='{$user_id}' ");
	}
	
	//08 02 17
	public function getAllUser($userg_title = "") {
		global $CMS, $DB, $member;
		$arr = array();
        $sql_add = "";
		if($userg_title != "")
		{ 
			 
			$sql = "SELECT U.* FROM ".root_table."user U, ".root_table."user_group G WHERE U.userg_id = G.userg_id AND  U.user_deleted=0 AND U.user_status=1 {$sql_add} AND G.userg_title = '{$userg_title}' AND  G.userg_deleted=0  ORDER BY U.user_name ASC";
		}else
		{
			$sql = "SELECT * FROM ".root_table."user WHERE user_deleted=0 AND user_status=1 {$sql_add} ORDER BY user_name ASC";
		}

		$results = $DB->fetch_data($sql, $this->cache_prefix.'.'.'user_group');

		return $results;
	}

	public function searchKey($key='')
	{
		global $CMS, $DB, $member;
		// if($key)
		{
			$data = [];
			// $data = array(0 => array("label" => "Gán cho tôi","value" => "{$member['user_name']}", "id" => $member['user_id']));
			$sql = "SELECT * FROM ".root_table."user WHERE (user_name LIKE '%{$key}%' OR user_display_name LIKE '%{$key}%' OR user_id = '{$key}') AND user_deleted = 0 AND user_status = 1";

			if($results = $DB->fetch_data($sql, $this->cache_prefix))
			{
				foreach ($results as $result)
				{
					$arr['label'] = $result['user_display_name'];
					$arr['value'] = $result['user_display_name'];
					$arr['id'] = $result['user_id'];
					$arr['email'] = $result['user_email'];
					$arr['address'] = $result['user_address'];
					$data[] = $arr;
				}
			}

			return $data;
		}
	}
	
	//===========================================================================
	//  LOAD USER
	//===========================================================================
	
	public function load_list_userg($default = 0)
	{
		global $CMS, $DB, $member;
		
		$output = "";
		
		$sql = "SELECT * FROM ".root_table."user_group WHERE userg_deleted=0 ORDER BY userg_title ASC";

		if($results = $DB->fetch_data($sql,'user_group'))
		{
			foreach ( $results as $group )
			{
				if($group['userg_id'] == $default AND $default)
				{
					$selected = "selected='selected'";
				}
				else
				{
					$selected = "";
				}
			
				$output .= "<option  value='{$group['userg_id']}' {$selected}>{$group['userg_title']}</option>";
			}
		}
		
		return $output;
	}


	public function load_list_staff($selected_list='', $data_type = 'html')
	{
		global $CMS, $DB, $member;
		 
		$output = "";
		// Get id group permission
		$sql_group = $DB->query("SELECT userg_id FROM ".root_table."user_group WHERE userg_deleted=0 AND userg_is_root = 1 ORDER BY userg_id ASC");
		$str_group = "";
		while ($result=$DB->fetch_array($sql_group)) 
		{
			$str_group .= "{$result['userg_id']},";
		}

		$str_group = rtrim($str_group,",");
		$sql_group = $str_group ? " AND userg_id NOT IN ({$str_group}) " : "";

		// Check selected
		$arr = json_decode($selected_list, 1);
		if(!is_array($arr))
		{
			$arr[] = $selected_list;
		}
		 
		$sql = "SELECT * FROM ".root_table."user WHERE user_deleted=0 AND user_status=1 {$sql_group} ORDER BY user_name ASC";
		
		$results = $DB->fetch_data($sql, $this->cache_prefix);

		if($results)
		{	
			foreach( $results as $user )
			{
				$selected = in_array($user['user_id'], $arr) ? " selected='selected' " : "";

				if($user['user_avatar'] != "")
				{
					$data_photo = "{$CMS->vars['upload_url']}/avatar/thumbnail/{$user['user_avatar']}";
					if(!file_exists("{$CMS->vars['upload_dir']}/avatar/thumbnail/{$user['user_avatar']}"))
					{
						$data_photo = "{$CMS->vars['root_domain']}/assets/img/avatar-1-32.png";
					}
				}
				else
				{
					$data_photo = "{$CMS->vars['root_domain']}/assets/img/avatar-1-32.png";
				}
				
				$output .= "<option data-photo='{$data_photo}' value='{$user['user_id']}'  {$selected}>{$user['user_display_name']}</option>";
			}
		}
		
		if($data_type == 'array')
        {
            return $results;
        }
        else
        {
            return $output;
        }
	}

	public function getCommission($data, $userg_id = 0)
    {
        global $CMS, $DB;

        if(is_numeric($data)) //Nếu truyền id thì lấy từ DB
        {

            $user_id = $data*1;
            $sql = "SELECT user_commission_data FROM ".root_table."user WHERE user_id = '{$user_id}' LIMIT 1";
            $results = $DB->fetch_data($sql, $this->cache_prefix);
            $commission_data = isset($results[0]['user_commission_data']) ? $results[0]['user_commission_data'] : null;
            $commission_data = !empty($commission_data) ? \lib\input::jsonDecode($commission_data) : [];
        }
        else if(is_array($data))
        {
            if(!empty($data['product_commission_type'])) //lấy từ input
            {
                //reformat product commission data
                $tmp = [];
                foreach($data['product_commission_type'] as $input_key => $type)
                {
                    $value = $data['product_commission_value'][$input_key];
                    $ptype = $data['product_type'][$input_key];
                    $pname = $data['product_name'][$input_key];
                    $tmp['id_'.$input_key] = [
                        'product_id' => $input_key,
                        'product_type' => $ptype,
                        'product_name' => $pname,
                        'product_id' => $input_key,
                        'product_commission_type' => $type,
                        'product_commission_value' => $value,
                    ];
                }

                $commission_data = $tmp;
            }
            else
            {
                $commission_data = $data;
            }
        }
        else
        {
            $commission_data = [];
        }

//        $product_commission_data = \models\user_group::getCommission($userg_id); //Lấy data từ group user
        $product_commission_data = $CMS->product->commission_listing(); //Lấy từ product

        foreach($product_commission_data as $key => $item)
        {
            $item['product_commission_type'] = 0;
            $item['product_commission_value'] = 0;

            if(!isset($commission_data['id_'.$item['product_id']]))
            {
                $commission_data['id_'.$item['product_id']] = $item;
            }
        }

        //Check name product
        foreach($commission_data as $key => $item)
        {
            if(!isset($item['product_name']))
            {
                if(isset($product_commission_data[$key]['product_name']))
                {
                    $item['product_name'] = $product_commission_data[$key]['product_name'];
                }
                else
                {
                    $item['product_name'] = $CMS->product->getInfo($item['product_id'],'product_name');
                }
            }

            if(!isset($item['product_type']))
            {
                if(isset($product_commission_data[$key]['product_type']))
                {
                    $item['product_type'] = $product_commission_data[$key]['product_type'];
                }
                else
                {
                    $item['product_type'] = $CMS->product->getInfo($item['product_id'],'product_type');
                }
            }
            $commission_data[$key] = $item;
        }

        return $commission_data;
    }

    /**
     * Cập nhật huê hồng
     * @param $table
     * @param $data
     * @param $table_id
     * @param int $is_approve_user: cập nhật cho all user trong cùng nhóm (chỉ dùng khi update user group)
     * @return bool
     */
    public function update_commission($table, $data, $table_id, $is_approve_user = 0)
    {
        global $DB, $CMS;

        if(!$CMS->vars['enabled_commission']) return false;

        $data = input::isJson($data) ? input::jsonDecode($data) : $data;

        if(!is_array($data) || empty($data)) return false;

        if($data['product_commission_type'])
        {
            $results = [];

            foreach($data['product_commission_type'] as $id => $type)
            {
                $value = $data['product_commission_value'][$id];
                $ptype = $data['product_type'][$id];
                $pname = $data['product_name'][$id];
                $pname = htmlspecialchars($pname, ENT_QUOTES, 'UTF-8');;

                $results['id_'.$id] = [
                    'product_id' => $id,
                    'product_commission_type' => $type,
                    'product_commission_value' => $value,
                ];
            }

            $commission_data = \lib\input::jsonEncode($results,0);

            if($table == 'user')
            {
                $sql = "UPDATE ".root_table."user SET user_commission_data='{$commission_data}' WHERE user_id={$table_id}";

                $query = $DB->query($sql);

                $CMS->class->cache->mdelete('user');
            }
            else
            {
                $sql = "UPDATE ".root_table."user_group SET userg_commission_data='{$commission_data}' WHERE userg_id={$table_id}";

                $query = $DB->query($sql);

                $CMS->class->cache->mdelete('user_group');

                if($is_approve_user)
                {
                    $sql = "UPDATE ".root_table."user SET user_commission_data='{$commission_data}' WHERE userg_id={$table_id}";
                    $DB->query($sql);
                    $CMS->class->cache->mdelete('user');
                }
            }

            return $query;
        }

        return false;
    }

    /**
     * @param array $data
     * @return array
     */
    static function convertToPOS($data = [])
    {
        $data = self::convertKeysPOS($data);

        $oriData = $data;

        $data['oriData'] = $oriData;

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    static public function convertKeysPOS($data = [])
    {
        $return = [];

        $keys = [
            "user_id" => "id",
            "user_display_name" => "name",
            "user_email" => "email",
            "store_id" => "storeId",
        ];

        foreach ($data as $key => $value) {
            if (isset($keys[$key])) {
                $return[$keys[$key]] = $value;
            }
        }

        $return['id'] *= 1;
        $return['storeId'] *= 1;

        return $return;
    }

    public function setServices($staffId, $serviceIds = [])
    {
        global $CMS, $DB;

        $staffId *= 1;

        if (!$staffId) return false;

        //Lấy những dịch vụ đã gán cho nhân viên
        $sql = "SELECT * FROM ".root_table."product WHERE product_type = 1 AND (staff_id LIKE '%\"{
        $staffId}\"%' OR staff_id LIKE '%{$staffId}%')";

        $services = $DB->fetch_data($sql, 'product');

        foreach ($services as $service) {
            $staffIds = json_decode($service['staff_id']);

            //Gỡ staff ra khỏi danh sách của dịch vụ
            $staffIds = array_filter($staffIds, function($x) use ($staffId) {
                return $x != $staffId;
            });

            $staffIds = array_values($staffIds);

            $staffIdsJson = input::jsonEncode($staffIds, 0);

            $service['staff_id'] = $staffIdsJson;
            $DB->update('product', $service, 'product_id');
        }


        if (is_array($serviceIds) && count($serviceIds)) {
            //Lấy những dịch vụ cần gán cho nhân viên
            $serviceIdsStr = implode(",", $serviceIds);
            $sql = "SELECT * FROM ".root_table."product WHERE product_type = 1 AND product_id IN ({$serviceIdsStr})";

            $services = $DB->fetch_data($sql, 'product');

            foreach ($services as $service) {
                $staffIds = input::jsonDecode($service['staff_id']);
                $staffIds[] = $staffId;
                $staffIdsJson = input::jsonEncode($staffIds, 0);

                $service['staff_id'] = $staffIdsJson;

                $DB->update('product', $service, 'product_id');
            }
        }
    }
}

?>