<?php
/**
 * Created by PhpStorm.
 * User: Phuong3F
 * Date: 5/23/2017
 * Time: 9:44 AM
 */
namespace models;

use \core\ezy;
use \lib\date;
use lib\input;

class shift_work
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
     * Add new shift_work
     * @param array $data array input
     * @output boolean|array
     */
    static public function add($data = [])
    {
        global $CMS, $member, $DB;

        $data['shift_work_token_key'] = $CMS->class->random->md5($CMS->class->random->character(16));
        $data['shift_work_created_at'] = $data['shift_work_updated_at'] = time();
        $data['user_id'] = intval($member['user_id']);
        $data['shift_work_in'] = date::hour2Sec($data['shift_work_in']);
        $data['shift_work_out'] = date::hour2Sec($data['shift_work_out']);
        $data['shift_work_order'] = $data['shift_work_order']*1;

        if($DB->insert('shift_work', $data))
        {
            //Clear cache
            $CMS->class->cache->mdelete('shift_work');

            $insertedRecord = self::insertedRecord($data['shift_work_token_key']); //Get inserted record

            $CMS->class->logs->key = "shift_work_{$insertedRecord['shift_work_id']}";
            $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['added_shift_work_success']}: <strong>{$insertedRecord['shift_work_name']}</strong>");
            return $insertedRecord;
        }
        else
        {
            $_SESSION['msg'] = "{$CMS->lang['added_shift_work_failed']}";
            return false;
        }
    }

    /**
     * Edit shift_work
     * @param array $data
     * @return bool|array
     */
    static public function edit($data = [])
    {
        global $CMS, $member, $DB;

        $oldData = self::getInfo($data['id']);
        if(!$oldData)
        {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            return false;
        }

        $data['shift_work_in'] = isset($data['shift_work_in']) ? date::hour2Sec($data['shift_work_in']) : $oldData['shift_work_in'];
        $data['shift_work_out'] = isset($data['shift_work_out']) ? date::hour2Sec($data['shift_work_out']) : $oldData['shift_work_out'];
        $data['shift_work_order'] = isset($data['shift_work_order']) ? $data['shift_work_order']*1 : $oldData['shift_work_order'];
        $data['shift_work_updated_at'] = time();

        if($DB->update("shift_work", $data, "shift_work_id"))
        {
            //Clear cache
            $CMS->class->cache->mdelete('shift_work');

            $updatedRecord = self::getInfo($CMS->input['id']);

            $CMS->class->logs->key = "shift_work_{$updatedRecord['shift_work_id']}";
            $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['updated_shift_work_success']}: <strong>{$updatedRecord['shift_work_name']}</strong>");

            return $updatedRecord;
        }
        else
        {
            $_SESSION['msg'] = "{$CMS->lang['updated_shift_work_failed']}";
            return false;
        }
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
        $DB->query("UPDATE ".root_table."shift_work SET shift_work_deleted=1 WHERE shift_work_id={$data['shift_work_id']}");

        //Clear cache
        $CMS->class->cache->mdelete('shift_work');

        @unlink("{$CMS->vars['upload_dir']}/{$data['shift_work_src']}");

        // Create log
        $CMS->class->logs->key = "shift_work_{$data['shift_work_id']}";
        $_SESSION["msg"] = $CMS->class->logs->insert("{$CMS->lang['shift_work_deleted']} <b>{$data['shift_work_name']}</b>")."<br />";

        return true;
    }

    /**
     * Delete multi shift_work
     * @return bool
     */
    static public function mdelete()
    {
        global $CMS, $DB;

        $deleted = 0;

        $_SESSION["msg"] = "";

        for ( $i = 0; $i < intval( $CMS->input["data_cnt"] ); $i++ )
        {
            $id = intval( input::get("id_{$i}") );

            if ( $id )
            {
                $data = self::getInfo($id);

                $DB->query("UPDATE ".root_table."shift_work SET shift_work_deleted=1 WHERE shift_work_id={$id}");

                @unlink("{$CMS->vars['upload_dir']}/{$data['shift_work_src']}");

                $CMS->class->logs->key = "shift_work_{$data['shift_work_id']}";
                $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['shift_work_deleted']} <b>{$data['shift_work_name']}</b>")."<br />";

                $deleted = 1;
            }
        }

        if ( $deleted == 0 )
        {
            //Clear cache
            $_SESSION["msg"] .= "{$CMS->lang['shift_work_delete_failed']}";
        }

        $CMS->class->cache->mdelete('shift_work');

        return true;
    }

    /**
     * Get infomation of a logo
     * @param int $id
     * @return array
     */
    static public function getInfo($id = 0, $sql_add='')
    {
        global $CMS, $DB;

        if(!$id)
        {
            $id = $CMS->input['id'];
        }

        if(!$id) return false;

        if(is_numeric($id))
        {
            $id = intval($id);
            $sql_add .= " shift_work_id='{$id}' AND ";
        }
        else
        {
            $sql_add .= " (shift_work_token_key='{$id}' AND ";
        }

        $sql = "SELECT * FROM ".root_table."shift_work WHERE {$sql_add} shift_work_deleted=0 ORDER BY shift_work_id LIMIT 0,1";

        return $DB->fetch_data($sql, 'shift_work')[0];
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

        $sql = "SELECT * FROM ".root_table."shift_work WHERE shift_work_token_key='{$token_key}' ORDER BY shift_work_id DESC LIMIT 1";

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
        global $CMS, $member;

        $data['data_bk'] = $data_bk = $data;
        $data['shift_work_order'] = input::arrayValue($data, 'shift_work_order') * 1;

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

        $data['shift_work_in'] = date::sec2Hour($data_bk['shift_work_in']);
        $data['shift_work_out'] = date::sec2Hour($data_bk['shift_work_out']);
        $data['shift_work_order'] *= 1;

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

        $data['shift_work_created_at'] = $data_bk['shift_work_created_at'] ? date::format($data['shift_work_created_at']) : '';
        $data['shift_work_updated_at'] = $data_bk['shift_work_updated_at'] ? date::format($data['shift_work_updated_at']) : '';

        if($CMS->permit['shift_work_read'] || $CMS->permit['shift_work_is_root'])
        {
            $data['shift_work_name'] = "<a style='display: inline-block' href='{$CMS->vars['root_domain']}/?site=shift_work&act=show&id={$data_bk['shift_work_id']}'>{$data_bk['shift_work_name']}</a>";
        }

        $data['record_cnt'] = self::$record_cnt;

        if($data_bk['user_id'])
        {
            if(!isset($CMS->vars['userTemp'][$data_bk['user_id']]))
            {
                $CMS->vars['userTemp'][$data_bk['user_id']] = $CMS->user->get_info($data_bk['user_id']);
            }
            $data['userInfo'] = $CMS->vars['userTemp'][$data_bk['user_id']];
        }

        if($CMS->permit['user_read'] || $CMS->permit['user_is_root'])
        {
            $data['user_id'] = "<a style='display: inline-block' href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['userInfo']['user_id']}'>{$data['userInfo']['user_display_name']}</a>";
        }
        else
        {
            $data['user_id'] = $data['userInfo']['user_display_name'];
        }

        $data['shift_work_in'] = date::sec2Hour($data_bk['shift_work_in']);
        $data['shift_work_out'] = date::sec2Hour($data_bk['shift_work_out']);

        self::$record_cnt++;

        return $data;
    }

    static  public function convertValueExport($data=[])
    {
        global $CMS;

        $data['data_bk'] = $data_bk = $data;

        // Replace search content
        $data = $CMS->class->search->convertvalue($data);

        $data['shift_work_created_at'] = $data_bk['shift_work_created_at'] ? date::format($data['shift_work_created_at']) : '';
        $data['shift_work_updated_at'] = $data_bk['shift_work_updated_at'] ? date::format($data['shift_work_updated_at']) : '';
        $data['record_cnt'] = self::$record_cnt;

        if($CMS->vars['userTemp'][$data_bk['user_id']])
        {
            $data['userInfo'] = $CMS->vars['userTemp'][$data_bk['user_id']];
        }
        else
        {
            $CMS->vars['userTemp'][$data_bk['user_id']] = $data['userInfo'] = $CMS->user->get_info($data_bk['user_id']);
        }

        $data['user_id'] = $data['userInfo']['user_display_name'];

        self::$record_cnt++;

        return $data;
    }

    /**
     * Get list shift_work
     * @param string $sql_add
     * @param integer $disabled_paging
     * @return array
     */
    static public function listing($sql_add = "", $disabled_paging = 0, $order_field = "shift_work_id", $order_desc = "desc")
    {
        global $CMS, $DB;

        // Update Arrange Data
        self::$arrangeData = trim("shift_work_id,shift_work_time,shift_work_update_time");

        // Set default for Arrange
        $default_field = input::get('order', $order_field);
        $default_order = input::get('by', $order_desc);

        // SQL Condition
        $sql_add .= " shift_work_deleted=0 AND ";

        $CMS->input['keyword'] = input::get('keyword', input::get('term'));

        $sql_add .= self::getSqlAdd($CMS->input);

        $sql = "SELECT * FROM ".root_table."shift_work WHERE {$sql_add} 1=1 ORDER BY {$default_field} {$default_order}";

        // Create SQL Query for listing Data
        if ($disabled_paging == 1) {
            $CMS->show_page = "";

            return $DB->fetch_data($sql,'shift_work');
        } else {
            list($CMS->show_page, $results) = $DB->fetch_listing($sql, self::$maxPage, self::$prefixPaging, self::$suffixPaging, $CMS->input['page'], 'shift_work');

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
    static public function checkExisted($field, $val, $except=null)
    {
        global $CMS, $DB;

        if($except !== null)
        {
            $sql_add = " {$field} != '{$except}' AND ";
        }

        $sql = "SELECT count(0) cnt FROM ".root_table."shift_work WHERE {$sql_add} {$field} = '{$val}' AND shift_work_deleted=0 LIMIT 1";

        return $DB->fetch_data($sql,'shift_work')[0]['cnt'];
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

    static function validate($data, $oldData=[])
    {
        global $CMS;

        $return['valid'] = true;
        $return['msg'] = [];

        if($data['shift_work_name'] == "")
        {
            $return['msg'][] = $CMS->lang['incomplete_name'];
        }

        if($data['shift_work_in'] == "")
        {
            $return['msg'][] = $CMS->lang['shift_work_in'];
        }

        if($data['shift_work_out']=='')
        {
            $return['msg'][] = $CMS->lang['shift_work_out'];
        }

        if(count($return['msg']))
        {
            $return['valid'] = false;
            $return['msg'] = implode('<br>',$return['msg']);
        }

        return $return;
    }

    public static function exportToExcel()
    {
        global $CMS, $DB, $member;

        $setTitle = [
            'No.',
            $CMS->lang['shift_work_name'],
            $CMS->lang['shift_work_code'],
            $CMS->lang['shift_work_type'],
            $CMS->lang['shift_work_start_time'],
            $CMS->lang['shift_work_end_time'],
            $CMS->lang['shift_work_status'],
            $CMS->lang['shift_work_time'],
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
        $fields = 'no,shift_work_name,shift_work_code,shift_work_type,shift_work_start_time,shift_work_end_time,shift_work_status,shift_work_time';

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

    static public function getAll($sql_add="", $order_field = "shift_work_id", $order_desc = "desc")
    {
        return self::listing($sql_add, 1, $order_field, $order_desc);
    }

    /**
     * Lấy điều kiện cho truy vấn sql
     * nkvp - 2017.09.28
     */
    static function getSqlAdd($data = [], $prefix='')
    {
        global $CMS;

        $sql_add = '';

        if(isset($data['keyword']) && $data['keyword']!=='')
        {
            $keyword = urldecode($data['keyword']);

            $sql_add .= " ({$prefix}shift_work_name LIKE '%{$keyword}%' OR {$prefix}shift_work_label LIKE '%{$keyword}%') AND ";
        }

        if(isset($data['shift_work_name']) && $data['shift_work_name']!=='')
        {
            $shift_work_name = urldecode($data['shift_work_name']);
            $sql_add .= " {$prefix}shift_work_name LIKE '%{$shift_work_name}%' AND ";
        }

        if(isset($data['shift_work_created_at']) && $data['shift_work_created_at']!=='')
        {
            $shift_work_created_at = $CMS->class->date->date2time($data['shift_work_created_at'],1);
            $sql_add .= " {$prefix}shift_work_created_at>=$shift_work_created_at AND ";
        }

        if(isset($data['shift_work_created_at_to']) && $data['shift_work_created_at_to']!=='')
        {
            $shift_work_created_at_to = $CMS->class->date->date2time($data['shift_work_created_at_to'],1)+(3600*24);
            $sql_add .= " {$prefix}shift_work_created_at<$shift_work_created_at_to AND ";
        }

        return $sql_add;
    }

    static function quickUpdateSortOrder($shift_work_id=0, $shift_work_order=0)
    {
        global $CMS, $DB;

        $shift_work_id *= 1;
        $shift_work_order *= 1;

        $data = ['shift_work_id' => $shift_work_id,'shift_work_order' => $shift_work_order];

        $CMS->class->logs->key = "shift_work_{$shift_work_id}";

        if($DB->update("shift_work", $data, 'shift_work_id')) {
            $result = [
                'status' => 'ok',
                'msg' => $CMS->class->logs->insert("Updated sort order success !")
            ];
        } else {
            $result = [
                'status' => 'fail',
                'msg' => $CMS->class->logs->insert("Updated sort order failed !")
            ];
        }

        return $result;
    }
}