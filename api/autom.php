<?php
header('Access-Control-Allow-Origin: *'); 
//===========================================================================
// INITIALIZE DATA
//===========================================================================

require_once("../init.api.php");

//Load language
$CMS->class->language->auto_run();
$CMS->class->date->auto_run();

$api_chatbot = new api_chatbot;
$api_chatbot->autorun();

class api_chatbot
{

    public function autorun()
    {
        global $CMS;

        header('Content-Type: application/json');
 
        $key = urldecode($CMS->input["key"]);
        $service_name = urldecode($CMS->input["domain_name"]);
 
        if ( isset($key) AND $key != "" )
        {   
            // Check acceptable site
            $this->verify($key);
        }
        else
        {
            $this->error("Unknown request");
        }
        
        //Check CMD API DOmain
        if($CMS->input['cmd'] != "")
        {    
            $arr_cmd = array("verify_store", "get_product_list", "get_asset_list" , "get_order_list", "add_order", "add_customer", "get_order_history", "get_store_list", "get_accounts_list");
            if(in_array($CMS->input['cmd'],$arr_cmd) == FALSE)
            {
                $return['status'] = "error"; 
                $return['message'] = "Tên lệnh để thực thi API không đúng"; 
                print json_encode($return);exit;     
            }
        }

        // Switch action
        switch ( $CMS->input["cmd"] )
        {       
            case "get_product_list":
                $this->get_product_list();
            break;
            case "get_asset_list":
                $this->get_asset_list();
            break;
            case "get_order_history":
                $this->get_order_history();
            break;
            case "get_store_list":
                $this->get_store_list();
            break;
            case "get_accounts_list":
                $this->get_accounts_list();
            break;
            case "add_customer":
                $this->add_customer();
            break;
            case "add_order":
                $this->add_order();
            break;
        }
    }
    

    public function verify($key = "")
    {
        global $CMS, $DB;
        
        $sql = $DB->query("SELECT * FROM ".root_table."conf_settings WHERE conf_key = 'addon_chatbot_facebook_enable' ");
 
        if($DB->num_rows($sql) > 0)
        {
            $chatbot = $DB->fetch_array($sql);
            $chatbot_enable = intval($chatbot['conf_value']); 
            if($chatbot_enable == 0)
            {
                $return['status'] = "error"; 
                $return['message'] = "Tính năng Chatbot Facebook chưa được kích hoạt(01)!"; 
                $return['data'] = array(); 
                print json_encode($return);exit;
            }
           
        }
        else
        {
            $return['status'] = "error"; 
            $return['message'] = "Tính năng Chatbot Facebook chưa được kích hoạt(02)!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;
        }
     

        $sql_2 = $DB->query("SELECT * FROM ".root_table."conf_settings WHERE conf_key = 'addon_chatbot_facebook_key' ");
        if($DB->num_rows($sql_2) > 0)
        {
            $key_api = $DB->fetch_array($sql_2);
            $secret_key = trim($key_api['conf_value']); 
            if($secret_key != $key)
            {
                $return['status'] = "error"; 
                $return['message'] = "Chatbot Facebook Secret Key không đúng!"; 
                $return['data'] = array(); 
                print json_encode($return);exit;
            } 
        }
        else
        {
            $return['status'] = "error"; 
            $return['message'] = "Chatbot Facebook Secret Key không tồn tại!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;
        }  

        if($CMS->input['cmd'] == "verify_store")
        {
            $return['status'] = "success"; 
            $return['message'] = "Chatbot Facebook kết nối thành công!"; 
            $return['data'] = array("currency_type" => $CMS->vars['currency_type']); 
            print json_encode($return);exit;
        }


    }


    public function error($mess)
    {
            $return['status'] = "error"; 
            $return['message'] = $mess; 
            print json_encode($return);exit;
    }

    

    public function get_product_list()
    {
        global $CMS, $DB, $DBW;

        foreach ($CMS->input as $key => $value) 
        {
            $info[$key] = $value;
        }
        $store_id = urldecode($info["store_id"]); 
        $sql = $DB->query("SELECT * FROM `".root_table."product` WHERE `product_deleted`=0 ORDER BY product_id DESC");

        $arr = [];
        if($DB->num_rows($sql) > 0)
        {
            while ($data = $DB->fetch_array($sql)) {
               array_push($arr,$data);
            }
            $return['status'] = "success"; 
            $return['message'] = ""; 
            $return['data'] = $arr; 
            print json_encode($return);exit;
            
        }
        else
        {
            $return['status'] = "error"; 
            $return['message'] = "Dữ liệu không tồn tại!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;  
        }
        print json_encode($return);exit;
    }
    
