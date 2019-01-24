<!-- call_me . layouts -->
<section class="section phone-callout">			
	<div class="container">
		<div class="row callout-wrap">
			<div class="col-md-6 text-center-md">
				<h1>Bạn cần hỗ trợ?</h1>
				<h3>Chúng tôi sẽ gọi lại để tư vấn cho bạn</h3>
			</div>
			<div class="col-md-6 text-center-md" id="error_contact">
				<div class="input-group email-input-group input-group-lg">
					<input type="text" id="contact_phone" class="form-control white-big phone ani" placeholder="Nhập số điện thoại" style="height:50px;">
					<span class="input-group-addon green-big-addon ani" onclick="contactPhone(this)">gọi lại cho tôi</span>
				</div>
			</div>
		</div>
	</div>
</section>
<section id="intro-6" class="section">			
	<div class="container">
		<div class="row callout-wrap">
			<div class="col-md-12 text-center">
				<h1 >Tạo ngay một trang web tuyệt vời ngay hôm nay</h1>
				<p class="intro-detail">Đăng ký dùng thử miễn phí 10 ngày để khám phá</p>
			</div>
			<div class="col-md-12 text-center">
				<a href="<?=$CMS->vars['root_domain']?>/dang-ky-dung-thu-website.html"><input type="button" class="btn green-big ani" value="Dùng thử miễn phí"></a>
				<a class="scroll_jumpto_1" data-jumpto="#about_price" href="<?=$CMS->vars['root_domain']?>/bao-gia-thiet-ke-web.html"><input type="button" class="btn green-big outline ani" value="XEM CHI TIẾT BẢNG GIÁ"></a>
			</div>	
		</div>
	</div>
</section>

<script>
	$(document).ready(function(){
		$('.scroll_jumpto_1').click(function(event){

			var jumpto = $(this).data('jumpto');
			if ( $(jumpto).length > 0 ) {
				event.preventDefault();
				
                $('html, body').animate({
                    scrollTop: $(jumpto).offset().top
                }, 1000);
			}
		});
	});
</script>


<script>

$.notify.defaults({
	position : "top"
});
function contactPhone(e) {
	var contact_phone = $("input[type='text']#contact_phone").val();
	if (contact_phone.match(/^[0-9]{1,15}$/)) {
		$(e).notify("Loading...", "warn");
		$("#contact_phone").attr("disabled", "disabled");
		$(e).removeAttr("onclick");
		$.post( 
			"<?=$CMS->vars['root_domain']?>/contact/contact_phone_ajax",
			{ contact_phone : contact_phone },
			{ func: "getNameAndTime" }, "json"
		)
		.done(function(json) {
			if (json.status == 'success') {
				$('#error_contact').html('<div class="alert alert-success">' + json.message + '</div>');
			} else {
				$('#error_contact').notify(
					json.message, "error"
				);
				$("#contact_phone").removeAttr("disabled");
				$(e).attr("onclick", "contactPhone(this)");
			}
		})
		.fail(function(jqxhr, textStatus, error) {
			console.log("Request Failed: " + err);
		});
	} else {
		$('#contact_phone').notify(
			"Vui lòng nhập số điện thoại", "error"
		);
	}
}
</script>