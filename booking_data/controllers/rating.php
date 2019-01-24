<?php
namespace controller;

use lib\input;

class rating
{
    static function get_ratings()
    {
        global $CMS;

        $cities = \bookingData\models\ratingModel::getRatings(input::get("order"), input::get("by"));

        input::jsonEncode($cities);
    }
}