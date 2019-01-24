<?php
use core\ezy;
use lib\input;

$transactions = new Transactions;
$transactions->auto_run();

class Transactions {

	public $html;

    /**
     * Transactions constructor.
     */

	function __construct() {
		global $CMS, $DB, $member;

		if (!$CMS->vars['is_login']) $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=login");
		$CMS->class->language->load("transactions");
		$this->html = $CMS->class->template->load_template("skin_transactions");
		$this->html_form = $CMS->class->template->load_template("skin_transactions_form");
		$CMS->core->page_title = $CMS->core->page_title = ($CMS->input['type']) ? "-> {$CMS->lang['trx_type_'.$CMS->input['type']]}" : $CMS->lang['trx_title'];
	}

    /**
     * Auto run
     */

	public function auto_run() 
        {
            global $CMS, $DB, $member;
            
            /*
             * hvu remove this condition, for request search all sales and expenses
             * 15/08/2017
            if($CMS->input['act'] != 'show')
            {
                if (empty($CMS->input['type']) || ! in_array($CMS->input['type'], array(1, 2)))
                {
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=transactions&type=1");
                }
            }
             */

		switch ($CMS->input['act']) {
			case 'add':
				$this->add();
				break;
            case 'copy':
                $this->copy();
                break;
			case 'edit':
				$this->edit();
				break;
			case 'show':
				$this->show();
				break;
			case 'delete':
				$this->delete();
				break;
            case 'preview':
                $this->preview();
                break;
            case 'print':
                $this->preview();
                break;
            case 'import':
                $this->import();
                break;
            case 'export':
                $this->export();
                break;
            case 'send':

                if(\lib\input::get('subact') == 'load-email-tpl')
                {
                    $this->loadEmailTpl();
                }

                $this->send();
                break;
			default:
			    if( \lib\input::get('subact') == 'autocomplete')
                {
                    $this->autocomplete();
                }
                else if( \lib\input::get('subact') == 'export_pdf')
                {
                    $this->exportPDF();
                }
                else if( \lib\input::get('subact') == 'get_export_files')
                {
                    $this->getExportFiles();
                }
                else if( \lib\input::get('subact') == 'check_linked')
                {
                    $this->checkLinked();
                }
                else if( \lib\input::get('subact') == 'get-unbilled-by-customer')
                {
                    $this->getUnbilledByCustomer();
                }
                else if( \lib\input::get('subact') == 'get-invoice')
                {
                    $this->getInvoice();
                }
                else if( \lib\input::get('subact') == 'get-expense-bill')
                {
                    $this->getExpenseBill();
                }
                else
                {
                    $this->default_page();
                }
				break;
		}
	}

    /**
     * Default page
     */

	public function default_page() {
		global $CMS, $DB, $member;

        //Check date from dashboard
        if(isset($CMS->input['dashboard_date']))
        {
            $dashboard_date_explode = explode("/",$CMS->input['dashboard_date']);
            $dashboard_date_cnt =  count($dashboard_date_explode);

            if($dashboard_date_cnt == 3)
            {
                $CMS->input['date_from'] = $CMS->input['date_to'] = $CMS->input['dashboard_date'];
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

                $CMS->input['date_from'] = implode('/',$date_convert);
                $CMS->input['date_to'] = date(str_replace('d','t',$CMS->vars['dateformat_php'][$CMS->vars['date_format']]), $CMS->class->date->date2time($CMS->input['date_from'])+(3600*24));
            }
        }

        if(! \lib\security::checkPermission($CMS->input['site'], 'all_branches')) {
            $CMS->input['store_id'] = $member['store_id'];
        }

		$out = $this->html->head();

		$data = $CMS->transactions->listing();
		if (count($data)) {
			foreach ($data as $dt) {
			    $invoiceInfo = $CMS->transactions->convertReceiveInvoiceInfo($dt['trx_invoice_info']);
			    $dt['invoiceInfo'] = $invoiceInfo;
				$out .= $this->html->mid($dt);
			}
		} else {
			$out .= $this->html->none();
		}
		$out .= $this->html->foot();
		$CMS->output .= $out;
	}

