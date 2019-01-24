<?php
namespace controller;

use core\ezy;

ezy::load_model('nhanhoa'); 

class contact {
    static public function auto_run() {
		global $CMS, $tpl;
		$CMS->class->language->load("contact");

		// SEO
        $CMS->lang['website_title'] = 'Thiết kế website bán hàng - doanh nghiệp - bất động sản giá rẻ';
        $CMS->lang['seo_description'] = 'Web4s - Dịch vụ thiết kế website bán hàng, website doanh nghiệp, website bất động sản giá rẻ, đạt chuẩn SEO, tích hợp quản lý sản phẩm, thanh toán online';
        $CMS->lang['seo_keyword'] = 'thiết kế website, website bán hàng, website doanh nghiệp, website bất động sản, website giá rẻ, seo website, website trọn gói';
        $CMS->lang['seo_author'] = 'Công ty TNHH phần mềm Nhân Hòa';
        
		$CMS->vars['website_title'] = $CMS->lang['website_title'];
		$CMS->vars['seo_description'] = $CMS->lang['seo_description'];
		$CMS->vars['seo_keyword'] = $CMS->lang['seo_keyword'];
		$CMS->vars['seo_author'] = $CMS->lang['seo_author'];

		$CMS->vars['og_title'] = $CMS->lang['website_title'];
		$CMS->vars['og_description'] = $CMS->lang['seo_description'];

		$CMS->vars['dc_title'] = $CMS->lang['website_title'];
		$CMS->vars['dc_description'] = $CMS->lang['seo_description'];
		$CMS->vars['dc_subject'] = $CMS->lang['website_title'];
		// END SEO

		switch (ezy::$act) {
			case 'contact_recall_ajax':
				self::contactReCallAjax();
				break;

			case 'contact_phone_ajax':
				self::contactPhoneAjax();
				break;

			case 'register_content_services':
				self::registerContentServices();
				break;

			default:
                self::main();
				break;
        }
    }
	protected function main() {
		global $CMS, $tpl;
		if ($CMS->input['request_method'] == 'post') {
			$msg = \models\contact::sendContact();
			if($msg['status'] == "error") {
				$tpl->error = $msg['message'];
			} else {
				$tpl->success = $msg['message'];
				$CMS->input['contactname'] = '';
				$CMS->input['contactemail'] = '';
				$CMS->input['contactsubject'] = '';
				$CMS->input['contactcontent'] = '';
			}
		}
        echo ezy::html('main');
	}
	protected function contactPhoneAjax() {
		global $CMS;
		$reponse = array('status' => 'error', 'message' => $CMS->lang['phone_err']);
		if ($CMS->input['request_method'] == 'post') {
			if (preg_match('/^[0-9]{1,15}$/', $CMS->input['contact_phone'])) {
				$data['cus_phone'] = $CMS->input['contact_phone'];
				$api = \models\nhanhoa::api('recall', $data);
				if ($api['status'] == 'success') {
					$reponse['status'] = 'success';
					$reponse['message'] = $CMS->lang['contact_success'];
				} else {
					$reponse['status'] = 'error';
					$reponse['message'] = $api['message'];
				}
				// $CMS->input['contactname'] = 'admin';
				// $CMS->input['contactemail'] = 'admin@eco.dev';
				// $CMS->input['contactsubject'] = 'Gọi lại cho khách hàng';
				// $CMS->input['contactcontent'] = 'Gọi lại sđt: ' . $CMS->input['contact_phone'];
				// $msg = \models\contact::sendContact();
				// if ($msg['status'] == 'error') {
					// $reponse['status'] = 'error';
					// $reponse['message'] = $msg['message'];
				// } else {
					// $reponse['status'] = 'success';
					// $reponse['message'] = $CMS->lang['contact_success'];
				// }
			}
		}
		exit(json_encode($reponse));
	}

