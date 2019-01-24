<?php

namespace models;

use core\ezy;
use lib\date;
use models\application;
use \lib\input;

class coupons
{
    /**
     * Get List Category
     * @return array
     */

    static public function getListCategory($cat_is_hot = 0)
    {
        global $DB;

        // Get hot category
        $cat_is_hot = $cat_is_hot ? "AND cat_is_hot='{$cat_is_hot}'" : "";

        // Select categories
        $sql = "SELECT cat_shorturl AS url, cat_name AS name, cat_id AS id
                    FROM ".root_table."gallery_category 
                    WHERE cat_deleted=0 AND cat_status=1 {$cat_is_hot} ORDER BY cat_name ASC";

        $results = $DB->fetch_data($sql,'gallery_category');

        $num_rows = count($results);
        $i = 0;
        $output = [];

        // Load Data
        foreach ( $results as $data )
        {
            // Define last record
            $data["last"] = $i == $num_rows-1 ? true : false;

            // Return Data
            $output[] = $data;

            $i++;
        }

        return $output;
    }

    /**
     * Get List Gallery
     * @return array
     */

    static public function getListCoupons($limit = 0)
    {
        global $CMS, $DB;

        // Limit
        $limit = $limit ? " LIMIT {$limit} " : "";
        
        // SELECT gallery & gallery_category
        $sql = "SELECT coupon_name as name, coupon_coupon_code as code, coupon_desc_1 as desc1 , coupon_desc_2 as desc2, coupon_desc_3 as desc3, coupon_image as image, coupon_end_date 
            FROM ".root_table."coupon WHERE coupon_deleted=0 AND coupon_status=1 ORDER BY coupon_id DESC ".$limit;



        $output = [];

        if($results = $DB->fetch_data($sql, 'coupon'))
        {
            // Check end date
            $end_date = time();
            foreach ( $results as $data )
            {
                if($data['coupon_end_date'] != 0 and $end_date > $data['coupon_end_date'] + 24*3600) { continue;}
                // Convert image
                $path = "coupon/{$data['image']}";
                $data['uploadPath'] = $path;
                $thumb = \lib\image::getThumb($path, $CMS->coupons->thumb_folder, 'L_', 1, $CMS->coupons->thumb_size['L']);
                $data['imageThumb'] = input::checkImage($thumb);
                $data['image'] = input::checkImage($path);

                // Return data
                $output[] = $data;
            }
        }
        // Load data

        return $output;
    }
}