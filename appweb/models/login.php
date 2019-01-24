<?php
namespace models;

use core\ezy;
use lib\date;
use models\customer;

ezy::load_model('customer');

class login {
	public static function loginDo() {
		global $CMS;
		$cus_email = $CMS->input["email"];
        $cus_password =  $CMS->class->editor->input("pass");
        $_SESSION['is_remember'] = $CMS->input['remember'] == 'on' ? 1 : 0;

        if (empty($cus_email) || empty($cus_password)) {
			return false;
        }
		
        return self::cusLogin($cus_email, $cus_password);
	}
	
	public static function cusLogin($cus_code, $input_password, $login_success = 0, $cus_type = 0) {
		global $DB, $CMS, $member; 
		$member = customer::getInfo($cus_code);
		
 		if (! $member) {
			return false;
		}
		if (customer::cusCheckPassword($input_password, $member['cus_password'], $member['cus_token_key'] )) {
			customer::cusDoIn();
			return true;
		}
		return false;	
	}
	
	public static function facebook() {
		global $CMS;
		$redirect_uri = urlencode("{$CMS->vars['root_domain']}/login/facebook/");
		$facebook_access_token_uri = "https://graph.facebook.com/oauth/access_token?client_id={$CMS->vars['fb_app_id']}&redirect_uri={$redirect_uri}&client_secret={$CMS->vars['fb_app_secret']}&code={$CMS->input['code']}";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $facebook_access_token_uri);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($ch);
		curl_close($ch);
		
		$accesstoken = str_replace('access_token=', '', explode("&", $response)[0]);
		$accesstoken = json_decode($accesstoken,true);
		$access_token = $accesstoken['access_token'];

		if ($access_token) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/me?access_token={$access_token}&fields=name,email");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			$response = curl_exec($ch);
			curl_close($ch);

			if ($response) {
				$user = json_decode($response, 1);
				if (filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
					return $user['email'];
				}
			}
		}
		return false;
	}
	
	public static function google() {
		global $CMS;
        $CMS->api->google->login_step2("{$CMS->vars['root_domain']}/login/google/");
		if (filter_var($_SESSION['google_data']['email'], FILTER_VALIDATE_EMAIL)) {
			return $_SESSION['google_data']['email'];
		}
		return false;
	}
}