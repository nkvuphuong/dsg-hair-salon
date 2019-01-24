<?php

namespace models;

use lib\input;
use lib\db;

class addons {
	static public $conf_group_id=0;
    static public function init()
    {
    	global $CMS, $DB;

    	// Check group conf seting title
    	$sql = $DB->query("SELECT conf_id FROM ".root_table."conf_settings_titles WHERE conf_key='group_addons'");
    	if(!$DB->num_rows($sql))
    	{
    		$DB->query("INSERT INTO ".root_table."conf_settings_titles (conf_title, conf_key, conf_protected) VALUES ('Group Addons', 'group_addons', 1)");
    		self::$conf_group_id = $DB->last_insert_id();
    	}else
    	{

    		self::$conf_group_id = $DB->fetch_array($sql)['conf_id'];

    	}

    }


    static function getListAddons()
    {
    	global $CMS, $DB;

    	// Select list add on in config 
    	$conf_group = self::$conf_group_id;
    	$DB->query("SELECT conf_title, conf_key, conf_value FROM ".root_table."conf_settings WHERE conf_group='{$conf_group}'");
    	$output = [];
    	$i = 1;
    	while ($result = $DB->fetch_array()) 
    	{
    		$result['conf_stt'] = $i;
    		$output[] = $result;
    		$i++;
    	}
    	return $output;
    }

    static function updateStatus($conf_key="",$conf_value="")
    {
    	global $CMS, $DB;

    	// xoá cache
		$CMS->class->cache->deletesql("config");

    	// Check exist key
    	if(self::checkExit($conf_key))
    	{
    		$check = $DB->query("UPDATE ".root_table."conf_settings SET conf_value='{$conf_value}' WHERE conf_key='{$conf_key}'");
            if($conf_key == "addon_website_enable")
            {
                $DB->query("UPDATE ".root_table."conf_settings SET conf_value='{$conf_value}' WHERE conf_key='sitestatus'");
            }
    		return $check ? true : false;
    	}else
    	{
    		$conf_group = self::$conf_group_id;
    		$check = $DB->query("INSERT INTO ".root_table."conf_settings (conf_title, conf_key, conf_description, conf_value, conf_lisence, conf_group, conf_expried) VALUES ('{$CMS->lang["title_{$conf_key}"]}', '{$conf_key}', '{$CMS->lang["des_{$conf_key}"]}', '{$conf_value}', 'Free', '{$conf_group}', 0)");
    		return $check ? true : false;
    	}
    }

    static function checkExit($conf_key="")
    {
    	global $CMS, $DB;

    	if($conf_key)
    	{
    		$DB->query("SELECT 0 FROM ".root_table."conf_settings WHERE conf_key='{$conf_key}'");
    		return $DB->num_rows() > 0 ? true : false;
    	}else
    	{
    		return false;
    	}
    }

    static function checkMobile()
    {
        global $CMS;

        $output = [];

        if($_SESSION['is_mobile'] == true)
        {
            $output['large_div'] = "data_table";
            $output['id_table'] = "example";
            $output['class_table'] = " display table ";
            $output['td_mobile'] = 1;//
            $output['script_mobile'] = 1;
        }else
        {
            $output['large_div'] = "table-responsive";
            $output['id_table'] = "tbl_addons";
            $output['class_table'] = "";
            $output['td_mobile'] = 0;//<th width="1%"></th>
            $output['script_mobile'] = 0;
        }

        return $output;
    }
    
}  