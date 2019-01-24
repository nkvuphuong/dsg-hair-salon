<?php

namespace models;

use core\ezy;
use lib\input;
use lib\date;
use api\paypal;
use api\authorize;
use Inacho\CreditCard;
use models\member;

// Models
ezy::load_model("customer");
ezy::load_model("giftcards");
ezy::load_model("book");
ezy::load_model("member");

class payment
{
    /**
     * Get List tag
     * @return array
     */

    static function getOptionState( $country_code = 'US' )
    {
        global $CMS, $DB;

        //set output
        $output = [];
        // Query country
        $results = $DB->fetch_data("SELECT state_id AS id, state_code AS code, state_name  FROM ".root_table."state WHERE country_code = '{$country_code}' ORDER BY state_id ASC", 'state');

        if(!$results) return $output;

        foreach ($results as $data)
        {
            $output[] = $data;
        }

        return $output;
    }

    /**
     * Get List tag
     * @return array
     */

    static function getOptionCountry()
    {
        global $CMS, $DB;

        //set output
        $output = [];
        // Query country
        $results = $DB->fetch_data("SELECT country_iso_code AS code, country_name FROM ".root_table."country ORDER BY country_name ASC",'country');

        if(!$results) return $output;

        foreach ($results as $data)
        {
            $output[] = $data;
        }

        return $output;
    }

    static function getInfoCountry($id)
    {
        global $CMS, $DB;

        //set output
        $output = [];
        // Query country
        $results = $DB->fetch_data("SELECT country_id,country_iso_code AS code, country_name FROM ".root_table."country WHERE country_id='{$id}' OR country_iso_code='{$id}' limit 1 ",'country');

        if(!$results) return $output;
        $results = array_shift($results);
        $output= $results;

        return $output;
    }

    static function getOptionCity($coutry_id = 0)
    {
        global $CMS, $DB;

        //set output
        $output = [];
        // Query country
        if(!$coutry_id){
            $coutry_id = $CMS->vars['default_country_id'];
        }
        $results = $DB->fetch_data("SELECT city_id, city_name FROM ".root_table."city WHERE country_id='{$coutry_id}' ORDER BY city_name ASC", 'city');

        if(!$results) return $output;

        foreach ($results as $data)
        {
            $output[] = $data;
        }

        return $output;
    }


    static function getOptionCity_by_salon()
    {
        global $CMS, $DB;
 
        //set output
        $output = [];
        // Query country
        $results = $DB->fetch_data("SELECT C.city_id, C.city_name FROM ".root_table."city C RIGHT JOIN  ".root_table."store S ON C.city_id = S.city_id WHERE C.country_id='{$CMS->vars['default_country_id']}'   GROUP BY C.city_id", 'city');

        if(!$results) return $output;

        foreach ($results as $data)
        {
            $output[] = $data;
        }

        return $output;
    }



    static function getOptionDistrict($city_id=0)
    {
        global $CMS, $DB;

        //set output
        $output = [];

        // Query country
        $results = $DB->fetch_data("SELECT district_name, district_id, district_type FROM ".root_table."district WHERE city_id='{$city_id}' ORDER BY district_type DESC, district_name ASC", 'district');

        if(!$results) return $output;

        foreach ($results as $data)
        {
            $output[] = $data;
        }

        return $output;
    }

    static function checkOut()
    {
        global $CMS, $DB;
        // print "<pre>"; print_r($CMS->input);exit;
        // Check token
        if(!\lib\security::check_token())
        {
            return self::createMsg("Please refresh page (F5) then try again.");
        }


        //check discount code
        if(isset($_SESSION['discount_code']['code']) && $_SESSION['discount_code']['code'])
        {
            $checkCode = discount::checkCode($_SESSION['discount_code']['code']);
            if($checkCode['status']!='ok')
            {
                return self::createMsg($checkCode['msg']);
            }
        }


        //Input
        $data['ship_email'] = $input['ship_email'] = isset($CMS->input['ship_email']) ? $CMS->input['ship_email'] : '';
        $data['ship_phone'] = $input['ship_phone'] = isset($CMS->input['ship_phone']) ? $CMS->input['ship_phone'] : '';
        $data['ship_receive_name'] = $input['ship_full_name'] = isset($CMS->input['ship_full_name']) ? $CMS->input['ship_full_name'] : '';
        $data['ship_address'] = $input['ship_address1'] = isset($CMS->input['ship_address1']) ? $CMS->input['ship_address1'] : '';
        $input['ship_address2'] = isset($CMS->input['ship_address2']) ? $CMS->input['ship_address2'] : '';
        $data['ship_location'] = $input['ship_city'] = isset($CMS->input['ship_city']) ? $CMS->input['ship_city'] : null;
        $input['ship_state'] = isset($CMS->input['ship_state']) ? $CMS->input['ship_state'] : null;
        $input['ship_postal_code'] = isset($CMS->input['ship_postal_code']) ? $CMS->input['ship_postal_code'] : null;
        $input['ship_country'] = isset($CMS->input['ship_country']) ? $CMS->input['ship_country'] : null;
        $data['ship_service_type'] = 1; // 0: normal, 1: fast, 2: day
        $data['ship_deliver'] = 1;
        $data['ship_deliver_fee'] = 0;

        $input['send_to_friend'] = isset($CMS->input['send_to_friend']) ? intval($CMS->input['send_to_friend']) : 0;
        $input['recipient_email'] = isset($CMS->input['recipient_email']) ? $CMS->input['recipient_email'] : '';
        $input['recipient_name'] = isset($CMS->input['recipient_name']) ? $CMS->input['recipient_name'] : '';
        $input['recipient_message'] = isset($CMS->input['recipient_message']) ? $CMS->input['recipient_message'] : '';
        $input['is_giftcard'] = 1;

        // Check input
        if(!$input['ship_email']) { return self::createMsg($CMS->lang['error_email']); }
        if(!$input['ship_full_name']) { return self::createMsg($CMS->lang['error_full_name']); }
        if(!$input['ship_phone']) { return self::createMsg($CMS->lang['error_phone']); }
        if($input['send_to_friend']) 
        { 
            if(!$input['recipient_email'])
            {
                return self::createMsg($CMS->lang['error_email_recipient']); 
            }else
            {
                if (!filter_var($input['recipient_email'], FILTER_VALIDATE_EMAIL)) {  return self::createMsg($CMS->lang['invalid_email']); }
            }
        }

        // if(!$input['ship_address1']) { return self::createMsg($CMS->lang['error_address']); }
        // if(!$input['ship_city']) { return self::createMsg($CMS->lang['error_city']); }
        // if(!$input['ship_state']) { return self::createMsg($CMS->lang['error_state']); }
        // if(!$input['ship_postal_code']) { return self::createMsg($CMS->lang['error_postal_code']); }
        // if(!$input['ship_country']) { return self::createMsg($CMS->lang['error_country']); }

        // Set session
        $_SESSION['shipping'] = $input;

        // Convert data
        $data['service_type'] = 0;
        $data['cus_id'] = isset($_SESSION['member']['cus_id']) ? intval($_SESSION['member']['cus_id']) : 0;
        $data['ord_note'] = "Place order at: ".$CMS->class->date->date_format(time(),1);
        $data['ord_content'] = json_encode($input, JSON_UNESCAPED_UNICODE);
        // $data['is_shipping'] = 1;
        // $data['trx_discount_type'] = 0;
        // $data['ord_tax'] = 10;


        // Convert data service
        foreach ($_SESSION['mycart'] as $product) 
        {
            $data['product_id'][] = $product['product_id'];
            $data['product_name'][] = $product['product_name'];
            $data['product_cycle_type'][] = $product['product_cycle'];
            $data['product_cycle'][] = 1;
            $data['product_quantity'][] = $product['quantity'];
            $data['product_price'][] = $product['price'];
            $data['product_price_add'][] = $product['price_add'];
            $data['product_tax'][] = $product['product_tax'];
            $data['product_discount_type'][] = isset($_SESSION['discount_code']['type']) ? $_SESSION['discount_code']['type'] : 0;
            $data['product_discount_value'][] = isset($_SESSION['discount_code']['value']) ? $_SESSION['discount_code']['value'] : 0;
        }

        //For discount
        $data['trx_discount_type'] = 1;
        $cartResult = cart::cartTotal(1,'');
        $data['trx_discount_value'] = $cartResult[5]*1;

        // Create customer
        // Check customer
        if(!$data['cus_id'])
        {
            if(!$customer = customer::getCustomerByPhone($input['ship_phone']))
            {
                // Data customer
                $data_cus['cus_email'] = $input['ship_email'];
                $data_cus['cus_full_name'] = $input['ship_full_name'];
                $data_cus['cus_phone'] = book::trimPhone($input['ship_phone']);
                $data_cus['cus_address'] = $input['ship_address1'];
                $data_cus['cus_address2'] = $input['ship_address2'];
                $data_cus['cus_city'] = $input['ship_city'];
                $data_cus['cus_district'] = $input['ship_state'];
                $data_cus['cus_country'] = $input['ship_country'];
                // $data_cus['cus_password'] = $data_cus['cus_repassword'] = $CMS->class->random->character(9);
                $cus_id = customer::createAccount($data_cus);
                $data['cus_id'] = intval($cus_id);
            }else
            {
                $data['cus_id'] = $customer['cus_id'];
            }
        }

        // payment method Cash on delievery (COD)
        if($CMS->input['payment_method']) {
//            $data['order_payment_method'] = $CMS->input['payment_method'];
        }

        if (isset($CMS->input['payment_method'] ) && $CMS->input['payment_method'] == "COD" )
        {
            $data['order_payment_method'] = 2;
        }
        // Add order
        $return = $CMS->order->quick_add($data);

        //Update used times discount code
        discount::updateUsed($return['ord_id']);
        
        // unset session messgage
        $_SESSION["msg"] = "";

        // rewrite shipping info for insert logs
        $data['shipping_info'] = $input;
        if($return)
        {
            // Check payment method is Cash on delievery (COD)
            if (isset($data['order_payment_method']) && $data['order_payment_method'] == 2 )
            {
                // return success
                return "/payment/success";
            }

            // Create session order
            $_SESSION['order'] = $return;

            // Check active paypal
            if($CMS->vars['payment_active'])
            {
                // Insert log subscription
                $sublog_id = self::addRequest(json_encode($data, JSON_UNESCAPED_UNICODE), 'order_'.$return['ord_id']);
                // paypal init
                paypal::init();
                paypal::$redirect_default = "/giftcards";
                list($response, $redirect_link) = paypal::payment($return, $_SESSION['mycart'], $sublog_id);
                
                // Update log subscription
                self::updateResponse($sublog_id, $response);

                $response = json_decode($response,1);
                // $redirect_link = $response['links'][1]['href'];
                $_SESSION['paypal'] = $response;
            }else
            {
                $redirect_link = "/payment/checksuccess";
            }
        }else {
            // Error
            $_SESSION['error_msg'] = $CMS->lang['error_insert_order'];
            $redirect_link = isset($_SESSION['curr_info']['type_page']) == 1 ? "/giftcards" : "/payment";
        }

        // return 
        return $redirect_link;
    }

    static function createMsg($message="",$status="error")
    {
        if($status == "error")
        {
            $_SESSION['error_msg']  = isset($_SESSION['error_msg']) ? $_SESSION['error_msg'] : '';
            $_SESSION['error_msg'] .= $message;
            return true;
        }else
        {
            $_SESSION['msg']  = isset($_SESSION['msg']) ? $_SESSION['msg'] : '';
            $_SESSION['msg'] .= $message;
            return true;
        }
    }

