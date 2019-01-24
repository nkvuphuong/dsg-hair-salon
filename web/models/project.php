<?php
/**
 * Created by PhpStorm.
 * User: HLoi
 * Date: 4/4/2017
 * Time: 10:42 AM
 */

namespace models;

// Use
use core\ezy;
use lib\date;
use lib\input;
use lib\page;
use models\app;

class project
{
// ********************
// *** START CLASS
// ********************

    /**
     * Get List Project Category
     * @return array
     */
    static public function getListCategory( $record_id = 0 )
    {
        global $DB;

        $parent_id = $record_id ? self::getParentId($record_id) : 0;
        $sqlString = "
        SELECT cat_id AS id, cat_name AS name, cat_description AS description, cat_shorturl AS url, cat_count AS posts_count 
        FROM ".root_table."news_category 
        WHERE parent_id = '{$parent_id}' AND cat_type = 2 AND cat_deleted = 0 AND cat_status = 1 
        ORDER BY cat_order ASC 
        ";

        $sqlQuery = $DB->query($sqlString);

        $output = [];
        while ( $data = $DB->fetch_array($sqlQuery) )
        {
            $data = self::convertCategory($data);
            $data['listCategory'] = self::getListCategoryChild($data['id']);
            $data['countProject'] = self::getCountProjectOfCategory($data['id']);
            $data['listProject'] = self::getListProject(0, 0, $data['id'], false);
            $output[] = $data;
        }

        return $output;
    }

    /**
     * Get Get List Project Category Child Of Project Category
     * @return array
     * Max loop 5
     */
    static public function getListCategoryChild( $record_id = 0, $loop = 0 )
    {
        global $DB;

        // Max loop
        $loop = intval($loop) + 1;

        $output = [];

        if( $record_id AND $loop <= 5 )
        {
	        $sqlString = "
	        SELECT cat_id AS id, cat_name AS name, cat_description AS description, cat_shorturl AS url, cat_count AS posts_count 
	        FROM ".root_table."news_category 
	        WHERE parent_id = '{$record_id}' AND cat_type = 2 AND cat_deleted = 0 AND cat_status = 1 
	        ORDER BY cat_order ASC 
	        ";

	        $sqlQuery = $DB->query($sqlString);

	        $output = [];
	        while ( $data = $DB->fetch_array($sqlQuery) )
	        {
	            $data = self::convertCategory($data);
	            $data['listCategory'] = self::getListCategoryChild($data['id'], $loop);
	            $data['countProject'] = self::getCountProjectOfCategory($data['id']);
	            $output[] = $data;
	        }
	    }

        return $output;
    }

    /**
     * Get Parent Id Of Project Category
     * @return number
     * Max loop 5
     */
    static function getParentId( $record_id = 0, $loop = 0 )
    {
        global $CMS, $DB;

        // Max loop
        $loop = intval($loop) + 1;

        $output = $record_id;

        if( $record_id AND $loop <= 5 )
        {
            $sqlString = "
            SELECT parent_id 
            FROM ".root_table."news_category 
            WHERE cat_id = '{$record_id}' AND cat_type = 2 AND cat_deleted = 0 AND cat_status = 1 
            LIMIT 1
            ";

            $sqlQuery = $DB->query($sqlString);

            if( $DB->num_rows($sqlQuery) > 0 )
            {
                $data = $DB->fetch_array($sqlQuery);
                if( $data['parent_id'] > 0 )
                {
                    $output = self::getParentId($data['parent_id'], $loop);
                }
            }
        }

        return $output;
    }

    /**
     * Convert Data Of Project Category
     * @return array array
     */
    static function convertCategory( $data = [] )
    {
        global $CMS;

        $name = @json_decode($data['name'], true);
        $data['name'] = $name ? $name : $data['name'];

        $shorturl = @json_decode($data['url'], true);
        $data['url'] = $shorturl ? $shorturl : $data['url'];

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

        $data['description'] = html_entity_decode($data['description']);

        // url
        $data['url'] = $data['url'] ? "/{$data['url']}-jc{$data['id']}.html" : "";

        return $data;
    }

    /**
     * Get List Project
     * @return array
     */
    static public function getListProject( $is_hot = 0, $limit = 4, $cat_id = 0, $paging = false, $none_cat = 0 )
    {
        global $CMS, $DB, $tpl;

        // Hot Project
        $is_hot = $is_hot ? "AND news_is_hot = 1" : "";

        // Category Project
        $cat_id = $cat_id ? "AND N.cat_id LIKE '%|{$cat_id}|%'" : "";

        // None category project
        $none_cat = $none_cat ? "AND N.cat_id = ''" : "";

        // Keyword
        $keyword = isset($tpl->keyword) ? " AND (news_name LIKE '%{$tpl->keyword}%' OR U.user_display_name LIKE '%".urldecode($tpl->keyword)."%') " : "";

        //Tags
        $tags = isset($tpl->tags) ? " AND news_tags LIKE '%\"{$tpl->tags}\"%' " : "";

        // SQL Select Project & Project Category & Paging
        $sqlString = "
        SELECT news_id AS id, news_name AS name, news_description AS description, news_image AS image, news_time AS `time`, news_shorturl AS url, news_views AS views, N.cat_id AS list_cat, C.cat_id, cat_name, cat_shorturl AS cat_url, U.user_display_name  
        FROM ".root_table."news AS N LEFT JOIN ".root_table."news_category AS C ON N.cat_id = concat('|',C.cat_id,'|') LEFT JOIN ".root_table."user As U ON N.user_id = U.user_id 
        WHERE news_type = 2 AND news_deleted = 0 AND news_display = 1 {$keyword} {$tags} {$is_hot} {$cat_id} {$none_cat}
        ORDER BY news_time DESC 
        ";

        $results = page::init($sqlString, $limit, $paging, 'web.news.news_category.user');

        // Declare output
        $output = [];

        // Load data
        if($results)
        {
            foreach ( $results as $data )
            {
                $output[] = self::convertProject($data);
            }
        }


        // Output
        return $output;
    }

