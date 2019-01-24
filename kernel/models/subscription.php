<?php
if (!defined('IN_ROOT')) exit();

$CMS->subscription = new subscription1;
class subscription1
{
	public $show_page=0;
	public $CMS = "";
	public $sql_query = "";
	public $sql_query_bk = "";
	public $arrange_data = "";
	public $record_cnt = 0;
	public $control = 0;
	public $total = 0;
	public $sql_add = "";
	public $action_control = "";
	public $data_array = array();	 
	public $per_page = 10;
	public $prefix_html = "";
	public $suffix_html = "";
	public $html;

	public function loadHtml()
	{
		global $CMS, $DB;

		if ( $CMS->vars['is_admin_module'] )
		{
			$this->html = $CMS->class->template->load_simple(\core\ezy::$app_dir."/modules/upgrade/templates/skin_subscription.php");
		}
		else
		{
			$this->html = $CMS->class->template->load_template("skin_subscription");
		}
	}

	public function getPackageList($pName = '')
	{
		global $CMS;

		// package data
		// payment_type 0:tháng
		$package_list = array();
		$package_list[] = array(
				'package_name' => 'basic',
				'package_price' => '0',
				'payment_cycle' => '1,6,12',
				'payment_type' => '0',
				'currency' => 'usd',
				'package_info' => array(
						'full_featured' => '1',
						'admin_account' => '2',
						'storage_capacity' => '200MB'
					),
				'package_suggest' => '0'
			);
		$package_list[] = array(
				'package_name' => 'standard',
				'package_price' => '10',
				'payment_cycle' => '1,6,12',
				'payment_type' => '0',
				'currency' => 'usd',
				'package_info' => array(
						'full_featured' => '1',
						'admin_account' => '10',
						'storage_capacity' => '5GB'
					),
				'package_suggest' => '0'
			);
		$package_list[] = array(
				'package_name' => 'professional',
				'package_price' => '50',
				'payment_cycle' => '1,6,12',
				'payment_type' => '0',
				'currency' => 'usd',
				'package_info' => array(
						'full_featured' => '1',
						'admin_account' => '20',
						'storage_capacity' => '50GB'
					),
				'package_suggest' => '1'
			);
		$package_list[] = array(
				'package_name' => 'enterprise',
				'package_price' => '100',
				'payment_cycle' => '1,6,12',
				'payment_type' => '0',
				'currency' => 'usd',
				'package_info' => array(
						'full_featured' => '1',
						'admin_account' => '50',
						'storage_capacity' => '200GB'
					),
				'package_suggest' => '0'
			);

		if($CMS->vars['currency_type'] == 'đ') //VND
        {
            foreach ($package_list as $k => $p)
            {
                $p['package_price'] = $p['package_price']*23000;
                $p['currency'] = 'vnd';
                $package_list[$k] = $p;
            }
        }

		if($pName)
        {
            foreach ($package_list as $p)
            {
                if($p['package_name'] == $pName) return $p;
            }
        }

		return $package_list;
	}

	public function add ($data)
	{
		global $CMS, $DB, $member;

		if ( empty($data) )
		{
			return false;
		}

        $sub_time = time();

		$site_id = $CMS->vars['siteInfo']['site_id'];

		$sql = "INSERT INTO ".root_table."subscription (sub_name, sub_comment, payment_method, payment_status, order_key, user_id, sub_time, site_id, sub_license_package, sub_license_expired, sub_license_cycle, sub_total) VALUES ('{$data['sub_name']}', '{$data['sub_comment']}', '{$data['payment_method']}', '{$data['payment_status']}', '{$data['order_key']}', '{$data['user_id']}', '{$sub_time}', '{$site_id}', '{$data['sub_license_package']}', '{$data['sub_license_expired']}', '{$data['sub_license_cycle']}', '{$data['sub_total']}')";

		if($DB->query($sql))
        {
            return $DB->last_insert_id();
        }

		return false;
	}