    static function checkCurrentTransaction($data=[])
    {
        global $CMS, $DB;
        
        // Check error
        $check = 1;
        // Check with paypal
        if($CMS->vars['payment_active'])
        {
            if($data['paymentId'] !== $_SESSION['paypal']['id'])
            {
                $check = 0;
            }
        }

        if($check == 1)
        {
            // Update status order
            if($_SESSION['order'] and $CMS->vars['payment_active'])
            {
                $id = $_SESSION['order']['ord_id'];

                // paypal init
                paypal::init();
                paypal::process("order_{$id}");

                if( paypal::$status != 'success' )
                {
                    $_SESSION['curr_info']['type_page'] = 1;
                    return false;
                }
                
                // Before change
                $CMS->class->logs->key = "order_{$id}";    
                $CMS->class->logs->old_data = $_SESSION['order'];
                $CMS->class->logs->insert("order_{$id}");

                // update status
                $DB->query("UPDATE ".root_table."order SET payment_status=1, ord_status=2, ord_time_update='".time()."' WHERE ord_id='{$id}'");
                // Update order item
                $DB->query("UPDATE ".root_table."order_item SET ordi_status=2 WHERE ord_id='{$id}'");

                // Update log key
                $DB->query("UPDATE ".root_table."subscription_logs SET sublog_status='success' WHERE logs_key='order_{$id}'");

                // After change
                $CMS->class->logs->key = "order_{$id}";
                // Get info
                $new_order = $CMS->order->get_info($id);
                $CMS->class->logs->save_detail("order",$id,$new_order); 

                // Insert giftcard item
                require_once root_path."vendor/autoload.php"; 
                // Check and create folder 
                $CMS->class->image->check_folder_img("giftcards","",0);
                
                // Input
                $str = "ABCDEFGHIJKLMNOPQRSTXYZ0987654321";
                $gitem_time = time();

                foreach ($_SESSION['mycart'] as $id => $product) 
                {
                    for($x=1;$x<=$product['quantity'];$x++)
                    {
                        $product_id = $id;
                        $number = rand(0,10000);
                        $number2 = rand(0,10000);
                        $str_rand= $str[rand(0,33)];
                        $gitem_code = md5("{$product_id}-{$number}-{$str_rand}-{$gitem_time}");
                        $gitem_amount_remain = $gitem_amount = $product['price_new'];
                        $gitem_time_update = $gitem_time;
                        $ord_id = $_SESSION['order']['ord_id'];
                        $cus_id = intval($_SESSION['order']['cus_id']);
// 
                        // Create image barcode
                        $qrcodeSrc = $CMS->vars['upload_dir'] . "/giftcards/{$number2}_{$gitem_code}.png";
                        $content_barcode = $CMS->vars['root_domain'].'/giftcards/barcode/'.$gitem_code;
                        \PHPQRCode\QRcode::png($content_barcode, $qrcodeSrc, 'L', 4, 2);

                        // Link image will paste watermask
                        $link_image = $CMS->vars['upload_dir']."/product/".$product['product_image'];

                        //watemask giftcard
                        $CMS->class->image->watermask( $qrcodeSrc, $link_image, $CMS->vars['upload_dir'] . "/giftcards/{$gitem_code}.png");

                        // Unlink watermask
                        @unlink($qrcodeSrc);
                        // Insert database
                        $DB->query("INSERT INTO ".root_table."giftcard_items (product_id, gitem_code, gitem_amount, gitem_amount_remain, gitem_time, gitem_time_update, ord_id, cus_id) VALUES ('{$product_id}', '{$gitem_code}', '{$gitem_amount}', '{$gitem_amount_remain}', '{$gitem_time}', '{$gitem_time_update}', '{$ord_id}', '{$cus_id}')");
                        $gitem_id = $DB->last_insert_id();
                        // Update gift card code
                        $DB->query("UPDATE ".root_table."giftcard_items SET giftcard_code='G{$gitem_id}' WHERE gitem_id='{$gitem_id}'");
                        
                    }

                }

                $type = $_SESSION['shipping']['send_to_friend'] ? 1 : 0;

                // sendmail
                self::send_email($type);
                
            }

            // unset session
            unset($_SESSION['order']);
            unset($_SESSION['shipping']);
            unset($_SESSION['mycart']);
            unset($_SESSION['paypal']);
            unset($_SESSION['customer']);

            return true;
        }else
        {
            $_SESSION['error_msg'] = $CMS->lang['error_invalid_transaction'];
            return false;
        }
    }

    static function addRequest($data='', $logs_key="", $sublog_gateway = 'paypal')
    {
        global $CMS, $DB;

        $sub_id = 0;
        $sublog_request = trim($data);
        $sublog_request_time = time();

        $sql = "INSERT INTO ".root_table."subscription_logs (sub_id, sublog_request, sublog_request_time, sublog_gateway, logs_key) VALUES ('{$sub_id}', '{$sublog_request}', '{$sublog_request_time}', '{$sublog_gateway}', '{$logs_key}')";

        if($DB->query($sql))
        {
            return $DB->last_insert_id();
        }

        return false;
    }

    static function updateResponse($sublog_id=0,$data='')
    {
        global $CMS, $DB, $member;
        $sublog_id = intval($sublog_id);
        $sublog_response = trim($data);
        $sublog_response_time = time();
        // Insert data
        return $DB->query("UPDATE ".root_table."subscription_logs SET sublog_response='{$sublog_response}', sublog_response_time='{$sublog_response_time}' WHERE sublog_id='{$sublog_id}'");
    }

    static function send_email($type=0)
    {
        global $CMS, $DB, $member;

        if(!$type)
        {
            $time = $CMS->class->date->date_format(time());
            $CMS->email->email_template = "order_success";
            $CMS->email->email_to = $_SESSION['shipping']['ship_email'];
            $CMS->email->email_toname = $_SESSION['shipping']['ship_full_name'];

            // email_from
            $CMS->email->data['date_send'] =  $time;
            $CMS->email->data['cus_name'] =  $_SESSION['shipping']['ship_full_name'];
            
            $ord_id = $_SESSION['order']['ord_id'];
            $arr_item = giftcards::getGitemByOrder($ord_id);

            $table_html = "<table class=\"table table-bordered\" width=\"100%\"><tbody><tr bgcolor=rgb(230, 229, 229) valign='middle' align='center'><td width='80%' style='text-align: center;'>Item</td><td width='20%' style='text-align: center;'>Price</td></tr>";
            $total = 0;
            foreach ($arr_item as $id => $data) 
            {
                $total += $data['gitem_amount_remain'];
                $amount_remain = $CMS->class->input->currency($data['gitem_amount_remain']);
                $data['image'] = $CMS->vars['upload_url']."/giftcards/".$data['gitem_code'].".png";
                $table_html .= "<tr style='text-align: center;'><td><img src=\"{$data['image']}\" style=\"max-width:80%; max-height: 300px;\" /><br/>{$data['product_name']}</td><td>{$amount_remain}</td></tr>";
            }
            $total_show = $CMS->class->input->currency($total);
            $table_html .="<tr><td style='text-align: right;'>Sub-Total</td><td style='text-align: center;'><strong>{$total_show}</strong></td></tr>";
            $table_html .= "</tbody></table>";
            $CMS->email->data['table_content'] = $table_html;
            $CMS->email->data['website_name'] = $CMS->vars['website_title'];
            $CMS->email->quick_send(0,0);

        }else
        {
            // Send mail for payer
            $time = $CMS->class->date->date_format(time());
            $CMS->email->email_template = "email_send_payer";
            $CMS->email->email_to = $_SESSION['shipping']['ship_email'];
            $CMS->email->email_toname = $_SESSION['shipping']['ship_full_name'];

            // data
            $CMS->email->data['date_send'] =  $time;
            $CMS->email->data['cus_name'] =  $_SESSION['shipping']['ship_full_name'];
            $CMS->email->data['cus_email_to'] =  $_SESSION['shipping']['recipient_email'];
            $CMS->email->data['website_name'] = $CMS->vars['website_title'];
            $CMS->email->quick_send(0,0);
            

            // Send to friend of payer
            $CMS->email->email_template = "email_send_to_friend";
            $CMS->email->email_to = $_SESSION['shipping']['recipient_email'];
            $CMS->email->email_toname = $_SESSION['shipping']['recipient_name'];

            $CMS->email->data['date_send'] =  $time;
            $CMS->email->data['cus_name_from'] =  $_SESSION['shipping']['ship_full_name'];
            $CMS->email->data['cus_email_from'] =  $_SESSION['shipping']['ship_email'];
            $CMS->email->data['message'] = $_SESSION['shipping']['recipient_message'] ? "Message: ".$_SESSION['shipping']['recipient_message'] : "";
            $CMS->email->data['website_name'] = $CMS->vars['website_title'];
            
            $ord_id = $_SESSION['order']['ord_id'];
            $arr_item = giftcards::getGitemByOrder($ord_id);

            $table_html = "<table class=\"table table-bordered\" width=\"100%\"><tbody><tr bgcolor=rgb(230, 229, 229) valign='middle' align='center'><td width='80%' style='text-align: center;'>Item</td><td width='20%' style='text-align: center;'>Price</td></tr>";
            $total = 0;
            foreach ($arr_item as $id => $data) 
            {
                $total += $data['gitem_amount_remain'];
                $amount_remain = $CMS->class->input->currency($data['gitem_amount_remain']);
                $data['image'] = $CMS->vars['upload_url']."/giftcards/".$data['gitem_code'].".png";
                $table_html .= "<tr style='text-align: center;'><td><img src=\"{$data['image']}\" style=\"max-width:80%; max-height: 300px;\" /><br/>{$data['product_name']}</td><td>{$amount_remain}</td></tr>";
            }
            $total_show = $CMS->class->input->currency($total);
            $table_html .="<tr><td style='text-align: right;'>Sub-Total</td><td style='text-align: center;'><strong>{$total_show}</strong></td></tr>";
            $table_html .= "</tbody></table>";
            $CMS->email->data['table_content'] = $table_html;
            $CMS->email->quick_send(0,0);

        }

        // Send admin
        if($CMS->vars['company_email'])
        {
            $CMS->email->email_template = "notification_order_admin";
            $CMS->email->email_to = $CMS->vars['company_email'];
            $CMS->email->email_toname = "Admin";

            $CMS->email->data['cus_name'] = $_SESSION['shipping']['ship_full_name'];
            $CMS->email->data['cus_email'] = $_SESSION['shipping']['ship_email'];
            $CMS->email->data['cus_phone'] = $_SESSION['shipping']['ship_phone'];
            $ord_id = $_SESSION['order']['ord_id'];
            $CMS->email->data['link_order'] = "{$CMS->vars['root_domain']}/acp/?site=order&act=show&id={$ord_id}";
            
            $CMS->email->quick_send(0,0);
        }

        return;
    }

