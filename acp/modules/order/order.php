<?php

use \core\ezy;
use \lib\input;
use \models\custom_status;

\core\ezy::load_model("custom_status");
\core\ezy::load_model("rating");

use \models\variants;
\core\ezy::load_model("variants");
\core\ezy::load_model("order", "checkin");

$order = new Order;
$order->autorun();

class Order{
	public $html;

	public function autorun()
    {
		global $tpl,$CMS;

		$CMS->class->language->load("order");
		$this->html = $CMS->class->template->load_template("skin_order");
		$CMS->core->page_title = "{$CMS->lang['order_title']}";

        $tpl->payment_method_list = $this->loadPaymentMethod();

		switch ($CMS->input['act'])
		{
			case 'add':
				if(\lib\input::get('subact') == "find_product_ajax")
				{
					$this->find_product_ajax();
				}
				else
				{
					$this->add();
				}
				
			break;
			case 'add_do':
				$this->add_do();
				break;
			case 'refund':
				$this->refund();
				break;
			case 'refund_do':
				$this->refund_do();
				break;
			case 'show':
				$this->show();
				break;
			case 'search':
				$this->search();
				break;
			case 'order-status':
				$CMS->order->editStatus();
				break;
			case 'delete':
				$CMS->order->acpDel();
				break;
			case 'edit':
				$this->edit();
				break;
			case 'edit_do':
				if(\lib\input::get('subact') == "updatelog")
				{
					$this->updatelog();
				}else
				{
					$this->edit_do();
				}
				break;
			case 'preview':
				$this->preview();
			break;	
			case 'renew':
				$this->renew();
			break;	
			case 'renew_do':
				$this->renew_do();
			break;
            case 'rating':
                $this->rating();
                break;
            case 'rating_do':
                $this->rating_do();
                break;
			default:
				if(\lib\input::get('subact') == "autocomplete_quick_search")
				{
					$this->autocomplete_quick_search();
					
				}
				else if ( \lib\input::get('subact') == "list_booking_json_formatted" )
				{
					$this->list_booking_json_formatted();
				}else if ( \lib\input::get('subact') == "getdistrict" )
				{
					$this->getdistrict();
				}elseif(\lib\input::get('subact') == 'getshipbiladdress')
                {
                    $this->getshipbiladdress();
                }elseif(\lib\input::get('subact') == 'getdatashipbill')
                {
                    $this->getdatashipbill();
                }elseif(\lib\input::get('subact') == 'getstatus')
                {
                    $this->getstatus();
                }
                else if ( \lib\input::get('subact') == "preview_label" )
				{
					$this->previewLabel();
				}
                else if ( \lib\input::get('subact') == "preview_invoice" )
				{
					$this->previewInvoice();
				}
				else if( \lib\input::get('subact') == 'export_pdf')
                {
                    $this->exportPDF();
                }
                else if( \lib\input::get('subact') == 'send_email')
                {
                    $this->sendEmail();
                }
                else if( \lib\input::get('subact') == 'calculate_ship_fee')
                {
                    $this->calculateShipFee();
                }
                else if(input::get('subact') == 'set_rating_status')
                {
                    $this->setRatingStatus();
                }
                else if(input::get('subact') == 'get_info_rating')
                {
                    $this->get_info_rating();
                }
 				else
 				{
 					$this->default_page();
 				}
			 break;
		}
	}

    /**
     * Listing
     */

	public function default_page(){
		global $CMS, $member;
		$CMS->order->per_page = 20;

        foreach($CMS->input as $inputKey => $inputValue)
        {
        	$CMS->input[$inputKey] = urldecode($inputValue);
        	// Fix cho trường hợp search status custom nhiều giá trị (array)
        	// if(!is_array($inputValue))
        	// {
         //    	$CMS->input[$inputKey] = urldecode($inputValue);
        	// }else
        	// {
        	// 	$CMS->input[$inputKey] = urldecode(implode(",",$inputValue));
        	// }
        }

        //Check date from dashboard
        if($CMS->input['dashboard_date'])
        {
            $dashboard_date_explode = explode("/",$CMS->input['dashboard_date']);
            $dashboard_date_cnt =  count($dashboard_date_explode);

            if($dashboard_date_cnt == 3)
            {
                $CMS->input['ord_time_from'] = $CMS->input['ord_time_to'] = $CMS->input['dashboard_date'];
            }
            elseif ($dashboard_date_cnt == 2)
            {
                $dashboard_date[0] = '01';
                if(strlen($dashboard_date_explode[0])==4)
                {
                    $dashboard_date[2] = $dashboard_date_explode[0];
                    $dashboard_date[1] = $dashboard_date_explode[1];
                }
                else
                {
                    $dashboard_date[2] = $dashboard_date_explode[1];
                    $dashboard_date[1] = $dashboard_date_explode[0];
                }

                $date_position = explode(',',$CMS->vars['position_date_format'][$CMS->vars['date_format']]);

                $date_convert = [];

                foreach ($date_position as $date_index)
                {
                    $date_convert[] = $dashboard_date[$date_index];
                }

                $CMS->input['ord_time_from'] = implode('/',$date_convert);
                $CMS->input['ord_time_to'] = date(str_replace('d','t',$CMS->vars['dateformat_php'][$CMS->vars['date_format']]), $CMS->class->date->date2time($CMS->input['ord_time_from'])+(3600*24));
            }
        }

		if(isset($CMS->input['ord_time_from']) && $CMS->input['ord_time_from'] != '')
        {
            $CMS->input['time_from'] = $CMS->class->date->date2time($CMS->input['ord_time_from']);
        }

        if(isset($CMS->input['ord_time_to']) && $CMS->input['ord_time_to'] != '')
        {
            $CMS->input['time_to'] = $CMS->class->date->date2time($CMS->input['ord_time_to'])+(3600*24)-1;
        }


        if(! \lib\security::checkPermission(input::get('site'), 'all_branches')) {
            $CMS->input['store_id'] = $member['store_id'];
        }

		$CMS->order->listing();
	}

    /**
     * Add an order
     */

