<?php
/****************************************************************************
*	Copyright  Facebook © 2017												*
*	URL: https://facebook.com/												*
*	Editer: lêlong															*
*	Date: 210817															*
*	API Graph Facebook														*
*	Document: https://developers.facebook.com/docs/graph-api?locale=en_US	*
*****************************************************************************/
if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
}

$_SESSION['FBRLH_state'] = isset($_GET['state']) ? $_GET['state'] : null; //fix : Cross-site request forgery validation failed

// require_once root_path."kernel/api/Facebook/autoload.php";
$CMS->api->facebook = new api_facebook;

class api_facebook
{
	public $fb;
	public $api_key; 
	public $secret_key;
	public $permissions;
	public $accessToken;
	// ===========================================================================
	 // EXECUTE
	// ===========================================================================
	public function __construct()
    {
		global $CMS;

		if ( !isset($CMS->vars['fb_app_id']) )
        {
            return false;
        }

		$this->api_key = $CMS->vars['fb_app_id']; 
		$this->app_secret = $CMS->vars['fb_app_secret']; 
	}
 
	function login($link_step2){
		global $CMS, $member;

		// Load Facebook via composer
        require_once root_path."vendor/autoload.php";
		 
		$array_config = array(
		  'app_id' => $this->api_key,
		  'app_secret' => $this->secret_key,
		  'default_graph_version' => 'v2.6',
		  'default_access_token'=>  $this->api_key.'|'.$this->app_secret
		);		
		$fb = new Facebook\Facebook($array_config);		
		unset($_SESSION['facebook_access_token']);
		$helper = $fb->getRedirectLoginHelper();
		$_SESSION['FBRLH_state']=$_GET['state']; //fix : Cross-site request forgery validation failed
		$permissions =  ['email', 'user_likes','public_profile'] ;
		$loginUrl = $helper->getLoginUrl($link_step2,$permissions);
 		
		$_SESSION['page_start_login'] = $CMS->vars['root_domain'].$_SERVER[REQUEST_URI];
		return $loginUrl;
	}

