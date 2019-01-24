<?php
namespace controller;

use core\ezy;
use lib\input;

class staff
{
    static function get_staffs()
    {
        global $CMS;

        $staffs = \models\staff::getStaffs(input::get('order'), input::get('by'));
        input::jsonEncode($staffs);
    }

    static function login() {
        global $CMS;
        $username = urldecode(input::get('username'));
        $password = urldecode(input::arrayValue($_GET, 'password'));
        $result = \models\staff::login($username, $password);
        input::jsonEncode($result);
    }
}