<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->manufacture=new ClassManufacture;

class ClassManufacture {
	public $per_page = 20;
	public $show_page = '';
	public $sql_query = '';
	public $arrange_data = '';
	public $prefix_html = "";
	public $suffix_html = "";

	public $cache_prefix = 'manufacture';

	public function listing() {
		global $CMS, $DB, $member;

		$this->arrange_data = trim("manufacture_id,manufacture_name,manufacture_code,user_id,manufacture_time,manufacture_status");
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "manufacture_id";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "DESC";
		$where = '';

		if (!empty($CMS->input['m_id'])) {
			$where .= " AND `manufacture_id`='{$CMS->input['m_id']}' ";
		}

		if (!empty($CMS->input['m_name'])) {
			$where .= " AND `manufacture_name` LIKE '%{$CMS->input['m_name']}%' ";
		}

		$sql = "SELECT * FROM `".root_table."manufacture` WHERE `manufacture_deleted`=0 {$where} ORDER BY {$default_field} {$default_order}";

		list($this->show_page, $results) = $DB->fetch_listing($sql,$this->per_page,$this->prefix_html,$this->suffix_html,$CMS->input['page'],$this->cache_prefix);

		if($results)
        {
            foreach ($results as $result)
            {
                $arr[] = $this->convertvalue($result);
            }
        }

        return $arr;
	}

	public function convertvalue($data=null) {
		global $CMS, $DB, $member;

		$data['manufacture_parent_c'] = $this->getInfo($data['manufacture_parent'], 'manufacture_name');
		$data['manufacture_time_c'] = $CMS->class->date->date_format($data['manufacture_time'],1);
		$data['user_id_c'] = $CMS->user->get_info($data['user_id'], 'user_name');

		switch ($data['manufacture_status']) {
			case '1':
				$data['manufacture_status_c'] = '<i class="font-icon font-icon-check-bird color-green"></i> '.$CMS->lang['m_status_01'];
				break;
			default:
				$data['manufacture_status_c'] = '<i class="font-icon font-icon-close-2 color-red"></i> '.$CMS->lang['m_status_00'];
				break;
		}

		return $data;
	}

	public function getInfo($record_id=null,$field_name='*') {
		global $CMS, $DB, $member;

		if(!$record_id) return false;

		$sql = "SELECT {$field_name} FROM `".root_table."manufacture` WHERE (`manufacture_id`='{$record_id}' OR `manufacture_name` = '{$record_id}') AND `manufacture_deleted`=0 LIMIT 1";

		$data = $DB->fetch_data($sql, $this->cache_prefix)[0];

        if ($field_name !== '*') {
            return $data[$field_name];
        }

        return $data;
	}

	public function getAll() {
		global $CMS, $DB, $member;

		$sql = "SELECT * FROM `".root_table."manufacture` WHERE  `manufacture_deleted`=0 ORDER BY `manufacture_id` DESC";

		$arr = $DB->fetch_data($sql, $this->cache_prefix);

		return $arr;
	}
	public function getParent($id_childen=null) {
		global $CMS, $DB, $member;

		$sql = "SELECT * FROM `".root_table."manufacture` WHERE `manufacture_deleted` = 0 AND `manufacture_parent` = 0 AND `manufacture_id` != '{$id_childen}' ORDER BY `manufacture_id` DESC";

		$arr = $DB->fetch_data($sql,$this->cache_prefix);

		return $arr;
	}

