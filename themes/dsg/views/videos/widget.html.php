<div class="news_sidebar_left">
    
     
     
    <div class="category_news_v1 mt-0 mb-30">
        <h3 class="leave_comment_v1" itemprop="name"><?=$CMS->lang['videos_category'];?></h3>
        <ul>
            <!-- Start category -->
            <? foreach ( $tpl->dataListCategory as $data ) { ?>
            <li itemprop="name"><a itemprop="url" href="<?=$data['cat_url_none_html'];?>"><?=$data['name'];?> <span></span></a></li>
            <? } ?>
            <!-- Start category -->
        </ul>
    </div>
    <div class="recent_area_bar mt-0 mb-30">
        <h3 class="leave_comment_v1" itemprop="name"><?=$CMS->lang['videos_recent_post'];?></h3>
        <!-- Start recent posts -->
        <? foreach ( $tpl->dataRecentPosts as $data ) { ?>
        <div itemscope itemtype="http://schema.org/NewsArticle" class="signle_post_v1">
            <meta itemprop="headline" content="<?=$data['name'];?>">
            <meta itemprop="author" content="<?=$CMS->vars['company_name'];?>">
            <font itemprop="publisher" itemscope itemtype="http://schema.org/Organization" style="display: none;">
                <meta itemprop="logo" content="<?=$tpl->logo_website;?>">
                <meta itemprop="name" content="<?=$CMS->vars['company_name'];?>">
            </font>
            <div class="img_post_rc_v1">
                <a itemprop="url" href="<?=$data['url_none_html'];?>" title="image recent">
                    <span itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
                        <img itemprop="image" src="<?=$data['image_L'];?>" alt="<?=$data['name'];?>"/>
                        <meta itemprop="url" content="<?=$data['image_L'];?>">
                        <meta itemprop="width" content="100">
                        <meta itemprop="height" content="auto">
                    </span>
                </a>
            </div>
            <div class="txt_post_rc_v1">
                <h4 itemprop="name"><a itemprop="url" href="<?=$data['url_none_html'];?>"><?=\lib\input::substr($data['name'],0,30);?></a></h4>
                <span itemprop="datePublished"><?=\lib\date::format($data['time']);?></span>
            </div>
        </div>
        <? } ?>
        <!-- End recent posts -->
    </div>
    
    <!-- End tags -->

</div>
