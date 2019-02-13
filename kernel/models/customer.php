<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->customer=new ClassCustomer;
class ClassCustomer {
	public $per_page = 20;
	public $show_page = '';
	public $sql_query = '';
	public $arrange_data = '';
	public $prefix_html = "";
	public $suffix_html = "";
	public $cache_prefix = "customer";

	public function listing() {
		global $CMS, $DB, $member;
		$this->arrange_data = trim("cus_id,cus_full_name,cus_type,cus_phone,cus_email,cus_group,cus_time");
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "cus_id";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "DESC";
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
		if (!empty($CMS->input['birthday'])) {
			$where .= " AND `cus_birthday` = '{$CMS->input['birthday']}' ";
		}
		if (!empty($CMS->input['sex']) && in_array($CMS->input['sex'], array(0,1,2))) {
			$where .= " AND `cus_sex` = '{$CMS->input['sex']}' ";
		}
		if (!empty($CMS->input['city'])) {
			$where .= " AND `cus_city` = '{$CMS->input['city']}' ";
		}
		if (!empty($CMS->input['district'])) {
			$where .= " AND `cus_district` = '{$CMS->input['district']}' ";
		}

		if (!empty($CMS->input['country'])) {
			$where .= " AND `cus_country` = '{$CMS->input['country']}' ";
		}

		if ( !empty($CMS->input['time_from']) && Validate::isNum($CMS->input['time_from']) )
		{
			$where.=" AND `cus_time`>='{$CMS->input['time_from']}'";
		}
		if ( !empty($CMS->input['time_to']) && Validate::isNum($CMS->input['time_to']) )
		{
			$where.=" AND `cus_time`<='{$CMS->input['time_to']}'";
		}

        if ( ($keyword = trim($CMS->input['keyword'])) != '')
        {
            $keyword = urldecode($keyword);
            $where.=" AND (cus_code LIKE '%$keyword%' OR  cus_full_name LIKE '%$keyword%' OR  cus_email LIKE '%$keyword%' OR cus_tags LIKE '%$keyword%') ";
        }

        if( ($tag = trim( urldecode($CMS->input['tag']) ) ) != '' )
        {
            $where.=" AND cus_tags LIKE '%$tag%' ";
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


        if(trim($CMS->input['group']) != '')
        {
            $cus_group = intval($CMS->input['group']);
            if($cus_group == 0) {
                $where .= " AND (cus_group = '' OR cus_group IS NULL) ";
            } else {
                $where .= " AND cus_group LIKE '%\"{$cus_group}\"%' ";
            }
        }

        $sql = "SELECT * FROM `".root_table."customer` WHERE `cus_deleted`=0 {$where} ORDER BY {$default_field} {$default_order}";

        list($this->show_page, $results) = $DB->fetch_listing($sql,$this->per_page,$this->prefix_html,$this->suffix_html,$CMS->input['page'],$this->cache_prefix);

        $arr = [];

        if($results)
        {
            foreach($results as $result)
            {
                array_push($arr,$this->convertvalue($result));
            }
        }

        return $arr;
	}

	public function convertvalue ($data=null) {
		global $CMS, $DB, $member;

		if(empty($data)) return null;

        $data_bk = $data['data_bk'] = $data;

		$data['cus_time_c'] = $CMS->class->date->date_format($data['cus_time'],1);
//		$data['cus_group_c'] = $CMS->group_customer->getInfo($data['cus_group'], 'gc_name');

        $cus_group = \lib\input::jsonDecode($data['cus_group'] , 0);
        $group_name = [];
        if($cus_group) {
            foreach ($cus_group as $group_id) {
                $group_name[] = $CMS->group_customer->getInfo($group_id, 'gc_name');
            }
        }
		$data['cus_group_c'] = $group_name ? implode(', ', $group_name) : "";
		$data['cus_country_c'] = $CMS->country->country(intval($data['cus_country']) ? intval($data['cus_country']) : -1);
		$data['cus_city_c'] = $CMS->country->city(null,intval($data['cus_city']) ? intval($data['cus_city']) : -1);
		$data['cus_district_c'] = $CMS->country->district(null,intval($data['cus_district']) ? intval($data['cus_district']) : -1);
        $data['cus_district_c'] = trim($data['cus_district_c']) ? $data['cus_district_c'] : 'N/A';
        $data['cus_country_c'] = trim($data['cus_country_c']) ? $data['cus_country_c'] : 'N/A';
        $data['cus_city_c'] = trim($data['cus_city_c']) ? $data['cus_city_c'] : 'N/A';
        $data['cus_address'] = trim($data['cus_address']) ? $data['cus_address'] : 'N/A';
        $data['cus_phone'] = trim($data['cus_phone']) ? $data['cus_phone'] : 'N/A';
        $data['cus_email'] = trim($data['cus_email']) ? $data['cus_email'] : 'N/A';
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

		// info order
		$data['cus_number_order'] = $CMS->order->getOrderinfo($data['cus_id'],"number");
		$data['cus_total_order'] = $CMS->order->getOrderinfo($data['cus_id'],"total");
		$data['cus_last_order'] = $CMS->order->getOrderinfo($data['cus_id'],"last");
	 

		$data['cus_last_order'] = $data['cus_last_order'] ? "<a href='{$CMS->vars['root_domain']}/?site=order&act=show&id={$data['cus_last_order']}'>#{$data['cus_last_order']} <i class=\"fa fa-external-link\" aria-hidden=\"true\"></i></a>" : 'N/A';

//		$data['cus_order_paid'] = $CMS->order->getOrderinfo($data['cus_id'],"paid");
//		$data['cus_order_unpaid'] = $CMS->order->getOrderinfo($data['cus_id'],"unpaid");

        $data['cus_birthday'] = \lib\date::format($data_bk['cus_birthday']);


		return $data;
	}

	public function getInfo($record_id = null, $field_name = '*') {
		global $CMS, $DB, $member;

		if(!$record_id) return false;

		$sql = "SELECT {$field_name} FROM `".root_table."customer` WHERE (`cus_id`='{$record_id}' OR `cus_full_name`='{$record_id}' OR `cus_email`='{$record_id}'  OR `cus_code` ='{$record_id}') AND `cus_deleted`=0 LIMIT 1";

		$results = $DB->fetch_data($sql, $this->cache_prefix);
		$data = isset($results[0]) ? $results[0] : null;

        if ($field_name !== '*') {
            return isset($data[$field_name]) ? $data[$field_name] : null;
        }

        return $data;
	}

	public function getAllLike($name = null, $field_name = '*') {
		global $CMS, $DB, $member;

		if(empty($name)) return false;

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

	public function add($cus_full_name=null, $cus_address=null, $cus_type=null, $cus_phone=null, $cus_company=null, $cus_tax_code=null, $cus_group=null, $cus_sex=null, $cus_birthday=null, $cus_city=null, $cus_district=null, $cus_email=null, $cus_cus=null, $cus_note=null, $cus_address2=null, $cus_country=null, $cus_company_address=null, $cus_company_email=null, $cus_company_phone=null, $cus_email_invoice=null, $cus_image=null) {
		if (!empty($cus_full_name) && !empty($cus_email)) {
			global $CMS, $DB, $member;
			// Add tag
			$cus_tags = @json_encode($CMS->tags->addTags($CMS->input['cus_tags'], "customer"), JSON_UNESCAPED_UNICODE);

            $cus_cus = $member['user_id'];
            $cus_group = trim($cus_group);

			$DB->query("INSERT INTO `".root_table."customer` (`cus_time`, `cus_full_name`, `cus_address`, `cus_type`, `cus_phone`, `cus_company`, `cus_tax_code`, `cus_group`, `cus_sex`, `cus_birthday`, `cus_city`, `cus_district`, `cus_email`, `cus_cus`, `cus_note`, cus_tags, cus_address2, cus_country, cus_company_address, cus_company_email, cus_company_phone, cus_email_invoice, cus_image) VALUES ('".time()."','{$cus_full_name}','{$cus_address}','{$cus_type}','{$cus_phone}','{$cus_company}','{$cus_tax_code}','{$cus_group}','{$cus_sex}','{$cus_birthday}','{$cus_city}','{$cus_district}','{$cus_email}','{$cus_cus}','{$cus_note}', '{$cus_tags}', '{$cus_address2}', '{$cus_country}', '{$cus_company_address}', '{$cus_company_email}', '{$cus_company_phone}', '{$cus_email_invoice}', '{$cus_image}')");
			$id = $DB->last_insert_id();
			$DB->query("UPDATE ".root_table."customer SET cus_code='CUS{$id}' WHERE cus_id='{$id}'");

			//Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$CMS->class->logs->insert("customer_{$id}");
			return $id;
		}
		return false;
	}

	public function edit($data_info=null, $cus_full_name=null, $cus_address=null, $cus_type=null, $cus_phone=null, $cus_company=null, $cus_tax_code=null, $cus_group=null, $cus_sex=null, $cus_birthday=null, $cus_city=null, $cus_district=null, $cus_email=null, $cus_cus=null, $cus_note=null, $cus_address2=null, $cus_country=null, $cus_company_address=null, $cus_company_email=null, $cus_company_phone=null, $cus_email_invoice=null, $cus_image=null) {
        if (!empty($cus_full_name) && !empty($cus_email)) {
			global $CMS, $DB, $member;
			$key = "customer_{$data_info['cus_id']}";
			$CMS->class->logs->key = $key;
			$CMS->class->logs->old_data = $data_info;

            $cus_cus = $member['user_id'];
            $cus_group = trim($cus_group);

            $cus_birthday = $CMS->class->date->date2time($cus_birthday,1);

			// Add tag
			$cus_tags = @json_encode($CMS->tags->addTags($CMS->input['cus_tags'], "customer"), JSON_UNESCAPED_UNICODE);

			$DB->query("UPDATE `".root_table."customer` SET `cus_full_name`='{$cus_full_name}',`cus_address`='{$cus_address}',`cus_type`='{$cus_type}',`cus_phone`='{$cus_phone}',`cus_company`='{$cus_company}',`cus_tax_code`='{$cus_tax_code}',`cus_group`='{$cus_group}',`cus_sex`='{$cus_sex}',`cus_birthday`='{$cus_birthday}',`cus_city`='{$cus_city}',`cus_district`='{$cus_district}',`cus_email`='{$cus_email}',`cus_cus`='{$cus_cus}',`cus_note`='{$cus_note}', cus_tags='{$cus_tags}', cus_address2='{$cus_address2}', cus_country='{$cus_country}', cus_company_address='{$cus_company_address}', cus_company_email='{$cus_company_email}', cus_company_phone='{$cus_company_phone}', cus_email_invoice='{$cus_email_invoice}', cus_image = '{$cus_image}' WHERE `cus_deleted`='0' AND `cus_id`='{$data_info['cus_id']}'");

            //Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$CMS->class->logs->insert("updated customer #{$data_info['cus_id']}");
			$CMS->class->logs->save_detail("customer",$data_info['cus_id'],$this->getInfo($data_info['cus_id']));
		}
		return false;
	}

	public function deleted($id=null) {
		if (!empty($id)) {
			global $CMS, $DB, $member;
			$where = is_numeric($id) ? "`cus_id`='{$id}'" : "`cus_email` ='{$id}'";
			$DB->query("UPDATE `".root_table."customer` SET `cus_deleted`=1 WHERE {$where}");

            //Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$CMS->class->logs->insert("Deleted_customer_{$id}");
		}
		return false;
	}

	public function getAllCityVN() {
		global $CMS, $DB, $member;

		$sql = "SELECT * FROM `".root_table."city_vn`";

        $arr = $DB->fetch_data($sql, 'city_vn');

		return $arr;
	}

	public function getInfoCityVN($record_id=null,$field_name='*') {
		global $CMS, $DB, $member;

		if(!$record_id) return false;

        $sql = "SELECT {$field_name} FROM `".root_table."city_vn` WHERE `cv_id`='{$record_id}' LIMIT 1";

        $data = $DB->fetch_data($sql, 'city_vn')[0];

        if ($field_name !== '*') {
            return $data[$field_name];
        }
        return $data;
	}

    public function getInfoCountryVN($record_id=null,$field_name='*')
    {
        global $CMS, $DB, $member;
        if(!$record_id) return false;

        $sql = "SELECT {$field_name} FROM `".root_table."country` WHERE `country_id`='{$record_id}' LIMIT 1";

        $data = $DB->fetch_data($sql, 'country')[0];

        if ($field_name !== '*') {
            return $data[$field_name];
        }
        return $data;
	}

	public function getAllDistrictVN($id_city=null) {
		global $CMS, $DB, $member;
		$id_city = intval($id_city);
		$id_city = $id_city<10?'0'.$id_city:$id_city;

		$sql = "SELECT * FROM `".root_table."district_vn` WHERE `cv_id`='{$id_city}'";

		$arr = $DB->fetch_data($sql,'district');

		return $arr;
	}

	public function getInfoDistrictVN($record_id=null,$field_name='*') {
		global $CMS, $DB, $member;

		if(!$record_id) return false;

		$sql = "SELECT {$field_name} FROM `".root_table."district_vn` WHERE `dv_id`='{$record_id}' LIMIT 1";

		$data = $DB->fetch_data($sql, 'district_vn')[0];

        if ($field_name !== '*') {
            return $data[$field_name];
        }
        return $data;
	}

	public function searchFullName($name = null) {
		global $CMS, $DB, $member;

		if(!$name) return false;

		$sql = "SELECT * FROM `".root_table."customer` WHERE ( `cus_full_name` LIKE '%{$name}%' OR `cus_email` LIKE '%{$name}%' ) AND `cus_deleted`=0 ORDER BY `cus_id` DESC";

		$arr = $DB->fetch_data($sql, $this->cache_prefix);

        return $arr;
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

	public function getListOption( $cus_selected = 0 )
	{
		global $CMS, $DB;

		$sql = "SELECT * FROM ".root_table."customer WHERE cus_deleted = 0  ORDER BY cus_full_name ASC";

		$results = $DB->fetch_data($sql, $this->cache_prefix);

		$output = "";

		if($results)
		{
			foreach($results as $result)
			{
                $selected = $cus_selected == $result['cus_id'] ? 'selected' : '';
				$output .= "<option {$selected} value='{$result['cus_id']}'>{$result['cus_full_name']}</option>";
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
		    $sql = "SELECT * FROM ".root_table."customer WHERE (cus_full_name LIKE '%{$key}%' OR cus_email LIKE '%{$key}%') AND cus_deleted = 0 ORDER BY  cus_last_action DESC LIMIT 5";

		    $data = $DB->fetch_data($sql, $this->cache_prefix);

            $return = [];
            if($data)
            {
                foreach($data as $result)
                {
                    $arr['value'] = '#'.$result['cus_id'].' - '.$result['cus_full_name'];
                    $arr['id'] = $result['cus_id'];
                    $arr['cus_full_name'] = $result['cus_full_name'];
                    $arr['cus_address'] = $result['cus_address'];
                    $arr['cus_email'] = $result['cus_email'];
                    $arr['cus_email_invoice'] = $result['cus_email_invoice'];
                    $arr['cus_phone'] = $result['cus_phone'];
                    $arr['cus_first_name'] = $result['cus_first_name'];
					$arr['cus_last_name'] = $result['cus_last_name'];
                    $return[] = $arr;
                }
            }
            else
            {
                $arr['value'] = "Add new";
                $arr['id'] = "add_new";
                $arr['cus_address'] = "";
                $arr['cus_email'] = "";
                $arr['cus_email_invoice'] = "";
                $arr['cus_phone'] = "";
                $arr['cus_first_name'] = "";
				$arr['cus_last_name'] = "";
                $return[] = $arr;
            }
		}
		return $return;
	}

	public function quickadd($data = null)
	{
		global $CMS, $DB;

		if($data)
        {
            $CMS->input = array_merge($CMS->input, $data);
        }

		$cus_full_name = $CMS->input['cus_full_name'];
		$cus_phone = $CMS->input['cus_phone'];
		$cus_email = $CMS->input['cus_email'];
		$cus_email_invoice = $CMS->input['cus_email_invoice'];
		$cus_address = $CMS->input['cus_address'];

		$time = time();
		$count = $DB->query("INSERT INTO ".root_table."customer (cus_full_name, cus_phone, cus_email, cus_address, cus_email_invoice) VALUES ('{$cus_full_name}', '{$cus_phone}', '{$cus_email}', '{$cus_address}', '{$cus_email_invoice}')");
		
		if($count) {
			$cus_id = $DB->last_insert_id();
			$DB->query("UPDATE ".root_table."customer SET cus_code='CUS{$cus_id}' WHERE cus_id='{$cus_id}'");
			//clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$CMS->class->logs->insert("customer_{$cus_id}");
			$data = $this->getInfo($cus_id);
            $data['cus_email'] = $data['cus_email_invoice'] ? $data['cus_email_invoice'] : $data['cus_email'];
			return $data;
		}
		return false;
	}

	public function quickedit()
	{
		global $CMS, $DB;

		$cus_full_name = $CMS->input['cus_full_name'];
		$cus_phone = $CMS->input['cus_phone'];
		$cus_email = $CMS->input['cus_email'];
        $cus_email_invoice = $CMS->input['cus_email_invoice'];
		$cus_address = $CMS->input['cus_address'];
		$cus_id = intval($CMS->input['cus_id']);

		$time = time();
		
		$count = $DB->query("UPDATE ".root_table."customer SET cus_full_name = '{$cus_full_name}', cus_phone = '{$cus_phone}', cus_email = '{$cus_email}', cus_address = '{$cus_address}', cus_email_invoice='{$cus_email_invoice}' WHERE cus_id = '{$cus_id}'");

		//clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		if($count)
		{
			$data = $this->getInfo($cus_id);

            $data['cus_email'] = $data['cus_email_invoice'] ? $data['cus_email_invoice'] : $data['cus_email'];

			return $data;
		}else
		{
			return false;
		}
	}

	function update_last_action_time($cus_id = 0)
    {
        global $DB, $CMS;

        $cus_id = intval($cus_id);

        if(!$cus_id) return false;

        $sql = "UPDATE ".root_table."customer SET  cus_last_action=UNIX_TIMESTAMP() WHERE cus_id=${cus_id}";

        //clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

        return $DB->query($sql);
    }

    public function register_account($data = array(), $type='', $send_mail = 1, $check_security = 1)
	{
		global $CMS, $DB, $member;
		

		if(empty($data) || !is_array($data))
		{
			$data = $CMS->input;
		}
	
		$this->token_key = $CMS->class->random->md5("add_customer");
		$data['cus_token_key'] = $this->token_key;
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
	  
	  	if($data['cus_password'] != $data['cus_repassword'])
	  	{ 
	  		$_SESSION['error_msg'] .= "Password does not match!<br />";
			return false;
	  	}
 
		if( $this->cus_check_exist($data['cus_email'], 1))
		{
			$_SESSION['error_msg'] .= "Email already exists!<br />";
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
					$data[$field] = md5($data[$field].$this->token_key);
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

                //clear cache
                $CMS->class->cache->mdelete($this->cache_prefix);

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

	/****************************************************************************
	* cus_check_exist
	* check customer 
	****************************************************************************/
	public function cus_check_exist( $cus_user, $reload = 0 )
	{
		global $CMS, $DB;
		
		$cus_user = $CMS->class->filter->clean_value($cus_user);

		$sql = "SELECT * FROM ".root_table."customer WHERE cus_email = '{$cus_user}' AND cus_deleted=0 LIMIT 1";

		$data = $DB->fetch_data($sql, $this->cache_prefix)[0];

		$member = $data;

		return $member;
	}

	/****************************************************************************
	* cus_check_password
	* check customer password
	****************************************************************************/
	public function cus_check_password( $input_password, $password, $salt )
	{
 
		if ( md5($input_password.$salt) == $password ) 
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	/****************************************************************************
	* cus_do_in
	* log customer in
	****************************************************************************/
	public function cus_do_in()
	{
		global $CMS, $DB, $member;


		if($this->is_remember == 1)
		{
			$CMS->class->cookie->set_cookie("cususername", $member["cus_code"], $this->is_remember);
			$CMS->class->cookie->set_cookie("cushash", $member["cus_password"], $this->is_remember);
		}

		$_SESSION["cus_code"] = $member['cus_code'];
		$_SESSION["member"] = $member;
	 
		// Log action
		$CMS->class->logs->insert("{$CMS->lang['login_log']}");
	}

	public function cus_do_out()
	{
		global $CMS, $DB, $member;

		// Log action
		$CMS->class->logs->insert("{$CMS->lang['logout_log']}");

		$CMS->class->cookie->delete("cususername");
		$CMS->class->cookie->delete("cushash");

		unset($_SESSION['cus_code']);
		unset($_SESSION['member']);
	}


	public function social_login($data, $type = 0)
	{
		global $CMS, $DB, $member;
	
		// User Input
	 	unset($_SESSION['msg']);
		unset($_SESSION['msg_success']);
		$username = $data["contact_email"];
		// Check Input
		if ( ! $username ) { $_SESSION['msg'] = "Failed to get Email information!"; return false; }	
		// Load User
		$member = $this->cus_check_exist( $username, 1 );	
	 
		// Continue check Input
		if ( ! $member ) {$_SESSION['msg'] = "Email does not exist!"; return false; }
		  
		
		// Log in

		$this->cus_do_in( $member );
 		if($type == 1)
		{
			$_SESSION['msg'] = "Account login successful!";
		}
		else
		{
			$_SESSION['msg'] = "Account login successful!";
		}
		if(isset($_SESSION['referer']))
		{
			$page =$_SESSION['referer'];
			unset($_SESSION['referer']);
		 
		 
			header("location: {$page}");exit;

		}
		// Message
		//$CMS->global->page_transfer($CMS->lang['login_msg'],"{$CMS->vars['root_domain']}");
		header("location: {$CMS->vars['root_domain']}");exit;

		return false;		
	}


    public function social_add($data)
	{
		global $CMS, $DB, $member;

		// User input
		$cus_full_name =   $data['name'];
		$cus_email = $cus_username =  $data['contact_email'];
		$cus_gender = $data['sex'] == "male" ? 1 : 0;
		$cus_address = empty($data['city']['name']) ? $data['address'] : $data['city']['name'];
		$cus_firstname = $data['first_name'];
		$cus_lastname = $data['last_name'];
		$cus_phone = empty($data['phone']) ? '' : $data['phone']; 
	 
		$cus_register_time = time();
		
		$this->token_key = $CMS->class->random->md5("add_customer");
		$data['cus_token_key'] = $this->token_key;
		$cus_password = $data['cus_password'] = $data['cus_repassword'] = $CMS->class->random->character(9);
		
		$data['cus_password'] = md5($data['cus_password'].$this->token_key);
		 
		$cus_time = time();
		// Insert data
		$DB->query("INSERT INTO ".root_table."customer (cus_full_name, cus_email,   cus_address,   cus_password , cus_token_key, cus_time , cus_phone)
		VALUES ('{$cus_full_name}', '{$cus_email}',  '{$cus_address}',  '{$data['cus_password']}', '{$data['cus_token_key']}', '{$cus_time}', '{$cus_phone}' )");
		
	 	$last_insert_id = $DB->last_insert_id();
	 	$cus_code = "CUS".$last_insert_id;

		$DB->query("UPDATE ".root_table."customer SET cus_code='{$cus_code}' WHERE cus_id = '{$last_insert_id}'");

		$CMS->class->cache->mdelete($this->cache_prefix);

 
		// Get info
		$customer = $this->getInfo($cus_email);
  		
		//Send mail
		$CMS->email->email_template = "register_by_login_social";

		$CMS->email->email_to = $cus_email;
		$CMS->email->email_toname = $cus_full_name;

		$CMS->email->data['cus_name'] = $cus_full_name;
		$CMS->email->data['username'] = $cus_email;
		$CMS->email->data['password'] = $cus_password;
		$CMS->email->data['website'] = $CMS->vars['root_domain'];
		$CMS->email->quick_send(0,0);



		// Delete cache
 
		$CMS->class->cache->mdelete("customer");
 		 
				
		// Create log
		$CMS->class->logs->key = "customer_{$customer['cus_id']}";
		
		return $customer;
	}

	//===========================================================================
	//  UPDATE PASSWORD
	//===========================================================================
	
	public function update_password( $cus_id, $input_password, $salt )
	{
		global $CMS, $DB;
 
		$new_password = md5($input_password.$salt);
 
		// Update
		$DB->query("UPDATE ".root_table."customer SET cus_password='{$new_password}' WHERE cus_id='{$cus_id}'");

        $CMS->class->cache->mdelete($this->cache_prefix);
		
		return true;
	}

	function importCustomerList()
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
		        	$cus_id = $this->checkExistEmail($cus_email);
		        }else
		        {
		        	continue;
		        }
		        
				// $cus_first_name = $data[0];
				// $cus_last_name = $data[1]; 
				$cus_full_name = $data[0];
				$cus_phone = $data[2];
				$cus_address = $data[4];
				$cus_address2 = $data[5];
				$cus_company = $data[3];

				$cus_city = $data[6];//$CMS->country->getIdByNameCity($data[6]);
				$cus_district =  $data[7];//$CMS->country->getIdByNameDistrict($data[7]);
				$cus_country = $data[9];
				$cus_postalcode = $data[10];
				$receive_email = strtolower($data[11]) == "yes" ? 1 : 0;
				$have_account = strtolower($data[16]) == "yes" ? 1 : 0;
				$cus_tags = explode(",",ltrim(rtrim(trim($data[14],","), ",")));
				$tags_temp = [];
				foreach ($cus_tags as $key => $value) 
				{
					if(trim($value) !== "")
					{
						$tags_temp[] = trim($value);
					}
				}
				$cus_tags = @json_encode($CMS->tags->addTags($tags_temp, "customer"), JSON_UNESCAPED_UNICODE);
				$cus_note = $data[15];
				$cus_total_spend = $data[12];
				$cus_total_order = $data[13];
				$cus_time = time();

				// Check is overwrite
				if($cus_id)
				{
					if(intval($CMS->input['is_overwrite']))
					{
						// Update database
						$DB->query("UPDATE ".root_table."customer SET cus_full_name='{$cus_full_name}', cus_phone='{$cus_phone}', cus_address='{$cus_address}', cus_address2='{$cus_address2}', cus_company='{$cus_company}', cus_city='{$cus_city}', cus_district='{$cus_district}', cus_country='{$cus_country}', cus_postalcode='{$cus_postalcode}', receive_email='{$receive_email}', have_account='{$have_account}', cus_tags='{$cus_tags}', cus_time='{$cus_time}', cus_note='{$cus_note}', cus_total_spend='{$cus_total_spend}', cus_total_order='{$cus_total_order}', cus_code='CUS{$cus_id}' WHERE cus_id='{$cus_id}'");

                        $CMS->class->cache->mdelete($this->cache_prefix);

						$count++;
					}else
					{
						continue;
					}
				}else
				{
			        // insert database
			        $DB->query("INSERT INTO ".root_table."customer (cus_full_name, cus_phone, cus_email,  cus_address, cus_address2, cus_company, cus_city, cus_district, cus_country, cus_postalcode, receive_email, have_account, cus_tags, cus_time, cus_note, cus_total_spend, cus_total_order) VALUES ('{$cus_full_name}', '{$cus_phone}', '{$cus_email}',  '{$cus_address}', '{$cus_address2}', '{$cus_company}', '{$cus_city}', '{$cus_district}', '{$cus_country}', '{$cus_postalcode}', '{$receive_email}', '{$have_account}', '{$cus_tags}', '{$cus_time}', '{$cus_note}', '{$cus_total_spend}', '{$cus_total_order}')");
			        $id = $DB->last_insert_id();
			        $DB->query("UPDATE ".root_table."customer SET cus_code='CUS{$id}' WHERE cus_id='{$id}'");

                    $CMS->class->cache->mdelete($this->cache_prefix);
			        $count++;
		        }
		    }
		}

		if($count)
		{
			$_SESSION['msg'] = $CMS->lang['import_file_success'];
			return true;
		}else
		{
			$_SESSION['error_msg'] = $CMS->lang['import_file_empty'];
			return false;
		}
	}

	function checkExistEmail($email="")
	{
		global $CMS, $DB;

		$sql = "SELECT cus_id FROM ".root_table."customer WHERE cus_email='{$email}' AND cus_deleted=0 LIMIT 1";

		$data = $DB->fetch_data($sql, $this->cache_prefix)[0];

		return $data ? $data['cus_id'] : false;
	}

    function autocomplete()
    {
        global $CMS;

        $this->per_page = 3;
        $data = $this->listing();

        $li = "";

        $return = [
            "status" => "error",
            "msg" => $CMS->lang['data_not_found']
        ];

        if($CMS->class->page->total_row > 0)
        {
            foreach ($data as $cus)
            {
                $li .= "<li><a href='{$CMS->vars['root_domain']}/?site=customer&act=show&id={$cus['cus_id']}'>#{$cus['cus_id']} - {$cus['cus_full_name']}</a></li>";
            }

            if($CMS->class->page->total_row > 3)
            {
                $li .= "<li class='see_more'><a href='{$CMS->vars['root_domain']}/?site=customer&keyword={$CMS->input['keyword']}' >{$CMS->lang['read_more']} ({$CMS->class->page->total_row}) {$CMS->lang['result']}</a></li>";
            }

            $return = [
                "status" => "success",
                "data_option" => $li
            ];
        }

        echo @json_encode($return); exit;
    }
	// 050817
	public function cusCheckExist($cus_email = null) {
		if (! is_null($cus_email)) {
			global $CMS, $DB;
			
			$DB->query("SELECT 0 FROM ".root_table."customer WHERE cus_email = '{$CMS->class->filter->clean_value($cus_email)}' AND cus_deleted=0 ");

			if ($DB->num_rows()) {
				return true;
			}
		}
		return false;
	}
	public function getEmailNotInArray($array_email = null) {
		$reponse = array();
		if (! is_null($array_email)) {
			global $CMS, $DB;
			$array_email = is_array($array_email) ? $array_email : array($array_email);
			
			$DB->query("SELECT * FROM ".root_table."customer WHERE cus_email NOT IN ('" .implode("','", $array_email) . "')");

			if ($DB->num_rows()) {
				while($result = $DB->fetch_array()) {
					$reponse[] = $result;
				}
			}
		}
		return $reponse;
	}


	function createAccount($data=[])
	{
		global $CMS, $DB;

		// Hàm này mục đích chỉ để insert customer và field yêu cầu chính là: cus_full_name, cus_phone. Các thông tin khác có thể khuyết.
		// Clone from web/models/customer

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

	function getCustomerByPhone($cus_phone="")
	{
		global $CMS, $DB;
		// Clone from web/models/customer
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

	/*
	* Get addressbook
	*/
	function getshipbiladdress( $cus_id = 0, $addr_id = 0 )
	{
		global $CMS, $DB;

		// Output
		$output = [];

		// Query
		$clause = $addr_id ? " AND addr_id='{$addr_id}' " : "";
		$limit = $addr_id ? " LIMIT 1 " : "";
		$sql = "
		SELECT * FROM ".root_table."addressbook 
		WHERE cus_id='{$cus_id}' {$clause} AND addr_deleted=0 
		ORDER BY addr_default_billing DESC, addr_default_shipping DESC, addr_time DESC 
		{$limit}
		";

		$cacheData = $DB->fetch_data($sql,'addressbook.customer');
        if( is_array($cacheData) )
        {
        	if( $addr_id )
        	{
        		if( isset($cacheData[0]) )
        		{
        			$output = $this->convertAddressbook($cacheData[0]);
        		}
        	}
        	else
        	{
        		foreach( $cacheData as $data ) 
        		{
        			$output[] = $this->convertAddressbook($data);
        		}
        	}
        }

        return $output;
	}

	function convertAddressbook($data=[])
    {
        global $CMS;

        $data['addr_full_name'] = $data['addr_full_name'] ? $data['addr_full_name'] : $data['addr_first_name'] . '' . $data['addr_last_name'];

        // address
        $data['addr_city_name'] = $CMS->country->nameCity($data['addr_city']);
        $data['addr_city_name'] = $data['addr_city_name'] ? $data['addr_city_name'] : $data['addr_city'];

        $data['addr_district_name'] = $CMS->country->nameDistrict($data['addr_district']);
        $data['addr_district_name'] = $data['addr_district_name'] ? $data['addr_district_name'] : $data['addr_district'];

        $data['addr_province_name'] = $CMS->country->nameState($data['addr_province']);
        $data['addr_province_name'] = $data['addr_province_name'] ? $data['addr_province_name'] : $data['addr_province'];

        $data['addr_country_name'] = $CMS->country->nameCountry($data['addr_country']);
        $data['addr_country_name'] = $data['addr_country_name'] ? $data['addr_country_name'] : $data['addr_country'];
        $data['addr_country_iso_code'] = $CMS->country->getInfoCountry($data['addr_country'], 'country_iso_code');

        $data['addr_address_full'] = $this->generalAddress(array(
            'address'   => $data['addr_address'], 
            'address2'  => $data['addr_address2'], 
            'city'      => $data['addr_city_name'], 
            'district'  => $data['addr_district_name'], 
            'province'  => $data['addr_province_name'], 
            'zipcode'   => $data['addr_zipcode'], 
            'country'   => $data['addr_country_name'], 
        ));

        return $data;
    }

    function generalAddress( $data=[] )
    {
        global $CMS, $DB;

        $output = '';

        if( $data['address'] )
        {
            $output .= $data['address'];
        }

        if( $data['address2'] )
        {
            $output .= ' ' . $data['address2'];
        }

        if( $data['city'] )
        {
            $output .= ' ' . $data['city'];
        }

        if( $data['province'] )
        {
            $output .= ', ' . $data['province'];
        }

        if( $data['zipcode'] )
        {
            $output .= $data['province'] ? '' : ', ';
            $output .= ' ' . $data['zipcode'];
        }

        if( $data['country'] )
        {
            $output .= ', ' . $data['country'];
        }

        return $output;
    }
}
?>