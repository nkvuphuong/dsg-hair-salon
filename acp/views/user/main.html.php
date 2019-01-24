<section class="add_table main_form">
    <figure class="heading">
        <h3><?=$CMS->lang['user_manage']?></h3>
        <figure class="pull-right right">
            <a href="<?=$CMS->vars['root_domain']?>/?site=user&act=add" title="" class="add_bill"><?=$CMS->lang['user_add']?></a>
            <a class="btn btn-warning" href="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>&subact=clear_cache"><?=$CMS->lang['clear_cache']?></a>
        </figure>
        <section class="tabs-section" style="margin-bottom: 15px">
            <div class="tabs-section-nav tabs-section-nav-inline">
                <ul class="nav" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link <?=$tpl->tabs['user'];?>" href="<?=$CMS->vars['root_domain']?>/?site=user">
                            <?=$CMS->lang['menu_user']?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?=$tpl->tabs['user_group'];?>" href="<?=$CMS->vars['root_domain']?>/?site=user_group">
                            <?=$CMS->lang['menu_user_group']?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?=$tpl->tabs['work_schedule'];?>" href="<?=$CMS->vars['root_domain']?>/?site=user&act=work_schedule">
                            <?=$CMS->lang['menu_work_schedule']?>
                        </a>
                    </li>
                </ul>
            </div><!--.tabs-section-nav-->
        </section>
        <?=$tpl->body?>
    </figure>
</section>