<?php

namespace models;

use core\ezy;
use lib\date;
use lib\input;
use lib\page;

ezy::load_model("customer");
ezy::load_model("product");
ezy::load_model("staff");

class bill
{
    private static $trxKeys = [
        "trx_id" => "id",
        "trx_time" => "time",
        "trx_code" => "code",
        "trx_total" => "total",
        "trx_status" => "status",
        "trx_invoice_no" => "invoiceNo",
        "trx_total_discount" => "discountTotal",
        "trx_total" => "total",
        "trx_amount" => "amount",
        "trx_tax" => "tax",
        "trx_excess_cash" => "excessCash",
        "trx_receive_payment" => "paymentAmount",
        "trx_booking_phone" => "phone",
        "store_id" => "storeId",
        "ord_id" => "ordId",
        "ord_rating_status" => "ratingStatus",
    ];

    private static $triKeys = [
        'tri_name' => 'productName',
        'product_id' => 'productId',
        'tri_quantity' => 'quantity',
        'tri_price' => 'price',
        'tri_old_price' => 'oldPrice',
        'tri_subtotal' => 'subTotal',
        'tri_total' => 'total',
        'tri_discount_type' => 'discountType',
        'tri_discount_value' => 'discountValue',
        'tri_total_discount' => 'discountAmount',
        'tri_total_tax' => 'taxAmount',
        'tri_tax' => 'taxValue',
    ];

