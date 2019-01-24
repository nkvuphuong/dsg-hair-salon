<!-- you_web_data . client -->
<? foreach ($tpl->sites as $site) { ?>
<div class="row web-modal-item">
	<div class="col-sm-3 col-md-4">
		<img class="img-responsive" src="/themes/<?=$site['site_theme']?>/preview-web4s.jpg">
	</div>
	<div class="col-sm-9 col-md-8 text-left ">
		<h4>Mã website: <?=$site['site_code']?></h4>
		<? if ($site['site_created'] == 1) { ?>
		<p>Tên miền: <a href="http://<?=$site['site_domainname']?>"><span class="h5"><?=$site['site_domainname']?></span></a></p>
		<p>Gói web: <span class="h5"><?=$CMS->lang['regtype_'.$site['site_regtype']]?></span></p>				
		<p>Ngày kết thúc: <span class="h5"><?=$CMS->class->date->date_format($site['site_license_expired'])?></span></p>
		<a class="btn btn-md btn-text-black" href="http://<?=$site['site_domainname']?>">XEM WEB</a>
		<a class="btn btn-md btn-text-green" href="http://<?=$site['site_domainname']?>/acp">TRUY CẬP ACP</a>
	

		<? } else { ?>
		<p>Website đang được khởi tạo</p>
		<? } ?>
	</div>	
</div>
<? } ?>