    static function checkOutVn()
    {
        global $CMS, $DB, $member;

        // Check token
        if(!\lib\security::check_token())
        {
            return self::createMsg("Please refresh page (F5) then try again.");
        }

        //check discount code
        if(isset($_SESSION['discount_code']['code']) && $_SESSION['discount_code']['code'])
        {
            $checkCode = discount::checkCode($_SESSION['discount_code']['code']);
            if($checkCode['status']!='ok')
            {
                return self::createMsg($checkCode['msg']);
            }
        }

        //Input
        $data['same_info'] = isset($CMS->input['same_info']) ? intval($CMS->input['same_info']) : null;
        // payer information
        $data['cus_full_name'] = isset($CMS->input['cus_full_name']) ? $CMS->input['cus_full_name'] : '';
        $data['cus_email'] = isset($CMS->input['cus_email']) ? $CMS->input['cus_email'] : '';
        $data['cus_phone'] = isset($CMS->input['cus_phone']) ? $CMS->input['cus_phone'] : '';
        $data['cus_address'] = isset($CMS->input['cus_address1']) ? $CMS->input['cus_address1'] : '';
        $data['cus_city'] = isset($CMS->input['cus_city']) ? intval($CMS->input['cus_city']) : 0;
        $data['cus_district'] = isset($CMS->input['cus_district']) ? intval($CMS->input['cus_district']) : 0;
        // shipping information
        if($data['same_info'])
        {
            $data['ship_full_name'] = isset($CMS->input['cus_full_name']) ? $CMS->input['cus_full_name'] : '';
            $data['ship_phone'] = isset($CMS->input['cus_phone']) ? $CMS->input['cus_phone'] : '';
            $data['ship_address'] = isset($CMS->input['cus_address1']) ? $CMS->input['cus_address1'] : '';
            $data['ship_city'] = isset($CMS->input['cus_city']) ? $CMS->input['cus_city'] : null;
            $data['ship_district'] = isset($CMS->input['cus_district']) ? $CMS->input['cus_district'] : null;
        }else
        {
            $data['ship_full_name'] = isset($CMS->input['ship_full_name']) ? $CMS->input['ship_full_name'] : '';
            $data['ship_phone'] = isset($CMS->input['ship_phone']) ? $CMS->input['ship_phone'] : '';
            $data['ship_address'] = isset($CMS->input['ship_address1']) ? $CMS->input['ship_address1'] : '';
            $data['ship_city'] = isset($CMS->input['ship_city']) ? $CMS->input['ship_city'] : null;
            $data['ship_district'] = isset($CMS->input['ship_district']) ? $CMS->input['ship_district'] : null;
        }

        $data['cus_country'] = $data['ship_country'] = isset($CMS->vars['default_country']) ? $CMS->vars['default_country'] : null;
        $data['payment_method'] = isset($CMS->input['payment_method']) ? intval($CMS->input['payment_method']) : 0;

        $data['ship_service_type'] = 1; // 0: normal, 1: fast, 2: day
        $data['ship_deliver'] = 1;
        $data['ship_deliver_fee'] = 0;

        // Check input
        if(!$data['cus_email']) { return self::createMsg($CMS->lang['error_email']); }
        if(!$data['cus_full_name']) { return self::createMsg($CMS->lang['error_full_name']); }
        if(!$data['cus_phone']) { return self::createMsg($CMS->lang['error_phone']); }
        if(!$data['cus_city']) { return self::createMsg($CMS->lang['error_city_vn']); }
        if(!$data['cus_district']) { return self::createMsg($CMS->lang['error_district_vn']); }


        // Convert data
        $data['service_type'] = 0;
        $data['cus_id'] = isset($_SESSION['member']['cus_id']) ? intval($_SESSION['member']['cus_id']) : 0;
        $data['ord_note'] = "";
        $data['ord_content'] = json_encode($data, JSON_UNESCAPED_UNICODE);
        // $data['is_shipping'] = 1;
        // $data['trx_discount_type'] = 0;
        // $data['ord_tax'] = 10;

        $data['cart_total'] = 0;
        // Convert data service
        foreach ($_SESSION['mycart'] as $product) 
        {
            $data['product_id'][] = $product['product_id'];
            $data['product_name'][] = $product['product_name'];
            $data['product_cycle_type'][] = $product['product_cycle'];
            $data['product_cycle'][] = 1;
            $data['product_quantity'][] = $product['quantity'];
            $data['product_price'][] = $product['price'];
            $data['cart_total'] += $product['quantity'] * $product['price'];
        }

        //For discount
        $data['trx_discount_type'] = 1;
        $cartResult = cart::cartTotal(1);
        $data['trx_discount_value'] = $cartResult[5]*1;

        // Create customer
        // Check customer
        if(!$data['cus_id'])
        {
            if(!$customer = customer::getInfo($data['cus_email']))
            {
                // Data customer
                $data_cus['cus_email'] = isset($data['cus_email']) ? $data['cus_email'] : '';
                $data_cus['cus_full_name'] = isset($data['cus_full_name']) ? $data['cus_full_name'] : '';
                $data_cus['cus_phone'] = isset($data['cus_phone']) ? $data['cus_phone'] : '';
                $data_cus['cus_address'] = isset($data['cus_address']) ? $data['cus_address'] : '';
                $data_cus['cus_city'] = isset($data['ship_city']) ? $data['ship_city'] : null;
                $data_cus['cus_district'] = isset($data['cus_district']) ? $data['cus_district'] : null;
                $data_cus['cus_country'] = isset($data['cus_country']) ? $data['cus_country'] : null;
                // $data_cus['cus_password'] = $data_cus['cus_repassword'] = $CMS->class->random->character(9);
                $cus_id = customer::createAccount($data_cus);
                $data['cus_id'] = intval($cus_id);
            }else
            {
                $data['cus_id'] = $customer['cus_id'];
            }
        }

        // payment method
        if ( $data['payment_method'] == "3" or $data['payment_method'] == "4" )
        {
            $data['order_payment_method'] = 3;
        }else
        {
            $data['order_payment_method'] = $data['payment_method'];
        }

        // Set session
        $_SESSION['shipping'] = $data;
// print "<pre>";print_r($CMS->vars);exit;
        // Add order
        $return = $CMS->order->quick_add($data);

// print "<pre>";print_r($data);exit;
        // unset session messgage
        $_SESSION["msg"] = "";

        // rewrite shipping info for insert logs
        // $data['shipping_info'] = $input;
        if($return)
        {
            // Create session order
            $_SESSION['order'] = $return;
            
            // Send email Customer
            self::sendmailVn();
            // Check payment method is Cash on delievery (COD)
            if ( $data['payment_method'] == 2 or  $data['payment_method'] == 1 ) 
            {
                // return success
                return "/payment/success";
            }else
            {
                // Thanh toan qua ngan luong
                if($data['payment_method'] == 4)
                {
                    // Insert log subscription
                    $sublog_id = self::addRequest(json_encode($data, JSON_UNESCAPED_UNICODE), 'order_'.$return['ord_id'], "ngan_luong");
                    $_SESSION['order']['sublog_id'] = $sublog_id;
                    
                    // // total de test tien
                    // $data['cart_total'] = 2000;

                    // Ngân lượng
                    $receiver = $CMS->vars['nl_receiver_email'];
                    //Mã đơn hàng 
                    $order_code='NL_'.time();
                    //Khai báo url trả về 
                    $return_url= "{$CMS->vars['root_domain']}/payment/checksuccess";
                    // Link nut hủy đơn hàng
                    $cancel_url= "{$CMS->vars['root_domain']}/payment/cancel";  
                    //Giá của cả giỏ hàng 
                    $txh_name =$data['cus_full_name'];  
                    $txt_email =$data['cus_email'];    
                    $txt_phone =$data['cus_phone'];    
                    $price =(int)$data['cart_total'];     
                    //Thông tin giao dịch
                    $transaction_info="Thong tin giao dich";
                    $currency= "vnd";
                    $quantity=1;
                    $tax=0;
                    $discount=0;
                    $fee_cal=0;
                    $fee_shipping=0;
                    $order_description="Thong tin don hang: ".$order_code;
                    $buyer_info=$txh_name."*|*".$txt_email."*|*".$txt_phone;
                    $affiliate_code="";

                    // Load info ngan luong
                    $CMS->api->nganluong->LoadMerchant();
                    //Tạo link thanh toán đến nganluong.vn
                    // $redirect_link= $CMS->api->nganluong->buildCheckoutUrlExpand($return_url, $receiver, $transaction_info, $order_code, $price, $currency, $quantity, $tax, $discount , $fee_cal,    $fee_shipping, $order_description, $buyer_info , $affiliate_code);

                    $redirect_link = $CMS->api->nganluong->buildCheckoutUrlExpand_2($return_url, $cancel_url, $receiver, $transaction_info, $order_code, $price, $currency, $quantity, $tax, $discount , $fee_cal, $fee_shipping, $order_description, $buyer_info , $affiliate_code);
                    // print $redirect_link; exit;

                    return $redirect_link;
                }else if($data['payment_method'] == 3)
                {
                    // Bảo Kim
                    // Insert log subscription
                    $sublog_id = self::addRequest(json_encode($data, JSON_UNESCAPED_UNICODE), 'order_'.$return['ord_id'], "bao_kim");
                    $_SESSION['order']['sublog_id'] = $sublog_id;
                    // total de test tien
                    $data['cart_total'] = 10000;
                    // params
                    $params = array(
                             // order info
                             'order_id' => $return['ord_name'],
                             'total_amount' => $data['cart_total'],
                             'order_description' => $order_description,

                             'tax_fee' => '0',
                             'shipping_fee' => '0',

                             // buyer info
                             'payer_name' => $data['cus_full_name'],
                             'payer_email' => $data['cus_email'],
                             'payer_phone_no' => $data['cus_phone'],
                             'shipping_address' => $data['cus_address'],

                             'url_success' => "{$CMS->vars['root_domain']}/payment/checksuccess",
                             'url_cancel' => "{$CMS->vars['root_domain']}/payment/cancel",

                         );
                    // Build link
                    $redirect_link= $CMS->api->baokim->createRequestUrl($params);
                    return $redirect_link;

                }else if($data['payment_method'] == 5)
                {
                    // Paypal
                    // Insert log subscription
                    $sublog_id = self::addRequest(json_encode($data, JSON_UNESCAPED_UNICODE), 'order_'.$return['ord_id'], "paypal");
                    $_SESSION['order']['sublog_id'] = $sublog_id;
                    // total de test tien
                    $data['cart_total'] = 10000;
                    // params
                    // $params = array(
                    //          // order info
                    //          'order_id' => $return['ord_name'],
                    //          'total_amount' => $data['cart_total'],
                    //          'order_description' => $order_description,

                    //          'tax_fee' => '0',
                    //          'shipping_fee' => '0',

                    //          // buyer info
                    //          'payer_name' => $data['cus_full_name'],
                    //          'payer_email' => $data['cus_email'],
                    //          'payer_phone_no' => $data['cus_phone'],
                    //          'shipping_address' => $data['cus_address'],

                    //          'url_success' => "{$CMS->vars['root_domain']}/payment/checksuccess",
                    //          'url_cancel' => "{$CMS->vars['root_domain']}/payment/cancel",

                    //      );
                    // Build link
                    paypal::init();
                    list($response, $redirect_link) = paypal::payment($return, $_SESSION['mycart'], $sublog_id);
                    
                    // Update log subscription
                    self::updateResponse($sublog_id, $response);

                    $response = json_decode($response,1);
                    $_SESSION['paypal'] = $response;

                    return $redirect_link;

                }

            }

        }else {
            // Error
            $_SESSION['error_msg'] = $CMS->lang['error_insert_order'];
            $redirect_link = "/payment";
        }

        // return 
        return $redirect_link;
    }

