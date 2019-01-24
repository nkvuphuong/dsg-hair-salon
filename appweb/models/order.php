<?php
namespace models;

use core\ezy;
use lib\date;
use models\customer;
use api\paypal;
ezy::load_model('customer');
ezy::load_model('nhanhoa'); 

class order {
	static function create_order($urlInfo = "") {
		global $CMS;
		 
		// // Create order
 		$data['cus_email'] = $_SESSION['member']['cus_email'];
 		$data['cus_full_name'] = $_SESSION['member']['cus_full_name'];
 		$data['cus_phone'] = $_SESSION['member']['cus_phone'];
 		$data['cus_address'] = $_SESSION['member']['cus_address'];

 		$formdata['tplid'] =  $_SESSION['cart']['site_theme'];
 		$formdata['diskspace'] =  $_SESSION['cart']['packet_id'];
 		$formdata['time_rent'] =  $_SESSION['cart']['cycle'];
 		$formdata['web_domain'] =   $_SESSION['cart']['code_storename'];
        $formdata['site_code'] =   $_SESSION['cart']['site_code'];
 		$formdata['cart_note'] =   "Thanh toan cong NganLuong: #".$urlInfo['payment_id'];
 		$formdata['is_paid'] = 1;
 		$formdata['payment_method'] = intval($_SESSION['cart']['payment_method']);

  
 		unset($_SESSION['reponse_apinh']);
 		$res = \models\nhanhoa::api2("register_eco_web",$data,$formdata);
 		$_SESSION['reponse_apinh'] = $res;
 		 
 		if($res['status'] == "success")
 		{
 			return true;
 		}
 		else
 		{
 			return false;
 		}
		 
		
	}
	

	static function checkOutVn()
    {
        global $CMS, $DB, $member;

        
        $_SESSION["msg"] = "";

      // http://eco.lo/payment/checksuccess?transaction_info=Thong+tin+giao+dich&order_code=NL_1501054082&price=2000&payment_id=30021052&payment_type=1&error_text=&secure_code=977a0306891823513ce956152a05e3e1&token_nl=10690459-6ec00e7c87636ce7feacbc51d0b5c776


            // Create session order
        
 
        // total de test tien
        if(!isset($_SESSION['cart']) OR count($_SESSION['cart']) == 0)
        {
            return false;
        }
        else
        {
            $data['cart_total'] =  $_SESSION['cart']['cycle']  * $_SESSION['cart']['price']; 
        }
  
        // Ngân lượng
        $receiver = $CMS->vars['nl_receiver_email'];
        //Mã đơn hàng 
        $order_code='NL_'.time();
        //Khai báo url trả về 
        $return_url= "{$CMS->vars['root_domain']}/payment/checksuccess";
        // Link nut hủy đơn hàng
        $cancel_url= "{$CMS->vars['root_domain']}/payment/cancel";  
        //Giá của cả giỏ hàng 
        $txh_name = $_SESSION['member']['cus_full_name'];  
        $txt_email =$_SESSION['member']['cus_email'];    
        $txt_phone =$_SESSION['member']['cus_phone'];    
        $price =(int)$data['cart_total'];     
     //   $price = 2000;
        //Thông tin giao dịch
        $transaction_info="Thong tin giao dich";
        $currency= "vnd";
        $quantity=1;
        $tax=0;
        $discount=0;
        $fee_cal=0;
        $fee_shipping=0;
        $order_description="Thanh toan don hang Web4s: ".$_SESSION['cart']['code_storename'];
        $buyer_info=$txh_name."*|*".$txt_email."*|*".$txt_phone;
        $affiliate_code="";
       
        // Load info ngan luong
        $CMS->api->nganluong->LoadMerchant();
        //Tạo link thanh toán đến nganluong.vn
        $redirect_link= $CMS->api->nganluong->buildCheckoutUrlExpand_2($return_url, $cancel_url, $receiver, $transaction_info, $order_code, $price, $currency, $quantity, $tax, $discount , $fee_cal,    $fee_shipping, $order_description, $buyer_info , $affiliate_code);

      
                  
        return $redirect_link;
    }

	 
}