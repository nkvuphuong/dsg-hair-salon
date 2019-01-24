<?php
/**
 * Created by PhpStorm.
 * User: Phuong3F
 * Date: 5/23/2017
 * Time: 9:44 AM
 */

namespace models;

use lib\date;
use lib\input;
use lib\db;
use \lib\template;

class custom_status
{
    /**
     * + session: hide/show by interval
     * + always: always show
     * @var array
     */
    static public $modes = ['always','session'];

    /**
     * + random: show random a record
     * + all: show all record
     * @var array
     */
    static public $types = ['all','random'];


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

    /**
     * Add new postion
     * @param array $data array input
     * @output boolean|array
     */
    static public function add($data = [])
    {
        global $CMS, $member, $DB;

        $status_time = time();
        $status_name =  $CMS->class->editor->input("status_name");
        $status_description = $CMS->class->editor->input("status_description");
        $status_display = intval($data['status_display']);
        $status_sort = intval($data['status_sort']);
        $status_is_step = intval($data['status_is_step']);
        $ord_status = $data['ord_status'] == "" ? -1 : intval($data['ord_status']);


        if(!$status_name)
        {
            $_SESSION['msg'] = $CMS->lang['incomplete_name']; return false;
        }

        if(!$ord_status or $ord_status == -1)
        {
            $_SESSION['msg'] = $CMS->lang['incomplete_order_status']; return false;
        }


        $sql = "INSERT INTO ".root_table."custom_status (status_name, status_description, status_display, status_time, status_sort, user_id, ord_status) VALUES ('{$status_name}', '{$status_description}', '{$status_display}', '{$status_time}','{$status_sort}','{$member['user_id']}', '{$ord_status}')";
        $DB->query($sql);
        $status_id = $DB->last_insert_id();

        // Get data
        $data_new = self::getInfo($status_id);

        //Clear cache
        $CMS->class->cache->mdelete('custom_status');
        $CMS->class->logs->key = "custom_status_{$status_id}";
        $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['added_custom_status_success']}: <strong>{$status_name}</strong>");

