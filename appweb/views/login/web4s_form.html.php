<!-- web4s_form . login -->
<form id="contact-form" class="contact-page-form" name="" action="<?=$CMS->vars['root_domain']?>/login" method="POST">
	<?php if ($CMS->input['referer'] == 0 && $tpl->error['login']) { ?>
	<div class="row">
		<div class="col-md-4"></div>
		<div class="col-md-8">
			<div class="alert alert-danger">
				<strong>Có lỗi!</strong> <?=$tpl->error['login']?>
			</div>
		</div>
	</div>
	<?php } ?>
	<div class="row <?=($CMS->input['referer'] == 0 && $tpl->error['email'])?'control with-errors':''?>">
		<div class="col-md-4">
			<?php if ($CMS->input['referer'] == 0 && $tpl->error['email']) { ?>
			<div class="help-block ">
				<div class="arrow"></div>
				<div class="help-block-wrap">
					<?=$tpl->error['email']?>
				</div>
			</div>
			<?php } ?>
		</div>
		<div class="col-md-8">
			<div class="form-group">
				<input type="text" class="form-control" placeholder="Tài khoản ID Nhân Hòa" name="email" value="<?=($CMS->input['referer'] == 0) ? $CMS->input['email'] : ''?>" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" title="Vui lòng nhập tài khoản ID Nhân Hòa">
				<?php if ($CMS->input['referer'] == 0 && $tpl->error['email']) { ?>
				<i class="fa fa-times-circle ani"></i>
				<?php } else { ?>
				<i class="fa fa-question-circle ani" title="Tài khoản ID Nhân Hòa"></i>
				<?php } ?>
			</div>	
		</div>    										
	</div>
	<div class="row <?=($CMS->input['referer'] == 0 && $tpl->error['pass'])?'control with-errors':''?>">
		<div class="col-md-4">
			<?php if ($CMS->input['referer'] == 0 && $tpl->error['pass']) { ?>
			<div class="help-block ">
				<div class="arrow"></div>
				<div class="help-block-wrap">
					<?=$tpl->error['pass']?>
				</div>
			</div>
			<?php } ?>
		</div>
		<div class="col-md-8">
			<div class="form-group">
				<input type="password" class="form-control" placeholder="Mật khẩu" name="pass" value="<?=($CMS->input['referer'] == 0) ? $CMS->input['pass'] : ''?>" title="Vui lòng nhập mật khẩu" maxlength="150">
				<?php if ($CMS->input['referer'] == 0 && $tpl->error['pass']) { ?>
				<i class="fa fa-times-circle ani"></i>
				<?php } else { ?>
				<i class="fa fa-question-circle ani" title="Mật khẩu của bạn"></i>
				<?php } ?>
			</div>	
		</div>    										
	</div>
	<div class="row">
		<div class="col-md-4"></div>
		<div class="col-md-8">
			<input type="hidden" name="referer" value="0">
			<a href="<?=$CMS->vars['root_domain']?>/register/account" class="pull-left">Đăng ký</a>
			<a href="javascript:void(0)" data-toggle="modal" data-target="#nhanhoa_form_forgot" class="forgot pull-right">Quên mật khẩu?</a>
			<input type="submit" class="btn btn-block green-big ani" value="ĐĂNG NHẬP">
		</div>
	</div>
</form>