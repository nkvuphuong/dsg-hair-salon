<?php

namespace models;

use core\ezy;
use lib\input;
use lib\page;

class city
{
    /**
     * @var array
     */
    private static $keys = [
        "city_id" => "id",
        "city_name" => "name",
    ];

    /**
     * @param string $order
     * @param string $by
     * @return array
     */
    static public function getCities($order="name", $by="asc")
    {
        $keys = array_flip(self::$keys);

        $order = isset($keys[$order]) ? $keys[$order] : 'city_name';
        $by = !in_array(strtolower($by), ['asc','desc']) ?  'asc' : $by;

        $sql = "SELECT * FROM " . root_table . "city WHERE country_id=238 AND city_id IN (SELECT city_id FROM " . root_table . "store WHERE store_deleted=0) ORDER BY {$order} {$by}";
        $results = page::init($sql, 0, true, 'city.store');

        if ($results) {
            foreach ($results as $key => $result) {
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
    static public function getCity($id = 0)
    {
        global $DB;
        if(!$id) return null;

        $sql = "SELECT * FROM " . root_table . "city WHERE city_id={$id} LIMIT 1";

        if($result = $DB->fetch_data($sql, 'city')[0]) {
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