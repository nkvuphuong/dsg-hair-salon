<?php

namespace models;

use core\ezy;
use lib\input;
use lib\page;

class store
{
    /**
     * @param string $order
     * @param string $by
     * @return array
     */
    static public function getStores($sqlAdd = "", $order = "store_name", $by = "asc")
    {
        global $DB;
        $order = $order ? $order : 'store_name';
        $by = !in_array(strtolower($by), ['asc', 'desc']) ? 'asc' : $by;

        $sql = "SELECT * FROM " . root_table . "store WHERE {$sqlAdd} store_deleted=0 ORDER BY {$order} {$by}";
        return $DB->fetch_data($sql, 'store');
    }

    static public function getStoresJoinCity($sql = "", $orderBy = "city_name ASC, store_name ASC")
    {
        global $DB;
        $orderBy = $orderBy ? $orderBy : 'store_name ASC';

        $sql = "SELECT * FROM " . root_table . "store S LEFT JOIN " . root_table . "city C ON S.city_id = C.city_id WHERE {$sql} store_deleted=0 ORDER BY {$orderBy}";

        return $DB->fetch_data($sql, 'store.city');
    }

    /**
     * @param int $id
     * @return array|null
     */
    static public function getStore($id = 0)
    {
        global $DB;
        if (!$id) return null;

        $sql = "SELECT * FROM " . root_table . "store WHERE store_id='{$id}' LIMIT 1";

        return $DB->fetch_data($sql, 'store')[0] ? $DB->fetch_data($sql, 'store')[0] : null;
    }
}