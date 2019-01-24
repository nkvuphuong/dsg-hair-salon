<?php

namespace controller;

use core\ezy;
use lib\date;
use lib\input;

ezy::load_model("order", "checkin");

class order
{
    static function check_rating()
    {
        global $CMS;

        $id = input::get('id') * 1;
        $return = \CheckIn\Model\order::checkRating($id);

        input::jsonEncode($return);
    }
}