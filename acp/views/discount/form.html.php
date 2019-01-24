<form enctype="multipart/form-data" method="post" id="form_<?=$CMS->input['site']?>" action="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>&act=<?=$tpl->act?><?=$CMS->input['id'] ? "&id={$CMS->input['id']}" : "";?>" onsubmit="return check_form(this.id);">

    <section class="add_form main_form">
        <figure class="heading">
            <h3><?=$tpl->header_title?></h3>
            <a href="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>" title=""><span class="font-icon font-icon-del"></span></a>
        </figure>

        <figure class="box-typical box-typical-padding border" style="display: inline-block;
    width: 100%;">
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="discount_name"><?=$CMS->lang['discount_name'];?> <span style="color:red">(*)</span></label>
                                <input data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_name'];?>" type="text" class="form-control" id="discount_name" name="discount_name" value="<?=$tpl->data['discount_name']?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="discount_value"><?=$CMS->lang['discount_value'];?> <span style="color:red">(*)</span></label>
                                <input data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_value'];?>" type="number" class="form-control" id="discount_value" name="discount_value" value="<?=$tpl->data['discount_value']?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="discount_value"><?=$CMS->lang['discount_type'];?></label>
                                <select class="form-control" id="discount_type" name="discount_type">
                                    <?=$tpl->type_options;?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="discount_num_chars"><?=$CMS->lang['discount_num_chars'];?> <span style="color:red">(*)</span></label>
                                <input data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_num_chars'];?>" type="number" class="form-control" id="discount_num_chars" name="discount_num_chars" value="<?=$tpl->data['discount_num_chars'];?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="discount_start_time"><?=$CMS->lang['discount_start_time'];?></label>
                                <div class="form-control-wrapper form-control-icon-right">
                                    <input type="text" class="form-control date-picker" id="discount_start_time" name="discount_start_time" value="<?=$tpl->data['discount_start_time'];?>">
                                    <i class="font-icon font-icon-calend"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="discount_end_time"><?=$CMS->lang['discount_end_time'];?></label>
                                <div class="form-control-wrapper form-control-icon-right">
                                    <input type="text" class="form-control date-picker" id="discount_end_time" name="discount_end_time" value="<?=$tpl->data['discount_end_time'];?>">
                                    <i class="font-icon font-icon-calend"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="discount_status"><?=$CMS->lang['discount_status'];?></label>
                                <select class="form-control" name="discount_status" id="discount_status">
                                    <option <?=$tpl->selected['discount_status'][1];?> value="1"><?=$CMS->lang['discount_status_1']?></option>
                                    <option <?=$tpl->selected['discount_status'][0];?> value="0"><?=$CMS->lang['discount_status_0']?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="discount_description"><?=$CMS->lang['discount_description'];?></label>
                        <textarea name="discount_description" id="discount_description" class="form-control" rows="6"><?=$tpl->data['discount_description'];?></textarea>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="discount_content"><?=$CMS->lang['discount_content'];?></label>
                        <textarea name="discount_content" id="discount_content" class="form-control editor_texarea"><?=$tpl->data['discount_content'];?></textarea>
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