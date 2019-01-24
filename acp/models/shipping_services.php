<?php
namespace models;

use \core\ezy;
use \lib\date;
use \lib\input;

class shipping_services
{
    /**
     * @param $record_cnt
     *		The order number of Data
     */
    static public $record_cnt = 0;

    /**
     * @param @$arrangeData
     *		The SQL Query for listing Data
     */
    static public $arrangeData;

    /**
     * @param $sqlAdd
     *		The additional SQL for $sql_query
     */
    static public $sqlAdd;

    /**
     * @param $sqlQuery
     *		The SQL Query for listing Data
     */
    static public $sqlQuery;

    /**
     * @param $maxPage
     *		Number of records on per page
     */
    static public $maxPage = 20;

    /**
     * @param $prefixPaging
     *		Prefix for paging url
     */
    static public $prefixPaging = '';

    /**
     * @param $prefixPaging
     *		Suffix for paging url
     */
    static public $suffixPaging = '';

    /**
     * @param $prefixPaging
     *      Suffix for paging url
     */
    static public $cache_prefix = 'shipping_fee';

    /**
     * @param $html
     *      The templates
     */
    static public $shipping_type_service = [
        '0' => "Standard",
        '1' => "Express",
    ];
    static public $shipping_location = [
        '0' => "Domestics",
        '1' => "International",
    ];

    static public $locateData = [
        'vn' => "Viet Nam",
        'us' => "USA",
    ];

    /**
     * Get list bin
     * @param string $sql_add
     * @param integer $disabled_paging
     * @return array
     */
    static public function listing( $sql_add = "", $disabled_paging = 0, $type = 'rows' )
    {
        global $CMS, $DB;

        // Update Arrange Data
        self::$arrangeData = trim("S.ship_id,S.product_id,Ship_time");

        // Set default for Arrange
        $default_field = $CMS->input['order'] ? $CMS->input['order'] : "S.ship_id";
        $default_order = $CMS->input['by'] ? $CMS->input['by'] : "desc";

        // SQL Condition
        $sql_add .= " S.ship_deleted=0 AND ";
        $sql_add .= self::getSqlAdd($CMS->input);
        
        $select = $type == 'cnt' ? " COUNT(0) AS cnt " : " S.*, P.product_name, P.product_barcode ";
        $sql = "
        SELECT {$select} 
        FROM ".root_table."shipping_fee AS S LEFT JOIN ".root_table."product AS P ON S.product_id = P.product_id 
        WHERE {$sql_add} 1=1 
        ORDER BY {$default_field} {$default_order} 
        ";

        if( $type == 'cnt' )
        {
            $results = $DB->fetch_data($sql, self::$cache_prefix);
            return isset($results[0]['cnt']) ? $results[0]['cnt'] : 0;
        }

        // Create SQL Query for listing Data
        if( $disabled_paging == 1 ) 
        {
            if( self::$maxPage > 0 )
            {
                $sql .= ' LIMIT ' . self::$maxPage;
            }
            
            $CMS->show_page = "";
            $results = $DB->fetch_data($sql, self::$cache_prefix);
        }
        else 
        {
            list($CMS->show_page, $results) = $DB->fetch_listing($sql, self::$maxPage, self::$prefixPaging,  self::$suffixPaging, $CMS->input['page'], self::$cache_prefix);
        }

        $data = array();
        if( is_array($results) )
        {
            foreach ($results as $key => $result) 
            {
                $data[] = self::convertvalue($result);
            }
        }

        return $data;
    }

    static function getSqlAdd( $data = [], $prefix = 'S.' )
    {
        global $CMS;

        $sql_add = '';

        if( !empty($data['keywords']) )
        {
            $keywords = urldecode($data['keywords']);
            $sql_add .=  " (P.product_name LIKE '%{$keywords}%' OR P.product_barcode LIKE '%{$keywords}%' OR P.product_sku LIKE '%{$keywords}%') AND ";
        }

        if( !empty($data['price']) )
        {
            $price = explode('-', $data['price']);
            if( !empty($price[0]) )
            {
                $sql_add .= " {$prefix}ship_price >= '{$price[0]}' AND ";
            }

            if( !empty($price[1]) )
            {
                $sql_add .= " {$prefix}ship_price <= '{$price[1]}' AND ";
            }
        }

        if( !empty($data['price_extra']) )
        {
            $price_extra = explode('-', $data['price_extra']);
            if( !empty($price_extra[0]) )
            {
                $sql_add .= " {$prefix}ship_price_extra >= '{$price_extra[0]}' AND ";
            }

            if( !empty($price_extra[1]) )
            {
                $sql_add .= " {$prefix}ship_price_extra <= '{$price_extra[1]}' AND ";
            }
        }

        if( isset($data['ship_type_service']) )
        {
            if( is_numeric($data['ship_type_service']) )
            {
                $sql_add .= " {$prefix}ship_type_service = '{$data['ship_type_service']}' AND ";
            }
        }

        if( isset($data['ship_location']) )
        {
            if( is_numeric($data['ship_location']) )
            {
                $sql_add .= " {$prefix}ship_location = '{$data['ship_location']}' AND ";
            }
        }

        return $sql_add;
    }

