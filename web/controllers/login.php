<?php
/**
 * Created by PhpStorm.
 * User: HLoi
 * Date: 4/4/2017
 * Time: 10:42 AM
 */

namespace controller;

use core\ezy;
use models\customer;
use models\product;

ezy::load_model("customer"); 
//ezy::load_model("product"); 
class login
{
    /**
     * Controller
     */

    static public function auto_run()
    {   
 
        switch ( ezy::$act )
        {
            case "login_fb":
                self::login_fb();
            break;
            case "login_gg":
                self::login_gg();
            break;
            case "login_zalo":
                self::login_zalo();
            break;
            case "login_do":
                self::login_do();
            break;
            case "logout":
                self::logout();
            break;
            case "forgot-password":
                self::forgot_password();
            break;
            case "forgot-password-do":
                self::forgot_password_do();
            break;
            case "change-password":
                self::change_password();
            break;
            case "changepassword-do":
                self::changepassword_do();
            break;
        case "info":
                self::info_detail();
            break;
        case "product-follow":
                self::product_foolow();
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
        global $tpl, $CMS,$member;

        if(ezy::$web_theme == "dsg")
        {  
            $tpl->zalo_url =  $CMS->api->zalo->get_urllogin("{$CMS->vars['parent_domain']}/register/login_zalo/");
        }
        //Check is login redirect page 
        $_SESSION['link_back'] = "{$CMS->vars['http_referer']}";
        $tpl->title = "Login";
        if($CMS->vars['is_login'] == 1)
        {
           header("location: {$CMS->vars['root_domain']}");exit;
        }
        echo ezy::html();
    }

    /**
     * Change password page
     */

    static private function change_password()
    {
        global $tpl, $CMS,$member;    
        //Check is login redirect page
        $tpl->title = $_SESSION['member']['cus_password'] != "" ? "Change password" : "Create a password for your account";
        if($CMS->vars['is_login'] == 0)
        {
            // header("location: {$CMS->vars['root_domain']}");exit;
        }
        echo ezy::html("change_password");     
    }

    /**
     * Change password submit
     */

    static function changepassword_do()
    {
        global  $CMS;
        //Check is login redirect page
        if($CMS->vars['is_login'] == 0)
        {
            header("location: {$CMS->vars['root_domain']}");exit;
        }
        
        if( \models\login::change_password() == true)
        {
            $_SESSION['msg'] = "Change password successfully!";
            header("location: {$CMS->vars['root_domain']}/");exit;
        }
        header("location: {$CMS->vars['root_domain']}/login/change-password/");exit;
    }

    /**
     * Forgot password page
     */

    static private function forgot_password()
    {
        global $tpl, $CMS,$member;
        //Check is login redirect page
        $tpl->title = "Forgot password";
        if($CMS->vars['is_login'] == 1)
        {
           header("location: {$CMS->vars['root_domain']}");exit;
        }
        echo ezy::html("forgot_password");
    }

    /**
     * Forgot pasword submit
     */

    static private function forgot_password_do()
    {
        global $tpl, $CMS,$member;
         
        if($CMS->vars['is_login'] == 1)
        {
           header("location: {$CMS->vars['root_domain']}");exit;
        } 
        if($customer = \models\login::forgot_password())
        {
            $_SESSION['msg'] = "The system has sent new password information to the email address: {$CMS->input['email']}. Please check your mail box and follow the instructions." ;  
        }
        header("location: {$CMS->vars['root_domain']}/login/forgot-password/");
    }

    /**
     * Login Facebook
     */

    static private function login_fb()
	{
		global $CMS;

        $_SESSION['link_back'] = "{$CMS->vars['http_referer']}";
		if(\models\login::login_fb() == true)
        {
           unset($_SESSION['link_back'] );    
        
        }
		header("location: {$CMS->vars['root_domain']}/login");
		 
	}

    /**
     * Login Google
     */

    static private function login_gg()
    { 
        global $CMS, $DB, $member;
        $_SESSION['link_back'] = "{$CMS->vars['http_referer']}";
        \models\login::login_gg();         
    }


     /**
     * Login Zalo
     */

    static private function login_zalo()
    { 
        global $CMS, $DB, $member;
        $_SESSION['link_back'] = "{$CMS->vars['http_referer']}";
       
        $tpl->zalo_url =  $CMS->api->zalo->accesstoken("http://eco.lo/register/login_zalo/");
        print_r ( $tpl->zalo_url);exit;
        \models\login::login_zalo();         
    }

    /**
     * Login normal
     */

    static private function login_do()
    {
        global $CMS;
        \models\login::login_do();
    }

    /**
     * Logout
     */

    static private function logout()
    {
        global $CMS, $DB, $member;

        // Log out
        customer::cus_do_out();

        // Output message
        $_SESSION['msg'] = "Account logout successful!";
        $_SESSION['referer'] = "{$CMS->vars['root_domain']}";

        // Page transfer
        echo ezy::render("page_transfer", "layouts");
        exit;
    }
    /**
     * detail info customer
     */
    static private function info_detail()
    {
        global $CMS, $DB, $member,$tpl;
        if($member['cus_id']){
            $customer = customer::getInfo($member['cus_id']);
            $tpl->data=$customer;
        }else{
            // neu chua dang nhap thi chuyen ve form dang nhap
           $_SESSION['msg'] = $CMS->lang['login_accsess']; 
            $_SESSION['referer'] = "{$CMS->vars['root_domain']}/login";

        // Page transfer
            
            echo ezy::render("page_transfer", "layouts");
            exit;
        }
        echo ezy::html("info_detail", "login");
        exit;
    }
    static private function product_foolow()
    {
        global $CMS, $DB, $member,$tpl;
        
        
        if($member['cus_id']){
            $list_id_product=\models\product::list_id_product_follow($member['cus_id']);
            $tpl->list_id_product=$list_id_product;
            $data=\models\product::getListProduct(0,0,0,20);
            $tpl->modLink='/login/product-follow';
            $tpl->data=$data;
            
        }else{
            // neu chua dang nhap thi chuyen ve form dang nhap
           $_SESSION['msg'] = $CMS->lang['login_accsess']; 
            $_SESSION['referer'] = "{$CMS->vars['root_domain']}/login";

        // Page transfer
            
            echo ezy::render("page_transfer", "layouts");
            exit;
        }
                
        echo ezy::html("product_foolow", "login");
        exit;
    }
}