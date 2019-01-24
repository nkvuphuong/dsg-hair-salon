<?php
if (!defined('IN_ROOT')) exit();

$CMS->assets_category = new cat1;
class cat1{
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
	public $cache_prefix = 'assets_category'; //Tiền tố cho cache key hoặc tên file cache
	

	public function add()
	{
		global $CMS, $DB, $member;
		
		$cat_name = trim($CMS->input['cat_name']);
		$cat_status = intval($CMS->input['cat_status']);
		$user_id = $member['user_id'];
		$cat_time = time();
		
		if(!$cat_name)
		{
			$CMS->errormsg = $CMS->lang['cat_empty_name'];
			return false;
		}
		
		// Check exist
		if($this->check_exist("cat_name",$cat_name))
		{
			$CMS->errormsg=$CMS->lang['cat_is_exist'];
			return false;
		}
		
		// Insert data
		$DB->query("INSERT INTO ".root_table."assets_category (cat_time,user_id,cat_name,cat_status) VALUES ('{$cat_time}','{$user_id}','{$cat_name}','{$cat_status}')");
		
		$cat_id = $DB->last_insert_id();
		$CMS->class->logs->key= "cat_{$cat_id}";
		$_SESSION['msg']=$CMS->class->logs->insert("{$member['cus_username']} create <b>category {$cat_name}</b>");
		
		$CMS->class->cache->mdelete($this->cache_prefix);
                
		return TRUE;
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
			$DB->query("SELECT cat_id FROM ".root_table."assets_category WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND cat_deleted=0");
		}
		else
		{
			$DB->query("SELECT cat_id FROM ".root_table."assets_category WHERE {$field}='{$value}' AND cat_deleted=0");
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
	
	public function listing() 
	{
		global $CMS,$DB,$member;
		
		if (!isset($this->html)) 
		{	
			$this->html = $CMS->class->template->load_template("skin_assets_category");
		}
		
		$this->arrange_data = trim("cat_id,cat_name,cat_time,user_id");
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "cat_id";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "DESC";
		
		$where='';
		if (($CMS->input['sname'])) {
			$where.=" AND `cat_name` LIKE '%{$CMS->input['sname']}%'";
			$this->prefix_html.="&sname={$CMS->input['sname']}";
		}
		if (($CMS->input['sid'])) {
			$where.=" AND cat_id='{$CMS->input['sid']}'";
			$this->prefix_html.="&sid={$CMS->input['sid']}";
		}

        $this->prefix_html=empty($this->prefix_html)?'':'?site=store'.$this->prefix_html.'&page=';
		$sql = "SELECT * FROM `".root_table."assets_category` WHERE `cat_deleted`=0 {$where} ORDER BY {$default_field} {$default_order}";

		//Query
        list($CMS->show_page, $results) = $DB->fetch_listing($sql, $this->per_page, $this->prefix_html, $this->suffix_html, $CMS->input['page'], $this->cache_prefix);

        $output = '';

        if($results)
        {
            foreach ($results as $data)
            {
                $data = $this->convertvalue($data);
                $output .= $this->html->mid($data);
            }
        }
        else
        {
            $output .= $this->html->none();
        }

		return $output;
	}

	public function info($id=null){

        global $CMS, $DB, $member;

	    if(!$id) return false;

	    $sql = "SELECT * FROM `".root_table."store` WHERE `cat_deleted`=0 AND `cus_id`='{$member['cus_id']}' AND `cat_id`={$id} LIMIT 1";

	    $data = $DB->fetch_data($sql, $this->cache_prefix)[0];

        return $data;
	}
	public function delete()
	{
		global $CMS, $DB;
		
		// Get info
		$data = $this->get_info();
		
		// Check existing
		if ( ! $data ) { return false; }
		
		// Update info
		$DB->query("UPDATE ".root_table."assets_category SET cat_deleted=1 WHERE cat_id={$data['cat_id']}");
		
		// Create log
		$CMS->class->logs->key = "cat_{$data['cat_id']}";
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['cat_deleted']} <b>{$data['cat_name']}</b>");

        $CMS->class->cache->mdelete($this->cache_prefix);
                
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=assets_category&page={$CMS->input['page']}");
		
		return true;
	}
	
