<?php

use \core\ezy;

$login = new login;
$login->auto_run();

class login {
	
	public function auto_run()
	{
		global $CMS, $DB;

		// Title
		ezy::$title = "-> {$CMS->lang['login_text']}";
 
		switch ( $CMS->input["act"] )
		{
			case "do":
				$this->log_in();
				$CMS->output .= self::main();
			break;
			case "logout":
				$this->log_out();
			break;
			default:
				$CMS->output .= self::main();
		    break;
		}
	}

    /**
     * Main page
     */

	static public function main()
    {
        global $CMS, $tpl;

        $tpl->referer = $CMS->vars['http_referer'] ? $CMS->vars['http_referer'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

        if ( isset( $CMS->vars['board_is_ssl']) && $CMS->vars['board_is_ssl'] == 1 )
        {
            $tpl->login = str_replace("http:", "https:", $CMS->vars['root_domain']);
        }
        else
        {
            $tpl->login = $CMS->vars['root_domain'];
        }

        // Output
        echo ezy::render();
    }
	
	public function log_in()
	{
		global $CMS, $tpl, $member;

		// Google ReCaptcha Verifier
        if ( \core\ezy::google_recaptcha_verify() == false )
        {
           // $CMS->errormsg = "Invalid Token!!!";

            //return false;
        }

        self::quicklogin();

		$name = $CMS->class->editor->input("name", "username");
		$password = $CMS->class->editor->input("password", "password");

		$input_password = md5( $password );
		//$input_password = $password; // acp_md5.js
		
		if ( ! $name )
		{
			$CMS->errormsg = $CMS->lang['incomplete_username'];
				
			return false;
		}
		
		if ( ! $password )
		{
			$CMS->errormsg = $CMS->lang['incomplete_password'];
				
			return false;
		}
		
		$member = $CMS->user->log_check_exist( $name, $input_password );

		if ( ! $member )
		{
			$CMS->errormsg = $CMS->lang['wrong_username'];
		
			return false;
		}
		
		if ( $member['user_status'] == 0 )
		{
			$CMS->errormsg = $CMS->lang['locked_username'];
		
			return false;
		}

		if ( $CMS->user->log_check_password( $input_password, $member["user_hash"], $member["user_salt"] ) == false )
		{
			$CMS->errormsg = $CMS->lang['wrong_password'];

			return false;
		}

		$CMS->user->log_do_in( $member );
		
		//Check HTTP REFERER exist as login
		if ( $CMS->input["url"] )
		{
			if ( preg_match( "/(login|logout|config)/", $CMS->input["url"] ) )
			{
				$CMS->vars['referer_found'] = 1;
			}
			else
			{
				$CMS->vars['referer_found'] = 0;
			}
		
			if ( $CMS->vars['referer_found'] == 1 ) 
			{ 
				$page = $CMS->vars['root_domain'];
			} 
			else
			{
				$page = $CMS->input["url"];
			}
		}
		else
		{
			$page = $CMS->vars['root_domain'];
		}
		//End check referer
		
		$CMS->class->logs->insert($CMS->lang['login_text']);

		// Output transfer
        $tpl->msg = $CMS->lang['login_success'];
        $tpl->page_transfer = isset($CMS->input['returnUrl']) && $CMS->input['returnUrl'] ? $CMS->input['returnUrl'] : $page;
		echo ezy::render("page_transfer");
		exit;
	}

	static function quicklogin(){
        global $CMS;
//http://dev.local/acp/?site=login&act=do&quicklogin=1&logintoken=eyJhbGciOiJIUzI1NiJ9.eyJuYW1lIjoidGhhbWx2IiwicGFzc3dvcmQiOiIxMjM0NTYifQ.TUhUWh1p0sh3Xavb19307xngyV0cMb-6G6aG1fcbgtA
        if($CMS->input['quicklogin'] ==1) {
            $logintoken = $CMS->input['logintoken'];
            if (isset($logintoken)) {
                try {
                    $data = Firebase\JWT\JWT::decode($logintoken, '3fteamToken', ['HS256']);
                } catch (Exception $e) {
                    $_SESSION['msg']='Token login invalid.';
                    header("location: /acp/?site=login");exit;
                }
                if($data) {
                    $_POST['name'] = $data->name;
                    $_POST['password'] = $data->password;
                }
            }
        }
    }

	public function log_out()
	{
		global $CMS, $tpl;

		$CMS->user->log_do_out();
		
		$CMS->class->logs->insert($CMS->lang['logout_text']);

        // Output transfer
        $tpl->msg = $CMS->lang['logout_success'];
        $tpl->page_transfer = $CMS->vars['root_domain'];
        echo ezy::render("page_transfer");
        exit;
	}
}

?>