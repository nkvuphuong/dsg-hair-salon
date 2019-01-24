<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->config_news = new class_config_news;

class class_config_news {

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
			$this->html = $CMS->class->template->load_template("skin_config_news");		
		}
	}
	
	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("cat_time,cat_id,cat_name,cat_status,parent_id");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "cat_id";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "desc";
		
		// SQL Condition
		$this->sql_add .= " cat_deleted=0 AND ";

		// Create SQL Query for listing Data
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."news_category WHERE {$this->sql_add} 1=1 AND parent_id > 0 ORDER BY {$default_field} {$default_order}");
	}
	
	public function html()
	{
		global $CMS, $DB, $member;
		
		$this->loadhtml();
		
		// Display Header
		$output .= $this->html->header();
		
		if ( $DB->num_rows( $CMS->config_news->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->config_news->sql_query ) )
			{
				// Convert info
				$result = $CMS->config_news->convertvalue($result);
				
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
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_news");
		}
		
		return $output;
	}

	public function data()
	{
		global $CMS, $DB;
		
		$sql = $DB->query("SELECT * FROM ".root_table."news_category WHERE cat_deleted=0");
		
		$output = "";
		
		while ( $cat = $DB->fetch_array() )
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
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_config_news_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_config_news_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";

			// Check permission to Delete
			if ( $CMS->permit["config_news_delete"] == true )
			{
				$data .= "<option value='delete_all'>{$CMS->lang['action_delete']}</option>";
				$this->control = 1;
			}
			
			$data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

			$CMS->class->cache->save("user_{$member['user_id']}_config_news_controller_{$CMS->vars['default_language']}", $data);
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
		$data['cat_shorturl_bk'] = $data['cat_shorturl'];
		$data['cat_shorturl'] = ($CMS->vars['is_rewrite'] == 1 && $data['cat_shorturl']) ? "loai-tin/{$data['cat_shorturl']}" : "?site=news&view=category&id={$data['cat_id']}";
		
		// Convert Register to GMT
		$data['cat_time'] = $CMS->class->date->date_format( $data['cat_time'], 1 );

		// Check permission to read Info
		if ( $CMS->permit["config_news_read"] == true )
		{
			$data['cat_name'] = "<a href='{$CMS->vars['root_domain']}/?site=config_news&act=show&id={$data['cat_id']}'>{$data['cat_name']}</a>";
		}

		$data['cat_url'] = "{$CMS->vars['root_domain']}/danh-muc/{$data['cat_shorturl_bk']}/{$data['cat_id']}.html";
		
		// Replace the Status
		$data['cat_status'] = $CMS->lang["display_{$data['cat_status']}"];

		// Parent CAtegory
		$data['parent_id_bk'] = $CMS->config_parent_news->get_info($data['parent_id'], "cat_name");
		// Display home
		
		
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

		$sql = "SELECT * FROM ".root_table."news_category WHERE (cat_id='{$record_id}' OR cat_name='{$record_id}' OR cat_shorturl='{$record_id}') AND cat_deleted=0 ORDER BY cat_id DESC LIMIT 1";

        $data = $DB->fetch_data($sql, $this->cache_prefix)[0];

        if( $field_name )
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
			$DB->query("SELECT * FROM ".root_table."news_category WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND cat_deleted=0");
		}
		else
		{
			$DB->query("SELECT * FROM ".root_table."news_category WHERE {$field}='{$value}' AND cat_deleted=0");
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
		
		$cat_shorturl = $CMS->class->seo->cleanurl($cat_name);
		
		$cat_status = $CMS->input["cat_status"];
		$parent_id = $CMS->input["parent_id"];
		// Check input
		if ( ! $cat_name ) { $CMS->errormsg = "{$CMS->lang['incomplete_name']}"; return false; }

		// Insert data
		$DB->query("INSERT INTO ".root_table."news_category (cat_name, cat_description, cat_status, cat_time, cat_shorturl,parent_id) VALUES ('{$cat_name}', '{$cat_description}', '{$cat_status}', '".time()."', '{$cat_shorturl}', '{$parent_id}')");
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['added']} <b>{$cat_name}</b>")."<br />";

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
		$cat_description = $CMS->input["cat_description"];
		
		$cat_shorturl = $CMS->class->seo->cleanurl($cat_name);
		
		$cat_status = $CMS->input["cat_status"];
		$parent_id = $CMS->input['parent_id'];
		// Check input
		if ( ! $cat_name ) { $CMS->errormsg = "{$CMS->lang['incomplete_name']}"; return false; }

		// Update info
		$DB->query("UPDATE ".root_table."news_category SET cat_name='{$cat_name}', cat_description='{$cat_description}', cat_status='{$cat_status}', cat_shorturl='{$cat_shorturl}', parent_id = '{$parent_id}' WHERE cat_id='{$data['cat_id']}'");
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['edited']} <b>{$cat_name}</b>")."<br />";
		
		// Get info
		$data = $this->get_info();
		
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
		/*if($data['cat_count'] > 0)
		{
				$_SESSION["msg"] .= "{$CMS->lang['can_not_deleted']}" ;
				// Redirect
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_parent_news&page={$CMS->input['page']}");
		
				return false;
		}*/
		// Update info
		$DB->query("UPDATE ".root_table."news_category SET cat_deleted=1 WHERE cat_id={$data['cat_id']}");
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['deleted']} <b>{$data['cat_name']}</b>")."<br />";

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_news&page={$CMS->input['page']}");
		
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
		
		$sql = $DB->query("SELECT * FROM ".root_table."news_category WHERE cat_deleted=0 AND cat_status=1 ORDER BY cat_name ASC");
		
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
	//  LOAD PARENT CATEGORY
	//===========================================================================
	
	public function load_parent_category()
	{
		global $CMS, $DB;
		
		$sql = $DB->query("SELECT * FROM ".root_table."news_category WHERE cat_deleted=0 AND cat_status=1 AND parent_id = 0 ORDER BY cat_name DESC");
		
		//$output = "";
		$cnt = 0;
		if($DB->num_rows($sql))
		{
			while ( $cat = $DB->fetch_array($sql) )
			{
		
				$output .= "<option value=\"{$cat['cat_id']}\">{$cat['cat_name']}</option>";
		
			}
		}
	
		$CMS->global->html['parent_cate_news'] = $output;
	}
	
	//===========================================================================
	//  INFO
	//===========================================================================
	
	public function get_info_cate( $record_id = "", $field_name = "" )
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
		
		$sql = $DB->query("SELECT * FROM ".root_table."news_category WHERE cat_id = '{$record_id}' AND cat_deleted=0 ORDER BY cat_id DESC LIMIT 1");

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
	
	



	/*******************************************NEW FUNCTION*********************************************************/

	public function html_news_category($type="")
	{
		global $CMS, $DB;

		$output = "";
		$sql = $DB->query("SELECT * FROM ".root_table."news_category WHERE cat_deleted = 0 AND cat_status = 1 AND cat_display_home = 1 ORDER BY cat_order ASC LIMIT 6");

		if($DB->num_rows($sql) > 0 )
		{
			$i = 0;
			while ($result= $DB->fetch_array($sql)) 
			{
				// print "<pre>";
				// print_r($result);exit;
				if($type == "option")
				{
					$output .=<<<EOF
					<option value="{$result['cat_id']}" type="news" >{$result['cat_name']}</option>
EOF;

				}else
				{
					// $active = $i ==  0 ? "class = 'active'" : "";
					$output .=<<<EOF
					<li><a {$active} type="news" value="{$result['cat_id']}" title="{$result['cat_name']}">{$result['cat_name']}</a></li>
EOF;

				$i++;
				}
				
			}
		}

		return $output;
	}

}

?>