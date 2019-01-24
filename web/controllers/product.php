<?php
/**
 * Created by PhpStorm.
 * User: HLoi
 * Date: 4/4/2017
 * Time: 10:42 AM
 */

namespace controller;

use core\ezy;
use models\logos;
use models\payment;

ezy::load_model("payment");

class product
{
    static public function auto_run()
    {
        global $CMS, $tpl;

        // og:type facebook
        $CMS->vars['og_type'] = "product";

        //Banner data
        $tpl->banner['slider_sub_page'] = logos::getDataByKey('slider_sub_page');
        
        switch ( ezy::$act )
        {
            case "search_travel":
                self::search_travel();
                break;
            case "find_travel":
                self::find_travel();
                break;
            case "search_advande":
                self::search_advande();
                break;
            case "search":
                self::search();
                break;
            case "group":
                self::group();
                break;
            case "detail":
                self::detail();
                break;
            case "chi-tiet":
                self::detail();
            break;
             case "add_follow":
                self::add_follow();
                break;
            case "delete_follow":
                self::delete_follow();
                break;
            case "promotion":
            case "khuyen-mai":
                self::promotion();
                break;
            default:
                self::main();
                break;
        }
    }

    /**
     * Default page
     */

    static private function main()
    {
        global $CMS, $tpl;

        // Get List Product of Group
        
        $tpl->dataListProduct = \models\product::getListProduct(0, 0, 0, isset($CMS->vars['product_limit']) ? $CMS->vars['product_limit'] : 12, true);
        
        // get list Parent group
        $tpl->dataListGroupParent = \models\product::getListGroup();
        $tpl->optionCity = payment::getOptionCity();
        $tpl->dataListBrand = \models\product::getBrandAndPrice();
        // Use for paging
        $tpl->modLink = "/product";
        
        echo ezy::html();
    }
    
    /**
     * Load product via group
     */
    
    static private function group()
    {
        global $tpl, $CMS;

        // print "<pre>";print_r(ezy::$subact);print_r(ezy::$input);exit;

        // Get Group Info follow group id or group short_url
        $tpl->dataGroup = \models\product::getGroupInfo(ezy::$subact);// rtrim -shorturl for ezy::$subact
        // Check group name
        if(empty($tpl->dataGroup['name']) and ezy::checkFile404()) 
        {
            echo ezy::html("tpl.error_404", "layouts");exit;
        }
        $tpl->group_id=$tpl->dataGroup['id'];

        // Get Parent Group Info
        $tpl->dataGroup['parentGroup'] = $tpl->dataGroup['parent'] > 0 ? \models\product::getGroupInfo($tpl->dataGroup['parent']) : "";

        // Get List Product of Group
        $tpl->dataListProduct = \models\product::getListProduct($tpl->dataGroup['ListGroupId'], 0, 0, isset($CMS->vars['product_limit']) ? $CMS->vars['product_limit'] : 9, true);
        
        // get list Parent group
        $tpl->dataListGroupParent = \models\product::getListGroup($tpl->dataGroup['id']);
        
        // get list brand and min_price max_price
        $tpl->dataListBrand = \models\product::getBrandAndPrice($tpl->dataGroup['id']);
        
        // Use for paging
        // $tpl->modLink = "/product/group/".(ezy::$subact ? ezy::$subact : "group-".ezy::$input['group']);
        $tpl->modLink = $tpl->dataGroup['url'];

        // Set breadcrumb
        $tpl->title = $tpl->dataGroup['name'];

        echo ezy::html();
    }

    /**
     * Get detail
     */

