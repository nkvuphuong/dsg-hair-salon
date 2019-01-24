<script src="/acp/jsacp/discount.js"></script>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="row">
        <div class="col-xl-8 col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label for="discount_num_chars"><strong><?= $CMS->lang['di_code']; ?></strong> <span style="color:red">(*)</span> <a  onclick="randomDiscountCode($('#di_code'),$('#di_num_chars').val())" style="float: right"><?=$CMS->lang['generate_code_automatically']?></a></label>

                        <input type="text" class="form-control" id="di_code"
                               data-validation="[NOTEMPTY]"
                               data-validation-message="<?= $CMS->lang['incomplete_code']; ?>"
                               name="di_code" value="<?=$tpl->data['di_code'];?>">

                        <!--<div class="input-group">
                    <input type="text" class="form-control" id="di_code"
                           data-validation="[NOTEMPTY]"
                           data-validation-message="<?/*= $CMS->lang['incomplete_code']; */?>"
                           name="di_code" value="<?/*=$tpl->data['di_code'];*/?>">
                    <span class="input-group-addon"
                          onclick="randomDiscountCode($('#di_code'),$('#di_num_chars').val())"><i
                                class="fa fa-random" aria-hidden="true"></i></span>
                </div>-->
                    </div>
                    <hr/>
                </div>
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label for="discount_num_chars"><strong><?=$CMS->lang['choose_discount'];?></strong> <span
                                            style="color:red">(*)</span></label>
                                <select class="form-control" name="discount_id" name="discount_id">
                                    <? if($tpl->discount_data){ ?>
                                        <option onclick="" value="<?=$tpl->discount_data['discount_id']?>" selected><?=$tpl->discount_data['discount_name']?></option>
                                    <? } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-xs-4" style="display: none">
                    <div class="form-group">
                        <label for="discount_num_chars">&nbsp;</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="di_num_chars"
                                   name="di_num_chars" value="<?=$tpl->data['di_num_chars'];?>">
                            <span class="input-group-addon"><?=$CMS->lang['char']?></span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="row">
                        <div class="col-xl-5 col-lg-6 col-md-6 col-sm-7 col-xs-8">
                            <label for="discount_num_chars"><strong style="font-size: 13px"><?=$CMS->lang['di_times_desc']?></strong></label>
                            <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                                <input type="number" class="form-control" id="di_times" name="di_times" value="<?=$tpl->data['di_times'];?>"
                                       min="1">
                                <div class="input-group-addon">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input <?=$tpl->checked_unlimited;?> type="checkbox" class="form-check-input" name="unlimited"
                                                                                 onchange="setUnlimitedTimesDiscount($(this))">
                                            <?=$CMS->lang['unlimited']?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr />
                </div>
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="row">
                        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-xs-4">
                            <label for="di_value"><?=$CMS->lang['discount_type']?></label>
                            <select class="form-control" id="di_type" name="di_type" onchange="changeDiscountType($(this).val(), $('.change_discout_type'))">
                                <?= $tpl->type_options; ?>
                            </select>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-5 col-xs-8">
                            <div class="form-group">
                                <label for="di_value"><?=$CMS->lang['discount']?></label>
                                <div class="input-group">
                                    <input data-validation="[NOTEMPTY]"
                                           data-validation-message="<?= $CMS->lang['incomplete_value']; ?>"
                                           type="number" class="form-control" id="di_value"
                                           name="di_value" value="<?=$tpl->data['di_value'];?>">
                                    <div class="input-group-addon change_discout_type"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-4 col-sm-5 col-xs-12">
                            <div class="form-group">
                                <label for="di_apply_for" ><?=$CMS->lang['di_apply_for']?></label>
                                <select class="form-control" name="di_apply_for" onchange="setApplyGroupDiscount($(this))">
                                    <?=$tpl->apply_for_options;?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8 col-xs-8" apply_for="amount_from">
                            <label for="di_apply_rules[amount_from]"><?=$CMS->lang['di_apply_for_amount_from']?></label>
                            <input type="number" class="form-control" id="di_apply_rules[amount_from]"
                                   name="di_apply_rules[amount_from]" value="<?=$tpl->data['di_apply_rules']['amount_from'];?>" min="1">
                        </div>
                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-xs-9" apply_for="product">
                            <div class="form-group">
                                <label for="di_apply_rules[product]"><?=$CMS->lang['di_apply_for_product']?></label>
                                <select style="width: 100%" class="form-control" multiple="multiple"
                                        id="di_apply_rules[product]" name="di_apply_rules[product][]">
                                    <? if($tpl->product_data){
                                        foreach($tpl->product_data as $product) { ?>
                                            <option selected value="<?=$product['product_id']?>"><?=$product['product_name'][$CMS->vars['default_language']];?></option>
                                        <? } } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-xs-9" apply_for="product_group">
                            <div class="form-group">
                                <label for="di_apply_rules[product_group]"><?=$CMS->lang['di_apply_for_product_group']?></label>
                                <select style="width: 100%" class="form-control" multiple="multiple"
                                        id="di_apply_rules[product_group]" name="di_apply_rules[product_group][]">
                                    <?=$tpl->options['di_apply_rules']['product_group']?>
                                    <? if($tpl->product_group_data){
                                        foreach($tpl->product_group_data as $product_group) { ?>
                                            <option selected value="<?=$product_group['product_group_id']?>"><?=$product_group['product_group_name'];?></option>
                                        <? } } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-xs-9" apply_for="customer_group">
                            <div class="form-group">
                                <label for="di_apply_rules[customer_group]"><?=$CMS->lang['di_apply_for_customer_group']?></label>
                                <select style="width: 100%" class="form-control" multiple="multiple"
                                        id="di_apply_rules[customer_group]"
                                        name="di_apply_rules[customer_group][]">
                                    <?=$tpl->options['di_apply_rules']['customer_group']?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-xs-12" style="background-color: lightgrey">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                    <label for="discount_start_time"><?= $CMS->lang['discount_start_time']; ?></label>
                    <div class="form-control-wrapper form-control-icon-right">
                        <input type="text" class="form-control date-picker" id="di_start_time"
                               name="di_start_time"
                               value="<?=$tpl->data['di_start_time'];?>">
                        <i class="font-icon font-icon-calend"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                    <label for="discount_end_time"><?= $CMS->lang['discount_end_time']; ?></label>
                    <div class="form-control-wrapper form-control-icon-right">
                        <input type="text" class="form-control date-picker" id="di_end_time"
                               name="di_end_time"
                               value="<?=$tpl->data['di_end_time'];?>">
                        <i class="font-icon font-icon-calend"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script language="javascript">
    $(document).ready(function () {
        select2Init();
        select2MultipleInit($("[name='di_apply_rules[product][]']"), "?site=discount&subact=load_product");
        select2MultipleInit($("[name='di_apply_rules[product_group][]']"), "?site=discount&subact=load_product_group");
        select2MultipleInit($("[name='di_apply_rules[customer_group][]']"), "?site=discount&subact=load_customer_group");
        changeItemsClassify();
    })
</script>
