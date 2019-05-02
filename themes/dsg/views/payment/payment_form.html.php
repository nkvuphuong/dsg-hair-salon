<!-- Form check out -->
<form enctype="multipart/form-data" class="form-horizontal" role="form" name="payment" id="payment" action="/payment/checkout" method="POST">
<div class="box-cart-left">
    <div class="title_style3 bottom0">
        <h3><?=$CMS->lang['payment_payer_info'];?>   </a></h3>
    </div>
    <div class="box-cont padd10">
        <ul class="row list_form">
            <li class="col-lg-12">
                <div class="fl-flex-label">
                    <input type="text" class="form-control" name="cus_full_name" id="cus_full_name" placeholder="<?=$CMS->lang['payment_full_name'];?>" value="<?=isset($tpl->shipping['cus_full_name']) ? $tpl->shipping['cus_full_name'] : '';?>" required>
                </div>
            </li>

            <li class="col-lg-12">
                <div class="fl-flex-label">
                    <input type="text" class="form-control" name="cus_email" id="cus_email" placeholder="<?=$CMS->lang['payment_email'];?>" value="<?=isset($tpl->shipping['cus_email'])?$tpl->shipping['cus_email']:'';?>" required>
                </div>
            </li>

            <li class="col-lg-12">
                <div class="fl-flex-label">
                    <input type="text" class="form-control" name="cus_phone" id="cus_phone" placeholder="<?=$CMS->lang['payment_phone'];?>" value="<?=isset($tpl->shipping['cus_phone'])?$tpl->shipping['cus_phone']:'';?>">
                </div>
            </li>

            <li class="col-lg-12">
                <div class="fl-flex-label">
                    <input type="text" class="form-control" name="cus_address1" id="cus_address1" placeholder="<?=$CMS->lang['payment_address'];?> 1" value="<?=isset($tpl->shipping['cus_address'])?$tpl->shipping['cus_address']:'';?>" required>
                </div>
            </li>

            <li class="col-lg-6">
                <div class="fl-flex-label">
                    <select name="cus_city" id="cus_city" class="form-control auto_select" defaultvalue="<?=isset($tpl->shipping['cus_city'])?$tpl->shipping['cus_city']:'';?>" style="padding: 0 7px;" onchange="getDistrict(this, '#cus_district');">
                        <option><?=$CMS->lang['payment_city'];?></option>
                        <!--Loop country-->
                        <? if(!empty($tpl->optionCity) && is_array($tpl->optionCity)) { ?>
                            <? $tpl->optionCity = !empty($tpl->optionCity) ? $tpl->optionCity : []; foreach ($tpl->optionCity as $data) { ?>
                                <option value="<?=$data['city_id'];?>"><?=$data['city_name'];?></option>
                            <? } ?>
                        <? } ?>
                        <!--End loop country-->
                    </select>
                </div>
            </li>

            <li class="col-lg-6">
                <div class="fl-flex-label">
                    <select name="cus_district" id="cus_district" class="form-control auto_select2" defaultvalue="<?=isset($tpl->shipping['cus_district'])?$tpl->shipping['cus_district']:'';?>" style="padding: 0 7px;">
                        <option><?=$CMS->lang['payment_district'];?></option>
                    </select>
                </div>
            </li>

            <li class="col-lg-12">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="same_info" name="same_info" value="<?=isset($tpl->shipping['same_info'])?$tpl->shipping['same_info']:'';?>" />
                        <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                        <?=$CMS->lang['lang_same_infomation'];?>
                    </label>
                </div>
            </li>
        </ul>
    </div>
    
    <div class="box_info_shipping" style="display: none;">

        <div class="title_style3 bottom0">
            <h3><?=$CMS->lang['payment_shipping_info'];?></h3>
        </div>
        <div class="box-cont padd10">
            <ul class="row list_form">
                <li class="col-lg-12">
                    <div class="fl-flex-label">
                        <input type="text" class="form-control" name="ship_full_name" id="ship_full_name" placeholder="<?=$CMS->lang['payment_full_name'];?>" value="<?=isset($tpl->shipping['ship_full_name'])?$tpl->shipping['ship_full_name']:'';?>" required>
                    </div>
                </li>

                <li class="col-lg-12">
                    <div class="fl-flex-label">
                        <input type="text" class="form-control" name="ship_phone" id="ship_phone" placeholder="<?=$CMS->lang['payment_phone'];?>" value="<?=isset($tpl->shipping['ship_phone'])?$tpl->shipping['ship_phone']:'';?>" required>
                    </div>
                </li>

                <li class="col-lg-12">
                    <div class="fl-flex-label">
                        <input type="text" class="form-control" name="ship_address1" id="ship_address1" placeholder="<?=$CMS->lang['payment_address'];?> 1" value="<?=isset($tpl->shipping['ship_address'])?$tpl->shipping['ship_address']:'';?>" required>
                    </div>
                </li>

                <li class="col-lg-6">
                    <div class="fl-flex-label">
                        <select name="ship_city" id="ship_city" class="form-control auto_select" defaultvalue="<?=isset($tpl->shipping['ship_city'])?$tpl->shipping['ship_city']:'';?>" style="padding: 0 7px;" onchange="getDistrict(this, '#ship_district');">
                            <option><?=$CMS->lang['payment_city'];?></option>
                            <!--Loop country-->
                            <? if(!empty($tpl->optionCity) && is_array($tpl->optionCity)) { ?>
                                <? $tpl->optionCity = !empty($tpl->optionCity) ? $tpl->optionCity : []; foreach ($tpl->optionCity as $data) { ?>
                                    <option value="<?=$data['city_id'];?>"><?=$data['city_name'];?></option>
                                <? } ?>
                            <? } ?>
                            <!--End loop country-->
                        </select>
                    </div>
                </li>

                <li class="col-lg-6">
                    <div class="fl-flex-label">
                        <select name="ship_district" id="ship_district" class="form-control auto_select2" defaultvalue="<?=isset($tpl->shipping['ship_district'])?$tpl->shipping['ship_district']:'';?>" style="padding: 0 7px;">
                            <option><?=$CMS->lang['payment_district'];?></option>
                        </select>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="box-cart-left">
    <div class="title_style3 bottom0">
        <h3>Hình thức thanh toán</h3>
    </div>
    <div class="box-cont no-padd clearfix">
        <div class="nav-tabs-01">
            <input type="hidden" name="payment_method" value="2"/>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs payment-tab-choose" role="tablist">
                <li class="payment-tab-choose-item active" data-target="#price-money" data-toggle="tab" data-payment-method="2">
                    <a class="pointer"><i class="demo-icon2 icon-circle-empty"></i><i class="demo-icon2 icon-dot-circled"></i>
                    <span class="name-tabs"><i class="arrow-left"></i> Thanh toán trực tiếp khi nhận hàng<i class="demo-icon2 fa fa-university"></i></span></a>
                </li>
                <? if(isset($CMS->vars['bk_active']) && $CMS->vars['bk_active']) { ?>
                    <li class="payment-tab-choose-item" data-target="#price-card" data-toggle="tab" data-payment-method="3">
                        <a class="pointer"><i class="demo-icon2 icon-circle-empty"></i><i class="demo-icon2 icon-dot-circled"></i> <span class="name-tabs"><i class="arrow-left"></i>Bảo Kim<i class="demo-icon2 fa  fa-credit-card" style="color: #3e3e3e;"></i></span></a>
                    </li>
                <? } ?>
                <? if(isset($CMS->vars['nl_active']) && $CMS->vars['nl_active']) { ?>
                    <li class="payment-tab-choose-item" data-target="#price-card" data-toggle="tab" data-payment-method="4">
                        <a class="pointer"><i class="demo-icon2 icon-circle-empty"></i><i class="demo-icon2 icon-dot-circled"></i> <span class="name-tabs"><i class="arrow-left"></i>Ngân lượng<i class="demo-icon2 fa fa-id-card-o" style="color: #3e3e3e;"></i></span></a>
                    </li>
                <? } ?>
                <? if(isset($CMS->vars['ck_active']) && $CMS->vars['ck_active']) { ?>
                    <li class="payment-tab-choose-item" data-target="#transfer-bank" data-toggle="tab" data-payment-method="1">
                        <a class="pointer"><i class="demo-icon2 icon-circle-empty"></i><i class="demo-icon2 icon-dot-circled"></i> <span class="name-tabs"><i class="arrow-left"></i>Chuyển salonản<i class="demo-icon2 fa fa-university" style="color: #3e3e3e;"></i></span></a>
                    </li>
                <? } ?>
            </ul>
        </div>
        <div class="tab-content payment-tab-content">
            <div class="tab-pane active" id="price-money">
                <div class="choose-bank" style="background: #0f0d0e;padding:15px; ">
                    <? if(isset($CMS->vars['payment_cod_note']) && $CMS->vars['payment_cod_note']) { echo $CMS->vars['payment_cod_note']; } else {?>
                    <p>Quý khách sẽ thanh toán bằng tiền mặt khi nhận hàng tại nhà</p>
                    <p><strong>Lưu ý</strong></p>
                    <p>- Bạn nhớ kiểm tra kỹ thông tin của đơn hàng bên phải vì thông tin này sẽ không thể thay đổi sau khi đơn hàng được xác nhận thành công.</p> 
                    <p>- Chúng tôi sẽ không gửi tin nhắn xác nhận đơn hàng nên bạn vui lòng xem lại thông tin trong xác nhận đơn hàng được gửi qua email.</p>
                    <p>- Nhằm đảm bảo quyền lợi mua sắm cho các khách hàng cá nhân, chúng tôi sẽ giới hạn số lượng sản phẩm trong mỗi đơn hàng và chúng tôi xin phép từ chối đơn hàng có dấu hiệu mua đi bán lại.</p>
                    <p>- Trong chương trình “Cách mạng mua sắm”, một khách hàng chỉ được sử dụng tối đa 05 mã giảm giá (voucher) mỗi ngày. Nếu khách hàng sử dụng nhiều hơn 05 mã giảm giá, chúng tôi rất tiếc chỉ thực hiện 05 đơn hàng đầu tiên và xin phép hủy các đơn hàng còn lại.</p>
                    <? } ?>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-md btn-primary nextBtn btn-lg pull-left"><span><?=$CMS->lang['payment_button_checkout'];?></span></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="transfer-bank">
                <div class="choose-bank" style="background: #0f0d0e;padding:15px;  ">
                    <?=isset($CMS->vars['payment_bank_info']) ? $CMS->vars['payment_bank_info'] : '';?>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-md btn-primary nextBtn btn-lg pull-left"><span><?=$CMS->lang['payment_button_checkout'];?></span></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="price-card">
                <div class="choose-bank" style="background: #0f0d0e;padding:15px; ">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-md btn-primary nextBtn btn-lg pull-left"><span><?=$CMS->lang['payment_button_checkout'];?></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
<script type="text/javascript">
    $(document).ready(function(){
        // Auto select
        $("select.auto_select").each(function(){
          var val_default = $(this).attr("defaultvalue");
          $(this).find("option[value='"+val_default+"']").prop("selected",true);
          $(this).trigger("change");
        });

        setTimeout( function() {
            $("select.auto_select2").each(function(){
              var val_default = $(this).attr("defaultvalue");
              $(this).find("option[value='"+val_default+"']").prop("selected",true);
            });
        },1200);

        $("input[name='same_info']").click(function(){
            var val_check = $(this).val();
            if(val_check == 1)
            {
                $(".box_info_shipping").show();
                $(this).val(0);
            }else
            {
                $(".box_info_shipping").hide();
                $(this).val(1);
            }
        });

        var check_same = "<?=isset($tpl->shipping['same_info'])?$tpl->shipping['same_info']:'';?>";
        // console.log(check_same);
        if(parseInt(check_same)==1)
        {
            $("input[name='same_info']").prop("checked", true);
            $(".box_info_shipping").hide();
            $(this).val(1);
        }else
        {
            $(".box_info_shipping").show();
        }
    });
</script>
<!-- Payment form -->