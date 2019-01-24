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

class logos
{
    /**
     * The positions of logos
     * pos_mode: session|always
     * + session: hide/show by interval
     * + always: always show
     * pos_interval: time to check show/hide by session (minutes)
     * pos_type: random|all
     * + random: show random a record
     * + all: show all record
     * @var array
     * @access public
     * @static
     * */
    static public $positions = [];

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
     * Add new logo
     * @param array $data array input
     * @output boolean|array
     */
    static public function add($data = [])
    {
        global $CMS, $member, $DB;

        $time = time();
        $random = rand(0,100);
        $logo_token_key = $CMS->class->random->md5($CMS->class->random->character(16));

        $logo_position = intval($data['logo_position']);
        $logo_name = $data['logo_name'];
        $logo_start_time = $data['logo_start_time'] ? $CMS->class->date->date2time($data['logo_start_time']) : 0;
        $logo_end_time = $data['logo_end_time'] ? $CMS->class->date->date2time($data['logo_end_time']) : 0;
        $logo_desc = $CMS->class->editor->input("logo_desc");
        $logo_link = $data['logo_link'];
        $logo_src_alt = $data['logo_src_alt'];

        $logo_upload_option = intval($CMS->input['upload_option']);

        $logo_status = 1;

        if(!$logo_position)
        {
            $_SESSION['msg'] = $CMS->lang['incomplete_position'];
            return false;
        }

        /**
         * Check if position existed
         */
        /*if(!isset(self::$positions[$logo_position]))
        {
            $_SESSION['msg'] = $CMS->lang['invalid_position'];
            return false;
        }*/

        // if($logo_name === "")
        // {
        //     $_SESSION['msg'] = $CMS->lang['incomplete_name'];
        //     return false;
        // }

        /*
         * Remove by LHL.
         * if(empty($logo_link))
        {
            $_SESSION['msg'] = $CMS->lang['incomplete_link'];
            return false;
        }*/

        /**
         * Check invalid url
         */
        if(!empty($logo_link) && !input::is_url($logo_link))
        {
            $_SESSION['msg'] = $CMS->lang['invalid_link'];
            return false;
        }


        if($logo_upload_option == 1)
        {

            //print_r (json_encode(array("a" => $_FILES['upload_img_original'])));exit;
            $file_1_tmp = isset($_FILES['upload_image_method_1']['tmp_name']) ? $_FILES['upload_image_method_1']['tmp_name'] : "";
            $file_1_name = isset($_FILES['upload_image_method_1']['name']) ? $_FILES['upload_image_method_1']['name'] : "";
            $file_1_type = isset($_FILES['upload_image_method_1']['type']) ? $_FILES['upload_image_method_1']['type'] : "";
            $file_1_size = isset($_FILES['upload_image_method_1']['size']) ? $_FILES['upload_image_method_1']['size'] : "";
            $file_1_error = isset($_FILES['upload_image_method_1']['error']) ? $_FILES['upload_image_method_1']['error'] : "";
            $file_1_ext =  explode("/",$file_1_type)[1];

            if($file_1_tmp == "" )
            {
                $_SESSION['error_msg'] = "{$CMS->lang['incomplete_image']}";
               return false;        
            }

           
            $logo_image_original = $file_1_location = "{$member['user_id']}_{$time}_{$random}_original_{$CMS->class->seo->remove_vietnamese($file_1_name)}";

            $dir ="logo";
            //
            $logo_original_src = "{$dir}/{$CMS->class->image->check_folder_img($dir,"",1)}/{$logo_image_original}";
            $path = "{$CMS->vars['upload_dir']}/{$logo_original_src}";
            @copy($file_1_tmp, $path) or die ("Could not be upload.");
            
            $list_image = $CMS->input['list_image'];
            $file_name = $CMS->class->image->uploadImgBase64($list_image,"logo");
            $logo_src = "{$dir}/{$CMS->class->image->check_folder_img($dir,"",1)}/{$member['user_id']}_{$time}_{$random}_{$file_name}";
            @rename($CMS->vars['upload_dir']."/logo/".$file_name, $CMS->vars['upload_dir']."/".$logo_src);

            // Filter style
            $designtext =  array_values($CMS->input['designtext']);
            $designtext = array_reverse($designtext);
            $style_push = explode("|||", $CMS->input['style_push']);
            $style_p_push = explode("|||", $CMS->input['style_p_push']);
         

            for ($i=0; $i<= count($designtext); $i++) {
             
                 if($designtext[$i] != "")
                 {
                    $style_editor[$i]["text"] = $designtext[$i];
                    $style_editor[$i]["style_push"] = $style_push[$i];
                    $style_editor[$i]["style_p_push"] = $style_p_push[$i];
                 }
            }
            $logo_style_content = json_encode($style_editor,  JSON_UNESCAPED_UNICODE );
             
        }
        else
        {
             /**
             * Upload file
             */
            $uploadFile = $_FILES['upload_file'];

            if(!$uploadFile['tmp_name']) //Check incomplete
            {
                $_SESSION['msg'] = $CMS->lang['incomplete_image'];
                return false;
            }
            else
            {
                $dir = "logo";

                if($uploadFile['error'] > 0) //Check file error
                {
                    $_SESSION['msg'] = "$CMS->lang['error_upload_image'] ({$uploadFile['error']})";
                    return false;
                }
                else
                {
                    $logo_src = "{$dir}/{$CMS->class->image->check_folder_img($dir,"",1)}/{$member['user_id']}_{$time}_{$random}_{$CMS->class->seo->remove_vietnamese($uploadFile['name'])}";
                    $path = "{$CMS->vars['upload_dir']}/{$logo_src}";
                    @copy($uploadFile['tmp_name'], $path) or die ("Could not be upload.");
                }
            }
        }


       

        $sql = "INSERT INTO ".root_table."logos(logo_name, logo_desc, logo_src, logo_link, logo_position, logo_start_time, logo_end_time, logo_time, logo_token_key, user_id, logo_status, logo_src_alt,logo_upload_option,logo_style_content,logo_image_original) VALUES('{$logo_name}', '{$logo_desc}', '{$logo_src}', '{$logo_link}', '{$logo_position}', '{$logo_start_time}', '{$logo_end_time}', '{$logo_end_time}', '{$logo_token_key}', '{$member['user_id']}', '{$logo_status}', '{$logo_src_alt}','{$logo_upload_option}','{$logo_style_content}', '{$logo_original_src}')";

        $DB->query($sql);

        //Clear cache
        $CMS->class->cache->mdelete('logos');

        $insertedRecord = self::insertedRecord($logo_token_key); //Get inserted record

        $CMS->class->logs->key = "logo_{$insertedRecord['logo_id']}";
        $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['added_logo_success']}: <strong>{$insertedRecord['logo_name']}</strong>");

