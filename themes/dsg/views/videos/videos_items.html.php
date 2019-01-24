<div class="row">
    <?foreach ( $tpl->dataListVideos as $data ){
 
        ?>
    <div class="col-md-6">
        <div itemscope itemtype="http://schema.org/NewsArticle" class="single_news_trend">
            <meta itemprop="headline" content="<?=$data['name'];?>">
            <meta itemprop="author" content="<?=$CMS->vars['company_name'];?>" style="display: none;">
            <font itemprop="publisher" itemscope itemtype="http://schema.org/Organization">
                <meta itemprop="logo" content="<?=$tpl->logo_website;?>">
                <meta itemprop="name" content="<?=$CMS->vars['company_name'];?>">
            </font>
            <div class="news_front">
                <font itemprop="image" itemscope itemtype="https://schema.org/ImageObject" style="display: none;">
                    <meta itemprop="image" content="<?=$data['image_L'];?>">
                    <meta itemprop="url" content="<?=$data['image_L'];?>">
                    <meta itemprop="width" content="auto">
                    <meta itemprop="height" content="auto">
                </font>
                <div class="news_trend_thumb" style="background: url('<?=$data['image_L'];?>') center center no-repeat; background-size: cover;">
                </div>
                <div class="news_hover_info">
                    
                        <a itemprop="url" href="<?=$data['url_none_html'];?>" title="view news">
                             <span class="playIcon"></span>
                        </a>
                   
                </div>
            </div>
            <div class="news_trend_details">
                <p class="posted">
                    <i class="fa fa-info-circle"></i> 
                    <time datetime="<?=\lib\date::format($data['time'], 'Y-m-d');?>"><?=\lib\date::format($data['time'], 'Y M d');?></time>
                    posted with <?=$data['views'];?> views
                </p>
                <h5 class="title">
                    <a itemprop="name" href="<?=$data['url_none_html'];?>" title="title news">
                       <?=$data['name'];?>
                   </a>
                </h5>
                <p class="description" itemprop="description">
                    <?=$data['description'];?>
                </p>
                <a itemprop="url" href="<?=$data['url_none_html'];?>" title="read more" class="read_more">
                    <?=$CMS->lang['videos_read_more'];?>
                    <i class="icon icon-angle-double-right"></i>
                </a>
            </div>
        </div>
    </div>
    <?}?>
</div>
<div class="row"><?=\core\ezy::render("paging", "layouts");?></div>