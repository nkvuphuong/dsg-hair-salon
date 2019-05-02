<?php
if (!defined('IN_ROOT')) exit();

use \core\ezy;
use \lib\input;

use models\product;
ezy::load_model("product");

$store_request = new store_request;
$store_request->autorun();
class store_request{
	public $html;
	public function autorun(){
		 
		global $CMS, $DB, $member;
		$CMS->class->language->load("store_request");
		$CMS->class->language->load("supplier");
		
		$this->html = $CMS->class->template->load_template("skin_store_request");
		
		switch ($CMS->input['act']) {
			default:
				if(input::get('subact') == "getDistrict")
				{
					$this->getDistrict();
				}elseif(input::get('subact') == "save_to_list_product")
				{
					$this->save_to_list_product();
				}elseif(input::get('subact') == "remove_product")
				{
					$this->remove_product();
				}elseif(input::get('subact') == "update_product_to_list")
				{
					$this->update_product_to_list();
				}elseif(input::get('subact') == "ajax_add_for_bill")
				{
					$this->ajax_add_for_bill();
				}elseif(input::get('subact') == "change_request_subtype")
				{
					$this->change_request_subtype();
				}elseif(input::get('subact') == "load_productgroup_ptype")
				{
					$this->load_productgroup_ptype();
				}else
				{
					$this->default_page();
				}
				break;
			case 'show':
				$this->show();
				break;
			case 'edit':
				$this->edit();
				break;
			case 'edit_do':
				$this->edit_do();
				break;
			case 'search':
                	if(input::get('subact') == "search_shipment")
                	{
                		$this->search_shipment();
                	}elseif(input::get('subact') == "search_user")
                	{
                		$this->search_user();
                	}elseif(input::get('subact') == "search_goods")
                	{
                		$this->search_goods();
                	}else
                	{
                		$this->search();
                	}
				break;
			case 'del':
				$this->del();
				break;
			case 'add':
				$this->add();
				break;
			case 'add_do':
				$this->addDo();
				break;
			case 'approve':
				$this->approve();
				break;
			case 'approve_do':
				$this->approve_do();
				break;
			case 'cancel':
				$this->cancel();
				break;
		}
	}
	public function default_page() { 
		global $CMS, $DB, $member;
		unset($_SESSION['list_product']);

		$CMS->core->page_title = "{$CMS->lang['store_request_head']}";
		$CMS->output.=$this->html->head();
		$CMS->output.=$CMS->store_request->listing();
		$CMS->output.=$this->html->foot();
	}
	public function show() {
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['prop_title']}";
		
		$data = $CMS->store_request->convertvalue($CMS->store_request->get_info($CMS->input['id']));
		// print_r($data['stage_show']);exit;

		$page_type = $CMS->input['stage'] ? $CMS->input['stage'] : "request";

