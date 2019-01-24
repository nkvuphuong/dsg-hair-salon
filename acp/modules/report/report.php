<?php

namespace controller;

use core\ezy;
use lib\input;
use lib\language;
use models\dashboard;

//Load models
ezy::load_model("report");
ezy::load_model("product_group");

new report;

class report {

	public $html;

	public function __construct()
	{
            global $CMS, $DB, $tpl;
            
            // Title
            $CMS->core->page_title = "-> {$CMS->lang['report_header']}";
            
            // Load product category
            $tpl->ListProductGroup = $CMS->product_group->getMultiOptionCategory(1,0,0,0,1);
            
            // Load List Store
            $tpl->ListStore = $CMS->store->get_list_store("",1,'',1);
            
            // Check for active link menu
            $tpl->menu_active = $CMS->input['act'];
            $tpl->submenu_active = \lib\input::get('subact');
          
            // Main switch
            switch ( $CMS->input['act'] )
            {
                        case "sales":
				self::report_sales();
			break;
			case "order":
				self::report_order();
			break;
			case "product":
				self::report_product();
			break;
            case "assets":
                    self::report_assets();
            break;
			case "customer":
				self::report_customer();
			break;
			case "finance":
				self::report_finance();
			break;
			case "order":
				self::report_order();
			break;
            case "inventory":
                    self::report_inventory();
            break;
                default:
                // self::pageDefault();
                if(\lib\input::get('subact') == "getTime")
				{
					self::getTime();
				}elseif(\lib\input::get('subact') == "getReport")
				{
					self::getReport();
				}elseif(\lib\input::get('subact') == "export")
				{
					self::export();
				}
                elseif(\lib\input::get('subact') == "product_quick_search")
				{
                        \models\report::product_quick_search();
				}
                elseif(\lib\input::get('subact') == "assets_quick_search")
				{
                        \models\report::assets_quick_search();
				}
                elseif(\lib\input::get('subact') == "search_type")
				{
                        \models\report::search_type();
				}
                break;
            }

                    echo ezy::html();
            }

    static function report_sales()
    {
    	global $CMS, $tpl;
    	$CMS->core->page_title = "-> {$CMS->lang['title_report_sales']}";
    	$tpl->page_view = "sales";
        $tpl->page_subact = \lib\input::get('subact') ? \lib\input::get('subact') : "date";
    	$tpl->title_report = $CMS->lang['title_report_sales'];
        $tpl->action_report = \lib\input::get('subact') ? "report_sales_".\lib\input::get('subact') : "report_sales_date";
        
        // Check subact for detail title
        if(\lib\input::get('subact') == "date")
        {
            // Convert to detail : day, month or year
            $CMS->input['view_type'] = isset($CMS->input['view_type']) ? $CMS->input['view_type'] : "view_day";
            $tpl->detail_title = $CMS->lang['title_menu_sales']." (".($CMS->lang["detail_title_{$CMS->input['view_type']}"]).")";
        }
        else // Other subact
        {
            $tpl->detail_title = $CMS->lang['title_menu_sales']." (".$CMS->lang["title_menu_sales_{$tpl->page_subact}"].")";
        }
        
        if(in_array(\lib\input::get('subact'),array("product","assets")))
        {
            // Load list User
            $CMS->user->load_list(1,0,1);
            $tpl->listUser = $CMS->user->list_html;

            // Load list Supplier
            $tpl->listSupp = $CMS->supplier->get_list_supplier(0,0,1);
        }
        else if(in_array(\lib\input::get('subact'),array("supplier")))
        {
            // Load list Supplier
            $tpl->listSupp = $CMS->supplier->get_list_supplier();
        }
    }

    static function report_order()
    {
    	global $CMS, $tpl;
    	$CMS->core->page_title = "-> {$CMS->lang['title_report_order']}";
    	$tpl->page_view = "order";
        $tpl->page_subact = \lib\input::get('subact') ? \lib\input::get('subact') : "date";
    	$tpl->title_report = $CMS->lang['title_report_order'];
        $tpl->action_report = \lib\input::get('subact') ? "report_order_".\lib\input::get('subact') : "report_order_date";
        
        $tpl->detail_title = $CMS->lang['title_menu_order']." (".$CMS->lang["title_menu_order_{$tpl->page_subact}"].")";
    }
    
