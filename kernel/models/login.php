<?php

use core\ezy;
$CMS->login = new class_login;

class class_login{


	//===========================================================================
	//  Get cus info to check block or not
	//===========================================================================
	public function cus_login($cus_code, $input_password, $login_success = 0, $cus_type = 0)
	{
		global $DB, $CMS, $member;

 
	 
		$url_fail = "{$CMS->vars['root_domain']}/login/";
		 
 
		$member=$CMS->customer->getInfo($cus_code);

 		if(!$member)
		{
			$_SESSION['error_msg'] = "Email does not exist!";

			if(isset($CMS->input['ajax']) && $CMS->input['ajax'] == 1)
			{
				$result['status'] = 'error';
				$result['msg'] = $_SESSION['msg'];
				unset($_SESSION['msg']);

				echo json_encode($result); exit;
			}
			else
			{
				header("location: {$url_fail}");exit;
			 
			}
		}

		 
		if (!$CMS->customer->cus_check_password( $input_password, $member['cus_password'], $member["cus_token_key"] ) && $login_success == 0)
		{

			$_SESSION['error_msg'] = "Wrong password!";

			if(isset($CMS->input['ajax']) && $CMS->input['ajax'] == 1)
			{
				$result['status'] = 'error';
				$result['msg'] = $_SESSION['msg'];
				unset($_SESSION['msg']);

				echo json_encode($result); exit;
				
			}
			else
			{ 	 
				header("location: {$url_fail}");exit;


			}
		}

		 
		$CMS->customer->cus_do_in();

		 
		if($_SESSION['link_back'])
		{
			$link = $_SESSION['link_back'];
		}
		else
		{
			$link = "{$CMS->vars['root_domain']}/";
		} 

		if(isset($CMS->input['ajax']) && $CMS->input['ajax'] == 1)
		{
			$result['status'] = 'ok';
			$result['msg'] = "Đăng nhập tài salonản thành công!";
			$result['link'] = $link;

			echo json_encode($result); exit;
		}
		else
		{
			 
			$_SESSION['msg'] = "Login success!";
			if($link != "")
			{
				$_SESSION['referer'] = $link;
			}
			else
			{
				$_SESSION['referer'] = "{$CMS->vars['root_domain']}";
			}
			

			echo ezy::render("page_transfer", "layouts");exit;

			//header("location: {$link}");exit;
		}
		
		
	}



	//=======================================================================
	// LOG IN
	//=======================================================================
	function log_do_in( $member )
	{
		global $CMS, $DB, $func;

		// Delete cookie before
        $CMS->class->cookie->set_cookie( "cususername" , "0"  );
        $CMS->class->cookie->set_cookie( "cushash" , "0"  );
        $CMS->class->cookie->set_cookie( "session_id" , "0"  );
        $CMS->class->cookie->set_cookie( "PHPSESSID" , "0"  );
		
		//if ( intval($CMS->input['remember']) == 1 )
		{
			$CMS->class->cookie->set_cookie("cususername", $member['member_user'], 1);
			$CMS->class->cookie->set_cookie("cushash", $member["cus_password_bk"], 1); // md5($member["member_login_key"].$CMS->vars["security_key"]) 
			
		}
		
		$CMS->ip_address = $_SERVER['REMOTE_ADDR'];
		
		// Update IP address
		// $DB->query("UPDATE ".root_table."members SET ip_address='{$CMS->ip_address}'");
		
		// Create / update session
		$poss_session_id = "";
		
		if ( $cookie_id = $CMS->class->cookie->get_cookie('session_id') )
		{
			$poss_session_id = $CMS->class->cookie->get_cookie('session_id');
		}
		
		if ($poss_session_id)
		{
			$session_id = $poss_session_id;
			
			$DB->query("DELETE FROM ".root_table."sessions WHERE session_ip_address='{$CMS->ip_address}' AND session_id <> '{$session_id}'");

			$DB->query("UPDATE ".root_table."sessions SET member_name='{$member['member_name']}', member_id='{$member['membere_id']}', running_time='". time() ."' WHERE session_id = '{$session_id}'");
		}
		else
		{
			$session_id = md5( uniqid(microtime()) );
			
			$DB->query("DELETE FROM ".root_table."sessions WHERE session_ip_address='{$CMS->ip_address}'");

			$DB->query("INSERT INTO ".root_table."sessions (
							session_id, 
							member_id, 
							member_name, 
							running_time, 
							session_ip_address, 
							session_browser, 
							session_referer 
							) VALUES (
							'". $session_id  ."', 
							'". intval($member['membere_id']) ."', 
							'{$member['member_name']}', 
							'". time() ."', 
							'". substr($CMS->ip_address, 0, 50) ."', 
							'". substr($CMS->class->filter->clean_value($_SERVER['HTTP_USER_AGENT']), 0, 50) ."', 
							'{$CMS->vars['http_referer']}')");		
		}
		

		$CMS->class->cookie->set_cookie("session_id", $session_id, -1);
	}
	
