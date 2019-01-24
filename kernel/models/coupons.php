<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";//0101
	exit();
}
use \models\dashboard;
$CMS->coupons = new class_coupons;

class class_coupons {

	public $CMS = "";
	
	/**
	 * @param @record_cnt
	 *		The order number of Data
	 */
	
	public $record_cnt = 0;
	
	/**
	 * @param @arrange_data
	 *		Arrange Data, using for re-order the listing
	 */
	
	public $arrange_data = "";
	
	/**
	 * @param @sql_query
	 *		The SQL Query for listing Data
	 */
	 
	public $sql_query = "";

	/**
	 * @param @sql_add
	 *		The additional SQL for $sql_query
	 */

	public $sql_add = "";
	
	/**
	 * @param $control
	 *		0 for no control, 1 for has control, DONT CHANGE the default value
	 */
	
	public $control = 0;
	
	/**
	 * @param $action_control
	 *		HTML action control
	 */
	
	public $action_control = "";
	
	/**
	 * @param $html
	 *		The templates
	 */
	 
	public $html;
	
	/**
	 * @param $per_page
	 *		Per page
	 */

	public $per_page = 20;
	
	/**
	 * @param $prefix_html
	 *		For page link
	 */
	public $total = 0;
	public $prefix_html = "";
	public $suffix_html = "";
	
	/**
	 * @param $coupon_project
	 *		Use for multiple projects
	 */

	public $coupon_project = "";

    /**
     * @var string
     *      Use for thumbnail image
     */
    public $thumb_folder = "thumbnail";

    /**
     * @var array
     *      Define size for images thumb (Width)
     */
    public $thumb_size = [
        'L' => 550,
//        'M' => 300,
//        'S' => 150
    ];

