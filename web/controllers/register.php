<?php
/**
 * Created by PhpStorm.
 * User: HLoi
 * Date: 4/4/2017
 * Time: 10:42 AM
 */

namespace controller;
use core\ezy;
use models\login;
use models\customer;

ezy::load_model("customer");
ezy::load_model("login");

class register
{
    /**
     * Controller
     */

    static public function auto_run()
    {
        switch ( ezy::$act )
        {
            case "submit":
                self::submit();
            break;
            case "login_zalo":
                self::zalo();
            break;
            default:
                self::main();
            break;
        }
    }

    /**
     * Default page
     */

    static private function main()
    {
    	global $tpl, $CMS, $member;
  
    	//Check is not login redirect page
  
    
    	if($CMS->vars['is_login'] == 1)
    	{
    		header("location: {$CMS->vars['root_domain']}");exit;
    	}

    	if($CMS->vars['is_login'] == 0)
        {
        	$_SESSION['referer']  = $_SESSION['link_back'] = "{$CMS->vars['http_referer']}";
        }
        if(ezy::$web_theme == "dsg")
        {
            $tpl->zalo_url =  $CMS->api->zalo->get_urllogin("{$CMS->vars['parent_domain']}/register/login_zalo/");
        }
        echo ezy::html();
    }


     /**
     * Zalo page
     */

    static private function zalo()
    {
        global $tpl, $CMS, $member;
  
        //Check is not login redirect page
  
    
        if($CMS->vars['is_login'] == 1)
        {
            header("location: {$CMS->vars['root_domain']}");exit;
        }

        if($CMS->vars['is_login'] == 0)
        {
            $_SESSION['referer']  = $_SESSION['link_back'] = "{$CMS->vars['http_referer']}";
        }
        if(ezy::$web_theme == "dsg")
        {
             $tpl->zalo_url =  $CMS->api->zalo->accesstoken("http://eco.lo/register/login_zalo/");
             if(is_array(  $tpl->zalo_url ) AND count(  $tpl->zalo_url ) > 0)
             {
                $_SESSION['msg'] = "Vui lòng nhập thông tin vào form dưới để hoàn tất việc đăng nhập!";
                $_SESSION['data_input']['cus_full_name'] =   $tpl->zalo_url['name'];
             }
        }
 
       header("location: {$CMS->vars['root_domain']}/register/");exit;
    }


    /**
     * Submit register
     */

    static private function submit()
    {
    	global $tpl, $CMS, $member;
    	$data = $CMS->input;
        // Clone data input
        foreach ($CMS->input as $key => $value) {
            # code...
            $_SESSION['data_input'][$key] = $value;
        }
		$data['password'] = $CMS->class->editor->input('cus_password');
		$data['repassword'] = $CMS->class->editor->input('cus_repassword');
 		$cus_id = customer::register_account($data);
		if($cus_id != false)
		{ 
			unset($_SESSION['msg']);
            unset($_SESSION['data_input']);
			$CMS->class->logs->key = "customer_{$cus_id}";
			$_SESSION['msg'] = $CMS->class->logs->insert("Account registration successful!").'<br />';
		}

        // login
        $data['cus_email'] = isset($data['cus_email']) ? $data['cus_email'] : null;
        $data['password'] = isset($data['password']) ? $data['password'] : null;
        login::cus_login($data['cus_email'], $data['password']);

		header("location: {$_SESSION['link_back']}");
    }

}