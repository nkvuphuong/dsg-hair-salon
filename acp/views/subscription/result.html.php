<div class="row">
    <div class="col-xl-6"  style="float:none;margin:0px auto;">
        <section class="box-typical steps-icon-block">
            <?= \core\ezy::render("header"); ?>

            <header class="steps-numeric-title"><?=$CMS->lang['form_transaction']?> <?=$tpl->payment_result['payment_title']?></header>
            <div class="form-group">
                <span class="form-control-span2"><?=$tpl->payment_result['payment_method']?></span><br/>
                <span class="form-control-span2">( <?=$tpl->payment_result['payment_status']?> )</span>
            </div>
            <div class="form-group">
                <fieldset class="form-control" style="border:none;text-align:left;">
                    <div class="form-group">
                        <span class="form-control-label2"><?=$CMS->lang['form_package']?>:</span>
                        <span class="form-control-span2"><?=$tpl->payment_result['package_name']?></span>
                    </div>
                    <div class="form-group">
                        <span class="form-control-label2"><?=$CMS->lang['period']?>:</span>
                        <span class="form-control-span2"><?=$tpl->payment_result['package_cycle']?> <?=$CMS->lang['month']?></span>
                    </div>
                </fieldset>
            </div>
        </section>
    </div>
</div>