<?php

//===========================================================================
// INITIALIZE DATA
//===========================================================================

require_once("../init.api.php");

//Load language
$CMS->class->language->auto_run();
$CMS->class->date->auto_run();

$api = new api_spo;
$api->autorun();

class api_spo
{

    public function autorun()
    {
        global $CMS;

        header('Content-Type: application/json');
     
        $key = urldecode($CMS->input["key"]);
        $service_name = urldecode($CMS->input["service_name"]);
 
        if ( isset($service_name) AND $service_name != "" )
        {   
            // Check acceptable site

            $data = file_get_contents($_SERVER['DOCUMENT_ROOT']."/api/cache/".$service_name.".txt");
         
            if ( ! $data )
            {
                $this->error("Unknown request");
            }
            else
            {
                if ( md5($data) != $key )
                {
                    $this->error("Invalid token key");
                }               
            }
            // $this->checkip($service_name);
        }
        else
        {
           
            $this->error("Unknown request");      
        
        }
        
        //Check CMD API DOmain
        if($CMS->input['cmd'] != "")
        {    
            $arr_cmd = array("create_site", "renew_site", "suspend_site", "get_info", "action_site", "fb_fanpage_add","sms_add");
            if(in_array($CMS->input['cmd'],$arr_cmd) == FALSE)
            {
                $return['status'] = "error"; 
                $return['message'] = "Tên lệnh để thực thi API không đúng"; 
                print json_encode($return);exit;
               
            }
        }

        // Switch action
        switch ( $CMS->input["act"] )
        {       
            case "create_site":
                $this->create_site();
            break;
            case "renew_site":
                $this->renew_site();
            break;
            case "get_info":
                $this->get_info();
            break;
            case "action_site":
                $this->action_site();
            break;
            case "suspend_site":
                $this->suspend_site();
            break;
            case "fb_fanpage_add":
                $this->fb_fanpage_add();
            break;
            case "sms_add":
                $this->sms_add();
            break;
            
        }
    }
    

    public function checkip($service_name)
    {
     
        $ip = file_get_contents($_SERVER['DOCUMENT_ROOT']."/api/cache/".$service_name."_ip.txt");
        
        $ip_address = $_SERVER['REMOTE_ADDR'];
     
        if($ip == "")
        {
             $this->error("Liên hệ Admin để cung cấp IP truy cập!");
            
            exit;      
        }

        
           

        if($ip_address != $ip)
        {
            $this->error("IP address can't access");
                
        }
         
        
    }


    public function error($mess)
    {
            $return['status'] = "error"; 
            $return['message'] = $mess; 
            print json_encode($return);exit;
    }

    

