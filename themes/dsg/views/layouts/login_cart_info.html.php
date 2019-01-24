 <li>
  	
  	<div class="account-user item-dropdown Foatright item-inline dropdown"> <a class="dropdown-toggle pointer" id="drop61" data-toggle="dropdown"><i class="fa fa-user-o" style="color:#000000"></i> </a>
    <ul class="dropdown-menu menu-list" aria-labelledby="drop61">
      <!-- if login ready -->
      <?if( isset($_SESSION['member']) AND $CMS->vars['is_login'] == 1 ){?>
      <li><a><span class="demo-stroke pe-7s-user"></span><span><?=$_SESSION['member']['cus_full_name'];?></span></a></li>
      <li><a href="/login/change-password"><span><i class="fa fa-lock" aria-hidden="true"></i></span> Thay đổi mật khẩu </a></li>
      <li><a href="<?=$tpl->login['link']?>"><span class=""><i class="fa fa-sign-out" aria-hidden="true"></i></span><?=$tpl->login['text']?></a></li>
      
      <?}else{?>
      <!-- if not login -->
      <li><a href="/register"><span class="icon-faa"><i class="fa fa-user-plus" aria-hidden="true"></i></span> Đăng ký</a></li>
      <li><a href="<?=$tpl->login['link'];?>"><span class="icon-fa"><i class="fa fa-sign-in" aria-hidden="true"></i></span></span><?=$tpl->login['text']?></a></li>
      <li><a href="/login/forgot-password"><span class="icon-fa"><i class="fa fa-lock" aria-hidden="true"></i></span> Quên mật khẩu</a></li>
      <?}?>
    </ul>
  </div>
 </li>
 <li><a href="/cart"><i class="fa fa-shopping-cart"></i><span class="cart-number-pop"><?=$tpl->countmycart;?></span></a></li>