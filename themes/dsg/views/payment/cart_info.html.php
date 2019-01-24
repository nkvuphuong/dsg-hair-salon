<!-- cart info -->
<div class="box-cart-right">
    <div class="title_style4 pa-15">
        <h3>Thông tin đơn hàng <span class="font-small">(<?=count($tpl->mycart);?> sản phẩm)</span> </h3>
    </div> 
    <div class="box-cart-right-cont">
        <div class="list-media clearfix">
            <!--Check cart-->
            <?if( empty($tpl->mycart) ){?>
            <div class="media"><div class="media-body"><h4 class="media-heading"><?=$CMS->lang['payment_empty'];?></h4></div></div>
            <?}else{ foreach( $tpl->mycart as $data ){?>
            <div class="media">
                <div class="media-left">
                    <a href="<?=$data['url_none_html']?>" class="img-80">
                        <img class="media-object" src="<?=$data['image'];?>" alt="<?=$data['product_name'];?>" width="80">
                    </a>
                </div>
                <div class="media-body">
                    <h4 class="media-heading"><a href="<?=$data['url_none_html']?>" title="<?=$data['product_name'];?>"><?=$data['quantity'];?> x <?=$data['product_name'];?></a></h4>
                    <div class="price-well"><?=$data['price_show'];?></div>
                </div>
            </div>
            <?}}?>
        </div>

        <? if($CMS->vars['discount_code']){ ?>
            <div class="item-cart clearfix">
                <div class="pull-left"><strong><?=$CMS->lang['subtotal'];?>:</strong></div>
                <div class="pull-right" id="cart_subtotal"><?=$tpl->cart_subtotal;?></div>
            </div>
            <div class="item-cart clearfix" id="discount_code_info" style="<?=$tpl->discount_code_info?>">
                <div class="pull-left"><strong><?=$CMS->lang['discount'];?> (<span id="cart_discount_code_text"><?=isset($_SESSION['discount_code']['code'])?$_SESSION['discount_code']['code']:''?></span>) <i onclick="removeDiscountCode()" class="fa fa-times" aria-hidden="true"></i></strong>: </div>
                <div class="pull-right" id="cart_discount_code_value"><?=$tpl->cart_discount;?></div>
            </div>
            <div class="item-cart clearfix" id="discount_code_input" style="<?=$tpl->discount_code_input;?>">
                <div class="pull-left"><strong><?=$CMS->lang['discount_code'];?>:</strong></div>
                <div class="pull-right">
                    <div class="input-group">
                        <input autocomplete="off" type="text" class="form-control" placeholder="<?=$CMS->lang['discount_code'];?>" id="cart_discount_code" value="<?=isset($_SESSION['discount_code']['code'])?$_SESSION['discount_code']['code']:''?>">
                        <span class="input-group-btn">
                            <button onclick="applyDiscountCode()" class="btn btn-secondary" type="button"><div style="display: none" id="loader_discount_code" class="b-loader"></div><i id="enter_discount_code" class="fa fa-sign-in" aria-hidden="true"></i></button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="item-cart clearfix">
                <div class="pull-left"><strong><?=$CMS->lang['payment_vat'];?>:</strong></div>
                <div class="pull-right" id="cart_tax"><?=$tpl->cart_tax;?></div>
            </div>
            <div class="item-cart clearfix">
                <div class="pull-left"><strong><?=$CMS->lang['payemnt_total'];?>:</strong></div>
                <div class="pull-right"><strong id="cart_payment_total"><?=$tpl->cart_amount;?></strong></div>
            </div>
        <? } else { ?>
            <? if($tpl->cart_taxOri) { ?>
                <div class="item-cart clearfix">
                    <div class="pull-left"><strong><?=$CMS->lang['payment_vat'];?>:</strong></div>
                    <div class="pull-right" id="cart_tax"><?=$tpl->cart_tax;?></div>
                </div>
            <? } ?>
            <div class="item-cart clearfix">
                <div class="pull-left"><strong><?=$CMS->lang['payemnt_total'];?>:</strong></div>
                <div class="pull-right"><strong><?=$tpl->amount;?></strong></div>
            </div>
        <? } ?>


    </div>
</div><!-- End cart info -->


<div style="margin-bottom:15px"></div>