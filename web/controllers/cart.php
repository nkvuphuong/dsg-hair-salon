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

// Load models
ezy::load_model("cart");

class cart
{
    static public function auto_run()
    {
        global $tpl, $CMS;
        //Load language
        $CMS->class->language->load("cart");

        switch ( ezy::$act ) 
        {
            case 'delitem':
                self::delItem();
                break;
        	case 'update':
        		self::updateCart();
        		break;
            case 'updateprice':
                self::updatePrice();
                break;
        	case 'addcart':
        		self::addCart();
        		break;
            case 'change_product':
                self::changeProduct();
                break;    
        	default:
        		self::page_default();
        		break;
        }

        echo ezy::html();
    }

    static function page_default()
    {
    	global $tpl, $CMS;

        // Check session
        if(!isset($_SESSION['mycart']))
        {
            $_SESSION['mycart'] = [];
        }
// unset($_SESSION['mycart']);
        // set mycart
        $tpl->mycart = $_SESSION['mycart'];
        // get cartTotal
        $tpl->amount = \models\cart::cartTotal();
 
    }

    static function addCart()
    {
        global $CMS, $tpl;
        // input
        $product_id = intval(ezy::$input[0]);
        // set session
        \models\cart::addCart($product_id);
        // redirect
        header("location: /cart");
        exit;
    }

    static function updateCart()
    {
        global $CMS, $tpl;
        //Input
        $product_id = intval($CMS->input['id']);
        $quantity = intval($CMS->input['quantity']);
        // set session
        list($total_show, $amount, $cart_data) = \models\cart::updateCart($quantity, $product_id);
        // Return
        print json_encode(array('total_show' => $total_show, 'amount' => $amount, 'cart_data' => $cart_data), JSON_UNESCAPED_UNICODE);exit;
    }

    static function delItem()
    {
        global $CMS, $tpl;
        //Input
        $product_id = intval($CMS->input['id']);
        // set session
        $data = \models\cart::delItem($product_id);

        // Return
        print json_encode(['amount' => $data[3], 'cart_data' => $data], JSON_UNESCAPED_UNICODE);exit;
    }

    static function updatePrice()
    {
        global $CMS, $tpl;
        //Input
        $product_id = intval($CMS->input['id']);
        $cus_price = $CMS->input['cus_price'];
        // set session
        $return = \models\cart::updatePrice($cus_price, $product_id);
        // Return
        print json_encode($return, JSON_UNESCAPED_UNICODE);exit;
    }

    static function changeProduct()
    {
        global $CMS, $tpl;
        
        // set session
        $data = \models\cart::changeProduct();
        // Return
        print json_encode($data, JSON_UNESCAPED_UNICODE);exit;
    }
}