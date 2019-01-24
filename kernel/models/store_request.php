<?php

use \lib\input;

if (!defined('IN_ROOT')) exit();

$CMS->store_request = new store_request1;
class store_request1{
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
	

	public function add($data=array(), $type_insert=0)
	{
		global $CMS, $DB, $member;
		
		// print "<pre>";
 
		if($type_insert)
		{
			// Dùng cho trường hợp insert trực tiếp từ bên kiểm kho
 
			// Input
			$store_id = intval($data['store_id']);
			$request_stage = intval($data['request_stage']); // Phiếu nhập
			$request_type = intval($data['request_type']); //Loại: 0: Nhập; 1: xuất
			$request_subtype = intval($data['request_subtype']); //Loại: 1: Nhập kiểm kho; 4: xuất kiểm kho
			$request_status = $data['request_status']; // Phiếu nhập đang chờ
			$request_time = time();
			$request_amount = $data['request_amount']; // Tổng tiền 
			$request_product = $data['request_product']; // json array product
			$user_id = $member['user_id'];
			$cus_id = intval($data['cus_id']);
			$inventory_id = intval($data['inventory_id']);
			$request_note = $data['request_note'];
			$count = $DB->query("INSERT INTO ".root_table."store_request (store_id, request_stage, request_type, request_status, request_time, request_time_update, request_amount, request_product, user_id, request_subtype, inventory_id, request_note, cus_id) VALUES ('{$store_id}', '{$request_stage}', '{$request_type}', '{$request_status}', '{$request_time}', '{$request_time}', '{$request_amount}', '{$request_product}', '{$user_id}', '{$request_subtype}', '{$inventory_id}', '{$request_note}', '{$cus_id}') ");

 
			
			  

			if($count)
			{
				$request_id = $DB->last_insert_id();

				$DB->query("UPDATE ".root_table."store_request SET request_code=concat('REQ',request_id) WHERE request_id = '{$request_id}'");
				$CMS->class->logs->key= "store_request_{$request_id}";
				$CMS->class->logs->insert("{$member['cus_username']} create <b>store_request #{$request_id}</b>");
				// Update tai san sau khi xuat kho
				if($request_type = 1 AND  $request_subtype == 1 AND $request_stage == 3 AND $request_status == 31)//Xuat kho
				{
					// xuat kho
					$CMS->assets->updateIsAvailable(json_decode($request_product,true), 1);
				}
				unset($_SESSION['list_product']);
				return $request_id;

			}else
			{
				return false;
			}

		} // End insert từ bên kiểm kho

		// Input
		$supplier_id = intval($CMS->input['supplier_id']);
		$store_id = intval($CMS->input['store_id']);
		$shi_id = intval($CMS->input['shi_id']);
		$stage = $CMS->input['stage'];
		$type_bill = $CMS->input['type_bill'];
		$user_id_assign = intval($CMS->input['user_id_assign']);
		$time = time();
		if(!$shi_id and $type_bill == "import")
		{
			// Insert shipment moi
			$shi_name = $CMS->class->editor->input('shi_name'); 
			if($shi_name)
			{
				$data = $CMS->shipment->get_info($shi_name);
				if($data)
				{
					$shi_id = $data['shi_id'];
				}else
				{
					$shipment['user_id'] = $member['user_id'];
					$shipment['shi_name'] = $shi_name;
					$shipment['shi_time'] = $time;
					$shi_id = $CMS->shipment->quickadd($shipment);
				}
			}else
			{
				// $_SESSION['msg_error'] = $CMS->lang['error_empty_shipment'];
				// return false;
			}
		}
		$user_id = intval($member['user_id']);
		$request_note = $CMS->class->editor->input("request_note");
		
		if($stage == "request")
		{
			$request_status = 10;
			$request_type = 0;
			$request_stage = 1;
		}elseif($stage == "request_ei")
		{
			$request_status = 20;
			$request_type = intval($CMS->input['request_type']);
			$request_stage = 2;
		}elseif($stage == "request_eis")
		{
			$request_status = 31;
			$request_type = intval($CMS->input['request_type']);
			$request_stage = 3;
		}



		if($type_bill == "import")// Dùng cho phiếu nhập
		{
			 
			// Check input time
			$input_time_create = $this->convertFormatDate($CMS->input['request_time_create'],1);
			$input_time_delivery = $this->convertFormatDate($CMS->input['request_time_delivery'],1);

			$request_time_create = strtotime($input_time_create);
			$request_time_delivery = strtotime($input_time_delivery);
			if($request_time_delivery < $request_time_create)
			{
				$_SESSION['error_msg'] = $CMS->lang['msg_error_date_delivery'];
				return false;
			}

			$request_subtype = intval($CMS->input['request_subtype']);
			$cus_id = 0;
			$arr_product_name = array_values($CMS->input['product_name']);
			$arr_product_id = array_values($CMS->input['product_id']);
			$arr_item_id = array_values($CMS->input['item_id']);
			$arr_product_description = array_values($CMS->input['product_description']);
			$arr_product_quantity = array_values($CMS->input['product_quantity']);
			$arr_product_price = array_values($CMS->input['product_price']);
			$arr_product_amount = array_values($CMS->input['product_amount']);
			$arr_product_tax = array_values($CMS->input['product_tax']);
			$arr_sup_id = array_values($CMS->input['sup_id']);

			$count = count($arr_product_name);
			$data_product = array();
			for($i=0; $i<$count; $i++)
			{
				if($arr_product_name[$i])
				{
					
					if (array_key_exists($arr_item_id[$i],$_SESSION['list_product']))
					{
						$index = $arr_item_id[$i];
						$data_product[$i] = $_SESSION['list_product'][$index];
						$data_product[$i]['product_description'] = $CMS->class->editor->input($arr_product_description[$i],"text");
					}else
					{
						$data_product[$i]['product_id'] = intval($arr_product_id[$i]);
						$info = $CMS->product->getInfo($arr_product_id[$i]);
						$data_product[$i]['product_name'] = $CMS->class->editor->input($arr_product_name[$i], "text");
						$data_product[$i]['product_description'] = $CMS->class->editor->input($arr_product_description[$i],"text");
						$data_product[$i]['product_quantity'] = intval($arr_product_quantity[$i]);
						$data_product[$i]['product_price'] = floatval($arr_product_price[$i]);
						$data_product[$i]['product_tax'] = floatval($arr_product_tax[$i]);
						$data_product[$i]['product_amount'] = ($arr_product_quantity[$i]*$arr_product_price[$i]) + round((floatval($arr_product_tax[$i])*floatval($arr_product_quantity[$i]*$arr_product_price[$i]))/100);
						$data_product[$i]['product_code'] = $info['product_code'];
						$data_product[$i]['product_subitem'] = json_decode($info['product_subitem'], true);
						$data_product[$i]['sup_id'] = intval($arr_sup_id[$i]);
						$data_product[$i]['sup_name'] = $CMS->supplier->get_info(intval($arr_sup_id[$i]), "supplier_name");

						// Insert product mới
						if(!$info)
						{
							$data_add = $data_product[$i];
							$data_add['sup_id'] = $supplier_id;
							$data_add['store_id'] = $store_id;
							$data_add['shi_id'] = $shi_id;
							$checkadd = $CMS->product->addQuick($data_add);
							if($checkadd)
							{
								// $_SESSION['error_msg'] = $CMS->lang['error_empty_user_id'];
							}
						}
					}

					
				}
			}


			// Tính tổng tiền
			$request_amount = 0;
			$subtotal = 0;
			foreach ($data_product as $key => $value) 
			{
				$subtotal = $value['product_price'] * $value['product_quantity'];
				$request_amount += $subtotal + round(($value['product_tax']*$subtotal)/100);
				// print $value['product_tax']."<br/>";
			}

		}elseif($type_bill == "export") /// Dùng cho phiếu xuất
		{

			$cus_id = intval($CMS->input['cus_id']);
			$arr_ass_name = array_values($CMS->input['ass_name']);
			$arr_ass_key = array_values($CMS->input['ass_id']);
			$arr_ass_code = array_values($CMS->input['ass_code']);
			$arr_item_id = array_values($CMS->input['item_id']);
			$arr_ass_quantity = array_values($CMS->input['ass_quantity']);
			$arr_ass_tax = array_values($CMS->input['ass_tax']);
			$arr_ass_price = array_values($CMS->input['ass_price']);

			$count = count($arr_ass_name);
			$data_product = array();
			for($i=0; $i<$count; $i++)
			{
				if($arr_ass_name[$i])
				{
					
					if (array_key_exists($arr_item_id[$i],$_SESSION['list_product']))
					{
						$index = $arr_item_id[$i];
						$data_product[$i] = $_SESSION['list_product'][$index];
						$data_product[$i]['ass_code'] = $arr_ass_code[$i];
					}else
					{
						$data_product[$i]['ass_key'] = intval($arr_ass_key[$i]);
						$info = $CMS->assets->get_info($arr_ass_key[$i]);
						
						$data_product[$i]['ass_name'] = $CMS->class->editor->input($arr_ass_name[$i], "text");
						$data_product[$i]['ass_quantity'] = intval($arr_ass_quantity[$i]);
						$data_product[$i]['ass_code'] = $arr_ass_code[$i];
						$data_product[$i]['ass_price'] = $arr_ass_price[$i];
						$data_product[$i]['ass_tax'] = $arr_ass_tax[$i];
						$data_product[$i]['ass_amount'] = (intval($arr_ass_quantity[$i]) * $arr_ass_price[$i]) + round(($arr_ass_price[$i] * $arr_ass_quantity[$i] * $arr_ass_tax[$i])/100);
						$data_product[$i]['ass_subitem'] = $CMS->assets->get_list_item($info['ass_id']);

					}

					
				}
			}

			// Tính tổng tiền
			$request_amount = 0;
			$subtotal = 0;
			foreach ($data_product as $key => $value) 
			{
				$subtotal = $value['ass_price'] * $value['ass_quantity'];
				$request_amount += $subtotal + round(($value['ass_tax']*$subtotal)/100);
				// print $value['product_tax']."<br/>";
			}

			// request_subtype
			$request_subtype = intval($CMS->input['request_subtype']);
			$request_reason = "";
			$supplier_id = 0;
			$cus_id = 0;
			$store_id_to = 0;
			if($request_subtype == 1)
			{
				//xuất trả nhà cung cấp / Khách hàng
				$sub_id = intval($CMS->input['sub_id']);
				if($sub_id == 1)
				{
					$supplier_id = intval($CMS->input['supplier_id']);
				}elseif($sub_id == 2)
				{
					$cus_id = intval($CMS->input['cus_id']);
				}

			}elseif($request_subtype == 2)
			{
				// Xuất chuyển kho
				$store_id_to = intval($CMS->input['store_id_to']);
			}elseif($request_subtype == 3 or $request_subtype == 4)
			{
				// xuất vì lý do khác
				$request_reason = $CMS->class->editor->input("request_reason");
			}
		}

		// CHECK TON KHO CUA CAC SAN PHAM CHUAN BI XUAT
		if(!$this->check_inventory($data_product) and $type_bill == "export" and ($stage == "request_ei" or $stage == "request_eis"))
		{
			// $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store_request&act=add&stage={$stage}&type_bill={$type_bill}&id={$request_id}");
			return false;
		}

		if(!$user_id)
		{
			$_SESSION['error_msg'] = $CMS->lang['error_empty_user_id'];
			return false;
		}

		$request_product = json_encode($data_product, JSON_UNESCAPED_UNICODE);
		// Insert data
		$count = $DB->query("INSERT INTO ".root_table."store_request (supplier_id, store_id, shi_id, user_id, request_note, request_product, request_type, request_stage, request_status, request_time, request_time_update, request_amount, cus_id, request_reason, store_id_to, request_subtype, user_id_assign, request_time_create, request_time_delivery) VALUES ('{$supplier_id}', '{$store_id}', '{$shi_id}', '{$user_id}', '{$request_note}', '{$request_product}', '{$request_type}', '{$request_stage}', '{$request_status}', '{$time}', '{$time}', '{$request_amount}', '{$cus_id}',  '{$request_reason}', '{$store_id_to}', '{$request_subtype}', '{$user_id_assign}', '{$request_time_create}', '{$request_time_delivery}')");
		$request_id = $DB->last_insert_id();
		if($count)
		{
			$_SESSION['highlight'] = $request_id;
			$DB->query("UPDATE ".root_table."store_request SET request_code=concat('REQ',request_id) WHERE request_id = '{$request_id}'");
			$CMS->class->logs->key= "store_request_{$request_id}";
			$CMS->class->logs->insert("{$member['cus_username']} create <b>store_request #{$request_id}</b>");
			
			// $_SESSION['msg'] = "{$member['user_name']} tạo phiếu yêu cầu thành công";
			
			$code = "REQ".$request_id;
			if($request_type == 0 and $request_subtype == 1)
			{
				$_SESSION['msg'] .= "[{$member['user_display_name']}] {$CMS->lang['msg_insert_success_'.$request_stage.'_'.$request_subtype."_".$type_bill]} <b>{$code}</b> <br/>";
			}else
			{
				$_SESSION['msg'] .= "[{$member['user_display_name']}] {$CMS->lang['msg_insert_success_'.$request_stage."_".$type_bill]} <b>{$code}</b> <br/>";
				
			}
	// 		print_r ($request_product);exit;
 // echo $request_type."<br />";
 // echo $request_stage."<br />";
 // echo $request_subtype."<br />";
 // echo $store_id."<br />";
 // echo $store_id_to."<br />";
 // exit;
			// Xuât Kho - cap nhat tai san ton trong kho //
				if($request_type = 1 AND $request_stage = 3 )
				{
					if($request_subtype == "1")
						{
							$sub_id = intval($CMS->input['sub_id']);
							if($sub_id == 1)
							{
								$supplier_id = intval($CMS->input['supplier_id']);
								$invoice_no = $CMS->transactions->getNoInvoice(3);
								$data_cus = $CMS->supplier->get_info($supplier_id);
								$trx_billing_address = $data_cus['supplier_address'];
								$field = array('cus_type' => 2, 'supplier_id' => $supplier_id, 'cus_email' => "{$data_cus['supplier_email']}" );
							}elseif($sub_id == 2)
							{
								$cus_id = intval($CMS->input['cus_id']);
								$invoice_no = $CMS->transactions->getNoInvoice(3);
								$data_cus = $CMS->customer->getInfo($cus_id);
								$trx_billing_address = $data_cus['cus_address'];
								$field = array('cus_type' => 1, 'cus_id' => $cus_id, 'cus_email' => "{$data_cus['cus_email']}");
							}
							$trx_terms = 15;
							$data_trx = array(
									"type" => 1, // Sale
									"sub" => 3, //bill
									"request_id" => $request_id,
									"trx_invoice_no" => $invoice_no,
									"trx_address" => $trx_billing_address,
									"trx_terms" => $trx_terms,
									'trx_status' => 3, // Close
									"trx_expiration_date" => date("d/m/Y",$time+$trx_terms*86400),
									"trx_total" => $request_amount,
									"user_id" => $user_id,
									"service_type" => 0, // 0: hàng hoá, 1: dich vu
									"trx_time" => $time,
									"trx_note" => "Tạo biên lai cho phiếu xuất {$data_new['request_code']}",

								);
							$data_trx = array_merge($data_trx, $field);
							$trx = $CMS->transactions->add($data_trx);
							$trx_id = $trx['trx_id'];

                            //Add items
                            $CMS->transactions->addItem($trx_id,[],1);

							// Update store_request transaction id
							$DB->query("UPDATE ".root_table."store_request SET trx_id = '{$trx_id}' WHERE request_id = '{$request_id}'");
							// Cập nhật tài sản thành đã bán
							$CMS->assets->updateIsAvailable($data_product, 1);
				}
				elseif( $request_subtype == 2)
				{   
					// Cập nhật chuyển kho
					$CMS->assets->updateIsAvailable($data_product, 2, $store_id, $store_id_to);
				}elseif( $request_subtype == 3 or $request_subtype == 4) 
				{
					// Cập nhật tài sản thành đã bán
					$CMS->assets->updateIsAvailable($data_product, 1);
				}

 			}//End if check type export
			unset($_SESSION['list_product']);
			return $request_id;
		}else
		{
			
			if($request_type == 0 and $request_subtype == 1)
			{
				$_SESSION['msg'] .= "[{$member['user_display_name']}] {$CMS->lang['msg_insert_error_'.$request_stage.'_'.$request_subtype."_".$type_bill]}";
			}else
			{
				$_SESSION['error_msg'] = "[{$member['user_display_name']}] {$CMS->lang['msg_insert_error_'.$request_stage."_".$type_bill]}";
			}
			return false;
		}
	}
	
