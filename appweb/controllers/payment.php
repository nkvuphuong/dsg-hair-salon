<?php
/**
 * Created by PhpStorm.
 * User: HLoi
 * Date: 4/4/2017
 * Time: 10:42 AM
 */

namespace controller;

use core\ezy;
use models\app;
use models\cart;

//Load model
ezy::load_model("order");

class payment
{
    static public $urlInfo=[];

    static public function auto_run()
    {
        global $tpl, $CMS;

        //Load language
        $CMS->class->language->load("payment");

        // SEO
        $CMS->lang['website_title'] = 'Thiết kế website bán hàng - doanh nghiệp - bất động sản giá rẻ';
        $CMS->lang['seo_description'] = 'Web4s - Dịch vụ thiết kế website bán hàng, website doanh nghiệp, website bất động sản giá rẻ, đạt chuẩn SEO, tích hợp quản lý sản phẩm, thanh toán online';
        $CMS->lang['seo_keyword'] = 'thiết kế website, website bán hàng, website doanh nghiệp, website bất động sản, website giá rẻ, seo website, website trọn gói';
        $CMS->lang['seo_author'] = 'Công ty TNHH phần mềm Nhân Hòa';
        
        $CMS->vars['website_title'] = $CMS->lang['website_title'];
        $CMS->vars['seo_description'] = $CMS->lang['seo_description'];
        $CMS->vars['seo_keyword'] = $CMS->lang['seo_keyword'];
        $CMS->vars['seo_author'] = $CMS->lang['seo_author'];

        $CMS->vars['og_title'] = $CMS->lang['website_title'];
        $CMS->vars['og_description'] = $CMS->lang['seo_description'];

        $CMS->vars['dc_title'] = $CMS->lang['website_title'];
        $CMS->vars['dc_description'] = $CMS->lang['seo_description'];
        $CMS->vars['dc_subject'] = $CMS->lang['website_title'];
        // END SEO

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
//http://eco.lo/payment/checksuccess?transaction_info=Thong+tin+giao+dich&order_code=NL_1501054082&price=2000&payment_id=30021052&payment_type=1&error_text=&secure_code=977a0306891823513ce956152a05e3e1&token_nl=10690459-6ec00e7c87636ce7feacbc51d0b5c776
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
        if(!$_SESSION['mycart'] or count($_SESSION['mycart']) == 0)
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

        $ship = $_SESSION['shipping'];
        $mem = $_SESSION['member'];

        // Set default country
        $default_country = $CMS->vars['default_language']=="en" ? "US" : "VN";

        // Set mycart
        $tpl->mycart = $_SESSION['mycart'];

        // Set Amount
        // list($tpl->ship_fee, $tpl->vat, $tpl->subtotal, $tpl->amount) = cart::cartTotal(1);

        // Set shipping
        $tpl->shipping = $_SESSION['shipping'];
        $tpl->shipping['same_info'] = isset($tpl->shipping['same_info']) ? $tpl->shipping['same_info'] : 1;
// print "<pre>";print_r($_SESSION['shipping'] );exit;
        // get Option city
        $tpl->optionCity = \models\payment::getOptionCity();
    }

    static function default_page_us()
    {
        global $CMS, $tpl;

        $ship = $_SESSION['shipping'];
        $mem = $_SESSION['member'];

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
        list($tpl->ship_fee, $tpl->vat, $tpl->subtotal, $tpl->amount) = cart::cartTotal(1);

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
                // Set shipping
                $tpl->shipping = $CMS->input;
                echo ezy::html();
                exit;
            }

        }

        // redirect
        header("location: {$redirect_link}");

        
    }

    /**
     * Check return data
     */

    static private function checkSuccess()
    {
        global $CMS, $tpl;
 
 
        if(count(self::$urlInfo) > 0)
        {
            if(self::$urlInfo['payment_id'] != "")
            {
                // Success
                if(\models\order::create_order(self::$urlInfo) == true)
                {
                   header("location: {$CMS->vars['root_domain']}/register/webstep3");   
                }
                else
                {
                    header("location: {$CMS->vars['root_domain']}/register/webstep3");  
                }
            }
        }    

       
    }

    /**
     * Return success
     */

    static private function pageSuccess()
    {
        /* destruy var mycart */
        // unset session
        unset($_SESSION['order']);
        unset($_SESSION['shipping']);
        unset($_SESSION['mycart']);
        unset($_SESSION['paypal']);

        echo ezy::html('success'); exit;
    }

    /**
     * Return cancel
     */

    static private function pageCancel()
    {
        /* destruy var mycart */
        // unset session
        unset($_SESSION['order']);
        unset($_SESSION['cart']);
        unset($_SESSION['shipping']);
        unset($_SESSION['mycart']);
        unset($_SESSION['paypal']);
        echo ezy::html('cancelpayment');  
    }

    static function getdistrict()
    {
        global $CMS;

        $city_id = intval($CMS->input['city_id']);
        $data = \models\payment::getOptionDistrict($city_id);

        print json_encode($data, JSON_UNESCAPED_UNICODE); exit;
    }

}