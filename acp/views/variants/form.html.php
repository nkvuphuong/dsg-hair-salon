<form enctype="multipart/form-data" method="post" id="form_<?=$CMS->input['site']?>" action="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>&act=<?=$tpl->act?><?=$CMS->input['id'] ? "&id={$CMS->input['id']}" : "";?>" onsubmit="return check_form(this.id);">
    <section class="add_form main_form">
        <figure class="heading">
            <h3><?=$tpl->header_title?></h3>
            <a href="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>" title=""><span class="font-icon font-icon-del"></span></a>
        </figure>
        <figure class="box-typical box-typical-padding border col-md-10" style="display: inline-block;">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="with-border m-t-0"><?=$CMS->lang['variants_information']?></h4>
                    <div class="row match-height">
                        <div class="col-md-6">
                            <fieldset class="form-group">
                                <label class="form-label"><?=$CMS->lang['variants_name']?> <span style="color:red">(*)</span></label>
                                <input class="form-control " type="text" name="variants_name" id="variants_name" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_name']?>" value="<?=\lib\input::arrayValue($tpl->data, 'variants_name')?>">
                            </fieldset>
                        </div>
                        <div class="col-md-2">
                            <fieldset class="form-group">
                                <label class="form-label"><?=$CMS->lang['variants_in']?> <span style="color:red">(*)</span></label>
                                <input class="form-control time-picker" type="text" name="variants_in" id="variants_in" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_in']?>" value="<?=\lib\input::arrayValue($tpl->data, 'variants_in')?>">
                            </fieldset>
                        </div>
                        <div class="col-md-2">
                            <fieldset class="form-group">
                                <label class="form-label"><?=$CMS->lang['variants_out']?> <span style="color:red">(*)</span></label>
                                <input class="form-control time-picker" type="text" name="variants_out" id="variants_out" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_out']?>" value="<?=\lib\input::arrayValue($tpl->data, 'variants_out')?>">
                            </fieldset>
                        </div>
                        <div class="col-md-2">
                            <fieldset class="form-group">
                                <label class="form-label"><?=$CMS->lang['variants_order']?></label>
                                <input class="form-control " type="number" name="variants_order" id="variants_order" value="<?=$tpl->data['variants_order']?>">
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </figure>
    </section>
    <section class="add_cart_footer">
            <?=$CMS->global->footer_back(['list' => "{$CMS->vars['root_domain']}/?site={$CMS->input['site']}"])?>
            <?= $CMS->input['act'] == 'add' || $CMS->input['act'] == 'add_do' ? $CMS->global->footer_save("{$CMS->input['site']}") : $CMS->global->footer_edit()?>
    </section>

</form>
<script language="javascript">
    $(document).ready(function () {
        validate_form_custom("#form_<?=$CMS->input['site']?>", "a.act_submit_save, a.act_submit_save, a.act_submit_save, [type='submit']");

        $(".time-picker").mask("00:00", {placeholder: "__:__"});
        $(".time-picker").datetimepicker({format: "HH:mm"});
    });
</script>