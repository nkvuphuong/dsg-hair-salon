<?php
namespace models;

use core\ezy;

class nhanhoa {
	static private $urlApi = 'https://nhanhoa.com/api/';
	static private $service_name = 'eco_web4s';
	static private $key = 'gupE9urA';
	
    static function api($act = null, $data = null) {
		$result = array();
		
		if (! is_null($act) && ! is_null($data)) {
			foreach ($data as $k=>$v) {
				$data[$k] = urlencode($v);
			}
			$data['service_name'] = urlencode(self::$service_name);
			$data['key'] = urlencode(MD5(self::$key));
			if ($ch = curl_init()) {
				curl_setopt($ch, CURLOPT_URL, self::$urlApi . '?act=' . $act);
				curl_setopt($ch, CURLOPT_ENCODING , 'UTF-8');
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				
				if (curl_errno($ch) == 0) {
					$result = unserialize(preg_replace('/\xEF\xBB\xBF/', '', curl_exec($ch)));
				}
				
				curl_close ($ch);
			}
		}

		return $result;
	}
	static function apiUrl($act = null,$data = null) {
		$result = array();
		if (! is_null($data) && ! is_null($data)) {
			$data['service_name'] = urlencode(self::$service_name);
			$data['key'] = urlencode(MD5(self::$key));
			$url = self::$urlApi . "?act={$act}";
			foreach ($data as $k=>$v) {
				$url .=	"&{$k}=" . urlencode($v);
			}
			$curl = curl_init();
			curl_setopt($curl , CURLOPT_URL , $url);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$result = unserialize(curl_exec($curl));
			curl_close ($ch);
		}
		return $result;
	}

	static function api2($act = null, $data_input = null, $form_data) {
		$result = array();
		
        foreach($data_input as $key => $value){
            $act_ref.= "&{$key}=".urldecode($value);
        }
        
        $url = self::$urlApi."?act=".$act."&service_name=".urlencode(self::$service_name)."&key=".urlencode(md5(self::$key)).$act_ref;
        
        $data = array();
                
        foreach($form_data as $key => $value){
            $data['formdata'] .= "&{$key}=".urlencode($value);
        }
 
        $curl = curl_init();
        curl_setopt( $curl , CURLOPT_URL , $url );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec( $curl );
        //$error = curl_error($curl);
  
		return unserialize($result);
	}

}