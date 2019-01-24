<?php

use core\ezy;
use lib\input;
use models\attribute;
use models\classProduct as product;

//Load models
ezy::load_model("report");
ezy::load_model("attribute");

use models\shipping_services;
ezy::load_model("shipping_services");

$product = new p_;
$product->auto_run();

class p_ {
	public $html;
	function __construct() {
		global $CMS, $DB, $member;
		if (!$CMS->vars['is_login']) $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=login");

		$CMS->class->language->load("product");
		$CMS->class->language->load("variants");
		$CMS->class->language->load("shipping_fee");

		$this->html = $CMS->class->template->load_template("skin_product");
		$CMS->core->page_title = "-> {$CMS->lang['p_title']}";
	}
	function __destruct() { }
	public function auto_run() {
		global $CMS, $DB, $member;

        if(\lib\input::get('subact') == 'preview_barcode' OR \lib\input::get('subact') == 'arrange')
        {
            $CMS->input['act'] = \lib\input::get('subact');
            unset($CMS->input['subact']);
        }
 		
		$CMS->product->auto_run();

		if($CMS->input['act'] != "edit_do" )
		{
			$CMS->input['p_type'] = 0;
		}

		$CMS->class->language->load("store_request");// Dung cho cac module ajax chuyen tu store_request qua
		$CMS->class->language->load("supplier");
		$CMS->class->language->load("manufacture");
		switch ($CMS->input['act']) {
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
			case 'edit':
				$this->edit();
				break;
			case 'edit_do':
				$this->edit_do();
			break;	
			case 'show':
				$this->show();
				break;
			case 'delete':
				$this->delete();
				break;
			case 'search':
				$this->search();
				break;
            case 'export':
                $this->export();
                break;
            case 'import':
                $this->import();
                break;
			case 'get_district':/// Loi check phan quyen
				$this->getDistrict();
				break;
            case 'get_barcode_info':
                $this->get_barcode_info();
                break;
            case 'arrange':
                $this->arrange();
            break;    
            case 'preview_barcode':
                $this->preview_barcode();
                break;
			default:
				if(\lib\input::get('subact') == "ajax_add_manufacture")
				{
					$this->ajax_add_manufacture();
				}
				elseif(\lib\input::get('subact') == "search_g_product_ajax")
				{
					$this->search_g_product_ajax();
				}
				elseif(\lib\input::get('subact') == "autocomplete_quick_search")
				{
					$this->autocomplete_quick_search();
				}
				elseif(\lib\input::get('subact') == "ajax_add_product")
				{
					$this->ajax_add_product();
				}elseif(\lib\input::get('subact') == "ajax_edit_product")
				{
					$this->ajax_edit_product();
				}elseif(\lib\input::get('subact') == "ajax_edit_product_do")
				{
					$this->ajax_edit_product_do();
				}
				elseif(\lib\input::get('subact') == "order_add")
				{
					$this->order_add();
				}elseif(\lib\input::get('subact') == "order_add_multi")
				{
					$this->order_add_multi();
				}elseif(\lib\input::get('subact') == "delete_all")
				{
					$this->delete_all();
				}elseif(\lib\input::get('subact') == "hide_all")
				{
					$this->hide_all();
				}elseif(\lib\input::get('subact') == "getdistrict")
				{
					$this->getDistrict();
				}elseif(\lib\input::get('subact') == "getwards")
				{
					$this->getWards();
				}elseif(\lib\input::get('subact') == "updatetown")
				{
					$CMS->country->updateIDdistrictforWards();
				}elseif(\lib\input::get('subact') == "unlink_img")
				{
					$this->unlink_img();
				}elseif(\lib\input::get('subact') == "rm_parent_id")
				{
					$this->rm_parent_id();
				}elseif(\lib\input::get('subact') == "get_product_child")
				{
					$this->get_product_child();
				}elseif(\lib\input::get('subact') == "load_quick_data")
                {
                    $this->load_quick_data();
                }elseif(\lib\input::get('subact') == "autocomplete_data")
                {
                    $this->autocomplete_data();
                }elseif(\lib\input::get('subact') == "gethtml_attr")
                {
                    $this->gethtml_attr();
                }elseif(\lib\input::get('subact') == "search_product")
                {
                    $this->search_product();
                }elseif(\lib\input::get('subact') == "assign_to_store")
                {
                    $this->assign_to_store();
                }elseif(\lib\input::get('subact') == "unassign_store")
                {
                    $this->unassign_store();
                }elseif(\lib\input::get('subact') == "delete_ship_fee")
                {
                    $this->deleteShipFee();
                }elseif(\lib\input::get('subact') == "load_ship_fee")
                {
                    $this->loadShipFee();
                }elseif(\lib\input::get('subact') == "edit_ship_fee")
                {
                    $this->editShipFee();
                }elseif(\lib\input::get('subact') == "list_ship_fee")
                {
                    $this->listShipFee();
                }elseif(\lib\input::get('subact') == "add_ship_fee")
                {
                    $this->addShipFee();
                }else
				{
                    if(\lib\input::get('subact') == 'clear_cache')
                    {
                        $CMS->class->cache->mdelete('product');
                        $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                    }

                    if($CMS->input['commission'])
                    {
                        $this->commission();
                    }
                    else
                    {
                        $this->defaultPage();
                    }
				}
			break;
				
		}
	}

