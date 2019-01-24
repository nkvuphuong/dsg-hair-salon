<form enctype="multipart/form-data" method="post" id="form_<?= $CMS->input['site'] ?>"
      action="<?= $CMS->vars['root_domain'] ?>/?site=<?= $CMS->input['site'] ?>&act=<?= $tpl->act ?><?= $CMS->input['id'] ? "&id={$CMS->input['id']}" : ""; ?>"
      onsubmit="return check_form(this.id);">

    <section class="add_form main_form">
        <figure class="heading">
            <h3><?= $tpl->header_title ?></h3>
            <a href="<?= $CMS->vars['root_domain'] ?>/?site=<?= $CMS->input['site'] ?>" title=""><span
                        class="font-icon font-icon-del"></span></a>
        </figure>

        <div class="row">
            <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12" style="float:none;margin:0px auto;">
                <section class="tabs-section">
                    <div class="tab-content">
                        <div class="form-group">
                            <div class="fl-flex-label">
                                <input class="form-control" type="text" name="attributes_category_url"
                                       value="<?= $tpl->data['attributes_category_url'] ?>" data-validation="[NOTEMPTY]"
                                       data-validation-message="<?= $CMS->lang['incomplete_url'] ?>"
                                       placeholder="<?= $CMS->lang['attributes_category_url'] ?>">
                            </div>
                        </div>
                        <!-- End - Tab meta -->
                    </div>
                </section>
            </div>
        </div>


    </section>

    <section class="add_cart_footer">
        <?= $CMS->global->footer_back(['list' => "{$CMS->vars['root_domain']}/?site={$CMS->input['site']}"]) ?>
        <?= $CMS->input['act'] == 'add' || $CMS->input['act'] == 'add_do' ? $CMS->global->footer_save("{$CMS->input['site']}") : $CMS->global->footer_edit() ?>
    </section>

</form>
<script language="javascript">
    $(document).ready(function () {
        validate_form_custom("#form_<?=$CMS->input['site']?>", "a.act_submit_save, a.act_submit_save, a.act_submit_save, [type='submit']");
    });
</script>