    public function create_site()
    {
        global $CMS, $DB;

       
        $list_info = explode("&amp;", $CMS->input['formdata']);
        foreach ($list_info as $key => $value) 
        {
            $arr = explode("=", $value);
            $info[$arr[0]] = $arr[1];
        }

        $cus_email = urldecode($info["cus_email"]); // email đang ky
        $cus_full_name = urldecode($info["cus_full_name"]); // email đang ky
        $cus_phone = urldecode($info["cus_phone"]); 
        $cus_type = urldecode($info["cus_type"]); 
        $cus_address = urldecode($info["cus_address"]); 
        $site_cycle = $sub['sub_license_cycle'] = $CMS->input['site_cycle'] = urldecode($info["site_cycle"]); 
        $site_theme =  $CMS->input['site_theme'] = urldecode($info["site_theme"]);
        $site_domainname = $CMS->input['site_domainname'] =urldecode($info["site_domainname"]);
        $site_regtype = $CMS->input['site_regtype'] =urldecode($info["site_regtype"]);// 1: trial; 0:subscripotion
        $package_id = $sub['sub_license_package']  = $CMS->input['package_id'] = urldecode($info["package_id"]);
        $site_code = $CMS->input['site_code'] =urldecode($info["site_code"]);// swith this site code from trial -> sub
        // Info subscription
        $sub['sub_name'] = urldecode($info["ord_service_name"]); 
        $sub['order_key'] = urldecode($info["ord_name"]); 
        $sub['sub_total'] = urldecode($info["ord_total"]); 
        $sub['payment_method'] = 15;//Direct from Nhanhoa

        if($site_code != "")
        {

        }
        else
        {
            // Check input
            if($site_theme == "")
            {
                $return['status'] = "error"; 
                $return['message'] = "Vui lòng chọn theme!"; 
                $return['data'] = array(); 
                print json_encode($return);exit;
            }
        }
       
        if($site_regtype == "" )
        {
            $return['status'] = "error"; 
            $return['message'] = "Vui lòng chọn loại đăng ký!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;
        }
        if($site_regtype == 0 )
        {
            if($site_domainname == "")
            {
                $return['status'] = "error"; 
                $return['message'] = "Vui lòng nhập tên miền đăng ký website!"; 
                $return['data'] = array(); 
                print json_encode($return);exit;
            }
          
        }

        if($site_cycle == "" OR  $site_cycle == 0)
        {
             
            $return['status'] = "error"; 
            $return['message'] = "Vui lòng nhập số tháng đăng ký website!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;
        }


        if($package_id == "" )
        {
            $return['status'] = "error"; 
            $return['message'] = "Vui lòng chọn gói dịch vụ web!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;
        }


        $member = $CMS->customer->getInfo($cus_email);

        //Check customer exits on this system web
        if(is_array($member))
        {
            $cus_id = $member['cus_id'];
        }
        else
        {
            $cus_time = time();
            //Auto create customer
            $DB->query("INSERT INTO `".root_table."customer` (`cus_time`, `cus_full_name`, `cus_address`, `cus_type`, `cus_phone`, `cus_company`,   `cus_email`) VALUES ('".time()."','{$cus_full_name}','{$cus_address}','{$cus_type}','{$cus_phone}','{$cus_company}','{$cus_email}')");
            $id = $DB->last_insert_id();
            $DB->query("UPDATE ".root_table."customer SET cus_code='CUS{$id}' WHERE cus_id='{$id}'");
            $cus_id = $id;
        }

        if($site_code != "")
        {
            $site_info = $CMS->sites->get_info($site_code);
            if(!is_array($site_info))
            {
                $return['status'] = "error"; 
                $return['message'] = "Thông tin website cần nâng cấp không tồn tại!"; 
                $return['data'] = $data_info;   
            }
            else
            {
                $time = time();
                
                $a = '+'.$site_cycle.' months'; 
                $time_end = strtotime($a, $time); 

                // Replace email hosting
                $new_email_hosting = str_replace( $site_info['site_domainname'] , $site_domainname , $site_info['email_hosting']);
                $change_domain_time = time();
                // Update status
                $DB->query("UPDATE ".root_table."sites SET site_regtype = 0, site_license_expired = '{$time_end}', site_status = 10, old_site_domainname ='{$site_info['site_domainname']}', site_domainname = '{$site_domainname}', email_hosting = '{$new_email_hosting}', site_domain_extra='', change_domain_time='{$time}'  WHERE site_id='{$site_info['site_id']}'");

                $site_info_2 = $CMS->sites->get_info($site_code);
                $return['status'] = "success"; 
                $return['message'] = "Đã chuyển website ".$site_domainname." #".$site_info['site_code']." thành Subscription thành công!"; 
                $return['data'] = $site_info_2;   
                 // Add subscription
                $sub['cus_id'] = $cus_id;
                $sub['site_id'] = $site_info_2['site_id'];
                $sub['sub_license_expired']= $site_info_2['site_license_expired'];;
                $CMS->subscription->api_add($sub);


            }
        }
        else
        {

            //Add site
            list($status, $msg, $data_info)  = $CMS->sites->add($cus_id);
            // Add subscription
            $sub['cus_id'] = $cus_id;
            $sub['site_id'] = $data_info['site_id'];
            $sub['sub_license_expired']= $data_info['site_license_expired'];;
            $CMS->subscription->api_add($sub);


            $return['status'] = $status; 
            $return['message'] = $msg; 
            $return['data'] = $data_info;   
 
        }
  
        print json_encode($return);exit;
    }
    

