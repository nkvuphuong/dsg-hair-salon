<?php
namespace controller;

use core\ezy;
use lib\input;

class product
{
    static function get_products()
    {
        global $CMS;

        $products = \models\product::getProducts($CMS->input['order'], $CMS->input['by']);

        input::jsonEncode($products);
    }
}