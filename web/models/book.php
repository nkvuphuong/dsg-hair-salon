<?php

namespace models;

use core\ezy;
use lib\input;

ezy::load_model("customer");

class book
{
    
    static function add_dsg()
    {
        global $CMS, $DB, $member;

        // Check use captcha home and booking
        // Google ReCaptcha Verifier
        if ( \core\ezy::google_recaptcha_verify() == false ) { return self::createMsg("Captcha is wrong, please try again"); }
        
        // Check token
        if(!\lib\security::check_token())
        {
           return self::createMsg("Please refresh page (F5) then try again.");
        }

        // Validate by ip
        if(intval($CMS->vars['enable_security_ip']))
        {
            if(!\lib\security::checkSecurityIp())
            {
                return false;
            }
        }

        // Input
        $booking_name = isset($CMS->input['booking_name']) ? trim($CMS->input['booking_name']) : (isset($_SESSION['member']['cus_full_name']) ? $_SESSION['member']['cus_full_name'] : null);
        $booking_email = isset($CMS->input['booking_email']) ? trim($CMS->input['booking_email']) : (isset($_SESSION['member']['cus_email']) ? $_SESSION['member']['cus_email'] : null);
        $booking_phone = ltrim(trim(isset($CMS->input['booking_area_code']) ? $CMS->input['booking_area_code'] : ''),"+") . self::trimPhone($CMS->input['booking_phone']);// ? trim($CMS->input['booking_phone']) : $_SESSION['member']['cus_phone'];

        $booking_service = isset($CMS->input['booking_service']) ? ( is_array($CMS->input['booking_service']) ? $CMS->input['booking_service'] : array(trim($CMS->input['booking_service'])) ): $CMS->input['product_id'];

        $booking_date = isset($CMS->input['booking_date']) ? trim($CMS->input['booking_date']) : '';
        $booking_time = isset($CMS->input['booking_time']) ? trim($CMS->input['booking_time']) : trim($CMS->input['booking_hours']);
        $store_id = isset($CMS->input['store_id']) ? intval($CMS->input['store_id']) : 0;

        $storeInfo = store::getInfo($store_id);
        $listperson = isset($CMS->input['person_number']) ? array_values($CMS->input['person_number']) : [];
        $notelist = isset($CMS->input['notelist']) ? (is_array($CMS->input['notelist']) ? array_values($CMS->input['notelist']) : array($CMS->input['notelist']) ) : [];
        $booking_form_email = isset($CMS->input['booking_form_email']) ? intval($CMS->input['booking_form_email']) : 0;

        // theme dsg
        $cosmetic =  $CMS->input['cosmetic'];
        $hair_type =  $CMS->input['hair_type'] ;
        $staff_type =  $CMS->input['staff_type'];
        $hair_length =  $CMS->input['hair_length'];

        // Chuẩn hoá notelist bỏ \n để json_decode ko bị lỗi
        $list_check = [];
        foreach ($notelist as $value) 
        {
            $note_check = str_replace("\n", " ", $value);
            $list_check[] = str_replace("  ", " ", $note_check);
        }
        // Gán lại note list
        $notelist = $list_check;

        // Check input
        if(!isset($CMS->input['nocaptcha']) || !$CMS->input['nocaptcha'])
        {
            if(!$booking_name) { return self::createMsg("Please enter your name!"); }
            // if(!$booking_email) { return self::createMsg("Please enter your email!"); }
            if(!$CMS->class->input->is_email($booking_email) and $booking_email) { return self::createMsg("You entered the wrong email! Please try again"); }
            if(!$booking_service) { return self::createMsg("Please choose a service!"); }
            if(!$booking_date) { return self::createMsg("Please choose a date!"); }
            if(!$booking_time) { return self::createMsg("Please choose a time!"); }
        }

        // Convert du lieu
        $data['service_type'] = 1;
        $data['booking_hours'] = self::convertHours($booking_time);
        $data['booking_form_email'] = $booking_form_email;
        $data['cus_id'] = isset($_SESSION['member']['cus_id']) ? intval($_SESSION['member']['cus_id']) : 0;
        if(isset($CMS->vars['is_cs']) and $CMS->vars['is_cs'] == 1)
        {
            $data['ord_note'] = "Booking Day: ".$booking_date."<br/>".(isset($notelist[0]) ? $notelist[0] : '')."<br/>".$CMS->input['date_sel'];
        }else
        {

            $data['ord_note'] = "Booking Day: ".$booking_date;
        }
        $data['store_id'] = $store_id;
        
        $sms_service = "";
        $str_note = "";
        $i=0;
        if ( $CMS->vars['theme'] == 'dsg' )
        {
           if(!empty($staff_type ))
            {
                $staff_user = $CMS->user->get_info($staff_type);
            }
        }
        $cnt = 0;
        // Convert data service
        foreach ($booking_service as $product_id) 
        {
            if(!empty($product_id))
            {
                
                $product = $CMS->product->getInfo($product_id);
                $price = $product['product_price_sell'] ? $product['product_price_sell'] : $product['product_price'];
                if($price >= 100000)
                {
                    $cnt++;
                }
                if($hair_length == 2 AND $price >= 200000)
                {
                     $price += 100000; //toc nhieu
                }
                if($cosmetic == 2 AND $price >= 200000)
                {
                     $price += 100000; //cao cap
                }

                $data['product_id'][] = $product_id;
                $data['product_name'][] = $product['product_name'];
                $data['product_cycle_type'][] = $product['product_cycle'];
                $data['product_cycle'][] = 1;
                $data['product_quantity'][] = 1;
                $data['product_price'][] = $price;
                $sms_service .= $product['product_name'] ? $product['product_name'].", ": "";
                
                if(isset($CMS->vars['is_restaurant']) and $CMS->vars['is_restaurant'] == 1)
                {
                    $str_note .= $product['product_name']." - ".$listperson[$i]." person".". Note: ".$notelist[$i].", "."<br/>";
                  
                }
                // dsg theme
                if ( $CMS->vars['theme'] == 'dsg' )
                {
                    if($i == 0)
                    {
                        $product_price_add = 0;
                        // $str_note .= $str_note ? ', ' : 'Mỹ phẩm: ';
                      
                       // if( $cosmetic  == 2) { $product_price_add += 200000; }
                        if($staff_user['userg_id'] == 15)
                        {
                           // $product_price_add += 100000;
                        }

                        if(strpos($product['product_option'],"6") == false) //Dịch vụ khác
                        {
                            $data['product_price_add'][$i] = $product_price_add;
                        }   
                    }
                   
                }
                $i++;
            }
        }
        if ( $CMS->vars['theme'] == 'dsg' )
        {
            $product_price_add = 0;
            $str_note .= $str_note ? ', ' : ', ';
           // if( $hair_type  == 1) {  $str_note .= "Loại tóc: Tóc nam, " ; }else { $str_note .= "Loại tóc: Tóc nữ, " ; }
            if( $cosmetic  == 1) {  $str_note .= "Mỹ phẩm: phổ thông, " ; }else { $str_note .= "Mỹ phẩm: cao cấp, " ;   }
            if($hair_length == 2) { $str_note .= "Tóc : nhiều, " ; } else { $str_note .= "Tóc : ít" ;  }
               $str_note .= "Nhân viên: {$staff_user['user_display_name']} " ;  
        }
 
        $sms_service = rtrim($sms_service,", ");

        // Check staff ID
        $sms_staff = "";
        if(isset($CMS->input['staff_id']) && $CMS->input['staff_id'])
        {
            foreach ($CMS->input['staff_id'] as $user_id) 
            {
                $name = $CMS->user->get_info($user_id, "user_display_name");
                $data['product_description'][] = "Staff: ".$name;
                $sms_staff .= $name ? $name.", ": "";
            } 
        }else
        {
            $data['product_description'] = [];
        }

        // Check khuyen mai combo
        if($cnt >= 2)
        {
            $discount_combo = $cnt * 50000;
            $data['product_discount_type'][0] = 1;
            $data['product_discount_value'][0] = $discount_combo;
            $str_note_combo = "Khuyến mãi combo: ".$discount_combo ;
        }
        $sms_staff = rtrim($sms_staff,", ");
        $sms_note = "";
        if(isset($CMS->vars['is_restaurant']) and $CMS->vars['is_restaurant'] == 1)
        {
            $data['ord_note'] .= "<br/>".$str_note;
        }
        // dsg theme
        else if ( $CMS->vars['theme'] == 'dsg' )
        {
            $data['ord_note'] .= "<br/>".$str_note."<br/>".$str_note_combo;
        }
        else
        {
            $data['ord_note'] .= " <br/>".(isset($notelist[0]) ? $notelist[0] : '');
            $sms_note = isset($notelist[0]) ? $notelist[0] : '';
        }

        // order content
        $info_cus = [];
        $info_cus['cus_name'] = $booking_name;
        $info_cus['cus_email'] = $booking_email;
        $info_cus['cus_phone'] = $booking_phone;
        $info_cus['booking_service'] = $booking_service;
        $info_cus['staff_id'] = isset($CMS->input['staff_id']) ? $CMS->input['staff_id'] : null;
        $info_cus['booking_date'] = $booking_date;
        $info_cus['booking_time'] = $booking_time;
        if(isset($CMS->vars['is_restaurant']) and $CMS->vars['is_restaurant'] == 1)
        {
            $info_cus['listperson'] = array_values($CMS->input['person_number']);
        }
        // dsg theme
        else if ( $CMS->vars['theme'] == 'dsg' ) 
        {
            //$info_cus['cosmetic'] = array_values($cosmetic);
        }

        $info_cus['notelist'] = $notelist;

        // print "<pre>";
        // print_r($info_cus);
        // exit;

        $data['ord_content'] = json_encode($info_cus, JSON_UNESCAPED_UNICODE);

        // Create customer
        // Check customer
        if(!$data['cus_id'])
        {
            // print $booking_phone;exit;
            if(!$customer = customer::getCustomerByPhone($booking_phone))
            {
                // Data customer
                $data_cus['cus_email'] = $booking_email;
                $data_cus['cus_full_name'] = $booking_name;
                $data_cus['cus_phone'] = $booking_phone;
                // $data_cus['cus_password'] = $data_cus['cus_repassword'] = $CMS->class->random->character(9);
                $cus_id = customer::createAccount($data_cus);
                $data['cus_id'] = intval($cus_id);
            }else
            {
                $data['cus_id'] = $customer['cus_id'];
            }
        }

        $parseUrl = parse_url($CMS->vars['root_domain']);
        $webDomain = $parseUrl['host'];
        // print "<pre>";print_r($data);exit;
        // Add order booking
        $return = $CMS->order->quick_add($data);
 
        if($return)
        {    
            if($booking_form_email==0)
            {
                // Info sms
                $date_show = $booking_time ? date("g:i A",strtotime($booking_time)) : "";
                $booking_phone_show = self::formatPhone($booking_phone, defined("is_web_us") ? "3-3-4" : "4-3-4");
                $data_sms = [
                    'cusname' => $booking_name,
                    'cusphone' => $booking_phone_show,  
                    'dateshow' => $date_show,
                    'bookingdate' => $booking_date,
                    'servicename' => $sms_service,
                    'staffname' => $sms_staff,
                    'orderid' => (isset($CMS->vars['site_id']) ? $CMS->vars['site_id'] : null)."-".$return['ord_id'],
                    'siteid' => isset($CMS->vars['site_id']) ? $CMS->vars['site_id'] : null,
                    'sitename' => $_SERVER['SERVER_NAME']
                ];

                $sms['sms_from'] = $CMS->vars['sms_number'];
                $sms['sms_to'] = $CMS->vars['company_mobile'];
                $sms['sms_content'] =  isset($sms['sms_content']) ? $sms['sms_content'] : '';
                $sms['sms_content'] .= $CMS->smstpl->renderContent('booking_notifiy_to_owner', $data_sms).($storeInfo['store_name'] ? " - (Storefront {$storeInfo['store_name']})" : '').($data['ord_note'] ? ". Note: ".$sms_note : "");
                $sms['sms_content'] .= ". Website: ".$_SERVER['SERVER_NAME'];
                // Send sms
                $CMS->sms->add($sms);
                if(isset($CMS->vars['is_restaurant']))
                {
                    // Send sms note 01/08/2017
                    // Change content sms
                    $sms['sms_content'] = strip_tags("{$booking_name} - {$booking_phone_show} note: ".$data['ord_note']. ". Website: ".$_SERVER['SERVER_NAME']);
                    $CMS->sms->add($sms);
                }
                
                // Send massage for customer
                if($booking_phone)
                {
                    $sms_cus['sms_from'] =  $storeInfo['store_phone'] ? $storeInfo['store_phone'] : $CMS->vars['sms_number']; //$storeInfo['store_phone'] ? $storeInfo['store_phone'] :
                    $sms_cus['sms_to'] = $booking_phone;
                    $sms_cus['sms_content'] = $CMS->smstpl->renderContent('booking_notify_to_customer').($storeInfo['store_name'] ? " - (Storefront {$storeInfo['store_name']})" : " ");
                    $sms_cus['sms_content'] .= ' - Your appointment information: ' . $date_show.", ".$booking_date;
                    $sms_cus['sms_content'] .= $CMS->vars['company_phone'] ? ' - Hotline: ' . $CMS->vars['company_phone'] : "";
                    $sms_cus['sms_content'] .= ". Website: ".$_SERVER['SERVER_NAME'];
                    // Send sms

                    $CMS->sms->add($sms_cus);
                }
                
            }else // send email
            {
                self::sendEmailInfo($data);
            }

            // Message
            $_SESSION['msg'] = "Cám ơn Quý khách đã đặt lịch hẹn. Chúng tôi sẽ liên hệ Quý khách trong thời gian sớm nhất!";

            return true;
        }else
        {
            return false;
        }

    }

