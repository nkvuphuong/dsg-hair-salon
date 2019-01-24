<<section class="section blog-section top-section">
	<div class="section-wrap">
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					<h1>Tin tức & Sự kiện</h1>
					<p><?=\core\ezy::render('breadcrumb', 'news');?></p>
					<br><br>	
				</div>
			</div>
			<div class="row">
				<div class="col-md-8 text-small">
					<div class="row">
						<div class="col-md-12">
							<h3><?=$tpl->data['name'];?></h3>
							<i class="fa fa-calendar text-gray"></i><span class="text-gray"> <?=\lib\date::format($tpl->data['time']);?> </span><br>
							<?=$tpl->data['content'];?>
						</div>									
					</div>
					<div class="row">
						<div class="col-md-12">
							<?=\views\layouts::facebook_comment($CMS->vars['root_domain']."/tin-tuc/chi-tiet/".$tpl->data['url']);?>
						</div>
					</div>
				</div>
				<div class="col-md-4 text-left left-panel">
					<?=\core\ezy::tpl("widget", "news");?> 				
				</div>
			</div>
		</div>    				
	</div>
</section>
<?=\core\ezy::render('call_me', 'layouts');?>

<script>
	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.9&appId=1651763725063604";
		fjs.parentNode.insertBefore(js, fjs);
    } (document, 'script', 'facebook-jssdk'));
</script>