	/*
	* Recall
	*/
	protected function contactReCallAjax() 
	{
		global $CMS;

		$reponse = array(
			'status' => 'error', 
			'message' => $CMS->lang['unkhown_err'], 
		);

		// Data
		$cus_phone = $CMS->input['contact_phone'];
		$cus_content = $CMS->input['contact_content'];
		$cus_location = $CMS->input['contact_location'];

		if ( $CMS->input['request_method'] != 'post' )
		{
			$reponse['message'] = $CMS->lang['request_method_err'];
		}
		else if ( preg_match('/^[0-9]{1,15}$/', $cus_phone) == false )
		{
			$reponse['message'] = $CMS->lang['phone_err'];
		}
		else if ( $cus_content AND strlen($cus_content) > 50 )
		{
			$reponse['message'] = $CMS->lang['content_maxlength_err'];
		}
		else
		{
			// Data
			$data['cus_phone'] = $cus_phone;
			$data['cus_content'] = $cus_content;
			$data['cus_location'] = $cus_location;
			
			// Send api
			$api = \models\nhanhoa::api('recall', $data);
			if ( $api['status'] == 'success' ) 
			{
				$reponse['status'] = 'success';
				$reponse['message'] = $CMS->lang['contact_success'];
			} 
			else 
			{
				$reponse['message'] = $api['message'];
			}
		}

		echo json_encode($reponse);exit;
	}

	/*
	* Register Content Services
	*/
	protected function registerContentServices() 
	{
		global $CMS;

		// output
		$reponse = array(
			'status' => 'error', 
			'message' => $CMS->lang['unkhown_err'], 
		);

		if ( $CMS->input['request_method'] != 'post' )
		{
			$reponse['message'] = $CMS->lang['request_method_err'];
		}
		else
		{
			// Inputs Data
			$regis_name = $CMS->input['regis_name'];
			$regis_phone = $CMS->input['regis_phone'];
			$regis_email = $CMS->input['regis_email'];
			$regis_industry = $CMS->input['regis_industry'];
			$regis_website = $CMS->input['regis_website'];
			$regis_location = $CMS->input['regis_location'];
			$regis_agree = $CMS->input['regis_agree'];

			// convert industry
			$industry_list = array(
				"thoi_trang" => "Thời trang", 
				"my_pham" => "Mỹ phẩm", 
				"dien_thoai_va_dien_may" => "Điện thoại & Điện máy", 
				"vat_lieu_xay_dung" => "Vật liệu xây dựng", 
				"me_va_be" => "Mẹ & Bé", 
				"tap_hoa" => "Tạp hóa", 
				"nong_san_va_thuc_pham" => "Nông sản & Thực phẩm", 
				"xe_may_va_linh_kien" => "Xe Máy & Linh Kiện", 
				"bar_cafe_nha_hang" => "Bar - Cafe - Nhà hàng", 
				"sieu_thi_mini" => "Siêu thị mini", 
				"noi_that_va_gia_dung" => "Nội thất & Gia dụng", 
				"nganh_hang_khac" => "Ngành hàng khác", 
			);
			$regis_industry = $industry_list[$regis_industry];

			// Check Data
			$reponse['message'] = '';
			
			if( ! $regis_name OR strlen($regis_name) > 50 )
			{
				$reponse['message'] .= $CMS->lang['name_err'] . '<br>';
			}

			if( ! preg_match('/^[0-9]{1,15}$/', $regis_phone) )
			{
				$reponse['message'] .= $CMS->lang['phone_err'] . '<br>';
			}

			if( ! $CMS->class->input->is_email($regis_email) OR strlen($regis_email) > 253 )
			{
				$reponse['message'] .= $CMS->lang['email_err'] . '<br>';
			}

			if( ! $regis_industry )
			{
				$reponse['message'] .= $CMS->lang['industry_err'] . '<br>';
			}

			if( $regis_website AND (! $CMS->class->domain->filter_var_domain($regis_website) OR strlen($regis_website) > 253) )
			{
				$reponse['message'] .= $CMS->lang['website_err'] . '<br>';
			}

			if( ! $regis_agree )
			{
				$reponse['message'] .= $CMS->lang['agree_err'] . '<br>';
			}

			// Checked data and send request
			if ( ! $reponse['message'] )
			{
				// Set Data
				$data['cus_phone'] = $regis_phone;
				$data['cus_content'] = "
					Đăng ký dịch vụ xây dựng nội dung cho website<br>\n<br>\n
					Name: {$regis_name}<br>\n
					Email: {$regis_email}<br>\n
					Industry: {$regis_industry}<br>\n
					Website: {$regis_website}<br>\n
				";
				$data['cus_location'] = $regis_location;
				
				// Send api
				$api = \models\nhanhoa::api('recall', $data);
				if ( $api['status'] == 'success' ) 
				{
					$reponse['status'] = 'success';
					$reponse['message'] = $CMS->lang['register_content_services_success'];
				} 
				else 
				{
					$reponse['message'] = $api['message'];
				}
			}
		}

		echo json_encode($reponse);exit;
	}
}