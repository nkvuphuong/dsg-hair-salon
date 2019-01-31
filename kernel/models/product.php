<?php

namespace models;

use \core\ezy;
use lib\input;
use models\attribute;


if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->product = new classProduct;

class classProduct {
	public $per_page = 20;
	public $show_page = '';
	public $sql_query = '';
	public $arrange_data = '';
	public $prefix_html = "";
	public $suffix_html = "";
	public $html;
	public $record_cnt = 0;
	public $control = 0;

    /**
     * LHL-08/08/2017
     * Custom form
     * @var array
     */

	public $form_career = array(
		"dem" => "form_nails",
	    "nai" => "form_nails",
	    "w3n" => "form_nails",
	    "web" => "form_nails",
        "mer" => "form_merchant",
        "rea" => "form_realestate",
        "tra" => "form_travel",
        "res" => "form_restaurant",
        "fre" => "form_restaurant",
        "fna" => "form_nails",
        "wco" => "form_introduction",
        // Fmer
        "fme" => "form_merchant",
        "fco" => "form_introduction",
        "ftr" => "form_travel",

        "nms" => "form_introduction",
        "ibe" => "form_nails", 
    );

    public $form_career_service = array(
    	"dem" => "form_nails",
	    "nai" => "form_nails",
	    "fna" => "form_nails",
	    "w3n" => "form_nails",
	    "web" => "form_nails",
        "mer" => "form_nails",
        "rea" => "form_nails",
        "wco" => "form_nails",
        
        "fme" => "form_nails",
        "fco" => "form_nails",
        "ftr" => "form_nails",
        "dsg" => "form_dsg",

        "nms" => "form_nails",
        "ibe" => "form_nails",
    );

	static public $form_theme = ""; // Using for product
	static public $form_theme_service = ""; // Using for service

    /**
     * Using for whitelist fields
     * @var
     */

	public $form_fields = array(
        // "nai" => array(),
        "mer" => array("color", "weight", "length", "width", "height"),
        "fme" => array("color", "weight", "length", "width", "height"),
        "rea" => array("acreage", "city", "district", "wards", "wards_search", "street", "street_search", "map", "facade", "entrance", "direction", "dbalcon", "nfloors", "nroom", "nbedroom", "nbathrooms", "internet", "furniture", "foutside", "utilities", "p_price_show","real_phone"),
        "tra" => array("map", "tra_number_day", "tra_number_night", "tra_time_start", "tra_time_end", "tra_vehicle_start", "tra_vehicle_end", "city", "district", "tra_minimum_seat", "tra_maximum_seat", "tra_summary_travel"),
    );

    /**
     * @var string
     *      Use for thumbnail image
     */
    public $thumb_folder = "thumbnail";

    /**
     * @var array
     *      Define size for images thumb (Width)
     */
    public $thumb_size = [
        'L' => 550,
        'M' => 300,
        'S' => 150
    ];

    /**
     * @var string $cache_prefix
     */
    public $cache_prefix = 'product';

	public function auto_run() {
		global $CMS, $DB, $member;

        // Define theme used
        if ( !empty(ezy::$theme_key) )
        {
            self::$form_theme = isset($this->form_career[ezy::$theme_key]) ? $this->form_career[ezy::$theme_key] : "";
            self::$form_theme_service = isset($this->form_career_service[ezy::$theme_key]) ? $this->form_career_service[ezy::$theme_key] : "";
        }
        // End define
		
		if (!isset($this->html)) {	
			
			$this->html = $CMS->class->template->load_template("skin_product", "product");  
		}

		// if ($CMS->class->cache->check("user_{$member['user_id']}_product_{$CMS->vars['default_language']}")) {
		// 	$CMS->vars['action_controller']=$CMS->class->cache->load("user_{$member['user_id']}_product_{$CMS->vars['default_language']}");
 
		// } else {
			$data = "";
			 


		  if ($CMS->permit["order_add"]) {
				$this->control = 1;
				$data .=<<<EOF
 			   <option value="order_add_multi">{$CMS->lang['title_add_order']}</option>
EOF;
			}

		// Xoá multi
		if ($CMS->permit["product_delete"] and $CMS->vars['addon_goods_enable'] == 0 ) 
		{
				$this->control = 1;
				$data .=<<<EOF
 			   <option value="delete_all">{$CMS->lang['title_delete_all']}</option>
EOF;
		}

		// Xoá multi
		if ($CMS->permit["product_hide_all"]  ) 
		{
				$this->control = 1;
				$data .=<<<EOF
 			   <option value="hide_all">{$CMS->lang['act_hide_all']}</option>
EOF;
		}

		// Add multi to store
		if( $CMS->permit["product_edit"] ) 
		{
				$this->control = 1;
				$data .=<<<EOF
 			   <option value="assign_to_store">{$CMS->lang['act_assign_to_store']}</option>
 			   <option value="unassign_store">{$CMS->lang['act_unassign_store']}</option>
EOF;
		}

			 
		    $data .= "<option value=\"preview_barcode\">{$CMS->lang['print_barcode']}</option>";
		    $data .= "<option value=\"arrange\">{$CMS->lang['act_arrange']}</option>";

			 
			$data = $this->control == 1 ?  $data : "";
			$CMS->class->cache->save("user_{$member['user_id']}_product_{$CMS->vars['default_language']}", $data);
			$CMS->vars['action_controller'] = $data;
 
		// }
		
		$this->action_control = $this->html->control();
	}


	public function listing() {
		global $CMS, $DB, $member;
		$this->arrange_data = trim("product_id,product_name,product_code,product_status,product_type,product_price,product_time");
		$order = $CMS->input['order'];
		$by = $CMS->input['by'];

		// Accepted keywords
		$list_field = array("product_id", "product_name", "product_code", "product_status", "product_type", "product_price", "product_time");
		$list_by = array("desc","asc");
		// Filter them
		$default_field = in_array($order, $list_field) ? $order : $list_field[0];
		$default_order = in_array($by, $list_by) ? $by : $list_by[0];
		 
 


		$where = '';
		if ( $CMS->input['p_name'] != "" AND  $CMS->input['p_code'] != "") {
			$p_name = urldecode($CMS->input['p_name']);
			$p_code = urldecode($CMS->input['p_code']);
			$where .= " AND (`product_name` LIKE '%{$p_name}%' OR `product_code` LIKE '%{$p_code}%' )  ";
		}
		elseif( $CMS->input['p_name'] != "")
		{
			$p_name = urldecode($CMS->input['p_name']);
			$where .= " AND `product_name` LIKE '%{$p_name}%'   ";
		}
		elseif( $CMS->input['p_code'] != "")
		{
			$p_code = urldecode($CMS->input['p_code']);
			$where .= " AND `product_code` LIKE '%{$p_code}%'   ";
		}

		if ( $CMS->input['p_id'] != "" && is_numeric($CMS->input['p_id'])) {
			$where .= " AND `product_id`='{$CMS->input['p_id']}' ";
		}

		// if (!empty($CMS->input['p_type']) && in_array($CMS->input['p_type'], array(0,1))) {
		// 	$where .= " AND `product_type`='{$CMS->input['p_type']}' ";
		// }
		// 
		//Detect load list sort by p_type : service / product
		//
        if(isset($CMS->input['p_type']) && $CMS->input['p_type'] !=='')
        {
            $where .= " AND `product_type`='{$CMS->input['p_type']}' ";
        }
		
		if ( $CMS->input['p_status'] != "" && in_array($CMS->input['p_status'], array(0,1,2))) {
			$where .= " AND `product_status`='{$CMS->input['p_status']}' ";
		}

		// ThamLV d14-5-2018
		if( isset(ezy::$theme_key) AND ezy::$theme_key == 'nms' )
		{
			if( ! empty( $CMS->input['p_group'] ) )
			{
				$group_ids = [$CMS->input['p_group'] => $CMS->input['p_group']];
				$group_ids = $this->getListGroupChildId($CMS->input['p_group'], 0, $group_ids);
				$group_ids = array_values($group_ids);
				$group_ids = implode(', ', $group_ids);
				$group_ids = trim($group_ids, ',');

				$where .= " AND `product_group` IN ({$group_ids}) ";
			}
		}
		else
		{
			if ( $CMS->input['p_group'] !="" && !empty($CMS->input['p_group'])) {
				$where .= " AND `product_group`='{$CMS->input['p_group']}' ";
			}	
		}

		if ( $CMS->input['p_manufacture'] !="" && !empty($CMS->input['p_manufacture'])) {
			$where .= " AND `product_manufacture`='{$CMS->input['p_manufacture']}' ";
		}
		if ( $CMS->input['p_supplier'] !=""  && !empty($CMS->input['p_supplier'])) {
			$where .= " AND `sup_id`='{$CMS->input['p_supplier']}' ";
		}

		if($CMS->input['ids'] != '')
        {
            $where .= " AND `product_id` IN ({$CMS->input['ids']}) ";
        }

        if( ! empty($CMS->input['listgroup']) )
		{
			$arrgroup = [];
			$listgroup = explode(",", trim(urldecode($CMS->input['listgroup']), ","));
			foreach ( $listgroup as $group ) 
			{
				$group = intval($group);
				if( $group )
				{
					$arrgroup = array_merge($arrgroup, $this->getListGroupChildId($group, 0, [$group=>$group]));
				}
			}

			if( ! empty($arrgroup) )
			{
				$where_group = implode(',', $arrgroup);
				$where_group = trim($where_group, ',');
				if( $where_group )
				{
					$where .= " AND product_group IN ({$where_group}) ";
				}
			}

			$this->prefix_html .= "&listgroup={$CMS->input['listgroup']}";
		}

		$this->prefix_html = empty($this->prefix_html) ? '' : "?site=product{$this->prefix_html}&page=";

        $sql = "SELECT * FROM `".root_table."product` WHERE `product_deleted`=0 {$where} ORDER BY {$default_field} {$default_order}";
        // print $sql; exit;

		list($this->show_page, $cacheData) = $DB->fetch_listing($sql,$this->per_page,$this->prefix_html,$this->suffix_html,$CMS->input['page'], $this->cache_prefix);

        $this->show_page = str_replace("ajax=1","ajax=0",$this->show_page);

        $arr = [];

		if ($cacheData)
		{
			foreach ($cacheData as $result)
            {
				array_push($arr,$this->convertvalue($result));
			}
		}

		return $arr;
	}
	public function convertvalue($data=null) {
		global $CMS, $DB, $member;

		$data['data_bk'] = $data_bk = $data;

		$data['product_commission'] = $this->commission_format($data_bk['product_commission_value'], $data_bk['product_commission_type']);

        if($CMS->vars['translations'])
        {
        	$name = @json_decode($data['product_name_lang'], true);
	        $data['product_name'] = $name ? $name : $data['product_name'];

	        $shorturl = @json_decode($data['product_shorturl_lang'], true);
	        $data['product_shorturl'] = $shorturl ? $shorturl : $data['product_shorturl'];
	// print "<pre>";print_r($data['news_description']);exit;
	        $description = @json_decode($data['product_description_lang'], true);
	        $data['product_description'] = $description ? $description : $data['product_description'];

	        $information_1 = @json_decode($data['product_information_1_lang'], true);
	        $data['product_information_1'] = $information_1 ? $information_1 : $data['product_information_1'];

	        $information_2 = @json_decode($data['product_information_2_lang'], true);
	        $data['product_information_2'] = $information_2 ? $information_2 : $data['product_information_2'];

            //Đa ngôn ngữ
            if(!is_array($data['product_shorturl']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $shorturl = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    $shorturl[$langCode] = $data['product_shorturl'];
                }

                $data['product_shorturl'] = $shorturl;
            }

            if(!is_array($data['product_name']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $name = [];
                $name_bk = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                	$name[$langCode] = $data['product_name'];
	            	// Check permission to read Info
					if ( $CMS->permit["product_read"] == true )
					{
						$site = $data['product_type'] == 0 ? 'product' : 'service';
						$name_bk[$langCode] = "<a href='{$CMS->vars['root_domain']}/?site={$site}&act=show&id={$data['product_id']}'>{$data['product_name']}</a>";
						
					}

                }

                $data['product_name_bk'] = $name_bk;
                $data['product_name'] = $name;
            }else
            {
            	$data['product_name_bk'] = $data['product_name'];

            	$name_bk = [];
            	// Check permission to read Info
				if ( $CMS->permit["product_read"])
				{
					$site = $data['product_type'] == 0 ? 'product' : 'service';
					foreach ($data['product_name_bk'] as $langCode => $product_name)
	                {
						$name_bk[$langCode] = "<a href='{$CMS->vars['root_domain']}/?site={$site}&act=show&id={$data['product_id']}'>{$product_name}</a>";
					}
					$data['product_name_bk'] = $name_bk;
				}

            }

            if(!is_array($data['product_description']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $description = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    $description[$langCode] = $data['product_description'];
                }
                $data['product_description'] = $description;
            }
            
            if(!is_array($data['product_information_1']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $content = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    $content[$langCode] = $data['product_information_1'];
                }

                $data['product_information_1'] = $content;
            }

            if(!is_array($data['product_information_2']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $content = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    $content[$langCode] = $data['product_information_2'];
                }

                $data['product_information_2'] = $content;
            }

        } else {

        	if(is_array($data['product_shorturl']))
            {
            	$data['product_shorturl'] = $data['product_shorturl'][$CMS->vars['default_language']];
        	}

        	if(is_array($data['product_description']))
            {
            	$data['product_description'] = $data['product_description'][$CMS->vars['default_language']];
        	}

        	if(is_array($data['product_information_1']))
            {
            	$data['product_information_1'] = $data['product_information_1'][$CMS->vars['default_language']];
        	}

        	if(is_array($data['product_information_2']))
            {
            	$data['product_information_2'] = $data['product_information_2'][$CMS->vars['default_language']];
        	}

            if(is_array($data['product_name']))
            {
                //Neu la dang mang thi chuyen ve dang chuoi binh thuong
                $data['product_name'] = $data['product_name'][$CMS->vars['default_language']];

                // Check permission to read Info
				if ( $CMS->permit["product_read"] == true )
				{
					$site = $data['product_type'] == 0 ? 'product' : 'service';
					$data['product_name_bk'] = "<a class='link' href='{$CMS->vars['root_domain']}/?site={$site}&act=show&id={$data['product_id']}'>{$data['product_name']}</a>";
				}

            }else
            {
            	$data['product_name_bk'] = $data['product_name'];
            	if ( $CMS->permit["product_read"] == true )
				{
					$site = $data['product_type'] == 0 ? 'product' : 'service';
					$data['product_name_bk'] = "<a class='link' href='{$CMS->vars['root_domain']}/?site={$site}&act=show&id={$data['product_id']}'>{$data['product_name']}</a>";
				}
            }


        }
        

		switch ($data['product_status']) {
			case 1:
				$data['product_statup_c'] = $CMS->lang['p_statup_01'];
				break;
			case 2:
				$data['product_statup_c'] = $CMS->lang['p_statup_02'];
				break;
			default:
				$data['product_statup_c'] = $CMS->lang['p_statup_00'];
				break;
		}
		switch ($data['product_type']) {
			case 1:
				$data['product_type_c'] = '<i class="fa fa-recycle"></i>';
				break;
			default:
				$data['product_type_c'] = '<i class="fa fa-gift"></i>';
				break;
		}
		switch ($data['product_cycle']) {
			case 1:
				$data['product_cycle_c'] = $CMS->lang['p_cycle_01'];
				break;
			case 1:
				$data['product_cycle_c'] = $CMS->lang['p_cycle_02'];
				break;
			default:
				$data['product_cycle_c'] = $CMS->lang['p_cycle_00'];
				break;
		}
		$data['product_show_c'] = $CMS->lang["p_show_{$data['product_show']}"];
		if($data['product_show'] == 1)
		{
			//Hien
			$data['product_show_bk'] = '<i class="fa fa-eye"></i>';
		}
		else
		{
			$data['product_show_bk'] = '<i class="fa fa-eye-slash"></i>';
		}

		$product_option = explode(',', $data['product_option']);
		$product_option = array_unique($product_option);
		$data['product_option_c'] = '';
        for ( $i = 1; $i <= 5; $i++ ) 
        {
            if ( in_array($i, $product_option) ) 
            {
            	$data['product_option_c'] .= $data['product_option_c'] ? ', ' : '';
            	$data['product_option_c'] .= $CMS->lang["product_option_{$i}"];
            }
        }
        $data['product_option_c'] = $data['product_option_c'] ? $data['product_option_c'] : ''; 

		$data['product_show_instock_c'] = $CMS->lang["p_show_{$data['product_show_instock']}"];
		$data['product_stock_available_c'] = $CMS->lang["p_stock_available_{$data['product_stock_available']}"];

		//Load staff
		if($data['staff_id'] != "" AND $data['product_type'] == 1)
		{
			$staff_id = json_decode($data['staff_id'],true);

			if(is_array($staff_id) AND count($staff_id) > 0)
			{
				// convert list staff
				foreach ($staff_id as $key => $value) {
					$user_s  = $CMS->user->get_info($value,"user_display_name");
					$data['staff_id_bk'] .= $user_s.", ";
				}
			}
		}
 
		// Search sản pham theo tai san
		if($CMS->permit['assets_read'] == true)
		{

			$data['search_p_name'] = "<a href='{$CMS->vars['root_domain']}/?site=assets&product_id={$data['product_id']}'><i class='fa fa-search q-search' aria-hidden='true'></i></a>";
			$data['search_pg_name'] = "<a href='{$CMS->vars['root_domain']}/?site=assets&pgroup_id={$data['product_group']}'><i class='fa fa-search q-search' aria-hidden='true'></i></a>";
			$data['search_ncc_name'] = "<a href='{$CMS->vars['root_domain']}/?site=assets&supplier_id={$data['sup_id']}'><i class='fa fa-search q-search' aria-hidden='true'></i></a>";
			$data['search_user_name'] = "<a href='{$CMS->vars['root_domain']}/?site=assets&user_id={$data['user_id']}'><i class='fa fa-search q-search' aria-hidden='true'></i></a>";

		} 
		$data['product_supplier_c'] = $CMS->supplier->get_info($data['sup_id'], 'supplier_name');
		$data['product_supplier_c'] = $data['product_supplier_c'] ? $data['product_supplier_c'] : '...';

		if($CMS->permit['supplier_read'] == true)
		{

			$data['supplier_name_bk'] = "<a href='{$CMS->vars['root_domain']}/?site=supplier&act=show&id={$data['sup_id']}''>{$data['product_supplier_c']}</a>";
		}
		else
		{
			$data['supplier_name_bk'] = $data['product_supplier_c'];
		}


		$data['product_group_c'] = $CMS->product_group->getInfo($data['product_group'], 'product_group_name');

		if($CMS->permit['product_group_read'] == true)
		{

			$data['product_group_bk'] = "<a href='{$CMS->vars['root_domain']}/?site=product_group&act=show&id={$data['product_group']}' title='{$data['product_group_c']}'>{$data['product_group_c']}</a>";
 
		}
		else
		{
			$data['product_group_bk'] = $data['product_group_c'];
		}

		$data['user_name'] = $CMS->user->get_info($data['user_id'],"user_name");


		if($CMS->permit['user_read'] == true)
		{

			$data['user_name_bk'] = "<a href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}''>{$data['user_name']}</a>";
		}
		else
		{
			$data['user_name_bk'] = $data['product_group_c'];
		}

	 	
		if($CMS->vars['translations'])
        {
			$data['product_name_c'] = $CMS->class->editor->substr($data['product_name'][$CMS->vars['default_language']],0,30);
		}else
		{
			$data['product_name_c'] = $CMS->class->editor->substr($data['product_name'],0,30);
		}
		$data['product_time_c'] = $CMS->class->date->date_format($data['product_time'],1);
	 
		$data['product_manufacture_c'] = $CMS->manufacture->getInfo($data['product_manufacture'], 'manufacture_name');
		$data['product_manufacture_c'] = $data['product_manufacture_c'] ? $data['product_manufacture_c'] : '...';


		
		$data['product_price_c'] =  $CMS->class->input->currency($data['product_price']);
		$data['product_price_original_c'] = $CMS->class->input->currency($data['product_price_original']);
		$data['product_price_sell_c'] = $CMS->class->input->currency($data['product_price_sell']);
		$data['product_price_old_c'] = $CMS->class->input->currency($data['product_price_old']);
		$data['product_price_sale_c'] = $CMS->class->input->currency($data['product_price_sale']);


		$imgPath = \lib\image::getThumb("{$CMS->vars['upload_dir']}/product/{$data['product_image']}", $this->thumb_folder,'L_',1);
		$data['product_image_c'] = is_file("{$imgPath}") ? str_replace($CMS->vars['upload_dir'], $CMS->vars['upload_url'], $imgPath) : '';
		

		
		if($data['product_guarantee_default'] == "" OR $data['product_guarantee_default'] == 0  )
		{
			$data['product_guarantee_default_c']  = "...";
		}
		else
		{
			$data['product_guarantee_default_c']  = $data['product_guarantee_default']. " tháng";
		}

		if( !empty($data['product_image']) && file_exists($CMS->vars['upload_dir'].'/product/'.$data['product_image']) )
		{

			$path = $CMS->vars['upload_dir'].'/product/'.$data['product_image'];

			$type = pathinfo($path, PATHINFO_EXTENSION);

			$img_data = file_get_contents($path);
			$data['base64_string'] = 'data:image/' . $type . ';base64,' . base64_encode($img_data);
			
 
		}
	
		// Count Store_request by product_id
		$c_store_request = $this->count_store_rq_bypid($data['product_id']);
		$data['ass_inventory'] = $this->count_asset_bypid($data['product_id']);

		// Get store
		$data['store_name'] = $CMS->store->get_info($data['store_id'], 'store_name');

		$data['parent_id_c'] = $data['parent_id'] ? $data['parent_id'] : '...' ;

		// attribute
		$product_attribute_custom = json_decode($data['product_attribute_custom'], 1);
		$data['product_attribute_custom_c'] = [];
		if( is_array($product_attribute_custom) )
		{
			foreach ($product_attribute_custom as $attribute_id => $attribute_options_id) 
			{
				$attribute = \models\attribute::getInfo($attribute_id);
				$attribute_name = ! empty($attribute['attr_name']) ? $attribute['attr_name'] : '...';

				$attribute_options = \models\attribute::getInfo_options($attribute_options_id);
				$attribute_options_name = ! empty($attribute_options['options_name']) ? $attribute_options['options_name'] : '...';

				$data['product_attribute_custom_c'][$attribute_name] = $attribute_options_name;
			}
		}

		$data['product_attribute_c'] = json_decode($data['product_attribute'], 1);
		$data['product_gallery_c'] = json_decode($data['product_gallery'], 1);
		$data['product_tra_type_c'] = $CMS->lang["tra_type_{$data['product_tra_type']}"];
		$data['product_real_type_c'] = $CMS->lang["real_type_{$data['product_real_type']}"];

		if( isset($data['product_attribute_c']['tra_time_start']) )
		{
			$data['product_attribute_c']['tra_time_start_c'] = $CMS->class->date->date_format($data['product_attribute_c']['tra_time_start']);
		}

		if( isset($data['product_attribute_c']['tra_time_end']) )
		{
			$data['product_attribute_c']['tra_time_end_c'] = $CMS->class->date->date_format($data['product_attribute_c']['tra_time_end']);
		}

		// Variants
		$data['var_content_c'] = \lib\input::jsonDecode($data['var_content']);

		$data['record_cnt'] = $this->record_cnt;

		$this->record_cnt++;
		return $data;
	}

