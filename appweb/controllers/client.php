<?php
namespace controller;

use core\ezy;

ezy::load_model('sites'); 
ezy::load_model('customer'); 
ezy::load_model('nhanhoa'); 
ezy::load_model('order'); 
class client {
    static public function auto_run() {
		global $CMS;
		if ($CMS->vars['is_login'] != 1) {
           header("location: {$CMS->vars['root_domain']}/login");
		   exit();
        }
		$_SESSION['url_referrer'] = '/client';
		$CMS->class->language->load("sites");
		$CMS->class->language->load("register");

		// SEO
        $CMS->lang['website_title'] = 'Thiết kế website bán hàng - doanh nghiệp - bất động sản giá rẻ';
        $CMS->lang['seo_description'] = 'Web4s - Dịch vụ thiết kế website bán hàng, website doanh nghiệp, website bất động sản giá rẻ, đạt chuẩn SEO, tích hợp quản lý sản phẩm, thanh toán online';
        $CMS->lang['seo_keyword'] = 'thiết kế website, website bán hàng, website doanh nghiệp, website bất động sản, website giá rẻ, seo website, website trọn gói';
        $CMS->lang['seo_author'] = 'Công ty TNHH phần mềm Nhân Hòa';
        
        $CMS->vars['website_title'] = $CMS->lang['website_title'];
        $CMS->vars['seo_description'] = $CMS->lang['seo_description'];
        $CMS->vars['seo_keyword'] = $CMS->lang['seo_keyword'];
        $CMS->vars['seo_author'] = $CMS->lang['seo_author'];

        $CMS->vars['og_title'] = $CMS->lang['website_title'];
        $CMS->vars['og_description'] = $CMS->lang['seo_description'];

        $CMS->vars['dc_title'] = $CMS->lang['website_title'];
        $CMS->vars['dc_description'] = $CMS->lang['seo_description'];
        $CMS->vars['dc_subject'] = $CMS->lang['website_title'];
        // END SEO

		switch (ezy::$act) {
			case 'change_info':
				self::changeInfo();
				break;
			case 'change_pass':
				self::changePass();
				break;
			case 'kich-hoat-web':
				self::active_web_step1();
				break;	
			case 'kich-hoat-web-step1do':
				self::active_web_step1do();
				break;	
			case 'kich-hoat-webstep2':
				self::active_web_step2();
				break;			
			case 'kich-hoat-web-step2do':
				self::active_web_step2do();
				break;	
            default:
                self::main();
				break;
        }
    }
	protected function main() {
		global $CMS, $tpl, $member;
		$tpl->sites = \models\sites::getSiteAll($member['cus_id']);
		echo ezy::html('main');
	}
	protected function changeInfo() {
		header("location: https://id.nhanhoa.com/usercp/edit_profile.html");
		exit();
		// global $CMS, $tpl, $member;
		// if ($CMS->input['request_method'] == 'post') {
			// if (empty($CMS->input['name']) || strlen($CMS->input['name']) > 50) {
				// $tpl->error[] = $CMS->lang['res_name_err'];
			// }
			// if (empty($CMS->input['phone']) || ! preg_match('/^[0-9]{1,11}$/', $CMS->input['phone'])) {
				// $tpl->error['phone'] = $CMS->lang['res_phone_err'];
			// }
			// if (empty($CMS->input['address']) || strlen($CMS->input['address']) > 200) {
				// $tpl->error['address'] = $CMS->lang['res_address_err'];
			// }
			// if (empty($tpl->error)) {
				// $data['cus_full_name'] = $CMS->input['name'];
				// $data['cus_phone'] = $CMS->input['phone'];
				// $data['cus_address'] = $CMS->input['address'];
				// if (\models\customer::editInfo($data)) {
					// $tpl->success = $CMS->lang['change_info_success'];
				// } else {
					// $tpl->error[] = $CMS->lang['change_info_error'];
				// }
			// }
		// }
		// $tpl->info = \models\customer::getInfo($member['cus_id']);
		// $tpl->info['cus_full_name'] = empty($tpl->info['cus_full_name']) ? '' : $tpl->info['cus_full_name'];
		// $tpl->info['cus_phone'] = empty($tpl->info['cus_phone']) || $tpl->info['cus_phone'] == 'N/A' ? '' : $tpl->info['cus_phone'];
		// $tpl->info['cus_address'] = empty($tpl->info['cus_address']) || $tpl->info['cus_address'] == 'N/A' ? '' : $tpl->info['cus_address'];
		// echo ezy::html('info');
	}
	protected function changePass() {
		global $CMS, $tpl, $member;
		if ($CMS->input['request_method'] == 'post') {
			if (! empty($CMS->input['pass']) && strlen($CMS->input['pass']) > 50) {
				$tpl->error[] = $CMS->lang['change_pass_pass_err'];
			}
			if (empty($CMS->input['pass_new']) || strlen($CMS->input['pass_new']) > 50) {
				$tpl->error[] = $CMS->lang['change_pass_new_err'];
			}
			if (empty($CMS->input['pass_confirm']) || strlen($CMS->input['pass_confirm']) > 50 || $CMS->input['pass_confirm'] != $CMS->input['pass_new']) {
				$tpl->error[] = $CMS->lang['change_pass_confirm_err'];
			}
			if (empty($tpl->error)) {
				$data['cus_new_password'] = MD5($CMS->input['pass_new']);
				$data['cus_password'] = MD5($CMS->input['pass']);
				$data['cus_email'] = $member['cus_email'];
				$api = \models\nhanhoa::apiUrl('update_password', $data);
				if ($api['status'] == 'success') {
					$tpl->success = $CMS->lang['change_nhanhoa_success'];
				} else {
					$tpl->error = array('nhanhoa' => $api['message']);
				}
			}
		}
		echo ezy::html('pass');
	}

