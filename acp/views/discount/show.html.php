<section class="add_form main_form">
    <figure class="heading">
        <h3><?= $CMS->lang['menu_discount']; ?>: <?= $tpl->data['discount_title']; ?></h3>
        <a href="<?= $CMS->vars['root_domain']; ?>/?site=discount<?= $CMS->class->search->url_return; ?>" title=""><span
                    class="font-icon font-icon-del"></span></a>
    </figure>
    <figure class="box-typical box-typical box-typical-padding border">
        <div class="row">
            <div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['discount_name']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['discount_name']; ?>
                            <div class="btn-group">
                                <button type="button" class="btn btn-inline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span id="discountCodeNums_<?=$tpl->data['discount_id']?>" style="display: inline-block"><?=$tpl->data['discount_num_items']?></span> <?=$CMS->lang['code']?>
                                </button>
                                <div class="dropdown-menu" style="right:inherit !important">
                                    <a class="dropdown-item" onclick="openAddItemsPopup(<?=$tpl->data['discount_id']?>)"><i class="fa fa-plus-square" aria-hidden="true"></i> <?=$CMS->lang['add_codes']?></a>
                                    <a class="dropdown-item" href="<?="{$CMS->vars['root_domain']}/?site=discount_items&discount_id={$tpl->data['data_bk']['discount_id']}";?>"><i class="fa fa-search" aria-hidden="true"></i> <?=$CMS->lang['search_codes']?></a>
                                    <a class="dropdown-item"><i class="fa fa-trash" aria-hidden="true"></i> <?=$CMS->lang['delete_codes']?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['discount_period_time']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['discount_period_time']; ?></div>
                    </div>
                </fieldset>

                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['discount_value']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['discount_value']; ?></div>
                    </div>
                </fieldset>

                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['discount_num_chars']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['discount_num_chars']; ?></div>
                    </div>
                </fieldset>
            </div>
            <div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['discount_status']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['discount_status']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['user_id']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['user_id']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['discount_time']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['discount_time']; ?></div>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
                <fieldset class="form-group row">
                    <label class="col-xl-2 form-control-label2"><?= $CMS->lang['discount_description']; ?></label>
                    <div class="col-xl-10 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['discount_description']; ?></div>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
                <fieldset class="form-group row">
                    <label class="col-xl-2 form-control-label2"><?= $CMS->lang['discount_content']; ?></label>
                    <div class="col-xl-10 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['discount_content']; ?></div>
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
                        <a onclick="delete_confirm('<?= $CMS->vars['root_domain']; ?>/?site=discount&act=delete&id=<?= $tpl->data['discount_id']; ?>');" title=""><i class="fa  fa-mail-reply"></i><?= $CMS->lang['delete']; ?></a></li>
                    <li>
                        <a href="<?= $CMS->vars['root_domain']; ?>/?site=discount&act=edit&id=<?= $tpl->data['discount_id']; ?>"><i class="fa fa-file-text-o"></i><?= $CMS->lang['edit']; ?></a></li>
                </ul>
            </div>
        </div>

    <?php } else { ?>
        <a onclick="delete_confirm('<?= $CMS->vars['root_domain']; ?>/?site=discount&act=delete&id=<?= $tpl->data['discount_id']; ?>');"
           class="btn btn-inline btn-primary ladda-button pull-right add_cart"><?= $CMS->lang['delete']; ?></a>
        <a href="<?= $CMS->vars['root_domain']; ?>/?site=discount&act=edit&id=<?= $tpl->data['discount_id']; ?>"
           class="btn btn-inline btn-primary ladda-button pull-right add_cart"><?= $CMS->lang['edit']; ?></a>
    <? } ?>
</section>

<?= $tpl->comment; ?>
<?= $tpl->logs; ?>