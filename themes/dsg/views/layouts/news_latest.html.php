<div class="right-panel-content data-news-latest">
    <?foreach( $tpl->dataLatestPosts as $data ){?>
    <a href="<?=$data['url_none_html'];?>" title="<?=$data['name'];?>" class="post-thumbnail">
        <span class="post-image"><img src="<?=\lib\input::getThumb($data['pathUpload'], 100);?>"></span>
        <span class="post-description">
            <p style="font-weight: bold;" itemprop="name"><?=$data['name'];?></p>
        </span>
    </a> 
    <?}?>
</div>