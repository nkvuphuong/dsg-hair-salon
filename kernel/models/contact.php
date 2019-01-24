<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->contact = new class_contact;

class class_contact {

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

	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	
	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			$this->html = $CMS->class->template->load_template("skin_contact");		
		}
	}
	
	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("contact_time,contact_id,contact_name,contact_status");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "contact_order";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "ASC";
		
		// SQL Condition
		//$this->sql_add .= " contact_deleted=0 AND ";

		// Create SQL Query for listing Data
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."contact WHERE {$this->sql_add} 1=1 AND contact_deleted = 0 ORDER BY {$default_field} {$default_order}");
		
		$this->pages_cnt = $DB->num_rows( $this->sql_query );
	}
	
	public function listingWHM() {
		global $CMS, $DB;
		
		$this->sql_add = empty($CMS->input['search_name']) ? '' : "con_name LIKE '%{$CMS->input['search_name']}%' AND";
		
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "con_id";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "DESC";
		
		list($this->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."contact WHERE {$this->sql_add} con_deleted = 0 ORDER BY {$default_field} {$default_order}");
		
		$this->pages_cnt = 0;
		$data = array();
		if ($DB->num_rows($this->sql_query) > 0) {
			$this->pages_cnt = $DB->num_rows($this->sql_query);
			while ($result = $DB->fetch_array($this->sql_query)) {
				$data[] = $result;
			}
		}
		return $data;
	}
	
	
	
	public function html()
	{
		global $CMS, $DB, $member;
		
		$this->loadhtml();
		
		// Display Header
		$output .= $this->html->header();
		
		if ( $DB->num_rows( $CMS->contact->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->contact->sql_query ) )
			{
				
				// Convert info
				$result = $CMS->contact->convertvalue($result);
				
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
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=contact");
		}
		
		return $output;
	}


	
	public function merge_html()
	{
		global $CMS, $DB, $member;
		
		$this->loadhtml();
		
		// Display Header
		$output .= $this->html->header();
		
		if ( $DB->num_rows( $CMS->contact->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->contact->sql_query ) )
			{
				// Convert info
				$result = $CMS->contact->convertvalue($result);
				
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
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=contact");
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
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_contact_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_contact_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";

			// Check permission to Delete
			if ( $CMS->permit["contact_delete"] == true )
			{
				$data .= "<option value='delete_all'>{$CMS->lang['action_delete']}</option>";
				$this->control = 1;
			}
			
			// Check permission to Arrange
			if ( $CMS->permit["contact_arrange"] == true )
			{
				$data .= "<option value='arrange'>{$CMS->lang['contact_action_arrange']}</option>";
				$this->control = 1;
			}

			$data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

			$CMS->class->cache->save("user_{$member['user_id']}_contact_controller_{$CMS->vars['default_language']}", $data);
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

		$data['contact_status'] = $data['contact_status'] ? $data['contact_status'] : 1;

		return $data;
	}
	
	public function convertvalue($data)
	{
		global $CMS, $DB;
	
		// Replace search content
		$data = $CMS->class->search->convertvalue($data);

		
		// Convert Register to GMT
		$data['contact_time'] = $CMS->class->date->date_format( $data['contact_time'], 1 );
		// Check permission to read Info
		if ( $CMS->permit["contact_read"] == true )
		{
			// $data['contact_name_show'] = "<a href='{$CMS->vars['root_domain']}/?site=contact&act=show&id={$data['contact_id']}'>{$data['contact_name']}</a>";
		}
		

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
		$data['contact_time'] = $CMS->class->date->date_format( $data['contact_time'], 0 );

		// Replace the Status
		$data['contact_status'] = $CMS->lang["display_{$data['contact_status']}"];

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
		
		$sql = $DB->query("SELECT * FROM ".root_table."contact WHERE contact_id='{$record_id}' AND contact_deleted=0  ORDER BY contact_id DESC LIMIT 1");
		
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
	
	public function getInfoWHM($record_id = 0, $field_name = "" ) {
		global $CMS, $DB, $member;
		
		$record_id = $record_id > 0 ? $record_id : intval($CMS->input['id']);
		$record_id = strip_tags($record_id);
		
		if (! $record_id ) {
			return false;
		}
		
		$sql = $DB->query("SELECT * FROM ".root_table."contact WHERE con_id='{$record_id}' AND con_deleted=0  ORDER BY con_id DESC LIMIT 1");
		
		if ( $DB->num_rows($sql) > 0 ) {
			$data = $DB->fetch_array($sql);
			if ($field_name) {
				if ( $data[$field_name]) {
					return $data[$field_name];
				}
			} else {
				return $data;
			}
		}
		
		return false;
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
			$DB->query("SELECT * FROM ".root_table."contact WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND contact_deleted=0");
		}
		else
		{
			$DB->query("SELECT * FROM ".root_table."contact WHERE {$field}='{$value}' AND contact_deleted=0");
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
		
		$cus_name = $CMS->input['cus_name'];
		$cus_phone = $CMS->input['cus_phone'];
		$cus_email = $CMS->input['cus_email'];
		$cus_title = $CMS->input['cus_title'];
		$member_id = intval($CMS->input['member_id']);
		$contact_type = intval($CMS->input['contact_type']);
		$cus_content = $CMS->class->editor->input('cus_content');
		$contact_time = time();
		$contact_ip_address = $_SERVER['REMOTE_ADDR'];
		//echo $parent_id;exit;
		// Check input
		if ( ! $cus_name ) { $CMS->errormsg = "{$CMS->lang['incomplete_name']}"; return false; }
		if ( ! $cus_phone ) { $CMS->errormsg = "{$CMS->lang['incomplete_name']}"; return false; }
		if ( ! $cus_email ) { $CMS->errormsg = "{$CMS->lang['incomplete_name']}"; return false; }
		if ( ! $cus_title ) { $CMS->errormsg = "{$CMS->lang['incomplete_name']}"; return false; }
		if ( ! $cus_content ) { $CMS->errormsg = "{$CMS->lang['incomplete_name']}"; return false; }

		
		// Insert data
		$DB->query("INSERT INTO ".root_table."contact (cus_name, cus_phone, cus_email, cus_title, contact_type, cus_content,contact_time, contact_time_update, contact_ip_address, member_id) VALUES ('{$cus_name}', '{$cus_phone}', '{$cus_email}', '{$cus_title}', '{$contact_type}', '{$cus_content}','{$contact_time}','{$contact_time}','{$contact_ip_address}','{$member_id}')");
		
		// Create log
		// Add lien he ben ngoai dang bi loi $member
		// print_r($member);exit;
		// $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['added']} <b>{$cus_title}</b>")."<br />";

		return true;
	}
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		// Get info
		$data = $this->get_info();

		// print "<pre>";
		// print_r($CMS->input);exit;
		// User input
		$contact_name = $CMS->input["contact_name"];
		$contact_description = $CMS->class->editor->input("contact_description");
		$contact_link = $CMS->class->editor->input("contact_link");
		$contact_shorturl = $CMS->class->seo->cleanurl($contact_name);
		
		$contact_status = intval($CMS->input["contact_status"]);
		$contact_order = intval($CMS->input['contact_order']);
		
		// Check upload
		$file_tmp = isset($_FILES['file_upload']['tmp_name']) ? $_FILES['file_upload']['tmp_name'] : "";
		$file_name = isset($_FILES['file_upload']['name']) ? $_FILES['file_upload']['name'] : "";
		$file_type = isset($_FILES['file_upload']['type']) ? $_FILES['file_upload']['type'] : "";
		$file_size = isset($_FILES['file_upload']['size']) ? $_FILES['file_upload']['size'] : "";
		$file_error = isset($_FILES['file_upload']['error']) ? $_FILES['file_upload']['error'] : "";
		
		$file_ext = $CMS->class->attachment->get_ext( $file_name );
		$file_name = str_replace( " ", "_", $file_name );
		$file_location = strtolower(time()."_".$file_name);
		$full_location = "{$CMS->vars['upload_dir']}/avatar/{$file_location}";
		if ( $file_name )
		{
			if ( $CMS->class->attachment->is_image($file_name, $file_ext) == false )
			{
				$CMS->errormsg = $CMS->lang['invalid_upload_file'];
					
				return false;		
			}
				$result = @copy($file_tmp, "{$CMS->vars['upload_dir']}/avatar/".$file_location) or die ("Could not be upload.");
					
			$contact_image = $file_location;
		}else
		{
			$contact_image = $data['contact_image'];
		}

		$time = time();
		// Check input
		if ( ! $contact_name ) { $CMS->errormsg = "{$CMS->lang['incomplete_name']}"; return false; }
		
		// Update info
		$DB->query("UPDATE ".root_table."contact SET contact_name='{$contact_name}', contact_description='{$contact_description}', contact_status='{$contact_status}', contact_shorturl='{$contact_shorturl}', contact_order = '{$contact_order}', contact_time_update = '{$time}', contact_image ='{$contact_image}', contact_link ='{$contact_link}'  WHERE contact_id='{$data['contact_id']}'");
		
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['edited']} <b>{$contact_name}</b>")."<br />";
		
		// Get info
		$data = $this->get_info();
		// Step 2: Save detail logs
		$CMS->class->logs->key = "contact_{$data['contact_id']}";
		$CMS->class->logs->save_detail("contact",$data['contact_id'],$data);
		
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
		//xoá hình đại diện
		@unlink("{$CMS->vars['upload_dir']}/avatar/{$data['contact_image']}");
		// Update info
		$DB->query("UPDATE ".root_table."contact SET contact_deleted=1 WHERE contact_id={$data['contact_id']}");
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['deleted']} <b>{$data['contact_name']}</b>")."<br />";

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=contact&page={$CMS->input['page']}");
		
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
				//xoá hình đại diện
				@unlink("{$CMS->vars['upload_dir']}/avatar/{$data['contact_image']}");
				$DB->query("UPDATE ".root_table."contact SET contact_deleted=1 WHERE contact_id={$id}");
				
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['deleted']} <b>{$data['contact_name']}</b>")."<br />";
				
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
	//  ARRANGE
	//===========================================================================

	public function arrange()
	{
		global $CMS, $DB;
		
		$_SESSION["msg"] .= "";

		$sql = $DB->query("SELECT * FROM ".root_table."contact WHERE contact_deleted=0 ORDER BY contact_id ASC");

		while ( $data = $DB->fetch_array( $sql ) )
		{
			$order = intval( $CMS->input["order_{$data['contact_id']}"] );
			
			if ( $order )
			{
				if(isset($_SESSION['order_contact_home']) AND $_SESSION['order_contact_home'] == 1)
				{
					$DB->query("UPDATE ".root_table."contact SET contact_order_home='{$order}' WHERE contact_id='{$data['contact_id']}'");
				}
				else
				{
					$DB->query("UPDATE ".root_table."contact SET contact_order='{$order}' WHERE contact_id='{$data['contact_id']}'");
				}
			}
		}
		
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['contact_arranged']}")."<br />";
		
		return true;
	}
	
	
	


	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB;

		// Setup data
		$CMS->class->search->table_name = "contact";
		$CMS->class->search->ignored_fields = array();
		$CMS->class->search->changed_fields = array();
		$CMS->class->search->fields_type = array("contact_time" => "time");
		// $CMS->class->search->search_type = 0;
		// $CMS->class->search->fields_prefix = "N.";
		// $CMS->class->search->fields_replace = array("cat_id" => "C.cat_id");
		
		// Output
		$data = $CMS->class->search->get_info();
	
		// Update SQL Query
		$this->sql_add .= $data;
		
		
		// Get List
		$this->listing();
		
		
		
	}
	
	public function get_option_cat()
	{
		global $CMS, $DB;

		$output = "";
		$sql = $DB->query("SELECT * FROM ".root_table."course WHERE course_deleted = 0");

		if($DB->num_rows($sql) > 0 )
		{
			while ($result = $DB->fetch_array($sql)) 
			{
				$output .= "<option value='{$result[course_id]}'>{$result[course_name]}</option>";
			}
		}

		return $output;
	}
	

	public function get_list_contact($member_id,$limit=10)
	{
		global $CMS, $DB;

		$data = array();
		$sql = "SELECT * FROM ".root_table."contact WHERE contact_deleted = 0 AND member_id = '{$member_id}' ORDER BY contact_id DESC";	
		$maxpage = $limit;
    	$prefix = "danh-sach-lien-he/trang_";
    	$suffix = ".html";
    	$CMS->class->page->type = "cat_news";
    	list($CMS->show_page, $this->sql_query) = $CMS->class->page->create($sql, $maxpage,$prefix,$suffix);

		if($DB->num_rows($this->sql_query) > 0)
		{
			while ($result = $DB->fetch_array($this->sql_query)) 
			{
				$result = $this->convertvalue($result);

				$data[] = $result;
			}
		}

		return $data;

	}

	public function check_id_contact($contact_id, $member_id)
	{
		global $CMS, $DB;

		$sql = $DB->query("SELECT * FROM ".root_table."contact WHERE contact_id = '{$contact_id}' AND contact_deleted = 0 AND member_id = '{$member_id}'");
		if($DB->num_rows($sql) > 0)
		{
			return true;
		}else
		{
			return false;
		}
	}
	
}

?>