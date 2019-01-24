<div class="row">
    <div class="col-md-12">
        <div class="mycarttable">
            <div class="mycarttable-inner table-responsive">
                <table class="table list-row">
                    <thead>
                        <tr>
                            <th class="th-name"><?=$CMS->lang['cart_item'];?></th>
                            <th class="th-qty"><?=$CMS->lang['cart_quantity'];?></th>
                            <th class="th-price-item"><?=$CMS->lang['cart_price'];?></th>
                            <th class="th-price-total"><?=$CMS->lang['cart_total'];?></th>
                            <th class="th-remove">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody class="step1">
                        <!--Check cart-->
                        <?if( empty($tpl->mycart) ){?>
                        <tr>
                            <td colspan="5">
                                <div class="list-row-col">
                                    <p>Cart empty... </p>
                                </div>
                            </td>
                        </tr>

                        <?}else{ foreach( $tpl->mycart as $key => $data ){?>
                        <tr>
                            <td>
                                <div class="list-row-col">
                                    <img class="image-item" src="<?=\lib\input::getThumb($data['uploadPath'],100);?>" alt="<?=$data['product_name'];?>" />
                                    <p class="name-item"><?=$data['product_name'];?></p>
                                </div>
                            </td>
                            <td>
                                <div class="list-row-col">
                                    <input class="form-control" type="number" min="1" value="<?=$data['quantity'];?>" cart_id="<?=$key;?>" onkeyup="update_cart(this);" onchange="update_cart(this);"/>
                                </div>
                            </td>
                            <td>
                                <div class="list-row-col">
                                    <?if( $CMS->vars['giftcard_buy_custom'] ){?>
                                    <input class="form-control autowidth list_price" type="text" name="custom_price" min="<?=$data['price'];?>" max="<?=$CMS->vars['giftcard_max_price'];?>" value="<?=$data['price_new']?>" cart_id="<?=$key;?>"  onkeyup="update_price(this);" onchange="update_price(this);"/>
                                    <p class="small-sm">You can set any value between <?=$data['price'];?> and <?=$CMS->vars['giftcard_max_price'];?> USD</p>

                                    <?}else{?>
                                    <p class="price-item"><?=$data['price_show'];?></p>
                                    <?}?>
                                </div>
                            </td>
                            <td>
                                <div class="list-row-col">
                                    <p class="total_change price-total-item"><?=$data['total_show'];?></p>
                                </div>
                            </td>
                            <td class="td-remove">
                                <div class="list-row-col">
                                    <span class="list_stt" style="display:none;"></span>
                                    <a onclick="delItem(this);" cart_id="<?=$key;?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?}}?>
                    </tbody>
                </table>
            </div>
            <div class="mycarttable-inner">
                <table class="table price-row">
                    <tbody>
                        <!--Cart Amount-->
                        <?if( $CMS->vars['discount_code'] ){?>
                        <tr>
                            <td>
                                <div class="price-row-col">
                                    <p><?=$CMS->lang['cart_subtotal'];?>:</p>
                                </div>
                            </td>
                            <td>
                                <div class="price-row-col">
                                    <p id="cart_subtotal"><?=$tpl->cart_subtotal;?></p>
                                </div>
                            </td>
                            <td class="td-remove">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="price-row-col">
                                    <p><?=$CMS->lang['cart_discount'];?>:</p>
                                </div>
                            </td>
                            <td>
                                <div class="price-row-col">
                                    <p id="cart_discount_code_value"><?=$tpl->cart_discount;?></p>
                                </div>
                            </td>
                            <td class="td-remove">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="price-row-col">
                                    <p><?=$CMS->lang['cart_tax'];?>:</p>
                                </div>
                            </td>
                            <td>
                                <div class="price-row-col">
                                    <p id="cart_tax"><?=$tpl->cart_tax;?></p>
                                </div>
                            </td>
                            <td class="td-remove">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="price-row-col">
                                    <p><?=$CMS->lang['cart_total'];?>:</p>
                                </div>
                            </td>
                            <td>
                                <div class="price-row-col">
                                    <p id="cart_payment_total"><?=$tpl->amount;?></p>
                                </div>
                            </td>
                            <td class="td-remove">&nbsp;</td>
                        </tr>

                        <?}else{?>
                        <?if( $tpl->cart_taxOri ){?>
                        <tr>
                            <td>
                                <div class="price-row-col">
                                    <p><?=$CMS->lang['cart_tax'];?>:</p>
                                </div>
                            </td>
                            <td>
                                <div class="price-row-col">
                                    <p id="cart_tax"><?=$tpl->cart_tax;?></p>
                                </div>
                            </td>
                            <td class="td-remove">&nbsp;</td>
                        </tr>
                        <?}?>
                        <tr>
                            <td>
                                <div class="price-row-col">
                                    <p><?=$CMS->lang['cart_amount'];?>:</p>
                                </div>
                            </td>
                            <td>
                                <div class="price-row-col">
                                    <p class="amount_change"><?=$tpl->amount;?></p>
                                </div>
                            </td>
                            <td class="td-remove">&nbsp;</td>
                        </tr>
                        <?}?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="mybutton">
    <a itemprop="url" class="btn-continue" href="/"><?=$CMS->lang['cart_continue'];?></a>
    <a itemprop="url" class="btn btn-primary btn-next btn_cart_order" href="/payment"><?=$CMS->lang['cart_next_step'];?></a>
</div>