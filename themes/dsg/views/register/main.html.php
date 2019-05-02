<section class="box_module bottom">
    <div class="container">
        <div class="row">
            <div class="row section-title-wrapper">
                <div class="col-sm-5 mlr-auto">
                    <div class="box box-forget panel-account box_area_login_v1">
                    <h2 class="section_title" itemprop="name"><?=$CMS->lang['breadcrumb_register'];?></h2>
                    <div class="row"><div class="col-sm-12">
                    <div class="btn_login_social">
                    <? if($tpl->fb_url) { ?>
                        <a class="btn btn_facebook_v1" style="width: 49%;" href="<?=$tpl->fb_url;?>" title="facebook login">
                            <i class="fa fa-facebook"></i>
                            <span>Facebook</span>
                        </a>
                    <? } ?>
                    <? if($tpl->zalo_url) { ?>
                        <a class="btn btn_zalo" style="width: 49%;" href="<?=$tpl->zalo_url;?>" title="zalo login">
                            <i></i>
                            <span><img style="width:23px" src="images/zalo-icon-1.png"> Zalo</span>
                        </a>
                    <? } ?>
                    </div>    </div>    </div>  
                   
                    <div class="submit_name_psw">
                        <form enctype="multipart/form-data" method="POST" id="form-register" action="/register/submit/">
                            <fieldset class="form-group">
                                <div class="col_name_v1">
                                   <div class="form-control-wrapper"> 
                                    <input type="text" name="cus_full_name" placeholder="Họ và tên"   data-validation="[NOTEMPTY]" value="<?php echo isset($_SESSION['data_input']['cus_full_name']) ? $_SESSION['data_input']['cus_full_name'] : ""; ?>" data-validation-message="Vui lòng nhập họ tên!"   >
                                   </div> 
                                </div>
                                <div class="col_name_v1">
                                     <div class="form-control-wrapper"> 
                                        <input type="text" name="cus_address" placeholder="Địa chỉ liên hệ" data-validation="[NOTEMPTY]" value="<?php echo isset($_SESSION['data_input']['cus_address']) ? $_SESSION['data_input']['cus_address'] : ''; ?>" data-validation-message="Vui lòng nhập địa chỉ liên hệ!" >
                                     </div>    
                                </div>
                                <div class="col_name_v1">
                                     <div class="form-control-wrapper"> 
                                        <input type="text" class="inputPhone" name="cus_phone" placeholder="Số điện thoại" data-validation="[NOTEMPTY]" value="<?php echo isset($_SESSION['data_input']['cus_phone']) ? $_SESSION['data_input']['cus_phone'] : ''; ?>" data-validation-message="Số điện thoại không đúng định dạng!" >
                                     </div>     
                                </div>
                                <div class="col_name_v1">
                                     <div class="form-control-wrapper"> 
                                         <input type="email" name="cus_email" placeholder="Email" data-validation="[EMAIL]" value="<?php echo isset($_SESSION['data_input']['cus_email']) ? $_SESSION['data_input']['cus_email'] : ''; ?>" data-validation-message="Email không đúng định dạng!">
                                      </div>       
                                </div>
                                <div class="col_psw_v1">
                                     <div class="form-control-wrapper"> 
                                        <input type="password" name="cus_password" placeholder="Mật khẩu" data-validation="[NOTEMPTY, L>=6]" data-validation-message="Mật khẩu ít nhất 6 ký tự" >
                                     </div>       
                                </div>
                                <div class="col_psw_v1">
                                     <div class="form-control-wrapper"> 
                                          <input type="password" name="cus_repassword" placeholder="Nhập lại mật khẩu" data-validation="[V==cus_password]" data-validation-message="Mật khẩu không trùng khớp!">
                                       </div>       
                                </div>
                                <div class="btn_submit_login">
                                    <button style="background-color: #e4e13f;border: 1px solid #e4e13f;color: #333;"  class="bt btn-default txt-upper" type="submit"><?=$CMS->lang['register'];?></button>
                                </div>
                             </fieldset>   
                        </form>
                    </div>
                  
                    <div class="text_login_now">
                        <p class="txt_login">
                         Bạn đã có tài salonản? 
                            <a href="/login"><?=$CMS->lang['login'];?></a>
                        </p>
                    </div>
                </div>
                 </div>
            </div>
        </div>
    </div>
</section>
<div style="margin-bottom:15px"></div>
<script>
$(document).ready(function(){
    $("#form-register").validate({
        submit: {
            settings: {
                button: "[type='submit']",
                inputContainer: '.form-group',
                errorListClass: 'form-tooltip-error',
            }
        }
    });
});
</script>        