        return $data_new;
    }

    /**
     * Edit logo_positions
     * @param array $data
     * @return bool|array
     */
    static public function edit($data = [])
    {
        global $CMS, $member, $DB;

        $oldData = self::getInfo($data['id']);

        if(!$oldData)
        {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            return false;
        }

        $status_time_update = time();
        $status_name =  $CMS->class->editor->input("status_name");
        $status_description = $CMS->class->editor->input("status_description");
        $status_display = intval($data['status_display']);
        $status_sort = intval($data['status_sort']);
        $status_is_step = intval($data['status_is_step']);
        $ord_status = $data['ord_status'] == "" ? -1 : intval($data['ord_status']);


        if(!$status_name)
        {
            $_SESSION['msg'] = $CMS->lang['incomplete_name']; return false;
        }

        if(!$ord_status or $ord_status == -1)
        {
            $_SESSION['msg'] = $CMS->lang['incomplete_order_status']; return false;
        }

        $sql = "UPDATE ".root_table."custom_status SET status_name='{$status_name}', status_display='{$status_display}', status_description='{$status_description}', status_sort = '{$status_sort}', status_time_update='{$status_time_update}', status_is_step='{$status_is_step}', ord_status='{$ord_status}' WHERE status_id='{$CMS->input['id']}'";

        $DB->query($sql);

        //Clear cache
        $CMS->class->cache->mdelete('custom_status');

        $data_new = self::getInfo($CMS->input['id']);

        $CMS->class->logs->key = "custom_status_{$data_new['status_id']}";
        $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['updated_custom_status_success']}: <strong>{$data_new['status_name']}</strong>");

        return $data_new;
    }

    /**
     * Delete logo
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
        $DB->query("UPDATE ".root_table."custom_status SET status_deleted=1 WHERE status_id={$data['status_id']}");

        //Clear cache
        $CMS->class->cache->mdelete('custom_status');

        // Create log
        $CMS->class->logs->key = "custom_status_{$data['status_id']}";
        $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['custom_status_deleted']} <b>{$data['status_name']}</b>")."<br />";

        return true;
    }

    /**
     * Delete multi custom_status
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

                $DB->query("UPDATE ".root_table."custom_status SET status_deleted=1 WHERE status_id={$id}");

                $CMS->class->logs->key = "status_{$data['status_id']}";
                $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['custom_status_deleted']} <b>{$data['status_name']}</b>")."<br />";

                $deleted = 1;
            }
        }

        if ( $deleted == 0 )
        {
            //Clear cache
            $CMS->class->cache->mdelete('custom_status');

            $_SESSION["msg"] .= "{$CMS->lang['custom_status_delete_failed']}";
        }

        return true;
    }

    /**
     * Get infomation of a logo
     * @param int $id
     * @return array
     */

    static function getInfo($record_id=0, $field_return="*")
    {
        global $DB, $CMS;

        if(!$record_id) { $record_id = intval($CMS->input['id']); }

        if($record_id and $field_return)
        {
            // Count field
            $countField = count(explode(",", $field_return));
            // sqladd
            $sql_add = self::$sqlAdd;
            //Query
            $data = $DB->fetch_data("SELECT {$field_return} FROM ".root_table."custom_status WHERE status_deleted=0 AND status_id='{$record_id}' {$sql_add} LIMIT 1",'custom_status')[0];

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
     * Get infomation of record which just added
     * @param string $token_key
     * @return bool|array
     */
    public function insertedRecord($token_key = "")
    {
        global $CMS, $DB;

        if(!$token_key) return false;

        $sql = "SELECT * FROM ".root_table."custom_status WHERE status_token_key='{$token_key}' ORDER BY status_id DESC LIMIT 1";

        $sql = $DB->query($sql);

        return $DB->fetch_assoc($sql);
    }

    /**
     * Convert original record to display on edit form
     * @param array $data
     * @return array
     */
    static  public function editValue($data=[])
    {
        global $CMS;

        $data['status_sort'] = !isset($data['status_sort']) ? 1 : $data['status_sort'];
        $data['status_display'] = !isset($data['status_display']) ? 1 : $data['status_display'];
        $data['status_is_step'] = !isset($data['status_is_step']) ? 0 : $data['status_is_step'];

        return $data;
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


        $data['status_detail'] = "{$CMS->vars['root_domain']}/?site=custom_status&act=show&id={$data['status_id']}";

        if($CMS->permit['custom_status_read'] || $CMS->permit['custom_status_is_root'])
        {
            $data['status_name'] = "<a href='{$data['status_detail']}'>{$data['status_name']}</a>";
        }

        $data['status_time'] = $data['status_time'] ? date::format($data['status_time']) : '';


        $data['record_cnt'] = self::$record_cnt;

        if($CMS->vars['userTemp'][$data_bk['user_id']])
        {
            $data['userInfo'] = $CMS->vars['userTemp'][$data_bk['user_id']];
        }
        else
        {
            $CMS->vars['userTemp'][$data_bk['user_id']] = $data['userInfo'] = $CMS->user->get_info($data_bk['user_id']);
        }

        self::$record_cnt++;

        return $data;
    }

    /**
     * Get list custom_status
     * @param string $sql_add
     * @return array
     */
    static public function listing($sql_add = "")
    {
        global $CMS, $DB;

        // Update Arrange Data
        self::$arrangeData = trim("status_id,status_name,status_description,status_display,status_time");

        // Set default for Arrange
        $default_field = $CMS->input['order'] ? $CMS->input['order'] : "status_id";
        $default_order = $CMS->input['by'] ? $CMS->input['by'] : "DESC";

        // SQL Condition
        $sql_add .= " status_deleted=0 AND ";

        if(isset($CMS->input['keyword']) && $CMS->input['keyword']!=='')
        {
            $keyword = urldecode($CMS->input['keyword']);
            $sql_add .= " status_name LIKE '%{$keyword}%' AND ";
        }

        if(isset($CMS->input['status_display']) && $CMS->input['status_display'] !== '')
        {
            $status_display = intval($CMS->input['status_display']);
            $sql_add .= " status_display = {$status_display} AND ";
        }

        if(isset($CMS->input['status_name']) && $CMS->input['status_name']!=='')
        {
            $status_name = urldecode($CMS->input['status_name']);
            $sql_add .= " status_name LIKE '%{$status_name}%' AND ";
        }


        $sql = "SELECT * FROM ".root_table."custom_status WHERE {$sql_add} 1=1 ORDER BY {$default_field} {$default_order}";

        // Create SQL Query for listing Data
        list($CMS->show_page, $data) = $DB->fetch_listing($sql, self::$maxPage, self::$prefixPaging, self::$suffixPaging, $CMS->input['page'], 'custom_status');

        return $data;
    }


    /**
     * get all custom_status
     * @param string $sql_add
     * @return array
     */
    static function getStatusByOrdId($ord_status = 0)
    {
        global $CMS, $DB;

        if(!$ord_status) { return false;}

        $sql = "SELECT * FROM ".root_table."custom_status WHERE status_deleted=0 AND status_display=1 AND ord_status='{$ord_status}' ORDER BY status_sort ASC";

        return $DB->fetch_data($sql,'custom_status');
    }
}