	//=======================================================================
	// LOG OUT
	//=======================================================================
	function log_do_out()
	{
		global $CMS, $DB, $func, $member;
		
		list( $privacy, $loggedin ) = explode( '&', $member['login_anonymous'] );
		
		$DB->query("UPDATE ".root_table."sessions SET member_name='', member_id='0', session_type='0' WHERE session_id='". $CMS->session_id ."'");
		// $DB->query("UPDATE ".root_table."members SET login_anonymous='{$privacy}&0', last_visit='".time()."', last_activity='".time()."' WHERE member_id='{$member['member_id']}'");
		
		$CMS->class->cookie->set_cookie( "cususername" , "0"  );
		$CMS->class->cookie->set_cookie( "cushash" , "0"  );
		$CMS->class->cookie->set_cookie( "session_id" , "0"  );
		$CMS->class->cookie->set_cookie( "PHPSESSID" , "0"  );
		
		//$CMS->class->cookie->set_cookie( "member_id" , "0"  );
		//$CMS->class->cookie->set_cookie( "pass_hash" , "0"  );
		//$CMS->class->cookie->set_cookie( "anonlogin" , "-1"  );
		//$CMS->class->cookie->set_cookie( "is_secpass" , "0"  );
	}
	
	public function log_check_exist( $username, $password = "" )
	{
		global $CMS, $DB, $member;
		
		// $sql = "SELECT M.member_id, M.name, M.member_login_key, M.sc_password, M.members_pass_hash AS converge_pass_hash, M.members_pass_salt AS converge_pass_salt FROM ".root_table."members AS M WHERE M.name='{$name}'";

		$sql = "SELECT * FROM ".root_table."member WHERE member_user = '{$username}' AND member_password = '{$password}' AND member_deleted = 0 AND member_status = 1 AND member_is_authenticated = 1 AND member_active = 1";
		// print $sql;exit;
		$DB->query( $sql );
		$member = $DB->fetch_array();
				
		return $member;
	}
	
