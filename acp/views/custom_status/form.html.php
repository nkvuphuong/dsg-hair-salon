<form enctype="multipart/form-data" method="post" id="form_<?=$CMS->input['site']?>" action="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>&act=<?=$tpl->act?><?=$CMS->input['id'] ? "&id={$CMS->input['id']}" : "";?>" onsubmit="return check_form(this.id);">

    <section class="add_form main_form">
        <figure class="heading">
            <div class="row">
                <div class="col-md-6">
                    <h3><?=$tpl->header_title?></h3>
                </div>
                <div class="col-md-6">
                    <a href="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>" title=""><span class="font-icon font-icon-del"></span></a>
                </div>
            </div>


        </figure>

        <figure class="box-typical box-typical box-typical-padding border">
            <div class="row">
                <div class="col-lg-6">
                    <div class="row match-height">
                        <div class="col-lg-12">
                            <div class="form-group row">
                                <label class="form-control-label"><?=$CMS->lang['custom_status_name']?><font style="margin-left:5px" color="#FF0000">(*)</font></label>
                                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                                    <input class="form-control" type="text" name="status_name" value="<?=$tpl->data['status_name']?>" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_name']?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="form-control-label"><?=$CMS->lang['title_status_order']?><font style="margin-left:5px" color="#FF0000">(*)</font></label>
                                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                                    <select class="form-control auto_select" name="ord_status" defaultvalue="<?=$tpl->data['ord_status'];?>" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_order_status']?>">
                                        <option value=""><?=$CMS->lang['title_choose_plz']?></option>
                                        <option value="0"><?=$CMS->lang['order_status_0']?></option>
                                        <option value="1"><?=$CMS->lang['order_status_1']?></option>
                                        <option value="2"><?=$CMS->lang['order_status_2']?></option>
                                        <option value="3"><?=$CMS->lang['order_status_3']?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="form-control-label"><?=$CMS->lang['custom_status_display']?></label>
                                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                                    <select class="form-control auto_select" name="status_display" defaultvalue="<?=$tpl->data['status_display'];?>">
                                        <option value="1"><?=$CMS->lang['custom_status_display_1']?></option>
                                        <option value="0"><?=$CMS->lang['custom_status_display_0']?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group row">
                                <label class="form-control-label"><?=$CMS->lang['custom_status_description']?></label>
                                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                                    <textarea class="form-control" rows="6" name="status_description"><?=$tpl->data['status_description']?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="form-control-label"><?=$CMS->lang['title_show_on_step']?>
                                    <span class="question">
                                        <i class="fa fa-question-circle" aria-hidden="true"></i>
                                        <p class="box_answer" style="display: none;"><?=$CMS->lang['title_show_step_description'];?></p>
                                      </span>
                                </label>
                                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                                    <select class="form-control auto_select" name="status_is_step" defaultvalue="<?=$tpl->data['status_is_step'];?>">
                                        <option value="1"><?=$CMS->lang['yes']?></option>
                                        <option value="0"><?=$CMS->lang['no']?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group row">
                                    <label class="form-control-label"><?=$CMS->lang['custom_status_sort']?></label>
                                    <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                                        <input class="form-control" type="text" name="status_sort" value="<?=$tpl->data['status_sort']?>" >
                                    </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-lg-2"></div>
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