    /**
     * @param $data
     */
    static public function payment($data)
    {
        global $CMS, $DB;
        //Convert to insert to DB

        $product_name = [];
        $product_id = [];
        $item_id = [];
        $product_cycle_type = [];
        $product_type = [];
        $product_description = [];
        $product_cycle = [];
        $product_quantity = [];
        $product_price = [];
        $product_old_price = [];
        $product_commission_type = [];
        $product_commission_value = [];
        $product_tax = [];
        $product_discount_value = [];
        $product_discount_type = [];

        if ($naItems = self::checkBookedHours($data)) {
            $return = [
                'status' => 'fail',
                'reset' => 1,
                'msg' => "Thời gian đặt chỗ đã hết. Quý khách vui lòng chọn khung giờ khác.",
                'naItems' => $naItems
            ];
            return $return;
        }

        foreach ($data['items'] as $key => $item) {

            $commission_data = $CMS->user->getCommission(input::arrayValue($item['staff'], 'id'));
            if($commission_data) {
                $commission = input::arrayValue($commission_data, 'id_'.$item['product']['oriData']['id'], []);
            } else {
                $commission = [];
            }

            $product = $item['product']['oriData'];

            $product_name[] = $product['name'];
            $product_id[] = $product['id'];
            $item_id[] = $key;
            $product_cycle_type[] = 0;
            $product_type[] = $product['type'];
            $product_description[] = $product['description'];
            $product_cycle[] = 1;
            $product_quantity[] = $item['quantity'];
            $product_price[] = $item['price'];
            $product_old_price[] = $item['oldPrice'];
            $product_commission_type[] = input::arrayValue($commission, 'product_commission_type', 0);
            $product_commission_value[] = input::arrayValue($commission, 'product_commission_value', 0);
            $product_tax[] = $item['taxType'] == 0 ? $item['taxValue'] : ($item['taxValue'] / $item['price']) * 100;
            $product_discount_value[] = $item['discountValue'];
            $product_discount_type[] = $item['discountType'];
            $product_booking_time[] = strtotime($item['bookingTime']);
            $product_staff[] = $item['staff']['id']*1;
        }

        $input = [
            'cus_id' => input::arrayValue($data['customer']['oriData'],'id'),
            'cus_name' =>  input::arrayValue($data['customer']['oriData'],'name'),
            'order_payment_method' => $data['paymentMethod']*1,
            'account_id' => 0,
            'store_id' => input::arrayValue($data['store'],'id')*1,
            'user_id' => $data['staff']['id'],
            'ord_note' => $data['note'],
            'total_discount_type' => 0,
            'total_commission_type' => 0,
            'product_name' => $product_name,
            'product_id' => $product_id,
            'item_id' => $item_id,
            'product_cycle_type' => $product_cycle_type,
            'product_type' => $product_type,
            'product_description' => $product_description,
            'product_cycle' => $product_cycle,
            'product_quantity' => $product_quantity,
            'product_price' => $product_price,
            'product_old_price' => $product_old_price,
            'product_commission_type' => $product_commission_type,
            'product_commission_value' => $product_commission_value,
            'product_tax' => $product_tax,
            'product_discount_value' => $product_discount_value,
            'product_discount_type' => $product_discount_type,
            'total_discount_type' => $data['discountType'] * 1,
            'total_discount_value' => $data['discountValue'] * 1,
            'product_booking_time' => $product_booking_time,
            'product_staff' => $product_staff,
            'excess_cash' => $data['paymentAmount'] - $data['total'],
            'service_type' => $data['productType'] * 1,
            'booking_phone' => trim($data['phone'])
        ];

        //Backup $CMS->input
        $cms_input = $CMS->input;
        $CMS->input = $input;

        //Tạo order
        $lang_bk = $CMS->lang;
        $CMS->class->language->load("order", "admin_");
        $results = $CMS->order->add("quick_add");
        $CMS->lang = $lang_bk;

        if ($results) { //success
            //Tạo phiếu thanh toán
            if ($data['paymentAmount'] > 0) {

                //Chuẩn bị đầu vào để tạo receive payment
                $input = [
                    'type' => 1,
                    'sub' => 2,
                    'invoice' => $results['transaction_id'],
                    'cus_type' => 1, //Khách hàng
                    'trx_cus' => $data['customer']['oriData']['name'],
                    'cus_id' => $data['customer']['oriData']['id'],
                    'trx_email' => input::arrayValue($data['customer']['oriData'], 'email'),
                    'trx_payment_date' => date::format(time()),
                    'trx_method' => $data['paymentMethod']*1, //0: tiền mặt; 1: chuyển khoản
                    'trx_account' => 0,
                    'at_id' => 0,
                    'trx_msg' => '',
                    'trx_note' => '',
                    'trx_receive_payment' => $data['paymentAmount'],
                ];

                if ($data['paymentAmount'] > $data['total']) { //Thanh toán thừa
                    $input['tri_payment'] = [$results['transaction_id'] => $data['total']];
                    $input['trx_total'] = $data['total'];
                    $input['trx_amount'] = $data['total'];
                    $input['trx_receive_payment'] = $data['total'];
                } else { //
                    $input['tri_payment'] = [$results['transaction_id'] => $data['paymentAmount']];
                    $input['trx_total'] = $data['paymentAmount'];
                    $input['trx_amount'] = $data['paymentAmount'];
                }

                $CMS->input = $input;
                $receive = $CMS->transactions->add();

                if ($receive) {

                    if($data['paymentAmount'] >= $data['total']) { //Cập nhật lại trạng thái thanh toán của đơn hàng
                        $DB->update('order', [
                            'ord_id' => $results['ord_id'],
                            'payment_status' => 1
                        ], 'ord_id');

                        $DB->update('order_item', [
                            'ord_id' => $results['ord_id'],
                            'ordi_payment_status' => 1
                        ], 'ord_id');
                    }

                    $return = [
                        'status' => 'success',
                        'msg' => 'Đã thanh toán đơn hàng thành công',
                        'insertedId' =>  $results['transaction_id']*1,
                        'trx' => self::convertToDisplay($CMS->transactions->getInfo($results['transaction_id']*1)),
                    ];
                } else {
                    $return = [
                        'status' => 'fail',
                        'msg' => 'Không thể tạo giao dịch thanh toán',
                    ];
                }

            } else {
                $return = [
                    'status' => 'success',
                    'msg' => 'Đã thanh toán đơn hàng thành công',
                ];
            }
        } else { //fail
            $return = [
                'status' => 'fail',
                'msg' => $_SESSION['error_msg'],
            ];
        }

        unset($_SESSION['error_msg']);

        $CMS->input = $cms_input;

        return $return;
    }

