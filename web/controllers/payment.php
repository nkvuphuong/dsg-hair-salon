<?php
/**
 * Created by PhpStorm.
 * User: HLoi
 * Date: 4/4/2017
 * Time: 10:42 AM
 */

namespace controller;

use core\ezy;
use lib\input;
use models\app;
use models\cart;
use models\discount;

//Load model
ezy::load_model("discount");

class payment
{
    static public $urlInfo=[];

    static public function auto_run()
    {
        global $tpl, $CMS;

        $tpl->discount_code_info = isset($_SESSION['discount_code']) && $_SESSION['discount_code'] ? '' : 'display:none';
        $tpl->discount_code_input = isset($_SESSION['discount_code']) && $_SESSION['discount_code'] ? 'display:none' : '';

        //Load language
        $CMS->class->language->load("payment");

        // Check active paypal
        $CMS->vars['payment_active'] = isset($CMS->vars['payment_active']) ? intval($CMS->vars['payment_active']) : 0;

        // Check redirect paypal
        self::$urlInfo = $_GET;
        if(count(self::$urlInfo) > 0)
        {
            $list_action = explode("?", ezy::$act);
            if(in_array($list_action[0], array('checksuccess', 'cancel')))
            {
                ezy::$act = $list_action[0];
            }
        }

        // Controller
        switch ( ezy::$act ) 
        {
            case 'getdistrict':
                self::getdistrict();
                break;
            case 'checksuccess';
                self::checkSuccess();
                break;
        	case 'success':
        		self::pageSuccess();
        		break;
        	case 'cancel':
        		self::pageCancel();
        		break;
            case "checkout":
                self::checkOut();
                break;
            case "discount_code":
                self::discount_code();
                break;
            case "discount_code_dsg":
                self::discount_code_dsg();
            break;
            case "remove_code":
                self::remove_discount_code();
                break;
        	default:
        		self::page_default();
        		break;
        }

        echo ezy::html();
    }

    /**
     * Default page
     */

    static private function page_default()
    {
    	global $CMS, $tpl;

        // Check session mycart
        if(empty($_SESSION['mycart']))
        {
            // set message error
            $_SESSION['error_msg'] = $CMS->lang['payment_cart_empty'];
            // redirect
            header("location: /");
        }

        if(defined("is_web_vn") == true)
        {
            self::default_page_vn();
        }elseif(defined("is_web_us") == true)
        {
            self::default_page_us();
        }
    }

    static function default_page_vn()
    {
        global $CMS, $tpl;

        $ship = isset($_SESSION['shipping']) ? $_SESSION['shipping'] : null;
        $mem = isset($_SESSION['member']) ? $_SESSION['member'] : null;

        // Set default country
        $default_country = $CMS->vars['default_language']=="en" ? "US" : "VN";

        // Set mycart
        $tpl->mycart = isset($_SESSION['mycart']) ? $_SESSION['mycart'] : null;

        // Set Amount
        // list($tpl->ship_fee, $tpl->vat, $tpl->subtotal, $tpl->amount) = cart::cartTotal(1);

        // Set shipping
        $tpl->shipping = isset($_SESSION['shipping']) ? $_SESSION['shipping'] : null;
        $tpl->shipping['same_info'] = isset($tpl->shipping['same_info']) ? $tpl->shipping['same_info'] : 1;
// print "<pre>";print_r($_SESSION['shipping'] );exit;
        // get Option city
        $tpl->optionCity = \models\payment::getOptionCity();
    }

    static function default_page_us()
    {
        global $CMS, $tpl;

        $ship = isset($_SESSION['shipping']) ? $_SESSION['shipping'] : null;
        $mem = isset($_SESSION['member']) ? $_SESSION['member'] : null;

        // Set default country
        $default_country = $CMS->vars['default_language']=="en" ? "US" : "VN";

        // Make shipping session
        $_SESSION['shipping'] = array(
            'ship_email' => isset($ship['ship_email']) ? $ship['ship_email'] : $mem['cus_email'],
            'ship_phone' => isset($ship['ship_phone']) ? $ship['ship_phone'] : $mem['cus_phone'],
            'ship_full_name' => isset($ship['ship_full_name']) ? $ship['ship_full_name'] : $mem['cus_full_name'], 
            'ship_address1' => isset($ship['ship_address1']) ? $ship['ship_address1'] : $mem['cus_address'],
            'ship_address2' => isset($ship['ship_address2']) ? $ship['ship_address2'] : $mem['cus_address2'],
            'ship_city' => isset($ship['ship_city']) ? $ship['ship_city'] : $mem['cus_city'],
            'ship_state' => isset($ship['ship_state']) ? $ship['ship_state'] : $mem['cus_district'],
            'ship_postal_code' => isset($ship['ship_postal_code']) ? $ship['ship_postal_code'] : $mem['cus_postalcode'],
            'ship_country' => isset($ship['ship_country']) ? $ship['ship_country'] : ($mem['cus_country'] ? $mem['cus_country'] : $default_country)
            );

        // Set mycart
        $tpl->mycart = $_SESSION['mycart'];

        // Set Amount
        list($tpl->ship_fee, $tpl->vat, $tpl->subtotal, $tpl->amount, $tpl->vatori) = cart::cartTotal(1);

        // Set shipping
        $tpl->shipping = $_SESSION['shipping'];

        // get Option city
        $tpl->optionCountry = \models\payment::getOptionCountry();
    }