    static function checkCurrentTransactionVn($data=[])
    {
        global $CMS, $DB;
        // Check error
        $check = 1;
        if($_SESSION['shipping']['payment_method'] == 3)
        {
            // Bảo Kim
            $arr_status = array(4,13);
            if(isset($data['transaction_status']) and in_array($data['transaction_status'], $arr_status))
            {
                $check = 1;
            }
        }

        if($_SESSION['shipping']['payment_method'] == 4)
        {
            // Ngân Lượng
            if(isset($data['payment_id']))
            {
                $check = 1;
            }
        }

        if($check == 1)
        {
            $sublog_data = json_encode($data, JSON_UNESCAPED_UNICODE);
            // Update log subscription
            self::updateResponse($_SESSION['order']['sublog_id'], $sublog_data);

            // Update status order
            $id = $_SESSION['order']['ord_id'];
            // Before change
            $CMS->class->logs->key = "order_{$id}";    
            $CMS->class->logs->old_data = $_SESSION['order'];
            $CMS->class->logs->insert("order_{$id}");
            // update status
            $DB->query("UPDATE ".root_table."order SET payment_status=1, ord_status=2, ord_time_update='".time()."' WHERE ord_id='{$id}'");
            // Update order item
            $DB->query("UPDATE ".root_table."order_item SET ordi_status=2 WHERE ord_id='{$id}'");

            // After change
            $CMS->class->logs->key = "order_{$id}";
            // Get info
            $new_order = $CMS->order->get_info($id);
            $CMS->class->logs->save_detail("order",$id,$new_order); 

            return true;
        }else
        {
            $_SESSION['error_msg'] = $CMS->lang['error_invalid_transaction'];
            return false;
        }
    }

    static function sendmailVn()
    {
        global $CMS, $DB;

        $time = $CMS->class->date->date_format(time());
        $CMS->email->email_template = "order_success";
        $CMS->email->email_to = $_SESSION['shipping']['cus_email'];
        $CMS->email->email_toname = $_SESSION['shipping']['cus_full_name'];

        // email_from
        $CMS->email->data['date_send'] =  $time;
        $CMS->email->data['cus_name'] =  $_SESSION['shipping']['cus_full_name'];
        
        $ord_id = $_SESSION['order']['ord_id'];
        $arr_item = self::getItemByOrder($ord_id);

        $table_html = "<table class=\"table table-bordered\" width=\"100%\"><tbody><tr bgcolor='#e0e0e0' valign='middle' align='center'><td width='80%' style='text-align: center;'>Item</td><td width='20%' style='text-align: center;'>Quantity</td><td width='20%' style='text-align: center;'>Price</td></tr>";
        $total = 0;
        foreach ($arr_item as $id => $data) 
        {
            $total += $data['total_price'];
            $amount_remain = $CMS->class->input->currency($data['total_price']);
            $data['image'] = $CMS->vars['upload_url']."/product/".$data['product_image'];
            $table_html .= "<tr style='text-align: center;'><td><img src=\"{$data['image']}\" style=\"max-width:80%; max-height: 300px;\" /><br/>{$data['product_name']}</td><td>{$data['quantity']}</td><td>{$amount_remain}</td></tr>";
        }
        $total_show = $CMS->class->input->currency($total);
        $table_html .="<tr><td style='text-align: right;'>Sub-Total</td><td style='text-align: center;'><strong>{$total_show}</strong></td></tr>";
        $table_html .= "</tbody></table>";
        $CMS->email->data['table_content'] = $table_html;
        $CMS->email->data['website_name'] = $CMS->vars['website_title'];
        $CMS->email->quick_send(0,0);
        // return;
        

    }

    static function getItemByOrder($ord_id=0)
    {
        global $CMS, $DB;

        $DB->query("SELECT SUM(ordi_price) as total_price, ordi_name as product_name, COUNT(O.product_id) as quantity, P.product_image, ordi_total_tax as total_tax, ordi_total_discount as total_discount, ordi_tax FROM ".root_table."order_item as O LEFT JOIN ".root_table."product as P ON O.product_id = P.product_id WHERE O.ord_id='{$ord_id}' AND ordi_deleted = 0 GROUP BY O.product_id ORDER BY ordi_id ASC");
        $output = [];
        while ($result = $DB->fetch_array()) 
        {
            $output[] = $result;
        }

        return $output;

    }

    static function updateSublogsStatus($status = "")
    {
        global $CMS, $DB;
        
        if($_SESSION['order'])
        {
            $id = $_SESSION['order']['ord_id'];
            $DB->query("UPDATE ".root_table."subscription_logs SET sublog_status='{$status}' WHERE logs_key='order_{$id}'");
        }
        
        return;
    }

    static function checkOutEcommerce()
    {
        global $CMS, $DB, $member;

        // Check token
        if( !\lib\security::check_token() )
        {
            self::createMsg("Please refresh page (F5) then try again.");
            return false;
        }

        // Check discount code
        if( isset($_SESSION['discount_code']['code']) && $_SESSION['discount_code']['code'] )
        {
            $checkCode = discount::checkCode($_SESSION['discount_code']['code']);
            if($checkCode['status']!='ok')
            {
                self::createMsg($checkCode['msg']);
                return false;
            }
        }

        /*
        * ThamLV-Y2018M8D15: General checkout info
        */
        $data['same_info'] = 0;
        $data['ord_note'] = isset($CMS->input['ord_note']) ? $CMS->input['ord_note'] : '';

        // shipping info
        $data['ship_first_name'] = isset($CMS->input['ship_first_name']) ? $CMS->input['ship_first_name'] : '';
        $data['ship_last_name'] = isset($CMS->input['ship_last_name']) ? $CMS->input['ship_last_name'] : '';
        $data['ship_email'] = isset($CMS->input['ship_email']) ? $CMS->input['ship_email'] : '';
        $data['ship_phone'] = isset($CMS->input['ship_phone']) ? \models\book::trimPhone($CMS->input['ship_phone']) : '';
        $data['ship_company']=isset($CMS->input['ship_company']) ? $CMS->input['ship_company'] : '';
        $data['ship_address'] = isset($CMS->input['ship_address']) ? $CMS->input['ship_address'] : '';
        $data['ship_address2'] = isset($CMS->input['ship_address2']) ? $CMS->input['ship_address2'] : '';
        $data['ship_city'] = isset($CMS->input['ship_city']) ? $CMS->input['ship_city'] : '';

        $data['ship_country'] = isset($CMS->input['ship_country']) ? $CMS->input['ship_country'] : '';
        if( strtoupper($data['ship_country']) == 'US' )
        {
            $data['ship_province'] = isset($CMS->input['ship_state']) ? $CMS->input['ship_state'] : '';
            $data['ship_zipcode'] = isset($CMS->input['ship_zipcode']) ? $CMS->input['ship_zipcode'] : '';
        }
        else
        {
            $data['ship_province'] = isset($CMS->input['ship_province']) ? $CMS->input['ship_province'] : '';
            $data['ship_zipcode'] = isset($CMS->input['ship_postalcode']) ? $CMS->input['ship_postalcode'] : '';
        }
        $data['ship_full_name'] = $data['ship_first_name']." ". $data['ship_last_name'];
        $data['ship_method'] = isset($CMS->input['ship_method']) ? $CMS->input['ship_method'] : 0;
        $data['ord_shipping_method'] = $data['ship_method'];
        $data['ord_shipping_location'] = strtoupper($CMS->input['ship_country']) == strtoupper($CMS->vars['default_shiping_location']) ? 0 : 1;

        // Billing info, If any of the data is empty, please take delivery information
        if( !$_SESSION['customer'] && $member['cus_id'] )
        {
            $customer = \models\customer::getInfo($member['cus_id']);
            
            // Location
            $customer['cus_province'] = isset($customer['cus_province']) ? $customer['cus_province'] : '';
            $customer['cus_state'] = isset($customer['cus_state']) ? $customer['cus_state'] : $customer['cus_province'];
            $customer['cus_zipcode'] = isset($customer['cus_zip']) ? $customer['cus_zip'] : '';
            $customer['cus_postalcode'] = isset($customer['cus_postalcode']) ? $customer['cus_postalcode'] : $customer['cus_zip'];
        }
        else
        {
            $customer = $_SESSION['customer'];
        }

        $data['cus_first_name'] = !empty($customer['cus_first_name']) ? $customer['cus_first_name'] : $data['ship_first_name'];
        $data['cus_last_name'] = !empty($customer['cus_last_name']) ? $customer['cus_last_name'] : $data['ship_last_name'];
        $data['cus_email'] = !empty($customer['cus_email']) ? $customer['cus_email'] : $data['ship_email'];
        $data['cus_phone'] = !empty($customer['cus_phone']) ? \models\book::trimPhone($customer['cus_phone']) : $data['ship_phone'];
        $data['cus_company'] = !empty($customer['cus_company']) ? $customer['cus_company'] : $data['ship_company'];
        $data['cus_address'] = !empty($customer['cus_address']) ? $customer['cus_address'] : $data['ship_address'];
        $data['cus_address2'] = !empty($customer['cus_address2']) ? $customer['cus_address2'] : $data['ship_address2'];
        $data['cus_city'] = !empty($customer['cus_city']) ? $customer['cus_city'] : $data['ship_city'];

        $data['cus_country'] = !empty($customer['cus_country']) ? $customer['cus_country'] : $data['cus_country'];
        if( strtoupper($data['cus_country']) == 'US' )
        {
            $data['cus_province'] = !empty($customer['cus_state']) ? $customer['cus_state'] : '';
            $data['cus_zipcode'] = !empty($customer['cus_zipcode']) ? $customer['cus_zipcode'] : '';
        }
        else
        {
            $data['cus_province'] = !empty($customer['cus_province']) ? $customer['cus_province'] : '';
            $data['cus_zipcode'] = !empty($customer['cus_postalcode']) ? $customer['cus_postalcode'] : '';
        }
        $data['cus_full_name'] = $data['cus_first_name']." ". $data['cus_last_name'];

        // Check inputs
        $check = true;

        if( !$data['ship_first_name'] )
        {
            self::createMsg($CMS->lang['error_first_name']);
            $check = false;
        }

        if( !$data['ship_last_name'] )
        {
            self::createMsg($CMS->lang['error_last_name']);
            $check = false;
        }

        if( !$data['ship_email'] )
        {
            self::createMsg($CMS->lang['error_email']); 
            $check = false;
        }

        if( !$data['ship_address'] )
        {
            self::createMsg($CMS->lang['error_address']); 
            $check = false;
        }

        if( !$data['ship_country'] )
        {
            self::createMsg($CMS->lang['country_error']);
            $check = false;
        }

        if( strtoupper($data['ship_country']) == 'US' AND !$data['ship_province'] )
        {
            self::createMsg($CMS->lang['state_error']);
            $check = false;
        }
        
        if( !$check )
        {
            return false;
        }

        $data['service_type'] = 0; // Flag order product
        $data['cus_id'] = !empty($member['cus_id']) ? $member['cus_id'] : 0;
        $data['ord_content'] = json_encode($data, JSON_UNESCAPED_UNICODE);

        // Convert data for add order items
        $data['cart_total'] = 0;
        foreach( $_SESSION['mycart'] as $product ) 
        {
            $data['product_id'][] = $product['product_id'];
            $data['product_name'][] = $product['product_name'];
            $data['product_cycle_type'][] = $product['product_cycle'];
            $data['product_cycle'][] = 1;
            $data['product_quantity'][] = $product['quantity'];
            $data['product_price'][] = $product['price'];
            $data['cart_total'] += $product['quantity'] * $product['price'];
            $data['product_tax'][] = $product['product_tax'];
            $data['product_discount_type'][] = isset($_SESSION['discount_code']['type']) ? $_SESSION['discount_code']['type'] : 0;
            $data['product_discount_value'][] = isset($_SESSION['discount_code']['value']) ? $_SESSION['discount_code']['value'] : 0;
        }

        // For discount
        $data['trx_discount_type'] = 1;
        $cartResult = cart::cartTotal(1, '');
        $data['trx_discount_value'] = $cartResult[5]*1;

        // Payment method
        $data['payment_method'] = isset($CMS->input['payment_method']) ? intval($CMS->input['payment_method']) : 0;
        $data['order_payment_method'] = $data['payment_method'];
        if ( $data['payment_method'] == "3" or $data['payment_method'] == "4" )
        {
            $data['order_payment_method'] = 3;
        }

        // Check thanh toán authorize
        if( $data['payment_method'] == 6 )
        {
            $data['authorize_card_number'] = isset($CMS->input['authorize_card_number']) ? str_replace(" ", "", $CMS->input['authorize_card_number']) : "";
            $data['authorize_expiration_date'] = isset($CMS->input['authorize_expiration_date']) ? $CMS->input['authorize_expiration_date'] : "";
            $data['authorize_cvv_cvc'] = isset($CMS->input['authorize_cvv_cvc']) ? $CMS->input['authorize_cvv_cvc'] : "";

            // Check input authorize
            if(!$data['authorize_card_number']) { self::createMsg($CMS->lang['error_card_number']); return false; }
            if(!$data['authorize_expiration_date']) { self::createMsg($CMS->lang['error_expiration_date']); return false; }
            if(!$data['authorize_cvv_cvc']) { self::createMsg($CMS->lang['error_cvv_cvc']); return false; }

            // Validate card
            $validCard = CreditCard::validCreditCard($data['authorize_card_number']);
            if(!$validCard['valid'])
            {
                self::createMsg("Error card number. Please try again!");
                return false;
            }

            $date = explode("-", $data['authorize_expiration_date']);
            $validDate = CreditCard::validDate($date[0], $date[1]);

            if(!$validDate)
            {
                self::createMsg("Error expiration date. Please try again!");
                return false;
            }

            $validCvc = CreditCard::validCvc($data['authorize_cvv_cvc'], $validCard['type']);
            if(!$validCvc)
            {
                self::createMsg("Error Cvc. Please try again!");
                return false;
            }
            // var_dump($validCvc);exit;
        }

        // check thanh toan stripe
        if($data['payment_method'] == 7){
            if(empty($CMS->input['stripeToken'])){
                self::createMsg("Error Cvc Stripe. Please try again!");
                return false;
            }

        }

        // Set session
        $_SESSION['shipping'] = $data;

        // Add order
        $return = $CMS->order->quick_add($data);
        if( $return )
        {
            // Create session order
            $_SESSION['order'] = $return;

            // Add information shipping
            self::insertShipBilladdress($data, $return);

            // Add comment đầu tiên
            self::insertComment($return, $cus_id);
            
            // Check payment method is Cash on delievery (COD)
            if ( $data['payment_method'] == 2 or  $data['payment_method'] == 1 ) 
            {
                $redirect_link = "/payment/success";

                // sendmail
                self::sendEmailEcommerce();

                // redirect link
                return $redirect_link;
            }
            else
            {
                // Paypal
                if( $data['payment_method'] == 5 )
                {
                    // Insert log subscription
                    $sublog_id = self::addRequest(json_encode($data, JSON_UNESCAPED_UNICODE), 'order_'.$return['ord_id'], "paypal");
                    $_SESSION['order']['sublog_id'] = $sublog_id;
                    
                    // Build link
                    paypal::init();
                    paypal::$redirect_default = "/payment/error";
                    list($response, $redirect_link) = paypal::payment($return, $_SESSION['mycart'], $sublog_id);

                    if( paypal::$status != 'success' )
                    {
                        // ThamLV-Y2018M8D22: missing order
                        $_SESSION['orderMissed'] = array(
                            'ord_id' => $return['ord_id'], 
                            'msg' => paypal::$message, 
                        );
                    }

                    // Update log subscription
                    self::updateResponse($sublog_id, $response);

                    $response = json_decode($response,1);
                    $_SESSION['paypal'] = $response;

                    // redirect link
                    return $redirect_link;
                }

                // Authorize
                elseif( $data['payment_method'] == 6 )
                {
                    // Insert log subscription
                    $sublog_id = self::addRequest(json_encode($data, JSON_UNESCAPED_UNICODE), 'order_'.$return['ord_id'], "authorize");
                    $_SESSION['order']['sublog_id'] = $sublog_id;
                    
                    // Build link
                    authorize::__init();
                    authorize::$urlSuccess = $CMS->vars['root_domain'].'/payment/checksuccess';
                    authorize::$urlError = $CMS->vars['root_domain'].'/payment/error';

                    $_SESSION['shipping']['city_name'] = payment::getCityname($_SESSION['shipping']['cus_city']);
                    $_SESSION['shipping']['district_name'] = payment::getDistrictname($_SESSION['shipping']['cus_district']);
                    $_SESSION['shipping']['shipcity_name'] = payment::getCityname($_SESSION['shipping']['ship_city']);
                    $_SESSION['shipping']['shipdistrict_name'] = payment::getDistrictname($_SESSION['shipping']['ship_district']);
                    
                    $redirect_link = authorize::chargeCreditCard($return, $_SESSION['mycart'], $_SESSION['shipping'], $sublog_id);

                    if( authorize::$status != 'success' )
                    {
                        // ThamLV-Y2018M8D22: missing order
                        $_SESSION['orderMissed'] = array(
                            'ord_id' => $return['ord_id'], 
                            'msg' => authorize::$message, 
                        );
                    }

                    // redirect link
                    return $redirect_link;
                }

                // Stripe
                else if($data['payment_method'] == 7)
                {
                    $stripe['email'] = $CMS->input['cus_email'];
                    $stripe['token'] = $CMS->input['stripeToken'];
                    $stripe['total'] = $return['ord_total'] * 100;
                    
                    // Init config stripe
                    $CMS->api->stripe->init();
                    $stripe_res = $CMS->api->stripe->charge($stripe);
                    $stripe_res_log =json_encode($stripe_res, JSON_UNESCAPED_UNICODE);
                    
                    if( $stripe_res['status'] == "success" )
                    {
                        $redirect_link = "/payment/checksuccess";
                    }
                    else
                    {
                        self::createMsg($stripe_res['message']);

                        // ThamLV-Y2018M8D22: missing order
                        $_SESSION['orderMissed'] = array(
                            'ord_id' => $return['ord_id'], 
                            'msg' => $stripe_res['message']
                        );

                        $redirect_link = "/payment/error";
                    }

                    // redirect link
                    return $redirect_link;
                }
            }
        }

        // Error
        self::createMsg($CMS->lang['error_insert_order']);
        $redirect_link = "/payment/shipping";
        return $redirect_link;
    }


