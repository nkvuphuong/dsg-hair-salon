<?php

namespace models;

use core\ezy;
use lib\input;
use lib\page;
use models\customer;
use models\payment;
use models\product;

ezy::load_model("product");
ezy::load_model("payment");
ezy::load_model("customer");
class member
{
    /**
     * Get List contact
     * @return array
     */
    static public $sqlAdd="";

    static public function listOrderByMember($cus_id=0, $status="")
    {
        global $CMS, $DB;

        $paging = true;
        $limit = 10;
        $clause = $status!="" ? " AND ord_status='{$status}' " : "";
        $results = page::init("SELECT * FROM ".root_table."order WHERE cus_id='{$cus_id}' AND ord_deleted=0 {$clause} ORDER BY ord_time DESC", $limit, $paging, 'order');
        // Declare output
        $output = [];

        // Load data
        if($results)
        {
            foreach ( $results as $data )
            {
                $output[] = self::convertOrder($data);
            }
        }

        // Output
        return $output;
    }

    static function createMsg($message="",$status="error")
    {
        $msg = array("status" => "{$status}",
                "hidden" => "display: block",
                "message" => "{$message}");
        return $msg;
    }

    static function getInfoOrder($record_id=0, $field_return="*")
    {
        global $DB, $CMS;

        if($record_id and $field_return)
        {
            // Count field
            $countField = count(explode(",", $field_return));
            // sqladd
            $sql_add = self::$sqlAdd;
            //Query
            $data = $DB->fetch_data("SELECT {$field_return} FROM ".root_table."order WHERE ord_deleted=0 AND cus_id='{$_SESSION['member']['cus_id']}' AND ord_id='{$record_id}' {$sql_add} LIMIT 1",'order')[0];

            //Check return
            if($countField == 1 and $field_return != "*")
            {
                return $data[$field_return];
            }else
            {
                return $data;
            }
        }else
        {
            return false;
        }
    }

    static function getInfoCarriers($record_id=0, $field_return="*")
    {
        global $DB, $CMS;

        if($record_id and $field_return)
        {
            // Count field
            $countField = count(explode(",", $field_return));
            // sqladd
            $sql_add = self::$sqlAdd;
            //Query
            $data = $DB->fetch_data("SELECT {$field_return} FROM ".root_table."partner_delivery WHERE p_delivery_deleted=0 AND p_delivery_display=1 AND p_delivery_id='{$record_id}' {$sql_add} LIMIT 1",'partner_delivery')[0];

            //Check return
            if($countField == 1 and $field_return != "*")
            {
                return $data[$field_return];
            }else
            {
                return $data;
            }
        }else
        {
            return false;
        }
    }

    static function convertOrder($data=[])
    {
        global $CMS;
// p($data);exit;  
        $data['status'] = $CMS->lang['order_status_'.$data['ord_status']];
        $data['amount'] = $CMS->class->input->currency($data['ord_total']);
        $data['subtotal'] = $CMS->class->input->currency($data['ord_amount']);
        $data['ord_tax_show'] = $CMS->class->input->currency($data['ord_tax']);
        $data['ord_discount_show'] = $CMS->class->input->currency($data['ord_discount']);
        $data['payment_method_show'] = $CMS->lang['payment_method_'.$data['payment_method']];
        $data['ord_item'] = self::getOrderItem($data['ord_id']);
        // get info product from ord_item
        $data_ordi = [];
        foreach ($data['ord_item'] as $key => $ordi) {
            $ordi['product'] = product::getInfo($ordi['product_id']);
            $ordi['product'] = product::convertProduct($ordi['product']);
            $ordi['price_show'] = $CMS->class->input->currency($ordi['ordi_price']);
            $data_ordi[] = $ordi;
        }
        $data['ord_item'] = $data_ordi;
        $data['carriers'] = [];
        $data['link_carriers'] = "";
        // Infor shipping
        if($data['shipping_info'])
        {
            $shipping = json_decode($data['shipping_info'], 1);
            $data['carriers'] = self::getInfoCarriers($shipping['ship_deliver']);
            $data['link_carriers'] = $data['carriers']['p_delivery_website'];
        }
        return $data;
    }

