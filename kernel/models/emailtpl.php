<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->emailtpl = new class_emailtpl;

class class_emailtpl {

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

    /**
     * @var string $cache_prefix
     */
	public $cache_prefix = 'email_template';

    /**
     * Paging html
     * @var string $show_page
     */
	public $show_page = '';

	public $per_page = 20;

	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	
	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			$this->html = $CMS->class->template->load_template("skin_emailtpl");		
		}
	}
	
	public function listing()
	{
		global $CMS, $DB;
		
		// Update Arrange Data
		$this->arrange_data = trim("emailtpl_id,emailtpl_title,emailtpl_status");
		
		// Set default for Arrange
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "emailtpl_time";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "desc";
		
		// SQL Condition
		$this->sql_add .= " emailtpl_deleted=0 AND ";	
		
		// Create SQL Query for listing Data
        $sql = "SELECT * FROM ".root_table."email_template WHERE {$this->sql_add} 1=1 ORDER BY {$default_field} {$default_order}";
 
        list($this->show_page, $data) = $DB->fetch_listing($sql,$this->per_page,$this->prefix_html,$this->suffix_html,$CMS->input['page'],$this->cache_prefix);
 
        return $data;
	}

	public function html($data)
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
				$result = $CMS->emailtpl->convertvalue($result);
				
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
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=emailtpl");
		}
		
		return $output;
	}
	
	public function ajax()
	{
		global $CMS, $DB;
		
		$emailtpl_id = intval($CMS->input['emailtpl_id']);

		$sql = $DB->query("SELECT * FROM ".root_table."email_template WHERE emailtpl_id='{$emailtpl_id}' AND emailtpl_deleted=0");
		
		$data = $DB->fetch_array( $sql );
		
		print "{$data['emailtpl_title']}||{$data['emailtpl_content']}";
		
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
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_emailtpl_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_emailtpl_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";

			// Check permission to Delete
			if ( $CMS->permit["emailtpl_delete"] == true )
			{
				$data .= "<option value='delete_all'>{$CMS->lang['emailtpl_action_delete']}</option>";
				$this->control = 1;
			}
			
			$data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

			$CMS->class->cache->save("user_{$member['user_id']}_emailtpl_controller_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}
		
		if ( $CMS->vars['action_controller']  OR $CMS->permit["emailtpl_search"] == 1 )
		{
			$this->action_control = $this->html->control();
		}
	}
	
	public function data()
	{
		global $CMS, $DB;

		$sql = "SELECT * FROM ".root_table."email_template WHERE emailtpl_deleted=0";

		$results = $DB->fetch_data($sql, $this->cache_prefix);

		$output = '';

		if($results)
        {
            foreach ( $results as $cat )
            {
                $output .= "<option value='{$cat['emailtpl_id']}'>{$cat['emailtpl_name']}</option>";
            }
        }

		$this->html_data = $output;
	}
	
	public function load_email()
	{
		global $CMS, $DB;
		
		$sql = $DB->query("SELECT * FROM ".root_table."email_template WHERE emailtpl_deleted=0");
		
		$output = "";
		
		while ( $cat = $DB->fetch_array() )
		{
			$output .= "<option value='{$cat['emailtpl_code']}'>{$cat['emailtpl_name']}</option>";
		}
		
		return $output;
	}
	
	//===========================================================================
	//  DATA
	//===========================================================================

	public function defaultvalue($data)
	{
		global $CMS, $member;
		
		$data['emailtpl_protected'] = $data['emailtpl_protected'] ? $data['emailtpl_protected'] : 1;
		$data['emailtpl_status'] = $data['emailtpl_status'] ? $data['emailtpl_status'] : 1;

		return $data;
	}
	
	public function convertvalue($data)
	{
		global $CMS, $DB;
	
		$data['emailtpl_protected'] = $CMS->lang["answer_{$data['emailtpl_protected']}"];
		$data['emailtpl_status'] = $CMS->lang["answer_{$data['emailtpl_status']}"];
		
		// Replace search content
		$data = $CMS->class->search->convertvalue($data);
		
		// Convert unix time to GMT time
		$data['emailtpl_time'] = $CMS->class->date->date_format( $data['emailtpl_time'], 1 );
		$data['emailtpl_time_update'] = $data['emailtpl_time_update'] ? $CMS->class->date->date_format( $data['emailtpl_time_update'], 1 ) : "";
	
		// Check permission to read Info
		if ( $CMS->permit["emailtpl_read"] == true )
		{
			$data['emailtpl_name'] = "<a href='{$CMS->vars['root_domain']}/?site=emailtpl&act=show&id={$data['emailtpl_id']}'>{$data['emailtpl_name']}</a>";
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
		$data['emailtpl_time'] = $CMS->class->date->date_format( $data['emailtpl_time'], 1 );
		$data['emailtpl_time_update'] = $data['emailtpl_time_update'] ? $CMS->class->date->date_format( $data['emailtpl_time_update'], 1 ) : "";

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
		$data['emailtpl_status'] = $CMS->lang["display_{$data['emailtpl_status']}"];
		
		return $data;
	}
	
	//===========================================================================
	//  INFO
	//===========================================================================
	
	public function get_info( $record_id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "emailtpl" )
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

        $sql = "SELECT * FROM ".root_table."email_template WHERE (emailtpl_id='{$record_id}' OR emailtpl_title='{$record_id}' OR emailtpl_code='{$record_id}') AND emailtpl_deleted=0 ORDER BY emailtpl_id DESC LIMIT 1";

		$results = $DB->fetch_data($sql, $this->cache_prefix);

        $data = isset($results[0]) ? $results[0] : null;

        if(!$data) return false;

        $data['emailtpl_content'] = str_replace(['<style type="text/css"><!--','<style><!--',"<style type='text/css'><!--",'--></style>'],['<style>','<style>','<style>','</style>'],$data['emailtpl_content']);

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

	public function check_exist( $field, $value = "", $except_value = "" )
	{
		global $CMS, $DB, $member;
		
		if ( ! $field )
		{
			return true;
		}
		
		if ( $except_value )
		{
			$sql = "SELECT count(emailtpl_id) cnt FROM ".root_table."email_template WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND emailtpl_deleted=0";
		}
		else
		{
			$sql = "SELECT count(emailtpl_id) cnt FROM ".root_table."email_template WHERE {$field}='{$value}' AND emailtpl_deleted=0";
		}

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
		$emailtpl_from = $CMS->input['emailtpl_from'];
		$emailtpl_fromname = $CMS->input['emailtpl_fromname'];
		$emailtpl_name = $CMS->input['emailtpl_name'];
		$emailtpl_title = $CMS->input['emailtpl_title'];
		$emailtpl_content = $CMS->class->editor->input("emailtpl_content");
		$emailtpl_note = $CMS->input['emailtpl_note'];
		$emailtpl_time = time();
		$emailtpl_code = $CMS->input['emailtpl_code'];
		$user_id = $member['user_id'];
		$emailtpl_protected = $CMS->input['emailtpl_protected'];
		$emailtpl_status = $CMS->input['emailtpl_status'];

		// Check input
		if ( ! $emailtpl_name ) { $CMS->errormsg = "{$CMS->lang['emailtpl_incomplete_name']}"; return false; }
		
		if ( ! $emailtpl_title ) { $CMS->errormsg = "{$CMS->lang['emailtpl_incomplete_title']}"; return false; }
		
		if ( ! $emailtpl_content ) { $CMS->errormsg = "{$CMS->lang['emailtpl_incomplete_content']}"; return false; }
		
		// if ( ! $emailtpl_from ) { $CMS->errormsg = "{$CMS->lang['emailtpl_incomplete_from']}"; return false; }
		
		// if ( ! $emailtpl_fromname ) { $CMS->errormsg = "{$CMS->lang['emailtpl_incomplete_fromname']}"; return false; }
		
		if ( ! $emailtpl_code ) { $CMS->errormsg = "{$CMS->lang['emailtpl_incomplete_code']}"; return false; }
		
		if ( $this->check_exist("emailtpl_code", $emailtpl_code) == true ) { $CMS->errormsg = "{$CMS->lang['emailtpl_code_exist']}"; return false; }

		// Insert data
		$DB->query("INSERT INTO ".root_table."email_template (emailtpl_from, emailtpl_fromname, emailtpl_name, emailtpl_title, emailtpl_content, emailtpl_note, emailtpl_time, emailtpl_code, user_id, emailtpl_protected, emailtpl_status) VALUES ('{$emailtpl_from}', '{$emailtpl_fromname}', '{$emailtpl_name}', '{$emailtpl_title}', '{$emailtpl_content}', '{$emailtpl_note}', '{$emailtpl_time}', '{$emailtpl_code}', '{$user_id}', '{$emailtpl_protected}', '{$emailtpl_status}')");

		//Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['emailtpl_added']} <b>{$emailtpl_title}</b>")."<br />";

		// Get info
		$emailtpl = $this->get_info($emailtpl_code);

		return $emailtpl;
	}
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		// Get info
		$emailtpl = $this->get_info();

		// User input
		$emailtpl_from = $CMS->input['emailtpl_from'];
		$emailtpl_fromname = $CMS->input['emailtpl_fromname'];
		$emailtpl_name = $CMS->input['emailtpl_name'];
		$emailtpl_title = $CMS->input['emailtpl_title'];
		$emailtpl_content = $CMS->class->editor->input("emailtpl_content");
		$emailtpl_note = $CMS->input['emailtpl_note'];
		$emailtpl_time_update = time();
		$emailtpl_code = $CMS->input['emailtpl_code'];
		$emailtpl_protected = $CMS->input['emailtpl_protected'];
		$emailtpl_status = $CMS->input['emailtpl_status'];

		// Check input
		if ( ! $emailtpl_name ) { $CMS->errormsg = "{$CMS->lang['emailtpl_incomplete_name']}"; return false; }
		
		if ( ! $emailtpl_title ) { $CMS->errormsg = "{$CMS->lang['emailtpl_incomplete_title']}"; return false; }
		
		if ( ! $emailtpl_content ) { $CMS->errormsg = "{$CMS->lang['emailtpl_incomplete_content']}"; return false; }
		
		// if ( ! $emailtpl_from ) { $CMS->errormsg = "{$CMS->lang['emailtpl_incomplete_from']}"; return false; }
		
		// if ( ! $emailtpl_fromname ) { $CMS->errormsg = "{$CMS->lang['emailtpl_incomplete_fromname']}"; return false; }
		
		if ( ! $emailtpl_code ) { $CMS->errormsg = "{$CMS->lang['emailtpl_incomplete_code']}"; return false; }

		if ( $this->check_exist("emailtpl_code", $emailtpl_code, $emailtpl['emailtpl_code']) == true ) { $CMS->errormsg = "{$CMS->lang['emailtpl_code_exist']}"; return false; }
		// Step 1: Save detail logs
		$CMS->class->logs->old_data = $emailtpl;
		
		// Update info
		$DB->query("UPDATE ".root_table."email_template SET emailtpl_from='{$emailtpl_from}', emailtpl_fromname='{$emailtpl_fromname}', emailtpl_name='{$emailtpl_name}', emailtpl_title='{$emailtpl_title}', emailtpl_content='{$emailtpl_content}', emailtpl_note='{$emailtpl_note}', emailtpl_time_update='{$emailtpl_time_update}', emailtpl_code='{$emailtpl_code}', emailtpl_protected='{$emailtpl_protected}', emailtpl_status='{$emailtpl_status}' WHERE emailtpl_id='{$emailtpl['emailtpl_id']}'");

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		$CMS->class->logs->key = "emailtpl_{$emailtpl['emailtpl_id']}";
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['emailtpl_edited']} <b>{$emailtpl_title}</b>")."<br />";
		
		// Get info
		$emailtpl = $this->get_info();

		// Step 2: Save detail logs
		$CMS->class->logs->key = "emailtpl_{$emailtpl['emailtpl_id']}";
		$CMS->class->logs->save_detail("email_template",$emailtpl['emailtpl_id'],$emailtpl);
		
		
		return $emailtpl;
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
		
		if ( $data['emailtpl_protected'] == 1 )
		{
			// Alert
			$_SESSION["msg"] .= $CMS->lang['emailtpl_delete_failed'];
		}
		else
		{
			// Update info
			$DB->query("UPDATE ".root_table."email_template SET emailtpl_deleted=1 WHERE emailtpl_id={$data['emailtpl_id']}");

            //Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);
		
			// Create log
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['emailtpl_deleted']} <b>{$data['emailtpl_title']}</b>")."<br />";
		}
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=emailtpl&page={$CMS->input['page']}");
		
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

				if ( $data['emailtpl_protected'] == 1 )
				{
					$_SESSION["msg"] .= $CMS->lang['emailtpl_delete_failed'];
				}
				else
				{
					$DB->query("UPDATE ".root_table."email_template SET emailtpl_deleted=1 WHERE emailtpl_id={$id}");
				
					$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['emailtpl_deleted']} <b>{$data['emailtpl_title']}</b>")."<br />";
					
					$deleted = 1;
				}
			}
		}

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);
		
		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['emailtpl_delete_failed']}";
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
		$CMS->class->search->mod_name = "emailtpl";
		$CMS->class->search->table_name = "email_template";
		$CMS->class->search->fields_type = array("emailtpl_time" => "time");

		// Output
		$data = $CMS->class->search->get_info();
		
		// Update SQL Query
		$this->sql_add .= $data;
		
		// Get List
		return $this->listing();
	}

    /**
     * Export to excel file
     * @return string
     */
    public function exportToExcel()
    {
        global $CMS, $DB, $member;

        $setTitle = [
            'From',
            'Name',
            'Title',
            'Note',
            'Content',
            'Code',
            'Display'
        ];

        \models\report::excel_header();

        // Set style
        $style = array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation'   => 0,
            'wrap'       => TRUE
        );

        /**
         * Fields to export
         * Note: muste by match with header cols (order and numbers)
         */
        $fields = 'emailtpl_from,emailtpl_fromname,emailtpl_name,emailtpl_title,emailtpl_content,emailtpl_code,emailtpl_status';

        // Query data
        $sql = "SELECT {$fields} FROM ".root_table."email_template WHERE emailtpl_deleted=0 ORDER BY emailtpl_id ASC";

        $data = $DB->fetch_data($sql, $this->cache_prefix);

        $count_row = count($data) + 1;// + 1 row title

        // Set style excel
        \models\report::excel_title($setTitle, $count_row, $style);

        // Loop data
        $i = 2;

        //Range char A-Z
        $rangeChar = range('A', 'Z') ;
        $fields = explode(',', $fields);

        foreach ($data as $result)
        {
            $result['emailtpl_status'] = $result['emailtpl_status'] ? 'Show' : 'Hide';

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

        // Set name file
        $file_name = "email_templates_u{$member['user_id']}.xls";

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
        $fields_text = 'emailtpl_from,emailtpl_fromname,emailtpl_name,emailtpl_title,emailtpl_content,emailtpl_code,emailtpl_status';;
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
        $checkRequired = ['emailtpl_name', 'emailtpl_from', 'emailtpl_title', 'emailtpl_code', 'emailtpl_content'];

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

                        if($fields[$col] == 'emailtpl_code')
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
                if(count($errorPos)>1 || $this->check_exist('emailtpl_code', $tplCode))
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
            $tmpFile = "import_email_tpl_C{$member['user_id']}.xls";
            $objWriter->save("{$CMS->vars['upload_dir']}/excel/{$tmpFile}");
            $_SESSION['error_msg'] = "{$CMS->lang['invalid_import_data']} <a href='{$CMS->vars['upload_url']}/excel/{$tmpFile}'>Download file</a>";
            $CMS->global->redirectReferer();
        }

        /**
         * Loop dữ liệu để vào DB
         */
        foreach ($validData as $row => $data)
        {
            $data['emailtpl_status'] = strtolower($data['emailtpl_status']) == 'show' ? 1 : 0;

            if($this->check_exist('emailtpl_code', $data['emailtpl_code']))
            {
                $data['emailtpl_time'] = $data['emailtpl_time_update'] = time();
                $data['user_id'] = $member['user_id'];
                /**
                 * Update record
                 */
                $sql_update = "UPDATE ".root_table."email_template SET ";

                foreach ($data as $field => $value)
                {
                    //Create values sql
                    $sql_update .= "{$field}='{$value}',";
                }

                $sql_update = trim($sql_update,',');

                $sql_update .= " WHERE emailtpl_code='{$data['emailtpl_code']}'";

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

        $sql = "INSERT INTO ".root_table."email_template ({$field_list}) VALUES {$sql_values}";

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

        $DB->query($sql);

        $_SESSION['msg'] = $CMS->lang['import_file_success'];

        $CMS->global->redirectReferer();

    }

}

?>