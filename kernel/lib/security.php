<?php
namespace lib;
use \core\ezy;
use Exception;
use Firebase\JWT\JWT;

$CMS->class->security = new security;

class security {

	public $captcha_name = "default";

	//===========================================================================
	//  CAPTCHA CHECK
	//===========================================================================

	public function check()
	{
		global $CMS;

		$security_code = $CMS->class->filter->clean_value($CMS->input['security_code']);

		if ( md5($security_code) == $_SESSION["security_captcha_{$this->captcha_name}"] )
		{
			unset($_SESSION["security_captcha_{$this->captcha_name}"]);
			return true;
		}
		else
		{
			unset($_SESSION["security_captcha_{$this->captcha_name}"]);
			
			return false;	
		}
	}

	//===========================================================================
	//  CAPTCHA
	//===========================================================================
		
	public function captcha( $mod = "" )
	{
		global $CMS;
		
		// Captcha name
		$mod = $mod ? $mod : $this->captcha_name;

		// Create text
		$random_number = $CMS->class->random->number();
		$_SESSION["security_captcha_{$mod}"] = md5($random_number);
		
		// Save session
		$CMS->class->session->save();

		// Create captcha
		require_once(root_path."kernel/lib/3rd/captcha.php");
		
		$captcha = new class_captcha();

		// OPTIONAL Change configuration...
		//$captcha->wordsFile = "words/es.php";
		$captcha->session_var = "security_captcha";
		$captcha->imageFormat = "png";
		$captcha->scale = 3;
		$captcha->blur = true;
		$captcha->resourcesPath = root_path."tools/captcha";
		//$captcha->backgroundColor = $isa == 1 ? array(0xE5, 0xEE, 0xCC) : array(0xF8, 0xFD, 0xFF);
		
		// OPTIONAL Simple autodetect language example
		/*
		if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$langs = array('en', 'es');
			$lang  = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
			if (in_array($lang, $langs)) {
				$captcha->wordsFile = "words/$lang.php";
			}
		}
		*/
		
		// Image generation
		$captcha->CreateImage( $random_number );	
	}
		
	//===========================================================================
	//  ENCODE
	//===========================================================================
	
	public function strhex($string)
	{
		$hex="";
		for ($i=0;$i<strlen($string);$i++)
			$hex.=(strlen(dechex(ord($string[$i])))<2)? "0".dechex(ord($string[$i])): dechex(ord($string[$i]));
		return $hex;
	}
	
	public function hexstr($hex)
	{
		$string="";
		for ($i=0;$i<strlen($hex)-1;$i+=2)
			$string.=chr(hexdec($hex[$i].$hex[$i+1]));
		return $string;
	}
	
	public function encode($data)
	{
		$data = $this->strhex($data);
	
		$data = base64_encode($data);
	
		$start = floor(strlen($data) / 2);
		$end = strlen($data) - $start;
	
		$data = $start . ":/" . substr($data,-$end,$end) . substr($data,0,$start);
	
		$output = @gzcompress($data);
		
		$output = base64_encode($output);
		
		return $output;
	}
	
	public function decode($data)
	{
		$output = base64_decode($data);
	
		$output = @gzuncompress($output);
		
		$array = explode(":/", $output);
		
		$number = substr($output,0,strlen($array[0]));
		
		$output = substr($output,strlen($array[0])+2,strlen($output));
		
		$output = substr($output,-$number,$number).substr($output,0,strlen($output)-$number);
		
		$output = base64_decode($output);
		
		$output = $this->hexstr($output);
		
		return $output;
	}

	//===========================================================================
	//  GZCOMPRESS BUFFERING
	//===========================================================================
	
	public function compress($string)
	{
		global $CMS;

		if ( isset($CMS->class->mysql->is_error) && $CMS->class->mysql->is_error == 1 )
		{
			print $string;
			return false;	
		}

        if (extension_loaded('zlib')){
		    ob_end_clean();
		    ob_start('ob_gzhandler');
		}
		else{
            ob_start('ob_gzhandler');
        }
		
		print $string;
		
		//ob_end_flush();
		
		exit;
	}
	