    static function getOrderItem($ord_id=0)
    {
        global $CMS, $DB;

        $cus_id = $_SESSION['member']['cus_id'];
        if(!$ord_id or !$cus_id) {return false;}
        $data = $DB->fetch_data("SELECT *, count(ordi_id) as total_quantity FROM ".root_table."order_item WHERE ordi_deleted=0 AND cus_id='{$cus_id}' AND ord_id='{$ord_id}' GROUP BY product_id ORDER BY ordi_time ASC",'order_item');
        // p($data);exit;
        return $data;
    }

    /**
     * Change password
     */

    static public function updatePassword($data = array())
    {
        global $CMS;
        if(!is_array($data) || empty($data))
        {
            $data = $CMS->input;
        }
        //Check no-password , bypass check old password
        if($_SESSION['member']['cus_password'] != "")
        {
            if($data['old_password'] == "")
            {
                $_SESSION['error_msg'] = 'Please enter old password<br />';
                return false;
            }
        }
        
        $customer = customer::getInfo($_SESSION['member']['cus_id']);
        if(!$customer)
        { 
            $_SESSION['error_msg'] = 'Account does not exist<br />';
            return false;
        }
        //Check no-password , bypass check old password
        if($_SESSION['member']['cus_password'] != "")
        {
            // Check old password
            if (! customer::cus_check_password( $data['old_password'], $customer['cus_password'], $customer["cus_token_key"] ))
            {
                $_SESSION['error_msg'] = "Old password is not correct!";
                return false;
            }
        }
        //check input
        if(empty($data['password']) OR strlen($data['password']) < 6)
        {
            $_SESSION['error_msg'] = "Please enter a password. Password must be 6 characters<br />";
            return false;
        }

        if(empty($data['repassword']) OR strlen($data['repassword']) < 6)
        {
            $_SESSION['error_msg'] = "Please enter a password. Password must be 6 characters<br />";
            return false;
        }

        if($data['password'] != $data['repassword'])
        {
            $_SESSION['error_msg'] = "Passwords do not match<br />";
            return false;
        }
        //Excute update new password
        customer::update_password( $customer['cus_id'], $data['password'], $customer['cus_token_key'] );
        //Refresh merber info
        $customer = customer::getInfo($_SESSION['member']['cus_id']);
        $_SESSION['member'] = $customer;
        if($customer)
        {
            $_SESSION['msg'] = "Change password successful<br />";
        }
        return true;
    }

    static function updateProfiles()
    {
        global $CMS, $DB;

        $cus_id = $_SESSION['member']['cus_id'];
        $cus_first_name = $CMS->input['cus_first_name'];
        $cus_last_name = $CMS->input['cus_last_name'];
        $cus_full_name = $cus_first_name." ".$cus_last_name;
        $cus_sex = intval($CMS->input['cus_sex']);
        $cus_phone = $CMS->input['cus_phone'];
        $cus_birthday = $CMS->input['cus_birthday'];
        $cus_address = $CMS->input['cus_address'];
        $cus_city = intval($CMS->input['cus_city']);
        $cus_district = intval($CMS->input['cus_district']);
        $cus_zip = $CMS->input['cus_zip'];

        if(!$cus_first_name) {$_SESSION['error_msg'] = "Please enter your first name"; return false;}
        if(!$cus_last_name) {$_SESSION['error_msg'] = "Please enter your last name"; return false;}

        $check = $DB->query("UPDATE ".root_table."customer SET cus_first_name='{$cus_first_name}', cus_last_name='{$cus_last_name}', cus_sex='{$cus_sex}', cus_phone='{$cus_phone}', cus_birthday='{$cus_birthday}', cus_address='{$cus_address}', cus_city='{$cus_city}', cus_district='{$cus_district}', cus_zip='{$cus_zip}', cus_full_name='{$cus_full_name}' WHERE cus_id='{$cus_id}'");
        if($check)
        {
            $_SESSION['msg'] = "Update profile information successful"; return true;
        }else
        {
            $_SESSION['error_msg'] = "Error when you update information. Please try again or contact admin."; return false;
        }
    }

