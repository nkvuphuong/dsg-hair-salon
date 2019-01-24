<?if( empty($tpl->themes) ){?>
<div class="col-xs-12 col-sm-12 col-md-12 theme-item-outer delay-0 scroll-to-show bottom-in in-view text-center">
	<p>Đang cập nhật ...</p>
</div>

<?}else{ foreach( $tpl->themes as $theme ){?>
<div class="col-xs-6 col-sm-6 col-md-3 theme-item-outer delay-0 scroll-to-show bottom-in in-view">							
	<div class="theme-item shadow">
		<div class="image-top toggle-state">
			<img class="theme-img img-responsive ani" src="<?=$theme['url_img_preview'];?>" alt="<?=$tpl->alt;?>">
			<div class="iphone ani">
				<div class="screen">
					<img class="theme-mobile img-responsive ani" src="<?=$theme['url_img_mobile'];?>" alt="<?=$tpl->alt;?>">
				</div>
			</div>
			<div class="detail-btn ani">
				<a target="_blank" href="http://<?=$theme['template'];?>.demo.net.vn">
					<input type="button" class="btn btn-block btn-opacity ani" value="Xem giao diện thực tế">
				</a>
			</div>
			<div class="buy-now-btn ani">
				<div class="row">
					<div class="col-xs-6 text-right" style="padding-right:5px">
						<a href="/dang-ky-dung-thu/webstep1/?theme=<?=$theme['template'];?>">
							<input type="button" style="margin-left:0" class="btn  btn-orange-md ani" value="Đăng ký">
						</a>
					</div>
					<div class="col-xs-6 text-left" style="padding-left:5px">
						<a href="/dang-ky-dung-thu-website.html/?theme=<?=$theme['template'];?>&warehouse=0">
							<input type="button" style="margin-left:0" class="btn  btn-green-md-outline ani" value="dùng thử">
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="info-bottom">																				
			<div class="info-left ani"><h5>Giao diện: <span class="text-capitalize"><?=$theme['template'];?></span></h5></div>
			<div class="icon-right ani">
				<a class="btn-view-demo-outner" href="http://<?=$theme['template'];?>.demo.net.vn">
					<div class="btn-view-demo">Xem Demo</div>
				</a>
			</div>
		</div>											
	</div>			
</div>
<?}}?>