    /**
     * convert key data for api
     * @param $result
     */
    static public function convertKeys($keys, $data)
    {
        $return = [];

        foreach ($data as $key => $value) {
            if (isset($keys[$key])) {
                $return[$keys[$key]] = $value;
            }
        }

        return $return;
    }

    /**
     * @param $keys
     * @param array $data
     * @return array
     */
    static public function revertKeys($keys, $data = [])
    {
        $return = [];

        $keys = array_flip($keys);

        foreach ($data as $key => $value) {
            if (isset($keys[$key])) {
                $return[$keys[$key]] = $value;
            }
        }

        return $return;
    }

    /**
     * @param array $data
     * @return array
     */
    static function convertToDisplay($data = [])
    {
        global $CMS;

        $items = $CMS->transactions->getItemAll($data['trx_id'], "all",1);
        $customer = $CMS->customer->getInfo($data['cus_id']);
        $data = self::convertKeys(self::$trxKeys, $data);

        $data['timestamp'] = $data['time']*1;
        $data['time'] = date::format($data['time']);
        $data['id'] *= 1;
        $data['amount'] *= 1;
        $data['invoiceNo'] *= 1;
        $data['tax'] *= 1;
        $data['total'] *= 1;
        $data['excessCash'] *= 1;
        $data['discountTotal'] *= 1;
        $data['paymentAmount'] *= 1;
        $data['storeId'] *= 1;
        $data['ratingStatus'] *= 1;

        $oriData = $data;
        $data['status'] = input::arrayValue($CMS->lang, 'trx_status_0' . $data['status']);
        $data['oriData'] = $oriData;

        //Convert keys of item
        if($items) {
            foreach($items as $kItem => $item) {
                $item = self::convertKeys(self::$triKeys, $item);
                $item['taxType'] = 0; //percent
                $product = product::getProduct($item['productId']);
                $product = $product ? product::convertToDisplay($product) : [];
                $item['product'] = $product;

                $items[$kItem] = $item;
            }
            $data['items'] = $items;
        }

        if($customer) {
            $customer = customer::convertToDisplay($customer);
            $data['customer'] = $customer;
        }

        return $data;
    }


    /**
     * @return array
     */
    static function loadBills($filter = [], $limit = 10)
    {
        global $CMS;

        //Load lang
        $bk_lang = $CMS->lang;
        $CMS->class->language->load("transactions", "admin_");

        $filter['store'] = input::arrayValue($filter, 'store') * 1;
        $filter['id'] *=  input::arrayValue($filter, 'id') * 1;
        $filter['phone'] = trim(input::arrayValue($filter, 'phone'));
        $filter['time'] = input::arrayValue($filter, 'time');
        $filter['amount'] = trim(input::arrayValue($filter, 'amount'));
        $filter['status'] = trim(input::arrayValue($filter, 'status'));
        $limit *= 1;

        $sql_add = "";

        if ($filter['store']) {
            $sql_add .= " T.store_id={$filter['store']} AND ";
        }

        if ($filter['phone']) {
            $sql_add .= " trx_booking_phone='{$filter['phone']}' AND ";
        }

        if ($filter['status'] !== '') {
            $filter['status'] *= 1;
            $sql_add .= " trx_status={$filter['status']} AND ";
        }

        if ($filter['time']) {
            $from = $filter['time'];
            $to = $filter['time'] + 3600 * 24;
            $sql_add .= " trx_time >= {$from} AND trx_time < {$to} AND ";
        }

        $sql = "SELECT T.*, O.ord_rating_status FROM " . root_table . "transaction T RIGHT JOIN " . root_table . "order O ON T.ord_id = O.ord_id where {$sql_add} trx_subtype=1 AND trx_deleted=0 ORDER BY trx_id DESC";

        $results = page::init($sql, $limit, true);

        if ($results) {
            foreach ($results as $key => $result) {
                $results[$key] = self::convertToDisplay($result);
            }
        }
        //Restore languagae
        $CMS->lang = $bk_lang;

        return $results;
    }

