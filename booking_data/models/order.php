<?php

namespace models;

use core\ezy;
use lib\date;
use lib\input;
use lib\page;

ezy::load_model("customer");
ezy::load_model("product");
ezy::load_model("store");
ezy::load_model("staff");

class order
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

    private static $ordKeys = [
        'ord_id' => 'id',
        'ord_name' => 'name',
        'ord_amount' => 'amount',
        'ord_tax' => 'tax',
        'ord_total' => 'total',
        'ord_status' => 'status',
        'ord_time' => 'time',
        'store_id' => 'storeId',
        'ord_total_discount' => 'discount',
        'ord_booking_phone' => 'phone',
        'ord_commission_rating' => 'rating',
    ];

    private static $ordiKeys = [
        'ordi_name' => 'name',
        'ordi_description' => 'description',
        'ordi_total' => 'total',
        'ordi_staff' => 'staffId',
        'ordi_booking_time' => 'bookingTime'
    ];

    /**
     * @param $data
     */
    static public function add($data)
    {
        global $CMS;
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

        foreach ($data['orderItems'] as $key => $item) {

            $commission_data = $CMS->user->getCommission(input::arrayValue($item['staff'], 'id'));
            if ($commission_data) {
                $commission = input::arrayValue($commission_data, 'id_' . $item['product']['oriData']['id'], []);
            } else {
                $commission = [];
            }

            $product = isset($item['product']) ? $item['product'] : null;

            $product_name[] = isset($product['name']) ? $product['name'] : null;
            $product_id[] = isset($product['id']) ? $product['id'] : null;
            $item_id[] = $key;
            $product_cycle_type[] = 0;
            $product_type[] = isset($product['type']) ? $product['type'] : null;
            $product_description[] = isset($product['description']) ? $product['description'] : null;
            $product_cycle[] = 1;
            $product_quantity[] = 1;
            $product_price[] = isset($product['price']) ? $product['price'] : null;
            $product_old_price[] = isset($product['oldPrice']) ? $product['oldPrice'] : null;
            $product_commission_type[] = input::arrayValue($commission, 'product_commission_type', 0);
            $product_commission_value[] = input::arrayValue($commission, 'product_commission_value', 0);
            $product_tax[] = ($product['tax'] / input::arrayValue($product, 'price', 1)) * 100;
            $product_discount_value[] = 0;
            $product_discount_type[] = 0;
            $product_booking_time[] = strtotime(date('Y-m-d 00:00:00', strtotime($item['date']))) + date::hour2Sec($item['hour']);
            $product_staff[] = input::arrayValue($item['staff'], 'id') * 1;
        }

        $input = [
            'cus_id' => 0,
            'cus_name' => $CMS->class->editor->input(input::arrayValue($data, 'phone'), 'text'),
            'order_payment_method' => input::arrayValue($data, 'paymentMethod') * 1,
            'account_id' => 0,
            'store_id' => $data['store']['id'],
            'user_id' => 0,
            'ord_note' => '',
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
            'total_discount_type' => 0,
            'total_discount_value' => 0,
            'product_booking_time' => $product_booking_time,
            'product_staff' => $product_staff,
            'excess_cash' => 0,
            'ord_note' => $CMS->class->editor->input(input::arrayValue($data, 'note'), 'text'),
            'booking_phone' => $CMS->class->editor->input(input::arrayValue($data, 'phone'), 'text'),
            'service_type' => 1
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
            if (input::arrayValue($data, 'paymentAmount') > 0) {

                //Chuẩn bị đầu vào để tạo receive payment
                $input = [
                    'type' => 1,
                    'sub' => 2,
                    'invoice' => $results['transaction_id'],
                    'cus_type' => 1, //Khách hàng
                    'trx_cus' => $data['customer']['oriData']['name'],
                    'cus_id' => $data['customer']['oriData']['id'],
                    'trx_email' => $data['customer']['oriData']['email'],
                    'trx_payment_date' => date::format(time()),
                    'trx_method' => $data['paymentMethod'] * 1, //0: tiền mặt; 1: chuyển salonản
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
                    $return = [
                        'status' => 'success',
                        'orderId' => $results['ord_id'] * 1,
                        'msg' => 'Qúy khách đã đặt chỗ thành công',
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
                    'orderId' => $results['ord_id'] * 1,
                    'msg' => 'Qúy khách đã đặt chỗ thành công',
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

        $items = $CMS->transactions->getItemAll($data['trx_id'], "all", 1);
        $customer = $CMS->customer->getInfo($data['cus_id']);
        $data = self::convertKeys(self::$trxKeys, $data);

        $data['timestamp'] = $data['time'] * 1;
        $data['time'] = date::format($data['time']);
        $data['id'] *= 1;
        $data['amount'] *= 1;
        $data['invoiceNo'] *= 1;
        $data['tax'] *= 1;
        $data['total'] *= 1;
        $data['excessCash'] *= 1;
        $data['discountTotal'] *= 1;
        $data['paymentAmount'] *= 1;

        $oriData = $data;
        $data['status'] = $CMS->lang['trx_status_0' . $data['status']];
        $data['oriData'] = $oriData;

        //Convert keys of item
        if ($items) {
            foreach ($items as $kItem => $item) {
                $item = self::convertKeys(self::$triKeys, $item);
                $item['taxType'] = 0; //percent
                $product = product::getProduct($item['productId']);
                $product = $product ? product::convertToDisplay($product) : [];
                $item['product'] = $product;

                $items[$kItem] = $item;
            }
            $data['items'] = $items;
        }

        if ($customer) {
            $customer = customer::convertToDisplay($customer);
            $data['customer'] = $customer;
        }

        return $data;
    }

    static function getBookedHours($storeId, $sql_add = "")
    {
        global $DB;

        $rs = [];

        if (!$storeId) return $rs;

        $sql = "SELECT ordi_booking_time, ordi_staff FROM " . root_table . "order_item I RIGHT JOIN  " . root_table . "order O ON I.ord_id = O.ord_id WHERE {$sql_add} ord_deleted=0 AND ord_status != 3 AND ordi_deleted=0 AND I.store_id=$storeId";

        $data = $DB->fetch_data($sql);

        foreach ($data as $item) {
            $rs[] = [
                'date' => date('Y-m-d', $item['ordi_booking_time']),
                'hour' => date('H : i', $item['ordi_booking_time']),
                'staff' => ['id' => $item['ordi_staff'] * 1 ? $item['ordi_staff'] * 1 : null]
            ];
        }

        return $rs;
    }

    static function checkBookedHours($data)
    {
        global $DB;
        $orderItems = $data['orderItems'];
        $storeId = $data['store'] ['id'];

        $naItems = []; //các item không khả dụng
        $slots = [];

        foreach ($orderItems as $item) {

            $staffId = isset($item['staff']['id']) ? $item['staff']['id'] * 1 : 0;
            $date = date("Y-m-d 00:00:00", strtotime($item['date']));
            $bookingTime = strtotime($date) + \lib\date::hour2Sec($item['hour']);
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

    static function cancel($phone, $orderId)
    {
        global $CMS, $DB;

        $CMS->class->language->load("order", "admin_");

        $status = 'success';
        $msg = 'Hủy đơn hàng thành công';

        $orderId *= 1;
        $phone = trim($phone);

        if (!$phone || !$orderId) {
            $status = 'fail';
            $msg = "Thông tin không hợp lệ";
        } else {
            $sql = "SELECT * FROM " . root_table . "order WHERE ord_id={$orderId} AND ord_booking_phone='{$phone}' AND ord_deleted=0 LIMIT 1";

            $data = $DB->fetch_data($sql);

            if (count($data)) {
                $ord = $data[0];

                if ($ord['ord_status'] == 1) {
                    $status = 'fail';
                    $msg = "Không thể hủy lịch đặt này";
                } else if ($ord['ord_status'] == 3) {
                    $status = 'fail';
                    $msg = "Lịch đặt này đã hủy trước đó";
                } else if ($ord['ord_status'] == 2 || $ord['ord_status'] == 0) {
                    if (!$CMS->order->cancel_order($orderId, 3)) {
                        $status = 'fail';
                        $msg = "Lỗi! " . input::arrayValue($_SESSION, 'error_msg', "");
                        unset($_SESSION['error_msg']);
                    }
                } else {
                    $status = 'fail';
                    $msg = "Trạng thái không hợp lệ";
                }
            } else {
                $status = 'fail';
                $msg = "Không tìm thấy thông tin";
            }
        }

        return ['status' => $status, 'msg' => $msg];
    }

    /**
     * @param string $sql_add
     * @return array
     */
    static function getAll($sql_add = "")
    {
        global $CMS, $DB;

        $langBK = $CMS->lang;

        $CMS->class->language->load("order", "admin_");

        $return = [];

        $sql = "SELECT * FROM " . root_table . "order O LEFT JOIN " . root_table . "order_item OI ON O.ord_id = OI.ord_id WHERE {$sql_add} ord_deleted = 0 AND service_type=1 ORDER BY O.ord_id DESC";

        $rs = $DB->fetch_data($sql);

        foreach ($rs as $k => $ord) {
            if(!isset($return[$ord['ord_id']])) {
                $store = store::getStore($ord['store_id']);
                $return[$ord['ord_id']] = self::convertKeys(self::$ordKeys, $ord);

                //Convert to display
                $return[$ord['ord_id']]['data_bk'] = $return[$ord['ord_id']];
                $return[$ord['ord_id']]['store'] = $store;
                $return[$ord['ord_id']]['status'] = $CMS->lang['ord_status_'.$return[$ord['ord_id']]['status']];
                $return[$ord['ord_id']]['time'] = date::format($return[$ord['ord_id']]['time'], $CMS->vars['dateformat_php'][$CMS->vars['date_format']] . ' H:i');
                $return[$ord['ord_id']]['items'] = [];
            }

            if(isset($return[$ord['ord_id']])){
                $staff = staff::getStaff($ord['ordi_staff']);
                $staff = staff::convertKeys($staff);
                $item = self::convertKeys(self::$ordiKeys, $ord);

                //Convert to display
                $item['data_bk'] = $item;
                $item['staff'] = $staff;
                $item['bookingTime'] = date::format($item['bookingTime'], $CMS->vars['dateformat_php'][$CMS->vars['date_format']] . ' H:i');

                $return[$ord['ord_id']]['items'][] = $item;
            }
        }

        $CMS->lang = $langBK;

        return array_values($return);
    }

    static function getOrder($ord_id, $sqlAdd = "")
    {
        global $DB;
        $ord_id *= 1;
        $sql = "SELECT * FROM " . root_table . "order WHERE {$sqlAdd} ord_id = {$ord_id} AND ord_deleted = 0 LIMIT 1";

        $rs = $DB->fetch_data($sql);

        return input::arrayValue($rs, 0 , null);
    }

    /**
     * Rate & complete order
     * @param $ord_id
     * @param $rate
     * @return array
     */
    static function rate($ord_id, $rate)
    {
        global $CMS, $DB;
        $ord_id *= 1;
        $rate *= 1;

        $CMS->input['id'] = $ord_id;
        $CMS->input['comment_content'] = "Hoàn thành đơn hàng từ trang Booking";
        $CMS->input['ord_status'] = 2; //Hoàn thành

        $order = self::getOrder($ord_id);

        if (!$order) {
            $rs = [
                'status' => 'success',
                'msg' => 'Không tìm thấy thông tin'
            ];
        } else {

            if ($order['ord_status'] == 3) {
                $rs = [
                    'status' => 'fail',
                    'msg' => 'Đơn hàng này đã bị hủy trước đó'
                ];
            } else {
                if ($CMS->order->approve_order("paid")) {
                    if ($CMS->order->updatelog()) {

                        if ($DB->update('order', ['ord_id' => $ord_id, 'ord_commission_rating' => $rate], 'ord_id')) {
                            $rs = [
                                'status' => 'success',
                                'msg' => 'Đánh giá thành công'
                            ];
                        } else {
                            $rs = [
                                'status' => 'fail',
                                'msg' => 'Lỗi đánh giá'
                            ];
                        }

                    } else {
                        $rs = [
                            'status' => 'fail',
                            'msg' => 'Lỗi cập nhật trạng thái'
                        ];
                    }
                } else {
                    $rs = [
                        'status' => 'fail',
                        'msg' => 'Lỗi xác nhận đơn hàng'
                    ];
                }
            }
        }

        unset($_SESSION['msg']);
        unset($_SESSION['error_msg']);

        return $rs;
    }
}