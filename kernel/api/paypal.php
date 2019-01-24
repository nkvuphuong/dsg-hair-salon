<?php
namespace api;

//Process
use \PayPal\Api\Amount;
use \PayPal\Api\Details;
use \PayPal\Api\Item;
use \PayPal\Api\ItemList;
use \PayPal\Api\Payer;
use \PayPal\Api\Payment;
use \PayPal\Api\RedirectUrls;
use \PayPal\Api\Transaction;
use \PayPal\Api\TransactionBase;
use \PayPal\Api\ExecutePayment;
use \PayPal\Api\PaymentExecution;
use \PayPal\Rest\ApiContext;
use \PayPal\Auth\OAuthTokenCredential;
use \PayPal\Exception\PayPalConnectionException;
use \PayPal\Exception\PayPalInvalidCredentialException;
use \PayPal\Cache\AuthorizationCache;


class paypal
{
    static public $apiContext = null;
    static public $redirect_default = "/giftcards/";

    // ThamLV-Y2018M8D23: status and message
    static public $status = "";
    static public $message = "";
    static public $urlSuccess = "/payment/checksuccess/";
    static public $urlCancel = "/payment/cancel/";

    static function init()
    {
        global $CMS;
        require_once root_path."vendor/autoload.php";
        self::$apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                "{$CMS->vars['paypal_client_id']}",
                "{$CMS->vars['paypal_client_secret']}"
            )
        );

        $mode = intval($CMS->vars['is_live']) > 0 ? "live" : "sandbox";

        self::$apiContext->setConfig(
        array(
            'mode' => $mode,
            // 'log.LogEnabled' => true,
            // 'log.FileName' => '../PayPal.log',
            // 'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
            // 'cache.enabled' => true,
            // 'http.CURLOPT_CONNECTTIMEOUT' => 30
            // 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
            //'log.AdapterFactory' => '\PayPal\Log\DefaultLogFactory' // Factory class implementing \PayPal\Log\PayPalLogFactory
        )
    );
    
        // ThamLV-Y2018M8D23: status and message
        self::$status = "";
        self::$message = "";
        
        self::$urlSuccess = str_replace("{$CMS->vars['root_domain']}/", "{$CMS->vars['root_domain']}/", self::$urlSuccess);
        self::$urlSuccess = $CMS->vars['root_domain'] . '/' .ltrim(self::$urlSuccess, '/');

        self::$urlCancel =  str_replace("{$CMS->vars['root_domain']}/", "{$CMS->vars['root_domain']}/", self::$urlCancel);
        self::$urlCancel = $CMS->vars['root_domain'] . '/' .ltrim(self::$urlCancel, '/');
    }


    static function payment($order=[], $product=[], $sublog_id=0)
    {
        global $CMS;

        // print "<pre>"; print_r($order); print_r($product);print_r($_SESSION['total_tax']);exit;
        // set url
        // $urlSuccess = $CMS->vars['root_domain'].'/payment/checksuccess';
        // $urlCancel = $CMS->vars['root_domain'].'/payment/cancel';

        // Set input
        $desc = $order['ord_note'];
        $subtotal = floatval($order['ord_amount']);
        $tax = $order['ord_tax'] ? $order['ord_tax'] : 0;//round(($order['ord_amount']*10)/100,2);//floatval($order['ord_tax']);
        $discount = $order['ord_total_discount'];//$order['ord_discount_type']==0? round($order['ord_discount']*$order['ord_amount'], 2) : $order['ord_discount'];
        $insurance = 0;
        $shipfee = $order['ord_fee_shipping'];//$CMS->vars['shipping_fee'] ? $CMS->vars['shipping_fee'] : "5";
        // Total 
        $total = floatval($order['ord_total']); // $order['ord_total'] : da tinh thue
// print "Total: ".$total."<br/> Subtotal: ".$subtotal."<br/> Tax:".$tax."<br/> discount: ".$discount;exit;
        // Create new payer and method
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        // Set details
        $details = new Details();
        $details->setSubtotal($subtotal)
                ->setTax($tax)
                ->setShippingDiscount($discount)
                ->setInsurance($insurance)
                ->setShipping($shipfee);

        // Set item
        $list = [];
        foreach ($product as $key => $data) 
        {
            $Item = new Item();
            $row = $Item->setName($data['product_name'])
                ->setCurrency('USD')
                ->setQuantity($data['quantity'])
                ->setPrice($data['price_new']);
            array_push($list, $row);   
        }
        
        // Set item list
        $ItemList = new ItemList();
        $ItemList->setItems($list);


        // Set payment amount
        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal($total)
            ->setDetails($details);

        // Set transaction object
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($ItemList)
            ->setDescription($desc);

        // Set redirect urls
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(self::$urlSuccess)
            ->setCancelUrl(self::$urlCancel);

        // Create the full payment object
        $payment = new Payment();
        $payment = $payment->setIntent('sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));
        
        try {
            $payment->create(self::$apiContext);

            // ThamLV-Y2018M8D23: status and message
            self::$status = "success";
            self::$message = $payment;
        
        } catch (\Exception $ex) {
            // self::checkError($ex->getData(), $sublog_id);
            // header("location: ".self::$redirect_default); exit;
            // $check = self::checkError($ex->getData(), $sublog_id);
            // if($check)
            // {
            //     header("location: ".self::$redirect_default); exit;
            // }
            
            // ThamLV-Y2018M8D23: status and message
            self::$status = "error";
            self::$message = $ex->getData();

            self::checkError(self::$message, $sublog_id);
            return array(self::$message, self::$redirect_default);
        }

        // Get PayPal redirect URL and redirect user
        $approvalUrl = $payment->getApprovalLink();
        return array($payment, $approvalUrl);
    }

    static function process($sublog_id=0)
    {
        // Get payment object by passing paymentId
        $paymentId = $_GET['paymentId'];
        $payment = Payment::get($paymentId, self::$apiContext);
        $payerId = $_GET['PayerID'];

// Execute payment with payer id
        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        try {
            // Execute payment
            $result = $payment->execute($execution, self::$apiContext);

            // ThamLV-Y2018M8D23: status and message
            self::$status = "success";
            self::$message = $result;

            return $result;
        } catch (\Exception $ex) {
            // self::checkError($ex->getData());
            // header("location: ".self::$redirect_default); exit;
            // // die($ex);
            // // $check = self::checkError($ex->getData());
            // // if($check) { return true; }

            // ThamLV-Y2018M8D23: status and message
            self::$status = "error";
            self::$message = $ex->getData();

            self::checkError(self::$message, $sublog_id);
            return self::$message;
        }
    }

    static function getPayment($paymentId=0, $sublog_id=0)
    {
        try {
            $payment = Payment::get($paymentId, self::$apiContext);

            // ThamLV-Y2018M8D23: status and message
            self::$status = "success";
            self::$message = $payment;

            return $payment;
        } catch (\Exception $ex) {
            // self::checkError($ex->getData());
            // header("location: ".self::$redirect_default); exit;
            // // die($ex);
            // // $check = self::checkError($ex->getData());
            // // if($check) { return true; }

            // ThamLV-Y2018M8D23: status and message
            self::$status = "error";
            self::$message = $ex->getData();

            self::checkError(self::$message, $sublog_id);
            return self::$message;
        }
    }

    static function checkError($response='', $sublog_id=0)
    {
        global $CMS, $DB;

        if($response and $sublog_id)
        {
            $response = $CMS->class->editor->input($response, "text");
            $DB->query("UPDATE ".root_table."subscription_logs SET sublog_response='{$response}', sublog_status='error' WHERE sublog_id='{$sublog_id}'");
        }
        $_SESSION['msg'] .= "Error payment. Please try again or contact to admin.";
        return false;
    }
}

?>