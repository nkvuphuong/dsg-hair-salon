<?=\views\layouts::google_recaptcha_header("booking_form", 1);?>
<?if( $CMS->vars['booking_email_form_enable'] == 1 ){?>
<form enctype="multipart/form-data" method="post" action="/book/add_dsg" class="booking-form"  id="booking_form" >
    <!-- Thông tin khách hàng -->
    <h4 class="small-title">Thông tin liên hệ</h4>
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-sm-6 col-md-4">
                    <div class="form-group">
                        <input type="text" class="form-control" name="booking_name" maxlength="70" data-validation="[NOTEMPTY]" data-validation-message="Vui lòng nhập họ tên!" placeholder="Họ tên" value=""/>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="form-group">
                        <input name="booking_phone" type="text" class="form-control inputPhone" data-validation="[NOTEMPTY]" data-validation-message="Phone number invalid" placeholder="Số điện thoại"/>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="form-group">
                        <input type="text" class="form-control" name="booking_email" maxlength="70" value="" placeholder="Email"/>
                    </div>                                  
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-12">
                    <div class="form-group">
                        <textarea class="form-control" name="notelist" rows="2" maxlength="200"  placeholder="Ghi chú: Tối đa 200 ký tự"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thông tin booking -->
    <h4 class="small-title">Chọn salon</h4>
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                 <div class="col-sm-6 col-md-4">
                    <div class="form-group">
                            <select class="form-control input-with-right-icon" name="city_id" id="city_id">
                                <?if( !empty($tpl->optionCity_by_salon) ){?>
                                    <?foreach( $tpl->optionCity_by_salon as $data ){
                                            if($data['city_id'] == "4167")
                                            {
                                                $selected = "selected";
                                            }else{ $selected = ""; }
                                        ?>
                                    <option value="<?=$data['city_id'];?>" <?=$selected;?> ><?=$data['city_name'];?></option>
                                <?}  } ?>

                            </select>
                        <span class="fa fa-caret-down right-icon-input"></span>         
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="form-group">
                        <?if( $tpl->storeCnt > 1 ){?>
                        <select class="form-control input-with-right-icon choose_store" name="choose_store"  id="choose_store" data-validation="[NOTEMPTY]" data-validation-message="Vui lòng chọn salon!">
                            <option value="">Chọn Salon</option>
                            <?foreach ($tpl->dataStories as $storefront){
                                    if($storefront['store_id'] == $tpl->store_id_input ){ $selected = "selected" ; }
                                    else {  $selected = "" ;  }
                                ?>
                           <option value="<?=$storefront['store_id']?>" <?=$selected;?> ><?=$storefront['store_name']?></option>
                            <?}?>
                        </select>
                        <?}else{?>
                        <select class="form-control input-with-right-icon choose_store" name="choose_store">
                            <option value="<?=$tpl->dataStories[0]['store_id']?>" selected="selected"><?=$tpl->dataStories[0]['store_name'];?></option>
                        </select>
                        <?}?>
                        <span class="fa fa-caret-down right-icon-input"></span>         
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="form-group">
                        <input type="text" typehtml="html" name="booking_date" data-validation="[NOTEMPTY]" data-validation-message="Vui lòng chọn ngày!" class="form-control booking_date choose_date" id="datetimepicker_v1"  placeholder="Chọn ngày">
                        <span class="fa fa-calendar-o right-icon-input"></span>
                    </div>                                  
                </div>
            </div>
        </div>
    </div>
    <div class="time-booking-container">
        <h4  class="small-title text-yellow">Chọn giờ</h4>
        <div class="btn-time-wrap databooktime">
            <!-- input booking_hours hidden -->
            <div class="form-group">
                <input type="hidden" name='booking_hours' class="booking_hours" value="" data-validation="[NOTEMPTY]" data-validation-message="Vui lòng chọn giờ"/>
            </div>

            <!--List hours Morning-->
            <h5>Sáng: <span class="note_am_time" style="color: red;"></span></h5>
            <div class="clearfix timemorning">
            <?if( $CMS->vars['booking_open_hours'] == 0 ){ foreach( $tpl->hoursMorning as $hours ){?>
            <a class="btn btn-md btn-default btn-time choose_hours" valhours="<?=$hours;?>"><?=$hours;?> am</a>
            <?}}?>
            </div><!--End list hours Morning-->

            <!--List hours Afternoon-->
            <h5>Chiều: <span class="note_pm_time" style="color: red;"></span></h5>
            <div class="clearfix timeafternoon">
            <?if( $CMS->vars['booking_open_hours'] == 0 ){ foreach( $tpl->hoursAfternoon as $hours ){?>
            <a class="btn btn-md btn-default btn-time choose_hours" valhours="<?=$hours;?>"><?=$hours;?> am</a>
            <?}}?>
            </div><!--End list hours Afternoon-->
        </div>

        <h4  class="small-title text-yellow">Chọn dịch vụ</h4>
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="row">
                    <div class="item-booking">
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group">
                                <select class="form-control input-with-right-icon " id="hair_type" name="hair_type" data-validation="[NOTEMPTY]" data-validation-message="Vui lòng chọn loại tóc" >
                                    <option value="" price="" staff="[]" cosmetic="[]">Chọn dịch vụ</option>   
                                    <?php 
                                    foreach (  $tpl->dataServiceAndCategory as $key => $value) {
                                        # code...
                                        if($value['id'] != 1)
                                        {
                                    ?>
                                         <option value="<?=$value['id'];?>"><?=$value['name'];?></option>
                                   <? } } ?>

                                   
                                     
                                </select>
                                <span class="fa fa-caret-down right-icon-input"></span>     
                            </div>      
                        </div>
                         <div class="col-sm-6 col-md-3">
                            <div class="form-group">
                                 <select class="form-control input-with-right-icon list_hair_length" name="hair_length" id="hair_length" data-validation="[NOTEMPTY]" data-validation-message="Vui lòng loại tóc" >
                                    <option  value="">Chọn loại tóc</option>
                                    <option value="1">Tóc ít</option>
                                    <option value="2">Tóc nhiều</option>
                                </select>
                                <span class="fa fa-caret-down right-icon-input"></span>     
                            </div>      
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group">
                                <select class="form-control input-with-right-icon list_cosmetic" name="cosmetic" data-validation="[NOTEMPTY]" data-validation-message="Vui lòng chọn gói Combo" >
                                    <option  value="">Chọn gói Combo</option>
                                    <option value="1">Gói phổ thông</option>
                                    <option value="2">Gói cao cấp</option>
                                </select>
                                <span class="fa fa-caret-down right-icon-input"></span>     
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group">
                                <select class="form-control input-with-right-icon list_staff" name="staff_type"  data-validation="[NOTEMPTY]" data-validation-message="Vui lòng chọn nhân viên" >
                                    <option  seniority="0" value=''>Chọn nhân viên</option>
                                  <?
                                    if(count($tpl->listStaff) > 0)
                                    {

                                        foreach ($tpl->listStaff as $key => $value) {
                                    ?>
                                     
                                         <?
                                        if(count($value['user']) > 0)
                                        {
                                            foreach ($value['user'] as $k => $user) {
                                                if($value['seniority'] == 1){  }
                                                    else{   }
                                                
                                        ?>
                                            <option  seniority="<?=$value['seniority'];?>" value="<?=$user['user_id'];?>" style="<?=$color_op;?>" ><?=$user['user_display_name'];?></option>
                                           <? }} ?>
                                        
                                <? }} ?>
                                </select>
                                <span class="fa fa-caret-down right-icon-input"></span>     
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn-service-wrap">
                       <?foreach( $tpl->dataServiceAndCategory as $data ){      ?>
                         
                        <div id="sg_<?=$data['id'];?>" class="book_svgroup" style="display:none">   
                            <!--List service-->
                            <?foreach( $data['services'] as $key => $item ){ if($key == 0){ $active_op = ""; $first_product = $item['id']; $first_product_price = $item['price_sell_og']; }else {  $active_op = ""; }  ?>
                             <a  service_id="<?=$item['id'];?>" other="<?=$item['other_p'];?>"  class="btn btn-md btn-default btn-service list_service_dsg <?=$active_op;?>" price="<?=$item['price_sell_og'];?>"><?=$item['name'];?>: <?=$item['price_sell'];?></a>
                              <input type="hidden" id="product_hidden_<?=$item['id'];?>" name="product_id[]" value ="" />

                            <?}?>
                        </div>
                        <?}?>
                         
                </div>
                <div id="show_svc_choice">
                        
                </div>   
                <div class="row">  
                    <div class="col-sm-6 col-md-9">  
                        <h4>Tổng tiền: <span id="output_total"></span></h4>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="form-group">
                            <button class="btn btn-md btn-primary btn-block search-btn-01 btn_send_appointment <?=\views\layouts::google_recaptcha_form("booking_form");?>" type="button">đặt lịch ngay</a>
                        </div>
                    </div>
                </div>
            </div>
 
        </div>
            <section class="promotion_information">
        <!-- tpl main -->
        <?=\core\ezy::tpl('promotion_information', 'book');?>
        </section>
    </div>

    <!-- Input hidden -->
    <input type="hidden" name="nocaptcha" value="1"/>
    <input type="hidden" name='booking_area_code' value="84" />
    <input type="hidden" name='booking_form_email' value="1" />

    <!-- Use for scroll when error -->
    <input type="hidden" name="scroll_jumpto" class="scroll_jumpto" data-jumpto="#booking_form">
</form>


<?}else{?>
<p style="color: red;" class="text-center">Đặt lịch tạm đóng trong thời gian này...</p>
<?}?>
 <?=\lib\assets::generate(array(
        "custom/js/book.js",
    ), "js");?>
    
 