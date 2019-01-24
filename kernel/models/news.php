<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->news = new class_news;

class class_news {

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
	 * @param $news_project
	 *		Use for multiple projects
	 */

	public $news_project = "";

    /**
     * @var string $show_page
     */
	public $show_page = "";

    /**
     * @var string $cache_prefix
     */
	public $cache_prefix = 'news';
	
	

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
			// 	$this->html = $CMS->class->template->load_simple(\core\ezy::$app_dir."/modules/news/templates/skin_news.php");
			// }
			// else
			// {
				$this->html = $CMS->class->template->load_template("skin_news");
			// }	

		}
	}
	
	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("news_time,news_name,news_id,news_display,cat_id");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "news_time";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "desc";

		$sql = "SELECT N.* FROM ".root_table."news AS N LEFT JOIN ".root_table."news_category AS C ON C.cat_id=N.cat_id WHERE N.news_deleted=0 AND {$this->sql_add} 1=1 ORDER BY N.{$default_field} {$default_order}";

		list($this->show_page, $data) = $DB->fetch_listing($sql, $this->per_page, $this->prefix_html, $this->suffix_html, $CMS->input['page'], $this->cache_prefix.'.'.$CMS->config_parent_news->cache_prefix);

        return $data;
	}

	public function listing_whm()
	{
		global $CMS, $DB, $member;
		$reseller_id = intval($member['reseller_id']);
		// Update Arrange Data
		$this->arrange_data = trim("news_time,news_name,news_id,news_display,cat_id");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "news_time";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "desc";

		$sql = "SELECT N.* FROM ".root_table."news AS N LEFT JOIN ".root_table."news_category AS C ON C.cat_id=N.cat_id WHERE N.news_deleted=0 AND {$this->sql_add} 1=1 AND reseller_id='{$reseller_id}' ORDER BY N.{$default_field} {$default_order}";

		list($this->show_page, $data) = $DB->fetch_listing($sql, $this->per_page, $this->prefix_html, $this->suffix_html, $CMS->input['page'], $this->cache_prefix.'.'.$CMS->config_parent_news->cache_prefix);

        return $data;
	}
	public function listing_report()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("news_time,news_name,news_id,news_display,cat_id");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "news_time";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "desc";

		// Create SQL Query for listing Data
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."news WHERE news_deleted=0 AND {$this->sql_add} 1=1 ORDER BY {$default_field} {$default_order}", $this->per_page, $this->prefix_html, $this->suffix_html);
		$this->total =  $DB->num_rows($DB->query("SELECT * FROM ".root_table."news WHERE news_deleted=0 AND {$this->sql_add} 1=1"));
		//echo "SELECT * FROM ".root_table."news WHERE news_deleted=0 AND {$this->sql_add}";exit;
	}
	
	public function html($data)
	{
		global $CMS, $DB, $member;

		$this->loadhtml();

		// Display Header
		$output .= $this->html->news_header();
		
		if ( $data )
		{
			foreach( $data as $result )
			{
				// Convert info
				$result = $CMS->news->convertvalue($result);
				
				// Display Middle
				$output .= $this->html->news_middle($result);
			}
		}
		else
		{
			// Display No data
			$output .= $this->html->news_none();

			// No data 0
			$CMS->is_error = 1;
		}
		
		// Display
		$output .= $this->html->news_footer();
		
		// If the page is giving no data
		if ( $CMS->input['page'] > 1 && $CMS->is_error == 1)
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=news");
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
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_news_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_news_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";

			// Check permission to Delete
			if ( $CMS->permit["news_delete"] == true )
			{
				$data .= "<option value='delete_all'>{$CMS->lang['news_action_delete']}</option>";
				$this->control = 1;
			}
			
			$data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

			$CMS->class->cache->save("user_{$member['user_id']}_news_controller_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}
		
		if ( $CMS->vars['action_controller']  OR $CMS->permit["news_search"] == 1 )
		{
			$this->action_control = $this->html->news_control();
		}
		
		// Get category
		$CMS->config_news->data();
	}

	//===========================================================================
	//  DATA
	//===========================================================================

	public function defaultvalue($data)
	{
		global $CMS;

		$data['news_display'] = $data['news_display'] ? $data['news_display'] : 1;

		return $data;
	}

	public function convertvalue($data, $type = 0, $project = "")
	{
		global $CMS, $DB;

		$data['data_bk'] = $data;

		// Replace search content
		$data = $CMS->class->search->convertvalue($data);

		// Rewrite URL
		/*if ( $project == "template" ) $data['news_shorturl'] = ($CMS->vars['is_rewrite'] == 1 && $data['news_shorturl'])? "thong-bao/{$data['news_shorturl']}.html": "?site=notice&view=detail&id={$data['news_id']}";
		else $data['news_shorturl'] = ($CMS->vars['is_rewrite'] == 1 && $data['news_shorturl'])? "tin-tuc/{$data['news_shorturl']}.html": "?site=news&view=topic&id={$data['news_id']}";;
		*/
		
		// Convert Register to GMT
		$data['news_time_bkk'] = $data['news_time'];
		$data['news_time'] = $CMS->class->date->normal_time_news( $data['news_time']);
		$data['news_time_update'] = $data['news_time_update'] ? $CMS->class->date->date_format( $data['news_time_update'], 1 ) : "<i>N/A</i>";
		// $data['news_name_bk'] = htmlspecialchars_decode($data['news_name']);

		//print_r ($data['news_name_bk']);exit;
		$name = @json_decode($data['news_name'], true);
        $data['news_name'] = $name ? $name : $data['news_name'];

        $shorturl = @json_decode($data['news_shorturl'], true);
        $data['news_shorturl'] = $shorturl ? $shorturl : $data['news_shorturl'];
// print "<pre>";print_r($data['news_description']);exit;
        $description = @json_decode($data['news_description'], true);
        $data['news_description'] = $description ? $description : $data['news_description'];

        $content = @json_decode($data['news_content'], true);
        $data['news_content'] = $content ? $content : $data['news_content'];

        if($CMS->vars['translations'])
        {
            //Đa ngôn ngữ
            if(!is_array($data['news_shorturl']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $shorturl = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    $shorturl[$langCode] = $data['news_shorturl'];
                }

                $data['news_shorturl'] = $shorturl;
            }

            if(!is_array($data['news_name']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $name = [];
                $name_bk = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                	$name[$langCode] = $data['news_name'];
	            	// Check permission to read Info
					if ( $CMS->permit["news_read"] == true )
					{
						$name_bk[$langCode] = "<a href='{$CMS->vars['root_domain']}/?site=news&act=show&id={$data['news_id']}'>{$data['news_name']}</a>";
						
					}

                }

                $data['news_name_bk'] = $name_bk;
                $data['news_name'] = $name;
            }else
            {
            	$data['news_name_bk'] = $data['news_name'];

            	$name_bk = [];
            	// Check permission to read Info
				if ( $CMS->permit["news_read"] == true )
				{
					foreach ($data['news_name_bk'] as $langCode => $cat_name)
	                {
						$name_bk[$langCode] = "<a href='{$CMS->vars['root_domain']}/?site=news&act=show&id={$data['news_id']}'>{$cat_name}</a>";
					}
					$data['news_name_bk'] = $name_bk;
				}

            }

            if(!is_array($data['news_description']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $description = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    $description[$langCode] = $data['news_description'];
                }

                $data['news_description'] = $description;
            }

            if(!is_array($data['news_content']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $content = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    $content[$langCode] = $data['news_content'];
                }

                $data['news_content'] = $content;
            }

        }
        else
        {
        	if(is_array($data['news_shorturl']))
            {
            	$data['news_shorturl'] = $data['news_shorturl'][$CMS->vars['default_language']];
        		$data['news_url'] = "/{$data['news_shorturl'][$CMS->vars['default_language']]}-n{$data['news_id']}.html";
        	}

        	if(is_array($data['news_description']))
            {
            	$data['news_description'] = $data['news_description'][$CMS->vars['default_language']];
        	}

        	if(is_array($data['news_content']))
            {
            	$data['news_content'] = $data['news_content'][$CMS->vars['default_language']];
        	}

            if(is_array($data['news_name']))
            {
                //Neu la dang mang thi chuyen ve dang chuoi binh thuong
                $data['news_name'] = $data['news_name'][$CMS->vars['default_language']];

                // Check permission to read Info
				if ( $type == 1 )
				{
					$data['news_name_bk'] = "<a href='{$CMS->vars['root_domain']}/{$data['news_shorturl']}'>{$data['news_name']}</a>";
				}
				else if ( $CMS->permit["news_read"] == true )
				{
					$data['news_name_bk'] = "<a class='link' href='{$CMS->vars['root_domain']}/?site=news&act=show&id={$data['news_id']}'>{$data['news_name']}</a>";
				}

            }else
            {
            	$data['news_name_bk'] = $data['news_name'];
            	if ( $CMS->permit["news_read"] == true )
				{
					$data['news_name_bk'] = "<a class='link' href='{$CMS->vars['root_domain']}/?site=news&act=show&id={$data['news_id']}'>{$data['news_name']}</a>";
				}
            }


        }

        if(is_array($data['news_shorturl']))
        {
    		$data['news_url'] = "/{$data['news_shorturl'][$CMS->vars['default_language']]}-n{$data['news_id']}.html";
    	}else
    	{
    		$data['news_url'] = "/{$data['news_shorturl']}-n{$data['news_id']}.html";
    	}
		
		
		// Image
		if ( $data['news_image'] )
		{
			//@list($width, $height) = @getimagesize("{$CMS->vars['upload_dir']}/news/{$data['news_image']}");
			$width = 130;
			$height = 100;
			
			if ( substr($data['news_image'], -3, 3) == "swf" )
			{
				$data['news_image'] = "<object width='{$width}' height='{$height}'><param name='movie' value='{$CMS->vars['upload_url']}/news/{$data['news_image']}'><embed src='{$CMS->vars['upload_url']}/news/{$data['news_image']}' type='application/x-shockwave-flash' wmode='transparent' width='{$width}' height='{$height}'></embed></object>";
			}
			else
			{
				$data['news_image'] = "<img align='left' src='{$CMS->vars['upload_url']}/news/{$data['news_image']}' width='{$width}' height='{$height}' border='0'>";
			}
		}
		else
		{
			//$data['news_image'] = "<img align='left' src='{$CMS->vars['upload_url']}/news/no_image.jpg' width='{$width}' height='{$height}' border='0'>";
		}

		if(is_file("{$CMS->vars['upload_dir']}/news/{$data['data_bk']['news_image']}") && file_exists("{$CMS->vars['upload_dir']}/news/{$data['data_bk']['news_image']}"))
		{
			$data['news_image_url'] = "{$CMS->vars['upload_url']}/news/{$data['data_bk']['news_image']}";

			$data['news_thumb_large_url'] = is_file($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/news/{$data['data_bk']['news_image']}", "thumbnail_large")) && is_file($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/news/{$data['data_bk']['news_image']}", "thumbnail_large")) ? $CMS->class->image->convert_to_thumbnail("{$data['news_image_url']}", "thumbnail_large")  : "{$CMS->vars['img_url']}/no_images.png";

			$data['news_thumb_medium_url'] = is_file($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/news/{$data['data_bk']['news_image']}", "thumbnail_medium")) && is_file($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/news/{$data['data_bk']['news_image']}", "thumbnail_medium")) ? $CMS->class->image->convert_to_thumbnail("{$data['news_image_url']}", "thumbnail_medium")  : "{$CMS->vars['img_url']}/no_images.png";

			$data['news_thumb_small_url'] = is_file($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/news/{$data['data_bk']['news_image']}", "thumbnail_small")) && is_file($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/news/{$data['data_bk']['news_image']}", "thumbnail_small")) ? $CMS->class->image->convert_to_thumbnail("{$data['news_image_url']}", "thumbnail_small")  : "{$CMS->vars['img_url']}/no_images.png";
		}
		else
		{
			$data['news_image_url'] = $data['news_thumb_large_url'] = $data['news_thumb_medium_url'] = $data['news_thumb_small_url'] = "{$CMS->vars['img_url']}/no_images.png";
		}
		

		if($data['news_tags_id'] != "")
		{
//			$data['news_tags_id'] = $CMS->tags->export_tags($data['news_tags_id']);
			$data['news_tags_id_bk'] = $CMS->news->get_tags($data['news_tags_id']);
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
		
		
		$data['news_royalty_bk'] = $CMS->class->input->currency($data['news_royalty']);
		// Description
//		$data['news_description_bk'] = $CMS->class->editor->substr(strip_tags($data['news_description']), 0, 500);
		$data['news_description_bk'] = $data['news_description'];
		// Replace the IS Hot
		$data['news_is_hot_bk'] = $CMS->lang["news_is_hot_{$data['news_is_hot']}"];		
		// Replace the IS Active Post
		$data['news_active_bk'] = $CMS->lang["news_active_{$data['news_active']}"];		

		// Replace the IS Active Post
		$data['news_active_bk2'] = $CMS->lang["news_active_color_{$data['news_active']}"];		

		// Replace the Status

		$data['news_display_bk'] = $CMS->lang["display_{$data['news_display']}"];		
		// Chuc nang binh luan 
		$data['enable_comment_bk'] = $CMS->lang["news_is_hot_{$data['enable_comment']}"];
		if($data['news_schedule'] == 1)
		{
			$data['news_time_display_bk'] = "(".$CMS->class->date->date_format( $data['news_time_display'], 1 ).")";
		}
		// Chuc nang hen gio dang bai
		$data['news_schedule_bk'] = $CMS->lang["news_schedule_{$data['news_schedule']}"];
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
		$data['news_time_bk'] = $data['news_time'];
		$data['news_time'] = $CMS->class->date->date_format( $data['news_time'], 1 );

		$data['news_name_bk'] = htmlspecialchars_decode($data['news_name']);
		$data['news_url'] = "{$CMS->vars['root_domain']}/chi-tiet/{$data['news_shorturl']}/{$data['news_id']}.html";
		// Image
		if ( $data['news_image'] )
		{
			//@list($width, $height) = @getimagesize("{$CMS->vars['upload_dir']}/news/{$data['news_image']}");
			$width = 130;
			$height = 100;
			
			if ( substr($data['news_image'], -3, 3) == "swf" )
			{
				$data['news_image'] = "{$CMS->vars['upload_url']}/news/{$data['news_image']}";
			}
			else
			{
				$data['news_image_thumbresize'] = "{$CMS->vars['upload_url']}/news/thumbresize/{$data['news_image']}";
		
				$data['news_image'] = "{$CMS->vars['upload_url']}/news/{$data['news_image']}";
				
				
			}
		}
		else
		{
			$data['news_image_thumbresize'] ="{$CMS->vars['root_domain']}/templates/images/no-image.jpg";
			$data['news_image'] ="{$CMS->vars['root_domain']}/templates/images/no-image.jpg";
			//$data['news_image'] = "<img align='left' src='{$CMS->vars['upload_url']}/news/no_image.jpg' width='{$width}' height='{$height}' border='0'>";
		}
		
		
		if($data['news_tags_id'] != "")
		{
			$data['news_tags_id_bk'] = $CMS->tags->get_tags($data['news_tags_id'],0);
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
		$data['news_description_bk'] =	$data['news_description'];
		// Description
//		$data['news_description'] = $CMS->class->editor->substr(strip_tags($data['news_description']), 0, 240);
			// User
		$data['user_name'] = $CMS->user->get_info($data['user_id'],"user_display_name");
		
		return $data;
	}
	
	public function searchvalue($data)
	{
		global $CMS, $DB;

		// Convert Register to GMT
		$data['news_time'] = $CMS->class->date->date_format( $data['news_time'], 0 );

		// Replace the Status
		$data['news_display'] = $CMS->lang["display_{$data['news_display']}"];
	

			// Description
		$data['parent_cat_id'] = $this->get_name_cate($data['parent_cat_id']);

		$data['news_active_bk'] = $CMS->lang["news_active_{$data['news_active']}"];		

		// Replace the IS Active Post
		$data['news_active'] = $CMS->lang["news_active_color_{$data['news_active']}"];	
		
		// Category
		$cat = $CMS->config_news->get_info($data['cat_id']);
		$data['cat_id'] = $cat['cat_name'];

		return $data;
	}
	
	//===========================================================================
	//  INFO
	//===========================================================================
	
	public function get_info( $record_id = 0, $field_name = "")
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "news" )
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
		//$sql_add .= ($this->news_project ? " AND news_project='{$this->news_project}' " : "");

        $sql = "SELECT * FROM ".root_table."news WHERE news_id='{$record_id}' OR news_name='{$record_id}' OR news_shorturl='{$record_id}' AND news_deleted=0 {$sql_add} ORDER BY news_id DESC LIMIT 1";

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
			$DB->query("SELECT * FROM ".root_table."news WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND news_deleted=0");
		}
		else
		{
			$DB->query("SELECT * FROM ".root_table."news WHERE {$field}='{$value}' AND news_deleted=0");
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
		$news_name = $CMS->input['news_name'];
		$news_description = $CMS->input['news_description'];
		$news_content = $CMS->input["news_content"];
		$check = true;
		if(is_array($news_name) and is_array($news_description) and is_array($news_content))
        {
        	foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
            	$news_shorturl[$langCode] = $CMS->class->seo->cleanurl($news_name[$langCode]);

            	$news_name[$langCode] = preg_replace( "/\r|\n/", "", $news_name[$langCode]);
            	$news_name[$langCode] = str_replace("'", "&#39;", $news_name[$langCode]);

            	$news_content[$langCode] = preg_replace( "/\r|\n/", "", $news_content[$langCode]);
            	$news_content[$langCode] = str_replace("'", "&#39;", $news_content[$langCode]);

            	$news_description[$langCode] = preg_replace( "/\r|\n/", "", $news_description[$langCode]);
            	$news_description[$langCode] = str_replace("'", "&#39;", $news_description[$langCode]);

            	if($langCode == $CMS->vars['default_language'])
				{
					$name_alert = $cat_name[$langCode];
				}
            }

            if (empty($news_name[$CMS->vars['default_language']])) {
                $_SESSION['msg'] .= $CMS->lang['news_incomplete_name'].' ('.$CMS->vars['translations'][$CMS->vars['default_language']].')<br>';
                $check = false;
            }
            
            // if (empty($news_content[$CMS->vars['default_language']])) {
            //     $_SESSION['msg'] .= $CMS->lang['news_incomplete_content'].' ('.$CMS->vars['translations'][$CMS->vars['default_language']].')<br>';
            //     $check = false;
            // }

            $news_name = @json_encode($news_name, JSON_UNESCAPED_UNICODE);
            $news_description = @json_encode($news_description, JSON_UNESCAPED_UNICODE);
            $news_content = @json_encode($news_content, JSON_UNESCAPED_UNICODE);
            $news_shorturl = @json_encode($news_shorturl, JSON_UNESCAPED_UNICODE);
        }
        else
        {
            $news_name = $CMS->class->editor->input('news_name');
			$news_description = $CMS->class->editor->input('news_description');
			$news_content = $CMS->class->editor->input("news_content");
			$news_shorturl = $CMS->class->seo->cleanurl($news_name);

			$name_alert = $news_name;
        }
		

		$news_order = intval($CMS->input['news_order']);
		$input_cat_id = $CMS->input["cat_id"];
		$cat_id = $this->convert_category($input_cat_id);
		
		$news_type = $CMS->input["news_type"] ? intval($CMS->input["news_type"]) : 1;
		$news_display = intval($CMS->input["news_display"]);
		$news_is_hot = intval($CMS->input["news_is_hot"]);
		// $news_tags_id_bk = $_POST["news_tags_id"];
		// $news_tags_id = $CMS->tags->add_cnv_tags($news_tags_id_bk);
		$parent_cat_id  = $this->convert_category($input_cat_id,1);
		
		
		$meta_title = trim($CMS->class->editor->input("meta_title"));
		$meta_description = trim($CMS->class->editor->input("meta_description"));
		$meta_keywords = trim($CMS->class->editor->input("meta_keywords"));
		$enable_comment = intval($CMS->input['enable_comment']);
		$news_tags = @json_encode($CMS->tags->addTags($CMS->input['news_tags']), JSON_UNESCAPED_UNICODE);

		$news_image_alt = $CMS->input['news_image_alt'];

		// Set post schedule
		// $news_schedule = intval($CMS->input['news_schedule']);
		// Convert news time display
		// $time = $CMS->input['news_time_display'];
		// $arr = explode(" ",$time);
		// $dd = explode("/",$arr[0]);
		// $outtemp = $dd[1]."/".$dd[0]."/".$dd[2]." ".$arr[1];
		// $unixTimestamp = strtotime($time);
		// $news_time_display = $unixTimestamp + (3647);

		// $CMS->tags->add_tags($news_tags_id);

			// Check input
		// if (  $news_name == "") { $CMS->errormsg = "{$CMS->lang['news_incomplete_name']}"; return false; }

		// if ( $_POST["cat_id"] == "") { $CMS->errormsg = "{$CMS->lang['news_incomplete_category']}"; return false; }
		
		 // if ( empty($news_content) )
	  //   {  $CMS->errormsg = "{$CMS->lang['news_incomplete_content']}"; return false; }
		if($check == false)
		{
			return false;
		}

		// Check upload
		if($CMS->input['image-data'])
		{
			$news_image = "{$CMS->class->image->check_folder_img("news","",1,"thumbnail_large")}/".$CMS->class->image->uploadImgBase64($CMS->input['image-data'], "news/{$CMS->class->image->check_folder_img("news","",1,"thumbnail_large")}", "thumbnail_large", 600);

			$CMS->class->image->check_folder_img("news","",1,"thumbnail_medium"); //Tao folder thumb truoc khi resize
			$CMS->class->image->resize("{$CMS->vars['upload_dir']}/news/{$news_image}",$CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/news/{$news_image}","thumbnail_medium"), 450);

			$CMS->class->image->check_folder_img("news","",1,"thumbnail_small"); //Tao folder thumb truoc khi resize
			$CMS->class->image->resize("{$CMS->vars['upload_dir']}/news/{$news_image}",$CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/news/{$news_image}","thumbnail_small"), 300);
		}
		else
		{
			$_SESSION['error_msg'] .= "{$CMS->lang['news_incomplete_image']}<br />";
			// return false;
		}

		// Insert data
		$DB->query("INSERT INTO ".root_table."news (news_name, news_description, news_content, cat_id,parent_cat_id, news_tags_id, news_image, news_display, news_is_hot, news_time, news_schedule, news_time_display ,user_id, news_shorturl,meta_title,meta_description,meta_keywords,news_active,enable_comment, news_tags, news_image_alt, news_order, news_type) VALUES ('{$news_name}', '{$news_description}', '{$news_content}','{$cat_id}' ,'{$parent_cat_id}', '{$news_tags_id}', '{$news_image}', '{$news_display}', '{$news_is_hot}','".time()."', '{$news_schedule}', '{$news_time_display}', '{$member['user_id']}', '{$news_shorturl}', '{$meta_title}','{$meta_description}', '{$meta_keywords}', 1,'{$enable_comment}', '{$news_tags}', '{$news_image_alt}', '{$news_order}', '{$news_type}')");

        // Delete cache
        $CMS->class->cache->mdelete($this->cache_prefix);
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['news_added']} <b>{$name_alert}</b>")."<br />";

		//Update Count
		$this->update_cat_count($cat_id,"add");
		// Get info
		$news = $this->get_info($news_name);

		//Update tags count
        $CMS->tags->updateCount($CMS->input['news_tags']);
		
		// Update Module ID
		$CMS->attach->update("news", $news['news_id']);
		
		// Update Cat news Count
		//$DB->query("UPDATE ".root_table."news_category SET cat_news_count=cat_news_count+1 WHERE cat_id='{$news['news_id']}'");
		//$DB->query("UPDATE ".root_table."news_category SET cat_news_count=cat_news_count+1  WHERE cat_id='{$news['news_id']}'");
		
		return $news;
	}
	


	//===========================================================================
	//  ADD
	//===========================================================================
	
	public function add_whm()
	{
		global $CMS, $DB, $member;

		// User input
	 	$reseller_id = intval($member['reseller_id']);
		$news_name = $CMS->input['news_name'];
		$news_description = $CMS->input['news_description'];
		$news_content = $CMS->input["news_content"];
		$check = true;
		if(is_array($news_name) and is_array($news_description) and is_array($news_content))
        {
        	foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
            	$news_shorturl[$langCode] = $CMS->class->seo->cleanurl($news_name[$langCode]);

            	$news_name[$langCode] = preg_replace( "/\r|\n/", "", $news_name[$langCode]);
            	$news_name[$langCode] = str_replace("'", "&#39;", $news_name[$langCode]);

            	$news_content[$langCode] = preg_replace( "/\r|\n/", "", $news_content[$langCode]);
            	$news_content[$langCode] = str_replace("'", "&#39;", $news_content[$langCode]);

            	$news_description[$langCode] = preg_replace( "/\r|\n/", "", $news_description[$langCode]);
            	$news_description[$langCode] = str_replace("'", "&#39;", $news_description[$langCode]);

            	if($langCode == $CMS->vars['default_language'])
				{
					$name_alert = $cat_name[$langCode];
				}
            }

            if (empty($news_name[$CMS->vars['default_language']])) {
                $_SESSION['msg'] .= $CMS->lang['news_incomplete_name'].' ('.$CMS->vars['translations'][$CMS->vars['default_language']].')<br>';
                $check = false;
            }
          
            $news_name = @json_encode($news_name, JSON_UNESCAPED_UNICODE);
            $news_description = @json_encode($news_description, JSON_UNESCAPED_UNICODE);
            $news_content = @json_encode($news_content, JSON_UNESCAPED_UNICODE);
            $news_shorturl = @json_encode($news_shorturl, JSON_UNESCAPED_UNICODE);
        }
        else
        {
            $news_name = $CMS->class->editor->input('news_name');
			$news_description = $CMS->class->editor->input('news_description');
			$news_content = $CMS->class->editor->input("news_content");
			$news_shorturl = $CMS->class->seo->cleanurl($news_name);

			$name_alert = $news_name;
        }
		

		$news_order = intval($CMS->input['news_order']);
		$input_cat_id = $CMS->input["cat_id"];
		$cat_id = $this->convert_category($input_cat_id);
		
		$news_type = $CMS->input["news_type"] ? intval($CMS->input["news_type"]) : 1;
		$news_display = intval($CMS->input["news_display"]);
		$news_is_hot = intval($CMS->input["news_is_hot"]);
		// $news_tags_id_bk = $_POST["news_tags_id"];
		// $news_tags_id = $CMS->tags->add_cnv_tags($news_tags_id_bk);
		$parent_cat_id  = $this->convert_category($input_cat_id,1);
		
		
		$meta_title = trim($CMS->class->editor->input("meta_title"));
		$meta_description = trim($CMS->class->editor->input("meta_description"));
		$meta_keywords = trim($CMS->class->editor->input("meta_keywords"));
		$enable_comment = intval($CMS->input['enable_comment']);
		$news_tags = @json_encode($CMS->tags->addTags($CMS->input['news_tags']), JSON_UNESCAPED_UNICODE);

		$news_image_alt = $CMS->input['news_image_alt'];
 
		if($check == false)
		{
			return false;
		}

		// Check upload
		if($CMS->input['image-data'])
		{
			$news_image = "{$CMS->class->image->check_folder_img("news","",1,"thumbnail_large")}/".$CMS->class->image->uploadImgBase64($CMS->input['image-data'], "news/{$CMS->class->image->check_folder_img("news","",1,"thumbnail_large")}", "thumbnail_large", 600);

			$CMS->class->image->check_folder_img("news","",1,"thumbnail_medium"); //Tao folder thumb truoc khi resize
			$CMS->class->image->resize("{$CMS->vars['upload_dir']}/news/{$news_image}",$CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/news/{$news_image}","thumbnail_medium"), 450);

			$CMS->class->image->check_folder_img("news","",1,"thumbnail_small"); //Tao folder thumb truoc khi resize
			$CMS->class->image->resize("{$CMS->vars['upload_dir']}/news/{$news_image}",$CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/news/{$news_image}","thumbnail_small"), 300);
		}
		else
		{
			$_SESSION['error_msg'] .= "{$CMS->lang['news_incomplete_image']}<br />";
			// return false;
		}

		// Insert data
		$DB->query("INSERT INTO ".root_table."news (news_name, news_description, news_content, cat_id,parent_cat_id, news_tags_id, news_image, news_display, news_is_hot, news_time, news_schedule, news_time_display ,user_id, news_shorturl,meta_title,meta_description,meta_keywords,news_active,enable_comment, news_tags, news_image_alt, news_order, news_type, reseller_id) VALUES ('{$news_name}', '{$news_description}', '{$news_content}','{$cat_id}' ,'{$parent_cat_id}', '{$news_tags_id}', '{$news_image}', '{$news_display}', '{$news_is_hot}','".time()."', '{$news_schedule}', '{$news_time_display}', '{$member['user_id']}', '{$news_shorturl}', '{$meta_title}','{$meta_description}', '{$meta_keywords}', 1,'{$enable_comment}', '{$news_tags}', '{$news_image_alt}', '{$news_order}', '{$news_type}', '{$reseller_id}')");

        // Delete cache
        $CMS->class->cache->mdelete($this->cache_prefix);
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['news_added']} <b>{$name_alert}</b>")."<br />";

		//Update Count
		$this->update_cat_count($cat_id,"add");
		// Get info
		$news = $this->get_info($news_name);

		//Update tags count
        $CMS->tags->updateCount($CMS->input['news_tags']);
		
		// Update Module ID
		$CMS->attach->update("news", $news['news_id']);
		
		// Update Cat news Count
		//$DB->query("UPDATE ".root_table."news_category SET cat_news_count=cat_news_count+1 WHERE cat_id='{$news['news_id']}'");
		//$DB->query("UPDATE ".root_table."news_category SET cat_news_count=cat_news_count+1  WHERE cat_id='{$news['news_id']}'");
		
		return $news;
	}
	

	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;

		// Get info
		$news_bk = $this->get_info();

		$news_name = $CMS->input['news_name'];
		$news_description = $CMS->input['news_description'];
		$news_content = $CMS->input["news_content"];
		$check = true;
		if(is_array($news_name) and is_array($news_description) and is_array($news_content))
        {
        	foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
            	$news_shorturl[$langCode] = $CMS->class->seo->cleanurl($news_name[$langCode]);

            	$news_name[$langCode] = preg_replace( "/\r|\n/", "", $news_name[$langCode]);
            	$news_name[$langCode] = str_replace("'", "&#39;", $news_name[$langCode]);

            	$news_content[$langCode] = preg_replace( "/\r|\n/", "", $news_content[$langCode]);
            	$news_content[$langCode] = str_replace("'", "&#39;", $news_content[$langCode]);

            	$news_description[$langCode] = preg_replace( "/\r|\n/", "", $news_description[$langCode]);
            	$news_description[$langCode] = str_replace("'", "&#39;", $news_description[$langCode]);

            	if($langCode == $CMS->vars['default_language'])
				{
					$name_alert = $news_name[$langCode];
				}
            }

            if (empty($news_name[$CMS->vars['default_language']])) {
                $_SESSION['msg'] .= $CMS->lang['news_incomplete_name'].' ('.$CMS->vars['translations'][$CMS->vars['default_language']].')<br>';
                $check = false;
            }
            
            // if (empty($news_content[$CMS->vars['default_language']])) {
            //     $CMS->errormsg .= $CMS->lang['news_incomplete_content'].' ('.$CMS->vars['translations'][$CMS->vars['default_language']].')<br>';
            //     $check = false;
            // }

            $news_name = @json_encode($news_name, JSON_UNESCAPED_UNICODE);
            $news_description = @json_encode($news_description, JSON_UNESCAPED_UNICODE);
            $news_content = @json_encode($news_content, JSON_UNESCAPED_UNICODE);
            $news_shorturl = @json_encode($news_shorturl, JSON_UNESCAPED_UNICODE);
        }
        else
        {
            $news_name = $CMS->class->editor->input('news_name');
			$news_description = $CMS->class->editor->input('news_description');
			$news_content = $CMS->class->editor->input("news_content");
			$news_shorturl = $CMS->class->seo->cleanurl($news_name);

			$name_alert = $news_name;
        }
		
		$news_order = intval($CMS->input['news_order']);
		$input_cat_id = $CMS->input["cat_id"];
		$cat_id = $this->convert_category($input_cat_id);

		$news_type = $CMS->input["news_type"] ? intval($CMS->input["news_type"]) : 1;
		$news_display = intval($CMS->input["news_display"]);
		$news_is_hot = intval($CMS->input["news_is_hot"]);
		$news_active = intval($CMS->input["news_active"]);
        $news_tags = @json_encode($CMS->tags->addTags($CMS->input['news_tags']), JSON_UNESCAPED_UNICODE);

		$parent_cat_id  = $this->convert_category($input_cat_id,1);
		// $news_royalty = $CMS->input['news_royalty'];
		// $news_tags_id_bk = $_POST["news_tags_id"];
		// $news_tags_id = $CMS->tags->add_cnv_tags($news_tags_id_bk);
		// $news_shorturl = $CMS->class->seo->cleanurl($news_name);

		$meta_title = trim($CMS->class->editor->input("meta_title"));
		$meta_description = trim($CMS->class->editor->input("meta_description"));
		$meta_keywords = trim($CMS->class->editor->input("meta_keywords"));
		// $enable_comment = intval($CMS->input['enable_comment']);

        $news_image_alt = $CMS->input['news_image_alt'];

		// Set post schedule
		// $news_schedule = intval($CMS->input['news_schedule']);
		// // Convert news time display
		// $time = $CMS->input['news_time_display'];
		// $arr = explode(" ",$time);
		// $dd = explode("/",$arr[0]);
		// $outtemp = $dd[1]."/".$dd[0]."/".$dd[2]." ".$arr[1];
		// $unixTimestamp = strtotime($time);
		// $news_time_display = $unixTimestamp + (3647);
		
		// // Convert Tags
		// $CMS->tags->add_tags($news_tags_id);
		
		
			// Check input
		// if ( ! $news_name ) { $CMS->errormsg = "{$CMS->lang['news_incomplete_name']}"; return false; }

		// if ( $_POST["cat_id"] == "") { $CMS->errormsg = "{$CMS->lang['news_incomplete_category']}"; return false; }
		// if ( empty($news_content)) { $CMS->errormsg = "{$CMS->lang['news_incomplete_content']}"; return false; }

		// Check upload
		if($CMS->input['image-data'])
		{
			$news_image = "{$CMS->class->image->check_folder_img("news","",1,"thumbnail_large")}/".$CMS->class->image->uploadImgBase64($CMS->input['image-data'], "news/{$CMS->class->image->check_folder_img("news","",1,"thumbnail_large")}", "thumbnail_large", 600);
			@unlink("{$CMS->vars['upload_dir']}/news/{$news_bk['news_image']}");
			@unlink($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/news/{$news_bk['news_image']}", "thumbnail_large"));

			$CMS->class->image->check_folder_img("news","",1,"thumbnail_medium"); //Tao folder thumb truoc khi resize
			$CMS->class->image->resize("{$CMS->vars['upload_dir']}/news/{$news_image}",$CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/news/{$news_image}","thumbnail_medium"), 450);
			@unlink($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/news/{$news_bk['news_image']}", "thumbnail_medium"));

			$CMS->class->image->check_folder_img("news","",1,"thumbnail_small"); //Tao folder thumb truoc khi resize
			$CMS->class->image->resize("{$CMS->vars['upload_dir']}/news/{$news_image}",$CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/news/{$news_image}","thumbnail_small"), 300);
			@unlink($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/news/{$news_bk['news_image']}", "thumbnail_small"));
		}
		else
		{
            $news_image = $news_bk['news_image'];
			// $_SESSION['msg'] .= "{$CMS->lang['news_incomplete_image']}<br />";
			// return false;
		}
		
		// Step 1: Save detail logs
		// $news_bk['news_tags_id'] = $CMS->tags->convert_postvalue($news_bk['news_tags_id']);
		$CMS->class->logs->old_data = $news_bk;
		
		// Update info
		$DB->query("UPDATE ".root_table."news SET news_name='{$news_name}', news_description='{$news_description}',  news_content='{$news_content}', news_display='{$news_display}' , news_is_hot='{$news_is_hot}', cat_id = '{$cat_id}',parent_cat_id = '{$parent_cat_id}', news_time_update='".time()."', news_shorturl='{$news_shorturl}', meta_title = '{$meta_title}' , meta_description = '{$meta_description}' , meta_keywords = '{$meta_keywords}', news_image='{$news_image}', news_tags = '{$news_tags}', news_image_alt='{$news_image_alt}', news_order='{$news_order}', news_type='{$news_type}'  WHERE news_id='{$news_bk['news_id']}'");

        // Delete cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		$CMS->class->logs->key = "news_{$news_bk['news_id']}";
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['news_edited']} <b>{$name_alert}</b>")."<br />";
		
		// Logs news schedule
		
		//Update_cat count
		$this->update_cat_count($news_bk['cat_id'],"dev");

        //Update tags count (old)

        if($tags_old = @json_decode($news_bk['news_tags'], true))
        {

            $CMS->input['news_tags'] = array_merge($CMS->input['news_tags'] ? $CMS->input['news_tags'] : [], $tags_old);
            $CMS->input['news_tags'] = array_unique($CMS->input['news_tags']);
        }

        //Update tags count (new)
        $CMS->tags->updateCount($CMS->input['news_tags']);

		// Get info
		$news = $this->get_info();
		
					
		
		//Update_cat count
		$this->update_cat_count($news['cat_id'],"add");
		
		// if($news_display != $news_bk['news_display'])
		// {
		// 	$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['convert_status_display']} <b> {$CMS->lang["news_display_{$news_bk['news_display']}"]} -> {$CMS->lang["news_display_{$news_display}"]}</b>")."<br />";
		// }

		// Step 2: Save detail logs
		$CMS->class->logs->key = "news_{$news['news_id']}";
		// $news['news_tags_id'] = $CMS->tags->convert_postvalue($news['news_tags_id']);
		$CMS->class->logs->save_detail("news",$news['news_id'],$news);
		
		// Logs news schedule
		// if($news_bk['news_schedule'] != $news['news_schedule'])
		// {
		// 	$news = $this->convertvalue($news);
		// 	$news_bk = $this->convertvalue($news_bk);
		// 	$CMS->class->logs->insert("{$CMS->lang['news_schedule']}: <b>{$news_bk['news_schedule_bk']}{$news_bk['news_time_display_bk']} -> {$news['news_schedule_bk']}{$news['news_time_display_bk']}</b>")."<br />";
		// }
		
		return $news;
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
		$this->delete_attach("news",$data['news_id']);
		// Xoa hinh cuar bai viet
		if($data['news_image'] != "")
		{
			@unlink("{$CMS->vars['upload_dir']}/news/{$data['news_image']}");
			@unlink($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/news/{$data['news_image']}", "thumbnail_large"));
			@unlink($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/news/{$data['news_image']}", "thumbnail_medium"));
			@unlink($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/news/{$data['news_image']}", "thumbnail_small"));
		}
		$DB->query("UPDATE ".root_table."news SET news_deleted = 1, algolia_sync = 0 WHERE news_id={$data['news_id']}");

        // Delete cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		//Update_cat count
		$cat = $this->get_info($data['news_id']);
		$this->update_cat_count($cat['cat_id'],"dev");
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['news_deleted']} <b>{$data['news_name']}</b>")."<br />";

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=news&page={$CMS->input['page']}");
		
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
				$this->delete_attach("news",$data['news_id']);
				// Xoa hinh cuar bai viet
				if($data['news_image'] != "")
				{
					@unlink("{$CMS->vars['upload_dir']}/news/{$data['news_image']}");
					@unlink($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/news/{$data['news_image']}", "thumbnail_large"));
					@unlink($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/news/{$data['news_image']}", "thumbnail_medium"));
					@unlink($CMS->class->image->convert_to_thumbnail("{$CMS->vars['upload_dir']}/news/{$data['news_image']}", "thumbnail_small"));
				}
				$DB->query("UPDATE ".root_table."news SET news_deleted = 1, algolia_sync = 0 WHERE news_id={$data['news_id']}");
		
				//Update_cat count
				$cat = $this->get_info($id);
				$this->update_cat_count($cat['cat_id'],"dev");
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['news_deleted']} <b>{$data['news_name']}</b>")."<br />";
				
				$deleted = 1;
			}
		}
		
		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['news_delete_failed']}";
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
		$CMS->class->search->mod_name = "news";
		$CMS->class->search->search_type = 0;
		$CMS->class->search->fields_type = array("news_time" => "time");
		$CMS->class->search->table_name = array("news");

		// Output
		$data = $CMS->class->search->get_info();

		// Update SQL Query
		$this->sql_add .= $data;


		// Get List
		return $this->listing();
		
		/*if($CMS->input['is_excel']=="news")
		{
			$CMS->report->report['format'] = "news";
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
		$news_bk = $this->get_info();

		// User input
		$news_active = intval($CMS->input['method']);
		if($news_active == 2)
		{
			$DB->query("UPDATE ".root_table."news SET news_active ='{$news_active}', news_royalty =0 WHERE news_id='{$news_bk['news_id']}'");
		}
		else
		{
			$DB->query("UPDATE ".root_table."news SET news_active ='{$news_active}' WHERE news_id='{$news_bk['news_id']}'");
	
		}

        // Delete cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		if($news_active != $news_bk['news_active'])
		{
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['convert_status']} {$news_bk['news_name']}<b> {$CMS->lang["news_active_{$news_bk['news_active']}"]} -> {$CMS->lang["news_active_{$news_active}"]}</b>")."<br />";
		}
		$news = $this->get_info();

		return $news;
	}
	
	//===========================================================================
	//  update_royalty  POST
	//===========================================================================
	
	public function update_royalty()
	{
		global $CMS, $DB, $member;
	
		// Get info
		$news_bk = $this->get_info();

		// User input
		$news_royalty = $CMS->input['news_royalty'];	
	
		if($news_bk['news_active'] == 0 OR $news_bk['news_active'] == 2)
		{
			$_SESSION["msg"] .= $CMS->lang['not_update_royalty'];
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=news&act=show&id={$news_bk['news_id']}");
			return false;
		}
		if( is_numeric($news_royalty) == FALSE)
		{
			$_SESSION["msg"] .= $CMS->lang['not_numeric_update_royalty'];
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=news&act=show&id={$news_bk['news_id']}");
			return false;
		}
	
		// Step 1: Save detail logs
		$CMS->class->logs->old_data = $news_bk;
		$CMS->class->logs->key = "news_{$news_bk['news_id']}";
		
		$DB->query("UPDATE ".root_table."news SET news_royalty ='{$news_royalty}' WHERE news_id='{$news_bk['news_id']}'");

        // Delete cache
        $CMS->class->cache->mdelete($this->cache_prefix);
	
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['update_royalty']} {$news_bk['news_name']}<b> : {$CMS->class->input->currency($news_bk['news_royalty'])} => {$CMS->class->input->currency($news_royalty)}</b>")."<br />";
		
		$news = $this->get_info();
		
			// Step 2: Save detail logs
		$CMS->class->logs->key = "news_{$news['news_id']}";
		$CMS->class->logs->save_detail("news",$news['news_id'],$news);
		

		return $news;
	}



	//===========================================================================
	//  LOAD NEWS
	//===========================================================================
	
	public function load_news()
	{
		global $CMS, $DB;
		
		$this->loadhtml();

		// Load news
		// $CMS->class->page->type = 1;
		$this->per_page = 10;
		$this->prefix_html = "danh-sach-tin-tuc-moi-nhat/trang_";
		$this->suffix_html = ".html";
		$CMS->class->page->type = "cat_news";
		$sql = "SELECT * FROM ".root_table."news WHERE news_display = 1 AND news_deleted = 0 AND news_active = 1 ORDER BY news_id DESC";

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
						<section class="top-news">
                              <figure class="img">
                                  <a href="{$result['news_url']}" title="{$result['news_name']}">
                                    <img src="{$result['news_thumb_large_url']}" alt="{$result['news_name']}"/>
                                        <span class="date">{$result['news_time']}</span>
                                    </a>
                                </figure>
                                <h2><a href="{$result['news_url']}" title="{$result['news_name']}">{$result['news_name']}</a></h2>
                                <p>{$result['news_description']}</p>
                            </section>
EOF;

    			}else
    			{
    				$output_li .= <<<EOF
						<li>
                          <div class="col-md-4 col-sm-4 col-xs-4">
                              <a href="{$result['news_url']}" title="{$result['news_name']}"><img src="{$result['news_thumb_medium_url']}" alt="{$result['news_name']}"/></a>
                            </div>
                            <div class="col-md-8 col-sm-8 col-xs-8">
                              <h2><a href="{$result['news_url']}" title="{$result['news_name']}">{$result['news_name']}</a></h2>
                                <span class="date">{$result['news_time']}</span>
                                <p>{$result['news_description']}</p>
                            </div>
                        </li>
EOF;


    			}
    			
    			$i++;	

    		}

    		$output =<<<EOF
    			{$output_con}
    			<section class="child-news">
		              <ul class="row">
							{$output_li}
		              </ul>
	              </section>

EOF;

    	}else
    	{
    		$output =<<<EOF
    		<section class="top-news">Đang cập nhật</section>
EOF;

    	}
			

		return $output;
	}
	
	//===========================================================================
	//  LOAD OTHER NEWS
	//===========================================================================
	
	public function load_news_other( $data )
	{
		global $CMS, $DB;
		
		$output = "";
		$sql_add = "";
		
		$sql_add .= ($this->news_project ? " AND news_project='{$this->news_project}' " : "");
		
		$sql = $DB->query("SELECT * FROM ".root_table."news WHERE news_id!='{$data['news_id']}' AND news_deleted=0 AND news_display=1 {$sql_add} ORDER BY news_time DESC LIMIT 10");

		while ( $data = $DB->fetch_array( $sql ) )
		{
			$data = $this->convertvalue( $data, 1 );
			
			$output .= $this->html->news_record_other( $data );
		}
		
		$CMS->global->html['news_other'] = $output;
	}
	
	//===========================================================================
	//  LOAD NEWS
	//===========================================================================
	
	public function load_news_right()
	{
		global $CMS, $DB;
		
		// Print data
		$output = "";
		
		$result = $DB->query("SELECT * FROM ".root_table."news WHERE news_deleted=0 and news_project='web' ORDER BY news_id DESC LIMIT 8");

		if ( $DB->num_rows( $result ) > 0 )
		{
			while ( $data = $DB->fetch_array( $result ) )
			{
				$data = $this->convertvalue( $data, 1 );
				
				$output .= "
					<li>
						<a href='{$CMS->vars['root_domain']}/tin-tuc/{$data['news_shorturl']}'> {$data['news_name']} </a>						
					</li>	
						";
			}
		}
		else
		{
			$output .= $CMS->lang['news_no_data'];
		}
				
		return $output;
	}
	
	
	//===========================================================================
	//  LOAD OTHER NEWS
	//===========================================================================

    public function load_cate_news( $input = "" )
    {
        global $CMS, $DB;

        $output = "";

        $sql = "SELECT * FROM ".root_table."news_category WHERE cat_deleted = 0 AND parent_id = 0 ORDER BY cat_id DESC ";

        $cacheData = $DB->fetch_data($sql, $CMS->config_parent_news->cache_prefix);

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
                    $parent_id = $CMS->config_news->get_info_cate($cat, "parent_id");

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
				// 	$DB->query("UPDATE ".root_table."news_category SET cat_count=cat_count+1 WHERE cat_id='{$array[$i]}'");
				// 	$cate = $CMS->config_news->get_info_cate($array[$i]);
				// 	$DB->query("UPDATE ".root_table."news_category SET cat_count=cat_count+1 WHERE cat_id='{$cate['parent_id']}'");
					
				// }
				// elseif($act == "dev")
				// {
				// 	$data = $CMS->config_parent_news->get_info($array[$i]);
		
				// 	if($data['cat_count'] > 0)
				// 	{
				// 		$DB->query("UPDATE ".root_table."news_category SET cat_count=cat_count-1 WHERE cat_id='{$data['cat_id']}'");
				// 		$cate = $CMS->config_news->get_info_cate($data['cat_id']);
				// 		$DB->query("UPDATE ".root_table."news_category SET cat_count=cat_count-1 WHERE cat_id='{$cate['parent_id']}'");
				// 	}

    //         	}

				// Tính lại cat count để update
				$sql = $DB->query("SELECT COUNT(news_id) as count FROM ".root_table."news WHERE cat_id LIKE '%|{$array[$i]}|%' AND news_deleted=0 AND news_display=1");
				$count = $DB->fetch_array($sql)['count'];
				$DB->query("UPDATE ".root_table."news_category SET cat_count='{$count}' WHERE cat_id='{$array[$i]}'");

				//Clear cache
                $CMS->class->cache->mdelete($this->cache_prefix);
        	}
		}
		
		// return $output;
	}
	
	
	//===========================================================================
	//  LOAD OTHER NEWS
	//===========================================================================
	
	public function load_sub_menu( $parent_id = 0, $input = "", $line="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")
	{
		global $DB, $CMS;
		
		$output = "";

		$sql = "SELECT * FROM ".root_table."news_category WHERE cat_deleted = 0 AND parent_id = '{$parent_id}' AND parent_id > 0 ORDER BY cat_id DESC ";

        $cacheData = $DB->fetch_data($sql,$CMS->config_parent_news->cache_prefix);

		if($cacheData)
		{
			foreach ( $cacheData as $data )
			{
				$data = $CMS->config_parent_news->convertvalue($data);
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
	//  LOAD OTHER NEWS
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
					$data = $CMS->config_news->get_info($arr[$i],"cat_name");
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
				<li class="tag" onClick="return del_tags($i);"  id="tags_$i">$tags_array[$i]<input type="hidden" name="news_tags_id[]" value="{$tags_array[$i]}"/><a class="close" href="javascript: void();">close</a></li>
					
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
    // check news id in aray
    //=========================================================
 
	public function check_exits_inarray( $news_array= "", $news_id = "")
	{
		global $CMS, $DB;
		$arr = explode(",",$news_array);
 
		for($i = 0; $i<= count($arr); $i++)
		{
      
        	if($arr[$i] != "" )
            { 
                if($arr[$i] == $news_id)
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

    public function news_view()
    {
    	global $CMS, $DB;

    	//$sevenday = time() - (3*24*3600);
        $sql = $DB->query("SELECT * FROM ".root_table."news WHERE news_display = 1 AND news_active = 1 AND news_deleted = 0 ORDER BY news_views DESC LIMIT 5");	
    	$output = "";
    	
        if($DB->num_rows($sql) > 0)
        {
            while($data = $DB->fetch_array($sql))
            {
                $data = $CMS->news->convert_value($data);
                
                $output .=<<<EOF
					<li><a href="{$data['news_url']}" title="{$data['news_name']}"><i class="fa fa-angle-right"></i>{$data['news_name']}</a></li>

EOF;


            }
        }

        return $output;
    }  

}

?>