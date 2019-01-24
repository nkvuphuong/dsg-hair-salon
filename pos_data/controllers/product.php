<?php
namespace controller;

use core\ezy;
use lib\input;

ezy::load_model("price");

class product
{
    static function get_products()
    {
        global $CMS;

        $products = \models\product::getProducts($CMS->input['order'], $CMS->input['by']);

        input::jsonEncode($products);
    }

    static function get_prices()
    {
        global $CMS;

        $time = time();
        $sqlAdd = " (price_start_time = 0 OR price_start_time <= $time) AND (price_end_time = 0 OR price_end_time + (3600 * 24) > $time) AND";

        $prices = \models\price::getPrices($sqlAdd);

        input::jsonEncode($prices);
    }
}