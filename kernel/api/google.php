<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
} 
 
$CMS->api->google = new api_google;

class api_google
{
	public $fb;
	public $clientId;
	public $clientSecret;

	//===========================================================================
	//  EXECUTE
	//===========================================================================
	public function __construct(){		
		global $CMS;
		
        if ( !isset($CMS->vars['gg_app_id']) )
        {
            return false;
        }

        $this->clientId = $CMS->vars['gg_app_id'];
        $this->clientSecret = $CMS->vars['gg_app_secret'];
	}

	// Step 1: call this function
	function login($link_step2){
		global $CMS, $member;

        // Load Google via composer
       //require_once root_path."vendor/autoload.php";
		require_once root_path."kernel/api/Google/Google_Client.php";
		require_once root_path."kernel/api/Google/contrib/Google_Oauth2Service.php";
	 	$this->clientId = $CMS->vars['gg_app_id'];
		$this->clientSecret = $CMS->vars['gg_app_secret'];
		$gClient = new Google_Client();
			$gClient->setApplicationName('Login to Nails');
			$gClient->setClientId($this->clientId);
			$gClient->setClientSecret($this->clientSecret);
			$gClient->setRedirectUri($link_step2);
			
			$google_oauthV2 = new Google_Oauth2Service($gClient);
			
			
		$authUrl = $gClient->createAuthUrl();

		return $authUrl;
	}

	function login_step2($link_step2 = ""){
		global $CMS, $member;

        // Load Google via composer
        //require_once root_path."vendor/autoload.php";
		require_once root_path."kernel/api/Google/Google_Client.php";
		require_once root_path."kernel/api/Google/contrib/Google_Oauth2Service.php";
	 	$this->clientId = $CMS->vars['gg_app_id'];
		$this->clientSecret = $CMS->vars['gg_app_secret'];
		$gClient = new Google_Client();
		$gClient->setApplicationName('Workshop');
		$gClient->setClientId($this->clientId);
		$gClient->setClientSecret($this->clientSecret);
		$gClient->setRedirectUri($link_step2);
		
		 $google_oauthV2 = new Google_Oauth2Service($gClient);
			if(isset($_REQUEST['code'])){
				 $gClient->authenticate();
				$_SESSION['token'] = $gClient->getAccessToken();
				 
				 // header('Location: ' . filter_var($link_step2, FILTER_SANITIZE_URL));
			}

			if (isset($_SESSION['token'])) {
				$gClient->setAccessToken($_SESSION['token']);
			}
	
		if ($gClient->getAccessToken()) {
			
			$userProfile = $google_oauthV2->userinfo->get();
	 
			//DB Insert
			$_SESSION['google_data'] = $userProfile; // Storing Google User Data in Session
			 
			$_SESSION['token'] = $gClient->getAccessToken();
		}
	}

}

?>