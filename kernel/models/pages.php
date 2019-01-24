<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->pages = new class_pagess;

class class_pagess {

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
	 * @param $pages_cnt
	 *		Count of pages for rearrange
	 */
	
	public $pages_cnt = 0;
	
	/**
	 * @param $previous_order
	 *		The templates
	 */
	 
	public $previous_order = 0;
	
	/**
	 * @param $parent_select
	 *		HTML of Parent Categories
	 */
	
	public $parent_select = "";

	public $cache_prefix = 'pages';

	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	
	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			$this->html = $CMS->class->template->load_template("skin_pages");		
		}
	}
	
	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("pages_time,pages_name,pages_id");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "pages_order";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "ASC";
		
		// SQL Condition
		$this->sql_add .= " pages_deleted=0 AND ";	
		
		// Create SQL Query for listing Data'
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."pages WHERE parent_id='{$parent_id}' AND {$this->sql_add} 1=1 ORDER BY {$default_field} {$default_order}", 20);
		
		$this->pages_cnt = $DB->num_rows( $this->sql_query );
	}

	public function html()
	{
		global $CMS, $DB, $member;

		$this->loadhtml();

		// Display Header
		$output .= $this->html->pages_header();
		
		if ( $DB->num_rows( $CMS->pages->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->pages->sql_query ) )
			{
				// Convert info
				$result = $CMS->pages->convertvalue($result);
				
				// Display Middle
				$output .= $this->html->pages_middle($result);
			}
		}
		else
		{
			// Display No data
			$output .= $this->html->pages_none();

			// No data
			$CMS->is_error = 1;
		}
		
		// Display
		$output .= $this->html->pages_footer();
		
		// If the pages is giving no data
		if ( $CMS->input['pages'] > 1 && $CMS->is_error == 1)
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=pages");
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
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_pages_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_pages_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";
			
			$data .= "<option value=''>{$CMS->lang['select_action']}</option>";
			
			// Check permission to Delete
			if ( $CMS->permit["pages_delete"] == true )
			{
				$data .= "<option value='delete_all'>{$CMS->lang['pages_action_delete']}</option>";
				$this->control = 1;
			}
			
			// Check permission to Arrange
			if ( $CMS->permit["pages_arrange"] == true )
			{
				$data .= "<option value='arrange'>{$CMS->lang['pages_action_arrange']}</option>";
				$this->control = 1;
			}

			$CMS->class->cache->save("user_{$member['user_id']}_pages_controller_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}
		
		if ( $CMS->vars['action_controller'] OR $CMS->permit["pages_search"] == 1 )
		{
			$this->action_control = $this->html->pages_control();
		}
		
		//-----------------------------------------------------------
		// Load Sub Models
		//-----------------------------------------------------------


	}

	//===========================================================================
	//  DATA
	//===========================================================================

	public function defaultvalue($data)
	{
		global $CMS;

		return $data;
	}
	
	public function convertvalue($data)
	{
		global $CMS, $DB;
	
		// Replace search content
		$data = $CMS->class->search->convertvalue($data);
		
		// Rewrite URL
		$data['pages_shorturl'] = ($CMS->vars['is_rewrite'] == 1 && $data['pages_shorturl']) ? "trang/{$data['pages_shorturl']}.html" : "?site=page&id={$data['pages_id']}";
		
		$data['parent_id'] = $this->get_info($data['parent_id'], "pages_id");
		
		// Convert Register to GMT
		$data['pages_time_bk'] = $data['pages_time'];
		$data['pages_time'] = $CMS->class->date->date_format( $data['pages_time'], 1 );
		$data['pages_time_update'] = $data['pages_time_update'] ? $CMS->class->date->date_format( $data['pages_time_update'], 1 ) : "<i>N/A</i>";

		// Check permission to read Info
		if ( $CMS->permit["pages_read"] == true )
		{
			$data['pages_name'] = "<a href='{$CMS->vars['root_domain']}/?site=pages&act=show&id={$data['pages_id']}'>{$data['pages_name']}</a>";
		}
		
		
		// Page key
		//$data['pages_key'] ? "<a href='{$CMS->vars['root_domain']}/?site=page&name={$data['pages_key']}'>{$CMS->vars['root_domain']}/?site=page&name={$data['pages_key']}</a>" : 
		
		$data['pages_url'] = "<a href='{$CMS->vars['root_domain']}/{$data['pages_shorturl']}'>{$CMS->vars['root_domain']}/{$data['pages_shorturl']}</a>";
		
		// Replace the Status

		$data['pos_id'] = $CMS->position->get_info($data['pos_id'],"pos_name");
		$data['pages_module_bk'] = $CMS->lang["pages_module_{$data['pages_module']}"];

		// Order
		$data['pages_order'] = $data['pages_order'] <= $this->pages_cnt ? $data['pages_order'] : 1;
        
        if ( $data['pages_order'] <= $this->previous_order )
        {
        	$data['pages_order'] = $this->previous_order+1;
        }
		
		$this->previous_order++;
		
		// Bgcolor
		$data['bgcolor'] = $this->record_cnt % 2 != 0 ? "#FFFFFF" : "#F6F6F6";
		
		// User
		$data['user_name'] = $CMS->user->get_info($data['user_id'],"user_display_name");
		
		// Count
		$data['record_cnt'] = $this->record_cnt;

		$this->record_cnt++;
		
		return $data;
	}
	
	public function searchvalue($data)
	{
		global $CMS, $DB;

		// Convert Register to GMT
		// $data['pages_time'] = $CMS->class->date->date_format( $data['pages_time'], 0 );
		// $data['pos_id'] = $CMS->lang["pos_id_{$data['pos_id']}"];
		return $data;
	}
	
	//===========================================================================
	//  INFO
	//===========================================================================
	
	public function get_info( $record_id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "pages" )
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

		$sql = "SELECT * FROM ".root_table."pages WHERE (pages_id='{$record_id}' OR pages_name='{$record_id}' OR pages_key='{$record_id}' OR pages_shorturl='{$record_id}') AND pages_deleted=0 ORDER BY pages_id DESC LIMIT 1";

		$data = $DB->fetch_data($sql, $this->cache_prefix)[0];

		if ($data)
		{
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


	//===========================================================================
	//  INFO
	//===========================================================================
	
	public function whm_get_info( $record_id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "pages" )
		{
			$record_id = intval($CMS->input['id']);
		}

		if(intval($member['reseller_id']) > 0)
		{
			$sql_add = " reseller_id = '{$member['reseller_id']}' AND ";
		}
		// Clear record
		$record_id = strip_tags($record_id);
		
		// Check record		
		if ( ! $record_id )
		{
			return false;
		}

		$sql = "SELECT * FROM ".root_table."pages WHERE (pages_id='{$record_id}' OR pages_name='{$record_id}' OR pages_key='{$record_id}' OR pages_shorturl='{$record_id}') AND pages_deleted=0  AND {$sql_add} 1=1 ORDER BY pages_id DESC LIMIT 1";

		$data = $DB->fetch_data($sql, $this->cache_prefix)[0];

		if ($data)
		{
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
	
	public function check_exist( $field, $value = "", $except_value = "" )
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

		$sql = "SELECT count(pages_id) cnt FROM ".root_table."pages WHERE {$sql_add} {$field}='{$value}' AND pages_deleted=0";

        $result = $DB->fetch_data($sql, $this->cache_prefix);

        return $result[0]['cnt'];
	}

	//===========================================================================
	//  ADD
	//===========================================================================
	
	public function add()
	{
		global $CMS, $DB, $member;

		// User input
		$pages_name = $CMS->input["pages_name"];
		$pages_content = $CMS->class->editor->input("pages_content");

		$pos_id = intval($CMS->input['pos_id']);
		$pages_module = intval($CMS->input['pages_module']);
		$pages_key = $CMS->input["pages_key"];
		$pages_shorturl = $CMS->class->seo->cleanurl($CMS->input["pages_name"]);
		
		$meta_title = trim($CMS->class->editor->input("meta_title"));
		$meta_description = trim($CMS->class->editor->input("meta_description"));
		$meta_keywords = trim($CMS->class->editor->input("meta_keywords"));
		$pages_time = time();
		// Check input
		if ( ! $pages_name ) { $CMS->errormsg = "{$CMS->lang['pages_incomplete_name']}"; return false; }

		if ( $this->check_exist("pages_key", $pages_key) == true ) { $CMS->errormsg = "{$CMS->lang['pages_key_duplicate']}"; return false; }
		// Insert data
		$DB->query("INSERT INTO ".root_table."pages (pages_name, parent_id, pages_content, pages_key,  pos_id, pages_module, pages_time, pages_time_update, user_id, pages_shorturl,meta_title ,meta_keywords, meta_description) VALUES ('{$pages_name}', '{$parent_id}', '{$pages_content}', '{$pages_key}', '{$pos_id}', '{$pages_module}' ,'{$pages_time}', '{$pages_time}', '{$member['user_id']}', '{$pages_shorturl}','{$meta_title}','{$meta_keywords}','{$meta_description}')");

        // Delete cache
        $CMS->class->cache->mdelete($this->cache_prefix);
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['pages_added']} <b>{$pages_name}</b>")."<br />";

		// Get info
		$pages = $this->get_info($pages_name);
		
		// Update Module ID
		$CMS->attach->update("pages", $pages['pages_id']);

		return $pages;
	}
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		// Get info
		$pages = $this->get_info();

		// User input
		$pages_name = $CMS->input["pages_name"];
		$pages_content = $CMS->class->editor->input("pages_content");

		$pos_id = intval($CMS->input['pos_id']);
		$pages_module = intval($CMS->input['pages_module']);
		$pages_key = $CMS->input["pages_key"];
		$pages_shorturl = $CMS->class->seo->cleanurl($CMS->input["pages_name"]);
		
		
		$meta_title = trim($CMS->class->editor->input("meta_title"));
		$meta_description = trim($CMS->class->editor->input("meta_description"));
		$meta_keywords = trim($CMS->class->editor->input("meta_keywords"));
		
		
		// Check input
		if ( ! $pages_name ) { $CMS->errormsg = "{$CMS->lang['pages_incomplete_name']}"; return false; }

		
		if ( $this->check_exist("pages_key", $pages_key, $pages['pages_key']) == true ) { $CMS->errormsg = "{$CMS->lang['pages_key_duplicate']}"; return false; }
		
		// Update info
		$DB->query("UPDATE ".root_table."pages SET pages_name='{$pages_name}',  pages_content='{$pages_content}', pages_key='{$pages_key}', pos_id='{$pos_id}', pages_module = '{$pages_module}', pages_time_update='".time()."', pages_shorturl='{$pages_shorturl}', meta_title = '{$meta_title}', meta_keywords = '{$meta_keywords}', meta_description = '{$meta_description}' WHERE pages_id='{$pages['pages_id']}'");

        // Delete cache
        $CMS->class->cache->mdelete($this->cache_prefix);
		
		$CMS->class->logs->key = "page_{$pages['page_id']}";
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['pages_edited']} <b>{$pages_name}</b>")."<br />";
		
		// Get info
		$pages = $this->get_info();

			// Step 2: Save detail logs
		$CMS->class->logs->key = "page_{$pages['page_id']}";
		$CMS->class->logs->save_detail("page",$pages['page_id'],$pages);


		return $pages;
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
		
		// Update info
		$DB->query("UPDATE ".root_table."pages SET pages_deleted=1 WHERE pages_id={$data['pages_id']}");

        // Delete cache
        $CMS->class->cache->mdelete($this->cache_prefix);
        $name = json_decode($data['pages_name'], 1);
        $page_name = is_array($name) ? $name[$CMS->vars['default_language']] : $data['pages_name'];
		// Create log
		$CMS->class->logs->insert("{$CMS->lang['pages_deleted']} <b>{$data['pages_name']}</b>")."<br />";
		
		// // Delete cache
		$CMS->class->cache->mdelete("page");

		// // Redirect
		// $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=pages&pages={$CMS->input['pages']}");
		
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

				$DB->query("UPDATE ".root_table."pages SET pages_deleted=1 WHERE pages_id={$id}");
				
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['pages_deleted']} <b>{$data['pages_name']}</b>")."<br />";
				
				$deleted = 1;
			}
		}
		
		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['pages_delete_failed']}";
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
		$CMS->class->search->table_name = "pages";
		$CMS->class->search->fields_type = array("pages_time" => "time");
		
		// Output
		$data = $CMS->class->search->get_info();
		// print $data;exit;
		// Update SQL Query
		$this->sql_add .= $data;
		
		// Get List
		$this->listing();
	}
	
	//===========================================================================
	//  GET MENU
	//===========================================================================
	
	public function getmenu()
	{
		global $CMS, $DB;

		$DB->query("SELECT * FROM ".root_table."pages WHERE pages_deleted=0");
		
		$output = "";
		
		while ( $data = $DB->fetch_array() )
		{
			$output .= $CMS->global->pages_menu($data);
		}
		
		return $output;
	}
	
	//===========================================================================
	//  ARRANGE
	//===========================================================================

	public function arrange()
	{
		global $CMS, $DB;
		
		$_SESSION["msg"] .= "";

		$sql = $DB->query("SELECT * FROM ".root_table."pages WHERE pages_deleted=0 ORDER BY pages_id ASC");
		
		while ( $data = $DB->fetch_array( $sql ) )
		{
			$order = intval( $CMS->input["order_{$data['pages_id']}"] );
			
			if ( $order )
			{
				$DB->query("UPDATE ".root_table."pages SET pages_order='{$order}' WHERE pages_id='{$data['pages_id']}'");
			}
		}

        // Delete cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['pages_arranged']}")."<br />";

		return true;
	}
	
	//===========================================================================
	//  Get full group
	//===========================================================================
	
	public function parentgroup_data( $parent_id, $parent_html )
	{
		global $CMS, $DB;

		$id = intval($CMS->input['id']);

		if ( $parent_id == $id AND $id)
		{
			return false;
		}

		$sql = $DB->query("SELECT * FROM ".root_table."pages WHERE parent_id='{$parent_id}' AND pages_deleted=0");
		
		if ( $DB->num_rows($sql) > 0 )
		{
			while ( $cat = $DB->fetch_array($sql) )
			{
				if ( $cat['pages_id'] != $id )
				{
					$output .= "<option value='{$cat['pages_id']}'>{$parent_html} {$cat['pages_name']}</option>";
				}

				$output .= $this->parentgroup_data( $cat['pages_id'], $parent_html."--" );
			}
		}
				
		return $output;
	}
	
	public function parentgroup()
	{
		global $CMS, $DB;

		$output = "";
		
		$output .= "<option value='0'>-----------------------</option>";
		
		$output .= $this->parentgroup_data(0, "");
		
		$this->parent_select = $output;
	}
	
	//===========================================================================
	//  Get group URL
	//===========================================================================
	
	public function parenturl_data( $parent_id )
	{
		global $CMS, $DB;

		$sql = $DB->query("SELECT * FROM ".root_table."pages WHERE pages_id='{$parent_id}' AND pages_deleted=0");
		
		if ( $DB->num_rows( $sql ) > 0 )
		{
			$cat = $DB->fetch_array( $sql );
			
			$output .= $this->parenturl_data( $cat['parent_id'] );

			$output .= " -> <a href='{$CMS->vars['root_domain']}/?site=pages&parent_id={$cat['pages_id']}'>{$cat['pages_name']}</a> ";
		}
		
		return $output;
	}
	
	public function parenturl()
	{
		global $CMS, $DB;

		$output = "";

		$id = intval( $CMS->input['parent_id'] ) ? intval( $CMS->input['parent_id'] ) : intval( $CMS->input['id'] );

		$output .= $this->parenturl_data( $id );

		$this->parent_url = $output;
	}
	
	public function sellListing() {
		global $CMS,$DB,$member;	
		// Create SQL Query for listing Data
		list($this->show_page, $this->sql_query)=$CMS->class->page->create("SELECT * FROM `".root_table."pages` WHERE `pages_deleted`=0 AND `user_id`={$member['cus_id']} ORDER BY `pages_id` DESC");
		if ($DB->num_rows($this->sql_query)!=2) {
			$DB->query("UPDATE `".root_table."pages` SET `pages_deleted`=1 WHERE `user_id`={$member['cus_id']}");
			$DB->query("INSERT INTO `".root_table."pages` (`pages_key`,`pages_name`,`pages_content`,`user_id`) VALUES('privacy_policy_{$member['cus_id']}','PRIVACY POLICY','','{$member['cus_id']}')");
			$DB->query("INSERT INTO `".root_table."pages` (`pages_key`,`pages_name`,`pages_content`,`user_id`) VALUES('terms_of_service_{$member['cus_id']}','TERMS OF SERVICE','','{$member['cus_id']}')");
			die("<script>location.reload();</script>");
		}
		// Display Header
		$this->loadhtml();
		$out.=$this->html->head();
		if ($DB->num_rows($CMS->pages->sql_query)>0) {
			while ($result=$DB->fetch_array($CMS->pages->sql_query)) {
				$out.= $this->html->mid($this->convertvalue($result));
			}
		} else {
			$out.= $this->html->none();
		}
		$out.=$this->html->foot();
		return $out;
	}
	public function sellEdit($key=null) {
		if (!is_null($key)) {
			global $CMS, $DB, $member;
			$pages_name=$CMS->input["pages_name"];
			$pages_content=substr($CMS->class->editor->input("pages_content"),0,1000);
			if (empty($pages_name)||(bool)preg_match("/^[0-9a-zA-Z-_]+$/",$pages_name)) {
				$CMS->errormsg = "{$CMS->lang['pages_incomplete_name']}";
				return false; 
			}
			$DB->query("UPDATE ".root_table."pages SET pages_name='{$pages_name}', pages_content='{$pages_content}',`pages_time_update`='".time()."' WHERE `pages_key`='{$key}' AND `user_id`={$member['cus_id']}");
			$_SESSION["msg"].=$CMS->lang['pages_update_success'];
			return true;
		}
		return false;
	}

	function addajax()
	{
		global $CMS, $DB;

		$page_name = $_POST['page_name'];
		$htmlEditorYour = $_POST['htmlEditorYour'];
		$use_layouts = intval($CMS->input['use_layouts']);
		// clear input
		$list_name = [];
		$list_shorturl = [];
		foreach ($page_name as $key => $value) 
		{
			$list_name[$key] = $CMS->class->editor->input($value, "text");
			$key_shorturl = $CMS->class->seo->cleanurl($value);
			$list_shorturl[$key] = $key_shorturl;
			if($this->checkExistUrl($key_shorturl))
			{
				print json_encode(array("status" => "error", "msg" => str_replace("%lang%", $key, $CMS->lang['title_exist_url']) , "data" => []), JSON_UNESCAPED_UNICODE);exit;
			}
		}

		$list_content = [];
		foreach ($htmlEditorYour as $key => $value) 
		{
			$str_rep = preg_replace( "/\r|\n/", "", $value);
			$list_content[$key] = $CMS->class->filter->clean_key(str_replace("'", "&#39;", $str_rep),0);
		}
		$pages_time = time();
		$user_id = $member['user_id'];
		// Json data
		$list_name = json_encode($list_name, JSON_UNESCAPED_UNICODE);
		$list_shorturl = json_encode($list_shorturl, JSON_UNESCAPED_UNICODE);
		$list_content = json_encode($list_content, JSON_UNESCAPED_UNICODE);

		$DB->query("INSERT INTO ".root_table."pages (pages_name, pages_content, user_id, pages_time, pages_time_update, pages_shorturl, use_layouts) VALUES ('{$list_name}', '{$list_content}', '{$user_id}', '{$pages_time}', '{$pages_time}', '{$list_shorturl}', '{$use_layouts}')");

        // Delete cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		$pages_id = $DB->last_insert_id();
		if($pages_id)
		{
			$data = $this->get_info($pages_id);
			$CMS->class->logs->key = "pages_{$pages_id}";
			$CMS->class->logs->insert("{$CMS->lang['add_page_html_successful']} HTML ID#{$pages_id}")."<br />";
			$data['pages_name'] = json_decode($data['pages_name'], 1);
			$data['pages_content'] = json_decode($data['pages_content'], 1);
			$data['pages_shorturl'] = json_decode($data['pages_shorturl'], 1);
			return $data;
		}else
		{
			return false;
		}
	}


	function whm_addajax()
	{
		global $CMS, $DB, $member;

		$page_name = $_POST['page_name'];
		$htmlEditorYour = $_POST['htmlEditorYour'];
		$use_layouts = intval($CMS->input['use_layouts']);
		$reseller_id = intval($member['reseller_id']);
		// clear input
		$list_name = [];
		$list_shorturl = [];
		foreach ($page_name as $key => $value) 
		{
			$list_name[$key] = $CMS->class->editor->input($value, "text");
			$key_shorturl = $CMS->class->seo->cleanurl($value);
			$list_shorturl[$key] = $key_shorturl;
			if($this->whm_checkExistUrl($key_shorturl))
			{
				print json_encode(array("status" => "error", "msg" => str_replace("%lang%", $key, $CMS->lang['title_exist_url']) , "data" => []), JSON_UNESCAPED_UNICODE);exit;
			}
		}

		$list_content = [];
		foreach ($htmlEditorYour as $key => $value) 
		{
			$str_rep = preg_replace( "/\r|\n/", "", $value);
			$list_content[$key] = $CMS->class->filter->clean_key(str_replace("'", "&#39;", $str_rep),0);
		}
		$pages_time = time();
		$user_id = $member['user_id'];
		// Json data
		$list_name = json_encode($list_name, JSON_UNESCAPED_UNICODE);
		$list_shorturl = json_encode($list_shorturl, JSON_UNESCAPED_UNICODE);
		$list_content = json_encode($list_content, JSON_UNESCAPED_UNICODE);

		$DB->query("INSERT INTO ".root_table."pages (pages_name, pages_content, user_id, pages_time, pages_time_update, pages_shorturl, use_layouts, reseller_id) VALUES ('{$list_name}', '{$list_content}', '{$user_id}', '{$pages_time}', '{$pages_time}', '{$list_shorturl}', '{$use_layouts}', '{$reseller_id}')");

        // Delete cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		$pages_id = $DB->last_insert_id();
		if($pages_id)
		{
			$data = $this->get_info($pages_id);
			$CMS->class->logs->key = "pages_{$pages_id}";
			$CMS->class->logs->insert("{$CMS->lang['add_page_html_successful']} HTML ID#{$pages_id}")."<br />";
			$data['pages_name'] = json_decode($data['pages_name'], 1);
			$data['pages_content'] = json_decode($data['pages_content'], 1);
			$data['pages_shorturl'] = json_decode($data['pages_shorturl'], 1);
			return $data;
		}else
		{
			return false;
		}
	}

	function editajax()
	{
		global $CMS, $DB;

		$pages_id = intval($CMS->input['pages_id']);
		if($pages_id)
		{
			// save logs
			$page_bk = $this->get_info($pages_id);
			$CMS->class->logs->old_data = $page_bk;
			$CMS->class->logs->key = "pages_{$page_bk['pages_id']}";

			$page_name = $_POST['page_name'];
			$htmlEditorYour = $_POST['htmlEditorYour'];
			$use_layouts = intval($CMS->input['use_layouts']);
			// clear input
			$list_name = [];
			$list_shorturl = [];
			foreach ($page_name as $key => $value) 
			{
				$list_name[$key] = $CMS->class->editor->input($value, "text");
				$key_shorturl = $CMS->class->seo->cleanurl($value);
				$list_shorturl[$key] = $key_shorturl;
				if($this->checkExistUrl($key_shorturl, $pages_id))
				{
					print json_encode(array("status" => "error", "msg" => str_replace("%lang%", $key, $CMS->lang['title_exist_url']) , "data" => []), JSON_UNESCAPED_UNICODE);exit;
				}
			}

			$list_content = [];
			foreach ($htmlEditorYour as $key => $value) 
			{
				$str_rep = preg_replace( "/\r|\n/", "", $value);
				$list_content[$key] = $CMS->class->filter->clean_key(str_replace("'", "&#39;", $str_rep),0);
			}

			$pages_time = time();
			$user_id = $member['user_id'];
			// Json data
			$list_name = json_encode($list_name, JSON_UNESCAPED_UNICODE);
			$list_shorturl = json_encode($list_shorturl, JSON_UNESCAPED_UNICODE);
			$list_content = json_encode($list_content, JSON_UNESCAPED_UNICODE);
			
			$DB->query("UPDATE ".root_table."pages SET pages_name='{$list_name}', pages_content='{$list_content}', user_id='{$user_id}', pages_time_update='{$pages_time}', pages_shorturl='{$list_shorturl}', use_layouts='{$use_layouts}' WHERE pages_id='{$pages_id}'");
			$CMS->class->logs->insert("{$CMS->lang['edited_page_html_successful']} HTML ID#{$pages_id}")."<br />";
            // Delete cache
            $CMS->class->cache->mdelete($this->cache_prefix);
			$data = $this->get_info($pages_id);
			$CMS->class->logs->key = "pages_{$pages_id}";
			$CMS->class->logs->save_detail("pages",$pages_id,$data);

			$data['pages_name'] = json_decode($data['pages_name'], 1);
			$data['pages_content'] = json_decode($data['pages_content'], 1);
			$data['pages_shorturl'] = json_decode($data['pages_shorturl'], 1);
			return $data;
		}else
		{
			return false;
		}
	}

	function whm_editajax()
	{
		global $CMS, $DB, $member;

		$pages_id = intval($CMS->input['pages_id']);
		$reseller_id = intval($member['reseller_id']);
		if($pages_id)
		{
			// save logs
			$page_bk = $this->get_info($pages_id);
			$CMS->class->logs->old_data = $page_bk;
			$CMS->class->logs->key = "pages_{$page_bk['pages_id']}";

			$page_name = $_POST['page_name'];
			$htmlEditorYour = $_POST['htmlEditorYour'];
			$use_layouts = intval($CMS->input['use_layouts']);
			// clear input
			$list_name = [];
			$list_shorturl = [];
			foreach ($page_name as $key => $value) 
			{
				$list_name[$key] = $CMS->class->editor->input($value, "text");
				$key_shorturl = $CMS->class->seo->cleanurl($value);
				$list_shorturl[$key] = $key_shorturl;
				if($this->whm_checkExistUrl($key_shorturl, $pages_id))
				{
					print json_encode(array("status" => "error", "msg" => str_replace("%lang%", $key, $CMS->lang['title_exist_url']) , "data" => []), JSON_UNESCAPED_UNICODE);exit;
				}
			}

			$list_content = [];
			foreach ($htmlEditorYour as $key => $value) 
			{
				$str_rep = preg_replace( "/\r|\n/", "", $value);
				$list_content[$key] = $CMS->class->filter->clean_key(str_replace("'", "&#39;", $str_rep),0);
			}

			$pages_time = time();
			$user_id = $member['user_id'];
			// Json data
			$list_name = json_encode($list_name, JSON_UNESCAPED_UNICODE);
			$list_shorturl = json_encode($list_shorturl, JSON_UNESCAPED_UNICODE);
			$list_content = json_encode($list_content, JSON_UNESCAPED_UNICODE);
			
			$DB->query("UPDATE ".root_table."pages SET pages_name='{$list_name}', pages_content='{$list_content}', user_id='{$user_id}', pages_time_update='{$pages_time}', pages_shorturl='{$list_shorturl}', use_layouts='{$use_layouts}' WHERE pages_id='{$pages_id}' AND reseller_id = '{$reseller_id}' ");
			$CMS->class->logs->insert("{$CMS->lang['edited_page_html_successful']} HTML ID#{$pages_id}")."<br />";
            // Delete cache
            $CMS->class->cache->mdelete($this->cache_prefix);
			$data = $this->get_info($pages_id);
			$CMS->class->logs->key = "pages_{$pages_id}";
			$CMS->class->logs->save_detail("pages",$pages_id,$data);

			$data['pages_name'] = json_decode($data['pages_name'], 1);
			$data['pages_content'] = json_decode($data['pages_content'], 1);
			$data['pages_shorturl'] = json_decode($data['pages_shorturl'], 1);
			return $data;
		}else
		{
			return false;
		}
	}

	function checkExistUrl($key_shorturl="", $pages_id=0)
	{
		global $CMS, $DB;

		$sql = "SELECT count(0) cnt FROM ".root_table."pages WHERE (pages_shorturl='{$key_shorturl}' OR pages_shorturl LIKE '%\"{$key_shorturl}\"%') AND pages_id!='{$pages_id}' AND pages_deleted=0 LIMIT 1";

        $result = $DB->fetch_data($sql, $this->cache_prefix);

        return $result[0]['cnt'];
	}


	function whm_checkExistUrl($key_shorturl="", $pages_id=0)
	{
		global $CMS, $DB, $member;
		$reseller_id = intval($member['reseller_id']);
		$sql = "SELECT count(0) cnt FROM ".root_table."pages WHERE (pages_shorturl='{$key_shorturl}' OR pages_shorturl LIKE '%\"{$key_shorturl}\"%') AND pages_id!='{$pages_id}' AND pages_deleted=0 AND reseller_id= '{$reseller_id}' LIMIT 1";

        $result = $DB->fetch_data($sql, $this->cache_prefix);

        return $result[0]['cnt'];
	}

	function getListPage()
	{
		global $CMS, $DB;

		$sql = "SELECT pages_id, pages_name, pages_shorturl FROM ".root_table."pages WHERE pages_deleted=0";

		$cacheData = $DB->fetch_data($sql, $this->cache_prefix);

		$output = [];

        if($cacheData)
        {
            foreach ($cacheData as $data)
            {
                if(json_decode($data['pages_name'], 1) == NULL) { continue;}

                $data['pages_name'] = json_decode($data['pages_name'], 1);
                $data['pages_shorturl'] = json_decode($data['pages_shorturl'], 1);
                $output[] = $data;
            }
        }

		return $output;
	}



	function whm_getListPage($reseller_id = '')
	{
		global $CMS, $DB;
 
		if($reseller_id != 0)
		{
			$sql_add = " reseller_id = '{$reseller_id}' AND  ";
		}
		$sql = "SELECT pages_id, pages_name, pages_shorturl FROM ".root_table."pages WHERE pages_deleted=0 AND  {$sql_add} 1=1 ";
 
		$cacheData = $DB->fetch_data($sql, $this->cache_prefix);

		$output = [];

        if($cacheData)
        {
            foreach ($cacheData as $data)
            {
                if(json_decode($data['pages_name'], 1) == NULL) { continue;}

                $data['pages_name'] = json_decode($data['pages_name'], 1);
                $data['pages_shorturl'] = json_decode($data['pages_shorturl'], 1);
                $output[] = $data;
            }
        }

		return $output;
	}
}

?>