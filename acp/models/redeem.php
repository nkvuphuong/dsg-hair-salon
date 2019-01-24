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

class redeem
{
    /**
     * The positions of redeem
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
        
        // if(isset($CMS->input['keyword']))
        // {
        //     // self::$sqlAdd .= " AND (giftcard_code LIKE '%{$CMS->input['keyword']}%' OR C.cus_full_name LIKE '%{$CMS->input['keyword']}%') ";
        //     // self::$prefixPaging .= "?site=redeem&subact=quick_search&keyword=".urlencode($CMS->input['keyword']);
        // }

        if(isset($CMS->input['keyword']) && $CMS->input['keyword']!=='')
        {
            self::$sqlAdd .= " AND (giftcard_code LIKE '%{$CMS->input['keyword']}%' OR C.cus_full_name LIKE '%{$CMS->input['keyword']}%' OR C.cus_email LIKE '%{$CMS->input['keyword']}%') ";
        }

        $clause = self::$sqlAdd;
        // self::$prefixPaging .= "&page=".intval($CMS->input['page']);
        $sql = "SELECT G.*, C.cus_full_name as cus_name, C.cus_email FROM ".root_table."giftcard_items as G LEFT JOIN ".root_table."customer AS C ON G.cus_id=C.cus_id WHERE gitem_deleted=0 {$clause} ORDER BY G.gitem_time DESC";
// print $sql;exit;

        // Create SQL Query for listing Data
        list($CMS->show_page, $results) = $DB->fetch_listing($sql, self::$maxPage, self::$prefixPaging, self::$suffixPaging, $CMS->input['page'], 'giftcard_items.customer');

        $data = [];

        if(!$results) return $data;

        foreach($results as $result)
        {
            $result['data_product'] = $CMS->product->get_info($result['product_id']);
            $result['image'] = $CMS->vars['upload_url']."/giftcards/".$result['gitem_code'].".png";
            $result['amount'] = $CMS->class->input->currency($result['gitem_amount']);
            $result['amount_remain'] = $CMS->class->input->currency($result['gitem_amount_remain']);
            $result['cus_name'] = $result['cus_name'] ? "<a href='{$CMS->vars['root_domain']}/?site=customer&act=show&id={$result['cus_id']}'>{$result['cus_name']}</a>" : "N/A";
            $result['record_cnt'] = self::$record_cnt;
            self::$record_cnt++;
            $data[] = $result;
        }

        return $data;
    }

    static function edit()
    {
        global $CMS, $DB;

        $data = self::getInfo($CMS->input['gitem_id']);
        $redeem_date = $CMS->input['redeem_date'];
        $redeem_amount = $CMS->input['redeem_amount'];
        $redeem_time = time();
        
        // check redeem_date
        $redeem_date = preg_replace('/\s+/', ' ', $redeem_date);
        if( $redeem_date )
        {
            $redeem_date = explode(' ', $redeem_date);

            // time
            $hour_minute = explode(':', $redeem_date[1]);
            $hour = (strtolower($redeem_date[2]) == 'pm' AND $hour_minute[0] < 12 ) ? 12 + $hour_minute[0] : $hour_minute[0];
            $minute = $hour_minute[1];
            
            // date + time
            $redeem_time = $CMS->class->date->date2time($redeem_date[0]);
            $redeem_time = $CMS->class->date->add_hour($redeem_time, $hour);
            $redeem_time = $CMS->class->date->add_minute($redeem_time, $minute);
        }

        if($redeem_amount > $data['gitem_amount_remain']) { $_SESSION['error_msg'] .= $CMS->lang['error_amount_redeem']; return false; }
        $amount_remain = round($data['gitem_amount_remain'] - $redeem_amount, 2);

        $CMS->class->logs->old_data = $data;
        //Update info
        $DB->query("UPDATE ".root_table."giftcard_items SET gitem_amount_remain='{$amount_remain}', gitem_time_update='{$redeem_time}' WHERE gitem_id='{$data['gitem_id']}'");

        //Clear cache
        $CMS->class->cache->mdelete('giftcard_items');

        // Save logs
        $CMS->class->logs->key = "redeem_{$data['gitem_id']}";
        // Create log
        $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['use_giftcard_item_successful']} <b>{$data['giftcard_code']}</b>")."<br />";
        // Get info
        $data_new = self::getInfo($data['gitem_id']);
        // Step 2: Save detail logs
        $CMS->class->logs->key = "redeem_{$data_new['gitem_id']}";
        $CMS->class->logs->save_detail("giftcard_items",$data_new['gitem_id'],$data_new);

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
            $data = $DB->fetch_data("SELECT {$field_return} FROM ".root_table."giftcard_items WHERE gitem_deleted=0 AND gitem_id='{$record_id}' {$sql_add} LIMIT 1", 'giftcard_items')[0];

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
        $results = $DB->fetch_data("SELECT G.*, C.cus_full_name as cus_name, C.cus_email FROM ".root_table."giftcard_items as G LEFT JOIN ".root_table."customer AS C ON G.cus_id=C.cus_id WHERE gitem_deleted=0 AND (giftcard_code LIKE '%{$key_search}%' OR C.cus_full_name LIKE '%{$key_search}%' OR C.cus_email LIKE '%{$key_search}%' OR gitem_code_old LIKE '%{$key_search}%')  ORDER BY G.gitem_time DESC", 'giftcard_items.customer');

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

    static function getDataGiftcard()
    {
        global $CMS, $DB;

        // Key product group for gift card
        $key = "gift_cards";
        $product_group = $CMS->product_group->getInfo($key,"product_group_id");
        $results = $DB->fetch_data("SELECT * FROM ".root_table."product WHERE product_deleted=0 AND product_show=1 AND product_group='{$product_group}' ORDER BY product_time DESC", 'product');

        $output = [];

        if(!$results) return $output;

        foreach($results as $data)
        {
            $output[] = $CMS->product->convertvalue($data);
        }

        return $output;
    }

    static function add()
    {
        global $CMS, $DB;

        // input
        $product_id = intval($CMS->input['product_id']);
        $gitem_amount_remain = $gitem_amount = floatval($CMS->input['gitem_amount']);
        $gitem_code_old = $CMS->input['gitem_code_old'];
        $gitem_note = $CMS->class->editor->input("gitem_note");

        // Check code old
        if(self::checkCodeOld($gitem_code_old))
        {
            $_SESSION['msg'] = "<b>[{$gitem_code_old}]</b> ".$CMS->lang['error_gitem_code_old_exist'];
            return false;
        }

        // information customer
        $cus_name = $CMS->input['cus_name'];
        $cus_email = $CMS->input['cus_email'];
        $cus_phone = $CMS->input['cus_phone'];
        $cus_id = 0;
        if(!$customer = $CMS->customer->getCustomerByPhone($cus_phone))
        {
            // Data customer
            $data_cus['cus_email'] = $cus_email;
            $data_cus['cus_full_name'] = $cus_name;
            $data_cus['cus_phone'] = $cus_phone;
            // $data_cus['cus_password'] = $data_cus['cus_repassword'] = $CMS->class->random->character(9);
            $cus_id = $CMS->customer->createAccount($data_cus);
            $cus_id = intval($cus_id);
        }else
        {
            $cus_id = $customer['cus_id'];
        }

        // Insert giftcard item
        require_once root_path."vendor/autoload.php"; 
        // Check and create folder 
        $CMS->class->image->check_folder_img("giftcards","",0);
        // gitem_code
        $str = "ABCDEFGHIJKLMNOPQRSTXYZ0987654321";
        $gitem_time_update = $gitem_time = time();
        $number = rand(0,10000);
        $number2 = rand(0,10000);
        $str_rand= $str[rand(0,33)];
        $gitem_code = md5("{$product_id}-{$number}-{$str_rand}-{$gitem_time}");
        $product = $CMS->product->convertvalue($CMS->product->get_info($product_id));
        // Create image barcode
        $qrcodeSrc = $CMS->vars['upload_dir'] . "/giftcards/{$number2}_{$gitem_code}.png";
        $content_barcode = $CMS->vars['parent_domain'].'/giftcards/barcode/'.$gitem_code;
        \PHPQRCode\QRcode::png($content_barcode, $qrcodeSrc, 'L', 4, 2);

        // Link image will paste watermask
        $link_image = $CMS->vars['upload_dir']."/product/".$product['product_image'];

        //watemask giftcard
        $CMS->class->image->watermask( $qrcodeSrc, $link_image, $CMS->vars['upload_dir'] . "/giftcards/{$gitem_code}.png");

        // Unlink watermask
        @unlink($qrcodeSrc);

        // Insert database
        $check = $DB->query("INSERT INTO ".root_table."giftcard_items (product_id, gitem_code, gitem_amount, gitem_amount_remain, gitem_time, gitem_time_update, ord_id, cus_id, gitem_note, gitem_code_old) VALUES ('{$product_id}', '{$gitem_code}', '{$gitem_amount}', '{$gitem_amount_remain}', '{$gitem_time}', '{$gitem_time_update}', '0', '{$cus_id}', '{$gitem_note}', '{$gitem_code_old}')");

        $CMS->class->cache->mdelete('giftcard_items');

        if($check)
        {
            $gitem_id = $DB->last_insert_id();
            // input order
            $data['ship_service_type'] = 1; // 0: normal, 1: fast, 2: day
            $data['ship_deliver'] = 1;
            $data['ship_deliver_fee'] = 0;
            $data['service_type'] = 0;
            $data['cus_id'] = $cus_id;
            $data['ord_note'] = "Place order at: ".$CMS->class->date->date_format(time(),1);
            $data['ord_content'] = json_encode($CMS->input, JSON_UNESCAPED_UNICODE);

            // information product
            $data['product_id'][] = $product_id;
            $data['product_name'][] = $product['product_name'][$CMS->vars['default_language']];
            $data['product_cycle_type'][] = $product['product_cycle'];
            $data['product_cycle'][] = 1;
            $data['product_quantity'][] = 1;
            $data['product_price'][] = $gitem_amount_remain;
            $data['product_price_add'][] = 0;
            $data['product_tax'][] = 0;
            $data['confirm_paid'][] = 1;

            // Add order
            $return = $CMS->order->quick_add($data);
            // Update gift card code
            $DB->query("UPDATE ".root_table."giftcard_items SET giftcard_code='G{$gitem_id}', ord_id='{$return['ord_id']}' WHERE gitem_id='{$gitem_id}'");

            $CMS->class->cache->mdelete('giftcard_items');

            // Update order
            $DB->query("UPDATE ".root_table."order SET ord_status=2 WHERE ord_id='{$return['ord_id']}'");
            $_SESSION['msg'] = $CMS->lang['create_gitem_customer_successful'];
            return true;
        }else
        {
            $_SESSION['msg'] = $CMS->lang['create_gitem_customer_error'];
            return false;
        }

    }

    static function checkCodeOld($code='')
    {
        global $CMS, $DB;

        if($code)
        {
            $DB->query("SELECT 0 FROM ".root_table."giftcard_items WHERE gitem_code_old='{$code}'");
            return $DB->num_rows() ? true : false;
        }
    }

    static function control()
    {
        global $CMS;

        $output = "";
        // Xoá multi
        if ($CMS->permit["redeem_delete"] ) 
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
        
        $DB->query("UPDATE ".root_table."giftcard_items SET gitem_deleted = 1 WHERE gitem_id={$data['gitem_id']}");

        $CMS->class->cache->mdelete('giftcard_items');

        // Create log
        $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['redeem_deleted']} <b>{$data['giftcard_code']}</b>")."<br />";
        
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
                
                $DB->query("UPDATE ".root_table."giftcard_items SET gitem_deleted = 1 WHERE gitem_id={$data['gitem_id']}");
        
                $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['redeem_deleted']} <b>{$data['giftcard_code']}</b>")."<br />";
                
                $deleted = 1;
            }
        }
        
        if ( $deleted == 0 )
        {
            $CMS->class->cache->mdelete('giftcard_items');

            $_SESSION["msg"] .= "{$CMS->lang['redeem_delete_failed']}";
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

    static function edit_amount()
    {
       global $CMS, $DB;

       $data = self::getInfo($CMS->input['gitem_id']);
       $redeem_amount = $CMS->input['money_amount'];
       $redeem_time = time();

       if($redeem_amount <= 0) { $_SESSION['error_msg'] .= $CMS->lang['error_add_money_amount']; return false; }
       $amount_remain = round($data['gitem_amount_remain'] + $redeem_amount, 2);

       $CMS->class->logs->old_data = $data;
       //Update info
       $DB->query("UPDATE ".root_table."giftcard_items SET gitem_amount_remain='{$amount_remain}', gitem_time_update='{$redeem_time}' WHERE gitem_id='{$data['gitem_id']}'");

       // Save logs
       $CMS->class->logs->key = "redeem_{$data['gitem_id']}";
       // Create log
       $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['title_add_money_successful']} <b>{$data['giftcard_code']}</b>")."<br />";
        
        //Clear cache
        $CMS->class->cache->mdelete('giftcard_items');

       // Get info
       $data_new = self::getInfo($data['gitem_id']);
       // Step 2: Save detail logs
       $CMS->class->logs->key = "redeem_{$data_new['gitem_id']}";
       $CMS->class->logs->save_detail("giftcard_items",$data_new['gitem_id'],$data_new);

    }
}