    static function checkTransactionEcommerce($data=[])
    {
        global $CMS, $DB;

        // Check error
        $check = 1;
        
        // Check with paypal
        if($CMS->vars['payment_active'])
        {
            if($data['paymentId'] !== $_SESSION['paypal']['id'])
            {
                $check = 0;
            }
        }

        // Infor order
        $id = $_SESSION['order']['ord_id'];

        if($check == 1)
        {
            // Before change
            $CMS->class->logs->key = "order_{$id}";    
            $CMS->class->logs->old_data = $_SESSION['order'];
            $CMS->class->logs->insert("order_{$id}");

            $payment_method = $_SESSION['shipping']['payment_method'];

            // Kiểm tra thanh toán đơn hàng. Nếu đã thanh toán rồi thì trạng thái của ordi_payment_status=1, ngược lại = 0
            $ordi_payment_status = 1;
            $ordi_status = 1;
            if($payment_method == 5)
            {
                // paypal init
                paypal::init();
                paypal::process("order_{$id}");

                if( paypal::$status != 'success' )
                {
                    // ThamLV-Y2018M8D23: missing order
                    $_SESSION['orderMissed'] = array(
                        'ord_id' => $id, 
                        'msg' => paypal::$message, 
                    );

                    $_SESSION['error_msg'] .= $CMS->lang['error_invalid_transaction'] . ' (0)';
                    return false;
                }

                // update status
                // Sau khi co su dung van chuyen thi don hang se ơ trang thai pending (Truoc do order o trang thai hoan thanh): ord_status=0
                $DB->query("UPDATE ".root_table."order SET payment_status=1, ord_status=0, ord_time_update='".time()."' WHERE ord_id='{$id}'");

            }elseif($payment_method == 6)
            {
                // update status
                $DB->query("UPDATE ".root_table."order SET payment_status=1, ord_status=0, ord_time_update='".time()."' WHERE ord_id='{$id}'");
                
            }elseif($payment_method == 7) // thanh toan qua stripe
            {
                // update status
                $DB->query("UPDATE ".root_table."order SET payment_status=1, ord_status=0, ord_time_update='".time()."' WHERE ord_id='{$id}'");

            }else
            {
                // update status
                $DB->query("UPDATE ".root_table."order SET ord_status=0, ord_time_update='".time()."' WHERE ord_id='{$id}'");
                $ordi_payment_status = 0;
                $ordi_status = 0;

                $error_msg = $CMS->lang['error_invalid_transaction'] . ' (1)';

                // ThamLV-Y2018M8D22: missing order
                $_SESSION['orderMissed'] = array(
                    'ord_id' => $id, 
                    'msg' => $error_msg
                );

                $_SESSION['error_msg'] = $error_msg;
                return false;
            }

            
            // Update order item
            $DB->query("UPDATE ".root_table."order_item SET ordi_status='{$ordi_status}', ordi_payment_status='{$ordi_payment_status}' WHERE ord_id='{$id}'");

            // Update log key
            $DB->query("UPDATE ".root_table."subscription_logs SET sublog_status='success' WHERE logs_key='order_{$id}'");

            // After change
            $CMS->class->logs->key = "order_{$id}";

            // Get info
            $new_order = $CMS->order->get_info($id);
            $CMS->class->logs->save_detail("order",$id,$new_order); 

            // sendmail
            self::sendEmailEcommerce();

            return true;

        }else
        {
            $error_msg = $CMS->lang['error_invalid_transaction'] . ' (2)';

            // ThamLV-Y2018M8D22: missing order
            $_SESSION['orderMissed'] = array(
                'ord_id' => $id, 
                'msg' => $error_msg
            );

            $_SESSION['error_msg'] = $error_msg;
            return false;
        }
    }


