<?php
$CMS->api->eco = new api_eco;

class api_eco {
	public $root_domain;
	public $service_name;
	public $key;
	
	public function __construct() {
		$this->root_domain = 'http://eco.3f.design/api/';
		$this->service_name = 'webhooks';
		$this->key = 'defeb431a1b02a6e215f1225a4239afe';
	}
	public function __destruct() {
	}
 
	public function execute($cmd = null, $data = null) {
		$response = array('status' => 'error', 'message' => 'Không có lệnh để thực thi API.');
		if (! is_null($cmd) && ! is_null($data)) {
			$postdata = '';
			$url = $this->root_domain . "?act=" . $cmd . "&cmd=" . $cmd;
			$data['service_name'] = $this->service_name;
			$data['key'] = $this->key;
			foreach ($data as $fname=>$fkey) {
				$postdata .= $fname . '=' . urlencode(str_replace('&amp;','&',$fkey)) . '&';	
			}	
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_POST,1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_HEADER, 0); 
			curl_setopt($ch, CURLOPT_TIMEOUT,1000); 
			curl_setopt($ch, CURLOPT_POSTREDIR, 3);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$postdata);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			if (curl_errno($ch)) {
				$response['message'] = curl_error($ch);
			} else {
				$response['status'] = true;
				$response['message'] = curl_exec($ch);
			}
			curl_close($ch);
		}
		return $response;
	}
}

?>