	public function check_exist( $field, $value = "", $except_value = "" )
	{
		global $CMS, $DB, $member;
		
		if ( ! $field )
		{
			return true;
		}
		
		if ( $except_value )
		{
			$DB->query("SELECT prop_id FROM ".root_table."store_request WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND prop_deleted=0");
		}
		else
		{
			$DB->query("SELECT prop_id FROM ".root_table."store_request WHERE {$field}='{$value}' AND prop_deleted=0");
		}
		
		if ( $DB->num_rows() == 0 )
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	public function listing() 
	{
		global $CMS,$DB,$member;
		
		
		if (!isset($this->html)) 
		{	
			$this->html = $CMS->class->template->load_template("skin_store_request");
		}
		
		$this->arrange_data = trim("store_id,shi_id,request_id,request_code,request_time,user_id,request_status");
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "request_time_update";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "DESC";

		$stage = isset($CMS->input['stage']) ? $CMS->input['stage'] : "request";
		if($stage == "request_ei")
		{
			$request_stage = 2;
		}elseif($stage == "request_eis")
		{
			$request_stage = 3;
		}else
		{
			$request_stage = 1;
		}
		$this->prefix_html .= "?site=store_request&stage={$stage}";
		if($CMS->input['act']=="search")
		{
			$this->prefix_html .= "&act=search";
		}

		$request_code = urldecode(input::get('request_code'));
		$product_name = urldecode(input::get('product_name'));
		$inventory_id = isset($CMS->input['inventory_id']) ? intval($CMS->input['inventory_id']) : "";
		$shi_name = urldecode(input::get('shi_name'));
		$shi_id = $shi_name ? intval(input::get('shi_id')) : "";
		$store_id = intval(input::get('store_id'));
		$keysearch = urldecode(input::get('quick_search'));

		if($keysearch)
		{
			$this->prefix_html .= "&quick_search=".urlencode($keysearch);
		}elseif($request_code)
		{
			$this->prefix_html .= "&request_code=".urlencode($request_code);
		}

		if($product_name)
		{
			$this->prefix_html .= "&product_name=".urlencode($product_name);
		}

		if($inventory_id)
		{
			$this->prefix_html .= "&inventory_id={$inventory_id}";
		}

		if($shi_id)
		{
			$this->prefix_html .= "&shi_name=".urlencode($shi_name)."&shi_id=".$shi_id;
		}

		if($store_id)
		{
			$this->prefix_html .= "&store_id=".$store_id;
		}


		$this->prefix_html .= "&page=";
		// print $this->prefix_html;exit;
		list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT * FROM `".root_table."store_request` WHERE `request_deleted`=0 AND request_stage = '{$request_stage}' {$this->sql_add} ORDER BY {$default_field} {$default_order}", 10, $this->prefix_html, $this->suffix_html);

        $output = "";

		if ($DB->num_rows($this->sql_query)>0) 
		{
			while ($data=$DB->fetch_array($this->sql_query)) 
			{
				$data = $this->convertvalue($data);
				$output .= $this->html->mid($data);
			}
		} 
		else 
		{
			$output .= $this->html->none();
		}
		
		return $output;
	}
	public function delete()
	{
		global $CMS, $DB;
		
		// Get info
		$data = $this->get_info();
		
		// Check existing
		if ( ! $data ) { return false; }
		
		// Update info
		$DB->query("UPDATE ".root_table."store_request SET request_deleted=1 WHERE request_id={$data['request_id']}");
		$stage = $CMS->input['stage'];
		$type_bill = $data['request_type'] == 0 ? "import" : "export";
		// Create log
		$CMS->class->logs->key = "store_request_{$data['store_request_id']}";
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['request_deleted_'.$type_bill.'_'.$stage]} <b>{$data['request_code']}</b>");
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store_request&stage={$stage}&page={$CMS->input['page']}");
		
		return true;
	}
	
