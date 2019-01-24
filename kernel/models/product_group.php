<?php
namespace models;

use \core\ezy;
use lib\input;
if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->product_group=new ClassProductGroup;
class ClassProductGroup {
	public $per_page = 20;
	public $show_page = '';
	public $sql_query = '';
	public $arrange_data = '';
	public $prefix_html = "";
	public $suffix_html = "";
	public $record_cnt=0;

    /**
     * @var string $cache_prefix
     */
	public $cache_prefix = 'product_group';

	public function listing() {
		global $CMS, $DB, $member;
		$this->arrange_data = trim("product_group_id,product_group_name,product_group_code,user_id,product_group_time,product_group_status");
	 
		$this->arrange_data = trim("product_group_id,product_group_name,product_group_code,user_id,product_group_time,product_group_status");
		$order = $CMS->input['order'];
		$by = $CMS->input['by'];

		// Accepted keywords
		$list_field = array("product_group_id", "product_group_name", "product_group_code", "user_id", "product_group_time", "product_group_status");
		$list_by = array("desc","asc");
		// Filter them
		$default_field = in_array($order, $list_field) ? $order : $list_field[0];
		$default_order = in_array($by, $list_by) ? $by : $list_by[0];

		$where = '';		
		if ( $CMS->input['pg_id'] != "") {
			$where .= " AND `product_group_id`='{$CMS->input['pg_id']}' ";
		}
		if ( $CMS->input['pg_name'] != "") {
			$pg_name = urldecode($CMS->input['pg_name']);
			$where .= " AND `product_group_name` LIKE '%{$pg_name}%' ";
		}
		if ( isset($CMS->input['pg_type']) ) {
			 
			$where .= " AND `product_group_type` = '{$CMS->input['pg_type']}' ";
		}

        if ( isset($CMS->input['ids']) && $CMS->input['ids']!='' ) {

            $where .= " AND `product_group_id` IN({$CMS->input['ids']}) ";
        }

        $sql = "SELECT * FROM `".root_table."product_group` WHERE `product_group_deleted`= 0 AND product_group_parent = 0 {$where} ORDER BY {$default_field} {$default_order}";

        //Cache
        list($this->show_page, $cacheData) = $DB->fetch_listing($sql, $this->per_page, $this->prefix_html, $this->suffix_html, $CMS->input['page'], $this->cache_prefix);

		$arr = array();

		if ($cacheData)
		{
			foreach ($cacheData as $result)
            {
				array_push($arr,$this->convertvalue($result));
			}
		}

		return $arr;
	}

	public function convertvalue($data=null) {
		global $CMS, $DB, $member;
		$data['product_group_parent_c'] = $this->getInfo($data['product_group_parent'], 'product_group_name');
		$data['product_group_time_c'] = $CMS->class->date->date_format($data['product_group_time'],1);
		$data['user_id_c'] = $CMS->user->get_info($data['user_id'], 'user_name');

		$data['product_group_type_bk'] = $CMS->lang["pg_type_{$data['product_group_type']}"];

		$data['product_group_description_short'] = $CMS->class->editor->substr($data['product_group_description'],0,50);
 
		$data['product_group_avatar_c'] = !empty($data['product_group_avatar']) && file_exists($CMS->vars['upload_dir'].'/product/thumbnail/'.$data['product_group_avatar']) ? $CMS->vars['upload_url'].'/product/thumbnail/'.$data['product_group_avatar'] : $CMS->vars['upload_url'].'/product/no-img.jpg';
		
		switch ($data['product_group_status']) {
			case '1':
				$data['product_group_statupg_c'] = "<div class=\"label label-success\">{$CMS->lang['pg_statupg_01']}</div>";
				break;
			default:
				$data['product_group_statupg_c'] =  "<div class=\"label label-default\">{$CMS->lang['pg_statupg_00']}</div>";
				break;
		}

		$data['record_cnt'] = $this->record_cnt;
		$this->record_cnt++;
		return $data;
	}

	public function getInfo($record_id = null,$field_name = '*', $by_field='') {
        global $CMS, $DB, $member;

		if (!empty($record_id)) {

			if($by_field)
            {
                $sql = "SELECT {$field_name} FROM `".root_table."product_group` WHERE {$by_field}='{$record_id}' AND `product_group_deleted`=0 LIMIT 1";
            }
            else
            {
                $sql = "SELECT {$field_name} FROM `".root_table."product_group` WHERE (`product_group_id`='{$record_id}' OR pg_shorturl = '{$record_id}' OR product_group_code='{$record_id}') AND `product_group_deleted`=0 LIMIT 1";
            }

            //cache
            $cacheData = $DB->fetch_data($sql, $this->cache_prefix);

			$data = isset($cacheData[0]) ? $cacheData[0] : null;

            if ($field_name !== '*') {
                return isset($data[$field_name]) ? $data[$field_name] : null;
            }
            return $data;
		}
		return false;
	}

	public function getAll($type = 0 ) {
		global $CMS, $DB, $member;

		$sql = "SELECT * FROM `".root_table."product_group` WHERE `product_group_deleted`=0  AND product_group_status = 1 AND product_group_type = '{$type}' AND product_group_parent = 0 ORDER BY `product_group_id` DESC";

		//Cache
        $cacheData = $DB->fetch_data($sql, $this->cache_prefix);

		$arr = array();

		if ($cacheData)
		{
			foreach ($cacheData as $result)
			{
				$child_group = $this->getchild_group($result['product_group_id'], $type);

				if(count($child_group) > 0)
				{
					$result['data_item'] = $child_group;
				}
			
 				$arr[] = $result;
			}
		}
		return $arr;
	}

	public function getchild_group($parent_id = 0, $type = 0 ) {
		
		global $CMS, $DB, $member;

		$sql = "SELECT * FROM `".root_table."product_group` WHERE `product_group_deleted`=0  AND product_group_status = 1   AND product_group_parent = '{$parent_id}' AND product_group_type = '{$type}' ORDER BY `product_group_id` DESC";

		//Cache
        $arr = $DB->fetch_data($sql, $this->cache_prefix);

		return $arr;
	}

	public function checkName($name = null, $pg_id = null) {
		if (!empty($name)) {
			global $CMS, $DB, $member;

			$sql = "SELECT * FROM `".root_table."product_group` WHERE `product_group_name`='{$name}' AND `product_group_id` != '{$pg_id}' AND `product_group_deleted`=0 LIMIT 1";

			//Cache
            $cacheData = $DB->fetch_data($sql, $this->cache_prefix);

            return $data = $cacheData[0];
		}
		return false;
	}

