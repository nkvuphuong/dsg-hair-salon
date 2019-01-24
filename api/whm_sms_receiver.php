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
            // For mat: yes webid_orderid ; ex : yes w101_10
            // Get ord_id
            
            $rep = explode("-", $text[1]);
            $site_id = trim($rep[0]);
            $ord_id = trim($rep[1]);
            $sql = $DB->query("SELECT * FROM ".root_table."sites where site_id = '{$site_id}' AND site_deleted = 0 LIMIT 1 ");
            if($DB->num_rows($sql) > 0)
            {
                $site = $DB->fetch_array($sql);
                if($site['is_ssl'] == 1)
                {
                    $url = "https://www.".$site['site_domainname']."/api/update_order.php";
                }
                else
                {
                    $url = "http://www.".$site['site_domainname']."/api/update_order.php"; 
                }
               
                //Send request update order
                $curl = curl_init();
                curl_setopt( $curl , CURLOPT_URL , $url );
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, '&order_id='.$ord_id);
                $result = curl_exec( $curl );
                $n_result = json_decode($result,true);
                if($n_result['status'] == 'success')
                {
                    $msg = "Confirm order #".$ord_id ." success. Website: ".$site['site_domainname'];
                }
                else
                {
                    $msg = "Confirm order #".$ord_id ." faild. Website: ".$site['site_domainname'];
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
                $sms['sms_content'] .= "SMS token: ". $CMS->class->random->character(6);
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


// $log = [
//     'inbound' => $inbound,
//     'is_valid' => $isValid,
//     'site_id' => $site_id,
//     'ord_id' => $ord_id,
//     'text' => $text_bk,
//     'msg' => $msg
// ];
$log =  $msg."-ord_id: ".$ord_id."-site id:".$site_id;
$CMS->class->logs->key = "whm_sms_receiver";
$CMS->class->logs->insert(trim(strip_tags($log)));

var_dump($msg); exit;