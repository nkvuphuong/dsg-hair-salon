<?php
/**
 * Created by PhpStorm.
 * User: HLoi
 * Date: 4/4/2017
 * Time: 10:42 AM
 */

namespace controller;

use core\ezy;
use models\gallery;
use models\logos;
use models\product;

ezy::load_model("gallery");
ezy::load_model("logos");
ezy::load_model("product");
class service
{
    static public function auto_run()
    {
        global $tpl;
     
        switch ( ezy::$act )
        {
            case "loadgallery":
                self::loadGallery();
                break;
            case "loadstaff":
                self::loadstaff();
                break;
            case "loadservice":
                self::loadService();
                break;
            case "group":
                self::groupService();
                break;
            case "detail":
                self::detail();
                break;
            case "chi-tiet":
                self::chi_tiet();
            break;
            default:
                self::page_default();
                break;
        }
        
    }
    
    static function page_default()
    {
        global $tpl, $CMS;

        // Load slider
        $tpl->banner['slider'] = \models\logos::getDataByKey("slider");
        
        if(isset($CMS->vars['is_restaurant']) and $CMS->vars['is_restaurant'] == 1)
        {
            $tpl->listProductGroup = \models\service::getListCategory(0, 0, 0);
        }else
        {
           $tpl->listProductGroup = \models\service::getListCategory();
        }

        if(isset(ezy::$input[0]) and is_numeric(ezy::$input[0]))
        {
            $tpl->group = ezy::$input[0];
        }else
        {
             $tpl->group = "";
        }

        $tpl->listGallery = gallery::getListGallery(0,4);
        if(ezy::$theme_key == "dsg")
        {
            $tpl->listService = \models\service::getListService('', 1, isset($CMS->vars['service_limit']) ? $CMS->vars['service_limit'] : '', true);
             
        }
        else
        {
            $tpl->listService = \models\service::getListService('', '', isset($CMS->vars['service_limit']) ? $CMS->vars['service_limit'] : '', true);
        }

        // Get list service and paging
        
        if(isset($tpl->dataListServiceGroup)) {
            $tpl->listAllGroupService = $tpl->dataListServiceGroup;
        }else{
            $tpl->listAllGroupService =  \models\service::getListCategory();
        }
        // Use for paging
        $tpl->modLink = "/service";
        
        echo ezy::html();
    }

    /**
     * Load service Ajax
     */

    static function loadService()
    {
        global $CMS, $tpl;
        
        if ( isset($CMS->input['page']) && $CMS->input['page'] > 0 ) {
            ezy::$input['page'] = $CMS->input['page'];
        }
        $pg_id = isset($CMS->input['pg_id']) ? intval($CMS->input['pg_id']) : 0;
        $limit = isset($CMS->input['limit']) ? intval($CMS->input['limit']) : 0;
        $paging = isset($CMS->input['paging']) && intval($CMS->input['paging']) ? true : false;
        $product_type = isset($CMS->input['product_type']) ? intval($CMS->input['product_type']) : 1;
        $resizeWidth = isset($CMS->input['resize_width']) ? intval($CMS->input['resize_width']) : 0;
        
        $listService = \models\service::getListService($pg_id, "", $limit, $paging, $resizeWidth, $product_type);

        // If paging
        if ( $paging == true )
        {
            // get description product group
            $group_des = \models\product::getInfoPGroup($pg_id, "product_group_description");
            $group_des = html_entity_decode($group_des, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $tpl->pg_id = $pg_id;
            $listService = array(
                "data" => $listService,
                "paging_ajax" => \core\ezy::render('paging_ajax', 'layouts'),
                "group_des" => $group_des
                );
        }

        // Output json
        print json_encode($listService);
        exit;
    }
    static function groupService(){
        global $CMS;
        global $CMS, $tpl;

        $tpl->group_id = ezy::$input['id'] ? ezy::$input['id'] : ezy::$subact;

        if(isset($CMS->vars['is_restaurant']) and $CMS->vars['is_restaurant'] == 1)
        {
            $tpl->listProductGroup = \models\service::getListCategory(0, 0, 0);
        }else
        {
           $tpl->listProductGroup = \models\service::getListCategory();
        }

        // Get list service and paging
        $tpl->listService = \models\service::getListService($tpl->group_id, '', isset($CMS->vars['service_limit']) ? $CMS->vars['service_limit'] : '', true);

        // Get category info
        $tpl->dataCategory = \models\service::getCategoryInfo($tpl->group_id);
        $tpl->title = $tpl->dataCategory['name'];

        // Use for paging
        $tpl->modLink .= $tpl->dataCategory['url'];
        
        echo ezy::html('list_group_service');
        exit();
    }
    
    static function detail(){
        global $CMS,$tpl;
        $service_id = !empty(ezy::$input['id']) ? ezy::$input['id'] : ezy::$subact;;
        
        $service = \models\service::getInfo($service_id);
//        $service['description'] = html_entity_decode($service['description']);
        $service['product_information_1'] = html_entity_decode($service['product_information_1']);
//        $service['information_2'] = html_entity_decode($service['information_2']);

        // Data for dsg theme
        $service['uploadPath'] = "product/{$service['product_image']}";
        $service['product_gallery'] = json_decode($service['product_gallery'], true);
        $tpl->seo['og_title'] = $service['product_name'];
        $tpl->seo['og_description'] = strip_tags($service['product_description']);
        $tpl->data = $service;
        
        echo ezy::html('detail');
        exit();
    }


    static function chi_tiet(){
        global $CMS,$tpl;
        $service_id = !empty(ezy::$input['id']) ? ezy::$input['id'] : ezy::$subact;;
        $service = \models\service::getInfo($service_id);
//        $service['description'] = html_entity_decode($service['description']);
        $service['product_information_1'] = html_entity_decode($service['product_information_1']);
//        $service['information_2'] = html_entity_decode($service['information_2']);

        // Data for dsg theme
        $service['uploadPath'] = "product/{$service['product_image']}";
        $service['product_gallery'] = json_decode($service['product_gallery'], true);
        
        $tpl->data = $service;
        
        echo ezy::html('detail');
        exit();
    }


        /**
     * Load staff
     */
    static function loadstaff()
    {
        global $CMS;

        $product_id = intval($CMS->input['service_id']);
        $listStaff = \models\service::getListStaff($product_id);
        // Output Json
        print json_encode($listStaff,JSON_UNESCAPED_UNICODE);
        exit;
    }

    static function loadGallery()
    {
        global $CMS;

        $pg_id = intval($CMS->input['id']);
        $cat_id = $CMS->product_group->getInfo($pg_id, "cat_gallery_id");
        $resizeWidth = isset($CMS->input['resize_width']) ? intval($CMS->input['resize_width']) : 0;

        $listGallery = gallery::getListGallery(0,4, false, $cat_id, $resizeWidth, 0);
        print json_encode($listGallery,JSON_UNESCAPED_UNICODE);
        exit;
    }
}