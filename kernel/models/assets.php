<?php
if (!defined('IN_ROOT')) exit();

$CMS->assets = new assets1;
class assets1{
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
	public $per_page = 20;
	public $prefix_html = "";
	public $suffix_html = "";
	public $html;
	public $cache_prefix = 'assets'; //Tiền tố cho cache key hoặc tên file cache


	public function add()
	{
		global $CMS, $DB, $member;

        if(!lib\security::check_token())
        {
            $_SESSION['msg'] = $CMS->lang['invalid_token'];
            return false;
        }

		// Input
		$ass_name = trim($CMS->input['ass_name']);
		$ass_code = trim($CMS->input['ass_code']);
		$store_id = intval($CMS->input['store_id']);
		$shi_id = trim($CMS->input['shi_id']);
		$supplier_id = intval($CMS->input['supplier_id']);
		$ass_purchase_price = intval($CMS->input['ass_purchase_price']);
		$ass_original_price = intval($CMS->input['ass_original_price']);
		$ass_price = intval($CMS->input['ass_price']);
		$ass_quantity = intval($CMS->input['ass_quantity']) ? intval($CMS->input['ass_quantity']) : 1;
		$ass_warranty = $CMS->input['ass_warranty'] ? $CMS->class->date->date2time($CMS->input['ass_warranty']) : 0;
		$ass_desc = $CMS->class->editor->input("ass_desc");
		$ass_status = intval($CMS->input['ass_status']);
		$pgroup_id = intval($CMS->input['pgroup_id']);
        $product_id = intval($CMS->input['product_id']);
		$ass_tax = intval($CMS->input['ass_tax']);
                
		$user_id = $member['user_id']; // Creator
		$ass_time = time();
		
		$ass_user_id = intval($CMS->input['ass_user_id']);
		$ass_userg_id = intval($CMS->input['ass_userg_id']);
		$ass_stocktaking_date = $CMS->input['ass_stocktaking_date'] ? $CMS->class->date->date2time($CMS->input['ass_stocktaking_date']) : 0;
                
                // Reset array key
                $CMS->input['sub_name'] = array_filter(array_values($CMS->input['sub_name']));
                $CMS->input['sub_code'] = array_filter(array_values($CMS->input['sub_code']));
                $CMS->input['sub_quantity'] = array_filter(array_values($CMS->input['sub_quantity']));
                $CMS->input['sub_purchase_price'] = array_filter(array_values($CMS->input['sub_purchase_price']));
                 $CMS->input['sub_original_price'] = array_filter(array_values($CMS->input['sub_original_price']));
                $CMS->input['sub_price'] = array_filter(array_values($CMS->input['sub_price']));
                $CMS->input['sub_warranty'] = array_filter(array_values($CMS->input['sub_warranty']));
                $CMS->input['sub_id'] = array_filter(array_values($CMS->input['sub_id']));
				$CMS->input['sub_tax'] = array_filter(array_values($CMS->input['sub_tax']));

		if(!$ass_name)
		{
			$_SESSION['error_msg'] = $CMS->lang['ass_empty_name'];
			return false;
		}
		
/*		if(!$ass_code)
		{
			$CMS->errormsg = $CMS->lang['ass_err_code'];
			return false;
		}
*/		
		if(!$store_id)
		{
			$_SESSION['error_msg'] = $CMS->lang['ass_err_store'];
			return false;
		}


		if(!$pgroup_id)
		{
			$_SESSION['error_msg'] = $CMS->lang['ass_err_cat'];
			return false;
		}

		if(!$ass_purchase_price)
		{
			$_SESSION['error_msg'] = $CMS->lang['ass_err_pprice'];
			return false;
		}
        
		// Check price
		if($CMS->class->input->check_price($ass_purchase_price)== false || ($CMS->class->input->check_price($ass_price)== false AND $ass_price))
		{
                    // Convert language
                    $array = array("assets_min_price" => $CMS->vars['assets_min_price'], "assets_max_price" => $CMS->vars['assets_max_price']);
                    $CMS->lang['ass_invalid_price'] = $CMS->class->language->replace( $array, $CMS->lang['ass_invalid_price'] );

                    $_SESSION['error_msg'] = $CMS->lang['ass_invalid_price'];
                    return false;
		}
		
		// Check price
		if($CMS->class->input->check_price($ass_quantity)== false)
		{
                    // Convert language
                    $array = array("assets_min_quantity" => $CMS->vars['assets_min_quantity'], "assets_max_quantity" => $CMS->vars['assets_max_quantity']);
                    $CMS->lang['ass_invalid_quantity'] = $CMS->class->language->replace( $array, $CMS->lang['ass_invalid_quantity'] );
			
                    $_SESSION['error_msg'] = $CMS->lang['ass_invalid_quantity'];
                    return false;
		}
		
		/*// Check exist name
		if($this->check_exist("ass_name",$ass_name))
		{
			$_SESSION['msg'] = $CMS->lang['ass_is_exist'];
			return false;
		}*/
                
                // Check exist code
		if($this->check_exist("ass_code",$ass_code,"",$store_id) AND $ass_code)
		{
                    
			$_SESSION['error_msg'] = $CMS->lang['ass_code_is_exist'];
			return false;
		}

        // Insert shipment moi
        $shi_name = $CMS->input['shi_name'];
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
                $shipment['shi_time'] = time();
                $shi_id = $CMS->shipment->quickadd($shipment);
            }
        }
        //else
        //{
        //    $_SESSION['msg'] = $CMS->lang['error_empty_shipment'];
        //    return false;
        //}


		// Get shipment
