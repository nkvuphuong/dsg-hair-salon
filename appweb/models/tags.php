<?php

namespace models;

use core\ezy;
use lib\date;
use lib\input;
use lib\page;
use models\app;

class tags
{
    /**
     * Get List tag
     * @return array
     */

    static function getData($limit = 10, $module="news")
    {
        global $DB;

        $limit = intval($limit);

        $sql = "SELECT tag_name name, tag_count cnt FROM ".root_table."tags WHERE tag_count != 0 AND tag_module='{$module}' ORDER BY cnt LIMIT 0,{$limit}";

        $sql = $DB->query($sql);

        $data = [];

        while($result = $DB->fetch_assoc($sql))
        {
            $data[] = $result;
        }

        return $data;
    }
}