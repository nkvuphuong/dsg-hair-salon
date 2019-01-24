<?php

use \core\ezy;

$CMS->attach = new class_attach;

class class_attach {

	public $CMS = "";
		
	/**
	 * @param @record_cnt
	 *		The order number of Data
	 */
	
	public $record_cnt = 0;
	
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
	 * @param @is_error
	 *		Is there any error or notice ?
	 */

	public $is_error = "";
	
	/**
	 * @param @js_alert
	 *		Set this value = 1 to alert with javascript
	 */

	public $js_alert = 0;
	
	/**
	 * @param @mod_name
	 *		Module name
	 */
	
	public $mod_name = "";
	
	/**
	 * @param @mod_id
	 *		Module ID
	 */
	
	public $mod_id = "";
	
	/**
	 * @param $html
	 *		The templates
	 */
	 
	public $html;
	 
	/**
	 * @param $backgroundcolor
	 *		Background color for iframe
	 */
	 
	public $backgroundcolor = "#FFFFFF";
	
	/**
	 * @param $add_attach
	 *		Manual attach, html added to attachment
	 */
	 
	public $add_attach;
	
	/**
	 * @param $default_width
	 *		default width for image
	 */
	 
	public $default_width = 120;
	
	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	
	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			if ( $CMS->vars['is_admin_module'] )
			{
				$this->html = $CMS->class->template->load_simple(\core\ezy::$app_dir."/modules/attach/templates/skin_attach.php");
			}
			else
			{
				$this->html = $CMS->class->template->load_template("skin_attach");
			}
		}
	}
	
	public function form( $mod_name = "", $mod_id = 0 )
	{
		global $CMS, $member;

		// Set Module info
		$this->mod_name = $mod_name;
		$this->mod_id = $mod_id;
		
		if ( ! $mod_id )
		{
			if ( $CMS->vars['is_admin_module'] == true )
			{
				$this->sql_add .= " user_id='{$member['user_id']}' AND ";
			}
			else
			{
				$this->sql_add .= " cus_id='{$member['cus_id']}' AND ";
			}
		}

		// Reset page
		$CMS->class->page->get = 1;
		
		// Load language
		$CMS->class->language->load("attach");

		// Load data
		$this->listing();

		// Print data
		$output = $this->html();

		return $this->html->attach_form($output);
	}	
	
	public function listing()
	{
		global $CMS;
			
		// Set Module info
		$this->mod_name = !$this->mod_name ? $CMS->class->filter->clean_value($CMS->input['mod_name']) : $this->mod_name;
		$this->mod_id = !$this->mod_id ? intval($CMS->input['mod_id']) : $this->mod_id;
		
		// Update Arrange Data
		$this->arrange_data = trim("attach_time");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "attach_time";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "ASC";

		// Create SQL Query for listing Data
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."attachment WHERE {$this->sql_add} module_name='{$this->mod_name}' AND module_id='{$this->mod_id}' AND 1=1 ORDER BY {$default_field} {$default_order}");
	}
	
	public function html()
	{
		global $CMS, $DB;
	
		$this->loadhtml();

		if ( $CMS->input['act'] == "attach" )
		{
			$CMS->input['referer'] = $CMS->input['referer'];
		}
		else if ( $CMS->input['act'] == "edit" )
		{
			$CMS->input['referer'] = "edit";
		}
		else
		{
			$CMS->input['referer'] = $CMS->input['referer'];
		}

        $output = "";

		if ( $DB->num_rows( $this->sql_query ) > 0 )
		{
			// Display Header
			$output .= $this->html->attach_header();

			while( $result = $DB->fetch_array( $this->sql_query ) )
			{
				// Convert info
				$result = $this->convertvalue($result);
				
				// Display Middle
				$output .= $this->html->attach_middle($result);
			}
			
			// Display
			$output .= $this->html->attach_footer();
		}
		
		$output .= $this->html->attach_control( $DB->num_rows( $this->sql_query ) . $this->is_error );

		return $output;
	}
	
	//===========================================================================
	//  AUTO RUN
	//===========================================================================

	public function auto_run()
	{
		$this->loadhtml();	
	}
	
	//===========================================================================
	//  ATTACH INFO
	//===========================================================================
	
	public function get_info( $record_id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND ($CMS->input['site'] == "attach") )
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
		
		$sql = $DB->query("SELECT * FROM ".root_table."attachment WHERE attach_id='{$record_id}' OR attach_location='{$record_id}' ORDER BY attach_id DESC LIMIT 1");

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
	
	//===========================================================================
	//  ATTACH DATA
	//===========================================================================
	
	public function convertvalue($data)
	{
		global $CMS, $DB;
	
		$data['attach_time'] = $CMS->class->date->date_format( $data['attach_time'], 1 );
		$data['attach_size'] = round( $data['attach_size'] / 1024 );
		
		$data['attach_url'] = $CMS->vars['root_domain'] . $data['attach_location'];
		
		return $data;
	}
	
	//===========================================================================
	//  UPDATE
	//===========================================================================
	
	public function update( $mod_name, $mod_id )
	{
		global $CMS, $DB, $member;
		
		if ( $CMS->vars['is_admin_module'] == true )
		{
			$sql_add = " user_id='{$member['user_id']}' AND ";
		}
		else
		{
			$sql_add = " cus_id='{$member['cus_id']}' AND ";
		}

		$DB->query("UPDATE ".root_table."attachment SET module_id='{$mod_id}' WHERE {$sql_add} module_name='{$mod_name}' AND module_id='0'");
	}
	
	//===========================================================================
	//  ADD
	//===========================================================================
	
	public function add()
	{
		global $CMS, $DB, $member;
		
		// Set Module info
		$this->mod_name = !$this->mod_name ? $CMS->class->filter->clean_value($CMS->input['mod_name']) : $this->mod_name;
		$this->mod_id = !$this->mod_id ? intval($CMS->input['mod_id']) : $this->mod_id;
		
		$input = "upload_file";/*/*/
/* */


		$file_tmp = isset($_FILES["{$input}"]['tmp_name']) ? $_FILES["{$input}"]['tmp_name'] : "";
		$file_name = isset($_FILES["{$input}"]['name']) ? $_FILES["{$input}"]['name'] : "";
		$file_type = isset($_FILES["{$input}"]['type']) ? $_FILES["{$input}"]['type'] : "";
		$file_size = isset($_FILES["{$input}"]['size']) ? $_FILES["{$input}"]['size'] : "";
		$file_error = isset($_FILES["{$input}"]['error']) ? $_FILES["{$input}"]['error'] : "";

		for($i=0; $i < count($file_name);$i++)
		{
			if($file_name[$i])
			{
					
				$file_time = time();
				$file_ext = $CMS->class->attachment->get_ext( $file_name[$i] );
				$file_name_bk = str_replace( array(" ","?","="), array("_","_","_"), $file_name[$i] );
				$file_location = strtolower($file_time."_".rand(1,1000000)."_".$file_name[$i]);
			
				if ( $CMS->class->attachment->check_ext( $file_ext ) == false )
				{
					if ( $this->js_alert )
					{
						$CMS->output .= "<script language='javascript'>alert('{$CMS->lang['invalid_name']}');</script>";
					}
					else
					{
						$CMS->output .= "{$CMS->lang['invalid_name']}<br /><br />";
						$this->is_error = 1;
					}
					
					return false;
				}
		
				if ( copy($file_tmp[$i], "{$CMS->vars['upload_dir']}/attach/".$file_location) )
				{
					// Thumbnail start
					if ( preg_match( "/(gif|png|jpg)/", $file_ext ) == true )
					{
						//$CMS->class->image->watermask( root_path."templates/images/watermark.png", "{$CMS->vars['upload_dir']}/attach/".$file_location, "{$CMS->vars['upload_dir']}/attach/".$file_location );
						$CMS->class->image->resize( "{$CMS->vars['upload_dir']}/attach/".$file_location, "{$CMS->vars['upload_dir']}/attach/thumbnail/".$file_location, $this->default_width );
						$file_type = 1;
					}
					else
					{
						$file_type = 0;
					}
					//End Thumbnail
				
					$DB->query("INSERT INTO ".root_table."attachment (user_id, module_name, module_id, attach_name, attach_ext, attach_size, attach_location, attach_time, attach_is_image, cus_id) VALUES ('{$member['user_id']}', '{$this->mod_name}', '{$this->mod_id}', '".$file_name_bk."', '".$file_ext."', '".$file_size[$i]."', '".$file_location."', '".$file_time."', '".$file_type."', '".intval($member['cus_id'])."')");
		
					//$result = $this->get_info($file_location, "attach_id");
		
					if ( $this->js_alert )
					{
						$CMS->output .= "<script language='javascript'>alert('{$CMS->lang['added']} {$file_name}');</script>";
					}
					else
					{
						$CMS->output .= "{$CMS->lang['added']} <b>{$file_name[$i]}</b><br /><br />";
						$this->is_error = 1;
					}
				}
				else
				{
					if ( $this->js_alert )
					{
						$CMS->output .= "<script language='javascript'>alert('{$CMS->lang['upload_failed']} {$file_name}');</script>";
					}
					else
					{
						$CMS->output .= "{$CMS->lang['upload_failed']} <b>{$file_name}</b><br /><br />";
						$this->is_error = 1;
					}
		
					return false;
				}
			}
		}
		return true;
	}
	
	//===========================================================================
	//  Attachment Data File
	//===========================================================================
	
	public function addtext( $file_name, $file_type, $file_data )
	{
		global $CMS, $DB, $member;
		
		$file_time = time();
		$file_name = $CMS->class->attachment->clean_name( strtolower(trim($file_name)) );
		//$file_location = substr($file_time."_".$file_name, 0, 32);
		$file_location = $file_time."_".$file_name;
		$file_ext = $CMS->class->attachment->get_ext( $file_name );
		$file_size = strlen($file_data);

		if ( $CMS->class->attachment->check_ext($file_ext ) == true )
		{
			if ( $fp = @fopen( "{$CMS->vars['upload_dir']}/attach/".$file_location, "w+") )
			{
				fwrite($fp, $file_data);
	
				fclose ($fp);
				
				// Thumbnail start
				if ( preg_match( "/(gif|png|jpg)/", $file_ext ) == true )
				{
					$CMS->class->image->watermask( root_path."templates/images/watermark.png", "{$CMS->vars['upload_dir']}/attach/".$file_location, "{$CMS->vars['upload_dir']}/attach/".$file_location );
					$CMS->class->image->resize( "{$CMS->vars['upload_dir']}/attach/".$file_location, "{$CMS->vars['upload_dir']}/attach/thumbnail/".$file_location, $this->default_width );
					$file_type = 1;
				}
				else
				{
					$file_type = 0;
				}
				//End Thumbnail
											
				$DB->query("INSERT INTO ".root_table."attachment (user_id, module_name, module_id, attach_name, attach_ext, attach_size, attach_location, attach_time, attach_is_image, cus_id) VALUES ('{$member['user_id']}', '{$this->mod_name}', '{$this->mod_id}', '".$file_name."', '".$file_ext."', '".$file_size."', '".$file_location."', '".$file_time."', '".$file_type."', '{$member['cus_id']}')");

				$result = $this->get_info($file_location);
				
				return $result;
			}
			else
			{
				return false;
			}
		}

		return true;
	}
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit( $input, $id )
	{
		global $CMS, $DB;
	
		$attach = $this->get_info($id);
		
		$file_tmp = isset($_FILES["{$input}"]['tmp_name']) ? $_FILES["{$input}"]['tmp_name'] : "";
		$file_name = isset($_FILES["{$input}"]['name']) ? $_FILES["{$input}"]['name'] : "";
		$file_type = isset($_FILES["{$input}"]['type']) ? $_FILES["{$input}"]['type'] : "";
		$file_size = isset($_FILES["{$input}"]['size']) ? $_FILES["{$input}"]['size'] : "";
		//$file_error = isset($_FILES["{$input}"]['error']) ? $_FILES["{$input}"]['error'] : "";
			
		$file_ext = $CMS->class->attachment->get_ext( $file_name );
		$file_name = str_replace( " ", "_", $file_name );
		$file_location = strtolower(time()."_".$file_name);
		
		if ( preg_match( "/\.(cgi|pl|asp|php|jsp|jar|mp3|wma|mpg|mpeg|asf|asx|avi|wmv|rm|ram|htaccess)/", $file_name ) )
		{
			$file_type = "text/plain";
		}
							
		if ( $file_type == "text/plain" )
		{
			$CMS->output .= ezy::$html->error("You can not upload a text file !");

			return false;
		}
				
		if ( @copy($file_tmp, "{$CMS->vars['upload_dir']}/attach/".$file_location) )
		{
			@unlink("{$CMS->vars['upload_dir']}/attach/".$attach['attach_location']);
			
			$DB->query("UPDATE ".root_table."attachment SET attach_name='{$file_name}', attach_ext='{$file_ext}', attach_size='{$file_size}', attach_location='{$file_location}', attach_time='".time()."' WHERE attach_id='{$attach['attach_id']}'");
			
			return $attach;
		}
		else
		{
			return false;
		}
	}
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB, $member;
		
		$attach = $this->get_info();
	
		if ( $CMS->vars['is_admin'] == 1 OR $attach['user_id'] == $member['user_id'] OR ($attach['cus_id'] == $member['cus_id'] AND $attach['cus_id'] > 0) )
		{
			$DB->query("DELETE FROM ".root_table."attachment WHERE attach_id='{$attach['attach_id']}'");
			
			@unlink("{$CMS->vars['upload_dir']}/attach/thumbnail/".$attach['attach_location']);
			@unlink("{$CMS->vars['upload_dir']}/attach/".$attach['attach_location']);
			
			if ( $this->js_alert )
			{
				$CMS->output .= "<script language='javascript'>alert('{$CMS->lang['deleted']} {$attach['attach_name']}');</script>";
			}
            else if ( $this->is_ajax )
            {
                echo @json_encode(['status' => 'ok', 'msg' => "{$CMS->lang['deleted']} <b>{$attach['attach_name']}</b>"]); exit;
            }
			else
			{
				$CMS->output .= "{$CMS->lang['deleted']} <b>{$attach['attach_name']}</b><br /><br />";
				$this->is_error = 1;
			}
		}
		else
		{
			if ( $this->js_alert )
			{
				$CMS->output .= "<script language='javascript'>alert('{$CMS->lang['delete_failed']} {$attach['attach_name']}');</script>";
			}
            else if ( $this->is_ajax )
            {
                echo @json_encode(['status' => 'error', 'msg' => "{$CMS->lang['delete_failed']} <b>{$attach['attach_name']}</b>"]); exit;
            }
			else
			{
				$CMS->output .= "{$CMS->lang['delete_failed']} <b>{$attach['attach_name']}</b><br /><br />";
				$this->is_error = 1;
			}
		}

		return true;	
	}
	
	//===========================================================================
	//  SHOW
	//===========================================================================
	
	public function show( $mod_name, $mod_id, $html = 0 )
	{
		global $CMS, $DB, $member;

		if ( ! $mod_name OR ! $mod_id )
		{
			return false;
		}
		
		$this->loadhtml();
		
		// user_id='{$member['user_id']}' AND 
		
		// Display attachment of order
		//if ( $mod_name == "order" )
		//{
		//	$DB->query("SELECT A.* FROM ".root_table."attachment AS A LEFT JOIN ".root_table."task AS T ON T.task_id=A.module_id WHERE (A.module_name='order' AND A.module_id='{$mod_id}') OR (A.module_name='task' AND T.module_id='{$mod_id}')");
		//}
		//else
		//{
			$DB->query("SELECT * FROM ".root_table."attachment WHERE module_name='{$mod_name}' AND module_id='{$mod_id}' ORDER BY attach_name ASC");
		//}
		
		$output = "";
		
		$cnt = 1;
		
		// Check for addition attachment
		if ( $this->add_attach )
		{
			$output .= "{$cnt}. {$this->add_attach}<br />";
			$cnt++;	
		}
		
		// Continue
		if ( $DB->num_rows() > 0 )
		{
			while ( $attach = $DB->fetch_array() )
			{
				$attach['attach_size'] = round($attach['attach_size'] / 1024);
				
				$output .= "{$cnt}. <a href={$CMS->vars['upload_url']}/attach/{$attach['attach_location']} target=_blank>{$attach['attach_name']}</a> <font color=gray>(<b>{$attach['attach_size']}</b> KB)</font><br />";
				$cnt++;
			}
		}
		
		if ( $output )
		{
			$output = $this->html->attach_show($output);	
		}
		
		return $output;
	}
	
	//===========================================================================
	//  GET ARRAY
	//===========================================================================
	
	function get_array( $module_name, $module_id, $module_limit = 0)
	{
		global $CMS, $DB;
		
		if ( ! $module_name OR ! $module_id )
		{
			return false;	
		}
		
		if ( $module_limit > 0 )
		{
			$module_limit = " LIMIT {$module_limit} ";	
		}
		else
		{
			$module_limit = "";	
		}

		$sql = $DB->query("SELECT * FROM ".root_table."attachment WHERE module_name='{$module_name}' AND module_id='{$module_id}' ORDER BY attach_id ASC {$module_limit}");
		
		$array = array();
		$i = 0;
		
		while ( $data = $DB->fetch_array( $sql ) )
		{
		    if($CMS->vars['is_admin_module'])
            {
                $root_domain = str_replace('/acp','',$CMS->vars['root_domain']);
            }
            else
            {
                $root_domain = $CMS->vars['root_domain'];
            }

			$array[$i] = array(
				"attach_id" => $data['attach_id'],
				"attach_name" => $data['attach_name'],
				"attach_ext" => $data['attach_ext'],
				"attach_size" => $data['attach_size'],
				"attach_thumbnail" => "{$CMS->vars['upload_dir']}/attach/thumbnail/".$data['attach_location'],
				"attach_location" => "{$CMS->vars['upload_url']}/attach/".$data['attach_location'],
				"attach_time" => $data['attach_time'],
				"attach_is_image" => $data['attach_is_image'],
				"attach_upload"  => "{$CMS->vars['upload_dir']}/attach/".$data['attach_location'],
			);
			
			$i++;
		}
		
		return $array;
	}
}

?>