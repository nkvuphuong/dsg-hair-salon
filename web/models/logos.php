<?php
/**
 * Created by PhpStorm.
 * User: Phuong3F
 * Date: 5/23/2017
 * Time: 9:44 AM
 */

namespace models;

use lib\date;
use lib\input;
use lib\db;
use \lib\template;

class logos
{
    /**
     * The positions of logos
     * pos_mode: session|always
     * + session: hide/show by interval
     * + always: always show
     * pos_interval: time to check show/hide by session (minutes)
     * pos_type: random|all
     * + random: show random a record
     * + all: show all record
     * @var array
     * @access public
     * @static
     * */
    static public $positions = [];

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
     * get all position
     * @param string $sql_add
     * @return array
     */
    static function getAllPosition($sql_add = "")
    {
        global $CMS, $DB;

        $sql = "SELECT * FROM ".root_table."logo_positions WHERE {$sql_add} pos_deleted=0 ORDER BY pos_name";

        $results = $DB->fetch_data($sql,'logo_positions');

        $data = [];

        if(!$results) return $data;

        foreach ($results as $re)
        {
            $data[$re['pos_id']] = $re;
        }

        return $data;
    }

    /**
     * @param $id
     * @param string $sql_add
     * @return array
     */
    static function getPosition($key, $sql_add = "")
    {
        global $CMS, $DB;

        $key = trim($key);

        if(!$key) return false;

        $sql_add .= " pos_key='{$key}' AND ";

        $sql = "SELECT * FROM ".root_table."logo_positions WHERE {$sql_add} pos_deleted=0 ORDER BY pos_name LIMIT 1";

        $data = $DB->fetch_data($sql,'logo_positions');

        return isset($data[0]) ? $data[0] : null;
    }

    /**
     * Get data to show by key of position
     * @param string $key
     * @return array|bool
     */
    static function getData()
    {
        global $CMS, $DB;

        $positions = self::getAllPosition();

        $data = [];

        foreach ($positions as $position)
        {
            $data[$position['pos_key']] = self::getDataByKey($position['pos_key'], $position);
        }

        return $data;
    }

    /**
     * @param string $key
     * @return array|bool
     */
    static function getDataByKey($key = "", $position = [])
    {
        global $CMS, $DB;

        if(empty($position) || !is_array($position))
        {
            if (!($position = self::getPosition($key))) {
                return false;
            }
        }

        if($position['pos_mode'] == 'session')
        {
            if (isset($_SESSION['popup_'.$key]) && $_SESSION['popup_'.$key] > time()) {
                return false;
            }

            $_SESSION['popup_'.$key] = time() + (intval($position['pos_interval']) * 60);
        }

        $time = $CMS->class->date->date2time(date::format(time()));

        if($position['pos_type'] == 'random')
        {
            $sql_order = "ORDER BY RAND() LIMIT 1";
        }
        else
        {
            $sql_order = "ORDER BY logo_desc";
        }


        $sql = "SELECT logo_name, logo_desc, logo_src, logo_link, logo_src_alt FROM " . root_table . "logos WHERE logo_deleted=0 AND logo_status=1 AND logo_position='{$position['pos_id']}' AND (logo_start_time=0 OR logo_start_time<={$time}) AND (logo_end_time=0 OR logo_end_time+(3600*24)>{$time}) {$sql_order}";

        $results = $DB->fetch_data($sql,'logos');

        $data = [];

        if($results)
        {
            $i=1;
            foreach ($results as $result)
            {
                $logo_src = "{$CMS->vars['upload_dir']}/{$result['logo_src']}";

                if (is_file($logo_src) && file_exists($logo_src)) {
                    $result['logoOri'] = "{$result['logo_src']}";
                    $result['logo_src'] = "{$CMS->vars['upload_url']}/{$result['logo_src']}";
                    $result['logo_link'] = $result['logo_link'] ? $result['logo_link'] : "/";
                    $result['inc'] = $i;
                    $data[] = $result;
                    $i++;
                }
            }
        }
        else
        {
            return [];
        }

        return $data;
    }
}