	public function add()
	{
		global $CMS, $tpl;
		$order = $CMS->input;
		$tpl->header_title = $CMS->lang['order_title_add'];
		if( $CMS->input['sub_act'] == "copy" )
		{
			$order = $CMS->order->get_info($CMS->input['id']);
			if(is_array($order))
			{
				//$order = $CMS->order->convertvalue($order);
				$customer = $CMS->customer->getInfo($order['cus_id']);
				$order['customer'] = $customer;
				$order['cus_name'] = $customer['cus_full_name'];
				$order['order_payment_method'] = $order['payment_method'];

				$shipping_info = json_decode($order['shipping_info'],true);

				$order['ship_receive_name'] = $shipping_info['ship_receive_name'];
		 		$order['ship_phone'] = $shipping_info['ship_phone'];
		 		$order['ship_address'] = $shipping_info['ship_address'];
		 		$order['ship_location'] = $shipping_info['ship_location'];
		 		$order['ship_code'] = $shipping_info['ship_code'];
		 		$order['ship_weight'] = $shipping_info['ship_weight'];
		 		$order['ship_long'] = $shipping_info['ship_long'];
		 		$order['ship_wide'] = $shipping_info['ship_wide'];
		 		$order['ship_height'] = $shipping_info['ship_height'];
		 		$order['ship_service_type'] = $shipping_info['ship_service_type'];
		 		$order['ship_deliver'] = $shipping_info['ship_deliver'];
		 		$order['ship_deliver_fee'] = $shipping_info['ship_deliver_fee'];
			}

			// get Address ship bill
        	list($tpl->ship, $tpl->bill, $tpl->sp_id) = $CMS->order->getShipBillByOrder($order['ord_id']);
		}

		// Data ship bill option
        $tpl->option_payment_country  = $CMS->country->get_country_option(isset($tpl->bill['country']) ? $tpl->bill['country'] : 0, 1);
        $tpl->option_payment_state 	  = $CMS->country->get_state_option(isset($tpl->bill['province']) ? $tpl->bill['province'] : 0, 1);
        $tpl->style_bill_state    = 'display: none;';
        $tpl->style_bill_province = 'display: block;';
        if( isset($tpl->bill['country']) AND strtoupper($tpl->bill['country']) == 'US' )
        {
        	$tpl->style_bill_state    = 'display: block;';
        	$tpl->style_bill_province = 'display: none;';
        }

        $tpl->option_shipping_country = $CMS->country->get_country_option(isset($tpl->ship['country']) ? $tpl->ship['country'] : 0, 1);
        $tpl->option_shipping_state   = $CMS->country->get_state_option(isset($tpl->ship['province']) ? $tpl->ship['province'] : 0, 1);
        $tpl->style_ship_state    = 'display: none;';
        $tpl->style_ship_province = 'display: block;';
        if( isset($tpl->ship['country']) AND strtoupper($tpl->ship['country']) == 'US' )
        {
        	$tpl->style_ship_state    = 'display: block;';
        	$tpl->style_ship_province = 'display: none;';
        }
        $tpl->shipping_method = isset($order['order_shipping_method']) ? $order['order_shipping_method'] : 0;

		//$CMS->output .= $this->html->add($order);

        // Check quick add item from assets module
        list($tpl->row_store, $tpl->option_store) = $CMS->store->get_list_store($order['store_id'],0,"");

		// Get product(s)
        if(isset($_SESSION['list_product_order']) AND count($_SESSION['list_product_order']) > 0)
        {
            $tpl->request_product = $_SESSION['list_product_order'];
            //	$product_id_param = $tpl->request_product[0]['product_id'];
            unset($_SESSION['list_product_order']);
        }

        // Get asset(s)
        if(isset($_SESSION['list_assets_order']) AND count($_SESSION['list_assets_order']) > 0)
        {
            $tpl->request_asset = $_SESSION['list_assets_order'];
            $store_id_param = $tpl->request_asset[0]['store_id'];
            unset($_SESSION['list_assets_order']);
        }

        // Copy action
        if($CMS->input['sub_act'] == "copy" AND $order['ord_id'] > 0)
        {
            // Overwrite session
            $tpl->request_product = $CMS->order->get_item($order['ord_id'],0);
            $tpl->request_asset = $CMS->order->get_item($order['ord_id'],1);
        }

        // Filter payment method
        $tpl->payment_method = isset($order['order_payment_method']) ? $order['order_payment_method'] : 0;
        $tpl->trx_account = isset($order['account_id']) ? $order['account_id'] : '';

        // Get account
        $tpl->account = $CMS->accounts->getAll(' accounts_status=1 AND ');

        // Get booking hours
       	$tpl->b_hours_morning = json_decode($CMS->vars['booking_hours_morning'],true);
        $tpl->b_hours_afternoon = json_decode($CMS->vars['booking_hours_afternoon'],true);
       
        // Form action
        $tpl->form_action = "{$CMS->vars['root_domain']}/?site=order&act=add_do";
          // Url back
        $url_back['list'] =	"{$CMS->vars['root_domain']}/?site=order";
        //ACtion button footer
        $tpl->btn_action['footer_back'] = $CMS->global->footer_back($url_back);
        $tpl->action_footer = \core\ezy::render("action_add_footer","order");


        // Url back
        $tpl->url_back['list'] = "<?=$CMS->vars['root_domain'];?>/?site=order";

		// Load order
        $tpl->data = $order;
 		
 		//Load logs and comment
        $tpl->comment = $CMS->global->comment();
        $tpl->logs = $CMS->global->logs("order_{$CMS->input['id']}");
        
        // Customer
        $tpl->customer = $order['customer'];
 		
 		// get Store id
        $tpl->store = $CMS->store->get_option_store();
        $tpl->optionCity = $CMS->country->getOptionCity($CMS->vars['default_country_id']);
        // p($tpl->listStatus);exit;

        // Output data
        // $CMS->output .= ezy::html("form");
        $CMS->output .= ezy::html("form_merchant");
	}

    /**
     * Submit add
     */

	public function add_do()
	{
		global $CMS, $member;

 	   	if($CMS->input['action_redirect'] == "confirm_paid")
 	   	{
 	   	 	$CMS->input['confirm_paid'] = 1;
 	   	}
	   	
	   	$this->generalVariantsContent(); // general variants content

        if (! \lib\security::checkPermission(input::get('site'), 'all_branches')) {
            $CMS->input['store_id'] = $member['store_id'];
        }

	   	$order =  $CMS->order->add();
	   	if( is_array($order) )
	   	{
	   		//$_SESSION['msg'] = "{$CMS->lang['create_order_success']}";
	   		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order");
	   	}
	   	else
	   	{
	   		$customer = $CMS->customer->getInfo($CMS->input['cus_id']);
			$CMS->input['customer'] = $customer;
	   		$this->add($CMS->input);	 
	   		//$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&act=add");
	   	}
	}

    /**
     * Show detail
     */

