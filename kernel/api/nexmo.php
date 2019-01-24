<?php

namespace api\Nexmo;

use \Nexmo;

class sms {

    static public function send($from, $to, $text)
    {
        global $CMS;

        //example of sending an sms using an API key / secret
        require_once root_path."vendor/autoload.php";

        $text = htmlspecialchars_decode($text);

        $params = [
            'to' => $to,
            'from' => $from,
            'text' => $text
        ];

        $CMS->class->logs->key = "sms_sender";
        $CMS->class->logs->insert(@json_encode($params, JSON_UNESCAPED_UNICODE));

        if($CMS->vars['sms_method'] == 'nexmo')
        {
            //create client with api key and secret
            $client = new Nexmo\Client(new Nexmo\Client\Credentials\Basic($CMS->vars['sms_nexmo_key'], $CMS->vars['sms_nexmo_secret']));


            try {
                //send message using simple api params
                $message = $client->message()->send($params);
                $return['status'] = 'success';
                $return['response'] = @json_encode($message->getResponseData(), 1);
                return $return;
            } catch (Nexmo\Client\Exception\Exception $e) {
                $CMS->class->logs->key = "sms_error";
                $CMS->class->logs->insert($e->getMessage());

                $return['status'] = 'fail';
                $return['response'] = $e->getMessage();

                return $return;
            }
        }
        else
        {
            $return = $CMS->sms->send($from, $to, $text);
            return $return;
        }
    }

}