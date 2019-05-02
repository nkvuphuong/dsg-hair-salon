<?php
global $CMS;
// print_r (  $CMS->input); exit;
// print_r (\core\ezy::$input['id']);exit;
//\core\ezy::$act;
//
$package_id = isset( $_SESSION['reg_info']['id'] ) ?  $_SESSION['reg_info']['id']  : \core\ezy::$input['id'];
?>


<div class="container">
    <header class="title-section">
        <h3>Tạo tài salonản Ezybook của bạn</h3>
        <div class="sub"> </div>
    </header>
    <div class="row">
        <div class="register_box">
            <div class="register-content" id="register_step2" style="display: block;">
                <div class="row box-scroll">
                    <div class="col-md-1"></div>
                    <div class="col-md-5 text-center">
                        <div class="register_title">
                            <h3>Để quản lý bán hàng mọi lúc, mọi nơi, trên mọi thiết bị.</h3>
                            <p>Không cần cài đặt, quản lý dễ dàng, tiết kiệm chi phí.</p>
                            <img src="images/register_step2.jpg">
                            <p class="color-red">* Hỗ trợ đăng ký 1800 6162</p>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="col-md-5">
                    <?php

                        if(\core\ezy::$act == "buy-ezybook" )
                        {
                            
                                echo  '<form name="reg-step-2" id="reg-step-2" action="/register/buy-ezybook/step-11/id-'.$package_id.'" method="POST" >   ';
                        }
                        else
                        {
                             echo  '<form name="reg-step-2" id="reg-step-2" action="/register/trial-ezybook/step-11/id-'.$package_id.'" method="POST" >   ';
                        }

                     ?>   
                            
                          
                            <div class="form-group">
                                <label for="fullname">Họ tên  <span class="color-red">*</span></label>
                                <input type="text" class="form-control" id="fullname" name="fullname" placeholder="" value="<?=  $_SESSION['reg_info']['fullname'] ?>">
                            </div>
                            <div class="form-group">
                                <label for="phone">Điện thoại <span class="color-red">*</span></label>
                                <input type="text" class="form-control" id="phone" name="phone"  value="<?=  $_SESSION['reg_info']['phone'] ?>" placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="storename">Tên cửa hàng <span class="color-red">*</span></label>
                                <input type="text" class="form-control" id="storename" name="storename"  placeholder="" value="<?=  $_SESSION['reg_info']['storename'] ?>">
                            </div>
                            <div class="form-group">
                                <label for="storename">Địa chỉ gian hàng Ezybook của bạn <span class="color-red">*</span></label>
                                <input type="text" class="form-control" id="code" name="code"  value="<?=  $_SESSION['reg_info']['code'] ?>"   placeholder="">
                                <span class="note">https://<span class="storetxt">địa chỉ gian hàng</span>.ezybook.com</span>
                            </div>
                            <div class="form-group">
                                <label for="name">Tên đăng nhập <span class="color-red">*</span></label>
                                <input type="text" class="form-control" id="name" name="name"  value="<?=  $_SESSION['reg_info']['name'] ?>" placeholder="">
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-xs-12 col-md-6">
                                        <label for="pass">Mật khẩu <span class="color-red">*</span></label>
                                        <input type="password" class="form-control" id="pass"  name="pass"  placeholder="">
                                    </div>
                                    <div class="col-xs-12 col-md-6">
                                        <label for="passagain">Xác nhận mật khẩu <span class="color-red">*</span></label>
                                        <input type="password" class="form-control" id="passagain"  name="passagain"  placeholder="">
                                        <input type="hidden" name="package_id" value="<?=  \core\ezy::$input['id'] ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="register-box-btn">
                                <a href="/register/phan-mem-ke-toan-doanh-nghiep" class="register-btn link pull-left  register-back-step1"  ><i class="fa fa-arrow-left"></i> Quay lại</a>
                                <button type="submit" class="register-btn pull-right register-step2" >Tiếp theo <i class="fa fa-arrow-right"></i></button>

                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>


<script src="js/custom.js"></script>

<br />
<br />
<br />
<br />