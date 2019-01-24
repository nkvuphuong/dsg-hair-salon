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

class logo_positions
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

        $time = time();
        $pos_token_key = $CMS->class->random->md5($CMS->class->random->character(16));

        $pos_name = $data['pos_name'];
        $pos_key = $data['pos_key'];
        $pos_mode = $data['pos_mode'];
        $pos_interval = intval($data['pos_interval']);
        $pos_type = $data['pos_type'];
        $pos_width = intval($data['pos_width']);
        $pos_height = intval($data['pos_height']);
        $pos_desc = $data['pos_desc'];

        $pos_status = 1;

        if(!$pos_name)
        {
            $_SESSION['msg'] = $CMS->lang['incomplete_name'];
            return false;
        }

        if(!$pos_key)
        {
            $_SESSION['msg'] = $CMS->lang['incomplete_key'];
            return false;
        }

        if(!$pos_mode)
        {
            $_SESSION['msg'] = $CMS->lang['incomplete_mode'];
            return false;
        }

        if(!$pos_type)
        {
            $_SESSION['msg'] = $CMS->lang['incomplete_type'];
            return false;
        }

        if(self::checkExisted('pos_key', $pos_key))
        {
            $_SESSION['msg'] = $CMS->lang['key_existed'];
            return false;
        }

        $sql = "INSERT INTO ".root_table."logo_positions(pos_name, pos_key, pos_mode, pos_type, pos_interval, pos_height, pos_width, pos_desc, pos_status, pos_token_key, pos_time, user_id) VALUES('{$pos_name}', '{$pos_key}', '{$pos_mode}', '{$pos_type}', '{$pos_interval}', '{$pos_height}', '{$pos_width}', '{$pos_desc}', '{$pos_status}', '{$pos_token_key}', '{$time}', '{$member['user_id']}')";

        $DB->query($sql);

        //Clear cache
        $CMS->class->cache->mdelete('logo_positions');

        $insertedRecord = self::insertedRecord($pos_token_key); //Get inserted record

        $CMS->class->logs->key = "pos_{$insertedRecord['pos_id']}";
        $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['added_pos_success']}: <strong>{$insertedRecord['pos_name']}</strong>");

        return $insertedRecord;
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

        $pos_name = $data['pos_name'];
        $pos_key = $data['pos_key'];
        $pos_mode = $data['pos_mode'];
        $pos_interval = intval($data['pos_interval']);
        $pos_type = $data['pos_type'];
        $pos_width = intval($data['pos_width']);
        $pos_height = intval($data['pos_height']);
        $pos_desc = $data['pos_desc'];

        if(!$pos_name)
        {
            $_SESSION['msg'] = $CMS->lang['incomplete_name'];
            return false;
        }

        if(!$pos_key)
        {
            $_SESSION['msg'] = $CMS->lang['incomplete_key'];
            return false;
        }

        if(!$pos_mode)
        {
            $_SESSION['msg'] = $CMS->lang['incomplete_mode'];
            return false;
        }

        if(!$pos_type)
        {
            $_SESSION['msg'] = $CMS->lang['incomplete_type'];
            return false;
        }

        if(self::checkExisted('pos_key', $pos_key, $oldData['pos_key']))
        {
            $_SESSION['msg'] = $CMS->lang['key_existed'];
            return false;
        }

        $sql = "UPDATE ".root_table."logo_positions SET pos_name='{$pos_name}', pos_key='{$pos_key}', pos_mode='{$pos_mode}', pos_interval='{$pos_interval}', pos_type='{$pos_type}', pos_width='{$pos_width}', pos_height='{$pos_height}', pos_desc='{$pos_desc}' WHERE pos_id='{$CMS->input['id']}'";

        $DB->query($sql);

        //Clear cache
        $CMS->class->cache->mdelete('logo_positions');

        $updatedRecord = self::getInfo($CMS->input['id']);

        $CMS->class->logs->key = "pos_{$updatedRecord['pos_id']}";
        $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['updated_pos_success']}: <strong>{$updatedRecord['pos_name']}</strong>");

        return $updatedRecord;
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
        $DB->query("UPDATE ".root_table."logo_positions SET pos_deleted=1 WHERE pos_id={$data['pos_id']}");

        //Clear cache
        $CMS->class->cache->mdelete('logo_positions');

        @unlink("{$CMS->vars['upload_dir']}/{$data['pos_src']}");

        // Create log
        $CMS->class->logs->key = "pos_{$data['pos_id']}";
        $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['pos_deleted']} <b>{$data['pos_name']}</b>")."<br />";

        return true;
    }

    /**
     * Delete multi logo_positions
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

                $DB->query("UPDATE ".root_table."logo_positions SET pos_deleted=1 WHERE pos_id={$id}");

                @unlink("{$CMS->vars['upload_dir']}/{$data['pos_src']}");

                $CMS->class->logs->key = "pos_{$data['pos_id']}";
                $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['pos_deleted']} <b>{$data['pos_name']}</b>")."<br />";

                $deleted = 1;
            }
        }

        if ( $deleted == 0 )
        {
            //Clear cache
            $CMS->class->cache->mdelete('logo_positions');

            $_SESSION["msg"] .= "{$CMS->lang['pos_delete_failed']}";
        }

        return true;
    }

    /**
     * Get infomation of a logo
     * @param int $id
     * @return array
     */
    static public function getInfo($id = 0)
    {
        global $CMS, $DB;

        if(!$id)
        {
            $id = $CMS->input['id'];
        }

        if(is_numeric($id))
        {
            $id = intval($id);
            $sql_add = " pos_id='{$id}' AND ";
        }
        else
        {
            $sql_add = " pos_token_key='{$id}' AND ";
        }

        $sql = "SELECT * FROM ".root_table."logo_positions WHERE {$sql_add} pos_deleted=0 ORDER BY pos_id LIMIT 0,1";

        return $DB->fetch_data($sql,'logo_positions')[0];
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

        $sql = "SELECT * FROM ".root_table."logo_positions WHERE pos_token_key='{$token_key}' ORDER BY pos_id DESC LIMIT 1";

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

        $data['pos_mode'] = $CMS->lang['pos_mode_'.$data_bk['pos_mode']];
        $data['pos_type'] = $CMS->lang['pos_type_'.$data_bk['pos_type']];

        $data['pos_detail'] = "{$CMS->vars['root_domain']}/?site=logo_positions&act=show&id={$data['pos_id']}";

        if($CMS->permit['logo_positions_read'] || $CMS->permit['logo_positions_is_root'])
        {
            $data['pos_name'] = "<a href='{$data['pos_detail']}'>{$data['pos_name']}</a>";
        }

        $data['pos_time'] = $data['pos_time'] ? date::format($data['pos_time']) : '';


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
     * Get list logo_positions
     * @param string $sql_add
     * @return array
     */
    static public function listing($sql_add = "")
    {
        global $CMS, $DB;

        // Update Arrange Data
        self::$arrangeData = trim("pos_id,pos_key,pos_name,pos_width,pos_height,pos_time");

        // Set default for Arrange
        $default_field = $CMS->input['order'] ? $CMS->input['order'] : "pos_id";
        $default_order = $CMS->input['by'] ? $CMS->input['by'] : "desc";

        // SQL Condition
        $sql_add .= " pos_deleted=0 AND ";

        if(isset($CMS->input['keyword']) && $CMS->input['keyword']!=='')
        {
            $keyword = urldecode($CMS->input['keyword']);
            $sql_add .= " (pos_name LIKE '%{$keyword}%' OR pos_desc LIKE '%{$keyword}%' OR pos_key = '{$keyword}') AND ";
        }

        if(isset($CMS->input['pos_status']) && $CMS->input['pos_status'] !== '')
        {
            $pos_status = intval($CMS->input['pos_status']);
            $sql_add .= " pos_status = {$pos_status} AND ";
        }

        if(isset($CMS->input['pos_name']) && $CMS->input['pos_name']!=='')
        {
            $pos_name = urldecode($CMS->input['pos_name']);
            $sql_add .= " pos_name LIKE '%{$pos_name}%' AND ";
        }

        if(isset($CMS->input['pos_key']) && $CMS->input['pos_key']!=='')
        {
            $pos_key = urldecode($CMS->input['pos_key']);
            $sql_add .= " pos_key LIKE '%{$pos_key}%' AND ";
        }

        if(isset($CMS->input['pos_mode']) && $CMS->input['pos_mode']!=='')
        {
            $pos_mode = urldecode($CMS->input['pos_mode']);
            $sql_add .= " pos_mode = '{$pos_mode}' AND ";
        }

        if(isset($CMS->input['pos_type']) && $CMS->input['pos_type'] !== '')
        {
            $pos_type = urldecode($CMS->input['pos_type']);
            $sql_add .= " pos_type = '{$pos_type}' AND ";
        }

        $sql = "SELECT * FROM ".root_table."logo_positions WHERE {$sql_add} 1=1 ORDER BY {$default_field} {$default_order}";

        // Create SQL Query for listing Data
        list($CMS->show_page, $data) = $DB->fetch_listing($sql, self::$maxPage, self::$prefixPaging, self::$suffixPaging, $CMS->input['page'], 'logo_positions');

        return $data;
    }

    /**
     * Check existed record
     * @param string $field
     * @param mixed $val
     * @param mixed $except
     * @return integer
     */
    function checkExisted($field, $val, $except=null)
    {
        global $CMS, $DB;

        if($except !== null)
        {
            $sql_add = " {$field} != '{$except}' AND ";
        }

        $sql = "SELECT count(0) cnt FROM ".root_table."logo_positions WHERE {$sql_add} {$field} = '{$val}' AND pos_deleted=0";

        return $DB->fetch_data($sql,'logo_positions')[0]['cnt'];
    }

    /**
     * get all position
     * @param string $sql_add
     * @return array
     */
    static function getAll($sql_add = "")
    {
        global $CMS, $DB;

        $sql = "SELECT * FROM ".root_table."logo_positions WHERE {$sql_add} pos_deleted=0 ORDER BY pos_name";

        return $DB->fetch_data($sql,'logo_positions');
    }
}