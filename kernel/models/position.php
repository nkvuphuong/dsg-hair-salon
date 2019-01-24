<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->position = new class_position;

class class_position {

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

	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	
	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			$this->html = $CMS->class->template->load_template("skin_position");		
		}
	}
	
	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("pos_id,pos_name,pos_time,pos_status");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "pos_time";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "desc";
		
		// SQL Condition
		$this->sql_add .= " pos_deleted=0 AND ";	
		
		// Create SQL Query for listing Data
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."position WHERE {$this->sql_add} 1=1 ORDER BY {$default_field} {$default_order}");
	}

	public function html()
	{
		global $CMS, $DB, $member;

		$this->loadhtml();

		// Display Header
		$output .= $this->html->header();
		
		if ( $DB->num_rows( $CMS->position->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->position->sql_query ) )
			{
				// Convert info
				$result = $CMS->position->convertvalue($result);
				
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
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=position");
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
		// POSITION
		//-----------------------------------------------------------
		
		if ( $CMS->class->cache->check("banner_pos_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['banner_position'] = $CMS->class->cache->load("banner_pos_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";
			
			$sql = $DB->query("SELECT * FROM ".root_table."position ORDER BY pos_id ASC");
			
			while ( $position = $DB->fetch_array($sql) )
			{
				$position['pos_width'] = $position['pos_width'] ? $position['pos_width'] : "Unlimited";
				$position['pos_height'] = $position['pos_height'] ? $position['pos_height'] : "Unlimited";
				
				$data .= "<option value='{$position['pos_id']}'>{$position['pos_name']} - {$position['pos_width']} x {$position['pos_height']}</option>";
			}
			
			$CMS->class->cache->save("banner_pos_{$CMS->vars['default_language']}", $data);
			$CMS->vars['banner_position'] = $data;
		}

		//-----------------------------------------------------------
		// ACTION CONTROLLER
		//-----------------------------------------------------------
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_pos_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_pos_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";
			
			$data .= "<option value=''>{$CMS->lang['select_action']}</option>";
			
			// Check permission to Delete
			if ( $CMS->permit["pos_delete"] == true )
			{
				$data .= "<option value='delete_all'>{$CMS->lang['pos_action_delete']}</option>";
				$this->control = 1;
			}

			$CMS->class->cache->save("user_{$member['user_id']}_pos_controller_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}

		if ( $CMS->vars['action_controller'] OR $CMS->permit["pos_search"] == 1 )
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

		$data['pos_status'] = $data['pos_status'] ? $data['pos_status'] : 1;

		return $data;
	}
	
	public function convertvalue($data)
	{
		global $CMS, $DB;

		$data['data_bk'] = $data;

		// Replace search content
		$data = $CMS->class->search->convertvalue($data);
		
		// Convert unix time to GMT time
		$data['pos_time'] = $CMS->class->date->date_format( $data['pos_time'], 1 );

		// Check permission to read Info
		if ( $CMS->permit["pos_read"] == true )
		{
			$data['pos_name'] = "<a href='{$CMS->vars['root_domain']}/?site=position&act=show&id={$data['pos_id']}'>{$data['pos_name']}</a>";
		}


		// Bgcolor
		$data['bgcolor'] = $this->record_cnt % 2 != 0 ? "#FFFFFF" : "#F6F6F6";
		
		// User
		$data['user_id'] = $CMS->user->get_display_name($data['user_id']);
		
		$data['pos_status'] = $CMS->lang["display_{$data['pos_status']}"];

		// Count
		$data['record_cnt'] = $this->record_cnt;

		$this->record_cnt++;
		
		return $data;
	}
	
	public function editvalue($data)
	{
		global $CMS, $DB;

		return $data;
	}
	
	public function searchvalue($data)
	{
		global $CMS, $DB;

		// Convert unix time to GMT time
		$data['pos_time'] = $CMS->class->date->date_format( $data['pos_time'], 1 );

		// User
		$data['user_id'] = $CMS->user->get_display_name($data['user_id']);

		// Display
		$data['pos_status'] = $CMS->lang["answer_{$data['pos_status']}"];

		return $data;
	}
	
	//===========================================================================
	//  INFO
	//===========================================================================
	
	public function get_info( $record_id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "position" )
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

		$sql = $DB->query("SELECT * FROM ".root_table."position WHERE (pos_id='{$record_id}' OR pos_name = '{$record_id}') AND pos_deleted=0 ORDER BY pos_id DESC LIMIT 1");

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
			$DB->query("SELECT * FROM ".root_table."position WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND pos_deleted=0");
		}
		else
		{
			$DB->query("SELECT * FROM ".root_table."position WHERE {$field}='{$value}' AND pos_deleted=0");
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
		// print_r($CMS->input);exit;
		$pos_name = $CMS->input['pos_name'];
		$pos_status = intval($CMS->input['pos_status']);
		$user_id = $member['user_id'];
		$pos_time = time();

		// Check input
		if ( ! $pos_name ) { $CMS->errormsg = "{$CMS->lang['position_incomplete_name']}"; return false; }
	
		// Insert data
		$DB->query("INSERT INTO ".root_table."position (pos_name, pos_time, user_id, pos_status) VALUES ('{$pos_name}', '{$pos_time}', '{$user_id}', '{$pos_status}')");
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['position_added']} <b>{$pos_name}</b>")."<br />";

		// Get info
		$position = $this->get_info($pos_name);
		// print_r($position);exit;
		// Delete cache
		$CMS->class->cache->mdelete("position");

		return $position;
	}
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		// Get info
		$position = $this->get_info();

		// User input
		$pos_name = $CMS->input['pos_name'];
		$pos_status = intval($CMS->input['pos_status']);
		$user_id = $member['user_id'];
		$pos_time_update = time();

		// Check input
		if ( ! $pos_name ) { $CMS->errormsg = "{$CMS->lang['position_incomplete_name']}"; return false; }
				
		
		// Step 1: Save detail logs
		$CMS->class->logs->old_data = $position;
		
		// Update info
		$DB->query("UPDATE ".root_table."position SET pos_name='{$pos_name}', pos_update_time='{$pos_update_time}', user_id='{$user_id}', pos_status = '{$pos_status}' WHERE pos_id='{$position['pos_id']}'");
		
		$CMS->class->logs->key = "pos_{$position['pos_id']}";
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['position_edited']} <b>{$pos_name}</b>")."<br />";
		
		// Get info
		$position = $this->get_info();

		// Delete cache
		$CMS->class->cache->mdelete("position");
		// Step 2: Save detail logs
		$CMS->class->logs->key = "pos_{$position['pos_id']}";
		$CMS->class->logs->save_detail("position",$position['pos_id'],$position);

		return $position;
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
		$DB->query("UPDATE ".root_table."position SET pos_deleted=1 WHERE pos_id={$data['pos_id']}");
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['position_deleted']} <b>{$data['pos_name']}</b>")."<br />";

		// Delete cache
		$CMS->class->cache->mdelete("position");

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=position&page={$CMS->input['page']}");
		
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

				$DB->query("UPDATE ".root_table."position SET pos_deleted=1 WHERE pos_id={$id}");
				
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['pos_deleted']} <b>{$data['pos_name']}</b>")."<br />";
				
				$deleted = 1;
			}
		}
		
		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['pos_delete_failed']}";
		}
		
		// Delete cache
		$CMS->class->cache->mdelete("position");

		return true;
	}
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB;

		// Setup data
		$CMS->class->search->table_name = "position";
		$CMS->class->search->ignored_fields = array();
		$CMS->class->search->changed_fields = array();
		$CMS->class->search->fields_type = array("pos_time" => "time");
		$CMS->class->search->search_type = 0;
		
		// Output
		$data = $CMS->class->search->get_info();
		
		// Update SQL Query
		$this->sql_add .= $data;
		
		// Get List
		$this->listing();
	}
	
	//===========================================================================
	//  Load list
	//===========================================================================
	
	public function get_list( $pos_key = "", $type = 0 )
	{
		global $CMS, $DB;
		
		if ( ! $pos_key )
		{
			return false;
		}
		
		$sql = $DB->query("SELECT L.*, P.* FROM ".root_table."position AS L, ".root_table."position AS P WHERE P.pos_id=L.pos_id AND P.pos_key='{$pos_key}' AND L.pos_deleted=0");
		
		$output = "";
		
		while ( $data = $DB->fetch_array( $sql ) )
		{
			$data = $this->convertvalue($data);
		
			$output .= $data['pos_path']."<br />";
		}
		
		return $output;
	}
	
	//===========================================================================
	//  Load position list
	//===========================================================================
	public function getPosition()
	{
		global $CMS, $DB;
		
		$output = "<option value=''>{$CMS->lang['select_position']}</option>";
		
		$DB->query("SELECT * FROM ".root_table."position ORDER BY pos_name ASC");
		
		while($result = $DB->fetch_array())
		{
			$output .= "<option value='{$result['pos_id']}'>{$result['pos_name']}</option>";
		}
		return $output;
	}
	
	//===========================================================================
	//  Load position
	//===========================================================================
	public function load_position($pos_id)
	{
		global $CMS, $DB;
		
		$output = "";
		
		$DB->query("SELECT * FROM ".root_table."position WHERE pos_id = '{$pos_id}' AND pos_deleted = 0");
		
		while($result = $DB->fetch_array())
		{
			$result['pos_path'] = substr($result['pos_path'],11);
			
			$output .= "<a href=\"{$result['pos_website']}\"><img src=\"{$CMS->vars['upload_url']}/position/{$result['pos_path']}\"></a>";
		}
		return $output;
	}

	public function load_display_type_html()
	{
		global $CMS;

		$output = "";

		$data = array(0,1);

		foreach ($data as $v)
		{
			$output .= <<<EOF
			<option value="{$v}">{$CMS->lang['pos_display_type_'.$v]}</option>
EOF;

		}

		return $output;
	}
}

?>