	public function editvalue($data)
	{
		global $CMS;
		
		$data['shi_id'] = $CMS->shipment->get_info($data['shi_id'],"shi_name");
		$data['parent_id'] = $this->get_info($data['parent_id'],"prop_name");
		
		return $data;
	}
	/**
	 * Quick edit bill
	 * Update status, stage off bill store_rquest
	 */
	public function quick_edit_bill($store_request_id = "", $stage = "", $status = "")
	{
		global $CMS, $DB, $member;
		
	 
		$request_id = $store_request_id;
		$data = $this->get_info($request_id);
		$CMS->class->logs->key = "store_request_{$store_request_id}";
		$CMS->class->logs->old_data = $data;


		// Update
		$DB->query("UPDATE ".root_table."store_request SET request_stage = '{$stage}', request_status = '{$status}'  WHERE request_id = '{$request_id}'");
		
		$data_new =  $this->get_info($request_id);
		$CMS->class->logs->insert("store_request_{$request_id}");
		$CMS->class->logs->key = "store_request_{$request_id}";
		$CMS->class->logs->save_detail("store_request",$request_id, $data_new);
		return;

	}
		
	public function edit()
	{
		global $CMS, $DB, $member;
		
		// print "<pre>";
		// print_r($CMS->input);
		// print_r($_SESSION['list_product']);exit;
		$request_id = $CMS->input['id'];
		$data = $this->get_info($request_id);
		$CMS->class->logs->key = "store_request_{$CMS->input['id']}";
		$CMS->class->logs->old_data = $data;
		// Input
		$supplier_id = intval($CMS->input['supplier_id']);
		$store_id = intval($CMS->input['store_id']);
		$shi_id = intval($CMS->input['shi_id']);
		$cus_id = intval($CMS->input['cus_id']);
		$type_bill = $CMS->input['type_bill'];
		$time = time();
		if(!$shi_id and $type_bill == "import")
		{
			// Insert shipment moi
			$shi_name = $CMS->class->editor->input('shi_name'); 
			if($shi_name)
			{
				$shipment['user_id'] = $member['user_id'];
				$shipment['shi_name'] = $shi_name;
				$shipment['shi_time'] = $time;
				$shi_id = $CMS->shipment->quickadd($shipment);
			}else
			{
				// $_SESSION['msg'] = $CMS->lang['error_empty_shipment'];
				// return false;
			}
		}

		$user_id = intval($member['user_id']);
		$request_note = $CMS->class->editor->input("request_note");
		$stage = $CMS->input['stage'];
		// request_subtype
		$request_subtype = intval($CMS->input['request_subtype']);
		

		if($type_bill == "import")// Dùng cho phiếu nhập
		{
			$cus_id = 0;
			$user_id_assign = intval($CMS->input['user_id_assign']);
			// Check input time
			$input_time_create = $this->convertFormatDate($CMS->input['request_time_create'],1);
			$input_time_delivery = $this->convertFormatDate($CMS->input['request_time_delivery'],1);

			$request_time_create = strtotime($input_time_create);
			$request_time_delivery = strtotime($input_time_delivery);

			$arr_product_name = array_values($CMS->input['product_name']);
			$arr_product_id = array_values($CMS->input['product_id']);
			$arr_item_id = array_values($CMS->input['item_id']);
			$arr_product_description = array_values($CMS->input['product_description']);
			$arr_product_quantity = array_values($CMS->input['product_quantity']);
			$arr_product_price = array_values($CMS->input['product_price']);
			$arr_product_amount = array_values($CMS->input['product_amount']);
			$arr_product_tax = array_values($CMS->input['product_tax']);
			$arr_sup_id = array_values($CMS->input['sup_id']);


			$count = count($arr_product_name);
			$data_product = array();
			for($i=0; $i<$count; $i++)
			{
				if($arr_product_name[$i])
				{
					
					if (array_key_exists($arr_item_id[$i],$_SESSION['list_product']))
					{
						$index = $arr_item_id[$i];
						if($_SESSION['list_product'][$index]['product_quantity'] > 0)
						{
							$data_product[$i] = $_SESSION['list_product'][$index];
							$data_product[$i]['product_description'] = $CMS->class->editor->input($arr_product_description[$i],"text");
						}
					}else
					{
						if($arr_product_quantity[$i] > 0)
						{
							$data_product[$i]['product_id'] = intval($arr_product_id[$i]);
							$info = $CMS->product->getInfo($arr_product_id[$i]);
							$data_product[$i]['product_name'] = $CMS->class->editor->input($arr_product_name[$i], "text");
							$data_product[$i]['product_description'] = $CMS->class->editor->input($arr_product_description[$i],"text");
							$data_product[$i]['product_quantity'] = intval($arr_product_quantity[$i]);
							$data_product[$i]['product_price'] = floatval($arr_product_price[$i]);
							$data_product[$i]['product_tax'] = floatval($arr_product_tax[$i]);

							$data_product[$i]['product_amount'] = ($arr_product_quantity[$i]*$arr_product_price[$i]) + round((floatval($arr_product_tax[$i])*floatval($arr_product_price[$i]*$arr_product_quantity[$i]))/100);
							$data_product[$i]['product_code'] = $info['product_code'];
							$data_product[$i]['product_subitem'] = json_decode($info['product_subitem'], true);
							$data_product[$i]['sup_id'] = intval($arr_sup_id[$i]);
							$data_product[$i]['sup_name'] = $CMS->supplier->get_info(intval($arr_sup_id[$i]), "supplier_name");
						}

						// Insert product mới
						if(!$info)
						{
							$data_add = $data_product[$i];
							$data_add['sup_id'] = $supplier_id;
							$data_add['store_id'] = $store_id;
							$data_add['shi_id'] = $shi_id;
							$checkadd = $CMS->product->addQuick($data_add);
							if($checkadd)
							{
								// $_SESSION['error_msg'] = $CMS->lang['error_empty_user_id'];
							}
						}
					}

					
				}
			}


			// Tính tổng tiền
			$request_amount = 0;
			$subtotal = 0;
			foreach ($data_product as $key => $value) 
			{
				$subtotal = $value['product_price'] * $value['product_quantity'];
				$request_amount += $subtotal + round(($value['product_tax']*$subtotal)/100);
				// print $value['product_tax']."<br/>";
			}
// print $request_amount;exit;
		}elseif($type_bill == "export") /// Dùng cho phiếu xuất
		{
			$user_id_assign = $data['user_id_assign'];
			$request_time_create = $data['input_time_create'];
			$request_time_delivery = $data['input_time_delivery'];

			$cus_id = intval($CMS->input['cus_id']);
			$arr_ass_name = array_values($CMS->input['ass_name']);
			$arr_ass_key = array_values($CMS->input['ass_key']);
			$arr_ass_code = array_values($CMS->input['ass_code']);
			$arr_item_id = array_values($CMS->input['item_id']);
			$arr_ass_quantity = array_values($CMS->input['ass_quantity']);
			$arr_ass_tax = array_values($CMS->input['ass_tax']);
			$arr_ass_price = array_values($CMS->input['ass_price']);
// print "<pre>"; print_r($CMS->input['ass_id']);exit;
			$count = count($arr_ass_name);
			$data_product = array();
			for($i=0; $i<$count; $i++)
			{
				if($arr_ass_name[$i])
				{
					
					if (array_key_exists($arr_item_id[$i],$_SESSION['list_product']))
					{
						$index = $arr_item_id[$i];
						if($_SESSION['list_product'][$index]['ass_quantity'] > 0)
						{
							$data_product[$i] = $_SESSION['list_product'][$index];
							$data_product[$i]['ass_code'] = $arr_ass_code[$i];
						}
					}else
					{
						if($arr_ass_quantity[$i] > 0)
						{
							$data_product[$i]['ass_key'] = $arr_ass_key[$i];
							$info = $CMS->assets->get_info($arr_ass_key[$i]);
							$data_product[$i]['ass_name'] = $CMS->class->editor->input($arr_ass_name[$i], "text");
							$data_product[$i]['ass_quantity'] = intval($arr_ass_quantity[$i]);
							$data_product[$i]['ass_code'] = $arr_ass_code[$i];
							$data_product[$i]['ass_price'] = $arr_ass_price[$i];
							$data_product[$i]['ass_tax'] = $arr_ass_tax[$i];
							$data_product[$i]['ass_amount'] = (intval($arr_ass_quantity[$i]) * $arr_ass_price[$i]) + round(($arr_ass_price[$i] * $arr_ass_quantity[$i] * $arr_ass_tax[$i])/100);
							$data_product[$i]['ass_subitem'] = $CMS->assets->get_list_item($info['ass_id']);
						}

					}

					
				}
			}

			// CHECK TON KHO CUA CAC SAN PHAM CHUAN BI XUAT
			if(!$this->check_inventory($data_product) and $CMS->input['act'] == "approve_do")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store_request&act=approve&stage={$stage}&type_bill={$type_bill}&id={$request_id}");
			}

			// Tính tổng tiền
			$request_amount = 0;
			$subtotal = 0;
			foreach ($data_product as $key => $value) 
			{
				$subtotal = $value['ass_price'] * $value['ass_quantity'];
				$request_amount += $subtotal + round(($value['ass_tax']*$subtotal)/100);
				// print $value['product_tax']."<br/>";
			}


			$request_reason = "";
			$supplier_id = 0;
			$cus_id = 0;
			$store_id_to = 0;
			if($request_subtype == 1)
			{
				//xuất trả nhà cung cấp / Khách hàng
				$sub_id = intval($CMS->input['sub_id']);
				if($sub_id == 1)
				{
					$supplier_id = intval($CMS->input['supplier_id']);
				}elseif($sub_id == 2)
				{
					$cus_id = intval($CMS->input['cus_id']);
				}

			}elseif($request_subtype == 2)
			{
				// Xuất chuyển kho
				$store_id_to = intval($CMS->input['store_id_to']);
			}elseif($request_subtype == 3 or $request_subtype == 4)
			{
				// xuất vì lý do khác
				$request_reason = $CMS->class->editor->input("request_reason");
			}

		}// End if import export


