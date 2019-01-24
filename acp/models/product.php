<?php

namespace models;

use \core\ezy;
use \lib\date;
use lib\input;

use models\variants;
ezy::load_model("variants");

class product
{
    /**
     * @var string $cache_prefix
     */
    static public $cache_prefix = 'product';

    /**
     * @param string $order
     * @param string $by
     * @return array
     */
    static public function getAll($where = "", $orderBy="product_name ASC")
    {
        global $DB;
        $orderBy = $orderBy ?  $orderBy : 'product_name ASC';
        $sql = "SELECT * FROM " . root_table . "product WHERE {$where} product_deleted=0  ORDER BY {$orderBy}";
        return $DB->fetch_data($sql, 'product');
    }

    /**
     * @param $id
     * @param string $where
     * @return mixed
     */
    static public function getInfo($id, $where = "") {
        global $DB;
        $id *= 1;
        $sql = "SELECT * FROM " . root_table . "product WHERE {$where} product_id={$id} AND product_deleted=0 LIMIT 1";
        $rs = $DB->fetch_data($sql, 'product');
        return input::arrayValue($rs, 0);
    }

    /**
     * @param $key, $type, $product_id, $store_id
     * @param string $where
     * @return mixed
     * Clone from kernel product
     */
    static public function searchKey( $key='', $type = 0 , $product_id = '', $store_id = '' )
    {
        global $CMS, $DB;

        $output = [];
        $module_name = $CMS->input['module_name'];
        $clause = "";

        if( $key )
        {
            $clause = " AND (product_name LIKE '%{$key}%' OR product_code LIKE '%{$key}%' OR product_sku LIKE '%{$key}%' OR product_barcode LIKE '%{$key}%') ";
        }

        if( $type == 0 )
        {
            // Loc theo loại hàng hoa
            $clause .= " AND product_type = '{$type}' ";
        }

        if( $product_id != "" AND $product_id > 0 )
        {
            // Search loai tru san pham goc
            $clause .= " AND product_id != '{$product_id}' ";
        }

        if( $store_id )
        {
            $clause .= " AND store_id = '{$store_id}' ";
        }

        $sql = "
        SELECT * FROM ".root_table."product 
        WHERE product_deleted = 0 {$clause} 
        ORDER BY product_id desc 
        LIMIT 10 
        ";
        // print $sql; exit;
        
        $results = $DB->fetch_data($sql, self::$cache_prefix);
        if( is_array($results) )
        {
            foreach( $results as $result )
            {
                // Over write gia ban, gia nhap
                //$result['product_price'] = $result['product_price_sell'] != 0 ? $result['product_price_sell'] : $result['product_price'];
                if( $module_name != "module_storerequest" ) // Neu moduel store request lay gia nhap
                {
                    $result['product_price'] = $result['product_price_sale'] > 0 ? $result['product_price_sale'] : $result['product_price_sell'];
                }

                $result['sup_name'] = "";
                if( $result['sup_id'] )
                {
                    $result['sup_name'] = $CMS->supplier->get_info($result['sup_id'],"supplier_name");
                }

                $result['product_price_show'] = $CMS->class->input->currency($result['product_price']);
                $result['product_description'] = strip_tags($result['product_description']);

                // variants
                $result['variants'] = \models\variants::getVariantsByProductId($result['product_id']);
                
                $output[] = $result;
            }
        }

        return $output;
    }

    /**
     * @param $product_id, $cus_id, $store_id
     * @param string $where
     * @return mixed
     * Clone from kernel product
     */
    static public function searchKey_byCus(  $product_id = '', $cus_id = '', $store_id = '')
    {
        global $CMS, $DB;

        $data = [];
        $module_name = $CMS->input['module_name'];

        $sql_add = "";
        if( $store_id )
        {
            $sql_add .= " AND store_id = '{$store_id}' ";
        }

        $sql = "
        SELECT DISTINCT product_id 
        FROM ".root_table."order_item 
        WHERE cus_id = '{$cus_id}' AND product_id > 0 AND ordi_deleted = 0 {$sql_add} 
        ORDER BY  ordi_time desc 
        LIMIT 3 
        ";
        // print $sql; exit;

        $results = $DB->fetch_data($sql, self::$cache_prefix);
        if( is_array($results) )
        {
            foreach( $results as $result )
            {
                $result = $CMS->product->getInfo($result['product_id']);
                if( $module_name != "module_storerequest" ) // Neu moduel store request lay gia nhap
                {
                    $result['product_price'] = $result['product_price_sale'] > 0 ? $result['product_price_sale'] : $result['product_price_sell'];
                }
                
                $result['sup_name'] = "";
                if($result['sup_id'])
                {
                    $result['sup_name'] = $CMS->supplier->get_info($result['sup_id'],"supplier_name");
                }

                $result['product_price_show'] = $CMS->class->input->currency($result['product_price']);
                $result['product_description'] = strip_tags($result['product_description']);

                // variants
                $result['variants'] = \models\variants::getVariantsByProductId($result['product_id']);

                $data[] = $result;
            }
        }

        return $data;
    }
}