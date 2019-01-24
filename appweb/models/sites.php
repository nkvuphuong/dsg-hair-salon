<?php

namespace models;
 
use core\ezy;
use lib\date;
use lib\input;
use \lib\template;

ezy::load_model("server"); 

class sites {

	/**
	 * @param @record_cnt
	 *		The order number of Data
	 */
	
	static private $record_cnt = 0;
	
	/**
	 * @param @arrange_data
	 *		Arrange Data, using for re-order the listing
	 */
	
	static private $arrange_data = "";
	
	/**
	 * @param @sql_query
	 *		The SQL Query for listing Data
	 */
	 
	static private $sql_query = "";

	/**
	 * @param @sql_add
	 *		The additional SQL for $sql_query
	 */

	static private $sql_add = "";
	
	/**
	 * @param $control
	 *		0 for no control, 1 for has control, DONT CHANGE the default value
	 */
	
	static private $control = 0;
	
	/**
	 * @param $action_control
	 *		HTML action control
	 */
	
	static private $action_control = "";
	
	/**
	 * @param $html
	 *		The templates
	 */
	 
	static private $html;
	
	/**
	 * @param $sites_cnt
	 *		Count of sites for rearrange
	 */
	
	static private $sites_cnt = 0;
	
	/**
	 * @param $previous_order
	 *		The templates
	 */
	 
	static private $previous_order = 0;
	
	/**
	 * @param $parent_select
	 *		HTML of Parent Categories
	 */
	
	static private $parent_select = "";

	static private $show_page = "";
	 /**
     * @param $sqlQuery
     *		The SQL Query for listing Data
     */
    static public $sqlQuery;

    /**
     * @param $maxPage
     *		Number of records on per page
     */
    static public $maxPage = 20;

    /**
     * @param $prefixPaging
     *		Prefix for paging url
     */
    static public $prefixPaging = '';

    /**
     * @param $prefixPaging
     *		Suffix for paging url
     */
    static public $suffixPaging = '';

    /**
     * @var array
     * @param $regType
     */
    static public $regType = [0,1,2];

	static public function getInfo($id = null, $field_name = '') {
		if (! is_null($id)) {
			 
			global $CMS, $DB, $member;
			$sql = $DB->query("SELECT * FROM ".root_table."sites WHERE (site_id='{$id}' OR site_key = '{$id}' OR site_code='{$id}' OR cusweb_id='{$id}') AND site_deleted=0 ORDER BY site_id DESC LIMIT 1");
	 
			if ($DB->num_rows($sql) > 0) {
				return $DB->fetch_array($sql);
			}
		}
		return false;
	}
	static public function getMaxId() {
		global $CMS, $DB;
        $sql = $DB->query("SELECT max(site_id) as max_id from ".root_table."sites");
        while ($result = $DB->fetch_assoc($sql)) {
            $response = $result['max_id'];
        }
		return empty($response) ? 0 : intval($response);
	}
	
	static public function getSiteRegtype($id = null, $regtype = '1') {
		if (! is_null($id)) {
			global $CMS, $DB, $member;
			$sql = $DB->query("SELECT * FROM ".root_table."sites WHERE (cusweb_id='{$id}' OR email='{$id}') AND site_regtype = '{$regtype}' AND site_deleted = 0 ORDER BY site_id DESC LIMIT 1");
 			if ($DB->num_rows($sql) > 0) {
				return $DB->fetch_array($sql);
			}
		}
		return false;
	}
	static public function getSiteAll($id = null) {
		if (! is_null($id)) {
			global $CMS, $DB, $member;
			$sql = $DB->query("SELECT * FROM ".root_table."sites WHERE cusweb_id='{$id}' AND site_deleted = 0 ORDER BY site_id DESC");
			if ($DB->num_rows($sql) > 0) {
				while($data = $DB->fetch_array($sql)) {
					$result[] = $data;
				}
				return $result;
			}
		}
		return false;
	}
	static public function add() {
		global $CMS, $DB, $member; 
		$lastname 	= trim($CMS->input['lastname']);
		$firstname 	= trim($CMS->input['firstname']);

		$phone 	  	= trim($CMS->input['phone']);
		$storename 	= trim($CMS->input['storename']);
		$code_storename 	= trim($CMS->input['code_storename']);
		$site_domainname = trim($CMS->input['site_domainname']);
		$site_theme = intval($CMS->input['site_theme']);
 		$site_app_secret =  $CMS->class->random->character(32);
		if($site_domainname == "" AND $code_storename == "") { 
			$_SESSION['error_msg'] = "{$CMS->lang['domainname_notempty_err_title']}";
			return false;
		}
		if (empty($site_theme)) { 
			$_SESSION['error_msg'] = "{$CMS->lang['theme_notempty_err_title']}";
			return false;
		} 
		$site_domainname = $code_storename.".".$CMS->vars['storename_domain'];
		$hosting_username = substr($code_storename,0,6).$CMS->class->random->character(7);
		$hosting_username = strtolower($hosting_username);
		$hosting_username = str_replace("-", "", $hosting_username);
		if (is_numeric(substr($hosting_username,0, 1))) {
			$hosting_username = substr($hosting_username, 1);	
		}
		
		if (preg_match("/test/", $hosting_username) == true) {
			$new_replace = $CMS->class->random->randomcharacter(4);
			$hosting_username = str_replace("test",$new_replace, $hosting_username);
		}
		$hosting_username = strtolower($hosting_username);
		if (is_numeric(substr($hosting_username, 0, 1)) == true) {
			$hosting_username = "s".$hosting_username;
		}

		$db_name = strtolower(substr($hosting_username,0,8)."_db");
		$db_username = strtolower(substr($hosting_username,0,8)."_user");
		$cusweb_id = trim($CMS->input['cusweb_id']);
		$firstname = $CMS->class->seo->remove_vietnamese($firstname);
		$firstname = str_replace(" ", "", $firstname);
		$firstname = $CMS->class->seo->clean($firstname);
 		$lastname = $CMS->class->seo->remove_vietnamese($lastname);
		$lastname = str_replace(" ", "", $lastname);
		$lastname = $CMS->class->seo->clean($lastname);
		
		$full_name = trim($firstname." ".$lastname);
		$full_name = empty($full_name) ? 'info' : $full_name;
		//$email_hosting = strtolower($full_name)."@".$site_domainname;
	  	$email_hosting =  "info@".$site_domainname;	
		$email_password = $hosting_password = ucfirst(substr($full_name,0,5))."!@".$CMS->class->random->password(5);
		$db_password = $CMS->class->random->password(12)."!@";
		
		$username 	=  "admin";
		$password 		= trim($CMS->input['password']);

		$package_id = intval($CMS->input['package_id']);
		$email = $CMS->input['email'];
		$location = $CMS->input['location'];
		$industry = intval($CMS->input['industry']);
		$address = trim($CMS->input['address']);
 		$site_regtype = $CMS->input['site_regtype'];
 		$default_language = $CMS->input['default_language'];
 		$default_language = $default_language != "" ? $default_language : "vn";
		$site_time = empty($CMS->input['site_time']) ? time() : $CMS->input['site_time'];
		$site_license_expired = empty($CMS->input['site_license_expired']) ? ($site_time + (10 * 24 * 60 * 60)) : $CMS->input['site_license_expired'];
        $site_start_time = time();
		$addon_warehouse = in_array(intval($CMS->input['addon_warehouse']), array(0, 1)) ? intval($CMS->input['addon_warehouse']) : 0;
		if (! $storename ) { 	 
			$_SESSION['error_msg'] = "{$CMS->lang['storename_err_title']}";
			return false;
		}

		if ($site_domainname != "" ) { 
			if (self::check_exist("site_domainname",$site_domainname) == true) {  
				$_SESSION['error_msg'] = "{$CMS->lang['code_storename_err_exits']}"; 
				return false;
			} 
		}
	
		if ($industry <= 0) { 
			$_SESSION['error_msg'] = "{$CMS->lang['industry_err_title']}";
			return false;
		}
		if ($package_id <= 0) {  
		 	$_SESSION['error_msg'] = "{$CMS->lang['packageid_err_title']}"; return false;
		}
		if (! $phone) {   
			$_SESSION['error_msg'] = "{$CMS->lang['phone_err_title']}"; return false;
		}
		if (! $email ) {   
			$_SESSION['error_msg'] = "{$CMS->lang['email_err_title']}"; return false;
		}
		if ($CMS->class->input->is_email($email) == false) {
			$_SESSION['error_msg'] = "{$CMS->lang['email_err_valid']}"; return false;
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
			$_SESSION['error_msg'] = "{$CMS->lang['theme_notempty_err_title']}";
			return false;
		}
	 
		$pwdoriginal = $password =  $hosting_password;
	 	$user_password = md5($password);
		$salt = $CMS->user->create_pwd_salt(5);
		$salt = addslashes($salt);
		$pwdhash = $CMS->user->create_pwd_hash( $salt, $user_password );
 		$sv_id = self::get_server();
  
		// Insert data
		$DB->query("INSERT INTO ".root_table."sites (firstname, lastname,phone, email, username, pwdoriginal, pwdhash,  pwdsalt, code_storename, site_theme, site_theme_id , storename, site_domainname, site_domain_extra, industry, location, address, package_id,ip_address,site_regtype, email_hosting, email_password, hosting_username , hosting_password, db_name, db_username, db_password, default_language, cusweb_id , sv_id, site_start_time, addon_warehouse, site_time,site_license_expired,site_status,clone_original,clone_original_domain, site_app_secret) VALUES ('{$firstname}', '{$lastname}','{$phone}', '{$email}', '{$username}', '{$pwdoriginal}' ,'{$pwdhash}', '{$salt}', '{$code_storename}', '{$theme_code}' ,'{$site_theme}' , '{$storename}', '{$site_domainname}' , '{$site_domain_extra}' ,'{$industry}' ,'{$location}', '{$address}', '{$package_id}', '{$ip_address}', '{$site_regtype}','{$email_hosting}','{$email_password}','{$hosting_username}', '{$hosting_password}', '{$db_name}', '{$db_username}', '{$db_password}', '{$default_language}', '{$cusweb_id}', '{$sv_id}', '{$site_start_time}', '{$addon_warehouse}', '{$site_time}', '{$site_license_expired}',1,1,'{$clone_original_domain}', '{$site_app_secret}')");//Chueyn cho kich hoat lun , k doi cron
		
		$id = $DB->last_insert_id();
		$site_code = "W".$id;
		$site_key = "key_".md5($site_code);
		
	  	$DB->query("UPDATE ".root_table."sites SET site_key='{$site_key}',site_code='{$site_code}'   WHERE site_id='{$id}'");
	  	
		$CMS->class->logs->key= "site_{$id}";
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['create_site_succes']}");

		$site = self::getInfo($id);
		return $site;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//===========================================================================
	//  AUTO RUN
	//===========================================================================

	static public function auto_run()
	{
		global $CMS, $DB, $member;
		
		self::loadhtml();
		// Init server Cpanel
		self::init_server();

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
			//	$this->control = 1;
			}
			// Check permission to Arrange
		//	$data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

			$CMS->class->cache->save("user_{$member['user_id']}_sms_controller_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}
		
		 
	}	

	/**
	 *  Init info server Cpanel
	 *  Info get from config file
	*/

	static public function init_server($server = "", $type = 0)
	{
		global $CMS, $DB, $member;
		

		if($type == 1)
		{
			$CMS->vars['ssh_server_real'] = $server['sv_ip'];
			$CMS->vars['ssh_user_real'] = $server['sv_username'];
			$CMS->vars['ssh_pass_real'] = $server['sv_password'];
			$CMS->vars['ssh_port_real'] = $server['sv_port'];
			$CMS->vars['ssh_rsync_key_real'] = $server['sv_rsync_key'];
		}
		else
		{  
			$CMS->vars['ssh_server'] = $server['sv_ip'];
			$CMS->vars['ssh_user'] = $server['sv_username'];
			$CMS->vars['ssh_pass'] = $server['sv_password'];
			$CMS->vars['ssh_port'] = $server['sv_port'];
			$CMS->vars['ssh_rsync_key'] = $server['sv_rsync_key'];
		}
	

		$server_info['ip'] =   $server['sv_ip'];
		$server_info['pass'] = $server['sv_password'];
		$server_info['user'] = $server['sv_username'];
		$server_info['hash'] = $server['sv_hash'];
	 
		$CMS->api->cpanel->is_ssl =  $server['sv_is_ssl'];
        $CMS->api->cpanel->server_info = $server_info;
        $CMS->api->cpanel->pass_is_hash = true;

        $CMS->api->cpanel_v2->is_ssl =  $server['sv_is_ssl'];
        $CMS->api->cpanel_v2->server_info = $server_info;
        $CMS->api->cpanel_v2->pass_is_hash = true;

        return true;
	}