    /**
     * Convert original record to show
     * @param array $data
     * @return array
     */
    static function convertValue($data=[])
    {
        global $CMS;

        $data['data_bk'] = $data_bk = $data;

        // Replace search content
        $data = $CMS->class->search->convertvalue($data);

        $data['us_shipping_c']=  $CMS->class->input->currency($data['ship_price']);
        $data['us_extra_c']=  $CMS->class->input->currency($data['ship_price_extra']);
        $data['shipping_service'] = explode("|", $data['ship_service_code'])[1];
        $data['shipping_type'] = \lib\input::lang("shipping_type_".$data['ship_type_service']);
        $data['shipping_location'] = \lib\input::lang("shipping_location_".$data['ship_location']);
        $data['text_color'] = $data['ship_location'] ? "green" : "blue";

        if( !isset($data['product_name']) OR !isset($data['product_barcode']) )
        {
            $product = $CMS->product->getInfo($data['product_id']);
            
            if( !isset($data['product_name']) )
            {
                $data['product_name'] = isset($product['product_name']) ? $product['product_name'] : '';
            }

            if( !isset($data['product_barcode']) )
            {
                $data['product_barcode'] = isset($product['product_barcode']) ? $product['product_barcode'] : '';
            }
        }
        
        $data['product_url'] = '';
        if( $CMS->permit['product_read'] )
        {
            $data['product_url'] = "<a href='{$CMS->vars['root_domain']}/?site=product&act=show&id={$data['product_id']}'>{$data['product_name']}</a>";
        }

        $data['record_cnt'] = self::$record_cnt;
        self::$record_cnt++;

        return $data;
    }

    /**
    * add_shipping_services
    * @param  input 
    * @output boolean 
    */
    static public function add( $data = [] )
    {
        global $CMS, $member, $DB;

        // Inputs
        $arr_product = array_values($data['product_id']);
        $arr_ship_type_service = array_values($data['ship_type_service']);
        $arr_ship_price_domestics = array_values($data['ship_price_domestics']);
        $arr_ship_extra_domestics = array_values($data['ship_extra_domestics']);
        $arr_ship_price_intl = array_values($data['ship_price_intl']);
        $arr_ship_extra_intl = array_values($data['ship_extra_intl']);
        $ship_time = time();
        $user_id = $member['user_id'];
    
        if( count($arr_product) <= 0 )
        {
            $_SESSION['error_msg'] = $CMS->lang['error_shipping_fee_no_product'];
            return false;
        }

        $clause = "";
        $cnt_shipping_type_service = count(self::$shipping_type_service);
        foreach( $arr_product as $key => $product_id ) 
        {
            $product = $CMS->product->getInfo($product_id);
            $product_name  = $product['product_name'];
            $product_name .= $product['product_barcode'] ? " ({$product['product_barcode']})" : '';

            for( $i=0; $i < $cnt_shipping_type_service; $i++ )
            {
                if( !isset($arr_ship_type_service[$i]) )
                {
                    continue;
                }
                $type_service = self::$shipping_type_service[$arr_ship_type_service[$i]];

                // Domestics
                if( !self::checkExist($product_id, $arr_ship_type_service[$i], 0) )
                {
                    $clause .= "('{$product_id}', '{$arr_ship_type_service[$i]}', '{$arr_ship_price_domestics[$i]}', '{$arr_ship_extra_domestics[$i]}', '{$ship_time}', 0, '{$user_id}') , ";
                }
                else
                {
                    $_SESSION['error_msg'] .= str_replace(array('[type_service]', '[location]', '[product_id]'), array($type_service, 'Domestics', "#{$product_id}:{$product_name}" ), $CMS->lang['error_shipping_fee_exist']) . "<br/>";
                }

                // International
                if( !self::checkExist($product_id, $arr_ship_type_service[$i], 1) )
                {
                    $clause .= "('{$product_id}', '{$arr_ship_type_service[$i]}', '{$arr_ship_price_intl[$i]}', '{$arr_ship_extra_intl[$i]}', '{$ship_time}', 1, '{$user_id}'), ";
                }else
                {
                    $_SESSION['error_msg'] .= str_replace(array('[type_service]', '[location]', '[product_id]'), array($type_service, 'International', "#{$product_id}:{$product_name}" ), $CMS->lang['error_shipping_fee_exist']) . "<br/>";
                }
            }
        }
        $clause = trim($clause);
        $clause = trim($clause, ',');

        // Query multi
        if( $clause )
        {
            $sql_multi = "
            INSERT INTO ".root_table."shipping_fee 
            (product_id, ship_type_service, ship_price, ship_price_extra, ship_time, ship_location, user_id) 
            VALUES 
            {$clause} 
            ";
            // print $sql_multi; exit;
            $query_multi = $DB->query($sql_multi);
        }

        // Logs
        if( isset($query_multi) AND $query_multi )
        {
            // Clear cache
            $CMS->class->cache->mdelete(self::$cache_prefix);

            $CMS->class->logs->insert("{$CMS->lang['add_new_shipping_fee_success']}" );
            $_SESSION['msg'] .= $CMS->lang['add_new_shipping_fee_success'];
            return true;
        }
        else
        {
            $CMS->class->logs->insert("{$CMS->lang['add_new_shipping_fee_failed']}" );
            $_SESSION['error_msg'] .= $CMS->lang['add_new_shipping_fee_failure'];
            return false;
        }
    }

