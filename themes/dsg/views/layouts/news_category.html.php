<? foreach ($tpl->dataNewsCategory as $data) { ?>
    <li itemprop="name"><a itemprop="url" href="<?=$data['cat_url_none_html'];?>" title=""><?=$data['name'];?></a></li>
<? } ?>