	public function add($m_manufacture_parent=null, $m_name=null, $m_code=null, $m_status=null, $m_description=null) {
		if (!empty($m_name)  ) {
			global $CMS, $DB, $member;
			$DB->query("INSERT INTO `".root_table."manufacture` (`manufacture_time`, `user_id`, `manufacture_parent`, `manufacture_name`, `manufacture_code`, `manufacture_status`, `manufacture_description`) VALUES ('".time()."','{$member['user_id']}','{$m_manufacture_parent}','{$m_name}','{$m_code}','{$m_status}','{$m_description}')");

			//Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$id = $DB->last_insert_id();
			$CMS->class->logs->insert("Add_manufacture_{$id}");
			return $id;
		}
		return false;
	}
	public function upAvartar($id=null, $url_avartar=null) {
		if (!empty($id) && !empty($url_avartar)) {
			global $CMS, $DB, $member;
			$DB->query("UPDATE `".root_table."manufacture` SET `manufacture_avartar`='{$url_avartar}' WHERE `manufacture_deleted`=0 AND `manufacture_id`='{$id}'");

            //Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			return true;
		}
		return false;
	}
	public function edit($data_info=null, $m_manufacture_parent, $m_name, $m_code,$m_status, $m_description) {
		if (!empty($data_info)  ) {
			global $CMS, $DB, $member;
			$key = "Edit_manufacture_{$data_info['manufacture_id']}";
			$CMS->class->logs->key = $key;
			$CMS->class->logs->old_data = $data_info;
			$CMS->class->logs->insert($key);
			$DB->query("UPDATE `".root_table."manufacture` SET `manufacture_parent`='{$m_manufacture_parent}',`manufacture_name`='{$m_name}',`manufacture_code`='{$m_code}',`manufacture_status`='{$m_status}',`manufacture_description`='{$m_description}',`manufacture_avartar`='{$data_info['manufacture_avartar']}' WHERE `manufacture_deleted`='0' AND `manufacture_id`='{$data_info['manufacture_id']}'");


            //Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$CMS->class->logs->key = $key;
			$CMS->class->logs->save_detail("manufacture",$data_info['manufacture_id'],$this->getInfo($data_info['manufacture_id']));
		}
		return false;
	}
	public function deleted($id=null) {
		/*
		if (!empty($id)) {
			global $CMS, $DB, $member;
			$DB->query("UPDATE `".root_table."sell_customer` SET `sc_deleted`=1 WHERE `sc_id`='{$id}'");

		    //Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$CMS->class->logs->insert("Deleted_sell_customer_{$id}");
		}
		*/
		return false;
	}



	public function add_ajax( ) 
	{
		global $CMS, $DB, $member;
  		    // $m_manufacture_parent = $CMS->input['m_manufacture_parent'];
			$manufacture_name = urldecode(trim($CMS->input['m_name']));
			$manufacture_code =  urldecode(trim($CMS->input['m_code']));
 
			$manufacture_status = isset($CMS->input['m_status']) ? intval($CMS->input['m_status']) : 1;
			$manufacture_description =  $CMS->class->editor->input(urldecode(trim($CMS->input['m_description'])), "text");
			
			if (empty($manufacture_name)) 
			{
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['m_name_err']}"));
				exit;
			}

			if ($CMS->manufacture->getInfo($manufacture_name, 'manufacture_id')) 
			{
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['m_name_exist']}"));
					exit;
			}

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
			$manufacture_avartar = "";
			if ( $file_name )
			{
				if ( $CMS->class->attachment->is_image($file_name, $file_ext) == false )
				{
					$arr_img = array("msg" => $CMS->lang['invalid_upload_file'] , "status" => "error");
					print json_encode($arr_img);exit;
					// return false;
				}

				$check = @copy($file_tmp, "{$CMS->vars['upload_dir']}/manufacture/{$file_location}");
				if(!$check)
				{
					$arr_img = array("msg" => $CMS->lang['msg_error_upload_img'], "status" => "error");
					print json_encode($arr_img);exit;
				}
				
				$manufacture_avartar = $file_location;
			}

			$DB->query("INSERT INTO `".root_table."manufacture` (`manufacture_time`, `user_id`, `manufacture_name`, `manufacture_code`, `manufacture_status`, `manufacture_description`, manufacture_avartar) VALUES ('".time()."','{$member['user_id']}','{$manufacture_name}','{$manufacture_code}','{$manufacture_status}','{$manufacture_description}', '{$manufacture_avartar}')");

            //Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$id = $DB->last_insert_id();
			$CMS->class->logs->insert("Add_manufacture_{$id}");
			return $id;

	}

	public function getOptionManufacture($m_id=0)
	{
		global $CMS, $DB;

		$sql = "SELECT * FROM ".root_table."manufacture WHERE manufacture_deleted = 0 AND manufacture_status = 1 ORDER BY manufacture_name ASC";

		$cacheData = $DB->fetch_data($sql, $this->cache_prefix);

		$output = "<option value>{$CMS->lang['select_manufacture']}</option>";

		if($cacheData)
		{
			foreach ($cacheData as $result)
			{
				if($m_id == $result['manufacture_id'])
				{
					$selected = "selected='selected'";
				}else
				{
					$selected = "";
				}
				$output .= "<option value='{$result['manufacture_id']}' {$selected}>{$result['manufacture_name']}</option>";
			}
		}

		return $output;
	}

	
}
?>