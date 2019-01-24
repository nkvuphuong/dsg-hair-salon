<?php

namespace models;

use core\ezy;
use lib\input;
use lib\page;

class district
{
    /**
     * @var array
     */
    private static $keys = [
        "district_id" => "id",
        "district_name" => "name",
        "city_id" => "cityId",
    ];

    /**
     * @param int $city_id
     * @param string $order
     * @param string $by
     * @return array|bool
     */
    static public function getDistricts($city_id=0, $order="name", $by="asc")
    {
        $city_id *= 1;
        if(!$city_id) return false;

        $keys = array_flip(self::$keys);

        $order = input::arrayValue($keys, $order, 'district_name');
        $by = !in_array(strtolower($by), ['asc','desc']) ?  'asc' : $by;

        $sql = "SELECT * FROM " . root_table . "district WHERE city_id={$city_id} AND district_deleted=0 ORDER BY {$order} {$by}";
        $results = page::init($sql, 0, true, $cache_prefix = 'district');

        if ($results) {
            foreach ($results as $key => $result) {
                $result['district_id'] *= 1;
                $result['city_id'] *= 1;
                $results[$key] = self::convertToDisplay($result);
            }
        }
        return $results;
    }

    /**
     * @param int $id
     * @return array|null
     */
    static public function getDistrict($id = 0)
    {
        global $DB;
        if(!$id) return null;

        $sql = "SELECT * FROM " . root_table . "district WHERE district_id={$id} LIMIT 1";

        if($result = $DB->fetch_data($sql, 'district')[0]) {
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

        foreach ($data as $key => $value) {
            if (isset($keys[$key])) {
                $return[$keys[$key]] = $value;
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
        $data['id'] *= 1;

        $oriData = $data;

        $data['oriData'] = $oriData;

        return $data;
    }
}