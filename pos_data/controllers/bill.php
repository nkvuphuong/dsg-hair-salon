<?php

namespace controller;

use core\ezy;
use lib\date;
use lib\input;

class bill
{
    static function payment()
    {
        global $CMS;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = file_get_contents("php://input");

            $data = input::jsonDecode($data);
            $results = \models\bill::payment($data);
            $results['payload'] = $data;

            input::jsonEncode($results);
        }
    }

    static function load_bills()
    {
        global $CMS;

        $filter = [
            'store' => input::get('store'),
            'id' => input::get('id'),
            'phone' => input::get('phone'),
            'time' => input::get('time') ? date::getUnixTimestamp(trim(urldecode(input::get('time'))), 'dmY') * 1 : 0,
            'amount' => input::get('amount'),
            'status' => input::get('status'),
        ];

        $bills = \models\bill::loadBills($filter, input::get('per_page'));
        $results = ['bills' => $bills, 'totalRows' => ezy::$page['rows']];
        input::jsonEncode($results);
    }

    static function print_bill()
    {
        global $CMS, $tpl;

        if($tpl->data = \models\bill::printBill($CMS->input['id'])) {
            $html = ezy::html("bill/main");

            $result = [
                'status' => 'success',
                'html' => $html
            ];
        } else {
            $result = [
              'status' => 'fail',
              'msg' => 'Lỗi in hóa đơn (44)'
            ];
        }

        input::jsonEncode($result);
    }
}