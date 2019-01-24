<?php
namespace controller;

use core\ezy;
use lib\input;

class store
{
    static function get_stores()
    {
        global $CMS;

        $stores = \models\store::getStores("", input::get('order'), input::get('by'));
        input::jsonEncode($stores);
    }
}