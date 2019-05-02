<?php

use \lib\input;

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->inventory=new ClassInventory;
class ClassInventory {
	public $per_page = 20;
	public $show_page = '';
	public $sql_query = '';
	public $arrange_data = '';
	public $prefix_html = "";
	public $suffix_html = "";

	public function listing() {
		global $CMS, $DB, $member;

		$search_type = intval(\lib\input::get('search_type'));

		$this->arrange_data = trim("inventory_id,inventory_name,inventory_code,user_id,inventory_time");
	 
		$this->arrange_data = trim("inventory_id,inventory_name,inventory_code,user_id,inventory_time");

		$order = \lib\input::get('order');
		$by = \lib\input::get('by');

		// Accepted keywords
		$list_field = array("inventory_id", "inventory_name", "inventory_code", "user_id", "inventory_time", "inventory_status");
		$list_by = array("desc","asc");
		// Filter them
		$default_field = in_array($order, $list_field) ? $order : $list_field[0];
		$default_order = in_array($by, $list_by) ? $by : $list_by[0];

		$where = $where_1 = '';		
		if (!empty($CMS->input['ini_name'])) {
			$ini_name = urldecode($CMS->input['ini_name']);
			$where_1 .= " AND  I.ini_name  LIKE '%{$ini_name}%' ";
		}
		if (!empty($CMS->input['ini_amount'])) {
	 		if($CMS->input['ini_amount'] == 1)
	 		{
	 			$where_1 .= " AND `I.ini_amount` = '0' ";
	 		}
	 		elseif($CMS->input['ini_amount'] == 2)
	 		{
	 			$where_1 .= " AND `I.ini_amount` > '0' ";
	 		}
			elseif($CMS->input['ini_amount'] == 3)
	 		{
	 			$where_1 .= " AND `I.ini_amount` < '0' ";
	 		}
		}


		if (!empty($CMS->input['in_name'])) {
			$in_name = urldecode($CMS->input['in_name']);
			$where .= " AND `inventory_name` = '{$in_name}' ";
		}
		if (!empty($CMS->input['in_type'])) {
			$where .= " AND `inventory_type` = '{$CMS->input['in_type']}' ";
		}
		if (!empty($CMS->input['store_id'])) {
			$where .= " AND `store_id` = '{$CMS->input['store_id']}' ";
		}
		if (!empty($CMS->input['user_id'])) {
			$where .= " AND `user_id` = '{$CMS->input['user_id']}' ";
		}
		if (!empty($CMS->input['in_balance'])) {
			$where .= " AND `is_balance` = '{$CMS->input['in_balance']}' ";
		}
		if (!empty($CMS->input['time_from'])) {
			$where .= " AND `inventory_time` >= '{$CMS->input['time_from']}' ";
		}
		if (!empty($CMS->input['time_to'])) {
			$where .= " AND `inventory_time` <= '{$CMS->input['time_to']}' ";
		}
 
		if($search_type == 1) // San phjam kiem salon
		{

			list($this->show_page, $this->sql_query) = $CMS->class->page->create("SELECT I.*, IT.*,  SUM(I.ini_quantity) as quantity, SUM(I.ini_check) as thucte FROM `".root_table."inventory_item` as I , ".root_table."inventory as IT  WHERE I.inventory_id = IT.inventory_id AND I.ini_deleted = 0  {$where_1} GROUP BY I.inventory_id,I.ass_key ORDER BY I.inventory_id DESC ",$this->per_page,$this->prefix_html,$this->suffix_html);
		}
		else
		{
			list($this->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM `".root_table."inventory` WHERE `inventory_deleted`= 0 {$where}  ORDER BY {$default_field} {$default_order}",$this->per_page,$this->prefix_html,$this->suffix_html);
		}

		


		$arr = array();
		if ($DB->num_rows($this->sql_query)>0) {
			while ($result = $DB->fetch_array($this->sql_query)) {
				array_push($arr,$this->convertvalue($result,$search_type));
			}
		}

		return $arr;
	}
	public function convertvalue($data=null, $type = 0) {
		global $CMS, $DB, $member;

		$data['inventory_time_bk'] = isset($data['inventory_time']) ? $CMS->class->date->date_format($data['inventory_time']) : "";
		if($type == 1)
		{
			$inventory = $this->get_info($data['inventory_id']);
		}

		if($type == 0)
		{
			if($CMS->permit['inventory_read'] == true)
			{
				$data['inventory_name_bk'] =  isset($data['inventory_name']) ? "<a href='{$CMS->vars['root_domain']}/?site=inventory&act=show&id={$data['inventory_id']}'>{$data['inventory_name']}</a>" : "";
			}
			else
			{
				$data['inventory_name_bk'] =  $data['inventory_name'];
			}
		}
		else
		{
			 
			if($CMS->permit['inventory_read'] == true)
			{
				$data['inventory_name_bk'] =  "<a href='{$CMS->vars['root_domain']}/?site=inventory&act=show&id={$data['inventory_id']}'>{$inventory['inventory_name']}</a>";
			}
			else
			{
				$data['inventory_name_bk'] =  $inventory['inventory_name'];
			}
		}
 
		$data['store_name'] = $CMS->store->get_info($data['store_id'],"store_name");
 
		if(isset($data['is_balance']) && $data['is_balance'] == 0)//Chưa tao phieu bu tru
		{
			$data['act_balance'] = "<a class=\"dropdown-item\" href='{$CMS->vars['root_domain']}/?site=inventory&act=approve&id={$data['inventory_id']}'><i class=\"fa fa-file-text-o\"></i> {$CMS->lang['btn_approve']}</a>";
 
		}
		else
		{
			$data['inventory_time_update_bk'] = isset($data['inventory_time_update']) ? $CMS->class->date->date_format($data['inventory_time_update']) : "";
			 $data['is_balance_bk'] = "<span class='span-success'><i class='fa fa-file-text-o'></i> {$CMS->lang['in_voucher_created']}</span><p>{$data['inventory_time_update_bk']}</p> ";

		}
		if(!empty($data['ass_key']))
		{
			$assets = $CMS->assets->get_info($data['ass_key']);
			$data['assets'] = $assets;
		}
 		// Count inventory_item
 		if($type == 0)
		{
	 		list($cnt_item, $ton_item, $thucte_item) = $this->count_in_item(isset($data['inventory_id']) ? $data['inventory_id'] : 0);
	 		$data['ini_count'] = $cnt_item;
	 		$data['ini_ton'] = $ton_item;
	 		$data['ini_check'] = $thucte_item;
	 		$data['ini_balance'] = $data['ini_check'] - $data['ini_ton'];
	 	}
	 	else
	 	{
	 		$data['ini_ton'] = $data['quantity'];
	 		$data['ini_check'] = $data['thucte'];
	 		$data['ini_balance'] = $data['ini_check'] - $data['ini_ton'];
	 	}

	 // 	if($data['sr_export']  > 0 AND $data['sr_import'] > 0)
		// {

		// 	//tao phieu xuat nhap thanh cong
		// 	$data['ref_store_request'] ="Mã phiếu xuất/nhập: <a href='{$CMS->vars['root_domain']}/?site=store_request&stage=request_ei&act=show&id={$data['sr_export']}'>REQ{$data['sr_export']}</a>, <a href='{$CMS->vars['root_domain']}/?site=store_request&stage=request_ei&act=show&id={$data['sr_import']}'>REQ{$data['sr_import']}</a> ";
		// }
		// elseif($data['sr_export']> 0 AND $data['sr_import'] == 0) 
		// {
		// 	//tao phieu xuat nhap thanh cong
		// 	$data['ref_store_request']  ="Mã phiếu xuất/nhập: <a href='{$CMS->vars['root_domain']}/?site=store_request&stage=request_ei&act=show&id={$data['sr_export']}'>REQ{$data['sr_export']}</a>  ";
		// }elseif($data['sr_export'] == 0 AND $data['sr_import'] > 0) 
		// {
		// 	//tao phieu xuat nhap thanh cong
		// 	$data['ref_store_request']  ="Mã phiếu xuất/nhập:  <a href='{$CMS->vars['root_domain']}/?site=store_request&stage=request_ei&act=show&id={$data['sr_import']}'>REQ{$data['sr_import']}</a>  ";
		// }
	 	$data['ref_store_request'] = "";
	 	if(isset($data['sr_export']) && $data['sr_export']  > 0)
	 	{
	 		$data_ex = $CMS->store_request->get_info($data['sr_export']);
	 		if($data_ex['request_stage'] == 2)
	 		{
	 			$stage = "request_ei";
	 			$status_rq = "Đang chờ";
	 		}elseif($data_ex['request_stage'] == 3)
	 		{
	 			$stage = "request_eis";
	 			$status_rq = "Đã hoàn thành";
	 		}
	 		$data['ref_store_request'] .= "{$CMS->lang['title_code_request_export']}: <a href='{$CMS->vars['root_domain']}/?site=store_request&stage={$stage}&act=show&id={$data['sr_export']}' data-toggle='tooltip' data-placement='bottom' title='{$status_rq}'>{$data_ex['request_code']}</a><br/>";
	 	}

	 	if(isset($data['sr_import']) && $data['sr_import']  > 0)
	 	{
	 		$data_im = $CMS->store_request->get_info($data['sr_import']);
	 		if($data_im['request_stage'] == 2)
	 		{
	 			$stage = "request_ei";
	 			$status_rq = "{$CMS->lang['title_status_0']}";
	 		}elseif($data_im['request_stage'] == 3)
	 		{
	 			$stage = "request_eis";
	 			$status_rq = "{$CMS->lang['title_status_1']}";
	 		}
	 		$data['ref_store_request'] .= "{$CMS->lang['title_code_request_import']}: <a href='{$CMS->vars['root_domain']}/?site=store_request&stage={$stage}&act=show&id={$data['sr_import']}' data-toggle='tooltip' data-placement='bottom' title='{$status_rq}'>{$data_im['request_code']}</a><br/>";
	 	}

		// check loại kiểm salon theo danh mục
		if($data['inventory_type'] == 2 AND $data['inventory_pgroup'] != "")
		{
			$arr_inventory_pgroup = explode( ",",$data['inventory_pgroup']);
			 
			if(count($arr_inventory_pgroup) > 0)
			{
				foreach ($arr_inventory_pgroup as $key => $value) {
					# code...
					if($value != "")
					{
						$pgroup = $CMS->product_group->getInfo($value);
						$data['inventory_pgroup_bk'] .= $pgroup['product_group_name'].", ";

					}
				}
			}
		}

		$data['inventory_time_c'] = isset($data['inventory_time']) ? $CMS->class->date->date_format($data['inventory_time'],1) : "";
		$data['user_id_c'] = isset($data['user_id']) ? $CMS->user->get_info($data['user_id'], 'user_name') : "";

		$data['inventory_type_bk'] = $CMS->lang["inventory_type_{$data['inventory_type']}"];

  
		return $data;
	}
	public function get_info($record_id = null,$field_name = '*') {
		if (!empty($record_id)) {
			global $CMS, $DB, $member;
			$DB->query("SELECT {$field_name} FROM `".root_table."inventory` WHERE `inventory_id`='{$record_id}' AND `inventory_deleted`=0 LIMIT 1");
			if ($DB->num_rows() > 0) {
				$data = $DB->fetch_array();
				if ($field_name !== '*') {
					return $data[$field_name];
				}
				return $data;
			}
		}
		return false;
	}
	 
	public function get_info_item($record_id = null,$field_name = '*') {
		if (!empty($record_id)) {
			global $CMS, $DB, $member;
			$DB->query("SELECT {$field_name} FROM `".root_table."inventory_item` WHERE `ini_id`='{$record_id}' AND `ini_deleted`=0 LIMIT 1");
			if ($DB->num_rows() > 0) {
				$data = $DB->fetch_array();
				if ($field_name !== '*') {
					return $data[$field_name];
				}
				return $data;
			}
		}
		return false;
	}
	      


	

	public function add( ) {
		global $CMS, $DB, $member;
 
	
		if(isset($_SESSION['add_inventory']) AND count($_SESSION['add_inventory']) > 0)
		{
			$store_id = intval($_SESSION['add_inventory']['store_id']);
			$inventory_type = intval($_SESSION['add_inventory']['inventory_type']);
			$inventory_pgroup =  $_SESSION['add_inventory']['inventory_pgroup'];
			$inventory_note = $CMS->class->editor->input($_SESSION['add_inventory']['inventory_note'],"text");

		}
 
		if($store_id == 0 OR empty($store_id))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['in_store_err']}";
			return false;
		}

