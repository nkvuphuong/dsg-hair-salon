<section class="section top-section register-trial-section">
	<div class="section-wrap">
		<div class="container">
			<div class="row">
				<div class="col-md-5 text-center">
					<br><br>
					<img class="img-responsive" src="images/register-trial-error_1.png">
				</div>
				<div class="col-md-7">
					<h1 class="section-title text-normal text-crimson">Rất tiếc, đã có lỗi xảy ra trong quá trình đăng ký</h1>		
					<br>	
					<p>Đã xảy ra lỗi làm cho quá trình đăng ký bị gián đoạn. Vui lòng đăng ký lại hoặc <span class="text-green">cho chúng tôi số điện thoại, chúng tôi sẽ gọi ngay cho bạn</span></p>	
					<br>
					<form id="contact-form" class="contact-page-form" >
						<div class="row control" id="error_contact">
							<div class="col-md-12">
								<div class="form-group">
									<input type="text" id="contact_phone" class="form-control white-big phone ani" placeholder="Nhập số điện thoại của bạn">
									<i class="fa fa-question-circle ani" title="Số đện thoại của bạn"></i>
								</div>	
							</div>    										
						</div>
						<div class="row">
							<div class="col-md-12">	
								<input type="button" class="btn green-big ani" value="GỌI LẠI CHO TÔI" onclick="contactPhone(this)">
								<a href="/dang-ky-dung-thu-website.html"><input type="button" class="btn green-big outline ani" value="quay về đăng ký lại"></a>
							</div>
						</div>
					</form>					
					<br><br><br><br>
				</div>
			</div>
		</div>
	</div>
</section>
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
				$(e).remove();
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
console.log('<?=$_SESSION['error_msg'];?>');
</script>