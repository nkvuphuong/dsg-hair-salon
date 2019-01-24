<?php

namespace models;

use core\ezy;
use lib\date;
 
class customer {
	static private $per_page = 20;
	static private $show_page = '';
	static private $sql_query = '';
	static private $arrange_data = '';
	static private $prefix_html = '';
	static private $suffix_html = '';
	
	static public function getInfo($record_id = null, $field_name = '*') {
		if (! is_null($record_id)) {
			global $CMS, $DB, $member;
			$DB->query("SELECT {$field_name} FROM `".root_table."customer` WHERE (`cus_id`='{$record_id}' OR `cus_email`='{$record_id}' OR `cus_code`='{$record_id}') AND `cus_deleted`=0 LIMIT 1");
			if ($DB->num_rows() > 0) {
				$data = $DB->fetch_array();
				if ($field_name !== '*') {
					return $data[$field_name];
				} else {
					return $data;
				}
			}
		}
		return false;
	}
	public function quickAdd($data = null) {
		if (! is_null($data)) {
			global $CMS, $DB;
			if (empty($data['cus_pass'])) {
				$data['cus_token_key'] = '';
				$data['cus_password'] = '';
			} else {
				$data['cus_token_key'] = $CMS->class->random->md5('add_customer');
				$data['cus_password'] = md5 ($data['cus_pass'] . $data['cus_token_key']);
			}
			$data['cus_time'] = time();
			$data['cus_is_reseller'] = intval($data['cus_is_reseller']);
			$DB->query("SELECT `cus_id` FROM `".root_table."customer` WHERE `cus_deleted` =0 AND `cus_email`='{$data['cus_email']}'");
			if ($DB->num_rows() > 0) {
				$cus_id = $DB->fetch_array()['cus_id'];
				$sql = $DB->query("UPDATE `".root_table."customer` SET `cus_time`='{$data['cus_time']}', `cus_full_name`='{$data['cus_full_name']}', `cus_first_name`='{$data['cus_first_name']}', `cus_last_name`='{$data['cus_last_name']}', `cus_address`='{$data['cus_address']}', `cus_phone`='{$data['cus_phone']}', `cus_email`='{$data['cus_email']}' WHERE (`cus_id` = '{$cus_id}')");
				if ($sql) {
					return self::getInfo($cus_id);
				}
			} else {

				$sql = $DB->query("INSERT INTO ".root_table."customer (cus_email, cus_full_name, cus_address, cus_phone, cus_first_name, cus_last_name, cus_password, cus_token_key, cus_time,cus_is_reseller) VALUES ('{$data['cus_email']}', '{$data['cus_full_name']}',  '{$data['cus_address']}', '{$data['cus_phone']}', '{$data['cus_first_name']}', '{$data['cus_last_name']}', '{$data['cus_password']}', '{$data['cus_token_key']}', '{$data['cus_time']}',  '{$data['cus_is_reseller']}')");
				if ($sql) {
					$cus_id = $DB->last_insert_id();
					$DB->query("UPDATE ".root_table."customer SET cus_code='CUS{$cus_id}' WHERE cus_id = '{$cus_id}'");
					return self::getInfo($cus_id);
				}
			}
		}
		return false;
	}
	public function quickUpdate($data = null) {
		global $CMS, $DB, $member;
		if (! is_null($data) && $member['cus_deleted'] == 0) {
			$data = is_array($data) ? $data : array($data);
			foreach ($data as $k => $dt) {
				$DB->query("UPDATE `".root_table."customer` SET `{$k}` = '{$dt}' WHERE `cus_id` = '{$member['cus_id']}' AND `cus_deleted` = 0");
			}
			$CMS->class->cache->mdelete("customer");
		}
		return self::getInfo($member['cus_id']);
	}
	static public function logOut() {
		global $CMS, $DB, $member;
		
		$CMS->class->logs->insert("{$CMS->lang['logout_log']}");
		$CMS->class->cookie->delete("cususername");
		$CMS->class->cookie->delete("cushash");
		
		unset($_SESSION['cus_code']);
		unset($_SESSION['member']);
		unset($_SESSION['is_remember']);
	}
	static public function cusDoIn() {
		global $CMS, $DB, $member;
		if($_SESSION['is_remember'] == 1) {
			$CMS->class->cookie->set_cookie("cususername", $member["cus_code"], $_SESSION['is_remember']);
			$CMS->class->cookie->set_cookie("cushash", $member["cus_password"], $_SESSION['is_remember']);
		}
		 
		$_SESSION["cus_code"] = $member['cus_code'];
		$_SESSION["member"] = $member;
	 
		// Log action
		$CMS->class->logs->insert("{$CMS->lang['login_log']}");
	}
	static public function cusCheckPassword($input_password, $password, $salt) {
		if ((empty($password) && empty($salt)) || (md5($input_password.$salt) == $password)) {
			return true;
		}
		return false;
	}
	public function editInfo($data = null) {
		if (! is_null($data)) {
			global $DB, $member;
			$data['cus_id'] = empty($data['cus_id']) ? intval($member['cus_id']) : intval($data['cus_id']);
			$data['cus_full_name'] = empty($data['cus_full_name']) ? '' : $data['cus_full_name'];
			$data['cus_phone'] = empty($data['cus_phone']) ? '' : $data['cus_phone'];
			$data['cus_address'] = empty($data['cus_address']) ? $CMS->class->random->md5('add_customer') : $data['cus_address'];
			$data['cus_time'] =  time();
			
			$sql = $DB->query("UPDATE ".root_table."customer SET cus_full_name = '{$data['cus_full_name']}', cus_phone = '{$data['cus_phone']}', cus_address = '{$data['cus_address']}', cus_time='{$data['cus_time']}' WHERE cus_id = '{$data['cus_id']}'");
			
			if ($sql) {
				return true;
			}
		}
		return false;
	}
	public function editPass($data = null) {
		if (! is_null($data)) {
			global $CMS, $DB, $member;
			$data['cus_id'] = empty($data['cus_id']) ? intval($member['cus_id']) : intval($data['cus_id']);
			$data['cus_token_key'] = empty($data['cus_token_key']) ?  $CMS->class->random->md5('add_customer') : $data['cus_token_key'];
			$data['cus_password'] = md5 ((empty($data['cus_password']) ? '' : $data['cus_password']) . $data['cus_token_key']);
			$data['cus_time'] =  time();
			
			$sql = $DB->query("UPDATE ".root_table."customer SET cus_token_key = '{$data['cus_token_key']}', cus_password = '{$data['cus_password']}', cus_time='{$data['cus_time']}' WHERE cus_id = '{$data['cus_id']}'");
			
			if ($sql) {
				return true;
			}
		}
		return false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	static public function listing () {
		global $CMS, $DB, $member;
		$this->arrange_data = trim("cus_id,cus_full_name,cus_type,cus_phone,cus_email,cus_group,cus_time");
		$default_field = $CMS->input['order'] ? $CMS->input['order'] : "cus_id";
		$default_order = $CMS->input['by'] ? $CMS->input['by'] : "DESC";
		$where = '';		
		if (!empty($CMS->input['cus_id'])) {
			$where .= " AND `cus_id`='{$CMS->input['cus_id']}' ";
		}
		if (!empty($CMS->input['full_name'])) {
			$where .= " AND `cus_full_name` LIKE '%{$CMS->input['full_name']}%' ";
		}
		if (!empty($CMS->input['cus'])) {
			$DB->query("SELECT `user_id` FROM `".root_table."user` WHERE `user_name` LIKE '%{$CMS->input['cus']}%'");
			if ($DB->num_rows() > 0) {
				$arr=array();
				while ($result = $DB->fetch_array()) {
					array_push($arr, $result['user_id']);
				}
				$where .= " AND `cus_cus` IN ('".implode("','",$arr)."') ";
			}
		}
		if (!empty($CMS->input['type'])) {
			$where .= " AND `cus_type` LIKE '%{$CMS->input['type']}%' ";
		}
		if (!empty($CMS->input['brithday'])) {
			$where .= " AND `cus_birthday` = '{$CMS->input['brithday']}' ";
		}
		if (!empty($CMS->input['sex']) && in_array($CMS->input['sex'], array(0,1,2))) {
			$where .= " AND `cus_sex` = '{$CMS->input['sex']}' ";
		}
		if (!empty($CMS->input['city']) && $CMS->customer->getInfoCityVN($CMS->input['city'], 'cv_id')) {
			$where .= " AND `cus_city` = '{$CMS->input['city']}' ";
		}
		if (!empty($CMS->input['district']) && $CMS->customer->getInfoDistrictVN($CMS->input['district'], 'dv_id')) {
			$where .= " AND `cus_district` = '{$CMS->input['district']}' ";
		}

		if (!empty($CMS->input['district']) && $CMS->customer->getInfoDistrictVN($CMS->input['district'], 'dv_id')) {
			$where .= " AND `cus_district` = '{$CMS->input['district']}' ";
		}

		if ( !empty($CMS->input['cus_time_from']) && Validate::isNum($CMS->input['cus_time_from']) )
		{
			$where.=" AND `cus_time`>='{$CMS->input['cus_time_from']}'";
		}
		if ( !empty($CMS->input['cus_time_to']) && Validate::isNum($CMS->input['cus_time_to']) )
		{
			$where.=" AND `cus_time`<='{$CMS->input['cus_time_to']}'";
		}

		// Ngày tạo KH
		if($CMS->input['time_from'] and $CMS->input['time_to'])
    	{
    		$where .= " AND cus_time BETWEEN '{$CMS->input['time_from']}' AND '{$CMS->input['time_to']}' ";
    	}elseif($CMS->input['time_from'])
    	{
    		$where .= " AND cus_time > '{$CMS->input['time_from']}' ";
    	}elseif($CMS->input['time_to'])
    	{
    		$where .= " AND cus_time < '{$CMS->input['time_to']}' ";
    	}
    	

		list($this->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM `".root_table."customer` WHERE `cus_deleted`=0 {$where} ORDER BY {$default_field} {$default_order}",$this->per_page,$this->prefix_html,$this->suffix_html);
		$arr = array();
		if ($DB->num_rows($this->sql_query)>0) {
			while ($result = $DB->fetch_array($this->sql_query)) {
				array_push($arr,$this->convertvalue($result));
			}
		}
		return $arr;
	}
	public function convertvalue ($data=null) {
		global $CMS, $DB, $member;
		$data['cus_time_c'] = $CMS->class->date->date_format($data['cus_time'],1);
		$data['cus_group_c'] = $CMS->group_customer->getInfo($data['cus_group'], 'gc_name');
		$data['cus_city_c'] = $this->getInfoCityVN($data['cus_city'], 'cv_name');
		$data['cus_district_c'] = $this->getInfoDistrictVN($data['cus_district']);
		$data['cus_district_c'] = $data['cus_district_c']['dv_type'].' '.$data['cus_district_c']['dv_name'];
		$data['cus_cus_c'] = $CMS->user->get_info($data['cus_cus'], 'user_name');
		switch ($data['cus_sex']) {
			case '1':
				$data['cus_sex_c'] = '<i class="fa fa-mars"></i> '.$CMS->lang['cus_sex_01'];
				break;
			case '2':
				$data['cus_sex_c'] = '<i class="fa fa-venus"></i> '.$CMS->lang['cus_sex_02'];
				break;
			default:
				$data['cus_sex_c'] = '';
				break;
		}
		return $data;
	}
	/**
	 * Function Get info customer
	 * 
	 */
	
	public function getAllLike($name = null, $field_name = '*') {
		global $CMS, $DB, $member;
		if (!empty($name)) {
			$DB->query("SELECT {$field_name} FROM `".root_table."customer` WHERE `cus_full_name` LIKE '%{$name}%' AND `cus_deleted`=0");
			$arr =array();
			if ($DB->num_rows() > 0) {
				while ($result = $DB->fetch_array()) {
					array_push($arr, $result);
				}
			}
			return $arr;
		}
		return false;
	}
	public function add($cus_full_name=null, $cus_address=null, $cus_type=null, $cus_phone=null, $cus_company=null, $cus_tax_code=null, $cus_group=null, $cus_sex=null, $cus_birthday=null, $cus_city=null, $cus_district=null, $cus_email=null, $cus_cus=null, $cus_note=null) {
		if (!empty($cus_full_name) && !empty($cus_address) && !empty($cus_type) && !empty($cus_phone) && !empty($cus_company) && !empty($cus_tax_code) && !empty($cus_group) && !empty($cus_sex) && !empty($cus_birthday) && !empty($cus_city) && !empty($cus_district) && !empty($cus_email) && !empty($cus_cus)) {
			global $CMS, $DB, $member;
			$DB->query("INSERT INTO `".root_table."customer` (`cus_time`, `cus_full_name`, `cus_address`, `cus_type`, `cus_phone`, `cus_company`, `cus_tax_code`, `cus_group`, `cus_sex`, `cus_birthday`, `cus_city`, `cus_district`, `cus_email`, `cus_cus`, `cus_note`) VALUES ('".time()."','{$cus_full_name}','{$cus_address}','{$cus_type}','{$cus_phone}','{$cus_company}','{$cus_tax_code}','{$cus_group}','{$cus_sex}','{$cus_birthday}','{$cus_city}','{$cus_district}','{$cus_email}','{$cus_cus}','{$cus_note}')");
			$id = $DB->last_insert_id();
			$CMS->class->logs->insert("Add_customer_{$id}");
			return $id;
		}
		return false;
	}
	public function edit($data_info=null, $cus_full_name=null, $cus_address=null, $cus_type=null, $cus_phone=null, $cus_company=null, $cus_tax_code=null, $cus_group=null, $cus_sex=null, $cus_birthday=null, $cus_city=null, $cus_district=null, $cus_email=null, $cus_cus=null, $cus_note=null) {
		if (!empty($data_info) && !empty($cus_full_name) && !empty($cus_address) && !empty($cus_type) && !empty($cus_phone) && !empty($cus_company) && !empty($cus_tax_code) && !empty($cus_group) && !empty($cus_sex) && !empty($cus_birthday) && !empty($cus_city) && !empty($cus_district) && !empty($cus_email) && !empty($cus_cus)) {
			global $CMS, $DB, $member;
			$key = "Edit_customer_{$data_info['cus_id']}";
			$CMS->class->logs->key = $key;
			$CMS->class->logs->old_data = $data_info;
			$CMS->class->logs->insert($key);
			$DB->query("UPDATE `".root_table."customer` SET `cus_full_name`='{$cus_full_name}',`cus_address`='{$cus_address}',`cus_type`='{$cus_type}',`cus_phone`='{$cus_phone}',`cus_company`='{$cus_company}',`cus_tax_code`='{$cus_tax_code}',`cus_group`='{$cus_group}',`cus_sex`='{$cus_sex}',`cus_birthday`='{$cus_birthday}',`cus_city`='{$cus_city}',`cus_district`='{$cus_district}',`cus_email`='{$cus_email}',`cus_cus`='{$cus_cus}',`cus_note`='{$cus_note}' WHERE `cus_deleted`='0' AND `cus_id`='{$data_info['cus_id']}'");
			$CMS->class->logs->key = $key;
			$CMS->class->logs->save_detail("customer",$data_info['cus_id'],$this->getInfo($data_info['cus_id']));
		}
		return false;
	}
	public function deleted($id=null) {
		if (!empty($id)) {
			global $CMS, $DB, $member;
			$DB->query("UPDATE `".root_table."customer` SET `cus_deleted`=1 WHERE `cus_id`='{$id}'");
			$CMS->class->logs->insert("Deleted_customer_{$id}");
		}
		return false;
	}
	public function getAllCityVN () {
		global $CMS, $DB, $member;
		$DB->query("SELECT * FROM `".root_table."city_vn`");
		$arr = array();
		if ($DB->num_rows() > 0) {
			while ($result = $DB->fetch_array()) {
				array_push($arr,$result);
			}
		}
		return $arr;
	}
	public function getInfoCityVN ($record_id=null,$field_name='*') {
		global $CMS, $DB, $member;
		if (!empty($record_id)) {
			$DB->query("SELECT {$field_name} FROM `".root_table."city_vn` WHERE `cv_id`='{$record_id}' LIMIT 1");
			if ($DB->num_rows() > 0) {
				$data = $DB->fetch_array();
				if ($field_name !== '*') {
					return $data[$field_name];
				}
				return $data;
			}
			return false;
		}
	}
	public function getAllDistrictVN($id_city=null) {
		global $CMS, $DB, $member;
		$id_city = intval($id_city);
		$id_city = $id_city<10?'0'.$id_city:$id_city;
		$DB->query("SELECT * FROM `".root_table."district_vn` WHERE `cv_id`='{$id_city}'");
		$arr = array();
		if ($DB->num_rows() > 0) {
			while ($result = $DB->fetch_array()) {
				array_push($arr,$result);
			}
		}
		return $arr;
	}
	public function getInfoDistrictVN ($record_id=null,$field_name='*') {
		global $CMS, $DB, $member;
		if (!empty($record_id)) {
			$DB->query("SELECT {$field_name} FROM `".root_table."district_vn` WHERE `dv_id`='{$record_id}' LIMIT 1");
			if ($DB->num_rows() > 0) {
				$data = $DB->fetch_array();
				if ($field_name !== '*') {
					return $data[$field_name];
				}
				return $data;
			}
			return false;
		}
	}
	public function searchFullName ($name = null) {
		global $CMS, $DB, $member;
		if (!empty($name)) {
			$DB->query("SELECT * FROM `".root_table."customer` WHERE ( `cus_full_name` LIKE '%{$name}%' OR `cus_email` LIKE '%{$name}%' ) AND `cus_deleted`=0 ORDER BY `cus_id` DESC");
			$arr = array();
			if ($DB->num_rows() > 0) {
				while ($result = $DB->fetch_array()) {
					array_push($arr, $result);
				}
			}
			return $arr;
		}
		return false;
	}
	public function checkFullName($name = null, $cus_id = null) {
		global $CMS, $DB, $member;
		if (!empty($name)) {
			$DB->query("SELECT 0 FROM `".root_table."customer` WHERE `cus_full_name`='{$name}' AND `cus_id`!='{$cus_id}' AND `cus_deleted`=0 LIMIT 1");
			if ($DB->num_rows() > 0) {
				return true;
			}
			return false;
		}
	}

	public function getListOption()
	{
		global $CMS, $DB;

		$output = "";
		//AND cus_status = 1
		$sql = $DB->query("SELECT * FROM ".root_table."customer WHERE cus_deleted = 0  ORDER BY cus_full_name ASC");
		if($DB->num_rows($sql) > 0)
		{
			while ($result = $DB->fetch_array($sql)) 
			{
				$output .= "<option value='{$result['cus_id']}'>{$result['cus_full_name']}</option>";
			}
		}

		return $output;
	}

 


	public function searchKey($key='')
	{
		global $CMS, $DB;

		// $data = array(0 => array("value" => "Add new", "id" => "add_new"));
//		if($key)
		{
			$sql = $DB->query("SELECT * FROM ".root_table."customer WHERE (cus_full_name LIKE '%{$key}%' OR cus_email LIKE '%{$key}%') AND cus_deleted = 0 ORDER BY  cus_last_action DESC");
			if($DB->num_rows($sql) > 0)
			{
				while ($result = $DB->fetch_array($sql)) 
				{
					$arr['value'] = $result['cus_full_name'];
					$arr['id'] = $result['cus_id'];
					$arr['cus_address'] = $result['cus_address'];
					$arr['cus_email'] = $result['cus_email'];
					$arr['cus_phone'] = $result['cus_phone'];
					$data[] = $arr;
				}
			}
			else
			{
				$arr['value'] = "Add new";
				$arr['id'] = "add_new";
				$arr['cus_address'] = "";
				$arr['cus_email'] = "";
				$arr['cus_phone'] = "";
				$data[] = $arr;
			}
		}
		return $data;
	}

	static function update_last_action_time($cus_id = 0)
    {
        global $DB;

        $cus_id = intval($cus_id);

        if(!$cus_id) return false;

        $sql = "UPDATE ".root_table."customer SET  cus_last_action=UNIX_TIMESTAMP() WHERE cus_id=${cus_id}";

        return $DB->query($sql);
    }
    /**
     * Function register account
     */
    static public function register_account($data = array(), $type='', $send_mail = 1, $check_security = 1)
	{
		global $CMS, $DB, $member;
		if(empty($data) || !is_array($data))
		{
			$data = $CMS->input;
		}
		
		// Check login fb : password is empty
		if($type != 1)
		{
			self::$token_key = $CMS->class->random->md5("add_customer");
			$data['cus_token_key'] = self::$token_key;
		}
		$data['cus_time'] = time();
		 
		// Check required input
		$required_input = array('cus_full_name', 'cus_email', 'cus_password', 'cus_repassword');

	
		if(  !is_array($data))
		{  
			$cus_password = $data['cus_password'] = $data['cus_repassword'] = $CMS->class->random->character(9);
		}
		else
		{
			$cus_password = $data['cus_password'];
		}
	  	
	  	// Register from form
	  	if($type == "")
	  	{
	  		//Check input
	  		if($data['cus_email'] == "")
		  	{ 
		  		$_SESSION['error_msg'] .= "Email is not valid!<br />";
				return false;
		  	}
		  	if(empty($data['cus_password']) OR strlen($data['cus_password']) < 6)
			{
				$_SESSION['error_msg'] .= "Please enter a password. Password must be 6 characters<br />";
				return false;
			}

			if(empty($data['cus_repassword']) OR strlen($data['cus_repassword']) < 6)
			{
				$_SESSION['error_msg'] .= "Please enter a password. Password must be 6 characters<br />";
				return false;
			}
			

	  	}
		if($data['cus_password'] != $data['cus_repassword'])
	  	{ 
	  		$_SESSION['error_msg'] .= "Password does not match!<br />";
			return false;
	  	}

		if( self::cus_check_exist($data['cus_email'], 1))
		{
			$_SESSION['error_msg'] .= "Email already exists!<br />";
			return false;
		}
		
		if(self::cusphone_check_exist($data['cus_phone'], 1) AND $data['cus_phone'] == "" and $type != 1) 
		{
			$_SESSION['error_msg'] .= "Phone number not empty!<br />";
			return false;
		}


		// Insert data
		$columns = $DB->get_column_names('customer');
		$sql_insert_fields = "";
		$sql_insert_values = "";
		foreach($columns as $field)
		{
			if(isset($data[$field]))
			{
				if($field == 'cus_password')
				{	
					// Check login fb : password is empty
					if($type != 1)
					{
						$data[$field] = md5($data[$field].self::$token_key);
					}
					else
					{
						$data[$field] = "";
					}
				}
				$sql_insert_fields .= "{$field},";
				$sql_insert_values .= "'{$data[$field]}',";
			}
		}

		$sql_insert_fields = trim($sql_insert_fields, ',');
		$sql_insert_values = trim($sql_insert_values, ',');

		if(empty($sql_insert_fields) || empty($sql_insert_values))
		{
			$_SESSION['error_msg'] .= "Error during registration (err:1)<br/>"; return false;
		}
		else
		{
			$sql_insert_data = "INSERT INTO ".root_table."customer ($sql_insert_fields) VALUES ($sql_insert_values)";

			if($DB->query($sql_insert_data))
			{
				// Get info
				$inserted_id = $DB->last_insert_id();
				$cus_code = "CUS".$inserted_id;
				// Update customer code
				$update_cus_code = "UPDATE ".root_table."customer SET cus_code = '{$cus_code}'  WHERE cus_id='{$inserted_id}' ";
				$DB->query($update_cus_code);
				// Create log
				$CMS->class->logs->key = "customer_{$inserted_id}";
				$_SESSION["msg"] .= $CMS->class->logs->insert("Create account successfully: <b>#{$cus_code}</b>")."<br />";
			}
			else
			{
				$_SESSION['error_msg']  .= "Error during registration (err:2)<br/>"; 
				return false;
			}
		}
		return $inserted_id;
	}

	/**
	 * cus check exist
	 */
	static public function cus_check_exist( $cus_user, $reload = 0 )
	{
		global $CMS, $DB;
		
		$cus_user = $CMS->class->filter->clean_value($cus_user);
		$sql = $DB->query("SELECT * FROM ".root_table."customer WHERE cus_email = '{$cus_user}' AND cus_deleted=0 ");
		$member = $DB->fetch_array($sql );	
		return $member;
	}
	/**
	 * cus phone check exist
	 */
	static public function cusphone_check_exist( $cus_phone, $reload = 0 )
	{
		global $CMS, $DB;
		
		$cus_phone = $CMS->class->filter->clean_value($cus_phone);
		$sql = $DB->query("SELECT * FROM ".root_table."customer WHERE cus_phone = '{$cus_phone}' AND cus_deleted=0 ");
		$member = $DB->fetch_array($sql );	
		return $member;
	}

	/**
	 * Social login
	 * 
	 */
	static public function social_login($data, $type = 0)
	{
		global $CMS, $DB, $member;
		// User Input
	 	unset($_SESSION['msg']);
		unset($_SESSION['msg_success']);
		$username = $data["contact_email"];
		// Check Input
		if ( ! $username ) { $_SESSION['msg'] = "Failed to get Email information!"; return false; }	
		// Load User
		$member = self::cus_check_exist( $username, 1 );	
	 
		// Continue check Input
		if ( ! $member ) {$_SESSION['msg'] = "Email does not exist!"; return false; }
		  
		// Log in
		self::cusDoIn( $member );
 		if($type == 1)
		{
			$_SESSION['msg'] = "Account login successful!";
		}
		else
		{
			$_SESSION['msg'] = "Account login successful!";
		}
		 
		return true;		
	}

	/**
	 * 	Check and Add account when login thought Social plugin
	 */
    static public function social_add($data)
	{
		global $CMS, $DB, $member;
		// User input
		$cus_full_name =   $data['name'];
		$cus_email = $cus_username =  $data['contact_email'];
		$cus_gender = $data['sex'] == "male" ? 1 : 0;
		$cus_address = $data["city"]["name"];
		$cus_firstname = $data["first_name"];
		$cus_lastname = $data["last_name"];
		$cus_register_time = time();
		
		//self::$token_key = $CMS->class->random->md5("add_customer");
		//$data['cus_token_key'] = self::$token_key;
		//$cus_password = $data['cus_password'] = $data['cus_repassword'] = $CMS->class->random->character(9);	
		//$data['cus_password'] = md5($data['cus_password'].self::$token_key);	 
		$cus_time = time();
		// Insert data
		$DB->query("INSERT INTO ".root_table."customer (cus_full_name, cus_email, cus_address,cus_time  )
		VALUES ('{$cus_full_name}', '{$cus_email}',  '{$cus_address}',  '{$cus_time}' )");
	 
	 	$last_insert_id = $DB->last_insert_id();
	 	$cus_code = "CUS".$last_insert_id;

		$DB->query("UPDATE ".root_table."customer SET cus_code='{$cus_code}' WHERE cus_id = '{$last_insert_id}'"); 
		// Get info
		$customer = self::getInfo($cus_email);
  		 

		// Delete cache
		$CMS->class->cache->mdelete("customer");
		// Create log
		$CMS->class->logs->key = "customer_{$customer['cus_id']}";
		
		return $customer;
	}

	/**
	 * Update new password
	 * @param 
	 */
	static public function update_password( $cus_id, $input_password, $salt )
	{
		global $CMS, $DB;
		$new_password = md5($input_password.$salt);
		// Update
		$DB->query("UPDATE ".root_table."customer SET cus_password='{$new_password}' WHERE cus_id='{$cus_id}'");
		$CMS->class->cache->mdelete("customer");
		return true;
	}

	static function createAccount($data=[])
	{
		global $CMS, $DB;

		// Hàm này mục đích chỉ để insert customer và field yêu cầu chính là: cus_full_name, cus_phone. Các thông tin khác có thể khuyết.

		//Input
		$cus_full_name = $data['cus_full_name'];
		$cus_phone = $data['cus_phone'];
		$cus_email = $data['cus_email'];
		$cus_address = $data['cus_address'];
		$cus_address2 = $data['cus_address2'];
		$cus_city = $data['cus_city'];
		$cus_district = $data['cus_district'];
		$cus_country = $data['cus_country'];
		$cus_company = $data['cus_company'];
		$cus_time = time();
		
		if(!$cus_full_name) { return false;}
		if(!$cus_phone) { return false;}

		// Query insert
		$DB->query("INSERT INTO ".root_table."customer (cus_full_name, cus_phone, cus_email, cus_address, cus_address2, cus_city, cus_district, cus_country, cus_company, cus_time) VALUES ('{$cus_full_name}', '{$cus_phone}', '{$cus_email}', '{$cus_address}', '{$cus_address2}', '{$cus_city}', '{$cus_district}', '{$cus_country}', '{$cus_company}', '{$cus_time}')");
		$cus_id = $DB->last_insert_id();
		//Update cus_code
		$DB->query("UPDATE ".root_table."customer SET cus_code=CONCAT('CUS', cus_id) WHERE cus_id='{$cus_id}'");
		return $cus_id;
	}

	static function getCustomerByPhone($cus_phone="")
	{
		global $CMS, $DB;

		// Input
		$cus_phone = ltrim(trim($cus_phone), "0");

		//Query
		$DB->query("SELECT * FROM ".root_table."customer WHERE cus_deleted=0 AND cus_phone LIKE '%{$cus_phone}%'");
		if($DB->num_rows() > 0)
		{
			return $DB->fetch_array();
		}else
		{
			return false;
		}
	}

}
?>