    public function get_asset_list()
    {
        global $CMS, $DB, $DBW;
        foreach ($CMS->input as $key => $value) 
        {
            $info[$key] = $value;
        }
        $store_id = urldecode($info["store_id"]); 
        $sql = $DB->query("SELECT *, COUNT(ass_id) as cnt FROM ".root_table."assets WHERE ass_deleted = 0 AND parent_id=0 AND is_available = 1 AND store_id = '{$store_id}'  GROUP BY ass_key ORDER BY ass_id");
        $arr = [];
        if($DB->num_rows($sql) > 0)
        {
            while ($data = $DB->fetch_array($sql)) {
               array_push($arr,$data);
            }
            $return['status'] = "success"; 
            $return['message'] = ""; 
            $return['data'] = $arr; 
            print json_encode($return);exit;
            
        }
        else
        {
            $return['status'] = "error"; 
            $return['message'] = "Dữ liệu không tồn tại!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;  
        }
        print json_encode($return);exit;
    }
    
    public function get_order_history()
    {
        global $CMS, $DB, $DBW;
        foreach ($CMS->input as $key => $value) 
        {
            $info[$key] = $value;
        }
        $cus_email = urldecode($info["cus_email"]); 
        $cus_id = $CMS->customer->getInfo($cus_email, 'cus_id');
        if(intval($cus_id) == 0)
        {
            $return['status'] = "error"; 
            $return['message'] = "Email không tồn tại trong hệ thống"; 
            $return['data'] = $data; 
            print json_encode($return);exit;
            
        }
        $sql = $DB->query("SELECT * FROM `".root_table."order` WHERE `ord_deleted`=0 AND cus_id='{$cus_id}' ORDER BY ord_id DESC");

        $arr = [];
        if($DB->num_rows($sql) > 0)
        {
            while ($data = $DB->fetch_array($sql)) {
                $data['ord_amount_n']=  $CMS->class->input->currency($data['ord_amount']);
                $data['ord_total_n']=  $CMS->class->input->currency($data['ord_total']);
                $data['ord_tax_n']=  $CMS->class->input->currency($data['ord_tax']);
                $data['ord_discount_n']=  $CMS->class->input->currency($data['ord_discount']);

                $arr[] = $data;
            }
            $return['status'] = "success"; 
            $return['message'] = ""; 
            $return['data'] = $arr; 
            print json_encode($return);exit;
            
        }
        else
        {
            $return['status'] = "error"; 
            $return['message'] = "Dữ liệu không tồn tại!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;  
        }
        print json_encode($return);exit;
    }
    
    public function add_customer()
    {
        global $CMS, $DB;
        //$list_info = explode("&amp;", $CMS->input);
        foreach ($CMS->input as $key => $value) 
        {
            $info[$key] = $value;
        }
        $cus_fullname = urldecode($info["cus_fullname"]); 
        $cus_phone = urldecode($info["cus_phone"]); 
        $cus_email = urldecode($info["cus_email"]); 
        $cus_address = urldecode($info["cus_address"]); 
       
        $cus_id = $CMS->customer->getInfo($cus_email, 'cus_id');

        if(intval($cus_id) > 0)
        {    
            if(intval($CMS->vars['addon_chatbot_facebook_override']) == 1)
            {
                $DB->query("UPDATE ".root_table."customer SET cus_full_name = '{$cus_fullname}', cus_phone = '{$cus_phone}', cus_email ='{$cus_email}', cus_address = '{$cus_address}' WHERE cus_id='{$cus_id}'");
            }
            $return['status'] = "success"; 
            $return['message'] = "Lưu thông tin thành công!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;  
        }
        else
        {

            $cus_time = time();
            
            $DB->query("INSERT INTO `".root_table."customer` (`cus_time`, `cus_full_name`, `cus_address`, `cus_type`, `cus_phone`,   `cus_email`) VALUES ('".time()."','{$cus_fullname}','{$cus_address}','0','{$cus_phone}', '{$cus_email}')");
            $return['status'] = "success"; 
            $id = $DB->last_insert_id();
            $DB->query("UPDATE ".root_table."customer SET cus_code='CUS{$id}' WHERE cus_id='{$id}'");
            $return['message'] = "Lưu thông tin thành công!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;  
        }
        
        print json_encode($return);exit;
    }


