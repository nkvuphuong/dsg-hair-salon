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

ezy::load_model('discount');

class discount_items
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

    static public $applyFor = ['all', 'amount_from', 'product', 'product_group', 'customer_group'];

    /**
     * Add new postion
     * @param array $data array input
     * @output boolean|array
     */
    static public function add($data = [])
    {
        global $CMS, $member, $DB;

        $time = time();

        $data['user_id'] = $member['user_id'];
        $data['di_deleted'] = 0;
        $data['di_time'] = $time;
        $data['di_apply_rules'] = $data['di_apply_rules'][$data['di_apply_for']];
        $data['di_apply_rules'] = is_array($data['di_apply_rules']) ? json_encode($data['di_apply_rules'], JSON_UNESCAPED_UNICODE) : $data['di_apply_rules'];
        $data['di_start_time'] = is_numeric($data['di_start_time']) ? $data['di_start_time'] : ($data['di_start_time'] != '' ? $CMS->class->date->date2time($data['di_start_time']) : 0);
        $data['di_end_time'] = is_numeric($data['di_end_time']) ? $data['di_end_time'] : ($data['di_end_time'] != '' ? $CMS->class->date->date2time($data['di_end_time']) : 0);
        $data['di_times'] = intval($data['di_times']);

        //Get fiels
        $fields = $DB->get_column_names('discount_items');
        $fields = array_diff($fields, ['di_id']); //remove fiels id

        $sql_fields = implode(',', $fields);

        $sql_values = "";

        foreach ($fields as $field) {
            if (is_numeric($data[$field])) {
                $sql_values .= "{$data[$field]},";
            } else {
                $sql_values .= "'{$data[$field]}',";
            }
        }

        $sql_values = trim($sql_values, ',');

        if (!$sql_values) {
            return false;
        }

        $sql = "INSERT INTO " . root_table . "discount_items({$sql_fields}) VALUES({$sql_values})";

        $DB->query($sql);

        //Clear cache
        $CMS->class->cache->mdelete('discount_items');

        $insertedRecord = self::insertedRecord($data['di_code']); //Get inserted record

        $CMS->class->logs->key = "di_{$insertedRecord['di_id']}";
        $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['added_di_success']}: <strong>{$insertedRecord['di_code']}</strong>");

        //update number of items
        discount::updateNumOfItems($insertedRecord['discount_id']);

        return $insertedRecord;
    }

    /**
     * Edit discount_items
     * @param array $data
     * @return bool|array
     */
    static public function edit($data = [])
    {
        global $CMS, $member, $DB;

        $data['di_apply_rules'] = $data['di_apply_rules'][$data['di_apply_for']];
        $data['di_apply_rules'] = is_array($data['di_apply_rules']) ? json_encode($data['di_apply_rules'], JSON_UNESCAPED_UNICODE) : $data['di_apply_rules'];
        $data['di_start_time'] = is_numeric($data['di_start_time']) ? $data['di_start_time'] : ($data['di_start_time'] != '' ? $CMS->class->date->date2time($data['di_start_time']) : 0);
        $data['di_end_time'] = is_numeric($data['di_end_time']) ? $data['di_end_time'] : ($data['di_end_time'] != '' ? $CMS->class->date->date2time($data['di_end_time']) : 0);
        $data['di_times'] = intval($data['di_times']);

        $oldData = self::getInfo($data['id']);

        if (!$oldData) {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            return false;
        }

        //Get fiels
        $fields = $DB->get_column_names('discount_items');
        $fields = array_diff($fields, ['di_id']); //remove field id

        $sql_set = "";

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                if (is_numeric($data[$field])) {
                    $sql_set .= "{$field}={$data[$field]},";
                } else {
                    $sql_set .= "{$field}='{$data[$field]}',";
                }
            }
        }

        $sql_set = trim($sql_set, ',');

        $sql = "UPDATE " . root_table . "discount_items SET {$sql_set} WHERE di_id='{$CMS->input['id']}'";
        $DB->query($sql);

        //Clear cache
        $CMS->class->cache->mdelete('discount_items');

        $updatedRecord = self::getInfo($CMS->input['id']);

        $CMS->class->logs->key = "di_{$updatedRecord['di_id']}";
        $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['updated_di_success']}: <strong>{$updatedRecord['di_code']}</strong>");

        return $updatedRecord;
    }

    /**
     * Delete logo
     * @return bool
     */
    static public function delete()
    {
        global $CMS, $DB;

        if ($discount_id = intval($CMS->input['discount_id'])) //deleted all by discount_id
        {
            // Get info
            $data = discount::getInfo($discount_id);

            if (!$data) {
                return false;
            }

            // Update info
            $DB->query("UPDATE " . root_table . "discount_items SET di_deleted=1 WHERE discount_id={$data['discount_id']}");

            //Clear cache
            $CMS->class->cache->mdelete('discount_items');

            // Create log
            $CMS->class->logs->key = "discount_{$data['discount_id']}";
            $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['di_deleted']} <b>{$data['discount_name']}</b>") . "<br />";

            discount::updateNumOfItems($data['discount_id']);

            return true;
        } else {
            // Get info
            $data = self::getInfo();

            // Check existing
            if (!$data) {
                return false;
            }

            // Update info
            $DB->query("UPDATE " . root_table . "discount_items SET di_deleted=1 WHERE di_id={$data['di_id']}");

            //Clear cache
            $CMS->class->cache->mdelete('discount_items');

            // Create log
            $CMS->class->logs->key = "di_{$data['di_id']}";
            $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['di_deleted']} <b>{$data['di_code']}</b>") . "<br />";

            discount::updateNumOfItems($data['discount_id']);

            return true;
        }
    }

    /**
     * Delete multi discount_items
     * @return bool
     */
    static public function mdelete()
    {
        global $CMS, $DB;

        $deleted = 0;

        $_SESSION["msg"] .= "";

        for ($i = 0; $i < intval($CMS->input["data_cnt"]); $i++) {
            $id = intval($CMS->input["id_{$i}"]);

            if ($id) {
                $data = self::getInfo($id);

                $DB->query("UPDATE " . root_table . "discount_items SET di_deleted=1 WHERE di_id={$id}");

                @unlink("{$CMS->vars['upload_dir']}/{$data['di_src']}");

                $CMS->class->logs->key = "di_{$data['di_id']}";
                $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['di_deleted']} <b>{$data['di_title']}</b>") . "<br />";

                $deleted = 1;
            }
        }

        if ($deleted == 0) {

            //Clear cache
            $CMS->class->cache->mdelete('discount_items');

            $_SESSION["msg"] .= "{$CMS->lang['di_delete_failed']}";
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

        if (!$id) {
            $id = $CMS->input['id'];
        }

        if (is_numeric($id)) {
            $id = intval($id);
            $sql_add = " di_id='{$id}' AND ";
        } else {
            $sql_add = " di_token_key='{$id}' AND ";
        }

        $sql = "SELECT * FROM " . root_table . "discount_items WHERE {$sql_add} di_deleted=0 ORDER BY di_id LIMIT 0,1";

        return $DB->fetch_data($sql,'discount_items')[0];
    }

    /**
     * Get infomation of record which just added
     * @param string $token_key
     * @return bool|array
     */
    static public function insertedRecord($di_code = "")
    {
        global $CMS, $DB;

        if (!$di_code) return false;

        $sql = "SELECT * FROM " . root_table . "discount_items WHERE di_code='{$di_code}' ORDER BY di_id DESC LIMIT 1";

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
        global $CMS;

        $data['data_bk'] = $data_bk = $data;

        $data['di_num_chars'] = isset($data_bk['di_num_chars']) ? intval($data_bk['di_num_chars']) : 5;
        $data['di_value'] = isset($data_bk['di_value']) ? floatval($data_bk['di_value']) : 1;
        $data['unlimited'] = isset($data_bk['unlimited']) ? $data_bk['unlimited'] : 'on';

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
        $data['di_start_time'] = $data_bk['di_start_time'] ? date::format($data_bk['di_start_time']) : '';
        $data['di_end_time'] = $data_bk['di_end_time'] ? date::format($data_bk['di_end_time']) : '';

        $data['di_num_chars'] = isset($data_bk['di_num_chars']) ? intval($data_bk['di_num_chars']) : (strlen($data_bk['di_code']) ? strlen($data_bk['di_code']) : 5);
        $data['di_value'] = isset($data_bk['di_value']) ? floatval($data_bk['di_value']) : 1;

        $data['di_apply_rules'] = [];
        $data['di_apply_rules'][$data_bk['di_apply_for']] = is_array($data_bk['di_apply_rules']) ? $data_bk['di_apply_rules'] : json_decode($data_bk['di_apply_rules'], 1);

        $data['unlimited'] = !$data['di_times'] ? 'on' : '';

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

        $data['di_time'] = $data_bk['di_time'] ? date::format($data['di_time']) : '';
        $data['di_start_time'] = $data_bk['di_start_time'] ? date::format($data['di_start_time']) : '';
        $data['di_end_time'] = $data_bk['di_end_time'] ? date::format($data['di_end_time']) : '';

        if ($data_bk['di_start_time'] && $data_bk['di_end_time']) {
            $data['di_period_time'] = "{$data['di_start_time']} - {$data['di_end_time']}";
        } else if ($data_bk['di_start_time']) {
            $data['di_period_time'] = "{$CMS->lang['from']} {$data['di_start_time']}";
        } else if ($data_bk['di_end_time']) {
            $data['di_period_time'] = "{$CMS->lang['to']} {$data['di_end_time']}";
        } else {
            $data['di_period_time'] = 'N/A';
        }

        if ($CMS->permit['discount_items_read'] || $CMS->permit['discount_items_is_root']) {
            $data['di_code'] = "<a href='{$CMS->vars['root_domain']}/?site=discount_items&act=show&id={$data_bk['di_id']}'>{$data_bk['di_code']}</a>";
        }

        $data['record_cnt'] = self::$record_cnt;

        if ($CMS->vars['userTemp'][$data_bk['user_id']]) {
            $data['userInfo'] = $CMS->vars['userTemp'][$data_bk['user_id']];
        } else {
            $CMS->vars['userTemp'][$data_bk['user_id']] = $data['userInfo'] = $CMS->user->get_info($data_bk['user_id']);
        }

        if ($CMS->permit['user_read'] || $CMS->permit['user_is_root']) {
            $data['user_id'] = "<a href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['userInfo']['user_id']}'>{$data['userInfo']['user_display_name']}</a>";
        } else {
            $data['user_id'] = $data['userInfo']['user_display_name'];
        }

        if ($CMS->vars['discountTemp'][$data_bk['user_id']]) {
            $data['discountInfo'] = $CMS->vars['discountTemp'][$data_bk['discount_id']];
        } else {
            $CMS->vars['discountTemp'][$data_bk['discount_id']] = $data['discountInfo'] = discount::getInfo($data_bk['discount_id']);
        }

        if ($CMS->permit['discount_items_read'] || $CMS->permit['discount_items_is_root']) {
            $data['discount_id'] = "<a href='{$CMS->vars['root_domain']}/?site=discount&act=show&id={$data['discountInfo']['discount_id']}'>{$data['discountInfo']['discount_name']}</a>";
        } else {
            $data['discount_id'] = $data['discountInfo']['discount_name'];
        }


        $data['di_times'] = $data['di_times'] ? $data['di_times'] : $CMS->lang['unlimited'];

        $data['di_type'] = $CMS->lang['di_type_' . $data_bk['di_type']];
        $data['di_value'] = "{$data_bk['di_value']}{$data['di_type']}";
        $data['di_status'] = $CMS->lang['di_status_' . $data_bk['di_status']];
        $data['di_apply_for'] = $CMS->lang['di_apply_for_' . $data_bk['di_apply_for']];

        self::$record_cnt++;

        return $data;
    }

    static public function convertValueExport($data = [])
    {
        global $CMS;

        $data['data_bk'] = $data_bk = $data;

        // Replace search content
        $data = $CMS->class->search->convertvalue($data);

        $data['di_time'] = $data_bk['di_time'] ? date::format($data['di_time']) : '';
        $data['di_start_time'] = $data_bk['di_start_time'] ? date::format($data['di_start_time']) : '';
        $data['di_end_time'] = $data_bk['di_end_time'] ? date::format($data['di_end_time']) : '';

        $data['record_cnt'] = self::$record_cnt;

        if ($CMS->vars['discountTemp'][$data_bk['user_id']]) {
            $data['discountInfo'] = $CMS->vars['discountTemp'][$data_bk['discount_id']];
        } else {
            $CMS->vars['discountTemp'][$data_bk['discount_id']] = $data['discountInfo'] = discount::getInfo($data_bk['discount_id']);
        }

        if ($CMS->permit['discount_items_read'] || $CMS->permit['discount_items_is_root']) {
            $data['discount_id'] = "<a href='{$CMS->vars['root_domain']}/?site=discount&act=show&id={$data['discountInfo']['discount_id']}'>{$data['discountInfo']['discount_name']}</a>";
        } else {
            $data['discount_id'] = $data['discountInfo']['discount_name'];
        }


        $data['di_times'] = $data['di_times'] ? $data['di_times'] : $CMS->lang['unlimited'];

        $data['di_type'] = $CMS->lang['di_type_' . $data_bk['di_type']];
        $data['di_status'] = $CMS->lang['di_status_' . $data_bk['di_status']];
        $data['di_apply_for'] = $CMS->lang['di_apply_for_' . $data_bk['di_apply_for']];

        self::$record_cnt++;

        return $data;
    }


    /**
     * Get list discount_items
     * @param string $sql_add
     * @return array
     */
    static public function listing($sql_add = "", $is_export = 0)
    {
        global $CMS, $DB;

        // Update Arrange Data
        self::$arrangeData = trim("di_id,di_time");

        // Set default for Arrange
        $default_field = $CMS->input['order'] ? $CMS->input['order'] : "di_id";
        $default_order = $CMS->input['by'] ? $CMS->input['by'] : "desc";

        // SQL Condition
        $sql_add .= " di_deleted=0 AND ";

        $CMS->input['keyword'] = $CMS->input['keyword'] ? $CMS->input['keyword'] : $CMS->input['term'];

        if (isset($CMS->input['keyword']) && $CMS->input['keyword'] !== '') {
            $keyword = urldecode($CMS->input['keyword']);
            $sql_add .= " di_code LIKE '%{$keyword}%' AND ";
        }

        if (isset($CMS->input['discount_id']) && $CMS->input['discount_id'] !== '') {
            $discount_id = intval($CMS->input['discount_id']);
            $sql_add .= " discount_id = {$discount_id} AND ";
        }

        if (isset($CMS->input['di_code']) && $CMS->input['di_code'] !== '') {
            $di_code = urldecode($CMS->input['di_code']);
            $sql_add .= " di_code LIKE '%{$di_code}%' AND ";
        }

        if (isset($CMS->input['di_type']) && $CMS->input['di_type'] !== '') {
            $di_type = intval($CMS->input['di_type']);
            $sql_add .= " di_type = {$di_type} AND ";
        }

        if (isset($CMS->input['di_value']) && $CMS->input['di_value'] !== '') {
            $di_value = floatval($CMS->input['di_value']);
            $sql_add .= " di_value >= {$di_value} AND ";
        }

        if (isset($CMS->input['di_value_to']) && $CMS->input['di_value_to'] !== '') {
            $di_value_to = floatval($CMS->input['di_value_to']);
            $sql_add .= " di_value <= {$di_value_to} AND ";
        }

        if (isset($CMS->input['di_apply_for']) && $CMS->input['di_apply_for'] !== '') {
            $di_apply_for = trim($CMS->input['di_apply_for']);
            $sql_add .= " di_apply_for = '{$di_apply_for}' AND ";
        }

        if (isset($CMS->input['di_start_time']) && $CMS->input['di_start_time'] !== '') {
            $di_start_time = urldecode($CMS->input['di_start_time']);
            $di_start_time = $CMS->class->date->date2time($di_start_time);
            $sql_add .= " di_start_time >= $di_start_time AND ";
        }

        if (isset($CMS->input['di_start_time_to']) && $CMS->input['di_start_time_to'] !== '') {
            $di_start_time_to = urldecode($CMS->input['di_start_time_to']);
            $di_start_time_to = $CMS->class->date->date2time($di_start_time_to) + (3600 * 24);
            $sql_add .= " di_start_time < $di_start_time_to AND ";
        }

        if (isset($CMS->input['di_end_time']) && $CMS->input['di_end_time'] !== '') {
            $di_end_time = urldecode($CMS->input['di_end_time']);
            $di_end_time = $CMS->class->date->date2time($di_end_time);
            $sql_add .= " di_end_time >= $di_end_time AND ";
        }

        if (isset($CMS->input['di_end_time_to']) && $CMS->input['di_end_time_to'] !== '') {
            $di_end_time_to = urldecode($CMS->input['di_end_time_to']);
            $di_end_time_to = $CMS->class->date->date2time($di_end_time_to) + (3600 * 24);
            $sql_add .= " di_end_time < $di_end_time_to AND ";
        }

        $sql = "SELECT * FROM " . root_table . "discount_items WHERE {$sql_add} 1=1 ORDER BY {$default_field} {$default_order}";

        // Create SQL Query for listing Data
        if ($is_export == 1) {
            $CMS->show_page = "";
            return $DB->fetch_data($sql, 'discount_items');
        } else {
            list($CMS->show_page, $results) = $DB->fetch_listing($sql, self::$maxPage, self::$prefixPaging, self::$suffixPaging, $CMS->input['page'], 'discount_items');

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
    static function checkExisted($field, $val, $except = null)
    {
        global $CMS, $DB;

        if ($except !== null) {
            $sql_add = " {$field} != '{$except}' AND ";
        }

        $sql = "SELECT count(0) cnt FROM " . root_table . "discount_items WHERE {$sql_add} {$field} = '{$val}' AND di_deleted=0 LIMIT 1";

        return $DB->fetch_data($sql, 'discount_items')[0]['cnt'];
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

    static function generateCode($num_chars, $codeList = [])
    {
        global $CMS, $DB;
        if ($num_chars <= 0) return false;
        $di_code = strtoupper($CMS->class->random->character($num_chars));
        while (in_array($di_code, $codeList) || self::checkExisted('di_code', $di_code)) {
            //If duplicate, create new code
            $di_code = $CMS->class->random->character($num_chars);
        }
        return $di_code;
    }

    static function validate($data, $exceptCode = null)
    {
        global $CMS;

        $return['valid'] = true;


        if ($data['di_code'] == '') {
            $return['msg'][] = $CMS->lang['incomplete_code'];
        } else {
            if (strlen($data['di_code']) < 5 || strlen($data['di_code']) > 32) {
                $return['msg'][] = $CMS->lang['invalid_code'];
            } else {
                //check duplicate
                if (self::checkExisted('di_code', $data['di_code'], $exceptCode)) {
                    $return['msg'][] = $CMS->lang['code_is_existed'];
                }
            }
        }


        if ($data['di_value'] == '') {
            $return['msg'][] = $CMS->lang['incomplete_value'];
        } else if ($data['di_value'] <= 0 || ($data['di_type'] == 0 && $data['di_value'] > 100)) {
            $return['msg'][] = $CMS->lang['invalid_value'];
        }

        if (!in_array($data['di_apply_for'], self::$applyFor)) {
            $return['msg'][] = $CMS->lang['invalid_apply_for'];
        } else {
            if (($key = $data['di_apply_for']) == 'amount_from') //Đơn giá từ
            {
                if ($data['di_apply_rules'][$key] <= 0) {
                    $return['msg'][] = $CMS->lang['invalid_amount_from'];
                }
            } elseif (($key = $data['di_apply_for']) == 'product') //Sản phẫm
            {
                if (!$data['di_apply_rules'][$key]) {
                    $return['msg'][] = $CMS->lang['incomplete_product'];
                }
            } elseif (($key = $data['di_apply_for']) == 'product_group') //Nhóm Sản phẫm
            {
                if (!$data['di_apply_rules'][$key]) {
                    $return['msg'][] = $CMS->lang['incomplete_product_group'];
                }
            } elseif (($key = $data['di_apply_for']) == 'customer_group') //Nhóm KH
            {
                if (!$data['di_apply_rules'][$key]) {
                    $return['msg'][] = $CMS->lang['incomplete_customer_group'];
                }
            }
        }

        if ($return['msg']) {
            $return['valid'] = false;
            $return['msg'] = implode('<br>', $return['msg']);
        }

        return $return;
    }

    static function showRules($apply_for, $rules, $glue = ' ')
    {
        global $CMS;

        if ($apply_for == 'amount_from') {
            return $CMS->lang['from'] . ' ' . $CMS->class->input->currency($rules);
        }

        if (!is_array($rules)) {
            $rules = json_decode($rules, 1);
        }

        if (!$rules) return "";

        $arr = [];

        if ($apply_for == "product") {
            foreach ($rules as $id) {
                $data = $CMS->product->getInfo($id);

                if ($data) {
                    $arr[] = "<a href='{$CMS->vars['root_domain']}/?site=product&act=show&id={$id}'><label class='label label-info'>{$data['product_name']}</label></a>";
                }
            }
        } else if ($apply_for == "product_group") {
            foreach ($rules as $id) {
                $data = $CMS->product_group->getInfo($id);

                if ($data) {
                    $arr[] = "<a href='{$CMS->vars['root_domain']}/?site=product_group&act=show&id={$id}'><label class='label label-info'>{$data['product_group_name']}</label></a>";
                }
            }
        } else if ($apply_for == "customer_group") {
            foreach ($rules as $id) {
                $data = $CMS->group_cutomer->getInfo($id);

                if ($data) {
                    $arr[] = "<a href='{$CMS->vars['root_domain']}/?site=group_cutomer&act=show&id={$id}'><label class='label label-info'>{$data['gc_name']}</label></a>";
                }
            }
        }

        return implode($glue, $arr);
    }

    public static function exportToExcel()
    {
        global $CMS, $DB, $member;

        $sql_add = "";

        $setTitle = [
            'No.',
            $CMS->lang['di_code'],
            $CMS->lang['di_value'],
            $CMS->lang['di_type'],
            $CMS->lang['di_used_times'],
            $CMS->lang['di_times'],
            $CMS->lang['di_apply_for'],
            $CMS->lang['di_apply_rules'],
            $CMS->lang['di_start_time'],
            $CMS->lang['di_end_time'],
            $CMS->lang['di_time'],
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
        $fields = 'no,di_code,di_value,di_type,di_used_times,di_times,di_apply_for,di_apply_rules,di_start_time,di_end_time,di_time';

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
            $result['di_apply_rules'] = strip_tags(self::showRules($result['data_bk']['di_apply_for'],$result['di_apply_rules'], ' || '));
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