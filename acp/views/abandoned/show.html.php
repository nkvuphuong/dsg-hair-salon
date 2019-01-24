<style type="text/css">
    .manage-container {
        font-family: arial, sans-serif;
        background: #fff;
        padding: 10px;
        margin-bottom: 30px;
    }
</style>
<section class="add_form main_form" style="margin-bottom: 15px;">
    <figure class="heading">
        <h3 style="margin: 0;"><?=$CMS->lang['detail_abandoned'];?>: #<?=$tpl->data['id'];?></h3>
        <a href="<?= $CMS->vars['root_domain']; ?>/?site=abandoned" title=""><span class="font-icon font-icon-del"></span></a>
    </figure>
</section>
<div class="manage-container">
    <section class="add_form main_form">
        <figure class="box-typical box-typical box-typical-padding border">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="with-border m-t-0"><?=$CMS->lang['abandoned_bill'];?></h4>
                    <fieldset class="form-group row">
                        <label class="col-xs-3 form-control-label2"><?=$CMS->lang['abandoned_name'];?></label>
                        <div class="col-xs-9 form-control-span2">
                            <div class="form-label semibold"><?=$tpl->data['bill_full_name'];?></div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group row">
                        <label class="col-xs-3 form-control-label2"><?=$CMS->lang['abandoned_email'];?></label>
                        <div class="col-xs-9 form-control-span2">
                            <div class="form-label semibold"><?=$tpl->data['bill_email'];?></div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group row">
                        <label class="col-xs-3 form-control-label2"><?=$CMS->lang['abandoned_phone'];?></label>
                        <div class="col-xs-9 form-control-span2">
                            <div class="form-label semibold"><?=$tpl->data['bill_phone'];?></div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group row">
                        <label class="col-xs-3 form-control-label2"><?=$CMS->lang['abandoned_company'];?></label>
                        <div class="col-xs-9 form-control-span2">
                            <div class="form-label semibold"><?=$tpl->data['bill_company'];?></div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group row">
                        <label class="col-xs-3 form-control-label2"><?=$CMS->lang['abandoned_address'];?></label>
                        <div class="col-xs-9 form-control-span2">
                            <div class="form-label semibold"><?=$tpl->data['bill_address_full_us'];?></div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group row">
                        <label class="col-xs-3 form-control-label2"><?=$CMS->lang['abandoned_country'];?></label>
                        <div class="col-xs-9 form-control-span2">
                            <div class="form-label semibold"><?=$tpl->data['bill_country_name'];?></div>
                        </div>
                    </fieldset>
                </div>
                <div class="col-md-6">
                    <h4 class="with-border m-t-0"><?=$CMS->lang['abandoned_ship'];?></h4>
                    <fieldset class="form-group row">
                        <label class="col-xs-3 form-control-label2"><?=$CMS->lang['abandoned_name'];?></label>
                        <div class="col-xs-9 form-control-span2">
                            <div class="form-label semibold"><?=$tpl->data['ship_full_name'];?></div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group row">
                        <label class="col-xs-3 form-control-label2"><?=$CMS->lang['abandoned_email'];?></label>
                        <div class="col-xs-9 form-control-span2">
                            <div class="form-label semibold"><?=$tpl->data['ship_email'];?></div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group row">
                        <label class="col-xs-3 form-control-label2"><?=$CMS->lang['abandoned_phone'];?></label>
                        <div class="col-xs-9 form-control-span2">
                            <div class="form-label semibold"><?=$tpl->data['ship_phone'];?></div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group row">
                        <label class="col-xs-3 form-control-label2"><?=$CMS->lang['abandoned_company'];?></label>
                        <div class="col-xs-9 form-control-span2">
                            <div class="form-label semibold"><?=$tpl->data['ship_company'];?></div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group row">
                        <label class="col-xs-3 form-control-label2"><?=$CMS->lang['abandoned_address'];?></label>
                        <div class="col-xs-9 form-control-span2">
                            <div class="form-label semibold"><?=$tpl->data['ship_address_full_us'];?></div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group row">
                        <label class="col-xs-3 form-control-label2"><?=$CMS->lang['abandoned_country'];?></label>
                        <div class="col-xs-9 form-control-span2">
                            <div class="form-label semibold"><?=$tpl->data['ship_country_name'];?></div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h4 class="with-border m-t-0"><?=$CMS->lang['abandoned_more'];?></h4>
                </div>
                <div class="col-md-6">
                    <fieldset class="form-group row">
                        <label class="col-xs-3 form-control-label2"><?=$CMS->lang['abandoned_id'];?></label>
                        <div class="col-xs-9 form-control-span2">
                            <div class="form-label semibold">#<?=$tpl->data['id'];?></div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group row">
                        <label class="col-xs-3 form-control-label2"><?=$CMS->lang['abandoned_total'];?></label>
                        <div class="col-xs-9 form-control-span2">
                            <div class="form-label semibold">#<?=$tpl->data['total_c'];?></div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group row">
                        <label class="col-xs-3 form-control-label2"><?=$CMS->lang['abandoned_status'];?></label>
                        <div class="col-xs-9 form-control-span2">
                            <div class="form-label semibold"><?=$tpl->data['status_label'];?></div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group row">
                        <label class="col-xs-3 form-control-label2"><?=$CMS->lang['abandoned_sent_email'];?></label>
                        <div class="col-xs-9 form-control-span2">
                            <div class="form-label semibold"><?=$tpl->data['sent_email_label'];?></div>
                        </div>
                    </fieldset>
                </div>
                <div class="col-md-6">
                    <fieldset class="form-group row">
                        <label class="col-xs-3 form-control-label2"><?=$CMS->lang['abandoned_count_sent'];?></label>
                        <div class="col-xs-9 form-control-span2">
                            <div class="form-label semibold"><?=$tpl->data['count_sent'];?></div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group row">
                        <label class="col-xs-3 form-control-label2"><?=$CMS->lang['abandoned_time'];?></label>
                        <div class="col-xs-9 form-control-span2">
                            <div class="form-label semibold"><?=$tpl->data['time_c'];?></div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group row">
                        <label class="col-xs-3 form-control-label2"><?=$CMS->lang['abandoned_next_time'];?></label>
                        <div class="col-xs-9 form-control-span2">
                            <div class="form-label semibold"><?=$tpl->data['next_time_c'];?></div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group row">
                        <label class="col-xs-3 form-control-label2"><?=$CMS->lang['abandoned_ip'];?></label>
                        <div class="col-xs-9 form-control-span2">
                            <div class="form-label semibold"><?=$tpl->data['ip'];?></div>
                        </div>
                    </fieldset>
                </div>
            </div><!--.row-->
        </figure>
        <?=\core\ezy::render('item');?>
        <?=\core\ezy::render('email');?>
    </section>
</div>
<section class="add_cart_footer">
    <?=$tpl->footer_html;?>
    <?if( $CMS->permit['abandoned_delete'] ){?>
    <a onclick="delete_confirm('<?=$CMS->vars['root_domain'];?>/?site=abandoned&act=delete&id=<?=$tpl->data['id'];?>')" class="pull-right add_cart_2 pointer"><?=$CMS->lang['gdelete'];?></a>
    <?}?>
</section>
<?=$tpl->comment;?>
<?=$tpl->logs;?>