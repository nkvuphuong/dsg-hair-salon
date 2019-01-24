<div class="row">
    <div class="col-xl-12">
        <div class="box-typical prices-page steps-icon-block">
            <?= \core\ezy::render("header"); ?>
            <header class="prices-page-title"><?=$CMS->lang['upgrade_title']?></header>
            <p class="prices-page-subtitle" style="text-align: justify;font-size: 1rem;"><?=$CMS->lang['upgrade_package_desc']?></p>
            <form id="upgrade_package_form" name="upgrade_package_form">
                <input type="hidden" name="package_name" value=""/>
                <input type="hidden" name="package_cycle" value=""/>
            </form>
            <section class="row price-card-list" style="margin-left:-20px;margin-right:-20px;">
                <? foreach ($tpl->packageList as $package){ ?>
                <article class="price-card">
                    <header class="price-card-header" style="text-transform:capitalize;"><?=$package['package_name']?></header>
                    <div class="price-card-body">
                        <div class="price-card-amount" style="color:red;"><?=$package['package_price'] ?  $CMS->class->input->currency($package['package_price']) : $CMS->lang['free']?></div>
                        <div class="price-card-amount-lbl">per month</div>
                        <?=$package['package_suggest'] ? "<div class=\"price-card-label\">{$CMS->lang['package_suggest']}</div>" : ''?>
                        <ul class="price-card-list" style="padding-left: 10px;padding-right: 10px;">
                            <li><i class="font-icon font-icon-ok"></i><?=$CMS->lang['full_featured']?> <strong><?=$package['package_info']['full_featured']?></strong></li>
                            <li><i class="font-icon font-icon-ok"></i><?=$CMS->lang['admin_account']?>: <strong><?=$package['package_info']['admin_account']?></strong></li>
                            <li><i class="font-icon font-icon-ok"></i><?=$CMS->lang['storage_capacity']?>: <strong><?=$package['package_info']['storage_capacity']?></strong></li>
                        </ul>
                        <div class="clear"></div>
                        <?=($package['package_name'] == $tpl->siteInfo['site_license_package'])
                            ? "<a href=\"#\" class=\"btn btn-rounded btn-current-use\">{$CMS->lang['form_using']}</a>"
                            : ($package['package_name'] == 'basic' ? "<a onclick=\"cancelPackage('{$CMS->vars['root_domain']}?site=subscription&amp;act=cancel_package')\" class=\"btn btn-rounded\">{$CMS->lang['form_upgrade']}</a>" : ($CMS->subscription->checkValidPackage($package['package_name'], $CMS->vars['siteInfo']['site_license_package']) ? "<a href=\"{$CMS->vars['root_domain']}/?site=subscription&act=select_package&package_name={$package['package_name']}\" class=\"btn btn-rounded\">{$CMS->lang['form_upgrade']}</a>" : "<a class=\"btn btn-rounded btn-current-use\">{$CMS->lang['form_upgrade']}</a>")) ?>
                    </div>
                </article>
                <? } ?>
            </section>
            <div class="prices-page-bottom">
                <p class="text-left"><?=$CMS->lang['upgrade_package_note']?></p>
            </div>
        </div>
    </div>
</div>