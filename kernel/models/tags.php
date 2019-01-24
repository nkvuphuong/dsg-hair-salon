<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->tags = new class_tags;

class class_tags {

	public $CMS = "";
	
	/**
	 * @param @record_cnt
	 *		The order number of Data
	 */
	
	public $record_cnt = 0;
	
	/**
	 * @param @arrange_data
	 *		Arrange Data, using for re-order the listing
	 */
	
	public $arrange_data = "";
	
	/**
	 * @param @sql_query
	 *		The SQL Query for listing Data
	 */
	 
	public $sql_query = "";

	/**
	 * @param @sql_add
	 *		The additional SQL for $sql_query
	 */

	public $sql_add = "";
	
	/**
	 * @param $control
	 *		0 for no control, 1 for has control, DONT CHANGE the default value
	 */
	
	public $control = 0;
	
	/**
	 * @param $action_control
	 *		HTML action control
	 */
	
	public $action_control = "";
	
	/**
	 * @param $html
	 *		The templates
	 */
	 
	public $html;
	
	/**
	 * @param $per_page
	 *		Per page
	 */

	public $per_page = 2;
	
	/**
	 * @param $prefix_html
	 *		For page link
	 */

	public $prefix_html = "";
	public $suffix_html = "";
	
	/**
	 * @param $tags_project
	 *		Use for multiple projects
	 */

	public $tags_project = "";

    //===========================================================================
    //  LISTING DATA
    //===========================================================================

    public function loadhtml()
    {
        global $CMS;

        if ( !isset($this->html) )
        {
            $this->html = $CMS->class->template->load_template("skin_tags");
        }
    }

    public function html()
    {
        global $CMS, $DB, $member;

        $this->loadhtml();

        // Display Header
        $output .= $this->html->tags_header();

        if ( $DB->num_rows( $CMS->tags->sql_query ) > 0 )
        {
            while( $result = $DB->fetch_array( $CMS->tags->sql_query ) )
            {
                // Convert info
                $result = $CMS->tags->convertvalue($result);

                // Display Middle
                $output .= $this->html->tags_middle($result);
            }
        }
        else
        {
            // Display No data
            $output .= $this->html->tags_none();

            // No data
            $CMS->is_error = 1;
        }

        // Display
        $output .= $this->html->tags_footer();

        // If the page is giving no data
        if ( $CMS->input['page'] > 1 && $CMS->is_error == 1)
        {
            $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=tags");
        }

        return $output;
    }

    //===========================================================================
    //  AUTO RUN
    //===========================================================================

    public function auto_run()
    {
        global $CMS, $DB, $member;

        $this->loadhtml();

        //-----------------------------------------------------------
        // ACTION CONTROLLER
        //-----------------------------------------------------------

        if ( $CMS->class->cache->check("user_{$member['user_id']}_tags_controller_{$CMS->vars['default_language']}") )
        {
            $CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_tags_controller_{$CMS->vars['default_language']}");
        }
        else
        {
            $data = "";

            // Check permission to Delete
            if ( $CMS->permit["tags_delete"] == true )
            {
                $data .= "<option value='delete_all'>{$CMS->lang['tags_action_delete']}</option>";
                $this->control = 1;
            }

            $data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

            $CMS->class->cache->save("user_{$member['user_id']}_tags_controller_{$CMS->vars['default_language']}", $data);
            $CMS->vars['action_controller'] = $data;
        }

        if ( $CMS->vars['action_controller']  OR $CMS->permit["tags_search"] == 1 )
        {
            $this->action_control = $this->html->tags_control();
        }


    }

    //===========================================================================
    //  DATA
    //===========================================================================

    public function defaultvalue($data)
    {
        global $CMS;

        return $data;
    }

    public function convertvalue($data, $type = 0, $project = "")
    {
        global $CMS, $DB;

        // Replace search content
        $data = $CMS->class->search->convertvalue($data);

        $this->record_cnt++;

        return $data;
    }

    public function searchvalue($data)
    {
        global $CMS, $DB;

        return $data;
    }


    public function add($data = [], $module="news")
    {
        global $CMS, $DB;

        $data = $data ? $data : $CMS->input;

        if(!$tag = $this->checkExisted($data['tag_name']))
        {
            $tag_name = trim($data['tag_name']);

            if($tag_name === "") return false;

            $sql = "INSERT INTO ".root_table."tags(tag_name, tag_module) VALUES('{$tag_name}', '{$module}')";

            $DB->query($sql);

            $tag = $this->get_info($DB->last_insert_id());
        }

        return $tag;
    }

    public function checkExisted($tag_name = "")
    {
        global $CMS, $DB;

        $sql = "SELECT * FROM ".root_table."tags WHERE tag_name='{$tag_name}' ORDER BY tag_id LIMIT 0,1";

        $sql = $DB->query($sql);

        return $DB->fetch_assoc($sql);
    }

    public function get_info($id = 0)
    {
        global $CMS, $DB;

        if(is_numeric($id))
        {
            $sql_add = " tag_id='{$id}' AND ";
        }
        else
        {
            $sql_add = " tag_name='{$id}' AND ";
        }

        $sql = "SELECT * FROM ".root_table."tags WHERE {$sql_add} 1=1 ORDER BY tag_id LIMIT 0,1";

        $sql = $DB->query($sql);

        return $DB->fetch_assoc($sql);
    }

    public function getTagsByKeyword($keyword = '', $module="news")
    {
        global $DB;

        $keyword = trim($keyword);

        if(!$keyword) return [];

        $sql = "SELECT * FROM ".root_table."tags WHERE tag_name LIKE '%{$keyword}%' AND tag_module='{$module}' ORDER BY tag_name LIMIT 0,10";

        $sql = $DB->query($sql);

        $data = [];

        while($result = $DB->fetch_assoc($sql))
        {
            $data[] = $result;
        }

        return $data;
    }

    public function addTags($data = [], $module="news")
    {
        $return = [];

        foreach ($data as $tag)
        {
            //check existed
            if (!$this->checkExisted($tag))
            {
                //Add tag
                $this->add(['tag_name' => $tag], $module);
            }

            $return[] = $tag;
        }

        return $return;
    }

    public function updateCount($data = [])
    {
        global $CMS, $DB;

        foreach ($data as $tag)
        {
            //count news
            $sql_count = "SELECT count(news_id) cnt FROM ".root_table."news WHERE news_deleted=0 AND news_active=1 AND news_tags LIKE '%\"{$tag}\"%'";

            $sql_count = $DB->fetch_assoc($DB->query($sql_count));

            $count_news = intval($sql_count['cnt']);

            $sql_update = "UPDATE ".root_table."tags SET tag_count={$count_news} WHERE tag_name = '{$tag}'";

            $DB->query($sql_update);
        }
    }

    static function getData($limit = 10)
    {
        global $DB;

        $limit = intval($limit);

        $sql = "SELECT tag_name name, tag_count cnt FROM ".root_table."tags WHERE tag_count != 0 ORDER BY cnt LIMIT 0,{$limit}";

        $sql = $DB->query($sql);

        $data = [];

        while($result = $DB->fetch_assoc($sql))
        {
            $data[] = $result;
        }

        return $data;
    }
}

?>