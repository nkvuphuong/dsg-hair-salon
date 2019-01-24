<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}
use \core\ezy;
use models\dashboard;

$CMS->gallery = new class_gallery;

class class_gallery {

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
	 * @param $gallery_project
	 *		Use for multiple projects
	 */

	public $gallery_project = "";

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
        'L' => 600,
        'M' => 400,
        'S' => 200
    ];

    /**
     * @var string $show_page
     */
    public $show_page = '';

    /**
     * @var string $cache_prefix
     */
    public $cache_prefix = 'gallery';

	//===========================================================================
	//  LISTING DATA
	//===========================================================================

	public function loadhtml()
	{
		global $CMS;

		if ( !isset($this->html) )
		{
			$this->html = $CMS->class->template->load_template("skin_gallery");
		}
	}

	public function listing()
	{
		global $CMS, $DB;

		// Update Arrange Data
		$this->arrange_data = trim("gallery_time,gallery_name,gallery_id,gallery_display,cat_id");

		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "gallery_time";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "desc";
		// Create SQL Query for listing Data
        $sql = "SELECT * FROM ".root_table."gallery WHERE gallery_deleted=0 AND {$this->sql_add} 1=1 ORDER BY {$default_field} {$default_order}";

        list($this->show_page, $arr) = $DB->fetch_listing($sql,$this->per_page,$this->prefix_html,$this->suffix_html,$CMS->input['page'],$this->cache_prefix);

        return $arr;
	}

	public function html()
	{
		global $CMS, $DB, $member;

		$this->loadhtml();

		// Display Header
		$output .= $this->html->gallery_header();

		if ( $DB->num_rows( $CMS->gallery->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->gallery->sql_query ) )
			{
				// Convert info
				$result = $CMS->gallery->convertvalue($result);

				// Display Middle
				$output .= $this->html->gallery_middle($result);
			}
		}
		else
		{
			// Display No data
			$output .= $this->html->gallery_none();

			// No data
			$CMS->is_error = 1;
		}

		// Display
		$output .= $this->html->gallery_footer();

		// If the page is giving no data
		if ( $CMS->input['page'] > 1 && $CMS->is_error == 1)
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=gallery");
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

		if ( $CMS->class->cache->check("user_{$member['user_id']}_gallery_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_gallery_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";

			// Check permission to Delete
			if ( $CMS->permit["gallery_delete"] == true )
			{
				$data .= "<option value='delete_all'>{$CMS->lang['gallery_action_delete']}</option>";
				$this->control = 1;
			}

			$data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

			$CMS->class->cache->save("user_{$member['user_id']}_gallery_controller_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}

		if ( $CMS->vars['action_controller']  OR $CMS->permit["gallery_search"] == 1 )
		{
			$this->action_control = $this->html->gallery_control();
		}

		// Get category
		$CMS->config_gallery->data();
	}

	//===========================================================================
	//  DATA
	//===========================================================================

	public function defaultvalue($data)
	{
		global $CMS;

		$data['gallery_display'] = $data['gallery_display'] ? $data['gallery_display'] : 1;

		return $data;
	}

	public function convertvalue($data, $type = 0, $project = "")
	{
		global $CMS, $DB;

		// Replace search content
		$data = $CMS->class->search->convertvalue($data);

		// Rewrite URL

		// // Convert Register to GMT
		// $data['gallery_time_bkk'] = $data['gallery_time'] ;
		// $data['gallery_time'] = $CMS->class->date->date_format( $data['gallery_time'], 1 );
		// $data['gallery_time_update'] = $data['gallery_time_update'] ? $CMS->class->date->date_format( $data['gallery_time_update'], 1 ) : "<i>N/A</i>";


		$gallery_name = @json_decode($data['gallery_name'], true);
        $data['gallery_name'] = $gallery_name ? $gallery_name : $data['gallery_name'];
        $shorturl = @json_decode($data['gallery_shorturl'], true);
        $data['gallery_shorturl'] = $shorturl ? $shorturl : $data['gallery_shorturl'];

        $gallery_description = @json_decode($data['gallery_description'], true);
        $data['gallery_description'] = $gallery_description ? $gallery_description : $data['gallery_description'];

        if($CMS->vars['translations'])
        {
            //Đa ngôn ngữ
            if(!is_array($data['gallery_shorturl']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $shorturl = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    $shorturl[$langCode] = $data['gallery_shorturl'];
                }

                $data['gallery_shorturl'] = $shorturl;
            }

            if(!is_array($data['gallery_name']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $name = [];
                $name_bk = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                	$name[$langCode] = $data['gallery_name'];
	            	// Check permission to read Info
					if ( $CMS->permit["gallery_read"] == true )
					{
						$name_bk[$langCode] = "<a href='{$CMS->vars['root_domain']}/?site=gallery&act=show&id={$data['gallery_id']}'>{$data['gallery_name']}</a>";
					}

                }

                $data['gallery_name_bk'] = $name_bk;
                $data['gallery_name'] = $name;
            }else
            {
            	$data['gallery_name_bk'] = $data['gallery_name'];

            	$name_bk = [];
            	// Check permission to read Info
				if ( $CMS->permit["gallery_read"] == true )
				{
					foreach ($data['gallery_name_bk'] as $langCode => $gal_name)
	                {
						$name_bk[$langCode] = "<a href='{$CMS->vars['root_domain']}/?site=gallery&act=show&id={$data['gallery_id']}'>{$gal_name}</a>";
					}
					$data['gallery_name_bk'] = $name_bk;
				}

            }

            if(!is_array($data['gallery_description']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $gallery_description = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    $gallery_description[$langCode] = $data['gallery_description'];
                }

                $data['gallery_description'] = $gallery_description;
            }

        }
        else
        {
        	if(is_array($data['gallery_shorturl']))
            {
            	$data['gallery_shorturl'] = $data['gallery_shorturl'][$CMS->vars['default_language']];
        	}

        	if(is_array($data['gallery_description']))
            {
            	$data['gallery_description'] = $data['gallery_description'][$CMS->vars['default_language']];
        	}

            if(is_array($data['gallery_name']))
            {
                //Neu la dang mang thi chuyen ve dang chuoi binh thuong
                $data['gallery_name_bk'] = $data['gallery_name'] = $data['gallery_name'][$CMS->vars['default_language']];

                // Check permission to read Info
				if ( $type == 1 )
				{
					$data['gallery_name_bk'] = "<a href='{$CMS->vars['root_domain']}/{$data['gallery_shorturl']}'>{$data['gallery_name']}</a>";
				}
				else if ( $CMS->permit["gallery_read"] == true )
				{
					$data['gallery_name_bk'] = "<a class='link' href='{$CMS->vars['root_domain']}/?site=gallery&act=show&id={$data['gallery_id']}'>{$data['gallery_name']}</a>";
				}

            }else
            {
            	$data['gallery_name_bk'] = $data['gallery_name'];
            	if ( $CMS->permit["gallery_read"] == true )
				{
					$data['gallery_name_bk'] = "<a class='link' href='{$CMS->vars['root_domain']}/?site=gallery&act=show&id={$data['gallery_id']}'>{$data['gallery_name']}</a>";
				}
            }


        }

		// Check permission to read Info
		// if ( $type == 1 )
		// {
		// 	$data['gallery_name_bk'] = "<a href='{$CMS->vars['root_domain']}/{$data['gallery_shorturl']}'>{$data['gallery_name']}</a>";
		// }
		// else if ( $CMS->permit["gallery_read"] == true )
		// {
		// 	$data['gallery_name_bk'] = "<a href='{$CMS->vars['root_domain']}/?site=gallery&act=show&id={$data['gallery_id']}'>{$data['gallery_name']}</a>";
		// }else
		// {
		// 	$data['gallery_name_bk'] = $data['gallery_name'];
		// }

		// get html list image
// 		$list_image = $this->getListImage($data['gallery_id']);
// 		foreach ($list_image as $key => $value) 
// 		{
// 			$link = "{$CMS->vars['upload_dir']}/gallery/{$value['gali_image']}";
// 			$data['li_html'] .=<<<EOF
// 				<li>
// 		          <image style="max-width: 120px; max-height: 120px;" src="{$link}" class='image_avatar'/>
// 		          <span onclick="delFile(this,1);" class="del_image" id="{$value['gali_id']}"><i class="fa fa-times" aria-hidden="true"></i></span>
// 		        </li>
// EOF;

// 		}

// 		$data['cat_name'] = $CMS->config_gallery->get_info($data['cat_id'], "cat_name");
// 		// Description
// 		$data['gallery_description'] = $CMS->class->editor->substr(strip_tags($data['gallery_description']), 0, 500);
// 		// Replace the IS Hot
// 		$data['gallery_is_hot_bk'] = $CMS->lang["gallery_is_hot_{$data['gallery_is_hot']}"];	

// 		$data['gallery_display_bk'] = $CMS->lang["display_{$data['gallery_display']}"];		


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
		$data['gallery_time'] = $CMS->class->date->date_format( $data['gallery_time'], 0 );

		// Replace the Status
		$data['gallery_display'] = $CMS->lang["display_{$data['gallery_display']}"];
		// Replace the IS Active Post
		$data['gallery_active'] = $CMS->lang["gallery_active_color_{$data['gallery_active']}"];

		// Category
		$cat = $CMS->config_gallery->get_info($data['cat_id']);
		$data['cat_id'] = $cat['cat_name'];

		return $data;
	}

	//===========================================================================
	//  INFO
	//===========================================================================

	public function get_info( $record_id = 0, $field_name = "")
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "gallery" )
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

		// Check type
		//$sql_add .= ($this->gallery_project ? " AND gallery_project='{$this->gallery_project}' " : "");

        $sql = "SELECT * FROM ".root_table."gallery WHERE gallery_id='{$record_id}' OR gallery_name='{$record_id}' OR gallery_shorturl='{$record_id}' AND gallery_deleted=0 {$sql_add} ORDER BY gallery_id DESC LIMIT 1";

		$data = $DB->fetch_data($sql,$this->cache_prefix)[0];

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
			$sql = "SELECT * FROM ".root_table."gallery WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND gallery_deleted=0";
		}
		else
		{
			$sql = "SELECT * FROM ".root_table."gallery WHERE {$field}='{$value}' AND gallery_deleted=0";
		}

		$DB->query($sql);

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
		// print "<pre> dsfaa";
		// print_r($_FILES);
		// print_r($CMS->input);exit;

		$gallery_name = $CMS->input['gallery_name'];
		$gallery_description = $CMS->input['gallery_description'];
		$check = true;
		if($CMS->vars['translations'])
        {
        	foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
            	$gallery_shorturl[$langCode] = $CMS->class->seo->cleanurl($gallery_name[$langCode]);
            	$gallery_name[$langCode] = preg_replace( "/\r|\n/", "", $gallery_name[$langCode]);
            	$gallery_name[$langCode] = str_replace("'", "&#39;", $gallery_name[$langCode]);

            	$gallery_description[$langCode] = preg_replace( "/\r|\n/", "", $gallery_description[$langCode]);
            	$gallery_description[$langCode] = str_replace("'", "&#39;", $gallery_description[$langCode]);

            }

            if (empty($gallery_name[$CMS->vars['default_language']])) {
                $_SESSION['error_msg'] .= $CMS->lang['gallery_incomplete_name'].' ('.$CMS->vars['translations'][$CMS->vars['default_language']].')<br>';
            }

            $gallery_name = @json_encode($gallery_name, JSON_UNESCAPED_UNICODE);
            $gallery_shorturl = @json_encode($gallery_shorturl, JSON_UNESCAPED_UNICODE);
            $gallery_description = @json_encode($gallery_description, JSON_UNESCAPED_UNICODE);
        }
        else
        {
            $gallery_name = strip_tags($CMS->class->editor->input(urldecode($CMS->input['gallery_name']), 'text'));
			$gallery_shorturl = $CMS->class->seo->cleanurl($gallery_name);
			$gallery_description = strip_tags($CMS->class->editor->input(urldecode($CMS->input['gallery_description']), 'text'));

			$name_alert = $gallery_name;
        }

		// print $gallery_name;exit;
		$gallery_time = time();
		$gallery_display = 1;//intval($CMS->input["gallery_display"]);
		$gallery_is_hot = intval($CMS->input["gallery_is_hot"]);
		$gallery_shorturl = $CMS->class->seo->cleanurl($gallery_name);
		$cat_id = intval($CMS->input['cat_id']);
		$meta_title = trim($CMS->class->editor->input("meta_title"));
		$meta_description =trim( $CMS->class->editor->input("meta_description"));
		$meta_keywords = trim($CMS->class->editor->input("meta_keywords"));
		$gallery_image_alt = trim(urldecode($CMS->input['gallery_image_alt']));
        $images = $CMS->input['upload_type'] == 'file' ? $_FILES['list_image'] : $_POST['url_image'];
        $hair_color = trim(urldecode($CMS->input['hair_color']));
		$check_image = $CMS->input['upload_type'] == 'file' ? count($images['name']) : count($images);

		$gallery_sort_order = $CMS->input['gallery_sort_order'] * 1;

		// Check input
		if ( ! $gallery_name ) { $_SESSION['error_msg'] = "{$CMS->lang['gallery_incomplete_name']}"; return false; }
		if( ! $check_image) { $_SESSION['error_msg'] = $CMS->lang['gallery_not_found']; return false;}

		$count_image = 0;

		for($x=0;$x<=$check_image;$x++)
		{
		    if($CMS->input['upload_type'] == 'file')
            {
                $file_tmp = isset($images['tmp_name'][$x]) ? $images['tmp_name'][$x] : "";
                $file_name = isset($images['name'][$x]) ? $images['name'][$x] : "";
                $file_type = isset($images['type'][$x]) ? $images['type'][$x] : "";
                $file_size = isset($images['size'][$x]) ? $images['size'][$x] : "";
                $file_error = isset($images['error'][$x]) ? $images['error'][$x] : "";

                $file_ext = $CMS->class->attachment->get_ext( $file_name );

                // Check dung luong file upload
                $max = 18;
                $max_file_upload = 1024*1024*$max;
                if($file_size > $max_file_upload )
                {
                    $_SESSION['error_msg'] = $CMS->lang['msg_maxfile_upload'].$max."MB";
                    return false;
                }

                $file_name = str_replace( " ", "_", $file_name );
                $file_location = strtolower(time()."_".$file_name);
                $product_image = "";

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

                $gallery_image = $file_location;
            }
			else
            {
                $b64image = \lib\image::convertImageFromUrlToBase64($images[$x]);

                $file_name = $b64image['file_name'];
                $file_ext = $b64image['file_ext'];

                if ( $CMS->class->attachment->is_image($file_name, $file_ext) == false )
                {
                    $_SESSION['error_msg'] = $CMS->lang['invalid_upload_file'];
                    return false;
                }

                // create dir, thumb dir and get path
                $CMS->class->image->check_folder_img("gallery","",0);
                //upload and get file name
                $gallery_image = $CMS->class->image->uploadImgBase64($b64image['file_src'], "gallery","",0,$CMS->class->seo->cleanurl($gallery_name).'-');

                if(!$gallery_image)
                {
                    $_SESSION['error_msg'] = $CMS->lang['msg_error_upload'];
                    return false;
                }
            }
            if(ezy::$theme_key == "dsg")
			{
	            // Insert data
	            $count = $DB->query("INSERT INTO ".root_table."gallery (gallery_name, gallery_description, cat_id, gallery_display, gallery_is_hot, gallery_time, gallery_time_update,user_id, gallery_shorturl, meta_title, meta_description, meta_keywords, gallery_image, gallery_image_alt, hair_color, gallery_sort_order) VALUES ('{$gallery_name}', '{$gallery_description}', '{$cat_id}','{$gallery_display}', '{$gallery_is_hot}','{$gallery_time}','{$gallery_time}','{$member['user_id']}', '{$gallery_shorturl}', '{$meta_title}','{$meta_description}', '{$meta_keywords}', '{$gallery_image}', '{$gallery_image_alt}', '{$hair_color}', '{$gallery_sort_order}')");
	        }
	        else
	        {
	        	// Insert data
           		$count = $DB->query("INSERT INTO ".root_table."gallery (gallery_name, gallery_description, cat_id, gallery_display, gallery_is_hot, gallery_time, gallery_time_update,user_id, gallery_shorturl, meta_title, meta_description, meta_keywords, gallery_image, gallery_image_alt, gallery_sort_order) VALUES ('{$gallery_name}', '{$gallery_description}', '{$cat_id}','{$gallery_display}', '{$gallery_is_hot}','{$gallery_time}','{$gallery_time}','{$member['user_id']}', '{$gallery_shorturl}', '{$meta_title}','{$meta_description}', '{$meta_keywords}', '{$gallery_image}', '{$gallery_image_alt}', '{$gallery_sort_order}')");
	        }




            //Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

            if($count)
            {
                $gallery_id = $DB->last_insert_id();

                //Sync WHM data
		       if($CMS->vars['web_free']== 1 AND intval($CMS->vars['ibe_synced']) == 1)
		       {
					
					$gallery_image_link = !empty($gallery_image) && file_exists($CMS->vars['upload_dir'].'/gallery/'.$gallery_image) ? $CMS->vars['upload_url'].'/gallery/'.$gallery_image : "";
		    		$data_api['gallery_name']   		  = "{$gallery_name}";
		    		$data_api['gallery_description']      = "{$gallery_description}";
		    		$data_api['cat_id'] 		      	  = "{$cat_id}";
		    		$data_api['gallery_display']          = "{$gallery_display}";
		    		$data_api['gallery_is_hot']           = "{$gallery_is_hot}";
		    		$data_api['gallery_time']             = "{$gallery_time}";
		    		$data_api['gallery_time_update']      = "{$gallery_time_update}";
		    		$data_api['gallery_shorturl']         = "{$gallery_shorturl}";
		    		$data_api['gallery_image']            = "{$gallery_image}";
		    		$data_api['gallery_image_link']   	  = "{$gallery_image_link}";
		    		$data_api['site_id']   		          = "{$CMS->vars['site_id']}";
		    		$data_api['original_id']   	  		  = "{$gallery_id}";
		    		
		    		$CMS->api->whm->execute('gallery_add', $data_api); 
		    	}

                $_SESSION['highlight'][] = $gallery_id;
                // Create log
                $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['gallery_added']} <b>{$file_name}</b>")."<br />";
                $count_image ++;
            }
		}
		return $count_image > 0 ? true : false;
	}

	//===========================================================================
	//  EDIT
	//===========================================================================

	public function edit()
	{
		global $CMS, $DB, $member;

		// Get info
		$gallery_bk = $this->get_info();
		$CMS->class->logs->old_data = $gallery_bk;
		// print "<pre> dsfaa";
		// print_r($_FILES);
		// print_r($CMS->input);//exit;
		$gallery_name = $CMS->input['gallery_name'];
		$gallery_description = $CMS->input['gallery_description'];
		$check = true;
		if($CMS->vars['translations'])
        {
        	foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
            	$gallery_shorturl[$langCode] = $CMS->class->seo->cleanurl($gallery_name[$langCode]);
            	$gallery_name[$langCode] = preg_replace( "/\r|\n/", "", $gallery_name[$langCode]);
            	$gallery_name[$langCode] = str_replace("'", "&#39;", $gallery_name[$langCode]);

            	$gallery_description[$langCode] = preg_replace( "/\r|\n/", "", $gallery_description[$langCode]);
            	$gallery_description[$langCode] = str_replace("'", "&#39;", $gallery_description[$langCode]);

            }

            if (empty($gallery_name[$CMS->vars['default_language']])) {
                $_SESSION['error_msg'] .= $CMS->lang['gallery_incomplete_name'].' ('.$CMS->vars['translations'][$CMS->vars['default_language']].')<br>';
            }

            $gallery_name = @json_encode($gallery_name, JSON_UNESCAPED_UNICODE);
            $gallery_shorturl = @json_encode($gallery_shorturl, JSON_UNESCAPED_UNICODE);
            $gallery_description = @json_encode($gallery_description, JSON_UNESCAPED_UNICODE);
        }
        else
        {
            $gallery_name = strip_tags($CMS->class->editor->input(urldecode($CMS->input['gallery_name']), 'text'));
			$gallery_shorturl = $CMS->class->seo->cleanurl($gallery_name);
			$gallery_description = strip_tags($CMS->class->editor->input(urldecode($CMS->input['gallery_description']), 'text'));

			$name_alert = $gallery_name;
        }
		// print $gallery_name;exit;
		$gallery_time = time();
		$gallery_display = intval($CMS->input["gallery_display"]);
		$gallery_is_hot = intval($CMS->input["gallery_is_hot"]);
		$gallery_shorturl = $CMS->class->seo->cleanurl($gallery_name);
		$cat_id = intval($CMS->input['cat_id']);
		$meta_title = trim($CMS->class->editor->input("meta_title"));
		$meta_description =trim( $CMS->class->editor->input("meta_description"));
		$meta_keywords = trim($CMS->class->editor->input("meta_keywords"));
        $gallery_image_alt = trim(urldecode($CMS->input['gallery_image_alt']));
        $hair_color = trim(urldecode($CMS->input['hair_color']));

        $gallery_sort_order = $CMS->input['gallery_sort_order'] * 1;

		// Check input
		if ( ! $gallery_name ) { $CMS->errormsg = "{$CMS->lang['gallery_incomplete_name']}"; return false; }
