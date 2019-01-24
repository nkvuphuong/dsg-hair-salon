<?php

namespace models;

use core\ezy;


class newsletter
{
    /**
     * Get List newsletter
     * @return array
     */

    static public function addNewsletter()
    {
        global $CMS, $DB;

        // Google ReCaptcha Verifier
        if ( \core\ezy::google_recaptcha_verify() == false ) {
            return self::createMsg("Captcha is wrong, please try again");
        }

        // Validate by ip
        if(intval($CMS->vars['enable_security_ip']))
        {
            if(!\lib\security::checkSecurityIp())
            {
                return false;
            }
        }

        // Check token
        if(!\lib\security::check_token())
        {
            return self::createMsg("Please refresh page (F5) then try again.");
        }
       
        // Input
        $newsletter_email = trim($CMS->input['newsletter_email']);
        $newsletter_time = time();
        $newsletter_ip = $CMS->class->input->get_client_ip();

        // Check email
        if(!$newsletter_email) { return self::createMsg("Please enter your email!"); }
        if(!$CMS->class->input->is_email($newsletter_email)) { return self::createMsg("Email was wrong. Please try again!"); }
        if(self::checkExistEmail($newsletter_email)) { return self::createMsg("Email exists, please enter another email!");}
        // End check

        // Insert Database
        $result = $DB->query("INSERT INTO ".root_table."newsletter (newsletter_email, newsletter_time, newsletter_ip) VALUES ('{$newsletter_email}', '{$newsletter_time}', '{$newsletter_ip}')");

        //Clear cache
        $CMS->class->cache->mdelete('newsletter');

        // Output result
        if($result) {
            return self::createMsg("Thank you for registering, you will receive our newsletter!", "success");
        } else {
            return self::createMsg("Something was wrong, please try again!");
        }
    }

    /**
     * Alert message
     * @param string $message
     * @param string $status
     * @return array
     */

    static function createMsg($message="",$status="error")
    {
        $msg = array("status" => "{$status}",
                "message" => "{$message}");
        return $msg;
    }

    /**
     * Check exist email
     * @param string $email
     * @return bool
     */

    static function checkExistEmail($email="")
    {
        global $DB;

        if($email)
        {
            $DB->query("SELECT 0 FROM ".root_table."newsletter WHERE newsletter_email = '{$email}' AND newsletter_deleted = 0 LIMIT 1");
            return $DB->num_rows() > 0 ? true : false;
        }else
        {
            return false;
        }
    }
}