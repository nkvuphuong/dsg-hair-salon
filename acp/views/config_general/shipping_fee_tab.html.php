<?$free_hidden = (isset($CMS->vars['web_free']) == 1 AND $CMS->vars['is_root'] != 1) ? "tab_hidden" : "";?>
<div class="tabs-section-nav tabs-section-nav-inline">
    <ul class="nav" role="tablist">
        <li class="nav-item">
            <a class="nav-link ontab config-menu" href="<?=$CMS->vars['root_domain'];?>/?site=config_general&tab=tabs-4-tab-1"><?=$CMS->lang['title_tab_general'];?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link ontab config-menu" href="<?=$CMS->vars['root_domain'];?>/?site=config_general&tab=tabs-4-tab-2">SEO</a>
        </li>
        <li class="nav-item <?=$free_hidden;?>">
            <a class="nav-link ontab config-menu" href="<?=$CMS->vars['root_domain'];?>/?site=config_general&tab=tabs-4-tab-11"><?=$CMS->lang['title_tab_advertisement'];?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link ontab config-menu" href="<?=$CMS->vars['root_domain'];?>/?site=config_general&tab=tabs-4-tab-3"><?=$CMS->lang['title_tab_company'];?></a>
        </li>
        <li class="nav-item <?=$free_hidden;?>">
            <a class="nav-link ontab config-menu" href="<?=$CMS->vars['root_domain'];?>/?site=config_general&tab=tabs-4-tab-4"><?=$CMS->lang['title_tab_invoice'];?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link ontab config-menu" href="<?=$CMS->vars['root_domain'];?>/?site=config_general&tab=tabs-4-tab-5"><?=$CMS->lang['title_tab_date_time'];?></a>
        </li>
        <li class="nav-item <?=$free_hidden;?>">
            <a class="nav-link ontab config-menu" href="<?=$CMS->vars['root_domain'];?>/?site=config_general&tab=tabs-4-tab-6">API</a>
        </li>
        <li class="nav-item <?=$free_hidden;?>">
            <a class="nav-link ontab config-menu" href="<?=$CMS->vars['root_domain'];?>/?site=config_general&tab=tabs-4-tab-9">Security</a>
        </li>
        <li class="nav-item <?=$free_hidden;?>">
            <a class="nav-link ontab config-menu" href="<?=$CMS->vars['root_domain'];?>/?site=config_general&tab=tabs-4-tab-7"><?=$CMS->lang['title_tab_booking'];?></a>
        </li>
        <li class="nav-item <?=$free_hidden;?>">
            <a class="nav-link ontab config-menu" key="payment" href="<?=$CMS->vars['root_domain'];?>/?site=config_general&tab=tabs-4-tab-8"><?=$CMS->lang['title_tab_payment'];?></a>
        </li>
        <li class="nav-item <?=$free_hidden;?>">
            <a class="nav-link ontab config-menu" href="<?=$CMS->vars['root_domain'];?>/?site=config_general&tab=tabs-4-tab-10"><?=$CMS->lang['title_tab_email_server'];?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link ontab config-menu" href="<?=$CMS->vars['root_domain'];?>/?site=config_general&tab=tabs-config-cache"><?=$CMS->lang['title_tab_cache'];?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link ontab config-menu" href="<?=$CMS->vars['root_domain'];?>/?site=config_general&tab=tabs-config-sale"><?=$CMS->lang['title_tab_sale'];?></a>
        </li>
        <?if( $CMS->vars['type_web'] == "ecommerce" ){?>
        <li class="nav-item">
            <a class="nav-link ontab config-menu active" href="<?=$CMS->vars['root_domain'];?>/?site=config_general&act=shipping_fee"><?=$CMS->lang['title_tab_shipping_fee'];?></a>
        </li>
        <?}?>
    </ul>
</div><!--.tabs-section-nav-->
<input id="is_payment_tab" value="0" type="hidden" />