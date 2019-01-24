<?php
if (!defined('IN_ROOT')) exit();

$CMS->shipment = new class_shipment;
class class_shipment{
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
			$this->html = $CMS->class->template->load_template("skin_shipment");		
		}
	}

	//===========================================================================
	//  AUTO RUN
	//===========================================================================

	public function auto_run_shipment()
	{
		global $CMS, $DB, $member;

		$this->loadhtml();

		//-----------------------------------------------------------
		// ACTION CONTROLLER
		//-----------------------------------------------------------
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_shipment_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_shipment_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";

			// Check permission to Delete
			if ( $CMS->permit["shipment_delete"] == true )
			{
				$data .= "<option value='delete_all'>Xóa lô hàng đã chọn</option>";
				$this->control = 1;
			}
			
			$data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

			$CMS->class->cache->save("user_{$member['user_id']}_shipment_controller_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}
		
		if ( $CMS->vars['action_controller']  OR $CMS->permit["order_search"] == 1 )
		{
			//$this->action_control = $this->html->shipment_control();
		}
		
	 
	}


	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("shi_id,shi_name,shi_time");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "shi_id";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "desc";
		
		// SQL Condition
		$this->sql_add .= " shi_deleted=0 AND ";	
 
		$where = '';
		if (!empty($CMS->input['shi_name']) ) {
			$shi_name = urldecode($CMS->input['shi_name']);
			$where .= " AND `shi_name` LIKE '%{$shi_name}%' ";
		}
		if (!empty($CMS->input['shi_id']) && Validate::isNum($CMS->input['shi_id'])) {
			$where .= " AND `shi_id`='{$CMS->input['shi_id']}'  ";
		}
 

		// Create SQL Query for listing Data
		list($this->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."shipment WHERE {$this->sql_add} 1=1  {$where}  ORDER BY {$default_field} {$default_order}");
		$this->pages_cnt = $DB->num_rows( $this->sql_query );
	}

	public function html()
	{
		global $CMS, $DB, $member;

		$this->loadhtml();

		// Display Header
		$output .= $this->html->header();
		
		if ( $DB->num_rows( $CMS->shipment->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->shipment->sql_query ) )
			{
				// Convert info
				$result = $CMS->shipment->convertvalue($result);
				
				// Display Middle
				$output .= $this->html->middle($result);
			}
		}
		else
		{
			// Display No data
			//$output .= $this->html->none();

			// No data
			$CMS->is_error = 1;
		}
		
		// Display
		$output .= $this->html->footer();
		
		// If the page is giving no data
		if ( $CMS->input['page'] > 1 && $CMS->is_error == 1)
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=shipment");
		}
		
		return $output;
	}

	public function shi_delete()
	{
		global $CMS, $DB, $member;

		$data = $this->get_info();
		
		// Check existing
		if ( ! $data ) { return false; }

		$c_asset = $this->count_product_byShipment($data['shi_id']);

 
		if($c_asset > 0)
		{
			$_SESSION['error_msg'] = "Tồn tại {$c_asset} tài sản thuộc lô hàng này. <a href='{$CMS->vars['root_domain']}/?site=assets&act=search&shi_id={$CMS->input['id']}'>[Danh sách tài sản]</a>";
			return false;

		}


		// Update info
		$DB->query("UPDATE   ".root_table."shipment SET shi_deleted = 1 WHERE shi_id={$data['shi_id']}");
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['shipment_deleted']} <b>{$data['shi_name']}</b>")."<br />";
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=shipment&page={$CMS->input['page']}");
		
		return true;


	}

	public function acp_add()
	{
		global $CMS, $DB, $member;
		
		$store_name = trim($CMS->input['store_name']);
		$store_type = intval($CMS->input['store_type']);
		$user_id = $member['user_id'];
		$store_time = time();
		
		if(!$store_name)
		{
			$CMS->errormsg = $CMS->lang['store_empty_name'];
			return false;
		}
		
		// Check input
		if (!$store_type) 
		{
			$CMS->errormsg=$CMS->lang['store_empty_type'];
			return false;
		}
		
		// Check exist
		if($this->check_exist("store_name",$store_name))
		{
			$CMS->errormsg=$CMS->lang['store_is_exist'];
			return false;
		}
		
		// Insert data
		$DB->query("INSERT INTO ".root_table."store (store_time,user_id,store_name,store_type) VALUES ('{$store_time}','{$user_id}','{$store_name}','{$store_type}')");
		
		$store_id = $DB->last_insert_id();
		$CMS->class->logs->key= "store_{$store_id}";
		$_SESSION['msg']=$CMS->class->logs->insert("{$member['cus_username']} create <b>store {$store_name}</b>");
		
                $CMS->class->cache->delete("store_list");
                
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
			$DB->query("SELECT shi_id FROM ".root_table."shipment WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND shi_deleted=0");
		}
		else
		{
			$DB->query("SELECT shi_id FROM ".root_table."shipment WHERE {$field}='{$value}' AND shi_deleted=0");
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
	
	public function acp_listing() 
	{
		global $CMS,$DB,$member;
 
		if (!isset($this->html)) 
		{	
			$this->html = $CMS->class->template->load_template("skin_store");
		}
		
		$this->arrange_data = trim("store_id,store_name,store_time,store_type,user_id");
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "store_id";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "DESC";
		
		$where='';
		if (($CMS->input['sname'])) {
			$where.=" AND `store_name` LIKE '%{$CMS->input['sname']}%'";
			$this->prefix_html.="&sname={$CMS->input['sname']}";
		}
		if (($CMS->input['sid'])) {
			$where.=" AND store_id='{$CMS->input['sid']}'";
			$this->prefix_html.="&sid={$CMS->input['sid']}";
		}
		
		$this->prefix_html=empty($this->prefix_html)?'':'?site=store'.$this->prefix_html.'&page=';
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM `".root_table."store` WHERE `store_deleted`=0 {$where} ORDER BY {$default_field} {$default_order}", $this->per_page, $this->prefix_html, $this->suffix_html);
		
		if ($DB->num_rows($this->sql_query)>0) 
		{
			while ($data=$DB->fetch_array($this->sql_query)) 
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
		if(!is_null($id)){
			global $CMS, $DB, $member;
			$DB->query("SELECT * FROM `".root_table."store` WHERE `store_deleted`=0 AND `cus_id`='{$member['cus_id']}' AND `store_id`={$id}");
			if ($DB->num_rows()>0) {
				return $DB->fetch_array();
			}
		}
		return array();
	}
	public function acp_info($id=null){
		if(!is_null($id)){
			global $CMS, $DB, $member;
			$DB->query("SELECT * FROM `".root_table."store` WHERE `store_deleted`=0 AND `store_id`={$id}");
			if ($DB->num_rows()>0) {
				return $DB->fetch_array();
			}
		}
		return array();
	}
	public function del(){
		global $CMS, $DB, $member;

	
		$DB->query("UPDATE `".root_table."store` SET `store_deleted`=1 WHERE `cus_id`='{$member['cus_id']}' AND `store_id` = {$CMS->input['id']}");
		$_SESSION["msg"]=$CMS->lang['store_del_success'];
		return TRUE;
	}
	public function acp_del()
	{
		global $CMS, $DB;
		
		// Get info
		$data = $this->get_info();
		
		// Check existing
		if ( ! $data ) { return false; }
		
		// Update info
		$DB->query("UPDATE ".root_table."store SET store_deleted=1 WHERE store_id='{$data['store_id']}'");
		
		// Create log
		$CMS->class->logs->key = "store_{$data['store_id']}";
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['store_deleted']} <b>{$data['store_name']}</b>");
		
                $CMS->class->cache->delete("store_list");
                
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store&page={$CMS->input['page']}");
		
		return true;
	}
	public function del_all(){
		global $CMS, $DB, $member;
		if(is_array($CMS->input['id_del'])) {
			foreach ($CMS->input['id_del'] as $id){
				$DB->query("UPDATE `".root_table."store` SET `store_deleted`=1 WHERE `cus_id`='{$member['cus_id']}' AND `store_id` = {$id}");
			}
		}
		die ('success');
	}
	public function acp_del_all(){
		global $CMS, $DB, $member;
		if(is_array($CMS->input['id_del'])) {
			foreach ($CMS->input['id_del'] as $id){
				$DB->query("UPDATE `".root_table."store` SET `store_deleted`=1 WHERE `store_id` = {$id}");
				$CMS->class->logs->insert("{$member['cus_username']} deleted <b>store {$id}</b>");
			}
		}
		die ('success');
	}
	public function edit($id=null) {
		if(!is_null($id)) {
			global $CMS, $DB, $member;
			$DB->query("SELECT `store_act` FROM `".root_table."store` WHERE `cus_id`={$member['cus_id']} AND `store_id`={$id}");
			if ($DB->num_rows()>0) {
				if ($DB->fetch_array()['store_act']==2) {
					$CMS->errormsg="{$CMS->lang['store_lock_err']}";
					return false;
				}
			} else return false;
			
			$store_title=$CMS->input['store_title'];
			$store_des=substr($CMS->input['store_des'],0,1000);
			$free_sub=$CMS->input['free_sub']=='sub'?1:0;
			if ($free_sub==1) {
				$store_url='';
				$store_url_sub=rtrim($CMS->input['store_url_sub'],'/');
			} else {
				$store_url=rtrim($CMS->input['store_url'],'/');
				$store_url_sub='';
			}
			if (empty($store_url) AND empty($store_url_sub)) {
				return false;
			}
			$store_cam_pub=is_array($CMS->input['store_cam_pub'])?$CMS->input['store_cam_pub']:array();
			foreach ($CMS->input['store_cam'] as $cam) {
				array_push($store_cam_pub,$cam);
			}
			$store_cam_pri=is_array($CMS->input['store_cam_pri'])?$CMS->input['store_cam_pri']:array();
			$store_cam=implode(",", $CMS->input['store_cam']);
			$store_cam = ",".$store_cam.",";
			// $store_tags=implode(",", $CMS->input['store_tags']);
			// $store_tags = ",".$store_tags.",";
			$store_ban=isset($_FILES['store_ban'])?$_FILES['store_ban']['name']:'';
			$store_logo=isset($_FILES['store_logo'])?$_FILES['store_logo']['name']:'';
			$store_pri=(isset($CMS->input['store_pri'])&&$CMS->input['store_pri']=='on')?1:0;
			$store_act=(isset($CMS->input['store_act'])&&$CMS->input['store_act']=='on')?1:0;
			$store_hide=(isset($CMS->input['store_hide'])&&$CMS->input['store_hide']=='on')?1:0;
			// Check input
			if (strlen($store_title)<3||strlen($store_title)>40) {
				$CMS->errormsg="{$CMS->lang['store_err_title']}";
				return false;
			}
			if (!empty($store_url)) {
				if (!(bool)preg_match("/^[0-9a-zA-Z-_]+$/",$store_url)||strlen($store_url)<3||strlen($store_url)>20) {
					$CMS->errormsg="{$CMS->lang['store_err_url']}";
					return false;
				}
				$DB->query("SELECT `store_id` FROM `".root_table."store` WHERE `store_url`='{$store_url}' AND `store_id`!={$id}");
				if($DB->num_rows()>0) {
					$CMS->errormsg="{$CMS->lang['store_exist_url']}";
					return FALSE;
				}
			}
			// Check upload
			if(!empty($store_ban)) {
				if (!$CMS->class->attachment->check_is_image($_FILES['store_ban']['tmp_name'])) {
					$CMS->errormsg="{$CMS->lang['store_war_ban']}";
					return false;
				}
				if ($_FILES['store_ban']['size']>(5*1024*1024)) {
					$CMS->errormsg="{$CMS->lang['store_war_ban']}";
					return false;
				}
				// $image_info=getimagesize($_FILES['store_ban']['tmp_name']);
				// if ($image_info[0]>1080 || $image_info[1]>230) {
					// $CMS->errormsg="{$CMS->lang['store_war_ban']}";
					// return false;
				// }
				$tmp=empty($store_url)?$store_url_sub:$store_url;
				$tmp=preg_replace('/[^a-zA-Z0-9]/','',$tmp);
				$new_image="/store/{$CMS->class->image->check_folder_img("store","",1,"")}/{$tmp}-ban.{$imageFileType}";
				if (!move_uploaded_file($_FILES['store_ban']['tmp_name'], $CMS->vars['upload_dir'].$new_image)) {
					return FALSE;
				} else {
					$store_ban=$new_image;
					$DB->query("SELECT `store_ban` FROM `".root_table."store` WHERE `cus_id`='{$member['cus_id']}' AND `store_id` = {$id} AND `store_ban` <>'{$store_ban}'");
					if ($DB->num_rows()>0) {
					while ($data=$DB->fetch_array()) {
							unlink($CMS->vars['upload_dir'].$data['store_ban']);
						}
					}
				}
			}
			if(!empty($store_logo)) {
				if (!$CMS->class->attachment->check_is_image($_FILES['store_logo']['tmp_name'])) {
					$CMS->errormsg="{$CMS->lang['store_war_ban']}";
					return false;
				}
				if ($_FILES['store_logo']['size']>(5*1024*1024)) {
					$CMS->errormsg="{$CMS->lang['store_war_ban']}";
					return false;
				}
				// $image_info=getimagesize($_FILES['store_ban']['tmp_name']);
				// if ($image_info[0]>1080 || $image_info[1]>230) {
					// $CMS->errormsg="{$CMS->lang['store_war_ban']}";
					// return false;
				// }
				$tmp=empty($store_url)?$store_url_sub:$store_url;
				$tmp=preg_replace('/[^a-zA-Z0-9]/','',$tmp);
				$new_image="/store/{$CMS->class->image->check_folder_img("store","",1,"")}/{$tmp}-logo.{$imageFileType}";
				if (!move_uploaded_file($_FILES['store_logo']['tmp_name'], $CMS->vars['upload_dir'].$new_image)) {
					return FALSE;
				} else {
					$store_logo=$new_image;
					$DB->query("SELECT `store_logo` FROM `".root_table."store` WHERE `cus_id`='{$member['cus_id']}' AND `store_id` = {$id} AND `store_logo` <>'{$store_logo}'");
					if ($DB->num_rows()>0) {
					while ($data=$DB->fetch_array()) {
							unlink($CMS->vars['upload_dir'].$data['store_logo']);
						}
					}
				}
			}
			if (!empty($store_url_sub)) {
				if (!preg_match("/^$|(http(s)?:\/\/)(www\.)?(.)*[\.](.)*$/i",$store_url_sub)) {
					$CMS->errormsg="{$CMS->lang['store_url_err']}";
					return false;
				}
				$DB->query("SELECT `store_id` FROM `".root_table."store` WHERE `store_url_sub`='{$store_url_sub}' AND `store_id`!={$id}");
				if($DB->num_rows()>0) {
					$CMS->errormsg="{$CMS->lang['store_exist_url_sub']}";
					return FALSE;
				}
			}
			/*
			$DB->query("SELECT `store_id`,`store_url_sub` FROM `".root_table."store` WHERE `store_id`={$id} ");
			if ($DB->num_rows()>0) {
				$data=$DB->fetch_array();
				if ($data['store_url_sub']!=$store_url_sub) {
					$data['str_store_url_sub']=rtrim(preg_replace("/(http(s)?:\/\/)(www\.)?/","",$data['store_url_sub']),'/');
					$str=file_get_contents(root_path.'zone/vhost.conf');
					$start=strpos($str,'#start'.$data['str_store_url_sub']);
					$end=strpos($str,'#end'.$data['str_store_url_sub'])+strlen('#end'.$data['str_store_url_sub']);
					$str1=substr($str,0,$start);
					$str2=substr($str,$end);
					file_put_contents(root_path.'zone/vhost.conf',$str1.$str2);
				}
			}
			if (!empty($store_url_sub)&&$data['store_url_sub']!=$store_url_sub) {
				$DB->query("SELECT `store_id` FROM `".root_table."store` WHERE `store_url_sub`='{$store_url_sub}'");
				if ($DB->num_rows()>0) {
					$CMS->errormsg="{$CMS->lang['store_exist_url_sub']}";
					return FALSE;
				}
				$str_store_url_sub=rtrim(preg_replace("/(http(s)?:\/\/)(www\.)?/","",$store_url_sub),'/');
				$str=<<<EOF
\n
#start{$str_store_url_sub}
<VirtualHost *:80>
	DocumentRoot "{$CMS->vars['root_document']}/cus_store"
	ServerName {$str_store_url_sub}
	ErrorLog "logs/{$str_store_url_sub}.log"
	CustomLog "logs/{$str_store_url_sub}-access.log" common
</VirtualHost>
#end{$str_store_url_sub}
EOF;
				$fp=fopen(root_path.'zone/vhost.conf','a') or exit('not open file httpd.conf');
				fwrite($fp, $str);
				fclose($fp);
			}
			*/
			// update data
			
			$store_ban=empty($store_ban)?"":"`store_ban`='{$store_ban}',";
			$store_logo=empty($store_logo)?"":"`store_logo`='{$store_logo}',";
			$DB->query("UPDATE `".root_table."store` SET `store_time_up`=".time().",`store_title`='{$store_title}',`store_des`='{$store_des}',`store_url`='{$store_url}',`store_cam`='{$store_cam}', {$store_ban} {$store_logo} `store_pri`={$store_pri},`store_act`={$store_act},`store_hide`={$store_hide},`store_url_sub`='{$store_url_sub}',`store_url_default`={$free_sub} WHERE `cus_id`='{$member['cus_id']}' AND `store_id` = {$id}");

			$cam_id=array();
			// $tags_id=array();
			$DB->query("DELETE FROM `".root_table."scampaigns` WHERE `store_id` = {$id}");
			// $DB->query("DELETE FROM `".root_table."link_tags_store` WHERE `store_id` = {$id}");
			foreach ($CMS->input['store_cam'] as $value) {
				$DB->query("INSERT INTO ".root_table."scampaigns (cam_id, store_id) VALUES ('{$value}', '{$id}')");
				array_push($cam_id,$value);
				// $DB->query("INSERT INTO `".root_table."link_tags_store` (`tags_id`, `store_id`) VALUES ('{$value}', '{$id}')");
				// array_push($tags_id,$value);
			}
			$cam_id=json_encode($cam_id);
			// $tags_id=json_encode($tags_id);
			$DB->query("UPDATE `".root_table."domain_store` SET `do_store_domain`='{$store_url_sub}',`do_store_cam_id`='{$cam_id}',`do_store_cus_id`='{$member['cus_id']}' WHERE `do_store_store_id`='{$id}'");
			if (isset($_SESSION['is_mobile'])&&$_SESSION['is_mobile']==0) {
				if (count($store_cam_pub)) {
					foreach ($store_cam_pub as $cam) {
						$DB->query("UPDATE `".root_table."campaigns` SET `cam_is_private`=0 WHERE `cam_id`={$cam}");  
					}					
				}
				if (count($store_cam_pri)) {
					foreach ($store_cam_pri as $cam) {
						$DB->query("UPDATE `".root_table."campaigns` SET `cam_is_private`=1 WHERE `cam_id`={$cam}");
					}					
				}
			}
			// Create log
			$this->vhost();
			$_SESSION["msg"]=$CMS->lang['emsg_update_success_store'].$store_title;
			return true;
		}
		return FALSE;
	}
	public function acp_edit() 
	{
		global $CMS, $DB, $member;
		
		$store = $this->get_info();
		
		$store_name = trim($CMS->input['store_name']);
		$store_type = intval($CMS->input['store_type']);
		
		if(!$store_name)
		{
			$CMS->errormsg = $CMS->lang['store_empty_name'];
			return false;
		}
		
		// Check input
		if (!$store_type) 
		{
			$CMS->errormsg=$CMS->lang['store_empty_type'];
			return false;
		}
		
		// Check exist
		if($this->check_exist("store_name",$store_name,$store['store_name']))
		{
			$CMS->errormsg=$CMS->lang['store_is_exist'];
			return false;
		}
		
		// Insert data
		$DB->query("UPDATE ".root_table."store SET store_name='{$store_name}',store_type='{$store_type}' WHERE store_id='{$store['store_id']}'");
		
		$CMS->class->logs->key= "store_{$store['store_id']}";
		$_SESSION['msg']=$CMS->class->logs->insert("{$member['cus_username']} edited <b>store {$store_name}</b>");
		
                $CMS->class->cache->delete("store_list");

                return TRUE;
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



	public function get_option_store($cus_id=0, $type="")
	{
		global $CMS, $DB;

		$output = "";
		$sql = $DB->query("SELECT * FROM ".root_table."store WHERE store_deleted = 0 AND cus_id = '{$cus_id}' ORDER BY store_time_creat DESC");

		if($DB->num_rows($sql) > 0)
		{
			while ($result = $DB->fetch_array($sql)) 
			{
				
				$output_list .= "\"{$result['store_id']}\",";
				$output .=<<<EOF
					<option value='{$result['store_id']}'>{$result['store_title']}</option>
EOF;
					
			}

			$output_list = rtrim($output_list,",");
		}

		if($type)
		{
			return array($output, $output_list);
		}else
		{
			return $output;
		}
	}

	public function get_info( $record_id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "shipment" )
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
		
		$sql = $DB->query("SELECT * FROM ".root_table."shipment WHERE (shi_id='{$record_id}' OR shi_name='{$record_id}') AND shi_deleted = 0 ORDER BY shi_id DESC LIMIT 1");

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

	public function convertvalue($data) 
	{
		global $CMS;
		
		$data['store_type'] = $CMS->lang["store_type_{$data['store_type']}"];
		$data['user_name'] = $CMS->user->get_info($data['user_id'],"user_name");
		$data['store_time'] = $CMS->class->date->date_format($data['store_time'],1);


		$data["shi_description_bk"] = $CMS->class->editor->substr($data['shi_description'],0,50);
		$data["shi_time_bk"] = $CMS->class->date->date_format($data['shi_time'],1);
		if($CMS->permit['assets_read'] == 1)
		{
			$data["shi_name_bk"] = "<a href=\"{$CMS->vars['root_domain']}/?site=assets&shi_id={$data['shi_id']}\">{$data["shi_name"]}</a>";
		}
		else
		{
			$data["shi_name_bk"] = $data["shi_name"];
		}

		$data['shi_quantity'] = $this->count_product_byShipment($data['shi_id']);
		return $data;
	}

	public function action ($id=null) {
		if (!is_null($id)) {
			global $CMS, $DB, $member;
			$DB->query("SELECT `store_act`, store_title FROM `".root_table."store` WHERE `cus_id`={$member['cus_id']} AND `store_id`={$id}");
			$data = $DB->fetch_array();
			if ($data['store_act']==2) {
				return FALSE;
			}
			$count = $DB->query("UPDATE `".root_table."store` SET `{$CMS->input['action']}`='{$CMS->input['value']}' WHERE `cus_id`='{$member['cus_id']}' AND `store_id` = {$id}");
			if($count)
			{
				if($CMS->input['action'] == "store_deleted")
				{
					$_SESSION['msg'] = $CMS->lang['emsg_deleted_success_store']. $data['store_title'];
				}else
				{
					$_SESSION['msg'] = $CMS->lang['emsg_update_success_store']. $data['store_title'];
				}
			}else
			{
				$_SESSION['msg'] = $CMS->lang['emsg_update_error_store']. $data['store_title'];
			}
			return true;
		}
		return false;
	}
	
	public function get_list_store()
	{
		global $CMS, $DB;
		
		if($CMS->class->cache->check("store_list"))
		{
			$data = $CMS->class->cache->load("store_list");
			return $data;
		}
		
		$sql = $DB->query("SELECT * FROM ".root_table."store WHERE store_deleted=0");
		
		$output = "<option value=''>{$CMS->lang['select']}</option>";	
		
		while($data = $DB->fetch_array($sql))
		{
			$output .= "<option value='{$data['store_id']}'>{$data['store_name']}</option>";	
		}
		
		$CMS->class->cache->save("store_list",$output);
		
		return $output;
	}


	// Tanlv 17/02
	public function searchKey($key='', $type=1)
	{
		global $CMS, $DB;

		if($type)
		{
			$data = array(0 => array("value" => "Add new", "id" => "add_new"));
		}
		
		// if($key)
		{
			$sql = $DB->query("SELECT * FROM ".root_table."shipment WHERE shi_name LIKE '%{$key}%' AND shi_deleted = 0 LIMIT 10");
			if($DB->num_rows($sql) > 0)
			{
				while ($result = $DB->fetch_array($sql)) 
				{
					$arr['value'] = $result['shi_name'];
					$arr['id'] = $result['shi_id'];
					$data[] = $arr;
				}
			}
		}
		return $data;
	}

	public function addAjax()
	{
		global $CMS, $DB, $member;

		$shi_name = $CMS->input['shi_name'];
		$shi_description = $CMS->class->editor->input("shi_description");
		if($shi_name)
		{
			if($this->check_exist("shi_name", $shi_name))
			{
				$data = $this->get_info($shi_name);
				print json_encode(array("status" => "error", "msg" => "Lô hàng này đã tồn tại", "data" => $data));exit;
			}else
			{
				$user_id = $member['user_id'];
				$shi_time = time();
				$count = $DB->query("INSERT INTO ".root_table."shipment (shi_name, user_id, shi_time, shi_description) VALUES ('{$shi_name}', '{$user_id}', '{$shi_time}', '{$shi_description}')");
				$shi_id = $DB->last_insert_id();
				if($count)
				{
					$data = $this->get_info($shi_id);
					return $data;
				}else
				{
					return false;
				}
			}
		}
	}

	public function editAjax()
	{
		global $CMS, $DB, $member;

		$shi_id = intval($CMS->input['shi_id']);
		$shi_name = $CMS->input['shi_name'];
		$shi_description = $CMS->class->editor->input("shi_description");
		$data = $this->get_info($shi_id);
		$CMS->class->logs->key= "shipment_{$supplier_id}";
		$CMS->class->logs->old_data = $data;
		// Check input
		if(!$shi_name)
		{
			print json_encode(array("status" => "error", "msg" => "Bạn chưa nhập tên lô hàng"));exit;
		}

		if($shi_id)
		{
			if($this->check_exist("shi_name", $shi_name, $data['shi_name']))
			{
				print json_encode(array("status" => "error", "msg" => "Lô hàng này đã tồn tại"));exit;
			}else
			{
				$user_id = $member['user_id'];
				$shi_time_update = time();
				$count = $DB->query("UPDATE ".root_table."shipment SET shi_name = '{$shi_name}', shi_description = '{$shi_description}', shi_time_update = '{$shi_time_update}' WHERE shi_id = '{$shi_id}'");

				if($count)
				{
					$data_new = $this->get_info($shi_id);
					$CMS->class->logs->key= "shipment_{$shi_id}";
					$CMS->class->logs->save_detail("shipment",$supplier_id, $data_new);
			        // Delete cache
			        $CMS->class->cache->delete("shipment_list");
	        
					return $data_new;
				}else
				{
					return false;
				}
			}
		}else
		{
			print json_encode(array("status" => "error", "msg" => "Không tìm thấy thông tin lô hàng này"));exit;
		}
	}

	public function quickadd($data=array())
	{
		global $CMS, $DB;

		if(is_array($data))
		{

			$DB->query("INSERT INTO ".root_table."shipment (shi_name, user_id, shi_time) VALUES ('{$data['shi_name']}', '{$data['user_id']}', '{$data['shi_time']}')");
			$shi_id = $DB->last_insert_id();
			return $shi_id;
		}else
		{
			return 0;
		}
	}


	public function count_product_byShipment($shi_id = "")
	{
		global $CMS, $DB;

		if($shi_id != "")
		{

			$sql = $DB->query("SELECT * FROM ".root_table."assets WHERE shi_id  = '{$shi_id}'   AND parent_id=0 AND ass_deleted = 0");
			$count = $DB->num_rows($sql);
			return $count;
		}else
		{
			return 0;
		}
	}

}
?>