    public function action_site()
    {
        global $CMS, $DB ;

       
        $list_info = explode("&amp;", $CMS->input['formdata']);
        foreach ($list_info as $key => $value) 
        {
            $arr = explode("=", $value);
            $info[$arr[0]] = $arr[1];
        }
        $CMS->class->language->load("lang_sites");

        $site_id = urldecode($info["site_id"]); // site_id 
        $site_action = urldecode($info["site_action"]); // site_id 
        // Check input
        if($site_id == "")
        {
            $return['status'] = "error"; 
            $return['message'] = "Thiếu thông tin Website ID!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;
        }
        if($site_action == "")
        {
            $return['status'] = "error"; 
            $return['message'] = "Thiếu thông tin lệnh thao tác Website!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;
        }

        $site = $CMS->sites->get_info($site_id);

        //Check customer exits on this system web
        if(!is_array($site))
        {
            $return['status'] = "error"; 
            $return['message'] = "Thông tin Website không tồn tại trong hệ thống!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;
        }
         
        //Add site
        list($status, $msg, $data_info)  = $CMS->sites->action($site,$site_action);

        $return['status'] = $status; 
        $return['message'] = $msg; 
        $return['data'] = $data_info;   

        print json_encode($return);exit;
    }
        
    public function renew_site()
    {
        global $CMS, $DB ;

       
        $list_info = explode("&amp;", $CMS->input['formdata']);
        foreach ($list_info as $key => $value) 
        {
            $arr = explode("=", $value);
            $info[$arr[0]] = $arr[1];
        }
        $CMS->class->language->load("lang_sites");

        $site_domain = urldecode($info["site_domain"]); // site_domain 
        $site_cycle = intval(urldecode($info["site_cycle"])); // site_cycle 
        // Check input
        if($site_domain == "")
        {
            $return['status'] = "error"; 
            $return['message'] = "Thiếu thông tin tên miền!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;
        }
        if($site_cycle == "" OR $site_cycle == 0)
        {
            $return['status'] = "error"; 
            $return['message'] = "Thiếu thông tin thời gian cần gia hạn!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;
        }

        $site = $CMS->sites->get_info($site_domain);

        //Check  exits on this system web
        if(!is_array($site))
        {
            $return['status'] = "error"; 
            $return['message'] = "Thông tin Website không tồn tại trong hệ thống!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;
        }
         
        //Add site
        list($status, $msg, $data_info)  = $CMS->sites->renew($site,$site_cycle);

        $return['status'] = $status; 
        $return['message'] = $msg; 
        $return['data'] = $data_info;   

        print json_encode($return);exit;
    }


    public function suspend_site()
    {
        global $CMS, $DB ;
        include_once root_path."whm/models/sites.php";
        include_once root_path."whm/models/server.php";
       
        $list_info = explode("&amp;", $CMS->input['formdata']);
        foreach ($list_info as $key => $value) 
        {
            $arr = explode("=", $value);
            $info[$arr[0]] = $arr[1];
        }
        $CMS->class->language->load("lang_sites");

        $site_domain = urldecode($info["site_domain"]); // site_domain 
    
        // Check input
        if($site_domain == "")
        {
            $return['status'] = "error"; 
            $return['message'] = "Thiếu thông tin tên miền!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;
        }
         

        $site = $CMS->sites->get_info($site_domain);

        //Check  exits on this system web
        if(!is_array($site))
        {
            $return['status'] = "error"; 
            $return['message'] = "Thông tin Website không tồn tại trong hệ thống!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;
        }
        $time = time();
        $CMS->input['id'] = $site['site_id'];
        if(sites::suspend() == true)
        {
            // Update time suspend
            $DB->query("UPDATE ".root_table."sites SET site_time_suspend = '{$time}', is_suspend = 1  WHERE site_id='{$site['site_id']}'");
            $return['status'] = "success"; 
            $return['message'] = "Time Expired -> Suspend site : {$site['site_domainname']} success!";
        }   
        else
        {   
            $return['status'] = "error"; 
            $return['message'] = "Time Expired ->  Suspend site : {$site['site_domainname']} fail!"; 
        }
 
        print json_encode($return);exit;
    }


    public function get_info()
    {
        global $CMS;

        $list_info = explode("&amp;", $CMS->input['formdata']);
        foreach ($list_info as $key => $value) 
        {
            $arr = explode("=", $value);
            $info[$arr[0]] = $arr[1];
        }

        $site_code = urldecode($info["site_code"]);
        if($site_code == "")
        {
            $result['status'] = "error";
            $result['message'] = "Mã website không được rỗng!"; 
            print json_encode($result);exit;

        }


        $site = $CMS->sites->get_info($site_code);
        if(!is_array($site))
        {
            $result['status'] = "error";
            $result['message'] = "Không tồn tại website này!"; 
            print json_encode($result);exit;

        }
        else
        {
            $result['status'] = "success";
            $result['message'] = "Lấy thông tin website thành công!"; 
            $result['data_info'] = $site; 

            print json_encode($result);exit;

        }
   
    
        
    

    }



