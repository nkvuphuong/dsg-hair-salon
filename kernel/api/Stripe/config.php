<?php
require_once('vendor/autoload.php');

$stripe = array(
    "secret_key"      => "sk_test_DsAnWrh55LM0Ri29Jmon0EQW",
    "publishable_key" => "pk_test_UosYFHHTj5wnehZqJX3gYmhb"
);

\Stripe\Stripe::setApiKey($stripe['secret_key']);
?>