 

<!--box--> 
<div class="box_module bottom">
  <div class="container">

 
    <div class="row">
      <div class="col-sm-3">
        <div class="box mb-15">
          <div class="title_style">
            <h3>Quản lý tài salonản</h3>
          </div>
          <nav class="bs-docs-sidebar border">
            <ul class="nav bs-docs-sidenav">
              <li class="border-bottom active"><a href="/login/change-password/"><span class="demo-icon icon-lock-open-alt"></span> Thay đổi mật khẩu</a></li>
              <li class="border-bottom"><a href="/login/logout"><span class="demo-icon icon-logout-1"></span> Thoát</a></li>
            </ul>
          </nav>
        </div>
      </div>
      <div class="col-sm-9">
        <div class="box bg_gray2 padding">
          <div class="title_style3 text-center">
            <h3 ><?if($_SESSION['member']['cus_password'] != ""){?>Thay đổi mật khẩu<?}else{?>Tạo mật khẩu<?}?></h3>
          </div>
          <div class="box-cont">
            <div class="row">
              <div class="col-sm-3">&nbsp;</div>
              <div class="col-sm-5 brow_bg" >
                <?= \views\layouts::google_recaptcha_header("send_changepwd",0); ?>
                <form enctype="multipart/form-data" method="POST" id="form-changepwd" action="/login/changepassword-do/">
                  <?if( $_SESSION['member']['cus_password'] != "" ){?>
                  <div class="form-group" style="margin-top:5px">
                    <label for="">Mật khẩu hiện tại <span class="clred">*</span></label>
                    <div style="display: inline-block; position: relative;width: 100%;">
                    <input class="form-control" type="password" name="old_password" placeholder="Old password"   data-validation="[NOTEMPTY]" data-validation-message="Old password not empty!">
                    </div>
                  </div>
                  <?}?>
                  <div class="form-group">
                    <label for="">Mật khẩu mới <span class="clred">*</span></label>
                    <div style="display: inline-block; position: relative;width: 100%;">
                    <input class="form-control" type="password" name="password" placeholder="New Password" data-validation="[NOTEMPTY, L>=6]" data-validation-message="Must be at least 6 characters and not be empty!" >
                    </div>
                  </div>
                  <div class="form-group">
                    <div style="display: inline-block; position: relative;width: 100%;">
                    <label for="">Nhập lại mật khẩu mới <span class="clred">*</span></label>
                    <input class="form-control" type="password" name="repassword" placeholder="New Password Confirm" data-validation="[V==cus_password]" data-validation-message="Password does not match!">
                    </div>
                  </div>
                  <div class="form-group"></div>
                  <div class="form-group">
                    <input type="submit" class="bt btn-default txt-upper <?=\views\layouts::google_recaptcha_form("send_changepwd");?>" id="" value="Gửi">
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>  
<!--End box-->
<div style="margin-bottom:15px"></div>