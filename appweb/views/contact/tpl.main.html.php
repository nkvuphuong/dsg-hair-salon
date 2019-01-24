<section class="section top-section">
	<div class="section-wrap">
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					<h1 class="section-title text-orange"> Chúng tôi có mặt ở đây luôn sẵn sàng tư vấn cho bạn</h1>
					<p>Vui lòng chọn khu vực để được tư vấn tốt nhất</p>
				</div>
			</div>
		</div>
		<? if ($tpl->error || $tpl->success) { ?>
		<div class="container">
			<div class="row">
				<?php if ($tpl->error) { ?>
				<div class="alert alert-danger">
					<strong>Có lỗi!</strong> <?=$tpl->error?>
				</div>
				<?php } ?>
				<?php if ($tpl->success) { ?>
				<div class="alert alert-success">
					<strong>Thành công!</strong> <?=$tpl->success?>
				</div>
				<?php } ?>
			</div>
		</div>
		<? } ?>
		<div class="contact-page-tap-wrap">
			<div class="tab-nav container text-center">	
				<ul class="nav nav-pills">
					<li class="active">
						<a href="#1a" data-toggle="tab" class="text-uppercase">trụ sở hà nội</a>
					</li>
					<li class="">
					
						<a href="#2a" data-toggle="tab" class="text-uppercase">chi nhánh hồ chí minh</a>
					</li>
				</ul>
			</div>
			<div class="tab-content clearfix">
				<div class="tab-pane active container" id="1a">
					<div class="col-md-6">
					<h3>Trụ sở Hà Nội</h3>
						<p>32 Võ Văn Dũng, Đống Đa, Hà Nội.<br>
						sales@nhanhoa.com<br>
						(024) 7308 6680</p>
						<form id="contact-form" class="contact-page-form" action="/thong-tin-lien-he.html" method="post">	
							<div class="form-group">
								<input type="text" class="form-control" placeholder="Tên đầy đủ của bạn" name="contactname" value="<?=$CMS->input['contactname']?>">
								<i class="fa fa-question-circle ani" title="Tên đầy đủ của bạn"></i>
							</div>	    	
							<div class="form-group">
								<input type="text" class="form-control" placeholder="Địa chỉ email" name="contactemail" value="<?=$CMS->input['contactemail']?>">
								<i class="fa fa-question-circle ani" title="Địa chỉ email"></i>
							</div>
							<div class="form-group">
								<input type="text" class="form-control" placeholder="Tiêu đề" name="contactsubject" value="<?=$CMS->input['contactsubject']?>">
								<i class="fa fa-question-circle ani" title="Tiêu đề"></i>
							</div>
							<div class="form-group">
								<textarea class="form-control" placeholder="Nội dung" rows="4" name="contactcontent"><?=$CMS->input['contactcontent']?></textarea>
								<i class="fa fa-question-circle ani" title="Nội dung"></i>
							</div>
							<input type="submit" class="btn btn-block green-big ani" value="GỬI TIN NHẮN">
						</form>
					</div>
					<div class="col-md-6">
						<div style="width:100%;float:left;;padding-top: 20px;"><iframe src="https://maps.google.com/maps/ms?msa=0&msid=205971603583971081992.0004bd738211988d9153b&ie=UTF8&ll=21.01581,105.824195&spn=0,0&t=m&iwloc=0004bd7382127d828f677&output=embed" width="100%" height="410" frameborder="0" style="border:0" allowfullscreen></iframe></div>
					</div>
				</div>
				<div class="tab-pane container" id="2a">
					<div class="col-md-6">
						<h3>Chi Nhánh Hồ Chí Minh</h3>
						<p>270 Cao Thắng (nối dài), Phường 12, Quận 10, TP HCM.<br>
						hcmsales@nhanhoa.com<br>
						(028) 7308 6680</p>
						<form id="contact-form" class="contact-page-form" action="/thong-tin-lien-he.html" method="post">	
							<div class="form-group">
								<input type="text" class="form-control" placeholder="Tên đầy đủ của bạn" name="contactname" value="<?=$CMS->input['contactname']?>">
								<i class="fa fa-question-circle ani" title="Tên đầy đủ của bạn"></i>
							</div>	    	
							<div class="form-group">
								<input type="text" class="form-control" placeholder="Địa chỉ email" name="contactemail" value="<?=$CMS->input['contactemail']?>">
								<i class="fa fa-question-circle ani" title="Địa chỉ email"></i>
							</div>
							<div class="form-group">
								<input type="text" class="form-control" placeholder="Tiêu đề" name="contactsubject" value="<?=$CMS->input['contactsubject']?>">
								<i class="fa fa-question-circle ani" title="Tiêu đề"></i>
							</div>
							<div class="form-group">
								<textarea class="form-control" placeholder="Nội dung" rows="4" name="contactcontent"><?=$CMS->input['contactcontent']?></textarea>
								<i class="fa fa-question-circle ani" title="Nội dung"></i>
							</div>
							<input type="submit" class="btn btn-block green-big ani" value="GỬI TIN NHẮN">
						</form>
					</div>
					<div class="col-md-6">
						<div style="width:100%;float:left;;padding-top: 20px;"><iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7838.9636481101015!2d106.67616076328014!3d10.774360991426926!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x1a6f65f1ee1fdb9b!2zQ8O0bmcgdHkgVE5ISCBQaOG6p24gbeG7gW0gTmjDom4gSG_DoA!5e0!3m2!1svi!2s!4v1499172198324" width="100%" height="410" frameborder="0" style="border:0" allowfullscreen></iframe></div>
					</div>
				</div>					
			</div>											
		</div>
	</div>
</section>