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

ezy::load_model("product");

class price
{
    /**
     * @param $record_cnt
     *        The order number of Data
     */
    static public $record_cnt = 0;

    /**
     * @param @$arrangeData
     *        The SQL Query for listing Data
     */
    static public $arrangeData;

    /**
     * @param $sqlAdd
     *        The additional SQL for $sql_query
     */
    static public $sqlAdd;

    /**
     * @param $sqlQuery
     *        The SQL Query for listing Data
     */
    static public $sqlQuery;

    /**
     * @param $maxPage
     *        Number of records on per page
     */
    static public $maxPage = 20;

    /**
     * @param $prefixPaging
     *        Prefix for paging url
     */
    static public $prefixPaging = '';

    /**
     * @param $prefixPaging
     *        Suffix for paging url
     */
    static public $suffixPaging = '';

    /**
     * Add new price
     * @param array $data array input
     * @output boolean|array
     */
    static public function add($data = [])
    {
        global $CMS, $member, $DB;

        $data['price_token_key'] = $CMS->class->random->md5($CMS->class->random->character(16));
        $data['price_created_at'] = $data['price_updated_at'] = time();
        $data['user_id'] = intval($member['user_id']);

        if ($DB->insert('price', $data)) {
            //Clear cache
            $CMS->class->cache->mdelete('price');

            $insertedRecord = self::insertedRecord($data['price_token_key']); //Get inserted record

            //Add item
            self::addItems($insertedRecord['price_id'], input::get('items'));

            $CMS->class->logs->key = "price_{$insertedRecord['price_id']}";
            $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['added_price_success']}: <strong>{$insertedRecord['price_name']}</strong>");
            return $insertedRecord;
        } else {
            $_SESSION['msg'] = "{$CMS->lang['added_price_failed']}";
            return false;
        }
    }

    /**
     * Edit price
     * @param array $data
     * @return bool|array
     */
    static public function edit($data = [])
    {
        global $CMS, $member, $DB;

        $oldData = self::getInfo($data['id']);
        if (!$oldData) {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            return false;
        }

        $data['price_updated_at'] = time();

        if ($DB->update("price", $data, "price_id")) {
            //remove all items
            $sql_delete_items = "DELETE FROM " . root_table . "price_item WHERE price_id = {$oldData['price_id']}";
            $DB->query($sql_delete_items);

            //Add item
            self::addItems($oldData['price_id'], input::get('items'));

            //Clear cache
            $CMS->class->cache->mdelete('price');

            $updatedRecord = self::getInfo($CMS->input['id']);

            $CMS->class->logs->key = "price_{$updatedRecord['price_id']}";
            $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['updated_price_success']}: <strong>{$updatedRecord['price_name']}</strong>");

            return $updatedRecord;
        } else {
            $_SESSION['msg'] = "{$CMS->lang['updated_price_failed']}";
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
        if (!$data) {
            return false;
        }

        // Update info
        $DB->query("UPDATE " . root_table . "price SET price_deleted=1 WHERE price_id={$data['price_id']}");

        //Clear cache
        $CMS->class->cache->mdelete('price');

        @unlink("{$CMS->vars['upload_dir']}/{$data['price_src']}");

        // Create log
        $CMS->class->logs->key = "price_{$data['price_id']}";
        $_SESSION["msg"] = $CMS->class->logs->insert("{$CMS->lang['price_deleted']} <b>{$data['price_name']}</b>") . "<br />";

        return true;
    }

    /**
     * Delete multi price
     * @return bool
     */
    static public function mdelete()
    {
        global $CMS, $DB;

        $deleted = 0;

        $_SESSION["msg"] = "";

        for ($i = 0; $i < intval($CMS->input["data_cnt"]); $i++) {
            $id = intval(input::get("id_{$i}"));

            if ($id) {
                $data = self::getInfo($id);

                $DB->query("UPDATE " . root_table . "price SET price_deleted=1 WHERE price_id={$id}");

                @unlink("{$CMS->vars['upload_dir']}/{$data['price_src']}");

                $CMS->class->logs->key = "price_{$data['price_id']}";
                $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['price_deleted']} <b>{$data['price_name']}</b>") . "<br />";

                $deleted = 1;
            }
        }

        if ($deleted == 0) {
            //Clear cache
            $_SESSION["msg"] .= "{$CMS->lang['price_delete_failed']}";
        }

        $CMS->class->cache->mdelete('price');

        return true;
    }

    /**
     * Get infomation of a logo
     * @param int $id
     * @return array
     */
    static public function getInfo($id = 0, $sql_add = '')
    {
        global $CMS, $DB;

        if (!$id) {
            $id = $CMS->input['id'];
        }

        if (!$id) return false;

        if (is_numeric($id)) {
            $id = intval($id);
            $sql_add .= " price_id='{$id}' AND ";
        } else {
            $sql_add .= " (price_token_key='{$id}' AND ";
        }

        $sql = "SELECT * FROM " . root_table . "price WHERE {$sql_add} price_deleted=0 ORDER BY price_id LIMIT 0,1";

        return $DB->fetch_data($sql, 'price')[0];
    }

    /**
     * Get infomation of record which just added
     * @param string $token_key
     * @return bool|array
     */
    static public function insertedRecord($token_key = "")
    {
        global $CMS, $DB;

        if (!$token_key) return false;

        $sql = "SELECT * FROM " . root_table . "price WHERE price_token_key='{$token_key}' ORDER BY price_id DESC LIMIT 1";

        $sql = $DB->query($sql);

        return $DB->fetch_assoc($sql);
    }

    /**
     * Convert input data to add data form
     * @param array $data
     * @return array
     */
    static public function addValue($data = [])
    {
        global $CMS, $member;

        $data['data_bk'] = $data_bk = $data;
        $data['price_status'] = input::arrayValue($data, 'price_status', 1) * 1;

        return $data;
    }

    /**
     * Convert original record to display on edit form
     * @param array $data
     * @return array
     */
    static public function editValue($data = [])
    {
        global $CMS;
        $data['data_bk'] = $data_bk = $data;
        $data['price_stores'] = $data_bk['price_stores'] ? input::jsonDecode($data_bk['price_stores']) : null;
        $data['price_cus_groups'] = $data_bk['price_cus_groups'] ? input::jsonDecode($data_bk['price_cus_groups']) : null;
        $data['price_start_time'] = $data_bk['price_start_time'] ? date::format($data_bk['price_start_time']) : '';
        $data['price_end_time'] = $data_bk['price_end_time'] ? date::format($data_bk['price_end_time']) : '';
        return $data;
    }

    /**
     * Convert original record to show
     * @param array $data
     * @return array
     */
    static public function convertValue($data = [])
    {
        global $CMS;

        $data['data_bk'] = $data_bk = $data;

        // Replace search content
        $data = $CMS->class->search->convertvalue($data);

        $data['price_created_at'] = $data_bk['price_created_at'] ? date::format($data['price_created_at']) : '';
        $data['price_updated_at'] = $data_bk['price_updated_at'] ? date::format($data['price_updated_at']) : '';
        $data['price_start_time'] = $data_bk['price_start_time'] ? date::format($data['price_start_time']) : 'N/A';
        $data['price_end_time'] = $data_bk['price_end_time'] ? date::format($data['price_end_time']) : 'N/A';

        $data['price_stores'] = input::jsonDecode($data_bk['price_stores']);
        $data['price_stores'] = !is_array($data['price_stores']) || !$data['price_stores'] ? [] : $data['price_stores'];

        $data['price_cus_groups'] = input::jsonDecode($data_bk['price_cus_groups']);
        $data['price_cus_groups'] = !is_array($data['price_cus_groups']) || !$data['price_cus_groups'] ? [] : $data['price_cus_groups'];

        $data['price_status'] = "<span class='label " . input::lang('price_status_class_' . $data_bk['price_status']) . "'>" . input::lang('price_status_' . $data_bk['price_status']) . "</span>";

        if ($CMS->permit['price_read'] || $CMS->permit['price_is_root']) {
            $data['price_name'] = "<a style='display: inline-block' href='{$CMS->vars['root_domain']}/?site=price&act=show&id={$data_bk['price_id']}'>{$data_bk['price_name']}</a>";
        }

        $data['record_cnt'] = self::$record_cnt;

        if ($data_bk['user_id']) {
            if (!isset($CMS->vars['userTemp'][$data_bk['user_id']])) {
                $CMS->vars['userTemp'][$data_bk['user_id']] = $CMS->user->get_info($data_bk['user_id']);
            }
            $data['userInfo'] = $CMS->vars['userTemp'][$data_bk['user_id']];
        }

        if ($CMS->permit['user_read'] || $CMS->permit['user_is_root']) {
            $data['user_id'] = "<a style='display: inline-block' href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['userInfo']['user_id']}'>{$data['userInfo']['user_display_name']}</a>";
        } else {
            $data['user_id'] = $data['userInfo']['user_display_name'];
        }

        self::$record_cnt++;

        return $data;
    }
    
    static public function convertStores($storeIds = [] | 0)
    {
        global $CMS;

        $rs = [];
        if(!$storeIds) return $rs;
        if(!is_array($storeIds)) $storeIds = [$storeIds];

        $idStr = implode(', ', $storeIds);

        if($idStr) {
            $rs = store::getStores(" store_id IN ({$idStr}) AND");
        }
        return $rs;
    }

    static public function convertCusGroups($cusGroupIds = [] | 0)
    {
        global $CMS;

        $rs = [];
        if(!$cusGroupIds) return $rs;
        if(!is_array($cusGroupIds)) $cusGroupIds = [$cusGroupIds];

        $idStr = implode(', ', $cusGroupIds);

        if($idStr) {
            $rs = $CMS->group_customer->getAll(" gc_id IN ({$idStr}) AND");
        }
        return $rs;
    }

    static public function convertValueExport($data = [])
    {
        global $CMS;

        $data['data_bk'] = $data_bk = $data;

        // Replace search content
        $data = $CMS->class->search->convertvalue($data);

        $data['price_created_at'] = $data_bk['price_created_at'] ? date::format($data['price_created_at']) : '';
        $data['price_updated_at'] = $data_bk['price_updated_at'] ? date::format($data['price_updated_at']) : '';
        $data['record_cnt'] = self::$record_cnt;

        if ($CMS->vars['userTemp'][$data_bk['user_id']]) {
            $data['userInfo'] = $CMS->vars['userTemp'][$data_bk['user_id']];
        } else {
            $CMS->vars['userTemp'][$data_bk['user_id']] = $data['userInfo'] = $CMS->user->get_info($data_bk['user_id']);
        }

        $data['user_id'] = $data['userInfo']['user_display_name'];

        self::$record_cnt++;

        return $data;
    }

    /**
     * Get list price
     * @param string $sql_add
     * @param integer $disabled_paging
     * @return array
     */
    static public function listing($sql_add = "", $disabled_paging = 0, $order_field = "price_id", $order_desc = "desc")
    {
        global $CMS, $DB;

        // Update Arrange Data
        self::$arrangeData = trim("price_id,price_time,price_update_time");

        // Set default for Arrange
        $default_field = input::get('order', $order_field);
        $default_order = input::get('by', $order_desc);

        // SQL Condition
        $sql_add .= " price_deleted=0 AND ";

        $CMS->input['keyword'] = input::get('keyword', input::get('term'));

        $sql_add .= self::getSqlAdd($CMS->input);

        $sql = "SELECT * FROM " . root_table . "price WHERE {$sql_add} 1=1 ORDER BY {$default_field} {$default_order}";

        // Create SQL Query for listing Data
        if ($disabled_paging == 1) {
            $CMS->show_page = "";

            return $DB->fetch_data($sql, 'price');
        } else {
            list($CMS->show_page, $results) = $DB->fetch_listing($sql, self::$maxPage, self::$prefixPaging, self::$suffixPaging, $CMS->input['page'], 'price');

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
    static public function checkExisted($field, $val, $except = null)
    {
        global $CMS, $DB;

        if ($except !== null) {
            $sql_add = " {$field} != '{$except}' AND ";
        }

        $sql = "SELECT count(0) cnt FROM " . root_table . "price WHERE {$sql_add} {$field} = '{$val}' AND price_deleted=0 LIMIT 1";

        return $DB->fetch_data($sql, 'price')[0]['cnt'];
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
        $url = preg_replace('/(\/page-[0-9]+)/', '', $url);

        return $url;
    }

    static function validate($data, $oldData = [])
    {
        global $CMS;

        $return['valid'] = true;
        $return['msg'] = [];

        if ($data['price_name'] == "") {
            $return['msg'][] = $CMS->lang['incomplete_name'];
        }

        if (count($return['msg'])) {
            $return['valid'] = false;
            $return['msg'] = implode('<br>', $return['msg']);
        }

        return $return;
    }

    public static function exportToExcel()
    {
        global $CMS, $DB, $member;

        $setTitle = [
            'No.',
            $CMS->lang['price_name'],
            $CMS->lang['price_code'],
            $CMS->lang['price_type'],
            $CMS->lang['price_start_time'],
            $CMS->lang['price_end_time'],
            $CMS->lang['price_status'],
            $CMS->lang['price_time'],
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
        $fields = 'no,price_name,price_code,price_type,price_start_time,price_end_time,price_status,price_time';

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

    static public function getAll($sql_add = "", $order_field = "price_id", $order_desc = "desc")
    {
        return self::listing($sql_add, 1, $order_field, $order_desc);
    }

    /**
     * Lấy điều kiện cho truy vấn sql
     * nkvp - 2017.09.28
     */
    static function getSqlAdd($data = [], $prefix = '')
    {
        global $CMS;

        $sql_add = '';

        if (isset($data['keyword']) && $data['keyword'] !== '') {
            $keyword = urldecode($data['keyword']);

            $sql_add .= " ({$prefix}price_name LIKE '%{$keyword}%' OR {$prefix}price_label LIKE '%{$keyword}%') AND ";
        }

        if (isset($data['price_name']) && $data['price_name'] !== '') {
            $price_name = urldecode($data['price_name']);
            $sql_add .= " {$prefix}price_name LIKE '%{$price_name}%' AND ";
        }

        if (isset($data['price_created_at']) && $data['price_created_at'] !== '') {
            $price_created_at = $CMS->class->date->date2time($data['price_created_at'], 1);
            $sql_add .= " {$prefix}price_created_at>=$price_created_at AND ";
        }

        if (isset($data['price_created_at_to']) && $data['price_created_at_to'] !== '') {
            $price_created_at_to = $CMS->class->date->date2time($data['price_created_at_to'], 1) + (3600 * 24);
            $sql_add .= " {$prefix}price_created_at<$price_created_at_to AND ";
        }

        return $sql_add;
    }

    static function quickUpdateSortOrder($price_id = 0, $price_order = 0)
    {
        global $CMS, $DB;

        $price_id *= 1;
        $price_order *= 1;

        $data = ['price_id' => $price_id, 'price_order' => $price_order];

        $CMS->class->logs->key = "price_{$price_id}";

        if ($DB->update("price", $data, 'price_id')) {
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
     * @param $price_id
     * @param $items
     * @return bool
     */
    static function addItems($price_id, $items)
    {
        global $DB;

        if (!$items || !is_array($items) || !$price_id) return false;

        foreach ($items as $product_id => $price) {
            $itemData = [
                'price_id' => $price_id,
                'product_id' => $product_id,
                'item_price' => $price['new'],
                'item_old_price' => $price['old'],
            ];

            $DB->insert('price_item', $itemData);
        }

        return false;
    }

    static function getItems($price_id, $sqlAdd = "")
    {
        global $CMS, $DB;
        $price_id *= 1;
        if(!$price_id) return [];

        $sql = "SELECT * FROM " . root_table . "price_item PI LEFT JOIN " . root_table . "product P ON PI.product_id = P.product_id WHERE {$sqlAdd} price_id={$price_id} AND product_deleted = 0 ORDER BY PI.item_id";

        return $DB->fetch_data($sql, "price_item.product");
    }

    static function getItemsByInputs($input_data, $sqlAdd = "")
    {
        global $CMS, $DB;
        if(!$input_data || !is_array($input_data)) return [];

        $rs = [];

        foreach($input_data as $porduct_id => $item_price) {
            $product = product::getInfo($porduct_id);

            if($product) {
                $product['item_price'] = $item_price;
                $rs[] = $product;
            }
        }

        return $rs;
    }
}