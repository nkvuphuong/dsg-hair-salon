<?php   //\core\ezy::$act; ?>
<?php  
	global $CMS, $DB;
 	use views\layouts as layouts;
	$layouts = new layouts();
	//echo $layouts->google_recaptcha_form();
   
	 $package_id = $CMS->input['package_id'];  
?>  


<div class="container">
        <header class="title-section">
            <h3>Thanh toán</h3>
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
                
              <form name="reg-step-2" id="reg-step-2" action="/register/payment-ezybook" method="POST" > 

                  
    
                    <input type="hidden" name="package_id"  value="<?=  $CMS->input['package_id']; ?>">
                    <?php 
                    	if(isset($_SESSION['error_msg']) AND $_SESSION['error_msg'] != "")
                    	{
                    		echo "<p style='color:red;font-weight:bold'>".$_SESSION['error_msg']."</p>";
                    		unset($_SESSION['error_msg']);
                    	}

                    ?>
                    <div class="form-group">
                        <label for="industry" class="col-md-7">Gói dịch vụ</label>
                        <div class="col-md-3 ">
                        <?php
                            if($package_id == 1)
                            {
                                echo " <div class=\"col-md-5\" style='font-size: 14px;'>Basic</div>";
                            }elseif($package_id == 2)
                            {
                               echo " <div class=\"col-md-5\" style='font-size: 14px;'>Standard</div>";
                            }elseif($package_id == 3)
                            {
                               echo " <div class=\"col-md-5\" style='font-size: 14px;'>Professional</div>";
                            }elseif($package_id == 4)
                            {
                                echo " <div class=\"col-md-5\" style='font-size: 14px;'>Enterprise</div>";
                            }

                        ?>
                        </div>
                    </div>
                    <div class="form-group" style="clear: both">

                        <label for="industry"  class="col-md-7">Giá</label>
                          <div class="col-md-3  ">
                        <?php
                            if($package_id == 1)
                            {
                                 echo " <div class=\"col-md-5 \" style='font-size: 14px;'>1$/tháng</div>";
                                echo "<input type='hidden' name='price' id='package_price' value='1' ";
                            }elseif($package_id == 2)
                            {
                                 echo " <div class=\"col-md-5\" style='font-size: 14px;'>2$/tháng</div>";
                                echo "<input type='hidden' name='price' id='package_price' value='2' ";
                            }elseif($package_id == 3)
                            {
                                 echo " <div class=\"col-md-5 \" style='font-size: 14px;'>3$/tháng</div>";
                                echo "<input type='hidden' name='price' id='package_price' value='3' ";
                            }elseif($package_id == 4)
                            {
                                echo " <div class=\"col-md-5\" style='font-size: 14px;'>4$/tháng</div>";
                                echo "<input type='hidden' name='price' id='package_price' value='4' ";
                            }

                        ?>
                        </div>
                    </div> 
                    <div class="form-group" style="clear: both">
                        <label for="address" class="col-md-7">Tháng</label>
                         <div class="col-md-3">
                            <select  class="form-control" id="choice_cycle"  name="cycle" style="float: right">
                                    <option value="1"> 1 tháng</option> 
                                      <option value="6"> 6 tháng</option> 
                                        <option value="12"> 12 tháng</option> 
                              
                            </select>
                          </div>
                    </div>
                     

                    <div class="form-group" style="clear: both">
                        <label for="address" class="col-md-7">Tổng tiền</label>
                         <div class="col-md-5">
                             <div class="col-md-5" style='font-size: 14px;' id="total"></div> 
                         </div>
                    </div>
                    <div style="clear: both">

                    <div class="form-group" style="margin-bottom:0px;">
                            <fieldset class="form-control" style="max-width:unset;border:none;text-align:left;">
                                <div class="radio w25" style="width:100%;">
                                    <input type="radio" checked="" name="payment_method" id="radio-show-10" value="10">
                                    <label for="radio-show-10">Ví điện tử Ngân Lượng</label>
                                </div>
                            </fieldset>
                        </div>

                        <div class="form-group" style="margin-bottom:0px;">
                            <fieldset class="form-control" style="max-width:unset;border:none;text-align:left;">
                                <div class="radio w25" style="width:100%;">
                                    <input type="radio" name="payment_method" id="radio-show-11" value="11">
                                    <label for="radio-show-11">Thẻ ATM qua Ngân Lượng</label>
                                </div>
                                <div class="boxContent paymentMethod11">
                                    <p><i><span style="color:#ff5a00;font-weight:bold;text-decoration:underline;">Lưu ý</span>: Bạn cần đăng ký Internet-Banking hoặc dịch vụ thanh toán trực tuyến tại ngân hàng trước khi thực hiện.</i></p>
                                    <ul class="cardList clearfix">
                                        <li class="bank-online-methods">
                                            <label for="vcb_ck_on">
                                                <i class="BIDV" title="Ngân hàng TMCP Đầu tư &amp; Phát triển Việt Nam"></i>
                                                <input type="radio" value="BIDV"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods">
                                            <label for="vcb_ck_on">
                                                <i class="VCB" title="Ngân hàng TMCP Ngoại Thương Việt Nam"></i>
                                                <input type="radio" value="VCB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods">
                                            <label for="vnbc_ck_on">
                                                <i class="DAB" title="Ngân hàng Đông Á"></i>
                                                <input type="radio" value="DAB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods">
                                            <label for="tcb_ck_on">
                                                <i class="TCB" title="Ngân hàng Kỹ Thương"></i>
                                                <input type="radio" value="TCB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods">
                                            <label for="sml_atm_mb_ck_on">
                                                <i class="MB" title="Ngân hàng Quân Đội"></i>
                                                <input type="radio" value="MB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods">
                                            <label for="sml_atm_vib_ck_on">
                                                <i class="VIB" title="Ngân hàng Quốc tế"></i>
                                                <input type="radio" value="VIB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods">
                                            <label for="sml_atm_vtb_ck_on">
                                                <i class="ICB" title="Ngân hàng Công Thương Việt Nam"></i>
                                                <input type="radio" value="ICB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods">
                                            <label for="sml_atm_exb_ck_on">
                                                <i class="EXB" title="Ngân hàng Xuất Nhập Khẩu"></i>
                                                <input type="radio" value="EXB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods">
                                            <label for="sml_atm_acb_ck_on">
                                                <i class="ACB" title="Ngân hàng Á Châu"></i>
                                                <input type="radio" value="ACB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods ">
                                            <label for="sml_atm_hdb_ck_on">
                                                <i class="HDB" title="Ngân hàng Phát triển Nhà TPHCM"></i>
                                                <input type="radio" value="HDB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods ">
                                            <label for="sml_atm_msb_ck_on">
                                                <i class="MSB" title="Ngân hàng Hàng Hải"></i>
                                                <input type="radio" value="MSB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods ">
                                            <label for="sml_atm_nvb_ck_on">
                                                <i class="NVB" title="Ngân hàng Nam Việt"></i>
                                                <input type="radio" value="NVB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods ">
                                            <label for="sml_atm_vab_ck_on">
                                                <i class="VAB" title="Ngân hàng Việt Á"></i>
                                                <input type="radio" value="VAB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods ">
                                            <label for="sml_atm_vpb_ck_on">
                                                <i class="VPB" title="Ngân Hàng Việt Nam Thịnh Vượng"></i>
                                                <input type="radio" value="VPB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods ">
                                            <label for="sml_atm_scb_ck_on">
                                                <i class="SCB" title="Ngân hàng Sài Gòn Thương tín"></i>
                                                <input type="radio" value="SCB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods ">
                                            <label for="bnt_atm_pgb_ck_on">
                                                <i class="PGB" title="Ngân hàng Xăng dầu Petrolimex"></i>
                                                <input type="radio" value="PGB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods ">
                                            <label for="bnt_atm_gpb_ck_on">
                                                <i class="GPB" title="Ngân hàng TMCP Dầu khí Toàn Cầu"></i>
                                                <input type="radio" value="GPB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods ">
                                            <label for="bnt_atm_agb_ck_on">
                                                <i class="AGB" title="Ngân hàng Nông nghiệp &amp; Phát triển nông thôn"></i>
                                                <input type="radio" value="AGB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods ">
                                            <label for="bnt_atm_sgb_ck_on">
                                                <i class="SGB" title="Ngân hàng Sài Gòn Công Thương"></i>
                                                <input type="radio" value="SGB"  name="bankcode" >
                                            </label>
                                        </li>   
                                        <li class="bank-online-methods ">
                                            <label for="sml_atm_bab_ck_on">
                                                <i class="BAB" title="Ngân hàng Bắc Á"></i>
                                                <input type="radio" value="BAB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods ">
                                            <label for="sml_atm_bab_ck_on">
                                                <i class="TPB" title="Tền phong bank"></i>
                                                <input type="radio" value="TPB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods ">
                                            <label for="sml_atm_bab_ck_on">
                                                <i class="NAB" title="Ngân hàng Nam Á"></i>
                                                <input type="radio" value="NAB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods ">
                                            <label for="sml_atm_bab_ck_on">
                                                <i class="SHB" title="Ngân hàng TMCP Sài Gòn - Hà Nội (SHB)"></i>
                                                <input type="radio" value="SHB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods ">
                                            <label for="sml_atm_bab_ck_on">
                                                <i class="OJB" title="Ngân hàng TMCP Đại Dương (OceanBank)"></i>
                                                <input type="radio" value="OJB"  name="bankcode" >
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </fieldset>
                        </div>

                        <div class="form-group" style="margin-bottom:0px;">
                            <fieldset class="form-control" style="max-width:unset;border:none;text-align:left;">
                                <div class="radio w25" style="width:100%;">
                                    <input type="radio" name="payment_method" id="radio-show-12" value="12">
                                    <label for="radio-show-12">Internet Banking qua Ngân Lượng</label>
                                </div>
                                <div class="boxContent paymentMethod12">
                                    <p><i><span style="color:#ff5a00;font-weight:bold;text-decoration:underline;">Lưu ý</span>: Bạn cần đăng ký Internet-Banking hoặc dịch vụ thanh toán trực tuyến tại ngân hàng trước khi thực hiện.</i></p>
                                    <ul class="cardList clearfix">
                                        <li class="bank-online-methods ">
                                            <label for="vcb_ck_on">
                                                <i class="BIDV" title="Ngân hàng TMCP Đầu tư &amp; Phát triển Việt Nam"></i>
                                                <input type="radio" value="BIDV"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods ">
                                            <label for="vcb_ck_on">
                                                <i class="VCB" title="Ngân hàng TMCP Ngoại Thương Việt Nam"></i>
                                                <input type="radio" value="VCB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods ">
                                            <label for="vnbc_ck_on">
                                                <i class="DAB" title="Ngân hàng Đông Á"></i>
                                                <input type="radio" value="DAB"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods ">
                                            <label for="tcb_ck_on">
                                                <i class="TCB" title="Ngân hàng Kỹ Thương"></i>
                                                <input type="radio" value="TCB"  name="bankcode" >
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </fieldset>
                        </div>

                        <div class="form-group" style="margin-bottom:0px;">
                            <fieldset class="form-control" style="max-width:unset;border:none;text-align:left;">
                                <div class="radio w25" style="width:100%;">
                                    <input type="radio" name="payment_method" id="radio-show-13" value="13">
                                    <label for="radio-show-13">Thẻ Visa/MasterCard qua Ngân Lượng</label>
                                </div>
                                <div class="boxContent paymentMethod13">
                                    <p><i><span style="color:#ff5a00;font-weight:bold;text-decoration:underline;">Lưu ý</span>: Visa hoặc MasterCard.</i></p>
                                    <ul class="cardList clearfix">
                                        <li class="bank-online-methods ">
                                            <label for="vcb_ck_on">
                                                Visa: <input type="radio" value="VISA"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods ">
                                            <label for="vnbc_ck_on">
                                                Master: <input type="radio" value="MASTER"  name="bankcode" >
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </fieldset>
                        </div>

                        <div class="form-group" style="margin-bottom:0px;">
                            <fieldset class="form-control" style="max-width:unset;border:none;text-align:left;">
                                <div class="radio w25" style="width:100%;">
                                    <input type="radio" name="payment_method" id="radio-show-14" value="14">
                                    <label for="radio-show-14">Thẻ Visa/MasterCard trả trước qua Ngân Lượng</label>
                                </div>
                                <div class="boxContent paymentMethod14">
                                    <p><i><span style="color:#ff5a00;font-weight:bold;text-decoration:underline;">Lưu ý</span>: Visa hoặc MasterCard.</i></p>
                                    <ul class="cardList clearfix">
                                        <li class="bank-online-methods ">
                                            <label for="vcb_ck_on">
                                                Visa: <input type="radio" value="VISA"  name="bankcode" >
                                            </label>
                                        </li>
                                        <li class="bank-online-methods ">
                                            <label for="vnbc_ck_on">
                                                Master: <input type="radio" value="MASTER"  name="bankcode" >
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </fieldset>
                        </div>

                        <script language="javascript">
                            $('input[name="payment_method"]').bind('click', function() {
                                $('.boxContent').hide();
                                $('.paymentMethod'+$(this).val()).show();
                                $('input[name=bankcode]').prop('checked', false);
                                $($('.paymentMethod'+$(this).val()+' ul li').find('input[name=bankcode]')[0]).prop('checked', true);
                            });
                            $(document).ready(function(){
                                $(".paymentMethod11,.paymentMethod12,.paymentMethod13,.paymentMethod14").hide();
                            });
                        </script>
 


                    </div>
	                <div class="register-box-btn">
	                        <a href="/register/buy-ezybook/step-2/id-<?=  $CMS->input['package_id']; ?>" class="register-btn link pull-left  register-back-step3"    ><i class="fa fa-arrow-left"></i> Quay lại</a>
	                        <button type="submit" class="register-btn pull-right register-step3">Đăng ký <i class="fa fa-arrow-right"></i></a>
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
<script>
    $(document).ready(function(){
        var cycle = $(this).val();
        var price = $("#package_price").val();
        var total = cycle * price;
           $("#total").html(formatNumberInput(total)+" đ");
    });

 
</script>