    static function listAddressBook($cus_id=0)
    {
        global $CMS, $DB;

        if(!$cus_id) {return false;}

        $paging = true;
        $limit = 10;
        $results = page::init("SELECT * FROM ".root_table."addressbook WHERE cus_id='{$cus_id}' AND addr_deleted=0 ORDER BY addr_time DESC", $limit, $paging, 'addressbook');
        // Declare output
        $output = [];
        // Load data
        if($results)
        {
            foreach ( $results as $data )
            {
                $output[] = $data = self::convertAddressbook($data);
            }
        }

        // Output
        return $output;
    }

    static function convertAddressbook($data=[])
    {
        global $CMS;

        $data['addr_city_name'] = $data['addr_city'] ? payment::getCityname($data['addr_city']) : "";
        $data['addr_district_name'] = $data['addr_district'] ? payment::getDistrictname($data['addr_district']) : "";

        return $data;
    }

    static function getAddressDefault($cus_id=0, $type=0)
    {
        global $CMS, $DB;

        $clause = $type==0 ? " AND addr_default_shipping=1 " : " AND addr_default_billing=1 ";
        
        $data = $DB->fetch_data("SELECT * FROM ".root_table."addressbook WHERE addr_deleted=0 AND cus_id='{$cus_id}' {$clause} LIMIT 1","addressbook")[0];
        return $data;
    }
    

    static function getInfoAddressFromToken($token='', $field_return="*")
{
    global $DB, $CMS;

    if($token and $field_return)
    {
        // Count field
        $countField = count(explode(",", $field_return));
        // sqladd
        $sql_add = self::$sqlAdd;
        //Query
        $data = $DB->fetch_data("SELECT {$field_return} FROM ".root_table."addressbook WHERE addr_deleted=0 AND addr_token_key='{$record_id}' {$sql_add} LIMIT 1",'addressbook')[0];

        //Check return
        if($countField == 1 and $field_return != "*")
        {
            return $data[$field_return];
        }else
        {
            return $data;
        }
    }else
    {
        return false;
    }
}

    static function getInfoAddress($record_id=0, $field_return="*")
    {
        global $DB, $CMS;

        if($record_id and $field_return)
        {
            // Count field
            $countField = count(explode(",", $field_return));
            // sqladd
            $sql_add = self::$sqlAdd;
            //Query
            $data = $DB->fetch_data("SELECT {$field_return} FROM ".root_table."addressbook WHERE addr_deleted=0 AND addr_id='{$record_id}' {$sql_add} LIMIT 1",'addressbook')[0];

            //Check return
            if($countField == 1 and $field_return != "*")
            {
                return $data[$field_return];
            }else
            {
                return $data;
            }
        }else
        {
            return false;
        }
    }

    static function editaddress($addr_id=0)
    {
        global $CMS, $DB;

        $cus_id = $_SESSION['member']['cus_id'];
        if(!$addr_id or !$cus_id) { return false;}
        $addr_first_name = $CMS->input['addr_first_name'];
        $addr_last_name = $CMS->input['addr_last_name'];
        $addr_full_name = $addr_first_name." ".$addr_last_name;
        $addr_phone = $CMS->input['addr_phone'];
        $addr_email = $CMS->input['addr_email'];
        $addr_address = $CMS->input['addr_address'];
        $addr_city = intval($CMS->input['addr_city']);
        $addr_district = intval($CMS->input['addr_district']);
        $addr_zipcode = $CMS->input['addr_zipcode'];


        if(!$addr_first_name) {$_SESSION['error_msg'] = "Please enter your first name"; return false;}
        if(!$addr_last_name) {$_SESSION['error_msg'] = "Please enter your last name"; return false;}

        // Check default address
        $addr_default_shipping = intval($CMS->input['addr_default_shipping']);
        if($addr_default_shipping == 1)
        {
            // Update all address default shipping = 0
            $DB->query("UPDATE ".root_table."addressbook SET addr_default_shipping=0 WHERE addr_deleted=0 AND cus_id='{$cus_id}' AND addr_default_shipping!=0");
        }

        $addr_default_billing = intval($CMS->input['addr_default_billing']);
        if($addr_default_billing == 1)
        {
            // Update all address default billing = 0
            $DB->query("UPDATE ".root_table."addressbook SET addr_default_billing=0 WHERE addr_deleted=0 AND cus_id='{$cus_id}' AND addr_default_billing!=0");
        }

        $check = $DB->query("UPDATE ".root_table."addressbook SET addr_first_name='{$addr_first_name}', addr_last_name='{$addr_last_name}', addr_phone='{$addr_phone}', addr_address='{$addr_address}', addr_city='{$addr_city}', addr_district='{$addr_district}', addr_zipcode='{$addr_zipcode}', addr_full_name='{$addr_full_name}', addr_email='{$addr_email}', addr_default_shipping='{$addr_default_shipping}', addr_default_billing='{$addr_default_billing}' WHERE addr_id='{$addr_id}' and cus_id='{$cus_id}'");
        if($check)
        {
            $_SESSION['msg'] = "Update address book information successful"; return true;
        }else
        {
            $_SESSION['error_msg'] = "Error when you update information. Please try again or contact admin."; return false;
        }
    }


