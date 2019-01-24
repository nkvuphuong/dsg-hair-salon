<?php
/**
 * Created by PhpStorm.
 * User: HLoi
 * Date: 4/4/2017
 * Time: 10:42 AM
 */

namespace controller;

use core\ezy;
ezy::load_model("sites"); 
class register
{
    static public function auto_run()
    {
        // Check for act
        global $CMS;

        switch( ezy::$act )
        {
            // Buy Ezybook: http://eco.3f.design/register/buy-ezybook
            case "buy-ezybook":
                if(ezy::$input['step'] == 11)
                {
                   self::buy_step_11();

                }
                elseif(ezy::$input['step'] == 2)
                {
                    self::buy_step_2();

                }
                elseif(ezy::$input['step'] == 21)
                {
                    self::buy_step_21();

                }
                elseif(ezy::$input['step'] == 3)
                {
                    self::buy_step_3();

                }elseif(ezy::$input['step'] == 4)
                {
                    self::buy_step_4();

                }
                else
                {
                    unset( $_SESSION['reg_info']);
                    echo ezy::html("buy");
                }

                break;
            case "trial-ezybook":
                
                if(ezy::$input['step'] == 11)
                {
                     self::buy_step_11();

                }elseif(ezy::$input['step'] == 2)
                {
                    self::buy_step_2();

                }elseif(ezy::$input['step'] == 21)
                {
                    self::buy_step_21();

                }
                elseif(ezy::$input['step'] == 3)
                {
                    self::buy_step_3();

                }
                else
                {   unset( $_SESSION['reg_info']);
                    echo ezy::html("buy");
                }

                break;    
            case "trial-ezybook-success":
                self::trial_ezybook_success();
                break;
            // Buy Ezybook: http://eco.3f.design/register/phan-mem-ke-toan-doanh-nghiep
            case "phan-mem-ke-toan-doanh-nghiep":
                echo ezy::html("pricing");
                break;
            // Show a package: http://eco.3f.design/register/package/id-15
            case "payment-ezybook":
                if( ezy::$subact  == "success")
                {
                    self::payment_success();

                }
                else
                {

                    self::payment_ezybook();
                }
            break;
            case "package":
                echo ezy::html("package");
            break;   
            // Default page
            default:
                echo ezy::html();
        }
    }

    function buy_step_11()
    {
        global $CMS;

        $fullname 	= trim($CMS->input['fullname']);
        $phone 	  	= trim($CMS->input['phone']);
        $storename 	= trim($CMS->input['storename']);
        $code_storename 	= trim($CMS->input['code']);
        $username 	= trim($CMS->input['name']);
        $pass 		= trim($CMS->input['pass']);
        $package_id = intval($CMS->input['package_id']);
 
      //  unset($_SESSION['reg_info']);
        // Set input to var SESSION
        foreach ($CMS->input as $key => $value) {
            # code...
            if($value != "")
            {
                $_SESSION['reg_info'][$key] = $value;
            }
        }

        if ( ! $package_id )
        {
            header('Location: /register/phan-mem-ke-toan-doanh-nghiep');die;
        }

        if ( ! $fullname )
        {
            $_SESSION['error_msg'] = "Vui lòng nhập họ tên";
            header('Location: /register/buy-ezybook/step-1/id-'.$package_id);die;

        }
        if ( ! $phone )
        {
            $_SESSION['error_msg'] = "Vui lòng nhập số điện thoại";
            header('Location: /register/buy-ezybook/step-1/id-'.$package_id);die;
        }
        if ( ! $storename )
        {
            $_SESSION['error_msg'] = "Vui lòng nhập tên cửa hàng";
            header('Location: /register/buy-ezybook/step-1/id-'.$package_id);die;
        }

        if ( ! $code_storename )
        {
            $_SESSION['error_msg'] = "Vui lòng nhập địa chỉ gian hàng";
            header('Location: /register/buy-ezybook/step-1/id-'.$package_id);die;
        }
        if ( ! $username )
        {
            $_SESSION['error_msg'] = "Vui lòng nhập tên đăng nhập";
            header('Location: /register/buy-ezybook/step-1/id-'.$package_id);die;
        }
        if ( ! $pass )
        {
            $_SESSION['error_msg'] = "Vui lòng nhập mật khẩu";
            header('Location: /register/buy-ezybook/step-1/id-'.$package_id);die;
        }
        if(\core\ezy::$act == "buy-ezybook" )
        {
            
            header('Location: /register/buy-ezybook/step-2/id-'.$package_id);die;
        }
        else
        {
             header('Location: /register/trial-ezybook/step-2/id-'.$package_id);die;
        }

        
    }

    function buy_step_2()
    {
         echo ezy::html("buy_step_2");
    }


    function buy_step_21()
    {
        global $CMS;
        $_SESSION['reg_info']['email'] = $email = $CMS->input['email'];
        $_SESSION['reg_info']['location'] = $location = $CMS->input['location'];
        $_SESSION['reg_info']['industry'] = $industry = intval($CMS->input['industry']);
        $_SESSION['reg_info']['address'] = $address = trim($CMS->input['address']);
        $_SESSION['reg_info']['package_id'] = $package_id = $CMS->input['package_id'];

        if ( ! $location ) 
        {  

            $_SESSION['error_msg'] = "Vui lòng chọn tỉnh thành";
            header('Location: /register/'.$type.'-ezybook/step-2/id-'.$package_id);die;
        }

        if ( ! $email ) 
        {   

            $_SESSION['error_msg'] = "Vui lòng nhập Email";
            header('Location: /register/'.$type.'-ezybook/step-2/id-'.$package_id);die;
        }

        // SAve info
        if(\core\ezy::$act == "buy-ezybook" )
        {

           echo ezy::html("payment");
        }
        else
        {
            self::buy_step_3();
        }
    }


    function buy_step_3()
    {
        global $CMS;

        if( \models\sites::add("trial") != false)
        {
            header('Location: /register/trial-ezybook-success');die;
        }

    }

    

    function trial_ezybook_success()
    {
        echo ezy::html("buy_success");
    }

    function payment_ezybook()
    {
        global $CMS;
        if ( !$_SESSION['reg_info'] )
        {   
            header('Location: /register/buy-ezybook/step-1');die;

           
        }

        if ( !$CMS->sites->payment_nganluong() )
        {
            //$CMS->global->redirect($CMS->vars['root_domain'] . '/?site=subscription&act=payment');
        }

    }

}

