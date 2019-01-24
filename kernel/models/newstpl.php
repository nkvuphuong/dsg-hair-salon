<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->newstpl = new class_newstpl;

class class_newstpl {

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

	public $prefix_html = "";
	public $suffix_html = "";
	
	/**
	 * @param $newstpl_project
	 *		Use for multiple projects
	 */

	public $newstpl_project = "";

	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	
	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			$this->html = $CMS->class->template->load_template("skin_newstpl");		
		}
	}
	
	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("newstpl_time,newstpl_name,newstpl_id,newstpl_display");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "newstpl_id";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "desc";
		
		
		// Create SQL Query for listing Data
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."newstpl WHERE {$this->sql_add} 1=1 AND newstpl_deleted=0  ORDER BY {$default_field} {$default_order}", $this->per_page, $this->prefix_html, $this->suffix_html);
	}

	public function html()
	{
		global $CMS, $DB, $member;

		$this->loadhtml();

		// Display Header
		$output .= $this->html->newstpl_header();
		
		if ( $DB->num_rows( $CMS->newstpl->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->newstpl->sql_query ) )
			{
				// Convert info
				$result = $CMS->newstpl->convertvalue($result);
				
				// Display Middle
				$output .= $this->html->newstpl_middle($result);
			}
		}
		else
		{
			// Display No data
			$output .= $this->html->newstpl_none();

			// No data
			$CMS->is_error = 1;
		}
		
		// Display
		$output .= $this->html->newstpl_footer();
		
		// If the page is giving no data
		if ( $CMS->input['page'] > 1 && $CMS->is_error == 1)
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=newstpl");
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
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_newstpl_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_newstpl_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";

			// Check permission to Delete
			if ( $CMS->permit["newstpl_delete"] == true )
			{
				$data .= "<option value='delete_all'>{$CMS->lang['newstpl_action_delete']}</option>";
				$this->control = 1;
			}
			
			$data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

			$CMS->class->cache->save("user_{$member['user_id']}_newstpl_controller_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}
		
		if ( $CMS->vars['action_controller']  OR $CMS->permit["newstpl_search"] == 1 )
		{
			$this->action_control = $this->html->newstpl_control();
		}
		
	
	}

	//===========================================================================
	//  DATA
	//===========================================================================

	public function defaultvalue($data)
	{
		global $CMS;

		$data['newstpl_display'] = $data['newstpl_display'] ? $data['newstpl_display'] : 1;

		return $data;
	}

	public function convertvalue($data, $type = 0, $project = "")
	{
		global $CMS, $DB;
	
		// Replace search content
		$data = $CMS->class->search->convertvalue($data);

		// Rewrite URL
		if ( $project == "template" ) $data['newstpl_shorturl'] = ($CMS->vars['is_rewrite'] == 1 && $data['newstpl_shorturl'])? "thong-bao/{$data['newstpl_shorturl']}.html": "?site=notice&view=detail&id={$data['newstpl_id']}";
		else $data['newstpl_shorturl'] = ($CMS->vars['is_rewrite'] == 1 && $data['newstpl_shorturl'])? "tin-tuc/{$data['newstpl_shorturl']}.html": "?site=newstpl&view=topic&id={$data['newstpl_id']}";;
		
		
		// Convert Register to GMT
		$data['newstpl_time_bk'] = $CMS->class->date->date_format( $data['newstpl_time'], 1 );
		$data['newstpl_time_update'] = $data['newstpl_time_update'] ? $CMS->class->date->date_format( $data['newstpl_time_update'], 1 ) : "<i>N/A</i>";

		// Check permission to read Info
		if ( $type == 1 )
		{
			$data['newstpl_name_bk'] = "<a href='{$CMS->vars['root_domain']}/{$data['newstpl_shorturl']}'>{$data['newstpl_name']}</a>";
		}
		else if ( $CMS->permit["newstpl_read"] == true )
		{
			$data['newstpl_name_bk'] = "<a href='{$CMS->vars['root_domain']}/?site=newstpl&act=show&id={$data['newstpl_id']}'>{$data['newstpl_name']}</a>";
		}
		
		// Replace the Status
		$data['newstpl_display_bk'] = $CMS->lang["display_{$data['newstpl_display']}"];		

		// Bgcolor
		$data['bgcolor'] = $this->record_cnt % 2 != 0 ? "#FFFFFF" : "#F6F6F6";
		
		// User
		$data['user_name'] = $CMS->user->get_info($data['member_id'],"user_display_name");
		
		// Count
		$data['record_cnt'] = $this->record_cnt;
		
		$this->record_cnt++;
		
		return $data;
	}
	
	public function searchvalue($data)
	{
		global $CMS, $DB;

		// Convert Register to GMT
		$data['newstpl_time'] = $CMS->class->date->date_format( $data['newstpl_time'], 0 );

		// Replace the Status
		$data['newstpl_display'] = $CMS->lang["display_{$data['newstpl_display']}"];

		$data['cat_id'] = $cat['cat_name'];

		return $data;
	}
	
	//===========================================================================
	//  INFO
	//===========================================================================
	
	public function get_info( $record_id = 0, $field_name = "")
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "newstpl" )
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
		//$sql_add .= ($this->newstpl_project ? " AND newstpl_project='{$this->newstpl_project}' " : "");
		
		$sql = $DB->query("SELECT * FROM ".root_table."newstpl WHERE newstpl_id='{$record_id}' OR newstpl_name='{$record_id}'  AND newstpl_deleted=0 {$sql_add} ORDER BY newstpl_id DESC LIMIT 1");

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
			$DB->query("SELECT * FROM ".root_table."newstpl WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND newstpl_deleted=0");
		}
		else
		{
			$DB->query("SELECT * FROM ".root_table."newstpl WHERE {$field}='{$value}' AND newstpl_deleted=0");
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
		$newstpl_name = $CMS->class->editor->input('newstpl_name');
		$newstpl_content = $CMS->class->editor->input("newstpl_content");

		$newstpl_time = time();
		$newstpl_display = $CMS->input['newstpl_display'];
		// Check input
		if (  $newstpl_name == "") { $CMS->errormsg = "{$CMS->lang['newstpl_incomplete_name']}"; return false; }

		if ( empty($newstpl_content) )
	    {  
			$CMS->errormsg = "{$CMS->lang['newstpl_incomplete_content']}"; return false;
		}
		// Check upload
		$file_tmp = isset($_FILES['file_upload']['tmp_name']) ? $_FILES['file_upload']['tmp_name'] : "";
		$file_name = isset($_FILES['file_upload']['name']) ? $_FILES['file_upload']['name'] : "";
		$file_type = isset($_FILES['file_upload']['type']) ? $_FILES['file_upload']['type'] : "";
		$file_size = isset($_FILES['file_upload']['size']) ? $_FILES['file_upload']['size'] : "";
		$file_error = isset($_FILES['file_upload']['error']) ? $_FILES['file_upload']['error'] : "";
		
		$file_ext = $CMS->class->attachment->get_ext( $file_name );
		$file_name = str_replace( " ", "_", $file_name );
		$file_location = strtolower(time()."_".$file_name);
		
		if ( $file_name )
		{
			if ( $CMS->class->attachment->is_image($file_name, $file_ext) == false )
			{
				$CMS->errormsg = $CMS->lang['invalid_upload_file'];
					
				return false;		
			}

			@copy($file_tmp, "{$CMS->vars['upload_dir']}/newstpl/".$file_location) or die ("Could not be upload.");
			
			$newstpl_image = $file_location;
		}
		
		

		// Insert data
		$DB->query("INSERT INTO ".root_table."newstpl (newstpl_name,  newstpl_content, newstpl_image,newstpl_time, newstpl_display, member_id) VALUES ('{$newstpl_name}',  '{$newstpl_content}', '{$newstpl_image}','{$newstpl_time}', '{$newstpl_display}', '{$member['user_id']}')");
	
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['newstpl_added']} <b>{$newstpl_name}</b>")."<br />";

		// Get info
		$newstpl = $this->get_info($newstpl_name);
		
		// Update Module ID
		$CMS->attach->update("newstpl", $newstpl['newstpl_id']);
		
		// Delete cache
		$CMS->class->cache->mdelete("newstpl");
		
		return $newstpl;
	}
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		// Get info
		$newstpl = $this->get_info();

		// User input
		$newstpl_name = $CMS->class->editor->input('newstpl_name');
		$newstpl_content = $CMS->class->editor->input("newstpl_content");

		$newstpl_time_update = time();
		$newstpl_display = $CMS->input['newstpl_display'];
		// Check input
		if (  $newstpl_name == "") { $CMS->errormsg = "{$CMS->lang['newstpl_incomplete_name']}"; return false; }

		if ( empty($newstpl_content) )
	    {  
			$CMS->errormsg = "{$CMS->lang['newstpl_incomplete_content']}"; return false;
		}
		
		// Check upload
		$file_tmp = isset($_FILES['file_upload']['tmp_name']) ? $_FILES['file_upload']['tmp_name'] : "";
		$file_name = isset($_FILES['file_upload']['name']) ? $_FILES['file_upload']['name'] : "";
		$file_type = isset($_FILES['file_upload']['type']) ? $_FILES['file_upload']['type'] : "";
		$file_size = isset($_FILES['file_upload']['size']) ? $_FILES['file_upload']['size'] : "";
		$file_error = isset($_FILES['file_upload']['error']) ? $_FILES['file_upload']['error'] : "";
		
		$file_ext = $CMS->class->attachment->get_ext( $file_name );
		$file_name = str_replace( " ", "_", $file_name );
		$file_location = strtolower(time()."_".$file_name);
		
		if ( $file_name != "")
		{
			if ( $CMS->class->attachment->is_image($file_name, $file_ext) == false )
			{
				$CMS->errormsg = $CMS->lang['invalid_upload_file'];
					
				return false;		
			}

			@copy($file_tmp, "{$CMS->vars['upload_dir']}/newstpl/".$file_location) or die ("Could not be upload.");
			
			$newstpl_image = $file_location;
		}
		else
		{
			$newstpl_image = $newstpl['newstpl_image'];
		}
		
		// Step 1: Save detail logs
		$CMS->class->logs->old_data = $newstpl;

		// Update infoah
		$DB->query("UPDATE ".root_table."newstpl SET newstpl_name='{$newstpl_name}', newstpl_content='{$newstpl_content}',newstpl_image = '{$newstpl_image}', newstpl_time_update='{$newstpl_time_update}', newstpl_display='{$newstpl_display}'  WHERE newstpl_id='{$newstpl['newstpl_id']}'");
		
		$CMS->class->logs->key = "newstpl_{$newstpl['newstpl_id']}";
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['newstpl_edited']} <b>{$newstpl_name}</b>")."<br />";
		
		// Get info
		$newstpl_bk = $this->get_info();
		
		// Delete cache
		$CMS->class->cache->mdelete("newstpl");
		// Step 2: Save detail logs
		$CMS->class->logs->key = "newstpl_{$newstpl['newstpl_id']}";
		$CMS->class->logs->save_detail("newstpl",$newstpl_bk['newstpl_id'],$newstpl_bk);
		// Update Module ID
		$CMS->attach->update("newstpl", $newstpl_bk['newstpl_id']);
		
		return $newstpl_bk;
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
		$DB->query("UPDATE ".root_table."newstpl SET newstpl_deleted=1 WHERE newstpl_id={$data['newstpl_id']}");
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['newstpl_deleted']} <b>{$data['newstpl_name']}</b>")."<br />";

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=newstpl&page={$CMS->input['page']}");
		
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

				$DB->query("UPDATE ".root_table."newstpl SET newstpl_deleted=1 WHERE newstpl_id={$id}");
				
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['newstpl_deleted']} <b>{$data['newstpl_name']}</b>")."<br />";
				
				$deleted = 1;
			}
		}
		
		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['newstpl_delete_failed']}";
		}

		return true;
	}
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB;

		// Setup data
		$CMS->class->search->table_name = "newstpl";
		$CMS->class->search->ignored_fields = array();
		$CMS->class->search->changed_fields = array();
		$CMS->class->search->fields_type = array("newstpl_time" => "time");
		$CMS->class->search->search_type = 0;
		$CMS->class->search->fields_prefix = "";
		$CMS->class->search->fields_replace = array("cat_id" => "C.cat_id");
		
		// Output
		$data = $CMS->class->search->get_info();
		
		// Update SQL Query
		$this->sql_add .= $data;
		
		// Get List
		$this->listing();
	}
	
	
	//===========================================================================
	//  Load list template
	//===========================================================================
	
	public function list_newstpl()
	{
		global $CMS, $DB;

		$sql = $DB->query("SELECT * FROM ".root_table."newstpl WHERE newstpl_deleted = 0 AND newstpl_display= 1 ORDER BY newstpl_id DESC");
		$output .=" <option value='' >--- Chọn mẫu tin ---</option>	";	
		if($DB->num_rows($sql) > 0)
		{
			while($data = $DB->fetch_array($sql))
			{
				$output .=<<<EOF
				
				<option value="{$data['newstpl_id']}" >{$data['newstpl_name']}</option>			
EOF;
			}
		}
		return $output;
	}
	
	
	public function ajax($type = 0)
	{
		global $CMS, $DB;
		
		$newstpl_id = intval($CMS->input['newstpl_id']);

		$sql = $DB->query("SELECT * FROM ".root_table."newstpl WHERE newstpl_id='{$newstpl_id}' AND newstpl_deleted=0");
		
		$data = $DB->fetch_array( $sql );

		if($type == 1)
		{
			$data['newstpl_content'] = $CMS->class->editor->shortcode($data['newstpl_content']);
			print "{$data['newstpl_name']}||{$data['newstpl_content']}||{$data['newstpl_image']}";
		}
		else
		{
			print "{$data['newstpl_name']}||{$data['newstpl_content']}||{$data['newstpl_image']}";
		}
		exit;
	}

	
	
	
	
	
	
	
	
	
}

?>