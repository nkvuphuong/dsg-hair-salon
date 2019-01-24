<?php

namespace models;

use core\ezy;
use lib\date;
use lib\image;
use lib\input;
use lib\language;
use \models\news;
use \models\cart;
use \models\store;
use models\service;
use \models\coupons;
use \models\payment;
ezy::load_model("news");
ezy::load_model("logos");
ezy::load_model("seo");
ezy::load_model("gallery");
ezy::load_model("product");
ezy::load_model("service");
ezy::load_model("cart");
ezy::load_model("store");
ezy::load_model("book");
ezy::load_model("coupons");
ezy::load_model("customer");
ezy::load_model("payment");
class app
{
    static private $sql_select = "pages_name AS name, pages_key AS code, pages_content AS content, pages_shorturl AS shorturl, meta_title AS title, meta_keywords AS keywords, meta_description AS description";

    /**
     * Auto run
     */

    static public function auto_run()
    {
        global $CMS, $tpl;
        if($_SESSION['member']['cus_id']){
            $tpl->cus_id = $_SESSION['member']['cus_id'];
        }
        // Set default language
        if(isset($CMS->input['lang']))
        {
            $_SESSION['current_lang'] = $CMS->input['lang'];
            header("location: /");
        }

        //Fix warning $CMS->vars[] -  nkvp - 2017.11.22
        $CMS->vars["company"] = isset($CMS->vars["company"]) ? $CMS->vars["company"] : '';
        $tpl->title = '';
        $tpl->modLink = isset($tpl->modLink) ? $tpl->modLink : '';
        \core\ezy::$page['data'] = !empty(\core\ezy::$page['data']) && is_array(\core\ezy::$page['data']) ? \core\ezy::$page['data'] : [];
        $tpl->sort = isset($tpl->sort) ? $tpl->sort : null;
        $tpl->price = isset($tpl->price) ? $tpl->price : 0;
        $tpl->optionCity = !empty($tpl->optionCity) ? $tpl->optionCity : [];
        $tpl->checkTimeBooking = isset($tpl->checkTimeBooking) ? $tpl->checkTimeBooking : 0;
        //End - Fix warning $CMS->vars[]

        $CMS->vars['default_language'] = isset($_SESSION['current_lang']) ? $_SESSION['current_lang'] : $CMS->vars['default_language'];
        
        // Number pagination
        $CMS->vars['pagination_number'] = isset($CMS->vars['pagination_number']) ? intval($CMS->vars['pagination_number']) : 1000;
        // enable booking
        $CMS->vars['booking_enable'] = isset($CMS->vars['booking_enable']) ? intval($CMS->vars['booking_enable']) : 1;

        // enable booking by hours
        $CMS->vars['booking_hours_enable'] = isset($CMS->vars['booking_hours_enable']) ? intval($CMS->vars['booking_hours_enable']) : 1;

        // enable booking by open hours
        $CMS->vars['booking_open_hours'] = isset($CMS->vars['booking_open_hours']) ? intval($CMS->vars['booking_open_hours']) : 0;

        // enable booking use form send email
        $CMS->vars['booking_email_form_enable'] = isset($CMS->vars['booking_email_form_enable']) ? intval($CMS->vars['booking_email_form_enable']) : 0;
        // Set booking before day
        $CMS->vars['booking_before_day'] = isset($CMS->vars['booking_before_day']) ? intval($CMS->vars['booking_before_day']) : 0;
        // default country
        $CMS->vars['default_country_id'] = !empty($CMS->vars['default_country']) ? $CMS->country->idCountry($CMS->vars['default_country']) :(defined('is_web_vn') == true ? 238 : 231); // 231: id US
        // step time
        $CMS->vars['step_time_booking'] = isset($CMS->vars['step_time_booking']) ? $CMS->vars['step_time_booking'] : 30;
        
        // Check time
        $CMS->vars['booking_hours_morning'] = intval($CMS->vars['booking_open_hours']) == 0 ? $CMS->vars['booking_hours_morning'] : json_encode(\models\book::getopenhours("morning"));
        $CMS->vars['booking_hours_afternoon'] = intval($CMS->vars['booking_open_hours']) == 0 ? $CMS->vars['booking_hours_afternoon'] : json_encode(\models\book::getopenhours("afternoon")); 
        
        // og:type facebook
        $CMS->vars['og_type'] = isset($CMS->vars['og_type']) ? $CMS->vars['og_type'] : "website";

        //Load lang global
        language::$default = $CMS->vars['default_language'];
        $CMS->class->language->load("application");

        // Load openHours
        list($tpl->openHours, $tpl->openHoursShort) = self::getOpenhours();

        // Time booking
        $tpl->bookingHoursMorning = json_decode($CMS->vars['booking_hours_morning']);
        $tpl->bookingHoursAfternoon = json_decode($CMS->vars['booking_hours_afternoon']);

        // Get logos
        // $tpl->banner = logos::getData();
        $tpl->banner['global_popup'] = logos::getDataByKey('global_popup');

        // Load SEO info
        $seo = seo::loadSEO();

        if($seo)
        {
            if(is_file("{$CMS->vars['upload_dir']}/{$seo['seo_og_image']}"))
            {
                $seo['seo_og_image'] = "{$CMS->vars['upload_url']}/{$seo['seo_og_image']}";
            }
            else
            {
                $seo['seo_og_image'] = '';
            }
        }

        // If there's no SEO info, try to load default
        $tpl->seo = array(
            "title" => !empty($seo['seo_title']) ? $seo['seo_title'] : $CMS->vars['website_title'],
            "description" => !empty($seo['seo_description']) ? $seo['seo_description'] : $CMS->vars['seo_description'],
            "keywords" => !empty($seo['seo_keywords']) ? $seo['seo_keywords'] : $CMS->vars['seo_keyword'],
            "author" => !empty($seo['seo_author']) ? $seo['seo_author'] : $CMS->vars['seo_author'],

            "og_title" => !empty($seo['seo_og_title']) ? $seo['seo_og_title'] : $CMS->vars['og_title'],
            "og_image" => !empty($seo['seo_og_image']) ? $seo['seo_og_image'] : (!empty($CMS->vars['og_image']) ? "{$CMS->vars['upload_url']}/attach/{$CMS->vars['og_image']}" : ''),
            "og_description" => !empty($seo['seo_og_description']) ? $seo['seo_og_description'] : $CMS->vars['og_description'],

            "dc_title" => !empty($seo['seo_dc_title']) ? $seo['seo_dc_title'] : $CMS->vars['dc_title'],
            "dc_subject" => !empty($seo['seo_dc_subject']) ? $seo['seo_dc_subject'] : $CMS->vars['dc_subject'],
            "dc_description" => !empty($seo['seo_dc_description']) ? $seo['seo_dc_description'] : $CMS->vars['dc_description'],

            "h1_content" => !empty($seo['seo_h1_content']) ? $seo['seo_h1_content'] : $CMS->vars['h1_content'],
        );


        $tpl->favIcon = is_file("{$CMS->vars['upload_dir']}/attach/{$CMS->vars['favicon']}") ? "{$CMS->vars['upload_url']}/attach/{$CMS->vars['favicon']}" : (is_file(root_path."themes/{$CMS->vars['theme']}/assets/img/favicon.ico") ? "img/favicon.ico" : "{$CMS->vars['root_domain']}/favicon.ico");

        // Get News Categories
        $tpl->dataNewsCategory = news::getListCategory();

        // get logo website
        // $tpl->logo_website = input::checkImage(image::getThumb("attach/{$CMS->vars['logo_website']}","thumbnail", "w200_", 1, 200), "assets/img/logo.png");
        $CMS->vars['logo_website'] = isset($CMS->vars['logo_website']) ? $CMS->vars['logo_website'] : '';
        $CMS->vars['logo_website_mobile'] = isset($CMS->vars['logo_website_mobile']) ? $CMS->vars['logo_website_mobile'] : '';

        $tpl->logo_website = input::checkImage("attach/{$CMS->vars['logo_website']}", "assets/img/logo.png");
        $tpl->logo_website_mobile = input::checkImage("attach/{$CMS->vars['logo_website_mobile']}", "assets/img/logo.png");
        
        if($_SESSION['is_mobile'] and $CMS->vars['logo_website_mobile'])
        {
            $tpl->logo_website = $tpl->logo_website_mobile;
        }

        // Customert
        if($CMS->vars['is_login'] == 1)
        {
            $tpl->login = ["link" => "/login/logout/", "text" => $CMS->lang['logout']];
            $tpl->booking_login = "open_booking";
        }
        else{
            $tpl->login = ["link" => "/login/", "text" => $CMS->lang['login']];
            $tpl->booking_login = "open_booking";//popup_login Tạm thời không bắt đăng nhập
        }

        // Error message
        $msg = isset($_SESSION['msg']) ? $_SESSION['msg'] : "";
        $errormsg = isset($_SESSION['error_msg']) ? $_SESSION['error_msg'] : "";

        if($errormsg !=  "")
        {
            $tpl->alert = ["status" => "error", "msg" => $errormsg] ;
        }
        else if ($msg !=  "")
        {
            $tpl->alert = ["status" => "ok", "msg" => $msg];
        }

        unset($_SESSION['error_msg']); // Remove error
        unset($_SESSION['msg']); // Remove msg

        $tpl->fb_url = "";
        $tpl->gg_url = "";

 
        // Facebook & Google+ plugins
        if($CMS->vars['is_login'] == 0)
        {
            // Check exist fb_app_id and fb_app_secret
            if ( $CMS->vars['fb_app_id'] AND $CMS->vars['fb_app_secret'] )
            {
                $redirect_uri = urlencode("{$CMS->vars['root_domain']}/login/login_fb/");
                $tpl->fb_url = "https://www.facebook.com/dialog/oauth?client_id={$CMS->vars['fb_app_id']}&redirect_uri={$redirect_uri}&scope=email,public_profile";
            }

            // Check exits gg_app_id and gg_app_secret
            if ( $CMS->vars['gg_app_id'] AND $CMS->vars['gg_app_secret'] )
            {
                $gg_redirect_uri =  "{$CMS->vars['root_domain']}/login/login_gg/";
                $tpl->gg_url = $CMS->api->google->login($gg_redirect_uri);
            }
        }
    
        // Get Lastest News: use for footer block
        $tpl->dataLatestPosts = \models\news::getListNews(0, isset($CMS->vars['latest_news_limit']) ? $CMS->vars['latest_news_limit'] : 4);
        
        // Get session cart to show info all module
        $tpl->mycart = isset($_SESSION['mycart']) ? $_SESSION['mycart'] : [];
        $countCart = 0;
        if($tpl->mycart){
            foreach($tpl->mycart  as $mycart){
                $countCart+=$mycart['quantity'];
            }
        }
        $tpl->countmycart = $countCart;

        // Get msg all module for notify
        $tpl->notify_title = 'Notification';
        $tpl->notify_msg  = isset($tpl->msg['message']) ? "{$tpl->msg['message']}" : '';
        $tpl->notify_msg .= isset($tpl->alert['msg']) ? ($tpl->notify_msg ? "<br />".$tpl->alert['msg'] : $tpl->alert['msg']) : '';
        $tpl->notify_type = !empty($tpl->msg['status']) && in_array($tpl->msg['status'], array('success', 'ok')) == true  ? 'success' : 'error';
        $tpl->notify_icon = $tpl->notify_type == 'success' ? 'fa fa-check-circle' : 'fa fa-exclamation-circle';

        // Get menu from news category
        $tpl->menuCategory = news::getMenuFromNewsCat();

        // Get Lastest Gallery: use for footer block
        $tpl->dataLatestListGallery = gallery::getListGallery(0, isset($CMS->vars['latest_gallery_limit']) ? $CMS->vars['latest_gallery_limit'] : 6);

        // Get List gallery and category 
        $tpl->catAndListgallery = gallery::getListCategory(1, isset($CMS->vars['cat_and_gallery_limit']) ? $CMS->vars['cat_and_gallery_limit'] : 3);
        // print "<pre>"; print_r($tpl->catAndListgallery);exit;

        // Use for theme mer
        // Get List Manufacture
        $tpl->dataListManufacture = product::getListManufacture();
        $tpl->dataListManufacture_home = $tpl->dataListManufacture;
        // Get List Group of product
        $tpl->dataListProductGroup = product::getListGroup();
        
        // Get List Top Product (sản phẩm nổi bật)
        // $tpl->dataListTopProduct = \models\product::getListProduct(0, 0, 0, isset($CMS->vars['hot_product_limit']) ? $CMS->vars['hot_product_limit'] : 10, false, 1);
 
        // get list produc reveiw
        if(isset($_SESSION['product_review'])){
            $tpl->listProductReview =  $_SESSION['product_review'];
        }
        // Get CartTotal
        $tpl->amount = isset($_SESSION['mycart']) ? \models\cart::cartTotal() : "0";

//        if(isset($_SESSION['mycart']))
        {
            list($tpl->cart_ship_fee, $tpl->cart_tax, $tpl->cart_subtotal, $tpl->cart_amount, $tpl->cart_taxOri, $tpl->cart_discount, $tpl->cart_discountOri) = \models\cart::cartTotal(1);
        }

        // Get store
        $tpl->storefronts = store::getAll();
        // move from boad in all module nhathh
        // Get list service: Temporary set to 3 with nail01d, 4 for the others. 
        $tpl->dataServiceCategory = service::getListCategory(isset($CMS->vars['board_service_category_limit']) ? $CMS->vars['board_service_category_limit'] : 4, 1);
        // Get List Latest Product
        // $tpl->dataListLatestProduct = product::getListProduct(0, 0, 0, isset($CMS->vars['latest_product_limit']) ? $CMS->vars['latest_product_limit'] : 12, false);
        
                // Variable for theme DSG
        $tpl->dataListService_dsg = service::getListService(1);

 
        //Banner data move banner all page
        $bannerKeys = ['board_popup','slider','home_1','home_2','home_3','home_4','home_5','home_6','home_7', 'slider_body', 'slider_middle'];

        foreach ($bannerKeys as $bannerKey)
        {
            $tpl->banner[$bannerKey] = logos::getDataByKey($bannerKey);
        }

        // Default slider body
        $tpl->banner['slider_body'] = empty($tpl->banner['slider_body']) ? $tpl->banner['slider'] : $tpl->banner['slider_body'];
        
        // get module active set menu
        $tpl->module=ezy::$site;

        // Get Lastest Coupons: use for footer block
        $tpl->dataLatestCoupons = \models\coupons::getListCoupons(isset($CMS->vars['latest_coupons_limit']) ? $CMS->vars['latest_coupons_limit'] : 4);

        // Get Services & Service Categories, used: booking
        $tpl->dataListServices = service::getListServiceCategory();
        
        // Get List Group of services
        $tpl->dataListServiceGroup = service::getListCategory();

        // Get Hot News use for global
        $tpl->dataHotPost = news::getListNews(1, isset($CMS->vars['hot_news_limit']) ? $CMS->vars['hot_news_limit'] : 4);

        // Get price for init search form
        $tpl->dataListBrand = \models\product::getBrandAndPrice();

        // ThamLV: D6M10Y2017: Use for form search
        // Backup price form - to before set default value.
        // Set price min - max for slider range price.
        // Located here because in product controller then current get follow id group
        // $tpl->price_from_ori = $tpl->price_from ? $tpl->price_from : '';
        // $tpl->price_to_ori = $tpl->price_to ? $tpl->price_to : '';

// $tpl->price_from = isset($tpl->price_from) && $tpl->price_from ? $tpl->price_from : floatval($datas['min_price']);
        // $tpl->price_to = isset($tpl->price_to) && $tpl->price_to ? $tpl->price_to : floatval($datas['max_price']);

        $tpl->price_min = $tpl->price_from_ori = 0;//floatval($tpl->dataListBrand['min_price']);
        $tpl->price_max = $tpl->price_to_ori = intval($tpl->dataListBrand['max_price']) + round((30*intval($tpl->dataListBrand['max_price']))/100,0); //floatval();
        $tpl->price_max = $tpl->price_max > 0 ? $tpl->price_max : 500;
        $tpl->price_step = intval($tpl->price_max/20) > 0 ? intval($tpl->price_max/20) : 50;




        // End ThamLV: D6M10Y2017

        // Get List Group of product full level
        $tpl->dataListProductGroupFull = product::getListGroupFull();

        // Get list service best seller: use for theme dsg
        $tpl->dataServiceBestSeller = service::getListService('', '',isset($CMS->vars['service_best_seller_limit']) ? $CMS->vars['service_best_seller_limit'] : 4, false, 0, 1, 4);

        // Get list service best seller: use for theme dsg
        $tpl->dataServiceHot = service::getListService('', '',isset($CMS->vars['service_hot_limit']) ? $CMS->vars['service_hot_limit'] : 4, false, 0, 1, 1);

        // Check default color website
        $CMS->vars['color_theme'] = isset($CMS->vars['color_theme']) ? $CMS->vars['color_theme'] : "default";
        $color_theme = "color-theme/theme-{$CMS->vars['color_theme']}.css";
        $tpl->css_add = "";

        if($CMS->vars['color_theme'] != "default" and file_exists($_SERVER['DOCUMENT_ROOT'].ezy::$web_assets.$color_theme))
        {
            $tpl->css_add = "<link rel='stylesheet' type='text/css' href='{$color_theme}'>";
        }

        // Link go-checkin
        $tpl->booking_link = $CMS->vars['whmcs_client_id'] ? "https://{$CMS->vars['whmcs_client_id']}.go-checkin.com/appointment/book" : "/";

        //Check if function is callable. Then call it.
        $additionalFunc = "autorun_".\core\ezy::$web_theme; 
        if(is_callable(["self", $additionalFunc]))
        {
            eval("self::{$additionalFunc}();");
        }

        return true;
    }

