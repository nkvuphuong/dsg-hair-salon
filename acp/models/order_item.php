<?php

namespace models;

use core\ezy;
use lib\date;
use lib\db;
use lib\input;
use \lib\template;

class order_item
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
     * @var array
     */
    static private $tmp = [];

    /**
     * Get list orders
     * @param string $sql_add
     * @param integer $disabled_paging
     * @return array
     */
    static public function listing($sql_add = "", $disabled_paging = 0, $order_field = "ordi_id", $order_desc = "desc")
    {
        global $CMS, $DB;

        // Update Arrange Data
        self::$arrangeData = trim("ordi_id,ordi_time");

        // Set default for Arrange
        $default_field = input::get('order', $order_field);
        $default_order = input::get('by', $order_desc);

        // SQL Condition
        $sql_add .= " ordi_deleted=0 AND ";

        $CMS->input['keyword'] = input::get('keyword', input::get('term'));

        $sql_add .= self::getSqlAdd($CMS->input, 'I.');

        $sql = "SELECT * FROM " . root_table . "order_item I RIGHT JOIN " . root_table . "order O
         ON I.ord_id = O.ord_id
         WHERE {$sql_add} 1=1 ORDER BY {$default_field} {$default_order}";

        // Create SQL Query for listing Data
        if ($disabled_paging == 1) {
            $CMS->show_page = "";

            return $DB->fetch_data($sql);
        } else {
            list($CMS->show_page, $results) = $DB->fetch_listing($sql, self::$maxPage, self::$prefixPaging, self::$suffixPaging, $CMS->input['page']);

            return $results;
        }
    }

    /**
     * Lấy điều kiện cho truy vấn sql
     * nkvp - 2017.09.28
     */
    static function getSqlAdd($data = [], $prefix = '')
    {
        global $CMS;

        $sql_add = '';

        if (isset($data['ordi_time_from']) && $data['ordi_time_from'] !== '') {
            $ordi_time_from = $CMS->class->date->date2time(urldecode($data['ordi_time_from']), 1);
            $sql_add .= " {$prefix}ordi_time>=$ordi_time_from AND ";
        }

        if (isset($data['ordi_time_to']) && $data['ordi_time_to'] !== '') {
            $ordi_time_to = $CMS->class->date->date2time(urldecode($data['ordi_time_to']), 1) + (3600 * 24);
            $sql_add .= " {$prefix}ordi_time<$ordi_time_to AND ";
        }

        return $sql_add;
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

        $data['ordi_time'] = $data_bk['ordi_time'] ? date::format($data['ordi_time']) : '';
        $data['ordi_booking_time'] = $data_bk['ordi_booking_time'] ? date::format($data['ordi_booking_time'], 'full') : '';

        $data['ordi_total'] = $CMS->class->input->currency($data_bk['ordi_total']);

        //Staff convert
        static::$tmp['staffs'] = static::$tmp['staffs'] ? static::$tmp['staffs'] : [];
        if (isset(static::$tmp['staffs'][$data_bk['ordi_staff']])) {
            $data['staff'] = static::$tmp['staffs'][$data_bk['ordi_staff']];
        } else {
            $staff = $CMS->user->get_info($data_bk['ordi_staff']);
            $data['staff'] = $staff;
            static::$tmp['staffs'][$data_bk['ordi_staff']] = $staff;
        }

        //Customer convert
        static::$tmp['customers'] = static::$tmp['customers'] ? static::$tmp['customers'] : [];
        if (isset(static::$tmp['customers'][$data_bk['cus_id']])) {
            $data['customer'] = static::$tmp['customers'][$data_bk['cus_id']];
        } else {
            $customer = $CMS->customer->getInfo($data_bk['cus_id']);
            $data['customer'] = $staff;
            static::$tmp['customers'][$data_bk['cus_id']] = $customer;
        }

        $data['record_cnt'] = self::$record_cnt;
        self::$record_cnt++;
        return $data;
    }
}