	// public function defaultPage() {
	// 	global $CMS, $DB, $member;
	// 	$out .= $this->html->head();
	// 	$data = $CMS->product->listing();
	// 	if (count($data)) {
	// 		foreach ($data as $dt) {
	// 			$dt = $CMS->product->convertvalue($dt);
	// 			$out .= $this->html->mid($dt);
	// 		}
	// 	} else {
	// 		$out .= $this->html->none();
	// 	}
	// 	$out .= $this->html->foot();
	// 	$CMS->output.=$out;
	// }

    public function defaultPage() {
        global $CMS, $DB, $member, $tpl;

        if( $CMS->vars['addon_goods_enable'] == 1 )
		{
        	list($tpl->row_store, $tpl->list_store) = $CMS->store->get_list_store($data['store_id'], 0, 1, 1);
        }

        $CMS->product->listingTplData();

        if( !empty(product::$form_theme) )
        {
        	// Data for shipping & fee
	        $tpl->shipping_type_service = \models\shipping_services::$shipping_type_service;
	        $tpl->option_shipping_service = '';
	        foreach( $tpl->shipping_type_service as $key => $value )
	        {
	            $tpl->option_shipping_service .= "<option value='{$key}'>{$value}</option>";
	        }
	        $tpl->dataShipFee = \models\shipping_services::generalDataShipFee($CMS->input);
	        
            $CMS->product->dataForm();
            $tpl->popupForm = ezy::render(product::$form_theme);
        }

        $tpl->main_form = ezy::render("main_form","product");

        if($CMS->input['ajax'] == 1)
        {
            echo $tpl->main_form; exit;
        }

        $CMS->output .= ezy::html("main");
    }

	public function add() {
		global $CMS, $DB, $member, $tpl;

		// data for add store
		$tpl->store_backend_type = ezy::$store_backend_type;

		// Data for shipping & fee
        $tpl->shipping_type_service = \models\shipping_services::$shipping_type_service;
        $tpl->option_shipping_service = '';
        foreach( $tpl->shipping_type_service as $key => $value )
        {
            $tpl->option_shipping_service .= "<option value='{$key}'>{$value}</option>";
        }
        $tpl->dataShipFee = \models\shipping_services::generalDataShipFee($CMS->input);

		// Custom form
		if( !empty(product::$form_theme) )
		{
            $CMS->product->dataForm();
            $CMS->output .= ezy::html(product::$form_theme);
		}
		// Normal form
		else
		{ 
			$CMS->product->dataForm();
			// Mặc định thì chạy qua form_merchant
			$CMS->output .= ezy::html("form_merchant");
            // $CMS->output.= $this->html->add();
		}

	}

