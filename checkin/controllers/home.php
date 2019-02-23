<?php

namespace CheckIn\Controller;

use CheckIn\Model\user;
use core\ezy;
use lib\input;
use models\city;
use models\order;
use models\store;
use models\rating;

ezy::load_model('user');
ezy::load_model('store', 'booking_data');
ezy::load_model('order');
ezy::load_model('city', 'acp');
ezy::load_model('rating', 'acp');

class home
{
    static function index()
    {
        global $CMS, $tpl;

        $tpl->data = user::getCurrentStaff();
        $tpl->data->store = store::getStore($tpl->data->staff->storeId);
        $tpl->data->cities = city::getCitiesHaveStores();
        $tpl->data->ratings = rating::getAll();
        $stores = store::getStores();

        $tpl->data->cities = array_map(function ($city) use ($stores) {
            $city['stores'] = array_filter($stores, function ($store) use ($city) {
                return $store['cityId'] == $city['city_id'];
            });

            $city['stores'] = array_values($city['stores']);

            return $city;
        }, $tpl->data->cities);

        $tpl->section = \core\ezy::render("rating-form", "home");
        $tpl->section .= \core\ezy::render("welcome-screen", "home");
        $tpl->section .= \core\ezy::render("confirm", "home");
    }

    static function rate()
    {
        global $CMS, $DB;

        input::jsonEncode(\CheckIn\Model\order::rate(input::get('id'), input::get('value'), input::get('ratingID')));
    }
}