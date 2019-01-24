<?=\core\ezy::tpl('you_web', 'client');?>
<section class="section customer-client-section top-section">
	<div class="section-wrap">
		<div class="container-fluid gray-bg">
			<div class="row">
				<div class="left-info-panel col-md-3">
					<?=\core\ezy::tpl('div_left', 'client');?>
				</div>
				<div class="right-user-info-container fluid-form col-md-9">
					<div class="row">
						<div class="col-md-10">
							<h3>Thay đổi mật khẩu</h3>
						</div>
					</div>
					<form id="contact-form" class="contact-page-form" name="" action="<?=$CMS->vars['root_domain']?>/tai-khoan/change_pass" method="POST">
						<? if($tpl->error) { ?>
						<div class="row">
							<div class="alert alert-danger">
								<strong>Có lỗi!</strong>
								<ul>
									<? foreach ($tpl->error as $error) { ?>
									<li><?=$error?></li>
									<? } ?>
								</ul>
							</div>
						</div>
						<? } ?>
						<? if($tpl->success) { ?>
						<div class="row">
							<div class="alert alert-success">
								<strong>Thành công!</strong>
								<?=$tpl->success?>
							</div>
						</div>
						<? } ?>
						<div class="row">
							<div class="col-sm-5">
								<div class="control">					
									<div class="form-group">						
										<input name="pass" type="password" class="form-control" placeholder="Mật khẩu" value="<?=$tpl->info['cus_full_name']?>" maxlength="50" title="Vui lòng nhập mật khẩu">
										<i class="fa fa-question-circle ani" title="Mật khẩu của bạn"></i>
									</div>	
								</div>
								<div class="control">		
									<div class="form-group">
										<input name="pass_new" type="password" class="form-control" placeholder="Mật khẩu mới" value="<?=$tpl->info['cus_phone']?>" maxlength="50" title="Vui lòng nhập mật khẩu mới">
										<i class="fa fa-question-circle ani" title="Mật khẩu mới của bạn"></i>
									</div>
								</div>
								<div class="control">		
									<div class="form-group">
										<input name="pass_confirm" type="password" class="form-control" placeholder="Xác nhận mật khẩu mới" value="<?=$tpl->info['cus_phone']?>" maxlength="50" title="Vui lòng nhập xác nhận mật khẩu mới">
										<i class="fa fa-question-circle ani" title="Xác nhận mật khẩu mới của bạn"></i>
									</div>
								</div>
								<input type="submit" class="btn green-big ani" value="cập nhật thông tin">							
							</div>
						</div>
					</form>
					<div class="row">									
						<div class="col-md-12">
							<div class="h5 bottom-coppyright text-gray">
								<p><?=\core\ezy::tpl('copyright_footer', 'layouts');?></p>	
							</div>										
						</div>
					</div>
				</div>
			</div>
		</div>	    			
	</div>
</section>