	public function complete($sub_id, $sub_comment, $sub_status){
        global $CMS, $DB;

        $sub_id= intval($sub_id);
        $sub_status = intval($sub_status);
        $sub_comment= trim($sub_comment);

        $sql = "UPDATE ".root_table."subscription SET sub_comment='{$sub_comment}', payment_status='{$sub_status}' WHERE sub_id='{$sub_id}'";

        return $DB->query($sql);
    }


    public function api_add ($data)
	{
		global $CMS, $DB, $member;

		if ( empty($data) )
		{
			return false;
		}

        $sub_time = time();

		$site_id = $CMS->vars['siteInfo']['site_id'];

		$sql = "INSERT INTO ".root_table."subscription (sub_name, sub_comment, payment_method, payment_status, order_key, sub_time, site_id, sub_license_package, sub_license_expired, sub_license_cycle, sub_total) VALUES ('{$data['sub_name']}', '{$data['sub_comment']}', '{$data['payment_method']}', '1', '{$data['order_key']}', '{$sub_time}', '{$data['site_id']}', '{$data['sub_license_package']}', '{$data['sub_license_expired']}', '{$data['sub_license_cycle']}', '{$data['sub_total']}')";

		if($DB->query($sql))
        {
            return $DB->last_insert_id();
        }

		return false;
	}



	public function update_site_package($data)
	{
		global $CMS,  $member;

		if ( empty($data) )
		{
			return false;
		}

		$sql = "UPDATE ". root_table ."sites SET site_license_package = '{$data['site_license_package']}', site_license_cycle = '{$data['site_license_cycle']}', site_license_expired = '{$data['site_license_expired']}', site_license_total = '{$data['site_license_total']}' WHERE site_id = '{$data['site_id']}'";
		$sql = $DB->query($sql);

		// Delete cache
  		$CMS->class->cache->delete("site_{$data['site_id']}");
		return true;
	}

	public function payment()
	{
		global $CMS;

		switch ( $CMS->input['payment_method'] )
		{
			case 'free':
				$this->payment_free();
			break;

			case '10':
			case '11':
			case '12':
			case '13':
			case '14':
				$this->payment_nganluong();
			break;

			// case '20':
			// 	$this->payment_baokim();
			// break;

			default:
				$_SESSION['error_msg'] .= $CMS->lang['payment_method_not_found'] . "<br/>";
				return false;
			break;
		}
	}

	public function payment_success()
	{
		global $CMS;

		if ( !$_SESSION['cart'] )
		{
			$_SESSION['error_msg'] .= $CMS->lang['cart_is_empty'] . "<br/>";
			$CMS->global->redirect($CMS->vars['root_domain']);
		}

		switch ( $_SESSION['payment']['method'] )
		{
			case '10':
			case '11':
			case '12':
			case '13':
			case '14':
				$this->payment_nganluong_success();
			break;

			// case '2':
			// 	$this->payment_baokim_success();
			// break;

			default:
				$_SESSION['error_msg'] .= $CMS->lang['payment_method_not_found'] . "<br/>";
				return false;
			break;
		}
	}

	public function payment_cancel()
	{
		global $CMS, $DB, $member;

		// inputs
		$user_id = $member['user_id'];
		$payment_method = ($_SESSION['payment']['method'] == 1)? 'nganluong.vn' : 'baokim.vn';

		// Package info
		$package_name = $_SESSION['cart']['info']['package_name'];
		$package_cycle = $_SESSION['cart']['info']['package_cycle'];

		// subscription info
		$payment_status = 4; //cancel

        $this->complete($_SESSION['cart']['sub_id'], '', $payment_status);

		// display payment
		$_SESSION['payment_result'] = array(
			'payment_method' => 'Thanh toán qua cổng thanh toán điện tử ' . $payment_method,
			'payment_title' => '<span class="text-danger">đã bị huỷ</span>',
			'payment_status' => $CMS->lang["payment_status_{$payment_status}"],
			'package_name' => $package_name,
			'package_cycle' => $package_cycle
		);

		// reset payment and cart
		$_SESSION['payment'] = array();
		$_SESSION['cart'] = array();

		// redirect
		$CMS->global->redirect($CMS->vars['root_domain'] . '/?site=subscription&act=payment_result');
	}

