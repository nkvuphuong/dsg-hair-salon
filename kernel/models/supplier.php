<?php
if (!defined('IN_ROOT')) exit();

$CMS->supplier = new supplier1;
class supplier1{
	public $show_page=0;
	public $CMS = "";
	public $sql_query = "";
	public $sql_query_bk = "";
	public $arrange_data = "";
	public $record_cnt = 0;
	public $control = 0;
	public $total = 0;
	public $sql_add = "";
	public $action_control = "";
	public $data_array = array();	 
	public $per_page = 20;
	public $prefix_html = "";
	public $suffix_html = "";
	public $html;

    /**
     * @var string $cache_prefix
     */
	public $cache_prefix = 'supplier';
	

	public function add($data = [])
	{
		global $CMS, $DB, $member;

		if($data)
        {
            $CMS->input = array_merge($CMS->input, $data);
        }

		// Input
		$supplier_name = trim($CMS->input['supplier_name']);
		$supplier_code = trim($CMS->input['supplier_code']);
		$supplier_phone = trim($CMS->input['supplier_phone']);
		$supplier_email = trim($CMS->input['supplier_email']);
		$supplier_taxcode = trim($CMS->input['supplier_taxcode']);
		$supplier_get_invoice = intval($CMS->input['supplier_get_invoice']);
		$supplier_type = intval($CMS->input['supplier_type']);
		$supplier_idcard_number = trim($CMS->input['supplier_idcard_number']);
		$city_id = intval($CMS->input['city_id']);
		$district_id = intval($CMS->input['district_id']);
		$supplier_address = trim($CMS->input['supplier_address']);
		$supplier_bank = trim($CMS->input['supplier_bank']);
		$supplier_branch = trim($CMS->input['supplier_branch']);
		$supplier_bank_number = trim($CMS->input['supplier_bank_number']);
		$supplier_bank_owner = trim($CMS->input['supplier_bank_owner']);
		$supplier_note = $CMS->class->editor->input('supplier_note');
		$supplier_status = intval($CMS->input['supplier_status']);
		
		$user_id = $member['user_id'];
		$supplier_time = time();
		
		if(!$supplier_name)
		{
			$CMS->errormsg = $CMS->lang['supplier_empty_name'];
			return false;
		}
		
		// if(!$supplier_code)
		// {
		// 	$CMS->errormsg = $CMS->lang['supplier_err_code'];
		// 	return false;
		// }
		
		// if(!$supplier_phone)
		// {
		// 	$CMS->errormsg = $CMS->lang['supplier_empty_phone'];
		// 	return false;
		// }
		
		// if(!$supplier_email)
		// {
		// 	$CMS->errormsg = $CMS->lang['supplier_err_email'];
		// 	return false;
		// }
		
		// if(!$city_id)
		// {
		// 	$CMS->errormsg = $CMS->lang['supplier_err_city'];
		// 	return false;
		// }

		// if(!$district_id)
		// {
		// 	$CMS->errormsg = $CMS->lang['supplier_err_district'];
		// 	return false;
		// }
		
		// // Check input
		// if (!$supplier_type) 
		// {
		// 	$CMS->errormsg=$CMS->lang['supplier_empty_type'];
		// 	return false;
		// }
		
		// Check exist name
		if($this->check_exist("supplier_name",$supplier_name))
		{
			$CMS->errormsg=$CMS->lang['supplier_is_exist'];
			return false;
		}
                
                // Check exist code
		if($this->check_exist("supplier_code",$supplier_code) and $supplier_code)
		{
			$CMS->errormsg=$CMS->lang['supplier_code_is_exist'];
			return false;
		}
		
		// Check type
		if($supplier_type == 2)
		{
			$supplier_idcard_number = "";
		}
		
		// Insert data
		$DB->query("INSERT INTO ".root_table."supplier (supplier_name,supplier_code,supplier_phone,supplier_email,supplier_taxcode,supplier_get_invoice,supplier_type,supplier_idcard_number,city_id,district_id,supplier_address,supplier_bank,supplier_branch,supplier_bank_number,supplier_bank_owner,supplier_time,user_id,supplier_note,supplier_status) VALUES ('{$supplier_name}','{$supplier_code}','{$supplier_phone}','{$supplier_email}','{$supplier_taxcode}','{$supplier_get_invoice}','{$supplier_type}','{$supplier_idcard_number}','{$city_id}','{$district_id}','{$supplier_address}','{$supplier_bank}','{$supplier_branch}','{$supplier_bank_number}','{$supplier_bank_owner}','{$supplier_time}','{$user_id}','{$supplier_note}','{$supplier_status}')");

        // Delete cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		$supplier_id = $DB->last_insert_id();
		$CMS->class->logs->key= "supplier_{$supplier_id}";
		$_SESSION['msg']=$CMS->class->logs->insert("{$member['cus_username']} create <b>supplier {$supplier_name}</b>");
                
                return $supplier_id;
	}
	
