<?php
namespace controller;

use core\ezy;
use lib\input;

class ward
{
    static function get_wards()
    {
        global $CMS;

        $wards = \models\ward::getWards($CMS->input['district'], $CMS->input['order'], $CMS->input['by']);
        input::jsonEncode($wards);
    }
}