<?php

namespace models;

use core\ezy;
use lib\date;
use lib\input;
use lib\page;

class shift_work
{
    /**
     * @var array
     */
    private static $keys = [
        "shift_work_id" => "id",
        "shift_work_name" => "name",
        "shift_work_in" => "in",
        "shift_work_out" => "out"
    ];

    /**
     * @param string $order
     * @param string $by
     * @return array
     */
    static public function getShiftWorks($order = "name", $by = "asc")
    {
        $keys = array_flip(self::$keys);

        $order = isset($keys[$order]) ? $keys[$order] : 'shift_work_id';
        $by = !in_array(strtolower($by), ['asc', 'desc']) ? 'asc' : $by;

        $sql = "SELECT * FROM " . root_table . "shift_work WHERE shift_work_deleted=0 ORDER BY {$order} {$by}";
        $results = page::init($sql, 0, true, 'shift_work');

        if ($results) {
            foreach ($results as $key => $result) {
                $result['shift_work_id'] *= 1;
                $result['shift_work_in'] *= 1;
                $result['shift_work_out'] *= 1;
                $results[$key] = self::convertToDisplay($result);
            }
        }
        return $results;
    }

    /**
     * @param int $id
     * @return array|null
     */
    static public function getShiftWork($id = 0)
    {
        global $DB;
        if (!$id) return null;

        $sql = "SELECT * FROM " . root_table . "store WHERE shift_work_deleted=0 AND shift_work_id={$id} LIMIT 1";

        if ($result = $DB->fetch_data($sql, 'shift_work')[0]) {
            return self::convertToDisplay($result);
        }

        return null;
    }

    /**
     * convert key data for api
     * @param $result
     */
    static public function convertKeys($data = [])
    {
        $return = [];

        $keys = self::$keys;

        foreach ($data as $key => $value) {
            if (isset($keys[$key])) {
                $return[$keys[$key]] = $value;
            }
        }

        return $return;
    }

    /**
     * @param array $data
     * @return array
     */
    static function convertToDisplay($data = [])
    {
        $data = self::convertKeys($data);
        $oriData = $data;
        $data['oriData'] = $oriData;

        return $data;
    }

    static function getShiftWorkSteps()
    {
        global $CMS;
        $rs = [];

        $data = self::getShiftWorks();
        $step = $CMS->vars['step_time_booking'] * 60;

        foreach ($data as $item)
        {
            $in = $item['in']*1;
            $out = $item['out'] == 0 ? 24 * 3600 : $item['out'];

            for ($s = $in; $s < $out; $s = $s = $s + $step)
            {
                $rs[$item['id']][] = date::sec2Hour($s);
            }
        }

        return $rs;
    }
}