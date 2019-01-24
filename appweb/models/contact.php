<?php

namespace models;

use core\ezy;
use lib\input;

class contact
{
    /**
     * Get List contact
     * @return array
     */

    static public function sendContact()
    {
        global $CMS, $DB;

        // Google ReCaptcha Verifier
        if ( \core\ezy::google_recaptcha_verify() == false ) { return self::createMsg($CMS->lang['capcha_err']); }
       
        // Input
        $con_name = trim($CMS->input['contactname']);
        $con_email = trim($CMS->input['contactemail']);
        $con_subject = trim($CMS->input['contactsubject']);
        $con_content = trim($CMS->input['contactcontent']);
        $con_time = time();

        // Check input
        if(!$con_name) { return self::createMsg($CMS->lang['name_err']); }
        if(!$con_email) { return self::createMsg($CMS->lang['email_err']); }
        if(!$CMS->class->input->is_email($con_email)) { return self::createMsg($CMS->lang['email_err']); }
        if(!$con_subject) { return self::createMsg($CMS->lang['subject_err']); }
        if(!$con_content) { return self::createMsg($CMS->lang['content_err']); }

        // End check
        $con_ipaddress = $CMS->class->input->get_client_ip();

        $count = $DB->query("INSERT INTO ".root_table."contact (con_name, con_email, con_subject, con_content, con_time, con_ipaddress) VALUES ('{$con_name}', '{$con_email}', '{$con_subject}', '{$con_content}', '{$con_time}', '{$con_ipaddress}')");

        if($count)
        {
            // Info
            $CMS->email->email_template = "customer_contact";
            $CMS->email->email_to = $CMS->vars['company_email'];
            $CMS->email->email_toname = $CMS->vars['smtp_user_display'];
            // $CMS->email->email_cc = $user['user_email'];

            // Email data
            $CMS->email->data['cus_name'] =  $con_name;
            $CMS->email->data['cus_email'] =  $con_email;
            $CMS->email->data['cus_subject'] =  $con_subject;
            $CMS->email->data['cus_content'] =  $con_content;
            $CMS->email->data['website_name'] =  $CMS->vars['website_title'];


            // Send mail
            $CMS->email->quick_send(0,0);

            return self::createMsg($CMS->lang['contact_success'], "success");
        } else {
            return self::createMsg($CMS->lang['contact_error']);
        }

       
    }

    static function createMsg($message="",$status="error")
    {
        $msg = array("status" => "{$status}",
                "hidden" => "display: block",
                "message" => "{$message}");
        return $msg;
    }

    function send_email($data = "")
    {
        global $CMS, $DB, $member;



    }
}