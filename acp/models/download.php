<?php

namespace models;

use lib\input;
use lib\db;

class download {
    static  public function setCookieToken(
        $cookieName, $cookieValue, $httpOnly = true, $secure = false ) {

        setcookie(
            $cookieName,
            $cookieValue,
            2147483647,            // expires January 1, 2038
            "/",                   // your path
            $_SERVER["HTTP_HOST"], // your domain
            $secure,               // Use true over HTTPS
            $httpOnly              // Set true for $AUTH_COOKIE_NAME
        );
    }


    static public function sendFile($url)
    {
        if(isset($_POST['downloadToken']))
        {
            self::setCookieToken('downloadToken', $_REQUEST['downloadToken'], false);
        }
       header("location: {$url}");
    }

    static function downloadSample($fileName,$fileExt = "xls",$lang = "")
    {
        $path = root_path."public/sample/{$fileName}.{$fileExt}";

        if($lang)
        {
            $pathLang = root_path."public/sample/{$fileName}_{$lang}.{$fileExt}";

            if(is_file($pathLang))
            {
                $path = $pathLang;
            }
        }

        if(!is_file($path))
        {
            return false;
        }
        else
        {
            self::downFile($path);
        }
    }

    public function downFile($filePath)
    {
        if(isset($_POST['downloadToken']))
        {
            self::setCookieToken('downloadToken', $_REQUEST['downloadToken'], false);
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($filePath).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
}  