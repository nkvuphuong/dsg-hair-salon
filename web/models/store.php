<?php
/**
 * Created by PhpStorm.
 * User: Phuong3F
 * Date: 5/30/2017
 * Time: 8:27 AM
 */

namespace models;


class store
{
    /**
     * Get all stories
     * @return array
     */
    static function getAll()
    {
        global $CMS, $DB;

        $sql = "SELECT store_id, store_name, store_phone, store_address FROM ".root_table."store WHERE store_deleted=0 ORDER BY store_name";

        $results = $DB->fetch_data($sql,'store');

        return $results;
    }

    /**
     * Get all stories
     * @return array
     */
    static function getAll_by_City($city_id = "" )
    {
        global $CMS, $DB;

        $sql = "SELECT store_id, store_name, store_phone, store_address FROM ".root_table."store WHERE store_deleted=0 AND city_id = '{$city_id}' ORDER BY store_name";

        $results = $DB->fetch_data($sql,'store');

        return $results;
    }

    /**
     * Get information store
     * @param $id
     * @return array|boolean
     */
    static function getInfo($id)
    {
        global $CMS, $DB;

        $id = intval($id);

        if(!$id) return false;

        $sql = "SELECT * FROM ".root_table."store WHERE store_deleted=0 AND store_id='{$id}'";

        return $DB->fetch_data($sql,'store')[0];
    }


     /**
     * Get Staff DSG
     * @return array
     */

    static public function get_staff_dsg()
    {
        global $DB;

        $sql = $DB->query("SELECT userg_id, userg_title, userg_prefix_html FROM ".root_table."user_group  WHERE  ( userg_prefix_html = '[little_experience]' OR    userg_prefix_html = '[seniority]' ) AND userg_deleted=0");
        $data_output = array();
        $data_ug = array();
        if($DB->num_rows($sql) > 0)
        {
            while($ug = $DB->fetch_array($sql))
            {
                $userg_id = $ug['userg_id'];
                if($ug['userg_prefix_html'] == '[seniority]' )
                {
                    $ug['seniority'] = 1;
                }
                else {   $ug['seniority'] = 0; }
                $sql_2 = $DB->query("SELECT user_id, user_name, user_display_name  FROM ".root_table."user  WHERE userg_id ='{$userg_id}'  AND user_deleted=0");
                if($DB->num_rows($sql_2) > 0)
                {
                    $us_data = array();
                    while($us = $DB->fetch_array($sql_2))
                    {
                        $us_data[] = $us;
                        
                    }
                    $ug['user'] = $us_data;
                }
                $data_output[] = $ug;
            }
        }

        return $data_output;
    }
}