<?php

// Load init
include_once("../init.cron.php");

// Load vendor
require_once root_path."vendor/autoload.php";

// Test case
$text = isset($CMS->input['text']) ? $CMS->input['text'] : "";
$msisdn = isset($CMS->input['msisdn']) ? $CMS->input['msisdn'] : "";

// Check text from SMS Provider
$inbound = \Nexmo\Message\InboundMessage::createFromGlobals();

if($isValid = $inbound->isValid()){

    if(isset($inbound['text']))
    {
        //    $text = $inbound->getBody();
        $text_bk = $text = $inbound['text'];
        $msisdn = $inbound['msisdn'];

        // If message's content available
        $text = $text ? explode(" ", strtolower(urldecode($text))) : "";

// Check message is "Yes"
        if( $text[0] == "yes" )
        {
            // Get ord_id
           // $ord_id = trim($text[1]);
            $rep = explode("-", $text[1]);
            $site_id = trim($rep[0]);
            $ord_id = trim($rep[1]);

            // Check alphanum
            if ( ctype_alnum($ord_id) == true )
            {
                // Confirm BOoking
                if( $CMS->order->confirmBooking($ord_id) )
                {
                    $msg = 'Confirm success';
                }
                // Failed
                else
                {
                    $msg = 'Confirm failed';
                }
            }
        }
// Invalid syntax
        else
        {
            $msg = "Invalid syntax";

            if($msisdn)
            {
                $sms['sms_from'] = $CMS->vars['sms_nexmo_number'];
                $sms['sms_to'] = $msisdn;
                $sms['sms_content'] = $CMS->smstpl->renderContent('booking_confirm_wrong_syntax');
                $sms['sms_content'] .= ' - At ' . date("g:i A, Y/m/d",time());
                $sms['sms_reply'] = $msisdn;

                $CMS->sms->add($sms);
            }
        }
    }
    else
    {
        $text_bk = "Text is not available";
    }
}
else
{
    $msg = "Invalid";
}


$log = [
    'inbound' => $inbound,
    'is_valid' => $isValid,
    'ord_id' => $ord_id,
    'text' => $text_bk,
    'msg' => $msg
];
$CMS->class->logs->key = "sms_receiver";
$CMS->class->logs->insert(@json_encode($log, JSON_UNESCAPED_UNICODE));

var_dump($msg); exit;