		// 	Check DUYỆT PHIẾU NHẬP
		if($CMS->input['act'] == "approve_do")
		{
			// Check quyền duyệt
			if($CMS->permit['store_request_approve_request'] or $CMS->permit['store_request_approve_request_ei'])
			{
				// Check loại duyệt
				if($stage == "request_ei")
				{
					// Duyệt phiếu nhập
					$request_stage = 3;
					$request_status = 31;

				}
				elseif($stage == "request")
				{
					// Duyệt phiếu yêu cầu
					$request_stage = 2;
					$request_status = 20;
				}else
				{
					// lấy lại thông tin phiếu cũ
					$request_stage = $data['request_stage'];
					$request_status = $data['request_status'];
				}
				
			}else
			{
				$_SESSION['msg'] = $CMS->lang['error_no_permit_approved'];
				return false;
			}
		}else
		{
			$request_stage = $data['request_stage'];
			$request_status = $data['request_status'];
		}


		// print_r($data_product);exit;
		if(!$user_id)
		{
			$_SESSION['msg'] = $CMS->lang['error_empty_user_id'];
			return false;
		}
		// print $CMS->input['act'];exit;
		// print "<pre>"; print_r($data_product);exit;
		$request_product_bk = $data_product;
		$data_product = array_values($data_product);
		$request_product = json_encode($data_product, JSON_UNESCAPED_UNICODE);

