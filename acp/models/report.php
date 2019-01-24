<?php

namespace models;

use \core\ezy;
use lib\input;
use lib\db;
use \PHPExcel;
use \PHPExcel_Style_Alignment;
use \PHPExcel_Style_Color;
use \PHPExcel_Style_Fill;
use \PHPExcel_Style_Border;
use \PHPExcel_IOFactory;

class report {
    static public $col = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    static public $col_expand = "AA AB AC AD AE AF AG AH AI AJ AK AL AM AN AO AP AQ AR AS AT AU AV AW AX AY AZ";
    static public $col_expand_2 = "BA BB BC BD BE BF BG BH BI BJ BK BL BM BN BO BP BQ BR BS BT BU BV BW BX BY BZ";

    static public $dataExcel = "";
    
    static function report_sales($time_from=0, $time_to=0, $view_type = "", $p_group="", $store_id="")
    {
        global $CMS, $DB;
        
        // Asset clause
        $ass_clause = "";

        if($time_from and $time_to)
        {
            $clause = $ass_clause = " AND ordi_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = $ass_clause = " AND ordi_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = $ass_clause = " AND ordi_time <= '{$time_to}' ";
        }else
        {
            $clause = $ass_clause = "";
        }
        
        // Check for product group
        if($p_group)
        {
            $clause .= " AND product_group='{$p_group}' ";
            $ass_clause .= " AND pgroup_id='{$p_group}' ";
        }
        
        // Check for store
        if($store_id)
        {
            $clause .= " AND O.store_id='{$store_id}' ";
            $ass_clause .= " AND O.store_id='{$store_id}' ";
        }
        
        $datef = self::getTypeView($view_type);
        $arr_chart = array();
        
        if($datef)
        {
            // Thống kê cho biểu đồ group theo thời gian
            // For product
            $sql = $DB->query("SELECT  COUNT(ordi_id) as qty, DATE_FORMAT(FROM_UNIXTIME(ordi_time),'{$datef}') AS datefm, sum(ordi_total) as total_price, sum(product_price_original) as total_buy FROM ".root_table."order_item as O left join ".root_table."product as P on O.product_id=P.product_id WHERE ordi_deleted = 0 AND O.product_id > 0 {$clause} GROUP BY datefm ORDER BY ordi_time ASC");
            if($DB->num_rows($sql) > 0)
            {
                while ($result = $DB->fetch_array($sql)) 
                {
                    $arr_chart[$result['datefm']] = $result['total_price'] - $result['total_buy'];
                }
            }
            
            // For asset
            $sql = $DB->query("SELECT  count(ordi_id), DATE_FORMAT(FROM_UNIXTIME(ordi_time),'{$datef}') AS datefm, sum(ordi_total) as total_price, sum(ass_original_price) as total_buy FROM ".root_table."order_item as O left join ".root_table."assets as A on O.ass_id=A.ass_id WHERE ordi_deleted = 0 AND O.ass_id > 0 {$ass_clause} GROUP BY datefm ORDER BY ordi_time ASC");
            if($DB->num_rows($sql) > 0)
            {
                while ($result = $DB->fetch_array($sql)) 
                {
                    if(isset($arr_chart[$result['datefm']]))
                    {
                        $arr_chart[$result['datefm']] += ($result['total_price'] - $result['total_buy']);
                    }
                    else
                    {
                       $arr_chart[$result['datefm']] = $result['total_price'] - $result['total_buy']; 
                    }
                }
            }
        }

        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_time']}\", \"{$CMS->lang['title_total_sales']}\"]"; // Khai bao
        $x = 1;
        
        if(!empty($arr_chart))
        {
            foreach ($arr_chart as $key => $value) 
            {
                $chart[] = "[\"{$key}\", {$value}]";
            }
            
            $data['str_chart'] = "[".implode($chart, ',')."]"; 
        }
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_sales_follow_time']}";
        $data['title_chart']['title_unit_y'] = "";
        
        // Thống kê cho số liệu group theo tên tài sản dịch vụ
        // Case: product
        $sql = $DB->query("SELECT  COUNT(ordi_id) as qty, sum(ordi_total) as total_price, DATE_FORMAT(FROM_UNIXTIME(ordi_time),'{$datef}') AS datefm,sum(product_price_original) as total_buy FROM ".root_table."order_item as O left join ".root_table."product as P on O.product_id=P.product_id WHERE ordi_deleted = 0 AND O.product_id > 0 {$clause} GROUP BY datefm ORDER BY ordi_name ASC");
        $output_report['data_1'] = [];
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&datefm={$datef}&pgroup={$p_group}&store={$store_id}&datef={$datef}&type_report=ordi_assets_service";
        if($DB->num_rows($sql) > 0)
        {
            $i = 0;
            while ($result = $DB->fetch_array($sql)) 
            {
                $output_report['data_1'][$i]['order'] = $i+1;
                $output_report['data_1'][$i]['date'] = $result['datefm'];
                
                // Date full for action search detail list
                list($time_search_from,$time_search_to) = self::get_search_time($time_from,$time_to,$result['datefm'],$view_type);
                $output_report['data_1'][$i]['link_search'] = "/?site=order&status=2&store_id={$store_id}&time_from={$time_search_from}&time_to={$time_search_to}"; 
                // End
                
                $output_report['data_1'][$i]['total_price_bk'] = (intval($result['total_price']));
                $output_report['data_1'][$i]['total_buy_bk'] = (intval($result['total_buy']));
                $output_report['data_1'][$i]['profit_bk'] = (intval($result['total_price']) - intval($result['total_buy']));
                
                $output_report['data_1'][$i]['total_price'] = $CMS->class->input->currency(intval($result['total_price']));
                $output_report['data_1'][$i]['total_buy'] = $CMS->class->input->currency(intval($result['total_buy']));
                $output_report['data_1'][$i]['profit'] = $CMS->class->input->currency(intval($result['total_price']) - intval($result['total_buy']));
                $i++;
            }
        }
        // End product
        
        // For asset
        $ass = array();
        $sql = $DB->query("SELECT  count(ordi_id), DATE_FORMAT(FROM_UNIXTIME(ordi_time),'{$datef}') AS datefm, sum(ordi_total) as total_price, sum(ass_original_price) as total_buy FROM ".root_table."order_item as O left join ".root_table."assets as A on O.ass_id=A.ass_id WHERE ordi_deleted = 0 AND O.ass_id > 0 {$ass_clause} GROUP BY datefm ORDER BY ordi_time ASC");
        if($DB->num_rows($sql) > 0)
        {
            while ($result = $DB->fetch_array($sql)) 
            {
                   $ass[$result['datefm']]['total_price_bk'] = intval($result['total_price']);
                   $ass[$result['datefm']]['total_buy_bk'] = intval($result['total_buy']);
                   $ass[$result['datefm']]['profit_bk'] = intval($result['total_price']) - intval($result['total_buy']);
            }
        }
        // End ass
        
        // Calculate asset + product
        $temp = $output_report['data_1'];
        $count = count($temp);
        foreach($ass as $key => $value)
        {
            
            $is_exist = 0;
            for($k=0;$k<count($temp);$k++)
            {
                if(isset($temp[$k][$key]))
                {
                    $temp[$k][$key]['total_price_bk'] += $value['total_price_bk'];
                    $temp[$k][$key]['total_buy_bk'] += $value['total_buy_bk'];
                    $temp[$k][$key]['profit_bk'] += $value['profit_bk'];
                    
                    $is_exist = 1;
                }
            }
            
            // Not exist => add new
            if($is_exist==0)
            {
                $temp[$count]['date'] = $key;
                
                // Date full for action search detail list
                list($time_search_from,$time_search_to) = self::get_search_time($time_from,$time_to,$key,$view_type);
                
                $temp[$count]['link_search'] = "/?site=order&status=2&store_id={$store_id}&time_from={$time_search_from}&time_to={$time_search_to}"; 
                // End
                
                $temp[$count]['order'] = $count+1;
                $temp[$count]['total_price_bk'] = $value['total_price_bk'];
                $temp[$count]['total_buy_bk'] = $value['total_buy_bk'];
                $temp[$count]['profit_bk'] = $value['profit_bk'];
                
                $count++;
            }
        }
        // End calculate
        
        // Save array
        $output_report['data_1'] = $temp;
        
        // New session for export data
        $_SESSION['export_sales_date'] = $output_report['data_1'];

        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report);//
    }
    
    static function report_sales_store($time_from=0, $time_to=0, $view_type = "", $p_group="", $store_id="")
    {
        global $CMS, $DB;

        if($time_from and $time_to)
        {
            $clause = " AND ordi_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ordi_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ordi_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check for product group
        if($p_group)
        {
            $clause .= " AND IF(O.product_id > 0,product_group='{$p_group}',pgroup_id='{$p_group}') ";
        }
        
        // Check for store
        if($store_id)
        {
            $clause .= " AND O.store_id='{$store_id}' ";
        }
        
        $datef = self::getTypeView($view_type);
        $arr_chart = array();
        
        if($datef)
        {
            // Thống kê cho biểu đồ group theo thời gian
            $sql = $DB->query("SELECT O.store_id, COUNT(ordi_id) as qty, DATE_FORMAT(FROM_UNIXTIME(ordi_time),'{$datef}') AS datefm, sum(ordi_total) as total_price,sum(IF(O.product_id > 0,product_price_original,ass_original_price)) as total_buy FROM ".root_table."order_item as O left join ".root_table."product as P on O.product_id=P.product_id left join ".root_table."assets as A on A.ass_id=O.ass_id WHERE ordi_deleted = 0 AND O.store_id > 0 {$clause} GROUP BY O.store_id ORDER BY ordi_time ASC");
            
            $output_report['data_1'] = [];
            $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&datefm={$datef}&pgroup={$p_group}&store={$store_id}&datef={$datef}&type_report=report_sales_store";
            
            if($DB->num_rows($sql) > 0)
            {
                $i = 0;
                while ($result = $DB->fetch_array($sql))
                {
                    $store_name = $CMS->store->get_info($result['store_id'],"store_name");
                    $arr_chart[$store_name] = $result['total_price'];
                    
                    $output_report['data_1'][$i]['order'] = $i+1;
                    $output_report['data_1'][$i]['store'] = "<a href='{$CMS->vars['root_domain']}/?site=store&act=show&id={$result['store_id']}'>".$store_name."</a>";
                    $output_report['data_1'][$i]['total_price'] = $CMS->class->input->currency(intval($result['total_price']));
                    $output_report['data_1'][$i]['total_buy'] = $CMS->class->input->currency(intval($result['total_buy']));
                    $output_report['data_1'][$i]['profit'] = $CMS->class->input->currency(intval($result['total_price']) - intval($result['total_buy']));
                    
                    $output_report['data_1'][$i]['total_price_bk'] = intval($result['total_price']);
                    $output_report['data_1'][$i]['total_buy_bk'] = intval($result['total_buy']);
                    $output_report['data_1'][$i]['profit_bk'] = intval($result['total_price']) - intval($result['total_buy']);

                    $i++;
                }
            }
        }
        
        // New session for export data
        $_SESSION['export_sales_store'] = $output_report['data_1'];
        
        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_store']}\", \"{$CMS->lang['title_total_sales']}\"]"; // Khai bao
        $x = 1;
        
        if(!empty($arr_chart))
        {
            foreach ($arr_chart as $key => $value) 
            {
                $chart[] = "[\"{$key}\", {$value}]";
            }
            
            $data['str_chart'] = "[".implode($chart, ',')."]"; 
        }
        else
        {
            $data['str_chart'] = '';
        }
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_sales_follow_store']}";
        $data['title_chart']['title_unit_y'] = "";

        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report);
    } 
    
    static function report_sales_product($time_from=0, $time_to=0, $view_type = "", $p_group="", $store_id="",$product="",$price_from=0,$price_to=0,$supplier=0,$user=0)
    {
        global $CMS, $DB;

        if($time_from and $time_to)
        {
            $clause = " AND ordi_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ordi_time >= '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ordi_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check for product group
        if($p_group){$clause .= " AND product_group='{$p_group}' ";}
        
        // Check for store
        if($store_id){$clause .= " AND O.store_id='{$store_id}' ";}
        
        // Check product name
        if($product){$clause .= " AND product_name like '%{$product}%' ";}
        
        // Check price from
        if($price_from){$clause .= " AND ordi_total >= '{$price_from}' ";}
        
        // Check price to
        if($price_to){$clause .= " AND ordi_total <= '{$price_to}' ";}
        
        // Check supplier
        if($supplier){$clause .= " AND sup_id='{$supplier}' ";}

        // Check user
        if($user)
        {
            // group
            if(substr($user, 0, 1) == "g")
            {
                $temp = explode("g",$user);
                $sql_user = " left join ".root_table."user as U on U.user_id=O.user_id ";
                $clause .= " AND userg_id='".intval($temp[1])."'";
            }
            // user
            else
            {
                $clause .= " AND O.user_id='{$user}' ";
            }
        }
        
        $datef = self::getTypeView($view_type);
        $arr_chart = array();
        
        if($datef)
        {
            // Thống kê cho biểu đồ group theo thời gian
            $sql = $DB->query("SELECT O.product_id,ordi_name, product_group, COUNT(ordi_id) as qty, DATE_FORMAT(FROM_UNIXTIME(ordi_time),'{$datef}') AS datefm, sum(ordi_total) as total_price,sum(product_price_original) as total_buy FROM ".root_table."order_item as O left join ".root_table."product as P on O.product_id=P.product_id {$sql_user} WHERE ordi_deleted = 0 AND O.product_id > 0 {$clause} GROUP BY ordi_name ORDER BY total_price DESC");
            
            $output_report['data_1'] = [];
            $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&datefm={$datef}&pgroup={$p_group}&store={$store_id}&product={$product}&price_from={$price_from}&price_to={$price_to}&supplier={$supplier}&user={$user}&datef={$datef}&type_report=report_sales_product";
            
            if($DB->num_rows($sql) > 0)
            {
                $i = 0;
                while ($result = $DB->fetch_array($sql))
                {
                    $output_report['data_1'][$i]['order'] = $i+1;
                    $output_report['data_1'][$i]['ordi_name'] = "<a href='{$CMS->vars['root_domain']}/?site=product&act=show&id={$result['product_id']}'>".$result['ordi_name']."</a>";
                    $output_report['data_1'][$i]['qty'] = intval($result['qty']);
                    $group = $CMS->product_group->getInfo($result['product_group'],"product_group_name");
                    $output_report['data_1'][$i]['group'] = $group ? "<a href='{$CMS->vars['root_domain']}/?site=product_group&act=show&id={$result['product_group']}'>".$group."</a>" : "";
                    $output_report['data_1'][$i]['total_price'] = $CMS->class->input->currency(intval($result['total_price']));
                    $output_report['data_1'][$i]['total_buy'] = $CMS->class->input->currency(intval($result['total_buy']));
                    $output_report['data_1'][$i]['profit'] = $CMS->class->input->currency(intval($result['total_price']) - intval($result['total_buy']));
                    
                    $output_report['data_1'][$i]['total_price_bk'] = intval($result['total_price']);
                    $output_report['data_1'][$i]['total_buy_bk'] = intval($result['total_buy']);
                    $output_report['data_1'][$i]['profit_bk'] = intval($result['total_price']) - intval($result['total_buy']);

                    $i++;
                }
            }
        }
        
        // New session for export data
        $_SESSION['export_sales_product'] = $output_report['data_1'];
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_sales_follow_product']}";
        $data['title_chart']['title_unit_y'] = "";

        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report);
    }
    
    static function report_sales_assets($time_from=0, $time_to=0, $view_type = "", $p_group="", $store_id="",$ass_name="",$price_from=0,$price_to=0,$supplier=0,$user=0)
    {
        global $CMS, $DB;

        if($time_from and $time_to)
        {
            $clause = " AND ordi_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ordi_time >= '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ordi_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check for product group
        if($p_group){$clause .= " AND pgroup_id='{$p_group}' ";}
        
        // Check for store
        if($store_id){$clause .= " AND O.store_id='{$store_id}' ";}
        
        // Check product name
        if($product){$clause .= " AND ass_name like '%{$product}%' ";}
        
        // Check price from
        if($price_from){$clause .= " AND ordi_total >= '{$price_from}' ";}
        
        // Check price to
        if($price_to){$clause .= " AND ordi_total <= '{$price_to}' ";}
        
        // Check supplier
        if($supplier){$clause .= " AND supplier_id='{$supplier}' ";}

        // Check user
        if($user)
        {
            // group
            if(substr($user, 0, 1) == "g")
            {
                $temp = explode("g",$user);
                $sql_user = " left join ".root_table."user as U on U.user_id=O.user_id ";
                $clause .= " AND userg_id='".intval($temp[1])."'";
            }
            // user
            else
            {
                $clause .= " AND O.user_id='{$user}' ";
            }
        }
        
        $datef = self::getTypeView($view_type);
        $arr_chart = array();
        
        if($datef)
        {
            // Thống kê cho biểu đồ group theo thời gian
            $sql = $DB->query("SELECT O.ass_id, ordi_name, pgroup_id, COUNT(ordi_id) as qty, DATE_FORMAT(FROM_UNIXTIME(ordi_time),'{$datef}') AS datefm, sum(ordi_total) as total_price,sum(ass_original_price) as total_buy FROM ".root_table."order_item as O left join ".root_table."assets as A on A.ass_id=O.ass_id {$sql_user}  WHERE ordi_deleted = 0 AND O.ass_id > 0 {$clause} GROUP BY ordi_name ORDER BY total_price DESC");
            
            $output_report['data_1'] = [];
            $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&datefm={$datef}&pgroup={$p_group}&store={$store_id}&ass_name={$product}&price_from={$price_from}&price_to={$price_to}&supplier={$supplier}&user={$user}&datef={$datef}&type_report=report_sales_assets";
            
            if($DB->num_rows($sql) > 0)
            {
                $i = 0;
                while ($result = $DB->fetch_array($sql))
                {
                    $output_report['data_1'][$i]['order'] = $i+1;
                    $output_report['data_1'][$i]['ordi_name'] = "<a href='{$CMS->vars['root_domain']}/?site=assets&act=show&id={$result['ass_id']}'>".$result['ordi_name']."</a>";
                    $output_report['data_1'][$i]['qty'] = intval($result['qty']);
                    $group = $CMS->product_group->getInfo($result['pgroup_id'],"product_group_name");
                    $output_report['data_1'][$i]['group'] = $group ? "<a href='{$CMS->vars['root_domain']}/?site=product_group&act=show&id={$result['pgroup_id']}'>".$group."</a>" : "";
                    $output_report['data_1'][$i]['total_price'] = $CMS->class->input->currency(intval($result['total_price']));
                    $output_report['data_1'][$i]['total_buy'] = $CMS->class->input->currency(intval($result['total_buy']));
                    $output_report['data_1'][$i]['profit'] = $CMS->class->input->currency(intval($result['total_price']) - intval($result['total_buy']));
                    
                    $output_report['data_1'][$i]['total_price_bk'] = intval($result['total_price']);
                    $output_report['data_1'][$i]['total_buy_bk'] = intval($result['total_buy']);
                    $output_report['data_1'][$i]['profit_bk'] = intval($result['total_price']) - intval($result['total_buy']);

                    $i++;
                }
            }
        }
        
        // New session for export data
        $_SESSION['export_sales_assets'] = $output_report['data_1'];
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_sales_follow_assets']}";
        $data['title_chart']['title_unit_y'] = "";

        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report);
    }
    
    static function report_product_store($time_from=0, $time_to=0, $view_type = "", $store_id="", $p_group="",$product="",$supplier="")
    {
        global $CMS, $DB;

        if($time_from and $time_to)
        {
            $clause = " AND ordi_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ordi_time >= '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ordi_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check for product group
        if($p_group){$clause .= " AND product_group='{$p_group}' ";}
        
        // Check for store
        if(is_array($store_id) AND !empty($store_id))
        {  
            $store_id = implode(",",array_values($store_id)); // Reset key array
            $clause .= " AND O.store_id IN (".$store_id.") ";
            
            // Convert array for get name
            $list_store = array();
            for($i=0;$i<count($store_id);$i++)
            {
                $store_name  = $CMS->store->get_info($store_id[$i],'store_name');
                $list_store[] = array('id' => $store_id[$i],'name' => $store_name);
            }
        }
        else if($store_id)
        {
            // Get again store_id when export data
        }
        else
        {
            // Get list all store for html content ajax
            $list_store = $CMS->store->get_list_store("",2);
            $store_id = $CMS->store->get_list_store("",3);
        }
        
        // Check for product
        if($product) { $clause .= " AND product_name='{$product}' "; }
        
        // Check supplier
        if($supplier){$clause .= " AND sup_id='{$supplier}' ";}
        
        // Check user
        $datef = self::getTypeView($view_type);
        $arr_chart = array();
        
        if($datef)
        {
            // Get query
            $sql = $DB->query("SELECT O.product_id,ordi_id,product_name,product_code,product_price, product_price_original,product_price_sell,O.store_id,count(ordi_id) as qty,O.store_id, sum(product_price_sell) as sales, ordi_total FROM ".root_table."order_item as O left join ".root_table."product as P on O.product_id=P.product_id left join ".root_table."order as OD on OD.ord_id=O.ord_id WHERE ord_deleted = 0 AND ord_status=2 AND product_name!='' {$clause} GROUP BY O.product_id,store_id ORDER BY O.store_id ASC");
            
            $output_report['data_1'] = [];
            $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&datefm={$datef}&pgroup={$p_group}&store={$store_id}&product={$product}&supplier={$supplier}&datef={$datef}&type_report=product_store";
            $temp = $output_report['data_1'];
            
            if($DB->num_rows($sql) > 0)
            {
                $i = 0;
                while ($result = $DB->fetch_array($sql))
                {
                    if(isset($temp[$result['product_name']]))
                    {
                        if(isset($temp[$result['product_name']][$result['store_id']]))
                        {
                            $temp[$result['product_name']][$result['store_id']]+= $result['qty'];
                            
                        }
                        else
                        {
                            $temp[$result['product_name']][$result['store_id']] = intval($result['qty']);
                        }
                        
                        $temp[$result['product_name']]['total_sales'] += $result['sales'];
                    }
                    else
                    {
                        $temp[$result['product_name']] = array(
                                            $result['store_id'] => intval($result['qty']),
                                            "name" => "<a href='{$CMS->vars['root_domain']}/?site=product&act=show&id={$result['product_id']}'>".$result['product_name']."</a>",
                                            "code" => $result['product_code'],
                                            "product_price" => intval($result['product_price_original']),
                                            "product_price_sell" => intval($result['product_price_sell']),
                                            "total_sales" => intval($result['sales']),
                                );
                    }
                    $i++;
                }
            }
        }
        
        $output_report['data_1'] = $_SESSION['export_product_store'] = $temp;
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_product_follow_store']}";
        $data['title_chart']['title_unit_y'] = "";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report, $list_store);
    }
    
    static function report_assets_store($time_from=0, $time_to=0, $view_type = "", $store_id="", $p_group="",$ass_name="", $ass_id="",$supplier="")
    {
        global $CMS, $DB;
        
        if($time_from and $time_to)
        {
            $clause = " AND ordi_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ordi_time >= '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ordi_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check for product group
        if($p_group){$clause .= " AND pgroup='{$p_group}' ";}
        
        // Check for store
        if(is_array($store_id) AND !empty($store_id))
        {  
            $store_id = implode(",",array_values($store_id)); // Reset key array
            $clause .= " AND O.store_id IN (".$store_id.") ";
            
            // Convert array for get name
            $list_store = array();
            for($i=0;$i<count($store_id);$i++)
            {
                $store_name  = $CMS->store->get_info($store_id[$i],'store_name');
                $list_store[] = array('id' => $store_id[$i],'name' => $store_name);
            }
        }
        else if($store_id)
        {
            // Get again store_id when export data
        }
        else
        {
            // Get list all store for html content ajax
            $list_store = $CMS->store->get_list_store("",2);
            $store_id = $CMS->store->get_list_store("",3);
        }
        
        // Check for product
        if($ass_id && $ass_name) { $clause .= " AND (O.ass_id='{$ass_id}' AND A.ass_name='{$ass_name}') "; }
        
        // Check supplier
        if($supplier){$clause .= " AND sup_id='{$supplier}' ";}
        
        // Check user
        $datef = self::getTypeView($view_type);
        $arr_chart = array();
        
        if($datef)
        {
            // Get query
            $sql = $DB->query("SELECT O.ass_id,ordi_id,ass_name,ass_code,ass_purchase_price, ass_original_price,ass_price,O.store_id,count(ordi_id) as qty,O.store_id, sum(ass_price) as sales,ordi_total FROM ".root_table."order_item as O left join ".root_table."assets as A on O.ass_id=A.ass_id left join ".root_table."order as OD on OD.ord_id=O.ord_id WHERE ord_deleted = 0 AND ord_status=2 AND ass_name!='' {$clause} GROUP BY O.ass_id,store_id ORDER BY O.store_id ASC");
            
            $output_report['data_1'] = [];
            $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&datefm={$datef}&pgroup={$p_group}&store={$store_id}&ass_name={$ass_name}&ass_id={$ass_id}&supplier={$supplier}&datef={$datef}&type_report=assets_store";
            $temp = $output_report['data_1'];
            
            if($DB->num_rows($sql) > 0)
            {
                $i = 0;
                while ($result = $DB->fetch_array($sql))
                {
                    if(isset($temp[$result['product_name']]))
                    {
                        if(isset($temp[$result['product_name']][$result['store_id']]))
                        {
                            $temp[$result['product_name']][$result['store_id']]+= $result['qty'];
                            
                        }
                        else
                        {
                            $temp[$result['product_name']][$result['store_id']] = intval($result['qty']);
                        }
                        
                        $temp[$result['product_name']]['total_sales'] += $result['sales'];
                    }
                    else
                    {
                        $temp[$result['product_name']] = array(
                                            $result['store_id'] => intval($result['qty']),
                                            "name" => "<a href='{$CMS->vars['root_domain']}/?site=assets&act=show&id={$result['ass_id']}'>".$result['ass_name']."</a>",
                                            "code" => $result['ass_code'],
                                            "product_price" => intval($result['ass_original_price']),
                                            "product_price_sell" => intval($result['ass_price']),
                                            "total_sales" => intval($result['sales']), 
                                );
                    }
                    $i++;
                }
            }
        }
        
        $output_report['data_1'] = $_SESSION['export_assets_store'] = $temp;
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_assets_follow_store']}";
        $data['title_chart']['title_unit_y'] = "";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report, $list_store);
    }
    
    static function report_sales_pgroup($time_from=0, $time_to=0, $view_type = "", $p_group="", $store_id=0)
    {
        global $CMS, $DB;

        if($time_from and $time_to)
        {
            $clause = " AND ordi_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ordi_time >= '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ordi_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check for product group
        if($p_group){$clause .= " AND IF(O.product_id >0,product_group='{$p_group}',pgroup_id='{$p_group}') ";}
        
        // Check for store
        if($store_id){$clause .= " AND O.store_id='{$store_id}' ";}
        
        $datef = self::getTypeView($view_type);
        $arr_chart = array();
        $arr_chart_2 = array();
        $arr_chart_3 = array();
        
        if($datef)
        {
            // Thống kê cho biểu đồ group theo thời gian
            $sql = $DB->query("SELECT IF(O.product_id > 0,product_group,pgroup_id) as group_id, COUNT(ordi_id) as qty, DATE_FORMAT(FROM_UNIXTIME(ordi_time),'{$datef}') AS datefm, sum(ordi_total) as total_price,sum(IF(O.product_id > 0,product_price_original,ass_original_price)) as total_buy FROM ".root_table."order_item as O left join ".root_table."product as P on O.product_id=P.product_id left join ".root_table."assets as A on A.ass_id=O.ass_id WHERE ordi_deleted = 0 {$clause} GROUP BY IF(O.product_id > 0,product_group,pgroup_id) ORDER BY total_price DESC");
            
            $output_report['data_1'] = [];
            $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&datefm={$datef}&pgroup={$p_group}&store={$store_id}&datef={$datef}&type_report=report_sales_pgroup";
            
            if($DB->num_rows($sql) > 0)
            {
                $i = 0;
                while ($result = $DB->fetch_array($sql))
                {
                    // Get group name
                    $group = $CMS->product_group->getInfo($result['group_id'],"product_group_name");
                    if($group)
                    {
                        // Info for chart
                        $arr_chart[$group] = $result['total_price'];

                        // Info for table
                        $output_report['data_1'][$i]['order'] = $i+1;
                        $output_report['data_1'][$i]['qty'] = intval($result['qty']);
                        $output_report['data_1'][$i]['group'] = "<a href='{$CMS->vars['root_domain']}/?site=product_group&act=show&id={$result['group_id']}'>".$group."</a>";
                        $output_report['data_1'][$i]['total_price'] = $CMS->class->input->currency(intval($result['total_price']));
                        $output_report['data_1'][$i]['total_buy'] = $CMS->class->input->currency(intval($result['total_buy']));
                        $output_report['data_1'][$i]['profit'] = $CMS->class->input->currency(intval($result['total_price']) - intval($result['total_buy']));

                        $output_report['data_1'][$i]['total_price_bk'] = intval($result['total_price']);
                        $output_report['data_1'][$i]['total_buy_bk'] = intval($result['total_buy']);
                        $output_report['data_1'][$i]['profit_bk'] = intval($result['total_price']) - intval($result['total_buy']);
                        
                        // Info chart 2
                        $arr_chart_2[$group] = $result['total_buy'];
                        $arr_chart_3[$group] = intval($result['total_price']) - intval($result['total_buy']);
                        
                        $i++;                
                    }

                }
            }
        }
        
        // New session for export data
        $_SESSION['export_sales_pgroup'] = $output_report['data_1'];
        
        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart_2 = array();
        $chart[0] = $chart_2[0] = $chart_3[0] = "[\"{$CMS->lang['title_pgroup']}\", \"{$CMS->lang['title_total_sales']}\"]"; // Khai bao
        $x = 1;
        
        // Chart 1
        if(!empty($arr_chart))
        {
            foreach ($arr_chart as $key => $value) 
            {
                $chart[] = "[\"{$key}\", {$value}]";
            }
            
            $data['str_chart'] = "[".implode($chart, ',')."]";
        }
        else
        {
            $data['str_chart'] = '';
        }
        // End chart 1
        
        // Chart 2
        if(!empty($arr_chart_2))
        {
            foreach ($arr_chart_2 as $key => $value) 
            {
                $chart_2[] = "[\"{$key}\", {$value}]";
            }
            
            $data['str_chart_2'] = "[".implode($chart_2, ',')."]";
        }
        else
        {
            $data['str_chart_2'] = '';
        }
        // End chart 2
        
        // Chart 3
        if(!empty($arr_chart_3))
        {
            foreach ($arr_chart_3 as $key => $value) 
            {
                $chart_3[] = "[\"{$key}\", {$value}]";
            }
            
            $data['str_chart_3'] = "[".implode($chart_3, ',')."]";
        }
        else
        {
            $data['str_chart_3'] = '';
        }
        // End chart 3
        
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_sales_follow_pgroup']}";
        $data['title_chart']['title_unit_y'] = "";
        $data['title_chart']['title_hearer'] = "{$CMS->lang['title_report_sale_total_price']}";
        $data['title_chart']['title_hearer_2'] = "{$CMS->lang['title_report_sale_total_pprice']}";
        $data['title_chart']['title_hearer_3'] = "{$CMS->lang['title_report_sale_profit']}";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report,$data['str_chart_2'],$data['str_chart_3']);
    }
    
    static function report_customer_overview($time_from=0, $time_to=0, $view_type = "", $city="", $times="")
    {
        global $CMS, $DB;

        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time >= '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check city
        $location_name = "cus_city"; // Default is city
        $sql_add = "";
        if($city)
        {
            $clause .= " AND cus_city='{$city}' ";
            $sql_add = " left join ".root_table."city as CT on C.cus_city=CT.city_id ";
            $location_name = "cus_district"; // if exist city, default is district
        }
        
        // Check times
        $times = $times ? abs($times) : 5; // Default
        
        $datef = self::getTypeView($view_type);
        $arr_chart = array();
        $arr_chart_2 = array();
        
        // Chart group by follow location: City or district
        $sql = $DB->query("SELECT DISTINCT(C.cus_id) as qty,{$location_name} FROM ".root_table."customer as C left join ".root_table."order as O on C.cus_id=O.cus_id WHERE cus_deleted = 0 {$clause} ORDER BY {$location_name} DESC");
            
        $output_report['data_1'] = [];
        $output_report['link_export_1'] = ""; // Disable
            
        if($DB->num_rows($sql) > 0)
        {
            $i = 0;
            while ($result = $DB->fetch_array($sql))
            {
                // Convert location
                if($city)
                {
                    $temp = $result['cus_district'] ? $CMS->country->district(0,$result['cus_district'],1) : $CMS->lang['title_report_none'];
                }
                else
                {
                    $temp = $result['cus_city'] ? $CMS->country->city(0,$result['cus_city']) : $CMS->lang['title_report_none'];
                }
                
                // Info for table
                if(isset($output_report['data_1'][$temp]))
                {
                    $output_report['data_1'][$temp]['qty']++;
                }
                else
                {
                    $output_report['data_1'][$temp]['location'] = $temp;
                    $output_report['data_1'][$temp]['qty'] = 1;
                }

                // Info chart
                if(isset($arr_chart[$temp]))
                {
                   $arr_chart[$temp] ++; 
                }
                else
                {
                   $arr_chart[$temp] = 1; 
                }
                
                        
                $i++;
            }
        }
        // End
        
        // Chart group by follow buy times
        // Get max buy times for range
        $sql_max = $DB->query("SELECT COUNT(ord_id) as max FROM ".root_table."customer as C left join ".root_table."order as O on C.cus_id=O.cus_id WHERE cus_deleted = 0 {$clause} GROUP BY C.cus_id ORDER BY max DESC LIMIT 1");
        $data = $DB->fetch_array($sql_max);
        
        // Create array for range
        $i = 0;
        $range = array();
        
        while($i<=$data['max'])
        {
           $range[$i."-".($i+$times)] = 0;
           $i+=$times;
        }
        
        $output_report['data_2'] = [];
          
        // Sql for list 2
        $sql_2 = $DB->query("SELECT COUNT(ord_id) as qty FROM ".root_table."customer as C left join ".root_table."order as O on C.cus_id=O.cus_id WHERE cus_deleted = 0 {$clause} GROUP BY C.cus_id ORDER BY qty ASC");
        if($DB->num_rows($sql) > 0)
        {
            while ($result = $DB->fetch_array($sql_2))
            {
                // Default range by 0
                $x = 0;
                foreach($range as $k => $v)
                {
                    if($result['qty'] > $x AND $result['qty'] <= ($x+$times))
                    {
                        $range[$k]++;
                        
                        // Data for list
                        if(isset($output_report['data_2'][$k]))
                        {
                           $output_report['data_2'][$k]['qty']++;
                        }
                        else
                        {
                            $output_report['data_2'][$k]['range'] = $k;
                            $output_report['data_2'][$k]['qty'] = 1;
                        }
                    }
                    
                    $x+=$times;
                }
            }
        }
        // End
        
        $arr_chart_2 = $range;
            
        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart_2 = array();
        $chart[0] = $chart_2[0] = $chart_3[0] = "[\"{$CMS->lang['title_pgroup']}\", \"{$CMS->lang['title_total_sales']}\"]"; // Khai bao
        $x = 1;
        
        // Chart 1
        if(!empty($arr_chart))
        {
            foreach ($arr_chart as $key => $value) 
            {
                $chart[] = "[\"{$key}\", {$value}]";
            }
            
            $data['str_chart'] = "[".implode($chart, ',')."]";
        }
        else
        {
           $data['str_chart'] = ''; 
        }
        // End chart 1
        
        // Chart 2
        if(!empty($arr_chart_2))
        {
            foreach ($arr_chart_2 as $key => $value) 
            {
                $chart_2[] = "[\"{$key}\", {$value}]";
            }
            
            $data['str_chart_2'] = "[".implode($chart_2, ',')."]";
        }
        else
        {
           $data['str_chart_2'] = ''; 
        }
        // End chart 2
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_customer_overview']}";
        $data['title_chart']['title_unit_y'] = "";
        $data['title_chart']['title_hearer'] = "{$CMS->lang['title_report_customer_location']}";
        $data['title_chart']['title_hearer_2'] = "{$CMS->lang['title_report_customer_times_buy']}";
        
        // Type chart
        $type_chart = "pie";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report,$data['str_chart_2'], $type_chart);
    }
    
    static function report_customer_sales($time_from=0, $time_to=0, $view_type = "", $store="",$city="", $district="", $customer="")
    {
        global $CMS, $DB;

        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time >= '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check store
        if($store){$clause .= " AND store_id='{$store}' ";}
        
        // Check City + district
        if($city) {$clause .= " AND cus_city='{$city}' ";}
        if($district) {$clause .= " AND cus_district='{$district}' ";}
        
        // Check customer name
        if($customer) { $clause .= " AND cus_full_name like '%{$customer}%' "; }
        
        $datef = self::getTypeView($view_type);
        $arr_chart = array();
        
        //print "SELECT C.cus_id,cus_full_name, cus_phone, O.ord_id, ord_quantity_items, ord_total FROM ".root_table."order_item as I left join ".root_table."order as O on I.ord_id=O.ord_id left join ".root_table."customer as C on O.cus_id=C.cus_id WHERE cus_deleted = 0 ANd ord_status=2 AND ord_deleted=0 {$clause} ORDER BY C.cus_id DESC";exit;
        
        // SQL data
        //$sql = $DB->query("SELECT C.cus_id,cus_full_name, cus_phone, count(ord_id) as qty, sum(ord_quantity_items) as total_item, sum(ord_total) as ord_total FROM ".root_table."customer as C left join ".root_table."order as O on C.cus_id=O.cus_id WHERE cus_deleted = 0 ANd ord_status=2 AND ord_deleted=0 {$clause} GROUP BY C.cus_id ORDER BY C.cus_id DESC");
        $sql = $DB->query("SELECT C.cus_id,cus_full_name, cus_phone, O.ord_id, ord_quantity_items, ord_total, ordi_id, I.product_id, I.ass_id FROM ".root_table."order_item as I left join ".root_table."order as O on I.ord_id=O.ord_id left join ".root_table."customer as C on O.cus_id=C.cus_id WHERE cus_deleted = 0 ANd ord_status=2 AND ord_deleted=0 {$clause} ORDER BY C.cus_id DESC");
            
        $output_report['data_1'] = $temp = [];
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&datefm={$datef}&store={$store}&city={$city}&district={$district}&customer=".urlencode($customer)."&datef={$datef}&type_report=customer_sales";
            
        if($DB->num_rows($sql) > 0)
        {
            $i = 0;
            while ($result = $DB->fetch_array($sql))
            {
                if(isset($temp[$result['cus_id']]))
                {
                    // If order is exist, not calculate ord_total again, only calculate item original price
                    if(!in_array($result['ord_id'],$temp[$result['cus_id']]['order_id']))
                    {
                        $temp[$result['cus_id']]['qty']++;
                        $temp[$result['cus_id']]['order_id'][] = $result['ord_id'];
                        $temp[$result['cus_id']]['total_item'] += $result['ord_quantity_items'];
                        $temp[$result['cus_id']]['ord_total'] += intval($result['ord_total']);
                    }
                    
                    if($result['product_id'])
                    {
                        $temp[$result['cus_id']]['ord_original'] += $CMS->product->get_info($result['product_id'],"product_price_original");
                    }
                    
                    if($result['ass_id'])
                    {
                        $temp[$result['cus_id']]['ord_original'] += $CMS->product->get_info($result['ass_id'],"ass_original_price");
                    }
                    
                    // Calculate profit
                    $temp[$result['cus_id']]['profit'] = intval($temp[$result['cus_id']]['ord_total']-$temp[$result['cus_id']]['ord_original']);
                }
                else
                {
                    // Customer info
                    $temp[$result['cus_id']]['order'] = $i+1;
                    $temp[$result['cus_id']]['name'] = "<a href='{$CMS->vars['root_domain']}/?site=customer&act=show&id={$result['cus_id']}'>".$result['cus_full_name']."</a>";
                    $temp[$result['cus_id']]['phone'] = $result['cus_phone'];
                    
                    // Order data
                    $temp[$result['cus_id']]['order_id'][] = $result['ord_id'];
                    $temp[$result['cus_id']]['qty'] = 1; // Count order
                    $temp[$result['cus_id']]['total_item'] = intval($result['ord_quantity_items']); // Count ordi item
                    $temp[$result['cus_id']]['ord_total'] = intval($result['ord_total']); // Order total
                    
                    // Calculate orginal price off item
                    $temp[$result['cus_id']]['ord_original'] = 0;
                    if($result['product_id'])
                    {
                        $temp[$result['cus_id']]['ord_original'] += $CMS->product->get_info($result['product_id'],"product_price_original");
                    }
                    
                    if($result['ass_id'])
                    {
                        $temp[$result['cus_id']]['ord_original'] += $CMS->product->get_info($result['ass_id'],"ass_original_price");
                    }
                    
                    // Calculate profit
                    $temp[$result['cus_id']]['profit'] = intval($temp[$result['cus_id']]['ord_total']-$temp[$result['cus_id']]['ord_original']);
                    
                    $i++;
                }
            }
        }
        // End
        
        // New session for export data
        $_SESSION['export_customer_sales'] = $output_report['data_1'] = $temp;
            
        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_pgroup']}\", \"{$CMS->lang['title_total_sales']}\"]"; // Khai bao
        $x = 1;
        
        // Chart 1
        if(!empty($arr_chart))
        {
            foreach ($arr_chart as $key => $value) 
            {
                $chart[] = "[\"{$key}\", {$value}]";
            }
            
            $data['str_chart'] = "[".implode($chart, ',')."]";
        }
        else
        {
           $data['str_chart'] = ''; 
        }
        // End chart 1
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_customer_follow_sales']}";
        $data['title_chart']['title_unit_y'] = "";
        $data['title_chart']['title_hearer'] = "{$CMS->lang['title_report_customer_location']}";
        $data['title_chart']['title_hearer_2'] = "{$CMS->lang['title_report_customer_times_buy']}";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report,$data['str_chart_2'], $type_chart);
    }
    
    static function report_finance_daily($time_from=0, $time_to=0, $view_type = "", $store="")
    {
        global $CMS, $DB;

        if($time_from and $time_to)
        {
            $clause = " AND trx_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND trx_time >= '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND trx_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check store
        if($store){$clause .= " AND O.store_id='{$store}' ";} 
        
        $datef = self::getTypeView($view_type);
        $arr_chart = array();
        
        // SQL data
        $sql = $DB->query("SELECT * FROM ".root_table."transaction T left join ".root_table."order O on T.trx_id=O.transaction_id left join ".root_table."store S on O.store_id=S.store_id WHERE ord_deleted=0 AND S.store_name!='' {$clause} ORDER BY trx_id DESC");
            
        $output_report['data_1'] = $temp = [];
        $output_report['link_export_1'] = "";
            
        if($DB->num_rows($sql) > 0)
        {
            while ($result = $DB->fetch_array($sql))
            {
                // Convert
                $result['trx_total'] = intval($result['trx_total']);
                $result['ord_total_discount'] = intval($result['ord_total_discount']);
                
                // Data for List
                if(isset($temp[$result['store_name']]))
                {
                    if($result['trx_type']== 1) // Sales
                    {
                        // Get total amount
                        $temp[$result['store_name']]['total'] += $result['trx_total'];
                        
                        // Total discount
                        $temp[$result['store_name']]['total_discount'] += $result['ord_total_discount'];
                        
                        // Total cash
                        $temp[$result['store_name']]['total_cash'] += $result['trx_payment_method'] == 0 ? $result['trx_total'] : 0;
                        
                        // Total bank transfer
                        $temp[$result['store_name']]['total_transfer_in'] += $result['trx_payment_method'] == 1 ? $result['trx_total'] : 0;
                    }
                    else // Expense
                    {
                        // Total cash out
                        $temp[$result['store_name']]['total_cash_out'] += $result['trx_payment_method'] == 0 ? $result['trx_total'] : 0;
                        
                        // Total bank transfer out
                        $temp[$result['store_name']]['total_transfer_out'] += $result['trx_payment_method'] == 1 ? $result['trx_total'] : 0;
                    }
                }
                else
                {
                    // Get name
                    $temp[$result['store_name']]['name'] = $result['store_name'];
                    
                    // Default
                    $temp[$result['store_name']]['total_cash'] = $temp[$result['store_name']]['total_cash_out'] = 0;
                    $temp[$result['store_name']]['total_transfer_in'] = $temp[$result['store_name']]['total_transfer_out'] = 0;
                    
                    if($result['trx_type']== 1) // Sales
                    {
                        // Get total amount
                        $temp[$result['store_name']]['total'] = $result['trx_total'];
                        
                        // Total discount
                        $temp[$result['store_name']]['total_discount'] = $result['ord_total_discount'];
                        
                        // Total cash
                        $temp[$result['store_name']]['total_cash'] = $result['trx_payment_method'] == 0 ? $result['trx_total'] : 0;
                        
                        // Total bank transfer
                        $temp[$result['store_name']]['total_transfer_in'] = $result['trx_payment_method'] == 1 ? $result['trx_total'] : 0;
                    }
                    else // Expense
                    {
                        // Total cash out
                        $temp[$result['store_name']]['total_cash_out'] = $result['trx_payment_method'] == 0 ? $result['trx_total'] : 0;
                        
                        // Total bank transfer out
                        $temp[$result['store_name']]['total_transfer_out'] = $result['trx_payment_method'] == 1 ? $result['trx_total'] : 0;
                    }
                }
                
                //=============== Calculate total
                $temp[$result['store_name']]['total_cash_left'] = intval($temp[$result['store_name']]['total_cash'] - $temp[$result['store_name']]['total_cash_out']);
                $temp[$result['store_name']]['total_debt'] = intval($temp[$result['store_name']]['total'] - $temp[$result['store_name']]['total_discount'] - $temp[$result['store_name']]['total_cash'] - $temp[$result['store_name']]['total_transfer_in']);
            }
        }
        // End
        
        $output_report['data_1'] = $temp;
            
        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_pgroup']}\", \"{$CMS->lang['title_total_sales']}\"]"; // Khai bao
        $x = 1;
        
        // Chart 1
        if(!empty($arr_chart))
        {
            foreach ($arr_chart as $key => $value) 
            {
                $chart[] = "[\"{$key}\", {$value}]";
            }
            
            $data['str_chart'] = "[".implode($chart, ',')."]";
        }
        else
        {
           $data['str_chart'] = ''; 
        }
        // End chart 1
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_menu_finance_retail_store']}";
        $data['title_chart']['title_unit_y'] = "";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report);
    }
    
    static function report_finance_revenue($time_from=0, $time_to=0, $view_type = "", $store="", $cus_type="", $object = "")
    {
        global $CMS, $DB;
 
        if($time_from and $time_to)
        {
            $clause = " AND trx_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND trx_time >= '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND trx_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check store
        if($store)
        {
            $sql_join = " T left join ".root_table."order O on T.trx_id=O.transaction_id left join ".root_table."store S on O.store_id=S.store_id ";
            $sql_cond = " ord_deleted=0 AND S.store_name!='' AND ";
            $clause .= " AND O.store_id='{$store}' ";
        } 
        
        // Check for cus type
        if($cus_type)
        { 
            $clause .= " AND cus_type='{$cus_type}' "; 
            
            // Check for data from cus type
            if($cus_type == 1 AND $object) // Customer
            {
                $clause .= $store ? " AND T.cus_id='{$object}' " : " AND cus_id='{$object}' ";
            }
            
            // Check for data from cus type
            if($cus_type == 2 AND $object) // Supplier
            {
                $clause .= $store ? " AND T.supplier_id='{$object}' " : " AND supplier_id='{$object}' ";
            }
            
            // Check for data from cus type
            if($cus_type == 3 AND $object) // User
            {
                $clause .= $store ? " AND T.user_assign='{$object}' " : " AND user_assign='{$object}' ";
            }
        }
        
        $datef = self::getTypeView($view_type);
        $arr_chart = array();
        
        // SQL data
        $sql = $DB->query("SELECT * FROM ".root_table."transaction {$sql_join} WHERE {$sql_cond} trx_status IN (1,3) AND trx_subtype IN (2,3,6,7) {$clause} ORDER BY trx_id DESC");
            
        $output_report['data_1'] = $temp = [];
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&store_id={$store}&cus_type={$cus_type}&object={$object}&datefm={$datef}&type_report=finance_revenue";
        $list_date = array();
            
        if($DB->num_rows($sql) > 0)
        {
            while ($result = $DB->fetch_array($sql))
            {
                // Convert
                $result['trx_total'] = intval($result['trx_total']);
                $time = $CMS->class->date->date_format($result['trx_time']);
                
                // Data for List
                if(isset($temp[$time]))
                {
                    if(in_array($result['trx_subtype'],array("2","3")) AND $result['trx_payment_method'] == 0) // Receive, Receipt - Credit cash
                    {
                        // Get total credit cash
                        $temp[$time]['credit_cash'] += $result['trx_total'];
                    }
                    else if(in_array($result['trx_subtype'],array("2","3")) AND $result['trx_payment_method'] == 1) // Receive, Receipt - Credit bank transfer
                    {
                        // Get total credit bank
                        $temp[$time]['credit_bank'] += $result['trx_total']; 
                    }
                    else if(in_array($result['trx_subtype'],array("6","7")) AND $result['trx_payment_method'] == 0) // Expense, Bill payment - Debit cash
                    {
                        // Get total debit cash
                        $temp[$time]['debit_cash'] += $result['trx_total'];
                    }
                    else if(in_array($result['trx_subtype'],array("6","7")) AND $result['trx_payment_method'] == 1) // Expense, Bill payment - Debit bank transfer
                    {
                        // Get total debit bank transfer
                        $temp[$time]['debit_bank'] += $result['trx_total'];
                    }
                }
                else
                {
                    // Get date
                    $temp[$time]['date'] = $time;
                    
                    // Date full for action search detail list
                    list($time_search_from,$time_search_to) = self::get_search_time($time_from,$time_to,$time,'view_day',0,1);
                    $temp[$time]['link_search'] = "/?site=transactions&trx_status=1,3&sub=2,3,6,7&type=1&date_from=".$time_search_from."&date_to=".$time_search_to; 
                    // End
                    
                    if(in_array($result['trx_subtype'],array("2","3")) AND $result['trx_payment_method'] == 0) // Receive, Receipt - Credit cash
                    {
                        // Get total credit cash
                        $temp[$time]['credit_cash'] = $result['trx_total'];
                    }
                    else if(in_array($result['trx_subtype'],array("2","3")) AND $result['trx_payment_method'] == 1) // Receive, Receipt - Credit bank transfer
                    {
                        // Get total credit bank
                        $temp[$time]['credit_bank'] = $result['trx_total']; 
                    }
                    else if(in_array($result['trx_subtype'],array("6","7")) AND $result['trx_payment_method'] == 0) // Expense, Bill payment - Debit cash
                    {
                        // Get total debit cash
                        $temp[$time]['debit_cash'] = $result['trx_total'];
                    }
                    else if(in_array($result['trx_subtype'],array("6","7")) AND $result['trx_payment_method'] == 1) // Expense, Bill payment - Debit bank transfer
                    {
                        // Get total debit bank transfer
                        $temp[$time]['debit_bank'] = $result['trx_total'];
                    }
                }
                
                // Calcuate total
                $temp[$time]['total_cash'] = $temp[$time]['credit_cash'] - $temp[$time]['debit_cash'];
                $temp[$time]['total_bank'] = $temp[$time]['credit_bank'] - $temp[$time]['debit_bank'];
            }
        }
        // End
        
        $output_report['data_1'] = $_SESSION['export_finance_revenue'] = $temp;
        
        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_pgroup']}\", \"{$CMS->lang['title_total_sales']}\"]"; // Khai bao
        $x = 1;
        
        // Chart 1
        if(!empty($arr_chart))
        {
            foreach ($arr_chart as $key => $value)
            {
                $chart[] = "[\"{$key}\", {$value}]";
            }
            
            $data['str_chart'] = "[".implode($chart, ',')."]";
        }
        else
        {
           $data['str_chart'] = ''; 
        }
        // End chart 1
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_menu_finance_retail_store']}";
        $data['title_chart']['title_unit_y'] = "";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report, $list_date);
    }
    


    static function report_finance_commission_staff($time_from=0, $time_to=0, $user_id = "" , $object = "")
    {
        global $CMS, $DB;
 
        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time >= '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check for cus type
        if($user_id != "")
        { 
            $clause .= " AND user_id ='{$user_id}' "; 
        }
        
        $datef = self::getTypeView($view_type);
        $arr_chart = array();
 
        // SQL data
        $sql = $DB->query("SELECT user_id, SUM(ord_commission * ord_commission_rating)/100 as commission FROM ".root_table."order   WHERE  ord_deleted = 0 AND ord_status = 2  {$clause} GROUP BY user_id");
            
        $output_report['data_1'] = $temp = [];
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&user_id={$user_id}&object={$object}&datefm={$datef}&type_report=finance_commission_staff";
        $list_date = array();
            
        if($DB->num_rows($sql) > 0)
        {
            while ($result = $DB->fetch_array($sql))
            {
                // Convert
             
                $user_id_ar =  $result['user_id'];
                $us = $CMS->user->get_info($user_id_ar,"user_name");
                // Data for List
                $temp[$user_id_ar]['commission'] = $CMS->class->input->currency($result['commission']);
                $temp[$user_id_ar]['username'] = $us;
               
            }
        }
        // End
        
        $output_report['data_1'] = $_SESSION['export_finance_commission_staff'] = $temp;
        
        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_pgroup']}\", \"{$CMS->lang['title_total_sales']}\"]"; // Khai bao
        $x = 1;
        
         
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_menu_finance_retail_store']}";
        $data['title_chart']['title_unit_y'] = "";
 
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report, $list_date);
    }



    //Backup lại code cũa Vũ
    /*static function report_finance_commission_service($time_from=0, $time_to=0, $view_type = "", $p_group="", $store_id="",$product="",$user=0)
    {
        global $CMS, $DB;

        if($time_from and $time_to)
        {
            $clause = " AND ordi_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ordi_time >= '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ordi_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }

        // Check for product group
        if($p_group){$clause .= " AND product_group='{$p_group}' ";}

        // Check for store
        if($store_id){$clause .= " AND O.store_id='{$store_id}' ";}

        // Check product name
        if($product){$clause .= " AND product_name like '%{$product}%' ";}

        // Check user
        if($user)
        {
            // group
            if(substr($user, 0, 1) == "g")
            {
                $temp = explode("g",$user);
                $sql_user = " left join ".root_table."user as U on U.user_id=O.user_id ";
                $clause .= " AND userg_id='".intval($temp[1])."'";
            }
            // user
            else
            {
                $clause .= " AND O.user_id='{$user}' ";
            }
        }

        $datef = self::getTypeView($view_type);
        $arr_chart = array();

        if($datef)
        {
            // Thống kê cho biểu đồ group theo thời gian
            $sql = $DB->query("SELECT O.product_id,ordi_name, product_group, COUNT(ordi_id) as qty, DATE_FORMAT(FROM_UNIXTIME(ordi_time),'{$datef}') AS datefm, sum(ordi_total) as total_price,sum(product_price_original) as total_buy FROM ".root_table."order_item as O left join ".root_table."product as P on O.product_id=P.product_id {$sql_user} WHERE ordi_deleted = 0 AND O.product_id > 0 {$clause} GROUP BY ordi_name ORDER BY total_price DESC");

            $output_report['data_1'] = [];
            $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&datefm={$datef}&pgroup={$p_group}&store={$store_id}&product={$product}&price_from={$price_from}&price_to={$price_to}&supplier={$supplier}&user={$user}&datef={$datef}&type_report=report_sales_product";

            if($DB->num_rows($sql) > 0)
            {
                $i = 0;
                while ($result = $DB->fetch_array($sql))
                {
                    $output_report['data_1'][$i]['order'] = $i+1;
                    $output_report['data_1'][$i]['ordi_name'] = "<a href='{$CMS->vars['root_domain']}/?site=product&act=show&id={$result['product_id']}'>".$result['ordi_name']."</a>";
                    $output_report['data_1'][$i]['qty'] = intval($result['qty']);
                    $group = $CMS->product_group->getInfo($result['product_group'],"product_group_name");
                    $output_report['data_1'][$i]['group'] = $group ? "<a href='{$CMS->vars['root_domain']}/?site=product_group&act=show&id={$result['product_group']}'>".$group."</a>" : "";
                    $output_report['data_1'][$i]['total_price'] = $CMS->class->input->currency(intval($result['total_price']));
                    $output_report['data_1'][$i]['total_buy'] = $CMS->class->input->currency(intval($result['total_buy']));
                    $output_report['data_1'][$i]['profit'] = $CMS->class->input->currency(intval($result['total_price']) - intval($result['total_buy']));

                    $output_report['data_1'][$i]['total_price_bk'] = intval($result['total_price']);
                    $output_report['data_1'][$i]['total_buy_bk'] = intval($result['total_buy']);
                    $output_report['data_1'][$i]['profit_bk'] = intval($result['total_price']) - intval($result['total_buy']);

                    $i++;
                }
            }
        }

        // New session for export data
        $_SESSION['export_sales_product'] = $output_report['data_1'];

        $data['title_chart']['main_title'] = "{$CMS->lang['title_sales_follow_product']}";
        $data['title_chart']['title_unit_y'] = "";

        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report);
    }*/

    static function report_finance_commission_service($time_from=0, $time_to=0, $view_type = "", $p_group="", $store_id="",$product="",$user=0)
    {
        global $CMS, $DB;

        if($time_from and $time_to)
        {
            $clause = " AND ordi_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ordi_time >= '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ordi_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }

        // Check for cus type

        if($user_id != "")
        {
            $clause .= " AND user_id ='{$user_id}' ";
        }

        $datef = self::getTypeView($view_type);
        $arr_chart = array();

        // SQL data
        $sql = "SELECT product_id, SUM(ordi_commission * ordi_commission_rating)/100 as commission FROM ".root_table."order_item WHERE  ordi_deleted = 0 AND ordi_status = 2  {$clause} GROUP BY product_id";
        $sql = $DB->query($sql);

        $output_report['data_1'] = $temp = [];
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&user_id={$user_id}&object={$object}&datefm={$datef}&type_report=finance_commission_service";
        $list_date = array();

        if($DB->num_rows($sql) > 0)
        {
            while ($result = $DB->fetch_array($sql))
            {
                $product = $CMS->product->getInfo($result['product_id']);
                // Data for List
                $temp[$result['product_id']]['commission'] = $CMS->class->input->currency($result['commission']);
                $temp[$result['product_id']]['product_name'] = $product['product_name'];
            }
        }
        // End
        $output_report['data_1'] = $_SESSION['export_finance_commission_service'] = $temp;

        $month = date("m");
        $year = date("Y");
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_pgroup']}\", \"{$CMS->lang['title_total_sales']}\"]"; // Khai bao
        $x = 1;



        $data['title_chart']['main_title'] = "{$CMS->lang['title_menu_finance_retail_store']}";
        $data['title_chart']['title_unit_y'] = "";

        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report, $list_date);
    }
    


    static function report_finance_record($time_from=0, $time_to=0, $view_type = "")
    {
        global $CMS, $DB;

        if($time_from and $time_to)
        {
            $clause = " AND trx_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND trx_time >= '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND trx_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        $datef = self::getTypeView($view_type);
        $arr_chart = array();
        
        // Get Opening balance (Số dư đầu kỳ)
        $open_balance = \models\report::get_open_balance($time_from);
        
        // SQL data
        $sql = $DB->query("SELECT * FROM ".root_table."accounts_type A left join ".root_table."transaction T on A.accounts_type_id=T.at_id WHERE accounts_type_deleted=0 AND trx_deleted=0 AND trx_subtype IN (2,3,5,6) AND trx_status IN (1,3) {$clause} ORDER BY accounts_type_id DESC");
            
        $output_report['data_1'] = $temp = [];
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&datefm={$datef}&type_report=finance_record";
        $i=1;
            
        if($DB->num_rows($sql) > 0)
        {
            while ($result = $DB->fetch_array($sql))
            {
                // Convert
                $result['trx_total'] = intval($result['trx_total']);
                
                // Data for List
                if(isset($temp[$result['accounts_type_name']]))
                {
                    // Get total
                    if($result['trx_subtype'] == 2 OR $result['trx_subtype'] == 3)
                    {
                        $temp[$result['accounts_type_name']]['balance_debit'] += $result['trx_total'];
                    }
                    else
                    {
                       $temp[$result['accounts_type_name']]['balance_credit'] += $result['trx_total'];
                    }
                }
                else
                {
                    // Get name
                    $temp[$result['accounts_type_name']]['name'] = $result['accounts_type_name'];
                    
                    // Get code
                    $temp[$result['accounts_type_name']]['code'] = $result['accounts_type_code'];
                    
                    // Number
                    $temp[$result['accounts_type_name']]['order'] = $i;
                    
                    // Ghi nợ
                    $temp[$result['accounts_type_name']]['balance_debit'] = ($result['trx_subtype'] == 2 OR $result['trx_subtype'] == 3) ? $result['trx_total'] : 0;
                    
                    // Ghi có
                    $temp[$result['accounts_type_name']]['balance_credit'] = ($result['trx_subtype'] == 5 OR $result['trx_subtype'] == 6) ? $result['trx_total'] : 0;
                    
                    $i++;
                }
                
                // Save open balance into data list
                $temp[$result['accounts_type_name']]['open_balance']['debit'] = intval($open_balance[$result['accounts_type_name']]['debit']);
                $temp[$result['accounts_type_name']]['open_balance']['credit'] = intval($open_balance[$result['accounts_type_name']]['credit']);
                
                // Calculate close balance
                // Close balance = open balance credit - open balance debit - balance_debit + balance_credit
                // If close balance < 0, its debit, else its credit
                $cal = $temp[$result['accounts_type_name']]['open_balance']['credit'] - $temp[$result['accounts_type_name']]['open_balance']['debit'] - $temp[$result['accounts_type_name']]['balance_debit'] + $temp[$result['accounts_type_name']]['balance_credit'];
                
                if($cal < 0)
                {
                   $temp[$result['accounts_type_name']]['close_balance']['debit'] = abs($cal);
                   $temp[$result['accounts_type_name']]['close_balance']['credit'] = 0;
                }
                else
                {
                   $temp[$result['accounts_type_name']]['close_balance']['debit'] = 0;
                   $temp[$result['accounts_type_name']]['close_balance']['credit'] = abs($cal); 
                }
            }
        }
        // End
        
        // Convert data
        $output_report['data_1'] = $temp;
        $_SESSION['export_finance_record'] = $temp;
            
        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_pgroup']}\", \"{$CMS->lang['title_total_sales']}\"]"; // Khai bao
        $x = 1;
        
        // Chart 1
        if(!empty($arr_chart))
        {
            foreach ($arr_chart as $key => $value) 
            {
                $chart[] = "[\"{$key}\", {$value}]";
            }
            
            $data['str_chart'] = "[".implode($chart, ',')."]";
        }
        else
        {
           $data['str_chart'] = ''; 
        }
        // End chart 1
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_menu_finance_retail_store']}";
        $data['title_chart']['title_unit_y'] = "";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report);
    }
    
    static function report_customer_store($time_from=0, $time_to=0, $view_type = "")
    {
        global $CMS, $DB;

        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time >= '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        $datef = self::getTypeView($view_type); // Default is view month
        $arr_chart = array();
        
        // SQL data
        $sql = $DB->query("SELECT count(cus_id) as qty, store_name,DATE_FORMAT(FROM_UNIXTIME(ord_time),'{$datef}') AS datefm FROM ".root_table."order O left join ".root_table."store S on O.store_id=S.store_id WHERE ord_status=2 AND ord_deleted=0 AND S.store_name !='' {$clause} GROUP BY datefm,O.store_id ORDER BY datefm ASC");
        $output_report['data_1'] = $temp = [];
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&datefm={$datef}&type_report=customer_store";
            
        if($DB->num_rows($sql) > 0)
        {
            // Get list month
            $list_month = array();
            
            while ($result = $DB->fetch_array($sql))
            {
                // Data for List
                $temp[$result['store_name']]['name'] = $result['store_name'];
                $temp[$result['store_name']][$result['datefm']] = $result['qty'];
                
                if(!in_array($result['datefm'],$list_month))
                {
                    $list_month[] = $result['datefm'];
                }
                
            }
        }
        $output_report['data_1'] = $_SESSION['export_customer_store'] = $temp;
        $_SESSION['export_list_month'] = $list_month;
        // End
        
        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_pgroup']}\", \"{$CMS->lang['title_total_sales']}\"]"; // Khai bao
        $x = 1;
        
        // Chart 1
        if(!empty($arr_chart))
        {
            foreach ($arr_chart as $key => $value) 
            {
                $chart[] = "[\"{$key}\", {$value}]";
            }
            
            $data['str_chart'] = "[".implode($chart, ',')."]";
        }
        else
        {
           $data['str_chart'] = ''; 
        }
        // End chart 1
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_customer_follow_store']}";
        $data['title_chart']['title_unit_y'] = "";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report,$list_month);
    }
    
    static function report_customer_product($time_from=0, $time_to=0, $view_type = "", $store="", $p_group="", $product="", $customer="", $supplier="")
    {
        global $CMS, $DB;

        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time >= '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        // Check store
        if($store){$clause .= " AND I.store_id='{$store}' ";}
        
        // Check pgroup
        if($p_group) {$clause .= " AND product_group='{$p_group}' ";}
        
        // Check product
        if($product) { $clause .= " AND product_name like '%{$product}%' "; }
        
        // Check customer
        if($customer) { $clause .= " AND cus_full_name like '%{$customer}%' "; }
        
        // Check supplier
        if($supplier) { $clause .= " AND P.sup_id='{$supplier}' "; }
        
        $datef = self::getTypeView($view_type);
        $arr_chart = array();
        
        // SQL data
        $sql = $DB->query("SELECT I.product_id,C.cus_id, cus_full_name, cus_phone, cus_email, product_name,product_code,I.product_id,product_price,ordi_total,ord_discount,ord_discount_type,ord_amount,product_price_original as cost  FROM ".root_table."customer C left join ".root_table."order_item I on C.cus_id=I.cus_id left join ".root_table."order O on I.ord_id=O.ord_id left join ".root_table."product P on I.product_id=P.product_id WHERE cus_deleted = 0 ANd ord_status=2 AND I.product_id > 0 AND ord_deleted=0 {$clause} ORDER BY ordi_id DESC");
            
        $output_report['data_1'] = $temp = [];
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&datefm={$datef}&store={$store}&pgroup={$pgroup}&product=".urlencode($product)."&customer=".urlencode($customer)."&supplier={$supplier}&datef={$datef}&type_report=customer_product";
            
        if($DB->num_rows($sql) > 0)
        {
            $i=1;
            while ($result = $DB->fetch_array($sql))
            {
                // Mark
                $code = $result['cus_id']."_".$result['product_id'];
                
                // Check for discount each items of order
                // Percent
                if($result['ord_discount_type'] == 0) 
                {
                    $discount = intval($result['ord_amount'])*intval($result['ord_discount'])/100;
                }
                // Cash
                else
                {
                    // Get back percent discount
                    $percent = $result['ord_discount']/$result['ord_amount'];
                    $discount = round($result['ordi_total']*$percent,2);
                }
                
                // Data for List
                if(isset($temp[$code]))
                {
                    $temp[$code]['qty']++;
                    $temp[$code]['discount'] += $discount;
                }
                else
                {
                    $temp[$code]['order'] = $i;
                    $temp[$code]['name'] = "<a href='{$CMS->vars['root_domain']}/?site=customer&act=show&id={$result['cus_id']}'>".$result['cus_full_name']."</a>";
                    $temp[$code]['phone'] = $result['cus_phone'] ? $CMS->lang['title_phone'].":".$result['cus_phone'] : "";
                    $temp[$code]['email'] = $result['cus_email'] ? $CMS->lang['title_email'].":".$result['cus_email'] : "";
                    $temp[$code]['address'] = $result['cus_address'] ? $CMS->lang['title_address'].":".$result['cus_address'] : "";
                    $temp[$code]['qty'] = 1;
                    $temp[$code]['product_name'] = "<a href='{$CMS->vars['root_domain']}/?site=product&act=show&id={$result['product_id']}'>".$result['product_name']."</a>";
                    $temp[$code]['product_code'] = $result['product_code'];
                    $temp[$code]['cost'] = intval($result['cost']);
                    $temp[$code]['price'] = intval($result['ordi_total']);
                    $temp[$code]['discount'] = $discount;
                    
                    $i++;
                }
                
                $temp[$code]['total_price'] = $temp[$code]['price'] * $temp[$code]['qty'];
                $temp[$code]['total_cost'] = $temp[$code]['cost'] * $temp[$code]['qty'];
                $temp[$code]['profit'] = $temp[$code]['total_price'] - $temp[$code]['total_cost'];// - $temp[$code]['discount'];
                
            }
        }
        $output_report['data_1'] = $_SESSION['export_customer_product'] = $temp;
        // End
        
        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_pgroup']}\", \"{$CMS->lang['title_total_sales']}\"]"; // Khai bao
        $x = 1;
        
        // Chart 1
        if(!empty($arr_chart))
        {
            foreach ($arr_chart as $key => $value) 
            {
                $chart[] = "[\"{$key}\", {$value}]";
            }
            
            $data['str_chart'] = "[".implode($chart, ',')."]";
        }
        else
        {
           $data['str_chart'] = ''; 
        }
        // End chart 1
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_customer_follow_product']}";
        $data['title_chart']['title_unit_y'] = "";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report);
    }
    
    static function report_customer_assets($time_from=0, $time_to=0, $view_type = "", $store="", $p_group="", $ass_name="", $customer="", $supplier="")
    {
        global $CMS, $DB;

        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time >= '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        // Check store
        if($store){$clause .= " AND I.store_id='{$store}' ";}
        
        // Check pgroup
        if($p_group) {$clause .= " AND pgroup_id='{$p_group}' ";}
        
        // Check product
        if($product) { $clause .= " AND ass_name like '%{$product}%' "; }
        
        // Check customer
        if($customer) { $clause .= " AND cus_full_name like '%{$customer}%' "; }
        
        // Check supplier
        if($supplier) { $clause .= " AND A.supplier_id='{$supplier}' "; }
        
        $datef = self::getTypeView($view_type);
        $arr_chart = array();
        
        // SQL data
        $sql = $DB->query("SELECT C.cus_id, cus_full_name, cus_phone, cus_email, ass_name,ass_code,I.ass_id,ass_price,ordi_total,ord_discount,ord_discount_type,ord_amount,ass_original_price as cost  FROM ".root_table."customer C left join ".root_table."order_item I on C.cus_id=I.cus_id left join ".root_table."order O on I.ord_id=O.ord_id left join ".root_table."assets as A on A.ass_id=I.ass_id WHERE cus_deleted = 0 ANd ord_status=2 AND I.ass_id > 0 AND ord_deleted=0 {$clause} ORDER BY ordi_id DESC");
            
        $output_report['data_1'] = $temp = [];
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&datefm={$datef}&store={$store}&pgroup={$pgroup}&ass_name=".urlencode($ass_name)."&customer=".urlencode($customer)."&supplier={$supplier}&datef={$datef}&type_report=customer_assets";
            
        if($DB->num_rows($sql) > 0)
        {
            $i=1;
            while ($result = $DB->fetch_array($sql))
            {
                // Mark
                $code = $result['cus_id']."_".$result['ass_id'];
                
                // Check for discount each items of order
                // Percent
                if($result['ord_discount_type'] == 0) 
                {
                    $discount = intval($result['ord_amount'])*intval($result['ord_discount'])/100;
                }
                // Cash
                else
                {
                    // Get back percent discount
                    $percent = $result['ord_discount']/$result['ord_amount'];
                    $discount = round($result['ordi_total']*$percent,2);
                }
                
                // Data for List
                if(isset($temp[$code]))
                {
                    $temp[$code]['qty']++;
                    $temp[$code]['discount'] += $discount;
                }
                else
                {
                    $temp[$code]['order'] = $i;
                    $temp[$code]['name'] = $result['cus_full_name'];
                    $temp[$code]['phone'] = $result['cus_phone'] ? $CMS->lang['title_phone'].":".$result['cus_phone'] : "";
                    $temp[$code]['email'] = $result['cus_email'] ? $CMS->lang['title_email'].":".$result['cus_email'] : "";
                    $temp[$code]['address'] = $result['cus_address'] ? $CMS->lang['title_address'].":".$result['cus_address'] : "";
                    $temp[$code]['qty'] = 1;
                    $temp[$code]['product_name'] = $result['ass_name'];
                    $temp[$code]['product_code'] = $result['ass_code'];
                    $temp[$code]['cost'] = intval($result['cost']);
                    $temp[$code]['price'] = intval($result['ordi_total']);
                    $temp[$code]['discount'] = $discount;
                    
                    $i++;
                }
                
                $temp[$code]['total_price'] = $temp[$code]['price'] * $temp[$code]['qty'];
                $temp[$code]['total_cost'] = $temp[$code]['cost'] * $temp[$code]['qty'];
                $temp[$code]['profit'] = $temp[$code]['total_price'] - $temp[$code]['total_cost'];// - $temp[$code]['discount'];
                
            }
        }
        $output_report['data_1'] = $_SESSION['export_customer_assets'] = $temp;
        // End
        
        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_pgroup']}\", \"{$CMS->lang['title_total_sales']}\"]"; // Khai bao
        $x = 1;
        
        // Chart 1
        if(!empty($arr_chart))
        {
            foreach ($arr_chart as $key => $value) 
            {
                $chart[] = "[\"{$key}\", {$value}]";
            }
            
            $data['str_chart'] = "[".implode($chart, ',')."]";
        }
        else
        {
           $data['str_chart'] = ''; 
        }
        // End chart 1
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_customer_follow_assets']}";
        $data['title_chart']['title_unit_y'] = "";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report);
    }
    
    static function report_sales_supplier($time_from=0, $time_to=0, $view_type = "", $p_group="", $store_id=0,$product="",$supplier=0)
    {
        global $CMS, $DB;
        
        if($time_from and $time_to)
        {
            $clause = " AND ordi_time BETWEEN '{$time_from}' AND '{$time_to}' "; 
        }elseif($time_from)
        {
            $clause = " AND ordi_time >= '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ordi_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check for product group
        if($p_group){$clause .= " AND IF(O.product_id > 0,product_group='{$p_group}',pgroup_id='{$p_group}') ";}
        
        // Check for store
        if($store_id){$clause .= " AND O.store_id='{$store_id}' ";}
        
        // Check for product
        if($product) { $clause .= " AND product_name like '%{$product}%' "; }
        
        // Check for supplier
        if($supplier) {$clause .= " AND IF(O.product_id > 0,sup_id='{$supplier}',supplier_id='{$supplier}') ";}
        
        $datef = self::getTypeView($view_type);
        
        // Cols
        $col = str_split(self::$col);
        
        if($datef)
        {
            // Thống kê cho biểu đồ group theo thời gian
            $sql = $DB->query("SELECT IF(O.product_id > 0,sup_id,supplier_id) as s_id, DATE_FORMAT(FROM_UNIXTIME(ordi_time),'{$datef}') AS datefm, sum(ordi_total) as total_price,sum(IF(O.product_id > 0,product_price_original,ass_original_price)) as total_buy FROM ".root_table."order_item as O left join ".root_table."product as P on O.product_id=P.product_id left join ".root_table."assets as A on A.ass_id=O.ass_id WHERE ordi_deleted = 0 AND sup_id > 0 {$clause} GROUP BY datefm,sup_id ORDER BY datefm ASC");
            
            $data['data_1'] = [];
            $data['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&datefm={$datef}&pgroup={$p_group}&store={$store_id}&product={$product}&supplier={$supplier}&datef={$datef}&type_report=report_sales_supplier";
            
            if($DB->num_rows($sql) > 0)
            {
                $i = 0;
                $day_total = array();
                $temp_day = array();
                while ($result = $DB->fetch_array($sql))
                {
                    // Get group name
                    $supplier_name = $CMS->supplier->get_info($result['s_id'],"supplier_name");
                    $result['total_price'] = intval($result['total_price']);
                    
                    if($supplier_name)
                    {
                        // Set temp array date for unset in list day if value of day is null
                        if(!in_array($result['datefm'],$temp_day) AND count($temp_day) <= count($col))
                        {
                            $temp_day[] =  $result['datefm'];
                        }
                        
                        // Date
                        $report[$supplier_name]['name'] = "<a href='{$CMS->vars['root_domain']}/?site=supplier&act=show&id={$result['s_id']}'>".$supplier_name."</a>";
                        $report[$supplier_name]['order'] = $report[$supplier_name]['order'] ? $report[$supplier_name]['order'] : $i+1;
                        $report[$supplier_name][$result['datefm']] = array(
                            "total" => $result['total_price'],
                            "buy" => $result["total_buy"],
                            "profit" => $result['total_price'] - $result["total_buy"],
                        );

                        // Set default value for All
                        $report[$supplier_name]['all'] = isset($report[$supplier_name]['all']) ? $report[$supplier_name]['all'] : array("total" => 0, "buy" => 0, "profit" => 0);

                        // All
                        $report[$supplier_name]['all'] = array(
                            "total" => $report[$supplier_name]['all']['total'] + $result['total_price'],
                            "buy" => $report[$supplier_name]['all']['buy'] + $result["total_buy"],
                            "profit" => $report[$supplier_name]['all']['profit'] + $result['total_price'] - $result["total_buy"],
                        );
                        
                        // All for days of all supplier
                        $day_total[$result['datefm']] = isset($day_total[$result['datefm']]) ? $day_total[$result['datefm']] : array("total" => 0, "buy" => 0);
                        if(isset($day_total[$result['datefm']]))
                        {
                            $day_total[$result['datefm']]['total'] += $result['total_price'];
                            $day_total[$result['datefm']]['buy'] += $result['total_buy'];
                            $day_total[$result['datefm']]['profit'] += $result['total_price'] - $result['total_buy'];
                        }
                        
                        $i++;                
                    }
                }
            }
        }
        
        // Clear list day
        $_SESSION['export_sales_supplier'] = $report;
        $day = $_SESSION['list_days'] = $temp_day;
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_sales_follow_supplier']}";
        $data['title_chart']['title_unit_y'] = "";
        
        $data['data_1'] = $report;
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $data, $day,$day_total,$data['link_export_1']);
    }

    static function getTypeView($input="")
    {
        $format = "";
        $input = $input ? $input : "view_day";
        if($input == "view_day")
        {
            $format = "%d%-%m%-%Y";
        }elseif($input == "view_month")
        {
            $format = "%m%-%Y";
        }elseif($input == "view_year")
        {
            $format = "%Y";
        }

        return $format;
    }
    
    static function report_order($time_from=0, $time_to=0, $view_type = "", $p_group="", $store_id="")
    {
        global $CMS, $DB;

        // Check time
        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }

        // Check product group
        if($p_group){$clause .= " AND IF(I.product_id > 0,product_group='{$p_group}',pgroup_id='{$p_group}') ";}
        
        // Check store
        if($store_id){$clause .= " AND O.store_id='{$store_id}' ";}

        $datef = self::getTypeView($view_type);
        $arr_chart = array();
        $output_report['data_1'] = [];
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&pgroup={$p_group}&store={$store_id}&datefm={$datef}&type_report=order_date";
        if($datef)
        {
            // Get data order item
            $sql = $DB->query("SELECT sum(ordi_total) as ord_total, count(ordi_id) as qty_items, DATE_FORMAT(FROM_UNIXTIME(ord_time),'{$datef}') AS datefm FROM ".root_table."order_item as I left join ".root_table."order as O on I.ord_id=O.ord_id left join ".root_table."product as P on I.product_id=P.product_id left join ".root_table."assets as A on A.ass_id=I.ass_id WHERE ord_deleted = 0 AND ord_status=2 {$clause} GROUP BY datefm ORDER BY datefm ASC");
            if($DB->num_rows($sql) > 0)
            {
                $i = 0;
                while ($result = $DB->fetch_array($sql))
                {
                    $amount = $CMS->class->input->currency($result['ord_total']);
                    $output_report['data_1'][$i]['datefm'] = $result['datefm'];
                    
                    // Date full for action search detail list
                    list($time_search_from,$time_search_to) = self::get_search_time($time_from,$time_to,$result['datefm'],$view_type);
                    $output_report['data_1'][$i]['link_search'] = "/?site=order&status=2&store_id={$store_id}&time_from={$time_search_from}&time_to={$time_search_to}"; 
                    // End
                    
                    $output_report['data_1'][$i]['quantity'] = $result['qty'];
                    $output_report['data_1'][$i]['qty_items'] = $result['qty_items'];
                    $output_report['data_1'][$i]['amount'] = $amount; 

                    $i++;
                }
            }
            
            // Get quantity order
            $sql_order = $DB->query("SELECT DISTINCT(O.ord_id),DATE_FORMAT(FROM_UNIXTIME(ord_time),'{$datef}') AS datefm FROM ".root_table."order_item as I left join ".root_table."order as O on I.ord_id=O.ord_id left join ".root_table."product as P on I.product_id=P.product_id left join ".root_table."assets as A on A.ass_id=I.ass_id WHERE ord_deleted = 0 AND ord_status=2 {$clause} ORDER BY datefm ASC");
            $temp = $output_report['data_1'];
            while($order = $DB->fetch_array())
            {
                for($i=0;$i<count($temp);$i++)
                {
                    // Get data for list
                    if($temp[$i]['datefm'] == $order['datefm'])
                    {
                        // Get quantity items
                        //$arr_chart[$result['datefm']]['qty'] = $result['qty'];
                        
                        $temp[$i]['quantity']++;
                    }
                    else
                    {
                       $temp[$i][$order['datefm']] = 1; 
                    }
                    
                    // Get data for chart
                    if($arr_chart[$order['datefm']])
                    {
                       $arr_chart[$order['datefm']]['qty']++; 
                    }
                    else
                    {
                        $arr_chart[$order['datefm']]['qty']=1;
                    }
                    
                }
                
            }
        }
        
        $output_report['data_1'] = $_SESSION['export_order_date'] = $temp;
        
        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_time']}\", \"{$CMS->lang['title_order']}\"]";
        $x = 1;
        
        if(!empty($arr_chart))
        {
           foreach ($arr_chart as $key => $value)
            {
                $chart[] = "[\"{$key}\", {$value['qty']}]";
            }
            
            $data['str_chart'] = "[".implode($chart, ',')."]"; 
        }
        else
        {
           $data['str_chart'] = '';
        }
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_chart_order']}";
        $data['title_chart']['title_unit_y'] = "";
        $data['type_chart'] = "";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report, $data['type_chart']);
    }
    
    static function report_product_bestseller($time_from=0, $time_to=0, $view_type = "", $store_id="", $pgroup="", $product="", $supplier="", $p_status="")
    {
        global $CMS, $DB;

        // Check time
        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check product group
        if($pgroup){$clause .= " AND IF(I.product_id > 0,product_group='{$pgroup}',pgroup_id='{$pgroup}') ";}

        // Check product
        if($product){$clause .= " AND (product_name like '%{$product}%' OR product_code like '%{$product}%' )";}
        
        // Check store
        if($store_id){$clause .= " AND I.store_id='{$store_id}' ";}
        
        // Check ord status
        if($p_status){$clause .= " AND product_status='{$p_status}' ";}
        
        // Check ord status
        if($supplier){$clause .= " AND IF(I.product_id > 0, sup_id='{$supplier}', supplier_id='{$supplier}') ";}

        $datef = self::getTypeView("view_day");
        $arr_chart = array();
        $output_report['data_1'] = [];
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&product={$product}&store={$store_id}&ord_status={$ord_status}&datefm={$datef}&type_report=product_bestseller";
        if($datef)
        {
            // Get data order item
            $sql = $DB->query("SELECT P.product_id, product_code, product_name, product_price,product_price_sell, product_price_original, product_image, sum(ordi_total) as ord_total, count(ordi_id) as qty_items, DATE_FORMAT(FROM_UNIXTIME(ord_time),'{$datef}') AS datefm FROM ".root_table."product as P left join ".root_table."order_item as I on P.product_id=I.product_id left join ".root_table."order as O on I.ord_id=O.ord_id WHERE ord_status=2 AND ord_deleted=0 {$clause} GROUP BY P.product_id ORDER BY ord_total DESC");
            if($DB->num_rows($sql) > 0)
            {
                $i = 0;
                while ($result = $DB->fetch_array($sql))
                {
                    $output_report['data_1'][$i]['order'] = $i+1;
                    $output_report['data_1'][$i]['product_code'] = $result['product_code'];
                    $output_report['data_1'][$i]['product_name'] = "<a href='{$CMS->vars['root_domain']}/?site=product&act=show&id={$result['product_id']}'>".$result['product_name']."</a>";
                    $output_report['data_1'][$i]['product_image'] = $result['product_image'] ? "<button title='{$result['product_image']}' class='btn view_detail' href='http://nail01c.3f.design/uploads/demo3f/product/{$result['product_image']}'><i class='fa fa-picture-o fa-lg'></i></button>" : "";
                    $output_report['data_1'][$i]['total'] = $CMS->class->input->currency($result['ord_total']);
                    $output_report['data_1'][$i]['total_bk'] = intval($result['ord_total']);
                    $output_report['data_1'][$i]['qty_items'] = $result['qty_items'];
                    $output_report['data_1'][$i]['p_cost'] = $CMS->class->input->currency(intval($result['product_price']));
                    $output_report['data_1'][$i]['p_sell'] = $CMS->class->input->currency(intval($result['product_price_sell']));
                    $output_report['data_1'][$i]['p_original'] = $CMS->class->input->currency(intval($result['product_price_original']));
                    
                    // Disable discount
                    // New calculate profit: quantity*ordi_total - quantity*product_original - hvu 03/08/2017
                    //$output_report['data_1'][$i]['discount'] = $CMS->class->input->currency(intval($result['total_discount']));
                    
                    $output_report['data_1'][$i]['profit'] = $CMS->class->input->currency($result['ord_total'] - ($result['product_price_original'])*$result['qty_items']);

                    $i++;
                }
                
                $_SESSION['export_product_bestseller'] = $output_report['data_1'];
            }
        }
        
        $month = date("m");
        $year = date("Y");
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_time']}\", \"{$CMS->lang['title_order']}\"]";
        $x = 1;
        foreach ($arr_chart as $key => $value)
        {
            $chart[] = "[\"{$key}\", {$value['qty']}]";
        }
        
        if(count($chart) == 1)
        {
            $day_crr = date("d-m");
            $chart[1] = "[\"{$day_crr}\",0]";
        }
        $data['str_chart'] = "[".implode($chart, ',')."]"; 
        $data['title_chart']['main_title'] = "{$CMS->lang['title_chart_order']}";
        $data['title_chart']['title_unit_y'] = "";
        $data['type_chart'] = "";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report);
    }
    
    static function report_assets_bestseller($time_from=0, $time_to=0, $view_type = "", $store_id="", $pgroup="", $ass_name="", $ass_id="", $supplier="", $ass_avaiable="")
    {
        global $CMS, $DB;

        // Check time
        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check product group
        if($pgroup){$clause .= " AND pgroup_id='{$pgroup}' ";}

        // Check product
        if($ass_id && $ass_name){$clause .= " AND (I.ass_id='{$ass_id}' AND ass_name='{$ass_name}') ";}
        
        // Check store
        if($store_id){$clause .= " AND I.store_id='{$store_id}' ";}
        
        // Check ord status
        if($ass_avaiable){$clause .= " AND is_available='{$ass_avaiable}' ";}
        
        // Check ord status
        if($supplier){$clause .= " AND supplier_id='{$supplier}' ";}

        $datef = self::getTypeView("view_day");
        $arr_chart = array();
        $output_report['data_1'] = [];
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&ass_name={$ass_name}&ass_id={$ass_id}&store={$store_id}&ass_avaiable={$ass_avaiable}&datefm={$datef}&type_report=assets_bestseller";
        if($datef)
        {
            // Get data order item
            $sql = $DB->query("SELECT I.ass_id,ass_code, ass_name, ass_price,ass_purchase_price, ass_original_price, sum(ordi_total) as ord_total, count(ordi_id) as qty_items, DATE_FORMAT(FROM_UNIXTIME(ord_time),'{$datef}') AS datefm FROM ".root_table."assets as A left join ".root_table."order_item as I on A.ass_id=I.ass_id left join ".root_table."order as O on I.ord_id=O.ord_id WHERE ord_status=2 AND ord_deleted=0 {$clause} GROUP BY I.ass_id ORDER BY ord_total DESC");
            if($DB->num_rows($sql) > 0)
            {
                $i = 0;
                while ($result = $DB->fetch_array($sql))
                {
                    $output_report['data_1'][$i]['order'] = $i+1;
                    $output_report['data_1'][$i]['product_code'] = $result['ass_code'];
                    $output_report['data_1'][$i]['product_name'] = "<a href='{$CMS->vars['root_domain']}/?site=assets&act=show&id={$result['ass_id']}'>".$result['ass_name']."</a>";
                    $output_report['data_1'][$i]['total'] = $CMS->class->input->currency($result['ord_total']);
                    $output_report['data_1'][$i]['total_bk'] = intval($result['ord_total']);
                    $output_report['data_1'][$i]['qty_items'] = $result['qty_items'];
                    $output_report['data_1'][$i]['p_cost'] = $CMS->class->input->currency(intval($result['ass_purchase_price']));
                    $output_report['data_1'][$i]['p_sell'] = $CMS->class->input->currency(intval($result['ass_price']));
                    $output_report['data_1'][$i]['p_original'] = $CMS->class->input->currency(intval($result['ass_original_price']));
                    
                    $output_report['data_1'][$i]['profit'] = $CMS->class->input->currency($result['ord_total'] - ($result['ass_original_price'])*$result['qty_items']);

                    $i++;
                }
                
                $_SESSION['export_assets_bestseller'] = $output_report['data_1'];
            }
        }
        
        $month = date("m");
        $year = date("Y");
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_time']}\", \"{$CMS->lang['title_order']}\"]";
        $x = 1;
        
        if(!empty($arr_chart))
        {
            foreach ($arr_chart as $key => $value)
            {
                $chart[] = "[\"{$key}\", {$value['qty']}]";
            }
            
            $data['str_chart'] = "[".implode($chart, ',')."]"; 
        }
        else
        {
            $data['str_chart'] = '';
        }
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_chart_order']}";
        $data['title_chart']['title_unit_y'] = "";
        $data['type_chart'] = "";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report);
    }
    
    static function report_order_user($time_from=0, $time_to=0, $view_type = "", $ord_status="")
    {
        global $CMS, $DB;
        
        // Check time
        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }

        // Check store
        if($ord_status!=NULL){$clause .= " AND ord_status='{$ord_status}' ";}

        $datef = self::getTypeView("view_day");
        $arr_chart = array();
        $output_report['data_1'] = [];
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&ord_status={$ord_status}&datefm={$datef}&type_report=order_user";
        if($datef)
        {
            // Get data order item
            $sql = $DB->query("SELECT user_display_name, user_name,O.user_id,sum(ord_total) as ord_total, count(ord_id) as qty,ord_quantity_items, DATE_FORMAT(FROM_UNIXTIME(ord_time),'{$datef}') AS datefm FROM ".root_table."order as O left join ".root_table."user as U on O.user_id=U.user_id WHERE ord_deleted = 0 {$clause} GROUP BY user_id ORDER BY user_id ASC");
            if($DB->num_rows($sql) > 0)
            {
                $i = 0;
                $temp = $output_report['data_1'];
                while ($result = $DB->fetch_array($sql))
                {
                    $temp[$i]['user_name'] = "<a href='{$CMS->vars['root_domain']}/?site=user&act=show&id={$result['user_id']}'>".$result['user_name']."</a>";
                    $temp[$i]['user_display_name'] = $result['user_display_name'];
                    $temp[$i]['qty'] = intval($result['qty']);
                    $temp[$i]['qty_items'] = intval($result['ord_quantity_items']);
                    $temp[$i]['total_bk'] = intval($result['ord_total']);
                    $temp[$i]['total'] = $CMS->class->input->currency(intval($result['ord_total']));

                    $i++;
                }
            }
        }
        
        // New session for export data
        $output_report['data_1'] = $_SESSION['export_order_user'] = $temp;
        
        $data['str_chart'] = "[".implode($chart, ',')."]"; 
        $data['title_chart']['main_title'] = "{$CMS->lang['title_chart_order']}";
        $data['title_chart']['title_unit_y'] = "";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report);
    }
    
    static function report_order_status($time_from=0, $time_to=0, $view_type = "", $p_group="", $store_id="")
    {
        global $CMS, $DB;

        // Check time
        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }

        // Check product group
        if($p_group){$clause .= " AND IF(I.product_id > 0,product_group='{$p_group}',pgroup_id='{$p_group}') ";}
        
        // Check store
        if($store_id){$clause .= " AND O.store_id='{$store_id}' ";}

        $datef = self::getTypeView($view_type);
        $arr_chart = array();
        $output_report['data_1'] = [];
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&pgroup={$p_group}&store={$store_id}&datefm={$datef}&type_report=order_status";
        if($datef)
        {
            // Get data order item
            $sql = $DB->query("SELECT sum(ord_total) as ord_total, count(O.ord_id) as qty, ord_status, DATE_FORMAT(FROM_UNIXTIME(ord_time),'{$datef}') AS datefm FROM ".root_table."order as O left join ".root_table."order_item as I on O.ord_id=I.ord_id left join ".root_table."product as P on I.product_id=P.product_id left join ".root_table."assets as A on A.ass_id=I.ass_id WHERE ord_deleted = 0 {$clause} GROUP BY datefm,ord_status ORDER BY datefm ASC");
            if($DB->num_rows($sql) > 0)
            {
                while ($result = $DB->fetch_array($sql))
                {
                    $result['qty'] = intval($result['qty']);
                    
                    // Set data for chart
                    if($result['qty'])
                    {
                        if(isset($arr_chart[$result['datefm']][$result['ord_status']]))
                        {
                           $arr_chart[$result['datefm']][$result['ord_status']]['qty'] += $result['qty']; 
                        }
                        else
                        {
                           $arr_chart[$result['datefm']][$result['ord_status']]['qty'] = $result['qty']; 
                        }
                    }
                    
                    // Set data for list
                    if($output_report['data_1'][$result['datefm']][$result['ord_status']])
                    {
                        $output_report['data_1'][$result['datefm']][$result['ord_status']]['qty'] += $result['qty'];
                    }
                    else
                    {
                        $output_report['data_1'][$result['datefm']][$result['ord_status']]['qty'] = $result['qty'];
                        $output_report['data_1'][$result['datefm']]['datefm'] = $result['datefm'];
                        
                        // Store info
                        $store = $CMS->store->get_info($store_id,"store_name");
                        $output_report['data_1'][$result['datefm']]['store'] = $store ? "<a href='{$CMS->vars['root_domain']}/?site=store&act=show&id={$store_id}'>".$store."</a>" : '';
                    }
                }
                
                // New session for export data
                $_SESSION['export_order_status'] = $output_report['data_1'];
            }
        }
        
        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_time']}\",\"{$CMS->lang['title_order_status_0']}\", \"{$CMS->lang['title_order_status_1']}\", \"{$CMS->lang['title_order_status_2']}\"]";
        $j = 1;
        
        // Array order status
        $status = array(0,1,2); // 0: pending, 1; processing, 2: Done
        
        // Loop for data
        if(!empty($arr_chart))
        {
            foreach ($arr_chart as $key => $value)
            {
                $chart[] = '["'.$key.'",'.intval($value[0]['qty']).','.intval($value[1]['qty']).','.intval($value[2]['qty']).']';
            }
            
            $data['str_chart'] = "[".implode($chart, ',')."]"; 
        }
        else
        {
            $data['str_chart'] = ''; 
        }
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_chart_order']}";
        $data['title_chart']['title_unit_y'] = "";
        $data['type_chart'] = "";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report, $data['type_chart']);
    }
    
    static function report_order_product($time_from=0, $time_to=0, $view_type = "", $p_group="", $product="")
    {
        global $CMS, $DB;

        // Check time
        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }

        // Check product group
        if($p_group){$clause .= " AND product_group='{$p_group}' ";}
        
        // Check store
        if($product){$clause .= " AND product_name like '%{$product}%' ";}
        
        // View type default is view day
        $datef = self::getTypeView("view_day");
        $arr_chart = array();
        $output_report['data_1'] = [];
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&pgroup={$p_group}&product={$product}&datefm={$datef}&type_report=order_product";
        if($datef)
        {
            // Get data order item
            $sql = $DB->query("SELECT I.product_id as data_id, product_code as data_code, product_name as data_name,sum(ordi_total) as ord_total, count(ordi_id) as qty_items FROM ".root_table."order_item as I left join ".root_table."order as O on I.ord_id=O.ord_id left join ".root_table."product as P on I.product_id=P.product_id WHERE ord_deleted = 0 AND ord_status=2 AND I.product_id > 0 {$clause} GROUP BY data_id ORDER BY data_id ASC");
            if($DB->num_rows($sql) > 0)
            {
                $temp = $output_report['data_1'];
                $i = 0;
                while ($result = $DB->fetch_array($sql))
                {
                    $temp[$i]['product_id'] = $result['data_id'];
                    $temp[$i]['product_code'] = $result['data_code'];
                    $temp[$i]['product_name'] = "<a href='{$CMS->vars['root_domain']}/?site=product&act=show&id={$result['data_id']}'>".$result['data_name']."</a>";
                    $temp[$i]['qty_items'] = intval($result['qty_items']);
                    $temp[$i]['qty'] = 0;
                    $temp[$i]['total_bk'] = intval($result['ord_total']);
                    $temp[$i]['total'] = $CMS->class->input->currency($result['ord_total']);
                    
                    $i++;
                }
            }
            
            // Get quantity order
            $sql_order = $DB->query("SELECT DISTINCT(I.ord_id),ord_item FROM ".root_table."order_item as I left join ".root_table."order as O on I.ord_id=O.ord_id left join ".root_table."product as P on I.product_id=P.product_id left join ".root_table."assets as A on A.ass_id=I.ass_id WHERE ord_deleted = 0 AND ord_status=2 AND I.product_id > 0 {$clause} ORDER BY I.product_id ASC");
            
            while($order = $DB->fetch_array())
            {
                // Get info product
                $data_item = json_decode($order['ord_item']);
                
                foreach($data_item as $key => $value)
                {
                    for($i=0;$i<count($temp);$i++)
                    {
                        if($temp[$i]['product_id'] == $value->product_id)
                        {
                           $temp[$i]['qty']++;
                        }
                    }
                }
                
            }
        }
        
        $output_report['data_1'] = $_SESSION['export_order_product'] = $temp;
        
        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_time']}\", \"{$CMS->lang['title_order']}\"]";
        $x = 1;
        
        if(!empty($arr_chart))
        {
           foreach ($arr_chart as $key => $value)
            {
                $chart[] = "[\"{$key}\", {$value['qty']}]";
            } 
            
            $data['str_chart'] = "[".implode($chart, ',')."]"; 
        }
        else
        {
            $data['str_chart'] = ''; 
        }
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_chart_order']}";
        $data['title_chart']['title_unit_y'] = "";
        $data['type_chart'] = "";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report, $data['type_chart']);
    }
    
    static function report_order_assets($time_from=0, $time_to=0, $view_type = "", $p_group="", $ass_name="")
    {
        global $CMS, $DB;

        // Check time
        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }

        // Check product group
        if($p_group){$clause .= " AND pgroup_id='{$p_group}' ";}
        
        // Check store
        if($ass_name){$clause .= " AND ass_name like '%{$ass_name}%' ";}
        
        // View type default is view day
        $datef = self::getTypeView("view_day");
        $arr_chart = array();
        $output_report['data_1'] = [];
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&pgroup={$p_group}&ass_name={$product}&datefm={$datef}&type_report=order_assets";
        if($datef)
        {
            // Get data order item
            $sql = $DB->query("SELECT (I.ass_id) as data_id, (ass_code) as data_code, (ass_name) as data_name,sum(ordi_total) as ord_total, count(ordi_id) as qty_items FROM ".root_table."order_item as I left join ".root_table."order as O on I.ord_id=O.ord_id left join ".root_table."assets as A on A.ass_id=I.ass_id WHERE ord_deleted = 0 AND ord_status=2 AND I.ass_id > 0 {$clause} GROUP BY data_id ORDER BY data_id ASC");
            if($DB->num_rows($sql) > 0)
            {
                $temp = $output_report['data_1'];
                $i = 0;
                while ($result = $DB->fetch_array($sql))
                {
                    $temp[$i]['product_id'] = $result['data_id'];
                    $temp[$i]['product_code'] = $result['data_code'];
                    $temp[$i]['product_name'] = "<a href='{$CMS->vars['root_domain']}/?site=assets&act=show&id={$result['data_id']}'>".$result['data_name']."</a>";
                    $temp[$i]['qty_items'] = intval($result['qty_items']);
                    $temp[$i]['qty'] = 0;
                    $temp[$i]['total_bk'] = intval($result['ord_total']);
                    $temp[$i]['total'] = $CMS->class->input->currency($result['ord_total']);
                    
                    $i++;
                }
            }
            
            // Get quantity order
            $sql_order = $DB->query("SELECT DISTINCT(I.ord_id),ord_item,I.ass_id as id FROM ".root_table."order_item as I left join ".root_table."order as O on I.ord_id=O.ord_id left join ".root_table."assets as A on A.ass_id=I.ass_id WHERE ord_deleted = 0 AND ord_status=2 AND I.ass_id > 0 {$clause} ORDER BY I.ass_id ASC");
            
            while($order = $DB->fetch_array())
            {
                for($i=0;$i<count($temp);$i++)
                {
                    if($temp[$i]['product_id'] == $order['id'])
                    {
                        $temp[$i]['qty']++;
                    }
                }
            }
        }
        
        $output_report['data_1'] = $_SESSION['export_order_assets'] = $temp;
        
        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_time']}\", \"{$CMS->lang['title_order']}\"]";
        $x = 1;
        
        if(!empty($arr_chart))
        {
           foreach ($arr_chart as $key => $value)
            {
                $chart[] = "[\"{$key}\", {$value['qty']}]";
            } 
            
            $data['str_chart'] = "[".implode($chart, ',')."]"; 
        }
        else
        {
            $data['str_chart'] = ''; 
        }
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_chart_order']}";
        $data['title_chart']['title_unit_y'] = "";
        $data['type_chart'] = "";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report, $data['type_chart']);
    }
    
    // Report order follow range price
    static function report_order_price($time_from=0, $time_to=0, $view_type = "", $range=0)
    {
        global $CMS, $DB;

        // Check time
        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }

        // Check range price
        $range = $range > 0 ? $range : 300000;

        // Get view type
        $datef = self::getTypeView($view_type);
        
        // Input
        $arr_chart = array();
        $output_report['data_1'] = [];
        
        // Link for export
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&range={$range}&datefm={$datef}&type_report=order_price";
        if($datef)
        {
            // Get data order with all price
            $sql = $DB->query("SELECT ord_total, count(ord_id) as qty FROM ".root_table."order WHERE ord_deleted = 0 AND ord_status=2 {$clause} GROUP BY ord_total ORDER BY ord_total ASC");
            if($DB->num_rows($sql) > 0)
            {
                $i = 0;
                while ($result = $DB->fetch_array($sql))
                {
                    // Check order total is in range price
                    if($result['ord_total']%$range == 0)
                    {
                        // Data for chart
                        $result['ord_total'] = $CMS->class->input->currency(intval($result['ord_total']));
                        $arr_chart[$result['ord_total']] = $result['qty'];
                    
                        // Data for list
                        $output_report['data_1'][$i]['order'] = $i+1;
                        $output_report['data_1'][$i]['quantity'] = $result['qty'];
                        $output_report['data_1'][$i]['total'] = $result['ord_total'];

                        $i++;  
                    }
                }
                
                // New session for export data
                $_SESSION['export_order_price'] = $output_report['data_1'];
            }
        }
        
        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_time']}\", \"{$CMS->lang['title_order']}\"]";
        $x = 1;
        
        if(!empty($arr_chart))
        {
            foreach ($arr_chart as $key => $value)
            {
                $chart[] = "[\"{$key}\", {$value['qty']}]";
            } 
            
            $data['str_chart'] = "[".implode($chart, ',')."]"; 
        }
        else
        {
           $data['str_chart'] = "";
        }
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_order_follow_price']}";
        $data['title_chart']['title_unit_y'] = "";
        $data['type_chart'] = "pie";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report, $data['type_chart']);
    }
    // End report order follow range price
    
    // Report product follow range price
    static function report_product_price($time_from=0, $time_to=0, $view_type = "",$pgroup=0, $range=0, $price_type=0)
    {
        global $CMS, $DB;

        // Check time
        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check product group
        if($pgroup) {$clause .= " AND product_group='{$pgroup}' ";}
        
        // Check price type
        $price_type = $price_type == 1 ? "product_price_sell" : "product_price_original";

        // Check range price
        $range = $range > 0 ? $range : 1;

        // Get view type
        $datef = self::getTypeView("view_day");
        
        // Input
        $arr_chart = array();
        $output_report['data_1'] = [];
        
        // Link for export
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&pgroup={$pgroup}&price_type={$price_type}&range={$range}&datefm={$datef}&type_report=product_price";
        if($datef)
        {
            // Get total product sold follow clause
            $sql = $DB->query("SELECT {$price_type} as price_type, count(ordi_id) as qty,sum(ordi_total) as total FROM ".root_table."product as P left join ".root_table."order_item as I on P.product_id=I.product_id left join ".root_table."order as O on I.ord_id=O.ord_id WHERE ord_deleted = 0 AND ord_status=2 {$clause} GROUP BY {$price_type} ORDER BY {$price_type} ASC");
            if($DB->num_rows($sql) > 0)
            {
                $i = 0;
                while ($result = $DB->fetch_array($sql))
                {
                    // Convert to int
                    $result['total'] = $CMS->class->input->currency(intval($result['total']));
                    
                    // Check order total is in range price
                    if($result['price_type']%$range == 0)
                    {
                        // Data for chart
                        $result['price_type'] = $CMS->class->input->currency($result['price_type']);
                        $arr_chart[$result['price_type']]['qty'] = $result['qty'];
                        
                        // Data for list
                        $output_report['data_1'][$result['price_type']]['order'] = $i+1;
                        $output_report['data_1'][$result['price_type']]['price_type'] = $result['price_type'];
                        $output_report['data_1'][$result['price_type']]['total_qty_sold'] = $result['qty'];
                        $output_report['data_1'][$result['price_type']]['total_price'] = $result['total'];
                        $output_report['data_1'][$result['price_type']]['total_product'] = 0;
                        
                        $i++;
                    }
                }
            }
            
            // Get total product in range price for compare with data_1
            $sql = $DB->query("SELECT {$price_type} as price_type, count(product_id) as qty FROM ".root_table."product WHERE ".($pgroup ? " product_group={$pgroup}" : "1=1")." GROUP BY {$price_type} ORDER BY {$price_type} ASC");
            while($data = $DB->fetch_array($sql))
            {
                // Check for exist price in data_1
                $data['price_type'] = $CMS->class->input->currency($data['price_type']);
                
                if(isset($output_report['data_1'][$data['price_type']]))
                {
                    $output_report['data_1'][$data['price_type']]['total_product'] = $data['qty'];
                }
            }
            
            // New session for export data
            $_SESSION['export_product_price'] = $output_report['data_1'];
        }
        
        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_time']}\", \"{$CMS->lang['title_order']}\"]";
        $x = 1;
        
        if(!empty($arr_chart))
        {
            foreach ($arr_chart as $key => $value)
            {
                $chart[] = "[\"{$key}\", {$value['qty']}]";
            }
            
            $data['str_chart'] = "[".implode($chart, ',')."]";
        }
        else
        {
            $data['str_chart'] = '';
        }
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_product_follow_price']}";
        $data['title_chart']['title_unit_y'] = "";
        $data['type_chart'] = "pie";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report, $data['type_chart']);
    }
    // End report product follow range price
    
    // Report assets follow range price
    static function report_assets_price($time_from=0, $time_to=0, $view_type = "",$pgroup=0, $range=0, $price_type=0)
    {
        global $CMS, $DB;

        // Check time
        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check product group
        if($pgroup) {$clause .= " AND pgroup_id='{$pgroup}' ";}
        
        // Check price type
        $price_type = $price_type == 1 ? "ass_price" : "ass_original_price";

        // Check range price
        $range = $range > 0 ? $range : 1;

        // Get view type
        $datef = self::getTypeView("view_day");
        
        // Input
        $arr_chart = array();
        $output_report['data_1'] = [];
        
        // Link for export
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&pgroup={$pgroup}&price_type={$price_type}&range={$range}&datefm={$datef}&type_report=assets_price";
        if($datef)
        {
            // Get total product sold follow clause
            $sql = $DB->query("SELECT {$price_type} as price_type, count(ordi_id) as qty,sum(ordi_total) as total FROM ".root_table."assets as A left join ".root_table."order_item as I on A.ass_id=I.ass_id left join ".root_table."order as O on I.ord_id=O.ord_id WHERE ord_deleted = 0 AND ord_status=2 {$clause} GROUP BY {$price_type} ORDER BY {$price_type} ASC");
            if($DB->num_rows($sql) > 0)
            {
                $i = 0;
                while ($result = $DB->fetch_array($sql))
                {
                    // Convert to int
                    $result['total'] = $CMS->class->input->currency(intval($result['total']));
                    
                    // Check order total is in range price
                    if($result['price_type']%$range == 0)
                    {
                        // Data for chart
                        $result['price_type'] = $CMS->class->input->currency($result['price_type']);
                        $arr_chart[$result['price_type']]['qty'] = $result['qty'];
                        
                        // Data for list
                        $output_report['data_1'][$result['price_type']]['order'] = $i+1;
                        $output_report['data_1'][$result['price_type']]['price_type'] = $result['price_type'];
                        $output_report['data_1'][$result['price_type']]['total_qty_sold'] = $result['qty'];
                        $output_report['data_1'][$result['price_type']]['total_price'] = $result['total'];
                        $output_report['data_1'][$result['price_type']]['total_product'] = 0;
                        
                        $i++;
                    }
                }
            }
            
            // Get total product in range price for compare with data_1
            $sql = $DB->query("SELECT {$price_type} as price_type, count(ass_id) as qty FROM ".root_table."assets WHERE ".($pgroup ? " pgroup_id={$pgroup}" : "1=1")." GROUP BY {$price_type} ORDER BY {$price_type} ASC");
            while($data = $DB->fetch_array($sql))
            {
                // Check for exist price in data_1
                $data['price_type'] = $CMS->class->input->currency($data['price_type']);
                
                if(isset($output_report['data_1'][$data['price_type']]))
                {
                    $output_report['data_1'][$data['price_type']]['total_product'] = $data['qty'];
                }
            }
            
            // New session for export data
            $_SESSION['export_assets_price'] = $output_report['data_1'];
        }
        
        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_time']}\", \"{$CMS->lang['title_order']}\"]";
        $x = 1;
        
        if(!empty($arr_chart))
        {
            foreach ($arr_chart as $key => $value)
            {
                $chart[] = "[\"{$key}\", {$value['qty']}]";
            }
            
            $data['str_chart'] = "[".implode($chart, ',')."]";
        }
        else
        {
            $data['str_chart'] = '';
        }
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_assets_follow_price']}";
        $data['title_chart']['title_unit_y'] = "";
        $data['type_chart'] = "pie";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report, $data['type_chart']);
    }
    // End report product follow range price
    
    // Report product follow date
    static function report_product_date($time_from=0, $time_to=0, $view_type = "",$store = "",$product="",$p_status="")
    {
        global $CMS, $DB;

        // Check time
        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check store
        if($store) {$clause .= " AND I.store_id='{$store}' ";}
        
        // Check product
        if($product) {$clause .= " AND product_name = '{$product}' ";}
        
        // Check product status
        if($p_status) {$clause .= " AND product_status='{$p_status}' ";}
        
        // Get view type
        $datef = self::getTypeView($view_type);
        
        // Input
        $arr_chart = array();
        $output_report['data_1'] = [];
        
        // Link for export
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&store={$store}&product=".  urlencode($product)."&p_status={$p_status}&datefm={$datef}&type_report=product_date";
        if($datef AND $product)
        {
            // Get data
            $sql = $DB->query("SELECT DATE_FORMAT(FROM_UNIXTIME(ordi_time),'{$datef}') AS datefm, count(ordi_id) as qty, product_code, P.product_id,product_name,product_price,product_price_sell,sum(ordi_total) as total,product_price_original FROM ".root_table."order_item as I left join ".root_table."product as P on P.product_id=I.product_id left join ".root_table."order as O on I.ord_id=O.ord_id WHERE ord_deleted = 0 AND ord_status=2 {$clause} GROUP BY datefm ORDER BY datefm ASC");
            if($DB->num_rows($sql) > 0)
            {
                $i = 0;
                while ($result = $DB->fetch_array($sql))
                {
                    // Data for excel
                    $arr_chart[$result['datefm']]['qty'] = $result['qty'];
                    
                    // Data for list
                    $output_report['data_1'][$i]['order'] = $i+1;
                    $output_report['data_1'][$i]['datefm'] = $result['datefm'];
                    
                    // Date full for action search detail list
                    list($time_search_from,$time_search_to) = self::get_search_time($time_from,$time_to,$result['datefm'],$view_type);
                    $output_report['data_1'][$i]['link_search'] = "/?site=order&status=2&store_id={$store_id}&time_from={$time_search_from}&time_to={$time_search_to}"; 
                    // End
                    
                    $output_report['data_1'][$i]['product_code'] = $result['product_code'];
                    $output_report['data_1'][$i]['product_name'] = "<a href='{$CMS->vars['root_domain']}/?site=product&act=show&id={$result['product_id']}'>".$result['product_name']."</a>";
                    $output_report['data_1'][$i]['qty'] = $result['qty'];
                    $output_report['data_1'][$i]['product_price'] = $CMS->class->input->currency(intval($result['product_price_original']));
                    $output_report['data_1'][$i]['product_price_sell'] = $CMS->class->input->currency(intval($result['product_price_sell']));
                    $output_report['data_1'][$i]['total'] = $CMS->class->input->currency(intval($result['total']));
                    $output_report['data_1'][$i]['profit'] = $CMS->class->input->currency($result['total'] - $result['product_price_original']*$result['qty']);
                        
                    $i++;
                }
                
                // New session for export data
                $_SESSION['export_product_data'] = $output_report['data_1'];
            }
        }
        
        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_time']}\", \"{$CMS->lang['title_order']}\"]";
        $x = 1;
        
        // Chart 1
        if(!empty($arr_chart))
        {
            foreach ($arr_chart as $key => $value)
            {
                $chart[] = "[\"{$key}\", {$value['qty']}]";
            }
            
            $data['str_chart'] = "[".implode($chart, ',')."]";
        }
        else
        {
           $data['str_chart'] = ''; 
        }
        // End chart 1
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_product_follow_date']}";
        $data['title_chart']['title_unit_y'] = "";
        $data['type_chart'] = "";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report, $data['type_chart']);
    }
    // End report product follow date
    
    // Report assets follow date
    static function report_assets_date($time_from=0, $time_to=0, $view_type = "",$store = "",$ass_name="", $ass_id, $ass_avaiable="")
    {
        global $CMS, $DB;

        // Check time
        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check store
        if($store) {$clause .= " AND I.store_id='{$store}' ";}
        
        // Check product
        if($ass_id && $ass_name) {$clause .= " AND I.ass_id = '{$ass_id}' AND ass_name='{$ass_name}' ";}
        
        // Check product status
        if($ass_avaiable) {$clause .= " AND is_available='{$ass_avaiable}' ";}
        
        // Get view type
        $datef = self::getTypeView($view_type);
        
        // Input
        $arr_chart = array();
        $output_report['data_1'] = [];
        
        // Link for export
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&store={$store}&ass_name=".  urlencode($ass_name)."&ass_id={$ass_id}&ass_avaiable={$ass_avaiable}&datefm={$datef}&type_report=assets_date";
        if($datef AND $ass_id)
        {
            // Get data
            $sql = $DB->query("SELECT I.ass_id,DATE_FORMAT(FROM_UNIXTIME(ordi_time),'{$datef}') AS datefm, count(ordi_id) as qty, ass_code, ass_name,ass_price,ass_purchase_price,sum(ordi_total) as total,ass_original_price FROM ".root_table."order_item as I left join ".root_table."assets as A on A.ass_id=I.ass_id left join ".root_table."order as O on I.ord_id=O.ord_id WHERE ord_deleted = 0 AND ass_status=1 {$clause} GROUP BY datefm ORDER BY datefm ASC");
            if($DB->num_rows($sql) > 0)
            {
                $i = 0;
                while ($result = $DB->fetch_array($sql))
                {
                    // Data for excel
                    $arr_chart[$result['datefm']]['qty'] = $result['qty'];
                    
                    // Data for list
                    $output_report['data_1'][$i]['order'] = $i+1;
                    $output_report['data_1'][$i]['datefm'] = $result['datefm'];
                    
                    // Date full for action search detail list
                    list($time_search_from,$time_search_to) = self::get_search_time($time_from,$time_to,$result['datefm'],$view_type);
                    $output_report['data_1'][$i]['link_search'] = "/?site=order&status=2&store_id={$store_id}&time_from={$time_search_from}&time_to={$time_search_to}"; 
                    // End
                    
                    $output_report['data_1'][$i]['product_code'] = $result['ass_code'];
                    $output_report['data_1'][$i]['product_name'] = "<a href='{$CMS->vars['root_domain']}/?site=assets&act=show&id={$result['ass_id']}'>".$result['ass_name']."</a>";
                    $output_report['data_1'][$i]['qty'] = $result['qty'];
                    $output_report['data_1'][$i]['product_price'] = $CMS->class->input->currency(intval($result['ass_original_price']));
                    $output_report['data_1'][$i]['product_price_sell'] = $CMS->class->input->currency(intval($result['ass_price']));
                    $output_report['data_1'][$i]['total'] = $CMS->class->input->currency(intval($result['total']));
                    $output_report['data_1'][$i]['profit'] = $CMS->class->input->currency($result['total'] - $result['ass_original_price']*$result['qty']);
                        
                    $i++;
                }
                
                // New session for export data
                $_SESSION['export_assets_date'] = $output_report['data_1'];
            }
        }
        
        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_time']}\", \"{$CMS->lang['title_order']}\"]";
        $x = 1;
        
        // Chart 1
        if(!empty($arr_chart))
        {
            foreach ($arr_chart as $key => $value)
            {
                $chart[] = "[\"{$key}\", {$value['qty']}]";
            }
            
            $data['str_chart'] = "[".implode($chart, ',')."]";
        }
        else
        {
           $data['str_chart'] = ''; 
        }
        // End chart 1
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_assets_follow_date']}";
        $data['title_chart']['title_unit_y'] = "";
        $data['type_chart'] = "";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report, $data['type_chart']);
    }
    // End report assets follow date

    static function report_product($time_from=0, $time_to=0, $view_type = "")
    {
        global $CMS, $DB;

        if($time_from and $time_to)
        {
            $clause = " AND ass_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ass_time > '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ass_time < '{$time_to}' ";
        }else
        {
            $clause = "";
        }

        $datef = self::getTypeView($view_type);
        $arr_chart = array();
        $output_report['data_1'] = [];
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&datefm={$datef}&type_report=store_product";
        if($datef)
        {
            // Thống kê cho biểu đồ group theo thời gian
            $sql = $DB->query("SELECT  ass_name, COUNT(ass_id) as qty, ass_key, S.store_name FROM ".root_table."assets as A LEFT JOIN ".root_table."store as S ON A.store_id = S.store_id WHERE ass_deleted = 0 AND is_available = 1 AND ass_time <= '{$time_to}' GROUP BY ass_key ORDER BY A.store_id ASC");
            
            if($DB->num_rows($sql) > 0)
            {
                $i = 0;
                while ($result = $DB->fetch_array($sql)) 
                {
                    $key_check = -1;
                    foreach ($arr_chart as $key => $data) 
                    {
                        if($data['ass_name'] == $result['ass_name'])
                        {
                            $key_check = $key;
                        }
                    }


                    if($key_check >= 0)
                    {
                        $arr_chart[$key_check]['quantity'] += $result['qty'];
                    }else
                    {
                        $arr_chart[$i]['quantity'] = $result['qty'];
                        $arr_chart[$i]['ass_name'] = $result['ass_name'];
                    }
                    $ass_name = "<a href='{$CMS->vars['root_domain']}/?site=assets&ass_name={$result['ass_name']}'>{$result['ass_name']}</a>";

                    $output_report['data_1'][$i]['store_name'] = $result['store_name'];
                    $output_report['data_1'][$i]['ass_name'] = $ass_name;
                    $output_report['data_1'][$i]['quantity'] = $result['qty'];

                    $i++;
                }
                
            }

        }

        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_product_name']}\", \"{$CMS->lang['title_number_assets']}\"]"; // Khai bao
        $x = 1;
        foreach ($arr_chart as $key => $value) 
        {
            $chart[] = "[\"{$value['ass_name']}\", {$value['quantity']}]";
        }
        
        if(count($chart) == 1)
        {
            $day_crr = date("d-m");
            $chart[1] = "[\"{$day_crr}\",0]";
        }
        $data['str_chart'] = "[".implode($chart, ',')."]"; 
        $data['title_chart']['main_title'] = "{$CMS->lang['title_assets_inventory']}";
        $data['title_chart']['title_unit_y'] = "";

        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report);
    }

    static function report_customers($time_from=0, $time_to=0, $view_type = "")
    {
        global $CMS, $DB;

        if($time_from and $time_to)
        {
            $clause = " AND C.cus_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND C.cus_time > '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND C.cus_time < '{$time_to}' ";
        }else
        {
            $clause = "";
        }

        $datef = self::getTypeView($view_type);
        $arr_chart = array();
        $output_tr = "";
        $output_report['data_1'] = [];
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&datefm={$datef}&type_report=customer_time";
        if($datef)
        {
            // Thống kê cho biểu đồ group theo thời gian
            $sql = $DB->query("SELECT COUNT(C.cus_id) as qty, DATE_FORMAT(FROM_UNIXTIME(C.cus_time),'{$datef}') AS datefm FROM ".root_table."customer as C WHERE C.cus_deleted = 0 {$clause} GROUP BY datefm ORDER BY C.cus_time ASC");
            
            if($DB->num_rows($sql) > 0)
            {
                $i=0;
                while ($result = $DB->fetch_array($sql)) 
                {
                    $arr_chart[$result['datefm']] = $result['qty'];
                    $output_report['data_1'][$i]['datefm'] = $result['datefm'];
                    $output_report['data_1'][$i]['quantity'] = $result['qty'];

                    $i++;
                }
            }

        }

        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_time']}\", \"{$CMS->lang['title_number_customer']}\"]"; // Khai bao
        
        foreach ($arr_chart as $key => $value) 
        {
            $chart[] = "[\"{$key}\", {$value}]";
        }
        
        if(count($chart) == 1)
        {
            $day_crr = date("d-m");
            $chart[1] = "[\"{$day_crr}\",0]";
        }
        $data['str_chart'] = "[".implode($chart, ',')."]"; 
        $data['title_chart']['main_title'] = "{$CMS->lang['title_quantity_customer']}";
        $data['title_chart']['title_unit_y'] = "";

        // Thống kê theo khách hàng
        $output_tr = "";
        $sql = $DB->query("SELECT C.cus_id, C.cus_full_name, COUNT(O.ord_id) as qty, SUM(O.ord_total) as total FROM ".root_table."customer AS C LEFT JOIN ".root_table."order AS O ON C.cus_id = O.cus_id WHERE C.cus_deleted = 0 AND O.ord_deleted = 0 {$clause} GROUP BY C.cus_id ORDER BY C.cus_time ASC");
        $output_report['data_2'] = [];
        $output_report['link_export_2'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&datefm={$datef}&type_report=customer_total";
        if($DB->num_rows($sql) > 0)
        {
            $i=0;
            while ($result = $DB->fetch_array($sql)) 
            {
                $total = input::currency($result['total'], 2);
                if($result['cus_id'])
                {
                    $cus_name = "<a href='{$CMS->vars['root_domain']}/?site=customer&act=show&id={$result['cus_id']}'>{$result['cus_full_name']}</a>";
                }else
                {
                    $cus_name = "";
                }
                $output_report['data_2'][$i]['cus_name'] = $cus_name;
                $output_report['data_2'][$i]['quantity'] = $result['qty'];
                $output_report['data_2'][$i]['amount'] = $total;

                $i++;

            }
        }

        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report);
    }

    static function report_finance($time_from=0, $time_to=0, $view_type = "")
    {
        global $CMS, $DB;

        if($time_from and $time_to)
        {
            $clause = " AND trx_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND trx_time > '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND trx_time < '{$time_to}' ";
        }else
        {
            $clause = "";
        }

        $datef = self::getTypeView($view_type);
        $arr_chart = array();

        // Thống kê cho biểu đồ group theo thời gian
        $sql = $DB->query("SELECT SUM(trx_total) as total, DATE_FORMAT(FROM_UNIXTIME(trx_time),'{$datef}') AS datefm FROM ".root_table."transaction WHERE trx_deleted = 0 AND trx_subtype IN (2,3) AND trx_status IN (2,3) {$clause} GROUP BY datefm ORDER BY trx_time ASC");
        $output_report['data_1'] = [];
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&datefm={$datef}&type_report=revenue";
        if($DB->num_rows($sql) > 0)
        {
            $i=0;
            while ($result = $DB->fetch_array($sql)) 
            {
                $arr_chart[$result['datefm']]= $result['total'];
                $amount = input::currency($result['total'], 2);
                $output_report['data_1'][$i]['datefm'] = $result['datefm'];
                $output_report['data_1'][$i]['amount'] = $amount;

                $i++;
            }
        }

        
        // Thống kê cho biểu đồ group theo thời gian
        $sql = $DB->query("SELECT SUM(trx_total) as total, DATE_FORMAT(FROM_UNIXTIME(trx_time),'{$datef}') AS datefm FROM ".root_table."transaction WHERE trx_deleted = 0 AND trx_subtype IN (6,7) AND trx_status IN (2,3) {$clause} GROUP BY datefm ORDER BY trx_time ASC");
        $arr_chart1 = array();
        $output_report['data_2'] = [];
        $output_report['link_export_2'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&datefm={$datef}&type_report=report_cost";
        if($DB->num_rows($sql) > 0)
        {
            $i=0;
            while ($result = $DB->fetch_array($sql)) 
            {
                $arr_chart1[$result['datefm']]= $result['total'];
                $amount = input::currency($result['total'], 2);
                $output_report['data_2'][$i]['datefm'] = $result['datefm'];
                $output_report['data_2'][$i]['amount'] = $amount;

                $i++;
            }
        }

        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_time']}\", \"{$CMS->lang['title_report_revenue']}\", \"{$CMS->lang['title_report_costs']}\"]"; // Khai bao
        $x = 1;
        $count_arr_1 = count($arr_chart);
        $count_arr_2 = count($arr_chart1);
        if($count_arr_1 > $count_arr_2)
        {
            foreach ($arr_chart as $key => $value) 
            {
                $value_1 = round($value/1000000,2);
                $value_2 = round($arr_chart1[$key]/1000000,2);
                $chart[] = "[\"{$key}\", {$value_1}, {$value_2}]";
            }
        }else
        {
            foreach ($arr_chart1 as $key => $value2) 
            {
                $value_1 = round($arr_chart[$key]/1000000, 2);
                $value_2 = round($value2/1000000, 2);
                $chart[] = "[\"{$key}\", {$value_1}, {$value_2}]";
            }
        }
        
        
        if(count($chart) == 1)
        {
            $day_crr = date("d-m");
            $chart[1] = "[\"{$day_crr}\",0,0]";
        }
        $data['str_chart'] = "[".implode($chart, ',')."]"; 
        $data['title_chart']['main_title'] = "{$CMS->lang['title_revenue_cost']}";
        $data['title_chart']['title_unit_y'] = $CMS->lang['title_unit_chart'];

        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report);
    }


    /***************************************************************************************/
    static function excel_header()
    {
        global $CMS;
        // $CMS->report->test_report();
        // require_once(root_path."acp/tools/excelphp/PHPExcel.php");
        require_once root_path."vendor/autoload.php";

        // Create new PHPExcel object
        self::$dataExcel = new PHPExcel();

        // Set document properties
        self::$dataExcel->getProperties()->setCreator("Maarten Balliauw")
                                     ->setLastModifiedBy("Maarten Balliauw")
                                     ->setTitle("Office 2007 XLSX Test Document")
                                     ->setSubject("Office 2007 XLSX Test Document")
                                     ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("Test result file");

        // Set default font
        self::$dataExcel->getDefaultStyle()->getFont()->setName('Arial')
                                                  ->setSize(10);
    }

    static function excel_title($arr_title=[], $count_row=1, $style=[], $title_sheet="Sheet 1",$is_supplier_merge=0)
    {
        global $CMS;
        // Format array
        // $arr_title=array("STT", "Name"...);
        // $count_row: number rows query
        
        if(is_array($arr_title))
        {
            $i=0;
            // Set basic style
            $number_col = $is_supplier_merge ? count($arr_title)*2 - 1: count($arr_title);
            $font_color = "000000";
            // $title_sheet = 'Danh sách liên hệ';
            $rowheight = 30;
            $background = 'cccccc';
            $border_color = '808080';
            $line_ranger = self::$col[0].'1:'.self::$col[$number_col - 1].'1';// Ex: A1:F1
            $circle_ranger = self::$col[0].'1:'.self::$col[$number_col - 1].$count_row; //Ex: A1:F6
            
            foreach ($arr_title as $key => $value)
            {
                $col_title = self::$col[$i].'1';
                
                // Set row title
                self::$dataExcel->getActiveSheet()->setCellValue($col_title, $value);

                // Format cell title
                self::$dataExcel->getActiveSheet()->getStyle($col_title)->getFont()->setBold(true)->setItalic(true)->setColor(new PHPExcel_Style_Color($font_color));
                self::$dataExcel->getActiveSheet()->getStyle($col_title)->getAlignment()->applyFromArray($style);
                
                if($is_supplier_merge == 1)
                {
                    // Set size column
                    self::$dataExcel->getActiveSheet()->getColumnDimension(self::$col[$i])->setAutoSize(true);

                    if($i > 0)
                    {
                        self::$dataExcel->getActiveSheet()->mergeCells(self::$col[$i].'1'.":".self::$col[$i+1].'1');
                        $i=$i+2;
                    }
                    else
                    {
                        $i++;
                    }
                }
                else
                {
                    // Set size column
                    self::$dataExcel->getActiveSheet()->getColumnDimension(self::$col[$i])->setAutoSize(true);
                    
                    $i++;
                }
                
            }
            
            
            
            // Set height 
            self::$dataExcel->getActiveSheet()->getRowDimension('1')->setRowHeight($rowheight);
            self::$dataExcel->getActiveSheet()->getStyle($line_ranger)->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => $background)
                        )
                    )
                );
            self::$dataExcel->getActiveSheet()->getStyle($circle_ranger)->getBorders()->applyFromArray(
                  array(
                      'allborders' => array(
                          'style' => PHPExcel_Style_Border::BORDER_THIN,
                          'color' => array(
                              'rgb' => $border_color
                            )
                        )
                    )
                );
            
            
            // Rename worksheet
            self::$dataExcel->getActiveSheet()->setTitle($title_sheet);
            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            self::$dataExcel->setActiveSheetIndex(0);
        }
    }

    static function excel_output($file_name="", $type="link")
    {
        global $CMS;

        if( $type == "link" )
        {
            $link_save = "{$CMS->vars['upload_dir']}/export/{$file_name}";
            $link_download = "{$CMS->vars['upload_url']}/export/{$file_name}";
            $CMS->class->image->check_folder_img("export","",0,"");
            $objWriter = PHPExcel_IOFactory::createWriter(self::$dataExcel, 'Excel5');
            $objWriter->save($link_save);

            // Return link
            return $link_download;
        }else
        {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$file_name.'"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter(self::$dataExcel, 'Excel5');
            $objWriter->save('php://output');
        }
    }

    static function export_assets_service()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_report_sale_num_order'], $CMS->lang['title_time'], $CMS->lang['title_report_sale_total_price'], $CMS->lang['title_report_sale_total_pprice'], $CMS->lang['title_report_sale_profit']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $pgroup = $CMS->input['p_group'];
        $store = $CMS->input['store'];
        $datef = $CMS->input['datef'];
        
        // Check session for export data
        if(isset($_SESSION['export_sales_date']))
        {
            $data = $_SESSION['export_sales_date'];
        }
        else
        {
            // Call function
            list($a,$b,$data) = self::report_sales($time_from, $time_to, '', $pgroup, $store);
            $data = $data['data_1'];
        }
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );


        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);

        // Loop data
        $i = 2;
        for($j=0;$j<count($data);$j++) 
        {
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $i-1)
                                              ->setCellValue('B'.$i, $data[$j]['date'])
                                              ->setCellValue('C'.$i, $CMS->class->input->currency($data[$j]['total_price_bk']))
                                              ->setCellValue('D'.$i, $CMS->class->input->currency($data[$j]['total_buy_bk']))
                                              ->setCellValue('E'.$i, $CMS->class->input->currency($data[$j]['profit_bk']));
            // Align center                                      
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);

            $i++;
        }
        // Set name file
        $file_name = "report_sales_date.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    static function export_report_sales_store()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_stt'], $CMS->lang['title_store'], $CMS->lang['title_report_sale_total_price'], $CMS->lang['title_report_sale_total_pprice'], $CMS->lang['title_report_sale_profit']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $pgroup = $CMS->input['p_group'];
        $store = $CMS->input['store'];
        $datef = $CMS->input['datef'];
        
        // Check session for export data
        if(isset($_SESSION['export_sales_store']))
        {
            $data = $_SESSION['export_sales_store'];
        }
        else
        {
            // Call function
            list($a,$b,$data) = self::report_sales_store($time_from, $time_to, $view_type, $p_group, $store_id);
            $data = $data['data_1'];
        }
        
        /*
        if($time_from and $time_to)
        {
            $clause = " AND ordi_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ordi_time > '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ordi_time < '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check for product group
        if($pgroup)
        {
            $clause .= " AND product_group='{$pgroup}' ";
        }
        
        // Check for store
        if($store)
        {
            $clause .= " AND store_id='{$store}' ";
        }
        */

        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);

        // Loop data
        $i = 2;
        $total_1 = 0;
        $total_2 = 0;
        $total_3 = 0;
                
        for($j=0;$j<count($data);$j++) 
        {
            $total_1 += intval($data[$j]['total_price_bk']);
            $total_2 += intval($data[$j]['total_buy_bk']);
            $total_3 += intval($data[$j]['total_price_bk']) - intval($data[$j]['total_buy_bk']);
            
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $i-1)
                                      ->setCellValue('B'.$i, $data[$j]['store'])
                                      ->setCellValue('C'.$i, $data[$j]['total_price'])
                                      ->setCellValue('D'.$i, $data[$j]['total_buy'])
                                      ->setCellValue('E'.$i, $data[$j]['profit']);
            // Align center                                      
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);

            $i++;
        }
        
        // Set last line
        self::$dataExcel->getActiveSheet()->setCellValue('B'.$i, "Total")
                                          ->setCellValue('C'.$i, $CMS->class->input->currency($total_1))
                                          ->setCellValue('D'.$i, $CMS->class->input->currency($total_2))  
                                          ->setCellValue('E'.$i, $CMS->class->input->currency($total_3));
        
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );
        
        self::$dataExcel->getActiveSheet()->getStyle("B".$i)->getAlignment()->applyFromArray($style);
        
        // Set name file
        $file_name = "report_sales_store.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    static function export_report_sales_product()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_report_sale_num_order'], $CMS->lang['title_product'], $CMS->lang['title_pgroup'], $CMS->lang['title_quantity'], $CMS->lang['title_report_sale_total_price'], $CMS->lang['title_report_sale_total_pprice'], $CMS->lang['title_report_sale_profit']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $pgroup = $CMS->input['p_group'];
        $store = $CMS->input['store'];
        $datef = $CMS->input['datef'];
        $product = trim($CMS->input['product']);
        $price_from = intval($CMS->input['price_from']);
        $price_to = intval($CMS->input['price_to']);
        $supplier = intval($CMS->input['supplier']);
        $user = intval($CMS->input['user']);
        
        // Check session for export data
        if(isset($_SESSION['export_sales_product']))
        {
            $data = $_SESSION['export_sales_product'];
        }
        else
        {
            // Call function again
            list($a,$b,$data) = self::report_sales_product($time_from, $time_to, '', $pgroup, $store, $product, $price_from, $price_to, $supplier, $user);
            $data = $data['data_1'];
        }
        
        /*
        if($time_from and $time_to)
        {
            $clause = " AND ordi_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ordi_time > '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ordi_time < '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check for product group
        if($pgroup){$clause .= " AND product_group='{$pgroup}' ";}
        
        // Check for store
        if($store){$clause .= " AND store_id='{$store}' ";}
        
        // Check product name
        if($product){$clause .= " AND ordi_name like '%{$product}%' ";}
        
        // Check price from
        if($price_from){$clause .= " AND ordi_total >= '{$price_from}' ";}
        
        // Check price to
        if($price_to){$clause .= " AND ordi_total <= '{$price_to}' ";}
        
        // Check supplier
        if($supplier){$clause .= " AND sup_id='{$supplier}' ";}

        // Check user
        if($user){$clause .= " AND O.user_id='{$user}' ";}
        */
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);

        // Loop data
        $i = 2;
        $total_1 = 0;
        $total_2 = 0;
        $total_3 = 0;
                
        for($j=0;$j<count($data);$j++) 
        {
            $total_1 += intval($data[$j]['total_price_bk']);
            $total_2 += intval($data[$j]['total_buy_bk']);
            $total_3 += intval($data[$j]['total_price_bk']) - intval($data[$j]['total_buy_bk']);
            $qty += intval($data[$j]['qty']);
            
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $i-1)
                                      ->setCellValue('B'.$i, html_entity_decode($data[$j]['ordi_name']))
                                      ->setCellValue('C'.$i, html_entity_decode($data[$j]['group']))  
                                      ->setCellValue('D'.$i, $data[$j]['qty'])  
                                      ->setCellValue('E'.$i, $data[$j]['total_price'])
                                      ->setCellValue('F'.$i, $data[$j]['total_buy'])
                                      ->setCellValue('G'.$i, $data[$j]['profit']);
            // Align center                                      
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);

            $i++;
        }
        
        // Set last line
        self::$dataExcel->getActiveSheet()->setCellValue('C'.$i, "Total")
                                          ->setCellValue('D'.$i, $qty)      
                                          ->setCellValue('E'.$i, $CMS->class->input->currency($total_1))
                                          ->setCellValue('F'.$i, $CMS->class->input->currency($total_2))  
                                          ->setCellValue('G'.$i, $CMS->class->input->currency($total_3));
        
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );
        
        self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
        
        // Set name file
        $file_name = "report_sales_product.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    static function export_report_sales_assets()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_report_sale_num_order'], $CMS->lang['title_assets_name'], $CMS->lang['title_pgroup'], $CMS->lang['title_quantity'], $CMS->lang['title_report_sale_total_price'], $CMS->lang['title_report_sale_total_pprice'], $CMS->lang['title_report_sale_profit']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $pgroup = $CMS->input['p_group'];
        $store = $CMS->input['store'];
        $datef = $CMS->input['datef'];
        $ass_name = trim($CMS->input['ass_name']);
        $price_from = intval($CMS->input['price_from']);
        $price_to = intval($CMS->input['price_to']);
        $supplier = intval($CMS->input['supplier']);
        $user = intval($CMS->input['user']);
        
        // Check session for export data
        if(isset($_SESSION['export_sales_assets']))
        {
            $data = $_SESSION['export_sales_assets'];
        }
        else
        {
            // Call function again
            list($a,$b,$data) = self::report_sales_assets($time_from, $time_to, '', $pgroup, $store, $ass_name, $price_from, $price_to, $supplier, $user);
            $data = $data['data_1'];
        }
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);

        // Loop data
        $i = 2;
        $total_1 = 0;
        $total_2 = 0;
        $total_3 = 0;
                
        for($j=0;$j<count($data);$j++) 
        {
            print_r("adadsad");exit;
            $total_1 += intval($data[$j]['total_price_bk']);
            $total_2 += intval($data[$j]['total_buy_bk']);
            $total_3 += intval($data[$j]['total_price_bk']) - intval($data[$j]['total_buy_bk']);
            $qty += intval($data[$j]['qty']);
            
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $i-1)
                                      ->setCellValue('B'.$i, html_entity_decode($data[$j]['ordi_name']))
                                      ->setCellValue('C'.$i, html_entity_decode($data[$j]['group']))  
                                      ->setCellValue('D'.$i, $data[$j]['qty'])  
                                      ->setCellValue('E'.$i, $data[$j]['total_price'])
                                      ->setCellValue('F'.$i, $data[$j]['total_buy'])
                                      ->setCellValue('G'.$i, $data[$j]['profit']);
            // Align center                                      
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);

            $i++;
        }
        
        // Set last line
        self::$dataExcel->getActiveSheet()->setCellValue('C'.$i, "Total")
                                          ->setCellValue('D'.$i, $qty)      
                                          ->setCellValue('E'.$i, $CMS->class->input->currency($total_1))
                                          ->setCellValue('F'.$i, $CMS->class->input->currency($total_2))  
                                          ->setCellValue('G'.$i, $CMS->class->input->currency($total_3));
        
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );
        
        self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
        
        // Set name file
        $file_name = "report_sales_assets.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    static function export_report_sales_pgroup()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_report_sale_num_order'], $CMS->lang['title_pgroup'], $CMS->lang['title_quantity'], $CMS->lang['title_report_sale_total_price'], $CMS->lang['title_report_sale_total_pprice'], $CMS->lang['title_report_sale_profit']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $pgroup = $CMS->input['p_group'];
        $store = $CMS->input['store'];
        $datef = $CMS->input['datef'];
        
        // Check session for export data
        if(isset($_SESSION['export_sales_pgroup']))
        {
            $data = $_SESSION['export_sales_pgroup'];
        }
        else
        {
            // Call function
            list($a,$b,$data) = self::report_sales_pgroup($time_from, $time_to, '', $pgroup, $store);
            $data = $data['data_1'];
        }
        
        /*
        if($time_from and $time_to)
        {
            $clause = " AND ordi_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ordi_time > '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ordi_time < '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check for product group
        if($pgroup){$clause .= " AND product_group='{$pgroup}' ";}
        
        // Check for store
        if($store){$clause .= " AND store_id='{$store}' ";}
        */
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);

        // Loop data
        $i = 2;
        $total_1 = 0;
        $total_2 = 0;
        $total_3 = 0;
                
        for($j=0;$j<count($data);$j++) 
        {
            $total_1 += intval($data[$j]['total_price_bk']);
            $total_2 += intval($data[$j]['total_buy_bk']);
            $total_3 += intval($data[$j]['total_price_bk']) - intval($data[$j]['total_buy_bk']);
            
            if($data[$j]['group'])
            {
                $qty += intval($data[$j]['qty']);
            
                self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $i-1)
                                      ->setCellValue('B'.$i, $data[$j]['group'])  
                                      ->setCellValue('C'.$i, $data[$j]['qty'])  
                                      ->setCellValue('D'.$i, $data[$j]['total_price'])
                                      ->setCellValue('E'.$i, $data[$j]['total_buy'])
                                      ->setCellValue('F'.$i, $data[$j]['profit']);
                // Align center                                      
                self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);

                $i++;            
            }
            
            
        }
        
        // Set last line
        self::$dataExcel->getActiveSheet()->setCellValue('B'.$i, "Total")
                                          ->setCellValue('C'.$i, $qty)      
                                          ->setCellValue('D'.$i, $CMS->class->input->currency($total_1))
                                          ->setCellValue('E'.$i, $CMS->class->input->currency($total_2))  
                                          ->setCellValue('F'.$i, $CMS->class->input->currency($total_3));
        
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );
        
        self::$dataExcel->getActiveSheet()->getStyle("B".$i)->getAlignment()->applyFromArray($style);
        
        // Set name file
        $file_name = "report_sales_product.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    static function export_report_sales_supplier()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_supplier'], $CMS->lang['title_report_sale_total_price']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $pgroup = $CMS->input['p_group'];
        $store = $CMS->input['store'];
        $datef = $CMS->input['datef'];
        
        // Check session for export data
        if(isset($_SESSION['export_sales_supplier']))
        {
            $data = $_SESSION['export_sales_supplier'];
        }
        else
        {
            // Call function again
            list($a,$b,$data,$list_days,$c,$d) = self::report_sales_supplier($time_from, $time_to, $view_type, $p_group, $store_id, $product, $supplier);
            $data = $data['data_1'];
        }
        
        // Get all day time from - time to
        $day = array();
        if(isset($_SESSION['list_days']))
        {
            $day = $_SESSION['list_days'];
        }
        else if($time_from <= $time_to)
        {
            while($time_from < $time_to)
            {
                $start = date("d-m-Y",$time_from);
                $time_from = $time_from + 24*3600;
                $day[] = $start;
            }
            
            $day[] = date("d-m-Y",$time_to);
        }
        // End

        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );
        
        // Get array cols
        $col = str_split(self::$col);
        $col_exp = explode(" ",  self::$col_expand);
        $col_exp_2 = explode(" ",  self::$col_expand_2);
        
        // Merge cols
        self::$col = $col = array_merge($col,$col_exp);
        self::$col = array_merge(self::$col,$col_exp_2);
        $count_cols = count(self::$col); // Set cols > days because 1 days has 2 col
        
        //print_r(self::$col);exit;
        
        // Set for title days
        for($j=0;$j<($count_cols);$j++) // Limit data cols is follow array cols
        {
            if(!$day[$j])
            {
                break;
            }
            
            $arr_title[] = $day[$j]; 
        } 
        
        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style,"Sheet 1",1);
        
        // Loop data
        $i = 2;
        
        $total = array();
        
        // Loop for data follow date
        foreach ($data as $key => $value)
        {
            // Set column total
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i,$key)
                                              ->setCellValue('B'.$i, $CMS->class->input->currency($value['all']['total']))  
                                              ->setCellValue('C'.$i, $CMS->class->input->currency($value['all']['profit']));
            $total['all']['total'] = $total['all']['total'] ? ($total['all']['total'] + $value['all']['total']) : intval($value['all']['total']);
            $total['all']['profit'] = $total['all']['profit'] ? ($total['all']['profit'] + $value['all']['total']) : intval($value['all']['total']);
            
            $p = 0;
            
            // Set column for each day
            for($k=0;$k<count($day);$k++)
            {
                if($value[$day[$k]])
                {
                    self::$dataExcel->getActiveSheet()->setCellValue(self::$col[$p+3].$i,$CMS->class->input->currency($value[$day[$k]]['total']))
                                                    ->setCellValue(self::$col[$p+4].$i,$CMS->class->input->currency($value[$day[$k]]['profit']));
                  
                    $total[$day[$k]]['total'] = $total[$day[$k]]['total'] ? ($total[$day[$k]]['total'] + $value[$day[$k]]['total']) : intval($value[$day[$k]]['total']);
                    $total[$day[$k]]['profit'] = $total[$day[$k]]['profit'] ? ($total[$day[$k]]['profit'] + $value[$day[$k]]['profit']) : intval($value[$day[$k]]['profit']);

                }
                else
                {
                    self::$dataExcel->getActiveSheet()->setCellValue(self::$col[$p+3].$i,'')
                                                      ->setCellValue(self::$col[$p+4].$i,'');
                }
              
                $p=$p+2;
            }
            $i++;
        }
        
        // Build last line
        self::$dataExcel->getActiveSheet()->setCellValue('B'.$i,$CMS->class->input->currency($total['all']['total']))
                                          ->setCellValue('C'.$i,$CMS->class->input->currency($total['all']['profit']));
        
        // Unset all
        unset($total['all']);
        
        $p=3;
        $exist = 0;
        
        // Loop for days
        for($k=0;$k<count($day);$k++)
        {
            foreach($total as $key => $value)
            {
                if($key == $day[$k])
                {
                    self::$dataExcel->getActiveSheet()->setCellValue(self::$col[$p].$i,$CMS->class->input->currency($value['total']))
                                                      ->setCellValue(self::$col[$p+1].$i,$CMS->class->input->currency($value['profit']));
                
                    $exist = 1;
                    break;
                }
            } 
            
            if($exist == 0)
            {
                self::$dataExcel->getActiveSheet()->setCellValue(self::$col[$p].$i,'')
                                                  ->setCellValue(self::$col[$p+1].$i,'');
            }
            
            $p=$p+2;
        }
        
        // Set name file
        $file_name = "report_sales_supplier.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }

    
    static function export_product_store()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_product_code'],$CMS->lang['title_product_name'], $CMS->lang['title_price'], $CMS->lang['title_cost']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $pgroup = $CMS->input['pgroup'];
        $store = $CMS->input['store'];
        $supplier = $CMS->input['supplier'];
        
        // Check session for export data
        if(isset($_SESSION['export_product_store']))
        {
            $data = $_SESSION['export_product_store'];
        }
        else
        {
            // Call function again
            list($a,$b,$data,$c) = self::report_product_store($time_from, $time_to, '', $store, $pgroup, $product, $supplier);
            $data = $data['data_1'];
        }
        
        /*
        if($time_from and $time_to)
        {
            $clause = " AND ordi_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ordi_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ordi_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check for product group
        if($pgroup){$clause .= " AND product_group='{$pgroup}' ";}
        
        // Check for store
        if($store){$clause .= " AND O.store_id IN ({$store}) ";}
        
        // Check for product
        if($product) { $clause .= " AND product_name like '%{$product}%' "; }
        
        // Check for supplier
        if($supplier) {$clause .= " AND sup_id='{$supplier}' ";}
        */
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );
        
        // Set for title store
        if($store)
        {
            $store = explode(",", $store);
            
            for($j=0;$j<count($store);$j++) // Limit data cols is follow array cols
            {
                if($store[$j])
                {
                    // Convert to store name
                    $arr_title[] = $CMS->store->get_info($store[$j],"store_name");
                }
            }   
        }
             
        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);
        
        // Get array data
        /*while ($result = $DB->fetch_array($sql)) 
        {
            if(isset($temp[$result['product_name']]))
            {
                if(isset($temp[$result['product_name']][$result['store_id']]))
                {
                    $temp[$result['product_name']][$result['store_id']]+= $result['qty'];
                }
                else
                {
                    $temp[$result['product_name']][$result['store_id']] = intval($result['qty']);
                }
            }
            else
            {
                $temp[$result['product_name']] = array(
                                            $result['store_id'] => intval($result['qty']), 
                                            "name" => $result['product_name'],
                                            "code" => $result['product_code'],
                                            "product_price" => $result['product_price'],
                                            "product_price_sell" => $result['product_price_sell'],
                                );
            }
        }*/
        
        // Loop data
        $i = 2;
        $col = str_split(self::$col);
        // Loop for data excel
        foreach ($data as $key => $value)
        {
            // Set column total
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $value['code'])
                                              ->setCellValue('B'.$i, $value['name'])  
                                              ->setCellValue('C'.$i, $value['product_price'])
                                              ->setCellValue('D'.$i, $value['product_price_sell'])
                                                ;
            
            // Set column for each store
            $p=0;
            for($k=0;$k<count($store);$k++)
            {
              if($value[$store[$k]])
              {
                  self::$dataExcel->getActiveSheet()->setCellValue($col[$p+4].$i,$value[$store[$k]]);
              }
              else
              {
                  self::$dataExcel->getActiveSheet()->setCellValue($col[$p+4].$i,'');
              }
              $p++;
            }
            $i++;
        }
        
        // Set name file
        $file_name = "report_product_store.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    static function export_assets_store()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_assets_code'],$CMS->lang['title_assets_name'], $CMS->lang['title_cost'], $CMS->lang['title_price']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $pgroup = $CMS->input['pgroup'];
        $store = $CMS->input['store'];
        $supplier = $CMS->input['supplier'];
        $ass_name = $CMS->input['ass_name'];
        $ass_id = $CMS->input['ass_id'];
        
        // Check session for export data
        if(isset($_SESSION['export_assets_store']))
        {
            $data = $_SESSION['export_assets_store'];
        }
        else
        {
            // Call function again
            list($a,$b,$data,$c) = self::report_assets_store($time_from, $time_to, '', $store, $pgroup, $ass_name, $ass_id, $supplier);
            $data = $data['data_1'];
        }
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );
        
        // Set for title store
        if($store)
        {
            $store = explode(",", $store);
            
            for($j=0;$j<count($store);$j++) // Limit data cols is follow array cols
            {
                if($store[$j])
                {
                    // Convert to store name
                    $arr_title[] = $CMS->store->get_info($store[$j],"store_name");
                }
            }   
        }
             
        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);
        
        // Loop data
        $i = 2;
        $col = str_split(self::$col);
        // Loop for data excel
        foreach ($data as $key => $value)
        {
            // Set column total
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $value['code'])
                                              ->setCellValue('B'.$i, strip_tags($value['name']))
                                              ->setCellValue('C'.$i, $value['product_price'])
                                              ->setCellValue('D'.$i, $value['product_price_sell'])
                                                ;
            
            // Set column for each store
            $p=0;
            for($k=0;$k<count($store);$k++)
            {
              if($value[$store[$k]])
              {
                  self::$dataExcel->getActiveSheet()->setCellValue($col[$p+4].$i,$value[$store[$k]]);
              }
              else
              {
                  self::$dataExcel->getActiveSheet()->setCellValue($col[$p+4].$i,'');
              }
              $p++;
            }
            $i++;
        }
        
        // Set name file
        $file_name = "report_assets_store.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }

    static function export_order_date()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_time'], $CMS->lang['title_order'], $CMS->lang['title_report_sale_total_price'], $CMS->lang['title_items']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $pgroup = $CMS->input['p_group'];
        $store = $CMS->input['store'];
        
        // Check session for export data
        if(isset($_SESSION['export_order_date']))
        {
            $data = $_SESSION['export_order_date'];
        }
        else
        {
            // Call function again
            list($a,$b,$data,$c) = self::report_order($time_from, $time_to, '', $pgroup, $store);
            $data = $data['data_1'];
        }
        
        /*
        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }

        // Check product group
        if($pgroup){$clause .= " AND product_group = '{$pgroup}' ";}
        
        // Check store
        if($store) {$clause .= " store_id='{$store}' ";}
        */
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);

        /*$j=0;
        // Get data follow order items
        while ($result = $DB->fetch_array($sql)) 
        {
            $amount = $CMS->class->input->currency($result['ord_total']);
            $temp[$j]['datefm'] = $result['datefm'];
            $temp[$j]['qty_items'] = $result['qty_items'];
            $temp[$j]['amount'] = $amount;

            $j++;
        }
        
        // Get count orders
        $sql_order = $DB->query("SELECT DISTINCT(O.ord_id),DATE_FORMAT(FROM_UNIXTIME(ord_time),'{$datefm}') AS datefm FROM ".root_table."order_item as I left join ".root_table."order as O on I.ord_id=O.ord_id  WHERE ord_deleted = 0 AND ord_status=2 {$clause} ORDER BY datefm ASC");
        
        while($order = $DB->fetch_array())
        {
            for($i=0;$i<count($temp);$i++)
            {
                // Get data for list
                if($temp[$i]['datefm'] == $order['datefm'])
                {
                    // Get quantity items
                    $temp[$i]['quantity']++;
                }
                else
                {
                    $temp[$i][$order['datefm']] = 1;
                }
            }
        }
         */
        
        // Loop for set excel value
        $i=2;
        
        for($j=0;$j<count($data);$j++)
        {
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i,$data[$j]['datefm'])
                                              ->setCellValue('B'.$i,$data[$j]['quantity'])
                                              ->setCellValue('C'.$i,$data[$j]['amount'])
                                              ->setCellValue('D'.$i,$data[$j]['qty_items']);
            
            // Align center                                      
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("B".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("D".$i)->getAlignment()->applyFromArray($style);
            
            $i++;
        }
        
        // Set name file
        $file_name = "report_order_date.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    // Export product best seller
    static function export_product_bestseller()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_report_sale_num_order'], $CMS->lang['title_product_code'], $CMS->lang['title_product_name'], $CMS->lang['title_total_sell'], $CMS->lang['title_price'], $CMS->lang['title_report_sale_total_pprice'], $CMS->lang['title_report_sale_total_price'], $CMS->lang['title_report_sale_profit']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $pgroup = $CMS->input['p_group'];
        $store = $CMS->input['store'];
        $product = $CMS->input['product'];
        $p_status = $CMS->input['p_status'];
        $supplier = $CMS->input['supplier'];
        
        // Check session for data
        if(isset($_SESSION['export_product_bestseller']))
        {
            $data = $_SESSION['export_product_bestseller'];
        }
        else
        {
            // Call function for data
            list($a,$b,$data) = self::report_product_bestseller($time_from, $time_to, '', $store, $pgroup, $product, $supplier, $p_status);
            $data = $data['data_1'];
        }
        
        /*if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }

        // Check product group
        if($pgroup){$clause .= " AND product_group='{$pgroup}' ";}

        // Check product
        if($product){$clause .= " AND product_name like '%{$product}%' ";}
        
        // Check store
        if($store){$clause .= " AND store_id='{$store}' ";}
        
        // Check ord status
        if($p_status){$clause .= " AND product_status='{$p_status}' ";}
        
        // Check ord status
        if($supplier){$clause .= " AND sup_id='{$supplier}' ";}
        */
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);
        
        $i=2;
        // Get data follow order items
        for($j=0;$j<count($data);$j++) 
        {
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i,$i-1)
                                              ->setCellValue('B'.$i,$data[$j]['product_code'])
                                              ->setCellValue('C'.$i,html_entity_decode($data[$j]['product_name']))
                                              ->setCellValue('D'.$i,$data[$j]['qty_items'])
                                              ->setCellValue('E'.$i,$data[$j]['p_sell'])
                                              ->setCellValue('F'.$i,$data[$j]['p_original'])
                                              ->setCellValue('G'.$i,$data[$j]['total'])
                                              ->setCellValue('H'.$i,$data[$j]['profit'])
                    ;
            
            // Align center                                   
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("B".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("D".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("E".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("F".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("G".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("H".$i)->getAlignment()->applyFromArray($style);
            
            $i++;
        }
        
        
        // Set name file
        $file_name = "report_order_bestseller.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    // Export assets best seller
    static function export_assets_bestseller()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_report_sale_num_order'], $CMS->lang['title_assets_code'], $CMS->lang['title_assets_name'], $CMS->lang['title_total_sell'], $CMS->lang['title_price'], $CMS->lang['title_report_sale_total_pprice'], $CMS->lang['title_report_sale_total_price'], $CMS->lang['title_report_sale_profit']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $pgroup = $CMS->input['p_group'];
        $store = $CMS->input['store'];
        $ass_name = $CMS->input['ass_name'];
        $ass_id = $CMS->input['ass_id'];
        $ass_avaiable = $CMS->input['ass_avaiable'];
        $supplier = $CMS->input['supplier'];
        
        // Check session for data
        if(isset($_SESSION['export_assets_bestseller']))
        {
            $data = $_SESSION['export_assets_bestseller'];
        }
        else
        {
            // Call function for data
            list($a,$b,$data) = self::report_assets_bestseller($time_from, $time_to, '', $store, $pgroup, $ass_name, $ass_id, $supplier, $ass_avaiable);
            $data = $data['data_1'];
        }
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);
        
        $i=2;
        // Get data follow order items
        for($j=0;$j<count($data);$j++) 
        {
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i,$i-1)
                                              ->setCellValue('B'.$i,$data[$j]['product_code'])
                                              ->setCellValue('C'.$i, strip_tags(html_entity_decode($data[$j]['product_name'])))
                                              ->setCellValue('D'.$i,$data[$j]['qty_items'])
                                              ->setCellValue('E'.$i,$data[$j]['p_sell'])
                                              ->setCellValue('F'.$i,$data[$j]['p_original'])
                                              ->setCellValue('G'.$i,$data[$j]['total'])
                                              ->setCellValue('H'.$i,$data[$j]['profit'])
                    ;
            
            // Align center                                   
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("B".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("D".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("E".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("F".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("G".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("H".$i)->getAlignment()->applyFromArray($style);
            
            $i++;
        }
        
        
        // Set name file
        $file_name = "report_assets_bestseller.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    static function export_order_user()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_user'], $CMS->lang['title_order'], $CMS->lang['title_items'], $CMS->lang['title_report_sale_total_price']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $ord_sattus = $CMS->input['ord_sattus'];
        
        // Check session for export data
        if(isset($_SESSION['export_order_user']))
        {
            $data = $_SESSION['export_order_user'];
        }
        else
        {
            // Call function again
            list($a,$b,$data) = self::report_order_user($time_from, $time_to, '', $ord_status);
            $data = $data['data_1'];
        }

        /*
        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }

        // Check ord status
        if($ord_sattus){$clause .= " AND ord_status = '{$ord_sattus}' ";}
        */
        
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);

        $i=2;
        $total_1 = 0;
        $total_2 = 0;
        $total_3 = 0;
        
        // Get data follow order items
        for($j=0;$j<count($data);$j++) 
        {
            $total_1 += $data[$j]['qty'];
            $total_2 += $data[$j]['qty_items'];
            $total_3 += $data[$j]['total_bk'];
            
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i,$data[$j]['user_display_name'])
                                              ->setCellValue('B'.$i,$data[$j]['qty'])
                                              ->setCellValue('C'.$i,$data[$j]['qty_items'])
                                              ->setCellValue('D'.$i,$data[$j]['total']);
            
            // Align center                                      
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("B".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("D".$i)->getAlignment()->applyFromArray($style);
            
            $i++;
        }
        
        // Set last line
        self::$dataExcel->getActiveSheet()->setCellValue('A'.$i,$CMS->lang['title_report_sale_total_price'])
                                          ->setCellValue('B'.$i,$total_1)
                                          ->setCellValue('C'.$i,$total_2)
                                          ->setCellValue('D'.$i,$CMS->class->input->currency($total_3));
        
        // Align center                                      
        self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);
        self::$dataExcel->getActiveSheet()->getStyle("B".$i)->getAlignment()->applyFromArray($style);
        self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
        self::$dataExcel->getActiveSheet()->getStyle("D".$i)->getAlignment()->applyFromArray($style);
        
        // Set name file
        $file_name = "report_order_status.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    static function export_order_product()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_product_id'], $CMS->lang['title_product_code'], $CMS->lang['title_product_name'], $CMS->lang['title_order'], $CMS->lang['title_items'], $CMS->lang['title_price']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $pgroup = $CMS->input['p_group'];
        $product = $CMS->input['product'];
        
        // Check session for export data
        if(isset($_SESSION['export_order_product']))
        {
            $data = $_SESSION['export_order_product'];
        }
        else
        {
            list($a,$b,$data,$c) = self::report_order_product($time_from, $time_to, '', $pgroup, $product);
            $data = $data['data_1'];
        }
        
        /*
        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }

        // Check product group
        if($pgroup){$clause .= " AND product_group = '{$pgroup}' ";}
        
        // Check product name
        if($product){$clause .= " AND product_name like '%{$product}%' ";}
        */
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);

        /*
        $i=0;
        // Get data follow order items
        while ($result = $DB->fetch_array($sql)) 
        {
            $temp[$i]['product_id'] = $result['product_id'];
            $temp[$i]['product_code'] = $result['product_code'];
            $temp[$i]['product_name'] = $result['product_name'];
            $temp[$i]['qty_items'] = intval($result['qty_items']);
            $temp[$i]['qty'] = 0;
            $temp[$i]['total_bk'] = intval($result['ord_total']);
            $temp[$i]['total'] = $CMS->class->input->currency($result['ord_total']);

            $i++;
        }
        
        // Get quantity order
        $sql_order = $DB->query("SELECT DISTINCT(I.ord_id),ord_item FROM ".root_table."order_item as I left join ".root_table."order as O on I.ord_id=O.ord_id left join ".root_table."product as P on I.product_id=P.product_id WHERE ord_deleted = 0 AND ord_status=2 AND I.product_id > 0 {$clause} ORDER BY I.product_id ASC");
            
        while($order = $DB->fetch_array())
        {
            // Get info product
            $data_item = json_decode($order['ord_item']);
                
            foreach($data_item as $key => $value)
            {
                for($i=0;$i<count($temp);$i++)
                {
                    if($temp[$i]['product_id'] == $value->product_id)
                    {
                        $temp[$i]['qty']++;
                    }
                }
            }
        }
        */
        
        // Loop for set excel value
        $i=2;
        $total_1 =0; // Total qty
        $total_2 =0; // Total qty_items
        $total_3 =0; // Total price
        for($j=0;$j<count($data);$j++)
        {
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i,$data[$j]['product_id'])
                                              ->setCellValue('B'.$i,$data[$j]['product_code'])
                                              ->setCellValue('C'.$i,$data[$j]['product_name'])
                                              ->setCellValue('D'.$i,$data[$j]['qty'])
                                              ->setCellValue('E'.$i,$data[$j]['qty_items'])
                                              ->setCellValue('F'.$i,$data[$j]['total']);
            
            // Align center   
            self::set_cell_style('A'.$i, $style);
            self::set_cell_style('B'.$i, $style);
            self::set_cell_style('C'.$i, $style);
            self::set_cell_style('D'.$i, $style);
            self::set_cell_style('E'.$i, $style);
            self::set_cell_style('F'.$i, $style);
            
            // Set value for last total line
            $total_1+=$data[$j]['qty'];
            $total_2+=$data[$j]['qty_items'];
            $total_3+=$data[$j]['total_bk'];
            
            $i++;
        }
        
        // Build last line
        self::$dataExcel->getActiveSheet()->setCellValue('C'.$i,$CMS->lang['title_report_sale_total_price'])
                                          ->setCellValue('D'.$i,$total_1)
                                          ->setCellValue('E'.$i,$total_2)
                                          ->setCellValue('F'.$i,$CMS->class->input->currency($total_3));
        
        // Align center   
        self::set_cell_style('C'.$i, $style);
        self::set_cell_style('D'.$i, $style);
        self::set_cell_style('E'.$i, $style);
        self::set_cell_style('F'.$i, $style);
        
        // Set name file
        $file_name = "report_order_product.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    static function export_order_assets()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_assets_id'], $CMS->lang['title_assets_code'], $CMS->lang['title_assets_name'], $CMS->lang['title_order'], $CMS->lang['title_items'], $CMS->lang['title_price']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $pgroup = $CMS->input['p_group'];
        $ass_name = $CMS->input['ass_name'];
        
        // Check session for export data
        if(isset($_SESSION['export_order_assets']))
        {
            $data = $_SESSION['export_order_assets'];
        }
        else
        {
            list($a,$b,$data,$c) = self::report_order_product($time_from, $time_to, '', $pgroup, $ass_name);
            $data = $data['data_1'];
        }
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);
        
        // Loop for set excel value
        $i=2;
        $total_1 =0; // Total qty
        $total_2 =0; // Total qty_items
        $total_3 =0; // Total price
        for($j=0;$j<count($data);$j++)
        {
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i,$data[$j]['product_id'])
                                              ->setCellValue('B'.$i,$data[$j]['product_code'])
                                              ->setCellValue('C'.$i,$data[$j]['product_name'])
                                              ->setCellValue('D'.$i,$data[$j]['qty'])
                                              ->setCellValue('E'.$i,$data[$j]['qty_items'])
                                              ->setCellValue('F'.$i,$data[$j]['total']);
            
            // Align center   
            self::set_cell_style('A'.$i, $style);
            self::set_cell_style('B'.$i, $style);
            self::set_cell_style('C'.$i, $style);
            self::set_cell_style('D'.$i, $style);
            self::set_cell_style('E'.$i, $style);
            self::set_cell_style('F'.$i, $style);
            
            // Set value for last total line
            $total_1+=$data[$j]['qty'];
            $total_2+=$data[$j]['qty_items'];
            $total_3+=$data[$j]['total_bk'];
            
            $i++;
        }
        
        // Build last line
        self::$dataExcel->getActiveSheet()->setCellValue('C'.$i,$CMS->lang['title_report_sale_total_price'])
                                          ->setCellValue('D'.$i,$total_1)
                                          ->setCellValue('E'.$i,$total_2)
                                          ->setCellValue('F'.$i,$CMS->class->input->currency($total_3));
        
        // Align center   
        self::set_cell_style('C'.$i, $style);
        self::set_cell_style('D'.$i, $style);
        self::set_cell_style('E'.$i, $style);
        self::set_cell_style('F'.$i, $style);
        
        // Set name file
        $file_name = "report_order_assets.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    static function export_order_price()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_report_sale_num_order'], $CMS->lang['title_price'], $CMS->lang['title_quantity']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $range = $CMS->input['range'];
        
        // Check session for export data
        if(isset($_SESSION['export_order_price']))
        {
            $data = $_SESSION['export_order_price'];
        }
        else
        {
            // Call function again
            list($a,$b,$data,$c) = self::report_order_price($time_from, $time_to, '', $range);
            $data = $data['data_1'];
        }
        
        /*
        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }

        // Check range
        $range = intval($CMS->input['range']);
         * */
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Query data
        

        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);

        $i=2;
        
        // Set excel value
        for($j=0;$j<count($data);$j++)
        {   
                self::$dataExcel->getActiveSheet()->setCellValue('A'.$i,$i-1)
                                                  ->setCellValue('B'.$i,$data[$j]['total'])
                                                  ->setCellValue('C'.$i,$data[$j]['quantity']);

                // Align center                                      
                self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);
                self::$dataExcel->getActiveSheet()->getStyle("B".$i)->getAlignment()->applyFromArray($style);
                self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
                $i++;
        }
        
        // Set name file
        $file_name = "report_order_price.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    static function export_product_price()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_report_sale_num_order'], $CMS->lang['title_price'], $CMS->lang['title_total_product'], $CMS->lang['title_total_sell'], $CMS->lang['title_total_price']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $range = intval($CMS->input['range']);
        $pgroup = intval($CMS->input['pgroup']);
        $price_type = $CMS->input['price_type'];
        
        // Check session for export data
        if(isset($_SESSION['export_product_price']))
        {
            $data = $_SESSION['export_product_price'];
        }
        else
        {
            list($a,$b,$data,$c) = self::report_product_price($time_from, $time_to, '', $pgroup, $range, $price_type);
            $data = $data['data_1'];
        }
        
        /*
        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check product group
        if($pgroup) {$clause .= " AND product_group={$pgroup} ";}
        */
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);

        /*$temp = array();
        // Set excel value
        while ($result = $DB->fetch_array($sql))
        {   
            // Convert to int
            $result['total'] = $CMS->class->input->currency(intval($result['total']));
                    
            // Check order total is in range price
            if($result['price_type']%$range == 0)
            {
                // Data for chart
                $result['price_type'] = $CMS->class->input->currency($result['price_type']);
                        
                // Data for list
                $temp[$result['price_type']]['price_type'] = $result['price_type'];
                $temp[$result['price_type']]['total_qty_sold'] = $result['qty'];
                $temp[$result['price_type']]['total_price'] = $result['total'];
                $temp[$result['price_type']]['total_product'] = 0;
            }
        }
        
        // Get total product in range price for compare with data_1
        $sql = $DB->query("SELECT {$price_type} as price_type, count(product_id) as qty FROM ".root_table."product WHERE ".($pgroup ? " product_group={$pgroup}" : "1=1")." GROUP BY {$price_type} ORDER BY {$price_type} ASC");
        while($data = $DB->fetch_array($sql))
        {
            // Check for exist price in data_1
            $data['price_type'] = $CMS->class->input->currency($data['price_type']);
                
            if(isset($temp[$data['price_type']]))
            {
                $temp[$data['price_type']]['total_product'] = $data['qty'];
            }
        }*/
        
        // Loop for excel
        $i=2;
        foreach($data as $key => $value)
        {
           self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $i-1)
                                              ->setCellValue('B'.$i, $value['price_type'])
                                              ->setCellValue('C'.$i, $value['total_product'])
                                              ->setCellValue('D'.$i, $value['total_qty_sold'])
                                              ->setCellValue('E'.$i, $value['total_price']);
            // Align center                                      
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("B".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("D".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("E".$i)->getAlignment()->applyFromArray($style);

            $i++; 
        }
        
        // Set name file
        $file_name = "report_product_price.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    static function export_assets_price()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_report_sale_num_order'], $CMS->lang['title_price'], $CMS->lang['title_total_product'], $CMS->lang['title_total_sell'], $CMS->lang['title_total_price']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $range = intval($CMS->input['range']);
        $pgroup = intval($CMS->input['pgroup']);
        $price_type = $CMS->input['price_type'];
        
        // Check session for export data
        if(isset($_SESSION['export_assets_price']))
        {
            $data = $_SESSION['export_assets_price'];
        }
        else
        {
            list($a,$b,$data,$c) = self::report_assets_price($time_from, $time_to, '', $pgroup, $range, $price_type);
            $data = $data['data_1'];
        }
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);
        
        // Loop for excel
        $i=2;
        foreach($data as $key => $value)
        {
           self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $i-1)
                                              ->setCellValue('B'.$i, $value['price_type'])
                                              ->setCellValue('C'.$i, $value['total_product'])
                                              ->setCellValue('D'.$i, $value['total_qty_sold'])
                                              ->setCellValue('E'.$i, $value['total_price']);
            // Align center                                      
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("B".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("D".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("E".$i)->getAlignment()->applyFromArray($style);

            $i++; 
        }
        
        // Set name file
        $file_name = "report_assets_price.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    static function export_product_date()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_report_sale_num_order'], $CMS->lang['title_time'], $CMS->lang['title_product_code'], $CMS->lang['title_product_name'], $CMS->lang['title_order'], $CMS->lang['title_report_sale_total_pprice'], $CMS->lang['title_price'], $CMS->lang['title_report_sale_total_price'], $CMS->lang['title_report_sale_profit']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $store = $CMS->input['store'];
        $product = urldecode($CMS->input['product']);
        $p_status = $CMS->input['p_status'];
        
        // Check session for export data
        if(isset($_SESSION['export_product_date']))
        {
            $data = $_SESSION['export_product_date'];
        }
        else
        {
            // Call function
            list($a,$b,$data,$c) = self::report_product_date($time_from, $time_to, $view_type, $store, $product, $p_status);
            $data = $data['data_1'];
        }
        
        /*
        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check store
        if($store) {$clause .= " AND store_id='{$store}' ";}
        
        // Check product
        if($product) {$clause .= " AND product_name = '{$product}' ";}
        
        // Check product status
        if($p_status) {$clause .= " AND product_status='{$p_status}' ";}
        */
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);
        
        // Set excel value
        $i=2;
        for ($j=0;$j<count($data);$j++)
        {   
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $i-1)
                                              ->setCellValue('B'.$i, $data[$j]['datefm'])
                                              ->setCellValue('C'.$i, $data[$j]['product_code'])
                                              ->setCellValue('D'.$i, html_entity_decode($data[$j]['product_name']))
                                              ->setCellValue('E'.$i, $data[$j]['qty'])
                                              ->setCellValue('F'.$i, $data[$j]['product_price'])
                                              ->setCellValue('G'.$i, $data[$j]['product_price_sell'])
                                              ->setCellValue('H'.$i, $data[$j]['total'])
                                              ->setCellValue('I'.$i, $data[$j]['profit'])
                                                
                    ;
            // Align center                                      
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("B".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("D".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("E".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("F".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("G".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("H".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("I".$i)->getAlignment()->applyFromArray($style);
        }
        
        // Set name file
        $file_name = "report_product_date.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    static function export_assets_date()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_report_sale_num_order'], $CMS->lang['title_time'], $CMS->lang['title_assets_code'], $CMS->lang['title_assets_name'], $CMS->lang['title_order'], $CMS->lang['title_report_sale_total_pprice'], $CMS->lang['title_price'], $CMS->lang['title_report_sale_total_price'], $CMS->lang['title_report_sale_profit']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $store = $CMS->input['store'];
        $ass_name = urldecode($CMS->input['ass_name']);
        $ass_id = $CMS->input['ass_id'];
        $ass_avaiable = $CMS->input['ass_avaiable'];
        
        // Check session for export data
        if(isset($_SESSION['export_assets_date']))
        {
            $data = $_SESSION['export_assets_date'];
        }
        else
        {
            // Call function
            list($a,$b,$data,$c) = self::report_assets_date($time_from, $time_to, $view_type, $store, $ass_name, $ass_id, $ass_avaiable);
            $data = $data['data_1'];
        }
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);
        
        // Set excel value
        $i=2;
        for ($j=0;$j<count($data);$j++)
        {   
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $i-1)
                                              ->setCellValue('B'.$i, $data[$j]['datefm'])
                                              ->setCellValue('C'.$i, $data[$j]['product_code'])
                                              ->setCellValue('D'.$i, html_entity_decode($data[$j]['product_name']))
                                              ->setCellValue('E'.$i, $data[$j]['qty'])
                                              ->setCellValue('F'.$i, $data[$j]['product_price'])
                                              ->setCellValue('G'.$i, $data[$j]['product_price_sell'])
                                              ->setCellValue('H'.$i, $data[$j]['total'])
                                              ->setCellValue('I'.$i, $data[$j]['profit'])
                                                
                    ;
            // Align center                                      
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("B".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("D".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("E".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("F".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("G".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("H".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("I".$i)->getAlignment()->applyFromArray($style);
        }
        
        // Set name file
        $file_name = "report_assets_date.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    static function export_customer_sales()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_report_sale_num_order'], $CMS->lang['title_customer'], $CMS->lang['title_phone'], $CMS->lang['title_order'], $CMS->lang['title_items'], $CMS->lang['title_cost'], $CMS->lang['title_total_price'], $CMS->lang['title_report_sale_profit']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $store = $CMS->input['store'];
        $city = urldecode($CMS->input['city']);
        $district = $CMS->input['district'];
        $customer = urldecode($CMS->input['customer']);
        
        // Check session for export data
        if(isset($_SESSION['export_customer_sales']))
        {
            $data = $_SESSION['export_customer_sales'];
        }
        else
        {
            // Call function again for data
            list($a,$b,$data,$d,$e) = self::report_customer_sales($time_from, $time_to, '', $store, $city, $district, $customer);
            $data = $data['data_1'];
        }
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);

        // Set excel value
        $i=2;
        $total_order = 0;
        $total_item = 0;
        $total_price = 0;
        $total_original = 0;
        $total_profit = 0;
        
        foreach($data as $key => $value)
        {   
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $i-1)
                                              ->setCellValue('B'.$i, strip_tags($value['name']))
                                              ->setCellValue('C'.$i, $value['phone'])
                                              ->setCellValue('D'.$i, $value['qty'])
                                              ->setCellValue('E'.$i, $value['total_item'])
                                              ->setCellValue('F'.$i, $CMS->class->input->currency(intval($value['ord_original'])))
                                              ->setCellValue('G'.$i, $CMS->class->input->currency(intval($value['ord_total'])))
                                              ->setCellValue('H'.$i, $CMS->class->input->currency(intval($value['profit'])))
                    ;
            // Align center                                      
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("B".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("D".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("E".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("F".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("G".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("H".$i)->getAlignment()->applyFromArray($style);
            
            $total_order += $value['qty'];
            $total_item += $value['total_item'];
            $total_price += $value['ord_total'];
            $total_original += $value['ord_original'];
            $total_profit += $value['profit'];
            
            $i++;
        }
        
        // Set last line
        self::$dataExcel->getActiveSheet()->setCellValue('C'.$i, $CMS->lang['title_total'])
                                          ->setCellValue('D'.$i, $total_order)
                                          ->setCellValue('E'.$i, $total_item)
                                          ->setCellValue('F'.$i, $CMS->class->input->currency(intval($total_original)))
                                          ->setCellValue('G'.$i, $CMS->class->input->currency(intval($total_price)))
                                          ->setCellValue('H'.$i, $CMS->class->input->currency(intval($total_profit)))
                                                ;   
        self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
        self::$dataExcel->getActiveSheet()->getStyle("D".$i)->getAlignment()->applyFromArray($style);
        self::$dataExcel->getActiveSheet()->getStyle("E".$i)->getAlignment()->applyFromArray($style);
        self::$dataExcel->getActiveSheet()->getStyle("F".$i)->getAlignment()->applyFromArray($style);
        self::$dataExcel->getActiveSheet()->getStyle("G".$i)->getAlignment()->applyFromArray($style);
        self::$dataExcel->getActiveSheet()->getStyle("H".$i)->getAlignment()->applyFromArray($style);
        
        // Set name file
        $file_name = "report_customer_sales.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    static function export_customer_store()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_store']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        
        // Check session for data
        if(isset($_SESSION['export_customer_store']))
        {
            $data = $_SESSION['export_customer_store'];
            $list_month = $_SESSION['export_list_month'];
        }
        else
        {
            list($a,$b,$data,$list_month) = self::report_customer_store($time_from, $time_to);
            $data = $data['data_1'];
        }
        
        /*if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }*/
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Query data
        $count_row = count($data) + 1;// + 1 row title

        // Loop for data
        /*$temp = array();
        $list_month = array();
        while ($result = $DB->fetch_array($sql))
        {
            if(!in_array($result['datefm'],$list_month))
            {
               $list_month[] = $result['datefm']; 
            }
            
            // Data for List
            $temp[$result['store_name']][$result['datefm']] = $result['qty'];
        }*/
        
        // Set style excel
        // Merge for title
        $arr_title = array_merge($arr_title,$list_month);
        self::excel_title($arr_title, $count_row, $style);
        
        // Loop for excel
        $i=2;
        self::$col = str_split(self::$col);
        foreach($data as $key => $value)
        {
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i,$key);
            
            //print_r($value);exit;
            
            $p = 0;
            // Set column for each day
            for($k=0;$k<count($list_month);$k++)
            {
              if($value[$list_month[$k]])
              {
                  self::$dataExcel->getActiveSheet()->setCellValue(self::$col[$p+1].$i,$value[$list_month[$k]]);
              }
              else
              {
                  self::$dataExcel->getActiveSheet()->setCellValue(self::$col[$p+1].$i,'');
              }
              $p=$p+1;
            }
            $i++;
        }
        
        // Set name file
        $file_name = "report_customer_store.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    static function export_customer_product()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_report_sale_num_order'], $CMS->lang['title_customer'], $CMS->lang['title_phone'], $CMS->lang['title_email'], $CMS->lang['title_address'], $CMS->lang['title_product_code'], $CMS->lang['title_product_name'], $CMS->lang['title_cost'], $CMS->lang['title_price'], $CMS->lang['title_items'], $CMS->lang['title_total_price'], $CMS->lang['title_total_cost'], $CMS->lang['title_report_sale_profit']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $store = $CMS->input['store'];
        $pgroup = $CMS->input['pgroup'];
        $product = urldecode($CMS->input['product']);
        $customer = urldecode($CMS->input['customer']);
        $supplier = $CMS->input['supplier'];
        
        // Check session for data
        if(isset($_SESSION['export_customer_product']))
        {
            $data = $_SESSION['export_customer_product'];
        }
        else
        {
            list($a,$b,$data) = self::report_customer_product($time_from, $time_to, $view_type, $store, $pgroup, $product, $customer, $supplier);
            $data = $data['data_1'];
        }
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);

        // Loop array for excel
        $i = 2;
        $total_item = 0;
        $total_price = 0;
        $total_cost = 0;
        $total_discount = 0;
        $total_profit = 0;
        
        foreach($data as $key => $value)
        {
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $i-1)
                                              ->setCellValue('B'.$i, $value['name'])
                                              ->setCellValue('C'.$i, $value['phone'])
                                              ->setCellValue('D'.$i, $value['email'])
                                              ->setCellValue('E'.$i, $value['address'])
                                              ->setCellValue('F'.$i, $value['product_code'])
                                              ->setCellValue('G'.$i, $value['product_name'])
                                              ->setCellValue('H'.$i, $CMS->class->input->currency($value['cost']))
                                              ->setCellValue('I'.$i, $CMS->class->input->currency($value['price']))
                                              ->setCellValue('J'.$i, $value['qty'])
                                              ->setCellValue('K'.$i, $CMS->class->input->currency($value['total_price']))
                                              ->setCellValue('L'.$i, $CMS->class->input->currency($value['total_cost']))
                                              ->setCellValue('M'.$i, $CMS->class->input->currency($value['profit']));
            
            // Set data for last line
            $total_item += $value['qty'];
            $total_price += $value['total_price'];
            $total_cost += $value['total_cost'];
            $total_discount += $value['discount'];
            $total_profit += $value['profit'];
            
            $i++;
        }
        
        // Set last line
        self::$dataExcel->getActiveSheet()->setCellValue('I'.$i, $CMS->lang['title_total'])
                                          ->setCellValue('J'.$i, $total_item)
                                          ->setCellValue('K'.$i, $CMS->class->input->currency($total_price))
                                          ->setCellValue('L'.$i, $CMS->class->input->currency(intval($total_cost)))
                                          ->setCellValue('M'.$i, $CMS->class->input->currency(intval($total_profit)))
                                                ;   
        self::$dataExcel->getActiveSheet()->getStyle("I".$i)->getAlignment()->applyFromArray($style);
        self::$dataExcel->getActiveSheet()->getStyle("J".$i)->getAlignment()->applyFromArray($style);
        self::$dataExcel->getActiveSheet()->getStyle("K".$i)->getAlignment()->applyFromArray($style);
        self::$dataExcel->getActiveSheet()->getStyle("L".$i)->getAlignment()->applyFromArray($style);
        self::$dataExcel->getActiveSheet()->getStyle("M".$i)->getAlignment()->applyFromArray($style);
        
        // Set name file
        $file_name = "report_customer_product.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    static function export_customer_assets()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_report_sale_num_order'], $CMS->lang['title_customer'], $CMS->lang['title_phone'], $CMS->lang['title_email'], $CMS->lang['title_address'], $CMS->lang['title_assets_code'], $CMS->lang['title_assets_name'], $CMS->lang['title_cost'], $CMS->lang['title_price'], $CMS->lang['title_items_assets'], $CMS->lang['title_total_price'], $CMS->lang['title_total_cost'], $CMS->lang['title_report_sale_profit']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $store = $CMS->input['store'];
        $pgroup = $CMS->input['pgroup'];
        $ass_name = urldecode($CMS->input['ass_name']);
        $customer = urldecode($CMS->input['customer']);
        $supplier = $CMS->input['supplier'];
        
        // Check session for data
        if(isset($_SESSION['export_customer_assets']))
        {
            $data = $_SESSION['export_customer_assets'];
        }
        else
        {
            list($a,$b,$data) = self::report_customer_assets($time_from, $time_to, $view_type, $store, $pgroup, $ass_name, $customer, $supplier);
            $data = $data['data_1'];
        }
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);

        // Loop array for excel
        $i = 2;
        $total_item = 0;
        $total_price = 0;
        $total_cost = 0;
        $total_discount = 0;
        $total_profit = 0;
        
        foreach($data as $key => $value)
        {
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $i-1)
                                              ->setCellValue('B'.$i, $value['name'])
                                              ->setCellValue('C'.$i, $value['phone'])
                                              ->setCellValue('D'.$i, $value['email'])
                                              ->setCellValue('E'.$i, $value['address'])
                                              ->setCellValue('F'.$i, $value['product_code'])
                                              ->setCellValue('G'.$i, $value['product_name'])
                                              ->setCellValue('H'.$i, $CMS->class->input->currency($value['cost']))
                                              ->setCellValue('I'.$i, $CMS->class->input->currency($value['price']))
                                              ->setCellValue('J'.$i, $value['qty'])
                                              ->setCellValue('K'.$i, $CMS->class->input->currency($value['total_price']))
                                              ->setCellValue('L'.$i, $CMS->class->input->currency($value['total_cost']))
                                              ->setCellValue('M'.$i, $CMS->class->input->currency($value['profit']));
            
            // Set data for last line
            $total_item += $value['qty'];
            $total_price += $value['total_price'];
            $total_cost += $value['total_cost'];
            $total_discount += $value['discount'];
            $total_profit += $value['profit'];
            
            $i++;
        }
        
        // Set last line
        self::$dataExcel->getActiveSheet()->setCellValue('I'.$i, $CMS->lang['title_total'])
                                          ->setCellValue('J'.$i, $total_item)
                                          ->setCellValue('K'.$i, $CMS->class->input->currency($total_price))
                                          ->setCellValue('L'.$i, $CMS->class->input->currency(intval($total_cost)))
                                          ->setCellValue('M'.$i, $CMS->class->input->currency(intval($total_profit)));
        
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );
        
        self::$dataExcel->getActiveSheet()->getStyle("I".$i)->getAlignment()->applyFromArray($style);
        self::$dataExcel->getActiveSheet()->getStyle("J".$i)->getAlignment()->applyFromArray($style);
        self::$dataExcel->getActiveSheet()->getStyle("K".$i)->getAlignment()->applyFromArray($style);
        self::$dataExcel->getActiveSheet()->getStyle("L".$i)->getAlignment()->applyFromArray($style);
        self::$dataExcel->getActiveSheet()->getStyle("M".$i)->getAlignment()->applyFromArray($style);
        
        // Set name file
        $file_name = "report_customer_assets.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
   
    static function export_order_status()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_report_sale_num_order'], $CMS->lang['title_time'], $CMS->lang['title_order_status_0'], $CMS->lang['title_order_status_1'], $CMS->lang['title_order_status_2'], $CMS->lang['title_report_sale_total_price']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $p_group = $CMS->input['pgroup'];
        $store = $CMS->input['store'];
        
        // Check session for export data
        if(isset($_SESSION['export_order_status']))
        {
            $data = $_SESSION['export_order_status'];
        }
        else
        {
            list($a,$b,$data,$c) = self::report_order_status($time_from, $time_to, $view_type, $p_group, $store_id);
            $data = $data['data_1'];
        }
        
        /*
        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time > '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time < '{$time_to}' ";
        }else
        {
            $clause = "";
        }

        // Check product group
        if($p_group){$clause .= " AND product_group='{$p_group}' ";}
        
        // Check store
        if($store_id){$clause .= " AND store_id='{$store_id}' ";}
        */
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);

        // Loop data
        $i = 2;
        
        /*$temp = array();
        while ($result = $DB->fetch_array($sql)) 
        {
            // Set data for list
            if($temp[$result['datefm']][$result['ord_status']])
            {
                $temp[$result['datefm']][$result['ord_status']]['qty'] += $result['qty'];
            }
            else
            {
                $temp[$result['datefm']][$result['ord_status']]['qty'] = $result['qty'];
                $temp[$result['datefm']]['datefm'] = $result['datefm'];
            }
        }*/
        
        foreach ($data as $key => $value)
        {
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $i-1)
                                              ->setCellValue('B'.$i, $value['datefm'])
                                              ->setCellValue('C'.$i, $value[0]['qty'])
                                              ->setCellValue('D'.$i, $value[1]['qty'])
                                              ->setCellValue('E'.$i, $value[2]['qty'])
                                              ->setCellValue('F'.$i, $value[0]['qty']+$value[1]['qty']+$value[2]['qty'])
                    ;
            // Align center                                      
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("B".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("D".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("E".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("F".$i)->getAlignment()->applyFromArray($style);
            $i++;
        }

        
        // Set name file
        $file_name = "report_order_status.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }

    static function export_store_product()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_stt'], $CMS->lang['title_store'], $CMS->lang['title_assets_name'], $CMS->lang['title_quantity']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $type_status = $CMS->input['type_status'];

        // Set time clause
        if($time_from and $time_to)
        {
            $clause = " AND ass_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ass_time > '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ass_time < '{$time_to}' ";
        }else
        {
            $clause = "";
        }

        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Query data
        $sql = $DB->query("SELECT  ass_name, COUNT(ass_id) as qty, ass_key, S.store_name FROM ".root_table."assets as A LEFT JOIN ".root_table."store as S ON A.store_id = S.store_id WHERE ass_deleted = 0 AND is_available = 1 AND ass_time <= '{$time_to}' GROUP BY ass_key ORDER BY A.store_id ASC");
        $count_row = $DB->num_rows($sql) + 1;// + 1 row title

        // Set style excel
        self::excel_title($arr_title, $count_row, $style);

        // Loop data
        $i = 2;
        while ($result = $DB->fetch_array($sql)) 
        {
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $i-1)
                                      ->setCellValue('B'.$i, $result['store_name'])
                                      ->setCellValue('C'.$i, $result['ass_name'])
                                      ->setCellValue('D'.$i, $result['qty']);
            // Align center                                      
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("D".$i)->getAlignment()->applyFromArray($style);
            $i++;
        }
        // Set name file
        $file_name = "report_store_product.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }

    static function export_customer_time()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_stt'], $CMS->lang['title_time'], $CMS->lang['title_number_customer']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $type_status = $CMS->input['type_status'];

        // Set time clause
        if($time_from and $time_to)
        {
            $clause = " AND C.cus_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND C.cus_time > '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND C.cus_time < '{$time_to}' ";
        }else
        {
            $clause = "";
        }

        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Query data
        $sql = $DB->query("SELECT COUNT(C.cus_id) as qty, DATE_FORMAT(FROM_UNIXTIME(C.cus_time),'{$datefm}') AS datefm FROM ".root_table."customer as C WHERE C.cus_deleted = 0 {$clause} GROUP BY datefm ORDER BY C.cus_time ASC");
        $count_row = $DB->num_rows($sql) + 1;// + 1 row title

        // Set style excel
        self::excel_title($arr_title, $count_row, $style);

        // Loop data
        $i = 2;
        while ($result = $DB->fetch_array($sql)) 
        {
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $i-1)
                                      ->setCellValue('B'.$i, $result['datefm'])
                                      ->setCellValue('C'.$i, $result['qty']);
            // Align center                                      
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("B".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
            $i++;
        }
        // Set name file
        $file_name = "report_customer_time.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }

    static function export_customer_total()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_stt'], $CMS->lang['title_name_customer'], $CMS->lang['title_number_order'], $CMS->lang['title_amount']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $type_status = $CMS->input['type_status'];

        // Set time clause
        if($time_from and $time_to)
        {
            $clause = " AND C.cus_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND C.cus_time > '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND C.cus_time < '{$time_to}' ";
        }else
        {
            $clause = "";
        }

        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Query data
        $sql = $DB->query("SELECT C.cus_id, C.cus_full_name, COUNT(O.ord_id) as qty, SUM(O.ord_total) as total FROM ".root_table."customer AS C LEFT JOIN ".root_table."order AS O ON C.cus_id = O.cus_id WHERE C.cus_deleted = 0 AND O.ord_deleted = 0 {$clause} GROUP BY C.cus_id ORDER BY C.cus_time ASC");
        $count_row = $DB->num_rows($sql) + 1;// + 1 row title

        // Set style excel
        self::excel_title($arr_title, $count_row, $style);

        // Loop data
        $i = 2;
        while ($result = $DB->fetch_array($sql)) 
        {
            $total = input::currency($result['total'], 2,0);
            
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $i-1)
                                      ->setCellValue('B'.$i, $result['cus_full_name'])
                                      ->setCellValue('C'.$i, $result['qty'])
                                      ->setCellValue('D'.$i, $total);
            // Align center                                      
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("D".$i)->getAlignment()->applyFromArray($style);
            $i++;
        }
        // Set name file
        $file_name = "report_customer_total.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }

    static function export_revenue()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_stt'], $CMS->lang['title_time'], $CMS->lang['title_amount']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $type_status = $CMS->input['type_status'];

        // Set time clause
        if($time_from and $time_to)
        {
            $clause = " AND trx_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND trx_time > '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND trx_time < '{$time_to}' ";
        }else
        {
            $clause = "";
        }

        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Query data
        $sql = $DB->query("SELECT SUM(trx_total) as total, DATE_FORMAT(FROM_UNIXTIME(trx_time),'{$datefm}') AS datefm FROM ".root_table."transaction WHERE trx_deleted = 0 AND trx_subtype IN (2,3) AND trx_status IN (2,3) {$clause} GROUP BY datefm ORDER BY trx_time ASC");
        $count_row = $DB->num_rows($sql) + 1;// + 1 row title

        // Set style excel
        self::excel_title($arr_title, $count_row, $style);

        // Loop data
        $i = 2;
        while ($result = $DB->fetch_array($sql)) 
        {
            $amount = input::currency($result['total'], 2,0);
            
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $i-1)
                                      ->setCellValue('B'.$i, $result['datefm'])
                                      ->setCellValue('C'.$i, $amount);
            // Align center                                      
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("B".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
            $i++;
        }
        // Set name file
        $file_name = "report_revenue.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }

    static function export_report_cost()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array($CMS->lang['title_stt'], $CMS->lang['title_time'], $CMS->lang['title_amount']);

        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $type_status = $CMS->input['type_status'];

        // Set time clause
        if($time_from and $time_to)
        {
            $clause = " AND trx_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND trx_time > '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND trx_time < '{$time_to}' ";
        }else
        {
            $clause = "";
        }

        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Query data
        $sql = $DB->query("SELECT SUM(trx_total) as total, DATE_FORMAT(FROM_UNIXTIME(trx_time),'{$datefm}') AS datefm FROM ".root_table."transaction WHERE trx_deleted = 0 AND trx_subtype IN (6,7) AND trx_status IN (2,3) {$clause} GROUP BY datefm ORDER BY trx_time ASC");
        $count_row = $DB->num_rows($sql) + 1;// + 1 row title

        // Set style excel
        self::excel_title($arr_title, $count_row, $style);

        // Loop data
        $i = 2;
        while ($result = $DB->fetch_array($sql)) 
        {
            $amount = input::currency($result['total'], 2,0);
            
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $i-1)
                                      ->setCellValue('B'.$i, $result['datefm'])
                                      ->setCellValue('C'.$i, $amount);
            // Align center                                      
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("B".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
            $i++;
        }
        // Set name file
        $file_name = "report_cost.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }


    static function export_customer_list()
    {
        global $CMS, $DB, $member;

        //Check is_exit Reseller and load data by Reseller id
        if(intval($member['reseller_id']) > 0)
        {
            $where = " AND reseller_id = '{$member['reseller_id']}' ";
        }
        // Input
        $arr_title = array( $CMS->lang['title_name_customer'],
                            $CMS->lang['title_email'],
                            $CMS->lang['title_phone'],
                            $CMS->lang['tilte_company'],
                            $CMS->lang['title_address'],
                            $CMS->lang['title_address']." 2",
                            $CMS->lang['title_city'],
                            $CMS->lang['title_district'],
                            $CMS->lang['title_country'],
                            $CMS->lang['title_country_code'],
                            $CMS->lang['title_postalcode'],
                            $CMS->lang['title_receive_email'],
                            $CMS->lang['title_total_spend'],
                            $CMS->lang['title_total_order'],
                            $CMS->lang['title_tags'],
                            $CMS->lang['title_note'],
                            $CMS->lang['title_have_account'],
                            );

        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Query data
        $sql = $DB->query("SELECT * FROM ".root_table."customer WHERE cus_deleted=0 AND cus_email!='' {$where} ORDER BY cus_first_name ASC");
        $count_row = $DB->num_rows($sql) + 1;// + 1 row title

        // Set style excel
        self::excel_title($arr_title, $count_row, $style);

        // Loop data
        $i = 2;
        while ($result = $DB->fetch_array($sql)) 
        {
            $city_name = $result['cus_city'];//$CMS->country->city($result['cus_city']);
            $district_name = $result['cus_district'];//$CMS->country->district($result['cus_district']);
            $country_name = $CMS->country->country($result['cus_country'] ? $result['cus_country'] : -1);
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $result['cus_full_name'])
                                      ->setCellValue('B'.$i, $result['cus_email'])
                                      ->setCellValue('C'.$i, $result['cus_phone'])
                                      ->setCellValue('D'.$i, $result['cus_company'])
                                      ->setCellValue('E'.$i, $result['cus_address'])
                                      ->setCellValue('F'.$i, $result['cus_address2'])
                                      ->setCellValue('G'.$i, $city_name)
                                      ->setCellValue('H'.$i, $district_name)
                                      ->setCellValue('I'.$i, $country_name)
                                      ->setCellValue('J'.$i, $result['cus_country'])
                                      ->setCellValue('K'.$i, $result['cus_postalcode'])
                                      ->setCellValue('L'.$i, $result['receive_email'] ? "Yes" : "No")
                                      ->setCellValue('M'.$i, $result['cus_total_spend'])
                                      ->setCellValue('N'.$i, $result['cus_total_order'])
                                      ->setCellValue('O'.$i, implode(",",json_decode($result['cus_tags'], 1)))
                                      ->setCellValue('P'.$i, $result['cus_note'])
                                      ->setCellValue('Q'.$i, $result['have_account'] ? "Yes" : "No")
                                      ;
            // Align center                                      
            self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("I".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("L".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("Q".$i)->getAlignment()->applyFromArray($style);
            $i++;
        }
        // Set name file
        $file_name = "customer_list.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }

    /**
     * Export product list to excel file
     * @param int $product_type:
     * + 0: product
     * + 1: service;
     * @return string
     */
    static function export_product_list($product_type = 0)
    {
        global $CMS, $DB;

        //Header cols title
        $arr_title = [
            $CMS->lang['p_name'],
            $CMS->lang['p_type'],
            $CMS->lang['p_sku'],
            $CMS->lang['p_code'],
            $CMS->lang['p_cycle'],
            $CMS->lang['p_price'],
            $CMS->lang['p_price_sell'],
            $CMS->lang['title_suffix_price']." ({$CMS->lang['p_type_01']})",
            $CMS->lang['p_tax'],
            $CMS->lang['p_product_group'],
            $CMS->lang['p_supplier'],
            $CMS->lang['p_guarantee'],
            $CMS->lang['p_description'],
            $CMS->lang['p_show'],
            $CMS->lang['p_avartar'],
        ];

        // Set header
        self::excel_header();

        // Set style
        $style = array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation'   => 0,
            'wrap'       => TRUE
        );


        /**
         * Fields to export
         * Note: muste by match with header cols (order and numbers)
         */
        $fields = 'product_name,product_type,product_sku,product_code,product_cycle,product_price,product_price_sell,product_up,product_tax,product_group,sup_id,product_guarantee_default,product_description,product_show,product_image';

        // ThamLV d14-5-2018
        $where = empty($CMS->input['p_id_search']) ? '' : " AND product_id = '{$CMS->input['p_id_search']}' ";
        $where .= empty($CMS->input['p_name_search']) ? '' : " AND product_name LIKE '%{$CMS->input['p_name_search']}%' ";

        if( isset(ezy::$theme_key) AND ezy::$theme_key == 'nms' )
        {
            if( ! empty( $CMS->input['p_group_search'] ) )
            {
                $group_ids = [$CMS->input['p_group_search'] => $CMS->input['p_group_search']];
                $group_ids = $CMS->product->getListGroupChildId($CMS->input['p_group_search'], 0, $group_ids);
                $group_ids = array_values($group_ids);
                $group_ids = implode(', ', $group_ids);
                $group_ids = trim($group_ids, ',');

                $where .= " AND `product_group` IN ({$group_ids}) ";
            }
        }
        else
        {
            if( ! empty($CMS->input['p_group_search']) )
            {
                $where .= " AND product_group = '{$CMS->input['p_group_search']}' ";
            }
        }

        $where .= empty($CMS->input['p_supplier_search']) ? '' : " AND sup_id = '{$CMS->input['p_supplier_search']}' ";

        // Query data
        if(isset($CMS->input['id']) && ($pId = intval($CMS->input['id'])))
        {
            $sql = $DB->query("SELECT {$fields} FROM ".root_table."product WHERE product_deleted=0 AND product_id=$pId {$where} ORDER BY product_id ASC");
        }
        else
        {
            $sql = $DB->query("SELECT {$fields} FROM ".root_table."product WHERE product_deleted=0 AND product_type=$product_type {$where} ORDER BY product_id ASC");
        }

        $count_row = $DB->num_rows($sql) + 1;// + 1 row title

        // Set style excel
        self::excel_title($arr_title, $count_row, $style);

        // Loop data
        $i = 2;

        //Range char A-Z
        $rangeChar = range('A', 'Z') ;
        $fields = explode(',', $fields);

        while ($result = $DB->fetch_assoc($sql))
        {

            $result['product_show'] = $result['product_show'] ? 'show' : 'hide';

            $supInfo = [];

            if($result['sup_id'] != 0)
            {
                if($CMS->vars['supTemp'][$result['sup_id']])
                {
                    $supInfo = $CMS->vars['supTemp'][$result['sup_id']];
                }
                else
                {
                    $supInfo = $CMS->supplier->get_info($result['sup_id']);
                    $CMS->vars['supTemp'][$result['sup_id']] =  $supInfo;
                }
            }

            $result['sup_id'] = $supInfo['supplier_name'];

            $pGroup = [];

            if($result['product_group'] != 0)
            {
                if($CMS->vars['pGroupTemp'][$result['product_group']])
                {
                    $pGroup = $CMS->vars['pGroupTemp'][$result['product_group']];
                }
                else
                {
                    $pGroup = $CMS->product_group->getInfo($result['product_group']);
                    $CMS->vars['pGroupTemp'][$result['product_group']] =  $pGroup;
                }
            }

            $result['product_group'] = $pGroup['product_group_name'];

            if($result['product_type'] == 0)
            {
                $result['product_type'] = 'product';
            }
            else
            {
                $result['product_type'] = 'service';
            }


            $maxColWidth = 120;

            foreach ($rangeChar as $charKey => $char)
            {
                /**
                 * Set values to cell by chars(A-Z) and fields from database
                 */
                $field = $fields[$charKey];

                if($field == 'product_image')
                {
                    if( is_file("{$CMS->vars['upload_dir']}/product/{$result[$field]}") AND empty($CMS->input['no_export_image']) )
                    {
                        $objDrawing = new \PHPExcel_Worksheet_Drawing();
                        $objDrawing->setPath("{$CMS->vars['upload_dir']}/product/{$result[$field]}");
                        $objDrawing->setCoordinates($char.$i);
                        $objDrawing->setWorksheet(self::$dataExcel->getActiveSheet());
                        $objDrawing->setHeight(120);

                        $imgHeight = $objDrawing->getHeight();
                        $imgWidth = $objDrawing->getWidth();
                        $imgRatio = $imgHeight/$imgWidth;

                        $colWidth = 120/$imgRatio;

                        if($colWidth > $maxColWidth)
                        {
                            $maxColWidth =  $colWidth;
                        }
                        else
                        {
                            $colWidth = $maxColWidth;
                        }

                        self::$dataExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(120);
                        self::$dataExcel->getActiveSheet()->getColumnDimension($char)->setAutoSize(false);
                        self::$dataExcel->getActiveSheet()->getColumnDimension($char)->setWidth("50"); //$colWidth
                    }
                    else
                    {
                        self::$dataExcel->getActiveSheet()->setCellValue($char.$i,'');
                    }
                }
                else
                {
                    $result[$field] = html_entity_decode($result[$field]);
                    self::$dataExcel->getActiveSheet()->setCellValue($char.$i, $result[$field]);
                }
            };

            $i++;
        }

        // Set name file
        $file_name = "product_list.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }

    static function exportAsset()
    {
        global $CMS, $member;

        // Set header
        self::excel_header();

        $name = urldecode($CMS->input['name']);
        $quantity = urldecode($CMS->input['quantity']);
        $price = urldecode($CMS->input['price']);

        $arr_title = [
            'Name',
            'Quantity',
            'Price',
        ];

        // Set style
        $style = array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation'   => 0,
            'wrap'       => TRUE
        );

        // Set style excel
        self::excel_title($arr_title, 2, $style);

        self::$dataExcel->getActiveSheet()->setCellValue('A2',$name);
        self::$dataExcel->getActiveSheet()->setCellValue('B2',$quantity);
        self::$dataExcel->getActiveSheet()->setCellValue('C2',$price);

        // Set name file
        $file_name = "u{$member['user_id']}_asset.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }

    static function exportBarcode()
    {
        global $CMS, $member;

        require_once (root_path."/vendor/autoload.php");

        // Set header
        self::excel_header();

        for ($i=0; $i<=2; $i++)
        {
            self::$dataExcel->getActiveSheet()->SetCellValue('A'.(($i*3)+1), urldecode($CMS->input['name']));

            $barcode = \lib\Barcode::generatorJPG(urldecode($CMS->input['barcode']));

            if(\lib\Barcode::$error)
            {
                echo $barcode; exit;
            }

            $objDrawing = new \PHPExcel_Worksheet_MemoryDrawing();
            $objDrawing->setName('Sample image');
            $objDrawing->setDescription('Sample image');
            $objDrawing->setImageResource(imagecreatefromstring($barcode));
            $objDrawing->setRenderingFunction(\PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
            $objDrawing->setMimeType(\PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
            $objDrawing->setHeight(20);
            $objDrawing->setCoordinates('A'.(($i*3)+2));
            $objDrawing->setWorksheet(self::$dataExcel->getActiveSheet());

            self::$dataExcel->getActiveSheet()->SetCellValue('A'.(($i*3)+3), strip_tags($CMS->class->input->currency($CMS->input['price'])));
        }

        // Set name file
        $file_name = "u{$member['user_id']}_barcode.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    static function set_cell_style($cell = "",$style = "")
    {
        if(!$cell || !$style)
        {
            return false;
        }
        
        self::$dataExcel->getActiveSheet()->getStyle($cell)->getAlignment()->applyFromArray($style);
    }
    
    static function product_quick_search() 
    {
		global $CMS, $DB, $member;
                
 		$keyword  = trim($CMS->input['product_keyword']);
	 
		$keyword  = urldecode($keyword);
 
		 $sql = $DB->query("SELECT * FROM ".root_table."product  WHERE (product_code = '{$keyword}' OR product_name LIKE '%{$keyword}%' ) AND product_deleted = 0  " );
		 $count = $DB->num_rows($sql);
		 if($count > 0)
		 {
                        while ($data = $DB->fetch_array($sql)) {
                                # code...
                                $li .= "<li><a onclick='update_result(\"{$data['product_name']}\")'>{$data['product_name']}</a></li>";
                        }
		 	
		 	print json_encode(array("status" => "success", "msg" => "{$CMS->lang['title_empty_search']}", "data_option" => $li));exit;
		 }
		 else
		 {
		 	print json_encode(array("status" => "error", "msg" => "{$CMS->lang['title_empty_search']}"));exit;
		 }
	}
        
    static function assets_quick_search() 
    {
        global $CMS, $DB, $member;

        $keyword  = trim($CMS->input['assets_keyword']);

        $keyword  = urldecode($keyword);

         $sql = $DB->query("SELECT * FROM ".root_table."assets  WHERE (ass_code = '{$keyword}' OR ass_name LIKE '%{$keyword}%' ) AND ass_deleted = 0  " );
         $count = $DB->num_rows($sql);
         if($count > 0)
         {
                        while ($data = $DB->fetch_array($sql)) {
                                # code...
                                $li .= "<li><a onclick='update_assets_result(\"{$data['ass_id']}\",\"{$data['ass_name']}\")'>#{$data['ass_id']} - {$data['ass_name']}</a></li>";
                        }

                print json_encode(array("status" => "success", "msg" => "{$CMS->lang['title_empty_search']}", "data_option" => $li));exit;

         }
         else
         {
                print json_encode(array("status" => "error", "msg" => "{$CMS->lang['title_empty_search']}"));exit;
         }
    }
        
        // Get open balance follow time start
        // Param is unixtime
        static public function get_open_balance($time_start = 0)
        {
            global $CMS, $DB;
            
            // Convert intval
            $time_start = intval($time_start);
            
            // Check value
            if($time_start == 0)
            {
                return 0;
            }
            
            // Open balance today is close balance yeaterday
            // Ex: Open balance of 4/7/2017 is close balance of 3/7/2017
            // Sql
            $sql = $DB->query("SELECT accounts_type_name,trx_total,trx_subtype FROM ".root_table."accounts_type A left join ".root_table."transaction T on A.accounts_type_id=T.at_id WHERE trx_time < '{$time_start}' AND accounts_type_deleted=0 AND trx_deleted=0 AND trx_subtype IN (2,3,5,6) AND trx_status IN (1,3) ORDER BY accounts_type_id DESC");
            $temp = array();
            $data = array();

            while($trans = $DB->fetch_array($sql))
            {
                // Convert
                $trans['trx_total'] = intval($trans['trx_total']);
                
               if(isset($temp[$trans['accounts_type_name']]))
               {
                   // Debit - Ghi nợ
                   $temp[$trans['accounts_type_name']]['debit'] += in_array($trans['trx_subtype'],array(2,3)) ? $trans['trx_total'] : 0;
               
                   // Credit - Ghi có
                   $temp[$trans['accounts_type_name']]['credit'] += in_array($trans['trx_subtype'],array(5,6)) ? $trans['trx_total'] : 0;
               }
               else
               {
                   // Debit - Ghi nợ
                   $temp[$trans['accounts_type_name']]['debit'] = in_array($trans['trx_subtype'],array(2,3)) ? $trans['trx_total'] : 0;
                   
                   // Credit - Ghi có
                   $temp[$trans['accounts_type_name']]['credit'] = in_array($trans['trx_subtype'],array(5,6)) ? $trans['trx_total'] : 0;
               }
               
               // Open balance debit and credit is exist 1 in 2, not both
               // Calculate final result to check its debit or credit
               $cal = $temp[$trans['accounts_type_name']]['credit'] - $temp[$trans['accounts_type_name']]['debit'];
               
               // Check result
               if($cal < 0)
               {
                   $data[$trans['accounts_type_name']]['credit'] = 0;
                   $data[$trans['accounts_type_name']]['debit'] = abs($cal);   
               }
               else
               {
                   $data[$trans['accounts_type_name']]['credit'] = abs($cal);
                   $data[$trans['accounts_type_name']]['debit'] = 0;   
               }
            }
            
            return $data;
        }

    static function export_newsletter_list()
    {
        global $CMS, $DB;

        // Input
        $arr_title = array( $CMS->lang['title_stt'], $CMS->lang['title_email'], $CMS->lang['title_time']);

        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Query data
        $sql = $DB->query("SELECT * FROM ".root_table."newsletter WHERE newsletter_deleted=0 ORDER BY newsletter_email ASC");
        $count_row = $DB->num_rows($sql) + 1;// + 1 row title

        // Set style excel
        self::excel_title($arr_title, $count_row, $style);

        // Loop data
        $i = 2;
        while ($result = $DB->fetch_array($sql)) 
        {
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $i-1)
                                                ->setCellValue('B'.$i, $result['newsletter_email'])
                                                ->setCellValue('C'.$i, $CMS->class->date->date_format($result['newsletter_time']));
            // Align center                                      
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
            $i++;
        }
        // Set name file
        $file_name = "newsletter_list.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    // Search data from type (customer,supplier,user)
    // Report finance revenue
    // 01/08/2017 hvu
    static public function search_type()
    {
        global $CMS, $DB;
        
        // Input
        $cus_type = intval($CMS->input['cus_type']);
        
        // Default
        $data = "<option value=''>{$CMS->lang['title_all']}</option>";
        
        if($cus_type == 1) // Customer
        {
            $sql = $DB->query("SELECT cus_id,cus_full_name,cus_email FROM ".root_table."customer WHERE cus_deleted=0");
            
            while($customer = $DB->fetch_array($sql))
            {
                $data .= "<option value='{$customer['cus_id']}'>{$customer['cus_full_name']} ({$customer['cus_email']})</option>";
            }
        }
        else if($cus_type == 2) // Supplier
        {
            $sql = $DB->query("SELECT supplier_id,supplier_name FROM ".root_table."supplier WHERE supplier_deleted=0");
            
            while($supplier = $DB->fetch_array($sql))
            {
                $data .= "<option value='{$supplier['supplier_id']}'>{$supplier['supplier_name']}</option>";
            }
        }
        else if($cus_type == 3) // User
        {
            $sql = $DB->query("SELECT user_id,user_display_name FROM ".root_table."user WHERE user_deleted=0");
            
            while($user = $DB->fetch_array($sql))
            {
                $data .= "<option value='{$user['user_id']}'>{$user['user_display_name']}</option>";
            }
        }
        print json_encode($data);exit;
    }
    
    // Export report finance revenue
    static function export_finance_revenue()
    {
        global $CMS, $DB;

        // Excel title
        $arr_title = array($CMS->lang['title_time'], $CMS->lang['title_cash_plus'], $CMS->lang['title_cash_sub'], $CMS->lang['title_total_cash'], $CMS->lang['title_bank_plus'], $CMS->lang['title_bank_sub'], $CMS->lang['title_total_bank']);
        
        // Get input
        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $store = $CMS->input['store_id'];
        $cus_type = $CMS->input['cus_type'];
        $object = $CMS->input['object'];
        
        
        // Check session export data
        if(isset($_SESSION['export_finance_revenue']))
        {
            $data = $_SESSION['export_finance_record'];
        }
        else
        {
            // Call function for get data
            list($a,$b,$data,$d) = \models\report::report_finance_revenue($time_from, $time_to, '', $store, $cus_type, $object);
            $data = $data['data_1'];
        }
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);

        // Loop data
        $i = 2;
        foreach ($data as $key => $value) 
        {
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $value['date'])
                                              ->setCellValue('B'.$i, $value['credit_cash'])
                                              ->setCellValue('C'.$i, $value['debit_cash'])
                                              ->setCellValue('D'.$i, $value['total_cash'])
                                              ->setCellValue('E'.$i, $value['credit_bank'])
                                              ->setCellValue('F'.$i, $value['debit_bank'])
                                              ->setCellValue('G'.$i, $value['total_bank']);
            
            // Align center                                      
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("B".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("D".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("E".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("F".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("G".$i)->getAlignment()->applyFromArray($style);
            $i++;
        }
        // Set name file
        $file_name = "report_finance_revenue.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    

    // Export report finance commission_staff
    static function export_finance_commission_staff()
    {
        global $CMS, $DB;

        // Excel title
        $arr_title = array($CMS->lang['title_time'], $CMS->lang['title_staff'], $CMS->lang['title_total_commission']);
        
        // Get input
        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        $user_id = $CMS->input['user_id'];
        $object = $CMS->input['object'];
        
        
        // Check session export data
        if(isset($_SESSION['export_finance_commission_staff']))
        {
            $data = $_SESSION['export_finance_commission_staff'];
        }
        else
        {
            // Call function for get data
            list($a,$b,$data,$d) = \models\report::report_finance_commission_staff($time_from, $time_to, $user_id , $object);
            $data = $data['data_1'];
        }
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);

        // Loop data
        $i = 2;
        foreach ($data as $key => $value) 
        {
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $value['username'])
                                              ->setCellValue('B'.$i, $value['commission']);
                                               
            
            // Align center                                      
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("B".$i)->getAlignment()->applyFromArray($style);
            
            $i++;
        }
        // Set name file
        $file_name = "report_finance_commission_staffe.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    


    // Export report finance record
    static function export_finance_record()
    {
        global $CMS, $DB;

        // Excel title
        $arr_title = array($CMS->lang['title_report_sale_num_order'], $CMS->lang['title_at_code'], $CMS->lang['title_at_name'], "Open Balance - Debit", "Open Balance - Credit", "Accrued Balance - Debit", "Accrued Balance - Credit", "Close Balance - Debit", "Close Balance - Credit");
        
        // Get input
        $time_from = $CMS->input['time_from'];
        $time_to = $CMS->input['time_to'];
        $datefm = $CMS->input['datefm'];
        
        // Check session export data
        if(isset($_SESSION['export_finance_record']))
        {
            $data = $_SESSION['export_finance_record'];
        }
        else
        {
            // Call function for get data
            list($a,$b,$data) = \models\report::report_finance_record($time_from, $time_to);
            $data = $data['data_1'];
        }
        
        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Set style excel
        $count_row = count($data) + 1;// + 1 row title
        self::excel_title($arr_title, $count_row, $style);
        
        // Loop data
        $i = 2;
        $total_open_debit = 0;
        $total_open_credit = 0;
        $total_balance_debit = 0;
        $total_balance_credit = 0;
        $total_close_debit = 0;
        $total_close_credit = 0;
        
        foreach ($data as $key => $value) 
        {
            // Cal total
            $total_open_debit += $value['open_balance']['debit'];
            $total_open_credit += $value['open_balance']['credit'];
            $total_balance_debit += $value['balance_debit'];
            $total_balance_credit += $value['balance_credit'];
            $total_close_debit += $value['close_balance']['debit'];
            $total_close_credit += $value['close_balance']['credit'];
            
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $value['order'])
                                              ->setCellValue('B'.$i, $value['code'])
                                              ->setCellValue('C'.$i, $value['name'])
                                              ->setCellValue('D'.$i, $value['open_balance']['debit'])
                                              ->setCellValue('E'.$i, $value['open_balance']['credit'])
                                              ->setCellValue('F'.$i, $value['balance_debit'])
                                              ->setCellValue('G'.$i, $value['balance_credit'])
                                              ->setCellValue('H'.$i, $value['close_balance']['debit'])
                                              ->setCellValue('I'.$i, $value['close_balance']['credit'])
                    ;
            
            // Align center                                      
            self::$dataExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("B".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("D".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("E".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("F".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("G".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("H".$i)->getAlignment()->applyFromArray($style);
            self::$dataExcel->getActiveSheet()->getStyle("I".$i)->getAlignment()->applyFromArray($style);
            $i++;
        }
        
        // Build last line
        self::$dataExcel->getActiveSheet()->setCellValue('C'.$i, "Total")
                                          ->setCellValue('D'.$i, $total_open_debit)
                                          ->setCellValue('E'.$i, $total_open_credit)
                                          ->setCellValue('F'.$i, $total_balance_debit)
                                          ->setCellValue('G'.$i, $total_balance_credit)
                                          ->setCellValue('H'.$i, $total_close_debit)
                                          ->setCellValue('I'.$i, $total_close_credit);
            
        // Align center                                      
        self::$dataExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->applyFromArray($style);
        self::$dataExcel->getActiveSheet()->getStyle("D".$i)->getAlignment()->applyFromArray($style);
        self::$dataExcel->getActiveSheet()->getStyle("E".$i)->getAlignment()->applyFromArray($style);
        self::$dataExcel->getActiveSheet()->getStyle("F".$i)->getAlignment()->applyFromArray($style);
        self::$dataExcel->getActiveSheet()->getStyle("G".$i)->getAlignment()->applyFromArray($style);
        self::$dataExcel->getActiveSheet()->getStyle("H".$i)->getAlignment()->applyFromArray($style);
        self::$dataExcel->getActiveSheet()->getStyle("I".$i)->getAlignment()->applyFromArray($style);
        
        // Set name file
        $file_name = "report_finance_record.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
    
    static function get_search_time($time_from, $time_to, $data, $view_type, $type=0, $date_format = 0)
    {
        global $CMS, $DB;
      
        if($type == 0) // Get exactly date record
        {
            if($view_type == "view_day")
            {
                $time_search_from = $CMS->class->date->date2time(str_replace("-","/",$data),1);
                $time_search_to = $time_search_from+(24*3600);
            }
            else if($view_type == "view_month")
            {
                // Get the last day of month
                $temp_end = explode("-",$data);
                $last_day = $CMS->class->date->get_dayofmonth($temp_end[0],$temp_end[1]);
                
                $time_search_from = $CMS->class->date->date2time("01/".str_replace("-","/",$data),1);
                $time_search_to = $CMS->class->date->date2time($last_day."/".str_replace("-","/",$data),1);
            }
            else // Case year
            {
                $time_search_from = $CMS->class->date->date2time("01/01/".$data,1);
                $time_search_to = $CMS->class->date->date2time("31/12/".$data,1)+(24*3600);
            }
        }
        else // Get follow time search of report
        {
            if($view_type == "view_day")
            {
                $time_search_from = $CMS->class->date->date2time(str_replace("-","/",$data),1);
                $time_search_to = $time_search_from+(24*3600);
            }
            else if($view_type == "view_month")
            {
                // Replace date
                $temp_from = str_replace("-","/",$data);

                // Check start time selected with the first days of month
                // If start time < the first days => get start time = the first days
                // Else get start time selected
                if($time_from > $CMS->class->date->date2time("01/".$temp_from,1))
                {
                    $time_search_from = $time_from;
                }
                else
                {
                    $time_search_from = $CMS->class->date->date2time("01/".$temp_from,1);
                }

                // Time end
                // Get the last day of month
                $temp_end = explode("-",$data);
                $last_day = $CMS->class->date->get_dayofmonth($temp_end[0],$temp_end[1]);

                // Convert
                $temp_end = str_replace("-","/",$data);

                // Check start time selected with the last day of month
                // If end time selected < the last days => get end time = selected
                // Else get end time = the last day of month
                if($time_end > $CMS->class->date->date2time($last_day."/".$temp_end,1))
                {
                    $time_search_to = $CMS->class->date->date2time($last_day."/".$temp_end,1);
                }
                else
                {
                    $time_search_to = $time_to;
                }
            }
            else // Case year
            {
                $time_search_from = $time_from > $CMS->class->date->date2time("01/01/".$data,1) ? $time_from : $CMS->class->date->date2time("01/01/".$data,1);
                $time_search_to = $time_to < $CMS->class->date->date2time("31/12/".$data,1) ? $time_to : $CMS->class->date->date2time("31/12/".$data,1)+24*3600;
                
                
            }
        }
        
        // Get in day
        $time_search_to = $time_search_to  - 1;
        
        // Convert to date format
        if($date_format == 1)
        {
            $time_search_from = $CMS->class->date->date_format($time_search_from);
            $time_search_to = $CMS->class->date->date_format($time_search_to);
        }
        
        //print $CMS->class->date->date_format($time_search_from)."----".$CMS->class->date->date_format($time_search_to);exit;
        
        return array($time_search_from,$time_search_to);
    }
    
    static function report_inventory_quantity($time_from=0, $time_to=0, $view_type = "", $p_group="", $store_id="")
    {
        global $CMS, $DB;

        // Check time
        if($time_from and $time_to)
        {
            $clause = " AND ord_time BETWEEN '{$time_from}' AND '{$time_to}' ";
        }elseif($time_from)
        {
            $clause = " AND ord_time => '{$time_from}' ";
        }elseif($time_to)
        {
            $clause = " AND ord_time <= '{$time_to}' ";
        }else
        {
            $clause = "";
        }
        
        // Check store
        if($store_id){$clause .= " AND O.store_id='{$store_id}' ";}

        $datef = self::getTypeView($view_type);
        $arr_chart = array();
        $output_report['data_1'] = $temp = [];
        $output_report['link_export_1'] = "?site=report&subact=export&time_from={$time_from}&time_to={$time_to}&pgroup={$p_group}&store={$store_id}&datefm={$datef}&type_report=order_date";
        
        if($datef)
        {
            //====================
            // PRODUCTS
            //====================
            
            // Get all product of inventory current (newest)
            $clause .= $p_group ? " AND product_group='{$p_group}' " : "";
            $sql = $DB->query("SELECT product_id FROM ".root_table."product WHERE product_deleted=0 AND product_id NOT IN (SELECT DISTINCT(P.product_id) FROM ".root_table."product as P left join ".root_table."order_item as I on P.product_id=I.product_id left join ".root_table."order as O on I.ord_id=O.ord_id WHERE product_deleted = 0 {$clause}) ");
            $total_quantity = $DB->num_rows($sql);
            
            // Get list product is ordered
            $sql = $DB->query("SELECT P.product_id, product_price_original, O.store_id, I.ordi_id, DATE_FORMAT(FROM_UNIXTIME(ord_time),'{$datef}') AS datefm FROM ".root_table."product as P left join ".root_table."order_item as I on P.product_id=I.product_id left join ".root_table."order as O on I.ord_id=O.ord_id WHERE product_deleted = 0 AND O.store_id > 0 AND ord_status=2 AND ord_deleted=0 {$clause} ORDER BY ord_time DESC");
            
            if($DB->num_rows($sql) > 0)
            {
                $i = 0;
                while ($data = $DB->fetch_array($sql))
                {
                    $total_quantity++;
                    
                    if(isset($temp[$data['store_id']]))
                    {
                        $temp[$data['store_id']][$data['datefm']] = $total_quantity;
                        $temp[$data['store_id']]['total_original'] += $data['product_price_original'];
                    }
                    else
                    {
                        // Get store name
                        $store_name = $CMS->store->get_info($data['store_id'],"store_name");
                        $temp[$data['store_id']] = array(
                            "store_name" => $store_name,
                            "total_original" => $data['product_price_original'],
                            $data['datefm'] => $total_quantity,
                        );
                    }
                }
            }
        }
        
      
        
        $output_report['data_1'] = $_SESSION['export_order_date'] = $temp;
        
        $month = date("m");
        $year = date("Y"); 
        $chart = array();
        $chart[0] = "[\"{$CMS->lang['title_time']}\", \"{$CMS->lang['title_order']}\"]";
        $x = 1;
        
        if(!empty($arr_chart))
        {
           foreach ($arr_chart as $key => $value)
            {
                $chart[] = "[\"{$key}\", {$value['qty']}]";
            }
            
            $data['str_chart'] = "[".implode($chart, ',')."]"; 
        }
        else
        {
           $data['str_chart'] = '';
        }
        
        $data['title_chart']['main_title'] = "{$CMS->lang['title_chart_order']}";
        $data['title_chart']['title_unit_y'] = "";
        $data['type_chart'] = "";
        
        // Return info
        return array($data['str_chart'],$data['title_chart'], $output_report, $data['type_chart']);
    }




    static function export_contact_list()
    {
        global $CMS, $DB, $member;

        //Check is_exit Reseller and load data by Reseller id
        if(intval($member['reseller_id']) > 0)
        {
            $where = " AND reseller_id = '{$member['reseller_id']}' ";
        }
        // Input
        $arr_title = array( $CMS->lang['title_contact_name'],
                            $CMS->lang['title_contact_email'],
                            $CMS->lang['title_contact_phone'],
                            $CMS->lang['title_contact_address'],
                            );

        // Set header
        self::excel_header();

        // Set style
        $style = array(
                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 'rotation'   => 0,
                 'wrap'       => TRUE
             );

        // Query data
        $sql = $DB->query("SELECT * FROM ".root_table."contact WHERE con_deleted=0 A  {$where} ORDER BY con_id ASC");
        $count_row = $DB->num_rows($sql) + 1;// + 1 row title

        // Set style excel
        self::excel_title($arr_title, $count_row, $style);

        // Loop data
        $i = 2;
        while ($result = $DB->fetch_array($sql)) 
        {
         
            self::$dataExcel->getActiveSheet()->setCellValue('A'.$i, $result['con_name'])
                                      ->setCellValue('B'.$i, $result['con_email'])
                                      ->setCellValue('C'.$i, $result['con_phone'])
                                      ->setCellValue('D'.$i, $result['con_address'])
                                       
                                      ;
            
            $i++;
        }
        // Set name file
        $file_name = "contact_list.xls";

        // Create file and return link download
        return self::excel_output($file_name);
    }
        
}  