	//===========================================================================
	//  CHECK/LOAD WHITELIST
	//	Based on cache: board_whitelist_ip (serialize array(0 => "xxx.xxx.xxx.xxx"))
	//===========================================================================

	public function check_whitelist()
	{
		global $CMS;

		if ( isset($CMS->vars['whitelist_acp_enable']) && $CMS->vars['whitelist_acp_enable'] == true )
		{
            if ( !in_array(ezy::$ip_address, explode(",", trim($CMS->vars['whitelist_acp']))) )
            {
                return false;
            }
        }

        return true;
	}

	//============================================================================
	//  SECURITY FORM 
	//  29/07/2017
	//============================================================================

	static function create_token()
    {
        global $CMS;

        if(isset($_SESSION['token']) && $_SESSION['token']) return $_SESSION['token']; //Neu da ton tai thi k can tao nua

        $hash = "3fsecurity";
        $str_random = $CMS->class->random->character(9);
        $token = md5($hash.$str_random.time());

        $_SESSION['token'] = $token;
        return $token;
    }

    static function check_token($token="")
    {
        global $CMS;

        $token = $token ? $token : (isset($CMS->input['token']) ? $CMS->input['token'] : null);
        if(!isset($_SESSION['token']) || $token !== $_SESSION['token'])
        {
            return false;
        }else
        {
        	//unset session
        	unset($_SESSION['token']);
            return true;
        }
    }

    static function checkSecurityIp()
    {
        global $CMS, $DB;
        $time_allow = isset($CMS->vars['time_allow_ip']) ? intval($CMS->vars['time_allow_ip']) : 5;
        $val_module = ezy::$site;
        $val_action = ezy::$act;
        $val_ip = $_SERVER['REMOTE_ADDR'];
        $curr_time = time();
        $sql = $DB->query("SELECT * FROM ".root_table."validate_ip WHERE val_module='{$val_module}' AND val_action='{$val_action}' AND val_ip='{$val_ip}' LIMIT 1");
        if($DB->num_rows($sql) > 0)
        {
            $data = $DB->fetch_array($sql);
            // check diều về time
            $time_check = $data['val_time'] + $time_allow * 60;
            if($curr_time > $time_check)
            {
                $DB->query("UPDATE ".root_table."validate_ip SET val_time='{$curr_time}' WHERE val_id='{$data['val_id']}'");
                return true;
            }else
            {
                $time_waiting = round(($time_check - $curr_time)/60);
                $_SESSION['error_msg'] = "Please wait in {$time_waiting} minutes";
                return false;
            }
        }else
        {
            $DB->query("INSERT INTO ".root_table."validate_ip (val_module, val_action, val_ip, val_time) VALUES ('{$val_module}','{$val_action}','{$val_ip}', '{$curr_time}')");
            return true;
        }

    }

    static function checkPermission($module = '', $action = '')
    {
        global $CMS, $member;

        if(input::arrayValue($CMS->permit,"{$module}_{$action}") || $CMS->vars['is_root'] || $CMS->vars['is_admin']) return true;

        return false;
    }

    /**
     * Get hearder Authorization
     * */
    static function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER["REDIRECT_HTTP_AUTHORIZATION"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
    /**
     * get access token from header
     * */
    static function getBearerToken() {
        $headers = self::getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    /**
     * Check valid access for API
     */
    static function checkValidAccess() {
        global $CMS, $tpl;

        $token = self::getBearerToken();

        self::jwtDecode($token);

        return true;
    }

    /**
     * @param $token
     * @return bool|object
     */
    static function jwtDecode($token) {
        try {
            return JWT::decode($token, ezy::$secret_key, ['HS256']);
        } catch (Exception $exception) {
            return false;
        }
    }
}

?>