    static function add()
    {
        global $CMS, $DB, $member;

        // Check use captcha home and booking
        // Google ReCaptcha Verifier
        if ( \core\ezy::google_recaptcha_verify() == false ) { return self::createMsg("Captcha is wrong, please try again"); }
        
        // Check token
        if(!\lib\security::check_token())
        {
           return self::createMsg("Please refresh this page then try again!");
        }

        // Validate by ip
        if(intval($CMS->vars['enable_security_ip']))
        {
            if(!\lib\security::checkSecurityIp())
            {
                return false;
            }
        }

        // Input
        $booking_name = isset($CMS->input['booking_name']) ? trim($CMS->input['booking_name']) : (isset($_SESSION['member']['cus_full_name']) ? $_SESSION['member']['cus_full_name'] : null);
        $booking_email = isset($CMS->input['booking_email']) ? trim($CMS->input['booking_email']) : (isset($_SESSION['member']['cus_email']) ? $_SESSION['member']['cus_email'] : null);
        $booking_phone = ltrim(trim(isset($CMS->input['booking_area_code']) ? $CMS->input['booking_area_code'] : ''),"+") . self::trimPhone($CMS->input['booking_phone']);// ? trim($CMS->input['booking_phone']) : $_SESSION['member']['cus_phone'];

        $booking_service = isset($CMS->input['booking_service']) ? ( is_array($CMS->input['booking_service']) ? $CMS->input['booking_service'] : array(trim($CMS->input['booking_service'])) ): $CMS->input['product_id'];

        $booking_date = isset($CMS->input['booking_date']) ? trim($CMS->input['booking_date']) : '';
        $booking_time = isset($CMS->input['booking_time']) ? trim($CMS->input['booking_time']) : trim($CMS->input['booking_hours']);
        $store_id = isset($CMS->input['store_id']) ? intval($CMS->input['store_id']) : 0;

        $storeInfo = store::getInfo($store_id);
        $listperson = isset($CMS->input['person_number']) ? array_values($CMS->input['person_number']) : [];
        $notelist = isset($CMS->input['notelist']) ? (is_array($CMS->input['notelist']) ? array_values($CMS->input['notelist']) : array($CMS->input['notelist']) ) : [];
        // $booking_form_email = isset($CMS->input['booking_form_email']) ? intval($CMS->input['booking_form_email']) : 0;
        $booking_form_email = isset($CMS->vars['booking_email_form_enable']) ? intval($CMS->vars['booking_email_form_enable']) : 0;

        // theme dsg
        $cosmetic =  isset($CMS->input['cosmetic']) ? $CMS->input['cosmetic'] : null;
        $hair_type =  isset($CMS->input['hair_type']) ? $CMS->input['hair_type'] : null;
        $staff_type =  isset($CMS->input['staff_type']) ? $CMS->input['staff_type'] : null;

        // Chuẩn hoá notelist bỏ \n để json_decode ko bị lỗi
        $list_check = [];
        foreach ($notelist as $value) 
        {
            $note_check = str_replace("\n", " ", $value);
            $list_check[] = str_replace("  ", " ", $note_check);
        }
        // Gán lại note list
        $notelist = $list_check;

        // Check input
        if(!isset($CMS->input['nocaptcha']) || !$CMS->input['nocaptcha'])
        {
            if(!$booking_name) { return self::createMsg("Please enter your name!"); }
            // if(!$booking_email) { return self::createMsg("Please enter your email!"); }
            if(!$CMS->class->input->is_email($booking_email) and $booking_email) { return self::createMsg("You entered the wrong email! Please try again"); }
            if(!$booking_service) { return self::createMsg("Please choose a service!"); }
            if(!$booking_date) { return self::createMsg("Please choose a date!"); }
            if(!$booking_time) { return self::createMsg("Please choose a time!"); }
        }

        // Convert du lieu
        $data['service_type'] = 1;
        $data['booking_hours'] = self::convertHours($booking_time);
        $data['booking_form_email'] = $booking_form_email;
        $data['cus_id'] = isset($_SESSION['member']['cus_id']) ? intval($_SESSION['member']['cus_id']) : 0;
        if(isset($CMS->vars['is_cs']) and $CMS->vars['is_cs'] == 1)
        {
            $data['ord_note'] = "Booking Day: ".$booking_date."<br/>".(isset($notelist[0]) ? $notelist[0] : '')."<br/>".$CMS->input['date_sel'];
        }else
        {

            $data['ord_note'] = "Booking Day: ".$booking_date;
        }
        $data['store_id'] = $store_id;
        
        $sms_service = "";
        $str_note = "";
        $i=0;
        if ( $CMS->vars['theme'] == 'dsg' )
        {
           if(!empty($staff_type ))
            {
                $staff_user = $CMS->user->get_info($staff_type);
            }
        }
 
        // Convert data service
        foreach ($booking_service as $product_id) 
        {
            if(!empty($product_id))
            {
                
                $product = $CMS->product->getInfo($product_id);
                $price = $product['product_price_sell'] ? $product['product_price_sell'] : $product['product_price'];
                
                $data['product_id'][] = $product_id;
                $data['product_name'][] = $product['product_name'];
                $data['product_cycle_type'][] = $product['product_cycle'];
                $data['product_cycle'][] = 1;
                $data['product_quantity'][] = 1;
                $data['product_price'][] = $price;
                $sms_service .= $product['product_name'] ? $product['product_name'].", ": "";
                
                if(isset($CMS->vars['is_restaurant']) and $CMS->vars['is_restaurant'] == 1)
                {
                    $str_note .= $product['product_name']." - ".$listperson[$i]." person".". Note: ".$notelist[$i].", "."<br/>";
                  
                }
                // dsg theme
                if ( $CMS->vars['theme'] == 'dsg' )
                {
                    $product_price_add = 0;
                   // $str_note .= $str_note ? ', ' : 'Mỹ phẩm: ';
                  
                    if( $cosmetic  == 2) { $product_price_add += 100000; }
                    if($staff_user['userg_id'] == 15)
                    {
                        $product_price_add += 100000;
                    }

                    if(strpos($product['product_option'],"6") == false) //Dịch vụ khác
                    {
                        $data['product_price_add'][$i] = $product_price_add;
                    }  
                }
                $i++;
            }
        }
        if ( $CMS->vars['theme'] == 'dsg' )
        {
            $product_price_add = 0;
            $str_note .= $str_note ? ', ' : ', ';
            if( $hair_type  == 1) {  $str_note .= "Loại tóc: Tóc nam, " ; }else { $str_note .= "Loại tóc: Tóc nữ, " ; }
            if( $cosmetic  == 1) {  $str_note .= "Mỹ phẩm: thường, " ; }else { $str_note .= "Mỹ phẩm: cao cấp, " ;   }
               $str_note .= "Nhân viên: {$staff_user['user_display_name']} " ;  
        }
 
        $sms_service = rtrim($sms_service,", ");

        // Check staff ID
        $sms_staff = "";
        if(isset($CMS->input['staff_id']) && $CMS->input['staff_id'])
        {
            foreach ($CMS->input['staff_id'] as $user_id) 
            {
                $name = $CMS->user->get_info($user_id, "user_display_name");
                $data['product_description'][] = "Staff: ".$name;
                $sms_staff .= $name ? $name.", ": "";
            } 
        }else
        {
            $data['product_description'] = [];
        }

        $sms_staff = rtrim($sms_staff,", ");
        $sms_note = "";
        if(isset($CMS->vars['is_restaurant']) and $CMS->vars['is_restaurant'] == 1)
        {
            $data['ord_note'] .= "<br/>".$str_note;
        }
        // dsg theme
        else if ( $CMS->vars['theme'] == 'dsg' )
        {
            $data['ord_note'] .= "<br/>".$str_note;
        }
        else
        {
            $data['ord_note'] .= " <br/>".(isset($notelist[0]) ? $notelist[0] : '');
            $sms_note = isset($notelist[0]) ? $notelist[0] : '';
        }

        // order content
        $info_cus = [];
        $info_cus['cus_name'] = $booking_name;
        $info_cus['cus_email'] = $booking_email;
        $info_cus['cus_phone'] = $booking_phone;
        $info_cus['booking_service'] = $booking_service;
        $info_cus['staff_id'] = isset($CMS->input['staff_id']) ? $CMS->input['staff_id'] : null;
        $info_cus['booking_date'] = $booking_date;
        $info_cus['booking_time'] = $booking_time;
        if(isset($CMS->vars['is_restaurant']) and $CMS->vars['is_restaurant'] == 1)
        {
            $info_cus['listperson'] = array_values($CMS->input['person_number']);
        }
        // dsg theme
        else if ( $CMS->vars['theme'] == 'dsg' ) 
        {
            //$info_cus['cosmetic'] = array_values($cosmetic);
        }

        $info_cus['notelist'] = $notelist;

        // print "<pre>";
        // print_r($info_cus);
        // exit;

        $data['ord_content'] = json_encode($info_cus, JSON_UNESCAPED_UNICODE);

        // Create customer
        // Check customer
        if(!$data['cus_id'])
        {
            // print $booking_phone;exit;
            if(!$customer = customer::getCustomerByPhone($booking_phone))
            {
                // Data customer
                $data_cus['cus_email'] = $booking_email;
                $data_cus['cus_full_name'] = $booking_name;
                $data_cus['cus_phone'] = $booking_phone;
                // $data_cus['cus_password'] = $data_cus['cus_repassword'] = $CMS->class->random->character(9);
                $cus_id = customer::createAccount($data_cus);
                $data['cus_id'] = intval($cus_id);
            }else
            {
                $data['cus_id'] = $customer['cus_id'];
            }
        }

        $parseUrl = parse_url($CMS->vars['root_domain']);
        $webDomain = $parseUrl['host'];
        // print "<pre>";print_r($data);exit;
        // Add order booking
        $return = $CMS->order->quick_add($data);
 
        if($return)
        {    
            if($booking_form_email==0)
            {
                // Info sms
                $date_show = $booking_time ? date("g:i A",strtotime($booking_time)) : "";
                $booking_phone_show = self::formatPhone($booking_phone, defined("is_web_us") ? "3-3-4" : "4-3-4");
                $data_sms = [
                    'cusname' => $booking_name,
                    'cusphone' => $booking_phone_show,  
                    'dateshow' => $date_show,
                    'bookingdate' => $booking_date,
                    'servicename' => $sms_service,
                    'staffname' => $sms_staff,
                    'orderid' => (isset($CMS->vars['site_id']) ? $CMS->vars['site_id'] : null)."-".$return['ord_id'],
                    'siteid' => isset($CMS->vars['site_id']) ? $CMS->vars['site_id'] : null,
                    'sitename' => $_SERVER['SERVER_NAME']
                ];

                $sms['sms_from'] = $CMS->vars['sms_number'];
                $sms['sms_to'] = $CMS->vars['company_mobile'];
                $sms['sms_content'] =  isset($sms['sms_content']) ? $sms['sms_content'] : '';
                $sms['sms_content'] .= $CMS->smstpl->renderContent('booking_notifiy_to_owner', $data_sms).($storeInfo['store_name'] ? " - (Storefront {$storeInfo['store_name']})" : '').($data['ord_note'] ? ". Note: ".$sms_note : "");
                $sms['sms_content'] .= ". Website: ".$_SERVER['SERVER_NAME'];
                // Send sms
                $CMS->sms->add($sms);
                if(isset($CMS->vars['is_restaurant']))
                {
                    // Send sms note 01/08/2017
                    // Change content sms
                    $sms['sms_content'] = strip_tags("{$booking_name} - {$booking_phone_show} note: ".$data['ord_note']. ". Website: ".$_SERVER['SERVER_NAME']);
                    $CMS->sms->add($sms);
                }
                
                // Send massage for customer
                if($booking_phone)
                {
                    $sms_cus['sms_from'] =  $storeInfo['store_phone'] ? $storeInfo['store_phone'] : $CMS->vars['sms_number']; //$storeInfo['store_phone'] ? $storeInfo['store_phone'] :
                    $sms_cus['sms_to'] = $booking_phone;
                    $sms_cus['sms_content'] = $CMS->smstpl->renderContent('booking_notify_to_customer').($storeInfo['store_name'] ? " - (Storefront {$storeInfo['store_name']})" : " ");
                    $sms_cus['sms_content'] .= ' - Your appointment information: ' . $date_show.", ".$booking_date;
                    $sms_cus['sms_content'] .= $CMS->vars['company_phone'] ? ' - Hotline: ' . $CMS->vars['company_phone'] : "";
                    $sms_cus['sms_content'] .= ". Website: ".$_SERVER['SERVER_NAME'];
                    // Send sms

                    $CMS->sms->add($sms_cus);
                }
                
            }else // send email
            {
                self::sendEmailInfo($data);
            }

            // Message
            $_SESSION['msg'] = "Thank you for requesting appointment with us. We will check your availability and get back to you asap";

            return true;
        }else
        {
            return false;
        }

    }

