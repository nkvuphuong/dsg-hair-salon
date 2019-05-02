<section class="section top-section">
	<div class="section-wrap">
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					<h1 class="section-title text-normal text-orange">ĐĂNG NHẬP</h1>		
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6 contact-form-outer">
					<?=\core\ezy::render('web4s_form', 'login');?>
				</div>
				<div class="col-sm-6">
					<div class="col-md-8">
						<!-- <button data-toggle="modal" data-target="#nhanhoa_form_login" class="btn btn-block btn-social-big nhanhoa ani">Đăng nhập ID Nhân Hòa</button> -->
						<button class="btn btn-block btn-social-big facebook ani" onclick="location.href='<?=$tpl->fb_url?>'">Tiếp tục với Facebook</button>
						<button class="btn btn-block btn-social-big googleplus ani" onclick="location.href='<?=$tpl->gg_url?>'">Tiếp tục với Google +</button>
					</div>
					<div class="col-md-4"></div>							
				</div>
			</div>
			<div class="row">
				<div class='col-md-12 text-center agree-term'>
					<p>
					* Khi đăng ký là bạn đã đồng ý với <a target="_blank" href="/thong-tin/dieu-khoan-su-dung.html">điều salonản sử dụng</a> <br>
					& <a target="_blank" href="/thong-tin/chinh-sach-bao-mat.html">chính sách bảo mật</a> của Web4s
					</p>
				</div>							
			</div>
		</div>
	</div>
</section>