	public function log_check_password( $input_password, $hash, $salt )
	{
		if ( $this->converge_member( $input_password, $hash, $salt ) == true ) 
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function converge_member ( $md5_once_password, $sql_password, $salt_password )
	{
		if ( ! $sql_password )
		{
			return FALSE;
		}
		
		if ( $sql_password == $this->converge_passhash( $salt_password, $md5_once_password ) ) 
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	function converge_passhash( $salt, $md5_once_password )
	{//echo md5( md5( $salt ) . $md5_once_password );exit;
		return md5( md5( $salt ) . $md5_once_password );
	}
	public function change(){
		global $CMS, $DB, $member;
		$change_old=$CMS->input['change_old'];
		$change_pass=$CMS->input['change_pass'];
		$change_repass=$CMS->input['change_repass'];
		
		if (strlen($change_pass)<6) {
			$CMS->errormsg="{$CMS->lang['change_err_pass']}";
			return FALSE;
		}
		if ($change_pass!=$change_repass) {
			$CMS->errormsg="{$CMS->lang['change_err_conpass']}";
			return FALSE;
		}
		if ($CMS->customer->cus_check_password($change_old,$member['cus_password'],$member['cus_token_key']) == false) {
			$CMS->errormsg = "{$CMS->lang['login_wrong_password']}";
			return false;
		}
		if ($CMS->customer->update_password($member['cus_id'],$change_pass,$member['cus_token_key'])) {
			$CMS->class->cache->delete('cus_'.$member['cus_username']);
			$_SESSION['msg']="{$CMS->lang['verify_complete']}";
			return TRUE;
		}
		return FALSE;
	}
	public function forgot(){
		global $CMS, $DB, $member;

		if(!$CMS->input['for_email'] || !$CMS->class->input->is_email($CMS->input['for_email']))
		{
			$CMS->errormsg = "{$CMS->lang['login_incomplete_email']}";
			return false;
		}

		$customer = $CMS->customer->getInfo($CMS->input['for_email']);

		if(!$customer)
		{
			$CMS->errormsg = "{$CMS->lang['login_wrong_email']}";
			return false;
		}

		if($customer['cus_status']==0)
		{
			$CMS->errormsg = "{$CMS->lang['login_without_activate_error']}";
			return false;
		}

		if($customer['cus_status']==2)
		{
			$CMS->errormsg = "{$CMS->lang['login_locked_username']}";
			return false;
		}
 		
		
		// $data['cus_password'] = $data['cus_repassword'] = $CMS->class->random->character(9);
		// $CMS->customer->update_password( $customer['cus_id'], $data['cus_password'], $customer['cus_token_key'] );
		// $CMS->class->cache->delete('cus_'.$customer['cus_username']);
		
		//Send mail
		$CMS->email->email_template = $CMS->vars['email_forgot'];

		$CMS->email->email_to = $customer['cus_email'];
		$CMS->email->email_toname = $customer['cus_name'] ? $customer['cus_name'] : $customer['cus_email'];

		$CMS->email->data['cus_name'] = $CMS->email->email_toname;
		$CMS->email->data['cus_email'] = $customer['cus_email'];
		$CMS->email->data['cus_password'] =  $data['cus_password'];
		$CMS->email->data['cus_link_pass'] = $CMS->vars['root_domain'].'/forgot-password-'.$CMS->validating->insert($customer['cus_id'],'forgot_password').'.html';
		$CMS->email->quick_send_2();
		
		// $CMS->errormsg = "{$CMS->lang['forgot_sent']}";
		$_SESSION['msg']= "{$CMS->lang['forgot_sent']}";
		return TRUE;
	}
	public function check_validating($id=null){
		if(!is_null($id)) {
			global $CMS, $DB, $member;
			return (bool)$CMS->validating->get($id,'forgot_password')['result'];
		}
		return false;
	}
	public function do_validating() {
		global $CMS, $DB, $member;
		$change_pass=$CMS->input['change_pass'];
		$change_repass=$CMS->input['change_repass'];
		
		if (strlen($change_pass)<6) {
			$CMS->errormsg="{$CMS->lang['change_err_pass']}";
			return FALSE;
		}
		if ($change_pass!=$change_repass) {
			$CMS->errormsg="{$CMS->lang['change_err_conpass']}";
			return FALSE;
		}
		$valida=$CMS->validating->get($CMS->input['validate_id'],'forgot_password');
		$customer = $CMS->customer->getInfo($valida['module_id']);
		if ($CMS->customer->update_password($customer['cus_id'],$change_pass,$customer['cus_token_key'])) {
			$CMS->class->cache->delete('cus_'.$customer['cus_username']);
			$CMS->validating->delete($valida['module_id'],'forgot_password');
			$_SESSION['msg']="{$CMS->lang['verify_complete']}";
			return TRUE;
		}
		return FALSE;
	}
	public function logIn($email=null,$pass=null) {
		if (!empty($email) && !empty($pass)) {
			global $CMS, $DB, $member;
			unset($_SESSION['username']);
			// Load User
			$member = $CMS->customer->cus_check_exist($email);		
			// Check Input
			if ($CMS->vars['is_reseller']==1 AND $member['cus_type']!=2) {	
				$CMS->errormsg = "{$CMS->lang['error_login_reseller']}";
				return false;
			}
			// Check Customer
			if ($CMS->vars['is_reseller']==0 AND $member['cus_type']!=0) {
				$CMS->errormsg = "{$CMS->lang['error_login_customer']}"; 
				return false; 
			}
			// Continue check Input
			if (!$member) {
				$CMS->errormsg = "{$CMS->lang['login_wrong_username']}";
				return false;
			}
			if (!$CMS->customer->cus_check_password($pass,$member['cus_password'],$member['cus_token_key'])) {
				$CMS->errormsg = "{$CMS->lang['login_wrong_password']}";
				return false; 
			}
			if ($member['cus_status']!=1) {
				$CMS->errormsg = "{$CMS->lang['login_without_activate_error']}";
				return FALSE;
			}
			// Log in
			$CMS->customer->cus_do_in($member);
			// Message
			$CMS->global->page_transfer($CMS->lang['login_msg'], $CMS->vars['sell_domain']);
		}
		return FALSE;
	}
	 
	public function login_g() {
		global $CMS,$DB,$member;
		//include_once("{$CMS->vars['api_url']}/Google/Google_Client.php");
		//include_once("{$CMS->vars['api_url']}/Google/contrib/Google_Oauth2Service.php");

        // Load Google via composer
        require_once root_path."vendor/autoload.php";

        $redirectUrl="{$CMS->vars['root_domain']}/?site=login&act=login_g";  //return url (url to script)
		$gClient = new Google_Client();
		$gClient->setApplicationName('Login to mrt.3f.design');
		$gClient->setClientId($CMS->vars['client_id_g']);
		$gClient->setClientSecret($CMS->vars['client_secret_g']);
		$gClient->setRedirectUri($redirectUrl);
	
		$google_oauthV2 = new Google_Oauth2Service($gClient);
		if(isset($_REQUEST['code'])){
			$gClient->authenticate();
			$_SESSION['token'] = $gClient->getAccessToken();
			header('Location: ' . filter_var($redirectUrl, FILTER_SANITIZE_URL));
		}

		if (isset($_SESSION['token'])) {
			$gClient->setAccessToken($_SESSION['token']);
		}

		if ($gClient->getAccessToken()) {
			$user=$google_oauthV2->userinfo->get();
			$DB->query("SELECT `cus_id` FROM `".root_table."customer` WHERE `cus_username`='{$user['email']}'");
			if ($DB->num_rows()<1) {
				$data=array('cus_username'=>$user['email'],
							'cus_email'=>$user['email'],
							'cus_password'=>'123456',
							'cus_repassword'=>'123456',
							'cus_phone'=>'0909123456',
							'country_id'=>'0',
							'city_id'=>'0',
							'district_id'=>'0',
							'town_id'=>'0',
							'cus_status'=>'1'); //1:active
				$CMS->customer->folderTmp($CMS->customer->add($data));
			}
			$CMS->class->cache->delete('cus_'.$user['email']);
			$member=$CMS->customer->cus_check_exist($user['email']);

			if ( $CMS->vars['is_reseller'] == 1 AND $member['cus_type'] != 2 ) {
				$_SESSION['msg']="{$CMS->lang['error_login_reseller']}";
				$CMS->global->redirect("{$CMS->vars['root_domain']}/login.html");
			}
			if ( $CMS->vars['is_reseller'] == 0 AND $member['cus_type'] != 0 ) {
				$_SESSION['msg']="{$CMS->lang['error_login_customer']}";
				$CMS->global->redirect("{$CMS->vars['root_domain']}/login.html");
			}
			if (!$member) {
				$_SESSION['msg']="{$CMS->lang['login_wrong_username']}";
				$CMS->global->redirect("{$CMS->vars['root_domain']}/login.html");
			}
			if ($member['cus_status']!=1) {
				$_SESSION['msg']="{$CMS->lang['login_without_activate_error']}";
				$CMS->global->redirect("{$CMS->vars['root_domain']}/login.html");
			}
			//TRUE
			$CMS->customer->cus_do_in();
			$CMS->global->page_transfer($CMS->lang['login_msg'], $CMS->vars['sell_domain']);
		} else {
			$authUrl = $gClient->createAuthUrl();
		}
		if(isset($authUrl)) {
			header("Location: {$authUrl}");
		}
	}

	public function login_do()
	{
		global $CMS;
		$cus_email = $CMS->input["cus_email"];
        $_SESSION['link_back'] = "{$CMS->vars['root_domain']}";
        $cus_password =  $CMS->class->editor->input("cus_password");
        $CMS->customer->is_remember = $CMS->input['remember'] == 'on' ? 1 : 0;

        // Check Input
        if (empty($cus_email))
        {
            $_SESSION['msg'] .= "Please enter Email!<br />";
            header("location: {$CMS->vars['root_domain']}/login/");exit;
           
        }

        if (empty($cus_password))
        {
            $_SESSION['msg'] .= "Please enter password!<br />";
            header("location: {$CMS->vars['root_domain']}/login/");exit;   
        }

        $CMS->login->cus_login($cus_email, $cus_password);
	}

	public function login_fb()
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
 
			$cus = $CMS->customer->getInfo($user['email']);

			if(!$cus)
			{
				//Register new

				$random_pw = $CMS->class->random->password(9);
 
				$register_data = array(
					'cus_email' => $user['email'],
					'cus_full_name' => $user['name'],
					'cus_name' => $user['name'],
					'cus_password' => $random_pw,
					'cus_repassword' => $random_pw,
					'cus_status' => 1,
				);

				$send_mail_active = 0;
				$check_security = 0;
				$cus_id = $CMS->customer->register_account($register_data, '', $send_mail_active, $check_security);
 
				if(!$cus_id)
				{
					$_SESSION['msg'] = "Account does not exist in the system!";
					return false;
				}

				//Unset msg
				unset($_SESSION['msg']);

				//Send mail
				$CMS->email->email_template = "register_by_login_social";

				$CMS->email->email_to = $user['email'];
				$CMS->email->email_toname = $user['name'];

				$CMS->email->data['cus_name'] = $user['name'];
				$CMS->email->data['username'] = $user['email'];
				$CMS->email->data['password'] = $random_pw;
				$CMS->email->data['website'] = $CMS->vars['root_domain'];
				$CMS->email->quick_send(0,0);

 
				//Login
				$this->cus_login($cus_id, $random_pw, 0,0);
				return true;
			}
		 
			//Login
			$this->cus_login($user['email'], '', 1,0);
			return true;
		}
	}


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
            
