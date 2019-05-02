<div class="box box-forget panel-account box_area_login_v1">
  <div class="box-cont padd10">
    <p>
      <div class="row">
         <div class="box_area_login_v1">
           <h2 class="section_title" itemprop="name">Đăng nhập</h2>
          </div>
        <div class="col-sm-12">
          <div class="btn_login_social">
          <!--Login facebook-->
          <? if($tpl->fb_url) { ?>
          <a class="btn btn-face pull-left" style="width: 49%;" href="<?=$tpl->fb_url;?>" title="facebook login">Facebook</a>
          <? } ?>
          <!--End login facebook-->
          <!--Login facebook-->
          <? if($tpl->zalo_url) { ?>
          <a class="btn btn_zalo_2 pull-right" style="width: 49%;" href="<?=$tpl->zalo_url;?>" title="gplus login"><img style="width:23px" src="images/zalo-icon-1.png"> Zalo</a>
        
                          
          <? } ?>
          <!--End login facebook-->
        </div>
        </div>
      </div>
    </p>
    <p class="bottom-big">Nhập email và mật khẩu để đăng nhập.</p>
    <?= \views\layouts::google_recaptcha_header("send_login",0); ?>
    <form enctype="multipart/form-data" id="form-login" method="POST" action="/login/login_do">
      <div class="form-group row">
        <div  class="col-sm-12">
          <label for="">Email: <span class="clred">*</span></label>
          <div style="display: inline-block; position: relative;width: 100%;">
          <input class="form-control" type="text" name="cus_email" placeholder="Email" data-validation="[EMAIL]"  data-validation-message="Email is not valid!">
          </div>
        </div>
      </div>
      <div class="form-group row">
        <div  class="col-sm-12">
          <label for="">Mật khẩu: <span class="clred">*</span></label>
          <div style="display: inline-block; position: relative;width: 100%;">
          <input class="form-control" type="password" name="cus_password" placeholder="Password"  data-validation="[NOTEMPTY]" data-validation-message="Password must not be empty!" >
          </div>
        </div>
      </div>
      <div class="form-group"></div>
      <div class="form-group">
        <input type="submit" style="background-color: #e4e13f;border: 1px solid #e4e13f;color: #333;" class="bt btn-default txt-upper<?=\views\layouts::google_recaptcha_form("send_login");?>" value="Đăng nhập">
      </div>
    </form>
  </div>
  <div class="row">
    <div class="col-sm-12">
      <a class="pull-left" href="/register">Đăng ký tài khoản mới</a>
      <a class="pull-right" href="/login/forgot-password/"  >Quên mật khẩu</a>
    </div>
  </div>
</div>
<div style="margin-bottom:15px"></div>