	public function getInfo($record_id = null,$field_name = '*', $check_deleted=1) {
            if (!empty($record_id)) {
                global $CMS, $DB, $member;

                $clause = "";
                // Use for order get infor product all Tanlv 29/09/2017
                if($check_deleted == 1) { $clause .= " AND `product_deleted`=0 ";}

                $sql = "SELECT {$field_name} FROM `".root_table."product` WHERE (`product_id`='{$record_id}' OR product_shorturl = '{$record_id}' OR product_name = '{$record_id}') {$clause} LIMIT 1";

                $results = $DB->fetch_data($sql, $this->cache_prefix);

                $data = isset($results[0]) ? $results[0] : null;

                if ($field_name !== '*') {
                    $data = $data[$field_name];
                }

                return $data;
            }
		return false;
	}

	public function checkName($name = null, $p_id = null, $product_group=0, $id_return=0) {
		if (!empty($name)) {
			global $CMS, $DB, $member;
			$clause = "";
			if($product_group)
			{
				$clause .= " AND product_group='{$product_group}' ";
			}

			$sql = "SELECT product_id FROM `".root_table."product` WHERE (`product_name`='{$name}' OR product_name_lang LIKE '%:\"{$name}\",%') AND `product_id` != '{$p_id}' AND `product_deleted`=0 {$clause} LIMIT 1";

			$data = $DB->fetch_data($sql, $this->cache_prefix)[0];

			if ($data) {
				if($id_return == 1)
				{
					return $data['product_id'];
				}
				return true;
			}
		}
		return false;
	}

	public function checkCode($code = null, $p_id = null) {
		if (!empty($code)) {
			global $CMS, $DB, $member;
			$DB->query("SELECT 0 FROM `".root_table."product` WHERE `product_code`='{$code}' AND `product_id` != '{$p_id}' AND `product_deleted`=0 LIMIT 1");
			if ($DB->num_rows() > 0) {
				return true;
			}
		}
		return false;
	}

	public function getParent($id_childen=null) {
		global $CMS, $DB, $member;
		$DB->query("SELECT * FROM `".root_table."product_category` WHERE  `product_category_deleted` = 0 AND `product_category_parent` = 0 AND `product_category_id` != '{$id_childen}' ORDER BY `product_category_id` DESC");
		$arr = array();
		if ($DB->num_rows() > 0) {
			while ($result = $DB->fetch_array()) {
				array_push($arr, $result);
			}
		}
		return $arr;
	}

	public function add($data = [], $check_valid=1)
	{
		global $CMS, $DB, $member;

		if($data)
        {
            $CMS->input = array_merge($CMS->input, $data);
        }

		// print "<pre>";
		// print_r($CMS->input);exit;
		$attr_arr = $this->validate_field($CMS->input);
		$product_attribute = json_encode(array_filter($attr_arr), JSON_UNESCAPED_UNICODE);
		$product_attribute_custom = json_encode(array_filter($CMS->input['attribute']), JSON_UNESCAPED_UNICODE);

		$p_name = $CMS->input['p_name'];
		$p_description = $CMS->input["p_description"];
		$p_information_1 = $CMS->input["p_information_1"];
		$p_information_2 = $CMS->input["p_information_2"];
		$parent_id = isset($CMS->input['parent_id']) ? intval($CMS->input['parent_id']) : 0;

		$store_id = intval($CMS->input["store_id"]);

		// Key sản phẩm dùng để xác định sản phẩm cùng loại, nhóm ...
        if($parent_id)
		{
			$product_series = $this->getInfo($parent_id, "product_series");
		}else
		{
			// Tạo key mới
			$product_series = md5(md5($p_name.time())."Ezy");
		}


		$check = true;
		if($CMS->vars['translations'])
        {
        	$i = 1;
        	foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
            	$product_shorturl_lang[$langCode] = $CMS->class->seo->cleanurl($p_name[$langCode]);

            	$p_name_lang[$langCode] = preg_replace( "/\r|\n/", "", $p_name[$langCode]);
            	$p_name_lang[$langCode] = str_replace("'", "&#39;", $p_name_lang[$langCode]);

            	$p_description_lang[$langCode] = preg_replace( "/\r|\n/", "", $p_description[$langCode]);
            	$p_description_lang[$langCode] = str_replace("'", "&#39;", $p_description_lang[$langCode]);

            	$p_information_1_lang[$langCode] = preg_replace( "/\r|\n/", "", $p_information_1[$langCode]);
            	$p_information_1_lang[$langCode] = str_replace("'", "&#39;", $p_information_1_lang[$langCode]);

            	$p_information_2_lang[$langCode] = preg_replace( "/\r|\n/", "", $p_information_2[$langCode]);
            	$p_information_2_lang[$langCode] = str_replace("'", "&#39;", $p_information_2_lang[$langCode]);

            	if($i==count($CMS->vars['translations']))
				{
					$lang = $CMS->vars['default_language'];
					$p_name = $p_name[$lang];
					$product_shorturl = $product_shorturl_lang[$lang];
					$p_description = $p_description_lang[$lang];
					$p_information_1 = $p_information_1_lang[$lang];
					$p_information_2 = $p_information_2_lang[$lang];
				}

				$i++;
            }

            if (empty($p_name_lang[$CMS->vars['default_language']])) {
                $_SESSION['error_msg'] .= $CMS->lang['p_name_err'].' ('.$CMS->vars['translations'][$CMS->vars['default_language']].')<br>';
                $check = false;
            }
            
            $p_name_lang = @json_encode($p_name_lang, JSON_UNESCAPED_UNICODE);
            $product_shorturl_lang = @json_encode($product_shorturl_lang, JSON_UNESCAPED_UNICODE);
            $p_description_lang = @json_encode($p_description_lang, JSON_UNESCAPED_UNICODE);
            $p_information_1_lang = @json_encode($p_information_1_lang, JSON_UNESCAPED_UNICODE);
            $p_information_2_lang = @json_encode($p_information_2_lang, JSON_UNESCAPED_UNICODE);
        }
        else
        {
            $p_name = $CMS->input['p_name'];
			$product_shorturl = $CMS->class->seo->cleanurl($p_name);
			$p_description = $CMS->class->editor->input("p_description");
			$p_information_1 = $CMS->class->editor->input("p_information_1");
			$p_information_2 = $CMS->class->editor->input("p_information_2");

			$name_alert = $p_name;

			$p_name_lang = "";
			$product_shorturl_lang = "";
			$p_description_lang = "";
			$p_information_1_lang = "";
			$p_information_2_lang = "";
        }
		
		$p_sku = $CMS->input['p_sku'];
 		$p_barcode = $CMS->input['p_barcode'];
		$p_status = intval($CMS->input['p_status']);
		$p_show = intval($CMS->input['p_show']);
		$p_stock_available = intval($CMS->input['p_stock_available']);
		$p_first_remain = intval($CMS->input['p_first_remain']);
		$p_show_instock = intval($CMS->input['p_show_instock']);

		$p_manufacture = $CMS->input['p_manufacture']*1;
		$p_product_group = $CMS->input['p_product_group'];
		$p_product_option_input =  $CMS->input['p_product_option'] ;
 
		$p_type = intval($CMS->input['p_type']);
		$p_cycle = $CMS->input['p_cycle'];
		$product_real_type = intval($CMS->input['product_real_type']);
		$product_tra_type = intval($CMS->input['product_tra_type']);

		$base64_string = $CMS->input['base64_image'];
		$p_image = $_FILES['p_image'];
		$p_tax = $CMS->input['p_tax'];
	 
	 	$base64_image = $CMS->input['base64_image'];
		$p_price = $CMS->input['p_price'];
			
		$add_product_option = intval($CMS->input['add_product_option']);

		$p_supplier = intval($CMS->input['p_supplier']);
		$p_guarantee = intval($CMS->input['p_guarantee']);

		// Meta seo
		$meta_title = isset($CMS->input['meta_title']) ? $CMS->input['meta_title'] : "";
		$meta_keywords = isset($CMS->input['meta_keywords']) ? $CMS->input['meta_keywords'] : "";
		$meta_description = isset($CMS->input['meta_description']) ? $CMS->input['meta_description'] : "";

		$p_price_original = $CMS->input['p_price_original'];
		$p_price_sell = $CMS->input['p_price_sell'];
		$p_price_sale = $CMS->input['p_price_sale'];
		$p_price_old = $CMS->input['p_price_old'];
		$staff_id = json_encode(array_values(input::get('staff_id', [])));

        $p_img_alt =  $CMS->input['p_img_alt'];
        // Sắp xếp, giá trở lên dùng cho service
        $p_order = intval($CMS->input['p_order']);
        $p_up = $CMS->input['p_up'];
        $user_id = $member['user_id'];

		if($p_product_option_input != "")
		{
			foreach ($p_product_option_input as $key => $value) {
				 if($value != "")
				 {
				 	$p_product_option .= ",".$value.",";
				 }	
			}
		}	
	 
		// $check=true;
		// if (empty($p_name)) {
		// 	$check=false;
		// 	$_SESSION['error_msg'] .= $CMS->lang['p_name_err'].'<br>';
		// }
		
		if (empty($p_product_group)) {
			$check=false;
			$_SESSION['error_msg'] .= $CMS->lang['p_product_group_err'].'<br>';
		}
		 