		// Insert data
		$count = $DB->query("UPDATE ".root_table."store_request SET supplier_id = '{$supplier_id}', store_id = '{$store_id}', shi_id = '{$shi_id}', user_id = '{$user_id}', request_note = '{$request_note}', request_product = '{$request_product}', request_stage = '{$request_stage}', request_status = '{$request_status}', request_time_update = '{$time}', request_amount = '{$request_amount}', cus_id = '{$cus_id}', request_subtype = '{$request_subtype}', request_reason = '{$request_reason}', store_id_to = '{$store_id_to}', user_id_assign = '{$user_id_assign}', request_time_create = '{$request_time_create}', request_time_delivery = '{$request_time_delivery}' WHERE request_id = '{$request_id}'");
		// $count = 1;
		if($count)
		{
			$_SESSION['highlight'] = $request_id;
			$data_new = $this->get_info($request_id);

			// DUYỆT PHIẾU XUẤT
			if($CMS->input['act'] == "approve_do" and $type_bill == "export")
			{
				// Check quyền duyệt
				if($CMS->permit['store_request_approve_request'] or $CMS->permit['store_request_approve_request_ei'])
				{
					// Check loại duyệt
					if($stage == "request_ei")
					{
						if($request_subtype == "1")
						{
							$sub_id = intval($CMS->input['sub_id']);
							if($sub_id == 1)
							{
								$supplier_id = intval($CMS->input['supplier_id']);
								$invoice_no = $CMS->transactions->getNoInvoice(3);
								$data_cus = $CMS->supplier->get_info($supplier_id);
								$trx_billing_address = $data_cus['supplier_address'];
								$field = array('cus_type' => 2, 'supplier_id' => $supplier_id, 'cus_email' => "{$data_cus['supplier_email']}" );
							}elseif($sub_id == 2)
							{
								$cus_id = intval($CMS->input['cus_id']);
								$invoice_no = $CMS->transactions->getNoInvoice(3);
								$data_cus = $CMS->customer->getInfo($cus_id);
								$trx_billing_address = $data_cus['cus_address'];
								$field = array('cus_type' => 1, 'cus_id' => $cus_id, 'cus_email' => "{$data_cus['cus_email']}");
							}
							$trx_terms = 15;
							$data_trx = array(
									"type" => 1, // Sale
									"sub" => 3, //bill
									"request_id" => $request_id,
									"trx_invoice_no" => $invoice_no,
									"trx_address" => $trx_billing_address,
									"trx_terms" => $trx_terms,
									'trx_status' => 3, // Close
									"trx_expiration_date" => date("d/m/Y",$time+$trx_terms*86400),
									"trx_total" => $request_amount,
									"user_id" => $user_id,
									"service_type" => 0, // 0: hàng hoá, 1: dich vu
									"trx_time" => $time,
									"trx_note" => "Tạo biên lai cho phiếu xuất {$data_new['request_code']}",

								);
							$data_trx = array_merge($data_trx, $field);
							$trx = $CMS->transactions->add($data_trx);
							$trx_id = $trx['trx_id'];

                            //Add items
                            $CMS->transactions->clearItem($trx_id);
                            $CMS->transactions->addItem($trx_id,[],1);

							// Update store_request transaction id
							$DB->query("UPDATE ".root_table."store_request SET trx_id = '{$trx_id}' WHERE request_id = '{$request_id}'");
							// Cập nhật tài sản thành đã bán
							$CMS->assets->updateIsAvailable($request_product_bk, 1);
							
						}elseif($request_subtype == 2)
						{
							// Cập nhật chuyển kho
							$CMS->assets->updateIsAvailable($request_product_bk, 2, $store_id, $store_id_to);
						}elseif($request_subtype == 3 or $request_subtype == 4)
						{
							// Cập nhật tài sản thành đã bán
							$CMS->assets->updateIsAvailable($request_product_bk, 1);
						}




					}
					
					
				}
			}


			// Duyệt xác nhận nhập đủ hàng hoặc thiếu hàng
			if($CMS->input['act'] == "approve_do" and $stage == 'request_ei' and $CMS->permit['store_request_approve_request_ei'] and $type_bill == "import")
			{
				$confirm_goods = intval($CMS->input['enough_goods']);
				$CMS->assets->addQuick($data_new);
				if(!$confirm_goods)
				{
					// Tạo phiếu nhập mới đối với xác nhận thiếu hàng
					$arr_1 = array_values(json_decode($data['request_product'], true));
					$arr_2 = array_values($request_product_bk);
					
					$amount = 0;
					$count = count($arr_1);
					for($x=0; $x<$count;$x++)
					{
						if(intval($arr_1[$x]['product_quantity']) > intval($arr_2[$x]['product_quantity']))
						{
							$arr_1[$x]['product_quantity'] = intval($arr_1[$x]['product_quantity']) - intval($arr_2[$x]['product_quantity']);
							$subamount = $arr_1[$x]['product_price'] * $arr_1[$x]['product_quantity'];
							$arr_1[$x]['product_amount'] = $subamount;
							$price_tax = round(($arr_1[$x]['product_tax'] * $subamount) /100);
							$amount += $subamount + $price_tax;
							
						}else
						{
							unset($arr_1[$x]);
						}
					}
					// print
// print "<pre>";
// print_r($arr_1);exit;
					$arr_1 = array_values($arr_1);
					$arr_1 = json_encode($arr_1, JSON_UNESCAPED_UNICODE);
					// Insert data
					$count_in = $DB->query("INSERT INTO ".root_table."store_request (supplier_id, store_id, shi_id, user_id, request_note, request_product, request_type, request_stage, request_status, request_time, request_time_update , request_amount, user_id_assign) VALUES ('{$supplier_id}', '{$store_id}', '{$shi_id}', '{$user_id}', '{$request_note}', '{$arr_1}', '0', '2', '20', '{$time}', '{$time}', '{$amount}', '{$user_id_assign}')");
					$subrequest_id = $DB->last_insert_id();
					if($count_in)
					{
						$DB->query("UPDATE ".root_table."store_request SET request_code=concat('REQ',request_id) WHERE request_id = '{$subrequest_id}'");
						$CMS->class->logs->key= "store_request_{$subrequest_id}";
						$CMS->class->logs->insert("{$member['cus_username']} create <b>store_request #{$subrequest_id}</b>");
						
						$code = "REQ".$subrequest_id;
						$_SESSION['msg'] .= "[{$member['user_display_name']}] {$CMS->lang['msg_insert_success_2_import']} <b>{$code}</b> <br/>";
						
					}

				}// End if nhap thieu hang

				// Tao phieu chi

				$invoice_no = $CMS->transactions->getNoInvoice(6);
				$data_user = $CMS->user->get_info($user_id_assign);
				$trx_terms = 15;
				$data_trx = array(
						"type" => 2, // Sale
						"sub" => 6, //bill
						'cus_type' => 3,
						"request_id" => $request_id,
						'cus_email' => "{$data_user['user_email']}",
						'user_assign' => $user_id_assign,
						'trx_status' => 3, // Close
						"trx_invoice_no" => $invoice_no,
						"trx_address" => $data_user['user_address'],
						"trx_terms" => $trx_terms,
						"trx_expiration_date" => date("d/m/Y",$time+$trx_terms*86400),
						"trx_total" => $request_amount,
						"user_id" => $user_id,
						"service_type" => 0, // 0: hàng hoá, 1: dich vu
						"trx_time" => $time,
						"trx_note" => "{$CMS->lang['msg_create_bill_for_import_bill']} {$data_new['request_code']}",

					);

				$trx = $CMS->transactions->add($data_trx);
				$trx_id = $trx['trx_id'];

                //Add items
                $CMS->transactions->addItem($trx_id,[],1);

				// Update store_request transaction id
				$DB->query("UPDATE ".root_table."store_request SET trx_id = '{$trx_id}' WHERE request_id = '{$request_id}'");
				// Check bên phiếu trả hàng xem có tồn tại không tin phiếu nhập này không
				// if($ret_id = $CMS->returns->checkInfoReturns($request_id))
				// {
				// 	// Update trạng thái và id phiếu chi qua phiếu trả hàng
				// 	$DB->query("UPDATE ".root_table."returns SET trx_id = '{$trx_id}', ret_status = '1' WHERE ret_id = '{$ret_id}' ");
				// }
				
			}


			$CMS->class->logs->insert("store_request_{$request_id}");
			$CMS->class->logs->key = "store_request_{$request_id}";
			$CMS->class->logs->save_detail("store_request",$request_id, $data_new);
			if($CMS->input['act'] == "approve_do")// Duyệt phiếu yêu cầu
			{
				if($data['request_subtype'] == 1)
				{
					if($data['cus_id'])
					{
						$sub_type = "_2";
					}else
					{
						$sub_type = "_1";
					}
				}else
				{
					$sub_type = "";
				}

				$_SESSION['msg'] .= "[{$member['user_display_name']}] {$CMS->lang['title_approved_'.$data['request_subtype'].$sub_type."_"."{$type_bill}_".$stage]} <b>{$data['request_code']}</b> thành công";
			}else
			{
				if($data['request_type'] == 0 and $data['request_subtype'] == 1)
				{

					$_SESSION['msg'] .= "[{$member['user_display_name']}] {$CMS->lang['title_edit_'.$data['request_subtype']."_"."{$type_bill}_".$stage]} <b>{$data['request_code']}</b> thành công";
				}else
				{
					$_SESSION['msg'] .= "[{$member['user_display_name']}] {$CMS->lang['title_edit_'."{$type_bill}_".$stage]} <b>{$data['request_code']}</b> thành công";
				}
			}
			
			return true;
		}else
		{
			if($CMS->input['act'] == "approve_do")// Duyệt phiếu yêu cầu
			{
				$_SESSION['msg'] .= "[{$member['user_display_name']}] {$CMS->lang['title_approved_'.$data['request_subtype']."_"."{$type_bill}_".$stage]} <b>{$data['request_code']}</b> thất bại";
			}else
			{
				if($data['request_type'] == 0 and $data['request_subtype'] == 1)
				{
					$_SESSION['msg'] .= "[{$member['user_display_name']}] {$CMS->lang['title_edit_'.$data['request_subtype']."_"."{$type_bill}_".$stage]} <b>{$data['request_code']}</b> thất bại";
				}else
				{
					$_SESSION['msg'] .= "[{$member['user_display_name']}] {$CMS->lang['title_edit_'."{$type_bill}_".$stage]} <b>{$data['request_code']}</b> thất bại";
				}
			}
			
			return false;
		}
	}
	
	public function auto_run() {
		global $CMS, $DB, $member;
		
		if (!isset($this->html)) {	
			$this->html = $CMS->class->template->load_template("skin_store_request");
		}
		
		if ($CMS->class->cache->check("user_{$member['user_id']}_store_request_{$CMS->vars['default_language']}")) {
			$CMS->vars['action_controller']=$CMS->class->cache->load("user_{$member['user_id']}_store_request_{$CMS->vars['default_language']}");
		} else {
			$data = "";
			if ($CMS->permit["store_request_search"]) {
				$data .=<<<EOF
<div class="tbl-cell tbl-cell-action-bordered" >
	<a href="{$CMS->vars['root_domain']}/?site=store_request&act=search" title="{$CMS->lang['title_search_store_request']}">
	<button type="button" class="action-btn"><i class="fa fa-search"></i></button>
  </a>
</div>
EOF;
				
				$this->control = 1;
			}
			if ($CMS->permit["pcategory_arrange"]) {
				$data .=<<<EOF
<div class="tbl-cell tbl-cell-action-bordered" act="arrange" id="glyphicon-sort" >
	<i class="fa fa-refresh" title="{$CMS->lang['title_arrange_store_request']}"></i>
</div>
EOF;
				$this->control = 1;
			}
			if ($CMS->permit["pcategory_delete"]) {
				$data .=<<<EOF
<div class="tbl-cell tbl-cell-action-bordered" act="delete_all" id="font-icon-trash">
	<i class="fa fa-trash-o" title="{$CMS->lang['title_delete_all_store_request']}"></i>
</div>
EOF;
				$this->control = 1;
			}
			if ($CMS->permit["pcategory_add"]) {
				$data .=<<<EOF
<div class="tbl-cell tbl-cell-action-bordered">
	<a href="{$CMS->vars['root_domain']}/?site=store_request&act=add" title="{$CMS->lang['title_add_pcategory']}">
		<button type="button" class="action-btn"><i class="fa fa-plus-circle"></i></button>
	</a>
</div>
EOF;
				$this->control = 1;
			}
			$data = $this->control == 1 ?  $data : "";
			$CMS->class->cache->save("user_{$member['user_id']}_store_request_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}
		
		$this->action_control = $CMS->vars['action_controller'];
	}
	public function search(){
		global $CMS, $DB, $member;
		$str='';
		if (trim($CMS->input['prop_name'])) {
			$str.='&prop_name='.trim($CMS->input['prop_name']);
		}
		if (trim($CMS->input['prop_code'])) {
			$str.='&prop_code='.trim($CMS->input['prop_code']);
		}
		if (trim($CMS->input['user_id'])) {
			$str.='&user_id='.trim($CMS->input['user_id']);
		}
		if ($CMS->input['store_id']) {
			$str.='&store_id='.($CMS->input['store_id']);
		}
		if (trim($CMS->input['shi_id'])) {
			$str.='&shi_id='.trim($CMS->input['shi_id']);
		}
		if (trim($CMS->input['parent_id'])) {
			$str.='&parent_id='.trim($CMS->input['parent_id']);
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store_request{$str}");
	}




	public function get_info( $record_id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "store_request" )
		{
			$record_id = intval($CMS->input['id']);
		}

		// Clear record
		$record_id = strip_tags($record_id);
		
		// Check record		
		if ( ! $record_id )
		{
			return false;
		}
		
		$sql = $DB->query("SELECT * FROM ".root_table."store_request WHERE request_id='{$record_id}' AND request_deleted = 0 ORDER BY request_id DESC LIMIT 1");

		if ( $DB->num_rows($sql) > 0 )
		{
			$data = $DB->fetch_array($sql);
		
			if ( $field_name )
			{
				if ( $data[$field_name] )
				{
					return $data[$field_name];
				}
				else
				{
					return false;
				}
			}
		
			return $data;
		}
		else
		{
			return false;
		}
	}

	public function convertvalue($data) 
	{
		global $CMS;
		
		$data['store_name_to'] = $CMS->store->get_info($data['store_id_to'],"store_name");
		$data['store_id_bk'] = $data['store_id'];
		$data['store_name'] = $data['store_id'] = $CMS->store->get_info($data['store_id_bk'],"store_name");
		if($CMS->permit['store_read'])
		{
			$data['store_id'] = $data['store_id_show'] = "<a href='{$CMS->vars['root_domain']}/?site=store&act=show&id={$data['store_id_bk']}' style='display: inline-block'>{$data['store_id']}</a>";
			$data['store_id_to_show'] = "<a href='{$CMS->vars['root_domain']}/?site=store&act=show&id={$data['store_id_to']}' style='display: inline-block'>{$data['store_name_to']}</a>";
		}
		if($CMS->permit['assets_read'])
		{
			$data['store_id_show'] .= " <a data-toggle='tooltip' data-placement='bottom' title='{$CMS->lang['tooltip_search_assets']}' href='{$CMS->vars['root_domain']}/?site=assets&store_id={$data['store_id_bk']}'><i class='fa fa-search q-search' aria-hidden='true'></i></a>";
		}
		$data['shi_id_bk'] = $data['shi_id'];
		$data['shi_name'] = $data['shi_id'] = $CMS->shipment->get_info($data['shi_id'],"shi_name");
		if($CMS->permit['shipment_read'] and $data['shi_id'])
		{
			$data['shi_id'] = $data['shi_id_show'] = "<a href='{$CMS->vars['root_domain']}/?site=shi&act=show&id={$data['shi_id_bk']}'>{$data['shi_id']}</a>";
		}
		if($CMS->permit['assets_read'] and $data['shi_id'])
		{
			$data['shi_id_show'] .= " <a data-toggle='tooltip' data-placement='bottom' title='{$CMS->lang['tooltip_search_assets']}' href='{$CMS->vars['root_domain']}/?site=assets&shi_id={$data['shi_id_bk']}'><i class='fa fa-search q-search' aria-hidden='true'></i></a>";
		}
		$data['user_name'] = $CMS->user->get_info($data['user_id'],"user_name");
		if($CMS->permit['user_read'])
		{
			$data['user_name'] = $data['user_name_show'] = "<a href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}'>{$data['user_name']}</a>";
		}
		if($CMS->permit['assets_read'])
		{
			$data['user_name_show'] .= " <a data-toggle='tooltip' data-placement='bottom' title='{$CMS->lang['tooltip_search_assets']}' href='{$CMS->vars['root_domain']}/?site=assets&user_id={$data['user_id']}'><i class='fa fa-search q-search' aria-hidden='true'></i></a>";
		}

		$data['request_time_bk'] = $data['request_time'];
		$data['request_time'] = $CMS->class->date->date_format($data['request_time'],0);
		$data['supplier_name'] = $CMS->supplier->get_info($data['supplier_id'],"supplier_name");

		$request_status = $data['request_status'];
		if($request_status == 10 or $request_status == 20 or $request_status == 30)
		{
			$color = "warning";
		}else if($request_status == 12 or $request_status == 22 or $request_status == 32)
		{
			$color = "default";
		}else
		{
			$color = "success";
		}
		$data['request_status_show'] = "<span class='label label-{$color}'>".$CMS->lang['request_status_'.$request_status]."</span>";

		$btn_approve = "";
		$btn_cancel = "";
		if($CMS->input['stage'] == "request_ei")
		{
			$stage = "&stage=request_ei";

			// Act duyệt
			if($CMS->permit['store_request_approve_request_ei'] == 1 and $data['request_status'] == 20 )
			{
				if($data['request_type'] == 1)
				{
					$type_bill = "&type_bill=export";
					if($data['request_subtype'] == 1 and $data['cus_id'])
					{
						$title_approve = $CMS->lang['request_approve_export_1_2'];
					}elseif($data['request_subtype'] == 1 and !$data['cus_id'])
					{
						$title_approve = $CMS->lang['request_approve_export_1_1'];
					}else
					{
						$title_approve = $CMS->lang['request_approve_export_'.$data['request_subtype']];
					}
					
				}else
				{
					$type_bill = '';
					$title_approve = $CMS->lang['request_approve_import_request_ei_'.$data['request_subtype']];
				}
				$btn_approve = "<a class=\"dropdown-item\" href='{$CMS->vars['root_domain']}/?site=store_request&act=approve{$stage}{$type_bill}&id={$data[request_id]}'>{$title_approve}</a>";
			}

			if($CMS->permit['store_request_cancel_request_ei'] == 1 and $data['request_status'] == 20 )
			{
				$btn_cancel = "<a class=\"dropdown-item\" onclick=\"confirmAction('{$CMS->lang['confirm_action']}','{$CMS->vars['root_domain']}/?site=store_request{$stage}&act=cancel&id={$data[request_id]}')\">{$CMS->lang['title_cancel']}</a>";
			}


		}elseif($CMS->input['stage'] == "request_eis")
		{
			$stage = "&stage=request_eis";
		}else
		{
			$stage = "&stage=request";

			// Act duyệt
			if($CMS->permit['store_request_approve_request'] == 1 and $data['request_status'] == 10 )
			{
				$btn_approve = "<a class=\"dropdown-item\" href='{$CMS->vars['root_domain']}/?site=store_request&act=approve{$stage}&id={$data[request_id]}'>{$CMS->lang['request_btn_approved_request']}</a>";
			}

			if($CMS->permit['store_request_cancel_request'] == 1 and $data['request_status'] == 10 )
			{
				$btn_cancel = "<a class=\"dropdown-item\" onclick=\"confirmAction('{$CMS->lang['confirm_action']}','{$CMS->vars['root_domain']}/?site=store_request{$stage}&act=cancel&id={$data[request_id]}')\">{$CMS->lang['title_cancel']}</a>";
			}
		}

		

		if($CMS->input['stage'] == "request_eis")
		{
			$data['html_action'] = "";
		}else
		{
			$data['html_action'] =<<<EOF
				<div class="btn-group">
					<button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						{$CMS->lang['gaction']}
					</button>
					<div class="dropdown-menu">
						{$btn_approve}
						{$btn_cancel}
					</div>
				</div>

EOF;
		}

		if($CMS->permit['store_request_add_'.$CMS->input['stage']])
		{
			$data['request_code_show'] = "<a href='{$CMS->vars['root_domain']}/?site=store_request{$stage}&act=show&id={$data['request_id']}'>{$data['request_code']}</a>";
		}else
		{
			$data['request_code_show'] = $data['request_code'];
		}

		// processbar
		$status_1 = array(10,11,12);
		$status_2 = array(20,21,22);
		$status_3 = array(30,32);
		$active_1 = $active_2 = $active_3 = $active_4 = "";
		if(in_array($data['request_status'], $status_1))
		{
			$active_1 = "active";
		}

		if(in_array($data['request_status'], $status_2))
		{
			$active_1 = "active";
			$active_2 = "active";
		}

		if(in_array($data['request_status'], $status_3))
		{
			$active_1 = "active";
			$active_2 = "active";
			$active_3 = "active";
		}
		if($data['request_status'] == 31)
		{
			$active_1 = "active";
			$active_2 = "active";
			$active_3 = "active";
			$active_4 = "active";
		}
		$processbar =<<<EOF
			<ul class="progressbar">
		        <li class="{$active_1}">{$CMS->lang['store_request_head']}</li>
		        <li class="{$active_2}">{$CMS->lang['store_request_ei']}</li>
		        <li class="{$active_3}">{$CMS->lang['store_request_eis']}</li>
		        <!--li class="{$active_4}">{$CMS->lang['store_request_done_store']}</li-->
		    </ul>
EOF;
		$data['processbar'] = $processbar;

		$data['request_amount_show'] = $CMS->class->input->currency($data['request_amount']);
		if($data['request_type'] == 0)
		{
			$data['icon_ei'] = "<span style='color: green; display: inline-block;' data-toggle='tooltip' data-placement='bottom' title='Nhập hàng'><i class='fa fa-arrow-left' aria-hidden='true'></i></span>";
		}else
		{
			
			if($data['request_subtype'] == 2)
			{
				$icon = "fa-refresh";
				$title = "Chuyển đến kho {$data['store_name_to']}";
			}else
			{
				$icon = "fa-arrow-right";
				$title = 'Xuất hàng';
			}
			$data['icon_ei'] = "<span style='color: red; display: inline-block;' data-toggle='tooltip' data-placement='bottom' title='{$title}'><i class='fa {$icon}' aria-hidden='true'></i></span>";
		}


		// html product
		$data['request_product_show'] = "";
		$request_product = json_decode($data['request_product'], true);
		$count_li = 1;
		$title_product = "";
		foreach ($request_product as $key => $value) 
		{
			$qty_pro_show = $value['product_name'] ? "({$value['product_quantity']})" : "";
			$qty_ass_show = $value['ass_name'] ? "({$value['ass_quantity']})" : "";
			
			$name_product_bk = $name_product = $value['product_name'] ? $value['product_name']." {$qty_pro_show}" : $value['ass_name']." {$qty_ass_show}";
			if($value['product_name'])
			{
				$name_product = "<a href='{$CMS->vars['root_domain']}/?site=product&act=show&id={$value['product_id']}'>{$name_product}</a>";
			}elseif($value['product_id'])
			{
				$name_product = "<a href='{$CMS->vars['root_domain']}/?site=assets&act=show&id={$value['product_id']}'>{$name_product}</a>";
			}

			
			if($count_li <= 2)
			{
				$data['request_product_show'].=<<<EOF
					<li>{$name_product}</li>
EOF;
				$title_product .= $name_product_bk.", ";
			}else
			{
				$title_product .= $name_product_bk.", ";
			}
			$count_li ++;

		}

		if($data['request_product_show'])
		{
			$title_product = rtrim($title_product,', ');
			$data['request_product_show'] = "<ul data-toggle='tooltip' data-placement='bottom' title='{$title_product}'>".$data['request_product_show']."</ul>";
		}

		if($data['request_stage'] == 1)
		{
			$data['stage_show'] = "request";
		}elseif($data['request_stage'] == 2)
		{
			$data['stage_show'] = "request_ei";
		}elseif($data['request_stage'] == 3)
		{
			$data['stage_show'] = "request_eis";
		}else
		{
			$data['stage_show'] = "";
		}
		
		return $data;
	}

	public function convertdata($data=array())
	{
		global $CMS, $DB;
		$data['store_id_bk'] = $data['store_id'] = $data['store_id'] ? $data['store_id'] : intval($CMS->input['store_id']);
		$data['subtype'] = intval($data['request_subtype']);
		$data['sub_id'] = intval($data['sub_id']);
		// print "<pre>";print_r($data);exit;
		return $data;
	}

	public function action ($id=null) {
		if (!is_null($id)) {
			global $CMS, $DB, $member;
			$DB->query("SELECT `store_request_act`, store_request_title FROM `".root_table."store_request` WHERE `cus_id`={$member['cus_id']} AND `store_request_id`={$id}");
			$data = $DB->fetch_array();
			if ($data['store_request_act']==2) {
				return FALSE;
			}
			$count = $DB->query("UPDATE `".root_table."store_request` SET `{$CMS->input['action']}`='{$CMS->input['value']}' WHERE `cus_id`='{$member['cus_id']}' AND `store_request_id` = {$id}");
			if($count)
			{
				if($CMS->input['action'] == "store_request_deleted")
				{
					$_SESSION['msg'] = $CMS->lang['emsg_deleted_success_store_request']. $data['store_request_title'];
				}else
				{
					$_SESSION['msg'] = $CMS->lang['emsg_update_success_store_request']. $data['store_request_title'];
				}
			}else
			{
				$_SESSION['msg'] = $CMS->lang['emsg_update_error_store_request']. $data['store_request_title'];
			}
			return true;
		}
		return false;
	}
	
	public function get_parent_list()
	{
		global $CMS, $DB;
		
		if($CMS->class->cache->check("parent_list"))
		{
			$data = $CMS->class->cache->load("parent_list");
			return $data;
		}
		
		$sql = $DB->query("SELECT * FROM ".root_table."store_request WHERE prop_deleted=0 AND parent_id=0");
		
		$output = "<option value=''>{$CMS->lang['select']}</option>";	
		
		while($data = $DB->fetch_array($sql))
		{
			$output .= "<option value='{$data['prop_id']}'>{$data['prop_name']}</option>";	
		}
		
		$CMS->class->cache->save("parent_list",$output);
		
		return $output;
	}

    public function loadlist()
    {
            global $CMS, $DB, $member;

            // Continue		
            $prop_input = urldecode(trim($CMS->class->filter->clean_value($CMS->input['prop_input'])));

            $sql_add = "";
			
            $output = "";

			if($CMS->input['search_name'] == "shipment")
			{
				$sql_add .= " (shi_name like '%{$prop_input}%') AND ";
				list($this->show_page, $sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."shipment WHERE {$sql_add} shi_deleted=0 ORDER BY shi_id DESC", $this->per_page,"","",1,1,"shipment_list");
				
				while ( $data = $DB->fetch_array( $sql_query ) )
				{
					$output.="{$data['shi_id']}|{$data['shi_name']}|{$data['user_id']}|{$data['shi_time']}||||";
				}
			}
			else
			{
            	$sql_add .= " (prop_name like '%{$prop_input}%' OR prop_code LIKE '%{$prop_input}%') AND ";
				list($this->show_page, $sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."store_request WHERE {$sql_add} prop_deleted=0 ORDER BY prop_id DESC", $this->per_page,"","",1,1,"prop_list");
				
				while ( $data = $DB->fetch_array( $sql_query ) )
				{
					$store_request = $this->convertvalue($data);
	
					$output.= "{$store_request['prop_id']}|{$store_request['prop_name']}|{$store_request['prop_code']}|{$store_request['shi_id']}|{$store_request['user_id']}||||";
				}
			}


            print $output;exit;
    }	

    public function approve($request_id=0)
    {
    	global $CMS, $DB, $member;

    	if($request_id)
    	{
    		$data = $this->get_info($request_id);
    		$CMS->class->logs->key = "store_request_{$CMS->input['id']}";
			$CMS->class->logs->old_data = $data;
			// Duyet phieu yeu cau
    		$count = $DB->query("UPDATE ".root_table."store_request SET request_stage = 2, request_status = 20 WHERE request_id = '{$request_id}'");
    		if($count)
    		{
	    		$data_new = $this->get_info($request_id);
				$CMS->class->logs->insert("store_request_{$request_id}");
				$CMS->class->logs->key = "store_request_{$request_id}";
				$CMS->class->logs->save_detail("store_request",$request_id, $data_new);
				return true;
    		}else
    		{
    			return false;
    		}
    	}
    }

    public function cancel($request_id=0)
    {
    	global $CMS, $DB, $member;

    	if($request_id)
    	{
    		$data = $this->get_info($request_id);
    		if($data['request_status'] != 10 and $data['request_status'] != 20 )
    		{
    			return false;
    		}

    		$CMS->class->logs->key = "store_request_{$CMS->input['id']}";
			$CMS->class->logs->old_data = $data;
			// Huỷ phieu yeu cau
    		$count = $DB->query("UPDATE ".root_table."store_request SET request_status = 12 WHERE request_id = '{$request_id}'");
    		if($count)
    		{
	    		$data_new = $this->get_info($request_id);
				$CMS->class->logs->insert("store_request_{$request_id}");
				$CMS->class->logs->key = "store_request_{$request_id}";
				$CMS->class->logs->save_detail("store_request",$request_id, $data_new);
				return true;
    		}else
    		{
    			return false;
    		}
    	}
    }

    public function getNumberbill()
    {
    	global $CMS, $DB;

    	$arr = array();
    	$sql = $DB->query("SELECT request_status, COUNT(request_id) as number_request FROM ".root_table."store_request WHERE request_status IN (10,20,30) AND request_deleted = 0 group BY request_status");
    	if($DB->num_rows($sql) > 0)
    	{
    		while ($data = $DB->fetch_array($sql)) 
    		{
    			$key = $data['request_status'];
    			$arr[$key] = $data['number_request'];
    		}

    		
    	}

    	return $arr;
    }

    public function change_request_subtype($request_subtype = 0, $sub_id = 0, $id_change = null, $invalid_store_id = null)
    {
    	global $CMS;

    	$output = "";
		if($request_subtype == 1)
		{
			if($sub_id == 1)
			{
				$checked_1 = " checked = 'checked' ";
				$checked_2 = "";
				if($CMS->permit['supplier_add'] == 1)
				{
				 	$btn_add_ncc =<<<EOF
						<a onclick="call_form_add_supplier(event);" class="btn_gen pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i></a>
EOF;
					
				}else
				{
					$btn_add_ncc = "";
				}

				$btn_edit_ncc = "";
				if($id_change)
				{
					$btn_edit_ncc = "<a id='{$id_change}' class='btn_edit_supplier btn_gen  pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
				}

				$output_sub =<<<EOF
				<fieldset class="form-group">
					<label class="form-label pull-left" for="supplier_id">{$CMS->lang['supplier_id_choose']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
					<div class="box_action pull-right">
				   		{$btn_add_ncc}
				   		<span class="box_edit_supplier pull-right" style="margin-left: 10px;">{$btn_edit_ncc}</span>
				   	</div>
				   	<div class="form-control-wrapper" style="clear:both">
						<div class="box_validate">
							<select name="supplier_id" id="supplier_id" onchange="change_supplier_action(this);" defaultvalue="{$id_change}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['request_err_supplier']}" class="form-control select_supplier auto_select select2" for="change">						
				                {$CMS->supplier->get_list_supplier(0, $id_change)}
				           </select>
				        </div>
				    </div>
		           
				</fieldset>    

EOF;
		
				
			}elseif($sub_id == 2)
			{
				$checked_1 = "";
				$checked_2 = " checked = 'checked' ";

				if($CMS->permit['customer_add'] == 1)
				{
				 	$btn_add_cus =<<<EOF
						<a class="btn_gen add_new_cus pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i></a>
EOF;
					
				}else
				{
					$btn_add_cus = "";
				}

				if($id_change)
				{
					$cus_name = $CMS->customer->getInfo($id_change, "cus_full_name");
					if($CMS->permit['customer_edit'] == 1)
					{
					 	$btn_edit_cus =<<<EOF
							<a id='{$id_change}' onclick='call_form_edit_customer(this);' class='btn_gen pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>
EOF;
						
					}else
					{
						$btn_edit_cus = "";
					}
				}

		$output_sub = <<<EOF
		
			<fieldset class="form-group">	
				<label class="form-label pull-left" for="supplier_id">{$CMS->lang['request_customer']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
				<div class="box_action pull-right">
			   		{$btn_add_cus}
			   		<span class="box_edit_cus pull-right" style="margin-left: 10px;">{$btn_edit_cus}</span>
			   	</div>
				<div class="form-control-wrapper" style="clear:both">
					<input autocomplete="off" name="cus_name" type="text" id="search_cus_id" onkeyup="autocompleteSearch('#search_cus_id', '{$CMS->vars['root_domain']}/?site=customer&subact=quicksearch', '#cus_id','customer')" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['request_err_customer']}" class="form-control" value="{$cus_name}" />
					<input type='hidden' name='cus_id' id='cus_id' value="{$id_change}" />	
				</div>
			</fieldset>

EOF;


			}

			

			$output =<<<EOF
			<input type="hidden" name="sub_id" value="{$sub_id}" />
			{$output_sub} 


EOF;


		}elseif($request_subtype == 2)
		{
			$option_store_id = $CMS->store->getListOptionStore($invalid_store_id);
			$output =<<<EOF
			<fieldset class="form-group">
				<label class="form-label pull-left" for="request_store_id">{$CMS->lang['request_store_id']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
				
				<div class="form-control-wrapper">
					<div class="box_validate">
			           <select name="store_id_to" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['request_err_store']}" defaultvalue="{$id_change}" class="form-control auto_select">
							<option value>{$CMS->lang['title_choose_plz']}</option>
							{$option_store_id}
						</select>
			        </div>
			    </div>
			</fieldset>    

EOF;

		}elseif($request_subtype == 3 or $request_subtype == 4)
		{
			$id_change = $id_change ? $id_change : "";
			$output =<<<EOF
			<fieldset class="form-group">
				<label class="form-label pull-left" for="request_reason">{$CMS->lang['request_reason']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
				<div class="form-control-wrapper" style="clear:both">
					<textarea name="request_reason" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['request_err_reason']}" rows="5">{$id_change}</textarea>
				</div>
	           
			</fieldset> 

EOF;
		}

		return $output;
    }

    public function convert_array($array=array(), $type=0)
    {
    	global $CMS, $DB;

    	$new_array = array();
    	if($type == 1)  // Tài sản
    	{
    		if(is_array($array))
    		{
    			$i = 0;
    			foreach ($array as $key => $value) 
    			{
    				$new_array['ass_name'][$i] = $value['ass_name'];
    				$new_array['ass_description'][$i] = $value['ass_description'];
    				$new_array['ass_quantity'][$i] = $value['ass_quantity'];
    				$new_array['ass_amount'][$i] = $value['ass_amount'];
    				$new_array['ass_tax'][$i] = $value['ass_tax'];
    				$new_array['ass_key'][$i] = $value['ass_key'];

    				$i++;
    			}
    		}
    	}

    	return $new_array;
    }

    public function check_inventory($data=array(), $type = 0)
    {
    	global $CMS, $DB;

    	if(is_array($data))
    	{
    		$count = 0;
    		foreach ($data as $key => $value) 
    		{
    			if($type == 0) // Check Assets
    			{
    				$info = $CMS->assets->getNumberAssets($value['ass_key']);
    				$number = intval($info['number_asset']);
	    			$store_name = $CMS->store->get_info($info['store_id'],"store_name");
	    			if($number < $value['ass_quantity'])
	    			{
	    				$_SESSION['error_msg'] .= "<b>{$value['ass_name']}</b> trong kho <b>{$store_name}</b> chỉ còn <b>{$number}</b> sản phẩm<br/>";
	    				$count ++;
	    			}
    			}
    			else
    			{
    				$number = $CMS->product->count_asset_bypid($value['product_id'], $value['store_id']);
    				$number = intval($number);
    				
	    			$store_name = $CMS->store->get_info($value['store_id'],"store_name");
	    			if($number < $value['product_quantity'])
	    			{
	    				$_SESSION['error_msg'] .= "<b>{$value['product_name']}</b> trong kho <b>{$store_name}</b> chỉ còn <b>{$number}</b> sản phẩm<br/>";
	    				$count ++;
	    			}
    			}
    		 
    			
    		}

    		if($count > 0)
    		{
    			return false;
    		}else
    		{
    			return true;
    		}
    	}else
    	{
    		return false;
    	}
    }


    public function getHtmlShowTypeBill($request_subtype=0, $sub_id = 0, $id_change="", $store_name_from = "", $label_width="title-label-125")
    {
    	global $CMS;

    	if($request_subtype == 1)
    	{
    		if($sub_id == 1)
    		{
    			$supplier_id = intval($id_change);
    			$sup_name = $CMS->supplier->get_info($supplier_id,"supplier_name");
    			$title_bill = $CMS->lang['request_subtype_supplier'];
    			if($CMS->permit['supplier_read'])
    			{
    				$sup_name = "<a href='{$CMS->vars['root_domain']}/?site=supplier&act=show&id={$supplier_id}'>{$sup_name}</a>";
    			}
    			$output_type =<<<EOF
    			<fieldset class="row">
					<div class="form-control-label2" >
			            <div class="title_label {$label_width} pull-left">{$CMS->lang['title_supplier_name']}</div>
			            <div class="form-control-span2">{$sup_name}</div>
			        </div>
				</fieldset>
				
EOF;

    		}elseif($sub_id == 2)
    		{
    			$cus_id = intval($id_change);
    			$cus_name = $CMS->customer->getInfo($cus_id,"cus_full_name");
    			$title_bill = $CMS->lang['request_subtype_customer'];
    			if($CMS->permit['customer_read'])
    			{
    				$cus_name = "<a href='{$CMS->vars['root_domain']}/?site=customer&act=show&id={$cus_id}'>{$cus_name}</a>";
    			}
    			$output_type =<<<EOF
    			<fieldset class="row">
					<div class="form-control-label2" >
			            <div class="title_label {$label_width} pull-left">{$CMS->lang['cus_full_name']}</div>
			            <div class="form-control-span2">{$cus_name}</div>
			        </div>
				</fieldset>
				
EOF;
    		}else
    		{
    			$title_bill = $CMS->lang['request_inventory_check_0'];
    		}
    	}elseif($request_subtype == 2)
    	{
    		$store_id = intval($id_change);
			$store_name = $CMS->store->get_info($store_id,"store_name");
			$title_bill = $CMS->lang['request_subtype_2'];
			$output_type =<<<EOF
			<fieldset class="row">
				<div class="form-control-label2" >
		            <div class="title_label {$label_width} pull-left">{$CMS->lang['request_subtype_2']}</div>
		            <div class="form-control-span2">{$store_name_from} <span style="color: red; display: inline-block;"><i class="fa fa-arrow-right" aria-hidden="true"></i></span> {$store_name}</div>
		        </div>
			</fieldset>
			
EOF;
    	}elseif($request_subtype == 3)
    	{
    		$title_bill = $CMS->lang['request_subtype_3'];
    		$output_type =<<<EOF
    		<fieldset class="row">
				<div class="form-control-label2" >
		            <div class="title_label {$label_width} pull-left">{$CMS->lang['request_subtype_3']}</div>
		            <div class="form-control-span2">{$id_change}</div>
		        </div>
			</fieldset>
			
EOF;
    	}elseif($request_subtype == 4)
		{
			$title_bill = $CMS->lang['request_inventory_check_1'];
		}
    	if($output_type)
    	{
    	$output .=<<<EOF
			{$output_type}
EOF;
		}
		return array($output, $title_bill);    	
    }

    public function convertFormatDate($input='', $type_input=1)
    {
    	global $CMS, $DB;
    	// Type: 1,2,3
    	// 1: d/m/Y
    	// 2: m/d/Y
    	// convert to Y/m/d
    	$input = str_replace("-", "/", $input);
	    $list = explode("/", $input);
    	if($type_input == 1)
    	{
	    	$new_input = $list[2]."/".$list[1]."/".$list[0];
    	}elseif($type_input == 2)
    	{
    		$new_input = $list[2]."/".$list[0]."/".$list[1];
    	}

    	return $new_input;
    }
}
?>