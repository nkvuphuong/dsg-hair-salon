<section class="add_form main_form">
    <figure class="heading">
        <h3><?=$CMS->lang['group_name']; ?>: <?= $tpl->data['group_name']; ?></h3>
        <a href="<?= $CMS->vars['root_domain']; ?>/?site=attribute<?= $CMS->class->search->url_return; ?>" title=""><span class="font-icon font-icon-del"></span></a>
    </figure>
    <figure class="box-typical box-typical box-typical-padding border">
        <div class="row">
            <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
                <fieldset class="form-group row">
                    <label class="col-xl-2 form-control-label2"><?= $CMS->lang['group_name']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold">
                            <?= $tpl->data['group_name']; ?>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="form-group row">
                    <label class="col-xl-2 form-control-label2"><?= $CMS->lang['group_description']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['group_description']; ?></div>
                    </div>
                </fieldset>

                <fieldset class="form-group row">
                    <label class="col-xl-2 form-control-label2"><?= $CMS->lang['group_status']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['group_status']; ?></div>
                    </div>
                </fieldset>

                <fieldset class="form-group row">
                    <label class="col-xl-2 form-control-label2"><?= $CMS->lang['group_time']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['group_time']; ?></div>
                    </div>
                </fieldset>

                <fieldset class="form-group row">
                    <label class="col-xl-2 form-control-label2"><?= $CMS->lang['attr_list']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="box_addtribute row">
                            <div class="box_header col-md-12">
                                <div class="line">
                                    <div class="box_css col-md-2">
                                        <label class="lbl_row"><?=$CMS->lang['attr_name'];?></label>
                                    </div>
                                    <div class="box_css col-md-2">
                                        <label class="lbl_row"><?=$CMS->lang['attr_value'];?></label>
                                        
                                    </div>
                                    <div class="box_css col-md-2">
                                        <label class="lbl_row"><?=$CMS->lang['attr_key'];?></label>
                                    </div>
                                    <div class="box_css col-md-2">
                                        <label class="lbl_row"><?=$CMS->lang['attr_unit'];?></label>
                                    </div>
                                    <div class="box_css col-md-1">
                                        <label class="lbl_row"><?=$CMS->lang['attr_order'];?></label>
                                    </div>
                                    
                                    <div class="box_css col-md-2">
                                        <label class="lbl_row"><?=$CMS->lang['attr_description'];?></label>
                                    </div>
                                    <div class="box_css col-md-1">
                                        <label class="lbl_row">&nbsp;</label>
                                    </div>
                                </div>
                            </div>
                    <div class="box_list_attribute col-md-12">
                            <? if(count($tpl->attribute_list) > 0) { 
                            foreach ($tpl->attribute_list as $attribute) {?>
                        <div class="box_line" rowid="line_<?=$attribute['attr_id'];?>">
                           <div class="mini_line" style="display: none;">
                                <div class=" col-md-12">
                                    <p class="line_name"><?=$attribute['attr_name'];?></p>
                                    <span class="expand">+</span>
                                </div>
                            </div>
                            <div class="line">
                                <div class="box_css col-md-2">
                                    <label class="lbl_row" style="display: none;">Name</label>
                                    <span class="name"><?=$attribute['attr_name'];?></span>
                                    <input type="hidden" value="<?=$attribute['attr_id'];?>" name="attr_id[]">
                                </div>
                                <div class="box_css col-md-2">
                                    <label class="lbl_row" style="display: none;">Value</label>
                                    <p class="content value"><?=$attribute['attr_value'];?></p>
                                </div>
                                <div class="box_css col-md-2">
                                    <label class="lbl_row" style="display: none;">Key</label>
                                    <span class="key"><?=$attribute['attr_key'];?></span>
                                </div>
                                <div class="box_css col-md-2">
                                    <label class="lbl_row" style="display: none;">Unit</label>
                                    <span class="unit"><?=$attribute['attr_unit'];?></span>
                                </div>
                                <div class="box_css col-md-1">
                                    <label class="lbl_row" style="display: none;">Order</label>
                                    <span class="order"><?=$attribute['attr_order'];?></span>
                                </div>
                                
                                <div class="box_css col-md-2">
                                    <label class="lbl_row" style="display: none;">Description</label>
                                    <p class="content description"><?=$attribute['attr_description'];?></p>
                                </div>
                                
                            </div>
                        </div>
                    <? }} ?>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </figure>
</section>


<section class="add_cart_footer">
    <? if ($_SESSION['is_mobile'] == true) { ?>

        <div class="btn-group dropup pull-left hidden-xl-up">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-mail-reply"></i><?= $CMS->lang['gaction']; ?>
            </button>
            <div class="dropdown-menu">
                <ul>
                    <li>
                        <a onclick="delete_confirm('<?= $CMS->vars['root_domain']; ?>/?site=attribute&act=delete&id=<?= $tpl->data['group_id']; ?>');" title=""><i class="fa  fa-mail-reply"></i><?= $CMS->lang['delete']; ?></a></li>
                    <li>
                        <a href="<?= $CMS->vars['root_domain']; ?>/?site=attribute&act=edit&id=<?= $tpl->data['group_id']; ?>"><i class="fa fa-file-text-o"></i><?= $CMS->lang['edit']; ?></a></li>
                </ul>
            </div>
        </div>

    <? } else { ?>
        <a onclick="delete_confirm('<?= $CMS->vars['root_domain']; ?>/?site=attribute&act=delete&id=<?= $tpl->data['group_id']; ?>');"
           class="btn btn-inline btn-primary ladda-button pull-right add_cart"><?= $CMS->lang['delete']; ?></a>
        <a href="<?= $CMS->vars['root_domain']; ?>/?site=attribute&act=edit&id=<?= $tpl->data['group_id']; ?>"
           class="btn btn-inline btn-primary ladda-button pull-right add_cart"><?= $CMS->lang['edit']; ?></a>
    <? } ?>
</section>

<?= $tpl->comment; ?>
<?= $tpl->logs; ?>