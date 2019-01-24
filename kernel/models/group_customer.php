<?php

use lib\input;

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->group_customer = new ClassGroupCustomer;
class ClassGroupCustomer {
	public $per_page = 20;
	public $show_page = '';
	public $sql_query = '';
	public $arrange_data = '';
	public $prefix_html = "";
	public $suffix_html = "";

    /**
     * @var string $cache_prefix
     */
	public $cache_prefix = 'group_customer';

	public function listing($sql_add = '') {
		global $CMS, $DB, $member;
		$this->arrange_data = trim("gc_id,gc_name,gc_status,gc_time");
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "gc_id";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "DESC";

        $CMS->input['gc_name'] = urldecode($CMS->input['gc_name']);
		if($CMS->input['gc_name'] != '')
        {
            $sql_add .= " gc_name LIKE '%{$CMS->input['gc_name']}%' AND";
        }

        $sql = "SELECT GC.*, COUNT(cus_id) cus_nums FROM `".root_table."group_customer` GC LEFT JOIN (SELECT cus_id, cus_group FROM ".root_table."customer WHERE cus_deleted=0) C ON gc_id=cus_group WHERE {$sql_add} `gc_deleted`=0 GROUP BY gc_id ORDER BY {$default_field} {$default_order}";

		list($this->show_page, $results) = $DB->fetch_listing($sql,$this->per_page,$this->prefix_html,$this->suffix_html,$CMS->input['page'],$this->cache_prefix);

		if($results)
        {
            foreach ($results as $result)
            {
                $arr[] = $this->convertvalue($result);
            }
        }

		return $arr;
	}
	public function convertvalue($data=null) {
		global $CMS, $DB, $member;
		$data['gc_time_c'] = $CMS->class->date->date_format($data['gc_time'],1);
		switch ($data['gc_status']) {
			case '1':
				$data['gc_status_c'] = '<i class="font-icon font-icon-check-bird color-green"></i> '.$CMS->lang['gc_status_01'];
				break;
			default:
				$data['gc_status_c'] = '<i class="font-icon font-icon-close-2 color-red"></i> '.$CMS->lang['gc_status_00'];
				break;
		}
		return $data;
	}

	public function getInfo($record_id=0,$field_name='*') {
		global $CMS, $DB, $member;

		$data = null;

		$sql = "SELECT {$field_name} FROM `".root_table."group_customer` WHERE (`gc_id`='{$record_id}' OR `gc_name`='{$record_id}') AND `gc_deleted`=0 LIMIT 1";

		if(!$record_id) return false;

        $data = $DB->fetch_data($sql, $this->cache_prefix)[0];

        if ($field_name !== '*') {
            $data = $data[$field_name];
        }

        return $data;
	}

	public function add($gc_name=null,$gc_status=null) {
		if (!empty($gc_name)&& !empty($gc_status)) {
			global $CMS, $DB, $member;
			$DB->query("INSERT INTO `".root_table."group_customer` (`gc_time`, `gc_name`, `gc_status`) VALUES ('".time()."','{$gc_name}','{$gc_status}')");

			//Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$id = $DB->last_insert_id();
			$CMS->class->logs->insert("Add group customer <b>{$gc_name} ({$id})</b>");
			return $id;
		}
		return false;
	}

	public function edit($id=null,$gc_name=null,$gc_status=null) {
		if (!empty($id)&& !empty($gc_name)&& !empty($gc_status)) {
			global $CMS, $DB, $member;
			$DB->query("UPDATE `".root_table."group_customer` SET `gc_name`='{$gc_name}',`gc_status`='{$gc_status}' WHERE `gc_id`='{$id}'");

            //Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$CMS->class->logs->insert("Edit group customer <b>{$gc_name} ({$id})</b>");
		}
		return false;
	}

	public function deleted($id=null) {
		if (!empty($id)) {
			global $CMS, $DB, $member;
			$DB->query("UPDATE `".root_table."group_customer` SET `gc_deleted`=1 WHERE `gc_id`='{$id}'");

            //Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$CMS->class->logs->insert("Deleted group customer <b>{$id}</b>");
		}
		return false;
	}

	public function getAll($sql_add = '') {
		global $CMS, $DB, $member;

		$sql = "SELECT * FROM `".root_table."group_customer` WHERE {$sql_add} `gc_deleted`=0 ORDER BY `gc_id` DESC";

		$results = $DB->fetch_data($sql, $this->cache_prefix);

		$arr = array();
		if ($results) {
			foreach($results as $result) {
				array_push($arr, $result);
			}
		}

		return $arr;
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
        $fields = 'gc_name,gc_status';

        // Query data
        $sql = "SELECT {$fields} FROM ".root_table."group_customer WHERE gc_deleted=0 ORDER BY gc_id ASC";

        $data = $DB->fetch_data($sql,$this->cache_prefix);

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
            $result['gc_status'] = $result['gc_status'] ? 'Show' : 'Hide';

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
        $file_name = "group_customers_u{$member['user_id']}.xls";

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
        $fields_text = 'gc_name,gc_status';
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
        $checkRequired = ['gc_name'];

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

                        if($fields[$col] == 'gc_name')
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
                if(count($errorPos)>1 || $this->getInfo($tplCode))
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
            $tmpFile = "import_group_customer_C{$member['user_id']}.xls";
            $objWriter->save("{$CMS->vars['upload_dir']}/excel/{$tmpFile}");
            $_SESSION['error_msg'] = "{$CMS->lang['invalid_import_data']} <a href='{$CMS->vars['upload_url']}/excel/{$tmpFile}'>Download file</a>";
            $CMS->global->redirectReferer();
        }

        /**
         * Loop dữ liệu để vào DB
         */
        foreach ($validData as $row => $data)
        {
            $data['gc_status'] = strtolower($data['gc_status']) == 'show' ? 1 : 0;
            $data['gc_time'] = time();

            if($this->getInfo($data['gc_name']))
            {
                /**
                 * Update record
                 */
                $sql_update = "UPDATE ".root_table."group_customer SET ";

                foreach ($data as $field => $value)
                {
                    //Create values sql
                    $sql_update .= "{$field}='{$value}',";
                }

                $sql_update = trim($sql_update,',');

                $sql_update .= " WHERE gc_name='{$data['gc_name']}'";

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

        $sql = "INSERT INTO ".root_table."group_customer ({$field_list}) VALUES {$sql_values}";

        $DB->query($sql);

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

        $_SESSION['msg'] = $CMS->lang['import_file_success'];

        $CMS->global->redirectReferer();

    }
}
?>