    static function createMsg($message="",$status="error")
    {
        if($status == "error")
        {
            $_SESSION['error_msg'] = $message;
            return false;
        }else
        {
            $_SESSION['msg'] = $message;
            return true;
        }
        // $msg = array("status" => "{$status}",
        //         "hidden" => "display: block",
        //         "message" => "{$message}");
        // return $msg;
    }

    static function saveForm($first=0)
    {
        global $CMS;

        $form = [];
        $booking_service = isset($CMS->input['product_id']) && is_array($CMS->input['product_id']) ? array_values($CMS->input['product_id']) : (isset(ezy::$input['service']) ? array(ezy::$input['service']) : []);
      
        $booking_staff = isset($CMS->input['staff_id']) && is_array($CMS->input['staff_id']) ? array_values($CMS->input['staff_id']) : [];
        $form['listperson'] = isset($CMS->input['person_number']) && is_array($CMS->input['person_number']) ? array_values($CMS->input['person_number']) : [];
        $form['notelist'] = isset($CMS->input['notelist']) && is_array($CMS->input['notelist']) ? array_values($CMS->input['notelist']) : [];
        $format_date = $CMS->vars['dateformat_php'][$CMS->vars['date_format']];

        // print_r($booking_service);exit;
        $checkdate = self::checkTimeBooking();
        // Checkdate = 1 la cho booking trong ngày,  ngược lại thì booking qua ngày hôm sau.
        $day_add = intval($CMS->vars['booking_before_day']) > 0 ? intval($CMS->vars['booking_before_day']) : 1;
        if($first)
        {
            // Vì saveform chạy trước khi set date nên lần đầu sẽ lấy ngày theo checkdate
            $form['booking_date'] = $checkdate == 1 ? date("{$format_date}") : date("{$format_date}", strtotime("+{$day_add} day"));
        }else
        {
            $form['booking_date'] = isset($CMS->input['booking_date']) ? $CMS->input['booking_date'] : ($checkdate == 1 ? date("{$format_date}") : date("{$format_date}", strtotime("+{$day_add} day")));
        }
        
        $form['booking_hours'] = isset($CMS->input['booking_hours']) ? trim($CMS->input['booking_hours']) : '';
        
        $count = count($booking_service);
        if($count)
        {
            for($i=0;$i<$count;$i++)
            {
                if($booking_service[$i])
                {
                    $booking_staff[$i] = isset($booking_staff[$i]) ? $booking_staff[$i] : '';
                    $form['service_staff'][$i] = $booking_service[$i] .",". $booking_staff[$i];
                }
            }
            
            // $form['service_staff'] = $form['service_staff'] ? $form['service_staff'] : ($form['service_staff'][0] = []);
        }else
        {
            // $form['service_staff'][0] = [];
        }

        $_SESSION['form_bk'] = json_encode($form, JSON_UNESCAPED_UNICODE);
        $_SESSION['checkservice'] = isset(ezy::$input['service']) ? ezy::$input['service'] : (isset($_SESSION['checkservice']) ? $_SESSION['checkservice'] : null);
        // print "<pre>";
        // print_r($_SESSION['form_bk']);exit;
        return $_SESSION['form_bk'];
    }

