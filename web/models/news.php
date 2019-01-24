<?php

namespace models;

use core\ezy;
use lib\date;
use lib\input;
use lib\page;
use models\app;

class news
{
    /**
     * Get List news
     * @return array
     */

    static public function getListNews($news_id_hot = 0, $limit = 4, $cat_id = 0, $paging = false)
    {
        global $CMS, $DB, $tpl;

        // Hot news
        $news_id_hot = $news_id_hot ? "AND news_is_hot=1" : "";

        // Category
        $cat_id = $cat_id ? "AND N.cat_id LIKE '%|{$cat_id}|%'" : "";

        // Keyword
        $keyword = isset($tpl->keyword) ? " AND (news_name LIKE '%{$tpl->keyword}%' OR U.user_display_name LIKE '%".urldecode($tpl->keyword)."%') " : "";

        //Tags
        $tags = isset($tpl->tags) ? " AND news_tags LIKE '%\"{$tpl->tags}\"%' " : "";

        // SQL Select news & news_category & paging
        $results = page::init("SELECT news_id AS id, news_name AS name, news_description AS description, news_image AS image, news_image_alt as image_alt, news_time AS `time`, news_shorturl AS url, news_views AS views, cat_name, cat_shorturl AS cat_url, U.user_display_name, C.cat_id, news_tags AS tags
                    FROM ".root_table."news AS N LEFT JOIN ".root_table."news_category AS C ON N.cat_id=concat('|',C.cat_id,'|') LEFT JOIN ".root_table."user As U ON N.user_id = U.user_id 
                    WHERE news_deleted=0 {$keyword} {$tags} {$news_id_hot} {$cat_id} ORDER BY news_time DESC", $limit, $paging, 'web.news.news_category');

        // Declare output
        $output = [];

        // Load data
        if($results)
        {
            foreach ( $results as $data )
            {
                $output[] = self::convertNews($data);
                // print "<pre>"; print_r($output);exit;
            }
        }

        // Output
        return $output;
    }

    /**
     * Get List News Category
     */

    static public function getListCategory($cat_id=0)
    {
        global $DB;
        $cat_id_parent = $cat_id ? self::get_cat_parent($cat_id) : 0;
        $results = $DB->fetch_data("SELECT cat_name AS name, cat_shorturl AS url, cat_count AS posts_count, cat_id 
                    FROM ".root_table."news_category WHERE cat_deleted=0 AND parent_id='{$cat_id_parent}' AND cat_status=1 ORDER BY cat_order ASC", 'news_category');

        $output = [];

        if($results)
        {
            foreach( $results as $data )
            {
                $data = self::convertCategoryNews($data);
                $data['cat_child'] = self::getListCatChild($data['cat_id']);
                $data['class_sub'] = count($data['cat_child']) > 0 ? " class='sub' tabindex='1' " : "";
                $data['img_sub'] = count($data['cat_child']) > 0 ? '<img src="common/images/minus.png" alt="">' : "";
                $output[] = $data;
            }
        }

        return $output;
    }

    static public function getListCatChild($cat_id=0)
    {
        global $DB;

        $results = $DB->fetch_data("SELECT cat_name AS name, cat_shorturl AS url, cat_count AS posts_count, cat_id 
                    FROM ".root_table."news_category WHERE cat_deleted=0 AND parent_id='{$cat_id}' AND cat_status=1 ORDER BY cat_order ASC",'news_category');

        $output = [];

        if(!$results) return $output;

        foreach ( $results as $data )
        {
            $data = self::convertCategoryNews($data);
            $data['cat_child'] = self::getListCatChild($data['cat_id']);
            $output[] = $data;
        }

        return $output;
    }

    /**
     * Get Detail Category
     */

    static public function getCategory($cat_shorturl="")
    {
        global $DB;

        $DB->query("SELECT cat_id AS id, cat_name AS name, cat_shorturl AS url, cat_count AS posts_count, meta_title as seo_title, meta_description as seo_description, meta_keywords as seo_keywords, cat_description AS description
                    FROM ".root_table."news_category WHERE (cat_shorturl LIKE '%{$cat_shorturl}%' OR cat_id='{$cat_shorturl}') AND cat_deleted=0 AND cat_status=1");
        $data = self::convertCategoryNews($DB->fetch_array());
        return $data;
    }

    /**
     * Get Detail News
     */

    static public function getNews($news_shorturl = "")
    {
        global $DB;

        // ThamLV Y2017D19M08
        // rtrim '-shorturl' đã thêm trong convertNews
        // if ( substr($news_shorturl, -9) == '-shorturl' ) {
        //     $news_shorturl = substr_replace($news_shorturl ,"",-9);
        // }

        if ( ! $news_shorturl ) { return false; }
        
        $add_sql = is_numeric($news_shorturl) ? " news_id='{$news_shorturl}' " : " news_shorturl LIKE '%{$news_shorturl}%' ";

        $DB->query("SELECT news_id AS id, news_name AS name, news_description AS description, news_content AS content, news_image AS image, news_time AS `time`, news_shorturl AS url, news_views AS views, cat_name, cat_shorturl AS cat_url, N.meta_title as seo_title, N.meta_description as seo_description, N.meta_keywords as seo_keywords, news_tags as tags, U.user_display_name, C.cat_id
                    FROM ".root_table."news AS N LEFT JOIN ".root_table."news_category AS C ON N.cat_id=concat('|',C.cat_id,'|') LEFT JOIN ".root_table."user As U ON N.user_id = U.user_id 
                    WHERE news_deleted=0 AND {$add_sql} ");
        return self::convertNews($DB->fetch_array());
    }

    /**
     * Convert news data
     * @param $data
     * @return mixed
     */

    static private function convertNews($data)
    {
        global $CMS;
// print "<pre>";print_r($data);exit;
        $name = @json_decode($data['name'], true);
        $data['name'] = $name ? $name : $data['name'];

        $shorturl = @json_decode($data['url'], true);
        $data['url'] = $shorturl ? $shorturl : $data['url'];
// print "<pre>";print_r($data['news_description']);exit;
        $description = @json_decode($data['description'], true);
        $data['description'] = $description ? $description : (isset($data['description']) ? $data['description'] : null);// of news

        $cat_url = @json_decode($data['cat_url'], true);
        $data['cat_url'] = $cat_url ? $cat_url : (isset($data['cat_url']) ? $data['cat_url'] : null);

        $cat_name = @json_decode($data['cat_name'], true);
        $data['cat_name'] = $cat_name ? $cat_name : (isset($data['cat_name']) ? $data['cat_name'] : null);

        $content = @json_decode($data['content'], true);

        $data['content'] = $content ? $content : (isset($data['content']) ? $data['content'] : null);// of news
// print_r($content);exit;
        if($CMS->vars['translations'])
        {
            //Đa ngôn ngữ
            if(is_array($data['cat_url']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    if($langCode == $CMS->vars['default_language'])
                    {
                        $data['cat_url'] = $data['cat_url'][$langCode];
                        break;
                    }
                }
            }

            if(is_array($data['cat_name']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    if($langCode == $CMS->vars['default_language'])
                    {
                        $data['cat_name'] = $data['cat_name'][$langCode];
                        break;
                    }
                    
                }

            }

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

            if(is_array($data['content']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    if($langCode == $CMS->vars['default_language'])
                    {
                        $data['content'] = $data['content'][$langCode];
                        break;
                    }
                }
            }

        }
        else
        {
            if(is_array($data['cat_url']))
            {
                $data['cat_url'] = $data['cat_url'][$CMS->vars['default_language']];
            }

            if(is_array($data['cat_name']))
            {
                $data['cat_name'] = $data['cat_name'][$CMS->vars['default_language']];
            }

            if(is_array($data['name']))
            {
                $data['name'] = $data['name'][$CMS->vars['default_language']];
            }

            if(is_array($data['description']))
            {
                $data['description'] = $data['description'][$CMS->vars['default_language']];
            }

            if(is_array($data['url']))
            {
                $data['url'] = $data['url'][$CMS->vars['default_language']];
            }

            if(is_array($data['content']))
            {
                $data['content'] = $data['content'][$CMS->vars['default_language']];
            }

        }


        // ThamLV Y2017D19M08
        // Thêm '-shorturl' để tránh trường hợp url kết thúc là số vd: 'xxx-2'
        // Khi get infor sẽ rtim nó.
        $data['url_none_html'] = "/{$data['url']}-n{$data['id']}";
        $data['url'] = "/{$data['url']}-n{$data['id']}.html";

        //Get thumb
        $data['image'] = isset($data['image']) ? $data['image'] : null;
        $path = "news/{$data['image']}";

        $data['pathUpload'] = $path;

        //Sise M
        $thumb = \lib\image::getThumb($path, 'thumbnail_large', '', 1, 600);
        $data['image_L'] = input::checkImage($thumb);

        //Sise M
        $thumb = \lib\image::getThumb($path, 'thumbnail_medium', '', 1, 450);
        $data['image_M'] = input::checkImage($thumb);

        //Sise S
        $thumb = \lib\image::getThumb($path, 'thumbnail_small', '', 1, 300);
        $data['image'] = input::checkImage($thumb);

        //Sise SS
        $thumb = \lib\image::getThumb($path, 'thumbnail_small', 'SS_', 1, 100);
        $data['image_SS'] = input::checkImage($thumb);

        $data['description_full'] = $data['description'];
        $data['description'] = input::substr($data['description'],0,50);
        $data['cat_url_none_html'] = $data['cat_url'] ? "/{$data['cat_url']}-nc{$data['cat_id']}" : "";
        $data['cat_url'] = $data['cat_url'] ? "/{$data['cat_url']}-nc{$data['cat_id']}.html" : "";
        $data['tags'] = @json_decode($data['tags'], 1);
        $data['short_name'] = $data['name'];//$CMS->class->editor->substr($data['name'], 0, 25);
        $data['content'] = html_entity_decode($data['content']);

        return $data;
    }

    /**
     * Update views for a post
     * @param $data
     */

    static public function updateViews( $news_id )
    {
        global $DB;

        // Last views
        $lastview = isset($_SESSION['news'.intval($news_id)]) ? $_SESSION['news'.intval($news_id)] : 0;

        // Check last views
        if ( $lastview + 5*60 <= time() )
        {
//            unset($lastview);
            $lastview = 0;
            unset($_SESSION['news'.intval($news_id)]);
        }

        // Check for update views
        if ( !$lastview )
        {
            $DB->query("UPDATE ".root_table."news SET news_views=news_views+1 WHERE news_id='{$news_id}'");
            $_SESSION['news'.intval($news_id)] = time();
        }
    }

    /**
     * Get list Author
     */

    static public function getListAuthorNews()
    {
        global $DB;

        $DB->query("SELECT U.user_id, U.user_display_name as name
                    FROM ".root_table."news AS N LEFT JOIN ".root_table."user As U ON N.user_id = U.user_id 
                    WHERE news_deleted=0 GROUP BY U.user_id ORDER BY U.user_display_name DESC");
        $output = [];
        while ($result=$DB->fetch_array()) 
        {
            if($result['name'])
            {
                $output[] = $result;
            }
        }

        return $output;
    }

    /**
     * Get menu from news category
     */

    static public function getMenuFromNewsCat()
    {
        global $DB;

        $results = $DB->fetch_data("SELECT cat_name AS name, cat_shorturl AS url, cat_count AS posts_count, cat_id, cat_description 
                    FROM ".root_table."news_category WHERE cat_deleted=0 AND cat_status=1 ORDER BY cat_order ASC LIMIT 7", 'news_category');

        $output = [];

        if($results)
        {
            foreach( $results as $data )
            {
                $data['description'] = $data['cat_description'];
                $data = self::convertCategoryNews($data);
                $data['list_menu_child'] = self::getMenuChild($data['cat_id']);
                $data['cat_url'] = $data['description'] ? "/{$data['url']}-nc{$data['cat_id']}.html" : "";
                $output[] = $data;
            }
        }

        return $output;
    }

    static function getMenuChild($cat_id=0)
    {
        global $CMS, $DB;

        $results = $DB->fetch_data("SELECT news_id AS id, news_name AS name, news_shorturl AS url FROM ".root_table."news WHERE news_deleted=0 AND news_display=1 AND cat_id LIKE '%|{$cat_id}|%' ORDER BY news_order ASC", 'news');

        $output = [];

        if($results)
        {
            foreach($results as $data)
            {
                $data = self::convertNews($data);
                $output[] = $data;
            }
        }

        return $output;
    }

    static function convertCategoryNews($data=[])
    {
        global $CMS;

        $name = @json_decode($data['name'], true);
        $data['name'] = $name ? $name : $data['name'];

        $shorturl = @json_decode($data['url'], true);
        $data['url'] = $shorturl ? $shorturl : $data['url'];

        $description = @json_decode($data['description'], true);
        $data['description'] = $description ? $description : (isset($data['description']) ? $data['description'] : null);// of news


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

            if(is_array($data['description']))
            {
                $data['description'] = $data['description'][$CMS->vars['default_language']];
            }

            if(is_array($data['url']))
            {
                $data['url'] = $data['url'][$CMS->vars['default_language']];
            }

        }

        // url
        $data['cat_id'] = isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id'] : (isset($data['id']) ? $data['id'] : null);
        $data['cat_url_none_html'] = isset($data['url']) && $data['url'] ? "/{$data['url']}-nc{$data['cat_id']}" : "";
        $data['cat_url'] = isset($data['url']) && $data['url'] ? "/{$data['url']}-nc{$data['cat_id']}.html" : "";

        return $data;
    }

    static function get_cat_parent($parent_id=0)
    {
        global $CMS, $DB;

        if($parent_id)
        {
            $results = $DB->fetch_data("SELECT parent_id FROM ".root_table."news_category WHERE cat_id='{$parent_id}' AND cat_deleted = 0 AND cat_status=1 LIMIT 1", 'news_category');
            $data = isset($results[0]) ? $results[0] : null;
            if($data)
            {
                $id = $data['parent_id'];

                if($id>0)
                {
                    $cat_id = self::get_cat_parent($id);
                    return $cat_id;                
                }else
                {
                    return $parent_id;
                }
            }else
            {
                return $parent_id;
            }
        }
    }

    /*
    * Get News With Category
    */
    static function getListNewsWithCategory( $limit = 8 )
    {
        global $CMS;

        $output = array();

        $listCategory = self::getListCategory();
        
        if ( ! empty($listCategory) )
        {
            foreach ( $listCategory as $category ) 
            {
                $output[$category['url']] = $category;
                $output[$category['url']]['listNews'] = self::getListNews(0, $limit, $category['cat_id']);
            }
        }

        return $output;
    }

    static function getNewsByCatKey($catId = 0, $sql_add = "")
    {
        global $CMS, $DB;

        $catId *= 1;

        $sql = "SELECT * FROM " . root_table . "news WHERE {$sql_add} news_deleted = 0 AND news_display = 1 AND cat_id LIKE '%|{$catId}|%'";
        $rs = $DB->fetch_data($sql, 'news');

        return $rs;
    }
}

