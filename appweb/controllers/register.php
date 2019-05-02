<?php
namespace controller;
use core\ezy;
ezy::load_model("sites"); 
ezy::load_model("customer"); 
ezy::load_model('nhanhoa'); 
ezy::load_model('order'); 
class register {
    static public function auto_run() {
		global $CMS;
 
		$CMS->class->language->load("register");
		$CMS->class->language->load("sites");
		
		// SEO
        $CMS->lang['website_title'] = 'Đăng ký website bán hàng - doanh nghiệp - bất động sản giá rẻ';
        $CMS->lang['seo_description'] = 'Web4s - Đăng ký dùng thử dịch vụ thiết kế website bán hàng, website doanh nghiệp, website bất động sản giá rẻ, đạt chuẩn SEO, tích hợp quản lý sản phẩm, thanh toán online';
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
			case 'test':
				self::test();
                break;
			case 'step2':
				self::step2();
                break;
            case 'webstep1':
				self::web_step_1();
            break;
            case 'webstep1do':
				self::web_step_1_do();
            break;
             case 'webstep2':
				self::web_step_2();
            break;
             case 'webstep2do':
				self::web_step_2_do();
            break;
            case 'webstep3':
				self::web_step_3();
            break;
            case 'cancelpayment':
				self::cancelpayment();
            break;
			case 'check_template_ajax':
				self::checkTemplateAjax();
                break;
			case 'check_email_ajax':
				self::checkEmailAjax();
                break;
			case 'thanh-cong.html':
			case 'success':
				self::success();
                break;
			case 'loi.html':
			case 'error':
				self::error();
                break;
			case 'buoc-3.html':
			case 'step3':
				self::step3();
                break;
			case 'buoc-2.html':
			case 'step2':
				self::step2();
                break;
			case 'account':
				self::account();
				break;
			case 'buoc-1.html':
			case 'step1':
            default:
                self::step1();
				break;
        }
    }
	protected function account() {
		global $CMS, $tpl, $member;
		if ($CMS->vars['is_login'] == 1) {
			$url = empty($_SESSION['url_referrer']) ? "{$CMS->vars['root_domain']}/client" : $_SESSION['url_referrer'];
			header("location: {$url}");
			exit();
		}
		
		if ($CMS->input['request_method'] == 'post') {
			if (! filter_var($CMS->input['email'], FILTER_VALIDATE_EMAIL)) {
				$tpl->error['email'] = $CMS->lang['res_email_err'];
			}
			if (empty($CMS->input['pass']) || strlen($CMS->input['pass']) > 150 || strlen($CMS->input['pass']) < 4) {
				$tpl->error['pass'] = $CMS->lang['res_pass_err'];
			}
			if ($CMS->input['pass'] != $CMS->input['confirm']) {
				$tpl->error['confirm'] = $CMS->lang['res_confirm_err'];
			}
			if (empty($CMS->input['name']) || strlen($CMS->input['name']) > 50) {
				$tpl->error['name'] = $CMS->lang['res_name_err'];
			}
			if (empty($CMS->input['address']) || strlen($CMS->input['address']) > 200) {
				$tpl->error['address'] = $CMS->lang['res_address_err'];
			}
			if (empty($CMS->input['phone']) || ! preg_match('/^[0-9]{7,12}$/', $CMS->input['phone'])) {
				$tpl->error['phone'] = $CMS->lang['res_phone_err'];
			}
			if (empty($tpl->error)) {
				$data['cus_email'] = trim($CMS->input['email']);
				$data['cus_password'] = MD5($CMS->input['pass']);
				$data['cus_name'] = trim($CMS->input['name']);
				$data['cus_address'] = trim($CMS->input['address']);
				$data['cus_phone'] = trim($CMS->input['phone']);
				$api = \models\nhanhoa::apiUrl('add_customer', $data);
				if ($api['status'] == 'success') {
					$tpl->success['nhanhoa'] = $CMS->lang['res_nhanhoa_success'];
					// $data['cus_email'] = $CMS->input['email'];
					// $data['cus_full_name'] = $api['customer']['cus_name'];
					// $data['cus_address'] = $api['customer']['cus_address'];
					// $data['cus_phone'] = $api['customer']['cus_phone'];
					// $data['cus_first_name'] = $api['customer']['cus_name'];
					// $data['cus_last_name'] = '';
					// $data['cus_is_reseller'] = $api['customer']['cus_is_reseller'];
					// $member = \models\customer::quickAdd($data);
					// if ($member) {
						// \models\customer::cusDoIn();
						// $url = empty($_SESSION['url_referrer']) ? "{$CMS->vars['root_domain']}/client" : $_SESSION['url_referrer'];
						// header("location: {$url}");
						// exit();
					// }
				} else {
					$tpl->error['email'] = $CMS->lang['res_nhanhoa_error'];
				}
			}
		}
		// Facebook & Google+ plugins
        if($CMS->vars['is_login'] == 0) {
            $tpl->fb_url = "";
            if ($CMS->vars['fb_app_id'] AND $CMS->vars['fb_app_secret']) {
				$redirect_uri = urlencode("{$CMS->vars['root_domain']}/login/facebook/");
                $tpl->fb_url = "https://www.facebook.com/dialog/oauth?client_id={$CMS->vars['fb_app_id']}&redirect_uri={$redirect_uri}&scope=email,public_profile";
            }
            $tpl->gg_url = "";
			if ($CMS->vars['gg_app_id'] AND $CMS->vars['gg_app_secret']) {
                $gg_redirect_uri =  "{$CMS->vars['root_domain']}/login/google/";
                $tpl->gg_url = $CMS->api->google->login($gg_redirect_uri);
            }
        }
		echo ezy::html('account');
	}
	protected function step1() {
		global $CMS, $tpl, $member;

		if ($CMS->vars['is_login'] == 1) {
			$customer = $member;
		} else {
			$CMS->input['email'] = empty($CMS->input['email']) ? $_SESSION['register']['email'] : $CMS->input['email'];
			$customer = \models\customer::getInfo($CMS->input['email']);
		}
			
		if ($CMS->vars['is_login'] == 1) {
			if (\models\sites::getSiteRegtype($customer['cus_id'])) {
				$_SESSION['res_site_error'] = $CMS->lang['res_site_error'];
				header("location: {$CMS->vars['root_domain']}/client");
				exit();
			} else {
				$CMS->input['email'] = empty($customer['cus_email']) ? trim($CMS->input['email']) : $customer['cus_email'];
				$CMS->input['name'] = empty($customer['cus_full_name']) ? trim($CMS->input['name']) : $customer['cus_full_name'];
				$CMS->input['address'] = empty($customer['cus_address']) || $customer['cus_address'] == 'N/A' ? trim($CMS->input['address']) : $customer['cus_address'];
				$CMS->input['phone'] = empty($customer['cus_phone']) || $customer['cus_phone'] == 'N/A' ? trim($CMS->input['phone']) : $customer['cus_phone'];
				$CMS->input['address'] = empty($customer['cus_address']) || $customer['cus_address'] == 'N/A' ? trim($CMS->input['address']) : $customer['cus_address'];
			}
		} else {
			if (\models\sites::getSiteRegtype($CMS->input['email'])) {
				$tpl->error['email'] = $CMS->lang['res_site_error'];
			} else {
				$data['cus_email'] = trim($CMS->input['email']); 
				$api = \models\nhanhoa::api('get_customer_info', $data);
				if ($api['result'] ==1) {
					$tpl->error['email'] = $CMS->lang['res_email_exist_err'];
				}
			}
		}
		if ($CMS->input['request_method'] == 'post') {

			$_SESSION['register']['template'] = intval($CMS->input['theme']);
			if (! filter_var($CMS->input['email'], FILTER_VALIDATE_EMAIL)) {
				$tpl->error['email'] = $CMS->lang['res_email_err'];
			}
			if ($CMS->vars['is_login'] == 0) {
				if (empty($CMS->input['pass']) || strlen($CMS->input['pass']) > 150 || strlen($CMS->input['pass']) < 4) {
					$tpl->error['pass'] = $CMS->lang['res_pass_err'];
				}
				if ($CMS->input['pass'] != $CMS->input['confirm']) {
					$tpl->error['confirm'] = $CMS->lang['res_confirm_err'];
				}
			}
			if (empty($CMS->input['name']) || strlen($CMS->input['name']) > 50) {
				$tpl->error['name'] = $CMS->lang['res_name_err'];
			}
			if (empty($CMS->input['address']) || strlen($CMS->input['address']) > 200) {
				$tpl->error['address'] = $CMS->lang['res_address_err'];
			}
			if (empty($CMS->input['phone']) || ! preg_match('/^[0-9]{7,12}$/', $CMS->input['phone'])) {
				$tpl->error['phone'] = $CMS->lang['res_phone_err'];
			}
			if (empty($_SESSION['register']['template'])) {
				$tpl->error['template'] = $CMS->lang['res_template_err'];
			}
			if (empty($tpl->error)) {
				$_SESSION['register']['nhanhoa'] = $CMS->lang['res_nhanhoa_success'];
				$_SESSION['register']['name'] = $CMS->input['name'];
				$_SESSION['register']['email'] = $CMS->input['email'];
				$_SESSION['register']['phone'] = $CMS->input['phone'];
				$_SESSION['register']['address'] = $CMS->input['address'];
				if ($CMS->vars['is_login'] == 0) {
					$_SESSION['register']['pass'] = $CMS->input['pass'];
				}
				header("location: {$CMS->vars['root_domain']}/register/step2");
				exit();
			}
		}
 		
 		// Overwrite data theme selected
 		if(isset( $CMS->input['theme']))
 		{
 			$_SESSION['register']['template'] = $CMS->input['theme'];
 		}
 		
		$list_themes = \models\sites::list_themes();
		$tpl->themes = array();
		foreach ($list_themes as $key=>$theme) { 
			$template = $theme['theme_code'];
				$tpl->themes[$key]['theme_preview'] = $CMS->vars['root_domain'].'/themes/'.$template.'/preview.jpg';
				$tpl->themes[$key]['theme_code'] = $theme['theme_code'];
				$tpl->themes[$key]['theme_name'] = $theme['theme_name'];
				$tpl->themes[$key]['theme_id'] = $theme['theme_id'];
		}
		 
		// config fb g+
		$CMS->api->facebook->api_key = $CMS->vars['fb_app_id'];
		$CMS->api->facebook->secret_key = $CMS->vars['fb_app_secret'];
		$CMS->api->google->clientId = $CMS->vars['gg_app_id'];
		$CMS->api->google->clientSecret = $CMS->vars['gg_app_secret'];
		// Facebook & Google+ plugins
        if($CMS->vars['is_login'] == 0) {
            $tpl->fb_url = "";
            if ($CMS->vars['fb_app_id'] AND $CMS->vars['fb_app_secret']) {
				$redirect_uri = urlencode("{$CMS->vars['root_domain']}/login/facebook/");
                $tpl->fb_url = "https://www.facebook.com/dialog/oauth?client_id={$CMS->vars['fb_app_id']}&redirect_uri={$redirect_uri}&scope=email,public_profile";
            }
            $tpl->gg_url = "";
			if ($CMS->vars['gg_app_id'] AND $CMS->vars['gg_app_secret']) {
                $gg_redirect_uri =  "{$CMS->vars['root_domain']}/login/google/";
                $tpl->gg_url = $CMS->api->google->login($gg_redirect_uri);
            }
        }
		$_SESSION['url_referrer'] = $CMS->vars['root_domain'] . '/register';
		echo ezy::html('step1');
	}
	protected function step2() {
		global $CMS, $tpl;
		if (! empty($CMS->input['id'])) {
			$id = intval($CMS->input['id']);
		
			if ($id > 0 && $id < 16) {
				$_SESSION['register']['industry'] = $id;
				header("location: {$CMS->vars['root_domain']}/register/step3");
				exit();
			}
		}
		$tpl->name = $_SESSION['register']['name'];
		echo ezy::html('step2');
	}
	protected function step3() {
		global $CMS, $tpl, $member;
	 
		if (empty($_SESSION['register']['template'])) {
			header("location: {$CMS->vars['root_domain']}/dang-ky-dung-thu-website.html");
			exit();
		}
		if (empty($_SESSION['register']['industry']) || $_SESSION['register']['industry'] < 0 || $_SESSION['register']['industry'] > 15) {
			header("location: {$CMS->vars['root_domain']}/register/step2");
			exit();
		}
		if (($CMS->input['request_method'] == 'post') || (! empty($_SESSION['register']['warehouse']) && $_SESSION['register']['warehouse'] == 1)) {
			// if (in_array($_SERVER['REMOTE_ADDR'], array('::1', '113.161.84.62'))) {
				// $tpl->error_no = 'E000';
				// $_SESSION['error_msg'] = 'test slack to ip:' . $_SERVER['REMOTE_ADDR'];
			// }

			if ($CMS->vars['is_login'] == 1) {
				$customer = $member;
			} else {
				$customer = \models\customer::getInfo($_SESSION['register']['email']);
			}	 
			if ($customer) {
				if (\models\sites::getSiteRegtype($customer['cus_id'])) {
					if ($CMS->vars['is_login'] == 1) {
						$_SESSION['res_site_error'] = $CMS->lang['res_site_error'];
						header("location: {$CMS->vars['root_domain']}/client");
						exit();
					} else {
						$tpl->error_no = 'E001';
						$_SESSION['error_msg'] = $CMS->lang['res_site_error'];
					}
				}
			}
			$data['cus_email'] = $_SESSION['register']['email']; 
			$api = \models\nhanhoa::api('get_customer_info', $data);
			if ($api['customer']['cus_email'] || $api['customer']['cus_phone'] || $api['customer']['cus_name'] || $api['customer']['cus_address']) {
				$data['cus_email'] = $_SESSION['register']['email'];
				$data['cus_phone'] = $_SESSION['register']['phone'];
				$data['cus_name'] = $_SESSION['register']['name'];
				$data['cus_address'] = $_SESSION['register']['address'];
				\models\nhanhoa::api('update_customer', $data);
			}
			if ($api['result'] != 1) {
				$data['cus_email'] = trim($_SESSION['register']['email']);
				$data['cus_password'] = MD5($_SESSION['register']['pass']);
				$data['cus_name'] = trim($_SESSION['register']['name']);
				$data['cus_address'] = trim($_SESSION['register']['address']);
				$data['cus_phone'] = trim($_SESSION['register']['phone']);
				$api = \models\nhanhoa::apiUrl('add_customer', $data);
				if ($api['status'] == 'success') {
				} else {
					$tpl->error_no = 'E002';
					$_SESSION['error_msg'] = $CMS->lang['res_nhanhoa_error'];
				}
			} 
			if (empty($tpl->error_no)) {
				// if (! $customer) {
					$data['cus_full_name'] = $_SESSION['register']['name'];
					$data['cus_email'] = $_SESSION['register']['email'];
					$data['cus_phone'] = $_SESSION['register']['phone'];
					$data['cus_address'] = $_SESSION['register']['address'];
					$data['cus_pass'] = $_SESSION['register']['pass'];
					$data['cus_first_name'] = $_SESSION['register']['name'];
					$data['cus_last_name'] = '';
					$customer = \models\customer::quickAdd($data);
				// }
				if ($customer) {
					$code = 'demo' . (intval(\models\sites::getMaxId()) + 1) . rand(0,9) . rand(0,9);
					$email = $CMS->class->validate->convertViToEn($customer['cus_full_name']).'@'.$CMS->vars['storename_domain'];
			 
					$CMS->input['site_regtype'] = '1';
					$CMS->input['storename'] = trim($code);
					$CMS->input['code_storename'] = trim($code);
					$CMS->input['site_domainname'] = '';
					$CMS->input['industry'] = trim($_SESSION['register']['industry']);
					$CMS->input['package_id'] = '1';
					$CMS->input['site_theme'] = trim($_SESSION['register']['template']);
					$CMS->input['default_language'] = trim($CMS->class->language->getDefaultLanguage());
					$CMS->input['site_start_time'] = trim($CMS->class->date->date_get('m/d/Y', time()));
					$CMS->input['cus_name'] = trim($customer['cus_full_name']);
					$CMS->input['cus_id'] = trim($customer['cus_id']);
					$CMS->input['cusweb_id'] = trim($customer['cus_id']);
					$CMS->input['firstname'] = empty($customer['cus_first_name']) ? trim($customer['cus_full_name']) : $customer['cus_first_name'];
					$CMS->input['lastname'] = empty($customer['cus_last_name']) ? '' : $customer['cus_last_name'];
					$CMS->input['phone'] = empty($customer['cus_phone']) ? '' : $customer['cus_phone'];
					$CMS->input['email'] = empty($customer['cus_email']) ? '' : $customer['cus_email'];
					$CMS->input['address'] = empty($customer['cus_address']) ? '' : $customer['cus_address'];
					$CMS->input['addon_warehouse'] = empty($_SESSION['register']['warehouse']) ? trim($CMS->input['warehouse']) : $_SESSION['register']['warehouse'];
					 
					if (\models\sites::add()) {
						header("location: {$CMS->vars['root_domain']}/register/success");
						exit();
					}
				} else {
					$tpl->error_no = 'E003';
					$_SESSION['error_msg'] = $CMS->lang['res_error'];
				}
			} else {
				$message = array(
					'email' => trim($_SESSION['register']['email']),
					'name' => trim($_SESSION['register']['name']),
					'phone' => trim($_SESSION['register']['phone']),
					'address' => trim($_SESSION['register']['address']),
					'error_no' => $tpl->error_no,
					'error_message' => $_SESSION['error_msg']
				);
				$CMS->api->slack->sendAlert($message);
				header("location: {$CMS->vars['root_domain']}/register/error");
				exit();
			}
		}
		$tpl->industry = $CMS->lang['industry_'.$_SESSION['register']['industry']];
		echo ezy::html('step3');
	}
	protected function success() {
		global $CMS, $tpl;
		echo ezy::html('success');
		unset($_SESSION['register']);
		unset($_SESSION['error_msg']);
		unset($_SESSION['success_msg']);
	}
	protected function error() {
		global $CMS, $tpl;
		echo ezy::html('error');
		unset($_SESSION['register']);
		unset($_SESSION['error_msg']);
		unset($_SESSION['success_msg']);
	}
	protected function checkEmailAjax() {
		global $CMS;
		$reponse = array('status' => 'error', 'message' => $CMS->lang['res_email_err']);
		if ($CMS->input['request_method'] == 'post') {
			if (filter_var($CMS->input['email'], FILTER_VALIDATE_EMAIL)) {
				if (\models\sites::getSiteRegtype($CMS->input['email'])) {
					$reponse['message'] = $CMS->lang['res_site_error'];
				} else {
					$data['cus_email'] = trim($CMS->input['email']); 
					$api = \models\nhanhoa::api('get_customer_info', $data);
					if ($api['result'] ==1) {
						$reponse['message'] = $CMS->lang['res_email_exist_err'];
					} else {
						$reponse['status'] = 'success';
						$reponse['message'] = '';
					}
				}
			}
		}
		exit(json_encode($reponse));
	}
	protected function checkTemplateAjax() {
		global $CMS;
		$reponse = array('status' => 'error', 'message' => $CMS->lang['res_template_err']);
		if ($CMS->input['request_method'] == 'post') {
			$dirs = array_filter(glob(root_path.'/themes/*'), 'is_dir');
			foreach ($dirs as $dir) { 
				$template = str_replace(root_path.'/themes/', '', $dir);
				if (file_exists($dir.'/preview.jpg')) {
					if ($template == $CMS->input['template']) {
						//$_SESSION['register']['template'] = $CMS->input['template'];
						$reponse['status'] = 'success';
						$reponse['message'] = $CMS->vars['root_domain'].'/themes/'.$CMS->input['template'].'/preview.jpg';
						break;
					}
				}
			}
		}
		exit(json_encode($reponse));
	}


	static public function web_step_1() {
		global $CMS, $tpl, $member;
	  
		if ($CMS->vars['is_login'] == 0) {
			//header("location: {$CMS->vars['root_domain']}/client");	 
			//exit;
		}  

 		 $tpl->packet_id = intval($CMS->input['packet_id']);

		 // List option theme
        $option_theme = "";
	   	$list_themes = \models\sites::list_themes();
		$tpl->themes = array();
		foreach ($list_themes as $key=>$theme) { 
			$template = $theme['theme_code'];
			if($template == $CMS->input['theme'])
			{
				$option_theme .= "<option selected theme='{$theme['theme_code']}' value='{$theme['theme_id']}'>{$theme['theme_name']}</option>";
			}else
			{
				$option_theme .= "<option theme='{$theme['theme_code']}' value='{$theme['theme_id']}'>{$theme['theme_name']}</option>";
			}

			
		}


	    $tpl->data['option_theme'] = $option_theme;

		// Get list package
		$tpl->data['list_package']  = \models\sites::list_package();
 
		echo ezy::html('webstep1');
	}


	static public function web_step_1_do() {
		global $CMS, $tpl, $member;
		$check = true; 
	   // Check input
	   $site_theme = intval($CMS->input['site_theme']);
	   $packet_id = intval($CMS->input['packet_id']);
	   $cycle = intval($CMS->input['cycle']);
	   $price = intval($CMS->input['price']);
	   $private_domain = intval($CMS->input['private_domain']);
	   $code_storename = trim($CMS->input['code_storename']);
 
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
	   else
	   {
	   		$reg_domain = $code_storename.".".$CMS->vars['storename_domain'];
	   }

	   // Check Reg doamin exits
	   
	   
	   if(empty($site_theme))
	   {
	   	 $_SESSION['error_msg'] = "Vui lòng chọn giao diện website";
	   	 $check = false;
	   }
 
	   if($check == false)
	   {
	   		// Redirect back step 1
	   		header("location: {$CMS->vars['root_domain']}/register/webstep1");	 
			exit;
	   }
	   else
	   {

	   		$_SESSION['cart']['site_theme'] = $site_theme;
	   		$_SESSION['cart']['packet_id'] =  $packet_id;
	    	$_SESSION['cart']['cycle'] = $cycle;
	    
	    	$_SESSION['cart']['private_domain'] = $private_domain;
	   		$_SESSION['cart']['code_storename'] = $reg_domain;
	   		// Check tai salonan đai ly - get price thought api nh
	   
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
	   	
	   		

	   		header("location: {$CMS->vars['root_domain']}/register/webstep2");	 
			exit;
	   }	

	}


	static public function web_step_2() {
		global $CMS, $tpl, $member;
	  //print_r ();exit;
	  //
	  	if(!isset($_SESSION['cart']))
	  	{
	  		header("location: {$CMS->vars['root_domain']}/register/webstep1");	 
			exit;
	  	}

	  	// Facebook & Google+ plugins
        if($CMS->vars['is_login'] == 0) {
            $tpl->fb_url = "";
			
            if ($CMS->vars['fb_app_id'] AND $CMS->vars['fb_app_secret']) {
                $redirect_uri = urlencode("{$CMS->vars['root_domain']}/login/facebook/");
                $tpl->fb_url = "https://www.facebook.com/dialog/oauth?client_id={$CMS->vars['fb_app_id']}&redirect_uri={$redirect_uri}&scope=email,public_profile";
            }
            $tpl->gg_url = "";
			if ($CMS->vars['gg_app_id'] AND $CMS->vars['gg_app_secret']) {
                $gg_redirect_uri =  "{$CMS->vars['root_domain']}/login/google/";
                $tpl->gg_url = $CMS->api->google->login($gg_redirect_uri);
            }
        }

        
	  	unset($_SESSION['url_referrer']);
		$_SESSION['url_referrer']=  $_SERVER['REQUEST_URI']; 
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
 
		echo ezy::html('webstep2');
	}


	static public function web_step_2_do() {
		global $CMS, $tpl, $member;
	  
	  //
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


 	static public function web_step_3() {
		global $CMS, $tpl, $member;
 		unset($_SESSION['cart']);
	  	unset($_SESSION['url_referrer']);
		$res = $_SESSION['reponse_apinh'];
		unset($_SESSION['reponse_apinh']);

		$tpl->data = $res;
	 
		echo ezy::html('webstep3');

	}


	static public function cancelpayment() {
		global $CMS, $tpl, $member;
 
		echo ezy::html('cancelpayment');

	}
	
	protected function test() {
		global $CMS, $tpl, $member;
		exit('test register');
	}

}