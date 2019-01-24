<div class="steps-icon-progress">
    <ul>
        <li class="active">
            <div class="icon">
                <i class="fa fa-archive"></i>
            </div>
            <div class="caption"><?=$CMS->lang['form_package']?></div>
        </li>
        <li class="<?=$tpl->cart_actived?>">
            <div class="icon">
                <i class="font-icon font-icon-cart-2"></i>
            </div>
            <div class="caption"><?=$CMS->lang['form_cart']?></div>
        </li>
        <li class="<?=$tpl->payment_actived?>">
            <div class="icon">
                <i class="font-icon font-icon-card"></i>
            </div>
            <div class="caption"><?=$CMS->lang['form_payment']?></div>
        </li>
        <li class="<?=$tpl->confirm_actived?>">
            <div class="icon">
                <i class="font-icon font-icon-check-bird"></i>
            </div>
            <div class="caption"><?=$CMS->lang['form_confirm']?></div>
        </li>
    </ul>
</div>