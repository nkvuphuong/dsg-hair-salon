<?php

namespace models;

use core\ezy;
use lib\input;

class sites
{
    /**
     * Get List sites
     * @return array
     */

    static public function add($type = "buy", $unset = 0 )
    {
        global $CMS, $DB;

        // User input
     
        $fullname   = trim($_SESSION['reg_info']['fullname']);
        $phone      = trim($_SESSION['reg_info']['phone']);
        $storename  = trim($_SESSION['reg_info']['storename']);
        $code_storename     = trim($_SESSION['reg_info']['code']);
        $username   = trim($_SESSION['reg_info']['name']);
        $pass       = trim($_SESSION['reg_info']['pass']);
        $package_id = intval($CMS->input['package_id']);
 
        $email = $_SESSION['reg_info']['email'];
        $location = $_SESSION['reg_info']['location'];
        $industry = intval($_SESSION['reg_info']['industry']);
        $address = trim($_SESSION['reg_info']['address']);
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $time = time();
        if($type == "trial")
        {
            $site_regtype = 1;
        }
        else
        {
            $site_regtype = 0;
        }
         
        if (  $package_id <= 0   ) 
        {  
            header('Location: /register/phan-mem-ke-toan-doanh-nghiep');die;
        }
     

 
        // Check input
        if ( ! $fullname ) 
        {  
     
            $_SESSION['error_msg'] = "Vui lòng nhập họ tên"; 

            header('Location: /register/'.$type.'-ezybook/step-1/id-'.$package_id);die;
        }
        if ( ! $phone ) 
        {   

            $_SESSION['error_msg'] = "Vui lòng nhập số điện thoại"; 
            header('Location: /register/'.$type.'-ezybook/step-1/id-'.$package_id);die; 
        }
        if ( ! $storename ) 
        {    

            $_SESSION['error_msg'] = "Vui lòng nhập tên cửa hàng";
            header('Location: /register/'.$type.'-ezybook/step-1/id-'.$package_id);die; 
        }

        if ( ! $code_storename ) 
        { 

            $_SESSION['error_msg'] = "Vui lòng nhập địa chỉ gian hàng"; 
            header('Location: /register/'.$type.'-ezybook/step-1/id-'.$package_id);die; 
        }
        if ( ! $username ) 
        { echo "1111222fsdfs21";exit;

            $_SESSION['error_msg'] = "Vui lòng nhập tên đăng nhập";
            header('Location: /register/'.$type.'-ezybook/step-1/id-'.$package_id);die;
        }
        if ( ! $pass ) 
        {  

            $_SESSION['error_msg'] = "Vui lòng nhập mật khẩu";
            header('Location: /register/'.$type.'-ezybook/step-1/id-'.$package_id);die;
        }
        if ( strlen($pass) < 6 ) 
        {   
            $_SESSION['error_msg'] = "Mật khẩu tối thiểu 6 ký tự";
            header('Location: /register/'.$type.'-ezybook/step-1/id-'.$package_id);die;
        }

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

        // Check exits email
        if($CMS->sites->check_exist("email",$email) == true)
        {  
            $_SESSION['error_msg'] = "Email đã được sử dụng";
            header('Location: /register/'.$type.'-ezybook/step-2/id-'.$package_id);die;
        } 

 

        $user_password = md5($pass);
        
        // Hash + salt
        $salt = $CMS->user->create_pwd_salt(5);
        $salt = addslashes($salt);
        $pwdhash = $CMS->user->create_pwd_hash( $salt, $user_password );
 
        // Insert data
        $DB->query("INSERT INTO ".root_table."sites (fullname, phone, email, username, pwdhash,  pwdsalt, code_storename, storename, industry, location, address, package_id,site_time,ip_address,site_regtype) VALUES ('{$fullname}', '{$phone}', '{$email}', '{$username}', '{$pwdhash}', '{$salt}', '{$code_storename}', '{$storename}', '{$industry}' ,'{$location}', '{$address}', '{$package_id}','{$time}', '{$ip_address}', '{$site_regtype}')");
        
        $id = $DB->last_insert_id();
        if($unset == 0)
        {
            unset($_SESSION['reg_info']);
        }
        
        $_SESSION['reg_info_success']['storename'] = $storename;
        $_SESSION['reg_info_success']['code_storename'] = $code_storename;
        $_SESSION['reg_info_success']['username'] = $username;

        return $id;
       
    }

    
}