<?php
/**
 * Created by PhpStorm.
 * User: HLoi
 * Date: 4/4/2017
 * Time: 10:42 AM
 */

namespace controller;

use core\ezy;
use models\tags;

ezy::load_model("tags");

class video
{
    static public function auto_run()
    {
        global $CMS, $tpl;

        switch ( ezy::$act )
        {
            case "search":
                self::search();
                break;
            case "category":
                self::category();
                break;
            case "detail":
                self::detail();
                break;
            case "tags":
                self::tags();
                break;
            default:
                self::main();
                break;
        }
    }

    /**
     * Default page
     */

    static private function main()
    {
        global $CMS, $tpl;

        echo ezy::html();
    }

    /**
     * Load video via category
     */

    static private function category()
    {
        global $tpl;

        echo ezy::html();
    }

    /**
     * Get detail
     */

    static private function detail()
    {
        global $tpl;

        echo ezy::html("detail");
    }

    /**
     * Search
     */

    static private function search()
    {
        global $CMS, $tpl;

        echo ezy::html();
    }

    /**
     * Tags
     */

    static private function tags()
    {
        global $CMS, $tpl;

        echo ezy::html();
    }
}