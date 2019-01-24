<section class="main_form">
    <figure class="heading">
        <h3>Add gift card for customer</h3>
        <a href="<?=$CMS->vars['root_domain'];?>/?site=redeem" title=""><span class="font-icon font-icon-del"></span></a>
    </figure>
    <form method="post" name="form_giftcard_item" id="form_giftcard_item" action="<?=$CMS->vars['root_domain'];?>/?site=redeem&act=add_do">
        <section class="box-typical box-typical-padding border">
            <div class="row">
                <div class="col-xl-12"><h5 class="m-t-lg with-border"><?=$CMS->lang['title_info_giftcard'];?></h5></div>
                <div class="col-xl-3">
                      <fieldset class="form-group">
                      <label class="form-label" ><?=$CMS->lang['title_choose_giftcard'];?> <span style="color:red">(*)</span></label>
                      <div class="form-control-wrapper">
                            <select class="form-control auto_select" name="product_id" defaultvalue="<?=$tpl->input['product_id'];?>" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['plz_choose_giftcard'];?>">
                                <option value=""><?=$CMS->lang['plz_choose_giftcard'];?></option>
                                <? foreach ($tpl->dataGiftCard as $data) { ?>
                                <option value="<?=$data['product_id'];?>"><?=$data['product_name'][$CMS->vars['default_language']];?></option>
                                <? }?>
                            </select>
                      </div>
                      </fieldset>
                </div>
                <div class="col-xl-3">
                    <fieldset class="form-group">
                      <label class="form-label" ><?=$CMS->lang['giftcard_amount'];?> <span style="color:red">(*)</span></label>
                      <div class="form-control-wrapper">
                          <input class="form-control" type="text" name="gitem_amount" onkeypress="return check_enter_number(event, this);" maxlength="4" value="<?=$tpl->input['gitem_amount'];?>" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['title_enter_amount_incomplete'];?>">
                      </div>
                    </fieldset>
                </div>
                <div class="col-xl-3">
                    <fieldset class="form-group">
                      <label class="form-label" ><?=$CMS->lang['giftcard_code_old'];?></label>
                      <div class="form-control-wrapper">
                          <input class="form-control" type="text" name="gitem_code_old" value="<?=$tpl->input['gitem_code_old'];?>">
                      </div>
                    </fieldset>
                </div>
            </div>
            <div class="row">
              <div class="col-xl-12"><h5 class="m-t-lg with-border"><?=$CMS->lang['title_info_customer'];?></h5></div>
              <div class="col-xl-3">
                  <fieldset class="form-group">
                    <label class="form-label" ><?=$CMS->lang['cus_name'];?> <span style="color:red">(*)</span></label>
                    <div class="form-control-wrapper">
                        <input class="form-control" type="text" name="cus_name" value="<?=$tpl->input['cus_name'];?>" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['title_enter_customer_name'];?>">
                    </div>
                  </fieldset>
              </div>
              <div class="col-xl-3">
                  <fieldset class="form-group">
                    <label class="form-label" ><?=$CMS->lang['cus_email'];?> <span style="color:red">(*)</span></label>
                    <div class="form-control-wrapper">
                        <input class="form-control" type="text" name="cus_email" value="<?=$tpl->input['cus_email'];?>" data-validation="[EMAIL]" data-validation-message="<?=$CMS->lang['title_enter_customer_email'];?>">
                    </div>
                  </fieldset>
              </div>
              <div class="col-xl-3">
                  <fieldset class="form-group">
                    <label class="form-label" ><?=$CMS->lang['cus_phone'];?> <span style="color:red">(*)</span></label>
                    <div class="form-control-wrapper">
                        <input class="form-control" type="text" name="cus_phone" value="<?=$tpl->input['cus_phone'];?>" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['title_enter_customer_phone'];?>" c_type=phone />
                    </div>
                  </fieldset>
              </div>
              <div class="col-xl-9">
                  <fieldset class="form-group">
                    <label class="form-label" ><?=$CMS->lang['giftcard_note'];?></label>
                    <div class="form-control-wrapper">
                        <textarea name="gitem_note" class="form-control" rows="7"><?=$tpl->input['gitem_note'];?></textarea>
                    </div>
                  </fieldset>
              </div>
            </div>
            <div class="row">
              <div class="col-xl-3"><button type="submit" class="btn btn_primary btn_submit"><?=$CMS->lang['btn_add_giftcard_item'];?></button></div>
            </div>
        </section>
        <section class="add_cart_footer">
          <?=$CMS->global->footer_back($tpl->url_back);?>
        </section>
    </form>
    <script>
           $(document).ready(function(){
              validate_form_custom("#form_giftcard_item",".btn_submit");
              $("[c_type=phone]").mask("<?=$CMS->vars['phone_format'];?>", {placeholder: "<?=$CMS->vars['phone_format'];?>"});
          ;});

    </script>
</section>

