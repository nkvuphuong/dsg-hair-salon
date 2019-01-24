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

class user_group
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
        $userg_token_key = $CMS->class->random->md5($CMS->class->random->character(16));

        $userg_title = $data["userg_title"];
        $userg_is_admin = $data["userg_is_admin"];
        $userg_is_root = $data["userg_is_root"];

        if(!$userg_title)
        {
            $_SESSION['msg'] = $CMS->lang['incomplete_name'];
            return false;
        }

        // Checking Group permission to anti escalate
        if ( $CMS->user->check_permission($userg_is_root, $userg_is_admin) == false )
        {
            return false;
        }

        $permission = $CMS->user->get_permission();

        $sql = "INSERT INTO ".root_table."user_group (userg_title, userg_is_admin, userg_is_root, userg_permission, userg_token_key) VALUES ('{$userg_title}', '{$userg_is_admin}', '{$userg_is_root}', '{$permission}','{$userg_token_key}')";

        $DB->query($sql);

        $CMS->class->cache->mdelete('user_group');

        $insertedRecord = self::insertedRecord($userg_token_key); //Get inserted record

        //Update commission
        $CMS->user->update_commission('user_group', $_POST['commission_data'], $insertedRecord['userg_id']);

        $CMS->class->logs->key = "user_group_{$insertedRecord['userg_id']}";
        $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['group_added']}: <strong>{$insertedRecord['userg_title']}</strong>");

        return $insertedRecord;
    }

    /**
     * Edit user_group
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

        $userg_title = $data["userg_title"];
        $userg_is_admin = $data["userg_is_admin"];
        $userg_is_root = $data["userg_is_root"];

        if(!$userg_title)
        {
            $_SESSION['msg'] = $CMS->lang['incomplete_name'];
            return false;
        }

        if(self::checkExisted('userg_title', $userg_key, $oldData['userg_key']))
        {
            $_SESSION['msg'] = $CMS->lang['userg_title_existed'];
            return false;
        }

        // Checking Group permission to anti escalate
        if ( $CMS->user->check_permission($userg_is_root, $userg_is_admin) == false )
        {
            return false;
        }

        $permission = $CMS->user->get_permission();

        $sql = "UPDATE ".root_table."user_group SET userg_title='{$userg_title}', userg_is_admin='{$userg_is_admin}', userg_is_root='{$userg_is_root}', userg_permission='{$permission}' WHERE userg_id='{$CMS->input['id']}'";

        $DB->query($sql);

        $CMS->class->cache->mdelete('user_group');

        $updatedRecord = self::getInfo($CMS->input['id']);

        $CMS->class->logs->key = "user_group_{$updatedRecord['userg_id']}";
        $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['group_edited']}: <strong>{$updatedRecord['userg_title']}</strong>");

        $apply_permission = intval($data['apply_permission']);

        $apply_commission = intval($data['apply_commission']);

        if ( $apply_permission )
        {
            $CMS->class->logs->insert("{$CMS->lang['updated_permission']} <b>{$userg_name}</b>");

            $DB->query("UPDATE ".root_table."user SET user_permission='{$permission}' WHERE userg_id='{$updatedRecord['userg_id']}'");

            $CMS->class->cache->mdelete("user");

            // Delete cache user
            $sql = $DB->query("SELECT * FROM ".root_table."user WHERE userg_id='{$id}'");

            while ( $data = $DB->fetch_array( $sql ) )
            {
                $CMS->class->cache->delete("usertask_{$data['user_id']}");
            }
        }

        //Update commission
        $CMS->user->update_commission('user_group', $_POST['commission_data'], $CMS->input['id'], $apply_commission);

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
        $DB->query("UPDATE ".root_table."user_group SET userg_deleted=1 WHERE userg_id={$data['userg_id']}");

        $CMS->class->cache->mdelete('user_group');

        @unlink("{$CMS->vars['upload_dir']}/{$data['userg_src']}");

        // Create log
        $CMS->class->logs->key = "user_group_{$data['userg_id']}";
        $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['userg_deleted']} <b>{$data['userg_title']}</b>")."<br />";

        return true;
    }

    /**
     * Delete multi user_group
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

                $DB->query("UPDATE ".root_table."user_group SET userg_deleted=1 WHERE userg_id={$id}");

                @unlink("{$CMS->vars['upload_dir']}/{$data['userg_src']}");

                $CMS->class->logs->key = "user_group_{$data['userg_id']}";
                $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['userg_deleted']} <b>{$data['userg_title']}</b>")."<br />";

                $deleted = 1;
            }
        }

        if ( $deleted == 0 )
        {
            $CMS->class->cache->mdelete('user_group');

            $_SESSION["msg"] .= "{$CMS->lang['userg_delete_failed']}";
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
            $sql_add = " userg_id='{$id}' AND ";
        }
        else
        {
            $sql_add = " userg_token_key='{$id}' AND ";
        }

        $sql = "SELECT * FROM ".root_table."user_group WHERE {$sql_add} userg_deleted=0 ORDER BY userg_id LIMIT 0,1";

        return $DB->fetch_data($sql, 'user_group')[0];
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

        $sql = "SELECT * FROM ".root_table."user_group WHERE userg_token_key='{$token_key}' ORDER BY userg_id DESC LIMIT 1";

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

        $data['userg_is_root'] = $data['userg_is_root'] ? '<i style="color:green" class="fa fa-check-circle" aria-hidden="true"></i>' : '<i class="fa fa-ban" aria-hidden="true"></i>';
        $data['userg_is_admin'] = $data['userg_is_admin'] ? '<i style="color:green" class="fa fa-check-circle" aria-hidden="true"></i>' : '<i class="fa fa-ban" aria-hidden="true"></i>';
        $data['count_user'] = self::countUser($data_bk['userg_id']);

        self::$record_cnt++;

        return $data;
    }

    /**
     * Get list user_group
     * @param string $sql_add
     * @return array
     */
    static public function listing($sql_add = "")
    {
        global $CMS, $DB;

        // Update Arrange Data
        self::$arrangeData = trim("userg_id");

        // Set default for Arrange
        $default_field = $CMS->input['order'] ? $CMS->input['order'] : "userg_id";
        $default_order = $CMS->input['by'] ? $CMS->input['by'] : "desc";

        // SQL Condition
        $sql_add .= " userg_deleted=0 AND ";

        if(isset($CMS->input['keyword']) && $CMS->input['keyword']!=='')
        {
            $keyword = urldecode($CMS->input['keyword']);
            $sql_add .= " (userg_title LIKE '%{$keyword}%') AND ";
        }

        if(isset($CMS->input['userg_is_root']) && $CMS->input['userg_is_root'] !== '')
        {
            $userg_is_root = intval($CMS->input['userg_is_root']);
            $sql_add .= " userg_is_root = {$userg_is_root} AND ";
        }

        if(isset($CMS->input['userg_is_admin']) && $CMS->input['userg_is_admin']!=='')
        {
            $userg_is_admin = intval($CMS->input['userg_is_admin']);
            $sql_add .= " userg_title = {$userg_is_admin} AND ";
        }

        if(isset($CMS->input['userg_leader_id']) && $CMS->input['userg_leader_id']!=='')
        {
            $userg_leader_id = intval($CMS->input['userg_leader_id']);
            $sql_add .= " userg_leader_id = {$userg_leader_id} AND ";
        }

        $sql = "SELECT * FROM ".root_table."user_group WHERE {$sql_add} 1=1 ORDER BY {$default_field} {$default_order}";

        // Create SQL Query for listing Data
        list($CMS->show_page, $data) = $DB->fetch_listing($sql, self::$maxPage, self::$prefixPaging, self::$suffixPaging, $CMS->input['page'], user_group);

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

        $sql = "SELECT COUNT(0) cnt FROM ".root_table."user_group WHERE {$sql_add} {$field} = '{$val}' AND userg_deleted=0";

        return $DB->fetch_data($sql,'user_group')[0]['cnt'];
    }

    /**
     * get all position
     * @param string $sql_add
     * @return array
     */
    static function getAll($sql_add = "")
    {
        global $CMS, $DB;

        $sql = "SELECT * FROM ".root_table."user_group WHERE {$sql_add} userg_deleted=0 ORDER BY userg_title";

        $results = $DB->fetch_data($sql, 'user_group');

        $data = [];

        if(!$results) return $data;

        foreach ($results as $re)
        {
            $data[$re['userg_id']] = $re;
        }

        return $data;
    }

    static function countUser($userg_id = 0)
    {
        global $CMS, $DB;

        if(!$userg_id) return 0;

        $sql = "SELECT COUNT(userg_id) as num FROM ".root_table."user WHERE userg_id = {$userg_id} AND user_deleted <> 1";

        return $DB->fetch_data($sql,'user')[0]['num'];
    }

    public static function getCommission($data)
    {
        global $CMS, $DB;

        if(is_numeric($data)) //Nếu truyền id thì lấy từ DB
        {

            $userg_id = $data*1;
            $sql = "SELECT userg_commission_data FROM ".root_table."user_group WHERE userg_id = '{$userg_id}' LIMIT 1";
            $results = $DB->fetch_data($sql, 'user_group');
            $commission_data = isset($results[0]['userg_commission_data']) ? $results[0]['userg_commission_data'] : null;
            $commission_data = !empty($commission_data) ? \lib\input::jsonDecode($commission_data) : [];
        }
        else if(is_array($data))
        {
            if(!empty($data['product_commission_type'])) //lấy từ input
            {
                //reformat product commission data
                $tmp = [];
                foreach($data['product_commission_type'] as $input_key => $type)
                {
                    $value = $data['product_commission_value'][$input_key];
                    $ptype = $data['product_type'][$input_key];
                    $pname = $data['product_name'][$input_key];
                    $tmp['id_'.$input_key] = [
                        'product_id' => $input_key,
                        'product_name' => $pname,
                        'product_type' => $ptype,
                        'product_commission_type' => $type,
                        'product_commission_value' => $value,
                    ];
                }

                $commission_data = $tmp;
            }
            else
            {
                $commission_data = $data;
            }
        }
        else
        {
            $commission_data = [];
        }


        $product_commission_data = $CMS->product->commission_listing();

        //reformat product commission data
        $tmp = [];
        foreach($product_commission_data as $item)
        {
            $tmp['id_'.$item['product_id']] = $item;
        }

        $product_commission_data = $tmp;

        $commission_data =  array_merge($product_commission_data,$commission_data);

        //Check name product
        foreach($commission_data as $key => $item)
        {
            if(!isset($item['product_name']))
            {
                if(isset($product_commission_data[$key]['product_name']))
                {
                    $item['product_name'] = $product_commission_data[$key]['product_name'];
                }
                else
                {
                    $item['product_name'] = $CMS->product->getInfo($item['product_id'],'product_name');
                }
            }

            if(!isset($item['product_type']))
            {
                if(isset($product_commission_data[$key]['product_type']))
                {
                    $item['product_type'] = $product_commission_data[$key]['product_type'];
                }
                else
                {
                    $item['product_type'] = $CMS->product->getInfo($item['product_id'],'product_type');
                }
            }
            $commission_data[$key] = $item;
        }

        return $commission_data;
    }
}