    public function checkCode($code = null, $pg_id = "")
    {
        if (!empty($code)) {
            global $CMS, $DB, $member;

            $sql_add = '';

            if ($pg_id != "") {
                $sql_add .= "`product_group_id` != '{$pg_id}' AND";
            }

            $sql = "SELECT * FROM `" . root_table . "product_group` WHERE {$sql_add} `product_group_code`='{$code}' AND  `product_group_deleted`=0 LIMIT 1";

            //cache
            $cacheData = $DB->fetch_data($sql,  $this->cache_prefix);

            return $cacheData[0];
        }
        return false;
    }

	public function getParent($id_childen=null) {
		global $CMS, $DB, $member;

		$sql = "SELECT * FROM `".root_table."product_group` WHERE  `product_group_deleted` = 0 AND `product_group_parent` = 0 AND `product_group_id` != '{$id_childen}' ORDER BY `product_group_id` DESC";

		//cache
        return $DB->fetch_data($sql, $this->cache_prefix);
	}

	public function getParent_bytype($type=0) {
		global $CMS, $DB, $member;

		$sql = "SELECT * FROM `".root_table."product_group` WHERE  `product_group_deleted` = 0 AND `product_group_parent` = 0 AND `product_group_type` = '{$type}' ORDER BY `product_group_id` DESC";

        return $DB->fetch_data($sql, $this->cache_prefix);
	}


	public function add($pg_parent = null, $pg_name = null, $pg_code = null, $pg_status = null, $pg_description = null, $pg_order=null, $cat_gallery_id=null, $product_group_type = 0, $product_group_avatar = "") {
			global $CMS, $DB, $member;
		$product_group_type = $product_group_type ? intval($product_group_type) : intval($CMS->input['pg_type']);
		$attr_group = intval($CMS->input['attr_group']);
		$pg_shorturl = $CMS->class->seo->cleanurl($pg_name);
		if (!empty($pg_name)  ) {
            $sql = $DB->query("INSERT INTO `".root_table."product_group` (`product_group_time`, `user_id`, `product_group_parent`, `product_group_name`, `product_group_code`, `product_group_status`, `product_group_description`, `product_group_type`, pg_shorturl, product_group_order, cat_gallery_id, product_group_avatar, attr_group) VALUES ('".time()."','{$member['user_id']}','{$pg_parent}','{$pg_name}','{$pg_code}','{$pg_status}','{$pg_description}', '{$product_group_type}', '{$pg_shorturl}', '{$pg_order}', '{$cat_gallery_id}', '{$product_group_avatar}', '{$attr_group}')");

            //Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$id = $DB->last_insert_id();
 
			$CMS->class->logs->insert("Add_product_group_{$id}");
			return $id;
		}
		return false;
	}

	public function upAvartar($pg_id = null, $pg_avartar = null) {
		if (!empty($pg_id) && !empty($pg_avartar)) {
			global $CMS, $DB, $member;
			 
			$DB->query("UPDATE `".root_table."product_group` SET `product_group_avatar`='{$pg_avartar}' WHERE `product_group_deleted`=0 AND `product_group_id`='{$pg_id}'");

            //Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			return true;
		}
		return false;
	}

	public function edit($data_info = null, $pg_parent = null, $pg_name = null, $pg_code = null, $pg_status = null, $pg_description = null, $pg_order=null, $cat_gallery_id=null) {
 			global $CMS, $DB, $member;
		$product_group_type = intval($CMS->input['pg_type']);
 		$pg_shorturl = $CMS->class->seo->cleanurl($pg_name);
 		$attr_group = intval($CMS->input['attr_group']);
		if (!empty($data_info) && !empty($pg_name)  ) {
		
			$key = "Edit_product_group_{$data_info['product_group_id']}";
			$CMS->class->logs->key = $key;
			$CMS->class->logs->old_data = $data_info;
			$CMS->class->logs->insert($key);
			$DB->query("UPDATE `".root_table."product_group` SET `product_group_parent`='{$pg_parent}',`product_group_name`='{$pg_name}',`product_group_code`='{$pg_code}',`product_group_status`='{$pg_status}',`product_group_description`='{$pg_description}',`product_group_avatar`='{$data_info['product_group_avatar']}', product_group_type = '{$product_group_type}', pg_shorturl = '{$pg_shorturl}', product_group_order='{$pg_order}', cat_gallery_id='{$cat_gallery_id}', attr_group='{$attr_group}' WHERE `product_group_deleted`='0' AND `product_group_id`='{$data_info['product_group_id']}'");

            //Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$CMS->class->logs->key = $key;
			$CMS->class->logs->save_detail("product_group",$data_info['product_group_id'],$this->getInfo($data_info['product_group_id']));
		}
		return false;
	}

	public function deleted($id = null) {
		if (!empty($id)) {
			global $CMS, $DB, $member;

			$DB->query("UPDATE `".root_table."product_group` SET `product_group_deleted`=1 WHERE `product_group_id`='{$id}'");

			if($CMS->vars['web_free']== 1 AND intval($CMS->vars['ibe_synced']) == 1)
	    	{
	    		$data_api['original_id']   		  = "{$id}";
	    		$data_api['site_id']   		  = "{$CMS->vars['site_id']}";
	    		$CMS->api->whm->execute('pservice_delete', $data_api); 
	   		}

            //Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$CMS->class->logs->insert("Deleted_product_group_{$id}");
		}
		return false;
	}

	public function getOptionCategory($is_parent=0)
	{
		global $CMS, $DB;
		if($is_parent)
		{
			$clause = " AND product_group_parent = 0 ";
		}else
		{
			$caluse = "";
		}
		$output = "<option value>{$CMS->lang['plz_choose_product_category']}</option>";

		$sql = "SELECT * FROM ".root_table."product_group WHERE product_group_deleted = 0 AND product_group_status = 1 {$caluse}";

		//Cache
        $cacheData = $DB->fetch_data($sql, $this->cache_prefix);

		if($cacheData)
		{
			foreach ($cacheData as $result)
			{
				$output .=<<<EOF
					<option value="{$result['product_group_id']}">{$result['product_group_name']}</option>
EOF;

			}
		}

		return $output;
	}

