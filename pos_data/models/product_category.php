<?php

namespace models;

use core\ezy;
use lib\input;
use lib\page;

class product_category
{
    private static $keys = [
        "product_group_id" => "id",
        "product_group_name" => "name",
        "product_group_type" => "type",
    ];

    static public function getProductCategories($order="id", $by="asc")
    {
        global $CMS, $DB;

        $keys = array_flip(self::$keys);

        $order = $keys[$order] ? $keys[$order] : 'product_group_id';
        $by = !in_array(strtolower($by), ['asc','desc']) ?  'asc' : $by;

        $sql = "SELECT * FROM " . root_table . "product_group WHERE product_group_deleted=0 ORDER BY {$order} {$by}";
        $results = page::init($sql, 0, true, $cache_prefix = 'product_group');

        if ($results) {
            foreach ($results as $key => $result) {
                $result['product_group_id'] *= 1;
                $result['product_group_type'] *= 1;
                $results[$key] = self::convertToDisplay($result);
            }
        }
        return $results;
    }

    /**
     * convert key data for api
     * @param $result
     */
    static public function convertKeys($data = [])
    {
        global $CMS, $DB;

        $return = [];

        $keys = self::$keys;

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
        $data = self::convertKeys($data);

        $oriData = $data;

        $data['oriData'] = $oriData;

        return $data;
    }
}