<?php

namespace lib;

$CMS->class->cookie = new cookie;

class cookie {
	
	static public $path = "/";
	static public $domain;

	public function set_cookie($name, $value = "", $remember = 1)
	{
		$expires = 0;

		if ( $remember == 1 )
		{
			$expires = time() + 60*60*24*365;
		}
 
        return setcookie($name, $value, $expires, self::$path, self::$domain);
	}

	public function get_cookie($name)
	{
        if (isset($_COOKIE[$name]))
        {
            return urldecode($_COOKIE[$name]);
        }
        else
        {
            return false;
        }
	}
	
	public function delete($name)
    {
        unset($_COOKIE[$name]);
   		return $this->set_cookie($name, NULL, -1);
		//setcookie($name, NULL, -1);
    }
	
	public function set_cookie_time($name = "", $time = "",$value = "")
	{
		// Get cookie
		if($_COOKIE["{$name}"]) 
		{
			return false;
		}
		else
		{
			// Set cookie
			setcookie($name, $value, $time, self::$path, self::$domain);
			return true;
		}
	}

}

?>