<?php

namespace models;

use core\ezy;
use lib\input;
use lib\language;
use lib\page;
use lib\cache;
use models\manufacture;
use models\supplier;
use models\city;
use models\district;
use models\attribute;
ezy::load_model("manufacture");
ezy::load_model("supplier");
ezy::load_model("city");
ezy::load_model("district");
ezy::load_model("attribute");

class product
{
    /**
     * Get List tag
     * @return array
     */
    static public $sqlAdd = "";
    static public $orderBy = "";
    static public $arrkey_color = array("color", "size", "volumetric", "capacity");

    static function getInfo($record_id=0, $field_return="*")
    {
        global $DB, $CMS;

        if($record_id and $field_return)
        {
            // Count field
            $countField = count(explode(",", $field_return));
            // sqladd
            $sql_add = self::$sqlAdd;
            //Query
            $data = $DB->fetch_data("SELECT {$field_return} FROM ".root_table."product WHERE product_deleted=0 AND (product_id='{$record_id}' OR product_shorturl='{$record_id}') {$sql_add} LIMIT 1",'product')[0];

            //Check return
            if($countField == 1 and $field_return != "*")
            {
                return $data[$field_return];
            }else
            {
                return $data;
            }
        }else
        {
            return false;
        }
    }

    static function createMsg($message="",$status="error")
    {
        if($status == "error")
        {
            $_SESSION['error_msg'] = $message;
            return false;
        }else
        {
            $_SESSION['msg'] = $message;
            return true;
        }
    }

    static function checkValidProduct($product_id=0)
    {
        global $CMS, $DB;

        if($product_id)
        {
            // Key product group for gift card
            // $key = "gift_cards";
            // $pg_id = $CMS->product_group->getInfo($key,"product_group_id");
            // AND product_group='{$pg_id}' 
            // 19/06: doi lai check nhung san pham thuoc product
            //Query
            $DB->query("SELECT 0 FROM ".root_table."product WHERE product_deleted=0 AND product_show='1' AND product_id='{$product_id}' ORDER BY product_id DESC LIMIT 1");
            if($DB->num_rows() > 0)
            {
                return true;
            }else
            {
                return false;
            }
        }else
        {
            return false;
        }
    }