	public function addAjax()
	{
		global $CMS, $DB, $member;
 
		$user_id = $member['user_id'];
		$product_group_parent = intval($CMS->input['pg_parent']);
		$product_group_name = $CMS->class->editor->input(urldecode($CMS->input['pg_name']), "text");
		$product_group_code = $CMS->class->editor->input(urldecode($CMS->input['pg_code']), "text");
		$product_group_order = intval($CMS->input['pg_order']);
		$product_group_description = $CMS->class->editor->input(urldecode($CMS->input['pg_description']), "text");

		$pg_shorturl = $CMS->class->seo->cleanurl($product_group_name);
		$cat_gallery_id = intval($CMS->input['cat_gallery_id']);
		$product_group_type = intval($CMS->input['pg_type']);
		$info_product_group_parent = $this->getInfo($product_group_parent);
		$attr_group = intval($CMS->input['attr_group']);
		if(is_array($info_product_group_parent))
		{
			$product_group_type = intval($info_product_group_parent['product_group_type']);
		}
		else
		{
			$product_group_type = intval($CMS->input['pg_type']);
		}
		
		$product_group_status = intval($CMS->input['pg_status']);;
		$product_group_time = time();

		// Check upload
		$file_tmp = isset($_FILES['upload_img']['tmp_name']) ? $_FILES['upload_img']['tmp_name'] : "";
		$file_name = isset($_FILES['upload_img']['name']) ? $_FILES['upload_img']['name'] : "";
		$file_type = isset($_FILES['upload_img']['type']) ? $_FILES['upload_img']['type'] : "";
		$file_size = isset($_FILES['upload_img']['size']) ? $_FILES['upload_img']['size'] : "";
		$file_error = isset($_FILES['upload_img']['error']) ? $_FILES['upload_img']['error'] : "";
		
		$file_ext = $CMS->class->attachment->get_ext( $file_name );
 


		// Check dung luong file upload
		$max = 10;
		$max_file_upload = 1024*1024*$max;
		if($file_size > $max_file_upload )
		{
			$arr_img = array("msg" => $CMS->lang['msg_maxfile_upload_img'].$max."MB" , "status" => "error");
			print json_encode($arr_img);exit;
		}
		$file_name = str_replace( " ", "_", $file_name );
		$file_location = strtolower(time()."_".$file_name);
		$product_image = "";
		if ( $file_name )
		{
			if ( $CMS->class->attachment->is_image($file_name, $file_ext) == false )
			{
				$arr_img = array("msg" => $CMS->lang['invalid_upload_file'] , "status" => "error");
				print json_encode($arr_img);exit;
				// return false;
			}
			$CMS->class->image->is_dir("{$CMS->vars['upload_dir']}/product");
			$check = @copy($file_tmp, "{$CMS->vars['upload_dir']}/product/{$file_location}");
			if(!$check)
			{
				$arr_img = array("msg" => $CMS->lang['msg_error_upload_img'], "status" => "error");
				print json_encode($arr_img);exit;
			}
			$CMS->class->image->is_dir("{$CMS->vars['upload_dir']}/product/thumbnail");
			$CMS->class->image->resize("{$CMS->vars['upload_dir']}/product/".$file_location, "{$CMS->vars['upload_dir']}/product/thumbnail/".$file_location, 220,150);	
			$product_group_avatar = $file_location;
		}

		if(!$product_group_name)
		{
			$arr_img = array("msg" => $CMS->lang['msg_empty_product_group_name'] , "status" => "error");
				print json_encode($arr_img);exit;
		}

		if($this->checkExit($product_group_name, '', $product_group_parent))	
		{
			$arr_img = array("msg" => $CMS->lang['msg_exist_product_group_name'] , "status" => "error");
				print json_encode($arr_img);exit;
		}

		$checkCode = $CMS->product_group->checkCode($product_group_code);

		if (is_array($checkCode) ) {
			$arr_img = array("msg" => $CMS->lang['pg_code_exist'] , "status" => "error");
			print json_encode($arr_img);exit;
 
		}


		$count = $DB->query("INSERT INTO ".root_table."product_group (user_id, product_group_parent, product_group_name, product_group_code, product_group_status, product_group_description, product_group_avatar, product_group_type, product_group_time, product_group_order, cat_gallery_id, pg_shorturl, attr_group) VALUES ('{$user_id}', '{$product_group_parent}', '{$product_group_name}', '{$product_group_code}', '{$product_group_status}', '{$product_group_description}', '{$product_group_avatar}', '{$product_group_type}', '{$product_group_time}', '{$product_group_order}', '{$cat_gallery_id}', '{$pg_shorturl}', '{$attr_group}')");

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

        $product_group_id = $DB->last_insert_id();

        //Sync WHM data
        if($CMS->vars['web_free']== 1 AND intval($CMS->vars['ibe_synced']) == 1)
    	{
    		$product_group_avatar_link = !empty($product_group_avatar) && file_exists($CMS->vars['upload_dir'].'/product/'.$product_group_avatar) ? $CMS->vars['upload_url'].'/product/'.$product_group_avatar : "";

    		$data_api['product_group_parent'] = $product_group_parent;
    		$data_api['product_group_name']   = "{$product_group_name}";
    		$data_api['product_group_code']   = "{$product_group_code}";
    		$data_api['product_group_status'] = "{$product_group_status}";
    		$data_api['product_group_description'] = "{$product_group_description}";
    		$data_api['product_group_avatar_link'] = "{$product_group_avatar_link}";
    		$data_api['product_group_avatar'] = "{$product_group_avatar}";
    		$data_api['product_group_type']   = "{$product_group_type}";
    		$data_api['product_group_time']   = "{$product_group_time}";
    		$data_api['pg_shorturl']   		  = "{$pg_shorturl}";
    		$data_api['original_id']   		  = "{$product_group_id}";
    		$data_api['site_id']   		  = "{$CMS->vars['site_id']}";
    		
    		$CMS->api->whm->execute('pservice_add', $data_api); 
    	}

		if($count)
		{
			$CMS->class->logs->key= "product_group_{$product_group_id}";
			$CMS->class->logs->insert("{$member['cus_username']} create <b>product group #{$product_group_id} {$product_group_name}</b>");
			return $product_group_id;
		}else
		{
			return false;
		}

	}