    public function fb_fanpage_add()
    {
        global $CMS, $DB ;

       
      //  $list_info = explode("&amp;", $CMS->input['formdata']);
        foreach ($CMS->input as $key => $value) 
        {
            $arr = explode("=", $value);
            $info[$arr[0]] = $arr[1];
        }
 
        $site_domain =  urldecode($CMS->input["site_domain"]);
        $fanpage_id = urldecode($CMS->input["fanpage_id"]); // fanpage_id 
        $fanpage_name = urldecode($CMS->input["fanpage_name"]); // fanpage_id 
        $access_token = urldecode($CMS->input["access_token"]); // fanpage_id 

     
        // Check input
        if($site_domain == "")
        {
            $return['status'] = "error"; 
            $return['message'] = "Vui lòng nhập website!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;
        }
        if($fanpage_id == "")
        {
            $return['status'] = "error"; 
            $return['message'] = "Thiếu thông tin Fanpage ID!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;
        }

        if($fanpage_name == "")
        {
            $return['status'] = "error"; 
            $return['message'] = "Thiếu thông tin Fanpage Name!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;
        }

        if($access_token == "")
        {
            $return['status'] = "error"; 
            $return['message'] = "Thiếu thông tin Access Token!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;
        }


        $site = $CMS->sites->get_info_2($site_domain);

        //Check customer exits on this system web
        if(!is_array($site))
        {
            $return['status'] = "error"; 
            $return['message'] = "Thông tin Website không tồn tại trong hệ thống!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;
        }
        $time = time();
        
        $sql = $DB->query("SELECT * FROM ".root_table."fb_fanpage WHERE site_id = '{$site['site_id']}' AND fanpage_id = '{$fanpage_id}' AND fbf_deleted = 0");
        if($DB->num_rows($sql) > 0)
        {
            $return['status'] = "success"; 
            $return['message'] = "Thông tin fanpage đã tồn tại trong hệ thống!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;
        }

        $DB->query("INSERT INTO `".root_table."fb_fanpage` ( `fanpage_name`, `fanpage_id`, `fanpage_access_token`, `fbf_time`, `site_id`) VALUES ('{$fanpage_name}','{$fanpage_id}','{$access_token}','{$time}','{$site['site_id']}')");


      
        $return['status'] = "success"; 
        $return['message'] = "Tạo thông tin fanpage fb thành công!"; 
        $return['data'] = array(); 
 

        print json_encode($return);exit;
    }
        

    public function sms_add()
    {
        global $CMS, $DB, $DBW;

       
        $list_info = explode("&amp;", $CMS->input['formdata']);
        foreach ($list_info as $key => $value) 
        {
            $arr = explode("=", $value);
            $info[$arr[0]] = $arr[1];
        }

        $sms_from  = urldecode($CMS->input["sms_from"]); // email đang ky
        $sms_to = urldecode($CMS->input["sms_to"]); // email đang ky
        $sms_content = urldecode($CMS->input["sms_content"]); 
        $time = urldecode($CMS->input["time"]); 
        $sms_reply = urldecode($CMS->input["sms_reply"]); 
        $sms_status = urldecode($CMS->input["sms_status"]); 
        $site_id = urldecode($CMS->input["site_id"]); 
        
        $DB->query("INSERT INTO `".root_table."sms` ( `fanpage_name`, `fanpage_id`, `fanpage_access_token`, `fbf_time`, `site_id`) VALUES ('{$fanpage_name}','{$fanpage_id}','{$access_token}','{$time}','{$site['site_id']}')");


        //Add site
        list($status, $msg, $data_info)  = $CMS->sites->add($cus_id);

        $return['status'] = $status; 
        $return['message'] = $msg; 
        $return['data'] = $data_info;   


  
        print json_encode($return);exit;
    }
    

    public function array2XML($obj, $array)
    {
        foreach ($array as $key => $value)
        {
            if(is_numeric($key))
                $key = 'item' . $key;

            if (is_array($value))
            {
                $node = $obj->addChild($key);
                $this->array2XML($node, $value);
            }
            else
            {
                $obj->addChild($key, htmlspecialchars($value));
            }
        }
    }

    

    
}
?>