            $sql = $DB->query("SELECT * FROM ".root_table."customer WHERE cus_email = '{$data['contact_email']}' AND cus_deleted = 0 ");
            if($DB->num_rows($sql) > 0)
            {
                $CMS->customer->social_login($data,1);
            }
            else
            {
                // Insert info to DB
                $customer = $CMS->customer->social_add($data);
                if(is_array($customer))
                {
                    
                    $CMS->customer->social_login($data);
                }
            }   
        }
	}


	public function forgot_password($data = array())
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

		$customer = $CMS->customer->getInfo($data['email']);

		if(!$customer)
		{
			$_SESSION['msg'] = "Email does not exist";
			return false;
		}

		 
 		
		$cus_password = $data['cus_password'] = $data['cus_repassword'] = $CMS->class->random->password(9,1);
				
		$CMS->customer->update_password( $customer['cus_id'], $data['cus_password'], $customer['cus_token_key'] );
		

		//Send mail
		$CMS->email->email_template = "forgot_password";

		$CMS->email->email_to = $customer['cus_email'];
		$CMS->email->email_toname = $customer['cus_name'] ? $customer['cus_name'] : $customer['cus_email'];

		$CMS->email->data['cus_name'] = $CMS->email->email_toname;
		$CMS->email->data['cus_email'] = $customer['cus_email'];
		$CMS->email->data['cus_password'] =  $data['cus_password'];
		$CMS->email->data['website_name'] =  $CMS->vars['website_title'];
		$CMS->email->quick_send(0,0);

		return $customer;
	}


	public function change_password($data = array())
	{
		global $CMS;

		if(!is_array($data) || empty($data))
		{
			$data = $CMS->input;
		}
 
		if($data['old_password'] == "")
		{
			$_SESSION['error_msg'] .= 'Please enter old password<br />';
			return false;
		}
	 	
	 	$customer = $CMS->customer->getInfo($_SESSION['member']['cus_id']);

 		if(!$customer)
		{ 
			$_SESSION['error_msg'] .= 'Account does not exist<br />';
			return false;
		}

        // Check old password
        if (!$CMS->customer->cus_check_password( $data['old_password'], $customer['cus_password'], $customer["cus_token_key"] ))
        {
            $_SESSION['error_msg'] = "Old password is not correct!";
            return false;
        }

		//check input
		if(empty($data['password']) OR strlen($data['password']) < 6)
		{
			$_SESSION['error_msg'] .= "Please enter a password. Password must be 6 characters<br />";
			return false;
		}

		if(empty($data['repassword']) OR strlen($data['repassword']) < 6)
		{
			$_SESSION['error_msg'] .= "Please enter a password. Password must be 6 characters<br />";
			return false;
		}

		if($data['password'] != $data['repassword'])
		{
			$_SESSION['error_msg'] .= "Passwords do not match<br />";
			return false;
		}


		$CMS->customer->update_password( $customer['cus_id'], $data['password'], $customer['cus_token_key'] );
 
		return true;
	}

}