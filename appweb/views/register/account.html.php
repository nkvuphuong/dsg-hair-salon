<section class="section top-section">
	<div class="section-wrap">
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					<h1 class="section-title text-normal">Đăng ký tài khoản Nhân Hòa</h1>
					<br>			
				</div>
			</div>
			<? if (isset($tpl->success['nhanhoa'])) { ?>
			<div class="alert alert-success">
				<strong>Thành công!</strong>
				<?=$tpl->success['nhanhoa']?>
			</div>
			<? } else { ?>
			<div class="row">
				<div class="col-sm-6 contact-form-outer">
					<form id="contact-form" class="contact-page-form" name="" action="<?=$CMS->vars['root_domain']?>/register/account" method="POST">
						<div class="row <?=($tpl->error['email']) ? 'control with-errors' : '';?>">
							<div class="col-md-4"></div>
							<div class="col-md-8">
								<h3>Thông tin tài khoản:</h3>
							</div>
							<div class="col-md-4" id="email_err">
								<?php if ($tpl->error['email']) { ?>
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
									<input type="text" class="form-control input-register" placeholder="Email" name="email" id="email" value="<?=$CMS->input['email'];?>" <?=($CMS->vars['is_login'] == 1 && ! empty($CMS->input['email'])) ? 'readonly' : 'pattern="[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[a-z]{2,4}$" title="Vui lòng nhập email bạn muốn sử dụng đăng nhập"';?>>
								</div>	
							</div>    										
						</div>
						<?php if ($CMS->vars['is_login'] == 0) { ?>
						<div class="row <?=( $tpl->error['pass'])?'control with-errors':''?>">
							<div class="col-md-4">
								<?php if ( $tpl->error['pass']) { ?>
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
									<input type="password" class="form-control input-register" placeholder="Mật khẩu" name="pass" value="<?=$CMS->input['pass'];?>" minlength="5" maxlength="150" title="Vui lòng nhập mật khẩu">
								</div>	
							</div>    										
						</div>
						<div class="row <?=( $tpl->error['confirm'])?'control with-errors':''?>">
							<div class="col-md-4">
								<?php if ( $tpl->error['confirm']) { ?>
								<div class="help-block ">
									<div class="arrow"></div>
									<div class="help-block-wrap">
										<?=$tpl->error['confirm']?>
									</div>
								</div>
								<?php } ?>
							</div>
							<div class="col-md-8">
								<div class="form-group">
									<input type="password" class="form-control input-register" placeholder="Xác nhận mật khẩu" name="confirm" value="<?=$CMS->input['confirm'];?>" maxlength="150" title="Vui lòng xác nhận mật khẩu">
								</div>	
							</div>    										
						</div>
						<?php } ?>
						<div class="row <?=( $tpl->error['name'])?'control with-errors':''?>">
							<div class="col-md-4">
								<?php if ( $tpl->error['name']) { ?>
								<div class="help-block ">
									<div class="arrow"></div>
									<div class="help-block-wrap">
										<?=$tpl->error['name']?>
									</div>
								</div>
								<?php } ?>
							</div>
							<div class="col-md-8">
								<div class="form-group">
									<input type="text" class="form-control input-register" placeholder="Họ và tên" name="name" value="<?=$CMS->input['name'];?>" <?=($CMS->vars['is_login'] == 1 && ! empty($CMS->input['name'])) ? 'readonly' : 'maxlength="50" title="Vui lòng nhập họ và tên"';?>>
								</div>	
							</div>    										
						</div>
						<div class="row <?=( $tpl->error['phone'])?'control with-errors':''?>">
							<div class="col-md-4">
								<?php if ( $tpl->error['phone']) { ?>
								<div class="help-block ">
									<div class="arrow"></div>
									<div class="help-block-wrap">
										<?=$tpl->error['phone']?>
									</div>
								</div>
								<?php } ?>
							</div>
							<div class="col-md-8">
								<div class="form-group">
									<input type="text" class="form-control input-register" placeholder="Số điện thoại" name="phone" value="<?=$CMS->input['phone'];?>" <?=($CMS->vars['is_login'] == 1 && ! empty($CMS->input['phone'])) ? 'readonly' : 'pattern="[0-9]{7,12}" title="Vui lòng nhập số điện thoại"';?>>
								</div>	
							</div>    										
						</div>
						<div class="row <?=( $tpl->error['address'])?'control with-errors':''?>">
							<div class="col-md-4">
								<?php if ( $tpl->error['address']) { ?>
								<div class="help-block ">
									<div class="arrow"></div>
									<div class="help-block-wrap">
										<?=$tpl->error['address']?>
									</div>
								</div>
								<?php } ?>
							</div>
							<div class="col-md-8">
								<div class="form-group">
									<input type="text" class="form-control input-register" placeholder="Địa chỉ" name="address" value="<?=$CMS->input['address'];?>" <?=($CMS->vars['is_login'] == 1 && ! empty($CMS->input['address'])) ? 'readonly' : 'title="Vui lòng nhập địa chỉ" maxlength="200"';?>>
								</div>	
							</div>    										
						</div>
						<div class="row">
							<div class="col-md-4"></div>
							<div class="col-md-8">
								<input type="submit" class="btn btn-block green-big ani" value="ĐĂNG KÝ">
							</div>
						</div>
					</form>
				</div>
				<?php if ($CMS->vars['is_login'] == 0) { ?>
				<div class="col-sm-6">
					<div class="col-md-8">
						<!-- <button data-toggle="modal" data-target="#nhanhoa_form_register" class="btn btn-block btn-social-big nhanhoa ani">Đăng ký ID Nhân Hòa</button> -->
						<button class="btn btn-block btn-social-big facebook ani" onclick="location.href='<?=$tpl->fb_url?>'">Đăng ký với Facebook</button>
						<button class="btn btn-block btn-social-big googleplus ani" onclick="location.href='<?=$tpl->gg_url?>'">Đăng ký với Google +</button>
					</div>
					<div class="col-md-4">	</div>							
				</div>
				<?php } ?>
			</div>
			<div class="row">
				<div class='col-md-12 text-center agree-term'>
					<p class="control with-errors">
						<span style="position: relative;display: inline-block;">
							<span class="help-block accept_policy_terms_msg" style="display:none;position:absolute;top:-117%;">
								<span class="arrow" style="bottom: -5px;left: 50%;top:auto;"></span>
								<span class="help-block-wrap">Vui lòng đọc qua Quy định sử dụng<br> và Chính sách bảo mật trước khi sử dụng</span>
							</span>
							<label class="checkbox-item" style="padding: 0px; margin: 0px;display: inline-block;">
								<input type="checkbox" class="checkbox-cb" name="accept_policy_terms" value="1" checked onclick="checkAcceptPolicyTerms()">
								<span class="checkbox-mark"></span>
							</label>
							Khi đăng ký là bạn đã đồng ý với <a target="_blank" href="/thong-tin/dieu-khoan-su-dung.html">quy định sử dụng</a> <br>& <a target="_blank" href="/thong-tin/chinh-sach-bao-mat.html">chính sách bảo mật</a> của Web4s
						</span>
					</p>
				</div>							
			</div>
			<? } ?>
		</div>
	</div>
</section>

<script>
// Check Accept Policy Terms
function checkAcceptPolicyTerms() {
	var accept_policy_terms = $('input[name="accept_policy_terms"]');
	if ( accept_policy_terms.length ) {
		if ( accept_policy_terms.is(':checked') ) {
			if ( $("#email_err").html().trim() == "" ) {
				$('#contact-form input[type="submit"]').removeAttr("disabled");
			}
			$('.accept_policy_terms_msg').css('display', 'none');
		} else {
			$('#contact-form input[type="submit"]').attr('disabled', 'disabled');
			$('.accept_policy_terms_msg').css('display', 'inline-block');
		}
	}
}
// End Check Accept Policy Terms
function checkErrors() {
	if ($("#email_err").html().trim() == "") {
		$('#contact-form input[type="submit"]').removeAttr("disabled");
	} else {
		$('#contact-form input[type="submit"]').attr('disabled', 'disabled');
		document.getElementById("email").scrollIntoView();
	}

	// Check Accept Policy Terms
	checkAcceptPolicyTerms();
}
$(document).ready(function() {
	checkErrors();
	$("input[type='text']#email").change(function() {
		$.post( 
			"<?=$CMS->vars['root_domain']?>/register/check_email_ajax",
			{ email: $("input[type='text']#email").val() },
			{ func: "getNameAndTime" }, "json"
		)
		.done(function(json) {
			if (json.status == 'success') {
				$('#email_err').empty();
				$('#email_err').parent().removeClass('control with-errors');
			} else {
				$('#email_err').parent().addClass('control with-errors');
				$('#email_err').html('<div class="help-block "><div class="arrow"></div><div class="help-block-wrap">' + json.message + '</div></div>');
			}
			checkErrors();
		})
		.fail(function(jqxhr, textStatus, error) {
			console.log("Request Failed: " + err);
		});
	});
});
</script>