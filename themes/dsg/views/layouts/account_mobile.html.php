<div class="visible-xs ">
    <ul class="nav top-sm-nav navbar-nav account account_mobile">
        <?if( isset($_SESSION['member']) AND $CMS->vars['is_login'] == 1 ){?>
        <li class="user">
            <a href="/login/change-password/" title="change password">
                <i class="fa fa-lock"></i>
            </a>
        </li>
        <li class="user">
            <a href="<?=$tpl->login['link']?>" title="<?=$tpl->login['text']?>">
                <i class="fa fa-sign-out"></i>
            </a>
        </li>

        <?}else{?>
        <li class="user">
            <a itemprop="url" href="<?=$tpl->login['link']?>" title="<?=$tpl->login['text']?>">
                <i class="fa fa-user"></i>
            </a>
        </li>
        <?}?>

        <li class="cart">
            <a href="/cart" title="<?=$CMS->lang['title_cart'];?>">
                <i class="fa fa-shopping-cart"></i>
                <span class="cart-number-pop"><?=$tpl->countmycart;?></span>
            </a>
        </li>
        <li><a class="mobile-top-btn" data-toggle="dropdown" data-target=".dropdown1"><i class="fa fa-bars"></i></a></li>
    </ul>
</div>