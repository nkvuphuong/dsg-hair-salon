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

class sitemap
{
    static public function auto_run()
    {
        global $CMS;

        header("Content-type: text/xml; charset=utf-8");

        if(is_file("{$CMS->vars['upload_dir']}/sitemap/{$CMS->vars['sitemap']}"))
        {
            $content = @file_get_contents("{$CMS->vars['upload_dir']}/sitemap/{$CMS->vars['sitemap']}");

            echo $content;
        }
        else
        {
            header("HTTP/1.0 404 Not Found");
        }
        exit;
    }
}