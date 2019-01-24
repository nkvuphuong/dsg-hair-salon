<?php

namespace models;

use core\ezy;
use lib\input;
use lib\page;

class store
{
    /**
     * @var array
     */
    private static $keys = [
        "store_id" => "id",
        "store_name" => "name",
        "store_address" => "address",
        "store_phone" => "phone",
        "googlemap_code" => "map",
        "city_id" => "cityId",
        "price_id" => "priceId",
    ];

    /**
     * @param string $order
     * @param string $by
     * @return array
     */
    static public function getStores($sql = "", $order = "name", $by = "asc")
    {
        $keys = array_flip(self::$keys);

        $order = isset($keys[$order]) ? $keys[$order] : 'store_name';
        $by = !in_array(strtolower($by), ['asc', 'desc']) ? 'asc' : $by;

        $sql = "SELECT * FROM " . root_table . "store WHERE {$sql} store_deleted=0 AND store_display=1 ORDER BY {$order} {$by}";
        $results = page::init($sql, 0, true, 'store');

        if ($results) {
            foreach ($results as $key => $result) {
                $result['store_id'] *= 1;
                $result['city_id'] *= 1;
                $result['price_id'] *= 1;
                $results[$key] = self::convertToDisplay($result);
            }
        }
        return $results;
    }

    /**
     * @param int $id
     * @return array|null
     */
    static public function getStore($id = 0)
    {
        global $DB;
        if (!$id) return null;

        $sql = "SELECT * FROM " . root_table . "store WHERE store_id={$id} LIMIT 1";

        if ($result = $DB->fetch_data($sql, 'store')[0]) {
            return self::convertToDisplay($result);
        }

        return null;
    }

    /**
     * convert key data for api
     * @param $result
     */
    static public function convertKeys($data = [])
    {
        $return = [];

        $keys = self::$keys;

        if($data && is_array($data)) {
            foreach ($data as $key => $value) {
                if (isset($keys[$key])) {
                    $return[$keys[$key]] = $value;
                }
            }
        }

        return $return;
    }

    /**
     * @param array $data
     * @return array
     */
    static function convertToDisplay($data = [])
    {
        $data = self::convertKeys($data);
        $oriData = $data;
        $data['oriData'] = $oriData;

        return $data;
    }
}