    static private function detail()
    {
        global $tpl, $CMS;

        // get short-url
        // $shorturl = substr_replace(ezy::$subact ,"",-9); // rtrim -shorturl
        // $shorturl = $CMS->class->filter->clean_value($shorturl);

        // Get product info
        $tpl->data = \models\product::getProduct(ezy::$subact);
        if(isset($CMS->input['ajax']) && $CMS->input['ajax']==1){
            echo json_encode($tpl->data);exit();
        }
        // check exist name
        if(empty($tpl->data['name']) and ezy::checkFile404()) 
        {
            echo ezy::html("tpl.error_404", "layouts");exit;
        }
        
        // Get product same
        $listGroupId = \models\product::getListGroupId($tpl->data['group_id']);
        $tpl->dataListProductSameGroup = \models\product::getListProduct($listGroupId, 0, $tpl->data['id'], (isset($CMS->vars['same_group_product_limit']) ? $CMS->vars['same_group_product_limit'] : 12), false);
        // add list produc review
        if(empty($_SESSION['product_review'][$tpl->data['id']])){
            $data_product=array(
                    'url'=>$tpl->data['url'],
                    'image_alt'=>$tpl->data['image_alt'],
                    'name'=>$tpl->data['name'],
                    'image_M'=>$tpl->data['image_M'],
                    'image_alt'=>$tpl->data['image_alt'],
                    'price_old'=>$tpl->data['price_old'],
                    'price_sell'=>$tpl->data['price_sell'],
                    'pathUpload'=>$tpl->data['pathUpload']
                );
            $_SESSION['product_review'][$tpl->data['id']]=$data_product;
        }
        // Set breadcrumb
        $tpl->title = $tpl->data['name'];
        // lay danh sach san pham con
        $tpl->parent_id = $tpl->data['id'];
        $tpl->data['parent'] = \models\product::getListProduct(0, 0, $tpl->data['id'], 0, false);
        // For realestate
        $tpl->optionCity = payment::getOptionCity();
        // for Seo
        $tpl->seo['og_title'] = $tpl->data['name'];
        $tpl->seo['og_description'] = $tpl->data['description'] ? strip_tags($tpl->data['description']) : strip_tags($tpl->seo['og_description']);
        $tpl->seo['og_image'] = $tpl->data['image_L'];

        self::real_estate_define(); //Fix warning/notice
        self::travel_define(); //Fix warning/notice

        echo ezy::html("detail");
    }

    /**
     * Search
     */