    static function sendEmailEcommerce($type=0)
    {
        global $CMS, $DB, $member;

        // inputs
        $ord_id = $_SESSION['order']['ord_id'];
        $order = self::getOrder($ord_id);
        list($ship, $bill, $sp_id) = self::getShipBillByOrder($ord_id);

        $cus_full_name = $bill['full_name'];
        $cus_email = $bill['email'];
        $cus_phone = $bill['phone'];

        $table_html = <<<EOF
        <table class="table table-bordered" width="100%" border="0">
            <tbody>
            <tr bgcolor="#e0e0e0" valign="middle" align="center" style="background: #e0e0e0;">
                <td width="70%" style="text-align: center;padding: 15px;border: 1px solid #e0e0e0;">Item</td>
                <td width="10%" style="text-align: center;padding: 15px;border: 1px solid #e0e0e0;">Quantity</td>
                <td width="20%" style="text-align: center;padding: 15px;border: 1px solid #e0e0e0;">Price</td>
            </tr>
EOF;
        
        if( is_array($order['items']) )
        {
            foreach( $order['items'] as $item ) 
            {
                $table_html .= <<<EOF
                <tr style="text-align: center;">
                    <td style="border: 1px solid #e0e0e0;padding: 15px;">
                        <img src="{$CMS->vars['upload_url']}/{$item['uploadPath']}" style="max-width:80%; max-height: 300px;" />
                        <br/>
                        {$item['product_name']}
                    </td>
                    <td style="border: 1px solid #e0e0e0;padding: 15px;">{$item['quantity']}</td>
                    <td style="text-align: right;border: 1px solid #e0e0e0;padding: 15px;">{$item['total_price_n']}</td>
                </tr>
EOF;
            }    
        }

        $table_html .= <<<EOF
        <tr>
            <td colspan="2" style="text-align: right;border: 1px solid #e0e0e0;padding: 15px;">Sub-Total: </td>
            <td style="text-align: right;border: 1px solid #e0e0e0;padding: 15px;"><strong>{$order['subtotal_n']}</strong></td>
        </tr>
EOF;
        if( $order['discount'] > 0 )
        {
            $table_html .= <<<EOF
            <tr>
                <td colspan="2" style="text-align: right;border: 1px solid #e0e0e0;padding: 15px;">Discount: </td>
                <td style="text-align: right;border: 1px solid #e0e0e0;padding: 15px;"><strong>{$order['discount_n']}</strong></td>
            </tr>
EOF;
        }

        if( $order['tax'] > 0 )
        {
            $table_html .= <<<EOF
            <tr>
                <td colspan="2" style="text-align: right;border: 1px solid #e0e0e0;padding: 15px;">Tax: </td>
                <td style="text-align: right;border: 1px solid #e0e0e0;padding: 15px;"><strong>{$order['tax_n']}</strong></td>
            </tr>
EOF;
        }

        if( $order['fee_shipping'] > 0 )
        {
            $table_html .= <<<EOF
            <tr>
                <td colspan="2" style="text-align: right;border: 1px solid #e0e0e0;padding: 15px;">Shipping & fee: </td>
                <td style="text-align: right;border: 1px solid #e0e0e0;padding: 15px;"><strong>{$order['fee_shipping_n']}</strong></td>
            </tr>
EOF;
        }

        $table_html .= <<<EOF
            <tr>
                <td colspan="2" style="text-align: right;border: 1px solid #e0e0e0;padding: 15px;">Total: </td>
                <td style="text-align: right;border: 1px solid #e0e0e0;padding: 15px;"><strong>{$order['total_n']}</strong></td>
            </tr>
EOF;
        
        $table_html .= "</tbody></table>";

        // email header
        $time = $CMS->class->date->date_format(time());
        $CMS->email->email_template = "order_success";
        $CMS->email->email_to = $cus_email;
        $CMS->email->email_toname = $cus_full_name;

        // email_from
        $CMS->email->data['date_send'] =  $time;
        $CMS->email->data['cus_name'] =  $cus_full_name;
        $CMS->email->data['table_content'] = $table_html;
        $CMS->email->data['website_name'] = $CMS->vars['website_title'];

        $CMS->email->quick_send(0,0);

        // Send admin
        if($CMS->vars['company_email'])
        {
            $CMS->email->email_template = "notification_order_admin";
            $CMS->email->email_to = $CMS->vars['company_email'];
            $CMS->email->email_toname = "Admin";

            $CMS->email->data['cus_name'] = $cus_full_name;
            $CMS->email->data['cus_email'] = $cus_email;
            $CMS->email->data['cus_phone'] = $cus_phone;
            $CMS->email->data['link_order'] = "{$CMS->vars['root_domain']}/acp/?site=order&act=show&id={$ord_id}";
            
            $CMS->email->quick_send(0,0);
        }

        return true;
    }


    static function getCountryname($country_id=0)
    {
        global $CMS, $DB;

        if(!$country_id) {return fasle;}

        $results = $DB->fetch_data("SELECT country_name FROM ".root_table."country WHERE country_id='{$country_id}' OR country_iso_code='{$country_id}' LIMIT 1", "country");

        $data = isset($results[0]) ? $results[0] : null;
        return $data['country_name'];
    }

    static function getFullAddress($data)
    {
        global $CMS, $DB;

        /*
        * ThamLV-Y2018M8D15: General shipping address full
        */

        $output = '';

        $output .= $data['ship_first_name'].' '.$data['ship_last_name'];

        $data['ship_phone'] = book::trimPhone($data['ship_phone']);
        if( $data['ship_phone'] )
        {
            $output .= ' ('.$data['ship_phone'].')';
        }

        if( $data['ship_address'] )
        {
            $output .= ' - '.$data['ship_address'];
        }

        if( $data['ship_address2'] )
        {
            $output .= ', '.$data['ship_address2'];
        }

        if( $data['ship_city'] )
        {
            $output .= \models\payment::getCityname($data['ship_city']);
            $cityName = $cityName ? $cityName : $data['ship_city'];

            $output .= ', ' . $cityName;
        }

        if( $data['ship_country'] == 'US' )
        {
            if( $data['ship_state'] )
            {
                $output .= ', ' . $data['ship_state'];
            }

            if( $data['ship_zipcode'] )
            {
                $output .= ', ' . $data['ship_zipcode'];
            }
        }
        else
        {
            if( $data['ship_province'] )
            {
                $output .= ', ' . $data['ship_province'];
            }

            if( $data['ship_postalcode'] )
            {
                $output .= ', ' . $data['ship_postalcode'];
            }
        }
        
        return $output;
    }



    static function getCityname($city_id=0)
    {
        global $CMS, $DB;

        if(!$city_id) {return fasle;}

        $results = $DB->fetch_data("SELECT city_name FROM ".root_table."city WHERE city_id='{$city_id}' LIMIT 1", "city");

        $data = isset($results[0]) ? $results[0] : null;
        return $data['city_name'];
    }

    static function getDistrictname($district_id=0)
    {
        global $CMS, $DB;

        if(!$district_id) {return fasle;}

        $results = $DB->fetch_data("SELECT district_name FROM ".root_table."district WHERE district_id='{$district_id}' LIMIT 1", "district");

        $data = isset($results[0]) ? $results[0] : null;
        return $data['district_name'];
    }

    static function getInforOrderAndShipping()
    {
        global $CMS, $DB;

        $ord_id = $_SESSION['order']['ord_id'];
        $arr_item = self::getItemByOrder($ord_id);

        $subtotal = 0;
        $total_tax = 0;
        $total_discount = 0; 
        foreach ($arr_item as $id => $data) 
        {
            $subtotal += $data['total_price'];
            $total_tax += $data['total_tax'] * $data['quantity'];
            $total_discount += $data['total_discount'];
            $amount_remain = $CMS->class->input->currency($data['total_price']);
            $data['image'] = $CMS->vars['upload_url']."/product/".$data['product_image'];
            
        }

        $subtotal_show = $CMS->class->input->currency($subtotal);
        $total_tax_show = $CMS->class->input->currency($total_tax);
        $total_discount_show = $CMS->class->input->currency($total_discount);
        $total_show = $CMS->class->input->currency($subtotal + $total_tax - $total_discount);

        $data['information']['list_items'] = $arr_item;
        $data['information']['subtotal'] = $subtotal;
        $data['information']['total_tax'] = $total_tax;
        $data['information']['total_discount'] = $total_discount;

        $data['information']['subtotal_show'] = $subtotal_show;
        $data['information']['total_tax_show'] = $total_tax_show;
        $data['information']['total_discount_show'] = $total_discount_show;
        $data['information']['total_show'] = $total_show;

        $data['information']['shipping'] = $_SESSION['shipping'];

        return $data;

    }

    static function insertShipBilladdress($info=[], $order=[])
    {
        global $CMS, $DB;

        // p($info); p($order); exit;
        // Billing address
        $ord_id = $order['ord_id'];
        $cus_id = $_SESSION['member']['cus_id'];
        if(!$ord_id) { return false;}

        $bill_first_name = $info['cus_first_name'];
        $bill_last_name = $info['cus_last_name'];
        $bill_full_name = $info['cus_full_name'];
        $bill_email = $info['cus_email'];
        $bill_phone = $info['cus_phone'];
        $bill_address = $info['cus_address'];
        $bill_address2 = $info['cus_address2'];
        $bill_zipcode = $info['cus_zipcode'];
        $bill_city = $info['cus_city'];
        $bill_district = $info['cus_district'];
        
        $bill_country = $info['cus_country'];
        $bill_province = $info['cus_province'];

        $ship_first_name = $info['ship_first_name'];
        $ship_last_name = $info['ship_last_name'];
        $ship_full_name = $info['ship_full_name'];
        $ship_email = $info['ship_email'];
        $ship_phone = $info['ship_phone'];
        $ship_address = $info['ship_address'];
        $ship_address2 = $info['ship_address2'];
        $ship_zipcode = $info['ship_zipcode'];
        $ship_city = $info['ship_city'];
        $ship_district = $info['ship_district'];

        $ship_country = $info['ship_country'];
        $ship_province = $info['ship_province'];

        $sp_deliver = $info['ship_deliver'];
        $sp_deliver_fee = $info['ship_deliver_fee'];
        
        // Insert shipping billing
        $DB->query("INSERT INTO ".root_table."shipbill_address (bill_first_name, bill_last_name, bill_full_name, bill_email, bill_phone, bill_address, bill_address2, bill_zipcode, bill_city, bill_district, ship_first_name, ship_last_name, ship_full_name, ship_email, ship_phone, ship_address, ship_address2, ship_zipcode, ship_city, ship_district, sp_deliver, sp_deliver_fee, ord_id, cus_id, bill_country, bill_province, ship_country, ship_province) VALUES ('{$bill_first_name}', '{$bill_last_name}', '{$bill_full_name}', '{$bill_email}', '{$bill_phone}', '{$bill_address}', '{$bill_address2}', '{$bill_zipcode}', '{$bill_city}', '{$bill_district}', '{$ship_first_name}', '{$ship_last_name}', '{$ship_full_name}', '{$ship_email}', '{$ship_phone}', '{$ship_address}', '{$ship_address2}', '{$ship_zipcode}', '{$ship_city}', '{$ship_district}', '{$sp_deliver}', '{$sp_deliver_fee}', '{$ord_id}', '{$cus_id}', '{$bill_country}', '{$bill_province}', '{$ship_country}', '{$ship_province}')");
        return true;
    }

    static function insertComment($order=[], $cus_id=0)
    {
        global $CMS, $DB, $member;

        // Insert comment for customer
        $ord_status = $order['ord_status'];
        $module_id = $order['ord_id'];
        $module_name = "order";
        $user_id = $cus_id;
        $comment_name = $comment_content = $CMS->lang['order_status_'.$ord_status];

        $comment_time = time();
        $comment_ip_address = $_SERVER['REMOTE_ADDR'];
        $comment_approved = 1;// Mặc định là duyệt
        $comment_hide = 0; // 0: Hiện, 1: Ẩn 

        // Insert logs comment
        $DB->query("INSERT INTO ".root_table."comment (module_id, module_name, user_id, cus_id, comment_name, comment_content, comment_time, comment_ip_address, comment_approved, ord_status, ord_status_custom, comment_hide) VALUES ('{$module_id}', '{$module_name}', '{$user_id}', '{$cus_id}', '{$comment_name}', '{$comment_content}', '{$comment_time}', '{$comment_ip_address}', '{$comment_approved}', '{$ord_status}', 0, '{$comment_hide}')");
        // Insert logs
        $CMS->class->logs->insert("Created comment successful");
    }

    /*
    * ThamLV-Y2018M8D22: get order, shipping, billing information, used for page success, error
    */

