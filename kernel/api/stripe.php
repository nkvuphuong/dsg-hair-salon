<?php
require_once(root_path."vendor/autoload.php");

$CMS->api->stripe = new api_stripe;
class api_stripe{

    public $secret_key;
    public $publishable_key;



    // ===========================================================================
    // EXECUTE
    // ===========================================================================
    public function init()
    {
        global $CMS;

        $this->secret_key      = $CMS->vars['stripe_secret_key'];
        $this->publishable_key = $CMS->vars['stripe_publishable_key'];
    }


    public function charge($data = "")
    {
        global $CMS;

        \Stripe\Stripe::setApiKey($this->secret_key);
        try {
            $customer = \Stripe\Customer::create(array(
                'email' => $data['email'],
                'source'  => $data['token']
            ));
        } catch (Exception   $e) {

            $data['message'] =  $e->getMessage();
            $data['status']  = "error";
            return $data;
        }

        try {
            $charge = \Stripe\Charge::create(array(
                'customer' => $customer->id,
                'amount'   => $data['total'],
                'currency' => 'usd'
            ));
            $reponse = (array) $charge;
            $data_charge = $this->convert_array($reponse);

            $data['message'] =  "success";
            $data['status']  = "success";
            $data['data_reponse']  = $data_charge['_values'];
            return $data;


        } catch (Exception $e) {

            $data['message'] =  $e->getMessage();
            $data['status']  = "error";
            return $data;
        }



    }


    public function convert_array($arr = array())
    {
        $arr_output = array();
        foreach ($arr as $key => $value) {
            # code...
            $key = str_replace(" ", "", $key);
            $key = preg_replace('/\s+/', '', $key);
            $key = str_replace("*", "", $key);
            $key = trim($key);
            $arr_output[$key] = $value;
        }
        return $arr_output;
    }

}