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
    static public $migrateFolder = "db/migrate/";
    static public $migratedFiles = "migrated_files.txt";
    static public $lockMigratedFiles = "migrated_lock.txt";
    static public $versionFile = "migrate_version.txt";
    static public $result = true;
    static public $msg;
    static public $output;

    /**
     * Init migration
     */

    static public function init()
    {
        global $CMS;

        //Create folder db
        $CMS->class->image->check_folder_img('db','',0,'');

        self::$versionFile = root_path."db/migrate/".self::$versionFile;

        // Set it into uploads
        $oldMigratedFiles = root_path."db/".self::$migratedFiles;
        $oldLockMigratedFiles = root_path."db/".self::$lockMigratedFiles;

        //New version
        self::$migratedFiles = $CMS->vars['upload_dir'].'/db/'.self::$migratedFiles;
        self::$lockMigratedFiles =$CMS->vars['upload_dir'].'/db/'.self::$lockMigratedFiles;

        //Sync for Old version
        if(is_file($oldMigratedFiles) && file_exists($oldMigratedFiles))
        {
            //Move to upload dir
            rename($oldMigratedFiles, self::$migratedFiles);
        }

        if(is_file($oldLockMigratedFiles) && file_exists($oldLockMigratedFiles))
        {
            //Move to upload dir
            rename($oldLockMigratedFiles, self::$lockMigratedFiles);
        }

        //Create file migrate
        if(!is_file(self::$migratedFiles) || !file_exists(self::$migratedFiles))
        {
            fopen(self::$migratedFiles, "w") or die("Can't create migrated files!");
        }

        // Set MySQL Error into silent mode
        db::$notify_silent = true;
        db::$notify_slack  = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', "::1", "103.3.244.23"]) ? false : true ;

        //Check is localhost
//        if(!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', "::1"]))
        {
            /*//Nếu có file lock thì stop
            if(is_file(self::$lockMigratedFiles) && file_exists(self::$lockMigratedFiles))
            {
                return false;
            }*/

            //Check version (07-08-2018)
            $currentVersion = file_get_contents(self::$versionFile);
            $lockVersion = file_get_contents(self::$lockMigratedFiles);

            if($currentVersion == $lockVersion) {
                return false;
            }
        }

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
        global $DB, $CMS;

        $migratedFilesData = (!file_exists(self::$migratedFiles) || !is_file(self::$migratedFiles)) ? [] : explode(PHP_EOL, file_get_contents(self::$migratedFiles));

        /**
         * Để đồng bộ với quy trình cũa thì lần chạy đầu tiên lấy hết record trong DB lưu vào file
         */
        if(empty($migratedFilesData))
        {
            //Initial sync from database
            $sql = "SELECT DISTINCT migrate_name FROM ".root_table."migration ORDER BY migrate_id";

            $sql = $DB->query($sql);

            while ($rs = $sql->fetch_assoc())
            {
                $migratedFilesData[] = $rs['migrate_name'];
            }

            //Write to file
            file_put_contents(self::$migratedFiles, implode(PHP_EOL, $migratedFilesData));
        }

        // Scan all files in migrated folder
        $migrateTree = array_diff(input::scandir(root_path.self::$migrateFolder, "txt"), ['migrate_version.txt']) ;

        //Lấy danh sách file chưa chạy migrate
        $diffFiles = array_diff($migrateTree, $migratedFilesData);

        if(empty($diffFiles))
        {
            //Tạo file lock đê đánh dấu đã migrate hết file. Khi deploy thì xóa file này đi để migrate có thể hoạt động lại.
//            file_put_contents(self::$lockMigratedFiles, date('Y-m-d H:i:s'));

            //Tạo file lock và set version file lock = nội dung migrate_version.txt để stop migrate (07-08-2018)
            file_put_contents(self::$lockMigratedFiles, file_get_contents(self::$versionFile));

            self::$msg = "Nothing to do";
            return true;
        }

        // Filter to get which files are not migrate yet and must be have .txt extend
        foreach ($diffFiles as $file )
        {
            //check valid file name yyyymmdd_hhmm_description.txt
            if (preg_match('/\.txt$/', $file) && !preg_match('/^[0-9]{8}_[0-9]{4}_[A-Za-z0-9_]+.txt$/', $file)) {
                self::msg(false,"File: {$file} invalid. Please remove or change name file to format yyyymmdd_hhmm_description.txt");
                return false;
            }
            else
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

                    $DB->query($query);
                    // Logs
                    self::$msg .= $query.PHP_EOL."<br />";
                }

                $migratedFilesData[] = $file;

                file_put_contents(self::$migratedFiles, implode(PHP_EOL, $migratedFilesData));
            }
        }

        //clear cache
        $CMS->class->cache->mdelete('');

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