    static function report_inventory()
    {
    	global $CMS, $tpl;
    	$CMS->core->page_title = "-> {$CMS->lang['title_report_inventory']}";
    	$tpl->page_view = "inventory";
        $tpl->page_subact = \lib\input::get('subact') ? \lib\input::get('subact') : "quantity";
    	$tpl->title_report = $CMS->lang['title_report_inventory'];
        $tpl->action_report = \lib\input::get('subact') ? "report_inventory_".\lib\input::get('subact') : "report_inventory_quantity";
        
        $tpl->detail_title = $CMS->lang['title_menu_inventory']." (".$CMS->lang["title_menu_inventory_{$tpl->page_subact}"].")";
    }

    static function report_product()
    {
        global $CMS, $tpl;
        
        $CMS->core->page_title = "-> {$CMS->lang['title_report_product']}";
        $tpl->page_view = "product";
        $tpl->page_subact = \lib\input::get('subact') ? \lib\input::get('subact') : "date";
    	$tpl->title_report = $CMS->lang['title_report_order'];
        $tpl->action_report = \lib\input::get('subact') ? "report_product_".\lib\input::get('subact') : "report_product_date";
        
        $tpl->detail_title = $CMS->lang['title_menu_product']." (".$CMS->lang["title_menu_product_{$tpl->page_subact}"].")";
        
        if(in_array(\lib\input::get('subact'),array("bestseller","date","store")))
        {
            // Load list Supplier
            $tpl->listSupp = $CMS->supplier->get_list_supplier(0,0,1);
            
            // Load lang product
            $CMS->class->language->load("product");
            
            // Load list product status
            $tpl->p_status = $CMS->product->list_product_status();

        }
    }

    static function report_assets()
    {
        global $CMS, $tpl;
        
        $CMS->core->page_title = "-> {$CMS->lang['title_report_assets']}";
        $tpl->page_view = "assets";
        $tpl->page_subact = \lib\input::get('subact') ? \lib\input::get('subact') : "date";
    	$tpl->title_report = $CMS->lang['title_report_assets'];
        $tpl->action_report = \lib\input::get('subact') ? "report_assets_".\lib\input::get('subact') : "report_assets_date";
        
        $tpl->detail_title = $CMS->lang['title_menu_assets']." (".$CMS->lang["title_menu_assets_{$tpl->page_subact}"].")";
        
        if(in_array(\lib\input::get('subact'),array("bestseller","date","store")))
        {
            // Load list Supplier
            $tpl->listSupp = $CMS->supplier->get_list_supplier(0,0,1);
            
            // Load lang product
            $CMS->class->language->load("assets");
        }
    }
    
	static function report_customer()
	{
            global $CMS, $tpl;
            
            $CMS->core->page_title = "-> {$CMS->lang['title_report_customers']}";
            $tpl->page_view = "customers";
            $tpl->page_subact = \lib\input::get('subact') ? \lib\input::get('subact') : "overview";
            $tpl->title_report = $CMS->lang['title_report_customers'];
            $tpl->action_report = \lib\input::get('subact') ? "report_customer_".\lib\input::get('subact') : "report_customer_overview";
            
            $tpl->detail_title = $CMS->lang['title_menu_customer']." (".$CMS->lang["title_menu_customer_{$tpl->page_subact}"].")";
            
            // Load City
            if(in_array($tpl->page_subact,array("overview","sales")))
            {
                // Load list City
                $tpl->CityList = $CMS->country->getOptionCity(238,0,1); // Default is VN
            }
            
            // Load District
            if(in_array($tpl->page_subact,array("sales")))
            {
                // Load list City
                $tpl->DistrictList = $CMS->country->getOptionDistrict(4121,0,1); // Default is HN
            }
            
            // Load supplier
            if(in_array($tpl->page_subact,array("product","assets")))
            {
                // Load list Supplier
                $tpl->listSupp = $CMS->supplier->get_list_supplier(0,0,1);
            }	
        }

