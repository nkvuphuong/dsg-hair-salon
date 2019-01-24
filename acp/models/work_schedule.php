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

class work_schedule
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
     * Add new work_schedule
     * @param array $data array input
     * @output boolean|array
     */
    static public function add($data = [])
    {
        global $CMS, $member, $DB;

        $data['work_schedule_token_key'] = $CMS->class->random->md5($CMS->class->random->character(16));
        $data['user_id'] = intval($member['user_id']);
        $data['work_schedule_date'] = is_numeric($data['work_schedule_date']) ? $data['work_schedule_date']*1 : $CMS->class->date->date2time($data['work_schedule_date']);

        if($DB->insert('work_schedule', $data))
        {
            //Clear cache
            $CMS->class->cache->mdelete('work_schedule');
            return true;
        }
        else
        {
            $_SESSION['msg'] = "{$CMS->lang['added_work_schedule_failed']}";
            return false;
        }
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

        $data['work_schedule_date'] = isset($data['work_schedule_date']) ? $data['work_schedule_date'] : date::format(time());

        return $data;
    }


    static function validate($data, $oldData=[])
    {
        global $CMS, $DB;

        $return['valid'] = true;

        $return['msg'] = [];

        if(!$data['store_id'])
        {
            $return['msg'][] = "Please choose a store";
        }

        if(!$data['work_schedule_date'])
        {
            $return['msg'][] = "Please choose date";
        }

        if(count($return['msg']))
        {
            $return['valid'] = false;
            $return['msg'] = implode('<br>',$return['msg']);
        }

        return $return;
    }


    /**
     * Lấy điều kiện cho truy vấn sql
     * nkvp - 2017.09.28
     */
    static function getSqlAdd($data = [], $prefix='')
    {
        global $CMS;

        $sql_add = '';

        if(isset($data['work_schedule_created_time']) && $data['work_schedule_created_time']!=='')
        {
            $work_schedule_created_time = $CMS->class->date->date2time($data['work_schedule_created_time'],1);
            $sql_add .= " {$prefix}work_schedule_created_time>=$work_schedule_created_time AND ";
        }

        return $sql_add;
    }

    static function cleanData($date, $store_id)
    {
        global $CMS, $DB;

        $date = is_numeric($date) ? $date*1 : $CMS->class->date->date2time($date);
        $store_id *=1 ;

        $sql = "DELETE FROM ".root_table."work_schedule WHERE work_schedule_date={$date} AND store_id={$store_id}";

        //Clear cache
        $CMS->class->cache->mdelete('work_schedule');

        return $DB->query($sql);
    }

    static function loadData($date, $sqlAdd = "")
    {
        global $CMS, $DB;

        if(!$date)
        {
            return ['status' => 'fail', 'msg' => 'Please choose date'];
        }

        $date = is_numeric($date) ? $date*1 : $CMS->class->date->date2time($date);

        $sql = "SELECT * FROM ".root_table."work_schedule WHERE {$sqlAdd} work_schedule_date={$date}";

        $data = $DB->fetch_data($sql, "work_schedule");

        $return['status'] = "success";
        $return['data'] = $data;

        return $return;
    }
}