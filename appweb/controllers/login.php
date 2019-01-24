<?php
namespace controller;

use core\ezy;

ezy::load_model('customer'); 
ezy::load_model('nhanhoa'); 

class login {
    static public function auto_run() {
		global $CMS;

		if (ezy::$act != 'logout' && $CMS->vars['is_login'] == 1) {
			$url = empty($_SESSION['url_referrer']) ? "{$CMS->vars['root_domain']}/client" : $_SESSION['url_referrer'];
			header("location: {$url}");
			exit();
        }
		
		$CMS->class->language->load("login");

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

		// config fb g+
		$CMS->api->facebook->api_key = $CMS->vars['fb_app_id'];
		$CMS->api->facebook->secret_key = $CMS->vars['fb_app_secret'];
		$CMS->api->google->clientId = $CMS->vars['gg_app_id'];
		$CMS->api->google->clientSecret = $CMS->vars['gg_app_secret'];
		
		$tmp = explode('?', ezy::$act);
		switch ($tmp[0]) {
			case 'test':
				self::test();
				break;
			case 'facebook':
				self::facebook();
				break;
			case 'google':
				self::google();
				break;
			case 'logout':
				self::logOut();
				break;
			default:
                self::main();
				break;
        }
    }
	protected function main() {
		global $CMS, $tpl, $member;
		if ($CMS->input['request_method'] == 'post') {
			if ($CMS->input['referer'] == 1) {
				if (empty($CMS->input['email']) || ! filter_var($CMS->input['email'], FILTER_VALIDATE_EMAIL)) {
					$tpl->error['email'] = $CMS->lang['login_email_err'];
				}
				if (empty($tpl->error)) {
					$data['cus_email'] = $CMS->input['email'];
					$api = \models\nhanhoa::apiUrl('forgot_password', $data);
					if ($api['status'] == 'success') {
						$tpl->success = $CMS->lang['forgot_success'];
					} else {
						$tpl->error = $api['message'];
					}
				}
			} else {
				if (empty($CMS->input['email']) || ! filter_var($CMS->input['email'], FILTER_VALIDATE_EMAIL)) {
					$tpl->error['email'] = $CMS->lang['login_email_err'];
				}
				if (empty($CMS->input['pass']) || strlen($CMS->input['pass']) > 150) {
					$tpl->error['pass'] = $CMS->lang['login_pass_err'];
				}
				if (empty($tpl->error)) {
					$data['cus_email'] = trim($CMS->input['email']);
					$data['cus_password'] = md5($CMS->input['pass']);
					$api = \models\nhanhoa::api('login', $data); 
					if ($api['status'] == 'success') {
						$member = \models\customer::getInfo($api['customer']['cus_email']);

						if ($member) {
							$data['cus_is_reseller'] = $api['customer']['cus_is_reseller'];
							$member = \models\customer::quickUpdate($data);
						} else {
							$data['cus_email'] = trim($CMS->input['email']);
							$data['cus_full_name'] = $api['customer']['cus_name'];
							$data['cus_address'] = $api['customer']['cus_address'];
							$data['cus_phone'] = $api['customer']['cus_phone'];
							$data['cus_first_name'] = $api['customer']['cus_name'];
							$data['cus_last_name'] = '';
							$data['cus_is_reseller'] = $api['customer']['cus_is_reseller'];
							$member = \models\customer::quickAdd($data);

						}

						if ($member) {
							\models\customer::cusDoIn();

							$url = empty($_SESSION['url_referrer']) ? "{$CMS->vars['root_domain']}/client" : $_SESSION['url_referrer'];
							header("location: {$url}");
							exit();
						}
					} else {
						$tpl->error['login'] = $CMS->lang['login_error_' . $api['result']];
					}
				}
			}
			if (empty($tpl->success)) {
				$tpl->error = (empty($tpl->error)) ? array('login' => $CMS->lang['login_error_6']) : $tpl->error;
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
		echo ezy::html('main');
	}
	protected function logOut() {
        global $CMS, $DB, $member;
        \models\customer::logOut();
        
		$_SESSION['msg'] = "Account logout successful!";
        $_SESSION['referer'] = "{$CMS->vars['root_domain']}";
		
        exit(ezy::render('page_transfer', 'layouts'));
    }
	protected function facebook() {
		global $CMS, $DB, $member;
		$email = \models\login::facebook();
		if ($email) {
			$data['cus_email'] = $email; 
			$api = \models\nhanhoa::api('login_social', $data);
			$member = \models\customer::getInfo($email);
			if ($member) {
				$data['cus_is_reseller'] = $api['customer']['cus_is_reseller'];
				$member = \models\customer::quickUpdate($data);
			} else {
				$data['cus_email'] = $email;
				$data['cus_full_name'] = $api['customer']['cus_name'];
				$data['cus_address'] = $api['customer']['cus_address'];
				$data['cus_phone'] = $api['customer']['cus_phone'];
				$data['cus_first_name'] = $api['customer']['cus_name'];
				$data['cus_last_name'] = '';
				$data['cus_is_reseller'] = $api['customer']['cus_is_reseller'];
				$member = \models\customer::quickAdd($data);
			}
			if ($member) {
				\models\customer::cusDoIn();
			}
		}
		$_SESSION['login_social'] = empty($api['customer']['is_register']) ? false : true;
		$url = empty($_SESSION['url_referrer']) ? "{$CMS->vars['root_domain']}/client" : $_SESSION['url_referrer'];
		header("location: {$url}");
		exit();
    }
	protected function google() {
		global $CMS, $DB, $member;
		$email = \models\login::google();
		if ($email) {
			$data['cus_email'] = $email; 
			$api = \models\nhanhoa::api('login_social', $data);
			$member = \models\customer::getInfo($email);
			if ($member) {
				$data['cus_is_reseller'] = $api['customer']['cus_is_reseller'];
				$member = \models\customer::quickUpdate($data);
			} else {
				$data['cus_email'] = $email;
				$data['cus_full_name'] = $api['customer']['cus_name'];
				$data['cus_address'] = $api['customer']['cus_address'];
				$data['cus_phone'] = $api['customer']['cus_phone'];
				$data['cus_first_name'] = $api['customer']['cus_name'];
				$data['cus_last_name'] = '';
				$data['cus_is_reseller'] = $api['customer']['cus_is_reseller'];
				$member = \models\customer::quickAdd($data);
			}
			if ($member) {
				\models\customer::cusDoIn();
			}
		}
		$_SESSION['login_social'] = empty($api['customer']['is_register']) ? false : true;
		$url = empty($_SESSION['url_referrer']) ? "{$CMS->vars['root_domain']}/client" : $_SESSION['url_referrer'];
		header("location: {$url}");
		exit();
    }
	protected function test() {
		global $CMS, $tpl, $member;
		// $data['cus_email'] = 'lyhuuloi@gmail.com';
		// $data['cus_password'] = md5('123456789');
		// $api = \models\nhanhoa::api('login', $data);
		// dump($api);
		exit('test');
	}	
}

?>