// print "asdfasda";exit;

        $is_new_file = 0;

        if($CMS->input['upload_type'] == 'file')
        {
            $file_tmp = isset($_FILES['list_image']['tmp_name']) ? $_FILES['list_image']['tmp_name'] : "";
            $file_name = isset($_FILES['list_image']['name']) ? $_FILES['list_image']['name'] : "";
            $file_type = isset($_FILES['list_image']['type']) ? $_FILES['list_image']['type'] : "";
            $file_size = isset($_FILES['list_image']['size']) ? $_FILES['list_image']['size'] : "";
            $file_error = isset($_FILES['list_image']['error']) ? $_FILES['list_image']['error'] : "";

            $file_ext = $CMS->class->attachment->get_ext( $file_name );

            // Check dung luong file upload
            $max = 10;
            $max_file_upload = 1024*1024*$max;

            if($file_size > $max_file_upload )
            {
                $_SESSION['error_msg'] = $CMS->lang['msg_maxfile_upload'].$max."MB";
                return false;
            }

            $number = rand(1,1000);
            $file_name = str_replace( " ", "_", $file_name );
            $file_location = strtolower(time()."_".$number.$file_name);

            $product_image = "";

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

                $gallery_image = $file_location;
                $oldPath = "{$CMS->vars['upload_dir']}/gallery/{$gallery_bk['gallery_image']}";
                @unlink($oldPath);

                $is_new_file = 1;
            }
        }
        else
        {
            if(!empty($CMS->input['url_image']))
            {
                $b64image = \lib\image::convertImageFromUrlToBase64(urldecode($CMS->input['url_image']));

                $file_name = $b64image['file_name'];
                $file_ext = $b64image['file_ext'];

                if ( $CMS->class->attachment->is_image($file_name, $file_ext) == false )
                {
                    $_SESSION['error_msg'] = $CMS->lang['invalid_upload_file'];
                    return false;
                }

                // create dir, thumb dir and get path
                $CMS->class->image->check_folder_img("gallery","",0);
                //upload and get file name
                $gallery_image = $CMS->class->image->uploadImgBase64($b64image['file_src'], "gallery","",0,$CMS->class->seo->cleanurl($gallery_name).'-');

                if(!$gallery_image)
                {
                    $_SESSION['error_msg'] = $CMS->lang['msg_error_upload'];
                    return false;
                }
                else
                {
                    $oldPath = "{$CMS->vars['upload_dir']}/gallery/{$gallery_bk['gallery_image']}";
                    @unlink($oldPath);

                    $is_new_file = 1;
                }
            }
        }


		if(!$is_new_file)
        {
            $gallery_image = $gallery_bk['gallery_image'];
        }
        $sql_hair_color = "";
        if(ezy::$theme_key == "dsg")
		{
			$sql_hair_color = ", hair_color = '{$hair_color}' ";
		}
		// update data
		$count = $DB->query("UPDATE ".root_table."gallery SET gallery_name = '{$gallery_name}', gallery_description = '{$gallery_description}', cat_id = '{$cat_id}', gallery_display = '{$gallery_display}', gallery_is_hot = '{$gallery_is_hot}', gallery_time_update = '{$gallery_time}',user_id = '{$member['user_id']}', gallery_shorturl = '{$gallery_shorturl}', meta_title = '{$meta_title}', meta_description = '{$meta_description}', meta_keywords = '{$meta_keywords}', gallery_image = '{$gallery_image}', gallery_image_alt='{$gallery_image_alt}', gallery_sort_order='{$gallery_sort_order}' {$sql_hair_color} WHERE gallery_id = '{$gallery_bk['gallery_id']}'");

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		$CMS->class->logs->key = "gallery_{$gallery_bk['gallery_id']}";
		// Get info
		$gallery = $this->get_info($gallery_name);

		   //Sync WHM data
	       if($CMS->vars['web_free']== 1 AND intval($CMS->vars['ibe_synced']) == 1)
	       {
				if($is_new_file)
        		{
					$gallery_image_link = !empty($gallery['gallery_image']) && file_exists($CMS->vars['upload_dir'].'/gallery/'.$gallery['gallery_image']) ? $CMS->vars['upload_url'].'/gallery/'.$gallery['gallery_image'] : "";
				}
	    		$data_api['gallery_name']   		  = "{$gallery['gallery_name']}";
	    		$data_api['gallery_description']      = "{$gallery['gallery_description']}";
	    		$data_api['cat_id'] 		      	  = "{$gallery['cat_id']}";
	    		$data_api['gallery_display']          = "{$gallery['gallery_display']}";
	    		$data_api['gallery_is_hot']           = "{$gallery['gallery_is_hot']}";
	    		$data_api['gallery_time']             = "{$gallery['gallery_time']}";
	    		$data_api['gallery_time_update']      = "{$gallery['gallery_time_update']}";
	    		$data_api['gallery_shorturl']         = "{$gallery['gallery_shorturl']}";
	    		$data_api['gallery_image']            = "{$gallery['gallery_image']}";
	    		$data_api['gallery_image_link']   	  = "{$gallery_image_link}";
	    		$data_api['site_id']   		          = "{$CMS->vars['site_id']}";
	    		$data_api['original_id']   	  		  = "{$gallery['gallery_id']}";
	    		
	    		$CMS->api->whm->execute('gallery_edit', $data_api); 
	    	}

		// Step 2: Save detail logs
		$CMS->class->logs->save_detail("gallery",$gallery['gallery_id'],$gallery);

		return $gallery;

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
		// $this->deleteImageInGallery($data['gallery_id']);
		@unlink("{$CMS->vars['upload_url']}/gallery/{$data['gallery_image']}");
		$DB->query("UPDATE ".root_table."gallery SET gallery_deleted = 1 WHERE gallery_id={$data['gallery_id']}");

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

           //Sync WHM data
	       if($CMS->vars['web_free']== 1 AND intval($CMS->vars['ibe_synced']) == 1)
	       { 
	    		$data_api['site_id']   		      = "{$CMS->vars['site_id']}";
	    		$data_api['original_id']   	      = "{$data['gallery_id']}";
	    		
	    		$CMS->api->whm->execute('gallery_delete', $data_api); 
	    	}



		// Update Cat count
		// $cat = $CMS->config_gallery->get_info($data['cat_id']);
		// $CMS->config_gallery->update_count($cat['cat_id'],"dev");
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['gallery_deleted']} <b>{$data['gallery_name']}</b>")."<br />";

		// Redirect
		// $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=gallery&page={$CMS->input['page']}");

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
				$CMS->news->delete_attach("gallery",$data['gallery_id']);
				// Xoa hinh cuar bai viet
				if($data['image_location'] != "")
				{
						@unlink("{$CMS->vars['upload_dir']}/gallery/{$data['image_location']}");
						//@unlink("{$CMS->vars['upload_dir']}/gallery/thumbresize/{$data['image_location']}");
				}
				$DB->query("DELETE FROM ".root_table."gallery WHERE gallery_id={$data['gallery_id']}");

				// Update Cat count
				$cat = $CMS->config_gallery->get_info($data['cat_id']);
				$CMS->config_gallery->update_count($cat['cat_id'],"dev");

	 

		        //Sync WHM data
			    if($CMS->vars['web_free']== 1 AND intval($CMS->vars['ibe_synced']) == 1)
			    { 
			    		$data_api['site_domain']   		      = "{$CMS->vars['site_id']}";
			    		$data_api['original_id']   	  		  = "{$data['gallery_id']}";
			    		
			    		$CMS->api->whm->execute('gallery_delete', $data_api); 
			    }
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['gallery_deleted']} <b>{$data['gallery_name']}</b>")."<br />";

				$deleted = 1;
			}
		}

		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['gallery_delete_failed']}";
		}

        //Clear cache
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
		$CMS->class->search->table_name = "gallery";
		$CMS->class->search->ignored_fields = array();
		$CMS->class->search->changed_fields = array();
		$CMS->class->search->fields_type = array("gallery_time" => "time");
		$CMS->class->search->search_type = 0;
		$CMS->class->search->fields_prefix = "";
		$CMS->class->search->fields_replace = array("cat_id" => "cat_id");

		// Output
		$data = $CMS->class->search->get_info();

		// Update SQL Query
		$this->sql_add .= $data;

		// Get List
		$this->listing();
		if($CMS->input['is_exel']=="gallery")
		{
			$CMS->report->report['format'] = "gallery";
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
		$gallery_bk = $this->get_info();

		// User input
		$gallery_active = intval($CMS->input['method']);
		if($gallery_active == 2)
		{
			$DB->query("UPDATE ".root_table."gallery SET gallery_active ='{$gallery_active}', gallery_royalty =0 WHERE gallery_id='{$gallery_bk['gallery_id']}'");
		}
		else
		{
			$DB->query("UPDATE ".root_table."gallery SET gallery_active ='{$gallery_active}' WHERE gallery_id='{$gallery_bk['gallery_id']}'");
		}

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		if($gallery_active != $gallery_bk['gallery_active'])
		{
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['convert_status']} {$gallery_bk['gallery_name']}<b> {$CMS->lang["gallery_active_{$gallery_bk['gallery_active']}"]} -> {$CMS->lang["gallery_active_{$gallery_active}"]}</b>")."<br />";
		}
		$gallery = $this->get_info();

		return $gallery;
	}

	//===========================================================================
	//  update_royalty  POST
	//===========================================================================

	public function update_royalty()
	{
		global $CMS, $DB, $member;

		// Get info
		$gallery_bk = $this->get_info();

		// User input
		$gallery_royalty = $CMS->input['gallery_royalty'];
		if($gallery_bk['gallery_active'] == 0 OR $gallery_bk['gallery_active'] == 2)
		{
			$_SESSION["msg"] .= $CMS->lang['not_update_royalty'];
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=gallery&act=show&id={$gallery_bk['gallery_id']}");
			return false;
		}
		if( is_numeric($gallery_royalty) == FALSE)
		{
			$_SESSION["msg"] .= $CMS->lang['not_numeric_update_royalty'];
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=gallery&act=show&id={$gallery_bk['gallery_id']}");
			return false;
		}

		// Step 1: Save detail logs
		$CMS->class->logs->old_data = $gallery_bk;
		$CMS->class->logs->key = "gallery_{$gallery_bk['gallery_id']}";

		$DB->query("UPDATE ".root_table."gallery SET gallery_royalty ='{$gallery_royalty}' WHERE gallery_id='{$gallery_bk['gallery_id']}'");

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);


		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['update_royalty']} {$gallery_bk['gallery_name']}<b> : {$CMS->class->input->currency($gallery_bk['gallery_royalty'])} => {$CMS->class->input->currency($gallery_royalty)}</b>")."<br />";
		$gallery = $this->get_info();

		// Step 2: Save detail logs
		$CMS->class->logs->key = "gallery_{$gallery['gallery_id']}";
		$CMS->class->logs->save_detail("gallery",$gallery['gallery_id'],$gallery);

		return $gallery;
	}




	//===========================================================================
	//  UPDATE VIEWS
	//===========================================================================

	public function update_views( $data )
	{
		global $CMS, $DB;

		$break = 5*60;
		if ( ( $_SESSION['gallery_view_time'] + $break ) <= time() )
		{
			unset($_SESSION['gallery_view_time']);
		}
		if ( !isset($_SESSION['gallery_view_time']) )
		{
			$DB->query("UPDATE ".root_table."gallery SET gallery_views=gallery_views+1 WHERE gallery_id='{$data['gallery_id']}'");

            //Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$_SESSION['gallery_view_time'] = time();
		}
	}
	//===========================================================================
	//  LOAD gallery
	//===========================================================================

	public function load_gallery( $category = 0 )
	{
		global $CMS, $DB;

		$this->loadhtml();

		// User input
		$cat = $CMS->config_gallery->get_info($category,"",1);
		$cat = $CMS->config_gallery->convertvalue($cat);

		// Load gallery
		$CMS->class->page->type = 1;
		$this->per_page = 10;
		$this->sql_add .= $category > 0 ? " C.cat_id='{$category}' AND gallery_display=1  AND " : "";
		$this->prefix_html = "{$cat['cat_shorturl']}_";
		$this->suffix_html = ".html";
		$this->listing();

		// Print data
		$output = "";

		if ( $DB->num_rows( $this->sql_query ) > 0 )
		{
			while ( $data = $DB->fetch_array( $this->sql_query ) )
			{

				$data = $this->convertvalue( $data, 1 );

				$output .= $this->html->gallery_record( $data );
			}
		}
		else
		{
			$output .= $CMS->lang['gallery_no_data'];
		}

		$CMS->gui->html['gallery_list'] = $output;
	}


	//===========================================================================
	//  LOAD OTHER gallery
	//===========================================================================

	public function load_cate_gallery($selected = 0)
	{
		global $CMS, $DB;

		$output = "";

		$sql = "SELECT * FROM ".root_table."gallery_category WHERE cat_deleted = 0 ORDER BY cat_id DESC ";

		$results = $DB->fetch_data($sql, $CMS->config_gallery->cache_prefix);

		if($results)
        {
            foreach ( $results as $data )
            {

            	$data_hair_cate = "";
            	if(isset($data['cat_cate']))
            	{
            		if($data['cat_cate'] != ""){
            			$data_hair_cate = "_".$CMS->lang["hair_category_{$data['cat_cate']}"];	
            		}else{
            			$data_hair_cate = "";	
            		}
            	}

            	$selected_attr = $data['cat_id'] == $selected ? "selected" : '';


            	// Convert language
            	$name = @json_decode($data['cat_name'], true);
        		$data['cat_name'] = $name ? $name : $data['cat_name'];
        		$data['cat_name'] = is_array($data['cat_name']) == true ? $data['cat_name'][$CMS->vars['default_language']] : $data['cat_name'];
        		
                $output .= "<option {$selected_attr} value=\"{$data['cat_id']}\">{$data['cat_name']}{$data_hair_cate}</option>";
            }
        }

		return $output;
	}

	//===========================================================================
	//  LOAD  gallery Anh
	//===========================================================================

	public function load_gallery_anh( $module_id= "", $gallery_note)
	{
		global $CMS, $DB;

		$output = "";
		$array = unserialize($gallery_note);

		// moi hon
        $sql = "SELECT * FROM ".root_table."attachment WHERE module_name='gallery' AND module_id='{$module_id}' ORDER BY attach_time ASC";

		$sql = $DB->query($sql);
		if($DB->num_rows($sql) >0 )
		{
			$i = 0 ;
			while ( $data = $DB->fetch_array( $sql ) )
			{
				$output .="<div class='style_gallery'>";
				$output .= "<img src=\"{$CMS->vars['upload_url']}/attach/{$data['attach_location']}\" style='margin-bottom:4px' /> ";
				$output .= "<p claass='gallery_note'>{$array[$i]}</p><br />";
				$output .="</div>";
				$i++;
			}
		}

		return $output;
	}


	//===========================================================================
	//  LOAD  danh sach hinh thuoc gallery
	//===========================================================================

	public function list_anh( $input= "", $type = 0)
	{
		global $CMS, $DB;

		$output = "";
		$array = unserialize($input['gallery_note']);

		// Load danh sach anh cua gallery
        $sql = "SELECT * FROM ".root_table."attachment WHERE module_name='gallery' AND module_id='{$input['gallery_id']}' ORDER BY attach_time ASC";

		$sql = $DB->query($sql);
		if($DB->num_rows($sql) >0 )
		{	$i = 0;
			while ( $data = $DB->fetch_array( $sql ) )
			{
				if($type == 1)
				{
					$array[$i] = $CMS->class->editor->substr($array[$i],0,250);
					$output .= "<li><a href=\"{$CMS->vars['upload_url']}/attach/{$data['attach_location']}\" data-rel=\"prettyPhoto[sliderGallery]\" alt=\"{$input['gallery_name']}\"><img src=\"{$CMS->vars['upload_url']}/attach/{$data['attach_location']}\" alt=\"{$input['gallery_name']}\" /></a><p>{$array[$i]}</p></li> ";

				}
				elseif($type == 2)
				{
						$output .= "<li><img src=\"{$CMS->vars['upload_url']}/attach/{$data['attach_location']}\" alt=\"Thumbnal\"  /></li> ";

				}
				else
				{
					$output .= "<li><a href=\"{$CMS->vars['root_domain']}/gallery/detail/{$input['gallery_shorturl']}/{$input['gallery_id']}.html\"><img src=\"{$CMS->vars['upload_url']}/attach/{$data['attach_location']}\"  /></a></li> ";
				}
				$i ++;
			}
		}

		return $output;
	}


	//===========================================================================
	//  Get tags
	//===========================================================================

	public function get_tags( $tags_id = "")
	{
		global $CMS, $DB;

		$tags_array = explode(",",$tags_id);
		//print_r ($tags_array);exit;
		for($i = 0; $i<= count($tags_array); $i++)
		{
			if($tags_array[$i] != "")
			{
				$output .=<<<EOF
				<li class="tag" onClick="return del_tags($i);"  id="tags_$i">$tags_array[$i]<input type="hidden" name="gallery_tags_id[]" value="{$tags_array[$i]}"/><a class="close" href="javascript: void();">close</a></li>
					
EOF;
			}
		}
		return $output;
	}


	//===========================================================================
	// Loaf gallery theo danh muc (Ajax phan trang)
	//===========================================================================

	public function cate_gallery( $input = "" )
	{
		global $CMS, $DB;

		$output = "";

		list($CMS->show_page_gallery, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."gallery WHERE cat_id = '{$input['cat_id']}' AND gallery_active = 1 AND gallery_display = 1 AND gallery_deleted = 0 ORDER BY gallery_id DESC ", $input['cat_sql'], "gallery/danh-muc/{$input['cat_shorturl']}/{$input['cat_id']}/", ".html");


		if($DB->num_rows($this->sql_query) > 0 )
		{
			$i = 0;
			while ( $data = $DB->fetch_array( $this->sql_query ) )
			{
				$data = $this->convertvalue($data);

					$data['gallery_description'] = $CMS->class->editor->substr($data['gallery_description'],0,135);
					if($i% 2 == 0 ){ $class = "no-margin-left";$xen = "";}else{$class=" "; $xen = "<div class=\"clearfix ie-sep\"></div>";}
					$output.=<<<EOF
			  		 <div class="span6 post {$class}">
					  <figure class="flexslider loading">
					  		 <ul class="slides">
			
EOF;
						$output .= "<li><a href=\"{$CMS->vars['root_domain']}/gallery/detail/{$data['gallery_shorturl']}/{$data['gallery_id']}.html\" ><img src=\"{$data['gallery_thumb']}\" alt=\"{$data['gallery_name']}\" title=\"{$data['gallery_name']}\"/></a></li>";
						$output .= $this->list_anh($data);
						$output .=<<<EOF
						 		</ul>
						  </figure>
						  <div class="text">
						   <h2><a href="{$CMS->vars['root_domain']}/gallery/detail/{$data['gallery_shorturl']}/{$data['gallery_id']}.html" alt="{$result['gallery_name']}">{$data['gallery_name']}</a></h2>
						   <p>{$data['gallery_description']}</p>
						      <div class="meta">{$data['gallery_time']}&nbsp;&nbsp;|&nbsp;&nbsp;{$data['gallery_views']} views &nbsp;|&nbsp;&nbsp;{$CMS->comment->count_comment($data['gallery_id'],"post_cm_gallery")} comments</div>
						  </div>
						 </div>
						 {$xen}
EOF;

				$i++;

			}
		}
		$output .="<div class=\"clearfix ie-sep\"></div> <!-- Clearfix -->";

		return $output;
	}



	//===========================================================================
	//  Load gallery mi nhat  (Top)
	//===========================================================================

	public function slide_show($data = "")
	{
		global $CMS, $DB, $member;
			$output = "";

			$array_1 = $this->list_anh($data,1);
			$array_2 = $this->list_anh($data,2);
			$output .= $this->html->skin_slide_show($array_1, $array_2);

		return $output;
	}

    function getListImage($gallery_id = 0, $type = 0)
    {
        global $CMS, $DB;

        $data = array();
        $_SESSION['highlight'] = isset($_SESSION['highlight']) ? $_SESSION['highlight'] : [];
        // if($gallery_id)
        // {
        $cus_id = intval($CMS->input['cus_id']);
        
        $gallery_sort_type = !empty($CMS->vars['gallery_sort_type']) ? 'DESC' : 'ASC';

        $order_by = !empty($CMS->input['sort']) ? "gallery_sort_order {$gallery_sort_type}," : "";

        $sql_add = "";

        $CMS->input['cat_id'] = $CMS->input['cat_id']*1;

        $sql_add .= !empty($CMS->input['cat_id']) ? "G.cat_id = {$CMS->input['cat_id']} AND " : "";
       
        $sql = "SELECT cat_name, G.* FROM " . root_table . "gallery G LEFT JOIN ".root_table."gallery_category GC on G.cat_id=GC.cat_id WHERE {$sql_add} gallery_deleted = 0 AND cat_deleted = 0 ORDER BY {$order_by} gallery_time DESC";

        list($CMS->show_page, $results) = $DB->fetch_listing($sql, 30, "", "", $CMS->input['page'],"gallery");

        $output = "";

        if ($results) {
            foreach ($results as $result) {
                // $data[] = $result;
                // if($type)
                // {
                $result = $this->convertvalue($result);
                if (is_array($result['gallery_shorturl'])) {
                    $result['gallery_shorturl'] = $result['gallery_shorturl'][$CMS->vars['default_language']];
                }

                if (is_array($result['gallery_description'])) {
                    $result['gallery_description'] = $result['gallery_description'][$CMS->vars['default_language']];
                }

                if (is_array($result['gallery_name'])) {
                    //Neu la dang mang thi chuyen ve dang chuoi binh thuong
                    $result['gallery_name_bk'] = $result['gallery_name'] = $result['gallery_name'][$CMS->vars['default_language']];
                } else {
                    $result['gallery_name_bk'] = $result['gallery_name'];
                }
                $time_due = dashboard::convertTimedue($result['gallery_time']);
                $link_image = "{$CMS->vars['upload_url']}/gallery/{$result['gallery_image']}";
                $btn_control = "";
                if ($CMS->permit['gallery_delete']) {
                    $btn_control .= <<<EOF
						      <button type="button" class="btn" onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=gallery&act=delete&id={$result['gallery_id']}&cus_id={$cus_id}');"><i class="font-icon font-icon-trash"></i></button>
EOF;
                }
                $class_hl = in_array($result['gallery_id'], $_SESSION['highlight']) ? "highlight_grid" : "";
                // Convert language
            	$name = @json_decode($result['cat_name'], true);
        		$result['cat_name'] = $name ? $name : $result['cat_name'];
        		$result['cat_name'] = is_array($result['cat_name']) == true ? $result['cat_name'][$CMS->vars['default_language']] : $result['cat_name'];

                $output .= <<<EOF
						<div class="gallery-col">
			              <article class="gallery-item {$class_hl}" style="height: 158px;">
			                <img class="gallery-picture" src="{$link_image}" alt="" height="158">
			                <div class="checkbox check_multi">
					          <label>
					            <input type="checkbox" class="check_multi_item" name="check_del_multi[]" value="{$result['gallery_id']}">
					            <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					          </label>
					        </div>
			                <div class="gallery-hover-layout">
			                  <div class="gallery-hover-layout-in">
			                    <p class="gallery-item-title">{$result['gallery_name']}</p>
			                    <p>{$result['gallery_description']}</p>
			                    <div class="btn-group">
			                      <button type="button" href="{$link_image}" class="btn view_detail">
			                        <i class="font-icon font-icon-eye"></i>
			                      </button>
			                      <button type="button" class="btn btn_edit_gallery" id="{$result['gallery_id']}">
			                        <i class="font-icon font-icon-pencil" aria-hidden="true"></i>
			                      </button>
			                      {$btn_control}
			                    </div>
			                    <p>{$time_due}</p>
			                  </div>
			                </div>
			                <div class="sort-order row">
			                    <div class="col-xs-5 col-md-5">
                                  <input id="sort_input_{$result['gallery_id']}" type="number" min="0" class="form-control input-sm" aria-describedby="basic-addon1" value="{$result['gallery_sort_order']}" onchange="galleryQuickUpdateSortOrder('{$result['gallery_id']}')">
                                </div>
                            </div>
                            <a class="label label-warning g-cat" href="?site=config_gallery&act=show&id={$result['cat_id']}">{$result['cat_name']}</a>
			              </article>
			            </div><!--.gallery-col-->
EOF;
                // }// End if
            }
        }
        // }

        // if($type)
        // {
        unset($_SESSION['highlight']);
        return $output;
        // }else
        // {
        // 	return $data;
        // }

    }

	function delImg($gali_id = 0)
	{
		global $CMS, $DB;

		$sql = $DB->query("SELECT * FROM ".root_table."gallery_item WHERE gali_id = '{$gali_id}'");

		if($DB->num_rows($sql) > 0)
		{
			$data = $DB->fetch_array($sql);
			@unlink("{$CMS->vars['upload_dir']}/gallery/{$data['gali_image']}");
			$DB->query("UPDATE ".root_table."gallery_item SET gali_deleted = 1 WHERE gali_id = '{$gali_id}'");

			if($CMS->input['no_ajax'])
			{
				$_SESSION['msg'] = "Delete image successful";
			}
			return true;
		}else
		{
			return false;
		}

	}

	function deleteImageInGallery($galley_id=0)
	{
		global $CMS, $DB;

		$sql = $DB->query("SELECT * FROM ".root_table."gallery_item WHERE gallery_id = '{$galley_id}'");

		if($DB->num_rows($sql) > 0)
		{
			while ($data = $DB->fetch_array($sql))
			{
				@unlink("{$CMS->vars['upload_dir']}/gallery/{$data['gali_image']}");
				$DB->query("UPDATE ".root_table."gallery_item SET gali_deleted = 1 WHERE gali_id = '{$data['gali_id']}'");
			}

		}
	}

	function quickUpdateSortOrder($gallery_id=0, $gallery_sort_order=0)
    {
	    global $CMS, $DB;

        $gallery_id *= 1;
        $gallery_sort_order *= 1;

	    $data = ['gallery_id' => $gallery_id,'gallery_sort_order' => $gallery_sort_order];

	    $result = [];

        $CMS->class->logs->key = "gallery_{$gallery_id}";

	    if($DB->update("gallery", $data, 'gallery_id')) {
	        $result = [
	            'status' => 'ok',
                'msg' => $CMS->class->logs->insert("Updated sort order success !")
            ];
        } else {
            $result = [
                'status' => 'fail',
                'msg' => $CMS->class->logs->insert("Updated sort order failed !")
            ];
        }

        return $result;
    }

    /**
     * @param $gallery_id array|integer
     * @param $cat_id integer
     * @return bool
     */
    function change_category($gallery_id, $cat_id)
    {
        global $DB, $CMS;

        if(!is_array($gallery_id))
        {
            $gallery_id *= 1;
            if(!$gallery_id) return false;

            $gallery_id[0] = $gallery_id;
        }

        if(empty($gallery_id)) return false;

        $gallery_id = implode(',',$gallery_id);

        $sql = "UPDATE ".root_table."gallery SET cat_id={$cat_id} WHERE gallery_id IN ({$gallery_id})";
        //Clear cache

        $CMS->class->cache->mdelete("gallery");

        return $DB->query($sql);
    }
}

?>