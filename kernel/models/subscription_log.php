<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->subscription_log = new class_subscription_log;

class class_subscription_log {

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

	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	
	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			$this->html = $CMS->class->template->load_template("skin_subscription_log");		
		}
	}
	
	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("sublog_id, sublog_request_time, sublog_repsonse_time");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "sublog_request_time";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "DESC";
		
		// SQL Condition
		$this->sql_add .= "";

		// Create SQL Query for listing Data
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."subscription_log WHERE {$this->sql_add} 1=1  ORDER BY {$default_field} {$default_order}");
		$this->pages_cnt = $DB->num_rows( $this->sql_query );
	}
	
	
	public function html()
	{
		global $CMS, $DB, $member;
		
		$this->loadhtml();
		
		// Display Header
		$output .= $this->html->header();
		
		if ( $DB->num_rows( $CMS->subscription_log->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->subscription_log->sql_query ) )
			{
				// Convert info
				$result = $CMS->subscription_log->convertvalue($result);
				
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
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=subscription_log");
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
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_sublog_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_sublog_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";

			// Check permission to Delete
			if ( $CMS->permit["sublog_delete"] == true )
			{
				$data .= "<option value='delete_all'>{$CMS->lang['action_delete']}</option>";
				$this->control = 1;
			}
			// Check permission to Arrange
			$data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

			$CMS->class->cache->save("user_{$member['user_id']}_sublog_controller_{$CMS->vars['default_language']}", $data);
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

		return $data;
	}
	
	public function convertvalue($data)
	{
		global $CMS, $DB;

        $data['data_bk'] = $data;

		// Replace search content
		$data = $CMS->class->search->convertvalue($data);

                
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
		
		$sql = $DB->query("SELECT * FROM ".root_table."subscription_log WHERE sublog_id='{$record_id}' ORDER BY sublog_id DESC LIMIT 1");

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
	//  ADD
	//===========================================================================
	
	public function addRequest($data=[])
	{
        global $CMS, $DB, $member;

        $sub_id = intval($data['sub_id']);
        $sublog_request = trim($data['sublog_request']);
        $sublog_request_time = time();
        $sublog_gateway = trim($data['sublog_gateway']);

        $sql = "INSERT INTO ".root_table."subscription_logs (sub_id, sublog_request, sublog_request_time, sublog_gateway) VALUES ('{$sub_id}', '{$sublog_request}', '{$sublog_request_time}', '{$sublog_gateway}')";

        if($DB->query($sql))
        {
            return $DB->last_insert_id();
        }

        return false;
	}

    //===========================================================================
    //  EDIT
    //===========================================================================

    public function updateResponse($data=[])
    {
        global $CMS, $DB, $member;
        $sublog_id = intval($data['sublog_id']);
        $sublog_response = trim($data['sublog_response']);
        $sublog_response_time = time();
        // Insert data
        return $DB->query("UPDATE ".root_table."subscription_logs SET sublog_response='{$sublog_response}', sublog_response_time='{$sublog_response_time}' WHERE sublog_id='{$sublog_id}'");
    }
}

?>