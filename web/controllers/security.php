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

class security
{
    static public function auto_run()
    {
        global $tpl;

        // Switch
        switch ( ezy::$act )
        {
            case "create":
                self::create();
                break;
           
            default;
        }
    }


    static function create()
    {
    	$token = \lib\security::create_token();
    	print $token; exit;
    }
}