    public function add_order()
    {
        global $CMS, $DB, $DBW;
        foreach ($CMS->input as $key => $value) 
        {
            $info[$key] = $value;
        }
        $CMS->class->language->load("order");
        // Info Khach hang
        $CMS->input['cus_fullname'] = $cus_fullname = urldecode($info["cus_fullname"]); 
        $CMS->input['cus_phone'] = $cus_phone = urldecode($info["cus_phone"]); 
        $CMS->input['cus_email'] = $cus_email = urldecode($info["cus_email"]); 
        $CMS->input['cus_address'] = $cus_address = urldecode($info["cus_address"]); 
        // Info order
        $CMS->input['store_id'] = $store_id = urldecode($info["store_id"]); 
        $CMS->input['ord_note'] = $ord_note = urldecode($info["ord_note"]); 
        $CMS->input['order_payment_method'] = $order_payment_method = urldecode($info["order_payment_method"]);  
        $CMS->input['account_id'] = $account_id = urldecode($info["account_id"]); 
        // Item
       $CMS->input['product_id'] = $product_id = json_decode(html_entity_decode($info["product_id"], ENT_COMPAT), true); 
        $CMS->input['product_name'] = $product_name = json_decode(html_entity_decode($info["product_name"], ENT_COMPAT), true); 
        $CMS->input['product_quantity'] = $product_quantity = json_decode(html_entity_decode($info["product_quantity"], ENT_COMPAT), true); 
        $CMS->input['product_price'] = $product_price = json_decode(html_entity_decode($info["product_price"], ENT_COMPAT), true); 
        
      	
			
        $cus_email = urldecode($info["cus_email"]); 
        $cus_id = $CMS->customer->getInfo($cus_email, 'cus_id');
        $CMS->input['cus_id'] =  $cus_id;
        if(intval($cus_id) == 0)
        {
            $DB->query("INSERT INTO `".root_table."customer` (`cus_time`, `cus_full_name`, `cus_address`, `cus_type`, `cus_phone`,   `cus_email`) VALUES ('".time()."','{$cus_fullname}','{$cus_address}','0','{$cus_phone}', '{$cus_email}')");
            $return['status'] = "success"; 
            $CMS->input['cus_id'] =  $cus_id = $id = $DB->last_insert_id();
            $DB->query("UPDATE ".root_table."customer SET cus_code='CUS{$id}' WHERE cus_id='{$id}'");
            
        }


        list($status, $data_return) = $CMS->order->add("",1);
       
        if($status == "success")
        {
           
            $return['status'] = "success"; 
            $return['message'] = ""; 
            $return['data'] = $data_return; 
            print json_encode($return);exit;
            
        }
        else
        {
            $return['status'] = "error"; 
            $return['message'] = $data_return; 
            $return['data'] = array(); 
            print json_encode($return);exit;  
        }
        print json_encode($return);exit;
    }
    

    
    public function get_store_list()
    {
        global $CMS, $DB, $DBW;

        foreach ($CMS->input as $key => $value) 
        {
            $info[$key] = $value;
        }

        $sql = $DB->query("SELECT store_id, store_name, store_phone, store_address FROM `".root_table."store` WHERE `store_deleted`=0  ORDER BY store_id DESC");

        $arr = [];
        if($DB->num_rows($sql) > 0)
        {
            while ($data = $DB->fetch_array($sql)) {
               array_push($arr,$data);
            }
            $return['status'] = "success"; 
            $return['message'] = ""; 
            $return['data'] = $arr; 
            print json_encode($return);exit;
            
        }
        else
        {
            $return['status'] = "error"; 
            $return['message'] = "Dữ liệu không tồn tại!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;  
        }
        print json_encode($return);exit;
    }    
    
    public function get_accounts_list()
    {
        global $CMS, $DB, $DBW;

        foreach ($CMS->input as $key => $value) 
        {
            $info[$key] = $value;
        }

        $account = $CMS->accounts->getAll(' accounts_status=1 AND ');
        if(count($account) > 0)
        {
            
            $return['status'] = "success"; 
            $return['message'] = ""; 
            $return['data'] = $account; 
            print json_encode($return);exit;
            
        }
        else
        {
            $return['status'] = "error"; 
            $return['message'] = "Dữ liệu không tồn tại!"; 
            $return['data'] = array(); 
            print json_encode($return);exit;  
        }
        print json_encode($return);exit;
    }   
}
?>