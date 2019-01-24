<!-- add class re-send-call for selector need for open popup -->
<div class="popup-re-call" style="display: none;">
    <div class="content_popup">
        <a href="#" class="re-close-popup close" data-dismiss="alert" title="close">×</a>
        <div class="input-content">
            <img src="https://nhanhoa.com/templates/images/calendar.png">
            <section class="form_re_call">
	            <div class="text-hello"> Xin chào,</div>
	            <p class="conten-sub">Vui lòng nhập thông tin để chúng tôi liên hệ lại với bạn theo lịch hẹn.</p>

	            <form name="re-call">
	            	<!-- Phone -->
		            <input class="request_phone form-control" name="contact_phone" value="" placeholder="Số điện thoại liên hệ lại" type="text">
		            <div class="phone_error alert-danger error-content"></div>

		            <!-- Note -->
		            <textarea class="request_content form-control" name="contact_content" placeholder="Nội dung thắc mắc?"></textarea>
		            
		            <!-- Localtion -->
		            <div>
		                <p class="conten-sub">Bạn Ở Khu Vực Nào?</p>

		                <input id="loc_req_1" name="contact_location" value="hn" class="css-checkbox-x2 request_location" checked="checked" type="radio">
		                <label for="loc_req_1" class="css-label-x2">HN</label>

		                <input id="loc_req_2" name="contact_location" value="hcm" class="css-checkbox-x2 request_location" type="radio">
		                <label for="loc_req_2" class="css-label-x2">HCM</label>
		            </div>

		            <div class="submit-content error_send error-send-content recall_error" style="display:none;"></div>

		            <div class="submit-content recall_submit">
		                <span class="send-go btn">Gửi Đi</span>
		                <div class="go-hotline"> Gọi hotline <span>0986 94 63 46</span> (24/7)</div>
		            </div>
		        </form>
		    </section>
        </div>

        <div class="recall_loading" style="display:none">
        	<img style="float:unset;display:block;margin:0px auto;" src="https://nhanhoa.com/templates/images/icon/icon-loading-bar1.gif">
        </div>
        <div class="success-conten recall_success" style="display:none">
            <img src="https://nhanhoa.com/templates/images/success.png">
            <div class="text-hello"> Thành công,</div>
            <p class="conten-sub">Chúng tôi sẽ liên hệ lại với bạn trong thời gian sớm nhất.</p>
            <p class="conten-warning">Rất lấy làm xin lổi nếu như vấn đề này làm bạn khó chịu!</p>
            <div class="submit-content"><div class="go-hotline">Thông báo sẽ tự động tắt trong 5 giây</div></div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){

	// Show and hide block
	$('.re-send-call').click(function(){
		$('.popup-re-call').show();
	});
	$('.re-close-popup').click(function(){
		$('.popup-re-call').hide();
	});

	$('form[name="re-call"] .send-go').click(function(){

		// Get values
		var contact_phone = $('form[name="re-call"] input[name="contact_phone"]').val();
		var contact_content = $('form[name="re-call"] textarea[name="contact_content"]').val();
		var contact_location = $('form[name="re-call"] input[name="contact_location"]:checked').val();

		// Check phone
		if( contact_phone.match(/^[0-9]{1,15}$/) )
		{
			// Send data
			$('.recall_loading').show();
			$('.recall_error').hide();

			$.post( 
				"<?=$CMS->vars['root_domain'];?>/contact/contact_recall_ajax",
				{ 
					contact_phone : contact_phone, 
					contact_content: contact_content, 
					contact_location: contact_location, 
				},
				{ 
					func: "getNameAndTime" 
				}, 
				"json"
			)
			.done(function(json) 
			{
				$('.recall_loading').hide();

				if( json.status == 'success' ) 
				{
					$('.form_re_call').hide();
					$('.recall_success .conten-sub').html(json.message);
					$('.recall_success').show();

					// Time out
					setTimeout(function(){
						$('.popup-re-call').hide();
					}, 5000);
				}
				else 
				{
					$('.recall_error').html(json.message);
					$('.recall_error').show();
				}
			})
			.fail(function(jqxhr, textStatus, error) 
			{
				console.log("Request Failed: " + error);
			});
		}
		else
		{
			$('form[name="re-call"] input[name="contact_phone"]').css('background', '#f2dede');
			$('.phone_error').html('Số điện thoại không hợp lệ');

			return false;
		}

	});
});
</script>