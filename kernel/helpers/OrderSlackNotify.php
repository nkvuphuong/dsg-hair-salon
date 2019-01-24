<?php
/**
 * Created by PhpStorm.
 * User: PhuongNKV
 * Date: 11/19/2018
 * Time: 11:24 AM
 */

namespace Kernel\Helpers;

use lib\input;

include_once '../../vendor/autoload.php';

class OrderSlackNotify extends SlackNotify
{
    public $attaches = [];

    public function __construct($endpoint)
    {
        parent::__construct($endpoint);

        if (!empty($this->client)) {
            $this->client->setDefaultUsername('Order notifications');
        }
    }

    /**
     * @param array $attaches
     * @return $this
     */
    public function setAttaches($attaches = [])
    {
        $this->attaches = $attaches;
        return $this;
    }

    /**
     * @param $ord_id
     * @return $this
     */
    public function setAttachesByOrder($ord_id)
    {
        global $CMS;

        $order = $CMS->order->get_info($ord_id);

        if(!$order) return $this;

        //Customer
        $customer = $CMS->customer->getInfo($order['cus_id']);
        $customerField = [
            'title' => "Customer",
            'value' => input::arrayValue($customer, 'cus_full_name', 'N/A'),
            'short' => true,
        ];

        //Total
        $totalField = [
            'title' => "Total",
            'value' => $CMS->class->input->currency($order['ord_total']),
            'short' => true,
        ];

        //Products
        $items = $CMS->order->getItemByOrder($order['ord_id']);
        $productNames = array_column($items, 'ordi_name');

        $productField = [
            'title' => "Products",
            'value' => implode(", ", $productNames),
            'short' => true,
        ];

        $attaches[0] = [
            'fallback' => "Order: {$order['ord_name']}",
            'title' => "Order {$order['ord_name']}",
            'color' => 'good',
            'title_link' => \lib\input::vars('root_domain') . '/?site=order&act=show&id=' . $order['ord_id'],
            'fields' => [$customerField, $totalField, $productField],
        ];

        $this->attaches = $attaches;

        return $this;
    }

    /**
     * Notify when have a new order
     */
    public function newOrderNotify()
    {
        $this->send($this->attaches, 'Have a new order');
    }

    /**
     * Notify when cancel a order
     */
    function cancelOrderNotify()
    {
        $this->send($this->attaches, 'Cancelled order');
    }

    /**
     * Notify when complete a order
     * @param array $attaches
     */
    function completeOrderNotify()
    {
        $this->send($this->attaches, 'Completed order');
    }

    /**
     * Notify when process a order
     */
    function processOrderNotify()
    {
        $this->send($this->attaches, 'Order is processing');
    }

    /**
     * Notify when pending a order
     */
    function pendingOrderNotify()
    {
        $this->send($this->attaches, 'Order is pending');
    }
}