	public function payment_free()
	{
		global $CMS, $member;

		if ( $_SESSION['cart']['total_amount'] == 0 )
		{
			// Package info
			$package_name = $_SESSION['cart']['info']['package_name'];
			$package_cycle = $_SESSION['cart']['info']['package_cycle'];
			$package_expired =  ($CMS->class->date->month + $package_cycle) . '/' . $CMS->class->date->day . '/'. $CMS->class->date->year;
			$package_expired = $CMS->class->date->date2time($package_expired);
			// Update upgrade package for user
			$site_package = array(
					'site_license_package' => $package_name,
					'site_license_cycle' => 0,
					'site_license_expired' => 0,
					'site_license_total' => 0,
					'site_id' => $CMS->vars['siteInfo']['site_id']
				);

			$this->update_site_package($site_package);

			// save transaction to table subscription
			$sub_name = 'Miễn phí: ' . $CMS->lang['upgrade_sub_name'] . ' ' . $package_name . ' thời hạng ' . $package_cycle . ' tháng';

			$subscription = array(
					'sub_name' => $sub_name,
					'sub_comment' => json_encode([]),
					'payment_method' => '0', 
					'payment_status' => '1', 
					'order_key' => $_SESSION['cart']['key'],
					'user_id' => $member['user_id'], 
					'sub_time' => time(),
                    'sub_license_package' => $package_name,
                    'sub_license_cycle' => $package_cycle,
                    'sub_license_expired' => $package_expired,
                    'sub_total' => $_SESSION['cart']['info']['package_total'],
				);
			$this->add($subscription);

			// display payment
			$_SESSION['payment_result'] = array(
				'payment_method' => 'Thanh toán miễn phí',
				'payment_title' => '<span class="text-success">thành công</span>',
				'payment_status' => $CMS->lang["payment_status_1"],
				'package_name' => $package_name,
				'package_cycle' => $package_cycle
			);

			// reset payment and cart
			$_SESSION['payment'] = array();
			$_SESSION['cart'] = array();

			// redirect
			$_SESSION['msg'] .= "Thành công<br/>";
			$CMS->global->redirect($CMS->vars['root_domain'] . '/?site=subscription&act=payment_result');
		}

		// redirect
		$_SESSION['error_msg'] .= $CMS->lang['cart_error_compare'] . "<br/>";
		$CMS->global->redirect($CMS->vars['root_domain']);
	}

