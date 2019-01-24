<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->logo = new class_logo;

class class_logo {

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
	
	
	public $pages_cnt = "";

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
			$this->html = $CMS->class->template->load_template("skin_logo");		
		}
	}
	
	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("logo_id,logo_name,logo_time,logo_status");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "logo_order";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "asc";
		
		// SQL Condition
		$this->sql_add .= " logo_deleted=0 AND ";	
		
		// Create SQL Query for listing Data
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."logo WHERE {$this->sql_add} 1=1 ORDER BY {$default_field} {$default_order}");
		$this->pages_cnt = $DB->num_rows( $this->sql_query );
	}

	public function html()
	{
		global $CMS, $DB, $member;

		$this->loadhtml();

		// Display Header
		$output .= $this->html->header();
		
		if ( $DB->num_rows( $CMS->logo->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->logo->sql_query ) )
			{
				// Convert info
				$result = $CMS->logo->convertvalue($result);
				
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
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=logo");
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
		
		if ( $CMS->class->cache->check("banner_position_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['banner_position'] = $CMS->class->cache->load("banner_position_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";
			
			//$sql = $DB->query("SELECT * FROM ".root_table."logo_position ORDER BY logo_position_id ASC");
			
			//while ( $position = $DB->fetch_array($sql) )
			//{
				//$position['position_width'] = $position['logo_position_width'] ? $position['logo_position_width'] : "Unlimited";
				//$position['position_height'] = $position['logo_position_height'] ? $position['logo_position_height'] : "Unlimited";
				
				//$data .= "<option value='{$position['logo_position_id']}'>{$position['position_name']} - {$position['position_width']} x {$position['logo_position_height']}</option>";
			//}
			
			//$CMS->class->cache->save("banner_position_{$CMS->vars['default_language']}", $data);
			//$CMS->vars['banner_position'] = $data;
		}

		//-----------------------------------------------------------
		// ACTION CONTROLLER
		//-----------------------------------------------------------
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_logo_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_logo_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";
			
			$data .= "<option value=''>{$CMS->lang['select_action']}</option>";
			
			// Check permission to Delete
			if ( $CMS->permit["logo_delete"] == true )
			{
				$data .= "<option value='delete_all'>{$CMS->lang['logo_action_delete']}</option>";
				$this->control = 1;
			}
			// Check permission to Arrange
			if ( $CMS->permit["logo_arrange"] == 1 )
			{
				$data .= "<option value='arrange'>{$CMS->lang['logo_action_arrange']}</option>";
				$this->control = 1;
			}
			$CMS->class->cache->save("user_{$member['user_id']}_logo_controller_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}

		if ( $CMS->vars['action_controller'] OR $CMS->permit["logo_search"] == 1 )
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

		$data['logo_status'] = $data['logo_status'] ? $data['logo_status'] : 1;

		return $data;
	}
	
	public function convertvalue($data)
	{
		global $CMS, $DB;

		$data['data_bk'] = $data;

		// Replace search content
		$data = $CMS->class->search->convertvalue($data);

		// Convert unix time to GMT time
		$data['logo_time'] = $CMS->class->date->date_format( $data['logo_time'], 1 );
		$data['logo_start_time_bk'] = $CMS->class->date->date_format( $data['logo_start_time'], 1 );
		$data['logo_end_time_bk'] = $CMS->class->date->date_format( $data['logo_end_time'], 1 );
		$data['position_id_bk'] = $CMS->logo_position->get_info( $data['position_id'],"logo_position_name");

		$pos = $CMS->logo_position->get_info( $data['position_id']);
		$data['width'] = $pos['logo_position_width'];
		$data['height'] = $pos['logo_position_height'];

		// Check permission to read Info
		if ( $CMS->permit["logo_read"] == true )
		{
			$data['logo_name_bk'] = "<a href='{$CMS->vars['root_domain']}/?site=logo&act=show&id={$data['logo_id']}'>{$data['logo_name']}</a>";
		}

		list($width, $height) = @getimagesize("{$CMS->vars['upload_dir']}/logo/{$data['logo_path']}");

		$data['logo_target_bk'] = $data['logo_target'] ? "target='{$data['logo_target']}'" : "target='_blank'";

		$data['logo_path_bk'] = $data['logo_path'];

		if ( substr($data['logo_path'], -3, 3) == "swf" )
		{
			$data['logo_type'] = 'flash';

			//$data['logo_path'] = "<object width='{$width}' height='{$height}'><param name='movie' value='{$CMS->vars['upload_url']}/logo/{$data['logo_path']}'><embed src='{$CMS->vars['upload_url']}/logo/{$data['logo_path']}' type='application/x-shockwave-flash' wmode='transparent' width='{$width}' height='{$height}'></embed></object>";
			$banner_name = substr($data['logo_path'], 0, -4);
			$data['logo_path'] = "
			
			<div class=\"flash_banner\" >
                	<script language=\"javascript\">
						if (AC_FL_RunContent == 0) {
							alert(\"This page requires AC_RunActiveContent.js.\");
						} else {
							AC_FL_RunContent(
								'codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0',
								'width', '{$width}',
								'height', '{$height}',
								'src', upload_url+'/logo/{$banner_name}',
								'quality', 'high',
								'pluginspage', 'http://www.macromedia.com/go/getflashplayer',
								'align', 'middle',
								'play', 'true',
								'loop', 'true',
								'scale', 'showall',
								'wmode', 'transparent',
								'devicefont', 'false',
								'id', '{$banner_name}',
								'bgcolor', '#ffffff',
								'name', '{$banner_name}',
								'menu', 'true',
								'allowFullScreen', 'false',
								'allowScriptAccess','sameDomain',
								'movie', upload_url+'/logo/{$banner_name}',
								'salign', ''
								); //end AC code
						}
					</script>
                    <noscript>
                        <object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0\" width=\"406\" height=\"81\" id=\"banner\" align=\"middle\">
                            <param name=\"allowScriptAccess\" value=\"sameDomain\" />
                            <param name=\"allowFullScreen\" value=\"false\" />
                            <param name=\"movie\" value=\"{$CMS->vars['upload_url']}/logo/{$data['logo_path']}\" /><param name=\"quality\" value=\"high\" /><param name=\"bgcolor\" value=\"#ffffff\" />	<embed src=\"{$CMS->vars['upload_url']}/logo/{$data['logo_path']}\" quality=\"high\" bgcolor=\"#ffffff\" width=\"{$width}\" height=\"{$height}\" name=\"{$banner_name}\" align=\"middle\" allowScriptAccess=\"sameDomain\" allowFullScreen=\"false\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
                        </object>
                    </noscript>
                </div>";
		}
		else
		{
			if(!file_exists("{$CMS->vars['upload_dir']}/logo/{$data['data_bk']['logo_path']}") || !is_file("{$CMS->vars['upload_dir']}/logo/{$data['data_bk']['logo_path']}"))
			{
				$data['logo_path'] = "{$CMS->vars['img_url']}/no-img.jpg";
			}
			else
			{
				$data['logo_path'] = "{$CMS->vars['upload_url']}/logo/{$data['data_bk']['logo_path']}";
			}
		}

//		$data['logo_code'] = str_replace("<br/>","",$data['logo_code']);
//		$data['logo_code'] = str_replace("<br />","",$data['logo_code']);
//		$data['logo_code'] = str_replace("<br>","",$data['logo_code']);
//		$data['logo_code'] = str_replace("<br >","",$data['logo_code']);
//		$data['logo_code'] = html_entity_decode($data['data_bk']['logo_code']);

		// Bgcolor
		$data['bgcolor'] = $this->record_cnt % 2 != 0 ? "#FFFFFF" : "#F6F6F6";

		// User
		$data['user_id'] = $CMS->user->get_display_name($data['user_id']);

		$data['logo_status_bk'] = $CMS->lang["logo_status_{$data['logo_status']}"];

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
		$data['logo_time'] = $CMS->class->date->date_format( $data['logo_time'], 1 );

		// User
		$data['user_id'] = $CMS->user->get_display_name($data['user_id']);

		// Display
		$data['logo_status'] = $CMS->lang["answer_{$data['logo_status']}"];
		$data['position_id'] = $CMS->logo_position->get_info($data['position_id'], "logo_position_name");

		return $data;
	}
	
	//===========================================================================
	//  INFO
	//===========================================================================
	
	public function get_info( $record_id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "logo" )
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
		
		$sql = $DB->query("SELECT * FROM ".root_table."logo WHERE logo_id='{$record_id}' OR logo_name='{$record_id}' OR logo_path='{$record_id}' AND logo_deleted=0 ORDER BY logo_id DESC LIMIT 1");

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
			$DB->query("SELECT * FROM ".root_table."logo WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND logo_deleted=0");
		}
		else
		{
			$DB->query("SELECT * FROM ".root_table."logo WHERE {$field}='{$value}' AND logo_deleted=0");
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
		$logo_name = $CMS->input['logo_name'];
		$logo_website = $CMS->input['logo_website'];
		$logo_target = $CMS->input['logo_target'];
		$logo_owner = $CMS->input['logo_owner'];
		$user_id = $member['user_id'];
		$position_id = $CMS->input['position_id'];
		$position_key = $CMS->logo_position->get_info($position_id, "logo_position_key");

		$logo_status = $CMS->input['logo_status'];
		$logo_above = intval($CMS->input['logo_above']);
			$time = $CMS->input['logo_start_time'];
			$logo_start_time = $CMS->class->date->date2time($time,1);
		
			$time_end = $CMS->input['logo_end_time'];
			$logo_end_time = $CMS->class->date->date2time($time_end,1)+(24*3600)-1;
			
			
		$logo_time = time();
		$logo_type = intval($CMS->input['logo_type']);
		$logo_code = $CMS->class->editor->input("logo_code");

		$logo_path = "";

		// Check input
		if ( ! $logo_name ) { $CMS->errormsg = "{$CMS->lang['logo_incomplete_name']}"; return false; }
		
		if ( ! $position_id ) { $CMS->errormsg = "{$CMS->lang['logo_incomplete_position']}"; return false; }

		if($logo_type == 0)
		{
			if ( ! $logo_website ) { $CMS->errormsg = "{$CMS->lang['logo_incomplete_website']}"; return false; }

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

				@copy($file_tmp, "{$CMS->vars['upload_dir']}/logo/".$file_location) or die ("Could not be upload.");

				$logo_path = $file_location;
			}
		}
		else if($logo_type == 1)
		{
			if ( ! $logo_code ) { $CMS->errormsg = "{$CMS->lang['logo_incomplete_code']}"; return false; }
		}


		
		// Insert data
		$DB->query("INSERT INTO ".root_table."logo (logo_name, logo_path, logo_website, logo_target, logo_owner, user_id, position_id, logo_above, logo_start_time, logo_end_time ,logo_status, logo_time, position_key, logo_type, logo_code) VALUES ('{$logo_name}', '{$logo_path}', '{$logo_website}', '{$logo_target}', '{$logo_owner}', '{$user_id}', '{$position_id}', '{$logo_above}','{$logo_start_time}','{$logo_end_time}', '{$logo_status}', '{$logo_time}','{$position_key}', '{$logo_type}', '{$logo_code}')");
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['logo_added']} <b>{$logo_name}</b>")."<br />";

		// Get info
		$logo = $this->get_info($DB->last_insert_id());
		
		// Delete cache
		$CMS->class->cache->mdelete("logo");

		return $logo;
	}
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		// Get info
		$logo = $this->get_info();

		// User input
		$logo_name = $CMS->input['logo_name'];
		$logo_website = $CMS->input['logo_website'];
		$logo_target = $CMS->input['logo_target'];
		$logo_owner = $CMS->input['logo_owner'];
		$position_id = $CMS->input['position_id'];
		$position_key = $CMS->logo_position->get_info($position_id, "logo_position_key");
		$logo_above = intval($CMS->input['logo_above']);
		$logo_status = $CMS->input['logo_status'];
		$logo_update_time = time();
		$time = $CMS->input['logo_start_time_bk'];

		if($time != "")
		{
			$logo_start_time = $CMS->class->date->date2time($time,1);
		}
		else
		{
			$logo_start_time = $logo['logo_start_time'];	
		}
		$time_end = $CMS->input['logo_end_time_bk'];
		if($time_end != "")
		{
			$logo_end_time = $CMS->class->date->date2time($time_end,1)+(24*3600)-1;
		}
		else
		{
			$logo_end_time = $logo['logo_end_time'];	
		}

		$logo_type = intval($CMS->input['logo_type']);
		$logo_code = $CMS->class->editor->input("logo_code");

		// Check input
		if ( ! $logo_name ) { $CMS->errormsg = "{$CMS->lang['logo_incomplete_name']}"; return false; }
				
		if ( ! $position_id ) { $CMS->errormsg = "{$CMS->lang['logo_incomplete_position']}"; return false; }

		if($logo_type == 0)
		{
			if ( ! $logo_website ) { $CMS->errormsg = "{$CMS->lang['logo_incomplete_website']}"; return false; }

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

				$result = @copy($file_tmp, "{$CMS->vars['upload_dir']}/logo/".$file_location) or die ("Could not be upload.");

				if ( $result )
				{
					@unlink("{$CMS->vars['upload_dir']}/logo/{$logo['logo_path']}");
				}

				$logo_path = $file_location;
			}
			else
			{
				$logo_path = $logo['logo_path'];
			}

			$logo_code = "";
		}
		elseif ($logo_type == 1)
		{
			if ( ! $logo_code ) { $CMS->errormsg = "{$CMS->lang['logo_incomplete_code']}"; return false; }

			@unlink("{$CMS->vars['upload_dir']}/logo/{$logo['logo_path']}");

			$logo_path = "";
		}


		// Step 1: Save detail logs
		$CMS->class->logs->old_data = $logo;
		
		// Update info
		$DB->query("UPDATE ".root_table."logo SET logo_name='{$logo_name}', logo_path='{$logo_path}', logo_website='{$logo_website}', logo_target='{$logo_target}', logo_owner='{$logo_owner}', position_id='{$position_id}', logo_above='{$logo_above}', logo_start_time = '{$logo_start_time}', logo_end_time = '{$logo_end_time}',logo_status='{$logo_status}', logo_update_time='{$logo_update_time}', position_key = '{$position_key}', logo_type = '{$logo_type}', logo_code = '{$logo_code}' WHERE logo_id='{$logo['logo_id']}'");
		
		$CMS->class->logs->key = "logo_{$logo['logo_id']}";
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['logo_edited']} <b>{$logo_name}</b>")."<br />";
		
		// Get info
		$logo = $this->get_info();

		// Delete cache
		$CMS->class->cache->mdelete("logo");
		// Step 2: Save detail logs
		$CMS->class->logs->key = "logo_{$logo['logo_id']}";
		$CMS->class->logs->save_detail("logo",$logo['logo_id'],$logo);
		return $logo;
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
		
		if($data['logo_path'] != "")
		{
			@unlink("{$CMS->vars['upload_dir']}/logo/{$data['logo_path']}");
		}
		// Update info
		$DB->query("DELETE FROM ".root_table."logo WHERE logo_id={$data['logo_id']}");
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['logo_deleted']} <b>{$data['logo_name']}</b>")."<br />";

		// Delete cache
		$CMS->class->cache->mdelete("logo");

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=logo&page={$CMS->input['page']}");
		
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
				
					if($data['logo_path'] != "")
					{
						@unlink("{$CMS->vars['upload_dir']}/logo/{$data['logo_path']}");
					}
					// Update info
					$DB->query("DELETE FROM ".root_table."logo WHERE logo_id={$data['logo_id']}");
				
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['logo_deleted']} <b>{$data['logo_name']}</b>")."<br />";
				
				$deleted = 1;
			}
		}
		
		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['logo_delete_failed']}";
		}
		
		// Delete cache
		$CMS->class->cache->mdelete("logo");

		return true;
	}
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB;

		// Setup data
		$CMS->class->search->table_name = "logo";
		$CMS->class->search->ignored_fields = array();
		$CMS->class->search->changed_fields = array();
		$CMS->class->search->fields_type = array("logo_time" => "time");
		$CMS->class->search->search_type = 0;
		
		// Output
		$data = $CMS->class->search->get_info();
		
		// Update SQL Query
		$this->sql_add .= $data;
		
		// Get List
		$this->listing();
	}
	
	
	
	
	//===========================================================================
	//  ARRANGE
	//===========================================================================

	public function arrange()
	{
		global $CMS, $DB;
		
		$_SESSION["msg"] .= "";

		$sql = $DB->query("SELECT * FROM ".root_table."logo WHERE logo_deleted=0 ORDER BY logo_id ASC");
		
		while ( $data = $DB->fetch_array( $sql ) )
		{
			$logo = intval( $CMS->input["logo_{$data['logo_id']}"] );
	
			if ( $logo )
			{
					$DB->query("UPDATE ".root_table."logo SET logo_order='{$logo}' WHERE logo_id ='{$data['logo_id']}'");		
			}
		}
		
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['logo_arranged']}")."<br />";
		
		return true;
	}
	
	//===========================================================================
	//  Load list
	//===========================================================================
	
	public function get_list( $position_key = "", $type = 0 )
	{
		global $CMS, $DB;
		
		if ( ! $position_key )
		{
			return false;
		}
		
		$sql = $DB->query("SELECT L.*, P.* FROM ".root_table."logo AS L, ".root_table."logo_position AS P WHERE P.position_id=L.position_id AND P.position_key='{$position_key}' AND L.logo_deleted=0");
		
		$output = "";
		
		while ( $data = $DB->fetch_array( $sql ) )
		{
			$data = $this->convertvalue($data);
		
			$output .= $data['logo_path']."<br />";
		}
		
		return $output;
	}
	
	//===========================================================================
	//  Load position list
	//===========================================================================
	public function get_position_list()
	{
		global $CMS, $DB;
		
		$output = "";
		
		$output .= "<option value=''>{$CMS->lang['select_position']}</option>";
		
		$DB->query("SELECT * FROM ".root_table."logo_position WHERE logo_position_deleted = 0 ORDER BY logo_position_id ASC");
		
		while($result = $DB->fetch_array())
		{
			$output .= "<option value='{$result['logo_position_id']}'>{$result['logo_position_name']} ({$result['logo_position_width']}x{$result['logo_position_height']})</option>";
		}
		return $output;
	}
	
	//===========================================================================
	//  Load logo
	//===========================================================================
	public function load_logo($pos_id)
	{
		global $CMS, $DB;
		
		$output = "";
		
		$DB->query("SELECT * FROM ".root_table."logo WHERE position_id = '{$pos_id}' AND logo_deleted = 0");
		
		while($result = $DB->fetch_array())
		{
			$result['logo_path'] = substr($result['logo_path'],11);
			
			$output .= "<a href=\"{$result['logo_website']}\"><img src=\"{$CMS->vars['upload_url']}/logo/{$result['logo_path']}\"></a>";
		}
		return $output;
	}


	public function show_adv($pos_key, $check_time="")
	{
		global $CMS, $DB;

		$sql = $DB->query("SELECT * FROM ".root_table."logo WHERE position_key = '{$pos_key}' AND logo_status = 1 AND logo_deleted = 0 ORDER BY logo_order ASC ");
		$output = "";
        if($DB->num_rows($sql) > 0)
        {
            while($data = $DB->fetch_array($sql) )
            {
                $data = $CMS->logo->convertvalue($data);
                // print "<pre>";
                // print_r($data);exit;
                if((time() >= $data['logo_start_time']) && (time() <= $data['logo_end_time']) and $check_time == 1 )
                {
                	if($pos_key == "news_mid_right")
                	{
                		$output .=<<<EOF

                        <div class="ads hidden-xs">
		                    {$data['logo_path']}
		                </div>
                    
                
EOF;
                	}else
                	{
                		if($pos_key == "news_right_top_member" or $pos_key == "news_right_bottom_member")
                		{
                			$class= "hidden-sm hidden-xs";
                		}else
                		{
                			$class = "";
                		}
                		$output .=<<<EOF
                     <section class="ads {$class}">
                            {$data['logo_path']}
                        </section>
                    
                
EOF;
                	}
                
        
                }else
                {

                    if($pos_key == "news_mid_right")
                	{
                		$output .=<<<EOF
                		
                        <div class="ads hidden-xs">
		                    {$data['logo_path']}
		                </div>
                    
                
EOF;
                	}else
                	{
                		if($pos_key == "news_right_top_member" or $pos_key == "news_right_bottom_member")
                		{
                			$class= "hidden-sm hidden-xs";
                		}else
                		{
                			$class = "";
                		}

                		$output .=<<<EOF
                     <section class="ads {$class}">
                            {$data['logo_path']}
                        </section>
                    
                
EOF;
                	}


                }
            }
		}


		return $output;

	}

	public function html_adv_home($pos_key, $tag="div", $class="", $check_time="")//Left - right
	{
		global $CMS, $DB;

		$sql = $DB->query("SELECT * FROM ".root_table."logo WHERE position_key = '{$pos_key}' AND logo_status = 1 AND logo_deleted = 0 ORDER BY logo_order ASC");

		$output = "";
        if($DB->num_rows($sql) > 0)
        {
            while($data = $DB->fetch_array($sql) )
            {
                $data = $CMS->logo->convertvalue($data);
                // print "<pre>";
                // print_r($data);exit;
                if($check_time == 1 )
                {


	                if((time() >= $data['logo_start_time']) && (time() <= $data['logo_end_time']))
	                {
                	
                	$output .=<<<EOF
					<{$tag} class="{$class}">
						{$data['logo_path']}
					</{$tag}>
EOF;

        			}
                }else
                {
					$output .=<<<EOF
					<{$tag} class="{$class}">
						{$data['logo_path']}	
					</{$tag}>
EOF;
                }
            }
		}


		return $output;

	}
	
	function load_type_html()
	{
		global $CMS, $DB;
		
		$output = "";

		$data = array(0,1);

		foreach ($data as $v)
		{
			$output .= <<<EOF
			<option value="{$v}">{$CMS->lang['logo_type_'.$v]}</option>
EOF;
		}

		return $output;
	}

	public function load_ads_data($position_key="")
	{
		global $CMS, $DB;

		$data = array();

		$time =  time();

		$position_key = trim($position_key);

		if($position_key=="") return false;

		$position = $CMS->logo_position->get_info($position_key);

		if(!$position) return false;

		$data['position'] = $position;

		$sql = "SELECT * FROM ".root_table."logo WHERE logo_deleted=0 AND logo_status=1 AND (logo_start_time=0 OR logo_start_time=25200 OR logo_start_time<$time) AND (logo_end_time=0 OR logo_end_time=111599 OR logo_end_time=86399 OR logo_end_time>$time) AND position_id='{$position['logo_position_id']}' ORDER BY logo_order";

		$sql = $DB->query($sql);

		while ($logo = $DB->fetch_assoc($sql))
		{
			$data['logo'][] = $logo;
		}

		if($position['logo_position_display_type'] == 1) //random
		{
			$rand_index = rand(0, count($data['logo'])-1);
			$logo = $data['logo'][$rand_index];
			unset($data['logo']);

			$data['logo'][0] = $logo;
		}

		return $data;

	}

	function load_ads_template($data, $class="")
	{
		global $DB, $CMS;
		$logo_data = $data['logo'];
		
		$output = "";

			foreach ($logo_data as $logo) {
				$logo = $CMS->logo->convertvalue($logo);

				if ($logo['logo_type'] == 'flash') {
					$output .= <<<EOF
		<section class="ads {$class}">
			{$logo['logo_path']}
		</section>
EOF;
				} else if ($logo['logo_type'] == 1) {
					$output .= <<<EOF
		<section class="ads {$class}">
			{$logo['logo_code']}
		</section>
EOF;
				} else {
					$output .= <<<EOF
		<section class="ads {$class}">
			<a href="{$logo['logo_website']}"><img src="{$logo['logo_path']}"></a>
		</section>
EOF;
				}
			}
		return $output;
	}
}

?>