	static function report_finance()
	{
            global $CMS, $tpl;
            
            $CMS->core->page_title = "-> {$CMS->lang['title_report_finance']}";
            $tpl->page_view = "finance";
            $tpl->page_subact = \lib\input::get('subact') ? \lib\input::get('subact') : "daily";
            $tpl->title_report = $CMS->lang['title_report_finance'];
            $tpl->action_report = \lib\input::get('subact') ? "report_finance_".\lib\input::get('subact') : "report_finance_daily";
            
            $tpl->detail_title = $CMS->lang['title_menu_finance']." (".$CMS->lang["title_menu_finance_{$tpl->page_subact}"].")";
            
            // Load City
            if(in_array(\lib\input::get('subact'),array("overview","sales")))
            {
                // Load list City
                $tpl->CityList = $CMS->country->getOptionCity(238,0,1); // Default is VN

            }
            
            // Load District
            if(in_array(\lib\input::get('subact'),array("sales")))
            {
                // Load list City
                $tpl->DistrictList = $CMS->country->getOptionDistrict(4121,0,1); // Default is HN

            }
            
            // Load supplier
            if(in_array(\lib\input::get('subact'),array("product")))
            {
                // Load list Supplier
                $tpl->listSupp = $CMS->supplier->get_list_supplier();
            }	
	}

    static function getTime()
    {
    	global $CMS, $tpl;

    	$type = $CMS->input['type'];

    	list($time_from, $time_to) = input::getTimeString($type,0);
        
        $format_date = $CMS->vars['dateformat_php'][$CMS->vars['date_format']];
       
    	print json_encode(array("time_from" => date($format_date, $time_from), "time_to" => date($format_date, $time_to)));
    	exit;
    }

