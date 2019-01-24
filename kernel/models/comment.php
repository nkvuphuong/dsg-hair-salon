<?php

use \core\ezy;

$CMS->comment = new class_comment;

class class_comment {

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
	 * @param $module_id
	 *		Module ID
	 */
	 
	public $module_id;
	 
	/**
	 * @param $module_name
	 *		Module name
	 */
	 
	public $module_name;
	
	/**
	 * @param $per_page
	 *		Order per page
	 */
	 
	public $per_page = 20;
	
	/**
	 * @param $show_page
	 *		Pages html
	 */
	 
	public $show_page = "";

	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	
	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			$this->html = $CMS->class->template->load_simple(\core\ezy::$app_dir."/modules/comment/templates/skin_comment.php");
		}
	}
	
	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("comment_id,comment_name,comment_time");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "comment_time";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "DESC";
		
		// SQL Condition
		$this->sql_add .= " comment_deleted IN (0,2) AND ";	
		
		// Create SQL Query for listing Data
		list($this->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."comment WHERE {$this->sql_add} 1=1 ORDER BY {$default_field} {$default_order}", $this->per_page);
	}

	public function html()
	{
		global $CMS, $DB, $member;

		$this->loadhtml();

		// Display Header
	 
		
		if ( $DB->num_rows( $CMS->comment->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->comment->sql_query ) )
			{
				// Convert info
				$result = $CMS->comment->convertvalue($result);
				
		 		$output .=<<<EOF
		 		<div class="comment-row-item" comment_id='{$result['comment_id']}'>
                                    <div class="avatar-preview avatar-preview-32">
                                         
                                             {$result['user_avatar']}
                                       
                                    </div>
                                    <div class="tbl comment-row-item-header">
                                        <div class="tbl-row">
                                            <div class="tbl-cell tbl-cell-name">{$result['comment_name']}</div>
                                            <div class="tbl-cell tbl-cell-date">{$result['comment_time']}</div>
                                        </div>
                                    </div>
                                    <div class="comment-row-item-content">
                                        <p>{$result['comment_content']}</p>
                                        
                                       
                                    </div>
                               
                 </div>
                               

EOF;
			}
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
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_comment_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_comment_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";

			// Check permission to Delete
			if ( $CMS->permit["comment_delete"] == true )
			{
				$data .= "<option value='delete_all'>{$CMS->lang['comment_action_delete']}</option>";
				$this->control = 1;
			}
			
			// Check permission to Arrange
			if ( $CMS->permit["comment_approve"] == true )
			{
				$data .= "<option value='approve'>{$CMS->lang['comment_action_approve']}</option>";
				$this->control = 1;
			}
			
			// Check permission to Arrange
			if ( $CMS->permit["comment_hide"] == true )
			{
				$data .= "<option value='hide_all'>{$CMS->lang['comment_action_hide']}</option>";
				$this->control = 1;
			}
			$data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

			$CMS->class->cache->save("user_{$member['user_id']}_comment_controller_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}
		
		if ( $CMS->vars['action_controller']  OR $CMS->permit["comment_search"] == 1 )
		{
			$this->action_control = $this->html->comment_control();
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

		$data['comment_approved'] = $data['comment_approved'] ? $data['comment_approved'] : 1;

		return $data;
	}
	
	public function convertvalue($data)
	{
		global $CMS, $DB;
	
		// Add Permission
		$data = $this->permission_init( $data );
	
		// Replace search content
		$data = $CMS->class->search->convertvalue($data);
		
		//-----------------------------------------------------------
		// USER CONVERT
		//-----------------------------------------------------------
		
		// Convert Register to GMT
		$data['comment_time'] = $CMS->class->date->date_format( $data['comment_time'], 1 );
		$data['comment_time_update'] = $CMS->class->date->date_format( $data['comment_time_update'], 1 );
		
		// Check permission to read Info
		$data['comment_name'] = $CMS->permit["user_read"] == true ? "<a href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['comment_id']}'>{$data['comment_name']}</a>" : $data['comment_name'];

		// User
		$user =  $CMS->user->get_info($data['user_id']);
		if ($user['user_avatar'] != "") {

                $user_avatar = <<<EOF
                                                            
                                <img src="{$CMS->vars['upload_url']}/avatar/thumbnail/{$user['user_avatar']}" alt="user-img" class="img-circle user-img" >
                                
EOF;
        } else {

                $user_avatar = <<<EOF
                                                            
                                <img src="assets/img/avatar-2-64.png" alt="user-img" class="img-circle user-img">
                                
EOF;
        }
        $data['user_avatar'] = $user_avatar;
		if($CMS->permit['user_read'] == true)
		{
			$data['user_name'] =  "<a href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}'>" . $user['user_display_name'] ."</a>" ;
		}
		else
		{
			$data['user_name'] = $user['user_display_name'];
		}

		// Customer
//		$data['cus_username'] = $CMS->permit['customer_read'] == true ? "<a href='{$CMS->vars['root_domain']}/?site=customer&act=show&id={$data['user_id']}'>" . $CMS->customer->getInfo($data['cus_id'],"cus_realname") ."</a>" : $CMS->customer->getInfo($data['cus_id'],"cus_realname");
		
		// Replace the Status
		$data['comment_approved'] = $CMS->lang["answer_{$data['comment_approved']}"];
		
		// Content
 
		$data['comment_content_bk'] = $CMS->class->editor->substr($data['comment_content'],0,150);
		
		// Check for language
		$data['comment_username'] = (substr($data['comment_username'],0,5) == "lang_") ? $CMS->lang[substr($data['comment_username'],5,strlen($data['comment_username']))] : $data['comment_username'];
		$data['comment_hide_bk'] = $CMS->lang["hide_{$data['comment_hide']}"];
		 

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
		$data['comment_time'] = $CMS->class->date->date_format( $data['comment_time'], 1 );
		$data['comment_time_update'] = $CMS->class->date->date_format( $data['comment_time_update'], 1 );
		
		// Check permission to read Info
		$data['comment_name'] = $CMS->permit["comment_read"] == true ? "<a href='{$CMS->vars['root_domain']}/?site=comment&act=show&id={$data['comment_id']}'>{$data['comment_name']}</a>" : "";

		// User
		$data['user_name'] = $CMS->permit['user_read'] == true ? "<a href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}'>" . $CMS->user->get_info($data['user_id'],"user_display_name") ."</a>" : $CMS->user->get_info($data['user_id'],"user_display_name");
		
		// Customer
		$data['cus_username'] = $CMS->permit['customer_read'] == true ? "<a href='{$CMS->vars['root_domain']}/?site=customer&act=show&id={$data['user_id']}'>" . $CMS->customer->getInfo($data['cus_username'],"cus_realname") ."</a>" : $CMS->customer->getInfo($data['cus_username'],"cus_realname");
		
		// Replace the Status
		$data['comment_approved'] = $CMS->lang["answer_{$data['comment_approved']}"];
		
		// Replace the Status
		$data['comment_hide'] = $CMS->lang["hide_{$data['comment_hide']}"];
		

		return $data;
	}
	
	//===========================================================================
	//  INFO
	//===========================================================================
	
	public function get_info( $record_id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "comment" )
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
		
		$sql = $DB->query("SELECT * FROM ".root_table."comment WHERE (comment_id='{$record_id}' OR comment_name='{$record_id}')  ORDER BY comment_id DESC LIMIT 1");

		if ( $DB->num_rows($sql) > 0 )
		{
			$data = $DB->fetch_array($sql);
		
			// Add Permission
			$data = $this->permission_init( $data );
		
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
			$DB->query("SELECT * FROM ".root_table."comment WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND comment_deleted=0");
		}
		else
		{
			$DB->query("SELECT * FROM ".root_table."comment WHERE {$field}='{$value}' AND comment_deleted=0");
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
	//  PERMISSION INITIALIZE
	//===========================================================================
	
	public function permission_init( $data )
	{
		global $CMS, $DB, $member;
		
		// Administrator
		if ( $CMS->vars['is_admin'] == 1 )
		{
			$data['comment_edit'] = "edit";	
			$data['comment_delete'] = "delete";
			
			return $data;
		}
		
		// Edit Permission
		$data['comment_edit'] = $data['user_id'] == $member['user_id'] ? "edit" : "";
		$data['comment_delete'] = $data['user_id'] == $member['user_id'] ? "delete" : "";

		return $data;
	}

	//===========================================================================
	//  ADD
	//===========================================================================
	
	public function add()
	{
		global $CMS, $DB, $member;

		// User input
		$user_id = $member['user_id'];
		$module_id = intval($CMS->input['module_id']);
		$module_name = $CMS->class->filter->clean_value($CMS->input['module_name']);
		$comment_username = isset($CMS->input['comment_username']) ? $CMS->input['comment_username'] : $member['user_display_name'];
		$comment_email = $CMS->input['comment_email'];
		$comment_name =isset($CMS->input['comment_username']) ? $CMS->input['comment_username'] : $member['user_display_name'];
		$comment_content = $CMS->class->editor->input('comment_content') ? $CMS->class->editor->input('comment_content'):$CMS->input['comment_content'];
		$comment_time = time();
		$comment_ip_address = ezy::$ip_address;
		$comment_approved = isset($CMS->input['comment_approved']) ? $CMS->input['comment_approved'] : 1;
		$comment_to_user = isset($CMS->input['comment_to_user']) ? intval($CMS->input['comment_to_user']) : 0;

		// Check input
		if ( ! $comment_name ) 
		{ 
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['comment_incomplete_name']}"));exit; 
		}
		if ( ! $comment_content )
		{ 
	 
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['comment_incomplete_content']}"));exit; 
		}
		if ( ! $module_id )
		{ 
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['comment_incomplete_moduleid']}"));exit; 
			 
		}
		if ( ! $module_name )
		{ 
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['comment_incomplete_modulename']}"));exit; 
		}
		
		$array_blacklist = explode(",",$CMS->vars['char_comment_blacklist']);
		foreach($array_blacklist as $forbid_word)
		{
			$comment_content = str_replace($forbid_word, '', $comment_content);
		}
		// Get comment to user
		$user_array = array();
		$module_userid = 0;
		$cnt = 0;
		
		if ( $comment_to_user > 0 )
		{
			$user_array[$cnt] = $comment_to_user;
			$module_userid = $comment_to_user;
			$cnt++;
		}
		
		if ( $module_name == "comment" )
		{
			$sql = $DB->query("SELECT * FROM ".root_table."comment WHERE user_id!='{$comment_to_user}' AND module_id='{$module_id}' AND comment_deleted=0 GROUP BY user_id");
			
			while ( $comment = $DB->fetch_array($sql) )
			{
				$user_array[$cnt] = $comment['user_id'];
				$cnt++;
			}
		}

		// Print user list
		$comment_to_user = "-1";
		
		foreach ( $user_array as $row )
		{
			$comment_to_user .= ",".$row;
		}
		
		$comment_to_user .= ",-1";
		
		$comment_user_read = $comment_to_user;


		if ( $module_name == "order" )
		{
			 if($module_id > 0)
			 {
			 	// Update last comment to module order
			 	$DB->query("UPDATE ".root_table."order SET ord_last_comment='{$comment_content}'  WHERE ord_id='{$module_id}'");
			 }
		}


		// Insert data
		$DB->query("INSERT INTO ".root_table."comment (module_id, module_name, user_id, comment_username, comment_email, comment_name, comment_content, comment_time, comment_ip_address, comment_approved, comment_to_user, comment_user_read, module_userid) VALUES ('{$module_id}', '{$module_name}', '{$user_id}', '{$comment_username}', '{$comment_email}', '{$comment_name}', '{$comment_content}', '{$comment_time}', '{$comment_ip_address}', '{$comment_approved}', '{$comment_to_user}', '{$comment_user_read}', '{$module_userid}')");
		$id_cmt = $DB->last_insert_id();
		// Create log
		$CMS->class->logs->insert("{$CMS->lang['comment_added']} <b>{$comment_name}</b>")."<br />";
	 


		 

		$comment = $this->get_info($id_cmt);
	 	return $comment;
	}
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		// Get info
		$comment = $this->get_info();

		// User input
		$module_id = intval($CMS->input['module_id']);
		$module_name = $CMS->class->filter->clean_value($CMS->input['module_name']);
		$comment_username = $CMS->input['comment_username'];
		$comment_email = $CMS->input['comment_email'];
		$comment_name = $CMS->input['comment_name'];
		$comment_content = $CMS->class->editor->input('comment_content');
		$comment_time_update = time();
		$comment_approved = $CMS->input['comment_approved'];
		
		// Check input
		if ( ! $comment_name ) { $CMS->errormsg = "{$CMS->lang['comment_incomplete_name']}"; return false; }
		if ( ! $comment_content ) { $CMS->errormsg = "{$CMS->lang['comment_incomplete_content']}"; return false; }
		if ( ! $module_id ) { $CMS->errormsg = "{$CMS->lang['comment_incomplete_moduleid']}"; return false; }
		if ( ! $module_name ) { $CMS->errormsg = "{$CMS->lang['comment_incomplete_modulename']}"; return false; }
		$array_blacklist = explode(",",$CMS->vars['char_comment_blacklist']);
		foreach($array_blacklist as $forbid_word)
		{
			$comment_content = str_replace($forbid_word, '', $comment_content);
		}
		// Update info
		$DB->query("UPDATE ".root_table."comment SET module_id='{$module_id}', module_name='{$module_name}', comment_username='{$comment_username}', comment_email='{$comment_email}', comment_name='{$comment_name}', comment_content='{$comment_content}', comment_time_update='{$comment_time_update}', comment_approved='{$comment_approved}' WHERE comment_id='{$comment['comment_id']}'");
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['comment_edited']} <b>{$comment_name}</b>")."<br />";
		
		// Get info
		$comment = $this->get_info();

		return $comment;
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
		$DB->query("DELETE FROM ".root_table."comment WHERE comment_id={$data['comment_id']}");
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['comment_deleted']} <b>{$data['comment_name']}</b>")."<br />";

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=comment&page={$CMS->input['page']}");
		
		return true;
	}
	
	//===========================================================================
	//  Hide comment
	//===========================================================================
	
	public function hide()
	{
		global $CMS, $DB;
		
		// Get info
		$data = $this->get_info();
		
		// Check existing
		if ( ! $data ) { return false; }
		
		// Update info
		$DB->query("UPDATE ".root_table."comment SET comment_hide=1 WHERE comment_id={$data['comment_id']}");
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['comment_hide']} <b>{$data['comment_name']}</b>")."<br />";

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=comment&page={$CMS->input['page']}");
		
		return true;
	}
	
	//===========================================================================
	//  Hien thị comment
	//===========================================================================
	
	public function unhide()
	{
		global $CMS, $DB;
		
		// Get info
		$data = $this->get_info();
	
		// Check existing
		if ( ! $data ) { return false; }
		
		// Update info
		$DB->query("UPDATE ".root_table."comment SET comment_hide=0 WHERE comment_id={$data['comment_id']}");
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['comment_unhide']} <b>{$data['comment_name']}</b>")."<br />";

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=comment&page={$CMS->input['page']}");
		
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

		
				$DB->query("DELETE FROM ".root_table."comment WHERE comment_id={$data['comment_id']}");
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['comment_deleted']} <b>{$data['comment_name']}</b>")."<br />";
				
				$deleted = 1;
			}
		}
		
		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['comment_delete_failed']}";
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
		$CMS->class->search->table_name = "comment";
		$CMS->class->search->fields_type = array("comment_time" => "time");

		// Output
		$data = $CMS->class->search->get_info();
		
		// Update SQL Query
		$this->sql_add .= $data;
		
		// Get List
		$this->listing();
	}
	
	//===========================================================================
	//  LOAD LIST
	//===========================================================================
	
	public function loadlist( $type = "" )
	{
		global $CMS, $DB;

		$module_id = intval($CMS->input['module_id']);
		$module_name = $CMS->class->filter->clean_value($CMS->input['module_name']);
		$page = isset($CMS->input['page']) ? intval($CMS->input['page']) : 1;

		// Limit
		$limit = $CMS->vars['comment_per_page'] ? $CMS->vars['comment_per_page'] : 10;

		list($show_page, $sql) = $CMS->class->page->create("SELECT * FROM ".root_table."comment WHERE module_id='{$module_id}' AND module_name='{$module_name}' AND comment_approved=1 AND comment_deleted=0 ORDER BY comment_id DESC", $limit);

		if ( $type == "ajax" )
		{
			$output = "";
			
			while ( $data = $DB->fetch_array( $sql ) )
			{
				$data = $this->convertvalue($data);
				$output .= "{$data['comment_id']}|{$data['comment_username']}|{$data['comment_time']}|{$data['comment_content']}||";
			}
			
			$output .= "{$CMS->class->page->maxpage}";
			
			print $output;
			
			exit;
		}
		else
		{
			$output = array();
			$count = 0;
		
			while ( $data = $DB->fetch_array() )
			{
				$output[$count] = $data;
				
				$count++;
			}
			
			return $output;
		}

	}
	
	//===========================================================================
	//  LOAD LIST
	//===========================================================================
	
	public function loadform()
	{
		global $CMS, $DB, $member;
		
		$this->loadhtml();
		
		return $this->html->comment_form();
	}
	
	//===========================================================================
	//  ACTIVE COMMENT
	//===========================================================================
	
	public function active()
	{
		global $CMS, $DB, $member;
		
		// Get info
		$comment = $this->get_info();

		$DB->query("UPDATE ".root_table."comment SET comment_approved = 1 WHERE comment_id={$comment['comment_id']}");

		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['comment_approved_bk']} #{$comment['comment_id']}<b> ")."<br />";

		$comment_bk = $this->get_info();

		// Delete cache
		$CMS->class->cache->mdelete("comment");

		return $comment_bk;
	}
	
	
	//===========================================================================
	//  APPROVE
	//===========================================================================

	public function approve()
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

				$DB->query("UPDATE ".root_table."comment SET comment_approved =1 WHERE comment_id={$id}");
				
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['comment_approved_bk']} <b>{$data['comment_name']}</b>")."<br />";
				
				$deleted = 1;
			}
		}
		
		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['comment_delete_failed']}";
		}

		return true;
	}
	
	//===========================================================================
	//  Hide
	//===========================================================================

	public function mhide()
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

				$DB->query("UPDATE ".root_table."comment SET comment_hide =1 WHERE comment_id={$id}");
				
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['comment_hide_bk']} <b>{$data['comment_name']}</b>")."<br />";
				
				$deleted = 1;
			}
		}
		
		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['comment_hide_failed']}";
		}

		return true;
	}
	
	
	//===========================================================================
	//  SEND EMAIL
	//===========================================================================
	
	public function send_email($comment)
	{
		global $CMS, $DB, $member;

		// Create list
		$list = array();
		
		// Load list
		switch ( $comment['module_name'] )
		{
			case "contract":
				$contract = $CMS->contract->get_info($comment['module_id']);
				$module_title = $CMS->contract->get_name($comment['module_id']);
				$module_url = "{$CMS->vars['root_domain']}/?site=contract&act=show&id={$comment['module_id']}";
				$list[$contract['user_id']] = 1;
			break;
			case "task":
				$task = $CMS->task->get_info($comment['module_id']);
				$module_title = $CMS->task->get_name($comment['module_id']);
				$module_url = "{$CMS->vars['root_domain']}/?site=task&act=show&id={$comment['module_id']}";
				$list[$task['user_id']] = 1;
				$list[$task['task_user_id']] = 1;
			break;
			case "user":
				$module_title = $CMS->user->get_info($comment['module_id'], "user_display_name");
				$module_url = "{$CMS->vars['root_domain']}/?site=user&act=show&id={$comment['module_id']}#{$comment['comment_id']}";
				$list[$comment['module_id']] = 1;
			break;
			case "comment":
				$message = $this->get_info($comment['module_id']);
				$module_title = $CMS->user->get_info($message['module_id'], "user_display_name");
				$module_url = "{$CMS->vars['root_domain']}/?site=user&act=show&id={$message['module_id']}#{$comment['comment_id']}";
				$list[$message['module_id']] = 1;
			break;
			default:
				$module_title = $CMS->lang['module_title_default'];
				$module_url = "";
			break;
		}

		// Load comment
		$sql = $DB->query("SELECT DISTINCT(user_id) FROM ".root_table."comment WHERE module_name='{$comment['module_name']}' AND module_id='{$comment['module_id']}'");

		while ( $data = $DB->fetch_array($sql) )
		{
			$list[$data['user_id']] = 1;
		}
		
		// Remove sender
		unset($list[$member['user_id']]);
		
		// Convert to array
		$key = array_keys($list);
	
		// Check if empty
		if ( count($key) == 0 )
		{
			return false;	
		}
		
		// Init
		$CMS->vars['module_title'] = $module_title;
		$CMS->vars['module_url'] = $module_url;
		$CMS->input['comment_id'] = $comment['comment_id'];

		// Default Input
		$CMS->email->logkey = "comment_{$comment['comment_id']}";
		$CMS->email->email_template = "comment_notifier";

		// Create email list
		for ( $i = 0; $i < count($key); $i++ )
		{
			$user_record = $CMS->user->get_info($key[$i]);
			
			if ( $user_record['user_email'] )
			{
				$CMS->email->email_to = $user_record['user_email'];
				$CMS->email->email_toname = $user_record['user_display_name'];
				$CMS->email->quick_send();
			}
		}
	}
	
	//=====================================================
	// Count comment
	//====================================================
	public function count_comment($module_id = "", $module_name = "")
	{
		global $CMS, $DB, $member;
	
		$sp = $DB->query("SELECT * FROM ".root_table."comment WHERE  module_name = '{$module_name}' AND module_id = '{$module_id}' AND comment_approved = 1 AND comment_deleted = 0 ORDER BY comment_id DESC ");
	
		$count = intval($DB->num_rows($sp));
		return $count;
	
	}
}

?>