    static function checkTimeBooking()
    {
        global $CMS;

        if(intval($CMS->vars['booking_before_day']) > 0)
        {
            return 0;
        }
        $checkreturn = 0;
        $before_hours = intval($CMS->vars['booking_before_hours']);
        if($CMS->vars['booking_open_hours'] == 1)
        {
            $arr_week = json_decode($CMS->vars['open_hours'],true);
            $arr_dayofweek = array(1 => "monday", 2 => "tuesday", 3 => "wednesday", 4 => "thursday", 5 => "friday", 6 => "saturday", 7 => "sunday");
            $num_date = date("N", time());
            $key_day = $arr_dayofweek[$num_date];

            if($arr_week[$key_day]['checked'] == 0)
            {
                return 0;
            }else
            {
                $listTime[] = explode(" ",$arr_week[$key_day]['close'])[0];
            }
        }else
        {
            // Check theo giờ buổi chiều của loại ko lấy theo giờ mở cửa
            $listTime = json_decode($CMS->vars['booking_hours_afternoon'],true);
        }
        
        $checkdate = strtotime("+{$before_hours} hours");
        $currdate = strtotime("today") + 24*3600; // Cộng thêm 1 ngày để so sánh với $checkdate
        // Kiểm tra ngày check có lớn hơn ngày hiện tại đã cộng 1 day hay ko?
        if($checkdate < $currdate)
        {
            $currTime = date("H:i",strtotime("+{$before_hours} hours"));
            $timecurr = explode(":", $currTime);
            
            foreach ($listTime as $key => $value) 
            {
                $timecheck = explode(":", $value);

                // Compare Hours            
                if($timecheck[0] > $timecurr[0])
                {
                    $checkreturn ++;
                }elseif($timecheck[0] == $timecurr[0])
                {
                    // Compare Minute 
                    if($timecheck[1] > $timecurr[1])
                    {
                        $checkreturn ++;
                    }
                }
            }
        }

        // return 1: Booking trong ngày
        // return 0: Booking ngày mai
        return $checkreturn > 0 ? 1 : 0;
    }

    static function getCountBooking($cus_id)
    {
        global $CMS, $DB, $member;

        $cus_id = $cus_id ? $cus_id : $member['cus_id'];

        $sql = "SELECT ord_id FROM ".root_table."order WHERE service_type = 1 AND ord_deleted = 0 AND ord_status = 1 AND ord_note <> '' AND ord_content <> '' AND cus_id = '{$cus_id}' AND UNIX_TIMESTAMP(STR_TO_DATE(RIGHT(ord_note, 10), '%m/%d/%Y')) >= CURRENT_TIMESTAMP() ";
        $sql = $DB->query($sql);
        
        return $DB->num_rows($sql);
    }

    static function trimPhone($phone="")
    {
        global $CMS;

        $phone = ltrim($phone,"0");
        $phone = str_replace(" ", "", $phone);
        $phone = str_replace("-", "", $phone);
        $phone = str_replace("_", "", $phone);
        $phone = str_replace("(", "", $phone);
        $phone = str_replace(")", "", $phone);
        
        // Return
        return $phone;
    }