    //autorun for web122
    static public function autorun_web122()
    {
        global $CMS, $tpl;

        $tpl->test = 'ok';
    }

    //autorun for web122
    static public function autorun_dsg()
    {
        global $CMS, $tpl;
         // Use for form search
        $tpl->optionCity_by_salon= \models\payment::getOptionCity_by_salon();
        // Get List KH tieubieu
        $tpl->dataListKHtieubieu = \models\customer::getCustomer_tieubieu();
        $tpl->dataListStudenttieubieu = \models\customer::getStudent_tieubieu();

        $tpl->dataListTopProduct = \models\product::getListProduct(0, 0, 0, isset($CMS->vars['hot_product_limit']) ? $CMS->vars['hot_product_limit'] : 10, false, 1);

        $cat = $CMS->config_parent_news->get_info_by_key('service_news');
        $tpl->serviceNews = news::getNewsByCatKey($cat['cat_id']);

        if($tpl->serviceNews) {
            foreach ($tpl->serviceNews as $k => $v) {
                $v['data_bk'] = $data_bk = $v;
                $v['upload_path'] = "/news/{$data_bk['news_image']}";
                $v['detail_href'] = "{$CMS->vars['root_domain']}/{$data_bk['news_shorturl']}-n{$data_bk['news_id']}";

                $tpl->serviceNews[$k] = $v;
            }
        }
    }


