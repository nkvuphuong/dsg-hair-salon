<?php
  namespace api;

  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  use net\authorize\api\constants as Cons;
    
  define("AUTHORIZENET_LOG_FILE", "phplog");
  //Load model
  

/**
* Authorize
*/
class authorize
{
    const SANDBOX = "https://apitest.authorize.net";
    const PRODUCTION = "https://api2.authorize.net";

    const VERSION = "1.9.5";
    const RESPONSE_OK = "Ok";

    static public $merchant_login_id = "";
    static public $merchant_transaction_key = "";
    static public $mode = "";
    static public $urlSuccess = "";
    static public $urlError = "";

    // ThamLV-Y2018M8D22: status and message
    static public $status = "";
    static public $message = "";

    static function __init()
    {
        global $CMS;
        require root_path.'vendor/autoload.php';
        self::$merchant_login_id = intval($CMS->vars['authorize_is_live']) > 0 ? $CMS->vars['authorize_login_id'] : "2rXNHABn8m54";
        
        self::$merchant_transaction_key = intval($CMS->vars['authorize_is_live']) > 0 ? $CMS->vars['authorize_transaction_key'] : "5ez4EB3Y27mqW4r6";
        self::$mode = intval($CMS->vars['authorize_is_live']) > 0 ? self::PRODUCTION : self::SANDBOX;
        
        // ThamLV-Y2018M8D22: status and message
        self::$status = "";
        self::$message = "";
    }


    static function chargeCreditCard($cus_order=[], $product=[], $input=[], $sublog_id=0)
    {
        global $CMS, $DB;

        $amount = floatval($cus_order['ord_total']);
        $country_name = $input['cus_country'];
        $city_name = $input['city_name'];
        $district_name = $input['district_name'];
        // Shipping
        $shipcountry_name = $input['ship_country'];
        $shipcity_name = $input['shipcity_name'];
        $shipdistrict_name = $input['shipdistrict_name'];

        /* Create a merchantAuthenticationType object with authentication details
           retrieved from the constants file */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(self::$merchant_login_id);
        $merchantAuthentication->setTransactionKey(self::$merchant_transaction_key);
        
        // Set the transaction's refId
        $refId = 'ref' . time();

        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($input['authorize_card_number']);
        $creditCard->setExpirationDate($input['authorize_expiration_date']);
        $creditCard->setCardCode($input['authorize_cvv_cvc']);

        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        // Create order information
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber($cus_order['ord_id']);
        $order->setDescription("ORD{$cus_order['ord_id']}");

        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType();
        $customerAddress->setFirstName($input['cus_first_name']);
        $customerAddress->setLastName($input['cus_last_name']);
        // $customerAddress->setCompany("Souveniropolis");
        $customerAddress->setAddress($input['cus_address']);
        $customerAddress->setPhoneNumber($input['cus_phone']);
        $customerAddress->setEmail($input['cus_email']);
        $customerAddress->setCity($city_name);
        $customerAddress->setState($district_name);
        $customerAddress->setZip($input['cus_zipcode']);
        $customerAddress->setCountry($country_name);

        // Set shipping
        $shippingAddress = new AnetAPI\NameAndAddressType();
        $shippingAddress->setFirstName($input['ship_first_name']);
        $shippingAddress->setLastName($input['ship_last_name']);
        $shippingAddress->setAddress($input['ship_address']);
        $shippingAddress->setCity($shipcity_name);
        $shippingAddress->setState($shipdistrict_name);
        $shippingAddress->setZip($input['cus_zipcode']);
        $shippingAddress->setCountry($shipcountry_name);

        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType();
        $customerData->setType("individual");
        $customerData->setId("#CUS".$input['cus_id']);
        $customerData->setEmail($input['cus_email']);

        // Add values for transaction settings
        $duplicateWindowSetting = new AnetAPI\SettingType();
        $duplicateWindowSetting->setSettingName("duplicateWindow");
        $duplicateWindowSetting->setSettingValue("60");

        // Add some merchant defined fields. These fields won't be stored with the transaction,
        // but will be echoed back in the response.
        $hash = "3fauthorized";
        $str_random = $CMS->class->random->character(9);
        $token = md5($hash.$str_random.time());

        $merchantDefinedField1 = new AnetAPI\UserFieldType();
        $merchantDefinedField1->setName("Token Transaction");
        $merchantDefinedField1->setValue($token);

        // $merchantDefinedField2 = new AnetAPI\UserFieldType();
        // $merchantDefinedField2->setName("favoriteColor");
        // $merchantDefinedField2->setValue("blue");

        

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setShipTo($shippingAddress);

        $transactionRequestType->setCustomer($customerData);
        $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
        $transactionRequestType->addToUserFields($merchantDefinedField1);
        $tax = new AnetAPI\ExtendedAmountType();
        $tax->setAmount($cus_order['ord_tax']);
        $transactionRequestType->setTax($tax);
        // $transactionRequestType->addToUserFields($merchantDefinedField2);
        // Set line item
        for ($x=0; $x<count($input['product_id']);$x++) 
        {
            $items = new AnetAPI\LineItemType();
            $items->setItemId($input['product_id'][$x]);
            $items->setName("P-{$input['product_id'][$x]}");
            $items->setDescription($CMS->class->seo->remove_vietnamese("{$input['product_name'][$x]}"));
            $items->setQuantity($input['product_quantity'][$x]); 
            $items->setUnitPrice($input['product_price'][$x]);
            $taxable = floatval($input['product_tax'][$x]) > 0 ? True : False;
            $items->setTaxable($taxable);
            $transactionRequestType->addToLineItems($items);
        }
        
        // Assemble the complete transaction request
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);

        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(self::$mode);

