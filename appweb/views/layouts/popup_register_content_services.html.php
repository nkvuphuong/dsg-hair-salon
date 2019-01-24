<!-- add attribute data-toggle="modal" data-target="#popup-register-content-services" for selector need for open popup -->
<div class="modal fade popup-register-content-services" id="popup-register-content-services" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  	<div class="modal-dialog col-xs-full-width" role="document">
    	<div class="modal-content">
      		<div class="modal-body p50">
      			<div class="close-modal-button"  data-dismiss="modal" aria-label="Close"></div>
      			<h2 class="text-orange">Thông tin đăng ký của bạn</h2>
      			<div class="row">
    				<div class="col-sm-12 contact-form-outer br-none">
						<form name="register-content-services" id="register-content-services" class="contact-page-form register-content-services">

							<!-- Full name -->
							<div class="row control">
								<div class="col-md-12">
									<div class="form-group">
										<input type="text" name="regis_name" class="form-control" placeholder="Tên của bạn">
										<i class="fa fa-question-circle ani"></i>
									</div>
								</div>
							</div>

							<!-- Phone -->
							<div class="row control">
								<div class="col-md-12">
									<div class="form-group">
										<input type="telephone" name="regis_phone" class="form-control" placeholder="Số điện thoại" onkeypress="return check_enter_number(event, this);">
										<i class="fa fa-question-circle ani"></i>
									</div>
								</div>
							</div>

							<!-- Email -->
							<div class="row control">
								<div class="col-md-12">
									<div class="form-group">
										<input type="email" name="regis_email" class="form-control" placeholder="Email đăng ký">
										<i class="fa fa-question-circle ani"></i>
									</div>
								</div>
							</div>

							<!-- Industry -->
							<div class="row control">
								<div class="col-md-12">
									<div class="form-group">										
										<select name="regis_industry" class="form-control select" placeholder="Ngành nghề kinh doanh">
											<option value="">Ngành nghề kinh doanh</option>
											<option value="thoi_trang">Thời trang</option>
											<option value="my_pham">Mỹ phẩm</option>
											<option value="dien_thoai_va_dien_may">Điện thoại & Điện máy</option>
											<option value="vat_lieu_xay_dung">Vật liệu xây dựng</option>
											<option value="me_va_be">Mẹ & Bé</option>
											<option value="tap_hoa">Tạp hóa</option>
											<option value="nong_san_va_thuc_pham">Nông sản & Thực phẩm</option>
											<option value="xe_may_va_linh_kien">Xe Máy & Linh Kiện</option>
											<option value="bar_cafe_nha_hang">Bar - Cafe - Nhà hàng</option>
											<option value="sieu_thi_mini">Siêu thị mini</option>
											<option value="noi_that_va_gia_dung">Nội thất & Gia dụng</option>
											<option value="nganh_hang_khac">Ngành hàng khác</option>
										</select>
										<span class="fa fa-angle-down "></span>
									</div>
								</div>  										
							</div>

							<!-- website -->
							<div class="row control">
								<div class="col-md-12">
									<div class="form-group">
										<input type="text" name="regis_website" class="form-control" placeholder="Website của bạn">
										<i class="fa fa-question-circle ani"></i>
									</div>
								</div>
							</div>

							<!-- Location -->
							<div class="row control">
								<div class="col-md-12">
									<fieldset class="form-group">
									    <p class="clearfix">Bạn ở khu vực nào ?</p>
									    <div class="form-check pull-left mr-30">
									      	<label class="form-check-label">
									        	<input type="radio" class="form-check-input" name="regis_location" id="location1" value="hn" checked>
									        	<span>Hà Nội</span>
									      	</label>
									    </div>
									    <div class="form-check pull-left">
									    	<label class="form-check-label">
									        	<input type="radio" class="form-check-input" name="regis_location" id="location2" value="hcm">
									        	<span>Hồ Chí Minh</span>
									      	</label>
									    </div>
									</fieldset>
								</div>
							</div>

							<!-- Agree register -->
							<div class="row control">
								<div class="col-md-12 pt-20 ">
									<div class="form-group">
										<label class="checkbox-item inline-block">
											<input type="checkbox" name="regis_agree" value="1" class="checkbox-cb" checked="">
											<span class="checkbox-mark"></span>
										</label>
										<span>Yêu cầu nhận tư vấn</span>
									</div>
								</div>
							</div>

							<!-- Result -->
							<div class="row">
								<div class="col-md-12 pt-20 ">
									<div class="result" style="display: none;"></div>
								</div>
							</div>

							<!-- submit -->
							<div class="row">
								<div class="col-md-12">
									<input type="button" class="btn green-big ani btn-block send-go" value="Đăng ký ngay">
								</div>
							</div>
			  			</form>
					</div>
      			</div>
      		</div>
    	</div>
  	</div>
