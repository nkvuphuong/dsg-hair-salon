<?php

namespace models;

use core\ezy;
use lib\input;

ezy::load_model("product");

class alive
{
    /**
     * Check Database connection
     * @return mixed
     */

    static public function database()
    {
        global $DB;

        return $DB->query("SELECT version()");
    }

    static public function output($output)
    {
        return empty($output) ? "alive:ok" : "alive:failed<br />".$output;
    }

    /**
     * Check MySQL Version
     * @param int $type
     * @return bool
     */

    static function checkMySQL( $type = 0 )
    {
        global $CMS, $DB;
        $DB->query("SELECT VERSION() as version");
        $version = $DB->fetch_array()['version'];
        if(!preg_match("/10.2/", $version))
        {
            if ( ! $type ){
                $_SESSION['error_msg'] = "Please upgrade to mysql MariaDB 10.2 or higher to use this function";
            }
            return false;
        }
        return $version;
    }

    /**
     *  Check valid web theme
     *  If the theme exist in themes/xxx/ return true
     */

    static public function checkValidTheme()
    {
        if ( ezy::$web_theme )
        {
            return file_exists(root_path. "/themes/".ezy::$web_theme );
        }

        return true;
    }

    /**
     * Load cron from: /cron/***.php
     */

    static public function loadCron($cron_name = "")
    {
        global $CMS, $DB;

        // If empty
        if ( empty($cron_name) )
        {
            return false;
        }

        // Put cronjob email below
        $path = root_path."/cron/{$cron_name}.php";

        if ( file_exists($path) )
        {
            ob_start();
            include($path);
            $log = ob_get_clean();
        }

        return $log;
    }

    /**
     * Update timezone if not existed
     */
    static public function updateTimezone()
    {
        global $CMS;

        // $CMS->config_general->updateTimezoneId();

        return true;
    }
}