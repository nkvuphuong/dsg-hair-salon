<link rel="stylesheet" type="text/css" href='/acp/assets/css/orders.css'>
<figure class="heading">
    <div class="row">
        <div class="col-md-6">
            <h3><?=$tpl->header_title;?></h3>
        </div>
        <div class="col-md-6">
            <a class="pull-right" href="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>" title=""><span class="font-icon font-icon-del"></span></a>
        </div>
    </div>
</figure>
<form id="formorder-signin_v1" name="formorder-signin_v1" action="<?=$tpl->form_action;?>" method="POST" enctype="multipart/form-data">

<div class="manage-container">
    <section class="tabs-section">
    <div class="tabs-section-nav tabs-section-nav-inline order-tabs-section-nav">
        <ul class="nav" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" href="#tabs-4-tab-1" role="tab" data-toggle="tab" aria-expanded="true">Customer details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#tabs-4-tab-2" role="tab" data-toggle="tab" aria-expanded="false">Payment details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#tabs-4-tab-3" role="tab" data-toggle="tab" aria-expanded="false">Shipping details</a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="#tabs-4-tab-4" role="tab" data-toggle="tab" aria-expanded="false">Totals</a>
            </li>
        </ul>
    </div>

    <div class="tab-content order-tab-content">
        <div role="tabpanel" class="tab-pane fade in active show" id="tabs-4-tab-1" aria-expanded="true">
            <!--.tabs-section-nav-->
            <div class="tab-content form-tab-content">
                <div role="tabpanel" class="tab-pane fade in active show" id="tabs-5-tab-1" aria-expanded="true">
                    <div class="box-typical box-typical-padding no-radius-top">
                        <fieldset class="form-group" id="content_change">
                            <div class="row">
                                <div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="row">
                                        <?= $CMS->vars['addon_goods_enable'] == 1 ? \core\ezy::render("store") : ""; ?>
                                        <input type="hidden" name="store_id_param" class="store_id_param" value="<?=$tpl->data['store_id'];?>" />
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <fieldset class="form-group">
                                                <label class="form-label pull-left" ><?=$CMS->lang['sale_user_id'];?></label>
                                                <div class="pull-right"  ><a class="form-label" onclick="cus_assign_me_ord(this)" val_name="<?=$tpl->member['user_display_name'];?>" val_id="<?=$tpl->member['user_id'];?>"><?=$CMS->lang['gassign_me'];?></a></div>
                                                <div style="clear:both; overflow:hidden"></div>

                                                <select class="select2-photo" name="user_id" id="user_id">
                                                    <?=$CMS->user->load_list_user($tpl->data['user_id']);?>
                                                </select>
                                            </fieldset>
                                        </div>
                                        
                                        <div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
                                            <label class="form-label semibold" for="supplier_id"><?=$CMS->lang['cus_id'];?><font style="margin-left:5px" color="#FF0000">(*)</font>
                                                <div class="box_action btn_add_more">
                                                    <? if ( $CMS->permit['customer_add'] == 1 ) { ?>
                                                    <a class="btn_gen add_new_cus pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                                    <? } ?>
                                                    <span class="box_edit_cus pull-right" style="margin-left: 10px;">
                                                        <? if ( $CMS->permit['customer_edit'] == 1 and $tpl->data['cus_id']) { ?>
                                                            <a id='<?=$tpl->data['cus_id'];?>' onclick='call_form_edit_customer(this);' class='btn_gen pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>
                                                        <? } ?>
                                                    </span>
                                                </div>
                                            </label>
                                            <div class="form-control-wrapper">
                                                <div class="input-group">
                                                    <input autofocus name="cus_name" type="text" id="search_cus_id" class="form-control" value="<?=$tpl->data['cus_name'];?>" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['notify_select_customer'];?>" />
                                                    <div class="input-group-addon">
                                                        <span style="cursor:pointer" onclick="autocompleteAll('#search_cus_id')" class="fa fa-arrow-down"></span>
                                                    </div>
                                                </div>  <!-- input-group-->
                                            </div>   <!-- form-control-wrapper-->
                                            <input type='hidden' name='cus_id' id='cus_id' value="<?=$tpl->data['cus_id'];?>" />
                                            <script>
                                                $(document).ready(function(){
                                                    autocompleteSearch('#search_cus_id', '<?=$CMS->vars['root_domain'];?>/?site=customer&subact=quicksearch', '#cus_id','order',0);
                                                    add_new_customer('#content_change');
                                                    add_do_new_customer('#box_customer','#formorder-signin_v1');
                                                    edit_do_customer('#box_customer','#formorder-signin_v1');
                                                    if($("#cus_id").val() != 0)
                                                    {
                                                        action_customer_morder('<?=$tpl->customer['cus_id'];?>', '<?=$tpl->customer['cus_email'];?>', '<?=$tpl->customer['cus_full_name'];?>', '<?=$tpl->customer['cus_address'];?>', '<?=$tpl->customer['cus_phone'];?>', '<?=$tpl->customer['cus_first_name'];?>', '<?=$tpl->customer['cus_last_name'];?>');
                                                    }
                                                });
                                            </script>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </fieldset>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row" id="apply_info" style="display: none;">
                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label class="form-label semibold">First name</label>
                                            <input type="text" class="form-control" name="cus_first_name">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label class="form-label semibold">Last name</label>
                                            <input type="text" class="form-control" name="cus_last_name">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label class="form-label semibold">Email</label>
                                            <input type="text" class="form-control" name="cus_email">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label class="form-label semibold">Telephone</label>
                                            <input type="text" class="form-control" name="cus_phone">
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <fieldset class="form-group">
                                    <label class="form-label semibold"><?=$CMS->lang['order_note'];?></label>
                                    <textarea class="form-control" name="ord_note" rows="3" style="height: 90px;"><?=$tpl->data['ord_note'];?></textarea>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

            <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-2" aria-expanded="true">
                <div class="box-typical box-typical-padding box-typical-info">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-12">
                                    <fieldset class="form-group">
                                        <label class="form-label semibold"><?=$CMS->lang['payment_method'];?></label>
                                        <select class="form-control" name="order_payment_method">
                                            <!-- Payment method -->
                                            <? foreach($tpl->payment_method_list  as $key=>$val){?>
                                                <option value="<?=$key;?>"><?=$val;?></option>
                                            <? } ?>
                                            <!-- End Payment method -->
                                        </select>
                                    </fieldset>
                                    <fieldset class="form-group" id="hide_show_account">
                                        <label class="form-label"><?=$CMS->lang['account_id'];?></label>

                                        <select class="form-control" name="account_id" id="account_id" >
                                            <option value="0"><?=$CMS->lang['select'];?></option>
                                            <!-- Transaction account -->
                                            <? foreach ($tpl->account as $a) { ?>
                                            <option value='<?=$a['accounts_id'];?>' <?=($a['accounts_id'] == $tpl->trx_account)?"selected":"";?>><?=$a['accounts_name'];?></option>
                                            <? } ?>
                                            <!-- End transaction account -->
                                        </select>
                                        <small class="text-muted" style="font-style:italic;font-size:11px !important"><?=$CMS->lang['ord_banknote'];?></small>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <fieldset class="form-group">
                                        <label class="form-label semibold">Choose billing address</label>
                                        <select class="form-control apply_id" cus_id="" name="billing_address" type="bill" onchange="applyInformation(this);">
                                            <option value="">-- None --</option>
                                        </select>
                                    </fieldset>
                                </div><!-- col-lg-6-->
                            </div><!-- class row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        <label class="form-label semibold">First name</label>
                                        <input type="text" class="form-control" maxlength="50" name="bill_first_name" value="<?=isset($tpl->bill['first_name']) ? $tpl->bill['first_name'] : "";?>">
                                    </fieldset>
                                </div>
                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        <label class="form-label semibold">Last name</label>
                                        <input type="text" class="form-control" maxlength="50" name="bill_last_name" value="<?=isset($tpl->bill['last_name']) ? $tpl->bill['last_name'] : "";?>">
                                    </fieldset>
                                </div>
                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        <label class="form-label semibold">Email</label>
                                        <input type="text" class="form-control" maxlength="70" name="bill_email" value="<?=isset($tpl->bill['email']) ? $tpl->bill['email'] : "";?>">
                                    </fieldset>
                                </div>
                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        <label class="form-label semibold">Telephone</label>
                                        <input type="text" class="form-control" maxlength="15" name="bill_phone" value="<?=isset($tpl->bill['phone']) ? $tpl->bill['phone'] : "";?>">
                                    </fieldset>
                                </div>

                                <div class="col-md-12">
                                    <fieldset class="form-group">
                                        <label class="form-label semibold">Address</label>
                                        <input type="text" class="form-control" maxlength="90" name="bill_address" value="<?=isset($tpl->bill['address']) ? $tpl->bill['address'] : "";?>">
                                    </fieldset>
                                </div>

                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        <label class="form-label semibold">City</label>
                                        <select name="bill_city" class="form-control auto_select" onchange="getDistrict(this, '#bill_district');" defaultvalue="<?=isset($tpl->bill['city']) ? $tpl->bill['city'] : "";?>">
                                            <?=$tpl->optionCity;?>
                                        </select>
                                    </fieldset>
                                </div>

                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        <label class="form-label semibold">District</label>
                                        <select name="bill_district" id="bill_district" class="form-control auto_select2" defaultvalue="<?=isset($tpl->bill['district']) ? $tpl->bill['district'] : "";?>">
                                            <option value="">-- Please choose a district --</option>
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        <label class="form-label semibold">Zip code/Postal code</label>
                                        <input type="text" class="form-control" maxlength="15" name="bill_zipcode" value="<?=isset($tpl->bill['zipcode']) ? $tpl->bill['zipcode'] : "";?>">
                                    </fieldset>
                                </div>
                            </div>
                        </div><!-- col-lg-6-->
                    </div><!-- class row -->


                </div>
            </div><!-- #End tabs-4-tab-2 -->





            <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-3" aria-expanded="true">
                <div class="box-typical box-typical-padding box-typical-info">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-6">
                                    <fieldset class="form-group">
                                        <label class="form-label semibold" ><?=$CMS->lang['ship_deliver'];?></label>
                                        <select class="form-control" name="ship_deliver">
                                            <?=$CMS->partner_delivery->get_list_partner_delivery($tpl->data['ship_deliver']);?>
                                        </select>
                                        <input type="hidden" name="is_shipping" value="1">
                                        <input type="hidden" name="sp_id" value="<?=isset($tpl->sp_id) ? $tpl->sp_id : "";?>">
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <fieldset class="form-group">
                                        <label class="form-label semibold">Choose shipping address</label>
                                        <select class="form-control apply_id" cus_id="" name="shipping_address" type="ship" onchange="applyInformation(this);">
                                            <option value="">-- None --</option>
                                        </select>
                                    </fieldset>
                                </div><!-- col-lg-6-->
                            </div><!-- class row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        <label class="form-label semibold">First name</label>
                                        <input type="text" class="form-control" maxlength="50" name="ship_first_name" value="<?=isset($tpl->ship['first_name']) ? $tpl->ship['first_name'] : "";?>">
                                    </fieldset>
                                </div>
                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        <label class="form-label semibold">Last name</label>
                                        <input type="text" class="form-control" maxlength="50" name="ship_last_name" value="<?=isset($tpl->ship['last_name']) ? $tpl->ship['last_name'] : "";?>">
                                    </fieldset>
                                </div>
                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        <label class="form-label semibold">Email</label>
                                        <input type="text" class="form-control" maxlength="70" name="ship_email" value="<?=isset($tpl->ship['email']) ? $tpl->ship['email'] : "";?>">
                                    </fieldset>
                                </div>
                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        <label class="form-label semibold">Telephone</label>
                                        <input type="text" class="form-control" maxlength="15" name="ship_phone" value="<?=isset($tpl->ship['phone']) ? $tpl->ship['phone'] : "";?>">
                                    </fieldset>
                                </div>

                                <div class="col-md-12">
                                    <fieldset class="form-group">
                                        <label class="form-label semibold">Address</label>
                                        <input type="text" class="form-control" maxlength="90" name="ship_address" value="<?=isset($tpl->ship['address']) ? $tpl->ship['address'] : "";?>">
                                    </fieldset>
                                </div>

                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        <label class="form-label semibold">City</label>
                                        <select name="ship_city" class="form-control auto_select" onchange="getDistrict(this, '#ship_district');" defaultvalue="<?=isset($tpl->ship['city']) ? $tpl->ship['city'] : "";?>">
                                            <?=$tpl->optionCity;?>
                                        </select>
                                    </fieldset>
                                </div>

                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        <label class="form-label semibold">District</label>
                                        <select name="ship_district" id="ship_district" class="form-control auto_select2" defaultvalue="<?=isset($tpl->ship['district']) ? $tpl->ship['district'] : "";?>">
                                            <option value="">-- Please choose a district --</option>
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        <label class="form-label semibold">Zip code/Postal code</label>
                                        <input type="text" class="form-control" maxlength="15" name="ship_zipcode" value="<?=isset($tpl->ship['zipcode']) ? $tpl->ship['zipcode'] : "";?>">
                                    </fieldset>
                                </div>
                            </div>
                        </div><!-- col-lg-6-->
                    </div><!-- class row -->


                </div>
            </div><!-- #End tabs-4-tab-3 -->

            <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-4" aria-expanded="true">
                <div class="box-typical box-typical-padding box-typical-info">
                    <div class="row">
                        <div class="col-lg-4">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="trackingumber">Tracking Number</label> 
                                <input type="text" class="form-control" id="trackingumber" name="tracking_code" value="<?=isset($tpl->data['tracking_code']) ? $tpl->data['tracking_code'] : "";?>" />
                            </fieldset>
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="trackingumber">Order Status</label>
                                <select class="form-control auto_select" name="ord_status" onchange="changeCusStatus(this, '[name=ord_status_custom]');" data-json='<?=$CMS->vars['custom_status'];?>' defaultvalue="<?=$tpl->data['ord_status'];?>">
                                    <option value="0"><?=$CMS->lang['ord_status_0'];?></option>
                                    <option value="1"><?=$CMS->lang['ord_status_1'];?></option>
                                    <option value="2"><?=$CMS->lang['ord_status_2'];?></option>
                                    <option value="3"><?=$CMS->lang['ord_status_3'];?></option>
                                </select>
                            </fieldset>

                            <fieldset class="form-group" id="custom_status">
                                <label class="form-label semibold" for="processing">Extend status</label>
                                <select class="form-control auto_select2" onchange="changecustomstatus(this, '#on_comment');" name="ord_status_custom" defaultvalue="<?=$tpl->data['ord_status_custom'];?>">
                                    <option value=""><?=$CMS->lang['title_custom_status'];?></option>
                                </select>
                                <input type="hidden" value="0" id="on_comment" name="on_comment">
                            </fieldset>


                            <fieldset class="form-group">
                                <label class="form-label semibold" for="exampleInputEmail1">Comment</label>
                                <textarea rows="6" class="form-control" placeholder="Comment" name="comment_content" data-autosize="" style="overflow: hidden; word-wrap: break-word; height: 88px;"></textarea>
                            </fieldset>
                            <fieldset class="form-group" style="margin-bottom: 5px;">
                                <div class="checkbox" style="margin-bottom: 0;">
                                    <input type="checkbox" name="send_notify" id="check-1" <?=$tpl->data['send_notify']==1?"checked":"";?> value="1">
                                    <label class="form-label semibold" for="check-1">Notify Customer</label>
                                </div>
                            </fieldset>

                            <fieldset class="form-group">
                                <div class="checkbox">
                                    <input type="checkbox" id="check-2" value="1" name="is_send_mail" <?=$tpl->data['is_send_mail']==1?"checked":"";?> />
                                    <label class="form-label semibold" for="check-2"><?=$CMS->lang['checkbox_sendemail'];?></label>
                                </div>
                            </fieldset>
                        </div>


                        <div class="col-lg-5">
                            <div class="total-wrap">
                                <h5 class="green-title">Total Summary</h5>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <p><?=$CMS->lang['gsubtotal'];?></p>
                                    </div>
                                    <div class="col-lg-6 text-right">
                                        <p class="semibold"><span class="subtotal-show">0 </span></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <p><?=$CMS->lang['gdiscount'];?></p>
                                    </div>
                                    <div class="col-lg-6 text-right">
                                        <div class="semibold">
                                            <div class="active_discount">
                                                <input  name="total_discount_value" id="total-discount-show" onchange="calculate_money();" type="number" class="form-control total-discount-show inp_dis" value="0">
                                                <div onclick="changeType(this, '.indiscount', '#total_discount_type');" class="indiscount b_gen b_percent active" value="0">%</div>
                                                <div onclick="changeType(this, '.indiscount', '#total_discount_type');" class="indiscount b_gen b_currency" value="1"><?=$CMS->vars['currency_type']?></div>
                                            </div>

                                            <input onchange="calculate_money();" type="hidden" name="total_discount_type" value="0" id="total_discount_type">

                                        </div>
                                    </div>
                                </div>
                                <?if($CMS->vars['enabled_commission']){?>
                                <div class="row">
                                    <div class="col-lg-6"><p><?=$CMS->lang['gcommission'];?></p></div>
                                    <div class="col-lg-6">
                                        <div class="active_discount">
                                            <input onchange="changeCommissionItems();" id="total-commission-show" type="number" class="form-control total-commission-show inp_dis" value="0">
                                            <div onclick="changeType(this, '.commission', '#total_commission_type');" class="commission b_gen b_percent active" value="0">%</div>
                                            <div onclick="changeType(this, '.commission', '#total_commission_type');" class="commission b_gen b_currency" value="1"><?=$CMS->vars['currency_type']?></div>
                                            <input onchange="changeCommissionItems();" type="hidden" name="total_commission_type" value="0" id="total_commission_type">
                                        </div>
                                    </div>
                                </div>
                                <?}?>

                                <div class="row">
                                    <div class="col-lg-6"><p><?=$CMS->lang['gtax'];?></p></div>
                                    <div class="col-lg-6 text-right"><p class="semibold"><span class="tax-show">0</span></p></div>
                                </div>
                                
                                <hr class="total-hr">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <p class="total-text-1"><p><?=$CMS->lang['gtotal'];?></p></p>
                                            <!--p class="text-gray mb-0">Paid by Cash on Delivery</p-->
                                        </div>
                                        <div class="col-lg-6 text-right">
                                            <p class="red-total"><span class="total-show">0</span></p>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div><!-- #End tabs-4-tab-6 -->
    </div><!-- #End tabs-4-tab-3 -->

    </section>
</div>

<section class="add_table">
    <div class="row">
        <div class="col-lg-12">
           <script>
                var pFrmType = 'transaction';
            </script>

            <?=$CMS->global->htmlTableProduct($tpl->request_product,1);?>
        </div>
    </div><!-- class row -->
</section>

<?=$tpl->action_footer;?>

</form>
<script>
    $(document).ready(function(){
        $("[name='order_payment_method']").change(function(){
            var val = parseInt($(this).val());
            if(val == 1)
            {
                $('#hide_show_account').show();
            }else
            {
                $('#hide_show_account').hide();
            }
        });

        validate_form_custom("#formorder-signin_v1",".act_submit_save","check_order");
    });
</script>

<div id="box_add_product" class="popup_add_product mfp-hide">
    <p class="title_add"><?=$CMS->lang['title_product_add'];?></p>
    <form id="add_product_form" name="add_product_form">
        <ul class="list_field">
            <li>
                <input class="" type="radio" name="product_type" value="0" checked='checked' /><?=$CMS->lang['title_product_type_0'];?>
                <input class="" type="radio" name="product_type" value="1" /><?=$CMS->lang['title_product_type_1'];?>
            </li>
            <li><input class="form-control" name="product_name" placeholder="<?=$CMS->lang['title_product_name'];?>" /></li>
            <li><input class="form-control" name="product_code" placeholder="<?=$CMS->lang['title_product_code'];?>" /></li>
            <li>
                <select name="sup_id" class="form-control select2">
                    <?=$CMS->supplier->get_list_supplier();?>
                </select>
                <?=$CMS->lang['title_product_supplier'];?>
            </li>
            <li>
                <select name="product_group" class="form-control select2">
                    <?=$CMS->product_group->getOptionCategory();?>
                </select>
                <?=$CMS->lang['title_product_group'];?>
            </li>
            <li>
                <div class="table-responsive" style="overflow-x: initial;">
                    <table class="table">
                        <tr>
                            <th>#</th>
                            <th><?=$CMS->lang['table_product_service'];?></th>
                            <th><?=$CMS->lang['table_description'];?></th>
                            <th><?=$CMS->lang['table_quantity'];?></th>
                            <th><?=$CMS->lang['table_price'];?></th>
                            <th><?=$CMS->lang['table_tax'];?></th>
                            <th></th>
                        </tr>
                        <tbody id="data_subitem">
                        <tr class="row-item" rowtr="last-row">
                            <td class="item-td" for="item-1" check="chtd"><span class="number">1</span></td>
                            <td class="item-td" for="item-2">
                                <input type="text" for="item-2" check="chtd" name="sub_product_name[]" class="form-control find_product hidden_border" autocomplete="off"/>
                                <input type="hidden" class="product_id" name="sub_product_id[]" value=""/>
                                <div class="box_result_find" style="position: absolute; background: #ccc;"></div>
                            </td>

                            <td class="item-td" for="item-3" check="chtd"><textarea for="item-3" name="sub_product_description[]" check="chtd" class="form-control hidden_border" style="resize: none;"></textarea></td>

                            <td class="item-td" for="item-4" check="chtd"><input type="text" for="item-4" name="sub_product_quantity[]" check="chtd" class="form-control hidden_border" onkeypress="return check_enter_number(event,this);"/></td>

                            <td class="item-td" for="item-5" check="chtd"><input type="text" for="item-5" name="sub_product_price[]" check="chtd" onkeypress="return check_enter_number(event,this);" class="form-control hidden_border"/></td>

                            <td class="item-td" for="item-6" check="chtd"><input type="text" for="item-6" check="chtd" onkeypress="return check_enter_number(event,this);" autocomplete="off" name="sub_product_tax[]" class="form-control search_tax hidden_border"/></td>

                            <td class="trash" for="item-7"><span class="del_row_invoid"><i class="fa fa-trash" aria-hidden="true"></i></span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </li>
            <li><input class="form-control" type="file" name="product_image" /></li>
            <li><textarea class="form-control" rows="7" cols="30" name="product_description" placeholder="<?=$CMS->lang['title_product_description'];?>" ></textarea></li>
            <li><input class="form-control" name="product_price" onkeypress="return check_enter_number(event,this)" placeholder="<?=$CMS->lang['title_product_price'];?>" /></li>
            <li><input class="" type="checkbox" name="inclusive_of_tax" value="1" /><?=$CMS->lang['title_product_is_tax'];?></li>
            <li><input class="form-control" name="product_tax" onkeypress="return check_enter_number(event,this)" placeholder="<?=$CMS->lang['title_product_tax'];?>" /></li>
            <li><input class="btn btn_add_product" type="button" value="Thêm"/></li>
            <p class="error_msg" style="display:none;"></p>
        </ul>
    </form>
</div>

<script>
    var pFrmType = 'transaction';
</script>
<?=$CMS->global->fullFormHtml();?>
<?=$CMS->global->assetFullFormHtml();?>
<?=\core\ezy::render("chooseProductType");?>
<script>
    var module_name = "module_order";
</script>

<script type='text/javascript' src='<?=$CMS->vars['js_acp'];?>/custom_transaction.js?20180312'></script>
<script type='text/javascript' src='<?=$CMS->vars['js_url'];?>/acp_assets.js'></script>
<script>
    $(document).ready(function() {
        $("#formorder-signin_v1").on("change keyup keydown", "[name='product_quantity[]'],[name='ass_quantity[]'], [name='product_price[]'], [name='ass_price[]'], [name='product_tax[]'], [name='ass_tax[]'], [name='product_cycle[]'], [name='trx_discount_value'], [name='trx_discount_type']", calculate_total_transaction);
        calculate_total_transaction();
    });
</script>
<script src="<?=$CMS->vars['js_acp'];?>/order.js"></script>
<script src="<?=$CMS->vars['js_acp'];?>/item_order.js"></script>


<input type="hidden" id="flag_product" />
<?=$tpl->comment;?>
<?=$tpl->logs;?>

