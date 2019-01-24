<?php

namespace models;

use core\ezy;
use lib\input;
//Load model
ezy::load_model("product");

class cart
{
    /**
     * Get List tag
     * @return array
     */

    static function addCart($product_id=0)
    {
        global $DB, $CMS;

        // Check product id
        $check = product::checkValidProduct($product_id);
        
        if(!$check)
        {
            return self::createMsg("This is product invalid!");
        }

        $_SESSION['curr_info']['pid'] = $product_id;
        $CMS->input['quantity'] = !empty($CMS->input['quantity']) ? $CMS->input['quantity'] : 1;

        // Check exist product
        if(isset($_SESSION['mycart'][$product_id]) && count($_SESSION['mycart'][$product_id]) > 0)
        {
            $_SESSION['mycart'][$product_id]['quantity']= $CMS->input['quantity'] ? $_SESSION['mycart'][$product_id]['quantity']+$CMS->input['quantity'] : $_SESSION['mycart'][$product_id]['quantity']+1;
            $_SESSION['mycart'][$product_id]['total'] = $_SESSION['mycart'][$product_id]['quantity'] * ($_SESSION['mycart'][$product_id]['price'] + $_SESSION['mycart'][$product_id]['price_add']);
            $_SESSION['mycart'][$product_id]['total_show'] = $CMS->class->input->currency($_SESSION['mycart'][$product_id]['total']);
        }else
        {
             // get Data product
            $data = product::getInfo($product_id,"product_id, product_image, product_description, product_code, product_price, product_price_sell, product_name, product_cycle, product_shorturl, product_tax, product_group, product_price_old");
            
            // convert product
            $data = self::convertdata($data);
            $data['quantity'] = !empty($CMS->input['quantity'])?$CMS->input['quantity']:1;
            $data['total'] = $data['price']*$data['quantity'];
            $data['price_new'] = $data['price'];
            $data['price_new_show'] = $CMS->class->input->currency($data['price']);
            $data['price_add'] = 0; // Mới add cart thì price_add = 0;
            $data['total_show'] = $CMS->class->input->currency($data['total']);
            // set for session
            $_SESSION['mycart'][$product_id] = $data;
        }
       

        // return
        // return $_SESSION['mycart'];
    }

    static function createMsg($message="",$status="error")
    {
        if($status == "error")
        {
            $_SESSION['error_msg'] = $message;
            return false;
        }else
        {
            $_SESSION['msg'] = $message;
            return true;
        }
    }

    static function convertdata($data=[])
    {
        global $CMS;

        $data['uploadPath'] = "product/{$data['product_image']}";
        $data['image'] = input::checkImage($data['uploadPath']);
        $data['price'] = $data['product_price_sell'] ? $data['product_price_sell'] : $data['product_price'];
        $data['price_show'] = $CMS->class->input->currency($data['price']);

        // convert url
        $data['url_none_html'] = "/{$data['product_shorturl']}-sp{$data['product_id']}";
        $data['url'] ="/{$data['product_shorturl']}-p{$data['product_id']}.html";
        
        return $data;
    }

    /*static function cartTotal($type = 0, $format = 'currency')
    {
        global $CMS;
        $amount = 0;
        foreach ($_SESSION['mycart'] as $key => $data) 
        {
            $amount += ($data['price']+$data['price_add']) * $data['quantity'];
        }

        if($type)
        {
            $vat = 0;
            // get tax product
            foreach ($_SESSION['mycart'] as $key => $data) 
            {
                $vat += $data['product_tax'] ? (($data['price']+$data['price_add']) * $data['quantity'])*$data['product_tax']/100 : 0;
            }
            $_SESSION['total_tax'] = $vat;
            // Tạm thời set shipping ở đây
            $ship_fee = 0;// $CMS->vars['shipping_fee'] ? $CMS->vars['shipping_fee'] : "5";

            // Sub-total
            $subtotal = $amount;
            // Total final
            $amount += $ship_fee + $vat;

            // return
            // return
            if($format == 'currency')
            {
                return array($CMS->class->input->currency($ship_fee), $CMS->class->input->currency(round($vat,2)), $CMS->class->input->currency($subtotal), $CMS->class->input->currency($amount), $vat);
            }
            else
            {
                return array($ship_fee,$vat, $subtotal, $amount, $vat);
            }
        }

        // return
        if($format == 'currency')
        {
            return $CMS->class->input->currency($amount);
        }
        else
        {
            return $amount;
        }
    }*/

