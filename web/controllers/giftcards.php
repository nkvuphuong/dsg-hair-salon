<?php

namespace controller;

use core\ezy;
use models\service;

// Models
ezy::load_model("service");

class giftcards
{
    static public function auto_run()
    {
    	global $tpl, $CMS;
        
    	// Use for paging
        $tpl->modLink = "/giftcards".ezy::$subact;

    	$tpl->dataListGiftcards = \models\giftcards::getListGiftcards(24,true);
        $CMS->class->language->load("payment");
        $tpl->discount_code_info = isset($_SESSION['discount_code']) && $_SESSION['discount_code'] ? '' : 'display:none';
        $tpl->discount_code_input = isset($_SESSION['discount_code']) && $_SESSION['discount_code'] ? 'display:none' : '';
        $tpl->shipping = isset($_SESSION['curr_info']['input']) ? $_SESSION['curr_info']['input'] : [];
    	// Get Services & Service Categories.
    	$tpl->dataListServices = service::getListServiceCategory();

        switch (ezy::$act) 
        {
            case 'barcode':
                self::getBarcode();
                break;
            
            default:
                break;
        }

        echo ezy::html();

    }

    static private function getBarcode()
    {
        global $CMS, $tpl;
        
        $gitem_code = ezy::$subact;
        $tpl->data = \models\giftcards::getBarcode($gitem_code);
        
        echo ezy::html("barcode");
        exit;
    }
}