	public function add_do() {
		global $CMS, $DB, $member, $tpl;
		
		$product = $CMS->product->add();
		if(is_array($product))
		{
			if(intval($CMS->input['add_product_option']) == 0)
			{
				$_SESSION['msg'] .= $CMS->lang['p_add_success'];
			}
			else
			{
				$_SESSION['msg'] .= $CMS->lang['p_add_draft_success'];
			}

			// add shipping fee
			\models\shipping_services::addByProduct($CMS->input, $product['product_id']);

            if($CMS->input['ajax'])
            {
                $ajaxReturn = [
                    'status' => 'ok',
                    'msg' => $_SESSION['msg'],
                ];
                unset($_SESSION['msg']);

                input::jsonEncode($ajaxReturn);
            }

			//Check action redirect
			$act_redirect =  $CMS->input['action_redirect'];
			if($act_redirect == "list" OR $act_redirect == "")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
			}
			elseif($act_redirect == "add")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=add");
			}
			elseif($act_redirect == "detail")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=show&id={$product['product_id']}");
			}
		}

		//fail
        if($CMS->input['ajax'])
        {
            $ajaxReturn = [
                'status' => 'fail',
                'msg' => $_SESSION['error_msg'],
            ];
            unset($_SESSION['error_msg']);
            input::jsonEncode($ajaxReturn);
        }

        // Data for shipping & fee
        $tpl->shipping_type_service = \models\shipping_services::$shipping_type_service;
        $tpl->option_shipping_service = '';
        foreach( $tpl->shipping_type_service as $key => $value )
        {
            $tpl->option_shipping_service .= "<option value='{$key}'>{$value}</option>";
        }
        $tpl->dataShipFee = \models\shipping_services::generalDataShipFee($CMS->input);

		// Custom form
		if( !empty(product::$form_theme) )
		{
            $CMS->product->dataForm();
            $CMS->output .= ezy::html(product::$form_theme);
		}
		// Normal form
		else
		{
			$CMS->product->dataForm();
			$CMS->output .= ezy::html("form_merchant");
            // $CMS->output.=$this->html->add($CMS->input);
		}
		
	}


	public function edit() 
	{
		global $CMS, $tpl, $DB, $member;
		$data = $CMS->product->getInfo($CMS->input['id']);
		$data = $CMS->product->convertvalue($data);

		// Data for shipping & fee
        $tpl->shipping_type_service = \models\shipping_services::$shipping_type_service;
        $tpl->option_shipping_service = '';
        foreach( $tpl->shipping_type_service as $key => $value )
        {
            $tpl->option_shipping_service .= "<option value='{$key}'>{$value}</option>";
        }
        $dataShipFeeOld = \models\shipping_services::getDataShipFeeByProductId($data['product_id']);
        $dataShipFeeOld = \models\shipping_services::convertDataShipFee($dataShipFeeOld);
        $dataShipFee 	= \models\shipping_services::generalDataShipFee($CMS->input);
        $tpl->dataShipFee = array_merge($dataShipFeeOld, $dataShipFee);

        // Custom form
        if( !empty(product::$form_theme) )
        {
            $CMS->product->dataForm($data);
            $CMS->output .= ezy::html(product::$form_theme);
        }
        // Normal form
        else
        { 
        	$CMS->product->dataForm($data);
			$CMS->output .= ezy::html("form_merchant");
            // $CMS->output.=$this->html->edit($data);
        }
	}

	public function edit_do() {
		global $CMS, $DB, $member, $tpl;
		$act_redirect =  $CMS->input['action_redirect'];
		// print $act_redirect;exit;
		if($CMS->product->edit() == true)
		{
			// Edit shipping fee
			\models\shipping_services::editByProduct($CMS->input, $CMS->input['id']);
			
            if($CMS->input['ajax'])
            {
                $ajaxReturn = [
                    'status' => 'ok',
                    'msg' => $_SESSION['msg'],
                ];
                unset($_SESSION['msg']);

                input::jsonEncode($ajaxReturn);
            }

			// $act_redirect = intval($CMS->input['action_redirect']);
			if($act_redirect == "list")
			{
				if($CMS->input['p_type'] == 0)
				{
					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=product");
				}
				else
				{
					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=service");
				}
				//$CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
			}
			elseif($act_redirect == "edit")
			{
				if($CMS->input['p_type'] == 0)
				{
					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=product&act=edit&id={$CMS->input['id']}");
				}
				else
				{
					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=service&act=edit&id={$CMS->input['id']}");
				}

				//$CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=edit&id={$CMS->input['id']}");
			}
		}
		else
		{
            if($CMS->input['ajax'])
            {
                $ajaxReturn = [
                    'status' => 'fail',
                    'msg' => $_SESSION['error_msg'],
                ];
                unset($_SESSION['error_msg']);
                input::jsonEncode($ajaxReturn);
            }

            $data_info = $CMS->product->getInfo($CMS->input['id']);

            // Data for shipping & fee
	        $tpl->shipping_type_service = \models\shipping_services::$shipping_type_service;
	        $tpl->option_shipping_service = '';
	        foreach( $tpl->shipping_type_service as $key => $value )
	        {
	            $tpl->option_shipping_service .= "<option value='{$key}'>{$value}</option>";
	        }
	        $dataShipFeeOld = \models\shipping_services::getDataShipFeeByProductId($CMS->input['id']);
	        $dataShipFeeOld = \models\shipping_services::convertDataShipFee($dataShipFeeOld);
	        $dataShipFee 	= \models\shipping_services::generalDataShipFee($CMS->input);
	        $tpl->dataShipFee = array_merge($dataShipFeeOld, $dataShipFee);

			// Custom form
			if( !empty(product::$form_theme) )
			{
	            $CMS->product->dataForm($data_info);
	            $CMS->output .= ezy::html(product::$form_theme);
			}
			// Normal form
			else
			{
				$CMS->product->dataForm($data_info);
				$CMS->output .= ezy::html("form_merchant");

	   //          $data_info = $CMS->product->getInfo($CMS->input['id']);
				// $data_info = $CMS->product->convertvalue($data_info);
				// $CMS->output.=$this->html->edit($data_info);
					return;
			}
			
		}
 
		


	}


	public function show() {
		global $CMS, $DB, $member, $tpl;
		$tpl->css_show = $CMS->input['site'] == "service" ? "block" : "none";
        $tpl->css_hide = $CMS->input['site'] == "service" ? "none" : "block";
        
		if (!empty($CMS->input['id'])) {
			$data_info = $CMS->product->getInfo($CMS->input['id']);
			if ($data_info) 
			{
				$tpl->data = $CMS->product->convertvalue($data_info);
				$tpl->logs = $CMS->global->logs("product_{$CMS->input['id']}");

				// Shipping & fee
				$tpl->dataShipFee = \models\shipping_services::getDataShipFeeByProductId($CMS->input['id'], false);
		        $tpl->shipping_type_service = \models\shipping_services::$shipping_type_service;
		        $tpl->option_shipping_service = '';
		        foreach( $tpl->shipping_type_service as $key => $value )
		        {
		            $tpl->option_shipping_service .= "<option value='{$key}'>{$value}</option>";
		        }
				
				// Custom show
				$show_theme = str_replace('form_', 'show_', product::$form_theme);
				if( ! empty(product::$form_theme) AND is_file(ezy::getLayoutPath($show_theme)) )
				{
		            $CMS->output .= ezy::html($show_theme);
				}
				
				// Normal show
				else
				{
					$CMS->output .= ezy::html("show_merchant");
				}
				
				return;
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
	}

	public function delete()
	{
		global $CMS, $DB, $member;
		if (!empty($CMS->input['id'])) {
			
			if ( $CMS->vars['addon_goods_enable'] == 0 )
        	{
        		$CMS->product->delete_normal($CMS->input['id']);
				
        	}else
        	{
        		$data_info = $CMS->product->getInfo($CMS->input['id']);
        		// Web us không dùng kho hàng
				if ($data_info) 
				{
					if($CMS->product->deleted($data_info['product_id']) == true)
					{
						$_SESSION['msg']=$CMS->lang['p_deleted_success'];
					}
					
				}
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
	}

	function delete_all()
	{
		global $CMS;
 
		if ( $CMS->vars['addon_goods_enable'] == 0 )
	    {
			for ( $i = 0; $i < intval( $CMS->input["data_cnt"] ); $i++ )
			{
				$id = intval( $CMS->input["id_{$i}"] );
					
				if ( $id )
				{
	        		$CMS->product->delete_normal($id);
		        }
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
	}

	function hide_all()
	{
		global $CMS;
		
 		$CMS->product->hide_all();
		 
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
	}


	public function search() {
		global $CMS, $DB, $member;
		$p_quick_search  = trim($CMS->input['p_quick_search']);
		$p_name_search  = $CMS->input['p_name_search'];
		$p_id_search  = $CMS->input['p_id_search'];
		$p_type_search = $CMS->input['p_type_search'];
		$p_statup_search = $CMS->input['p_statup_search'];
		$p_group_search = $CMS->input['p_group_search'];
		$p_manufacture_search = $CMS->input['p_manufacture_search'];
		$p_supplier_search = $CMS->input['p_supplier_search'];

		
		$str = '';
		if ( $p_quick_search != "" ) {
			if(Validate::isNum($p_quick_search))
			{
				$str.='&p_id='.$p_quick_search;	
			}
			else
			{
					$str.='&p_name='.$p_quick_search.'&p_code='.$p_quick_search;
			}
		}

		if ( $p_id_search != "" && Validate::isNum($p_id_search)) {
			$str.='&p_id='.$p_id_search;
		}
		if ( $p_type_search != "" && in_array($p_type_search, array(0,1))) {
			$str.='&p_type='.$p_type_search;
		}
		if (! $p_statup_search != "" && in_array($p_statup_search, array(0,1,2))) {
			$str.='&p_status='.$p_statup_search;
		}
		if ( $p_group_search != "" && $CMS->product_group->getInfo($p_group_search, 'product_group_id')) {
			$str.='&p_group='.$p_group_search;
		}
		if ( $p_manufacture_search != "" && $CMS->manufacture->getInfo($p_manufacture_search, 'manufacture_id')) {
			$str.='&p_manufacture='.$p_manufacture_search;
		}
		if ( $p_supplier_search != "" && $CMS->supplier->get_info($p_supplier_search, 'supplier_id')) {
			$str.='&p_supplier='.$p_supplier_search;
		}
		if ( $p_name_search != "" ) {
			$str.='&p_name='.$p_name_search;
		}


		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}{$str}");
	}


	public function ajax_add_manufacture() {
		global $CMS, $DB, $member;
 
		$m_id = $CMS->manufacture->add_ajax();
		if($m_id)
		{
			$option_manufacture = $CMS->manufacture->getOptionManufacture($m_id);
			print json_encode(array("status" => "success", "msg" => "{$CMS->lang['title_add_manufacture_success']}", "data_option" => $option_manufacture));exit;
		}else
		{
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['title_add_manufacture_error']}"));exit;
		}
	}

	public function search_g_product_ajax() {
		global $CMS, $DB, $member;
 		$p_type = intval($CMS->input['p_type']);
		$option = $CMS->product_group->getAll($p_type);
			 
		print json_encode(array("status" => "success", "msg" => "Có dữ liệu", "data_option" => $option));exit;
			 
	}

 
	public function autocomplete_quick_search() {
		global $CMS, $DB, $member;
 		$keyword  = trim($CMS->input['product_keyword']);
	 
		$keyword  = urldecode($keyword);
 
		 $sql = $DB->query("SELECT * FROM ".root_table."product  WHERE (product_id = '{$keyword}' OR product_name LIKE '%{$keyword}%' ) AND product_deleted = 0  " );
		 $count = $DB->num_rows($sql);
		 if($count > 0)
		 {
		 	if($count <= 3)
		 	{
		 		while ($data = $DB->fetch_array($sql)) {
		 			# code...
		 			$li .= "<li><a href='{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=show&id={$data['product_id']}'>{$data['product_name']}</a></li>";
		 		}
		 	}
		 	elseif($count > 3)
		 	{
		 		$i = 1;
		 		while ($data = $DB->fetch_array($sql)) {
		 			# code...
		 			if($i <= 3)
		 			{

		 				$li .= "<li><a href='{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=show&id={$data['product_id']}'>{$data['product_name']}</a></li>";
		 			}
		 			$i++;
		 		}
		 		$li .= "<li class='see_more'><span onclick='return autosubmit_frm_qs_product();' >Xem thêm ({$count}) kết quả</spa></li>";
		 	}
		 	print json_encode(array("status" => "success", "msg" => "Success", "data_option" => $li));exit;

		 }
		 else
		 {
		 	print json_encode(array("status" => "error", "msg" => "Data not found"));exit;
		 }

		 
	}

 
	public function ajax_add_product()
	{
		global $CMS;

		if(!lib\security::check_token())
        {
            print json_encode(array("status" => "error", "msg" => "{$CMS->lang['invalid_token']}"));exit;
        }
		
		if($CMS->permit['product_add'])
		{
			$return = $CMS->product->addAjax();
			// Converrt price 
			$return['product_price'] = $return['product_price_sell'];
	

			if(is_array($return))
			{
				print json_encode(array("status" => "success", "msg" => "{$CMS->lang['title_add_product_success']}", "data" => $return));exit;
			}else
			{
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['title_add_product_error']}"));exit;
			}
		}else
		{
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['msg_no_permision']}"));exit;
		}
		
	}

	public function ajax_edit_product()
	{
		global $CMS;

		$item_id = intval($CMS->input['item_id']);
		$type_product_edit = intval($CMS->input['type_product_edit']);
		$product_id = intval($CMS->input['product_id']);

		if($type_product_edit == 1)
		{
			$data = $CMS->product->getInfo($product_id);
		}
		else
		{
			$data = $_SESSION['list_product'][$item_id];
		}
		// Converrt price 
		$data['product_price'] = $data['product_price_sell'];
		$data['html_subitem'] = $this->html_subitem($data['product_subitem']);
		print json_encode($data, JSON_UNESCAPED_UNICODE);exit;	
		
	}

	public function html_subitem($data=array())
	{
		global $CMS;

		$output = "";
		$form_name = 'edit';
		if(is_array($data))
		{
			$i = 1;
			foreach ($data as $key => $value) 
			{
				$tax_fee = $value['product_tax'] ? $value['product_tax'] : "";
				$output .=<<<EOF
					<tr class="row-item" rowtr="">
						<td class="item-td" for="item-1" check="chtd"><span class="number">#{$i}</span></td>
						<td class="item-td" for="item-2" check="chtd">
							<div class='box-container'>
								<input type="text" for="item-2" check="chtd" name="sub_product_name[]" class="form-control find_product hidden_border" autocomplete="off" value="{$value['product_name']}"/>
								<input type="hidden" class="product_id" name="sub_product_id[]" value="{$value['product_id']}"/>
								<div class="box_result_find" style="position: relative; background: #ccc;z-index: 3;top: 0; border: none; margin: 0;"></div>
							</div>
							
						</td>

						<td class="item-td" for="item-3" check="chtd"><textarea for="item-3" name="sub_product_description[]" rows="1" check="chtd" class="form-control hidden_border">{$value['product_description']}</textarea></td>

						<td class="item-td" for="item-4" check="chtd"><input type="text" for="item-4" name="sub_product_quantity[]" value="{$value['product_quantity']}" check="chtd" class="form-control hidden_border" onkeyup="calculate_money_subitem('{$form_name}');" onkeypress="return check_enter_number(event);"/></td>

						<td class="item-td" for="item-5" check="chtd"><input type="text" for="item-5" name="sub_product_price[]" value="{$value['product_price']}" check="chtd" onkeyup="calculate_money_subitem('{$form_name}');" onkeypress="return check_enter_number(event);" class="form-control hidden_border"/></td>

						<td class="item-td" for="item-6" check="chtd">
							<select for="item-6" name="sub_product_tax[]" defaultvalue="{$tax_fee}" class="form-control hidden_border auto_select" check="chtd">
									<option value="0">0%</option>
									<option value="10">10%</option>
							</select>
						</td>

						<td class="trash" for="item-7"><span class="del_row_invoid"><i class="fa fa-trash" aria-hidden="true"></i></span></td>
					</tr>
EOF;
				$i++;
			}

			$output .=<<<EOF
				<tr class="row-item" rowtr="last-row">
					<td class="item-td" for="item-1" check="chtd"><span class="number">#{$i}</span></td>
					<td class="item-td" for="item-2" check="chtd">
						<div class='box-container'>
							<input type="text" for="item-2" check="chtd" name="sub_product_name[]" class="form-control find_product hidden_border" autocomplete="off"/>
							<input type="hidden" class="product_id" name="sub_product_id[]" value=""/>
							<div class="box_result_find" style="position: relative; background: #ccc;z-index: 3;top: 0; border: none; margin: 0;"></div>
						</div>
						
					</td>

					<td class="item-td" for="item-3" check="chtd"><textarea for="item-3" name="sub_product_description[]" rows="3" check="chtd" class="form-control hidden_border" style="resize: none;"></textarea></td>

					<td class="item-td" for="item-4" check="chtd"><input type="text" for="item-4" name="sub_product_quantity[]" value="1" check="chtd" class="form-control hidden_border" onkeyup="calculate_money_subitem('{$form_name}');" onkeypress="return check_enter_number(event);"/></td>

					<td class="item-td" for="item-5" check="chtd"><input type="text" for="item-5" name="sub_product_price[]" check="chtd" onkeyup="calculate_money_subitem('{$form_name}');" onkeypress="return check_enter_number(event);" class="form-control hidden_border"/></td>

					<td class="item-td" for="item-6" check="chtd">
							<select for="item-6" name="product_tax[]" class="form-control hidden_border tax_list" check="chtd">
									<option value="0">0%</option>
									<option value="10" selected="selected">10%</option>
							</select>
					</td>

					<td class="trash" for="item-7"><span class="del_row_invoid"><i class="fa fa-trash" aria-hidden="true"></i></span></td>
				</tr>
EOF;


		}else
		{
			$output .=<<<EOF
				<tr class="row-item" rowtr="last-row">
					<td class="item-td" for="item-1" check="chtd"><span class="number">#1</span></td>
					<td class="item-td" for="item-2" check="chtd">
						<div class='box-container'>
							<input type="text" for="item-2" check="chtd" name="sub_product_name[]" class="form-control find_product hidden_border" autocomplete="off"/>
							<input type="hidden" class="product_id" name="sub_product_id[]" value=""/>
							<div class="box_result_find" style="position: relative; background: #ccc;z-index: 3;top: 0; border: none; margin: 0;"></div>
						</div>
						
					</td>

					<td class="item-td" for="item-3" check="chtd"><textarea for="item-3" name="sub_product_description[]" rows="3" check="chtd" class="form-control hidden_border" style="resize: none;"></textarea></td>

					<td class="item-td" for="item-4" check="chtd"><input type="text" for="item-4" name="sub_product_quantity[]" value="1" check="chtd" class="form-control hidden_border" onkeyup="calculate_money_subitem('{$form_name}');" onkeypress="return check_enter_number(event);"/></td>


					<td class="item-td" for="item-5" check="chtd"><input type="text" for="item-5" name="sub_product_price[]" check="chtd" onkeyup="calculate_money_subitem('{$form_name}');" onkeypress="return check_enter_number(event);" class="form-control hidden_border"/></td>

					<td class="item-td" for="item-6" check="chtd">
							<select for="item-6" name="product_tax[]" class="form-control hidden_border tax_list" check="chtd">
									<option value="0">0%</option>
									<option value="10" selected="selected">10%</option>
							</select>
					</td>

					<td class="trash" for="item-7"><span class="del_row_invoid"><i class="fa fa-trash" aria-hidden="true"></i></span></td>
				</tr>
EOF;
		}

		return $output;
	}

	public function ajax_edit_product_do()
	{
		global $CMS;

		if(!lib\security::check_token())
        {
            print json_encode(array("status" => "error", "msg" => "{$CMS->lang['invalid_token']}"));exit;
        }

		if($CMS->permit['product_edit']) 
		{
			$return = $CMS->product->editAjax();
            // Converrt price 
			$return['product_price'] = $return['product_price_sell'];
			           
			if(is_array($return))
			{
				print json_encode(array("status" => "success", "msg" => "{$CMS->lang['title_edit_product_success']}", "data" => $return));exit;
			}else
			{
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['title_edit_product_error']}"));exit;
			}
		}else
		{
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['msg_no_permision']}"));exit;
		}
	}
 	

 	 public function order_add() {

		global $CMS, $DB, $member;
	 	unset( $_SESSION['list_product_order']);
		$p_id = $CMS->input['p_id'];

		$product = $CMS->product->getInfo($p_id);
		$new_array = array();
		if(is_array($product))
		{  
		  $_SESSION['list_product_order'][0] = $product;
         
          $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&act=add");
           
		}
		else
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=assets");
		}

	}


		public function order_add_multi() {

		global $CMS, $DB, $member;
	 	unset( $_SESSION['list_product_order']);
 
		for ( $i = 0; $i < intval( $CMS->input["data_cnt"] ); $i++ )
		{
			$id = intval( $CMS->input["id_{$i}"] );
 
			if ( $id )
			{
				 $product = $CMS->product->getInfo($id);
					if(is_array($product))
					{
					   $_SESSION['list_product_order'][$i]  = $product ;
        
					}
			}
		}
		 
		if(is_array( $_SESSION['list_product_order']))
		{ 	 
			 $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&act=add");
		}
		else
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
		}

	}

    /**
     * Export to excel
     */
    function export()
    {
        global $CMS;
        $CMS->class->language->load("report");

        $link = \models\report::export_product_list(intval($CMS->input['export_type']));
//        header("location: {$link}");

        ezy::load_model("download");
        \models\download::sendFile($link);
    }

    /**
     * Import forn excel
     */
    function import()
    {
        global $CMS;
        $CMS->product->importProductList();
        $CMS->global->redirect($CMS->vars['http_referer']);
    }

    public function get_barcode_info()
    {
        global $CMS;

        $id = intval($CMS->input['id']);

        $product = $CMS->product->get_info($id);

        if(!$product)
        {
            $return =  [
                'status' => 'fail',
                'msg' => $CMS->lang['data_not_found']
            ];
        }
        else
        {
            if(trim($product['product_barcode']) == '')
            {
                $return =  [
                    'status' => 'fail',
                    'msg' => $CMS->lang['barcode_incomplete']
                ];
            }
            else
            {
                $pgroup = $CMS->product_group->getInfo($product['product_group']);
                $return =  [
                    'status' => 'ok',
                    'data' => [
                        'name' => $product['product_name'],
                        'barcode' => $product['product_barcode'],
                        'skucode' => $product['product_sku'],
                        'price' => $product['product_price_sell'] ? $product['product_price_sell'] : $product['product_price'],
                        'group' => $pgroup['product_group_name'],
                    ]
                ];
            }
        }

        header('Content-Type: application/json');
        echo @json_encode($return); exit;
    }

    public function preview_barcode()
    {
        global $CMS, $member;

        require_once root_path."vendor/autoload.php";

        if($CMS->input['save_as'] == 'excel')
        {
            ezy::load_model("report");

            header("location: ".\models\report::exportBarcode());
        }
        else
        {
            ob_start();

            if($CMS->input['type'] == 1)
            {
                $html = $this->html->preview_barcode1();
            }
            else
            {
                $html = $this->html->preview_barcode();
            }


            $CMS->class->html2pdf->build();
            $CMS->class->html2pdf->pdf->SetDisplayMode('fullpage');
            $CMS->class->html2pdf->pdf->WriteHTML($html);
            $CMS->class->html2pdf->pdf->Output();
        }
        exit;
    }


    //===========================================================================
	//  ARRANGE
	//===========================================================================
	
	public function arrange()
	{
		global $CMS, $DB, $member;
 
		// Re-arrange
		$CMS->product->arrange();
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=product&pages={$CMS->input['page']}");	
		 
	}
	
	function getDistrict()
	{
		global $CMS;

		$city_id = intval($CMS->input['city_id']);
		$option_district = $CMS->country->getOptionDistrict($city_id);
		print $option_district;exit;
	}

	function getWards()
	{
		global $CMS;

		$district_id = intval($CMS->input['district_id']);
		$option_wards = $CMS->country->getOptionWards($district_id);
		print $option_wards;exit;
	}

	function unlink_img()
	{
		global $CMS;
		$check = $CMS->product->unlink_img($CMS->input['link_image'], $CMS->input['id']);
		print $check;exit;
	}

	function rm_parent_id()
	{
		global $CMS;

		$product_id = intval($CMS->input['product_id']);
		$parent_id = intval($CMS->input['parent_id']);
		// set parent_id = 0
		$CMS->product->resetParentId($product_id, $parent_id);
		print 1;exit;
	}

	function get_product_child()
	{
		global $CMS;

		$product_id = intval($CMS->input['product_id']);
		$type = intval($CMS->input['type']);

		if($type == 2)
		{
			$data = $CMS->product->getProductChild($product_id, $type);
			print json_encode($data, JSON_UNESCAPED_UNICODE);exit;
		}elseif($type==1)
		{
			$data = $CMS->product->getInfo($parent_id, "product_subitem");
			$data = $data ? $data : json_encode([]);
			print $data;exit;
		}
	}

    /**
     * Commission list
     * nkvp - 2017.12.21
     */
    public function commission()
    {
        global $CMS, $DB;

        if(!$CMS->vars['enabled_commission'])
        {
            $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
        }

        if(!empty($_POST))
        {
            $CMS->product->update_commission($_POST);
        }

        $data = $CMS->product->commission_listing(0);
        $out = $this->html->commission($data);
        $CMS->output.=$out;
    }

    function load_quick_data()
    {
        global $CMS, $tpl;

        $id = $CMS->input['id']*1;
        $data = $data = $CMS->product->getInfo($id);
        $data = $CMS->product->convertvalue($data);
        $CMS->product->dataForm($data);
        $return = [];
        
        foreach($tpl as $k => $v)
        {
            if(preg_match('/^p_/',$k))
            {
                if(is_array($v)) //Multi language
                {
                    foreach($v as $k1 => $v1)
                    {
                        $v1 = str_replace('&#39;',"'",$v1);
                        $v1 = html_entity_decode($v1);
                        $v1 = htmlspecialchars_decode($v1);
                        $return["{$k}[{$k1}]"] = $v1;
                    }
                }
                else
                {
                    $v = str_replace('&#39;',"'",$v);
                    $v = html_entity_decode($v);
                    $v = htmlspecialchars_decode($v);
                    $return[$k] = $v;
                }
            }
        }

        if($return){
            $return['product_image'] = $data['product_image_c'];
            $return['staff_id'] = $tpl->option_staff;
        }

        input::jsonEncode($return);
    }

    function autocomplete_data()
    {
        global $CMS;

        $data = $CMS->product->autoCompleteData();

        if($CMS->input['lang'])
        {
           input::jsonEncode($data[$CMS->input['lang']]);
        }
        else
        {
            input::jsonEncode($data);
        }
    }

    function gethtml_attr()
    {
    	global $CMS;

    	$attr_group = intval($CMS->input['attr_group']);
    	$type = intval($CMS->input['type']);
    	if($type)
    	{
    		$option = attribute::getDataAttribute($attr_group);
    	}else
    	{
    		$option = attribute::getHtmlAttributeByGroup($attr_group);
    	}

    	print $option;exit;
    }

    function search_product()
    {
    	global $CMS;

    	$key = urldecode($CMS->input['term']);
    	$data = $CMS->product->searchProduct($key);

    	print $data;exit;
    }

    function assign_to_store()
	{
		global $CMS;
		
 		$CMS->product->assign_to_store();
		 
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
	}

	function unassign_store()
	{
		global $CMS;
		
 		$CMS->product->unassign_store();
		 
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
	}

	function deleteShipFee()
	{
		global $CMS;
		
		// Inputs
		$ship_id = isset($CMS->input['ship_id']) ? $CMS->input['ship_id'] : 0;

		if( \models\shipping_services::delete($CMS->input['ship_id']) )
		{
			$status = 'success';
			$msg = $_SESSION['msg'];
		}
		else
		{
			$status = 'error';
			$msg = "{$CMS->lang['ship_delete_failure']} #{$ship_id}<br>";
		}

		if( empty($CMS->input['is_ajax']) )
		{
			$shipping_fee = \models\shipping_services::getInfo($ship_id);
			if( isset($shipping_fee['product_id']) )
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=show&id={$shipping_fee['product_id']}");
			}
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
		}
		else
		{
			unset($_SESSION['msg']);
			unset($_SESSION['error_msg']);
			input::jsonEncode(array('status'=>$status, 'msg'=>$msg));
		}
	}

	function loadShipFee()
	{
		global $CMS;

		// Inputs
		$ship_id = isset($CMS->input['ship_id']) ? $CMS->input['ship_id'] : 0;

		$shipping_fee = \models\shipping_services::getInfo($ship_id);
		if( $shipping_fee )
		{
			$shipping_fee = \models\shipping_services::convertValue($shipping_fee);
			input::jsonEncode(array('status'=>'success', 'msg'=>'', 'data'=>$shipping_fee));
		}
		else
		{
			input::jsonEncode(array('status'=>'error', 'msg'=>$CMS->lang["not_found_shipping_fee"], 'data'=>$shipping_fee));
		}
	}

	function listShipFee()
	{
		global $CMS, $tpl;

		// Inputs
		$product_id = isset($CMS->input['product_id']) ? $CMS->input['product_id'] : 0;
		$tpl->dataShipFee = \models\shipping_services::getDataShipFeeByProductId($product_id, false);
		$html_content = \core\ezy::render('show_shipping_fee_data', 'product');
		input::jsonEncode(array('status'=>'success', 'data'=>$html_content));
	}

	function editShipFee()
	{
		global $CMS;
		
		// Inputs
		$ship_id = isset($CMS->input['ship_id']) ? $CMS->input['ship_id'] : 0;
		$data_info = \models\shipping_services::getInfo($ship_id);
		$data_edit = $CMS->input;
		$data_edit['id'] = $ship_id;
		if( \models\shipping_services::edit($data_info, $data_edit) )
		{
			$status = 'success';
			$msg = $_SESSION['msg'];
		}
		else
		{
			$status = 'error';
			$msg = $_SESSION['error_msg'];
		}

		if( empty($CMS->input['is_ajax']) )
		{
			if( isset($shipping_fee['product_id']) )
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=show&id={$shipping_fee['product_id']}");
			}
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
		}
		else
		{
			unset($_SESSION['msg']);
			unset($_SESSION['error_msg']);
			input::jsonEncode(array('status'=>$status, 'msg'=>$msg));
		}
	}

	function addShipFee()
	{
		global $CMS;

		// Inputs
		$product_id = isset($CMS->input['product_id']) ? $CMS->input['product_id'] : 0;
		$data_add = $CMS->input;
		if( \models\shipping_services::addByProduct($data_add, $product_id) )
		{
			$status = 'success';
			$msg  = $_SESSION['msg'];
			$msg .= $_SESSION['error_msg'] ? "<div style='color: red;'>{$_SESSION['error_msg']}</div>" : '';
		}
		else
		{
			$status = 'error';
			$msg = "<div style='color: red;'>{$_SESSION['error_msg']}</div>";
		}
		
		if( empty($CMS->input['is_ajax']) )
		{
			if( $product_id )
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=show&id={$product_id}");
			}
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
		}
		else
		{
			unset($_SESSION['msg']);
			unset($_SESSION['error_msg']);
			input::jsonEncode(array('status'=>$status, 'msg'=>$msg));
		}
	}
}

?>