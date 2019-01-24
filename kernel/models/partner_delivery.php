<?php
if (!defined('IN_ROOT')) exit();

$CMS->partner_delivery = new partner_delivery1;
class partner_delivery1{
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

	public $cache_prefix = 'partner_delivery';
	

	public function add()
	{
		global $CMS, $DB, $member;
		
		// Input
		$p_delivery_name = trim($CMS->input['p_delivery_name']);
		$p_delivery_code = trim($CMS->input['p_delivery_code']);
		$p_delivery_phone = trim($CMS->input['p_delivery_phone']);
		$p_delivery_email = trim($CMS->input['p_delivery_email']);
 
		$p_delivery_type = intval($CMS->input['p_delivery_type']);
	 
		$p_delivery_address = trim($CMS->input['p_delivery_address']);
		$p_delivery_group = trim($CMS->input['p_delivery_group']); 
		$p_delivery_note = $CMS->class->editor->input('p_delivery_note');
		$p_delivery_website = $CMS->class->editor->input('p_delivery_website');

		$shipping_tracking_url = $CMS->class->editor->input('shipping_tracking_url');

		$user_id = $member['user_id'];
		$p_delivery_time = time();
		
		if(!$p_delivery_name)
		{
			$_SESSION['error_msg'] = $CMS->lang['partner_delivery_empty_name'];
			return false;
		}
		
		if(!$p_delivery_code)
		{
			$_SESSION['error_msg'] = $CMS->lang['partner_delivery_empty_code'];
			return false;
		}

		if ( $shipping_tracking_url AND ! $CMS->class->input->is_url($shipping_tracking_url) ) 
		{
		    $_SESSION['error_msg'] = $CMS->lang['shipping_tracking_url_err'];
			return false;
		}

        // Check exist code
		if($this->check_exist("p_delivery_code",$p_delivery_code) and $p_delivery_code)
		{
			$_SESSION['error_msg'] =$CMS->lang['partner_delivery_is_exist'];
			return false;
		}
		
		// Insert data
		$DB->query("INSERT INTO ".root_table."partner_delivery (p_delivery_name,p_delivery_code,p_delivery_phone,p_delivery_email,p_delivery_type,p_delivery_address,p_delivery_time,user_id,p_delivery_note,p_delivery_group, p_delivery_website, shipping_tracking_url) VALUES ('{$p_delivery_name}','{$p_delivery_code}','{$p_delivery_phone}','{$p_delivery_email}','{$p_delivery_type}','{$p_delivery_address}','{$p_delivery_time}','{$user_id}','{$p_delivery_note}', '{$p_delivery_group}', '{$p_delivery_website}', '{$shipping_tracking_url}')");
		
		$p_delivery_id = $DB->last_insert_id();
		$CMS->class->logs->key= "p_delivery_{$p_delivery_id}";
		$_SESSION['msg']=$CMS->class->logs->insert("{$CMS->lang['partner_delivery_add']}: {$p_delivery_name}</b>");
		
		$CMS->class->cache->mdelete($this->cache_prefix);
              
		$p_delivery = $this->get_info($p_delivery_id);
        return $p_delivery;
	}
	
	public function check_exist( $field, $value = "", $except_value = ""   )
	{
		global $CMS, $DB, $member;
		
		if ( ! $field )
		{
			return true;
		}

		$sql_add = '';

		if ( $except_value )
		{
            $sql_add .= "{$field}!='{$except_value}' AND";
		}


		$sql = "SELECT count(0) cnt FROM ".root_table."partner_delivery WHERE {$sql_add} {$field}='{$value}' AND  p_delivery_deleted=0";

        $result = $DB->fetch_data($sql, $this->cache_prefix);

        return $result[0]['cnt'];
	}
	
