<?php
namespace models;

use \core\ezy;
if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->config_gallery = new class_config_gallery;

class class_config_gallery {

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
	 * @param $html_data
	 *		HTML of records
	 */
	
	public $html_data = 0;
	
	/**
	 * @param $html
	 *		The templates
	 */
	 
	 public $html;

	 public $thumb_folder = "thumbnail";
	 public $thumb_size = [
	    'L' => 550,
//        'M' => 300,
//        'S' => 150
    ];
    public $cache_prefix = 'gallery_category'; //Tiền tố cho cache key hoặc tên file cache
    public $form_career_custom = array("dsg");

	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	
	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			$this->html = $CMS->class->template->load_template("skin_config_gallery");		
		}
	}
	
	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("cat_time,cat_id,cat_name,cat_status");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "cat_order";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "ASC";
		
		// SQL Condition
		$this->sql_add .= " cat_deleted=0 AND ";
		$sql = "SELECT * FROM ".root_table."gallery_category WHERE {$this->sql_add} 1=1  ORDER BY {$default_field} {$default_order}";

		list($CMS->show_page, $data) = $DB->fetch_listing($sql,$this->per_page,$this->prefix_html,$this->suffix_html,$CMS->input['page'],$this->cache_prefix);
		$this->pages_cnt = $CMS->class->page->total_row;
		return $data;
	}
	
		//===========================================================================
	//  LISTING DATA CATEGORY HOME
	//===========================================================================
	
	public function listing_home()
	{
		global $CMS, $DB;
		
			
		// Update Arrange Data
		$this->arrange_data = trim("cat_time,cat_id,cat_name,cat_status");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "cat_order_home";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "ASC";
		
		// SQL Condition
		$this->sql_add .= " cat_deleted=0 AND ";

		// Create SQL Query for listing Data
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."gallery_category WHERE cat_display_home =1  AND {$this->sql_add}  1=1  ORDER BY {$default_field} {$default_order}");
		$this->pages_cnt = $DB->num_rows( $this->sql_query );
	}
	
	
	public function html($data = [])
	{
		global $CMS, $DB, $member;
		
		$this->loadhtml();
		
		// Display Header
		$output = $this->html->header();

		if($data)
        {
            foreach ($data as $result)
            {
                // Convert info
                $result = $CMS->config_gallery->convertvalue($result);

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
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_gallery");
		}
		
		return $output;
	}



	public function html_home()
	{
		global $CMS, $DB, $member;
		
		$this->loadhtml();
		
		// Display Header
		$output .= $this->html->header_home();
		
		if ( $DB->num_rows( $CMS->config_gallery->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->config_gallery->sql_query ) )
			{
				// Convert info
				$result = $CMS->config_gallery->convertvalue($result);
				
				// Display Middle
				$output .= $this->html->middle_home($result);
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
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_gallery");
		}
		
		return $output;
	}


	public function data()
	{
		global $CMS, $DB;

		$sql = "SELECT * FROM ".root_table."gallery_category WHERE cat_deleted=0";

		$results = $DB->fetch_data($sql, $this->cache_prefix);

		$output = "";

		foreach ( $results as $cat )
		{
			$output .= "<option value='{$cat['cat_id']}'>{$cat['cat_name']}</option>";
		}

		$this->html_data = $output;
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
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_config_gallery_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_config_gallery_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";

			// Check permission to Delete
			if ( $CMS->permit["config_gallery_delete"] == true )
			{
				// $data .= "<option value='delete_all'>{$CMS->lang['action_delete']}</option>";
				$this->control = 1;
			}
			// Check permission to Arrange
			if ( $CMS->permit["config_gallery_arrange"] == true )
			{
				$data .= "<option value='arrange'>{$CMS->lang['cat_action_arrange']}</option>";
				$this->control = 1;
			}
			$data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

			$CMS->class->cache->save("user_{$member['user_id']}_config_gallery_controller_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}
		
		if ( $CMS->vars['action_controller'] )
		{
			$this->action_control = $this->html->control();
		}
	}

	//===========================================================================
	//  DATA
	//===========================================================================

	public function defaultvalue($data)
	{
		global $CMS;

		$data['cat_status'] = $data['cat_status'] ? $data['cat_status'] : 1;

		return $data;
	}
	
	public function convertvalue($data)
	{
		global $CMS, $DB;
	
		// Replace search content
		$data = $CMS->class->search->convertvalue($data);

		// Rewrite URL
		$data['cat_shorturl'] = ($CMS->vars['is_rewrite'] == 1 && $data['cat_shorturl']) ? "loai-tin/{$data['cat_shorturl']}" : "?site=news&view=category&id={$data['cat_id']}";
		
		// Convert Register to GMT
		$data['cat_time'] = $CMS->class->date->date_format( $data['cat_time'], 1 );

		// Check permission to read Info
		// if ( $CMS->permit["config_gallery_read"] == true )
		// {
		// 	$data['cat_name'] = "<a href='{$CMS->vars['root_domain']}/?site=config_gallery&act=show&id={$data['cat_id']}'>{$data['cat_name']}</a>";
		// }


		$name = @json_decode($data['cat_name'], true);
        $data['cat_name'] = $name ? $name : $data['cat_name'];

        $shorturl = @json_decode($data['cat_shorturl'], true);
        $data['cat_shorturl'] = $shorturl ? $shorturl : $data['cat_shorturl'];

        $description = @json_decode($data['cat_description'], true);
        $data['cat_description'] = $description ? $description : $data['cat_description'];

        if($CMS->vars['translations'])
        {
            //Đa ngôn ngữ
            if(!is_array($data['cat_shorturl']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $shorturl = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    $shorturl[$langCode] = $data['cat_shorturl'];
                }

                $data['cat_shorturl'] = $shorturl;
            }

            if(!is_array($data['cat_name']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $name = [];
                $name_bk = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                	$name[$langCode] = $data['cat_name'];
	            	// Check permission to read Info
					if ( $CMS->permit["config_gallery_read"] == true )
					{
						$name_bk[$langCode] = "<a href='{$CMS->vars['root_domain']}/?site=config_gallery&act=show&id={$data['cat_id']}'>{$data['cat_name']}</a>";
						
					}

                }

                $data['cat_name_bk'] = $name_bk;
                $data['cat_name'] = $name;
            }else
            {
            	$data['cat_name_bk'] = $data['cat_name'];

            	$name_bk = [];
            	// Check permission to read Info
				if ( $CMS->permit["config_gallery_read"] == true )
				{
					foreach ($data['cat_name_bk'] as $langCode => $cat_name)
	                {
						$name_bk[$langCode] = "<a href='{$CMS->vars['root_domain']}/?site=config_gallery&act=show&id={$data['cat_id']}'>{$cat_name}</a>";
					}
					$data['cat_name_bk'] = $name_bk;
				}

            }

            if(!is_array($data['cat_description']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $description = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    $description[$langCode] = $data['cat_description'];
                }

                $data['cat_description'] = $description;
            }

        }
        else
        {
        	if(is_array($data['cat_shorturl']))
            {
            	$data['cat_shorturl'] = $data['cat_shorturl'][$CMS->vars['default_language']];
        	}

        	if(is_array($data['cat_description']))
            {
            	$data['cat_description'] = $data['cat_description'][$CMS->vars['default_language']];
        	}

            if(is_array($data['cat_name']))
            {
                //Neu la dang mang thi chuyen ve dang chuoi binh thuong
                $data['cat_name'] = $data['cat_name'][$CMS->vars['default_language']];

                // Check permission to read Info
				if ( $CMS->permit["config_gallery_read"] == true )
				{
					$data['cat_name_bk'] = "<a class='link' href='{$CMS->vars['root_domain']}/?site=config_gallery&act=show&id={$data['cat_id']}'>{$data['cat_name']}</a>";
				}

            }else
            {
            	$data['cat_name_bk'] = $data['cat_name'];
            	if ( $CMS->permit["config_gallery_read"] == true )
				{
					$data['cat_name_bk'] = "<a class='link' href='{$CMS->vars['root_domain']}/?site=config_gallery&act=show&id={$data['cat_id']}'>{$data['cat_name']}</a>";
				}
            }


        }

        
		
		// Replace the Status
		$data['cat_status_bk'] = $data['cat_status'];
		$data['cat_status'] = $CMS->lang["display_{$data['cat_status']}"];

		// Parent CAtegory
		$data['parent_id_bk'] = $CMS->config_parent_news->get_info($data['parent_id'], "cat_name");

		// Bgcolor
		$data['bgcolor'] = $this->record_cnt % 2 != 0 ? "#FFFFFF" : "#F6F6F6";
		
		// Count
		$data['record_cnt'] = $this->record_cnt;
		
		$this->record_cnt++;
		
		return $data;
	}
	
	public function searchvalue($data)
	{
		global $CMS, $DB;

		// Convert Register to GMT
		$data['cat_time'] = $CMS->class->date->date_format( $data['cat_time'], 0 );

		// Replace the Status
		$data['cat_status'] = $CMS->lang["display_{$data['cat_status']}"];

		return $data;
	}
	
	//===========================================================================
	//  INFO
	//===========================================================================
	
	public function get_info( $record_id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;

		if ( ! $record_id )
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

		$sql = "SELECT * FROM ".root_table."gallery_category WHERE (cat_id='{$record_id}' OR cat_name='{$record_id}' OR cat_shorturl='{$record_id}') AND cat_deleted=0 ORDER BY cat_id DESC LIMIT 1";

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
			$DB->query("SELECT * FROM ".root_table."gallery_category WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND cat_deleted=0");
		}
		else
		{
			$DB->query("SELECT * FROM ".root_table."gallery_category WHERE {$field}='{$value}' AND cat_deleted=0");
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

		// User input
		$cat_name = $CMS->input["cat_name"];
		$cat_description = $CMS->input["cat_description"];

		$check = true;
		if(is_array($cat_name) and is_array($cat_description))
        {
        	foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
            	$cat_shorturl[$langCode] = $CMS->class->seo->cleanurl($cat_name[$langCode]);

            	$cat_name[$langCode] = preg_replace( "/\r|\n/", "", $cat_name[$langCode]);
            	$cat_name[$langCode] = str_replace("'", "&#39;", $cat_name[$langCode]);

            	$cat_description[$langCode] = preg_replace( "/\r|\n/", "", $cat_description[$langCode]);
            	$cat_description[$langCode] = str_replace("'", "&#39;", $cat_description[$langCode]);

            	if($langCode == $CMS->vars['default_language'])
				{
					$name_alert = $cat_name[$langCode];
				}
            }

            if (empty($cat_name[$CMS->vars['default_language']])) {
                $_SESSION['msg'] .= $CMS->lang['incomplete_name'].' ('.$CMS->vars['translations'][$CMS->vars['default_language']].')<br>';
                $check = false;
            }
            
            $cat_name = @json_encode($cat_name, JSON_UNESCAPED_UNICODE);
            $cat_description = @json_encode($cat_description, JSON_UNESCAPED_UNICODE);
            $cat_shorturl = @json_encode($cat_shorturl, JSON_UNESCAPED_UNICODE);
        }
        else
        {
            $cat_name = $CMS->class->editor->input('cat_name');
			$cat_description = $CMS->class->editor->input('cat_description');
			$cat_shorturl = $CMS->class->seo->cleanurl($cat_name);

			$name_alert = $cat_name;
        }



		$cat_status = intval($CMS->input["cat_status"]);
		$cat_is_hot = intval($CMS->input["cat_is_hot"]);
		$meta_title = trim($CMS->class->editor->input("meta_title"));
		$meta_description = trim($CMS->class->editor->input("meta_description"));
		$meta_keywords = trim($CMS->class->editor->input("meta_keywords"));
		

		// Check input
		if ( ! $cat_name ) { $CMS->errormsg = "{$CMS->lang['incomplete_name']}"; return false; }
		// if ( $cat_status == "" ) { $CMS->errormsg = "{$CMS->lang['incomplete_status']}"; return false; }

		// Upload image
		$file_tmp = isset($_FILES['cat_image']['tmp_name']) ? $_FILES['cat_image']['tmp_name'] : "";
		$file_name = isset($_FILES['cat_image']['name']) ? $_FILES['cat_image']['name'] : "";
		$file_type = isset($_FILES['cat_image']['type']) ? $_FILES['cat_image']['type'] : "";
		$file_size = isset($_FILES['cat_image']['size']) ? $_FILES['cat_image']['size'] : "";
		$file_error = isset($_FILES['cat_image']['error']) ? $_FILES['cat_image']['error'] : "";
		
		$file_ext = $CMS->class->attachment->get_ext( $file_name );

		// Check dung luong file upload
		$max = 7;
		$max_file_upload = 1024*1024*$max;
		if($file_size > $max_file_upload )
		{
			$_SESSION['error_msg'] = $CMS->lang['msg_maxfile_upload'].$max."MB";
			return false;
		}

		$file_name = str_replace( " ", "_", $file_name );
		$file_location = strtolower(time()."_".$file_name);
		$cat_image = "";
                
		if ( $file_name )
		{
			if ( $CMS->class->attachment->is_image($file_name, $file_ext) == false )
			{
				$_SESSION['error_msg'] = $CMS->lang['invalid_upload_file'];
				return false;
			}

			$CMS->class->image->check_folder_img("gallery","",0);

			$imgPath = "{$CMS->vars['upload_dir']}/gallery/{$file_location}";
			$check = @copy($file_tmp, $imgPath);
			if(!$check)
			{
				$_SESSION['error_msg'] = $CMS->lang['msg_error_upload'];
				return false;
			}

			//Create thumb
            /*foreach ($this->thumb_size as $keySize => $valSize)
            {
                $CMS->class->image->resize($imgPath, \lib\image::getThumb($imgPath, $this->thumb_folder, "{$keySize}_"), $valSize);
            }*/

			$cat_image = $file_location;
		}

		// check False
		if($check == false)
		{
			return false;
		}

		if(ezy::$theme_key == "dsg" AND in_array(ezy::$theme_key , $this->form_career_custom))
		{
			$cat_cate = intval($CMS->input['cat_cate']);
 			// Insert data
			$DB->query("INSERT INTO ".root_table."gallery_category (cat_name, cat_description, cat_status, cat_time, cat_shorturl, meta_title, meta_description, meta_keywords, cat_is_hot, cat_image,cat_cate) VALUES ('{$cat_name}', '{$cat_description}', '{$cat_status}', '".time()."','{$cat_shorturl}', '{$meta_title}', '{$meta_description}', '{$meta_keywords}', '{$cat_is_hot}', '{$cat_image}', '{$cat_cate}')");
		}
		else
		{
			// Insert data
			$DB->query("INSERT INTO ".root_table."gallery_category (cat_name, cat_description, cat_status, cat_time, cat_shorturl, meta_title, meta_description, meta_keywords, cat_is_hot, cat_image) VALUES ('{$cat_name}', '{$cat_description}', '{$cat_status}', '".time()."','{$cat_shorturl}', '{$meta_title}', '{$meta_description}', '{$meta_keywords}', '{$cat_is_hot}', '{$cat_image}')");
		}
		


		//Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['added']} <b>{$name_alert}</b>")."<br />";

		// Get info
		$data = $this->get_info($cat_name);


		 //Sync WHM data
        if($CMS->vars['web_free']== 1 AND intval($CMS->vars['ibe_synced']) == 1)
    	{
    		$cat_image_link = !empty($cat_image) && file_exists($CMS->vars['upload_dir'].'/gallery/'.$cat_image) ? $CMS->vars['upload_url'].'/gallery/'.$cat_image : "";

    
    		$data_api['cat_name']   		  = "{$data['cat_name']}";
    		$data_api['cat_description']      = "{$data['cat_description']}";
    		$data_api['cat_status'] 		  = "{$data['cat_status']}";
    		$data_api['cat_time']             = "{$data['cat_time']}";
    		$data_api['cat_deleted']          = "{$data['cat_deleted']}";
    		$data_api['cat_shorturl']         = "{$data['cat_shorturl']}";
    		$data_api['cat_is_hot']           = "{$data['cat_is_hot']}";
    		$data_api['cat_image']            = "{$data['cat_image']}";
    		$data_api['cat_image_link']       = "{$cat_image_link}";
    		$data_api['original_id']   		  = "{$data['cat_id']}";
    		$data_api['site_id']   		      = "{$CMS->vars['site_id']}";
    		
    		$CMS->api->whm->execute('cat_gallery_add', $data_api); 
     	}


		return $data;
	}
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		// Get info
		$data = $this->get_info();

		// User input
		$cat_name = $CMS->input["cat_name"];
		$cat_description = $CMS->input["cat_description"];

		$check = true;
		if(is_array($cat_name) and is_array($cat_description))
        {
        	foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
            	$cat_shorturl[$langCode] = $CMS->class->seo->cleanurl($cat_name[$langCode]);

            	$cat_name[$langCode] = preg_replace( "/\r|\n/", "", $cat_name[$langCode]);
            	$cat_name[$langCode] = str_replace("'", "&#39;", $cat_name[$langCode]);

            	$cat_description[$langCode] = preg_replace( "/\r|\n/", "", $cat_description[$langCode]);
            	$cat_description[$langCode] = str_replace("'", "&#39;", $cat_description[$langCode]);

            	if($langCode == $CMS->vars['default_language'])
				{
					$name_alert = $cat_name[$langCode];
				}
            }

            if (empty($cat_name[$CMS->vars['default_language']])) {
                $_SESSION['msg'] .= $CMS->lang['incomplete_name'].' ('.$CMS->vars['translations'][$CMS->vars['default_language']].')<br>';
                $check = false;
            }
            
            $cat_name = @json_encode($cat_name, JSON_UNESCAPED_UNICODE);
            $cat_description = @json_encode($cat_description, JSON_UNESCAPED_UNICODE);
            $cat_shorturl = @json_encode($cat_shorturl, JSON_UNESCAPED_UNICODE);
        }
        else
        {
            $cat_name = $CMS->class->editor->input('cat_name');
			$cat_description = $CMS->class->editor->input('cat_description');
			$cat_shorturl = $CMS->class->seo->cleanurl($cat_name);

			$name_alert = $cat_name;
        }


		
		$cat_status = intval($CMS->input["cat_status"]);
		$cat_is_hot = intval($CMS->input["cat_is_hot"]);
		$meta_title = trim($CMS->class->editor->input("meta_title"));
		$meta_description = trim($CMS->class->editor->input("meta_description"));
		$meta_keywords = trim($CMS->class->editor->input("meta_keywords"));
		
		
		// Check input
		if ( ! $cat_name ) { $CMS->errormsg = "{$CMS->lang['incomplete_name']}"; return false; }

		// Upload image
		$file_tmp = isset($_FILES['cat_image']['tmp_name']) ? $_FILES['cat_image']['tmp_name'] : "";
		$file_name = isset($_FILES['cat_image']['name']) ? $_FILES['cat_image']['name'] : "";
		$file_type = isset($_FILES['cat_image']['type']) ? $_FILES['cat_image']['type'] : "";
		$file_size = isset($_FILES['cat_image']['size']) ? $_FILES['cat_image']['size'] : "";
		$file_error = isset($_FILES['cat_image']['error']) ? $_FILES['cat_image']['error'] : "";
		
		$file_ext = $CMS->class->attachment->get_ext( $file_name );

		// Check dung luong file upload
		$max = 7;
		$max_file_upload = 1024*1024*$max;
		if($file_size > $max_file_upload )
		{
			$_SESSION['error_msg'] = $CMS->lang['msg_maxfile_upload'].$max."MB";
			return false;
		}

		$file_name = str_replace( " ", "_", $file_name );
		$file_location = strtolower(time()."_".$file_name);
		$cat_image = "";
                
		if ( $file_name )
		{
			if ( $CMS->class->attachment->is_image($file_name, $file_ext) == false )
			{
				$_SESSION['error_msg'] = $CMS->lang['invalid_upload_file'];
				return false;
			}

			$CMS->class->image->check_folder_img("gallery","",0);

			$imgPath = "{$CMS->vars['upload_dir']}/gallery/{$file_location}";
			$check = @copy($file_tmp, $imgPath);
			if(!$check)
			{
				$_SESSION['error_msg'] = $CMS->lang['msg_error_upload'];
				return false;
			}

			//Create thumb
            /*foreach ($this->thumb_size as $keySize => $valSize)
            {
                $CMS->class->image->resize($imgPath, \lib\image::getThumb($imgPath, $this->thumb_folder, "{$keySize}_"), $valSize);
            }*/

			$cat_image = $file_location;
		}else
		{
			$cat_image = $data['cat_image'];
		}

		// check False
		if($check == false)
		{
			return false;
		}

		// Step 1: Save detail logs
		$CMS->class->logs->old_data = $data;
		
		// Update info
		$DB->query("UPDATE ".root_table."gallery_category SET cat_name='{$cat_name}', cat_description='{$cat_description}', cat_status='{$cat_status}', cat_shorturl='{$cat_shorturl}',meta_title='{$meta_title}' , meta_description='{$meta_description}', meta_keywords='{$meta_keywords}', cat_is_hot = '{$cat_is_hot}', cat_image='{$cat_image}' WHERE cat_id='{$data['cat_id']}'");

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);
		
		$CMS->class->logs->key = "config_gallery_{$data['cat_id']}";
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['edited']} <b>{$name_alert}</b>")."<br />";
		
		// Get info
		$data = $this->get_info();
		
		 //Sync WHM data
        if($CMS->vars['web_free']== 1 AND intval($CMS->vars['ibe_synced']) == 1)
    	{
			
			$cat_image_link = !empty($cat_image) && file_exists($CMS->vars['upload_dir'].'/gallery/'.$cat_image) ? $CMS->vars['upload_url'].'/gallery/'.$cat_image : "";
			
    		$data_api['cat_name']   		  = "{$data['cat_name']}";
    		$data_api['cat_description']      = "{$data['cat_description']}";
    		$data_api['cat_status'] 		  = "{$data['cat_status']}";
    		$data_api['cat_time']             = "{$data['cat_time']}";
    		$data_api['cat_deleted']          = "{$data['cat_deleted']}";
    		$data_api['cat_shorturl']         = "{$data['cat_shorturl']}";
    		$data_api['cat_is_hot']           = "{$data['cat_is_hot']}";
    		$data_api['cat_image']            = "{$data['cat_image']}";
    		$data_api['cat_image_link']       = "{$cat_image_link}";
    		$data_api['original_id']   		  = "{$data['cat_id']}";
    		$data_api['site_id']   		      = "{$CMS->vars['site_id']}";
    		
    		$CMS->api->whm->execute('cat_gallery_edit', $data_api); 
     	}



		// Step 2: Save detail logs
		$CMS->class->logs->key = "config_gallery_{$data['cat_id']}";
		$CMS->class->logs->save_detail("gallery_category",$data['cat_id'],$data);
		
		
		return $data;
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
		
		// Check del gallery
		if($this->checkDeleteGallery($data['cat_id']))
		{
			$_SESSION["msg"] .= "Please delete gallery in this category before delete it." ;
			// Redirect
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_gallery&page={$CMS->input['page']}");
	
			return false;
		}
		// Update info
		$DB->query("UPDATE ".root_table."gallery_category SET cat_deleted=1 WHERE cat_id={$data['cat_id']}");

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['deleted']} <b>{$data['cat_name']}</b>")."<br />";

	        if($CMS->vars['web_free']== 1 AND intval($CMS->vars['ibe_synced']) == 1)
	    	{
	    		$data_api['original_id']      = "{$data['cat_id']}";
	    		$data_api['site_id']   		  = "{$CMS->vars['site_id']}";
	    		
	    		$CMS->api->whm->execute('cat_gallery_delete', $data_api); 
	     	}

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_gallery&page={$CMS->input['page']}");
		
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

				$DB->query("UPDATE ".root_table."gallery_category SET cat_deleted=1 WHERE cat_id={$id}");
				
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['deleted']} <b>{$data['cat_name']}</b>")."<br />";
				
				$deleted = 1;
			}
		}

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['delete_failed']}";
		}

		return true;
	}
	
	//===========================================================================
	//  ARRANGE
	//===========================================================================

	public function arrange()
	{
		global $CMS, $DB;
		
		$_SESSION["msg"] .= "";
		$sql = $DB->query("SELECT * FROM ".root_table."gallery_category WHERE cat_deleted=0 ORDER BY cat_id ASC");
		
		while ( $data = $DB->fetch_array( $sql ) )
		{ 
			$order = intval( $CMS->input["order_{$data['cat_id']}"] );
			if ( $order )
			{
				$DB->query("UPDATE ".root_table."gallery_category SET cat_order='{$order}' WHERE cat_id='{$data['cat_id']}'");
			}
		}

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['cat_arranged']}")."<br />";

		return true;
	}
	
	//===========================================================================
	//  UPDATE POST COUNT
	//===========================================================================

	public function update_count($cat_id = "", $act = "add")
	{
		global $CMS, $DB;
	
		if($act == "add")
		{
			$DB->query("UPDATE ".root_table."gallery_category SET cat_count=cat_count+1 WHERE cat_id='{$cat_id}'");
		}
		elseif($act == "dev")
		{
			$data = $this->get_info($cat_id);
			if($data['cat_count'] > 0)
			{
				$DB->query("UPDATE ".root_table."gallery_category SET cat_count=cat_count-1 WHERE cat_id='{$cat_id}'");
			}
		}

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);
		
		return true;
	}
	
	//===========================================================================
	//  LOAD CATEGORY
	//===========================================================================
	
	public function load_category()
	{
		global $CMS, $DB;
		
		$sql = $DB->query("SELECT * FROM ".root_table."gallery_category WHERE cat_deleted=0 AND cat_status=1 ORDER BY cat_name ASC");
		
		$output = "";
		$cnt = 0;
		
		while ( $cat = $DB->fetch_array() )
		{
			$output .= "\t\tnews_menu[{$cnt}] = new Array('?site=news&view=category&id={$cat['cat_id']}', '{$cat['cat_name']}');\n";
			$cnt++;
		}
		
		$CMS->gui->html['news_menu'] = $output;
	}
	
	
	//===========================================================================
	//  LOAD PARENT CATEGORY
	//===========================================================================
	
	public function load_parent_category()
	{
		global $CMS, $DB;

		$sql = "SELECT * FROM ".root_table."gallery_category WHERE cat_deleted=0 AND cat_status=1 ORDER BY cat_name DESC";

		//Query
        $results = $DB->fetch_data($sql, $this->cache_prefix);

        $output = "";

        if($results)
        {
            foreach ( $results as $cat )
            {
                $output .= "<option value=\"{$cat['cat_id']}\">{$cat['cat_name']}</option>";
            }
        }

		$CMS->gui->html['parent_cate_news'] = $output;
	}
	
	//===========================================================================
	//  LOAD LIST CATEGORY
	//===========================================================================

	public function load_list_cate()
	{
		global $CMS, $DB;

		$sql = $DB->query("SELECT * FROM ".root_table."gallery_category WHERE cat_deleted=0 ORDER BY cat_id ASC");
		if($DB->num_rows($sql) > 0)
		{
			while ( $data = $DB->fetch_array( $sql ) )
			{
				$output .= "<option value='{$data['cat_id']}' >{$data['cat_name']}</option>";
			}
		}
		return $output;
	}

	function checkDeleteGallery($cat_id)
	{
		global $CMS, $DB;

		if($cat_id)
		{
			$sql = $DB->query("SELECT 0 FROM ".root_table."gallery WHERE cat_id = '{$cat_id}' AND gallery_deleted = 0");
			if($DB->num_rows($sql) > 0)
			{
				return true;
			}else
			{
				return false;
			}
		}
	}	
}

?>