	protected function active_web_step1() {
		global $CMS, $tpl, $member;
	 
		$site_code = $CMS->input['web'];
		$tpl->site_code = $site_code;
		$site_info =  \models\sites::getInfo($site_code);
		if(!is_array($site_info))
		{
			header("location: {$CMS->vars['root_domain']}/tai-khoan.html");
			exit();
		}
		if($site_info['cusweb_id'] != $member['cus_id'])
		{
			header("location: {$CMS->vars['root_domain']}/tai-khoan.html");
			exit();
		}
		 
 		
  

		$list_themes = \models\sites::list_themes();
		$tpl->themes = array();
		foreach ($list_themes as $key=>$theme) { 
			$template = $theme['theme_code'];
				$tpl->themes[$key]['theme_preview'] = $CMS->vars['root_domain'].'/themes/'.$template.'/preview.jpg';
				$tpl->themes[$key]['theme_code'] = $theme['theme_code'];
				$tpl->themes[$key]['theme_name'] = $theme['theme_name'];
		}


			// Get list package
		$tpl->data['list_package']  = \models\sites::list_package();
		echo ezy::html('active_web_step_1');
	}


	protected function active_web_step1do() {
		global $CMS, $tpl, $member;
	 	$cycle = intval($CMS->input['cycle']);
		$site_code = $CMS->input['site_code'];
		$private_domain = intval($CMS->input['private_domain']);
		$code_storename = trim($CMS->input['code_storename']);
 		$packet_id = intval($CMS->input['packet_id']);
		$tpl->site_code = $site_code;
		$site_info =  \models\sites::getInfo($site_code);
		 $check = true;
		unset($_SESSION['error_msg']);
		if(!is_array($site_info))
		{
			header("location: {$CMS->vars['root_domain']}/tai-khoan.html");
			exit();
		}
		if($site_info['cusweb_id'] != $member['cus_id'])
		{
			header("location: {$CMS->vars['root_domain']}/tai-khoan.html");
			exit();
		}
		 
 		
 	   if(empty($packet_id))
	   { 
	   	 $_SESSION['error_msg'] = "Vui lòng chọn gói dịch vụ!";
	   	 $check = false;
	   }
 
	   if(empty($cycle))
	   { 
	   	 $_SESSION['error_msg'] = "Vui lòng chọn tháng sử dụng!";
	   	 $check = false;
	   }

	   if(empty($code_storename))
	   { 
	   	 $_SESSION['error_msg'] = "Vui lòng nhập tên miền!";
	   	 $check = false;
	   }
 
	   if($private_domain == 1)
	   {
	   	  /* Check is domain */ 
			if($CMS->class->domain->filter_var_domain($code_storename) == false)
			{   
				$_SESSION['error_msg'] = "Tên miền không đúng định dạng!";
				$check = false;
			} 
			$reg_domain = $code_storename;
	   }

	   if($check == false)
	   {  
	   		// Redirect back step 1
	   		header("location: {$CMS->vars['root_domain']}/tai-khoan/kich-hoat-web/?web={$site_code}");	 
			exit;
	   }
	   else
	   {
	   		$_SESSION['cart']['site_code'] = $site_info['site_code'];
	   		$_SESSION['cart']['site_theme'] = $site_info['site_theme'];
	   		$_SESSION['cart']['packet_id'] =  $packet_id;
	    	$_SESSION['cart']['cycle'] = $cycle;
	    
	    	$_SESSION['cart']['private_domain'] = $private_domain;
	   		$_SESSION['cart']['code_storename'] = $reg_domain;
	   		// Check tai khoan đai ly - get price thought api nh
	   
	   		if($_SESSION['member']['cus_is_reseller'] == 1)
	   		{
	   			//print_r (  $_SESSION['cart']['cycle']);exit;
	   			$data['diskspace'] =  $_SESSION['cart']['packet_id'];
 				$data['time_rent'] =  $_SESSION['cart']['cycle'];
 				$data['cus_email'] =  "{$_SESSION['member']['cus_email']}";

	 			$res = \models\nhanhoa::api2("get_package_price",$data,"");
	 	 
 				if($res['status'] == "success")
 				{	
 					unset($_SESSION['price_reseller']  );
 					unset($_SESSION['cart']['price_total'] );
 					$_SESSION['price_reseller'] .= "<p style='text-align:center'>Thanh toán thông qua tài khoản đại lý để được hướng chiết khấu hấp dẫn.</p>";
 					$_SESSION['price_reseller']  .= "<p style='text-align:center' class='text-orange'>Số tiền phải thanh toán: ".$CMS->class->input->currency($res['price_total'])."</p>";			
 				}
	   		}
	   	
	   		$packed = \models\sites::package_info( $packet_id);
	   		$_SESSION['cart']['price'] = $packed['packet_price_'.$cycle];
	   	
	   		header("location: {$CMS->vars['root_domain']}/tai-khoan/kich-hoat-webstep2/");	 
			exit;
	   }	


 
	}