    static function cartTotal($type = 0, $format = 'currency')
    {
        global $CMS, $member;

        $subtotal = 0; //Tien goc chua bao gom thue, giam gia v.v...
        $amount = 0; //Tien thuc te
        $discount = 0; //Tien giam gia
        $tax = 0; //thue
        $ship_fee = 0;

        $inputData = self::convertToCalculate(isset($_SESSION['mycart']) ? $_SESSION['mycart'] : null);

        $calResult = $CMS->transactions->calculate($inputData);

        if($calResult)
        {
            $subtotal = $calResult['subtotal'];
            $amount = $calResult['total'];
            $discount = $calResult['discount'];
            $tax = $calResult['tax'];
        }


        $amount += $ship_fee;

        $taxOri = $tax;
        $discountOri = $discount;

        if($format == 'currency')
        {
            $subtotal = $CMS->class->input->currency($subtotal);
            $amount = $CMS->class->input->currency($amount);
            $discount = $CMS->class->input->currency($discount);
            $tax = $CMS->class->input->currency(round($tax, 2));
            $ship_fee = $CMS->class->input->currency($ship_fee);
        }

        if($type)
        {
            return array($ship_fee,$tax, $subtotal, $amount, $taxOri, $discount, $discountOri);
        }
        else
        {
            return $amount;
        }
    }

    static function updateCart($quantity=0,$product_id=0)
    {
        global $CMS;

        if($product_id and $quantity)
        {
            $cus_price = $_SESSION['mycart'][$product_id]['price'] + $_SESSION['mycart'][$product_id]['price_add'];
            $_SESSION['mycart'][$product_id]['price_new'] = $cus_price;
            $_SESSION['mycart'][$product_id]['price_new_show'] = $CMS->class->input->currency($cus_price);

            $_SESSION['mycart'][$product_id]['quantity'] = $quantity;
            // Change total
            $_SESSION['mycart'][$product_id]['total'] = $quantity * $cus_price;
            $_SESSION['mycart'][$product_id]['total_show'] = $CMS->class->input->currency($_SESSION['mycart'][$product_id]['total']);
            // Change amount
            $cart_data = self::cartTotal(1);
            $amount = $cart_data[3];
            return array($_SESSION['mycart'][$product_id]['total_show'], $amount, $cart_data);
        }else
        {
            return [null,null,null];
        }


    }

    static function delItem($product_id=0)
    {
        global $CMS;

        if($product_id)
        {
            unset($_SESSION['mycart'][$product_id]);
            // Change amount
            $data = self::cartTotal(1);
            return $data;
        }else
        {
            return [];
        }


    }

    static function updatePrice($cus_price=0,$product_id=0)
    {
        global $CMS;

        if($product_id and $cus_price)
        {
            if($cus_price < $_SESSION['mycart'][$product_id]['price'])
            {
                return array("status" => "error", "msg" => "Min price is {$_SESSION['mycart'][$product_id]['price']} USD", "price" => $_SESSION['mycart'][$product_id]['price_new']);
            }

            if($cus_price > $CMS->vars['giftcard_max_price'])
            {
                return array("status" => "error", "msg" => "Max price is {$CMS->vars['giftcard_max_price']} USD", "price"=>$_SESSION['mycart'][$product_id]['price_new']);
            }

            $_SESSION['mycart'][$product_id]['price_new'] = $cus_price;
            $_SESSION['mycart'][$product_id]['price_new_show'] = $CMS->class->input->currency($cus_price);
            $_SESSION['mycart'][$product_id]['price_add'] = $cus_price - $_SESSION['mycart'][$product_id]['price'];
            // Change total
            $_SESSION['mycart'][$product_id]['total'] = $_SESSION['mycart'][$product_id]['quantity'] * ($_SESSION['mycart'][$product_id]['price']+$_SESSION['mycart'][$product_id]['price_add']);
            
            $_SESSION['mycart'][$product_id]['total_show'] = $CMS->class->input->currency($_SESSION['mycart'][$product_id]['total']);
// print "<pre>"; print_r($_SESSION['mycart']); exit;
            // Change amount
            $data = self::cartTotal(1);
            return array('total_show' => $_SESSION['mycart'][$product_id]['total_show'], 'amount' => $data[3], "status" => "success", 'cart_data' => $data);
        }else
        {
            return [];
        }
    }

    function cartCalculate($data)
    {
        global $CMS, $member;

        $tmpSubTotal =  ($data['price']+$data['price_add']) * $data['quantity'];
        $tmpAmount = 0;
        $tmpDiscount = 0;
        $tmpTax = 0;

        if(!($CMS->vars['discount_code']))
        {
            unset($_SESSION['discount_code']);
        }
        else
        {
            $discountApplyFor = $_SESSION['discount_code']['apply_for'];
            $discountApplyRules = $_SESSION['discount_code']['apply_rules'];
            $discountType = $_SESSION['discount_code']['type'];
            $discountValue = $_SESSION['discount_code']['value'];
        }

        //calculate discount
        if($_SESSION['discount_code'])
        {
            if(($discountApplyFor == 'product' && in_array($data['product_id'], $discountApplyRules))
                || ($discountApplyFor == 'product_group' && in_array($data['product_group'], $discountApplyRules))
                || $discountApplyFor == 'all'
                || ($discountApplyFor == 'customer_group' && in_array($member['cus_id'], $discountApplyRules)))
            {
                if($discountType == 0) //%
                {
                    $tmpDiscount = ($tmpSubTotal * $discountValue)/100;
                }
                else if ($discountType == 1)
                {
                    $tmpDiscount = $discountValue * $data['quantity'];
                }
            }
        }

        $tmpAmount = $tmpSubTotal - $tmpDiscount;
        $tmpAmount = $tmpAmount <=0 ? 0 : $tmpAmount;

        $tmpTax = ($tmpAmount * $data['product_tax']) / 100;
        $tmpAmount = $tmpAmount + $tmpTax;

        $return = [
            'tmpSubTotal' => $tmpSubTotal,
            'tmpAmount' => $tmpAmount,
            'tmpTax' => $tmpTax,
            'tmpDiscount' => $tmpDiscount
        ];
        // tax for payment
        $_SESSION['total_tax'] = $tmpTax;

        return $return;
    }