    static private function search()
    {
        global $CMS, $tpl;

        // Page
        ezy::$input['page'] = isset($CMS->input['page']) && $CMS->input['page'] ? $CMS->input['page'] : (isset(ezy::$input['page']) && ezy::$input['page'] ? ezy::$input['page'] : 1);

        // Keyword
        $keyword = isset($CMS->input['keyword']) ? $CMS->input['keyword'] : ezy::$subact;
        $tpl->keyword = $CMS->class->filter->clean_value($keyword);

        // Group
        $tpl->group_id = isset($CMS->input['group_id']) ? $CMS->input['group_id'] : (isset(ezy::$input['group_id']) ? ezy::$input['group_id'] : 0);
        $tpl->group_id = intval($tpl->group_id);
        $listGroupId = $tpl->group_id ? \models\product::getListGroupId($tpl->group_id) : 0;
        $tpl->dataListGroupParent = \models\product::getListGroup($tpl->group_id);

        // get group info
        if($tpl->group_id){
            $tpl->data_group = \models\product::getGroupInfo($tpl->group_id);
            $tpl->data_title = $tpl->data_group['name'];
        }

        // price from-to
        $tpl->price_from = isset($CMS->input['price_from']) ? $CMS->input['price_from'] : ( isset(ezy::$input['price_from'])?ezy::$input['price_from']:'' );
        $tpl->price_from = floatval($tpl->price_from);
        $tpl->price_to = isset($CMS->input['price_to']) ? $CMS->input['price_to'] : ( isset(ezy::$input['price_to'])?ezy::$input['price_to']:'' );
        $tpl->price_to = floatval($tpl->price_to);

        // use for rea theme
        $tpl->is_rea = isset($CMS->input['is_rea']) ? $CMS->input['is_rea'] : (isset(ezy::$input['is_rea']) ? ezy::$input['is_rea'] : null);
        $tpl->price_agreed = isset(ezy::$input['price_agreed']) ? ezy::$input['price_agreed'] : null;
        if( $tpl->is_rea AND ! empty($CMS->input['price']) )
        {
            $price = explode("-", $CMS->input['price']);
            $tpl->price_from = isset($price[0]) ? floatval($price[0]) : 0;
            $tpl->price_to = isset($price[1]) ? floatval($price[1]) : 0;

            $tpl->price_agreed = ( $CMS->input['price'] == 'price_agreed' ) ? 1 : $tpl->price_agreed;
        }
        $tpl->price = ( ! $tpl->price_from AND ! $tpl->price_to) ? ( $tpl->price_agreed ? 'price_agreed' : '') : $tpl->price_from . '-' . $tpl->price_to;
        
        // Manufacture
        $tpl->manufacture = array();
        if ( isset($CMS->input['manufacture']) )
        {
            $tpl->manufacture = is_array($CMS->input['manufacture']) ? $CMS->input['manufacture'] : explode('_', $CMS->input['manufacture']);
        }
        else if ( isset(ezy::$input['manuf_cnt']) && ezy::$input['manuf_cnt'] )
        {
            for ( $i = 0; $i < ezy::$input['manuf_cnt']; $i++ ) 
            {
                if ( ezy::$input['manuf_'.$i] ) 
                {
                    $tpl->manufacture[ezy::$input['manuf_'.$i]] = ezy::$input['manuf_'.$i];
                }
            }
        }
        // Loop set id manufacture
        if ( ! empty($tpl->manufacture) ) 
        {

            $temp_manufacture = array();
            foreach ( $tpl->manufacture as $manuf_key => $manuf_value ) 
            {
               $manuf_value = intval($manuf_value);
               if ( $manuf_value ) 
               {
                    $temp_manufacture[$manuf_key] = $manuf_value;
               }
            }
            $tpl->manufacture = $temp_manufacture;
        }

        //color
        $tpl->color=array();
         if ( isset($CMS->input['color']) )
        {
            $tpl->color = is_array($CMS->input['color']) ? $CMS->input['color'] : explode('_', $CMS->input['color']);
        }
        else 
        {
            $input = \models\product::convertUrlSearch('search','-');
            if (isset($input['color_cnt']) && $input['color_cnt'] ){
                for ( $i = 0; $i < $input['color_cnt']; $i++ ) 
                {
                    if ( $input['color_'.$i] ) 
                    {
                        $tpl->color[] =$input['color_'.$i];
                    }
                }
            }
        }
        // SORT PRICE
        $tpl->sort = isset($CMS->input['sort']) && $CMS->input['sort'] ? $CMS->input['sort'] : ( isset(ezy::$input['sort']) && ezy::$input['sort'] ? ezy::$input['sort'] : "" );

        // Product option
        $tpl->product_option = isset($CMS->input['option']) ? $CMS->input['option'] : ( isset(ezy::$input['option']) && ezy::$input['option'] ? ezy::$input['option'] : null );
        $tpl->product_option = intval($tpl->product_option);

        // Product real type: Loại bất động sản
        $tpl->product_real_type = isset($CMS->input['real_type']) ? $CMS->input['real_type'] : (isset(ezy::$input['real_type']) ? ezy::$input['real_type'] : null);
        $tpl->product_real_type = intval($tpl->product_real_type);

        // Product tra type: Loại tour
        $tpl->product_tra_type = isset($CMS->input['tra_type']) ? $CMS->input['tra_type'] : ( isset(ezy::$input['tra_type']) ? ezy::$input['tra_type'] : null );
        $tpl->product_tra_type = intval($tpl->product_tra_type);
        
        // param
        // Use for paging
        $tpl->modLink = "/product/search";
        
        if($tpl->keyword){
            $tpl->modLink.="/{$tpl->keyword}";
        }
        $tpl->modLink .= $tpl->group_id ? "/group_id-".$tpl->group_id : "";
        $tpl->modLink .= $tpl->price_from ? "/price_from-".$tpl->price_from : "";
        $tpl->modLink .= $tpl->price_to ? "/price_to-".$tpl->price_to : "";
        $tpl->modLink .= $tpl->product_option ? "/option-".$tpl->product_option : "";
        
        // use for paging theme rea
        $tpl->modLink .= $tpl->is_rea ? "/is_rea-".$tpl->is_rea : "";
        $tpl->modLink .= $tpl->price_agreed ? "/price_agreed-".$tpl->price_agreed : "";
        $tpl->modLink .= $tpl->product_real_type ? "/real_type-{$tpl->product_real_type}" : "";

        // use for paging theme tra
        $tpl->modLink .= $tpl->product_tra_type ? "/tra_type-".$tpl->product_tra_type : "";

        // create manufacture modlink
        if ( $tpl->manufacture ) 
        {
            $tpl->modLink .= "/manuf_cnt-".count($tpl->manufacture);
            $i = 0;
            foreach( $tpl->manufacture as $manufactureId ) 
            {
                $tpl->modLink .= "/manuf_".$i."-".$manufactureId;
                $i++;
            }
        }
        // color set modlink
        if ( $tpl->color ) 
        {
            $tpl->modLink .= "/color_cnt-".count($tpl->color);
            $i = 0;
            foreach( $tpl->color as $color ) 
            {
                if($color){
                    $tpl->modLink .= "/color_".$i."-".$color;
                    $i++;
                }
            }
        }
        // create sort price modlink
        $tpl->modLink .= $tpl->sort ? "/sort-".$tpl->sort : "";
        
        // Get List Product of Group
        
        if(isset($CMS->input['promotion']) && $CMS->input['promotion']){
            $tpl->promotion = $CMS->input['promotion'];
        } else if (isset(ezy::$input['promotion']) && ezy::$input['promotion'] )
        {
            $tpl->promotion = ezy::$input['promotion'];
        }

        $tpl->promotion = isset($tpl->promotion) ? $tpl->promotion : null;

        $tpl->modLink .= $tpl->promotion ?'/promotion-1':'';

        $tpl->dataListProduct = \models\product::getListProduct($listGroupId, $tpl->promotion, 0, isset($CMS->vars['product_limit']) ? $CMS->vars['product_limit'] : 12, true, $tpl->product_option, $tpl->product_tra_type, $tpl->product_real_type);
        if(isset($CMS->input['ajax']) && $CMS->input['ajax']==1){
            echo json_encode($tpl->dataListProduct);
            exit();
        }
        // get list brand and min_price max_price
        $tpl->dataListBrand = \models\product::getBrandAndPrice($tpl->group_id,$tpl->promotion);
        
        echo ezy::html();
    }
    
    

