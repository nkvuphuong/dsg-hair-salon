<?php

namespace models;

use lib\date;
use lib\input;
use lib\db;
use \lib\template;

class staff
{
    /**
     * The positions of staff
     *
     * @var array
     * @access public
     * @static
     * */
    static public $sqlQuery="";
    static public $maxPage="10";
    static public $prefixPaging="";
    static public $suffixPaging="";
    static public $sqlAdd="";
    static public $record_cnt=0;


    static function listing()
    {
        global $CMS, $DB;

        // if(isset($CMS->input['keyword']) && $CMS->input['keyword']!=='')
        // {
        //     self::$sqlAdd .= " AND (giftcard_code LIKE '%{$CMS->input['keyword']}%' OR C.cus_full_name LIKE '%{$CMS->input['keyword']}%' OR C.cus_email LIKE '%{$CMS->input['keyword']}%') ";
        // }

        $clause = self::$sqlAdd;
        // self::$prefixPaging .= "&page=".intval($CMS->input['page']);
        $sql = "SELECT * FROM ".root_table."user WHERE user_deleted=0 AND user_is_staff=1 {$clause} ORDER BY user_id DESC";

        // Create SQL Query for listing Data
        list($CMS->show_page, $results) = $DB->fetch_listing($sql, self::$maxPage, self::$prefixPaging, self::$suffixPaging, $CMS->input['page'], 'user.staff');

        $data = [];

        if(!$results) return $data;

        foreach($results as $result)
        {
            $result['image'] = $CMS->vars['upload_url']."/avatar/".$result['user_avatar'];
            
            $result['record_cnt'] = self::$record_cnt;
            self::$record_cnt++;
            $data[] = $result;
        }

        return $data;
    }

    static function edit()
    {
        global $CMS, $DB;

        // Data
        $data = self::getInfo($CMS->input['id']);

        // input
        $user_display_name = $CMS->input['user_display_name'];
        $user_email = $CMS->input['user_email'];
        $user_note = $CMS->class->editor->input("user_note");
        $user_status = intval($CMS->input['user_status']);
        $user_range = intval($CMS->input['user_range']);
        $user_time_update = time();

        if(!$user_display_name) {$_SESSION['msg'] = $CMS->lang['title_full_name_incomplete']; return false;}
        if(!$user_email) {$_SESSION['msg'] = $CMS->lang['invalid_email']; return false;}
        if(self::checkExistEmail($user_email, $data['user_email'])) { $_SESSION['msg'] = $CMS->lang['exist_email']; return false;}

        // Avatar
        $check = true;
        $dir = $CMS->vars['upload_dir'].'/avatar';
        $image = $_FILES['user_avatar'];
        $base64_image = $CMS->input['base64_image'];
        $size_allow = 3*1024*1024;

        if (!empty($image['tmp_name'])) 
        {
            if ($image['size'] > $size_allow || ! in_array(exif_imagetype($image['tmp_name']), array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF))) {
                $check = false;
                $_SESSION['msg'] .= $CMS->lang['user_avatar_error'].'<br>';
                return false;
            }
        }

        if (!empty($image['tmp_name'])) 
        {
            $user_avatar = !empty($user_avatar) ? $user_avatar :  $CMS->class->random->randomString(5,'abcdefghijklmnopqrstuvwxyz')."_".time().'.jpg';
            
            //Check is_dir
            $CMS->class->image->is_dir($dir);
            $imgPath = $dir.'/'.$user_avatar;
            move_uploaded_file($image['tmp_name'], $imgPath);

            $CMS->class->image->quality = 1;

            //Create thumb
            /*foreach ($this->thumb_size as $keySize => $valSize)
            {
                $CMS->class->image->resize($imgPath, \lib\image::getThumb($imgPath, $this->thumb_folder, "{$keySize}_"), $valSize);
            }*/
        }
        else
        {
            // if( !empty($base64_image))
            // {
            //     // Tai hinh tu basse 64
            //     $user_avatar = $CMS->class->image->uploadImgBase64($base64_image, 'avatar', 'thumbnail', 220);      
            // }

            $user_avatar = $data['user_avatar'];
        }