	protected function active_web_step2() {
		global $CMS, $tpl, $member;
	 	
	 	if(!isset($_SESSION['cart']))
	  	{
	  		header("location: {$CMS->vars['root_domain']}/tai-khoan.html");	 
			exit;
	  	}

		$site_code = $_SESSION['cart']['site_code'];
		$tpl->site_code = $site_code;
		$site_info =  \models\sites::getInfo($site_code);
		if(!is_array($site_info))
		{
			header("location: {$CMS->vars['root_domain']}/tai-khoan.html");
			exit();
		}
		if($site_info['cusweb_id'] != $member['cus_id'])
		{
			header("location: {$CMS->vars['root_domain']}/tai-khoan.html");
			exit();
		}
		 
 		
 		$tpl->data['cycle'] = $_SESSION['cart']['cycle'] ;
		if(isset($_SESSION['cart']['price_total']) AND $_SESSION['cart']['price_total'] != 0)
		{
			 
			$tpl->data['total_bk'] =$CMS->class->input->currency($_SESSION['cart']['price_total']);
		}
		else
		{
			$tpl->data['total'] = $_SESSION['cart']['cycle']  * $_SESSION['cart']['price'];
			$tpl->data['total_bk'] =$CMS->class->input->currency($tpl->data['total']);
		}
		echo ezy::html('active_web_step_2');
	}


	protected function active_web_step2do() {
		global $CMS, $tpl, $member;
	 	
	 	if(!isset($_SESSION['cart']))
	  	{
	  		header("location: {$CMS->vars['root_domain']}/register/webstep1");	 
			exit;
	  	}
	  	if($CMS->vars['is_login'] != 1)
	  	{
	  		header("location: {$CMS->vars['root_domain']}/register/webstep1");	 
			exit;
	  	}
	   	// Create order
 		$data['cus_email'] = $_SESSION['member']['cus_email'];
 		$data['cus_full_name'] = $_SESSION['member']['cus_full_name'];
 		$data['cus_phone'] = $_SESSION['member']['cus_phone'];
 		$data['cus_address'] = $_SESSION['member']['cus_address'];

 		$formdata['tplid'] =  $_SESSION['cart']['site_theme'];
 		$formdata['diskspace'] =  $_SESSION['cart']['packet_id'];
 		$formdata['time_rent'] =  $_SESSION['cart']['cycle'];
 		$formdata['web_domain'] =   $_SESSION['cart']['code_storename'];
 		$formdata['site_code'] =   $_SESSION['cart']['site_code'];
 		$formdata['is_paid'] = 0;
 		$_SESSION['cart']['payment_method'] = $formdata['payment_method'] = intval($CMS->input['payment_method']);

 		unset($_SESSION['reponse_apinh']);
 
 		if($CMS->input['payment_method'] == 8 )//NL
 		{ 
 			$redirect_link = \models\order::checkOutVn();

 			// redirect
        	header("location: {$redirect_link}");
        	exit;
       
 		}elseif($CMS->input['payment_method'] == 4 ) // Dai ly
 		{
 			$formdata['is_paid'] = 1;
 		}
 
 	 	$res = \models\nhanhoa::api2("register_eco_web",$data,$formdata);

 		$_SESSION['reponse_apinh'] = $res;
 	 
 		header("location: {$CMS->vars['root_domain']}/register/webstep3");	 
	}



}