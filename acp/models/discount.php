<?php
/**
 * Created by PhpStorm.
 * User: Phuong3F
 * Date: 5/23/2017
 * Time: 9:44 AM
 */

namespace models;

use core\ezy;
use lib\date;
use lib\input;
use lib\db;
use \lib\template;
use \models\discount_items;

ezy::load_model('discount_items');

class discount
{
    /**
     * @param $record_cnt
     *		The order number of Data
     */
    static public $record_cnt = 0;

    /**
    * @param @$arrangeData
    *		The SQL Query for listing Data
    */
    static public $arrangeData;

    /**
     * @param $sqlAdd
     *		The additional SQL for $sql_query
     */
    static public $sqlAdd;

    /**
     * @param $sqlQuery
     *		The SQL Query for listing Data
     */
    static public $sqlQuery;

    /**
     * @param $maxPage
     *		Number of records on per page
     */
    static public $maxPage = 20;

    /**
     * @param $prefixPaging
     *		Prefix for paging url
     */
    static public $prefixPaging = '';

    /**
     * @param $prefixPaging
     *		Suffix for paging url
     */
    static public $suffixPaging = '';

    /**
     * Add new postion
     * @param array $data array input
     * @output boolean|array
     */
    static public function add($data = [])
    {
        global $CMS, $member, $DB;

        $time = time();
        $random = rand(0,100);
        $discount_token_key = $CMS->class->random->md5($CMS->class->random->character(16));

        $discount_name= $data['discount_name'];
        $discount_description = $data['discount_description'];
        $discount_content = $data['discount_content'];
        $discount_type = intval($data['discount_type']);
        $discount_num_chars = intval($data['discount_num_chars']);
        $discount_status = intval($data['discount_status']);
        $discount_value = floatval($data['discount_value']);
        $discount_start_time = $data['discount_start_time'] ? $CMS->class->date->date2time($data['discount_start_time']) : 0;
        $discount_end_time = $data['discount_end_time'] ? $CMS->class->date->date2time($data['discount_end_time']) : 0;
        $discount_time = time();
        $user_id = $member['user_id'];

        $sql = "INSERT INTO ".root_table."discount(discount_name, discount_description, discount_content,discount_type, discount_num_chars, discount_status, discount_start_time, discount_end_time, discount_value, discount_time, discount_token_key, user_id) VALUES('{$discount_name}', '{$discount_description}', '{$discount_content}', '{$discount_type}', '{$discount_num_chars}', '{$discount_status}', '{$discount_start_time}', '{$discount_end_time}', '{$discount_value}', '{$discount_time}', '{$discount_token_key}', '{$user_id}')";

        $DB->query($sql);

        //Clear cache
        $CMS->class->cache->mdelete('discount');

        $insertedRecord = self::insertedRecord($discount_token_key); //Get inserted record

        $CMS->class->logs->key = "discount_{$insertedRecord['discount_id']}";
        $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['added_discount_success']}: <strong>{$insertedRecord['discount_name']}</strong>");

