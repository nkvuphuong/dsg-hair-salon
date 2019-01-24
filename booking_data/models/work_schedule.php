<?php

namespace models;

use core\ezy;
use lib\date;
use lib\input;
use lib\page;

ezy::load_model("shift_work");
ezy::load_model("staff");

class work_schedule
{
    /**
     * @var array
     */
    private static $keys = [
        "work_schedule_id" => "id",
        "shift_work_id" => "swId",
        "staff_id" => "staffId",
        "store_id" => "storeId",
        "work_schedule_date" => "date",
        "user_booking_slots" => "maxSlots"
    ];

    /**
     * @param string $order
     * @param string $by
     * @return array
     */
    static public function getWorkSchedules($sql_add = "", $order = "name", $by = "asc")
    {
        $keys = array_flip(self::$keys);

        $order = input::arrayValue($keys, $order, 'work_schedule_id');
        $by = !in_array(strtolower($by), ['asc', 'desc']) ? 'asc' : $by;

        $sql = "SELECT WS.*, U.user_booking_slots FROM " . root_table . "work_schedule WS LEFT JOIN " . root_table .  "user U ON staff_id = U.user_id  WHERE {$sql_add} 1=1  ORDER BY {$order} {$by}";

        $results = page::init($sql, 0, true, 'work_schedule.user');

        $swSteps = shift_work::getShiftWorkSteps();

        if ($results) {
            foreach ($results as $key => $result) {
                foreach($result as $k => $v) {
                    $result[$k] *= 1;
                }

                $staff = staff::getStaff($result['staff_id']);

                $results[$key] = self::convertToDisplay($result);
                $steps = isset($swSteps[$result['shift_work_id']]) ? $swSteps[$result['shift_work_id']] : [];

                foreach($steps as $kStep => $step) {
                    $wsTime =  $result['work_schedule_date'] + date::hour2Sec($step);

                    //Loại bỏ những khung h nhân viên bận/ẩn
                    if($staff['user_busy_from'] && $staff['user_busy_to']) {
                        if($wsTime >= $staff['user_busy_from'] && $wsTime <= $staff['user_busy_to']) {
                            unset($steps[$kStep]);
                        }
                    } else if ($staff['user_busy_from']) {
                        if($wsTime >= $staff['user_busy_from']) {
                            unset($steps[$kStep]);
                        }
                    } else if ($staff['user_busy_to']) {
                        if($wsTime <= $staff['user_busy_to']) {
                            unset($steps[$kStep]);
                        }
                    }
                }

                $results[$key]['swSteps'] = array_values($steps);
            }
        }

        return $results;
    }

    /**
     * @param int $id
     * @return array|null
     */
    static public function getWorkSchedule($id = 0)
    {
        global $DB;
        if (!$id) return null;

        $sql = "SELECT * FROM " . root_table . "store WHERE work_schedule_id={$id} LIMIT 1";

        if ($result = $DB->fetch_data($sql, 'work_schedule')[0]) {
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

        $data['date'] = date('Y-m-d', $data['date']);

        $oriData = $data;
        $data['oriData'] = $oriData;

        return $data;
    }
}