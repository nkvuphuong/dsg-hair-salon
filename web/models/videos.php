<?php

namespace models;

use core\ezy;
use lib\date;
use lib\input;
use lib\page;
use models\app;

class videos
{
    /**
     * Get List videos
     * @return array
     */

    static public function getListvideos($videos_id_hot = 0, $limit = 4, $cat_id = 0, $paging = false)
    {
        global $CMS, $DB, $tpl;

        // Hot videos
        $videos_id_hot = $videos_id_hot ? "AND videos_is_hot=1" : "";

        // Category
        $cat_id = $cat_id ? "AND N.cat_id LIKE '%|{$cat_id}|%'" : "";

        // Keyword
        $keyword = isset($tpl->keyword) ? " AND (videos_name LIKE '%{$tpl->keyword}%' OR U.user_display_name LIKE '%".urldecode($tpl->keyword)."%') " : "";

        //Tags
        $tags = isset($tpl->tags) ? " AND videos_tags LIKE '%\"{$tpl->tags}\"%' " : "";

        // SQL Select videos & videos_category & paging
        $results = page::init("SELECT videos_id AS id, videos_name AS name, videos_description AS description, videos_image AS image, videos_link  , videos_time AS `time`, videos_shorturl AS url, videos_views AS views, cat_name, cat_shorturl AS cat_url, U.user_display_name, C.cat_id, videos_tags AS tags
                    FROM ".root_table."videos AS N LEFT JOIN ".root_table."videos_category AS C ON N.cat_id=concat('|',C.cat_id,'|') LEFT JOIN ".root_table."user As U ON N.user_id = U.user_id 
                    WHERE videos_deleted=0 {$keyword} {$tags} {$videos_id_hot} {$cat_id} ORDER BY videos_time DESC", $limit, $paging, 'web.videos.videos_category');

        // Declare output
        $output = [];

        // Load data
        if($results)
        {
            foreach ( $results as $data )
            {
                $output[] = self::convertvideos($data);
                // print "<pre>"; print_r($output);exit;
            }
        }

        // Output
        return $output;
    }

    /**
     * Get List videos Category
     */

    static public function getListCategory($cat_id=0)
    {
        global $DB;
        $cat_id_parent = $cat_id ? self::get_cat_parent($cat_id) : 0;
        $results = $DB->fetch_data("SELECT cat_name AS name, cat_shorturl AS url, cat_count AS posts_count, cat_id 
                    FROM ".root_table."videos_category WHERE cat_deleted=0 AND parent_id='{$cat_id_parent}' AND cat_status=1 ORDER BY cat_order ASC", 'videos_category');

        $output = [];

        if($results)
        {
            foreach( $results as $data )
            {
                $data = self::convertCategoryvideos($data);
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
                    FROM ".root_table."videos_category WHERE cat_deleted=0 AND parent_id='{$cat_id}' AND cat_status=1 ORDER BY cat_order ASC",'videos_category');

        $output = [];

        if(!$results) return $output;

        foreach ( $results as $data )
        {
            $data = self::convertCategoryvideos($data);
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
                    FROM ".root_table."videos_category WHERE (cat_shorturl LIKE '%{$cat_shorturl}%' OR cat_id='{$cat_shorturl}') AND cat_deleted=0 AND cat_status=1");
        $data = self::convertCategoryvideos($DB->fetch_array());
        return $data;
    }

    /**
     * Get Detail videos
     */

    static public function getvideos($videos_shorturl = "")
    {
        global $DB;

        // ThamLV Y2017D19M08
        // rtrim '-shorturl' đã thêm trong convertvideos
        // if ( substr($videos_shorturl, -9) == '-shorturl' ) {
        //     $videos_shorturl = substr_replace($videos_shorturl ,"",-9);
        // }

        if ( ! $videos_shorturl ) { return false; }
        
        $add_sql = is_numeric($videos_shorturl) ? " videos_id='{$videos_shorturl}' " : " videos_shorturl LIKE '%{$videos_shorturl}%' ";

        $DB->query("SELECT videos_id AS id, videos_name AS name, videos_description AS description, videos_content AS content, videos_link , videos_time AS `time`, videos_shorturl AS url, videos_views AS views, cat_name, cat_shorturl AS cat_url, N.meta_title as seo_title, N.meta_description as seo_description, N.meta_keywords as seo_keywords, videos_tags as tags, U.user_display_name, C.cat_id
                    FROM ".root_table."videos AS N LEFT JOIN ".root_table."videos_category AS C ON N.cat_id=concat('|',C.cat_id,'|') LEFT JOIN ".root_table."user As U ON N.user_id = U.user_id 
                    WHERE videos_deleted=0 AND {$add_sql} ");
        return self::convertvideos($DB->fetch_array());
    }

    /**
     * Convert videos data
     * @param $data
     * @return mixed
     */

    static private function convertvideos($data)
    {
        global $CMS;
// print "<pre>";print_r($data);exit;
        $name = @json_decode($data['name'], true);
        $data['name'] = $name ? $name : $data['name'];

        $shorturl = @json_decode($data['url'], true);
        $data['url'] = $shorturl ? $shorturl : $data['url'];
// print "<pre>";print_r($data['videos_description']);exit;
        $description = @json_decode($data['description'], true);
        $data['description'] = $description ? $description : (isset($data['description']) ? $data['description'] : null);// of videos

        $cat_url = @json_decode($data['cat_url'], true);
        $data['cat_url'] = $cat_url ? $cat_url : (isset($data['cat_url']) ? $data['cat_url'] : null);

        $cat_name = @json_decode($data['cat_name'], true);
        $data['cat_name'] = $cat_name ? $cat_name : (isset($data['cat_name']) ? $data['cat_name'] : null);

        $content = @json_decode($data['content'], true);

        $data['content'] = $content ? $content : (isset($data['content']) ? $data['content'] : null);// of videos
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
        $data['url_none_html'] = "/{$data['url']}-vd{$data['id']}";
        $data['url'] = "/{$data['url']}-vd{$data['id']}.html";

        //Get thumb
        $data['image_L'] = self::embed_code($data['videos_link'],1);
        $data['videos_player'] = self::embed_code($data['videos_link'] );
        $path = "videos/{$data['image']}";

        $data['pathUpload'] = $path;

        $data['description_full'] = $data['description'];
        $data['description'] = input::substr($data['description'],0,50);
        $data['cat_url_none_html'] = $data['cat_url'] ? "/{$data['cat_url']}-vc{$data['cat_id']}" : "";
        $data['cat_url'] = $data['cat_url'] ? "/{$data['cat_url']}-vc{$data['cat_id']}.html" : "";
        $data['tags'] = @json_decode($data['tags'], 1);
        $data['short_name'] = $data['name'];//$CMS->class->editor->substr($data['name'], 0, 25);
        $data['content'] = html_entity_decode($data['content']);

        return $data;
    }

    /**
     * Update views for a post
     * @param $data
     */

    static public function updateViews( $videos_id )
    {
        global $DB;

        // Last views
        $lastview = isset($_SESSION['videos'.intval($videos_id)]) ? $_SESSION['videos'.intval($videos_id)] : 0;

        // Check last views
        if ( $lastview + 5*60 <= time() )
        {
//            unset($lastview);
            $lastview = 0;
            unset($_SESSION['videos'.intval($videos_id)]);
        }

        // Check for update views
        if ( !$lastview )
        {
            $DB->query("UPDATE ".root_table."videos SET videos_views=videos_views+1 WHERE videos_id='{$videos_id}'");
            $_SESSION['videos'.intval($videos_id)] = time();
        }
    }

    /**
     * Get list Author
     */

    static public function getListAuthorvideos()
    {
        global $DB;

        $DB->query("SELECT U.user_id, U.user_display_name as name
                    FROM ".root_table."videos AS N LEFT JOIN ".root_table."user As U ON N.user_id = U.user_id 
                    WHERE videos_deleted=0 GROUP BY U.user_id ORDER BY U.user_display_name DESC");
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
     * Get menu from videos category
     */

    static public function getMenuFromvideosCat()
    {
        global $DB;

        $results = $DB->fetch_data("SELECT cat_name AS name, cat_shorturl AS url, cat_count AS posts_count, cat_id, cat_description 
                    FROM ".root_table."videos_category WHERE cat_deleted=0 AND cat_status=1 ORDER BY cat_order ASC LIMIT 7", 'videos_category');

        $output = [];

        if($results)
        {
            foreach( $results as $data )
            {
                $data['description'] = $data['cat_description'];
                $data = self::convertCategoryvideos($data);
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

        $results = $DB->fetch_data("SELECT videos_id AS id, videos_name AS name, videos_shorturl AS url FROM ".root_table."videos WHERE videos_deleted=0 AND videos_display=1 AND cat_id LIKE '%|{$cat_id}|%' ORDER BY videos_order ASC", 'videos');

        $output = [];

        if($results)
        {
            foreach($results as $data)
            {
                $data = self::convertvideos($data);
                $output[] = $data;
            }
        }

        return $output;
    }

    static function convertCategoryvideos($data=[])
    {
        global $CMS;

        $name = @json_decode($data['name'], true);
        $data['name'] = $name ? $name : $data['name'];

        $shorturl = @json_decode($data['url'], true);
        $data['url'] = $shorturl ? $shorturl : $data['url'];

        $description = @json_decode($data['description'], true);
        $data['description'] = $description ? $description : (isset($data['description']) ? $data['description'] : null);// of videos


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
        $data['cat_url_none_html'] = isset($data['url']) && $data['url'] ? "/{$data['url']}-vc{$data['cat_id']}" : "";
        $data['cat_url'] = isset($data['url']) && $data['url'] ? "/{$data['url']}-vc{$data['cat_id']}.html" : "";

        return $data;
    }

    static function get_cat_parent($parent_id=0)
    {
        global $CMS, $DB;

        if($parent_id)
        {
            $results = $DB->fetch_data("SELECT parent_id FROM ".root_table."videos_category WHERE cat_id='{$parent_id}' AND cat_deleted = 0 AND cat_status=1 LIMIT 1", 'videos_category');
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
    * Get videos With Category
    */
    static function getListvideosWithCategory( $limit = 8 )
    {
        global $CMS;

        $output = array();

        $listCategory = self::getListCategory();
        
        if ( ! empty($listCategory) )
        {
            foreach ( $listCategory as $category ) 
            {
                $output[$category['url']] = $category;
                $output[$category['url']]['listvideos'] = self::getListvideos(0, $limit, $category['cat_id']);
            }
        }

        return $output;
    }

    /****************************************************************************
	* HTML EMBED MEDIA
	****************************************************************************/
	static function embed_code( $url = "", $type = 0 )
	{
			global $CMS, $member, $DB;
			//print_r ($url);exit;
			$output = "";
		 
			$post_url = parse_url($url);
			$host = preg_replace('/www./','',$post_url['path']);
			$host = substr($host,0, -6);
			$split = explode('v=',$post_url['query']);
			$split_1 = explode('&',$post_url['query']);
			 if(stristr($split_1[0], 'v=') == TRUE) 
			 {
				$split = explode('v=',$split_1[0]);
			}
			else
			{
				$split = explode('v=',$split_1[1]);
		    }	
			$link = $split[1];

            if($host == "www.youtube.com" || $host == "youtube.com" || $post_url['host'] == "www.youtube.com" || $post_url['host'] == "youtube.com")
			{
				if($type == 3)
				{
				$output .=<<<EOF
	
				
				  	  <object width="100%" height="70%">
					<param name="movie" value="http://www.youtube.com/v/{$link}" />
					<embed src="http://www.youtube.com/v/{$link}"
					  type="application/x-shockwave-flash" width="640" height="240" />
				</object>
EOF;
				}
				else
				{
					$output .=<<<EOF
							 <iframe width="800" height="450" src="https://www.youtube.com/embed/{$link}" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>

				 
EOF;
				}
			}
			elseif($post_url['host'] == "www.dailymotion.com" || $post_url['host'] == "dailymotion.com" || $host == "www.dailymotion.com" || $host == "dailymotion.com" )
			 {
				$bk = parse_url($url);
				// The part you want
				$url= $bk['path'];
				$parts = explode('/',$url);
				$parts = explode('_',$parts[2]);
				if($type == 3)
				{
				$output .=<<<EOF
				<object width="640" height="440">
					<param name="movie" value="http://www.dailymotion.com/swf/video/{$parts[0]}?background=493D27&foreground=E8D9AC&highlight=FFFFF0"></param>
					<param name="allowFullScreen" value="true"></param>
					<param name="allowScriptAccess" value="always"></param>
					<embed type="application/x-shockwave-flash" src="http://www.dailymotion.com/swf/video/{$parts[0]}?background=493D27&foreground=E8D9AC&highlight=FFFFF0" width="560" height="315" allowfullscreen="true" allowscriptaccess="always"></embed>
				</object>

EOF;
				}
				else
				{
						$output .=<<<EOF
			
				<object width="240" height="240">
					<param name="movie" value="http://www.dailymotion.com/swf/video/{$parts[0]}?background=493D27&foreground=E8D9AC&highlight=FFFFF0"></param>
					<param name="allowFullScreen" value="true"></param>
					<param name="allowScriptAccess" value="always"></param>
					<embed type="application/x-shockwave-flash" src="http://www.dailymotion.com/swf/video/{$parts[0]}?background=493D27&foreground=E8D9AC&highlight=FFFFF0" width="560" height="315" allowfullscreen="true" allowscriptaccess="always"></embed>
				</object>
EOF;
				}
			}
			else
			 {
				 
				 $output .=<<<EOF
			  <embed width="450" height="350" type="application/x-shockwave-flash" src="{$CMS->vars['public_url']}/player/player.swf" flashvars="skin={$CMS->vars['parent_domain']}/public/player/blueratio/blueratio.xml&amp;file={$CMS->vars['parent_domain']}/uploads/video/file/{$data['file_location']}&amp;playlistsize=100&amp;image={$CMS->vars['parent_domain']}/uploads/video/images/{$data['image_location']}&amp;logo={$CMS->vars['parent_domain']}/uploads/video/images/{$data['image_location ']}&amp;autostart=false&amp;shuffle=true&amp;repeat=list" allowfullscreen="true">
</embed>

  
			
EOF;
		     }
			 
			// Get image url 
			
			if($type == 1)
			{
				if($host == "www.youtube.com" || $host == "youtube.com" || $post_url['host'] == "www.youtube.com" || $post_url['host'] == "youtube.com")
				{
					$img = "http://img.youtube.com/vi/{$link}/0.jpg";
				}
				else
				{
					if($data['image_location'] != '')
					{
						$img = "{$CMS->vars['upload_url']}/video/images/{$data['image_location']}";
					
					}
					else
					{
						$img = "{$CMS->vars['root_domain']}/templates/images/no-image.jpg";
					}
				}
				return $img;
				
			}

		
			return $output;
		}
		

}

