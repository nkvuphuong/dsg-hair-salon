<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->smstpl = new class_smstpl;

class class_smstpl {

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
	 * @param $cache
	 *		Temp store customer info
	 */
	 
	public $cache = array();

	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	
	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			$this->html = $CMS->class->template->load_template("skin_smstpl");		
		}
	}
	
	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("smstpl_id,smstpl_name,smstpl_status");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "smstpl_time";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "desc";
		
		// SQL Condition
		$this->sql_add .= " smstpl_deleted=0 AND ";

        if(isset($CMS->input['keyword']))
        {
            $keyword = urldecode($CMS->input['keyword']);
            $this->sql_add .= " (smstpl_name LIKE '%{$keyword}%' OR smstpl_code LIKE '%{$keyword}%') AND ";
        }

        if(isset($CMS->input['smstpl_status']) && $CMS->input['smstpl_status'] !== '')
        {
            $smstpl_status = intval($CMS->input['smstpl_status']);
            $this->sql_add .= " smstpl_status = {$smstpl_status} AND ";
        }

        if(isset($CMS->input['smstpl_name']))
        {
            $smstpl_name = urldecode($CMS->input['smstpl_name']);
            $this->sql_add .= " smstpl_name LIKE '%{$smstpl_name}%' AND ";
        }

        if(isset($CMS->input['smstpl_content']))
        {
            $smstpl_content = urldecode($CMS->input['smstpl_content']);
            $this->sql_add .= " smstpl_content LIKE '%{$smstpl_content}%' AND ";
        }
		
		// Create SQL Query for listing Data
		list($CMS->show_page, $results) = $DB->fetch_listing("SELECT * FROM ".root_table."sms_template WHERE {$this->sql_add} 1=1 ORDER BY {$default_field} {$default_order}", 20, '', '', $CMS->input['page'], 'sms_template');

        return $results;
	}

	public function html($data = [])
	{
		global $CMS, $DB, $member;

		$this->loadhtml();

		// Display Header
		$output .= $this->html->header();
		
		if ( $data )
		{
			foreach( $data as $result )
			{
				// Convert info
				$result = $CMS->smstpl->convertvalue($result);
				
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
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=smstpl");
		}
		
		return $output;
	}
	
	public function ajax()
	{
		global $CMS, $DB;
		
		$smstpl_id = intval($CMS->input['smstpl_id']);

		$data = $DB->fetch_data("SELECT * FROM ".root_table."sms_template WHERE smstpl_id='{$smstpl_id}' AND smstpl_deleted=0 LIMIT 1", 'sms_template')[0];

		print "{$data['smstpl_name']}||{$data['smstpl_content']}";
		
		exit;
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
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_smstpl_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_smstpl_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";

			// Check permission to Delete
			if ( $CMS->permit["smstpl_delete"] == true )
			{
				$data .= "<option value='delete_all'>{$CMS->lang['smstpl_action_delete']}</option>";
				$this->control = 1;
			}
			
			$data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

			$CMS->class->cache->save("user_{$member['user_id']}_smstpl_controller_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}
		
		if ( $CMS->vars['action_controller']  OR $CMS->permit["smstpl_search"] == 1 )
		{
			$this->action_control = $this->html->control();
		}
	}
	
	public function data()
	{
		global $CMS, $DB;

		$sql = "SELECT * FROM ".root_table."sms_template WHERE smstpl_deleted=0";

		$results = $DB->fetch_data($sql, 'sms_template');
		
		$output = "";

		if(!$results) return $output;

		foreach ( $results as $cat )
		{
			$output .= "<option value='{$cat['smstpl_id']}'>{$cat['smstpl_name']}</option>";
		}

		$this->html_data = $output;
	}
	
	public function load_sms()
	{
		global $CMS, $DB;

        $sql = "SELECT * FROM ".root_table."sms_template WHERE smstpl_deleted=0";

        $results = $DB->fetch_data($sql, 'sms_template');

        $output = "";

        if(!$results) return $output;

        foreach ( $results as $cat )
		{
			$output .= "<option value='{$cat['smstpl_code']}'>{$cat['smstpl_name']}</option>";
		}
		
		return $output;
	}
	
	//===========================================================================
	//  DATA
	//===========================================================================

	public function defaultvalue($data)
	{
		global $CMS, $member;
		
		$data['smstpl_protected'] = $data['smstpl_protected'] ? $data['smstpl_protected'] : 1;
		$data['smstpl_status'] = $data['smstpl_status'] ? $data['smstpl_status'] : 1;

		return $data;
	}
	
	public function convertvalue($data)
	{
		global $CMS, $DB;
	
		$data['smstpl_protected'] = $CMS->lang["answer_{$data['smstpl_protected']}"];
		$data['smstpl_status'] = $CMS->lang["answer_{$data['smstpl_status']}"];
		
		// Replace search content
		$data = $CMS->class->search->convertvalue($data);
		
		// Convert unix time to GMT time
		$data['smstpl_time'] = $CMS->class->date->date_format( $data['smstpl_time'], 1 );
		$data['smstpl_time_update'] = $data['smstpl_time_update'] ? $CMS->class->date->date_format( $data['smstpl_time_update'], 1 ) : "";
	
		// Check permission to read Info
		if ( $CMS->permit["smstpl_read"] == true )
		{
			$data['smstpl_name'] = "<a href='{$CMS->vars['root_domain']}/?site=smstpl&act=show&id={$data['smstpl_id']}'>{$data['smstpl_name']}</a>";
		}

		// Bgcolor
		$data['bgcolor'] = $this->record_cnt % 2 != 0 ? "#FFFFFF" : "#F6F6F6";

		// Check permission to read Info
		$user_name = $CMS->user->get_info($data['user_id'],"user_display_name");
		
		if ( $CMS->permit["user_read"] == true )
		{
			$data['user_id'] = "<a href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}'>{$user_name}</a>";
		}
		else
		{
			$data['user_id'] = $user_name;
		}

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
		$data['smstpl_time'] = $CMS->class->date->date_format( $data['smstpl_time'], 1 );
		$data['smstpl_time_update'] = $data['smstpl_time_update'] ? $CMS->class->date->date_format( $data['smstpl_time_update'], 1 ) : "";

		// Check permission to read Info
		$user_name = $CMS->user->get_info($data['user_id'],"user_display_name");
		
		if ( $CMS->permit["user_read"] == true )
		{
			$data['user_id'] = "<a href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}'>{$user_name}</a>";
		}
		else
		{
			$data['user_id'] = $user_name;
		}

		// Display
		$data['smstpl_status'] = $CMS->lang["display_{$data['smstpl_status']}"];
		
		return $data;
	}
	
	//===========================================================================
	//  INFO
	//===========================================================================
	
	public function get_info( $record_id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "smstpl" )
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
		
		// Load cache
		//if ( isset($this->cache[$record_id]) )
		//{
		//	$data = $this->cache[$record_id];

		// Continue
		$data = $DB->fetch_data("SELECT * FROM ".root_table."sms_template WHERE (smstpl_id='{$record_id}' OR smstpl_code='{$record_id}') AND smstpl_deleted=0 ORDER BY smstpl_id DESC LIMIT 1", 'sms_template')[0];

		if ( $data )
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

		$sql = "SELECT COUNT(0) cnt FROM ".root_table."sms_template WHERE {$sql_add} {$field}='{$value}' AND smstpl_deleted=0 LIMIT 1";
	
		return $DB->fetch_data($sql, sms_template)[0]['cnt'];
	}

	//===========================================================================
	//  ADD
	//===========================================================================
	
	public function add()
	{
		global $CMS, $DB, $member;

		// User input
		$smstpl_name = $CMS->input['smstpl_name'];
		$smstpl_content = $CMS->class->editor->input("smstpl_content");
		$smstpl_note = $CMS->input['smstpl_note'];
		$smstpl_time = time();
		$smstpl_code = $CMS->input['smstpl_code'];
		$user_id = $member['user_id'];
//		$smstpl_protected = $CMS->input['smstpl_protected'];
		$smstpl_protected = 0;
//		$smstpl_status = $CMS->input['smstpl_status'];
		$smstpl_status = 1;
        $smstpl_required_keys = $CMS->input['smstpl_required_keys'];

		// Check input
		if ( ! $smstpl_name ) { $_SESSION['msg'] = "{$CMS->lang['smstpl_incomplete_name']}"; return false; }
		
		if ( ! $smstpl_content ) { $_SESSION['msg'] = "{$CMS->lang['smstpl_incomplete_content']}"; return false; }
		
		if ( ! $smstpl_code ) { $_SESSION['msg'] = "{$CMS->lang['smstpl_incomplete_code']}"; return false; }
		
		if ( $this->check_exist("smstpl_code", $smstpl_code) == true ) { $_SESSION['msg'] = "{$CMS->lang['smstpl_code_exist']}"; return false; }

        /*if(!$this->checkRequiredKeys($smstpl_content, $smstpl_required_keys))
        {
            return false;
        }
        else*/
        {
            $smstpl_required_keys = array_values($smstpl_required_keys);
            $smstpl_required_keys = array_unique($smstpl_required_keys);
            $smstpl_required_keys = @json_encode($smstpl_required_keys, JSON_UNESCAPED_UNICODE);
        }

		// Insert data
		$DB->query("INSERT INTO ".root_table."sms_template (smstpl_name, smstpl_content, smstpl_note, smstpl_time, smstpl_code, user_id, smstpl_protected, smstpl_status, smstpl_required_keys) VALUES ('{$smstpl_name}', '{$smstpl_content}', '{$smstpl_note}', '{$smstpl_time}', '{$smstpl_code}', '{$user_id}', '{$smstpl_protected}', '{$smstpl_status}', '{$smstpl_required_keys}')");

        //Clear cache
        $CMS->class->cache->mdelete('sms_template');
		
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['smstpl_added']} <b>{$smstpl_name}</b>")."<br />";

		// Get info
		$smstpl = $this->get_info($smstpl_code);

		return $smstpl;
	}
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		// Get info
		$smstpl = $this->get_info();

		// User input
		$smstpl_name = $CMS->input['smstpl_name'];
		$smstpl_content = $CMS->class->editor->input("smstpl_content");
		$smstpl_note = $CMS->input['smstpl_note'];
		$smstpl_time_update = time();
