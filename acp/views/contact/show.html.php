<section class="add_form main_form">
    <figure class="heading">
        <h3><?=$CMS->lang['menu_contact'];?>: <?=$tpl->data['con_name'];?></h3>
        <a href="<?= $CMS->vars['root_domain']; ?>/?site=contact<?= $CMS->class->search->url_return; ?>" title=""><span
                    class="font-icon font-icon-del"></span></a>
    </figure>

    <figure class="box-typical box-typical box-typical-padding border">
        <h4 class="with-border m-t-0"><?=$CMS->lang['con_info'];?></h4>
        <div class="row">
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <fieldset class="form-group row">
                    <label class="col-xs-3 form-control-label2"><?=$CMS->lang['con_name'];?></label>
                    <div class="col-xs-9 form-control-span2">
                        <div class="form-label semibold"><?=$tpl->data['con_name'];?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xs-3 form-control-label2"><?=$CMS->lang['con_email'];?></label>
                    <div class="col-xs-9 form-control-span2">
                        <div class="form-label semibold"><?=$tpl->data['con_email'];?></div>
                    </div>
                </fieldset>

                <?if( $tpl->data['con_type'] == 1 ){?>
                <fieldset class="form-group row">
                    <label class="col-xs-3 form-control-label2"><?=$CMS->lang['con_phone'];?></label>
                    <div class="col-xs-9 form-control-span2">
                        <div class="form-label semibold"><?=$tpl->data['con_phone'];?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xs-3 form-control-label2"><?=$CMS->lang['con_address'];?></label>
                    <div class="col-xs-9 form-control-span2">
                        <div class="form-label semibold"><?=$tpl->data['con_address'];?></div>
                    </div>
                </fieldset>
                <?}?>

                <fieldset class="form-group row">
                    <label class="col-xs-3 form-control-label2"><?=$CMS->lang['con_time'];?></label>
                    <div class="col-xs-9 form-control-span2">
                        <div class="form-label semibold"><?=$tpl->data['con_time'];?></div>
                    </div>
                </fieldset>

                <?if( $tpl->data['con_type'] == 1 ){?>
                <fieldset class="form-group row">
                    <label class="col-xs-3 form-control-label2"><?=$CMS->lang['con_update_time'];?></label>
                    <div class="col-xs-9 form-control-span2">
                        <div class="form-label semibold"><?=$tpl->data['con_update_time'];?></div>
                    </div>
                </fieldset>
                <?}?>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <?if( $tpl->data['con_type'] == 1 ){?>
                <fieldset class="form-group row">
                    <label class="col-xs-3 form-control-label2"><?=$CMS->lang['con_type'];?></label>
                    <div class="col-xs-9 form-control-span2">
                        <div class="form-label semibold"><?=$CMS->lang["con_type_{$tpl->data['con_type']}"];?></div>
                    </div>
                </fieldset>
                <?}?>

                <fieldset class="form-group row">
                    <label class="col-xs-3 form-control-label2"><?=$CMS->lang['con_subject'];?></label>
                    <div class="col-xs-9 form-control-span2">
                        <div class="form-label semibold"><?=$tpl->data['con_subject'];?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xs-3 form-control-label2"><?=$CMS->lang['con_content'];?></label>
                    <div class="col-xs-9 form-control-span2">
                        <div class="form-label semibold"><?=$tpl->data['con_content'];?></div>
                    </div>
                </fieldset>
            </div>
        </div>
    </figure>
</section>
<?=$tpl->comment;?>
<?=$tpl->logs;?>