    /**
    * Check_exist_shipping_services
    * @param  input 
    * @output boolean 
    */
    static function checkExist( $product_id=0, $ship_type_service="", $ship_location=0, $return_info=false )
    {
        global $CMS, $DB;

        $sql = "
        SELECT * FROM ".root_table."shipping_fee 
        WHERE ship_deleted=0 AND product_id='{$product_id}' AND ship_type_service='{$ship_type_service}' AND ship_location='{$ship_location}' 
        LIMIT 1 
        ";
        // print $sql; exit;
        $results = $DB->fetch_data($sql, self::$cache_prefix.'.product');

        if( $return_info )
        {
            return isset($results[0]) ? $results[0] : false;
        }
        else
        {
            return isset($results[0]['ship_id']) ? $results[0]['ship_id'] : 0;
        }
    }

    /**
     * Get infomation
     * @param int $id
     * @return array
     */
    static public function getInfo( $id = 0, $field_name = '*', $sql_add = '' )
    {
        global $CMS, $DB;

        $output = false;

        if( !$id )
        {
            $id = $CMS->input['id'];
        }

        $sql = "
        SELECT {$field_name} 
        FROM ".root_table."shipping_fee 
        WHERE {$sql_add} ship_id='{$id}' AND ship_deleted=0 
        ORDER BY ship_id DESC 
        LIMIT 1 
        ";
        // print $sql; exit;

        $data = $DB->fetch_data($sql, self::$cache_prefix);
        $data = isset($data[0]) ? $data[0] : false;

        if ( $field_name !== '*' AND count(explode(",", $field_name)) == 1 ) 
        {
            if( isset($data[$field_name]) )
            {
                $output = $data[$field_name];
            }
        }
        else
        {
            $output = $data;
        }

        return $output;
    }

    /**
    *  edit_shipping_services
    * @param  input 
    * @output boolean 
    */
    static public function edit( $data_info = [], $data_edit = [] )
    {
        global $CMS, $member, $DB;

        // Input
        $ship_id = isset($data_edit['id']) ? $data_edit['id'] : 0;
        $ship_price = isset($data_edit['ship_price']) ? $data_edit['ship_price'] : 0;
        $ship_price_extra = isset($data_edit['ship_price_extra']) ? $data_edit['ship_price_extra'] : 0;
        $ship_time_update = time();

        // Update
        $sql = "
        UPDATE ".root_table."shipping_fee 
        SET ship_price='{$ship_price}', ship_price_extra='{$ship_price_extra}', ship_time_update='{$ship_time_update}' 
        WHERE ship_id={$ship_id} 
        ";
        // print $sql; exit;
        $query_sql = $DB->query($sql);

        if( !$query_sql )
        {
            $_SESSION['error_msg'] .= $CMS->lang['edit_shipping_fee_failure']."<br>";
            return false;
        }
        
        // Clear cache
        $CMS->class->cache->mdelete(self::$cache_prefix);

        // Add logs
        $CMS->class->logs->key = self::$cache_prefix."_".$ship_id;
        $_SESSION['msg'] .= $CMS->class->logs->insert("{$CMS->lang['edit_shipping_fee_success']}: <a href=\"{$CMS->vars['root_domain']}/?site=config_general&act=shipping_fee&subact=edit&id={$ship_id}\">#{$ship_id}</a>" ) . '<br>';
        
        // Add detail
        $CMS->class->logs->key = self::$cache_prefix."_".$ship_id;
        $CMS->class->logs->old_data = $data_info;
        $CMS->class->logs->save_detail(self::$cache_prefix, $ship_id, self::getInfo($ship_id));

        return true;
    }