        return $insertedRecord;
    }

    /**
     * Edit discount
     * @param array $data
     * @return bool|array
     */
    static public function edit($data = [])
    {
        global $CMS, $member, $DB;

        $time = time();
        $random = rand(0,100);

        $oldData = self::getInfo($data['id']);

        if(!$oldData)
        {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            return false;
        }

        $discount_name= $data['discount_name'];
        $discount_description = $data['discount_description'];
        $discount_content = $data['discount_content'];
        $discount_type = intval($data['discount_type']);
        $discount_num_chars = intval($data['discount_num_chars']);
        $discount_status = intval($data['discount_status']);
        $discount_value = floatval($data['discount_value']);
        $discount_start_time = $data['discount_start_time'] ? $CMS->class->date->date2time($data['discount_start_time']) : 0;
        $discount_end_time = $data['discount_end_time'] ? $CMS->class->date->date2time($data['discount_end_time']) : 0;

        $sql = "UPDATE ".root_table."discount SET discount_name='{$discount_name}', discount_description='{$discount_description}', discount_content='{$discount_content}', discount_type='{$discount_type}', discount_num_chars='{$discount_num_chars}', discount_status='{$discount_status}', discount_value='{$discount_value}', discount_start_time='{$discount_start_time}', discount_end_time='{$discount_end_time}' WHERE discount_id='{$CMS->input['id']}'";

        $DB->query($sql);

        //Clear cache
        $CMS->class->cache->mdelete('discount');

        $updatedRecord = self::getInfo($CMS->input['id']);

        $CMS->class->logs->key = "discount_{$updatedRecord['discount_id']}";
        $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['updated_discount_success']}: <strong>{$updatedRecord['discount_name']}</strong>");

        return $updatedRecord;
    }

    /**
     * Delete logo
     * @return bool
     */
    static public function delete()
    {
        global $CMS, $DB;

        // Get info
        $data = self::getInfo();

        // Check existing
        if ( ! $data ) { return false; }

        // Update info
        $DB->query("UPDATE ".root_table."discount SET discount_deleted=1 WHERE discount_id={$data['discount_id']}");

        //Clear cache
        $CMS->class->cache->mdelete('discount');

        @unlink("{$CMS->vars['upload_dir']}/{$data['discount_src']}");

        // Create log
        $CMS->class->logs->key = "discount_{$data['discount_id']}";
        $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['discount_deleted']} <b>{$data['discount_title']}</b>")."<br />";

        return true;
    }

    /**
     * Delete multi discount
     * @return bool
     */
    static public function mdelete()
    {
        global $CMS, $DB;

        $deleted = 0;

        $_SESSION["msg"] .= "";

        for ( $i = 0; $i < intval( $CMS->input["data_cnt"] ); $i++ )
        {
            $id = intval( $CMS->input["id_{$i}"] );

            if ( $id )
            {
                $data = self::getInfo($id);

                $DB->query("UPDATE ".root_table."discount SET discount_deleted=1 WHERE discount_id={$id}");

                @unlink("{$CMS->vars['upload_dir']}/{$data['discount_src']}");

                $CMS->class->logs->key = "discount_{$data['discount_id']}";
                $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['discount_deleted']} <b>{$data['discount_title']}</b>")."<br />";

                $deleted = 1;
            }
        }

        if ( $deleted == 0 )
        {
            //Clear cache
            $CMS->class->cache->mdelete('discount');

            $_SESSION["msg"] .= "{$CMS->lang['discount_delete_failed']}";
        }

        return true;
    }

    /**
     * Get infomation of a logo
     * @param int $id
     * @return array
     */
    static public function getInfo($id = 0)
    {
        global $CMS, $DB;

        if(!$id)
        {
            $id = $CMS->input['id'];
        }

        if(is_numeric($id))
        {
            $id = intval($id);
            $sql_add = " discount_id='{$id}' AND ";
        }
        else
        {
            $sql_add = " discount_token_key='{$id}' AND ";
        }

        $sql = "SELECT * FROM ".root_table."discount WHERE {$sql_add} discount_deleted=0 ORDER BY discount_id LIMIT 0,1";

        return $DB->fetch_data($sql,'discount')[0];
    }

    /**
     * Get infomation of record which just added
     * @param string $token_key
     * @return bool|array
     */
    static public function insertedRecord($token_key = "")
    {
        global $CMS, $DB;

        if(!$token_key) return false;

        $sql = "SELECT * FROM ".root_table."discount WHERE discount_token_key='{$token_key}' ORDER BY discount_id DESC LIMIT 1";

        $sql = $DB->query($sql);

        return $DB->fetch_assoc($sql);
    }

    /**
     * Convert input data to add data form
     * @param array $data
     * @return array
     */
    static  public function addValue($data=[])
    {
        global $CMS;

        $data['data_bk'] = $data_bk = $data;

        $data['discount_num_chars'] = isset($data_bk['discount_num_chars']) ? intval($data_bk['discount_num_chars']) : 5;
        $data['generate_code'] = intval($data_bk['generate_code']);
        $data['discount_value'] = isset($data_bk['discount_value']) ? floatval($data_bk['discount_value']) : 1;

        return $data;
    }

    /**
     * Convert original record to display on edit form
     * @param array $data
     * @return array
     */
    static  public function editValue($data=[])
    {
        global $CMS;

        $data['data_bk'] = $data_bk = $data;
        $data['discount_start_time'] = $data_bk['discount_start_time'] ?  date::format($data_bk['discount_start_time']) : '';
        $data['discount_end_time'] = $data_bk['discount_end_time'] ?  date::format($data_bk['discount_end_time']) : '';

        $data['discount_num_chars'] = isset($data_bk['discount_num_chars']) ? intval($data_bk['discount_num_chars']) : 5;
        $data['generate_code'] = intval($data_bk['generate_code']);
        $data['discount_value'] = isset($data_bk['discount_value']) ? floatval($data_bk['discount_value']) : 1;

        return $data;
    }

    /**
     * Convert original record to show
     * @param array $data
     * @return array
     */
    static  public function convertValue($data=[])
    {
        global $CMS;

        $data['data_bk'] = $data_bk = $data;

        // Replace search content
        $data = $CMS->class->search->convertvalue($data);

        $data['discount_time'] = $data_bk['discount_time'] ? date::format($data['discount_time']) : '';
        $data['discount_start_time'] = $data_bk['discount_start_time'] ? date::format($data['discount_start_time']) : '';
        $data['discount_end_time'] = $data_bk['discount_end_time'] ? date::format($data['discount_end_time']) : '';

        if($data_bk['discount_start_time'] && $data_bk['discount_end_time'])
        {
            $data['discount_period_time'] = "{$data['discount_start_time']} - {$data['discount_end_time']}";
        }
        else if($data_bk['discount_start_time'])
        {
            $data['discount_period_time'] = "{$CMS->lang['from']} {$data['discount_start_time']}";
        }
        else if($data_bk['discount_end_time'])
        {
            $data['discount_period_time'] = "{$CMS->lang['to']} {$data['discount_end_time']}";
        }
        else
        {
            $data['discount_period_time'] = 'N/A';
        }

        if($CMS->permit['discount_read'] || $CMS->permit['discount_is_root'])
        {
            $data['discount_name'] = "<a href='{$CMS->vars['root_domain']}/?site=discount&act=show&id={$data_bk['discount_id']}'>{$data_bk['discount_name']}</a>";
        }

        $data['record_cnt'] = self::$record_cnt;

        if($CMS->vars['userTemp'][$data_bk['user_id']])
        {
            $data['userInfo'] = $CMS->vars['userTemp'][$data_bk['user_id']];
        }
        else
        {
            $CMS->vars['userTemp'][$data_bk['user_id']] = $data['userInfo'] = $CMS->user->get_info($data_bk['user_id']);
        }

        if($CMS->permit['user_read'] || $CMS->permit['user_is_root'])
        {
            $data['user_id'] = "<a href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['userInfo']['user_id']}'>{$data['userInfo']['user_display_name']}</a>";
        }
        else
        {
            $data['user_id'] = $data['userInfo']['user_display_name'];
        }

        $data['discount_type'] = $CMS->lang['discount_type_'.$data_bk['discount_type']];
        $data['discount_value'] = "{$data_bk['discount_value']}{$data['discount_type']}";
        $data['discount_status'] = $CMS->lang['discount_status_'.$data_bk['discount_status']];

        self::$record_cnt++;

        return $data;
    }

    static  public function convertValueExport($data=[])
    {
        global $CMS;

        $data['data_bk'] = $data_bk = $data;

        // Replace search content
        $data = $CMS->class->search->convertvalue($data);

        $data['discount_time'] = $data_bk['discount_time'] ? date::format($data['discount_time']) : '';
        $data['discount_start_time'] = $data_bk['discount_start_time'] ? date::format($data['discount_start_time']) : '';
        $data['discount_end_time'] = $data_bk['discount_end_time'] ? date::format($data['discount_end_time']) : '';

        $data['discount_type'] = $CMS->lang['discount_type_'.$data_bk['discount_type']];
        $data['discount_status'] = $CMS->lang['discount_status_'.$data_bk['discount_status']];

        self::$record_cnt++;

        return $data;
    }

    /**
     * Get list discount
     * @param string $sql_add
     * @return array
     */
    static public function listing($sql_add = "", $is_export = 0)
    {
        global $CMS, $DB;

        // Update Arrange Data
        self::$arrangeData = trim("discount_id,discount_time");

        // Set default for Arrange
        $default_field = $CMS->input['order'] ? $CMS->input['order'] : "discount_id";
        $default_order = $CMS->input['by'] ? $CMS->input['by'] : "desc";

        // SQL Condition
        $sql_add .= " discount_deleted=0 AND ";

        $CMS->input['keyword'] = $CMS->input['keyword'] ? $CMS->input['keyword'] :  $CMS->input['term'];

        if(isset($CMS->input['keyword']) && $CMS->input['keyword']!=='')
        {
            $keyword = urldecode($CMS->input['keyword']);

            $discount_url = self::clearUrl($keyword);

            $sql_add .= " (discount_name LIKE '%{$keyword}%' OR discount_description LIKE '%{$keyword}%') AND ";
        }

        if(isset($CMS->input['discount_status']) && $CMS->input['discount_status'] !== '')
        {
            $discount_status = intval($CMS->input['discount_status']);
            $sql_add .= " discount_status = {$discount_status} AND ";
        }

        if(isset($CMS->input['discount_type']) && $CMS->input['discount_type'] !== '')
        {
            $discount_type = intval($CMS->input['discount_type']);
            $sql_add .= " discount_type = {$discount_type} AND ";
        }

        if(isset($CMS->input['discount_name']) && $CMS->input['discount_name']!=='')
        {
            $discount_name = urldecode($CMS->input['discount_name']);
            $sql_add .= " discount_name LIKE '%{$discount_name}%' AND ";
        }

        if(isset($CMS->input['discount_description']) && $CMS->input['discount_description']!=='')
        {
            $discount_description = urldecode($CMS->input['discount_description']);
            $sql_add .= " discount_description LIKE '%{$discount_description}%' AND ";
        }

        if(isset($CMS->input['discount_start_time']) && $CMS->input['discount_start_time']!=='')
        {
            $discount_start_time = urldecode($CMS->input['discount_start_time']);
            $discount_start_time = $CMS->class->date->date2time($discount_start_time);
            $sql_add .= " discount_start_time >= $discount_start_time AND ";
        }

        if(isset($CMS->input['discount_start_time_to']) && $CMS->input['discount_start_time_to']!=='')
        {
            $discount_start_time_to = urldecode($CMS->input['discount_start_time_to']);
            $discount_start_time_to = $CMS->class->date->date2time($discount_start_time_to)+(3600*24);
            $sql_add .= " discount_start_time < $discount_start_time_to AND ";
        }

        if(isset($CMS->input['discount_end_time']) && $CMS->input['discount_end_time']!=='')
        {
            $discount_end_time = urldecode($CMS->input['discount_end_time']);
            $discount_end_time = $CMS->class->date->date2time($discount_end_time);
            $sql_add .= " discount_end_time >= $discount_end_time AND ";
        }

        if(isset($CMS->input['discount_end_time_to']) && $CMS->input['discount_end_time_to']!=='')
        {
            $discount_end_time_to = urldecode($CMS->input['discount_end_time_to']);
            $discount_end_time_to = $CMS->class->date->date2time($discount_end_time_to)+(3600*24);
            $sql_add .= " discount_end_time < $discount_end_time_to AND ";
        }


        $sql = "SELECT * FROM ".root_table."discount WHERE {$sql_add} 1=1 ORDER BY {$default_field} {$default_order}";

        // Create SQL Query for listing Data
        if ($is_export == 1) {
            $CMS->show_page = "";
            return $DB->fetch_data($sql,'discount');
        } else {
            list($CMS->show_page, $results) = $DB->fetch_listing($sql, self::$maxPage, self::$prefixPaging, self::$suffixPaging, $CMS->input['page'], 'discount');
            return $results;
        }
    }

    /**
     * Check existed record
     * @param string $field
     * @param mixed $val
     * @param mixed $except
     * @return integer
     */
    function checkExisted($field, $val, $except=null)
    {
        global $CMS, $DB;

        if($except !== null)
        {
            $sql_add = " {$field} != '{$except}' AND ";
        }

        $sql = "SELECT COUNT(0) cnt FROM ".root_table."discount WHERE {$sql_add} {$field} = '{$val}' AND  discount_deleted=0 LIMIT 1";

        return $DB->fetch_data($sql,'discount')[0]['cnt'];
    }


    /**
     * clear domain, paging param
     * @param string $url
     * @return string
     */
    static public function clearUrl($url)
    {
        global $CMS;

        //remove domain
        $url = str_replace($CMS->vars['parent_domain'], '', $url);

        //remove paging param
        $url = preg_replace('/(\/page-[0-9]+)/','',$url);

        return $url;
    }

    static function validate($data)
    {
        global $CMS;

        $return['valid'] = true;

        if(!$data['discount_name'])
        {
            $return['msg'][] = $CMS->lang['incomplete_name'];
        }

        if($data['discount_value']=='')
        {
            $return['msg'][] = $CMS->lang['incomplete_value'];
        }
        else if ($data['discount_value'] <= 0 || ($data['discount_type'] == 0 && $data['discount_value']>100))
        {
            $return['msg'][] = $CMS->lang['invalid_value'];
        }

        if($data['discount_num_chars'] <5 || $data['discount_num_chars'] > 32)
        {
            $return['msg'][] = $CMS->lang['invalid_num_chars'];
        }

        if($return['msg'])
        {
            $return['valid'] = false;
            $return['msg'] = implode('<br>',$return['msg']);
        }

        return $return;
    }

   static function updateNumOfItems($discount_id)
    {
        global $DB, $CMS;
        $discount_id = intval($discount_id);
        if(!$discount_id) return false;

        $sql = "UPDATE ".root_table."discount SET discount_num_items=(SELECT COUNT(di_id) FROM ".root_table."discount_items WHERE di_deleted=0 AND discount_id='{$discount_id}') WHERE discount_id='{$discount_id}'";

        $CMS->class->cache->mdelete('discount');

        return $DB->query($sql);
    }

    public static function exportToExcel()
    {
        global $CMS, $DB, $member;

        $sql_add = "";

        $setTitle = [
            'No.',
            $CMS->lang['discount_name'],
            $CMS->lang['discount_value'],
            $CMS->lang['discount_type'],
            $CMS->lang['discount_start_time'],
            $CMS->lang['discount_end_time'],
            $CMS->lang['discount_status'],
            $CMS->lang['discount_time'],
        ];

        \models\report::excel_header();
        // Set style
        $style = array(
            'rotation' => 0,
            'wrap' => TRUE
        );

        /**
         * Fields to export
         * Note: muste by match with header cols (order and numbers)
         */
        $fields = 'no,discount_name,discount_value,discount_type,discount_start_time,discount_end_time,discount_status,discount_time';

        $results = self::listing("", 1);

        $count_row = count($results) + 1;// + 1 row title

        // Set style excel
        \models\report::excel_title($setTitle, $count_row, $style);

        // Loop data
        $i = 2;

        //Range char A-Z
        $rangeChar = range('A', 'Z');
        $fields = explode(',', $fields);

        foreach ($results as $no => $result) {
            $result = self::convertValueExport($result);
            $result['no'] = $no + 1;
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

        // Set name file
        $file_name = "{$CMS->input['site']}_u{$member['user_id']}.xls";

        // Create file and return link download
        return \models\report::excel_output($file_name);
    }
}