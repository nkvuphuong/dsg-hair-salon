<!-- menu_mobile tpl client -->
<li class="dropdown contain-user"> 
	<a href="/tai-khoan.html" class="dropdown-toggle" data-hover="" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
		<div class="user-avartar"><img class="img-responsive" src="images/fa-user.jpg"></div>
		<?=$_SESSION['member']['cus_email'];?>
		<span class="caret"></span>
	</a>
	<ul class="dropdown-menu">
		<li><a href="/login/logout"><?=$CMS->lang['logout']?></a></li>
	</ul> 
</li>