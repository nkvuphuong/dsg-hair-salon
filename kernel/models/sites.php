<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->sites = new class_sites;

class class_sites {

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
	 * @param $sites_cnt
	 *		Count of sites for rearrange
	 */
	
	public $sites_cnt = 0;
	
	/**
	 * @param $previous_order
	 *		The templates
	 */
	 
	public $previous_order = 0;
	
	/**
	 * @param $parent_select
	 *		HTML of Parent Categories
	 */
	
	public $parent_select = "";

	

	//===========================================================================
	//  AUTO RUN
	//===========================================================================

	public function auto_run()
	{
		global $CMS, $DB, $member;

		$this->loadhtml();
		// Init server Cpanel
		$this->init_server();

		//-----------------------------------------------------------
		// ACTION CONTROLLER
		//-----------------------------------------------------------
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_sites_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_sites_controller_{$CMS->vars['default_language']}");
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

			$CMS->class->cache->save("user_{$member['user_id']}_sites_controller_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}
		
		if ( $CMS->vars['action_controller'] )
		{
			//$this->action_control = $this->html->control();
		}
	}	

	/**
	 *  Init info server Cpanel
	 *  Info get from config file
	*/

	public function init_server()
	{
		global $CMS, $DB, $member;
		
		$server_info['ip'] = $CMS->vars['cpanel_ip'];
		$server_info['pass'] = $CMS->vars['cpanel_pass'];
		$server_info['user'] = $CMS->vars['cpanel_user'];
		$server_info['hash'] = $CMS->vars['cpanel_hash'];
		 
		$CMS->api->cpanel->is_ssl =  $CMS->vars['cpanel_is_ssl'];
        $CMS->api->cpanel->server_info = $server_info;
        $CMS->api->cpanel->pass_is_hash = true;
        return true;
	}

	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			$this->html = $CMS->class->template->load_template("skin_sites");		
		}
	}
	
	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	 
	public function convertvalue($data)
	{
		global $CMS, $DB;
	 
		// Replace search content
		$data = $CMS->class->search->convertvalue($data);
		// Convert Register to GMT
		$data['site_time_bk'] = $CMS->class->date->date_format($data['site_time']);
		$data['code_storename_link'] = " <a href='{$CMS->vars['is_http']}{$data['code_storename']}.{$CMS->vars['storename_domain']}'>{$data['code_storename']}.{$CMS->vars['storename_domain']}</a> ";
		
		if($CMS->permit['sites_read'] == 1)
		{
			$data['site_code_bk'] = "<a href='{$CMS->vars['root_domain']}/?site=sites&act=show&id={$data['site_id']}'>{$data['site_code']}</a>";
		}
		else
		{
			$data['site_code_bk'] =  $data['site_code'];
		} 
		// Replace the Status
		$data['industry_bk'] = $CMS->lang["industry_{$data['industry']}"];
		$data['package_id_bk'] = $CMS->lang["package_id_{$data['package_id']}"];	 	
		$data['site_regtype_bk'] = $CMS->lang["site_regtype_{$data['site_regtype']}"];

		   // Site status
		switch ($data['site_status']) {
			case '0':
				$data['site_status_bk']= "<span class='label label-default'>{$CMS->lang['site_status_0']}</span>"; 
				break;
			case '1':
				$data['site_status_bk']= "<span class='label label-warning'>{$CMS->lang['site_status_1']}</span>";		 
				break;
			case '2':
				$data['site_status_bk']= "<span class='label label-success'>{$CMS->lang['site_status_2']}</span>";	 
			break;
			case '3':
				$data['site_status_bk']= "<span class='label label-danger'>{$CMS->lang['site_status_3']}</span>";	 
			break;
			case '4':
				$data['site_status_bk']= "<span class='label label-info'>{$CMS->lang['site_status_4']}</span>";	 
			break;
			case '5':
				$data['site_status_bk']= "<span class='label label-info'>{$CMS->lang['site_status_5']}</span>";	 
			break;
			case '6':
				$data['site_status_bk']= "<span class='label label-info'>{$CMS->lang['site_status_6']}</span>";	 
			break;
			case '7':
				$data['site_status_bk']= "<span class='label label-primary'>{$CMS->lang['site_status_7']}</span>";	 
			break;
			case '8':
				$data['site_status_bk']= "<span class='label label-primary'>{$CMS->lang['site_status_8']}</span>";	 
			break;
			case '9':
				$data['site_status_bk']= "<span class='label label-primary'>{$CMS->lang['site_status_9']}</span>";	 
			break;

		}


		$data['site_status_text'] = $CMS->lang["site_status_{$data['site_status']}"];

		$this->previous_order++;
		
		// Bgcolor
		$data['bgcolor'] = $this->record_cnt % 2 != 0 ? "#FFFFFF" : "#F6F6F6";
		
		// User
		$data['user_name'] = $CMS->user->get_info($data['user_id'],"user_display_name");
		
		// Count
		$data['record_cnt'] = $this->record_cnt;

		$this->record_cnt++;
		
		return $data;
	}
	

	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("site_id,site_from,site_to,site_time");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "site_id";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "DESC";
		
		// SQL Condition
		$this->sql_add .= " site_deleted=0 AND ";
 
		// Create SQL Query for listing Data
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."sites WHERE {$this->sql_add} 1=1  ORDER BY {$default_field} {$default_order}");
		$this->pages_cnt = $DB->num_rows( $this->sql_query );
	}
	
	public function html()
	{
		global $CMS, $DB, $member;
		
		$this->loadhtml();
		
		// Display Header
		$output .= $this->html->header();
		
		if ( $DB->num_rows( $CMS->sites->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->sites->sql_query ) )
			{
				// Convert info
				$result = $CMS->sites->convertvalue($result);
				
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
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites");
		}
		
		return $output;
	}

	 
	

	//===========================================================================
	//  Get last site
	//===========================================================================
	
	public function get_lastsite(  )
	{
		global $CMS, $DB, $member;
  
        $sql = "SELECT * FROM ".root_table."sites WHERE site_deleted=0 ORDER BY site_id DESC LIMIT 1";

        $sql = $DB->query($sql);

        if($DB->num_rows($sql) == 0) return false;

        return $DB->fetch_assoc($sql);
	}
	


	//===========================================================================
	//  INFO
	//===========================================================================
	
	public function get_info( $id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;
 
        if(is_numeric($id))
        {
            $id =  intval($id);
            $sql_add = " site_id = '{$id}' AND ";
        }
        elseif (\lib\input::is_domain($id))
        {
            $sql_add = " site_domainname = '{$id}' AND ";
        }
        else
        {
            $sql_add = " ( site_id='{$id}' OR site_key = '{$id}' OR site_code =  '{$id}' ) AND ";
        }

        $sql = "SELECT * FROM ".root_table."sites WHERE {$sql_add} site_deleted=0 ORDER BY site_id DESC LIMIT 1";

        $sql = $DB->query($sql);

        if($DB->num_rows($sql) == 0) return false;

        return $DB->fetch_assoc($sql);
	}

	//===========================================================================
	//  INFO
	//===========================================================================
	
	public function get_info_2( $id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;
 
        if(is_numeric($id))
        {
            $id =  intval($id);
            $sql_add = " site_id = '{$id}' AND ";
        }
        elseif (\lib\input::is_domain($id))
        {
            $sql_add = " (  site_domainname = '{$id}' OR site_domain_extra = '{$id}' ) AND ";
        }
        else
        {
            $sql_add = " ( site_id='{$id}' OR site_key = '{$id}' OR site_code =  '{$id}' ) AND ";
        }
 
        $sql = "SELECT * FROM ".root_table."sites WHERE {$sql_add} site_deleted=0 ORDER BY site_id DESC LIMIT 1";

        $sql = $DB->query($sql);

        if($DB->num_rows($sql) == 0) return false;

        return $DB->fetch_assoc($sql);
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
			$DB->query("SELECT * FROM ".root_table."sites WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND site_deleted=0");
		}
		else
		{
			$DB->query("SELECT * FROM ".root_table."sites WHERE {$field}='{$value}' AND site_deleted=0");
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
	
	public function add($cus_id = "")
	{
		global $CMS, $DB, $member;
 		$customer =  $CMS->customer->getInfo($cus_id);
		// User input
		$lastname 	= trim($customer['cus_full_name']);
	
		$phone 	  	= trim($customer['cus_phone']);
		$site_domainname_input = strtolower(trim($CMS->input['site_domainname']));
		$site_theme = intval($CMS->input['site_theme']);
		$site_cycle = $CMS->input['site_cycle'];
		$site_app_secret =  $CMS->class->random->character(32);

		// Check neu dung thu thi randoma domain
		if($CMS->input['site_regtype'] == 1)
		{
			$last_site = $this->get_lastsite();
			$new_last_site = $last_site['site_id']+1;
			$site_domainname_prefix = "demo".$new_last_site.rand(01, 99);
			$site_domainname = $site_domainname_prefix.".".$CMS->vars['storename_domain'];//$CMS->vars['storename_domain']
		}		
		else
		{
			$site_domainname = $site_domainname_input;
		}
	 
		/* Check site domainname */
		 
		if($site_domainname != "")
		{
			/* Using domain private */
			/* Check is domain */ 
			if($CMS->class->domain->is_domain($site_domainname) ==false)
			{ 
				$data_output = array( "error", "Tên miền không đúng định dạng",  "");
				return $data_output;
			} 
			$short_username = $CMS->class->editor->clean($site_domainname);
			$short_username = substr($short_username,0,6).$CMS->class->random->character(7);
			$hosting_username = strtolower($short_username);
			$hosting_username = str_replace("-", "", $hosting_username);
			// Check regmatch hosting username
			if( is_numeric(substr($hosting_username,0, 1))  ){
		 		$hosting_username = substr($hosting_username, 1);	
			}
			//Check string hosting exits "test"
			if(preg_match("/test/", $hosting_username) == true)
			{
				$new_replace = $CMS->class->random->randomcharacter(4);
				$hosting_username = str_replace("test",$new_replace, $hosting_username);
			}
			$hosting_username = strtolower($hosting_username);
			// Check first letter is numeric in string
			if(is_numeric(substr($hosting_username, 0, 1)) == true)
			{
				$hosting_username = "s".$hosting_username;
			}
			$db_name = strtolower(substr($hosting_username,0,8)."_db");
			$db_username = strtolower(substr($hosting_username,0,8)."_user");
		}
		 
		$cusweb_id = trim($customer['cus_id']);
		//Convert user email hosting
		$firstname = $CMS->class->seo->remove_vietnamese($firstname);
		$firstname = str_replace(" ", "", $firstname);
		$firstname = $CMS->class->seo->clean($firstname);
 		$lastname = $CMS->class->seo->remove_vietnamese($lastname);
		$lastname = str_replace(" ", "", $lastname);
		$lastname = $CMS->class->seo->clean($lastname);
		//==
		$full_name = $firstname."".$lastname;
		$full_name = trim($full_name);
		if(strlen($full_name) == 0)
		{
			$email_hosting =  "info@".$site_domainname;
			$email_password = $hosting_password =  $CMS->class->random->password(12)."!@";
		}
		else
		{
			//$email_hosting = strtolower($full_name)."@".$site_domainname;
			$email_hosting =  "info@".$site_domainname;
			$email_password = $hosting_password =  ucfirst(trim(substr($full_name,0,10)))."!@";
		}
	  
		
		$db_password = $CMS->class->random->password(12)."!@";
		//Info acp user
		$username 	=  "admin";
		$password 		= trim($CMS->input['password']);

		$package_id = intval($CMS->input['package_id']);
		$email = $customer['cus_email'];

		$industry = intval($CMS->input['industry']);
		$address = trim($customer['cus_address']);
 		$site_regtype = $CMS->input['site_regtype'];
 		$default_language = $CMS->input['default_language'];
 		$default_language = $default_language != "" ? $default_language : "vn";
		$time = time();
		 
		$a = '+'.$site_cycle.' months'; 
		$time_end = strtotime($a, $time); 


        $site_start_time = time();
		if (  $site_domainname != "" ) 
		{ 
			//$_SESSION['error_msg'] = "{$CMS->lang['code_storename_err_title']}";
			// Check exits code_storename
			if($this->check_exist("site_domainname",$site_domainname) == true)
			{  
				$data_output = array( "error", "Tên miền đã được sử dụng",  "");
				return $data_output;
 
			} 
 
		}

	   	// Get info theme
		$sql_theme = $DB->query("SELECT * FROM  ".root_table."themes WHERE theme_id = '{$site_theme}' AND theme_deleted = 0 ORDER BY theme_id DESC LIMIT 1");
		if($DB->num_rows($sql_theme) > 0)
		{
			$themes = $DB->fetch_array($sql_theme);
			$theme_code = strtolower($themes['theme_code']);
			if($themes['theme_link_demo'] != "")
			{
				if($themes['theme_custom'] == 1)
				{
					$clone_original_domain = $theme_code.".demo.net.vn";
				}
				else
				{
					$clone_original_domain = $CMS->class->domain->domain_filter($themes['theme_link_demo']);
				}
			}
			else
			{    
				$clone_original_domain = $theme_code.".demo.net.vn";
			}
		}
		else
		{
			$data_output = array( "error", "Vui lòng chọn theme!",  "");
			return $data_output;
		}


		$pwdoriginal = $password =   $CMS->class->random->password(12);
	 
	 	$user_password = md5($password);
		 
		// Hash + salt
		$salt = $CMS->user->create_pwd_salt(5);
		$salt = addslashes($salt);
		$pwdhash = $CMS->user->create_pwd_hash( $salt, $user_password );
 		//Get server
 		$sv_id = $this->get_server();
	 
 		
		// Insert datasta
		$DB->query("INSERT INTO ".root_table."sites (firstname, lastname,phone, email, username, pwdoriginal, pwdhash,  pwdsalt, code_storename, site_theme, site_theme_id ,storename, site_domainname, site_domain_extra, industry, location, address, package_id,site_time,site_license_expired,ip_address,site_regtype, email_hosting, email_password, hosting_username , hosting_password, db_name, db_username, db_password, default_language, cusweb_id , sv_id, site_start_time,site_status,clone_original,clone_original_domain,site_app_secret) VALUES ('{$firstname}', '{$lastname}','{$phone}', '{$email}', '{$username}', '{$pwdoriginal}' ,'{$pwdhash}', '{$salt}', '{$code_storename}', '{$theme_code}' , '{$site_theme}' , '{$storename}', '{$site_domainname}' , '{$site_domain_extra}' ,'{$industry}' ,'{$location}', '{$address}', '{$package_id}','{$time}', '{$time_end}','{$ip_address}', '{$site_regtype}','{$email_hosting}','{$email_password}','{$hosting_username}', '{$hosting_password}', '{$db_name}', '{$db_username}', '{$db_password}', '{$default_language}', '{$cusweb_id}', '{$sv_id}', '{$site_start_time}',1,1,'{$clone_original_domain}', '{$site_app_secret}')");
		
		$id = $DB->last_insert_id();
 
		$site_code = "W".$id;
		$site_key = "key_".md5($site_code);
 
	  	$DB->query("UPDATE ".root_table."sites SET site_key='{$site_key}',site_code='{$site_code}'   WHERE site_id='{$id}'");
	  		// Create log
		$CMS->class->logs->key= "site_{$id}";
		$_SESSION["msg"] .= $CMS->class->logs->insert("Gửi lệnh tạo site thành công");

		// Get info
		$site = self::get_info($id);
		
		$data_output = array("success", "Gửi lệnh tạo site thành công",  $site);
		return $data_output;
 
	}
	

	//===========================================================================
	//  Get server id
	//===========================================================================
	
	public function get_server()
	{
		global $CMS, $DB, $member;
  
        $sql_x = "SELECT * FROM ".root_table."server WHERE  sv_deleted=0 AND sv_active = 1 AND sv_type= 0 ORDER BY sv_id DESC LIMIT 1";
 		$sql = $DB->query($sql_x);
        if($DB->num_rows($sql) == 0) 
        {
        		return 0;
        }
        else
        {
       		$sv = $DB->fetch_array($sql);
       		return $sv['sv_id'];
        }
      
	}

	
	//===========================================================================
	//  Edit
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;

		// User input\
		$id = $CMS->input['id'];
		$site = $this->get_info();
 		$CMS->class->logs->key = "site_{$id}";    
		$CMS->class->logs->old_data = $site;
		$CMS->class->logs->insert($key);


		$fullname 	= trim($CMS->input['fullname']);
		$phone 	  	= trim($CMS->input['phone']);
		$storename 	= trim($CMS->input['storename']);
		$code_storename 	= trim($CMS->input['code_storename']);
		$username 	= trim($CMS->input['username']);
		$package_id = intval($CMS->input['package_id']);
 		$site_domainname = $code_storename.".".$CMS->vars['storename_domain'];
		$email = $CMS->input['email'];
		$location = $CMS->input['location'];
		$industry = intval($CMS->input['industry']);
		$address = trim($CMS->input['address']);
 		$site_regtype = $CMS->input['site_regtype'];
 		
		$time = time();
		if ( ! $storename ) 
		{ 	 
			$_SESSION['error_msg'] = "{$CMS->lang['storename_err_title']}";
			return false;
		}

		if ( ! $code_storename ) 
		{ 
			$_SESSION['error_msg'] = "{$CMS->lang['code_storename_err_title']}";
			return false;
		}

		// Check exits code_storename
		if($CMS->sites->check_exist("code_storename",$code_storename) == true AND $code_storename != $site['code_storename'])
		{  
			$_SESSION['error_msg'] = "{$CMS->lang['code_storename_err_exits']}"; return false;
		} 

		if (  $industry <= 0   ) 
		{ 
			$_SESSION['error_msg'] = "{$CMS->lang['industry_err_title']}";
			return false;
		}

		if (  $package_id <= 0   ) 
		{  
		 	$_SESSION['error_msg'] = "{$CMS->lang['packageid_err_title']}"; return false;
		}
 
		// Check input
		if ( ! $fullname ) 
		{  
			$_SESSION['error_msg'] = "{$CMS->lang['fullname_err_title']}"; return false;
		}
		if ( ! $phone ) 
		{   
			$_SESSION['error_msg'] = "{$CMS->lang['phone_err_title']}"; return false;
		}
		if ( ! $email ) 
		{   
			$_SESSION['error_msg'] = "{$CMS->lang['email_err_title']}"; return false;
		}
		if($CMS->class->input->is_email($email) == false   )
		{
			$_SESSION['error_msg'] = "{$CMS->lang['email_err_valid']}"; return false;
		}
		// Check exits email
		if($CMS->sites->check_exist("email",$email) == true  AND $email != $site['email'])
		{  
			$_SESSION['error_msg'] = "{$CMS->lang['email_err_exits']}"; return false;
		} 

		if ( ! $username ) 
		{  
			 //$_SESSION['error_msg'] = "{$CMS->lang['username_err_title']}"; return false;
		}
  
		// Update data
		 
	  	$DB->query("UPDATE `".root_table."sites` SET `fullname`='{$fullname}',`phone`='{$phone}' ,`email`='{$email}', `code_storename`='{$code_storename}',`storename`='{$storename}', `site_domainname` = '{$site_domainname}' ,`industry`='{$industry}',`location`='{$location}',`address`='{$address}',`package_id`='{$package_id}',`site_regtype`='{$site_regtype}' WHERE `site_id`='{$id}'");
	  		// Create log
		$CMS->class->logs->key= "site_{$id}";
		// Get info
		$new_site = $this->get_info($id);

		$CMS->class->logs->save_detail("sites",$id,$new_site);

		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['edit_site_succes']}");

		return $new_site;
	}
	 

	 /**
	  * Function: ACtion site 
	  * Param: info site
	  */
	public function action($site = "", $action = 0)
	{
		global $CMS, $DB, $member;
		
		if($action == 1)//Kich hoat
		{
			// Update status
	  		$DB->query("UPDATE ".root_table."sites SET site_status = 1 , site_created = 0 WHERE site_id='{$site['site_id']}'");
	  
	  		
		} 
		$CMS->class->logs->key= "site_{$site['site_id']}";
		$msg = $CMS->class->logs->insert("[API] Gửi lệnh kích hoạt site thành công");		
		// Get info
		$site = self::get_info($site['site_id']);

		$data_output = array("success", "[API] Gửi lệnh kích hoạt site thành công",  $site);
		return $data_output; 
	}


	/**
	  * Function: API Renew site 
	  * Param: info site
	  */
	public function renew($site = "", $cycle = 0)
	{
		global $CMS, $DB, $member;
		
 
		$a = '+'.$cycle.' months'; 
		$newTimestamp = strtotime($a, $site['site_license_expired']); 
 
		// Update status
	  	$DB->query("UPDATE ".root_table."sites SET site_license_expired = '{$newTimestamp}'   WHERE site_id='{$site['site_id']}'");	
	 
		$CMS->class->logs->key= "site_{$site['site_id']}";
		$msg = $CMS->class->logs->insert("[API] Gửi lệnh gia hạn website thành công");		
		// Get info
		$site = self::get_info($site['site_id']);

		$data_output = array("success", "[API] Gửi lệnh gia hạn website thành công",  $site);
		return $data_output; 
	}




	 /**
	  * Function: Active site -> create site -> create host -> import DB
	  * Param: info site
	  */
	public function active()
	{
		global $CMS, $DB, $member;
		$id= $CMS->input['id'];
		$site  = $this->get_info();
	 	
		if(!is_array($site))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['site_dosenot_exites']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites");	
			return false;
		}

		$params['hosting_domain'] = $site['site_domainname'];
  		$params['hosting_username'] = $site['hosting_username'];
 		$params['hosting_password'] =  $site['hosting_password'];
  		$params['hosting_email'] = $site['email'];
 		$params['package_name'] ="plan_1";
 		$return_hosting = $CMS->api->cpanel->add_hosting($params);

		// Check create hosting
		if($return_hosting['status'] == 1)//Success
		{
			// Update status
	  		$DB->query("UPDATE `".root_table."sites` SET site_status = 2, activity_log='{$return_hosting['msg']}' WHERE `site_id`='{$id}'");
	  		$_SESSION['msg'] = "{$CMS->lang['create_site_success']}";
	  		return true;
			
		}
		else
		{
			// Update status
	  		$DB->query("UPDATE `".root_table."sites` SET site_status = 3, activity_log='{$return_hosting['msg']}' WHERE `site_id`='{$id}'");
	  		$_SESSION['error_msg'] = "{$CMS->lang['create_site_error']}";
	  		return false;
			
		}
	}

	 /**
	  * Function: Suspend site hosting 
	  * Param: info site
	  */
	 public function suspend()
	{
		global $CMS, $DB, $member;
		$id= $CMS->input['id'];
		$site  = $this->get_info();

		if(!is_array($site))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['site_dosenot_exites']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites");	
			return false;
		}

		$params['hosting_username'] = $site['hosting_username'];
  		 
 		$return_hosting = $CMS->api->cpanel->suspend_hosting($params);

		// Check create hosting
		if($return_hosting['status'] == 1)//Success
		{
			// Update status
	  		$DB->query("UPDATE `".root_table."sites` SET site_status = 5  WHERE `site_id`='{$id}'");
	  		$_SESSION['msg'] = "{$CMS->lang['suspend_site_success']}";
	  		$CMS->class->logs->key= "site_{$id}";
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['suspend_site_success']}");


	  		return true;
			
		}
		else
		{
			// Update status
	  		$DB->query("UPDATE `".root_table."sites` SET site_status = 6  WHERE `site_id`='{$id}'");
	  		$_SESSION['error_msg'] = "{$CMS->lang['suspend_site_error']}";
	  		$CMS->class->logs->key= "site_{$id}";
			$CMS->class->logs->insert("{$CMS->lang['suspend_site_success']} <br /> {$return_hosting['msg']}");

	  		return false;
			
		}
	}


	 /**
	  * Function: UnSuspend site hosting 
	  * Param: info site
	  */
	public function unsuspend()
	{
		global $CMS, $DB, $member;
		$id= $CMS->input['id'];
		$site  = $this->get_info();
	 
		if(!is_array($site))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['site_dosenot_exites']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites");	
			return false;
		}

		$params['hosting_username'] = $site['hosting_username'];
  		 
 		$return_hosting = $CMS->api->cpanel->unsuspend_hosting($params);

		// Check create hosting
		if($return_hosting['status'] == 1)//Success
		{
			// Update status
	  		$DB->query("UPDATE `".root_table."sites` SET site_status = 8  WHERE `site_id`='{$id}'");
	  		$_SESSION['msg'] = "{$CMS->lang['unsuspend_site_success']}";
	  		$CMS->class->logs->key= "site_{$id}";
			$CMS->class->logs->insert("{$CMS->lang['unsuspend_site_success']}");


	  		return true;
			
		}
		else
		{
			// Update status
	  		$DB->query("UPDATE `".root_table."sites` SET site_status = 9  WHERE `site_id`='{$id}'");
	  		$_SESSION['error_msg'] = "{$CMS->lang['unsuspend_site_error']}";
	  		$CMS->class->logs->key= "site_{$id}";
			$CMS->class->logs->insert("{$CMS->lang['unsuspend_site_error']} <br /> {$return_hosting['msg']}");

	  		return false;
			
		}
	}

	 
}

?>