	public function payment_nganluong()
	{
		global $CMS, $member;

		// test: online thì bỏ đi (quang trọng)
		// $_SESSION['cart']['total_amount'] = 2000;
		//end
		
		// Cart info
		$order_code = $_SESSION['cart']['key'];
		$total_amount = $_SESSION['cart']['total_amount'];
		$array_items = array(
			'0' => array(
				'item_name1' => $_SESSION['cart']['info']['package_name'],
				'item_quantity1' => '1',
				'item_amount1' => $_SESSION['cart']['info']['package_total'],
				'item_url1' => ''
			));
		$order_description = 'Nâng cấp gói ' . $_SESSION['cart']['info']['package_name'] . ' - ' . $_SESSION['cart']['info']['package_cycle'] . ' tháng';

		$buyer_fullname = $CMS->input['payer_name'];
		$buyer_email = $CMS->input['payer_email'];
		$buyer_mobile =  $CMS->input['payer_phone'];
		$buyer_address =  $CMS->input['payer_address'];

		$bank_code = $CMS->input['bankcode'];

		// check inputs
		if ( !$buyer_fullname )
		{
			$_SESSION['error_msg'] .= $CMS->lang['buyer_fullname_not_found'] . "<br/>";
			return false;
		}

		if ( !$buyer_mobile )
		{
			$_SESSION['error_msg'] .= $CMS->lang['buyer_mobile_not_found'] . "<br/>";
			return false;
		}

		/*if ( $CMS->class->input->is_nan($buyer_mobile) )
		{
			$_SESSION['error_msg'] .= $CMS->lang['buyer_mobile_invalid'] . "<br/>";
			return false;
		}*/

		if ( !$buyer_email )
		{
			$_SESSION['error_msg'] .= $CMS->lang['buyer_email_not_found'] . "<br/>";
			return false;
		}

		if ( !$CMS->class->input->is_email($buyer_email) )
		{
			$_SESSION['error_msg'] .= $CMS->lang['buyer_email_invalid'] . "<br/>";
			return false;
		}

		if ( $total_amount <= 0 )
		{
			$_SESSION['error_msg'] .= $CMS->lang['payment_total_amount_invalid'] . "<br/>";
			return false;
		}

		if ( $CMS->input['payment_method'] != '10' AND !$bank_code )
		{
			$_SESSION['error_msg'] .= $CMS->lang['payment_bank_code_invalid'] . "<br/>";
			return false;
		}

		// params
		$params = array(
				'cur_code' => 'vnd', // default vnd : usd
				'bank_code' => $bank_code,

				// order info
				'order_code' => $order_code,
				'total_amount' => $total_amount,
				'array_items' => $array_items,
				'order_description' => $order_description,

				'tax_amount' => '0',
				'fee_shipping' => '0',
				'discount_amount' => '0',

				// buyer info
				'buyer_fullname' => $buyer_fullname,
				'buyer_email' => $buyer_email,
				'buyer_mobile' => $buyer_mobile,
				'buyer_address' => $buyer_address,

				'return_url' => $CMS->vars['root_domain'] . '/subscription-payment-success.html',
				'cancel_url' => urlencode($CMS->vars['root_domain'] . '/subscription-payment-cancel.html'),
				'payment_type' => '1',
			);


        $package_expired =  ($CMS->class->date->month + $_SESSION['cart']['package_cycle']) . '/' . $CMS->class->date->day . '/'. $CMS->class->date->year;
        $package_expired = $CMS->class->date->date2time($package_expired);

		//add subscription
        $_SESSION['cart']['sub_id'] = $this->add([
            'sub_name' => $params['order_description'],
            'payment_method' => $CMS->input['payment_method'],
            'payment_status' => 0,
            'order_key' => $params['order_code'],
            'user_id' => $member['user_id'],
            'sub_time' => time(),
            'sub_license_package' => $_SESSION['cart']['info']['package_name'],
            'sub_license_cycle' => $_SESSION['cart']['info']['package_cycle'],
            'sub_license_expired' => $package_expired,
            'sub_total' => $_SESSION['cart']['info']['package_total'],
        ]);

        if(!$_SESSION['cart']['sub_id'])
        {
            $_SESSION['msg'] = "Không thể tạo giao dịch";
            return false;
        }

        $_SESSION['cart']['sublog_id'] = $CMS->subscription_log->addRequest([
            'sub_id' => $_SESSION['cart']['sub_id'],
            'sublog_request' => @json_encode($params, JSON_UNESCAPED_UNICODE),
            'sublog_gateway' => 'ngan_luong'
        ]);

		if ( $CMS->input['payment_method'] == '10' )
		{
			// Ví điện tử Ngân Lượng
			$nl_result = $CMS->api->nganluong->NLCheckout($params);
		}
		else if ( $CMS->input['payment_method'] == '11' )
		{
			// Thẻ ngân hàng nội địa (ATM ONLINE)
			$nl_result = $CMS->api->nganluong->BankCheckout($params);
		}
		else if ( $CMS->input['payment_method'] == '12' )
		{
			// Thẻ ngân hàng nội địa (INTERNET BANKING)
			$nl_result = $CMS->api->nganluong->IBCheckout($params);
		}
		else if ( $CMS->input['payment_method'] == '13' )
		{
			// Thẻ Visa/MasterCard
			$nl_result = $CMS->api->nganluong->VisaCheckout($params);
		}
		else if ( $CMS->input['payment_method'] == '14' )
		{
			// Thẻ Visa/MasterCard trả trước
			$nl_result = $CMS->api->nganluong->PrepaidVisaCheckout($params);
		}

		if ( $nl_result->error_code == '00' )
		{
			// token is unique for one transaction
			// save session payment
			$_SESSION['payment']['method'] = $CMS->input['payment_method'];
			$_SESSION['payment']['bankcode'] = $params['bank_code'];
			$_SESSION['payment']['token'] = (string)$nl_result->token;
			$_SESSION['payment']['currency'] = $params['cur_code'];

			// redirect
			$CMS->global->redirect((string)$nl_result->checkout_url);
		}
		else
		{
			// create log
			$logs_info = array(
					'time' => time(),
					'params' => $params,
					'error_code' => (string)$nl_result->error_code,
					'error_message' => (string)$nl_result->error_message,
					'error_description' => (string)$nl_result->description,
				);
			$CMS->class->logs->key = 'nl_payment_error_' . $member['user_id'];
			$CMS->class->logs->keep_content = 1;
			$CMS->class->logs->insert('Lỗi xác thực thanh toán', serialize($logs_info));

			// redirect
			$_SESSION['error_msg'] .= $logs_info['error_message'] ? $logs_info['error_message']  . "<br/>" : $logs_info['error_description'] . "<br/>";
			$CMS->global->redirect($CMS->vars['root_domain'] . '/?site=subscription&act=payment');
		}
	}

