<form enctype="multipart/form-data" method="post" id="form_<?=$CMS->input['site']?>" action="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>&act=<?=$tpl->act?><?=$CMS->input['id'] ? "&id={$CMS->input['id']}" : "";?>" onsubmit="return check_form(this.id);">

    <section class="add_form main_form">
        <figure class="heading">
            <h3><?=$tpl->header_title?></h3>
            <a href="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>" title=""><span class="font-icon font-icon-del"></span></a>
        </figure>

        <figure class="box-typical box-typical box-typical-padding border">
            <div class="row">
                <div class="col-lg-6">
                    <div class="row match-height">
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="form-control-label"><?=$CMS->lang['pos_name']?><font style="margin-left:5px" color="#FF0000">(*)</font></label>
                                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                                    <input class="form-control" type="text" name="pos_name" value="<?=$tpl->data['pos_name']?>" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_name']?>">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="form-control-label"><?=$CMS->lang['pos_key']?><font style="margin-left:5px" color="#FF0000">(*)</font></label>
                                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                                    <input class="form-control" type="text" name="pos_key" value="<?=$tpl->data['pos_key']?>" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_key']?>">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="form-control-label"><?=$CMS->lang['pos_mode']?><font style="margin-left:5px" color="#FF0000">(*)</font></label>
                                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                                    <select class="form-control select2" name="pos_mode" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_mode']?>">
                                        <?=\core\ezy::render('mode_options');?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="form-control-label"><?=$CMS->lang['pos_interval']?></label>
                                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                                    <input class="form-control" type="number" name="pos_interval" value="<?=$tpl->data['pos_interval']?>">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="form-control-label"><?=$CMS->lang['pos_type']?><font style="margin-left:5px" color="#FF0000">(*)</font></label>
                                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                                    <select class="form-control select2" name="pos_type" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_type']?>">
                                        <?=\core\ezy::render('type_options');?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!--End col-lg-4-->

                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="form-control-label"><?=$CMS->lang['pos_width']?></label>
                                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                                    <input class="form-control" type="number" name="pos_width" value="<?=$tpl->data['pos_width']?>">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="form-control-label"><?=$CMS->lang['pos_height']?></label>
                                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                                    <input class="form-control" type="number" name="pos_height" value="<?=$tpl->data['pos_height']?>">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group row">
                                <label class="form-control-label"><?=$CMS->lang['pos_desc']?></label>
                                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                                    <textarea class="form-control" rows="6" name="pos_desc"><?=$tpl->data['pos_desc']?></textarea>
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