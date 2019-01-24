<section class="add_form main_form">
    <figure class="heading">
        <h3><?= $CMS->lang['menu_seo']; ?>: <?= $tpl->data['seo_title']; ?></h3>
        <a href="<?= $CMS->vars['root_domain']; ?>/?site=seo<?= $CMS->class->search->url_return; ?>" title=""><span
                    class="font-icon font-icon-del"></span></a>
    </figure>
    <figure class="box-typical box-typical box-typical-padding border">
        <div class="row">
            <div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['seo_url']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold">
                            <?= $tpl->data['seo_url']; ?>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['seo_title']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['seo_title']; ?></div>
                    </div>
                </fieldset>

                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['seo_keywords']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['seo_keywords']; ?></div>
                    </div>
                </fieldset>

                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['seo_description']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['seo_description']; ?></div>
                    </div>
                </fieldset>

                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['seo_og_title']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['seo_og_title']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['seo_og_description']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['seo_og_description']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['seo_og_image']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <?if($tpl->data['seo_og_image']){?>
                            <div class="form-label semibold"><img src="<?= $tpl->data['seo_og_image']; ?>" style="max-width: 300px;"></div>
                        <?}?>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['seo_dc_title']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['seo_dc_title']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['seo_dc_subject']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['seo_dc_subject']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['seo_dc_description']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['seo_dc_description']; ?></div>
                    </div>
                </fieldset>
            </div>
            <div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['seo_time']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['seo_time']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['created_by']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['userInfo']['user_display_name']; ?></div>
                    </div>
                </fieldset>
            </div>
        </div>
    </figure>
</section>


<section class="add_cart_footer">
    <?php if ($_SESSION['is_mobile'] == true) { ?>

        <div class="btn-group dropup pull-left hidden-xl-up">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-mail-reply"></i><?= $CMS->lang['gaction']; ?>
            </button>
            <div class="dropdown-menu">
                <ul>
                    <li>
                        <a onclick="delete_confirm('<?= $CMS->vars['root_domain']; ?>/?site=seo&act=delete&id=<?= $tpl->data['seo_id']; ?>');" title=""><i class="fa  fa-mail-reply"></i><?= $CMS->lang['delete']; ?></a></li>
                    <li>
                        <a href="<?= $CMS->vars['root_domain']; ?>/?site=seo&act=edit&id=<?= $tpl->data['seo_id']; ?>"><i class="fa fa-file-text-o"></i><?= $CMS->lang['edit']; ?></a></li>
                </ul>
            </div>
        </div>

    <?php } else { ?>
        <a onclick="delete_confirm('<?= $CMS->vars['root_domain']; ?>/?site=seo&act=delete&id=<?= $tpl->data['seo_id']; ?>');"
           class="btn btn-inline btn-primary ladda-button pull-right add_cart"><?= $CMS->lang['delete']; ?></a>
        <a href="<?= $CMS->vars['root_domain']; ?>/?site=seo&act=edit&id=<?= $tpl->data['seo_id']; ?>"
           class="btn btn-inline btn-primary ladda-button pull-right add_cart"><?= $CMS->lang['edit']; ?></a>
    <? } ?>
</section>

<?= $tpl->comment; ?>
<?= $tpl->logs; ?>