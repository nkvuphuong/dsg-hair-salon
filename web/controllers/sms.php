<?php
/**
 * Created by PhpStorm.
 * User: HLoi
 * Date: 4/4/2017
 * Time: 10:42 AM
 */

namespace controller;

use core\ezy;
use models\gallery;

ezy::load_model("gallery");

class sms
{
    static public function auto_run()
    {
    	global $tpl;
    	
    	\api\sms::send();
    }
}