	public function listing() 
	{
		global $CMS,$DB,$member;

		if (!isset($this->html)) 
		{	
			$this->html = $CMS->class->template->load_template("skin_partner_delivery");
		}
		
		$this->arrange_data = trim("p_delivery_id,p_delivery_name,p_delivery_time,p_delivery_type,user_id");
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "p_delivery_id";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "DESC";
		
		$where='';
		if (($CMS->input['pd_name'])) {
			$where.=" AND `p_delivery_name` LIKE '%{$CMS->input['pd_name']}%'";
			$this->prefix_html.="&pd_name={$CMS->input['pd_name']}";
		}
		if (($CMS->input['pd_id'])) {
			$where.=" AND `p_delivery_id` = '{$CMS->input['pd_id']}' ";
			$this->prefix_html.="&pd_id={$CMS->input['pd_id']}";
		}
		 
		
		$this->prefix_html=empty($this->prefix_html)?'':'?site=partner_delivery'.$this->prefix_html.'&page=';

		$sql = "SELECT * FROM `".root_table."partner_delivery` {$sql_table} WHERE `p_delivery_deleted`=0 {$where} ORDER BY {$default_field} {$default_order}";

        list($this->show_page, $cacheData) = $DB->fetch_listing($sql, $this->per_page, $this->prefix_html, $this->suffix_html, $CMS->input['page'], $this->cache_prefix);
		
		$count = count($cacheData);

        $output = '';

		if ( $count > 0 ) 
		{
 
			foreach ($cacheData as $data)
			{
				$data = $this->convertvalue($data);
				 
				$output .= $this->html->mid($data);
			}
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
		$DB->query("UPDATE ".root_table."partner_delivery SET p_delivery_deleted=1 WHERE p_delivery_id={$data['p_delivery_id']}");

        $CMS->class->cache->mdelete($this->cache_prefix);
		
		// Create log
		$CMS->class->logs->key = "p_delivery_{$data['p_delivery_id']}";
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['partner_delivery_delete']} <b>{$data['p_delivery_name']}</b>");
		
         
                
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=partner_delivery&page={$CMS->input['page']}");
		
		return true;
	}
	public function edit() 
	{
		global $CMS, $DB, $member;
		
		$partner_delivery = $this->get_info();
		$p_delivery_id = $partner_delivery['p_delivery_id'];
		if(! is_array($partner_delivery))
		{
			$_SESSION['error_msg'] = $CMS->lang['partner_delivery_not_exist'];
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=partner_delivery");
		}
		// Input
		$p_delivery_name = trim($CMS->input['p_delivery_name']);
		$p_delivery_code = trim($CMS->input['p_delivery_code']);
		$p_delivery_phone = trim($CMS->input['p_delivery_phone']);
		$p_delivery_email = trim($CMS->input['p_delivery_email']);
 
		$p_delivery_type = intval($CMS->input['p_delivery_type']);
	 
		$p_delivery_address = trim($CMS->input['p_delivery_address']);
		$p_delivery_group = trim($CMS->input['p_delivery_group']);
		 
		$p_delivery_note = $CMS->class->editor->input('p_delivery_note');
		$p_delivery_website = $CMS->class->editor->input('p_delivery_website');
		
		$shipping_tracking_url = $CMS->class->editor->input('shipping_tracking_url'); 
		
		// Check exist name
		if($this->check_exist("p_delivery_name",$p_delivery_name,$partner_delivery['p_delivery_name']))
		{
			$_SESSION['error_msg'] = $CMS->lang['partner_delivery_is_exist'];
			return false;
		}

		if ( $shipping_tracking_url AND ! $CMS->class->input->is_url($shipping_tracking_url) ) 
		{
		    $_SESSION['error_msg'] = $CMS->lang['shipping_tracking_url_err'];
			return false;
		}
                
                // Check exist code
		if($this->check_exist("p_delivery_code",$p_delivery_code,$partner_delivery['p_delivery_code'] ) and $p_delivery_code)
		{
			$_SESSION['error_msg']  =$CMS->lang['partner_delivery_code_is_exist'];
			return false;
		}
		
	 	$CMS->class->logs->key = "p_delivery_{$p_delivery_id}";
        $CMS->class->logs->old = $partner_delivery;
		
		// Insert data
		$DB->query("UPDATE ".root_table."partner_delivery SET p_delivery_name='{$p_delivery_name}',p_delivery_code='{$p_delivery_code}',p_delivery_phone='{$p_delivery_phone}',p_delivery_email='{$p_delivery_email}' ,p_delivery_type='{$p_delivery_type}' ,p_delivery_address='{$p_delivery_address}' ,p_delivery_note='{$p_delivery_note}', p_delivery_group = '{$p_delivery_group}', p_delivery_website='{$p_delivery_website}', shipping_tracking_url = '{$shipping_tracking_url}'  where p_delivery_id='{$partner_delivery['p_delivery_id']}'");

        $CMS->class->cache->mdelete($this->cache_prefix);
        $data_new = $this->get_info($p_delivery_id);
		$CMS->class->logs->key= "p_delivery_{$p_delivery_id}";
        $CMS->class->logs->save_detail("partner_delivery",$$p_delivery_id,$data_new);
		$_SESSION['msg']=$CMS->class->logs->insert("{$CMS->lang['partner_delivery_edit']}  <b>{$p_delivery_name}</b>");
                
        return TRUE;
	}
	
