<?=\core\ezy::tpl('you_web', 'client');?>
<section class="section customer-client-section top-section">
	<div class="section-wrap">
		<div class="container-fluid gray-bg">
			<div class="row">
				<div class="left-info-panel col-md-3">
					<?=\core\ezy::tpl('div_left', 'client');?>
				</div>
				<div class="right-web-container pink-bg col-md-9">
					<div class="user-web-items-wrap col-lg-12 fluid-form">
					<? if($_SESSION['error_msg']) { ?>
						<div class="row" >
							<div class="alert alert-danger">
								<strong>Có lỗi!</strong>
								<ul >
									 
									<li><?=$_SESSION['error_msg']?></li>
								 
								</ul>
							</div>
						</div>
						<? } ?>
			 		<div class="row" id="alert_msg" style="display:none">
							<div class="alert alert-danger">
								<strong>Có lỗi!</strong>
								<ul id="err_msg">
									 
									<li><?=$_SESSION['error_msg']?></li>
								 
								</ul>
							</div>
						</div>
					<div class="user-web-title-wrap">
										<h3 class="user-web-title">Thông tin website</h3>
					 </div>
					<form id="contact-form" class="contact-page-form" name="" action="<?=$CMS->vars['root_domain']?>/tai-khoan/kich-hoat-web-step1do" method="POST">
						
					<div class="user-web-item">	 
						<div class="row">
							<div class="col-sm-12 col-sm-push-0 col-md-4 col-md-push-1">
								<div class="control">					
									<div class="form-group">						
										<input type="text" class="form-control" name="code_storename" placeholder="Tên miền đăng ký">
											<i class="fa fa-question-circle ani" title="Tên miền sử dụng cho website"></i>
									</div>	
								</div>
							</div>
						</div>	 	
									 <div class="row">
										<div class="col-sm-12 col-sm-push-0 col-md-10 col-md-push-1">
											<div class="col-md-12 text-center"><h4 class="part-title">Chọn gói web sử dụng</h4></div>
											<input type="checkbox" checked style="display:none" name="private_domain" value="1" class="checkbox-cb">
											<input type="hidden"   style="display:none" name="site_code" value="<?=$tpl->site_code;?>" >

								<?php

									foreach ($tpl->data['list_package'] as $key => $value) { 
										if($value['packet_id'] == $tpl->packet_id)
										{ 
											$checked = "checked";
										}
										else
										{
											$checked = "";
										}

								?>
										 
										 <div class="col-sm-6 col-md-3">
												<div class="form-group">
													<label class="radio-item">
														<span class="radio-desc"><?=$value['packet_name'];?><br> 
													</label>
													<div class="row">
													 
															<div class="form-group">
																<label class="radio-item">
																	<input class="radio-cb" type="radio" <?=$checked;?> name="cycle" packet_id="<?=$value['packet_id'];?>" value="12" price="<?=$value['packet_price_12'];?>">
																	<span class="radio-mark"></span>
																	<span class="radio-desc">12 tháng- <?=$value['packet_price_12_bk'];?>/tháng</span>
																</label>
															</div>
														 
													</div>	
													<div class="row">
													 
															<div class="form-group">
																<label class="radio-item">
																	<input class="radio-cb" type="radio" name="cycle" packet_id="<?=$value['packet_id'];?>"  value="24" price="<?=$value['packet_price_24'];?>">
																	<span class="radio-mark"></span>
																	<span class="radio-desc">24 tháng <?=$value['packet_price_24_bk'];?>/tháng</span>
																</label>
															</div>
														 
													</div>	
												</div>
											</div>
								<?php			
									}
								?>
											
											 <input type="hidden" name="packet_id" value="<?=$tpl->packet_id;?>"  />
										</div>
									 
							 
								<div class="row">
										<div class="col-sm-12 col-sm-push-0 col-md-10 col-md-push-1">	 
										<input type="button" id="next_step" class="btn green-big ani" value="Tiếp tục" style="float: right;">							
						 		 </div>
						</div>
					  </div>	
					   </div>		
					</form>
					<div class="row">									
						<div class="col-md-12">
							<div class="h5 bottom-coppyright text-gray">
								<p style="color: #fff;"><?=\core\ezy::tpl('copyright_footer', 'layouts');?></p>	
							</div>										
						</div>
					</div>

					</div>
				</div> <!-- end 1-->
			</div>
		</div>	    			
	</div>
</section>
<script type="text/javascript">
	var packet_id = "<?=$tpl->packet_id;?>";
</script>		
  <script src="js/sites.js"></script>     
  <script type="text/javascript">
  	
  $("#next_step").click(function(){
 
  	var code_storename = $("input[name=code_storename]").val();
  	$("#alert_msg").hide();
  	if(code_storename.length == 0)
  	{	  
  		$("#err_msg").html("<li>Vui lòng nhập tên miền!</li>");
  		$("#alert_msg").show();
  		return false;
  	}

  	 if (/^[a-zA-Z0-9][a-zA-Z0-9-]{0,61}[a-zA-Z0-9](?:\.[a-zA-Z]{2,})+$/.test(code_storename)) {
           $("#contact-form").submit();
     }else
     {
     	$("#err_msg").html("<li>Tên miền không đúng định dạng!</li>");
  		$("#alert_msg").show();
     	return false;
     }
  });

  </script>