	public function payment_nganluong_success()
	{
		global $CMS, $DB, $member;

		// Get data
		$url = explode("?",$_SERVER['REQUEST_URI']);
		$url = explode("&",$url[1]);		
		for ( $i = 0; $i < count($url); $i++ )
		{
			$url2 = explode( "=", $url[$i] );
			$CMS->input[$url2[0]] = $url2[1];
		}

		if ( $CMS->input['token'] == $_SESSION['payment']['token'] )
		{
			$nl_result = $CMS->api->nganluong->GetTransactionDetail($CMS->input['token']);
			if ( $nl_result AND (string)$nl_result->error_code == '00' )
			{
				// inputs
				$order_key = $_SESSION['cart']['key'];

				// Package info
				$package_name = $_SESSION['cart']['info']['package_name'];
				$package_cycle = $_SESSION['cart']['info']['package_cycle'];

				// order info
				$order_info = array(
						'package_name' => $package_name,
						'package_cycle' => $package_cycle,
					);

				// Is paid
				if ( (string)$nl_result->transaction_status == '00' AND $order_key == (string)$nl_result->order_code AND $_SESSION['cart']['total_amount'] == (string)$nl_result->total_amount )
				{
					$payment_status = 1;

                    $package_expired =  ($CMS->class->date->month + $package_cycle) . '/' . $CMS->class->date->day . '/'. $CMS->class->date->year;
					$package_expired = $CMS->class->date->date2time($package_expired);

					$order_info['package_expired'] = $package_expired;

					// Update upgrade package for user
					$site_package = array(
							'site_license_package' => $package_name,
							'site_license_cycle' => $package_cycle,
							'site_license_expired' => $package_expired,
							'site_license_total' => $_SESSION['cart']['info']['package_total'],
							'site_id' => $CMS->vars['siteInfo']['site_id']
						);
					$this->update_site_package($site_package);
				}
				else
                {
                    $payment_status = 3; //failed
                }

				// save transaction to table subscription

				$sub_comment = json_encode($nl_result, JSON_UNESCAPED_UNICODE);

                $this->complete($_SESSION['cart']['sub_id'], serialize($sub_comment), $payment_status);

				// display payment
				$_SESSION['payment_result'] = array(
					'payment_method' => $CMS->lang['payment_method' . $_SESSION['payment']['method']],
					'payment_title' => '<span class="text-success">thành công</span>',
					'payment_status' => $CMS->lang["payment_status_{$payment_status}"],
					'package_name' => $package_name,
					'package_cycle' => $package_cycle
				);
			}

			// reset payment and cart
			$_SESSION['payment'] = array();
			$_SESSION['cart'] = array();

			// redirect
			if ( (string)$nl_result->error_code == '00' )
			{
				$_SESSION['msg'] .= $CMS->api->nganluong->GetErrorMessage((string)$nl_result->error_code) . "<br/>";
			}
			else
			{
				$_SESSION['error_msg'] .= $CMS->api->nganluong->GetErrorMessage((string)$nl_result->error_code) . "<br/>";
			}
			$CMS->global->redirect($CMS->vars['root_domain'] . '/?site=subscription&act=payment_result');
		}

		// redirect
		$_SESSION['error_msg'] .= $CMS->lang['cart_error_compare'] . "<br/>";
		$CMS->global->redirect($CMS->vars['root_domain']);
	}
	
