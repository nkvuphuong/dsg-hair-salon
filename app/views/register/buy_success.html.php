<?php   \core\ezy::$act; ?>
<?php  
	global $CMS, $DB;
 	use views\layouts as layouts;
	$layouts = new layouts();
	//echo $layouts->google_recaptcha_form();
 	//print_r (  $CMS->vars['google_recaptcha_sitekey']);   
	
?>


<div class="container">
       <div class="content-register">
                <h2 class="register_head">Chúc mừng bạn đã đăng ký thành công !</h2>
                <div id="register_step4" style="display: block;padding: 0;">
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <div class="register_title">
                                <h3><span>Bạn chỉ cần ghi nhớ tài khoản và địa chỉ </span>truy cập Ezybook là có thể bắt đầu kinh doanh.</h3>
                                <p>Ezybook không phải là phần mềm cài đặt.</p>
                                <img src="images/register_step4.png">
                            </div>
                        </div>
                        <div class="col-md-6 register_success">
                            <div class="form-group">
                                <label>Tên cửa hàng:</label>
                                <h5 id="store-name">
                                <?php
                                    echo $_SESSION['reg_info_success']['storename'];
                                ?>

                                </h5>
                            </div>
                            <div class="form-group">
                                <label>Địa chỉ truy cập gian hàng của bạn là:</label>
                                <h5 id="store-url"><a href="http://<?php  echo $_SESSION['reg_info_success']['code_storename']; ?>.ezybook.vn">http://<?php  echo $_SESSION['reg_info_success']['code_storename']; ?>.ezybook.vn</a></h5>
                            </div>
                            <div class="form-group">
                                <label>Tên đăng nhập:</label>
                                <h5 id="store-user">
                                <?php
                                    echo $_SESSION['reg_info_success']['username'];
                                ?>
                                </h5>
                            </div>
                            <button class="register" onclick="window.location.href = 'http://<?php  echo $_SESSION['reg_info_success']['code_storename']; ?>.ezybook.vn'" data-url="http://<?php  echo $_SESSION['reg_info_success']['code_storename']; ?>.ezybook.vn">Bắt đầu kinh doanh  <i class="fa fa-arrow-right"></i></button>
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