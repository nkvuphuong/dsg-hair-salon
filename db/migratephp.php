<?php

namespace lib;

use \lib\db;
use \lib\input;
use \core\ezy;
use Symfony\Component\Config\Definition\Exception\Exception;

//Start
// Init
if ( ! defined("is_init") )
{
    //define("in_app", true); // Load init only
    require_once '../init.cron.php';
}

class migrationphp
{
    static private $migrateFolder = "db/migratephp/";
    static private $migrateLast = "dbphp.txt";
    static public $result = true;
    static public $msg;
    static public $output;

    /**
     * Init migration
     */

    static public function init()
    {
        global $CMS;

        // Set it into uploads
        self::$migrateLast = "{$CMS->vars['upload_dir']}/".self::$migrateLast;

        // Set MySQL Error into silent mode
        db::$notify_silent = true;
        db::$notify_slack = false;

        // Load cache
        $migrate_inprogress = $CMS->class->cache->loadsql("migratephp_inprogress");

        // Continue
        if ( $migrate_inprogress > 0 AND $migrate_inprogress > (time() - 60 ) AND in_array(strtolower($CMS->vars['ezy_env']), array("development", "production")) )
        {
             //self::msg(false,"PHP Migration is being loaded");
        }
        else {
            // Update cache
            $CMS->class->cache->savesql("migratephp_inprogress", time());

            // Success
            if ( $result = self::auto_run() == true )
            {
                // Update cache
                $CMS->class->cache->savesql("migratephp_inprogress", 0);

                // Return success message
                self::msg(true, self::$msg);
            }
            // Failed
            else
            {
                self::msg(false, self::$msg.$result);
            }
        }
    }

    /**
     * Return message
     */

    static private function msg($result, $msg)
    {
        global $CMS;

        self::$output = json_encode(array("status" => $result, "msg" => $msg));

        // If erorr
        if ( $result == false )
        {
            exit($msg);
        }

        return self::$output;
    }

    /**
     * Auto run migrate
     * @return bool|string
     */

    static private function auto_run()
    {
        global $DB;

        //Files has migrated
        //$migratedFiles = file_get_contents(self::$migrateDb);
        //$migratedFiles = explode(PHP_EOL, $migratedFiles);
        //define('MIGRATED_FILES', serialize($migratedFiles));

        // Last migrated file
        $migrateLastDb = $DB->fetch("SELECT migrate_name AS name FROM ".root_table."migrationphp ORDER BY migrate_id DESC LIMIT 1", "name");

        // Compare
        if ( self::$migrateLast == $migrateLastDb )
        {
            self::$msg = "Nothing to do";
            return true;
        }

        // Scan all files in migrated folder
        $migrateTree = input::scandir(root_path.self::$migrateFolder, "php");

        // Migration data
        $migrateData = [];

        // Filter to get which files are not migrate yet and must be have .txt extend
        foreach ($migrateTree as $file ) {

            //check valid file name yyyymmdd_hhmm_description.txt
            if (preg_match('/\.php/', $file) && !preg_match('/^[0-9]{8}_[0-9]{4}_[A-Za-z0-9_]+.php$/', $file)) {
                self::msg(false,"File: {$file} invalid. Please remove or change name file to format yyyymmdd_hhmm_description.php");
                return false;
            }

            // Start insert migrate data
            if ( ! $migrateLastDb OR intval(explode("_", $file)[0]) > intval(explode("_", $migrateLastDb)[0]) OR (intval(explode("_", $file)[0]) == intval(explode("_", $migrateLastDb)[0]) && intval(explode("_", $file)[1]) > intval(explode("_", $migrateLastDb)[1])))
            {
                $migrateData[] = $file;
            }
        }

        // If nothing
        if ( count($migrateData) == 0 ) {
            self::$msg = "Nothing to do";
            return true;
        }

        // Do query...
        foreach ($migrateData as $file)
        {
            // Do include
            try
            {
                $phpfile = root_path.self::$migrateFolder.$file;

                // Logs
                if ( file_exists($phpfile) )
                {
                    include($phpfile);

                    // Insert migration
                    $DB->query("INSERT INTO ".root_table."migrationphp (migrate_name, migrate_time) VALUES ('{$file}', '".time()."');");

                    // Update last query
                    file_put_contents(self::$migrateLast, $file);
                }
                // Error when loading file.
                else{
                    echo "failed";
                    // Failed
                    self::$msg .= "Migration error: failed to load {$file}";
                    self::$result = false;
                    return false;
                }
            }
            // If any errors appear
            catch ( Exception $e )
            {
                // Failed
                self::$msg .= "Migration error: {$file}".PHP_EOL.$e->getMessage();
                self::$result = false;
                return false;
            }
        }

        self::$msg .= "PHP Migration successful";

        return true;
    }

    /**
     * Auto clean
     * @return string
     */

    static private function auto_clean()
    {
        // Check exist
        if (!file_exists('migratephp.php')) {
            self::msg(false, "Cannot find file migratephp");
            return false;
        }

        // Success
        if ( self::auto_run() == true )
        {
            // Remove migration
            if (@unlink('migratephp.php')){
                self::msg(true,'PHP Migrate success');
            }
            else {
                self::msg(false,'PHP Migrate Failed');
            }
        }
        // Failed
        else {
            self::msg(false, self::$msg);
        }

        return true;
    }
}

\lib\migrationphp::init();