    static function getOrder( $ord_id = 0, $disableItems = 0 )
    {
        global $CMS, $DB;

        $output = [];

        $sql = "
        SELECT ord_id AS id, ord_name AS name, ord_email AS email, ord_amount AS subtotal, ord_total_discount AS discount, ord_tax AS tax, ord_fee_shipping AS fee_shipping, ord_total AS total, payment_status, payment_method, ord_shipping_method AS shipping_method, ord_shipping_location AS shipping_location 
        FROM ".root_table."order 
        WHERE ord_id = '{$ord_id}' 
        ORDER BY ord_id DESC 
        LIMIT 1
        ";
        $output = $DB->fetch_data($sql, 'order');
        $output = isset($output[0]) ? $output[0] : false;
        if( $output )
        {
            // convert data
            $output['subtotal_n'] = $CMS->class->input->currency($output['subtotal']);
            $output['discount_n'] = $CMS->class->input->currency($output['discount']);
            $output['tax_n'] = $CMS->class->input->currency($output['tax']);
            $output['fee_shipping_n'] = $CMS->class->input->currency($output['fee_shipping']);
            $output['total_n'] = $CMS->class->input->currency($output['total']);

            $output['payment_status_n'] = $CMS->lang["payment_status_{$output['payment_status']}"];
            $output['payment_method_n'] = $CMS->lang["payment_method_{$output['payment_method']}"];

            $output['shipping_method_n'] = $CMS->lang["ship_method_{$output['shipping_method']}"];
            $output['shipping_location_n'] = $CMS->lang["ship_location_{$output['shipping_location']}"];

            if( !$disableItems )
            {
                $output['items'] = self::getItemByOrder($ord_id);

                if( is_array($output['items']) )
                {
                    foreach( $output['items'] as $key => $item ) 
                    {
                        $output['items'][$key]['uploadPath'] = "product/{$item['product_image']}";
                        $output['items'][$key]['total_price_n'] = $CMS->class->input->currency($item['total_price']);
                        $output['items'][$key]['total_tax_n'] = $CMS->class->input->currency($item['total_tax']);
                        $output['items'][$key]['total_discount_n'] = $CMS->class->input->currency($item['total_discount']);
                    }
                }
            }
        }

        return $output;
    }

    static function getShipBillByOrder( $ord_id = 0 )
    {
        global $CMS, $DB;

        $ship = [];
        $bill = [];
        $sp_id = 0;

        if( $ord_id )
        {
            $sql = "
            SELECT * 
            FROM ".root_table."shipbill_address 
            WHERE ord_id='{$ord_id}' 
            AND sp_deleted=0 
            LIMIT 1
            ";
            $data = $DB->fetch_data($sql, 'shipbill_address.order');
            $arr = isset($data[0]) ? $data[0] : false;

            if( is_array($arr) )
            {
                $ship['first_name'] = $arr['ship_first_name'];
                $ship['last_name'] = $arr['ship_last_name'];
                $ship['full_name'] = $arr['ship_full_name'];
                $ship['email'] = $arr['ship_email'];
                $ship['phone'] = $arr['ship_phone'];
                $ship['address'] = $arr['ship_address'];
                $ship['address2'] = $arr['ship_address2'];
                $ship['zipcode'] = $arr['ship_zipcode'];
                $ship['city'] = $arr['ship_city'];
                $ship['district'] = $arr['ship_district'];
                $ship['province'] = $arr['ship_province'];
                $ship['country'] = $arr['ship_country'];
                
                $ship['city_name'] = $CMS->country->nameCity($arr['ship_city']);
                $ship['city_name'] = $ship['city_name'] ? $ship['city_name'] : $ship['city'];
                
                $ship['district_name'] = $CMS->country->nameDistrict($arr['ship_district']);
                $ship['district_name'] = $ship['district_name'] ? $ship['district_name'] : $ship['district'];
                
                $ship['province_name'] = $CMS->country->nameState($arr['ship_province']);
                $ship['province_name'] = $ship['province_name'] ? $ship['province_name'] : $ship['province'];

                $ship['country_name'] = $CMS->country->nameCountry($arr['ship_country']);
                $ship['country_name'] = $ship['country_name'] ? $ship['country_name'] : $ship['country'];

                $bill['first_name'] = $arr['bill_first_name'];
                $bill['last_name'] = $arr['bill_last_name'];
                $bill['full_name'] = $arr['bill_full_name'];
                $bill['email'] = $arr['bill_email'];
                $bill['phone'] = $arr['bill_phone'];
                $bill['address'] = $arr['bill_address'];
                $bill['address2'] = $arr['bill_address2'];
                $bill['zipcode'] = $arr['bill_zipcode'];
                $bill['city'] = $arr['bill_city'];
                $bill['district'] = $arr['bill_district'];
                $bill['province'] = $arr['bill_province'];
                $bill['country'] = $arr['bill_country'];
                
                $bill['city_name'] = $CMS->country->nameCity($arr['bill_city']);
                $bill['city_name'] = $bill['city_name'] ? $bill['city_name'] : $bill['city'];

                $bill['district_name'] = $CMS->country->nameDistrict($arr['bill_district']);
                $bill['district_name'] = $bill['district_name'] ? $bill['district_name'] : $bill['district'];

                $bill['province_name'] = $CMS->country->nameState($arr['bill_province']);
                $bill['province_name'] = $bill['province_name'] ? $bill['province_name'] : $bill['province'];

                $bill['country_name'] = $CMS->country->nameCountry($arr['bill_country']);
                $bill['country_name'] = $bill['country_name'] ? $bill['country_name'] : $bill['country'];

                $sp_id = $arr['sp_id'];
            }
        }
        
        return array($ship, $bill, $sp_id);
    }

    /*
    * ThamLV-Y2018M8D22: get order, shipping, billing information, used for page success, error
    */
    static function updateOrderMissed( $ord_id = 0, $msg = "", $title = "" )
    {
        global $CMS, $DB;

        // Update order missed
        $ord_status_custom = 40;
        $ord_time_update = time();

        $custom_status = \lib\input::jsonDecode($CMS->vars['custom_status']);
        if( in_array($ord_status_custom, $custom_status[0]) )
        {
            $sql = "
            UPDATE ".root_table."order 
            SET ord_status_custom = '40', ord_time_update = '{$ord_time_update}' 
            WHERE ord_id = '{$ord_id}' 
            ";

            $query_sql = $DB->query($sql);
        }

        // Add Logs
        $fullmsg  = $title ? $title : $CMS->lang['title_order_payment_error'];
        $fullmsg .= "<br>{$msg}";
        $CMS->class->logs->key = "order_{$ord_id}";
        $CMS->class->logs->insert($fullmsg);
    }

    /*
    * ThamLV-Y2018M8D23: get missed order 
    */

    static function getOrderMissed( $ord_name = '', $ord_email = '' )
    {
        global $CMS, $DB;

        $output = [];

        $sql = "
        SELECT * 
        FROM ".root_table."order 
        WHERE ord_name = '{$ord_name}' AND ord_email = '{$ord_email}' AND ord_status_custom = '40' 
        ORDER BY ord_id DESC 
        LIMIT 1
        ";
        // p($sql);exit;
        $output = $DB->fetch_data($sql, 'order');
        $output = isset($output[0]) ? $output[0] : false;
        return $output;
    }

    /*
    * ThamLV-Y2018M8D23: repayment for order missed 
    */
    static function reCheckOutEcommerce()
    {
        global $CMS, $DB, $member;

        // Check token
        if( !\lib\security::check_token() )
        {
            self::createMsg("Please refresh page (F5) then try again.");
            return false;
        }

        // Inputs
        $data = [];
        $data['payment_method'] = isset($CMS->input['payment_method']) ? intval($CMS->input['payment_method']) : 0;
        $data['order_payment_method'] = $data['payment_method'];
        if ( $data['payment_method'] == "3" or $data['payment_method'] == "4" )
        {
            $data['order_payment_method'] = 3;
        }

        $order = isset($_SESSION['order']) ? $_SESSION['order'] : [];

        // Validate inputs
        if( empty($order) OR empty($order['repayment']) )
        {
            self::createMsg($CMS->lang['repayment_order_notfound']); 
            return '/';
        }

        if( $order['payment_status'] == 1 )
        {
            self::createMsg($CMS->lang['repayment_order_paid']); 
            return '/';
        }

        // Payment method is Cash on delievery (COD), Bank Transfer
        if ( $data['payment_method'] == 2 or  $data['payment_method'] == 1 ) 
        {
            // Update order
            $data['payment_status'] = 0;
            $data['ord_status_custom'] = 0;
            self::updateOrderRepayment($data);

            // sendmail
            self::sendEmailEcommerce();

            // redirect link
            return '/payment/success';
        }

        // Payment method is paypal.com
        else if( $data['payment_method'] == 5 )
        {
            // Inputs
            $data['repayment'] = array(
                'ord_id'    => $order['ord_id'], 
                'ord_total' => $order['ord_total'], 
            );

            $mycart = self::generalCartRepayment($order['ord_id']);
            $shipping = $data;

            // Set session shipping
            $_SESSION['shipping'] = $shipping;

            // Insert log subscription
            $sublog_id = self::addRequest(json_encode($data, JSON_UNESCAPED_UNICODE), "order_{$order['ord_id']}", "authorize");
            $_SESSION['order']['sublog_id'] = $sublog_id;
            
            // Build link
            paypal::init();
            paypal::$redirect_default = "/payment/repayment";
            paypal::$urlSuccess = $CMS->vars['root_domain'].'/payment/rechecksuccess/';
            list($response, $redirect_link) = paypal::payment($order, $mycart, $sublog_id);
            $_SESSION['paypal'] = json_decode($response, 1);

            if( paypal::$status != 'success' )
            {
                // Update payment error
                self::updateOrderMissed( $order['ord_id'], paypal::$message, "{$CMS->lang['title_repayment_order']}, {$CMS->lang['title_order_payment_error']} :" . $CMS->lang["payment_method_{$data['payment_method']}"] );
            }

            // redirect link
            return $redirect_link;
        }

        // Payment method is authorize.net
        else if( $data['payment_method'] == 6 )
        {
            $data['authorize_card_number'] = isset($CMS->input['authorize_card_number']) ? str_replace(" ", "", $CMS->input['authorize_card_number']) : "";
            $data['authorize_expiration_date'] = isset($CMS->input['authorize_expiration_date']) ? $CMS->input['authorize_expiration_date'] : "";
            $data['authorize_cvv_cvc'] = isset($CMS->input['authorize_cvv_cvc']) ? $CMS->input['authorize_cvv_cvc'] : "";

            // Check input authorize
            if( !$data['authorize_card_number'] ) { self::createMsg($CMS->lang['error_card_number']); return false; }
            if( !$data['authorize_expiration_date'] ) { self::createMsg($CMS->lang['error_expiration_date']); return false; }
            if( !$data['authorize_cvv_cvc'] ) { self::createMsg($CMS->lang['error_cvv_cvc']); return false; }

            // Validate card
            $validCard = CreditCard::validCreditCard($data['authorize_card_number']);
            if( !$validCard['valid'] )
            {
                self::createMsg("Error card number. Please try again!");
                return false;
            }

            $date = explode("-", $data['authorize_expiration_date']);
            $validDate = CreditCard::validDate($date[0], $date[1]);

            if( !$validDate )
            {
                self::createMsg("Error expiration date. Please try again!");
                return false;
            }

            $validCvc = CreditCard::validCvc($data['authorize_cvv_cvc'], $validCard['type']);
            if( !$validCvc )
            {
                self::createMsg("Error Cvc. Please try again!");
                return false;
            }

            // Inputs
            $data['repayment'] = array(
                'ord_id'    => $order['ord_id'], 
                'ord_total' => $order['ord_total'], 
            );

            $mycart = [];
            $shipping = self::generalShippingRepayment($order['ord_id']);
            $shipping = array_merge($shipping, $data);

            // Set session shipping
            $_SESSION['shipping'] = $shipping;

            // Insert log subscription
            $sublog_id = self::addRequest(json_encode($data, JSON_UNESCAPED_UNICODE), "order_{$order['ord_id']}", "authorize");
            $_SESSION['order']['sublog_id'] = $sublog_id;
            
            // Build link
            authorize::__init();
            authorize::$urlSuccess = $CMS->vars['root_domain'].'/payment/rechecksuccess';
            authorize::$urlError = $CMS->vars['root_domain'].'/payment/repayment';

            $redirect = authorize::chargeCreditCard($order, $mycart, $shipping, $sublog_id);
            if( authorize::$status != 'success' )
            {
                // Update payment error
                self::updateOrderMissed( $order['ord_id'], authorize::$message, "{$CMS->lang['title_repayment_order']}, {$CMS->lang['title_order_payment_error']} :" . $CMS->lang["payment_method_{$data['payment_method']}"]);
            }

            return $redirect;
        }

        // Payment stripe.com
        else if( $data['payment_method'] == 7 )
        {
            if( empty($CMS->input['stripeToken']) )
            {
                self::createMsg("Error Cvc Stripe. Please try again!");
                return false;
            }

            $stripe['email'] = $CMS->input['cus_email'];
            $stripe['token'] = $CMS->input['stripeToken'];
            $stripe['total'] = $order['ord_total'] * 100;
            
            // Init config stripe
            $CMS->api->stripe->init();
            $stripe_res = $CMS->api->stripe->charge($stripe);
            $stripe_res_log =json_encode($stripe_res, JSON_UNESCAPED_UNICODE);
            
            if( $stripe_res['status'] == "success" )
            {
                $redirect_link = "/payment/rechecksuccess";
            }
            else
            {
                self::createMsg($stripe_res['message']);

                // Update payment error
                self::updateOrderMissed( $order['ord_id'], $stripe_res['message'], "{$CMS->lang['title_repayment_order']}, {$CMS->lang['title_order_payment_error']} :" . $CMS->lang["payment_method_{$data['payment_method']}"]);
                
                $redirect_link = "/payment/repayment";
            }

            // redirect link
            return $redirect_link;
        }

        // Error
        self::createMsg($CMS->lang['error_insert_order']);
        $redirect_link = "/payment/shipping";
        return $redirect_link;
    }

