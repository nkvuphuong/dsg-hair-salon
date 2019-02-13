<?php
use core\ezy;
use lib\date;
use lib\input;
use models\download;
if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}



$CMS->order = new class_order;

class class_order {

	public $CMS = "";
	
	/**
	 * @param @record_cnt
	 *		The order number of Data
	 */
	
	public $record_cnt = 0;
	
	/**
	 * @param @arrange_data
	 *		Arrange Data, using for re-order the listing
	 */
	
	public $arrange_data = "";
	
	/**
	 * @param @sql_query
	 *		The SQL Query for listing Data
	 */
	 
	public $sql_query = "";

	/**
	 * @param @sql_add
	 *		The additional SQL for $sql_query
	 */

	public $sql_add = "";
	
	/**
	 * @param $control
	 *		0 for no control, 1 for has control, DONT CHANGE the default value
	 */
	
	public $control = 0;
	
	/**
	 * @param $action_control
	 *		HTML action control
	 */
	
	public $action_control = "";
	
	/**
	 * @param $html
	 *		The templates
	 */
	 
	public $html;
	
	/**
	 * @param $per_page
	 *		Per page
	 */

	public $per_page = 20;
	
	/**
	 * @param $prefix_html
	 *		For page link
	 */
	public $suffix_html = "";
	
	/**
	 * @param $order_project
	 *		Use for multiple projects
	 */
	public $total = 0;
	public $order_project = "";
	
	
	/**
	 * @param $temp_order
	 *		Place order: Order List
	 */
	 
	public $temp_order = array();
	
	/**
	 * @param $temp_count
	 *		Place order: Order Count
	 */
	 
	public $temp_count = 1;
	
	/**
	 * @param $per_page
	 *		Order per page
	 */
	 public $token_key = "";
	 
	//===========================================================================
	//  LISTING DATA
	//===========================================================================
	
	public function loadhtml()
	{
		global $CMS;
		
		if ( !isset($this->html) )
		{	
			if ( $CMS->vars['is_admin_module'] )
			{
				$this->html = $CMS->class->template->load_simple(\core\ezy::$app_dir."/modules/order/templates/skin_order.php");
			}
			else
			{
				$this->html = $CMS->class->template->load_template("skin_order");
			}
		}
	}
 
	public function html()
	{
		global $CMS, $DB, $member;

		$this->loadhtml();
 
		// Display Header
		$output .= $this->html->order_header();
		
		if ( $DB->num_rows( $CMS->order->sql_query ) > 0 )
		{
			while( $result = $DB->fetch_array( $CMS->order->sql_query ) )
			{
				// Convert info
				$result = $CMS->order->convertvalue($result);
				
				// Display Middle
				$output .= $this->html->order_middle($result);
			}
		}
		else
		{
			// Display No data
			$output .= $this->html->order_none();

			// No data
			$CMS->is_error = 1;
		}
		
		// Display
		$output .= $this->html->order_footer();
		
		// If the page is giving no data
		if ( $CMS->input['page'] > 1 && $CMS->is_error == 1)
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order");
		}
		