	public function check_exist( $field, $value = "", $except_value = ""   )
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

		$sql = "SELECT count(0) cnt FROM ".root_table."supplier WHERE {$sql_add} {$field}='{$value}' AND supplier_deleted=0 LIMIT 1";

		$data = $DB->fetch_data($sql, $this->cache_prefix);

		return $data[0]['cnt'];
	}
	
	public function listing() 
	{
		global $CMS,$DB,$member;
		
		
		if (!isset($this->html)) 
		{	
			$this->html = $CMS->class->template->load_template("skin_supplier");
		}
		
		$this->arrange_data = trim("supplier_id,supplier_name,supplier_time,supplier_type,user_id");
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "supplier_id";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "DESC";
		
		$where='';
		if (($CMS->input['sname'])) {
			$where.=" AND `supplier_name` LIKE '%{$CMS->input['sname']}%'";
			$this->prefix_html.="&sname={$CMS->input['sname']}";
		}
		if (($CMS->input['sphone'])) {
			$where.=" AND supplier_phone='{$CMS->input['sphone']}'";
			$this->prefix_html.="&sphone={$CMS->input['sphone']}";
		}
		if (($CMS->input['city_id'])) {
			$where.=" AND city_id='{$CMS->input['city_id']}'";
			$this->prefix_html.="&sphone={$CMS->input['sphone']}";
		}
		if (($CMS->input['district_id'])) {
			$where.=" AND district_id='{$CMS->input['district_id']}'";
			$this->prefix_html.="&district_id={$CMS->input['district_id']}";
		}
		if (($CMS->input['user_name'])) {
			$sql_table = " as S left join nh_user as U ON S.user_id=U.user_id ";
			$where.=" AND user_display_name LIKE '%{$CMS->input['user_name']}%'";
			$this->prefix_html.="&user_name={$CMS->input['user_name']}";
		}
		if ($CMS->input['status']!=NULL) {
			$CMS->input['status'] = intval($CMS->input['status']);
			$where.=" AND supplier_status ='{$CMS->input['status']}'";
			$this->prefix_html.="&status='{$CMS->input['status']}'";
		}
		
		$this->prefix_html=empty($this->prefix_html)?'':'?site=supplier'.$this->prefix_html.'&page=';

		$sql = "SELECT * FROM `".root_table."supplier` {$sql_table} WHERE `supplier_deleted`=0 {$where} ORDER BY {$default_field} {$default_order}";

		list($CMS->show_page, $results) = $DB->fetch_listing($sql, $this->per_page, $this->prefix_html, $this->suffix_html, $CMS->input['page'], $this->cache_prefix);

		$count = count($results);
		if ( $count > 0 ) 
		{
			$cnt = 1;
			foreach ($results as $data)
			{
				$data = $this->convertvalue($data);
				$data['keyrow'] = $cnt;
				$data['rowtr'] = $cnt == $count ? 'last-row' : '';
				$cnt += 1;
				$output .= $this->html->mid($data);
			}
		} 
		else 
		{
			$output .= $this->html->none();
		}
		
		return $output;
	}
	public function delete()
	{
		global $CMS, $DB;
		
		// Get info
		$data = $this->get_info();
		
		// Check existing
		if ( ! $data ) { return false; }
		
		// Update info
		$DB->query("UPDATE ".root_table."supplier SET supplier_deleted=1 WHERE supplier_id={$data['supplier_id']}");

        // Delete cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		// Create log
		$CMS->class->logs->key = "supplier_{$data['supplier_id']}";
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['supplier_deleted']} <b>{$data['supplier_name']}</b>");
                
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=supplier&page={$CMS->input['page']}");
		
		return true;
	}
	public function edit() 
	{
		global $CMS, $DB, $member;
		
		$supplier = $this->get_info();
		
		// Input
		$supplier_name = trim($CMS->input['supplier_name']);
		$supplier_code = trim($CMS->input['supplier_code']);
		$supplier_phone = trim($CMS->input['supplier_phone']);
		$supplier_email = trim($CMS->input['supplier_email']);
		$supplier_taxcode = trim($CMS->input['supplier_taxcode']);
		$supplier_get_invoice = intval($CMS->input['supplier_get_invoice']);
		$supplier_type = intval($CMS->input['supplier_type']);
		$supplier_idcard_number = trim($CMS->input['supplier_idcard_number']);
		$city_id = intval($CMS->input['city_id']);
		$district_id = intval($CMS->input['district_id']);
		$supplier_address = trim($CMS->input['supplier_address']);
		$supplier_bank = trim($CMS->input['supplier_bank']);
		$supplier_branch = trim($CMS->input['supplier_branch']);
		$supplier_bank_number = trim($CMS->input['supplier_bank_number']);
		$supplier_bank_owner = trim($CMS->input['supplier_bank_owner']);
		$supplier_note = $CMS->class->editor->input('supplier_note');
		$supplier_status = intval($CMS->input['supplier_status']);
		
		if(!$supplier_name)
		{
			$CMS->errormsg = $CMS->lang['supplier_empty_name'];
			return false;
		}
		
		// if(!$supplier_code)
		// {
		// 	$CMS->errormsg = $CMS->lang['supplier_err_code'];
		// 	return false;
		// }
		
		// if(!$supplier_phone)
		// {
		// 	$CMS->errormsg = $CMS->lang['supplier_empty_phone'];
		// 	return false;
		// }
		
		// if(!$supplier_email)
		// {
		// 	$CMS->errormsg = $CMS->lang['supplier_err_email'];
		// 	return false;
		// }
		
		// if(!$city_id)
		// {
		// 	$CMS->errormsg = $CMS->lang['supplier_err_city'];
		// 	return false;
		// }

		// if(!$district_id)
		// {
		// 	$CMS->errormsg = $CMS->lang['supplier_err_district'];
		// 	return false;
		// }
		
		// // Check input
		// if (!$supplier_type) 
		// {
		// 	$CMS->errormsg=$CMS->lang['supplier_empty_type'];
		// 	return false;
		// }
		
		// Check exist name
		if($this->check_exist("supplier_name",$supplier_name,$supplier['supplier_name']))
		{
			$CMS->errormsg=$CMS->lang['supplier_is_exist'];
			return false;
		}
                
                // Check exist code
		if($this->check_exist("supplier_code",$supplier_code,$supplier['supplier_code'] ) and $supplier_code)
		{
			$CMS->errormsg=$CMS->lang['supplier_code_is_exist'];
			return false;
		}
		
		// Check type
		if($supplier_type == 2)
		{
			$supplier_idcard_number = "";
		}
		
		// Insert data
		$DB->query("UPDATE ".root_table."supplier SET supplier_name='{$supplier_name}',supplier_code='{$supplier_code}',supplier_phone='{$supplier_phone}',supplier_email='{$supplier_email}',supplier_taxcode='{$supplier_taxcode}',supplier_get_invoice='{$supplier_get_invoice}',supplier_type='{$supplier_type}',supplier_idcard_number='{$supplier_idcard_number}',city_id='{$city_id}',district_id='{$district_id}',supplier_address='{$supplier_address}',supplier_bank='{$supplier_bank}',supplier_branch='{$supplier_branch}',supplier_bank_number='{$supplier_bank_number}',supplier_bank_owner='{$supplier_bank_owner}',supplier_note='{$supplier_note}',supplier_status='{$supplier_status}' where supplier_id='{$supplier['supplier_id']}'");

        // Delete cache
        $CMS->class->cache->mdelete($this->cache_prefix);
		
		$supplier_id = $DB->last_insert_id();
		$CMS->class->logs->key= "supplier_{$supplier_id}";
		$_SESSION['msg']=$CMS->class->logs->insert("{$member['cus_username']} edited <b>supplier {$supplier_name}</b>");

        return TRUE;
	}
	
	public function auto_run() {
		global $CMS, $DB, $member;
		
		if (!isset($this->html)) {	
			$this->html = $CMS->class->template->load_template("skin_supplier");
		}
		
		if ($CMS->class->cache->check("user_{$member['user_id']}_supplier_{$CMS->vars['default_language']}")) {
			$CMS->vars['action_controller']=$CMS->class->cache->load("user_{$member['user_id']}_supplier_{$CMS->vars['default_language']}");
		} else {
			$data = "";
			if ($CMS->permit["supplier_search"]) {
				$data .=<<<EOF
<div class="tbl-cell tbl-cell-action-bordered" >
	<a href="{$CMS->vars['root_domain']}/?site=supplier&act=search" title="{$CMS->lang['title_search_supplier']}">
	<button type="button" class="action-btn"><i class="fa fa-search"></i></button>
  </a>
</div>
EOF;
				
				$this->control = 1;
			}
			if ($CMS->permit["pcategory_arrange"]) {
				$data .=<<<EOF
<div class="tbl-cell tbl-cell-action-bordered" act="arrange" id="glyphicon-sort" >
	<i class="fa fa-refresh" title="{$CMS->lang['title_arrange_supplier']}"></i>
</div>
EOF;
				$this->control = 1;
			}
			if ($CMS->permit["pcategory_delete"]) {
				$data .=<<<EOF
<div class="tbl-cell tbl-cell-action-bordered" act="delete_all" id="font-icon-trash">
	<i class="fa fa-trash-o" title="{$CMS->lang['title_delete_all_supplier']}"></i>
</div>
EOF;
				$this->control = 1;
			}
			if ($CMS->permit["pcategory_add"]) {
				$data .=<<<EOF
<div class="tbl-cell tbl-cell-action-bordered">
	<a href="{$CMS->vars['root_domain']}/?site=supplier&act=add" title="{$CMS->lang['title_add_pcategory']}">
		<button type="button" class="action-btn"><i class="fa fa-plus-circle"></i></button>
	</a>
</div>
EOF;
				$this->control = 1;
			}
			$data = $this->control == 1 ?  $data : "";
			$CMS->class->cache->save("user_{$member['user_id']}_supplier_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}
		
		$this->action_control = $CMS->vars['action_controller'];
	}

	public function search(){
		global $CMS, $DB, $member;
		$str='';
		if ($CMS->input['sname']) {
			$str.='&sname='.trim($CMS->input['sname']);
		}
		if (trim($CMS->input['sphone'])) {
			$str.='&sphone='.trim($CMS->input['sphone']);
		}
		if (($CMS->input['city_id'])) {
			$str.='&city_id='.($CMS->input['city_id']);
		}
		if (($CMS->input['district_id'])) {
			$str.='&district_id='.($CMS->input['district_id']);
		}
		if ($CMS->input['user_name']) {
			$str.='&user_name='.trim($CMS->input['user_name']);
		}
		if ($CMS->input['status']!=NULL) {
			$str.='&status='.trim($CMS->input['status']);
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=supplier{$str}");
	}

	public function get_info( $record_id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "supplier" )
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
		
		$sql = "SELECT * FROM ".root_table."supplier WHERE (supplier_id='{$record_id}' OR supplier_name='{$record_id}') AND supplier_deleted = 0 ORDER BY supplier_id DESC LIMIT 1";

		$data = $DB->fetch_data($sql, $this->cache_prefix)[0];

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

	public function convertvalue($data) 
	{
		global $CMS;
		
		$data['supplier_type'] = $CMS->lang["supplier_type_{$data['supplier_type']}"];
		$data['user_name'] = $CMS->user->get_info($data['user_id'],"user_name");
		$data['supplier_time'] = $CMS->class->date->date_format($data['supplier_time'],1);
		
		return $data;
	}

	public function action($id=null) {
		if (!is_null($id)) {
			global $CMS, $DB, $member;
			$DB->query("SELECT `supplier_act`, supplier_title FROM `".root_table."supplier` WHERE `cus_id`={$member['cus_id']} AND `supplier_id`={$id}");
			$data = $DB->fetch_array();
			if ($data['supplier_act']==2) {
				return FALSE;
			}
			$count = $DB->query("UPDATE `".root_table."supplier` SET `{$CMS->input['action']}`='{$CMS->input['value']}' WHERE `cus_id`='{$member['cus_id']}' AND `supplier_id` = {$id}");
			if($count)
			{
				if($CMS->input['action'] == "supplier_deleted")
				{
					$_SESSION['msg'] = $CMS->lang['emsg_deleted_success_supplier']. $data['supplier_title'];
				}else
				{
					$_SESSION['msg'] = $CMS->lang['emsg_update_success_supplier']. $data['supplier_title'];
				}
			}else
			{
				$_SESSION['msg'] = $CMS->lang['emsg_update_error_supplier']. $data['supplier_title'];
			}
			return true;
		}
		return false;
	}
	
	public function get_list_supplier($type = 0, $id_select =0, $disable_title=0)
	{
		global $CMS, $DB;
	 
		$CMS->class->language->load("store");
		
		$sql = "SELECT * FROM ".root_table."supplier WHERE supplier_deleted=0 AND supplier_status=1 ORDER BY supplier_name ASC";

		$results = $DB->fetch_data($sql, $this->cache_prefix);

		$output = $disable_title ? "" : "<option value=''>{$CMS->lang['select_supplier']}</option>";	
		
		foreach($results as $data)
		{
			if($type == 1)
			{
				$output_data[] = $data;
			}
			else
			{
				$selected = "";
				if($id_select == $data['supplier_id'])
				{
					$selected = " selected = 'selected' ";
				}

				$output .= "<option {$selected} value='{$data['supplier_id']}' email='{$data['supplier_email']}' address='{$data['supplier_address']}'>{$data['supplier_name']}</option>";
			}
			
		}

		if($type == 1)
		{
			return $output_data;
		}
		else
		{
			return $output;
		}
	}

	public function addAjax()
	{
		global $CMS, $DB, $member;

		// Input
		$supplier_name = trim($CMS->input['supplier_name']);
		$supplier_code = trim($CMS->input['supplier_code']);
		$supplier_phone = trim($CMS->input['supplier_phone']);
		$supplier_email = trim($CMS->input['supplier_email']);
		$supplier_taxcode = trim($CMS->input['supplier_taxcode']);
		$supplier_get_invoice = intval($CMS->input['supplier_get_invoice']);
		$supplier_type = intval($CMS->input['supplier_type']);
		$supplier_idcard_number = trim($CMS->input['supplier_idcard_number']);
		$city_id = intval($CMS->input['city_id']);
		$district_id = intval($CMS->input['district_id']);
		$supplier_address = trim($CMS->input['supplier_address']);
		$supplier_bank = trim($CMS->input['supplier_bank']);
		$supplier_branch = trim($CMS->input['supplier_branch']);
		$supplier_bank_number = trim($CMS->input['supplier_bank_number']);
		$supplier_bank_owner = trim($CMS->input['supplier_bank_owner']);
		$supplier_note = $CMS->class->editor->input('supplier_note');
		$supplier_status = 1;//intval($CMS->input['supplier_status']);
		
		$user_id = $member['user_id'];
		$supplier_time = time();
		
		if(!$supplier_name)
		{
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['supplier_empty_name']}"));
			exit;
		}
		
		// if(!$supplier_code)
		// {
		// 	print json_encode(array("status" => "error", "msg" => "{$CMS->lang['supplier_err_code']}"));
		// 	exit;
		// }
		
		// if(!$supplier_phone)
		// {
		// 	print json_encode(array("status" => "error", "msg" => "{$CMS->lang['supplier_empty_phone']}"));
		// 	exit;
		// }
		
		// if(!$supplier_email)
		// {
		// 	print json_encode(array("status" => "error", "msg" => "{$CMS->lang['supplier_err_email']}"));
		// 	exit;
		// }
		
		// if(!$city_id)
		// {
		// 	print json_encode(array("status" => "error", "msg" => "{$CMS->lang['supplier_err_city']}"));
		// 	exit;
		// }

		// if(!$district_id)
		// {
		// 	print json_encode(array("status" => "error", "msg" => "{$CMS->lang['supplier_err_district']}"));
		// 	exit;
		// }
		
		// // Check input
		// if (!$supplier_type) 
		// {
		// 	print json_encode(array("status" => "error", "msg" => "{$CMS->lang['supplier_empty_type']}"));
		// 	exit;
		// }
		
		// Check exist name
		if($this->check_exist("supplier_name",$supplier_name))
		{
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['supplier_is_exist']}"));
			exit;
		}
                
                // Check exist code
		if($this->check_exist("supplier_code",$supplier_code) and $supplier_code)
		{
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['supplier_code_is_exist']}"));
			exit;
		}
		
		// Check type
		if($supplier_type == 2)
		{
			$supplier_idcard_number = "";
		}
		
		// Insert data
		$count = $DB->query("INSERT INTO ".root_table."supplier (supplier_name,supplier_code,supplier_phone,supplier_email,supplier_taxcode,supplier_get_invoice,supplier_type,supplier_idcard_number,city_id,district_id,supplier_address,supplier_bank,supplier_branch,supplier_bank_number,supplier_bank_owner,supplier_time,user_id,supplier_note,supplier_status) VALUES ('{$supplier_name}','{$supplier_code}','{$supplier_phone}','{$supplier_email}','{$supplier_taxcode}','{$supplier_get_invoice}','{$supplier_type}','{$supplier_idcard_number}','{$city_id}','{$district_id}','{$supplier_address}','{$supplier_bank}','{$supplier_branch}','{$supplier_bank_number}','{$supplier_bank_owner}','{$supplier_time}','{$user_id}','{$supplier_note}','{$supplier_status}')");

		$CMS->class->cache->mdelete($this->cache_prefix);

		if($count)
		{
			$supplier_id = $DB->last_insert_id();
			$CMS->class->logs->key= "supplier_{$supplier_id}";
			$CMS->class->logs->insert("{$member['cus_username']} create <b>supplier {$supplier_name}</b>");

	        return $supplier_id;
		}else
		{
			return false;
		}
                
	}

	public function editAjax()
	{
		global $CMS, $DB, $member;

		// Input
		$supplier_id = intval($CMS->input['supplier_id']);
		$supplier_name = trim($CMS->input['supplier_name']);
		$supplier_code = trim($CMS->input['supplier_code']);
		$supplier_phone = trim($CMS->input['supplier_phone']);
		$supplier_email = trim($CMS->input['supplier_email']);
		$supplier_taxcode = trim($CMS->input['supplier_taxcode']);
		$supplier_get_invoice = intval($CMS->input['supplier_get_invoice']);
		$supplier_type = intval($CMS->input['supplier_type']);
		$supplier_idcard_number = trim($CMS->input['supplier_idcard_number']);
		$city_id = intval($CMS->input['city_id']);
		$district_id = intval($CMS->input['district_id']);
		$supplier_address = trim($CMS->input['supplier_address']);
		$supplier_bank = trim($CMS->input['supplier_bank']);
		$supplier_branch = trim($CMS->input['supplier_branch']);
		$supplier_bank_number = trim($CMS->input['supplier_bank_number']);
		$supplier_bank_owner = trim($CMS->input['supplier_bank_owner']);
		$supplier_note = $CMS->class->editor->input('supplier_note');
		$supplier_status = 1;//intval($CMS->input['supplier_status']);
		
		$user_id = $member['user_id'];
		$supplier_time = time();
		$data = $this->get_info($supplier_id);

		 
		$CMS->class->logs->key= "supplier_{$supplier_id}";
		$CMS->class->logs->old_data = $data;
                
                
		if(!$supplier_name)
		{
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['supplier_empty_name']}"));
			exit;
		}
		
		// Check exist name
		if($this->check_exist("supplier_name",$supplier_name, $data['supplier_name']))
		{
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['supplier_is_exist']}"));
			exit;
		}
                
                
                
                // Check exist code
		if($supplier_code !=  $data['supplier_code'])
		{
 		//print json_encode(array("status" => "error", "msg" => "{$CMS->lang['supplier_code_is_exist']}fff{$supplier_code} -- đ {$data} "));
				//	exit;
				if($this->check_exist("supplier_code",$supplier_code, $data['supplier_code'] ) and $supplier_code)
				{
					print json_encode(array("status" => "error", "msg" => "{$CMS->lang['supplier_code_is_exist']}"));
					exit;
				}
		}
	
                
		
		// Check type
		if($supplier_type == 2)
		{
			$supplier_idcard_number = "";
		}
		// Insert data
		$count = $DB->query("UPDATE ".root_table."supplier SET supplier_name = '{$supplier_name}',supplier_code = '{$supplier_code}',supplier_phone = '{$supplier_phone}',supplier_email = '{$supplier_email}',supplier_taxcode = '{$supplier_taxcode}',supplier_get_invoice = '{$supplier_get_invoice}',supplier_type = '{$supplier_type}',supplier_idcard_number = '{$supplier_idcard_number}',city_id = '{$city_id}',district_id = '{$district_id}',supplier_address = '{$supplier_address}',supplier_bank = '{$supplier_bank}',supplier_branch = '{$supplier_branch}',supplier_bank_number = '{$supplier_bank_number}',supplier_bank_owner = '{$supplier_bank_owner}',supplier_time_update = '{$supplier_time}',user_id = '{$user_id}',supplier_note = '{$supplier_note}' WHERE supplier_id = '{$supplier_id}'");

        $CMS->class->cache->mdelete($this->cache_prefix);
                
		if($count)
		{
			$data_new = $this->get_info($supplier_id);
			$CMS->class->logs->key= "supplier_{$supplier_id}";
			$CMS->class->logs->save_detail("supplier",$supplier_id, $data_new);
            return $data_new;
                
		}
                else
		{
			return false;
		}
                
	}
	

	public function getOptionSupplier($supplier_id=0)
	{
		global $CMS, $DB;

		$output = "<option value>{$CMS->lang['select_supplier']}</option>";
		$sql = "SELECT * FROM ".root_table."supplier WHERE supplier_deleted = 0 AND supplier_status = 1 ORDER BY supplier_name ASC";

		$results =  $DB->fetch_data($sql, $this->cache_prefix);

		if($results)
		{
			foreach ($results as $result)
			{
				if($supplier_id == $result['supplier_id'])
				{
					$selected = "selected='selected'";
				}else
				{
					$selected = "";
				}
				$output .= "<option value='{$result['supplier_id']}' email='{$result['supplier_email']}' address='{$result['supplier_address']}' {$selected}>{$result['supplier_name']}</option>";
			}
		}

		return $output;
	}
	
}
?>