<?php

namespace models;

use core\ezy;
use lib\date;
use lib\input;
use \lib\page;
use \models\app;

class gallery
{
    /**
     * Get List Category
     * @return array
     */
    static public $sqlAdd = "";
    static public function getListCategory($cat_is_hot = 0, $limit=0, $not_id=0, $all_cat_not_get_list = 0)
    {
        global $DB;

        // Get hot category
        $clause = $cat_is_hot ? " AND cat_is_hot='{$cat_is_hot}' " : "";
        $clause .= $not_id > 0 ? " AND cat_id != '{$not_id}' " : "";
        $limit_clause = $limit > 0 ? " LIMIT {$limit} " : "";
        // Select categories
        $sql = "SELECT cat_shorturl AS url, cat_name AS name, cat_description AS description, cat_id AS id, cat_image AS image, cat_time as `time`
                    FROM ".root_table."gallery_category 
                    WHERE cat_deleted=0 AND cat_status=1 {$clause} ORDER BY cat_order ASC, cat_name ASC {$limit_clause}";

        $results = $DB->fetch_data($sql,'gallery_category');

        $num_rows = count($results);
        $i = 0;
        $output = [];

        // Load Data
        foreach ( $results as $data )
        {
            // convert language
            $data = self::catGalleryConvert($data);

            $data['inc'] = $i >= 1 ? $i + 1 : "";
            $data['num'] = $i + 1;
            
            // Define last record
            $data["last"] = $i == $num_rows-1 ? true : false;
            $data['uploadPath'] = "gallery/{$data['image']}";
            $data['image'] = input::checkImage($data['uploadPath']);

            // Return Data
            if ( $all_cat_not_get_list != 1 ) 
            {
                if($data['gallery_list'] = self::checkGalleryByCat($data['id']))
                {
                    $output[] = $data;
                }
            }
            else
            {
                $output[] = $data;
            }

            $i++;
        }

        return $output;
    }

 
    /**
     * Get List Gallery
     * @return array
     */

