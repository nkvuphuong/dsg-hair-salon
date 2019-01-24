<ul class="service-list-ul">
    <?foreach( $tpl->dataServiceHot as $data ){?>
    <li><a href="<?=$data['url_none_html'];?>" title="<?=$data['name'];?>"><?=$data['name'];?></a></li>
    <?}?>
</ul>