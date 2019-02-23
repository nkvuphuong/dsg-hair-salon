<?php

namespace models;

use \core\ezy;
use lib\date;
use lib\input;
use lib\db;
use \PHPExcel;
use \PHPExcel_Style_Alignment;
use \PHPExcel_Style_Color;
use \PHPExcel_Style_Fill;
use \PHPExcel_Style_Border;
use \PHPExcel_IOFactory;

ezy::load_model('store');
ezy::load_model('staff');
ezy::load_model('rating');

class report
{
    /**
     * @param string $sqlAdd
     * @return mixed
     */
    public static function SalesReportByStoreByStore($sqlAdd = "")
    {
        global $DB;

        //Report sales by store

        $sql = "SELECT S.store_id, S.store_name, SUM(ord_total) AS sales_total FROM " . root_table . "order O RIGHT JOIN " . root_table . "store S ON O.store_id = S.store_id WHERE store_deleted=0 AND ord_deleted=0 AND ord_status=2 {$sqlAdd} GROUP BY S.store_id ORDER BY sales_total DESC";

        return $DB->fetch_data($sql);
    }

    /**
     * @param $start
     * @param $end
     * @return mixed
     */
    public static function salesGeneralReportChart($start, $end)
    {
        global $DB, $CMS;

        $sqlAdd = "";

        if ($start) {
            $sqlAdd .= " AND ord_time >= {$start} ";
        }

        if ($end) {
            $sqlAdd .= " AND ord_time < {$end} ";
        }

        $rs = static::SalesReportByStoreByStore($sqlAdd);

        $datasets = [];
        $extra = [];
        $labels = [];

        foreach ($rs as $item) {
            $datasets[] = $item['sales_total'];
            $extra[] = $CMS->class->input->currency($item['sales_total']);
            $labels[] = ' ' . $item['store_name'];
        }
        $data['datasets'][] = ['data' => $datasets];
        $data['labels'] = $labels;
        $data['extra'] = $extra;
        return $data;
    }

    /**
     * @param $groupField
     * @param string $sqlAdd
     * @return mixed
     */
    public static function SalesReportByStoreByTime($groupField, $sqlAdd = "")
    {
        global $CMS, $DB;

        $sql = "SELECT S.store_id, S.store_name, SUM(ord_total) AS sales_total, {$groupField} AS time FROM " . root_table . "order O RIGHT JOIN " . root_table . "store S ON O.store_id = S.store_id WHERE store_deleted=0 AND ord_deleted=0 AND ord_status=2 {$sqlAdd} GROUP BY S.store_id, time ORDER BY time DESC, S.store_name";

        return $DB->fetch_data($sql);
    }

    /**
     * @param $start
     * @param $end
     * @param string $type
     * @return array
     */
    public static function SalesReportByStoreByTimeChart($start, $end, $type = 'week')
    {
        global $CMS;

        $stores = store::getStores();
        $sqlAdd = "";
        $groupField = "DATE_FORMAT(FROM_UNIXTIME(ord_time), '%Y-%m-%d')";

        if ($type == 'week') {
            $timesteps = date::daysOfWeek();
            $groupField = "WEEKDAY(DATE_FORMAT(FROM_UNIXTIME(ord_time), '%Y-%m-%d'))";
        } else if ($type == 'month') {
            $timesteps = date::daysOfMonth(date('Y', $start), date('m', $start));
            $groupField = "DATE_FORMAT(FROM_UNIXTIME(ord_time), '%d')";
        } else if ($type == 'quarter') {
            $quarter = date::getQuarter($start);
            $timesteps = date::getMonthsOfQuarter($quarter['quarter']);
            $groupField = "QUARTER(DATE_FORMAT(FROM_UNIXTIME(ord_time), '%Y-%m-%d'))";
        } else if ($type == 'year') {
            $timesteps = date::monthsOfYear();
            $groupField = "DATE_FORMAT(FROM_UNIXTIME(ord_time), '%m')";
        }

        if ($start) {
            $sqlAdd .= " AND ord_time >= {$start} ";
        }

        if ($end) {
            $sqlAdd .= " AND ord_time < {$end} ";
        }

        $rs = static::SalesReportByStoreByTime($groupField, $sqlAdd);

        $labels = []; //Global labels
        $datasets = [];
        $groupData = []; //Grouping data

        foreach ($timesteps as $timestep) {
            if ($type == 'week') {
                $labels[] = input::lang('day_' . $timestep);
            } else if ($type == 'month') {
                $labels[] = substr("0{$timestep}", -2) . '-' . date('m', $start);
            } else if ($type == 'quarter') {
                $labels[] = "Tháng {$timestep}";
            } else if ($type == 'year') {
                $labels[] = "Tháng {$timestep}";
            }
        }

        foreach ($rs as $r) {
            $groupData[$r['store_id']][$r['time']] = $r['sales_total'];
        }

        foreach ($stores as $store) {
            $label = $store['store_name'];
            $data = [];
            $extra = [];

            foreach ($timesteps as $timestep) {
                $totalVal = input::arrayValue(input::arrayValue($groupData, $store['store_id'], []), $timestep, 0) * 1;
                $data[] = $totalVal;
                $extra[] = $CMS->class->input->currency($totalVal);
            }

            $datasets[] = compact('label', 'data', 'extra');
        }

        return compact('labels', 'datasets');
    }

