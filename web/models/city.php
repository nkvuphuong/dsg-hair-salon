<?php

namespace models;

use core\ezy;
use lib\input;


class city
{
    /**
     * Get List contact
     * @return array
     */

    static public function getInfo($record_id)
    {
        global $DB, $CMS;

        if(!$record_id) return false;

        $sql = "SELECT * FROM ".root_table."city WHERE city_id='{$record_id}'";

        $data = $DB->fetch_data($sql, 'city')[0];

        return $data;
    }

     
}