	static public function loadhtml()
	{
		global $CMS;
		
		if ( !isset(self::$html) )
		{
            self::$html = $CMS->class->template->load_template("skin_sites");
		}
	}
	
	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	 
	static public function convertvalue($data)
	{
		global $CMS, $DB;
 
		// Replace search content
		$data = $CMS->class->search->convertvalue($data);
		// Convert Register to GMT
		$data['site_time_bk'] =  $data['site_time'] > 0 ? $CMS->class->date->date_format($data['site_time']) : "N/A";
		$data['site_start_time_bk'] = $data['site_start_time'] ? $CMS->class->date->date_format($data['site_start_time']) : '';

		$data['change_domain_time_bk'] = $data['change_domain_time'] > 0 ?$CMS->class->date->date_format($data['change_domain_time']) : "N/A" ;

		$data['site_time_moved_bk'] = $CMS->class->date->date_format($data['site_time_moved']);

		$data['code_storename_link'] = " <a href='{$CMS->vars['is_http']}{$data['site_domainname']}'>{$data['site_domainname']}</a> ";
		
		$data['site_domain_extra_link'] = " <a href='{$CMS->vars['is_http']}{$data['site_domain_extra']}'>{$data['site_domain_extra']}</a> ";
		

		if($CMS->permit['sites_read'] == 1)
		{
			$data['site_code_bk'] = "<a href='{$CMS->vars['root_domain']}/?site=sites&act=show&id={$data['site_id']}'>{$data['site_code']}</a>";
		}
		else
		{
			$data['site_code_bk'] =  $data['site_code'];
		} 
 
		if($data['email'] != "")
		{
			$cus = $CMS->customer->getInfo($data['email']);
			 
			if(is_array($cus))
			{
				$data['cus_url'] = "<a class=\"pull-right\" href='{$CMS->vars['root_domain']}/?site=customer&act=show&id={$cus['cus_id']}'>{$CMS->lang['customer_url']}</a>";
			}
		}
		// Replace the Status
		$data['industry_bk'] = $CMS->lang["industry_{$data['industry']}"];
		$data['package_id_bk'] = $CMS->lang["package_id_{$data['package_id']}"];	 	
		$data['site_regtype_bk'] = $CMS->lang["site_regtype_{$data['site_regtype']}"];
		$data['clone_original_bk'] = $CMS->lang["clone_original_{$data['clone_original']}"];

		if($data['site_moved'] == 1)
		{

		   $data['site_moved_bk']= "<span class='label label-success'>{$CMS->lang['site_moved_1']}</span>"; 
		}
		//Get server
		if($data['sv_id'] > 0 )
		{
			$sv_demo_info = \models\server::get_info($data['sv_id']);
 			$data['sv_demo_info']= $sv_demo_info['sv_ip']; 
		 
		}
		if($data['sv_id_real'] > 0 )
		{
			$sv_prod_info = \models\server::get_info($data['sv_id_real']);
 			$data['sv_prod_info']= $sv_prod_info['sv_ip']; 
		 
		}
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
			case '10':
				$data['site_status_bk']= "<span class='label  label-brown'>{$CMS->lang['site_status_10']}</span>";	 
			break;
			case '11':
				$data['site_status_bk']= "<span class='label  label-brown'>{$CMS->lang['site_status_11']}</span>";	 
			break;
			case '12':
				$data['site_status_bk']= "<span class='label  label-pink'>{$CMS->lang['site_status_12']}</span>";	 
			break;
			case '13':
				$data['site_status_bk']= "<span class='label  label-pink'>{$CMS->lang['site_status_13']}</span>";	 
			break;
			case '14':
				$data['site_status_bk']= "<span class='label  label-grey-blue'>{$CMS->lang['site_status_14']}</span>";	 
			break;
			case '15':
				$data['site_status_bk']= "<span class='label  label-grey-blue'>{$CMS->lang['site_status_15']}</span>";	 
			break;
			case '16':
				$data['site_status_bk']= "<span class='label  label-violet'>{$CMS->lang['site_status_16']}</span>";	 
			break;
			case '17':
				$data['site_status_bk']= "<span class='label  label-violet'>{$CMS->lang['site_status_17']}</span>";	 
			break;
			case '18':
				$data['site_status_bk']= "<span class='label  label-pink'>{$CMS->lang['site_status_18']}</span>";	 
			break;
			case '19':
				$data['site_status_bk']= "<span class='label  label-pink'>{$CMS->lang['site_status_19']}</span>";	 
			break;
			case '20':
				$data['site_status_bk']= "<span class='label  label-violet'>{$CMS->lang['site_status_20']}</span>";	 
			break;
			case '21':
				$data['site_status_bk']= "<span class='label  label-violet'>{$CMS->lang['site_status_21']}</span>";	 
			break;
			case '22':
				$data['site_status_bk']= "<span class='label  label-brown'>{$CMS->lang['site_status_22']}</span>";	 
			break;
			case '23':
				$data['site_status_bk']= "<span class='label  label-brown'>{$CMS->lang['site_status_23']}</span>";	 
			break;

		}


		$data['site_status_text'] = $CMS->lang["site_status_{$data['site_status']}"];
 
		$data['mode_debug_bk'] = $CMS->lang["mode_debug_{$data['environment']}"];
		// Bgcolor
		$data['bgcolor'] = self::$record_cnt % 2 != 0 ? "#FFFFFF" : "#F6F6F6";
		
		// User
		$data['user_name'] = $CMS->user->get_info($data['user_id'],"user_display_name");
		
		// Count
		$data['record_cnt'] = self::$record_cnt;

		self::$record_cnt++;
		