</div>
<style>
.popup-register-content-services .text-orange {
	margin-top: 0;
	margin-bottom: 38px;
}
.popup-register-content-services .br-none {
	border-right:none;
}
.popup-register-content-services .select {
	-webkit-appearance: none;
	-moz-appearance: none;
	text-indent: 1px;
	text-overflow: '';
}
.popup-register-content-services .pt-20 {
	padding-top: 20px;
}
.popup-register-content-services .inline-block {
	display: inline-block;
}
.popup-register-content-services .mr-30 {
	margin-right: 30px;
}
@media screen and ( max-width: 575px ){
	.col-xs-full-width {
		width: 100%;
	}
}
</style>
<script>
// Check input number
function check_enter_number(evt, onthis)
{
    if( isNaN(onthis.value + "" + String.fromCharCode(evt.charCode)) )
    {
        return false;
    }
}
$(document).ready(function(){

	// set msg
	function setMsg (element = '', msg = '') 
	{
		element = $(element).closest('.control');
		element.addClass('with-errors');

		element = element.find('.form-group');
		element.append('<i class="fa fa-times-circle ani"></i>');

		msg = $('<div class="error"><i class="fa fa-info"></i> ' + msg + '</div>');
		msg.insertBefore(element);
	}

	// remove msg
	function removeMsg (element = '')
	{
		element = $(element).closest('.control');
		element.removeClass('with-errors');
		element.find('.fa-times-circle').remove();
		element.find('.error').remove();
	}

	// function check input
	function formValidate ( element = '', msg = '', type = 'notempty' ) 
	{
		// remove msg
		removeMsg(element);

		// get value
		var value = $(element).val();

		// check is not empty
		if( type == 'notempty' ) 
		{
			if ( ! value )
			{
				msg = msg ? msg : 'Vui lòng nhập giá trị!';
				setMsg(element, msg);
				return false;
			}
		}
		// check is phone
		else if( type == 'isphone' )
		{
			if ( ! /^[0-9]{1,15}$/i.test(value) )
			{
				msg = msg ? msg : 'Vui lòng nhập số điện thoại!';
				setMsg(element, msg);
				return false;
			}
		}
		// check is email
		else if( type == 'isemail' ) 
		{
			// from js global nhacp
			if ( ! /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i.test(value) )
			{
				msg = msg ? msg : 'Vui lòng nhập email!';
				setMsg(element, msg);
				return false;
			}
		}
		// check is domain
		else if( type == 'isdomain' ) 
		{
			// remove http, https
			value = value.replace(/(^\w+:|^)\/\//i, '');

			// from https://stackoverflow.com/questions/13027854/javascript-regular-expression-validation-for-domain-name
			if ( ! /^((?:(?:(?:\w[\.\-\+]?)*)\w)+)((?:(?:(?:\w[\.\-\+]?){0,62})\w)+)\.(\w{2,6})$/i.test(value) )
			{
				msg = msg ? msg : 'Vui lòng nhập domain!';
				setMsg(element, msg);
				return false;
			}
		}

		// default return
		return true;
	}

	// Check agree send request
	$('input[name="regis_agree"]').click(function(){
		if ( $(this).is(':checked') ) 
		{
			removeMsg(this);
			$('form[name="register-content-services"] .send-go').removeAttr('disabled');
		} 
		else 
		{
			$('form[name="register-content-services"] .send-go').attr('disabled', 'disabled');
			setMsg(this, 'Vui lòng chọn đồng ý!');
		}
	});

	// Check form
	$('form[name="register-content-services"] .send-go').click(function(){

		// Get elements
		var name = $('form[name="register-content-services"] input[name="regis_name"]');
		var phone = $('form[name="register-content-services"] input[name="regis_phone"]');
		var email = $('form[name="register-content-services"] input[name="regis_email"]');
		var industry = $('form[name="register-content-services"] select[name="regis_industry"]');
		var website = $('form[name="register-content-services"] input[name="regis_website"]');
		var location = $('form[name="register-content-services"] input[name="regis_location"]');
		var agree = $('form[name="register-content-services"] input[name="regis_agree"]');

		// Validate elements
		var check_name = formValidate(name, 'Vui lòng nhập họ và tên!', 'notempty');
		var check_phone = formValidate(phone, 'Vui lòng nhập số điện thoại!', 'isphone');
		var check_email = formValidate(email, 'Vui lòng nhập email!', 'isemail');
		var check_industry = formValidate(industry, 'Vui lòng chọn ngành nghề!', 'notempty');
		var check_website = website.val() ? formValidate(website, 'Vui lòng nhập domain!', 'isdomain') : true;

		var check = true;
			check = check == true ? check_name : check;
			check = check == true ? check_phone : check;
			check = check == true ? check_email : check;
			check = check == true ? check_industry : check;
			check = check == true ? check_website : check;

		if( check == false )
		{
			return false;
		}

		// Send data
		var element_result = $('form[name="register-content-services"] .result');
			element_result.html(`
				<img style="float:unset;display:block;margin:0px auto;" src="images/icon-loading.gif">
			`);
			element_result.show();

		$.post( 
			"<?=$CMS->vars['root_domain'];?>/contact/register_content_services", 
			$('form[name="register-content-services"]').serialize(), 
			{ 
				func: "getNameAndTime" 
			}, 
			"json"
		)
		.done( function(json) {

			if( json.status == 'success' ) 
			{
				element_result.html(`
					<div class="alert alert-success" role="alert">
					 	<strong>Success!</strong> ` + json.message + `
					</div>
				`);

				// Time out to close modol
				setTimeout(function(){
					$('#popup-register-content-services').modal('hide');
				}, 5000);
			}
			else 
			{
				element_result.html(`
					<div class="alert alert-danger" role="alert">
					 	<strong>Error!</strong> ` + json.message + `
					</div>
				`);
			}
		})
		.fail(function(jqxhr, textStatus, error) {
			element_result.html(`
				<div class="alert alert-danger" role="alert">
				 	<strong>Error!</strong> Đã có lỗi xảy ra, vui lòng thử lại!.
				</div>
			`);
			console.log("Request Failed: " + error);
		});
	});
});
</script>