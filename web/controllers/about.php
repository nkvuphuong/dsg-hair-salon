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

class about
{
    static public function auto_run()
    {
        global $tpl;

        $tpl->html = app::getPage("about");

        echo ezy::html();
    }
}