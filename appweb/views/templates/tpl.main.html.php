<section class="section top-section center-header-section">
	<div class="section-inner">
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					<h1 class="section-title so-big-title text-green" style="margin-bottom:30px;">Gần 400 giao diện cho website bán hàng, <br class="hidden-xs hidden-sm">giúp bạn có thể kinh doanh ngay</h1>
					<a href="/dang-ky-dung-thu-website.html">
						<input type="button" style="margin-left:0;" class="btn btn-outline-1 btn-orange cta ani" value="Dùng thử miễn phí 10 ngày">
					</a>
				</div>
			</div>
		</div>
	</div>
</section>
<section id="price-table" class="section">	
	<div class="section-wrap">
		<ul class="nav nav-with-arrow nav-filter nav-pills"> 
			<li role="presentation" data-active="">
				<a href="/kho-giao-dien-thiet-ke-moi.html">Tất cả</a>
			</li>
			<li role="presentation" data-active="moi-nhat">
				<a href="/kho-giao-dien-thiet-ke-moi/bo-suu-tap/moi-nhat.html">Mới nhất</a>
			</li>
			<li role="presentation" data-active="su-dung-nhieu-nhat">
				<a href="/kho-giao-dien-thiet-ke-moi/bo-suu-tap/su-dung-nhieu-nhat.html">Sử dụng nhiều nhất
					<button class="btn btn-in-nav btn-orange">HOT</button>
				</a>
			</li>
			<li role="presentation" class="dropdown" data-active="ban-hang">
				<a class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> Bán hàng <span class="fa fa-angle-down"></span></a> 
				<ul class="dropdown-menu dropdown-double-size"> 
					<div class="col-md-6">
    					<li data-active="phu-kien-dien-thoai">
    						<a href="/thiet-ke-website-da-nganh/phu-kien-dien-thoai.html"><i class="fa fa-angle-double-right"></i> Phụ kiện điện thoại</a>
    					</li>
						<li data-active="dong-ho">
							<a href="/thiet-ke-website-da-nganh/dong-ho.html"><i class="fa fa-angle-double-right"></i> Đồng hồ</a>
						</li> 
						<li data-active="tui-xach">
							<a href="/thiet-ke-website-da-nganh/tui-xach.html"><i class="fa fa-angle-double-right"></i> Túi xách</a>
						</li> 
						<li data-active="giay-dep">
							<a href="/thiet-ke-website-da-nganh/giay-dep.html"><i class="fa fa-angle-double-right"></i> Giày dép</a>
						</li>
					</div>
					<div class="col-md-6">
						<li data-active="my-pham">
							<a href="/thiet-ke-website-da-nganh/my-pham.html"><i class="fa fa-angle-double-right"></i> Mỹ phẩm</a>
						</li>
						<li data-active="noi-that">
							<a href="/thiet-ke-website-da-nganh/noi-that.html"><i class="fa fa-angle-double-right"></i> Thiết kế bán Nội thất</a>
						</li> 
						<li data-active="hoa">
							<a href="/thiet-ke-website-da-nganh/hoa.html"><i class="fa fa-angle-double-right"></i> Hoa</a>
						</li> 
						<li data-active="oto">
							<a href="/thiet-ke-website-da-nganh/oto.html"><i class="fa fa-angle-double-right"></i> Xe hoi</a>
						</li>
					</div>
					
				</ul> 
			</li>
			<li role="presentation" data-active="bat-dong-san">
				<a href="/kho-giao-dien-thiet-ke-moi/bo-suu-tap/bat-dong-san.html">Bất động sản</a>
			</li>
			<li role="presentation" data-active="doanh-nghiep">
				<a href="/kho-giao-dien-thiet-ke-moi/bo-suu-tap/doanh-nghiep.html">Doanh nghiệp</a>
			</li>
			<li role="presentation" data-active="du-lich">
				<a href="/kho-giao-dien-thiet-ke-moi/bo-suu-tap/du-lich.html">Du lịch</a>
			</li>
		</ul>
		<script>
			var collectionsActive = '<?=isset($tpl->collectionsActive) ? $tpl->collectionsActive : '';?>';
			$(document).ready(function(){
				$('.nav-filter li[data-active="'+collectionsActive+'"]').addClass('active');
				$('.nav-filter li[data-active="'+collectionsActive+'"]').closest('ul').parent('li').addClass('active');
			});
		</script>
		<div class="container pt50">
			<div class="row">
				<div class="col-md-12 text-center storage-price-table-wrap">
					<h1 class="text-green">Chọn ngay giao diện website mà bạn thích</h1>
				</div>
			</div>
		</div>
	</div>