    static function convertToCalculate($data = [], $useCode = 1)
    {
        global $CMS, $member;

        if(!$data) return false;

        $discountApplyFor = isset($_SESSION['discount_code']['apply_for']) ? $_SESSION['discount_code']['apply_for'] : null;
        $discountApplyRules = isset($_SESSION['discount_code']['apply_rules']) ? $_SESSION['discount_code']['apply_rules']: null;
        $discountType = isset($_SESSION['discount_code']['type']) ? $_SESSION['discount_code']['type'] : null;
        $discountValue = isset($_SESSION['discount_code']['value']) ? $_SESSION['discount_code']['value'] : null;

        if(!$CMS->vars['discount_code'])
        {
            unset($_SESSION['discount_code']);
        }

        $return = [];

        $arr = [
            'product_name' => 'product_name',
            'product_id' => 'product_id',
            'product_description' => 'product_description',
            'quantity' => 'product_quantity',
            'price' => 'product_price',
            'price_add' => 'product_price_add',
            'product_tax' => 'product_tax',
        ];

        $cntValid = 0; //Đếm số item được hưởng KM

        foreach ($data as $item)
        {
            foreach ($arr as $k => $v)
            {
                $return[$v][] = $item[$k];
            }

            $return['product_discount_type'][] = $discountType;

            //calculate discount
            if(!empty($_SESSION['discount_code']) && $useCode)
            {
                if(($discountApplyFor == 'product' && in_array($item['product_id'], $discountApplyRules))
                    || ($discountApplyFor == 'product_group' && in_array($item['product_group'], $discountApplyRules))
                    || $discountApplyFor == 'all'
                    || $discountApplyFor == 'amount_from'
                    || ($discountApplyFor == 'customer_group' && in_array($member['cus_id'], $discountApplyRules)))
                {
                    $cntValid++;

                    if($discountApplyFor == 'amount_from')
                    {
                        $inputData = self::convertToCalculate($_SESSION['mycart'],0);
                        $calResult = $CMS->transactions->calculate($inputData);

                        if($calResult['subtotal'] < $discountApplyRules)
                        {
                            /**
                             * Nếu tổng giá đơn hàng nhỏ hơn giá sàn thì xóa luôn mã KM
                             */
                            unset($_SESSION['discount_code']);
                            $return['product_discount_value'][] = 0;
                        }
                        else
                        {
                            $return['product_discount_value'][] = $discountValue;
                        }
                    }
                    else
                    {
                        $return['product_discount_value'][] = $discountValue;
                    }
                }
                else
                {
                    $return['product_discount_value'][] = 0;
                }
            }
            else
            {
                $return['product_discount_value'][] = 0;
            }
        }

        if($discountType == 1 && $cntValid) //KM theo số tiền thì chia đều tiền cho tất cả các item hop le
        {
            foreach($return['product_discount_value'] as $k => $v)
            {
                $return['product_discount_value'][$k] =  $v/$cntValid;
            }
        }

        //Check discount code
        return $return;
    }

    static function changeProduct()
    {
        global $CMS, $DB;

        // Xoá giỏ hàng
        unset($_SESSION['mycart']);
        // Add lại giỏ hàng
        $product_id = intval($CMS->input['pid']);
        self::addCart($product_id);
        $_SESSION['curr_info']['type_page']=1;
        // $_SESSION['mycart'][$product_id]['price'] = isset($_SESSION['curr_info']['input']['custom_price']) ? $_SESSION['curr_info']['input']['custom_price'] : $_SESSION['mycart'][$product_id]['price'];

        // print "<pre>"; print_r($_SESSION['curr_info']);exit;
        // Change amount
        $data = self::cartTotal(1);

        //$ship_fee,$tax, $subtotal, $amount, $taxOri, $discount
        // print "<pre>"; print_r($data); print_r($_SESSION['discount_code']);exit;
        return array("ship_fee" => $data[0], "tax" => $data[1], "subtotal" => $data[2], "amount" => $data[3], "taxOri" => $data[4], "discount" => $data[5], "discount_code" => $_SESSION['discount_code']['code']);
    }
}