		if($data['stage_show'] != $CMS->input['stage'])
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}?site=store_request&act=show&stage={$data['stage_show']}&id={$CMS->input['id']}");
		}


		$CMS->output.=$this->html->show($data);
		$CMS->output.=$CMS->global->logs("store_request_{$CMS->input['id']}");
		
		
	}
	public function del() {
		global $CMS, $DB, $member;
		$CMS->store_request->delete();
		$CMS->global->redirect("{$CMS->vars['http_referer']}");
	}

	public function edit() 
	{
		global $CMS, $DB, $member;

		$data = $CMS->store_request->convertvalue($CMS->store_request->get_info($CMS->input['id']));
		// print_r($data['stage_show']);exit;

		$page_type = $CMS->input['stage'] ? $CMS->input['stage'] : "request";
		$type_bill = $data['request_type'] == 0 ? "import": ($data['request_type'] == 1 ? "export" : "");
		if(!in_array($data['request_status'], array(10,20)))
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}?site=store_request&stage={$data['stage_show']}");
		}

		if($data['stage_show'] != $CMS->input['stage'] or $type_bill != $CMS->input['type_bill'])
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}?site=store_request&act=edit&type_bill={$type_bill}&stage={$data['stage_show']}&id={$CMS->input['id']}");
		}

		if($CMS->input['type_bill'] == "export")
		{
			$CMS->core->page_title = "{$CMS->lang['prop_edit']}";
			$CMS->output.=$this->html->editExport($data);
			
		}else
		{
			$CMS->core->page_title = "{$CMS->lang['prop_edit']}";
			// $data = $CMS->store_request->convertvalue($CMS->store_request->get_info($CMS->input['id']));
			$data['stage'] = $CMS->input['stage'];
			$data['type'] = $CMS->input['type'];
			$CMS->output.=$this->html->edit($data);
		}
	}
	public function edit_do() 
	{ 
		global $CMS, $DB, $member;

		$data = $CMS->store_request->get_info($CMS->input['id']);
		if(!in_array($data['request_status'], array(10,20)))
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}?site=store_request&stage={$data['stage_show']}");
		}

		$CMS->core->page_title = "{$CMS->lang['store_request_edit']}";
		if($CMS->input['type'])
		{
			$link = "&type={$CMS->input['type']}";
		}else
		{
			$link = "";
		}
		if ($CMS->store_request->edit($CMS->input['id'])) {
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store_request{$link}&stage={$CMS->input['stage']}");
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store_request&act=edit{$link}&stage={$CMS->input['stage']}&id={$CMS->input['id']}");
	}
	public function add() 
	{
		global $CMS, $DB, $member;

		if($CMS->input['type_bill'] == "export")
		{
			$CMS->core->page_title = "{$CMS->lang['store_request_add']}";
			$CMS->output.=$this->html->addExport();
		}else
		{
			$CMS->core->page_title = "{$CMS->lang['store_request_add']}";
			$CMS->output.=$this->html->add();
		}
		
	}
	public function addDo() {
		global $CMS, $DB, $member;

		if ($request_id = $CMS->store_request->add()) 
		{
			if($CMS->input['page_type'] == 0)
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store_request&stage={$CMS->input['stage']}");
			}elseif($CMS->input['page_type'] == 1)
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store_request&stage={$CMS->input['stage']}&act=show&id={$request_id}");
			}elseif($CMS->input['page_type'] == 2)
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store_request&stage={$CMS->input['stage']}&act=add");
			}
		}else
		{
			$CMS->core->page_title = "{$CMS->lang['store_request_add']}";
			$data = $CMS->store_request->convertdata($CMS->input);
			if($CMS->input['type_bill'] == "export")
			{
				$CMS->output.=$this->html->addExport($data);
			}else
			{
				$CMS->output.=$this->html->add($data);
			}
		}
		
	}


	public function search_shipment()
	{
		global $CMS;
// print_r($CMS->input);exit;
		$key_search = urldecode($CMS->input['term']);
		$data = $CMS->shipment->searchKey($key_search);
		header('Content-Type: application/json');
		print json_encode($data);exit;
		// if($data)
		// {
		// 	print json_encode(array('status' => 'success' , 'data' => $data));exit;
		// }else
		// {
		// 	print json_encode(array('status' => 'error' , 'msg' => "Thêm mới lô hàng"));exit;
		// }
	}

	public function search_user()
	{
		global $CMS;
		$key_search = urldecode($CMS->input['term']);
		$data = $CMS->user->searchKey($key_search);
		header('Content-Type: application/json');
		print json_encode($data);exit;
		// if($data)
		// {
		// 	print json_encode(array('status' => 'success' , 'data' => $data));exit;
		// }else
		// {
		// 	print json_encode(array('status' => 'error' , 'msg' => "Không có dữ liệu"));exit;
		// }
	}

	public function search_goods()
	{
		global $CMS;

		$key_search = $CMS->input['key_search'];
		$product_type = $CMS->input['product_type'];
		$product_id = $CMS->input['product_id'];
		$cus_id = intval($CMS->input['cus_id']);
		$user_id = intval($CMS->input['user_id']);
		$store_id = intval($CMS->input['store_id']);
		
		if($cus_id > 0) // Search by customer
		{
			// $data = $CMS->product->searchKey_byCus($product_id,$cus_id, $store_id);

			// call to acp/models
			$data = \models\product::searchKey_byCus($product_id,$cus_id, $store_id);

		}
		else
		{
			// $data = $CMS->product->searchKey($key_search, $product_type, $product_id, $store_id);
			
			// call to acp/models
			$data = \models\product::searchKey($key_search, $product_type, $product_id, $store_id);
		}
		
		if($data)
		{
			$commission = $CMS->user->getCommission($user_id);

			foreach($data as $key => $product)
            {
                if(isset($commission['id_'.$product['product_id']]))
                {
                    $product['product_commission_type'] = $commission['id_'.$product['product_id']]['product_commission_type'];
                    $product['product_commission_value'] = $commission['id_'.$product['product_id']]['product_commission_value'];
                }
                else
                {
                    $product['product_commission_type'] = 0;
                    $product['product_commission_value'] = 0;
                }

                $data[$key] = $product;
            }

			print json_encode(array('status' => 'success' , 'data' => $data));exit;
		}else
		{
			print json_encode(array('status' => 'error' , 'msg' => "Không có dữ liệu"));exit;
		}
		
	}

	

	public function getDistrict()
	{
		global $CMS;

		$city_id = intval($CMS->input['city_id']);
		$option_district = $CMS->country->getOptionDistrict($city_id);
		print $option_district;exit;

	}

	

	public function approve()
	{
		global $CMS, $member;

		$stage = $CMS->input['stage'] ? $CMS->input['stage'] : "request";

		if(!$CMS->permit['store_request_approve_request'] and $stage=="request")
		{
			$_SESSION['msg'] = $CMS->lang['error_no_permit_approved'];
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store_request&stage=request");
		}

		if(!$CMS->permit['store_request_approve_request_ei'] and $stage=="request_ei")
		{
			$_SESSION['msg'] = $CMS->lang['error_no_permit_approved'];
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store_request&stage=request_ei");
		}

		if($CMS->input['stage']!="request" AND $CMS->input['type_bill'] == "export")
		{
			$CMS->output.=$this->html->approveExport($CMS->store_request->convertvalue($CMS->store_request->get_info($CMS->input['id'])));
		}else
		{
			// request
			$CMS->core->page_title = "{$CMS->lang['prop_edit']}";
			$CMS->output.=$this->html->approve($CMS->store_request->convertvalue($CMS->store_request->get_info($CMS->input['id'])));
		}
	}

	public function approve_do() 
	{ 
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['store_request_edit']}";
		
		if ($CMS->store_request->edit($CMS->input['id'])) 
		{
			if($CMS->input['stage'] == "request")
			{
				$page_redirect = "request_ei";
			}elseif($CMS->input['stage'] == "request_ei")
			{
				$page_redirect = "request_eis";
			}
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store_request&stage={$page_redirect}");
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store_request&act=edit&stage={$CMS->input['stage']}&id={$CMS->input['id']}");
	}

	public function cancel()
	{
		global $CMS;

		$request_id = intval($CMS->input['id']);
		if($CMS->store_request->cancel($request_id))
		{
			$_SESSION['msg'] = $CMS->lang['msg_cancel_success']."#{$request_id}";
		}else
		{
			$_SESSION['msg'] = $CMS->lang['msg_cancel_error']."#{$request_id}";
		}

		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store_request");
	}	

	

	

	

	

	public function save_to_list_product()
	{
		global $CMS;

		$product_id = intval($CMS->input['product_id']);
		$data = $CMS->product->get_info($product_id);
		$sup_name = $CMS->supplier->get_info($data['sup_id'],"supplier_name");
		$data['sup_name'] = $sup_name;
		// Create SESSION TEMP SAVE PRODUCT
		if(!$_SESSION['list_product'])
		{
			$_SESSION['list_product'] == array();
		}

		if($data)
		{
			$index = intval($CMS->input['item_id']);
			$_SESSION['list_product'][$index]['product_id'] = $data['product_id'];
			$_SESSION['list_product'][$index]['product_name'] = $data['product_name'];
			$_SESSION['list_product'][$index]['product_description'] = $data['product_description'];
			$_SESSION['list_product'][$index]['product_code'] = $data['product_code'];
			$_SESSION['list_product'][$index]['product_sku'] = $data['product_sku'];
			$_SESSION['list_product'][$index]['product_group'] = $data['product_group'];
			$_SESSION['list_product'][$index]['product_price'] = $data['product_price'];
			$_SESSION['list_product'][$index]['product_price_sell'] = $data['product_price_sell'];
			$_SESSION['list_product'][$index]['product_quantity'] = 1;
			$_SESSION['list_product'][$index]['product_amount'] =  round($data['product_price_sell'] + ($data['product_price_sell']*$data['product_tax'])/100);
			$_SESSION['list_product'][$index]['product_tax'] = $data['product_tax'];
			$_SESSION['list_product'][$index]['inclusive_of_tax'] = $data['inclusive_of_tax'];
			$_SESSION['list_product'][$index]['product_type'] = $data['product_type'];
			$_SESSION['list_product'][$index]['product_cycle'] = $data['product_cycle'];
			$_SESSION['list_product'][$index]['product_status'] = $data['product_status'];
			$_SESSION['list_product'][$index]['product_subitem'] = json_decode($data['product_subitem'], true);
			$_SESSION['list_product'][$index]['product_image'] = $data['product_image'];
			$_SESSION['list_product'][$index]['product_time'] = $data['product_time'];
			$_SESSION['list_product'][$index]['sup_id'] = $data['sup_id'];
			$_SESSION['list_product'][$index]['sup_name'] = $data['sup_name'];
			$_SESSION['list_product'][$index]['user_id'] = $data['user_id'];
			$_SESSION['list_product'][$index]['product_manufacture'] = $data['product_manufacture'];
			print 1;exit;
		}else
		{
			print 0;exit;
		}
	}

	public function remove_product()
	{
		global $CMS;
		// Check xoa tat ca
		if($CMS->input['type'] == "all")
		{
			unset($_SESSION['list_product']);
			print 1;exit;
		}

		// Xoá từng product
		$item_id = intval($CMS->input['item_id']);
		$type = intval($CMS->input['type']);
		if (array_key_exists($item_id,$_SESSION['list_product']))
		{
			unset($_SESSION['list_product'][$item_id]);
			if($type)
			{
				$_SESSION['list_product'] = array_values($_SESSION['list_product']);
			}
			print 1;exit;
		}else
		{
			print 0;exit;
		}
	}

	public function update_product_to_list()
	{
		global $CMS;

		$item_id = intval($CMS->input['item_id']);
		$quantiy = intval($CMS->input['quantity']);
		$price = intval($CMS->input['price']);
		$price_sell = intval($CMS->input['price_sell']);
		$tax = intval($CMS->input['tax']);
		
		$_SESSION['list_product'][$item_id]['product_price'] = $price;
		$_SESSION['list_product'][$item_id]['product_price_sell'] = $price_sell;
		$_SESSION['list_product'][$item_id]['product_quantity'] = $quantiy;
		$_SESSION['list_product'][$item_id]['product_amount'] =  round(($quantiy * $price_sell) + ($quantiy * $price_sell * $tax)/100);
		$_SESSION['list_product'][$item_id]['product_tax'] = $tax;

		print 1;exit;
	}

	public function ajax_add_for_bill()
	{
		global $CMS, $member;
		
		$product_id = intval($CMS->input['product_id']); // Có thể có hoặc ko
		$product_name = $CMS->class->editor->input(urldecode($CMS->input['product_name']), "text");
		$product_description = $CMS->class->editor->input(urldecode($CMS->input['product_description']), "text");
		$product_code = $CMS->class->editor->input(urldecode($CMS->input['product_code']), "text");
		$product_group = intval($CMS->input['product_group']);
		$product_manufacture = intval($CMS->input['product_manufacture']);
		
		$product_price = floatval($CMS->input['product_price']);
		$product_price_sell = floatval($CMS->input['product_price_sell']);
		$product_tax = floatval($CMS->input['product_tax']);
		$inclusive_of_tax = -1;//intval($CMS->input['inclusive_of_tax']);
		$product_type = 0;//intval($CMS->input['product_type']);
		$product_cycle = 0;
		$product_status = 0;
		$sup_id = intval($CMS->input['sup_id']);
		$user_id = $member['user_id'];

		$arr_product_name = array_values($CMS->input['sub_product_name']);
		$arr_product_id = array_values($CMS->input['sub_product_id']);
		$arr_product_description = array_values($CMS->input['sub_product_description']);
		$arr_product_quantity = array_values($CMS->input['sub_product_quantity']);
		$arr_product_price = array_values($CMS->input['sub_product_price']);
		$arr_product_tax = array_values($CMS->input['sub_product_tax']);
		$arr_product_manufacture = $product_manufacture;// Tạm thời lấy theo sản phẩm gốc

		$count = count($arr_product_name);
		$product_subitem = array();
		if(!$product_name)
		{
			print json_encode(array("status" => "error", "msg" => $CMS->lang['error_empty_product_name']));exit;
		}
		for($i=0; $i<$count; $i++)
		{
			if($arr_product_name[$i])
			{
				$product_subitem[$i]['product_name'] = $CMS->class->editor->input($arr_product_name[$i], "text");
			 
				$product_subitem[$i]['product_id'] = intval($arr_product_id[$i]);
				$product_subitem[$i]['product_description'] = $CMS->class->editor->input($arr_product_description[$i],"text");
				$product_subitem[$i]['product_quantity'] = intval($arr_product_quantity[$i]);
				$product_subitem[$i]['product_price'] = floatval($arr_product_price[$i]);
				$product_subitem[$i]['product_tax'] = floatval($arr_product_tax[$i]);
				$product_subitem[$i]['product_manufacture'] = intval($arr_product_manufacture[$i]);
			}
			
		}
		$product_subitem = json_encode($product_subitem, JSON_UNESCAPED_UNICODE);

		// Check upload
		$file_tmp = isset($_FILES['upload_img']['tmp_name']) ? $_FILES['upload_img']['tmp_name'] : "";
		$file_name = isset($_FILES['upload_img']['name']) ? $_FILES['upload_img']['name'] : "";
		$file_type = isset($_FILES['upload_img']['type']) ? $_FILES['upload_img']['type'] : "";
		$file_size = isset($_FILES['upload_img']['size']) ? $_FILES['upload_img']['size'] : "";
		$file_error = isset($_FILES['upload_img']['error']) ? $_FILES['upload_img']['error'] : "";
		
		$file_ext = $CMS->class->attachment->get_ext( $file_name );

		// Check dung luong file upload
		$max = 10;
		$max_file_upload = 1024*1024*$max;
		if($file_size > $max_file_upload )
		{
			$arr_img = array("msg" => $CMS->lang['msg_maxfile_upload_img'].$max."MB" , "status" => "error");
			print json_encode($arr_img);exit;
		}
		

		
		$file_name = str_replace( " ", "_", $file_name );
		$file_location = strtolower(time()."_".$file_name);
		$product_image = "";
		if ( $file_name )
		{
			if ( $CMS->class->attachment->is_image($file_name, $file_ext) == false )
			{
				$arr_img = array("msg" => $CMS->lang['invalid_upload_file'] , "status" => "error");
				print json_encode($arr_img);exit;
				// return false;
			}

			$check = @copy($file_tmp, "{$CMS->vars['upload_dir']}/product/{$file_location}");
			if(!$check)
			{
				$arr_img = array("msg" => $CMS->lang['msg_error_upload_img'], "status" => "error");
				print json_encode($arr_img);exit;
			}
			
			$product_image = $file_location;
		}

		$product_time = time();

		// Create SESSION TEMP SAVE PRODUCT
		if(!$_SESSION['list_product'])
		{
			$_SESSION['list_product'] == array();
		}
		$index = intval($CMS->input['item_id']);

		$_SESSION['list_product'][$index]['product_id'] = $product_id;// Vi salonng insert vao product nen ko co product_id
		$_SESSION['list_product'][$index]['product_name'] = $product_name;
		$_SESSION['list_product'][$index]['product_description'] = $product_description;
		$_SESSION['list_product'][$index]['product_code'] = $product_code;
		$_SESSION['list_product'][$index]['product_group'] = $product_group;
		$_SESSION['list_product'][$index]['product_price'] = $product_price;
		$_SESSION['list_product'][$index]['product_price_sell'] = $product_price_sell;
		$_SESSION['list_product'][$index]['product_quantity'] = 1;
		$_SESSION['list_product'][$index]['product_amount'] = round($product_price + ($product_price*$product_tax)/100);
		$_SESSION['list_product'][$index]['product_tax'] = $product_tax;
		$_SESSION['list_product'][$index]['inclusive_of_tax'] = $inclusive_of_tax;
		$_SESSION['list_product'][$index]['product_type'] = $product_type;
		$_SESSION['list_product'][$index]['product_cycle'] = $product_cycle;
		$_SESSION['list_product'][$index]['product_status'] = $product_status;
		$_SESSION['list_product'][$index]['product_subitem'] = $product_subitem;
		$_SESSION['list_product'][$index]['product_image'] = $product_image;
		$_SESSION['list_product'][$index]['product_time'] = $product_time;
		$_SESSION['list_product'][$index]['sup_id'] = $sup_id;
		$_SESSION['list_product'][$index]['user_id'] = $user_id;
		$_SESSION['list_product'][$index]['product_manufacture'] = $product_manufacture;

		$arr_img = array("msg" => $CMS->lang['title_add_for_bill_success'], "status" => "success", "data" => $_SESSION['list_product'][$index]);
		print json_encode($arr_img);exit;
	}

	
	public function search()
	{
		global $CMS;

		if($CMS->input['p_id'])
		{
			$id = intval($CMS->input['p_id']);
			$CMS->store_request->sql_add .= " AND request_product LIKE '%,\"product_id\":{$id},%' ";
		}

		//Input
		$key = urldecode($CMS->input['quick_search']); // qsearch
		// print $key;exit;
		$request_code = urldecode($CMS->input['request_code']);
		$product_name = urldecode($CMS->input['product_name']);
		$inventory_id = intval($CMS->input['inventory_id']);
		$shi_name = urldecode($CMS->input['shi_name']);
		$shi_id =intval($CMS->input['shi_id']);
		$store_id = intval($CMS->input['store_id']);

		if($key)
		{
			$CMS->store_request->sql_add .= " AND (request_code LIKE '%{$key}%' OR request_product LIKE '%{$key}%' )";
		}elseif($request_code)
		{
			$CMS->store_request->sql_add .= " AND request_code LIKE '%{$request_code}%' ";
		}

		if($product_name)
		{
			$CMS->store_request->sql_add .= " AND request_product LIKE '%{$product_name}%' ";
		}

		if($inventory_id)
		{
			$CMS->store_request->sql_add .= " AND inventory_id = '{$inventory_id}' ";
		}

		if($shi_id)
		{
			$CMS->store_request->sql_add .= " AND shi_id = '{$shi_id}' ";
		}

		if($store_id)
		{
			$CMS->store_request->sql_add .= " AND store_id = '{$store_id}' ";
		}

		// print $CMS->store_request->sql_add;exit;

		$CMS->core->page_title = "{$CMS->lang['store_request_title']}";
		$CMS->output.=$this->html->head();
		$CMS->output.=$CMS->store_request->listing();
		$CMS->output.=$this->html->foot();
	}

	public function change_request_subtype()
	{
		global $CMS;

		$request_subtype = intval($CMS->input['request_subtype']);
		$sub_id = intval($CMS->input['sub_id']) ? intval($CMS->input['sub_id']) : 1;
		$invalid_store_id = intval($CMS->input['invalid_store_id']);

		$output = $CMS->store_request->change_request_subtype($request_subtype, $sub_id,0, $invalid_store_id);
		
		print $output;exit;

	}

	public function load_productgroup_ptype()
	{
		global $CMS;

		$p_type = intval($CMS->input['p_type']);
		$option_category = $CMS->product_group->getMultiOptionCategory_2(1,0,0,0,$p_type);
	 
		print json_encode(array('status' => 'success' , 'data' => $option_category));exit;

	}
}