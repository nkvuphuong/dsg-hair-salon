<section id="price-table" class="section top-section">	
	<div class="section-wrap">
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center storage-price-table-wrap">
					<h1 class="text-green">Nhiều giao diện hơn, nhiều sự lựa chọn tuyệt vời hơn</h1>
					<p class="text-center">Giao diện <?=$tpl->themesCategoryName;?></p>
				</div>
			</div>
		</div>
	</div>
</section>
<section class="section themes-section pb-30">
	<div class="section-wrap">
		<div class="container">
			<div class="row">
				<div class="col-md-12 themes-wrap">
					<?=\core\ezy::render('themes_data', 'templates');?>
				</div>
			</div>
		</div>    				
	</div>
</section>
<?=\core\ezy::render('industry_data', 'templates');?>
<?=\core\ezy::render('call_me', 'layouts');?>