<?php

$CMS->api->baokim = new api_baokim;
class api_baokim
{
	public $url_api = 'https://www.baokim.vn/payment/order/version11';
	public $url_bpn = '';
	public $merchant_id = '';
	public $email_business = '';
	public $secure_pass = '';
	public $currency = 'VND';

	function LoadMerchant()
	{
		global $CMS;

		// $this->url_api = "";//$CMS->vars['baokim_url_api'];
		// $this->url_bpn = $CMS->vars['baokim_url_bpn'];
		$this->merchant_id = $CMS->vars['bk_merchant_id'];
		$this->email_business = $CMS->vars['bk_email_business'];
		$this->secure_pass = $CMS->vars['bk_secure_pass'];
	}

	public function createRequestUrl($inputs)
	{
		$this->LoadMerchant();

		$params = array(
			'merchant_id'		=>	strval($this->merchant_id),
			'business'			=>	trim($this->email_business),
			'order_id'			=>	strval($inputs['order_id']),

			'total_amount'		=>	strval($inputs['total_amount']),
			'shipping_fee'		=>  strval($inputs['shipping_fee']),
			'tax_fee'			=>  strval($inputs['tax_fee']),
			'order_description'	=>	strval($inputs['order_description']),

			'url_success'		=>	strval($inputs['url_success']),
			'url_cancel'		=>	strval($inputs['url_cancel']),
			'url_detail'		=>	strval($inputs['url_detail']),

			'payer_name'		=>  strval($inputs['payer_name']),
			'payer_email'		=> 	strval($inputs['payer_email']),
			'payer_phone_no'	=> 	strval($inputs['payer_phone_no']),
			'shipping_address'  =>  strval($inputs['shipping_address']),
			'currency' 			=>  strval($this->currency)
		);
		
		ksort($params);

		$params['checksum'] = hash_hmac('SHA1',implode('',$params),$this->secure_pass);

		// Tạo đoạn url chứa tham số
		$url_params = '';
		foreach ( $params as $key=>$value )
		{
			if ( $url_params != '' )
			{
				$url_params .= '&';
			}

			$url_params .= $key . '=' . urlencode($value);
		}
		
		return $this->url_api . '?' . $url_params;
	}

	public function verifyResponseUrl($url_params = array())
	{
		$this->LoadMerchant();

		if( empty($url_params['checksum']) )
		{
			echo "invalid parameters: checksum is missing";
			return false;
		}

		$checksum = $url_params['checksum'];
		unset($url_params['checksum']);

		ksort($url_params);

		if( strcasecmp($checksum,hash_hmac('SHA1',implode('',$url_params),$this->secure_pass)) === 0 )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function veyfyBPN($req='')
	{
		/*Mẫu $req = created_on=1287729470&customer_address=Dia+Chi+Khach+Hang&customer_email=khoinm%40baokim.vn&customer_name=Nguyen+Minh+Khoi&customer_phone=84987654321&fee_amount=1000&merchant_address=Dia+Chi+Cong+Ty&merchant_email=hangntt%40baokim.vn&merchant_id=8merchant_name=Nguyen+Thi+Thu+Hang&merchant_phone=84981234567&net_amount=99000&order_id=100139&payment_type=2&total_amount=100000.00&transaction_id=2506B4F7E6E6C&transaction_status=4&resend=true&verify_sign=2IsQX54QVnYrU2wpsaWJCusC1veXr0vu2auZ451trdoA6
		*/

		$this->LoadMerchant();

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->url_bpn);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);//thời gian chờ tối đa để lấy dữ liệu
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);

		$result = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
		$error = curl_error($ch);

		curl_close ($ch);

		return array(
			'result' => $result,
			'status' => $status,
			'error'  => $error
			);
	}

	public function getTransaction_status($input = 'X')
	{
		$this->LoadMerchant();

		$status = array( 
				'1' => 'giao dịch chưa xác minh OTP',
				'2' => 'giao dịch đã xác minh OTP',
				'4' => 'giao dịch hoàn thành',//đã nhận tiền
				'5' => 'giao dịch bị hủy',
				'6' => 'giao dịch bị từ chối nhận tiền',
				'7' => 'giao dịch hết hạn',
				'8' => 'giao dịch thất bại',
				'12'=> 'giao dịch bị đóng băng',
				'13'=> 'giao dịch bị tạm giữ (thanh toán an toàn)',
				'X' => 'các trạng thái giao dịch khác'
			);

		return $status[$input];
	}
}