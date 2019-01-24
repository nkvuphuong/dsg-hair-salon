<!-- div_left tpl client -->
<div class="row">
	<div class="col-sm-6 col-md-12">
		<div class="welcome-user-avartar"><a href="/tai-khoan.html"><img class="img-responsive" src="images/fa-user-lg.jpg"></a></div>
		<a href="/tai-khoan.html" style="text-decoration:none;"><h3 class="welcome-user-name text-orange tippy" title="<?=$_SESSION['member']['cus_email'];?>">
		<?php 
			if($_SESSION['member']['cus_email'] != "")
			{
				$short_email = explode("@", $_SESSION['member']['cus_email']);
			}

		?>
		<?=$short_email['0'];?>@...
		</h3></a>	
	</div>
	<div class="col-sm-6 col-md-12">
		<div class="welcome-user-action">
			<p class="text-small"><a class="btn btn-blockbtn-md btn-text-normal" href="https://id.nhanhoa.com/usercp/edit_profile.html"><i class="fa fa-check-circle">&nbsp;&nbsp;&nbsp;&nbsp;</i>Thay đổi thông tin cá nhân</a></p>
			<p class="text-small"><a class="btn btn-blockbtn-md btn-text-normal" href="/tai-khoan/change_pass"><i class="fa fa-check-circle">&nbsp;&nbsp;&nbsp;&nbsp;</i>Thay đổi mật khẩu</a></p>
			<p class="text-small"><a class="btn btn-blockbtn-md btn-text-normal" href="/kho-giao-dien-thiet-ke-moi.html"><i class="fa fa-check-circle">&nbsp;&nbsp;&nbsp;&nbsp;</i>Tạo thêm trang web mới</a></p>
		</div>
		<a href="/login/logout"><input type="button" class="btn green-big outline ani" value="Đăng xuất"></a>
	</div>	
</div>