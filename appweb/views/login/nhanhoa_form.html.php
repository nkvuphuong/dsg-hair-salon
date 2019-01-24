<!-- nhanhoa_form . login -->
<div class="modal" id="nhanhoa_form_forgot" tabindex="-1" role="dialog" aria-labelledby="nhanhoa_form_forgot" aria-hidden="true">
	<div class="modal-dialog nhanhoa_form_login_dialog">
		<div class="row">
			<div class="form_login">
				<button type="button" class="close" data-dismiss="modal" style="    font-size: 35px;">&times;</button>
				<h1 class="logo">
					<img src="https://id.nhanhoa.com/templates/images/logo.png" alt="">
				</h1>
				<? if ($CMS->input['referer'] == 1 && $tpl->error) {
					$tpl->error = is_array($tpl->error) ? $tpl->error : array($tpl->error); ?>
				<div class="row">
					<div class="alert alert-danger">
						<strong>Có lỗi!</strong><br/>
						<ul>
							<? foreach($tpl->error as $error) { ?>
							<li><?=$error?></li>
							<? } ?>
						</ul>
					</div>
				</div>
				<? } ?>
				<? if ($CMS->input['referer'] == 1 && $tpl->success) {
					$tpl->success = is_array($tpl->success) ? $tpl->success : array($tpl->success); ?>
				<div class="row">
					<div class="alert alert-success">
						<strong>Thành công!</strong><br/>
						<ul>
							<? foreach($tpl->success as $success) { ?>
							<li><?=$success?></li>
							<? } ?>
						</ul>
					</div>
				</div>
				<? } else { ?>
				<div class="row">
					<div class="tab-content">
						<form id="contact-form" class="contact-page-form" name="" action="<?=$CMS->vars['root_domain']?>/login" method="POST">
							<input type="hidden" name="referer" value="1">
							<input type="hidden" name="tabs" value="forgot">
							<div class="form_input">
								<input type="text" name="email" value="<?=($CMS->input['referer'] == 1 && $CMS->input['tabs'] == 'forgot') ? $CMS->input['email'] : ''?>" autocomplete="off" placeholder="Email Nhân Hòa">
								<i class="fa fa-user"></i>
							</div>
							<input type="submit" value="QUÊN MẬT KHẨU">
						</form>
					</div>
				</div>
				<? } ?>
			</div>
		</div>
	</div>
</div>

<? if($CMS->input['referer'] == 1 || $CMS->input['forgot'] == 1) {  ?>
	<script> $('#nhanhoa_form_forgot').modal('show'); </script> 
<? }