    public $cache_prefix = "coupon";

	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	
	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			$this->html = $CMS->class->template->load_template("skin_coupons");
		}
	}
	
	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("coupon_time,coupon_name,coupon_id");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "coupon_time";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "desc";
		// Create SQL Query for listing Data
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."coupon WHERE coupon_deleted=0 AND {$this->sql_add} 1=1 ORDER BY {$default_field} {$default_order}", $this->per_page, $this->prefix_html, $this->suffix_html);
		$this->total =  $DB->num_rows($DB->query("SELECT * FROM ".root_table."coupon WHERE coupon_deleted=0 AND {$this->sql_add} 1=1 "));
	}

	public function html()
	{
		global $CMS, $DB, $member;

		$this->loadhtml();

		// Display Header
		$output .= $this->html->coupon_header();
		
		if ( $DB->num_rows( $CMS->coupons->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->coupons->sql_query ) )
			{
				// Convert info
				$result = $CMS->coupons->convertvalue($result);
				
				// Display Middle
				$output .= $this->html->coupon_middle($result);
			}
		}
		else
		{
			// Display No data
			$output .= $this->html->coupon_none();

			// No data
			$CMS->is_error = 1;
		}
		
		// Display
		$output .= $this->html->coupon_footer();
		
		// If the page is giving no data
		if ( $CMS->input['page'] > 1 && $CMS->is_error == 1)
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=coupon");
		}
		
		return $output;
	}
	
	//===========================================================================
	//  AUTO RUN
	//===========================================================================

	public function auto_run()
	{
		global $CMS, $DB, $member;

		$this->loadhtml();
                   
		//-----------------------------------------------------------
		// ACTION CONTROLLER
		//-----------------------------------------------------------
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_coupon_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_coupon_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";

			// Check permission to Delete
			if ( $CMS->permit["coupons_delete"] == true )
			{
				$data .= "<option value='delete_all'>{$CMS->lang['coupon_action_delete']}</option>";
				$this->control = 1;
			}
			
			$data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

			$CMS->class->cache->save("user_{$member['user_id']}_coupon_controller_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}
		
		if ( $CMS->vars['action_controller']  OR $CMS->permit["coupon_search"] == 1 )
		{
			$this->action_control = $this->html->coupon_control();
		}
		
		// Get category
		//$CMS->config_coupon->data();
	}

	//===========================================================================
	//  DATA
	//===========================================================================

	public function defaultvalue($data)
	{
		global $CMS;

		$data['coupon_display'] = $data['coupon_display'] ? $data['coupon_display'] : 1;

		return $data;
	}

	public function convertvalue($data, $type = 0, $project = "")
	{
		global $CMS, $DB;
	
		// Replace search content
		$data = $CMS->class->search->convertvalue($data);
		
		// Rewrite URL
	
		// Convert Register to GMT
		$data['coupon_time_bkk'] = $data['coupon_time'] ;
		$data['coupon_time'] = $CMS->class->date->date_format( $data['coupon_time'], 1 );
		$data['coupon_time_update'] = $data['coupon_time_update'] ? $CMS->class->date->date_format( $data['coupon_time_update'], 1 ) : "<i>N/A</i>";

		// Check permission to read Info
		if ( $type == 1 )
		{
			$data['coupon_name_bk'] = "<a href='{$CMS->vars['root_domain']}/{$data['coupon_shorturl']}'>{$data['coupon_name']}</a>";
		}
		else if ( $CMS->permit["coupon_read"] == true )
		{
			$data['coupon_name_bk'] = "<a href='{$CMS->vars['root_domain']}/?site=coupon&act=show&id={$data['coupon_id']}'>{$data['coupon_name']}</a>";
		}else
		{
			$data['coupon_name_bk'] = $data['coupon_name'];
		}
		
		// get html list image
		$list_image = $this->getListImage($data['coupon_id']);
		foreach ($list_image as $key => $value) 
		{
			$link = "{$CMS->vars['upload_dir']}/coupon/{$value['gali_image']}";
			$data['li_html'] .=<<<EOF
				<li>
		          <image style="max-width: 120px; max-height: 120px;" src="{$link}" class='image_avatar'/>
		          <span onclick="delFile(this,1);" class="del_image" id="{$value['gali_id']}"><i class="fa fa-times" aria-hidden="true"></i></span>
		        </li>
EOF;

		}
		
		$data['cat_name'] = $CMS->config_coupon->get_info($data['cat_id'], "cat_name");
		// Description
		$data['coupon_description'] = $CMS->class->editor->substr(strip_tags($data['coupon_description']), 0, 500);
		// Replace the IS Hot
		$data['coupon_is_hot_bk'] = $CMS->lang["coupon_is_hot_{$data['coupon_is_hot']}"];	
	
		$data['coupon_display_bk'] = $CMS->lang["display_{$data['coupon_display']}"];		
		
		$data['coupon_end_date'] = $data['coupon_end_date'] ? $CMS->class->date->date_format( $data['coupon_end_date'], 1 ) : "";
		

		// Bgcolor
		$data['bgcolor'] = $this->record_cnt % 2 != 0 ? "#FFFFFF" : "#F6F6F6";
		
		// User
		$data['user_name'] = $CMS->user->get_info($data['user_id'],"user_name");
		
		// Count
		$data['record_cnt'] = $this->record_cnt;
		
		$this->record_cnt++;
		
		return $data;
	}
	
	public function searchvalue($data)
	{
		global $CMS, $DB;

		// Convert Register to GMT
		$data['coupon_time'] = $CMS->class->date->date_format( $data['coupon_time'], 0 );

		// Replace the Status
		$data['coupon_display'] = $CMS->lang["display_{$data['coupon_display']}"];
		// Replace the IS Active Post
		$data['coupon_active'] = $CMS->lang["coupon_active_color_{$data['coupon_active']}"];	
		
		// Category
		$cat = $CMS->config_coupon->get_info($data['cat_id']);
		$data['cat_id'] = $cat['cat_name'];

		return $data;
	}
	
	//===========================================================================
	//  INFO
	//===========================================================================
	
	public function get_info( $record_id = 0, $field_name = "")
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "coupons" )
		{
			$record_id = intval($CMS->input['id']);
		}
                
                //print_r($record_id);exit;

		// Clear record
		$record_id = strip_tags($record_id);
		
		// Check record		
		if ( ! $record_id )
		{
			return false;
		}

        $sql_add  = '';
		
		// Check type
		//$sql_add .= ($this->coupon_project ? " AND coupon_project='{$this->coupon_project}' " : "");

        $sql = "SELECT * FROM ".root_table."coupon WHERE (coupon_id='{$record_id}' OR coupon_name='{$record_id}') AND coupon_deleted=0 {$sql_add} ORDER BY coupon_id DESC LIMIT 1";

		$data = $DB->fetch_data($sql, $this->cache_prefix)[0];

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
	
	public function check_exist( $field, $value = "", $except_value = "" )
	{
		global $CMS, $DB, $member;
		
		if ( ! $field )
		{
			return true;
		}
		
		if ( $except_value )
		{
			$DB->query("SELECT * FROM ".root_table."coupon WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND coupon_deleted=0");
		}
		else
		{
			$DB->query("SELECT * FROM ".root_table."coupon WHERE {$field}='{$value}' AND coupon_deleted=0");
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

	//===========================================================================
	//  ADD
	//===========================================================================
	
	public function add()
	{
		global $CMS, $DB, $member;

		// User input p_design
		$coupon_name = strip_tags($CMS->class->editor->input(urldecode($CMS->input['coupon_name']), 'text'));
		$coupon_desc_1 = strip_tags($CMS->class->editor->input(urldecode($CMS->input['coupon_desc_1']), 'text'));
		$coupon_desc_2 = strip_tags($CMS->class->editor->input(urldecode($CMS->input['coupon_desc_2']), 'text'));
		$coupon_desc_3 = strip_tags($CMS->class->editor->input(urldecode($CMS->input['coupon_desc_3']), 'text'));
		$coupon_end_date = $CMS->input['coupon_end_date'] ? $CMS->class->date->date2time(urldecode($CMS->input['coupon_end_date'])) : 0;
		$coupon_status = intval(urldecode($CMS->input['coupon_status']));
		$coupon_time = time();
		$coupon_coupon_code = strip_tags($CMS->class->editor->input(urldecode($CMS->input['coupon_coupon_code']), 'text'));
		$user_id = $member['user_id'];
		$coupon_image_alt = urldecode($CMS->input['coupon_image_alt']);
		 
		$p_design = strip_tags($CMS->class->editor->input(urldecode($CMS->input['p_design']), 'text'));
	 	
	 	$coupon_upload_option = $option_upload_image = intval($CMS->input['option_upload_image']);
 
	 	if($option_upload_image == 1)
	 	{
	 		//print_r (json_encode(array("a" => $_FILES['upload_img_original'])));exit;
	 		$file_1_tmp = isset($_FILES['upload_img_original']['tmp_name']) ? $_FILES['upload_img_original']['tmp_name'] : "";
			$file_1_name = isset($_FILES['upload_img_original']['name']) ? $_FILES['upload_img_original']['name'] : "";
			$file_1_type = isset($_FILES['upload_img_original']['type']) ? $_FILES['upload_img_original']['type'] : "";
			$file_1_size = isset($_FILES['upload_img_original']['size']) ? $_FILES['upload_img_original']['size'] : "";
			$file_1_error = isset($_FILES['upload_img_original']['error']) ? $_FILES['upload_img_original']['error'] : "";
			$file_1_ext =  explode("/",$file_1_type)[1];

			if($file_1_tmp == "" )
			{
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['no_upload_image']}", "data_option" => ""));exit;

				 
			}
			if($file_1_name == "blob")
 			{
 				$file_1_name .= ".png";
 			}

			$file_1_name =  "img_coupon_original_".time()."_".$file_1_name;
			$coupon_image_original = $file_1_location = strtolower(time()."_".$file_1_name);

			$CMS->class->image->check_folder_img("coupon","",0);

			$imgPath = "{$CMS->vars['upload_dir']}/coupon/{$file_1_location}";
			$check = @copy($file_1_tmp, $imgPath);	

			// Filter style
			$designtext =  array_values($CMS->input['designtext']);
			$designtext = array_reverse($designtext);
			$style_push = explode("|||", $CMS->input['style_push']);
			$style_p_push = explode("|||", $CMS->input['style_p_push']);
		 

			for ($i=0; $i<= count($designtext); $i++) {
			 
				 if($designtext[$i] != "")
				 {
				 	$style_editor[$i]["text"] = $designtext[$i];
					$style_editor[$i]["style_push"] = $style_push[$i];
					$style_editor[$i]["style_p_push"] = $style_p_push[$i];
				 }
			}
			$coupon_style_content = json_encode($style_editor,  JSON_UNESCAPED_UNICODE );
	 	}
 
		// Check input
		if ( ! $coupon_name ) { $CMS->errormsg = "{$CMS->lang['coupon_incomplete_name']}"; return false; }
		$file_tmp = isset($_FILES['list_image']['tmp_name']) ? $_FILES['list_image']['tmp_name'] : "";
		$file_name = isset($_FILES['list_image']['name']) ? $_FILES['list_image']['name'] : "";
		$file_type = isset($_FILES['list_image']['type']) ? $_FILES['list_image']['type'] : "";
		$file_size = isset($_FILES['list_image']['size']) ? $_FILES['list_image']['size'] : "";
		$file_error = isset($_FILES['list_image']['error']) ? $_FILES['list_image']['error'] : "";
		
		$file_ext =  explode("/",$file_type)[1];

		// Check dung luong file upload
		$max = 18;
		$max_file_upload = 1024*1024*$max;

		if($file_name == "")
		{
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['no_upload_image']}", "data_option" => ""));exit;
		}
		if($file_size > $max_file_upload )
		{
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['msg_maxfile_upload']}", "data_option" => ""));exit;

			//$_SESSION['error_msg'] = $CMS->lang['msg_maxfile_upload'].$max."MB";
			//return false;
		}

		
	
		$product_image = "";

		if ( $file_name )
		{
			$file_name =  "img_coupon".time().".".$file_ext;
			$file_location = strtolower(time()."_".$file_name);

			if (!in_array($file_ext, array("jpg","png","gif","jpeg"))) {
    			//$_SESSION['error_msg'] = $CMS->lang['invalid_upload_file'];
				//return false;
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['invalid_upload_file']}", "data_option" => ""));exit;
				
			}  

			// if ( $CMS->class->attachment->is_image($file_name, ".".$file_ext) == false )
			// {   print json_encode(array("a"=> $file_ext));exit; 
			// 	$_SESSION['error_msg'] = $CMS->lang['invalid_upload_file'];
			// 	return false;
			// }

			$CMS->class->image->check_folder_img("coupon","",0);

			$imgPath = "{$CMS->vars['upload_dir']}/coupon/{$file_location}";
			$check = @copy($file_tmp, $imgPath);
			  // print json_encode(array("as"=> $imgPath));exit; 
			if(!$check)
			{
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['msg_error_upload']}", "data_option" => ""));exit;
				//$_SESSION['error_msg'] = $CMS->lang['msg_error_upload'];
				//return false;
			}

            //Create thumb
            /*foreach ($this->thumb_size as $keySize => $valSize)
            {
                $CMS->class->image->resize($imgPath, \lib\image::getThumb($imgPath, $this->thumb_folder, "{$keySize}_"), $valSize);
            }*/
			
			$coupon_image = $file_location;
               
			// Insert data
			$count = $DB->query("INSERT INTO ".root_table."coupon (coupon_name, coupon_desc_1, coupon_desc_2, coupon_desc_3,  coupon_time, user_id, coupon_coupon_code,coupon_image, coupon_image_alt,coupon_upload_option, coupon_style_content,coupon_image_original, coupon_end_date, coupon_status) VALUES ('{$coupon_name}', '{$coupon_desc_1}', '{$coupon_desc_2}', '{$coupon_desc_3}', '{$coupon_time}','{$member['user_id']}','{$coupon_coupon_code}','{$coupon_image}','{$coupon_image_alt}', '{$coupon_upload_option}', '{$coupon_style_content}', '{$coupon_image_original}', '{$coupon_end_date}', '{$coupon_status}')");

			$CMS->class->cache->mdelete($this->cache_prefix);
			
			if($count)
			{
				$coupon_id = $DB->last_insert_id();
                                
				// Create log
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['coupon_added']}: <b>{$coupon_name}</b>")."<br />";

				// Get info
				$coupon = $this->get_info($coupon_name);
				
				  //Sync WHM data
		        if($CMS->vars['web_free']== 1 AND intval($CMS->vars['ibe_synced']) == 1)
		    	{
					
					$coupon_image_link = !empty($coupon_image) && file_exists($CMS->vars['upload_dir'].'/coupon/'.$coupon_image) ? $CMS->vars['upload_url'].'/coupon/'.$coupon_image : "";
		    		$data_api['coupon_name']   		      = "{$coupon_name}";
		    		$data_api['coupon_desc_1']            = "{$coupon_desc_1}";
		    		$data_api['coupon_desc_2']            = "{$coupon_desc_2}";
		    		$data_api['coupon_desc_3']            = "{$coupon_desc_3}";
		    		$data_api['coupon_time'] 		      = "{$coupon_time}";
		    		$data_api['coupon_coupon_code']       = "{$coupon_coupon_code}";
		    		$data_api['coupon_image']             = "{$coupon_image}";
		    		$data_api['coupon_end_date']          = "{$coupon_end_date}";
		    		$data_api['coupon_status']            = "{$coupon_status}";
		    		$data_api['coupon_image_link']   	  = "{$coupon_image_link}";
		    		$data_api['site_id']   		          = "{$CMS->vars['site_id']}";
		    		$data_api['original_id']   	  		  = "{$coupon['coupon_id']}";
		    	 
		    		$CMS->api->whm->execute('coupon_add', $data_api); 
		     	}

				// Delete cache
				$CMS->class->cache->mdelete("coupon");
				
				

			}

			return $coupon;
		}

		
	}
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
                
                
                
		// Get info
		$coupon_bk = $this->get_info($CMS->input['id']);
                
		$CMS->class->logs->old_data = $coupon_bk;
		$coupon_name = strip_tags($CMS->class->editor->input(urldecode($CMS->input['coupon_name']), 'text'));
		$coupon_desc_1 = strip_tags($CMS->class->editor->input(urldecode($CMS->input['coupon_desc_1']), 'text'));
		$coupon_desc_2 = strip_tags($CMS->class->editor->input(urldecode($CMS->input['coupon_desc_2']), 'text'));
		$coupon_desc_3 = strip_tags($CMS->class->editor->input(urldecode($CMS->input['coupon_desc_3']), 'text'));
		$coupon_end_date = $CMS->input['coupon_end_date'] ? $CMS->class->date->date2time(urldecode($CMS->input['coupon_end_date'])) : 0;
		$coupon_status = intval(urldecode($CMS->input['coupon_status']));
		$coupon_coupon_code = strip_tags($CMS->class->editor->input(urldecode($CMS->input['coupon_coupon_code']), 'text'));
		$coupon_time_update = time();
		$coupon_image_alt = urldecode($CMS->input['coupon_image_alt']);

        $coupon_upload_option = $option_upload_image = intval($CMS->input['option_upload_image']);
 
	 	if($option_upload_image == 1)
	 	{
	 		//print_r (json_encode(array("a" => $_FILES['upload_img_original'])));exit;
	 		$file_1_tmp = isset($_FILES['upload_img_original']['tmp_name']) ? $_FILES['upload_img_original']['tmp_name'] : "";
			$file_1_name = isset($_FILES['upload_img_original']['name']) ? $_FILES['upload_img_original']['name'] : "";
			$file_1_type = isset($_FILES['upload_img_original']['type']) ? $_FILES['upload_img_original']['type'] : "";
			$file_1_size = isset($_FILES['upload_img_original']['size']) ? $_FILES['upload_img_original']['size'] : "";
			$file_1_error = isset($_FILES['upload_img_original']['error']) ? $_FILES['upload_img_original']['error'] : "";
			$file_1_ext =  explode("/",$file_1_type)[1];

			$file_1_name =  "img_coupon_original_".time()."_".$file_1_name;
			
			if($file_1_tmp != "")
			{
				if($file_1_name == "blob")
	 			{
	 				$file_1_name .= ".png";
	 			}
 			
				$coupon_image_original = $file_1_location = strtolower(time()."_".$file_1_name);
				$CMS->class->image->check_folder_img("coupon","",0);
				$imgPath = "{$CMS->vars['upload_dir']}/coupon/{$file_1_location}";
				$check = @copy($file_1_tmp, $imgPath);	
			}
			else
			{
				$coupon_image_original = $coupon_bk['coupon_image_original'];
			}

			// Filter style
			$designtext =  array_values($CMS->input['designtext']);
			$designtext = array_reverse($designtext);
			$style_push = explode("|||", $CMS->input['style_push']);
			$style_p_push = explode("|||", $CMS->input['style_p_push']);
		 

			for ($i=0; $i<= count($designtext); $i++) {
			 
				 if($designtext[$i] != "")
				 {
				 	$style_editor[$i]["text"] = $designtext[$i];
					$style_editor[$i]["style_push"] = $style_push[$i];
					$style_editor[$i]["style_p_push"] = $style_p_push[$i];
				 }
			}
			$coupon_style_content = json_encode($style_editor,  JSON_UNESCAPED_UNICODE );
	 	}
 

		// Check input
		if ( ! $coupon_name ) { $CMS->errormsg = "{$CMS->lang['coupon_incomplete_name']}"; return false; }

		$file_tmp = isset($_FILES['list_image']['tmp_name']) ? $_FILES['list_image']['tmp_name'] : "";
		$file_name = isset($_FILES['list_image']['name']) ? $_FILES['list_image']['name'] : "";
		$file_type = isset($_FILES['list_image']['type']) ? $_FILES['list_image']['type'] : "";
		$file_size = isset($_FILES['list_image']['size']) ? $_FILES['list_image']['size'] : "";
		$file_error = isset($_FILES['list_image']['error']) ? $_FILES['list_image']['error'] : "";
		
		//$file_ext = $CMS->class->attachment->get_ext( $file_name );
        $file_ext =  explode("/",$file_type)[1];       
                

		// Check dung luong file upload
		$max = 10;
		$max_file_upload = 1024*1024*$max;
		if($file_size > $max_file_upload )
		{
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['msg_maxfile_upload']}", "data_option" => ""));exit;
			//$_SESSION['error_msg'] = $CMS->lang['msg_maxfile_upload'].$max."MB";
			//return false;
		}

		$number = rand(1,1000);
		//$file_name = str_replace( " ", "_", $file_name );
		//$file_location = strtolower(time()."_".$number.$file_name);
		$product_image = "";
		if ( $file_name )
		{
			$file_name =  "img_coupon".time().".".$file_ext;
			$file_location = strtolower(time()."_".$file_name);


			if (!in_array($file_ext, array("jpg","png","gif","jpeg"))) {
    			//$_SESSION['error_msg'] = $CMS->lang['invalid_upload_file'];
				//return false;
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['invalid_upload_file']}", "data_option" => ""));exit;
				
			}  


			// if ( $CMS->class->attachment->is_image($file_name, $file_ext) == false )
			// {
			// 	print json_encode(array("status" => "error", "msg" => "{$CMS->lang['msg_maxfile_upload']}", "data_option" => ""));exit;
			// 	//$_SESSION['error_msg'] = $CMS->lang['invalid_upload_file'];
			// 	//return false;
			// }

			$CMS->class->image->check_folder_img("coupon","",0);
                        
            $imgPath = "{$CMS->vars['upload_dir']}/coupon/{$file_location}";
			$check = move_uploaded_file($file_tmp, $imgPath);
			if(!$check)
			{
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['msg_error_upload']}", "data_option" => ""));exit;
				//$_SESSION['error_msg'] = $CMS->lang['msg_error_upload'];
				//return false;
			}
			
			$coupon_image = $file_location;
			$oldPath = "{$CMS->vars['upload_dir']}/coupon/{$coupon_bk['coupon_image']}";
			@unlink($oldPath);

            //Create thumb
            /*foreach ($this->thumb_size as $keySize => $valSize)
            {
                $CMS->class->image->resize($imgPath, \lib\image::getThumb($imgPath,$this->thumb_folder, "{$keySize}_"), $valSize);

                @unlink(\lib\image::getThumb($oldPath, $this->thumb_folder, "{$keySize}_"));
            }*/
		}else
		{
			$coupon_image = $coupon_bk['coupon_image'];
		}

		// update data
		$count = $DB->query("UPDATE ".root_table."coupon SET coupon_name = '{$coupon_name}', coupon_desc_1 = '{$coupon_desc_1}', coupon_desc_2 = '{$coupon_desc_2}', coupon_desc_3 = '{$coupon_desc_3}', coupon_coupon_code = '{$coupon_coupon_code}', coupon_time_update = '{$coupon_time_update}', coupon_image = '{$coupon_image}', coupon_image_alt='{$coupon_image_alt}',  coupon_upload_option =  '{$coupon_upload_option}', coupon_style_content = '{$coupon_style_content}', coupon_image_original = '{$coupon_image_original}', coupon_end_date='{$coupon_end_date}', coupon_status='{$coupon_status}' WHERE coupon_id = '{$coupon_bk['coupon_id']}'");

        $CMS->class->cache->mdelete($this->cache_prefix);
		
		$CMS->class->logs->key = "coupon_{$coupon_bk['coupon_id']}";
		// Get info
		$coupon = $this->get_info($coupon_name);


				//Sync WHM data
		        if($CMS->vars['web_free']== 1 AND intval($CMS->vars['ibe_synced']) == 1)
		        {
					
					$coupon_image_link = !empty($coupon_image) && file_exists($CMS->vars['upload_dir'].'/coupon/'.$coupon_image) ? $CMS->vars['upload_url'].'/coupon/'.$coupon_image : "";
		    		$data_api['coupon_name']   		      = "{$coupon_name}";
		    		$data_api['coupon_desc_1']            = "{$coupon_desc_1}";
		    		$data_api['coupon_desc_2']            = "{$coupon_desc_2}";
		    		$data_api['coupon_desc_3']            = "{$coupon_desc_3}";
		    		$data_api['coupon_time'] 		      = "{$coupon_time}";
		    		$data_api['coupon_coupon_code']       = "{$coupon_coupon_code}";
		    		$data_api['coupon_image']             = "{$coupon_image}";
		    		$data_api['coupon_end_date']          = "{$coupon_end_date}";
		    		$data_api['coupon_status']            = "{$coupon_status}";
		    		$data_api['coupon_image_link']   	  = "{$coupon_image_link}";
		    		$data_api['site_id']   		          = "{$CMS->vars['site_id']}";
		    		$data_api['original_id']   	  		  = "{$coupon['coupon_id']}";
		     
		    		$CMS->api->whm->execute('coupon_edit', $data_api); 
		     	}



		// Step 2: Save detail logs
		$CMS->class->logs->save_detail("coupon",$coupon['coupon_id'],$coupon);
                
                $_SESSION["msg"] = "{$CMS->lang['coupons_edited']}: {$coupon['coupon_name']}";
		
		return $coupon;

	}
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB;
		
		// Get info
		$data = $this->get_info();
                
		// Check existing
		if ( ! $data ) { return false; }
		
		// Xoa hinh cuar bai viet
		@unlink("{$CMS->vars['upload_dir']}/coupon/{$data['coupon_image']}");
		$DB->query("UPDATE ".root_table."coupon SET coupon_deleted = 1 WHERE coupon_id={$data['coupon_id']}");
		//Sync WHM data
        if($CMS->vars['web_free']== 1 AND intval($CMS->vars['ibe_synced']) == 1)
    	 {
    		$data_api['site_id']   		      = "{$CMS->vars['site_id']}";
    		$data_api['original_id']   	  	  = "{$coupon['coupon_id']}";
    		$CMS->api->whm->execute('coupon_delete', $data_api); 
    	}



        $CMS->class->cache->mdelete($this->cache_prefix);
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['coupon_deleted']} <b>{$data['coupon_name']}</b>")."<br />";

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=coupons&page={$CMS->input['page']}");
		
		return true;
	}
	
	public function mdelete()
	{
		global $CMS, $DB;
		
		$deleted = 0;
		
		$_SESSION["msg"] .= "";
		
		for ( $i = 0; $i < intval( $CMS->input["data_cnt"] ); $i++ )
		{
			$id = intval( $CMS->input["id_{$i}"] );
				
			if ( $id )
			{
				$data = $this->get_info($id);

				// Xoa hinh anh trong attach
				$CMS->news->delete_attach("coupon",$data['coupon_id']);
				// Xoa hinh cuar bai viet
				if($data['image_location'] != "")
				{
						@unlink("{$CMS->vars['upload_dir']}/coupon/{$data['image_location']}");
						//@unlink("{$CMS->vars['upload_dir']}/coupon/thumbresize/{$data['image_location']}");
				}
				$DB->query("DELETE FROM ".root_table."coupon WHERE coupon_id={$data['coupon_id']}");
		
				// Update Cat count
				$cat = $CMS->config_coupon->get_info($data['cat_id']);
				$CMS->config_coupon->update_count($cat['cat_id'],"dev");
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['coupon_deleted']} <b>{$data['coupon_name']}</b>")."<br />";
				
				$deleted = 1;
			}
		}

        $CMS->class->cache->mdelete($this->cache_prefix);
		
		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['coupon_delete_failed']}";
		}

		return true;
	}
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB;

		// Setup data
		$CMS->class->search->table_name = "coupon";
		$CMS->class->search->ignored_fields = array();
		$CMS->class->search->changed_fields = array();
		$CMS->class->search->fields_type = array("coupon_time" => "time");
		$CMS->class->search->search_type = 0;
		$CMS->class->search->fields_prefix = "";
		$CMS->class->search->fields_replace = array("cat_id" => "cat_id");
		
		// Output
		$data = $CMS->class->search->get_info();
		
		// Update SQL Query
		$this->sql_add .= $data;
		
		// Get List
		$this->listing();
		if($CMS->input['is_exel']=="coupon")
		{
			$CMS->report->report['format'] = "coupon";
			$CMS->report->report_display = 2;
			$CMS->report->build_excel_header();
			$CMS->report->build_excel_module();
			$CMS->report->build_excel_footer();
		}
	}
	
	
	
	//===========================================================================
	//  ACTIVE POST
	//===========================================================================
	
	public function active()
	{
		global $CMS, $DB, $member;
		
		// Get info
		$coupon_bk = $this->get_info();

		// User input
		$coupon_active = intval($CMS->input['method']);
		if($coupon_active == 2)
		{
			$DB->query("UPDATE ".root_table."coupon SET coupon_active ='{$coupon_active}', coupon_royalty =0 WHERE coupon_id='{$coupon_bk['coupon_id']}'");
		}
		else
		{
			$DB->query("UPDATE ".root_table."coupon SET coupon_active ='{$coupon_active}' WHERE coupon_id='{$coupon_bk['coupon_id']}'");
		}

        $CMS->class->cache->mdelete($this->cache_prefix);

		if($coupon_active != $coupon_bk['coupon_active'])
		{
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['convert_status']} {$coupon_bk['coupon_name']}<b> {$CMS->lang["coupon_active_{$coupon_bk['coupon_active']}"]} -> {$CMS->lang["coupon_active_{$coupon_active}"]}</b>")."<br />";
		}
		$coupon = $this->get_info();

		// Delete cache
		$CMS->class->cache->mdelete("coupon");

		return $coupon;
	}
	
	
	function getListImage($coupon_id, $type = 0)
	{
		global $CMS, $DB;

		$data = array();
		$output = "";
		// if($coupon_id)
		// {

            $sql = "SELECT * FROM ".root_table."coupon WHERE coupon_deleted = 0 ORDER BY coupon_time DESC";

            $results = $DB->fetch_data($sql, $this->cache_prefix);

			if($results)
			{
				foreach ($results as $result)
				{
					// if($type)
					// {
						$link_image = \lib\image::getThumb("{$CMS->vars['upload_url']}/coupon/{$result['coupon_image']}", $this->thumb_folder, 'L_', 1);
						$btn_control = "";
						if($CMS->permit['coupons_delete'])
						{
						    $btn_control .= <<<EOF
						      <button type="button" class="btn" onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=coupons&act=delete&id={$result['coupon_id']}');"><i class="font-icon font-icon-trash"></i></button>
EOF;

						}
					$output .=<<<EOF
						<div class="coupon-col">
			              <article class="coupon-item" style="height: 158px;">
			                <img class="coupon-picture" src="{$link_image}" alt="" height="158">
			                <div class="coupon-hover-layout">
			                  <div class="coupon-hover-layout-in">
			                    <p class="coupon-item-title">{$result['coupon_name']}</p>
			                    <p>{$result['coupon_description']}</p>
			                    <div class="btn-group">
			                      <button type="button" href="{$link_image}" class="btn view_detail">
			                        <i class="font-icon font-icon-eye"></i>
			                      </button>
			                      <a href="{$CMS->vars['root_domain']}/?site=coupons&act=edit&id={$result['coupon_id']}" class="btn" id="{$result['coupon_id']}">
			                        <i class="font-icon font-icon-pencil" aria-hidden="true"></i>
			                      </a>
			                      {$btn_control}
			                    </div>
			                    <p>{$time_due}</p>
			                  </div>
			                </div>
			              </article>
			            </div><!--.coupon-col-->
EOF;
					// }// End if
				}
			}
			
		// }

		// if($type)
		// {
			return $output;
		// }else
		// {
		// 	return $data;
		// }

	}
	
	
	
}

?>