    /**
     * Convert Data Of Project
     * @return mixed array
     */
    static private function convertProject($data)
    {
        global $CMS;

        $name = @json_decode($data['name'], true);
        $data['name'] = $name ? $name : $data['name'];

        $shorturl = @json_decode($data['url'], true);
        $data['url'] = $shorturl ? $shorturl : $data['url'];

        $description = @json_decode($data['description'], true);
        $data['description'] = $description ? $description : $data['description'];// of news

        $cat_url = @json_decode($data['cat_url'], true);
        $data['cat_url'] = $cat_url ? $cat_url : $data['cat_url'];

        $cat_name = @json_decode($data['cat_name'], true);
        $data['cat_name'] = $cat_name ? $cat_name : $data['cat_name'];

        $content = @json_decode($data['content'], true);
        $data['content'] = $content ? $content : ( isset($data['content']) ? $data['content'] : '' );// of news

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

        // List category
        $data['list_cat'] = isset($data['list_cat']) ? $data['list_cat'] : '';
        $data['list_cat'] = explode('|', $data['list_cat']);
        $list_cat = [];
        foreach( $data['list_cat'] as $cat_id ) 
        {
        	if ( $cat_id ) 
        	{
        		$list_cat[] = self::getCategory($cat_id);
        	}
        }
        $data['list_cat'] = $list_cat;

        // Url detail
        $data['url'] = "/{$data['url']}-j{$data['id']}.html";

        // Path Upload
        $data['pathUpload'] = "news/{$data['image']}";

        $data['descriptionOri'] = $data['description'];
        $data['description'] = input::substr($data['description'],0,50);

        // Url Category
        $data['cat_url'] = $data['cat_url'] ? "/{$data['cat_url']}-jc{$data['cat_id']}.html" : "";

        $data['tags'] = @json_decode($data['tags'], 1);
        $data['short_name'] = $data['name'];
        $data['content'] = html_entity_decode($data['content']);

        return $data;
    }

    /**
     * Get Detail Project Category
     * Return array
     */
    static public function getCategory($shorturl="")
    {
        global $DB;

        $sqlString = "
        SELECT cat_id AS id, cat_name AS name, cat_description AS description, cat_shorturl AS url, cat_count AS posts_count, meta_title AS seo_title, meta_description AS seo_description, meta_keywords AS seo_keywords 
        FROM ".root_table."news_category WHERE (cat_shorturl LIKE '%{$shorturl}%' OR cat_id = '{$shorturl}') AND cat_deleted = 0 AND cat_status = 1 
        LIMIT 1 
        ";

        $sqlQuery = $DB->query($sqlString);

        $output = $DB->fetch_array($sqlQuery);
        $output = self::convertCategory($output);

        return $output;
    }

    /**
     * Get Detail Project
     @ Return array
     */
    static public function getProject($shorturl)
    {
        global $DB;

        $sqlString = "
        SELECT news_id AS id, news_name AS name, news_description AS description, news_content AS content, news_image AS image, news_time AS `time`, news_shorturl AS url, news_views AS views, N.meta_title AS seo_title, N.meta_description AS seo_description, N.meta_keywords AS seo_keywords, news_tags AS tags, C.cat_id, cat_name, cat_shorturl AS cat_url, U.user_display_name 
        FROM ".root_table."news AS N LEFT JOIN ".root_table."news_category AS C ON N.cat_id = concat('|',C.cat_id,'|') LEFT JOIN ".root_table."user As U ON N.user_id = U.user_id 
        WHERE news_type = 2 AND news_deleted = 0 AND news_display = 1 AND (news_shorturl LIKE '%{$shorturl}%' OR news_id = '{$shorturl}') 
        LIMIT 1 
        ";

        $sqlQuery = $DB->query($sqlString);

        $output = $DB->fetch_array($sqlQuery);
        $output = self::convertProject($output);

        return $output;
    }

    /**
     * Update views for a post
     * no return
     */
    static public function updateViews( $record_id = 0 )
    {
        global $DB;

        $record_id = intval($record_id);

        if ( $record_id ) 
        {
        	// Last views
	        $lastview = isset($_SESSION['news'.$record_id]) ? $_SESSION['news'.$record_id] : 0;

	        // Check last views
	        if ( $lastview + 5*60 <= time() )
	        {
	            unset($lastview);
	            unset($_SESSION['news'.$record_id]);
	        }

	        // Check for update views
	        if ( empty($lastview) )
	        {
	        	$sqlString = "
	        	UPDATE ".root_table."news 
	        	SET news_views = news_views+1 
	        	WHERE news_id = '{$record_id}' 
	        	";

	            $sqlQuery = $DB->query($sqlString);

	            $_SESSION['news'.$record_id] = time();
	        }
        }
    }

    /**
     * Get Count Project Of Project Category
     * @return number
     */
    static function getCountProjectOfCategory( $record_id = 0 )
    {
        global $CMS, $DB;

        $output = 0;

        if( $record_id )
        {
            $sqlString = "
            SELECT 0 
            FROM ".root_table."news 
            WHERE news_type = 2 AND news_deleted = 0 AND news_display = 1 AND cat_id LIKE '%|{$record_id}|%' 
            ";

            $sqlQuery = $DB->query($sqlString);

            $output = $DB->num_rows($sqlQuery);
        }

        return $output;
	}
// ********************
// *** END CLASS
// ********************
}