/*		$shi_name = $shi_id;
		$shi_id = $CMS->shipment->get_info($shi_id,"shi_id");
		
		if(!$shi_id)
		{
                    // If does not exist shipment will add new
                    $DB->query("INSERT INTO ".root_table."shipment (shi_name,user_id,shi_time) VALUES ('{$shi_name}','{$user_id}','{$ass_time}')");
                    $shi_id = $DB->last_insert_id();
                    
                    // Logs
                    $CMS->class->logs->key = "shi_{$shi_id}";
                    $CMS->class->logs->insert("Created Shipment {$shi_name}");
                }
*/                
		// Check Item quantity with quantity in table product
		$product = $DB->fetch_array($DB->query("SELECT * FROM ".root_table."product where product_deleted=0 AND product_name='{$ass_name}'"));
                
		if(!empty($product) AND $product['product_quantity'] < $ass_quantity)
		{
			//$_SESSION['msg'] = $CMS->lang['ass_quantity_error'];
			//return false;
		}


		/*
		 * Convert input subitem to hash
		 * */

        $tmp_sub_items = array();

		foreach ($CMS->input['sub_name'] as $item_key => $item_value)
        {
            for($i = 1; $i <= $CMS->input['sub_quantity'][$item_key]; $i++)
            {
                $tmp_sub_items['sub_name'][] = $CMS->input['sub_name'][$item_key];
                $tmp_sub_items['sub_price'][] = $CMS->input['sub_price'][$item_key];
                $tmp_sub_items['sub_warranty'][] = $CMS->input['sub_warranty'][$item_key];
            }
        }

		$sub_hash = json_encode($tmp_sub_items['sub_name']).json_encode($tmp_sub_items['sub_price']).json_encode($tmp_sub_items['sub_warranty']);

		// If product not exist, add new
		if(empty($product))
		{
			$product_name = $ass_name;
			$product_type = 0; // Default is product
			$product_cycle = 0; // One times
			$product_status = 0; // New
			$product_tax = 10; // Default is 10%
			$product_price = $ass_purchase_price;
			
			// Insert product
			$DB->query("INSERT INTO ".root_table."product (product_name,product_type,product_cycle,product_status,product_tax,product_price,user_id,product_time) VALUES ('{$product_name}','{$product_type}','{$product_cycle}','{$product_status}','{$product_tax}','{$product_price}','{$user_id}','{$ass_time}')");

			//clear cache product
            $CMS->class->cache->mdelete('product');

			// Log
			$product_id = $DB->last_insert_id();
			$CMS->class->logs->key = "product_{$product_id}";
			$CMS->class->logs->insert("{$CMS->lang['ass_added_product']} {$product_name}");
		}
                
                
		// Check sub item
		if(!empty($CMS->input['sub_name']))
		{
                    if(empty($CMS->input['sub_quantity']) || empty($CMS->input['sub_purchase_price']))
                    {
						$_SESSION['error_msg'] = $CMS->lang['sub_info_incomplete'];
                        return false;
                    }
				
                    // Check info
                    foreach($CMS->input['sub_name'] as $key => $value)
                    {
                        if($value AND (!$CMS->input['sub_quantity'][$key] || !$CMS->input['sub_purchase_price'][$key]))
                        {
                            $_SESSION['error_msg'] = $CMS->lang['sub_info_incomplete'];
                            return false;
                        }
                    }
		}
		
		// Insert by loop
		for($i=0;$i<$ass_quantity;$i++)
		{
 
            // Insert data
            $DB->query("INSERT INTO ".root_table."assets (ass_name,ass_code,user_id,store_id,shi_id,parent_id,supplier_id,ass_purchase_price,ass_original_price,ass_price,ass_time,ass_status,ass_warranty,pgroup_id,ass_desc,product_id,ass_key,ass_tax,ass_user_id,ass_userg_id,ass_stocktaking_date) VALUES ('{$ass_name}','{$ass_code}','{$user_id}','{$store_id}','{$shi_id}','{$parent_id}','{$supplier_id}','{$ass_purchase_price}','{$ass_original_price}','{$ass_price}','{$ass_time}','{$ass_status}','{$ass_warranty}','{$pgroup_id}','{$ass_desc}','{$product_id}', MD5('{$ass_name}_{$store_id}_{$shi_id}_{$ass_price}_{$product_id}_{$ass_warranty}_{$sub_hash}'),'{$ass_tax}','{$ass_user_id}','{$ass_userg_id}','{$ass_stocktaking_date}')");

			$assets_id = $DB->last_insert_id();

			//echo "UPDATE ".root_table."assets SET ass_keyname= '{$ass_keyname}' WHERE ass_id={$assets_id}";exit;
			$ass_keyname = "AS".$assets_id;		 
			$DB->query("UPDATE ".root_table."assets SET ass_keyname= '{$ass_keyname}' WHERE ass_id={$assets_id}");


			$CMS->class->logs->key= "assets_{$assets_id}";
			$_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['ass_added_new']} <b> {$ass_name}</b>");
			
			// Insert subitem
                        foreach ($CMS->input['sub_name'] as $key => $value)
                        {
                           if($value)
                           {
                                // Get product info
                                $sub_product_id = $CMS->product->get_info($value,"product_id");

                               $sub_warranty = $CMS->input['sub_warranty'][$key] ? $CMS->class->date->date2time($CMS->input['sub_warranty'][$key]) : 0;

                                for($j=0;$j<$CMS->input['sub_quantity'][$key];$j++)
                                {
                                    $DB->query("INSERT INTO ".root_table."assets (ass_name,ass_code,user_id,store_id,shi_id,parent_id,ass_warranty,ass_purchase_price,ass_original_price, ass_price,ass_time,ass_status,pgroup_id, product_id, ass_key,ass_tax) VALUES ('{$value}','{$CMS->input['sub_code'][$key]}','{$user_id}','{$store_id}','{$shi_id}','{$assets_id}','{$sub_warranty}','{$CMS->input['sub_purchase_price'][$key]}', '{$CMS->input['sub_original_price'][$key]}', '{$CMS->input['sub_price'][$key]}','{$ass_time}','{$ass_status}','{$pgroup_id}', '{$sub_product_id}', MD5('{$value}_{$store_id}_{$shi_id}_{$CMS->input['sub_price'][$key]}_{$sub_product_id}_{$sub_warranty}'),'{$CMS->input['sub_tax'][$key]}')");

                                    $sub_id = $DB->last_insert_id();
                                    $ass_keyname = "AS".$sub_id;
									$DB->query("UPDATE ".root_table."assets SET ass_keyname= '{$ass_keyname}' WHERE ass_id={$sub_id}");

                                    $CMS->class->logs->key= "assets_{$sub_id}";
                                    $CMS->class->logs->insert("{$CMS->lang['ass_added_subitem']} <b>[{$value}]</b>");
                                }
                           }
                        }
		}
                
                // Update assets count for table product
        $count = $DB->num_rows($DB->query("SELECT * FROM ".root_table."assets WHERE product_id='{$product_id}' AND parent_id=0 AND ass_deleted=0"));
		$DB->query("UPDATE ".root_table."product set product_quantity='{$count}' WHERE product_id='{$product_id}'");

        //clear cache product and asset
        $CMS->class->cache->mdelete($CMS->product->cache_prefix);
        $CMS->class->cache->mdelete($this->cache_prefix);

		$inserted_record = $this->get_info($assets_id);

		return $inserted_record;
	}
	
	public function check_exist( $field, $value = "", $except_value = "", $store_id = "" )
	{
		global $CMS, $DB, $member;
		
		if ( ! $field )
		{
			return true;
		}
                
                $sql_add = $store_id ? " AND store_id='{$store_id}' " : "";
		
		if ( $except_value )
		{
			$DB->query("SELECT ass_id FROM ".root_table."assets WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND ass_deleted=0 {$sql_add}");
		}
		else
		{
			$DB->query("SELECT ass_id FROM ".root_table."assets WHERE {$field}='{$value}' AND ass_deleted=0 {$sql_add}");
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
			$this->html = $CMS->class->template->load_template("skin_assets");
		}
	 
		$this->arrange_data = trim("ass_id,ass_name,ass_time,user_id");
		$default_field = isset($CMS->input['order']) ? $CMS->input['order'] : "ass_id";
		$default_order = isset($CMS->input['by']) ? $CMS->input['by'] : "DESC";
		
		$store_id = intval($CMS->input['store_id']);
                $p_id = intval($CMS->input['p_id']);
                $shi_id = intval($CMS->input['shi_id']);
      
 		$CMS->input['ass_name'] = urldecode($CMS->input['ass_name']);
 		$CMS->input['ass_code'] = urldecode($CMS->input['ass_code']);
 		$CMS->input['store_id'] = urldecode($CMS->input['store_id']);
 		$CMS->input['shi_name'] = urldecode($CMS->input['shi_name']);
 		$CMS->input['user_name'] = urldecode($CMS->input['user_name']);
 		$CMS->input['supplier_id'] = urldecode($CMS->input['supplier_id']);
 		$CMS->input['product_id'] = urldecode($CMS->input['product_id']);
 		$CMS->input['pgroup_id'] = urldecode($CMS->input['pgroup_id']);
 		
 
		$where='';
		if (($CMS->input['ass_name'])) {

			$where.=" AND `ass_name` LIKE '%{$CMS->input['ass_name']}%'";
			$this->prefix_html.="&ass_name={$CMS->input['ass_name']}";
		}
		if (($CMS->input['ass_code'])) {
			$where.=" AND ass_code LIKE '%{$CMS->input['ass_code']}%'";
			$this->prefix_html.="&ass_code={$CMS->input['ass_code']}";
		}
		if (($CMS->input['store_id'])) { 
			$where.=" AND store_id='{$CMS->input['store_id']}'";
			$this->prefix_html.="&store_id={$CMS->input['store_id']}";
		}  
		if (($CMS->input['shi_name'])) {
			$sql_table = " as S left join nh_shipment as U ON S.shi_id=U.shi_id ";
			$where.=" AND shi_name LIKE '%{$CMS->input['shi_name']}%'";
			$this->prefix_html.="&shi_name={$CMS->input['shi_name']}";
		}
		if (($CMS->input['user_name'])) {
			$sql_table = " as S left join nh_user as U ON S.user_id=U.user_id ";
			$where.=" AND user_display_name LIKE '%{$CMS->input['user_name']}%'";
			$this->prefix_html.="&user_name={$CMS->input['user_name']}";
		}
		if ($CMS->input['supplier_id']) {
			$where.=" AND supplier_id ='{$CMS->input['supplier_id']}'";
			$this->prefix_html.="&supplier_id='{$CMS->input['supplier_id']}'";
		} 

		if ($CMS->input['product_id']) {
			$where.=" AND product_id ='{$CMS->input['product_id']}'";
			$this->prefix_html.="&product_id='{$CMS->input['product_id']}'";
		}
		if ($CMS->input['pgroup_id']) {
			$where.=" AND pgroup_id ='{$CMS->input['pgroup_id']}'";
			$this->prefix_html.="&pgroup_id='{$CMS->input['pgroup_id']}'";
		}

	 
        // From module store
        if($store_id)
        {
            $where.= " AND store_id='{$store_id}'";
            $this->prefix_html.="&store_id={$store_id}";
        }
        // From module product
        else if($p_id)
        {
            $where.= " AND product_id='{$p_id}'";
            $this->prefix_html.="&product_id={$p_id}";
        }
        // From module shipment
        else if($shi_id)
        {
           $where.= " AND shi_id='{$shi_id}'"; 
           $this->prefix_html.="&shi_id={$shi_id}";
        }
        
        $this->prefix_html .= isset($CMS->input['is_group']) ? "&is_group=".intval($CMS->input['is_group']) : "";
		 
		$sql_group = (isset($CMS->input['is_group']) AND intval($CMS->input['is_group']) == 0) ? "" : "GROUP BY ass_key";
		$sql_count = $sql_group ? ",count(ass_name) as quantity" : "";
				
		$this->prefix_html=empty($this->prefix_html)? '':'?site=assets'.$this->prefix_html.'&page=';

		$sql = "SELECT * {$sql_count} FROM `".root_table."assets` {$sql_table} WHERE `ass_deleted`=0 AND is_available = 1 AND parent_id=0 {$where} {$sql_group}  ORDER BY {$default_field} {$default_order}";

        list($CMS->show_page, $cacheData) = $DB->fetch_listing($sql, $this->per_page, $this->prefix_html, $this->suffix_html, $CMS->input['page'], $this->cache_prefix);

        $output = "";

        if($cacheData)
        {
            foreach ( $cacheData as $data )
            {
                $data = $this->convertvalue($data);

                $output .= $this->html->mid_search($data);
            }
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
		$DB->query("UPDATE ".root_table."assets SET ass_deleted=1 WHERE ass_id={$data['ass_id']}");

        //clear cache asset
        $CMS->class->cache->mdelete($this->cache_prefix);
		
		// Create log
		$CMS->class->logs->key = "assets_{$data['assets_id']}";
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['ass_deleted']} <b>{$data['ass_name']}</b>");
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=assets&page={$CMS->input['page']}");
		
		return true;
	}

    public function get_list_item($ass_id, $group_by = 0)
    {
        global $CMS, $DB;

        if (!$ass_id) {
            return false;
        }

        $sql_add = '';
        $select_add = '';
        //$sql_add = $CMS->input['act'] == "show" ? " GROUP BY ass_code " : "";
        //$select_add = $sql_add ? ",count(ass_id) as quantity" : "";

        if ($group_by) {
            $sql = "SELECT *, COUNT(ass_id) as cnt {$select_add} FROM " . root_table . "assets WHERE parent_id='{$ass_id}' AND ass_deleted=0 {$sql_add} GROUP BY ass_key";
        } else {
            $sql = "SELECT * {$select_add} FROM " . root_table . "assets WHERE parent_id='{$ass_id}' AND ass_deleted=0 {$sql_add}";
        }

        //cache
        $results = $DB->fetch_data($sql, $this->cache_prefix);

        $output = [];

        if($results)
        {
            foreach ($results as $data)
            {
                $data['ass_warranty'] = $data['ass_warranty'] ? $CMS->class->date->date_format($data['ass_warranty']) : "";
                $output[] = $data;
            }
        }

        return $output;
    }
	
	public function editvalue($data)
	{
		global $CMS;
		
                $data['shi_id_bk'] = $data['shi_id'];
		$data['shi_id'] = $CMS->shipment->get_info($data['shi_id'],"shi_name");
                
                $data['supplier_id_bk'] = $data['supplier_id'];
                $data['supplier_id'] = $CMS->supplier->get_info($data['supplier_id'],"supplier_name");
				
		$data['ass_warranty'] = $data['ass_warranty'] ? $CMS->class->date->date_format($data['ass_warranty']) : "";
		
		// Get lsi sub item
		$data['list_item'] = $this->get_list_item($data['ass_id']);
                
		return $data;
	}
	
	
	public function edit() 
	{
		global $CMS, $DB, $member;

		if($CMS->input['by_key']) //update hàng loạt
        {
            $assets = $this->get_info($CMS->input['ass_key'], '', 1);
        }
        else
        {
            $assets = $this->get_info();
        }


		// Input
		$ass_name = trim($CMS->input['ass_name']);
		$ass_code = trim($CMS->input['ass_code']);
		$store_id = intval($CMS->input['store_id']);
		$shi_id = trim($CMS->input['shi_id']);
		$supplier_id = intval($CMS->input['supplier_id']);
		$ass_purchase_price = intval($CMS->input['ass_purchase_price']);
		$ass_original_price = intval($CMS->input['ass_original_price']);
		$ass_price = intval($CMS->input['ass_price']);
		$ass_warranty = $CMS->input['ass_warranty'] ? $CMS->class->date->date2time($CMS->input['ass_warranty']) : 0;
		$ass_desc = $CMS->class->editor->input("ass_desc");
		$ass_status = intval($CMS->input['ass_status']);
		$pgroup_id = intval($CMS->input['pgroup_id']);
		$product_id = intval($CMS->input['product_id']);
		$ass_tax = intval($CMS->input['ass_tax']);
		$ass_user_id = intval($CMS->input['ass_user_id']);
		$ass_userg_id = intval($CMS->input['ass_userg_id']);
		$ass_stocktaking_date = $CMS->input['ass_stocktaking_date'] ? $CMS->class->date->date2time($CMS->input['ass_stocktaking_date']) : 0;

		if(!$ass_name)
		{
			$_SESSION['error_msg'] = $CMS->lang['ass_empty_name'];
			return false;
		}

/*		if(!$ass_code)
		{
			$_SESSION['msg'] = $CMS->lang['ass_err_code'];
			return false;
		}
*/		
		if(!$store_id)
		{
			$_SESSION['error_msg'] = $CMS->lang['ass_err_store'];
			return false;
		}


		if(!$pgroup_id)
		{
			$_SESSION['error_msg'] = $CMS->lang['ass_err_cat'];
			return false;
		}

		if(!$ass_purchase_price)
		{
			$_SESSION['error_msg'] = $CMS->lang['ass_err_pprice'];
			return false;
		}
   
		// Check price
		if($CMS->class->input->check_price($ass_purchase_price)== false || ($CMS->class->input->check_price($ass_price)== false AND $ass_price))
		{
                    // Convert language
                    $array = array("assets_min_price" => $CMS->vars['assets_min_price'], "assets_max_price" => $CMS->vars['assets_max_price']);
                    $CMS->lang['ass_invalid_price'] = $CMS->class->language->replace( $array, $CMS->lang['ass_invalid_price'] );
                    
			$_SESSION['error_msg'] = $CMS->lang['ass_invalid_price'];
			return false;
		}

		// Check exist name
		/*if($this->check_exist("ass_name",$ass_name,$assets['ass_name']))
		{
			$_SESSION['msg'] = $CMS->lang['ass_is_exist'];
			return false;
		}*/

                // Check exist code
		if($this->check_exist("ass_code",$ass_code,$assets['ass_code'],$store_id) AND $ass_code)
		{
			$_SESSION['error_msg'] = $CMS->lang['ass_code_is_exist'];
			return false;
		}

		// Get shipment
                /*		
                $shi_name = $shi_id;
		$shi_id = $CMS->shipment->get_info($shi_id,"shi_id");
		
		if(!$shi_id)
		{
			// If does not exist shipment will add new
			$DB->query("INSERT INTO ".root_table."shipment (shi_name,user_id,shi_time) VALUES ('{$shi_name}','{$user_id}','{$ass_time}')");
			$shi_id = $DB->last_insert_id();
			
			// Logs
			$CMS->class->logs->key = "shi_{$shi_id}";
			$CMS->class->logs->insert("Created Shipment {$shi_name}");
		}
              
                */
                
		// If product not exist, add new
		$product = $DB->fetch_array($DB->query("SELECT * FROM ".root_table."product where product_deleted=0 AND product_name='{$ass_name}'"));
		if(empty($product))
		{
			$product_name = $ass_name;
			$product_type = 0; // Default is product
			$product_cycle = 0; // One times
			$product_status = 0; // New
			$product_tax = 10; // Default is 10%
			$product_price = $ass_purchase_price;
			
			// Insert product
			$DB->query("INSERT INTO ".root_table."product (product_name,product_type,product_cycle,product_status,product_tax,product_price,user_id,product_time) VALUES ('{$product_name}','{$product_type}','{$product_cycle}','{$product_status}','{$product_tax}','{$product_price}','{$user_id}','{$ass_time}')");

            //clear cache product
            $CMS->class->cache->mdelete('product');
			
			// Log
			$product_id = $DB->last_insert_id();
			$CMS->class->logs->key = "product_{$product_id}";
			$CMS->class->logs->insert("{$CMS->lang['ass_added_product']} {$product_name}");
		}
                
                $product_id = $product['product_id'];
		
		// Check Item quantity with quantity in table product
		//$product = $DB->fetch_array($DB->query("SELECT * FROM ".root_table."product where product_deleted=0 AND product_name='{$ass_name}'"));
                
		//if(!empty($product) AND $product['product_quantity'] < $ass_quantity)
		//{
		//	$CMS->errormsg = $CMS->lang['ass_quantity_error'];
		//	return false;
		//}

		// Reset array key
		$CMS->input['sub_name'] = array_filter(array_values($CMS->input['sub_name']));
		$CMS->input['sub_code'] = array_filter(array_values($CMS->input['sub_code']));
		$CMS->input['sub_quantity'] = array_filter(array_values($CMS->input['sub_quantity']));
		$CMS->input['sub_purchase_price'] = array_filter(array_values($CMS->input['sub_purchase_price']));
		$CMS->input['sub_original_price'] = array_filter(array_values($CMS->input['sub_original_price']));
		$CMS->input['sub_price'] = array_filter(array_values($CMS->input['sub_price']));
		$CMS->input['sub_warranty'] = array_filter(array_values($CMS->input['sub_warranty']));
        $CMS->input['sub_id'] = array_filter(array_values($CMS->input['sub_id']));
        $CMS->input['sub_ass_id'] = array_filter(array_values($CMS->input['sub_ass_id']));
		$CMS->input['sub_tax'] = array_filter(array_values($CMS->input['sub_tax']));

		$subName = $CMS->input['sub_name'] ? $CMS->input['sub_name'] : null;
		$subPrice = $CMS->input['sub_price'] ? $CMS->input['sub_price'] : null;
		$subWarranty = $CMS->input['sub_warranty'] ? $CMS->input['sub_warranty'] : null;

        $sub_hash = json_encode($subName).json_encode($subPrice).json_encode($subWarranty);

		// Check sub item
		if(!empty($CMS->input['sub_name']))
		{
                    if(empty($CMS->input['sub_purchase_price']))
                    {
						$_SESSION['error_msg'] = $CMS->lang['sub_info_incomplete'];
                        return false;
                    }
				
                    // Check info
                    foreach($CMS->input['sub_name'] as $key => $value)
                    {
                        if($value AND (!$CMS->input['sub_purchase_price'][$key]))
                        {
                            $_SESSION['error_msg'] = $CMS->lang['sub_info_incomplete'];
                            return false;
                        }
                    }
		}

                
		// Update data

        if($CMS->input['by_key'])
        {
            //GET SUBKEY
            $sql_get_subkey = "SELECT DISTINCT ass_key FROM ".root_table."assets WHERE parent_id='{$assets['ass_id']}'";

            $sql_get_subkey = $DB->query($sql_get_subkey);

            while($subkey = $DB->fetch_assoc($sql_get_subkey))
            {
                $DB->query("UPDATE ".root_table."assets SET ass_deleted=1 WHERE ass_key='{$subkey['ass_key']}'");
            }

            $DB->query("UPDATE ".root_table."assets SET ass_deleted=1 WHERE ass_key='{$CMS->input['ass_key']}'");

            for($i=1; $i <= $CMS->input['ass_quantity']; $i++)
            {
                $DB->query("INSERT INTO ".root_table."assets(ass_name, ass_code, store_id, shi_id, supplier_id, ass_purchase_price, ass_original_price, ass_price, ass_status, ass_warranty, pgroup_id, ass_desc, product_id, ass_key, ass_tax) VALUES('{$ass_name}', '{$ass_code}', '{$store_id}', '{$shi_id}', '{$supplier_id}', '{$ass_purchase_price}','{$ass_original_price}' ,'{$ass_price}', '{$ass_status}', '{$ass_warranty}', '{$pgroup_id}', '{$ass_desc}', '{$product_id}', MD5('{$ass_name}_{$store_id}_{$shi_id}_{$ass_price}_{$product_id}_{$ass_warranty}_{$sub_hash}'), '{$ass_tax}')");

                $assets_id = $DB->last_insert_id();

                $ass_keyname = "AS".$assets_id;
				$DB->query("UPDATE ".root_table."assets SET ass_keyname= '{$ass_keyname}' WHERE ass_id={$assets_id}");


                $CMS->class->logs->key= "assets_{$assets_id}";
                $_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['ass_added_new']} <b>assets {$ass_name}</b>");

                // Insert subitem
                foreach ($CMS->input['sub_name'] as $key => $value)
                {
                    if($value)
                    {
                        // Get product info
                        $sub_product_id = $CMS->product->get_info($value,"product_id");

                        $sub_warranty = $CMS->input['sub_warranty'][$key] ? $CMS->class->date->date2time($CMS->input['sub_warranty'][$key]) : 0;

                        for($j=0;$j<$CMS->input['sub_quantity'][$key];$j++)
                        {
                            $DB->query("INSERT INTO ".root_table."assets (ass_name,ass_code,user_id,store_id,shi_id,parent_id,ass_warranty,ass_purchase_price,ass_original_price, ass_price,ass_time,ass_status,pgroup_id, product_id, ass_key, ass_tax) VALUES ('{$value}','{$CMS->input['sub_code'][$key]}','{$user_id}','{$store_id}','{$shi_id}','{$assets_id}','{$sub_warranty}','{$CMS->input['sub_purchase_price'][$key]}','{$CMS->input['sub_original_price'][$key]}','{$CMS->input['sub_price'][$key]}','{$ass_time}','{$ass_status}','{$pgroup_id}', '{$sub_product_id}', MD5('{$value}_{$store_id}_{$shi_id}_{$CMS->input['sub_price'][$key]}_{$sub_product_id}_{$sub_warranty}'), '{$CMS->input['sub_tax'][$key]}')");

                            $sub_id = $DB->last_insert_id();

                            $ass_keyname = "AS".$sub_id;
							$DB->query("UPDATE ".root_table."assets SET ass_keyname= '{$ass_keyname}' WHERE ass_id={$sub_id}");


                            $CMS->class->logs->key= "assets_{$sub_id}";
                            $CMS->class->logs->insert("{$CMS->lang['ass_added_subitem']} <b>[{$value}]</b>");
                        }
                    }
                }
            }

            //clear cache asset
            $CMS->class->cache->mdelete($this->cache_prefix);

            $data = $this->get_info($assets_id);

            $this->update_asset_key($assets['ass_key'], $data['ass_key']);

            return $data;
        }
        else
        {
			$count_all = 0;
			$array_all = array();
			$array_old = array();
			 
			// Update for all assets same type
			if($CMS->input['ass_is_edit_all'] == 1)
			{
				// Select all assets same name
				$sql_all = $DB->query("SELECT * FROM ".root_table."assets where ass_name = '{$assets['ass_name']}' AND ass_id!='{$assets['ass_id']}' AND ass_deleted=0  AND ass_key = '{$assets['ass_key']}' ");
				$count_all = $DB->num_rows($sql_all);
				
				if($count_all > 0)
				{
					while($data_all = $DB->fetch_array($sql_all))
					{
						$array_all[] = $data_all['ass_id'];
						$array_old[] = $data_all;
					 	$sql_all_qr .= $sql_all_qr ? ",{$data_all['ass_id']}" : $data_all['ass_id'];
					}
					
					 $sql_all_qr = " OR ass_id IN ($sql_all_qr) ";
				}
			}
 
 
            $DB->query("UPDATE ".root_table."assets SET ass_name='{$ass_name}',ass_code='{$ass_code}',store_id='{$store_id}',shi_id='{$shi_id}',supplier_id='{$supplier_id}',ass_purchase_price='{$ass_purchase_price}',ass_original_price='{$ass_original_price}',ass_price='{$ass_price}',ass_status='{$ass_status}',ass_warranty={$ass_warranty},pgroup_id='{$pgroup_id}',ass_desc='{$ass_desc}',product_id='{$product_id}', ass_key = MD5('{$ass_name}_{$store_id}_{$shi_id}_{$ass_price}_{$product_id}_{$ass_warranty}_{$sub_hash}'), ass_tax='{$ass_tax}',ass_user_id='{$ass_user_id}',ass_userg_id='{$ass_userg_id}', ass_stocktaking_date='{$ass_stocktaking_date}' WHERE (ass_id='{$assets['ass_id']}' {$sql_all_qr} )");

            $assets_id = $assets['ass_id'];
			$array_all[] = $assets_id;
			
			for($i=0;$i<count($array_all);$i++)
			{
				$CMS->class->logs->old_data = $array_old[$i];
				$CMS->class->logs->key = "ass_{array_all[$i]}";
				$CMS->class->logs->save_detail("assets",$array_all[$i],$this->get_info($array_all[$i]));
			}
			
            $_SESSION['msg'] = "{$CMS->lang['ass_edited']} <b>[{$ass_code}]</b>";
            $_SESSION['msg'] .= $count_all > 0 ? "<br />{$count_all} {$CMS->lang['ass_updated_same']}" : "";

            // Update subitem
            foreach ($CMS->input['sub_name'] as $key => $value)
            {
                if($value)
                {

                    //for($j=0;$j<$CMS->input['sub_quantity'][$key];$j++)
                    {
                        $sub_warranty = $CMS->input['sub_warranty'][$key] ? $CMS->class->date->date2time($CMS->input['sub_warranty'][$key]) : 0;

                        // Insert if id not exist
                        if(!$CMS->input['sub_id'][$key])
                        {
                            $DB->query("INSERT INTO ".root_table."assets (ass_name,ass_code,user_id,store_id,shi_id,parent_id,ass_warranty,ass_purchase_price,ass_original_price,ass_price,ass_time,ass_status,pgroup_id, product_id, ass_key,ass_tax) VALUES ('{$value}','{$CMS->input['sub_code'][$key]}','{$user_id}','{$store_id}','{$shi_id}','{$assets_id}','".($sub_warranty)."','{$CMS->input['sub_purchase_price'][$key]}','{$CMS->input['sub_original_price'][$key]}','{$CMS->input['sub_price'][$key]}','{$ass_time}','{$ass_status}','{$pgroup_id}', '{$CMS->input['sub_id'][$key]}', MD5('{$value}_{$store_id}_{$shi_id}_{$CMS->input['sub_price'][$key]}_{$CMS->input['sub_id'][$key]}_{$sub_warranty}'),'{$CMS->input['sub_tax'][$key]}')");

                            $sub_id = $DB->last_insert_id();
                            $CMS->class->logs->key= "assets_{$CMS->input['sub_code'][$key]}";
                            $CMS->class->logs->insert("{$CMS->lang['ass_added_subitem']} <b>[{$value}]</b>");

                            // Update input sub_id
                            $CMS->input['sub_id'][$key] = $sub_id;
                        }
                        else
                        {
                            $DB->query("UPDATE ".root_table."assets SET ass_name='{$value}',ass_code='{$CMS->input['sub_code'][$key]}',store_id='{$store_id}',shi_id='{$shi_id}',ass_warranty='".$sub_warranty."',ass_purchase_price='{$CMS->input['sub_purchase_price'][$key]}',ass_original_price='{$CMS->input['ass_original_price'][$key]}',ass_price='{$CMS->input['sub_price'][$key]}',ass_status='{$ass_status}',pgroup_id='{$pgroup_id}', product_id='{$CMS->input['sub_id'][$key]}', ass_key=MD5('{$value}_{$store_id}_{$shi_id}_{$CMS->input['sub_price'][$key]}_{$CMS->input['sub_id'][$key]}_{$sub_warranty}'),ass_tax='{$CMS->input['sub_tax'][$key]}' where ass_id='{$CMS->input['sub_ass_id'][$key]}'");

                            $CMS->class->logs->key= "assets_{$CMS->input['sub_code'][$key]}";
                            $CMS->class->logs->insert("{$CMS->lang['ass_edited_subitem']} <b>[{$value}]</b>");
                        }
                    }
                }
            }

            // Check and update subitem is removed
            $list_item = $this->get_list_item($assets_id);
            for($i=0;$i<count($list_item);$i++)
            {
                if(!in_array($list_item[$i]['ass_id'],$CMS->input['sub_ass_id']))
                {
                    // Remove parent id
                    $DB->query("UPDATE ".root_table."assets SET parent_id=0 where ass_id='{$list_item[$i]['sub_ass_id']}'");

                    $CMS->class->logs->key = "assets_{$list_item[$i]['ass_id']}";
                    $CMS->class->logs->insert("{$CMS->lang['ass_remove_from']} {$ass_code}");
                }
            }

            // Update assets count for table product
            if($assets['product_id']!=$product_id)
            {
                // Update for old
                $count = $DB->num_rows($DB->query("SELECT * FROM ".root_table."assets WHERE product_id='{$assets['product_id']}' AND parent_id=0 AND ass_deleted=0"));
                $DB->query("UPDATE ".root_table."product set product_quantity='{$count}' WHERE product_id='{$assets['product_id']}'");


                // Update for new
                $count = $DB->num_rows($DB->query("SELECT * FROM ".root_table."assets WHERE product_id='{$product_id}' AND parent_id=0 AND ass_deleted=0"));
                $DB->query("UPDATE ".root_table."product set product_quantity='{$count}' WHERE product_id='{$product_id}'");
            }

            //clear cache product, asset
            $CMS->class->cache->mdelete($CMS->product->cache_prefix);
            $CMS->class->cache->mdelete($this->cache_prefix);


            return TRUE;
        }
	}

    public function auto_run()
    {
        global $CMS, $DB, $member;
//        print_r($CMS->input); exit;
        if (!isset($this->html)) {
            $this->html = $CMS->class->template->load_template("skin_assets");
        }

        /*if ($CMS->class->cache->check("user_{$member['user_id']}_assets_{$CMS->vars['default_language']}")) {
            $CMS->vars['action_controller']=$CMS->class->cache->load("user_{$member['user_id']}_assets_{$CMS->vars['default_language']}");
        } else*/
        {
            $data = "";

            if ($CMS->permit["assets_move"]) {
                $data .= <<<EOF
 			   <option value="assets_move">{$CMS->lang['ass_move_subitem']}</option>
EOF;
            }

            $data .= <<<EOF
<option value="preview_barcode">{$CMS->lang['print_barcode']}</option>
EOF;


            if ($CMS->permit["order_add"]) {
                $this->control = 2;
                $data2 .= <<<EOF
 			   <option value="order_add_multi">{$CMS->lang['ass_add_order']}</option>
EOF;
            }
            $data2 .= <<<EOF
 			   <option value="transfer_multi">{$CMS->lang['ass_move_store']}</option>
 			    <option value="export_multi">{$CMS->lang['ass_export']}</option>
EOF;


            $data = $this->control == 2 ? $data . $data2 : $data;
            $CMS->class->cache->save("user_{$member['user_id']}_assets_{$CMS->vars['default_language']}", $data);
            $CMS->vars['action_controller'] = $data;


        }

        $this->action_control = $this->html->control();
    }
	public function search(){
            
		global $CMS, $DB, $member;
		
		
                
                // Check search from other site
                if(!$CMS->input['current_search'])
                {
                   //$this->search_do();
                   //return true;
                }
				                
		$str='';
                $CMS->input['ass_name'] = trim($CMS->input['quick_search_keyword']) ? trim($CMS->input['quick_search_keyword']) : $CMS->input['ass_name'];
		if (trim($CMS->input['ass_name'])) {
			$str.='&ass_name='.trim($CMS->input['ass_name']);
		}
		if (trim($CMS->input['ass_code'])) {
			$str.='&ass_code='.trim($CMS->input['ass_code']);
		}
		if (trim($CMS->input['user_name'])) {
			$str.='&user_name='.trim($CMS->input['user_name']);
		}
		if ($CMS->input['store_id']) {
			$str.='&store_id='.($CMS->input['store_id']);
		}
		if (trim($CMS->input['shi_id'])) {
			$str.='&shi_id='.trim($CMS->input['shi_id']);
		}
		if (intval($CMS->input['supplier_id'])) {
			$str.='&supplier_id='.intval($CMS->input['supplier_id']);
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=assets{$str}&as=1");
	}

        public function search_do()
        {
            global $CMS, $DB, $member;
            
            $this->html = $CMS->class->template->load_template("skin_assets");
            
            $store_id = intval($CMS->input['store_id']);
            $p_id = intval($CMS->input['p_id']);
            $shi_id = intval($CMS->input['shi_id']);
            $lang_title = "";
			
			if(!$store_id AND !$p_id AND !$shi_id)
			{
				$CMS->global->redirect($CMS->vars['root_domain']."/?site=assets");
			}
            
            // From module store
            if($store_id)
            {
                $sql_add = " AND store_id='{$store_id}'";
				$name=$CMS->store->get_info($store_id,"store_name");
                $lang_title = $CMS->lang['assets_search_store'].": ".$name;
            }
            // From module product
            else if($p_id)
            {
                $sql_add = " AND product_id='{$p_id}'";
				$name=$CMS->product->get_info($p_id,"product_name");
				$lang_title = $CMS->lang['assets_search_product'].": ".$name;
            }
            // From module shipment
            else if($shi_id)
            {
               $sql_add = " AND shi_id='{$shi_id}'"; 
			   $name=$CMS->shipment->get_info($shi_id,"shi_name");
			   $lang_title = $CMS->lang['assets_search_shipment'].": ".$name;
            }
			
            list($CMS->show_page, $this->sql_query) = $CMS->class->page->create("SELECT *,count(ass_name) as quantity FROM `".root_table."assets` {$sql_table} WHERE `ass_deleted`=0 AND parent_id=0 {$sql_add} GROUP BY ass_name ORDER BY ass_id DESC ", $this->per_page, $this->prefix_html, $this->suffix_html);
		
            if ($DB->num_rows($this->sql_query)>0) 
            {
                while ($data=$DB->fetch_array($this->sql_query)) 
                {
                    $data = $this->convertvalue($data);
                    $output .= $this->html->mid_search($data);
                   

                }
            } 
            else 
            {
                $output .= $this->html->none();
            } 
            
            $CMS->core->page_title = $lang_title;
            $CMS->output.=$this->html->head_search($lang_title);
            $CMS->output.=$output;
            $CMS->output.=$this->html->foot();
            
            
        }


	public function get_info( $record_id = 0, $field_name = "", $group_by=0)
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "assets" )
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

		if(!$group_by)
        {
        	$clause = "";
        	if(is_numeric($record_id))
        	{
        		$clause = " ass_id='{$record_id}' OR ";
        	}
        	
            $sql = "SELECT * FROM ".root_table."assets WHERE ( {$clause} ass_name='{$record_id}' OR ass_code='{$record_id}' OR ass_key LIKE '{$record_id}') AND ass_deleted = 0 {$this->sql_add} ORDER BY ass_id DESC LIMIT 1";
        }
        else
        {
            $sql = "SELECT *, COUNT(ass_id) AS cnt FROM ".root_table."assets WHERE ass_key = '{$record_id}' AND ass_deleted = 0 {$this->sql_add} GROUP BY ass_key ORDER BY ass_id DESC LIMIT 1";
        }

        //cache
        $data = $DB->fetch_data($sql, $this->cache_prefix)[0];

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

	public function get_asset_byproduct( $product_id = 0,  $sql_add = "", $field_name = "", $limit = 1)
	{
		global $CMS, $DB, $member;

		if ( ! $product_id AND $CMS->input['site'] == "assets" )
		{
			$product_id = intval($CMS->input['id']);
		}

		// Clear record
		$product_id = strip_tags($product_id);
		
		// Check record		
		if ( ! $product_id )
		{
			return false;
		}

 
    	if(is_numeric($product_id))
    	{
    		$clause = " product_id='{$product_id}'  ";
    	}
    	if($sql_add != "")
    	{
    		$clause .= $sql_add;
    	}
        $sql = "SELECT * FROM ".root_table."assets WHERE {$clause} AND 1=1 AND ass_deleted = 0  ORDER BY ass_id DESC LIMIT {$limit}";
         
        //cache
        $data = $DB->fetch_data($sql, $this->cache_prefix)[0];

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



	public function convertvalue($data) 
	{
		global $CMS;

		$data['data_bk'] = $data;

		$data['store_id_bk'] = $data['store_id'];
		$data['shi_id_bk'] = $data['shi_id']; 
		$data['store_id'] = $CMS->store->get_info($data['store_id'],"store_name");
		$data['shi_id'] = $CMS->shipment->get_info($data['shi_id'],"shi_name");
		$data['parent_id'] = $this->get_info($data['parent_id'],"ass_name");
		$data['user_name'] = $CMS->user->get_info($data['user_id'],"user_name");
		$data['ass_time'] = $CMS->class->date->date_format($data['ass_time']);
		$data['supplier_id'] = $CMS->supplier->get_info($data['supplier_id'],"supplier_name");
		$data['ass_status'] = $CMS->lang["ass_status_{$data['ass_status']}"];
		$data['ass_warranty'] = $data['ass_warranty'] ? $CMS->class->date->date_format($data['ass_warranty'],1) : "";
		$data['quantity'] = $data['quantity'] ? $data['quantity'] : 1;
		$data['ass_price'] = $CMS->class->input->currency($data['ass_price']);
		$data['ass_original_price'] = $CMS->class->input->currency($data['ass_original_price']);
		$data['ass_purchase_price'] = $CMS->class->input->currency($data['ass_purchase_price']);
		$data['pgroup_id_bk'] =  $CMS->product_group->getInfo($data['pgroup_id'], 'product_group_name');
	 
		if($CMS->permit['assets_read'] == 1)
		{
			if($data['quantity'] == 1)
			{
				$data['ass_name_bk'] = "<a href=\"{$CMS->vars['root_domain']}/?site=assets&act=show&id={$data['ass_id']}\">{$data['ass_name']}</a>";
			}
			else
			{
				$data['ass_name_bk'] = "<a href=\"{$CMS->vars['root_domain']}/?site=assets&ass_name={$data['ass_name']}&is_group=0\">{$data['ass_name']}</a>";
			}
			
			if($CMS->input['act'] == "show")
			{
				$data['ass_name_bk'] = "<span style=\"float:left\"><a href=\"{$CMS->vars['root_domain']}/?site=assets&act=show&id={$data['ass_id']}\">{$data['ass_name']}</a></span> <span style=\"float:left;\" title=\"Search\"><a href=\"{$CMS->vars['root_domain']}/?site=assets&ass_name={$data['ass_name']}\" style=\"margin-left:10px\"><i class=\"fa fa-search\" style=\"color:#b9b9b9\" aria-hidden=\"true\"></i></a></span>";
			}
		}
		else
		{
			$data['ass_name_bk'] = $data['ass_name'];
		}

		// Load list item
		$data['list_item'] = $this->get_list_item($data['ass_id']);
		$data['is_exist_subitem'] = empty($data['list_item']) ? 0 : 1;
                
		// Usergroup + user assign assets
		$data['ass_userg_name'] = $CMS->user->get_group_info($data['ass_userg_id'],"userg_title");
		$user = $CMS->user->get_info($data['ass_user_id']);
		$data['ass_user_name'] = $user['user_name'];
		$data['ass_user_display_name'] = $user['user_display_name'];
		
		$data['store_id'] = ($data['store_id'] AND $CMS->permit['store_read']) ? "<ul><li style=\"display:inline-block;width:auto !important;border:none !important\"><a style=\"width:auto !important\" href=\"{$CMS->vars['root_domain']}/?site=store&act=show&id={$data['store_id_bk']}\">{$data['store_id']}</a></li> <li style=\"display:inline-block\" title=\"Search\"><a href=\"{$CMS->vars['root_domain']}/?site=assets&store_id={$data['store_id_bk']}\" style=\"margin-left:10px\"><i class=\"fa fa-search\" style=\"color:#b9b9b9\" aria-hidden=\"true\"></i></a></li>" : $data['store_id'];
			
		$data['shi_id'] = ($data['shi_id'] AND $CMS->permit['shipment_read']) ?  "<ul><li style=\"display:inline-block;width:auto !important;border:none !important\"><a style=\"width:auto !important\" href=\"{$CMS->vars['root_domain']}/?site=shipment&act=show&id={$data['shi_id_bk']}\">{$data['shi_id']}</a></li> <li style=\"display:inline-block\" title=\"Search\"><a href=\"{$CMS->vars['root_domain']}/?site=assets&shi_id={$data['shi_id_bk']}\" style=\"margin-left:10px\"><i class=\"fa fa-search\" style=\"color:#b9b9b9\" aria-hidden=\"true\"></i></a></li>" : $data['shi_id'];
                
		$data['record_cnt'] = $this->record_cnt;

		$this->record_cnt++;
		return $data;
	}

	public function action ($id=null) {
		if (!is_null($id)) {
			global $CMS, $DB, $member;
			$DB->query("SELECT `assets_act`, assets_title FROM `".root_table."assets` WHERE `cus_id`={$member['cus_id']} AND `assets_id`={$id}");
			$data = $DB->fetch_array();
			if ($data['assets_act']==2) {
				return FALSE;
			}
			$count = $DB->query("UPDATE `".root_table."assets` SET `{$CMS->input['action']}`='{$CMS->input['value']}' WHERE `cus_id`='{$member['cus_id']}' AND `assets_id` = {$id}");
			if($count)
			{
				if($CMS->input['action'] == "assets_deleted")
				{
					$_SESSION['msg'] = $CMS->lang['emsg_deleted_success_assets']. $data['assets_title'];
				}else
				{
					$_SESSION['msg'] = $CMS->lang['emsg_update_success_assets']. $data['assets_title'];
				}
			}else
			{
				$_SESSION['msg'] = $CMS->lang['emsg_update_error_assets']. $data['assets_title'];
			}
			return true;
		}
		return false;
	}

    /**
     * @return string
     */
	public function get_parent_list()
	{
		global $CMS, $DB;

		$sql = "SELECT * FROM ".root_table."assets WHERE ass_deleted=0 AND parent_id=0";

		$results = $DB->fetch_data($sql, $this->cache_prefix);
		
		$output = "<option value=''>{$CMS->lang['select']}</option>";	
		
		foreach($results as $data)
		{
			$output .= "<option value='{$data['ass_id']}'>{$data['ass_name']}</option>";	
		}
		
		return $output;
	}

    public function loadlist()
    {
            global $CMS, $DB, $member;

            // Continue		
            $ass_input = urldecode(trim($CMS->class->filter->clean_value($CMS->input['ass_input'])));

            $sql_add = "";
			
            $output = "";

			if($CMS->input['search_name'] == "shipment")
			{
				$sql_add .= " (shi_name like '%{$ass_input}%') AND ";
				list($this->show_page, $sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."shipment WHERE {$sql_add} shi_deleted=0 ORDER BY shi_id DESC", $this->per_page,"","",1,1,"shi_list");
				
                                if($DB->num_rows($sql_query))
                                {
                                    while ( $data = $DB->fetch_array( $sql_query ) )
                                    {
                                            $output.="<option value='{$data['shi_id']}|{$data['shi_name']}|{$data['user_id']}|{$data['shi_time']}'>{$data['shi_name']}</option>";
                                    }
                                }
                                else
                                {
                                    $output.="<option value=''>{$CMS->lang['not_data']}</option>";
                                }
			}
			elseif($CMS->input['search_name'] == "product")
			{
			  if($CMS->input['is_get_one'])
			  {
				  $data = $CMS->product->get_info($ass_input);
				  $output = json_encode(unserialize(base64_decode($data['product_subitem'])));
			  }
			  else
			  {
				  $sql_add .= " (product_name like '%{$ass_input}%' OR product_code like '%{$ass_input}%' OR product_sku like '%{$ass_input}%' ) AND ";
				  $sql_query = $DB->query("SELECT * FROM ".root_table."product as P left join nh_supplier as S on P.sup_id=S.supplier_id WHERE {$sql_add} product_deleted=0 AND product_type=0 ORDER BY product_id DESC");

				  if($DB->num_rows($sql_query))
				  {
					  while ( $data = $DB->fetch_array( $sql_query ) )
					  {
                                                $output .= "<li p_id='{$data['product_id']}' p_name='{$data['product_name']}' p_code='{$data['product_code']}'  p_price='{$data['product_price']}' sup_id='{$data['sup_id']}' sup_name='{$data['supplier_name']}' p_item='{$data['product_subitem']}' p_group='{$data['product_group']}' p_guarantee='{$data['product_guarantee_default']}' onclick='update_data(this)'><span class='name_show' title='null'>{$data['product_name']}</span></li>";
					  }
				  }
				  else
				  {
					  $output.="<option value=''>{$CMS->lang['not_data']}</option>";
				  }
			  }
			}
			elseif($CMS->input['search_name'] == "supplier")
			{
				$sql_add .= " (supplier_name like '%{$ass_input}%' OR supplier_code like '%{$ass_input}%') AND ";
				list($this->show_page, $sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."supplier WHERE {$sql_add} supplier_deleted=0 ORDER BY supplier_id DESC", $this->per_page,"","",1,1,"sup_list");
				
				while ( $data = $DB->fetch_array( $sql_query ) )
				{
					$output.="{$data['supplier_id']}|{$data['supplier_name']}|{$data['supplier_code']}|{$data['supplier_address']}|{$data['supplier_supplier_email']}|{$data['supplier_phone']}||||";
				}
			}	
			elseif($CMS->input['search_name'] == "assets")
			{
				$ass_expect_id = trim($CMS->input['ass_expect_id']);
                                
                                if(substr($ass_expect_id,-1) == ",")
                                {
                                    $ass_expect_id = substr($ass_expect_id,0,  strlen($ass_expect_id)-1);
                                }
                                
				$sql_add .= " (ass_name like '%{$ass_input}%' OR ass_code like '%{$ass_input}%') AND ";
                                //print "SELECT * FROM ".root_table."assets WHERE {$sql_add} ass_deleted=0 AND parent_id=0 AND ass_id NOT IN ({$ass_expect_id}) ORDER BY ass_id DESC";exit;
				$sql_query = $DB->query("SELECT * FROM ".root_table."assets WHERE {$sql_add} ass_deleted=0 AND parent_id=0 AND ass_id NOT IN ({$ass_expect_id}) ORDER BY ass_id DESC");
				
				if($DB->num_rows($sql_query))
				{
					while ( $data = $DB->fetch_array( $sql_query ) )
					{
						$output.="<option value='{$data['ass_id']}|{$data['ass_name']}'>{$data['ass_name']}</option>";
					}
				}
				else
				{
					$output.="<option value=''>{$CMS->lang['not_data']}</option>";
				}
			}			
			else
			{
            	$sql_add .= " (ass_name like '%{$ass_input}%' OR ass_code LIKE '%{$ass_input}%') AND ";
				list($this->show_page, $sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."assets WHERE {$sql_add} ass_deleted=0 ORDER BY ass_id DESC", $this->per_page,"","",1,1,"ass_list");
				
				while ( $data = $DB->fetch_array( $sql_query ) )
				{
					$assets = $this->convertvalue($data);
	
					$output.= "{$assets['ass_id']}|{$assets['ass_name']}|{$assets['ass_code']}|{$assets['shi_id']}|{$assets['user_id']}||||";
				}
			}


            print $output;exit;
    }

    public function searchKey($key='', $store_id=0, $supplier_id = 0)
    {
        global $CMS, $DB;

        $clause = "";
        if($key)
        {
            $clause = " AND (ass_name LIKE '%{$key}%' OR ass_code LIKE '%{$key}%' OR ass_keyname LIKE '%{$key}%' OR ass_key LIKE '%{$key}%'  ) ";
        }

        if($store_id)
        {
        	$clause .= " AND store_id = '{$store_id}' ";
        }

        if($supplier_id)
        {
        	$clause .= " AND supplier_id = '{$supplier_id}' ";
        }

        $sql = "SELECT *, COUNT(ass_id) as cnt FROM ".root_table."assets WHERE ass_deleted = 0 AND parent_id=0 {$clause} AND is_available = 1 GROUP BY ass_key ORDER BY ass_id LIMIT 10";

        //Query
        $results = $DB->fetch_data($sql, $this->cache_prefix);

        if($results)
        {
            foreach ($results as $result)
            {

                $price = $result['ass_price'];

                $result['ass_price_show'] = $CMS->class->input->currency($price);
                $data[] = $result;
            }
        }

        return $data;
    }

    public function addQuick($data=array())
    {
        global $CMS, $DB,$member;
 
        $list_assets = json_decode($data['request_product'], true);
 // print "<pre>";
 // print_r($list_assets);exit;
        // foreach insert assets
        foreach ($list_assets as $key => $value)
        {
         
            // Input
            $ass_name = $CMS->class->editor->input(trim($value['product_name']), "text");
            $ass_code = trim($value['product_code']);
            $store_id = intval($data['store_id']);
            $shi_id = trim($data['shi_id']);
            $supplier_id = intval($data['supplier_id']);
            $ass_purchase_price = intval($value['product_price']);
            $ass_original_price = intval($value['product_price_original']);
            $ass_price = intval($value['product_price_sell']);
            $ass_tax = $value['product_tax'];
            
            $ass_quantity = intval($value['product_quantity']);
            $ass_desc = $CMS->class->editor->input($value['product_description'], "text");
            $ass_status = 1;
            $pgroup_id = intval($value['product_group']);
            $product_id = intval($value['product_id']);
            $user_id = $member['user_id'];
            $ass_time = time();
            $ass_warranty = 0;
            // MD5 HASH
            $tmp_sub_items = array();
  
			foreach ($value['product_subitem'] as $item_key => $item_value)
	        {
	            for($i = 1; $i <= $item_value['product_quantity']; $i++)
	            {
	                $tmp_sub_items['sub_name'][] = $item_value['product_name'];
	                $tmp_sub_items['sub_price'][] = $item_value['product_price'];
	                $tmp_sub_items['sub_warranty'][] = 0;
	            }
	        }

			$sub_hash = json_encode($tmp_sub_items['sub_name']).json_encode($tmp_sub_items['sub_price']).json_encode($tmp_sub_items['sub_warranty']);

     		$ass_key = MD5("{$ass_name}_{$store_id}_{$shi_id}_{$ass_price}_{$product_id}_{$ass_warranty}_{$sub_hash}");
 
            for($x=1; $x<=$ass_quantity; $x++)
            {
                // insert tài sản
                $count = $DB->query("INSERT INTO ".root_table."assets (ass_name, ass_code, user_id, store_id, shi_id, supplier_id, ass_purchase_price, ass_original_price, ass_price, ass_time, ass_status, ass_desc, product_id, pgroup_id, ass_tax,ass_key) VALUES ('{$ass_name}', '{$ass_code}', '{$user_id}', '{$store_id}', '{$shi_id}', '{$supplier_id}', '{$ass_purchase_price}', '{$ass_original_price}',  '{$ass_price}',  '{$ass_time}', '{$ass_status}', '{$ass_desc}', '{$product_id}', '{$pgroup_id}', '{$ass_tax}', '{$ass_key}')");
                $assets_id = $DB->last_insert_id();

                if($count)
                {
                    $CMS->class->logs->key= "assets_{$assets_id}";
                    $CMS->class->logs->insert("{$CMS->lang['ass_added_subitem']} <b>[{$ass_name}]</b>");

                    if(count($value['product_subitem']) > 0)
                    {
                        foreach ($value['product_subitem'] as $key => $row)
                        {
                            $product_id = $row['product_id'];
                            $data_product = $CMS->product->get_info($product_id);

                            $ass_name_sub = $CMS->class->editor->input($row['product_name'], "text");
                            $ass_code = $data_product['product_code'];
                            $ass_desc = $CMS->class->editor->input($row['product_description'],"text");
                            $supplier_id = $data_product['sup_id'];
                            $ass_purchase_price = $row['product_price'];
                            $ass_tax = $row['product_tax'];
                            $pgroup_id = $data_product['product_group'];
                            $sub_ass_price = 0;
                            $quantity = $row['product_quantity'] > 0 ? $row['product_quantity'] : 1;
                            $ass_warranty = 0;

                            $sub_ass_key = MD5("{$ass_name_sub}_{$store_id}_{$shi_id}_{$sub_ass_price}_{$product_id}_{$ass_warranty}");
                            for($y=1; $y <= $quantity; $y++)
                            {
                                $count2 = $DB->query("INSERT INTO ".root_table."assets (ass_name, ass_code, user_id, store_id, shi_id, supplier_id, ass_purchase_price, ass_time, ass_status, ass_desc, product_id, pgroup_id, ass_tax, parent_id, ass_key) VALUES ('{$ass_name_sub}', '{$ass_code}', '{$user_id}', '{$store_id}', '{$shi_id}', '{$supplier_id}', '{$ass_purchase_price}', '{$ass_time}', '{$ass_status}', '{$ass_desc}', '{$product_id}', '{$pgroup_id}', '{$ass_tax}', '{$assets_id}', '{$sub_ass_key}')");
                                $assets_id2 = $DB->last_insert_id();
                                if($count2)
                                {
                                    $CMS->class->logs->key= "assets_{$assets_id2}";
                                    $CMS->class->logs->insert("{$CMS->lang['ass_added_subitem']} <b>[{$ass_name_sub}]</b>");
                                }

                            }
                        }
                    }

                }


            }// End for

        }// End foreach

        //Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);

    }

    function convert_input_old($data = [])
    {
        global $CMS;

        $arr = ['ass_name', 'ass_id', 'ass_key', 'ass_code', 'ass_quantity', 'ass_price', 'ass_amount', 'ass_tax', 'ass_discount_type', 'ass_discount_value'];

        $tmp = [];

        foreach ($arr as $key)
        {
            $tmp[$key] = array_values($data[$key]);
        }

        $return = [];

        foreach ($tmp['ass_key'] as $k => $v)
        {
            if($v)
            {
                foreach ($arr as $key)
                {
                    $return[$k][$key] = $tmp[$key][$k];
                }
            }
        }

        return $return;
    }

    function convert_trx_item_old($data = [])
    {
        global $CMS;

        $arr = [
            'ass_name' => 'tri_name',
            'ass_id' => 'ass_key',
            'ass_key' => 'ass_key',
            'ass_code' => 'tri_description',
            'ass_quantity' => 'tri_quantity',
            'ass_price' => 'tri_price',
            'ass_old_price' => 'tri_old_price',
            'ass_tax' => 'tri_tax',
            'ass_discount_type' => 'tri_discount_type',
            'ass_discount_value' => 'tri_discount_value',
            'ass_amount' => 'tri_total',
        ];

        foreach ($data as $item)
        {
            $tmp = [];
            foreach ($arr as $k => $v)
            {
                $tmp[$k] = $item[$v];
            }

            $return[] = $tmp;
        }

        return $return;
    }
	
	public function move_subitem()
	{
		global $CMS, $DB;
		
		$list_subitem = trim($CMS->input['list_subitem_for_move']);
		$ass_name = trim($CMS->input['ass_name_to']);
		$ass_id_to = intval($CMS->input['product_id_to']);
                $ass_is_move_to_item = intval($CMS->input['ass_is_move_to_item']);
		
		$list_subitem = explode(",",$list_subitem);
		
		if(empty($list_subitem))
		{
			print json_encode(array("status" => "error", "msg" => "Vui lòng chọn tài sản để di chuyển"));exit;
		}
		
		if((!$ass_name OR !$ass_id_to) AND !$ass_is_move_to_item )
		{
			print json_encode(array("status" => "error", "msg" => "Vui lòng chọn tài sản chuyển vào"));exit;
		}
		
		$list_sub_moved = "";
		
		// Update new parent
		for($i=0;$i<count($list_subitem);$i++)
		{	
			if($list_subitem[$i])
			{
				// Get info
				$assets = $this->get_info($list_subitem[$i]);
				$parent = $this->get_info($assets['parent_id']);
                                
                                // Move subitem to item
                                if($ass_is_move_to_item)
                                {
                                    $DB->query("UPDATE ".root_table."assets SET parent_id=0 where ass_id='{$list_subitem[$i]}'");
                                    
                                    // Log move
                                    $CMS->class->logs->key = "assets_{$list_subitem[$i]}";
                                    $CMS->class->logs->insert("{$CMS->lang['ass_moved_to_original']}");
                                }
                                // Move subitem to other item
				else
                                {
                                    // Update query
                                    $DB->query("UPDATE ".root_table."assets SET parent_id='{$ass_id_to}' where ass_id='{$list_subitem[$i]}'");

                                    $list_sub_moved .= $list_subitem[$i]+",";

                                    if($assets['parent_id'])
                                    {
                                        // Log move from
                                        $CMS->class->logs->key = "assets_{$parent['ass_id']}";
                                        $CMS->class->logs->insert("{$CMS->lang['ass_moved_from']} [{$assets['ass_name']}] {$CMS->lang['ass_moved_to']} [{$ass_name}]");
                                        
                                        // Log move to
                                        $CMS->class->logs->key = "assets_{$ass_id_to}";
                                        $CMS->class->logs->insert("{$CMS->lang['ass_added_to']} [{$assets['ass_name']}] {$CMS->lang['ass_added_from']} [{$parent['ass_name']}]");
                                    }
                                    else
                                    {
                                        // Log move from
                                        $CMS->class->logs->key = "assets_{$assets['ass_id']}";
                                        $CMS->class->logs->insert("{$CMS->lang['ass_move_one_to']} [{$ass_name}]");
                                        
                                        // Log move to
                                        $CMS->class->logs->key = "assets_{$ass_id_to}";
                                        $CMS->class->logs->insert("{$CMS->lang['ass_added_to']} [{$assets['ass_name']}] {$CMS->lang['ass_added_from_original']}"); 
                                    }
                                }
			}
		}

		//Clear cache
        $CMS->class->cache->mdelete($this->cache_prefix);
		
		print json_encode(array("status" => "success", "msg" => "{$CMS->lang['ass_moved_success']}","list_sub_moved" => $list_sub_moved));exit;
	}
        
        public function move()
        {
            global $CMS, $DB, $member;
            
            // Get input
            for($i=0;$i<$CMS->input['data_cnt'];$i++)
            {
                if($CMS->input["id_{$i}"])
                {
                    // Get info
                    $assets = $this->get_info($CMS->input["id_{$i}"]);
                    
                    // Check assets is have subitem
                    $count = $DB->query("SELECT ass_id FROM ".root_table."assets WHERE parent_id='{$assets['ass_id']}' AND ass_deleted=0");
                    if($count > 0)
                    {
                        $_SESSION['msg'] = "{$CMS->lang['ass_move_error']}";
                        return false;
                    }
                }
            }

            //Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);
            
        }

    public function getNumberAssets($ass_key='')
    {
    	global $CMS, $DB;

    	if($ass_key)
    	{
    		$sql = $DB->query("SELECT COUNT(ass_id) as number_asset, store_id FROM ".root_table."assets WHERE ass_deleted = 0 AND is_available = 1 AND ass_key = '{$ass_key}' AND parent_id = 0 ");
    		return $DB->fetch_array($sql);
    	}else
    	{
    		return 0;
    	}
    }

    public function updateIsAvailable($data=array(), $type = 0, $store_from = 0, $store_to = 0)
    {
    	global $CMS, $DB;

    	// is_available:
    	// 1: Có giá trị
    	// 2: Đang chờ
    	// 3: Đã bán
     
    	if(is_array($data))
    	{
    		// Xuat ban cho NCC va KH, xuất vì lý do khác
    		if($type == 1)
    		{ 
	    		foreach ($data as $key => $value) 
	    		{ 
	    			 
	    			if($value['ass_quantity']<= 0) { continue; }
	    			// SQL SELECT TÀI SẢN CẦN UPDATE
	    			$sql = $DB->query("SELECT ass_id FROM ".root_table."assets WHERE ass_deleted = 0 AND is_available = 1 AND ass_key = '{$value['ass_key']}' ORDER BY ass_id ASC LIMIT {$value['ass_quantity']} ");
	    			if($DB->num_rows($sql) > 0)
	    			{
	    				while ($result = $DB->fetch_array($sql)) 
	    				{
	    					$DB->query("UPDATE ".root_table."assets SET is_available = '3' WHERE parent_id = '{$result['ass_id']}' OR ass_id = '{$result['ass_id']}' ");
	    				}

                        //Clear cache
                        $CMS->class->cache->mdelete($this->cache_prefix);
	    			}
	    		}
    		}elseif($type == 2)
    		{
    			// Xuat chuyen salon
    			$store_id = $store_from;
    			$store_id_to = intval($store_to);
    			foreach ($data as $key => $value) 
	    		{
	    			if($value['ass_quantity']<= 0) { continue; }
	    			// SQL SELECT TÀI SẢN CẦN UPDATE
	    			$sql = $DB->query("SELECT * FROM ".root_table."assets WHERE ass_deleted = 0 AND is_available = 1 AND ass_key = '{$value['ass_key']}' AND store_id = '{$store_id}' ORDER BY ass_id ASC LIMIT {$value['ass_quantity']} ");
	    			 
	    			if($DB->num_rows($sql) > 0)
	    			{
	    				while ($result = $DB->fetch_array($sql)) 
	    				{
	    					$data_sub = array();
		    				// Get thong tin subitem
		    				$sql_sub = $DB->query("SELECT * FROM ".root_table."assets WHERE ass_deleted = 0 AND parent_id = '{$result['ass_id']}'");
		    				if($DB->num_rows($sql_sub) > 0)
		    				{
		    					while ($result2 = $DB->fetch_array($sql_sub)) 
		    					{
		    						$data_sub['sub_name'][] = $result2['ass_name'];
		    						$data_sub['sub_price'][] = $result2['ass_price'];
		    						$data_sub['sub_warranty'][] = $result2['ass_warranty'];
		    						// Tạo key
		    						$sub_key = MD5("{$result2['ass_name']}_{$store_id_to}_{$result2['shi_id']}_{$result2['ass_price']}_{$result2['product_id']}_{$result2['ass_warranty']}");
		    						// Chuyển salon thằng con trước
		    						$DB->query("UPDATE ".root_table."assets SET ass_key = '{$sub_key}', store_id = '{$store_id_to}' WHERE ass_key = '{$result2['ass_key']}' AND ass_deleted = 0 AND parent_id = '{$result['ass_id']}'");
		    					}
		    				}

		    				$sub_hash = json_encode($data_sub['sub_name']).json_encode($data_sub['sub_price']).json_encode($data_sub['sub_warranty']);
		    				
		    				$ass_key = MD5("{$result['ass_name']}_{$store_id_to}_{$result['shi_id']}_{$result['ass_price']}_{$result['product_id']}_{$result['ass_warranty']}_{$sub_hash}");

		    				// CHUYỂN KHO THẰNG CHA
		    				// print "UPDATE ".root_table."assets SET ass_key = '{$ass_key}', store_id = '{$store_id_to}' WHERE ass_id = '{$result['ass_id']}' AND ass_deleted = 0 <br/>";
		    				$DB->query("UPDATE ".root_table."assets SET ass_key = '{$ass_key}', store_id = '{$store_id_to}' WHERE ass_id = '{$result['ass_id']}' AND ass_deleted = 0");

                            //Clear cache
                            $CMS->class->cache->mdelete($this->cache_prefix);
		    				
	    				}// End while result
	    				
	    			}

	    		}// End if type
	    		// exit;
    		}
    	}
    }

    function update_asset_key($old, $new)
    {
        global $DB, $CMS;
        if(!$old || !$new) return false;

        //Store request
        $sql = "UPDATE ".root_table."store_request SET request_product = REPLACE(request_product, '\"ass_key\":\"{$old}\"', '\"ass_key\":\"{$new}\"') WHERE request_product LIKE '%\"ass_key\":\"{$old}\"%'";

        $DB->query($sql);

        //Returns
        $sql = "UPDATE ".root_table."returns SET ret_assets = REPLACE(ret_assets, '\"ass_key\":\"{$old}\"', '\"ass_key\":\"{$new}\"') WHERE ret_assets LIKE '%\"ass_key\":\"{$old}\"%'";

        $DB->query($sql);

        //Transaction item
        $sql = "UPDATE ".root_table."transaction_item SET ass_key = '{$new}' WHERE ass_key='{$old}'";

        $DB->query($sql);

        //Order item
        $sql = "UPDATE ".root_table."order_item SET ass_key = '{$new}' WHERE ass_key='{$old}'";

        //Clear cache
        $CMS->class->cache->mdelete($CMS->store_request->cache_prefix);
        $CMS->class->cache->mdelete($CMS->returns->cache_prefix);
        $CMS->class->cache->mdelete($CMS->transaction_terms->cache_prefix);
        $CMS->class->cache->mdelete('order_item');

        $DB->query($sql);
    }

    function check_stockproduct_allstore($product_id = "")
    {
    	global $CMS, $DB;
    	$sql = $DB->query("SELECT store_id, count(ass_name) as stock FROM ".root_table."assets WHERE ass_deleted = 0 AND is_available = 1 AND product_id = '{$product_id}'  AND parent_id=0 GROUP BY store_id, ass_key ");
 
    	$output = array();
    	if($DB->num_rows($sql) > 0)
    	{
    		while ($data = $DB->fetch_array($sql)) {
    			# code...
    			$store = $CMS->store->get_info($data['store_id']);
    			$data['store_name'] = $store['store_name'];
    			$output[] = $data;
    		}
    		 
    	}
    	return $output;
    }
    

}
?>