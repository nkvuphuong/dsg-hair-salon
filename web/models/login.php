<?php
namespace models;

use core\ezy;
use lib\date;
use models\customer;

ezy::load_model("customer");

class login
{
	/**
	 * Log in
	 */

	static function log_do_in( $member )
	{
		global $CMS, $DB, $func;

        // Check token
        if(!\lib\security::check_token())
        {
            return self::createMsg("Please refresh page (F5) then try again.");
        }
		//if ( intval($CMS->input['remember']) == 1 )
		{
			$CMS->class->cookie->set_cookie("cususername", $member['cus_username'], 1);
			$CMS->class->cookie->set_cookie("cushash", $member["cus_password_bk"], 1); // md5($member["member_login_key"].$CMS->vars["security_key"])
		}
		$ip_address = $_SERVER['REMOTE_ADDR'];
		// Update IP address
		$DB->query("UPDATE ".forum_table."members SET ip_address='{$ip_address}'");
		
		// Create / update session
		$poss_session_id = "";
		
		if ( $cookie_id = $CMS->class->cookie->get_cookie('session_id') )
		{
			$poss_session_id = $CMS->class->cookie->get_cookie('session_id');
		}
		
		if ($poss_session_id)
		{
			$session_id = $poss_session_id;
			
			$DB->query("DELETE FROM ".forum_table."sessions WHERE ip_address='{$CMS->ip_address}' AND id <> '{$session_id}'");

			$DB->query("UPDATE ".forum_table."sessions SET member_name='{$member['cus_username']}', member_id='{$member['cus_id']}', running_time='". time() ."' WHERE id='{$session_id}'");
		}
		else
		{
			$session_id = md5( uniqid(microtime()) );
			
			$DB->query("DELETE FROM ".forum_table."sessions WHERE ip_address='{$ip_address}'");

			$DB->query("INSERT INTO ".forum_table."sessions (id, member_id, member_name, running_time, location, ip_address, browser, referer, in_error ) VALUES ('". $session_id  ."', '". intval($member['cus_id']) ."', '{$member['cus_username']}', '". time() ."', 'idx,,', '". substr($ip_address, 0, 50) ."', '". substr($CMS->class->filter->clean_value($_SERVER['HTTP_USER_AGENT']), 0, 50) ."', '{$CMS->vars['http_referer']}', 0)");		
		}	

		$CMS->class->cookie->set_cookie("session_id", $session_id, -1);

		return true;
	}
	
	/**
	 * Logout
	 * @return [type] [description]
	 */
	
	static function log_do_out()
	{
		global $CMS, $DB, $func, $member;
		
		list( $privacy, $loggedin ) = explode( '&', $member['login_anonymous'] );
		
		$DB->query("UPDATE ".forum_table."sessions SET member_name='', member_id='0', login_type='0', member_group='2' WHERE id='". $CMS->session_id ."'");
		$DB->query("UPDATE ".forum_table."members SET login_anonymous='{$privacy}&0', last_visit='".time()."', last_activity='".time()."' WHERE member_id='{$member['member_id']}'");
		
		$CMS->class->cookie->set_cookie( "cususername" , "0"  );
		$CMS->class->cookie->set_cookie( "cushash" , "0"  );
		$CMS->class->cookie->set_cookie( "session_id" , "0"  );
		$CMS->class->cookie->set_cookie( "PHPSESSID" , "0"  );

 		return true;
	}

	/**
	 * Check exist
	 */
	
	static public function log_check_exist( $name, $password = "" )
	{
		global $CMS, $DB;
		
		$DB->query("SELECT M.member_id, M.name, M.member_login_key, M.sc_password, M.members_pass_hash AS converge_pass_hash, M.members_pass_salt AS converge_pass_salt FROM ".forum_table."members AS M WHERE M.name='{$name}'");
		$member = $DB->fetch_array();
				
		return $member;
	}

	/**
	 * Check password
	 */
	
