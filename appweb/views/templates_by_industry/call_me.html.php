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
					<span class="input-group-addon green-big-addon ani" onclick="sendCallMe(this)">gọi lại cho tôi</span>
				</div>
			</div>
		</div>
	</div>
</section>

<script>
$.notify.defaults({
	position : "top"
});
function sendCallMe(e) 
{
	var contact_phone = $("input[type='text']#contact_phone").val();
	if (contact_phone.match(/^[0-9]{1,15}$/)) 
	{
		$(e).notify("Loading...", "warn");
		$("#contact_phone").attr("disabled", "disabled");
		$(e).removeAttr("onclick");

		$.post( 
			"<?=$CMS->vars['root_domain']?>/contact/contact_phone_ajax",
			{ contact_phone : contact_phone },
			{ func: "getNameAndTime" }, "json"
		)
		.done(function(json) 
		{
			if (json.status == 'success') 
			{
				$('#error_contact').html('<div class="alert alert-success">' + json.message + '</div>');
			} 
			else 
			{
				$('#error_contact').notify(
					json.message, "error"
				);
				$("#contact_phone").removeAttr("disabled");
				$(e).attr("onclick", "contactPhone(this)");
			}
		})
		.fail(function(jqxhr, textStatus, error) 
		{
			console.log("Request Failed: " + err);
		});
	} 
	else 
	{
		$('#contact_phone').notify(
			"Vui lòng nhập số điện thoại", "error"
		);
	}
}
</script>