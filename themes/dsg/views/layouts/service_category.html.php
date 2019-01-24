<?foreach( $tpl->dataListService_dsg as $data ){
	?>
<li><a itemprop="url" href="<?=$data['url_none_html'];?>" title="<?=$data['name'];?>"><?=$data['name'];?></a></li>
<?}?>