    /**
     * Delete
     * @return bool
     */
    static public function delete($id=0)
    {
        global $CMS, $DB;

        if( !id )
        {
            $id = intval($CMS->input['id']);
        }

        // Check existing
        if ( $id == 0 ) { return false; }
 
        // Delete
        $sql = "
        UPDATE ".root_table."shipping_fee 
        SET ship_deleted=1 
        WHERE ship_id={$id}
        ";
        // print $sql; exit;
        $query_sql = $DB->query($sql);

        // Clear cache
        $CMS->class->cache->mdelete(self::$cache_prefix);
        
        // Create log
        $CMS->class->logs->key = self::$cache_prefix."_".$id;
        $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['ship_deleted']} <b>Shipping fee #{$id}</b>")."<br />";

        return true;
    }

    static public function delete_all()
    {
        global $CMS, $DB;

        $deleted = 0;
        $data_cnt = intval($CMS->input["data_cnt"]);
        for( $i = 0; $i < $data_cnt; $i++ )
        {
            if( $id = intval($CMS->input["id_{$i}"]) )
            {
                if( self::delete($id) )
                {
                    $deleted = 1;
                }
            }
        }
        
        // Clear cache
        $CMS->class->cache->mdelete(self::$cache_prefix);

        if( $deleted == 0 )
        {
            $_SESSION["msg"] .= "{$CMS->lang['ship_delete_all_failed']}";
        }

        return true;
    }

    static public function generalDataShipFee( $data=[] )
    {
        global $CMS;

        $output = [];

        if( !empty($data['ship_type_service']) )
        {
            $output['product_id']           = isset($data['product_id']) ? array_values($data['product_id']) : [];
            $output['ship_type_service']    = isset($data['ship_type_service']) ? array_values($data['ship_type_service']) : [];
            $output['ship_price_domestics'] = isset($data['ship_price_domestics']) ? array_values($data['ship_price_domestics']) : [];
            $output['ship_extra_domestics'] = isset($data['ship_extra_domestics']) ? array_values($data['ship_extra_domestics']) : [];
            $output['ship_price_intl']      = isset($data['ship_price_intl']) ? array_values($data['ship_price_intl']) : [];
            $output['ship_extra_intl']      = isset($data['ship_extra_intl']) ? array_values($data['ship_extra_intl']) : [];
        }

        return $output;
    }

    static public function addByProduct( $data = [], $product_id = 0 )
    {
        global $CMS;

        $data['product_id'] = [$product_id];
        $shipping_services = self::generalDataShipFee($data);

        return self::add($data);
    }
    
    static public function getDataShipFeeByProductId( $product_id = 0, $disabled_convert = true )
    {
        global $CMS, $DB;
        
        $output = false;

        if( $product_id )
        {
            $sql = "
            SELECT * 
            FROM ".root_table."shipping_fee 
            WHERE product_id='{$product_id}' AND ship_deleted=0 
            ORDER BY ship_id ASC 
            ";
            // print $sql; exit;

            $results = $DB->fetch_data($sql, self::$cache_prefix);
            if( is_array($results) )
            {
                if( $disabled_convert )
                {
                    $output = $results;
                }
                else
                {
                    foreach( $results as $result ) 
                    {
                        $output[] = self::convertValue($result);
                    }
                }
            }
        }

        return $output;
    }

    static public function convertDataShipFee( $inputs=[] )
    {
        global $CMS;

        $output = [];

        if( is_array($inputs) )
        {
            foreach( $inputs as $data ) 
            {
                $output['ship_type_service'][$data['ship_type_service']] = $data['ship_type_service'];

                // domestics
                if( $data['ship_location'] == 0 )
                {
                    $output['ship_price_domestics'][$data['ship_type_service']] = $data['ship_price'];
                    $output['ship_extra_domestics'][$data['ship_type_service']] = $data['ship_price_extra'];
                }

                // international
                else if( $data['ship_location'] == 1 )
                {
                    $output['ship_price_intl'][$data['ship_type_service']] = $data['ship_price'];
                    $output['ship_extra_intl'][$data['ship_type_service']] = $data['ship_price_extra'];
                }
            }
        }

        return self::generalDataShipFee($output);
    }

    static public function editByProduct( $data = [], $product_id = 0 )
    {
        global $CMS, $DB, $member;

        // Inputs
        $data = self::generalDataShipFee($data);
        $arr_ship_type_service      = array_values($data['ship_type_service']);
        $arr_ship_price_domestics   = array_values($data['ship_price_domestics']);
        $arr_ship_extra_domestics   = array_values($data['ship_extra_domestics']);
        $arr_ship_price_intl = array_values($data['ship_price_intl']);
        $arr_ship_extra_intl = array_values($data['ship_extra_intl']);
        
        $ship_time = time();
        $user_id   = $member['user_id'];
        $ship_id_arr     = []; // Save id live
        $sql_multi_value = '';

        $cnt_shipping_type_service = count(self::$shipping_type_service);
        for( $i=0; $i < $cnt_shipping_type_service; $i++ )
        {
            if( !isset($arr_ship_type_service[$i]) )
            {
                continue;
            }
            $type_service = self::$shipping_type_service[$arr_ship_type_service[$i]];

            // Domestics
            $data_info = self::checkExist($product_id, $arr_ship_type_service[$i], 0, true);
            if( !empty($data_info['ship_id']) )
            {
                $ship_id_arr[] = $data_info['ship_id'];

                // Data edit
                $data_edit = [];
                $data_edit['id']                = $data_info['ship_id'];
                $data_edit['ship_price']        = $arr_ship_price_domestics[$i];
                $data_edit['ship_price_extra']  = $arr_ship_extra_domestics[$i];
                self::edit($data_info, $data_edit);
            }
            else
            {
                $sql_multi_value .= "('{$product_id}', '{$arr_ship_type_service[$i]}', '{$arr_ship_price_domestics[$i]}', '{$arr_ship_extra_domestics[$i]}', '{$ship_time}', 0, '{$user_id}') , ";
            }

            // International
            $data_info = self::checkExist($product_id, $arr_ship_type_service[$i], 1, true);
            if( !empty($data_info['ship_id']) )
            {
                $ship_id_arr[] = $data_info['ship_id'];

                // Data edit
                $data_edit = [];
                $data_edit['id']                = $data_info['ship_id'];
                $data_edit['ship_price']        = $arr_ship_price_intl[$i];
                $data_edit['ship_price_extra']  = $arr_ship_extra_intl[$i];
                self::edit($data_info, $data_edit);
            }
            else
            {
                $sql_multi_value .= "('{$product_id}', '{$arr_ship_type_service[$i]}', '{$arr_ship_price_intl[$i]}', '{$arr_ship_extra_intl[$i]}', '{$ship_time}', 1, '{$user_id}'), ";
            }
        }

        // Delete shipping die
        $dataShipFeeOld = self::getDataShipFeeByProductId($product_id);
        foreach( $dataShipFeeOld as $shipFeeOld ) 
        {
            if( in_array($shipFeeOld['ship_id'], $ship_id_arr) )
            {
                continue;
            }

            self::delete($shipFeeOld['ship_id']);
        }

        // Query multi
        $sql_multi_value = trim($sql_multi_value);
        $sql_multi_value = trim($sql_multi_value, ',');
        if( $sql_multi_value )
        {
            $sql_multi = "
            INSERT INTO ".root_table."shipping_fee 
            (product_id, ship_type_service, ship_price, ship_price_extra, ship_time, ship_location, user_id) 
            VALUES 
            {$sql_multi_value} 
            ";
            // print $sql_multi; exit;
            $query_multi = $DB->query($sql_multi);

            // Logs
            if( $query_multi )
            {
                // Clear cache
                $CMS->class->cache->mdelete(self::$cache_prefix);

                $CMS->class->logs->insert("{$CMS->lang['add_new_shipping_fee_success']}" );
                $_SESSION['msg'] .= $CMS->lang['add_new_shipping_fee_success'];
                return true;
            }
            else
            {
                $CMS->class->logs->insert("{$CMS->lang['add_new_shipping_fee_failed']}" );
                $_SESSION['error_msg'] .= $CMS->lang['add_new_shipping_fee_failure'];
                return false;
            }
        }

        return true;
    }
}