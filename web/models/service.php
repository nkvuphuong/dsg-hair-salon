<?php

namespace models;

use core\ezy;
use lib\input;
use lib\page;

class service
{
    /**
     * Get List service
     * @return array
     */

    static public function getListCategory($limit = 0, $parent = 0, $pg_type=1)
    {
        global $DB, $CMS;

        // Limit
        $limit = $limit > 0 ? "LIMIT {$limit}" : "";

        // Parent Mac dinh chi load product_group_parent=0
        $parent = $parent == 1 ? " AND product_group_avatar!='' " : "";

        // Key product group for gift card
        $key = "gift_cards";
        $pg_id = $CMS->product_group->getInfo($key,"product_group_id");

 
        // Select categories
        $results = $DB->fetch_data("SELECT product_group_name AS name, product_group_id AS id, product_group_description as description, product_group_avatar as image, pg_shorturl AS url
                    FROM ".root_table."product_group 
                    WHERE product_group_deleted=0 AND product_group_status=1 AND product_group_type='{$pg_type}' AND product_group_parent=0 {$parent} AND product_group_id != '{$pg_id}' ORDER BY product_group_order ASC {$limit}", 'product_group');
 
        $i = 1;
        $output = [];

        if(!$results) return $output;

        // Load Data
        foreach ( $results as $data )
        {
            $data = self::convertCategory($data);
            $data['active'] = $i == 1 ? true: false;
            $data['image_bk'] = $data['image'];
            $path = "product/".$data['image'];
            $data['pathUpload'] = $path;
            $thumb = \lib\image::getThumb($path, "thumbnail", 'S_', 1, 150);
            $data['imageThumb'] = input::checkImage($thumb);
            $data['image'] = input::checkImage($path);
            $data['imageAvailable'] = ($data['image'] == 'custom/no-photo.png') ? false : true;
            
            $data['list_child'] = self::getListChild($data['id'], $pg_type);
            $data['list_service'] = self::getListServiceByCat($data['id'], $pg_type);
            $data['count_service'] = count($data['list_service']);
            $data['group_key'] = $CMS->class->seo->cleanurl($data['name']);
            // Return Data
            $output[] = $data;

            $i++;
        }
        
        return $output;
    }

    /**
     * Get List product
     * note: product_option: 0: all, 1:Featured products, 4: Favorite products, 5: New Product
     * @return array
     */

    static public function getListService($pg_id=0, $group="", $limit="", $paging = false, $resizeWidth=0, $product_type=1, $product_option = 0)
    {
        global $DB, $CMS;

        // check list child-cat
        if($pg_id > 0 )
        {
           $list_id = self::getListProductGroupChild($pg_id);
        }

        $clause = $pg_id>0 ? " AND product_group IN ({$list_id}) " : "";
        $group_by = $group ? " GROUP BY {$group} " : "";
        $climit = $limit ? " LIMIT {$limit} " : "";
        // Key product group for gift card
        $key = "gift_cards";
        $product_group_id = $CMS->product_group->getInfo($key,"product_group_id");

        $clause .= " AND product_group !='{$product_group_id}' ";

        // product option
        $clause .= $product_option > 0 ? " AND ( product_option LIKE '%,{$product_option},%' OR product_option = '{$product_option}') " : '';

        // Set number pagination
        $number_page = $limit?$limit:$CMS->vars['pagination_number'];
        
        // SQL Select product & product_group & paging
        $resutls = page::init("SELECT product_name AS name, product_group, product_price_sell AS price_sell, product_price AS price, product_description AS description, product_id, product_image, product_up, product_price_old, product_name_lang, product_description_lang, product_shorturl AS url, product_shorturl_lang 
                FROM ".root_table."product 
                WHERE product_deleted=0 {$clause} AND product_status IN (0,1) AND product_show=1 AND product_type='{$product_type}' {$group_by} ORDER BY product_group ASC, product_order ASC", $number_page, $paging, 'web.product');

        $output = [];
        $i = 1;
        // Load data
        if($resutls)
        {
            foreach ( $resutls as $data )
            {    
                $data = self::convertService($data);

                $data['id'] = $data['product_id'];
                $data['inc'] = $i < 10 ? "0".$i : $i;
                $data['price_sell_og'] =    $data['price_sell'];
                $data['price_sell'] = $data['price_sell'] > 0 ? $data['price_sell'] : $data['price'];
                $data['price_sell'] = $data['price_sell'] > 0 ? $CMS->class->input->currency($data['price_sell']) : "";
                $data['product_description'] = $data['description'] ? $data['description'] : "";
                $data['description_ori'] = html_entity_decode($data['product_description'], ENT_QUOTES | ENT_XML1, 'UTF-8');
                
                $data['description'] = input::substr(preg_replace("/<img[^>]+\>/i", "", $data['description']), 0, 100);
                $data['link_book'] = "/book/service-{$data['product_id']}/";
                $data['product_up'] = " ".$data['product_up'];
                $data['product_price_old'] = $data['product_price_old'] > 0 ? $CMS->class->input->currency($data['product_price_old']) : "";
                // Convert image
                $data['uploadPath'] = "product/{$data['product_image']}";
                $data['image'] = input::getThumb($data['uploadPath'], $resizeWidth);

                // Add new data structure json format use for Theme nail02a
                $json_data = json_encode(array("id"=>$data['product_id'],"name"=>$data['name'], "image"=>$data['image'], "description"=>$data['description'], "description_full" => $data['product_description']));
                $data['json_data'] = $json_data;

                $output[] = $data;
                $i++;
            }
        }


        return $output;
    }

    /**
     * Get List product and Group product
     * @return array
     */

    static public function getListServiceCategory()
    {
        global $DB, $CMS;
        // SELECT product & product_group
 
        $results = $DB->fetch_data("SELECT product_id AS id, product_name AS name, product_group_id AS group_id, product_group_name AS group_name, product_price_sell AS price_sell, product_price AS price, staff_id, product_up, product_name_lang, product_description_lang,product_description AS description,product_group_parent as group_parent, product_group_avatar as group_image, product_subitem AS subitem,   product_option AS product_option FROM ".root_table."product AS P LEFT JOIN ".root_table."product_group AS PG ON P.product_group=PG.product_group_id
                    WHERE product_deleted=0 AND product_group<>0 AND product_show=1 AND product_status IN (0,1) AND product_type=1 AND PG.product_group_type=1 AND PG.product_group_status=1 ORDER BY PG.product_group_order ASC, PG.product_group_id ASC, P.product_order ASC", 'product.product_group');

        $output = [];
        $pg_id = 0;
        $pg_name = '';
        $services = array();

        // Load data
        if($results)
        {
            foreach ( $results as $data )
            {
                
                if ( $pg_id != $data['group_id'] )
                {
                    if ( $pg_id != 0 )
                    {
                        $output[] = array('id' => $pg_id, 'name' => $pg_name,'image'=>"product/".$data['group_image'],'group_parent'=>$data['group_parent'], 'services' => $services);
                        $services = array();
                    }

                    $pg_id = $data['group_id'];
                    $pg_name = $data['group_name'];
                }
     
                if(strpos($data['product_option'],"6") == true) //Dịch vụ khác
                {
                    $data['other_p'] = 1;
                }else{  $data['other_p'] = 0; }


                $data = self::convertService($data);
                $data['price_sell_og'] = $data['price_sell'] ;
                $data['price_sell'] = $data['price_sell'] > 0 ? $data['price_sell'] : $data['price'];
                $data['price_sell'] = $data['price_sell'] > 0 ? $CMS->class->input->currency($data['price_sell']) : "";
                $data['product_up'] = $data['product_up'] ? " ".$data['product_up'] : "";
                $data['price_sell_show'] = $data['price_sell'] ? " (".$data['price_sell'].$data['product_up'].") " : "";

                $info_staff = self::getListStaff($data['id']);
                $services[] = array('id' => $data['id'],'name' => $data['name'],'price_sell' => $data['price_sell'],'staff' => json_encode($info_staff, JSON_UNESCAPED_UNICODE),'product_up' => $data['product_up'],'price_sell_show' => $data['price_sell_show'], 'price_sell_og' => $data['price_sell_og'], 'description'=>$data['description'], 'subitem' => $data['subitem'] , 'other_p' => $data['other_p']);
            }
        }
   
        $output[] = array('id' => $pg_id,'name' => $pg_name,'image'=>"product/".$data['group_image'],'group_parent'=>$data['group_parent'],'services' => $services);
        
        return $output;
    }

    
    static function getInfo($record_id=0, $field_return="*")
    {
        global $DB, $CMS;

        if($record_id and $field_return)
        {
            $sql_add = '';
            // Count field
            $countField = count(explode(",", $field_return));
            // sqladd
            //Query
            $data = $DB->fetch_data("SELECT {$field_return} FROM ".root_table."product WHERE product_deleted=0 AND (product_id='{$record_id}' OR product_shorturl='{$record_id}') {$sql_add} LIMIT 1", 'product')[0];

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

    /**
     * Get list staff of a product for booking service
     * @param int $product_id
     * @return array
     */
    
    static function getListStaff($product_id=0)
    {
        global $DB, $CMS;

        $data = $DB->fetch_data("SELECT staff_id FROM ".root_table."product WHERE product_id = '{$product_id}'",'product')[0];
        $arr_id = json_decode($data['staff_id']);
        $output = [];
        $i = 0;
        if ( $arr_id ) {
            foreach ($arr_id as $user_id) {
                $user = $CMS->user->get_info($user_id);
                if($user) {
                    $output[$i]['id'] = $user_id;
                    $output[$i]['name'] = $user['user_display_name'];
                    $path = "avatar/{$user['user_avatar']}";
                    $thumb = \lib\image::getThumb($path, 'thumbnail');
                    $output[$i]['imageThumb'] = input::checkImage($thumb);
                    $output[$i]['image'] = input::checkImage($path);
                    $i++;
                }
            }
        }

        return $output;
    }

    static function getListChild($group_id=0, $pg_type=1)
    {
        global $CMS, $DB;

        $results= $DB->fetch_data("SELECT product_group_name AS name, product_group_id AS id, product_group_description as description, product_group_avatar as image, pg_shorturl AS url
                    FROM ".root_table."product_group 
                    WHERE product_group_deleted=0 AND product_group_status=1 AND product_group_type='{$pg_type}' AND product_group_parent='{$group_id}' ORDER BY product_group_order ASC", 'product_group');

        $output = [];

        // Load Data
        if(!$results) return $output;

        foreach( $results as $data )
        {
            $data = self::convertCategory($data);
            $path = "product/".$data['image'];
            $thumb = \lib\image::getThumb($path, "thumbnail", 'S_', 1, 150);
            $data['imageThumb'] = input::checkImage($thumb);
            $data['image'] = input::checkImage($path);
            $data['list_service_child'] = self::getListServiceByCat($data['id'], $pg_type);

            // Return Data
            $output[] = $data;
        }

        return $output;
    }

    static function getListProductGroupChild($group_id=0)
    {
        global $CMS, $DB;

        $resutls = $DB->fetch_data("SELECT product_group_id as id FROM ".root_table."product_group WHERE product_group_deleted=0 AND product_group_status=1 AND product_group_type=1 AND product_group_parent='{$group_id}' ORDER BY product_group_order ASC", 'product_group');

        $list_str = "{$group_id},";

        if($resutls)
        {
            foreach($resutls as $data)
            {
                $list_str .= $data['id'].",";
            }
            
            $list_str = rtrim($list_str,",");
        }

        $list_str = rtrim($list_str,",");

        return $list_str;
    }

    static function getListServiceByCat($cat_id=0, $p_type=1)
    {
        global $CMS, $DB;

        // check list child-cat
        $clause = $cat_id ? " AND product_group = '{$cat_id}' " : "";
        $group_by = "";
        // $group_by = $group ? " GROUP BY {$group} " : "";
//        $climit = $limit ? " LIMIT {$limit} " : "";

        // Set number pagination
        $number_page = $CMS->vars['pagination_number'];
        
        // SQL Select product & product_group & paging
        $results = $DB->fetch_data("SELECT product_name AS name, product_group, product_price_sell AS price_sell, product_price AS price, product_description AS description, product_id, product_image, product_up, product_price_old, product_name_lang, product_description_lang, product_shorturl AS url, product_shorturl_lang
                FROM ".root_table."product 
                WHERE product_deleted=0 {$clause} AND product_status IN (0,1) AND product_show=1 AND product_type='{$p_type}' {$group_by} ORDER BY product_order ASC", 'product');
        
        $output = [];

        if(!$results) return $output;

        // Load data
        foreach ( $results as $data )
        {
            $data = self::convertService($data);
            $data['price_sell'] = $data['price_sell'] > 0 ? $data['price_sell'] : $data['price'];
            $data['price_sell'] = $data['price_sell'] > 0 ? $CMS->class->input->currency($data['price_sell']) : "";
            $data['product_description'] = $data['description'];
            $data['description'] = input::substr($data['description'], 0, 100);
            $data['link_book'] = "/book/service-{$data['product_id']}/";
            $data['product_up'] = " ".$data['product_up'];
            $data['product_price_old'] = $data['product_price_old'] > 0 ? $CMS->class->input->currency($data['product_price_old']) : "";
            // Convert image
            $data['uploadPath'] = "product/{$data['product_image']}";
            $data['image'] = input::getThumb($data['uploadPath']);

            $output[] = $data;
            
        }
       
        return $output;

    }

    static function convertService($data=[])
    {
        global $CMS;

        if($CMS->vars['translations'])
        {
            $name = @json_decode($data['product_name_lang'], true);
            $data['name'] = $name ? $name : $data['name'];

            $description = @json_decode($data['product_description_lang'], true);
            $data['description'] = $description ? $description : (isset($data['description']) ? $data['description'] : '');// of news
            //Đa ngôn ngữ
            if(is_array($data['name']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    if($langCode == $CMS->vars['default_language'])
                    {
                        $data['name'] = $data['name'][$langCode];
                        break;
                    }
                }
            }

            if(is_array($data['description']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    if($langCode == $CMS->vars['default_language'])
                    {
                        $data['description'] = $data['description'][$langCode];
                        break;
                    }
                }
            }

            $shorturl = @json_decode($data['product_shorturl_lang'], true);
            $data['url'] = $shorturl ? $shorturl : ( isset($data['url']) ? $data['url'] : null);
            if(is_array($data['url']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    if($langCode == $CMS->vars['default_language'])
                    {
                        $data['url'] = $data['url'][$langCode];
                        break;
                    }
                }
            }

        }
        else
        {
            if(is_array($data['name']))
            {
                $data['name'] = $data['name'][$CMS->vars['default_language']];
            }

            if(isset($data['description']) && is_array($data['description']))
            {
                $data['description'] = $data['description'][$CMS->vars['default_language']];
            }

            if(isset($data['url']) && is_array($data['url']))
            {
                $data['url'] = $data['url'][$CMS->vars['default_language']];
            }
        }

        $data['url'] = $data['url_original']= isset($data['url']) ? $data['url'] : null;
        $data['id'] = isset($data['id']) ? $data['id'] : null;
        //$data['url_non_html'] = '/'. $data['url_original'] . '-s' . ($data['id'] ? $data['id'] : $data['product_id']);
        $data['url'] = '/'. $data['url'] . '-s' . ($data['id'] ? $data['id'] : $data['product_id']) .'.html';
        $data['url_none_html'] = '/'. $data['url_original'] . '-s' . ($data['id'] ? $data['id'] : $data['product_id']);
        
        return $data;
    }

    /**
     * Get category info
     * @return array
     */
    static public function getCategoryInfo( $record_id = 0, $field_return = "*" )
    {
        global $DB, $CMS;

        // Output
        $output = [];

        if( $record_id AND $field_return )
        {
            // Count field
            $countField = count(explode(",", $field_return));

            // Sql
            $sqlString = "SELECT product_group_name AS name, product_group_id AS id, product_group_description as description, product_group_avatar as image, pg_shorturl AS url
                    FROM ".root_table."product_group 
                    WHERE product_group_deleted = 0 AND product_group_status = 1 AND product_group_type = 1 AND product_group_id = '{$record_id}' LIMIT 1";

            // Get data
            $output = $DB->fetch_data($sqlString,'product_group')[0];

            // Convert data
            $output = self::convertCategory($output);
            $path = "product/" . $output['image']; // path image
            $thumbPath = \lib\image::getThumb($path, "thumbnail", 'S_', 1, 150); // get thumbnail

            $output['pathUpload'] = $path;
            $output['image'] = input::checkImage($path);
            $output['imageThumb'] = input::checkImage($thumbPath);
            $output['list_child'] = self::getListChild($output['id']);

            //Check return
            if( $countField == 1 AND $field_return != "*" )
            {
                $output = $output[$field_return];
            }
        }

        return $output;
    }

    /**
     * Convert category data
     * @param $data
     * @return mixed
     */

    static function convertCategory($data)
    {
        global $CMS;

      
        $data['url'] = '/' . $data['url'] . '-sc' . $data['id'] .'.html';
        $data['url_group'] = '/service/group-'. $data['id'];

        return $data;
    }

    /**
     * ThamLV M11-D10-Y2017
     * Get List services by category for block
     * Config id and limit for block service with format string: block_location=category_id-limit, block_location=category_id-limit, ...
     * @param $data
     * @return array()
     */
    static function getServiceBlockByCategory( $serviceBlockByCategory = '', $limit = 4 )
    {
        global $CMS;

        $output = [];

        if ( $serviceBlockByCategory )
        {
            $serviceBlockByCategory = explode(',', $serviceBlockByCategory);
            foreach ( $serviceBlockByCategory as $dataService ) 
            {
                if ( $dataService )
                {
                    $dataService = explode('=', $dataService);
                    $dataService['1'] = explode('-', $dataService['1']);

                    $blockKey = trim($dataService['0']);
                    $blockId  = intval($dataService['1']['0']);
                    $blockLimit = intval($dataService['1']['1']);
                    $blockLimit = $blockLimit ? $blockLimit : $limit;

                    if ( $blockKey AND $blockId )
                    {
                        $output[$blockKey] = self::getListService($blockId, '', $blockLimit);
                    }
                }
            }
        }

        return $output;
    }
}