<?php

namespace models;

use core\ezy;
use lib\input;


class district
{
    static public $cache_prefix = 'district';

    /**
     * Get List contact
     * @return array
     */

    static public function getInfo($record_id)
    {
         
        global $DB, $CMS;

        if(!$record_id) return false;

        $sql = "SELECT * FROM ".root_table."district WHERE district_id='{$record_id}' LIMIT 1";

        $data = $DB->fetch_data($sql, self::$cache_prefix)[0];

        return $data;
    }

     
}