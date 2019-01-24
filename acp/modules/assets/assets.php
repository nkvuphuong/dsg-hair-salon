<?php
if (!defined('IN_ROOT')) exit();

use core\ezy;

$assets = new assets;
$assets->autorun();

class assets{
	public $html;
	public function autorun(){
		 
		global $CMS, $DB, $member;
        // 
		$CMS->class->language->load("assets");
		 $CMS->assets->auto_run();
		 $this->html = $CMS->class->template->load_template("skin_assets");

		 
		if(\lib\input::get('subact') == "update_location")
		{
			if(isset($CMS->input['city_id']))
			{
				print $CMS->global->get_list_district($CMS->input['city_id']);
			}
			exit;
		}
		if(\lib\input::get('subact') == "find_asset_inventory")
		{
			 $this->find_asset_inventory();
		}

		switch ($CMS->input['act']) {
			default:
				if(\lib\input::get('subact') == "order_add")//Tao don hàng
				{
						$this->order_add();
				}
				else if(\lib\input::get('subact') == "order_add_multi")//Chuyen kho
				{
						$this->order_add_multi();
				}
				else if(\lib\input::get('subact') == "transfer")//Chuyen kho
				{
						$this->transfer();
				}
				else if(\lib\input::get('subact') == "transfer_multi")//Chuyen kho
				{
						$this->transfer_multi();
				}
				else if(\lib\input::get('subact') == "export")//Chuyen kho
				{
						$this->export();
				}
				else if(\lib\input::get('subact') == "export_multi")//Chuyen kho
				{
						$this->transfer_multi();
				}
                elseif(\lib\input::get('subact') == "ajax_edit_asset")
                {
                    $this->ajax_edit_asset();
                }elseif(\lib\input::get('subact') == "ajax_edit_asset_do")
                {
                    $this->ajax_edit_asset_do();
                }
				else if(\lib\input::get('subact') == "get_list_user")
				{
					print $CMS->user->load_list_user(0,intval($CMS->input['group']));exit;
				}
				else if(\lib\input::get('subact') == "ajax_add_for_bill")
				{
					$this->ajax_add_for_bill();
				}
				else if(\lib\input::get('subact') == "autocomplete_quick_search")
                {
                    $this->autocomplete_quick_search();
                }
//                else if(\lib\input::get('subact') == "get_barcode_info")
//                {
//                    $this->get_barcode_info();
//                }
//                else if(\lib\input::get('subact') == "preview_barcode")
//                {
//                    $this->preview_barcode();
//                }
                else if(\lib\input::get('subact') == 'create_barcode_image')
                {
                    $this->createBarcodeImage();
                }
//                else if(\lib\input::get('subact') == 'print_to_excel')
//                {
//                    $this->printAssetToExcel();
//                }
                else if(\lib\input::get('subact') == 'ajax_checkstock_assets')
                {
                    $this->ajax_checkstock_assets();
                }
                else if(\lib\input::get('subact') == 'clear_cache')
                {
                    $CMS->class->cache->mdelete($CMS->assets->cache_prefix);
                    $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                }
				else
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
                                if($CMS->input['is_ajax']){$CMS->assets->loadlist();}
                                else{$CMS->assets->search();}
				break;
			case 'delete':
				$this->del();
				break;
			case 'add':
				$this->add();
			break;
			case 'add_do':
				$this->addDo();
			break;
                        case "move":
                            $this->move();
                        break;
            case 'get_barcode_info':
                $this->get_barcode_info();
                break;
            case 'preview_barcode':
                $this->preview_barcode();
                break;
            case 'print_to_excel':
                $this->printAssetToExcel();
                break;
			case 'move_subitem':
				$this->move_subitem();
			break;
		}
	}
	public function default_page() { 
		global $CMS, $DB, $member;
		 
		$CMS->core->page_title = "{$CMS->lang['assets_title']}";
    	$store_id = intval($CMS->input['store_id']);
        $p_id = intval($CMS->input['p_id']);
		$supplier_id = intval($CMS->input['supplier_id']);
        $shi_id = intval($CMS->input['shi_id']);
        $lang_title = "{$CMS->lang['assets_title']}";
		
	 
        // From module store
        if($store_id)
        {
            $sql_add = " AND store_id='{$store_id}'";
			$name=$CMS->store->get_info($store_id,"store_name");
            $lang_title = $CMS->lang['assets_search_store'].": ".$name;
            $CMS->core->page_title = $lang_title;
        }
        // From module product
        else if($p_id)
        {
            $sql_add = " AND product_id='{$p_id}'";
			$name=$CMS->product->get_info($p_id,"product_name");
			$lang_title = $CMS->lang['assets_search_product'].": ".$name;
			$CMS->core->page_title = $lang_title;
        }
        // From module shipment
        else if($shi_id)
        {
           $sql_add = " AND shi_id='{$shi_id}'"; 
		   $name=$CMS->shipment->get_info($shi_id,"shi_name");
		   $lang_title = $CMS->lang['assets_search_shipment'].": ".$name;
		   $CMS->core->page_title = $lang_title;
        }
		
        // From module supplier
        else if($supplier_id)
        {
           $sql_add = " AND supplier_id='{$supplier_id}'"; 
		   $name=$CMS->supplier->get_info($supplier_id,"supplier_name");
		   $lang_title = $CMS->lang['assets_search_supplier'].": ".$name;
		   $CMS->core->page_title = $lang_title;
        }
       
     

		$CMS->output.=$this->html->head_search($lang_title);
		$CMS->output.=$CMS->assets->listing();
		$CMS->output.=$this->html->foot();
	}
	public function show() {
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['ass_title']}";
		$CMS->output.=$this->html->show($CMS->assets->convertvalue($CMS->assets->get_info($CMS->input['id'])));
		$CMS->output.=$CMS->global->logs("assets_{$CMS->input['id']}");
	}
	public function del() {
		global $CMS, $DB, $member;
		$CMS->assets->delete();
		$CMS->global->redirect("{$CMS->vars['http_referer']}");
	}
	// }
	public function edit() {
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['ass_edit']}";
$CMS->class->language->load("product_group");
                $CMS->class->language->load("store_request");
		$CMS->class->language->load("supplier");		$CMS->output.=$this->html->edit($CMS->assets->editvalue($CMS->assets->get_info($CMS->input['id'])));
	}
	public function edit_do() { 
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['assets_edit']}";
		if ($data = $CMS->assets->edit($CMS->input['id'])) {
		    if(\lib\input::get('subact') == 'ajax')
            {
                $return = json_encode([
                    'status' => 'success',
                    'msg' => strip_tags($_SESSION['msg']),
                    'data' => $data
                ]);

                unset($_SESSION['msg']);

                echo $return;
                exit;
            }
            else
            {
                $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=assets");
            }
		}

        if(\lib\input::get('subact') == 'ajax')
        {
            $return = json_encode([
                'status' => 'error',
                'msg' => strip_tags($_SESSION['msg'])
            ]);

            unset($_SESSION['msg']);

            echo $return;
            exit;
        }
        else
        {
            $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=assets&act=edit&id={$CMS->input['id']}");
        }

	}
	public function add() {
		global $CMS, $DB, $member;
                
                $CMS->class->language->load("product_group");
                $CMS->class->language->load("store_request");
		$CMS->class->language->load("supplier");
                
		$CMS->core->page_title = "{$CMS->lang['assets_add']}";
		$CMS->output.=$this->html->add();
	}
	public function addDo() {

		global $CMS, $DB, $member;
		if ($data = $CMS->assets->add()) {

		    if(\lib\input::get('subact') == 'ajax')
            {
                $return = [
                    'status' => 'success',
                    'msg' => strip_tags($_SESSION['msg']),
                    'data' => $data,
                ];

                unset($_SESSION['msg']);

                echo @json_encode($return); exit;
            }

			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=assets");
		}

        if(\lib\input::get('subact') == 'ajax')
        {
            $return = [
                'status' => 'error',
                'msg' => strip_tags($_SESSION['msg']),
            ];

            unset($_SESSION['msg']);

            echo @json_encode($return); exit;
        }

		$CMS->core->page_title = "{$CMS->lang['assets_add']}";
		$CMS->output.=$this->html->add($CMS->input);
	}

	

    public function order_add_multi() {

        global $CMS, $DB, $member;
         unset( $_SESSION['list_assets_order']);
     
        for ( $i = 0; $i < intval( $CMS->input["data_cnt"] ); $i++ )
        {
            $id = intval( $CMS->input["id_{$i}"] );

  
            if ( $id )
            {
                 $asset = $CMS->assets->get_info($id);
                    if(is_array($asset))
                    {

                    	if (!in_array($asset['store_id'], $store) AND count($store) > 0) {
                     
						     $_SESSION['error_msg'] = "Vui lòng chọn tài sản cùng kho!";
						     $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=assets");
						}
                       $store[$i] = $asset['store_id'];
 

                       $_SESSION['list_assets_order'][$i]  = $asset ;
        
                    }
            }
        }
    
        if(is_array( $_SESSION['list_assets_order']))
        {      
             $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&act=add");
        }
        else
        {
            $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=assets");
        }

    }


    public function order_add() {

        global $CMS, $DB, $member;
         unset( $_SESSION['list_assets_order']);
        $asset_id = $CMS->input['asset_id'];

        $asset = $CMS->assets->get_info($asset_id);
        
        $new_array = array();
        if(is_array($asset))
        {  
          $_SESSION['list_assets_order'][0] = $asset;
         
          $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&act=add");
           
        }
        else
        {
            $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=assets");
        }

    }
    



 
	public function transfer_multi() {

		global $CMS, $DB, $member;

	 	if($_SESSION['list_product'])
	 	{
	 		unset($_SESSION['list_product']);
	 	}
	 	$store_id = 0;
		for ( $i = 0; $i < intval( $CMS->input["data_cnt"] ); $i++ )
		{
			$id = intval( $CMS->input["id_{$i}"] );

			if ( $id )
			{
				 $asset = $CMS->assets->get_info($id);
					if(is_array($asset))
					{
						if (!in_array($asset['store_id'], $store) AND count($store) > 0) {
                     
						     $_SESSION['error_msg'] = "Vui lòng chọn tài sản cùng kho!";
						     $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=assets");
						}
                        $store[$i] = $asset['store_id'];
                     	$store_id = $asset['store_id'];


					   $_SESSION['list_product'][$i]['ass_name'] = $asset['ass_name'];
			           $_SESSION['list_product'][$i]['ass_code'] = $asset['ass_code'];
			           $_SESSION['list_product'][$i]['ass_key'] = $asset['ass_key'];
			           $_SESSION['list_product'][0]['ass_tax'] = $asset['ass_tax'];
			           $_SESSION['list_product'][$i]['ass_price'] = $asset['ass_price'] ? $asset['ass_price'] : $asset['ass_purchase_price'];

			           $_SESSION['list_product'][$i]['ass_quantity'] = 1;
			           $_SESSION['list_product'][$i]['ass_subitem'] = $CMS->assets->get_list_item($asset['ass_id']);
			          
			           
					}
			}
		}
		 
		if(is_array( $_SESSION['list_product']))
		{
			 $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store_request&act=add&stage=request_ei&type_bill=export&subtype=2&store_id={$store_id}");
		}
		else
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=assets");
		}

	}


	public function transfer() {

		global $CMS, $DB, $member;
	 	unset( $_SESSION['list_product']);
		$asset_id = $CMS->input['asset_id'];

		$asset = $CMS->assets->get_info($asset_id);
		if(is_array($asset))
		{
		   $_SESSION['list_product'][0]['ass_name'] = $asset['ass_name'];
           $_SESSION['list_product'][0]['ass_code'] = $asset['ass_code'];
           $_SESSION['list_product'][0]['ass_key'] = $asset['ass_key'];
           $_SESSION['list_product'][0]['ass_tax'] = $asset['ass_tax'];
           $_SESSION['list_product'][0]['ass_price'] = $asset['ass_price'] ? $asset['ass_price'] : $asset['ass_purchase_price'];

           $_SESSION['list_product'][0]['ass_quantity'] = 1;
           $_SESSION['list_product'][0]['ass_subitem'] = $CMS->assets->get_list_item($asset['ass_id']);
           $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store_request&act=add&stage=request_ei&type_bill=export&subtype=2&store_id={$asset['store_id']}");
           
		}
		else
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=assets");
		}

	}

	public function export_multi() {

		global $CMS, $DB, $member;

	 	if($_SESSION['list_product'])
	 	{
	 		unset($_SESSION['list_product']);
	 	}
	 	$store_id = 0;
		for ( $i = 0; $i < intval( $CMS->input["data_cnt"] ); $i++ )
		{
			$id = intval( $CMS->input["id_{$i}"] );

			if ( $id )
			{
				 $asset = $CMS->assets->get_info($id);
					if(is_array($asset))
					{
						if (!in_array($asset['store_id'], $store) AND count($store) > 0) {
                     
						     $_SESSION['error_msg'] = "Vui lòng chọn tài sản cùng kho!";
						     $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=assets");
						}
                        $store[$i] = $asset['store_id'];
                        $store_id = $asset['store_id']; 
                        
					   $_SESSION['list_product'][$i]['ass_name'] = $asset['ass_name'];
			           $_SESSION['list_product'][$i]['ass_code'] = $asset['ass_code'];
			           $_SESSION['list_product'][$i]['ass_key'] = $asset['ass_key'];
			           $_SESSION['list_product'][0]['ass_tax'] = $asset['ass_tax'];
			           $_SESSION['list_product'][$i]['ass_price'] = $asset['ass_price'] ? $asset['ass_price'] : $asset['ass_purchase_price'];

			           $_SESSION['list_product'][$i]['ass_quantity'] = 1;
			           $_SESSION['list_product'][$i]['ass_subitem'] = $CMS->assets->get_list_item($asset['ass_id']);
			          
			           
					}
			}
		}
		 
		if(is_array( $_SESSION['list_product']))
		{
			 $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store_request&act=add&stage=request_ei&type_bill=export&subtype=3&store_id={$store_id}");
		}
		else
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=assets");
		}

	}


	public function export() {

		global $CMS, $DB, $member;
	 	unset( $_SESSION['list_product']);
		$asset_id = $CMS->input['asset_id'];

		$asset = $CMS->assets->get_info($asset_id);
		if(is_array($asset))
		{
		   $_SESSION['list_product'][0]['ass_name'] = $asset['ass_name'];
           $_SESSION['list_product'][0]['ass_code'] = $asset['ass_code'];
           $_SESSION['list_product'][0]['ass_key'] = $asset['ass_key'];
           $_SESSION['list_product'][0]['ass_tax'] = $asset['ass_tax'];

           $_SESSION['list_product'][0]['ass_price'] = $asset['ass_price'] ? $asset['ass_price'] : $asset['ass_purchase_price'];
           $_SESSION['list_product'][0]['ass_quantity'] = 1;
           $_SESSION['list_product'][0]['ass_subitem'] = $CMS->assets->get_list_item($asset['ass_id']);
          $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store_request&act=add&stage=request_ei&type_bill=export&subtype=3&store_id={$asset['store_id']}");
           
		}
		else
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=assets");
		}

	}
	
	public function move_subitem()
	{
		global $CMS;
		
		$CMS->assets->move_subitem();
	}
        
        public function move()
        {
            global $CMS;
            
            $CMS->assets->move();
            
            $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=assets");
        }

    public function ajax_edit_asset()
    {
        global $CMS;

        $data = $CMS->assets->get_info($CMS->input['key'],'',1);

        if($data)
        {
            $data['ass_warranty'] = $data['ass_warranty'] ? $CMS->class->date->date_format($data['ass_warranty']) : "";

            $shipment = $CMS->shipment->get_info($data['shi_id']);

            $data['shipment_info'] = $shipment;

            $sub_items = $CMS->assets->get_list_item($data['ass_id'], 1);
            $data['sub_items'] = $sub_items;
        }
        else
        {
            $data['status'] = 'error';
            $data['msg'] = $CMS->lang['no_data'];
        }

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


    public function find_asset_inventory()
    {
        global $CMS, $DB, $member;
        $key_search = $CMS->input['key_search'];
        $store_id = intval($CMS->input['store_id']);

        if($key_search != "")
        {
        	$sql_add = " (ass_name like '%{$key_search}%' OR ass_code like '%{$key_search}%') AND store_id='{$store_id}' AND  ";
        }
		else
		{
			$sql_add = "  store_id='{$store_id}' AND  ";
		}
	                       
		 
				
 
		$sql = $DB->query("SELECT * , count(ass_name) as quantity FROM `".root_table."assets`   WHERE  {$sql_add} `ass_deleted`=0 AND is_available = 1 AND parent_id=0 GROUP BY ass_key  ORDER BY ass_id DESC");
		

			$asset_arr = array();
			if($DB->num_rows($sql_query))
			{
				while ( $data = $DB->fetch_array( $sql_query ) )
				{
				 	$asset_arr[] = $data;
				}

				 $return = json_encode([
                    'status' => 'success',
                    'msg' => "search list asset",
                    'data' => $asset_arr
                ]);

               
                echo $return;
                exit;

			}
			else
			{
				 $return = json_encode([
                    'status' => 'error',
                    'msg' => "search no asset" 
                ]);

               
                echo $return;
                exit;
			}


    }

    public function ajax_add_for_bill()
	{
		global $CMS, $member;
		
		$ass_key = $CMS->input['ass_key'];
		$ass_name = $CMS->class->editor->input(urldecode($CMS->input['ass_name']), "text");
		$ass_code = $CMS->class->editor->input(urldecode($CMS->input['ass_code']), "text");
		$pgroup_id = intval($CMS->input['pgroup_id']);
		$ass_price = floatval($CMS->input['ass_price']);
		$ass_original_price = floatval($CMS->input['ass_original_price']);
		$ass_purchase_price = floatval($CMS->input['ass_purchase_price']);
		$ass_tax = floatval($CMS->input['ass_tax']);
		$shi_id = intval($CMS->input['shi_id']);
		$ass_warranty = $CMS->input['ass_warranty'];
		$ass_status = intval($CMS->input['ass_status']);
		$ass_quantity = intval($CMS->input['ass_quantity']);
		$supplier_id = intval($CMS->input['supplier_id']);
		$user_id = $member['user_id'];

		$arr_product_name = array_values($CMS->input['sub_name']);
		$arr_product_id = array_values($CMS->input['sub_id']);
		$arr_product_quantity = array_values($CMS->input['sub_quantity']);
		$arr_original_price = array_values($CMS->input['sub_original_price']);
		$arr_purchase_price = array_values($CMS->input['sub_purchase_price']);
		$arr_product_price = array_values($CMS->input['sub_price']);
		$arr_product_tax = array_values($CMS->input['sub_tax']);
		$arr_product_warranty = array_values($CMS->input['sub_warranty']);// Tạm thời lấy theo sản phẩm gốc

		$count = count($arr_product_name);
		$product_subitem = array();
		if(!$ass_name)
		{
			print json_encode(array("status" => "error", "msg" => $CMS->lang['error_empty_ass_name']));exit;
		}
		for($i=0; $i<$count; $i++)
		{
			if($arr_product_name[$i])
			{
				$product_subitem[$i]['ass_name'] = $CMS->class->editor->input($arr_product_name[$i], "text");
				$product_subitem[$i]['ass_id'] = intval($arr_product_id[$i]);
				$product_subitem[$i]['ass_quantity'] = intval($arr_product_quantity[$i]);
				$product_subitem[$i]['ass_purchase_price'] = floatval($arr_purchase_price[$i]);
				$product_subitem[$i]['ass_original_price'] = floatval($ass_original_price[$i]);
				$product_subitem[$i]['ass_price'] = floatval($arr_product_price[$i]);
				$product_subitem[$i]['ass_tax'] = floatval($arr_product_tax[$i]);
				$product_subitem[$i]['ass_warranty'] = intval($arr_product_warranty[$i]);
			}
			
		}
		$product_subitem = json_encode($product_subitem, JSON_UNESCAPED_UNICODE);

		$ass_time = time();

		// Create SESSION TEMP SAVE PRODUCT
		if(!$_SESSION['list_product'])
		{
			$_SESSION['list_product'] == array();
		}
		$index = intval($CMS->input['item_id']);

		$_SESSION['list_product'][$index]['ass_key'] = $ass_key;// Vi khong insert vao product nen ko co product_id
		$_SESSION['list_product'][$index]['ass_id'] = $ass_key;
		$_SESSION['list_product'][$index]['ass_name'] = $ass_name;
		$_SESSION['list_product'][$index]['ass_code'] = $ass_code;
		$_SESSION['list_product'][$index]['pgroup_id'] = $pgroup_id;
		$_SESSION['list_product'][$index]['ass_price'] = $ass_price;
		$_SESSION['list_product'][$index]['ass_original_price'] = $ass_original_price;
		$_SESSION['list_product'][$index]['ass_purchase_price'] = $ass_purchase_price;
		// $_SESSION['list_product'][$index]['ass_quantity'] = 1;
		// $_SESSION['list_product'][$index]['ass_amount'] = round($ass_price + ($ass_price*$ass_tax)/100);
		$_SESSION['list_product'][$index]['ass_tax'] = $ass_tax;
		$_SESSION['list_product'][$index]['ass_status'] = $ass_status;
		$_SESSION['list_product'][$index]['ass_subitem'] = $product_subitem;
		$_SESSION['list_product'][$index]['ass_time'] = $ass_time;
		$_SESSION['list_product'][$index]['supplier_id'] = $supplier_id;
		$_SESSION['list_product'][$index]['user_id'] = $user_id;
		$_SESSION['list_product'][$index]['ass_warranty'] = $ass_warranty;

		$arr = array("msg" => $CMS->lang['title_add_for_bill_success'], "status" => "success", "data" => $_SESSION['list_product'][$index]);
		print json_encode($arr);exit;
	}
	
	public function autocomplete_quick_search() {
		global $CMS, $DB, $member;
 		$keyword  = trim($CMS->input['ass_keyword']);
	 
		$keyword  = urldecode($keyword);
 
		 $sql = $DB->query("SELECT * FROM ".root_table."assets  WHERE (ass_id = '{$keyword}' OR ass_name LIKE '%{$keyword}%' ) AND ass_deleted = 0  " );
		 $count = $DB->num_rows($sql);
		 if($count > 0)
		 {
		 	if($count <= 3)
		 	{
		 		while ($data = $DB->fetch_array($sql)) {
		 			# code...
		 			$li .= "<li><a href='{$CMS->vars['root_domain']}/?site=assets&act=show&id={$data['ass_id']}'>{$data['ass_name']}</a></li>";
		 		}
		 	}
		 	elseif($count > 3)
		 	{
		 		$i = 1;
		 		while ($data = $DB->fetch_array($sql)) {
		 			# code...
		 			if($i <= 3)
		 			{

		 				$li .= "<li><a href='{$CMS->vars['root_domain']}/?site=assets&act=show&id={$data['ass_id']}'>{$data['ass_name']}</a></li>";
		 			}
		 			$i++;
		 		}
		 		$li .= "<li class='see_more'><span onclick='return autosubmit_frm_qs_product();' >Xem thêm ({$count}) results</spa></li>";
		 	}
		 	print json_encode(array("status" => "success", "msg" => "{$CMS->lang['search_no_result']}", "data_option" => $li));exit;

		 }
		 else
		 {
		 	print json_encode(array("status" => "error", "msg" => "{$CMS->lang['search_no_result']}"));exit;
		 }

		 
	}

	public function get_barcode_info()
    {
        global $CMS;

        $key = trim($CMS->input['key']);

        $ass = $CMS->assets->get_info($key);

        if(!$ass)
        {
            $return =  [
                'status' => 'fail',
                'msg' => $CMS->lang['data_not_found']
            ];
        }
        else
        {
            if(trim($ass['ass_code']) == '')
            {
                $return =  [
                    'status' => 'fail',
                    'msg' => $CMS->lang['barcode_incomplete']
                ];
            }
            else
            {
                $pgroup = $CMS->product_group->getInfo($ass['pgroup_id']);

                $return =  [
                    'status' => 'ok',
                    'data' => [
                        'name' => $ass['ass_name'],
                        'barcode' => $ass['ass_code'],
                        'price' => $ass['ass_price'] ? $ass['ass_price'] : $ass['ass_purchase_price'],
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

            $html = $this->html->preview_barcode();

            $CMS->class->html2pdf->build();
            $CMS->class->html2pdf->pdf->SetDisplayMode('fullpage');
            $CMS->class->html2pdf->pdf->WriteHTML($html);
            $CMS->class->html2pdf->pdf->Output();
        }
        exit;
    }

    public function createBarcodeImage()
    {
        global $CMS;
        header("Content-type: image/png");
        $barcode = lib\Barcode::generatorPNG($CMS->input['barcode']);
        exit;
    }

    public function printAssetToExcel()
    {
        global $CMS, $member;

        ezy::load_model("report");

        header("location: ".\models\report::exportAsset());
    }

    /**
     *  Get list asset - statictis group by store
     */
    public function ajax_checkstock_assets()
    {
    	global $CMS, $DB;
    	$product_id = $CMS->input['product_id'];
    	$data_stock = $CMS->assets->check_stockproduct_allstore($product_id);
    	if(count($data_stock) > 0)
    	{
    		$return = json_encode([
                'status' => 'success',
                'msg' => "Get data success",
                'data' => $data_stock
            ]);
    	}
    	else
    	{
    		$return = json_encode([
                'status' => 'error',
                'msg' => "{$CMS->lang['assets_empty_allstore']}",
                
            ]);
    	}
    	echo $return;exit;
    }
}