<?php
if (!defined('IN_ROOT')) exit();

$CMS->returns = new class_returns;
class class_returns{
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
	public $news_project = "";
	public $data_array = array();	 
	public $per_page = 10;
	public $prefix_html = "";
	public $suffix_html = "";
	public $html;
	

	//===========================================================================
	// Load html
	//===========================================================================
	
	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			$this->html = $CMS->class->template->load_template("skin_returns");		
		}
	}

	//===========================================================================
	//  AUTO RUN
	//===========================================================================

	public function auto_run_returns()
	{
		global $CMS, $DB, $member;

		$this->loadhtml();

		//-----------------------------------------------------------
		// ACTION CONTROLLER
		//-----------------------------------------------------------
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_returns_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_returns_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";

			// Check permission to Delete
			if ( $CMS->permit["returns_delete"] == true )
			{
				$data .= "<option value='delete_all'>Xóa lô hàng đã chọn</option>";
				$this->control = 1;
			}
			
			$data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

			$CMS->class->cache->save("user_{$member['user_id']}_returns_controller_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}
		
		if ( $CMS->vars['action_controller']  OR $CMS->permit["order_search"] == 1 )
		{
			//$this->action_control = $this->html->returns_control();
		}
		
	 
	}


	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("ret_id,ret_name,ret_time");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "ret_time_update";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "desc";
		
		// SQL Condition
		$this->sql_add .= " AND ret_deleted=0 ";

 		$this->prefix_html .= "?site=returns";
		if($CMS->input['quick_search'])
		{
			$this->prefix_html .= "&quick_search={$CMS->input['quick_search']}";
		}
		$this->prefix_html .= "&page=";
		// Create SQL Query for listing Data

		list($this->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."returns WHERE  1=1 {$this->sql_add}  ORDER BY {$default_field} {$default_order}",20, $this->prefix_html, $this->suffix_html );
		$this->pages_cnt = $DB->num_rows( $this->sql_query );
	}

	public function html()
	{
		global $CMS, $DB, $member;

		$this->loadhtml();

		// Display Header
		$output .= $this->html->header();
		
		if ( $DB->num_rows( $CMS->returns->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->returns->sql_query ) )
			{
				// Convert info
				$result = $CMS->returns->convertvalue($result);
				
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
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=returns");
		}
		
		return $output;
	}

	
	public function add()
	{
		global $CMS, $DB, $member;
		
		// print "<pre>";
		// print_r($_SESSION['list_product']);
		// print_r($CMS->input);exit;
		$cus_id = intval($CMS->input['cus_id']);
		$store_id = intval($CMS->input['store_id']);
		$user_assign = intval($CMS->input['user_assign']);
		$user_check = intval($CMS->input['user_check']);
		$ret_note = $CMS->class->editor->input("ret_note");
		$ret_fee = intval($CMS->input['ret_fee']);
		$user_id = $member['user_id'];
		$ret_time = time();
		
		$arr_ass_name = array_values($CMS->input['ass_name']);
		$arr_ass_key = array_values($CMS->input['ass_id']);
		$arr_ass_code = array_values($CMS->input['ass_code']);
		$arr_item_id = array_values($CMS->input['item_id']);
		$arr_ass_quantity = array_values($CMS->input['ass_quantity']);
		$arr_ass_tax = array_values($CMS->input['ass_tax']);
		$arr_ass_price = array_values($CMS->input['ass_price']);

		$count = count($arr_ass_name);
		$data_product = array();
		for($i=0; $i<$count; $i++)
		{
			if($arr_ass_name[$i])
			{
				
				if (array_key_exists($arr_item_id[$i],$_SESSION['list_product']))
				{
					$index = $arr_item_id[$i];
					$data_product[$i] = $_SESSION['list_product'][$index];
					$data_product[$i]['ass_code'] = $arr_ass_code[$i];
				}else
				{
					$data_product[$i]['ass_key'] = intval($arr_ass_key[$i]);
					$info = $CMS->assets->get_info($arr_ass_key[$i]);
					
					$data_product[$i]['ass_name'] = $CMS->class->editor->input($arr_ass_name[$i], "text");
					$data_product[$i]['ass_quantity'] = intval($arr_ass_quantity[$i]);
					$data_product[$i]['ass_code'] = $arr_ass_code[$i];
					$data_product[$i]['ass_price'] = $arr_ass_price[$i];
					$data_product[$i]['ass_tax'] = $arr_ass_tax[$i];
					$data_product[$i]['ass_amount'] = (intval($arr_ass_quantity[$i]) * $arr_ass_price[$i]) + round(($arr_ass_price[$i] * $arr_ass_quantity[$i] * $arr_ass_tax[$i])/100);
					$data_product[$i]['ass_subitem'] = $CMS->assets->get_list_item($info['ass_id']);

				}

				
			}
		}

		// Tính tổng tiền
		$ret_amount = 0;
		$subtotal = 0;
		foreach ($data_product as $key => $value) 
		{
			$subtotal = $value['ass_price'] * $value['ass_quantity'];
			$ret_amount += $subtotal + round(($value['ass_tax']*$subtotal)/100);
			// print $value['product_tax']."<br/>";
		}


		if(!$cus_id)
		{
			$_SESSION['error_msg'] = $CMS->lang['ret_not_empty_customer'];
			return false;
		}
		
		// Check input
		if (!$store_id) 
		{
			$_SESSION['error_msg'] = $CMS->lang['ret_not_empty_store'];
			return false;
		}
		$data_product_bk = $data_product;
		$ret_assets = json_encode($data_product, JSON_UNESCAPED_UNICODE);
		// Insert data
		$count = $DB->query("INSERT INTO ".root_table."returns (cus_id, store_id, user_assign, user_check, user_id, ret_note, ret_time, ret_time_update, ret_fee, ret_amount, ret_assets) VALUES ('{$cus_id}', '{$store_id}', '{$user_assign}', '{$user_check}', '{$user_id}', '{$ret_note}', '{$ret_time}', '{$ret_time}', '{$ret_fee}', '{$ret_amount}', '{$ret_assets}')");
		if($count)
		{
			$ret_id = $DB->last_insert_id();
			$_SESSION['highlight'] = $ret_id;
			$ret_code = "RET".$ret_id;
			$DB->query("UPDATE ".root_table."returns SET ret_code = '{$ret_code}' WHERE ret_id = '{$ret_id}'");
			$CMS->class->logs->key= "returns_{$ret_id}";
			$_SESSION['msg'] = $CMS->class->logs->insert("{$member['cus_username']} đã tạo phiếu trả hàng <b>{$ret_code}</b> thành công<br/>");
			$data_new = $this->get_info($ret_id);
			if($CMS->input['type_submit'] == 1)
			{
				$this->create_bill_trans($data_new, $data_product_bk);
			}
			
			return true;
		}else
		{
        	return false;
		}
                
	}

	public function edit() 
	{
		global $CMS, $DB, $member;
		
		$data = $this->get_info();
		$CMS->class->logs->key = "returns_{$CMS->input['id']}";
		$CMS->class->logs->old_data = $data;
		// print "<pre>";
		// print_r($_SESSION['list_product']);
		// print_r($CMS->input);exit;
		$cus_id = intval($CMS->input['cus_id']);
		$store_id = intval($CMS->input['store_id']);
		$user_assign = intval($CMS->input['user_assign']);
		$user_check = intval($CMS->input['user_check']);
		$ret_note = $CMS->class->editor->input("ret_note");
		$ret_fee = intval($CMS->input['ret_fee']);
		$user_id = $member['user_id'];
		$ret_time_update = time();
		
		$arr_ass_name = array_values($CMS->input['ass_name']);
		$arr_ass_key = array_values($CMS->input['ass_id']);
		$arr_ass_code = array_values($CMS->input['ass_code']);
		$arr_item_id = array_values($CMS->input['item_id']);
		$arr_ass_quantity = array_values($CMS->input['ass_quantity']);
		$arr_ass_tax = array_values($CMS->input['ass_tax']);
		$arr_ass_price = array_values($CMS->input['ass_price']);

		$count = count($arr_ass_name);
		$data_product = array();
		for($i=0; $i<$count; $i++)
		{
			if($arr_ass_name[$i])
			{
				
				if (array_key_exists($arr_item_id[$i],$_SESSION['list_product']))
				{
					$index = $arr_item_id[$i];
					$data_product[$i] = $_SESSION['list_product'][$index];
					$data_product[$i]['ass_code'] = $arr_ass_code[$i];
				}else
				{
					$data_product[$i]['ass_key'] = intval($arr_ass_key[$i]);
					$info = $CMS->assets->get_info($arr_ass_key[$i]);
					
					$data_product[$i]['ass_name'] = $CMS->class->editor->input($arr_ass_name[$i], "text");
					$data_product[$i]['ass_quantity'] = intval($arr_ass_quantity[$i]);
					$data_product[$i]['ass_code'] = $arr_ass_code[$i];
					$data_product[$i]['ass_price'] = $arr_ass_price[$i];
					$data_product[$i]['ass_tax'] = $arr_ass_tax[$i];
					$data_product[$i]['ass_amount'] = (intval($arr_ass_quantity[$i]) * $arr_ass_price[$i]) + round(($arr_ass_price[$i] * $arr_ass_quantity[$i] * $arr_ass_tax[$i])/100);
					$data_product[$i]['ass_subitem'] = $CMS->assets->get_list_item($info['ass_id']);

				}

				
			}
		}

		// Tính tổng tiền
		$ret_amount = 0;
		$subtotal = 0;
		foreach ($data_product as $key => $value) 
		{
			$subtotal = $value['ass_price'] * $value['ass_quantity'];
			$ret_amount += $subtotal + round(($value['ass_tax']*$subtotal)/100);
			// print $value['product_tax']."<br/>";
		}


		if(!$cus_id)
		{
			$_SESSION['error_msg'] = $CMS->lang['ret_not_empty_customer'];
			return false;
		}
		
		// Check input
		if (!$store_id) 
		{
			$_SESSION['error_msg'] = $CMS->lang['ret_not_empty_store'];
			return false;
		}
		
		$data_product_bk = $data_product;
		$ret_assets = json_encode($data_product, JSON_UNESCAPED_UNICODE);
		if($CMS->input['act'] == "approve_do")
		{
			$ret_status = 1; // Duyệt là hoàn thành luôn
		}else
		{
			$ret_status = 0;
		}
		// Insert data
		$count = $DB->query("UPDATE ".root_table."returns SET cus_id = '{$cus_id}', store_id = '{$store_id}', user_assign = '{$user_assign}', user_check = '{$user_check}', user_id = '{$user_id}', ret_note = '{$ret_note}', ret_time_update = '{$ret_time_update}', ret_fee = '{$ret_fee}', ret_amount = '{$ret_amount}', ret_assets = '{$ret_assets}', ret_status = '{$ret_status}' WHERE ret_id = '{$data['ret_id']}'");
		if($count)
		{
			$_SESSION['highlight'] = $data['ret_id'];
			$data_new = $this->get_info($data['ret_id']);
			$CMS->class->logs->key = "returns_{$data['ret_id']}";
			$CMS->class->logs->save_detail("returns",$data['ret_id'], $data_new);
			
			// Duyệt phiếu
			if($CMS->input['act'] == "approve_do")
			{
				$this->create_bill_trans($data_new, $data_product_bk);
			}else
			{
				$_SESSION['msg'] = $CMS->class->logs->insert("{$member['cus_username']} {$CMS->lang['update_msg_edit']} <b>{$data_new['ret_code']}</b> {$CMS->lang['msg_success']}</br/>");
			}
			return true;
		}else
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
			$DB->query("SELECT ret_id FROM ".root_table."returns WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND ret_deleted=0");
		}
		else
		{
			$DB->query("SELECT ret_id FROM ".root_table."returns WHERE {$field}='{$value}' AND ret_deleted=0");
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
	
	
	public function delete()
	{
		global $CMS, $DB;
		
		if($CMS->permit['returns_delete'])
		{
			// Get info
			$data = $this->get_info();
			
			// Check existing
			if ( ! $data ) { return false; }
			
			// Update info
			$DB->query("UPDATE ".root_table."returns SET ret_deleted=1 WHERE ret_id={$data['ret_id']}");
			
			// Create log
			$CMS->class->logs->key = "ret_{$data['ret_id']}";
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['ret_deleted']} <b>{$data['ret_code']}</b>");
			
	        $CMS->class->cache->delete("ret_list");
			
		}
                
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=returns&page={$CMS->input['page']}");
		
		return true;
	}
	
	
	
	public function auto_run() {
		global $CMS, $DB, $member;
	 
		if (!isset($this->html)) {	
			$this->html = $CMS->class->template->load_template("skin_store");
		}
		
		if ($CMS->class->cache->check("user_{$member['user_id']}_store_{$CMS->vars['default_language']}")) {
			$CMS->vars['action_controller']=$CMS->class->cache->load("user_{$member['user_id']}_store_{$CMS->vars['default_language']}");
		} else {
			$data = "";
			if ($CMS->permit["store_search"]) {
				$data .=<<<EOF
<div class="tbl-cell tbl-cell-action-bordered" >
	<a href="{$CMS->vars['root_domain']}/?site=store&act=search" title="{$CMS->lang['title_search_store']}">
	<button type="button" class="action-btn"><i class="fa fa-search"></i></button>
  </a>
</div>
EOF;
				
				$this->control = 1;
			}
			if ($CMS->permit["pcategory_arrange"]) {
				$data .=<<<EOF
<div class="tbl-cell tbl-cell-action-bordered" act="arrange" id="glyphicon-sort" >
	<i class="fa fa-refresh" title="{$CMS->lang['title_arrange_store']}"></i>
</div>
EOF;
				$this->control = 1;
			}
			if ($CMS->permit["pcategory_delete"]) {
				$data .=<<<EOF
<div class="tbl-cell tbl-cell-action-bordered" act="delete_all" id="font-icon-trash">
	<i class="fa fa-trash-o" title="{$CMS->lang['title_delete_all_store']}"></i>
</div>
EOF;
				$this->control = 1;
			}
			if ($CMS->permit["pcategory_add"]) {
				$data .=<<<EOF
<div class="tbl-cell tbl-cell-action-bordered">
	<a href="{$CMS->vars['root_domain']}/?site=store&act=add" title="{$CMS->lang['title_add_pcategory']}">
		<button type="button" class="action-btn"><i class="fa fa-plus-circle"></i></button>
	</a>
</div>
EOF;
				$this->control = 1;
			}
			$data = $this->control == 1 ?  $data : "";
			$CMS->class->cache->save("user_{$member['user_id']}_store_{$CMS->vars['default_language']}", $data);
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
		if (intval($CMS->input['sid'])) {
			$str.='&sid='.intval($CMS->input['sid']);
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store{$str}");
	}



	public function get_info( $record_id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "returns" )
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
		
		$sql = $DB->query("SELECT * FROM ".root_table."returns WHERE (ret_id='{$record_id}' OR ret_code='{$record_id}') AND ret_deleted = 0 ORDER BY ret_id DESC LIMIT 1");

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
	public function convert_data($data)
	{
		global $CMS;


		// Customer
		$data['cus_name'] = $CMS->customer->getInfo($data['cus_id'], "cus_full_name");
		

		$data['user_name_assign'] = $CMS->user->get_info($data['user_assign'], "user_display_name");
		$data['user_name_check'] = $CMS->user->get_info($data['user_check'], "user_display_name");
		
		return $data;
		
	}
	public function convertvalue($data) 
	{
		global $CMS;
	
		$data['user_name'] = $CMS->user->get_info($data['user_id'],"user_name");
		$data["ret_time_bk"] = $CMS->class->date->date_format($data['ret_time'],0);
		if($CMS->permit['returns_read'] == 1)
		{
			$data["ret_code"] = "<a href=\"{$CMS->vars['root_domain']}/?site=returns&act=show&id={$data['ret_id']}\">{$data["ret_code"]}</a>";
		}

		// Customer
		$cus_name = $CMS->customer->getInfo($data['cus_id'], "cus_full_name");
		if($CMS->permit['customer_read'] == 1)
		{
			
			$data["cus_name_show"] = "<a href=\"{$CMS->vars['root_domain']}/?site=customer&act=show&id={$data['cus_id']}\">{$cus_name}</a>";
		}else
		{
			$data["cus_name_show"] = $cus_name;
		}
		// Store
		$store_name = $CMS->store->get_info($data['store_id'], "store_name");
		if($CMS->permit['store_read'] == 1)
		{
			
			$data["store_name_show"] = "<a href=\"{$CMS->vars['root_domain']}/?site=store&act=show&id={$data['store_id']}\">{$store_name}</a>";
		}else
		{
			$data["store_name_show"] = $store_name;
		}

		$user_name_assign = $CMS->user->get_info($data['user_assign'], "user_display_name");
		$user_name_check = $CMS->user->get_info($data['user_check'], "user_display_name");
		if($CMS->permit['user_read'] == 1)
		{
			$data["user_name_assign"] = "<a href=\"{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}\">{$user_name_assign}</a>";
			$data["user_name_check"] = "<a href=\"{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}\">{$user_name_check}</a>";
		}else
		{
			$data["user_name_assign"] = $user_name_assign;
			$data["user_name_check"] = $user_name_check;
		}
		
		if($data['ret_status'] == 0)
		{
			$color = "danger";
		}elseif($data['ret_status'] == 1)
		{
			$color = "success";
		}elseif($data['ret_status'] == 2)
		{
			$color = "warning";
		}else
		{
			$color = "default";
		}
		// status
		$data['ret_status_show'] = "<span class='label label-{$color}'>".$CMS->lang['ret_status_'.$data['ret_status']]."</span>";

		$data['ret_assets_show'] = "";
		$ret_assets = json_decode($data['ret_assets'], true);
		$count_li = 1;
		$title_product = "";
		foreach ($ret_assets as $key => $value) 
		{
			$name_product_bk = $name_product = $value['ass_name']." ({$value['ass_quantity']})";
			if($value['product_id'])
			{
				$name_product = "<a style='display: inline-block;' href='{$CMS->vars['root_domain']}/?site=assets&act=show&id={$value['product_id']}'>{$name_product}</a>";
			}

			
			if($count_li <= 2)
			{
				$data['ret_assets_show'].=<<<EOF
					<li>{$name_product}</li>
EOF;
				$title_product .= $name_product_bk.", ";
			}else
			{
				$title_product .= $name_product_bk.", ";
			}
			$count_li ++;

		}

		if($data['ret_assets_show'])
		{
			$title_product = rtrim($title_product,', ');
			$data['ret_assets_show'] = "<ul data-toggle='tooltip' data-placement='bottom' title='{$title_product}'>".$data['ret_assets_show']."</ul>";
		}

		$data['ret_amount_show'] = $data['ret_amount'] ? $CMS->class->input->currency($data['ret_amount']): "";

		// Check quyền
		$data['action_box'] = "";
		$btn_cancel = "";
		$btn_approve = "";
		
		if(($CMS->permit['returns_approve'] or $CMS->permit['returns_cancel']) and $data['ret_status'] == 0)
		{
			if($CMS->permit['returns_approve'])
			{
				$btn_approve = <<<EOF
					<a class="dropdown-item" href="{$CMS->vars['root_domain']}/?site=returns&act=approve&id={$data['ret_id']}" >{$CMS->lang['title_approve_returns']}</a>
EOF;

			}

			if($CMS->permit['returns_cancel'])
			{
				$btn_cancel = <<<EOF
					<a class="dropdown-item" onclick="confirmAction('{$CMS->lang['confirm_action']}','{$CMS->vars['root_domain']}/?site=returns&act=cancel&id={$data[ret_id]}')">{$CMS->lang['title_cancel_returns']}</a>
EOF;
			}
			$data['action_box'] .=<<<EOF
				<div class="btn-group">
					<button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						{$CMS->lang['title_action']}
					</button>
					<div class="dropdown-menu">
						{$btn_approve}
						{$btn_cancel}
					</div>
				</div>
EOF;

		}

		if($CMS->permit['returns_edit'] == 1 and ($data['ret_status'] == 0))
		{
			$data['action_box'] .= <<<EOF
				<a href="{$CMS->vars['root_domain']}/?site=returns&act=edit&id={$data['ret_id']}"  class="edit"><i class="fa fa-edit"></i></a>
EOF;

		}
		
		if($CMS->permit['returns_delete'] == 1 and ($data['ret_status'] == 0 or $data['ret_status'] == 3))
		{
			$data['action_box'] .=<<<EOF
							<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=returns&act=delete&id={$data['ret_id']}');" class="edit"><i class="fa fa-trash-o"></i></a>
					 
EOF;
		}


		$data['ret_fee_show'] = $CMS->class->input->currency($data['ret_fee'],".")." đ";

		// Ma giao dich
		$info_trans = $CMS->transactions->getInfo($data['trx_id']);
		$data['trx_id_show'] = "";
		if($info_trans)
		{
			$data['trx_id_show'] = "<a href='{$CMS->vars['root_domain']}/?site=transactions&act=show&id={$data['trx_id']}'>{$info_trans['trx_code']}</a>";
		}

		return $data;
	}

	public function cancel_returns($ret_id)
	{
		global $CMS, $DB;

		$data = $this->get_info($ret_id);
		if($data)
		{
			$CMS->class->logs->key = "ret_{$CMS->input['id']}";
			$CMS->class->logs->old_data = $data;

			$count = $DB->query("UPDATE ".root_table."returns SET ret_status = 3 WHERE ret_id = '{$ret_id}'");
			if($count)
			{
				$data_new = $this->get_info($ret_id);
				$CMS->class->logs->key = "ret_{$ret_id}";
				$CMS->class->logs->save_detail("returns",$ret_id, $data_new);

				return $data_new;
			}else
			{
				return false;
			}
		}
	}

	public function convert_assets_to_product($data)
	{
		global $CMS;

		$result = array();
		if(is_array($data))
		{
			$i = 0;
			foreach ($data as $key => $value) 
			{
				$assets = $CMS->assets->get_info($value['ass_key']);
				$product = $CMS->product->getInfo($assets['product_id']);
				$result[$i]['product_id'] = $product['product_id'];
				$result[$i]['product_name'] = $product['product_name'];
				$result[$i]['product_description'] = $product['product_description'];
				$result[$i]['product_code'] = $product['product_code'];
				$result[$i]['product_sku'] = $product['product_sku'];
				$result[$i]['product_group'] = $product['product_group'];
				$result[$i]['product_price'] = $value['ass_price'];
				$result[$i]['product_quantity'] = $value['ass_quantity'];
				$result[$i]['product_amount'] = $value['ass_amount'];
				$result[$i]['product_tax'] = $value['ass_tax'];
				$result[$i]['product_type'] = 0;
				$result[$i]['product_cycle'] = 0;
				$result[$i]['product_status'] = 1;
			 
				$i++;
			}
		}
// print "<pre>";
// print_r($result);exit;
		return $result;
	}

	function checkInfoReturns($request_id = 0)
	{
		global $CMS, $DB;

		if($request_id)
		{
			$sql = $DB->query("SELECT ret_id FROM ".root_table."returns WHERE request_id = '{$request_id}'");
			if($DB->num_rows($sql) > 0)
			{
				return $DB->fetch_array($sql)['ret_id'];
			}else
			{
				return false;
			}
		}else
		{
			return false;
		}
	}

	function searchKey($key)
	{
		global $CMS, $DB;

		if($key)
		{
			$data = [];
			$sql = $DB->query("SELECT R.ret_code, R.ret_id, C.cus_id, C.cus_full_name FROM ".root_table."returns AS R LEFT JOIN ".root_table."customer AS C ON R.cus_id = C.cus_id WHERE (R.ret_code='{$key}' OR R.ret_id = '{$key}' OR R.ret_assets LIKE '%{$key}%' OR C.cus_full_name LIKE '%{$key}%') AND R.ret_deleted = 0 AND C.cus_deleted = 0 ORDER BY R.ret_time DESC LIMIT 10");

			if($DB->num_rows($sql) > 0)
			{
				while ($result = $DB->fetch_array($sql)) 
				{

					$arr['label'] = $result['ret_code']." - ".$result['cus_full_name'];
					$arr['value'] = $result['ret_code']." - ".$result['cus_full_name'];
					$arr['id'] = $result['ret_id'];
					$data[] = $arr;
				}
			}

			return $data;
		}
	}

	function create_bill_trans($data_new = array(), $data_product = array())
	{
		global $CMS, $DB, $member;

		$user_id = $member['user_id'];
		$time = time();
		// Tao phieu thu phí trả hàng
		if($data_new['ret_fee'] > 0)
		{
			$invoice_no = $CMS->transactions->getNoInvoice(3);
			$data_cus = $CMS->customer->getInfo($data_new['cus_id']);
			$trx_billing_address = $data_cus['cus_address'];
			$field = array('cus_type' => 1, 'cus_id' => $data_new['cus_id'], 'cus_email' => "{$data_cus['cus_email']}");
			
			$trx_terms = 15;
			$data_trx_in = array(
					"type" => 1, // Sale
					"sub" => 3, //bill
					"ret_id" => $data_new['ret_id'],
					"trx_invoice_no" => $invoice_no,
					"trx_address" => $trx_billing_address,
					"trx_terms" => $trx_terms,
					'trx_status' => 3, // Close
					"trx_expiration_date" => date("d/m/Y",$time+$trx_terms*86400),
					"trx_total" => $data_new['ret_fee'],
					"user_id" => $user_id,
					"service_type" => 0, // 0: hàng hoá, 1: dich vu
					"trx_time" => $time,
					"trx_note" => "Thu phí trả hàng theo phiếu trả hàng {$data_new['ret_code']}",

				);
			$data_trx_in = array_merge($data_trx_in, $field);
			$trx_id_in = $CMS->transactions->add($data_trx_in,1);

            //Add items
            $CMS->transactions->addItem($trx_id_in,[],1);

			$data_id_in = $CMS->transactions->getInfo($trx_id_in);
		}

		
		// Convert thong tin tài sản thành thông tin product dùng để hợp lý với bên phiếu nhập
		$request_product = $this->convert_assets_to_product($data_product);
		$request_product = json_encode($request_product, JSON_UNESCAPED_UNICODE);
		// Tạo phiếu nhập hàng bên store request
		$count2 = $DB->query("INSERT INTO ".root_table."store_request (supplier_id, store_id, shi_id, user_id, request_note, request_product, request_type, request_stage, request_status, request_time, request_time_update, request_amount, cus_id, request_reason, store_id_to, request_subtype, user_id_assign, request_time_create, request_time_delivery) VALUES ('0', '{$data_new['store_id']}', '0', '{$user_id}', '{$data_new['ret_note']}', '{$request_product}', '0', '3', '31', '{$time}', '{$time}', '{$data_new['ret_amount']}', '{$data_new['cus_id']}',  '', '0', '0', '{$data_new['user_assign']}', '{$time}', '')");
		if($count2)
		{
			$request_id = $DB->last_insert_id();
			$code = "REQ".$request_id;
			$DB->query("UPDATE ".root_table."store_request SET request_code ='{$code}', ret_id = '{$data_new['ret_id']}' WHERE request_id = '{$request_id}'");
			$data_request = $CMS->store_request->get_info($request_id);
			//Tao tai san
			$CMS->assets->addQuick($data_request);

			// Tao phieu chi

			$invoice_no = $CMS->transactions->getNoInvoice(6);
			$data_user = $CMS->user->get_info($data_new['user_assign']);
			$trx_terms = 15;
			$data_trx = array(
					"type" => 2, // Sale
					"sub" => 6, //bill
					"request_id" => $request_id,
					'cus_type' => 3,
					'cus_email' => "{$data_user['user_email']}",
					'user_assign' => $user_id_assign,
					'trx_status' => 3, // Close
					"trx_invoice_no" => $invoice_no,
					"trx_address" => $data_user['user_address'],
					"trx_terms" => $trx_terms,
					"trx_expiration_date" => date("d/m/Y",$time+$trx_terms*86400),
					"trx_total" => $data_new['ret_amount'],
					"user_id" => $user_id,
					"service_type" => 0, // 0: hàng hoá, 1: dich vu
					"trx_time" => $time,
					"trx_note" => "{$CMS->lang['msg_create_bill_for_import_bill']} {$data_new['ret_code']}",

				);

			$trx_id = $CMS->transactions->add($data_trx,1);

			//Add items
            $CMS->transactions->addItem($trx_id,[],1);

			$data_trans = $CMS->transactions->getInfo($trx_id);
			// Update trx_id cho store request
			$DB->query("UPDATE ".root_table."store_request SET trx_id = '{$trx_id}' WHERE request_id = '{$request_id}'");

			// Update returns
			$DB->query("UPDATE ".root_table."returns SET request_id ='{$request_id}', trx_id = '{$trx_id}', trx_id_fee = '{$trx_id_in}', ret_status = 1 WHERE ret_id = '{$data_new['ret_id']}'");

			$CMS->class->logs->key= "store_request_{$request_id}";
			$CMS->class->logs->insert("{$member['cus_username']} create <b>store_request #{$request_id}</b>");
			$_SESSION['msg'] .= $CMS->class->logs->insert("{$member['cus_username']} {$CMS->lang['update_msg_approve']} <b>{$data_new['ret_code']}</b> {$CMS->lang['msg_success']}</br/>");
			$_SESSION['msg'] .= "[{$member['user_display_name']}] đã tạo phiếu nhập hàng <b>{$data_request['request_code']}</b> {$CMS->lang['msg_success']}<br/>";
			if($data_id_in)
			{
				$_SESSION['msg'] .= "[{$member['user_display_name']}] đã tạo phiếu thu <b>{$data_id_in['trx_code']}</b> {$CMS->lang['msg_success']}<br/>";
			}
			$_SESSION['msg'] .= "[{$member['user_display_name']}] đã tạo phiếu chi <b>{$data_trans['trx_code']}</b> {$CMS->lang['msg_success']}<br/>";
			
		}
	}

}
?>