	public function editAjax()
	{
		global $CMS, $DB, $member;

		$product_group_id = intval($CMS->input['pg_id']);
		$data = $this->getInfo($product_group_id);
		$CMS->class->logs->key = "product_group_{$data['product_group_id']}";
		$CMS->class->logs->old_data = $data;

		$cat_gallery_id = intval($CMS->input['cat_gallery_id']);
		$user_id = $member['user_id'];
		$product_group_parent = intval($CMS->input['pg_parent']);
		$attr_group = intval($CMS->input['attr_group']);
		// Get info g parent
 
		$info_product_group_parent = $this->getInfo($product_group_parent);
		if(is_array($info_product_group_parent))
		{
			$product_group_type = intval($info_product_group_parent['product_group_type']);
		}
		else
		{
			$product_group_type = intval($CMS->input['pg_type']);
		}
		$product_group_name = $CMS->class->editor->input(urldecode($CMS->input['pg_name']), "text");
		$product_group_code = $CMS->class->editor->input(urldecode($CMS->input['pg_code']), "text");
		$product_group_description = $CMS->class->editor->input(urldecode($CMS->input['pg_description']), "text");
		$pg_shorturl = $CMS->class->seo->cleanurl($product_group_name);
		$product_group_order = intval($CMS->input['pg_order']);
		$product_group_status = intval($CMS->input['pg_status']);;
		$product_group_time_update = time();

		// Check upload
		$file_tmp = isset($_FILES['upload_img']['tmp_name']) ? $_FILES['upload_img']['tmp_name'] : "";
		$file_name = isset($_FILES['upload_img']['name']) ? $_FILES['upload_img']['name'] : "";
		$file_type = isset($_FILES['upload_img']['type']) ? $_FILES['upload_img']['type'] : "";
		$file_size = isset($_FILES['upload_img']['size']) ? $_FILES['upload_img']['size'] : "";
		$file_error = isset($_FILES['upload_img']['error']) ? $_FILES['upload_img']['error'] : "";
		
		$file_ext = $CMS->class->attachment->get_ext( $file_name );

		// Check dung luong file upload
		$max = 10;
		$max_file_upload = 1024*1024*$max;
		if($file_size > $max_file_upload )
		{
			$arr_img = array("msg" => $CMS->lang['msg_maxfile_upload_img'].$max."MB" , "status" => "error");
			print json_encode($arr_img);exit;
		}
		$file_name = str_replace( " ", "_", $file_name );
		$file_location = strtolower(time()."_".$file_name);
		$product_image = "";
		if ( $file_name )
		{
			if ( $CMS->class->attachment->is_image($file_name, $file_ext) == false )
			{
				$arr_img = array("msg" => $CMS->lang['invalid_upload_file'] , "status" => "error");
				print json_encode($arr_img);exit;
				// return false;
			}
			$CMS->class->image->is_dir("{$CMS->vars['upload_dir']}/product");
			$check = @copy($file_tmp, "{$CMS->vars['upload_dir']}/product/{$file_location}");
			if(!$check)
			{
				$arr_img = array("msg" => $CMS->lang['msg_error_upload_img'], "status" => "error");
				print json_encode($arr_img);exit;
			}
			$CMS->class->image->is_dir("{$CMS->vars['upload_dir']}/product/thumbnail");
			$CMS->class->image->resize("{$CMS->vars['upload_dir']}/product/".$file_location, "{$CMS->vars['upload_dir']}/product/thumbnail/".$file_location, 220,150);	
			$product_group_avatar = $file_location;
		}else
		{
			$product_group_avatar = $data['product_group_avatar'];
		}

		if(!$product_group_name)
		{
			$arr_img = array("msg" => $CMS->lang['msg_empty_product_group_name'] , "status" => "error");
				print json_encode($arr_img);exit;
		}

		if($this->checkExit($product_group_name, $data['product_group_id'], $product_group_parent))	
		{
			$arr_img = array("msg" => $CMS->lang['msg_exist_product_group_name'] , "status" => "error");
				print json_encode($arr_img);exit;
		}

		$checkCode = $CMS->product_group->checkCode($product_group_code, $data['product_group_id']);

		if (is_array($checkCode) ) {
			$arr_img = array("msg" => $CMS->lang['pg_code_exist'] , "status" => "error");
			print json_encode($arr_img);exit;
 
		}

		$count = $DB->query("UPDATE ".root_table."product_group SET user_id = '{$user_id}', product_group_parent = '{$product_group_parent}', product_group_name = '{$product_group_name}', product_group_code = '{$product_group_code}', product_group_description = '{$product_group_description}', product_group_avatar = '{$product_group_avatar}', product_group_type = '{$product_group_type}', product_group_time_update = '{$product_group_time_update}', product_group_status = '{$product_group_status}', product_group_order='{$product_group_order}', cat_gallery_id='{$cat_gallery_id}', pg_shorturl='{$pg_shorturl}', attr_group='{$attr_group}' WHERE product_group_id = '{$data['product_group_id']}'");

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		if($count)
		{

		    if($CMS->vars['web_free']== 1 AND intval($CMS->vars['ibe_synced']) == 1)
	        {
				if ( $file_name )
				{
	    			$product_group_avatar_link = !empty($product_group_avatar) && file_exists($CMS->vars['upload_dir'].'/product/'.$product_group_avatar) ? $CMS->vars['upload_url'].'/product/'.$product_group_avatar : "";
	    		}
	    		else
	    		{
	    			$product_group_avatar_link = "";
	    		}
	    		$data_api['product_group_parent'] = $product_group_parent;
	    		$data_api['product_group_name']   = "{$product_group_name}";
	    		$data_api['product_group_code']   = "{$product_group_code}";
	    		$data_api['product_group_status'] = "{$product_group_status}";
	    		$data_api['product_group_description'] = "{$product_group_description}";
	    		$data_api['product_group_avatar_link'] = "{$product_group_avatar_link}";
	    		$data_api['product_group_avatar'] = "{$product_group_avatar}";
	    		$data_api['product_group_type']   = "{$product_group_type}";
	    		$data_api['product_group_time']   = "{$product_group_time}";
	    		$data_api['product_group_time_update']   = "{$product_group_time_update}";
	    		$data_api['pg_shorturl']   		  = "{$pg_shorturl}";
	    		$data_api['original_id']   		  = "{$data['product_group_id']}";
	    		$data_api['site_id']   		  = "{$CMS->vars['site_id']}";
	    		
	    		$CMS->api->whm->execute('pservice_edit', $data_api); 
	    	}


			$CMS->class->logs->key= "product_group_{$product_group_id}";
			$CMS->class->logs->insert("{$member['cus_username']} edit <b>product group #{$product_group_id} {$product_group_name}</b>");
			$data_new = $this->getInfo($data['product_group_id']);
			$data_new['data_option'] = $this->getMultiOptionCategory(1, 0, 0, 0, 0, $product_group_type);
			$CMS->class->logs->key ="product_group_{$data['product_group_id']}";
			$CMS->class->logs->save_detail("product_group",$data['product_group_id'],$data_new);

			return $data_new;
		}else
		{
			return false;
		}

	}

	public function checkExit($pg_name='', $id_accept=0, $product_group_parent=0)
	{
		global $CMS, $DB;

		if($id_accept)
		{
			$clause = " AND product_group_id != '{$id_accept}' ";
		}else
		{
			$clause = "";
		}

		if($product_group_parent)
		{
			$clause .= " AND product_group_parent = '{$product_group_parent}' ";
		}else
		{
			$clause .= "";
		}

		$sql = "SELECT count(0) as cnt FROM ".root_table."product_group WHERE product_group_deleted = 0 AND product_group_name = '{$pg_name}' {$clause}";

        $result = $DB->fetch_data($sql, $this->cache_prefix);

        return $result[0]['cnt'];
	}

