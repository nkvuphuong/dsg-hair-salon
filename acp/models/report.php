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

class report
{
    public static function salesReportByStore()
    {
        global $DB;

        //Report sales by store

        $sql = "SELECT S.store_id, S.store_name, SUM(ord_total) AS sales_total FROM " . root_table . "order O RIGHT JOIN " . root_table . "store S ON O.store_id = S.store_id WHERE store_deleted=0 AND ord_deleted=0 AND ord_status=2 GROUP BY S.store_id ORDER BY sales_total DESC";

        return $DB->fetch_data($sql);
    }

    public static function salesGeneralReportChart()
    {
        global $DB, $CMS;

        $rs = static::salesReportByStore();

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

    public static function salesReportByTime($groupField, $sqlAdd = "")
    {
        global $CMS, $DB;

        $sql = "SELECT S.store_id, S.store_name, SUM(ord_total) AS sales_total, {$groupField} AS time FROM " . root_table . "order O RIGHT JOIN " . root_table . "store S ON O.store_id = S.store_id WHERE store_deleted=0 AND ord_deleted=0 AND ord_status=2 {$sqlAdd} GROUP BY S.store_id, time ORDER BY time DESC, S.store_name";

        return $DB->fetch_data($sql);
    }

    public static function salesReportByTimeChart($start, $end, $type = 'week')
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
        }

        if ($start) {
            $sqlAdd .= " AND ord_time >= {$start} ";
        }

        if ($end) {
            $sqlAdd .= " AND ord_time < {$end} ";
        }

        $rs = static::salesReportByTime($groupField, $sqlAdd);

        $labels = []; //Global labels
        $datasets = [];
        $groupData = []; //Grouping data

        foreach ($timesteps as $timestep) {
            if ($type == 'week') {
                $labels[] = input::lang('day_' . $timestep);
            } else if ($type == 'month') {
                $labels[] = substr("0{$timestep}", -2) . '-' . date('m', $start);
            }
            else if ($type == 'quarter') {
                $labels[] = "Quý {$timestep}";
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
}  

