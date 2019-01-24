<?php
namespace models;

use \core\ezy;
use \lib\date;

class contact
{
    /**
     * @param $record_cnt
     *		The order number of Data
     */
    static public $record_cnt = 0;

    /**
     * @param @$arrangeData
     *		The SQL Query for listing Data
     */
    static public $arrangeData;

    /**
     * @param $sqlAdd
     *		The additional SQL for $sql_query
     */
    static public $sqlAdd;

    /**
     * @param $sqlQuery
     *		The SQL Query for listing Data
     */
    static public $sqlQuery;

    /**
     * @param $maxPage
     *		Number of records on per page
     */
    static public $maxPage = 20;

    /**
     * @param $prefixPaging
     *		Prefix for paging url
     */
    static public $prefixPaging = '';

    /**
     * @param $prefixPaging
     *		Suffix for paging url
     */
    static public $suffixPaging = '';

    static public $locateData = [
        'vn' => "Viet Nam",
        'us' => "USA",
    ];

    /**
     * Get list contact
     * @param string $sql_add
     * @param integer $disabled_paging
     * @return array
     */
    static public function listing($sql_add = "", $disabled_paging = 0)
    {
        global $CMS, $DB;

        // Update Arrange Data
        self::$arrangeData = trim("con_id, con_time, con_update_time");

        // Set default for Arrange
        $default_field = $CMS->input['order'] ? $CMS->input['order'] : "con_id";
        $default_order = $CMS->input['by'] ? $CMS->input['by'] : "desc";

        // SQL Condition
        $sql_add .= " con_deleted = 0 AND ";

        // SQL Add
        $sql_add .= self::getSqlAdd($CMS->input);

        $sql = "SELECT * FROM ".root_table."contact WHERE {$sql_add} 1=1 ORDER BY {$default_field} {$default_order}";

        // Create SQL Query for listing Data
        if ( $disabled_paging == 1 ) 
        {
            $CMS->show_page = "";
            return $DB->fetch_data($sql,'contact');
        } 
        else 
        {
            list($CMS->show_page, $results) = $DB->fetch_listing($sql, self::$maxPage, self::$prefixPaging, self::$suffixPaging, $CMS->input['page'], 'contact');

            return $results;
        }
    }

    /**
     * Get sql string for listing
     * @param array input
     * @return string
     */
    static function getSqlAdd( $data = [] )
    {
        global $CMS;

        $sql_add = '';

        if( isset($data['keyword']) AND $data['keyword'] !== '' )
        {
            $keyword = urldecode($data['keyword']);
            $sql_add .= " ( con_subject LIKE '%{$keyword}%' OR con_name LIKE '%{$keyword}%' OR con_email LIKE '%{$keyword}%' ) AND ";
        }

        if( isset($data['con_subject']) AND $data['con_subject'] !== '' )
        {
            $con_subject = urldecode($data['con_subject']);
            $sql_add .= " con_subject LIKE '%{$con_subject}%' AND ";
        }

        if( isset($data['con_name']) AND $data['con_name'] !== '' )
        {
            $con_name = urldecode($data['con_name']);
            $sql_add .= " con_name LIKE '%{$con_name}%' AND ";
        }

        if( isset($data['con_email']) AND $data['con_email'] !== '' )
        {
            $con_email = urldecode($data['con_email']);
            $sql_add .= " con_email LIKE '%{$con_email}%' AND ";
        }

        if( isset($data['con_type']) AND $data['con_type'] !== '' )
        {
            $con_type = intval($data['con_type']);
            $sql_add .= " con_type = {$con_type} AND ";
        }

        return $sql_add;
    }

    /**
     * Convert original record to show
     * @param array $data
     * @return array
     */
    static  public function convertValue($data=[])
    {
        global $CMS;

        $data['data_bk'] = $data_bk = $data;

        // Replace search content
        $data = $CMS->class->search->convertvalue($data);

        if( $CMS->permit['contact_read'] || $CMS->permit['contact_is_root'] )
        {
            $data['con_subject'] = "<a style='display: inline-block' href='{$CMS->vars['root_domain']}/?site=contact&act=show&id={$data_bk['con_id']}'>{$data_bk['con_subject']}</a>";
        }

        $data['con_time'] = $data_bk['con_time'] ? date::format($data['con_time']) : '';
        $data['con_update_time'] = $data_bk['con_update_time'] ? date::format($data['con_update_time']) : '';
        $data['con_status'] = $CMS->lang['con_status_'.$data_bk['con_status']];
        $data['con_status_color']=  $CMS->lang['con_status_color_'.$data_bk['con_status']];

        $data['record_cnt'] = self::$record_cnt;

        self::$record_cnt++;

        return $data;
    }

    /**
     * Delete contact
     * @return bool
     */
    static public function delete()
    {
        global $CMS, $DB;

        // Get info
        $data = self::getInfo();

        // Check existing
        if ( ! $data ) { return false; }

        // Update info
        $DB->query("UPDATE ".root_table."contact SET con_deleted = 1 WHERE con_id={$data['con_id']}");

        //Clear cache
        $CMS->class->cache->mdelete('contact');

        // Create log
        $CMS->class->logs->key = "contact_{$data['con_id']}";
        $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['con_deleted']} <b>#{$data['con_id']}: {$data['con_name']}</b>")."<br />";

        return true;
    }

    /**
     * Delete multi contact
     * @return bool
     */
    static public function mdelete()
    {
        global $CMS, $DB;

        $deleted = 0;

        $_SESSION["msg"] .= "";

        for ( $i = 0; $i < intval( $CMS->input["data_cnt"] ); $i++ )
        {
            $id = intval( $CMS->input["id_{$i}"] );

            if ( $id )
            {
                $data = self::getInfo($id);

                $DB->query("UPDATE ".root_table."contact SET con_deleted = 1 WHERE con_id={$data['con_id']}");

                $CMS->class->logs->key = "contact_{$data['con_id']}";
                $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['con_deleted']} <b>#{$data['con_id']}: {$data['con_name']}</b>")."<br />";

                $deleted = 1;
            }
        }

        if ( $deleted == 0 )
        {
            //Clear cache
            $CMS->class->cache->mdelete('contact');

            $_SESSION["msg"] .= "{$CMS->lang['con_delete_failed']}";
        }

        return true;
    }

    /**
     * Get infomation of a contact
     * @param int $id
     * @return array
     */
    static public function getInfo($id = 0, $sql_add='')
    {
        global $CMS, $DB;

        if(!$id)
        {
            $id = $CMS->input['id'];
        }

        if(!$id) return false;

        $id = intval($id);
        $sql_add .= " con_id='{$id}' AND ";

        $sql = "SELECT * FROM ".root_table."contact WHERE {$sql_add} con_deleted=0 ORDER BY con_id LIMIT 0,1";

        return $DB->fetch_data($sql, 'contact')[0];
    }


    /**
     * Update contact viewed
     * @param id
     * @return bool
     */
    static public function updateViewed( $id = 0 )
    {
        global $CMS, $member, $DB;

        $id = intval($id);
        if(!$id) return false;
        $con_update_time = time();

        // Update info
        $DB->query("UPDATE ".root_table."contact SET con_status = 1, con_update_time = '{$con_update_time}' WHERE con_id={$id}");

        //Clear cache
        $CMS->class->cache->mdelete('contact');

        // Create log
        $CMS->class->logs->key = "contact_{$id}";
        $CMS->class->logs->insert("{$CMS->lang['con_updated_status']} <b>#{$id}</b>")."<br />";

        return true;
    }
}