	// public function payment_baokim()
	// {
	// 	global $CMS, $DB, $member;

	// 	// test: online thì bỏ đi (quang trọng)
	// 	$_SESSION['cart']['total_amount'] = 10000;
	// 	//end

	// 	// Cart info
	// 	$order_id = $_SESSION['cart']['key'];
	// 	$total_amount = $_SESSION['cart']['total_amount'];
	// 	$order_description = 'Nâng cấp gói ' . $_SESSION['cart']['info']['package_name'] . ' - ' . $_SESSION['cart']['info']['package_cycle'] . ' tháng';

	// 	$payer_name = $member['user_display_name'];
	// 	$payer_email = $member['user_email'];
	// 	$payer_phone_no = $member['user_phone'];
	// 	$shipping_address = $member['user_address'];

	// 	// check inputs
	// 	if ( !$payer_name )
	// 	{
	// 		$_SESSION['error_msg'] .= $CMS->lang['buyer_fullname_not_found'] . "<br/>";
	// 		return false;
	// 	}

	// 	if ( !$payer_phone_no )
	// 	{
	// 		$_SESSION['error_msg'] .= $CMS->lang['buyer_mobile_not_found'] . "<br/>";
	// 		return false;
	// 	}

	// 	if ( $CMS->class->input->is_nan($payer_phone_no) )
	// 	{
	// 		$_SESSION['error_msg'] .= $CMS->lang['buyer_mobile_invalid'] . "<br/>";
	// 		return false;
	// 	}

	// 	if ( !$payer_email )
	// 	{
	// 		$_SESSION['error_msg'] .= $CMS->lang['buyer_email_not_found'] . "<br/>";
	// 		return false;
	// 	}

	// 	if ( !$CMS->class->input->is_email($payer_email) )
	// 	{
	// 		$_SESSION['error_msg'] .= $CMS->lang['buyer_email_invalid'] . "<br/>";
	// 		return false;
	// 	}

	// 	if ( $total_amount <= 0 )
	// 	{
	// 		$_SESSION['error_msg'] .= $CMS->lang['payment_total_amount_invalid'] . "<br/>";
	// 		return false;
	// 	}

	// 	// params
	// 	$params = array(
	// 			// order info
	// 			'order_id' => $order_id,
	// 			'total_amount' => $total_amount,
	// 			'order_description' => $order_description,

	// 			'tax_fee' => '0',
	// 			'shipping_fee' => '0',

	// 			// buyer info
	// 			'payer_name' => $payer_name,
	// 			'payer_email' => $payer_email,
	// 			'payer_phone_no' => $payer_phone_no,
	// 			'shipping_address' => $shipping_address,

	// 			'return_url' => $CMS->vars['root_domain'] . '/?site=subscription&act=payment_success',
	// 			'cancel_url' => urlencode($CMS->vars['root_domain'] . '/?site=subscription&act=payment_cancel'),
	// 		);

	// 	// save session payment
	// 	$_SESSION['payment']['method'] = '2';
	// 	$_SESSION['payment']['token'] = '';

	// 	// redirect
	// 	$CMS->global->redirect($CMS->api->baokim->createRequestUrl($params));
	// }

	// public function payment_baokim_success()
	// {
	// 	global $CMS, $DB, $member;

	// 	//lưu các giá trị gửi về từ bảo kim
	// 	$url_params = array();

	// 	$url = explode("?",$_SERVER['REQUEST_URI']);
	// 	$url = explode("&",$url[1]);
	// 	for ( $i = 0; $i < count($url); $i++ )
	// 	{
	// 		$url2 = explode( "=", $url[$i] );
	// 		$url_params[$url2[0]] = $url2[1];
	// 	}

