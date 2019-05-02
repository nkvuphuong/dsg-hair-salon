<?php
//==================================================================================
//===PAYMENT NGÂN LƯỢNG VERSION 3.1=================================================
//==================================================================================
if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->api->nganluong = new api_nganluong;
class api_nganluong
{
	public $version = '3.1';
    public $url_api ='';  
	public $merchant_id = '';
	public $merchant_password = '';
	public $receiver_email = '';
	public $keyonline = '';

	// Địa chỉ thanh toán hoá đơn của NgânLượng.vn
	public $nganluong_url = 'https://www.nganluong.vn/checkout.php';	
	

	function LoadMerchant()
	{
		global $CMS;
		$this->url_api = $this->nganluong_url;
		$this->merchant_id = $CMS->vars['nl_merchant_id'];
		$this->merchant_password = $CMS->vars['nl_merchant_password'];
		$this->receiver_email = $CMS->vars['nl_receiver_email'];
	}

	function GetTransactionDetail($token)
	{
		$this->LoadMerchant();

		$params = array(
				'merchant_id' 		=> $this->merchant_id ,
				'merchant_password' => MD5($this->merchant_password),
				'version' 			=> $this->version,
				'function' 			=> 'GetTransactionDetail',
				'token' 			=> $token
			);

		$post_field = '';
		foreach ( $params as $key => $value )
		{
			if ( $post_field != '' )
			{
				$post_field .= '&';
			}
			$post_field .= $key . '=' . $value;
		}

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,$this->url_api);
		curl_setopt($ch, CURLOPT_ENCODING , 'UTF-8');
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field);

		$result = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
		$error  = curl_error($ch);

		curl_close ($ch);
		
		if ( $result != '' AND $status == 200 )
		{
			$nl_result  = simplexml_load_string($result);	
			return $nl_result;
		}
		
		return false;
	}

	// Hàm lấy link thanh toán dùng số dư ví ngân lượng
	// payment_type Kiểu giao dịch: 1 - Ngay; 2 - Tạm giữ; Nếu không truyền hoặc bằng rỗng thì lấy theo chính sách của NganLuong.vn
	// $order_code, $total_amount, $payment_type, $order_description, $tax_amount, $fee_shipping, $discount_amount, $return_url, $cancel_url, $buyer_fullname, $buyer_email, $buyer_mobile, $buyer_address, $array_items
	function NLCheckout($inputs)
	{
	    global $CMS;

		$this->LoadMerchant();

		$params = array(
				'cur_code'			=>  $inputs['cur_code'],  // vnd : usd
				'function'			=>  'SetExpressCheckout',
				'version'			=>  $this->version,
				'merchant_id'		=>  $this->merchant_id,
				'receiver_email'	=>  $this->receiver_email,
				'merchant_password'	=>  MD5($this->merchant_password),
				'order_code'		=>  $inputs['order_code'],
				'total_amount'		=>  $inputs['total_amount'],
				'payment_method'	=>  'NL',
				'payment_type'		=>  $inputs['payment_type'],
				'order_description'	=>  $inputs['order_description'],
				'tax_amount'		=>  $inputs['tax_amount'],
				'fee_shipping'		=>  $inputs['fee_shipping'],
				'discount_amount'	=>  $inputs['discount_amount'],
				'return_url'		=>  $inputs['return_url'],
				'cancel_url'		=>  $inputs['cancel_url'],
				'buyer_fullname'	=>  $inputs['buyer_fullname'],
				'buyer_email'		=>  $inputs['buyer_email'],
				'buyer_mobile'		=>  $inputs['buyer_mobile'],
				'buyer_address'		=>  $inputs['buyer_address'],
				'total_item'		=>  count($inputs['array_items'])
			);
		
		$post_field = '';
		foreach ( $params as $key => $value )
		{
			if ( $post_field != '' )
			{
				$post_field .= '&';
			}
			$post_field .= $key . '=' . $value;
		}

		if ( count($array_items) > 0 )
		{
		 	foreach ( $array_items as $array_item )
		 	{
				foreach ( $array_item as $key => $value )
				{
					if ( $post_field != '' )
					{
						$post_field .= '&';
					}
					$post_field .= $key . '=' . $value;
				}
			}
		}

		$nl_result = $this->CheckoutCall($post_field);
		return $nl_result;
    }

    // Hàm lấy link thanh toán qua ngân hàng (ATM ONLINE)
	// payment_type Kiểu giao dịch: 1 - Ngay; 2 - Tạm giữ; Nếu không truyền hoặc bằng rỗng thì lấy theo chính sách của NganLuong.vn
	// $order_code,$total_amount,$bank_code,$payment_type,$order_description,$tax_amount,$fee_shipping,$discount_amount,$return_url,$cancel_url,$buyer_fullname,$buyer_email,$buyer_mobile,$buyer_address,$array_items
    function BankCheckout($inputs)
    {
		$this->LoadMerchant();

    	$params = array(
    		'cur_code'				=>	$inputs['cur_code'],  // vnd : usd
			'function'				=>  'SetExpressCheckout',
			'version'				=>  $this->version,
			'merchant_id'			=>  $this->merchant_id,
			'receiver_email'		=>  $this->receiver_email,
			'merchant_password'		=>  MD5($this->merchant_password),				
			'order_code'			=>  $inputs['order_code'],
			'total_amount'			=>  $inputs['total_amount'],					
			'payment_method'		=>  'ATM_ONLINE',
			'bank_code'				=>  $inputs['bank_code'],
			'payment_type'			=>  $inputs['payment_type'],
			'order_description'		=>  $inputs['order_description'],
			'tax_amount'			=>  $inputs['tax_amount'],
			'fee_shipping'			=>  $inputs['fee_shipping'],
			'discount_amount'		=>  $inputs['discount_amount'],
			'return_url'			=>  $inputs['return_url'],
			'cancel_url'			=>  $inputs['cancel_url'],
			'buyer_fullname'		=>  $inputs['buyer_fullname'],
			'buyer_email'			=>  $inputs['buyer_email'],
			'buyer_mobile'			=>  $inputs['buyer_mobile'],
			'buyer_address'			=>  $inputs['buyer_address'],
			'total_item'			=>  count($inputs['array_items'])
		);

		$post_field = '';
		foreach ( $params as $key => $value )
		{
			if ( $post_field != '' )
			{
				$post_field .= '&';
			}
			$post_field .= $key . '=' . $value;
		}

		if ( count($array_items) > 0 )
		{
		 	foreach ( $array_items as $array_item )
		 	{
				foreach ( $array_item as $key => $value )
				{
					if ( $post_field != '' )
					{
						$post_field .= '&';
					}
					$post_field .= $key . '=' . $value;
				}
			}
		}

		$nl_result=$this->CheckoutCall($post_field);
		return $nl_result;
	}

	// Hàm lấy link thanh toán qua ngân hàng (INTERNET BANKING)
	// payment_type Kiểu giao dịch: 1 - Ngay; 2 - Tạm giữ; Nếu không truyền hoặc bằng rỗng thì lấy theo chính sách của NganLuong.vn
	// $order_code, $total_amount, $bank_code, $payment_type, $order_description, $tax_amount, $fee_shipping, $discount_amount, $return_url, $cancel_url, $buyer_fullname, $buyer_email, $buyer_mobile, $buyer_address, $array_items
	function IBCheckout($inputs)
	{
		$this->LoadMerchant();

        $params = array(
            'cur_code'				=>  $inputs['cur_code'],  // vnd : usd
            'function' 				=>  'SetExpressCheckout',
            'version' 				=>  $this->version,
            'merchant_id' 			=>  $this->merchant_id,
            'receiver_email' 		=>  $this->receiver_email,
            'merchant_password' 	=>  MD5($this->merchant_password),				
            'order_code' 			=>  $inputs['order_code'],
            'total_amount' 			=>  $inputs['total_amount'],
            'payment_method' 		=>  'IB_ONLINE',
			'bank_code' 			=>  $inputs['bank_code'],
            'payment_type' 			=>  $inputs['payment_type'],
            'order_description' 	=>  $inputs['order_description'],
            'tax_amount' 			=>  $inputs['tax_amount'],
            'fee_shipping' 			=>  $inputs['fee_shipping'],
            'discount_amount' 		=>  $inputs['discount_amount'],
            'return_url' 			=>  $inputs['return_url'],
            'cancel_url' 			=>  $inputs['cancel_url'],
            'buyer_fullname' 		=>  $inputs['buyer_fullname'],
            'buyer_email' 			=>  $inputs['buyer_email'],
            'buyer_mobile' 			=>  $inputs['buyer_mobile'],
            'buyer_address' 		=>  $inputs['buyer_address'],
            'total_item' 			=>  count($inputs['array_items'])
        );

        $post_field = '';
		foreach ( $params as $key => $value )
		{
			if ( $post_field != '' )
			{
				$post_field .= '&';
			}
			$post_field .= $key . '=' . $value;
		}

		if ( count($array_items) > 0 )
		{
		 	foreach ( $array_items as $array_item )
		 	{
				foreach ( $array_item as $key => $value )
				{
					if ( $post_field != '' )
					{
						$post_field .= '&';
					}
					$post_field .= $key . '=' . $value;
				}
			}
		}

		$nl_result=$this->CheckoutCall($post_field);
		return $nl_result;
    }

    // Hàm lấy link thanh toán qua thẻ VISA/MASTERCARD
	// payment_type Kiểu giao dịch: 1 - Ngay; 2 - Tạm giữ; Nếu không truyền hoặc bằng rỗng thì lấy theo chính sách của NganLuong.vn
	// $order_code,$total_amount,$payment_type,$order_description,$tax_amount,$fee_shipping,$discount_amount,$return_url,$cancel_url,$buyer_fullname,$buyer_email,$buyer_mobile,$buyer_address,$array_items,$bank_code
    function VisaCheckout($inputs) 
	{
		$this->LoadMerchant();

		$params = array(
			'cur_code'				=>	$inputs['cur_code'],  // vnd : usd
			'function'				=>  'SetExpressCheckout',
			'version'				=>  $this->version,
			'merchant_id'			=>  $this->merchant_id,
			'receiver_email'		=>  $this->receiver_email,
			'merchant_password'		=>  MD5($this->merchant_password),					
			'order_code'			=>  $inputs['order_code'],
			'total_amount'			=>  $inputs['total_amount'],
			'payment_method'		=>  'VISA',
			'bank_code'				=>  $inputs['bank_code'],					
			'payment_type'			=>  $inputs['payment_type'],
			'order_description'		=>  $inputs['order_description'],
			'tax_amount'			=>  $inputs['tax_amount'],
			'fee_shipping'			=>  $inputs['fee_shipping'],
			'discount_amount'		=>  $inputs['discount_amount'],
			'return_url'			=>  $inputs['return_url'],
			'cancel_url'			=>  $inputs['cancel_url'],
			'buyer_fullname'		=>  $inputs['buyer_fullname'],
			'buyer_email'			=>  $inputs['buyer_email'],
			'buyer_mobile'			=>  $inputs['buyer_mobile'],
			'buyer_address'			=>  $inputs['buyer_address'],
			'total_item'			=>  count($inputs['array_items'])
		);

		$post_field = '';
		foreach ( $params as $key => $value )
		{
			if ( $post_field != '' )
			{
				$post_field .= '&';
			}
			$post_field .= $key . '=' . $value;
		}

		if ( count($array_items) > 0 )
		{
		 	foreach ( $array_items as $array_item )
		 	{
				foreach ( $array_item as $key => $value )
				{
					if ( $post_field != '' )
					{
						$post_field .= '&';
					}
					$post_field .= $key . '=' . $value;
				}
			}
		}

		$nl_result=$this->CheckoutCall($post_field);
		return $nl_result;
	}

	// Hàm lấy link thanh toán qua thẻ VISA/MASTERCARD
	// payment_type Kiểu giao dịch: 1 - Ngay; 2 - Tạm giữ; Nếu không truyền hoặc bằng rỗng thì lấy theo chính sách của NganLuong.vn
	// $order_code, $total_amount, $payment_type, $order_description, $tax_amount, $fee_shipping, $discount_amount, $return_url, $cancel_url, $buyer_fullname, $buyer_email, $buyer_mobile, $buyer_address, $array_items, $bank_code
	function PrepaidVisaCheckout($inputs)
	{
		$this->LoadMerchant();

        $params = array(
            'cur_code' 				=>  $inputs['cur_code'],  // vnd : usd
            'function'				=>  'SetExpressCheckout',
			'version'				=>  $this->version,
            'merchant_id' 			=>  $this->merchant_id,
            'receiver_email' 		=>  $this->receiver_email,
            'merchant_password' 	=>  MD5($this->merchant_password),					
            'order_code' 			=>  $inputs['order_code'],
            'total_amount' 			=>  $inputs['total_amount'],
            'payment_method' 		=>  'CREDIT_CARD_PREPAID',
            'bank_code' 			=>  $inputs['bank_code'],								
            'payment_type' 			=>  $inputs['payment_type'],
            'order_description' 	=>  $inputs['order_description'],
            'tax_amount' 			=>  $inputs['tax_amount'],
            'fee_shipping' 			=>  $inputs['fee_shipping'],
            'discount_amount' 		=>  $inputs['discount_amount'],
            'return_url'			=>  $inputs['return_url'],
            'cancel_url' 			=>  $inputs['cancel_url'],
            'buyer_fullname' 		=>  $inputs['buyer_fullname'],
            'buyer_email' 			=>  $inputs['buyer_email'],
            'buyer_mobile' 			=>  $inputs['buyer_mobile'],
            'buyer_address' 		=>  $inputs['buyer_address'],
            'total_item' 			=>  count($inputs['array_items'])
        );

        $post_field = '';
		foreach ( $params as $key => $value )
		{
			if ( $post_field != '' )
			{
				$post_field .= '&';
			}
			$post_field .= $key . '=' . $value;
		}

		if ( count($array_items) > 0 )
		{
		 	foreach ( $array_items as $array_item )
		 	{
				foreach ( $array_item as $key => $value )
				{
					if ( $post_field != '' )
					{
						$post_field .= '&';
					}
					$post_field .= $key . '=' . $value;
				}
			}
		}

		$nl_result=$this->CheckoutCall($post_field);
		return $nl_result;
    }

    function CheckoutCall($post_field)
    {
        global $CMS;

    	$this->LoadMerchant();

    	$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL,$this->url_api);
		curl_setopt($ch, CURLOPT_ENCODING , 'UTF-8');
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field);

		$res['result'] = $result = curl_exec($ch);
        $res['status'] = $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $res['error'] = $error = curl_error($ch);

        $CMS->subscription_log->updateResponse([
            'sublog_id' => $_SESSION['cart']['sublog_id'],
            'sublog_response' => @json_encode($res, JSON_UNESCAPED_UNICODE),
        ]);

		curl_close ($ch);
		
		if ( $result != '' AND $status == 200 )
		{					
			$xml_result = str_replace('&','&amp;',(string)$result);
			$nl_result  = simplexml_load_string($xml_result);					
			$nl_result->error_message = $this->GetErrorMessage($nl_result->error_code);									
		}
		else
		{
			$nl_result->error_message = $error;
		}

		return $nl_result;
	}
			
	function GetErrorMessage($error_code)
	{
		$arrCode = array(
				'00'  => 'Thành công',
				'99'  => 'Lỗi chưa xác minh',
				'06'  => 'Mã merchant không tồn tại hoặc bị khóa',
				'02'  => 'Địa chỉ IP truy cập bị từ chối',
				'03'  => 'Mã checksum không chính xác, truy cập bị từ chối',
				'04'  => 'Tên hàm API do merchant gọi tới không hợp lệ (không tồn tại)',
				'05'  => 'Sai version của API',
				'07'  => 'Sai mật khẩu của merchant',
				'08'  => 'Địa chỉ email tài salonản nhận tiền không tồn tại',
				'09'  => 'Tài salonản nhận tiền đang bị phong tỏa giao dịch',
				'10'  => 'Mã đơn hàng không hợp lệ',
				'11'  => 'Số tiền giao dịch lớn hơn hoặc nhỏ hơn quy định',
				'12'  => 'Loại tiền tệ không hợp lệ',
				'29'  => 'Token không tồn tại',
				'80'  => 'Không thêm được đơn hàng',
				'81'  => 'Đơn hàng chưa được thanh toán',
				'110' => 'Địa chỉ email tài salonản nhận tiền không phải email chính',
				'111' => 'Tài salonản nhận tiền đang bị khóa',
				'113' => 'Tài salonản nhận tiền chưa cấu hình là người bán nội dung số',
				'114' => 'Giao dịch đang thực hiện, chưa kết thúc',
				'115' => 'Giao dịch bị hủy',
				'118' => 'tax_amount không hợp lệ',
				'119' => 'discount_amount không hợp lệ',
				'120' => 'fee_shipping không hợp lệ',
				'121' => 'return_url không hợp lệ',
				'122' => 'cancel_url không hợp lệ',
				'123' => 'items không hợp lệ',
				'124' => 'transaction_info không hợp lệ',
				'125' => 'quantity không hợp lệ',
				'126' => 'order_description không hợp lệ',
				'127' => 'affiliate_code không hợp lệ',
				'128' => 'time_limit không hợp lệ',
				'129' => 'buyer_fullname không hợp lệ',
				'130' => 'buyer_email không hợp lệ',
				'131' => 'buyer_mobile không hợp lệ',
				'132' => 'buyer_address không hợp lệ',
				'133' => 'total_item không hợp lệ',
				'134' => 'payment_method, bank_code không hợp lệ',
				'135' => 'Lỗi kết nối tới hệ thống ngân hàng',
				'140' => 'Đơn hàng không hỗ trợ thanh toán trả góp',
			);

		return $arrCode[(string)$error_code];
	}
