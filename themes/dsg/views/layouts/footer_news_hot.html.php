<?foreach( $tpl->dataHotPost as $data ){?>
<a itemprop="url" href="<?=$data['url_none_html'];?>" title="<?=$data['name'];?>" class="post-thumbnail">
    <span class="post-image"><img src="<?=\lib\input::getThumb($data['pathUpload'],100);?>"></span>
    <span class="post-description"><?=$data['name'];?></span>
</a>
<?}?>