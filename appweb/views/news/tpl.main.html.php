<section class="section blog-section top-section">
	<div class="section-wrap">
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					<h1></h1>
					<p> </p>
							 
				 
						<?php foreach ($tpl->dataListCategory as $category) { ?>
						<a href="/tin-tuc/danh-muc/<?=$category['url'];?>"><input type="button" class="btn green-big ani" value="<?=$category['name'];?>"></a>

						<?php } ?>

						<p> </p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8 text-small">
					<?php if (empty($tpl->dataListNews)) { ?>
					<div class="row">
						<div class="col-md-12">
							<h5 class="text-orange">Đang cập nhật ...</h5>
						</div>
					</div>
					<?php } else {
						for ($i = 0; $i < count($tpl->dataListNews); $i +=2) { ?>
					<div class="row">
						<? for ($j = 0; $j < 2; $j ++) { 
							$news = $tpl->dataListNews[$i + $j];
							if (! empty($news)) { ?>
						<div class="col-sm-6 col-md-6">
							<a href="<?=$news['url'];?>" title="<?=$news['name'];?>"><img class="img-responsive" src="<?=$news['image_M']?>" alt="tin tức website" style="max-width: 365px; max-height: 192px;"></a>
							<br><br><br>
							<a href="<?=$news['url'];?>" title="<?=$news['name'];?>"><p><strong class="text-medium"><?=$news['name']?></strong><p></a>
							<i class="fa fa-calendar text-gray"></i><span class="text-gray"> <?=\lib\date::format($news['time']);?> </span>
							<p><?=$news['description']?></p>
						</div>
							<? }
						} ?>
					</div>
					<? } ?>
					<div class="row">
						<div class="col-md-12 text-center">
							<?=\core\ezy::render('paging', 'layouts');?>
						</div>
					</div>
					<?php } ?>
				</div>
				<div class="col-md-4 text-left left-panel">
					<?=\core\ezy::tpl("widget", "news");?> 
				</div>
			</div>
		</div> 
	</div>
</section>
<?=\core\ezy::render('call_me', 'layouts');?>