    function login_step2(){

        // Load Facebook via composer
        require_once root_path."vendor/autoload.php";

        $array_config = array(
            'app_id' => $this->api_key,
            'app_secret' => $this->secret_key,
            'default_graph_version' => 'v2.6'
        );
        if(isset($_SESSION['facebook_access_token'])){
            $array_config['default_access_token'] = $_SESSION['facebook_access_token'];
        }else{
            $array_config['default_access_token'] = $this->api_key.'|'.$this->app_secret;
        }

        $fb = new Facebook\Facebook($array_config);
        $helper = $fb->getRedirectLoginHelper();
        $_SESSION['FBRLH_state']=$_GET['state'];//fix : Cross-site request forgery validation failed

        if(!isset($_SESSION['facebook_access_token'])){


            $accessToken = $helper->getAccessToken();
            $_SESSION['facebook_access_token'] = (string) $accessToken;

            header("Refresh:0");exit;
        }
        if (isset($_SESSION['facebook_access_token'])){
            // Logged in!

            $response = $fb->get('/me?fields=id,name,first_name,last_name,gender,email');

            $user = $response->getGraphUser();

            $_SESSION['FGID'] 		= $user['id'];
            $_SESSION['FULLNAME'] 	= $user['name'];
            $_SESSION['FIRST_NAME'] = $user['first_name'];
            $_SESSION['LAST_NAME'] 	= $user['last_name'];
            $_SESSION['GENDER'] 	= $user['gender'];
            $_SESSION['EMAIL'] 		= $user['email'];
            $_SESSION['customer_type'] =  "fb";

        }
    }
	function serverConnection() {
		$respone = array('status' => false, 'messages' => '');
		global $CMS;
		if (! empty($CMS->vars['graph_facebook_token']) || (! empty($CMS->vars['graph_facebook_id']) && ! empty($CMS->vars['graph_facebook_secret']))) {
			$this->permissions = array('email','publish_pages','manage_pages');
			
			if (! empty($CMS->vars['graph_facebook_token']) && empty($CMS->input['code'])) {
				$_SESSION['graph_facebook_token'] = $CMS->vars['graph_facebook_token'];
			}
			
			$this->fb = new Facebook\Facebook([
				'app_id' => $CMS->vars['graph_facebook_id'],
				'app_secret' => $CMS->vars['graph_facebook_secret'],
				'default_graph_version' => 'v2.10',
			]);
			$helper = $this->fb->getRedirectLoginHelper();
			try {
				if (isset($_SESSION['graph_facebook_token'])) {
					$this->accessToken = $_SESSION['graph_facebook_token'];
				} else {
					$this->accessToken = $helper->getAccessToken();
				}
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
				$respone['messages'] = 'Graph returned an error: ' . $e->getMessage();
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
				$respone['messages'] = 'Facebook SDK returned an error: ' . $e->getMessage();
			}
			if (isset($this->accessToken)) {
				$_SESSION['graph_facebook_token'] = isset($_SESSION['graph_facebook_token']) ? $_SESSION['graph_facebook_token'] : (string) $this->accessToken;
				$this->fb->setDefaultAccessToken($_SESSION['graph_facebook_token']);
				$_POST['config']['input']['graph_facebook_token'] = (string)$this->accessToken;
				$CMS->config_general->edit();
				try {
					$request = $this->fb->get('/me?fields=email');
					$data = $request->getGraphNode()->asArray();
					if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
						$respone['status'] = true;
					}
				} catch(Facebook\Exceptions\FacebookResponseException $e) {
					$respone['messages'] = 'Graph returned an error: ' . $e->getMessage();
				} catch(Facebook\Exceptions\FacebookSDKException $e) {
					$respone['messages'] = 'Facebook SDK returned an error: ' . $e->getMessage();
				}
			} else {
				$respone['status'] = 'not_login';
				$url = $helper->getLoginUrl($CMS->vars['root_domain'] . '/?site=facebook&act=fanpage', $this->permissions);
				$respone['messages'] = $url;
			}
		}
		return $respone;
	}
	function connect() {
		global $CMS;
		if (! empty($CMS->vars['graph_facebook_id']) && ! empty($CMS->vars['graph_facebook_secret'])) {
			$this->fb = new Facebook\Facebook([
				'app_id' => $CMS->vars['graph_facebook_id'],
				'app_secret' => $CMS->vars['graph_facebook_secret'],
				'default_graph_version' => 'v2.10',
			]);
		}
	}
	function getLinkLogin() {
		$respone = array('status' => false, 'messages' => '', 'link' => '', 'id' => '', 'email' => '', 'token' => '');
		global $CMS;
		if (! empty($this->fb)) {
			$this->permissions = array('email', 'manage_pages', 'publish_pages', 'read_page_mailboxes', 'pages_messaging', 'pages_messaging_subscriptions', 'business_management');
			$helper = $this->fb->getRedirectLoginHelper();
			try {
				if (isset($_SESSION['graph_facebook_token'])) {
					$this->accessToken = $_SESSION['graph_facebook_token'];
				} else {
					try {
						$accessToken = $helper->getAccessToken();
					} catch(Facebook\Exceptions\FacebookResponseException $e) {
						$respone['messages'] = 'Graph returned an error: ' . $e->getMessage();
					} catch(Facebook\Exceptions\FacebookSDKException $e) {
						$respone['messages'] = 'Facebook SDK returned an error: ' . $e->getMessage();
					}
					if (empty($respone['messages']) && $accessToken) {
						$oAuth2Client = $this->fb->getOAuth2Client();
						if ($accessToken->isLongLived()) {
						} else {
							try {
								$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
							} catch (Facebook\Exceptions\FacebookSDKException $e) {
								$respone['messages'] = 'Error getting long-lived access token: ' . $e->getMessage();
							}
						}
						$this->accessToken = $_SESSION['graph_facebook_token'] = (string) $accessToken;
					}
				}
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
				$respone['messages'] = 'Graph returned an error: ' . $e->getMessage();
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
				$respone['messages'] = 'Facebook SDK returned an error: ' . $e->getMessage();
			}
			if (empty($respone['messages']) && isset($this->accessToken)) {
				$this->fb->setDefaultAccessToken((string) $this->accessToken);
				try {
					$request = $this->fb->get('/me?fields=email,id');
					$data = $request->getGraphNode()->asArray();
					if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
						$respone['status'] = true;
						$respone['id'] = $data['id'];
						$respone['email'] = $data['email'];
						$respone['token'] = (string)$this->accessToken;
					}
				} catch(Facebook\Exceptions\FacebookResponseException $e) {
					$respone['messages'] = 'Graph returned an error: ' . $e->getMessage();
				} catch(Facebook\Exceptions\FacebookSDKException $e) {
					$respone['messages'] = 'Facebook SDK returned an error: ' . $e->getMessage();
				}
			} else {
				$respone['link'] = $helper->getLoginUrl($CMS->vars['root_domain']  . '/?act=fanpage', $this->permissions);
			}
		}
		return $respone;
	}
}

?>