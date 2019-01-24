<?php
namespace controller;

use core\ezy;
use lib\input;

class city
{
    static function get_cities()
    {
        global $CMS;

        $cities = \models\city::getCities(input::get("order"), input::get("by"));
        input::jsonEncode($cities);
    }
}