	public function show() 
	{
		global $CMS, $tpl, $member;

        $sql_add = "";

		if(! \lib\security::checkPermission(input::get('site'), 'all_branches')) {
		    $sql_add = "store_id = {$member['store_id']} AND";
        }

		$data = $CMS->order->get_info(0, "", $sql_add);
		$tpl->header_title = "{$CMS->lang['order_info']} #{$data['ord_name']}";
		if(!is_array($data))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['order_isnot_exits']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order");
		}

		$shipping_info = json_decode($data['shipping_info'],true);

		$data['ship_receive_name'] = $shipping_info['ship_receive_name'];
 		$data['ship_phone'] = $shipping_info['ship_phone'];
 		$data['ship_address'] = $shipping_info['ship_address'];
 		$data['ship_location'] = $shipping_info['ship_location'];
 		$data['ship_code'] = $shipping_info['ship_code'];
 		$data['ship_weight'] = $shipping_info['ship_weight'];
 		$data['ship_long'] = $shipping_info['ship_long'];
 		$data['ship_wide'] = $shipping_info['ship_wide'];
 		$data['ship_height'] = $shipping_info['ship_height'];
 		$data['ship_service_type'] = $shipping_info['ship_service_type'];
 		$data['ship_deliver'] = $shipping_info['ship_deliver'];
 		$data['ship_deliver_fee'] = $shipping_info['ship_deliver_fee'];
		$data['ship_deliver_name'] = $CMS->partner_delivery->get_info($shipping_info['ship_deliver'], "p_delivery_name");

		// $order_item = $CMS->order->get_list_order_item($data['ord_id']);
		// $list_transaction = $CMS->order->get_list_transaction($data['ord_id']);
		// $CMS->output.=$this->html->show($CMS->order->convertvalue($data),$order_item,$list_transaction);
		// $CMS->output.=$CMS->global->comment();
		// $CMS->output.=$CMS->global->logs("order_{$CMS->input['id']}");
		// $CMS->output.=$CMS->global->subLogs("order_{$CMS->input['id']}");

		// Quy trình mới chạy theo view order
		$tpl->data = $CMS->order->convertvalue($data);
		$tpl->order_item = $CMS->order->get_list_order_item($data['ord_id']);
		$tpl->list_transaction = $CMS->order->get_list_transaction($data['ord_id']);
		
		// get Address ship bill
        list($tpl->ship, $tpl->bill, $tpl->sp_id) = $CMS->order->getShipBillByOrder($data['ord_id']);
        
        // get list comment
        $tpl->listComment = $CMS->order->getListComment($data['ord_id'], $data['cus_id']);
        $tpl->numberComment = count($tpl->listComment);

        // footer
        $url_back['list'] = "{$CMS->vars['root_domain']}/?site=order";
        $tpl->footer_html = $CMS->global->footer_back($url_back);
        
        // Check enable salon hang
		if($CMS->vars['addon_goods_enable'] == 1)
		{
			$addon_goods_enable = "block";
			$tpl->stock = $CMS->order->check_stock($data['ord_id']);
		}
		else
		{
			$addon_goods_enable = "none";
			$tpl->stock = 0;
		}

		$tpl->ratings = models\rating::getAll();
		$tpl->logs = $CMS->global->logs("order_{$CMS->input['id']}");

