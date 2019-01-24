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

class seo
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

    /**
     * Add new postion
     * @param array $data array input
     * @output boolean|array
     */
    static public function add($data = [])
    {
        global $CMS, $member, $DB;

        $time = time();
        $random = rand(0,100);
        $seo_token_key = $CMS->class->random->md5($CMS->class->random->character(16));

        $seo_url = self::clearUrl($data['seo_url']);
        $seo_title = $data['seo_title'];
        $seo_keywords = $data['seo_keywords'];
        $seo_description = $data['seo_description'];

        $seo_og_title = $data['seo_og_title'];
        $seo_og_description = $data['seo_og_description'];

        $seo_dc_title = $data['seo_dc_title'];
        $seo_dc_subject = $data['seo_dc_subject'];
        $seo_dc_description = $data['seo_dc_description'];

        $seo_h1_content = $data['seo_h1_content'];

        if(!$seo_url)
        {
            $_SESSION['msg'] = $CMS->lang['incomplete_url'];
            return false;
        }

        /**
         * Upload file
         */
        $uploadFile = $_FILES['seo_og_image'];

        $dir = "seo";

        if($uploadFile['error'] != 4)
        {
            if($uploadFile['error'] > 0) //Check file error
            {
                $_SESSION['msg'] = "{$CMS->lang['error_upload_image']} ({$uploadFile['error']})";
                return false;
            }
            else
            {
                $seo_og_image = "{$dir}/{$CMS->class->image->check_folder_img($dir,"",1)}/{$member['user_id']}_{$time}_{$random}_{$CMS->class->seo->remove_vietnamese($uploadFile['name'])}";
                $path = "{$CMS->vars['upload_dir']}/{$seo_og_image}";
                @copy($uploadFile['tmp_name'], $path) or die ("Could not be upload.");
            }
        }


        $sql = "INSERT INTO ".root_table."seo(seo_url, seo_title, seo_keywords, seo_description, seo_token_key, seo_time, user_id, seo_og_title, seo_og_image, seo_og_description, seo_dc_title, seo_dc_subject, seo_dc_description,seo_h1_content) VALUES('{$seo_url}', '{$seo_title}', '{$seo_keywords}', '{$seo_description}', '{$seo_token_key}', '{$time}', '{$member['user_id']}', '{$seo_og_title}', '{$seo_og_image}', '{$seo_og_description}', '{$seo_dc_title}', '{$seo_dc_subject}', '{$seo_dc_description}', '{$seo_h1_content}')";

        $DB->query($sql);

        //Clear cache
        $CMS->class->cache->mdelete('seo');

        $insertedRecord = self::insertedRecord($seo_token_key); //Get inserted record

        $CMS->class->logs->key = "seo_{$insertedRecord['seo_id']}";
        $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['added_seo_success']}: <strong>{$insertedRecord['seo_url']}</strong>");

        return $insertedRecord;
    }

    /**
     * Edit seo
     * @param array $data
     * @return bool|array
     */
    static public function edit($data = [])
    {
        global $CMS, $member, $DB;

        $time = time();
        $random = rand(0,100);

        $oldData = self::getInfo($data['id']);

        if(!$oldData)
        {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            return false;
        }

        $seo_url = self::clearUrl($data['seo_url']);
        $seo_title = $data['seo_title'];
        $seo_keywords = $data['seo_keywords'];
        $seo_description = $data['seo_description'];

        $seo_og_title = $data['seo_og_title'];
        $seo_og_description = $data['seo_og_description'];

        $seo_dc_title = $data['seo_dc_title'];
        $seo_dc_subject = $data['seo_dc_subject'];
        $seo_dc_description = $data['seo_dc_description'];

        $seo_h1_content = $data['seo_h1_content'];

        if(!$seo_url)
        {
            $_SESSION['msg'] = $CMS->lang['incomplete_url'];
            return false;
        }

        /**
         * Upload image
         */
        $uploadFile = $_FILES['seo_og_image'];

        if(!$uploadFile['tmp_name'])
        {
            $seo_og_image = $oldData['seo_og_image'];
        }
        else
        {
            $dir = "seo";

            if($uploadFile['error'] > 0)
            {
                $_SESSION['msg'] = "{$CMS->lang['error_upload_image']} ({$uploadFile['error']})";
                return false;
            }
            else
            {
                $seo_og_image = "{$dir}/{$CMS->class->image->check_folder_img($dir,"",1)}/{$member['user_id']}_{$time}_{$random}_{$CMS->class->seo->remove_vietnamese($uploadFile['name'])}";
                $path = "{$CMS->vars['upload_dir']}/{$seo_og_image}";
                @copy($uploadFile['tmp_name'], $path) or die ("Could not be upload.");

                //delete old image
                @unlink("{$CMS->vars['upload_dir']}/{$oldData['seo_og_image']}");
            }
        }

        $sql = "UPDATE ".root_table."seo SET seo_url='{$seo_url}', seo_title='{$seo_title}', seo_keywords='{$seo_keywords}', seo_description='{$seo_description}', seo_og_title='{$seo_og_title}', seo_og_image='{$seo_og_image}', seo_og_description='{$seo_og_description}', seo_dc_title='{$seo_dc_title}', seo_dc_subject='{$seo_dc_subject}', seo_dc_description='{$seo_dc_description}', seo_h1_content='{$seo_h1_content}' WHERE seo_id='{$CMS->input['id']}'";

        $DB->query($sql);

        //Clear cache
        $CMS->class->cache->mdelete('seo');

        $updatedRecord = self::getInfo($CMS->input['id']);

        $CMS->class->logs->key = "seo_{$updatedRecord['seo_id']}";
        $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['updated_seo_success']}: <strong>{$updatedRecord['seo_url']}</strong>");

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
        $DB->query("UPDATE ".root_table."seo SET seo_deleted=1 WHERE seo_id={$data['seo_id']}");

        @unlink("{$CMS->vars['upload_dir']}/{$data['seo_src']}");

        //Clear cache
        $CMS->class->cache->mdelete('seo');

        // Create log
        $CMS->class->logs->key = "seo_{$data['seo_id']}";
        $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['seo_deleted']} <b>{$data['seo_title']}</b>")."<br />";

        return true;
    }

    /**
     * Delete multi seo
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

                $DB->query("UPDATE ".root_table."seo SET seo_deleted=1 WHERE seo_id={$id}");

                @unlink("{$CMS->vars['upload_dir']}/{$data['seo_src']}");

                $CMS->class->logs->key = "seo_{$data['seo_id']}";
                $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['seo_deleted']} <b>{$data['seo_title']}</b>")."<br />";

                $deleted = 1;
            }
        }

        if ( $deleted == 0 )
        {
            //Clear cache
            $CMS->class->cache->mdelete('seo');

            $_SESSION["msg"] .= "{$CMS->lang['seo_delete_failed']}";
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
            $sql_add = " seo_id='{$id}' AND ";
        }
        else
        {
            $sql_add = " seo_token_key='{$id}' AND ";
        }

        $sql = "SELECT * FROM ".root_table."seo WHERE {$sql_add} seo_deleted=0 ORDER BY seo_id LIMIT 0,1";

        return $DB->fetch_data($sql,'seo')[0];
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

        $sql = "SELECT * FROM ".root_table."seo WHERE seo_token_key='{$token_key}' ORDER BY seo_id DESC LIMIT 1";

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

        $data['data_bk'] = $data_bk = $data;

        if(is_file("{$CMS->vars['upload_dir']}/{$data_bk['seo_og_image']}"))
        {
            $data['seo_og_image'] = "{$CMS->vars['upload_url']}/{$data_bk['seo_og_image']}";
        }
        else
        {
            $data['seo_og_image'] = "";
        }

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

        $data['seo_time'] = $data['seo_time'] ? date::format($data['seo_time']) : '';

        if($CMS->permit['seo_read'] || $CMS->permit['seo_is_root'])
        {
            $data['seo_url'] = "<a href='{$CMS->vars['root_domain']}/?site=seo&act=show&id={$data_bk['seo_id']}'>{$data_bk['seo_url']}</a>";
        }

        $data['seo_title'] = "<a href='{$CMS->vars['parent_domain']}{$data_bk['seo_url']}'>{$data_bk['seo_title']}</a>";

        $data['record_cnt'] = self::$record_cnt;

        if($CMS->vars['userTemp'][$data_bk['user_id']])
        {
            $data['userInfo'] = $CMS->vars['userTemp'][$data_bk['user_id']];
        }
        else
        {
            $CMS->vars['userTemp'][$data_bk['user_id']] = $data['userInfo'] = $CMS->user->get_info($data_bk['user_id']);
        }

        if(is_file("{$CMS->vars['upload_dir']}/{$data_bk['seo_og_image']}"))
        {
            $data['seo_og_image'] = "{$CMS->vars['upload_url']}/{$data_bk['seo_og_image']}";
        }
        else
        {
            $data['seo_og_image'] = "";
        }

        self::$record_cnt++;

        return $data;
    }

    /**
     * Get list seo
     * @param string $sql_add
     * @return array
     */
    static public function listing($sql_add = "")
    {
        global $CMS, $DB;

        // Update Arrange Data
        self::$arrangeData = trim("seo_id,seo_time");

        // Set default for Arrange
        $default_field = $CMS->input['order'] ? $CMS->input['order'] : "seo_id";
        $default_order = $CMS->input['by'] ? $CMS->input['by'] : "desc";

        // SQL Condition
        $sql_add .= " seo_deleted=0 AND ";

        if(isset($CMS->input['keyword']) && $CMS->input['keyword']!=='')
        {
            $keyword = urldecode($CMS->input['keyword']);

            $seo_url = self::clearUrl($keyword);

            $sql_add .= " (seo_url LIKE '%{$seo_url}%' OR seo_title LIKE '%{$keyword}%' OR seo_description LIKE '%{$keyword}%' OR seo_keywords = '{$keyword}') AND ";
        }

        if(isset($CMS->input['seo_status']) && $CMS->input['seo_status'] !== '')
        {
            $seo_status = intval($CMS->input['seo_status']);
            $sql_add .= " seo_status = {$seo_status} AND ";
        }

        if(isset($CMS->input['seo_url']) && $CMS->input['seo_url']!=='')
        {
            $seo_url = urldecode($CMS->input['seo_url']);

            $seo_url = self::clearUrl($seo_url);

            $sql_add .= " seo_url LIKE '%{$seo_url}%' AND ";
        }

        if(isset($CMS->input['seo_title']) && $CMS->input['seo_title']!=='')
        {
            $seo_title = urldecode($CMS->input['seo_title']);
            $sql_add .= " seo_title LIKE '%{$seo_title}%' AND ";
        }

        if(isset($CMS->input['seo_keywords']) && $CMS->input['seo_keywords']!=='')
        {
            $seo_keywords = urldecode($CMS->input['seo_keywords']);
            $sql_add .= " seo_keywords LIKE '%{$seo_keywords}%' AND ";
        }

        if(isset($CMS->input['seo_description']) && $CMS->input['seo_description']!=='')
        {
            $seo_description = urldecode($CMS->input['seo_description']);
            $sql_add .= " seo_description LIKE '%{$seo_description}%' AND ";
        }


        $sql = "SELECT * FROM ".root_table."seo WHERE {$sql_add} 1=1 ORDER BY {$default_field} {$default_order}";

        // Create SQL Query for listing Data
        list($CMS->show_page, $data) = $DB->fetch_listing($sql, self::$maxPage, self::$prefixPaging, self::$suffixPaging, $CMS->input['page'], 'seo');

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

        $sql = "SELECT COUNT(0) cnt FROM ".root_table."seo WHERE {$sql_add} {$field} = '{$val}' AND seo_deleted=0 LIMIT 1";

        return $DB->fetch_data($sql,'seo')[0]['cnt'];
    }


    /**
     * clear domain, paging param
     * @param string $url
     * @return string
     */
    static public function clearUrl($url)
    {
        global $CMS;

        //remove domain
        $url = str_replace($CMS->vars['parent_domain'], '', $url);

        //remove paging param
        $url = preg_replace('/(\/page-[0-9]+)/','',$url);

        return $url;
    }
}