//		$smstpl_code = $CMS->input['smstpl_code'];
//		$smstpl_protected = $CMS->input['smstpl_protected'];
		$smstpl_protected = 0;
//		$smstpl_status = $CMS->input['smstpl_status'];
		$smstpl_status = 1;
        $smstpl_required_keys = $CMS->input['smstpl_required_keys'];

		// Check input
		if ( ! $smstpl_name ) { $_SESSION['msg'] = "{$CMS->lang['smstpl_incomplete_name']}"; return false; }
		
		if ( ! $smstpl_content ) { $_SESSION['msg'] = "{$CMS->lang['smstpl_incomplete_content']}"; return false; }

		/*if ( $this->check_exist("smstpl_code", $smstpl_code, $smstpl['smstpl_code']) == true ) { $_SESSION['msg'] = "{$CMS->lang['smstpl_code_exist']}"; return false; }*/

        /*if(!$this->checkRequiredKeys($smstpl_content, $smstpl_required_keys))
        {
            return false;
        }
        else*/
        {
            $smstpl_required_keys = array_values($smstpl_required_keys);
            $smstpl_required_keys = array_unique($smstpl_required_keys);
            $smstpl_required_keys = @json_encode($smstpl_required_keys, JSON_UNESCAPED_UNICODE);
        }

		// Step 1: Save detail logs
		$CMS->class->logs->old_data = $smstpl;
		
		// Update info
		$DB->query("UPDATE ".root_table."sms_template SET smstpl_name='{$smstpl_name}', smstpl_content='{$smstpl_content}', smstpl_note='{$smstpl_note}', smstpl_time_update='{$smstpl_time_update}', smstpl_protected='{$smstpl_protected}', smstpl_status='{$smstpl_status}' WHERE smstpl_id='{$smstpl['smstpl_id']}'");

        //Clear cache
        $CMS->class->cache->mdelete('sms_template');

		$CMS->class->logs->key = "smstpl_{$smstpl['smstpl_id']}";
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['smstpl_edited']} <b>{$smstpl_name}</b>")."<br />";
		
		// Get info
		$smstpl = $this->get_info();

		// Step 2: Save detail logs
		$CMS->class->logs->key = "smstpl_{$smstpl['smstpl_id']}";
		$CMS->class->logs->save_detail("sms_template",$smstpl['smstpl_id'],$smstpl);
		
		
		return $smstpl;
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
		
		if ( $data['smstpl_protected'] == 1 )
		{
			// Alert
			$_SESSION["msg"] .= $CMS->lang['smstpl_delete_failed'];
		}
		else
		{
			// Update info
			$DB->query("UPDATE ".root_table."sms_template SET smstpl_deleted=1 WHERE smstpl_id={$data['smstpl_id']}");
		
			// Create log
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['smstpl_deleted']} <b>{$data['smstpl_name']}</b>")."<br />";
		}

        //Clear cache
        $CMS->class->cache->mdelete('sms_template');
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=smstpl&page={$CMS->input['page']}");
		
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

				if ( $data['smstpl_protected'] == 1 )
				{
					$_SESSION["msg"] .= $CMS->lang['smstpl_delete_failed'];
				}
				else
				{
					$DB->query("UPDATE ".root_table."sms_template SET smstpl_deleted=1 WHERE smstpl_id={$id}");

					$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['smstpl_deleted']} <b>{$data['smstpl_name']}</b>")."<br />";
					
					$deleted = 1;
				}
			}
		}
		
		if ( $deleted == 0 )
		{
            //Clear cache
            $CMS->class->cache->mdelete('sms_template');

			$_SESSION["msg"] .= "{$CMS->lang['smstpl_delete_failed']}";
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
		$CMS->class->search->mod_name = "smstpl";
		$CMS->class->search->table_name = "sms_template";
		$CMS->class->search->fields_type = array("smstpl_time" => "time");

		// Output
		$data = $CMS->class->search->get_info();
		
		// Update SQL Query
		$this->sql_add .= $data;
		
		// Get List
		return $this->listing();
	}

	function renderContent($tmpCode = "", $data = [])
    {
        global $CMS;

        $data = !$data ? [] : $data;

        $text = '';

        $template = $this->get_info($tmpCode);

        if($template)
        {

            $text = $template['smstpl_content'];

            $text = preg_replace("/{{([a-zA-Z0-9\_]+?)}}/i", "\$data[\\1]", $text);

            // Remove slash
            $text = str_replace("\"", "\\\"", $text);

            eval("\$text = \"$text\";");
        }

        return $text;
    }

    function autocomplete()
    {
        global $CMS, $DB;

        $this->per_page = 3;

        $data = $this->listing();

        $li = "";

        $return = [
            "status" => "error",
            "msg" => "Không tìm thấy dữ liệu!"
        ];


        if($data)
        {
            foreach($data as $smstpl)
            {
                $li .= "<li><a href='{$CMS->vars['root_domain']}/?site=smstpl&act=show&id={$smstpl['smstpl_id']}'>{$smstpl['smstpl_name']}</a></li>";
            }

            if($CMS->class->page->total_row > 3)
            {
                $li .= "<li class='see_more'><a href='{$CMS->vars['root_domain']}/?site=smstpl&act=search&keyword={$CMS->input['keyword']}' >Xem thêm ({$CMS->class->page->total_row}) kết quả</a></li>";
            }

            $return = [
                "status" => "success",
                "data_option" => $li
            ];
        }

        echo @json_encode($return); exit;
    }

    function checkRequiredKeys($content, $required_keys = [])
    {
        global $CMS;

        $flag = true;

        $required = [];

        if( is_array($required_keys) && count($required_keys))
        {
            foreach ($required_keys as $required_key)
            {
                if(!preg_match('/({{'.$required_key.'}})/', $content))
                {
                    $required[] = $required_key;
                    $flag = false;
                }
            }
        }

        if($flag == false)
        {
            $_SESSION['msg'] = $CMS->lang["invalid_required_keys"].': '.implode(', ', $required);
        }
        return $flag;
    }

    /**
     * Export to excel file
     * @return string
     */
    public function exportToExcel()
    {
        global $CMS, $DB, $member;

        $setTitle = [
            'Name',
            'Note',
            'Content',
            'Code',
            'Display'
        ];

        \models\report::excel_header();

        // Set style
        $style = array(
            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical'   => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation'   => 0,
            'wrap'       => TRUE
        );

        /**
         * Fields to export
         * Note: muste by match with header cols (order and numbers)
         */
        $fields = 'smstpl_name,smstpl_note,smstpl_content,smstpl_code,smstpl_status';

        // Query data
        $sql = "SELECT {$fields} FROM ".root_table."sms_template WHERE smstpl_deleted=0 ORDER BY smstpl_id ASC";

        $results = $DB->fetch_data($sql, 'sms_template');

        $count_row = count(sms_template) + 1;// + 1 row title

        // Set style excel
        \models\report::excel_title($setTitle, $count_row, $style);

        // Loop data
        $i = 2;

        //Range char A-Z
        $rangeChar = range('A', 'Z') ;
        $fields = explode(',', $fields);

        if($results)
        {
            foreach ($results as $result)
            {
                $result['smstpl_status'] = $result['smstpl_status'] ? 'Show' : 'Hide';

                foreach ($rangeChar as $charKey => $char)
                {
                    /**
                     * Set values to cell by chars(A-Z) and fields from database
                     */
                    $field = $fields[$charKey];
                    $result[$field] = html_entity_decode($result[$field]);
                    \models\report::$dataExcel->getActiveSheet()->setCellValue($char.$i, $result[$field]);

                };

                $i++;
            }
        }

        // Set name file
        $file_name = "sms_templates_u{$member['user_id']}.xls";

        // Create file and return link download
        return \models\report::excel_output($file_name);
    }

    /**
     * Import data from excel file
     * @return bool
     */
    function importFromExcel()
    {
        global $CMS, $DB, $member;

        // require file
        require_once root_path."vendor/autoload.php";

        //Range char A-Z
        $rangeChar = range('A', 'Z') ;

        // Input
        $file_tmp = isset($_FILES['upload_file']['tmp_name']) ? $_FILES['upload_file']['tmp_name'] : "";
        $file_name = isset($_FILES['upload_file']['name']) ? $_FILES['upload_file']['name'] : "";

        // Check file allow
        $file_ext = $CMS->class->attachment->get_ext( $file_name );
        $arr_allow = array("xls","xlsx");

        if(!in_array($file_ext, $arr_allow))
        {
            $_SESSION['error_msg'] = $CMS->lang['error_ext_file_upload'];
            return false;
        }

        // Khoi tao
        $objPHPExcel = \PHPExcel_IOFactory::load($file_tmp);

        $highestColumn = $objPHPExcel->getActiveSheet()->getHighestColumn(); //Cột cuối cùng
        $highestRow         = $objPHPExcel->getActiveSheet()->getHighestRow(); // e.g. 10

        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);

        //Reset style
        $styleDefault = array(
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_NONE,
            ),
            'font'  => array(
                'bold'  => false,
                'color' => array('rgb' => '000000'))
        );

        $objPHPExcel->getActiveSheet()->getStyle("A2:{$highestColumn}{$highestRow}")->applyFromArray($styleDefault);

        //Danh sách thứ tự các field tương ứng với thứ tự cột từ file excel
        $fields_text = 'smstpl_name,smstpl_note,smstpl_content,smstpl_code,smstpl_status';
        $fields = explode(',', $fields_text);

        $errStyleCell = array(
//            'borders' => array(
//                'allborders' => array(
//                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
//                    'color' => array('rgb' => 'FF0000')
//                )
//            ),
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'E0E0E0')
            )
        ); //Style đánh dấu ô có lỗi

        $errStyleRow = array(
            'font'  => array(
//                'bold'  => true,
                'color' => array('rgb' => 'FF0000'),
            )); //Style đánh dấu dòng có lỗi

        $sql_values = ""; //values for multi insert

        $validData = []; //Mang chua cac dong hop le

        /**
         * Fields is required
         */
        $checkRequired = ['smstpl_name', 'smstpl_code', 'smstpl_content'];

        /**
         * Marked error position
         */
        $errorPositions = [];

        /**
         * Tpl key list to check existed
         */
        $tplKeyList = [];

        /**
         * Loop to check validate
         */
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
        {
            // Loop row
            for ($row = 1; $row <= $highestRow; ++ $row)
            {
                if($row == 1) { continue;}
                $data = [];

                // Loop col
                for ($col = 0; $col < $highestColumnIndex; ++$col)
                {
                    $cell = $worksheet->getCellByColumnAndRow($col, $row);
                    if($fields[$col])
                    {
                        $data[$fields[$col]] = $CMS->class->editor->input($cell->getValue(), "text"); //Gan du lieu lai theo giong field trong DB

                        /**
                         * Check required
                         */
                        if(in_array($fields[$col], $checkRequired))
                        {
                            if($data[$fields[$col]] == '')
                            {
                                $errorPositions[$row][] = $col;
                            }
                        }

                        if($fields[$col] == 'smstpl_code')
                        {
                            if($data[$fields[$col]]!='')
                            {
                                $tplKeyList[$data[$fields[$col]]][] = ['col' => $col, 'row' => $row];
                            }
                        }
                    }
                }

                if(!isset($errorPositions[$row]))
                {
                    $validData[$row] = $data;
                }
            }
        }


        //Duyet danh sach key de check trung
        foreach ($tplKeyList as $tplCode => $errorPos)
        {

            if(!$CMS->input['is_overwrite'])
            {
                if(count($errorPos)>1 || $this->check_exist('smstpl_code', $tplCode))
                {
                    foreach ($errorPos as $errColRows)
                    {
                        $errorPositions[$errColRows['row']][] = $errColRows['col'];
                    }
                }
            }

            //Xóa bỏ các dòng trung key, chỉ giữ lại dòng cuối
            if(($cnt = count($errorPos)) >= 2)
            {
                for ($i=0; $i<$cnt-1; $i++)
                {
                    unset($validData[$errorPos[$i]['row']]);
                }
            }
        }


        if($errorPositions)
        {
            foreach ($errorPositions as $errRow => $errCols)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension();

                //Đánh dấu dòng lỗi
                $objPHPExcel->getActiveSheet()->getStyle("A{$errRow}:{$highestColumn}{$errRow}")->applyFromArray($errStyleRow);

                foreach ($errCols as $errCol)
                {
                    //Đánh dấu các ô bị lỗi
                    $colName = $rangeChar[$errCol];
                    $objPHPExcel->getActiveSheet()->getStyle("{$colName}{$errRow}")->applyFromArray($errStyleCell);
                }
            }

            /**
             * Trả về file highlight các dòng bị lỡi cho khách sửa lại
             */
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $tmpFile = "import_sms_tpl_C{$member['user_id']}.xls";
            $objWriter->save("{$CMS->vars['upload_dir']}/excel/{$tmpFile}");
            $_SESSION['error_msg'] = "{$CMS->lang['invalid_import_data']} <a href='{$CMS->vars['upload_url']}/excel/{$tmpFile}'>Download file</a>";
            $CMS->global->redirectReferer();
        }

        /**
         * Loop dữ liệu để vào DB
         */
        foreach ($validData as $row => $data)
        {
            $data['smstpl_status'] = strtolower($data['smstpl_status']) == 'show' ? 1 : 0;
            $data['smstpl_time'] = $data['smstpl_time_update'] = time();
            $data['user_id'] = $member['user_id'];

            if($this->check_exist('smstpl_code', $data['smstpl_code']))
            {
                /**
                 * Update record
                 */
                $sql_update = "UPDATE ".root_table."sms_template SET ";

                foreach ($data as $field => $value)
                {
                    //Create values sql
                    $sql_update .= "{$field}='{$value}',";
                }

                $sql_update = trim($sql_update,',');

                $sql_update .= " WHERE smstpl_code='{$data['smstpl_code']}'";

                $DB->query($sql_update);
            }
            else
            {
                /**
                 * Multi insert
                 */
                $sql_values .= "(";
                foreach ($data as $field => $value)
                {
                    //Create values sql
                    $sql_values .= "'{$value}',";
                }
                $sql_values = trim($sql_values,',');
                $sql_values .= "),";
            }
        }

        $sql_values = trim($sql_values,',');

        /**
         * Lay danh sach field de insert
         */
        $field_list = array_keys($data);
        $field_list = implode(',',$field_list);

        $sql = "INSERT INTO ".root_table."sms_template ({$field_list}) VALUES {$sql_values}";

        $DB->query($sql);

        $_SESSION['msg'] = $CMS->lang['import_file_success'];

        $CMS->global->redirectReferer();

    }
}

?>