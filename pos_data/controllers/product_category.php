<?php
namespace controller;

use core\ezy;
use lib\input;

class product_category
{
    static function get_product_categories()
    {
        global $CMS;
        $data = \models\product_category::getProductCategories($CMS->input['order'], $CMS->input['by']);
        input::jsonEncode($data);
    }
}