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

class TrxSlackNotify extends SlackNotify
{
    public $attaches = [];

    public function __construct($endpoint)
    {
        parent::__construct($endpoint);

        if (!empty($this->client)) {
            $this->client->setDefaultUsername('Transaction notifications');
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
     * @param $trx_id
     * @return $this
     */
    public function setAttachesByTrx($trx_id)
    {
        global $CMS;

        $attaches = [];

        $attachInfo = $this->createAttachByTrx($trx_id);

        if ($attachInfo) {
            $attaches[] = $attachInfo;
        }

        $this->setAttaches($attaches);

        return $this;
    }

    /**
     * @param $trx_id
     * @return array|bool
     */
    public function createAttachByTrx($trx_id)
    {
        global $CMS;

        $trx = $CMS->transactions->getInfo($trx_id);

        if(!$trx) return false;

        $cus_type = $trx['cus_type']; //1: customer, 2: supplier, 3: staff

        $buyer = [];
        if ($cus_type == 1) {
            $customer = $CMS->customer->getInfo($trx['cus_id']);
            $buyer = [
                'title' => "Customer",
                'value' => input::arrayValue($customer, 'cus_full_name', 'N/A'),
                'short' => true,
            ];
        } else if ($cus_type == 2) {
            $supplier = $CMS->supplier->get_info($trx['supplier_id']);
            $buyer = [
                'title' => "Customer",
                'value' => input::arrayValue($supplier, 'supplier_name', 'N/A'),
                'short' => true,
            ];
        } else if ($cus_type == 3) {
            $user = $CMS->user->get_info($trx['user_assign']);
            $buyer = [
                'title' => "Customer",
                'value' => input::arrayValue($user, 'user_display_name', 'N/A'),
                'short' => true,
            ];
        }

        $total = [
            'title' => "Total",
            'value' => $CMS->class->input->currency($trx['trx_total']),
            'short' => true,
        ];

        $fields = [];

        if ($buyer) {
            $fields[] = $buyer;
        }

        $fields[] = $total;

        $items = $CMS->transactions->getItemAll($trx['trx_id'], 'all', 1);

        $productsName = array_column($items, 'tri_name');

        if (count($items)) {
            $fields[] = [
                'title' => "Products",
                'value' => implode(", ", $productsName),
                'short' => true,
            ];
        }

        $trx_is_recurring = $trx['trx_is_recurring'];

        $attach = [
            'fallback' => input::lang('trx_subtype_0'.$trx['trx_subtype']) . ': ' . $trx['trx_code'] . ($trx_is_recurring ? ' (recurring)' : ''),
            'title' => input::lang('trx_subtype_0'.$trx['trx_subtype']) . ': ' . $trx['trx_code'] . ($trx_is_recurring ? ' (recurring)' : ''),
            'color' => 'good',
            'title_link' => input::vars('root_domain') . "/?site=transactions&act=show&type={$trx['trx_type']}&sub={$trx['trx_subtype']}&id={$trx['trx_id']}",
            'fields' => $fields
        ];

        return $attach;
    }

    /**
     * Notify when new transactions are created by recurring
     */
    function recurringTrxNotify() {
        $this->send($this->attaches, ''); //Transactions were created by recurring automatically
    }

    /**
     * Notify when have a new transaction
     */
    public function newTrxNotify()
    {
        $this->send($this->attaches, ''); //Have a new transaction
    }

    /**
     * Notify when have a new transaction
     */
    public function cloneAsRecurringNotify()
    {
        $this->send($this->attaches, ''); //Have a new recurring
    }
}