    static function addaddress($cus_id=0)
    {
        global $CMS, $DB;
        if (!$cus_id){
            $cus_id = $_SESSION['member']['cus_id'];
        }
        if(!$cus_id) { return false;}
        $addr_first_name = $CMS->input['addr_first_name'];
        $addr_last_name = $CMS->input['addr_last_name'];
        $addr_full_name = $addr_first_name." ".$addr_last_name;
        $addr_company = $CMS->input['addr_company'];
        $addr_phone = $CMS->input['addr_phone'];
        $addr_email = $CMS->input['addr_email'];
        $addr_address = $CMS->input['addr_address'];
        $addr_address2 = $CMS->input['addr_address2'];
        $addr_city_text = $CMS->input['addr_city_text'];
        $addr_city = $CMS->input['addr_city'];

        $addr_district = intval($CMS->input['addr_district']);
        $addr_zipcode = $CMS->input['addr_zipcode'];

        if(!$addr_first_name) {$_SESSION['error_msg'] = "Please enter your first name"; return false;}
        if(!$addr_last_name) {$_SESSION['error_msg'] = "Please enter your last name"; return false;}

        // Check default address
        $addr_default_shipping = intval($CMS->input['addr_default_shipping']);
        if($addr_default_shipping == 1)
        {
            // Update all address default shipping = 0
            $DB->query("UPDATE ".root_table."addressbook SET addr_default_shipping=0 WHERE addr_deleted=0 AND cus_id='{$cus_id}' AND addr_default_shipping!=0");
        }

        $addr_default_billing = intval($CMS->input['addr_default_billing']);
        if($addr_default_billing == 1)
        {
            // Update all address default billing = 0
            $DB->query("UPDATE ".root_table."addressbook SET addr_default_billing=0 WHERE addr_deleted=0 AND cus_id='{$cus_id}' AND addr_default_billing!=0");
        }

        $check = $DB->query("INSERT INTO ".root_table."addressbook (addr_first_name, addr_last_name, addr_full_name, addr_phone, addr_address, addr_city, addr_district, addr_zipcode, addr_email, cus_id, addr_default_shipping, addr_default_billing) VALUES ('{$addr_first_name}', '{$addr_last_name}', '{$addr_full_name}', '{$addr_phone}', '{$addr_address}', '{$addr_city}', '{$addr_district}', '{$addr_zipcode}', '{$addr_email}', '{$cus_id}', '{$addr_default_shipping}', '{$addr_default_billing}')");
        if($check)
        {
            $_SESSION['msg'] = "Create address book information successful"; return true;
        }else
        {
            $_SESSION['error_msg'] = "Error when you create address book information. Please try again or contact admin."; return false;
        }
    }
    static public function addaddress_ajax($cus_id=0)
    {
        global $CMS, $DB;
        if (!$cus_id){
            $cus_id = $_SESSION['member']['cus_id'];
        }
        if(!$cus_id) { return false;}
        $addr_first_name = $CMS->input['addr_first_name'];
        $addr_last_name = $CMS->input['addr_last_name'];
        $addr_full_name = $addr_first_name." ".$addr_last_name;
        $addr_phone = $CMS->input['addr_phone'];
        $addr_email = $CMS->input['addr_email'];
        $addr_address = $CMS->input['addr_address'];
        $addr_city = $CMS->input['addr_city'];
        $addr_city_text = $CMS->input['addr_city_text'];
        $addr_district = intval($CMS->input['addr_district']);
        $addr_zipcode = $CMS->input['addr_zipcode'];

        if(!$addr_first_name) {$_SESSION['error_msg'] = "Please enter your first name"; return false;}
        if(!$addr_last_name) {$_SESSION['error_msg'] = "Please enter your last name"; return false;}
        $token_key = $CMS->class->random->md5($CMS->class->random->character(16));
        // Check default address
        $addr_default_shipping = intval($CMS->input['addr_default_shipping']);
        if($addr_default_shipping == 1)
        {
            // Update all address default shipping = 0
            $DB->query("UPDATE ".root_table."addressbook SET addr_default_shipping=0 WHERE addr_deleted=0 AND cus_id='{$cus_id}' AND addr_default_shipping!=0");
        }

        $addr_default_billing = intval($CMS->input['addr_default_billing']);
        if($addr_default_billing == 1)
        {
            // Update all address default billing = 0
            $DB->query("UPDATE ".root_table."addressbook SET addr_default_billing=0 WHERE addr_deleted=0 AND cus_id='{$cus_id}' AND addr_default_billing!=0");
        }

        $check = $DB->query("INSERT INTO ".root_table."addressbook (addr_first_name, addr_last_name, addr_full_name, addr_phone, addr_address, addr_city, addr_district, addr_zipcode, addr_email, cus_id, addr_default_shipping, addr_default_billing, addr_token_key) VALUES ('{$addr_first_name}', '{$addr_last_name}', '{$addr_full_name}', '{$addr_phone}', '{$addr_address}', '{$addr_city}', '{$addr_district}', '{$addr_zipcode}', '{$addr_email}', '{$cus_id}', '{$addr_default_shipping}', '{$addr_default_billing}','{$token_key}')");


        $inserted_id = self::getInfoAddressFromToken($token_key,'addr_id');
        if($check)
        {
            $_SESSION['msg'] = "Create address book information successful"; return true;
        }else
        {
            $_SESSION['error_msg'] = "Error when you create address book information. Please try again or contact admin."; return false;
        }
        return $inserted_id;
    }