	public function auto_run() {
		global $CMS, $DB, $member;
		
		if (!isset($this->html)) {	
			$this->html = $CMS->class->template->load_template("skin_partner_delivery");
		}
		
		if ($CMS->class->cache->check("user_{$member['user_id']}_p_delivery_{$CMS->vars['default_language']}")) {
			$CMS->vars['action_controller']=$CMS->class->cache->load("user_{$member['user_id']}_p_delivery_{$CMS->vars['default_language']}");
		} else {
			$data = "";
			if ($CMS->permit["p_delivery_search"]) {
				$data .=<<<EOF
<div class="tbl-cell tbl-cell-action-bordered" >
	<a href="{$CMS->vars['root_domain']}/?site=partner_delivery&act=search" title="{$CMS->lang['title_search_partner_delivery']}">
	<button type="button" class="action-btn"><i class="fa fa-search"></i></button>
  </a>
</div>
EOF;
				
				$this->control = 1;
			}
			if ($CMS->permit["pcategory_arrange"]) {
				$data .=<<<EOF
<div class="tbl-cell tbl-cell-action-bordered" act="arrange" id="glyphicon-sort" >
	<i class="fa fa-refresh" title="{$CMS->lang['title_arrange_partner_delivery']}"></i>
</div>
EOF;
				$this->control = 1;
			}
			if ($CMS->permit["pcategory_delete"]) {
				$data .=<<<EOF
<div class="tbl-cell tbl-cell-action-bordered" act="delete_all" id="font-icon-trash">
	<i class="fa fa-trash-o" title="{$CMS->lang['title_delete_all_partner_delivery']}"></i>
</div>
EOF;
				$this->control = 1;
			}
			if ($CMS->permit["pcategory_add"]) {
				$data .=<<<EOF
<div class="tbl-cell tbl-cell-action-bordered">
	<a href="{$CMS->vars['root_domain']}/?site=partner_delivery&act=add" title="{$CMS->lang['title_add_pcategory']}">
		<button type="button" class="action-btn"><i class="fa fa-plus-circle"></i></button>
	</a>
</div>
EOF;
				$this->control = 1;
			}
			$data = $this->control == 1 ?  $data : "";
			$CMS->class->cache->save("user_{$member['user_id']}_p_delivery_{$CMS->vars['default_language']}", $data);
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
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=partner_delivery{$str}");
	}




	public function get_info( $record_id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "partner_delivery" )
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

		$sql = "SELECT * FROM ".root_table."partner_delivery WHERE (p_delivery_id='{$record_id}' OR p_delivery_name='{$record_id}') AND p_delivery_deleted = 0 ORDER BY p_delivery_id DESC LIMIT 1";

		$data = $DB->fetch_data($sql, $this->cache_prefix)[0];

		if ($data)
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
		
		$data['p_delivery_type_bk'] = $CMS->lang["p_delivery_type_{$data['p_delivery_type']}"];
		$data['user_name'] = $CMS->user->get_info($data['user_id'],"user_name");
		$data['p_delivery_time_bk'] = $CMS->class->date->date_format($data['p_delivery_time'],1);
		
		 if($CMS->permit['partner_delivery_read'] == 1)
		 {
		 		$data['p_delivery_name_bk'] = "<a href='{$CMS->vars['root_domain']}/?site=partner_delivery&act=show&id={$data['p_delivery_id']}' >{$data['p_delivery_name']}</a>"; 
		 }
		 else
		 {
		 	$data['p_delivery_name_bk'] = $data['p_delivery_name'];
		 }
		return $data;
	}

