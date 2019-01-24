<?php

namespace models;

use core\ezy;
use lib\input;

class attribute
{
    
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
            $data = $DB->fetch_data("SELECT {$field_return} FROM ".root_table."attribute WHERE attr_deleted=0 AND attr_id='{$record_id}' {$sql_add} LIMIT 1",'attribute')[0];

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


    static function getInfoOption($record_id=0, $field_return="*")
    {
        global $DB, $CMS;

        if($record_id and $field_return)
        {
            // Count field
            $countField = count(explode(",", $field_return));
            // sqladd
            $sql_add = self::$sqlAdd;
            //Query
            $data = $DB->fetch_data("SELECT {$field_return} FROM ".root_table."attribute_options WHERE options_deleted=0 AND options_id='{$record_id}' {$sql_add} LIMIT 1",'attribute_options')[0];

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


    static function getAttrByKey($record_id=0, $field_return="*")
    {
        global $DB, $CMS;

        if($record_id and $field_return)
        {
            // Count field
            $countField = count(explode(",", $field_return));
            // sqladd
            $sql_add = self::$sqlAdd;
            //Query
            $data = $DB->fetch_data("SELECT {$field_return} FROM ".root_table."attribute WHERE attr_deleted=0 AND attr_key='{$record_id}' {$sql_add} LIMIT 1",'attribute')[0];

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

    static function getListOption($attr_id=0)
    {
        global $CMS, $DB;

        $sql = "SELECT * FROM ".root_table."attribute_options WHERE options_deleted=0 AND attr_id='{$attr_id}' ORDER BY options_id DESC";
        $result = array();
        $result = $DB->fetch_data($sql, "attribute");
        return $result;
    }

}