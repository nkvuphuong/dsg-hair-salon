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

class variants
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
     * @var string $cache_prefix
     */
    static public $cache_prefix = 'variants';

    /**
     * Add new variants
     * @param array $data array input
     * @output boolean|array
     */
    static public function add($data = [])
    {
        global $CMS, $member, $DB;

        $data['var_token_key'] = $CMS->class->random->md5($CMS->class->random->character(16));
        $data['var_time'] = $data['var_time_update'] = time();
        $data['user_id'] = intval($member['user_id']);
        $data['var_in'] = date::hour2Sec($data['var_in']);
        $data['var_out'] = date::hour2Sec($data['var_out']);
        $data['var_order'] = $data['var_order']*1;

        if($DB->insert('variants', $data))
        {
            //Clear cache
            $CMS->class->cache->mdelete('variants');

            $insertedRecord = self::insertedRecord($data['var_token_key']); //Get inserted record

            $CMS->class->logs->key = "var_{$insertedRecord['var_id']}";
            $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['added_var_success']}: <strong>{$insertedRecord['var_title']}</strong>");
            return $insertedRecord;
        }
        else
        {
            $_SESSION['msg'] = "{$CMS->lang['added_var_failed']}";
            return false;
        }
    }

    /**
     * Edit variants
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

        $data['var_in'] = isset($data['var_in']) ? date::hour2Sec($data['var_in']) : $oldData['var_in'];
        $data['var_out'] = isset($data['var_out']) ? date::hour2Sec($data['var_out']) : $oldData['var_out'];
        $data['var_order'] = isset($data['var_order']) ? $data['var_order']*1 : $oldData['var_order'];
        $data['var_time_update'] = time();

        if($DB->update("variants", $data, "var_id"))
        {
            //Clear cache
            $CMS->class->cache->mdelete('variants');

            $updatedRecord = self::getInfo($CMS->input['id']);

            $CMS->class->logs->key = "var_{$updatedRecord['var_id']}";
            $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['updated_var_success']}: <strong>{$updatedRecord['var_title']}</strong>");

            return $updatedRecord;
        }
        else
        {
            $_SESSION['msg'] = "{$CMS->lang['updated_var_failed']}";
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
        $DB->query("UPDATE ".root_table."variants SET var_deleted=1 WHERE var_id={$data['var_id']}");

        //Clear cache
        $CMS->class->cache->mdelete('variants');

        // Create log
        $CMS->class->logs->key = "var_{$data['var_id']}";
        $_SESSION["msg"] = $CMS->class->logs->insert("{$CMS->lang['var_deleted']} <b>{$data['var_title']}</b>")."<br />";

        return true;
    }

    /**
     * Delete multi variants
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

                $DB->query("UPDATE ".root_table."variants SET var_deleted=1 WHERE var_id={$id}");

                $CMS->class->logs->key = "var_{$data['var_id']}";
                $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['var_deleted']} <b>{$data['var_title']}</b>")."<br />";

                $deleted = 1;
            }
        }

        if ( $deleted == 0 )
        {
            //Clear cache
            $_SESSION["msg"] .= "{$CMS->lang['var_delete_failed']}";
        }

        $CMS->class->cache->mdelete('variants');

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
            $sql_add .= " var_id='{$id}' AND ";
        }
        else
        {
            $sql_add .= " (var_token_key='{$id}' AND ";
        }

        $sql = "SELECT * FROM ".root_table."variants WHERE {$sql_add} var_deleted=0 ORDER BY var_id LIMIT 0,1";

        return $DB->fetch_data($sql, 'variants')[0];
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

        $sql = "SELECT * FROM ".root_table."variants WHERE var_token_key='{$token_key}' ORDER BY var_id DESC LIMIT 1";

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
        $data['var_order'] = input::arrayValue($data, 'var_order') * 1;

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

        $data['var_in'] = date::sec2Hour($data_bk['var_in']);
        $data['var_out'] = date::sec2Hour($data_bk['var_out']);
        $data['var_order'] *= 1;

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

        $data['var_time'] = $data_bk['var_time'] ? date::format($data['var_time']) : '';
        $data['var_time_update'] = $data_bk['var_time_update'] ? date::format($data['var_time_update']) : '';

        if($CMS->permit['variants_read'] || $CMS->permit['variants_is_root'])
        {
            $data['var_title'] = "<a style='display: inline-block' href='{$CMS->vars['root_domain']}/?site=variants&act=show&id={$data_bk['var_id']}'>{$data_bk['var_title']}</a>";
        }

        if($CMS->permit['user_read'] || $CMS->permit['user_is_root'])
        {
            $data['user_id'] = "<a style='display: inline-block' href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['userInfo']['user_id']}'>{$data['userInfo']['user_display_name']}</a>";
        }
        else
        {
            $data['user_id'] = $data['userInfo']['user_display_name'];
        }

        $data['var_price_c'] = $CMS->class->input->currency($data['var_price']);

        $data['record_cnt'] = self::$record_cnt;
        self::$record_cnt++;

        return $data;
    }

    static  public function convertValueExport($data=[])
    {
        global $CMS;

        $data['data_bk'] = $data_bk = $data;

        // Replace search content
        $data = $CMS->class->search->convertvalue($data);

        $data['var_time'] = $data_bk['var_time'] ? date::format($data['var_time']) : '';
        $data['var_time_update'] = $data_bk['var_time_update'] ? date::format($data['var_time_update']) : '';
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
     * Get list variants
     * @param string $sql_add
     * @param integer $disabled_paging
     * @return array
     */
    static public function listing($sql_add = "", $disabled_paging = 0, $order_field = "var_id", $order_desc = "desc")
    {
        global $CMS, $DB;

        // Update Arrange Data
        self::$arrangeData = trim("var_id,var_time,var_update_time");

        // Set default for Arrange
        $default_field = input::get('order', $order_field);
        $default_order = input::get('by', $order_desc);

        // SQL Condition
        $sql_add .= " var_deleted=0 AND ";

        $CMS->input['keyword'] = input::get('keyword', input::get('term'));

        $sql_add .= self::getSqlAdd($CMS->input);

        $sql = "SELECT * FROM ".root_table."variants WHERE {$sql_add} 1=1 ORDER BY {$default_field} {$default_order}";

        // Create SQL Query for listing Data
        if ($disabled_paging == 1) {
            $CMS->show_page = "";

            return $DB->fetch_data($sql,'variants');
        } else {
            list($CMS->show_page, $results) = $DB->fetch_listing($sql, self::$maxPage, self::$prefixPaging, self::$suffixPaging, $CMS->input['page'], 'variants');

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

        $sql = "SELECT count(0) cnt FROM ".root_table."variants WHERE {$sql_add} {$field} = '{$val}' AND var_deleted=0 LIMIT 1";

        return $DB->fetch_data($sql,'variants')[0]['cnt'];
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

        if($data['var_title'] == "")
        {
            $return['msg'][] = $CMS->lang['incomplete_name'];
        }

        if($data['var_in'] == "")
        {
            $return['msg'][] = $CMS->lang['var_in'];
        }

        if($data['var_out']=='')
        {
            $return['msg'][] = $CMS->lang['var_out'];
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
            $CMS->lang['var_title'],
            $CMS->lang['var_code'],
            $CMS->lang['var_type'],
            $CMS->lang['var_start_time'],
            $CMS->lang['var_end_time'],
            $CMS->lang['var_status'],
            $CMS->lang['var_time'],
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
        $fields = 'no,var_title,var_code,var_type,var_start_time,var_end_time,var_status,var_time';

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

    static public function getAll($sql_add="", $order_field = "var_id", $order_desc = "desc")
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

            $sql_add .= " ({$prefix}var_title LIKE '%{$keyword}%' OR {$prefix}var_label LIKE '%{$keyword}%') AND ";
        }

        if(isset($data['var_title']) && $data['var_title']!=='')
        {
            $var_title = urldecode($data['var_title']);
            $sql_add .= " {$prefix}var_title LIKE '%{$var_title}%' AND ";
        }

        if(isset($data['var_time']) && $data['var_time']!=='')
        {
            $var_time = $CMS->class->date->date2time($data['var_time'],1);
            $sql_add .= " {$prefix}var_time>=$var_time AND ";
        }

        if(isset($data['var_time_to']) && $data['var_time_to']!=='')
        {
            $var_time_to = $CMS->class->date->date2time($data['var_time_to'],1)+(3600*24);
            $sql_add .= " {$prefix}var_time<$var_time_to AND ";
        }

        return $sql_add;
    }

    static function quickUpdateSortOrder($var_id=0, $var_order=0)
    {
        global $CMS, $DB;

        $var_id *= 1;
        $var_order *= 1;

        $data = ['var_id' => $var_id,'var_order' => $var_order];

        $CMS->class->logs->key = "var_{$var_id}";

        if($DB->update("variants", $data, 'var_id')) {
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

    /**
     * @param $product_id
     * @param string $where
     * @return mixed
     */
    static public function getVariantsByProductId( $product_id = 0 )
    {
        global $CMS, $DB;

        $output = [];

        $sql = "
        SELECT * FROM ".root_table."variants 
        WHERE var_deleted = 0 AND product_id = '{$product_id}' 
        ORDER BY var_id ASC 
        ";
        // print $sql; exit;
        
        $results = $DB->fetch_data($sql, self::$cache_prefix);
        if( is_array($results) )
        {
            foreach( $results as $result )
            {
                $output[] = self::convertValue($result);
            }
        }
        
        return $output;
    }
}