	public function action($id=null) {
		if (!is_null($id)) {
			global $CMS, $DB, $member;
			$DB->query("SELECT `p_delivery_act`, p_delivery_title FROM `".root_table."partner_delivery` WHERE `cus_id`={$member['cus_id']} AND `p_delivery_id`={$id}");
			$data = $DB->fetch_array();
			if ($data['p_delivery_act']==2) {
				return FALSE;
			}
			$count = $DB->query("UPDATE `".root_table."partner_delivery` SET `{$CMS->input['action']}`='{$CMS->input['value']}' WHERE `cus_id`='{$member['cus_id']}' AND `p_delivery_id` = {$id}");
			if($count)
			{
				if($CMS->input['action'] == "p_delivery_deleted")
				{
					$_SESSION['msg'] = $CMS->lang['emsg_deleted_success_partner_delivery']. $data['p_delivery_title'];
				}else
				{
					$_SESSION['msg'] = $CMS->lang['emsg_update_success_partner_delivery']. $data['p_delivery_title'];
				}
			}else
			{
				$_SESSION['msg'] = $CMS->lang['emsg_update_error_partner_delivery']. $data['p_delivery_title'];
			}
			return true;
		}
		return false;
	}

    public function get_list_partner_delivery($id_select = 0) {
        global $CMS, $DB;

        $sql = "SELECT * FROM " . root_table . "partner_delivery WHERE p_delivery_deleted=0 ORDER BY p_delivery_name ASC";

        $cacheData = $DB->fetch_data($sql, $this->cache_prefix);
        $output = '<option value="">-- '.$CMS->lang['choose_carrier'].' --</option>';
        
        if($cacheData)
        {
            foreach ($cacheData as $data)
            {
                if ($id_select > 0)
                {
                    if ($data['p_delivery_id'] == $id_select)
                    {
                        $output .= "<option  value='{$data['p_delivery_id']}' selected  >{$data['p_delivery_name']}</option>";
                    }
                    else
                    {
                        $output .= "<option  value='{$data['p_delivery_id']}'  >{$data['p_delivery_name']}</option>";
                    }
                }
                else
                {
                    $output .= "<option  value='{$data['p_delivery_id']}'  >{$data['p_delivery_name']}</option>";
                }
            }
        }

        return $output;
    }

	  

	public function getOptionpartner_delivery($p_delivery_id=0)
	{
		global $CMS, $DB;

		$output = "<option value>{$CMS->lang['select_partner_delivery']}</option>";
		$sql = $DB->query("SELECT * FROM ".root_table."partner_delivery WHERE p_delivery_deleted = 0 AND p_delivery_status = 1 ORDER BY p_delivery_name ASC");
		if($DB->num_rows($sql) > 0)
		{
			while ($result = $DB->fetch_array($sql)) 
			{
				if($p_delivery_id == $result['p_delivery_id'])
				{
					$selected = "selected='selected'";
				}else
				{
					$selected = "";
				}
				$output .= "<option value='{$result['p_delivery_id']}' email='{$result['p_delivery_email']}' address='{$result['p_delivery_address']}' {$selected}>{$result['p_delivery_name']}</option>";
			}
		}

		return $output;
	}
	
}
?>