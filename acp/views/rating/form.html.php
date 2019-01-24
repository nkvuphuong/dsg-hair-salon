<form enctype="multipart/form-data" method="post" id="form_<?=$CMS->input['site']?>" action="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>&act=<?=$tpl->act?><?=$CMS->input['id'] ? "&id={$CMS->input['id']}" : "";?>" onsubmit="return check_form(this.id);">
    <section class="add_form main_form">
        <figure class="heading">
            <h3><?=$tpl->header_title?></h3>
            <a href="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>" title=""><span class="font-icon font-icon-del"></span></a>
        </figure>
        <figure class="box-typical box-typical-padding border col-md-10" style="display: inline-block;">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="with-border m-t-0"><?=$CMS->lang['rating_information']?></h4>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row match-height">
                                <div class="col-md-4">
                                    <fieldset class="form-group">
                                        <label class="form-label"><?=$CMS->lang['rating_name']?> <span style="color:red">(*)</span></label>
                                        <input class="form-control " type="text" name="rating_name" id="rating_name" data-validation="[NOTEMPTY]" data-validation-message="<?=\lib\input::lang('incomplete_name','')?>" value="<?=\lib\input::arrayValue($tpl->data, 'rating_name')?>">
                                    </fieldset>
                                </div>
                                <div class="col-md-3">
                                    <fieldset class="form-group">
                                        <label class="form-label"><?=$CMS->lang['rating_value']?> <span style="color:red">(*)</span></label>
                                        <input class="form-control" type="number" name="rating_value" id="rating_value" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_value']?>" value="<?=$tpl->data['rating_value']?>">
                                    </fieldset>
                                </div>
                                <div class="col-md-3">
                                    <fieldset class="form-group">
                                        <label class="form-label"><?=$CMS->lang['rating_status']?></label>
                                        <select class="form-control" name="rating_status" id="rating_status">
                                            <?=$tpl->status_options;?>
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-md-2">
                                    <fieldset class="form-group">
                                        <label class="form-label"><?=$CMS->lang['rating_order']?></label>
                                        <input type="number" class="form-control" id="rating_order" name="rating_order" value="<?=$tpl->data['rating_order']?>">
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset class="form-group">
                                        <label class="form-label"><?=$CMS->lang['rating_label']?> <span style="color:red">(*)</span></label>
                                        <input class="form-control " type="text" name="rating_label" id="rating_label" data-validation="[NOTEMPTY]" data-validation-message="<?=\lib\input::lang('incomplete_label','')?>" value="<?=\lib\input::arrayValue($tpl->data, 'rating_label')?>">
                                    </fieldset>
                                </div>
                            </div>
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
    });
</script>