        if ($response != null) {
            $message = "";
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == self::RESPONSE_OK) {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();
            
                if ($tresponse != null && $tresponse->getMessages() != null) {
                    $str_message = " Successfully created transaction with Transaction ID: " . $tresponse->getTransId()."\n";
                    $str_message .= " Transaction Response Code: " . $tresponse->getResponseCode() ."\n";
                    $str_message .= " Message Code: " . $tresponse->getMessages()[0]->getCode() ."\n";
                    $str_message .= " Auth Code: " . $tresponse->getAuthCode()."\n";
                    $str_message .= " Description: " . $tresponse->getMessages()[0]->getDescription()."\n";
                    self::updateResponse($sublog_id, $str_message);

                    // ThamLV-Y2018M8D22: status and message
                    self::$status = "success";
                    self::$message = $str_message;

                    return self::$urlSuccess;
                } else {
                    
                    $str_error = "Transaction Failed \n";
                    if ($tresponse->getErrors() != null) {
                        $str_error .= " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                        $str_error .= " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
                    }
                    $message .= $tresponse->getErrors()[0]->getErrorText();

                    // ThamLV-Y2018M8D22: status and message
                    self::$status = "error";
                    self::$message = $message;
                    
                    self::checkError($str_error, $sublog_id, $message);
                    return self::$urlError;
                }
                // Or, print errors if the API request wasn't successful
            } else {
                $str_error = "Transaction Failed \n";
                $tresponse = $response->getTransactionResponse();
            
                if ($tresponse != null && $tresponse->getErrors() != null) {
                    $str_error .= " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                    $str_error .= " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
                    $message .= $tresponse->getErrors()[0]->getErrorText();
                } else {
                    $str_error .= " Error Code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
                    $str_error .= " Error Message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";
                    $message .= $response->getMessages()->getMessage()[0]->getText();
                }

                // ThamLV-Y2018M8D22: status and message
                self::$status = "error";
                self::$message = $message;
                
                self::checkError($str_error, $sublog_id, $message);
                return self::$urlError;
            }
        } else {

            // ThamLV-Y2018M8D22: status and message
            self::$status = "error";
            self::$message = "No response returned";

            self::checkError("No response returned", $sublog_id);
            return self::$urlError;
        }

    }

    static function checkError($response='', $sublog_id=0, $message="")
    {
        global $CMS, $DB;

        if($response and $sublog_id)
        {
            $response = $CMS->class->editor->input($response, "text");
            $DB->query("UPDATE ".root_table."subscription_logs SET sublog_response='{$response}', sublog_status='error' WHERE sublog_id='{$sublog_id}'");
        }
        $_SESSION['msg'] .= $message ? $message : "Error payment. Please try again or contact to admin.";
        return false;
    }

    static function updateResponse($sublog_id=0,$data='')
    {
        global $CMS, $DB, $member;
        $sublog_id = intval($sublog_id);
        $sublog_response = trim($data);
        $sublog_response_time = time();
        // Insert data
        return $DB->query("UPDATE ".root_table."subscription_logs SET sublog_response='{$sublog_response}', sublog_response_time='{$sublog_response_time}' WHERE sublog_id='{$sublog_id}'");
    }

}
