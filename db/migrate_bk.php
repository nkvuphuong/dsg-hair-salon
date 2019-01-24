<?php

namespace lib;

use \lib\db;
use \lib\input;
use \core\ezy;
//Start
// Init
if ( ! defined("is_init") )
{
    //define("in_app", true); // Load init only
    require_once '../init.cron.php';
}

class migration
{
    static private $migrateFolder = "db/migrate/";
    static private $migrateLast = "db.txt";
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
        db::$notify_slack  = false;

        // Load cache
        $migrate_inprogress = $CMS->class->cache->loadsql("migrate_inprogress");

        // Continue
        if ( $migrate_inprogress > 0 AND $migrate_inprogress > (time() - 60 ) AND in_array(strtolower($CMS->vars['ezy_env']), array("development", "production")) )
        {
            // self::msg(false,"Migration is being loaded");
        }
        else {
            // Update cache
            $CMS->class->cache->savesql("migrate_inprogress", time());

            // Success
            if ( $result = self::auto_run() == true )
            {
                $CMS->class->cache->savesql("migrate_inprogress", 0);

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
        $migrateLastDb = $DB->fetch("SELECT migrate_name AS name FROM ".root_table."migration ORDER BY migrate_id DESC LIMIT 1", "name");

        // Compare
        if ( self::$migrateLast == $migrateLastDb )
        {
            self::$msg = "Nothing to do";
            return true;
        }

        // Scan all files in migrated folder
        $migrateTree = input::scandir(root_path.self::$migrateFolder, "txt");

        // Migration data
        $migrateData = [];

        // Filter to get which files are not migrate yet and must be have .txt extend
        foreach ($migrateTree as $file ) {

            //check valid file name yyyymmdd_hhmm_description.txt
            if (preg_match('/\.txt$/', $file) && !preg_match('/^[0-9]{8}_[0-9]{4}_[A-Za-z0-9_]+.txt$/', $file)) {
                self::msg(false,"File: {$file} invalid. Please remove or change name file to format yyyymmdd_hhmm_description.txt");
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
            // Get queries (Support multi queries, separate by newline \n)
            $sqldata = file_get_contents(root_path.self::$migrateFolder. $file);
            $sqldata = str_replace("\r\n", "\n", $sqldata);
            $sql = explode(";\n", $sqldata);

            // Do queries
            foreach ($sql as $query)
            {
                // Disable notify slack
                db::$notify_slack  = false;
                // Success
                if ( $DB->query($query) )
                {

                    // Logs
                    self::$msg .= $query.PHP_EOL."<br />";

                    // Insert migration
                    $DB->query("INSERT INTO ".root_table."migration (migrate_name, migrate_time) VALUES ('{$file}', '".time()."');");

                    // Update last query
                    file_put_contents(self::$migrateLast, $file);
                }
                // Failed
                else {

 
                    // If duplicate column, just passed for freedom
                    if ( preg_match("/(Duplicate column name|Query was empty|already exists|doesn't exist|Duplicate entry|key exists|Unknown column|Multiple primary key defined)/", db::$silent_msg) )
                    {
 
                        self::$msg .= "Passed SQL: {$file}".PHP_EOL.db::$silent_msg;
 
                        continue;
                    }
 
                    // Failed
                    self::$msg .= "SQL error: {$file}".PHP_EOL.db::$silent_msg;
                     
                    self::$result = false;
                    return false;
                }
            }
        }

        self::$msg .= "Migration success";

        return true;
    }

    /**
     * Auto clean
     * @return string
     */

    static private function auto_clean()
    {
        // Check exist
        if (!file_exists('migrate.php')) {
            self::msg(false, "Cannot find file migrate");
            return false;
        }

        // Success
        if ( self::auto_run() == true )
        {
            // Remove migration
            if (@unlink('migrate.php')){
                self::msg(true,'Migrate success');
            }
            else {
                self::msg(false,'Migrate Failed');
            }
        }
        // Failed
        else {
            self::msg(false, self::$msg);
        }

        return true;
    }
}

\lib\migration::init();