	// 	if ( $url_params['order_id'] == $_SESSION['cart']['key'] )
	// 	{
	// 		// inputs
	// 		$order_key = $_SESSION['cart']['key'];
	// 		$payment_status = 2; // chờ bpn

	// 		// save transaction to table subscription
	// 		$sub_name = $CMS->lang['upgrade_sub_name'];
	// 		$sub_comment = array(
	// 				'TotalAmount' => $_SESSION['cart']['total_amount'],

	// 				'TransactionId' => $url_params['transaction_id'],
	// 				'TransactionStatus' => $url_params['transaction_status'],

	// 				'OrderInfo' => array(
	// 						'package_name' => $_SESSION['cart']['info']['package_name'],
	// 						'package_cycle' => $_SESSION['cart']['info']['package_cycle'],
	// 					),

	// 				'Results' => $url_params
	// 			);
	// 		$sub_comment = serialize($sub_comment);
	// 		$sub_time = time();
			
	// 		$sql = "INSERT INTO ".root_table."subscription (sub_name, sub_comment, payment_method, payment_status, order_key, user_id, sub_time) VALUES ('{$sub_name}', '{$sub_comment}', '2', '{$payment_status}', '{$order_key}', '{$member['user_id']}', '{$sub_time}')";
	// 		$sql = $DB->query($sql);

	// 		// display payment
	// 		$_SESSION['payment_result'] = array(
	// 			'payment_method' => 'Thanh toán qua cổng thanh toán điện tử baokim.vn',
	// 			'payment_title' => '<span class="text-success">thành công</span>',
	// 			'payment_status' => $CMS->lang["payment_status_{$payment_status}"],
	// 			'package_name' => $_SESSION['cart']['info']['package_name'],
	// 			'package_cycle' => $_SESSION['cart']['info']['package_cycle'],
	// 		);

	// 		// reset payment and cart
	// 		$_SESSION['payment'] = array();
	// 		$_SESSION['cart'] = array();

	// 		// redirect
	// 		$CMS->global->redirect($CMS->vars['root_domain'] . '/?site=subscription&act=payment_result');
	// 	}

	// 	// redirect
	// 	$_SESSION['error_msg'] .= $CMS->lang['cart_error_compare'] . "<br/>";
	// 	$CMS->global->redirect($CMS->vars['root_domain']);
	// }

	// public function verify_baokim_BPN()
	// {
	// 	global $CMS, $DB, $member;

	// 	$url_params = $_POST;

	// 	if ( $url_params['verify_sign'] )
	// 	{
	// 		//tạo chuổi gửi lại cho bảo kim verify
	// 		$req = '';
	// 		foreach ( $url_params as $key => $value )
	// 		{
	// 			$value = urlencode ( stripslashes ( $value ) );
	// 			$req .= "&$key=$value";
	// 		}

	// 		//gọi api check BPN
	// 		$bk_result = $CMS->api->baokim->veyfyBPN($req);
	// 		if( $bk_result['result'] AND strstr($bk_result['result'],'VERIFIED') AND $bk_result['status'] == 200 )
	// 		{
	// 			$CMS->class->language->load("subscription");

	// 			$sql = "SELECT * FROM " . root_table . "subscription WHERE order_key = '{$bk_result['order_id']}' AND payment_status = '2' AND payment_method = '2' LIMIT 1";
	// 			$sql = $DB->query($sql);
	// 			if ( $DB->num_rows($sql) > 0 )
	// 			{
	// 				$data_record = $DB->fetch_array($sql);
					
	// 				// order info
	// 				$sub_comment = unserialize($data_record['sub_content']);
	// 				$package_name = $sub_comment['OrderInfo']['package_name'];
	// 				$package_cycle = $sub_comment['OrderInfo']['package_cycle'];

	// 				$payment_status = 0;

	// 				// Is paid
	// 				// online delete 13: thanh toán tạm giữ
	// 				if ( $bk_result['net_amount'] == $sub_comment['TotalAmount'] AND ($bk_result['transaction_status'] == 4 OR $bk_result['transaction_status'] == 13) )
	// 				{
	// 					$payment_status = 1;

