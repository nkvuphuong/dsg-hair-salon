<!-- main_data . client -->
<? foreach ($tpl->sites as $site) { ?>
<div class="user-web-item">
	<? if ($site['site_created'] == 1) { ?>
	<div class="user-web-item-wrap row">
		<div class="user-web-item-thumbnail col-sm-4 col-md-4">
			<img class="img-responsive" src="/themes/<?=$site['site_theme']?>/preview-web4s.jpg">
		</div>
		<div class="user-web-item-desr col-sm-8 col-md-8">
			<h4>Mã website: <?=$site['site_code']?></h4>
			<p>Giao diện: <span class="h4"><?=$site['site_theme']?></span></p> 
			<p>Tên miền: <a href="http://<?=$site['site_domainname']?>"><span class="h4 text-green"><?=$site['site_domainname']?></span></a></p>
			<p>Gói web: <span class="h4"><?=$CMS->lang['regtype_'.$site['site_regtype']]?></span></p>
			<p>Ngày bắt đầu: <span class="h4"><?=$CMS->class->date->date_format($site['site_start_time'])?></span></p>
			<p>Ngày kết thúc: <span class="h4"><?=$CMS->class->date->date_format($site['site_license_expired'])?></span></p>
		</div>	
	</div>
	<div class="text-right">
		<? if ($site['site_regtype'] != 1) { ?>
		 
		<? } ?>
		<a href="http://<?=$site['site_domainname']?>"><input type="button" class="btn green-big outline ani" value="xem web"></a>
		<a href="http://<?=$site['site_domainname']?>/acp"><input type="button" class="btn green-big outline ani" value="truy cập acp"></a>


		<a class="btn btn-md btn-text-green" href="<?=$CMS->vars['root_domain'];?>/tai-khoan/kich-hoat-web/?web=<?=$site['site_code'];?>" ><input type="button" class="btn green-big outline ani" value="MUA WEB"></a>
	</div>
	<? } else { ?>
	<div class="user-web-item-wrap row">
		<div class="user-web-item-thumbnail col-sm-4 col-md-4">
			<img class="img-responsive" src="/themes/<?=$site['site_theme']?>/preview-web4s.jpg">
		</div>
		<div class="user-web-item-desr col-sm-8 col-md-8">
			<h3>Website đang được khởi tạo</h3>
			<h4>Mã website: <?=$site['site_code']?></h4>
			<p>Giao diện: <span class="h4"><?=$site['site_theme']?></span></p> 
		</div>	
	</div>
	<? } ?>
</div>
<? } ?>