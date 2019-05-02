<?php
/**
 * Created by PhpStorm.
 * User: HLoi
 * Date: 4/4/2017
 * Time: 10:42 AM
 */

namespace controller;

use core\ezy;
use lib\input;
use models\app;
use models\service;

ezy::load_model("service");

class pages
{
    static public function auto_run()
    {
        global $CMS, $tpl;

        $arr_page = array("make-an-appointment", "price-enquiry", "send-info", "location",'tragop');
        if(ezy::$act == "tragop")
        {
            echo ezy::html("tra_gop");
            exit();
        }

        // page custom for dsg
        $arr_page_dsg = array("educate", "price", "cosmetic" , "bang-gia" , "dao-tao");

        $arr_info = \models\pages::getInfo(ezy::$act);

        $actArr = explode('?', ezy::$act);
        ezy::$act = input::arrayValue($actArr, 0);

        if( in_array(ezy::$act, $arr_page) and isset($CMS->vars['is_cs']) and $CMS->vars['is_cs'] == 1 and !$arr_info)
        {
            $tpl->listHoursMorning = json_decode($CMS->vars['booking_hours_morning']);
            $tpl->listHoursAfternoon = json_decode($CMS->vars['booking_hours_afternoon']);
            $tpl->listService = service::getListService();
            if(ezy::$act == "make-an-appointment")
            {
                echo ezy::html("tpl.make_appointment");
            }else if(ezy::$act == "price-enquiry")
            {
                echo ezy::html("tpl.price_enquiry");
            }else if(ezy::$act == "send-info")
            {
                self::send_info(); // only use for web KH
            }else if(ezy::$act == "location")
            {
                echo ezy::html("tpl.location");
            }

        }
        else if ( $CMS->vars['theme'] == 'dsg' AND in_array(ezy::$act, $arr_page_dsg) ) 
        {
            if(ezy::$act == "bang-gia")
            {
                echo ezy::html("tpl.price");
            }else if(ezy::$act == "dao-tao")
            {
                echo ezy::html("tpl.educate");
            }else
            {
                 echo ezy::html(ezy::$act);
                exit();     
            }  
        }
        else
        {
            $pages_content = json_decode($arr_info['pages_content'], 1);
            $tpl->html = html_entity_decode($pages_content[$CMS->vars['default_language']]);
            $arr_name = json_decode($arr_info['pages_name'], 1);
            $tpl->name = $arr_name[$CMS->vars['default_language']];
            // Check exsit name page
            if(empty($tpl->name) and ezy::checkFile404()) 
            {
                echo ezy::html("tpl.error_404", "layouts");exit;
            }
            // Check dùng layout hay ko
            if($arr_info['use_layouts'] == 1)
            {
                echo ezy::html();exit;
            }else
            {
                print $tpl->html;exit;
            }
        }
       
    }


    static function send_info()
    {
        global $CMS;

        \models\pages::send_info();
        $_SESSION['msg'] = "You send email successful";
        header("location: /p/price-enquiry");
    }
}