		if($inventory_type == 0 OR empty($inventory_type))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['inventory_type_err']}";
			return false;
		}
 
		if($inventory_type == 2  AND count($inventory_pgroup) <= 0 )
		{ 
			$_SESSION['error_msg'] = "{$CMS->lang['inventory_pgroup_err']}";
			return false;
		}
 
		//Convert array pgroup to string
		$inventory_pgroup_str = "";
		if(count($inventory_pgroup) > 0)
		{
			foreach ($inventory_pgroup as $key => $value) {
				# code...
				if($value != "")
				{
					$inventory_pgroup_str .= ",".$value.",";
				}
			}
		}
 
 		$time = time();
		$sql = $DB->query("INSERT INTO `".root_table."inventory` (`inventory_time`, `user_id`,   `inventory_type`,   `store_id`, `inventory_pgroup`, `inventory_note`) VALUES ('".time()."','{$member['user_id']}', '{$inventory_type}','{$store_id}','{$inventory_pgroup_str}','{$inventory_note}')");
		$id = $DB->last_insert_id();
 		
 		$inventory_name = "INT".$id;

 		$DB->query("UPDATE `".root_table."inventory` SET `inventory_name`='{$inventory_name}' WHERE `inventory_id`= '{$id}'");

 		// addproduct_do
 		if($this->addproduct_do($id) == true)
 		{
 			unset($_SESSION['add_inventory']);
 		}
 		else
 		{
 			return false;
 		}

 		// Tao phieu xuat nhap bu tru
 		$is_balance = intval($CMS->input['is_balance']);
 		if($is_balance == 1)
 		{
 			$inven = $this->get_info($id);
 			$approve_return = $this->approve($inven);
 			if($approve_return['status'] == true)
 			{
 			 
	 			if($approve_return['data_output'][0] > 0 AND $approve_return['data_output'][1] > 0)
	 			{
	 				//tao phieu xuat nhap thanh cong
	 				$msg =", Mã phiếu xuất/nhập: <a href='{$CMS->vars['root_domain']}/?site=store_request&stage=request_ei&act=show&id={$approve_return['data_output'][0]}'>REQ{$approve_return['data_output'][0]}</a>, <a href='{$CMS->vars['root_domain']}/?site=store_request&stage=request_ei&act=show&id={$approve_return['data_output'][1]}'>REQ{$approve_return['data_output'][1]}</a> ";
	 			}
	 			elseif($approve_return['data_output'][0]> 0 AND $approve_return['data_output'][1] == 0) 
	 			{
	 				//tao phieu xuat nhap thanh cong
	 				$msg =", Mã phiếu xuất/nhập: <a href='{$CMS->vars['root_domain']}/?site=store_request&stage=request_ei&act=show&id={$approve_return['data_output'][0]}'>REQ{$approve_return['data_output'][0]}</a>  ";
	 			}elseif($approve_return['data_output']['request_id'] == 0 AND $approve_return['data_output']['request_im_id'] > 0) 
	 			{
	 				//tao phieu xuat nhap thanh cong
	 				$msg =", Mã phiếu xuất/nhập: <a href='{$CMS->vars['root_domain']}/?site=store_request&stage=request_ei&act=show&id={$approve_return['data_output'][1]}'>REQ{$approve_return['data_output'][1]}</a>  ";
	 			}
 			}
 			
 		} 
 		$CMS->class->logs->key= "inventory_{$id}";
 		$_SESSION['msg'] = "{$CMS->lang['inventory_add_success']} {$msg}";


		$CMS->class->logs->insert("{$CMS->lang['in_add_title']} #{$id}");
		 
		$inv = $this->get_info($id);	

		return $inv;
	}
	 
	public function edit() {
 		global $CMS, $DB, $member;
		
 		$in = $this->get_info($CMS->input['id']);

 		if(!is_array($in))
 		{
 			$_SESSION['error_msg'] = "{$CMS->lang['no_data_inventory']}";
 			return false;
 		}

		$key = "inventory_{$in['inventory_id']}";
		$CMS->class->logs->key = $key;
		$CMS->class->logs->old_data = $data_info;
		$CMS->class->logs->insert($key);


		$store_id = intval($_SESSION['edit_inventory']['store_id']);
		$inventory_type = intval($_SESSION['edit_inventory']['inventory_type']);
		$inventory_pgroup =  $_SESSION['edit_inventory']['inventory_pgroup'];
		$inventory_note = $CMS->class->editor->input($_SESSION['edit_inventory']['inventory_note'],"text");

 
		if($store_id == 0 OR empty($store_id))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['in_store_err']}";
			return false;
		}

		if($inventory_type == 0 OR empty($inventory_type))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['inventory_type_err']}";
			return false;
		}
 
		if($inventory_type == 2  AND count($inventory_pgroup) <= 0 )
		{ 
			$_SESSION['error_msg'] = "{$CMS->lang['inventory_pgroup_err']}";
			return false;
		}

		// Check type mới so với type cũ 
		//if($inventory_type != $in['inventory_type'])
		//{
			$this->del_in_item($in['inventory_id']);
		//}
		
 		$inventory_pgroup_str = "";
		//Convert array pgroup to string
		if(count($inventory_pgroup) > 0)
		{
			foreach ($inventory_pgroup as $key => $value) {
				# code...
				if($value != "")
				{
					$inventory_pgroup_str .= ",".$value.",";
				}
			}
		}

 		$time = time();
  
		
		$DB->query("UPDATE `".root_table."inventory` SET `inventory_time_update`='{$time}',`user_id`='{$member['user_id']}',`inventory_type`='{$inventory_type}',`store_id`='{$store_id}',`inventory_pgroup`='{$inventory_pgroup_str}',`inventory_note`='{$inventory_note}'   WHERE `inventory_deleted`='0' AND `inventory_id`='{$in['inventory_id']}'");
			$CMS->class->logs->key = $key;
			$CMS->class->logs->save_detail("inventory",$in['inventory_id'],$this->get_info($in['inventory_id']));
		if($this->addproduct_do($in['inventory_id']) == true)
		{
			unset($_SESSION['edit_inventory']);
		}
		else
		{
			return false;
		}
		$_SESSION['msg'] = "{$CMS->lang['inventory_edit_success']}";
		$CMS->class->logs->key = "inventory_".$in['inventory_id'];
		$CMS->class->logs->insert("Edit_inventory_{$in['inventory_id']}");

		return true;
	 
	 
	}


	public function edit_item() {
 		global $CMS, $DB, $member;
		
 		 
 		$item = $CMS->inventory->get_info_item($CMS->input['ini_id']);
		$key = "inventory_{$item['inventory_id']}";
		$CMS->class->logs->key = $key;
		$CMS->class->logs->old_data = $item;
		$CMS->class->logs->insert($key);
 

		$ini_check = intval($CMS->input['ini_check']);
		 

		if($ini_check < 0 OR empty($ini_check))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['ini_check_err']}";
			return false;
		}
 
	 	$ini_amount = $ini_check - $item['ini_quantity'];
  
		
		$DB->query("UPDATE `".root_table."inventory_item` SET `ini_check`='{$ini_check}', `ini_amount`='{$ini_amount}'   WHERE    `inventory_id`='{$CMS->input['id']}' AND ini_id = '{$CMS->input['ini_id']}' ");

		$new_item = $CMS->inventory->get_info_item($CMS->input['ini_id']);

		$CMS->class->logs->key = $key;
		$CMS->class->logs->save_detail("inventory",$item['inventory_id'],$new_item);
		$CMS->class->logs->insert("Edit_inventory_item: #{$new_item['ini_id']} , Phiếu kiểm salon: #{$item['inventory_id']} ");

		return true;
	 
	 
	}

	public function del_item($in_id = null) {
		if (!empty($in_id)) {
			global $CMS, $DB, $member;
			
			$ini_id = intval($CMS->input['ini_id']);
			$DB->query("UPDATE `".root_table."inventory_item` SET `ini_deleted`=1 WHERE `ini_id`='{$ini_id}'");
			$CMS->class->logs->key = "inventory_".$in_id;
			$CMS->class->logs->insert("{$CMS->lang['title_log_delete_1']} #{$ini_id} {$CMS->lang['title_log_delete_2']} #{$in_id}");
			return true;
		}
		return false;
	}


	public function deleted($id = null) {
		if (!empty($id)) {
			global $CMS, $DB, $member;
			

			$DB->query("UPDATE `".root_table."inventory` SET `inventory_deleted`=1 WHERE `inventory_id`='{$id}'");
			$CMS->class->logs->key = "inventory_".$id;
			$CMS->class->logs->insert("{$CMS->lang['title_delete_inventory']} #{$id}");
			return true;
		}
		return false;
	}


	public function get_inventory_item($inventory_id = '', $ass_key = null) {
		if (!empty($inventory_id) AND !empty($ass_key)  ) {
			global $CMS, $DB, $member;
			
 
		  $sql = $DB->query("SELECT * FROM ".root_table."inventory_item WHERE ini_deleted = 0 AND ass_key='{$ass_key}' AND inventory_id = '{$inventory_id}' ORDER BY ini_id DESC LIMIT 1");
		  if($DB->num_rows($sql) > 0)
		  {
		  		$data = $DB->fetch_array($sql);

		  		return $data;
		  }
		  else
		  {
		  	return false;
		  }	
			 
		}
		return false;
		  
	}

	public function get_inventory_item_byin($inventory_id = '' ) {
		if (!empty($inventory_id) AND !empty($ass_key)  ) {
			global $CMS, $DB, $member;
			
 		$out = array();
		  $sql = $DB->query("SELECT * FROM ".root_table."inventory_item WHERE ini_deleted = 0   AND inventory_id = '{$inventory_id}' ORDER BY ini_id DESC ");
		  if($DB->num_rows($sql) > 0)
		  {
		  		while($data = $DB->fetch_array($sql))
		  		{
		  			$out[] = $data;
		  		}

		  		return $out;
		  }
		  else
		  {
		  	return false;
		  }	
			 
		}
		return false;
		  
	}


	 
	 public function list_asset($in = array() )
	 {
	 		global $CMS, $DB, $member;
 	 	 	$output = "";

	 	 	$search_type = intval(input::get('search_type'));

            $in['inventory_id'] = isset($in['inventory_id']) ? $in['inventory_id'] : 0;
	        $store_id = intval($in['store_id']);
	        $ls_inventory_pg =  $in['inventory_pgroup'];

         $clause = "";
			if($store_id)
	        {
	        	$clause .= " AND store_id = '{$store_id}' ";
	        }

	        if($in['inventory_type'] == 2)//Theo danh mục
	        {

	        		$in_pg_arr = explode(",", $ls_inventory_pg);
		        	foreach ($in_pg_arr as $key => $value) {
		        		# code...
		        		if($value != "")
		        		{
		        			$p_group .= $value.",";
		        		}
		        		
		        	}
		        	$p_group = rtrim($p_group,',');

	         
	        	
	        	$clause .= " AND pgroup_id IN ({$p_group}) ";
	        }

          $sql = $DB->query("SELECT *, COUNT(ass_id) as cnt FROM ".root_table."assets WHERE ass_deleted = 0 AND parent_id=0 {$clause} AND ass_key != '' AND is_available = 1 GROUP BY ass_key ORDER BY ass_name ");
	        if($DB->num_rows($sql) > 0)
	        {

	            while ($result = $DB->fetch_array($sql))
	            {
	            	 
	             	$data[] = $result;
	                $check = $amount = $description = "";
	                // Get inventory_items
	                $in_item = $this->get_inventory_item($in['inventory_id'],$result['ass_key']);
	                if(is_array($in_item))
	                {  
	                	$check = $in_item['ini_check'];  
	                	$amount = $in_item['ini_check'] - $result['cnt'];
	                	$description = $in_item['ini_description'];
	                }
	                


	                $ass_purchase_price = number_format($result['ass_purchase_price']) ."<sup>đ</sup></td>"; 
	                $output .=<<<EOF
 
	                <tr>
	                	<td><a href="{$CMS->vars['root_domain']}/?site=assets&act=show&id={$result['ass_id']}">{$result['ass_keyname']}</a></td>
	                	<td>{$result['ass_name']}</td>
	                	
	                	<td>
	                		<textarea class='form-control' maxlength='100'  name='ass_description[]'>{$description}</textarea>
	                	</td>
	                	<td>
	                		<span>{$ass_purchase_price}</span>
	                	</td>
	                	<td>
	                		<span>{$result['cnt']}</span>
	                	</td>
	                	<td>
	                		<input type='text' class='form-control' onkeypress='return check_enter_number(event,this);'   name="ass_check[]" onkeyup='cal_quantity({$result['cnt']},this)'  maxlength='6' value="{$check}" />
	                	</td>
	                	<td>
	                		<span id='container_ass_quantity'>{$amount}</span>
	                	</td>
	                	<td class='trash'><span class='del_row_order'><i class='fa fa-trash' aria-hidden='true'></i></span></td>
	                	<input type='hidden' name='ass_name[]' value='{$result['ass_name']}'/>
	                	<input type='hidden' name='ass_key[]' value='{$result['ass_key']}'/>
	                	<input type='hidden' name='ass_code[]' value='{$result['ass_key']}'/>
	                	<input type='hidden' name='ass_quantity[]' value='{$result['cnt']}'/>
	                </tr>

EOF;

	            }
	        }

	        return $output;
	 }  

	 public function list_asset_2($inventory_id = "", $type = 0)
	 {
	 		global $CMS, $DB, $member;

	 	 $output = "";
	 	  if($type == 1)
	 	  {
	 	  	// List ass : amount <= 0
	 	  	$sql_add = " AND ini_amount < 0 ";
	 	  }
	 	  else if($type == 2)
	 	  {
	 	  	// List ass : amount <= 0
	 	  	$sql_add = " AND ini_amount > 0 ";
	 	  }

          $sql = $DB->query("SELECT *  FROM ".root_table."inventory_item WHERE ini_deleted = 0 AND inventory_id={$inventory_id}  {$sql_add} ORDER BY ini_id DESC");
	        if($DB->num_rows($sql) > 0)
	        {
	            while ($result = $DB->fetch_array($sql))
	            {
	             	$data[] = $result;
 
	            }
	        }

	        return $data;
	 }  

	public function addproduct_do($inventory_id = "")
	{
	 	global $CMS, $DB, $member;
	// 	$inventory_id = intval($CMS->input['id']);
	 	$time = time();
	 	//Get info inventory
	 	$in = $this->get_info($inventory_id);
	 	if(! is_array($in))
	 	{
	 		$_SESSION['error_msg'] = "Phiếu kiếm salon không tồn tại";
	 		return false;
	 	}
	 	//

	 	$item_asset =  array();
	 	// Producr
		$ass_name = array_values($CMS->input['ass_name']);
		$ass_key = array_values($CMS->input['ass_key']);
		$ass_description = array_values($CMS->input['ass_description']);
	 
		$ass_quantity = array_values($CMS->input['ass_quantity']);
		$ass_check = array_values($CMS->input['ass_check']);
		 
	 	$flag = 0;
		$count = count($ass_key);
		for($i=0; $i<$count; $i++)
		{
			if( $ass_name[$i] != "" AND $ass_name[$i] != "" AND $ass_check[$i] > 0 )
			{
			 
					$item_ass[$i]['ass_name'] = $ass_name[$i];
					$item_ass[$i]['ass_description'] = $ass_description[$i];
					$item_ass[$i]['ass_key'] = $ass_key[$i];
				 

					$item_ass[$i]['ass_quantity'] = $ass_quantity[$i];
					$item_ass[$i]['ass_check'] = intval($ass_check[$i]);
					$item_ass[$i]['ass_diff'] = $ass_check[$i] -  $ass_quantity[$i];
 					$flag += 1;
			}
		

		}
 
		if($flag == 0)
		{
			// Check co tai sản trong phieu
			$_SESSION['error_msg'] = "{$CMS->lang['emsg_add_assets_inventory']}";
			return false;
		}
		// Loop item ass for insert
 
		foreach ($item_ass as $key => $value) {
			# code...
			if($value['ass_name'] != "")
			{
				if($value['ass_check'] != "" AND $value['ass_check'] > 0)
				{
					//Start insert
					$DB->query("INSERT INTO `".root_table."inventory_item` (  `ini_name`, `ini_description`,`ini_quantity` ,`ini_check`,  `ini_amount` , `ass_key`, `inventory_id`, `store_id`, `ini_time`, `user_id`) VALUES ( '{$value['ass_name']}', '{$value['ass_description']}' , '{$value['ass_quantity']}', '{$value['ass_check']}', '{$value['ass_diff']}', '{$value['ass_key']}', '{$inventory_id}', '{$in['store_id']}', '{$time}', '{$member['user_id']}' )");
				}
				
			}
		}

		$CMS->class->logs->key= "inventory_{$in['inventory_id']}";
		$_SESSION["msg"] .= "{$CMS->lang['add_inventory_item_msg']} <b> #{$inventory_id}</b>";
		$CMS->class->logs->insert("{$CMS->lang['add_inventory_item']} <b> #{$inventory_id}</b>")."<br />";
		return true;

	}

	 
	function del_in_item($inventory_id = "")
	{
		global $CMS, $DB, $member;
		$DB->query("UPDATE `".root_table."inventory_item` SET `ini_deleted`=1 WHERE `inventory_id`='{$inventory_id}'");
		return;
	} 	
	function count_in_item($inventory_id = "")
	{
		global $CMS, $DB, $member;
		$sql = $DB->query("SELECT * FROM `".root_table."inventory_item`   WHERE `inventory_id`='{$inventory_id}' AND `ini_deleted`= 0");

		$ton = $thucte = 0;
		$rows = $DB->num_rows($sql);
		if($rows > 0)
		{
			while($data = $DB->fetch_array($sql))
			{
				$ton += $data['ini_quantity'];
				$thucte += $data['ini_check'];
			}
		}
		$data_output = array($rows, $ton, $thucte);
		return $data_output;

	}



	function approve($in = "")
	{
		global $CMS, $DB, $member;
		 
		$data_ex = $this->list_asset_2($in['inventory_id'],1);
		$data_im = $this->list_asset_2($in['inventory_id'],2);
		$request_id = $request_im_id = 0;
		if(count($data_ex) == 0 AND count($data_im) == 0)
		{
			//Check co tai san trong phieu kiem salon k?

			$_SESSION['error_msg'] = "{$CMS->lang['emsg_add_assets_inventory']}";
			return array("status" => false);
		}

		$data_item = array();
		$total_ex = 0;
		if(count($data_ex) > 0)
		{
			$i = 0;
			foreach ($data_ex as $key => $value) {
				# code...
				$ass = $CMS->assets->get_info($value['ass_key']);

				$data_item[$i]['ass_name'] = $value['ini_name'];
				$data_item[$i]['ass_code'] = $ass['ass_code'];
				$data_item[$i]['ass_key'] = $value['ass_key'];
				if($ass['ass_price']!= "" AND $ass['ass_price'] > 0)
				{
					$data_item[$i]['ass_price'] = $ass['ass_price'];
				}
				else
				{
					$data_item[$i]['ass_price'] = $ass['ass_purchase_price'];
				}
				$data_item[$i]['ass_quantity'] = $value['ini_amount'] * (-1);
				$data_item[$i]['ass_tax'] = 0;

				$data_item[$i]['ass_amount'] = (intval($data_item[$i]['ass_quantity']) * $data_item[$i]['ass_price']) + round(($data_item[$i]['ass_price'] * $data_item[$i]['ass_quantity'] * $data_item[$i]['ass_tax'])/100);
 
				$total_ex += $data_item[$i]['ass_amount'];
				$i++;
			}
			$ass_ex['request_product'] = $CMS->returns->convert_assets_to_product($data_item);

			$ass_ex['request_product'] = json_encode($ass_ex['request_product'], JSON_UNESCAPED_UNICODE); 
			$ass_ex['request_type'] = 1;
			$ass_ex['request_subtype'] = 4;
			$ass_ex['request_amount'] = $total_ex;
			$ass_ex['request_stage'] = 2;
			$ass_ex['request_status'] = 20;
			$ass_ex['store_id'] = $in['store_id'];
			$ass_ex['inventory_id'] = $in['inventory_id'];
			$ass_ex['request_note']  = "{$CMS->lang['title_inventory_control']} #{$in['inventory_name']}";


			$request_id = $CMS->store_request->add($ass_ex, 1);

		}
	 
		
 		


		

		$data_item_2 = array();
		$total_im = 0;
		if(count($data_im) > 0)
		{
			$i = 0;
			foreach ($data_im as $key => $value) {
				# code...
				$ass = $CMS->assets->get_info($value['ass_key']);

				$data_item_2[$i]['ass_name'] = $value['ini_name'];
				$data_item_2[$i]['ass_code'] = $ass['ass_code'];
				$data_item_2[$i]['ass_key'] = $value['ass_key'];
				if($ass['ass_price']!= "" AND $ass['ass_price'] > 0)
				{
					$data_item_2[$i]['ass_price'] = $ass['ass_price'];
				}
				else
				{
					$data_item_2[$i]['ass_price'] = $ass['ass_purchase_price'];
				}
				$data_item_2[$i]['ass_quantity'] = $value['ini_amount'];
				$data_item_2[$i]['ass_tax'] = 0;

				$data_item_2[$i]['ass_amount'] = (intval($data_item_2[$i]['ass_quantity']) * $data_item_2[$i]['ass_price']) + round(($data_item_2[$i]['ass_price'] * $data_item_2[$i]['ass_quantity'] * $data_item_2[$i]['ass_tax'])/100);
 
				$total_im += $data_item_2[$i]['ass_amount'];
				$i++;
			}
			
			$ass_im['request_product'] = $CMS->returns->convert_assets_to_product($data_item_2);

			$ass_im['request_product'] = json_encode($ass_im['request_product'], JSON_UNESCAPED_UNICODE); 
			$ass_im['request_type'] = 0;
			$ass_im['request_subtype'] = 1;
			$ass_im['request_stage'] = 2;
			$ass_im['request_status'] = 20;
			$ass_im['request_amount'] = $total_im;
			$ass_im['store_id'] = $in['store_id'];
			$ass_im['inventory_id'] = $in['inventory_id'];
			$ass_im['request_note']  = "{$CMS->lang['title_inventory_control']} #{$in['inventory_name']}";


			$request_im_id = $CMS->store_request->add($ass_im, 1);

		}
 
		
 		
 		$time = time();
		if($request_id > 0 OR $request_im_id > 0)
		{
			$DB->query("UPDATE `".root_table."inventory` SET `sr_export`='{$request_id}', `sr_import`='{$request_im_id}', 	is_balance = 1, inventory_time_update = '{$time}' WHERE `inventory_id`= '{$in['inventory_id']}'");
		}

		$data_output = array($request_id, $request_im_id);
		return array("status" =>true, "data_output" => $data_output);
		 
	}

	// Tanlv 17/02
	public function searchKey($key='')
	{
		global $CMS, $DB;

		
		$data = array();
		
		if($key)
		{
			$sql = $DB->query("SELECT * FROM ".root_table."inventory WHERE inventory_name LIKE '%{$key}%' AND inventory_deleted = 0");
			if($DB->num_rows($sql) > 0)
			{
				while ($result = $DB->fetch_array($sql)) 
				{
					$arr['value'] = $result['inventory_deleted'];
					$arr['id'] = $result['inventory_id'];
					$data[] = $arr;
				}
			}
		}
		return $data;
	}

}
?>