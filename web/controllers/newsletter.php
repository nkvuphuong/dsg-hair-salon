<?php
/**
 * Created by PhpStorm.
 * User: HLoi
 * Date: 4/4/2017
 * Time: 10:42 AM
 */

namespace controller;

use core\ezy;

class newsletter
{
    static public function auto_run()
    {
        global $tpl;

        switch ( ezy::$act )
        {
        	case "add":
                self::add_newsletter();
                break;
            default:
                echo ezy::html();
                break;
        }
        
    }

    static private function add_newsletter()
    {
    	$check = \models\newsletter::addNewsletter();
    	print json_encode($check);exit;
    }
}