    /**
     * Do checkout
     */

    static private function checkOut()
    {
        global $CMS, $tpl;

        if(defined("is_web_vn") == true)
        {
            $redirect_link = \models\payment::checkOutVn();
            if(!$redirect_link) 
            {
                // Set shipping
                $tpl->shipping = $_SESSION['shipping'];
                // print "<pre>";print_r($tpl->shipping);exit;
                // get Option city
                $tpl->optionCity = \models\payment::getOptionCity();
                echo ezy::html();
                exit;
            }
        }elseif(defined("is_web_us") == true)
        {
            // Checkout
            $redirect_link = \models\payment::checkOut();
            if(!$redirect_link) 
            {
                if(isset($_SESSION['curr_info']['type_page']) == 1)
                {
                    $_SESSION['curr_info']['input'] = $CMS->input;
                    header("location: /giftcards");exit;
                }else
                {
                    // Set shipping
                    $tpl->shipping = $CMS->input;
                    echo ezy::html();
                    exit;
                }
            }

        }

        // Fix link redirect when error form
        $redirect_link = (isset($_SESSION['curr_info']['type_page']) == 1 and $redirect_link == 1) ? "/giftcards" : $redirect_link;
        // redirect
        header("location: {$redirect_link}");

        
    }

    /**
     * Check return data
     */

    static private function checkSuccess()
    {
        global $CMS, $tpl;
        
        if(defined("is_web_vn") == true)
        {
            // Check current transaction
            $check = \models\payment::checkCurrentTransactionVn(self::$urlInfo);
        }else
        {
            // Check current transaction
            $check = \models\payment::checkCurrentTransaction(self::$urlInfo);
        }

        if(!$check) 
        { 
            $redirect = isset($_SESSION['curr_info']['type_page']) == 1 ? "/giftcards" : "/payment";
            header("location: {$redirect}");exit;

        }else
        {
            header("location: /payment/success");exit;
        }

    }

    /**
     * Return success
     */

    static private function pageSuccess()
    {
        /* destroy var mycart */
        // unset session
        unset($_SESSION['order']);
        unset($_SESSION['total_tax']);
        unset($_SESSION['shipping']);
        unset($_SESSION['mycart']);
        unset($_SESSION['paypal']);
        unset($_SESSION['curr_info']);

        echo ezy::html('success'); exit;
    }

    /**
     * Return cancel
     */

    static private function pageCancel()
    {
        /* destroy var mycart */
        // unset session
        unset($_SESSION['order']);
        unset($_SESSION['total_tax']);
        unset($_SESSION['shipping']);
        unset($_SESSION['mycart']);
        unset($_SESSION['paypal']);
        unset($_SESSION['curr_info']);

        echo ezy::html('cancel'); exit;
    }

    static function getdistrict()
    {
        global $CMS;

        $city_id = isset($CMS->input['city_id']) ? intval($CMS->input['city_id']) : 0;
        $data = \models\payment::getOptionDistrict($city_id);

        print json_encode($data, JSON_UNESCAPED_UNICODE); exit;
    }

    static function discount_code()
    {
        global $CMS, $member;

        input::jsonEncode(discount::checkCode($CMS->input['code']));
    }


    static function discount_code_dsg()
    {
        global $CMS, $member;
        // $booking_service =  $CMS->input['product_id'];
        // $i = 0;
        // foreach ($booking_service as $product_id) 
        // {
        //     if(!empty($product_id))
        //     {
        //         $i++;
        //     }
        // }

        // $discount_code = $CMS->input['discount_code'];
        // echo $i;exit;
        input::jsonEncode(discount::checkCode_dsg($CMS->input['discount_code']));
    }


    static function remove_discount_code()
    {
        global $CMS, $member;

        if(isset($_SESSION['discount_code']))
        {
            unset($_SESSION['discount_code']);

            $return = [
                'status' => 'ok',
                'msg' => 'Removed code',
                'cart_data' => \models\cart::cartTotal(1),
            ];
        }
        else
        {
            $return = [
                'status' => 'fail',
                'msg' => 'Have no code',
            ];
        }

        input::jsonEncode($return);
    }

}