        $CMS->class->logs->old_data = $data;
        //Update info
        $DB->query("UPDATE ".root_table."user SET user_display_name='{$user_display_name}', user_email='{$user_email}', user_status='{$user_status}', user_note='{$user_note}', user_avatar='{$user_avatar}', user_time_update='{$user_time_update}', user_range='{$user_range}' WHERE user_id='{$data['user_id']}'");

        //Clear cache
        $CMS->class->cache->mdelete('user');

        // Save logs
        $CMS->class->logs->key = "user_{$data['user_id']}";
        // Create log
        $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['update_staff_successful']} <b>{$data['user_display_name']}</b>")."<br />";
        // Get info
        $data_new = self::getInfo($data['user_id']);
        // Step 2: Save detail logs
        $CMS->class->logs->key = "user_{$data_new['user_id']}";
        $CMS->class->logs->save_detail("user",$data_new['user_id'],$data_new);
        return true;

    }

    static function getInfo($record_id=0, $field_return="*")
    {
        global $DB, $CMS;

        if($record_id and $field_return)
        {
            // Count field
            $countField = count(explode(",", $field_return));
            // sqladd
            $sql_add = self::$sqlAdd;
            //Query
            $data = $DB->fetch_data("SELECT {$field_return} FROM ".root_table."user WHERE user_deleted=0 AND user_id='{$record_id}' {$sql_add} LIMIT 1", 'user')[0];

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

    static function searchAjax()
    {
        global $CMS, $DB;

        // Key search
        $key_search = $CMS->input['key_search'];

        // Query search follow key
        $results = $DB->fetch_data("SELECT G.*, C.cus_full_name as cus_name, C.cus_email FROM ".root_table."user as G LEFT JOIN ".root_table."customer AS C ON G.cus_id=C.cus_id WHERE gitem_deleted=0 AND (giftcard_code LIKE '%{$key_search}%' OR C.cus_full_name LIKE '%{$key_search}%' OR C.cus_email LIKE '%{$key_search}%' OR gitem_code_old LIKE '%{$key_search}%')  ORDER BY G.gitem_time DESC", 'user.customer');

        $output = [];

        if(!$results) return $output;

        foreach($results as $result)
        {
            $result['data_product'] = $CMS->product->get_info($result['product_id']);
            $result['image'] = $CMS->vars['upload_url']."/giftcards/".$result['gitem_code'].".png";
            $result['amount'] = $CMS->class->input->currency($result['gitem_amount']);
            $result['amount_remain'] = $CMS->class->input->currency($result['gitem_amount_remain']);
            $result['cus_name'] = $result['cus_name'] ? "<a href='{$CMS->vars['root_domain']}/?site=customer&act=show&id={$result['cus_id']}'>{$result['cus_name']}</a>" : "N/A";
            $result['record_cnt'] = self::$record_cnt;
            self::$record_cnt++;
            $output[] = $result;
        }

        // Output
        return $output;

    }

    static function getdataForm($id=0)
    {
        global $CMS, $DB;

        // $data
        $data = self::getInfo(intval($CMS->input['id']));

        $data['link_act'] = $CMS->input['act'] == "add" ? "{$CMS->vars['root_domain']}/?site=staff&act=add_do" : "{$CMS->vars['root_domain']}/?site=staff&act=edit_do&id={$user_id}";
        $data['title_staff'] = "";
        if($CMS->input['act'] == "add" or $CMS->input['act'] == "add_do")
        {
            $data['title_staff'] = $CMS->lang['add_staff'];
            $data['url_back']['list'] = "{$CMS->vars['root_domain']}/?site={$CMS->input['site']}";
        }elseif($CMS->input['act'] == "edit" or $CMS->input['act'] == "edit_do")
        {
            $data['title_staff'] = $CMS->lang['edit_staff'];
            $data['url_back']['list'] = "{$CMS->vars['root_domain']}/?site={$CMS->input['site']}";
            // $data['url_back']['detail_id'] = "{$data['user_id']}";
        }

        $data['src_image'] = $data['user_avatar'] ? $CMS->vars['upload_url']."/avatar/".$data['user_avatar'] : "";
        $data['user_status'] = !isset($data['user_status']) ? 1 : $data['user_status'];
        
        return $data;
    }

    static function add()
    {
        global $CMS, $DB;

        // input
        $user_display_name = $CMS->input['user_display_name'];
        $user_email = $CMS->input['user_email'];
        $user_note = $CMS->class->editor->input("user_note");
        $user_status = intval($CMS->input['user_status']);
        $user_range = intval($CMS->input['user_range']);
        $user_time = time();

        if(!$user_display_name) {$_SESSION['msg'] = $CMS->lang['title_full_name_incomplete']; return false;}
        if(!$user_email) {$_SESSION['msg'] = $CMS->lang['invalid_email']; return false;}
        // print "aseedfa";exit;
        if(self::checkExistEmail($user_email)) { $_SESSION['msg'] = $CMS->lang['exist_email']; return false;}

        // Avatar
        $check = true;
        $dir = $CMS->vars['upload_dir'].'/avatar';
        $image = $_FILES['user_avatar'];
        $base64_image = $CMS->input['base64_image'];
        $size_allow = 3*1024*1024;

        if (!empty($image['tmp_name'])) 
        {
            if ($image['size'] > $size_allow || ! in_array(exif_imagetype($image['tmp_name']), array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF))) {
                $check = false;
                $_SESSION['msg'] .= $CMS->lang['user_avatar_error'].'<br>';
                return false;
            }
        }

        if (!empty($image['tmp_name'])) 
        {
            $user_avatar = !empty($user_avatar) ? $user_avatar :  $CMS->class->random->randomString(5,'abcdefghijklmnopqrstuvwxyz')."_".time().'.jpg';
            
            //Check is_dir
            $CMS->class->image->is_dir($dir);
            $imgPath = $dir.'/'.$user_avatar;
            move_uploaded_file($image['tmp_name'], $imgPath);

            $CMS->class->image->quality = 1;

            //Create thumb
            /*foreach ($this->thumb_size as $keySize => $valSize)
            {
                $CMS->class->image->resize($imgPath, \lib\image::getThumb($imgPath, $this->thumb_folder, "{$keySize}_"), $valSize);
            }*/
        }
        else
        {
            if( !empty($base64_image))
            {
                // Tai hinh tu basse 64
                $user_avatar = $CMS->class->image->uploadImgBase64($base64_image, 'avatar', 'thumbnail', 220);      
            }
        }
        // Insert database
        $check = $DB->query("INSERT INTO ".root_table."user (user_display_name, user_email, user_note, user_status, user_time, user_avatar, user_is_staff, user_range) VALUES ('{$user_display_name}', '{$user_email}', '{$user_note}', '{$user_status}', '{$user_time}', '{$user_avatar}', 1, '{$user_range}')");

        $CMS->class->cache->mdelete('user');
        if($check)
        {
            $id = $DB->last_insert_id();
            $user_name = "staff_{$id}";
            $DB->query("UPDATE ".root_table."user SET user_name='{$user_name}' WHERE user_id='{$id}'");
            $_SESSION['msg'] = $CMS->lang['create_staff_success'];
            return true;
        }else
        {
            $_SESSION['msg'] = $CMS->lang['create_staff_error'];
            return false;
        }

    }

    static function checkCodeOld($code='')
    {
        global $CMS, $DB;

        if($code)
        {
            $DB->query("SELECT 0 FROM ".root_table."user WHERE gitem_code_old='{$code}'");
            return $DB->num_rows() ? true : false;
        }
    }

    static function control()
    {
        global $CMS;

        $output = "";
        // Xoá multi
        if ($CMS->permit["staff_delete"] ) 
        {
            $output .= "<option value=\"delete_all\">{$CMS->lang['title_delete_all']}</option>";
        }

        return $output;
    } 

    public function delete()
    {
        global $CMS, $DB;
        
        // Get info
        $data = self::getInfo($CMS->input['id']);
      
        // Check existing
        if ( ! $data ) { return false; }
        
        $DB->query("UPDATE ".root_table."user SET user_deleted = 1 WHERE user_id={$data['user_id']}");

        $CMS->class->cache->mdelete('user');

        // Create log
        $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['staff_deleted']} <b>{$data['user_display_name']}</b>")."<br />";
        
        return true;
    }

    static function delete_all()
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
                
                $DB->query("UPDATE ".root_table."user SET user_deleted = 1 WHERE user_id={$data['user_id']}");
        
                $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['staff_deleted']} <b>{$data['user_display_name']}</b>")."<br />";
                
                $deleted = 1;
            }
        }
        
        if ( $deleted == 0 )
        {
            $CMS->class->cache->mdelete('user');

            $_SESSION["msg"] .= "{$CMS->lang['staff_delete_failed']}";
        }

        return true;
    }

    static function sendMailAgain()
    {
        global $CMS, $DB;

        // Information gift card item
        $data = self::getInfo($CMS->input['id']);

        // Information customer
        $customer = $CMS->customer->getInfo($data['cus_id']);

        $time = $CMS->class->date->date_format(time());
        $CMS->email->email_template = "resend_giftcard";
        $CMS->email->email_to = $customer['cus_email'];
        $CMS->email->email_toname = $customer['cus_full_name'];

        // email_from
        $CMS->email->data['date_send'] =  $time;
        $CMS->email->data['cus_name'] = $customer['cus_full_name'];
        
       

        $table_html = "<table class=\"table table-bordered\" width=\"100%\"><tbody><tr bgcolor=rgb(230, 229, 229) valign='middle' align='center'><td width='80%' style='text-align: center;'>Item</td><td width='20%' style='text-align: center;'>Price</td></tr>";
        $total = 0;
        
        $total += $data['gitem_amount_remain'];
        $amount_remain = $CMS->class->input->currency($data['gitem_amount_remain']);
        $data['image'] = $CMS->vars['upload_url']."/giftcards/".$data['gitem_code'].".png";
        $table_html .= "<tr style='text-align: center;'><td><img src=\"{$data['image']}\" style=\"max-width:80%; max-height: 300px;\" /><br/>{$data['product_name']}</td><td>{$amount_remain}</td></tr>";
        $table_html .= "</tbody></table>";
        $CMS->email->data['table_content'] = $table_html;
        $CMS->email->data['website_link'] = str_replace("https://", "", str_replace("http://", "", $CMS->vars['parent_domain']));
        $CMS->email->data['website_name'] = $CMS->vars['website_title'];
        $check = $CMS->email->quick_send(0,0);
        if($check)
        {
            $_SESSION['msg'] = "Send information gift card for customer successful";
        }else
        {
            $_SESSION['msg'] = "Send information gift card for customer unsuccessful";
        }
    }

    static function checkExistEmail($email="", $email_accept="")
    {
        global $CMS, $DB;

        $clause = $email_accept ? " AND user_email!='{$email_accept}' " : "";
        $DB->query("SELECT 0 FROM ".root_table."user WHERE user_email='{$email}' AND user_deleted=0 {$clause}");
        return $DB->num_rows() ? true : false;
    }

    static function setBusy($input = [])
    {
        global $CMS, $DB;

        $dateFrom = explode(" ",$input['user_busy_from']);
        $dateTo = explode(" ",$input['user_busy_to']);

        $data = [
            'user_id' => $input['user_id'] * 1,
            'user_busy_from' => $CMS->class->date->date2time($dateFrom[0]) + date::hour2Sec($dateFrom[1]),
            'user_busy_to' => $CMS->class->date->date2time($dateTo[0]) + date::hour2Sec($dateTo[1]),
        ];

        return $DB->update("user", $data, "user_id");
    }
}