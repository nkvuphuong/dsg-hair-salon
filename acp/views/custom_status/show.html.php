<section class="add_form main_form">
    <figure class="heading">
        <h3><?= $CMS->lang['menu_custom_status']; ?>: <?= $tpl->data['status_name']; ?></h3>
        <a href="<?= $CMS->vars['root_domain']; ?>/?site=custom_status<?= $CMS->class->search->url_return; ?>" title=""><span
                    class="font-icon font-icon-del"></span></a>
    </figure>
    <figure class="box-typical box-typical box-typical-padding border">
        <div class="row">
            <div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?=$CMS->lang['custom_status_name'];?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold">
                            <?=$tpl->data['status_name'];?>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?=$CMS->lang['title_status_order'];?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold">
                            <span style="color: red;"><?=$CMS->lang['order_status_'.$tpl->data['ord_status']];?></span>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?=$CMS->lang['custom_status_display'];?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?=$CMS->lang['custom_status_display_'.$tpl->data['status_display']];?></div>
                    </div>
                </fieldset>

                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?=$CMS->lang['title_show_on_step'];?>
                        <span class="question">
                            <i class="fa fa-question-circle" aria-hidden="true"></i>
                            <p class="box_answer" style="display: none;"><?=$CMS->lang['title_show_step_description'];?></p>
                        </span>
                    </label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?=$tpl->data['status_is_step'] ? "Yes" : "No";?></div>
                    </div>
                </fieldset>

                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['custom_status_description']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['status_description']; ?></div>
                    </div>
                </fieldset>
            </div>
            <div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">

                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['custom_status_sort']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['status_sort']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['custom_status_time']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['status_time']; ?></div>
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
                        <a onclick="delete_confirm('<?= $CMS->vars['root_domain']; ?>/?site=logo_positions&act=delete&id=<?= $tpl->data['pos_id']; ?>');" title=""><i class="fa  fa-mail-reply"></i><?= $CMS->lang['delete']; ?></a></li>
                    <li>
                        <a href="<?= $CMS->vars['root_domain']; ?>/?site=logo_positions&act=edit&id=<?= $tpl->data['pos_id']; ?>"><i class="fa fa-file-text-o"></i><?= $CMS->lang['edit']; ?></a></li>
                </ul>
            </div>
        </div>

    <?php } else { ?>
        <a onclick="delete_confirm('<?= $CMS->vars['root_domain']; ?>/?site=logo_positions&act=delete&id=<?= $tpl->data['pos_id']; ?>');"
           class="btn btn-inline btn-primary ladda-button pull-right add_cart"><?= $CMS->lang['delete']; ?></a>
        <a href="<?= $CMS->vars['root_domain']; ?>/?site=logo_positions&act=edit&id=<?= $tpl->data['pos_id']; ?>"
           class="btn btn-inline btn-primary ladda-button pull-right add_cart"><?= $CMS->lang['edit']; ?></a>
    <? } ?>
</section>

<?= $tpl->comment; ?>
<?= $tpl->logs; ?>