		if (!empty($p_image['tmp_name'])) {
			if ($p_image['size'] > 3*1024*1024 || ! in_array(exif_imagetype($p_image['tmp_name']), array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF))) {
				$check = false;
				$_SESSION['error_msg'] .= $CMS->lang['p_image_err'].'<br>';
			}
		}

		if (!empty($p_status) && ! in_array($p_status, array(0,1,2))) {
			$p_status = 0;
		}
		if (!empty($p_type) && ! in_array($p_type, array(0,1))) {
			$p_type = 0;
		}
		if ($p_type != 0) {
			if (!empty($p_cycle) && ! in_array($p_cycle, array(1,2))) {
				$p_type = 1;
			}
		} else {
			$p_cycle = 0;
		}

		if (empty($p_tax) || !  is_numeric($p_tax)) {
			$p_tax = 0;
		} else {
			$p_tax = $p_tax < 100 ? $p_tax : 99;
		}
		if (empty($p_price) || !  is_numeric($p_price)) {
			$p_price = 0;
		}


		if($check_valid)
        {
            if ($check) {

                if (empty($p_product_group) || ! $CMS->product_group ->getInfo($p_product_group , 'product_group_id')) {
                    $p_product_group = 0;
                }

                if ($CMS->product->checkName($p_name,null,$p_product_group)) {
                    $check=false;
                    $_SESSION['error_msg'] .= $CMS->lang['p_name_exist'].'<br>';
                    return false;
                }
            }
            else
            {
                return false;
            }
        }



		if (empty($p_guarantee) || !  is_numeric($p_guarantee) || $p_guarantee < 0) {
				$p_guarantee = 0;
		} 

		if($p_guarantee  > 999)
		{
			$_SESSION['error_msg'] .= "The maximum warranty period is 999";
			return false;
		}
		if($p_guarantee  < 0 )
		{
			$_SESSION['error_msg'] .= "The minimum warranty period is 0";
			return false;
		}

		if (empty($p_price_sell) || !  is_numeric($p_price_sell)) {
				$p_price_sell = 0;
		}

		if( !is_numeric($p_price_sale) ) 
		{
			$p_price_sale = 0;
		}

		if (empty($p_price_old) || !  is_numeric($p_price_old)) {
				$p_price_old = 0;
		}
		if (empty($p_price_original) || !  is_numeric($p_price_original)) {
				$p_price_original = 0;
		}

		$product_subitem = "";
		// $arr_product_name = array_values($CMS->input['sub_product_name']);
		// $arr_product_id = array_values($CMS->input['sub_product_id']);
		// $arr_product_sku = array_values($CMS->input['sub_product_sku']);
		// $arr_product_description = array_values($CMS->input['sub_product_description']);
		// $arr_product_quantity = array_values($CMS->input['sub_product_quantity']);
		// $arr_product_price = array_values($CMS->input['sub_product_price']);
		// $arr_product_tax = array_values($CMS->input['sub_product_tax']);
		// $arr_sub_color = array_values($CMS->input['sub_color']);

		// $count = count($arr_product_name);
	 // 	$form_type = intval($CMS->input['type_form']);
	 // 	if($form_type == 0 or $form_type == 1)
	 // 	{
		// 	for($i=0; $i<$count; $i++)
		// 	{
		// 		if($arr_product_name[$i])
		// 		{
		// 			$product_subitem[$i]['product_name'] = $CMS->class->editor->input($arr_product_name[$i], "text");
				 
		// 			$product_subitem[$i]['product_id'] = intval($arr_product_id[$i]);
		// 			$product_subitem[$i]['product_description'] = $CMS->class->editor->input($arr_product_description[$i],"text");
		// 			$product_subitem[$i]['product_quantity'] = intval($arr_product_quantity[$i]) != 0 ? intval($arr_product_quantity[$i]) : 1;
		// 			$product_subitem[$i]['product_price'] = floatval($arr_product_price[$i]);
		// 			$product_subitem[$i]['product_tax'] = floatval($arr_product_tax[$i]);
		// 			$product_subitem[$i]['product_sku'] =  $arr_product_sku[$i] ;

		// 			$product_subitem[$i]['product_tax'] = ( $product_subitem[$i]['product_tax'] != 0 AND $product_subitem[$i]['product_tax'] !=  10) ? 0  : $arr_product_tax[$i];
		// 			$product_subitem[$i]['product_color'] =  $arr_sub_color[$i] ;
		
		 
		// 		}
			 
		// 	}
	 // 	}
  //  		//Get info product group
  //  		$product_group_code = $CMS->product_group->getInfo($p_product_group,'product_group_code');
  //  		$product_subitem_bk = $product_subitem;
		// $product_subitem =  json_encode($product_subitem, JSON_UNESCAPED_UNICODE);


        //Upload gallery
        $galleryData = [];
        if(count($_FILES['p_gallery']['name']))
        {
            $gallery_folder_root = "product/gallery/";
            $gallery_folder = $CMS->class->image->check_folder_img($gallery_folder_root,"",1,"thumbnail");

            //Check valid
            foreach ($_FILES['p_gallery']['name'] as $fileIndex => $fileName)
            {
                if(!$_FILES['p_gallery']['tmp_name'][$fileIndex]) continue;
                if(!$CMS->class->attachment->check_is_image($_FILES['p_gallery']['tmp_name'][$fileIndex]))
                {
                    $check=false;
                    $_SESSION['error_msg'] .= "File {$fileName} is not a image <br />";
                }
            }

            if($check)
            {
                //Upload
                foreach ($_FILES['p_gallery']['name'] as $fileIndex => $fileName)
                {
                    if(!$_FILES['p_gallery']['tmp_name'][$fileIndex]) continue;
                    $fileExt = $CMS->class->attachment->get_ext($fileName);
                    $newFileName = $CMS->class->seo->cleanurl($p_name).'-'.time().'-'.rand(0,9999).".{$fileExt}";

                    $uploadPath = "{$gallery_folder_root}{$gallery_folder}/{$newFileName}";

                    if(copy($_FILES['p_gallery']['tmp_name'][$fileIndex], "{$CMS->vars['upload_dir']}/{$uploadPath}"))
                    {
                        $galleryData[] = $uploadPath;
                    }
                }
            }
        }

        $galleryData = @json_encode($galleryData, JSON_UNESCAPED_UNICODE);

 		if ($check) {

			$dir = $CMS->vars['upload_dir'].'/product';
			if (!empty($p_image['tmp_name'])) {
			 
				$dir = $CMS->vars['upload_dir'].'/product';
				$data_info['product_image'] = !empty($data_info['product_image']) ? $data_info['product_image'] :  $CMS->class->random->randomString(5,'abcdefghijklmnopqrstuvwxyz')."_".time().'.jpg';
				
				//Check is_dir
				$CMS->class->image->is_dir($dir);
				$imgPath = $dir.'/'.$data_info['product_image'];

				move_uploaded_file($p_image['tmp_name'], $imgPath);

				$CMS->class->image->quality = 1;

                //Create thumb
                /*foreach ($this->thumb_size as $keySize => $valSize)
                {
                    $CMS->class->image->resize($imgPath, \lib\image::getThumb($imgPath, $this->thumb_folder, "{$keySize}_"), $valSize);
                }*/
			}
			else
			{
				if( !empty($base64_image))
				{
					// Tai hinh tu basse 64
					$data_info['product_image']  = $CMS->class->image->uploadImgBase64($base64_image, 'product', 'thumbnail', 220); 	 
				}
			}
			
		}

		// Check lưu tùy chọn
		if($add_product_option == 1)
		{
			$temp['product_type'] = $p_type;
			$temp['product_group'] = $p_product_group;
			$temp['product_manufacture'] = $p_manufacture;
			$temp['product_supplier'] = $p_supplier;
			$user_staft_product = json_encode($temp, JSON_UNESCAPED_UNICODE);
			$DB->query("UPDATE `".root_table."user` SET `user_draft_product`='{$user_staft_product}'  WHERE `user_id`='{$user_id}' ");
 
		}
		else // Xóa lưu tùy chọn
		{
			$DB->query("UPDATE `".root_table."user` SET `user_draft_product`=''  WHERE `user_id`='{$user_id}' ");

			//Sản phẩm đã được tạo, các tùy chọn trước đó sẽ được giữ lại
		}

		//Commission
        $product_commission_type = $CMS->vars['enabled_commission'] ? $CMS->input['product_commission_type']*1 : 0;
        $product_commission_value = $CMS->vars['enabled_commission'] ? $CMS->input['product_commission_value']*1 : 0;

        $product_estimated_time = input::get('p_estimated_time') * 1;

        $CMS->class->cache->mdelete("user");

		$DB->query("INSERT INTO `".root_table."product` (`product_time`, `user_id`, `product_name`, `product_sku`, `product_barcode`,  `product_status`,   `product_show`, `product_stock_available`, `product_manufacture`, `product_group`, `product_option`,  `product_type`, `product_cycle`, `product_tax`, `product_price`, `product_price_original`, `product_price_sell`, `product_subitem`, `sup_id`, product_description, product_image, product_guarantee_default, product_shorturl,staff_id, product_image_alt, product_order, product_up, product_price_old, product_information_1, product_information_2, product_gallery, product_name_lang, product_shorturl_lang, product_description_lang, product_information_1_lang, product_information_2_lang, product_attribute, product_real_type, product_tra_type, product_commission_type, product_commission_value, meta_title
			, meta_keywords, meta_description, parent_id, product_attribute_custom, product_series, store_id, product_estimated_time, product_price_sale) VALUES ('".time()."','{$user_id}', '{$p_name}', '{$p_sku}', '{$p_barcode}', '{$p_status}', '{$p_show}', '{$p_stock_available}', '{$p_manufacture}', '{$p_product_group}', '{$p_product_option}' ,'{$p_type}', '{$p_cycle}', '{$p_tax}', '{$p_price}', '{$p_price_original}', '{$p_price_sell}',  '{$product_subitem}', '{$p_supplier}', '{$p_description}', '{$data_info['product_image']}', '{$p_guarantee}', '{$product_shorturl}', '{$staff_id}', '{$p_img_alt}', '{$p_order}', '{$p_up}', '{$p_price_old}', '{$p_information_1}', '{$p_information_2}', '{$galleryData}', '{$p_name_lang}', '{$product_shorturl_lang}', '{$p_description_lang}', '{$p_information_1_lang}', '{$p_information_2_lang}', '{$product_attribute}', '{$product_real_type}', '{$product_tra_type}', '{$product_commission_type}', '{$product_commission_value}', '{$meta_title}'
			, '{$meta_keywords}', '{$meta_description}', '{$parent_id}', '{$product_attribute_custom}', '{$product_series}', '{$store_id}', '{$product_estimated_time}', '{$p_price_sale}')");
			$id = $DB->last_insert_id();

			$product_code = $product_group_code ."". $CMS->class->input->generate_code($id, 6 - strlen(utf8_decode($product_group_code)) );
			// Update product code
			$DB->query("UPDATE `".root_table."product` SET `product_code`='{$product_code}' WHERE  `product_id`='{$id}'");

			// Insert moi doi voi sản phẩm con lấy theo thông tin sản phẩm cha
			if($id)
			{
				$child_parent_id = $parent_id ? $parent_id : $id;
				$cp_attr_id = array_values($CMS->input['cp_attr_id']);
				$cp_option_id = array_values($CMS->input['cp_option_id']);
				$cp_name_ext = array_values($CMS->input['cp_name_ext']);
				$cp_code_ext = array_values($CMS->input['cp_code_ext']);
				$cp_quantity = array_values($CMS->input['cp_quantity']);


				$count_child = count($cp_name_ext);
				$p_name_bk = $p_name;
				$p_barcode_bk = $p_barcode;
				$p_sku_bk = $p_sku;
				$p_name_lang_bk = $p_name_lang; 
				$product_shorturl_lang_bk = $product_shorturl_lang;
				for($i=0; $i<$count_child; $i++)
				{
					if($cp_name_ext[$i])
					{
						// p_name_lang
						$p_name = $p_name_bk." ".$cp_name_ext[$i];
						$p_name = $CMS->class->editor->input($p_name, "text");
						$product_shorturl = $CMS->class->seo->cleanurl($p_name);

						if($check_valid)
				        {
			                if ($CMS->product->checkName($p_name,null,$p_product_group)) 
			                {
			                    $_SESSION['msg'] .= $CMS->lang['p_name_exist']." [{$p_name}] <br>";
			                    continue;
			                }
				        }

						if($CMS->vars['translations'])
				        {
				        	$p_name_lang = json_decode($p_name_lang_bk, 1);
				        	$product_shorturl_lang = json_decode($product_shorturl_lang_bk, 1);
				        	foreach ($CMS->vars['translations'] as $langCode => $langName)
				            {
				            	$p_name_lang[$langCode] = $p_name_lang[$langCode] ." ".$cp_name_ext[$i];
				            	$product_shorturl_lang[$langCode] = $product_shorturl;
				            }

				            $p_name_lang = @json_encode($p_name_lang, JSON_UNESCAPED_UNICODE);
				            $product_shorturl_lang = @json_encode($product_shorturl_lang, JSON_UNESCAPED_UNICODE);
				        }
				        


						$p_barcode = $p_barcode_bk.$cp_code_ext[$i];
						$product_subitem = [];
						$p_sku = $p_sku_bk.$cp_code_ext[$i];

						$attr_arr = array();
						$list_attr = explode(",", $cp_attr_id[$i]);
						$list_option = explode(",", $cp_option_id[$i]);
						for($j=0;$j<count($list_attr); $j++)
						{
							$attr_arr[$list_attr[$j]] = "{$list_option[$j]}";
						}
						$product_attribute_custom = json_decode($product_attribute_custom, JSON_UNESCAPED_UNICODE);
						// Array $attr_arr đè lên $product_attribute
						$attr_arr = array_replace($product_attribute_custom, $attr_arr);
						$product_attribute_custom = json_encode($attr_arr, JSON_UNESCAPED_UNICODE);
						$DB->query("INSERT INTO `".root_table."product` (`product_time`, `user_id`, `product_name`, `product_sku`, `product_barcode`,  `product_status`,   `product_show`, `product_stock_available`, `product_manufacture`, `product_group`, `product_option`,  `product_type`, `product_cycle`, `product_tax`, `product_price`, `product_price_original`, `product_price_sell`, `sup_id`, product_description, product_image, product_guarantee_default, product_shorturl,staff_id, product_image_alt, product_order, product_up, product_price_old, product_information_1, product_information_2, product_gallery, product_name_lang, product_shorturl_lang, product_description_lang, product_information_1_lang, product_information_2_lang, product_attribute, product_real_type, product_commission_value, meta_title, meta_keywords, meta_description, parent_id, product_attribute_custom, product_series) VALUES ('".time()."','{$user_id}', '{$p_name}', '{$p_sku}', '{$p_barcode}', '{$p_status}', '{$p_show}', '{$p_stock_available}', '{$p_manufacture}', '{$p_product_group}', '{$p_product_option}' ,'{$p_type}', '{$p_cycle}', '{$p_tax}', '{$p_price}', '{$p_price_original}', '{$p_price_sell}', '{$p_supplier}', '{$p_description}', '{$data_info['product_image']}', '{$p_guarantee}', '{$product_shorturl}', '{$staff_id}', '{$p_img_alt}', '{$p_order}', '{$p_up}', '{$p_price_old}', '{$p_information_1}', '{$p_information_2}', '{$galleryData}', '{$p_name_lang}', '{$product_shorturl_lang}', '{$p_description_lang}', '{$p_information_1_lang}', '{$p_information_2_lang}', '{$product_attribute}', '{$product_real_type}', '{$product_commission_value}', '{$meta_title}', '{$meta_keywords}', '{$meta_description}', '{$child_parent_id}', '{$product_attribute_custom}', '{$product_series}')");

						$sid = $DB->last_insert_id();
						$product_code = $product_group_code ."". $CMS->class->input->generate_code($sid, 6 - strlen(utf8_decode($product_group_code)) );

						$DB->query("UPDATE `".root_table."product` SET `product_code`='{$product_code}' WHERE  `product_id`='{$sid}'");
			
					}
				 
				}
			}


			// Insert variant
			if($id)
			{
				// Insert Attribute
				$arr['option1'] = input::get("option1");
				$arr['option2'] = input::get("option2");
				$arr['option3'] = input::get("option3");
				foreach ($arr as $key => $value) {
					if($value)
					{
						if(attribute::checkInput($value, "attr_name"))
						{
							continue;
						}
						$attr_key = str_replace("-","", $CMS->class->seo->cleanurl($value));
						$attr_time = time();
						
						// Insert
						$DB->query("INSERT INTO ".root_table."attribute (attr_name, attr_key, attr_type, attr_time) VALUES ('{$value}', '{$attr_key}', '{$attr_type}', '{$attr_time}')");
					}
				}

				// Insert variants
				$var_name = array_values(input::get("variant_name"));
				$var_sku = array_values(input::get("variant_sku"));
				$var_basecost = array_values(input::get("variant_basecost"));
				$var_option1 = array_values(input::get("variant_option1"));
				$var_option2 = array_values(input::get("variant_option2"));
				$var_option3 = array_values(input::get("variant_option3"));

				if(count($var_name) > 0)
	            {
	                $str_sql = "";
	                $var_time = time();
	                for($x=0; $x<count($var_name); $x++)
	                {
	                    $str_sql .= "('{$var_name[$x]}', '{$var_sku[$x]}', '{$var_basecost[$x]}', '{$var_option1[$x]}', '{$var_option2[$x]}', '{$var_option3[$x]}', '{$id}' ,'{$var_time}', '{$user_id}'),";
	                }

	                // Xữ lý chuỗi thừa
	                $str_sql = rtrim($str_sql, ",");
	                $str_field = "INSERT INTO ".root_table."variants (var_title, var_sku, var_price, var_option1, var_option2, var_option3, product_id, var_time, user_id) VALUES ";

	                $str_sql = $str_sql ? $str_field.$str_sql : "";
	                
	                // Check insert database
	                if( $str_sql )
	                {
	                	$DB->query($str_sql);
	                	
	                	//Clear all cache
            			$CMS->class->cache->mdelete('variants');
	                }

	                // save variants to product
	                if( !$this->generalVariants($id) )
	                {
	                	$_SESSION['msg'] .= "<p style='color: red;'>{$CMS->lang['p_var_content_not_saved']}</p>";
	                }
	            }

			}

			//Sync WHM data
       		if($CMS->vars['web_free']== 1 AND intval($CMS->vars['ibe_synced']) == 1)
    		{
	    		$product_image_link = !empty($data_info['product_image']) && file_exists($CMS->vars['upload_dir'].'/product/'.$data_info['product_image']) ? $CMS->vars['upload_url'].'/product/'.$data_info['product_image'] : "";

	  
	    		$data_api['product_name']   			= "{$p_name}";
	    		$data_api['product_shorturl']   		= "{$product_shorturl}";
	    		$data_api['product_show']   		    = "{$p_show}";
	    		$data_api['product_image_alt']   		= "{$p_img_alt}";
	    		$data_api['product_group'] 				= "{$p_product_group}";
	    		$data_api['product_price_old'] 			= "{$p_price_old}";
	    		$data_api['product_price_original'] 	= "{$p_price_original}";
	    		$data_api['product_price_sell'] 		= "{$p_price_sell}";
	    		$data_api['product_up']   				= "{$p_up}";
	    		$data_api['product_description']   		= "{$p_description}";
	    		$data_api['product_image']   		    = "{$data_info['product_image']}";
	    		$data_api['product_image_link']   		= "{$product_image_link}";
	    		$data_api['product_time']   		  	= time();
	    		$data_api['original_id']   		  		= "{$id}";
	    		$data_api['site_id']   		  		    = "{$CMS->vars['site_id']}";
 
	    		$CMS->api->whm->execute('service_add', $data_api); 
    	 	}


			if($CMS->vars['addon_goods_enable'] == 1)//Enable Store
			{

				$DB->query("UPDATE `".root_table."product` SET `product_code`='{$product_code}', product_first_remain = '{$p_first_remain}', product_show_instock = '{$p_show_instock}' WHERE  `product_id`='{$id}'");
				// Add Assets
				$data_asset = array();
				if($p_first_remain > 0 AND $p_type == 0 AND $CMS->input['store_id'] > 0 )
				{ 
					$data_asset[0]['product_id'] = $id;
					$data_asset[0]['product_name'] = $p_name;
					$data_asset[0]['product_description'] = $p_description;
					$data_asset[0]['product_code'] = $product_code;
					$data_asset[0]['product_sku'] = $p_sku;
					$data_asset[0]['product_group'] = $p_product_group;
					$data_asset[0]['product_price'] = $p_price;
					$data_asset[0]['product_price_original'] = $p_price_original;
					$data_asset[0]['product_price_sell'] = $p_price_sell;
					$data_asset[0]['product_quantity'] = $p_first_remain;
					$data_asset[0]['product_amount'] = $p_price + ($p_price * ($p_tax/100));
					$data_asset[0]['product_tax'] = $p_tax;
					$data_asset[0]['product_type'] = 0;
					$data_asset[0]['product_cycle'] = $p_cycle;
					$data_asset[0]['product_status'] = $p_status;
					$data_asset[0]['store_id'] = intval($CMS->input['store_id']);
					$data_rq['request_product'] = json_encode($data_asset);
					$data_rq['store_id'] =  intval($CMS->input['store_id']);
					$data_rq['supplier_id'] = $p_supplier;
					$data_rq['shi_id']= "";

					// Add assets 
					$CMS->assets->addQuick($data_rq);
					

				}
			}
			
			
            //Clear all cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$product = $CMS->product->getInfo($id);

			$key = "product_{$product['product_id']}";
			$CMS->class->logs->key = $key;
			$_SESSION['msg'] .= $CMS->class->logs->insert("{$CMS->lang['add_product_success']} #{$id} </br>");
			return $product;
	}

	public function upAvartar($p_id = null, $p_avartar = null) {
		if (!empty($p_id) && !empty($p_avartar)) {
			global $CMS, $DB, $member;
			$DB->query("UPDATE `".root_table."product` SET `product_avartar`='{$p_avartar}' WHERE `product_deleted`=0 AND `product_id`='{$p_id}'");

            //Clear all cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			return true;
		}
		return false;
	}
	public function edit() {
		 
        global $CMS, $DB, $member;

		$oldData = $data_info = $CMS->product->getInfo($CMS->input['id']);
		$key = "product_{$data_info['product_id']}";
		$CMS->class->logs->key = $key;
		$CMS->class->logs->old_data = $data_info;

		$attr_arr = $this->validate_field($CMS->input);
		$product_attribute = json_encode($attr_arr, JSON_UNESCAPED_UNICODE);

		$product_attribute_custom = json_encode($CMS->input['attribute'], JSON_UNESCAPED_UNICODE);
		$parent_id = isset($CMS->input['parent_id']) ? intval($CMS->input['parent_id']) : $oldData['parent_id'];
 		
 		$p_name = $CMS->input['p_name'];
		$p_description = $CMS->input["p_description"];
		$p_information_1 = $CMS->input["p_information_1"];
		$p_information_2 = $CMS->input["p_information_2"];

		$store_id = intval($CMS->input['store_id']);

		$check = true;
		
		if($CMS->vars['translations'])
        {
        	$i=1;
        	foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
            	$product_shorturl_lang[$langCode] = $CMS->class->seo->cleanurl($p_name[$langCode]);

            	$p_name_lang[$langCode] = preg_replace( "/\r|\n/", "", $p_name[$langCode]);
            	$p_name_lang[$langCode] = str_replace("'", "&#39;", $p_name_lang[$langCode]);

            	$p_description_lang[$langCode] = preg_replace( "/\r|\n/", "", $p_description[$langCode]);
            	$p_description_lang[$langCode] = str_replace("'", "&#39;", $p_description_lang[$langCode]);

            	$p_information_1_lang[$langCode] = preg_replace( "/\r|\n/", "", $p_information_1[$langCode]);
            	$p_information_1_lang[$langCode] = str_replace("'", "&#39;", $p_information_1_lang[$langCode]);

            	$p_information_2_lang[$langCode] = preg_replace( "/\r|\n/", "", $p_information_2[$langCode]);
            	$p_information_2_lang[$langCode] = str_replace("'", "&#39;", $p_information_2_lang[$langCode]);

            	if($i==count($CMS->vars['translations']))
				{
					$lang = $CMS->vars['default_language'];
					$p_name = $p_name[$lang];
					$product_shorturl = $product_shorturl_lang[$lang];
					$p_description = $p_description_lang[$lang];
					$p_information_1 = $p_information_1_lang[$lang];
					$p_information_2 = $p_information_2_lang[$lang];
				}
				$i++;
            }

            if (empty($p_name_lang[$CMS->vars['default_language']])) {
                $_SESSION['error_msg'] .= $CMS->lang['p_name_err'].' ('.$CMS->vars['translations'][$CMS->vars['default_language']].')<br>';
                $check = false;
            }
            
            $p_name_lang = @json_encode($p_name_lang, JSON_UNESCAPED_UNICODE);
            $product_shorturl_lang = @json_encode($product_shorturl_lang, JSON_UNESCAPED_UNICODE);
            $p_description_lang = @json_encode($p_description_lang, JSON_UNESCAPED_UNICODE);
            $p_information_1_lang = @json_encode($p_information_1_lang, JSON_UNESCAPED_UNICODE);
            $p_information_2_lang = @json_encode($p_information_2_lang, JSON_UNESCAPED_UNICODE);
        }
        else
        {
            $p_name = $CMS->input['p_name'];
			$product_shorturl = $CMS->class->seo->cleanurl($p_name);
			$p_description = $CMS->class->editor->input("p_description");
			$p_information_1 = $CMS->class->editor->input("p_information_1");
			$p_information_2 = $CMS->class->editor->input("p_information_2");

			$name_alert = $p_name;

			$p_name_lang = "";
			$product_shorturl_lang = "";
			$p_description_lang = "";
			$p_information_1_lang = "";
			$p_information_2_lang = "";
        }
 
		$p_sku = $CMS->input['p_sku'];
	 	$p_barcode = $CMS->input['p_barcode'];
		$p_made_in = $CMS->input['p_made_in'];
		$p_status = intval($CMS->input['p_status']);
		$p_show = intval($CMS->input['p_show']);
		$p_stock_available = intval($CMS->input['p_stock_available']);
	 
		$p_first_remain = intval($CMS->input['p_first_remain']);
		$p_show_instock = intval($CMS->input['p_show_instock']);

		$p_image = $_FILES['p_image'];
		$base64_image = $CMS->input['base64_image'];

		$p_manufacture = intval($CMS->input['p_manufacture']);
		$p_product_group = $CMS->input['p_product_group'];
		$p_product_option_input =  $CMS->input['p_product_option'] ;
 
		$p_type = intval($CMS->input['p_type']);
		$p_cycle = $CMS->input['p_cycle'];
		$product_real_type = intval($CMS->input['product_real_type']);
		$product_tra_type = intval($CMS->input['product_tra_type']);

		$p_tax = $CMS->input['p_tax'];
		$p_supplier = intval($CMS->input['p_supplier']);
		$p_price = $CMS->input['p_price'];

		$p_supplier = intval($CMS->input['p_supplier']);
		$p_guarantee = intval($CMS->input['p_guarantee']);
		$p_price_original = $CMS->input['p_price_original'];
		$p_price_sell = $CMS->input['p_price_sell'];
		$p_price_sale = $CMS->input['p_price_sale'];
		$p_price_old = $CMS->input['p_price_old'];

		// Meta seo
		$meta_title = isset($CMS->input['meta_title']) ? $CMS->input['meta_title'] : "";
		$meta_keywords = isset($CMS->input['meta_keywords']) ? $CMS->input['meta_keywords'] : "";
		$meta_description = isset($CMS->input['meta_description']) ? $CMS->input['meta_description'] : "";


        $staff_id = json_encode(array_values(input::get('staff_id', [])));

 		$product_estimated_time = input::get('p_estimated_time') * 1;

 		$p_img_alt = $CMS->input['p_img_alt'];
 		// Sắp xếp, giá trở lên dùng cho service
        $p_order = intval($CMS->input['p_order']);
        $p_up = $CMS->input['p_up'];
		
		if($p_product_option_input != "")
		{
			foreach ($p_product_option_input as $key => $value) {
				 if($value != "")
				 {
				 	$p_product_option .= ",".$value.",";
				 }	
			}
		}	
 
		// $check=true;
		// if (empty($p_name)) {
		// 	$check=false;
		// 	$_SESSION['error_msg'] .= $CMS->lang['p_name_err'].'<br>';
		// }
		if (empty($p_product_group)) {
			$check=false;
			$_SESSION['error_msg'] .= $CMS->lang['p_product_group_err'].'<br>';
		} 
		if (!empty($p_status) && ! in_array($p_status, array(0,1,2))) {
			$p_status = 0;
		}
		if (!empty($p_type) && ! in_array($p_type, array(0,1))) {
			$p_type = 0;
		}
		if ($p_type != 0) {
			if (!empty($p_cycle) && ! in_array($p_cycle, array(1,2))) {
				$p_type = 1;
			}
		} else {
			$p_cycle = 0;
		}

		if (empty($p_tax) || ! is_numeric($p_tax)) {
			$p_tax = 0;
		} else {
			$p_tax = $p_tax < 100 ? $p_tax : 99;
		}

		if (empty($p_price) || ! is_numeric($p_price)) {
			$p_price = 0;
		}

		if (!empty($p_image['tmp_name'])) {
			if ($p_image['size'] > 3*1024*1024 || ! in_array(exif_imagetype($p_image['tmp_name']), array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF))) {
				$check = false;
				$_SESSION['error_msg'] .= $CMS->lang['p_image_err'].'<br>';
			}
		}

		if ($check) {
			
			if (empty($p_product_group) || ! $CMS->product_group ->getInfo($p_product_group, 'product_group_id')) {
				$p_product_group = 0;
			}

			if ($CMS->product->checkName($p_name, $data_info['product_id'], $p_product_group) AND $p_name) 
			{
				$check=false;
				$_SESSION['msg'] .= $CMS->lang['p_name_exist'].'<br>';
			}
		}
		else
		{
			return false;
		}

		if (empty($p_guarantee) || !  is_numeric($p_guarantee)) {
				$p_guarantee = 0;
		} 
		if($p_guarantee  > 999)
		{
			$_SESSION['error_msg'] = "Thời gian bảo hành tối đa là 999";
			return false;
		}
		if($p_guarantee  < 0 )
		{
			$_SESSION['error_msg'] = "Thời gian bảo hành tối thiểu là 0";
			return false;
		}

		if (empty($p_price_sell) || !  is_numeric($p_price_sell)) {
				$p_price_sell = 0;
		}
		
		if( !is_numeric($p_price_sale) )
		{
			$p_price_sale = 0;
		}

		if (empty($p_price_old) || !  is_numeric($p_price_old)) {
				$p_price_old = 0;
		}
		if (empty($p_price_original) || !  is_numeric($p_price_original)) {
				$p_price_original = 0;
		}

		$product_subitem = "";

		// $arr_product_name = array_values($CMS->input['sub_product_name']);
		// $arr_product_id = array_values($CMS->input['sub_product_id']);
		// $arr_product_sku = array_values($CMS->input['sub_product_sku']);

		// $arr_product_description = array_values($CMS->input['sub_product_description']);
		// $arr_product_quantity = array_values($CMS->input['sub_product_quantity']);
		// $arr_product_price = array_values($CMS->input['sub_product_price']);
		// $arr_product_amount = array_values($CMS->input['sub_product_amount']);
		// $arr_product_tax = array_values($CMS->input['sub_product_tax']);
		// $arr_sub_color = isset($CMS->input['sub_color']) ? array_values($CMS->input['sub_color']) : [];

		// $count = count($arr_product_name);
		// $data_product = array();
		// $form_type = intval($CMS->input['type_form']);
		
	 // 	if($form_type == 0 or $form_type == 1)
	 // 	{  
		// 	for($i=0; $i<$count; $i++)
		// 	{  
		// 		if($arr_product_name[$i])
		// 		{
		// 			$data_product[$i]['product_id'] = intval($arr_product_id[$i]); 
		// 			$info = $CMS->product->getInfo($arr_product_id[$i]);
		// 			$data_product[$i]['product_name'] = $CMS->class->editor->input($arr_product_name[$i], "text");
		// 			$data_product[$i]['product_sku'] =  $arr_product_sku[$i] ;

		// 			$data_product[$i]['product_description'] = $CMS->class->editor->input($arr_product_description[$i],"text");
		// 			$data_product[$i]['product_quantity'] = intval($arr_product_quantity[$i]) != 0 ?  intval($arr_product_quantity[$i]) : 1;
		// 			$data_product[$i]['product_price'] = floatval($arr_product_price[$i]);
		// 			$data_product[$i]['product_amount'] = floatval($arr_product_amount[$i]);
		// 			$data_product[$i]['product_tax'] = floatval($arr_product_tax[$i]);
		// 			$data_product[$i]['product_code'] = $info['product_code'];
		// 			$data_product[$i]['product_subitem'] = $info['product_subitem'];

		// 			$product_subitem[$i]['product_tax'] = ($product_subitem[$i]['product_tax'] != 0 AND $product_subitem[$i]['product_tax'] != 10) ? 0  : $product_subitem[$i]['product_tax'];
		// 			$product_subitem[$i]['product_color'] =  $arr_sub_color[$i] ;

 
		// 		}
		// 	}
	 // 	}
 
		// $product_subitem =  json_encode($data_product, JSON_UNESCAPED_UNICODE); 
 		$product_group_code = $CMS->product_group->getInfo($p_product_group,'product_group_code');
		$product_code = $product_group_code ."". $CMS->class->input->generate_code($data_info['product_id'], 6 - strlen(utf8_decode($product_group_code))) ;

        //Upload gallery
        $galleryData = [];
        if(count($_FILES['p_gallery']['name']))
        {
            $gallery_folder_root = "product/gallery/";
            $gallery_folder = $CMS->class->image->check_folder_img($gallery_folder_root,"",1,"thumbnail");

            //Check valid
            foreach ($_FILES['p_gallery']['name'] as $fileIndex => $fileName)
            {
                if(!$_FILES['p_gallery']['tmp_name'][$fileIndex]) continue;

                if(!$CMS->class->attachment->check_is_image($_FILES['p_gallery']['tmp_name'][$fileIndex]))
                {
                    $check=false;
                    $_SESSION['msg'] .= "File {$fileName} is not a image <br />";
                }
            }

            if($check)
            {
                //Upload
                foreach ($_FILES['p_gallery']['name'] as $fileIndex => $fileName)
                {
                    if(!$_FILES['p_gallery']['tmp_name'][$fileIndex]) continue;
                    $fileExt = $CMS->class->attachment->get_ext($fileName);
                    $newFileName = $CMS->class->seo->cleanurl($p_name).'-'.time().'-'.rand(0,9999).".{$fileExt}";

                    $uploadPath = "{$gallery_folder_root}{$gallery_folder}/{$newFileName}";

                    if(copy($_FILES['p_gallery']['tmp_name'][$fileIndex], "{$CMS->vars['upload_dir']}/{$uploadPath}"))
                    {
                        $galleryData[] = $uploadPath;
                    }
                }
            }
        }

        $galleryData = $CMS->input['old_gallery'] ? array_merge($CMS->input['old_gallery'], $galleryData) : $galleryData;

        //Check and remove old image
        $oldImageData = @json_decode($oldData['product_gallery'], true);
        foreach ($oldImageData as $removeImage)
        {
            if(!in_array($removeImage, $galleryData))
            {
                @unlink("{$CMS->vars['upload_dir']}/{$removeImage}");
            }
        }

        $galleryData = @json_encode($galleryData, JSON_UNESCAPED_UNICODE);

 		if ($check) 
 		{
 
			$dir = $CMS->vars['upload_dir'].'/product'; 
			if (!empty($p_image['tmp_name'])) 
			{
				if($data_info['product_image'] == "Array")
				{
					$data_info['product_image'] = time().'.jpg';
				}
				$dir = $CMS->vars['upload_dir'].'/product';
				$data_info['product_image'] =   $CMS->class->random->randomString(5,'abcdefghijklmnopqrstuvwxyz')."_".time().'.jpg';
				 
				if (! is_dir($dir)) {
					mkdir($dir, 0755, true);
				}
 
				$imgPath = $dir.'/'.$data_info['product_image'];

				move_uploaded_file($p_image['tmp_name'], $imgPath);

				$oldPath = $dir.'/'.$oldData['product_image'];

				@unlink($oldPath);

				$CMS->class->image->quality = 1;

                //Create thumb
                /*foreach ($this->thumb_size as $keySize => $valSize)
                {
                    @unlink(\lib\image::getThumb($oldPath, $this->thumb_folder, "{$keySize}_"));
                    $CMS->class->image->resize($imgPath, \lib\image::getThumb($imgPath,$this->thumb_folder, "{$keySize}_"), $valSize);
                }*/
			}		


		}
		else
		{
 
			//
			if( !empty($base64_image))
			{

				// Tai hinh tu basse 64
				$data_info['product_image']  = $CMS->class->image->uploadImgBase64($base64_image, 'product', 'thumbnail', 220); 	 
			}
		}

		if($CMS->vars['enabled_commission'])
        {
            $product_commission_type = $CMS->input['product_commission_type']*1;
            $product_commission_value = $CMS->input['product_commission_value']*1;

            $sql_update_commission = ", product_commission_type='{$product_commission_type}', product_commission_value='{$product_commission_value}'";
        }
        else
        {
            $sql_update_commission = '';
        }

        //echo "UPDATE `".root_table."product` SET `product_name`='{$p_name}', `product_sku`='{$p_sku}', `product_barcode`='{$p_barcode}',   `product_status`='{$p_status}',  `product_show`='{$p_show}', `product_stock_available`='{$p_stock_available}', `product_show_instock`= '{$p_show_instock}', `product_manufacture`='{$p_manufacture}', `product_group`='{$p_product_group}', `product_option` = '{$p_product_option}' ,  `product_type`='{$p_type}', `product_cycle`='{$p_cycle}', `product_tax`='{$p_tax}', `product_price`='{$p_price}', `product_price_sell`='{$p_price_sell}', `product_price_original`='{$p_price_original}', product_subitem ='{$product_subitem}' , sup_id = '{$p_supplier}', product_description = '{$p_description}' , product_image = '{$data_info['product_image']}', sup_id = '{$p_supplier}' , product_guarantee_default = '{$p_guarantee}' , product_code  = '{$product_code}', product_shorturl = '{$product_shorturl}', staff_id = '{$staff_id}', product_image_alt='{$p_img_alt}', product_order='{$p_order}', product_up='{$p_up}', product_price_old='{$p_price_old}', product_information_1='$p_information_1', product_information_2='{$p_information_2}', product_gallery='{$galleryData}', product_name_lang='{$p_name_lang}', product_shorturl_lang='{$product_shorturl_lang}', product_information_1_lang='{$p_information_1_lang}', product_information_2_lang='{$p_information_2_lang}', product_description_lang='{$p_description_lang}', product_attribute='{$product_attribute}', product_real_type='{$product_real_type}', product_tra_type='{$product_tra_type}', meta_title='{$meta_title}', meta_keywords='{$meta_keywords}', meta_description='{$meta_description}', parent_id='{$parent_id}', product_attribute_custom='{$product_attribute_custom}', store_id = '{$store_id}' {$sql_update_commission}  WHERE `product_deleted`='0' AND `product_id`='{$data_info['product_id']}'";
 		//exit;

			$DB->query("UPDATE `".root_table."product` SET `product_name`='{$p_name}', `product_sku`='{$p_sku}', `product_barcode`='{$p_barcode}',   `product_status`='{$p_status}',  `product_show`='{$p_show}', `product_stock_available`='{$p_stock_available}', `product_show_instock`= '{$p_show_instock}', `product_manufacture`='{$p_manufacture}', `product_group`='{$p_product_group}', `product_option` = '{$p_product_option}' ,  `product_type`='{$p_type}', `product_cycle`='{$p_cycle}', `product_tax`='{$p_tax}', `product_price`='{$p_price}', `product_price_sell`='{$p_price_sell}', `product_price_original`='{$p_price_original}', product_subitem ='{$product_subitem}' , sup_id = '{$p_supplier}', product_description = '{$p_description}' , product_image = '{$data_info['product_image']}', sup_id = '{$p_supplier}' , product_guarantee_default = '{$p_guarantee}' , product_code  = '{$product_code}', product_shorturl = '{$product_shorturl}', staff_id = '{$staff_id}', product_image_alt='{$p_img_alt}', product_order='{$p_order}', product_up='{$p_up}', product_price_old='{$p_price_old}', product_information_1='$p_information_1', product_information_2='{$p_information_2}', product_gallery='{$galleryData}', product_name_lang='{$p_name_lang}', product_shorturl_lang='{$product_shorturl_lang}', product_information_1_lang='{$p_information_1_lang}', product_information_2_lang='{$p_information_2_lang}', product_description_lang='{$p_description_lang}', product_attribute='{$product_attribute}', product_real_type='{$product_real_type}', product_tra_type='{$product_tra_type}', meta_title='{$meta_title}', meta_keywords='{$meta_keywords}', meta_description='{$meta_description}', parent_id='{$parent_id}', product_attribute_custom='{$product_attribute_custom}', store_id = '{$store_id}', product_estimated_time={$product_estimated_time}, product_price_sale = '{$p_price_sale}' {$sql_update_commission}  WHERE `product_deleted`='0' AND `product_id`='{$data_info['product_id']}'");

			//Sync WHM data
       		if($CMS->vars['web_free']== 1 AND intval($CMS->vars['ibe_synced']) == 1)
    	    {
	    		$product_image_link = !empty($data_info['product_image']) && file_exists($CMS->vars['upload_dir'].'/product/'.$data_info['product_image']) ? $CMS->vars['upload_url'].'/product/'.$data_info['product_image'] : "";

	  
	    		$data_api['product_name']   			= "{$p_name}";
	    		$data_api['product_shorturl']   		= "{$product_shorturl}";
	    		$data_api['product_show']   		    = "{$p_show}";
	    		$data_api['product_image_alt']   		= "{$p_img_alt}";
	    		$data_api['product_group'] 				= "{$p_product_group}";
	    		$data_api['product_price_old'] 			= "{$p_price_old}";
	    		$data_api['product_price_original'] 	= "{$p_price_original}";
	    		$data_api['product_price_sell'] 		= "{$p_price_sell}";
	    		$data_api['product_up']   				= "{$p_up}";
	    		$data_api['product_description']   		= "{$p_description}";
	    		$data_api['product_image']   		    = "{$data_info['product_image']}";
	    		$data_api['product_image_link']   		= "{$product_image_link}";
	    		$data_api['product_time']   		  	= time();
	    		$data_api['original_id']   		  		= "{$data_info['product_id']}";
	    		$data_api['site_id']   		  		= "{$CMS->vars['site_id']}";
 
	    		$CMS->api->whm->execute('service_edit', $data_api); 
    	    }


			// // Insert moi doi voi sản phẩm con lấy theo thông tin sản phẩm cha (Edit ko thêm sản phẩm con)
			// if($form_type == 2)
			// {
			// 	$count_child = count($arr_product_name);
				
			// 	for($i=0; $i<$count_child; $i++)
			// 	{
			// 		if($arr_product_name[$i])
			// 		{
			// 			$p_name = $CMS->class->editor->input($arr_product_name[$i], "text");
			// 			$product_shorturl = $CMS->class->seo->cleanurl($p_name);

			// 			$p_barcode = "";
			// 			$product_subitem = [];
			// 			$p_price_sell = floatval($arr_product_price[$i]);
			// 			$p_sku =  $arr_product_sku[$i] ;
			// 			$p_tax = floatval($arr_product_tax[$i]);

			// 			$attr_arr['color'] = $arr_sub_color[$i];
			// 			$product_attribute = json_encode($attr_arr, JSON_UNESCAPED_UNICODE);
			// 			// if($check_valid)
			// 	  //       {
			//                 if ($idp = $CMS->product->checkName($p_name,null,$p_product_group, 1)) 
			//                 {
			//                 	// Update thông tin product
			//                 	$DB->query("UPDATE ".root_table."product SET product_attribute='{$product_attribute}', product_price_sell='{$p_price_sell}', product_sku='{$p_sku}', product_tax='{$p_tax}' WHERE product_id='{$idp}'");
			//                     $_SESSION['msg'] .= $CMS->lang['title_product_updated']." [{$p_name}] <br>";
			//                     continue;
			//                 }
			// 	        // }

			// 			if($CMS->vars['translations'])
			// 	        {
			// 	        	$p_name_lang = [];
			// 	        	$product_shorturl_lang = [];
			// 	        	$p_description_lang = [];
			// 	        	$p_information_1_lang = [];
			// 	        	$p_information_2_lang = [];
			// 	        	foreach ($CMS->vars['translations'] as $langCode => $langName)
			// 	            {
			// 	            	$p_name_lang[$langCode] = $p_name;
			// 	            	$product_shorturl_lang[$langCode] = $product_shorturl;
			// 	            	$p_description_lang[$langCode] = $CMS->class->editor->input($arr_product_description[$i],"text");
			// 	            	$p_information_1_lang[$langCode] = $p_information_1;
			// 	            	$p_information_2_lang[$langCode] = $p_information_2;
			// 	            }

			// 	            $p_name_lang = @json_encode($p_name_lang, JSON_UNESCAPED_UNICODE);
			// 	            $product_shorturl_lang = @json_encode($product_shorturl_lang, JSON_UNESCAPED_UNICODE);
			// 	            $p_description_lang = @json_encode($p_description_lang, JSON_UNESCAPED_UNICODE);
			// 	            $p_information_1_lang = @json_encode($p_information_1_lang, JSON_UNESCAPED_UNICODE);
			// 	            $p_information_2_lang = @json_encode($p_information_2_lang, JSON_UNESCAPED_UNICODE);
			// 	        }
				        

			// 			$DB->query("INSERT INTO `".root_table."product` (`product_time`, `user_id`, `product_name`, `product_sku`, `product_barcode`,  `product_status`,   `product_show`, `product_stock_available`, `product_manufacture`, `product_group`, `product_option`,  `product_type`, `product_cycle`, `product_tax`, `product_price`, `product_price_original`, `product_price_sell`, `sup_id`, product_description, product_image, product_guarantee_default, product_shorturl,staff_id, product_image_alt, product_order, product_up, product_price_old, product_information_1, product_information_2, product_gallery, product_name_lang, product_shorturl_lang, product_description_lang, product_information_1_lang, product_information_2_lang, product_attribute, product_real_type, parent_id) VALUES ('".time()."','{$member['user_id']}', '{$p_name}', '{$p_sku}', '{$p_barcode}', '{$p_status}', '{$p_show}', '{$p_stock_available}', '{$p_manufacture}', '{$p_product_group}', '{$p_product_option}' ,'{$p_type}', '{$p_cycle}', '{$p_tax}', '{$p_price}', '{$p_price_original}', '{$p_price_sell}', '{$p_supplier}', '{$p_description}', '{$data_info['product_image']}', '{$p_guarantee}', '{$product_shorturl}', '{$staff_id}', '{$p_img_alt}', '{$p_order}', '{$p_up}', '{$p_price_old}', '{$p_information_1}', '{$p_information_2}', '{$galleryData}', '{$p_name_lang}', '{$product_shorturl_lang}', '{$p_description_lang}', '{$p_information_1_lang}', '{$p_information_2_lang}', '{$product_attribute}', '{$product_real_type}', '{$data_info['product_id']}')");

			// 			$sid = $DB->last_insert_id();
			// 			$product_code = $product_group_code ."". $CMS->class->input->generate_code($sid, 6 - strlen(utf8_decode($product_group_code)) );

			// 			$DB->query("UPDATE `".root_table."product` SET `product_code`='{$product_code}' WHERE  `product_id`='{$sid}'");
			
			// 		}
				 
			// 	}
			// }

            //Clear all cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			$CMS->class->logs->key = "product_{$data_info['product_id']}";   
			// Create log
			$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['p_edit_success']} <b>#{$data_info['product_id']}</b>") . '<br>';

			$CMS->class->logs->key = "product_{$data_info['product_id']}";   
 			//$CMS->class->logs->key = "product_{$data_info['product_id']}";   
			$CMS->class->logs->save_detail("product",$data_info['product_id'],$this->getInfo($data_info['product_id']));
			return true;
	}

	public function deleted($id = null) {
		if (!empty($id)) {
			global $CMS, $DB, $member;
			// Check so luong tai san thuoc san pham nay
			$c_store_request = $this->count_store_rq_bypid($id);

		 	$c_asset = $CMS->product->getInfo($id,"product_quantity");
		 	$check = true;
		 
			if($c_store_request > 0)
			{
				$check = false;
				$_SESSION['error_msg'] .= "Tồn tại {$c_store_request} phiếu yêu cầu thuộc sản phẩm này. <a href='{$CMS->vars['root_domain']}/?site=store_request&stage=request&act=search&p_id={$id}'>[Danh sách phiếu yêu cầu]</a><br />";
			 

			}

			if($c_asset > 0)
			{
				$check = false;
				$_SESSION['error_msg'] .= "Tồn tại {$c_asset} tài sản thuộc sản phẩm này. <a href='{$CMS->vars['root_domain']}/?site=assets&act=search&p_id={$id}'>[Danh sách tài sản]</a>";
			 

			}

			if($check == false)
			{
				return false;
			}


			//Check so luong phieu yeu cau thuoc san pham nay

			$DB->query("UPDATE `".root_table."product` SET `product_deleted`=1 WHERE `product_id`='{$id}'");

			if($CMS->vars['web_free']== 1 AND intval($CMS->vars['ibe_synced']) == 1)
	    	{
	    		$data_api['original_id']   		  = "{$id}";
	    		$data_api['site_id']   		  = "{$CMS->vars['site_id']}";
	    		$CMS->api->whm->execute('service_delete', $data_api); 
	   		}

            //Clear all cache
            $CMS->class->cache->mdelete($this->cache_prefix);

            $_SESSION['msg'] = "Đã xóa sản phẩm thành công!";
			$CMS->class->logs->insert("Deleted_product_{$id}");
			return true;
		}
		else
		{
			$_SESSION['error_msg'] = "Không tìm thấy sản phẩm cần xóa!";
				return false;
		}
	}


	//===========================================================================
	//  ARRANGE
	//===========================================================================

	public function arrange()
	{
		global $CMS, $DB;
		
		$_SESSION["msg"] .= "";
 
		$sql = $DB->query("SELECT * FROM ".root_table."product WHERE product_deleted=0 ORDER BY product_id ASC");
	
		while ( $data = $DB->fetch_array( $sql ) )
		{ 
			$order = intval( $CMS->input["order_{$data['product_id']}"] );
			if ( $order )
			{
				 
				$DB->query("UPDATE ".root_table."product SET product_order='{$order}' WHERE product_id='{$data['product_id']}'");
				 
			}
		}

        $CMS->class->cache->mdelete($this->cache_prefix);

		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['product_arranged']}")."<br />";

		return true;
	}

	// Tanlv 18/02
	public function searchKey($key='', $type = 0 , $product_id = '', $store_id = '')
	{
		global $CMS, $DB;

		$module_name = $CMS->input['module_name'];

		if($key)
		{
			$clause = " AND (product_name LIKE '%{$key}%' OR product_code LIKE '%{$key}%' OR product_sku LIKE '%{$key}%'  OR product_barcode LIKE '%{$key}%')";
		}else
		{
			$clause = "";
		}
		if($type == 0)
		{
			// Loc theo loại hàng hoa
			$clause .= " AND product_type = '{$type}' ";
		}
		if($product_id != "" AND $product_id > 0)
		{
			// Search loai tru san pham goc
			$clause .= " AND product_id != '{$product_id}' ";
		}

		$clause .= $store_id ? " AND store_id = '{$store_id}' " : '';

		$data = array();

		$sql = "SELECT * FROM ".root_table."product WHERE product_deleted = 0 {$clause} ORDER BY product_id desc LIMIT 10 ";

		$cacheData = $DB->fetch_data($sql,$this->cache_prefix);
       
		if($cacheData)
		{
			foreach ($cacheData as $result)
			{
				// Over write gia ban, gia nhap
				//$result['product_price'] = $result['product_price_sell'] != 0 ? $result['product_price_sell'] : $result['product_price'];
 				if($module_name != "module_storerequest") // Neu moduel store request lay gia nhap
				{
					$result['product_price'] = $result['product_price_sell'];
				} 
				 

				if($result['sup_id'])
				{
					$result['sup_name'] = $CMS->supplier->get_info($result['sup_id'],"supplier_name");
				}else
				{
					$result['sup_name'] = "";
				}
				$result['product_price_show'] = $CMS->class->input->currency($result['product_price']);

				$data[] = $result;
			}
		}

		return $data;
	}


	// Tanlv 18/02
	public function searchKey_byCus(  $product_id = '', $cus_id = '', $store_id = '')
	{
		global $CMS, $DB;

		$module_name = $CMS->input['module_name'];

		$data = array();

		$sql_add = $store_id ? " AND store_id = '{$store_id}' " : '';

		$sql = $DB->query("SELECT DISTINCT product_id FROM ".root_table."order_item WHERE cus_id = '{$cus_id}' AND product_id > 0 AND ordi_deleted = 0 {$sql_add} ORDER BY  ordi_time desc LIMIT 3 ");

		if($DB->num_rows($sql) > 0)
		{
			while ($ord = $DB->fetch_array($sql)) 
			{
				 
				$result = $CMS->product->getInfo($ord['product_id']);
				if($module_name != "module_storerequest") // Neu moduel store request lay gia nhap
				{
					$result['product_price'] = $result['product_price_sell'];
				} 
				 
				if($result['sup_id'])
				{
					$result['sup_name'] = $CMS->supplier->get_info($result['sup_id'],"supplier_name");
				}else
				{
					$result['sup_name'] = "";
				}
				$result['product_price_show'] = $CMS->class->input->currency($result['product_price']);

				$data[] = $result;
			}
		}

		return $data;
	}

	public function addAjax()
	{
		global $CMS, $DB, $member;

		// print_r($_FILES);
		// print_r($CMS->input);exit;
		$product_name = $CMS->class->editor->input(urldecode($CMS->input['product_name']), "text");
		$product_description = $CMS->class->editor->input(urldecode($CMS->input['product_description']), "text");
		$product_sku = $CMS->class->editor->input(urldecode($CMS->input['product_sku']), "text");

		$product_barcode = $CMS->class->editor->input(urldecode($CMS->input['product_barcode']), "text");


		$product_group = intval($CMS->input['product_group']);
		$product_manufacture = intval($CMS->input['product_manufacture']);
		
		$product_price = $CMS->input['product_price'];
		$product_price_original = $CMS->input['product_price_original'];
		$product_price_sell = $CMS->input['product_price_sell'];

		$product_tax = floatval($CMS->input['product_tax']);
		$inclusive_of_tax = -1;//intval($CMS->input['inclusive_of_tax']);
		$product_type = intval($CMS->input['product_type']);
		$product_cycle = intval($CMS->input['product_cycle']);
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
		// 
		// if($count == 0)
		// {
		// 		print json_encode(array("status" => "error", "msg" => "Vui lòng nhập sản phẩm/dịch vụ"));exit;
		// }
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
			// else
			// {
			// 		print json_encode(array("status" => "error", "msg" => "Vui lòng nhập sản phẩm/dịch vụ"));exit;
			// }
		}
 
		$product_subitem_bk = $product_subitem;
 
		$product_subitem = json_encode($product_subitem, JSON_UNESCAPED_UNICODE);
		// Check name
		if(!$product_name)
		{
			print json_encode(array("status" => "error", "msg" => $CMS->lang['error_empty_product_name']));exit;
		}

		if(!$product_group)
		{
			print json_encode(array("status" => "error", "msg" => $CMS->lang['error_empty_product_group']));exit;
		}

		if($this->checkExit($product_name))
		{
			print json_encode(array("status" => "error", "msg" => $CMS->lang['error_exit_product_name']));exit;
		}

		// Check upload
		$file_tmp = isset($_FILES['upload_img']['tmp_name']) ? $_FILES['upload_img']['tmp_name'] : "";
		$file_name = isset($_FILES['upload_img']['name']) ? $_FILES['upload_img']['name'] : "";
		$file_type = isset($_FILES['upload_img']['type']) ? $_FILES['upload_img']['type'] : "";
		$file_size = isset($_FILES['upload_img']['size']) ? $_FILES['upload_img']['size'] : "";
		$file_error = isset($_FILES['upload_img']['error']) ? $_FILES['upload_img']['error'] : "";
		
		$file_ext = $CMS->class->attachment->get_ext( $file_name );
		// // Check type
		// if($file_ext != "png")
		// {
		// 	$arr_img = array("msg" => $CMS->lang['msg_error_type_image_upload'] , "status" => "error");
		// 	print json_encode($arr_img);exit;
		// }

		// Check dung luong file upload
		$max = 10;
		$max_file_upload = 1024*1024*$max;
		if($file_size > $max_file_upload )
		{
			$arr_img = array("msg" => $CMS->lang['msg_maxfile_upload_img'].$max."MB" , "status" => "error");
			print json_encode($arr_img);exit;
		}
		// // Check width height image
		// list($width, $height) = getimagesize($file_tmp);
		// if($width < 300 or $width > 4000)
		// {
		// 	$arr_img = array("msg" => $CMS->lang['msg_error_width_upload_img'].$CMS->lang['msg_note_upload_image']."(Width: {$width}px)" , "status" => "error");
		// 	print json_encode($arr_img);exit;
		// }

		// if($height < 300 or $height > 5000)
		// {
		// 	$arr_img = array("msg" => $CMS->lang['msg_error_height_upload_img'].$CMS->lang['msg_note_upload_image']."(Height: {$height}px)" , "status" => "error");
		// 	print json_encode($arr_img);exit;
		// }

		
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
			$CMS->class->image->is_dir("{$CMS->vars['upload_dir']}/product");
			$check = @copy($file_tmp, "{$CMS->vars['upload_dir']}/product/{$file_location}");
			if(!$check)
			{
				$arr_img = array("msg" => $CMS->lang['msg_error_upload_img'], "status" => "error");
				print json_encode($arr_img);exit;
			}
			
			$product_image = $file_location;
		}

		$product_time = time();

		$count = $DB->query("INSERT INTO ".root_table."product (product_name, product_description, product_sku, product_barcode,  product_group, product_price, product_price_original ,product_price_sell, product_tax, inclusive_of_tax, product_type, product_cycle, product_status, sup_id, product_subitem, product_image, product_time, user_id, product_manufacture) VALUES ('{$product_name}', '{$product_description}', '{$product_sku}', '{$product_barcode}',  '{$product_group}', '{$product_price}', '{$product_price_original}', '{$product_price_sell}', '{$product_tax}', '{$inclusive_of_tax}', '{$product_type}', '{$product_cycle}', '{$product_status}', '{$sup_id}', '{$product_subitem}', '{$product_image}', '{$product_time}', '{$user_id}', '{$product_manufacture}')");

		$id_product = $DB->last_insert_id();
		$product_group_code = $CMS->product_group->getInfo($product_group,'product_group_code');
		$product_code = $product_group_code . $CMS->class->input->generate_code($id_product, 6 - strlen(utf8_decode($product_group_code)) );

		$DB->query("UPDATE ".root_table."product SET product_code = '{$product_code}' WHERE product_id = '{$id_product}'");

        //Clear all cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		$info_product = $this->getInfo($id_product);
	 
		if($info_product['sup_id'] == 0)
		{
			$sup_name = $CMS->supplier->get_info($info_product['sup_id'],"supplier_name");
			if($sup_name == false) { $sup_name = "";}
		}
		else
		{
			$sup_name = "";
		}

		$sup_name = $CMS->supplier->get_info($info_product['sup_id'],"supplier_name");
		$info_product['sup_name'] = $sup_name;

		$key = "product_{$info_product['product_id']}";
		$CMS->class->logs->key = $key;
		$CMS->class->logs->insert("{$CMS->lang['add_product_success']} #{$info_product['product_id']}");


		// Create SESSION TEMP SAVE PRODUCT
		if(!$_SESSION['list_product'])
		{
			$_SESSION['list_product'] == array();
		}
		$index = intval($CMS->input['item_id']);

		$_SESSION['list_product'][$index]['product_id'] = $id_product;
		$_SESSION['list_product'][$index]['product_name'] = $product_name;
		$_SESSION['list_product'][$index]['product_description'] = $product_description;
		$_SESSION['list_product'][$index]['product_code'] = $product_code;
		$_SESSION['list_product'][$index]['product_group'] = $product_group;
		$_SESSION['list_product'][$index]['product_sku'] = $product_sku;
		$_SESSION['list_product'][$index]['product_barcode'] = $product_barcode;

		$_SESSION['list_product'][$index]['product_price'] = $product_price;
		$_SESSION['list_product'][$index]['product_price_original'] = $product_price_original;
		$_SESSION['list_product'][$index]['product_price_sell'] = $product_price_sell;
		$_SESSION['list_product'][$index]['product_quantity'] = 1;
		$_SESSION['list_product'][$index]['product_amount'] = round($product_price_sell + ($product_price_sell*$product_tax)/100);
		$_SESSION['list_product'][$index]['product_tax'] = $product_tax;
		$_SESSION['list_product'][$index]['inclusive_of_tax'] = $inclusive_of_tax;
		$_SESSION['list_product'][$index]['product_type'] = $product_type;
		$_SESSION['list_product'][$index]['product_cycle'] = $product_cycle;
		$_SESSION['list_product'][$index]['product_status'] = $product_status;
		$_SESSION['list_product'][$index]['product_subitem'] = $product_subitem_bk;
		$_SESSION['list_product'][$index]['product_image'] = $product_image;
		$_SESSION['list_product'][$index]['product_time'] = $product_time;
		$_SESSION['list_product'][$index]['sup_id'] = $sup_id;
		$_SESSION['list_product'][$index]['sup_name'] = $sup_name;
		$_SESSION['list_product'][$index]['user_id'] = $user_id;
		$_SESSION['list_product'][$index]['product_manufacture'] = $product_manufacture;
 
		
		if($count)
		{
			return $info_product;
			 
		}else
		{
			return 0;
		}

	}

	public function editAjax()
	{
		global $CMS, $DB, $member;
		$product_id = intval($CMS->input['product_id']);
		$oldData = $data = $this->getInfo($product_id);
		 
		$key = "product_{$oldData['product_id']}";
		$CMS->class->logs->key = $key;
		$CMS->class->logs->old_data = $oldData;
 



		$product_name = $CMS->class->editor->input(urldecode($CMS->input['product_name']), "text");
		$product_description = $CMS->class->editor->input(urldecode($CMS->input['product_description']), "text");
		$product_sku = $CMS->class->editor->input(urldecode($CMS->input['product_sku']), "text");
		$product_barcode = $CMS->class->editor->input(urldecode($CMS->input['product_barcode']), "text");

		$product_group = intval($CMS->input['product_group']);
		$product_manufacture = intval($CMS->input['product_manufacture']);
		$product_price =  $CMS->input['product_price'];
		$product_price_original = $CMS->input['product_price_original'];
		$product_price_sell = $CMS->input['product_price_sell'];
		$product_tax = floatval($CMS->input['product_tax']);
		$inclusive_of_tax = -1;//intval($CMS->input['inclusive_of_tax']);
		$product_cycle = intval($CMS->input['product_cycle']);

		$product_cycle_data = [0 => 0, 1 => 1, 2 => 1];
        $product_type =  intval($CMS->input['product_type']);
		$product_status = 0;
		$sup_id = intval($CMS->input['sup_id']);
		$user_id = $member['user_id'];

		$product_group_code = $CMS->product_group->getInfo($product_group,'product_group_code');
		$product_code = $product_group_code . $CMS->class->input->generate_code($product_id, 6 - strlen(utf8_decode($product_group_code)) );

		$arr_product_name = array_values($CMS->input['sub_product_name']);
		$arr_product_id = array_values($CMS->input['sub_product_id']);
		$arr_product_description = array_values($CMS->input['sub_product_description']);
		$arr_product_quantity = array_values($CMS->input['sub_product_quantity']);
		$arr_product_price = array_values($CMS->input['sub_product_price']);
		$arr_product_tax = array_values($CMS->input['sub_product_tax']);

		$count = count($arr_product_name);
		$product_subitem = array();
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
                
                
 
		// print_r($product_subitem);
		$product_subitem_bk = $product_subitem;
 
		$product_subitem = json_encode($product_subitem, JSON_UNESCAPED_UNICODE);
		// Check name
		if(!$product_name)
		{
			print json_encode(array("status" => "error", "msg" => $CMS->lang['error_empty_product_name']));exit;
		}
		if($this->checkExit($product_name, $product_id))
		{
			print json_encode(array("status" => "error", "msg" => $CMS->lang['error_exit_product_name']));exit;
		}

		// Check upload
		$file_tmp = isset($_FILES['upload_img']['tmp_name']) ? $_FILES['upload_img']['tmp_name'] : "";
		$file_name = isset($_FILES['upload_img']['name']) ? $_FILES['upload_img']['name'] : "";
		$file_type = isset($_FILES['upload_img']['type']) ? $_FILES['upload_img']['type'] : "";
		$file_size = isset($_FILES['upload_img']['size']) ? $_FILES['upload_img']['size'] : "";
		$file_error = isset($_FILES['upload_img']['error']) ? $_FILES['upload_img']['error'] : "";
		
		$file_ext = $CMS->class->attachment->get_ext( $file_name );
		// // Check type
		// if($file_ext != "png")
		// {
		// 	$arr_img = array("msg" => $CMS->lang['msg_error_type_image_upload'] , "status" => "error");
		// 	print json_encode($arr_img);exit;
		// }

		// Check dung luong file upload
		$max = 10;
		$max_file_upload = 1024*1024*$max;
		if($file_size > $max_file_upload )
		{
			$arr_img = array("msg" => $CMS->lang['msg_maxfile_upload_img'].$max."MB" , "status" => "error");
			print json_encode($arr_img);exit;
		}
		// // Check width height image
		// list($width, $height) = getimagesize($file_tmp);
		// if($width < 300 or $width > 4000)
		// {
		// 	$arr_img = array("msg" => $CMS->lang['msg_error_width_upload_img'].$CMS->lang['msg_note_upload_image']."(Width: {$width}px)" , "status" => "error");
		// 	print json_encode($arr_img);exit;
		// }

		// if($height < 300 or $height > 5000)
		// {
		// 	$arr_img = array("msg" => $CMS->lang['msg_error_height_upload_img'].$CMS->lang['msg_note_upload_image']."(Height: {$height}px)" , "status" => "error");
		// 	print json_encode($arr_img);exit;
		// }

		
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
			$CMS->class->image->is_dir("{$CMS->vars['upload_dir']}/product");
			$check = @copy($file_tmp, "{$CMS->vars['upload_dir']}/product/{$file_location}");
			if(!$check)
			{
				$arr_img = array("msg" => $CMS->lang['msg_error_upload_img'], "status" => "error");
				print json_encode($arr_img);exit;
			}
			
			$product_image = $file_location;
		}else
		{
			$product_image = $data['product_image'];
		}
		$product_time = time();
                
                
		
		if($product_id)
		{
			$count = $DB->query("UPDATE ".root_table."product SET product_name = '{$product_name}', product_description = '{$product_description}', product_code = '{$product_code}', product_group = '{$product_group}', product_price = '{$product_price}', product_price_original = '{$product_price_original}', product_price_sell = '{$product_price_sell}', product_tax = '{$product_tax}', inclusive_of_tax = '{$inclusive_of_tax}', product_cycle = '{$product_cycle}', product_type = '{$product_type}', sup_id ='{$sup_id}', product_subitem = '{$product_subitem}', product_image = '{$product_image}', product_manufacture = '{$product_manufacture}', product_sku = '{$product_sku}' , product_barcode = '{$product_barcode}' WHERE product_id = '{$product_id}'");
		}else
		{
			$count = $DB->query("INSERT INTO ".root_table."product (product_name, product_description, product_sku, product_barcode, product_group, product_price, product_price_original, product_price_sell, product_tax, inclusive_of_tax, product_type, product_cycle, product_status, sup_id, product_subitem, product_image, product_time, user_id, product_manufacture) VALUES ('{$product_name}', '{$product_description}', '{$product_sku}', '{$product_barcode}', '{$product_group}', '{$product_price}', '{$product_price_original}', '{$product_price_sell}', '{$product_tax}', '{$inclusive_of_tax}', '{$product_type}', '{$product_cycle}', '{$product_status}', '{$sup_id}', '{$product_subitem}', '{$product_image}', '{$product_time}', '{$user_id}', '{$product_manufacture}')");

			$product_id = $DB->last_insert_id();
			$product_code = $product_group_code . $CMS->class->input->generate_code($product_id, 6 - strlen(utf8_decode($product_group_code)) );
			$DB->query("UPDATE ".root_table."product SET product_code = '{$product_code}' WHERE product_id = '{$product_id}'");
		}

        //Clear all cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		$info_product = $this->getInfo($product_id);
		if($info_product['sup_id'] == 0)
		{
			$sup_name = $CMS->supplier->get_info($info_product['sup_id'],"supplier_name");
			if($sup_name == false) { $sup_name = "";}
		}
		else
		{
			$sup_name = "";
		}
		$info_product['sup_name'] = $sup_name;

		$CMS->class->logs->key = $key;
		$CMS->class->logs->insert("{$CMS->lang['edit_product_success']} <b>#{$info_product['product_id']}</b>");
		$CMS->class->logs->key = $key;
		$CMS->class->logs->save_detail("product",$product_id, $info_product );

		if($count)
		{
			if(intval($CMS->input['type'] == 1))
			{
				// Create SESSION TEMP SAVE PRODUCT
				if(!$_SESSION['list_product'])
				{
					$_SESSION['list_product'] == array();
				}
				$index = intval($CMS->input['item_id']);

				$_SESSION['list_product'][$index]['product_id'] = $product_id;
				$_SESSION['list_product'][$index]['product_name'] = $product_name;
				$_SESSION['list_product'][$index]['product_description'] = $product_description;
				$_SESSION['list_product'][$index]['product_code'] = $product_code;
				$_SESSION['list_product'][$index]['product_sku'] = $product_sku;
				$_SESSION['list_product'][$index]['product_barcode'] = $product_barcode;
				$_SESSION['list_product'][$index]['product_group'] = $product_group;
				$_SESSION['list_product'][$index]['product_price'] = $product_price;
				$_SESSION['list_product'][$index]['product_price_original'] = $product_price_original;
				$_SESSION['list_product'][$index]['product_price_sell'] = $product_price_sell;
				$_SESSION['list_product'][$index]['product_quantity'] = 1;
				$_SESSION['list_product'][$index]['product_amount'] = round($product_price_sell + ($product_price_sell*$product_tax)/100);
				$_SESSION['list_product'][$index]['product_tax'] = $product_tax;
				$_SESSION['list_product'][$index]['inclusive_of_tax'] = $inclusive_of_tax;
				$_SESSION['list_product'][$index]['product_type'] = $product_type;
				$_SESSION['list_product'][$index]['product_cycle'] = $product_cycle;
				$_SESSION['list_product'][$index]['product_status'] = $product_status;
				$_SESSION['list_product'][$index]['product_subitem'] = $product_subitem_bk;
				$_SESSION['list_product'][$index]['product_image'] = $product_image;
				$_SESSION['list_product'][$index]['product_time'] = $product_time;
				$_SESSION['list_product'][$index]['sup_id'] = $sup_id;
				$_SESSION['list_product'][$index]['sup_name'] = $sup_name;
				$_SESSION['list_product'][$index]['user_id'] = $user_id;
				$_SESSION['list_product'][$index]['product_manufacture'] = $product_manufacture;
			}

			// print_r($_SESSION['list_product']) ;exit;
			return $info_product;
			 
		}else
		{
			return 0;
		}

	}

	public function getOptionProduct($parent_id=0, $disable_title = false, $disable_barcode = true, $check_ship_fee = false)
	{
		global $CMS, $DB;

		$output = $disable_title ? "" : "<option value>{$CMS->lang['tilte_choose_parent']}</option>";
		$sql = $DB->query("SELECT product_id, product_name, product_barcode FROM ".root_table."product WHERE product_deleted = 0 AND parent_id = '{$parent_id}' ORDER BY product_name");
		if( $DB->num_rows($sql) > 0 )
		{
			while( $result = $DB->fetch_array($sql) ) 
			{
				if( $check_ship_fee )
				{
					$sql_ship_fee = $DB->query("SELECT 0 FROM ".root_table."shipping_fee WHERE product_id = '{$result['product_id']}' AND ship_deleted = 0");
					if( $DB->num_rows($sql_ship_fee) >= 4 )
					{
						continue;
					}
				}

				$name_show = $result['product_name'];
				if( !$disable_barcode AND $result['product_barcode'] )
				{
					$name_show .= ' (' . $result['product_barcode'] . ')';
				}
				$name_show = preg_replace('!\s+!', ' ', $name_show);
				
				$output .="<option value='{$result['product_id']}'>{$name_show}</option>";
			}
		}

		return $output;
	}

    public function checkExist( $field, $value = "", $except_value = "" )
    {
        global $CMS, $DB, $member;

        if ( ! $field )
        {
            return true;
        }

        $sql_add = '';

        if ( $except_value )
        {
            $sql_add .= "{$field}!='{$except_value}' AND";
        }


        $sql =  "SELECT count(product_id) cnt FROM ".root_table."product WHERE {$sql_add} {$field}='{$value}' AND product_deleted=0";

        $result = $DB->fetch_data($sql, $this->cache_prefix);

        return $result[0]['cnt'];
    }

	public function checkExit($product_name='', $id_accept=0)
	{
		global $CMS, $DB;

		if($id_accept)
		{
			$clause = " AND product_id != '{$id_accept}' ";
		}else
		{
			$clause = "";
		}

		$sql = "SELECT count(0) cnt FROM ".root_table."product WHERE product_deleted = 0 AND product_name = '{$product_name}' {$clause}";

        $result = $DB->fetch_data($sql, $this->cache_prefix);

        return $result[0]['cnt'];
	}
	
	public function get_info( $record_id = 0, $field_name = "" )
	{
		global $CMS, $DB, $member;

		if ( ! $record_id AND $CMS->input['site'] == "product" )
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

		$sql = "SELECT * FROM ".root_table."product WHERE (product_id='{$record_id}' OR product_name='{$record_id}' OR product_code='{$record_id}') AND product_deleted = 0 ORDER BY product_id DESC LIMIT 1";

		$data = $DB->fetch_data($sql, $this->cache_prefix)[0];

        if($field_name)
        {
            return $data[$field_name];
        }

        return $data;
	}


	// Count storereuqest by product_id
	public function count_store_rq_bypid( $product_id = "")
	{
		global $CMS, $DB, $member;
		$key_s = ',"product_id":"'.$product_id.'",';
	//	echo "SELECT * FROM ".root_table."store_request WHERE request_product LIKE '%{$key_s}%' AND request_deleted = 0 " ;exit;
 
		$sql = $DB->query("SELECT * FROM ".root_table."store_request WHERE request_deleted=0 AND request_stage = '1' AND request_product LIKE '%{$key_s}%' ORDER BY request_id DESC");


		$count = $DB->num_rows($sql);

		if($count > 0)
		{
			return  $count;
		}
		else
		{
			return 0;
		}
	}


	public function count_asset_bypid( $product_id = "", $store_id = "")
	{
		global $CMS, $DB, $member;
		
		if($store_id != "")
		{
			$sql_add  = " AND store_id = '{$store_id}' ";
		}
 
		$sql = $DB->query("SELECT * FROM ".root_table."assets WHERE ass_deleted=0 AND is_available = 1 AND product_id = '{$product_id}'  {$sql_add} ");


		$count = $DB->num_rows($sql);

		if($count > 0)
		{
			return  $count;
		}
		else
		{
			return 0;
		}
	}


	public function addQuick($data=array())
	{
		global $CMS, $DB, $member;

		// Input
		$product_name = $data['product_name'];
		$product_description = $data['product_description'];
		$product_sku = $data['product_sku'];
		$product_barcode = $data['product_barcode'];
		$product_group = intval($data['product_group']);
		$product_price = $data['product_price'];
		$inclusive_of_tax = -1;
		$product_type = 0;
		$product_cycle = 0;
		$product_status = 1;
		$sup_id = $data['sup_id'];
		$product_subitem = "";
        $product_image = isset($data['product_image']) ? $data['product_image'] : "";
		$product_time = time();
		$product_manufacture = 0;
		$user_id = $member['user_id'];

		$count = $DB->query("INSERT INTO ".root_table."product (product_name, product_description, product_sku, product_barcode, product_group, product_price, product_tax, inclusive_of_tax, product_type, product_cycle, product_status, sup_id, product_subitem, product_image, product_time, user_id, product_manufacture) VALUES ('{$product_name}', '{$product_description}', '{$product_sku}', '{$product_barcode}', '{$product_group}', '{$product_price}', '{$product_tax}', '{$inclusive_of_tax}', '{$product_type}', '{$product_cycle}', '{$product_status}', '{$sup_id}', '{$product_subitem}', '{$product_image}', '{$product_time}', '{$user_id}', '{$product_manufacture}')");

		if($count)
		{
			$product_id = $DB->last_insert_id();
			$product_group_code = $CMS->product_group->getInfo($product_group,'product_group_code');
			$product_code = $product_group_code . $CMS->class->input->generate_code($product_id, 6 - strlen(utf8_decode($product_group_code)) );

			$DB->query("UPDATE ".root_table."product SET product_code = '{$product_code}' WHERE product_id = '{$product_id}'");

            //Clear cache
            $CMS->class->cache->mdelete($this->cache_prefix);

			return $product_id;
		}else
		{
			return false;
		}
	}

    function convert_input_old($data = [])
    {
        global $CMS;

        $arr = ['product_name', 'product_id', 'product_description', 'product_tax', 'product_quantity', 'product_price', 'product_amount', 'product_cycle', 'product_discount_type', 'product_discount_value'];

        $tmp = [];

        foreach ($arr as $key)
        {
            $tmp[$key] = array_values($data[$key]);
        }

        $return = [];

        foreach ($tmp['product_name'] as $k => $v)
        {
            if($v)
            {
                foreach ($arr as $key)
                {
                    $return[$k][$key] = $tmp[$key][$k];
                }

                $product = $this->getInfo($return[$k]['product_id']);
                $return[$k]['product_cycle_value'] = $return[$k]['product_cycle'] ;
                $return[$k]['product_cycle'] = $product['product_cycle'];
                $return[$k]['product_cycle_type'] = $product['product_cycle'];
                $return[$k]['product_type'] = $product['product_type'];

            }
        }

        return $return;
    }

    function convert_trx_item_old($data = [])
    {
        global $CMS;

        $arr = [
            'product_name' => 'tri_name',
            'product_id' => 'product_id',
            'product_description' => 'tri_description',
            'product_tax' => 'tri_tax',
            'product_quantity' => 'tri_quantity',
            'product_amount' => 'tri_total',
            'product_price' => 'tri_price',
            'product_old_price' => 'tri_old_price',
            'product_cycle' => 'tri_cycle_type',
            'product_cycle_value' => 'tri_cycle',
            'product_discount_type' => 'tri_discount_type',
            'product_discount_value' => 'tri_discount_value',
        ];

        foreach ($data as $item)
        {
            $tmp = [];
            foreach ($arr as $k => $v)
            {
                $tmp[$k] = $item[$v];
            }

            $tmp['product_type'] = $item['tri_cycle_type'] == 0 ? 0 : 1;

            $return[] = $tmp;
        }

        return $return;
    }

    function importProductList()
    {
        global $CMS, $DB, $member;

        // require file
        require_once root_path."vendor/autoload.php";

        /**
         * Tpl key list to check existed
         */
        $tplKeyList = [];

        /**
         * Marked error position
         */
        $errorPositions = [];

        /**
         * Fields is required
         */
        $checkRequired = ['product_name','product_group'];

        // Input
        $file_tmp = isset($_FILES['upload_file']['tmp_name']) ? $_FILES['upload_file']['tmp_name'] : "";
        $file_name = isset($_FILES['upload_file']['name']) ? $_FILES['upload_file']['name'] : "";

        //CHeck error
        if($fileUploadErrorCode = $_FILES['upload_file']['error'])
        {
            $_SESSION['error_msg'] = $CMS->lang['file_upload_error_'.$fileUploadErrorCode];
            return false;
        }

        // Check file allow
        $file_ext = $CMS->class->attachment->get_ext( $file_name );
        $arr_allow = array("xls","xlsx");
        $count=0;
        if(!in_array($file_ext, $arr_allow))
        {
            $_SESSION['error_msg'] = $CMS->lang['error_ext_file_upload'];
            return false;
        }

        // Khoi tao
        $objPHPExcel = \PHPExcel_IOFactory::load($file_tmp);

        $highestRow         = $objPHPExcel->getActiveSheet()->getHighestRow(); // e.g. 10
        $highestColumn      = $objPHPExcel->getActiveSheet()->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);

        //Reset style
        $styleDefault = array(
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_NONE,
            ),
            'font'  => array(
                'bold'  => false,
                'color' => array('rgb' => '000000'))
        );

        $objPHPExcel->getActiveSheet()->getStyle("A2:{$highestColumn}{$highestRow}")->applyFromArray($styleDefault);

        //Danh sách thứ tự các field tương ứng với thứ tự cột từ file excel
        $fields_text = 'product_name,product_type,product_sku,product_code,product_cycle,product_price,product_price_sell,product_up,product_tax,product_group,sup_id,product_guarantee_default,product_description,product_show,product_image';
        $fields = explode(',', $fields_text);
        $fields_flip = array_flip($fields); // Đảo ngược key và value

        //Range char A-Z
        $rangeChar = range('A', 'Z') ;

        //Xác định cột chứa hình ảnh
        $imageColIndex = $fields_flip['product_image'];
        $imageColName = $rangeChar[$imageColIndex];

        /**
         * Lay thong tin cac hinh anh trong file
         */

        $fileImages = [];
        $flagErr = false; //Biến cờ đánh dấu lỗi
        $errStyleCell = array(
//            'borders' => array(
//                'allborders' => array(
//                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
//                    'color' => array('rgb' => 'FF0000')
//                )
//            ),
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'E0E0E0')
            )
        ); //Style đánh dấu ô có lỗi

        $errStyleRow = array(
            'font'  => array(
//                'bold'  => true,
                'color' => array('rgb' => 'FF0000'),
            )); //Style đánh dấu dòng có lỗi

        $sql_values = ""; //values for multi insert

        $validData = []; //Mang chua cac dong hop le

        /**
         * Loop to check validate
         */
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
        {
            // Loop row
            for ($row = 1; $row <= $highestRow; ++ $row)
            {
                if($row == 1) { continue;}
                $data = [];

                //Check null row
                $rowIsNULL = true;
                for ($col = 0; $col < $highestColumnIndex; ++$col)
                {
                    $cell = $worksheet->getCellByColumnAndRow($col, $row);
                    $cellData = trim($cell->getValue());
                    if($cellData !== "" && $cellData !== null) {$rowIsNULL = false;}
                }
                if($rowIsNULL){continue;} //If row is null => ignore it

                // Loop col
                for ($col = 0; $col < $highestColumnIndex; ++$col)
                {
                    $cell = $worksheet->getCellByColumnAndRow($col, $row);
                    if($fields[$col])
                    {
                        $data[$fields[$col]] = $CMS->class->editor->input($cell->getValue(), "text"); //Gan du lieu lai theo giong field trong DB

                        if($fields[$col] == 'product_group')
                        {
                            if($CMS->vars['pGroupTemp'][$data['product_group']])
                            {
                                $product_group = $CMS->vars['pGroupTemp'][$data['product_group']];
                            }
                            else
                            {
                                $product_group = $CMS->product_group->getInfo($data['product_group'], '*', 'product_group_name');

                                if(!$product_group)
                                {
                                    $product_group_type = strtolower($data['product_type']) == 'product' ? 0 : 1;

                                    //Create new
                                    $pg_id = $CMS->product_group->add(null, $data['product_group'], null, 1, null, null, null, $product_group_type);

                                    $product_group = $CMS->product_group->getInfo($pg_id);
                                }

                                $CMS->vars['pGroupTemp'][$data['product_group']] = $product_group;
                            }

                            $data['product_group'] = intval($product_group['product_group_id']);
                        }


                        if($fields[$col] == 'sup_id')
                        {
                            if($CMS->vars['supplierTemp'][$data['sup_id']])
                            {
                                $supplier = $CMS->vars['supplierTemp'][$data['sup_id']];
                            }
                            else
                            {
                                $supplier = $CMS->supplier->get_info($data['sup_id']);
                                $CMS->vars['supplierTemp'][$data['sup_id']] = $supplier;
                            }

                            $data['sup_id'] = intval($supplier['supplier_id']);
                        }

                        /**
                         * Check required
                         */
                        if(in_array($fields[$col], $checkRequired))
                        {
                            if($data[$fields[$col]] == '')
                            {
                                $errorPositions[$row][] = $col;
                            }
                        }

                        if($fields[$col] == 'product_code')
                        {
                            if($data[$fields[$col]]!='')
                            {
                                $tplKeyList[$data[$fields[$col]]][] = ['col' => $col, 'row' => $row];
                            }
                        }
                    }
                }

                if(!isset($errorPositions[$row]))
                {
                    $validData[$row] = $data;
                }
            }
        }

        //Duyet danh sach key de check trung
        foreach ($tplKeyList as $tplCode => $errorPos)
        {

            if(!$CMS->input['is_overwrite'])
            {
                if(count($errorPos)>1 || $this->checkExist('product_code', $tplCode))
                {
                    foreach ($errorPos as $errColRows)
                    {
                        $errorPositions[$errColRows['row']][] = $errColRows['col'];
                    }
                }
            }

            //Xóa bỏ các dòng trung key, chỉ giữ lại dòng cuối
            if(($cnt = count($errorPos)) >= 2)
            {
                for ($i=0; $i<$cnt-1; $i++)
                {
                    unset($validData[$errorPos[$i]['row']]);
                }
            }
        }

        if($errorPositions)
        {
            foreach ($errorPositions as $errRow => $errCols)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension();

                //Đánh dấu dòng lỗi
                $objPHPExcel->getActiveSheet()->getStyle("A{$errRow}:{$highestColumn}{$errRow}")->applyFromArray($errStyleRow);

                foreach ($errCols as $errCol)
                {
                    //Đánh dấu các ô bị lỗi
                    $colName = $rangeChar[$errCol];
                    $objPHPExcel->getActiveSheet()->getStyle("{$colName}{$errRow}")->applyFromArray($errStyleCell);
                }
            }

            /**
             * Trả về file highlight các dòng bị lỡi cho khách sửa lại
             */
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $tmpFile = "import_file_{$member['user_id']}.xls";
            $objWriter->save("{$CMS->vars['upload_dir']}/excel/{$tmpFile}");
            $_SESSION['error_msg'] = "{$CMS->lang['invalid_import_data']} <a href='{$CMS->vars['upload_url']}/excel/{$tmpFile}'>Download file</a>";
            $CMS->global->redirectReferer();
        }

        /**
         * get images from file
         */
        foreach ($objPHPExcel->getActiveSheet()->getDrawingCollection() as $drawing) {
            if ($drawing instanceof \PHPExcel_Worksheet_MemoryDrawing) {
                ob_start();
                call_user_func(
                    $drawing->getRenderingFunction(),
                    $drawing->getImageResource()
                );

                $imageContents = ob_get_contents();
                ob_end_clean();
                $extension = 'jpg';

                switch ($drawing->getMimeType()) {
                    case \PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG :
                        $extension = 'png'; break;
                    case \PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_GIF:
                        $extension = 'gif'; break;
                    case \PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_JPEG :
                        $extension = 'jpg'; break;
                }

            } else {
                $zipReader = fopen($drawing->getPath(),'r');
                $imageContents = '';

                while (!feof($zipReader)) {
                    $imageContents .= fread($zipReader,1024);
                }
                fclose($zipReader);
                $extension = $drawing->getExtension();
            }

            //Lưu tam source hinh va vi tri cua cell chua hinh lai

            $fileImages[$drawing->getCoordinates()]['src'] = $imageContents;
            $fileImages[$drawing->getCoordinates()]['ext'] = $extension;
        }

        //Create folder upload product

        /**
         * Loop dữ liệu để upload ảnh và insert vào DB
         */
        foreach ($validData as $row => $data)
        {
            $time = time();
            $random = rand(0,10000);

            //UPLOAD IMAGE
            $fileToUpload = $fileImages["{$imageColName}{$row}"];

            if($fileToUpload)
            {
                $fileName = "{$time}_{$random}_".$CMS->class->seo->cleanurl("{$data['product_name']}").".{$fileToUpload['ext']}";

                @file_put_contents("{$CMS->vars['upload_dir']}/product/{$fileName}", $fileToUpload['src']);

                //Tạo Thumb
                $CMS->class->image->resize("{$CMS->vars['upload_dir']}/product/{$fileName}", "{$CMS->vars['upload_dir']}/product/thumbnail/{$fileName}", 220,150);

                $data['product_image'] = $fileName;
            }
            else
            {
                // $data['product_image'] = '';
                unset($data['product_image']); // ThamLV d14-5-2018
            }

            $data['product_show'] = strtolower($data['product_show']) == 'show' ? 1 : 0;

            $data['product_status'] = 0;

            $data['product_type'] = strtolower($data['product_type']) == 'product' ? 0 : 1;

            $data['product_shorturl'] = $CMS->class->seo->cleanurl($data['product_name']);

            $data['product_code'] = $data['product_code'] ? $data['product_code'] : $data['product_shorturl'].'_'.$CMS->class->random->character(4);

            if($this->checkExist('product_code', $data['product_code']))
            {
                /**
                 * Update record
                 */
                $sql_update = "UPDATE ".root_table."product SET ";

                foreach ($data as $field => $value)
                {
                    //Create values sql
                    $sql_update .= "{$field}='{$value}',";
                }

                $sql_update = trim($sql_update,',');

                $sql_update .= " WHERE product_code='{$data['product_code']}'";

                $DB->query($sql_update);
            }
            else
            {
                $data['user_id'] = $member['user_id'];

                /**
                 * Multi insert
                 */
                $sql_values .= "(";
                foreach ($data as $field => $value)
                {
                    //Create values sql
                    $sql_values .= "'{$value}',";
                }
                $sql_values = trim($sql_values,',');
                $sql_values .= "),";
            }

        }

        $sql_values = trim($sql_values,',');

        /**
         * Lay danh sach field de insert
         */
        $field_list = array_keys($data);
        $field_list = implode(',',$field_list);

        $sql = "INSERT INTO ".root_table."product ({$field_list}) VALUES {$sql_values}";

        $DB->query($sql);

        //Clear all cache
        $CMS->class->cache->mdelete($this->cache_prefix);

        $_SESSION['msg'] = $CMS->lang['import_file_success'];

        $CMS->global->redirectReferer();

    }

    function delete_normal($id=0)
    {
    	global $CMS, $DB;

    	// Get info
		$data = $this->get_info($id);
		
		// Check existing
		if ( ! $data ) { return false; }
		
		$DB->query("UPDATE ".root_table."product SET product_deleted = 1 WHERE product_id={$data['product_id']}");

		// Delete image
		if($data['product_image'])
		{
			@unlink("{$CMS->vars['upload_dir']}/product/{$data['product_image']}");
		}

		// Delete product_gallery
		$arr_gallery = json_decode($data['product_gallery'], 1);
		if(is_array($arr_gallery))
		{
			foreach ($arr_gallery as $image) {
				@unlink("{$CMS->vars['upload_dir']}/{$image}");
			}
		}

        //Clear all cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		// Create log
		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['p_deleted_success']} <b>[{$data['product_name']}]</b>")."<br />";
		
		return true;
    }
    

    function hide_all()
    {
    	global $CMS, $DB;

    	 $_SESSION["msg"] .= "";
 		for ( $i = 0; $i < intval( $CMS->input["data_cnt"] ); $i++ )
		{
			$id = intval( $CMS->input["id_{$i}"] );
				
			if ( $id )
			{
        		$DB->query("UPDATE ".root_table."product SET product_show='0' WHERE product_id='{$id}'"); 
	        }
		}

        //Clear all cache
        $CMS->class->cache->mdelete($this->cache_prefix);

		$_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['product_hided_all']}")."<br />";

		return true;
    }

    // Load product status
    static function list_product_status ()
    {
        global $CMS;
        $output = "";
        
        $output .= "<option value='0'>{$CMS->lang['p_statup_00']}</option>"
                .  "<option value='1'>{$CMS->lang['p_statup_01']}</option>"
                .  "<option value='2'>{$CMS->lang['p_statup_02']}</option>";
        
        return $output;
    }

    // Data form product
    function dataForm($data=[])
    {
    	global $CMS, $tpl, $member;
// print "<pre>"; print_r($data);exit;
    	$user = $CMS->user->get_info($member['user_id']);
        $draft_product = json_decode($user['user_draft_product'], true);
      
        $tpl->store_id =  isset($data['store_id']) ? $data['store_id'] : 0;

        $tpl->p_type = $data['product_type'] ? $data['product_type'] : ($CMS->input['p_type'] ? $CMS->input['p_type'] : 0);
		if($CMS->vars['addon_goods_enable'] == 1 AND $tpl->p_type == 0)
		{
        	list($tpl->row_store,$tpl->list_store) = $CMS->store->get_list_store($data['store_id']);
        }

        $tpl->p_name = isset($data['product_name']) ? $data['product_name'] : ($CMS->input['p_name'] ? $CMS->input['p_name'] : "");
        $tpl->p_img_alt = isset($data['product_image_alt']) ? $data['product_image_alt'] : ($CMS->input['p_img_alt'] ? $CMS->input['p_img_alt'] : "");

        $tpl->p_show = isset($data['product_show']) ? $data['product_show'] : ($CMS->input['p_show'] ? $CMS->input['p_show'] : 1);
 
        $tpl->p_first_remain = isset($data['product_tax']) ? $data['product_first_remain'] : ($CMS->input['p_first_remain'] ? $CMS->input['p_first_remain'] : 0);
        $tpl->p_stock_available = isset($data['product_stock_available']) ? $data['product_stock_available'] : ($CMS->input['p_stock_available'] ? $CMS->input['p_stock_available'] : 0);
        $tpl->p_show_instock = isset($data['product_show_instock']) ? $data['product_show_instock'] : ($CMS->input['p_show_instock'] ? $CMS->input['p_show_instock'] : 0);


        // $p_product_group = isset($CMS->input['p_product_group']) ? $CMS->input['p_product_group'] : ($draft_product['product_group'] != "" ? $draft_product['product_group'] : "");
        $tpl->p_product_group = isset($CMS->input['p_product_group']) ? $CMS->input['p_product_group'] : ($data['product_group'] != "" ? $data['product_group'] : "");


        $tpl->p_tax = isset($data['product_tax']) ? $data['product_tax'] : ($CMS->input['p_tax'] ? $CMS->input['p_tax'] : 0);
        $tpl->p_price = isset($data['product_price']) ? $data['product_price'] : ($CMS->input['p_price'] ? $CMS->input['p_price'] : 0);
        $tpl->p_price_sell = isset($data['product_price_sell']) ? $data['product_price_sell'] : ($CMS->input['p_price_sell'] ? $CMS->input['p_price_sell'] : 0);
        $tpl->p_price_sale = isset($data['product_price_sale']) ? $data['product_price_sale'] : ($CMS->input['p_price_sale'] ? $CMS->input['p_price_sale'] : 0);
        $tpl->p_price_old = isset($data['product_price_old']) ? $data['product_price_old'] : ($CMS->input['p_price_old'] ? $CMS->input['p_price_old'] : 0);
        $tpl->p_order = isset($data['product_order']) ? intval($data['product_order']) : ($CMS->input['p_order'] ? $CMS->input['p_order'] : 0);
        $tpl->p_up = isset($data['product_up']) ? $data['product_up'] : ($CMS->input['p_up'] ? $CMS->input['p_up'] : "");

        $tpl->p_price_original = isset($data['product_price_original']) ? $data['product_price_original'] : ($CMS->input['p_price_original'] ? $CMS->input['p_price_original'] : 0);

        $tpl->p_description = isset($data['product_description']) ? $data['product_description'] : ($CMS->input['p_description'] ? $CMS->input['p_description'] : "");

        $tpl->p_information_1 = isset($data['product_information_1']) ? $data['product_information_1'] : ($CMS->input['p_information_1'] ? $CMS->input['p_information_1'] : "");

        $tpl->p_information_2 = isset($data['product_information_2']) ? $data['product_information_2'] : ($CMS->input['p_information_2'] ? $CMS->input['p_information_2'] : "");
        $tpl->product_real_type = isset($data['product_real_type']) ? $data['product_real_type'] : ($CMS->input['product_real_type'] ? $CMS->input['product_real_type'] : "");
        
		$tpl->product_tra_type = isset($data['product_tra_type']) ? $data['product_tra_type'] : ($CMS->input['product_tra_type'] ? $CMS->input['product_tra_type'] : "");

		// Meta seo
		$tpl->meta_title = isset($data['meta_title']) ? $data['meta_title'] : ($CMS->input['meta_title'] ? $CMS->input['meta_title'] : "");
		$tpl->meta_keywords = isset($data['meta_keywords']) ? $data['meta_keywords'] : ($CMS->input['meta_keywords'] ? $CMS->input['meta_keywords'] : "");
		$tpl->meta_description = isset($data['meta_description']) ? $data['meta_description'] : ($CMS->input['meta_description'] ? $CMS->input['meta_description'] : "");


        $tpl->product_option = isset($data['product_option']) ? $data['product_option'] : ($CMS->input['p_product_option'] ? implode(",", $CMS->input['p_product_option']): "");
        $tpl->p_sku = isset($data['product_sku']) ? $data['product_sku'] : ($CMS->input['p_sku'] ? $CMS->input['p_sku'] : '');
        $tpl->p_barcode = isset($data['product_barcode']) ? $data['product_barcode'] : ($CMS->input['p_barcode'] ? $CMS->input['p_barcode'] : '');
        $tpl->p_cycle = isset($data['product_cycle']) ? $data['product_cycle'] : ($CMS->input['p_cycle'] ? $CMS->input['p_cycle'] : 0);
        // manufacture
        $tpl->p_manufacture = isset($data['product_manufacture']) ? $data['product_manufacture'] : ( $CMS->input['p_manufacture'] ? $CMS->input['p_manufacture'] : $draft_product['product_manufacture']);
        $tpl->manufacture = $CMS->manufacture->getAll();
        // Supplier
        $tpl->p_supplier = isset($data['sup_id']) ? $data['sup_id'] : ($CMS->input['p_supplier'] ? $CMS->input['p_supplier'] : $draft_product['product_supplier']);
        $tpl->supplier = $CMS->supplier->get_list_supplier(1);

        $tpl->p_guarantee = isset($data['product_guarantee_default']) ? $data['product_guarantee_default'] : ($CMS->input['p_guarantee'] ? $CMS->input['p_guarantee'] : "");

        $tpl->product_commission_type = isset($CMS->input['product_commission_type']) ? $CMS->input['product_commission_type']*1 : $data['product_commission_type'];
        $tpl->product_commission_value = isset($CMS->input['product_commission_value']) ? $CMS->input['product_commission_value']*1 : $data['product_commission_value'];

        // parent id
        $tpl->parent_id = isset($CMS->input['parent_id']) ? $CMS->input['parent_id'] : $data['parent_id'];
        $data_name = $this->getInfo($tpl->parent_id);
        if($CMS->vars['translations'])
        {
        	$parent_name = $data_name['product_name_lang'] ? json_decode($data_name['product_name_lang'], 1) : $data_name['product_name']; 
        	$tpl->parent_name = is_array($parent_name) ? $parent_name[$CMS->vars['default_language']] : $data_name['product_name'];
        }else
        {
        	$tpl->parent_name = $data_name['product_name'];
        }

        // Thuộc tính show
        $tpl->attribute = isset($CMS->input['attribute']) ? json_encode($CMS->input['attribute'], JSON_UNESCAPED_UNICODE) : $data['product_attribute_custom'];
        
        // Check hinh upload
        if (isset($_FILES['p_image'])) 
        {
            $tpl->base64_image = $tpl->src_image_upload = $data['base64_image'];
            $tpl->style_display = " style='display:block; width: 100%; height: auto; margin: 0 auto;' ";
        }else if($data['base64_string'])
        {
            $tpl->base64_image = $tpl->src_image_upload = $data['base64_string'];
            $tpl->style_display = " style='display:block; width: 100%; height: auto; margin: 0 auto;' ";
        }

        // Get list mage gallery
        $list_gallery = json_decode($data['product_gallery'],1);
        if(is_array($list_gallery))
        {
        	$tpl->list_gallery = $list_gallery;
        }

        $tpl->option_p_show = "";
        for ($i = 1; $i >= 0; $i--) {
            if ($i == $tpl->p_show) {
                $tpl->option_p_show .= "   <div class='radio w25'><input type='radio' checked  name='p_show' id='radio-show-{$i}' value='{$i}'><label for='radio-show-{$i}'>{$CMS->lang['p_show_'.$i]}</label></div>";
            } else {
                $tpl->option_p_show .= "   <div class='radio w25'><input type='radio' name='p_show' id='radio-show-{$i}' value='{$i}'><label for='radio-show-{$i}'>{$CMS->lang['p_show_'.$i]}</label></div>";
            }
        }

 
        $tpl->option_p_show_instock = "";
        for ($i = 1; $i >= 0; $i--) {
            if ($i == $tpl->p_show_instock) {
                $tpl->option_p_show_instock .= "   <div class='radio w25'><input type='radio' checked  name='p_show_instock' id='radio-p-show-instock-{$i}' value='{$i}'><label for='radio-p-show-instock-{$i}'>{$CMS->lang['p_show_'.$i]}</label></div>";
            } else {
                $tpl->option_p_show_instock .= "   <div class='radio w25'><input type='radio'    name='p_show_instock' id='radio-p-show-instock-{$i}' value='{$i}'><label for='radio-p-show-instock-{$i}'>{$CMS->lang['p_show_'.$i]}</label></div>";
            }
        }

        $tpl->option_p_stock_available = "";
        for ($i = 1; $i >= 0; $i--) {
            if ($i == $tpl->p_stock_available) {
                $tpl->option_p_stock_available .= "   <div class='radio w25'><input type='radio' checked  name='p_stock_available' id='radio-p-stock-available-{$i}' value='{$i}'><label for='radio-p-stock-available-{$i}'>{$CMS->lang['p_stock_available_'.$i]}</label></div>";
            } else {
                $tpl->option_p_stock_available .= "   <div class='radio w25'><input type='radio'    name='p_stock_available' id='radio-p-stock-available-{$i}' value='{$i}'><label for='radio-p-stock-available-{$i}'>{$CMS->lang['p_stock_available_'.$i]}</label></div>";
            }
        }

        $tpl->option_p_product_group = "<option value=''>{$CMS->lang['select']}</option>";
        $group = $CMS->product_group->getAllFull($tpl->p_type);

        // Level 1
        foreach ($group as $g) 
        {
            $selected = $tpl->p_product_group == $g['product_group_id'] ? 'selected' : "";
            $tpl->option_p_product_group .= "<option value='{$g['product_group_id']}' attrGroup='{$g['attr_group']}' {$selected}>{$g['product_group_name']}</option>";

            // Level 2
            if (count($g['data_item']) > 0) 
            {
                foreach ($g['data_item'] as $g2) 
                {
                    $selected2 = $tpl->p_product_group == $g2['product_group_id'] ? 'selected' : "";
                    $tpl->option_p_product_group .= "<option value='{$g2['product_group_id']}' attrGroup='{$g2['attr_group']}' {$selected2}> |__{$g2['product_group_name']}</option>";

                    // Level 3
                    if (count($g2['data_item']) > 0) 
                    {
                        foreach ($g2['data_item'] as $g3) 
                        {
                            $selected3 = $tpl->p_product_group == $g3['product_group_id'] ? 'selected' : "";
                            $tpl->option_p_product_group .= "<option value='{$g3['product_group_id']}' attrGroup='{$g3['attr_group']}' {$selected3}> &nbsp;&nbsp;&nbsp;|__{$g3['product_group_name']}</option>";

                            // Level 4
                            if (count($g3['data_item']) > 0) 
                            {
                                foreach ($g3['data_item'] as $g4) 
                                {
                                    $selected4 = $tpl->p_product_group == $g4['product_group_id'] ? 'selected' : "";
                                    $tpl->option_p_product_group .= "<option value='{$g4['product_group_id']}' attrGroup='{$g4['attr_group']}' {$selected4}> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|__{$g4['product_group_name']}</option>";
                                }
                            }
                        }
                    }
                }
            }
        }

        $tpl->product_group_name = $data['product_group_name'];
        $tpl->hidden_create_child_product = " style='display: block' ";
        if($CMS->input['act'] == "add" or $CMS->input['act'] == "add_do")
        {
        	$tpl->action_form = "/acp/?site={$CMS->input['site']}&act=add_do";
        	$tpl->url_back['list'] = "{$CMS->vars['root_domain']}/?site={$CMS->input['site']}";
		    $tpl->url_save[1]['key'] = "save_and_saveoption";
		    $tpl->url_save[1]['icon'] = " fa-life-saver";
		    $tpl->url_save[1]['js'] = " onclick='product_submit_saveoption();' ";
		    $tpl->url_save[1]['redirect'] = "0";
		    $tpl->footer_button = $CMS->global->footer_save("{$CMS->input['site']}", $tpl->url_save);
        }elseif($CMS->input['act'] == "edit" or $CMS->input['act'] == "edit_do")
        {
        	$tpl->un_product_subitem = json_decode($data['product_subitem'], true);
        	$tpl->check_is_pc = count($CMS->product->getProductChild($data['product_id']));
        	$tpl->action_form = "/acp/?site={$CMS->input['site']}&act=edit_do&id={$CMS->input['id']}";
        	$tpl->url_back['list'] = "{$CMS->vars['root_domain']}/?site={$CMS->input['site']}";
	        $tpl->url_back['detail_id'] = "{$data['product_id']}";

	        $tpl->footer_button = $CMS->global->footer_edit("{$CMS->input['site']}");
	        $tpl->hidden_create_child_product = " style='display: none' ";
        }

        $tpl->css_show = $CMS->input['site'] == "service" ? "block" : "none";
        $tpl->css_hide = $CMS->input['site'] == "service" ? "none" : "block";

        // staff
        // $tpl->option_staff = $CMS->input['act'] == "add" ? $CMS->user->load_list_user() : $CMS->user->load_list_user_2($data['staff_id']);
        $tpl->option_staff = $CMS->user->load_list_staff($data['staff_id']);
        // Option country
        $country_id_default = $CMS->country->idCountry($CMS->vars['default_country']);
        $tpl->city = $CMS->country->getOptionCity($country_id_default);
        $tpl->district = "";
        // product attribute
        $attr = json_decode($data['product_attribute'], 1);
// print "<pre>"; print_r($attr);exit;
        if($attr)
        {
        	// $CMS->input = array_merge($CMS->input, $data);
        	// For realestate
        	$tpl->p_price_show = $attr['p_price_show'];
        	$tpl->product_acreage = $attr['acreage'];
        	$tpl->product_city = $attr['city'];
        	$tpl->product_district = $attr['district'];
        	$tpl->wards = $attr['wards'];
        	$tpl->street = $attr['street'];
        	$tpl->map = $attr['map'];
        	$tpl->facade = $attr['facade'];
        	$tpl->entrance = $attr['entrance'];
        	$tpl->direction = $attr['direction'];
        	$tpl->dbalcon = $attr['dbalcon'];
        	$tpl->nfloors = $attr['nfloors'];
        	$tpl->nroom = $attr['nroom'];
        	$tpl->nbedroom = $attr['nbedroom'];
        	$tpl->nbathrooms = $attr['nbathrooms'];
        	$tpl->internet = $attr['internet'];
        	$tpl->furniture = $attr['furniture'];
        	$tpl->foutside = $attr['foutside'];
        	$tpl->utilities = $attr['utilities'];
        	$tpl->real_phone = $attr['real_phone'];

        	$tpl->color = $attr['color'];
        	$tpl->weight = $attr['weight'];
        	$tpl->width = $attr['width'];
        	$tpl->height = $attr['height'];
        	$tpl->length = $attr['length'];

        	// travel
        	$tpl->tra_number_day = $attr["tra_number_day"];
        	$tpl->tra_number_night = $attr["tra_number_night"];

        	$tpl->tra_time_start = $CMS->class->date->date_format($attr["tra_time_start"]);
        	$tpl->tra_time_end = $CMS->class->date->date_format($attr["tra_time_end"]);
        	$tpl->tra_vehicle_start = $attr["tra_vehicle_start"];
        	$tpl->tra_vehicle_end = $attr["tra_vehicle_end"];
        	$tpl->tra_minimum_seat = $attr["tra_minimum_seat"];
        	$tpl->tra_maximum_seat = $attr["tra_maximum_seat"];
        	$tpl->tra_summary_travel = $attr["tra_summary_travel"];
        	

        	$tpl->district = $CMS->country->getOptionDistrict($tpl->product_city);
// print "<pre>"; print_r($tpl->furniture);exit;
        	$tpl->list_internet = implode(",", $attr['internet']);
        	$tpl->list_furniture = implode(",", $attr['furniture']);
        	$tpl->list_foutside = implode(",", $attr['foutside']);
        	$tpl->list_utilities = implode(",", $attr['utilities']);
        	// print "<pre>"; print_r($tpl->list_furniture);exit;
        }

        // Option Attribute group
        $tpl->optionAttributeGroup = attribute::getOptionAttrGroup();

        $tpl->p_estimated_time = isset($data['product_estimated_time']) ? $data['product_estimated_time'] : input::get('p_estimated_time') * 1;

    }


    function validate_field($data)
    {
    	global $CMS;
    	// print "<pre>";
    	// print_r($data);exit;
    	$arr_default = $this->form_fields[ezy::$theme_key];
    	$output=[];
    	$arr_key = array("wards_search", "street_search", "tra_time_start", "tra_time_end");

        // LHL-2018-06-06: Fix error Invalid argument supplied for foreach()
        if ( count($arr_default) == 0 ) {
            return false;
        }

    	foreach ($arr_default as $key) {
    		if(isset($data[$key]) or in_array($key, $arr_key))
    		{
    			if(is_array($data[$key]))
    			{
    				$output[$key] = array_values($data[$key]);
    			}else
    			{
    				// wards, street search
	    			if($key == "wards_search")
	    			{
	    				$output[$key] = $CMS->class->seo->remove_vietnamese($data['wards']);
	    			}elseif($key == "street_search")
	    			{
	    				$output[$key] = $CMS->class->seo->remove_vietnamese($data['street']);
	    			}elseif($key == "tra_time_start" or $key == "tra_time_end")
	    			{
	    				$output[$key] = $CMS->class->date->date2time($data[$key]);
	    			}else
	    			{
    					$output[$key] = $data[$key];
	    			}
    			}
    		}
    	}

    	// change price realestate
    	// if($data['p_up'] == "billion" or $data['p_up'] == "billion_m2" or $data['p_up'] == "billion_apartment")
    	// {
    	// 	$data['p_price_sell'] = $data['p_price_show'] * 1000000000;
    	// }elseif($data['p_up'] == "million" or $data['p_up'] == "million_m2" or $data['p_up'] == "million_apartment" or $data['p_up'] == "million_month")
    	// {
    	// 	$data['p_price_sell'] = $data['p_price_show'] * 1000000;
    	// }else
    	// {
    	// 	$data['p_price_sell'] = $data['p_price_show'];
    	// }
    	
    	return $output;
    }

    function unlink_img($link_image="", $product_id=0)
    {
    	global $CMS, $DB;

    	if($product_id)
    	{
    		$gallery = $this->get_info($product_id, "product_gallery");
    		$gallery = json_decode($gallery, 1);
    		$list = [];
    		foreach ($gallery as $key => $value) {
    			if($link_image == $value)
    			{
    				unlink("{$CMS->vars['upload_dir']}/{$link_image}");
    			}else
    			{
    				$list[] = $value;
    			}
    		}

    		$list_gallery = json_encode($list);
    		// Update database
    		$check = $DB->query("UPDATE ".root_table."product SET product_gallery='{$list_gallery}' WHERE product_id='{$product_id}'");

            //Clear all cache
            $CMS->class->cache->mdelete($this->cache_prefix);

    		if($check)
    		{
    			return 1;
    		}else
    		{
    			return 0;
    		}
    	}
    }

    function resetParentId($product_id=0, $parent_id=0)
    {
    	global $CMS, $DB;

    	$DB->query("UPDATE ".root_table."product SET parent_id=0 WHERE product_id='{$product_id}' AND parent_id='{$parent_id}'");

        //Clear all cache
        $CMS->class->cache->mdelete($this->cache_prefix);

    	return;
    }

    function getProductChild($parent_id=0)
    {
    	global $CMS, $DB;

    	$output = [];
    	if($parent_id)
    	{
    	    $sql = "SELECT * FROM ".root_table."product WHERE parent_id='{$parent_id}' AND product_deleted=0";

    	    $cacheData = $DB->fetch_data($sql, $this->cache_prefix);

            if($cacheData)
            {
                foreach ($cacheData as $result)
                {
                    $output[] = $this->convertvalue($result);
                }
            }
    	}

    	return $output;
    }


    /**
     * @param $value
     * @param $type
     * @return string
     * nkvp - 2017.12.04
     */
    function commission_format($value, $type)
    {
        global $CMS;
        $result = $type ? $CMS->class->input->currency($value) : number_format($value,2).'%';
        return $result;
    }

    /**
     * Danh sách huê hồng dịch vụ
     * @return mixed
     * nkvp - 2017.12.04
     */
    public function commission_listing($product_type = -1)
    {
        global $CMS, $DB;

        if($product_type != -1)
        {
            $sql_add = "AND product_type={$product_type}";
        }

        $sql = "SELECT product_id, product_name, product_commission_type,product_commission_value, product_type FROM ".root_table."product WHERE product_deleted=0  {$sql_add}  ORDER BY product_id desc";

        $results = $DB->fetch_data($sql, $this->cache_prefix);

        return $results;
    }

    public function update_commission($data)
    {
        global $CMS, $DB;

        if(!$CMS->vars['enabled_commission']) return false;

        //Clear all cache
        $CMS->class->cache->mdelete($this->cache_prefix);

        $_SESSION['msg'] = isset($_SESSION['msg']) ? $_SESSION['msg'] : '';

        $data = input::isJson($data) ?  input::jsonDecode($data) : $data;

        if(!is_array($data) || empty($data)) return false;

        foreach($data['product_commission_type'] as $id => $type)
        {
            $value = isset($data['product_commission_value'][$id]) ? $data['product_commission_value'][$id] * 1 : 0;
            $sql = "UPDATE ".root_table."product SET product_commission_type={$type}, product_commission_value={$value} WHERE product_id={$id};";

            if($DB->query($sql))
            {
                $_SESSION['msg'] .= "{$CMS->lang['update_commission_success']} {$CMS->lang['product']}/{$CMS->lang['service']} #{$id}<br />";
            }
            else
            {
                $_SESSION['msg'] .= "{$CMS->lang['update_commission_failed']} {$CMS->lang['product']}/{$CMS->lang['service']} #{$id}<br />";
            }
        }
    }

    function listingTplData(){
        global $tpl, $CMS, $DB;

        $tpl->data = $CMS->product->listing();

        $tpl->p_id_search = isset($CMS->input['p_id']) ? $CMS->input['p_id'] : '';
        $tpl->p_type_search = isset($CMS->input['p_type']) ? $CMS->input['p_type'] : '';
        $tpl->p_name_search = isset($CMS->input['p_name']) ? $CMS->input['p_name'] : '';

        // Check type
        if (isset($CMS->input['p_type']) AND $CMS->input['p_type'] == 0) {
            $tpl->active_0 = " active ";
            $tpl->active_all = " ";
            $tpl->active_1 = " ";
        } elseif (isset($CMS->input['p_type']) AND $CMS->input['p_type'] == 1) {
            $tpl->active_1 = " active ";
            $tpl->active_all = " ";
            $tpl->active_0 = " ";
        } else {
            $tpl->active_1 = " ";
            $tpl->active_all = " active ";
            $tpl->active_0 = " ";
        }


        $tpl->p_statup_search = isset($CMS->input['p_status']) ? $CMS->input['p_status'] : '';
        $tpl->p_group_search = isset($CMS->input['p_group']) ? $CMS->input['p_group'] : '';
        $tpl->p_manufacture_search = isset($CMS->input['p_manufacture']) ? $CMS->input['p_manufacture'] : '';

        $tpl->p_supplier_search = isset($CMS->input['p_supplier']) ? $CMS->input['p_supplier'] : '';

        $tpl->option_p_supplier = "<option value=''>{$CMS->lang['select_supplier']}</option>";
        $tpl->supplier = $CMS->supplier->get_list_supplier(1);

        foreach ($tpl->supplier as $s) {

            if ($s['supplier_id'] == $tpl->p_supplier_search AND $tpl->p_supplier_search != "") {
                $tpl->option_p_supplier .= "<option value='{$s['supplier_id']}' selected>{$s['supplier_name']}</option>";
            } else {

                $tpl->option_p_supplier .= "<option value='{$s['supplier_id']}'>{$s['supplier_name']}</option>";
            }
        }


        $tpl->option_p_type_search = "<option value=''>{$CMS->lang['select']}</option>";
        for ($i = 0; $i <= 1; $i++) {
            if ($i == $tpl->p_type_search && $tpl->p_type_search != '') {
                $tpl->option_p_type_search .= "<option value='{$i}' selected>{$CMS->lang['p_type_0'.$i]}</option>";
            } else {
                $tpl->option_p_type_search .= "<option value='{$i}'>{$CMS->lang['p_type_0'.$i]}</option>";
            }
        }
        $tpl->option_p_statup_search = "<option value=''>-- {$CMS->lang['p_status']} --</option>";
        for ($i = 0; $i <= 2; $i++) {
            if ($i == $tpl->p_statup_search && $tpl->p_statup_search != '') {
                $tpl->option_p_statup_search .= "<option value='{$i}' selected>{$CMS->lang['p_statup_0'.$i]}</option>";
            } else {
                $tpl->option_p_statup_search .= "<option value='{$i}'>{$CMS->lang['p_statup_0'.$i]}</option>";
            }
        }


        $tpl->option_p_group_search = "<option value=''>-- {$CMS->lang['p_product_group']} --</option>";
        $tpl->group = $CMS->product_group->getAllFull($CMS->input['p_type'], 1);

        // Level 1
        foreach ($tpl->group as $g) 
        {
            $selected = $tpl->p_group_search == $g['product_group_id'] ? 'selected' : "";
            $tpl->option_p_group_search .= "<option value='{$g['product_group_id']}' {$selected}>{$g['product_group_name']}</option>";

            // Level 2
            if (count($g['data_item']) > 0) 
            {
                foreach ($g['data_item'] as $g2) 
                {
                    $selected2 = $tpl->p_group_search == $g2['product_group_id'] ? 'selected' : "";
                    $tpl->option_p_group_search .= "<option value='{$g2['product_group_id']}' {$selected2}> |__{$g2['product_group_name']}</option>";

                    // Level 3
                    if (count($g2['data_item']) > 0) 
                    {
                        foreach ($g2['data_item'] as $g3) 
                        {
                            $selected3 = $tpl->p_group_search == $g3['product_group_id'] ? 'selected' : "";
                            $tpl->option_p_group_search .= "<option value='{$g3['product_group_id']}' {$selected3}> &nbsp;&nbsp;&nbsp;|__{$g3['product_group_name']}</option>";

                            // Level 4
                            if (count($g3['data_item']) > 0) 
                            {
                                foreach ($g3['data_item'] as $g4) 
                                {
                                    $selected4 = $tpl->p_group_search == $g4['product_group_id'] ? 'selected' : "";
                                    $tpl->option_p_group_search .= "<option value='{$g4['product_group_id']}' {$selected4}> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|__{$g4['product_group_name']}</option>";
                                }
                            }
                        }
                    }
                }
            }
        }

        $tpl->option_p_manufacture_search = "<option value=''>--{$CMS->lang['p_manufacture']}--</option>";
        $tpl->manufacture = $CMS->manufacture->getAll();
        foreach ($tpl->manufacture as $m) {
            if ($m['manufacture_id'] == $tpl->p_manufacture_search && $tpl->p_manufacture_search != '') {
                $tpl->option_p_manufacture_search .= "<option value='{$m['manufacture_id']}' selected>{$m['manufacture_name']}</option>";
            } else {
                $tpl->option_p_manufacture_search .= "<option value='{$m['manufacture_id']}'>{$m['manufacture_name']}</option>";
            }
        }

        $tpl->p_name_convert = urldecode($CMS->input['p_name']);

        $tpl->export_type = $CMS->input['site'] == 'product' ? 0 : 1;

        $tpl->curTab = "";

        if ($CMS->input['site'] == "product_group") {
            $tpl->curTab = 'product_group';
        } else if (($CMS->input['commission'] == 1)) {
            $tpl->curTab = 'commission';
        } else {
            if ($tpl->p_type_search == 1) {
                $tpl->curTab = 'service';
            } else {
                $tpl->curTab = 'product';
            }
        }

        $tpl->tab_active[$tpl->curTab] = 'active';

        // cnt for list
        $tpl->listCountProductOfGroup = $this->countProduct($tpl->p_type_search);
    	$tpl->totalProduct = $this->countProductByType($tpl->p_type_search);
    	$tpl->totalGroup = $CMS->product_group->countProductGroupByType($tpl->p_type_search);
    	$tpl->totalCommission = $this->countCommissionByType($tpl->p_type_search);
    }

    function autoCompleteData()
    {
        global $CMS, $DB;

        $sql = "SELECT product_name, product_name_lang FROM ".root_table."product WHERE product_deleted=0 ORDER BY product_id DESC";

        $data = $DB->fetch_data($sql, $this->cache_prefix);

        $return = [];
        foreach($data as $item)
        {
            $item = $CMS->product->convertvalue($item);

            if(is_array($item['product_name'])) //multi lang
            {
                foreach($item['product_name'] as $lang => $name)
                {
                    $name = str_replace('&#39;',"'",$name);
                    $name = html_entity_decode($name);
                    $name = htmlspecialchars_decode($name);
                    $return[$lang][] = $name;
                }
            }
            else
            {
                $name =  str_replace('&#39;',"'",$item['product_name']);
                $name = html_entity_decode($name);
                $name = htmlspecialchars_decode($name);
                $return[] = $name;
            }
        }

        return $return;
    }

    /**
     * Get List Group Id
     * @return array
     */
    function getListGroupChildId( $record_id = 0, $loop = 0, $output = [] )
    {
        global $DB, $CMS;

        // Load Data
        $output = $output ? $output : [];

        // Max loop
        $loop = intval($loop) + 1;

        if( $record_id > 0 AND $loop <= 5 )
        {
            $sqlString = "
            SELECT product_group_id 
            FROM ".root_table."product_group 
            WHERE product_group_deleted = 0 AND product_group_parent = '{$record_id}' 
            ";

            $sqlQuery = $DB->query($sqlString);

            while ( $data = $DB->fetch_array($sqlQuery) )
            {
                $output[$data['product_group_id']] = $data['product_group_id'];
                $output = $this->getListGroupChildId($data['product_group_id'], $loop, $output);
            }
        }

        return $output;
    }

    public function searchProduct($key="")
    {
    	global $CMS, $DB;

    	$output = [];
    	if($key)
    	{
    		$result = $DB->fetch_data("SELECT * FROM ".root_table."product WHERE product_deleted=0 AND (product_name LIKE '%{$key}%' OR product_name_lang LIKE '%{$key}%') ORDER BY product_name ASC");
    		foreach ($result as $data) 
    		{
    			$data = $this->convertNameProduct($data);
    			$output[] = $data;
    		}
    	}

    	return json_encode($output, JSON_UNESCAPED_UNICODE);
    }


    public function convertNameProduct($data=[])
    {
    	global $CMS, $DB;

    	if($CMS->vars['translations'])
        {
        	$name = @json_decode($data['product_name_lang'], true);
	        $data['product_name'] = $name ? $name : $data['product_name'];

	        $shorturl = @json_decode($data['product_shorturl_lang'], true);
	        $data['product_shorturl'] = $shorturl ? $shorturl : $data['product_shorturl'];
	// print "<pre>";print_r($data['news_description']);exit;
	        $description = @json_decode($data['product_description_lang'], true);
	        $data['product_description'] = $description ? $description : $data['product_description_lang'];

	        $information_1 = @json_decode($data['product_information_1_lang'], true);
	        $data['product_information_1'] = $information_1 ? $information_1 : $data['product_information_1'];

	        $information_2 = @json_decode($data['product_information_2_lang'], true);
	        $data['product_information_2'] = $information_2 ? $information_2 : $data['product_information_2'];

            //Đa ngôn ngữ
            if(!is_array($data['product_shorturl']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $shorturl = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    $shorturl[$langCode] = $data['product_shorturl'];
                }

                $data['product_shorturl'] = $shorturl;
            }

            if(!is_array($data['product_name']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $name = [];
                $name_bk = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                	$name[$langCode] = $data['product_name'];
	            	// Check permission to read Info
					if ( $CMS->permit["product_read"] == true )
					{
						$name_bk[$langCode] = "<a href='{$CMS->vars['root_domain']}/?site=product&act=show&id={$data['product_id']}'>{$data['product_name']}</a>";
						
					}

                }

                $data['product_name_bk'] = $name_bk;
                $data['product_name'] = $name;
            }else
            {
            	$data['product_name_bk'] = $data['product_name'];

            	$name_bk = [];
            	// Check permission to read Info
				if ( $CMS->permit["product_read"])
				{
					foreach ($data['product_name_bk'] as $langCode => $product_name)
	                {
						$name_bk[$langCode] = "<a href='{$CMS->vars['root_domain']}/?site=news&act=show&id={$data['product_id']}'>{$product_name}</a>";
					}
					$data['product_name_bk'] = $name_bk;
				}

            }

            if(!is_array($data['product_description']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $description = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    $description[$langCode] = $data['product_description'];
                }
                $data['product_description'] = $description;
            }
            
            if(!is_array($data['product_information_1']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $content = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    $content[$langCode] = $data['product_information_1'];
                }

                $data['product_information_1'] = $content;
            }

            if(!is_array($data['product_information_2']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                $content = [];
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    $content[$langCode] = $data['product_information_2'];
                }

                $data['product_information_2'] = $content;
            }

            // Convert theo ngôn ngữ mặc định
            $data['product_name_show'] = $data['product_name'][$CMS->vars['default_language']];
            $data['product_shorturl_show'] = $data['product_shorturl'][$CMS->vars['default_language']];
            $data['product_description_show'] = $data['product_description'][$CMS->vars['default_language']];
            $data['product_information_1_show'] = $data['product_information_1'][$CMS->vars['default_language']];
            $data['product_information_2_show'] = $data['product_information_2'][$CMS->vars['default_language']];

        } else {

        	if(is_array($data['product_shorturl']))
            {
            	$data['product_shorturl'] = $data['product_shorturl'][$CMS->vars['default_language']];
        	}

        	if(is_array($data['product_description']))
            {
            	$data['product_description'] = $data['product_description'][$CMS->vars['default_language']];
        	}

        	if(is_array($data['product_information_1']))
            {
            	$data['product_information_1'] = $data['product_information_1'][$CMS->vars['default_language']];
        	}

        	if(is_array($data['product_information_2']))
            {
            	$data['product_information_2'] = $data['product_information_2'][$CMS->vars['default_language']];
        	}

            if(is_array($data['product_name']))
            {
                //Neu la dang mang thi chuyen ve dang chuoi binh thuong
                $data['product_name'] = $data['product_name'][$CMS->vars['default_language']];

                // Check permission to read Info
				if ( $CMS->permit["product_read"] == true )
				{
					$data['product_name_bk'] = "<a class='link' href='{$CMS->vars['root_domain']}/?site=product&act=show&id={$data['product_id']}'>{$data['product_name']}</a>";
				}

            }else
            {
            	$data['product_name_bk'] = $data['product_name'];
            	if ( $CMS->permit["product_read"] == true )
				{
					$data['product_name_bk'] = "<a class='link' href='{$CMS->vars['root_domain']}/?site=product&act=show&id={$data['product_id']}'>{$data['product_name']}</a>";
				}
            }
        }
       
    	return $data;
    }

    function assign_to_store()
    {
    	global $CMS, $DB;

    	$store_id = intval($CMS->input['store_id']);
    	$_SESSION["msg"] .= "";
    	$_SESSION["error_msg"] .= "";

    	if( ! $store_id )
    	{
    		$_SESSION["error_msg"] .= "{$CMS->lang['product_assign_to_store_failure']}<br />";
    		return false;
    	}

    	for ( $i = 0; $i < intval( $CMS->input["data_cnt"] ); $i++ )
		{
			$id = intval( $CMS->input["id_{$i}"] );
				
			if ( $id )
			{
        		$DB->query("UPDATE ".root_table."product SET store_id='{$store_id}' WHERE product_id='{$id}'"); 
	        }
		}

        //Clear all cache
        $CMS->class->cache->mdelete($this->cache_prefix);

        $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['product_assign_to_store_success']}")."<br />";

		return true;
    }

    function unassign_store()
    {
    	global $CMS, $DB;

    	$_SESSION["msg"] .= "";
    	$_SESSION["error_msg"] .= "";

		for ( $i = 0; $i < intval( $CMS->input["data_cnt"] ); $i++ )
		{
			$id = intval( $CMS->input["id_{$i}"] );
				
			if ( $id )
			{
        		$DB->query("UPDATE ".root_table."product SET store_id='0' WHERE product_id='{$id}'"); 
	        }
		}
		
        //Clear all cache
        $CMS->class->cache->mdelete($this->cache_prefix);

        $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['product_unassign_store_success']}")."<br />";

		return true;
    }

    function countProduct( $type = '' )
    {
    	global $CMS, $DB;

    	$output = [];

    	$clause = '';
    	if( is_numeric($type) )
    	{
    		$clause = " AND product_group_type='{$type}' ";
    	}

    	$sql = "
    	SELECT PG.product_group_id, PG.product_group_name 
    	FROM ".root_table."product_group AS PG 
    	WHERE PG.product_group_deleted = 0 AND PG.product_group_parent = 0 {$clause} 
    	";

    	$groups = $DB->fetch_data($sql, $this->cache_prefix);
    	if( is_array($groups) )
    	{
    		foreach ( $groups as $group ) 
    		{
    			$group['cnt'] = $this->countProductByGroup($group['product_group_id'], $type);
    			$output[$group['product_group_id']] = $group;
    		}
    	}

    	return $output;
    }

    function countProductByGroup( $group = '', $type = '' )
    {
    	global $CMS, $DB;

    	$output = 0;

    	$clause = '';
    	if( $group )
    	{
    		$clause = " AND P.product_group IN ( -1 ";
    		$child_ids = $this->getListGroupChildId($group, 0, [$group=>$group]);
    		foreach( $child_ids as $child_id )
    		{
    			if( $child_id )
    			{
    				$clause .= " , '{$child_id}' ";
    			}
    		}
    		$clause .= " ) ";
    	}

    	if( is_numeric($type) )
    	{
    		$clause .= " AND P.product_type='{$type}' ";
    	}

    	$sql = "
    	SELECT COUNT(P.product_id) AS cnt  
    	FROM ".root_table."product AS P 
    	WHERE P.product_deleted = 0 {$clause} 
    	";

    	$result = $DB->fetch_data($sql, $this->cache_prefix);
    	if( is_array($result) AND isset($result[0]['cnt']))
    	{
    		$output = $result[0]['cnt']*1;
    	}

    	return $output;
    }

    function countProductByType($type = '')
    {
    	global $CMS, $DB;

    	$output = 0;

    	$clause = '';
    	if( is_numeric($type) )
    	{
    		$clause = " AND P.product_type='{$type}' ";
    	}

    	$sql = "
    	SELECT COUNT(P.product_id) AS cnt  
    	FROM ".root_table."product AS P 
    	WHERE P.product_deleted = 0 {$clause} 
    	";

    	$result = $DB->fetch_data($sql, $this->cache_prefix);
    	if( is_array($result) AND isset($result[0]['cnt']))
    	{
    		$output = $result[0]['cnt']*1;
    	}

    	return $output;
    }

    public function countCommissionByType($type = '')
    {
        global $CMS, $DB;

        $output = 0;

    	$clause = '';
    	if( is_numeric($type) )
    	{
    		$clause = " AND P.product_type='{$type}' ";
    	}

    	$sql = "
    	SELECT COUNT(P.product_id) AS cnt  
    	FROM ".root_table."product AS P 
    	WHERE P.product_deleted = 0 {$clause} 
    	";
    	
    	$result = $DB->fetch_data($sql, $this->cache_prefix);
    	if( is_array($result) AND isset($result[0]['cnt']))
    	{
    		$output = $result[0]['cnt']*1;
    	}

    	return $output;
    }

    /**
    * general Variants save json to product
    */
    public function generalVariants( $product_id = 0 )
    {
    	global $CMS, $DB;

    	// get variants of product
    	$sql_variants = "
    	SELECT var_id, product_id, var_title, var_price, var_sku, var_option1, var_option2, var_option3 
    	FROM ".root_table."variants AS V 
    	WHERE V.var_deleted = 0 AND V.product_id = '{$product_id}' 
    	";
    	// print $sql_variants; exit;

  		$results = $DB->fetch_data($sql_variants, 'variants.product');
  		
  		// json variants
  		$var_content = \lib\input::jsonEncode($results, 0);
  		$var_content = addslashes($var_content);

  		// save variants to product
  		$sql_save = "
  		UPDATE ".root_table."product 
  		SET var_content = '{$var_content}' 
  		WHERE product_id = '{$product_id}' 
  		";
  		// print $sql_save; exit;

  		$query_save = $DB->query($sql_save);

  		return $query_save ? true : false;
    }
}
?>