    static function defaultshipping($addr_id=0)
    {
        global $CMS, $DB;

        $cus_id = $_SESSION['member']['cus_id'];
        if(!$addr_id or !$cus_id) { return false;}

        // Update all address default shipping = 0
        $DB->query("UPDATE ".root_table."addressbook SET addr_default_shipping=0 WHERE addr_deleted=0 AND cus_id='{$cus_id}' AND addr_default_shipping!=0");

        // Update default shipping = 0 by $addr_id
        $DB->query("UPDATE ".root_table."addressbook SET addr_default_shipping=1 WHERE addr_deleted=0 AND cus_id='{$cus_id}' AND addr_id='{$addr_id}'");
        $_SESSION['msg'] = "Change the shipping address book default information successfully"; return true;
        return true;
    }

    static function defaultbilling($addr_id=0)
    {
        global $CMS, $DB;

        $cus_id = $_SESSION['member']['cus_id'];
        if(!$addr_id or !$cus_id) { return false;}

        // Update all address default billing = 0
        $DB->query("UPDATE ".root_table."addressbook SET addr_default_billing=0 WHERE addr_deleted=0 AND cus_id='{$cus_id}' AND addr_default_billing!=0");

        // Update default billing = 0 by $addr_id
        $DB->query("UPDATE ".root_table."addressbook SET addr_default_billing=1 WHERE addr_deleted=0 AND cus_id='{$cus_id}' AND addr_id='{$addr_id}'");

        $_SESSION['msg'] = "Change the billing address book default information successfully"; return true;
        return true;
    }