	public function getMultiOptionCategory($is_child = 0, $pg_id=0, $disable_child = 0, $parent_id=0, $disable_title=0, $pg_type = '')
	{
		global $CMS, $DB;

		/*
		* $is_child        : lấy danh mục con hay ko
		* $pg_id           : láy những danh mục khác pg_id này
		* $disable_child   : ko cho chọn danh mục con
		* $parent_id       : Dùng để check selected
		* $disable_title   : Không dùng option title
		* Repair function on date: 01/11/2017
		* By: Tanlv
		**/


		$clause = $pg_id ? " AND product_group_id != '{$pg_id}' " : "";

		if( is_numeric($pg_type) )
		{
			$clause .= " AND product_group_type = '{$pg_type}' ";
		}

		$output = $disable_title ? "" : "<option value>{$CMS->lang['plz_choose_product_category']}</option>";
		$sql = $DB->query("SELECT * FROM ".root_table."product_group WHERE product_group_deleted = 0 AND product_group_status = 1 AND product_group_parent = 0 {$clause} ORDER BY product_group_name ASC");
		if($DB->num_rows($sql) > 0)
		{
			while ($result = $DB->fetch_array($sql))
			{
				$selected1 = $parent_id == $result['product_group_id'] ? " selected='selected' " : "";
				$output .=<<<EOF
				<option value="{$result['product_group_id']}" type="{$result['product_group_type']}" attrGroup="{$result['attr_group']}" {$selected1}>{$result['product_group_name']}</option>
EOF;
				if($is_child)
				{
					// option child
					$sql2 = $DB->query("SELECT * FROM ".root_table."product_group WHERE product_group_deleted = 0 AND product_group_status = 1 AND product_group_parent = '{$result['product_group_id']}' {$clause} ORDER BY product_group_name ASC");
					if($DB->num_rows($sql2) > 0)
					{
						while ($result2 = $DB->fetch_array($sql2))
						{
							$selected2 = $parent_id == $result2['product_group_id'] ? "selected='selected'" : "";
							$disabled = $disable_child ? " disabled='disabled' " : "";
							$output .=<<<EOF
							<option value="{$result2['product_group_id']}"  type="{$result2['product_group_type']}" attrGroup="{$result2['attr_group']}" {$selected2} {$disabled}>&nbsp; &nbsp; &nbsp;|__{$result2['product_group_name']}</option>
EOF;

							// option child 3
							$sql3 = $DB->query("SELECT * FROM ".root_table."product_group WHERE product_group_deleted = 0 AND product_group_status = 1 AND product_group_parent = '{$result2['product_group_id']}' {$clause} ORDER BY product_group_name ASC");
							if($DB->num_rows($sql3) > 0)
							{
								while ($result3 = $DB->fetch_array($sql3))
								{
									$selected3 = $parent_id == $result3['product_group_id'] ? "selected='selected'" : "";
									$disabled2 = $disable_child ? " disabled='disabled' " : "";
									$output .=<<<EOF
									<option value="{$result3['product_group_id']}"  type="{$result3['product_group_type']}" attrGroup="{$result3['attr_group']}" {$selected3} {$disabled2}>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;|__{$result3['product_group_name']}</option>
EOF;
								
									// option child 4
									$sql4 = $DB->query("SELECT * FROM ".root_table."product_group WHERE product_group_deleted = 0 AND product_group_status = 1 AND product_group_parent = '{$result3['product_group_id']}' {$clause} ORDER BY product_group_name ASC");
									if ( $DB->num_rows($sql4) > 0 )
									{
										while ( $result4 = $DB->fetch_array($sql4) )
										{
											$selected4 = $parent_id == $result4['product_group_id'] ? "selected='selected'" : "";
											// $disabled3 = $disable_child ? " disabled='disabled' " : "";
											$disabled3 = " disabled='disabled' ";
											$output .=<<<EOF
											<option value="{$result4['product_group_id']}"  type="{$result4['product_group_type']}"  attrGroup="{$result4['attr_group']}"  {$selected4} {$disabled3}>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;|__{$result4['product_group_name']}</option>
EOF;
										}
									}
								}
							}
						}
					}
				}// End if
			}
		}

		return $output;
	}



	public function getMultiOptionCategory_bk($is_child = 0, $pg_id=0, $disable_child = 0, $parent_id=0, $disable_title=0)
	{
		global $CMS, $DB;

		$clause = $pg_id ? " AND product_group_id != '{$pg_id}' " : "";

		$output = $disable_title ? "" : "<option value>{$CMS->lang['plz_choose_product_category']}</option>";

		$sql = "SELECT * FROM ".root_table."product_group WHERE product_group_deleted = 0 AND product_group_status = 1 AND product_group_parent = 0 {$clause} ORDER BY product_group_name ASC";

		//Cache
		$cacheData = $DB->fetch_data($sql, $this->cache_prefix);

		if($cacheData)
		{
			foreach ($cacheData as $result)
			{
				if($parent_id == $result['product_group_id'])
				{
					$selected1 = " selected='selected' ";
				}else
				{
					$selected1 = "";
				}
				$output .=<<<EOF
					<option value="{$result['product_group_id']}" type="{$result['product_group_type']}" {$selected1}>{$result['product_group_name']}</option>
EOF;
				if($is_child)
				{
					// option child
					$sql2 = $DB->query("SELECT * FROM ".root_table."product_group WHERE product_group_deleted = 0 AND product_group_status = 1 AND product_group_parent = '{$result['product_group_id']}' {$clause} ORDER BY product_group_name ASC");
					if($DB->num_rows($sql2) > 0)
					{
						while ($result2 = $DB->fetch_array($sql2)) 
						{
							if($parent_id == $result2['product_group_id'])
							{
								$selected2 = "selected='selected'";
							}else
							{
								$selected2 = "";
							}
							if($disable_child)
							{
								// $disabled = " disabled='disabled' ";
							}else
							{
								$disabled = "";
							}
							$output .=<<<EOF
						<option value="{$result2['product_group_id']}"  type="{$result2['product_group_type']}" {$selected2} {$disabled}>&nbsp; &nbsp; &nbsp;|__{$result2['product_group_name']}</option>
EOF;
						}
					}
				}// End if
			}
		}

		return $output;
	}


	public function getMultiOptionCategory_2($is_child = 0, $pg_id=0, $disable_child = 0, $parent_id=0, $product_group_type = 0)
	{
		global $CMS, $DB;
		if($pg_id)
		{
			$clause = " AND product_group_id != '{$pg_id}'";
		}else
		{
			$clause = "";
		}

		$clause .= " AND product_group_type='{$product_group_type}' ";
		$output = "<option value>{$CMS->lang['plz_choose_product_category']}</option>";

		$sql = "SELECT * FROM ".root_table."product_group WHERE product_group_deleted = 0 AND product_group_status = 1 AND product_group_parent = 0 {$clause} ORDER BY product_group_name ASC";

		//cache
        $cacheData = $DB->fetch_data($sql, $this->cache_prefix);

		if($cacheData)
		{
			foreach ($cacheData as $result)
			{
				if($parent_id == $result['product_group_id'])
				{
					$selected1 = " selected='selected' ";
				}else
				{
					$selected1 = "";
				}
				$output .=<<<EOF
					<option value="{$result['product_group_id']}" type="{$result['product_group_type']}" {$selected1}>{$result['product_group_name']}</option>
EOF;
				if($is_child)
				{
					// option child
					$sql2 = $DB->query("SELECT * FROM ".root_table."product_group WHERE product_group_deleted = 0 AND product_group_status = 1 AND product_group_parent = '{$result['product_group_id']}' {$clause} ORDER BY product_group_name ASC");
					if($DB->num_rows($sql2) > 0)
					{
						while ($result2 = $DB->fetch_array($sql2)) 
						{
							if($parent_id == $result2['product_group_id'])
							{
								$selected2 = "selected='selected'";
							}else
							{
								$selected2 = "";
							}
							if($disable_child)
							{
								$disabled = " disabled='disabled' ";
							}else
							{
								$disabled = "";
							}
							$output .=<<<EOF
						<option value="{$result2['product_group_id']}"  type="{$result2['product_group_type']}" {$selected2} {$disabled}>&nbsp; &nbsp; &nbsp;|__{$result2['product_group_name']}</option>
EOF;
						}
					}
				}// End if
			}
		}

		return $output;
	}


