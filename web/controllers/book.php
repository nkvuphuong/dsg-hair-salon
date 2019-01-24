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
use models\payment;
use models\service;
use models\store;
use models\product;
use lib\language;

ezy::load_model("gallery");
ezy::load_model("service");
ezy::load_model("store");
ezy::load_model("product");
ezy::load_model("payment");
class book
{
    static public function auto_run() 
    {
        global $tpl,$CMS;

        //Load lang global
        language::$default = $CMS->vars['default_language'];
        $CMS->class->language->load("book");

        // Check bat/tat booking
        if(!$CMS->vars['booking_enable']) { header("location: /"); exit;}

        // Init gallery
        $tpl->dataListGallery = gallery::getListGallery(1, 8);

        //Get all stories
        $tpl->dataStories = store::getAll();
        $tpl->storeCnt = count($tpl->dataStories);

        // Switch
        switch ( ezy::$act )
        {
            case "change_time":
                self::change_time();
                break;
            case "add":
                self::add();
            break;
            case "add_dsg":
                self::add_dsg();
            break;
            case "add_travel":
                self::add_travel();
                break;
            case "saveform":
                self::saveform();
                break;
            default:
                self::main();
            break;
        }
    }

    static function main()
    {
        global $tpl, $CMS;

        if($CMS->vars['is_login'] == 0)
        {
        	$_SESSION['referer']  = $_SESSION['link_back'] = "{$CMS->vars['root_domain']}/book/";
        }

        // Check sms enable
        // booking_email_form_enable = 0 use form sms
        // booking_email_form_enable = 1 use form email
        if(!$CMS->vars['booking_email_form_enable'] and !$CMS->vars['sms_enabled'])
        {
            \models\book::checkEnableSms();
        }

        $tpl->store_id_input =   isset( ezy::$input['store_id']) ?  ezy::$input['store_id'] : 0;                       
        $tpl->dataServiceAndCategory = service::getListServiceCategory();

        $tpl->hoursMorning = intval($CMS->vars['booking_open_hours']) == 0 ? json_decode($CMS->vars['booking_hours_morning']) : \models\book::getopenhours("morning");
        $tpl->hoursAfternoon = intval($CMS->vars['booking_open_hours']) == 0 ? json_decode($CMS->vars['booking_hours_afternoon']) : \models\book::getopenhours("afternoon");//json_decode($CMS->vars['booking_hours_afternoon']);
        $tpl->checkTimeBooking = \models\book::checkTimeBooking();
        $_SESSION['checkservice'] = isset($_SESSION['checkservice']) ? $_SESSION['checkservice'] : null;
        if(isset(ezy::$input['service']) and ezy::$input['service'] != $_SESSION['checkservice'])
        {
            // Unset session when exist input['service']
            unset($_SESSION['form_bk']);
        }

        // btn booking
        $tpl->btn_booking = intval($CMS->vars['booking_hours_enable']) == 0 ? "<button href='#{$tpl->booking_login}' valhours='' type='button' class='btn btn-search {$tpl->booking_login}'>Booking</button>" : "<button class='btn btn-search btn_action title' type='button'>Search</button>";
        // print $tpl->btn_booking;exit;
        $tpl->sessFormBk = isset($_SESSION['form_bk']) ? $_SESSION['form_bk'] : \models\book::saveForm();
        // For travel
        if(isset(ezy::$input['pid']) && ezy::$input['pid'])
        {
            $dataProduct = \models\product::getInfo(ezy::$input['pid'], "product_id AS id,product_name_lang,product_name AS name");
            $tpl->travel = \models\product::convertProduct($dataProduct);
        }

        if(ezy::$theme_key == "dsg")
        {
           
           $tpl->optionCity = \models\payment::getOptionCity();
           if($tpl->store_id_input != 0)
           {
                $store_info = \models\store::getInfo($tpl->store_id_input);
                $tpl->defaultcity = $store_info['city_id'];
                
           }else
           {
                 $tpl->defaultcity = 4167;//BinhDuong
           }
            //Get all stories
            $tpl->dataStories = store::getAll_by_City($tpl->defaultcity);
            $tpl->storeCnt = count($tpl->dataStories);

            // Load list staff
            $tpl->listStaff = \models\store::get_staff_dsg();
          
        }
        

        // variable
        $tpl->ser_id = isset(ezy::$input['service']) && ezy::$input['service'] ? ezy::$input['service'] : "";

        echo ezy::html();
    }

    static function add()
    {
        global $CMS;
        // Check sms booking enable
        $booking_form_email = isset($CMS->input['booking_form_email']) ? intval($CMS->input['booking_form_email']) : 0;
    
        if(!$CMS->vars['sms_enabled'] and $booking_form_email == 0)
        {
           // \models\book::checkEnableSms();
        }
 
        // Add booking
        $check = \models\book::add();
        if(isset($CMS->input['ajax']) && $CMS->input['ajax']=='1'){
            if($check){
                unset($_SESSION['form_bk']);
                $array=array('status'=>'success','msg'=>"Thank you for making Appointment with us. We will confirm your appointment shortly");
            }else{
                
                $array=array('status'=>'error','msg'=>'Thank you for making Appointment with us. Appointment you sent not success');
            }
            echo json_encode($array);
            exit();
        }
        if($check)
        {
            // Unset session
            unset($_SESSION['form_bk']);
        }

        // Redirect
            header("location: {$_SERVER['HTTP_REFERER']}");
            exit;
    }


    static function add_dsg()
    {
        global $CMS;
        // Check sms booking enable
        $booking_form_email = isset($CMS->input['booking_form_email']) ? intval($CMS->input['booking_form_email']) : 0;
    
        if(!$CMS->vars['sms_enabled'] and $booking_form_email == 0)
        {
           // \models\book::checkEnableSms();
        }
 
        // Add booking
        $check = \models\book::add_dsg();
        if(isset($CMS->input['ajax']) && $CMS->input['ajax']=='1'){
            if($check){
                unset($_SESSION['form_bk']);
                $array=array('status'=>'success','msg'=>"Cám ơn Quý khách đã đặt lịch hẹn. Chúng tôi sẽ liên hệ Quý khách sớm!");
            }else{
                
                $array=array('status'=>'error','msg'=>'Cám ơn Quý khách đã đặt lịch hẹn. Có lỗi xảy ra khi đặt lịch hẹn. Vui lòng thử lại!');
            }
            echo json_encode($array);
            exit();
        }
        if($check)
        {
            // Unset session
            unset($_SESSION['form_bk']);
        }

        // Redirect
            header("location: {$_SERVER['HTTP_REFERER']}");
            exit;
    }

    static function saveform()
    {
        global $tpl;
        // Save form
        \models\book::saveForm();
        exit;
    }

    static function change_time()
    {
        global $CMS;
        if(intval($CMS->vars['booking_open_hours']) == 0)
        {
            return false;
        }
        $date = $CMS->input['date'];
        $time = $CMS->class->date->date2time($date);
        $num_date = date("N", $time);
        $time_morning = \models\book::getopenhours("morning", $num_date);
        $time_afternoon = \models\book::getopenhours("afternoon", $num_date);
        print_r(json_encode(array("time_morning" => $time_morning, "time_afternoon" => $time_afternoon)));exit;

    }

    static function add_travel()
    {
        global $CMS;
        // Add booking
        $check = \models\book::add_travel();
        

        // Redirect
            header("location: {$_SERVER['HTTP_REFERER']}");
            exit;
    }

}