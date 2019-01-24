<?php

// Load init
include_once("../init.cron.php");

// Load vendor
require_once root_path."vendor/autoload.php";
 
// Test case
$order_id = isset($CMS->input['order_id']) ? $CMS->input['order_id'] : "";
 
if ( ctype_alnum($order_id) == true )
{
    // Confirm BOoking
    if( $CMS->order->confirmBooking($order_id) )
    {
        $msg = 'success';
    }
    // Failed
    else
    {
        $msg = 'failed';
    }
}
 
$log = [
    'ord_id' => $order_id,
    'msg' => $msg
];
$CMS->class->logs->key = "api_update_order";
$CMS->class->logs->insert(@json_encode($log, JSON_UNESCAPED_UNICODE));

echo json_encode(array('status' => $msg)); exit;