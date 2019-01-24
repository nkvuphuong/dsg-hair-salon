<?php

if (!defined('IN_ROOT')) {
    print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
    exit();
}

$CMS->accounts_type = new ClassAccountsType;

class ClassAccountsType
{
    public $per_page = 20;
    public $show_page = '';
    public $sql_query = '';
    public $arrange_data = '';
    public $prefix_html = "";
    public $suffix_html = "";
    public $cache_prefix = "accounts_type";

    public function listing()
    {
        global $CMS, $DB, $member;

        foreach($CMS->input as $k => $v)
        {
            $CMS->input[$k] = urldecode($v);
        }

        $this->arrange_data = trim("accounts_type_id,accounts_type_code,accounts_type_name,accounts_type_parent,accounts_type_status,accounts_type_time");
        $default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "accounts_type_id";
        $default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "DESC";

        $where = '';
        if (!empty($CMS->input['at_id']) && Validate::isNum($CMS->input['at_id'])) {
            $where .= " AND `accounts_type_id`='{$CMS->input['at_id']}' ";
        }
        if (!empty($CMS->input['at_name'])) {
            $where .= " AND `accounts_type_name` LIKE '%{$CMS->input['at_name']}%' ";
        }
        if (isset($CMS->input['at_status']) && $CMS->input['at_status']!='' && in_array($CMS->input['at_status'], array(0, 1))) {
            $where .= " AND `accounts_type_status` = '{$CMS->input['at_status']}' ";
        }

        $sql = "SELECT * FROM `" . root_table . "accounts_type` WHERE `accounts_type_deleted`=0 {$where} ORDER BY {$default_field} {$default_order}";

        list($this->show_page, $results) = $DB->fetch_listing($sql, $this->per_page, $this->prefix_html, $this->suffix_html, $CMS->input['page'], $this->cache_prefix);

        $arr = [];

        if($results)
        {
            foreach($results as $result)
            {
                array_push($arr, $this->convertValue($result));
            }
        }

        return $arr;
    }

    public function convertValue($data = null)
    {
        global $CMS, $DB, $member;

        $data['data_bk'] = $data_bk = $data;

        $data['accounts_type_parent_c'] = $this->getInfo($data['accounts_type_parent'], 'accounts_type_name');

        if ($accounts_type_parent_c = @json_decode($data['accounts_type_parent_c'], true)) {
            $data['accounts_type_parent_c'] = $accounts_type_parent_c[$CMS->vars['default_language']];
        }

        switch ($data['accounts_type_status']) {
            case 1:
                $data['accounts_type_status_c'] = $CMS->lang['at_status_01'];
                break;
            default:
                $data['accounts_type_status_c'] = $CMS->lang['at_status_00'];
                break;
        }
        $data['accounts_type_time_c'] = $CMS->class->date->date_format($data['accounts_type_time'], 1);

        $name = @json_decode($data_bk['accounts_type_name'], true);
        $data['accounts_type_name'] = $name ? $name : $data_bk['accounts_type_name'];

        if ($CMS->vars['translations']) {
            //Đa ngôn ngữ
            if (!is_array($data['accounts_type_name'])) {
                //Neu k phai dang mang thi chuyen ve mang
                $name = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName) {
                    $name[$langCode] = $data['accounts_type_name'];
                }

                $data['accounts_type_name'] = $name;
            }
        } else {
            if (is_array($data['accounts_type_name'])) {
                //Neu la dang mang thi chuyen ve dang chuoi binh thuong
                $data['accounts_type_name'] = $data['accounts_type_name'][$CMS->vars['default_language']];
            }
        }