    static public function getListGallery($cat_is_hot = 0, $limit = 8, $paging = false, $cat_id=0, $resizeWidth=0, $is_display=1)
    {
        global $CMS, $DB;

        // Get hot category
        $gallery_sort_type = !empty($CMS->vars['gallery_sort_type']) ? 'DESC' : 'ASC';
        $order_by = "gallery_sort_order {$gallery_sort_type}, ";
        $clause = $cat_is_hot ? " AND C.cat_is_hot='{$cat_is_hot}' " : "";
        $clause .= $cat_id ? " AND G.cat_id='{$cat_id}' " : "";
        $clause .= $is_display ? " AND gallery_display=1 " : "";

        // SELECT gallery & gallery_category
        $results = page::init("SELECT gallery_id AS id, gallery_name AS name, gallery_image AS image, gallery_description AS description, gallery_image_alt AS image_alt, C.cat_id AS cat_id, C.cat_shorturl AS cat_url 
                      FROM ".root_table."gallery AS G LEFT JOIN ".root_table."gallery_category AS C ON C.cat_id=G.cat_id
                      WHERE gallery_deleted=0 {$clause} ORDER BY {$order_by} gallery_id DESC", $limit, $paging,'web.gallery.gallery_category');

        $output = [];

        // Load data
        if($results)
        {
            foreach ( $results as $data )
            {
                // convert gallery
                $data = self::galleryConvert($data);
                $cats['cat_shorturl'] = $data['cat_url'];
                $data['cat_url'] = self::catGalleryConvert($cats)['cat_shorturl'];
                $image = $data['image'];

                //Check thumb medium
                $path = "gallery/{$image}";

                $data['pathUpload'] = $path;

                $thumb = \lib\image::getThumb($path, $CMS->gallery->thumb_folder, 'M_', 1, $CMS->gallery->thumb_size['M']);
                // Convert image
                $data['imageThumb'] = input::getThumb($thumb,$resizeWidth);

                //Original image
                // Convert image
                $data['image'] = $data['imageOri'] = input::getThumb($path,$resizeWidth);

                // Return data
                $output[] = $data;
            }
        }

        return $output;
    }


    /**
     * Get List Gallery
     * @return array
     */

    static public function getListGallery_dsg($cat_is_hot = 0, $limit = 8, $paging = false, $cat_id=0, $color_hair = 0, $resizeWidth=0, $is_display=1)
    {
        global $CMS, $DB;

        // Get hot category
        $clause = $cat_is_hot ? " AND C.cat_is_hot='{$cat_is_hot}' " : "";
        $clause .= $cat_id ? " AND G.cat_id='{$cat_id}' " : "";
        $clause .= !empty($color_hair) ? " AND G.hair_color='{$color_hair}' " : "";
        $clause .= $is_display ? " AND gallery_display=1 " : "";
 
        // SELECT gallery & gallery_category
        $results = page::init("SELECT gallery_name AS name, gallery_image AS image, gallery_description AS description, gallery_image_alt AS image_alt, C.cat_id AS cat_id, C.cat_shorturl AS cat_url 
                      FROM ".root_table."gallery AS G LEFT JOIN ".root_table."gallery_category AS C ON C.cat_id=G.cat_id
                      WHERE gallery_deleted=0 {$clause} ORDER BY gallery_id DESC", $limit, $paging,'web.gallery.gallery_category');

        $output = [];

        // Load data
        if($results)
        {
            foreach ( $results as $data )
            {
            
                // convert gallery
                $data = self::galleryConvert($data);
                $image = $data['image'];

                //Check thumb medium
                $path = "gallery/{$image}";

                $data['pathUpload'] = $path;

                $thumb = \lib\image::getThumb($path, $CMS->gallery->thumb_folder, 'M_', 1, $CMS->gallery->thumb_size['M']);
                // Convert image
                $data['imageThumb'] = input::getThumb($thumb,$resizeWidth);

                //Original image
                // Convert image
                $data['image'] = $data['imageOri'] = input::getThumb($path,$resizeWidth);

                // Return data
                $output[] = $data;
            }
        }

        return $output;
    }

    static function getInfoCategory($record_id=0, $field_return="*")
    {
        global $DB, $CMS;

        if($record_id and $field_return)
        {
            // Count field
            $countField = count(explode(",", $field_return));
            // sqladd
            $sql_add = self::$sqlAdd;
            //Query
            $sql = "SELECT {$field_return} FROM ".root_table."gallery_category WHERE cat_deleted=0 AND (cat_id='{$record_id}' OR cat_shorturl='{$record_id}') {$sql_add} LIMIT 1";
            $data = $DB->fetch_data($sql, 'gallery_category')[0];
            $data = self::catGalleryConvert($data);
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


    static public function getListGalleryByCat($limitGallery=12, $cat_id=0, $cat_is_hot=0)
    {
        global $CMS, $DB;

        // Get hot category
        $clause = $cat_id ? " AND cat_id='{$cat_id}' " : "";
        $clause .= $cat_is_hot ? " AND cat_is_hot='{$cat_is_hot}' " : "";

        // Select categories
        $sql = "SELECT cat_shorturl AS url, cat_name AS name, cat_id AS id, cat_image AS image
                    FROM ".root_table."gallery_category 
                    WHERE cat_deleted=0 AND cat_status=1 {$clause} ORDER BY cat_order ASC";

        $results = $DB->fetch_data($sql, 'gallery_category');

        $num_rows = count($results);
        $i = 0;
        $output = [];

        // Load Data
        if($results)
        {
            foreach ( $results as $data )
            {
                $data = self::catGalleryConvert($data);
                // Define last record
                $data['class_active'] = $i==0 ? 'active' : "";

                // use for get thumb lib
                $data['pathUpload'] = "gallery/{$data['image']}";

                $data['image'] = input::checkImage($data['pathUpload']);
                $listGallery = self::getdataGallery($limitGallery, $data['id']);
                if(count($listGallery) > 0)
                {
                    $data['listGallery'] = $listGallery;
                    // Return Data
                    $output[] = $data;
                    $i++;
                }

            }
        }

        return $output;
    }

    static public function getdataGallery($limit=12, $cat_id=0, $paging=false)
    {
        global $CMS, $DB;

        $gallery_sort_type = !empty($CMS->vars['gallery_sort_type']) ? 'DESC' : 'ASC';
        $order_by = "gallery_sort_order {$gallery_sort_type}, ";

        // Get hot category
        $clause = $cat_id ? " AND cat_id='{$cat_id}' " : "";
        // $limit = $limit ? " LIMIT {$limit} " : "";

        // SELECT gallery & gallery_category
        $results = page::init("SELECT gallery_id AS id, gallery_name AS name, gallery_image AS image, gallery_description AS description, gallery_image_alt AS image_alt 
                      FROM ".root_table."gallery
                      WHERE gallery_deleted=0 AND gallery_display=1 {$clause} ORDER BY {$order_by} gallery_id DESC", $limit, $paging, 'web.gallery');
        $output = [];
        
        // Load data
        if($results)
        {
            foreach ( $results as $data )
            {
                // convert gallery
                $data = self::galleryConvert($data);

                $image = $data['image'];

                //Check thumb medium
                $path = "gallery/{$image}";
                $thumb = \lib\image::getThumb($path, $CMS->gallery->thumb_folder, 'M_', 1, $CMS->gallery->thumb_size['M']);
                // Convert image
                $data['imageThumb'] = input::checkImage($thumb);

                // use for get thumb lib
                $data['pathUpload'] = $path;

                //Original image
                $path = "gallery/{$image}";
                // Convert image
                $data['image'] = $data['imageOri'] = input::checkImage($path);

                // Return data
                $output[] = $data;
            }
        }


        return $output;
    }

    static public function checkGalleryByCat($cat_id=0)
    {
        global $CMS, $DB;

        $clause = $cat_id ? " AND cat_id='{$cat_id}' " : "";
        // Select categories
        $sql = "SELECT gallery_name AS name, gallery_image AS image, gallery_description AS description, gallery_image_alt AS image_alt FROM ".root_table."gallery WHERE gallery_deleted=0 AND gallery_display=1 {$clause} ORDER BY gallery_id DESC";

        $results = $DB->fetch_data($sql, 'gallery');

        $output = [];
        if($results)
        {
            foreach($results as $data)
            {
                // convert gallery
                $data = self::galleryConvert($data);
                // Short name gallery
                $data['name_short'] = input::substr($data['name'],0,30);
                $image = $data['image'];
                //Check thumb medium
                $path = "gallery/{$image}";

                $data['pathUpload'] = $path;

                $thumb = \lib\image::getThumb($path, $CMS->gallery->thumb_folder, 'M_', 1, $CMS->gallery->thumb_size['M']);
                // Convert image
//                $data['imageThumb'] = input::getThumb($thumb,$resizeWidth);
                $data['imageThumb'] = input::getThumb($thumb);

                // Original image
//                $data['image'] = $data['imageOri'] = input::getThumb($path,$resizeWidth);
                $data['image'] = $data['imageOri'] = input::getThumb($path);

                $output[] = $data;
            }
            return $output;
        }else
        {
            return false;
        }
        
    }

    static function galleryConvert($data=array())
    {
        global $CMS;
 
        $name = @json_decode($data['name'], true);
        $data['name'] = $name ? $name : $data['name'];

        $description = @json_decode($data['description'], true);
        $data['description'] = $description ? $description : $data['description'];// of news

        if($CMS->vars['translations'])
        {
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

        }
        else
        {

            if(is_array($data['name']))
            {
                $data['name'] = $data['name'][$CMS->vars['default_language']];
            }

            if(is_array($data['description']))
            {
                $data['description'] = $data['description'][$CMS->vars['default_language']];
            }

        }

        return $data;
    }

    static function catGalleryConvert($data=array())
    {
        global $CMS;
        
        $data['name'] = !empty($data['name']) ? $data['name'] : (!empty($data['cat_name']) ? $data['cat_name'] : "");
        $data['url'] = !empty($data['url']) ? $data['url'] : (!empty($data['cat_shorturl']) ? $data['cat_shorturl'] : "");
        $data['description'] = !empty($data['description']) ? $data['description'] : (!empty($data['cat_description']) ? $data['cat_description'] : "");

        $name = @json_decode($data['name'], true);
        $data['name'] = $name ? $name : $data['name'];

        $url = @json_decode($data['url'], true);
        $data['url'] = $url ? $url : $data['url'];

        $description = @json_decode($data['description'], true);
        $data['description'] = $description ? $description : $data['description'];

        if($CMS->vars['translations'])
        {
            //Đa ngôn ngữ
            if($data['name'] and is_array($data['name']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    if($langCode == $CMS->vars['default_language'])
                    {
                        $data['name'] = $data['cat_name'] = $data['name'][$langCode];
                        break;
                    }
                }
            }

            if($data['url'] and is_array($data['url']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    if($langCode == $CMS->vars['default_language'])
                    {
                        $data['url'] = $data['cat_shorturl'] = $data['url'][$langCode];
                        break;
                    }
                }
            }

            if($data['description'] and is_array($data['description']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    if($langCode == $CMS->vars['default_language'])
                    {
                        $data['description'] = $data['cat_description'] = $data['description'][$langCode];
                        break;
                    }
                }
            }

        }
        else
        {

            if(is_array($data['name']))
            {
                $data['name'] = $data['cat_name'] = $data['name'][$CMS->vars['default_language']];
            }

            if(is_array($data['url']))
            {
                $data['url'] = $data['cat_shorturl'] = $data['url'][$CMS->vars['default_language']];
            }

            if(is_array($data['description']))
            {
                $data['description'] = $data['cat_description'] = $data['description'][$CMS->vars['default_language']];
            }

        }

        return $data;
    }


    static function getCounShareSocial($link="", $type="all")
    {
        global $CMS;

        $link="http://suckhoe.vnexpress.net/tin-tuc/suc-khoe/canh-bao-mieng-dan-chong-say-xe-gay-roi-loan-tam-than-cho-tre-nho-3623217.html";
        print urlencode($link);exit;
        // Get facebook
        $count_facebook = json_decode(file_get_contents("https://graph.facebook.com/?id={$link}"), 1)['share']['share_count'];


        print "<pre>";
        print_r($count_facebook);exit;
    }


    static function get_cate_bygroup($id = "")
    {
        global $DB, $CMS;

        if($id != "" )
        {
            // Count field
            $countField = count(explode(",", $field_return));
            // sqladd
            $sql_add = " AND  cat_cate = '{$id}' ";
            //Query
            $sql = "SELECT * FROM ".root_table."gallery_category WHERE cat_deleted=0  {$sql_add} ";
      
            $data = $DB->fetch_data($sql);
            $data = self::catGalleryConvert($data);
            return $data;
 
        }else
        {
            return false;
        }
    }

    static function getListGalleryByAllCats($limit = 0)
    {
        global $DB, $CMS;

        $data = [];

        $sql_cats = "SELECT cat_id, cat_shorturl FROM ".root_table."gallery_category WHERE cat_deleted=0 ORDER BY cat_order";

        $cats = $DB->fetch_data($sql_cats, 'gallery_category');
        
        foreach($cats as $cat)
        {
            $cats = self::catGalleryConvert($cats);
            $gallery = self::getdataGallery($limit, $cat['cat_id'], 0);

            foreach($gallery as $key => $item)
            {
                $gallery[$key]['cat_id'] = $cat['cat_id'];
                $gallery[$key]['cat_url'] = $cat['cat_shorturl'];
            }

            $data = array_merge($data, $gallery);
        }

        return $data;
    }

}