<section class="section top-section register-trial-section">
    			<div class="section-wrap">
	    			<div class="container">
						<div class="row">
							<div class="col-md-5 text-center">
								<br><br>
								<img class="img-responsive" src="images/register-trial-complete_1.png">
							</div>
							<div class="col-md-7">

					<?php 
						if($tpl->data['status'] == "success")
						{
					  ?>
							<h1 class="section-title text-normal text-green">Chúc mừng, bạn đã hoàn thành</h1>	
								<p>Đơn hàng: <?=$tpl->data['order']['transaction_name'];?>. Quý khách có thể truy cập vào hệ thống <a href="https://customer.nhanhoa.com">Customer.nhanhoa.com</a> để kiểm tra tình trạng đơn hàng</p>	
								<br>	
								<p>Nhân viên của chúng tôi sẽ kích hoạt trang web trong thời gian sớm nhất 
								hoặc liên hệ hotline <a href="tel:19006680" style="color:inherit;">19006680</a> để được hỗ trợ ngay</p>	
								<br>	
					<?php
						}else{	
					?>				
	 				
	 						 <h1 class="section-title text-normal text-green" style="color:red">Có lỗi trong quá trình đăng ký Website</h1>		
								<br>	
								<p>Quý khách vui lòng liên hệ hotline <a href="tel:19006680" style="color:inherit;">19006680</a> để được hỗ trợ ngay</p>	
								<br>	
					<?php			
						} 
					?>	

								
								<input type="button" class="btn green-big ani" value="GỌI NGAY CHO CHÚNG TÔI">
								<a href="<?=$CMS->vars['root_domain'];?>"><input type="button" class="btn green-big outline ani" value="QUAY VỀ TRANG CHỦ"></a><br><br>
								<p class="text-small">Chúng tôi tin rằng bạn sẽ cần thêm sự hỗ trợ trước khi quản trị một trang web</p>
								<p class="text-small"><i class="fa fa-check-circle">&nbsp&nbsp&nbsp&nbsp</i>Hướng dẫn bạn quản trị môt trang web4s</p>
								<p class="text-small"><i class="fa fa-check-circle">&nbsp&nbsp&nbsp&nbsp</i>Đọc những bài viết chia sẻ kinh nghiệm kinh doanh</p>
								<p class="text-small"><i class="fa fa-check-circle">&nbsp&nbsp&nbsp&nbsp</i>Cho chúng tôi số điện thoại, chúng tôi sẽ gọi lại cho bạn</p>
								<p class="text-small"><i class="fa fa-check-circle">&nbsp&nbsp&nbsp&nbsp</i>Nhận các bài viết / tin khuyến mãi qua email</p>
								<br><br><br><br>
							</div>
						</div>
					</div>
				</div>
 </section>