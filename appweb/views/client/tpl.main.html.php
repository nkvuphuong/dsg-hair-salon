<?=\core\ezy::tpl('you_web', 'client');?>
<section class="section customer-client-section top-section">
	<div class="section-wrap">
		<div class="container-fluid">
			<div class="row">
				<div class="left-info-panel col-md-3">
					<?=\core\ezy::tpl('div_left', 'client');?>
				</div>
				<div class="right-web-container pink-bg col-md-9">
					<div class="user-web-items-wrap col-lg-8 ">
						<? if($_SESSION['res_site_error']) { ?>
						<div class="row">
							<div class="alert alert-danger">
								<strong>Có lỗi!</strong> Mỗi tài khoản chỉ được đăng ký dùng thử một lần.
							</div>
						</div>
						<? unset($_SESSION['res_site_error']);
						} ?>
						<? if($_SESSION['login_social']) { ?>
						<div class="row">
							<div class="alert alert-success">
								<strong>Thành công!</strong> Vui lòng kiểm tra email để kích hoạt tài khoản.
							</div>
						</div>
						<? unset($_SESSION['login_social']);
						} ?>
						<? if(is_array($tpl->sites) && count($tpl->sites) > 0) { ?>
						<div class="user-web-title-wrap">
							<h3 class="user-web-title">Danh sách trang web của bạn</h3>
						</div>										
						<?=\core\ezy::render('main_data', 'client');?>
						<? } ?>
					</div>
					<div class="col-lg-4">
						<div class="user-web-promotion">
							<?=\core\ezy::tpl('promotion', 'client');?>
						</div>
					</div>
					<div class="col-md-12 text-white">
						<div class="h5 bottom-coppyright">
							<p><?=\core\ezy::tpl('copyright_footer', 'layouts');?></p>	
						</div>
					</div>
				</div>
			</div>
		</div>	    			
	</div>
</section>