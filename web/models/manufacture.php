<?php

namespace models;

use core\ezy;
use lib\input;
use lib\page;

class manufacture
{
    /**
     * Get List tag
     * @return array
     */
    static public $sqlAdd = "";

    static function getInfo($record_id=0, $field_return="*")
    {
        global $DB, $CMS;

        if($record_id and $field_return)
        {
            // Count field
            $countField = count(explode(",", $field_return));
            // sqladd
            $sql_add = self::$sqlAdd;
            //Query
            $data = $DB->fetch_data("SELECT {$field_return} FROM ".root_table."manufacture WHERE manufacture_deleted=0 AND manufacture_id='{$record_id}' {$sql_add} LIMIT 1",'manufacture')[0];

            //Check return
            if($countField == 1 and $field_return != "*")
            {
                return $data[$field_return];
            }else
            {
                return $data;
            }
        }else
        {
            return false;
        }
    }

    static function createMsg($message="",$status="error")
    {
        if($status == "error")
        {
            $_SESSION['error_msg'] = $message;
            return false;
        }else
        {
            $_SESSION['msg'] = $message;
            return true;
        }
    }

}