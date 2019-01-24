<?php

/**
 * Author: lyhuuloi
 * Date: 03/07/2017
 * Class: Optimize javascript, css files
 * Description: Based on Ruby on rails framework.
 * Usage:
  1. Data input:
echo <<<EOF
{$assets->generate(array('/templates/js/header.js',
'/templates/js/footer.js'), "js")}
EOF;

  2. $assets->env = "development"; // Will output:
     <script type='text/javascript' src='http://localhost/templates/js/header.js'></script>
     <script type='text/javascript' src='http://localhost/templates/js/footer.js'></script>

  3. $assets_env = "production"; // Will output:
    <script type='text/javascript' src='http://localhost/public/assets/acp/4574ef4ca9c7483c034263b0cf62ea67.js'></script>
    (I'll automatic generate file and store it in default assets's folder)
 *
 *
 */

namespace lib;

use core\ezy;
//use Cocur\BackgroundProcess\BackgroundProcess;

class assets
{
    static public $hostname = ""; // Use for CDN
    static public $environment = "development"; // development / production / test
    static private $assetpath = "/public/assets/"; // Modify if you want
    static private $srcpath = ""; // Resources path
    static private $baseurl = ""; // Domain
    static private $basepath = ""; // Path url
    static private $appname = ""; // Random name

    static private $syncname; // Auto generate a string (time) and include it to the $appname for ensure every change will not cache by browser
    static private $syncfile_exists = false; // Detect when the self::$syncname exists

    /**
     * @param $data: array(path1, path2, path3)
     * @param string $type: js/css
     * @return string: html tags for js/css
     *
     */

    static public function generate($data, $type = "js")
    {
        global $CMS;

        // Check for invalid input
        if ( is_array($data) == false )
        {
            return "<!-- Error while loading list -->";
        }

        // Check for cache
        $syncfile = self::$basepath.self::$assetpath."/sync.assets".ezy::$app_dir.".txt";
        $is_cached = 0;
        $output = "";

        // Check synced name
        if ( self::$environment == "production" )
        {
            // Detect if self::$syncfile was loaded
            self::$syncfile_exists = self::$syncfile_exists == false ? file_exists($syncfile) : self::$syncfile_exists;

            // Try to load self::$syncfile content
            self::$syncname = self::$syncfile_exists == true && empty(self::$syncname) ? file_get_contents($syncfile) : self::$syncname;
        }

       
        // Generate an App name
        self::$appname = ezy::$app_dir.(ezy::$app_dir=="web" ?ezy::$web_theme:"").md5(implode($data).self::$syncname);



        if ( self::$environment == "production" && self::$syncfile_exists && file_exists(self::$basepath.self::$assetpath.self::$appname.".".$type) )
        {
            $is_cached = 1;
        }
        // Load data
        else
        {
            foreach ( $data as $url )
            { 
                $output .= self::generateItem($url, $type);
            }
        }

        // Environments
        if ( self::$environment == "production" )
        {
            if (! is_dir(self::$basepath.self::$assetpath)) {
                mkdir(self::$basepath.self::$assetpath, 0755, true);
            }

            // Create file
            if ( $is_cached == 0 )
            {
                file_put_contents(self::$basepath.self::$assetpath.self::$appname.".".$type, $output);

                // Write date signature
                if ( empty(self::$syncname) ) {
                    file_put_contents($syncfile, time());
                }
            }

            // Create js/css tag to load via appname
            $output = self::generateTag(self::$assetpath.self::$appname.".".$type, $type);
        }

        //$output = "<!-- Start ".self::$appname.".".$type." -->".PHP_EOL."\t".$output."\t<!-- End ".self::$appname.".".$type." -->".PHP_EOL;
        //$output = $output;

        return $output;
    }

    /**
     * Generate js/css item
     * @param $url: Path of a file
     * @param $type: js/css
     * @return string: html
     */

    static private function generateItem($url, $type)
    {
        $output = "";

        // Developement
        if ( self::$environment == "development" )
        {
            $output .= self::generateTag($url, $type);
        }
        // Production
        else if ( self::$environment == "production" )
        {
            $output .= self::generateContent($url, $type);
        }
        // Test
        else {
            $output .= self::generateTag($url, $type);
        }
        return $output;
    }

    /**
     * Generate and compress the js/css file's content
     * @param $url: url/path
     * @param $type: js/css
     */

    static private function generateContent($url, $type)
    {
        list($url, $protocol) = self::parsePath($url);

        // Protocol = URL
        if ( $protocol == "url" )
        {
            //$output = "/* {$url} */".PHP_EOL;
            $output = file_get_contents($url).PHP_EOL;
        }
        // Protocol = Path
        else if ( $protocol == "path" )
        {
            //$output = "/* {$url} */".PHP_EOL;
            $output = file_get_contents(root_path.$url, FILE_USE_INCLUDE_PATH).PHP_EOL;
        }
        //
        else
        {
            $output = "// Unknown protocol";
        }

        // Minify
        $output = self::compressMinify($url, $output, $type);

        return $output;
    }


    /**
     * Generate html tag
     * @param $url: Path of a file
     * @param $type: js/css
     * @return string: Return html
     */

    static private function generateTag($url, $type)
    {
        list($url) = self::parsePath($url);

        // Javascript
        if ( $type == "js" )
        {
            //async
            $output = "<script type=\"text/javascript\" src=\"{$url}\"></script>".PHP_EOL;
        }
        // CSS
        else if ( $type == "css" )
        {
            $output = "<link rel=\"stylesheet\" type=\"text/css\" href='{$url}'>".PHP_EOL;
        }

        return $output;
    }

    /**
     * Parse URL, convert it into valid URL.
     * @param $url: path/URL
     */

    static private function parsePath($url)
    {
        // Check for an URL (HTTP)
        if ( substr($url, 0, 4) == "http" )
        {
            $url = $url;
            $protocol = "url";
        }
        // Path
        else
        { 
            // if Path without slash, then insert slash.
            $url = substr($url, 0, 1) != "/" ? self::$baseurl."/".$url : $url;

            $protocol = "path";
        }

        return array($url, $protocol);
    }

    /**
     * @param $url: Default System URL
     * @param $path: Default System Path
     */

    static public function setBase($url, $path, $srcpath)
    {
        self::$baseurl = $url;
        self::$basepath = $path;
        self::$srcpath = $srcpath;
    }

    /**
     * Compress data by minify
     * @param $output
     */

    static public function compressMinify($url, $output, $type)
    {
        require_once root_path.'/vendor/autoload.php';
        require_once root_path.'/kernel/lib/3rd/Minifier.php';

        if ( $type == "css" ){

            //echo trim(pathinfo($url, PATHINFO_DIRNAME)."/").PHP_EOL;
            //echo $url.PHP_EOL;
           // echo "/themes/nail01a/assets/css/".PHP_EOL;

            $options = [
                //"currentDir" => pathinfo($url, PATHINFO_DIRNAME)."/",
                "prependRelativePath" => pathinfo($url, PATHINFO_DIRNAME)."/", // pathinfo($url, PATHINFO_DIRNAME)
                "docRoot" => root_path
            ];

            $output = \Minify_CSSmin::minify($output, $options);
            //exit;
        }
        else if ( $type == "js" ){

            $output = \Minify\JS\JShrink::minify($output);
            //exit;
        }

        return $output;
    }
}