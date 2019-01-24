<?php
/**
 * Created by PhpStorm.
 * User: Phuong3F
 * Date: 5/23/2017
 * Time: 9:44 AM
 */

namespace models;

use core\ezy;
use lib\date;
use lib\input;
use lib\db;
use \lib\template;

ezy::load_model('cart');

class discount
{
    static $cache_prefix = 'discount';

    static function checkCode($code)
    {
        global $CMS, $DB, $member;

        $data = self::getCodeInfo($code);

        $return = [];

        $time = time();

        if(!$data)
        {
            $return['msg'] = "Code not found";
        }
        else
        {
            if($data['di_apply_for'] != 'all'){

                $apply_rules = @json_decode($data['di_apply_rules'], 1);

                if($data['di_apply_for'] == 'customer_group')
                {
                    if(!$member)
                    {
                        $return['msg'] = "Please login to continue";
                    }
                    else if(!in_array($member['cus_group'], $apply_rules))
                    {
                        $return['msg'] = "You cannot use this code";
                    }
                }
                else if($data['di_apply_for'] == 'amount_from')
                {
                    $amount = \models\cart::cartTotal(1,"");

                    if($amount[2] < $apply_rules)
                    {
                        $return['msg'] = "Total amount have to over ".$CMS->class->input->currency($apply_rules);
                    }
                }
            }

            if($data['di_start_time'] && $data['di_start_time'] > $time)
            {
                $return['msg'] = "This code is only use after ".date::format($data['di_start_time']);
            }
            else if($data['di_end_time'] && $data['di_end_time'] < $time)
            {
                $return['msg'] = "This code expired";
            }
            else if($data['di_times'] !=0 && $data['di_used_times'] >= $data['di_times'])
            {
                $return['msg'] = "This code used already";
            }
        }

        if(isset($return['msg']) && $return['msg'])
        {
            $return['status'] = 'fail';
        }
        else
        {
            $_SESSION['discount_code'] = [
                'code' => $data['di_code'],
                'apply_for' => $data['di_apply_for'],
                'apply_rules' => isset($apply_rules)?$apply_rules:[],
                'type' => $data['di_type'],
                'value' => $data['di_value'],
            ];

            $return['status'] = 'ok';
            $return['code_data'] = $_SESSION['discount_code'];
            $return['cart_data'] = cart::cartTotal(1);
        }

        return $return;
    }


    static function checkCode_dsg($code)
    {
        global $CMS, $DB, $member;

        $data = self::getCodeInfo($code);

        $return = [];

        $time = time();

        if(!$data)
        {
            $return['msg'] = "Mã không tìm thấy!";
        }
        else
        {
            if($data['di_apply_for'] != 'all'){

                $apply_rules = @json_decode($data['di_apply_rules'], 1);

                if($data['di_apply_for'] == 'customer_group')
                {
                    if(!$member)
                    {
                        $return['msg'] = "Vui lòng đăng nhập để tiếp tục!";
                    }
                    else if(!in_array($member['cus_group'], $apply_rules))
                    {
                        $return['msg'] = "Bạn không thể sử dụng mã này!";
                    }
                }
                else if($data['di_apply_for'] == 'amount_from')
                {
                    $amount = \models\cart::cartTotal(1,"");

                    if($amount[2] < $apply_rules)
                    {
                        $return['msg'] = "Total amount have to over ".$CMS->class->input->currency($apply_rules);
                    }
                }
            }

            if($data['di_start_time'] && $data['di_start_time'] > $time)
            {
                $return['msg'] = "Mã được sử dụng sau ngày ".date::format($data['di_start_time']);
            }
            else if($data['di_end_time'] && $data['di_end_time'] < $time)
            {
                $return['msg'] = "Mã Code đã hết hạn";
            }
            else if($data['di_times'] !=0 && $data['di_used_times'] >= $data['di_times'])
            {
                $return['msg'] = "Mã Code đã được sử dụng";
            }
        }

        if(isset($return['msg']) && $return['msg'])
        {
            $return['status'] = 'Lỗi!';
        }
        else
        {
            $_SESSION['discount_code'] = [
                'code' => $data['di_code'],
                'apply_for' => $data['di_apply_for'],
                'apply_rules' => isset($apply_rules)?$apply_rules:[],
                'type' => $data['di_type'],
                'value' => $data['di_value'],
            ];

            $return['status'] = 'ok';
            $return['code_data'] = $_SESSION['discount_code'];
            $return['cart_data'] = cart::cartTotal(1);
        }

        return $return;
    }

    /**
     * @param $code
     * @return mixed
     */
    static function getCodeInfo($code)
    {
        global $DB, $CMS;

        $sql = "SELECT * FROM ".root_table."discount_items WHERE di_code='{$code}' AND di_deleted=0 LIMIT 1";

        $data = $DB->fetch_data($sql, 'discount_items');

        return isset($data[0])?$data[0]:null;
    }

    static function updateUsed($ord_id = 0)
    {
        global $CMS, $DB;

        //update used times
        $code = isset($_SESSION['discount_code']['code']) ? $_SESSION['discount_code']['code'] : null;
        $sql = "UPDATE ".root_table."discount_items SET di_used_times = di_used_times+1 WHERE di_code='{$code}'";
        $DB->query($sql);

        //delete cache discount code
        $CMS->class->cache->mdelete('discount_items');

        //update info dicount code for order
        $discount_code = isset($_SESSION['discount_code']) ? $_SESSION['discount_code'] : [];
        $discountInfo = input::jsonEncode($discount_code,0);
        $sql = "UPDATE ".root_table."order SET ord_discount_info='{$discountInfo}' WHERE ord_id='{$ord_id}'";
        $DB->query($sql);

        unset($_SESSION['discount_code']);

        return true;
    }
}