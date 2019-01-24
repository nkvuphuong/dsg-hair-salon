<?php
namespace controller;

use core\ezy;
use lib\date;
use lib\input;

ezy::load_model("shift_work");
ezy::load_model("order");

class work_schedule
{
    static function get_work_schedules()
    {
        global $CMS;

        $store_id = input::get('store') * 1;
        $from = strtotime(date('Y-m-d'));
        $rs = \models\work_schedule::getWorkSchedules("WS.store_id = {$store_id} AND work_schedule_date >=$from AND", 'date', 'asc');
        input::jsonEncode($rs);
    }

    /**
     * Get booking hours by date and store
     */
    static function get_booking_hours()
    {
        global $CMS;

        $store_id = input::get('store',0) * 1;
        $date = strtotime(input::get('date',date('Y-m-d')));
        $work_schedules = \models\work_schedule::getWorkSchedules("WS.store_id = {$store_id} AND work_schedule_date =$date AND", 'date', 'asc');

        //Reformat to display
        $hours = [];
        foreach ($work_schedules as $work_schedule) {
            foreach ($work_schedule['swSteps'] as $step) {
                $hours[$step]['booked'] = isset($hours[$step]['booked']) ? $hours[$step]['booked'] : [];
                $hours[$step]['bookedOthers'] = [];
                $hours[$step]['slots'] = isset($hours[$step]['slots']) ? $hours[$step]['slots'] : [];
                $hours[$step]['hour'] = $step;
                $hours[$step]['second'] = date::hour2Sec($step);
                if(!in_array($work_schedule['staffId'], $hours[$step]['slots'])) {
                    $maxSlots = $work_schedule['maxSlots'] >= 1 ? $work_schedule['maxSlots'] : 1;
                    for ($i = 1; $i <= $maxSlots; $i++) {
                        $hours[$step]['slots'][] = $work_schedule['staffId'];
                    }
                }
            }
        }

        //Booked hours
        $bookedHours = \models\order::getBookedHours(input::get('store')*1, "ordi_booking_time >= {$date} AND ordi_booking_time < {$date}+(3600*24) AND");

        if($bookedHours) {
            foreach ($bookedHours as $bookedHour) {
                $hours[$bookedHour['hour']]['booked'] = isset($hours[$bookedHour['hour']]['booked']) ? $hours[$bookedHour['hour']]['booked'] : [];
                $hours[$bookedHour['hour']]['booked'][] =  $bookedHour['staff']['id'];
            }
        }

        foreach ($hours as $k => $hour) {
            $hour['booked'] = $hour['booked'] ? $hour['booked'] : [];
            $hour['bookedOthers'] = $hour['bookedOthers'] ? $hour['bookedOthers'] : [];
            $hour['slots'] = $hour['slots'] ? $hour['slots'] : [];
            $hour['hour'] = $k;
            $hour['second'] = date::hour2Sec($k);
            $hours[$k] = $hour;
        }

        input::jsonEncode(array_values($hours));
    }
}