</section>
<section class="section themes-section pb-30">
	<div class="section-wrap">
		<div class="container">
			<div class="row">
				<div class="col-md-12 themes-wrap">
					<?=\core\ezy::render('themes_data', 'templates');?>
				</div>
			</div>
		</div>    				
	</div>
</section>
<section id="price-table" class="section pakage-section pt50 pb50">	
	<div class="section-wrap">
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center storage-price-table-wrap">
					<h1 id="about_price" class="text-white">Nhiều gói hơn, nhiều sự lựa chọn tuyệt vời hơn</h1>
				</div>
			</div>
			<div class="row" style="padding-bottom:20px">
				<div class="col-sm-push-0 col-sm-6 col-md-6 col-lg-4">
					<div class="storage-price-tag-wrap delay-0 scroll-to-show bottom-in in-view" style="position:relative">
						<div class="price-detail-outter">
						  	<table class="table price-detail">
						  		<tbody>
						  			<tr>			  		
									    <td colspan="2" class="price-name">
									    	<img src="images/shop-pakage.png" alt="Kho giao diện website">
									    </td>
									</tr>
							  		<tr>			  		
									    <td colspan="2" class="price-name">
									    	Website Bán Hàng
									    </td>
									</tr>
							  		<tr>			  		
									    <td colspan="2" class="price-slogan">
									    	Dành riêng cho ngành hàng ăn uống, thời trang, điện thoại, điện máy...
									    </td>
									</tr>
							  		<tr>			  		
									    <td colspan="2" class="price-amount">
									    	<span class="price-devider"></span>196.000<span class="price-currency"> vnđ/th</span>
									    </td>
									</tr>
							  		<tr>			  		
									    <td colspan="2" class="price-discount">
									    	<div class="price-discount-wrap text-center" style="border-bottom:none">
									    		- Tăng doanh số bán hàng<br>
									    		- Tăng thứ hạng trong tìm kiếm Google<br>
									    		- Tích hợp Live chat tư vấn KH<br>
									    		- Tích hợp thanh toán trực tuyến<br>
									    		- Hiển thị trên mọi thiết bị<br>
									    		- Bảo mật thông tin khách hàng
									    	</div>
									    </td>
								  	</tr>
									<tr>
										<td colspan="2">
									  		<a href="/bao-gia-thiet-ke-web/website-ban-hang.html" style="text-decoration:none;">
									  			<input type="button" style="margin-left:0" class="btn btn-outline-1 btn-orange ani btn-block" value="TRẢI NGHIỆM MIỄN PHÍ">
									  		</a>
										</td>
									</tr>
								</tbody>
							</table>
						</div>									
					</div>
				</div>
				<div class="col-sm-push-0 col-sm-6 col-md-6 col-lg-4">
					<div class="storage-price-tag-wrap delay-0 scroll-to-show bottom-in in-view" style="position:relative">
						<div class="price-detail-outter">
						  	<table class="table price-detail">
						  		<tbody>
						  			<tr>			  		
									    <td colspan="2" class="price-name">
									    	<img style="margin-bottom: 6px;" src="images/heritage-pakage.png" alt="Kho giao diện website">
									    </td>
									</tr>
							  		<tr>			  		
									    <td colspan="2" class="price-name">
									    	Website Bất Động Sản
									    </td>
									</tr>
							  		<tr>			  		
									    <td colspan="2" class="price-slogan">
									    	Dành riêng cho các nhà Môi giới, địa ốc, chủ đầu tư...
									    </td>
									</tr>
							  		<tr>			  		
									    <td colspan="2" class="price-amount">
									    	<span class="price-devider"></span>196.000<span class="price-currency"> vnđ/th</span>
									    </td>
									</tr>
							  		<tr>			  		
									    <td colspan="2" class="price-discount">
									    	<div class="price-discount-wrap text-center" style="border-bottom:none;padding-bottom:29px;">
									    		- Tính năng niêm yết giá sản phẩm<br>
												- Thiết kế website tối ưu chuẩn SEO<br>
												- Trang thông tin cá nhân<br>
												- Tương thích mọi thiết bị<br>
												- Tính năng đăng thông tin để nhận tư vấn<br>
												- Tốc độ tải trang nhanh
									    	</div>
									    </td>
								  	</tr>
									<tr>
										<td colspan="2">
											<a href="/bao-gia-thiet-ke-web/website-bat-dong-san.html" style="text-decoration:none;">
									  			<input type="button" style="margin-left:0" class="btn btn-outline-1 btn-orange ani btn-block" value="TRẢI NGHIỆM MIỄN PHÍ">
									  		</a>
										</td>
									</tr>
								</tbody>
							</table>
						</div>									
					</div>
				</div>
				<div class="col-sm-push-3 col-sm-6 col-md-push-3 col-md-6 col-lg-push-0 col-lg-4">
					<div class="storage-price-tag-wrap delay-0 scroll-to-show bottom-in in-view" style="position:relative">
						<div class="price-detail-outter">
						  	<table class="table price-detail">
						  		<tbody>
						  			<tr>			  		
									    <td colspan="2" class="price-name">
									    	<img style="margin-bottom: 9px;" src="images/enterprise-pakage.png" alt="Kho giao diện website">
									    </td>
									</tr>
							  		<tr>			  		
									    <td colspan="2" class="price-name">
									    	Website Doanh nghiệp
									    </td>
									</tr>
							  		<tr>			  		
									    <td colspan="2" class="price-slogan">
									    	Dành riêng cho công ty du lịch, báo điện tử, cung cấp sản phẩm dịch vụ...
									    </td>
									</tr>
							  		<tr>			  		
									    <td colspan="2" class="price-amount">
									    	<span class="price-devider"></span>196.000<span class="price-currency"> vnđ/th</span>
									    </td>
									</tr>
							  		<tr>			  		
									    <td colspan="2" class="price-discount">
									    	<div class="price-discount-wrap text-center" style="border-bottom:none">
									    		- Website chuẩn SEO<br>
												- Thúc đẩy doanh số<br>
												- Hỗ trợ xây dựng thương hiệu trên Internet<br>
												- Tương thích trên mọi thiết bị<br>
												- Thể hiện sự chuyên nghiệp của công ty<br>
												- Tốc độ tải trang nhanh
									    	</div>
									    </td>
								  	</tr>
									<tr>
										<td colspan="2">
											<a href="/bao-gia-thiet-ke-web/website-doanh-nghiep.html" style="text-decoration:none;">
									  			<input type="button" style="margin-left:0" class="btn btn-outline-1 btn-orange ani btn-block" value="TRẢI NGHIỆM MIỄN PHÍ">
									  		</a>
										</td>
									</tr>
								</tbody>
							</table>
						</div>									
					</div>
				</div>							
			</div>
		</div>    				
	</div>	
</section>
<?=\core\ezy::render('industry_data', 'templates');?>
<?=\core\ezy::render('call_me', 'layouts');?>