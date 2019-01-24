<?php
namespace controller;

use core\ezy;
use lib\input;

class district
{
    static function get_districts()
    {
        global $CMS;

        $districts = \models\district::getDistricts(input::get('city'), input::get('order'), input::get('by'));
        input::jsonEncode($districts);
    }
}