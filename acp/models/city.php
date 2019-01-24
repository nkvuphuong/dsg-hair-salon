<?php

namespace models;

class city
{
    /**
     * @param string $order
     * @param string $by
     * @return array
     */
    static public function getCitiesHaveStores($order="city_name", $by="asc")
    {
        global $DB;
        $order = $order ? $order : 'city_name';
        $by = !in_array(strtolower($by), ['asc','desc']) ?  'asc' : $by;

        $sql = "SELECT * FROM " . root_table . "city WHERE country_id=238 AND city_id IN (SELECT city_id FROM " . root_table . "store WHERE store_deleted=0) ORDER BY {$order} {$by}";
        return $DB->fetch_data($sql, 'city.store');
    }
}