    static function getopenhours($type="", $num_date=0)
    {
        global $CMS;

        $arr_dayofweek = array(1 => "monday", 2 => "tuesday", 3 => "wednesday", 4 => "thursday", 5 => "friday", 6 => "saturday", 7 => "sunday");
        // run by session
        if( (isset($_SESSION['form_bk']) && $_SESSION['form_bk']) and !$num_date)
        {
            $date_sess = json_decode($_SESSION['form_bk'],1); 
            $date_sess = $date_sess['booking_date']; 
            $time = $CMS->class->date->date2time($date_sess);
            $num_date = date("N", $time);
        }

        // print $num_date;exit;
        $num_date = $num_date ? $num_date : $CMS->vars['this_dayofweek'];
        $key_day = $arr_dayofweek[$num_date];
        $arr_open_hours = json_decode($CMS->vars['open_hours'], 1);
        $output = [];
        $last_hours = "";
        $time_more=0;
        
        // Buoi sang
        if($type == "morning")
        {
            // Lấy thời gian theo giờ mở cửa
            $morning = $arr_open_hours[$key_day];
            if($morning['checked'] == 1 and $morning['open'] != "")
            {
                $hours_start = intval($morning['open']);
                $list = explode(" ", $morning['open']);
                if($list[1] == "pm") { return $output; }// Dung cho truong hop KH chon gio mo cua buoi sang la > 12 pm
                $part_time = explode(":", $list[0]);
                $time_over = intval($part_time[0]);
                $last_hours = $list[0];

                // Check theo thời gian hiện tại và giờ mở cửa
                if($time_over < 12) 
                { 
                    // print $CMS->vars['step_time_booking'];exit;
                    if($CMS->vars['step_time_booking'] == 15)
                    {
                        if(intval($part_time[1]) ==0)
                        {
                            $output[] = $time_over.":00";
                            $output[] = $time_over.":15"; 
                            $output[] = $time_over.":30";
                            $output[] = $time_over.":45"; 
                        }elseif(intval($part_time[1]) ==15)
                        {
                            $output[] = $time_over.":15"; 
                            $output[] = $time_over.":30";
                            $output[] = $time_over.":45";  
                        }elseif(intval($part_time[1]) ==30)
                        {
                            $output[] = $time_over.":30";
                            $output[] = $time_over.":45"; 
                        }elseif(intval($part_time[1]) ==45)
                        {
                            $output[] = $time_over.":45"; 
                        }
                    }elseif($CMS->vars['step_time_booking'] == 30)
                    {
                        if(intval($part_time[1]) ==0)
                        {
                            $output[] = $time_over.":00"; 
                            $output[] = $last_hours = $time_over.":30"; 
                        }elseif(intval($part_time[1]) ==15)
                        {
                            $output[] = $time_over.":15"; 
                            $output[] = $last_hours = $time_over.":45"; 
                        }elseif(intval($part_time[1]) ==30)
                        {
                            $output[] = $time_over.":30";
                            $output[] = $last_hours = ($time_over+1).":00"; 
                        }elseif(intval($part_time[1]) ==45)
                        {
                            $output[] = $time_over.":45";
                            if($time_over+1 < 12)
                            {
                                $output[] = $last_hours = ($time_over+1).":15";
                            }
                        }
                    }elseif($CMS->vars['step_time_booking'] == 60)
                    {
                        if(intval($part_time[1]) ==0)
                        {
                            $output[] = $last_hours = $time_over.":00"; 
                        }elseif(intval($part_time[1]) ==15)
                        {
                            $output[] = $time_over.":15"; 
                            $output[] = $last_hours = ($time_over+1).":15";
                            $hours_start ++;
                        }elseif(intval($part_time[1]) ==30)
                        {
                            $output[] = $time_over.":30"; 
                            $output[] = $last_hours = ($time_over+1).":30";
                            $hours_start ++;
                        }elseif(intval($part_time[1]) ==45)
                        {
                            $output[] = $time_over.":45"; 
                            if($time_over+1 < 12)
                            {
                                $output[] = $last_hours = ($time_over+1).":45"; 
                            }
                            $hours_start ++;
                        }
                    }
                    $hours_start ++;
                }
                // print $hours_start;exit;    
                // Mốc là 12h trưa
                for($x=$hours_start; $x < 12; $x++)
                {
                    if($CMS->vars['step_time_booking'] == 15)
                    {
                        $output[] = "{$x}:00";
                        $output[] = "{$x}:15";
                        $output[] = "{$x}:30";
                        $output[] = "{$x}:45";
                    }elseif($CMS->vars['step_time_booking'] == 30)
                    {
                        $hrs = explode(":", $last_hours);
                        $step = $CMS->vars['step_time_booking'];
                        if(intval($hrs[1]) == 0)
                        {
                            $output[] = $last_hours = "{$x}:".$step;
                        }elseif(intval($hrs[1]) == 15)
                        {
                            $output[] = $last_hours = "{$x}:".(15+$step);
                        }elseif(intval($hrs[1]) == 30)
                        {
                            $x--;
                            $output[] = $last_hours = ($x+1).":00";
                        }elseif(intval($hrs[1]) == 45)
                        {
                            $x--;
                            $output[] = $last_hours = ($x+1).":15";
                        }
                        
                    }
                    elseif($CMS->vars['step_time_booking'] == 60)
                    {
                        $hrs = explode(":", $last_hours);
                        $output[] = $last_hours = "{$x}:".$hrs[1];
                    }
                    
                }

            }
        }else
        // Buoi chiều
        {

            $afternoon = $arr_open_hours[$key_day];
            // Check morning
            $list = explode(" ", $afternoon['open']);
            $check_time = "";
            // set biến check time
            $part_time = explode(":", $list[0]);
            
            $last_hours="";
            if($list[1] == "pm") 
            { 
                $check_time = $list[0]; 
                // Thời gian đầu
                if(intval($part_time[1]) > 0) 
                { 
                    if(intval($part_time[0]) != 12)
                    {
                        if($CMS->vars['step_time_booking'] == 15)
                        {
                            $time = $part_time[0] + 12;
                            if(intval($part_time[1]) == 0)
                            {
                                $output[] = $time.":00";
                                $output[] = $time.":15";
                                $output[] = $time.":30";
                                $output[]= $last_hours = $time.":45";
                            }elseif(intval($part_time[1]) == 15)
                            {
                                $output[] = $time.":15";
                                $output[] = $time.":30";
                                $output[]= $last_hours = $time.":45";
                            }elseif(intval($part_time[1]) == 30)
                            {
                                $output[] = $time.":30";
                                $output[]= $last_hours = $time.":45";
                            }elseif(intval($part_time[1]) == 45)
                            {
                                $output[]= $last_hours = $time.":45";
                            }
                        }elseif($CMS->vars['step_time_booking'] == 30)
                        {
                            $time = $part_time[0] + 12;
                            if(intval($part_time[1]) == 0)
                            {
                                $output[] = $time.":00";
                                $output[]= $last_hours = $time.":30";
                            }elseif(intval($part_time[1]) == 15)
                            {
                                $output[] = $time.":15";
                                $output[]= $last_hours = $time.":45";
                            }elseif(intval($part_time[1]) == 30 or intval($part_time[1]) == 45)
                            {
                                $output[]= $last_hours = $time.":{$part_time[1]}";
                            }
                        }elseif($CMS->vars['step_time_booking'] == 60)
                        {
                            $output[]= $last_hours = $list[0];
                        }


                    }// end #12h
                    else
                    {
                        if($CMS->vars['step_time_booking'] == 15)
                        {
                            $time = $part_time[0];
                            if(intval($part_time[1]) == 0)
                            {
                                $output[] = $time.":00";
                                $output[] = $time.":15";
                                $output[] = $time.":30";
                                $output[]= $last_hours = $time.":45";
                            }elseif(intval($part_time[1]) == 15)
                            {
                                $output[] = $time.":15";
                                $output[] = $time.":30";
                                $output[]= $last_hours = $time.":45";
                            }elseif(intval($part_time[1]) == 30)
                            {
                                $output[] = $time.":30";
                                $output[]= $last_hours = $time.":45";
                            }elseif(intval($part_time[1]) == 45)
                            {
                                $output[]= $last_hours = $time.":45";
                            }
                        }elseif($CMS->vars['step_time_booking'] == 30)
                        {
                            $time = $part_time[0];
                            if(intval($part_time[1]) == 0)
                            {
                                $output[] = $time.":00";
                                $output[]= $last_hours = $time.":30";
                            }elseif(intval($part_time[1]) == 15)
                            {
                                $output[] = $time.":15";
                                $output[]= $last_hours = $time.":45";
                            }elseif(intval($part_time[1]) == 30 or intval($part_time[1]) == 45)
                            {
                                $output[]= $last_hours = $time.":{$part_time[1]}";
                            }
                        }elseif($CMS->vars['step_time_booking'] == 60)
                        {
                            $output[]= $last_hours = $list[0];
                        }
                      
                    }// end 12h
                    $time_more++;
                }
            }
            
            // Check afternoon
            $list_af = explode(":", explode(" ", $afternoon['close'])[0]);
            
            if($afternoon['checked'] == 1 and $afternoon['close'] != "")
            {
                $hours_start = ($check_time and intval($check_time) != 12) ? intval($check_time): 0;
                $hours_start = $hours_start + $time_more;
                $afternoon['close'] = ($list_af[0]).":".$list_af[1];
                $hours_end = intval($afternoon['close']);

                if(intval($list_af[0]) != 12)
                {
                    // Mốc là 12h trưa
                    for($x=$hours_start; $x <= $hours_end; $x++)
                    {
                        $last_hours = !$last_hours ? ($hours_start+12).":".$part_time[1] : $last_hours;
                        if($x==0)
                        {
                            if($CMS->vars['step_time_booking'] == 15)
                            {
                                $output[] = "12:00";
                                $output[] = "12:15";
                                $output[] = "12:30";
                                $output[] = $last_hours = "12:45";
                            }elseif($CMS->vars['step_time_booking'] == 30)
                            {
                                if(intval($part_time[1]) == 0 or intval($part_time[1]) == 30)
                                {
                                    $output[] = "12:00";
                                    $output[] = $last_hours = "12:30";
                                }elseif(intval($part_time[1]) == 15 or intval($part_time[1]) == 45)
                                {
                                    $output[] = "12:15";
                                    $output[] = $last_hours = "12:45";
                                }
                                
                            }elseif($CMS->vars['step_time_booking'] == 60)
                            {
                                $output[] = $last_hours = "12:".$part_time[1];
                            }
                        }else
                        {
                            // print $x;exit;
                            $time_show = $x+12;
                            $hrs = explode(":", $last_hours);
                            $step = $CMS->vars['step_time_booking'];
                            if($CMS->vars['step_time_booking'] == 15)
                            {
                                if($x == $hours_end)
                                {
                                    $output[] = "{$time_show}:00";
                                    if(intval($list_af[1]) == 0)
                                    {
                                        return $output;
                                    }

                                    $output[] = "{$time_show}:15";
                                    if(intval($list_af[1]) == 15)
                                    {
                                        return $output;
                                    }

                                    $output[] = "{$time_show}:30";
                                    if(intval($list_af[1]) == 30)
                                    {
                                        return $output;
                                    }
                                    $output[] = "{$time_show}:45";
                                    if(intval($list_af[1]) == 45)
                                    {
                                        return $output;
                                    }
                                }else
                                {
                                    $output[] = "{$time_show}:00";
                                    $output[] = "{$time_show}:15";
                                    $output[] = "{$time_show}:30";
                                    $output[] = "{$time_show}:45";
                                }
                                
                            }elseif($CMS->vars['step_time_booking'] == 30)
                            {
                                if($x == $hours_end)
                                {
                                    if(intval($hrs[1]) == 0)
                                    {
                                        if(intval($hrs[1]) + 30 >= intval($list_af[1]))
                                        {
                                            $output[] = "{$time_show}:00";
                                            if(intval($list_af[1]) > 0)
                                            {
                                                $output[] = "{$time_show}:{$list_af[1]}";
                                            }
                                            return $output;
                                        }else{
                                            $output[] = "{$time_show}:00";
                                            $output[] = "{$time_show}:30";
                                            $output[] = "{$time_show}:{$list_af[1]}";
                                            return $output;
                                        }
                                    }elseif(intval($hrs[1]) == 15 )
                                    {
                                        if(intval($hrs[1]) + 30 >= intval($list_af[1]))
                                        {
                                            $output[] = "{$time_show}:15";
                                            $output[] = "{$time_show}:{$list_af[1]}";
                                            return $output;
                                        }

                                    }elseif(intval($hrs[1]) == 30 )
                                    {
                                        $output[] = "{$time_show}:00";
                                        
                                        if(intval($list_af[1]) > 30)
                                        {
                                            $output[] = "{$time_show}:30";
                                            $output[] = "{$time_show}:{$list_af[1]}";
                                        }elseif(intval($list_af[1]) >= 15){
                                            $output[] = "{$time_show}:{$list_af[1]}";
                                        }
                                        return $output;

                                    }elseif(intval($hrs[1]) == 45 )
                                    {
                                        if(intval($hrs[1]) == intval($list_af[1]) or (intval($hrs[1]) > intval($list_af[1]) and intval($list_af[1]) > 15))
                                        {
                                            $output[] = "{$time_show}:15";
                                        }
                                        $output[] = "{$time_show}:{$list_af[1]}";
                                        return $output;
                                    }
                                }else
                                {
                                    if(intval($hrs[1]) == 30)
                                    {
                                        $output[] = "{$time_show}:00";
                                        $output[] = "{$time_show}:30";
                                    }elseif(intval($hrs[1]) == 45)
                                    {
                                        $output[] = "{$time_show}:15";
                                        $output[] = "{$time_show}:45";
                                    }
                                }
                                
                            }elseif($CMS->vars['step_time_booking'] == 60)
                            {
                                if($x == $hours_end)
                                {

                                    if(intval($hrs[1]) > intval($list_af[1]))
                                    {
                                        $output[] = "{$time_show}:{$list_af[1]}";
                                    }else
                                    {
                                        $output[] = "{$time_show}:{$hrs[1]}";
                                    }

                                    if(intval($list_af[1]) > intval($hrs[1]) )
                                    {
                                        $output[] = "{$time_show}:{$list_af[1]}";
                                    }

                                    return $output;
                                    
                                }else
                                {
                                    $output[] = "{$time_show}:".$hrs[1];
                                }
                            }

                        }
                    }
                }
               
            }
        }

        return $output;
    }