	public function edit() 
	{
		global $CMS, $DB, $member;
		
		$cat = $this->get_info();
		
		$cat_name = trim($CMS->input['cat_name']);
		$cat_status = intval($CMS->input['cat_status']);
		
		if(!$cat_name)
		{
			$CMS->errormsg = $CMS->lang['cat_err_name'];
			return false;
		}
		
		// Check exist
		if($this->check_exist("cat_name",$cat_name,$cat['cat_name']))
		{
			$CMS->errormsg=$CMS->lang['cat_is_exist'];
			return false;
		}
		
		// Insert data
		$DB->query("UPDATE ".root_table."assets_category SET cat_name='{$cat_name}',cat_status='{$cat_status}' WHERE cat_id='{$cat['cat_id']}'");
		
		$CMS->class->logs->key= "cat_{$cat['cat_id']}";
		$_SESSION['msg']=$CMS->class->logs->insert("{$member['cus_username']} edited <b>category {$cat_name}</b>");

        $CMS->class->cache->mdelete($this->cache_prefix);

		return TRUE;
	}
	
	public function auto_run() {
		global $CMS, $DB, $member;
		
		if (!isset($this->html)) {	
			$this->html = $CMS->class->template->load_template("skin_store");
		}
		
		if ($CMS->class->cache->check("user_{$member['user_id']}_cat_{$CMS->vars['default_language']}")) {
			$CMS->vars['action_controller']=$CMS->class->cache->load("user_{$member['user_id']}_cat_{$CMS->vars['default_language']}");
		} else {
			$data = "";
			if ($CMS->permit["cat_search"]) {
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
			$CMS->class->cache->save("user_{$member['user_id']}_cat_{$CMS->vars['default_language']}", $data);
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

		if ( ! $record_id AND $CMS->input['site'] == "assets_category" )
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

		$sql = "SELECT * FROM ".root_table."assets_category WHERE (cat_id='{$record_id}' OR cat_name='{$record_id}') AND cat_deleted = 0 ORDER BY cat_id DESC LIMIT 1";

        $data = $DB->fetch_data($sql, $this->cache_prefix)[0];

        return $data;
	}

	public function convertvalue($data) 
	{
		global $CMS;
		
		$data['cat_status'] = $CMS->lang["cat_status_{$data['cat_status']}"];
		$data['user_name'] = $CMS->user->get_info($data['user_id'],"user_name");
		$data['cat_time'] = $CMS->class->date->date_format($data['cat_time'],1);
		
		return $data;
	}

	public function action ($id=null) {
		if (!is_null($id)) {
			global $CMS, $DB, $member;
			$DB->query("SELECT `cat_act`, cat_title FROM `".root_table."store` WHERE `cus_id`={$member['cus_id']} AND `cat_id`={$id}");
			$data = $DB->fetch_array();
			if ($data['cat_act']==2) {
				return FALSE;
			}
			$count = $DB->query("UPDATE `".root_table."store` SET `{$CMS->input['action']}`='{$CMS->input['value']}' WHERE `cus_id`='{$member['cus_id']}' AND `cat_id` = {$id}");

			//Clear cache
            $CMS->class->cache->mdelete($CMS->store->cache_prefix);

			if($count)
			{
				if($CMS->input['action'] == "cat_deleted")
				{
					$_SESSION['msg'] = $CMS->lang['emsg_deleted_success_store']. $data['cat_title'];
				}else
				{
					$_SESSION['msg'] = $CMS->lang['emsg_update_success_store']. $data['cat_title'];
				}
			}else
			{
				$_SESSION['msg'] = $CMS->lang['emsg_update_error_store']. $data['cat_title'];
			}
			return true;
		}
		return false;
	}
	
	public function get_list_cat($defaultvalue="")
	{
		global $CMS, $DB;

		$sql = "SELECT * FROM ".root_table."assets_category WHERE cat_deleted=0";

		$cacheData = $DB->fetch_data($sql, $this->cache_prefix, 0);

		$row = count($cacheData);
		if($row >= 3)
		{
			$output = "<option value=''>{$CMS->lang['select']}</option>";
		}

		$i = 1;

		foreach($cacheData as $data)
		{
			if($row >= 3)
			{
				$selected = ($defaultvalue AND $defaultvalue == $data['cat_id']) ? "selected" : "";
				$output .= "<option value='{$data['store_id']}' {$selected}>{$data['cat_name']}</option>";
			}
			else
			{
				if($defaultvalue)
				{
					$checked = ($defaultvalue AND $defaultvalue == $data['cat_id']) ? "checked" : "";
				}else
				{
					$checked = $i == 1 ? "checked" : "";
				}

				$output .=<<<EOF
					<div class="radio">
						<input type="radio" {$checked} name="cat_id" id="radio-{$data['cat_id']}" value="{$data['cat_id']}">
						<label for="radio-{$data['cat_id']}">{$data['cat_name']}</label>
					</div>
EOF;

			}
			$i++;
		}

	 	$data = array($row, $output);

        return $data;
	}

	
}
?>