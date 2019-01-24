<form enctype="multipart/form-data" method="post" id="form_<?=$CMS->input['site']?>" action="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>&act=<?=$tpl->act?><?=$CMS->input['id'] ? "&id={$CMS->input['id']}" : "";?>" onsubmit="return check_form(this.id);">
    <section class="add_form main_form">
        <figure class="heading">
            <h3><?=$tpl->header_title?></h3>
            <a href="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>" title=""><span class="font-icon font-icon-del"></span></a>
        </figure>
        <figure class="box-typical box-typical-padding border col-md-10" style="display: inline-block;">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="with-border m-t-0"><?=$CMS->lang['shift_work_information']?></h4>
                    <div class="row match-height">
                        <div class="col-md-6">
                            <fieldset class="form-group">
                                <label class="form-label"><?=$CMS->lang['shift_work_name']?> <span style="color:red">(*)</span></label>
                                <input class="form-control " type="text" name="shift_work_name" id="shift_work_name" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_name']?>" value="<?=\lib\input::arrayValue($tpl->data, 'shift_work_name')?>">
                            </fieldset>
                        </div>
                        <div class="col-md-2">
                            <fieldset class="form-group">
                                <label class="form-label"><?=$CMS->lang['shift_work_in']?> <span style="color:red">(*)</span></label>
                                <input class="form-control time-picker" type="text" name="shift_work_in" id="shift_work_in" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_in']?>" value="<?=\lib\input::arrayValue($tpl->data, 'shift_work_in')?>">
                            </fieldset>
                        </div>
                        <div class="col-md-2">
                            <fieldset class="form-group">
                                <label class="form-label"><?=$CMS->lang['shift_work_out']?> <span style="color:red">(*)</span></label>
                                <input class="form-control time-picker" type="text" name="shift_work_out" id="shift_work_out" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_out']?>" value="<?=\lib\input::arrayValue($tpl->data, 'shift_work_out')?>">
                            </fieldset>
                        </div>
                        <div class="col-md-2">
                            <fieldset class="form-group">
                                <label class="form-label"><?=$CMS->lang['shift_work_order']?></label>
                                <input class="form-control " type="number" name="shift_work_order" id="shift_work_order" value="<?=$tpl->data['shift_work_order']?>">
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