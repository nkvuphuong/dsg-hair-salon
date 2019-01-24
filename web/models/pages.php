<?php

namespace models;

use core\ezy;
use lib\input;

class pages
{
    static public $sqlAdd="";
    static function getInfo($record_id=0, $field_return="*")
    {
        global $DB, $CMS;

        if($record_id and $field_return)
        {
            // Count field
            $countField = count(explode(",", $field_return));
            // sqladd
            $sql_add = self::$sqlAdd;
            //Query
            $results = $DB->fetch_data("SELECT {$field_return} FROM ".root_table."pages WHERE pages_deleted=0 AND (pages_id='{$record_id}' OR pages_shorturl='{$record_id}' OR pages_shorturl LIKE '%\"{$record_id}\"%') {$sql_add} LIMIT 1",'pages');

            $data = isset($results[0]) ? $results[0] : null;

            //Check return
            if($countField == 1 and $field_return != "*")
            {
                return isset($data[$field_return]) ? $data[$field_return] : null;
            }else
            {
                return $data;
            }
        }else
        {
            return false;
        }
    }

    static function send_info()
    {
        global $CMS, $DB;

        // print "<pre>";
        // print_r($CMS->vars);
        // print_r($CMS->input);exit;

        // $time = $CMS->class->date->date_format(time());
        // $CMS->email->email_template = "order_success";
        // $CMS->email->email_to = $CMS->vars['company_email'];
        // $CMS->email->email_toname = "Admin";

        // email_from
        $etc_country = $CMS->input['enquiry_country_etc'] ? $CMS->input['enquiry_country_etc'] : "";
        $country = $CMS->input['enquiry_country']. " ETC: ". $etc_country;
        $contact_email = $CMS->input['enquiry_email'];
        $contact_tel = $CMS->input['enquiry_tel'];
        if($CMS->input['enquiry_contact_method'] == "phone")
        {
            $contact_method = "Phone: {$CMS->input['phone_contact1']} {$CMS->input['phone_contact2']}";
        }elseif($CMS->input['enquiry_contact_method'] == "email")
        {
            $contact_method = "Email: {$CMS->input['email_contact1']} {$CMS->input['email_contact2']}";
        }elseif($CMS->input['enquiry_contact_method'] == "messenger")
        {
            $contact_method = "Messenger: {$CMS->input['messenger_contact1']} - {$CMS->input['messenger_contact2']}";
        }

        $contact_full_name = $CMS->input['enquiry_name'];
        $contact_gender = $CMS->input['enquiry_gender'] == 1 ? "Male" : "Female";
        $etc_age = $CMS->vars['enquiry_age_etc'] ? $CMS->vars['enquiry_age_etc'] : "";
        $contact_age = $CMS->input['enquiry_age']." Etc: ".$etc_age;
        $multi_select = $CMS->input['MultipleSelected'];

        $table_html =<<<EOF
        <style type="text/css"><!--

  #outlook a { padding: 0; }
          .ReadMsgBody { width: 100%; }
          .ExternalClass { width: 100%; }
          .ExternalClass * { line-height:100%; }
          body { margin: 0; padding: 0; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
          table, td { border-collapse:collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
          img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }
          p {
            display: block;
            margin: 13px 0;
        }
    --></style>
    <!-- [if !mso]><!-->
    <style type="text/css"><!--
        @import url(https://fonts.googleapis.com/css?family=Ubuntu:400,500,700,300);

    --></style>
    <style type="text/css"><!--
        @media only screen and (max-width:480px) {
          @-ms-viewport { width:320px; }
          @viewport { width:320px; }
      }

  --></style>
  <!--<![endif]-->
  <style type="text/css"><!--
    @media only screen and (min-width:480px) {
        .mj-column-per-100, * [aria-labelledby="mj-column-per-100"] { width:100%!important; }
    }
--></style>
<div class="mj-body" style="background-color: #eceff4;"><!-- [if mso]>
    <table border="0" cellpadding="0" cellspacing="0" width="700" align="center" style="width:700px;"><tr><td>
        <![endif]-->
        <div style="margin: 0 auto; max-width: 700px;">
            <table cellpadding="0" cellspacing="0" style="width: 100%; font-size: 0px;" align="center">
                <tbody>
                    <tr>
                        <td style="text-align: center; vertical-align: top; font-size: 0; padding: 20px 0; padding-top: 0px; padding-bottom: 24px;"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- [if mso]>
    </td></tr></table>
    <![endif]--> <!-- [if mso]>
    <table border="0" cellpadding="0" cellspacing="0" width="700" align="center" style="width:700px;"><tr><td>
        <![endif]-->
        <div style="margin: 0 auto; max-width: 700px; background: #d8e2e7;">
            <table cellpadding="0" cellspacing="0" style="width: 100%; font-size: 0px; background: #d8e2e7;" align="center">
                <tbody>
                    <tr>
                        <td style="text-align: center; vertical-align: top; font-size: 0; padding: 1px;"><!-- [if mso]>
                          <table border="0" cellpadding="0" cellspacing="0"><tr><td style="width:700px;">
                              <![endif]-->
                              <div style="vertical-align: top; display: inline-block; font-size: 13px; text-align: left; width: 100%;" class="mj-column-per-100" aria-labelledby="mj-column-per-100">
                                <table style="background: white;" width="100%">
                                    <tbody>
                                        <tr>
                                            <td style="font-size: 0; padding: 30px 30px 16px;" align="left">
                                                <div class="mj-content" style="cursor: auto; color: #000000; font-family: 'Proxima Nova', Arial, Arial, Helvetica, sans-serif; font-size: 15px; line-height: 22px; text-align: center;"><span style="font-size: 26px;"><strong>You have a contact from customer</strong></span></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 0; padding: 0 30px 6px;" align="left">
                                                <div class="mj-content" style="cursor: auto; color: #000000; font-family: Proxima Nova, Arial, Arial, Helvetica, sans-serif; font-size: 15px; line-height: 22px;">Dear Admin!<br />You have a contact from customer. Infomation<br />- Country: {$country}<br />- Email:{$contact_email}<br />- Tel:{$contact_tel}<br />- Contact method :{$contact_method}<br />- Full name:{$contact_full_name}<br />- Gender: {$contact_gender}<br />- Age: {$contact_age}<br />-&nbsp;Desired surgical part:{$multi_select}<br /><br /></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 0; padding: 8px 16px 10px; padding-bottom: 16px; padding-right: 30px; padding-left: 30px;" align="left"></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 0; padding: 0 30px 30px 30px;" align="left">
                                                <div class="mj-content" style="cursor: auto; color: #000000; font-family: Proxima Nova, Arial, Arial, Helvetica, sans-serif; font-size: 15px; line-height: 22px;">&mdash; Thanks you so much</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- [if mso]>
                        </td></tr></table>
                        <![endif]--></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- [if mso]>
    </td></tr></table>
    <![endif]--> <!-- [if mso]>
    <table border="0" cellpadding="0" cellspacing="0" width="700" align="center" style="width:700px;"><tr><td>
        <![endif]-->
        <div style="margin: 0 auto; max-width: 700px;">
            <table cellpadding="0" cellspacing="0" style="width: 100%; font-size: 0px;" align="center">
                <tbody>
                    <tr>
                        <td style="text-align: center; vertical-align: top; font-size: 0; padding: 20px 0 0;"><!-- [if mso]>
                          <table border="0" cellpadding="0" cellspacing="0"><tr><td style="width:700px;">
                              <![endif]-->
                              <div style="vertical-align: top; display: inline-block; font-size: 13px; text-align: left; width: 100%;" class="mj-column-per-100" aria-labelledby="mj-column-per-100">
                                <table width="100%">
                                    <tbody>
                                        <tr>
                                            <td style="font-size: 0; padding: 0px;" align="center">
                                                <div class="mj-content" style="cursor: auto; color: #6b7a85; font-family: Proxima Nova, Arial, Arial, Helvetica, sans-serif; font-size: 15px; line-height: 22px;">&copy; {$CMS->vars['website_title']}</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- [if mso]>
                        </td></tr></table>
                        <![endif]--></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- [if mso]>
    </td></tr></table>
    <![endif]--> <!-- [if mso]>
    <table border="0" cellpadding="0" cellspacing="0" width="700" align="center" style="width:700px;"><tr><td>
        <![endif]-->
        <div style="margin: 0 auto; max-width: 700px;">
            <table cellpadding="0" cellspacing="0" style="width: 100%; font-size: 0px;" align="center">
                <tbody>
                    <tr>
                        <td style="text-align: center; vertical-align: top; font-size: 0; padding: 20px 0; padding-top: 0px; padding-bottom: 24px;"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- [if mso]>
    </td></tr></table>
    <![endif]--></div>
EOF;

    
    $CMS->class->mail->sendmail($CMS->vars['company_email'], "Admin", $CMS->vars['smtp_user'], $CMS->vars['smtp_user_display'], "You have a contact from customer", $table_html, $cc = "", $bcc = "", $file = "");
}
}