    static function printBill($id)
    {
        global $CMS;
        $data = [];
        $trx = $CMS->transactions->getInfo($id);
        if ($trx) {
            $items = $CMS->transactions->getItemAll($trx['trx_id'], "all",1);
            $customer = $CMS->customer->getInfo($trx['cus_id']);

            $data = [
              'trx' => $CMS->transactions->convertvalue($trx),
              'items' => $items,
              'customer' => $customer,
            ];
        }

        return $data;
    }

    static function checkBookedHours($data)
    {
        global $DB;
        $items = $data['items'];
        $storeId = $data['store']['id'];

        $naItems = []; //các item không khả dụng
        $slots = [];

        if($data['productType'] != 1) return $naItems;

        foreach ($items as $item) {

            $staffId = isset($item['staff']['id']) ? $item['staff']['id'] * 1 : 0;
            $date = date("Y-m-d 00:00:00", strtotime($item['bookingTime']));
            $bookingTime = strtotime($item['bookingTime']);
            if ($staffId) {
                $sql = "SELECT count(ordi_id) AS cnt FROM " . root_table . "order_item I RIGHT JOIN " . root_table . "order O ON I.ord_id = O.ord_id WHERE ord_deleted=0 AND ord_status != 3 AND ordi_deleted=0 AND ordi_booking_time = $bookingTime AND I.store_id=$storeId AND ordi_staff={$staffId}";

                $staff = staff::getStaff($staffId);
                $maxSlots = $staff ? $staff['user_booking_slots'] * 1 : 0 ;

                $rs = $DB->fetch_data($sql);
                if ($rs[0]['cnt'] >= $maxSlots) {
                    $naItems[] = $item['key'];
                }
            }

            if (!isset($slots[$date][$item['hour']])) {
                $slots[$date][$item['hour']] = [];
            }
            $slots[$date][$item['hour']][] = $staffId;
        }

        foreach ($slots as $date => $slotData) {
            foreach ($slotData as $hour => $id) {
                $totalSlots = self::getSlotsByStoreAndDate($data['store']['id'], strtotime($date), date::hour2Sec($hour));
                if ($totalSlots - count($id) < 0) {
                    $naItems[] = $item['key'];
                }
            }
        }

        $naItems = array_unique($naItems);

        return $naItems;
    }

    /**
     * Lấy danh sách khung giờ và nhân viên trực trong khung giờ theo ngày và theo cửa hàng
     * @param $storeId
     * @param $date
     * @return array
     */
    static function getSlotsByStoreAndDate($storeId, $date, $hour)
    {
        global $DB, $CMS;
        $rs = [];

        //Get slots without booked hours
        $sql = "SELECT staff_id, shift_work_in AS sw_in, IF(shift_work_out = 0, 24 * 3600, shift_work_out) AS sw_out, U.user_booking_slots AS max_slots FROM " . root_table . "work_schedule W LEFT JOIN " . root_table . "shift_work S ON W.shift_work_id = S.shift_work_id LEFT JOIN ". root_table . "user U ON W.staff_id=U.user_id WHERE W.store_id={$storeId} AND work_schedule_date={$date} HAVING sw_in <= $hour AND sw_out >= $hour";

        if ($dataWS = $DB->fetch_data($sql, "work_schedule.shift_work")) {
            foreach ($dataWS as $item) {
                $max_slots = $item['max_slots'] >=1 ? $item['max_slots'] : 1;
                for($i = 1; $i <= $max_slots; $i++) {
                    $rs[] = $item['staff_id'];
                }
            }
        }

        $bookingTime = $date + $hour;

        //Get booked hours
        $sql = "SELECT COUNT(ordi_id) AS cnt FROM " . root_table . "order_item I RIGHT JOIN  " . root_table . "order O ON I.ord_id = O.ord_id WHERE ord_deleted=0 AND ord_status != 3 AND ordi_deleted=0 AND ordi_booking_time = $bookingTime AND I.store_id=$storeId";

        $dataOrder = $DB->fetch_data($sql);

        return count($rs) - ($dataOrder[0]['cnt'] * 1);
    }
}