    static function sendEmailInfo($data=[])
    {
        global $CMS, $DB;

        // Email
        $str_email = (isset($CMS->vars['company_email_cc']) ? $CMS->vars['company_email_cc'] : "") . ", ".$CMS->vars['company_email'];
        $list_email = explode(",", str_replace(" ", "", $str_email));
        $list_email = array_unique($list_email);
        foreach ($list_email as $email) 
        {
            // Check email
            if(!$email or !filter_var($email, FILTER_VALIDATE_EMAIL)) { continue; }
// print "<pre>";print_r($data);exit;
            $time = $CMS->class->date->date_format(time());
            $CMS->email->email_template = "send_information_appointment";
            $CMS->email->email_to = $email;
            $CMS->email->email_toname = $CMS->vars['company_name'];

            // email_from
            $customer = json_decode($data['ord_content'],1);
            $cusphone = self::formatPhone($customer['cus_phone'], defined("is_web_us") ? "3-3-4" : "4-3-4");
            $CMS->email->data['date_send'] = $time;
            $CMS->email->data['cus_name'] = $customer['cus_name'];
            $CMS->email->data['cus_phone'] = $cusphone;
            $CMS->email->data['cus_email'] = $customer['cus_email'];
            $CMS->email->data['service'] = implode(", ", $data['product_name']);
            $CMS->email->data['technician'] = ltrim(implode(", ", $data['product_description']), "Staff: ");
            $CMS->email->data['booking_date'] = $customer['booking_date'];
            $CMS->email->data['booking_theday'] = $CMS->class->date->dateToTheDay($customer['booking_date']);
            $hrs = explode(":",$customer['booking_time']);
            $CMS->email->data['booking_hours'] = $hrs[0] > 12 ? ($hrs[0] - 12).":".$hrs[1] : $customer['booking_time'];
            $CMS->email->data['cus_note'] = implode(", ", $customer['notelist']);

            $CMS->email->data['to_name'] = "Admin";
            $CMS->email->data['title_email'] = "Order information";
            $CMS->email->data['title_content'] = "You have an appointment from phone number {$cusphone}";
            
            $website_title_text = strip_tags($CMS->vars['website_title']);
            $CMS->email->data['logo_website'] = "<img src=\"{$CMS->vars['upload_url']}/attach/{$CMS->vars['logo_website']}\" alt=\"{$website_title_text}\" width=\"124\" editable=\"true\" label=\"{$website_title_text}\" />";
            $CMS->email->data['website_title'] = $CMS->vars['website_title'];
            $CMS->email->data['company_email'] = $CMS->vars['company_email'];
            $CMS->email->data['company_phone'] = $CMS->vars['company_phone'];
            $CMS->email->data['website_link'] = "<a href=\"{$CMS->vars['root_domain']}\">{$CMS->vars['root_domain']}</a>";
            
            $CMS->email->quick_send(0,0);
        }// End for

        if(!empty($customer['cus_email']))
        {
             
            $CMS->email->email_template = "send_information_appointment";
            $CMS->email->email_to = $customer['cus_email'];
            $CMS->email->email_toname = $customer['cus_name'];

            // email_from
            $CMS->email->data['to_name'] = $customer['cus_name'];
            $CMS->email->data['title_email'] = "Appointment information";
            $CMS->email->data['title_content'] = "You have made an appointment on the website: {$CMS->vars['root_domain']}";
            $CMS->email->quick_send(0,0);
        }
    }


