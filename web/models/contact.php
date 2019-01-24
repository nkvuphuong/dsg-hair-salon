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

    static public function sendContact($check=1)
    {
        global $CMS, $DB;

        // Google ReCaptcha Verifier
        if ( \core\ezy::google_recaptcha_verify() == false ) { return self::createMsg("Captcha is wrong, please try again"); }

        // Check token
        if(!\lib\security::check_token())
        {
            return self::createMsg("Please refresh page (F5) then try again.");
        }
        
        // Validate by ip
        if(intval($CMS->vars['enable_security_ip']))
        {
            if(!\lib\security::checkSecurityIp())
            {
                return false;
            }
        }


        // Input
        $con_name = isset($CMS->input['contactname']) ? trim($CMS->input['contactname']) : null;
        $con_email = isset($CMS->input['contactemail']) ? trim($CMS->input['contactemail']) : null;
        $con_subject = isset($CMS->input['contactsubject']) ? trim($CMS->input['contactsubject']) : null;
        $con_content = isset($CMS->input['contactcontent']) ? trim($CMS->input['contactcontent']) : null;
        $course_hair = isset($CMS->input['course_hair']) ? trim($CMS->input['course_hair']) : null;


        $con_time = time();

        // Input for register student
        $con_type = isset($CMS->input['contacttype']) ? trim($CMS->input['contacttype']) : null;
        $con_phone = isset($CMS->input['contactphone']) ? trim($CMS->input['contactphone']) : null;
        $con_address = isset($CMS->input['contactaddress']) ? trim($CMS->input['contactaddress']) : null;
        if ( $con_type == '1' AND $CMS->vars['theme'] == 'dsg' )
        {
            $con_subject .= " - {$con_name} - $con_phone";
            $con_content .= "Khóa học: ".$course_hair;
        }

        // Check input
        if(!$con_name && $check==1) { return self::createMsg("Please enter your name!"); }
        if(!$con_email && $check==1) { return self::createMsg("Please enter your email!"); }
        if(!$CMS->class->input->is_email($con_email)) { return self::createMsg("Email is wrong. Please try again!"); }
        if(!$con_subject && $check==1) { return self::createMsg("Please enter a subject!"); }
        if(!$con_content && $check==1) { return self::createMsg("Please enter a content!"); }

        // Check for register student
        if ( $con_type == '1' AND $CMS->vars['theme'] == 'dsg' ) 
        {
            if(!$con_phone) { return self::createMsg("Please enter your phone!"); }
            if(!$con_address) { return self::createMsg("Please enter your address!"); }
        }

        // End check
        $con_ipaddress = $CMS->class->input->get_client_ip();

        $count = $DB->query("INSERT INTO ".root_table."contact (con_name, con_email, con_subject, con_content, con_time, con_ipaddress, con_type, con_phone, con_address) VALUES ('{$con_name}', '{$con_email}', '{$con_subject}', '{$con_content}', '{$con_time}', '{$con_ipaddress}', '{$con_type}', '{$con_phone}', '{$con_address}')");

        if($count)
        {
            // Email
            $str_email = (isset($CMS->vars['company_email_cc']) ? $CMS->vars['company_email_cc'] : "") . ", ".$CMS->vars['company_email'];
            $list_email = explode(",", str_replace(" ", "", $str_email));
            $list_email = array_unique($list_email);
            foreach ($list_email as $email) 
            {
                if(!$email or !filter_var($email, FILTER_VALIDATE_EMAIL)) { continue; }

                // Info
                $CMS->email->email_template = "customer_contact";
                $CMS->email->email_to = $email;
                $CMS->email->email_toname = $CMS->vars['smtp_user_display'];
                // $CMS->email->email_cc = $user['user_email'];

                // Email data
                $CMS->email->data['cus_name'] =  $con_name;
                $CMS->email->data['cus_email'] =  $con_email;
                $CMS->email->data['cus_subject'] =  $con_subject;
                $CMS->email->data['cus_content'] =  $con_content;
                $CMS->email->data['website_name'] =  $CMS->vars['website_title'];

                if ( $con_type == '1' AND $CMS->vars['theme'] == 'dsg' ) 
                {
                    $CMS->email->data['phone_address'] = "Phone: {$con_phone}<br>Address: {$con_address}";
                }else
                {
                    $CMS->email->data['cus_name'] .= $con_phone ? "<br/> Phone: {$con_phone}" : "";
                    $CMS->email->data['cus_name'] .= $con_address ? "<br/> Address: {$con_address}<br/>" : "";
                }

                

                // Send mail
                $CMS->email->quick_send(0,0);
            }



            return self::createMsg("Thank you for contact us, we will contact to you soon as possible!", "success");
        } else {
            return self::createMsg("Error while sending the contact, please try again!");
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

    /**
     * Subscribe / Signup to receive coupon
     * @return array
     */
    static public function subscribe()
    {
        global $CMS, $DB;

        // Google ReCaptcha Verifier
        if ( \core\ezy::google_recaptcha_verify() == false ) { return self::createMsg("Captcha is wrong, please try again"); }

        // Check token
        if(!\lib\security::check_token())
        {
            return self::createMsg("Please refresh page (F5) then try again.");
        }

        // Validate by ip
        if(intval($CMS->vars['enable_security_ip']))
        {
            if(!\lib\security::checkSecurityIp())
            {
                return false;
            }
        }


        // Input
        $first_name = trim($CMS->input['firstname']);
        $last_name = trim($CMS->input['lastname']);
        $con_name = trim($CMS->input['contactname']);
        $con_name = empty($con_name) ? "{$first_name} {$last_name}" : $con_name;
        $con_email = trim($CMS->input['contactemail']);
        $con_service = trim($CMS->input['contactservice']);
        $con_subject = !empty($CMS->input['contactsubject']) ? trim($CMS->input['contactsubject']) : '';
        $con_content = !empty(trim($CMS->input['contactcontent'])) ? trim($CMS->input['contactcontent']) : '';
        $con_bmonth = trim($CMS->input['contactbmonth']);
        $con_date = trim($CMS->input['contactdate']);
        $con_bday = trim($CMS->input['contactbday']);
        $con_birthday = trim($CMS->input['contactbirthday']);
        if(!$con_birthday)
        {
            $con_birthday = !empty($con_bmonth)||!empty($con_bday) ? "{$con_bmonth}/{$con_bday}" : "";
        }
        $con_phone = trim($CMS->input['contactphone']);

        $con_content .= "<hr>&nbsp;Additional informations: ";

        $con_content .= !empty($con_service) ? "<br>&nbsp;&nbsp;+ Service: {$con_service}" : '';
        $con_content .= !empty($con_date) ? "<br>&nbsp;&nbsp;+ Date: {$con_date}" : '';
        $con_content .= !empty($con_birthday) ? "<br>&nbsp;&nbsp;+ Birthday: {$con_birthday}" : '';

        //Check additional informations
        $additionalInputs = [];
        $additionalText = '';
        if(count($CMS->input['add']))
        {
            foreach($CMS->input['add'] as $key => $value)
            {
                $explodeKey = explode("_",$key);
                $last_index = count($explodeKey)-1;
                $tmp_key1 = $explodeKey[$last_index];

                if(!in_array($tmp_key1, ['label','value'])) continue;

                //Unset last element
                unset($explodeKey[$last_index]);
                $tmp_key2 = implode("_",$explodeKey);

                if($tmp_key1 == 'label')
                {
                    $additionalInputs[$tmp_key2][0] = $value;
                }
                else
                {
                    $additionalInputs[$tmp_key2][1] = $value;
                }
            }
        }

        if(!empty($additionalInputs))
        {
            foreach($additionalInputs as $addKey => $addItems)
            {
                ksort($addItems);
                $additionalInputs[$addKey] = implode(': ', $addItems);
            }

            $additionalText = implode('<br>&nbsp;&nbsp;+ ', $additionalInputs);
        }

        $con_content .= !empty($additionalText) ? "<br>&nbsp;&nbsp;+ {$additionalText}" : '';

        $con_time = time();

        // Check input
        if(!$CMS->class->input->is_email($con_email)) { return self::createMsg("Email is wrong. Please try again!"); }

        // End check
        $con_ipaddress = $CMS->class->input->get_client_ip();

        $count = $DB->query("INSERT INTO ".root_table."contact (con_name, con_email, con_subject, con_content, con_time, con_ipaddress, con_type, con_phone, con_address) VALUES ('{$con_name}', '{$con_email}', '{$con_subject}', '{$con_content}', '{$con_time}', '{$con_ipaddress}', '{$con_type}', '{$con_phone}', '{$con_address}')");

        if($count)
        {
            // Email
            $str_email = (isset($CMS->vars['company_email_cc']) ? $CMS->vars['company_email_cc'] : "") . ", ".$CMS->vars['company_email'];
            $list_email = explode(",", str_replace(" ", "", $str_email));
            $list_email = array_unique($list_email);
            foreach ($list_email as $email) 
            {
                // Check email
                if(!$email or !filter_var($email, FILTER_VALIDATE_EMAIL)) { continue; }

                // Info
                $CMS->email->email_template = "customer_contact";
                $CMS->email->email_to = $email;
                $CMS->email->email_toname = $CMS->vars['smtp_user_display'];
                // $CMS->email->email_cc = $user['user_email'];

                // Email data
                $CMS->email->data['cus_name'] =  $con_name;
                $CMS->email->data['cus_email'] =  $con_email;
                $CMS->email->data['cus_subject'] =  $con_subject;
                $CMS->email->data['cus_content'] =  $con_content;
                $CMS->email->data['website_name'] =  $CMS->vars['website_title'];

                if ( $con_type == '1' AND $CMS->vars['theme'] == 'dsg' )
                {
                    $CMS->email->data['phone_address'] = "Phone: {$con_phone}<br>Address: {$con_address}";
                }else
                {
                    $CMS->email->data['cus_name'] .= $con_phone ? "<br/> Phone: {$con_phone}" : "";
                    $CMS->email->data['cus_name'] .= $con_address ? "<br/> Address: {$con_address}<br/>" : "";
                }

                // Send mail
                $CMS->email->quick_send(0,0);
            }

            return self::createMsg("Thank you for contact us, we will contact to you soon as possible!", "success");
        } else {
            return self::createMsg("Error while sending the contact, please try again!");
        }


    }
}