    static function getShipBillAddress($ord_id=0, $cus_id=0)
    {
        global $CMS, $DB;

        if(!$cus_id) {return false;}

        $data = $DB->fetch_data("SELECT * FROM ".root_table."shipbill_address WHERE sp_deleted=0 AND cus_id='{$cus_id}' AND ord_id='{$ord_id}' LIMIT 1")[0];
        $data['bill_city_name'] = $data['bill_city'] ? payment::getCityname($data['bill_city']) : "";
        $data['bill_district'] = $data['bill_district'] ? payment::getDistrictname($data['bill_district']) : "";
        $data['ship_city_name'] = $data['ship_city'] ? payment::getCityname($data['ship_city']) : "";
        $data['ship_district'] = $data['ship_district'] ? payment::getDistrictname($data['ship_district']) : "";
        $data['sp_deliver_fee_show'] = $data['sp_deliver_fee'] ? $CMS->class->input->currency($data['sp_deliver_fee']) : "";
        return $data;
    }

    static function getBarProcess($order=[], $cus_id=0)
    {
        global $CMS, $DB;
        // Set tạm order ship_status
        // $order['ship_id'] = 2;
        if(!$cus_id) {return false;}
        $status_data = json_decode($CMS->vars['custom_status'], 1);//self::getListShipStatus();
        $status_curr = $status_data[$order['ord_status']];
        $number_step = (count($status_curr) > 0 && ($order['ord_status'] == 1 || $order['ord_status'] ==0)) ? 3 : 2;
        
        // calculator width
        $width = round(100/($number_step), 2);

        $arr = [];
        if($order['ord_status'] == 3)
        {
            $width = "50%";
            $arr[0]['is_active'] = "active";
            $arr[0]['width'] = $width;
            $arr[0]['caption'] = "Place order";
            $arr[0]['sub_caption'] = "";

            $arr[1]['is_active'] = "active";
            $arr[1]['width'] = $width;
            $arr[1]['caption'] = "Cancel order";
            $arr[1]['sub_caption'] = "";
        }else
        {
            $arr[0]['is_active'] = "active";
            $arr[0]['width'] = "{$width}%";
            $arr[0]['caption'] = "Place order";
            $arr[0]['sub_caption'] = "";

            if($order['ord_status'] == 2)
            {
                $arr[1]['is_active'] = "active";
                $arr[1]['width'] = "{$width}%";
                $arr[1]['caption'] = "Complete order";
                $arr[1]['sub_caption'] = "";
            }else
            {
                $arr[1]['is_active'] = "";
                $arr[1]['width'] = "{$width}%";
                foreach ($status_curr as $status) 
                {
                    if($order['ord_status_custom'] == $status)
                    {
                        $arr[1]['caption'] = $CMS->lang['title_custom_status_'.$status];
                    }
                }
               
                $arr[1]['caption'] = $arr[1]['caption'] ? $arr[1]['caption'] : $CMS->lang['order_status_'.$order['ord_status']];
                $arr[1]['sub_caption'] = "";

                $arr[2]['is_active'] = "";
                $arr[2]['width'] = "{$width}%";
                $arr[2]['caption'] = "Complete order";
                $arr[2]['sub_caption'] = "";
            }
            

            

            // Tạm thời đóng
            // $x=1;
            // foreach ($status as $data) 
            // {
            //     $arr[$x]['is_active'] = $x <= $order['ord_status_custom'] ? "active" : "";
            //     $arr[$x]['width'] = "{$width}%";
            //     $arr[$x]['caption'] = $data['status_name'];
            //     $x++;
            // }

            // $arr[$x]['is_active'] = $order['ord_status'] == 2 ? "active" : "";
            // $arr[$x]['width'] = "{$width}%";
            // $arr[$x]['caption'] = "Completed";
        }

        return $arr;
    }

    
    static function getListShipStatus()
    {
        global $CMS, $DB;

        $data = $DB->fetch_data("SELECT * FROM ".root_table."custom_status WHERE status_deleted=0 ORDER BY status_sort ASC");
        // p($data);exit;
        return $data;
    }

    static function getMessageOrder($ord_id=0)
    {
        global $CMS, $DB;

        $data = $DB->fetch_data("SELECT * FROM ".root_table."comment WHERE comment_deleted=0 AND comment_hide=0 AND module_name='order' AND module_id='{$ord_id}' ORDER BY comment_id DESC");
        // p($data);exit;
        return $data;
    }

}