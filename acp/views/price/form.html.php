<form enctype="multipart/form-data" method="post" id="form_<?=$CMS->input['site']?>" action="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>&act=<?=$tpl->act?><?=$CMS->input['id'] ? "&id={$CMS->input['id']}" : "";?>" onsubmit="return check_form(this.id);">
    <section class="add_form main_form">
        <figure class="heading">
            <h3><?=$tpl->header_title?></h3>
            <a href="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>" title=""><span class="font-icon font-icon-del"></span></a>
        </figure>
        <figure class="box-typical box-typical-padding border col-md-10" style="display: inline-block;">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="with-border m-t-0"><?=$CMS->lang['price_information']?></h4>
                    <div class="row">
                        <div class="col-md-8">
                            <fieldset class="form-group">
                                <label class="form-label"><?=$CMS->lang['price_name']?> <span style="color:red">(*)</span></label>
                                <input class="form-control " type="text" name="price_name" id="price_name" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_name']?>" value="<?=\lib\input::arrayValue($tpl->data, 'price_name')?>">
                            </fieldset>
                        </div>
                        <div class="col-md-4">
                            <fieldset class="form-group">
                                <label class="form-label"><?=$CMS->lang['price_status']?></label>
                                <select name="price_status" id="price_status" class="form-control">
                                    <?= core\ezy::render("status_options"); ?>
                                </select>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h4 class="with-border m-t-0"><?=\lib\input::lang('scope_of_application')?></h4>
                    <div class="row">
                        <div class="col-md-6">
                            <fieldset class="form-group">
                                <label class="form-label">
                                    <?=$CMS->lang['price_start_time']?>
                                    <span class="question">
                                        <i class="fa fa-question-circle" aria-hidden="true"></i>
                                        <p class="box_answer" style="display: none;"><?=\lib\input::lang('price_start_time_note')?></p>
                                    </span>
                                </label>
                                <input class="form-control date-picker" type="text" name="price_start_time" id="price_start_time" value="<?=\lib\input::arrayValue($tpl->data, 'price_start_time')?>">
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset class="form-group">
                                <label class="form-label">
                                    <?=$CMS->lang['price_end_time']?>
                                    <span class="question">
                                        <i class="fa fa-question-circle" aria-hidden="true"></i>
                                        <p class="box_answer" style="display: none;"><?=\lib\input::lang('price_end_time_note')?></p>
                                    </span>
                                </label>
                                <input class="form-control date-picker" type="text" name="price_end_time" id="price_end_time" value="<?=\lib\input::arrayValue($tpl->data, 'price_end_time')?>">
                            </fieldset>
                        </div>
                        <div class="col-md-12">
                            <fieldset class="form-group">
                                <label class="form-label">
                                    <?=$CMS->lang['price_stores']?>
                                    <span class="question">
                                        <i class="fa fa-question-circle" aria-hidden="true"></i>
                                        <p class="box_answer" style="display: none;"><?=\lib\input::lang('price_stores_note')?></p>
                                    </span>
                                </label>
                                <select name="price_stores[]" id="price_stores" class="select2" multiple>
                                    <?= core\ezy::render("store_options"); ?>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-md-12">
                            <fieldset class="form-group">
                                <label class="form-label">
                                    <?=$CMS->lang['price_cus_groups']?>
                                    <span class="question">
                                        <i class="fa fa-question-circle" aria-hidden="true"></i>
                                        <p class="box_answer" style="display: none;"><?=\lib\input::lang('price_cus_groups_note')?></p>
                                    </span>
                                </label>
                                <select name="price_cus_groups[]" id="price_cus_groups" class="select2" multiple>
                                    <?= core\ezy::render("group_customer_options"); ?>
                                </select>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <h4 class="with-border m-t-0"><?=$CMS->lang['product_information']?></h4>
                    <?= core\ezy::render("products"); ?>
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