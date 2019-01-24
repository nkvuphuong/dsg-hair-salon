<?php

namespace controller;

use core\ezy;
use lib\input;

class order
{
    /**
     * Add order
     */
    static function add()
    {
        global $CMS;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = file_get_contents("php://input");
            $data = input::jsonDecode($data);
            $results = \models\order::add($data);
            $results['payload'] = $data;
            input::jsonEncode($results);
        }
    }

    /**
     * Get booked hours from orders
     */
    static function get_booked_hours()
    {
        global $CMS;
        $now = time();
        $results = \models\order::getBookedHours(input::get('store') * 1, "ordi_booking_time > $now AND");
        input::jsonEncode($results);
    }

    /**
     * Cancel order
     */
    static function cancel()
    {
        global $CMS;

        $orderId = input::get('order');
        $phone = input::get('phone');

        $result = \models\order::cancel($phone, $orderId);
        input::jsonEncode($result);
    }

    /**
     * Get orders by phone
     */
    static function get_orders()
    {
        global $CMS;
        $sql_add = "";

        if($phone = input::get('phone')) {
            $sql_add .= " ord_booking_phone = '{$phone}' AND ";
        }

        $result = \models\order::getAll($sql_add);
        input::jsonEncode($result);
    }

    /**
     * Rate order
     */
    static function rate()
    {
        $ord_id = input::get('order') * 1;
        $rate = input::get('rate') * 1;

        $rs = \models\order::rate($ord_id, $rate);

        input::jsonEncode($rs);
    }
}