	// 					$package_expired = $CMS->class->date->day . '/' . ($CMS->class->date->month + $package_cycle) . '/' . $CMS->class->date->year;
	// 					$package_expired = $CMS->class->date->date2time($package_expired);

	// 					$sub_comment['OrderInfo']['package_expired'] = $package_expired;

	// 					// Update upgrade package for user
	// 					$sql = "UPDATE ". root_table ."user SET user_license_package = '{$package_name}', user_license_cycle = '{$package_cycle}', user_license_expired = '{$package_expired}' WHERE user_id = '{$data_record['user_id']}'";
	// 					$sql = $DB->query($sql);
	// 				}

	// 				// update transaction to table subscription
	// 				$sub_comment['TransactionStatus'] = $CMS->lang["payment_status_{$payment_status}"];
	// 				$sub_comment['BpnResults'] = $bk_result;
	// 				$sub_comment['BpnResultsTime'] = time();
	// 				$sub_comment = serialize($sub_comment);
					
	// 				$sql = "UPDATE ".root_table."subscription SET sub_comment = '{$sub_comment}', payment_status = '{payment_status}' WHERE sub_id = '{$data_record['sub_id']}'";
	// 				$sql = $DB->query($sql);
	// 			}
	// 		}
	// 	}
	// }

    public function cancelPackage()
    {
        global $CMS,   $member;

        $basicPackage = $this->getPackageList()[0];

        // Update upgrade package for user
        $site_package = array(
            'site_license_package' => $basicPackage['package_name'],
            'site_license_cycle' => 0,
            'site_license_expired' => 0,
            'site_license_total' => 0,
            'site_id' => $CMS->vars['siteInfo']['site_id']
        );

        $this->update_site_package($site_package);

        $subscription = array(
            'sub_name' => "Hạ cấp xuống gói free",
            'sub_comment' => json_encode([]),
            'payment_method' => '0',
            'payment_status' => '1',
            'order_key' => "SUB{$CMS->vars['siteInfo']['site_id']}_".time(),
            'user_id' => $member['user_id'],
            'sub_time' => time(),
            'sub_license_package' => $basicPackage['package_name'],
            'sub_license_cycle' => 0,
            'sub_license_expired' => 0,
            'sub_total' => 0,
        );
        $this->add($subscription);


        $sql = "UPDATE ".root_table."sites SET site_license_package='{$basicPackage['package_name']}' WHERE site_id='{$CMS->vars['siteInfo']['site_id']}'";

        return $DB->query($sql);
    }

    public function listing()
    {
        global $CMS, $DB;

        $this->arrange_data = trim("sub_id,sub_time");
        $default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "sub_id";
        $default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "DESC";

        list($this->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM `".root_table."subscription` WHERE {$this->sql_add} 1=1 ORDER BY {$default_field} {$default_order}", $this->per_page, $this->prefix_html, $this->suffix_html);

        $arr = array();

        $this->num_rows = $DB->num_rows($this->sql_query);

        if ($this->num_rows>0) {
            while ($result = $DB->fetch_assoc($this->sql_query)) {
                array_push($arr, $result);
            }
        }

        return $arr;
    }

    public function convertvalue($data)
    {
        global $CMS;
        $data['data_bk'] = $data_bk = $data;
        $data['payment_method'] = $CMS->lang['payment_method_'.$data_bk['payment_method']];
        $data['sub_time'] = \lib\date::format($data['sub_time']);
        $data['sub_total'] = $CMS->class->input->currency($data['sub_total']);
        $data['payment_status'] = $CMS->lang['payment_status_'.$data_bk['payment_status']];
        return $data;
    }

    public function checkValidPackage($checkPackName, $curCheckName)
    {
        global $CMS;

        $packLevel = ['basic', 'standard', 'professional', 'enterprise'];

        $checkIndex = array_search($checkPackName, $packLevel);
        $curIndex = array_search($curCheckName, $packLevel);

        if($checkIndex <= $curIndex) return false;

        return true;
    }
}
?>