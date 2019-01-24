<?php
$CMS->api->whm = new api_whm;

class api_whm {
	public $root_domain;
	public $service_name;
	public $key;
	
	public function __construct() {
		$this->root_domain = 'https://cp.fastboydemo.com/api/whm_sync';
		//$this->root_domain = 'http://eco.io/api/whm_sync';
		$this->service_name = 'whm';
		$this->key = 'defeb431a1b02a6e215f1225a4239afe';
	}
	public function __destruct() {
	}
 
	public function execute($cmd = null, $data = null) {
		$response = array('status' => 'error', 'message' => 'No command for excute.');

		if (! is_null($cmd) && ! is_null($data)) {
			$postdata = '';
			$url = $this->root_domain . "?act=" . $cmd . "&cmd=" . $cmd;
			
			$data['service_name'] = $this->service_name;
			$data['key'] = $this->key;
			$data['act'] = $cmd;
			// foreach ($data as $fname=>$fkey) {
			// 	$postdata .= $fname . '=' . urlencode(str_replace('&amp;','&',$fkey)) . '&';	
			// }	

			$postdata = json_encode($data);		
		 
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_POST,1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_HEADER, 0); 
			curl_setopt($ch, CURLOPT_TIMEOUT,1000); 
			curl_setopt($ch, CURLOPT_POSTREDIR, 3);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$postdata);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			if (curl_errno($ch)) {
				$response= curl_error($ch);
			} else {
				
				$response= curl_exec($ch);
			}
			curl_close($ch);
		}
	 
		return $response;
	}


	public function whmcs_execute($cmd = null, $data = null) {
		$response = array('status' => 'error', 'message' => 'No command for excute.');

		if (! is_null($cmd) && ! is_null($data)) {
			$postdata = '';
			 $url =  "https://fastboy.marketing/modules/addons/hosting_synced/callback.php?&action=" . $cmd . "&cmd=" . $cmd;
			//$url =  "http://whmcs.3f.design/modules/addons/hosting_synced/callback.php?&action=" . $cmd . "&cmd=" . $cmd;
			$data['action'] = $cmd;
			$postdata = json_encode($data);	

		// echo $url;exit;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_POST,1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_HEADER, 0); 
			curl_setopt($ch, CURLOPT_TIMEOUT,1000); 
			curl_setopt($ch, CURLOPT_POSTREDIR, 3);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$postdata);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			if (curl_errno($ch)) {
				$response= curl_error($ch);
			} else {
				
				$response= curl_exec($ch);
			}
			curl_close($ch);


		}
	 
		return json_decode($response,true);
	}

	public function whmcs_client_execute($cmd = null, $data = null) {
		$response = array('status' => 'error', 'message' => 'No command for excute.');

		if (! is_null($cmd) && ! is_null($data)) {
			$postdata = '';
			$url =  "https://fastboy.marketing/modules/addons/autom_crm/callback.php?&action=" . $cmd . "&cmd=" . $cmd;
			//$url =  "http://whmcs.3f.design/modules/addons/autom_crm/callback.php?&action=" . $cmd . "&cmd=" . $cmd;
			$data['action'] = $cmd;
			$postdata = json_encode($data);		
			//  echo $postdata;exit;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_POST,1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_HEADER, 0); 
			curl_setopt($ch, CURLOPT_TIMEOUT,1000); 
			curl_setopt($ch, CURLOPT_POSTREDIR, 3);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$postdata);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			if (curl_errno($ch)) {
				$response= curl_error($ch);
			} else {
				
				$response= curl_exec($ch);
			}
			curl_close($ch);


		}
	  	//print_r (json_decode($response,true));exit;
		return json_decode($response,true);
	}

	public function execute_sync_data($url = "") {
		$response = array('status' => 'error', 'message' => 'No command for excute.');

		if (! is_null($url)  ) {
			$data = array();
			$postdata = json_encode($data);	
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_POST,1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_HEADER, 0); 
			curl_setopt($ch, CURLOPT_TIMEOUT,1000); 
			curl_setopt($ch, CURLOPT_POSTREDIR, 3);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$postdata);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			if (curl_errno($ch)) {
				$response= curl_error($ch);
			} else {
				
				$response= curl_exec($ch);
			}
			curl_close($ch);
		}
		 
		return $response;
	}

}

?>