    static function getReport()
    {
    	global $CMS, $tpl;

    	$page_type = $CMS->input['page'];
        $subact = $CMS->input['sub'];
    	$time_from = $CMS->class->date->date2time($CMS->input['time_from']);
    	$time_to = $CMS->class->date->date2time($CMS->input['time_to']) + 24*3600; // Over 24h of end day
    	$view_type = $CMS->input['view_type'];
    	$type_status = $CMS->input['type_status'];// for order
        $product = trim($CMS->input['product']);
        $price_from = intval($CMS->input['price_from']);
        $price_to = intval($CMS->input['price_to']);
        $supplier = $CMS->input['supplier'];
        $user = $CMS->input['user'];
        $ord_status = $CMS->input['ord_status'];
        $p_status = $CMS->input['p_status'];
        $price_type = $CMS->input['price_type'];
        $p_group = $CMS->input['p_group']; // Get product group
        $store_id = $CMS->input['store_id']; // Get store id
        $range = $CMS->input['range']; // Range price
        $times = $CMS->input['buy_times']; // Get buy times
        $city = $CMS->input['city']; // Get city
        $district = $CMS->input['district']; // Get district
        $customer = $CMS->input['customer']; // Get customer name
        $cus_type = $CMS->input['cus_type']; // Customer type in transaction. Ex: customer, provider....
        $object = $CMS->input['object']; // Data from select cus type
        $ass_avaiable = $CMS->input['ass_avaiable']; // Ass is avaiable
        $ass_name = $CMS->input['ass_name'];
        $ass_id_data = $CMS->input['ass_id_data'];
        $user_id = $CMS->input['user_id'];
      
        //==================================
        // Report Sales
        //==================================
    	if($page_type == "sales")
    	{
            // Static follow date
            if($subact == "date")
            {
                list($data_chart, $title_chart, $output_report) = \models\report::report_sales($time_from, $time_to, $view_type, $p_group, $store_id);
            }
            // Static follow store
            else if($subact == "store")
            {
                list($data_chart, $title_chart, $output_report) = \models\report::report_sales_store($time_from, $time_to, $view_type, $p_group, $store_id);
            }
            // Static follow product
            else if($subact == "product")
            {
                list($data_chart, $title_chart, $output_report) = \models\report::report_sales_product($time_from, $time_to, $view_type, $p_group, $store_id,$product,$price_from,$price_to,$supplier,$user);
            }
            // Static follow assets
            else if($subact == "assets")
            {
                list($data_chart, $title_chart, $output_report) = \models\report::report_sales_assets($time_from, $time_to, $view_type, $p_group, $store_id,$ass_name,$price_from,$price_to,$supplier,$user);
            }
            // Static follow product group
            else if($subact == "pgroup")
            {
                list($data_chart, $title_chart, $output_report,$data_chart_2,$data_chart_3) = \models\report::report_sales_pgroup($time_from, $time_to, $view_type, $p_group, $store_id,$product,$price_from,$price_to,$supplier,$user);
            }
            // Static follow Supplier
            else if($subact == "supplier")
            {
                list($data_chart, $title_chart, $output_report, $days, $day_total,$link_export) = \models\report::report_sales_supplier($time_from, $time_to, $view_type, $p_group, $store_id,$product,$supplier);
            }
            
            print json_encode(array("data_chart" => $data_chart,"data_chart_2" => $data_chart_2,"data_chart_3" => $data_chart_3, "html_report" => $output_report, "title_chart" => $title_chart, "days" => $days, "day_total" => $day_total, "link_export_bk"=>$link_export));exit;
    	}
        //==================================
        // Report Order
        //==================================
        elseif($page_type == "order")
    	{
            // Static follow date
            if($subact == "date")
            {
                list($data_chart, $title_chart, $output_report) = \models\report::report_order($time_from, $time_to, $view_type, $p_group, $store_id);
            }
            // Static follow price
            else if($subact == "price")
            {
                list($data_chart, $title_chart, $output_report,$type_chart) = \models\report::report_order_price($time_from, $time_to, $view_type, $range);
            }
            // Static follow product
            else if($subact == "product")
            {
                list($data_chart, $title_chart, $output_report) = \models\report::report_order_product($time_from, $time_to, $view_type, $p_group, $product);
            }
            // Static follow assets
            else if($subact == "assets")
            {
                list($data_chart, $title_chart, $output_report) = \models\report::report_order_assets($time_from, $time_to, $view_type, $p_group, $ass_name);
            }
            // Static follow status
            else if($subact == "status")
            {
                // Load language
                list($data_chart, $title_chart, $output_report) = \models\report::report_order_status($time_from, $time_to, $view_type, $p_group, $store_id);
            }
            // Static follow User
            else if($subact == "user")
            {
                list($data_chart, $title_chart, $output_report) = \models\report::report_order_user($time_from, $time_to, $view_type, $ord_status);
            }
            
            print json_encode(array("data_chart" => $data_chart, "html_report" => $output_report, "title_chart" => $title_chart, "type_chart" =>  $type_chart));exit;
    	}
        //==================================
        // Report Products
        //==================================
        elseif($page_type == "product")
    	{
            // Static follow date
            if($subact == "date")
            {
                list($data_chart, $title_chart, $output_report) = \models\report::report_product_date($time_from, $time_to, $view_type, $store_id, $product, $p_status);
            }
            // Static follow price
            else if($subact == "price")
            {
                list($data_chart, $title_chart, $output_report,$type_chart) = \models\report::report_product_price($time_from, $time_to, $view_type, $p_group, $range, $price_type);
            }
            // Static follow product
            else if($subact == "store")
            {
                list($data_chart, $title_chart, $output_report,$list_store) =  \models\report::report_product_store($time_from, $time_to, $view_type, $store_id, $p_group,$product,$supplier);
            }
            // Static follow status
            else if($subact == "bestseller")
            {
                // Load language
                list($data_chart, $title_chart, $output_report) = \models\report::report_product_bestseller($time_from, $time_to, $view_type, $store_id, $p_group, $product, $supplier, $p_status);
            }
            
            print json_encode(array("data_chart" => $data_chart, "html_report" => $output_report, "title_chart" => $title_chart, "type_chart" => $type_chart, "list_store" => $list_store));exit;
    	}
        
        //==================================
        // Report Assets
        //==================================
        elseif($page_type == "assets")
    	{
            // Static follow date
            if($subact == "date")
            {
                list($data_chart, $title_chart, $output_report) = \models\report::report_assets_date($time_from, $time_to, $view_type, $store_id, $ass_name, $ass_id_data, $ass_avaiable);
            }
            // Static follow price
            else if($subact == "price")
            {
                list($data_chart, $title_chart, $output_report,$type_chart) = \models\report::report_assets_price($time_from, $time_to, $view_type, $p_group, $range, $price_type);
            }
            // Static follow product
            else if($subact == "store")
            {
                list($data_chart, $title_chart, $output_report,$list_store) =  \models\report::report_assets_store($time_from, $time_to, $view_type, $store_id, $p_group,$ass_name,$ass_id_data,$supplier);
            }
            // Static follow status
            else if($subact == "bestseller")
            {
                // Load language
                list($data_chart, $title_chart, $output_report) = \models\report::report_assets_bestseller($time_from, $time_to, $view_type, $store_id, $p_group, $ass_name, $ass_id_data, $supplier, $ass_avaiable);
            }
            
            print json_encode(array("data_chart" => $data_chart, "html_report" => $output_report, "title_chart" => $title_chart, "type_chart" => $type_chart, "list_store" => $list_store));exit;
    	}
        
        //==================================
        // Report Customer
        //==================================
        elseif($page_type == "customers")
    	{
            // Static follow overview
            if($subact == "overview")
            {
                list($data_chart, $title_chart, $output_report,$data_chart_2,$type_chart) = \models\report::report_customer_overview($time_from, $time_to, $view_type, $city, $times);
            }
            // Static follow sales
            else if($subact == "sales")
            {
                list($data_chart, $title_chart, $output_report) = \models\report::report_customer_sales($time_from, $time_to, $view_type, $store_id, $city, $district, $customer);
            }
            // Static follow product
            else if($subact == "product")
            {
                list($data_chart, $title_chart, $output_report) = \models\report::report_customer_product($time_from, $time_to, $view_type, $store_id, $p_group,$product,$customer,$supplier);
            }
            // Static follow product
            else if($subact == "assets")
            {
                list($data_chart, $title_chart, $output_report) = \models\report::report_customer_assets($time_from, $time_to, $view_type, $store_id, $p_group,$ass_name,$customer,$supplier);
            }
            // Static follow store
            else if($subact == "store")
            {
                // Load language
                list($data_chart, $title_chart, $output_report, $list_month) = \models\report::report_customer_store($time_from, $time_to, $view_type);
            }
            
            print json_encode(array("data_chart" => $data_chart,"data_chart_2" => $data_chart_2, "html_report" => $output_report, "title_chart" => $title_chart,"type_chart" => $type_chart, "list_month" => $list_month));exit;
    	}
        //==================================
        // Report Finance
        //==================================
        elseif($page_type == "finance")
    	{
            // Static follow daily retail
            if($subact == "daily")
            {
                list($data_chart, $title_chart, $output_report) = \models\report::report_finance_daily($time_from, $time_to, $view_type, $store_id);
            }
            // Static follow record transaction
            else if($subact == "record")
            {
                list($data_chart, $title_chart, $output_report) = \models\report::report_finance_record($time_from, $time_to, $view_type, $store_id, $city, $district, $customer);
            }
            // Static follow revenue
            else if($subact == "revenue")
            {
                list($data_chart, $title_chart, $output_report, $list_date) = \models\report::report_finance_revenue($time_from, $time_to, $view_type, $store_id, $cus_type, $object);
            }
            // Static follow revenue
            else if($subact == "commission_staff")
            { 
                list($data_chart, $title_chart, $output_report, $list_date) = \models\report::report_finance_commission_staff($time_from, $time_to, $user_id,  $object);
            }
            else if($subact == "commission_service")
            {
                list($data_chart, $title_chart, $output_report) = \models\report::report_finance_commission_service($time_from, $time_to, $view_type, $p_group, $store_id,$product,$user);
            }
            print json_encode(array("data_chart" => $data_chart, "html_report" => $output_report, "title_chart" => $title_chart, "list_date" => $list_date));exit;
    	}
        //==================================
        // Report Inventory
        //==================================
        elseif($page_type == "inventory")
    	{
            // Static follow quantity
            if($subact == "quantity")
            {
                list($data_chart, $title_chart, $output_report) = \models\report::report_inventory_quantity($time_from, $time_to, $view_type, $p_group, $store_id);
            }
            // Static follow product
            else if($subact == "product")
            {
                list($data_chart, $title_chart, $output_report) = \models\report::report_inventory_product($time_from, $time_to, $view_type, $store_id, $city, $district, $customer);
            }
            // Static follow assets
            else if($subact == "assets")
            {
                list($data_chart, $title_chart, $output_report, $list_date) = \models\report::report_inventory_assets($time_from, $time_to, $view_type, $store_id, $cus_type, $object);
            }
            // Static follow product group
            else if($subact == "group")
            {
                list($data_chart, $title_chart, $output_report, $list_date) = \models\report::report_inventory_group($time_from, $time_to, $view_type, $store_id, $cus_type, $object);
            }
            // Static follow total import / export
            else if($subact == "total")
            {
                list($data_chart, $title_chart, $output_report, $list_date) = \models\report::report_inventory_total($time_from, $time_to, $view_type, $store_id, $cus_type, $object);
            }
            
            print json_encode(array("data_chart" => $data_chart, "html_report" => $output_report, "title_chart" => $title_chart, "list_date" => $list_date));exit;
    	}
    }

