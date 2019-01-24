<?php

namespace bookingData\models;

use lib\page;

class ratingModel
{
    /**
     * @var array
     */
    private static $keys = [
        "rating_id" => "id",
        "rating_name" => "name",
        "rating_label" => "label",
        "rating_value" => "value",
    ];

    /**
     * @param string $order
     * @param string $by
     * @return array
     */
    static public function getRatings($order="name", $by="asc")
    {
        $keys = array_flip(self::$keys);
        $order = isset($keys[$order]) ? $keys[$order] : 'rating_name';
        $by = !in_array(strtolower($by), ['asc','desc']) ?  'asc' : $by;

        $sql = "SELECT * FROM " . root_table . "rating WHERE rating_deleted=0 AND rating_status=1 ORDER BY {$order} {$by}";
        $results = page::init($sql, 0, true, 'rating');

        if ($results) {
            foreach ($results as $key => $result) {
                $result['rating_id'] *= 1;
                $result['rating_value'] *= 1;
                $results[$key] = self::convertToDisplay($result);
            }
        }
        return $results;
    }

    /**
     * @param int $id
     * @return array|null
     */
    static public function getRating($id = 0)
    {
        global $DB;
        if(!$id) return null;

        $sql = "SELECT * FROM " . root_table . "rating WHERE rating_id={$id} AND rating_deleted=0 LIMIT 1";

        if($result = $DB->fetch_data($sql, 'city')[0]) {
            return self::convertToDisplay($result);
        }

        return null;
    }

    /**
     * @param array $data
     * @return array
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

        $data['label'] = htmlspecialchars_decode($oriData['label']);
        $data['label'] = html_entity_decode($data['label'], ENT_QUOTES | ENT_XML1, 'UTF-8');

        return $data;
    }
}