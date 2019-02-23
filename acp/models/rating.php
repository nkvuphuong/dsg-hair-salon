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

class rating
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
     * Add new rating
     * @param array $data array input
     * @output boolean|array
     */
    static public function add($data = [])
    {
        global $CMS, $member, $DB;

        $data['rating_token_key'] = $CMS->class->random->md5($CMS->class->random->character(16));
        $data['rating_created_time'] = $data['rating_updated_time'] = time();
        $data['user_id'] = intval($member['user_id']);

        if($DB->insert('rating', $data))
        {
            //Clear cache
            $CMS->class->cache->mdelete('rating');

            $insertedRecord = self::insertedRecord($data['rating_token_key']); //Get inserted record

            $CMS->class->logs->key = "rating_{$insertedRecord['rating_id']}";
            $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['added_rating_success']}: <strong>{$insertedRecord['rating_name']}</strong>");
            return $insertedRecord;
        }
        else
        {
            $_SESSION['msg'] = "{$CMS->lang['added_rating_failed']}";
            return false;
        }
    }

    /**
     * Edit rating
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

        $data['rating_updated_time'] = time();

        if($DB->update("rating", $data, "rating_id"))
        {
            //Clear cache
            $CMS->class->cache->mdelete('rating');

            $updatedRecord = self::getInfo($CMS->input['id']);

            $CMS->class->logs->key = "rating_{$updatedRecord['rating_id']}";
            $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['updated_rating_success']}: <strong>{$updatedRecord['rating_name']}</strong>");

            return $updatedRecord;
        }
        else
        {
            $_SESSION['msg'] = "{$CMS->lang['updated_rating_failed']}";
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
        $DB->query("UPDATE ".root_table."rating SET rating_deleted=1 WHERE rating_id={$data['rating_id']}");

        //Clear cache
        $CMS->class->cache->mdelete('rating');

        @unlink("{$CMS->vars['upload_dir']}/{$data['rating_src']}");

        // Create log
        $CMS->class->logs->key = "rating_{$data['rating_id']}";
        $_SESSION["msg"] = $CMS->class->logs->insert("{$CMS->lang['rating_deleted']} <b>{$data['rating_name']}</b>")."<br />";

        return true;
    }

    /**
     * Delete multi rating
     * @return bool
     */
    static public function mdelete()
    {
        global $CMS, $DB;

        $deleted = 0;

        $_SESSION["msg"] = "";

        for ( $i = 0; $i < intval( $CMS->input["data_cnt"] ); $i++ )
        {
            $id = intval(input::get("id_{$i}",0));

            if ( $id )
            {
                $data = self::getInfo($id);

                $DB->query("UPDATE ".root_table."rating SET rating_deleted=1 WHERE rating_id={$id}");

                @unlink("{$CMS->vars['upload_dir']}/{$data['rating_src']}");

                $CMS->class->logs->key = "rating_{$data['rating_id']}";
                $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['rating_deleted']} <b>{$data['rating_name']}</b>")."<br />";

                $deleted = 1;
            }
        }

        if ( $deleted == 0 )
        {
            //Clear cache
            $_SESSION["msg"] .= "{$CMS->lang['rating_delete_failed']}";
        }

        $CMS->class->cache->mdelete('rating');

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
            $sql_add .= " rating_id='{$id}' AND ";
        }
        else
        {
            $sql_add .= " (rating_token_key='{$id}' AND ";
        }

        $sql = "SELECT * FROM ".root_table."rating WHERE {$sql_add} rating_deleted=0 ORDER BY rating_id LIMIT 0,1";

        return $DB->fetch_data($sql, 'rating')[0];
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

        $sql = "SELECT * FROM ".root_table."rating WHERE rating_token_key='{$token_key}' ORDER BY rating_id DESC LIMIT 1";

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

        $data['rating_order'] = input::arrayValue($data, 'rating_order', 0) * 1;
        $data['rating_value'] = input::arrayValue($data, 'rating_value', 0) * 1;
        $data['rating_status'] = input::arrayValue($data, 'rating_status', 1) * 1;

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

        $data['rating_order'] = $data_bk['rating_order']*1;
        $data['rating_value'] = $data_bk['rating_value']*1;

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

        $data['rating_created_time'] = $data_bk['rating_created_time'] ? date::format($data['rating_created_time']) : '';
        $data['rating_updated_time'] = $data_bk['rating_updated_time'] ? date::format($data['rating_updated_time']) : '';

        if($CMS->permit['rating_read'] || $CMS->permit['rating_is_root'])
        {
            $data['rating_name'] = "<a style='display: inline-block' href='{$CMS->vars['root_domain']}/?site=rating&act=show&id={$data_bk['rating_id']}'>{$data_bk['rating_name']}</a>";
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

        $data['rating_label'] = htmlspecialchars_decode($data_bk['rating_label']);
        $data['rating_label'] = html_entity_decode($data['rating_label'], ENT_QUOTES | ENT_XML1, 'UTF-8');

        $data['rating_status'] = $CMS->lang['rating_status_'.$data_bk['rating_status']];
        $data['rating_status_color']=  $CMS->lang['rating_status_color_'.$data_bk['rating_status']];

        self::$record_cnt++;

        return $data;
    }

    static  public function convertValueExport($data=[])
    {
        global $CMS;

        $data['data_bk'] = $data_bk = $data;

        // Replace search content
        $data = $CMS->class->search->convertvalue($data);

        $data['rating_created_time'] = $data_bk['rating_created_time'] ? date::format($data['rating_created_time']) : '';
        $data['rating_updated_time'] = $data_bk['rating_updated_time'] ? date::format($data['rating_updated_time']) : '';
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
        $data['rating_status'] = $CMS->lang['rating_status_'.$data_bk['rating_status']];

        self::$record_cnt++;

        return $data;
    }

    /**
     * Get list rating
     * @param string $sql_add
     * @param integer $disabled_paging
     * @return array
     */
    static public function listing($sql_add = "", $disabled_paging = 0, $order_field = "rating_id", $order_desc = "desc")
    {
        global $CMS, $DB;

        // Update Arrange Data
        self::$arrangeData = trim("rating_id,rating_time,rating_update_time");

        // Set default for Arrange
        $default_field = input::get('order', $order_field);
        $default_order = input::get('by', $order_desc);

        // SQL Condition
        $sql_add .= " rating_deleted=0 AND ";

        $CMS->input['keyword'] = input::get('keyword', input::get('term'));

        $sql_add .= self::getSqlAdd($CMS->input);

        $sql = "SELECT * FROM ".root_table."rating WHERE {$sql_add} 1=1 ORDER BY {$default_field} {$default_order}";

        // Create SQL Query for listing Data
        if ($disabled_paging == 1) {
            $CMS->show_page = "";

            return $DB->fetch_data($sql,'rating');
        } else {
            list($CMS->show_page, $results) = $DB->fetch_listing($sql, self::$maxPage, self::$prefixPaging, self::$suffixPaging, $CMS->input['page'], 'rating');

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

        $sql = "SELECT count(0) cnt FROM ".root_table."rating WHERE {$sql_add} {$field} = '{$val}' AND rating_deleted=0 LIMIT 1";

        return $DB->fetch_data($sql,'rating')[0]['cnt'];
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
        if($data['rating_name'] == "")
        {
            $return['msg'][] = $CMS->lang['incomplete_name'];
        }

        if($data['rating_label'] == "")
        {
            $return['msg'][] = $CMS->lang['incomplete_label'];
        }

        if($data['rating_value']=='')
        {
            $return['msg'][] = $CMS->lang['rating_value'];
        }

        if($return['msg'])
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
            $CMS->lang['rating_name'],
            $CMS->lang['rating_code'],
            $CMS->lang['rating_type'],
            $CMS->lang['rating_start_time'],
            $CMS->lang['rating_end_time'],
            $CMS->lang['rating_status'],
            $CMS->lang['rating_time'],
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
        $fields = 'no,rating_name,rating_code,rating_type,rating_start_time,rating_end_time,rating_status,rating_time';

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

    static public function getAll($sql_add="", $order_field = "rating_id", $order_desc = "desc")
    {
        return self::listing($sql_add, 1, $order_field, $order_desc);
    }

    /**
     * Xuất file thống kê
     * @return string
     */
    static function exportToReport()
    {
        global $CMS, $member, $tpl;

        \models\report::excel_header();

        $startRows = 1;

        //Thống kê theo số lượng
        foreach($tpl->statsByUsers as $key => $item)
        {
            $setHeader = ['No', $CMS->lang['name'], $CMS->lang['num_of_rating']];
            $setTitle = $CMS->lang[$key];
            $fields = 'no,name,y';
            \models\report::fillData($fields, $item, $setHeader, $setTitle, $startRows);

            $startRows += count($item)+3;
        }


        //Danh sách chi tiết
        $setHeader = [
            'No.',
            $CMS->lang['rating_code'],
            $CMS->lang['rating_link_demo'],
            $CMS->lang['rating_status'],
            $CMS->lang['rating_layout_designer'],
            $CMS->lang['rating_frontend_designer'],
            $CMS->lang['rating_developer'],
            $CMS->lang['rating_time'],
        ];
        $setTitle = $CMS->lang['list'];

        /**
         * Fields to export
         * Note: muste by match with header cols (order and numbers)
         */
        $fields = 'no,rating_code,rating_link_demo,rating_status,rating_layout_designer,rating_frontend_designer,rating_developer,rating_time';

        $results = self::listing('',1);

        foreach ($results as $no => $result)
        {
            $results[$no] = self::convertValueExport($result);
        }

        \models\report::fillData($fields, $results, $setHeader, $setTitle, $startRows);

        // Set name file
        $file_name = "report_rating_u{$member['user_id']}.xls";

        // Create file and return link download
        return \models\report::excel_output($file_name);
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

            $sql_add .= " ({$prefix}rating_name LIKE '%{$keyword}%' OR {$prefix}rating_label LIKE '%{$keyword}%') AND ";
        }

        if(isset($data['rating_status']) && $data['rating_status'] !== '')
        {
            $rating_status = intval($data['rating_status']);
            $sql_add .= " {$prefix}rating_status = {$rating_status} AND ";
        }

        if(isset($data['rating_name']) && $data['rating_name']!=='')
        {
            $rating_name = urldecode($data['rating_name']);
            $sql_add .= " {$prefix}rating_name LIKE '%{$rating_name}%' AND ";
        }

        if(isset($data['rating_label']) && $data['rating_label']!=='')
        {
            $rating_label = urldecode($data['rating_label']);
            $sql_add .= " {$prefix}rating_label='{$rating_label}' AND ";
        }

        if(isset($data['rating_created_time']) && $data['rating_created_time']!=='')
        {
            $rating_created_time = $CMS->class->date->date2time($data['rating_created_time'],1);
            $sql_add .= " {$prefix}rating_created_time>=$rating_created_time AND ";
        }

        if(isset($data['rating_created_time_to']) && $data['rating_created_time_to']!=='')
        {
            $rating_created_time_to = $CMS->class->date->date2time($data['rating_created_time_to'],1)+(3600*24);
            $sql_add .= " {$prefix}rating_created_time<$rating_created_time_to AND ";
        }

        return $sql_add;
    }

    static function quickUpdateSortOrder($rating_id=0, $rating_order=0)
    {
        global $CMS, $DB;

        $rating_id *= 1;
        $rating_order *= 1;

        $data = ['rating_id' => $rating_id,'rating_order' => $rating_order];

        $result = [];

        $CMS->class->logs->key = "rating_{$rating_id}";

        if($DB->update("rating", $data, 'rating_id')) {
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