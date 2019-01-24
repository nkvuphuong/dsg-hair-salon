<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
} 

require_once root_path."vendor/autoload.php";
use Zalo\Zalo;
use Zalo\ZaloConfig; 
use Zalo\ZaloEndpoint; 

$CMS->api->zalo = new api_zalo;

class api_zalo
{
	public $zalo;
	public $helper;
	public $clientId;
	public $clientSecret;

	//===========================================================================
	//  EXECUTE
	//===========================================================================
	public function __construct(){		
		global $CMS;
	}
	public function get_urllogin($callBackUrl = "")
	{
		global $CMS;
		$CMS->vars['zalo_app_id'] = '{$CMS->vars["zalo_app_id"]}';
		$CMS->vars['zalo_app_secret'] = '{$CMS->vars["zalo_app_secret"]}';
		//
		$loginUrl = "";
		if($CMS->vars['zalo_app_id'] == "" OR $CMS->vars['zalo_app_secret'] == "")
		{ 
			return $loginUrl;
		}
		 
	 	$param = array('app_id' => $CMS->vars['zalo_app_id'], 'app_secret' => $CMS->vars['zalo_app_secret']);
		$this->zalo = new Zalo(ZaloConfig::getInstance()->getConfig($param));


		$this->helper = $this->zalo->getRedirectLoginHelper();

		$loginUrl = $this->helper->getLoginUrl($callBackUrl); // This is login url
		 
		return $loginUrl;
	}


	// Step 1: call this function
	function accesstoken($callBackUrl = ""){
		global $CMS, $member;
		
		$CMS->vars['zalo_app_id'] = '{$CMS->vars["zalo_app_id"]}';
		$CMS->vars['zalo_app_secret'] = '{$CMS->vars["zalo_app_secret"]}';
		//
		$loginUrl = "";
		if($CMS->vars['zalo_app_id'] == "" OR $CMS->vars['zalo_app_secret'] == "")
		{ 
			return $loginUrl;
		}
		 
	 	$param = array('app_id' => $CMS->vars['zalo_app_id'], 'app_secret' => $CMS->vars['zalo_app_secret']);
		$this->zalo = new Zalo(ZaloConfig::getInstance()->getConfig($param));
		
		$this->helper = $this->zalo->getRedirectLoginHelper();
		$oauthCode = isset($_GET['code']) ? $_GET['code'] : "THIS NOT CALLBACK PAGE !!!"; // get oauthoauth code from url params
		$accessToken = $this->helper->getAccessToken($callBackUrl); // get access token
		if ($accessToken != null) {
		    $expires = $accessToken->getExpiresAt(); // get expires time
		}

		 
		$params = [];
		$response = $this->zalo->get(ZaloEndpoint::API_GRAPH_ME, $params, $accessToken);
		$result = $response->getDecodedBody(); // result

		return $result;
	}

	 

}

?>