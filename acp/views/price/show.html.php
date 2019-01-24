<section class="add_form main_form">
    <figure class="heading">
        <h3><?= $CMS->lang['menu_price']; ?>: <?= $tpl->data['price_name']; ?></h3>
        <a href="<?= $CMS->vars['root_domain']; ?>/?site=price<?= $CMS->class->search->url_return; ?>" title=""><span
                    class="font-icon font-icon-del"></span></a>
    </figure>
    <figure class="box-typical box-typical box-typical-padding border">
        <div class="row">
            <div class="col-xl-8 col-md-6 col-sm-12 col-xs-12">
                <h4 class="with-border m-t-0"><?=$CMS->lang['price_information'];?></h4>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['price_name']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['price_name']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['price_start_time']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['price_start_time']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['price_end_time']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['price_end_time']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['price_stores']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['price_stores']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['price_cus_groups']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['price_cus_groups']; ?></div>
                    </div>
                </fieldset>
            </div>
            <div class="col-xl-4 col-md-6 col-sm-12 col-xs-12">
                <h4 class="with-border m-t-0"><?=$CMS->lang['other_information'];?></h4>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['user_id']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['user_id']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['price_created_time']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['price_created_at']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['price_updated_time']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['price_updated_at']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['price_status']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold" style="color: <?=$CMS->lang['price_status_color_'.$tpl->data['data_bk']['price_status']]?>"><?= $tpl->data['price_status']; ?></div>
                    </div>
                </fieldset>
            </div>
            <div class="col-md-12">
                <h4 class="with-border m-t-0"><?=$CMS->lang['product_information']?></h4>
                <section class="add_table">
                    <div class="data_table">
                        <div class="table-responsive">
                            <table class="table table_cus">
                                <thead>
                                <tr>
                                    <th width="55%">
                                        Name
                                    </th>
                                    <th width="15%">General price</th>
                                    <th width="15%">Old price</th>
                                    <th width="15%">New price</th>
                                </tr>
                                </thead>
                                <tbody>
                                <? if ($tpl->items) { ?>
                                    <? foreach ($tpl->items as $item) {
                                        ?>
                                        <tr>
                                            <td><?=$item['product_name']?></td>
                                            <td><?=$CMS->class->input->currency($item['product_price_sell'])?></td>
                                            <td><?=$CMS->class->input->currency($item['item_old_price'])?></td>
                                            <td><?=$CMS->class->input->currency($item['item_price'])?></td>
                                        </tr>
                                        <?
                                    } ?>
                                <? } ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
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
                        <a onclick="delete_confirm('<?= $CMS->vars['root_domain']; ?>/?site=price&act=delete&id=<?= $tpl->data['price_id']; ?>');" title=""><i class="fa  fa-mail-reply"></i><?= $CMS->lang['delete']; ?></a></li>
                    <li>
                        <a href="<?= $CMS->vars['root_domain']; ?>/?site=price&act=edit&id=<?= $tpl->data['price_id']; ?>"><i class="fa fa-file-text-o"></i><?= $CMS->lang['edit']; ?></a></li>
                </ul>
            </div>
        </div>

    <?php } else { ?>
        <a onclick="delete_confirm('<?= $CMS->vars['root_domain']; ?>/?site=price&act=delete&id=<?= $tpl->data['price_id']; ?>');"
           class="btn btn-inline btn-primary ladda-button pull-right add_cart"><?= $CMS->lang['delete']; ?></a>
        <a href="<?= $CMS->vars['root_domain']; ?>/?site=price&act=edit&id=<?= $tpl->data['price_id']; ?>"
           class="btn btn-inline btn-primary ladda-button pull-right add_cart"><?= $CMS->lang['edit']; ?></a>
    <? } ?>
</section>

<?= $tpl->comment; ?>
<?= $tpl->logs; ?>

<script>
    $('.codemirror').each(function() {
        var $this = $(this),
            $code = $this.html();
        $this.empty();
        var myCodeMirror = CodeMirror(this, {
            value: $code,
            mode: 'javascript',
            readOnly: true,
        });

    });
</script>