        return $insertedRecord;
    }

    /**
     * Edit logos
     * @param array $data
     * @return bool|array
     */
    static public function edit($data = [])
    {
        global $CMS, $member, $DB;

        $logo_data = self::getInfo($data['id']);

        $time = time();
        $random = rand(0,100);

        $logo_position = intval($data['logo_position']);
        $logo_name = $data['logo_name'];
        $logo_start_time = $data['logo_start_time'] ? $CMS->class->date->date2time($data['logo_start_time']) : 0;
        $logo_end_time = $data['logo_end_time'] ? $CMS->class->date->date2time($data['logo_end_time']) : 0;
        $logo_desc = $CMS->class->editor->input("logo_desc");
        $logo_link = $data['logo_link'];
        $logo_src_alt = $data['logo_src_alt'];

        $logo_upload_option = intval($CMS->input['upload_option']);
        $oldData = self::getInfo($CMS->input['id']);

        if(!$oldData)
        {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            return false;
        }

        if(!$logo_position)
        {
            $_SESSION['msg'] = $CMS->lang['incomplete_position'];
            return false;
        }

        /*if(!isset(self::$positions[$logo_position]))
        {
            $_SESSION['msg'] = $CMS->lang['invalid_position'];
            return false;
        }*/

        // if($logo_name === "")
        // {
        //     $_SESSION['msg'] = $CMS->lang['incomplete_name'];
        //     return false;
        // }

        /*
         * Remove by LHL.
         * if(empty($logo_link))
        {
            $_SESSION['msg'] = $CMS->lang['incomplete_link'];
            return false;
        }*/

        /**
         * Check invalid url
         */
        if(!empty($logo_link) && !input::is_url($logo_link))
        {
            $_SESSION['msg'] = $CMS->lang['invalid_link'];
            return false;
        }

        if($logo_upload_option == 1)
        {
             
            //print_r (json_encode(array("a" => $_FILES['upload_img_original'])));exit;
            $file_1_tmp = isset($_FILES['upload_image_method_1']['tmp_name']) ? $_FILES['upload_image_method_1']['tmp_name'] : "";
            $file_1_name = isset($_FILES['upload_image_method_1']['name']) ? $_FILES['upload_image_method_1']['name'] : "";
            $file_1_type = isset($_FILES['upload_image_method_1']['type']) ? $_FILES['upload_image_method_1']['type'] : "";
            $file_1_size = isset($_FILES['upload_image_method_1']['size']) ? $_FILES['upload_image_method_1']['size'] : "";
            $file_1_error = isset($_FILES['upload_image_method_1']['error']) ? $_FILES['upload_image_method_1']['error'] : "";
            $file_1_ext =  explode("/",$file_1_type)[1];

            if($file_1_tmp == "" )
            {
                 $logo_image_original = $logo_data['logo_image_original'];
              
            }
            else
            {
                $logo_image_original = $file_1_location = "{$member['user_id']}_{$time}_{$random}_original_{$CMS->class->seo->remove_vietnamese($file_1_name)}";

                $dir ="logo";
                //
                $logo_original_src = "{$dir}/{$CMS->class->image->check_folder_img($dir,"",1)}/{$logo_image_original}";
                $path = "{$CMS->vars['upload_dir']}/{$logo_original_src}";
                @copy($file_1_tmp, $path) or die ("Could not be upload.");
                
                

            }
           
            $list_image = $CMS->input['list_image'];
            $file_name = $CMS->class->image->uploadImgBase64($list_image,"logo");
            $logo_src = "{$dir}/{$CMS->class->image->check_folder_img($dir,"",1)}/{$member['user_id']}_{$time}_{$random}_{$file_name}";
            @rename($CMS->vars['upload_dir']."/logo/".$file_name, $CMS->vars['upload_dir']."/".$logo_src);
 
            // Filter style
            $designtext =  array_values($CMS->input['designtext']);
            $designtext = array_reverse($designtext);
            $style_push = explode("|||", $CMS->input['style_push']);
            $style_p_push = explode("|||", $CMS->input['style_p_push']);
         

            for ($i=0; $i<= count($designtext); $i++) {
             
                 if($designtext[$i] != "")
                 {
                    $style_editor[$i]["text"] = $designtext[$i];
                    $style_editor[$i]["style_push"] = $style_push[$i];
                    $style_editor[$i]["style_p_push"] = $style_p_push[$i];
                 }
            }
            $logo_style_content = json_encode($style_editor,  JSON_UNESCAPED_UNICODE );
             
        }
        else
        {
            $uploadFile = $_FILES['upload_file'];

            if(!$uploadFile['tmp_name'])
            {
                $logo_src = $oldData['logo_src'];
            }
            else
            {
                $dir = "logo";

                if($uploadFile['error'] > 0)
                {
                    $_SESSION['msg'] = "$CMS->lang['error_upload_image'] ({$uploadFile['error']})";
                    return false;
                }
                else
                {
                    $logo_src = "{$dir}/{$CMS->class->image->check_folder_img($dir,"",1)}/{$member['user_id']}_{$time}_{$random}_{$CMS->class->seo->remove_vietnamese($uploadFile['name'])}";
                    $path = "{$CMS->vars['upload_dir']}/{$logo_src}";
                    @copy($uploadFile['tmp_name'], $path) or die ("Could not be upload.");

                    //delete old image
                    @unlink("{$CMS->vars['upload_dir']}/{$oldData['logo_src']}");
                }
            }
        }//End if
            
        $sql = "UPDATE ".root_table."logos SET logo_name='{$logo_name}', logo_desc='{$logo_desc}', logo_position='{$logo_position}', logo_start_time='{$logo_start_time}', logo_end_time='{$logo_end_time}', logo_link='{$logo_link}', logo_src='{$logo_src}', logo_src_alt='{$logo_src_alt}', logo_upload_option='{$logo_upload_option}', logo_image_original = '{$logo_original_src}', logo_style_content='{$logo_style_content}' WHERE logo_id='{$CMS->input['id']}'";

        $DB->query($sql);

        //Clear cache
        $CMS->class->cache->mdelete('logos');

        $updatedRecord = self::getInfo($CMS->input['id']);

        $CMS->class->logs->key = "logo_{$updatedRecord['logo_id']}";
        $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['updated_logo_success']}: <strong>{$updatedRecord['logo_name']}</strong>");

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
        $DB->query("UPDATE ".root_table."logos SET logo_deleted=1 WHERE logo_id={$data['logo_id']}");

        //Clear cache
        $CMS->class->cache->mdelete('logos');

        @unlink("{$CMS->vars['upload_dir']}/{$data['logo_src']}");

        // Create log
        $CMS->class->logs->key = "logo_{$data['logo_id']}";
        $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['logo_deleted']} <b>{$data['logo_name']}</b>")."<br />";

        return true;
    }

    /**
     * Delete multi logos
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

                $DB->query("UPDATE ".root_table."logos SET logo_deleted=1 WHERE logo_id={$id}");

                @unlink("{$CMS->vars['upload_dir']}/{$data['logo_src']}");

                $CMS->class->logs->key = "logo_{$data['logo_id']}";
                $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['logo_deleted']} <b>{$data['logo_name']}</b>")."<br />";

                $deleted = 1;
            }
        }

        if ( $deleted == 0 )
        {
            //Clear cache
            $CMS->class->cache->mdelete('logos');

            $_SESSION["msg"] .= "{$CMS->lang['logo_delete_failed']}";
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
            $sql_add = " logo_id='{$id}' AND ";
        }
        else
        {
            $sql_add = " logo_token_key='{$id}' AND ";
        }

        $sql = "SELECT * FROM ".root_table."logos WHERE {$sql_add} logo_deleted=0 ORDER BY logo_id LIMIT 0,1";

        return $DB->fetch_data($sql,'logos')[0];
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

        $sql = "SELECT * FROM ".root_table."logos WHERE logo_token_key='{$token_key}' ORDER BY logo_id DESC LIMIT 1";

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

        $data['logo_start_time'] = $data['logo_start_time'] ? date::format($data['logo_start_time']) : '';
        $data['logo_end_time'] = $data['logo_end_time'] ? date::format($data['logo_end_time']) : '';

        $logo_src = "{$CMS->vars['upload_dir']}/{$data['logo_src']}";
        $data['logo_src'] = (file_exists($logo_src) && is_file($logo_src)) ? "{$CMS->vars['upload_url']}/{$data['logo_src']}" : "{$CMS->vars['root_domain']}/assets/img/no-photo.png";

        return $data;
    }

    /**
     * Convert original record to show
     * @param array $data
     * @return array
     */
    static  public function convertValue($data=[])
    {
        global $CMS, $tpl;

        $data['data_bk'] = $data_bk = $data;

        // Replace search content
        $data = $CMS->class->search->convertvalue($data);

        $data['logo_detail'] = "{$CMS->vars['root_domain']}/?site=logos&act=show&id={$data['logo_id']}";

        if($CMS->permit['logos_read'] || $CMS->permit['logos_is_root'])
        {
            $data['logo_name'] = "<a href='{$data['logo_detail']}'>{$data['logo_name']}</a>";
        }


        $data['logo_start_time'] = $data['logo_start_time'] ? date::format($data['logo_start_time']) : '';
        $data['logo_end_time'] = $data['logo_end_time'] ? date::format($data['logo_end_time']) : '';
        $data['logo_time'] = $data['logo_time'] ? date::format($data['logo_time']) : '';

        $logo_src = "{$CMS->vars['upload_dir']}/{$data['logo_src']}";
        $data['logo_src'] = (file_exists($logo_src) && is_file($logo_src)) ? "{$CMS->vars['upload_url']}/{$data['logo_src']}" : "{$CMS->vars['root_domain']}/assets/img/no-photo.png";


        $data['posInfo'] = $tpl->dataPositions[$data['logo_position']];
        $data['logo_position'] = $data['posInfo']['pos_name'];

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
     * Get list logos
     * @param string $sql_add
     * @return array
     */
    static public function listing($sql_add = "")
    {
        global $CMS, $DB;

        // Update Arrange Data
        self::$arrangeData = trim("logo_id,logo_start_time,logo_end_time,logo_time,logo_status");

        // Set default for Arrange
        $default_field = $CMS->input['order'] ? $CMS->input['order'] : "logo_id";
        $default_order = $CMS->input['by'] ? $CMS->input['by'] : "desc";

        // SQL Condition
        $sql_add .= " logo_deleted=0 AND ";

        $sql_having = "";

        if(isset($CMS->input['keyword']) && $CMS->input['keyword']!=='')
        {
            $keyword = urldecode($CMS->input['keyword']);
            $sql_add .= " (logo_name LIKE '%{$keyword}%' OR logo_desc LIKE '%{$keyword}%') AND ";
        }

        if(isset($CMS->input['logo_status']) && $CMS->input['logo_status'] !== '')
        {
            $logo_status = intval($CMS->input['logo_status']);
            $sql_add .= " logo_status = {$logo_status} AND ";
        }

        if(isset($CMS->input['logo_position']) && $CMS->input['logo_position'] !== '')
        {
            $logo_position = intval($CMS->input['logo_position']);
            $sql_add .= " logo_position = {$logo_position} AND ";
        }

        if(isset($CMS->input['logo_name']) && $CMS->input['logo_name']!=='')
        {
            $logo_name = urldecode($CMS->input['logo_name']);
            $sql_add .= " logo_name LIKE '%{$logo_name}%' AND ";
        }

        if(isset($CMS->input['logo_start_time']) && $CMS->input['logo_start_time']!=='')
        {
            $logo_start_time = urldecode($CMS->input['logo_start_time']);
            $sql_having .= " start_time='$logo_start_time' AND ";
        }

        if(isset($CMS->input['logo_end_time']) && $CMS->input['logo_end_time']!=='')
        {
            $logo_end_time = urldecode($CMS->input['logo_end_time']);
            $sql_having .= " end_time='{$logo_end_time}' AND ";
        }

        $datFormatSQL = $CMS->vars['date_format'];
        $datFormatSQL = str_replace('YYYY','%Y',$datFormatSQL);
        $datFormatSQL = str_replace('MM','%m',$datFormatSQL);
        $datFormatSQL = str_replace('DD','%d',$datFormatSQL);

        $sql = "SELECT *, DATE_FORMAT(FROM_UNIXTIME(`logo_start_time`), '{$datFormatSQL}') start_time, DATE_FORMAT(FROM_UNIXTIME(`logo_end_time`), '{$datFormatSQL}') end_time FROM ".root_table."logos WHERE {$sql_add} 1=1 HAVING {$sql_having} 1=1 ORDER BY {$default_field} {$default_order}";

        // Create SQL Query for listing Data
        list($CMS->show_page, $data) = $DB->fetch_listing($sql, self::$maxPage, self::$prefixPaging, self::$suffixPaging, $CMS->input['page'], 'logos');

        return $data;
    }
}