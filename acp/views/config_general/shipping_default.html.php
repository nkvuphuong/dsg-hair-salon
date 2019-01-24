<section class="box-typical scrollable" style="padding: 15px;">
<h5 class="m-t-lg with-border" style="margin: 0 0 15px;"><?=$CMS->lang['title_flat_rate_shipping'];?></h5>

<form method="POST" id="form_default_shipping" name="form_default_shipping" action="<?=$CMS->vars['root_domain'];?>/?site=config_general&act=shipping_fee&subact=shipping_default">
<div class="row">
    <div class="col-md-6">
        <div class="form-group row">
            <label class="col-xs-12 form-control-label">
                <span><?=$CMS->lang['title_default_shiping_location'];?></span>
                <p style="color: red; font-size: 12px;margin: 0;"><?=$CMS->lang['title_default_shiping_location_note'];?></p>
            </label>
            <div class="col-xs-12">
               <select name="config[select][default_shiping_location]" class="form-control select2" defaultvalue="<?=$CMS->vars['default_shiping_location'];?>" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['default_shiping_location_error'];?>">
                    <?=$tpl->option_location;?>
                </select>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group row">
            <label class="col-xs-12 form-control-label">
                <span><?=$CMS->lang['title_default_shiping_free'];?> (<?=$CMS->vars['currency_type'];?>)</span>
                <p style="color: red; font-size: 12px;margin: 0;"><?=$CMS->lang['title_default_shiping_free_note'];?></p>
            </label>
            <div class="col-xs-12">
               <input class="form-control" type="text" name="config[input][default_shiping_free]" value="<?=$CMS->vars['default_shiping_free'];?>" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);">
            </div>
        </div>
    </div>
</div>
<div class="text-center clearfix">
    <button type="submit" class="btn btn-inline btn-primary ladda-button add_cart" id="form_default_shipping_submit">
        <span class="ladda-label"><?=$CMS->lang['default_shiping_btn_update'];?></span>
    </button>
</div>
</form>

</section>

<script type="text/javascript">
    $(document).ready(function(){
        validate_form_custom("#form_default_shipping", "#form_default_shipping_submit");
    });
</script>

<!-- Logs default shipping -->
<?=$tpl->logs;?>