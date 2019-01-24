<section class="section top-section bottom-sectiom">
	<div class="section-wrap">
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<h1>Chào <?=$tpl->name?></h1>
					<h3>Bạn đang kinh doanh lĩnh vực nào?</h3>	
					<br>			
				</div>
			</div>
			<div class="row">
				<ul class="register-type-list">
					<?php for ($i = 0; $i < 4; $i++) { ?>
					<div class="col-sm-6 col-md-3">
						<?php for ($j = 0; $j < 3; $j++) {
							$id = ($i * 3) + $j;
							$id = $id < 11 ? $id + 1 : 15; ?>
						<li><a href="<?=$CMS->vars['root_domain']?>/dang-ky-dung-thu/step2/?id=<?=$id?>"><i class="fa fa-angle-double-right" aria-hidden="true"></i> <?=$CMS->lang['industry_'.$id]?></a></li>
						<?php } ?>
					</div>
					<?php } ?>
				</ul>								
			</div>
			<div class="row">
				<div class="col-md-12 text-gray">
					<br>
					<p>*** Chủ đề có thể thay đổi trong phần Quản trị website</p>
				</div>
			</div>
		</div>
	</div>
</section>