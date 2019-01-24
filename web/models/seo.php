<?php
/**
 * Created by PhpStorm.
 * User: Phuong3F
 * Date: 5/23/2017
 * Time: 9:44 AM
 */

namespace models;

use core\ezy;
use lib\date;
use lib\input;
use lib\db;
use \lib\template;

class seo
{
    static function loadSEO()
    {
        global $CMS, $DB;

        $url = self::clearUrl($CMS->vars['request_url']);

        // Normal SQL
        $sql =  "SELECT seo_title , seo_keywords, seo_description, seo_og_title, seo_og_description, seo_og_image, seo_dc_title, seo_dc_subject, seo_h1_content FROM ".root_table."seo WHERE seo_url='{$url}' AND seo_deleted=0 ORDER BY seo_id LIMIT 1";
        $data = $DB->fetch_data($sql,'seo');

        return isset($data[0]) ? $data[0] : null;
    }

    /**
     * clear domain, paging param
     * @param string $url
     * @return string
     */
    static public function clearUrl($url)
    {
        global $CMS;

        //remove domain
        $url = str_replace($CMS->vars['root_domain'], '', $url);

        //remove paging param
        $url = preg_replace('/(\/page-[0-9]+)/','',$url);

        return $url;
    }
}