/******************** CODE MOI CHO THANH TOAN BEN NGOAI WEBSITE Tanlv 28/06/2017 **********************/
	public function buildCheckoutUrlExpand($return_url, $receiver, $transaction_info, $order_code, $price, $currency = 'vnd', $quantity = 1, $tax = 0, $discount = 0, $fee_cal = 0, $fee_shipping = 0, $order_description = '', $buyer_info = '', $affiliate_code = '')
	{	

		if ($affiliate_code == "") $affiliate_code = $this->affiliate_code;
		$arr_param = array(
			'merchant_site_code'=>	strval($this->merchant_id),
			'return_url'		=>	strval(strtolower($return_url)),
			'receiver'			=>	strval($receiver),
			'transaction_info'	=>	strval($transaction_info),
			'order_code'		=>	strval($order_code),
			'price'				=>	strval($price),
			'currency'			=>	strval($currency),
			'quantity'			=>	strval($quantity),
			'tax'				=>	strval($tax),
			'discount'			=>	strval($discount),
			'fee_cal'			=>	strval($fee_cal),
			'fee_shipping'		=>	strval($fee_shipping),
			'order_description'	=>	strval($order_description),
			'buyer_info'		=>	strval($buyer_info), //"Họ tên người mua *|* Địa chỉ Email *|* Điện thoại *|* Địa chỉ nhận hàng"
			'affiliate_code'	=>	strval($affiliate_code)
		);
		
		$secure_code ='';
		$secure_code = implode(' ', $arr_param) . ' ' . $this->merchant_password;
		//var_dump($secure_code). "<br/>";
		$arr_param['secure_code'] = md5($secure_code);		
		//echo $arr_param['secure_code'];
		/* */
		$redirect_url = $this->url_api;
		if (strpos($redirect_url, '?') === false) {
			$redirect_url .= '?';
		} else if (substr($redirect_url, strlen($redirect_url)-1, 1) != '?' && strpos($redirect_url, '&') === false) {
			$redirect_url .= '&';			
		}
				
		/* */
		$url = '';
		foreach ($arr_param as $key=>$value) {
			$value = urlencode($value);
			if ($url == '') {
				$url .= $key . '=' . $value;
			} else {
				$url .= '&' . $key . '=' . $value;
			}
		}
		//echo $url;
		// die;
		return $redirect_url.$url;
	}
	

	public function buildCheckoutUrlExpand_2($return_url, $cancel_url, $receiver, $transaction_info, $order_code, $price, $currency = 'vnd', $quantity = 1, $tax = 0, $discount = 0, $fee_cal = 0, $fee_shipping = 0, $order_description = '', $buyer_info = '', $affiliate_code = '')
	{	

		if ($affiliate_code == "") $affiliate_code = $this->affiliate_code;
		$arr_param = array(
			'merchant_site_code'=>	strval($this->merchant_id),
			'return_url'		=>	strval(strtolower($return_url)),
			'receiver'			=>	strval($receiver),
			'transaction_info'	=>	strval($transaction_info),
			'order_code'		=>	strval($order_code),
			'price'				=>	strval($price),
			'currency'			=>	strval($currency),
			'quantity'			=>	strval($quantity),
			'tax'				=>	strval($tax),
			'discount'			=>	strval($discount),
			'fee_cal'			=>	strval($fee_cal),
			'fee_shipping'		=>	strval($fee_shipping),
			'order_description'	=>	strval($order_description),
			'buyer_info'		=>	strval($buyer_info), //"Họ tên người mua *|* Địa chỉ Email *|* Điện thoại *|* Địa chỉ nhận hàng"
			'affiliate_code'	=>	strval($affiliate_code)
		);
		
		$secure_code ='';
		$secure_code = implode(' ', $arr_param) . ' ' . $this->merchant_password;
		//var_dump($secure_code). "<br/>";
		$arr_param['secure_code'] = md5($secure_code);		
		//echo $arr_param['secure_code'];
		/* */
		$redirect_url = $this->url_api;
		if (strpos($redirect_url, '?') === false) {
			$redirect_url .= '?';
		} else if (substr($redirect_url, strlen($redirect_url)-1, 1) != '?' && strpos($redirect_url, '&') === false) {
			$redirect_url .= '&';			
		}
				
		/* */
		$url = '';
		foreach ($arr_param as $key=>$value) {
			$value = urlencode($value);
			if ($url == '') {
				$url .= $key . '=' . $value;
			} else {
				$url .= '&' . $key . '=' . $value;
			}
		}
		//echo $url;
		// die;
		// 
		// 
		if($cancel_url != "")
		{
			 
			return $redirect_url.$url.'&cancel_url='. $cancel_url;
		}else
		{
			return $redirect_url.$url;
		}
		
		
	}

	/**
	 * HÀM TẠO ĐƯỜNG LINK THANH TOÁN QUA NGÂNLƯỢNG.VN VỚI THAM SỐ CƠ BẢN
	 *
	 * @param string $return_url: Đường link dùng để cập nhật tình trạng hoá đơn tại website của bạn khi người mua thanh toán thành công tại NgânLượng.vn
	 * @param string $receiver: Địa chỉ Email chính của tài salonản NgânLượng.vn của người bán dùng nhận tiền bán hàng
	 * @param string $transaction_info: Tham số bổ sung, bạn có thể dùng để lưu các tham số tuỳ ý để cập nhật thông tin khi NgânLượng.vn trả kết quả về
	 * @param string $order_code: Mã hoá đơn/Tên sản phẩm
	 * @param int $price: Tổng tiền phải thanh toán
	 * @return string
	 */
	public function buildCheckoutUrl($return_url, $receiver, $transaction_info, $order_code, $price)
	{
		// Bước 1. Mảng các tham số chuyển tới nganluong.vn
		$arr_param = array(
			'merchant_site_code'=>	strval($this->merchant_id),
			'return_url'		=>	strtolower(urlencode($return_url)),
			'receiver'			=>	strval($receiver),
			'transaction_info'	=>	strval($transaction_info),
			'order_code'		=>	strval($order_code),
			'price'				=>	strval($price)					
		);
		$secure_code ='';
		$secure_code = implode(' ', $arr_param) . ' ' . $this->merchant_password;
		$arr_param['secure_code'] = md5($secure_code);
		
		/* Bước 2. Kiểm tra  biến $redirect_url xem có '?' không, nếu không có thì bổ sung vào*/
		$redirect_url = $this->url_api;
		if (strpos($redirect_url, '?') === false)
		{
			$redirect_url .= '?';
		}
		else if (substr($redirect_url, strlen($redirect_url)-1, 1) != '?' && strpos($redirect_url, '&') === false)
		{
			// Nếu biến $redirect_url có '?' nhưng không kết thúc bằng '?' và có chứa dấu '&' thì bổ sung vào cuối
			$redirect_url .= '&';			
		}
				
		/* Bước 3. tạo url*/
		$url = '';
		foreach ($arr_param as $key=>$value)
		{
			if ($key != 'return_url') $value = urlencode($value);
			
			if ($url == '')
				$url .= $key . '=' . $value;
			else
				$url .= '&' . $key . '=' . $value;
		}		
		return $redirect_url.$url;
	}
	
	/**
	 * HÀM KIỂM TRA TÍNH ĐÚNG ĐẮN CỦA ĐƯỜNG LINK KẾT QUẢ TRẢ VỀ TỪ NGÂNLƯỢNG.VN
	 *
	 * @param string $transaction_info: Thông tin về giao dịch, Giá trị do website gửi sang
	 * @param string $order_code: Mã hoá đơn/tên sản phẩm
	 * @param string $price: Tổng tiền đã thanh toán
	 * @param string $payment_id: Mã giao dịch tại NgânLượng.vn
	 * @param int $payment_type: Hình thức thanh toán: 1 - Thanh toán ngay (tiền đã chuyển vào tài salonản NgânLượng.vn của người bán); 2 - Thanh toán Tạm giữ (tiền người mua đã thanh toán nhưng NgânLượng.vn đang giữ hộ)
	 * @param string $error_text: Giao dịch thanh toán có bị lỗi hay không. $error_text == "" là không có lỗi. Nếu có lỗi, mô tả lỗi được chứa trong $error_text
	 * @param string $secure_code: Mã checksum (mã kiểm tra)
	 * @return unknown
	 */
	
	public function verifyPaymentUrl($transaction_info, $order_code, $price, $payment_id, $payment_type, $error_text, $secure_code)
	{
		// Tạo mã xác thực từ chủ web
		$str = '';
		$str .= ' ' . strval($transaction_info);
		$str .= ' ' . strval($order_code);
		$str .= ' ' . strval($price);
		$str .= ' ' . strval($payment_id);
		$str .= ' ' . strval($payment_type);
		$str .= ' ' . strval($error_text);
		$str .= ' ' . strval($this->merchant_id);
		$str .= ' ' . strval($this->merchant_password);

        // Mã hóa các tham số
		$verify_secure_code = '';
		$verify_secure_code = md5($str);
		
		// Xác thực mã của chủ web với mã trả về từ nganluong.vn
		if ($verify_secure_code === $secure_code) return true;
		else return false;
	}
	function GetTransactionDetailss($token)
	{
				###################### BEGIN #####################
						$params = array(
							'merchant_id'       => $this->merchant_id ,
							'merchant_password' => MD5($this->merchant_password),
							'version'           => Config::$_VERSION,
							'function'          => 'GetTransactionDetail',
							'token'             => $token
						);						
						$api_url = Config::$_URL_SERVICE;
						$post_field = '';
						foreach ($params as $key => $value){
							if ($post_field != '') $post_field .= '&';
							$post_field .= $key."=".$value;
						}
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL,$api_url);
						curl_setopt($ch, CURLOPT_ENCODING , 'UTF-8');
						curl_setopt($ch, CURLOPT_VERBOSE, 1);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
						curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field);
						$result = curl_exec($ch);
						$status = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
						$error = curl_error($ch);
						if ($result != '' && $status==200){
							$nl_result  = simplexml_load_string($result);						
							return $nl_result;
						}
						
						return false;
				###################### END #####################
		  
		  }		
}
?>