    /** 
     * Get Openhours
     * @return mixed|null
     */

    static private function getOpenhours(){
        global $CMS;

        //Open hours data
        $CMS->config_general->setDefaultOpenHours();

        $openHours = @json_decode($CMS->vars['open_hours'], true);
        $openHoursShort = [];

        // Use for openHoursShort
        $previousDay = "";
        $previousHour = "";

        // Check data
        if($openHours)
        {
            foreach ($openHours as $dayOpen => $openHour)
            {
                // Check for openHourShort
                $openHourStr = $openHour['checked'] ? $openHour['open'].$openHour['close'] : "close";

                if ( $previousHour == $openHourStr )
                {
                    // Remove duplicate openHourStr
                    unset($openHoursShort[$previousDay]);

                    // Set new day with syntax: Monday-Tuesday
                    // $dayOpen = explode(" - ", $previousDay)[0]." - ".ucfirst($dayOpen);
                    $dayOpen = substr(explode(" - ", $previousDay)[0], 0, 3)." - ".ucfirst(substr($dayOpen, 0, 3));
                }
                // else
                // {
                //     $dayOpen = substr($dayOpen,0,3);
                // }

                // Set time for this Day
                $openHoursShort[$dayOpen] = $openHour;

                // Save current Day
                $previousDay = $dayOpen;
                $previousHour = $openHourStr;
            }

            return [$openHours,$openHoursShort];
        }
        else
        {
            return [null,null];
        }
    }