    static function export()
    {
    	global $CMS, $tpl;

    	$type_report = $CMS->input['type_report'];
        
        // ========== Export report sales
    	if($type_report == "ordi_assets_service")
    	{
    		$link = \models\report::export_assets_service();
    	}else if($type_report == "report_sales_store")
        {
            $link = \models\report::export_report_sales_store();
        }
        else if($type_report == "report_sales_product")
        {
            $link = \models\report::export_report_sales_product();
        }
        else if($type_report == "report_sales_assets")
        {
            $link = \models\report::export_report_sales_assets();
        }
        else if($type_report == "report_sales_pgroup")
        {
            $link = \models\report::export_report_sales_pgroup();
        }
        else if($type_report == "report_sales_supplier")
        {
            $link = \models\report::export_report_sales_supplier();
        }
        // ========== End report sales
        
        // ========== Export report order
        elseif($type_report == "order_date")
        {
            $link = \models\report::export_order_date();
        }
        elseif($type_report == "order_price")
        {
            $link = \models\report::export_order_price();
        }
        elseif($type_report == "order_product")
        {
            $link = \models\report::export_order_product();
        }
        elseif($type_report == "order_assets")
        {
            $link = \models\report::export_order_assets();
        }
        elseif($type_report == "order_status")
        {
            $link = \models\report::export_order_status();
        }
        elseif($type_report == "order_user")
        {
            $link = \models\report::export_order_user();
        }
        elseif($type_report == "order_store")
        {
            $link = \models\report::export_order_store();
        }
        // ========== End report order
        
        // ========== Export report product
        elseif($type_report == "product_bestseller")
        {
            $link = \models\report::export_product_bestseller();
        }elseif($type_report == "product_price")
        {
            $link = \models\report::export_product_price();
        }elseif($type_report == "product_date")
        {
            $link = \models\report::export_product_date();
        }elseif($type_report == "product_store")
        {
            $link = \models\report::export_product_store();
        }
        // ========== End report product
        
        // ========== Export report assets
        elseif($type_report == "assets_bestseller")
        {
            $link = \models\report::export_assets_bestseller();
        }elseif($type_report == "assets_price")
        {
            $link = \models\report::export_assets_price();
        }elseif($type_report == "assets_date")
        {
            $link = \models\report::export_assets_date();
        }elseif($type_report == "assets_store")
        {
            $link = \models\report::export_assets_store();
        }
        // ========== End report assets
        
        // ========== Export report customer
        elseif($type_report == "customer_sales")
        {
            $link = \models\report::export_customer_sales();
        }
        elseif($type_report == "customer_product")
        {
            $link = \models\report::export_customer_product();
        }
        elseif($type_report == "customer_assets")
        {
            $link = \models\report::export_customer_assets();
        }
        elseif($type_report == "customer_store")
        {
            $link = \models\report::export_customer_store();
        }
        elseif($type_report == "product_store")
        {
            $link = \models\report::export_product_store();
        }        
        // ========== End report customer
        
        // ========== Export finance
        elseif($type_report == "finance_record")
        {
            $link = \models\report::export_finance_record();
        }
        elseif($type_report == "finance_revenue")
        {
            $link = \models\report::export_finance_revenue();
        }
        elseif($type_report == "finance_commission_staff")
        {
            $link = \models\report::export_finance_commission_staff();
        }
        // ========== End finance
        
        elseif($type_report == "order_status")
        {
            $link = \models\report::export_order_status();
        }elseif($type_report == "store_product")
        {
            $link = \models\report::export_store_product();
        }elseif($type_report == "customer_time")
        {
            $link = \models\report::export_customer_time();
        }elseif($type_report == "customer_total")
        {
            $link = \models\report::export_customer_total();
        }elseif($type_report == "revenue")
        {
            $link = \models\report::export_revenue();
        }elseif($type_report == "report_cost")
        {
            $link = \models\report::export_report_cost();
        }

    	print $link;exit;

    }
    
}
	
?>