		return $output;
	}
	
	//===========================================================================
	//  AUTO RUN
	//===========================================================================

	public function auto_run()
	{
		global $CMS, $DB, $member;

		$this->loadhtml();

		//-----------------------------------------------------------
		// ACTION CONTROLLER
		//-----------------------------------------------------------
		
		if ( $CMS->class->cache->check("user_{$member['user_id']}_order_controller_{$CMS->vars['default_language']}") )
		{
			$CMS->vars['action_controller'] = $CMS->class->cache->load("user_{$member['user_id']}_order_controller_{$CMS->vars['default_language']}");
		}
		else
		{
			$data = "";

			// Check permission to Delete
			if ( $CMS->permit["order_delete"] == true )
			{
				$data .= "<option value='delete_all'>{$CMS->lang['delete_seleted_order']}</option>";
				$this->control = 1;
			}
			
			$data = $this->control == 1 ? "<option value=''>{$CMS->lang['select_action']}</option>" . $data : "";

			$CMS->class->cache->save("user_{$member['user_id']}_order_controller_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
		}
		
		if ( $CMS->vars['action_controller']  OR $CMS->permit["order_search"] == 1 )
		{
			$this->action_control = $this->html->order_control();
		}
		
	 
	}

	//===========================================================================
	//  DATA
	//===========================================================================

	public function defaultvalue($data)
	{
		global $CMS;

		$data['order_display'] = $data['order_display'] ? $data['order_display'] : 1;

		return $data;
	}
	 
	public function searchvalue($data)
	{
		global $CMS, $DB;

		// Convert Register to GMT
		$data['ord_time'] = $CMS->class->date->date_format( $data['ord_time'], 0 );

		// Replace the Status
		$data['ord_display'] = $CMS->lang["display_{$data['ord_display']}"];

		// Replace the IS Active Post
		$data['ord_active'] = $CMS->lang["ord_active_color_{$data['ord_active']}"];	
		
 
		$data['cat_id'] = $cat['cat_name'];

		return $data;
	}
	
	//===========================================================================
	//  INFO
	//===========================================================================
	
	public function get_info( $record_id = 0, $field_name = "", $sql_add = "")
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "order" )
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

		// Check type
		//$sql_add .= ($this->order_project ? " AND order_project='{$this->order_project}' " : "");
		$sql = $DB->query("SELECT * FROM ".root_table."order WHERE {$sql_add} (ord_id='{$record_id}' OR ord_name='{$record_id}') AND ord_deleted=0  ORDER BY ord_id DESC LIMIT 1");

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

    public function get_info_by_transaction( $record_id = 0, $field_name = "")
    {
        global $CMS, $DB, $member;

        if ( ! $record_id AND $CMS->input['site'] == "order" )
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

        // Check type
        //$sql_add .= ($this->order_project ? " AND order_project='{$this->order_project}' " : "");
        $sql = $DB->query("SELECT * FROM ".root_table."order WHERE transaction_id='{$record_id}' AND ord_deleted=0  ORDER BY ord_id DESC LIMIT 1");

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
	
	public function check_exist( $field, $value = "", $except_value = "" )
	{
		global $CMS, $DB, $member;
		
		if ( ! $field )
		{
			return true;
		}
		
		if ( $except_value )
		{
			$DB->query("SELECT * FROM ".root_table."order WHERE {$field}='{$value}' AND {$field}!='{$except_value}' AND order_deleted=0");
		}
		else
		{
			$DB->query("SELECT * FROM ".root_table."order WHERE {$field}='{$value}' AND order_deleted=0");
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
	public function create_session_order()
	{
		global $CMS, $DB, $member;

				$item_asset = $item_product = array();
			 	// Producr
				$product_id = isset($CMS->input['product_id']) && is_array($CMS->input['product_id']) ? array_values($CMS->input['product_id']) : [];
				$product_name = isset($CMS->input['product_name']) && is_array($CMS->input['product_name']) ? array_values($CMS->input['product_name']) : [];
				$product_description = isset($CMS->input['product_description']) && is_array($CMS->input['product_description']) ? array_values($CMS->input['product_description']) : [];
				$product_cycle_type = isset($CMS->input['product_cycle_type']) && is_array($CMS->input['product_cycle_type']) ? array_values($CMS->input['product_cycle_type']) : [];

				$product_cycle = isset($CMS->input['product_cycle']) && is_array($CMS->input['product_cycle']) ? array_values($CMS->input['product_cycle']) : [];
				$product_quantity = isset($CMS->input['product_quantity']) && is_array($CMS->input['product_quantity']) ? array_values($CMS->input['product_quantity']) : [];
				$product_price = isset($CMS->input['product_price']) && is_array($CMS->input['product_price']) ? array_values($CMS->input['product_price']) : [];
				$product_old_price = isset($CMS->input['product_old_price']) && is_array($CMS->input['product_old_price']) ? array_values($CMS->input['product_old_price']) : [];
				# Bo sung gia cong them cho dịch vụ giftcard
				$product_price_add = isset($CMS->input['product_price_add']) && is_array($CMS->input['product_price_add']) ? array_values($CMS->input['product_price_add']) : [];

				$product_tax = isset($CMS->input['product_tax']) && is_array($CMS->input['product_tax']) ? array_values($CMS->input['product_tax']) : [];
				$product_tri_id = isset($CMS->input['product_tri_id']) && is_array($CMS->input['product_tri_id']) ? array_values($CMS->input['product_tri_id']) : [];

				$product_discount_type = isset($CMS->input['product_discount_type']) && is_array($CMS->input['product_discount_type']) ? array_values($CMS->input['product_discount_type']) : [];
				$product_discount_value = isset($CMS->input['product_discount_value']) && is_array($CMS->input['product_discount_value']) ? array_values($CMS->input['product_discount_value']) : [];

                $product_commission_type = isset($CMS->input['product_commission_type']) && is_array($CMS->input['product_commission_type']) ? array_values($CMS->input['product_commission_type']) : [];
                $product_commission_value = isset($CMS->input['product_commission_value']) && is_array($CMS->input['product_commission_value']) ? array_values($CMS->input['product_commission_value']) : [];
                $product_booking_time = isset($CMS->input['product_booking_time']) && is_array($CMS->input['product_booking_time']) ? array_values($CMS->input['product_booking_time']) : [];
                $product_staff = isset($CMS->input['product_staff']) && is_array($CMS->input['product_staff']) ? array_values($CMS->input['product_staff']) : [];

                $key = input::get('key',[]);
                $parent_key = input::get('parent_key',[]);

                $var_id = isset($CMS->input['var_id']) && is_array($CMS->input['var_id']) ? array_values($CMS->input['var_id']) : [];
                $var_content = isset($CMS->input['var_content']) && is_array($CMS->input['var_content']) ? array_values($CMS->input['var_content']) : [];

				$product_total = 0;
				$count = count($product_id);

				for($i=0; $i<$count; $i++)
				{	
					$price = 0;
					if( $product_id[$i] != "" AND $product_name[$i] != "")
					{

                            $item_product[$i]['product_discount_type'] = isset($product_discount_type[$i]) ? intval($product_discount_type[$i]) : 0;
                            $item_product[$i]['product_discount_value'] = isset($product_discount_value[$i]) ? floatval($product_discount_value[$i]) : 0;

                            $item_product[$i]['product_commission_type'] = isset($product_commission_type[$i]) ? intval($product_commission_type[$i]) : 0;
                            $item_product[$i]['product_commission_value'] = isset($product_commission_value[$i]) ? floatval($product_commission_value[$i]) : 0;

							$item_product[$i]['product_id'] = isset($product_id[$i]) ? $product_id[$i] : 0;
							$item_product[$i]['product_name'] = isset($product_name[$i]) ? $product_name[$i] : '';
							$item_product[$i]['product_description'] = isset($product_description[$i]) ? $product_description[$i] : '';
							$item_product[$i]['product_cycle'] = isset($product_cycle[$i]) ? $product_cycle[$i] : 1;
							$item_product[$i]['product_tri_id'] = isset($product_tri_id[$i]) ? $product_tri_id[$i] : 0;
							$item_product[$i]['product_cycle_type'] = isset($product_cycle_type[$i]) ? $product_cycle_type[$i] : 0;
							if(isset($product_cycle_type[$i]) && $product_cycle_type[$i] == 0)//Neu loai mot lan
							{
								$item_product[$i]['product_cycle'] = 1;
							}


							if( !isset($product_quantity[$i]) OR $product_quantity[$i] == 0 OR $product_quantity[$i] == "")
							{
								$product_quantity[$i] = 1;
							}

							if(!isset($product_cycle[$i]) OR $product_cycle[$i] == 0 OR $product_cycle[$i] == "")
							{
								$product_cycle[$i] = 1;
							}

							$item_product[$i]['product_quantity'] = isset($product_quantity[$i]) ? $product_quantity[$i] : 1;
							$item_product[$i]['product_price'] = isset($product_price[$i]) ? $product_price[$i] : 0;
							$item_product[$i]['product_old_price'] = isset($product_old_price[$i]) ? $product_old_price[$i] : 0;
							$item_product[$i]['product_price_add'] = isset($product_price_add[$i]) ? intval($product_price_add[$i]) : 0;
							$item_product[$i]['product_tax'] = isset($product_tax[$i]) ? $product_tax[$i] : 0;
							$item_product[$i]['product_booking_time'] = isset($product_booking_time[$i]) ? $product_booking_time[$i] : 0;
							$item_product[$i]['product_staff'] = isset($product_staff[$i]) ? $product_staff[$i] : 0;

							$item_product[$i]['var_id'] = isset($var_id[$i]) ? $var_id[$i] : 0;
							$item_product[$i]['var_content'] = isset($var_content[$i]) ? $var_content[$i] : [];

                            $item_product[$i]['key'] = input::arrayValue($key, $i);
                            $item_product[$i]['parent_key'] = input::arrayValue($parent_key, $i);

							$price = $item_product[$i]['product_price'] + $item_product[$i]['product_price_add'];

							$total = (( $item_product[$i]['product_cycle'] * $price) + (( $item_product[$i]['product_cycle'] * $price)  * $item_product[$i]['product_tax'] /100)) * $item_product[$i]['product_quantity'];
			 
							$product_total += $total;
					}
				}

				// Assets
				$asset_id = isset($CMS->input['ass_id']) && is_array($CMS->input['ass_id']) ? array_values($CMS->input['ass_id']) : [];
				$asset_key = isset($CMS->input['ass_key']) && is_array($CMS->input['ass_key']) ? array_values($CMS->input['ass_key']) : [];
				$asset_name = isset($CMS->input['ass_name']) && is_array($CMS->input['ass_name']) ? array_values($CMS->input['ass_name']) : [];
			 
				$asset_code = isset($CMS->input['ass_code']) && is_array($CMS->input['ass_code']) ? array_values($CMS->input['ass_code']) : [];
				$asset_quantity = isset($CMS->input['ass_quantity']) && is_array($CMS->input['ass_quantity']) ? array_values($CMS->input['ass_quantity']) : [];
				$asset_price = isset($CMS->input['ass_price']) && is_array($CMS->input['ass_price']) ? array_values($CMS->input['ass_price']) : [];
				$asset_tax = isset($CMS->input['ass_tax']) && is_array($CMS->input['ass_tax']) ? array_values($CMS->input['ass_tax']) : [];
				$asset_tri_id = isset($CMS->input['asset_tri_id']) && is_array($CMS->input['asset_tri_id']) ? array_values($CMS->input['asset_tri_id']) : [];

                $asset_discount_type = isset($CMS->input['ass_discount_type']) && is_array($CMS->input['ass_discount_type']) ? array_values($CMS->input['ass_discount_type']) : [];
                $asset_discount_value = isset($CMS->input['ass_discount_value']) && is_array($CMS->input['ass_discount_value']) ? array_values($CMS->input['ass_discount_value']) : [];

                $asset_booking_time = isset($CMS->input['ass_booking_time']) && is_array($CMS->input['ass_booking_time']) ? array_values($CMS->input['ass_booking_time']) : [];
                $asset_staff = isset($CMS->input['ass_staff']) && is_array($CMS->input['ass_staff']) ? array_values($CMS->input['ass_staff']) : [];

				$asset_total = 0;
				$count = count($asset_key);
				 

				for($i=0; $i<$count; $i++)
				{
					if( $asset_key[$i] != "" AND $asset_name[$i] != "")
					{
                        $item_asset[$i]['asset_discount_type'] = intval($asset_discount_type[$i]);
                        $item_asset[$i]['asset_discount_value'] = floatval($asset_discount_value[$i]);

						$item_asset[$i]['asset_key'] = $asset_key[$i];
						$item_asset[$i]['asset_name'] = $asset_name[$i];
						$item_asset[$i]['asset_code'] = $asset_code[$i];
						$item_asset[$i]['asset_quantity'] = $asset_quantity[$i];
						if($asset_quantity[$i] == 0 OR $asset_quantity[$i] == "")
						{
							$asset_quantity[$i] = 1;
						}
		 

						$item_asset[$i]['asset_quantity'] = $asset_quantity[$i];
						$item_asset[$i]['asset_price'] = $asset_price[$i];
						$item_asset[$i]['asset_tax'] = $asset_tax[$i];
						$item_asset[$i]['asset_tri_id'] = $asset_tri_id[$i];
						$item_asset[$i]['asset_booking_time'] = $asset_booking_time[$i];
						$item_asset[$i]['asset_staff'] = $asset_staff[$i];
						$total = (  $item_asset[$i]['asset_price'] + (  $item_asset[$i]['asset_price'] *$item_asset[$i]['asset_tax'] /100)) * $item_asset[$i]['asset_quantity'];
						$asset_total += $total;
					}
				}

 
	 		$order_total = $product_total + $asset_total;
			$item = array_merge($item_product, $item_asset);

			$data_output = array($item, $order_total);
			return $data_output;
	}

	public function unset_asset_item()
	{
		global $CMS, $DB, $member;
		unset($CMS->input['ass_id']);
		unset($CMS->input['ass_key']);
		unset($CMS->input['ass_name']); 
		unset($CMS->input['ass_code']);
		unset($CMS->input['ass_quantity']);
		unset($CMS->input['ass_price']);
		unset($CMS->input['ass_tax']);
		unset($CMS->input['asset_tri_id']);

	}

	//===========================================================================
	// QUICK ADD => orded from web 
	//===========================================================================
	
	public function quick_add($data = array())
	{
		global $CMS, $DB, $member;
 		foreach ($data as $key => $value) {
 			# code...
 			$CMS->input[$key] = $value;
 		}

 		return $CMS->order->add("quick_add");

 	}
	//===========================================================================
	//  ADD
	//===========================================================================

    public function add($type = "", $is_api = 0)
    {
        global $CMS, $DB, $member;

        $sql_columns = "";
        $sql_values = "";

        // User input
        unset($_SESSION['order_item']);
        // Default service type = 0; // Product
        $service_type = isset($CMS->input['service_type']) ? intval($CMS->input['service_type']) : 0;
        // Booking dành cho quy trình order cũ
        $booking_hours = isset($CMS->input['booking_hours']) ? $CMS->input['booking_hours'] : null;
        $booking_date = (isset($CMS->input['booking_date']) && $CMS->input['booking_date']) ? $CMS->class->date->date2time($CMS->input['booking_date']) : 0;
        // End booking quy trình cũ

        $cus_id = isset($CMS->input['cus_id']) ? intval($CMS->input['cus_id']) : 0;
        $user_id = isset($CMS->input['user_id']) ? intval($CMS->input['user_id']) : 0;
        $store_id = isset($CMS->input['store_id']) ? intval($CMS->input['store_id']) : 0;
        $ord_note = $CMS->class->editor->input(\lib\input::get('ord_note'),'text');
        // $ord_note = isset($CMS->input['ord_note']) ? $CMS->input['ord_note'] : '';
       	// $ord_discount_type = intval($CMS->input['trx_discount_type']);
       	// $ord_discount = $CMS->input['trx_discount_value'];
        $is_send_mail = isset($CMS->input['is_send_mail']) ? intval($CMS->input['is_send_mail']) : 0;
        $ord_content = isset($CMS->input['ord_content']) ? $CMS->input['ord_content'] : '';
        $is_shipping = isset($CMS->input['is_shipping']) ? intval($CMS->input['is_shipping']) : 0;
        $confirm_paid = isset($CMS->input['confirm_paid']) ? intval($CMS->input['confirm_paid']) : 0;
        $ord_status_custom = isset($CMS->input['ord_status_custom']) ? intval($CMS->input['ord_status_custom']) : 0;
        $tracking_code = $CMS->input['tracking_code'];

        $ship = array();
        if( $is_shipping == 1 )
        {
        	// if(intval($CMS->vars['enable_shipping']) == 0)
        	// {
	        // 	// Shipping quy trình cũ
	        //     $ship['ship_receive_name'] = isset($CMS->input['ship_receive_name']) ? $CMS->input['ship_receive_name'] : '';
	        //     $ship['ship_phone'] = isset($CMS->input['ship_phone']) ? $CMS->input['ship_phone'] : '';
	        //     $ship['ship_address'] = isset($CMS->input['ship_address']) ? $CMS->input['ship_address'] : '';
	        //     $ship['ship_location'] = isset($CMS->input['ship_location']) ? $CMS->input['ship_location'] : null;
	        //     $ship['ship_code'] = isset($CMS->input['ship_code']) ? $CMS->input['ship_code'] : '';
	        //     $ship['ship_weight'] = isset($CMS->input['ship_weight']) ? $CMS->input['ship_weight'] : null;
	        //     $ship['ship_long'] = isset($CMS->input['ship_long']) ? $CMS->input['ship_long'] : '';
	        //     $ship['ship_wide'] = isset($CMS->input['ship_wide']) ? $CMS->input['ship_wide'] : '';
	        //     $ship['ship_height'] = isset($CMS->input['ship_height']) ? $CMS->input['ship_height'] : '';
	        //     $ship['ship_service_type'] = isset($CMS->input['ship_service_type']) ? $CMS->input['ship_service_type'] :  null;
	        //     $ship['ship_deliver'] = isset($CMS->input['ship_deliver']) ? $CMS->input['ship_deliver'] : null;
	        //     $ship['ship_deliver_fee'] = isset($CMS->input['ship_deliver_fee']) ? $CMS->input['ship_deliver_fee'] : null;
	        // }
	        // else
	        {
	        	// Quy trình mới
	        	
	        	// Billing
	            $ship['bill_first_name'] = isset($CMS->input['bill_first_name']) ? $CMS->input['bill_first_name'] : '';
	            $ship['bill_last_name']  = isset($CMS->input['bill_last_name']) ? $CMS->input['bill_last_name'] : '';
	            $ship['bill_full_name']  = $ship['bill_first_name']. " " .$ship['bill_last_name'];
	            $ship['bill_email']      = isset($CMS->input['bill_email']) ? $CMS->input['bill_email'] : '';
	            $ship['bill_phone']      = isset($CMS->input['bill_phone']) ? $CMS->input['bill_phone'] : '';
	            $ship['bill_company']    = isset($CMS->input['bill_company']) ? $CMS->input['bill_company'] : '';
	            $ship['bill_address']    = isset($CMS->input['bill_address']) ? $CMS->input['bill_address'] : '';
	            $ship['bill_address2']   = isset($CMS->input['bill_address2']) ? $CMS->input['bill_address2'] : '';
	            $ship['bill_city']       = isset($CMS->input['bill_city']) ? $CMS->input['bill_city'] : '';
	            $ship['bill_province']   = isset($CMS->input['bill_province']) ? $CMS->input['bill_province'] : '';
	            $ship['bill_zipcode']    = isset($CMS->input['bill_zipcode']) ? $CMS->input['bill_zipcode'] : '';
	            $ship['bill_country']    = isset($CMS->input['bill_country']) ? $CMS->input['bill_country'] : '';
	            if( strtoupper($ship['bill_country']) == 'US' )
	            {
	            	$ship['bill_province']   = isset($CMS->input['bill_state']) ? $CMS->input['bill_state'] : '';
	            }

	            // Shipping
	            $ship['ship_deliver']    = isset($CMS->input['ship_deliver']) ? $CMS->input['ship_deliver'] : null;
	            $ship['ship_first_name'] = isset($CMS->input['ship_first_name']) ? $CMS->input['ship_first_name'] : '';
	            $ship['ship_last_name']  = isset($CMS->input['ship_last_name']) ? $CMS->input['ship_last_name'] : '';
	            $ship['ship_full_name']  = $ship['ship_first_name']. " " .$ship['ship_last_name'];
	            $ship['ship_email']      = isset($CMS->input['ship_email']) ? $CMS->input['ship_email'] : '';
	            $ship['ship_phone']      = isset($CMS->input['ship_phone']) ? $CMS->input['ship_phone'] : '';
	            $ship['ship_company']    = isset($CMS->input['ship_company']) ? $CMS->input['ship_company'] : '';
	            $ship['ship_address']    = isset($CMS->input['ship_address']) ? $CMS->input['ship_address'] : '';
	            $ship['ship_address2']   = isset($CMS->input['ship_address2']) ? $CMS->input['ship_address2'] : '';
	            $ship['ship_city']       = isset($CMS->input['ship_city']) ? $CMS->input['ship_city'] : '';
	            $ship['ship_province']   = isset($CMS->input['ship_province']) ? $CMS->input['ship_province'] : '';
	            $ship['ship_zipcode']    = isset($CMS->input['ship_zipcode']) ? $CMS->input['ship_zipcode'] : '';
	            $ship['ship_country']    = isset($CMS->input['ship_country']) ? $CMS->input['ship_country'] : '';
	            if( strtoupper($ship['ship_country']) == 'US' )
	            {
	            	$ship['ship_province']   = isset($CMS->input['ship_state']) ? $CMS->input['ship_state'] : '';
	            }
	        }

        }

        $shipping_info = json_encode($ship, JSON_UNESCAPED_UNICODE);

        // Use for service
        if ($service_type == 1) {
            if ($booking_hours == "" and intval($CMS->input['booking_form_email']) == 1) {
                $_SESSION['msg'] = "{$CMS->lang['booking_hours_not_empty']}";
                return false;
            }
            //Unset value asset sub-item
            $this->unset_asset_item();

        } else {
            if ($type == "") {
                if (($store_id == "" OR $store_id <= 0) AND $CMS->vars['addon_goods_enable'] == 1) {
                    //$_SESSION['error_msg']  = "{$CMS->lang['store_not_empty']}"; return false;
                }
            }

        }

        $ord_status = isset($CMS->input['ord_status']) ? intval($CMS->input['ord_status']) : 0;
        $payment_method = isset($CMS->input['order_payment_method']) ? intval($CMS->input['order_payment_method']) : 0;

        $account_id = isset($CMS->input['account_id']) ? intval($CMS->input['account_id']) : 0;

        if ($type == "") {
            if ($payment_method == 0) {
                $account_id = 0;
            } elseif ($payment_method == 1)// Chọn bank
            {
                if ($account_id == "" OR $account_id == 0) {
                	// Check for return output format API
                	if($is_api == 1)
                	{
                		$api_output = array("error", "bank_err");
						return $api_output;

                	}else
                	{
                		$_SESSION['error_msg'] = "{$CMS->lang['bank_err']}";
                    	return false;
                	}
                  
                }
            }
        }

        $ord_is_vat = isset($CMS->input['ord_is_vat']) ? intval($CMS->input['ord_is_vat']) : 0;

        // Check input
        if( $type == "" )
        {
            // Order quick add by pass check cus id
            if ($cus_id == "" OR !$cus_id) 
            {
            	if($is_api == 1)
            	{
            		$api_output = array("error", "order_cus_err");
					return $api_output;
            	}
            	else
            	{
            		// Not check: Order for Guest then cus_id 0
            		// $_SESSION['error_msg'] = "{$CMS->lang['order_cus_err']}";
                	// return false;
            	}
            }
        }

        $CMS->customer->update_last_action_time($cus_id);
        list($item, $order_total) = $this->create_session_order();

        if (!is_array($item) OR count($item) <= 0) {
        	if($is_api == 1)
        	{
        		$api_output = array("error", "order_total_err");
				return $api_output;

        	}else
        	{
        		$_SESSION['error_msg'] = isset($CMS->lang['order_total_err']) ? "{$CMS->lang['order_total_err']}" : '';
            	return false;
        	}
            
        }

        $_SESSION['order_item'] = $item;

        $ord_item = json_encode($item, JSON_UNESCAPED_UNICODE);

        // Get quantity items for report
        $quantity_item = 0;
        $amount = 0;
        foreach ($_SESSION['order_item'] as $key => $subitem) {
            $quantity_item += isset($subitem['product_quantity']) ? $subitem['product_quantity'] : 0;
            $quantity_item += isset($subitem['asset_quantity']) ? $subitem['asset_quantity'] : 0;
            $subitem['item_price'] =  isset($subitem['item_price']) ? $subitem['item_price'] : 0;
            $subitem['item_cycle'] =  isset($subitem['item_cycle']) ? $subitem['item_cycle'] : 1;
            $amount += $subitem['item_price'] * $subitem['item_cycle'];
        }

        $quantity_item = !$quantity_item ? 1 : $quantity_item;

        // ThamLV d22-6-2018: Detect store follow first product if not choose store
        if( ! $store_id )
        {
        	$store_id = $this->getDefaultStoreFollowProducts($item);
        }

        // Shipping service
        // Order shipping method: 0:Standard; 1: Express 
        // Order shipping location: 0: Domestics , 1: International 
        $ord_shipping_method = isset($CMS->input['ord_shipping_method']) ? $CMS->input['ord_shipping_method'] : (isset($CMS->input['ship_method']) ? $CMS->input['ship_method'] : 1);
        $ord_shipping_method = $ord_shipping_method == 0 ? 0 : 1;

        $ord_shipping_location = isset($CMS->input['ord_shipping_location']) ? $CMS->input['ord_shipping_location'] : ((isset($CMS->input['ship_country']) AND isset($CMS->vars['default_shiping_location']) AND strtoupper($CMS->vars['default_shiping_location']) == strtoupper($CMS->input['ship_country'])) ? 0 : 1);
        $ord_shipping_location = $ord_shipping_location == 0 ? 0 : 1;

        // Calculate money
        $output_total = $CMS->transactions->calculate();
        $ord_amount = $CMS->input['trx_amount'] = $output_total['subtotal'];
        $ord_total = $output_total['total'];
        $ord_tax = $CMS->input['trx_tax'] = $output_total['tax'];
        $total_discount = $CMS->input['trx_total_discount'] = $output_total['discount'];
        $commission = $output_total['commission']*1;
        $ord_fee_shipping = $output_total['fee_shipping'];
        
        $CMS->input['trx_total'] = $ord_total - $ord_fee_shipping; // Transaction not contain shipping fee

        // Email owned order
        if( $cus_id )
        {
        	$ord_email = $CMS->customer->getInfo($cus_id, 'cus_email');	
        }
        else
        {
        	$ord_email = isset($CMS->input['cus_email']) ? trim($CMS->input['cus_email']) : (isset($CMS->input['bill_email']) ? trim($CMS->input['bill_email']) : '');
        }

        // Add transaction
        $trx_id = $CMS->transactions->add_transaction();

        // LHL-2018-06-20: Prevent add order if transaction failed
        if ( ! $trx_id ){
            if($is_api == 1)  {
                $api_output = array("error", "Error: Transaction id not found");
                return $api_output;
            } else
            {
                $_SESSION['error_msg'] = "Error: Transaction id not found";
                return false;
            }
        }

        // Neu confirm pay: xac nhan da thanh toan inv => update invoice ->paid
        if ($confirm_paid == 1) {
            $CMS->transactions->createReceivedPayment($trx_id);
        }

        $ord_booking_phone = \lib\input::get('booking_phone');

        if($ord_booking_phone !== null) {
            $sql_columns = ",ord_booking_phone";
            $sql_values = ",'{$ord_booking_phone}'";
        }
 
        // Add order
        $sql = "INSERT INTO `" . root_table . "order` ( service_type, booking_hours, booking_date,   `ord_amount`,`ord_tax`, `ord_total`,  `ord_status` , `payment_method`, `payment_status`, `account_id`, `ord_item`, `ord_content`, `transaction_id`, `cus_id`,   `user_id`,  `ord_note`, `ord_time`, store_id, shipping_info, is_shipping,ord_quantity_items,ord_total_discount,ord_commission, ord_status_custom, tracking_code, ord_fee_shipping, ord_shipping_method, ord_shipping_location, ord_email {$sql_columns}) VALUES ( '{$service_type}', '{$booking_hours}', '{$booking_date}' ,'{$ord_amount}', '{$ord_tax}','{$ord_total}', '{$ord_status}', '{$payment_method}','{$confirm_paid}' , '{$account_id}', '{$ord_item}', '{$ord_content}' ,'{$trx_id}', '{$cus_id}', '{$user_id}','{$ord_note}','" . time() . "', '{$store_id}', '{$shipping_info}', '{$is_shipping}','{$quantity_item}','{$total_discount}', '{$commission}', '{$ord_status_custom}', '{$tracking_code}', '{$ord_fee_shipping}', '{$ord_shipping_method}', '{$ord_shipping_location}', '{$ord_email}' {$sql_values})";
        // p($sql); exit;
        $DB->query($sql);
 		
        $ord_id = $DB->last_insert_id();

        $generate_ord_id = "OD" . $ord_id;

        $DB->query("UPDATE " . root_table . "order SET ord_name = '{$generate_ord_id}' WHERE ord_id='{$ord_id}'  ");
        $DB->query("UPDATE " . root_table . "transaction SET ord_id = '{$ord_id}' WHERE trx_id='{$trx_id}'  ");
        
        // Insert address shipp bill
        if( $is_shipping == 1 AND $ord_id )
        {
        	$sp_time = time();
        	$sql_shipbill_address = "
        	INSERT INTO ".root_table."shipbill_address 
        	( ord_id, bill_first_name, bill_last_name, bill_full_name, bill_email, bill_phone, bill_company, bill_address, bill_address2, bill_city, bill_province, bill_zipcode, bill_country, ship_first_name, ship_last_name, ship_full_name, ship_email, ship_phone, ship_company, ship_address, ship_address2, ship_city, ship_province, ship_zipcode, ship_country, cus_id, sp_deliver, sp_time) 
        	VALUES 
        	('{$ord_id}', '{$ship['bill_first_name']}', '{$ship['bill_last_name']}', '{$ship['bill_full_name']}', '{$ship['bill_email']}', '{$ship['bill_phone']}', '{$ship['bill_company']}', '{$ship['bill_address']}', '{$ship['bill_address2']}', '{$ship['bill_city']}', '{$ship['bill_province']}', '{$ship['bill_zipcode']}', '{$ship['bill_country']}', '{$ship['ship_first_name']}', '{$ship['ship_last_name']}', '{$ship['ship_full_name']}', '{$ship['ship_email']}', '{$ship['ship_phone']}', '{$ship['ship_company']}', '{$ship['ship_address']}', '{$ship['ship_address2']}', '{$ship['ship_city']}', '{$ship['ship_province']}', '{$ship['ship_zipcode']}', '{$ship['ship_country']}', '{$cus_id}', '{$ship['ship_deliver']}', '{$sp_time}') 
        	";
        	// p($sql_shipbill_address); exit;
        	$DB->query($sql_shipbill_address);
        }

        // Get info
        $order = $this->get_info($generate_ord_id);

        // Insert comment
        $comment_content = $CMS->class->editor->input('comment_content');
        if($comment_content)
        {
			// Insert comment for customer
	        $module_id = $ord_id;
	    	$module_name = "order";
	    	$user_id = $member['user_id'];
	    	$comment_name = $CMS->lang['order_status_'.$ord_status];

	    	$comment_time = time();
	    	$comment_ip_address = $_SERVER['REMOTE_ADDR'];
	    	$comment_approved = 1;// Mặc định là duyệt
	    	$comment_hide = intval($CMS->input['send_notify']) == 1 ? 0 : 1; // 0: Hiện, 1: Ẩn 

	    	// Insert logs comment
	    	$DB->query("INSERT INTO ".root_table."comment (module_id, module_name, user_id, cus_id, comment_name, comment_content, comment_time, comment_ip_address, comment_approved, ord_status, ord_status_custom, comment_hide) VALUES ('{$module_id}', '{$module_name}', '{$user_id}', '{$cus_id}', '{$comment_name}', '{$comment_content}', '{$comment_time}', '{$comment_ip_address}', '{$comment_approved}', '{$ord_status}', '{$ord_status_custom}', '{$comment_hide}')");
	    	$comment_id = $DB->last_insert_id();
	    	if($comment_id)
	    	{
	    		// Create log
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['title_addlog_successful']} <b>#{$comment_id}</b>")."<br />";
				// Send email
				if($comment_hide == 0)
				{
					$data_comment = $CMS->comment->get_info($comment_id);
					$data_customer = $CMS->customer->getInfo($cus_id);
					$order_convert = $this->convertvalue($order);
					$this->sendNotifyCustomer($data_comment, $data_customer, $order_convert);
				}
	    	}else
	    	{
	    		$_SESSION["msg"] .= $CMS->class->logs->insert($CMS->lang['title_addlog_error'])."<br/>";
	    	}
        }

        // ADd order item
        $this->add_order_item($ord_id, $confirm_paid);
        $time_create = $CMS->class->date->date_format(time(), 1);
        // Create log
        $CMS->class->logs->key = "order_{$ord_id}";
        $_SESSION["msg"] = isset($_SESSION["msg"]) ? $_SESSION["msg"] : '';
        $_SESSION["msg"] .= isset($CMS->lang['create_order_success_log']) ? "{$CMS->lang['create_order_success_log']} <br />" : '';
        $_SESSION["msg"] .= isset($CMS->lang['create_order_success_date']) ? $CMS->class->logs->insert("{$CMS->lang['create_order_success_date']}<br />") : "";

        //Check lang
        $CMS->lang['can_see_detail'] = isset($CMS->lang['can_see_detail']) ? $CMS->lang['can_see_detail'] : "";
        $CMS->lang['view_detail'] = isset($CMS->lang['view_detail']) ? $CMS->lang['view_detail'] : "";
        $CMS->lang['or'] = isset($CMS->lang['or']) ? $CMS->lang['or'] : "";
        $CMS->lang['add_new_order'] = isset($CMS->lang['add_new_order']) ? $CMS->lang['add_new_order'] : "";

        $_SESSION["msg"] .= "{$CMS->lang['can_see_detail']} <a href='{$CMS->vars['root_domain']}/?site=order&act=show&id={$ord_id}'>{$CMS->lang['view_detail']}</a> {$CMS->lang['or']} <a href='{$CMS->vars['root_domain']}/?site=order&act=add'>{$CMS->lang['add_new_order']}</a>";
        

        // Check send mail
        if ($is_send_mail == 1) {
            $this->send_email_order($order);
        }
        unset($_SESSION['order_item']);

        if($is_api == 1)
    	{
    		$api_output = array("success", $order);
			return $api_output;

    	}else
    	{	
        	return $order;
        }

    }
	

	//===========================================================================
	//  Edit
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
 		$ord_id = $id = intval($CMS->input['id']);
 		$order = $CMS->order->get_info($CMS->input['id']);
 		if(! is_array($order))
 		{
 			$_SESSION['error_msg'] = "{$CMS->lang['order_edit_error']}";
 			return false;
 		}

 		// Check trang thai don hang , neu order da thanh toan roi, hoac cong nu, k cho edit
	 	if(	$order['payment_status'] > 0)
		{
			$_SESSION['error_msg'] = "{$CMS->lang['order_not_modify']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order");
		}

 		$CMS->class->logs->key = "order_{$id}";    
		$CMS->class->logs->old_data = $order;
		$CMS->class->logs->insert("order_{$id}");

		// User input
		unset($_SESSION['order_item']);

		// Default service type = 0; // Product
        $service_type = isset($CMS->input['service_type']) ? intval($CMS->input['service_type']) : 0;
        // Booking dành cho quy trình order cũ
        $booking_hours = isset($CMS->input['booking_hours']) ? $CMS->input['booking_hours'] : null;
        $booking_date = (isset($CMS->input['booking_date']) && $CMS->input['booking_date']) ? $CMS->class->date->date2time($CMS->input['booking_date']) : 0;
        // End booking quy trình cũ

        $cus_id = isset($CMS->input['cus_id']) ? intval($CMS->input['cus_id']) : 0;
        $user_id = isset($CMS->input['user_id']) ? intval($CMS->input['user_id']) : 0;
        $store_id = isset($CMS->input['store_id']) ? intval($CMS->input['store_id']) : 0;
        $ord_note = $CMS->class->editor->input('ord_note');

		$payment_method = isset($CMS->input['order_payment_method']) ? intval($CMS->input['order_payment_method']) : $order['payment_method'];
        $ord_time_update = time();
        // $ord_note = isset($CMS->input['ord_note']) ? $CMS->input['ord_note'] : '';
       	// $ord_discount_type = intval($CMS->input['trx_discount_type']);
       	// $ord_discount = $CMS->input['trx_discount_value'];
        $is_send_mail = isset($CMS->input['is_send_mail']) ? intval($CMS->input['is_send_mail']) : 0;
        $ord_content = isset($CMS->input['ord_content']) ? $CMS->input['ord_content'] : $order['ord_content'];
        $is_shipping = isset($CMS->input['is_shipping']) ? intval($CMS->input['is_shipping']) : 0;
        $confirm_paid = isset($CMS->input['confirm_paid']) ? intval($CMS->input['confirm_paid']) : 0;
        $ord_status_custom = isset($CMS->input['ord_status_custom']) ? intval($CMS->input['ord_status_custom']) : 0;
        $tracking_code = $CMS->input['tracking_code'];
        $ship = array();
        if( $is_shipping == 1 ) 
        {
        	// if( intval($CMS->vars['enable_shipping']) == 0 )
        	// {
	        // 	// Shipping quy trình cũ
	        //     $ship['ship_receive_name'] = isset($CMS->input['ship_receive_name']) ? $CMS->input['ship_receive_name'] : '';
	        //     $ship['ship_phone'] = isset($CMS->input['ship_phone']) ? $CMS->input['ship_phone'] : '';
	        //     $ship['ship_address'] = isset($CMS->input['ship_address']) ? $CMS->input['ship_address'] : '';
	        //     $ship['ship_location'] = isset($CMS->input['ship_location']) ? $CMS->input['ship_location'] : null;
	        //     $ship['ship_code'] = isset($CMS->input['ship_code']) ? $CMS->input['ship_code'] : '';
	        //     $ship['ship_weight'] = isset($CMS->input['ship_weight']) ? $CMS->input['ship_weight'] : null;
	        //     $ship['ship_long'] = isset($CMS->input['ship_long']) ? $CMS->input['ship_long'] : '';
	        //     $ship['ship_wide'] = isset($CMS->input['ship_wide']) ? $CMS->input['ship_wide'] : '';
	        //     $ship['ship_height'] = isset($CMS->input['ship_height']) ? $CMS->input['ship_height'] : '';
	        //     $ship['ship_service_type'] = isset($CMS->input['ship_service_type']) ? $CMS->input['ship_service_type'] :  null;
	        //     $ship['ship_deliver'] = isset($CMS->input['ship_deliver']) ? $CMS->input['ship_deliver'] : null;
	        //     $ship['ship_deliver_fee'] = isset($CMS->input['ship_deliver_fee']) ? $CMS->input['ship_deliver_fee'] : null;
	        // }
	        // else
	        {
	            // Quy trình mới
	        	
	        	// Billing
	            $ship['bill_first_name'] = isset($CMS->input['bill_first_name']) ? $CMS->input['bill_first_name'] : '';
	            $ship['bill_last_name']  = isset($CMS->input['bill_last_name']) ? $CMS->input['bill_last_name'] : '';
	            $ship['bill_full_name']  = $ship['bill_first_name']. " " .$ship['bill_last_name'];
	            $ship['bill_email']      = isset($CMS->input['bill_email']) ? $CMS->input['bill_email'] : '';
	            $ship['bill_phone']      = isset($CMS->input['bill_phone']) ? $CMS->input['bill_phone'] : '';
	            $ship['bill_company']    = isset($CMS->input['bill_company']) ? $CMS->input['bill_company'] : '';
	            $ship['bill_address']    = isset($CMS->input['bill_address']) ? $CMS->input['bill_address'] : '';
	            $ship['bill_address2']   = isset($CMS->input['bill_address2']) ? $CMS->input['bill_address2'] : '';
	            $ship['bill_city']       = isset($CMS->input['bill_city']) ? $CMS->input['bill_city'] : '';
	            $ship['bill_province']   = isset($CMS->input['bill_province']) ? $CMS->input['bill_province'] : '';
	            $ship['bill_zipcode']    = isset($CMS->input['bill_zipcode']) ? $CMS->input['bill_zipcode'] : '';
	            $ship['bill_country']    = isset($CMS->input['bill_country']) ? $CMS->input['bill_country'] : '';
	            if( strtoupper($ship['bill_country']) == 'US' )
	            {
	            	$ship['bill_province']   = isset($CMS->input['bill_state']) ? $CMS->input['bill_state'] : '';
	            }

	            // Shipping
	            $ship['ship_deliver']    = isset($CMS->input['ship_deliver']) ? $CMS->input['ship_deliver'] : null;
	            $ship['ship_first_name'] = isset($CMS->input['ship_first_name']) ? $CMS->input['ship_first_name'] : '';
	            $ship['ship_last_name']  = isset($CMS->input['ship_last_name']) ? $CMS->input['ship_last_name'] : '';
	            $ship['ship_full_name']  = $ship['ship_first_name']. " " .$ship['ship_last_name'];
	            $ship['ship_email']      = isset($CMS->input['ship_email']) ? $CMS->input['ship_email'] : '';
	            $ship['ship_phone']      = isset($CMS->input['ship_phone']) ? $CMS->input['ship_phone'] : '';
	            $ship['ship_company']    = isset($CMS->input['ship_company']) ? $CMS->input['ship_company'] : '';
	            $ship['ship_address']    = isset($CMS->input['ship_address']) ? $CMS->input['ship_address'] : '';
	            $ship['ship_address2']   = isset($CMS->input['ship_address2']) ? $CMS->input['ship_address2'] : '';
	            $ship['ship_city']       = isset($CMS->input['ship_city']) ? $CMS->input['ship_city'] : '';
	            $ship['ship_province']   = isset($CMS->input['ship_province']) ? $CMS->input['ship_province'] : '';
	            $ship['ship_zipcode']    = isset($CMS->input['ship_zipcode']) ? $CMS->input['ship_zipcode'] : '';
	            $ship['ship_country']    = isset($CMS->input['ship_country']) ? $CMS->input['ship_country'] : '';
	            if( strtoupper($ship['ship_country']) == 'US' )
	            {
	            	$ship['ship_province']   = isset($CMS->input['ship_state']) ? $CMS->input['ship_state'] : '';
	            }
	        }
        }

        $shipping_info = json_encode($ship, JSON_UNESCAPED_UNICODE);

        // Use for service
        if ($service_type == 1) {
            if ($booking_hours == "" and intval($CMS->input['booking_form_email']) == 1) {
                $_SESSION['msg'] = "{$CMS->lang['booking_hours_not_empty']}";
                return false;
            }
            //Unset value asset sub-item
            $this->unset_asset_item();

        } else {
            if ($type == "") {
                if (($store_id == "" OR $store_id <= 0) AND $CMS->vars['addon_goods_enable'] == 1) {
                    //$_SESSION['error_msg']  = "{$CMS->lang['store_not_empty']}"; return false;
                }
            }

        }

        $ord_status = isset($CMS->input['ord_status']) ? intval($CMS->input['ord_status']) : $order['ord_status'];
        $account_id = isset($CMS->input['account_id']) ? intval($CMS->input['account_id']) : $order['account_id'];

        if($payment_method == 0)
		{
			$account_id = 0;
		}
		elseif($payment_method == 1)// Chọn bank
		{
			 if($account_id == "" OR $account_id == 0)
			 {
			 	 $_SESSION['error_msg']  = "{$CMS->lang['bank_err']}"; return false; 
			 }
		}

        $ord_is_vat = isset($CMS->input['ord_is_vat']) ? intval($CMS->input['ord_is_vat']) : 0;

        // Check input
		if (  $cus_id == "" OR ! $cus_id ) 
		{ 
			// Not check: Order for Guest then cus_id 0
			// $_SESSION['error_msg']  = "{$CMS->lang['order_cus_err']}"; return false; 
		}

        $CMS->customer->update_last_action_time($cus_id);

        list($item, $order_total) = $this->create_session_order();
        if (!is_array($item) OR count($item) <= 0 ) { $_SESSION['error_msg'] = "{$CMS->lang['order_total_err']}"; return false; }

 		$_SESSION['order_item'] = $item;

 		$ord_item = json_encode($item);

        // Get quantity items for report
        $quantity_item = 0;
 		foreach ($_SESSION['order_item'] as $key => $subitem) {
                        $quantity_item += $subitem['product_quantity'];
                        $quantity_item += $subitem['asset_quantity'];
 			$amount += $subitem['item_price'] *  $subitem['item_cycle'];
 		}

 		// ThamLV d22-6-2018: Detect store follow first product if not choose store
        if( ! $store_id )
        {
        	$store_id = $this->getDefaultStoreFollowProducts($item);
        }

        // Shipping service
        // Order shipping method: 0:Standard; 1: Express 
        // Order shipping location: 0: Domestics , 1: International 
        $ord_shipping_method = $order['ord_shipping_method'];
        if( isset($CMS->input['ord_shipping_method']) )
        {
        	if( in_array($CMS->input['ord_shipping_method'], array('0', '1')) )
        	{
        		$ord_shipping_method = $CMS->input['ord_shipping_method'];
        	}
        }
        else if( isset($CMS->input['ship_method']) )
        {
        	if( in_array($CMS->input['ship_method'], array('0', '1')) )
        	{
        		$ord_shipping_method = $CMS->input['ship_method'];
        	}
        }

        $ord_shipping_location = $order['ord_shipping_location'];
        if( isset($CMS->input['ord_shipping_location']) )
        {
        	if( in_array($CMS->input['ord_shipping_location'], array('0', '1')) )
        	{
        		$ord_shipping_location = $CMS->input['ord_shipping_location'];
        	}
        }
        else if( isset($CMS->input['ship_country']) AND isset($CMS->vars['default_shiping_location']) )
        {
        	$ord_shipping_location = strtoupper($CMS->vars['default_shiping_location']) == strtoupper($CMS->input['ship_country']) ? 0 : 1;
        }

 		$output_total = $CMS->transactions->calculate(); 
 		$ord_amount = $CMS->input['trx_amount'] = $output_total['subtotal'];
 		$ord_total = $output_total['total'];
 		$ord_tax = $CMS->input['trx_tax']  = $output_total['tax'];
        $total_discount = $CMS->input['trx_total_discount'] = $output_total['discount'];
        $commission = $output_total['commission']*1;
        $ord_fee_shipping = $output_total['fee_shipping'];

        $CMS->input['trx_total'] = $ord_total - $ord_fee_shipping; // Transaction not contain shipping fee

        // Email owned order
        if( $cus_id )
        {
        	$ord_email = $CMS->customer->getInfo($cus_id, 'cus_email');	
        }
        else
        {
        	$ord_email = isset($CMS->input['cus_email']) ? trim($CMS->input['cus_email']) : (isset($CMS->input['bill_email']) ? trim($CMS->input['bill_email']) : '');
        }

 		//Update transaction
 		$CMS->transactions->edit_transaction($order['transaction_id']);

		// Update order
 		$sql = $DB->query("UPDATE `".root_table."order` SET `ord_amount`='{$ord_amount}', `ord_total`='{$ord_total}', ord_tax = '{$ord_tax}', `payment_method`='{$payment_method}',  `account_id`='{$account_id}', `ord_item`='{$ord_item}', `store_id`='{$store_id}', `cus_id`='{$cus_id}', `user_id`='{$user_id}', `ord_time_update`='{$ord_time_update}', `ord_note`='{$ord_note}', is_shipping ='{$is_shipping}', shipping_info='{$shipping_info}', booking_hours = '{$booking_hours}', booking_date = '{$booking_date}',ord_total_discount='{$total_discount}',ord_quantity_items='{$quantity_item}', ord_commission={$commission}, ord_status='{$ord_status}', ord_status_custom='{$ord_status_custom}', tracking_code='{$tracking_code}', ord_shipping_method='{$ord_shipping_method}', ord_shipping_location='{$ord_shipping_location}', ord_fee_shipping='{$ord_fee_shipping}', ord_email='{$ord_email}' WHERE `ord_id`={$id}");

		// Edit address shipp bill
        if( $is_shipping == 1 and $id )
        {
        	$sp_id = intval($CMS->input['sp_id']);
        	$sp_time = time();
        	$sql_shipbill_address = "
        	UPDATE ".root_table."shipbill_address 
        	SET bill_first_name='{$ship['bill_first_name']}', bill_last_name='{$ship['bill_last_name']}', bill_full_name='{$ship['bill_full_name']}', bill_email='{$ship['bill_email']}', bill_phone='{$ship['bill_phone']}', bill_phone='{$ship['bill_company']}', bill_address='{$ship['bill_address']}', bill_address2='{$ship['bill_address2']}', bill_city='{$ship['bill_city']}', bill_province='{$ship['bill_province']}', bill_zipcode='{$ship['bill_zipcode']}', bill_country='{$ship['bill_country']}', ship_first_name='{$ship['ship_first_name']}', ship_last_name='{$ship['ship_last_name']}', ship_full_name='{$ship['ship_full_name']}', ship_email='{$ship['ship_email']}', ship_phone='{$ship['ship_phone']}', ship_company='{$ship['ship_company']}', ship_address='{$ship['ship_address']}', ship_address2='{$ship['ship_address2']}', ship_city='{$ship['ship_city']}', ship_province='{$ship['ship_province']}', ship_country='{$ship['ship_country']}', cus_id='{$cus_id}', sp_deliver='{$ship['ship_deliver']}', sp_time='{$sp_time}' 
        	WHERE sp_id='{$sp_id}' 
        	";
	        $DB->query($sql_shipbill_address);
        }

        // Insert comment
        $comment_content = $CMS->class->editor->input('comment_content');
        $on_comment = intval($CMS->input['on_comment']);
        if($comment_content )//and $on_comment
        {
			// Insert comment for customer
	        $module_id = $ord_id;
	    	$module_name = "order";
	    	$user_id = $member['user_id'];
	    	$comment_name = $CMS->lang['order_status_'.$ord_status];

	    	$comment_time = time();
	    	$comment_ip_address = $_SERVER['REMOTE_ADDR'];
	    	$comment_approved = 1;// Mặc định là duyệt
	    	$comment_hide = intval($CMS->input['send_notify']) == 1 ? 0 : 1; // 0: Hiện, 1: Ẩn 

	    	// Insert logs comment
	    	$DB->query("INSERT INTO ".root_table."comment (module_id, module_name, user_id, cus_id, comment_name, comment_content, comment_time, comment_ip_address, comment_approved, ord_status, ord_status_custom, comment_hide) VALUES ('{$module_id}', '{$module_name}', '{$user_id}', '{$order['cus_id']}', '{$comment_name}', '{$comment_content}', '{$comment_time}', '{$comment_ip_address}', '{$comment_approved}', '{$ord_status}', '{$ord_status_custom}', '{$comment_hide}')");
	    	$comment_id = $DB->last_insert_id();
	    	if($comment_id)
	    	{
	    		// Create log
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['title_addlog_successful']} <b>#{$comment_id}</b>")."<br />";
				// Send email
				if($comment_hide == 0)
				{
					$data_comment = $CMS->comment->get_info($comment_id);
					$data_customer = $CMS->customer->getInfo($order['cus_id']);
					$order_convert = $this->convertvalue($order);
					$this->sendNotifyCustomer($data_comment, $data_customer, $order_convert);
				}
	    	}else
	    	{
	    		$_SESSION["msg"] .= $CMS->lang['title_addlog_error']."<br/>";
	    	}
		    	
        }

	    // Del order item old
	    $DB->query("UPDATE ".root_table."order_item SET ordi_deleted='1'  WHERE ord_id='{$id}'");
	    unset($CMS->input);
 		// ADd order item
 		$this->add_order_item($id);
 		$this->convert_input_item($order['ord_id']);
 	//	echo $order['transaction_id'];exit;
 		// Check is_exits trx_id
 		if($order['transaction_id'] != "" AND $order['transaction_id'] > 0)
 		{ 	 
 			// Del transaction_item item old
		    $DB->query("UPDATE ".root_table."transaction_item SET tri_deleted='1'  WHERE trx_id='{$order['transaction_id']}'");
 		    $CMS->input['sub'] =   1;
	 		// Update item transaction
	 		$CMS->transactions->addItem($order['transaction_id'],[],1);
 		}
 		 	 
 		$CMS->class->logs->key = "order_{$id}";   
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['order_edited']} <b>{$order['ord_name']}</b>")."<br />";

		// Get info
		$new_order = $this->get_info($id);
 		 
		$CMS->class->logs->key = "order_{$id}";   
		$CMS->class->logs->save_detail("order",$id,$new_order);

		// Check send mail
		if($is_send_mail == 1)
		{
			$this->send_email_order($new_order);
		}

		unset($_SESSION['order_item']);

		return $new_order;
	}


	//===========================================================================
	//  Renew
	//===========================================================================
	
	public function renew()
	{
		global $CMS, $DB, $member;

		// Show form config renew
		$ord_id = $CMS->input['id'];
		$ordi = $CMS->input['ordi']; 
		$price = intval($CMS->input['price']);
		$cycle = intval($CMS->input['cycle']);
		$end_time =  $CMS->input['end_time'];
		if($end_time == "")
		{
			$_SESSION['msg'] = "{$CMS->lang['choice_date_end_renew']}";
			return false;
		}
		$order_item =  $CMS->order->getinfo_ordi($ordi);  
		if(!is_array($order_item))
		{ 
			$_SESSION['msg'] = "{$CMS->lang['order_isnot_exits']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order");return false;
		}

		if(is_array($order_item))
		{   
			unset($_SESSION['ord_renew_info']);
			
		    $order = $CMS->order->get_info($ord_id);
		   	if(!is_array($order))
			{ 
				$_SESSION['msg'] = "{$CMS->lang['order_isnot_exits']}";
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order");return false;
			}

		    // Action submit
		    // "{$CMS->vars['root_domain']}/?site=order&act=renew_do&id={$ord_id}&ordi={$ordi}";
		    $total = $price * $cycle;
		    $CMS->input['trx_amount'] = $total;
	 	    $CMS->input['trx_total'] = $total;
	 		$CMS->input['trx_tax']  = 0;
	 	 	$CMS->input['user_id'] = $member['user_id'];
	 	 	$CMS->input['cus_id'] =  $order['cus_id'];
	 	 	$product = $CMS->product->getInfo($order_item['product_id']);
	 	 	
	 	 	$CMS->input['product_id'][0] = $product['product_id'];
	 	 	$CMS->input['product_name'][0] = $product['product_name'];
	 	 	$CMS->input['product_description'][0] = $product['product_description'];
	 	 	$CMS->input['product_cycle_type'][0] = $product['product_cycle_type'];
	 	 	$CMS->input['product_cycle'][0] = $cycle;
	 	 	$CMS->input['product_price'][0] = $price;
	 	 	$CMS->input['product_quantity'][0] = 1;
	 	 				 
			// Add transaction
			$trx_id = $CMS->transactions->add_transaction();
 
			//$date = date("Y-m-d");
			$date = $order_item['ordi_expiry_date'];
			// if($product['product_cycle'] == 1)//Hang thang
			// {
				 
			// 	$ordi_expiry_date = strtotime(date("Y-m-d",  $date) . " +{$cycle} month");			  
			// }
			// if($product['product_cycle'] == 2)//Hang nam
			// {
			// 	$product_cycle = $cycle * 12;
			// 	$ordi_expiry_date = strtotime(date("Y-m-d",  $date) . " +{$cycle} month");			  
			// }
			// if($product['product_cycle'] == 0)//Hang nam
			// {
			// 	$product_cycle =  1;
				 	  
			// }
 
			$ordi_expiry_date = $CMS->input['end_time'] ? $CMS->class->date->date2time($CMS->input['end_time']) : 0;
			// Update order_item
			$DB->query("UPDATE ".root_table."order_item SET trx_id='{$trx_id}', ordi_status = 4, ordi_expiry_date_estimate = '{$ordi_expiry_date}', ordi_payment_status = '0' WHERE ordi_id = '{$ordi}' ");
			// Update order original to transaxtion
			$DB->query("UPDATE ".root_table."transaction SET ord_id='{$ord_id}' WHERE trx_id='{$trx_id}' ");
			$this->update_order_original_status($ord_id);	 
			$this->update_order_original_paystatus($ord_id);	
			$CMS->class->logs->key= "order_{$ord_id}";
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['renew_order_success_1']}({$order['ord_name']}, Items:#{$ordi}){$CMS->lang['renew_order_success_2']}");
 

			return true;
		}
		 

	}
	//=====================================================
	// Send email order
	// ====================================================
	function send_email_order($order = "", $emailtpl = "")
	{
		global $CMS, $DB, $member;

		$info = json_decode($order['ord_content'],1);
		if($info['is_giftcard'] == 1)
		{
			$order['ord_info'] = $info;
			$this->sendEmailGiftcard($order);
		}else
		{
			$time = $CMS->class->date->date_format(time(),1);
			$order = $this->convertvalue($order);
 
			$user = $CMS->user->get_info($order['user_id']);
			$customer = $CMS->customer->getInfo($order['cus_id']);
			$CMS->email->email_template = $emailtpl ? $emailtpl : "order_info";
			$CMS->email->email_to = $customer['cus_email'];
			$CMS->email->email_toname = $customer['cus_full_name'];
			$CMS->email->email_cc = $user['user_email'];


 			// email_from
 			$CMS->email->data['ord_full_day'] =  $time;
			$CMS->email->data['cus_name'] =  $customer['cus_full_name'];
			$CMS->email->data['cus_email'] =  $customer['cus_email'];
			$CMS->email->data['cus_realname'] =  $customer['cus_full_name'];
 
			$CMS->email->data['ord_name'] = $order['ord_name'];
			$CMS->email->data['trx_name'] = $order['trx_name_text'];
			$CMS->email->data['trx_payment_status'] = $order['payment_status_text'];
			$CMS->email->data['ord_status'] = $order['ord_status_text'];
			$CMS->email->data['ord_start_time'] = $order['ord_time_bk'];
			$CMS->email->data['payment_method'] = $order['payment_mothod_bk'];
			$CMS->email->data['ord_total'] = $order['ord_total_n'];

            $host = parse_url($CMS->vars['root_domain'])['host'];
            $CMS->email->data['website_name'] = $host;
		 
			$CMS->email->quick_send(0,0);
			return;
		}

	}



	//===========================================================================
	//  Approve order
	//===========================================================================
	
	public function approve_order($type = "unpaid")
	{
		global $CMS, $DB, $member;
		$id = intval($CMS->input['id']);
 		$order = $CMS->order->get_info($CMS->input['id']);
 		if(! is_array($order))
 		{
 			$_SESSION['error_msg'] = "{$CMS->lang['order_isnot_exits']}";
 			return false;
 		}

 		$CMS->class->logs->key = "order_{$id}";
		$CMS->class->logs->old_data = $order;



 		$CMS->input['cus_id'] = $order['cus_id'];
 		$CMS->input['user_id'] =  $order['user_id'];
 		$CMS->input['payment_method'] =  $order['payment_method'];
 		$CMS->input['account_id'] =  $order['account_id'];
 		if($type == "paid")
 		{
 			 $CMS->input['trx_status'] = 1; // Da thanh toan
 		}
 		else
 		{
 			 $CMS->input['trx_status'] = 0; // Nợ thanh toan
 		}
 


 	 	$this->convert_input_item($order['ord_id']);
 	  	
 	  	$CMS->input['trx_total'] = $order['ord_total'];


 	 	// Add invoice
 	
 		if($type == "paid")
 		{
            $now = time();
 			if($order['transaction_id'] != 0)
 			{
 				// Tao receive payment cho hoa don tuong ung
	 		//	if( $CMS->transactions->createReceivedPayment($order['transaction_id']) != false)
	 			//{

	 			   $CMS->transactions->createReceivedPayment($order['transaction_id']) ;
	 			   $DB->query("UPDATE ".root_table."order SET  payment_status = 1, ord_payment_time={$now}  WHERE ord_id='{$id}'");
	 			   // Update ơayment status order_item
	 			   $DB->query("UPDATE ".root_table."order_item SET  ordi_payment_status = 1  WHERE ord_id='{$id}'");
	 			// }
	 	 	// 	else
	 	 	// 	{
	 	 	// 		// Loi khi tao receive payment
	 	 	// 			$_SESSION["error_msg"] = "creater_receive_payment_error";
	 	 	// 			return false;
	 	 	// 	}
 			}
 			else
 			{
 				$trx_id = $CMS->transactions->add_transaction($type);
 				// Tao receive payment cho hoa don tuong ung
	 			if( $CMS->transactions->createReceivedPayment($trx_id) != false)
	 			{ 
	 			   $DB->query("UPDATE ".root_table."order SET  payment_status = 1, transaction_id = '{$trx_id}', ord_payment_time={$now}  WHERE ord_id='{$id}'");
	 			   // Update ơayment status order_item
	 			   $DB->query("UPDATE ".root_table."order_item SET  ordi_payment_status = 1  WHERE ord_id='{$id}'");
	 			}
	 	 		else
	 	 		{
	 	 			// Loi khi tao receive payment
	 	 				$_SESSION["error_msg"] = "creater_receive_payment_error";
	 	 				return false;
	 	 		}

 			} 
 			

 			
 	 	}
 	 	else
 	 	{  
 	 		//  ord_status = 1,  
 	 		$DB->query("UPDATE ".root_table."order SET payment_status = 2  WHERE ord_id='{$id}'");
 	 		// Update ơayment status order_item
 	 		$DB->query("UPDATE ".root_table."order_item SET ordi_payment_status = 0  WHERE ord_id='{$id}'");
 	 		//No thanh toan
 	 	}

 	 	// Get info
		$new_order = $this->get_info($id);
 		   
 
		$CMS->class->logs->key = "order_{$id}";
		if($type == "paid")
 		{

			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['order_apporve']} <b>{$order['ord_name']}</b>")."<br />";
		}
		else
		{
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['order_apporve_unpaid']} <b>{$order['ord_name']}</b>")."<br />";
		}	
		$CMS->class->logs->key = "order_{$id}";
		$CMS->class->logs->save_detail("order",$id,$new_order);

		if($CMS->input['is_send_mail'] == 1)
		{
			$this->send_email_order($new_order, 'cancel_order');
		}
	  				
		return true;

	}

	//===========================================================================
	//  Cancel order
	//===========================================================================
	
	public function cancel_order($ord_id = "", $status = "")
	{
		global $CMS, $DB, $member;
		// Status : 1 => Hoan thành
		// Status : 2 => Dang xu ly
		// Status : 3 => Hủy
 
		$id = intval($ord_id ? $ord_id : $CMS->input['id']);
 		$order = $CMS->order->get_info($id);
 	 
 		 
 		if(! is_array($order))
 		{
 			$_SESSION['error_msg'] = \lib\input::arrayValue($CMS->lang, 'order_isnot_exits');
 			return false;
 		}
 		$old_status = $order['ord_status'];

 		$CMS->class->logs->key = "order_{$id}";
		$CMS->class->logs->old_data = $order;

		 
 	 	$DB->query("UPDATE ".root_table."order SET  ord_status = '{$status}' WHERE ord_id='{$id}'");
 	 	$DB->query("UPDATE ".root_table."order_item SET  ordi_status = '{$status}' WHERE ord_id='{$id}'"); 
 	 	// Update transaction status cancel 
 	 	$DB->query("UPDATE ".root_table."transaction SET  trx_status = '4' WHERE trx_id='{$order['transaction_id']}'"); 
 	 	// Get info
		$new_order = $this->get_info($id );
 		
 		$new_status = $new_order['ord_status'];   
	 
		$CMS->class->logs->key = "order_{$id}";
	 
		$_SESSION["msg"] = $CMS->class->logs->insert("{$CMS->lang['process_order']} {$order['ord_name']}, <b> {$CMS->lang["ord_status_{$old_status}"]} -> {$CMS->lang["ord_status_{$new_status}"]}</b>")."<br />";
		$CMS->class->logs->key = "order_{$id}";
		$CMS->class->logs->save_detail("order",$id,$new_order);
 
		if(\lib\input::get('is_send_mail') == 1)
		{
			$this->send_email_order($new_order,'cancel_order');
		}
	  				
		return true;

	}


	//===========================================================================
	//  Process order
	//===========================================================================
	
	public function process_order($ord_id = "", $status = "")
	{
		global $CMS, $DB, $member;
		// Status : 1 => Hoan thành
		// Status : 2 => Dang xu ly
		//print_r ($CMS->input);exit;

		// Re check stock 
		if($CMS->vars['addon_goods_enable'] == 1)
		{
			$stock = $CMS->order->check_stock(1);
			if($stock > 0 AND $CMS->vars['negative_sale'] ==  1 )
			{  
				$_SESSION['error_msg'] = "{$CMS->lang['store_not_enought']}";
 				return false;
			}
		}

		$id = intval($CMS->input['id']);
 		$order = $CMS->order->get_info($CMS->input['id']);

 		// Check service_type booking
 		if($order['service_type'] == 1)
 		{
 			// Update trạng thái booking và gửi sms (chỉ dùng cho theme nail => K dùng)
// 			$this->confirmBooking($id);
// 			return true;
 		}
 		else
 		{
 			//Check ton kho
 			// if($this->check_stock($CMS->input['id']) > 1)
 			// {
 			// 	$_SESSION['error_msg'] = "{$CMS->lang['store_not_enought']}";
 			// 	return false;
 			// }
 		}
 		if(! is_array($order))
 		{
 			$_SESSION['error_msg'] = "{$CMS->lang['order_isnot_exits']}";
 			return false;
 		}
 		$old_status = $order['ord_status'];

 		$CMS->class->logs->key = "order_{$id}";
		$CMS->class->logs->old_data = $order;

		// Update status cho order_item va Tao phieu xuat kho cho cac order_item 
		$this->update_order_item($id, $status);
 
 	 	$DB->query("UPDATE ".root_table."order SET  ord_status = '{$status}' WHERE ord_id='{$id}'");
 	 	$DB->query("UPDATE ".root_table."order_item SET  ordi_status = '{$status}' WHERE ord_id='{$id}'");

 	 	// Get info
		$new_order = $this->get_info($id );
 		
 		$new_status = $new_order['ord_status'];   
	 
		$CMS->class->logs->key = "order_{$id}";
	 
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['process_order']} {$order['ord_name']}, <b> {$CMS->lang["ord_status_{$old_status}"]} -> {$CMS->lang["ord_status_{$new_status}"]}</b>")."<br />";
		$CMS->class->logs->key = "order_{$id}";
		$CMS->class->logs->save_detail("order",$id,$new_order);
		// Check for giftcard
		if($status == 2)
		{
			$info = json_decode($new_order['ord_content'],1);
			if($info['is_giftcard'] == 1)
			{
				$list_item = $this->getItemByOrder($id);
				$CMS->giftcards->createGiftcard($list_item);
			}
		}

		if($CMS->input['is_send_mail'] == 1)
		{
			$this->send_email_order($new_order);
		}
	  				
		return true;

	}



	/*
	Auto Update payemnt_status order, order_item by transactions_id
	 */
	public function update_payment_status($trx_id = "", $status = "")
	{
		global $CMS, $DB, $member;
	 	
 		if($trx_id == "" OR $status == "" )
 		{
 			 
 			return false;
 		}

 		if($status == 0 ){ // if status 0 (payment fail) , don't update status
 			return false;
 		}

		$time =time();

  		// Check exits order by trx_id
		$ord_sqlquery = "SELECT * FROM ".root_table."order WHERE ord_deleted=0 AND transaction_id = '{$trx_id}' ORDER BY ord_id DESC LIMIT 1";
   		$ord_sql = $DB->query($ord_sqlquery);
        if($DB->num_rows($ord_sql) > 0)
        {   
        	$order = $DB->fetch_array($ord_sql); 
        	// update payment status ->paid

         	$CMS->class->logs->key = "order_{$order['ord_id']}";
			$CMS->class->logs->old_data = $order;	 
	 		$DB->query("UPDATE ".root_table."order SET  payment_status = '1', ord_time_update = '{$time}' WHERE transaction_id = '{$trx_id}'");
	 		$DB->query("UPDATE ".root_table."order_item SET  ordi_payment_status = '1'  WHERE ord_id = '{$order['ord_id']}'");

	 		 $this->update_order_original_paystatus($order['ord_id']);	 
	 		 $this->update_order_original_status($order['ord_id']); 
	 	 	// Get info
			$new_order = $this->get_info($order['ord_id']);
	 		   
		 
			$CMS->class->logs->key = "order_{$order['ord_id']}";
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['update_payment_status']} <b>{$order['ord_name']}</b>")."<br />"; 
			$CMS->class->logs->key = "order_{$order['ord_id']}";
			$CMS->class->logs->save_detail("order",$order['ord_id'],$new_order);
			

        }

        // Check exits order_item by trx_id
		$ordi_sqlquery = "SELECT ordi_id,ordi_expiry_date_estimate,ord_id FROM ".root_table."order_item WHERE ordi_deleted=0 AND trx_id = '{$trx_id}' ORDER BY ordi_id DESC LIMIT 1";
   		$ordi_sql = $DB->query($ordi_sqlquery);
        if($DB->num_rows($ordi_sql) > 0)
        {   
        	$ordi= $DB->fetch_array($ordi_sql); 
     		 
         	// update payment status ->paid, update new date end
         	$DB->query("UPDATE ".root_table."order_item SET ordi_status = '2', ordi_payment_status = '1', ordi_expiry_date='{$ordi['ordi_expiry_date_estimate']}'  WHERE ordi_id='{$ordi['ordi_id']}'");
         	$this->update_order_original_paystatus($ordi['ord_id']);	
     		$this->update_order_original_status($ordi['ord_id']); 
        
        }

 	 	
		return true;

	}


	/*
	 Check status order_item and update status order original
	 */
	public function update_order_original_status($ord_id = "" )
	{
		global $CMS, $DB, $member;
	 	
		$time =time();
 	
		$ordi_sqlquery = "SELECT ordi_id  FROM ".root_table."order_item WHERE ordi_deleted=0 AND ord_id = '{$ord_id}' AND (ordi_status = '1' OR ordi_status = '4') ORDER BY ordi_id DESC  ";
	 
   		$ordi_sql = $DB->query($ordi_sqlquery);

        if($DB->num_rows($ordi_sql) > 0)
        {   // Update order original is Processing, when one of all orders item is processing or renewing
         	$DB->query("UPDATE ".root_table."order  SET ord_status = '1', ord_time_update = '{$time}'  WHERE ord_id='{$ord_id}'");
        }
 	 	
 	 	$ordi_sqlquery_total = "SELECT ordi_id  FROM ".root_table."order_item WHERE ordi_deleted=0 AND ord_id = '{$ord_id}'   ORDER BY ordi_id DESC  ";
 	 	$total_ord = $DB->num_rows($DB->query($ordi_sqlquery_total));

 	 	// Check all order_item is DONE
 	 	$ordi_sqlquery_1 = "SELECT ordi_id  FROM ".root_table."order_item WHERE ordi_deleted=0 AND ord_id = '{$ord_id}' AND ordi_status = '2' ORDER BY ordi_id DESC  ";

   		$ordi_sql_1 = $DB->query($ordi_sqlquery_1);
   		$total_sql_1 =  $DB->num_rows($ordi_sql_1);
   	 
        if( $total_sql_1 == $total_ord)
        {    
        	//Update order original is Done, when one of all orders item is Done
        	$DB->query("UPDATE ".root_table."order  SET ord_status = '2'  WHERE ord_id='{$ord_id}'");
        }
 		
 		// Check all order_item is CACNEL
 	 	$ordi_sqlquery_3 = "SELECT ordi_id  FROM ".root_table."order_item WHERE ordi_deleted=0 AND ord_id = '{$ord_id}' AND ordi_status = '3' ORDER BY ordi_id DESC  ";

   		$ordi_sql_3 = $DB->query($ordi_sqlquery_3);
   		$total_sql_3 =  $DB->num_rows($ordi_sql_3);
   	 
        if( $total_sql_3 == $total_ord)
        {
        	//Update order original is CANCEL, when one of all orders item is CANCEL
        	$DB->query("UPDATE ".root_table."order  SET ord_status = '3'  WHERE ord_id='{$ord_id}'");
        	$ord_original = $CMS->order->get_info($ord_id);
        	// Update transaction status cancel 
 	 		$DB->query("UPDATE ".root_table."transaction SET  trx_status = '4' WHERE trx_id='{$ord_original['transaction_id']}'");

        }

        // Check all order_item is RENEW
 	 	$ordi_sqlquery_4 = "SELECT ordi_id  FROM ".root_table."order_item WHERE ordi_deleted=0 AND ord_id = '{$ord_id}' AND ordi_status = '4' ORDER BY ordi_id DESC  ";

   		$ordi_sql_4 = $DB->query($ordi_sqlquery_4);
   		$total_sql_4 =  $DB->num_rows($ordi_sql_4);
   	 
        if( $total_sql_4 == $total_ord)
        {
        	//Update order original is RENEW, when one of all orders item is RENEW
        	$DB->query("UPDATE ".root_table."order  SET ord_status = '4'  WHERE ord_id='{$ord_id}'");
        }


		return true;

	}

	/*
	 Check status order_item and update  paymentstatus order original
	 */
	public function update_order_original_paystatus($ord_id = "" )
	{
		global $CMS, $DB, $member;
	 	
		$time =time();
 
 		$ordi_sqlquery_total = "SELECT ordi_id  FROM ".root_table."order_item WHERE ordi_deleted=0 AND ord_id = '{$ord_id}'   ORDER BY ordi_id DESC  ";
 		$ord_sql_total = $DB->query($ordi_sqlquery_total);
 	 	$total_ord = $DB->num_rows($ord_sql_total);

 	 	// Check all orders item is PAID
		$ordi_sqlquery = "SELECT ordi_id  FROM ".root_table."order_item WHERE ordi_deleted=0 AND ord_id = '{$ord_id}'  AND ordi_payment_status = '1' ORDER BY ordi_id DESC  ";
   		$ordi_sql = $DB->query($ordi_sqlquery);
   		$total_sql = $DB->num_rows($ordi_sql);

        if( $total_sql == $total_ord)
        {   // Update order original is PAID, when   all orders item is PAID
         	$DB->query("UPDATE ".root_table."order  SET  payment_status = '1'   WHERE ord_id='{$ord_id}'");
        }
 	 	else
 		{
 			 
 			//Update order original is Partially Paid, when one of all orders item is  PAID
        	$DB->query("UPDATE ".root_table."order  SET  payment_status = '3', ord_time_update = '{$time}'  WHERE ord_id='{$ord_id}'");
 		}

 	 	// Check all orders item is UNPAID
 	 	$ordi_sqlquery_1 = "SELECT ordi_id  FROM ".root_table."order_item WHERE ordi_deleted=0 AND ord_id = '{$ord_id}' AND ordi_payment_status = '0'  ORDER BY ordi_id DESC  ";
   		$ordi_sql_1 = $DB->query($ordi_sqlquery_1);
   		$total_sql_1 = $DB->num_rows($ordi_sql_1) ;
        if($total_sql_1  == $total_ord)
        {
        	//Update order original is UNPAID, when one of all orders item is UNPAID
        	$DB->query("UPDATE ".root_table."order  SET payment_status = '0', ord_time_update = '{$time}'  WHERE ord_id='{$ord_id}'");
        }

		return true;

	}

	/*
	 Get order by transactions_id
	 */
	public function get_order_payment($trx_id = "" )
	{
		global $CMS, $DB, $member;
	 	
 		if($trx_id == "")
 		{
 			 
 			return false;
 		}

 	 
  		// Check exits order by trx_id
		$ord_sqlquery = "SELECT ord_id, ord_name FROM ".root_table."order WHERE ord_deleted=0 AND transaction_id = '{$trx_id}' ORDER BY ord_id DESC LIMIT 1";
   		$ord_sql = $DB->query($ord_sqlquery);
        if($DB->num_rows($ord_sql) > 0)
        {   
        	$order = $DB->fetch_array($ord_sql); 
  			$order_link = "<a href='{$CMS->vars['root_domain']}/?site=order&act=show&id={$order['ord_id']}' >{$order['ord_name']}</a>";
  			return $order_link;
        }

        // Check exits order_item by trx_id
		$ordi_sqlquery = "SELECT OI.ordi_id, OI.ord_id , O.ord_name FROM ".root_table."order_item OI, ".root_table."order O WHERE O.ord_id = OI.ord_id AND OI.ordi_deleted=0 AND OI.trx_id = '{$trx_id}' ORDER BY ordi_id DESC LIMIT 1";
		 
   		$ordi_sql = $DB->query($ordi_sqlquery);
        if($DB->num_rows($ordi_sql) > 0)
        {   
        	$ordi= $DB->fetch_array($ordi_sql); 

        	$order_link = "<a href='{$CMS->vars['root_domain']}/?site=order&act=show&id={$ordi['ord_id']}' >{$ordi['ord_name']}</a>";
        	 
        	return $order_link;
      
        }
 	 	
		return false;

	}

	//===========================================================================
	//  Add Order Item
	//===========================================================================
	
	public function add_order_item($ord_id = "" )
	{
		global $CMS, $DB, $member;

		$order = $this->get_info($ord_id);
		$time = $order['ord_time'];
		$cus_id = intval($order['cus_id']);
		$user_id = intval($order['user_id']);

		foreach ($_SESSION['order_item'] as $key => $value) {
			# code...
			$price = 0;

			if($value['product_id'] != "" AND $value['product_id'] > 0)
			{
//				$product = $CMS->product->getInfo($value['product_id']);
				$date = date("Y-m-d");

                $ordi_expiry_date = 0;

				if($value['product_cycle_type'] == 1)//Hang thang
				{
					 
					$ordi_expiry_date = strtotime(date("Y-m-d", strtotime($date)) . " +{$value['product_cycle']} month");			  
				}
				else if($value['product_cycle_type'] == 2)//Hang nam
				{
					$product_cycle = $value['product_cycle'] * 12;
					$ordi_expiry_date = strtotime(date("Y-m-d", strtotime($date)) . " +{$product_cycle} month");			  
				}
				else if($value['product_cycle_type'] == 0)//1 lan
				{
					$product_cycle = $value['product_cycle'] = 1;
				}

				$price = floatval($value['product_price']) + floatval($value['product_price_add']);
				$old_price = floatval($value['product_old_price']) + floatval($value['product_price_add']);
				$discountType = $value['product_discount_type']*1;
				$discountValue = $value['product_discount_value']*1;
				if($value['product_quantity'] > 1)
				{
					$calculateResult = $CMS->transactions->calculateItem($old_price > $price ? $old_price : $price, $discountType, $discountValue, $value['product_tax'], 1, 1);
				}else
				{
					$calculateResult = $CMS->transactions->calculateItem($old_price > $price ? $old_price : $price, $discountType, $discountValue, $value['product_tax'], $value['product_quantity'], $value['product_cycle']);
				}


                $subTotal = $calculateResult['subTotal'];
                $totalDiscount =  $calculateResult['totalDiscount'];
                $total = $calculateResult['total'];
                $totalTax = $calculateResult['totalTax'];
				$order_total = $total;

                $commissionType = $value['product_commission_type']*1;
                $commissionValue = $value['product_commission_value']*1;
                $commission = $commissionType ? $commissionValue : ($subTotal - $totalDiscount) * $commissionValue / 100;

                $bookingTime = $value['product_booking_time']*1;
                $staff = $value['product_staff']*1;

                $ordi_key = $value['key'];
                $ordi_parent_key = $value['parent_key'];

                $var_id = $value['var_id']*1;
                if( $var_id > 0 AND isset($value['var_content']['var_title']))
                {
                	$value['product_name'] .= ' ' . $value['var_content']['var_title'];
                }
                $var_content = \lib\input::jsonEncode($value['var_content'], 0);
                $var_content = addslashes($var_content);
                
				if($value['product_quantity'] > 1)
				{
					for($i = 1; $i <= $value['product_quantity']; $i++)
					{
						if($value['product_tri_id'] > 0 AND $value['product_tri_id'] != "" )
						{
							//LAp va tao theo so luong 
							$DB->query("INSERT INTO `".root_table."order_item` (  `ordi_name`,`ordi_description`, `ordi_price` ,`ordi_total`, `ordi_tax`,  `ordi_status` , `product_id`, `ordi_cycle`, `cycle_type`, `user_id`, `cus_id`, `ord_id`,  `ordi_time`, `ordi_expiry_date`, `store_id`, `ordi_payment_status`, ordi_subtotal, ordi_total_tax, ordi_discount_type, ordi_discount_value, ordi_total_discount, ordi_commission_type, ordi_commission_value, ordi_commission, ordi_old_price, ordi_booking_time, ordi_staff, var_id, var_content, ordi_key, ordi_parent_key) VALUES ( '{$value['product_name']}', '{$value['product_description']}', '{$price}' , '{$total}', '{$value['product_tax']}' , '0', '{$value['product_id']}', '{$value['product_cycle']}', '{$value['product_cycle_type']}', '{$user_id}', '{$cus_id}', '{$ord_id}','{$time}','{$ordi_expiry_date}','{$order['store_id']}', '{$order['payment_status']}', '{$subTotal}', '{$totalTax}',  '{$discountType}', '{$discountValue}', '{$totalDiscount}', '{$commissionType}', '{$commissionValue}', '{$commission}', '{$old_price}', '{$bookingTime}', '{$staff}', '{$var_id}', '{$var_content}', '{$ordi_key}', '{$ordi_parent_key}')");
						}
						else
						{
							//LAp va tao theo so luong 
							$DB->query("INSERT INTO `".root_table."order_item` (  `ordi_name`,`ordi_description`, `ordi_price` ,  `ordi_total`, `ordi_tax`,  `ordi_status` , `product_id`, `ordi_cycle`, `cycle_type`, `user_id`, `cus_id`,   `ord_id`, `tri_id`, `ordi_time`, `ordi_expiry_date`, `store_id`,`ordi_payment_status`, ordi_subtotal, ordi_total_tax, ordi_discount_type, ordi_discount_value, ordi_total_discount, ordi_commission_type, ordi_commission_value, ordi_commission, ordi_old_price, ordi_booking_time, ordi_staff, var_id, var_content, ordi_key, ordi_parent_key) VALUES ( '{$value['product_name']}', '{$value['product_description']}', '{$price}' , '{$total}', '{$value['product_tax']}' , '0', '{$value['product_id']}', '{$value['product_cycle']}', '{$value['product_cycle_type']}', '{$user_id}', '{$cus_id}', '{$ord_id}', '{$value['product_tri_id']}', '{$time}','{$ordi_expiry_date}','{$order['store_id']}','{$order['payment_status']}', '{$subTotal}', '{$totalTax}',  '{$discountType}', '{$discountValue}', '{$totalDiscount}', '{$commissionType}', '{$commissionValue}', '{$commission}', '{$old_price}', '{$bookingTime}', '{$staff}', '{$var_id}', '{$var_content}', '{$ordi_key}', '{$ordi_parent_key}')");
						}
					}
				}
				else
				{ 
					if($value['product_tri_id'] > 0 AND $value['product_tri_id'] != "" )
					{
						$DB->query("INSERT INTO `".root_table."order_item` (  `ordi_name`,`ordi_description`, `ordi_price` , `ordi_total`, `ordi_tax`,  `ordi_status` , `product_id`, `ordi_cycle`, `cycle_type`, `user_id`, `cus_id`,   `ord_id`, `tri_id`,   `ordi_time`, `ordi_expiry_date`, `store_id`,`ordi_payment_status`, ordi_subtotal, ordi_total_tax, ordi_discount_type, ordi_discount_value, ordi_total_discount, ordi_quantity, ordi_commission_type, ordi_commission_value, ordi_commission, ordi_old_price, ordi_booking_time, ordi_staff, var_id, var_content, ordi_key, ordi_parent_key) VALUES ( '{$value['product_name']}', '{$value['product_description']}', '{$price}' , '{$order_total}', '{$value['product_tax']}' , '0', '{$value['product_id']}', '{$value['product_cycle']}', '{$value['product_cycle_type']}', '{$user_id}', '{$cus_id}', '{$ord_id}', '{$value['product_tri_id']}', '{$time}','{$ordi_expiry_date}','{$order['store_id']}', '{$order['payment_status']}', '{$subTotal}', '{$totalTax}',  '{$discountType}', '{$discountValue}', '{$totalDiscount}', '{$value['product_quantity']}', '{$commissionType}', '{$commissionValue}', '{$commission}', '{$old_price}', '{$bookingTime}', '{$staff}', '{$var_id}', '{$var_content}', '{$ordi_key}', '{$ordi_parent_key}')");
					}
					else
					{
						$DB->query("INSERT INTO `".root_table."order_item` (  `ordi_name`,`ordi_description`, `ordi_price` ,`ordi_total`, `ordi_tax`,  `ordi_status` , `product_id`, `ordi_cycle`, `cycle_type`, `user_id`, `cus_id`,   `ord_id`,  `ordi_time`, `ordi_expiry_date`, `store_id`, `ordi_payment_status`, ordi_subtotal, ordi_total_tax, ordi_discount_type, ordi_discount_value, ordi_total_discount, ordi_quantity, ordi_commission_type, ordi_commission_value, ordi_commission, ordi_old_price, ordi_booking_time, ordi_staff, var_id, var_content, ordi_key, ordi_parent_key) VALUES ( '{$value['product_name']}', '{$value['product_description']}', '{$price}', '{$order_total}', '{$value['product_tax']}' , '0', '{$value['product_id']}', '{$value['product_cycle']}', '{$value['product_cycle_type']}', '{$user_id}', '{$cus_id}', '{$ord_id}','{$time}','{$ordi_expiry_date}','{$order['store_id']}', '{$order['payment_status']}', '{$subTotal}', '{$totalTax}',  '{$discountType}', '{$discountValue}', '{$totalDiscount}', '{$value['product_quantity']}', '{$commissionType}', '{$commissionValue}', '{$commission}', '{$old_price}', '{$bookingTime}', '{$staff}', '{$var_id}', '{$var_content}', '{$ordi_key}', '{$ordi_parent_key}')");
					}
					
				}
				
			}


			if(isset($value['asset_name']) AND $value['asset_name'] != "" AND isset($value['asset_key']) AND $value['asset_key'] != "")
			{	 
//				$asset = $CMS->assets->get_info($value['asset_key']);

                $price = $value['asset_price']*1;
                $old_price = $value['asset_old_price']*1;
                $discountType = $value['asset_discount_type']*1;
                $discountValue = $value['asset_discount_value']*1;

                $calculateResult = $CMS->transactions->calculateItem($price, $discountType, $discountValue, $value['asset_tax'], 1, 1);

                $subTotal = $calculateResult['subTotal'];
                $totalDiscount =  $calculateResult['totalDiscount'];
                $total = $calculateResult['total'];
                $totalTax = $calculateResult['totalTax'];
                $order_total = $total;

                $bookingTime = $value['asset_booking_time']*1;
                $staff = $value['asset_staff']*1;

				if( $value['asset_quantity'] > 1)
				{
					for($k = 1; $k <=  $value['asset_quantity']; $k++)
					{
						//LAp va tao theo so luong 
						$DB->query("INSERT INTO `".root_table."order_item` (  `ordi_name`,`ordi_description` ,`ordi_total`,`ordi_price` , `ordi_tax`,  `ordi_status` , `ass_key`, `ass_id`,  `user_id`, `cus_id`,   `ord_id`,  `ordi_time`, `store_id`,`ordi_payment_status`, ordi_subtotal, ordi_total_tax, ordi_discount_type, ordi_discount_value, ordi_total_discount, ordi_old_price, ordi_booking_time, ordi_staff)  VALUES ( '{$value['asset_name']}', '', '{$order_total}', '{$value['asset_price']}' , '{$value['asset_tax']}' ,'0', '{$value['asset_key']}', '{$value['ass_id']}',  '{$user_id}', '{$cus_id}', '{$ord_id}','{$time}','{$order['store_id']}', '{$order['payment_status']}', '{$subTotal}', '{$totalTax}',  '{$discountType}', '{$discountValue}', '{$totalDiscount}', '{$old_price}', '{$bookingTime}', '{$staff}')");
					}
				}
				else
				{
					if($value['ass_tri_id'] > 0 AND $value['ass_tri_id'] != "" )
					{
						$DB->query("INSERT INTO `".root_table."order_item` (  `ordi_name`,`ordi_description` ,`ordi_total`,`ordi_price` , `ordi_tax`,  `ordi_status` , `ass_key`, `ass_id`,  `user_id`, `cus_id`,   `ord_id`, `tri_id`,  `ordi_time`, `store_id`, `ordi_payment_status`, ordi_subtotal, ordi_total_tax, ordi_discount_type, ordi_discount_value, ordi_total_discount, ordi_old_price, ordi_booking_time, ordi_staff)  VALUES ( '{$value['asset_name']}', '{$value['asset_name']}', '{$order_total}', '{$value['asset_price']}' , '{$value['asset_tax']}' ,'0', '{$value['asset_name']}', '{$value['ass_id']}', '{$user_id}', '{$cus_id}', '{$ord_id}', '{$value['ass_tri_id']}', '{$time}','{$order['store_id']}', '{$order['payment_status']}', '{$subTotal}', '{$totalTax}',  '{$discountType}', '{$discountValue}', '{$totalDiscount}' , '{$old_price}', '{$bookingTime}', '{$staff}')");
					}
					else
					{
						$DB->query("INSERT INTO `".root_table."order_item` (  `ordi_name`,`ordi_description` ,`ordi_total`,`ordi_price` , `ordi_tax`,  `ordi_status` , `ass_key`, `ass_id`,   `user_id`, `cus_id`,   `ord_id`,  `ordi_time`, `store_id`, `ordi_payment_status`, ordi_subtotal, ordi_total_tax, ordi_discount_type, ordi_discount_value, ordi_total_discount, ordi_old_price, ordi_booking_time, ordi_staff)  VALUES ( '{$value['asset_name']}', '{$value['asset_name']}', '{$order_total}', '{$value['asset_price']}' , '{$value['asset_tax']}' ,'0', '{$value['asset_key']}', '{$value['ass_id']}',  '{$user_id}', '{$cus_id}', '{$ord_id}','{$time}','{$order['store_id']}', '{$order['payment_status']}', '{$subTotal}', '{$totalTax}',  '{$discountType}', '{$discountValue}', '{$totalDiscount}', '{$old_price}', '{$bookingTime}', '{$staff}')");
					}
				}
			}
		}
		return;
	}

	 
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB;
		
		// Get info
		$data = $this->get_info();
		// Dion hang co trang thai thanh toan: "Da thanh toan", "No thanh toan" thi k dc phep xoa
		if($data['payment_status'] == 1 OR $data['payment_status'] == 2)
		{

			$_SESSION['error_msg'] = "{$CMS->lang['order']} : #{$data['ord_name']} {$CMS->lang['not_canceled']}!";
			// Redirect
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&page={$CMS->input['page']}");

		}
		// Check existing
		if ( ! $data ) { return false; }
		
  
 	
		$DB->query("UPDATE ".root_table."order SET ord_deleted = 1  WHERE ord_id='{$data['ord_id']}'");

		// Xoa transaction tuong ung
		if($data['transaction_id'] > 0)
		{
			$CMS->transactions->delete($data['transaction_id'] );
		}
		
	
		$CMS->class->logs->key = "order_{$id}";
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['order_deleted']} <b>{$data['ord_name']}</b>")."<br />";

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&page={$CMS->input['page']}");
		
		return true;
	}
	
	public function mdelete()
	{
		global $CMS, $DB;
		
		$deleted = 0;
		
		$_SESSION["msg"] .= "";
		
		for ( $i = 0; $i < intval( $CMS->input["data_cnt"] ); $i++ )
		{
			$id = intval( $CMS->input["id_{$i}"] );
				
			if ( $id )
			{
				$data = $this->get_info($id);
 
				// Dion hang co trang thai thanh toan: "Da thanh toan", "No thanh toan" thi k dc phep xoa
				if($data['payment_status'] == 1 OR $data['payment_status'] == 2)
				{

					$_SESSION['error_msg'] = "{$CMS->lang['order']} : #{$data['ord_name']} {$CMS->lang['not_canceled']}!";
					// Redirect
					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&page={$CMS->input['page']}");

				} 
			 
				$DB->query("UPDATE ".root_table."order SET ord_deleted = 1  WHERE ord_id='{$data['ord_id']}'");

				// Xoa transaction tuong ung
				if($data['transaction_id'] > 0)
				{
					$CMS->transactions->delete($data['transaction_id'] );
				}

				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['order_deleted']} <b>{$data['ord_name']}</b>")."<br />";
				
				$deleted = 1;
			}
		}
		
		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['order_delete_failed']}";
		}

		return true;
	}
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB;

		// Setup data
		$CMS->class->search->table_name = "order";
		$CMS->class->search->ignored_fields = array();
		$CMS->class->search->changed_fields = array();
		$CMS->class->search->fields_type = array("order_time" => "time");
		$CMS->class->search->search_type = 0;
		$CMS->class->search->fields_prefix = "";
		$CMS->class->search->fields_replace = array("cat_id" => "cat_id");
		
		// Output
		$data = $CMS->class->search->get_info();
		
		// Update SQL Query
		$this->sql_add .= $data;
		
		// Get List
		$this->listing();

		if($CMS->input['is_exel']=="order")
		{
			$CMS->report->report['format'] = "order";
			$CMS->report->report_display = 2;
			$CMS->report->build_excel_header();
			$CMS->report->build_excel_module();
			$CMS->report->build_excel_footer();
		}
	}
	
	//===========================================================================
	//  ACTIVE POST
	//===========================================================================
	
	public function active()
	{
		global $CMS, $DB, $member;
		
		// Get info
		$order_bk = $this->get_info();

		// User input
		$order_active = intval($CMS->input['method']);
		if($order_active == 2)
		{
			$DB->query("UPDATE ".root_table."order SET order_active ='{$order_active}', order_royalty = 0 WHERE order_id='{$order_bk['order_id']}'");
		}
		else
		{
			$DB->query("UPDATE ".root_table."order SET order_active ='{$order_active}' WHERE order_id='{$order_bk['order_id']}'");
		}
		
	
		if($order_active != $order_bk['order_active'])
		{
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['convert_status']} {$order_bk['order_name']}<b> {$CMS->lang["order_active_{$order_bk['order_active']}"]} -> {$CMS->lang["order_active_{$order_active}"]}</b>")."<br />";
		}
		$order = $this->get_info();

		// Delete cache
		$CMS->class->cache->mdelete("order");

		return $order;
	}
	
	//===========================================================================
	//  update_royalty  POST
	//===========================================================================
	
	public function update_royalty()
	{
		global $CMS, $DB, $member;
		
		// Get info
		$order_bk = $this->get_info();

		// User input
		$order_royalty = $CMS->input['order_royalty'];
		if($order_bk['order_active'] == 0 OR $order_bk['order_active'] == 2)
		{
			$_SESSION["msg"] .= $CMS->lang['not_update_royalty'];
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&act=show&id={$order_bk['order_id']}");
			return false;
		}
		if( is_numeric($order_royalty) == FALSE)
		{
			$_SESSION["msg"] .= $CMS->lang['not_numeric_update_royalty'];
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order&act=show&id={$order_bk['order_id']}");
			return false;
		}
		// Step 1: Save detail logs
		$CMS->class->logs->old_data = $order_bk;
		$CMS->class->logs->key = "order_{$order_bk['order_id']}";
		
		$DB->query("UPDATE ".root_table."order SET order_royalty ='{$order_royalty}' WHERE order_id='{$order_bk['order_id']}'");
	
		
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['update_royalty']} {$order_bk['order_name']}<b> : {$CMS->class->input->currency($order_bk['order_royalty'])} => {$CMS->class->input->currency($order_royalty)}</b>")."<br />";
		$order = $this->get_info();

		// Delete cache
		$CMS->class->cache->mdelete("order");
	// Step 2: Save detail logs
		$CMS->class->logs->key = "order_{$order['order_id']}";
		$CMS->class->logs->save_detail("order",$order['order_id'],$order);
		
		return $order;
	}
	

	//===========================================================================
	//  LOAD order
	//===========================================================================
	
	public function load_order( $category = 0 )
	{
		global $CMS, $DB;
		
		$this->loadhtml();

		// User input
		$cat = $CMS->config_order->get_info($category,"",1);
		$cat = $CMS->config_order->convertvalue($cat);

		// Load order
		$CMS->class->page->type = 1;
		$this->per_page = 10;
		$this->sql_add .= $category > 0 ? " C.cat_id='{$category}' AND order_display=1  AND " : "";
		$this->prefix_html = "{$cat['cat_shorturl']}_";
		$this->suffix_html = ".html";
		$this->listing();
		
		// Print data
		$output = "";

		if ( $DB->num_rows( $this->sql_query ) > 0 )
		{
			while ( $data = $DB->fetch_array( $this->sql_query ) )
			{

				$data = $this->convertvalue( $data, 1 );
				
				$output .= $this->html->order_record( $data );
			}
		}
		else
		{
			$output .= $CMS->lang['order_no_data'];
		}
				
		$CMS->global->html['order_list'] = $output;
	}
	
	//===========================================================================
	//  LOAD OTHER order
	//===========================================================================
	
	public function load_order_other( $data )
	{
		global $CMS, $DB;
		
		$output = "";
		$sql_add = "";
		
		$sql_add .= ($this->order_project ? " AND order_project='{$this->order_project}' " : "");
		
		$sql = $DB->query("SELECT * FROM ".root_table."order WHERE order_id!='{$data['order_id']}' AND order_deleted=0 AND order_display=1 {$sql_add} ORDER BY order_time DESC LIMIT 10");

		while ( $data = $DB->fetch_array( $sql ) )
		{
			$data = $this->convertvalue( $data, 1 );
			
			$output .= $this->html->order_record_other( $data );
		}
		
		$CMS->global->html['order_other'] = $output;
	}
	
	//===========================================================================
	//  LOAD order
	//===========================================================================
	
	public function load_order_right()
	{
		global $CMS, $DB;
		
		// Print data
		$output = "";
		
		$result = $DB->query("SELECT * FROM ".root_table."order WHERE order_deleted=0 and order_project='web' ORDER BY order_id DESC LIMIT 8");

		if ( $DB->num_rows( $result ) > 0 )
		{
			while ( $data = $DB->fetch_array( $result ) )
			{
				$data = $this->convertvalue( $data, 1 );
				
				$output .= "
					<li>
						<a href='{$CMS->vars['root_domain']}/tin-tuc/{$data['order_shorturl']}'> {$data['order_name']} </a>						
					</li>	
						";
			}
		}
		else
		{
			$output .= $CMS->lang['order_no_data'];
		}
				
		return $output;
	}
	
	
	//===========================================================================
	//  LOAD OTHER order
	//===========================================================================
	
	public function load_cate_order(  )
	{
		global $CMS, $DB;
		
		$output = "";
	
		$sql = $DB->query("SELECT * FROM ".root_table."order_category WHERE cat_deleted = 0 ORDER BY cat_id DESC ");

		while ( $data = $DB->fetch_array( $sql ) )
		{
			$output .= "<option value=\"{$data['cat_id']}\">{$data['cat_name']}</option>";
			
		}
		
		return $output;
	}
	
	
	//===========================================================================
	//  LOAD  order Gan day
	//===========================================================================

	
	public function load_order_ganday( $order_id )
	{
		global $CMS, $DB;
		
		$output = "";
		// moi hon
		$sql = $DB->query("SELECT * FROM ".root_table."order WHERE order_id > '{$order_id}' AND order_active = 1 AND order_display = 1 AND cat_deleted = 0 ORDER BY order_id DESC LIMIT 5");

		while ( $data = $DB->fetch_array( $sql ) )
		{
			$output .= "<option value=\"{$data['cat_id']}\">{$data['cat_name']}</option>";
			
		}
		
		
		// Cu hon
		$sql_bk = $DB->query("SELECT * FROM ".root_table."order WHERE order_id < '{$order_id}' AND order_active = 1 AND order_display = 1 AND cat_deleted = 0 ORDER BY order_id DESC LIMIT 5");

		while ( $data = $DB->fetch_array( $sql ) )
		{
			$output .= "<option value=\"{$data['cat_id']}\">{$data['cat_name']}</option>";
			
		}

		return $output;
	}
	
	
	//===========================================================================
	//  LOAD  order Xem nhieu
	//===========================================================================
	
	public function load_order_xemnhieu( )
	{
		global $CMS, $DB;
		
		$sevenday = time() - (3*24*3600);
		
		$output = "";
		// moi hon
		$sql = $DB->query("SELECT * FROM ".root_table."order WHERE order_time > '{$sevenday}' AND order_active = 1 AND order_display = 1 AND cat_deleted = 0 ORDER BY order_views DESC LIMIT 5");

		while ( $data = $DB->fetch_array( $sql ) )
		{
			$output .= "<option value=\"{$data['cat_id']}\">{$data['cat_name']}</option>";
			
		}
		
	
		return $output;
	}
	
	/*<object width="560" height="315">
    <param name="movie" value="http://www.dailymotion.com/swf/order/order_ID?background=493D27&foreground=E8D9AC&highlight=FFFFF0"></param>
    <param name="allowFullScreen" value="true"></param>
    <param name="allowScriptAccess" value="always"></param>
    <embed type="application/x-shockwave-flash" src="http://www.dailymotion.com/swf/order/order_ID?background=493D27&foreground=E8D9AC&highlight=FFFFF0" width="560" height="315" allowfullscreen="true" allowscriptaccess="always"></embed>
	</object>*/
	
	
	/****************************************************************************
	* HTML EMBED MEDIA
	****************************************************************************/
	public function embed_code( $data = "", $type = 0 )
	{
			global $CMS, $member, $DB;
		 
			$output = "";
			$url = $data['order_url'];
			$post_url = parse_url($url);
			$host = preg_replace('/www./','',$post_url['path']);
			$host = substr($host,0, -6);
			$split = explode('v=',$post_url['query']);
			$split_1 = explode('&',$post_url['query']);
			 if(stristr($split_1[0], 'v=') == TRUE) 
			 {
				$split = explode('v=',$split_1[0]);
			}
			else
			{
				$split = explode('v=',$split_1[1]);
		    }	
			$link = $split[1];

            if($host == "www.youtube.com" || $host == "youtube.com" || $post_url['host'] == "www.youtube.com" || $post_url['host'] == "youtube.com")
			{
				if($type == 3)
				{
				$output .=<<<EOF
	
				
				  	  <object width="100%" height="70%">
					<param name="movie" value="http://www.youtube.com/v/{$link}" />
					<embed src="http://www.youtube.com/v/{$link}"
					  type="application/x-shockwave-flash" width="640" height="240" />
				</object>
EOF;
				}
				else
				{
					$output .=<<<EOF
							  <object width="100%" height="100%">
					<param name="movie" value="http://www.youtube.com/v/{$link}" />
					<embed src="http://www.youtube.com/v/{$link}"
					  type="application/x-shockwave-flash" width="240" height="240" />
				</object>
				 
EOF;
				}
			}
			elseif($post_url['host'] == "www.dailymotion.com" || $post_url['host'] == "dailymotion.com" || $host == "www.dailymotion.com" || $host == "dailymotion.com" )
			 {
				$bk = parse_url($url);
				// The part you want
				$url= $bk['path'];
				$parts = explode('/',$url);
				$parts = explode('_',$parts[2]);
				if($type == 3)
				{
				$output .=<<<EOF
				<object width="640" height="440">
					<param name="movie" value="http://www.dailymotion.com/swf/order/{$parts[0]}?background=493D27&foreground=E8D9AC&highlight=FFFFF0"></param>
					<param name="allowFullScreen" value="true"></param>
					<param name="allowScriptAccess" value="always"></param>
					<embed type="application/x-shockwave-flash" src="http://www.dailymotion.com/swf/order/{$parts[0]}?background=493D27&foreground=E8D9AC&highlight=FFFFF0" width="560" height="315" allowfullscreen="true" allowscriptaccess="always"></embed>
				</object>

EOF;
				}
				else
				{
						$output .=<<<EOF
			
				<object width="240" height="240">
					<param name="movie" value="http://www.dailymotion.com/swf/order/{$parts[0]}?background=493D27&foreground=E8D9AC&highlight=FFFFF0"></param>
					<param name="allowFullScreen" value="true"></param>
					<param name="allowScriptAccess" value="always"></param>
					<embed type="application/x-shockwave-flash" src="http://www.dailymotion.com/swf/order/{$parts[0]}?background=493D27&foreground=E8D9AC&highlight=FFFFF0" width="560" height="315" allowfullscreen="true" allowscriptaccess="always"></embed>
				</object>
EOF;
				}
			}
			else
			 {
				 
				 $output .=<<<EOF
			  <embed width="450" height="350" type="application/x-shockwave-flash" src="{$CMS->vars['public_url']}/player/player.swf" flashvars="skin={$CMS->vars['public_url']}/player/blueratio/blueratio.xml&amp;file={$CMS->vars['upload_url']}/order/file/{$data['file_location']}&amp;playlistsize=100&amp;image={$CMS->vars['upload_url']}/order/images/{$data['image_location']}&amp;logo={$CMS->vars['upload_url']}/order/images/{$data['image_location ']}&amp;autostart=false&amp;shuffle=true&amp;repeat=list" allowfullscreen="true">
</embed>

  
			
EOF;
		     }
			 
			// Get image url 
			
			if($type == 1)
			{
				if($host == "www.youtube.com" || $host == "youtube.com" || $post_url['host'] == "www.youtube.com" || $post_url['host'] == "youtube.com")
				{
					$img = "http://img.youtube.com/vi/{$link}/0.jpg";
				}
				else
				{
					if($data['image_location'] != '')
					{
						$img = "{$CMS->vars['upload_url']}/order/images/{$data['image_location']}";
					
					}
					else
					{
						$img = "{$CMS->vars['root_domain']}/templates/images/no-image.jpg";
					}
				}
				return $img;
				
			}

		
			return $output;
		}
		
		
		//===========================================================================
	//  Get tags
	//===========================================================================
	
	public function get_tags( $tags_id = "")
	{
		global $CMS, $DB;

        $output = "";

		$tags_array = explode(",",$tags_id);
	 
		for($i = 0; $i<= count($tags_array); $i++)
		{
			if($tags_array[$i] != "")
			{
				$output .=<<<EOF
				<li class="tag" onClick="return del_tags($i);"  id="tags_$i">$tags_array[$i]<input type="hidden" name="order_tags_id[]" value="{$tags_array[$i]}"/><a class="close" href="javascript: void();">close</a></li>
					
EOF;
			}
		}
		return $output;
	}
	
	
		
	 //===========================================================================
	//  Get  
	//===========================================================================
	
	
	public function inserted_record()
	{
		global $CMS, $DB, $member;

		// Check record
		if ( ! $this->token_key )
		{
			return false;
		}

		$sql = $DB->query("SELECT * FROM ".root_table."order WHERE ord_key = '{$this->token_key}' AND ord_deleted=0 ORDER BY ord_id DESC LIMIT 1");

		if ( $DB->num_rows($sql) > 0 )
		{
			$data = $DB->fetch_array($sql);

			return $data;
		}
		else
		{
			return false;
		}
	}
	 
	
		
	//===========================================================================
	//  Lcheck duplicate
	//===========================================================================

	public function check_duplicate($array = "", $id ="")
	{
		global $CMS, $DB, $member;
		$arr = explode(",",$array);
		
		for($i = 0; $i<= count($arr);$i++)
		{
			if($arr[$i]!= "")
			{
				if($arr[$i] == $id)
				{
					return true;
				}
			}
		}
		return false;
	}
	
	
	
		
	//===========================================================================
	//  Loaf danh sach order theo Tags
	//===========================================================================
	
	public function order_by_tags( $tags = "" )
	{
		global $CMS, $DB;
		
		$output = "";
	
		list($CMS->show_page_order, $this->sql_query) = $CMS->class->page->create("SELECT * FROM ".root_table."order WHERE order_tags_id LIKE  '%{$tags['tags_name']}%' AND order_active = 1 AND order_deleted = 0  AND order_display = 1 ORDER BY order_id DESC ", 20, "order/tags/{$tags['tags_url']}/{$tags['tags_id']}/", ".html");
 	
		if($DB->num_rows( $this->sql_query) > 0 )
		{
			$i = 0;
			while ( $data = $DB->fetch_array( $this->sql_query) )
			{
				$data = $this->convertvalue($data);
					if($i % 2 == 0)
					{
					$output .= <<<EOF
						 
				     <div class="span6 post no-margin-left">
						  <figure>
						   		<a href="{$CMS->vars['root_domain']}/order/detail/{$data['order_shorturl']}/{$data['order_id']}.html"  alt="{$data['order_name']}"><img  src="{$data['order_thumb']}" alt="{$data['order_name']}" title="{$data['order_name']}"/></a>
                			 <span class="btn-hover"></span>
						  </figure>
						 
						  <div class="text">
						   <h2><a href="{$CMS->vars['root_domain']}/order/detail/{$data['order_shorturl']}/{$data['order_id']}.html" title="{$data['order_name']}">{$data['order_name']}</a></h2>
						  
						  </div>
					</div>
   	 
EOF;
					}
					if($i % 2 == 1)
					{
					$output .= <<<EOF
						 
				     <div class="span6 post">
						  <figure>
						   		<a href="{$CMS->vars['root_domain']}/order/detail/{$data['order_shorturl']}/{$data['order_id']}.html"  alt="{$data['order_name']}"><img  src="{$data['order_thumb']}"  alt="{$data['order_name']}" title="{$data['order_name']}"/></a>
                			 <span class="btn-hover"></span>
						  </figure>
						  <div class="text">
						   <h2><a href="{$CMS->vars['root_domain']}/order/detail/{$data['order_shorturl']}/{$data['order_id']}"  alt="{$data['order_name']}" title="{$data['order_name']}">{$data['order_name']}</a></h2>
						  
						  </div>
					</div>
   	 				<div class="clearfix ie-sep"></div> <!-- Clearfix -->
EOF;
					}
				
				$i++;
				
			}
		}
		$output .="<div class=\"clearfix ie-sep\"></div> <!-- Clearfix -->";

		return $output;
	}
	
	//===========================================================================
	//  LOAD  order lien quan
	//===========================================================================
	public function load_order_lienquan( $data )
	{
		global $CMS, $DB;
		$output = "";

		if($data['order_tags_id'] == "")
		{
			return false;
		}
     
		$array = explode(",",$data['order_tags_id']);
		$temp = array();
		for($i = 0; $i <= count($array);$i++)
		{
			if($array[$i] != "")
			{
				$sql = $DB->query("SELECT * FROM ".root_table."order WHERE order_tags_id LIKE '%,{$array[$i]},%' AND order_id <> '{$data['order_id']}' AND order_active = 1 AND order_display = 1 AND order_deleted = 0 ORDER BY order_id DESC LIMIT 5");
				if($DB->num_rows($sql) > 0)
				{
					while($result = $DB->fetch_array($sql))
					{
						$result = $this->convertvalue($result);	
						$temp = array_merge($temp,array($result));
					}
				}
			}
		}
		$order_id = "";
		for($m = 0;$m <= count($temp); $m++)
		{
			if($CMS->news->check_exits_inarray($order_id,$temp[$m]['order_id']) == FALSE)
			{
				 unset($temp[$m]);
			}
			$order_id = $order_id.",".$temp[$m]['order_id'];
		}
		
	
		if(count($temp) > 0){$output .= " <ul class=\"detail_moreorder\">";}
        if(count($temp) > 0)
		{
            $output .= " <li class=\"title\">order liên quan </li>";
            for($j = 0;$j <= 5; $j++)
            {
                if($temp[$j]['order_id'] != "" AND $temp[$j]['order_id'] != $data['order_id'])
                {
                $output .=<<<EOF
                <li><a href="{$CMS->vars['root_domain']}/order/detail/{$temp[$j]['order_shorturl']}/{$temp[$j]['order_id']}.html" alt="{$temp[$j]['order_name']}">{$temp[$j]['order_name']}</a><span></span></li>
             
EOF;
                }
            }
        }    
		if(count($temp) > 0){$output .= " </ul>";}	
		return $output;
	}
	
 
	 

	function generateRandomString($length = 12) 
	{
	    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';//abcdefghijklmnopqrstuvwxyz
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

	public function check_name_exist($ord_name='')
	{
		global $CMS, $DB;

		$sql = $DB->query("SELECT 0 FROM ".root_table."order WHERE ord_name = '{$ord_name}'");
		if($DB->num_rows($sql) > 0)
		{
			return true;
		}else
		{
			return false;
		}
	}
		
	 
	public function get_order_by_transaction($transaction_id=0)
	{
		global $CMS, $DB;

		$data = array();
		$sql = $DB->query("SELECT * FROM ".root_table."order WHERE transaction_id = '{$transaction_id}' AND ord_deleted = 0");
		if($DB->num_rows($sql) > 0)
		{
			while ($result = $DB->fetch_array($sql)) 
			{
				$data[] = $result;
			}
		}

		return $data;
	}

	 
	  
  
 
	public function listing() {
		global $CMS,$DB,$member;
		$this->loadhtml();
		
		$default_order = intval($CMS->input['order_by']) == 1 ? "ASC" : "DESC";
		if($CMS->input['sort'] == 1)
		{
			$default_field = "O.ord_id";
		}elseif($CMS->input['sort'] == 2)
		{
			$default_field = "O.ord_status";
		}elseif($CMS->input['sort'] == 6)
		{
			$default_field = "O.ord_status";
		}else
		{
			$default_field = "O.ord_id";
		}

		$where='';
		$check_search = intval($CMS->input['check_search']);
		if (!empty($CMS->input['id'])) {
			$where.=" AND O.ord_id = '{$CMS->input['id']}'";
			$this->prefix_html.="&id={$CMS->input['id']}";
		}
		if (!empty($CMS->input['ord_name'])) {
			$s_ord_name = urldecode($CMS->input['ord_name']);
			$where.=" AND ( O.ord_name LIKE '%{$s_ord_name}%' OR OI.ordi_name LIKE '%{$s_ord_name}%' ) ";
			$this->prefix_html.="&ord_name={$CMS->input['ord_name']}";
			$check_search = 1;
		}
		
		// if (!empty($CMS->input['user'])) {
		// 	$where.=" AND C.cus_username LIKE '%{$CMS->input['user']}%'";
		// 	$this->prefix_html.="&user={$CMS->input['user']}";
		// }
		// 
		// $CMS->input['status'] = null;
		if (!empty($CMS->input['status']) or $CMS->input['status'] != '') {
			$where.=" AND O.ord_status='{$CMS->input['status']}'";
			$this->prefix_html.="&status={$CMS->input['status']}";
			$check_search = 1;
		}

		if (!empty($CMS->input['custom_status']) or $CMS->input['custom_status'] != '') {
			$where.=" AND O.ord_status_custom='{$CMS->input['custom_status']}'";
			$this->prefix_html.="&custom_status={$CMS->input['custom_status']}";
			$check_search = 1;
		}

		if( isset($CMS->input['trx_id']) )
		{
			if( is_numeric($CMS->input['trx_id']) )
			{
				$trx_id = $CMS->input['trx_id'];
			}
			else if( $CMS->input['trx_id'] )
			{
				$trx_id = $CMS->transactions->getInfoSearch($CMS->input['trx_id'], 'trx_id');
			}

			if( isset($trx_id) )
			{
				$where.=" AND O.transaction_id='{$trx_id}'";
				$this->prefix_html.="&transaction_id={$CMS->input['trx_id']}";
				$check_search = 1;
			}
		}

		if (!empty($CMS->input['store_id']) or $CMS->input['store_id'] != '') {
			$where.=" AND O.store_id='{$CMS->input['store_id']}'";
			$this->prefix_html.="&store_id={$CMS->input['store_id']}";
			$check_search = 1;
		}

		if( isset($CMS->input['cus_id']) ) 
		{
			if( is_numeric($CMS->input['cus_id']) )
			{
				$where.=" AND O.cus_id='{$CMS->input['cus_id']}'";
				$this->prefix_html.="&cus_id={$CMS->input['cus_id']}";
				$check_search = 1;
			}
			else if( $CMS->input['cus_id'] )
			{
				$s_cus_id = urldecode($CMS->input['cus_id']);
				$where.=" AND ( C.cus_full_name LIKE '%{$s_cus_id}%' OR SA.bill_full_name LIKE '%{$s_cus_id}%' OR SA.ship_full_name LIKE '%{$s_cus_id}%' ) ";
				$this->prefix_html.="&cus_id={$CMS->input['cus_id']}";
				$check_search = 1;
			}
		}

		if (!empty($CMS->input['user_id']) or $CMS->input['user_id'] != '') {
			$where.=" AND O.user_id='{$CMS->input['user_id']}'";
			$this->prefix_html.="&user_id={$CMS->input['user_id']}";
			$check_search = 1;
		}

		if (!empty($CMS->input['payment_method']) or $CMS->input['payment_method'] != '') {
			$where.=" AND O.payment_method='{$CMS->input['payment_method']}'";
			$this->prefix_html.="&payment_method={$CMS->input['payment_method']}";
			$check_search = 1;
		}


		if (!empty($CMS->input['total_from'])&&$CMS->input['total_from']>0) {
			$where.=" AND O.ord_total >='{$CMS->input['total_from']}'";
			$this->prefix_html.="&total_from={$CMS->input['total_from']}";
			$check_search = 1;
		}
		if (!empty($CMS->input['total_to'])&&$CMS->input['total_to']>0) {
			$where.=" AND O.ord_total <='{$CMS->input['total_to']}'";
			$this->prefix_html.="&total_to={$CMS->input['total_to']}";
			$check_search = 1;
		}
		if (!empty($CMS->input['time_from'])) {
			$where.=" AND O.ord_time >='{$CMS->input['time_from']}'";
			$this->prefix_html.="&time_from={$CMS->input['time_from']}";
			$check_search = 1;
		}
		if (!empty($CMS->input['time_to'])) {
			$where.=" AND O.ord_time <='{$CMS->input['time_to']}'";
			$this->prefix_html.="&time_to={$CMS->input['time_to']}";
			$check_search = 1;
		}

        if (!empty($CMS->input['ord_booking_phone']) or $CMS->input['ord_booking_phone'] != '') {
            $where.=" AND O.ord_booking_phone='{$CMS->input['ord_booking_phone']}'";
            $this->prefix_html.="&ord_booking_phone={$CMS->input['ord_booking_phone']}";
            $check_search = 1;
        }

		if($CMS->input['sort'])
		{
			$this->prefix_html.= "&sort={$CMS->input['sort']}";
		}

		if($CMS->input['order_by'])
		{
			$this->prefix_html.= "&by={$CMS->input['order_by']}";
		}

		if(!empty($CMS->input['list_status']))
		{
			$new_clause = "";
			$liststatus = explode(",", ltrim($CMS->input['list_status'], ","));
			foreach ($liststatus as $status) 
			{
				if(!isset($status)) { continue;}
				$new_clause.=" O.ord_status_custom ='{$status}' OR ";
			}
			$new_clause = rtrim($new_clause, " OR ");

			$where.= $new_clause ? " AND (".$new_clause.") " : "";
			$this->prefix_html .= "&list_status=".urlencode($CMS->input['list_status']);
		}

		if( isset($CMS->input['time_from_to']) ) 
		{
			$start_end = explode('-', urldecode($CMS->input['time_from_to']));
			$start = isset($start_end['0']) ? $start_end['0'] : 0;
			$start = is_numeric($start) ? $start : $CMS->class->date->date2time($start);
			$end   = isset($start_end['1']) ? $start_end['1'] : 0;
			$end   = is_numeric($end) ? $end : $CMS->class->date->date2time($end);

			if( $start > 0 )
			{
				$where.=" AND O.ord_time >='{$start}'";
				$check_search = 1;
			}

			if( $end > 0 )
			{
				$end  += (60*60*24)-1; // in day
				$where.=" AND O.ord_time <='{$end}'";
				$check_search = 1;
			}
			$this->prefix_html.="&time_from_to={$CMS->input['time_from_to']}";
		}

		if($check_search)
		{
			$this->prefix_html.="&check_search={$check_search}";
		}
		
		$this->prefix_html=empty($this->prefix_html)?'':'?site=order'.$this->prefix_html.'&page=';
		//$this->arrange_data = trim(); // Remove by LHL

		$sql = "
		SELECT O.*, SA.bill_full_name, SA.ship_full_name FROM ".root_table."order O LEFT JOIN ".root_table."order_item OI ON  O.ord_id = OI.ord_id LEFT JOIN ".root_table."customer C ON O.cus_id = C.cus_id LEFT JOIN ".root_table."shipbill_address SA ON O.ord_id = SA.ord_id 
		WHERE O.ord_deleted = 0 {$where} 
		GROUP BY O.ord_id 
		ORDER BY {$default_field} {$default_order} 
		";
		
		list($this->show_page, $this->sql_query) = $CMS->class->page->create($sql, $this->per_page, $this->prefix_html, $this->suffix_html);
		
			$CMS->output.=$this->html->head();
		
		if ($DB->num_rows($this->sql_query)>0) {

			while( $data=$DB->fetch_array($this->sql_query)) {
				$CMS->output.=$this->html->mid($this->convertvalue($data));
			}
		} else {
			 
		}
		$CMS->output.=$this->html->foot();
	}
	  
	public function convertvalue($data) {
		global $CMS, $DB, $member;

		$data['data_bk'] = $data;
	 	
	 	$data['ord_name_bk'] = $data['ord_name'] != "" ? "<a href='{$CMS->vars['root_domain']}/?site=order&act=show&id={$data['ord_id']}'>{$data['ord_name']}</a>" : "<a href='{$CMS->vars['root_domain']}/?site=order&act=show&id={$data['ord_id']}'>{$data['ord_id']}</a>";
		$data['ord_time_bk']=$CMS->class->date->date_format($data['ord_time'],1); 
		if($data['ord_time_update'] > 0)
		{
			$data['ord_time_update_bk']=$CMS->class->date->date_format($data['ord_time_update'],1);
		}
		else{
			$data['ord_time_update_bk']= "<span style='font-weight:italic'>N/A</span>";
		} 

		$data['booking_date_bk']= $data['booking_date'] == 0 ? "N/A": $CMS->class->date->date_format($data['booking_date'],2);
		$customer = $CMS->customer->getInfo($data['cus_id']);
		if($CMS->permit['customer_read'] == 1)
		{
			$data['cus_name_bk'] = "<a style='display: inline; max-width: 100%; width: 100%' class='link-text' href=\"{$CMS->vars['root_domain']}/?site=customer&act=show&id={$customer['cus_id']}\">{$customer['cus_full_name']}</a> <a style='display: inline' href='{$CMS->vars['root_domain']}/?site=order&cus_id={$customer['cus_id']}'><i class=\"fa fa-filter pull-right\" aria-hidden=\"true\"></i></a>";
			$data['cus_name_show'] = $customer['cus_full_name'];
		}
		else
		{
			$data['cus_name_show'] = $data['cus_name_bk'] = $customer['cus_full_name'];
		}
		$data['customer'] = $customer;

		// Guest
		if( !$customer )
		{
			if( !isset($data['bill_full_name']) AND !isset($data['ship_full_name']) )
			{
				list($ship, $bill, $sp_id) = $this->getShipBillByOrder($data['ord_id']);
				$data['bill_full_name'] = $bill['full_name'];
				$data['ship_full_name'] = $ship['full_name'];
			}

			$data['cus_name_show'] = $data['cus_name_bk'] = ($data['bill_full_name'] ? $data['bill_full_name'] : $data['ship_full_name']) . ' (<small>Guest</small>)';
		}

		$user = $CMS->user->get_info($data['user_id']);
		if($CMS->permit['user_read'] == 1)
		{
			$data['user_name_bk'] = "<a class='link-text' href=\"{$CMS->vars['root_domain']}/?site=user&act=show&id={$user['user_id']}\">{$user['user_display_name']}</a>";
			$data['user_name_show'] = $user['user_display_name'];

		}
		else
		{
			$data['user_name_show'] = $data['user_name_bk'] = $user['user_display_name'];
		}

        // Order status
		switch ($data['ord_status']) {
			case '0':
				$data['ord_status_n']= "<span class='label label-default'>{$CMS->lang['order_status_0']}</span>";
				$data['ord_status_text']= $CMS->lang['order_status_0'];
				break;
			case '1':
				$data['ord_status_n']= "<span class='label label-warning'>{$CMS->lang['order_status_1']}</span>";
				$data['ord_status_text']= $CMS->lang['order_status_1'];
				break;
			case '2':
				$data['ord_status_n']= "<span class='label label-success'>{$CMS->lang['order_status_2']}</span>";
				$data['ord_status_text']= $CMS->lang['order_status_2'];
				break;
			case '3':
				$data['ord_status_n']= "<span class='label label-danger'>{$CMS->lang['order_status_3']}</span>";
				$data['ord_status_text']= $CMS->lang['order_status_3'];
				break;	
			case '4':
				$data['ord_status_n']= "<span class='label label-pink'>{$CMS->lang['order_status_4']}</span>";
				$data['ord_status_text']= $CMS->lang['order_status_4'];
				break;				
		}

		 
		
        // Payment status, again?
		switch ($data['payment_status']) {
			case '0':
				$data['payment_status_bk'] = "<span class='label label-default'>{$CMS->lang['payment_status_0']}</span>";
				$data['payment_status_text'] = $CMS->lang['payment_status_0'];
				break;
			case '1':
				$data['payment_status_bk'] = "<span class='label label-primary'>{$CMS->lang['payment_status_1']}</span>";
				$data['payment_status_text'] = $CMS->lang['payment_status_1'];
				break;
			 case '2':
				$data['payment_status_bk'] = "<span class='label label-warning'>{$CMS->lang['payment_status_2']}</span>";
				$data['payment_status_text'] = $CMS->lang['payment_status_2'];
				break;
		 	 case '3':
				$data['payment_status_bk'] = "<span class='label label-danger'>{$CMS->lang['payment_status_3']}</span>";
				$data['payment_status_text'] = $CMS->lang['payment_status_3'];
				break;
		}

		// Convert booking hours
		if($data['booking_hours'] != "")
		{
		   if($CMS->vars['hours_time_format'] == 12)
		   {
		   		$hrs = explode(":",$data['booking_hours']);
        		$data['booking_hours_bk'] = $hrs[0] > 12 ? ($hrs[0] - 12).":".$hrs[1] : $data['booking_hours'];
		   }
		   else
		   {
		   	  $data['booking_hours_bk'] = 	$data['booking_hours'];
		   }
		  
		}
 
		 // Get transaction
		$trx = $CMS->transactions->getInfo($data['transaction_id']);
		$data['trx'] = $trx;
		if($CMS->permit['transactions_read'] == 1)
		{
			 if(is_array($trx))
			 {
				$data['trx_name_ls'] = $data['trx_name'] = "<a class='link-text' href=\"{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=1&act=show&id={$data['transaction_id']}\">{$trx['trx_code']}</a>";
				$data['trx_name_text'] =  $trx['trx_code'];
			 }
			 else
			 {
			 	$data['trx_name_text'] = $data['trx_name'] =  "N/A";
			 	$data['trx_name_ls'] = "";
			 }
		}
		else
		{ 
			 if(is_array($trx))
			 {
				$data['trx_name_text'] = $data['trx_name_ls'] = $data['trx_name'] =  $trx['trx_code'];
			}
			else
			{
				$data['trx_name_text'] = $data['trx_name'] =  "N/A";
				$data['trx_name_ls'] = "";
			}
			
		}
 
		//Get list item
        $li_list = "";
        $li_title = "";
		$list_item = $this->get_item($data['ord_id'], "");

		if(count($list_item) > 0)
		{
			
			for($i = 0; $i<= count($list_item); $i ++)
			{
				if($list_item[$i]['product_name'] != "" AND $i<= 1)
				{
					$product_name_bk = $CMS->class->editor->substr($list_item[$i]['product_name'],0,21);
					$li_list .= "<li><a href='{$CMS->vars['root_domain']}/?site=product&act=show&id={$list_item[$i]['product_id']}' title='{$list_item[$i]['product_name']}'>- {$product_name_bk}</a></li>";
				}
				 
				if($i < count($list_item)-1)
				{
					$product_name_bk = $CMS->class->editor->substr($list_item[$i]['product_name'],0,21);
					$li_title .= "{$product_name_bk}, "; 
				}
				else
				{	$product_name_bk = $CMS->class->editor->substr($list_item[$i]['product_name'],0,21);
				 	$li_title .= "{$product_name_bk}  "; 
				}
				
			 
			}
			$data['product_name'] .= '<ul data-toggle="tooltip" data-placement="bottom" title="'.$li_title.'">'.$li_list.'</ul>';
		}
		

		$store = $CMS->store->get_info($data['store_id']);

		if(is_array($store))
		{
			if($CMS->permit['assets_read'] == 1)
			{
				$data['store_name'] =  "<a class='link-text' href=\"{$CMS->vars['root_domain']}/?site=assets&store_id={$data['store_id']}\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"{$CMS->lang['store_name']}: {$store['store_name']}\">{$store['store_name']}</a>";
			}
			else
			{
				$data['store_name'] = "<span data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"{$CMS->lang['store_name']}: {$store['store_name']}\">{$store['store_name']}</span>"; 
			}
		}
		else
		{
			$data['store_name'] = "...";
		}

		if($data['account_id'] > 0)
		{
			$account = $CMS->accounts->getInfo($data['account_id']);
			if(is_array($account))
			{
				if($CMS->permit['accounts_read'] == 1)
				{
					$data['account_name'] =  "<a class='link-text' href=\"{$CMS->vars['root_domain']}/?site=accounts&act=show&id={$account['accounts_id']}\">{$account['accounts_name']}</a>";
				}
				else
				{
					$data['account_name'] = $account['accounts_name']; 
				}
			}
		}

		$data['ord_amount_n']=  $CMS->class->input->currency($data['ord_amount']);
		$data['ord_total_n']=  $CMS->class->input->currency($data['ord_total']);
		$data['ord_tax_n']=  $CMS->class->input->currency($data['ord_tax']);
		$data['ord_discount_n']=  $CMS->class->input->currency($data['ord_discount']);
		$data['ord_total_discount_n']=  $CMS->class->input->currency($data['ord_total_discount']);
		 
		$data['is_shipping_bk'] = $data['is_shipping'] == 1 ? "{$CMS->lang['yes']}" : "{$CMS->lang['no']}"; 
	 
		$data['payment_mothod_bk'] = "{$CMS->lang["payment_method_{$data['payment_method']}"]}";
		$data['service_type_bk'] = "{$CMS->lang["service_type_{$data['service_type']}"]}";
		switch ($data['payment_method']) {
			case '0':
				$data['payment_method_icon'] = "<i class='fa fa-money' style='float:left;padding-right:10px;padding-top:7px;color:#b9b9b9; text-align: center; '></i>";
				break;
			case '1':
				$data['payment_method_icon'] = "<i class='fa fa-bank' style='float:left;padding-right:10px;padding-top:7px;color:#b9b9b9; text-align: center; '></i>";				
				break;
		}


		$data['ord_cus_city_n'] = $data['ord_cus_city'];
		$data['ord_cus_district_n'] = $data['ord_cus_province'];

		 // Check don hang shipping
		if($data['is_shipping'] == 1)
		{
			//Convert partner_delivery
			$ship = json_decode($data['shipping_info'],true);
			if($ship['ship_deliver'] > 0)
			{
				$partner_delivery = $CMS->partner_delivery->get_info($ship['ship_deliver']);
				if(is_array($partner_delivery))
				{
					if($CMS->permit["partner_delivery_read"] == 1)
					{
						$data['ship_deliver_bk'] = "<a class='link-text' href='{$CMS->vars['root_domain']}/?site=partner_delivery&act=show&id={$ship['ship_deliver']}' >{$partner_delivery['p_delivery_name']}</a>";
					}
					else
					{
							$data['ship_deliver_bk'] = $partner_delivery['p_delivery_name'];
					}
				}
			}

			$data['ship_service_type_bk'] = "{$CMS->lang["ship_service_type_{$ship['ship_service_type']}"]}";

		}
		$data['data_cnt'] = $this->record_cnt++;
		$data['ord_commission_rating'] = $CMS->class->input->number($data['data_bk']['ord_commission_rating'])."%";
		$data['ord_commission'] = $CMS->class->input->currency(($data['data_bk']['ord_commission'] * $data['data_bk']['ord_commission_rating'])/100);

		$ord_time_bk = explode(" ", $data['ord_time_bk']);
		$data['ord_time_bk_date'] = $ord_time_bk[0];
		unset($ord_time_bk[0]);
		$data['ord_time_bk_hours'] = implode(" ", $ord_time_bk);

		// Shipping
		$data['ord_fee_shipping_n'] = $CMS->class->input->currency($data['ord_fee_shipping']);
		$data['ord_shipping_method_n'] = $CMS->lang["ord_shipping_method_{$data['ord_shipping_method']}"];
		$data['ord_shipping_location_n'] = $CMS->lang["ord_shipping_location_{$data['ord_shipping_location']}"];

		// dump($data);die;
		return $data;
	}
	public function show() {
		global $CMS, $DB, $member;
		$this->loadhtml();

		$sql1=$DB->query("SELECT * FROM `".root_table."order` o INNER JOIN `".root_table."customer` c ON c.cus_id=o.cus_id WHERE `ord_deleted`=0 AND o.`ord_id`={$CMS->input['id']} LIMIT 1");
		if ($DB->num_rows($sql1)>0) {
			while( $data=$DB->fetch_array($sql1)) {
				
				$data['content']=array();
				$sql2=$DB->query("SELECT * FROM `".root_table."order_content` o WHERE o.`ord_id`={$data['ord_id']} AND ordc_deleted = 0");
				if ($DB->num_rows($sql2)>0) {
					while ($result2=$DB->fetch_array($sql2)) {
						array_push($data['content'],$result2);
					}
				}

				$CMS->output.=$this->html->show($this->convertvalue($data));
			}
		}

	}
 
	public function sell_show() {
		global $CMS, $DB, $member;
		$DB->query("SELECT * from `".root_table."orders` WHERE `id`='{$CMS->input['id']}' AND cus_id ='{$member['cus_id']}' LIMIT 1");
		if ($DB->num_rows()>0) 
		{
			$data=$DB->fetch_array();
			$data['line']=$data['ship']=$data['bill']=array();
			$sql1=$DB->query("SELECT * from `".root_table."order_line` WHERE `deleted`=0 AND `order_id`='{$data['id']}' AND `cus_id`='{$member['cus_id']}'");
			if ($DB->num_rows($sql1)>0) 
			{
				while ($tmp=$DB->fetch_array($sql1)) 
				{
					array_push($data['line'],$tmp);
				}
			}
			$sql2=$DB->query("SELECT * from `".root_table."order_ship_bill` WHERE `deleted`=0 AND `order_id`='{$data['id']}'");
			if ($DB->num_rows($sql2)>0) {
				while ($tmp=$DB->fetch_array($sql2)) {
					switch ($tmp['type']) {
						default:
							array_push($data['ship'],$tmp);
							array_push($data['bill'],$tmp);
							break;
						case '1':
							array_push($data['ship'],$tmp);
							break;
						case '2':
							array_push($data['bill'],$tmp);
							break;
					}
				}
			}
			$this->loadhtml();
			$CMS->output.=$this->html->show($data);
			
		}
	}
 
	public function acpDel() {
		global $CMS, $DB, $member;

		if($CMS->input['id'] == "" OR !isset($CMS->input['id']))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['choice_order_delete']}";
		}
		else
		{
			$order = $this->get_info($CMS->input['id']);
			 
			if(is_array($order))
			{
				$DB->query("UPDATE `".root_table."order` SET `ord_deleted`=1 WHERE `ord_id`={$CMS->input['id']}");
					// Xoa transaction tuong ung
					// 
				if($order['transaction_id'] > 0)
				{
					$CMS->transactions->delete($order['transaction_id'] );
				}
				

				// Create log
				$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['deleted']} : <b>{$order['ord_name']}</b> {$CMS->lang['is_success']}")."<br />";
			}
			else
			{ 
				$_SESSION['error_msg'] = "{$CMS->lnag['choice_order_delete']}";
			}
 
		}
		
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=order");
	}


	public function acpEdit($data=null) 
	{
		global $CMS,$DB,$member;
 
		$id=$CMS->input['id'];
	 
		$ord_status = $CMS->input['ord_status'];
		$ord_note = $CMS->class->editor->input('ord_note');
 		
		//update
		$data = $this->get_info($id);
		if(!is_array($data))
		{
			$_SESSION['error_msg']=$CMS->lang['order_edit_error'];
		}
		$CMS->class->logs->key = "edit_order_{$id}";
		$CMS->class->logs->old_data = $data;
	 
		
		$count = $DB->query("UPDATE `".root_table."order` SET `ord_status`='{$ord_status}', `ord_note`='{$ord_note}'  WHERE `ord_id`={$id}");
		 
		   
		$CMS->class->logs->insert("edit_order_{$id}");
		$CMS->class->logs->key = "edit_order_{$id}";
		$CMS->class->logs->save_detail("order",$id,$this->get_info($id));
		$_SESSION['msg']=$CMS->lang['order_edit_success'];
		return true;
	}


	public function refund()
	{
		global $CMS, $DB;

		$data = $this->get_info($CMS->input['id']);
		// $refund_amount = $CMS->input['refund_amount'];
		$refund_amount = $data['ord_total']; // Refund toàn bộ
		$note_refund = $CMS->input['reason_refund'];
		if($refund_amount < 0)
		{
			$_SESSION['msg'] = "{$CMS->lang['error_refund_amount_less_than_0']}";
			return false;
		}

		if($refund_amount > $data['ord_total'])
		{
			$_SESSION['msg'] = "{$CMS->lang['error_refund_amount_greater_than_total']}";
			return false;
		}

		if(!$note_refund)
		{
			$_SESSION['msg'] = "{$CMS->lang['error_note_refund']}";
			return false;
		}

		$transaction_content = "{$CMS->lang['note_refund_order']}";
		$transaction_time = time();
		$transaction_datetime = date("Y/m/d",$transaction_time);
		$transaction_datetime = strtotime($transaction_datetime);
		$transaction_ip_address = $_SERVER['REMOTE_ADDR'];
		// INSERT TRANSACTION
		$count = $DB->query("INSERT INTO ".root_table."transaction (transaction_name, transaction_content, transaction_datetime, transaction_time, transaction_total, transaction_type, transaction_ip_address, transaction_status, transaction_parent) VALUES ('', '{$transaction_content}', '{$transaction_datetime}', '{$transaction_time}', '{$refund_amount}', 3, '{$transaction_ip_address}', 0, '{$data['transaction_id']}')");
		$transaction_id = $DB->last_insert_id();
		//Update transaction_name
		$DB->query("UPDATE ".root_table."transaction SET transaction_name = CONCAT('TRX',RIGHT(CONCAT('000000000',transaction_id),9)) WHERE  transaction_id = '{$transaction_id}' ");

		if($count)
		{
			// Update trạng thái đơn hàng thành huỷ
			// $DB->query("UPDATE ".root_table."order SET ord_status = 3, ord_update_time = '{$transaction_time}' WHERE ord_id = '{$data['ord_id']}'");
			return $transaction_id;
		}else
		{
			return false;
		}

	}
 

	 
 

	public function count_order($cus_id=0, $type="")
	{
		global $CMS, $DB;

		$count = 0;
		$clause = "";
		if($cus_id)
		{
			$clause .= " AND cus_id = '{$cus_id}' ";
		}

		$sql = $DB->query("SELECT COUNT(ord_id) as number_order FROM ".root_table."order WHERE ord_deleted = 0 {$clause}");

		$result = $DB->fetch_array($sql);
		$count = $result['number_order'];
		

		return $count;
	}

 
	            
	public function checkOrderBymember($order_id=0, $member_id=0)
	{
		global $CMS, $DB, $member;

		$sql = $DB->query("SELECT 0 FROM ".root_table."orders WHERE id = '{$order_id}' AND cus_id = '{$member_id}'");
		if($DB->num_rows($sql) > 0)
		{
			return true;
		}else
		{
			return false;
		}
	}
	public function cancelOrder($order_id=0, $member_id=0)
	{
		global $CMS, $DB, $member;

		$count = $DB->query("UPDATE ".root_table."orders SET status = 2 WHERE id = '{$order_id}' AND cus_id = '{$member_id}'");
		$data = $this->getInfoOrders($order_id);
		$check_order_admin = $CMS->order->checkStatusOrderAdmin($data['key'], $member_id);
		if($check_order_admin == 0)
		{
			$note_order = $CMS->lang['note_order_cancel'];
			$DB->query("UPDATE ".root_table."order SET ord_status = 6, ord_note ='{$note_order}' WHERE ord_name = '{$data['key']}' AND cus_id = '{$member_id}'");
		}
		if($count)
		{
			// Add notify
			$url_redirect = "{$CMS->vars['root_domain']}/show-order-{$order_id}.html";
			$CMS->notify->add(['cus_id' => $member_id, 'notify_name' => '{$CMS->lang[tilte_order]}'." #{$order_id}".' {$CMS->lang[tilte_order_canceled]}', 'notify_redirect' => "{$url_redirect}"]);
			return true;
		}else
		{
			return false;
		}
	}

    public function report($cus_id=0)
    {
        global $CMS, $DB;

        $cus_id =  intval($cus_id);

        $return = [];

        $sql_add = "";

        $report_start = trim(urldecode($CMS->input['report_start']));
        $report_end = trim(urldecode($CMS->input['report_end']));

        if($report_start)
        {
            $report_start = $CMS->class->date->date2time($report_start);
            $sql_add .= " ord_time>={$report_start} AND ";
        }

        if($report_end)
        {
            $report_end = $CMS->class->date->date2time($report_end) + (24*3600);
            $sql_add .= " ord_time<{$report_end} AND ";
        }

        if($cus_id)
        {
            $sql_add .= " cus_id={$cus_id} AND ";
        }

        if($CMS->input['type'] == 'chart')
        {
            $sql = "SELECT SUM(ord_total) AS total, FROM_UNIXTIME(ord_time, '%Y-%m-%d') AS time FROM ".root_table."order WHERE {$sql_add} ord_deleted = 0 GROUP BY time";

            $sql = $DB->query($sql);
            
            while ($data = $DB->fetch_assoc($sql))
            {
                $return['x'][] = $data['time'];
                $return['total'][] = $data['total'];
            }

            for($i=$report_start; $i<$report_end; $i = $i+(3600*24))
            {
                if(!in_array(date('Y-m-d', $i), $return['x']))
                {
                    $return['x'][] = date('Y-m-d', $i);
                    $return['total'][] = 0;
                }
            }
        }
        else if($CMS->input['type'] == 'board')
        {
            if($cus_id)
            {
                $sql_add = " cus_id={$cus_id} AND ";
            }

            //all

            $sql = "SELECT SUM(ord_total) AS total, COUNT(ord_id) AS cnt FROM ".root_table."order WHERE {$sql_add} ord_deleted = 0";

            $data = $DB->fetch($sql);

            $return['all']['total'] = $CMS->class->input->currency($data['total']);
            $return['all']['cnt'] = $data['cnt'];

            //today
            $today[0] = $today[1] = strtotime(date('Y-m-d'));

            $sql = "SELECT SUM(ord_total) AS total, COUNT(ord_id) AS cnt FROM ".root_table."order WHERE {$sql_add} ord_time>={$today[0]} AND ord_time<{$today[1]} + (24*3600) AND ord_deleted = 0";

            $data = $DB->fetch($sql);

            $return['today']['start'] = date("d/m/Y",$today[0]);
            $return['today']['end'] = date("d/m/Y",$today[1]);
            $return['today']['total'] = $CMS->class->input->currency($data['total']);
            $return['today']['cnt'] = $data['cnt'];

            //this week
            $this_week = $CMS->class->date->getFisrtLastInCurrentWeek();
            $sql = "SELECT SUM(ord_total) AS total, COUNT(ord_id) AS cnt FROM ".root_table."order WHERE {$sql_add} ord_time>={$this_week[0]} AND ord_time<{$this_week[1]} + (24*3600) AND ord_deleted = 0";

            $data = $DB->fetch($sql);

            $return['this_week']['start'] = date("d/m/Y",$this_week[0]);
            $return['this_week']['end'] = date("d/m/Y",$this_week[1]);
            $return['this_week']['total'] = $CMS->class->input->currency($data['total']);
            $return['this_week']['cnt'] = $data['cnt'];

            //last week
            /*$last_week = $CMS->class->date->getFisrtLastInLastWeek();
            $sql = "SELECT SUM(ord_total) AS total, COUNT(ord_id) AS cnt FROM ".root_table."order WHERE {$sql_add} ord_time>={$last_week[0]} AND ord_time<{$last_week[1]} + (24*3600) AND ord_deleted = 0";

            $data = $DB->fetch($sql);

            $return['last_week']['start'] = date("d/m/Y",$last_week[0]);
            $return['last_week']['end'] = date("d/m/Y",$last_week[1]);
            $return['last_week']['total'] = $CMS->class->input->currency($data['total']);
            $return['last_week']['cnt'] = $data['cnt'];*/

            //this month
            $this_month = $CMS->class->date->getFisrtLastInCurrentMonth();
            $sql = "SELECT SUM(ord_total) AS total, COUNT(ord_id) AS cnt FROM ".root_table."order WHERE  {$sql_add} ord_time>={$this_month[0]} AND ord_time<{$this_month[1]} + (24*3600) AND ord_deleted = 0";

            $data = $DB->fetch($sql);

            $return['this_month']['start'] = date("d/m/Y",$this_month[0]);
            $return['this_month']['end'] = date("d/m/Y",$this_month[1]);
            $return['this_month']['total'] = $CMS->class->input->currency($data['total']);
            $return['this_month']['cnt'] = $data['cnt'];

            //this month
            /*$last_month = $CMS->class->date->getFisrtLastInLastMonth();
            $sql = "SELECT SUM(ord_total) AS total, COUNT(ord_id) AS cnt FROM ".root_table."order WHERE {$sql_add} ord_time>={$last_month[0]} AND ord_time<{$last_month[1]} + (24*3600) AND ord_deleted = 0";

            $data = $DB->fetch($sql);

            $return['last_month']['start'] = date("d/m/Y",$last_month[0]);
            $return['last_month']['end'] = date("d/m/Y",$last_month[1]);
            $return['last_month']['total'] = $CMS->class->input->currency($data['total']);
            $return['last_month']['cnt'] = $data['cnt'];*/


        }
        else
        {
            $sql = "SELECT SUM(ord_total) AS total, COUNT(ord_id) AS cnt FROM ".root_table."order WHERE {$sql_add} ord_deleted = 0";

            $data = $DB->fetch($sql);

            $return['ord_total'] = $CMS->class->input->currency($data['total']);
            $return['ord_cnt'] = $data['cnt'];
        }

        return $return;
    }

    public function report_sell($cus_id=0)
    {
        global $CMS, $DB;

        $cus_id =  intval($cus_id);

        $return = [];

        $sql_add = "";

        $report_start = trim(urldecode($CMS->input['report_start']));
        $report_end = trim(urldecode($CMS->input['report_end']));

        if($report_start)
        {
            $report_start = $CMS->class->date->date2time($report_start);
            $sql_add .= " time>={$report_start} AND ";
        }

        if($report_end)
        {
            $report_end = $CMS->class->date->date2time($report_end) + (24*3600);
            $sql_add .= " time<{$report_end} AND ";
        }

        if($cus_id)
        {
            $sql_add .= " cus_id={$cus_id} AND ";
        }

        if($CMS->input['type'] == 'chart')
        {
            $sql = "SELECT SUM(total) AS total, FROM_UNIXTIME(time, '%Y-%m-%d') AS times FROM ".root_table."orders WHERE {$sql_add} deleted = 0 GROUP BY times";

            $sql = $DB->query($sql);

            while ($data = $DB->fetch_assoc($sql))
            {
                $return['x'][] = $data['times'];
                $return['total'][] = $data['total'];
            }

            for($i=$report_start; $i<$report_end; $i = $i+(3600*24))
            {
                if(!in_array(date('Y-m-d', $i), $return['x']))
                {
                    $return['x'][] = date('Y-m-d', $i);
                    $return['total'][] = 0;
                }
            }
        }
        else if($CMS->input['type'] == 'board')
        {
            if($cus_id)
            {
                $sql_add = " cus_id={$cus_id} AND ";
            }

            //all

            $sql = "SELECT SUM(total) AS total, COUNT(id) AS cnt FROM ".root_table."orders WHERE {$sql_add} deleted = 0";

            $data = $DB->fetch($sql);

            $return['all']['total'] = $CMS->class->input->currency($data['total']);
            $return['all']['cnt'] = $data['cnt'];

            //today
            $today[0] = $today[1] = strtotime(date('Y-m-d'));

            $sql = "SELECT SUM(total) AS total, COUNT(id) AS cnt FROM ".root_table."orders WHERE {$sql_add} time>={$today[0]} AND time<{$today[1]} + (24*3600) AND deleted = 0";

            $data = $DB->fetch($sql);

            $return['today']['start'] = date("d/m/Y",$today[0]);
            $return['today']['end'] = date("d/m/Y",$today[1]);
            $return['today']['total'] = $CMS->class->input->currency($data['total']);
            $return['today']['cnt'] = $data['cnt'];

            //this week
            $this_week = $CMS->class->date->getFisrtLastInCurrentWeek();
            $sql = "SELECT SUM(total) AS total, COUNT(id) AS cnt FROM ".root_table."orders WHERE {$sql_add} time>={$this_week[0]} AND time<{$this_week[1]} + (24*3600) AND deleted = 0";

            $data = $DB->fetch($sql);

            $return['this_week']['start'] = date("d/m/Y",$this_week[0]);
            $return['this_week']['end'] = date("d/m/Y",$this_week[1]);
            $return['this_week']['total'] = $CMS->class->input->currency($data['total']);
            $return['this_week']['cnt'] = $data['cnt'];

            //last week
            /*$last_week = $CMS->class->date->getFisrtLastInLastWeek();
            $sql = "SELECT SUM(total) AS total, COUNT(id) AS cnt FROM ".root_table."orders WHERE {$sql_add} time>={$last_week[0]} AND time<{$last_week[1]} + (24*3600) AND deleted = 0";

            $data = $DB->fetch($sql);

            $return['last_week']['start'] = date("d/m/Y",$last_week[0]);
            $return['last_week']['end'] = date("d/m/Y",$last_week[1]);
            $return['last_week']['total'] = $CMS->class->input->currency($data['total']);
            $return['last_week']['cnt'] = $data['cnt'];*/

            //this month
            $this_month = $CMS->class->date->getFisrtLastInCurrentMonth();
            $sql = "SELECT SUM(total) AS total, COUNT(id) AS cnt FROM ".root_table."orders WHERE  {$sql_add} time>={$this_month[0]} AND time<{$this_month[1]} + (24*3600) AND deleted = 0";

            $data = $DB->fetch($sql);

            $return['this_month']['start'] = date("d/m/Y",$this_month[0]);
            $return['this_month']['end'] = date("d/m/Y",$this_month[1]);
            $return['this_month']['total'] = $CMS->class->input->currency($data['total']);
            $return['this_month']['cnt'] = $data['cnt'];

            //this month
            /*$last_month = $CMS->class->date->getFisrtLastInLastMonth();
            $sql = "SELECT SUM(total) AS total, COUNT(id) AS cnt FROM ".root_table."orders WHERE {$sql_add} time>={$last_month[0]} AND time<{$last_month[1]} + (24*3600) AND deleted = 0";

            $data = $DB->fetch($sql);

            $return['last_month']['start'] = date("d/m/Y",$last_month[0]);
            $return['last_month']['end'] = date("d/m/Y",$last_month[1]);
            $return['last_month']['total'] = $CMS->class->input->currency($data['total']);
            $return['last_month']['cnt'] = $data['cnt'];*/


        }
        else
        {
            $sql = "SELECT SUM(total) AS total, COUNT(id) AS cnt FROM ".root_table."orders WHERE {$sql_add} deleted = 0";

            $data = $DB->fetch($sql);

            $return['ord_total'] = $CMS->class->input->currency($data['total']);
            $return['ord_cnt'] = $data['cnt'];
        }

        return $return;
    }
 
	public function answerContact($id=null,$answer=null) {
		global $CMS, $DB, $member;
		if (!empty($id) && !empty($answer)) {
			$DB->query("UPDATE `".root_table."order_contact` SET `ordcon_answer`='{$answer}',`ordcon_status`='1',`ordcon_time_answer`='".time()."' WHERE `ordcon_id`='{$id}'");
			
			$info=$this->infoContact($id);
			switch ($info['ordcon_reason']) {
				default:
					$info['ordcon_reason']=$CMS->lang['reason_0'];
					break;
				case '1':
					$info['ordcon_reason']=$CMS->lang['reason_1'];
					break;
				case '2':
					$info['ordcon_reason']=$CMS->lang['reason_2'];
					break;
				case '3':
					$info['ordcon_reason']=$CMS->lang['reason_3'];
					break;
				case '4':
					$info['ordcon_reason']=$CMS->lang['reason_4'];
					break;
			}
			$customer=$CMS->customer->getInfo($info['ordcon_cus_id']);
			$CMS->email->email_template='answer_contact';
			$CMS->email->email_to=$customer['cus_email'];
			$CMS->email->email_toname=$customer['cus_name']?$customer['cus_name']:$customer['cus_email'];
			$CMS->email->data['cus_name']=$CMS->email->email_toname;
			$CMS->email->data['reason']=$info['ordcon_reason'];
			$CMS->email->data['body']=$info['ordcon_body'];
			$CMS->email->data['time']=$CMS->class->date->date_format($info['ordcon_time'],1);
			$CMS->email->data['answer']=$answer;
			$CMS->email->quick_send_2();
			
			return true;
		}
		return false;
	}
	 
	 
	//02 02 17
	public function saveLog($message=null) {
		if (!empty($message)) {
			global $CMS, $DB, $member;
			$CMS->class->logs->insert('Date: '.date('Y/m/d H:i:s').'<br>Message: '.$message);
			return true;
		}
		return false;
	}
	public function sendEmail($data=null) {
		if (!empty($data)&& !empty($data['email_template'])&& !empty($data['email_to'])&& !empty($data['cus_name'])&& !empty($data['body'])) {
			global $CMS, $DB, $member;
			$CMS->email->email_template=$data['email_template'];
			$CMS->email->email_to=$data['email_to'];
			$CMS->email->email_toname=$data['cus_name'];
			$CMS->email->data['cus_name']=$data['cus_name'];
			$CMS->email->data['body']=$data['body'];
			$CMS->email->quick_send_2();
			return true;
		}
		return false;
	}

	//=========================================
	// Get list item order
	//==========================================
	function get_list_order_item($ord_id = "")
	{
		global $CMS, $DB, $member;
 
		// Check enable kho hang
		if($CMS->vars['addon_goods_enable'] == 1)
		{
			$addon_goods_enable = "block";
		}
		else
		{
			$addon_goods_enable = "none";
		}
		$order = $this->get_info($ord_id);
		$trx_original = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=1&act=show&id={$order['transaction_id']}'>TRX{$order['transaction_id']}</a>";
		$sql = $DB->query("SELECT * FROM ".root_table."order_item WHERE ord_id = '{$ord_id}' AND ordi_deleted = 0  ORDER BY ordi_id ASC");
		$output = <<<EOF
		<style>
			.table-responsive {
                          overflow-x: visible !important;
                   
                        }
            
		</style>
		<section class="add_table">				 			 
		<form name="update_status_ordi" id="update_status_ordi" method="POST" action="{$CMS->vars['root_domain']}/?site=order&act=edit&subact=update_ordi&id={$ord_id}">

				  <h4 class="heading"><i class="fa fa-caret-down"></i><span>{$CMS->lang['product_header']}</span></h4>
						 
					 <div>
						<div  class="table-responsive" style="overflow-x: initial;">
							<table class="table_cus"   width="100%">
								<thead>
									<tr>
									
										 
						 				<th scope="col" width="2%">#</th>
						 				<th scope="col" width="2%">ID</th>
										<th scope="col" width="20%">{$CMS->lang['product_items']}</th>
									 
EOF;
										if($CMS->vars['addon_goods_enable'] == 1)
										{
											$output .= <<<EOF
											 
											<th scope="col"   width="7%">{$CMS->lang['product_export']}</th>
EOF;

										}										
										$output .= <<<EOF
										<th scope="col" width="7%">{$CMS->lang['product_period']}</th>
										<th scope="col" width="10%">{$CMS->lang['product_total_money']}</th>
										<th scope="col" width="7%">{$CMS->lang['product_status']}</th>
										<th scope="col" width="7%">{$CMS->lang['payment_status']}</th>
							 			<th scope="col" width=6%">{$CMS->lang['product_date_expired']}</th>
							 			<th scope="col"  width=4%">{$CMS->lang['action']}</th>
									</tr>
								</thead>	
								<tbody>

EOF;
 
		if($DB->num_rows($sql) > 0)
		{
			while($data = $DB->fetch_array($sql))
			{
			    //Booking info
                $bookingInfo = "";
                if($data['ordi_booking_time']) {
                    $bookingInfo = "Thời gian hẹn: " . \lib\date::format($data['ordi_booking_time'], $CMS->vars['dateformat_php'][$CMS->vars['date_format']] . " H:i");

                    if($data['ordi_staff']) {
                        $staff = $CMS->user->get_info($data['ordi_staff']);
                        if($staff) {
                            $bookingInfo .= "<br>Nhân viên phụ trách: " . $staff['user_display_name'];
                        }
                    }
                }

                if($data[''])

				if($data['ordi_expiry_date'] != 0)
				{
					$data['ordi_expiry_date_bk']=$CMS->class->date->date_format($data['ordi_expiry_date']);
				}
				else
				{
					$data['ordi_expiry_date_bk']= "";
				}

				$ordi_total =  $CMS->class->input->currency($data['ordi_total']);

			  
				if($data['ordi_status'] == 0)
				 	{
				 		$data['ordi_status_n']= "<span class=\"label label-default\">{$CMS->lang['ordi_status_10']}</span>";
				 	}
				 	elseif($data['ordi_status'] == 1)
				 	{
				 		$data['ordi_status_n']="<span class=\"label label-warning\">{$CMS->lang['ordi_status_11']}</span>";
				 	}
				 	elseif($data['ordi_status'] == 2)
				 	{
				 		$data['ordi_status_n']="<span class=\"label label-success\">{$CMS->lang['ordi_status_12']}</span>";
				 	}elseif($data['ordi_status'] == 3)
				 	{
				 		$data['ordi_status_n']="<span class=\"label label-danger\">{$CMS->lang['ordi_status_13']}</span>";
				 	}elseif($data['ordi_status'] == 4)
				 	{
				 		$data['ordi_status_n']="<span class=\"label label-pink\">{$CMS->lang['ordi_status_14']}</span>";
				 	}
				 	elseif($data['ordi_status'] == 5)
				 	{
				 		$data['ordi_status_n']="<span class=\"label label-violet\">{$CMS->lang['ordi_status_15']}</span>";
				 	} 
				
				 // Payment status, again?
				switch ($data['ordi_payment_status']) {
					case '0':
						$data['ordi_payment_status_bk'] = "<span class='label label-default'>{$CMS->lang['payment_status_0']}</span>";
						$data['ordi_payment_status_text'] = $CMS->lang['payment_status_0'];
						break;
					case '1':
						$data['ordi_payment_status_bk'] = "<span class='label label-primary'>{$CMS->lang['payment_status_1']}</span>";
						$data['ordi_payment_status_text'] = $CMS->lang['payment_status_1'];
						break;
					 case '2':
						$data['ordi_payment_status_bk'] = "<span class='label label-warning'>{$CMS->lang['payment_status_2']}</span>";
						$data['ordi_payment_status_text'] = $CMS->lang['payment_status_2'];
						break;
				 
				}

				 
	 

				if($data['product_id'] > 0)//SAn pham
				{
					if($data['cycle_type'] == 0)
					{
						$ordi_cycle = $CMS->lang['gonce'];
					}
					elseif($data['cycle_type'] == 1)
					{
						$ordi_cycle = $data['ordi_cycle']." {$CMS->lang['gmonth']}";
					}elseif($data['cycle_type'] == 2)
					{
						$ordi_cycle = $data['ordi_cycle']." {$CMS->lang['gyear']}";
					}
					 
					if($order['service_type'] == 0)
					{
						$product_type = $CMS->product->getInfo($data['product_id'],"product_type", 0);
						if($product_type == 0)
						{
 
							if($CMS->vars['addon_goods_enable'] == 1)
							{
								$check_stock = "<i class=\"fa fa-search q-search\" style=\"cursor:pointer  \" onclick=\"check_stock_product({$data['product_id']});\"></i>";

								$bill_export_id = "";
								if($data['bill_export_id'] > 0)
								{
									if($CMS->permit['store_request_read_request_ei'] == 1)
									{
										$bill_export_id  .= "<a href=\"{$CMS->vars['root_domain']}/?site=store_request&stage=request_ei&act=show&id={$data['bill_export_id']}\" >REQ".$data['bill_export_id']."</a><span style='color: red; display: inline-block;'  data-toggle='tooltip' data-placement='bottom' title='Xuất hàng'><i class='fa fa-arrow-right' aria-hidden='true'></i></span>";
									}
									else
									{
										$bill_export_id  .= "REQ".$data['bill_export_id'];
									}
								}
								if($data['bill_import_id'] > 0)
								{
									if($CMS->permit['store_request_read_request_ei'] == 1)
									{
										$bill_export_id  .= "<a href=\"{$CMS->vars['root_domain']}/?site=store_request&stage=request_ei&act=show&id={$data['bill_import_id']}\" >REQ".$data['bill_import_id']."</a><span style='color: green; display: inline-block;' data-toggle='tooltip' data-placement='bottom' title='Nhập hàng'><i class='fa fa-arrow-left' aria-hidden='true'></i></span>";
									}
									else
									{
										$bill_export_id  .= "REQ".$data['bill_import_id'];
									}
								}

							}										
									 
							$site_p_type = "product";
						}else
						{
							$site_p_type = "service";
							$order['ord_quantity_items'] = ""; 
						}
			
					}
					$output .=<<<EOF
					<tr>
							<td>
EOF;

							
  							if($order['payment_status'] == 2 AND $order['ord_status'] == 2)
							{}
							else
							{
								$output .=<<<EOF
					 			 <div class="checkbox checkbox-only">
						                      <input type="checkbox" name="checkbox_ordi[]" id="id_{$data['ordi_id']}" value="{$data['ordi_id']}">
						                      <label for="id_{$data['ordi_id']}"></label>
						         </div>

						 
EOF;
							}							
						   $output .=<<<EOF
						</td>
						<td>#{$data['ordi_id']}</td>
						<td><span style="word-wrap: break-word;width: 100%;"><a href="{$CMS->vars['root_domain']}/?site={$site_p_type}&act=show&id={$data['product_id'] }">{$data['ordi_name']}</a></span> 
{$bookingInfo}{$check_stock}
						</td>
		 
EOF;
						if($CMS->vars['addon_goods_enable'] == 1)
						{
							$output .=<<<EOF
							 
							<td> {$bill_export_id} </td>
EOF;
						}
						
						$output .=<<<EOF
						<td>{$ordi_cycle}</td>

						<td>{$ordi_total}</td>
						<td>{$data['ordi_status_n']}</td>
						<td>{$data['ordi_payment_status_bk']}</td>
						<td>{$data['ordi_expiry_date_bk']}</td>
						<td>
EOF;
						
  							if($order['payment_status'] == 2 AND $order['ord_status'] == 2)
							{}
							else
							{
								$output .=<<<EOF
							

							<div class="btn-group">
								<button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									{$CMS->lang['status_action']}
								</button>
								<div class="dropdown-menu">
EOF;
								for($i = 0; $i<= 2; $i++)
								{
									$icons = ['<i class="fa fa-pause-circle"></i>', '<i class="fa fa-play-circle"></i>', '<i class="fa fa-check-circle"></i>'];
									if($i != $data['ordi_status'])
									{

										$output .=<<<EOF
										 <a class="dropdown-item" href="{$CMS->vars['root_domain']}/?site=order&act=edit&subact=update_ordi_id&id={$ord_id}&ordi={$data['ordi_id']}&status={$i}">{$icons[$i]} {$CMS->lang['ordi_status_1'.$i]}</a> 
									

EOF;
									}
								}
 									
								if(  in_array($data['ordi_status'], array(1,4)) == false AND in_array($data['cycle_type'], array(1,2)) == true ) 
								{
									$output .=<<<EOF
									 <a class="dropdown-item"  href="{$CMS->vars['root_domain']}/?site=order&act=renew&subact=config_renew&id={$ord_id}&ordi={$data['ordi_id']}"  ><i class="fa fa-file-text-o"></i> {$CMS->lang['button_renew']}</a> 
									

EOF;

								}
								
								$output .=<<<EOF
									 
								</div>
							</div>
EOF;
					}
						$output .=<<<EOF

						</td>
					</tr>
EOF;

				}
				elseif($data['ass_key'] != "")
				{
					$assets = $CMS->assets->get_info($data['ass_key']);
					$bill_export_id = "";
					if($data['bill_export_id'] > 0)
								{
									if($CMS->permit['store_request_read_request_ei'] == 1)
									{
										$bill_export_id  .= "<a href=\"{$CMS->vars['root_domain']}/?site=store_request&stage=request_ei&act=show&id={$data['bill_export_id']}\" >REQ".$data['bill_export_id']."</a><span style='color: red; display: inline-block;'  data-toggle='tooltip' data-placement='bottom' title='Xuất hàng'><i class='fa fa-arrow-right' aria-hidden='true'></i></span>";
									}
									else
									{
										$bill_export_id  .= "REQ".$data['bill_export_id'];
									}
								}
								if($data['bill_import_id'] > 0)
								{
									if($CMS->permit['store_request_read_request_ei'] == 1)
									{
										$bill_export_id  .= "<a href=\"{$CMS->vars['root_domain']}/?site=store_request&stage=request_ei&act=show&id={$data['bill_import_id']}\" >REQ".$data['bill_import_id']."</a><span style='color: green; display: inline-block;' data-toggle='tooltip' data-placement='bottom' title='Nhập hàng'><i class='fa fa-arrow-left' aria-hidden='true'></i></span>";
									}
									else
									{
										$bill_export_id  .= "REQ".$data['bill_import_id'];
									}
								}
					$check_stock = "";
					if($order['service_type'] == 0)
					{
						if($CMS->vars['addon_goods_enable'] == 1)
						{

							$check_stock = "<i class=\"fa fa-search q-search\" style=\"cursor:pointer \" onclick=\"check_stock_product({$assets['product_id']});\"></i>";
						}
					}
					$output .=<<<EOF
					<tr>
						<td>
EOF;
						
  							
  							if($order['payment_status'] == 2 AND $order['ord_status'] == 2)
							{}
							else
							{
								$output .=<<<EOF
						    <div class="checkbox checkbox-only">
						                      <input type="checkbox" name="checkbox_ordi[]" id="id_{$data['ordi_id']}" value="{$data['ordi_id']}">
						                      <label for="id_{$data['ordi_id']}"></label>
						     </div>
EOF;
							}
							$output .=<<<EOF

						</td>
						<td>#{$data['ordi_id']}</td>
						<td><span style="word-wrap: break-word;width: 100%;"><a href="{$CMS->vars['root_domain']}/?site=assets&act=show&id={$assets['ass_id'] }">{$data['ordi_name']}</a></span>  {$check_stock}</td>
				 
EOF;
						if($CMS->vars['addon_goods_enable'] == 1)
						{
							$output .=<<<EOF
						 
							<td>{$bill_export_id}</td>
EOF;
						}

						$output .=<<<EOF
						<td>{$CMS->lang['gonce']}</td>
						<td>{$ordi_total}</td>
						<td>{$data['ordi_status_n']}</td>
						 <td>{$data['ordi_payment_status_bk']}</td>
						<td>{$data['ordi_expiry_date_bk']}</td>
						<td>
EOF;

  							if($order['payment_status'] == 2 AND $order['ord_status'] == 2)
							{}
							else
							{
								$output .=<<<EOF
					 			<div class="btn-group">
								<button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									{$CMS->lang['status_action']}
								</button>
								<div class="dropdown-menu">
EOF;
								for($j = 0; $j<= 2; $j++)
								{
									if($j != $data['ordi_status'])
									{
										$output .=<<<EOF
										 <a class="dropdown-item" href="{$CMS->vars['root_domain']}/?site=order&act=edit&subact=update_ordi_id&id={$ord_id}&ordi={$data['ordi_id']}&status={$j}"><i class="fa fa-file-text-o"></i> {$CMS->lang['ordi_status_0'.$j]}</a> 

EOF;
									}
								}
								$output .=<<<EOF
								</div>
							</div>
EOF;
							}							
						   $output .=<<<EOF
							
						</td>
					</tr>
EOF;
				}
			}
		}



		$output .=<<<EOF
					</tbody>
				</table>
			</div>

			<div class="fuction_table">
				<div class=" pull-left"  >

EOF;
			if($order['payment_status'] == 2 AND $order['ord_status'] == 2)
			{}
			else
			{
				$output .=<<<EOF
		 
					<div class="checkbox checkbox-only pull-left custom-checkbox"  onclick="javascript:form_checkall('update_status_ordi');" id="checkall">
				                   <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
				                   <label for="id_" class="tbl-check-1"></label>
				         </div>
				  
					<p class="form-control-static ">
					 	
						<select class="form-control" name="ordi_status" onchange="return submit_action_ordicontrol(this,'update_status_ordi');" defaultvalue="delete_all" emsg="{$CMS->lang['select_action']} !" ehide="1">
							<option value="">-- {$CMS->lang['update_status_checkall']} --</option>
							 			   <option value="0">{$CMS->lang['order_status_0']}</option>
							 			   <option value="1">{$CMS->lang['order_status_1']}</option>
							 			   <option value="2">{$CMS->lang['order_status_2']}</option>
							 			   <option value="3">{$CMS->lang['order_status_3']}</option>

						</select>
					</p>
		 
EOF;
			}
				$output .=<<<EOF

				</div>
				<nav class="pull-right">
				</nav>
			</div>	
		</div>

	 </form>	
	</section>	
 	<script>
 		function submit_action_ordicontrol(onthis, el)
		{

		    var value_act = $(onthis).val();
		    if(value_act != "")
		    {
		      $("#"+el).submit();
		    }
		}
 	</script>
				

EOF;

			return $output;
	}



	//=========================================
	// Get list transaction of order original
	//==========================================
	function get_list_transaction($ord_id = "")
	{
		global $CMS, $DB, $member;

 		$sql = $DB->query("SELECT * FROM ".root_table."transaction WHERE ord_id = '{$ord_id}' AND trx_deleted = 0  ORDER BY trx_id DESC");
 		$output = "";
 		if($DB->num_rows($sql) > 0)
 		{
 			$output .=<<<EOF
 			<section class="add_table">	
 			<h4 class="heading"><i class="fa fa-caret-down"></i><span>{$CMS->lang['list_transaction']}</span></h4>
 			<div class="table-responsive" style="overflow-x: initial;">
				<table class="table_cus" width="100%">
					<thead>
						<tr>
		 
			 				<th scope="col" width="10%">ID</th>
							<th scope="col" width="10%">{$CMS->lang['trx_code']}</th>				
							<th scope="col" width="20%">{$CMS->lang['trx_cus']}</th>						 
							<th scope="col" width="15%">{$CMS->lang['trx_date']}</th>
							<th scope="col" width="15%">{$CMS->lang['trx_total']}</th>
							<th scope="col" width="10%">{$CMS->lang['trx_status']}</th>
 							 <th scope="col" width="10%">{$CMS->lang['action']}</th>
						</tr>
					</thead>	
					<tbody>
					 

EOF;
			
					while($data =$DB->fetch_array($sql))
					{
				 
						   switch ($data['trx_subtype']) {
				            case 1:
				                $show = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=1&act=show&id={$data['trx_id']}'>{$data['trx_code']}</a>";
				                $url_edit = "	<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=1&act=edit&id={$data['trx_id']}'   class='edit'><i class='fa fa-edit'></i></a>";
				                $url_delete = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=1&act=delete&id={$data['trx_id']}'  class='edit'><i class='fa fa-trash-o'></i></a>";

				                $url_edit_link = "{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=1&act=edit&id={$data['trx_id']}";
				                break;
				            case 2:
				                $show = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=2&act=show&id={$data['trx_id']}'>{$data['trx_code']}</a>";
				                $url_edit = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=2&act=edit&id={$data['trx_id']}'   class='edit'><i class='fa fa-edit'></i></a>";
				                $url_delete = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=2&act=delete&id={$data['trx_id']}'  class='edit'><i class='fa fa-trash-o'></i></a>";

				                $url_edit_link = "{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=2&act=edit&id={$data['trx_id']}";
				                break;
				            case 3:
				                $show = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=3&act=show&id={$data['trx_id']}'>{$data['trx_code']}</a>";
				                $url_edit = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=3&act=edit&id={$data['trx_id']}'   class='edit'><i class='fa fa-edit'></i></a>";
				                $url_delete = "	<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=3&act=delete&id={$data['trx_id']}'  class='edit'><i class='fa fa-trash-o'></i></a>";

				                $url_edit_link = "{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=3&act=edit&id={$data['trx_id']}";
				                break;
				            case 4:
				                $show = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=4&act=show&id={$data['trx_id']}'>{$data['trx_code']}</a>";
				                $url_edit = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=4&act=edit&id={$data['trx_id']}'   class='edit'><i class='fa fa-edit'></i></a>";
				                $url_delete = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=4&act=delete&id={$data['trx_id']}'  class='edit'><i class='fa fa-trash-o'></i></a>";

				                $url_edit_link = "{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=4&act=edit&id={$data['trx_id']}";
				                break;
				            case 5:
				                $show = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=5&act=show&id={$data['trx_id']}'>{$data['trx_code']}</a>";
				                $url_edit = "	<a href='{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=5&act=edit&id={$data['trx_id']}'   class='edit'><i class='fa fa-edit'></i></a>";
				                $url_delete = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=5&act=delete&id={$data['trx_id']}'  class='edit'><i class='fa fa-trash-o'></i></a>";
				                if ($data['trx_status'] ==0) {
				                    $link_add_sub_2 = " <a href='{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=7&act=add&invoice={$data['trx_id']}'>{$CMS->lang['trx_add_7']}</a>";
				                }

				                $url_edit_link = "{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=5&act=edit&id={$data['trx_id']}";
				                break;
				            case 6:
				                $show = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=6&act=show&id={$data['trx_id']}'>{$data['trx_code']}</a>";
				                $url_edit = "	<a href='{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=6&act=edit&id={$data['trx_id']}'   class='edit'><i class='fa fa-edit'></i></a>";
				                $url_delete = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=6&act=delete&id={$data['trx_id']}'  class='edit'><i class='fa fa-trash-o'></i></a>";

				                $url_edit_link = "{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=6&act=edit&id={$data['trx_id']}";
				                break;
				            case 7:
				                $show = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=7&act=show&id={$data['trx_id']}'>{$data['trx_code']}</a>";
				                $url_edit = "	<a href='{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=7&act=edit&id={$data['trx_id']}'   class='edit'><i class='fa fa-edit'></i></a>";
				                $url_delete = "<a href='{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=7&act=delete&id={$data['trx_id']}'  class='edit'><i class='fa fa-trash-o'></i></a>";

				                $url_edit_link = "{$CMS->vars['root_domain']}/?site=transactions&type=2&sub=7&act=edit&id={$data['trx_id']}";
				                break;
				            default:
				                $show = "";
				                $url_edit = "<a href='{$CMS->vars['root_domain']}/?site=transactions&act=sales'   class='edit'><i class='fa fa-edit'></i></a>";
				                $url_delete = "{$CMS->vars['root_domain']}/?site=transactions&act=sales";

				                $url_edit_link = "{$CMS->vars['root_domain']}/?site=transactions&act=sales";
				                break;
				        }

						if($data['trx_status'] == 0)//Dang cho
						{
								$data['trx_status_c'] = "<span class=\"label label-default\">".$CMS->lang['trx_status_0'.$data['trx_status']]."</span>";
						}
						elseif($data['trx_status'] == 1)//Hoan thanh
						{
							$data['trx_status_c'] = "<span class=\"label label-success\">".$CMS->lang['trx_status_0'.$data['trx_status']]."</span>";
						}
				 		elseif($data['trx_status'] == 2)//Quá hạn
						{
							$data['trx_status_c'] = "<span class=\"label label-warning\">".$CMS->lang['trx_status_0'.$data['trx_status']]."</span>";
						}
						elseif($data['trx_status'] == 3)//Đã đóng
						{
							$data['trx_status_c'] = "<span class=\"label label-success\">".$CMS->lang['trx_status_0'.$data['trx_status']]."</span>";
						}
						elseif($data['trx_status'] == 4)//Công nợ - status mới
						{
							$data['trx_status_c'] = "<span class=\"label label-danger\">".$CMS->lang['trx_status_0'.$data['trx_status']]."</span>";
						}
						$data['cus_id_c'] = $CMS->customer->getInfo($data['cus_id'], 'cus_full_name');
						$data['trx_due_c'] = !in_array($data['trx_subtype'], [1,5]) || ! $data['trx_due_date'] ? '...' : $CMS->class->date->date_format($data['trx_due_date']);
						$prefix_trx = $data['trx_type'] == 2 ? 'PAY' : 'INV';
						$data['trx_invoice_no_c'] = !empty($data['trx_invoice_no']) && $data['trx_invoice_no'] > 0 ? $prefix_trx.$data['trx_invoice_no'] : '-';
						$data['trx_time_c'] = $CMS->class->date->date_format($data['trx_time']);
						$data['trx_total_c'] =  $CMS->class->input->currency($data['trx_total']);

						$data['url_edit_link'] = ($CMS->permit['transactions_edit'] == 1 && $data['trx_status']<=2) ? $url_edit_link : '';

							$output .=<<<EOF
					 		<tr>
					 			<td><a href="{$CMS->vars['root_domain']}/?site=transactions&type=1&sub=1&act=show&id={$data['trx_id']}">{$data['trx_code']}</a></td>
					 			<td><a href="{$CMS->vars['root_domain']}/?site=transactions&type=1&act=show&id={$data['trx_id']}">{$data['trx_invoice_no_c']}</a></td>
					 			<td><a href="{$CMS->vars['root_domain']}/?site=customer&act=show&id={$data['cus_id']}">{$data['cus_id_c']}</a></td>
					 			<td>{$data['trx_due_c']}</td>
					 			<td>{$data['trx_total_c']}</td>
					 			<td>{$data['trx_status_c']}</td>
					 			 <td align="center">
									{$CMS->transactions->action_html($data)}
EOF;
							     
// 							        if($CMS->permit['transactions_edit'] == 1 && $data['trx_status']<=2)
// 							        {
// 							            $output .=<<<EOF

// 							            {$url_edit}
// EOF;

//     								}
        
      						  $output .=<<<EOF

						 
						</td>
					 		</tr>
EOF;
					}
			$output .=<<<EOF
					</tbody>
				</table>
			</div>
		</section>		
EOF;

 		}
 		return $output;
 	}

	//=========================================
	// Get list item order
	//==========================================
	function get_item($ord_id = "", $type = "")
	{
		global $CMS, $DB, $member;
 
		if($type == 0 AND is_numeric($type))
		{  
			$sql = " AND product_id > 0 ";
		}
		elseif($type == 1  AND is_numeric($type))
		{
			$sql = " AND product_id = 0  AND ass_key != '' ";
		}
		elseif($type == "")
		{
			$sql = "  ";
		}
 
		$sql = $DB->query("SELECT * FROM ".root_table."order_item WHERE ord_id = '{$ord_id}' AND ordi_deleted = 0 {$sql} ORDER BY ordi_id ASC");
		 
		$item = array();
		if($DB->num_rows($sql) > 0 )
		{
			$i = 0;
			while($data = $DB->fetch_array($sql))
			{
				$item[$i]['product_id'] = $data['product_id'];
				$item[$i]['product_name'] = $data['ordi_name'];
				$item[$i]['product_description'] = $data['ordi_description'];
				$item[$i]['product_old_price'] = $data['ordi_old_price'];
				$item[$i]['product_price'] = $data['ordi_price'];
				 
				$item[$i]['product_tax'] = $data['ordi_tax'];
				$item[$i]['product_cycle'] = $data['cycle_type'];
				$item[$i]['product_cycle_value'] = $data['ordi_cycle'];
				$item[$i]['product_discount_type'] = $data['ordi_discount_type'];
				$item[$i]['product_discount_value'] = $data['ordi_discount_value'];
				$item[$i]['product_quantity'] = $data['ordi_quantity'];

                $item[$i]['product_commission_type'] = $data['ordi_commission_type'];
                $item[$i]['product_commission_value'] = $data['ordi_commission_value'];

				$item[$i]['ass_id'] = $item[$i]['ass_key'] = $data['ass_key'];
				$item[$i]['ass_name'] = $data['ordi_name'];
				$item[$i]['ass_description'] = $data['ordi_description'];
				$item[$i]['ass_old_price'] = $data['ordi_old_price'];
				$item[$i]['ass_price'] = $data['ordi_price'];
				$item[$i]['ass_tax'] = $data['ordi_tax'];
				$item[$i]['ass_cycle'] = $data['cycle_type'];
				$item[$i]['ass_cycle_value'] = $data['ordi_cycle'];
				$item[$i]['ass_discount_type'] = $data['ordi_discount_type'];
				$item[$i]['ass_discount_value'] = $data['ordi_discount_value'];
                $item[$i]['ass_quantity'] = $data['ordi_quantity'];

                $item[$i]['var_id'] = $data['var_id'];
                $item[$i]['var_content'] = \lib\input::jsonDecode($data['var_content']);

				$i++;
 
			}
		}

		return $item;
	}


	//=========================================
	// Convert input item order
	//==========================================
	function convert_input_item($ord_id = "" )
	{
		global $CMS, $DB, $member;
 
	 
		$sql = $DB->query("SELECT * FROM ".root_table."order_item WHERE ord_id = '{$ord_id}' AND ordi_deleted = 0 {$sql} ORDER BY ordi_id ASC");
	
		$item = array();
		if($DB->num_rows($sql) > 0 )
		{
			$i = 0;
			while($data = $DB->fetch_array($sql))
			{
				if($data['product_id'] > 0)
				{
						$CMS->input['product_id'][$i] = $data['product_id'];
						$CMS->input['product_name'][$i] = $data['ordi_name'];
						$CMS->input['product_description'][$i] = $data['ordi_description'];
						$CMS->input['product_price'][$i] = $data['ordi_price'];
						$CMS->input['product_tax'][$i] = $data['ordi_tax'];
						$CMS->input['product_cycle_type'][$i] = $data['cycle_type'];
						$CMS->input['product_cycle'][$i] = $data['ordi_cycle'];
						$CMS->input['product_quantity'][$i] = 1;
						$CMS->input['product_item_id'][$i] =  $data['ordi_id'];
				}
				else
				{

						$CMS->input['ass_id'][$i] = $CMS->input['ass_key'][$i] = $data['ass_key'];
						$CMS->input['ass_name'][$i] = $data['ordi_name'];
						$CMS->input['ass_description'][$i] = $data['ordi_description'];
						$CMS->input['ass_price'][$i] = $data['ordi_price'];
						$CMS->input['ass_tax'][$i] = $data['ordi_tax'];
						$CMS->input['ass_quantity'][$i] = 1;
						$CMS->input['ass_item_id'][$i] =  $data['ordi_id'];
 
				}


				$i++;
 
			}
		}

	 
	}



	function del_orditem($ord_id = "")
	{
		global $CMS, $DB, $member;
 
		$DB->query("UPDATE ".root_table."order_item SET  ordi_deleted='1'  WHERE ord_id='{$ord_id}'");
		return true;

	}

	function get_trx_item($trx_id = "")
	{
		global $CMS, $DB, $member;
 
		$sql = $DB->query("SELECT * FROM ".root_table."transaction_item WHERE trx_id = '{$trx_id}' AND tri_deleted = 0   ORDER BY tri_id ASC");
		unset($CMS->input['product_id']);
		unset($CMS->input['product_name']);
		unset($CMS->input['product_description']);

		unset($CMS->input['product_price']);
		unset($CMS->input['product_tax']);
		unset($CMS->input['product_cycle_type']);
		unset($CMS->input['product_cycle']);
		unset($CMS->input['product_quantity']);
		unset($CMS->input['product_tri_id']);
		unset($CMS->input['ass_id']);
		unset($CMS->input['ass_name']);
		unset($CMS->input['ass_description']);
		unset($CMS->input['ass_tax']);
		unset($CMS->input['ass_quantity']);
		unset($CMS->input['ass_tri_id']);
 
		$item = array();
		if($DB->num_rows($sql) > 0 )
		{
			$i = 0;
			while($data = $DB->fetch_array($sql))
			{
				if($data['product_id'] > 0)
				{
						$CMS->input['product_id'][$i] = $data['product_id'];
						$CMS->input['product_name'][$i] = $data['tri_name'];
						$CMS->input['product_description'][$i] = $data['tri_description'];
						$CMS->input['product_price'][$i] = $data['tri_total'];
						$CMS->input['product_tax'][$i] = $data['tri_tax'];
						$CMS->input['product_cycle_type'][$i] = $data['cycle_type'];
						$CMS->input['product_cycle'][$i] = $data['tri_cycle'];
						$CMS->input['product_quantity'][$i] = 1;
						$CMS->input['product_tri_id'][$i] =  $data['tri_id'];
				}
				else
				{

						$CMS->input['ass_id'][$i] = $CMS->input['ass_key'][$i] = $data['ass_key'];
						$CMS->input['ass_name'][$i] = $data['tri_name'];
						$CMS->input['ass_description'][$i] = $data['tri_description'];
						$CMS->input['ass_price'][$i] = $data['tri_total'];
						$CMS->input['ass_tax'][$i] = $data['tri_tax'];
						$CMS->input['ass_quantity'][$i] = 1;
						$CMS->input['ass_tri_id'][$i] =  $data['tri_id'];
 
				}


				$i++;
 
			}
		}
	
	}


	function add_ord_item($trx_id = "" )
	{
		global $CMS, $DB, $member;

		if($trx_id == "")
		{
			return false;		 
		}

        $ord_sql = "SELECT ord_id FROM ".root_table."order WHERE ord_deleted=0 AND transaction_id = '{$trx_id}' ORDER BY ord_id DESC LIMIT 1";

       
        
        $ord_sql = $DB->query($ord_sql);
        if($DB->num_rows($ord_sql) == 0)
        {
        	return false;
        }
         //Update for order
        $ord = $DB->fetch_assoc($ord_sql);
        $ord_id = $ord['ord_id'];
        

		$this->get_trx_item($trx_id );
 		list($item, $order_total)   = $this->create_session_order();
 		$_SESSION['order_item'] = $item;
 		$this->del_orditem($ord_id);
 		$this->add_order_item($ord_id);
 		return true;


	}


	function update_order( $tr = array() )
	{
		global $CMS, $DB, $member;

		if(!is_array($tr))
		{
			return false;
		}

		
        $ord_sql = "SELECT ord_id FROM ".root_table."order WHERE ord_deleted=0 AND transaction_id = '{$tr['trx_id']}' ORDER BY ord_id DESC LIMIT 1";
     

        $ord_sql = $DB->query($ord_sql);
        if($DB->num_rows($ord_sql) == 0)
        {
        	return false;
        }
        //Update for order
        $ord = $DB->fetch_assoc($ord_sql);
        $ord_id = $ord['ord_id'];
        
        $CMS->class->logs->key = "order_{$ord_id}";    
		$CMS->class->logs->old_data = $ord;
		$CMS->class->logs->insert($key);

 		$ord_amount = $tr['trx_amount'];
 		$ord_total = $tr['trx_total'];
 		$ord_tax = $tr['trx_tax'];
 		$ord_discount_type = $tr['trx_discount_type'];
 		$ord_discount = $tr['trx_discount_value'];
 		$payment_method = $tr['trx_payment_method'];
 		$account_id = $tr['trx_account'];
 		$ord_time_update = time();
 
		$sql = $DB->query("UPDATE `".root_table."order` SET `ord_amount`='{$ord_amount}', `ord_total`='{$ord_total}', ord_tax = '{$ord_tax}' , ord_discount_type = '{$ord_discount_type}', ord_discount='{$ord_discount}' , `payment_method`='{$payment_method}',  `account_id`='{$account_id}',  `ord_item`='{$ord_item}',   `cus_id`='{$tr['cus_id']}' , `ord_time_update`='{$ord_time_update}'   WHERE `ord_id`={$ord_id}");
		 


		$new_ord = $this->get_info($ord_id);
 		$CMS->class->logs->key = "order_{$ord_id}";    
		$CMS->class->logs->save_detail("order", $ord_id, $new_ord);


 		return true;


	}
	//===========================================================================
	//  Update status order_item
	//===========================================================================
	
	public function update_status_ordi_id($type = "")
	{
		global $CMS, $DB;
		$ordi = $CMS->input['ordi'];
		$sql = $DB->query("SELECT * FROM ".root_table."order_item WHERE ordi_id = '{$ordi}' AND ordi_deleted = 0 ");
		$status =intval($CMS->input['status']);

		if($DB->num_rows($sql) > 0)
		{
			$ord_i = $DB->fetch_array($sql);
			$before_status = $ord_i['ordi_status'];
			if($ord_i['ass_key']!="")
			{
				//Hang hoa
				$before_status = "0".$before_status;
			}
			else
			{
				$before_status = "1".$before_status;
			}
			// Tao phieu xuat kho
	  		// if($status == 2 AND $ord_i['bill_export_id'] == 0 ) // Trang thai dang giao va chua tao phieu xuat kho lan nao
	  		// {
	  		// 	$bill_return = $this->bill_export($ordi, "");
	  		// 	if($bill_return['status'] == true)
	  		// 	{
	  		// 		$bill_id = $bill_return['data_output'];
	  		// 		//Tao phieu xuat kho thanh cong
	  		// 		$DB->query("UPDATE ".root_table."order_item SET ordi_status = '{$status}' , bill_export_id='{$bill_id}'  WHERE ordi_id='{$ordi}'");
	  		// 	}
	  		// 	else
	  		// 	{
	  		// 		return false;
	  		// 			//$DB->query("UPDATE ".root_table."order_item SET ordi_status = '{$status}'  WHERE ordi_id='{$ordi}'");
	  		// 	}
	  			 
	  		// }
	  		// elseif($status == 1 AND $ord_i['bill_export_id'] == 0 ) // Trang thai dang giao va chua tao phieu xuat kho lan nao
	  		// {   // Tao phieu xuat kho =>buoc 3 cua phieu xuat kho
	  		// 	$bill_return = $this->bill_export($ordi, 31);
	  		// 	if($bill_return['status'] == true)
	  		// 	{
	  		// 		$bill_id = $bill_return['data_output'];
	  		// 		//Tao phieu xuat kho thanh cong
	  		// 		$DB->query("UPDATE ".root_table."order_item SET ordi_status = '{$status}' , bill_export_id='{$bill_id}'  WHERE ordi_id='{$ordi}'");
	  		// 	}
	  		// 	else
	  		// 	{
	  		// 		return false;
	  		// 			//$DB->query("UPDATE ".root_table."order_item SET ordi_status = '{$status}'  WHERE ordi_id='{$ordi}'");
	  		// 	}
	  		// }	 
	  		// // }elseif($status == 1 AND $ord_i['bill_export_id'] > 0 ) 
			  // // 		//  Trang thai moi la "da nhan" ma da co phieu xuat kho roi thi update lại status 31 cho phieu xuat kho do
	  		// // {   // Tao phieu xuat kho =>buoc 3 cua phieu xuat kho
	  			 
	  		// // 	 $DB->query("UPDATE ".root_table."store_request SET request_status = '31'  WHERE request_id='{$ord_i['bill_export_id']}'");
	  		// // }
	  		// else
	  		// {
	  			$DB->query("UPDATE ".root_table."order_item SET ordi_status = '{$status}'  WHERE ordi_id='{$ordi}'");
	  		//}
	  	 
				// UPDATE STATUS ORDER ORIGINAL 
  				$this->update_order_original_status($ord_i['ord_id']);
		}
  		else
  		{
  			//update all1
  			 $_SESSION['error_msg'] = "{$CMS->lang['order_isnot_exits']}";
  			 return false;
  		}
 		
  	
		if($ord_i['ass_key']!="")
		{
			//Hang hoa
			$after_status = "0".$status;
		}
		else
		{
			$after_status = "1".$status;
		}

		$CMS->class->logs->key = "order_{$CMS->input['id']}";
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['update_status_ordi']}: {$CMS->lang['ordi_status_'.$before_status]} =>  {$CMS->lang['ordi_status_'.$after_status]}");

	 
		return true;
	}
	 

	//===========================================================================
	//  Update status order_item
	//===========================================================================
	
	public function update_status_ordi($type = "")
	{
		global $CMS, $DB;
		$status = intval($CMS->input['ordi_status']);
  		if($type == "") // Update theo checkbox order item
  		{
  			if(count($CMS->input['checkbox_ordi']) > 0)
	  		{
	  			foreach ( $CMS->input['checkbox_ordi'] as $key => $value) {
	  				# code...
	  				$ordi_id = $value;
	  				$sql = $DB->query("SELECT * FROM ".root_table."order_item WHERE ordi_id = '{$ordi_id}' AND ordi_deleted = 0 ");
	  				if($DB->num_rows($sql)  == 0)
	  				{
	  					// K tim thay order item
	  					$_SESSION['error_msg'] = "{$CMS->lang['order_module']} #{$ordi_id} {$CMS->lang['not_exits']}";
	  					return false;
	  				}
	  				$ord_i = $DB->fetch_array($sql);
					$before_status = $ord_i['ordi_status'];

	  				// Tao phieu xuat kho
			  		// if($status == 2 AND $ord_i['bill_export_id'] == 0 ) // Trang thai dang giao va chua tao phieu xuat kho lan nao
			  		// {
			  		// 	$bill_return = $this->bill_export($ordi_id, "");
			  		// 	if($bill_return['status'] == true)
			  		// 	{
			  		// 		$bill_id = $bill_return['data_output'];
			  		// 		//Tao phieu xuat kho thanh cong
			  		// 		$DB->query("UPDATE ".root_table."order_item SET ordi_status = '{$status}' , bill_export_id='{$bill_id}'  WHERE ordi_id='{$ordi_id}'");
			  		// 	}
			  		// 	else
			  		// 	{
			  		// 		return false;
			  		// 			//$DB->query("UPDATE ".root_table."order_item SET ordi_status = '{$status}'  WHERE ordi_id='{$ordi}'");
			  		// 	}
			  			 
			  		// }
			  		// elseif($status == 1 AND $ord_i['bill_export_id'] == 0 ) // Trang thai dang giao va chua tao phieu xuat kho lan nao
			  		// {   // Tao phieu xuat kho =>buoc 3 cua phieu xuat kho
			  		// 	$bill_return = $this->bill_export($ordi_id, 31);
			  		// 	if($bill_return['status'] == true)
			  		// 	{
			  		// 		$bill_id = $bill_return['data_output'];
			  		// 		//Tao phieu xuat kho thanh cong
			  		// 		$DB->query("UPDATE ".root_table."order_item SET ordi_status = '{$status}' , bill_export_id='{$bill_id}'  WHERE ordi_id='{$ordi_id}'");
			  		// 	}
			  		// 	else
			  		// 	{
			  		// 		return false;
			  		// 			//$DB->query("UPDATE ".root_table."order_item SET ordi_status = '{$status}'  WHERE ordi_id='{$ordi}'");
			  		// 	}
			  			 
			  		// }
			  		// // elseif($status == 1 AND $ord_i['bill_export_id'] > 0 ) 
			  		// // //  Trang thai moi la "da nhan" ma da co phieu xuat kho roi thi update lại status 31 cho phieu xuat kho do
			  		// // {   // Tao phieu xuat kho =>buoc 3 cua phieu xuat kho
			  			 
			  		// // 	 $DB->query("UPDATE ".root_table."store_request SET request_status = '31'  WHERE request_id='{$ord_i['bill_export_id']}'");
			  		// // }
			  		// else
			  		// {
			  			$DB->query("UPDATE ".root_table."order_item SET ordi_status = '{$status}'  WHERE ordi_id='{$ordi_id}'");
			  		//}

 

	  				if($CMS->input['is_send_mail'] == 1)
	  				{
	  					$this->send_email_order($CMS->order->get_info($ordi_id));
	  				}
	  			}
	  		}
	  		else
	  		{
				$_SESSION["error_msg"] =  "{$CMS->lang['update_status_ordi_error']}";

	  			return false;
	  		}
  		}
  		elseif($type == "all") 
  		{
  			//update all
  			 
  			$DB->query("UPDATE ".root_table."order_item SET ordi_status = '1'  WHERE ord_id='{$CMS->input['id']}'");
  		}
 	 
  		// UPDATE STATUS ORDER ORIGINAL 
  		$this->update_order_original_status($CMS->input['id']);	
		$CMS->class->logs->key = "order_{$CMS->input['id']}";
		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['update_status_ordi']}");

	 
		return true;
	}




	//===========================================================================
	//  Order_item : create invoice and udpate status
	//===========================================================================
	
	public function update_order_item($ord_id = "", $status = "")
	{
		global $CMS, $DB;
		$status = intval($status);
		$sql = $DB->query("SELECT * FROM ".root_table."order_item WHERE ord_id = '{$ord_id}' AND ordi_deleted = 0 ");
 
  		if($DB->num_rows($sql) > 0) // Update theo checkbox order item
  		{

  		 
	  			while ($value = $DB->fetch_array($sql)) 
	  			{
	  				$ordi_id = $value['ordi_id'];
	  				if( ( $value['bill_export_id'] == 0  OR $value['bill_export_id'] == "") AND $CMS->vars['addon_goods_enable'] == 1) // Trang thai dang giao va chua tao phieu xuat kho lan nao
			  		{	
			  			// AND $value['ass_key'] !=''
			  			if($value['product_id'] > 0)
			  			{
			  				// Get type product
			  				$value['product_type'] = $CMS->product->getInfo($value['product_id'] , 'product_type');
			  			}
			  			if($value['product_type'] == 0 OR $value['ass_key'] !='' )
			  			{
			  				$bill_return = $this->bill_export($ordi_id, $status, $value);
				  			if($bill_return['status'] == true)
				  			{
				  				$bill_id = $bill_return['data_output'];

				  				if( $bill_return['type_output'] == "import_bill")
				  				{
				  					$DB->query("UPDATE ".root_table."order_item SET ordi_status = '{$status}' , bill_import_id='{$bill_id}'  WHERE ordi_id='{$ordi_id}'");
				  				}
				  				else
				  				{
				  					//Tao phieu nhap kho thanh cong
				  					$DB->query("UPDATE ".root_table."order_item SET ordi_status = '{$status}' , bill_export_id='{$bill_id}'  WHERE ordi_id='{$ordi_id}'");
				  				}
				  				
				  			}
				  			else
				  			{
				  				 //return false;
				  				$DB->query("UPDATE ".root_table."order_item SET ordi_status = '{$status}'  WHERE ordi_id='{$ordi_id}'");
				  			}
			  			}else
			  			{
			  				// SP loại dịch vụ thi chi update status
			  				$DB->query("UPDATE ".root_table."order_item SET ordi_status = '{$status}'  WHERE ordi_id='{$ordi_id}'");
			  			}
			  			
			  			 
			  		} 
			  		elseif( $value['bill_export_id'] != 0 AND $value['ordi_status'] == 1 AND  $status == 2 AND $CMS->vars['addon_goods_enable'] == 1 )
			  		{
			  			 
			  			// Update status phieu xuat nhap tu xuat/nhap thanh phieu xuat(status: hoan thanh)
			  			//$assets = $CMS->assets->get_info($value['ass_key']);
			  			$bill_ex = $CMS->store_request->get_info($value['bill_export_id']);
			  			$CMS->store_request->quick_edit_bill($value['bill_export_id'],3,31);
			  			// Cap nhat tai san da ban
							
			  			$assets_ex = json_decode($bill_ex['request_product'],true);
			  			$request_product[0]['ass_key'] = $assets_ex[0]['ass_key'];
			  			$request_product[0]['ass_quantity'] = 1;
			  			 
			  			$CMS->assets->updateIsAvailable( $request_product, 1);
			  			// Update trang thai ord_item
			  			$DB->query("UPDATE ".root_table."order_item SET ordi_status = '{$status}'  WHERE ordi_id='{$ordi_id}'");

			  		//	$bill_return = $this->bill_export_2($ordi_id, 2, $value);

			  		}
			  		else
			  		{
			  			$DB->query("UPDATE ".root_table."order_item SET ordi_status = '{$status}'  WHERE ordi_id='{$ordi_id}'");
			  		}
 	 
	  			 
	  		}
		  }
	  		 
   
 	  
		return true;
	}



	//========================================
	// Func: count order filter by status
	//========================================
	public function count_order_by_status($status = 0, $sql_add = "")
	{
		global $CMS, $DB;

		$sql = "SELECT COUNT(ord_id) as cnt FROM ".root_table."order  WHERE {$sql_add} ord_deleted = 0 AND ord_status = '{$status}' ";
		$data = $DB->fetch_data($sql);
		return $data[0]['cnt'];
	}




	function bill_export($ordi_id = "", $status = "", $ordi = array())
	{
		global $CMS, $DB, $member;

		$auto_addbill_import = intval($CMS->input['addbill_import']);
		if($ordi_id == "")
		{
			return false;
		}
		$request_id = $request_im_id = 0;
		if(  count($ordi) == 0)
		{
			//Check co tai san trong phieu kiem kho k?
			$_SESSION['error_msg'] = "{$CMS->lang['select_asset_to_export']}";
			return array("status" => false);
		}
 		$flag_check_stock = 0;
		$data_item = array();
		$total_ex = 0;
		if(is_array($ordi))
		{
			$i = 0;
				# code...
				if( $ordi['product_id'] > 0 )
				{
					$ass = $CMS->assets->get_asset_byproduct($ordi['product_id'], " AND store_id= '{$ordi['store_id']}' AND is_available = 1 ", "" ,1);
					if(! is_array($ass) ) { $flag_check_stock = 1; }
					$ordi['ass_key'] = $ass['ass_key'];
					$product_id = $ordi['product_id'];

				}
				else
				{
					$ass = $CMS->assets->get_info($ordi['ass_key']);
					$product_id = $ass['product_id'];
				}
 
				$store_id = $ass['store_id'];
				$data_item[$i]['ass_name'] = $ass['ass_name'];
				$data_item[$i]['ass_code'] = $ass['ass_code'];
				$data_item[$i]['ass_key'] = $ordi['ass_key'];
				if($ass['ass_price']!= "" AND $ass['ass_price'] > 0)
				{
					$data_item[$i]['ass_price'] = $ass['ass_price'];
				}
				else
				{
					$data_item[$i]['ass_price'] = $ass['ass_purchase_price'];
				}
				$data_item[$i]['ass_quantity'] = 1;
				$data_item[$i]['ass_tax'] = $ordi['ordi_tax'];

				$data_item[$i]['ass_amount'] = (intval($data_item[$i]['ass_quantity']) * $data_item[$i]['ass_price']) + round(($data_item[$i]['ass_price'] * $data_item[$i]['ass_quantity'] * $data_item[$i]['ass_tax'])/100);
 
				$total_ex += $data_item[$i]['ass_amount'];
		 
			// CHECK TON KHO CUA CAC SAN PHAM CHUAN BI XUAT
			if($flag_check_stock == 0)
			{ 
				if(!$CMS->store_request->check_inventory($data_item) AND $auto_addbill_import == 1 ) // Tu dong tao them phieu nhap
				{
					$request_id = $this->init_data_import_bill($product_id, $ordi);
					//echo $request_id;
		 			if($request_id > 0)
		 			{
		 			//	echo "2";
		 				//print_r (array("status" =>true, "data_output" => $request_id, "type_output" => "import_bill"));exit;
		 				return array("status" =>true, "data_output" => $request_id, "type_output" => "import_bill");	//echo "22a";exit;
		 			}
		 			//echo "22a";exit;
				}
			}else
			{
				$request_id = $this->init_data_import_bill($product_id, $ordi);
				//echo $request_id;
	 			if($request_id > 0)
	 			{
	 				//print_r (array("status" =>true, "data_output" => $request_id, "type_output" => "import_bill"));exit;
	 				return array("status" =>true, "data_output" => $request_id, "type_output" => "import_bill");	//echo "122a";exit;
	 			}
	 				//echo "122a";exit;
			}
			

 
			//$ass_ex['request_product'] = $CMS->returns->convert_assets_to_product($data_item);
			$ass_ex['request_product'] = 	$data_item;
			$ass_ex['request_product'] = json_encode($ass_ex['request_product'], JSON_UNESCAPED_UNICODE); 
			$ass_ex['request_type'] = 1;
			$ass_ex['request_subtype'] = 1;
			if($status == 2)
			{
				// Tao phieu xuat kho (buoc 3 cua phieu xuat)
				$ass_ex['request_status'] = 31;
				$ass_ex['request_stage'] = 3;
			}
			else
			{
				$ass_ex['request_status'] = 20;
				$ass_ex['request_stage'] = 2;
			}
			
	 
			$ass_ex['request_amount'] = $total_ex;
			$ass_ex['store_id'] = $store_id;
			$ass_ex['cus_id'] = $ordi['cus_id'];
			$ass_ex['request_note']  = "{$CMS->lang['export_assets']} Order #OD{$ordi['ord_id']}";
 
			$request_id = $CMS->store_request->add($ass_ex, 1);
 			if($request_id != "")
 			{
 				return array("status" =>true, "data_output" => $request_id, "type_output" => "export_bill");
 			}
			
 			return array("status" =>false, "data_output" => "");
		}
	 
		
 		
	}


	function init_data_import_bill($product_id = "", $ordi = "" )
	{	
		global $CMS, $DB;
		// Get info product
		$product_data = $CMS->product->getInfo($product_id);
		$data_item_p[0]['product_id'] = intval($product_data['product_id']);
		$data_item_p[0]['product_name'] =  $product_data['product_name'];
		$data_item_p[0]['product_description'] =  $product_data['product_description'];
		$data_item_p[0]['product_quantity'] =  1;
		$data_item_p[0]['product_price'] =  $product_data['product_price'];
		$data_item_p[0]['product_code'] =  $product_data['product_code'];
		$data_item_p[0]['product_tax'] =  0;
		$data_item_p[0]['product_amount'] =  $product_data['product_price'] + round(floatval($data_item_p[0]['product_tax']) * floatval( $product_data['product_price'])/100);

	   $data_item_import['store_id'] = $ordi['store_id'];
	   $data_item_import['cus_id'] = $ordi['cus_id'];
	   $data_item_import['request_product'] = 	$data_item_p;
	   $data_item_import['request_product'] = json_encode($data_item_import['request_product'], JSON_UNESCAPED_UNICODE); 
	   $data_item_import['request_type'] = 0;
	   $data_item_import['request_subtype'] = 0;
	   $data_item_import['request_status'] = 10;
	   $data_item_import['request_stage'] = 1;
	   $data_item_import['request_amount'] = $data_item_p[0]['product_amount'];
	   $ass_ex['request_note']  = "{$CMS->lang['import_product_bill_stage_1']} Order #OD{$ordi['ord_id']}";

		$request_id = $CMS->store_request->add($data_item_import, 1);
		return $request_id;

	}


	function check_stock($ord_id = "")
	{
		global $CMS, $DB, $member;
		// Check enable kho hàng
		if($CMS->vars['addon_goods_enable'] == 0)
		{ 
			return 0;
		}
		$stock = 0; 
		$sql = $DB->query("SELECT *, count(ass_key) as quantity FROM ".root_table."order_item WHERE ord_id = '{$ord_id}' AND ordi_deleted = 0 AND ass_key <> '' GROUP BY ass_key  ");
 
		if($DB->num_rows($sql) > 0)
		{
		
			$i = 0;
			while ($data = $DB->fetch_array($sql)) {
				# code...
				$ass = $CMS->assets->get_info($data['ass_key']);
				$store_id = $ass['store_id'];
				$data_item[$i]['ass_name'] = $ass['ass_name'];
				$data_item[$i]['ass_code'] = $ass['ass_code'];
				$data_item[$i]['ass_key'] = $ass['ass_key'];
				if($ass['ass_price']!= "" AND $ass['ass_price'] > 0)
				{
					$data_item[$i]['ass_price'] = $ass['ass_price'];
				}
				else
				{
					$data_item[$i]['ass_price'] = $ass['ass_purchase_price'];
				}
				$data_item[$i]['ass_quantity'] = $data['quantity'];
				$data_item[$i]['ass_tax'] = 0;
				// CHECK TON KHO CUA CAC SAN PHAM CHUAN BI XUAT
				// // => Tam thoi tat check ton kho
				 if(!$CMS->store_request->check_inventory($data_item)  )
				 {
				 	unset($_SESSION['error_msg']);
				 	$stock = $stock + 1;
				 }	
			}
		} 
		 

		$sql_2 = $DB->query("SELECT *, count(product_id) as quantity FROM ".root_table."order_item WHERE ord_id = '{$ord_id}' AND ordi_deleted = 0 AND product_id  > '0' GROUP BY product_id  ");
 	 
		if($DB->num_rows($sql_2) > 0)
		{
			$i = 0;
			while ($data_p = $DB->fetch_array($sql_2)) {
				# code...
				$product = $CMS->product->getInfo($data_p['product_id']);
				if($product['product_type'] == 0)
				{
					$store_id = $product['store_id'];
					$data_pitem[$i]['product_id'] = $product['product_id'];
					$data_pitem[$i]['product_name'] = $product['product_name'];
					$data_pitem[$i]['product_code'] = $product['product_code'];
					$data_pitem[$i]['product_key'] = $product['product_key'];
					$data_pitem[$i]['product_price'] = $ass['product_price'];
					$data_pitem[$i]['product_quantity'] = $data_p['quantity'];
					$data_pitem[$i]['store_id'] = $data_p['store_id'];
					$data_pitem[$i]['product_tax'] = 0;
	 				
					// CHECK TON KHO CUA CAC SAN PHAM CHUAN BI XUAT
					// // => Tam thoi tat check ton kho
					 if(!$CMS->store_request->check_inventory($data_pitem,1)  )
					 {	 
					 	unset($_SESSION['error_msg']);
					 	$stock = $stock + 1;
					 }
				}
		
 	
			}
		} 
	 
		return $stock;
	}

	function confirmBooking($ord_id=0, $cus_id=0)
	{
		global $CMS, $DB;

		// Get order info
		$data = $this->get_info($ord_id);
		$CMS->class->logs->key = "order_{$ord_id}";
		$CMS->class->logs->old_data = $data;

		// Get cus id
        if($cus_id)
        {
            $sql_add = " cus_id = {$cus_id} AND ";
        }
        else if ( $data['cus_id'] )
        {
            $sql_add = " cus_id = {$data['cus_id']} AND ";
        }

        // Update order already process or complete
		if ( in_array($data['ord_status'], array(1,2)) == true ) {
            $check = true;
            $_SESSION['msg'] .= $CMS->lang['confrim_booking_successful'];
            return false;
        }
        // Normal situation
        else{
            $check = $DB->query("UPDATE ".root_table."order SET ord_status=1 WHERE {$sql_add} ord_id='{$ord_id}' AND service_type=1 AND ord_status=0");
        }

		// Save logs
		$new_order = $this->get_info($ord_id);  
		$CMS->class->logs->key = "order_{$ord_id}";
		$CMS->class->logs->save_detail("order",$ord_id,$new_order);

		// If success
		if($check)
        {
        	$_SESSION['msg'] .= $CMS->lang['confrim_booking_successful'];
        }
        // Failed
        else
        {
        	$_SESSION['error_msg'] .= $CMS->lang['confrim_booking_error'];
        	return false;
        }

		// Send sms for customer
		$info = json_decode($data['ord_content'], true);

		$sms_service = "";
        foreach ($info['booking_service'] as $product_id) 
        {
            $name_product = $CMS->product->getInfo($product_id, 'product_name');
            $sms_service .= $name_product ? $name_product.", ": "";
        }
        $sms_service = rtrim($sms_service,", ");
        $sms_staff = "";
        if($info['staff_id'])
        {
            foreach ($info['staff_id'] as $user_id) 
            {
                $name = $CMS->user->get_info($user_id, "user_display_name");
                $sms_staff .= $name ? $name.", ": "";
            } 
        }else
        {
            $data['product_description'] = [];
        }
        $sms_staff = rtrim($sms_staff,", ");

        $date_show = date("g:i A",strtotime($info['booking_time']));

        // Send sms
        $data_sms = [
            'dateshow' => $date_show,
            'bookingdate' => $info['booking_date'],
            'servicename' => $sms_service,
            'staffname' => $sms_staff,
            'companymobile' => $CMS->vars['company_mobile'],
        ];

        $sms['sms_from'] = $CMS->vars['sms_nexmo_number'];
        $sms['sms_to'] = $info['cus_phone'];
        $sms['sms_content'] = $CMS->smstpl->renderContent('booking_confirm_to_customer', $data_sms);
//        $sms['sms_content'] .= ". Website: ".$_SERVER['SERVER_NAME'];

        // $sms['ord_id'] = $return['ord_id'];
//        $sms['sms_content'] =  "Your appointment is confirmed at {$date_show}, {$info['booking_date']} ({$sms_service} - {$sms_staff}). To change your appointment, please call {$CMS->vars['company_mobile']}";

        $return = $CMS->sms->add($sms);

        if($return)
        {
        	$_SESSION['msg'] .= $CMS->lang['confrim_send_sms_successful'];
        }else
        {
        	$_SESSION['error_msg'] .= $CMS->lang['confrim_send_sms_error'];
        }

        return true;
	}

	// get list booking
	public function get_list_booking($sql_add = "")
	{
		global $CMS, $DB;

		$output = array();

		// list order is booking
		$sql = "SELECT * FROM ".root_table."order WHERE {$sql_add} service_type = 1 AND ord_deleted = 0 AND ord_note <> '' AND ord_content <> '' ORDER BY booking_date ASC, booking_hours ASC";
		$sql = $DB->query($sql);
		if ( $DB->num_rows($sql) > 0 )
		{
			while ( $data = $DB->fetch_assoc($sql) )
			{
				$output[] = $data;
			}
		}

		return $output;
	}

	function getOrderinfo($cus_id=0, $type="")
	{
		global $CMS, $DB;

		$select = "*";
		$clause = "";
		if($type == "number")
		{
			$select = " COUNT(ord_id) as number_order ";
		}else if($type=="total")
        {
            $select = " SUM(ord_total) as total_order ";
        }else if($type=="last")
        {
            $select = " MAX(ord_id) as number_order ";
        }else if($type=="paid")
		{
			$select = " SUM(ord_total) as total_order ";
			$clause = " AND payment_status=1";
		}else if( $type=="unpaid")
		{
			$select = " SUM(ord_total) as total_order ";
			$clause = " AND payment_status=0";
		}

		// Query
		$DB->query("SELECT {$select} FROM ".root_table."order WHERE cus_id='{$cus_id}' {$clause}");
		$data = $DB->fetch_array();

		return in_array($type, ['number', 'last']) ? $data['number_order'] : $CMS->class->input->currency($data['total_order']);
	}


	function getItemByOrder($ord_id=0)
	{
		global $CMS, $DB;

		$DB->query("SELECT * FROM ".root_table."order_item WHERE ordi_deleted=0 AND ord_id='{$ord_id}'");
		$output = [];
		while ($result = $DB->fetch_array()) 
		{
			$output[] = $result;
		}
		return $output;
	}

	function getinfo_ordi($ordi_id = "")
	{
		global $CMS, $DB;

		$sql = $DB->query("SELECT * FROM ".root_table."order_item WHERE ordi_deleted=0 AND ordi_id='{$ordi_id}'");
		if($DB->num_rows($sql) > 0)
		{
			$result = $DB->fetch_array($sql);
			
			return $result;
		}else
		{
			return false;
		}
		
	}


	function sendEmailGiftcard($order=[])
	{
		global $CMS, $DB;

		$type = $order['ord_info']['send_to_friend'] ? 1 : 0;

		if(!$type)
        {
            $time = $CMS->class->date->date_format(time());
            $CMS->email->email_template = "order_success";
            $CMS->email->email_to = $order['ord_info']['ship_email'];
            $CMS->email->email_toname = $order['ord_info']['ship_full_name'];

            // email_from
            $CMS->email->data['date_send'] =  $time;
            $CMS->email->data['cus_name'] =  $order['ord_info']['ship_full_name'];
            
            $ord_id = $order['ord_id'];
            $arr_item = $CMS->giftcards->getGitemByOrder($ord_id);

            $table_html = "<table class=\"table table-bordered\" width=\"100%\"><tbody><tr bgcolor=rgb(230, 229, 229) valign='middle' align='center'><td width='80%' style='text-align: center;'>Item</td><td width='20%' style='text-align: center;'>Price</td></tr>";
            $total = 0;
            foreach ($arr_item as $id => $data) 
            {
                $total += $data['gitem_amount_remain'];
                $amount_remain = $CMS->class->input->currency($data['gitem_amount_remain']);
                $data['image'] = $CMS->vars['upload_url']."/giftcards/".$data['gitem_code'].".png";
                $table_html .= "<tr style='text-align: center;'><td><img src=\"{$data['image']}\" style=\"max-width:80%; max-height: 300px;\" /><br/>{$data['product_name']}</td><td>{$amount_remain}</td></tr>";
            }
            $total_show = $CMS->class->input->currency($total);
            $table_html .="<tr><td style='text-align: right;'>Sub-Total</td><td style='text-align: center;'><strong>{$total_show}</strong></td></tr>";
            $table_html .= "</tbody></table>";
            $CMS->email->data['table_content'] = $table_html;
            $CMS->email->data['website_name'] = $CMS->vars['website_title'];
            $CMS->email->quick_send(0,0);
            return;
        }else
        {

            // Send mail for payer
            $time = $CMS->class->date->date_format(time());
            $CMS->email->email_template = "email_send_payer";
            $CMS->email->email_to = $order['ord_info']['ship_email'];
            $CMS->email->email_toname = $order['ord_info']['ship_full_name'];

            // data
            $CMS->email->data['date_send'] =  $time;
            $CMS->email->data['cus_name'] =  $order['ord_info']['ship_full_name'];
            $CMS->email->data['cus_email_to'] =  $order['ord_info']['recipient_email'];
            $CMS->email->data['website_name'] = $CMS->vars['website_title'];
            $CMS->email->quick_send(0,0);
            

            // Send to friend of payer
            $CMS->email->email_template = "email_send_to_friend";
            $CMS->email->email_to = $order['ord_info']['recipient_email'];
            $CMS->email->email_toname = $order['ord_info']['recipient_name'];

            $CMS->email->data['date_send'] =  $time;
            $CMS->email->data['cus_name_from'] =  $order['ord_info']['ship_full_name'];
            $CMS->email->data['cus_email_from'] =  $order['ord_info']['ship_email'];
            $CMS->email->data['message'] = $order['ord_info']['recipient_message'] ? "Message: ".$order['ord_info']['recipient_message'] : "";
            $CMS->email->data['website_name'] = $CMS->vars['website_title'];
            
            $ord_id = $order['ord_id'];
            $arr_item = $CMS->giftcards->getGitemByOrder($ord_id);

            $table_html = "<table class=\"table table-bordered\" width=\"100%\"><tbody><tr bgcolor=rgb(230, 229, 229) valign='middle' align='center'><td width='80%' style='text-align: center;'>Item</td><td width='20%' style='text-align: center;'>Price</td></tr>";
            $total = 0;
            foreach ($arr_item as $id => $data) 
            {
                $total += $data['gitem_amount_remain'];
                $amount_remain = $CMS->class->input->currency($data['gitem_amount_remain']);
                $data['image'] = $CMS->vars['upload_url']."/giftcards/".$data['gitem_code'].".png";
                $table_html .= "<tr style='text-align: center;'><td><img src=\"{$data['image']}\" style=\"max-width:80%; max-height: 300px;\" /><br/>{$data['product_name']}</td><td>{$amount_remain}</td></tr>";
            }
            $total_show = $CMS->class->input->currency($total);
            $table_html .="<tr><td style='text-align: right;'>Sub-Total</td><td style='text-align: center;'><strong>{$total_show}</strong></td></tr>";
            $table_html .= "</tbody></table>";
            $CMS->email->data['table_content'] = $table_html;
            $CMS->email->quick_send(0,0);
            return;

        }
	}

	function rating($id, $commission_rating)
    {
	    global $CMS, $DB;

        $id = $id*1;
        $commission_rating = $commission_rating*1;

	    if(!$id)
        {
            $_SESSION['msg'] = $CMS->lang['invalid_data'];
            return false;
        }

        $sql = "UPDATE ".root_table."order SET ord_commission_rating={$commission_rating} WHERE ord_id={$id}";

	    if(!($result = $DB->query($sql)))
        {
            $_SESSION['msg'] = "Updated failed";
            return false;
        }

        $sql_items = "UPDATE ".root_table."order_item SET ordi_commission_rating={$commission_rating} WHERE ord_id={$id}";

        if(!($result = $DB->query($sql_items)))
        {
            $_SESSION['msg'] = "Updated items failed";
            return false;
        }

        $_SESSION['msg'] = "Updated successful";

        return true;
    }

    function getShipBillByOrder($ord_id=0)
    {
	    global $CMS, $DB;

	    if(!$ord_id) {return false;}

	    $sql = $DB->query("SELECT * FROM ".root_table."shipbill_address WHERE ord_id='{$ord_id}' AND sp_deleted=0 LIMIT 1");
	    $arr = $DB->fetch_array($sql);
	    $ship = [];
	    $bill = [];
	    $ship['first_name'] = $arr['ship_first_name'];
	    $ship['last_name']  = $arr['ship_last_name'];
	    $ship['full_name']  = $arr['ship_full_name'];
	    $ship['email'] 		= $arr['ship_email'];
	    $ship['phone'] 		= $arr['ship_phone'];
	    $ship['company'] 	= $arr['ship_company'];
	    $ship['address'] 	= $arr['ship_address'];
	    $ship['address2'] 	= $arr['ship_address2'];
	    $ship['zipcode'] 	= $arr['ship_zipcode'];
	    $ship['city'] 		= $arr['ship_city'];
	    $ship['district'] 	= $arr['ship_district'];
	    $ship['province'] 	= $arr['ship_province'];
	    $ship['country'] 	= $arr['ship_country'];
	    
	    $ship['city_name'] 	= $CMS->country->nameCity($arr['ship_city']);
	    $ship['city_name']	= $ship['city_name'] ? $ship['city_name'] : $ship['city'];
		
		$ship['district_name'] = $CMS->country->nameDistrict($arr['ship_district']);
		$ship['district_name'] = $ship['district_name'] ? $ship['district_name'] : $ship['district'];
		
		$ship['province_name'] = $CMS->country->nameState($arr['ship_province']);
		$ship['province_name'] = $ship['province_name'] ? $ship['province_name'] : $ship['province'];

	    $ship['country_name'] = $CMS->country->nameCountry($arr['ship_country']);
		$ship['country_name'] = $ship['country_name'] ? $ship['country_name'] : $ship['country'];

		$ship['address_full']    = $this->generalAddress($ship);
		$ship['address_full_us'] = $this->generalAddress($ship, 1);

	    $bill['first_name'] = $arr['bill_first_name'];
	    $bill['last_name']  = $arr['bill_last_name'];
	    $bill['full_name']  = $arr['bill_full_name'];
	    $bill['email'] 		= $arr['bill_email'];
	    $bill['phone'] 		= $arr['bill_phone'];
	    $bill['company'] 	= $arr['bill_company'];
	    $bill['address'] 	= $arr['bill_address'];
	    $bill['address2'] 	= $arr['bill_address2'];
	    $bill['zipcode'] 	= $arr['bill_zipcode'];
	    $bill['city'] 		= $arr['bill_city'];
	    $bill['district'] 	= $arr['bill_district'];
	    $bill['province'] 	= $arr['bill_province'];
	    $bill['country'] 	= $arr['bill_country'];
	    
	    $bill['city_name'] = $CMS->country->nameCity($arr['bill_city']);
	    $bill['city_name'] = $bill['city_name'] ? $bill['city_name'] : $bill['city'];

		$bill['district_name'] = $CMS->country->nameDistrict($arr['bill_district']);
		$bill['district_name'] = $bill['district_name'] ? $bill['district_name'] : $bill['district'];

		$bill['province_name'] = $CMS->country->nameState($arr['bill_province']);
		$bill['province_name'] = $bill['province_name'] ? $bill['province_name'] : $bill['province'];

	    $bill['country_name'] = $CMS->country->nameCountry($arr['bill_country']);
		$bill['country_name'] = $bill['country_name'] ? $bill['country_name'] : $bill['country'];

		$bill['address_full']    = $this->generalAddress($bill);
		$bill['address_full_us'] = $this->generalAddress($bill, 1);
		
	    return array($ship, $bill, $arr['sp_id']);
	}

	function generalAddress( $data=[], $type = 0 )
    {
        global $CMS, $DB;

        $output = '';

        if( $data['address'] )
        {
            $output .= $data['address'];
        }

        if( $data['address2'] )
        {
            $output .= $type ? ' <br>' : ' ';
            $output .= $data['address2'];
        }

        if( $data['city'] )
        {
            $output .= $type ? ' <br>' : ' ';
            $output .= $data['city'];
        }

        if( $data['province'] )
        {
            $output .= ', ' . $data['province'];
        }

        if( $data['zipcode'] )
        {
            $output .= $data['province'] ? '' : ', ';
            $output .= ' ' . $data['zipcode'];
        }

        return $output;
    }


	function getListComment($ord_id=0, $cus_id=0)
	{
		global $CMS, $DB;

		$results = $DB->fetch_data("SELECT * FROM ".root_table."comment WHERE module_name='order' AND cus_id='{$cus_id}' AND module_id='{$ord_id}' ORDER BY comment_time DESC", "comment");
		$output = [];
		if($results)
        {
            foreach( $results as $data )
            {
            	$data = $this->convertComment($data);
            	$output[] = $data;
            }
        }

        return $output;
	}

	function convertComment($data)
	{
		global $CMS, $DB;

		$data['comment_time_show'] = $CMS->class->date->date_format($data['comment_time'],1);
		$user = $CMS->user->get_info($data['user_id']);
		if($CMS->permit['user_read'] )
		{
			$data['user_name'] = "<a class='link-text2' href=\"{$CMS->vars['root_domain']}/?site=customer&act=show&id={$user['user_id']}\">{$user['user_display_name']}</a>";
		}else
		{
			$data['user_name'] = $customer['cus_full_name'];
		}

		$data['is_notify_customer'] = $data['comment_hide'] == 0 ? "<i class=\"fa fa-check fa-primary\"></i>" : "<i class=\"fa fa-times fa-default\"></i>";

		// Order status
		switch ($data['ord_status']) {
			case '0':
				$data['status']= "<span class='label label-default'>{$CMS->lang['order_status_0']}</span>";
				$data['ord_status_text']= $CMS->lang['order_status_0'];
				break;
			case '1':
				$data['status']= "<span class='label label-warning'>{$CMS->lang['order_status_1']}</span>";
				$data['ord_status_text']= $CMS->lang['order_status_1'];
				break;
			case '2':
				$data['status']= "<span class='label label-success'>{$CMS->lang['order_status_2']}</span>";
				$data['ord_status_text']= $CMS->lang['order_status_2'];
				break;
			case '3':
				$data['status']= "<span class='label label-danger'>{$CMS->lang['order_status_3']}</span>";
				$data['ord_status_text']= $CMS->lang['order_status_3'];
				break;	
			case '4':
				$data['status']= "<span class='label label-pink'>{$CMS->lang['order_status_4']}</span>";
				$data['ord_status_text']= $CMS->lang['order_status_4'];
				break;				
		}

		return $data;
	}

	function updatelog()
	{
		global $CMS, $DB, $member;

		$ord_id = intval($CMS->input['id']);
		$order = $CMS->order->get_info($ord_id);
 		if(! is_array($order))
 		{
 			$_SESSION['error_msg'] = "{$CMS->lang['order_edit_error']}";
 			return false;
 		}

 		if(!$CMS->input['comment_content'])
 		{
 			$_SESSION['error_msg'] = "{$CMS->lang['plz_enter_your_comment']}";
 			return false;
 		}

 		$CMS->class->logs->key = "order_{$ord_id}";    
		$CMS->class->logs->old_data = $order;
		$CMS->class->logs->insert("order_{$ord_id}");



		$ship_deliver = intval($CMS->input['ship_deliver']);
		$tracking_code = $CMS->input['tracking_code'];
		$ord_status = intval($CMS->input['ord_status']);
		$ord_status_custom = isset($CMS->input['ord_status_custom']) ? intval($CMS->input['ord_status_custom']) : 0;
		$shipping_info = json_decode($order['shipping_info'], 1);
		$shipping_info['ship_deliver'] = $ship_deliver;
		$shipping_info = json_encode($shipping_info, JSON_UNESCAPED_UNICODE); 


		// Insert comment for customer
        $module_id = $ord_id;
    	$module_name = "order";
    	$user_id = $member['user_id'];
    	$module_name = "order";
    	$comment_name = $CMS->lang['order_status_'.$ord_status];
    	$comment_content = $CMS->class->editor->input('comment_content');

    	$comment_time = time();
    	$comment_ip_address = $_SERVER['REMOTE_ADDR'];
    	$comment_approved = 1;// Mặc định là duyệt
    	$comment_hide = intval($CMS->input['send_notify']) == 1 ? 0 : 1; // 0: Hiện, 1: Ẩn 

    	// Insert logs comment
    	$DB->query("INSERT INTO ".root_table."comment (module_id, module_name, user_id, cus_id, comment_name, comment_content, comment_time, comment_ip_address, comment_approved, ord_status, ord_status_custom, comment_hide) VALUES ('{$module_id}', '{$module_name}', '{$user_id}', '{$order['cus_id']}', '{$comment_name}', '{$comment_content}', '{$comment_time}', '{$comment_ip_address}', '{$comment_approved}', '{$ord_status}', '{$ord_status_custom}', '{$comment_hide}')");
    	$comment_id = $DB->last_insert_id();
    	if($comment_id)
    	{
    		// Delete cache
			$CMS->class->cache->mdelete("comment");

    		// Create log
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['title_addlog_successful']} <b>#{$comment_id}</b>")."<br />";
			// Send email
			if($comment_hide == 0)
			{
				$data_comment = $CMS->comment->get_info($comment_id);
				$data_customer = $CMS->customer->getInfo($order['cus_id']);
				$order_convert = $this->convertvalue($order);
				$this->sendNotifyCustomer($data_comment, $data_customer, $order_convert);
			}
    	}else
    	{
    		$_SESSION["msg"] .= $CMS->class->logs->insert($CMS->lang['title_addlog_error'])."<br/>";
    	}

    	// update order status
    	$DB->query("UPDATE ".root_table."order SET ord_status='{$ord_status}', tracking_code='{$tracking_code}', ord_status_custom='{$ord_status_custom}', shipping_info='{$shipping_info}' WHERE ord_id='{$ord_id}'");
    	$DB->query("UPDATE ".root_table."order_item SET ordi_status = '{$ord_status}' WHERE ord_id='{$ord_id}'"); 

    	// Delete cache
		$CMS->class->cache->mdelete("order");

		// Create log
		$CMS->class->logs->key = "order_{$ord_id}";   
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['order_edited']} <b>{$order['ord_name']}</b>")."<br />";

		// Get info
		$new_order = $this->get_info($ord_id);
 		 
		$CMS->class->logs->key = "order_{$ord_id}";   
		$CMS->class->logs->save_detail("order",$ord_id,$new_order);
		
    	return true;

	}


	function getNumberOrderByCstatus($list_status=[], $ord_status=0)
	{
		global $CMS, $DB;

		$output = [];
		if(is_array($list_status))
		{
			foreach ($list_status as $status) 
			{
				$results = $DB->fetch_data("SELECT 0 FROM ".root_table."order WHERE ord_deleted=0 AND ord_status_custom='{$status}' AND ord_status='{$ord_status}'", "order");
				$output[$status] = count($results);
			}
		}

		return $output;
	}

	/**
	* ThamLV d22-6-2018
	* Get default store
	*/
	function getDefaultStoreFollowProducts($items)
	{
		global $CMS;

		$store_id = 0;
		if( is_array($items) )
		{
			foreach ( $items as $key => $item ) 
			{
				$store_id = $CMS->product->get_info($item['product_id'], 'store_id');
				break;
			}
		}

		return $store_id;
	}


	function sendNotifyCustomer($comment=[], $customer=[], $order=[])
	{
		global $CMS, $DB, $member;

		$CMS->email->email_template = "notify_customer";
		$CMS->email->email_to = $customer['cus_email'];
		$CMS->email->email_toname = $customer['cus_full_name'];

		$host = parse_url($CMS->vars['root_domain'])['host'];
        $CMS->email->data['website_name'] = $host;
		$CMS->email->data['cus_name'] = $customer['cus_full_name'];	
		$CMS->email->data['content_email'] =<<<EOF
			<p>You have a new notify from you order #{$order['ord_name']}</p>
			<p><strong>Information order</strong></p>
			<p>Order name: #{$order['ord_name']}</p>
			<p>Order status: <strong>{$order['ord_status_text']}</strong></p>
			<p>Message content: {$comment['comment_content']}</p>
EOF;

		$CMS->email->quick_send(0,0);
		return true;
	}

	/*
	* Convert items
	*/
	function convertItems( $data = [] )
	{
		global $CMS, $DB, $member;

		$output = [];

		foreach ( $data as $item ) 
		{
			if( isset($output[$item['product_id']]) )
			{
				$output[$item['product_id']]['ordi_quantity'] += $item['ordi_quantity'];
				$output[$item['product_id']]['ordi_total_tax'] += $item['ordi_total_tax'];
				$output[$item['product_id']]['ordi_total_discount'] += $item['ordi_total_discount'];
				$output[$item['product_id']]['ordi_subtotal'] += $item['ordi_subtotal'];
				$output[$item['product_id']]['ordi_total'] += $item['ordi_total'];
			}
			else
			{
				$output[$item['product_id']] = $item;
			}
		}

		return array_values($output);
	}

	/**
     * Download file
     */
    function download()
    {
        global $CMS;

        $root = "order_files";

        $fileName = urldecode($CMS->input['file']);

        $exp = explode('-',$fileName);

        $folder = $exp[1];

        $redirect = $CMS->vars['root_domain'];

        if(!$folder || !is_dir($uploadDir = "{$CMS->vars['upload_dir']}/{$root}/{$folder}"))
        {
            $_SESSION['msg'] = "Have no any attachment";
        }
        else
        {
            if(!is_file($filePath = "{$uploadDir}/{$fileName}"))
            {
                $_SESSION['msg'] = "Not found file";
            }
            else
            {
                ezy::load_model("download");
                download::downFile($filePath);
            }
        }

        $CMS->global->redirect($redirect);
    }

    //===========================================================================
	//  Pending order
	//===========================================================================
	public function pending_order( $ord_id = "", $status = "" )
	{
		global $CMS, $DB, $member;
		// Status : 0 => Chờ
		// Status : 1 => Hoan thành
		// Status : 2 => Dang xu ly
		// Status : 3 => Hủy
		// Status : 6 => Lưu
 
		$id = intval($ord_id ? $ord_id : $CMS->input['id']);
 		$order = $CMS->order->get_info($id);

 		if( !is_array($order) )
 		{
 			$_SESSION['error_msg'] = \lib\input::arrayValue($CMS->lang, 'order_isnot_exits');
 			return false;
 		}
 		$old_status = $order['ord_status'];

 		// Update status
 	 	$DB->query("UPDATE ".root_table."order SET  ord_status = '{$status}' WHERE ord_id='{$id}'");
 	 	$DB->query("UPDATE ".root_table."order_item SET  ordi_status = '{$status}' WHERE ord_id='{$id}'"); 

 	 	// Get info
		$new_order = $this->get_info($id );
 		$new_status = $new_order['ord_status'];
	 	
 		// Add logs
		$CMS->class->logs->key = "order_{$id}";
		$_SESSION["msg"] = $CMS->class->logs->insert("{$CMS->lang['process_order']} {$order['ord_name']}, <b> {$CMS->lang["ord_status_{$old_status}"]} -> {$CMS->lang["ord_status_{$new_status}"]}</b>")."<br />";
		
		// Details
		$CMS->class->logs->key = "order_{$id}";
		$CMS->class->logs->old_data = $order;
		$CMS->class->logs->save_detail("order", $id, $new_order);
 
		if( \lib\input::get('is_send_mail') == 1 )
		{
			$this->send_email_order($new_order);
		}
	  				
		return true;
	}
}




?>