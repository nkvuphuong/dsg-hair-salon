<?php
/**
 * Created by PhpStorm.
 * User: HLoi
 * Date: 4/4/2017
 * Time: 10:42 AM
 */

namespace controller;

// Use
use core\ezy;
use models\app;
use models\logos;
use models\news;
use models\gallery;
use models\giftcards;
use models\coupons;
use models\service;
use models\book;
use models\product;
use models\project;
use models\payment;

// Models
ezy::load_model("news");
ezy::load_model("gallery");
ezy::load_model("giftcards");
ezy::load_model("coupons");
ezy::load_model("service");
ezy::load_model("book");
ezy::load_model("product");
ezy::load_model("project");
ezy::load_model("payment");

class board
{
    static public function auto_run()
    {
        global $tpl, $CMS;

        // Get Partial HTML
        $tpl->html = app::getHtml();

        // Get Hot News
        $tpl->dataHotNews = news::getListNews(1, isset($CMS->vars['board_Hot_news_limit']) ? $CMS->vars['board_Hot_news_limit'] : 4);

        // Get Lastest News
        $tpl->dataLatestNews = \models\news::getListNews(0, isset($CMS->vars['board_latest_news_limit']) ? $CMS->vars['board_latest_news_limit'] : 4);

        // Get Hot Gallery
        $tpl->dataListCategory = gallery::getListCategory(1, isset($CMS->vars['board_gallery_category_limit']) ? $CMS->vars['board_gallery_category_limit'] : 0);

        // print "<pre>"; print_r($tpl->dataListCategory);exit;
        // Temporary set to 9 with nail01a, 8 for the others.
        $tpl->dataListGallery = gallery::getListGallery(1, isset($CMS->vars['board_gallery_limit']) ? $CMS->vars['board_gallery_limit'] : 8);

        // News list gallery 
        $tpl->dataGallery = gallery::getListGalleryByCat(isset($CMS->vars['board_gallery_by_category_limit']) ? $CMS->vars['board_gallery_by_category_limit'] : 12);
        
        // Update for theme w3ni10021
        // $groupGallery=array();
        // foreach ($tpl->dataListGallery as $key =>$val){
        //     $groupGallery[$val['cat_url']][]=$val;
        // }

        // $active = 0;
        // foreach ($tpl->dataListCategory as $key=>$data){
        //     $tpl->dataListCategory[$key]['datas']=$groupGallery[$data['url']];
        //     if($groupGallery[$data['url']] && $active==0){
        //        $tpl->dataListCategory[$key]['class_active']='active';
        //        $active=1;
        //     }
        // }
// print "<pre>";print_r($tpl->dataGallery);exit;
        // Get List Gift Cards
        $tpl->dataListGiftcards = giftcards::getListGiftcards();

        // Get List Gift Cards
        $tpl->dataListCoupons = coupons::getListCoupons();


        // set date
        $format_date = $CMS->vars['dateformat_php'][$CMS->vars['date_format']];
        $checkdate = book::checkTimeBooking();
        $tpl->setDate = $checkdate == 1 ? date("{$format_date}") : date("{$format_date}", strtotime("+1day"));

        // Time booking
        $tpl->bookingHoursMorning = json_decode($CMS->vars['booking_hours_morning']);
        $tpl->bookingHoursAfternoon = json_decode($CMS->vars['booking_hours_afternoon']);

        // Use for theme mer
        // Get List Latest Product
        $tpl->dataListLatestProduct = isset($CMS->vars['board_latest_product_limit']) ? product::getListProduct(0, 0, 0, $CMS->vars['board_latest_product_limit'], false) : $tpl->dataListLatestProduct; // Already get in application

        // Get List Promotional Product
        $tpl->dataListPromotionalProduct = product::getListProduct(0, 1, 0, 12, false);

        // Get List Product With Group
        $tpl->dataListProductWithGroup = product::getListProductWithGroup(12);

        // Get List Product Market Trend
        $tpl->dataListProductMarketTrend = product::getListProduct(0, 0, 0, 12, false, 3);

        // Get List Product Best Seller
        $tpl->dataListProductBestSeller = product::getListProduct(0, 0, 0, 12, false, 4);

        // Get List New Product
        $tpl->dataListNewProduct = product::getListProduct(0, 0, 0, (isset($CMS->vars['board_new_product_limit']) ? $CMS->vars['board_new_product_limit'] : 12), false, 5);

        // product hot
        $tpl->dataListProductHot = product::getListProduct(0, 0, 0, (isset($CMS->vars['board_hot_product_limit']) ? $CMS->vars['board_hot_product_limit'] : 12), false, 1);

        // Get List New View More
        $tpl->dataListProductViewMore = product::getListProduct(0, 0, 0, (isset($CMS->vars['board_viewmore_product_limit']) ? $CMS->vars['board_viewmore_product_limit'] : 12), false, 2);

        // Get News By Category
        $tpl->dataNewsWithCategory = news::getListNewsWithCategory(isset($CMS->vars['board_news_with_category_limit']) ? $CMS->vars['board_news_with_category_limit'] : 8);

        // Get List Product Group
        $tpl->dataProductCategory = isset($CMS->vars['board_product_category_limit']) ? product::getListGroup(0, $CMS->vars['board_product_category_limit']) : $tpl->dataListProductGroup; // Already get in application

        // Get List Project Hot
        $tpl->dataListProjectHot = project::getListProject(1, isset($CMS->vars['board_project_hot_limit']) ? $CMS->vars['board_project_hot_limit'] : 4);

        // Get List services
        $tpl->dataService = \models\service::getListService('', '', isset($CMS->vars['board_service_limit']) ? $CMS->vars['board_service_limit'] : 4);
        $tpl->dataService_dsg = \models\service::getListService(1, '', isset($CMS->vars['board_service_limit']) ? $CMS->vars['board_service_limit'] : 4);
        // product country : tour du lịch trong nước
        $tpl->dataListProductCountry = product::getListProduct(0, 0, 0, (isset($CMS->vars['board_country_product_limit']) ? $CMS->vars['board_country_product_limit'] : 12), false, 0, 1);

        // product country : tour du lịch ngoài nước
        $tpl->dataListProductAbroad = product::getListProduct(0, 0, 0, (isset($CMS->vars['board_abroad_product_limit']) ? $CMS->vars['board_abroad_product_limit'] : 12), false, 0, 2);

        // Use for form search
        $tpl->optionCity = payment::getOptionCity();

        // Get List services by category for block in board
        $tpl->dataServiceBlockByCategory = service::getServiceBlockByCategory(isset($CMS->vars['board_service_block_by_category']) ? $CMS->vars['board_service_block_by_category'] : null);

        // Get list food group: use for theme res
        $tpl->dataFoodCategory = service::getListCategory(isset($CMS->vars['board_food_category_limit']) ? $CMS->vars['board_food_category_limit'] : 4, 1, 0);

        // Get list food featured: use for theme res
        $tpl->dataFoodFeatured = service::getListService('', '',isset($CMS->vars['board_food_featured_limit']) ? $CMS->vars['board_food_featured_limit'] : 12, false, 0, 0, 1);

        //Check if function is callable. Then call it.
        $additionalFunc = "board_".\core\ezy::$web_theme;
        if(is_callable([self, $additionalFunc]))
        {
            eval("self::{$additionalFunc}();");
        }

        echo ezy::html();
    }

    /**
     * Load data for theme web
     * nkvp - 2017.12.29
     */
    static public function board_web122()
    {
        global $CMS, $tpl;

        /**
         * Load data for service
         */
        $tpl->banner['slider'] = \models\logos::getDataByKey("slider");

        if(isset(ezy::$input[0]) and is_numeric(ezy::$input[0]))
        {
            $tpl->group = ezy::$input[0];
        }else
        {
            $tpl->group = "";
        }

        if(isset($tpl->dataListServiceGroup)) {
            $tpl->listAllGroupService = $tpl->dataListServiceGroup;
        }else{
            $tpl->listAllGroupService =  \models\service::getListCategory();
        }

        /**
         * End - load data for service
         */

    }

    /**
     * Load data for theme web211
     * nkvp - 2018.01.31
     */
    static public function board_web211()
    {
        global $CMS, $tpl;

        $tpl->dataListGallery = gallery::getListGalleryByAllCats(8);
    }
}