    // FOR TRAVEL
    static function add_travel()
    {
        global $CMS, $DB, $member;

        // Check use captcha home and booking
        // Google ReCaptcha Verifier
        if ( \core\ezy::google_recaptcha_verify() == false ) { return self::createMsg("Captcha is wrong, please try again"); }
        
        // Check token
        if(!\lib\security::check_token())
        {
            return self::createMsg("Please refresh page (F5) then try again.");
        }

        // Validate by ip
        if(intval($CMS->vars['enable_security_ip']))
        {
            if(!\lib\security::checkSecurityIp())
            {
                return false;
            }
        }

        // Input
        $booking_name = isset($CMS->input['booking_name']) ? trim($CMS->input['booking_name']) : $_SESSION['member']['cus_full_name'];
        $booking_email = isset($CMS->input['booking_email']) ? trim($CMS->input['booking_email']) : $_SESSION['member']['cus_email'];
        $booking_phone = self::trimPhone($CMS->input['booking_phone']);
        $booking_fax = $CMS->input['booking_fax'];
        $booking_address = isset($CMS->input['booking_address']) ? trim($CMS->input['booking_address']) : "";

        $booking_service = isset($CMS->input['booking_service']) ? $CMS->input['booking_service']: 0;
        $booking_date_start = trim($CMS->input['date_start']);
        $booking_date_end = trim($CMS->input['date_end']);
        $guest_total = intval($CMS->input['guest_total']);
        $guest_adult = intval($CMS->input['guest_adult']);
        $guest_child_1 = intval($CMS->input['guest_child_1']);
        $guest_child_2 = intval($CMS->input['guest_child_2']);

        $booking_note = trim($CMS->input['booking_note']);

        // Check input
        if(!$booking_name) { return self::createMsg("Please enter your name!"); }
        if(!$booking_email) { return self::createMsg("Please enter your email!"); }
        if(!$CMS->class->input->is_email($booking_email) and $booking_email) { return self::createMsg("You entered the wrong email! Please try again"); }

        // if(!$booking_service) { return self::createMsg("Please choose a service!"); }
        if(!$booking_date_start) { return self::createMsg("Please choose a date start!"); }
        if(!$booking_date_end) { return self::createMsg("Please choose a date end!"); }
        if(!$guest_total) { return self::createMsg("Please enter the number of people!"); }
        if(!$guest_adult) { return self::createMsg("Please enter the adult number!"); }

        // Convert du lieu
        $data['service_type'] = 0;
        $data['cus_id'] = isset($_SESSION['member']['cus_id']) ? intval($_SESSION['member']['cus_id']) : 0;
        $data['ord_note'] = $booking_note;
        
        // Convert data service
        foreach ($booking_service as $product_id) 
        {
            $product = $CMS->product->getInfo($product_id);
            $price = $product['product_price_sell'] ? $product['product_price_sell'] : $product['product_price'];
            
            $data['product_id'][] = $product_id;
            $data['product_name'][] = $product['product_name'];
            $data['product_cycle_type'][] = $product['product_cycle'];
            $data['product_cycle'][] = 1;
            $data['product_quantity'][] = 1;
            $data['product_price'][] = $price;
            
        }

        // order content
        $info_cus = [];
        $info_cus['cus_name'] = $booking_name;
        $info_cus['cus_email'] = $booking_email;
        $info_cus['cus_phone'] = $booking_phone;
        $info_cus['booking_service'] = $booking_service;
        $info_cus['booking_date_start'] = $booking_date_start;
        $info_cus['booking_date_end'] = $booking_date_end;
        $info_cus['booking_address'] = $booking_address;
        $info_cus['booking_fax'] = $booking_fax;
        $info_cus['guest_total'] = $guest_total;
        $info_cus['guest_adult'] = $guest_adult;
        $info_cus['guest_child_1'] = $guest_child_1;
        $info_cus['guest_child_2'] = $guest_child_2;
        $info_cus['cus_note'] = $booking_note;

        
        $data['ord_content'] = json_encode($info_cus, JSON_UNESCAPED_UNICODE);
        
        // Create customer
        // Check customer
        if(!$data['cus_id'])
        {
            // print $booking_phone;exit;
            if(!$customer = customer::getCustomerByPhone($booking_phone))
            {
                // Data customer
                $data_cus['cus_email'] = $booking_email;
                $data_cus['cus_full_name'] = $booking_name;
                $data_cus['cus_phone'] = $booking_phone;
                // $data_cus['cus_password'] = $data_cus['cus_repassword'] = $CMS->class->random->character(9);
                $cus_id = customer::createAccount($data_cus);
                $data['cus_id'] = intval($cus_id);
            }else
            {
                $data['cus_id'] = $customer['cus_id'];
            }
        }

// print "<pre>";print_r($data);exit;
        // Add order booking
        $return = $CMS->order->quick_add($data);
// print "<pre>";print_r($return);exit;
        if($return)
        {
            self::sendEmailTravel($data);
            // Message
            $_SESSION['msg'] = "Cám ơn quý khách đã gửi yêu cầu đặt tour, chúng tôi sẽ liên hệ lại với quy khách sớm nhất có thể.";

            return true;
        }else
        {
            $_SESSION['msg_error'] = "Có lỗi trong quá trình gửi yêu cầu. Vui lòng thử lại.";
            return false;
        }

    }

    static function sendEmailTravel($data=[])
    {
        global $CMS, $DB;
        $str_email = (isset($CMS->vars['company_email_cc']) ? $CMS->vars['company_email_cc'] : "") . ", ".$CMS->vars['company_email'];
        $list_email = explode(",", str_replace(" ", "", $str_email));
        $list_email = array_unique($list_email);
        foreach ($list_email as $email) 
        {
            // Check email
            if(!$email or !filter_var($email, FILTER_VALIDATE_EMAIL)) { continue; }
// print "<pre>";print_r($data);exit;
            $time = $CMS->class->date->date_format(time());
            $CMS->email->email_template = "contact_travel";
            $CMS->email->email_to = $email;
            $CMS->email->email_toname = $CMS->vars['company_name'];

            // email_from
            $customer = json_decode($data['ord_content'],1);
            $CMS->email->data['date_send'] = $time;
            $CMS->email->data['cus_name'] = $customer['cus_name'];
            $CMS->email->data['cus_phone'] = $customer['cus_phone'];
            $CMS->email->data['cus_email'] = $customer['cus_email'];
            $CMS->email->data['cus_fax'] = $customer['booking_fax'];
            $CMS->email->data['cus_address'] = $customer['booking_address'];
            $CMS->email->data['total_people'] = $customer['guest_total'];
            $CMS->email->data['number_adult'] = $customer['guest_adult'];
            $CMS->email->data['childrent_1'] = $customer['guest_child_1'];
            $CMS->email->data['childrent_2'] = $customer['guest_child_2'];


            $CMS->email->data['travel_name'] = implode(", ", $data['product_name']);
            $CMS->email->data['booking_date_start'] = $customer['booking_date_start'];
            $CMS->email->data['booking_date_end'] = $customer['booking_date_end'];
            $CMS->email->data['cus_note'] = $customer['cus_note'];

            $CMS->email->data['to_name'] = "Admin";
            $CMS->email->data['title_email'] = "Contact about travel from customer";
            $CMS->email->data['title_content'] = "You have a contact from {$customer['cus_name']}";
            $CMS->email->data['logo_website'] = "<img src=\"{$CMS->vars['upload_url']}/{$CMS->vars['logo_website']}\" alt=\"{$CMS->vars['website_title']}\" width=\"124\" editable=\"true\" label=\"{$CMS->vars['website_title']}\" />";
            $CMS->email->data['website_title'] = $CMS->vars['website_title'];
            $CMS->email->data['company_email'] = $CMS->vars['company_email'];
            $CMS->email->data['company_phone'] = $CMS->vars['company_phone'];
            $CMS->email->quick_send(0,0);
        }
    }

    static function formatPhone($input="", $type="3-3-4")
    {
        global $CMS;

        /*
        ** 1: US
        ** 2: VN
        **
        */

        if($type)
        {
            $num = explode("-", $type);
            krsort($num);
            $pos = 0;
            $numc = [];
            foreach ($num as $value) 
            {
                $numc[] = "-".substr($input, -($value + $pos), $value);
                $pos += $value;
            }
            krsort($numc);
            $str = implode("", $numc);
            $remain_str = substr($input, 0, strlen($input)-$pos);
            $new_str = ltrim($remain_str.$str,"-");
            return $new_str;
        }else
        {
            return $input;
        }
    }

    static function convertHours($input='')
    {
        $input = strtoupper($input);
        $pos = strpos($input, "AM");
        if($pos === false) 
        {
            $pos2 = strpos($input, "PM");
            if($pos2 === false) 
            {
                $hours = explode(":", $input);
                return $hours[0] < 12 ? $input." AM" : $input." PM";
            }else
            {
                return $input;
            }
        } else {
            return $input;
        }
    }

    static function convertphone($number)
    {
        $number = str_replace("-", "", $number);
        $number = preg_replace("/[^0-9]/", "", $number);
        $number = preg_replace("/([0-9]{1})([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3-$4", $number);
        
        return $number;
    }