	public function countitem_group($parent_id = null, $type = 0, $type_group=0)
	{
		global $CMS, $DB;
	 	
		$sql = "SELECT * FROM ".root_table."product_group WHERE product_group_deleted = 0 AND product_group_parent = '{$parent_id}' AND product_group_type='{$type_group}' ";

        //Cache
		$item = $DB->fetch_data($sql, $this->cache_prefix);
		$count = count($item) ;
		if($type == 0)
		{
            return $count;
		}
		else
		{
			return $item;
		}
	}

	public function countproduct_bygroup($group_id = null, $type_product = 0)
	{
		global $CMS, $DB;

        $sql = "SELECT count(0) cnt FROM ".root_table."product  WHERE product_deleted = 0 AND product_group  = '{$group_id}' AND product_type='{$type_product}' ";

        $result = $DB->fetch_data($sql, 'product');

        return $result[0]['cnt'];
	}

	function updateShorurl()
	{
		global $DB, $CMS;

		$sql = $DB->query("SELECT * FROM ".root_table."product_group");
		while ($result = $DB->fetch_array($sql)) 
		{
			$shorurl = $CMS->class->seo->cleanurl($result['product_group_name']);
			$DB->query("UPDATE ".root_table."product_group SET pg_shorturl = '{$shorurl}' WHERE product_group_id = '{$result['product_group_id']}'");
		}
	}

	function updatePShorurl()
	{
		global $DB, $CMS;

		$sql = $DB->query("SELECT * FROM ".root_table."product");
		while ($result = $DB->fetch_array($sql)) 
		{
			$shorurl = $CMS->class->seo->cleanurl($result['product_name']);
			$DB->query("UPDATE ".root_table."product SET product_shorturl = '{$shorurl}' WHERE product_id = '{$result['product_id']}'");
		}
	}

    function exportToExcel($product_group_type = null)
    {
        global $CMS, $DB;

        //Header cols title
        $arr_title = [
            $CMS->lang['pg_name'],
            $CMS->lang['pg_type'],
            $CMS->lang['pg_code'],
            $CMS->lang['pg_description'],
            $CMS->lang['pg_status'],
            $CMS->lang['pg_avartar'],
        ];

        // Set header
        \models\report::excel_header();

        // Set style
        $style = array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation'   => 0,
            'wrap'       => TRUE
        );


        /**
         * Fields to export
         * Note: muste by match with header cols (order and numbers)
         */
        $fields = 'product_group_name,product_group_type,product_group_code,product_group_description,product_group_status,product_group_avatar';

        // Query data
        if(isset($CMS->input['id']) && ($pId = intval($CMS->input['id'])))
        {
            $sql = "SELECT {$fields} FROM ".root_table."product_group WHERE product_group_deleted=0 AND product_group_id=$pId ORDER BY product_group_id ASC";
        }
        else
        {
            if($product_group_type !== null)
            {
                $sql_add = " product_group_type=$product_group_type AND ";
            }

            $sql = "SELECT {$fields} FROM ".root_table."product_group WHERE product_group_deleted=0 AND {$sql_add} 1=1 ORDER BY product_group_id ASC";
        }

        $results = $DB->fetch_data($sql, $this->cache_prefix);

        $count_row = count($results) + 1;// + 1 row title

        // Set style excel
        \models\report::excel_title($arr_title, $count_row, $style);

        // Loop data
        $i = 2;

        //Range char A-Z
        $rangeChar = range('A', 'Z') ;
        $fields = explode(',', $fields);

        foreach ($results as $result)
        {
            $result['product_group_status'] = $result['product_group_status'] ? 'show' : 'hide';

            /*$supInfo = [];

            if($result['sup_id'] != 0)
            {
                if($CMS->vars['supTemp'][$result['sup_id']])
                {
                    $supInfo = $CMS->vars['supTemp'][$result['sup_id']];
                }
                else
                {
                    $supInfo = $CMS->supplier->get_info($result['sup_id']);
                    $CMS->vars['supTemp'][$result['sup_id']] =  $supInfo;
                }
            }

            $result['sup_id'] = $supInfo['supplier_name'];*/

            if($result['product_group_type'] == 0)
            {
                $result['product_group_type'] = 'product';
            }
            else
            {
                $result['product_group_type'] = 'service';
            }

            $maxColWidth = 120;

            foreach ($rangeChar as $charKey => $char)
            {
                /**
                 * Set values to cell by chars(A-Z) and fields from database
                 */
                $field = $fields[$charKey];

                if($field == 'product_group_avatar')
                {
                    if(is_file("{$CMS->vars['upload_dir']}/product/{$result[$field]}"))
                    {
                        $objDrawing = new \PHPExcel_Worksheet_Drawing();
                        $objDrawing->setPath("{$CMS->vars['upload_dir']}/product/{$result[$field]}");
                        $objDrawing->setCoordinates($char.$i);
                        $objDrawing->setWorksheet(\models\report::$dataExcel->getActiveSheet());
                        $objDrawing->setHeight(120);

                        $imgHeight = $objDrawing->getHeight();
                        $imgWidth = $objDrawing->getWidth();
                        $imgRatio = $imgHeight/$imgWidth;

                        $colWidth = 120/$imgRatio;

                        if($colWidth > $maxColWidth)
                        {
                            $maxColWidth =  $colWidth;
                        }
                        else
                        {
                            $colWidth = $maxColWidth;
                        }

                        \models\report::$dataExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(120);
                        \models\report::$dataExcel->getActiveSheet()->getColumnDimension($char)->setAutoSize(false);
                        \models\report::$dataExcel->getActiveSheet()->getColumnDimension($char)->setWidth("50"); //$colWidth
                    }
                    else
                    {
                        \models\report::$dataExcel->getActiveSheet()->setCellValue($char.$i,'');
                    }
                }
                else
                {
                    $result[$field] = html_entity_decode($result[$field]);
                    \models\report::$dataExcel->getActiveSheet()->setCellValue($char.$i, $result[$field]);
                }
            };
            $i++;
        }

