<?php
/**
 * Created by PhpStorm.
 * User: PhuongNKV
 * Date: 9/26/2018
 * Time: 2:58 PM
 */

namespace CheckIn\Model;

use core\ezy;
use lib\input;
use lib\security;
use models\staff;

ezy::load_model('staff', 'booking_data');

class order
{
    /**
     * Để chuyển tên field gốc của order sang dạng rút gọn
     * @var array
     */
    public static $ordKeys = [
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
        'ord_rating_status' => 'ratingStatus',
        'ord_booking_phone' => 'phone'
    ];

    /**
     * Để chuyển tên field gốc của order item sang dạng rút gọn
     * @var array
     */
    public static $ordiKeys = [
        'ordi_name' => 'name',
        'ordi_description' => 'description',
        'ordi_total' => 'total',
        'ordi_price' => 'price',
        'ordi_staff' => 'staffId',
        'ordi_booking_time' => 'bookingTime'
    ];

    /**
     * Để xác định field nào là field dạng số
     * @var array
     */
    public static $fieldsNumber = ['ord_id', 'ord_amount', 'ord_tax', 'ord_total', 'ord_status', 'ord_time', 'store_id', 'ord_total_discount', 'ord_commission_rating', 'ord_rating_status', 'ordi_total', 'ordi_staff', 'ordi_booking_time'];

    public static $fieldsMoney = ['amount', 'tax', 'total', 'discount', 'price'];

    public static $fieldsTime = ['time', 'bookingTime'];

    public static $fieldsPhone = ['phone'];

    /**
     * Chuyển nhóm key của 1 mảng sang tên khác
     * @param $result
     */
    static public function convertKeys($keys, $data)
    {
        $return = [];

        foreach ($data as $key => $value) {
            if (isset($keys[$key])) {
                if (in_array($key, self::$fieldsNumber)) {
                    $value *= 1;
                }
                $return[$keys[$key]] = $value;
            }
        }

        return $return;
    }

    /**
     * Đảo ngược trở lại key gốc
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
     * @param $data
     * @return mixed
     */
    static function convertData($data)
    {
        global $CMS;

        $data['data_bk'] = $data_bk = $data;

        foreach ($data_bk as $k => $v) {
            if (in_array($k, self::$fieldsNumber)) {
                $v *= 1;
            }

            if (in_array($k, self::$fieldsMoney)) {
                $v = $CMS->class->input->currency($v);
            } else if (in_array($k, self::$fieldsTime)) {
                $v = date('d/m/Y H:i', $v);
            } else if (in_array($k, self::$fieldsPhone)) {
                $v = input::phoneNumber($v);
            }

            $data[$k] = $v;
        }

        return $data;
    }

    /**
     * @param $data
     * @return array
     */
    static function convertOrder($data)
    {
        global $CMS;

        $data = self::convertData($data);

        if (count($data['items'])) {
            $data['items'] = array_map(function ($item) {
                return self::convertItem($item);
            }, $data['items']);
        }

        return $data;
    }

    /**
     * @param $data
     * @return array
     */
    static function convertItem($data)
    {
        global $CMS;

        $data = self::convertData($data);

        $staff = staff::getStaff($data['data_bk']['staffId']);
        $data['staff'] = $staff ?  staff::convertKeys($staff) : null;

        return $data;
    }

    /**
     * Lấy thông tin 1 đơn hàng theo id
     * @param $ord_id
     * @param string $sqlAdd
     * @return mixed
     */
    static function getOrder($ord_id, $sqlAdd = "")
    {
        global $DB;
        $ord_id *= 1;
        $sql = "SELECT * FROM " . root_table . "order WHERE {$sqlAdd} ord_id = {$ord_id} AND ord_deleted = 0 LIMIT 1";

        $rs = $DB->fetch_data($sql);

        return self::convertKeys(self::$ordKeys, input::arrayValue($rs, 0, null));
    }

    /**
     * Lấy danh sách order item
     * @param string $sqlAdd
     * @return array
     */
    static function getItems($sqlAdd = "")
    {
        global $DB;

        $sql = "SELECT * FROM " . root_table . "order_item WHERE {$sqlAdd} ordi_deleted = 0";

        $rs = $DB->fetch_data($sql);
        if (count($rs)) {
            $rs = array_map(function ($item) {
                return self::convertKeys(self::$ordiKeys, $item);
            }, $rs);
        }

        return $rs;
    }

    /**
     * Kiểm tra tính hợp lệ và lấy thông tin của order + items
     * @param $order_id
     * @return array
     */
    static function checkRating($order_id)
    {
        global $DB, $CMS;

        $order = \CheckIn\Model\order::getOrder($order_id);

        $langBK = $CMS->lang;
        $CMS->class->language->load("order", "admin_");

        if ($order) {

            /*if ($order['status'] != 2) { //Hoàn thành*/
                if ($order['ratingStatus'] == 1) {
                    $return = [
                        'status' => 'failed',
                        'msg' => input::lang('rated_alrealy'),
                    ];
                } else {

                    $order['items'] = self::getItems("ord_id={$order_id} AND");

                    $return = [
                        'status' => 'success',
                        'data' => self::convertOrder($order),
                    ];
                }
            /*} else {
                $return = [
                    'status' => 'failed',
                    'msg' => input::lang('invalid_for_rating'),
                ];
            }*/
        } else {
            $return = [
                'status' => 'failed',
                'msg' => input::lang('no_data'),
            ];
        }

        $CMS->lang = $langBK;
        return $return;
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
        $CMS->input['comment_content'] = "Hoàn thành đơn hàng từ trang Checkin";
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
            } elseif ($order['ord_rating_status'] == 1) {
                $rs = [
                    'status' => 'fail',
                    'msg' => 'Đơn hàng này đã đánh giá rồi'
                ];
            }
            else {
                if ($CMS->order->approve_order("paid")) {
                    if ($CMS->order->updatelog()) {

                        if ($DB->update('order', ['ord_id' => $ord_id, 'ord_commission_rating' => $rate, 'ord_rating_status' => 1], 'ord_id')) {
                            $rs = [
                                'data' => self::getOrder($ord_id),
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