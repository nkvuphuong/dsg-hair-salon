<form method="POST" action="/dang-ky-dung-thu/webstep1do">
<section class="section top-section bottom-sectiom register-trial-section">
    			<div class="section-wrap fluid-form">
	    			<div class="container">
						<div class="row padding-bottom-row">
							<div class="col-md-12 text-center">
								<h1 class="section-title text-normal text-orange">Chỉ một vài thao tác cho trang web của bạn tốt hơn</h1>		
								<p>Vui lòng cấu hình thông tin và giao diện trang web của bạn</p>	
							</div>
						</div>
				<?php 
						if(isset($_SESSION['error_msg']) AND $_SESSION['error_msg'] != ""){
					?>			
						<div class="row">
							<div class="col-sm-12 col-sm-push-0 col-md-10 col-md-push-1">
								<div class="alert alert-danger">
									<strong><?=$_SESSION['error_msg'];?></strong>
						   		</div>
						   	</div>
						</div>
						<? } ?>
						<div class="row">
							<div class="col-sm-12 col-sm-push-0 col-md-10 col-md-push-1">
								<div class="col-md-6">
								    	
							  		<div class="form-group">
							  			<input type="text" class="form-control" name="code_storename"  placeholder="Địa chỉ web"  >
							  			<i class="fa fa-question-circle ani" aria-describedby="tippy-tooltip-1" title="Tên miền sử dụng cho website"></i>
							  			<span class="fa fa-suffix">.<?=$CMS->vars['storename_domain'];?></span>
							  			<label class="checkbox-item">
											<input type="checkbox" name="private_domain" value="1" class="checkbox-cb">
											<span class="checkbox-mark"></span>
											<span class="checkbox-desc">Tôi muốn sử dụng tên miền riêng</span>
										</label>
							  		</div>
							  		 <span class="text-bold">Tên miền đăng ký</span>
									<div class="form-group">										
										 <span id="reg_domain">.<?=$CMS->vars['storename_domain'];?></span>
									</div>	

							  		<div class="form-group">
							  			<select class="form-control" name="industry">
							  			 <option value="1" selected="">Thời trang</option>
							  			 <option value="2" selected="">Mẹ &amp; Bé</option>
							  			 <option value="3" selected="">Bar - Cafe - Nhà hàng</option>
							  			 <option value="4" selected="">Mỹ phẩm</option>
							  			 <option value="5" selected="">Tạp hóa</option>
							  			 <option value="6" selected="">Siêu thị mini</option>
							  			 <option value="7" selected="">Điện thoại &amp; Điện máy</option>
							  			 <option value="8" selected="">Nông sản &amp; Thực phẩm</option>
							  			 <option value="9" selected="">Nội thất &amp; Gia dụng</option>
							  			 <option value="10" selected="">Vật liệu xây dựng</option>
							  			 <option value="11" selected="">Xe Máy &amp; Linh Kiện</option>
							  			 <option value="12" selected="">Nhà thuốc</option>
							  			 <option value="13" selected="">Hoa &amp; Quà tặng</option>
							  			 <option value="14" selected="">Sách &amp; Văn phòng phẩm</option>
							  			 <option value="15" selected="">Ngành hàng khác</option>      
							  			</select>
							  		</div>
							  		<div class="form-group">
							  			 <select class="form-control"  name="site_theme" id="site_theme" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['theme_err_title'];?>">
						                      <?=$tpl->data['option_theme'];?>
						                 </select>
							  		</div>
								</div>
								<div class="col-md-6 text-center">
									<img class="img-responsive" id="preview_theme" src="">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12 col-sm-push-0 col-md-10 col-md-push-1">
								<div class="col-md-12 text-center"><h4 class="part-title">Chọn gói web sử dụng</h4></div>
								
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
							 
							 <div class="col-sm-3 col-md-3">
									<div class="form-group">
										<label class="radio-item">
											<span class="radio-desc" style="font-weight:bold"><?=$value['packet_name'];?><br> 
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
								  
								
								 <input type="hidden" name="packet_id" value="<?=$tpl->packet_id;?>" />
							</div>
						</div>
						 
						<div class="row">
							<div class="col-md-12 text-center">
							<span class="h4 check-out-info">Chi phí web của bạn là <span class="text-orange" id="out_total"></span> cho <span id="out_month"></span> tháng sử dụng </span> <input type="submit" class="btn green-big ani check-out-btn" value="tiến hành thanh toán"></div>
						</div>
					</div>
				</div>
			</section>
</form>	
<script type="text/javascript">
	var packet_id = "<?=$tpl->packet_id;?>";
</script>		
  <script src="js/sites.js"></script>     