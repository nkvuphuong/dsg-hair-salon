<!-- widget tpl news -->
<ul class="list-group">
	
	<li class="list-group-item free-box  delay-0 scroll-to-show bottom-in">
		<h3 class="text-center">Bạn sẽ sở hữu ngay</h3>
		<h4 class="text-center red-bg">website miễn phí</h4>
		<form id="send_mail" action="<?=$CMS->vars['root_domain'];?>" method="POST">
			<div class="form-group">										    
				<input id="email" name="email" type="text" class="form-control" placeholder="Email đăng ký">
			</div>
			<label class="checkbox-item">
			<input type="checkbox" name="task_1" value="0" class="checkbox-cb" checked="">
			<span class="checkbox-mark"></span>
			<span class="checkbox-desc">Tôi đồng ý với <a target="_blank" href="/thong-tin/dieu-khoan-su-dung.html">quy định sử dụng</a> & <a target="_blank" href="/thong-tin/chinh-sach-bao-mat.html">chính sách bảo mật</a> của Web4s.vn</span>
			</label>
			<input type="button" class="btn btn-block btn-lg btn-success ani" value="BẮT ĐẦU TẠO WEBSITE" onclick="submitEmail()">
		</form>
	</li>	
	<li class="list-group-item head">
		<h5>ĐƯỢC ĐỌC NHIỀU NHẤT</h5>
	</li>	
</ul>
<ul class="media-list text-small hidden-sm hidden-xs">
	<?php foreach ($tpl->dataRecentPosts as $post) { ?>
	<li class="media">
		<div class="media-left" style="width: 35%;">
			<a href="<?=$post['url'];?>">
				<img class="media-object img-responsive" src="<?=\lib\input::getThumb($post['pathUpload'],100);?>" alt="tin tức website">
			</a>
		</div>
		<div class="media-body">
			<h5 class="media-heading"><?=\lib\input::substr($post['name'],0,30);?></h5>
			<i class="fa fa-calendar text-gray"></i><span class="text-gray"> <?=\lib\date::format($post['time']);?> </span>
		</div>
	</li>
	<?php } ?>
</ul>

<script>
$.notify.defaults({ position : "top" });
function submitEmail() {
	if (isEmail($("input[type='text']#email").val())) {
		$("form#send_mail").submit();
	} else {
		$("input[type='text']#email").notify(
			"Vui lòng nhập địa chỉ email", "error"
		);
	}
}

function isEmail(email) {
	if (email != undefined) {
		var emailReg = new RegExp(/^([\w-\.]+)@((?:[\w]+\.)+)([a-zA-Z]{2,4})/i);
		if (emailReg.test(email)) {
			return true;
		}
	}
    return false;
}
</script>		