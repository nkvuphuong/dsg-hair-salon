<?php
/**
 * Created by PhpStorm.
 * User: HLoi
 * Date: 4/4/2017
 * Time: 10:42 AM
 */

namespace controller;

use core\ezy;
use models\product;
use models\store;
use \views\layouts;

ezy::load_model("store");

class contact
{
    static public function auto_run()
    {
        global $tpl;
        
    	switch ( ezy::$act )
        {
            case "send":
                self::send_contact();
                break;
            default:
            	self::page_default();
                break;
        }
    }

    /**
     * Send contact
     */

    static private function send_contact()
    {
        global $tpl;
        
        $msg = \models\contact::sendContact();
        
        if($msg['status'] == "error")
        {
           $_SESSION['error_msg'] = $msg['message'];
        }
        else
        {
            $_SESSION['msg'] = $msg['message'];
        }
        
    	header("location: /");
        exit;
    }

    /**
     * Default contact form
     */

    static private function page_default()
    {
    	global $tpl, $CMS;

        // Move to application model, use for board
    	// $tpl->storefronts = store::getAll();

        // Build contact form
    	echo ezy::html();
    }
}