    static function checkEnableSms()
    {
        global $CMS;

        // Check on/off SMS
        if(!$CMS->vars['sms_enabled'])
        {
            // check nhap số điện thoại
            if(!self::trimPhone($CMS->vars['company_mobile']))
            {
                $_SESSION['msg'] .= isset($CMS->lang['booking_not_available_by_disable_sms']) ? "{$CMS->lang['booking_not_available_by_disable_sms']}" : '';
            }else
            {
                $phone = \models\book::convertphone($CMS->vars['company_mobile']);
                
                $_SESSION['msg'] .= "{$CMS->lang['booking_contact_phone_1']} <b>".$phone."</b> {$CMS->lang['booking_contact_phone_2']}";
            }
        }else
        {
            // check nhap số điện thoại
            if(!self::trimPhone($CMS->vars['company_mobile']))
            {
                $_SESSION['msg'] .= isset($CMS->lang['booking_not_available_by_disable_sms']) ? "{$CMS->lang['booking_not_available_by_disable_sms']}" : '';
            }else
            {
                // Return true when check Okie phone
                return true;
            }

        }
        

        // Check time
        $time_set = 5; // minute
        if(!isset($_SESSION["CTime"]))
        {
            $_SESSION["CTime"] = time()+(60*$time_set);
            $CMS->api->slack->sendMessage("[{$_SERVER['SERVER_NAME']}] This website doesn’t have SMS notification for Booking Service. Please contact support team to enable it!");
        }else{
            $timecurr = time();
            if($timecurr >= $_SESSION["CTime"])
            {
                $CMS->api->slack->sendMessage("[{$_SERVER['SERVER_NAME']}]. This website doesn’t have SMS notification for Booking Service. Please contact support team to enable it!");
                $_SESSION["CTime"] = $timecurr+(60*$time_set);
            }
        }
        
        header("location: /");
        exit;
    }

    static function getBtnBooking($input_date="", $type="")
    {
        global $CMS;

        $checkdate = date("{$CMS->vars['dateformat_php'][$CMS->vars['date_format']]}");

        // Time get
        $curr_a = date("a");
        $currHours = ($curr_a === "am" and intval(date("h")) == 12) ? Intval($CMS->vars['booking_before_hours']) : intval(date("h")) + Intval($CMS->vars['booking_before_hours']);
        $currMinutes = intval(date("i"));

        $time = $CMS->class->date->date2time($input_date);
        $num_date = date("N", $time);
        $defaultMorning = \models\book::getopenhours("morning", $num_date);
        $defaultAfternoon = \models\book::getopenhours("afternoon", $num_date);
        $htmlMorning = "";
        $htmlAfternoon = "";

        $checkmorning = false;
        $checkafternoon = false;
        $suffixMorning = "";
        $suffixAfternoon = "";
        $hoursTimeFormat = $CMS->vars['hours_time_format'];
        $bookLogin = $CMS->vars['is_login'] == 1 ? "open_booking" : "open_booking";// Tạm thời ko bắt đăng nhập

        if($hoursTimeFormat == 12)
        {
            $suffixMorning = " am";
            $suffixAfternoon = " pm"; 
        }

        if($input_date === $checkdate)
        {
            // Like Date
            $html_expired = " (Overdue scheduled)";
            $style_option = " style='color: #909090;' ";
            
            // Html morning
            for($x = 0;$x <count($defaultMorning); $x++)
            {
                $checktime = explode(":", $defaultMorning[$x]);
                if(Intval($checktime[0]) < $currHours)
                {
                    $htmlMorning .= $type == "html" ? '<li><span onclick="call_notify(\'Notification\', \'You can not booking in this time, please choose another.\')">'.$defaultMorning[$x].$suffixMorning.'</span></li>' : '<option value="'.$defaultMorning[$x].'" disabled'.$style_option.'>'.$defaultMorning[$x].$suffixMorning.$html_expired.'</option>';
                }else if(Intval($checktime[0]) == $currHours)
                {
                    if(Intval($checktime[1]) < $currMinutes)
                    {
                        $htmlMorning .= $type == "html" ? '<li><span onclick="call_notify(\'Notification\', \'You can not booking in this time, please choose another.\')">'.$defaultMorning[$x].$suffixMorning.'</span></li>' : '<option value="'.$defaultMorning[$x].'" disabled'.$style_option.'>'.$defaultMorning[$x].$suffixMorning.$html_expired.'</option>';
                    }else
                    {
                        $checkmorning = true;
                        $htmlMorning .= $type == "html" ? '<li><a href="#'.$bookLogin.'" valhours="'.$defaultMorning[$x].'" class="'.$bookLogin.'">'.$defaultMorning[$x].$suffixMorning.'</a></li>' : '<option value="'.$defaultMorning[$x].'">'.$defaultMorning[$x].$suffixMorning.'</option>';
                    }
                }else
                {
                    $checkmorning = true;
                    $htmlMorning .= $type == "html" ? '<li><a href="#'.$bookLogin.'" valhours="'.$defaultMorning[$x].'" class="'.$bookLogin.'">'.$defaultMorning[$x].$suffixMorning.'</a></li>' : '<option value="'.$defaultMorning[$x].'">'.$defaultMorning[$x].$suffixMorning.'</option>';
                }
            }

            // Html Afternoon
            for($x = 0;$x <count($defaultAfternoon); $x++)
            {
                $checktime = explode(":", $defaultAfternoon[$x]);
                $timeShow = $hoursTimeFormat == 12 ? ($checktime[0]>12 ? (($checktime[0]%12).":". $checktime[1] . $suffixAfternoon) : ($checktime[0].":".$checktime[1] . $suffixAfternoon)) : $defaultAfternoon[$x];
                if(Intval($checktime[0]) < $currHours)
                {
                    $htmlAfternoon .= $type == "html" ? '<li><span onclick="call_notify(\'Notification\', \'You can not booking in this time, please choose another.\')">'.$timeShow.'</span></li>' : '<option value="'.$defaultAfternoon[$x].'" disabled'.$style_option.'>'.$timeShow.$html_expired.'</option>';
                }else if(Intval($checktime[0]) == $currHours)
                {
                    if(Intval($checktime[1]) < $currMinutes)
                    {
                        $htmlAfternoon .= $type == "html" ? '<li><span onclick="call_notify(\'Notification\', \'You can not booking in this time, please choose another.\')">'.$timeShow.'</span></li>' : '<option value="'.$defaultAfternoon[$x].'" disabled'.$style_option.'>'.$timeShow.$html_expired.'</option>';
                    }else
                    {
                        $checkafternoon = true;
                        $htmlAfternoon .= $type == "html" ? '<li><a href="#'.$bookLogin.'" valhours="'.$defaultAfternoon[$x].'" class="'.$bookLogin.'">'.$timeShow.'</a></li>' : '<option value="'.$defaultAfternoon[$x].'">'.$timeShow.'</option>';
                    }
                }else
                {
                    $checkafternoon = true;
                    $htmlAfternoon .= $type == "html" ? '<li><a href="#'.$bookLogin.'" valhours="'.$defaultAfternoon[$x].'" class="'.$bookLogin.'">'.$timeShow.'</a></li>' : '<option value="'.$defaultAfternoon[$x].'">'.$timeShow.'</option>';
                }
            }
        }else
        {

            // Different Date
            // Html morning
            for($x = 0;$x <count($defaultMorning); $x++)
            {
                $htmlMorning .= $type == "html" ? '<li><a href="#'.$bookLogin.'" valhours="'.$defaultMorning[$x].'" class="'.$bookLogin.'">'.$defaultMorning[$x].$suffixMorning.'</a></li>' : '<option value="'.$defaultMorning[$x].'">'.$defaultMorning[$x].$suffixMorning.'</option>';
            }

            // Html Afternoon
            for($x = 0;$x <count($defaultAfternoon); $x++)
            {
                $checktime = explode(":", $defaultAfternoon[$x]);
                $timeShow = $hoursTimeFormat == 12 ? ($checktime[0]>12 ? (($checktime[0]%12).":". $checktime[1] . $suffixAfternoon) : ($checktime[0].":". $checktime[1] . $suffixAfternoon)) : $defaultAfternoon[$x];
                $htmlAfternoon .= $type == "html" ? '<li><a href="#'.$bookLogin.'" valhours="'.$defaultAfternoon[$x].'" class="'.$bookLogin.'">'.$timeShow.'</a></li>' : '<option value="'.$defaultAfternoon[$x].'">'.$timeShow.'</option>';
            }

            $checkmorning = true;
            $checkafternoon = true;
        }
    
        return array($htmlMorning, $htmlAfternoon, $checkmorning, $checkafternoon);
    }

}