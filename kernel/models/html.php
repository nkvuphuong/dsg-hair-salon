<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->html = new class_html;

class class_html {

	public $CMS;
	
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

	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	
	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			$this->html = $CMS->class->template->load_template("skin_html");		
		}
	}
	
	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("html_id,html_name,html_time");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "html_time";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "DESC";
		
		// SQL Condition
		$this->sql_add .= " html_deleted=0 AND ";	
		
		// Create SQL Query for listing Data
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."html WHERE {$this->sql_add} 1=1 ORDER BY {$default_field} {$default_order}");
	}

	public function html()
	{
		global $CMS, $DB, $member;

		$this->loadhtml();

		// Display Header
		$output .= $this->html->html_header();
		
		if ( $DB->num_rows( $CMS->html->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->html->sql_query ) )
			{
				// Convert info
				$result = $CMS->html->convertvalue($result);
				
				// Display Middle
				$output .= $this->html->html_middle($result);
			}
		}
		else
		{
			// Display No data
			$output .= $this->html->html_none();

			// No data
			$CMS->is_error = 1;
		}
		
		// Display
		$output .= $this->html->html_footer();
		
		// If the page is giving no data
		if ( $CMS->input['page'] > 1 && $CMS->is_error == 1)
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=html");
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
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_html_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_html_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";

			// Check permission to Delete
			if ( $CMS->permit["html_delete"] == true )
			{
				$data .= "<option value='delete_all'>{$CMS->lang['html_action_delete']}</option>";
				$this->control = 1;
			}
			
			$data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

			$CMS->class->cache->save("user_{$member['user_id']}_html_controller_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}
		
		if ( $CMS->vars['action_controller']  OR $CMS->permit["html_search"] == 1 )
		{
			$this->action_control = $this->html->html_control();
		}
		
		//-----------------------------------------------------------
		// LOAD ANOTHER MODELS
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
		
		//-----------------------------------------------------------
		// USER CONVERT
		//-----------------------------------------------------------
		
		// Convert Register to GMT
		$data['html_time'] = $CMS->class->date->date_format( $data['html_time'], 1 );
		$data['html_time_update'] = $data['html_time_update'] ? $CMS->class->date->date_format( $data['html_time_update'], 1 ) : "";
		
		// Check permission to read Info
		$data['html_name'] = $CMS->permit["html_read"] == true ? "<a href='{$CMS->vars['root_domain']}/?site=html&act=show&id={$data['html_id']}'>{$data['html_name']}</a>" : "{$data['html_name']}";

		// User
		$data['user_name'] = $CMS->permit['user_read'] == true ? "<a href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}'>" . $CMS->user->get_info($data['user_id'],"user_display_name") ."</a>" : $CMS->user->get_info($data['user_id'],"user_display_name");

		//-----------------------------------------------------------
		// SYSTEM CONVERT
		//-----------------------------------------------------------
		
		// Background Color
		$data['bgcolor'] = $this->record_cnt % 2 != 0 ? "#FFFFFF" : "#F6F6F6";
		
		// Order Number
		$data['record_cnt'] = $this->record_cnt;
	
		// Increase Order Number
		$this->record_cnt++;
		
		return $data;
	}
	
	public function editvalue($data)
	{
		global $CMS;

		return $data;
	}
	
	public function searchvalue($data)
	{
		global $CMS, $DB;

		//-----------------------------------------------------------
		// USER CONVERT
		//-----------------------------------------------------------
		
		// Convert Register to GMT
		$data['html_time'] = $CMS->class->date->date_format( $data['html_time'], 1 );
		$data['html_time_update'] = $data['html_time_update'] ? $CMS->class->date->date_format( $data['html_time_update'], 1 ) : "";
		
		// Check permission to read Info
		$data['html_name'] = $CMS->permit["html_read"] == true ? "<a href='{$CMS->vars['root_domain']}/?site=html&act=show&id={$data['html_id']}'>{$data['html_name']}</a>" : "{$data['html_name']}";

		// User
		$data['user_name'] = $CMS->permit['user_read'] == true ? "<a href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}'>" . $CMS->user->get_info($data['user_id'],"user_display_name") ."</a>" : $CMS->user->get_info($data['user_id'],"user_display_name");

		return $data;
	}
	
	//===========================================================================
	//  INFO
	//===========================================================================
	
	public function get_info( $record_id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "html" )
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
		
		$sql = $DB->query("SELECT * FROM ".root_table."html WHERE (html_id='{$record_id}' OR html_name='{$record_id}') AND html_deleted=0 ORDER BY html_id DESC LIMIT 1");

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
	
	public function check_exist( $field, $value = "", $except_value = "" )
	{
		global $CMS, $DB, $member;
		
		if ( ! $field )
		{
			return true;
		}
		
		if ( $except_value )
		{
			$DB->query("SELECT * FROM ".root_table."html WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND html_deleted=0");
		}
		else
		{
			$DB->query("SELECT * FROM ".root_table."html WHERE {$field}='{$value}' AND html_deleted=0");
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
		$user_id = $member['user_id'];
		$html_name = $CMS->input['html_name'];
		$html_content = $CMS->class->editor->input('html_content');
		$html_key = $CMS->input['html_key'];
		$html_time = time();

		// Check input
		if ( ! $html_name ) { $CMS->errormsg = "{$CMS->lang['html_incomplete_name']}"; return false; }
		//if ( ! $html_content ) { $CMS->errormsg = "{$CMS->lang['html_incomplete_content']}"; return false; }
		
		// Insert data
		$DB->query("INSERT INTO ".root_table."html (user_id, html_name, html_content, html_time, html_key) VALUES ('{$user_id}', '{$html_name}', '{$html_content}', '{$html_time}', '{$html_key}')");
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['html_added']} <b>{$html_name}</b>")."<br />";

		// Get info
		$html = $this->get_info($html_name);
		
		// Update Module ID
		$CMS->attach->update("html", $html['html_id']);
		
		// Delete cache
		$CMS->class->cache->mdelete("html");

		return $html;
	}
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		// Get info
		$html = $this->get_info();

		// User input
		$html_name = $CMS->input['html_name'];
		$html_content = $CMS->class->editor->input('html_content');
		$html_key = $CMS->input['html_key'];
		$html_time_update = time();

		// Check input
		if ( ! $html_name ) { $CMS->errormsg = "{$CMS->lang['html_incomplete_name']}"; return false; }
		//if ( ! $html_content ) { $CMS->errormsg = "{$CMS->lang['html_incomplete_content']}"; return false; }
		
		// Update info
		$DB->query("UPDATE ".root_table."html SET html_name='{$html_name}', html_content='{$html_content}', html_time_update='{$html_time_update}', html_key='{$html_key}' WHERE html_id='{$html['html_id']}'");
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['html_edited']} <b>{$html_name}</b>")."<br />";
		
		// Get info
		$html = $this->get_info();
		
		// Update Module ID
		$CMS->attach->update("pages", $pages['pages_id']);
		
		// Delete cache
		$CMS->class->cache->mdelete("html");

		return $html;
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
		$DB->query("UPDATE ".root_table."html SET html_deleted=1 WHERE html_id={$data['html_id']}");
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['html_deleted']} <b>{$data['html_name']}</b>")."<br />";
		
		// Delete cache
		$CMS->class->cache->mdelete("html");

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=html&page={$CMS->input['page']}");
		
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

				$DB->query("UPDATE ".root_table."html SET html_deleted=1 WHERE html_id={$id}");
				
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['html_deleted']} <b>{$data['html_name']}</b>")."<br />";
				
				$deleted = 1;
			}
		}
		
		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['html_delete_failed']}";
		}

		// Delete cache
		$CMS->class->cache->mdelete("html");

		return true;
	}
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB;

		// Setup data
		$CMS->class->search->table_name = "html";
		$CMS->class->search->fields_type = array("html_time" => "time");

		// Output
		$data = $CMS->class->search->get_info();
		
		// Update SQL Query
		$this->sql_add .= $data;
		
		// Get List
		$this->listing();
	}

}

?>