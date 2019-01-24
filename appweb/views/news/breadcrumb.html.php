<ul class="breadcrumblist">
    <li>
        <a itemprop="item" href="/" title="Home">Trang chủ</a>
    </li>

    <?if( in_array(\core\ezy::$act, array('category', 'danh-muc')) ){?>
    <!-- Start category -->
    <li>
        <a href="/news" title="News">Tin tức</a>
    </li>
    <li>
        <span class="active"><?=$tpl->title;?></span>
    </li>
    <!-- End category -->

    <? } else if ( in_array(\core\ezy::$act, array('detail', 'chi-tiet')) ) { ?>
    <!-- Start detail -->
    <li>
        <a href="/news" title="News">Tin tức</a>
    </li>
    <?if( $tpl->data['cat_name'] ){?>
    <li>
        <a href="<?=$tpl->data['cat_url'];?>" title="<?=$tpl->data['cat_name'];?>"><?=$tpl->data['cat_name'];?></a>
    </li>
    <?}?>
    <li>
        <span class="active"><?=$tpl->data['name'];?></span>
    </li>
    <!-- End detail -->
    
    <?}else{?>
    <li>
        <span class="active">Tin tức</span>
    </li>
    <?}?>
</ul>