<?php
namespace controller;

use core\ezy;
use lib\input;

class shift_work
{
    static function get_shift_works()
    {
        global $CMS;

        $stores = \models\shift_work::getShiftWorks(input::get('order'), input::get('by'));
        input::jsonEncode($stores);
    }
}