<?=\core\ezy::tpl('you_web', 'client');?>
<section class="section customer-client-section top-section ">
	<div class="section-wrap">
		<div class="container-fluid gray-bg">
			<div class="row">
				<div class="left-info-panel col-md-3">
					<?=\core\ezy::tpl('div_left', 'client');?>
				</div>
				<div class="right-web-container pink-bg col-md-9">
					<div class="user-web-items-wrap col-lg-12 fluid-form">
					 
					 <div class="user-web-title-wrap">
										<h3 class="user-web-title">Thanh toán</h3>
					 </div>

					 <form method="POST" id="frm_reg2" action="<?=$CMS->vars['root_domain'];?>/tai-khoan/kich-hoat-web-step2do">	
					<div class="user-web-item">	 	
						<div class="row">
							<div class="col-sm-12">
								<div class="row">
									<div class="col-md-8 col-md-push-2">
										<br>
										<h4 class="part-title1 text-center" >Chọn phương thức thanh toán</h4>
										<div class="tab-mobile-select">
											<div class="form-group">
												<select id='payTabSelect' class="form-control" >
											        <option value='0' href="#b1" tab="1" data-toggle="tab">Thanh toán trực tiếp</option>
											        <option value='8' href="#b2" tab="2" data-toggle="tab">Thanh toán qua Ngân Lượng</option>
											        <option value='1' href="#b3" tab="3" data-toggle="tab">Thanh toán qua chuyển salonản</option>
											    	<?php
										if($_SESSION['member']['cus_is_reseller'] == 1)
									{
									?>

												 <option value='4' href="#b4" tab="4" data-toggle="tab">Thanh toán qua tài salonản đại lý</option>

 
									<?php } ?>			
									    
											    </select>
											</div>	
										</div>
									</div>	

									<div class="col-sm-12">
										<div class="tab-desktop-select tab-nav tab-opacity text-center">	
											<ul id="payTab" class="nav nav-pills">
												<li class="active" href="#b1" payment="0" data-toggle="tab">
													<label class="radio-item">
														<input class="radio-cb" type="radio" name="payment_method" checked value="0">
														<span class="radio-mark"></span>
														<span class="radio-desc">Thanh toán <br>trực tiếp</span>
													</label>							        
												</li>
												<li class="" href="#b2"  payment="8" data-toggle="tab">								
													<label class="radio-item">
														<input class="radio-cb" type="radio" name="payment_method" value="8"  >
														<span class="radio-mark"></span>
														<span class="radio-desc">Thanh toán qua <br>Ngân Lượng</a></span>
													</label>
												</li>
												 
												<li class="" href="#b3"  payment="1" data-toggle="tab">
													<label class="radio-item">
														 <input class="radio-cb" type="radio"  name="payment_method" value="1">  
														<span class="radio-mark"></span>
														<span class="radio-desc">Thanh toán qua <br>chuyển salonản</a></span>
													</label>
												</li>
									<?php
										if($_SESSION['member']['cus_is_reseller'] == 1)
									{
									?>


												<li class="" href="#b4"  payment="4" data-toggle="tab">
													<label class="radio-item">
														 <input class="radio-cb" type="radio"  name="payment_method" value="4">  
														<span class="radio-mark"></span>
														<span class="radio-desc">Thanh toán qua<br> tài salonản đại lý</a></span>
													</label>
												</li>
									<?php } ?>			


											</ul>
										</div>
									</div>

									<div class="col-md-8 col-md-push-2">
										<div class="tab-content clearfix">
											<div class="tab-pane active" id="b1">
												<div class="pay-type-item">
													<h4>Khu vực Hà Nội</h4>
													<p>32 Võ Văn Dũng, Đống Đa, Hà Nội  -  Tel: (024) 7308 6680</p>
												</div>
												<div class="pay-type-item">
													<h4>Khu vực Hồ Chí Minh</h4>
													<p>270 Cao Thắng (nối dài), Phường 12,Quận 10  -  Tel: (028) 7308 6680</p>
												</div>
											</div>
											<div class="tab-pane" id="b2">
												 
											</div>
											 
											<div class="tab-pane" id="b3">
												
												<div class="pay-type-item techcombank">
													<h4>Ngân hàng Techcombank - trụ Sở Hà Nội</h4>
													<p>Chủ TK: Công ty TNHH Phần Mềm Nhân Hòa - Số TK: 11110137258015</p>
													<p>Chi nhánh: Hà Nội</p>
												</div>
												<div class="pay-type-item techcombank">
													<h4>Ngân hàng Techcombank - Chi Nhánh TP HCM</h4>
													<p>Chủ TK: Chi Nhánh Công ty TNHH Phần Mềm Nhân Hòa Tại Tp.Hồ Chí Minh - Số TK: 19027511408012</p>
													<p>Chi nhánh: Đông Sài Gòn</p>
												</div>
 
												<div class="pay-type-item vietcombank">
													<h4>Ngân hàng Vietcombank - Trụ sở Hà Nội</h4>
													<p>Chủ TK: Công ty TNHH phần mềm Nhân Hòa - Số TK:  0491000089315</p>
													<p>Chi nhánh: Thăng Long</p>
												</div>
												<div class="pay-type-item vietcombank">
													<h4>Ngân hàng Vietcombank - Chi nhánh TP HCM</h4>
													<p>Chủ TK: CN CT TNHH PHẦN MỀM NHÂN HÒA TẠI TPHCM- Số TK: 0071000805777</p>
													<p>Chi nhánh: Phú Thọ</p>
												</div>

												 
											</div>
											<div class="tab-pane" id="b4">
												 <?php
												 	if(isset($_SESSION['price_reseller']) AND $_SESSION['price_reseller'] != "")
												 	{
												       echo  $_SESSION['price_reseller']; 

												  	} ?>
											</div>
										</div>
										<!-- tab content -->	
									</div>
																	
								</div>
							</div>
							<!-- end col-sm-12 -->
						</div>

						<div class="row">
							<div class="col-md-12 text-center">
							<span class="h4 check-out-info" >Chi phí web của bạn là <span class="text-orange"><?=$tpl->data['total_bk'];?></span> cho <?=$tpl->data['cycle'];?> tháng sử dụng </span> <input type="button" id="submit_form" class="btn green-big ani check-out-btn" value="tiến hành thanh toán"></div>
						</div>
					 </div>	
					</form>	
					<div class="row">									
						<div class="col-md-12">
							<div class="h5 bottom-coppyright text-gray">
								<p  style="color: #fff;"><?=\core\ezy::tpl('copyright_footer', 'layouts');?></p>	
							</div>										
						</div>
					</div>
				</div>
			   	</div>	
			</div>
		</div>	    			
	</div>
</section>
 

<? if($CMS->input['referer'] == 1) { ?>
<script>$( ".btn.nhanhoa.ani" ).trigger("click");</script>
<? } ?>			
<script type="text/javascript">
	
	$(".nav-pills li").on("click",function(){

   var payment = $(this).attr("payment");
   $("input[name='payment_method'][value='"+payment+"']").prop("checked",true);

});

	$('#payTabSelect').on('change', function(e) {
		var op = $(this).val();
		var tab_bk = $('#payTabSelect option[value='+op+']').attr("tab");
		$(".tab-pane").hide();
		$('#b'+tab_bk).show();
		 $("input[name='payment_method'][value='"+op+"']").prop("checked",true);
	});

		 
</script>
<script type="text/javascript" src="js/jquery.loadingModal.js"></script>	
 
<script type="text/javascript">
function showModal() {
		$('body').loadingModal({text: 'Hệ thống đang xử lý, vui lòng chờ trong giây lát...', 'animation': 'fadingCircle'});
		setTimeout(function(){$('body').loadingModal('destroy') ;}, 15000)
 }
		
$("#submit_form").click(function(){
	$("#frm_reg2").submit();
	showModal();
});

	
</script>	
<link href="css/jquery.loadingModal.css" rel="stylesheet">    

