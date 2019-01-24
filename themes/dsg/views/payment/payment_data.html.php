<section class="box_cart cart clearfix">
    <div class="row">
        <div class="col-lg-6">
            <form enctype="multipart/form-data" id="payment" action="/payment/checkout" method="POST">
                <!-- Use for scroll when error -->
                <input type="hidden" name="scroll_jumpto" class="scroll_jumpto" data-jumpto="#payment" data-headerfixed=".navbar.main-nav" data-redirect="">

                <figure class="form_cart">
                    <h4 itemprop="description" class="sanb mb15"><?=$CMS->lang['payment_payer_info'];?></h4>
                    <ul class="row list_form">
                        <li class="col-lg-12">
                            <div class="form-group">
                                <div class="fl-flex-label">
                                    <input type="text" class="form-control" name="ship_full_name" id="ship_full_name" placeholder="<?=$CMS->lang['payment_full_name'];?>" value="<?=isset($tpl->shipping['ship_full_name'])?$tpl->shipping['ship_full_name']:'';?>" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['error_full_name'];?>" required autocomplete="off">
                                </div>
                            </div>
                        </li>

                        <li class="col-lg-12">
                            <div class="form-group">
                                <div class="fl-flex-label">
                                    <input type="text" class="form-control" name="ship_email" id="ship_email" placeholder="<?=$CMS->lang['payment_email'];?>" value="<?=isset($tpl->shipping['ship_email'])?$tpl->shipping['ship_email']:'';?>"  data-validation="[EMAIL]" data-validation-message="<?=$CMS->lang['error_email'];?>" required autocomplete="off">
                                </div>
                            </div>
                        </li>

                        <li class="col-lg-12">
                            <div class="form-group">
                                <div class="fl-flex-label">
                                    <input type="text" class="form-control inputPhone" name="ship_phone" id="ship_phone" placeholder="<?=$CMS->lang['payment_phone'];?>" value="<?=isset($tpl->shipping['ship_phone'])?$tpl->shipping['ship_phone']:'';?>"  data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['error_phone'];?>" required autocomplete="off">
                                </div>
                            </div>
                        </li>
                        
                        <li class="col-lg-12">
                            <div class="form-group">
                                <div class="checkbox">
                                    <input type="checkbox" name="send_to_friend" value="<?=isset($tpl->shipping['send_to_friend'])?$tpl->shipping['send_to_friend']:'';?>" style="margin-left: 0;" id="send_to_friend">
                                    <label for="send_to_friend"><?=$CMS->lang['payment_send_to_friend'];?></label>
                                </div>
                            </div>
                        </li>
                    </ul>
                    
                    <div class="box_recipient" style="display: none;">
                        <h4 class="sanb mb15"><?=$CMS->lang['payment_recipient_info'];?></h4>
                        <ul class="row list_form">
                            <li class="col-lg-12">
                                <div class="form-group">
                                    <div class="fl-flex-label">
                                        <input type="text" class="form-control" name="recipient_email" id="recipient_email" placeholder="<?=$CMS->lang['payment_email'];?>" value="<?=isset($tpl->shipping['recipient_email'])?$tpl->shipping['recipient_email']:'';?>" autocomplete="off">
                                    </div>
                                </div>
                            </li>

                            <li class="col-lg-12">
                                <div class="form-group">
                                    <div class="fl-flex-label">
                                        <input type="text" class="form-control" name="recipient_name" id="recipient_name" placeholder="<?=$CMS->lang['recipient_name'];?>" value="<?=isset($tpl->shipping['recipient_name'])?$tpl->shipping['recipient_name']:'';?>" autocomplete="off">
                                    </div>
                                </div>
                            </li>

                            <li class="col-lg-12">
                                <div class="form-group">
                                    <div class="fl-flex-label">
                                        <textarea class="form-control" col="5" name="recipient_message" id="recipient_message" placeholder="<?=$CMS->lang['recipient_message'];?>"><?=isset($tpl->shipping['recipient_message'])?$tpl->shipping['recipient_message']:'';?></textarea>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    
                    <button type="submit" title="<?=$CMS->lang['payment_button_'.$CMS->vars['payment_active']];?>" class="btn btn-primary btn-sm btn-style1 style1"><span><?=$CMS->lang['payment_button_'.$CMS->vars['payment_active']];?></span></button>

                    <p><?=$CMS->vars['payment_active'] ? $CMS->lang['payment_policy'] : '';?></p>
                </figure>
            </form>             
        </div>
        <div class="col-lg-1"></div>
        <div class="col-lg-5">
            <h4 class="timr"><?=$CMS->lang['payment_your_cart'];?></h4>
            <figure class="mybox-order">
                <figure class="inner">
                    <ul>
                        <!--Check cart-->
                        <?if( count($tpl->mycart) > 0 ){?>
                            
                        <!--Loop cart-->
                        <?foreach( $tpl->mycart as $key => $data ){?>
                        <li>
                            <div class="item-info clearfix">
                                <div class="col-xs-4">
                                    <div class="item-image">
                                        <img src="<?=\lib\input::getThumb($data['uploadPath'],100)?>" alt="<?=$data['product_name'];?>" title="<?=$data['product_name'];?>"/>
                                    </div>
                                </div>
                                <div class="col-xs-8">
                                    <div class="item-name">
                                        <?=$data['quantity'];?> x <?=$data['product_name'];?>
                                    </div>
                                    <div class="item-price"><?=$data['total_show'];?></div>
                                </div>
                            </div>
                        </li>
                        <?}?>
                        <!--End loop cart-->

                        <?}else{?>
                        <li><?=$CMS->lang['payment_empty'];?></li>
                        <?}?>
                    <!--End check cart-->
                    </ul>
                </figure>

                <!--figure class="subtotal">
                    <ul>
                        <li>
                            <label><?=$CMS->lang['payment_subtotal'];?></label>
                            <span class="sanb pull-right"><?=isset($tpl->subtotal)?$tpl->subtotal:'';?></span>
                        </li>
                    </ul>
                </figure-->
                <!--figure class="subtotal">
                    <ul>
                        <li>
                            <label><?=$CMS->lang['payment_shipping_fee'];?></label>
                            <span class="sanb pull-right"><?=isset($tpl->ship_fee)?$tpl->ship_fee:'';?></span>
                        </li>
                    </ul>
                </figure-->

                <? if($CMS->vars['discount_code']){ ?>
                    <figure class="total">
                        <ul>
                            <li>
                                <label class="sanb"><?=$CMS->lang['subtotal'];?></label>
                                <span class="sanb pull-right" id="cart_subtotal"><?=$tpl->cart_subtotal?></span>
                            </li>
                            <li id="discount_code_info" style="<?=$tpl->discount_code_info?>">
                                <label class="sanb"><?=$CMS->lang['discount'];?> <span id="cart_discount_code_text"><?=isset($_SESSION['discount_code']['code'])?$_SESSION['discount_code']['code']:''?></span> <i onclick="removeDiscountCode()" class="fa fa-times" aria-hidden="true"></i></label>
                                <span class="sanb pull-right" id="cart_discount_code_value"><?=$tpl->cart_discount;?></span>
                            </li>
                            <li id="discount_code_input" style="<?=$tpl->discount_code_input;?>">
                                <label class="sanb"><?=$CMS->lang['discount_code'];?></label>
                                <span class="sanb pull-right">
                                    <div class="input-group">
                                      <input autocomplete="off" type="text" class="form-control" placeholder="<?=$CMS->lang['discount_code'];?>" id="cart_discount_code" value="<?=isset($_SESSION['discount_code']['code'])?$_SESSION['discount_code']['code']:''?>">
                                      <button onclick="applyDiscountCode()" class="btn btn-secondary btn-discount" type="button"><div style="display: none" id="loader_discount_code" class="b-loader"></div><i id="enter_discount_code" class="fa fa-sign-in" aria-hidden="true"></i></button>
                                    </div>
                                </span>
                            </li>
                            <li>
                                <label class="sanb"><?=$CMS->lang['payment_vat'];?></label>
                                <span class="sanb pull-right" id="cart_tax"><?=$tpl->cart_tax?></span>
                            </li>
                            <li>
                                <label class="sanb"><?=$CMS->lang['payemnt_total'];?></label>
                                <span class="sanb amount-total pull-right" id="cart_payment_total"><?=$tpl->cart_amount;?></span>
                            </li>
                        </ul>
                    </figure>
                <? } else { ?>

                    <? if(isset($tpl->vatori) && $tpl->vatori) { ?>
                        <figure class="subtotal">
                            <ul>
                                <li>
                                    <label><?=$CMS->lang['payment_vat'];?></label>
                                    <span class="sanb pull-right"><?=$tpl->vat;?></span>
                                </li>
                            </ul>
                        </figure>
                    <? } ?>

                    <figure class="total">
                        <ul>
                            <li>
                                <label class="sanb"><?=$CMS->lang['payemnt_total'];?></label>
                                <span class="sanb amount-total pull-right"><?=$tpl->amount;?></span>
                            </li>
                        </ul>
                    </figure>
                <? } ?>

            </figure>
            <figure class="note">
                <p itemprop="name" class="sanb" style="font-size: 16px; font-weight: bold;"><?=isset($CMS->lang['payment_question_ship'])?$CMS->lang['payment_question_ship']:'';?></p>
                <p itemprop="description"><?=isset($CMS->lang['payment_answer_ship'])?$CMS->lang['payment_answer_ship']:'';?></p>
            </figure>
        </div>
    </div>
</section>
 <div style="margin-bottom:10px"></div>
<script type="application/javascript">
    $(document).ready(function() {
        $('.fl-flex-label').flexLabel();
        $('form#payment').validate({
            submit: {
                settings: {
                    clear: 'keypress',
                    display: "inline",
                    button: "[type='submit']",
                    inputContainer: 'form-group',
                    errorListClass: 'form-tooltip-error',
                },
                callback: {
                    onError: function (node, globalError) {
                        $("form#payment .scroll_jumpto").trigger('click');
                    }
                }
            }
        });

        $("input[name='send_to_friend']").click(function(){
            var check_val = $(this).val();
            if(check_val == 0) {
                $(".box_recipient").show();
                $(this).val(1);

            } else {
                $(".box_recipient").hide();
                $(this).val(0);
            }
        });

        var check_send = parseInt($("input[name='send_to_friend']").val());
        if(check_send == 1) {
            $("input[name='send_to_friend'][value='1']").prop("checked", true);
            $(".box_recipient").show();
        }
    });
</script>