        // Set name file
        $file_name = "product_group_list.xls";

        // Create file and return link download
        return \models\report::excel_output($file_name);
    }

    function importFromExcel()
    {
        global $CMS, $DB, $member;

        // require file
        require_once root_path."vendor/autoload.php";

        /**
         * Tpl key list to check existed
         */
        $tplKeyList = [];

        /**
         * Marked error position
         */
        $errorPositions = [];

        /**
         * Fields is required
         */
        $checkRequired = ['product_group_name','product_group_code','product_group_type'];

        // Input
        $file_tmp = isset($_FILES['upload_file']['tmp_name']) ? $_FILES['upload_file']['tmp_name'] : "";
        $file_name = isset($_FILES['upload_file']['name']) ? $_FILES['upload_file']['name'] : "";

        //CHeck error
        if($fileUploadErrorCode = $_FILES['upload_file']['error'])
        {
            $_SESSION['error_msg'] = $CMS->lang['file_upload_error_'.$fileUploadErrorCode];
            return false;
        }

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
        $objPHPExcel = \PHPExcel_IOFactory::load($file_tmp);

        $highestRow         = $objPHPExcel->getActiveSheet()->getHighestRow(); // e.g. 10
        $highestColumn      = $objPHPExcel->getActiveSheet()->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);

        //Reset style
        $styleDefault = array(
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_NONE,
            ),
            'font'  => array(
                'bold'  => false,
                'color' => array('rgb' => '000000'))
        );

        $objPHPExcel->getActiveSheet()->getStyle("A2:{$highestColumn}{$highestRow}")->applyFromArray($styleDefault);

        $lastCols = $objPHPExcel->getActiveSheet()->getHighestColumn(); //Cột cuối cùng

        //Danh sách thứ tự các field tương ứng với thứ tự cột từ file excel
        $fields_text = 'product_group_name,product_group_type,product_group_code,product_group_description,product_group_status,product_group_avatar';
        $fields = explode(',', $fields_text);
        $fields_flip = array_flip($fields); // Đảo ngược key và value

        //Range char A-Z
        $rangeChar = range('A', 'Z') ;

        //Xác định cột chứa hình ảnh
        $imageColIndex = $fields_flip['product_group_avatar'];
        $imageColName = $rangeChar[$imageColIndex];

        /**
         * Lay thong tin cac hinh anh trong file
         */

        $fileImages = [];
        $flagErr = false; //Biến cờ đánh dấu lỗi
        $errStyleCell = array(
//            'borders' => array(
//                'allborders' => array(
//                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
//                    'color' => array('rgb' => 'FF0000')
//                )
//            ),
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'E0E0E0')
            )
        ); //Style đánh dấu ô có lỗi

        $errStyleRow = array(
            'font'  => array(
//                'bold'  => true,
                'color' => array('rgb' => 'FF0000'),
            )); //Style đánh dấu dòng có lỗi

        $sql_values = ""; //values for multi insert

        $validData = []; //Mang chua cac dong hop le

        /**
         * Loop to check validate
         */
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
        {
            // Loop row
            for ($row = 1; $row <= $highestRow; ++ $row)
            {
                if($row == 1) { continue;}
                $data = [];

                // Loop col
                for ($col = 0; $col < $highestColumnIndex; ++$col)
                {
                    $cell = $worksheet->getCellByColumnAndRow($col, $row);
                    if($fields[$col])
                    {
                        $data[$fields[$col]] = $CMS->class->editor->input($cell->getValue(), "text"); //Gan du lieu lai theo giong field trong DB

                        /*if($fields[$col] == 'sup_id')
                        {
                            if($CMS->vars['supplierTemp'][$data['sup_id']])
                            {
                                $supplier = $CMS->vars['supplierTemp'][$data['sup_id']];
                            }
                            else
                            {
                                $supplier = $CMS->supplier->get_info($data['sup_id']);
                                $CMS->vars['supplierTemp'][$data['sup_id']] = $supplier;
                            }

                            $data['sup_id'] = intval($supplier['supplier_id']);
                        }*/

                        /**
                         * Check required
                         */
                        if(in_array($fields[$col], $checkRequired))
                        {
                            if($data[$fields[$col]] == '')
                            {
                                $errorPositions[$row][] = $col;
                            }
                        }

                        if($fields[$col] == 'product_group_code')
                        {
                            if($data[$fields[$col]]!='')
                            {
                                $tplKeyList[$data[$fields[$col]]][] = ['col' => $col, 'row' => $row];
                            }
                        }
                    }
                }

                if(!isset($errorPositions[$row]))
                {
                    $validData[$row] = $data;
                }
            }
        }

        //Duyet danh sach key de check trung
        foreach ($tplKeyList as $tplCode => $errorPos)
        {

            if(!$CMS->input['is_overwrite'])
            {
                if(count($errorPos)>1 || $this->checkExist('product_group_code', $tplCode))
                {
                    foreach ($errorPos as $errColRows)
                    {
                        $errorPositions[$errColRows['row']][] = $errColRows['col'];
                    }
                }
            }

            //Xóa bỏ các dòng trung key, chỉ giữ lại dòng cuối
            if(($cnt = count($errorPos)) >= 2)
            {
                for ($i=0; $i<$cnt-1; $i++)
                {
                    unset($validData[$errorPos[$i]['row']]);
                }
            }
        }

        if($errorPositions)
        {
            foreach ($errorPositions as $errRow => $errCols)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension();

                //Đánh dấu dòng lỗi
                $objPHPExcel->getActiveSheet()->getStyle("A{$errRow}:{$highestColumn}{$errRow}")->applyFromArray($errStyleRow);

                foreach ($errCols as $errCol)
                {
                    //Đánh dấu các ô bị lỗi
                    $colName = $rangeChar[$errCol];
                    $objPHPExcel->getActiveSheet()->getStyle("{$colName}{$errRow}")->applyFromArray($errStyleCell);
                }
            }

            /**
             * Trả về file highlight các dòng bị lỡi cho khách sửa lại
             */
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $tmpFile = "import_product_group_C{$member['user_id']}.xls";
            $objWriter->save("{$CMS->vars['upload_dir']}/excel/{$tmpFile}");
            $_SESSION['error_msg'] = "{$CMS->lang['invalid_import_data']} <a href='{$CMS->vars['upload_url']}/excel/{$tmpFile}'>Download file</a>";
            $CMS->global->redirectReferer();
        }

        /**
         * get images from file
         */
        foreach ($objPHPExcel->getActiveSheet()->getDrawingCollection() as $drawing) {
            if ($drawing instanceof \PHPExcel_Worksheet_MemoryDrawing) {
                ob_start();
                call_user_func(
                    $drawing->getRenderingFunction(),
                    $drawing->getImageResource()
                );

                $imageContents = ob_get_contents();
                ob_end_clean();
                $extension = 'jpg';

                switch ($drawing->getMimeType()) {
                    case \PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG :
                        $extension = 'png'; break;
                    case \PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_GIF:
                        $extension = 'gif'; break;
                    case \PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_JPEG :
                        $extension = 'jpg'; break;
                }

            } else {
                $zipReader = fopen($drawing->getPath(),'r');
                $imageContents = '';

                while (!feof($zipReader)) {
                    $imageContents .= fread($zipReader,1024);
                }
                fclose($zipReader);
                $extension = $drawing->getExtension();
            }

            //Lưu tam source hinh va vi tri cua cell chua hinh lai

            $fileImages[$drawing->getCoordinates()]['src'] = $imageContents;
            $fileImages[$drawing->getCoordinates()]['ext'] = $extension;
        }

        //Create folder upload product

        /**
         * Loop dữ liệu để upload ảnh và insert vào DB
         */
        foreach ($validData as $row => $data)
        {
            $time = time();
            $random = rand(0,10000);

            //UPLOAD IMAGE
            $fileToUpload = $fileImages["{$imageColName}{$row}"];

            if($fileToUpload)
            {
                $fileName = "{$time}_{$random}_".$CMS->class->seo->cleanurl("{$data['product_group_name']}").".{$fileToUpload['ext']}";

                @file_put_contents("{$CMS->vars['upload_dir']}/product/{$fileName}", $fileToUpload['src']);

                //Tạo Thumb
                $CMS->class->image->resize("{$CMS->vars['upload_dir']}/product/{$fileName}", "{$CMS->vars['upload_dir']}/product/thumbnail/{$fileName}", 220,150);

                $data['product_group_avatar'] = $fileName;
            }
            else
            {
                $data['product_group_avatar'] = '';
            }

            $data['product_group_status'] = strtolower($data['product_group_status']) == 'show' ? 1 : 0;

            $data['product_group_type'] = strtolower($data['product_group_type']) == 'product' ? 0 : 1;

            if($data['product_group_code'] != '' && $this->checkExist('product_group_code', $data['product_group_code']))
            {
                /**
                 * Update record
                 */
                $sql_update = "UPDATE ".root_table."product_group SET ";

                foreach ($data as $field => $value)
                {
                    //Create values sql
                    $sql_update .= "{$field}='{$value}',";
                }

                $sql_update = trim($sql_update,',');

                $sql_update .= " WHERE product_group_code='{$data['product_group_code']}'";

                $DB->query($sql_update);
            }
            else
            {
                $data['user_id'] = $member['user_id'];
                $data['product_group_time'] = $data['product_group_time_update'] = time();

                /**
                 * Multi insert
                 */
                $sql_values .= "(";
                foreach ($data as $field => $value)
                {
                    //Create values sql
                    $sql_values .= "'{$value}',";
                }
                $sql_values = trim($sql_values,',');
                $sql_values .= "),";
            }

        }

        $sql_values = trim($sql_values,',');

        /**
         * Lay danh sach field de insert
         */
        $field_list = array_keys($data);
        $field_list = implode(',',$field_list);

        $sql = "INSERT INTO ".root_table."product_group ({$field_list}) VALUES {$sql_values}";

        $DB->query($sql);

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

        $_SESSION['msg'] = $CMS->lang['import_file_success'];

        $CMS->global->redirectReferer();

    }

    public function checkExist( $field, $value = "", $except_value = "" )
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

        $sql = "SELECT count(0) cnt FROM ".root_table."product_group WHERE {$field}='{$value}' AND product_group_deleted=0";

        $result = $DB->fetch_data($sql, $this->cache_prefix);

        return $result[0]['cnt'];
    }

    function del_img($id=0)
    {
    	global $CMS, $DB;

    	$image = $this->getInfo($id, "product_group_avatar");
    	if($image and $id)
    	{
    		
    		@unlink("{$CMS->vars['upload_dir']}/product/{$image}");
    		@unlink("{$CMS->vars['upload_dir']}/product/thumbnail/{$image}");
    		
    		$check = $DB->query("UPDATE ".root_table."product_group SET product_group_avatar='' WHERE product_group_id='{$id}'");
    		return $check ? 1 : 0;
    	}else
    	{
    		return 0;
    	}

    }

    // Get full level
    public function getAllFull( $type = 0, $all = 0 ) 
    {
		global $CMS, $DB, $member;

		$output = [];

		$clause = " AND product_group_type = '{$type}' ";
		if( ! $all )
		{
			$clause .= " AND product_group_status = 1 ";
		}

		$sql = "
		SELECT * FROM `".root_table."product_group` 
		WHERE `product_group_deleted`= 0 AND product_group_parent = 0 {$clause} 
		ORDER BY `product_group_id` DESC
		";

        $results = $DB->fetch_data($sql, $this->cache_prefix);
		if ( $results )
		{
			foreach ($results as $result)
			{
				$result['data_item'] = $this->getchild_group_all($result['product_group_id'], $type, 0, $all);
 				$output[] = $result;
			}
		}

		return $output;
	}

	// Get all child
	// Max loop 5
	public function getchild_group_all($parent_id = 0, $type = 0, $loop = 0, $all = 0 ) 
	{
		global $CMS, $DB, $member;

		$output = [];

		// Max loop
        $loop = intval($loop) + 1;

        if( $parent_id > 0 AND $loop <= 5 ) 
        {
        	$clause = " AND product_group_type = '{$type}' AND product_group_parent = '{$parent_id}' ";
			if( ! $all )
			{
				$clause .= " AND product_group_status = 1 ";
			}

			$sql = "
			SELECT * FROM `".root_table."product_group` 
			WHERE `product_group_deleted`= 0 {$clause} 
			ORDER BY `product_group_id` DESC
			";

			$results = $DB->fetch_data($sql, $this->cache_prefix);

			if($results)
            {
            	foreach ($results as $result)
				{
					$result['data_item'] = $this->getchild_group_all($result['product_group_id'], $type, $loop, $all);
	 				$output[] = $result;
				}
            }
		}

		return $output;
	}

	function countProductGroupByType($type = '')
    {
    	global $CMS, $DB;

    	$output = 0;

    	$clause = '';
    	if( is_numeric($type) )
    	{
    		$clause = " AND PG.product_group_type='{$type}' ";
    	}

    	$sql = "
    	SELECT COUNT(PG.product_group_id) AS cnt  
    	FROM ".root_table."product_group AS PG 
    	WHERE PG.product_group_deleted = 0 {$clause} 
    	";

    	$result = $DB->fetch_data($sql, $this->cache_prefix);
    	if( is_array($result) AND isset($result[0]['cnt']))
    	{
    		$output = $result[0]['cnt']*1;
    	}

    	return $output;
    }	
}
?>