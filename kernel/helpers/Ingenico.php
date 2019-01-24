<?php
/**
 * Created by PhpStorm.
 * User: PhuongNKV
 * Date: 11/26/2018
 * Time: 8:24 AM
 */

namespace Kernel\Helpers;


use Ingenico\Connect\Sdk\Client;
use Ingenico\Connect\Sdk\Communicator;
use Ingenico\Connect\Sdk\CommunicatorConfiguration;
use Ingenico\Connect\Sdk\DefaultConnection;
use Ingenico\Connect\Sdk\Domain\Payment\CapturePaymentRequest;
use Ingenico\Connect\Sdk\Merchant\Payments\FindPaymentsParams;

class Ingenico
{
    public $apiKeyId = 'df039104b3579672';
    public $apiSecret = 'd8VzIEcrbR/RPm9fPgBCO+rATnvED+4aLOC7oXNL00o=';
    public $apiEndpoint = 'https://eu.sandbox.api-ingenico.com';
    public $integrator = '3FS';
    public $merchantId = '2926';
    public $client;

    public function __construct()
    {
        $communicatorConfiguration =
            new CommunicatorConfiguration($this->apiKeyId, $this->apiSecret, $this->apiEndpoint, $this->integrator);
        $connection = new DefaultConnection();
        $communicator = new Communicator($connection, $communicatorConfiguration);
        $this->client = new Client($communicator);
    }

    public function getPayment($paymentId)
    {
        return $this->client->merchant($this->merchantId)->payments()->get($paymentId);
    }

    public function findPayments($merchantReference, $merchantOrderId)
    {
        $query = new FindPaymentsParams();
        $query->merchantReference = $merchantReference;
        $query->merchantOrderId = $merchantOrderId;
        $query->offset = 0;
        $query->limit = 10;

        return $this->client->merchant($this->merchantId)->payments()->find($query);
    }
}