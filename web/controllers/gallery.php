<?php
/**
 * Created by PhpStorm.
 * User: HLoi
 * Date: 4/4/2017
 * Time: 10:42 AM
 */

namespace controller;

use core\ezy;

class gallery
{
    static public function auto_run()
    {
        global $tpl;

         // Switch
        switch ( ezy::$act )
        {
            case  "getlistbycat":
                self::getListByCat();
            break;
            case  "get_stylehair":
                self::get_stylehair();
            break;
            case "list":
                self::listGallery();
                break;
            case "list_dsg":
                self::listGallery_dsg();
            break;
            default:
                self::main();
            break;
        }

        
    }

    static function main()
    {
        global $CMS, $tpl;

        // Get list categories
        $tpl->dataListCategory = \models\gallery::getListCategory();
        
        $tpl->dataAllCategory = \models\gallery::getListCategory(0, 0, 0, 1); // Usefor dsg

        // Paging
        $tpl->modLink = "/gallery";

        // Get list gallery
        $limit = isset($CMS->vars['pagination_number']) ? intval($CMS->vars['pagination_number']) : (isset($CMS->vars['gallery_limit']) ? $CMS->vars['gallery_limit'] : 8);
        $tpl->dataListGallery = \models\gallery::getListGallery(0, $limit, true);

        // Update for theme nail01e
        $groupGallery = array();

        foreach ($tpl->dataListGallery as $key =>$val){
            $groupGallery[$val['cat_url']][]=$val;
        }
        $active = 0;
        foreach ($tpl->dataListCategory as $key=>$data){
            $tpl->dataListCategory[$key]['datas']=!empty($groupGallery[$data['url']])? $groupGallery[$data['url']]:array();
            if(!empty($groupGallery[$data['url']]) && $active==0){
               $tpl->dataListCategory[$key]['class_active']='active';
               $active=1;
            }
        }

        echo ezy::html();

    }

    static function listGallery()
    {
        global $CMS, $tpl;
        
        // Get cat_id
        $cat_id = intval(ezy::$input[0]);
        $tpl->modLink = "/gallery/list/{$cat_id}";
         // Get list categories
        $tpl->dataListCategory = \models\gallery::getListCategory(0,4,$cat_id);

         $limit = isset($CMS->vars['pagination_number']) ? intval($CMS->vars['pagination_number']) : (isset($CMS->vars['gallery_limit']) ? $CMS->vars['gallery_limit'] : 9);
        // Get list gallery
        $tpl->dataListGallery = \models\gallery::getListGallery(0, $limit, true, $cat_id);

        // get Cat Name
        $CMS->vars['gallery_cat_name'] = $tpl->catName = \models\gallery::getInfoCategory($cat_id, "cat_name");

        // Url share
        $tpl->linkShare = "{$CMS->vars['root_domain']}/{$_SERVER['REQUEST_URI']}";
        // \models\gallery::getCounShareSocial();

        $tpl->dataAllCategory = \models\gallery::getListCategory(0, 0, 0, 1); // Usefor dsg

        // echo
        echo ezy::html("list_gallery");
    }


    static function listGallery_dsg()
    {
        global $CMS, $tpl;
     //  print_r (ezy::$input);exit;
        // Get cat_id
        $tpl->cat_id =$cat_id = intval(ezy::$input['style-hair']);
        $tpl->color_hair = $color_hair = intval(ezy::$input['color-hair']);
        $tpl->modLink = "/gallery/list/{$cat_id}";
           // Use for paging
        $tpl->modLink = "/gallery/list_dsg/style-hair-".$tpl->cat_id."/color-hair-".$tpl->color_hair;

        $limit = isset($CMS->vars['pagination_number']) ? intval($CMS->vars['pagination_number']) : (isset($CMS->vars['gallery_limit']) ? $CMS->vars['gallery_limit'] : 9);
        // Get list gallery
        $tpl->dataListGallery = \models\gallery::getListGallery_dsg(0, $limit, true, $cat_id, $color_hair);

        // get Cat Name
        $tpl->cat_cate = \models\gallery::getInfoCategory($cat_id, "cat_cate");

        // Url share
        $tpl->linkShare = "{$CMS->vars['root_domain']}/{$_SERVER['REQUEST_URI']}";
        // \models\gallery::getCounShareSocial();

       //$tpl->dataAllCategory = \models\gallery::getListCategory(0, 0, 0, 1); // Usefor dsg

        // echo
        echo ezy::html("list_gallery");
    }

    static function getListByCat()
    {
        global $CMS, $tpl;

        if ( $CMS->input['page'] > 0 ) {
            ezy::$input['page'] = $CMS->input['page'];
        }
        $cat_id = intval($CMS->input['cat_id']);
        $limit = isset($CMS->input['limit']) ? intval($CMS->input['limit']) : intval($CMS->vars['gallery_limit']);
        $paging = true;

        $listGallery = \models\gallery::getdataGallery($limit, $cat_id, $paging);

        // If paging
        if ( $paging == true )
        {
            $tpl->cat_id = $cat_id;
            $tpl->limit = $limit;
            $tpl->blockId = isset($CMS->input['blockId']) ? trim($CMS->input['blockId']) : '';

            $listGallery = array(
                "data" => $listGallery,
                "paging_ajax" => \core\ezy::render('paging_ajax', 'gallery')
                );
        }

        // Output json
        print json_encode($listGallery);
        exit;
    }


    static function get_stylehair()
    {
        global $CMS, $tpl;

        $id = ezy::$input['id'];

        $tpl->liststyleHair = \models\gallery::get_cate_bygroup($id);

        if(is_array($tpl->liststyleHair) AND count($tpl->liststyleHair) > 0)
        {
            $html = \core\ezy::render("list_style_hair","gallery");
            echo $html;exit;
        }
        else{
            print "";
            exit;
        }
       
    }
}