	static public function log_check_password( $input_password, $hash, $salt )
	{
		if ( self::converge_member( $input_password, $hash, $salt ) == true )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Check password hash
	 */
	
	static function converge_member ( $md5_once_password, $sql_password, $salt_password )
	{
		if ( ! $sql_password )
		{
			return FALSE;
		}
		
		if ( $sql_password == self::converge_passhash( $salt_password, $md5_once_password ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	static function converge_passhash( $salt, $md5_once_password )
	{ 
		return md5( md5( $salt ) . $md5_once_password );
	}

	/**
	 * Login first?
	 */
	static public function cus_login($cus_code, $input_password, $login_success = 0, $cus_type = 0)
	{
		global $DB, $CMS, $member; 

		// $url_fail = "{$CMS->vars['root_domain']}/login/";

		// ThamLV-Y2018M8D15
		$url_fail = \models\login::getReference();

		$member= customer::getInfo($cus_code);
 		if(!$member)
		{
			$_SESSION['error_msg'] = "Email does not exist!";
			if(isset($CMS->input['ajax']) && $CMS->input['ajax'] == 1)
			{
				$result['status'] = 'error';
				$result['msg'] = $_SESSION['error_msg'];
				unset($_SESSION['error_msg']);
				echo json_encode($result);
				exit;
			}
			else
			{
				header("location: {$url_fail}");
				exit;
			}
		}
		if (! customer::cus_check_password( $input_password, $member['cus_password'], $member["cus_token_key"] ) && $login_success == 0)
		{
			$_SESSION['error_msg'] = "Wrong password!";
			if(isset($CMS->input['ajax']) && $CMS->input['ajax'] == 1)
			{
				$result['status'] = 'error';
				$result['msg'] = $_SESSION['error_msg'];
				unset($_SESSION['error_msg']);
				echo json_encode($result);
				exit;
			}
			else
			{ 	 
				header("location: {$url_fail}");
				exit;
			}
		}
		 
		customer::cus_do_in();
		// if(isset($_SESSION['link_back']) && $_SESSION['link_back'])
		// {
		// 	$link = $_SESSION['link_back'];
		// }
		// else
		// {
		// 	$link = "{$CMS->vars['http_referer']}/";
		// }

		// ThamLV-Y2018M8D15
		$link = \models\login::getReference();

		$_SESSION['msg'] = "Login successful";
		if(isset($CMS->input['ajax']) && $CMS->input['ajax'] == 1)
		{
			$result['status'] = 'ok';
			$result['msg'] = "Login successful";
			$result['link'] = $link;
			echo json_encode($result);
			exit;
		}
		else
		{
			if($link != "")
			{
				$_SESSION['referer'] = $link;
			}
			else
			{
				$_SESSION['referer'] = "{$CMS->vars['http_referer']}";
			}

			echo ezy::render("page_transfer", "layouts");
			exit;
		}
		return true;	
	}

	/**
	 * Forgot password
	 * Input: $data['email']
	 */

	static public function forgot_password($data = array())
	{
		global $CMS;
		if(!is_array($data) || empty($data))
		{
			$data = $CMS->input;
		}

		if(!$data['email'] || !$CMS->class->input->is_email($data['email']))
		{
			$_SESSION['msg'] .= "{$CMS->lang['login_incomplete_email']}";
			return false;
		}
		$customer = customer::getInfo($data['email']);
		if(!$customer)
		{
			$_SESSION['msg'] = "Email does not exist";
			return false;
		}

		// Tạo password mới
		$cus_password = $data['cus_password'] = $data['cus_repassword'] = $CMS->class->random->password(9,1);
				
		customer::update_password( $customer['cus_id'], $data['cus_password'], $customer['cus_token_key'] );
		//Send mail
		$CMS->email->email_template = "forgot_password";
		$CMS->email->email_to = isset($customer['cus_email']) ? $customer['cus_email'] : '';
		$CMS->email->email_toname = isset($customer['cus_name']) && $customer['cus_name'] ? $customer['cus_name'] : $customer['cus_email'];
		$CMS->email->data['cus_name'] = $CMS->email->email_toname;
		$CMS->email->data['cus_email'] = $customer['cus_email'];
		$CMS->email->data['cus_password'] =  $data['cus_password'];
        $CMS->email->data['website_name'] =  $CMS->vars['website_title'];
		$CMS->email->quick_send(0,0);
		return $customer;
	}

	/**
	 * Check validating
	 */

	static function check_validating($cus_id, $validate_id)
	{
		global $CMS;

		$validate_info = $CMS->validating->get($validate_id, 'forgot_password');

		$customer = $CMS->customer->getInfo($cus_id);

		if(!$customer)
		{
			$_SESSION['msg'] .= $CMS->lang['login_wrong_username'].'<br />';
			return false;
		}

		if($customer['cus_status'] == 0)
		{
			$_SESSION['msg'] .= $CMS->lang['login_without_activate_error'].'<br />';
			return false;
		}
		elseif($customer['cus_status'] == 2)
		{
			$_SESSION['msg'] .= $CMS->lang['user_is_blocked'].'<br />';
			return false;
		}

		if(!$validate_info['result'] || $cus_id != $validate_info['module_id'])
		{
			$_SESSION['msg'] .= $CMS->lang['invalid_validate_info'].'<br />';
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Change password
	 */

	static public function change_password($data = array())
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
		return true;
	}

	/**
	 * Login facebook
	 */

	static public function login_fb()
	{
		global $CMS;

		$app_id = $CMS->vars['fb_app_id'];
		$app_secret = $CMS->vars['fb_app_secret'];
		$redirect_uri = urlencode("{$CMS->vars['root_domain']}/login/login_fb/");
		// Get code value
		$code = $CMS->input['code'];

		// Get access token info
		$facebook_access_token_uri = "https://graph.facebook.com/oauth/access_token?client_id=$app_id&redirect_uri=$redirect_uri&client_secret=$app_secret&code=$code";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $facebook_access_token_uri);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($ch);
		curl_close($ch);

		// Get access token
		$accesstoken = str_replace('access_token=', '', explode("&", $response)[0]);

		$accesstoken = json_decode($accesstoken,true);
		$access_token = $accesstoken['access_token'];

		if(!$access_token) return false;
 
		// Get user infomation
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/me?access_token=$access_token&fields=name,email");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($ch);
		curl_close($ch);

		if(!$response)
		{
			$_SESSION['msg'] = "Facebook login failed!";
			return false;
		}
		$user = json_decode($response,1);

		if(!$user['email'])
		{
			$_SESSION['msg'] = "Failed to retrieve email information";
			return false;
		}
		else
		{
			$cus = customer::getInfo($user['email']);
			
			if(!$cus)
			{
				//Register new
				$register_data = array(
					'cus_email' => $user['email'],
					'cus_full_name' => $user['name'],
					'cus_name' => $user['name'],
					'cus_password' => '',
					'cus_repassword' => '',
					'cus_status' => 1,
				);
				$send_mail_active = 0;
				$check_security = 0;
				$cus_id = customer::register_account($register_data, 1, $send_mail_active, $check_security);
				if(!$cus_id)
				{
					$_SESSION['msg'] = "Account does not exist in the system!";
					return false;
				}
				//Unset msg
				unset($_SESSION['msg']);
				//Login
			 
				if(isset($_SESSION['link_back']) AND $_SESSION['link_back'] != "")
				{}
				else
				{
					$_SESSION['link_back'] = "{$CMS->vars['root_domain']}/";
				}
				self::cus_login($cus_id, '', 1,0);
				return true;
			}
			 
			if(isset($_SESSION['link_back']) AND $_SESSION['link_back'] != "")
			{}
			else
			{
				$_SESSION['link_back'] = "{$CMS->vars['root_domain']}/";
			}
			//Login
			self::cus_login($user['email'], '', 1,0);
			return true;
		}
	}
	/**
	 * Login Google
	 */
	public function login_gg()
	{
		global $CMS, $DB, $member;
		//chạy qua hàm nay nếu tạo ra session['EMAIL'] = > thành công nhé
        $link_step2 = "{$CMS->vars['root_domain']}/login/login_gg/";
        $CMS->api->google->login_step2($link_step2);
         
        if(isset($_SESSION['google_data'])){
            $data['contact_email']  = $_SESSION['google_data']['email'];
            $data['first_name']     = $_SESSION['google_data']['given_name'];
            $data['last_name']      = $_SESSION['google_data']['family_name'];
            $data['name']           = $_SESSION['google_data']['given_name'];   
            // Check exits Email
            $sql = $DB->query("SELECT * FROM ".root_table."customer WHERE cus_email = '{$data['contact_email']}' AND cus_deleted = 0 LIMIT 1");
            if($DB->num_rows($sql) > 0)
            {
            	$cus = $DB->fetch_array($sql);
            	
				if(isset($_SESSION['link_back']) AND $_SESSION['link_back'] != "")
				{}
				else
				{
					$_SESSION['link_back'] = "{$CMS->vars['root_domain']}/";
				}
				
				self::cus_login($cus['cus_id'], '', 1,0);
                return true;
            }
            else
            {
                // Insert info to DB
                $customer = customer::social_add($data);
                if(is_array($customer))
                {
                  if(isset($_SESSION['link_back']) AND $_SESSION['link_back'] != "")
					{}
					else
					{
						$_SESSION['link_back'] = "{$CMS->vars['root_domain']}/";
					}
                   self::cus_login($customer['cus_id'], '', 1,0);
                   return true;
                }
            }   
        }
	}
	/**
	 * Login thought account DB
	 * @return [type] [description]
	 */
	static function login_do()
	{
		global $CMS;
		$cus_email = isset($CMS->input["cus_email"]) ? $CMS->input["cus_email"] : '';
        $cus_password =  $CMS->class->editor->input("cus_password");
        $_SESSION['is_remember'] = isset($CMS->input['remember']) && $CMS->input['remember'] == 'on' ? 1 : 0;

        $_SESSION['error_msg'] = isset($_SESSION['error_msg']) ? $_SESSION['error_msg'] : '';

        // Check Input
        if (empty($cus_email))
        {
            $_SESSION['error_msg'] .= "Please enter Email!<br />";

            if(isset($CMS->input['ajax']) && $CMS->input['ajax'] == 1)
			{
				$result['status'] = 'error';
				$result['msg'] = $_SESSION['error_msg'];
				unset($_SESSION['msg']);
				echo json_encode($result);
				exit;
			}

            // header("location: {$CMS->vars['root_domain']}/login/");exit;   

			// ThamLV-Y2018M8D15
            $http_referer = \models\login::getReference();
            header("location: {$http_referer}");exit;
        }

        if (empty($cus_password))
        {
            $_SESSION['error_msg'] .= "Please enter password!<br />";

            if(isset($CMS->input['ajax']) && $CMS->input['ajax'] == 1)
			{
				$result['status'] = 'error';
				$result['msg'] = $_SESSION['error_msg'];
				unset($_SESSION['msg']);
				echo json_encode($result);
				exit;
			}

            // header("location: {$CMS->vars['root_domain']}/login/");exit;   

			// ThamLV-Y2018M8D15
            $http_referer = \models\login::getReference();
            header("location: {$http_referer}");exit;
        }
        self::cus_login($cus_email, $cus_password);
        return true;
	}

	/**
	 * ThamLV-Y2018M8D15
	 * Get reference address
	 * @param type success: for login success, else failure
	 * @return website address
	 */
	static function getReference()
	{
		global $CMS;

		$output = "";

		// Inputs
		$link_back = '';
		if( !empty($_SESSION['link_back']) )
		{
			$link_back = $_SESSION['link_back'];
			$link_back = $CMS->class->filter->clean_value($link_back);
			$link_back = strtolower($link_back);
			$link_back = str_replace(array('http://', 'https://'), '', $link_back);
		}

		$link_referer = '';
		if( !empty($CMS->vars['http_referer']) )
		{
			$link_referer = $CMS->vars['http_referer'];
			// $link_referer = $CMS->class->filter->clean_value($link_referer); // not use this line because it used in core
			$link_referer = strtolower($link_referer);
			$link_referer = str_replace(array('http://', 'https://'), '', $link_referer);
		}

		$root_domain = str_replace(array('http://', 'https://'), '', $CMS->vars['root_domain']);
		$pattern = "/^{$root_domain}/i";

		if( $link_back AND preg_match($pattern, $link_back) )
		{
			$output = $_SESSION['link_back'];
		}
		else if( $link_referer AND preg_match($pattern, $link_referer) )
		{
			$output = $CMS->vars['http_referer'];
		} 
		else
		{
			$output = "{$CMS->vars['root_domain']}/login";
		}
		
		return $output;
	}
}