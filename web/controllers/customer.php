<?php
/**
 * Created by PhpStorm.
 * User: HLoi
 * Date: 4/4/2017
 * Time: 10:42 AM
 */

namespace controller;

use core\ezy;
 
class customer
{
    static public function auto_run()
    {
    	global $tpl;
        
    	switch ( ezy::$act )
        {
            case "detail":
                  self::detail();
            break;
            default:
                self::page_default();
            break;
        }
        
    }
    
    static function page_default()
    {
    	global $tpl, $CMS;   
    	$tpl->listCustomer_tieubieu = \models\customer::getAllCustomer_tieubieu();
    	echo ezy::html();
    }

    static function detail()
    {
        global $tpl, $CMS;   

 
            // Get customr info
            $tpl->data = \models\customer::getInfo(ezy::$subact);
            $tpl->data['pathUpload'] = "customer/".$tpl->data['cus_image'];
            // check exist name
            if(empty($tpl->data['name']) and ezy::checkFile404()) 
            {
               // echo ezy::html("tpl.error_404", "layouts");exit;
            }
            
            // Get product same
          
            // Set breadcrumb
            $tpl->title = $tpl->data['name'];
             // Update SEO
            $tpl->seo['title'] = $tpl->data['cus_full_name'];
            $tpl->seo['keywords'] = $tpl->data['cus_note'];
            $tpl->seo['description'] = $tpl->data['cus_note'];
            $tpl->seo['author'] = $tpl->data['seo_keywords'];


            // for Seo
            $tpl->seo['og_title'] = $tpl->data['cus_full_name'];
            $tpl->seo['og_description'] = $tpl->data['cus_note'];
            $tpl->seo['og_image'] = \lib\input::getThumb( $tpl->data['pathUpload'], 555);
 
            echo ezy::html("detail");
    }

}