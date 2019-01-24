<?php

namespace models;

use core\ezy;
use lib\input;
use lib\page;

class price
{
    private static $keys = [
        "price_id" => "id",
        "price_name" => "name",
        "price_stores" => "storeIds",
        "price_cus_groups" => "cusGroupIds"
    ];

    private static $itemKeys = [
        "product_id" => "productId",
        "item_price" => "price"
    ];

    /**
     * @param string $sqlAdd
     * @return mixed
     */
    static function getPrices($sqlAdd = "")
    {
        global $CMS, $DB;

        if(!input::vars('price_book_enabled')) return [];

        $sql = "SELECT * FROM " . root_table . "price WHERE {$sqlAdd} price_status=1 AND price_deleted=0 ORDER BY price_name";

        $rs = $DB->fetch_data($sql, 'price');

        if($rs) {
            foreach ($rs as $key => $data) {

                $items = self::getItems($data['price_id']);
                if($items) {
                    foreach ($items as $key2 => $item) {
                        $item = self::convertKeys($item, self::$itemKeys);

                        if($item) {
                            foreach ($item as $key3 => $value3) {
                                $item[$key3] *= 1;
                            }
                        }

                        $items[$key2] = $item;
                    }
                }

                $data = self::convertToDisplay($data);

                $data['storeIds'] = input::jsonDecode($data['storeIds']);
                $data['storeIds'] = is_array($data['storeIds']) ? array_values($data['storeIds']) : [];
                if($data['storeIds']) {
                    foreach ($data['storeIds'] as $k => $v) {
                        $data['storeIds'][$k] *= 1;
                    }
                }

                $data['cusGroupIds'] = input::jsonDecode($data['cusGroupIds']);
                $data['cusGroupIds'] = is_array($data['cusGroupIds']) ? array_values($data['cusGroupIds']) : [];
                if($data['cusGroupIds']) {
                    foreach ($data['cusGroupIds'] as $k => $v) {
                        $data['cusGroupIds'][$k] *= 1;
                    }
                }

                $data['items'] = $items;
                $rs[$key] = $data;
            }
        }

        return $rs;
    }

    /**
     * @param $priceId
     * @return mixed
     */
    static function getItems($priceId)
    {
        global $CMS, $DB;
        $priceId *= 1;
        $sql = "SELECT * FROM " . root_table . "price_item WHERE price_id=$priceId ORDER BY item_id";

        $items = $DB->fetch_data($sql, 'price_item');

        return $items;
    }

    /**
     * convert key data for api
     * @param $result
     */
    static public function convertKeys($data, $keys)
    {
        global $CMS, $DB;

        $return = [];

        foreach ($data as $key => $value) {
            if (isset($keys[$key])) {
                $return[$keys[$key]] = $value;
            }
        }

        return $return;
    }

    static function convertToDisplay($data = [])
    {
        global $CMS;

        $data = self::convertKeys($data, self::$keys);

        $oriData = $data;

        $data['name'] = html_entity_decode($data['name'], ENT_QUOTES | ENT_XML1, 'UTF-8');

        $data['oriData'] = $oriData;

        return $data;
    }
}