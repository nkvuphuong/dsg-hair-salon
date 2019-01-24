<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->config_parent_news = new class_config_parent_news;

class class_config_parent_news {

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
	public $pages_cnt = 0;
	
	/**
	 * @param $previous_order
	 *		The templates
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

    /**
     * @var string $cache_prefix
     */
    public $cache_prefix = 'news_category';

	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	
	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			$this->html = $CMS->class->template->load_template("skin_config_parent_news");		
		}
	}
	
	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("cat_time,cat_id,cat_name,cat_status,cat_display_home");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "cat_order";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "ASC";
		
		// SQL Condition
		$this->sql_add .= " cat_deleted=0 AND ";

		// Create SQL Query for listing Data
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."news_category WHERE {$this->sql_add} 1=1 AND parent_id = 0 AND cat_deleted = 0 ORDER BY {$default_field} {$default_order}, cat_time DESC");
		
		$this->pages_cnt = $DB->num_rows( $this->sql_query );
	}
	
	//===========================================================================
	//  LISTING DATA CATEGORY HOME
	//===========================================================================
	
	public function listing_home()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("cat_time,cat_id,cat_name,cat_status,cat_display_home");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "cat_order_home";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "ASC";
		
		// SQL Condition
		$this->sql_add .= " cat_deleted=0 AND ";

		// Create SQL Query for listing Data
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."news_category WHERE {$this->sql_add} 1=1 AND parent_id = 0 AND cat_display_home = 1 AND cat_deleted = 0  ORDER BY {$default_field} {$default_order}");
		
		$this->pages_cnt = $DB->num_rows( $this->sql_query );
	}
	
	
	public function html()
	{
		global $CMS, $DB, $member;
		
		$this->loadhtml();
		
		// Display Header
		$output .= $this->html->header();
		
		if ( $DB->num_rows( $CMS->config_parent_news->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->config_parent_news->sql_query ) )
			{
				
				// Convert info
				$result = $CMS->config_parent_news->convertvalue($result);
				
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
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_parent_news");
		}
		
		return $output;
	}


	public function html_home()
	{
		global $CMS, $DB, $member;
		
		$this->loadhtml();
		
		// Display Header
		$output .= $this->html->header_home();
		
		if ( $DB->num_rows( $CMS->config_parent_news->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->config_parent_news->sql_query ) )
			{
				
				// Convert info
				$result = $CMS->config_parent_news->convertvalue($result);
				
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
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_parent_news");
		}
		
		return $output;
	}




	public function merge_html()
	{
		global $CMS, $DB, $member;

		$sql = "SELECT * FROM ".root_table."news_category WHERE  parent_id = 0 AND cat_deleted = 0 ORDER BY cat_order ASC";

        $cacheData = $DB->fetch_data($sql, $this->cache_prefix);

        $CMS->config_parent_news->pages_cnt = count($cacheData);

		$this->loadhtml();
		
		// Display Header
		$output .= $this->html->header();
		
		if ( $cacheData )
		{
			foreach( $cacheData as $result )
			{
				// Convert info
				$result = $CMS->config_parent_news->convertvalue($result);
				
				// Display Middle
				$output .= $this->html->middle($result);
				$output .= $this->load_sub_cate($result['cat_id']);
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
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_parent_news");
		}
		
		return $output;
	}
	
	public function data()
	{
		global $CMS, $DB;

		$sql = "SELECT * FROM ".root_table."news_category WHERE cat_deleted=0";

		$results = $DB->fetch_data($sql, $this->cache_prefix);

		$output = "";

		if($results)
        {
            foreach ( $results as $cat )
            {
                $output .= "<option value='{$cat['cat_id']}'>{$cat['cat_name']}</option>";
            }
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
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_config_parent_news_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_config_parent_news_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";

			// Check permission to Delete
			if ( $CMS->permit["config_parent_news_delete"] == true )
			{
				$data .= "<option value='delete_all'>{$CMS->lang['action_delete']}</option>";
				$this->control = 1;
			}
			
			// Check permission to Arrange
			if ( $CMS->permit["config_parent_news_arrange"] == true )
			{
				$data .= "<option value='arrange'>{$CMS->lang['cat_action_arrange']}</option>";
				$this->control = 1;
			}

			$data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

			$CMS->class->cache->save("user_{$member['user_id']}_config_parent_news_controller_{$CMS->vars['default_language']}", $data);
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

        $data['data_bk'] = $data_bk = $data;

		// Replace search content
		$data = $CMS->class->search->convertvalue($data);

		// Rewrite URL
		// $data['cat_shorturl'] = ($CMS->vars['is_rewrite'] == 1 && $data['cat_shorturl']) ? "loai-tin/{$data['cat_shorturl']}" : "?site=news&view=category&id={$data['cat_id']}";
		
		// Convert Register to GMT
		$data['cat_time'] = $CMS->class->date->date_format( $data['cat_time'], 1 );

		$name = @json_decode($data['cat_name'], true);
        $data['cat_name'] = $name ? $name : $data['cat_name'];

        $shorturl = @json_decode($data['cat_shorturl'], true);
        $data['cat_shorturl'] = $shorturl ? $shorturl : $data['cat_shorturl'];
// print "<pre>";print_r($data['cat_description']);exit;
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
					if ( $CMS->permit["config_parent_news_read"] == true )
					{
						$name_bk[$langCode] = "<a style='display: initial;' href='{$CMS->vars['root_domain']}/?site=config_parent_news&act=show&id={$data['cat_id']}'>{$data['cat_name']}</a>";
					}
                }

                $data['cat_name'] = $name;
                $data['cat_name_bk'] = $name_bk;

            }else
            {
            	$data['cat_name_bk'] = $data['cat_name'];
            	$name_bk = [];
            	// Check permission to read Info
				if ( $CMS->permit["config_parent_news_read"] == true )
				{
					foreach ($data['cat_name_bk'] as $langCode => $cat_name)
	                {
						$name_bk[$langCode] = "<a style='display: initial;' href='{$CMS->vars['root_domain']}/?site=config_parent_news&act=show&id={$data['cat_id']}'>{$cat_name}</a>";
					}
				}

				$data['cat_name_bk'] = $name_bk;
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
				if ( $CMS->permit["config_parent_news_read"] == true )
				{
					$data['cat_name_bk'] = "<a style='display: initial;' href='{$CMS->vars['root_domain']}/?site=config_parent_news&act=show&id={$data['cat_id']}'>{$data['cat_name']}</a>";
				}

            }else
            {
            	$data['cat_name_bk'] = $data['cat_name'];
            	if ( $CMS->permit["config_parent_news_read"] == true )
				{
					$data['cat_name_bk'] = "<a style='display: initial;' href='{$CMS->vars['root_domain']}/?site=config_parent_news&act=show&id={$data['cat_id']}'>{$data['cat_name']}</a>";

				}
            }


        }

        $data['cat_url'] = is_array($data['cat_shorturl']) ? "/{$data['cat_shorturl'][$CMS->vars['default_language']]}-nc{$data['cat_id']}.html" : "/{$data['cat_shorturl']}-nc{$data['cat_id']}.html";
		
		if($data['parent_id'] == 0)
		{
			$data['list_submenu'] = $CMS->config_parent_news->list_submenu($data['cat_id']);
			
			if($CMS->vars['translations'])
        	{
        		//Neu k phai dang mang thi chuyen ve mang
                $cat_name = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    $cat_name[$langCode] = "{$CMS->lang['parent_category']}";
                }

                $data['parent_category'] = $cat_name;
			}else
			{
				$data['parent_category'] = "{$CMS->lang['parent_category']}";
			}
		}
		else
		{
			$parent_category = $this->get_info($data['parent_id'],'cat_name');//"{$CMS->lang['sub_category']}";
    		$cat_name = @json_decode($parent_category, true);
    		$parent_category = $cat_name ? $cat_name : $parent_category;

			if($CMS->vars['translations'])
        	{
        		
        		if(!is_array($parent_category))
	            {
	                //Neu k phai dang mang thi chuyen ve mang
	                $cat_name = [];
	                foreach ($CMS->vars['translations'] as $langCode => $langName)
	                {
	                    $cat_name[$langCode] = $parent_category;
	                }

	                $data['parent_category'] = $cat_name;
	            }else
	            {
	            	$data['parent_category'] = $parent_category;
	            }
	
			}else
			{
				if(is_array($parent_category))
	            {
	            	$data['parent_category'] = $parent_category[$CMS->vars['default_language']];
	        	}

			}
		}
		// print "<pre>"; print_r($data);exit;
		// Replace the Status
		$data['cat_status_bk'] = $data['cat_status'];
		$data['cat_status'] = $CMS->lang["display_{$data['cat_status']}"];
		$data['cat_display_home'] = $CMS->lang["display_{$data['cat_display_home']}"];

		$data['description_short'] = $CMS->class->editor->substr(strip_tags(html_entity_decode($data['cat_description'])), 0, 150);

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

        $sql = "SELECT * FROM ".root_table."news_category WHERE (cat_id='{$record_id}' OR cat_name='{$record_id}' OR cat_shorturl='{$record_id}') AND cat_deleted=0  ORDER BY cat_id DESC LIMIT 1";

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
		
		$clause_search = count($CMS->vars['translations']) > 1 ? " AND {$field} LIKE '%\"{$value}\"%' " : " AND {$field} = '{$value}' "; 

		if ( $except_value )
		{
			// print "SELECT * FROM ".root_table."news_category WHERE {$field}='{$value}' AND ({$field}!='{$except_value}' OR cat_id!='{$except_value}') AND cat_deleted=0";exit;
			$DB->query("SELECT * FROM ".root_table."news_category WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND cat_id !='{$except_value}' AND cat_deleted=0");
		}
		else
		{
			$DB->query("SELECT * FROM ".root_table."news_category WHERE cat_deleted=0 {$clause_search}");
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
		$cat_name = $CMS->input["cat_name"];
		$cat_key = $CMS->input["cat_key"];
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

            	// Check ẽits
				if($this->check_exist("cat_name","{$cat_name[$langCode]}"))
				{
					$_SESSION['msg'] .= "{$CMS->lang['cat_name_exits']}"; 
					$check = false;
				}

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
            $cat_name = $CMS->input["cat_name"];
			$cat_description = $CMS->input["cat_description"];
			$cat_shorturl = $CMS->class->seo->cleanurl($cat_name);
			if($this->check_exist("cat_name","{$cat_name}"))
			{
				$_SESSION['msg'] = "{$CMS->lang['cat_name_exits']}"; return false;
			}

			$name_alert = $cat_name;
        }


        if($this->check_unique_key($cat_key) > 0) {
            $_SESSION['msg'] = "{$CMS->lang['cat_key_existed']}"; return false;
        }
		
		$cat_status = intval($CMS->input['cat_status']);
		$cat_order = intval($CMS->input['cat_order']);
		
		$parent_id = intval($CMS->input['parent_id']);
		$cat_type = $CMS->input['cat_type'] ? intval($CMS->input['cat_type']) : 1;
		$meta_title = trim($CMS->class->editor->input("meta_title"));
		$meta_description = trim($CMS->class->editor->input("meta_description"));
		$meta_keywords = trim($CMS->class->editor->input("meta_keywords"));
		
		
		//echo $parent_id;exit;
		// Check input
		// if ( ! $cat_name ) { $_SESSION['msg'] = "{$CMS->lang['incomplete_name']}"; return false; }

		// Check ẽits
		// if($this->check_exist("cat_name","{$cat_name}") == TRUE)
		// {
		// 	$_SESSION['msg'] = "{$CMS->lang['cat_name_exits']}"; return false;
		// }
		if($check == false)
		{
			return false;
		}

		// Insert data
		$DB->query("INSERT INTO ".root_table."news_category (cat_name, cat_description, cat_status, cat_time, cat_shorturl, parent_id, meta_title, meta_description, meta_keywords, cat_order, cat_type, cat_key) VALUES ('{$cat_name}', '{$cat_description}', '{$cat_status}', '".time()."', '{$cat_shorturl}', '{$parent_id}', '{$meta_title}', '{$meta_description}', '{$meta_keywords}', '{$cat_order}', '{$cat_type}', '{$cat_key}')");

        $CMS->class->cache->mdelete($this->cache_prefix);
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['added']} <b>{$name_alert}</b>")."<br />";

		// Get info
		$data = $this->get_info($cat_name);

		return $data;
	}
	

	//===========================================================================
	//  ADD
	//===========================================================================
	
	public function add_whm()
	{
		global $CMS, $DB, $member;

 		$reseller_id = intval($member['reseller_id']);
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

            	// Check ẽits
				if($this->check_exist("cat_name","{$cat_name[$langCode]}"))
				{
					$_SESSION['msg'] .= "{$CMS->lang['cat_name_exits']}"; 
					$check = false;
				}

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
            $cat_name = $CMS->input["cat_name"];
			$cat_description = $CMS->input["cat_description"];
			$cat_shorturl = $CMS->class->seo->cleanurl($cat_name);
			if($this->check_exist("cat_name","{$cat_name}"))
			{
				$_SESSION['msg'] = "{$CMS->lang['cat_name_exits']}"; return false;
			}

			$name_alert = $cat_name;
        }

		
		$cat_status = intval($CMS->input['cat_status']);
		$cat_order = intval($CMS->input['cat_order']);
		
		$parent_id = intval($CMS->input['parent_id']);
		$cat_type = $CMS->input['cat_type'] ? intval($CMS->input['cat_type']) : 1;
		$meta_title = trim($CMS->class->editor->input("meta_title"));
		$meta_description = trim($CMS->class->editor->input("meta_description"));
		$meta_keywords = trim($CMS->class->editor->input("meta_keywords"));
		 
		if($check == false)
		{
			return false;
		}

		// Insert data
		$DB->query("INSERT INTO ".root_table."news_category (cat_name, cat_description, cat_status, cat_time, cat_shorturl, parent_id, meta_title, meta_description, meta_keywords, cat_order, cat_type, reseller_id) VALUES ('{$cat_name}', '{$cat_description}', '{$cat_status}', '".time()."', '{$cat_shorturl}', '{$parent_id}', '{$meta_title}', '{$meta_description}', '{$meta_keywords}', '{$cat_order}', '{$cat_type}', '{$reseller_id}')");

        $CMS->class->cache->mdelete($this->cache_prefix);
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['added']} <b>{$name_alert}</b>")."<br />";

		// Get info
		$data = $this->get_info($cat_name);

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
		$cat_key = $CMS->input["cat_key"];
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

            	// Check ẽits
				if($this->check_exist("cat_name","{$cat_name[$langCode]}", $data['cat_id']))
				{
					$_SESSION['msg'] .= "{$CMS->lang['cat_name_exits']}"; 
					$check = false;
				}

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
            $cat_name = $CMS->input["cat_name"];
			$cat_description = $CMS->input["cat_description"];
			$cat_shorturl = $CMS->class->seo->cleanurl($cat_name);
			if($this->check_exist("cat_name","{$cat_name}",$data['cat_id']))
			{
				$_SESSION['msg'] = "{$CMS->lang['cat_name_exits']}"; return false;
			}

			$name_alert = $cat_name;
        }

        if($this->check_unique_key($cat_key) > 0) {
            $_SESSION['msg'] = "{$CMS->lang['cat_key_existed']}";
            return false;
        }
		
		$cat_order = intval($CMS->input['cat_order']);
		$cat_status = $CMS->input["cat_status"];
		$parent_id = intval($CMS->input['parent_id']);
		$cat_type = $CMS->input['cat_type'] ? intval($CMS->input['cat_type']) : 1;
		$meta_title = trim($CMS->class->editor->input("meta_title"));
		$meta_description = trim($CMS->class->editor->input("meta_description"));
		$meta_keywords = trim($CMS->class->editor->input("meta_keywords"));
		
		// Check input
		// if ( ! $cat_name ) { $_SESSION['msg'] = "{$CMS->lang['incomplete_name']}"; return false; }
		
		// Step 1: Save detail logs
		$CMS->class->logs->old_data = $data;
		// Update info
		$DB->query("UPDATE ".root_table."news_category SET cat_name='{$cat_name}', cat_description='{$cat_description}', cat_status='{$cat_status}', cat_shorturl='{$cat_shorturl}', parent_id='{$parent_id}', meta_title='{$meta_title}', meta_description='{$meta_description}', meta_keywords ='{$meta_keywords}', cat_order='{$cat_order}', cat_type='{$cat_type}', cat_key='{$cat_key}' WHERE cat_id='{$data['cat_id']}'");

        $CMS->class->cache->mdelete($this->cache_prefix);
		
		$CMS->class->logs->key = "config_parent_news_{$data['cat_id']}";
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['edited']} <b>{$name_alert}</b>")."<br />";
		
		// Get info
		$data = $this->get_info();
		// Step 2: Save detail logs
		$CMS->class->logs->key = "config_parent_news_{$data['cat_id']}";
		$CMS->class->logs->save_detail("news_category",$data['cat_id'],$data);
		
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
		if($data['cat_count'] > 0)
		{
				$_SESSION["msg"] .= "{$CMS->lang['can_not_deleted']}" ;
				// Redirect
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_parent_news&page={$CMS->input['page']}");
		
				return false;
		}
		// Update info
		$DB->query("UPDATE ".root_table."news_category SET cat_deleted=1 WHERE cat_id={$data['cat_id']}");

        $CMS->class->cache->mdelete($this->cache_prefix);
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['deleted']} <b>{$data['cat_name']}</b>")."<br />";

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_parent_news&page={$CMS->input['page']}");
		
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

				$DB->query("UPDATE ".root_table."news_category SET cat_deleted=1 WHERE cat_id={$id}");
				
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['deleted']} <b>{$data['cat_name']}</b>")."<br />";
				
				$deleted = 1;
			}
		}

        $CMS->class->cache->mdelete($this->cache_prefix);

		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['delete_failed']}";
		}

		return true;
	}
	
	//===========================================================================
	//  LOAD CATEGORY
	//===========================================================================
	
	public function load_category()
	{
		global $CMS, $DB;
		
		$sql = $DB->query("SELECT * FROM ".root_table."news_category WHERE cat_deleted=0 ORDER BY cat_name ASC");
		
		$output = "";
		$cnt = 0;
		
		while ( $cat = $DB->fetch_array() )
		{
			$output .= "\t\tnews_menu[{$cnt}] = new Array('?site=news&view=category&id={$cat['cat_id']}', '{$cat['cat_name']}');\n";
			$cnt++;
		}
		
		$CMS->global->html['news_menu'] = $output;
	}
	
	//===========================================================================
	//  LOAD LIST SUBMENU
	//===========================================================================
	
	public function list_submenu($parent_id = "", $type = 0, $line="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")
	{
		global $CMS, $DB;

		$sql = "SELECT * FROM ".root_table."news_category WHERE cat_deleted=0 AND parent_id = '{$parent_id}' ORDER BY cat_name ASC";

		$data = $DB->fetch_data($sql, $this->cache_prefix);

		$output = "";
		if($data)
		{
			foreach ($data as $cat)
			{
				if($type == 1)
				{
					// $output .= "<optgroup label=\"└-----{$cat['cat_name']}\">└-----{$cat['cat_name']}</optgroup>";
					$cat = $this->convertvalue($cat);
					$name = $CMS->vars['translations'] ? $cat['cat_name'][$CMS->vars['default_language']] : $cat['cat_name'];
					$output .= "<option value='{$cat['cat_id']}'>{$line}└-----{$name}</option>";
					$output .= $this->list_submenu($cat['cat_id'], 1, $line."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
				}
				else
				{
					$cat = $this->convertvalue($cat);
					$name = $CMS->vars['translations'] ? $cat['cat_name'][$CMS->vars['default_language']] : $cat['cat_name'];
					$output .= "{$name},";
				}
			}
			// return $output;
		}
		return $output;
	}
	
	
	//===========================================================================
	//  LOAD LIST SUBMENU
	//===========================================================================
	
	public function load_sub_cate($parent_id = "", $line="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")
	{
		global $CMS, $DB;

		$sql = "SELECT * FROM ".root_table."news_category WHERE cat_deleted=0 AND parent_id = '{$parent_id}' ORDER BY cat_order ASC";

		$data = $DB->fetch_data($sql, $this->cache_prefix);
		$count_cate = count($data);
		$output = "";
		if($count_cate > 0)
		{
			foreach ( $data as $result )
			{
                $btn_control = "";

                if($CMS->permit['config_parent_news_read'])
                {
                    $btn_control .=<<<EOF
		           <a href="{$CMS->vars['root_domain']}/?site=config_parent_news&act=edit&id={$result['cat_id']}" title""="" class="edit"><i class="fa fa-edit"></i></a>
EOF;

                }

                if($CMS->permit['config_parent_news_edit'])
                {
                    $btn_control .=<<<EOF
	        <a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=config_parent_news&act=delete&id={$result['cat_id']}');"   class="edit"><i class="fa fa-trash-o"></i></a>
EOF;

                }

				$result = $this->convertvalue($result);
				
				$output .= <<<EOF
			  <tr bgcolor="{$result['bgcolor']}">
			 	<td class="table-check">
EOF;
				if($result['cat_count'] == 0)			  
			    {
					$output .=<<<EOF
				
 					<div class="checkbox checkbox-only">
                      <input type="checkbox"  name="id_{$result['record_cnt']}" id="id_{$result['record_cnt']}" value="{$result['cat_id']}"/>
                      <label for="id_{$result['record_cnt']}"></label>
                    </div>
      			</td>
EOF;
				}
                $output .=<<<EOF
                </td>
		
                
                <td>
				 <span class='pull-left'></span><select class="form-control pull-left" name="order_{$result['cat_id']}">
				<script language="javascript">
			
					for ( var i = 1; i <= {$count_cate}; i ++ )
					{
						if ( i == {$result['cat_order']} )
						{
							document.writeln("<option name='option_"+i+"' value='"+i+"' selected>"+i+"</option>");
						}
						else
						{
							document.writeln("<option name='option_"+i+"' value='"+i+"'>"+i+"</option>");
						}
					}
			
				</script>
    </select>
    	
    </td>

EOF;

        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $output .= <<<EOF
                <td class="langTab" lang="{$langCode}">{$line}└-----{$result['cat_name_bk'][$langCode]}</td>
                <td class="langTab" lang="{$langCode}">{$result['parent_category'][$langCode]}</td>
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
                <td>{$line}└-----{$result['cat_name_bk']}</td>
                <td>{$result['parent_category']}</td>
EOF;
        }
$output .= <<<EOF
    
    <td style="text-align: center;">{$result['cat_status']}</td>  
    <td style="text-align: center;">{$result['cat_time']}</td>
    <td style="text-align: center;">{$btn_control}</td>
  </tr>
EOF;
	$output .= $this->load_sub_cate($result['cat_id'], $line."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
			}


			return $output;
		}
		return false;
	}
	
	
	
	
	//===========================================================================
	//  ARRANGE
	//===========================================================================

	public function arrange()
	{
		global $CMS, $DB;
		
		$_SESSION["msg"] .= "";

		$sql = "SELECT * FROM ".root_table."news_category WHERE cat_deleted=0 ORDER BY cat_id ASC";

        $list = $DB->fetch_data($sql, $this->cache_prefix);

		foreach ( $list as $data )
		{
			$order = intval( $CMS->input["order_{$data['cat_id']}"] );
			
			if ( $order )
			{
				if(isset($_SESSION['order_cat_home']) AND $_SESSION['order_cat_home'] == 1)
				{
					$DB->query("UPDATE ".root_table."news_category SET cat_order_home='{$order}' WHERE cat_id='{$data['cat_id']}'");
				}
				else
				{
					$DB->query("UPDATE ".root_table."news_category SET cat_order='{$order}' WHERE cat_id='{$data['cat_id']}'");
				}
			}
		}

		//Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['cat_arranged']}")."<br />";
		
		return true;
	}
	
	
	
	//===========================================================================
	//  LOAD LIST CATEGORY
	//===========================================================================

	public function load_list_cate()
	{
		global $CMS, $DB;

		$_SESSION["msg"] .= "";

		$sql = "SELECT * FROM ".root_table."news_category WHERE cat_deleted=0 AND parent_id = 0 ORDER BY cat_id ASC";

		$results = $DB->fetch_data($sql, $this->cache_prefix);

        $output = '';

		if($results)
		{
			foreach ( $results as $data )
			{
				$output .= "<option value='{$data['cat_id']}' >{$data['cat_name']}</option>";
			}
		}

		return $output;
	}
	
	
	
	//===========================================================================
	//  LOAD LIST CATEGORY
	//===========================================================================

	public function load_all_cate($cat_id=0)
	{
		global $CMS, $DB;

		$_SESSION["msg"] .= "";

		$sql = "SELECT * FROM ".root_table."news_category WHERE cat_deleted=0 AND parent_id = 0 ORDER BY cat_id ASC";

		$results = $DB->fetch_data($sql, $this->cache_prefix);

        $output = "<option value=\"0\">{$CMS->lang['parent_category']}</option>";

		if($results)
		{
			foreach ( $results as $data )
			{
				$disable = $cat_id == $data['cat_id'] ? " disabled='disabled' " : "";
				$data = $this->convertvalue($data);
				$name = $CMS->vars['translations'] ? $data['cat_name'][$CMS->vars['default_language']] : $data['cat_name'];

				$output .= "<option value=\"{$data['cat_id']}\" {$disable}>{$name}</option>";
				$output .= $this->list_submenu($data['cat_id'],1);
			}
		}

		return $output;
	}
    
    //===========================================================================
	//  LOAD Count sub category
	//===========================================================================

	public function load_count_cate($parent_id = "")
	{
		global $CMS, $DB;
		
		$_SESSION["msg"] .= "";

		$sql = $DB->query("SELECT * FROM ".root_table."news_category WHERE cat_deleted=0 AND parent_id = '{$parent_id}' ORDER BY cat_id ASC");
		return $DB->num_rows($sql);

	}
	
    
	
	//===========================================================================
	//  LOAD Count sub category
	//===========================================================================

	public function load_cate_parent()
	{
		global $CMS, $DB;
		
		$_SESSION["msg"] .= "";

		$sql = $DB->query("SELECT * FROM ".root_table."news_category WHERE cat_deleted=0 AND parent_id = 0 ORDER BY cat_id ASC");
		if( $DB->num_rows($sql) > 0)
        {
        	while($data = $DB->fetch_array($sql))
            {
            	$output .=<<<EOF
             	<option value="{$data['cat_id']}">{$data['cat_name']}</option>   
                
EOF;
            }
            return $output;
        }

	}
	
	public function get_link_by_key($key)
	{
		global $CMS, $DB;

		$sql = $DB->query("SELECT cat_shorturl, cat_id FROM ".root_table."news_category WHERE cat_deleted = 0 AND cat_key = '{$key}' ");
		if($DB->num_rows($sql) > 0)
		{
			$result = $DB->fetch_array($sql);

			$url = "{$CMS->vars['root_domain']}/danh-muc/{$result['cat_shorturl']}/{$result['cat_id']}.html";
		}else
		{
			$url = "#";
		}

		return $url;
	}

	public function check_unique_key($key = '', $id = 0)
    {
        global $CMS, $DB;

        $id *= 1;
        $key = trim($key);

        if(!$key) return 0;

        $sql = "SELECT COUNT(0) cnt FROM " . root_table . "news_category WHERE cat_deleted = 0 AND cat_key = '{$key}' AND cat_id != '{$id}' AND cat_status = 1";

        $rs = $DB->fetch_data($sql, 'news_category');

        return $rs && isset($rs[0]) ? $rs[0]['cnt'] * 1 : 0;
    }

    public function get_info_by_key($key)
    {
        global $DB;

        $sql = "SELECT * FROM " . root_table . "news_category WHERE cat_deleted = 0 AND cat_key = '{$key}'  AND cat_status = 1 ORDER BY cat_id DESC LIMIT 1";

        $rs = $DB->fetch_data($sql, 'news_category');

        return \lib\input::arrayValue($rs, 0);
    }
}

?>