    static function updateOrderRepayment( $data = [] )
    {
        global $CMS, $DB;

        // Inputs
        $payment_status = $ordi_payment_status = isset($data['payment_status']) ? $data['payment_status'] : 0;
        $payment_method = isset($data['payment_method']) ? $data['payment_method'] : 0;
        $order_payment_method = isset($data['order_payment_method']) ? $data['order_payment_method'] : 0;
        $ord_status_custom = isset($data['ord_status_custom']) ? $data['ord_status_custom'] : 0;

        $order = isset($_SESSION['order']) ? $_SESSION['order'] : [];
        $ord_id = isset($order['ord_id']) ? $order['ord_id'] : 0;
        $ord_time_update = time();

        $sql_order_set = $sql_order_item_set = "";
        if( isset($order['ord_status']) AND $order['ord_status'] = 0 AND $payment_status )
        {
            $sql_order_set = ", ord_status = '1'";
            $sql_order_item_set = ", ordi_status = '1'";
        }

        // update order
        $sql_order = "
        UPDATE ".root_table."order 
        SET payment_status = '{$payment_status}', payment_method = '{$order_payment_method}', ord_status_custom = '{$ord_status_custom}', ord_time_update='{$ord_time_update}' {$sql_order_set} 
        WHERE ord_id = '{$ord_id}' 
        ";
        $query_order = $DB->query($sql_order);

        // update order items
        $sql_order_item = "
        UPDATE ".root_table."order_item 
        SET ordi_payment_status = '{$ordi_payment_status}' {$sql_order_item_set} 
        WHERE ord_id = '{$ord_id}' 
        ";
        $query_order_item = $DB->query($sql_order_item);

        // Clear cache
        $CMS->class->cache->mdelete("order");

        // Add logs
        $CMS->class->logs->key = "order_{$ord_id}";
        $CMS->class->logs->old_data = $order;
        $CMS->class->logs->insert("{$CMS->lang['title_repayment_order']}");

        // Add logs details
        $CMS->class->logs->key = "order_{$ord_id}";
        $CMS->class->logs->save_detail("order", $ord_id, $CMS->order->get_info($ord_id)); 

        return true;
    }

    static function generalCartRepayment( $ord_id = 0 )
    {
        global $CMS, $DB;

        $output = [];
        
        if( $ord_id )
        {
            $sql = "
            SELECT *, COUNT(product_id) as quantity 
            FROM ".root_table."order_item 
            WHERE ord_id = '{$ord_id}' AND ordi_deleted = 0 
            GROUP BY product_id 
            ORDER BY ordi_id ASC 
            ";
            $items = $DB->fetch_data($sql, 'order_item.order');
            if( is_array($items) )
            {
                foreach( $items as $key => $item ) 
                {
                    $output[$key]['product_id'] = $item['product_id'];
                    $output[$key]['product_name'] = $item['ordi_name'];
                    $output[$key]['product_price'] = $item['ordi_price'];
                    $output[$key]['product_tax'] = $item['ordi_tax'];
                    $output[$key]['quantity'] = $item['quantity'];
                    $output[$key]['price_new'] = $item['ordi_price'];
                }            
            }
        }

        return $output;
    }

    static function generalShippingRepayment( $ord_id = 0 )
    {
        global $CMS, $DB;

        $output = [];
        
        if( $ord_id )
        {
            $sql = "
            SELECT * 
            FROM ".root_table."shipbill_address 
            WHERE ord_id='{$ord_id}' AND sp_deleted = 0 
            LIMIT 1
            ";
            $data = $DB->fetch_data($sql, 'shipbill_address.order');
            $arr = isset($data[0]) ? $data[0] : false;

            if( is_array($arr) )
            {
                // Bill
                $output['cus_first_name'] = $arr['bill_first_name'];
                $output['cus_last_name'] = $arr['bill_last_name'];
                $output['cus_full_name'] = $arr['bill_full_name'];
                $output['cus_email'] = $arr['bill_email'];
                $output['cus_phone'] = $arr['bill_phone'];
                $output['cus_address'] = $arr['bill_address'];
                $output['cus_address2'] = $arr['bill_address2'];
                $output['cus_zipcode'] = $arr['bill_zipcode'];
                $output['cus_city'] = $arr['bill_city'];
                $output['cus_district'] = $arr['bill_district'];
                $output['cus_province'] = $arr['bill_province'];
                $output['cus_country'] = $arr['bill_country'];
                
                $output['city_name'] = $CMS->country->nameCity($arr['bill_city']);
                $output['city_name'] = $output['city_name'] ? $output['city_name'] : $output['cus_city'];

                $output['district_name'] = $CMS->country->nameDistrict($arr['bill_district']);
                $output['district_name'] = $output['district_name'] ? $output['district_name'] : $output['cus_district'];

                $output['province_name'] = $CMS->country->nameState($arr['bill_province']);
                $output['province_name'] = $output['province_name'] ? $output['province_name'] : $output['cus_province'];

                $output['country_name'] = $CMS->country->nameCountry($arr['bill_country']);
                $output['country_name'] = $output['country_name'] ? $output['country_name'] : $output['cus_country'];

                $output['ship_first_name'] = $arr['ship_first_name'];
                $output['ship_last_name'] = $arr['ship_last_name'];
                $output['ship_full_name'] = $arr['ship_full_name'];
                $output['ship_email'] = $arr['ship_email'];
                $output['ship_phone'] = $arr['ship_phone'];
                $output['ship_address'] = $arr['ship_address'];
                $output['ship_address2'] = $arr['ship_address2'];
                $output['ship_zipcode'] = $arr['ship_zipcode'];
                $output['ship_city'] = $arr['ship_city'];
                $output['ship_district'] = $arr['ship_district'];
                $output['ship_province'] = $arr['ship_province'];
                $output['ship_country'] = $arr['ship_country'];
                
                $output['shipcity_name'] = $CMS->country->nameCity($arr['ship_city']);
                $output['shipcity_name'] = $output['shipcity_name'] ? $output['shipcity_name'] : $output['ship_city'];
                
                $output['shipdistrict_name'] = $CMS->country->nameDistrict($arr['ship_district']);
                $output['shipdistrict_name'] = $output['district_name'] ? $output['district_name'] : $output['ship_district'];
                
                $output['shipprovince_name'] = $CMS->country->nameState($arr['ship_province']);
                $output['shipprovince_name'] = $output['province_name'] ? $output['province_name'] : $output['ship_province'];

                $output['shipcountry_name'] = $CMS->country->nameCountry($arr['ship_country']);
                $output['shipcountry_name'] = $output['country_name'] ? $output['country_name'] : $output['ship_country'];
            }

            $sql = "
            SELECT *, COUNT(product_id) as quantity 
            FROM ".root_table."order_item 
            WHERE ord_id = '{$ord_id}' 
            GROUP BY product_id 
            ORDER BY ordi_id ASC 
            ";
            $items = $DB->fetch_data($sql, 'order_item.order');

            if( is_array($items) )
            {
                foreach( $items as $item ) 
                {
                    $output['product_id'][] = $item['product_id'];
                    $output['product_name'][] = $item['ordi_name'];
                    $output['product_quantity'][] = $item['quantity'];
                    $output['product_price'][] = $item['ordi_price'];
                    $output['product_tax'][] = $item['ordi_tax'];
                }            
            }
        }
        
        return $output;
    }

    static function reCheckTransactionEcommerce($data=[])
    {
        global $CMS, $DB;

        // Check error
        $check = 1;
        $message = "";
        
        // Check with paypal
        if($CMS->vars['payment_active'])
        {
            if($data['paymentId'] !== $_SESSION['paypal']['id'])
            {
                $check = 0;
            }
        }

        // Inputs
        $ord_id = isset($_SESSION['order']['ord_id']) ? $_SESSION['order']['ord_id'] : 0;
        $payment_method = isset($_SESSION['shipping']['payment_method']) ? $_SESSION['shipping']['payment_method'] : 0;
        $order_payment_method = isset($_SESSION['shipping']['order_payment_method']) ? $_SESSION['shipping']['order_payment_method'] : 0;
        $payment_status = 0;

        if( $check == 1 )
        {
            // Payment paypal
            if( $payment_method == 5 )
            {
                // paypal init
                paypal::init();
                paypal::process("order_{$ord_id}");

                if( paypal::$status == 'success' )
                {
                    $payment_status = 1;
                }
                else
                {
                    $message = paypal::$message;
                }
            }

            // Payment method is authorize.net
            else if( $payment_method == 6 )
            {
                $payment_status = 1;   
            }

            // Payment method is stripe.com
            elseif( $payment_method == 7 ) 
            {
                $payment_status = 1;   
            }

            // Update log key
            $sql_subscription_logs = "
            UPDATE ".root_table."subscription_logs 
            SET sublog_status = 'success' 
            WHERE logs_key = 'order_{$ord_id}' 
            ";
            $query_subscription_logs = $DB->query($sql_subscription_logs);
            
            if( $payment_status == 1 )
            {
                // Update order
                $data['payment_method'] = $payment_method;
                $data['payment_status'] = $payment_status;
                $data['order_payment_method'] = $order_payment_method;
                $data['ord_status_custom'] = 0;
                self::updateOrderRepayment($data);

                // sendmail
                self::sendEmailEcommerce();

                return true;
            }
        }

        // Update payment error
        self::updateOrderMissed( $ord_id, ($message ? $message : $CMS->lang['error_invalid_transaction']), "{$CMS->lang['title_repayment_order']}, {$CMS->lang['title_order_payment_error']} :" . $CMS->lang["payment_method_{$payment_method}"] );

        $_SESSION['error_msg'] .= $CMS->lang['error_invalid_transaction'];
        return false;
    }
}