    /**
     * @param $sqlAdd
     * @return mixed
     */
    static function reportSalesByStaff($sqlAdd = "", $statsVal = "SUM(ordi_total)")
    {
        global $CMS, $DB;

        $sql = "SELECT ordi_staff, user_display_name, {$statsVal} AS val 
        FROM " . root_table . "order_item I
        RIGHT JOIN " . root_table . "user U ON ordi_staff = U.user_id
        LEFT JOIN " . root_table . "order O ON O.ord_id = I.ord_id
        WHERE ord_deleted = 0 AND ord_status = 2 AND ordi_deleted = 0 AND U.user_deleted=0 {$sqlAdd}
        GROUP BY ordi_staff
        ORDER BY val DESC";

        return $DB->fetch_data($sql);
    }

    /**
     * @param string $start
     * @param string $end
     * @param int $store
     * @return array
     */
    static function reportSalesByStaffChart($start = "", $end = "", $store = 0, $statsVal = "SUM(ordi_total)", $label = "Doanh số", $valType = 'currency')
    {
        global $CMS;

        $sqlAdd = "";

        if ($start) {
            $sqlAdd .= " AND ord_time >= {$start} ";
        }

        if ($end) {
            $sqlAdd .= " AND ord_time < {$end} ";
        }

        if ($store) {
            $sqlAdd .= " AND I.store_id = {$store} ";
        }

        $rs = static::reportSalesByStaff($sqlAdd, $statsVal);
        $labels = array_column($rs, 'user_display_name');
        $datasetLabel = $label;
        $datasetData = array_column($rs, 'val');
        $datasetExtra = array_map(function ($x) use ($CMS, $valType) {
            return $valType == 'currency' ? $CMS->class->input->currency($x) : $x;
        }, $datasetData);

        $data = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $datasetLabel,
                    'data' => $datasetData,
                    'extra' => $datasetExtra,
                ]
            ]
        ];

        return $data;
    }

    /**
     * @param $sqlAdd
     * @return mixed
     */
    static function reportSalesByServices($sqlAdd = "")
    {
        global $CMS, $DB;

        $sql = "SELECT P.product_id, product_name, SUM(ordi_total) AS val 
        FROM " . root_table . "order_item I
        RIGHT JOIN " . root_table . "product P ON I.product_id = P.product_id
        LEFT JOIN " . root_table . "order O ON O.ord_id = I.ord_id
        WHERE ord_deleted = 0 AND ord_status = 2 AND ordi_deleted = 0 AND P.product_deleted=0 {$sqlAdd}
        GROUP BY P.product_id
        ORDER BY val DESC";

        return $DB->fetch_data($sql);
    }

    static function reportSalesByServicesChart($start = "", $end = "", $store = 0)
    {
        global $CMS;

        $sqlAdd = "";

        if ($start) {
            $sqlAdd .= " AND ord_time >= {$start} ";
        }

        if ($end) {
            $sqlAdd .= " AND ord_time < {$end} ";
        }

        if ($store) {
            $sqlAdd .= " AND I.store_id = {$store} ";
        }

        $rs = static::reportSalesByServices($sqlAdd);
        $labels = array_column($rs, 'product_name');
        $datasetLabel = "Doanh số";
        $datasetData = array_column($rs, 'val');
        $datasetExtra = array_map(function ($x) use ($CMS) {
            return $CMS->class->input->currency($x);
        }, $datasetData);

        $data = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $datasetLabel,
                    'data' => $datasetData,
                    'extra' => $datasetExtra,
                ]
            ]
        ];

        return $data;
    }

    /**
     * @param string $sqlAdd
     * @param string $statsVal
     * @return mixed
     */
    static function reportRating($sqlAdd = "", $statsVal = "COUNT(0)")
    {
        global $CMS, $DB;

        $sql = "SELECT ordi_staff, rating_id, user_display_name, {$statsVal} AS val 
        FROM " . root_table . "order_item I
        RIGHT JOIN " . root_table . "user U ON ordi_staff = U.user_id
        LEFT JOIN " . root_table . "order O ON O.ord_id = I.ord_id
        WHERE ord_deleted = 0 AND ord_status = 2 AND ordi_deleted = 0 AND U.user_deleted=0 {$sqlAdd}
        GROUP BY ordi_staff, rating_id
        ORDER BY ordi_staff, rating_id";

        return $DB->fetch_data($sql);
    }

    /**
     * @param string $start
     * @param string $end
     * @param int $store
     * @return array
     */
    static function reportRatingChart($start = "", $end = "", $store = 0)
    {
        global $CMS;

        $sqlAdd = "";

        if ($start) {
            $sqlAdd .= " AND ord_time >= {$start} ";
        }

        if ($end) {
            $sqlAdd .= " AND ord_time < {$end} ";
        }

        if ($store) {
            $sqlAdd .= " AND I.store_id = {$store} ";
        }

        $rs = static::reportRating($sqlAdd);
        foreach ($rs as $r) {
            $groupData[$r['rating_id']][$r['ordi_staff']] = $r['val'];
        }

        $staffIds = array_values(array_unique(array_column($rs, 'ordi_staff')));
        $staffNames =  array_values(array_unique(array_column($rs, 'user_display_name')));


        $labels =  $staffNames;

        $ratings = rating::getAll('', 'rating_id', 'asc');

        $datasets = [];

        foreach ($ratings as $rating) {
            $item['label'] = $rating['rating_name'];
            $item['data'] = [];
            foreach ($staffIds as $staffId) {
                $item['data'][] = input::arrayValue(input::arrayValue($groupData, $rating['rating_id'], []), $staffId, 0) * 1;
            }
            $datasets[] = $item;
        }

        return compact('labels', 'datasets');
    }
}  