    /**
     * list product promotion
     */
    static private function promotion()
    {
        global $CMS, $tpl;
        $tpl->dataListProduct = \models\product::getListProduct(0, 1, 0, 12, false);
        $tpl->promotion=1;
        $tpl->data_title='Khuyến mãi';
        // get list brand and min_price max_price
        $tpl->dataListBrand = \models\product::getBrandAndPrice(0,$tpl->promotion);
        echo ezy::html();
        exit();
    }
    
    /**
     * Add product_follow
     */
    static private function add_follow(){
        global $tpl, $CMS, $member;
        $result=0;
        if($member['cus_id'] && $CMS->input['product_id']){
            // kiem tra san pham da ton tai trong danh sach
            if(\models\product::check_product_follow($member['cus_id'],$CMS->input['product_id'])){
                
                $data=array(
                    'status'=>'error',
                    'msg'=>'Sản phẩm đã tồn tại trong danh sách yêu thích!',
                    );
                 echo json_encode($data);
                exit;
                
            }else{
                
                 $result = \models\product::add_product_follow($member['cus_id'],$CMS->input['product_id']);
            }
        }
        if($result){
            $data=array(
                'status'=>'success',
                'msg'=>'Đã thêm sản phẩm vào danh sách yêu thích!',
            );
        }else{ 
            $data=array(
                'status'=>'error',
                'msg'=>'Bạn cần phải đăng nhập để thực hiện tính năng này.',
            );
        }
        echo json_encode($data);
        exit;
    }
 /**
     * Deleted product_follow
     */
    static private function delete_follow()
    {
        global $CMS, $tpl,$member;
        $result=0;
         if($member['cus_id'] && ezy::$input['productid']){
             if(\models\product::check_product_follow($member['cus_id'],ezy::$input['productid'])){
                 $result = \models\product::delete_product_follow($member['cus_id'],ezy::$input['productid']);
             }else{
                 $result=0;
             }
        }
        if($result){
        $_SESSION['msg'] = 'Xóa sản phẩm yêu thích thành công'; 
        $_SESSION['status']='success';
        
        }else{
            $_SESSION['msg'] = 'Xóa sản phẩm yêu thích không thành công, mời bạn thử lại.'; 
            $_SESSION['status']='error';
        }
        $_SESSION['referer'] = "{$CMS->vars['root_domain']}/login/product-follow";

        // Page transfer
        echo ezy::render("page_transfer", "layouts");
        exit;
    }

    static function search_advande()
    {
        global $CMS, $tpl;

        // Check version mysql
        if(!\models\alive::checkMySQL())
        {
           header("location: /product");
        }

        // Convert link
        $CMS->input = array_merge($CMS->input,\models\product::convertUrlSearch());
        list($tpl->dataListProduct, $tpl->modLink) = \models\product::search_advande(1, true);
        $tpl->optionCity = payment::getOptionCity();
        echo ezy::html("main");
    }

    static function find_travel()
    {
        global $CMS;
        $key_search = $CMS->input['term'];
        $data = \models\product::findTravel($key_search);

        print json_encode($data, JSON_UNESCAPED_UNICODE);exit;
    }