	public function add() {
		global $CMS, $DB, $member;

		//Test
        /*$data = $CMS->transactions->getInfo($CMS->input['id']);
        $data['account_info'] = $CMS->accounts->getInfo($data['trx_account']);
        $data['accounts_type_info'] = $CMS->accounts_type->getInfo($data['at_id']);
        $data['cus_info'] = $data['cus_type']==1 ? $CMS->customer->getInfo($data['cus_id']) : null;
        $data['supplier_info'] = $data['cus_type']==2 ? $CMS->supplier->get_info($data['supplier_id']): null;
        $data['assign_info'] = $data['cus_type']==3 ? $CMS->user->get_info($data['user_assign']): null;

        $email_content_func = "email_content_{$data['trx_subtype']}";

        //Send mail
        $CMS->email->email_template = "invoice_info";
        $data['cus_email'] = 'phuong@3f.team';
        $CMS->email->email_to = $data['cus_email'];
        $CMS->email->email_toname = $data['cus_email'];

        $data = $CMS->transactions->convertvalue($data);

        foreach($data as $k => $v)
        {
            if(!is_array($v))
            {
                $data[$k] = strip_tags($v);
            }
        }

        $CMS->email->data['content'] = $this->html->$email_content_func($data);
        echo $CMS->email->data['content']; exit;
        $CMS->email->quick_send(0,0); exit;*/
        //End test


		if (empty($CMS->input['sub']) || ! in_array($CMS->input['sub'], array(1,2,3,4,5,6,7,8))) $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=transactions&type=1");
		if ($CMS->input['request_method'] == 'post') {

			$_SESSION['trx_error'] = array();

            //Check CSRF
            if(!\lib\security::check_token())
            {
                array_push($_SESSION['trx_error'], $CMS->lang['invalid_token']);
            }

			/*// all
            if($CMS->input['cus_type'] == 1) //KH
            {
                if (empty($CMS->input['cus_id'])) {
                    array_push($_SESSION['trx_error'], $CMS->lang['trx_cus_err']);
                }
            }

            if($CMS->input['cus_type'] == 2) //NCC
            {
                if (empty($CMS->input['supplier_id'])) {
                    array_push($_SESSION['trx_error'], $CMS->lang['trx_supplier_err']);
                }
            }

            if($CMS->input['cus_type'] == 3) //NV
            {
                if (empty($CMS->input['user_assign'])) {
                    array_push($_SESSION['trx_error'], $CMS->lang['trx_user_assign_err']);
                }
            }*/

			/*if ( (empty($CMS->input['trx_email']) || ! Validate::isEmail($CMS->input['trx_email']))  && $CMS->input['sub'] != 6 && $CMS->input['sub'] != 5 && $CMS->input['sub'] != 2) {
				array_push($_SESSION['trx_error'], $CMS->lang['trx_cus_email_err']);
			}
			if (empty($CMS->input['trx_address']) && $CMS->input['sub'] != 2 && $CMS->input['sub'] != 7 && $CMS->input['sub'] != 6) {
				array_push($_SESSION['trx_error'], $CMS->lang['trx_address_err']);
			}*/

//			if (empty($CMS->input['trx_payment_date'])) {
//				array_push($_SESSION['trx_error'], $CMS->lang['trx_payment_date_err']);
//			}

			$CMS->input['trx_note'] = substr($CMS->input['trx_note'], 0, 255);

			switch ($CMS->input['sub']) {
                case 5:
				case 1:
					if (empty($CMS->input['trx_terms']) || ! Validate::isNum($CMS->input['trx_terms']) || $CMS->input['trx_terms'] < 0) {
						$CMS->input['trx_terms'] = '0';
					}

                    $calculate_result = $CMS->transactions->calculate();

                    $CMS->input['trx_total'] = $calculate_result['total'];
                    $CMS->input['trx_amount'] = $calculate_result['subtotal'];
                    $CMS->input['trx_tax'] = $calculate_result['tax'];
                    $CMS->input['trx_total_discount'] = $calculate_result['discount'];

                    if($CMS->input['cus_type'] == 1)
                    {
                        $custype_id = $CMS->input['cus_id'];
                    }
                    elseif ($CMS->input['cus_type'] == 2)
                    {
                        $custype_id = $CMS->input['supplier_id'];
                    }
                    elseif ($CMS->input['cus_type'] == 3)
                    {
                        $custype_id = $CMS->input['user_assign'];
                    }

                    $CMS->input['trx_invoice_no'] = $CMS->transactions->getNoInvoice($CMS->input['sub']);

					break;
                case 7:
				case 2:

				    $CMS->input['trx_total'] = 0;

                    $invoices = $CMS->input['tri_payment'];

                    foreach ($invoices as $key => $value)
                    {
                        $CMS->input['trx_total'] += $value;
                    }

                    $CMS->input['trx_amount'] = $CMS->input['trx_total'];

					if (empty($CMS->input['trx_method']) || ! in_array($CMS->input['trx_method'], array(0,1))) {
						$CMS->input['trx_method'] = '0';
					}
					if ($CMS->input['trx_account'] == 0 || empty($CMS->input['trx_account']) || ! $CMS->accounts->getInfo($CMS->input['trx_account'], 'accounts_id')) {
						$CMS->input['trx_account'] = '0';
					}

					if(!isset($CMS->input['trx_receive_payment']))
                    {
                        $CMS->input['trx_receive_payment'] = $CMS->input['trx_total'];
                    }

					break;
				case 3:
                case 6:
					if (empty($CMS->input['trx_method']) || ! in_array($CMS->input['trx_method'], array(0,1))) {
						$CMS->input['trx_method'] = '0';
					}
					if ($CMS->input['trx_account'] == 0 || empty($CMS->input['trx_account']) || ! $CMS->accounts->getInfo($CMS->input['trx_account'], 'accounts_id')) {
						$CMS->input['trx_account'] = '0';
					}

                    $calculate_result = $CMS->transactions->calculate();

                    $CMS->input['trx_total'] = $calculate_result['total'];
                    $CMS->input['trx_amount'] = $calculate_result['subtotal'];
                    $CMS->input['trx_tax'] = $calculate_result['tax'];
                    $CMS->input['trx_total_discount'] = $calculate_result['discount'];

					break;
				case 4:

					if (empty($CMS->input['trx_expiration_date'])) {
						array_push($_SESSION['trx_error'], $CMS->lang['trx_expiration_date_err']);
					}

                    $calculate_result = $CMS->transactions->calculate();

                    $CMS->input['trx_total'] = $calculate_result['total'];
                    $CMS->input['trx_amount'] = $calculate_result['subtotal'];
                    $CMS->input['trx_tax'] = $calculate_result['tax'];
                    $CMS->input['trx_total_discount'] = $calculate_result['discount'];

					break;
                case 8:

                    if (empty($CMS->input['trx_credit_memo_date'])) {
                        array_push($_SESSION['trx_error'], $CMS->lang['trx_credit_memo_date_err']);
                    }

                    $calculate_result = $CMS->transactions->calculate();

                    $CMS->input['trx_total'] = $calculate_result['total'];
                    $CMS->input['trx_amount'] = $calculate_result['subtotal'];
                    $CMS->input['trx_tax'] = $calculate_result['tax'];
                    $CMS->input['trx_total_discount'] = $calculate_result['discount'];

                    break;
				default:
					break;
			}

			if(!$CMS->input['trx_total'])
            {
                $_SESSION['trx_error'] = $CMS->lang['trx_total_err'];
            }

			if (empty($_SESSION['trx_error'])) {

			    if(! lib\security::checkPermission($CMS->input['site'], 'all_branches')) {
			        $CMS->input['store_id'] = $member['store_id'];
                }

				switch ($CMS->input['sub']) {
                    case 5:
					case 1:
                        $trx = $CMS->transactions->add();
                        $id = $trx['trx_id'];

//						if (isset($CMS->input['product_name']))
						{
							$CMS->transactions->addItem($id);
						}
						break;
                    case 7:
					case 2:
                        $trx = $CMS->transactions->add();
						break;
                    case 6:
                    case 3:

                        $CMS->input['trx_status'] = 1; //paid: đánh dấu đã thanh toán

                        $trx = $CMS->transactions->add();
                        $id = $trx['trx_id'];

//                        if (isset($CMS->input['product_name']))
                        {
                            $CMS->transactions->addItem($id);
                        }
                        break;
					case 4:

                        if($CMS->input['trx_status'] == 5) //accepted
                        {
                            /*
                             * Chuyển qua thành hóa đơn thuòng (pending)
                             * */
                            $CMS->vars['trx_is_accepted'] = 1;

                            $CMS->input['type'] = 1;
                            $CMS->input['sub'] = 1;
                            $CMS->input['trx_status'] = 0;

                            if($CMS->input['cus_type'] == 1)
                            {
                                $custype_id = $CMS->input['cus_id'];
                            }
                            elseif ($CMS->input['cus_type'] == 2)
                            {
                                $custype_id = $CMS->input['supplier_id'];
                            }
                            elseif ($CMS->input['cus_type'] == 3)
                            {
                                $custype_id = $CMS->input['user_assign'];
                            }

                            $CMS->input['trx_payment_date'] = $CMS->input['trx_accepted_date'];
                            $CMS->input['trx_due_date'] = $CMS->input['trx_expiration_date'];

                            $CMS->input['trx_invoice_no'] = $CMS->transactions->getNoInvoice($CMS->input['sub']);
                        }

                        $trx = $CMS->transactions->add();
                        $id = $trx['trx_id'];

//						if (isset($CMS->input['product_name']))
						{
							$CMS->transactions->addItem($id);
						}
						break;
                    case 8:
                        $CMS->input['trx_status'] = 0; //pending: đầu tiên đánh dấu pending trước sau đó update lai trạng thái và thông số sau

                        $trx = $CMS->transactions->add();
                        $id = $trx['trx_id'];

//                        if (isset($CMS->input['product_name']))
                        {
                            $CMS->transactions->addItem($id);
                        }

                        break;
					default:
						break;
				}

                if($CMS->input['trx_send_email'] == 'on' && $id)
                {
                    $CMS->transactions->sendTrxEmail($id);
                }

                $CMS->transactions->attachFiles($id);

				$_SESSION['trx_success'] = "{$CMS->lang['trx_add_success_'.$CMS->input['sub']]} - {$trx['trx_code']} - {$CMS->lang['trx_status_0'.$trx['trx_status']]}";

				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=transactions&type={$CMS->input['type']}");
			}
		}

		$CMS->output .= $this->html->add($this->html_form);
	}
	public function edit() {
		global $CMS, $DB, $member;

//		if (empty($CMS->input['sub']) || ! in_array($CMS->input['sub'], array(1,2,3,4,5,6,7,8))) $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=transactions&type=1");

		if (!empty($CMS->input['id'])) {
			$data_info = $CMS->transactions->getInfo($CMS->input['id'], $CMS->input['type'], $CMS->input['sub']);

			if($data_info) {
			    if (! \lib\security::checkPermission($CMS->input['site'], 'all_branches')) {
			        if($data_info['store_id'] != $member['store_id']) {
                        $data_info = null;
                    }
                }
            }

			if (!empty($data_info)) {

                //Gan lai type va subtype de tranh nguoi dung sua loai tren url - nkvp - 11.12.2017
                $CMS->input['type'] = $data_info['trx_type'];
                $CMS->input['sub'] = $data_info['trx_subtype'];

//                $data_info = $CMS->transactions->editvalue($data_info);
				if ($CMS->input['request_method'] == 'post') {

					$_SESSION['trx_error'] = array();

                    //Check CSRF
                    if(!\lib\security::check_token())
                    {
                        array_push($_SESSION['trx_error'], $CMS->lang['invalid_token']);
                    }

					// all
//                    if($CMS->input['cus_type'] == 1) //KH
//                    {
//                        if (empty($CMS->input['trx_cus'])) {
//                            array_push($_SESSION['trx_error'], $CMS->lang['trx_cus_err']);
//                        }
//                    }
//
//                    if($CMS->input['cus_type'] == 2) //NCC
//                    {
//                        if (empty($CMS->input['supplier_id'])) {
//                            array_push($_SESSION['trx_error'], $CMS->lang['trx_supplier_err']);
//                        }
//                    }
//
//                    if($CMS->input['cus_type'] == 3) //NV
//                    {
//                        if (empty($CMS->input['user_assign'])) {
//                            array_push($_SESSION['trx_error'], $CMS->lang['trx_user_assign_err']);
//                        }
//                    }
//
//                    if ( (empty($CMS->input['trx_email']) || ! Validate::isEmail($CMS->input['trx_email']))  && $CMS->input['sub'] != 6 && $CMS->input['sub'] != 5 && $CMS->input['sub'] != 2) {
//                        array_push($_SESSION['trx_error'], $CMS->lang['trx_cus_email_err']);
//                    }
//                    if (empty($CMS->input['trx_address']) && $CMS->input['sub'] != 2 && $CMS->input['sub'] != 7 && $CMS->input['sub'] != 6) {
//                        array_push($_SESSION['trx_error'], $CMS->lang['trx_address_err']);
//                    }
//					if (empty($CMS->input['trx_payment_date'])) {
//						array_push($_SESSION['trx_error'], $CMS->lang['trx_payment_date_err']);
//					}
					$CMS->input['trx_note'] = substr($CMS->input['trx_note'], 0, 255);

					switch ($CMS->input['sub']) {
                        case 5:
						case 1:
							if (empty($CMS->input['trx_terms']) || ! Validate::isNum($CMS->input['trx_terms']) || $CMS->input['trx_terms'] < 0) {
								$CMS->input['trx_terms'] = '0';
							}

                            $calculate_result = $CMS->transactions->calculate();

                            $CMS->input['trx_total'] = $calculate_result['total'];
                            $CMS->input['trx_amount'] = $calculate_result['subtotal'];
                            $CMS->input['trx_tax'] = $calculate_result['tax'];
                            $CMS->input['trx_total_discount'] = $calculate_result['discount'];

                            if($CMS->input['cus_type']==1)
                            {
                                $old_custype_id = $data_info['cus_id'];
                                $custype_id = $CMS->input['user_id'];
                            }
                            else if($CMS->input['cus_type']==2)
                            {
                                $old_custype_id = $data_info['supplier_id'];
                                $custype_id = $CMS->input['supplier_id'];
                            }
                            else if($CMS->input['cus_type']==3)
                            {
                                $old_custype_id = $data_info['user_assign'];
                                $custype_id = $CMS->input['user_assign'];
                            }


                            $CMS->input['trx_invoice_no'] = intval($data_info['trx_invoice_no']) ? intval($data_info['trx_invoice_no']) : $CMS->transactions->getNoInvoice($CMS->input['sub']);


							break;
                        case 7:
						case 2:

                            $CMS->input['trx_total'] = 0;

                            $invoices = $CMS->input['tri_payment'];

                            foreach ($invoices as $key => $value)
                            {
                                $CMS->input['trx_total'] += $value;
                            }

							if (empty($CMS->input['trx_method']) || ! in_array($CMS->input['trx_method'], array(0,1))) {
								$CMS->input['trx_method'] = '0';
							}
							if ($CMS->input['trx_account'] == 0 || empty($CMS->input['trx_account']) || ! $CMS->accounts->getInfo($CMS->input['trx_account'], 'accounts_id')) {
								$CMS->input['trx_account'] = '0';
							}
							if($CMS->input['sub'] == 2)
                            {
                                $CMS->input['trx_status'] = 3;
                            }

							break;
                        case 6:
						case 3:
							if (empty($CMS->input['trx_method']) || ! in_array($CMS->input['trx_method'], array(0,1))) {
								$CMS->input['trx_method'] = '0';
							}
							if ($CMS->input['trx_account'] == 0 || empty($CMS->input['trx_account']) || ! $CMS->accounts->getInfo($CMS->input['trx_account'], 'accounts_id')) {
								$CMS->input['trx_account'] = '0';
							}
							if (empty($CMS->input['trx_terms']) || ! Validate::isNum($CMS->input['trx_terms']) || $CMS->input['trx_terms'] < 0) {
								$CMS->input['trx_terms'] = '0';
							}

                            $CMS->input['trx_status'] = 1; //paid: đánh dấu đã thanh toán

//							if($CMS->input['sub'] == 3)
                            {
                                $calculate_result = $CMS->transactions->calculate();

                                $CMS->input['trx_total'] = $calculate_result['total'];
                                $CMS->input['trx_amount'] = $calculate_result['subtotal'];
                                $CMS->input['trx_tax'] = $calculate_result['tax'];
                                $CMS->input['trx_total_discount'] = $calculate_result['discount'];
                            }

							break;
						case 4:
							if (empty($CMS->input['trx_expiration_date'])) {
								array_push($_SESSION['trx_error'], $CMS->lang['trx_expiration_date_err']);
							}

                            $calculate_result = $CMS->transactions->calculate();

                            $CMS->input['trx_total'] = $calculate_result['total'];
                            $CMS->input['trx_amount'] = $calculate_result['subtotal'];
                            $CMS->input['trx_tax'] = $calculate_result['tax'];
                            $CMS->input['trx_total_discount'] = $calculate_result['discount'];

                            if($CMS->input['trx_status'] == 5) //accepted
                            {
                                $CMS->vars['trx_is_accepted'] = 1;

                                /*
                                 * Chuyển qua thành hóa đơn thuòng (pending)
                                 * */
                                $CMS->input['type'] = 1;
                                $CMS->input['sub'] = 1;
                                $CMS->input['trx_status'] = 0;

                                if($CMS->input['cus_type'] == 1)
                                {
                                    $custype_id = $CMS->input['cus_id'];
                                }
                                elseif ($CMS->input['cus_type'] == 2)
                                {
                                    $custype_id = $CMS->input['supplier_id'];
                                }
                                elseif ($CMS->input['cus_type'] == 3)
                                {
                                    $custype_id = $CMS->input['user_assign'];
                                }

                                $CMS->input['trx_payment_date'] = $CMS->input['trx_accepted_date'];
                                $CMS->input['trx_due_date'] = $CMS->input['trx_expiration_date'];

                                $CMS->input['trx_invoice_no'] = $CMS->transactions->getNoInvoice($CMS->input['sub']);
                            }

							break;
                        case 8:
                            $calculate_result = $CMS->transactions->calculate();

                            $CMS->input['trx_total'] = $calculate_result['total'];
                            $CMS->input['trx_amount'] = $calculate_result['subtotal'];
                            $CMS->input['trx_tax'] = $calculate_result['tax'];
                            $CMS->input['trx_total_discount'] = $calculate_result['discount'];

                            if($CMS->input['cus_type']==1)
                            {
                                $old_custype_id = $data_info['cus_id'];
                                $custype_id = $CMS->input['user_id'];
                            }
                            else if($CMS->input['cus_type']==2)
                            {
                                $old_custype_id = $data_info['supplier_id'];
                                $custype_id = $CMS->input['supplier_id'];
                            }
                            else if($CMS->input['cus_type']==3)
                            {
                                $old_custype_id = $data_info['user_assign'];
                                $custype_id = $CMS->input['user_assign'];
                            }
                            break;
						default:
							break;
					}

                    if(!$CMS->input['trx_total'])
                    {
                        $_SESSION['trx_error'] = $CMS->lang['trx_total_err'];
                    }

					if (empty($_SESSION['trx_error'])) {

						$trx = $CMS->transactions->edit($data_info);
						switch ($CMS->input['sub']) {
                            case 5:
							case 1:
							case 3:
							case 6:
							case 8:
//								if (!empty($CMS->input['product_name']))
								{
									$CMS->transactions->clearItem($data_info['trx_id']);
									$CMS->transactions->addItem($data_info['trx_id']);
								}
								break;
                            case 7:
							case 2:
								break;
							case 4:

//								if (!empty($CMS->input['product_name']))
								{
									$CMS->transactions->clearItem($data_info['trx_id']);
									$CMS->transactions->addItem($data_info['trx_id']);
								}
								break;
							default:
								break;
						}

                        $CMS->transactions->attachFiles($CMS->input['id']);

                        if($CMS->input['trx_send_email'] == 'on' && $CMS->input['id'])
                        {
                            $CMS->transactions->sendTrxEmail($CMS->input['id']);
                        }

                        $_SESSION['trx_success'] = "{$CMS->lang['trx_edit_success_'.$CMS->input['sub']]} - {$trx['trx_code']} - {$CMS->lang['trx_status_0'.$trx['trx_status']]}";

						$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=transactions&type={$CMS->input['type']}");
					}
				}
				$CMS->output.=$this->html->edit($this->html_form, $data_info);
				return;
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=transactions&type={$CMS->input['type']}");
	}
	public function show() {
		global $CMS, $DB, $member;

		if (!empty($CMS->input['id'])) {
			$data_info = $CMS->transactions->getInfo($CMS->input['id']);
			if (!empty($data_info)) {

			    if(! \lib\security::checkPermission($CMS->input['site'], 'all_branches')) {
			        if($data_info['store_id'] != $member['store_id']) {
                        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=transactions&type={$CMS->input['type']}");
                    }
                }

                $CMS->transactions->status_font_size = 28;
				$CMS->output .= $this->html->show($CMS->transactions->convertvalue($data_info),$this->html_form);
				$CMS->output .= $CMS->global->logs("transaction_{$data_info['trx_id']}");
				return;
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=transactions&type={$CMS->input['type']}");
	}
	public function delete() {
		global $CMS, $DB, $member;
		if (empty($CMS->input['sub']) || ! in_array($CMS->input['sub'], array(1,2,3,4,5,6,7))) $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=transactions&type=1");
		if (!empty($CMS->input['id'])) {
			$data_info = $CMS->transactions->getInfo($CMS->input['id'], $CMS->input['type'], $CMS->input['sub']);
			if (!empty($data_info)) {
				$CMS->transactions->delete($data_info['trx_id']);
				$_SESSION['trx_success']=$CMS->lang['trx_r_deleted_success'];
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=transactions&type={$CMS->input['type']}");
	}
	public function receiptAddSearchCus() {
		global $CMS, $DB,$member;
		$key = isset($CMS->input['key']) ? $CMS->input['key'] : '';
		$data = $CMS->customer->searchFullName($key);
		$str = '';
		if(count($data)) {
			foreach ($data as $dt) {
				$str .= "<option value='{$dt['cus_full_name']} |-| {$dt['cus_email']} |-| {$dt['cus_address']} |-| {$dt['cus_id']}'>{$dt['cus_full_name']} ({$dt['cus_email']}) </option>";
			}
		} else {
			$str .= "<option value=''>{$CMS->lang['not_data']}</option>";
		}
		exit($str);
	}
	public function getInvoice() {
		global $CMS, $DB,$member;
		$arr = array('status' => 'error', 'message' => $CMS->lang['not_data']);
		if (!empty($CMS->input['code_id'])  && !empty($CMS->input['cus_type'])) {

			$data_info = $CMS->transactions->getInvoiceEstimate($CMS->input['id'], $CMS->input['code_id'], $CMS->input['type'], $CMS->input['cus_type']);

//			$cus_info = $CMS->customer->getInfo($CMS->input['cus_id']);

			if (!empty($data_info)) {
				$arr['status'] = 'success';
				$arr['message'] = '';
				/*$arr['cus_info'] = [
				    'id' => $cus_info['cus_id'],
				    'name' => $cus_info['cus_full_name'],
				    'email' => $cus_info['cus_email'],
				    'address' => $cus_info['cus_address'],
                ];*/

                $prefix_trx = $data_info['trx_type'] == 2 ? 'PAY' : 'INV';

				$arr['data'] = array(
					'invoice_no'	=>	$data_info['trx_id'],
                    'invoice_no_as'	=>	"<a href='{$CMS->vars['root_domain']}/?site=transactions&act=show&id={$data_info['trx_id']}' target='_blank'>{$prefix_trx}{$data_info['trx_invoice_no']}</a>",
					'description'	=>	!empty($data_info['trx_invoice_no']) && $data_info['trx_invoice_no'] > 0 ? 'NO.'.$data_info['trx_invoice_no'] : $CMS->lang['trx_subtype_0'.$data_info['trx_subtype']].' '.$data_info['trx_id'],
					'due' 			=>	$CMS->class->date->date_format(intval($data_info['trx_terms'])*60*60*24 + $data_info['trx_payment_date']),
					'original'		=>	$data_info['trx_total'],
					'open'			=>	$data_info['trx_receive_payment'],
					'payment'		=>	$data_info['trx_total'] - $data_info['trx_receive_payment'],
				);
			}
		}

		exit(json_encode($arr));
	}
	public function editInvoice() {
		global $CMS, $DB,$member;
		$arr = array('status' => 'error', 'message' => $CMS->lang['not_data']);
		if (!empty($CMS->input['cus_id']) && !empty($CMS->input['code_id'])) {
			$data_info = $CMS->transactions->getInfoItem($CMS->input['code_id']);
			if (!empty($data_info)) {
				$arr['status'] = 'success';
				$arr['message'] = array(
					'description'	=>	$data_info['tri_description'],
					'due' 			=>	'-',
					'original'		=>	$data_info['tri_total'],
					'open'			=>	$data_info['tri_total'],
					'payment'		=>	'0',
				);
				if (empty($arr['message'])) {
					$arr = array('status' => 'error', 'message' => $CMS->lang['not_data']);
				}
			}
		}
		exit(json_encode($arr));
	}

	function getExpenseBill()
    {
        global $CMS;

        $arr = array('status' => 'error', 'message' => $CMS->lang['not_data']);
        if (!empty($CMS->input['cus_id'])) {
            $data_info =  $CMS->transactions->getExpenseBill($CMS->input['cus_id']);
            if (!empty($data_info)) {
                $arr['status'] = 'success';
                $arr['message'] = '';

                foreach ($data_info as $info)
                {
                    $arr['data'][] = array(
                        'invoice_no'	=>	$info['trx_id'],
                        'description'	=>	!empty($info['trx_invoice_no']) && $info['trx_invoice_no'] > 0 ? 'NO.'.$info['trx_invoice_no'] : $CMS->lang['trx_subtype_0'.$info['trx_subtype']].' '.$info['trx_id'],
                        'due' 			=>	$CMS->class->date->date_format(intval($info['trx_terms'])*60*60*24 + $info['trx_payment_date']),
                        'original'		=>	$info['trx_total'],
                        'open'			=>	$info['trx_receive_payment'],
                        'payment'		=>	$info['trx_total'] - $data_info['trx_receive_payment'],
                    );
                }
            }
        }

        exit(json_encode($arr));
    }

    function getUnbilledByCustomer()
    {
        global $CMS;

        $arr = array('status' => 'error', 'message' => $CMS->lang['not_data']);
        if (!empty($CMS->input['id'])) {

            if($CMS->input['sub'] == 2)
            {
                $subtype = 1;
            }
            else if($CMS->input['sub'] == 7)
            {
                $subtype = 5;
            }
            else
            {
                echo @json_encode($arr); exit;
            }

            $data_info =  $CMS->transactions->getUnbilledByCustomer($CMS->input['id'], 0, $subtype, $CMS->input['cus_type']);
            if (!empty($data_info)) {
                $arr['status'] = 'success';
                $arr['message'] = '';

                foreach ($data_info as $info)
                {
                    $prefix_trx = $info['trx_type'] == 2 ? 'PAY' : 'INV';

                    $arr['data'][] = array(
                        'invoice_no'	=>	$info['trx_id'],
                        'invoice_no_as'	=>	"<a href='{$CMS->vars['root_domain']}/?site=transactions&act=show&id={$info['trx_id']}' target='_blank'>{$prefix_trx}{$info['trx_invoice_no']}</a>",
                        'description'	=>	!empty($info['trx_invoice_no']) && $info['trx_invoice_no'] > 0 ? 'NO.'.$info['trx_invoice_no'] : $CMS->lang['trx_subtype_0'.$info['trx_subtype']].' '.$info['trx_id'],
                        'due' 			=>	$CMS->class->date->date_format(intval($info['trx_terms'])*60*60*24 + $info['trx_payment_date']),
                        'original'		=>	$info['trx_total'],
                        'open'			=>	$info['trx_receive_payment'],
                        'remain'        => $info['trx_total'] - $info['trx_receive_payment'],
                        'payment'		=>	$info['trx_total'] - $info['trx_receive_payment'],
                    );
                }
            }
        }

        exit(json_encode($arr));
    }

    public function preview()
    {
        global $CMS;
        $id = $CMS->input['id'];
        $tran = $CMS->transactions->convertvalue($CMS->transactions->getInfo($id));

        if(isset($CMS->input['preview_type']) && $CMS->input['preview_type'] == 'commercial')
        {
            $preview = $this->html->preview2($tran);
        }
        else
        {
            $preview = $this->html->preview($tran);
        }

        if($CMS->input['act'] == 'preview' && $CMS->input['mode'] != 'compose')
        {
            ob_start();
            $CMS->class->html2pdf->build();

            $CMS->class->html2pdf->pdf->SetDisplayMode('fullpage');

            $CMS->class->html2pdf->pdf->WriteHTML($preview);

            $CMS->class->html2pdf->pdf->Output();
        }
        else
        {
            echo $preview;
        }

        exit;
    }

    function autocomplete()
    {
        global $CMS, $member;

        if(! \lib\security::checkPermission($CMS->input['site'], 'all_branches')) {
            $CMS->input['store_id'] = $member['store_id'];
        }

        $CMS->transactions->autocomplete();
    }

    function copy()
    {
        global $CMS;

        $id = intval($CMS->input['id']);

        $CMS->transactions->copy($id);

        $CMS->output .= $this->html->add($this->html_form);
    }

    function loadEmailTpl()
    {
        global $CMS;

        $id = intval($CMS->input['id']);

        $data = $CMS->transactions->loadEmailTpl($id);

        if(!$data)
        {
            $return = [
                'status' => 'err',
                'msg' => $_SESSION['msg']
            ];
        }
        else
        {
            $return = [
                'status' => 'ok',
                'data' => $data
            ];
        }

        header('Content-Type: application/json');
        echo @json_encode($return); exit;
    }

    /**
     * Send invoice to email
     */
	function send()
    {
        global $CMS, $member;

        $return = [];

        if($CMS->input['mode'] == 'inv')
        { 

            $host = parse_url($CMS->vars['root_domain'])['host'];

            $email_from = trim($CMS->input['email_from']);
            $email_to = trim($CMS->input['email_to']);
            $email_cc = trim($CMS->input['email_cc']);
            $email_bcc = trim($CMS->input['email_bcc']);
            $email_title = trim($CMS->input['email_title']);
            $email_content = trim($_POST['email_content']);

            $trx_id = intval($CMS->input['trx_id']);
            $trx = $CMS->transactions->getInfo($trx_id);
            $preview_type = trim($CMS->input['preview_type']);
            $iframe = trim($CMS->input['iFrame']);
            $backTo = trim($CMS->input['backTo']);

            //Copy to store folder
            $CMS->class->image->check_folder_img('invoice_files','',0);
            $folder = "invoice_files/{$trx_id}";
            $fileName = "{$CMS->vars['this_year']}{$CMS->vars['this_month']}{$CMS->vars['this_day']}-{$trx_id}-{$CMS->class->random->character(4)}.pdf";
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
                $flag = @copy("{$CMS->vars['upload_dir']}/tmp/tmp_inv_cus{$member['user_id']}_trx{$trx_id}.pdf","{$CMS->vars['upload_dir']}/{$folder}/{$fileName}");

                $attachFile = "{$CMS->vars['upload_dir']}/{$folder}/{$fileName}";
            }

            if($flag)
            {
                $CMS->transactions->updateFileCount($trx_id); //Update file number to transaction

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
                $CMS->class->logs->key = "transaction_{$trx_id}";
                $CMS->class->logs->insert("An invoice (<a href=\"{$CMS->vars['root_domain']}/billing/invoice/{$fileName}\">{$fileName}</a>) has been sent to {$email_to}{$log_add}");

                $data = $CMS->transactions->sendEmail($email_to, $email_title, $email_content, 0, $email_from, $email_from, $email_cc,$email_bcc,$attachFile);
            }
            else
            {
                $data = false;
            }
        }
        else
        {
            if(is_array($CMS->input['email_send_to']))
            {
                $CMS->input['email_send_to'] = implode(";",$CMS->input['email_send_to']);
            }

            if(is_array($CMS->input['email_cc']))
            {
                $CMS->input['email_cc'] = implode(";",$CMS->input['email_cc']);
            }

            if(is_array($CMS->input['email_bcc']))
            {
                $CMS->input['email_bcc'] = implode(";",$CMS->input['email_bcc']);
            }

            if(empty($CMS->input['email_send_to']))
            {
                $return['status'] = 'fail';
                $return['msg'] = $CMS->lang['incomplete_email'];
                echo json_encode($return); exit;
            }

            $data = $CMS->transactions->sendEmail($CMS->input['email_send_to'], $CMS->input['email_title'], $_POST['email_content'], 0,$CMS->input['email_from'], $CMS->input['email_from'], $CMS->input['email_cc'], $CMS->input['email_bcc']);
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

    /**
     * Export file invoice to pdf
     */
    function exportPdf()
    {
        global $CMS, $member;

        $return = ['status' => 'ok'];

        $trx_id = intval($CMS->input['trx_id']);

        //Get email template
        $trx_info = $CMS->transactions->getInfo($trx_id);
        $email_tpl = $CMS->emailtpl->get_info('send_invoice_file');

        $host = parse_url($CMS->vars['root_domain'])['host'];
        $email_title = $CMS->email->convert_v2($email_tpl['emailtpl_title'], ['website_name' => $host]);
        $email_content = $CMS->email->convert_v2($email_tpl['emailtpl_content'], ['trx_code' => $trx_info['trx_code'], 'website_name' => $host]);


        $email = [
            'email_from' => isset($CMS->vars['smtp_email_display']) ? $CMS->vars['smtp_email_display'] : null,
            'email_to' => isset($trx_info['cus_email']) ? $trx_info['cus_email'] : null,
            'email_title' => $email_title,
            'email_content' => $email_content,
        ];


        if($fileName = $CMS->input['file'])
        {
            $filePath = "/invoice_files/{$trx_id}/{$fileName}";
            if(!is_file($CMS->vars['upload_dir'].'/'.$filePath))
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

            echo @json_encode($return,JSON_UNESCAPED_UNICODE); exit;
        }
        else
        {
            //create folder
            $folder = 'tmp';
            $CMS->class->image->check_folder_img($folder,'',0);

            $content = $_POST['content'];
            $content = str_replace(['<style><!--','--></style>'],['<style>','</style>'],$content);



            $fileName= "tmp_inv_cus{$member['user_id']}_trx{$trx_id}.pdf";

            if(is_file("{$CMS->vars['upload_dir']}/{$folder}/{$fileName}"))
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
     * Get PDF files
     */
    function getExportFiles()
    {
        global $CMS;

        header('Content-Type: application/json; charset=utf-8');
        $trx_id = intval($CMS->input['id']);
        $return = $CMS->transactions->getExportFiles($trx_id);
        echo json_encode($return, JSON_UNESCAPED_UNICODE); exit;
    }

    /**
     * Import excel
     */
    public function import()
    {
        global $CMS;
        ezy::load_model("report");
        $CMS->transactions->importFromExcel();
        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=accounts_type");
    }

    /**
     * Export excel
     */
    public function export()
    {
        global $CMS;
        ezy::load_model("report");
        $link = $CMS->transactions->exportToExcel($CMS->input['is_template']);
//        header("location: {$link}");
        ezy::load_model("download");
        \models\download::sendFile($link);
    }

    /**
     * Check linked transaction
     * nkvp - 11.12.2017
     */
    public function checkLinked()
    {
        global $CMS;
        echo $CMS->transactions->checkLinked($CMS->input['id']) ? 'valid' : 'invalid'; exit;
    }
}
?>