<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}
use \models\dashboard;
$CMS->giftcards = new class_giftcards;

class class_giftcards {

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
	 * @param $giftcard_project
	 *		Use for multiple projects
	 */

	public $giftcard_project = "";

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

    /**
     * @var string $cache_prefix
     */
	public $cache_prefix = 'giftcard';

    /**
     * for cache gifcard items
     * @var string $cache_items_prefix
     */
	public $cache_items_prefix = 'giftcard_items.';

	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	
	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			$this->html = $CMS->class->template->load_template("skin_giftcards");
		}
	}
	
	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("giftcard_time,giftcard_name,giftcard_id");

		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "giftcard_time";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "desc";
		// Create SQL Query for listing Data
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."giftcard WHERE giftcard_deleted=0 AND {$this->sql_add} 1=1 ORDER BY {$default_field} {$default_order}", $this->per_page, $this->prefix_html, $this->suffix_html);
		$this->total =  $DB->num_rows($DB->query("SELECT * FROM ".root_table."giftcard WHERE giftcard_deleted=0 AND {$this->sql_add} 1=1 "));
	}

	public function html()
	{
		global $CMS, $DB, $member;

		$this->loadhtml();

		// Display Header
		$output .= $this->html->giftcard_header();
		
		if ( $DB->num_rows( $CMS->giftcards->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->giftcards->sql_query ) )
			{
				// Convert info
				$result = $CMS->giftcards->convertvalue($result);
				
				// Display Middle
				$output .= $this->html->giftcard_middle($result);
			}
		}
		else
		{
			// Display No data
			$output .= $this->html->giftcard_none();

			// No data
			$CMS->is_error = 1;
		}
		
		// Display
		$output .= $this->html->giftcard_footer();
		
		// If the page is giving no data
		if ( $CMS->input['page'] > 1 && $CMS->is_error == 1)
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=giftcard");
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
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_giftcard_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_giftcard_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";

			// Check permission to Delete
			if ( $CMS->permit["giftcards_delete"] == true )
			{
				$data .= "<option value='delete_all'>{$CMS->lang['giftcard_action_delete']}</option>";
				$this->control = 1;
			}
			
			$data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

			$CMS->class->cache->save("user_{$member['user_id']}_giftcard_controller_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}
		
		if ( $CMS->vars['action_controller']  OR $CMS->permit["giftcard_search"] == 1 )
		{
			// $this->action_control = $this->html->giftcard_control();
		}
		
		// Get category
		//$CMS->config_giftcard->data();
	}

	//===========================================================================
	//  DATA
	//===========================================================================

	public function defaultvalue($data)
	{
		global $CMS;

		$data['giftcard_display'] = $data['giftcard_display'] ? $data['giftcard_display'] : 1;

		return $data;
	}

	public function convertvalue($data, $type = 0, $project = "")
	{
		global $CMS, $DB;
	
		// Replace search content
		$data = $CMS->class->search->convertvalue($data);
		
		// Rewrite URL
	
		// Convert Register to GMT
		$data['giftcard_time_bkk'] = $data['giftcard_time'] ;
		$data['giftcard_time'] = $CMS->class->date->date_format( $data['giftcard_time'], 1 );
		$data['giftcard_time_update'] = $data['giftcard_time_update'] ? $CMS->class->date->date_format( $data['giftcard_time_update'], 1 ) : "<i>N/A</i>";

		// Check permission to read Info
		if ( $type == 1 )
		{
			$data['giftcard_name_bk'] = "<a href='{$CMS->vars['root_domain']}/{$data['giftcard_shorturl']}'>{$data['giftcard_name']}</a>";
		}
		else if ( $CMS->permit["giftcard_read"] == true )
		{
			$data['giftcard_name_bk'] = "<a href='{$CMS->vars['root_domain']}/?site=giftcard&act=show&id={$data['giftcard_id']}'>{$data['giftcard_name']}</a>";
		}else
		{
			$data['giftcard_name_bk'] = $data['giftcard_name'];
		}
		
		// get html list image
		$list_image = $this->getListImage($data['giftcard_id']);
		foreach ($list_image as $key => $value) 
		{
			$link = "{$CMS->vars['upload_dir']}/giftcard/{$value['gali_image']}";
			$data['li_html'] .=<<<EOF
				<li>
		          <image style="max-width: 120px; max-height: 120px;" src="{$link}" class='image_avatar'/>
		          <span onclick="delFile(this,1);" class="del_image" id="{$value['gali_id']}"><i class="fa fa-times" aria-hidden="true"></i></span>
		        </li>
EOF;

		}
		
		$data['cat_name'] = $CMS->config_giftcard->get_info($data['cat_id'], "cat_name");
		// Description
		$data['giftcard_description'] = $CMS->class->editor->substr(strip_tags($data['giftcard_description']), 0, 500);
		// Replace the IS Hot
		$data['giftcard_is_hot_bk'] = $CMS->lang["giftcard_is_hot_{$data['giftcard_is_hot']}"];	
	
		$data['giftcard_display_bk'] = $CMS->lang["display_{$data['giftcard_display']}"];		
		

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
		$data['giftcard_time'] = $CMS->class->date->date_format( $data['giftcard_time'], 0 );

		// Replace the Status
		$data['giftcard_display'] = $CMS->lang["display_{$data['giftcard_display']}"];
		// Replace the IS Active Post
		$data['giftcard_active'] = $CMS->lang["giftcard_active_color_{$data['giftcard_active']}"];	
		
		// Category
		$cat = $CMS->config_giftcard->get_info($data['cat_id']);
		$data['cat_id'] = $cat['cat_name'];

		return $data;
	}
	
	//===========================================================================
	//  INFO
	//===========================================================================
	
	public function get_info( $record_id = 0, $field_name = "")
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "giftcards" )
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

        $sql_add = '';

		// Check type
		//$sql_add .= ($this->giftcard_project ? " AND giftcard_project='{$this->giftcard_project}' " : "");

        //Cache
        $sql = "SELECT * FROM ".root_table."giftcard WHERE (giftcard_id='{$record_id}' OR giftcard_name='{$record_id}') AND giftcard_deleted=0 {$sql_add} ORDER BY giftcard_id DESC LIMIT 1";

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
			$DB->query("SELECT * FROM ".root_table."giftcard WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND giftcard_deleted=0");
		}
		else
		{
			$DB->query("SELECT * FROM ".root_table."giftcard WHERE {$field}='{$value}' AND giftcard_deleted=0");
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

        // print "<pre>";
        // print_r($CMS->input);exit;
		// User input
		$product_name = urldecode($CMS->input['product_name']);
		$product_name = strip_tags($CMS->class->editor->input($product_name,"text")); 
		$product_shorturl = $CMS->class->seo->cleanurl($product_name);
		$product_description = strip_tags($CMS->class->editor->input('product_description'));
		$product_price_sell = $CMS->input['product_price_sell'];
		$product_subitem = '[]';
		$product_time = time();
		$product_show = intval($CMS->input['product_show']);
		$product_tax = $CMS->input['product_tax'];
		// Key product group for gift card
        $key = "gift_cards";
        $product_group = $CMS->product_group->getInfo($key,"product_group_id");
        $product_cycle = 0;
		$user_id = $member['user_id'];
		$product_image_alt = $CMS->class->editor->input(urldecode($CMS->input['product_image_alt']),"text");
	 
		// Check input
		if ( ! $product_name ) { 
			
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['giftcard_incomplete_name']}", "data_option" => ""));exit;
			//$CMS->errormsg = "{$CMS->lang['giftcard_incomplete_name']}"; return false; 
		}
		if ( ! $product_price_sell ) { 
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['giftcard_incomplete_product_price']}", "data_option" => ""));exit;
			//$CMS->errormsg = "{$CMS->lang['giftcard_incomplete_product_price']}"; return false;
	     }
 		

 		$product_upload_option = $option_upload_image = intval($CMS->input['option_upload_image']);
 
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
			$file_1_name =  "img_product_original_".time()."_".$file_1_name;

			

			$product_image_original = $file_1_location = strtolower(time()."_".$file_1_name);
	 
			$CMS->class->image->check_folder_img("product","",0);

			$imgPath = "{$CMS->vars['upload_dir']}/product/{$file_1_location}";
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
			$product_style_content = json_encode($style_editor,  JSON_UNESCAPED_UNICODE );
	 	}


		// Upload image
		$file_tmp = isset($_FILES['product_image']['tmp_name']) ? $_FILES['product_image']['tmp_name'] : "";
		$file_name = isset($_FILES['product_image']['name']) ? $_FILES['product_image']['name'] : "";
		$file_type = isset($_FILES['product_image']['type']) ? $_FILES['product_image']['type'] : "";
		$file_size = isset($_FILES['product_image']['size']) ? $_FILES['product_image']['size'] : "";
		$file_error = isset($_FILES['product_image']['error']) ? $_FILES['product_image']['error'] : "";
		
		//$file_ext = $CMS->class->attachment->get_ext( $file_name );
		$file_ext =  explode("/",$file_type)[1];
		if($file_name == "")
		{
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['no_upload_image']}", "data_option" => ""));exit;
		}

		// Check dung luong file upload
		$max = 7;
		$max_file_upload = 1024*1024*$max;
		if($file_size > $max_file_upload )
		{
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['msg_maxfile_upload']}".$max."MB", "data_option" => ""));exit;

		 
		}

		//$file_name = str_replace( " ", "_", $file_name );
		//$file_location = strtolower(time()."_".$file_name);
		$file_name =  "img_product".time().".".$file_ext;
		$file_location = strtolower(time()."_".$file_name);
		//print json_encode(array("status" => "error", "msg" => $file_location, "data_option" => ""));exit;
		$product_image = "";
                
		if ( $file_name )
		{
			// if ( $CMS->class->attachment->is_image($file_name, $file_ext) == false )
			// {
			// 	$_SESSION['error_msg'] = $CMS->lang['invalid_upload_file'];
			// 	return false;
			// }
			if (!in_array($file_ext, array("jpg","png","gif"))) { // bo jpeg
    			//$_SESSION['error_msg'] = $CMS->lang['invalid_upload_file'];
				//return false;
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['invalid_upload_file']}", "data_option" => ""));exit;
				
			}  

			$CMS->class->image->check_folder_img("product","",0);

			$imgPath = "{$CMS->vars['upload_dir']}/product/{$file_location}";
			$check = @copy($file_tmp, $imgPath);
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


			$product_image = $file_location;
		}
        	 
		// Insert data
		$count = $DB->query("INSERT INTO ".root_table."product (product_name, product_description, product_price_sell, product_time, user_id, product_cycle, product_group, product_image, product_shorturl, product_subitem, product_show, product_image_alt,product_upload_option, product_style_content,product_image_original, product_tax) VALUES ('{$product_name}', '{$product_description}', '{$product_price_sell}', '{$product_time}', '{$user_id}', '{$product_cycle}', '{$product_group}', '{$product_image}', '{$product_shorturl}', '{$product_subitem}', '{$product_show}', '{$product_image_alt}', '{$product_upload_option}', '{$product_style_content}', '{$product_image_original}', '{$product_tax}')");

        // Delete cache
        $CMS->class->cache->mdelete($CMS->product->cache_prefix);

		if($count)
		{
			$product_id = $DB->last_insert_id();
			// Create log
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['giftcard_added']}: <b>{$product_name}</b>")."<br />";

			// Get info
			$giftcard = $CMS->product->get_info($product_name);
		}

		return $giftcard;
		
	}
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;

		// Get info
		$product_bk = $CMS->product->get_info($CMS->input['id']);
                
		$CMS->class->logs->old_data = $product_bk;
		// User input
		$product_name = urldecode($CMS->input['product_name']);
		$product_name = strip_tags($CMS->class->editor->input($product_name,"text")); 

		$product_shorturl = $CMS->class->seo->cleanurl($product_name);
		$product_description = strip_tags($CMS->class->editor->input('product_description'));
		$product_price_sell = $CMS->input['product_price_sell'];
		$product_subitem = '[]';
		$product_time = time();
		$product_show = intval($CMS->input['product_show']);
		$product_tax = $CMS->input['product_tax'];
		// Key product group for gift card
        $key = "gift_cards";
        $product_group = $CMS->product_group->getInfo($key,"product_group_id");
        $product_cycle = 0;
		$user_id = $member['user_id'];
		$product_image_alt = $CMS->class->editor->input(urldecode($CMS->input['product_image_alt']), "text");
		
		// Check input
		if ( ! $product_name ) { 
			
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['giftcard_incomplete_name']}", "data_option" => ""));exit;
			//$CMS->errormsg = "{$CMS->lang['giftcard_incomplete_name']}"; return false; 
		}
		if ( ! $product_price_sell ) { 
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['giftcard_incomplete_product_price']}", "data_option" => ""));exit;
			//$CMS->errormsg = "{$CMS->lang['giftcard_incomplete_product_price']}"; return false;
	     }

	    $product_upload_option = $option_upload_image = intval($CMS->input['option_upload_image']);
	    if($option_upload_image == 1)
	 	{
	 		//print_r (json_encode(array("a" => $_FILES['upload_img_original'])));exit;
	 		$file_1_tmp = isset($_FILES['upload_img_original']['tmp_name']) ? $_FILES['upload_img_original']['tmp_name'] : "";
			$file_1_name = isset($_FILES['upload_img_original']['name']) ? $_FILES['upload_img_original']['name'] : "";
			$file_1_type = isset($_FILES['upload_img_original']['type']) ? $_FILES['upload_img_original']['type'] : "";
			$file_1_size = isset($_FILES['upload_img_original']['size']) ? $_FILES['upload_img_original']['size'] : "";
			$file_1_error = isset($_FILES['upload_img_original']['error']) ? $_FILES['upload_img_original']['error'] : "";
			$file_1_ext =  explode("/",$file_1_type)[1];

			

			$file_1_name =  "img_product_original_".time()."_".$file_1_name;
			if($file_1_tmp != "")
			{
				if($file_1_name == "blob")
	 			{
	 				$file_1_name .= ".png";
	 			}
				$product_image_original = $file_1_location = strtolower(time()."_".$file_1_name);
				$CMS->class->image->check_folder_img("product","",0);
				$imgPath = "{$CMS->vars['upload_dir']}/product/{$file_1_location}";
				$check = @copy($file_1_tmp, $imgPath);	
			}
			else
			{
				$product_image_original = $product_bk['product_image_original'];
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
			$product_style_content = json_encode($style_editor,  JSON_UNESCAPED_UNICODE );
	 	}
	 	else
	 	{
	 		$product_image_original = $product_bk['product_image_original'];
	 		$product_style_content = $product_bk['product_style_content'];
	 		 

	 	}
		// Upload image
		$file_tmp = isset($_FILES['product_image']['tmp_name']) ? $_FILES['product_image']['tmp_name'] : "";
		$file_name = isset($_FILES['product_image']['name']) ? $_FILES['product_image']['name'] : "";
		$file_type = isset($_FILES['product_image']['type']) ? $_FILES['product_image']['type'] : "";
		$file_size = isset($_FILES['product_image']['size']) ? $_FILES['product_image']['size'] : "";
		$file_error = isset($_FILES['product_image']['error']) ? $_FILES['product_image']['error'] : "";
		
		$file_ext = $CMS->class->attachment->get_ext( $file_name );

//		  $file_ext =  explode("/",$file_type)[1];

		// Check dung luong file upload
		$max = 7;
		$max_file_upload = 1024*1024*$max;
		if($file_size > $max_file_upload )
		{
			$_SESSION['error_msg'] = $CMS->lang['msg_maxfile_upload'].$max."MB";
			return false;
		}

		//$file_name = str_replace( " ", "_", $file_name );
		//$file_location = strtolower(time()."_".$file_name);
		$product_image = "";
                
		if ( $file_name )
		{
			$file_name =  "img_product".time().".".$file_ext;
			$file_location = strtolower(time()."_".$file_name);

			if (!in_array($file_ext, array("jpg","png","gif"))) { // remove jpeg
    			//$_SESSION['error_msg'] = $CMS->lang['invalid_upload_file'];
				//return false;
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['invalid_upload_file']}", "data_option" => ""));exit;
				
			}  
			$CMS->class->image->check_folder_img("product","",0);

			$imgPath = "{$CMS->vars['upload_dir']}/product/{$file_location}";
			$check = @copy($file_tmp, $imgPath);
			if(!$check)
			{
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['msg_error_upload']}", "data_option" => ""));exit;
				//$_SESSION['error_msg'] = $CMS->lang['msg_error_upload'];
				//return false;
			}

			$oldPath = "{$CMS->vars['upload_dir']}/product/{$product_bk['product_image']}";
			@unlink($oldPath);

            //Create thumb
            /*foreach ($this->thumb_size as $keySize => $valSize)
            {
                $CMS->class->image->resize($imgPath, \lib\image::getThumb($imgPath,$this->thumb_folder, "{$keySize}_"), $valSize);

                @unlink(\lib\image::getThumb($oldPath, $this->thumb_folder, "{$keySize}_"));
            }*/
			
			$product_image = $file_location;
		}else
		{
			$product_image = $product_bk['product_image'];
		}

		// Insert data
		$count = $DB->query("UPDATE ".root_table."product SET product_name='{$product_name}', product_description='{$product_description}', product_price_sell='{$product_price_sell}', user_id='{$user_id}', product_image='{$product_image}', product_shorturl='{$product_shorturl}', product_show='{$product_show}', product_image_alt='{$product_image_alt}' ,  product_upload_option =  '{$product_upload_option}', product_style_content = '{$product_style_content}', product_image_original = '{$product_image_original}', product_tax='{$product_tax}'  WHERE product_id='{$product_bk['product_id']}'");

        // Delete cache
        $CMS->class->cache->mdelete($CMS->product->cache_prefix);

		if($count)
		{
			$CMS->class->logs->key = "giftcard_{$product_bk['product_id']}";
			// Get info
			$giftcard = $CMS->product->get_info($product_name);

			// Step 2: Save detail logs
			$CMS->class->logs->save_detail("giftcard",$giftcard['product_id'],$giftcard);
	                
	        $_SESSION["msg"] = "{$CMS->lang['giftcards_edited']}: {$giftcard['product_name']}";
			
			return $giftcard;
		}else
		{
			return false;
		}

		
		
		

	}
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB;
		
		// Get info
		$data = $CMS->product->get_info($CMS->input['id']);
                
		// Check existing
		if ( ! $data ) { return false; }
		
		// Xoa hinh cuar bai viet
		@unlink("{$CMS->vars['upload_dir']}/product/{$data['product_image']}");
		$DB->query("UPDATE ".root_table."product SET product_deleted = 1 WHERE product_id={$data['product_id']}");

        // Delete cache
        $CMS->class->cache->mdelete($CMS->product->cache_prefix);
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['giftcard_deleted']} <b>{$data['product_name']}</b>")."<br />";

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=giftcards&page={$CMS->input['page']}");
		
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
				$CMS->news->delete_attach("giftcard",$data['giftcard_id']);
				// Xoa hinh cuar bai viet
				if($data['image_location'] != "")
				{
						@unlink("{$CMS->vars['upload_dir']}/giftcard/{$data['image_location']}");
						//@unlink("{$CMS->vars['upload_dir']}/giftcard/thumbresize/{$data['image_location']}");
				}
				$DB->query("DELETE FROM ".root_table."giftcard WHERE giftcard_id={$data['giftcard_id']}");
		
				// Update Cat count
				$cat = $CMS->config_giftcard->get_info($data['cat_id']);
				$CMS->config_giftcard->update_count($cat['cat_id'],"dev");
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['giftcard_deleted']} <b>{$data['giftcard_name']}</b>")."<br />";
				
				$deleted = 1;
			}
		}
		
		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['giftcard_delete_failed']}";
		}

        // Delete cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		return true;
	}
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB;

		// Setup data
		$CMS->class->search->table_name = "giftcard";
		$CMS->class->search->ignored_fields = array();
		$CMS->class->search->changed_fields = array();
		$CMS->class->search->fields_type = array("giftcard_time" => "time");
		$CMS->class->search->search_type = 0;
		$CMS->class->search->fields_prefix = "";
		$CMS->class->search->fields_replace = array("cat_id" => "cat_id");
		
		// Output
		$data = $CMS->class->search->get_info();
		
		// Update SQL Query
		$this->sql_add .= $data;
		
		// Get List
		$this->listing();
		if($CMS->input['is_exel']=="giftcard")
		{
			$CMS->report->report['format'] = "giftcard";
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
		$giftcard_bk = $this->get_info();

		// User input
		$giftcard_active = intval($CMS->input['method']);
		if($giftcard_active == 2)
		{
			$DB->query("UPDATE ".root_table."giftcard SET giftcard_active ='{$giftcard_active}', giftcard_royalty =0 WHERE giftcard_id='{$giftcard_bk['giftcard_id']}'");
		}
		else
		{
			$DB->query("UPDATE ".root_table."giftcard SET giftcard_active ='{$giftcard_active}' WHERE giftcard_id='{$giftcard_bk['giftcard_id']}'");
		}

        // Delete cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		if($giftcard_active != $giftcard_bk['giftcard_active'])
		{
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['convert_status']} {$giftcard_bk['giftcard_name']}<b> {$CMS->lang["giftcard_active_{$giftcard_bk['giftcard_active']}"]} -> {$CMS->lang["giftcard_active_{$giftcard_active}"]}</b>")."<br />";
		}
		$giftcard = $this->get_info();

		return $giftcard;
	}
	
	
	function getListImage($giftcard_id=0, $type = 0)
	{
		global $CMS, $DB;

		$data = array();
		$output = "";
		// Key product group for gift card
        $key = "gift_cards";
        $product_group = $CMS->product_group->getInfo($key,"product_group_id");
		// if($giftcard_id)
		// {
            $sql = "SELECT * FROM ".root_table."product WHERE product_deleted=0 AND product_group='{$product_group}' ORDER BY product_time DESC";

            $results = $DB->fetch_data($sql, $CMS->product->cache_prefix);

			if($results)
			{
				foreach ($results as $result)
				{
					// if($type)
					// {
						$link_image = \lib\image::getThumb("{$CMS->vars['upload_url']}/product/{$result['product_image']}", $this->thumb_folder, 'L_', 1);
						$btn_control = "";

						$time_due = $CMS->class->date->date_format($result['product_time']);
						if($CMS->permit['giftcards_delete'])
						{
						    $btn_control .= <<<EOF
						      <button type="button" class="btn" onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=giftcards&act=delete&id={$result['product_id']}');"><i class="font-icon font-icon-trash"></i></button>
EOF;

						}

					$output .=<<<EOF
						<div class="giftcard-col">
			              <article class="giftcard-item" style="height: 158px;">
			                <img class="giftcard-picture" src="{$link_image}" alt="" height="158">
			                <div class="giftcard-hover-layout">
			                  <div class="giftcard-hover-layout-in">
			                    <p class="giftcard-item-title">{$result['product_name']}</p>
			                    <p>{$result['product_description']}</p>
			                    <div class="btn-group">
			                      <button type="button" href="{$link_image}" class="btn view_detail">
			                        <i class="font-icon font-icon-eye"></i>
			                      </button>
			                      <a href="{$CMS->vars['root_domain']}/?site=giftcards&act=edit&id={$result['product_id']}" class="btn"> <i class="font-icon font-icon-pencil" aria-hidden="true"></i></a>
			                     
			                      {$btn_control}
			                    </div>
			                    <p>{$time_due}</p>
			                  </div>
			                </div>
			              </article>
			            </div><!--.giftcard-col-->
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
	
	
	function editConfig()
	{
		global $CMS, $DB;
// print "<pre>"; print_r($_POST);exit;
		// xoá cache
		$CMS->class->cache->deletesql("config");
		$CMS->input['config'] = $_POST['config'];

		// insert bien trong config trước
		foreach ($CMS->input['config'] as $type => $arr_input) 
		{
			foreach ($arr_input as $key => $value) 
			{
				$value = $CMS->class->editor->input($value, "text");
				if($CMS->config_general->checkKey($key))
				{
					$DB->query("UPDATE ".root_table."conf_settings SET conf_value = '{$value}', conf_type = '{$type}' WHERE conf_key = '{$key}'");
				}else
				{
					$conf_title = $CMS->lang['title_'.$key];
					$DB->query("INSERT INTO ".root_table."conf_settings (conf_title, conf_key, conf_value, conf_type, conf_group) VALUES ('{$conf_title}', '{$key}', '{$value}', '{$type}', 1)");
				}

				$CMS->class->cache->mdelete($CMS->config_general->cache_prefix);
			}
			
		}

		return true;
	}

	function createGiftcard($order_item=[])
	{
		global $CMS, $DB;

		// Insert giftcard item
        require_once root_path."vendor/autoload.php"; 
        $qrcode = new \PHPQRCode\QRcode;
        // Check and create folder 
        $CMS->class->image->check_folder_img("giftcards","",0);
        
        // Input
        $str = "ABCDEFGHIJKLMNOPQRSTXYZ0987654321";
        $gitem_time = time();
        foreach ($order_item as $product) 
        {
            $product_id = $product['product_id'];
            $product['product_image'] = $CMS->product->getInfo($product_id, "product_image");
            $number = rand(0,10000);
            $number2 = rand(0,10000);
            $str_rand= $str[rand(0,33)];
            $gitem_code = md5("{$product_id}-{$number}-{$str_rand}-{$gitem_time}");
            $gitem_amount_remain = $gitem_amount = $product['ordi_price'];
            $gitem_time_update = $gitem_time;
            $ord_id = $product['ord_id'];
            $cus_id = intval($product['cus_id']);
// 
            // Create image barcode
            $qrcodeSrc = $CMS->vars['upload_dir'] . "/giftcards/{$number2}_{$gitem_code}.png";
            $content_barcode = $CMS->vars['root_domain'].'/giftcards/barcode/'.$gitem_code;
            $qrcode->png($content_barcode, $qrcodeSrc, 'L', 4, 2);

            // Link image will paste watermask
            $link_image = $CMS->vars['upload_dir']."/product/".$product['product_image'];

            //watemask giftcard
            $CMS->class->image->watermask( $qrcodeSrc, $link_image, $CMS->vars['upload_dir'] . "/giftcards/{$gitem_code}.png");
            // Unlink watermask
            @unlink($qrcodeSrc);
            // Insert database
            $DB->query("INSERT INTO ".root_table."giftcard_items (product_id, gitem_code, gitem_amount, gitem_amount_remain, gitem_time, gitem_time_update, ord_id, cus_id) VALUES ('{$product_id}', '{$gitem_code}', '{$gitem_amount}', '{$gitem_amount_remain}', '{$gitem_time}', '{$gitem_time_update}', '{$ord_id}', '{$cus_id}')");
            $gitem_id = $DB->last_insert_id();
            // Update gift card code
            $DB->query("UPDATE ".root_table."giftcard_items SET giftcard_code='G{$gitem_id}' WHERE gitem_id='{$gitem_id}'");

            $CMS->class->cache->mdelete($this->cache_items_prefix);
           
        }

        return true;
	}

	function getGitemByOrder($ord_id=0)
    {
        global $CMS, $DB;

        $sql = "SELECT gitem_code, giftcard_code, gitem_amount_remain FROM ".root_table."giftcard_items WHERE ord_id='{$ord_id}'";

        $results = $DB->fetch_data($sql, $this->cache_items_prefix);

        return $results;
    }
	
	function library_giftcard()
	{
		global $CMS, $DB;
		$path = root_path.'/public/giftcard_library/';
 
		//$files = array_diff(scandir($path), array('.', '..'));
	//	$files =  glob($path."*.{jpg,gif,png}",GLOB_BRACE  );
		// print_r ($files);exit;
 		$count = 0;
		   foreach( glob($path."*.{jpg,gif,png}",GLOB_BRACE  )  as $dir) {
	 
 				$count++;
 				$value = str_replace($path, '', $dir);
				$image =  "{$CMS->vars['parent_domain']}/public/giftcard_library/".$value;
				//$type = pathinfo($image, PATHINFO_EXTENSION);
			//	$data = file_get_contents($image);
				//$dataUri = 'data:image/' . $type . ';base64,' . base64_encode($data);
			 	$li .=<<<EOF
			 	  <li style="width:250px;float:left"><a data-slide-to="1" onclick="embed_gc('{$image}');"><img class="img-thumbnail" src="{$CMS->vars['public_url']}/giftcard_library/{$value}"><br>Pick</a></li>

EOF;

			}
			$output .=<<<EOF
			<ul class="list-inline text-center" style="margin-top:10px;display:none" id="library_giftcard">
                       {$li}
                                   
          <!--end of thumbnails--></ul>
EOF;

			 
 
		 
		$data_output = array($count, $output);
		return $data_output;
		//


	}
}

?>