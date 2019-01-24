<?php
namespace controller;

use core\ezy;
use lib\input;

class price
{
    static function get_prices()
    {
        global $CMS;
        $storeId = input::get("store_id");
        $sqlAdd = "(price_stores IS NULL OR price_stores='' OR price_stores LIKE '%:\"{$storeId}\"%') AND"; //Get by store

        //Get by customer group
        ezy::load_model("customer", "pos_data");
        $phoneNumber = input::get('phone');
        $customer = \models\customer::getInfoByPhone($phoneNumber);

        $addCons = ["price_cus_groups IS NULL", "price_cus_groups=''"] ;

        if($customer) {
            ezy::load_model("district", "pos_data");
            $customer = \models\customer::convertToDisplay($customer);
            if($customer['groupIds']) {
               foreach ($customer['groupIds'] as $cusGroupId) {
                   $cusGroupId *= 1;
                   if($cusGroupId) {
                       $addCons[] = "price_cus_groups LIKE '%:\"{$cusGroupId}\"%' ";
                   }
               }
            }
        }

        $sqlAdd .= " (" . implode(" OR ", $addCons) . ") AND";

        $time = time();
        $sqlAdd .= " (price_start_time = 0 OR price_start_time <= $time) AND (price_end_time = 0 OR price_end_time + (3600 * 24) > $time) AND";

        $prices = \models\price::getPrices($sqlAdd);
        input::jsonEncode($prices);
    }
}