    /**
     * Get List Manufacturer
     * @return array
     */
    static public function getListManufacture()
    {
        global $DB, $CMS;

        // Select Manufacturer
        $results = $DB->fetch_data("SELECT manufacture_id AS id, manufacture_name AS name, manufacture_description as description, manufacture_avartar as avartar, manufacture_parent as parent
                    FROM ".root_table."manufacture
                    WHERE manufacture_deleted=0 AND manufacture_status=1
                    ORDER BY manufacture_parent ASC, manufacture_name ASC", 'manufacture');

        $output = [];

        // Load Data
        $i=0;
        $tmp_data = array();
        foreach ( $results as $data )
        {
            // pathUpload: use for seo get thumb
            $data['pathUpload'] = 'manufacture/'.$data['avartar'];
            
            // convert image
            $data['avartar'] = input::checkImage('manufacture/'.$data['avartar']);

            if ( $data['parent'] <= 0 ) // Group
            {
                $output[$i] = $data;
                $output[$i]['ListManufactureId'] = array($data['id']);
                $output[$i]['ListSubManufacture'] = array();

                $tmp_data[$data['id']] = $i;
                $i++;
            }
            else // SubGroup
            {
                $output[$tmp_data[$data['parent']]]['ListManufactureId'][] = $data['id'];
                $output[$tmp_data[$data['parent']]]['ListSubManufacture'][] = $data;
            }
        }

        return $output;
    }

    /**
     * Get List Group
     * @return array
     */
    static public function getListGroup( $product_group_id = 0, $limit = 0 )
    {
        global $DB, $CMS;

        // get list id group and count product
        $dataListGroupCountProduct = self::getCountProductInGroup();
        
        $product_group_parent = $product_group_id ? " AND (product_group_parent='{$product_group_id}' OR product_group_id = '{$product_group_id}') " : "";
        // Select Group
        $resutls = $DB->fetch_data("SELECT product_group_id AS id, product_group_code AS code, product_group_name AS name, product_group_description as description, product_group_parent as parent, pg_shorturl AS url,product_group_avatar AS image
                    FROM ".root_table."product_group 
                    WHERE product_group_deleted=0 AND product_group_status=1 AND product_group_type=0  {$product_group_parent} 
                    ORDER BY product_group_parent ASC, product_group_order ASC", 'product_group');

        $output = [];
        
        // Load Data
        $tmp_data = array();

        if($resutls)
        {
            foreach($resutls as $data)
            {
                $data = self::convertGroup($data);
                $data['countProduct']=$dataListGroupCountProduct[$data['id']];
                if ( $data['parent'] <= 0 ) // Group
                {
                    $output[$data['code']] = $data;
                    $output[$data['code']]['ListGroupId'] = array($data['id']);
                    $output[$data['code']]['ListSubGroup'] = array();
                    $output[$data['code']]['countProduct']=$dataListGroupCountProduct[$data['id']];
                    $tmp_data[$data['id']] = $data['code'];
                }
                else // SubGroup
                {
                    if(isset($tmp_data[$data['parent']]))
                    {
                        $output[$tmp_data[$data['parent']]]['ListGroupId'][] = $data['id'];
                        $output[$tmp_data[$data['parent']]]['ListSubGroup'][] = $data;
                        $output[$tmp_data[$data['parent']]]['countProduct']+=$dataListGroupCountProduct[$data['id']];
                    }
                }
            }
        }


        // Limit
        // foreach because group contain subgroup
        if ( $limit > 0 ) 
        {
            $temp_output = $output;
            $output = [];
            $i = 0;
            foreach ( $temp_output as $data ) 
            {
                // Check limit
                $i++;
                if ( $i > $limit ) 
                {
                    break;
                }

                // set output
                $output[$data['code']] = $data;
            }
        }
        
        return $output;
    }

    /**
     * Get List Group Id
     * @return array
     */
    static public function getListGroupId($product_group_id = 0)
    {
        global $DB, $CMS;

        // Load Data
        $output = [];
        $product_group_parent = "";
        if($product_group_id)
        {
            $output[] = $product_group_id;
            // Product Group Parent Id
            $product_group_parent = " AND product_group_parent='{$product_group_id}' ";
        }

        // Select Group
        $results = $DB->fetch_data("SELECT product_group_id AS id
                    FROM ".root_table."product_group 
                    WHERE product_group_deleted=0 AND product_group_status=1 AND product_group_type=0 {$product_group_parent} 
                    ORDER BY product_group_id ASC", 'product_group');

        if(!$results) return $output;

        foreach( $results as $data )
        {
            $output[] = $data['id'];
        }

        return $output;
    }

    /**
     * Get List Group Id
     * @return array
     */
    static public function getGroupInfo($product_group_id = 0)
    {
        global $DB, $CMS;

        // Select Group
        $data = $DB->fetch_data("SELECT product_group_id AS id, product_group_code AS code, product_group_name AS name, product_group_description as description, product_group_parent as parent, pg_shorturl AS url
                    FROM ".root_table."product_group 
                    WHERE product_group_deleted=0 AND product_group_status=1 AND product_group_type=0 AND ( product_group_id='{$product_group_id}' OR pg_shorturl='{$product_group_id}' ) 
                    LIMIT 1",'product_group');

        // Load Data
        $output = isset($data[0]) ? $data[0] : null;
        $output = self::convertGroup($output);
        $output['ListGroupId'] = $DB->num_rows()==1 ? self::getListGroupChildId($output['id'], 0, array($product_group_id => $product_group_id)) : array($product_group_id);
        
        return $output;
    }

    /**
     * Convert group data
     * @param $data
     * @return mixed
     */

    static private function convertGroup($data)
    {
        global $CMS;

        if(empty($data)) return null;
        
        $data['code'] = trim($data['code']);
        // $data['url'] = $data['url'] ? "/product/group/{$data['url']}-shorturl" : "/product/group/group-{$data['id']}";
        $data['url_original'] = $data['url'];
        $data['url_none_html'] = "/{$data['url']}-pc{$data['id']}";
        $data['url'] = "/{$data['url']}-pc{$data['id']}.html";
        $path='product/'.( isset($data['image']) ? $data['image'] : '' );
        $data['pathUpload'] = $data['uploadPath'] = $path;
        $data['image'] = input::checkImage($path);
         
        return $data;
    }

    /**
     * Get List product
     * @return array
     * note: product_option: 0: all, 1:sản phẩm nổi bật, 2: sản phẩm xem nhiều, 3: Xu hướng thị trường, 4: Bán chạy, 5: Mới
     * note: product_tra_type: 0: all, 1: tour trong nước, 2: tour ngoài nước
     */

    static public function getListProduct($product_group = 0, $product_is_promotional = 0, $product_id_except = 0, $limit = 5, $paging = false, $product_option = 0, $product_tra_type = 0)
    {
        global $CMS, $DB, $tpl;

        self::$sqlAdd = '';

        // Promotional product
        $product_is_promotional = $product_is_promotional ? " AND ( product_price_old > 0 AND product_price_old > product_price_sell)" : "";
        self::$sqlAdd .= $product_is_promotional;

        // Except product
        $product_id_except = $product_id_except ? " AND product_id<>'{$product_id_except}'" : "";
        self::$sqlAdd .= $product_id_except;

        // Product option
        if(!is_array($product_option)) {
            $product_option = $product_option > 0 ? " AND ( product_option LIKE '%,{$product_option},%' OR product_option = '{$product_option}')" : "";
            self::$sqlAdd .= $product_option;
        }else{
            $product_option_sql='';
            foreach ($product_option as $item) {
                if($item){
                    $product_option_sql.= " AND ( product_option LIKE '%,{$item},%' OR product_option = '{$item}')";
                    self::$sqlAdd .= $product_option_sql;
                }
            }
        }
        // Product group 
        if ( $product_group )
        {
            if ( is_array($product_group) )
            {
                $temp_product_group = "";
                $i = 0;
                foreach ( $product_group as $id ) 
                {
                    $temp_product_group .= $i == 0 ? "'{$id}'": ", '{$id}'";
                    $i++;
                }
                $product_group = $temp_product_group ? " AND P.product_group IN ({$temp_product_group})" : "";
            }
            else
            {
                $product_group = " AND P.product_group = '{$product_group}'";
            }
        }
        else
        {
            $product_group = "";
        }
        self::$sqlAdd .= $product_group;

        // Keyword
        $tpl->keyword = isset($tpl->keyword) ? $tpl->keyword : null;
        $key_search = urldecode($tpl->keyword);
        $keyword = empty($tpl->keyword) ? "" : " AND (product_name_lang LIKE '%{$key_search}%' OR product_name LIKE '%{$key_search}%' OR product_description_lang LIKE '%{$key_search}%' OR product_description LIKE '%{$key_search}%' OR U.user_display_name LIKE '%{$key_search}%') ";
        self::$sqlAdd .= $keyword;
        
        // price from to
        $price_from = empty($tpl->price_from) ? "" : " AND product_price_sell >= '{$tpl->price_from}'";
        self::$sqlAdd .= $price_from;
         
        $price_to = empty($tpl->price_to) ? "" : " AND product_price_sell <= '{$tpl->price_to}'";
        self::$sqlAdd .= $price_to;

        // use for rea theme
        if ( ! empty($tpl->is_rea) AND ! empty($tpl->price_agreed) ) 
        {
            $product_up = " AND product_up = 'price_agreed' ";
            self::$sqlAdd .= $product_up;
        }
        
        // Manufacture
        $manufacture = "";
        if ( ! empty($tpl->manufacture) ) 
        {
            if ( is_array($tpl->manufacture) )
            {
                $arr_manuf=array();
                foreach ($tpl->manufacture as $manuf){
                    if($manuf){
                        $arr_manuf[]=$manuf;
                    }
                }
                if($arr_manuf){
                    $manufacture =  " AND P.product_manufacture IN (". rtrim(ltrim(implode(',', $arr_manuf),","),",").")";
                }
            }
            else
            {
                $manufacture = " AND P.product_manufacture = '{$tpl->manufacture}'";
            }
        }
        self::$sqlAdd .= $manufacture;
        
        
        // Manufacture
        // $sql_color = "";
        // Backup
        // if ( ! empty($tpl->color) ) 
        // {
        //     if ( is_array($tpl->color) )
        //     {
        //         $color_i = 0;
        //         $tt=0;
        //         foreach ($tpl->color as $color){
        //             if($color){
        //                 if($tt==0){
        //                     $sql_color.=  " AND ( P.product_attribute_custom LIKE '%:\"".$color."\"%' " ;
        //                     $tt=1;
        //                 }else{
        //                     $sql_color.=  " OR P.product_attribute_custom LIKE '%:\"".$color."\"%' " ;
        //                 }
        //             }
        //         }
        //         if($tt==1){
        //             $sql_color.=')';
        //         }
        //     }
        //     else
        //     {
        //         $sql_color.=  " AND P.product_attribute_custom LIKE '%:\"".$tpl->color."\"%' " ;
        //     }
        // }

        $sql_attr = "";
        if ( ! empty($tpl->listOptionSearch) ) 
        {
            // $listattr = explode(",",$tpl->listOptionSearch);
            // Attribute
            $listattr = self::convertStringAttribute($tpl->listOptionSearch);
            foreach ($listattr as $options)
            {
                $sql_qr = "";
                foreach ($options as $options_id) 
                {
                    $sql_qr .= $options_id ? " OR P.product_attribute_custom LIKE '%:\"".$options_id."\"%'" : "";    
                }
                // remove Or thừa
                $sql_attr .= " AND (".ltrim($sql_qr, " OR ").")";
            }
            
        }

        self::$sqlAdd .= $sql_attr;
        
        // str product id
        $list_id_product='';
        if(!empty($tpl->list_id_product)){
            if(is_array($tpl->list_id_product)){
                
                $str_id_product = implode(',',$tpl->list_id_product);
            }else{
                $str_id_product=$tpl->list_id_product;
            }
            $list_id_product.=' AND product_id in ('.$str_id_product.') ';
        }
        self::$sqlAdd .= $list_id_product;

        $order_by='';
        if(isset($tpl->sort))
        {
            if($tpl->sort==1){
                $order_by =  ' product_price_sell ASC,';
            }else if($tpl->sort==2){
                $order_by =  ' product_price_sell DESC,';
            }
        }

        // Product real type: use for realtor themes
        $tpl->product_real_type = isset($tpl->product_real_type) ? $tpl->product_real_type : null;
        $product_real_type = $tpl->product_real_type ? " AND product_real_type = '{$tpl->product_real_type}'" : "";
        self::$sqlAdd .= $product_real_type;

        $parent_id = '';

        if(!empty($tpl->parent_id)){
            $parent_id.=' AND parent_id = '.$tpl->parent_id;
            self::$sqlAdd .= $parent_id;
        }else
        {
             // self::$sqlAdd .= ' AND parent_id!=0 ';
        }

        // Product tra type: use for travel themes
        $product_tra_type = $product_tra_type > 0 ? " AND product_tra_type = '{$product_tra_type}'" : "";
        self::$sqlAdd .= $product_tra_type;

        // SQL QUERY
        // Tan xem lai cho nay chut nha
        // AND parent_id != 0
        $sql_query = "SELECT product_id AS id, product_name AS name, product_description AS description, product_image AS image, product_image_alt AS image_alt, product_price_old AS price_old, product_price_sell AS price_sell, product_shorturl AS url, product_option AS `option`, product_attribute AS attribute, product_up AS up, product_time AS `time`, product_group_id AS group_id, product_group_name AS group_name, pg_shorturl AS group_url, U.user_display_name, product_name_lang, product_shorturl_lang, product_description_lang, product_information_1_lang, product_information_2_lang, product_manufacture, product_real_type as real_type, product_attribute_custom AS attribute_custom, product_series {$select_extend} 
                    FROM ".root_table."product AS P LEFT JOIN ".root_table."product_group AS PG ON P.product_group=PG.product_group_id LEFT JOIN ".root_table."manufacture As M ON P.product_manufacture=M.manufacture_id LEFT JOIN ".root_table."user As U ON P.user_id=U.user_id 
                    WHERE product_deleted=0  AND product_type=0 AND product_show='1' ".self::$sqlAdd."  ORDER BY {$order_by} product_order ASC, product_time DESC";
// p($sql_query);exit;

        // SQL Select product & product group & paging
        $results = page::init($sql_query, $limit, $paging,'web.product.product_group.user');

        // Declare output
        $output = [];
       
        // Load data
        if($results)
        {
            foreach ( $results as $data )
            {
                $output[] = self::convertProduct($data);
            }
        }
        // Output
        
        return $output;
    }

    /**
     * Get List product
     * @return array
     */

    static public function getListProductWithGroup( $limit = 5 )
    {
        global $CMS, $DB, $tpl;        

        $output = self::getListGroup();
        
        if ( count($output) > 0 )
        {
            foreach ( $output as $key => $category ) 
            {
                $output[$key]['ListProduct'] = self::getListProduct($category['ListGroupId'], 0, 0, $limit);
// print "dadsf "; print_r($output[$key]['ListProduct']);exit;
                $output[$key]['ListProductPromotional'] = self::getListProduct($category['ListGroupId'], 1, 0, $limit); 
                
                // Product Promotional
                $output[$key]['ListProductHot'] = self::getListProduct($category['ListGroupId'], 0, 0, $limit, false, 1); // Product hot
                $output[$key]['ListProductViewMore'] = self::getListProduct($category['ListGroupId'], 0, 0, $limit, false, 2); // Product view more
                $output[$key]['ListProductTrending'] = self::getListProduct($category['ListGroupId'], 0, 0, $limit, false, 3); // Product Trending
                $output[$key]['ListProductBestSeller'] = self::getListProduct($category['ListGroupId'], 0, 0, $limit, false, 4); // Product Best Seller
                $output[$key]['ListProductNew'] = self::getListProduct($category['ListGroupId'], 0, 0, $limit, false, 5); // Product New
            }
        }
        
        return $output;
    }

    /**
     * Get Detail Product
     */
    static public function getProduct($product_shorturl)
    {
        global $DB;

        // Tạm thời bỏ điều kiện này vì get info ko chính xác : product_shorturl_lang LIKE '%{$product_shorturl}%' or
        $data = $DB->fetch_data("SELECT product_id AS id, product_code AS code, product_sku AS sku, product_name AS name, product_description AS description, product_image AS image, product_image_alt AS image_alt, product_gallery AS gallery, product_information_1 AS information_1, product_information_2 AS information_2, product_price_old AS price_old, product_price_sell AS price_sell, product_shorturl AS url, product_option AS `option`, product_time AS `time`, product_group_id AS group_id, product_group_name AS group_name, pg_shorturl AS group_url, U.user_display_name, product_name_lang, product_shorturl_lang, product_description_lang, product_information_1_lang, product_information_2_lang, product_manufacture as manufacture,sup_id, product_attribute AS attribute, product_up AS up,product_real_type as real_type, product_guarantee_default AS guarantee, product_stock_available AS stock_available, product_attribute_custom AS attribute_custom, parent_id, product_series 
                    FROM ".root_table."product AS P LEFT JOIN ".root_table."product_group AS PG ON P.product_group=PG.product_group_id LEFT JOIN ".root_table."user As U ON P.user_id=U.user_id 
                    WHERE product_deleted=0 AND product_type=0 AND product_show='1' AND (product_shorturl='{$product_shorturl}' OR product_id='{$product_shorturl}') LIMIT 1", 'product.product_group.user');

        return self::convertProduct($data[0]);
    }

    /**
     * Convert product data
     * @param $data
     * @return mixed
     */

    static function convertProduct($data)
    {
        global $CMS;
// print "<pre>"; print_r($data);exit;
        
// print_r($content);exit;

        // Load language
        $CMS->class->language->load("product");

        if($CMS->vars['translations'])
        {
            $name = @json_decode($data['product_name_lang'], true);
            $data['name'] = $name ? $name : $data['name'];

            $shorturl = @json_decode($data['product_shorturl_lang'], true);
            $data['url'] =   $shorturl ? $shorturl : $data['url'];
    // print "<pre>";print_r($data['news_description']);exit;
            $description = @json_decode($data['product_description_lang'], true);
            $data['description'] = $description ? $description : $data['description'];// of news

            $information_1 = @json_decode($data['product_information_1_lang'], true);
            $data['information_1'] = $information_1 ? $information_1 : (isset($data['information_1']) ? $data['information_1'] : null);

            $information_2 = @json_decode($data['product_information_2_lang'], true);
            $data['information_2'] = $information_2 ? $information_2 : (isset($data['information_2']) ? $data['information_2'] : null);

            if(is_array($data['name']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    if($langCode == $CMS->vars['default_language'])
                    {
                        $data['name'] = $data['name'][$langCode];
                        break;
                    }
                }
            }

            if(is_array($data['url']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    if($langCode == $CMS->vars['default_language'])
                    {
                        $data['url'] = $data['url'][$langCode];
                        break;
                    }
                }
            }

            if(is_array($data['description']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    if($langCode == $CMS->vars['default_language'])
                    {
                        $data['description'] = html_entity_decode($data['description'][$langCode]);
                        break;
                    }
                }
            }

            if(is_array($data['information_1']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    if($langCode == $CMS->vars['default_language'])
                    {
                        $data['information_1'] = html_entity_decode($data['information_1'][$langCode]);
                        break;
                    }
                }
            }

            if(is_array($data['information_2']))
            {
                //Neu k phai dang mang thi chuyen ve mang
                foreach ($CMS->vars['translations'] as $langCode => $langName)
                {
                    if($langCode == $CMS->vars['default_language'])
                    {
                        $data['information_2'] = html_entity_decode($data['information_2'][$langCode]);
                        break;
                    }
                }
            }

        }else
        {
            $data['description'] = html_entity_decode(isset($data['description']) ? $data['description'] : '');
            $data['information_1'] = html_entity_decode(isset($data['information_1']) ? $data['information_1'] : null);
            $data['information_2'] = html_entity_decode(isset($data['information_2']) ? $data['information_2'] : null);
        }

        // Name
        $data['name'] = html_entity_decode($data['name'], ENT_QUOTES | ENT_XML1, 'UTF-8');

        // $data['url'] = "/product/detail/{$data['url']}-shorturl";
        $data['url_original']  =  $data['url'] = isset($data['url']) ? $data['url'] : '';
        $data['url_none_html'] = "/{$data['url']}-sp{$data['id']}";
        $data['url'] = "/{$data['url']}-p{$data['id']}.html";

        //Check thumb
        $data['image'] = isset($data['image']) ? $data['image'] : (isset($data['product_image']) ? $data['product_image'] : '');
        $path = "product/{$data['image']}";

        // pathUpload: use for seo get thumb
        $data['pathUpload'] = $path;

        //Sise L
        $thumb = \lib\image::getThumb($path, "thumbnail", 'L_', 1, 550);
        $data['image_L'] = input::checkImage($thumb);

        //Sise M
        $thumb = \lib\image::getThumb($path, "thumbnail", 'M_', 1, 300);
        $data['image_M'] = input::checkImage($thumb);

        //Sise S
        $thumb = \lib\image::getThumb($path, "thumbnail", 'S_', 1, 150);
        $data['image'] = $data['image_S'] = input::checkImage($thumb);

        $data['description_ori'] = $data['description'];
        $data['description'] = input::substr($data['description'],0,50);
         $data['group_url_none_html'] = !empty($data['group_url']) ? "/{$data['group_url']}-pc{$data['group_id']}" : "";
        $data['group_url'] = !empty($data['group_url']) ? "/{$data['group_url']}-pc{$data['group_id']}.html" : "";
        $data['short_name'] = $data['name'];//$CMS->class->editor->substr($data['name'], 0, 25);
        $data['price_old'] = isset($data['price_old']) ? $data['price_old'] : 0;
        $data['price_sell'] = isset($data['price_sell']) ? $data['price_sell'] : 0;
        $data['sale_off']= $data['price_old'] > $data['price_sell']?round(($data['price_old']-$data['price_sell'])/$data['price_old']*100).'%':'';
        $data['price_sell'] = $data['price_sell'] > 0 ? $CMS->class->input->currency($data['price_sell']) : "";
        $data['price_old'] = ($data['price_old'] > 0 and $data['price_sell'])? $CMS->class->input->currency($data['price_old']) : "";
        $data['allow_cart'] = 1;
        // For theme mer
        if( in_array(ezy::$theme_key, array("fme","mer", "wco", "fco","nms")) )
        {
            $data['title_button'] = $data['price_sell'] ? $CMS->lang['title_price_sell'] : $CMS->lang['title_price_contact'];
            $data['link_button'] = $data['price_sell'] ? "/cart/addcart/{$data['id']}" : "/contact";
            $data['allow_cart'] = $data['price_sell'] ? 1 : 0;
        }

        // For theme nms
        if( in_array(ezy::$theme_key, array("nms")) )
        {
            $data['price_sell'] = $data['price_sell'] ? $data['price_sell'] : $CMS->lang['price_0'];
        }

        // List gallery
        $data['gallery'] = isset($data['gallery']) ? $data['gallery'] : null;
        $data['gallery'] = json_decode($data['gallery'], true);

        $listGallery = array();

        if ( !empty($data['gallery']) )
        {
            $i=0;
            foreach ( $data['gallery'] as $gallery ) 
            {
                // pathUpload: use for seo get thumb
                $listGallery[$i]['pathUpload'] = $gallery;

                //Sise L
                $thumb = \lib\image::getThumb($gallery, "thumbnail", 'L_', 1, 550);
                $listGallery[$i]['image_L'] = input::checkImage($thumb);

                //Sise M
                $thumb = \lib\image::getThumb($gallery, "thumbnail", 'M_', 1, 300);
                $listGallery[$i]['image_M'] = input::checkImage($thumb);

                $i++;
            }
        }
        $data['gallery'] = $listGallery;

        // Product option
        $data['option'] = isset($data['option']) ? $data['option'] : '';
        $options = explode(',',$data['option']);
        $data['option'] = array();
        foreach ( $options as $option ) 
        {
            if ( ! empty($option) ) 
            {
                $data['option'][] = $option;
            }
        }
        
        // Attribute
        $data['attribute'] = json_decode(!empty($data['attribute'])?$data['attribute']:null, true);
        // Attribute theo quy trình mới
        $data['attribute_custom'] = self::getListAttributeProduct($data['attribute_custom']);
        

        if(isset($data['attribute']['city']) && $data['attribute']['city']){
            $city = city::getInfo($data['attribute']['city']);
        }
        if(isset($data['attribute']['district']) && $data['attribute']['district']){
            $district = district::getInfo($data['attribute']['district']);
        }

        if( isset( $city ) && isset( $district ) )
        {
            $address  = !empty($data['attribute']['street']) ? $data['attribute']['street'] . ',' : '';
            $address .=  !empty($data['attribute']['wards']) ? $data['attribute']['wards'] . ',' : '';
            $address .= $district['district_type'] .' '. $district['district_name'] . ', ' . $city['city_type'] . ' ' . $city['city_name'];
            
            $data['attribute']['address']=$address;
        }

        // Unit price
        $data['up'] = isset($data['up']) ? $data['up'] : null;
        $CMS->lang["title_unit_{$data['up']}"] = isset($CMS->lang["title_unit_{$data['up']}"]) ? $CMS->lang["title_unit_{$data['up']}"] : '';

        $data['unit_price'] = isset($CMS->lang["title_unit_{$data['up']}"]) ? $CMS->lang["title_unit_{$data['up']}"] : null;

        // Map
        $data['attribute']['map'] = isset($data['attribute']['map']) ? $data['attribute']['map'] : null;
        $data['attribute']['map'] = html_entity_decode($data['attribute']['map'], ENT_QUOTES | ENT_XML1, 'UTF-8');

        // lay thong tin nha san xuat va thuong hieu
        if(isset($data['manufacture']) && $data['manufacture']){
            $data['manufacture_id']=$data['manufacture'];
            $manufa = manufacture::getInfo($data['manufacture']);
            $data['manufacture']=$manufa;
            $data['manufacture_name']=$manufa['manufacture_name'];
        }
        if(isset($data['sup_id']) && $data['sup_id']){
            $sup = supplier::getInfo($data['sup_id']);
            $data['supplier']=$sup;
        }

        $data['stock_available_ori'] = !empty($data['stock_available']) ? $data['stock_available'] : null;
        $data['stock_available'] = !empty($data['stock_available']) ? intval($data['stock_available']) : 0;
        $data['stock_available'] = $CMS->lang["product_stock_available_{$data['stock_available']}"];
        
        return $data;
    }
    /**
     * Add product follow
     * @param $customer_id,$product_id
     * @return boolean
     */
    static public  function add_product_follow($customer_id='',$product_id='')
    {
        global $DB;
        
        if($product_id && $customer_id){
            $sql ="INSERT INTO ".root_table."product_follows (`product_id`, `customer_id`) VALUES ({$product_id}, {$customer_id})";
            
            $result = $DB->query($sql);
            return $result;
        } else {
            return false;
        }
        
    }
    /**
     * Add product follow
     * @param $customer_id,$product_id
     * @return boolean
     */
    static public  function delete_product_follow($customer_id='',$product_id='')
    {
        global $DB;
        if($product_id && $customer_id){
            $sql = "DELETE FROM ".root_table."product_follows WHERE product_id = {$product_id} and customer_id = {$customer_id}";
            $result = $DB->query($sql);
            return $result;
        } else {
            return false;
        }
        
    }
    /**
     * check isset product follow
     * @param customer_id product_id
     * @return boolean
     */
    static public  function check_product_follow($customer_id='',$product_id=''){
        global $DB;
        if($customer_id && $product_id){
         $sql_query="SELECT count(product_id) as count_id FROM ".root_table."product_follows WHERE customer_id = {$customer_id} and product_id = {$product_id}";
                        
             $DB->query($sql_query);
             $data = $DB->fetch_array();
             if($data['count_id']>0){
                 return TRUE;
             }else{
                 return FALSE;
             }
        }else{
            return FALSE;
        }
    }
    
    static public  function list_id_product_follow($customer_id='')
    {
        global $DB;
        if($customer_id){
            $output = [];
            $sql_query="SELECT product_id 
                        FROM ".root_table."product_follows 
                        WHERE customer_id = {$customer_id}";
             $DB->query($sql_query);
             while ( $data = $DB->fetch_array() )
                {
                    $output[] = $data['product_id'];
                }
                return $output;
        } else {
            return false;
        }
        
    }
    
    
    static public function getListProductAtTriBute($arr_attribute=array(),$limit=20,$paging=true,$order_by=''){
        global $DB;
        
        $sql_adtribute=self::getSqlAtTriBute($arr_attribute);
        // SQL QUERY
        $sql_query ="SELECT product_id AS id, product_name AS name, product_description AS description, product_image AS image, product_image_alt AS image_alt, product_price_old AS price_old, product_price_sell AS price_sell, product_shorturl AS url, product_option AS `option`, product_time AS `time`, product_name_lang, product_shorturl_lang, product_description_lang, product_information_1_lang, product_information_2_lang, product_attribute_custom AS attribute_custom, product_series
                    FROM ".root_table."product 
                    WHERE product_deleted=0 AND product_type=0  {$sql_adtribute} ORDER BY {$order_by} product_order ASC"; // product_time DESC
      // return $sql_query."<br/>" ;
        // SQL Select product & product group & paging
        $results = page::init($sql_query, $limit, $paging,'web.product');

        // Declare output
        $output = [];
        
        // Load data
        if($results)
        {
            foreach ( $results as $data )
            {
                $output[] = self::convertProduct($data);
            }
        }


        // Output
        return $output;
    }
    
    static public function getSqlAtTriBute($arr_attribute){
        $sql_adtribute='';
        
        if(!empty($arr_attribute['list_id_product'])){
            if(is_array($arr_attribute['list_id_product'])){
                
                $list_id_product= implode(',',$arr_attribute['list_id_product']);
            }else{
                $list_id_product=$arr_attribute['list_id_product'];
            }
            $sql_adtribute.=' AND product_id in ('.$list_id_product.') ';
        }
        return $sql_adtribute;
    }
    
     /**
     * get brand and price show in widget
     * @param $group_id
     * @return max_price, min_price, list brand
     */
    
     static public function getBrandAndPrice($group_id = '', $promotion = '') {
        global $tpl, $CMS, $DB;
        
//        if($CMS->class->cache->check('data_search_'.$group_id.'_'.$promotion)){
//            
//            $datas= json_decode($CMS->class->cache->load('data_search_'.$group_id.'_'.$promotion),true);
//            
//        }else{
            
        
        // get list group pareat
        $sql_add = "";
        if ($promotion) {
            $sql_add = $promotion ? "AND ( product_price_old > 0 AND product_price_old > product_price_sell)" : "";
        }

        $list_group = self::getListGroupId($group_id);
        if( !empty($list_group) AND is_array($list_group))
        {
            $sql_add .= " AND product_group in (" .rtrim(ltrim(implode(',', $list_group),","),",") . ") ";
        }

        // $sql_query = "SELECT product_id AS id, product_price_sell AS price_sell,product_manufacture AS manufact,product_attribute as attribute, product_attribute_custom AS attribute_custom, product_series
        //             FROM " . root_table . "product 
        //             WHERE product_deleted=0 AND product_type=0 {$sql_add} ORDER BY  product_order ASC";

        // Manufacture
        $sql_manufacture = "SELECT product_manufacture AS manufacture_id FROM " . root_table . "product WHERE product_deleted=0 AND product_type=0 {$sql_add} GROUP BY manufacture_id ORDER BY product_order ASC";
        $manufacture = $DB->fetch_data($sql_manufacture, 'manufacture');
        $curr_man=array();
        // $tpl->dataListManufacture: lấy từ application
        foreach($tpl->dataListManufacture as $data)
        {
            $curr_man[$data['id']]=$data;
        }

        $result = array();
        if($manufacture)
        {
            foreach ($manufacture as $manufacture_id)
            {
                $result[$manufacture_id] = $curr_man[$manufacture_id];
            }
        }

        // Max min price
        $sql_minmax = "SELECT MAX(product_price_sell) AS max_price, MIN(product_price_sell) AS min_price FROM " . root_table . "product WHERE product_deleted=0 AND product_type=0 {$sql_add} ORDER BY  product_order ASC";
        
        $data_minmax = $DB->fetch_data($sql_minmax, 'product')[0];
        
        // Get list attr
        $sql_attr = "SELECT product_attribute_custom AS attribute_custom FROM " . root_table . "product WHERE product_deleted=0 AND product_type=0 {$sql_add} ORDER BY  product_order ASC";
        
        $productData = $DB->fetch_data($sql_attr, 'product');
        
        $arr_attribute = [];
        $key_list = self::$arrkey_color;

        if($productData)
        {
            $arr_temp = [];
            foreach ($productData as $row)
            {
                // Push into array color, size quy trình mới
                $data_attribute = self::getListAttributeProduct($row['attribute_custom']);
                foreach ($data_attribute as $key => $attribute) 
                {
                    if(in_array($key, $key_list))
                    {
                        $options_id = $attribute['id'];
                        if(!in_array($options_id, $arr_temp) and $attribute['id'] )
                        {
                            $attribute['value'] = str_replace("#","", $attribute['value']);
                            $arr_attribute[$key][] = $attribute;
                            $arr_temp[] = $options_id;
                        }
                    }
                }
            }
        }

        
        $datas=array(
            'dataListManufacture'=>$result,
            'min_price'=> intval($data_minmax['min_price']),
            'max_price'=> intval($data_minmax['max_price']),
            'dataSearchOption'=>$arr_attribute
        );

//        $CMS->class->cache->save('data_search_'.$group_id.'_'.$promotion, json_encode($datas));
//        
//        }
        
        // $tpl->price_from = isset($tpl->price_from) && $tpl->price_from ? $tpl->price_from : floatval($datas['min_price']);
        // $tpl->price_to = isset($tpl->price_to) && $tpl->price_to ? $tpl->price_to : floatval($datas['max_price']);
        $tpl->dataListManufacture = $datas['dataListManufacture'];
        $tpl->dataSearchOption = $datas['dataSearchOption'];
        
        return $datas;
    }
/*
 * get count all product in group
 */
    static public function getCountProductInGroup(){
        global $DB,$CMS;
        // tam thoi commen 
//        if($CMS->class->cache->check('count_product_in_group')){
//            $datas= json_decode($CMS->class->cache->load('count_product_in_group'),true);
//        }else{
        $sql = 'SELECT npg.product_group_id as group_id,count(np.product_id) as countss  
                FROM nh_product_group as npg
                LEFT JOIN nh_product as np ON npg.product_group_id = np.product_group AND np.product_deleted=0 AND np.product_type=0
                WHERE npg.product_group_deleted = 0 AND npg.product_group_status=1 AND npg.product_group_type=0 GROUP BY npg.product_group_id  ';
        
        $results = $DB->fetch_data($sql, 'product_group.product');

        if($results)
        {
            foreach($results as $row){
                $datas[$row['group_id']]=$row['countss'];
            }
        }

//        $CMS->class->cache->save('count_product_in_group', json_encode($datas));
//        }
        return $datas;
    }

    function search_advande($limit, $paging)
    {
        global $CMS, $DB;

        $clause = "";
        $modLink = "/product/search_advande";
        if(!empty($CMS->input['group']))
        {
            $group = intval($CMS->input['group']);
            $clause .= " AND product_group=\"{$group}\" ";
            $modLink .= "/group_{$CMS->input['group']}";
        }

        if(!empty($CMS->input['key_search']))
        {
            $key_search = urldecode($CMS->input['key_search']);
            $clause .= " AND (product_name_lang LIKE \"%{$key_search}%\" OR product_name LIKE \"%{$key_search}%\" OR product_description_lang LIKE \"%{$key_search}%\" OR product_description LIKE \"%{$key_search}%\" ) ";
            $modLink .= "/key_search_".urlencode($CMS->input['key_search']);
        }

        if(!empty($CMS->input['price']))
        {
            $price = explode("-", $CMS->input['price']);
            $price_start = intval($price[0]) * 1000000;
            $price_end = intval($price[1]) <= 100000 ? intval($price[1]) * 1000000 : 100000;

            if($price_start < $price_end)
            {
                $clause .= " AND product_price_sell BETWEEN {$price_start} AND {$price_end} ";
            }elseif($price_end == 0 and $price_start == 0)
            {
                $clause .= " AND product_up = 'price_agreed' ";
            }
            $modLink .= "/price_{$CMS->input['price']}";
        }

        if(!empty($CMS->input['city']))
        {
            $city = intval($CMS->input['city']);
            $clause .= " AND JSON_EXTRACT(product_attribute, \"$.city\")=\"{$city}\" ";
            $modLink .= "/city_{$CMS->input['city']}";
        }

        if(!empty($CMS->input['district']))
        {
            $district = intval($CMS->input['district']);
            $clause .= " AND JSON_EXTRACT(product_attribute, \"$.district\")=\"{$district}\" ";
            $modLink .= "/district_{$CMS->input['district']}";
        }

        if(!empty($CMS->input['wards']))
        {
            $wards = $CMS->class->seo->remove_vietnamese(urldecode($CMS->input['wards']));
            $clause .= " AND JSON_EXTRACT(product_attribute, \"$.wards_search\") LIKE \"%{$wards}%\" ";
            $modLink .= "/wards_".urlencode($CMS->input['wards']);
        }

        if(!empty($CMS->input['street']))
        {
            $street = $CMS->class->seo->remove_vietnamese(urldecode($CMS->input['street']));
            $clause .= " AND JSON_EXTRACT(product_attribute, \"$.street_search\") LIKE \"%{$street}%\" ";
            $modLink .= "/street_".urlencode($CMS->input['street']);
        }

        if(!empty($CMS->input['acreage']))
        {
            $acreage = explode("-", $CMS->input['acreage']);
            // print "<pre>";print_r($acreage);exit;
            $arc_start = intval($acreage[0]);
            $arc_end = intval($acreage[1]) <= 2000 ? intval($acreage[1]) : 2000;
            if($arc_start < $arc_end)
            {
                $clause .= " AND JSON_EXTRACT(product_attribute, \"$.acreage\") BETWEEN {$arc_start} AND {$arc_end} ";
            }
            $modLink .= "/acreage_{$CMS->input['acreage']}";
        }

        if(!empty($CMS->input['direction']))
        {
            $direction = $CMS->input['direction'];
            $clause .= " AND JSON_EXTRACT(product_attribute, \"$.direction\")=\"{$direction}\" ";
            $modLink .= "/direction_{$CMS->input['direction']}";
        }

        $results = page::init("SELECT product_id AS id, product_name AS name, product_description AS description, product_image AS image, product_image_alt AS image_alt, product_price_old AS price_old, product_price_sell AS price_sell, product_shorturl AS url, product_option AS `option`, product_attribute AS attribute, product_up AS up, product_time AS `time`, product_group_id AS group_id, product_group_name AS group_name, pg_shorturl AS group_url, U.user_display_name, product_name_lang, product_shorturl_lang, product_description_lang, product_information_1_lang, product_information_2_lang, product_manufacture, product_real_type as real_type,product_up, product_attribute_custom AS attribute_custom, product_series 
                    FROM ".root_table."product AS P LEFT JOIN ".root_table."product_group AS PG ON P.product_group=PG.product_group_id LEFT JOIN ".root_table."user As U ON P.user_id = U.user_id 
                    WHERE product_deleted=0 AND product_show=1 AND JSON_VALID(product_attribute)=1 {$clause} ORDER BY product_id DESC", $limit, $paging, 'web.product.product_group.user');

        // Declare output
        $output = [];

        // Load data
        if($results)
        {
            foreach ( $results as $data )
            {
                $output[] = self::convertProduct($data);
            }
        }


        // Output
        return array($output, $modLink);

    }

    static function convertUrlSearch($root_link="search_advande", $line="_")
    {
        global $CMS;

       $link = $_SERVER['REQUEST_URI'];

       // Tách action search
        $str_search = explode("{$root_link}/", $link);
        $list_param = explode("/", isset($str_search[1]) ? $str_search[1] : '');
        
        $output = [];
        foreach ($list_param as $value)
        {
            $param = explode($line, $value);
            if(isset($param[0]))
            {
                $output[$param[0]] = isset($param[1]) ? $param[1] : null;
            }
        }

        return $output;
    }

    static function getInfoPGroup($record_id=0, $field_return="*")
    {
        global $DB, $CMS;

        if($record_id and $field_return)
        {
            // Count field
            $countField = count(explode(",", $field_return));
            // sqladd
            $sql_add = self::$sqlAdd;
            //Query
            $data = $DB->fetch_data("SELECT {$field_return} FROM ".root_table."product_group WHERE product_group_deleted=0 AND (product_group_id='{$record_id}' OR pg_shorturl='{$record_id}') {$sql_add} LIMIT 1", 'product_group')[0];

            //Check return
            if($countField == 1 and $field_return != "*")
            {
                return $data[$field_return];
            }else
            {
                return $data;
            }
        }else
        {
            return false;
        }
    }

    static public function update_sql()
    {
        global $CMS, $DB;

        // bỏ sau khoảng 1 tháng
        // bổ sung shorturl cho các url product group
        $sql = $DB->query("SELECT product_group_name, product_group_id FROM ".root_table."product_group WHERE pg_shorturl='' ");
        while ($data = $DB->fetch_array($sql)) {
            $pg_shorturl = $CMS->class->seo->cleanurl($data['product_group_name']);
            $DB->query("UPDATE ".root_table."product_group SET pg_shorturl = '{$pg_shorturl}' WHERE product_group_id='{$data['product_group_id']}'");
        }

        // bổ sung shorturl cho các url product
        $sql = $DB->query("SELECT product_name, product_id FROM ".root_table."product WHERE product_shorturl='' ");
        while ($data = $DB->fetch_array($sql)) {
            $product_shorturl = $CMS->class->seo->cleanurl($data['product_name']);
            $DB->query("UPDATE ".root_table."product SET product_shorturl = '{$product_shorturl}' WHERE product_id='{$data['product_id']}'");
        }

    }

    static function findTravel($key_search='')
    {
        global $CMS, $DB;

        $output = [];
        if($key_search)
        {
            $results = $DB->fetch_data("SELECT product_id AS id, product_name AS name, product_description AS description, product_image AS image, product_image_alt AS image_alt, product_price_old AS price_old, product_price_sell AS price_sell, product_shorturl AS url, product_option AS `option`, product_time AS `time`, product_name_lang, product_shorturl_lang, product_description_lang, product_information_1_lang, product_information_2_lang, product_attribute_custom AS attribute_custom, product_series 
                FROM ".root_table."product WHERE product_deleted=0 AND product_show=1 AND (product_name LIKE '%{$key_search}%' OR product_name_lang LIKE '%{$key_search}%' OR product_shorturl LIKE '%{$key_search}%' OR product_shorturl_lang LIKE '%{$key_search}%') ", 'product');

            foreach ($results as $result)
            {
                $output[] = self::convertProduct($result);
            }
        }

        return $output;
    }


    static function search_travel($limit, $paging)
    {
        global $CMS, $DB;

        $clause = "";
        $modLink = "/product/search_travel";
        if(!empty($CMS->input['group']))
        {
            $group = intval($CMS->input['group']);
            $clause .= " AND product_group=\"{$group}\" ";
            $modLink .= "/group_{$CMS->input['group']}";
        }

        if(!empty($CMS->input['keysearch']))
        {
            $keysearch = urldecode($CMS->input['keysearch']);
            $clause .= " AND (product_name_lang LIKE \"%{$keysearch}%\" OR product_name LIKE \"%{$keysearch}%\" OR product_description_lang LIKE \"%{$keysearch}%\" OR product_description LIKE \"%{$keysearch}%\" ) ";
            $modLink .= "/keysearch_".urlencode($keysearch);
        }

        if(!empty($CMS->input['price']))
        {
            $price = explode("-", $CMS->input['price']);
            $price_start = intval($price[0]) * 1000000;
            $price_end = intval($price[1]) <= 100000 ? intval($price[1]) * 1000000 : 100000;

            if($price_start < $price_end)
            {
                $clause .= " AND product_price_sell BETWEEN {$price_start} AND {$price_end} ";
            }elseif($price_end == 0 and $price_start == 0)
            {
                $clause .= " AND product_up = 'price_agreed' ";
            }
            $modLink .= "/price_{$CMS->input['price']}";
        }

        if(!empty($CMS->input['city']))
        {
            $city = intval($CMS->input['city']);
            // $clause .= " AND product_attribute LIKE \"%{$city}%\" ";
            $clause .= " AND JSON_EXTRACT(product_attribute, \"$.city\") = \"{$city}\" ";
            $modLink .= "/city_{$CMS->input['city']}";
        }

        if(!empty($CMS->input['typetour']))
        {
            $typetour = intval($CMS->input['typetour']);
            $clause .= " AND product_tra_type='{$typetour}' ";
            $modLink .= "/typetour_{$CMS->input['typetour']}";
        }

        if(!empty($CMS->input['datefrom']))
        {
            $datefrom =$CMS->class->date->date2time(urldecode($CMS->input['datefrom']));
            $datefrom_link = str_replace("/", "-", $CMS->input['datefrom']);
            // $clause .= " AND JSON_EXTRACT(product_attribute, \"$.tra_time_start\") >= {$datefrom} ";
            $modLink .= "/datefrom_{$datefrom_link}";
        }

        if(!empty($CMS->input['dateto']))
        {
            $dateto =$CMS->class->date->date2time(urldecode($CMS->input['dateto']));
            $dateto_link = str_replace("/", "-", $CMS->input['dateto']);
            // $clause .= " AND JSON_EXTRACT(product_attribute, \"$.tra_time_end\") <= {$dateto} ";
            $modLink .= "/dateto_{$dateto_link}";
        }

        // filter number day
        if(!empty($CMS->input['numday']))
        {
            $number =intval($CMS->input['numday']);
            $clause .= " AND product_attribute LIKE '%tra_number_day\":\"{$number}\"%' ";
            $modLink .= "/numday_{$number}";
        }

        $results = page::init("SELECT product_id AS id, product_name AS name, product_description AS description, product_image AS image, product_image_alt AS image_alt, product_price_old AS price_old, product_price_sell AS price_sell, product_shorturl AS url, product_option AS `option`, product_attribute AS attribute, product_up AS up, product_time AS `time`, product_group_id AS group_id, product_group_name AS group_name, pg_shorturl AS group_url, U.user_display_name, product_name_lang, product_shorturl_lang, product_description_lang, product_information_1_lang, product_information_2_lang, product_manufacture, product_real_type as real_type, product_up, product_attribute_custom AS attribute_custom, product_series 
                    FROM ".root_table."product AS P LEFT JOIN ".root_table."product_group AS PG ON P.product_group=PG.product_group_id LEFT JOIN ".root_table."user As U ON P.user_id = U.user_id 
                    WHERE product_deleted=0 AND product_show=1 {$clause} ORDER BY product_id DESC", $limit, $paging, 'web.product.product_group.user'); // AND JSON_VALID(product_attribute)=1


        // Declare output
        $output = [];

        // Load data
        if($results)
        {
            foreach ( $results as $data )
            {
                $output[] = self::convertProduct($data);
            }
        }


        // Output
        return array($output, $modLink);

    }

    // ====================================
    // UPDATE GET LIST GROUP FULL LEVEL
    // THAMLV: Y2017M10D30
    // ====================================
    /**
     * Get List Product Group
     * @return array
     */
    static public function getListGroupFull( $record_id = 0 )
    {
        global $DB;

        $record_id = $record_id ? self::getParentId($record_id) : 0;
        $sqlString = "
        SELECT product_group_id AS id, product_group_code AS code, product_group_name AS name, product_group_description as description, product_group_parent as parent, pg_shorturl AS url,product_group_avatar AS image 
        FROM ".root_table."product_group 
        WHERE product_group_type = 0 AND product_group_status = 1 AND product_group_deleted = 0 AND product_group_parent = '{$record_id}' 
        ORDER BY product_group_order ASC
        ";

        $results = $DB->fetch_data($sqlString, 'product_group');

        $output = [];

        if($results)
        {
            foreach ( $results as $data )
            {
                $data = self::convertGroup($data);
                $data['listGroup'] = self::getListGroupChild($data['id']);
                // $data['countProduct'] = self::getCountProductOfGroup($data['id']);
                $output[$data['code']] = $data;
            }
        }

        return $output;
    }

    /**
     * Get Get List Product Group Child Of Product Group
     * @return array
     * Max loop 5
     */
    static public function getListGroupChild( $record_id = 0, $loop = 0 )
    {
        global $DB;

        // Max loop
        $loop = intval($loop) + 1;

        $output = [];

        if( $record_id > 0 AND $loop <= 5 )
        {
            $sqlString = "
            SELECT product_group_id AS id, product_group_code AS code, product_group_name AS name, product_group_description as description, product_group_parent as parent, pg_shorturl AS url,product_group_avatar AS image 
            FROM ".root_table."product_group 
            WHERE product_group_type = 0 AND product_group_status = 1 AND product_group_deleted = 0 AND product_group_parent = '{$record_id}' 
            ORDER BY product_group_order ASC
            ";

            $results = $DB->fetch_data($sqlString, 'product_group');

            $output = [];

            if($results)
            {
                foreach( $results as $data )
                {
                    $data = self::convertGroup($data);
                    $data['listGroup'] = self::getListGroupChild($data['id'], $loop);
                    // $data['countProduct'] = self::getCountProductOfGroup($data['id']);
                    $output[$data['code']] = $data;
                }
            }

        };

        return $output;
    }

    /**
     * Get Parent Id Of Product Group
     * @return number Primary
     * Max loop 5
     */
    static function getParentId( $record_id = 0, $loop = 0 )
    {
        global $CMS, $DB;

        // Max loop
        $loop = intval($loop) + 1;

        $output = $record_id;

        if( $record_id > 0 AND $loop <= 5 )
        {
            $sqlString = "
            SELECT parent_id 
            FROM ".root_table."product_group 
            WHERE product_group_type = 0 AND product_group_status = 1 AND product_group_deleted = 0 AND product_group_parent = '{$record_id}'
            LIMIT 1
            ";

            $sqlQuery = $DB->query($sqlString);

            if( $DB->num_rows($sqlQuery) > 0 )
            {
                $data = $DB->fetch_array($sqlQuery);
                if( $data['parent_id'] > 0 )
                {
                    $output = self::getParentId($data['parent_id'], $loop);
                }
            }
        }

        return $output;
    }

    /**
     * Get Count Product Of Product Group
     * @return number
     */
    static function getCountProductOfGroup( $record_id = 0 )
    {
        global $CMS, $DB;

        $output = 0;

        if( $record_id > 0 )
        {
            // Get list child id
            $child_id = self::getListGroupChildId($record_id);

            // Convert sql
            $record_id = "'{$record_id}'";
            foreach( $child_id as $id )
            {
                if ( $id )
                {
                    $record_id .= ", '{$id}'";
                }
            }
            $record_id = " AND product_group IN ({$record_id})";

            $sqlString = "
            SELECT 0 
            FROM ".root_table."product 
            WHERE product_type = 0 AND product_show = 1 AND product_deleted = 0 {$record_id} 
            ";

            $sqlQuery = $DB->query($sqlString);

            $output = $DB->num_rows($sqlQuery);
        }

        return $output;
    }

    /**
     * Get List Group Id
     * @return array
     */
    static public function getListGroupChildId( $record_id = 0, $loop = 0, $output = [] )
    {
        global $DB, $CMS;

        // Load Data
        $output = $output ? $output : [];

        // Max loop
        $loop = intval($loop) + 1;

        if( $record_id > 0 AND $loop <= 5 )
        {
            $sqlString = "
            SELECT product_group_id AS id  
            FROM ".root_table."product_group 
            WHERE product_group_type = 0 AND product_group_status = 1 AND product_group_deleted = 0 AND product_group_parent = '{$record_id}'
            ";

            $sqlQuery = $DB->query($sqlString);

            while ( $data = $DB->fetch_array($sqlQuery) )
            {
                $output[$data['id']] = $data['id'];
                $output = self::getListGroupChildId($data['id'], $loop, $output);
            }
        }

        return $output;
    }
    // ====================================
    // END UPDATE GET LIST GROUP FULL LEVEL
    // ====================================

    static function getListAttributeProduct($str_attr="")
    {
        global $CMS, $DB;

        $listAttr = json_decode($str_attr, 1);
        $output = [];

        if(is_array($listAttr))
        {
            foreach ($listAttr as $attr_id => $options_id) 
            {
                // Get information attribute
                $data_attribute = attribute::getInfo($attr_id);
                // Get infomation attribute options
                $attr_option = attribute::getInfoOption($options_id);
                $key = $data_attribute['attr_key'];
                $output[$key]['value'] = $attr_option['options_value'];
                $output[$key]['name'] = $attr_option['options_name'];
                $output[$key]['unit'] = $data_attribute['attr_unit'];
                $output[$key]['order'] = $data_attribute['attr_order'];
                $output[$key]['required'] = $data_attribute['attr_required'];
                $output[$key]['search'] = $data_attribute['attr_search'];
                $output[$key]['id'] = $options_id;
                $output[$key]['attr_id'] = $attr_id;
            }
        }

        return $output;
    }


    static public function getListChildProduct($product_id_except = 0, $parent_id=0, $limit = 5, $paging = false)
    {
        global $CMS, $DB, $tpl;

        // Không lấy id hiện tại
        self::$sqlAdd .= $product_id_except ? " AND product_series = '{$product_id_except}' " : "";
        // Lấy sản phẩm cùng parent_id
        self::$sqlAdd .= $parent_id ? " AND parent_id='{$parent_id}' " : "";

        // Sắp xếp 
        $order_by = self::$orderBy ? self::$orderBy.", " : "";

        // SQL QUERY
        $sql_query = "SELECT product_id AS id, product_name AS name, product_description AS description, product_image AS image, product_image_alt AS image_alt, product_price_old AS price_old, product_price_sell AS price_sell, product_shorturl AS url, product_option AS `option`, product_attribute AS attribute, product_up AS up, product_time AS `time`, product_group_id AS group_id, product_group_name AS group_name, pg_shorturl AS group_url, U.user_display_name, product_name_lang, product_shorturl_lang, product_description_lang, product_information_1_lang, product_information_2_lang, product_manufacture, product_real_type as real_type, product_attribute_custom AS attribute_custom, product_series 
                    FROM ".root_table."product AS P LEFT JOIN ".root_table."product_group AS PG ON P.product_group=PG.product_group_id LEFT JOIN ".root_table."manufacture As M ON P.product_manufacture=M.manufacture_id LEFT JOIN ".root_table."user As U ON P.user_id=U.user_id 
                    WHERE product_deleted=0 AND product_type=0 AND product_show='1' ".self::$sqlAdd."  ORDER BY {$order_by} product_order ASC, product_time DESC";

// p($sql_query);exit;
        // SQL Select product & product group & paging
        $results = page::init($sql_query, $limit, $paging,'web.product.product_group.user');
// p($results);exit;
        // Declare output
        $output = [];
        $arr_attribute = [];
        $key_list = self::$arrkey_color;
        // Load data
        if($results)
        {
            $arr_temp = [];
            foreach ( $results as $data )
            {
                $data = self::convertProduct($data);
                // p($data);exit;
                // Push into array color, size
                foreach ($data['attribute_custom'] as $key => $attribute) 
                {
                    if(in_array($key, $key_list))
                    {
                        $options_id = $attribute['id'];
                        if(!in_array($options_id, $arr_temp) and $attribute )
                        {
                            $arr_attribute[$key][] = $attribute;
                            $arr_temp[] = $options_id;
                        }
                    }
                }
                
                $output[] = $data;

            }
        }

        // p($arr_attribute);exit;
        // Output
        return array($output, $arr_attribute);
    }

    static function findProductByAttribute($product_series="", $pcurrent_id=0, $str_attribute="")
    {
        global $CMS, $DB;

        $sql_add = "";
        $output = [];
        // create sql
        if($str_attribute)
        {
            $list_attr = explode(",", $str_attribute);
            foreach ($list_attr as $options_id) {
                $sql_add .= " AND product_attribute_custom LIKE '%:\"{$options_id}\"%' ";
            }
        }
        // remove "OR" dư thừa
        // $sql_add = ltrim($sql_add, " OR ");
// AND product_id !='{$pcurrent_id}'
        $sql_query = "SELECT product_id AS id, product_name AS name, product_description AS description, product_image AS image, product_image_alt AS image_alt, product_price_old AS price_old, product_price_sell AS price_sell, product_shorturl AS url, product_option AS `option`, product_attribute AS attribute, product_up AS up, product_time AS `time`, product_group_id AS group_id, product_group_name AS group_name, pg_shorturl AS group_url, U.user_display_name, product_name_lang, product_shorturl_lang, product_description_lang, product_information_1_lang, product_information_2_lang, product_manufacture AS manufacture, product_real_type as real_type, product_attribute_custom AS attribute_custom, product_series, product_gallery AS gallery, product_sku 
                    FROM ".root_table."product AS P LEFT JOIN ".root_table."product_group AS PG ON P.product_group=PG.product_group_id LEFT JOIN ".root_table."manufacture As M ON P.product_manufacture=M.manufacture_id LEFT JOIN ".root_table."user As U ON P.user_id=U.user_id 
                    WHERE product_deleted=0 AND parent_id !=0 AND product_type=0 AND product_show='1' {$sql_add}  ORDER BY product_order ASC, product_time DESC";

        if($product_series)
        {
            $results = $DB->fetch_data($sql_query, "product")[0];
            $output = self::convertProduct($results);
        }

        return $output;

    }


    static function convertStringAttribute($str = "")
    {
        global $CMS, $DB;

        $list_arr = explode(",", $str);
        $output = [];
        if(is_array($list_arr))
        {
            foreach ($list_arr as $options_id) 
            {
                if($options_id)
                {
                    $attr_id = self::getInfoOpAttr($options_id, "attr_id");
                    $output[$attr_id][] = $options_id;
                }
            }
        }

        return $output;
    }


    static function getInfoOpAttr($record_id=0, $field_return="*")
    {
        global $DB, $CMS;

        if($record_id and $field_return)
        {
            // Count field
            $countField = count(explode(",", $field_return));
            //Query
            $data = $DB->fetch_data("SELECT {$field_return} FROM ".root_table."attribute_options WHERE options_deleted=0 AND options_id='{$record_id}' LIMIT 1",'options')[0];

            //Check return
            if($countField == 1 and $field_return != "*")
            {
                return $data[$field_return];
            }else
            {
                return $data;
            }
        }else
        {
            return false;
        }
    }
}