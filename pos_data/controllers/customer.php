<?php
namespace controller;

use core\ezy;
use lib\image;
use lib\input;

class customer
{
    static function get_customers()
    {
        global $CMS;
        $staffs = \models\customer::getCustomers(input::get('order'), input::get('by'));
        input::jsonEncode($staffs);
    }

    static function add_customer()
    {
        global $CMS;

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = $_POST;
            $data = \models\customer::addValue($data);
            $result = \models\customer::add($data);
            input::jsonEncode($result);
        }
    }

    static function update_customer()
    {
        global $CMS;

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = $_POST;
            $data = \models\customer::editValue($data);
            $result = \models\customer::edit($data);
            input::jsonEncode($result);
        }
    }
}