		return $data;
	}
	

	static public function listing()
	{
		global $CMS, $DB;

		// Update Arrange Data
		$arrange_data = trim("site_id,site_from,site_to,site_time");
		
		// Set default for Arrange
		$default_field = $CMS->input['order'] ? $CMS->input['order'] : "site_id";
		$default_order = $CMS->input['by'] ? $CMS->input['by'] : "DESC";
		
		// SQL Condition
		$sql_add = " site_deleted=0 AND ";

 		// /p_sites_name
		if ( $CMS->input['p_sites_name'] != "" ) {
			$p_sites_name = urldecode($CMS->input['p_sites_name']);

            $sql_add .= " (`site_code` LIKE '%{$p_sites_name}%' OR `site_domainname` LIKE '%{$p_sites_name}%' OR `old_site_domainname` LIKE '%{$p_sites_name}%' OR `site_domain_extra` LIKE '%{$p_sites_name}%' OR `cusweb_id` LIKE '%{$p_sites_name}%' OR `site_theme` LIKE '%{$p_sites_name}%' ) AND ";
		}

		if(isset($CMS->input['type']) && trim($CMS->input['type']!=''))
        {
            $site_regtype= intval($CMS->input['type']);
            $sql_add .= " site_regtype='{$site_regtype}' AND ";
        }

        $server_filter = implode(',',$CMS->input['server_filter']);
		if(trim($server_filter) != '')
        {
            $sql_add .= " sv_id IN ($server_filter) AND ";
        }

        $keyword = trim(urldecode($CMS->input['keyword']));
        if($keyword)
        {
            $sql_add .=  " (`site_code` LIKE '%{$keyword}%' OR `site_domainname` LIKE '%{$keyword}%' OR `old_site_domainname` LIKE '%{$keyword}%' OR `site_domain_extra` LIKE '%{$keyword}%' OR `cusweb_id` LIKE '%{$keyword}%' OR `site_theme` LIKE '%{$keyword}%' ) AND ";
        }

		$sql = "SELECT * FROM  ".root_table."sites WHERE {$sql_add} 1=1 ORDER BY {$default_field} {$default_order}";

        // Create SQL Query for listing Data
        list($CMS->show_page, self::$sqlQuery) = $CMS->class->page->create($sql, self::$maxPage, self::$prefixPaging, self::$suffixPaging);
 

	 	$data = array();
		//$this->pages_cnt = $DB->num_rows( $this->sql_query );
		if ( $DB->num_rows( self::$sqlQuery ) > 0 )
		{
			while( $result = $DB->fetch_array(  self::$sqlQuery) )
			{
				// Convert info
				$data[] = self::convertvalue($result);
				 
			}
		}
		 
		return $data;
	}
	
	public function html()
	{
		global $CMS, $DB, $member;
		
		$this->loadhtml();
		
		// Display Header
		$output = $this->html->header();
		
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
	
	static public function check_exist( $field, $value = "", $except_value = "" )
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
	//  Edit
	//===========================================================================
	
	static public function edit()
	{
		global $CMS, $DB, $member;

		// User input\
		$id = $CMS->input['id'];
		$site = self::get_info($id);
 		$CMS->class->logs->key = "site_{$id}";    
		$CMS->class->logs->old_data = $site;
		$CMS->class->logs->insert($key);


		 // User input
				// User input
		$lastname 	= trim($CMS->input['lastname']);
		$firstname 	= trim($CMS->input['firstname']);

		$storename 	= trim($CMS->input['storename']);
		//$code_storename 	= trim($CMS->input['code_storename']);
		//$site_domainname = trim($CMS->input['site_domainname']);
		$site_theme = $CMS->input['site_theme'];
		$new_hosting_username = trim($CMS->class->editor->input('new_hosting_username'));
	  	$hosting_password = trim($CMS->class->editor->input('hosting_password'));
 		
 		if($hosting_password != "")
 		{
 			//Check password
 			if($CMS->class->random->checker_password($hosting_password) == false)
 			{
 				$_SESSION['error_msg'] = "{$CMS->lang['password_is_bad']}";
				return false;
 			}
 		}
 		else
 		{
 			$hosting_password = $site['hosting_password'];
 		}

 		if($new_hosting_username != "")
 		{
 			if($CMS->class->random->checker_username($new_hosting_username) == false)
			{
				$_SESSION['error_msg'] = "{$CMS->lang['username_is_not_valid']}";
				return false;
			}
			// Check strle
			if(strlen($new_hosting_username) <= 11)
			{
				$_SESSION['error_msg'] = "{$CMS->lang['username_is_not_valid']}";
				return false;
			}
			// Generate user hosting
		 	 
			$hosting_username = strtolower($new_hosting_username);
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
 		else
 		{
 			$hosting_username = $site['hosting_username'];
 			$db_name = $site['db_name'];
 			$db_username = $site['db_username'];

 		}

 		$cusweb_id = trim($CMS->input['cusweb_id']);
		$package_id = intval($CMS->input['package_id']);

		$lastname 	= trim($CMS->input['lastname']);
		$firstname 	= trim($CMS->input['firstname']);
		$phone 	  	= trim($CMS->input['phone']);
		$email = $CMS->input['email'];
		$location = $CMS->input['location'];
		$industry = intval($CMS->input['industry']);
		$address = trim($CMS->input['address']);
 		$site_regtype = $CMS->input['site_regtype'];
 		$default_language = $CMS->input['default_language'];
 		$default_language = $default_language != "" ? $default_language : "vn";
		$time = time();

        $site_start_time = $CMS->input['site_start_time'] ? $CMS->class->date->date2time($CMS->input['site_start_time']) : 0;

        if($CMS->class->logs->old_data['site_regtype']!=0 && $site_regtype==0 && $site_start_time == 0)
        {
            $site_start_time = $CMS->class->date->gmt($CMS->vars['this_day'],$CMS->vars['this_month'],$CMS->vars['this_year']);
        }

		if ( ! $storename ) 
		{ 	 
			$_SESSION['error_msg'] = "{$CMS->lang['storename_err_title']}";
			return false;
		}

		if (  $code_storename != "" ) 
		{ 
			//$_SESSION['error_msg'] = "{$CMS->lang['code_storename_err_title']}";
			// Check exits code_storename
			if(self::check_exist("code_storename",$code_storename) == true)
			{  
				$_SESSION['error_msg'] = "{$CMS->lang['code_storename_err_exits']}"; 
				return false;
			} 
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
		if ( ! $lastname ) 
		{  
			$_SESSION['error_msg'] = "{$CMS->lang['lastname_err_title']}"; return false;
		}
		if ( ! $firstname ) 
		{  
			$_SESSION['error_msg'] = "{$CMS->lang['firstname_err_title']}"; return false;
		}
		if ( ! $phone ) 
		{   
			$_SESSION['error_msg'] = "{$CMS->lang['phone_err_title']}"; return false;
		}
		if ( ! $email ) 
		{   
			$_SESSION['error_msg'] = "{$CMS->lang['email_err_title']}"; return false;
		}
		if($CMS->class->input->is_email($email) == false )
		{
			$_SESSION['error_msg'] = "{$CMS->lang['email_err_valid']}"; return false;
		}
		 
 		
 		//Convert user email hosting
		$firstname = $CMS->class->seo->remove_vietnamese($firstname);
		$firstname = str_replace(" ", "", $firstname);
		$firstname = $CMS->class->seo->clean($firstname);
 		$lastname = $CMS->class->seo->remove_vietnamese($lastname);
		$lastname = str_replace(" ", "", $lastname);
		$lastname = $CMS->class->seo->clean($lastname);
		//==
		$full_name = $firstname."".$lastname;
		//$email_hosting = strtolower($full_name)."@".$site['site_domainname'];
		$email_hosting =  "info@".$site['site_domainname'];

		// Check neu web site da tao thi k ran lai pass acp
		
		if($site['site_created'] == 1)
		{
			$pwdoriginal = $site['pwdoriginal'];
			$pwdhash = $site['pwdhash'];
			$salt = $site['pwdsalt'];
			
 			$db_name = $site['db_name'];
 			$db_username = $site['db_username'];
		}
		else
		{
			$pwdoriginal = $password =  $hosting_password;
			$user_password = md5($password);
			// Hash + salt
			$salt = $CMS->user->create_pwd_salt(5);
			$salt = addslashes($salt);
			$pwdhash = $CMS->user->create_pwd_hash( $salt, $user_password );
		}
 		
 		
 		
  		$DB->query("UPDATE ".root_table."sites SET storename = '{$storename}', firstname='{$firstname}', lastname='{$lastname}',phone='{$phone}' ,email='{$email}' ,industry='{$industry}',location='{$location}',address='{$address}',package_id='{$package_id}',site_regtype='{$site_regtype}', site_theme= '{$site_theme}', default_language='{$default_language}', hosting_password= '{$hosting_password}', cusweb_id = '{$cusweb_id}', hosting_username = '{$hosting_username}', db_name = '{$db_name}', db_username = '{$db_username}',  pwdoriginal = '{$pwdoriginal}', pwdhash = '{$pwdhash}',  pwdsalt = '{$salt}', email_hosting='{$email_hosting}', site_start_time='{$site_start_time}' WHERE site_id='{$id}'");
 
	   
	  	// Create log
		$CMS->class->logs->key= "site_{$id}";
		// Get info
		$new_site = self::get_info($id);

		//$CMS->class->logs->save_detail("sites",$id,$new_site);

		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['edit_site_success']}");

		return $new_site;
	}
	 

	static public function delete()
	{
		global $CMS, $DB, $member;

		// User input\
		$id = $CMS->input['id'];
		$site = self::get_info($id);
		if(!is_array($site))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['site_dosenot_exites']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites");	
			return false;
		}
		// Del hosting
		$server_info = \models\server::get_info($site['sv_id']);
		self::init_server($server_info);	
		$params['hosting_domain'] = $site['site_domainname'];
	  	$params['hosting_username'] = $site['hosting_username'];
	 	$params['hosting_password'] =  $site['hosting_password'];
	  	$params['hosting_email'] = $site['email'];
	 	$params['package_name'] ="plan_1";

		$del_hosting = $CMS->api->cpanel->delete_hosting($params);
		if($del_hosting['status'] == 0)//del hosting error
		{
			
	 		$CMS->class->logs->key= "site_{$id}";
			$CMS->class->logs->insert("[System] {$del_hosting['msg']}");
		}
		else
		{
			$DB->query("UPDATE ".root_table."sites SET site_deleted = 1 WHERE site_id='{$id}'");
			// Create log
			$CMS->class->logs->key= "site_{$id}";
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['deleted_site_success']}");
			return true;
		}
		return true;
 
	}


	static public function delete_hosting($site = "")
	{
		global $CMS, $DB, $member;

		$id= $site['site_id'];
		 
		$server_info = \models\server::get_info($site['sv_id']);

		self::init_server($server_info);
 
		$params['hosting_domain'] = $site['site_domainname'];
  		$params['hosting_username'] = $site['hosting_username'];
 		$params['hosting_password'] =  $site['hosting_password'];
  		$params['hosting_email'] = $site['email'];
 		$params['package_name'] ="plan_1";

 
		if(!is_array($site))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['site_dosenot_exites']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites");	
			return false;
		}
		 
		$del_hosting = $CMS->api->cpanel->delete_hosting($params);
		if($del_hosting['status'] == 0)//del hosting error
		{
			
	 		$CMS->class->logs->key= "site_{$id}";
			$CMS->class->logs->insert("[System] Site_{$id} {$CMS->lang['deleted_sitehosting_error']}  {$del_hosting['msg']} ");
			return false;
		}
		else
		{
			 
			// Create log
			$CMS->class->logs->key= "site_{$id}";
			$_SESSION["msg"] .= $CMS->class->logs->insert("[System] Site_{$id} {$CMS->lang['deleted_sitehosting_success']}");
			return true;
		}
		return true;
 
	}



	/**
	  * Function: Action Rsync all site -> receive action 
	  * Param: info site
	  */
	static public function act_rsync_all()
	{
		global $CMS, $DB, $member;
		 
		// Update status
	  	$DB->query("UPDATE ".root_table."sites SET site_status = 12  WHERE   site_created = 1 AND is_suspend = 0 ");
	  
	  	//$CMS->class->logs->key= "site_{$id}";
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['receive_rsync_success']}");
		return true;

 	}


	/**
	  * Function: Action Rsync site -> receive action 
	  * Param: info site
	  */
	static public function act_rsync()
	{
		global $CMS, $DB, $member;
		$id= $CMS->input['id'];
		$site  =self::get_info($id);
		 
 		if(!is_array($site))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['site_dosenot_exites']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites");	
			return false;
		}
		if($site['site_created'] != 1 )
		{
			// if site not create , can not excute this action!
			$_SESSION['error_msg'] = "{$CMS->lang['not_excute_act_rsync']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites");	
			return false;
		}
		if($site['is_suspend'] == 1 )
		{
			// if site is suspended hosting , can not excute this action!
			$_SESSION['error_msg'] = "{$CMS->lang['not_excute_site_is_suspended']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites");	
			return false;
		}

		// Update status
	  	$DB->query("UPDATE ".root_table."sites SET site_status = 12   WHERE site_id='{$id}'");
	  
	  	$CMS->class->logs->key= "site_{$id}";
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['receive_rsync_success']}");
		return true;

 	}

 	/**
	  * Function:Rsync from original->  to hosing user
	  * Param: info site
	  */
	static public function rsync($site = "")
	{
		global $CMS, $DB, $member;
		$id= $site['site_id'];
		$logs_created =array();
		$site  =self::get_info($id); 

		$server_info = \models\server::get_info($site['sv_id']);
		self::init_server($server_info);
		// Init logs 
		$logs_created = self::init_logs_site();
 		 
		// Execute ssh -> clone source and import DB
		$logs_change = self::exec_shell_rsync($site, $logs_created);
		if($logs_change['status'] == 1)
		{
			// Update status
				$DB->query("UPDATE ".root_table."sites SET site_status = 2 , site_created = 1 WHERE site_id='{$id}'");
				$CMS->class->logs->key= "site_{$id}";
				$CMS->class->logs->insert("{$CMS->lang['rsync_site_successfully']}");	

			// //Check health site
			// $health_site = self::health_site($site);
			// if($health_site['status'] == 1)
			// {
				
			// }
			// else
			// {
			// 	// Site don't work
			// 	// Update status
			// 	$DB->query("UPDATE ".root_table."sites SET site_status = 13 , site_created = 1 WHERE site_id='{$id}'");
			// 	$CMS->class->logs->key= "site_{$id}";
			// 	$CMS->class->logs->insert("[System] {$CMS->lang['rsync_site_dont_work']} => {$health_site['msg']}");
			// }

		}
		else
		{
			$DB->query("UPDATE ".root_table."sites SET site_status = 13, activity_log='{$db_return['msg']}'  WHERE site_id='{$id}'");
			$CMS->class->logs->key= "site_{$id}";
			$CMS->class->logs->insert("[System] {$CMS->lang['rsync_site_failure']} - {$db_return['msg']}");
		}
		return true; 		
 			
		 
	}


	/**
	  * Function: Action act_development site -> receive action 
	  * Param: info site
	  */
	static public function act_debug_mode($type = 0)
	{
		global $CMS, $DB, $member;
		$id= $CMS->input['id'];
		$site  =self::get_info($id);
		 
 		if(!is_array($site))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['site_dosenot_exites']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites");	
			return false;
		}
		if($site['site_created'] != 1 )
		{
			// if site not create , can not excute this action!
			$_SESSION['error_msg'] = "{$CMS->lang['not_excute_act_rsync']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites");	
			return false;
		}
		if($site['is_suspend'] == 1 )
		{
			// if site is suspended hosting , can not excute this action!
			$_SESSION['error_msg'] = "{$CMS->lang['not_excute_site_is_suspended']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites");	
			return false;
		}

		if($type == 1)
		{
			// Update status
	 	 	$DB->query("UPDATE ".root_table."sites SET site_status = 18  WHERE site_id='{$id}'");
	  
	  		$CMS->class->logs->key= "site_{$id}";
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['receive_act_production_success']}");
		}
		else
		{
			
	  		// Update status
	 	 	$DB->query("UPDATE ".root_table."sites SET site_status = 20  WHERE site_id='{$id}'");
	  		$CMS->class->logs->key= "site_{$id}";
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['receive_act_development_success']}");
		}
		return true;

 	}

 		/**
	  * Function: Action act_debig mode all site -> receive action 
	  * Param: info site
	  */
	static public function act_debug_mode_all($type = 0)
	{
		global $CMS, $DB, $member;
		if($type == 1)
		{
			// Update status
	 	 	$DB->query("UPDATE ".root_table."sites SET site_status = 18  WHERE site_deleted='0' AND site_created = 1 AND is_suspend = 0 ");
	  
	  		$CMS->class->logs->key= "site_{$id}";
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['receive_act_production_success']}");
		}
		else
		{
			
	  		// Update status
	 	 	$DB->query("UPDATE ".root_table."sites SET site_status = 20  WHERE site_deleted='0' AND site_created = 1 AND is_suspend = 0  ");
	  		$CMS->class->logs->key= "site_{$id}";
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['receive_act_development_success']}");
		}
		return true;

 	}

	 /**
	  * Function: Action Active site -> receive action 
	  * Param: info site
	  */
	static public function act_active()
	{
		global $CMS, $DB, $member;
		$id= $CMS->input['id'];
		$site  =self::get_info($id);
		$clone_original = intval($CMS->input['check_clone']);
		$clone_original_domain = trim($CMS->input['base_domain']);
		$clone_original_domain = preg_replace('#^https?://#', '', $clone_original_domain);
		
 		if(!is_array($site))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['site_dosenot_exites']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites");	
			return false;
		}

		// Check base domain
		if($clone_original == 1)
		{
			/* Check is domain */ 
			if($CMS->class->domain->is_domain($clone_original_domain) ==false)
			{ 
				$_SESSION['error_msg'] = "{$CMS->lang['domainname_valid_err_title']}";
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites&act=active&subact=clone_base_domain&id={$id}"); 
				return false;
			} 
			// Check domain exites in DB
			$sql_c = $DB->query("SELECT * FROM ".root_table."sites WHERE site_domainname= '{$clone_original_domain}' AND site_deleted = 0 AND site_created = 1 AND is_suspend = 0 ");
			if($DB->num_rows($sql_c) == 0)
			{
				$_SESSION['error_msg'] = "{$CMS->lang['base_domain_not_exites']}";
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites&act=active&subact=clone_base_domain&id={$id}"); 
				return false;
			}

		}
		// Update status
	  	$DB->query("UPDATE ".root_table."sites SET site_status = 1 , clone_original ='{$clone_original}', clone_original_domain='{$clone_original_domain}', site_created = 0 WHERE site_id='{$id}'");
	  
	  	$CMS->class->logs->key= "site_{$id}";
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['receive_active_success']}");
		return true;

 	}

	 /**
	  * Function: Active site -> create site -> create host -> import DB
	  * Param: info site
	  */
	static public function active($site = "")
	{
		global $CMS, $DB, $member;
		$id= $site['site_id'];
		$logs_created =array();
		$server_info = \models\server::get_info($site['sv_id']);
 

		self::init_server($server_info);
		
		// Init logs 
		$logs_created = self::init_logs_site();
 	
		$params['hosting_domain'] = $site['site_domainname'];
  		$params['hosting_username'] = $site['hosting_username'];
 		$params['hosting_password'] =  $site['hosting_password'];
  		$params['hosting_email'] = $site['email'];
 		$params['package_name'] ="plan_1";

 		if($site['site_domain_extra'] != "")
 		{
 			$params['extra_domain'] =  $site['site_domain_extra'];
 		}
 		$params['email_hosting'] =  $site['email_hosting'];
 		$email_hosting = explode("@", $site['email_hosting']);
 		$params['email_user'] =  $email_hosting[0];
  		$params['email_password'] = $site['email_password'];
  		// Check hosting exits???
  		$del_hosting = $CMS->api->cpanel->delete_hosting($params);
  	
		if($del_hosting['status'] == 0)//del hosting error
		{	
			$DB->query("UPDATE ".root_table."sites SET site_status = 3, activity_log='{$del_hosting['msg']}', site_created = 0 WHERE site_id='{$id}'");
	  		$_SESSION['error_msg'] = "{$CMS->lang['create_site_error']}";
	  		$logs_created[1]['msg'] = $return_hosting['msg'];
	  		self::save_logs_site($logs_created, $id);
	  		$CMS->class->logs->key= "site_{$id}";
			$CMS->class->logs->insert("[System] {$del_hosting['msg']}");

	  		//save_logs_site
	  		return false;
		}

  		// Add hosting
 		$return_hosting = $CMS->api->cpanel->add_hosting($params);
 
 		$params['db_name'] = $site['db_name'];
  		$params['db_username'] = $site['db_username'];
 		$params['db_password'] =  $site['db_password'];
 
		// Check create hosting
		if($return_hosting['status'] == 1)//Success
		{	
			$logs_created[1]['msg'] = $return_hosting['msg'];
			$logs_created[1]['status'] = 1;
	  		self::save_logs_site($logs_created, $id);

	  		if($site['site_domain_extra'] != "")
	 		{
	 			$ex_domain = $CMS->api->cpanel_v2->parkdomain($params);
	 		}

			//Create Email hosting
			$return_email = $CMS->api->cpanel_v2->create_email($params);
		 
			if($return_email['status'] == 1)//Success
			{
				$logs_created[2]['msg'] = $return_email['msg'];
				$logs_created[2]['status'] = 1;
	  			self::save_logs_site($logs_created, $id);

				// Create DB user
				$db_return = $CMS->api->cpanel_v2->createdbuser($params);
				if($db_return['status'] == 1)
				{
					//Execute ssh -> clone source and import DB
					if($site['clone_original'] == 1) // Clone form hosting base
					{
						$ssh_result = self::exec_shell_base($site, $logs_created);
					}
					else
					{
						$ssh_result = self::exec_shell($site, $logs_created);
					}
					 	 
					if($ssh_result['status'] == 1)
					{
						// Update status
			  			$DB->query("UPDATE ".root_table."sites SET site_status = 2, activity_log='{$return_hosting['msg']}', site_created = 1 WHERE site_id='{$id}'");

			  		 	self::send_email_site($site);
			  			$CMS->class->logs->key= "site_{$id}";
						$CMS->class->logs->insert("{$CMS->lang['create_site_successfully']}");

			  			return true;
					}
					else
					{
						$DB->query("UPDATE ".root_table."sites SET site_status = 3, activity_log='{$ssh_result['msg']}', site_created = 0 WHERE site_id='{$id}'");
			  			$CMS->class->logs->key= "site_{$id}";
						$CMS->class->logs->insert("[System] {$ssh_result['msg']}");
						//$_SESSION['msg'] = "{$CMS->lang['create_site_success']}";
			  			return false;
					}
				}
				else
				{
					$DB->query("UPDATE ".root_table."sites SET site_status = 3, activity_log='{$db_return['msg']}', site_created = 0 WHERE site_id='{$id}'");
				
					$logs_created[2]['msg'] = $db_return['msg'];
					$logs_created[2]['status'] = 0;
		  			self::save_logs_site($logs_created, $id);
		  			$CMS->class->logs->key= "site_{$id}";
					$CMS->class->logs->insert("[System] {$db_return['msg']}");
					//$_SESSION['msg'] = "{$CMS->lang['create_site_success']}";
		  			return false;
				}
				
			}
			else
			{	
				$DB->query("UPDATE ".root_table."sites SET site_status = 3, activity_log='{$return_email['msg']}', site_created = 0 WHERE site_id='{$id}'");
				
				$logs_created[2]['msg'] = $return_email['msg'];
				$logs_created[2]['status'] = 0;
	  			self::save_logs_site($logs_created, $id);
	  			$CMS->class->logs->key= "site_{$id}";
				$CMS->class->logs->insert("[System] {$return_email['msg']}");
				//$_SESSION['msg'] = "{$CMS->lang['create_site_success']}";
	  			return false;
			}
	  		//$_SESSION['msg'] = "{$CMS->lang['create_site_success']}";
	  		
			
		}
		else
		{
			// Update status
	  		$DB->query("UPDATE ".root_table."sites SET site_status = 3, activity_log='{$return_hosting['msg']}', site_created = 0 WHERE site_id='{$id}'");
	  		$_SESSION['error_msg'] = "{$CMS->lang['create_site_error']}";
	  		$logs_created[1]['msg'] = $return_hosting['msg'];
	  		self::save_logs_site($logs_created, $id);
	  		$CMS->class->logs->key= "site_{$id}";
			$CMS->class->logs->insert("[System] {$return_hosting['msg']}");

	  		//save_logs_site
	  		return false;
			
		}
	}



	 /**
	  * Function: Suspend site hosting 
	  * Param: info site
	  */
	static public function suspend()
	{
		global $CMS, $DB, $member;
 
		$id= $CMS->input['id'];
		$site  = self::get_info($id);
	 	
	 	$server_info = \models\server::get_info($site['sv_id']);
		self::init_server($server_info);

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
	  		$DB->query("UPDATE ".root_table."sites SET site_status = 5, is_suspend = 1  WHERE site_id='{$id}'");
	  		$_SESSION['msg'] = "{$CMS->lang['suspend_site_success']}";
	  		$CMS->class->logs->key= "site_{$id}";
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['suspend_site_success']}");

	  		return true;	
		}
		else
		{
			// Update status
	  		$DB->query("UPDATE ".root_table."sites SET site_status = 6   WHERE site_id='{$id}'");
	  		$_SESSION['error_msg'] = "{$CMS->lang['suspend_site_error']}";
	  		$CMS->class->logs->key= "site_{$id}";
			$CMS->class->logs->insert("{$CMS->lang['suspend_site_error']} <br /> {$return_hosting['msg']}");

	  		return false;
			
		}
	}


	 /**
	  * Function: UnSuspend site hosting 
	  * Param: info site
	  */
	static public function unsuspend()
	{
		global $CMS, $DB, $member;
	 
		 
		$id= $CMS->input['id'];
		$site  = self::get_info($id);
		$server_info = \models\server::get_info($site['sv_id']);
		self::init_server($server_info);
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
	  		$DB->query("UPDATE ".root_table."sites SET site_status = 2, is_suspend = 0  WHERE site_id='{$id}'");
	  		$_SESSION['msg'] = "{$CMS->lang['unsuspend_site_success']}";
	  		$CMS->class->logs->key= "site_{$id}";
			$CMS->class->logs->insert("{$CMS->lang['unsuspend_site_success']}");
	  		return true;
			
		}
		else
		{
			// Update status
	  		$DB->query("UPDATE ".root_table."sites SET site_status = 9   WHERE site_id='{$id}'");
	  		$_SESSION['error_msg'] = "{$CMS->lang['unsuspend_site_error']}";
	  		$CMS->class->logs->key= "site_{$id}";
			$CMS->class->logs->insert("{$CMS->lang['unsuspend_site_error']} <br /> {$return_hosting['msg']}");
	  		return false;
		}
	}

	/**
	  * Function: Action Change theme omain site -> receive action 
	  * Param: info site
	  */
	static public function act_change_theme()
	{
		global $CMS, $DB, $member;
		$id= $CMS->input['id'];
		$site  =self::get_info($id);

		$site_theme = trim($CMS->input['site_theme']);
 		if(!is_array($site))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['site_dosenot_exites']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites");	
			return false;
		}
		if($site['site_created'] != 1)
		{
			$_SESSION['error_msg'] = "{$CMS->lang['not_excute_act_rsync']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites&act=show&id={$id}"); return false;
		}
		 
        
		// Update status
	  	$DB->query("UPDATE ".root_table."sites SET site_status = 14, site_theme = '{$site_theme}',  site_created = 1  WHERE site_id='{$id}'");
	  
	  	$CMS->class->logs->key= "site_{$id}";
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['receive_act_change_site_theme_success']}: {$site['site_theme']} -> {$site_theme} ");
		return true;

 	}


	 /**
	  * Function: Change theme in site hosting 
	  * Param: info site
	  */
	public function change_theme($site = "")
	{
		global $CMS, $DB, $member;
		$id = $site['site_id'];
		if(!is_array($site))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['site_dosenot_exites']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites");	
			return false;
		}
		$server_info = \models\server::get_info($site['sv_id']);
		self::init_server($server_info);
 	 
	
		//Change theme in file config.route.php of hosting
		$ssh_result = self::exec_shell_changetheme($site);
		if($ssh_result['status'] == 0)
		{
			$ssh_result_msg = $CMS->class->editor->input($ssh_result['msg'],"text");
			// Update status
			 
	  		$DB->query("UPDATE ".root_table."sites SET site_status = 15 , site_created= 1  WHERE site_id='{$id}'");
	  		$_SESSION['error_msg'] = "{$CMS->lang['change_theme_site_error']}";
	  		$CMS->class->logs->key= "site_{$id}";
			$CMS->class->logs->insert("[System] {$CMS->lang['change_theme_site_error']} <br /> {$ssh_result_msg}");
			return false;
		}
		else
		{
			 
			// Update status
	  		$DB->query("UPDATE ".root_table."sites SET site_status = 2, site_created= 1   WHERE site_id='{$id}'");
	  		$_SESSION['msg'] = "{$CMS->lang['change_theme_site_success']}";
	  		$CMS->class->logs->key= "site_{$id}";
			$CMS->class->logs->insert("[System] {$CMS->lang['change_theme_site_success']}");
			return true;
		}
 			
		 
	}

	/**
	  * Function: Action Change domain site -> receive action 
	  * Param: info site
	  */
	static public function act_change_domain()
	{
		global $CMS, $DB, $member;
		$id= $CMS->input['id'];
		$site  =self::get_info($id);
		$new_site_domainname = trim($CMS->input['new_site_domainname']);
 		if(!is_array($site))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['site_dosenot_exites']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites");	
			return false;
		}
		if($new_site_domainname == "")
		{
			$_SESSION['error_msg'] = "{$CMS->lang['new_site_domainname_required']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites&act=change_domain&id={$id}"); return false;
		}
		/* Check is domain */ 
		if($CMS->class->domain->is_domain($new_site_domainname) ==false)
		{ 
			$_SESSION['error_msg'] = "{$CMS->lang['domainname_valid_err_title']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites&act=change_domain&id={$id}"); 
			return false;
		} 
		//Check new_domain equal old_domain
		// if($new_site_domainname == $site['site_domainname'])
		// {
		// 	$_SESSION['error_msg'] = "{$CMS->lang['domainname_equalvalid_err_title']}";
		// 	$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites&act=change_domain&id={$id}"); 
		// 	return false;
		// }

		// Check domain exits in list site
		// $sql_d = $DB->query("SELECT * FROM ".root_table."sites WHERE site_domainname = '{$new_site_domainname}' AND site_deleted = 0 ");
		// if($DB->num_rows($sql_d) > 0)
		// {
		// 	$_SESSION['error_msg'] = "{$CMS->lang['domainname_exits_inlist_site']}";
		// 	$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites&act=change_domain&id={$id}"); 
		// 	return false;
		// }

		if($site['site_domain_extra'] != "")
		{
			$site['site_domainname'] = $site['site_domain_extra'];
		}
		// Replace email hosting
		$new_email_hosting = str_replace( $site['site_domainname'] , $new_site_domainname , $site['email_hosting']);
 		$change_domain_time = time();
		// Update status
	  	$DB->query("UPDATE ".root_table."sites SET site_status = 10, old_site_domainname ='{$site['site_domainname']}', site_domainname = '{$new_site_domainname}', email_hosting = '{$new_email_hosting}', site_domain_extra='', change_domain_time='{$change_domain_time}'  WHERE site_id='{$id}'");
	  
	  	$CMS->class->logs->key= "site_{$id}";
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['receive_act_change_domain_succes']}: {$site['site_domainname']} -> {$new_site_domainname} ");
		return true;

 	}


	 /**
	  * Function: Change domain site hosting 
	  * Param: info site
	  */
	public function change_domain($site = "")
	{
		global $CMS, $DB, $member;
		$id = $site['site_id'];
		if(!is_array($site))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['site_dosenot_exites']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites");	
			return false;
		}
 		$server_info = \models\server::get_info($site['sv_id']);
		self::init_server($server_info);
		$params['hosting_username'] = $site['hosting_username'];
  		$params['changelist']['hosting_domain']  = true;
  		$params['hosting_domain'] = $site['site_domainname'];
 		$return_hosting = $CMS->api->cpanel->edit_hosting($params);
 		$return_hosting['msg'] = $CMS->class->editor->input($return_hosting['msg'],"text");
		// Check create hosting
		if($return_hosting['status'] == 1)//Success
		{
			//Create Email hosting
			$params['email_hosting'] =  $site['email_hosting'];
 			$email_hosting = explode("@", $site['email_hosting']);
 			$params['email_user'] =  $email_hosting[0];
  			$params['email_password'] = $site['email_password'];
			$return_email = $CMS->api->cpanel_v2->create_email($params);
			//Change domain in file config.route.php of hosting
			$ssh_result = self::exec_shell_changedomain($site);
			if($ssh_result['status'] == 0)
			{
				$ssh_result_msg = $CMS->class->editor->input($ssh_result['msg'],"text");
				// Update status
				//$ssh_result_msg = $ssh_result['msg'];
				// Set status change domain fail
		  		$DB->query("UPDATE ".root_table."sites SET site_status = 11, site_created =1  WHERE site_id='{$id}'");
		  		$_SESSION['error_msg'] = "{$CMS->lang['change_domain_site_error']}";
		  		$CMS->class->logs->key= "site_{$id}";
				$CMS->class->logs->insert("[System] {$CMS->lang['change_domain_site_error']} <br /> {$ssh_result_msg}");
				return false;
			}
			else
			{
				//Send mail
				self::send_email_site($site);
				// Update status
				// Set status change domain success
		  		$DB->query("UPDATE ".root_table."sites SET site_status = 2, site_created =1  WHERE site_id='{$id}'");
		  		$_SESSION['msg'] = "{$CMS->lang['change_domain_site_success']}";
		  		$CMS->class->logs->key= "site_{$id}";
				$CMS->class->logs->insert("[System] {$CMS->lang['change_domain_site_success']}");
				return true;
			}
 			
		}
		else
		{
			// Update status
	  		$DB->query("UPDATE ".root_table."sites SET site_status = 11  WHERE site_id='{$id}'");
	  		$_SESSION['error_msg'] = "{$CMS->lang['change_domain_site_error']}";
	  		$CMS->class->logs->key= "site_{$id}";
			$CMS->class->logs->insert("[System] {$CMS->lang['change_domain_site_error']} <br /> {$return_hosting['msg']}");

	  		return false;
			
		}
	}


	/**
	  * Function: Action Change password acp site -> receive action 
	  * Param: info site
	  */
	static public function act_change_password()
	{
		global $CMS, $DB, $member;
		$id= $CMS->input['id'];
		$site  =self::get_info($id);
		 
		$acp_password = trim($CMS->class->editor->input('acp_password'));
 		
 		if(!is_array($site))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['site_dosenot_exites']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites");	
			return false;
		}
		if($acp_password == "")
		{
			$_SESSION['error_msg'] = "{$CMS->lang['new_site_password_required']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites&act=change_password&id={$id}"); return false;
		}	 
		//Check new_domain equal old_domain
		if(strlen($acp_password) < 6)
		{
			$_SESSION['error_msg'] = "{$CMS->lang['new_site_password_required_minlength']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites&act=change_password&id={$id}"); 
			return false;
		}
		 
	 	$user_password = md5($acp_password);
		// Hash + salt
		$salt = $CMS->user->create_pwd_salt(5);
		$salt = addslashes($salt);
		$pwdhash = $CMS->user->create_pwd_hash( $salt, $user_password );
		// Update status
	  	$DB->query("UPDATE ".root_table."sites SET site_status = 16, pwdoriginal ='{$acp_password}', pwdhash = '{$pwdhash}', pwdsalt='{$salt}'  WHERE site_id='{$id}'");
	  
	  	$CMS->class->logs->key= "site_{$id}";
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['receive_act_change_password_success']}");
		return true;

 	}


	 /**
	  * Function: Change password acp site
	  * Param: info site
	  */
	public function change_password($site = "")
	{
		global $CMS, $DB, $member;
		$id = $site['site_id'];
		if(!is_array($site))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['site_dosenot_exites']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites");	
			return false;
		}
 		$server_info = \models\server::get_info($site['sv_id']);
		self::init_server($server_info);
	 
		//Change password acp 
		$ssh_result = self::exec_shell_changepassword($site);
		if($ssh_result['status'] == 0)
		{
			$ssh_result_msg = $CMS->class->editor->input($ssh_result['msg'],"text");
			// Update status
			//$ssh_result_msg = $ssh_result['msg'];
			// Set status change domain fail
	  		$DB->query("UPDATE ".root_table."sites SET site_status = 17, site_created =1  WHERE site_id='{$id}'");
	  		$_SESSION['error_msg'] = "{$CMS->lang['change_password_site_error']}";
	  		$CMS->class->logs->key= "site_{$id}";
			$CMS->class->logs->insert("[System] {$CMS->lang['change_password_site_error']} <br /> {$ssh_result_msg}");
			return false;
		}
		else
		{
			//Send mail
			self::send_email_site($site);
			// Update status
			// Set status change domain success
	  		$DB->query("UPDATE ".root_table."sites SET site_status = 2, site_created =1 WHERE site_id='{$id}'");
	  		$_SESSION['msg'] = "{$CMS->lang['change_password_site_success']}";
	  		$CMS->class->logs->key= "site_{$id}";
			$CMS->class->logs->insert("[System] {$CMS->lang['change_password_site_success']}");
			return true;
		}
 			
		 
	}


	/**
	  * Function: Action move server to server real 
	  * Param: info site
	  */
	static public function act_move()
	{
		global $CMS, $DB, $member;
		$id= $CMS->input['id'];
		$site  =self::get_info($id);

	 
 		if(!is_array($site))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['site_dosenot_exites']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites");	
			return false;
		}
		if($site['site_created'] != 1)
		{
			$_SESSION['error_msg'] = "{$CMS->lang['not_excute_act_rsync']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites&act=show&id={$id}"); return false;
		}
		 
		// Get server
		$sql = $DB->query("SELECT * FROM ".root_table."server WHERE sv_deleted = 0 AND sv_type = 1 AND sv_active = 1   ORDER BY sv_id DESC LIMIT 1 "); 

		if($DB->num_rows($sql) > 0)
		{
			$sv = $DB->fetch_array($sql);
		}
        
		// Update status
	  	$DB->query("UPDATE ".root_table."sites SET site_status = 22 ,  site_created = 1,  sv_id_real = '{$sv['sv_id']}'  WHERE site_id='{$id}'");
	  
	  	$CMS->class->logs->key= "site_{$id}";
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['receive_act_move_success']}  ");
		return true;

 	}


 	 /**
	  * Function: move server
	  * Param: info site
	  */
	static public function move($site = "")
	{
		global $CMS, $DB, $member;
		$id= $site['site_id'];
		$logs_created =array();

		$server_real_info = \models\server::get_info($site['sv_id_real']);
		self::init_server($server_real_info, 1);

		// Init logs 
		$logs_created = self::init_logs_site();
 		
		$params['hosting_domain'] = $site['site_domainname'];
  		$params['hosting_username'] = $site['hosting_username'];
 		$params['hosting_password'] =  $site['hosting_password'];
  		$params['hosting_email'] = $site['email'];
 		$params['package_name'] ="plan_1";

 		$params['email_hosting'] =  $site['email_hosting'];
 		$email_hosting = explode("@", $site['email_hosting']);
 		$params['email_user'] =  $email_hosting[0];
  		$params['email_password'] = $site['email_password'];
  		// Check hosting exits???
  		$del_hosting = $CMS->api->cpanel->delete_hosting($params);
  	
		if($del_hosting['status'] == 0)//del hosting error
		{	
			$DB->query("UPDATE ".root_table."sites SET site_status = 23, activity_log='{$del_hosting['msg']}', site_created = 1 WHERE site_id='{$id}'");
	  		$_SESSION['error_msg'] = "{$CMS->lang['move_site_error']}";
	  		$logs_created[1]['msg'] = $return_hosting['msg'];
	  		self::save_logs_site($logs_created, $id);
	  		$CMS->class->logs->key= "site_{$id}";
			$CMS->class->logs->insert("[System] {$del_hosting['msg']}");

	  		//save_logs_site
	  		return false;
		}

  		// Add hosting
 		$return_hosting = $CMS->api->cpanel->add_hosting($params);
 
 		$params['db_name'] = $site['db_name'];
  		$params['db_username'] = $site['db_username'];
 		$params['db_password'] =  $site['db_password'];
 
		// Check create hosting
		if($return_hosting['status'] == 1)//Success
		{	
			$logs_created[1]['msg'] = $return_hosting['msg'];
			$logs_created[1]['status'] = 1;
	  		self::save_logs_site($logs_created, $id);

			//Create Email hosting
			$return_email = $CMS->api->cpanel_v2->create_email($params);
		 
			if($return_email['status'] == 1)//Success
			{
				$logs_created[2]['msg'] = $return_email['msg'];
				$logs_created[2]['status'] = 1;
	  			self::save_logs_site($logs_created, $id);

				// Create DB user
				$db_return = $CMS->api->cpanel_v2->createdbuser($params);
				if($db_return['status'] == 1)
				{
					// Suspend old hosting
					
					$server_demo_info = \models\server::get_info($site['sv_id']);
					self::init_server($server_demo_info);
					$return_sus_hosting = $CMS->api->cpanel->suspend_hosting($params);

					// Check create hosting
					if($return_sus_hosting['status'] == 0)//Success
					{
						$DB->query("UPDATE ".root_table."sites SET site_status = 23, activity_log='{$return_sus_hosting['msg']}', site_created = 1 WHERE site_id='{$id}'");
						$logs_created[2]['msg'] = $return_sus_hosting['msg'];
						$logs_created[2]['status'] = 0;
			  			self::save_logs_site($logs_created, $id);
			  			$CMS->class->logs->key= "site_{$id}";
						$CMS->class->logs->insert("[System] {$return_sus_hosting['msg']}");
						//$_SESSION['msg'] = "{$CMS->lang['create_site_success']}";
			  			return false;
					}

					// Execute ssh -> clone source and import DB
					
					 
					self::init_server($server_real_info, 1);
					$ssh_result = self::exec_shell_move($site, $logs_created);
				
					if($ssh_result['status'] == 1)
					{


						//Check health site
						//$health_site_result = self::health_site($site);
					 	$time = time();
						//if($health_site_result['status'] == 1)
						//{
							// Check pointserver
							// $ip = gethostbyname($site['site_domainname']);
							// if($ip == $server_real_info['sv_ip'] )
							// {  echo "11Aa"; 
							// 	self::init_server($server_demo_info);echo "11444444Aa";exit;
							// 	$del_hosting = $CMS->api->cpanel->delete_hosting($params);
							// }
							// echo "Aa";exit;
							// Update status
				  			$DB->query("UPDATE ".root_table."sites SET site_status = 2, activity_log='{$return_hosting['msg']}', site_created = 1, site_moved = 1, sv_id = '{$site['sv_id_real']}', site_time_moved = '{$time}'  WHERE site_id='{$id}'");

				  		 	self::send_email_site($site);
				  			$CMS->class->logs->key= "site_{$id}";
							$CMS->class->logs->insert("{$CMS->lang['move_site_successfully']}");

				  			return true;
						// }
						// else
						// {
						// 	$DB->query("UPDATE ".root_table."sites SET site_status = 23, activity_log='{$health_site_result['msg']}', site_created = 1 WHERE site_id='{$id}'");
				  // 			$CMS->class->logs->key= "site_{$id}";
						// 	$CMS->class->logs->insert("[System] {$CMS->lang['move_site_error']} Check health_site error {$health_site_result['msg']}");
						// 	//$_SESSION['msg'] = "{$CMS->lang['create_site_success']}";
				  // 			return false;
						// }
						
					}
					else
					{
						$DB->query("UPDATE ".root_table."sites SET site_status = 23, activity_log='{$ssh_result['msg']}', site_created = 1 WHERE site_id='{$id}'");
			  			$CMS->class->logs->key= "site_{$id}";
						$CMS->class->logs->insert("[System] {$CMS->lang['move_site_error']} {$ssh_result['msg']}");
						//$_SESSION['msg'] = "{$CMS->lang['create_site_success']}";
			  			return false;
					}
				}
				else
				{
					$DB->query("UPDATE ".root_table."sites SET site_status = 23, activity_log='{$db_return['msg']}', site_created = 1 WHERE site_id='{$id}'");
					$logs_created[2]['msg'] = $db_return['msg'];
					$logs_created[2]['status'] = 0;
		  			self::save_logs_site($logs_created, $id);
		  			$CMS->class->logs->key= "site_{$id}";
					$CMS->class->logs->insert("[System] {$CMS->lang['move_site_error']} {$db_return['msg']}");
					//$_SESSION['msg'] = "{$CMS->lang['create_site_success']}";
		  			return false;
				}
				
			}
			else
			{	
				$DB->query("UPDATE ".root_table."sites SET site_status = 23, activity_log='{$return_email['msg']}', site_created = 1 WHERE site_id='{$id}'");
				
				$logs_created[2]['msg'] = $return_email['msg'];
				$logs_created[2]['status'] = 0;
	  			self::save_logs_site($logs_created, $id);
	  			$CMS->class->logs->key= "site_{$id}";
				$CMS->class->logs->insert("[System] {$CMS->lang['move_site_error']} {$return_email['msg']}");
				//$_SESSION['msg'] = "{$CMS->lang['create_site_success']}";
	  			return false;
			}
	  		//$_SESSION['msg'] = "{$CMS->lang['create_site_success']}";
			
		}
		else
		{
			// Update status
	  		$DB->query("UPDATE ".root_table."sites SET site_status = 23, activity_log='{$return_hosting['msg']}', site_created = 1 WHERE site_id='{$id}'");
	  		$_SESSION['error_msg'] = "{$CMS->lang['move_site_error']}";
	  		$logs_created[1]['msg'] = $return_hosting['msg'];
	  		self::save_logs_site($logs_created, $id);
	  		$CMS->class->logs->key= "site_{$id}";
			$CMS->class->logs->insert("[System] {$CMS->lang['move_site_error']} {$return_hosting['msg']}");

	  		//save_logs_site
	  		return false;
			
		}
	}



	 /**
	  * Function: Change mode development in site  
	  * Param: info site
	  */
	public function mode_development($site = "")
	{
		global $CMS, $DB, $member;
		$id = $site['site_id'];
		if(!is_array($site))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['site_dosenot_exites']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites");	
			return false;
		}
		$server_info = \models\server::get_info($site['sv_id']);
		self::init_server($server_info);
 	 
	
		//Change theme in file config.route.php of hosting
		$ssh_result = self::exec_shell_change_modedebug($site,"development");
		if($ssh_result['status'] == 0)
		{
			$ssh_result_msg = $CMS->class->editor->input($ssh_result['msg'],"text");
			// Update status
	 
	  		$DB->query("UPDATE ".root_table."sites SET site_status = 19 , site_created= 1  WHERE site_id='{$id}'");
	  		$_SESSION['error_msg'] = "{$CMS->lang['change_modedebug_dev_site_error']}";
	  		$CMS->class->logs->key= "site_{$id}";
			$CMS->class->logs->insert("[System] {$CMS->lang['change_modedebug_dev_site_error']} <br /> {$ssh_result_msg}");
			return false;
		}
		else
		{
			 
			// Update status
	  		$DB->query("UPDATE ".root_table."sites SET site_status = 2, site_created= 1, environment = 0   WHERE site_id='{$id}'");
	  		$_SESSION['msg'] = "{$CMS->lang['change_modedebug_dev_site_success']}";
	  		$CMS->class->logs->key= "site_{$id}";
			$CMS->class->logs->insert("[System] {$CMS->lang['change_modedebug_dev_site_success']}");
			return true;
		}
 			
		 
	}

	/**
	  * Function: Change mode production in site  
	  * Param: info site
	  */
	public function mode_production($site = "")
	{
		global $CMS, $DB, $member;
		$id = $site['site_id'];
		if(!is_array($site))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['site_dosenot_exites']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sites");	
			return false;
		}
		$server_info = \models\server::get_info($site['sv_id']);
		self::init_server($server_info);
 	 
	
		//Change theme in file config.route.php of hosting
		$ssh_result = self::exec_shell_change_modedebug($site,"production");
		if($ssh_result['status'] == 0)
		{
			$ssh_result_msg = $CMS->class->editor->input($ssh_result['msg'],"text");
			// Update status
	 
	  		$DB->query("UPDATE ".root_table."sites SET site_status = 21 , site_created= 1  WHERE site_id='{$id}'");
	  		$_SESSION['error_msg'] = "{$CMS->lang['change_modedebug_pro_site_error']}";
	  		$CMS->class->logs->key= "site_{$id}";
			$CMS->class->logs->insert("[System] {$CMS->lang['change_modedebug_pro_site_error']} <br /> {$ssh_result_msg}");
			return false;
		}
		else
		{
			 
			// Update status
	  		$DB->query("UPDATE ".root_table."sites SET site_status = 2, site_created= 1,environment = 1   WHERE site_id='{$id}'");
	  		$_SESSION['msg'] = "{$CMS->lang['change_modedebug_pro_site_success']}";
	  		$CMS->class->logs->key= "site_{$id}";
			$CMS->class->logs->insert("[System] {$CMS->lang['change_modedebug_pro_site_success']}");
			return true;
		}
 			
		 
	}

	/**
	 * Function :init logs create site
	 */
	static function init_logs_site()
	{
		$logs_create = array();
		$logs_create[1] = array("key" => "create_hosting","step_name" => "Create hosting","status" => "0", "msg" => "msg");
		$logs_create[2] = array("key" => "create_emailhosting","step_name" => "Create email hosting","status" => "0", "msg" => "msg");
		$logs_create[3] = array("key" => "sync_souce","step_name" => "Sync souce","status" => "0", "msg" => "msg");
		$logs_create[4] = array("key" => "import_database","step_name" => "Import database site","status" => "0", "msg" => "msg");
		$logs_create[5] = array("key" => "config_default_site","step_name" => "Config default site","status" => "0", "msg" => "msg");
		return $logs_create;

	}

	/**
	 * Function : Save logs create
	 */
	static function save_logs_site($logs = "", $site_id = "")
	{
		global $CMS, $DB, $member;
		$logs = json_encode($logs);
		$DB->query("UPDATE ".root_table."sites SET logs_created = '{$logs}'  WHERE site_id='{$site_id}'");
		return true;
	}

	/**
	 * [send_email_site infomation description]
	 * @param  string $site [description]
	 * @return [type]        [description]
	 */
	static function send_email_site($site = "")
	{
		global $CMS, $DB, $member;

			$CMS->email->email_template = "site_info";
			$CMS->email->email_to = $site['email'];
			$CMS->email->email_toname = $site['firstname'];

			//$CMS->email->email_cc = $user['user_email'];
 			// email_from
 			$CMS->email->data['firstname'] =  $site['firstname'];
			$CMS->email->data['lastname'] =  $site['lastname'];
			$CMS->email->data['site_domainname'] =  $site['site_domainname'];
			$CMS->email->data['username'] = $site['username'];
			$CMS->email->data['pwdoriginal'] = $site['pwdoriginal'];
			$CMS->email->data['email_url'] = "https://".$site['site_domainname'].":2096";
			$CMS->email->data['email_hosting'] = $site['email_hosting'];
			$CMS->email->data['email_password'] = $site['email_password'];
 
		 
			$CMS->email->quick_send(0,0);
			return;
		 
	}


	static function exec_shell($site = "",$logs_created= "")
	{
		global $CMS;
		if($site['site_domain_extra'] != "")
		{
			$info = "db_name={$site['db_name']}|||db_user={$site['db_username']}|||db_pass={$site['db_password']}|||url=http://{$site['site_domain_extra']}|||domain={$site['site_domain_extra']}|||theme={$site['site_theme']}|||acp_username={$site['username']}|||acp_pwdhash={$site['pwdhash']}|||acp_pwdhash={$site['pwdsalt']}|||hosting_username={$site['hosting_username']}|||default_language={$site['default_language']}|||storename={$site['storename']}|||email_user={$site['email_hosting']}|||email_password={$site['email_password']}|||";
		}
		else
		{
			$info = "db_name={$site['db_name']}|||db_user={$site['db_username']}|||db_pass={$site['db_password']}|||url=http://{$site['site_domainname']}|||domain={$site['site_domainname']}|||theme={$site['site_theme']}|||acp_username={$site['username']}|||acp_pwdhash={$site['pwdhash']}|||acp_pwdhash={$site['pwdsalt']}|||hosting_username={$site['hosting_username']}|||default_language={$site['default_language']}|||storename={$site['storename']}|||email_user={$site['email_hosting']}|||email_password={$site['email_password']}|||";
		}
		//Connect SSH
 		$connection = \ssh2_connect("{$CMS->vars['ssh_server']}",$CMS->vars['ssh_port'], array('hostkey'=>'ssh-rsa'));
		if (!$connection) 
		{
			$logs_created[3]['msg'] = $ssh_result['msg'] =  "Connection failed !"; 
			$logs_created[3]['status'] = $ssh_result['status'] =  0;	
		  	self::save_logs_site($logs_created, $site['site_id']);
		  	return $ssh_result;
		} 
 
		if(\ssh2_auth_pubkey_file($connection, "{$CMS->vars['ssh_user']}", "/home/.ssh/".$CMS->vars['rsa_key'].".pub", "/home/.ssh/".$CMS->vars['rsa_key'],'')) 
		{
			 echo "Public Key Authentication Successful\n";
		} 
		else 
		{
			$logs_created[3]['msg'] = $ssh_result['msg'] =  "SSH Authorization failed !"; 
			$logs_created[3]['status'] = $ssh_result['status'] =  0;	
		  	self::save_logs_site($logs_created, $site['site_id']);
		  	return $ssh_result;
		}
  

		$stream= ssh2_exec($connection, "rsync -ave '' --include '/themes/".$site['site_theme']."/' --exclude '/themes/*'  --quiet --delete --numeric-ids   /home/_original/ /home/".$site['hosting_username']."/public_html/");

		
		stream_set_blocking($stream, true);
		$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
		$stream_out_content =  stream_get_contents($stream_out);
		$logs_created[3]['msg'] = $ssh_result['msg'] = $stream_out_content;
		
		if($stream_out_content == "")
		{
			$logs_created[3]['status'] = 1; 
			self::save_logs_site($logs_created, $site['site_id']);	
 
	  	}
	  	else
	  	{
			$logs_created[3]['status'] = $ssh_result['status'] = 0;	
			self::save_logs_site($logs_created, $site['site_id']);
			return $ssh_result;

	  	}
	   
 
		\ssh2_exec($connection, 'chown -R '.$site['hosting_username'].':'.$site['hosting_username'].' /home/'.$site['hosting_username'].'/public_html/');
		//sleep for 1 seconds
		\ssh2_exec($connection, 'chown -R '.$site['hosting_username'].':'.$site['hosting_username'].' /home/'.$site['hosting_username'].'/public_html/');
		//Push info host to db_info.txt
		\ssh2_exec($connection, "echo '".$info."' > /home/".$site['hosting_username']."/public_html/db/db_info.txt");
		
		// Run php script
		\ssh2_exec($connection, "php -f /home/".$site['hosting_username']."/public_html/db/db_script.php");
	 
		$stream_db = \ssh2_exec($connection, "mysql -u root ".$site['db_name']." < /home/_original/db/eco_base.sql"); 
		stream_set_blocking($stream_db, true);
		$stream_db_out = ssh2_fetch_stream($stream_db, SSH2_STREAM_STDIO);
		$stream_db_out_content =  stream_get_contents($stream_db_out);
		$logs_created[4]['msg'] = $ssh_result['msg'] = $stream_db_out_content;
		if($stream_db_out_content == "")
		{	
			$logs_created[4]['status'] = 1;
			self::save_logs_site($logs_created, $site['site_id']);
		}
		else
		{
			$logs_created[4]['status'] = $ssh_result['status'] = 0;
			self::save_logs_site($logs_created, $site['site_id']);
			return $ssh_result;
		}
		
		$stream_usr = \ssh2_exec($connection, "php -f /home/".$site['hosting_username']."/public_html/db/createusr_script.php");
		stream_set_blocking($stream_usr, true);
		$stream_usr_out = ssh2_fetch_stream($stream_usr, SSH2_STREAM_STDIO);
		$stream_usr_out_content =  stream_get_contents($stream_usr_out);
		$logs_created[5]['msg'] = $ssh_result['msg'] = $stream_usr_out_content;
		if($stream_usr_out_content == "create_user_admin_done")
		{	
			$logs_created[5]['status'] = $ssh_result['status'] = 1;
			self::save_logs_site($logs_created, $site['site_id']);
		}
		else
		{
			$logs_created[5]['status'] = $ssh_result['status'] = 0;
			self::save_logs_site($logs_created, $site['site_id']);
			return $ssh_result;
			
		}
		
 
		return $ssh_result;
		 
	}

	 

	/**
	 * [exec_shell description] => Sync from server web demo
	 * @param  string $site         [description]
	 * @param  string $logs_created [description]
	 * @return [type]               [description]
	 */
	static function exec_shell_base($site = "",$logs_created= "")
	{
		global $CMS;

		if($site['clone_original'] == 1 AND $site['clone_original_domain'] != "")
		{
 
			$info_hosting_original = self::get_info($site['clone_original_domain']);
			$base_hosting = $info_hosting_original['hosting_username'];
			$base_db = $info_hosting_original['db_name'];
			$base_theme = $info_hosting_original['site_theme'];
		}
		
		if($site['site_domain_extra'] != "")
		{
			$info = "db_name={$site['db_name']}|||db_user={$site['db_username']}|||db_pass={$site['db_password']}|||url=http://{$site['site_domain_extra']}|||domain={$site['site_domain_extra']}|||theme={$site['site_theme']}|||acp_username={$site['username']}|||acp_pwdhash={$site['pwdhash']}|||acp_pwdhash={$site['pwdsalt']}|||hosting_username={$site['hosting_username']}|||default_language={$site['default_language']}|||storename={$site['storename']}|||email_user={$site['email_hosting']}|||email_password={$site['email_password']}|||";
		}
		else
		{
			$info = "db_name={$site['db_name']}|||db_user={$site['db_username']}|||db_pass={$site['db_password']}|||url=http://{$site['site_domainname']}|||domain={$site['site_domainname']}|||theme={$base_theme}|||acp_username={$site['username']}|||acp_pwdhash={$site['pwdhash']}|||acp_pwdhash={$site['pwdsalt']}|||hosting_username={$site['hosting_username']}|||default_language={$site['default_language']}|||storename={$site['storename']}|||email_user={$site['email_hosting']}|||email_password={$site['email_password']}|||";
		}
		
 		// Connect server web-demo
 		# Export DB: 
 		# Clone source to Server Us
 			 
		 
	 	$connection = \ssh2_connect("{$CMS->vars['ssh_server']}",$CMS->vars['ssh_port'], array('hostkey'=>'ssh-rsa'));
		if (!$connection) 
		{
			$logs_created[3]['msg'] = $ssh_result['msg'] =  "Connection failed !"; 
			$logs_created[3]['status'] = $ssh_result['status'] =  0;	
		  	self::save_logs_site($logs_created, $site['site_id']);
		  	return $ssh_result;
		} 


		if(\ssh2_auth_pubkey_file($connection, "{$CMS->vars['ssh_user']}", "/home/.ssh/".$CMS->vars['rsa_key'].".pub", "/home/.ssh/".$CMS->vars['rsa_key'],''))
		{
			 echo "Public Key Authentication Successful\n";
		} 
		else 
		{
			$logs_created[3]['msg'] = $ssh_result['msg'] =  "SSH Authorization failed !"; 
			$logs_created[3]['status'] = $ssh_result['status'] =  0;	
		  	self::save_logs_site($logs_created, $site['site_id']);
		  	return $ssh_result;
		}

		ssh2_exec($connection, "rsync -ave '' --include '/themes/".$base_theme."/' --exclude '/themes/*'  --delete --numeric-ids   /home/_original/ /home/".$site['hosting_username']."/public_html/");

		sleep(1); 
		// Create folder upload by name hosting need create
		ssh2_exec($connection, 'mkdir -p  /home/'.$site['hosting_username'].'/public_html/uploads/'.$site['hosting_username'].'/');
		// Copy folder uploafs from hosting base to new hosting
	    ssh2_exec($connection, 'rsync -r /home/'.$base_hosting.'/public_html/uploads/'.$base_hosting.'/* /home/'.$site['hosting_username'].'/public_html/uploads/'.$site['hosting_username'].'/');

	
        $stream=  \ssh2_exec($connection, 'mysqldump --defaults-extra-file=/root/mysql/.sqlpwd '.$base_db.' > /home/'.$site['hosting_username'].'/public_html/db/eco_base.sql');
		stream_set_blocking($stream, true);
		//Start copy source orginal
		$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
		$stream_out_content =  stream_get_contents($stream_out);
		$logs_created[3]['msg'] = $ssh_result['msg'] = $stream_out_content;
		if($stream_out_content == "")
		{
			$logs_created[3]['status'] = $ssh_result['status'] = 1; 
			self::save_logs_site($logs_created, $site['site_id']);	
	  	}
	  	else
	  	{
			$logs_created[3]['status'] = $ssh_result['status'] = 0;	
			self::save_logs_site($logs_created, $site['site_id']);
			return $ssh_result;
	  	}
	  	
	  	sleep(1); 
		ssh2_exec($connection, 'chown -R '.$site['hosting_username'].':'.$site['hosting_username'].' /home/'.$site['hosting_username'].'/public_html/');
 		sleep(2); 
		//Push info host to db_info.txt
		ssh2_exec($connection, "echo '".$info."' > /home/".$site['hosting_username']."/public_html/db/db_info.txt");

		// Run php script
		ssh2_exec($connection, "php -f /home/".$site['hosting_username']."/public_html/db/db_script_base.php");
		$stream_db = \ssh2_exec($connection, "mysql -u root ".$site['db_name']." < /home/".$site['hosting_username']."/public_html/db/eco_base.sql"); 
		stream_set_blocking($stream_db, true);
		$stream_db_out = ssh2_fetch_stream($stream_db, SSH2_STREAM_STDIO);
		$stream_db_out_content =  stream_get_contents($stream_db_out);
		$logs_created[4]['msg'] =  $ssh_result['msg'] = $stream_db_out_content;
		if($stream_db_out_content == "")
		{	
			$logs_created[4]['status'] =  $ssh_result['status'] = 1;
			self::save_logs_site($logs_created, $site['site_id']);
		}
		else
		{
			$logs_created[4]['status'] =  $ssh_result['status'] = 0;
			self::save_logs_site($logs_created, $site['site_id']);
			return $ssh_result;
		}
		

		sleep(2);
		$stream_usr = \ssh2_exec($connection, "php -f /home/".$site['hosting_username']."/public_html/db/createusr_script_base.php");
		stream_set_blocking($stream_usr, true);
		$stream_usr_out = ssh2_fetch_stream($stream_usr, SSH2_STREAM_STDIO);
		$stream_usr_out_content =  stream_get_contents($stream_usr_out);
		 
		//
	 	sleep(1);

		$logs_created[5]['msg'] =  $ssh_result['msg'] = $stream_usr_out_content;
		if($stream_usr_out_content == "create_user_admin_done")
		{	
			$logs_created[5]['status'] =  $ssh_result['status'] = 1;
			self::save_logs_site($logs_created, $site['site_id']);
		}
		else
		{
			$logs_created[5]['status'] =  $ssh_result['status'] = 0;
			self::save_logs_site($logs_created, $site['site_id']);
		}
		
 
		return $ssh_result;
		 
	}


	/**
	 * [exec_shell description] => Move data from server demo - to server real
	 * @param  string $site         [description]
	 * @param  string $logs_created [description]
	 * @return [type]               [description]
	 */
	static function exec_shell_move($site = "",$logs_created= "")
	{
		global $CMS;
		$info = "db_name={$site['db_name']}|||db_user={$site['db_username']}|||db_pass={$site['db_password']}|||url=http://{$site['site_domainname']}|||domain={$site['site_domainname']}|||theme={$base_theme}|||acp_username={$site['username']}|||acp_pwdhash={$site['pwdhash']}|||acp_pwdhash={$site['pwdsalt']}|||hosting_username={$site['hosting_username']}|||default_language={$site['default_language']}|||storename={$site['storename']}|||email_user={$site['email_hosting']}|||email_password={$site['email_password']}|||";
 		// Connect server web-demo
 		# Export DB: 
 		# Clone source to Server Us
 			 
		//Connect SSH
		$connection = \ssh2_connect("{$CMS->vars['ssh_server']}",$CMS->vars['ssh_port'], array('hostkey'=>'ssh-rsa'));
		if (!$connection) 
		{
			$logs_created[3]['msg'] = $ssh_result['msg'] =  "Connection failed !"; 
			$logs_created[3]['status'] = $ssh_result['status'] =  0;	
		  	self::save_logs_site($logs_created, $site['site_id']);
		  	return $ssh_result;
		} 


		if(\ssh2_auth_pubkey_file($connection, "{$CMS->vars['ssh_user']}", "/home/.ssh/".$CMS->vars['rsa_key'].".pub", "/home/.ssh/".$CMS->vars['rsa_key'],'')) 
		{
			 echo "Public Key Authentication Successful\n";
		} 
		else 
		{
			$logs_created[3]['msg'] = $ssh_result['msg'] =  "SSH Authorization failed !"; 
			$logs_created[3]['status'] = $ssh_result['status'] =  0;	
		  	self::save_logs_site($logs_created, $site['site_id']);
		  	return $ssh_result;
		}

		 

	 	ssh2_exec($connection, 'mysqldump --defaults-extra-file=/root/mysql/.sqlpwd '.$site['db_name'].' > /home/'.$site['hosting_username'].'/public_html/db/eco_base.sql');
 
		$stream_s = ssh2_exec($connection, "rsync -ave 'ssh -p22 -i /root/.ssh/".$CMS->vars['ssh_rsync_key']."'  --include '.htaccess' --quiet --delete --numeric-ids /home/".$site['hosting_username']."/public_html/* root@".$CMS->vars['ssh_server_real'].":/home/".$site['hosting_username']."/public_html/");

		 //rsync -ave 'ssh -p22 -i /root/.ssh/webhost70-rsync-key' --quiet --delete --numeric-ids /home/webtaolojnglj/public_html/* root@173.199.123.162:/home/webtaolojnglj/public_html/"
		 //
		stream_set_blocking($stream_s, true);
		$stream_s_out = ssh2_fetch_stream($stream_s, SSH2_STREAM_STDIO);
		$stream_s_out_content =  stream_get_contents($stream_s_out);
		$logs_created[3]['msg'] =  $ssh_result['msg'] = $stream_s_out_content;
	 
		if($stream_s_out_content == "")
		{	
			$logs_created[3]['status'] =  $ssh_result['status'] = 1;

			self::save_logs_site($logs_created, $site['site_id']);
		}
		else
		{
			$logs_created[3]['status'] =  $ssh_result['status'] = 0;
			self::save_logs_site($logs_created, $site['site_id']);
			return $ssh_result;
		}
	    ssh2_exec($connection, "rsync -ave 'ssh -p22 -i /root/.ssh/".$CMS->vars['ssh_rsync_key']."'  --quiet --delete --numeric-ids /home/".$site['hosting_username']."/public_html/.htaccess root@".$CMS->vars['ssh_server_real'].":/home/".$site['hosting_username']."/public_html/");
		
		// Discontent server demo
		ssh2_exec($connection, 'exit');
		unset($connection);


		//Connect SSH
		
		$connection_2 = \ssh2_connect("{$CMS->vars['ssh_server_real']}",$CMS->vars['ssh_port_real'], array('hostkey'=>'ssh-rsa'));
		if (\ssh2_auth_pubkey_file($connection_2, "{$CMS->vars['ssh_user_real']}", "/home/.ssh/".$CMS->vars['rsa_key'].".pub", "/home/.ssh/".$CMS->vars['rsa_key'],'')) 
		{
			 echo "Public Key Authentication Successful\n";
		} 
		else 
		{
			$logs_created[3]['msg'] = $ssh_result['msg'] =  "SSH Authorization failed !"; 
			$logs_created[3]['status'] = $ssh_result['status'] =  0;	
		  	self::save_logs_site($logs_created, $site['site_id']);
		  	return $ssh_result;
		}

 
		ssh2_exec($connection_2, 'chown -R '.$site['hosting_username'].':'.$site['hosting_username'].' /home/'.$site['hosting_username'].'/public_html/');
 	 //mysql -u root webtaolo_db < /home/webtaolojnglj/public_html/db/eco_base.sql
 	 //
		$stream_db = \ssh2_exec($connection_2, "mysql -u root ".$site['db_name']." < /home/".$site['hosting_username']."/public_html/db/eco_base.sql"); 
		stream_set_blocking($stream_db, true);
		$stream_db_out = ssh2_fetch_stream($stream_db, SSH2_STREAM_STDIO);
		$stream_db_out_content =  stream_get_contents($stream_db_out);
		$logs_created[4]['msg'] =  $ssh_result['msg'] = $stream_db_out_content;
		if($stream_db_out_content == "")
		{	
			$logs_created[4]['status'] =  $ssh_result['status'] = 1;
			self::save_logs_site($logs_created, $site['site_id']);
		}
		else
		{
			$logs_created[4]['status'] =  $ssh_result['status'] = 0;
			self::save_logs_site($logs_created, $site['site_id']);
			return $ssh_result;
		}
		

		sleep(2);
		$stream_usr = \ssh2_exec($connection_2, "php -f /home/".$site['hosting_username']."/public_html/db/createusr_script_base.php");
		stream_set_blocking($stream_usr, true);
		$stream_usr_out = ssh2_fetch_stream($stream_usr, SSH2_STREAM_STDIO);
		$stream_usr_out_content =  stream_get_contents($stream_usr_out);
		 
 
		$logs_created[5]['msg'] =  $ssh_result['msg'] = $stream_usr_out_content;
		if($stream_usr_out_content == "create_user_admin_done")
		{	
			$logs_created[5]['status'] =  $ssh_result['status'] = 1;
 
			self::save_logs_site($logs_created, $site['site_id']);
		}
		else
		{
			$logs_created[5]['status'] =  $ssh_result['status'] = 0;
			self::save_logs_site($logs_created, $site['site_id']);
		}
		

		return $ssh_result;
		 
	}



	/**
	 * [exec_shell_changedomain description]
	 * @param  string $site [description]
	 * @return result
	 */
	static function exec_shell_changedomain($site = "")
	{
		global $CMS;
		 
		//Connect SSH
		 if($site['site_domain_extra'] != "")
		{
			$info = "db_name={$site['db_name']}|||db_user={$site['db_username']}|||db_pass={$site['db_password']}|||url=http://{$site['site_domain_extra']}|||domain={$site['site_domain_extra']}|||theme={$site['site_theme']}|||acp_username={$site['username']}|||acp_pwdhash={$site['pwdhash']}|||acp_pwdhash={$site['pwdsalt']}|||hosting_username={$site['hosting_username']}|||default_language={$site['default_language']}|||storename={$site['storename']}|||email_user={$site['email_hosting']}|||email_password={$site['email_password']}|||";
		}
		else
		{
			$info = "db_name={$site['db_name']}|||db_user={$site['db_username']}|||db_pass={$site['db_password']}|||url=http://{$site['site_domainname']}|||domain={$site['site_domainname']}|||theme={$site['site_theme']}|||acp_username={$site['username']}|||acp_pwdhash={$site['pwdhash']}|||acp_pwdhash={$site['pwdsalt']}|||hosting_username={$site['hosting_username']}|||default_language={$site['default_language']}|||storename={$site['storename']}|||email_user={$site['email_hosting']}|||email_password={$site['email_password']}|||";
		}
 		
 		$connection = \ssh2_connect("{$CMS->vars['ssh_server']}",$CMS->vars['ssh_port'], array('hostkey'=>'ssh-rsa'));
		if (!$connection) 
		{
			$logs_created[3]['msg'] = $ssh_result['msg'] =  "Connection failed !"; 
			$logs_created[3]['status'] = $ssh_result['status'] =  0;	
		  	self::save_logs_site($logs_created, $site['site_id']);
		  	return $ssh_result;
		} 


		if(\ssh2_auth_pubkey_file($connection, "{$CMS->vars['ssh_user']}", "/home/.ssh/".$CMS->vars['rsa_key'].".pub", "/home/.ssh/".$CMS->vars['rsa_key'],'')) 
		{
			 echo "Public Key Authentication Successful\n";
		} 
		else 
		{
			$logs_created[3]['msg'] = $ssh_result['msg'] =  "SSH Authorization failed !"; 
			$logs_created[3]['status'] = $ssh_result['status'] =  0;	
		  	self::save_logs_site($logs_created, $site['site_id']);
		  	return $ssh_result;
		}
		\ssh2_exec($connection, "echo '".$info."' > /home/".$site['hosting_username']."/public_html/db/db_info.txt");
		$stream_usr = \ssh2_exec($connection, "sed -i -- 's/".$site['old_site_domainname']."/".$site['site_domainname']."/g' /home/".$site['hosting_username']."/public_html/config.route.php");
		stream_set_blocking($stream_usr, true);
		$stream_usr_out = ssh2_fetch_stream($stream_usr, SSH2_STREAM_STDIO);
		$stream_usr_out_content =  stream_get_contents($stream_usr_out);

		$logs_created[5]['msg'] =  $stream_usr_out_content;
		if($stream_usr_out_content == "")
		{	
			$logs_change['status'] = 1;
		}
		else
		{
			$logs_change['status'] = 0;
			$logs_change['msg'] = $stream_usr_out_content;
		}
		 
 
		return $logs_change;
		 
	}


	/**
	 * [exec_shell_change password description]
	 * @param  string $site [description]
	 * @return result
	 */
	static function exec_shell_changepassword($site = "")
	{
		global $CMS;
		 
		if($site['site_domain_extra'] != "")
		{
			$info = "db_name={$site['db_name']}|||db_user={$site['db_username']}|||db_pass={$site['db_password']}|||url=http://{$site['site_domain_extra']}|||domain={$site['site_domain_extra']}|||theme={$site['site_theme']}|||acp_username={$site['username']}|||acp_pwdhash={$site['pwdhash']}|||acp_pwdhash={$site['pwdsalt']}|||hosting_username={$site['hosting_username']}|||default_language={$site['default_language']}|||storename={$site['storename']}|||email_user={$site['email_hosting']}|||email_password={$site['email_password']}|||";
		}
		else
		{
			$info = "db_name={$site['db_name']}|||db_user={$site['db_username']}|||db_pass={$site['db_password']}|||url=http://{$site['site_domainname']}|||domain={$site['site_domainname']}|||theme={$site['site_theme']}|||acp_username={$site['username']}|||acp_pwdhash={$site['pwdhash']}|||acp_pwdhash={$site['pwdsalt']}|||hosting_username={$site['hosting_username']}|||default_language={$site['default_language']}|||storename={$site['storename']}|||email_user={$site['email_hosting']}|||email_password={$site['email_password']}|||";
		}
		//Connect SSH
		 
		$connection = \ssh2_connect("{$CMS->vars['ssh_server']}",$CMS->vars['ssh_port'], array('hostkey'=>'ssh-rsa'));
		if (!$connection) 
		{
			$logs_created[3]['msg'] = $ssh_result['msg'] =  "Connection failed !"; 
			$logs_created[3]['status'] = $ssh_result['status'] =  0;	
		  	self::save_logs_site($logs_created, $site['site_id']);
		  	return $ssh_result;
		} 


		if(\ssh2_auth_pubkey_file($connection, "{$CMS->vars['ssh_user']}", "/home/.ssh/".$CMS->vars['rsa_key'].".pub", "/home/.ssh/".$CMS->vars['rsa_key'],'')) 
		{
			 echo "Public Key Authentication Successful\n";
		} 
		else 
		{
			$logs_created[3]['msg'] = $ssh_result['msg'] =  "SSH Authorization failed !"; 
			$logs_created[3]['status'] = $ssh_result['status'] =  0;	
		  	self::save_logs_site($logs_created, $site['site_id']);
		  	return $ssh_result;
		}
 		//Push info host to db_info.txt
		\ssh2_exec($connection, "echo '".$info."' > /home/".$site['hosting_username']."/public_html/db/db_info.txt");
		sleep(1);
		// Run php script
		$stream_usr = \ssh2_exec($connection, "php -f /home/".$site['hosting_username']."/public_html/db/change_password_acp.php");
	 
		stream_set_blocking($stream_usr, true);
		$stream_usr_out = ssh2_fetch_stream($stream_usr, SSH2_STREAM_STDIO);
		$stream_usr_out_content =  stream_get_contents($stream_usr_out);

		 
		if($stream_usr_out_content == "connection_error" OR $stream_usr_out_content == "no_account_admin" OR $stream_usr_out_content == "update_password_acp_faild")
		{	
			$logs_change['status'] = 0;
			$logs_change['msg'] = $stream_usr_out_content;
		}
		else
		{
			$logs_change['status'] = 1;
			$logs_change['msg'] = $stream_usr_out_content;
		}
		 
 
		return $logs_change;
		 
	}

	/**
	 * [exec_shell_changedomain description]
	 * @param  string $site [description]
	 * @return result
	 */
	static function exec_shell_changetheme($site = "")
	{
		global $CMS;
		if($site['site_domain_extra'] != "")
		{
			$info = "db_name={$site['db_name']}|||db_user={$site['db_username']}|||db_pass={$site['db_password']}|||url=http://{$site['site_domain_extra']}|||domain={$site['site_domain_extra']}|||theme={$site['site_theme']}|||acp_username={$site['username']}|||acp_pwdhash={$site['pwdhash']}|||acp_pwdhash={$site['pwdsalt']}|||hosting_username={$site['hosting_username']}|||default_language={$site['default_language']}|||storename={$site['storename']}|||email_user={$site['email_hosting']}|||email_password={$site['email_password']}|||";
		}
		else
		{
			$info = "db_name={$site['db_name']}|||db_user={$site['db_username']}|||db_pass={$site['db_password']}|||url=http://{$site['site_domainname']}|||domain={$site['site_domainname']}|||theme={$site['site_theme']}|||acp_username={$site['username']}|||acp_pwdhash={$site['pwdhash']}|||acp_pwdhash={$site['pwdsalt']}|||hosting_username={$site['hosting_username']}|||default_language={$site['default_language']}|||storename={$site['storename']}|||email_user={$site['email_hosting']}|||email_password={$site['email_password']}|||";
		}
	
		//Connect SSH 
		$connection = \ssh2_connect("{$CMS->vars['ssh_server']}",$CMS->vars['ssh_port'], array('hostkey'=>'ssh-rsa'));
		
		if (!$connection) 
		{
			$logs_created[3]['msg'] = $ssh_result['msg'] =  "Connection failed !"; 
			$logs_created[3]['status'] = $ssh_result['status'] =  0;	
		  	self::save_logs_site($logs_created, $site['site_id']);
		  	return $ssh_result;
		} 

		if(\ssh2_auth_pubkey_file($connection, "{$CMS->vars['ssh_user']}", "/home/.ssh/".$CMS->vars['rsa_key'].".pub", "/home/.ssh/".$CMS->vars['rsa_key'],''))
		{
			 echo "Public Key Authentication Successful\n";
		} 
		else 
		{
			$logs_created[3]['msg'] = $ssh_result['msg'] =  "SSH Authorization failed !"; 
			$logs_created[3]['status'] = $ssh_result['status'] =  0;	
		  	self::save_logs_site($logs_created, $site['site_id']);
		  	return $ssh_result;
		}
 		//Push info host to db_info.txt
		\ssh2_exec($connection, "echo '".$info."' > /home/".$site['hosting_username']."/public_html/db/db_info.txt");
		sleep(1);
 		// del folder old theme
 		\ssh2_exec($connection, "rm -rf /home/".$site['hosting_username']."/public_html/themes/*");

		// Sync new folder theme from original
		\ssh2_exec($connection, "rsync -ave '' --include '/".$site['site_theme']."/' --exclude '/*' --delete --numeric-ids   /home/_original/themes/ /home/".$site['hosting_username']."/public_html/themes/");

		// Run php script
		$stream_usr = \ssh2_exec($connection, "php -f /home/".$site['hosting_username']."/public_html/db/db_script_change_theme.php");
	 
		stream_set_blocking($stream_usr, true);
		$stream_usr_out = ssh2_fetch_stream($stream_usr, SSH2_STREAM_STDIO);
		$stream_usr_out_content =  stream_get_contents($stream_usr_out);

		 
		if($stream_usr_out_content != "")
		{	
			$logs_change['status'] = 1;
		}
		else
		{
			$logs_change['status'] = 0;
			$logs_change['msg'] = $stream_usr_out_content;
		}
		 
 
		return $logs_change;
		 
	}

	/**
	 * [exec_shell exec_shell_syncweb] => Sync new source from folder source original to web
	 * @param  string $site         [description]
	 * @param  string $logs_created [description]
	 * @return [type]               [description]
	 */
	static function exec_shell_rsync($site = "",$logs_created= "")
	{
		global $CMS;
	
 		# Clone new source to web
 		if($site['site_domain_extra'] != "")
		{
			$info = "db_name={$site['db_name']}|||db_user={$site['db_username']}|||db_pass={$site['db_password']}|||url=http://{$site['site_domain_extra']}|||domain={$site['site_domain_extra']}|||theme={$site['site_theme']}|||acp_username={$site['username']}|||acp_pwdhash={$site['pwdhash']}|||acp_pwdhash={$site['pwdsalt']}|||hosting_username={$site['hosting_username']}|||default_language={$site['default_language']}|||storename={$site['storename']}|||email_user={$site['email_hosting']}|||email_password={$site['email_password']}|||";
		}
		else
		{
			$info = "db_name={$site['db_name']}|||db_user={$site['db_username']}|||db_pass={$site['db_password']}|||url=http://{$site['site_domainname']}|||domain={$site['site_domainname']}|||theme={$site['site_theme']}|||acp_username={$site['username']}|||acp_pwdhash={$site['pwdhash']}|||acp_pwdhash={$site['pwdsalt']}|||hosting_username={$site['hosting_username']}|||default_language={$site['default_language']}|||storename={$site['storename']}|||email_user={$site['email_hosting']}|||email_password={$site['email_password']}|||";
		} 
		//Connect SSH
		 
		$connection = \ssh2_connect("{$CMS->vars['ssh_server']}",$CMS->vars['ssh_port'], array('hostkey'=>'ssh-rsa'));
		if (!$connection) 
		{
			$logs_created[3]['msg'] = $ssh_result['msg'] =  "Connection failed !"; 
			$logs_created[3]['status'] = $ssh_result['status'] =  0;	
		  	self::save_logs_site($logs_created, $site['site_id']);
		  	return $ssh_result;
		} 

		if(\ssh2_auth_pubkey_file($connection, "{$CMS->vars['ssh_user']}", "/home/.ssh/".$CMS->vars['rsa_key'].".pub", "/home/.ssh/".$CMS->vars['rsa_key'],'')) 
		{
			 echo "Public Key Authentication Successful\n";
		} 
		else 
		{
			$logs_created[3]['msg'] = $ssh_result['msg'] =  "SSH Authorization failed !"; 
			$logs_created[3]['status'] = $ssh_result['status'] =  0;	
		  	self::save_logs_site($logs_created, $site['site_id']);
		  	return $ssh_result;
		}

 		// del folder old theme
		\ssh2_exec($connection, "rm -rf /home/".$site['hosting_username']."/public_html/uploads/demo3f/*");   
 		\ssh2_exec($connection, "rm -rf /home/".$site['hosting_username']."/public_html/themes/*");
 
		//Start copy source orginal
		ssh2_exec($connection, "rsync -ave '' --include '/themes/".$site['site_theme']."/' --exclude '/themes/*'  --exclude 'error_log' --exclude '/cgi-bin'  --exclude '/uploads' --exclude '/db/db_info.txt' --exclude '/db/eco_base.sql' --exclude 'config.inc.php' --exclude 'config.route.php' --delete --numeric-ids   /home/_original/ /home/".$site['hosting_username']."/public_html/");
	
		$logs_created[3]['status'] = 1; 
		$logs_change['status'] = 1;	

	  	
	  //	self::save_logs_site($logs_created, $site['site_id']);
		sleep(5);
		ssh2_exec($connection, 'chown -R '.$site['hosting_username'].':'.$site['hosting_username'].' /home/'.$site['hosting_username'].'/public_html/');
		ssh2_exec($connection, 'rm -rf /home/'.$site['hosting_username'].'/public_html/uploads/'.$site['hosting_username'].'/cache');
// Run php script
		ssh2_exec($connection, "echo '".$info."' > /home/".$site['hosting_username']."/public_html/db/db_info.txt");
		 \ssh2_exec($connection, "php -f /home/".$site['hosting_username']."/public_html/db/db_script_change_theme.php");
	 
		//sleep for 1 seconds
		sleep(1);
 
		return $logs_change;
 
	}

	static function health_site($site = "")
	{
		global $CMS;
	
		$file = "http://".$site['site_domainname'];
		$file_headers = @get_headers($file);

 		if(!$file_headers || preg_match('/200/', $file_headers[0]) == false)
		{
			
			$file_headers[0] = $CMS->class->editor->input($file_headers[0],"text");
		  	$health['status'] = 0;
		   	$health['msg'] = $file_headers[0];
			 
		
		}
		else {
			//check error mirgation 
			$site_string = file_get_contents($file);
			if(  preg_match('/(SQL error)/', $site_string) == true)
			{
			    $site_string = $CMS->class->editor->input($site_string,"text");
		  		$health['status'] = 0;
		   		$health['msg'] = $site_string;
			}
			else
			{
		    	$health['status'] = 1;
		    }

	 
		}

		return $health;
	}


	/**
	 * [exec_shell_changed mode debug description]
	 * @param  string $site [description]
	 * @return result
	 */
	static function exec_shell_change_modedebug($site = "", $mode_debug = "development")
	{
		global $CMS;
		$info = "db_name={$site['db_name']}|||db_user={$site['db_username']}|||db_pass={$site['db_password']}|||url=http://{$site['site_domainname']}|||domain={$site['site_domainname']}|||theme={$site['site_theme']}|||acp_username={$site['username']}|||acp_pwdhash={$site['pwdhash']}|||acp_pwdhash={$site['pwdsalt']}|||hosting_username={$site['hosting_username']}|||default_language={$site['default_language']}|||storename={$site['storename']}|||email_user={$site['email_hosting']}|||email_password={$site['email_password']}|||mode_debug={$mode_debug}|||";
		//Connect SSH
		 
		$connection = \ssh2_connect("{$CMS->vars['ssh_server']}",$CMS->vars['ssh_port'], array('hostkey'=>'ssh-rsa'));
		if (!$connection) 
		{
			$logs_created[3]['msg'] = $ssh_result['msg'] =  "Connection failed !"; 
			$logs_created[3]['status'] = $ssh_result['status'] =  0;	
		  	self::save_logs_site($logs_created, $site['site_id']);
		  	return $ssh_result;
		} 

		if(\ssh2_auth_pubkey_file($connection, "{$CMS->vars['ssh_user']}", "/home/.ssh/".$CMS->vars['rsa_key'].".pub", "/home/.ssh/".$CMS->vars['rsa_key'],'')) 
		{
			 echo "Public Key Authentication Successful\n";
		} 
		else 
		{
			$logs_created[3]['msg'] = $ssh_result['msg'] =  "SSH Authorization failed !"; 
			$logs_created[3]['status'] = $ssh_result['status'] =  0;	
		  	self::save_logs_site($logs_created, $site['site_id']);
		  	return $ssh_result;
		}

 		//Push info host to db_info.txt
		\ssh2_exec($connection, "echo '".$info."' > /home/".$site['hosting_username']."/public_html/db/db_info.txt");
		sleep(1);
		// Run php script
		$stream_usr = \ssh2_exec($connection, "php -f /home/".$site['hosting_username']."/public_html/db/change_mode_debug.php");
	 
		stream_set_blocking($stream_usr, true);
		$stream_usr_out = ssh2_fetch_stream($stream_usr, SSH2_STREAM_STDIO);
		$stream_usr_out_content =  stream_get_contents($stream_usr_out);

		 
		if($stream_usr_out_content == "done_change_mode_debug")
		{	
			$logs_change['status'] = 1;
		}
		else
		{
			$logs_change['status'] = 0;
			$logs_change['msg'] = $stream_usr_out_content;
		}
		 
 
		return $logs_change;
		 
	}

	//===========================================================================
	//  Get server id
	//===========================================================================
	
	static public function get_server()
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

    /**
     * Get numbers of site by regtype
     * @return array
     */
    static function getCountByType()
    {
        global $CMS, $DB;

        $data = [];

        $sql_add = " site_deleted=0 AND ";

        $sql = "SELECT site_regtype, COUNT(site_id) AS cnt FROM  ".root_table."sites WHERE {$sql_add} 1=1 GROUP BY site_regtype ORDER BY cnt DESC";

        $sql= $DB->query($sql);

        while($result = $DB->fetch_assoc($sql))
        {
            $data[$result['site_regtype']] = $result['cnt'];
        }

        return $data;
    }

    static function autocomplete()
    {
        global $CMS;

        self::$maxPage = 3;
        $data = self::listing();

        $li = "";

        $return = [
            "status" => "error",
            "msg" => $CMS->lang['data_not_found']
        ];

        if($CMS->class->page->total_row > 0)
        {
            foreach ($data as $site)
            {
                $li .= "<li><a href='{$CMS->vars['root_domain']}/?site=sites&act=show&id={$site['site_id']}'>{$site['storename']} - {$site['site_domainname']}</a></li>";
            }

            if($CMS->class->page->total_row > 3)
            {
                $li .= "<li class='see_more'><a href='{$CMS->vars['root_domain']}/?site=sites&keyword={$CMS->input['keyword']}' >{$CMS->lang['read_more']} ({$CMS->class->page->total_row}) {$CMS->lang['result']}</a></li>";
            }

            $return = [
                "status" => "success",
                "data_option" => $li
            ];
        }

        echo @json_encode($return); exit;
    }

      /**
     * Get list package
     * @return array
     */
    static function list_package()
    {
        global $CMS, $DB;

        $data = [];

      
 
        $sql = "SELECT * FROM  ".root_table."web_packet WHERE  packet_customer_owner = 0 AND packet_status = 1 ORDER BY packet_id ASC";

        $sql= $DB->query($sql);

        while($result = $DB->fetch_assoc($sql))
        {
        	$packet_price = explode("<br />", $result['packet_price']);
        	$result['packet_price_12'] = explode("=", $packet_price[0])[1];
        	$result['packet_price_24'] = explode("=", $packet_price[1])[1];

        	$result['packet_price_12_bk'] = $CMS->class->input->currency($result['packet_price_12']);
        	$result['packet_price_24_bk'] = $CMS->class->input->currency($result['packet_price_24']);

            $data[] = $result;
        }
 
        return $data;
    }

      /**
     * Get list package
     * @return array
     */
    static function package_info($id)
    {
        global $CMS, $DB;
        $query = "SELECT * FROM  ".root_table."web_packet WHERE  packet_customer_owner = 0 AND packet_status = 1 AND packet_id = '{$id}' ";
 
        $sql= $DB->query($query);
    	$result = $DB->fetch_array($sql);
        $packet_price = explode("<br />", $result['packet_price']);
        $result['packet_price_12'] = explode("=", $packet_price[0])[1];
        $result['packet_price_24'] = explode("=", $packet_price[1])[1];

        $result['packet_price_12_bk'] = $CMS->class->input->currency($result['packet_price_12']);
        $result['packet_price_24_bk'] = $CMS->class->input->currency($result['packet_price_24']);
        return $result;
    }


      /**
     * Get list themes
     * @return array
     */
    static function list_themes()
    {
        global $CMS, $DB;
//        $query = "SELECT * FROM  ".root_table."themes WHERE theme_deleted=0 AND theme_status = 2 AND theme_display = 1 AND theme_locate = 'vn' ORDER BY theme_id ASC  ";
        $query = "SELECT * FROM  ".root_table."themes WHERE theme_deleted=0 AND theme_status = 2 AND theme_display = 1 ORDER BY theme_id ASC  ";

        $sql= $DB->query($query);
        $theme = array();
    	if($DB->num_rows($sql) > 0)
    	{
    		while ($data = $DB->fetch_array($sql)) {
    			# code...
    			$theme[] = $data;
    		}
    	}
    	return $theme;
    }

}
?>