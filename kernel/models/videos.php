<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->videos = new class_videos;

class class_videos {

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
	 * @param @sql_query
	 *		The SQL Query for listing Data
	 */
	 
	public $sql_query_bk = "";

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
	
	public $total = 0;
	
	/**
	 * @param $total
	 *		HTML total
	 */
	 
	public $action_control = "";
	
	/**
	 * @param $action_control
	 *		HTML action control
	 */
	
	public $data_array = array();
	
	
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

	public $prefix_html = "";
	public $suffix_html = "";
	
	/**
	 * @param $videos_project
	 *		Use for multiple projects
	 */

	public $videos_project = "";

    /**
     * @var string $show_page
     */
	public $show_page = "";

    /**
     * @var string $cache_prefix
     */
	public $cache_prefix = 'videos';
	
	

	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	
	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			// if ( $CMS->vars['is_admin_module'] )
			// {
			// 	$this->html = $CMS->class->template->load_simple(\core\ezy::$app_dir."/modules/videos/templates/skin_videos.php");
			// }
			// else
			// {
				$this->html = $CMS->class->template->load_template("skin_videos");
			// }	

		}
	}
	
	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("videos_time,videos_name,videos_id,videos_display,cat_id");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "videos_time";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "desc";

		$sql = "SELECT N.* FROM ".root_table."videos AS N LEFT JOIN ".root_table."videos_category AS C ON C.cat_id=N.cat_id WHERE N.videos_deleted=0 AND {$this->sql_add} 1=1 ORDER BY N.{$default_field} {$default_order}";

		list($this->show_page, $data) = $DB->fetch_listing($sql, $this->per_page, $this->prefix_html, $this->suffix_html, $CMS->input['page'], $this->cache_prefix.'.'.$CMS->config_parent_videos->cache_prefix);

        return $data;
	}

	
	public function listing_report()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("videos_time,videos_name,videos_id,videos_display,cat_id");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "videos_time";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "desc";

		// Create SQL Query for listing Data
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."videos WHERE videos_deleted=0 AND {$this->sql_add} 1=1 ORDER BY {$default_field} {$default_order}", $this->per_page, $this->prefix_html, $this->suffix_html);
		$this->total =  $DB->num_rows($DB->query("SELECT * FROM ".root_table."videos WHERE videos_deleted=0 AND {$this->sql_add} 1=1"));
		//echo "SELECT * FROM ".root_table."videos WHERE videos_deleted=0 AND {$this->sql_add}";exit;
	}
	
	public function html($data)
	{
		global $CMS, $DB, $member;

		$this->loadhtml();

		// Display Header
		$output .= $this->html->videos_header();
		
		if ( $data )
		{
			foreach( $data as $result )
			{
				// Convert info
				$result = $CMS->videos->convertvalue($result);
				
				// Display Middle
				$output .= $this->html->videos_middle($result);
			}
		}
		else
		{
			// Display No data
			$output .= $this->html->videos_none();

			// No data 0
			$CMS->is_error = 1;
		}
		
		// Display
		$output .= $this->html->videos_footer();
		
		// If the page is giving no data
		if ( $CMS->input['page'] > 1 && $CMS->is_error == 1)
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=videos");
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
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_videos_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_videos_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";

			// Check permission to Delete
			if ( $CMS->permit["videos_delete"] == true )
			{
				$data .= "<option value='delete_all'>{$CMS->lang['videos_action_delete']}</option>";
				$this->control = 1;
			}
			
			$data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

			$CMS->class->cache->save("user_{$member['user_id']}_videos_controller_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}
		
		if ( $CMS->vars['action_controller']  OR $CMS->permit["videos_search"] == 1 )
		{
			$this->action_control = $this->html->videos_control();
		}
		
		// Get category
		$CMS->config_videos->data();
	}

	//===========================================================================
	//  DATA
	//===========================================================================

	public function defaultvalue($data)
	{
		global $CMS;

		$data['videos_display'] = $data['videos_display'] ? $data['videos_display'] : 1;

		return $data;
	}

	public function convertvalue($data, $type = 0, $project = "")
	{
		global $CMS, $DB;

		$data['data_bk'] = $data;

		// Replace search content
		$data = $CMS->class->search->convertvalue($data);

		// Rewrite URL
		/*if ( $project == "template" ) $data['videos_shorturl'] = ($CMS->vars['is_rewrite'] == 1 && $data['videos_shorturl'])? "thong-bao/{$data['videos_shorturl']}.html": "?site=notice&view=detail&id={$data['videos_id']}";
		else $data['videos_shorturl'] = ($CMS->vars['is_rewrite'] == 1 && $data['videos_shorturl'])? "tin-tuc/{$data['videos_shorturl']}.html": "?site=videos&view=topic&id={$data['videos_id']}";;
		*/
		
		// Convert Register to GMT
		$data['videos_time_bkk'] = $data['videos_time'];
		$data['videos_time'] = $CMS->class->date->normal_time_news( $data['videos_time']);
		$data['videos_time_update'] = $data['videos_time_update'] ? $CMS->class->date->date_format( $data['videos_time_update'], 1 ) : "<i>N/A</i>";
		// $data['videos_name_bk'] = htmlspecialchars_decode($data['videos_name']);

		//print_r ($data['videos_name_bk']);exit;
		$name = @json_decode($data['videos_name'], true);
        $data['videos_name'] = $name ? $name : $data['videos_name'];

        $shorturl = @json_decode($data['videos_shorturl'], true);
        $data['videos_shorturl'] = $shorturl ? $shorturl : $data['videos_shorturl'];
// print "<pre>";print_r($data['videos_description']);exit;
        $description = @json_decode($data['videos_description'], true);
        $data['videos_description'] = $description ? $description : $data['videos_description'];

        $content = @json_decode($data['videos_content'], true);
        $data['videos_content'] = $content ? $content : $data['videos_content'];

        if($CMS->vars['translations'])
        {
            //Đa ngôn ngữ
            if(!is_array($data['videos_shorturl']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $shorturl = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    $shorturl[$langCode] = $data['videos_shorturl'];
                }

                $data['videos_shorturl'] = $shorturl;
            }

            if(!is_array($data['videos_name']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $name = [];
                $name_bk = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                	$name[$langCode] = $data['videos_name'];
	            	// Check permission to read Info
					if ( $CMS->permit["videos_read"] == true )
					{
						$name_bk[$langCode] = "<a href='{$CMS->vars['root_domain']}/?site=videos&act=show&id={$data['videos_id']}'>{$data['videos_name']}</a>";
						
					}

                }

                $data['videos_name_bk'] = $name_bk;
                $data['videos_name'] = $name;
            }else
            {
            	$data['videos_name_bk'] = $data['videos_name'];

            	$name_bk = [];
            	// Check permission to read Info
				if ( $CMS->permit["videos_read"] == true )
				{
					foreach ($data['videos_name_bk'] as $langCode => $cat_name)
	                {
						$name_bk[$langCode] = "<a href='{$CMS->vars['root_domain']}/?site=videos&act=show&id={$data['videos_id']}'>{$cat_name}</a>";
					}
					$data['videos_name_bk'] = $name_bk;
				}

            }

            if(!is_array($data['videos_description']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $description = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    $description[$langCode] = $data['videos_description'];
                }

                $data['videos_description'] = $description;
            }

            if(!is_array($data['videos_content']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $content = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    $content[$langCode] = $data['videos_content'];
                }

                $data['videos_content'] = $content;
            }

        }
        else
        {
        	if(is_array($data['videos_shorturl']))
            {
            	$data['videos_shorturl'] = $data['videos_shorturl'][$CMS->vars['default_language']];
        		$data['videos_url'] = "/{$data['videos_shorturl'][$CMS->vars['default_language']]}-n{$data['videos_id']}.html";
        	}

        	if(is_array($data['videos_description']))
            {
            	$data['videos_description'] = $data['videos_description'][$CMS->vars['default_language']];
        	}

        	if(is_array($data['videos_content']))
            {
            	$data['videos_content'] = $data['videos_content'][$CMS->vars['default_language']];
        	}

            if(is_array($data['videos_name']))
            {
                //Neu la dang mang thi chuyen ve dang chuoi binh thuong
                $data['videos_name'] = $data['videos_name'][$CMS->vars['default_language']];

                // Check permission to read Info
				if ( $type == 1 )
				{
					$data['videos_name_bk'] = "<a href='{$CMS->vars['root_domain']}/{$data['videos_shorturl']}'>{$data['videos_name']}</a>";
				}
				else if ( $CMS->permit["videos_read"] == true )
				{
					$data['videos_name_bk'] = "<a class='link' href='{$CMS->vars['root_domain']}/?site=videos&act=show&id={$data['videos_id']}'>{$data['videos_name']}</a>";
				}

            }else
            {
            	$data['videos_name_bk'] = $data['videos_name'];
            	if ( $CMS->permit["videos_read"] == true )
				{
					$data['videos_name_bk'] = "<a class='link' href='{$CMS->vars['root_domain']}/?site=videos&act=show&id={$data['videos_id']}'>{$data['videos_name']}</a>";
				}
            }


        }

        if(is_array($data['videos_shorturl']))
        {
    		$data['videos_url'] = "/{$data['videos_shorturl'][$CMS->vars['default_language']]}-n{$data['videos_id']}.html";
    	}else
    	{
    		$data['videos_url'] = "/{$data['videos_shorturl']}-n{$data['videos_id']}.html";
    	}
		
		
		// Image
		if ( $data['videos_image'] )
		{
			//@list($width, $height) = @getimagesize("{$CMS->vars['upload_dir']}/videos/{$data['videos_image']}");
			$width = 130;
			$height = 100;
			
			if ( substr($data['videos_image'], -3, 3) == "swf" )
			{
				$data['videos_image'] = "<object width='{$width}' height='{$height}'><param name='movie' value='{$CMS->vars['upload_url']}/videos/{$data['videos_image']}'><embed src='{$CMS->vars['upload_url']}/videos/{$data['videos_image']}' type='application/x-shockwave-flash' wmode='transparent' width='{$width}' height='{$height}'></embed></object>";
			}
			else
			{
				$data['videos_image'] = "<img align='left' src='{$CMS->vars['upload_url']}/videos/{$data['videos_image']}' width='{$width}' height='{$height}' border='0'>";
			}
		}
		else
		{
			//$data['videos_image'] = "<img align='left' src='{$CMS->vars['upload_url']}/videos/no_image.jpg' width='{$width}' height='{$height}' border='0'>";
		}

		if(is_file("{$CMS->vars['upload_dir']}/videos/{$data['data_bk']['videos_image']}") && file_exists("{$CMS->vars['upload_dir']}/videos/{$data['data_bk']['videos_image']}"))
		{
			$data['videos_image_url'] = "{$CMS->vars['upload_url']}/videos/{$data['data_bk']['videos_image']}";

			$data['videos_thumb_large_url'] = is_file($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/videos/{$data['data_bk']['videos_image']}", "thumbnail_large")) && is_file($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/videos/{$data['data_bk']['videos_image']}", "thumbnail_large")) ? $CMS->class->image->convert_to_thumbnail("{$data['videos_image_url']}", "thumbnail_large")  : "{$CMS->vars['img_url']}/no_images.png";

			$data['videos_thumb_medium_url'] = is_file($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/videos/{$data['data_bk']['videos_image']}", "thumbnail_medium")) && is_file($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/videos/{$data['data_bk']['videos_image']}", "thumbnail_medium")) ? $CMS->class->image->convert_to_thumbnail("{$data['videos_image_url']}", "thumbnail_medium")  : "{$CMS->vars['img_url']}/no_images.png";

			$data['videos_thumb_small_url'] = is_file($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/videos/{$data['data_bk']['videos_image']}", "thumbnail_small")) && is_file($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/videos/{$data['data_bk']['videos_image']}", "thumbnail_small")) ? $CMS->class->image->convert_to_thumbnail("{$data['videos_image_url']}", "thumbnail_small")  : "{$CMS->vars['img_url']}/no_images.png";
		}
		else
		{
			$data['videos_image_url'] = $data['videos_thumb_large_url'] = $data['videos_thumb_medium_url'] = $data['videos_thumb_small_url'] = "{$CMS->vars['img_url']}/no_images.png";
		}
		

		if($data['videos_tags_id'] != "")
		{
//			$data['videos_tags_id'] = $CMS->tags->export_tags($data['videos_tags_id']);
			$data['videos_tags_id_bk'] = $CMS->videos->get_tags($data['videos_tags_id']);
		} 
		// Category
		$data['cat_id_bk'] = $this->get_name_cate($data['cat_id']);
		// Sub Category
		$data['parent_cat_id_bk'] = $this->get_name_cate($data['parent_cat_id']);
		
		$data['cat_id_bk2'] = $this->get_name_cate($data['cat_id'],1);
		//name category the first
		$data['name_cat_name_first'] = $this->get_name_cate($data['cat_id'],2);

		$data['name_cat_id_first'] = $this->get_name_cate($data['cat_id'],3);
		$name_cate_seo = $CMS->class->seo->cleanurl($data['name_cat_name_first']);
		$data['url_cate_first'] = "{$CMS->vars['root_domain']}/danh-muc/{$name_cate_seo}/{$data['name_cat_id_first']}.html";
		// Category
		$data['parent_cat_id_bk2'] = $this->get_name_cate($data['parent_cat_id'],1);
		
		
		$data['videos_royalty_bk'] = $CMS->class->input->currency($data['videos_royalty']);
		// Description
		$data['videos_description_bk'] = $CMS->class->editor->substr(strip_tags($data['videos_description']), 0, 500);
		// Replace the IS Hot
		$data['videos_is_hot_bk'] = $CMS->lang["videos_is_hot_{$data['videos_is_hot']}"];		
		// Replace the IS Active Post
		$data['videos_active_bk'] = $CMS->lang["videos_active_{$data['videos_active']}"];		

		// Replace the IS Active Post
		$data['videos_active_bk2'] = $CMS->lang["videos_active_color_{$data['videos_active']}"];		

		// Replace the Status

		$data['videos_display_bk'] = $CMS->lang["display_{$data['videos_display']}"];		
		// Chuc nang binh luan 
		$data['enable_comment_bk'] = $CMS->lang["videos_is_hot_{$data['enable_comment']}"];
		if($data['videos_schedule'] == 1)
		{
			$data['videos_time_display_bk'] = "(".$CMS->class->date->date_format( $data['videos_time_display'], 1 ).")";
		}
		// Chuc nang hen gio dang bai
		$data['videos_schedule_bk'] = $CMS->lang["videos_schedule_{$data['videos_schedule']}"];
		// Bgcolor
		$data['bgcolor'] = $this->record_cnt % 2 != 0 ? "#FFFFFF" : "#F6F6F6";
		
		// User
		$data['user_name'] = $CMS->user->get_info($data['user_id'],"user_name");

		
		// Count
		$data['record_cnt'] = $this->record_cnt;
		
		$this->record_cnt++;
		
		return $data;
	}

	
	public function convert_value($data)
	{
		global $CMS, $DB;

	
		// Convert Register to GMT
		$data['videos_time_bk'] = $data['videos_time'];
		$data['videos_time'] = $CMS->class->date->date_format( $data['videos_time'], 1 );

		$data['videos_name_bk'] = htmlspecialchars_decode($data['videos_name']);
		$data['videos_url'] = "{$CMS->vars['root_domain']}/chi-tiet/{$data['videos_shorturl']}/{$data['videos_id']}.html";
		// Image
		if ( $data['videos_image'] )
		{
			//@list($width, $height) = @getimagesize("{$CMS->vars['upload_dir']}/videos/{$data['videos_image']}");
			$width = 130;
			$height = 100;
			
			if ( substr($data['videos_image'], -3, 3) == "swf" )
			{
				$data['videos_image'] = "{$CMS->vars['upload_url']}/videos/{$data['videos_image']}";
			}
			else
			{
				$data['videos_image_thumbresize'] = "{$CMS->vars['upload_url']}/videos/thumbresize/{$data['videos_image']}";
		
				$data['videos_image'] = "{$CMS->vars['upload_url']}/videos/{$data['videos_image']}";
				
				
			}
		}
		else
		{
			$data['videos_image_thumbresize'] ="{$CMS->vars['root_domain']}/templates/images/no-image.jpg";
			$data['videos_image'] ="{$CMS->vars['root_domain']}/templates/images/no-image.jpg";
			//$data['videos_image'] = "<img align='left' src='{$CMS->vars['upload_url']}/videos/no_image.jpg' width='{$width}' height='{$height}' border='0'>";
		}
		
		
		if($data['videos_tags_id'] != "")
		{
			$data['videos_tags_id_bk'] = $CMS->tags->get_tags($data['videos_tags_id'],0);
		} 
		// Category
		$data['cat_id_bk'] = $this->get_name_cate($data['cat_id']);
		// Sub Category
		$data['parent_cat_id_bk'] = $this->get_name_cate($data['parent_cat_id']);
		
		$data['cat_id_bk2'] = $this->get_name_cate($data['cat_id'],1);
		//name category the first
		$data['name_cat_name_first'] = $this->get_name_cate($data['cat_id'],2);
		
		$data['name_cat_id_first'] = $this->get_name_cate($data['cat_id'],3);
		$name_cate_seo = $CMS->class->seo->cleanurl($data['name_cat_name_first']);
		$data['url_cate_first'] = "{$CMS->vars['root_domain']}/danh-muc/{$name_cate_seo}/{$data['name_cat_id_first']}.html";
		// Category
		$data['parent_cat_id_bk2'] = $this->get_name_cate($data['parent_cat_id'],1);
		$data['videos_description_bk'] =	$data['videos_description'];
		// Description
		$data['videos_description'] = $CMS->class->editor->substr(strip_tags($data['videos_description']), 0, 240);
			// User
		$data['user_name'] = $CMS->user->get_info($data['user_id'],"user_display_name");
		
		return $data;
	}
	
	public function searchvalue($data)
	{
		global $CMS, $DB;

		// Convert Register to GMT
		$data['videos_time'] = $CMS->class->date->date_format( $data['videos_time'], 0 );

		// Replace the Status
		$data['videos_display'] = $CMS->lang["display_{$data['videos_display']}"];
	

			// Description
		$data['parent_cat_id'] = $this->get_name_cate($data['parent_cat_id']);

		$data['videos_active_bk'] = $CMS->lang["videos_active_{$data['videos_active']}"];		

		// Replace the IS Active Post
		$data['videos_active'] = $CMS->lang["videos_active_color_{$data['videos_active']}"];	
		
		// Category
		$cat = $CMS->config_videos->get_info($data['cat_id']);
		$data['cat_id'] = $cat['cat_name'];

		return $data;
	}
	
	//===========================================================================
	//  INFO
	//===========================================================================
	
	public function get_info( $record_id = 0, $field_name = "")
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "videos" )
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

        $sql_add = '';
		
		// Check type
		//$sql_add .= ($this->videos_project ? " AND videos_project='{$this->videos_project}' " : "");

        $sql = "SELECT * FROM ".root_table."videos WHERE videos_id='{$record_id}' OR videos_name='{$record_id}' OR videos_shorturl='{$record_id}' AND videos_deleted=0 {$sql_add} ORDER BY videos_id DESC LIMIT 1";

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
			$DB->query("SELECT * FROM ".root_table."videos WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND videos_deleted=0");
		}
		else
		{
			$DB->query("SELECT * FROM ".root_table."videos WHERE {$field}='{$value}' AND videos_deleted=0");
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
		// print "<pre>";
		// print_r($CMS->input);
		// print_r($_FILES);exit;
		$videos_name = $CMS->input['videos_name'];
		$videos_description = $CMS->input['videos_description'];
		$videos_content = $CMS->input["videos_content"];
		$check = true;
		if(is_array($videos_name) and is_array($videos_description) and is_array($videos_content))
        {
        	foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
            	$videos_shorturl[$langCode] = $CMS->class->seo->cleanurl($videos_name[$langCode]);

            	$videos_name[$langCode] = preg_replace( "/\r|\n/", "", $videos_name[$langCode]);
            	$videos_name[$langCode] = str_replace("'", "&#39;", $videos_name[$langCode]);

            	$videos_content[$langCode] = preg_replace( "/\r|\n/", "", $videos_content[$langCode]);
            	$videos_content[$langCode] = str_replace("'", "&#39;", $videos_content[$langCode]);

            	$videos_description[$langCode] = preg_replace( "/\r|\n/", "", $videos_description[$langCode]);
            	$videos_description[$langCode] = str_replace("'", "&#39;", $videos_description[$langCode]);

            	if($langCode == $CMS->vars['default_language'])
				{
					$name_alert = $cat_name[$langCode];
				}
            }

            if (empty($videos_name[$CMS->vars['default_language']])) {
                $_SESSION['msg'] .= $CMS->lang['videos_incomplete_name'].' ('.$CMS->vars['translations'][$CMS->vars['default_language']].')<br>';
                $check = false;
            }
            
            // if (empty($videos_content[$CMS->vars['default_language']])) {
            //     $_SESSION['msg'] .= $CMS->lang['videos_incomplete_content'].' ('.$CMS->vars['translations'][$CMS->vars['default_language']].')<br>';
            //     $check = false;
            // }

            $videos_name = @json_encode($videos_name, JSON_UNESCAPED_UNICODE);
            $videos_description = @json_encode($videos_description, JSON_UNESCAPED_UNICODE);
            $videos_content = @json_encode($videos_content, JSON_UNESCAPED_UNICODE);
            $videos_shorturl = @json_encode($videos_shorturl, JSON_UNESCAPED_UNICODE);
        }
        else
        {
            $videos_name = $CMS->class->editor->input('videos_name');
			$videos_description = $CMS->class->editor->input('videos_description');
			$videos_content = $CMS->class->editor->input("videos_content");
			$videos_shorturl = $CMS->class->seo->cleanurl($videos_name);

			$name_alert = $videos_name;
        }
		
        $videos_link = $CMS->class->editor->input('videos_link');
		$videos_order = intval($CMS->input['videos_order']);
		$input_cat_id = $CMS->input["cat_id"];
		$cat_id = $this->convert_category($input_cat_id);
		
		$videos_type = $CMS->input["videos_type"] ? intval($CMS->input["videos_type"]) : 1;
		$videos_display = intval($CMS->input["videos_display"]);
		$videos_is_hot = intval($CMS->input["videos_is_hot"]);
		// $videos_tags_id_bk = $_POST["videos_tags_id"];
		// $videos_tags_id = $CMS->tags->add_cnv_tags($videos_tags_id_bk);
		$parent_cat_id  = $this->convert_category($input_cat_id,1);
		
		
		$meta_title = trim($CMS->class->editor->input("meta_title"));
		$meta_description = trim($CMS->class->editor->input("meta_description"));
		$meta_keywords = trim($CMS->class->editor->input("meta_keywords"));
		$enable_comment = intval($CMS->input['enable_comment']);
		$videos_tags = @json_encode($CMS->tags->addTags($CMS->input['videos_tags']), JSON_UNESCAPED_UNICODE);

		$videos_image_alt = $CMS->input['videos_image_alt'];

		// Set post schedule
		// $videos_schedule = intval($CMS->input['videos_schedule']);
		// Convert videos time display
		// $time = $CMS->input['videos_time_display'];
		// $arr = explode(" ",$time);
		// $dd = explode("/",$arr[0]);
		// $outtemp = $dd[1]."/".$dd[0]."/".$dd[2]." ".$arr[1];
		// $unixTimestamp = strtotime($time);
		// $videos_time_display = $unixTimestamp + (3647);

		// $CMS->tags->add_tags($videos_tags_id);

			// Check input
		// if (  $videos_name == "") { $CMS->errormsg = "{$CMS->lang['videos_incomplete_name']}"; return false; }

		// if ( $_POST["cat_id"] == "") { $CMS->errormsg = "{$CMS->lang['videos_incomplete_category']}"; return false; }
		
		 // if ( empty($videos_content) )
	  //   {  $CMS->errormsg = "{$CMS->lang['videos_incomplete_content']}"; return false; }
		if($check == false)
		{
			return false;
		}

		// Check upload
		if($CMS->input['image-data'])
		{
			// $videos_image = "{$CMS->class->image->check_folder_img("videos","",1,"thumbnail_large")}/".$CMS->class->image->uploadImgBase64($CMS->input['image-data'], "videos/{$CMS->class->image->check_folder_img("videos","",1,"thumbnail_large")}", "thumbnail_large", 600);

			// $CMS->class->image->check_folder_img("videos","",1,"thumbnail_medium"); //Tao folder thumb truoc khi resize
			// $CMS->class->image->resize("{$CMS->vars['upload_dir']}/videos/{$videos_image}",$CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/videos/{$videos_image}","thumbnail_medium"), 450);

			// $CMS->class->image->check_folder_img("videos","",1,"thumbnail_small"); //Tao folder thumb truoc khi resize
			// $CMS->class->image->resize("{$CMS->vars['upload_dir']}/videos/{$videos_image}",$CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/videos/{$videos_image}","thumbnail_small"), 300);
		}
		else
		{
			//$_SESSION['error_msg'] .= "{$CMS->lang['videos_incomplete_image']}<br />";
			// return false;
		}

		// Insert data
		$DB->query("INSERT INTO ".root_table."videos (videos_name, videos_description, videos_content, cat_id,parent_cat_id, videos_tags_id, videos_image, videos_display, videos_is_hot, videos_time, videos_schedule, videos_time_display ,user_id, videos_shorturl,meta_title,meta_description,meta_keywords,videos_active,enable_comment, videos_tags, videos_link, videos_order, videos_type) VALUES ('{$videos_name}', '{$videos_description}', '{$videos_content}','{$cat_id}' ,'{$parent_cat_id}', '{$videos_tags_id}', '{$videos_image}', '{$videos_display}', '{$videos_is_hot}','".time()."', '{$videos_schedule}', '{$videos_time_display}', '{$member['user_id']}', '{$videos_shorturl}', '{$meta_title}','{$meta_description}', '{$meta_keywords}', 1,'{$enable_comment}', '{$videos_tags}', '{$videos_link}', '{$videos_order}', '{$videos_type}')");

        // Delete cache
        $CMS->class->cache->mdelete($this->cache_prefix);
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['videos_added']} <b>{$name_alert}</b>")."<br />";

		//Update Count
		$this->update_cat_count($cat_id,"add");
		// Get info
		$videos = $this->get_info($videos_name);

		//Update tags count
        $CMS->tags->updateCount($CMS->input['videos_tags']);
		
		// Update Module ID
		$CMS->attach->update("videos", $videos['videos_id']);
		
		// Update Cat videos Count
		//$DB->query("UPDATE ".root_table."videos_category SET cat_videos_count=cat_videos_count+1 WHERE cat_id='{$videos['videos_id']}'");
		//$DB->query("UPDATE ".root_table."videos_category SET cat_videos_count=cat_videos_count+1  WHERE cat_id='{$videos['videos_id']}'");
		
		return $videos;
	}
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;

		// Get info
		$videos_bk = $this->get_info();

		$videos_name = $CMS->input['videos_name'];
		$videos_description = $CMS->input['videos_description'];
		$videos_content = $CMS->input["videos_content"];
		$check = true;
		if(is_array($videos_name) and is_array($videos_description) and is_array($videos_content))
        {
        	foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
            	$videos_shorturl[$langCode] = $CMS->class->seo->cleanurl($videos_name[$langCode]);

            	$videos_name[$langCode] = preg_replace( "/\r|\n/", "", $videos_name[$langCode]);
            	$videos_name[$langCode] = str_replace("'", "&#39;", $videos_name[$langCode]);

            	$videos_content[$langCode] = preg_replace( "/\r|\n/", "", $videos_content[$langCode]);
            	$videos_content[$langCode] = str_replace("'", "&#39;", $videos_content[$langCode]);

            	$videos_description[$langCode] = preg_replace( "/\r|\n/", "", $videos_description[$langCode]);
            	$videos_description[$langCode] = str_replace("'", "&#39;", $videos_description[$langCode]);

            	if($langCode == $CMS->vars['default_language'])
				{
					$name_alert = $videos_name[$langCode];
				}
            }

            if (empty($videos_name[$CMS->vars['default_language']])) {
                $_SESSION['msg'] .= $CMS->lang['videos_incomplete_name'].' ('.$CMS->vars['translations'][$CMS->vars['default_language']].')<br>';
                $check = false;
            }
            
            // if (empty($videos_content[$CMS->vars['default_language']])) {
            //     $CMS->errormsg .= $CMS->lang['videos_incomplete_content'].' ('.$CMS->vars['translations'][$CMS->vars['default_language']].')<br>';
            //     $check = false;
            // }

            $videos_name = @json_encode($videos_name, JSON_UNESCAPED_UNICODE);
            $videos_description = @json_encode($videos_description, JSON_UNESCAPED_UNICODE);
            $videos_content = @json_encode($videos_content, JSON_UNESCAPED_UNICODE);
            $videos_shorturl = @json_encode($videos_shorturl, JSON_UNESCAPED_UNICODE);
        }
        else
        {
            $videos_name = $CMS->class->editor->input('videos_name');
			$videos_description = $CMS->class->editor->input('videos_description');
			$videos_content = $CMS->class->editor->input("videos_content");
			$videos_shorturl = $CMS->class->seo->cleanurl($videos_name);

			$name_alert = $videos_name;
        }
		$videos_link = $CMS->class->editor->input('videos_link');
		$videos_order = intval($CMS->input['videos_order']);
		$input_cat_id = $CMS->input["cat_id"];
		$cat_id = $this->convert_category($input_cat_id);

		$videos_type = $CMS->input["videos_type"] ? intval($CMS->input["videos_type"]) : 1;
		$videos_display = intval($CMS->input["videos_display"]);
		$videos_is_hot = intval($CMS->input["videos_is_hot"]);
		$videos_active = intval($CMS->input["videos_active"]);
        $videos_tags = @json_encode($CMS->tags->addTags($CMS->input['videos_tags']), JSON_UNESCAPED_UNICODE);

		$parent_cat_id  = $this->convert_category($input_cat_id,1);
		// $videos_royalty = $CMS->input['videos_royalty'];
		// $videos_tags_id_bk = $_POST["videos_tags_id"];
		// $videos_tags_id = $CMS->tags->add_cnv_tags($videos_tags_id_bk);
		// $videos_shorturl = $CMS->class->seo->cleanurl($videos_name);

		$meta_title = trim($CMS->class->editor->input("meta_title"));
		$meta_description = trim($CMS->class->editor->input("meta_description"));
		$meta_keywords = trim($CMS->class->editor->input("meta_keywords"));
		// $enable_comment = intval($CMS->input['enable_comment']);

        $videos_image_alt = $CMS->input['videos_image_alt'];

		// Set post schedule
		// $videos_schedule = intval($CMS->input['videos_schedule']);
		// // Convert videos time display
		// $time = $CMS->input['videos_time_display'];
		// $arr = explode(" ",$time);
		// $dd = explode("/",$arr[0]);
		// $outtemp = $dd[1]."/".$dd[0]."/".$dd[2]." ".$arr[1];
		// $unixTimestamp = strtotime($time);
		// $videos_time_display = $unixTimestamp + (3647);
		
		// // Convert Tags
		// $CMS->tags->add_tags($videos_tags_id);
		
		
			// Check input
		// if ( ! $videos_name ) { $CMS->errormsg = "{$CMS->lang['videos_incomplete_name']}"; return false; }

		// if ( $_POST["cat_id"] == "") { $CMS->errormsg = "{$CMS->lang['videos_incomplete_category']}"; return false; }
		// if ( empty($videos_content)) { $CMS->errormsg = "{$CMS->lang['videos_incomplete_content']}"; return false; }

		// Check upload
		if($CMS->input['image-data'])
		{
			// $videos_image = "{$CMS->class->image->check_folder_img("videos","",1,"thumbnail_large")}/".$CMS->class->image->uploadImgBase64($CMS->input['image-data'], "videos/{$CMS->class->image->check_folder_img("videos","",1,"thumbnail_large")}", "thumbnail_large", 600);
			// @unlink("{$CMS->vars['upload_dir']}/videos/{$videos_bk['videos_image']}");
			// @unlink($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/videos/{$videos_bk['videos_image']}", "thumbnail_large"));

			// $CMS->class->image->check_folder_img("videos","",1,"thumbnail_medium"); //Tao folder thumb truoc khi resize
			// $CMS->class->image->resize("{$CMS->vars['upload_dir']}/videos/{$videos_image}",$CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/videos/{$videos_image}","thumbnail_medium"), 450);
			// @unlink($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/videos/{$videos_bk['videos_image']}", "thumbnail_medium"));

			// $CMS->class->image->check_folder_img("videos","",1,"thumbnail_small"); //Tao folder thumb truoc khi resize
			// $CMS->class->image->resize("{$CMS->vars['upload_dir']}/videos/{$videos_image}",$CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/videos/{$videos_image}","thumbnail_small"), 300);
			// @unlink($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/videos/{$videos_bk['videos_image']}", "thumbnail_small"));
		}
		else
		{
           // $videos_image = $videos_bk['videos_image'];
			// $_SESSION['msg'] .= "{$CMS->lang['videos_incomplete_image']}<br />";
			// return false;
		}
		
		// Step 1: Save detail logs
		// $videos_bk['videos_tags_id'] = $CMS->tags->convert_postvalue($videos_bk['videos_tags_id']);
		$CMS->class->logs->old_data = $videos_bk;
		
		// Update info
		$DB->query("UPDATE ".root_table."videos SET videos_name='{$videos_name}', videos_description='{$videos_description}',  videos_content='{$videos_content}', videos_display='{$videos_display}' , videos_is_hot='{$videos_is_hot}', cat_id = '{$cat_id}',parent_cat_id = '{$parent_cat_id}', videos_time_update='".time()."', videos_shorturl='{$videos_shorturl}', meta_title = '{$meta_title}' , meta_description = '{$meta_description}' , meta_keywords = '{$meta_keywords}', videos_image='{$videos_image}', videos_tags = '{$videos_tags}', videos_link='{$videos_link}', videos_order='{$videos_order}', videos_type='{$videos_type}'  WHERE videos_id='{$videos_bk['videos_id']}'");

        // Delete cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		$CMS->class->logs->key = "videos_{$videos_bk['videos_id']}";
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['videos_edited']} <b>{$name_alert}</b>")."<br />";
		
		// Logs videos schedule
		
		//Update_cat count
		$this->update_cat_count($videos_bk['cat_id'],"dev");

        //Update tags count (old)

        if($tags_old = @json_decode($videos_bk['videos_tags'], true))
        {

            $CMS->input['videos_tags'] = array_merge($CMS->input['videos_tags'] ? $CMS->input['videos_tags'] : [], $tags_old);
            $CMS->input['videos_tags'] = array_unique($CMS->input['videos_tags']);
        }

        //Update tags count (new)
        $CMS->tags->updateCount($CMS->input['videos_tags']);

		// Get info
		$videos = $this->get_info();
		
					
		
		//Update_cat count
		$this->update_cat_count($videos['cat_id'],"add");
		
		// if($videos_display != $videos_bk['videos_display'])
		// {
		// 	$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['convert_status_display']} <b> {$CMS->lang["videos_display_{$videos_bk['videos_display']}"]} -> {$CMS->lang["videos_display_{$videos_display}"]}</b>")."<br />";
		// }

		// Step 2: Save detail logs
		$CMS->class->logs->key = "videos_{$videos['videos_id']}";
		// $videos['videos_tags_id'] = $CMS->tags->convert_postvalue($videos['videos_tags_id']);
		$CMS->class->logs->save_detail("videos",$videos['videos_id'],$videos);
		
		// Logs videos schedule
		// if($videos_bk['videos_schedule'] != $videos['videos_schedule'])
		// {
		// 	$videos = $this->convertvalue($videos);
		// 	$videos_bk = $this->convertvalue($videos_bk);
		// 	$CMS->class->logs->insert("{$CMS->lang['videos_schedule']}: <b>{$videos_bk['videos_schedule_bk']}{$videos_bk['videos_time_display_bk']} -> {$videos['videos_schedule_bk']}{$videos['videos_time_display_bk']}</b>")."<br />";
		// }
		
		return $videos;
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
		
	
		// Xoa hinh anh trong attach
		$this->delete_attach("videos",$data['videos_id']);
		// Xoa hinh cuar bai viet
		if($data['videos_image'] != "")
		{
			// @unlink("{$CMS->vars['upload_dir']}/videos/{$data['videos_image']}");
			// @unlink($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/videos/{$data['videos_image']}", "thumbnail_large"));
			// @unlink($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/videos/{$data['videos_image']}", "thumbnail_medium"));
			// @unlink($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/videos/{$data['videos_image']}", "thumbnail_small"));
		}
		$DB->query("UPDATE ".root_table."videos SET videos_deleted = 1, algolia_sync = 0 WHERE videos_id={$data['videos_id']}");

        // Delete cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		//Update_cat count
		$cat = $this->get_info($data['videos_id']);
		$this->update_cat_count($cat['cat_id'],"dev");
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['videos_deleted']} <b>{$data['videos_name']}</b>")."<br />";

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=videos&page={$CMS->input['page']}");
		
		return true;
	}
	
	public function mdelete()
	{
		global $CMS, $DB;
		
		$deleted = 0;
		
		$_SESSION["msg"] .= "";

		//print_r($CMS->input); exit;
		
		for ( $i = 0; $i < intval( $CMS->input["data_cnt"] ); $i++ )
		{
			$id = intval( $CMS->input["id_{$i}"] );
				
			if ( $id )
			{
				$data = $this->get_info($id);
				// Xoa hinh anh trong attach
				$this->delete_attach("videos",$data['videos_id']);
				// Xoa hinh cuar bai viet
				if($data['videos_image'] != "")
				{
					@unlink("{$CMS->vars['upload_dir']}/videos/{$data['videos_image']}");
					@unlink($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/videos/{$data['videos_image']}", "thumbnail_large"));
					@unlink($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/videos/{$data['videos_image']}", "thumbnail_medium"));
					@unlink($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/videos/{$data['videos_image']}", "thumbnail_small"));
				}
				$DB->query("UPDATE ".root_table."videos SET videos_deleted = 1, algolia_sync = 0 WHERE videos_id={$data['videos_id']}");
		
				//Update_cat count
				$cat = $this->get_info($id);
				$this->update_cat_count($cat['cat_id'],"dev");
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['videos_deleted']} <b>{$data['videos_name']}</b>")."<br />";
				
				$deleted = 1;
			}
		}
		
		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['videos_delete_failed']}";
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
		$CMS->class->search->mod_name = "videos";
		$CMS->class->search->search_type = 0;
		$CMS->class->search->fields_type = array("videos_time" => "time");
		$CMS->class->search->table_name = array("videos");

		// Output
		$data = $CMS->class->search->get_info();

		// Update SQL Query
		$this->sql_add .= $data;


		// Get List
		return $this->listing();
		
		/*if($CMS->input['is_excel']=="videos")
		{
			$CMS->report->report['format'] = "videos";
			$CMS->report->report_display = 2;
			$CMS->report->build_excel_header();
			$CMS->report->build_excel_module();
			$CMS->report->build_excel_footer();
		}*/
		
	}

	//===========================================================================
	//  ACTIVE POST
	//===========================================================================
	
	public function active()
	{
		global $CMS, $DB, $member;
		
		// Get info
		$videos_bk = $this->get_info();

		// User input
		$videos_active = intval($CMS->input['method']);
		if($videos_active == 2)
		{
			$DB->query("UPDATE ".root_table."videos SET videos_active ='{$videos_active}', videos_royalty =0 WHERE videos_id='{$videos_bk['videos_id']}'");
		}
		else
		{
			$DB->query("UPDATE ".root_table."videos SET videos_active ='{$videos_active}' WHERE videos_id='{$videos_bk['videos_id']}'");
	
		}

        // Delete cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		if($videos_active != $videos_bk['videos_active'])
		{
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['convert_status']} {$videos_bk['videos_name']}<b> {$CMS->lang["videos_active_{$videos_bk['videos_active']}"]} -> {$CMS->lang["videos_active_{$videos_active}"]}</b>")."<br />";
		}
		$videos = $this->get_info();

		return $videos;
	}
	
	//===========================================================================
	//  update_royalty  POST
	//===========================================================================
	
	public function update_royalty()
	{
		global $CMS, $DB, $member;
	
		// Get info
		$videos_bk = $this->get_info();

		// User input
		$videos_royalty = $CMS->input['videos_royalty'];	
	
		if($videos_bk['videos_active'] == 0 OR $videos_bk['videos_active'] == 2)
		{
			$_SESSION["msg"] .= $CMS->lang['not_update_royalty'];
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=videos&act=show&id={$videos_bk['videos_id']}");
			return false;
		}
		if( is_numeric($videos_royalty) == FALSE)
		{
			$_SESSION["msg"] .= $CMS->lang['not_numeric_update_royalty'];
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=videos&act=show&id={$videos_bk['videos_id']}");
			return false;
		}
	
		// Step 1: Save detail logs
		$CMS->class->logs->old_data = $videos_bk;
		$CMS->class->logs->key = "videos_{$videos_bk['videos_id']}";
		
		$DB->query("UPDATE ".root_table."videos SET videos_royalty ='{$videos_royalty}' WHERE videos_id='{$videos_bk['videos_id']}'");

        // Delete cache
        $CMS->class->cache->mdelete($this->cache_prefix);
	
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['update_royalty']} {$videos_bk['videos_name']}<b> : {$CMS->class->input->currency($videos_bk['videos_royalty'])} => {$CMS->class->input->currency($videos_royalty)}</b>")."<br />";
		
		$videos = $this->get_info();
		
			// Step 2: Save detail logs
		$CMS->class->logs->key = "videos_{$videos['videos_id']}";
		$CMS->class->logs->save_detail("videos",$videos['videos_id'],$videos);
		

		return $videos;
	}



	//===========================================================================
	//  LOAD videos
	//===========================================================================
	
	public function load_videos()
	{
		global $CMS, $DB;
		
		$this->loadhtml();

		// Load videos
		// $CMS->class->page->type = 1;
		$this->per_page = 10;
		$this->prefix_html = "danh-sach-tin-tuc-moi-nhat/trang_";
		$this->suffix_html = ".html";
		$CMS->class->page->type = "cat_videos";
		$sql = "SELECT * FROM ".root_table."videos WHERE videos_display = 1 AND videos_deleted = 0 AND videos_active = 1 ORDER BY videos_id DESC";

		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create($sql, $this->per_page, $this->prefix_html, $this->suffix_html );
		// Print data
		$output = "";

		if ( $DB->num_rows( $this->sql_query ) > 0 )
		{
			while ( $result = $DB->fetch_array( $this->sql_query ) )
			{

				$result = $this->convertvalue($result);

    			if($i == 0)
    			{
    				$output_con .=<<<EOF
						<section class="top-videos">
                              <figure class="img">
                                  <a href="{$result['videos_url']}" title="{$result['videos_name']}">
                                    <img src="{$result['videos_thumb_large_url']}" alt="{$result['videos_name']}"/>
                                        <span class="date">{$result['videos_time']}</span>
                                    </a>
                                </figure>
                                <h2><a href="{$result['videos_url']}" title="{$result['videos_name']}">{$result['videos_name']}</a></h2>
                                <p>{$result['videos_description']}</p>
                            </section>
EOF;

    			}else
    			{
    				$output_li .= <<<EOF
						<li>
                          <div class="col-md-4 col-sm-4 col-xs-4">
                              <a href="{$result['videos_url']}" title="{$result['videos_name']}"><img src="{$result['videos_thumb_medium_url']}" alt="{$result['videos_name']}"/></a>
                            </div>
                            <div class="col-md-8 col-sm-8 col-xs-8">
                              <h2><a href="{$result['videos_url']}" title="{$result['videos_name']}">{$result['videos_name']}</a></h2>
                                <span class="date">{$result['videos_time']}</span>
                                <p>{$result['videos_description']}</p>
                            </div>
                        </li>
EOF;


    			}
    			
    			$i++;	

    		}

    		$output =<<<EOF
    			{$output_con}
    			<section class="child-videos">
		              <ul class="row">
							{$output_li}
		              </ul>
	              </section>

EOF;

    	}else
    	{
    		$output =<<<EOF
    		<section class="top-videos">Đang cập nhật</section>
EOF;

    	}
			

		return $output;
	}
	
	//===========================================================================
	//  LOAD OTHER videos
	//===========================================================================
	
	public function load_videos_other( $data )
	{
		global $CMS, $DB;
		
		$output = "";
		$sql_add = "";
		
		$sql_add .= ($this->videos_project ? " AND videos_project='{$this->videos_project}' " : "");
		
		$sql = $DB->query("SELECT * FROM ".root_table."videos WHERE videos_id!='{$data['videos_id']}' AND videos_deleted=0 AND videos_display=1 {$sql_add} ORDER BY videos_time DESC LIMIT 10");

		while ( $data = $DB->fetch_array( $sql ) )
		{
			$data = $this->convertvalue( $data, 1 );
			
			$output .= $this->html->videos_record_other( $data );
		}
		
		$CMS->global->html['videos_other'] = $output;
	}
	
	//===========================================================================
	//  LOAD videos
	//===========================================================================
	
	public function load_videos_right()
	{
		global $CMS, $DB;
		
		// Print data
		$output = "";
		
		$result = $DB->query("SELECT * FROM ".root_table."videos WHERE videos_deleted=0 and videos_project='web' ORDER BY videos_id DESC LIMIT 8");

		if ( $DB->num_rows( $result ) > 0 )
		{
			while ( $data = $DB->fetch_array( $result ) )
			{
				$data = $this->convertvalue( $data, 1 );
				
				$output .= "
					<li>
						<a href='{$CMS->vars['root_domain']}/tin-tuc/{$data['videos_shorturl']}'> {$data['videos_name']} </a>						
					</li>	
						";
			}
		}
		else
		{
			$output .= $CMS->lang['videos_no_data'];
		}
				
		return $output;
	}
	
	
	//===========================================================================
	//  LOAD OTHER videos
	//===========================================================================

    public function load_cate_videos( $input = "" )
    {
        global $CMS, $DB;

        $output = "";

        $sql = "SELECT * FROM ".root_table."videos_category WHERE cat_deleted = 0 AND parent_id = 0 ORDER BY cat_id DESC ";

        $cacheData = $DB->fetch_data($sql, $CMS->config_parent_videos->cache_prefix);

        foreach ( $cacheData as $data )
        {
            $selected = preg_match("/(\|".$data['cat_id']."\|)/", $input) == true ? "selected" : "";

            $output .= "<option {$selected} data-icon=\"font-icon-home\" value='{$data['cat_id']}'>{$data['cat_name']}</option>";

            $output .= $this->load_sub_menu($data['cat_id'], $input);
        }

        return $output;
    }
		
	
	//===========================================================================
	// CONVERT CATEGORY
	//===========================================================================
	
	public function convert_category($input = "", $type = 0)
	{
		global $CMS;
		
        if ( ! $input ) { return ""; }
        
		$output = "|";

		// Load data
		foreach( $input as $data => $cat)
        {
            // If not empty
            if ( $cat )
            {
                // Parent category
                if ( $type == 1 )
                {
                    $parent_id = $CMS->config_videos->get_info_cate($cat, "parent_id");

                    // If it's a parent
                    if ( $parent_id > 0 )
                    {
                        // Except if it's existed
                        if ( preg_match("/(\|".$parent_id."\|)/", $output) == false  )
                        {
                            $output .= "{$parent_id}|";
                        }
                    }
                    // If it's a child
                    else{
                        $output .= "{$cat}|";
                    }
                }
                // Normal category
                else{
                    $output .= "{$cat}|";
                }
            }
        }

        return $output;
	}
	
	//===========================================================================
	// CONVERT CATEGORY
	//===========================================================================
	
	public function update_cat_count($input = "", $act = "add")
	{
		global $CMS, $DB;
		
		$output = "";
		$array = explode("|",$input);
	
		for($i = 0; $i <= count($array); $i++)
		{
			if($array[$i] != '')
			{
				// Tạm ẩn để tính lại cat count 
				// if($act == "add")
				// {
				// 	$DB->query("UPDATE ".root_table."videos_category SET cat_count=cat_count+1 WHERE cat_id='{$array[$i]}'");
				// 	$cate = $CMS->config_videos->get_info_cate($array[$i]);
				// 	$DB->query("UPDATE ".root_table."videos_category SET cat_count=cat_count+1 WHERE cat_id='{$cate['parent_id']}'");
					
				// }
				// elseif($act == "dev")
				// {
				// 	$data = $CMS->config_parent_videos->get_info($array[$i]);
		
				// 	if($data['cat_count'] > 0)
				// 	{
				// 		$DB->query("UPDATE ".root_table."videos_category SET cat_count=cat_count-1 WHERE cat_id='{$data['cat_id']}'");
				// 		$cate = $CMS->config_videos->get_info_cate($data['cat_id']);
				// 		$DB->query("UPDATE ".root_table."videos_category SET cat_count=cat_count-1 WHERE cat_id='{$cate['parent_id']}'");
				// 	}

    //         	}

				// Tính lại cat count để update
				$sql = $DB->query("SELECT COUNT(videos_id) as count FROM ".root_table."videos WHERE cat_id LIKE '%|{$array[$i]}|%' AND videos_deleted=0 AND videos_display=1");
				$count = $DB->fetch_array($sql)['count'];
				$DB->query("UPDATE ".root_table."videos_category SET cat_count='{$count}' WHERE cat_id='{$array[$i]}'");

				//Clear cache
                $CMS->class->cache->mdelete($this->cache_prefix);
        	}
		}
		
		// return $output;
	}
	
	
	//===========================================================================
	//  LOAD OTHER videos
	//===========================================================================
	
	public function load_sub_menu( $parent_id = 0, $input = "", $line="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")
	{
		global $DB, $CMS;
		
		$output = "";

		$sql = "SELECT * FROM ".root_table."videos_category WHERE cat_deleted = 0 AND parent_id = '{$parent_id}' AND parent_id > 0 ORDER BY cat_id DESC ";

        $cacheData = $DB->fetch_data($sql,$CMS->config_parent_videos->cache_prefix);

		if($cacheData)
		{
			foreach ( $cacheData as $data )
			{
				$data = $CMS->config_parent_videos->convertvalue($data);
				$name_cat = is_array($data['cat_name']) ? $data['cat_name'][$CMS->vars['default_language']] : $data['cat_name'];
				if($input != '')
				{
					$pos = strpos($input, "|{$data['cat_id']}|");
		    		$selected = $pos === false ? "" : "selected";

		    		$output .= "<option {$selected} data-icon=\"font-icon-home\" value='{$data['cat_id']}'>{$line}|-- {$name_cat}</option>";
				}
				else
				{
                    $output .= "<option data-icon=\"font-icon-home\" value='{$data['cat_id']}'>{$line}|-- {$name_cat}</option>";
				}
			}
		}

		return $output;
	}
	
	//===========================================================================
	//  LOAD OTHER videos
	//===========================================================================
	
	public function get_name_cate( $cate = "", $type = 0)
	{
		global $CMS, $DB;
		
		$output = "";

		$arr = explode("|",$cate);
		if(count($arr) > 0)
		{
			for($i = 0; $i <= count($arr); $i ++)
			{
				if($arr[$i] != '')
				{
					$data = $CMS->config_videos->get_info($arr[$i],"cat_name");
					$name = @json_decode($data, true);
			        $data = $name ? $name : $data;
			        if($CMS->vars['translations'])
			        {
			            //Đa ngôn ngữ
			            if(is_array($data))
			            {
			                //Neu k phai dang mang thi chuyen ve mang
			                $cat_name = '';
			                foreach ($CMS->vars['translations'] as $langCode => $langName)
			                {
			                	if($langCode == $CMS->vars['default_language'])
			                	{
			                    	$cat_name = $data[$langCode];
			                    	break;
			                	}
			                }

			                $data = $cat_name;
			            }
			        }
			        else
			        {
			        	if(is_array($data))
			            {
			            	$data = $data[$CMS->vars['default_language']];
			        	}
			        }

					if($type == 1)
					{
						$output .= "{$data}, ";
					}elseif($type == 2)
					{
						$output = "{$data}";
						break;
					}elseif($type == 3)
					{
						$output = "{$arr[$i]}";
						break;
					}else
					{
						$output .= "<p class='cata'>{$data}, </p>";
					}
				}
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
				<li class="tag" onClick="return del_tags($i);"  id="tags_$i">$tags_array[$i]<input type="hidden" name="videos_tags_id[]" value="{$tags_array[$i]}"/><a class="close" href="javascript: void();">close</a></li>
					
EOF;
			}
		}
		return $output;
	}
	
	//=============================================================
    // Xóa hình ảnh của bài viết trong table Attach
    //=============================================================
    public function delete_attach($module_name = "", $module_id = "")
    {
    	global $CMS, $DB;
		
		$output = "";
        $sql = $DB->query("SELECT * FROM ".root_table."attachment WHERE module_name = '{$module_name}'  AND module_id = '{$module_id}' ORDER BY attach_id DESC");
   		if($DB->num_rows($sql) > 0) 
        {
        	while($data = $DB->fetch_array($sql))
            {
            	@unlink("{$CMS->vars['upload_dir']}/attach/{$data['attach_location']}");	
                @unlink("{$CMS->vars['upload_dir']}/attach/thumbnail/{$data['attach_location']}");	
				
            }
        }
   		// Delete record
        $DB->query("DELETE FROM ".root_table."attachment WHERE module_name = '{$module_name}'  AND module_id = '{$module_id}' ");
    }
    

    //=========================================================
    // check videos id in aray
    //=========================================================
 
	public function check_exits_inarray( $videos_array= "", $videos_id = "")
	{
		global $CMS, $DB;
		$arr = explode(",",$videos_array);
 
		for($i = 0; $i<= count($arr); $i++)
		{
      
        	if($arr[$i] != "" )
            { 
                if($arr[$i] == $videos_id)
                {
                    return false;
                }
            }
          
		}
		return true;
	}
	
    
    //=========================================================
    // convert_array_duplicate
    //=========================================================
 
	public function cv_array_duplicate( $input= "")
	{
		global $CMS, $DB;
		if(substr($input,0,1) == ",")
        {
            $input = substr($input,1); 
        } 
        if(substr($input,-1,strlen($input)) == ",")
        {
            $input = substr($input,0,strlen($input)-1); 
        }
		return $input;
	}

    public function videos_view()
    {
    	global $CMS, $DB;

    	//$sevenday = time() - (3*24*3600);
        $sql = $DB->query("SELECT * FROM ".root_table."videos WHERE videos_display = 1 AND videos_active = 1 AND videos_deleted = 0 ORDER BY videos_views DESC LIMIT 5");	
    	$output = "";
    	
        if($DB->num_rows($sql) > 0)
        {
            while($data = $DB->fetch_array($sql))
            {
                $data = $CMS->videos->convert_value($data);
                
                $output .=<<<EOF
					<li><a href="{$data['videos_url']}" title="{$data['videos_name']}"><i class="fa fa-angle-right"></i>{$data['videos_name']}</a></li>

EOF;


            }
        }

        return $output;
    }  

    /****************************************************************************
	* HTML EMBED MEDIA
	****************************************************************************/
	public function embed_code( $url = "", $type = 0 )
	{
			global $CMS, $member, $DB;
			//print_r ($url);exit;
			$output = "";
		 
			$post_url = parse_url($url);
			$host = preg_replace('/www./','',$post_url['path']);
			$host = substr($host,0, -6);
			$split = explode('v=',$post_url['query']);
			$split_1 = explode('&',$post_url['query']);
			 if(stristr($split_1[0], 'v=') == TRUE) 
			 {
				$split = explode('v=',$split_1[0]);
			}
			else
			{
				$split = explode('v=',$split_1[1]);
		    }	
			$link = $split[1];

            if($host == "www.youtube.com" || $host == "youtube.com" || $post_url['host'] == "www.youtube.com" || $post_url['host'] == "youtube.com")
			{
				if($type == 3)
				{
				$output .=<<<EOF
	
				
				  	  <object width="100%" height="70%">
					<param name="movie" value="http://www.youtube.com/v/{$link}" />
					<embed src="http://www.youtube.com/v/{$link}"
					  type="application/x-shockwave-flash" width="640" height="240" />
				</object>
EOF;
				}
				else
				{
					$output .=<<<EOF
							  
			  
				 <iframe width="560" height="315" src="https://www.youtube.com/embed/{$link}" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>


EOF;
				}
			}
			elseif($post_url['host'] == "www.dailymotion.com" || $post_url['host'] == "dailymotion.com" || $host == "www.dailymotion.com" || $host == "dailymotion.com" )
			 {
				$bk = parse_url($url);
				// The part you want
				$url= $bk['path'];
				$parts = explode('/',$url);
				$parts = explode('_',$parts[2]);
				if($type == 3)
				{
				$output .=<<<EOF
				<object width="640" height="440">
					<param name="movie" value="http://www.dailymotion.com/swf/video/{$parts[0]}?background=493D27&foreground=E8D9AC&highlight=FFFFF0"></param>
					<param name="allowFullScreen" value="true"></param>
					<param name="allowScriptAccess" value="always"></param>
					<embed type="application/x-shockwave-flash" src="http://www.dailymotion.com/swf/video/{$parts[0]}?background=493D27&foreground=E8D9AC&highlight=FFFFF0" width="560" height="315" allowfullscreen="true" allowscriptaccess="always"></embed>
				</object>

EOF;
				}
				else
				{
						$output .=<<<EOF
			
				<object width="240" height="240">
					<param name="movie" value="http://www.dailymotion.com/swf/video/{$parts[0]}?background=493D27&foreground=E8D9AC&highlight=FFFFF0"></param>
					<param name="allowFullScreen" value="true"></param>
					<param name="allowScriptAccess" value="always"></param>
					<embed type="application/x-shockwave-flash" src="http://www.dailymotion.com/swf/video/{$parts[0]}?background=493D27&foreground=E8D9AC&highlight=FFFFF0" width="560" height="315" allowfullscreen="true" allowscriptaccess="always"></embed>
				</object>
EOF;
				}
			}
			else
			 {
				 
				 $output .=<<<EOF
			  <embed width="450" height="350" type="application/x-shockwave-flash" src="{$CMS->vars['public_url']}/player/player.swf" flashvars="skin={$CMS->vars['parent_domain']}/public/player/blueratio/blueratio.xml&amp;file={$CMS->vars['parent_domain']}/uploads/video/file/{$data['file_location']}&amp;playlistsize=100&amp;image={$CMS->vars['parent_domain']}/uploads/video/images/{$data['image_location']}&amp;logo={$CMS->vars['parent_domain']}/uploads/video/images/{$data['image_location ']}&amp;autostart=false&amp;shuffle=true&amp;repeat=list" allowfullscreen="true">
</embed>

  
			
EOF;
		     }
			 
			// Get image url 
			
			if($type == 1)
			{
				if($host == "www.youtube.com" || $host == "youtube.com" || $post_url['host'] == "www.youtube.com" || $post_url['host'] == "youtube.com")
				{
					$img = "http://img.youtube.com/vi/{$link}/0.jpg";
				}
				else
				{
					if($data['image_location'] != '')
					{
						$img = "{$CMS->vars['upload_url']}/video/images/{$data['image_location']}";
					
					}
					else
					{
						$img = "{$CMS->vars['root_domain']}/templates/images/no-image.jpg";
					}
				}
				return $img;
				
			}

		
			return $output;
		}
		

}

?>