    /**
     * Get common HTML
     */

    static public function getHtml()
    {
        global $DB;

        // Load Html
        // Default load pos_id = 2 (partial html)
        $sql = "SELECT ".self::$sql_select." FROM ".root_table."pages WHERE pos_id=2 AND pages_deleted=0";

        // Get Data
        $output = [];

        if($results = $DB->fetch_data($sql, 'pages'))
        {
            foreach ( $results as $data )
            {
                $output[$data['code']] = $data['content'];
            }
        }

        // Set Data
        return $output;
    }

    /**
     * Get Page
     * @param string $code
     * @param string $key
     * @return mixed
     */

    static public function getPage( $code = "", $key = "content" )
    {
        global $DB;

        $sql = "SELECT ".self::$sql_select." FROM ".root_table."pages WHERE pages_key='{$code}' AND pages_deleted=0";

        $results = $DB->fetch_data($sql, 'pages');
        $data = isset($results[0]) ? $results[0] : null;

        if($key)
        {
            return isset($data[$key]) ? $data[$key] : null;
        }

        return $data;
    }

    static function countClick()
    {
        global $CMS, $DB;

        $time_cookie = 0;
        if(!$CMS->class->cookie->get_cookie("token_time_bk"))
        {
            $time_cookie = $CMS->class->cookie->set_cookie("token_time_bk", time());
        }else
        {
            $time_cookie = $CMS->class->cookie->get_cookie("token_time_bk");
        }

        $time_new = time();
        $time_check = $time_cookie + 10 * 60; // minute

            
        if($time_new >= $time_check or !$time_cookie)
        {
            // Check variable count click
            $sql = "SELECT conf_value FROM ".root_table."conf_settings WHERE conf_key='number_call_now' LIMIT 1";

            $results = $DB->fetch_data($sql)[0];
            // xoá cache
            $CMS->class->cache->deletesql("config");

            if($results)
            {
                // if exist then update
                $DB->query("UPDATE ".root_table."conf_settings SET conf_value=conf_value+1 WHERE conf_key='number_call_now'");
            }else
            {
                // if exist then insert
                $DB->query("INSERT INTO ".root_table."conf_settings ( conf_key, conf_value, conf_type, conf_group) VALUES ( 'number_call_now', '1', 'input', 1)");
            }

            // gán lại time
            // $_SESSION['token_time_bk'] = time();
            $CMS->class->cookie->set_cookie("token_time_bk", time());
           
        }

         return 1;
    }   

}