    static function search_travel()
    {
        global $CMS, $tpl;

        // Convert link
        $CMS->input = array_merge($CMS->input,\models\product::convertUrlSearch("search_travel"));
        list($tpl->dataListProduct, $tpl->modLink) = \models\product::search_travel(15, true);
        // $tpl->optionCity = payment::getOptionCity();
        echo ezy::html("main");
    }

    /**
     * nkvp - 2017.11.28
     * Define vars for realestate theme to prevent warning/notice logs
     */
    static function real_estate_define()
    {
        global $CMS, $tpl;

        //Fix notice - warning - nkvp - 2017.11.28
        $tpl->data['attribute']['direction'] = isset($tpl->data['attribute']['direction']) ? $tpl->data['attribute']['direction'] : '';
        $CMS->lang["real_{$tpl->data['attribute']['direction']}"] = isset($CMS->lang["real_{$tpl->data['attribute']['direction']}"]) ? $CMS->lang["real_{$tpl->data['attribute']['direction']}"] : '';
        $tpl->data['attribute']['nbedroom'] = isset($tpl->data['attribute']['nbedroom']) ? $tpl->data['attribute']['nbedroom'] : '';
        $tpl->data['attribute']['nbathrooms'] = isset($tpl->data['attribute']['nbathrooms']) ? $tpl->data['attribute']['nbathrooms'] : '';
        $tpl->data['attribute']['facade'] = isset($tpl->data['attribute']['facade']) ? $tpl->data['attribute']['facade'] : '';
        $tpl->data['attribute']['entrance'] = isset($tpl->data['attribute']['entrance']) ? $tpl->data['attribute']['entrance'] : '';
        $tpl->data['attribute']['dbalcon'] = isset($tpl->data['attribute']['dbalcon']) ? $tpl->data['attribute']['dbalcon'] : '';
        $CMS->lang["real_{$tpl->data['attribute']['dbalcon']}"] = isset($CMS->lang["real_{$tpl->data['attribute']['dbalcon']}"]) ? $CMS->lang["real_{$tpl->data['attribute']['dbalcon']}"] : '';
        $tpl->data['attribute']['nfloors'] = isset($tpl->data['attribute']['nfloors']) ? $tpl->data['attribute']['nfloors'] : '';
        $tpl->data['attribute']['nroom'] = isset($tpl->data['attribute']['nroom']) ? $tpl->data['attribute']['nroom'] : '';
        $tpl->data['attribute']['nbedroom'] = isset($tpl->data['attribute']['nbedroom']) ? $tpl->data['attribute']['nbedroom'] : '';
        $tpl->data['attribute']['nbathrooms'] = isset($tpl->data['attribute']['nbathrooms']) ? $tpl->data['attribute']['nbathrooms'] : '';
        $tpl->data['attribute']['acreage'] = isset($tpl->data['attribute']['acreage']) ? $tpl->data['attribute']['acreage'] : '';
        $tpl->data['attribute']['p_price_show'] = isset($tpl->data['attribute']['p_price_show']) ? $tpl->data['attribute']['p_price_show'] : '';
        //End - Fix notice - warning
    }

    static function travel_define()
    {
        global $CMS, $tpl;

        $tpl->data['attribute']['tra_summary_travel'] = isset($tpl->data['attribute']['tra_summary_travel']) ? $tpl->data['attribute']['tra_summary_travel'] : '';
        $tpl->data['attribute']['tra_number_day'] = isset($tpl->data['attribute']['tra_number_day']) ? $tpl->data['attribute']['tra_number_day'] : '';
        $tpl->data['attribute']['tra_number_night'] = isset($tpl->data['attribute']['tra_number_night']) ? $tpl->data['attribute']['tra_number_night'] : '';
        $tpl->data['attribute']['tra_time_start'] = isset($tpl->data['attribute']['tra_time_start']) ? $tpl->data['attribute']['tra_time_start'] : '';
        $tpl->data['attribute']['tra_time_end'] = isset($tpl->data['attribute']['tra_time_end']) ? $tpl->data['attribute']['tra_time_end'] : '';
        $tpl->data['attribute']['tra_vehicle_start'] = isset($tpl->data['attribute']['tra_vehicle_start']) ? $tpl->data['attribute']['tra_vehicle_start'] : '';
        $tpl->data['attribute']['tra_vehicle_end'] = isset($tpl->data['attribute']['tra_vehicle_end']) ? $tpl->data['attribute']['tra_vehicle_end'] : '';
        $tpl->data['attribute']['address'] = isset($tpl->data['attribute']['address']) ? $tpl->data['attribute']['address'] : '';
    }
}
