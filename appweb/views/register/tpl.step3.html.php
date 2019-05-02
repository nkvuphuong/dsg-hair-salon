<section class="section top-section bottom-sectiom">
	<div class="section-wrap">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h1 class="section-title text-normal">Với lĩnh vực <font style="text-transform: lowercase;"><?=$tpl->industry?></font> đã chọn.</h1>
					<h1 class="section-title text-normal">Bạn muốn quản lý theo hình thức nào?</h1>	
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-md-6">
					<div class="shadow-hover-box">
						<div class="recomended">khuyến khích sử dụng</div>
						<div class="row">
							<div class="col-md-2 shadow-hover-box-img text-center">
								<img class="img-responsive"src="images/store-ecom-web.png">
							</div>
							<div class="col-md-10">
								<h3 class="text-normal">Quản lý salon + Website bán hàng</h3>
								<p>Web4s cung cấp cho bạn một hệ thống Quản lý Salon tối ưu kèm theo một website với đầy đủ tính năng bán hàng chuyên nghiệp</p>
								<form action="<?=$CMS->vars['root_domain']?>/dang-ky-dung-thu/step3" method="POST" class="form-step3">
									<input type="text" name="warehouse" value="1" hidden>
									<input type="submit" class="btn btn-black-green ani submit" value="tôi dùng gói này">	
								</form>
							</div>
						</div>										
					</div>								
				</div>
				<div class="col-md-6">
					<div class="shadow-hover-box">
						<div class="row">
							<div class="col-md-2 shadow-hover-box-img text-center">
								<img class="img-responsive"src="images/store-only-web.png">
							</div>
							<div class="col-md-10">
								<h3 class="text-normal">Website bán hàng</h3>
								<p>Web4s cung cấp cho bạn một cửa hàng online đầy đủ tính năng bán hàng chuyên nghiệp</p>
								<form action="<?=$CMS->vars['root_domain']?>/dang-ky-dung-thu/step3" method="POST" class="form-step3">
									<input type="text" name="warehouse" value="0" hidden>
									<input type="submit" class="btn btn-black-green ani submit" value="tôi dùng gói này">
								</form>
							</div>
						</div>										
					</div>								
				</div>
			</div>
		</div>
	</div>
</section>
<script>
$( document ).ready(function() {
	$('.form-step3').submit(function(event) {
		event.preventDefault();
		$(this).find('input.submit').attr('disabled','disabled');
		$(this).unbind('submit').submit();
	});
});
</script>