        return $data;
    }

    public function getInfo($record_id = null, $field_name = '*')
    {
        global $CMS, $DB, $member;

        if (!$record_id) return false;

        $sql = "SELECT {$field_name} FROM `" . root_table . "accounts_type` WHERE (`accounts_type_id`='{$record_id}' OR accounts_type_name = '{$record_id}' OR accounts_type_name LIKE '%:\"{$record_id}\"%') AND `accounts_type_deleted`=0 LIMIT 1";

        $data = $DB->fetch_data($sql, $this->cache_prefix)[0];

        if ($field_name !== '*') {
            return $data[$field_name];
        }

        return $data;
    }

    public function add($at_parent = null, $at_code = null, $at_name = null, $at_status = null, $at_group = 0)
    {
        if (!empty($at_name)) {
            global $CMS, $DB, $member;
            $DB->query("INSERT INTO `" . root_table . "accounts_type` (`accounts_type_parent`, `accounts_type_code`, `accounts_type_name`, `accounts_type_status`, `user_id`, `accounts_type_time`, accounts_group) VALUES ('{$at_parent}', '{$at_code}', '{$at_name}', '{$at_status}', '{$member['user_id']}','" . time() . "', '{$at_group}')");

            //Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

            $id = $DB->last_insert_id();
            $CMS->class->logs->insert("Add_accounts_type_{$id}");
            return $id;
        }
        return false;
    }

    public function edit($data_info = null, $at_parent = null, $at_code = null, $at_name = null, $at_status = null, $at_group = 0)
    {
        if (!empty($data_info) && !empty($at_code) && !empty($at_name)) {
            global $CMS, $DB, $member;
            $key = "Edit_accounts_type_{$data_info['accounts_type_id']}";
            $CMS->class->logs->key = $key;
            $CMS->class->logs->old_data = $data_info;
            $CMS->class->logs->insert($key);
            $DB->query("UPDATE `" . root_table . "accounts_type` SET `accounts_type_parent`='{$at_parent}', `accounts_type_code`='{$at_code}', `accounts_type_name`='{$at_name}', `accounts_type_status`='{$at_status}', accounts_group='{$at_group}' WHERE `accounts_type_deleted`='0' AND `accounts_type_id`='{$data_info['accounts_type_id']}'");

            //Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

            $CMS->class->logs->key = $key;
            $CMS->class->logs->save_detail("accounts_type", $data_info['accounts_type_id'], $this->getInfo($data_info['accounts_type_id']));
            return true;
        }
        return false;
    }

    public function deleted($id = null)
    {
        if (!empty($id)) {
            global $CMS, $DB, $member;
            $DB->query("UPDATE `" . root_table . "accounts_type` SET `accounts_type_deleted`=1 WHERE `accounts_type_id`='{$id}'");

            //Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

            $CMS->class->logs->insert("Deleted_accounts_type_{$id}");
        }
        return false;
    }

    public function getParent($id_childen = null)
    {
        global $CMS, $DB, $member;

        $sql = "SELECT * FROM `" . root_table . "accounts_type` WHERE  `accounts_type_deleted` = 0 AND `accounts_type_parent` = 0 AND `accounts_type_id` != '{$id_childen}' ORDER BY `accounts_type_id` DESC";

        $data = $DB->fetch_data($sql, $this->cache_prefix);

        return $data;
    }

    function loadGroupOption($default = 0)
    {
        global $CMS;

        $data = [0, 1];

        $output = "";

        $selected[$default] = "selected";

        foreach ($data as $v) {
            $output .= <<<EOF
            <option value="{$v}" {$selected[$v]}>{$CMS->lang['at_group_' . $v]}</option>
EOF;

        }

        return $output;
    }

    function getData($sql_add = "")
    {
        global $CMS, $DB;

        $sql = "SELECT * FROM " . root_table . "accounts_type WHERE {$sql_add} accounts_type_deleted=0 ORDER BY accounts_type_name";

        $data = $DB->fetch_data($sql, $this->cache_prefix);

        return $data;
    }

    /**
     * Export to excel file
     * @return string
     */
    public function exportToExcel()
    {
        global $CMS, $DB, $member;

        $setTitle = [
            'Group',
            'Parent',
            'Code',
            'Name',
            'Display'
        ];

        \models\report::excel_header();

        // Set style
        $style = array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation' => 0,
            'wrap' => TRUE
        );

        /**
         * Fields to export
         * Note: muste by match with header cols (order and numbers)
         */
        $fields = 'accounts_group,accounts_type_parent,accounts_type_code,accounts_type_name,accounts_type_status';

        // Query data
        $sql = "SELECT {$fields} FROM " . root_table . "accounts_type WHERE accounts_type_deleted=0 ORDER BY accounts_type_id ASC";

        $cacheData = $DB->fetch_data($sql, $this->cache_prefix);

        $count_row = count($cacheData) + 1;

        // Set style excel
        \models\report::excel_title($setTitle, $count_row, $style);

        // Loop data
        $i = 2;

        //Range char A-Z
        $rangeChar = range('A', 'Z');
        $fields = explode(',', $fields);

        if($cacheData)
        {
            foreach ($cacheData as $result) {
                $result['accounts_type_status'] = $result['accounts_type_status'] ? 'Show' : 'Hide';

                $result['accounts_group'] = $result['accounts_group'] == 1 ? 'default' : 'custom';

                foreach ($rangeChar as $charKey => $char) {
                    /**
                     * Set values to cell by chars(A-Z) and fields from database
                     */
                    $field = $fields[$charKey];

                    $result[$field] = html_entity_decode($result[$field]);

                    \models\report::$dataExcel->getActiveSheet()->setCellValue($char . $i, $result[$field]);

                };

                $i++;
            }
        }


        // Set name file
        $file_name = "accounts_types_u{$member['user_id']}.xls";

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
        require_once root_path . "vendor/autoload.php";

        //Range char A-Z
        $rangeChar = range('A', 'Z');

        // Input
        $file_tmp = isset($_FILES['upload_file']['tmp_name']) ? $_FILES['upload_file']['tmp_name'] : "";
        $file_name = isset($_FILES['upload_file']['name']) ? $_FILES['upload_file']['name'] : "";

        // Check file allow
        $file_ext = $CMS->class->attachment->get_ext($file_name);
        $arr_allow = array("xls", "xlsx");

        if (!in_array($file_ext, $arr_allow)) {
            $_SESSION['error_msg'] = $CMS->lang['error_ext_file_upload'];
            return false;
        }

        // Khoi tao
        $objPHPExcel = \PHPExcel_IOFactory::load($file_tmp);

        $highestColumn = $objPHPExcel->getActiveSheet()->getHighestColumn(); //Cột cuối cùng
        $highestRow = $objPHPExcel->getActiveSheet()->getHighestRow(); // e.g. 10

        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);

        //Reset style
        $styleDefault = array(
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_NONE,
            ),
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => '000000'))
        );

        $objPHPExcel->getActiveSheet()->getStyle("A2:{$highestColumn}{$highestRow}")->applyFromArray($styleDefault);

        //Danh sách thứ tự các field tương ứng với thứ tự cột từ file excel
        $fields_text = 'accounts_group,accounts_type_parent,accounts_type_code,accounts_type_name,accounts_type_status';
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
            'font' => array(
//                'bold'  => true,
                'color' => array('rgb' => 'FF0000'),
            )); //Style đánh dấu dòng có lỗi

        $sql_values = ""; //values for multi insert

        $validData = []; //Mang chua cac dong hop le

        /**
         * Fields is required
         */
        $checkRequired = ['accounts_type_name', 'accounts_type_code'];

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
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            // Loop row
            for ($row = 1; $row <= $highestRow; ++$row) {
                if ($row == 1) {
                    continue;
                }
                $data = [];

                // Loop col
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $cell = $worksheet->getCellByColumnAndRow($col, $row);
                    if ($fields[$col]) {
                        $data[$fields[$col]] = $CMS->class->editor->input($cell->getValue(), "text"); //Gan du lieu lai theo giong field trong DB

                        /**
                         * Check required
                         */
                        if (in_array($fields[$col], $checkRequired)) {
                            if ($data[$fields[$col]] == '') {
                                $errorPositions[$row][] = $col;
                            }
                        }

                        if ($fields[$col] == 'accounts_type_code') {
                            if ($data[$fields[$col]] != '') {
                                $tplKeyList[$data[$fields[$col]]][] = ['col' => $col, 'row' => $row];
                            }
                        }
                    }
                }

                if (!isset($errorPositions[$row])) {
                    $validData[$row] = $data;
                }
            }
        }

        //Duyet danh sach key de check trung
        foreach ($tplKeyList as $tplCode => $errorPos) {

            if (!$CMS->input['is_overwrite']) {
                if (count($errorPos) > 1 || $this->checkExist('accounts_type_code', $tplCode)) {
                    foreach ($errorPos as $errColRows) {
                        $errorPositions[$errColRows['row']][] = $errColRows['col'];
                    }
                }
            }

            //Xóa bỏ các dòng trung key, chỉ giữ lại dòng cuối
            if (($cnt = count($errorPos)) >= 2) {
                for ($i = 0; $i < $cnt - 1; $i++) {
                    unset($validData[$errorPos[$i]['row']]);
                }
            }
        }

        if ($errorPositions) {
            foreach ($errorPositions as $errRow => $errCols) {
                $objPHPExcel->getActiveSheet()->getColumnDimension();

                //Đánh dấu dòng lỗi
                $objPHPExcel->getActiveSheet()->getStyle("A{$errRow}:{$highestColumn}{$errRow}")->applyFromArray($errStyleRow);

                foreach ($errCols as $errCol) {
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
        foreach ($validData as $row => $data) {
            $data['accounts_type_status'] = strtolower($data['accounts_type_status']) == 'show' ? 1 : 0;
            $data['accounts_group'] = strtolower($data['accounts_group']) == 'custom' ? 1 : 0;
            $data['accounts_type_time'] = time();
            $data['user_id'] = $member['user_id'];

            if ($this->checkExist('accounts_type_code', $data['accounts_type_code'])) {
                /**
                 * Update record
                 */
                $sql_update = "UPDATE " . root_table . "accounts_type SET ";

                foreach ($data as $field => $value) {
                    //Create values sql
                    $sql_update .= "{$field}='{$value}',";
                }

                $sql_update = trim($sql_update, ',');

                $sql_update .= " WHERE accounts_type_code='{$data['accounts_type_code']}'";

                $DB->query($sql_update);
            } else {
                /**
                 * Multi insert
                 */
                $sql_values .= "(";
                foreach ($data as $field => $value) {
                    //Create values sql
                    $sql_values .= "'{$value}',";
                }
                $sql_values = trim($sql_values, ',');
                $sql_values .= "),";
            }
        }

        $sql_values = trim($sql_values, ',');

        /**
         * Lay danh sach field de insert
         */

        $field_list = array_keys($data);
        $field_list = implode(',', $field_list);

        $sql = "INSERT INTO " . root_table . "accounts_type ({$field_list}) VALUES {$sql_values}";

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

        $DB->query($sql);

        $_SESSION['msg'] = $CMS->lang['import_file_success'];

        $CMS->global->redirectReferer();

    }

    /**
     * Convert data for edit form
     * @param array $data
     * @return array
     */
    public function editValue($data = [])
    {
        global $CMS;

        $data_bk = $data['data_bk'] = $data;

        $name = @json_decode($data_bk['accounts_type_name'], true);
        $data['accounts_type_name'] = $name ? $name : $data_bk['accounts_type_name'];

        if ($CMS->vars['translations']) {
            //Đa ngôn ngữ
            if (!is_array($data['accounts_type_name'])) {
                //Neu k phai dang mang thi chuyen ve mang
                $name = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName) {
                    $name[$langCode] = $data['accounts_type_name'];
                }

                $data['accounts_type_name'] = $name;
            }
        } else {
            if (is_array($data['accounts_type_name'])) {
                //Neu la dang mang thi chuyen ve dang chuoi binh thuong
                $data['accounts_type_name'] = $data['accounts_type_name'][$CMS->vars['default_language']];
            }
        }

        return $data;
    }

    public function checkExist($field, $value = "", $except_value = "")
    {
        global $CMS, $DB, $member;

        if (!$field) {
            return true;
        }

        if ($except_value) {
            $DB->query("SELECT * FROM " . root_table . "accounts_type WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND accounts_type_deleted=0");
        } else {
            $DB->query("SELECT * FROM " . root_table . "accounts_type WHERE {$field}='{$value}' AND accounts_type_deleted=0");
        }

        if ($DB->num_rows() == 0) {
            return false;
        } else {
            return true;
        }
    }
}

?>