		$CMS->output .= ezy::html("show_order");

	}

    /**
     * Search
     */

	public function search() {
		global $CMS, $DB, $member;
		$o_quick_search  = trim($CMS->input['o_quick_search']);
		$str='';

		if (!empty($o_quick_search) ) {
			if(Validate::isNum($o_quick_search))
			{
				$str.='&id='.$o_quick_search;	
			}
			else
			{
					$str.='&name='.$o_quick_search;
			}
		}



		if (!empty($CMS->input['ord_name'])) {
			$str.='&name='.$CMS->input['ord_name'];
		}
		
		if (!empty($CMS->input['ord_seach_user'])) {
			$str.='&user='.$CMS->input['ord_seach_user'];
		}
		if (isset($CMS->input['ord_status'])) {
			$str.='&status='.$CMS->input['ord_status'];
		}
		if (isset($CMS->input['trx_id'])) {
			$str.='&trx_id='.$CMS->input['trx_id'];
		}
		if (isset($CMS->input['payment_method'])) {
			$str.='&payment_method='.$CMS->input['payment_method'];
		}
		if (isset($CMS->input['store_id'])) {
			$str.='&store_id='.$CMS->input['store_id'];
		}

		if (isset($CMS->input['cus_id'])) {
			$str.='&cus_id='.$CMS->input['cus_id'];
		}
		if (isset($CMS->input['user_id'])) {
			$str.='&user_id='.$CMS->input['user_id'];
		}



		if (isset($CMS->input['ord_total_from'])&&$CMS->input['ord_total_from']>=0) {
			$str.='&total_from='.$CMS->input['ord_total_from'];
		}
		if (isset($CMS->input['ord_total_to'])&&$CMS->input['ord_total_to']>=0) {
			$str.='&total_to='.$CMS->input['ord_total_to'];
		}
		if (isset($CMS->input['ord_time_from'])&&preg_match("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/",$CMS->input['ord_time_from'])) {
			$tmp=explode('/',$CMS->input['ord_time_from']);
			$str.='&time_from='.(string)(mktime(0,0,0,$tmp[1],$tmp[0],$tmp[2]));
		}
		if (isset($CMS->input['ord_time_to'])&&preg_match("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/",$CMS->input['ord_time_to'])) {
			$tmp=explode('/',$CMS->input['ord_time_to']);
			$str.='&time_to='.(string)(mktime(24,0,0,$tmp[1],$tmp[0],$tmp[2]));
		}

		if(isset($CMS->input['sort']))
		{
			$str.='&sort='.$CMS->input['sort'];
		}

		if(isset($CMS->input['order_by']))
		{
			$str.='&order_by='.$CMS->input['order_by'];
		}

		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order{$str}");
	}



	 /**
     * Edit an order
     */

	public function edit()
	{
		global $CMS, $tpl, $member;

		$sql_add = "";
		if (! lib\security::checkPermission(input::get('site'), 'all_branches')) {
		    $sql_add .= "store_id={$member['store_id']} AND";
        }
		 
		$order = $CMS->order->get_info($CMS->input['id'], '', $sql_add);

		if(!$order) {
		    $_SESSION['error_msg'] = $CMS->lang['no_data'];
		    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
		    exit;
        }

		$tpl->header_title = $CMS->lang['order_title_edit'];
	 	if(\lib\input::get('subact') == "update_ordi")
		{
			if(is_array($order))
			{
	 			$CMS->order->update_status_ordi($CMS->input['type']);
	 			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&act=show&id={$CMS->input['id']}");
			}
			else
			{
					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order");
			}
		}
		elseif(\lib\input::get('subact') == "update_ordi_id")
		{
			if(is_array($order))
			{
	 			$CMS->order->update_status_ordi_id();
	 			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&act=show&id={$CMS->input['id']}");			 
			}
			else
			{
					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order");
			}
		}
		else {
            if (is_array($order)) {
                // Check trang thai don hang , neu order da thanh toan roi, hoac cong nu, k cho edit
                if ($order['payment_status'] > 0) {
                    $_SESSION['msg'] = "{$CMS->lang['order_not_modify']}";
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&act=show&id={$CMS->input['id']}");
                }

                $shipping_info = json_decode($order['shipping_info'], true);

                $order['ship_receive_name'] = $shipping_info['ship_receive_name'];
                $order['ship_phone'] = $shipping_info['ship_phone'];
                $order['ship_address'] = $shipping_info['ship_address'];
                $order['ship_location'] = $shipping_info['ship_location'];
                $order['ship_code'] = $shipping_info['ship_code'];
                $order['ship_weight'] = $shipping_info['ship_weight'];
                $order['ship_long'] = $shipping_info['ship_long'];
                $order['ship_wide'] = $shipping_info['ship_wide'];
                $order['ship_height'] = $shipping_info['ship_height'];
                $order['ship_service_type'] = $shipping_info['ship_service_type'];
                $order['ship_deliver'] = $shipping_info['ship_deliver'];
                $order['ship_deliver_fee'] = $shipping_info['ship_deliver_fee'];

            } 
        }

        //Convert order
        $order = $CMS->order->convertvalue($order);
        $order['cus_name'] = $order['cus_name_show'];
     
        // Check quick add item from assets module
        list($tpl->row_store, $tpl->option_store) = $CMS->store->get_list_store($order['store_id'],0,"");

		// Get product(s)
	    $tpl->request_product = $CMS->order->get_item($order['ord_id'],0);
	    // Get asset(s)
	    $tpl->request_asset = $CMS->order->get_item($order['ord_id'],1);
	    
        // Filter payment method
        $tpl->payment_method = !empty($order['order_payment_method']) ? $order['order_payment_method'] : $order['payment_method'];
        $tpl->trx_account = !empty($order['account_id']) ? $order['account_id'] : '';

        // Get account
        $tpl->account = $CMS->accounts->getAll(' accounts_status=1 AND ');

        // Get booking hours
        $tpl->b_hours_morning = json_decode($CMS->vars['booking_hours_morning'],true);
        $tpl->b_hours_afternoon = json_decode($CMS->vars['booking_hours_afternoon'],true);

        // Form action
        $tpl->form_action = "{$CMS->vars['root_domain']}/?site=order&act=edit_do&id={$order['ord_id']}";

        // Url back
        $url_back['list'] =	"{$CMS->vars['root_domain']}/?site=order";
        $url_back['detail_id'] = "{$order['ord_id']}";
        //ACtion button footer
        $tpl->btn_action['footer_back'] = $CMS->global->footer_back($url_back);

        $tpl->action_footer = \core\ezy::render("action_edit_footer","order");
		// Load order
        $tpl->data = $order;
 		//Load logs and comment
        $tpl->comment = $CMS->global->comment();
        $tpl->logs = $CMS->global->logs("order_{$CMS->input['id']}");
        // Customer
        $tpl->customer = $order['customer'];
        
        // Option city
        $tpl->optionCity = $CMS->country->getOptionCity($CMS->vars['default_country_id']);
        
        // get Address ship bill
        list($tpl->ship, $tpl->bill, $tpl->sp_id) = $CMS->order->getShipBillByOrder($order['ord_id']);
        
        $tpl->option_payment_country  = $CMS->country->get_country_option($tpl->bill['country'], 1);
        $tpl->option_payment_state 	  = $CMS->country->get_state_option($tpl->bill['province'], 1);
        $tpl->style_bill_state    = 'display: none;';
        $tpl->style_bill_province = 'display: block;';
        if( isset($tpl->bill['country']) AND strtoupper($tpl->bill['country']) == 'US' )
        {
        	$tpl->style_bill_state    = 'display: block;';
        	$tpl->style_bill_province = 'display: none;';
        }

        $tpl->option_shipping_country = $CMS->country->get_country_option($tpl->ship['country'], 1);
        $tpl->option_shipping_state   = $CMS->country->get_state_option($tpl->ship['province'], 1);
        $tpl->style_ship_state    = 'display: none;';
        $tpl->style_ship_province = 'display: block;';
        if( isset($tpl->ship['country']) AND strtoupper($tpl->ship['country']) == 'US' )
        {
        	$tpl->style_ship_state    = 'display: block;';
        	$tpl->style_ship_province = 'display: none;';
        }
        $tpl->shipping_method = isset($order['order_shipping_method']) ? $order['order_shipping_method'] : $order['ord_shipping_method'];;

        // Output data
        // $CMS->output .= ezy::html("form");
        $CMS->output .= ezy::html("form_merchant");
	}

    /**
     * Submit edit
     */

	public function edit_do() {
		global $CMS, $DB, $member;
 
 		if($CMS->input['sub_act'] == "approve_paid")
 		{
 			$CMS->order->approve_order("paid");
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&act=show&id={$CMS->input['id']}");
			 
 		}
 		elseif($CMS->input['sub_act'] == "approve_unpaid")
 		{
 			$CMS->order->approve_order();
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&act=show&id={$CMS->input['id']}");
			
 		}elseif($CMS->input['sub_act'] == "process")
 		{
 			$CMS->order->process_order($CMS->input['id'], 1);

			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&act=show&id={$CMS->input['id']}");
			
 		}elseif($CMS->input['sub_act'] == "cancel")
 		{
 			$CMS->order->cancel_order($CMS->input['id'], 3);

			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&act=show&id={$CMS->input['id']}");
			
 		}elseif($CMS->input['sub_act'] == "success")
 		{
 			$CMS->order->process_order($CMS->input['id'],2);

			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&act=show&id={$CMS->input['id']}");
			
 		}
 		elseif($CMS->input['sub_act'] == "pending")
 		{
 			$CMS->order->pending_order($CMS->input['id'],0);

			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&act=show&id={$CMS->input['id']}");
			
 		}
 		else
 		{
 			$id = $CMS->input['id'];
 			$redirect = $CMS->input['redirect'];

 			$this->generalVariantsContent(); // general variants content

            if (! \lib\security::checkPermission(input::get('site'), 'all_branches')) {
                $CMS->input['store_id'] = $member['store_id'];
            }

 			if ($CMS->order->edit() == true) {
 				if($redirect == 0)
 				{ 
 					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order");
 				}
				else
				{ 
					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&act=show&id={$id}");
				}
			}
 		}
		
		$ord= $CMS->order->get_info($CMS->input['id']);
		$shipping_info = json_decode($ord['shipping_info'],true);

		$order['ship_receive_name'] = $shipping_info['ship_receive_name'];
 		$order['ship_phone'] = $shipping_info['ship_phone'];
 		$order['ship_address'] = $shipping_info['ship_address'];
 		$order['ship_location'] = $shipping_info['ship_location'];
 		$order['ship_code'] = $shipping_info['ship_code'];
 		$order['ship_weight'] = $shipping_info['ship_weight'];
 		$order['ship_long'] = $shipping_info['ship_long'];
 		$order['ship_wide'] = $shipping_info['ship_wide'];
 		$order['ship_height'] = $shipping_info['ship_height'];
 		$order['ship_service_type'] = $shipping_info['ship_service_type'];
 		$order['ship_deliver'] = $shipping_info['ship_deliver'];
 		$order['ship_deliver_fee'] = $shipping_info['ship_deliver_fee'];
 		

		$this->edit($CMS->order->convertvalue($ord));
	}

    /**
     * Find product(s) ajax
     */
	 
	public function find_product_ajax() 
	{
		global $CMS, $DB, $member;
 	
 		$key_search = trim($CMS->input['key_search']);
 		$service_type = trim($CMS->input['service_type']);

 		if($service_type == 0) // Mua ban tai san
 		{
 			$sql = $DB->query("SELECT * FROM ".root_table."assets WHERE ass_status = 1 AND ass_deleted = 0");
 			if($DB->num_rows($sql) > 0)
 			{
 				while($data = $DB->fetch_array($sql))
 				{
 					$store = $CMS->store->get_info($data['store_id']);
 					$data['store_name'] = $store['store_name'];
 					$option[] = $data;

 				}
 				print json_encode(array("status" => "success", "msg" => "", "data_option" => $option));exit;
 			}
 			else
 			{
 				print json_encode(array("status" => "error", "msg" => ""));exit;
 			}
 		}
 		else
 		{
            $option = [];
            $DB->query("SELECT * FROM ".root_table."product WHERE product_type = 1 AND (product_status = 0 OR product_status = 1) AND  product_deleted = 0");

            if($DB->num_rows() > 0)
 			{
 				while($data = $DB->fetch_array())
 				{
 					$option[] = $data;
 				}
 				print json_encode(array("status" => "success", "msg" => "", "data_option" => $option));exit;
 			}
 			else
 			{
 				print json_encode(array("status" => "error", "msg" => ""));exit;
 			}
		}
 	}

 	//============================================
 	// Search autocomplete order
 	//===========================================

	public function autocomplete_quick_search() {
		global $CMS, $DB, $member;
 		$keyword  = trim($CMS->input['order_keyword']);

		$keyword  = urldecode($keyword);

        $where = "";

		if(Validate::isNum($keyword))
		{
			$where.=" AND O.ord_id = '{$keyword}'";	
		}
		else
		{
			$where.=" AND ( O.ord_name LIKE '%{$keyword}%' OR OI.ordi_name LIKE '%{$keyword}%' ) ";
		}


		if (! \lib\security::checkPermission(input::get('site'), 'all_branches')) {
            $where.= "AND O.store_id={$member['store_id']}";
        }

        $li = "";
        	 
		$sql = $DB->query("SELECT O.* FROM ".root_table."order O LEFT JOIN ".root_table."order_item OI ON  O.ord_id = OI.ord_id WHERE O.ord_deleted = 0 {$where}  GROUP BY O.ord_id ORDER BY O.ord_id DESC" );
		 $count = $DB->num_rows($sql);
		 if($count > 0)
		 {
		 	if($count <= 3)
		 	{
		 		while ($data = $DB->fetch_array($sql)) {
		 			# code...
		 			$li .= "<li><a href='{$CMS->vars['root_domain']}/?site=order&act=show&id={$data['ord_id']}'>{$data['ord_name']}</a></li>";
		 		}
		 	}
		 	elseif($count > 3)
		 	{
		 		$i = 1;
		 		while ($data = $DB->fetch_array($sql)) {
		 			# code...
		 			if($i <= 3)
		 			{

		 				$li .= "<li><a href='{$CMS->vars['root_domain']}/?site=order&act=show&id={$data['ord_id']}'>{$data['ord_name']}</a></li>";
		 			}
		 			$i++;
		 		}
		 		$li .= "<li class='see_more'><span onclick='return autosubmit_frm_qs_product();' >{$CMS->lang['see_more']} ({$count}) {$CMS->lang['result']}</spa></li>";
		 	}
		 	print json_encode(array("status" => "success", "msg" => "Not found", "data_option" => $li));exit;

		 }
		 else
		 {
		 	print json_encode(array("status" => "error", "msg" => "Not found"));exit;
		 }

		 
	}

    /**
     * Preview invoice
     */

	public function preview()
	{
		global $CMS;
	//	$id = $CMS->input['id'];
		$id= 25;
		$tran = $CMS->transactions->convertvalue($CMS->transactions->getInfo($id));
		$order = $this->html->preview($tran);
	 // print_r ($order);exit;
  
		ob_start();
		$CMS->class->html2pdf->build();
 // set default header data

        $CMS->class->html2pdf->pdf->SetDisplayMode('fullpage');

        $CMS->class->html2pdf->pdf->WriteHTML($order);

        $CMS->class->html2pdf->pdf->Output();

        exit;
	}

	// list booking for scheduler dashboard
 	public function list_booking_json_formatted()
 	{
 		global $CMS;

 		$startDate = $CMS->class->date->date2time($CMS->class->date->format(strtotime($CMS->input['start'])));
 		$endDate = $CMS->class->date->date2time($CMS->class->date->format(strtotime($CMS->input['end']) + 3600*4));

 		$sql_add = " booking_date>=$startDate AND booking_date<$endDate AND ";

        $booking_list = $CMS->order->get_list_booking($sql_add);
        $schedule = [];

        foreach ($booking_list as $key_booking => $booking)
        {
            $booking['booking_date'] = date("Y-m-d", $booking['booking_date']);
            $booking['booking_hours'] = substr ("0".$booking['booking_hours'].":00", -8);
            $booking['ord_item'] = @json_decode($booking['ord_item'], 1);
            $booking['ord_content'] = @json_decode($booking['ord_content'], 1);

            $schedule[] = [
                'title' => htmlspecialchars_decode("{$booking['ord_item'][0]['product_name']}"),
                'startHour' => $booking['booking_hours'],
                'start' => $booking['booking_date']."T".$booking['booking_hours'],
                'ordCode' => $booking['ord_name'],
                'link' => "{$CMS->vars['root_domain']}/?site=order&act=show&id={$booking['ord_id']}",
            ];
        }

 		echo json_encode($schedule, JSON_UNESCAPED_UNICODE); exit;

 		/*$events = array();
 		$infos = '<session style="display:none;" id="infos">';

 		$classNameList = array('event-green','event-red','event-orange','event-coral');
 		$classNameCount = count($$classNameList) - 1;

 		$booking_list = $CMS->order->get_list_booking();
 		// print_r($booking_list);exit;

 		$lastIndex = 0;
 		foreach ( $booking_list as $key => $booking ) 
 		{
 			$index = mt_rand( 0, $classNameCount );
 			if ( $index == $lastIndex )
 			{
 				$index = ( $index == $classNameCount ) ? mt_rand( 0, $classNameCount - 1 ) : $index + 1;
 			}
 			$lastIndex = $index;

 			// title
 			$title = ''; //$booking['ord_name'] . ' - ';

 			$booking['ord_content'] = json_decode($booking['ord_content'], true);
 			$booking_service = $booking['ord_content']['booking_service'];
 			$booking_staff = $booking['ord_content']['staff_id'];

 			foreach ( $booking_service as $service )
 			{
 				$service = $CMS->product->getInfo($service, 'product_name');
 				$title .= $service ? str_replace('&amp;', '&', $service) . ',' : '';
 			}
 			$title = rtrim($title, ',');

 			$events[] = array(
 				'id' => $key,
		        'title'	=> $title,
		        'start'	=> $CMS->class->date->date_format($booking['booking_date_unix'], 'Y-m-d') . 'T' . date("H:i:s",strtotime($booking['booking_hours'])),
		        'end'	=> $CMS->class->date->date_format($booking['booking_date_unix'], 'Y-m-d') . 'T' . date("H:i:s",strtotime($booking['booking_hours'])),
		        'className' => $classNameList[$index],

		        'ord' => $booking['ord_name'],
		        'name' => $booking['ord_content']['cus_name'],
		        'phone' => $booking['ord_content']['cus_phone'],
		        'email' => $booking['ord_content']['cus_email'],
		    );

// 		    $infos .= <<<EOF
// 		    <div data-id="{$key}" data-title="{$title}" data-name="{$booking['ord_content']['cus_name']}" data-email="{$booking['ord_content']['cus_email']}" data-phone="{$booking['ord_content']['cus_phone']}" data-date="{$booking['ord_content']['booking_date']}" data-time="{$booking['ord_content']['booking_time']}"></div>
// EOF;
 		}
 		$infos .= '</session>';
 		print json_encode(array('events' => $events, 'infos' => $infos));
 		exit;*/
 	}

 
 	 /**
     * Renew an order
     */

	public function renew()
	{
		global $CMS, $tpl;
		if(\lib\input::get('subact') == "config_renew")
		{
			// Show form config renew
			$ord_id = $CMS->input['id'];
			$ordi = $CMS->input['ordi']; 
			$order_item =  $CMS->order->getinfo_ordi($ordi);  
	 
			if(  in_array($order_item['ordi_status'], array(1,4)) == true AND in_array($order_item['cycle_type'], array(1,2)) == true ) 
			{
				$_SESSION['msg'] = "{$CMS->lang['order_item_cannot_renew']}";
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&act=renew_do&id={$ord_id}");
			}
			if(is_array($order_item))
			{  
				$order_item['ordi_expiry_date_bk'] = $order_item['ordi_expiry_date'] ? $CMS->class->date->date_format($order_item['ordi_expiry_date'],1) : time();
 
			    $tpl->data['ordi'] = $order_item;

			    $order = $CMS->order->get_info($ord_id);
			    $tpl->data['ord'] = $order;
			    // Get product
			    $product = $CMS->product->getInfo($order_item['product_id']);
			 
			    if($product['product_price_sell'] != 0)
			    {
			    	$product['product_price_o'] = $product['product_price_sell'];
			    }else if($product['product_price_original'] != 0)
			    {
			    	$product['product_price_o'] = $product['product_price_original'];
			    }else
			    {
			    	$product['product_price_o'] = $product['product_price'];
			    }
			    $product['product_price_o'] = intval($product['product_price_o']);


			    $tpl->data['product'] = $product;
			    // Action submit
			    $tpl->form_action = "{$CMS->vars['root_domain']}/?site=order&act=renew_do&id={$ord_id}&ordi={$ordi}";

			    // Output data
       			$CMS->output .= ezy::html("form_renew");
			}
		}
	}


 	 /**
     * Renew an order
     */

	public function renew_do()
	{
		global $CMS, $tpl;
		$ord_id = $CMS->input['id'];

		if($CMS->order->renew() == false)
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&act=renew&subact=config_renew&id={$ord_id}&ordi={$CMS->input['ordi']}");
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&act=show&id={$ord_id}");
		 
	}

	public function rating()
    {
        global $CMS, $tpl;

        \core\ezy::load_model("rating");
        $tpl->order = $CMS->order->get_info();
        $tpl->rating = \models\rating::listing("",1,"rating_order","asc");

        // Output data
        $CMS->output .= ezy::html("rating");
    }

	public function rating_do()
    {
	    global $CMS;

        $rs = $CMS->order->rating($CMS->input['id'], $CMS->input['commission_rating']);

        if($rs) {
            $res = [
                'status' => 'success',
                'msg' => $_SESSION['msg']
            ];
        } else {
            $res = [
                'status' => 'error',
                'msg' => $_SESSION['msg']
            ];
        }

        unset($_SESSION['msg']);

        \lib\input::jsonEncode($res);
    }

    function getdistrict()
    {
    	global $CMS;

    	$city_id = intval($CMS->input['city_id']);
    	$optionDistrict = $CMS->country->getOptionDistrict($city_id);
    	print $optionDistrict;exit;
    }

    function getshipbiladdress()
    {
    	global $CMS;

    	$cus_id = intval($CMS->input['cus_id']);
    	$type = $CMS->input['type'];
    	$data = $CMS->customer->getshipbiladdress($cus_id, $type);
    	$data = json_encode($data, JSON_UNESCAPED_UNICODE);
    	print $data;exit;
    }

    function getdatashipbill()
    {
    	global $CMS;

    	$addr_id = intval($CMS->input['addr_id']);
    	$cus_id = intval($CMS->input['cus_id']);
    	$data = $CMS->customer->getshipbiladdress($cus_id, $addr_id);
    	$data = json_encode($data, JSON_UNESCAPED_UNICODE);
    	print $data;exit;
    }

    function updatelog()
    {
    	global $CMS;
    	$CMS->order->updatelog();
    	$tab = urldecode($CMS->input['tab']);
    	header("location: /acp/?site=order&act=show&id={$CMS->input['id']}&tab={$tab}&position=worklogs");
    	exit;
    }

    function getstatus()
    {
    	global $CMS;

    	$ord_status = intval($CMS->input['ordstatus']);
        $data = \models\custom_status::getStatusByOrdId($ord_status);

        print $data ? json_encode($data, JSON_UNESCAPED_UNICODE) : "";exit;
    }
    function loadPaymentMethod(){
        global $CMS;

        $payment_method=array();
        // Thanh toán tiền mặt
        $payment_method[0]=$CMS->lang['payment_method_0'];

        // Thanh toán chuyển khoản ngân hàng nội địa
        if($CMS->vars['ck_active']==1) {
            $payment_method[1] = $CMS->lang['payment_method_1'];
        }

        // Thanh toán khi nhận hàng COD
        if($CMS->vars['payment_active']==1) {
            $payment_method[2] = $CMS->lang['payment_method_2'];
        }

        // Thanh toán qua bảo kim
        if($CMS->vars['bk_active']==1) {
            $payment_method[3] = $CMS->lang['payment_method_3'];
        }
        //Thanh toán qua ngân lượng
        if($CMS->vars['nl_active']==1) {
            $payment_method[4] = $CMS->lang['payment_method_4'];
        }
        // Thanh toán qua Paypal
        if($CMS->vars['payment_active']==1){
            $payment_method[5]=$CMS->lang['payment_method_5'];
        }
        //Thanh toán qua Authorize
        if($CMS->vars['authorize_active']==1){
            $payment_method[5]=$CMS->lang['payment_method_6'];
        }
        //Thanh toán qua stripe
        if($CMS->vars['stripe_active']==1){
            $payment_method[5]=$CMS->lang['payment_method_7'];
        }

    return $payment_method;


    }

    function generalVariantsContent()
    {
    	global $CMS;

    	if( isset($CMS->input['var_id']) AND is_array($CMS->input['var_id']) )
    	{
    		$CMS->input['var_content'] = isset($CMS->input['var_content']) ? $CMS->input['var_content'] : [];
    		foreach( $CMS->input['var_id'] as $key => $var_id ) 
    		{
    			$CMS->input['var_content'][$key] = \models\variants::getInfo($var_id);
    		}
    	}
    }

    /**
     * General content
     */
    function previewInvoice()
    {
    	global $CMS, $tpl;

    	// Data
    	$order = $CMS->order->get_info($CMS->input['id']);
    	$tpl->data = $CMS->order->convertvalue($order);

    	$order_item = $CMS->order->getItemByOrder($order['ord_id']);
    	$order_item = $CMS->order->convertItems($order_item);
    	$tpl->items = $order_item;
    	
    	// Ship, Bill
        list($tpl->ship, $tpl->bill, $tpl->sp_id) = $CMS->order->getShipBillByOrder($order['ord_id']);

    	// output
    	$content = \core\ezy::render('preview_invoice_form', 'order');
    	$return = array( 
    		'content' 		=> $content, 
    		'urlPreview' 	=> "{$CMS->vars['root_domain']}/?site=order&subact=export_pdf&id={$order['ord_id']}&suffix=invoice&email_tpl=send_shipping_file", 

        	'urlSendEmail'	=>	"{$CMS->vars['root_domain']}/?site=order&subact=send_email&id={$order['ord_id']}&suffix=invoice", 
    	);

    	if( $CMS->input['view_content'] == 1 )
    	{
    		print $content; exit;
    	}

    	echo @json_encode($return, JSON_UNESCAPED_UNICODE); exit;
    	exit;
    }

    function previewLabel()
    {
    	global $CMS, $tpl;

    	// Data
    	$order = $CMS->order->get_info($CMS->input['id']);
    	$tpl->data = $CMS->order->convertvalue($order);

    	// Ship, Bill
        list($tpl->ship, $tpl->bill, $tpl->sp_id) = $CMS->order->getShipBillByOrder($order['ord_id']);
        
    	// output
    	$content = \core\ezy::render('preview_label_form', 'order');
    	$return = array( 
    		'content' 		=> $content, 
    		'urlPreview' 	=> "{$CMS->vars['root_domain']}/?site=order&subact=export_pdf&id={$order['ord_id']}&suffix=label&email_tpl=send_shipping_file", 

        	'urlSendEmail'	=>	"{$CMS->vars['root_domain']}/?site=order&subact=send_email&id={$order['ord_id']}&suffix=label", 
    	);

    	if( $CMS->input['view_content'] == 1 )
    	{
    		print $content; exit;
    	}

    	echo @json_encode($return, JSON_UNESCAPED_UNICODE); exit;
    	exit;
    }

    /**
     * Export file to pdf
     */
    function exportPdf()
    {
        global $CMS, $member;

        // Order
        $order = $CMS->order->get_info($CMS->input['id']);

        // Customer
        $customer = $CMS->customer->getInfo($order['cus_id']);

        // Ship, Bill
        list($shipping, $billing, $sp_id) = $CMS->order->getShipBillByOrder($order['ord_id']);

        //Get email template
        $email_tpl = $CMS->emailtpl->get_info($CMS->input['email_tpl'] ? $CMS->input['email_tpl'] : 'send_shipping_file');

        // Set data email
        $host = parse_url($CMS->vars['root_domain'])['host'];
        $email_title = $CMS->email->convert_v2($email_tpl['emailtpl_title'], ['website_name' => $host]);
        $email_content = $CMS->email->convert_v2($email_tpl['emailtpl_content'], ['ord_name' => $order['ord_name'], 'website_name' => $host]);

        $email_to = $customer['cus_email'] ? $customer['cus_email'] : ($billing['email'] ? $billing['email'] :  $shipping['email']);
        $email_cc = $billing['email'] != $email_to ? $billing['email'] : ( $shipping['email'] != $email_to ? $shipping['email'] : null );
        $email_bcc = ( $shipping['email'] != $email_cc AND $shipping['email'] != $email_to ) ? $shipping['email'] : null;

        $email = [
            'email_from' => isset($CMS->vars['smtp_email_display']) ? $CMS->vars['smtp_email_display'] : null,
            'email_to' => $email_to, 
            'email_cc' => $email_cc, 
            'email_bcc' => $email_bcc, 
            'email_title' => $email_title, 
            'email_content' => $email_content, 
        ];

        $return = array(
        	'status' 	=> 'ok', 
        );

        if( $fileName = $CMS->input['file'] )
        {
            $filePath = "/order_files/{$order['ord_id']}/{$fileName}";
            if( !is_file($CMS->vars['upload_dir'].'/'.$filePath) )
            {
                $return['status'] = 'fail';
                $return['msg'] = 'File not found';
            }
            else
            {
                $fileUrl = $CMS->vars['upload_url'].'/'.$filePath;
                $return['fileUrl'] = "{$fileUrl}?".$CMS->class->random->character(10);
                $return['email'] = $email;
            }

            echo @json_encode($return, JSON_UNESCAPED_UNICODE); exit;
        }
        else
        {
            //create folder
            $folder = 'tmp';
            $CMS->class->image->check_folder_img($folder,'',0);

            $content = $_POST['content'];
            $content = str_replace(['<style><!--','--></style>'],['<style>','</style>'],$content);

            $fileName= "tmp_order_user{$member['user_id']}_ord{$order['ord_id']}_{$CMS->input['suffix']}.pdf";

            if( is_file("{$CMS->vars['upload_dir']}/{$folder}/{$fileName}") )
            {
                @unlink("{$CMS->vars['upload_dir']}/{$folder}/{$fileName}");
            }

            ob_start();
            $CMS->class->html2pdf->build();

            $CMS->class->html2pdf->pdf->SetDisplayMode('fullpage');

            $CMS->class->html2pdf->pdf->WriteHTML($content);

            $CMS->class->html2pdf->pdf->Output("{$CMS->vars['upload_dir']}/{$folder}/{$fileName}");

            $return['fileUrl'] = "{$CMS->vars['upload_url']}/{$folder}/{$fileName}?".$CMS->class->random->character(10);

            $return['email'] = $email;

            echo @json_encode($return,JSON_UNESCAPED_UNICODE); exit;
        }
    }

    /**
     * Send email
     */
	function sendEmail()
    {
        global $CMS, $member;

        $return = [];

        $host = parse_url($CMS->vars['root_domain'])['host'];

        $email_from = trim($CMS->input['email_from']);
        $email_to = trim($CMS->input['email_to']);
        $email_cc = trim($CMS->input['email_cc']);
        $email_bcc = trim($CMS->input['email_bcc']);
        $email_title = trim($CMS->input['email_title']);
        $email_content = trim($_POST['email_content']);

        $order = $CMS->order->get_info($CMS->input['id']);
        $iframe = trim($CMS->input['iFrame']);
        $backTo = trim($CMS->input['backTo']);

        //Copy to store folder
        $CMS->class->image->check_folder_img('order_files','',0);
        $folder = "order_files/{$order['ord_id']}";
        $fileName = "{$CMS->vars['this_year']}{$CMS->vars['this_month']}{$CMS->vars['this_day']}-{$order['ord_id']}-{$CMS->class->random->character(4)}.pdf";
        $CMS->class->image->check_folder_img($folder,'',0);

        $flag = true;

        //Check file temp or file available
        if($backTo == 'getExportFiles') //available file
        {
            $attachFile = str_replace($CMS->vars['upload_url'],$CMS->vars['upload_dir'],$iframe);
            $attachFile = strtok($attachFile,'?');
            $flag = file_exists($attachFile);
        }
        else //tmp file
        {
            $flag = @copy("{$CMS->vars['upload_dir']}/tmp/tmp_order_user{$member['user_id']}_ord{$order['ord_id']}_{$CMS->input['suffix']}.pdf","{$CMS->vars['upload_dir']}/{$folder}/{$fileName}");

            $attachFile = "{$CMS->vars['upload_dir']}/{$folder}/{$fileName}";
        }

        if( $flag )
        {
            $log_add = "";

            if($email_cc)
            {
                $log_add .= " - CC: $email_cc";
            }

            if($email_bcc)
            {
                $log_add .= " - BCC: $email_bcc";
            }

            //Logs
            $CMS->class->logs->key = "order_{$order['ord_id']}";
            $CMS->class->logs->insert("A file (<a href=\"{$CMS->vars['root_domain']}/order/{$fileName}\">{$fileName}</a>) has been sent to {$email_to}{$log_add}");

            // Quick call send email in transactions kernel
            $data = $CMS->transactions->sendEmail($email_to, $email_title, $email_content, 0, $email_from, $email_from, $email_cc,$email_bcc,$attachFile);
        }
        else
        {
            $data = false;
        }

        if($data)
        {
            $return['status'] = 'success';
            $return['msg'] = $CMS->lang['send_mail_success'];
        }
        else
        {
            $return['status'] = 'fail';
            $return['msg'] = $CMS->lang['send_mail_fail'];
        }

        echo json_encode($return); exit;
    }

    function calculateShipFee()
    {
    	global $CMS, $member;

    	$type = isset($CMS->input['type']) ? $CMS->input['type'] : 0;
    	if( $type != 1 )
    	{
    		unset($CMS->input['ord_fee_shipping']);
    	}

    	$results = $CMS->transactions->calculate();
    	input::jsonEncode($results);
    	exit;
    }

    function setRatingStatus()
    {
        global $CMS, $DB;

        $rating_status = input::get("status") * 1;
        $order_id = input::get("id") * 1;

        $order = $CMS->order->get_info($order_id);

        if($order) {

            $data = [
                'ord_id' => $order_id,
                'ord_rating_status' => $rating_status
            ];
            if($data = $DB->update('order', $data, 'ord_id')) {
                $rs = [
                    'status' => 'success',
                    'data' => $data
                ];
            } else {
                $rs = [
                    'status' => 'failed',
                    'msg' => "Lỗi khi cập nhật trạng thái đánh giá."
                ];
            }

        } else {
            $rs = [
                'status' => 'failed',
                'msg' => input::lang('no_data')
            ];
        }

        input::jsonEncode($rs);
    }

    function get_info_rating()
    {
        global $CMS, $DB;

        $order_id = input::get("id") * 1;

        input::jsonEncode(\CheckIn\Model\order::checkRating($order_id));
    }
}