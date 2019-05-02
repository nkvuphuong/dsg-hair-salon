<?php \core\ezy::$act; ?>
<?php  
	global $CMS, $DB;
 	use views\layouts as layouts;
	$layouts = new layouts();
	//echo $layouts->google_recaptcha_form();
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
                        <h3>Bạn có thể quản lý cửa hàng và bán hàng ở mọi nơi!</h3>
                        <p>Chúng tôi đã tích hợp Ezybook trên mọi thiết bị.</p>
                        <img src="images/register_step3.png">
                    </div>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-5">
                
                <?php
                    $package_id =  $_SESSION['reg_info']['package_id'];
                    if(\core\ezy::$act == "buy-ezybook" )
                    {
                        
                            echo  '<form name="reg-step-2" id="reg-step-2" action="/register/buy-ezybook/step-21/id-'.$package_id.'" method="POST" >   ';
                    }
                    else
                    {
                         echo  '<form name="reg-step-2" id="reg-step-2" action="/register/trial-ezybook/step-21/id-'.$package_id.'" method="POST" >   ';
                    }

                ?>

                  
    
                    <input type="hidden" name="package_id"  value="<?=  $package_id; ?>">
                    <?php 
                    	if(isset($_SESSION['error_msg']) AND $_SESSION['error_msg'] != "")
                    	{
                    		echo "<p style='color:red;font-weight:bold'>".$_SESSION['error_msg']."</p>";
                    		unset($_SESSION['error_msg']);
                    	}

                    ?>
                    <div class="form-group">
                        <label for="industry">Ngành nghề kinh doanh</label>
                        <select class="form-control" id="industry" name="industry">    
                        		<option value="1">Thời trang</option>
                        		<option value="2">Mẹ & Bé</option> 
                        		<option value="3">Bar - Cafe - Nhà hàng</option> 
                        		<option value="4">Mỹ phẩm</option> 
                        		<option value="5">Tạp hóa</option> 
                        		<option value="6">Siêu thị mini</option> 
                        		<option value="7">Điện thoại & Điện máy</option> 
                        		<option value="8">Nông sản & Thực phẩm</option> 
                        		<option value="9">Nội thất & Gia dụng</option> 
                        		<option value="10">Vật liệu xây dựng</option> 
                        		<option value="11">Xe Máy & Linh Kiện</option> 
                        		<option value="12">Nhà thuốc</option> 
                        		<option value="13">Hoa & Quà tặng</option> 
                        		<option value="14">Sách & Văn phòng phẩm</option> 
                        		<option value="15">Ngành hàng khác</option> 

                        </select>
                    </div>
                    <div class="form-group">
                        <label for="location">Tỉnh thành <span class="color-red">*</span></label>
                        <select class="form-control" id="location" name="location">
                            <option value="" selected="selected">Tỉnh Thành</option>
                                                                    <option value="Hà Nội">Hà Nội</option>
                                                                    <option value="Tp Hồ Chí Minh">Tp Hồ Chí Minh</option>
                                                                    <option value="An Giang">An Giang</option>
                                                                    <option value="Bà Rịa - Vũng Tàu">Bà Rịa - Vũng Tàu</option>
                                                                    <option value="Bình Dương">Bình Dương</option>
                                                                    <option value="Bình Phước">Bình Phước</option>
                                                                    <option value="Bình Thuận">Bình Thuận</option>
                                                                    <option value="Bình Định">Bình Định</option>
                                                                    <option value="Bạc Liêu">Bạc Liêu</option>
                                                                    <option value="Bắc Giang">Bắc Giang</option>
                                                                    <option value="Bắc Kạn">Bắc Kạn</option>
                                                                    <option value="Bắc Ninh">Bắc Ninh</option>
                                                                    <option value="Bến Tre">Bến Tre</option>
                                                                    <option value="Cao Bằng">Cao Bằng</option>
                                                                    <option value="Cà Mau">Cà Mau</option>
                                                                    <option value="Cần Thơ">Cần Thơ</option>
                                                                    <option value="Gia Lai">Gia Lai</option>
                                                                    <option value="Hà Giang">Hà Giang</option>
                                                                    <option value="Đồng Tháp">Đồng Tháp</option>
                                                                    <option value="Đồng Nai">Đồng Nai</option>
                                                                    <option value="Điện Biên">Điện Biên</option>
                                                                    <option value="Đắk Nông">Đắk Nông</option>
                                                                    <option value="Đà Nẵng">Đà Nẵng</option>
                                                                    <option value="Đaklak">Đaklak</option>
                                                                    <option value="Hà Nam">Hà Nam</option>
                                                                    <option value="Hà Tĩnh">Hà Tĩnh</option>
                                                                    <option value="Hải Dương">Hải Dương</option>
                                                                    <option value="Tp.Hải Phòng">Tp.Hải Phòng</option>
                                                                    <option value="Hậu Giang">Hậu Giang</option>
                                                                    <option value="Hoà Bình">Hoà Bình</option>
                                                                    <option value="Hưng Yên">Hưng Yên</option>
                                                                    <option value="Khánh Hoà">Khánh Hoà</option>
                                                                    <option value="Kiên Giang">Kiên Giang</option>
                                                                    <option value="Kon Tum">Kon Tum</option>
                                                                    <option value="Lai Châu">Lai Châu</option>
                                                                    <option value="Lạng Sơn">Lạng Sơn</option>
                                                                    <option value="Lào Cai">Lào Cai</option>
                                                                    <option value="Lâm Đồng">Lâm Đồng</option>
                                                                    <option value="Long An">Long An</option>
                                                                    <option value="Nam Định">Nam Định</option>
                                                                    <option value="Nghệ An">Nghệ An</option>
                                                                    <option value="Ninh Bình">Ninh Bình</option>
                                                                    <option value="Ninh Thuận">Ninh Thuận</option>
                                                                    <option value="Phú Thọ">Phú Thọ</option>
                                                                    <option value="Phú Yên">Phú Yên</option>
                                                                    <option value="Quảng Bình">Quảng Bình</option>
                                                                    <option value="Quảng Nam">Quảng Nam</option>
                                                                    <option value="Quảng Ngãi">Quảng Ngãi</option>
                                                                    <option value="Quảng Ninh">Quảng Ninh</option>
                                                                    <option value="Quảng Trị">Quảng Trị</option>
                                                                    <option value="Sóc Trăng">Sóc Trăng</option>
                                                                    <option value="Sơn La">Sơn La</option>
                                                                    <option value="Tây Ninh">Tây Ninh</option>
                                                                    <option value="Thanh Hoá">Thanh Hoá</option>
                                                                    <option value="Thái Bình">Thái Bình</option>
                                                                    <option value="Thái Nguyên">Thái Nguyên</option>
                                                                    <option value="Thừa Thiên Huế">Thừa Thiên Huế</option>
                                                                    <option value="Tiền Giang">Tiền Giang</option>
                                                                    <option value="Trà Vinh">Trà Vinh</option>
                                                                    <option value="Tuyên Quang">Tuyên Quang</option>
                                                                    <option value="Vĩnh Long">Vĩnh Long</option>
                                                                    <option value="Vĩnh Phúc">Vĩnh Phúc</option>
                                                                    <option value="Yên Bái">Yên Bái</option>
                                                    </select>
                    </div>
                    <div class="form-group">
                        <label for="address">Địa chỉ</label>
                        <input type="text" class="form-control" id="address"  name="address" placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" id="email" name="email" placeholder="">
                    </div>
                    
                    <!-- 
                    <div class="form-group capchar">
                        <label for="code">Mã xác thực <span class="color-red">*</span></label>
                        <img id="captcha-image" src="https://www.Ezybook.vn/wp-content/themes/Ezybook/recaptcha/get_captcha.php">
                        <a href="javascript:void(0)" id="reload-captcha"><i class="fa fa-refresh"></i></a>

                        <div class="g-recaptcha"  >

                        <input type="text" class="form-control" id="captcha" pattern="\d" placeholder="">
                    </div>
                    --> 
	                    <div class="register-box-btn">
	                        <a href="/register/buy-ezybook/step-1/id-<?=  $CMS->input['package_id']; ?>" class="register-btn link pull-left  register-back-step3"    ><i class="fa fa-arrow-left"></i> Quay lại</a>
                            <button type="submit" class="register-btn pull-right register-step3">Đăng ký <i class="fa fa-arrow-right"></i></button></a>
	                    </div>
               	 </div>

                </form>
            </div>




	          </div><!--end -->
  
        </div>

        </div>
</div>        

 
<script src="js/custom.js"></script>
<br />
<br />
<br />
<br />