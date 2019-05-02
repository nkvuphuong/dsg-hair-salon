 
<!--box--> 
<div class="box_module bottom">
  <div class="container">
 
    <div class="row">

      <div class="col-sm-4 mlr-auto">
        <div class="box panel-account">
            <div class="box_area_login_v1">
           <h2 class="section_title" itemprop="name">Quên mật khẩu</h2>
          </div>
          <div class="box-cont padd10">
            <p class="bottom-big">Nhập email để nhận lại thông tin đăng nhập.</p>
            <?= \views\layouts::google_recaptcha_header("send_forgot_header",0); ?>
            <form enctype="multipart/form-data" method="POST" id="form-forgot-pwd" action="/login/forgot-password-do/">
              <div class="form-group row">
                <div  class="col-sm-12">
                  <label for="">Email: <span class="clred">*</span></label>
                  <div style="display: inline-block; position: relative;width: 100%;">
                  <input class="form-control" type="email" name="email" placeholder="Email" data-validation="[EMAIL]" data-validation-message="Email không đúng định dạng!">
                  </div>
                </div>
              </div>
              <div class="form-group"></div>
              <div class="form-group">
                <input type="submit" style="background-color: #e4e13f;border: 1px solid #e4e13f;color: #333;"  class="bt btn-default txt-upper<?=\views\layouts::google_recaptcha_form("send_forgot_header");?>" value="Gửi">
              </div>
            </form>
          </div>
          <div class="">
            Bạn chưa có tài salonản? <a href="/login">Đăng ký ngay</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>  
 <!--End box-->
  <div style="margin-bottom:10px"></div>