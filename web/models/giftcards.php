<?php

namespace models;

use core\ezy;
use lib\date;
use models\application;
use \lib\input;
use lib\page;

class giftcards
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
        $results = $DB->fetch_data("SELECT cat_shorturl AS url, cat_name AS name, cat_id AS id
                    FROM ".root_table."gallery_category 
                    WHERE cat_deleted=0 AND cat_status=1 {$cat_is_hot} ORDER BY cat_name ASC", 'gallery_category');

        $num_rows = count($results);
        $i = 0;
        $output = [];

        if(!$results) return $output;

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

    static public function getListGiftcards_bk($limit = 0)
    {
        global $DB;

        // Limit
        $limit = $limit ? " LIMIT {$limit} " : "";

        // SELECT gallery & gallery_category
        $DB->query("SELECT giftcard_name as name, giftcard_coupon_code as code, giftcard_desc_1 as desc1 , giftcard_desc_2 as desc2, giftcard_desc_3 as desc3, giftcard_image as image 
            FROM ".root_table."giftcard WHERE giftcard_deleted=0 ORDER BY giftcard_id DESC ".$limit);

        $output = [];
        
        // Load data
        while ( $data = $DB->fetch_array() )
        {
            // Convert image
            $data['image'] = input::checkImage("giftcard/{$data['image']}");
            
            // Return data
            $output[] = $data;
        }

        return $output;
    }

    static public function getListGiftcards($limit = 0, $paging=false)
    {
        global $CMS, $DB;

        // Limit
        // $limit = $limit ? " LIMIT {$limit} " : "";

        // Key product group for gift card
        $key = "gift_cards";
        $pg_id = $CMS->product_group->getInfo($key,"product_group_id");
        // SELECT gallery & gallery_category
        $results = page::init("SELECT product_id, product_image, product_name, product_description, product_price_sell FROM ".root_table."product WHERE product_deleted=0 AND product_group='{$pg_id}' AND product_show='1' ORDER BY product_id DESC ", $limit, $paging,'web.product');

        $output = [];
        
        // Load data
        if($results)
        {
            foreach ( $results as $data )
            {

                $data['name'] = $data['product_name'];
                //Check thumb
                $path = "product/{$data['product_image']}";
                $data['uploadPath'] = $path;
                $thumb = \lib\image::getThumb($path, "thumbnail", 'L_', 1, 550);

                // Convert image
                $data['image'] = input::checkImage($thumb);
                $data['link_cart'] = "/cart/addcart/{$data['product_id']}";
                // Return data
                $output[] = $data;
            }
        }


        return $output;
    }

    static function getBarcode($gitem_code="")
    {
        global $CMS, $DB;

        $output = [];
        if($gitem_code)
        {
            $DB->query("SELECT gitem_code, giftcard_code, gitem_amount_remain, gitem_time, gitem_time_update, C.cus_full_name as name, C.cus_email FROM ".root_table."giftcard_items AS G LEFT JOIN ".root_table."customer AS C ON G.cus_id=C.cus_id WHERE gitem_code='{$gitem_code}'");
            $output = $DB->fetch_array();
            $output['amount_remain'] = $CMS->class->input->currency($output['gitem_amount_remain']);
        }

        return $output;
    }

    static function getGitemByOrder($ord_id=0)
    {
        global $CMS, $DB;

        $DB->query("SELECT gitem_code, giftcard_code, gitem_amount_remain FROM ".root_table."giftcard_items WHERE ord_id='{$ord_id}'");
        $output = [];
        while ($result=$DB->fetch_array()) 
        {
            $output[] = $result;
        }

        return $output;
    }
}