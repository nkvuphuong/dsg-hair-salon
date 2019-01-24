<?php
/**
 * Created by PhpStorm.
 * User: HLoi
 * Date: 4/4/2017
 * Time: 10:42 AM
 */

namespace controller;

use core\ezy;
use models\product;
use models\store;
use models\payment;
use \views\layouts;

ezy::load_model("store");
ezy::load_model("payment");
class salon
{
    static public function auto_run()
    {
        global $tpl;

        switch ( ezy::$act )
        {
            case "send":
                self::send_contact();
                break;
            case "getstore":
                self::get_store();
                break;
            case "optionstore":
                self::optionstore();
                break;
            default:
                self::store();
//            	self::page_default();
                break;
        }
    }

    /**
     * Send contact
     */

    static private function send_contact()
    {
        global $tpl;

        $msg = \models\contact::sendContact();

        if($msg['status'] == "error")
        {
            $_SESSION['error_msg'] = $msg['message'];
        }
        else
        {
            $_SESSION['msg'] = $msg['message'];
        }

        header("location: /");
        exit;
    }

    /**
     * Default contact form
     */

    static private function page_default()
    {
        global $tpl, $CMS;
        $salon_id = intval(ezy::$input['salon']);
        if(!empty($salon_id))
        {

        }
        else{
            $city_id = 4167;
        }
        $tpl->list_store = $CMS->store->getListStore_by_cityid($city_id);
        $tpl->optionCity = \models\payment::getOptionCity();


        // Build contact form
        echo ezy::html();
    }

    /**
     * Get store
     */

    static private function get_store()
    {
        global $CMS, $tpl;
        $city_id = ezy::$input['id'];
        $tpl->list_store = $CMS->store->getListStore_by_cityid($city_id);
        if(count($tpl->list_store) > 0) {
            $html = \core\ezy::render("list_store","salon");
            echo $html;exit;
        }
        else
        {
            echo "<p>Không tìm thấy Salon phù hợp!<p>";exit;
        }
    }


    /**
     * Get optionstore
     */

    static private function optionstore()
    {
        global $CMS, $tpl;
        $city_id = ezy::$input['id'];
        $tpl->typeshow = intval(ezy::$input['typeshow']);
        $tpl->list_store = $CMS->store->getListStore_by_cityid($city_id, " AND store_display=1 ");
        if(count($tpl->list_store) > 0) {
            $html = \core\ezy::render("option_store","salon");
            echo $html;exit;
        }
        else
        {
            echo "<option value=''>Chọn Salon</option>";exit;
        }
    }

    static private function store()
    {
        global $CMS, $tpl, $DB;
        $storeId = ezy::$subact;

        if(!$storeId) {
            $sql = "SELECT store_id FROM ".root_table."store WHERE store_deleted=0 AND store_display=1 LIMIT 0,1";
            $rs = $DB->fetch_data($sql);
            if($rs && $rs[0]) {
                $storeId = $rs[0]['store_id'];
            } else {
                $storeId = 0;
            }
        }

        $store = store::getInfo($storeId);
        if($store) {
            $tpl->store = $store;
            $tpl->selected_store = $storeId;
        } else {
            $tpl->store = null;
        }


        // Build contact form
        echo ezy::html("store");
    }


}