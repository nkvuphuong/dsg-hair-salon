<?php

namespace models;

use core\ezy;
use lib\input;
use lib\page;

class ward
{
    private static $keys = [
        "town_id" => "id",
        "town_name" => "name",
    ];

    static public function getWards($district_id=0, $order="name", $by="asc")
    {
        $district_id *= 1;
        if(!$district_id) return false;

        $keys = array_flip(self::$keys);

        $order = $keys[$order] ? $keys[$order] : 'town_name';
        $by = !in_array(strtolower($by), ['asc','desc']) ?  'asc' : $by;

        $sql = "SELECT * FROM " . root_table . "town WHERE district_id={$district_id} ORDER BY {$order} {$by}";
        $results = page::init($sql, 0, true, $cache_prefix = 'ward');

        if ($results) {
            foreach ($results as $key => $result) {
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
        $data = self::convertKeys($data);

        $oriData = $data;

        $data['oriData'] = $oriData;

        return $data;
    }
}