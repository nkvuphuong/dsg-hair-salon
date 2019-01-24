<?php
if ( defined("is_web_us") == true )
{
    $hide_field = " style='display:block' ";
}else
{
    $hide_field = " style='display:none' ";
}
?>
<form id="formorder-signin_v1" name="formorder-signin_v1" action="<?=$tpl->form_action;?>" method="POST" enctype="multipart/form-data">
    <section class="add_form main_form">
        <figure class="heading">
            <h3><?=$CMS->lang['order_title_add'];?></h3>
            <a href="<?=$CMS->vars['root_domain'];?>/?site=order" title=""><span class="font-icon font-icon-del"></span></a>
        </figure>
        <figure class="box-typical box-typical box-typical-padding border">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

                    <div class="row" <?=$hide_field;?>>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <fieldset class="form-group row">
                                <label class="col-xl-12 col-md-12 col-sm-12 col-xs-12 form-label"><?=$CMS->lang['service_type'];?>:</label>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12  form-label">
                                    <!-- Service type -->
                                    <? for ($s = 0; $s <= 1; $s++) { ?>
                                    <div class="radio checkbox w25">
                                        <input type="radio" onclick="change_service_type(<?=$s;?>)" name="service_type" id="service_type_<?=$s;?>"  value="<?=$s;?>" <?=$s==intval($tpl->data['service_type'])?"checked":"";?>>
                                        <label for="service_type_<?=$s;?>"><?=$CMS->lang["service_type_{$s}"];?></label>
                                    </div>
                                    <? } ?>
                                    <!-- End service type -->
                                </div>
                            </fieldset>
                        </div><!-- col-lg-6-->
                    </div>

                    <fieldset class="form-group" id="content_change">
                        <div class="row">
                            <div class="col-xl-6 col-md-6 col-sm-12 col-xs-12" style="clear: both; overflow: hidden;">
                                <label class="form-label pull-left" for="supplier_id"><?=$CMS->lang['cus_id'];?><font style="margin-left:5px" color="#FF0000">(*)</font></label>
                                <div class="box_action pull-right">
                                    <? if ( $CMS->permit['customer_add'] == 1 ) { ?>
                                    <a class="btn_gen add_new_cus pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                    <? } ?>
                                    <span class="box_edit_cus pull-right" style="margin-left: 10px;">
                                        <? if ( $CMS->permit['customer_edit'] == 1 ) { ?>
                                        <? } ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-md-6 col-sm-12 col-xs-12 form-control-wrapper">
                                <div class="input-group">
                                    <input autofocus name="cus_name" type="text" id="search_cus_id"   class="form-control" value="<?=$tpl->data['cus_name'];?>" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['notify_select_customer'];?>" />
                                    <div class="input-group-addon">
                                        <span style="cursor:pointer" onclick="autocompleteAll('#search_cus_id')" class="fa fa-arrow-down"></span>
                                    </div>
                                </div>	<!-- input-group-->
                            </div>	 <!-- form-control-wrapper-->
                            <input type='hidden' name='cus_id' id='cus_id' value="<?=$tpl->data['cus_id'];?>" />
                            <script>
                                $(document).ready(function(){
                                    autocompleteSearch('#search_cus_id', '<?=$CMS->vars['root_domain'];?>/?site=customer&subact=quicksearch', '#cus_id','order',0);
                                    add_new_customer('#content_change');
                                    add_do_new_customer('#box_customer','#formorder-signin_v1');
                                    edit_do_customer('#box_customer','#formorder-signin_v1');
                                });
                            </script>
                        </div>
                    </fieldset>

                    <div class="row">
                        <div class="col-lg-7">

                            <fieldset class="form-group">
                                <label class="form-label"><?=$CMS->lang['payment_method'];?></label>
                                <!-- Payment method -->
                                    <? for ($i = 0; $i <= 5; $i++) { ?>
                                    <div class='radio checkboxs'>
                                        <input type='radio'  name='order_payment_method' id='radio-method-<?=$i;?>' value='<?=$i;?>' <?=$i == $tpl->payment_method?"checked":"";?>>
                                        <label for='radio-method-<?=$i;?>'><?=$CMS->lang["payment_method_{$i}"];?></label>
                                    </div>
                                    <? } ?>
                                <!-- End Payment method -->
                            </fieldset>
                        </div><!-- col-lg-6-->
                        <div class="col-lg-5" id="hide_show_account">
                            <fieldset class="form-group">
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
                        </div><!-- col-lg-6-->
                    </div><!-- class row -->

                    <div class="row">
                        
                        <?= $CMS->vars['addon_goods_enable'] == 1 ? \core\ezy::render("store") : ""; ?>

                        <div class="col-lg-6">
                            <fieldset class="form-group">
                                <label class="form-label pull-left" ><?=$CMS->lang['sale_user_id'];?></label>
                                <div class="pull-right"  ><a class="form-label" onclick="cus_assign_me_ord(this)" val_name="<?=$tpl->member['user_display_name'];?>" val_id="<?=$tpl->member['user_id'];?>"><?=$CMS->lang['gassign_me'];?></a></div>
                                <div style="clear:both; overflow:hidden"></div>

                                <select class="select2-photo" name="user_id" id="user_id">
                                    <?=$CMS->user->load_list_user($tpl->data['user_id']);?>
                                </select>
                                <input type="hidden" name="store_id_param" value="<?=$tpl->data['store_id'];?>" />
                            </fieldset>
                        </div>
                    </div>

                    <fieldset class="form-group">
                        <label class="form-label" ><?=$CMS->lang['order_note'];?></label>
                        <textarea class="form-control" name="ord_note" rows="3"><?=$tpl->data['ord_note'];?></textarea>
                    </fieldset>

                    <fieldset class="form-group">
                        <div class="checkbox-toggle">
                            <input type="checkbox" id="check-1" value="1" name="is_send_mail" <?=$tpl->data['is_send_mail']==1?"checked":"";?> />
                            <label for="check-1"><?=$CMS->lang['checkbox_sendemail'];?></label>
                        </div>
                    </fieldset>

                    <fieldset class="form-group block_service_type_0">
                        <div class="checkbox-toggle">
                            <input type="checkbox" id="check-2" value="1"  defaultvalue="<?=$tpl->data['is_shipping'];?>" name="is_shipping" <?=$tpl->data['is_shipping']==1?"checked":"";?> />
                            <label for="check-2"><?=$CMS->lang['checkbox_shipping'];?></label>
                        </div>
                    </fieldset>
                    <fieldset class="row block_service_type_1">
                       <div class="col-lg-6">
                            <label class="form-label"><?=$CMS->lang['booking_date'];?></label>
                            <div class="input-group">
                                            <input name="booking_date" id="booking_date" type="text" value="<?=$tpl->data['booking_date_bk'];?>" class="form-control booking_date">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                    
                             </div>
                        </div>     
                    </fieldset>    
                    <fieldset class="form-group block_service_type_1">
                        <label class="form-label"><?=$CMS->lang['booking_hours'];?></label>
                        <!-- Booking hours morning -->
                        <? foreach ($tpl->b_hours_morning as $key => $value) { ?>
                        <div class="checkbox-detailed">
                            <input type="radio" name="booking_hours" id="check-det-<?=$value;?>"  value="<?=$value;?>" <?=$value == $tpl->data['booking_hours'] ? "checked" : "";?>>
                            <label for="check-det-<?=$value;?>">
								<span class="checkbox-detailed-tbl">
									<span class="checkbox-detailed-cell">
										<span class="checkbox-detailed-title"><?=$CMS->vars['hours_time_format'] == 12 ? date("g:i A",strtotime($value)) : $value; ?></span>
									</span>
								</span>
                            </label>
                        </div>
                        <? } ?>
                        <!-- Emd Booking hours morning -->
                        <!-- Booking hours afternoon -->
                        <? foreach ($tpl->b_hours_afternoon as $key => $value) { ?>
                            <div class="checkbox-detailed">
                                <input type="radio" name="booking_hours" id="check-det-<?=$value;?>"  value="<?=$value;?>" <?=$value == $tpl->data['booking_hours'] ? "checked" : "";?>>
                                <label for="check-det-<?=$value;?>">
								<span class="checkbox-detailed-tbl">
									<span class="checkbox-detailed-cell">
										<span class="checkbox-detailed-title"><?=$CMS->vars['hours_time_format'] == 12 ? date("g:i A",strtotime($value)) :  $value; ?></span>
									</span>
								</span>
                                </label>
                            </div>
                        <? } ?>
                        <!-- Emd Booking hours afternoon -->
                    </fieldset>
                </div><!-- col-md-6-->

                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 cus_show_info" style="display:<?=$tpl->data['customer']['cus_id'] > 0 ? "block" : "none";?>">
                        <h4 class="cus_show_info"><?=$CMS->lang['cus_info'];?></h4>
                        <div class="form-group row cus_show_info">
                            <label class="col-xl-3 form-control-label2"><?=$CMS->lang['cus_realname'];?>:</label>
                            <div class="col-xl-9 form-control-span2" id="cus_name_display"><?=$tpl->data['customer']['cus_full_name'];?></div>
                        </div>
                        <div class="form-group row cus_show_info">
                            <label class="col-xl-3 form-control-label2"><?=$CMS->lang['cus_email'];?>:</label>
                            <div class="col-xl-9 form-control-span2" id="cus_email"><?=$tpl->data['customer']['cus_email'];?></div>
                        </div>
                        <div class="form-group row cus_show_info">
                            <label class="col-xl-3 form-control-label2"><?=$CMS->lang['cus_address'];?>:</label>
                            <div class="col-xl-9 form-control-span2" id="cus_address"><?=$tpl->data['customer']['cus_address'];?></div>
                        </div>
                        <div class="form-group row cus_show_info">
                            <label class="col-xl-3 form-control-label2"><?=$CMS->lang['cus_phone'];?>:</label>
                            <div class="col-xl-9 form-control-span2" id="cus_phone"><?=$tpl->data['customer']['cus_phone'];?></div>
                        </div>
                    </div>
                    <h4><?=$CMS->lang['product_total_money'];?></h4>
                    <p class="total_price" id="total_price">0</p>
                </div><!-- col-md-6-->
            </div>
        </figure>

    </section>

    <!-- Shipping info -->
    <section class="add_table" style="display:none" id="container_shipping_info">
        <h4 class="heading" style="font-size: 16px;cursor:pointer">
            <i class="fa fa-caret-down" style="cursor:pointer;margin-right: 10px;"></i>
            <span style="font-family: robob;"><?=$CMS->lang['ship_header'];?></span>
        </h4>

        <section class="add_form main_form">
            <figure class="box-typical box-typical box-typical-padding border">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">

                        <fieldset class="form-group">
                            <div class="fl-flex-label fl-background">
                                <input type="text" class="form-control ks-rounded" id="ship_receive_name" name="ship_receive_name" placeholder="" value="<?=$tpl->data['ship_receive_name'];?>">
                                <label class="fl-label" for="ship_receive_name" style="left: 13px; right: 12px;"><?=$CMS->lang['ship_receive_name'];?></label></div>
                        </fieldset>
                        <fieldset class="form-group">
                            <div class="fl-flex-label fl-background">
                                <input type="text" class="form-control ks-rounded" id="ship_phone" name="ship_phone" placeholder="" value="<?=$tpl->data['ship_phone'];?>">
                                <label class="fl-label" for="ship_phone" style="left: 13px; right: 12px;"><?=$CMS->lang['ship_phone'];?></label></div>
                        </fieldset>
                        <fieldset class="form-group">

                            <div class="fl-flex-label fl-background">
                                <input type="text" class="form-control ks-rounded" id="ship_address" name="ship_address" placeholder="" value="<?=$tpl->data['ship_address'];?>">
                                <label class="fl-label" for="ship_address" style="left: 13px; right: 12px;"><?=$CMS->lang['ship_address'];?></label></div>
                        </fieldset>
                        <fieldset class="form-group">

                            <div class="fl-flex-label fl-background">
                                <input type="text" class="form-control ks-rounded" id="ship_location" name="ship_location" placeholder="" value="<?=$tpl->data['ship_location'];?>">
                                <label class="fl-label" for="ship_location" style="left: 13px; right: 12px;"><?=$CMS->lang['ship_location'];?></label></div>
                        </fieldset>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <fieldset class="form-group">

                            <div class="fl-flex-label fl-background">
                                <input type="text" class="form-control ks-rounded" id="ship_code" name="ship_code" placeholder="" value="<?=$tpl->data['ship_code'];?>">
                                <label class="fl-label" for="ship_code" style="left: 13px; right: 12px;"><?=$CMS->lang['ship_code'];?></label></div>

                        </fieldset>
                        <fieldset class="form-group">

                            <div class="fl-flex-label fl-background">
                                <input type="text" class="form-control ks-rounded" id="ship_weight" name="ship_weight" placeholder="" value="<?=$tpl->data['ship_weight'];?>">
                                <label class="fl-label" for="ship_weight" style="left: 13px; right: 12px;"><?=$CMS->lang['ship_weight'];?></label></div>
                        </fieldset>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                <fieldset class="form-group">

                                    <div class="fl-flex-label fl-background">
                                        <input type="text" class="form-control ks-rounded" id="ship_long" name="ship_long" placeholder="" value="<?=$tpl->data['ship_long'];?>">
                                        <label class="fl-label" for="ship_long" style="left: 13px; right: 12px;"><?=$CMS->lang['ship_long'];?></label></div>
                                </fieldset>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                <fieldset class="form-group">

                                    <div class="fl-flex-label fl-background">
                                        <input type="text" class="form-control ks-rounded" id="ship_wide" name="ship_wide" placeholder="" value="<?=$tpl->data['ship_wide'];?>">
                                        <label class="fl-label" for="ship_wide" style="left: 13px; right: 12px;"><?=$CMS->lang['ship_wide'];?></label></div>
                                </fieldset>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                <fieldset class="form-group">

                                    <div class="fl-flex-label fl-background">
                                        <input type="text" class="form-control ks-rounded" id="ship_height" name="ship_height" placeholder="" value="<?=$tpl->data['ship_height'];?>">
                                        <label class="fl-label" for="ship_height" style="left: 13px; right: 12px;"><?=$CMS->lang['ship_height'];?></label></div>
                                </fieldset>
                            </div>
                        </div>
                        <fieldset class="form-group">

                            <select class="form-control" name="ship_service_type" id="ship_service_type">
                                <!-- Shipping type -->
                                <? for ($s = 0; $s <= 2; $s++) { ?>
                                    <option value='<?=$s;?>' <?=($s == $tpl->data['ship_service_type'])?"selected":"";?>><?=$CMS->lang['ship_service_type_'.$s];?></option>
                                <? } ?>
                                <!-- Shipping type -->
                            </select>
                        </fieldset>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <fieldset class="form-group">
                            <label class="form-label pull-left" ><?=$CMS->lang['ship_deliver'];?></label>

                            <select class="select2" name="ship_deliver">
                                <?=$CMS->partner_delivery->get_list_partner_delivery($tpl->data['ship_deliver']);?>
                            </select>
                        </fieldset>

                        <fieldset class="form-group">
                            <div class="fl-flex-label fl-background">
                                <input type="text" class="form-control ks-rounded" id="ship_deliver_fee" name="ship_deliver_fee" placeholder="" value="<?=$tpl->data['ship_deliver_fee'];?>">
                                <label class="fl-label" for="ship_deliver_fee" style="left: 13px; right: 12px;"><?=$CMS->lang['ship_deliver_fee'];?></label></div>
                        </fieldset>

                    </div>
                </div>
            </figure>

        </section>
    </section>
    <!-- End Shipping-->

    <script>
        var pFrmType = 'transaction';
    </script>

    <?=$CMS->global->htmlTableProduct($tpl->request_product,1);?>

    <div class="block_service_type_0">
        <?= $CMS->global->htmlTableAsset($tpl->request_asset); ?>
    </div>

    <section class="add_form main_form">
        <figure class="box-typical box-typical box-typical-padding border">
            <div class="row">
                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-122">
                            <fieldset class="form-group">

                            </fieldset>
                        </div>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">

                        </div>
                    </div>
                </div>
                <div class="col-xl-8 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="row">
                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right"><?=$CMS->lang['gsubtotal'];?>:</div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-xs-3"><span class="subtotal-show">0 </span></div>
                    </div>
                    <div class="row">
                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right"><?=$CMS->lang['gdiscount'];?>:</div>
                        <!--<div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-xs-3 "><span class="discount-show">0</span></div>-->
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="input-group">
                                <input name="total_discount_value" id="total-discount-show" onchange="calculate_money();" type="number" class="form-control total-discount-show" value="<?=$tpl->data['data_bk']['ord_total_discount'];?>">
                                <div class="input-group-btn" id="ord_discount_dropdown">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">%<span class="caret"></span></button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" value="0">%</a>
                                        <a class="dropdown-item" value="1"><?=$CMS->vars['currency_type']?></a>
                                    </div>
                                    <input onchange="calculate_money();" type="hidden" name="total_discount_type" value="1" id="total_discount_type">
                                    <script>
                                        dropdownInput($("#ord_discount_dropdown"), $("#total_discount_type"));
                                    </script>
                                </div><!-- /btn-group -->
                            </div>
                        </div>
                    </div>
                    <?if($CMS->vars['enabled_commission']){?>
                        <div class="row">
                            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right"><?=$CMS->lang['gcommission'];?>:</div>
                            <!--<div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-xs-3 "><span class="commission-show">0</span></div>-->
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="input-group">
                                    <input onchange="changeCommissionItems();" id="total-commission-show" type="number" class="form-control total-commission-show" value="0">
                                    <div class="input-group-btn" id="ord_commission_dropdown">
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">%<span class="caret"></span></button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" value="0">%</a>
                                            <a class="dropdown-item" value="1"><?=$CMS->vars['currency_type']?></a>
                                        </div>
                                        <input onchange="changeCommissionItems();" type="hidden" name="total_commission_type" value="0" id="total_commission_type">
                                        <script>
                                            dropdownInput($("#ord_commission_dropdown"), $("#total_commission_type"));
                                        </script>
                                    </div><!-- /btn-group -->
                                </div>
                            </div>
                        </div>
                    <?}?>
                    <div class="row">
                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right"><?=$CMS->lang['gtax'];?>:</div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-xs-3"><span class="tax-show">0</span></div>
                    </div>
                    <div class="row">
                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right"><?=$CMS->lang['gtotal'];?>:</div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-xs-3"><span class="total-show">0</span></div>
                    </div>
                </div>
            </div>
        </figure>
    </section>

<?=$tpl->action_footer;?>


</form>
<script>
    $(document).ready(function(){
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














