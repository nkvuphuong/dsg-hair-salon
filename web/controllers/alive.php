<?php
/**
 * Created by PhpStorm.
 * User: HLoi
 * Date: 4/4/2017
 * Time: 10:42 AM
 */

namespace controller;

use core\ezy;
use lib\migration;
use models\app;

class alive
{
    /**
     * Check alive
     */

    static private function alive()
    {
        // Added by LHL, 29/09/2017
        \models\alive::database() == true ? "" : die("Database connection failed");
        \models\app::auto_run() == true ? "" : die("Application load failed");
        \models\alive::checkValidTheme() == true ? "" : die("Theme (Web) load failed");
        \models\alive::updateTimezone() == true ? "" : die("Update timezone load failed");

        // Added by LHL, 03/10/2017
        \lib\migration::$result == true ? "" : die("DB Migration failed");
        \lib\migrationphp::$result == true ? "" : die("PHP Migration failed");
    }

    /**
     * Manual Cronjob
     */

    static private function cronjob()
    {
        // Added by LHL, 29/09/2017
        \models\alive::loadCron("email") == true ? "" : die("Load cron Email failed");
        \models\alive::loadCron("sms") == true ? "" : die("Load cron SMS failed");
    }

    /**
     * Auto run
     * - Check alive
     * - Check migration (PHP)
     */

    static public function auto_run()
    {
        // Get data in touch
        ob_start();

        // Run cronjob
        self::cronjob();

        // Check Alive
        self::alive();

        // Get output
        $output = ob_get_clean();

        // Retrun result
        echo \models\alive::output($output);

        exit;
    }

}