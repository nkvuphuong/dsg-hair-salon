 <div class="box-payment bottom">
    <div class="container">
        <div class="row section-title-wrapper" style="padding-top: 0">
              <div class="col-md-12 text-center">
                  <h1>Đơn hàng</h1>
              </div>
        </div>
        <!--Step-->
        

        <!-- Cart info -->
        <div class="row">
            <div class="col-sm-12">
                <div class="box-cart-right pa-15">
                    <div class="title_style4">
                        <h3>Thông tin đơn hàng <span class="font-small">(<?=count($tpl->mycart);?> sản phẩm)</span> </h3>
                    </div>
                    <div class="box-cart-right-cont">
                        <div class="list-media clearfix cart-details" style="overflow-y:visible;max-height:unset;">
                            <!-- title -->
                            <div class="media cart-details-items">
                                <div class="media-left">&nbsp;</div>
                                <div class="media-body">
                                    <div class="col-sm-8 col-xs-12">
                                        <h5 class="media-heading"><?=$CMS->lang['cart_item'];?></h5>
                                    </div>
                                    <div class="col-sm-2 col-xs-6">
                                        <h5 class="media-heading"><?=$CMS->lang['cart_quantity'];?></h5>
                                    </div>
                                    <div class="col-sm-2 col-xs-6">
                                        <h5 class="media-heading"><?=$CMS->lang['cart_total'];?></h5>
                                    </div>
                                </div>
                                <div class="media-right">
                                    <h5 class="media-heading" style="padding-right: 0px;"><?=$CMS->lang['cart_del'];?></h5>
                                </div>
                            </div>

                            <!--Check cart-->
                            <?if( empty($tpl->mycart) ){?>
                                <div class="media"><div class="media-body"><h4 class="media-heading"><?=$CMS->lang['cart_empty'];?></h4></div></div>
                            <?}else{ foreach( $tpl->mycart as $key => $data ){?>
                                <!-- Item -->
                                <div class="media cart-details-items cart-details-items-<?=$key;?>">
                                    <div class="media-left">
                                        <a href="<?=$data['url_none_html']?>" title="<?=$data['product_name'];?>" class="img-80">
                                            <img class="media-object" src="<?=$data['image'];?>" alt="<?=$data['product_name'];?>" width="80">
                                        </a>
                                    </div>
                                    <div class="media-body">
                                        <div class="col-sm-7 col-xs-12">
                                            <h4 class="media-heading"><a href="<?=$data['url_none_html']?>" title="<?=$data['product_name'];?>"><?=$data['product_name'];?></a></h4>
                                            <div class="price-well"><?=$data['price_show'];?></div>
                                        </div>
                                        <div class="col-sm-3 col-xs-6">
                                            <input type="number" min="1" cart_id="<?=$key;?>" onkeyup="update_cart(this);" onchange="update_cart(this);" value="<?=$data['quantity'];?>" class="ui-inputText box_quantity form-control" />
                                        </div>
                                        <div class="col-sm-2 col-xs-6">
                                            <div class="price-well total_change_<?=$key;?>"><?=$data['total_show'];?></div>
                                        </div>
                                    </div>
                                    <div class="media-right width-30px">
                                        <a class="pointer" onclick="delItem(this);" cart_id="<?=$key;?>"><i class="fa fa-times" aria-hidden="true"></i></a>
                                    </div>
                                </div><!-- End Item -->
                            <?}}?>
                        </div>

                        <!--Cart Amount-->
                        <? if($CMS->vars['discount_code']){ ?>
                            <div class="item-cart clearfix">
                                <div class="pull-left"><strong><?=$CMS->lang['cart_subtotal'];?>:</strong></div>
                                <div class="pull-right"><span id="cart_subtotal"><?=$tpl->cart_subtotal;?></span></div>
                            </div>
                            <div class="item-cart clearfix">
                                <div class="pull-left"><strong><?=$CMS->lang['cart_discount'];?>:</strong></div>
                                <div class="pull-right"><span id="cart_discount_code_value"><?=$tpl->cart_discount;?></span></div>
                            </div>
                            <div class="item-cart clearfix">
                                <div class="pull-left"><strong><?=$CMS->lang['cart_tax'];?>:</strong></div>
                                <div class="pull-right"><span  id="cart_tax"><?=$tpl->cart_tax;?></span></div>
                            </div>
                            <div class="item-cart clearfix">
                                <div class="pull-left"><strong><?=$CMS->lang['cart_total'];?>:</strong></div>
                                <div class="pull-right"><strong class="price-well" id="cart_payment_total"><?=$tpl->cart_amount;?></strong></div>
                            </div>
                        <? } else { ?>
                            <? if($tpl->cart_taxOri) { ?>
                                <div class="item-cart clearfix">
                                    <div class="pull-left"><strong><?=$CMS->lang['cart_tax'];?>:</strong></div>
                                    <div class="pull-right"><span  id="cart_tax"><?=$tpl->cart_tax;?></span></div>
                                </div>
                            <? } ?>
                            <div class="item-cart clearfix">
                                <div class="pull-left"><strong><?=$CMS->lang['cart_amount'];?>:</strong></div>
                                <div class="pull-right"><strong class="price-well amount_change"><?=$tpl->amount;?></strong></div>
                            </div>
                        <? } ?>

                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-15">
            <div class="col-xs-6">
                <a href="/" style="color:#e4e426"><?=$CMS->lang['cart_continue'];?></a>
            </div>
            <div class="col-xs-6">
                
                <a   class="btn btn-md btn-primary nextBtn pull-right" href="/payment"  type="button"><?=$CMS->lang['cart_next_step'];?></a>


            </div>
        </div>
    </div>
</div>
<div style="margin-bottom:20px"></div>