<form id="form-signin_v1" name="form-signin_v1" action="<?=$tpl->action_form;?>" method="POST" enctype="multipart/form-data">

<section class="add_form main_form">
  <figure class="heading">
    <h3><?=$CMS->lang['tra_'.$CMS->input['act']];?></h3>
      <a href="/acp/?site=<?=$CMS->input['site'];?>" title=""><span class="font-icon font-icon-del"></span></a>
  </figure>
<?=$CMS->global->languageTab('langTab');?>
 <figure class="box-typical box-typical box-typical-padding border">
  <div class="row">
      <div class="col-md-6">
        <div class="row">
        <? if($CMS->vars['translations']) { ?>
            <? foreach ($CMS->vars['translations'] as $langCode => $langName) { ?>
                <div class="col-xl-6">
                  <fieldset class="form-group langTab" lang="<?=$langCode;?>">
                  <label class="form-label" ><?=$CMS->lang['tra_name'];?> <span style="color:red">(*)</span></label>
                   <div class="form-control-wrapper">
                    <input class="form-control " type="text" name="p_name[<?=$langCode;?>]" id="p_name[<?=$langCode;?>]" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['p_name_err'];?>" value="<?=$tpl->p_name[$langCode];?>">
                  </div>
                  </fieldset>
              </div>
        <? } }else {?>

                <div class="col-xl-6">
                  <fieldset class="form-group">
                  <label class="form-label" ><?=$CMS->lang['tra_name'];?> <span style="color:red">(*)</span></label>
                  <div class="form-control-wrapper">
                    <input class="form-control " type="text" name="p_name" id="p_name" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['p_name_err'];?>" value="<?=$tpl->p_name;?>">
                  </div>
                  </fieldset>
                </div>
        <? } ?>
          <div class="col-xl-6">
             <fieldset class="form-group">
              <label class="form-label" ><?=$CMS->lang['tra_barcode'];?></label>
              <input class="form-control " type="text" name="p_barcode" id="p_barcode" value="<?=$tpl->p_barcode;?>">
            </fieldset>
          </div>
          <div class="col-xl-6">
             <fieldset class="form-group">
              <label class="form-label" ><?=$CMS->lang['p_img_alt'];?></label>
                  <input class="form-control" type="text" name="p_img_alt" id="p_img_alt" value="<?=$tpl->p_img_alt;?>">
            </fieldset>
        </div>
      </div>
      <div class="row">
        <div class="col-xl-6">
            <fieldset class="form-group">
              <label class="form-label pull-left" ><?=$CMS->lang['p_avartar'];?></label>
                <div class="actionButtons pull-right">
                    <ul>
                        <li onclick="return performClick('ufile');">
                            <i tabindex="0" class="fa fa-pencil" ></i>
                        </li>
                        <li>
                            <span class="text-left">|</span>
                        </li>
                        <li onclick="return delete_fileToAttach();">
                            <i class="fa fa-trash-o"></i>
                        </li>
                    </ul>
                    <input  type="hidden" id="ufile_output_b64" name="base64_image" value="<?=$tpl->base64_image;?>" />
                </div>
               
                <div class="drop-zone fileinput-button" style="height: 110px !important">
                  <img id="upload_img_show"  width="205" src="<?=$tpl->src_image_upload;?>" <?=$tpl->style_display;?>  />
                      <i class="font-icon font-icon-cloud-upload-2"></i>
                      <div class="drop-zone-caption">Drag file to upload</div>
                      <input type="file"  name="p_image" id="ufile" accept="image/*">
                  </div><!--.drop-zone-->
                <img class="img-responsive" src="<?=$tpl->pg_avartar;?>" style="max-width: 100%;" alt="<?=$tpl->product_group_name;?>"> 
          </fieldset>
        </div>
      
        
        <div class="col-xl-6">
            <input type="hidden" name="p_type" value="<?=$tpl->p_type;?>" />
            <fieldset class="form-group">
            <label class="form-label pull-left" ><?=$CMS->lang['tra_product_group'];?> <span style="color:red">(*)</span></label>
        <? if ($CMS->permit["product_group_add"] == 1) { ?>
              <a   data-size="s" class="add_new_product_group pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline">
                <i class="fa fa-plus" aria-hidden="true"></i> 
              </a>
        <? } ?>

        <? if ($CMS->permit["product_group_edit"] == 1) { ?>
            <span class="box_product_group pull-right" style="margin-left:10px"></span>
        <? } ?>
            <div class="form-control-wrapper">
              <select class="form-control select2 select_product_group" name="p_product_group" defaultvalue="<?=$tpl->p_product_group;?>" id="p_product_group" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['tra_product_group_err'];?>"  >
                <?=$tpl->option_p_product_group;?>
              </select>
            </div>
          </fieldset>

          <fieldset class="form-group">
            <label class="form-label pull-left" ><?=$CMS->lang['tra_option'];?></label>
            <div class="form-control-wrapper">
                <div class='checkbox'>
                    <input type='checkbox' name='p_product_option[]' value='1' id='check-1'  >
                    <label for='check-1'><?=$CMS->lang['tra_option_1'];?></label>
                </div>
                <div class='checkbox'>
                    <input type='checkbox' name='p_product_option[]' value='2' id='check-2'  >
                    <label for='check-2'><?=$CMS->lang['tra_option_2'];?></label>
                </div>
                <div class='checkbox'>
                    <input type='checkbox' name='p_product_option[]' value='5' id='check-5'  >
                    <label for='check-5'><?=$CMS->lang['tra_option_5'];?></label>
                </div>
            </div>
          </fieldset>

          <div class="form-control-wrapper">
            <label class="form-label pull-left" ><?=$CMS->lang['tra_option_type'];?></label>
            <select class="form-control auto_select" name="product_tra_type" defaultvalue="<?=$tpl->product_tra_type;?>" id="product_tra_type">
              <option value=""><?=$CMS->lang['real_choose_type'];?></option>
              <option value="1"><?=$CMS->lang['tra_type_1'];?></option>
              <option value="2"><?=$CMS->lang['tra_type_2'];?></option>
            </select>
          </div>
        </div>


      </div>

      <div class="row">
          <div class="col-xl-12"><h5 class="m-t-lg with-border"><?=$CMS->lang['title_info_transaction'];?></h5></div>
          
          <div class="col-xl-6">
            <fieldset class="form-group">
              <label class="form-label" ><?=$CMS->lang['tra_price_old'];?></label>
              <input class="form-control " type="text" name="p_price_old" id="p_price_old" value="<?=$tpl->p_price_old;?>"  onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
            </fieldset>     
          </div>

          <div class="col-xl-6">
            <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['tra_price_sell'];?></label>
              <input class="form-control " type="text" name="p_price_sell" id="p_price_sell" value="<?=$tpl->p_price_sell;?>" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
            </fieldset> 
          </div>
          
      </div>

        <div class="row">
          <div class="col-xl-6">
            <fieldset class="form-group">
              <label class="form-label" ><?=$CMS->lang['tra_suffix_price'];?></label>
                <input class="form-control" placeholder="Vd: /người hoặc /trọn gói" type="text" name="p_up" id="p_up" value="<?=$tpl->p_up;?>"" >
            </fieldset>
          </div>

          <div class="col-xl-6">
            <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['p_order'];?></label>
              <input class="form-control" min="0" type="number" name="p_order" id="p_order" value="<?=$tpl->p_order;?>" />
            </fieldset>     
          </div>
        </div>
      <div class="row">
        <div class="col-xl-12"><h5 class="m-t-lg with-border"><?=$CMS->lang['title_info_tour'];?></h5></div>
        <div class="col-xl-12">
          <div class="row" style="margin-bottom: 1rem;">
            <!--tra_time_total-->
            <div class="col-xl-12"><label class="form-label" ><?=$CMS->lang['tra_time'];?></label></div>
            <div class="col-xl-6">
              <input class="form-control" style="width: 80%; float: left;" placeholder="Vd: 3 Ngày" type="number" min="0" name="tra_number_day" id="tra_number_day" value="<?=$tpl->tra_number_day;?>">
              <span class="unit_span" style="display: block; float: left; margin: 0 10px; font-size: 12px; text-align: center; line-height: 34px;" ><?=$CMS->lang['tra_time_day'];?></span>
            </div>
            <div class="col-xl-6">
              <input class="form-control" style="width: 80%; float: left;" placeholder="Vd: 2 Đêm" type="number" min="0" name="tra_number_night" id="tra_number_night" value="<?=$tpl->tra_number_night;?>">
              <span class="unit_span" style="display: block; float: left; margin: 0 10px; font-size: 12px; text-align: center; line-height: 34px;"><?=$CMS->lang['tra_time_night'];?></span>
            </div> 
          </div>
        </div>
      
        <div class="col-xl-6">
          <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['tra_time_start'];?></label>
              <input class="form-control datepicker" placeholder="Vd: 30/7/2017" type="text" name="tra_time_start" id="tra_time_start" value="<?=$tpl->tra_time_start;?>"" >
          </fieldset> 
        </div>

        <div class="col-xl-6">
          <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['tra_time_end'];?></label>
              <input class="form-control datepicker" placeholder="Vd: 30/7/2017" type="text" name="tra_time_end" id="tra_time_end" value="<?=$tpl->tra_time_end;?>"" >
          </fieldset> 
        </div>

        <div class="col-xl-6">
          <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['tra_vehicle_start'];?></label>
              <input class="form-control" placeholder="Vd: Xe, máy bay" type="text" name="tra_vehicle_start" id="tra_vehicle_start" value="<?=$tpl->tra_vehicle_start;?>"" >
          </fieldset> 
        </div>

        <div class="col-xl-6">
          <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['tra_vehicle_end'];?></label>
              <input class="form-control" placeholder="Vd: Xe, máy bay" type="text" name="tra_vehicle_end" id="tra_vehicle_end" value="<?=$tpl->tra_vehicle_end;?>"" >
          </fieldset> 
        </div>

        <div class="col-xl-6">
          <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['tra_minimum_seat'];?></label>
              <input class="form-control" placeholder="Vd: 12" type="text" maxlength="3" name="tra_minimum_seat" id="tra_minimum_seat" value="<?=$tpl->tra_minimum_seat;?>"" >
          </fieldset> 
        </div>

        <div class="col-xl-6">
          <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['tra_maximum_seat'];?></label>
              <input class="form-control" placeholder="Vd: 35" type="text" maxlength="3" name="tra_maximum_seat" id="tra_maximum_seat" value="<?=$tpl->tra_maximum_seat;?>"" >
          </fieldset> 
        </div>

        <div class="col-xl-6">
          <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['tra_departure_city'];?></label>
            <select name="city" id='product_city' onchange="change_district(this);" class="form-control auto_select" defaultvalue="<?=$tpl->product_city;?>">
              <?=$tpl->city;?>
            </select>
          </fieldset>
        </div>
      
        <div class="col-xl-6">
          <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['tra_departure_district'];?></label>
            <select name="district" id='product_district' class="form-control auto_select" defaultvalue="<?=$tpl->product_district;?>">
              <option value=""><?=$CMS->lang['select_district'];?></option>
              <?=$tpl->district;?>
            </select>
          </fieldset>
        </div>
      </div>

      


    </div><!-- col-md-6 -->
    
    <div class="col-md-6" >
      <div class="row">
        <div class="col-xl-12">
          <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['tra_map'];?></label>
            <textarea class="form-control" name="map" rows="5"><?=$tpl->map;?></textarea>
          </fieldset>
        </div>
        <div class="col-xl-12">
          <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['tra_summary_travel'];?></label>
            <textarea class="form-control" name="tra_summary_travel" placeholder="Vd: Rạch Giá – Khám Phá Đảo Nam Du - Chinh Phục Dốc Ông Tình – Hải Đăng Nam Du" rows="5"><?=$tpl->tra_summary_travel;?></textarea>
          </fieldset>
        </div>

      </div>
    </div>
    <script type="text/javascript">
    $(document).ready(function(){
      var setting = [];
      setting['height'] = 200;
      setting['selector'] = ".texarea_des";
      tinyMCEInit(setting);
    })
  </script>
      <div class="col-xl-6">
        <? if($CMS->vars['translations']) {
            foreach ($CMS->vars['translations'] as $langCode => $langName) { ?>
                <fieldset class="form-group langTab" lang="<?=$langCode;?>">
                  <label class="form-label" ><?=$CMS->lang['tra_description'];?></label>
                    <textarea name="p_description[<?=$langCode;?>]" rows="9" class="form-control texarea_des"><?=$tpl->p_description[$langCode];?></textarea>
                </fieldset>
        <? } } else {?>
                <fieldset class="form-group">
                  <label class="form-label" ><?=$CMS->lang['tra_description'];?></label>
                    <textarea name="p_description" rows="9" class="form-control texarea_des"><?=$tpl->p_description;?></textarea>
                </fieldset>
        <? } ?>
      </div>
      <div class="col-xl-6">
        <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['p_gallery'];?></label>
                <div class="box-typical-upload box-typical-upload-in">
                        <div class="drop-zone fileinput-button" style="width: 100%; height: 110px;">
                            <i class="font-icon font-icon-cloud-upload-2"></i>
                            <div class="drop-zone-caption">Drag file to upload</div>
                            <input type="file" multiple name="p_gallery[]" id="list_image" class="multiple_upload" accept="image/*">
                        </div><!--.drop-zone-->
                    <p class="box_error" style="display: none;"></p>
                    <div class="box_img_upload">
                      <? foreach ($tpl->list_gallery as $image) { ?>
                          <p class="img_list"><a class="del_img" id="<?=$CMS->input['id'];?>" onclick="del_img_box('<?=$image;?>', this);"><i class="fa fa-trash" aria-hidden="true"></i></a><img src="<?=$CMS->vars['upload_url'];?>/<?=$image;?>" class="img_item" />
                          <input type="hidden" name="old_gallery[]" value="<?=$image;?>"/>
                        </p>
                      <? } ?>
                    </div>
                    <h6 class="uploading-list-title title_upload" style="display: none;"><?=$CMS->lang['title_note_uploading'];?></h6>
                    <ul class="uploading-list list_upload">
                        
                    </ul>
                </div>
        </fieldset>

       <fieldset class="form-group">
        <div class="row">
          <div class="col-lg-6">
            <label class="form-label" ><?=$CMS->lang['p_show'];?></label>
            <?=$tpl->option_p_show;?>
          </div>
        </div><!-- row -->
      </fieldset>

    </div><!-- col-md-6 -->
    <div class="col-xl-12">
        <? if($CMS->vars['translations']) { ?>
        <? foreach ($CMS->vars['translations'] as $langCode => $langName) { ?>

      <div class="langTab" lang="<?=$langCode;?>">                
              <section class="tabs-section">
                    <div class="tabs-section-nav tabs-section-nav-inline">
                        <ul class="nav" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" href="#p_information_1_<?=$langCode;?>" role="tab" data-toggle="tab">
                                    <?=$CMS->lang['tra_overview'];?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#p_information_2_<?=$langCode;?>" role="tab" data-toggle="tab">
                                    <?=$CMS->lang['tra_tour_schedule'];?>
                                </a>
                            </li>
                        </ul>
                    </div><!--.tabs-section-nav-->

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade in active" id="p_information_1_<?=$langCode;?>">
                        <textarea name="p_information_1[<?=$langCode;?>]" rows="5" class="editor_texarea"><?=$tpl->p_information_1[$langCode];?></textarea>
                    </div><!--.tab-pane-->
                    <div role="tabpanel" class="tab-pane fade" id="p_information_2_<?=$langCode;?>">
                        <textarea name="p_information_2[<?=$langCode;?>]" rows="5" class="editor_texarea"><?=$tpl->p_information_2[$langCode];?></textarea>
                    </div><!--.tab-pane-->
                    
                </div><!--.tab-content-->
      </section>
    </div>
  <? }} else { ?>
              <section class="tabs-section">
                    <div class="tabs-section-nav tabs-section-nav-inline">
                        <ul class="nav" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" href="#p_information_1" role="tab" data-toggle="tab">
                                    <?=$CMS->lang['tra_overview'];?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#p_information_2" role="tab" data-toggle="tab">
                                    <?=$CMS->lang['tra_tour_schedule'];?>
                                </a>
                            </li>
                        </ul>
                    </div><!--.tabs-section-nav-->
            
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade in active" id="p_information_1">
                        <textarea name="p_information_1" rows="5" class="editor_texarea"><?=$tpl->p_information_1;?></textarea>
                    </div><!--.tab-pane-->
                    <div role="tabpanel" class="tab-pane fade" id="p_information_2">
                        <textarea name="p_information_2" rows="5" class="editor_texarea"><?=$tpl->p_information_2;?></textarea>
                    </div><!--.tab-pane-->
                    
                </div><!--.tab-content-->
            </section>
  <? } ?>
  
    </div>
    
  </div><!-- row -->
  
</section>

  <section class="add_cart_footer">
<?=$CMS->global->footer_back($tpl->url_back);?>
<?=$tpl->footer_button;?>
    </section>
    <input type="hidden" value="0" name="add_product_option"  />
 </form>
  

<script>  
$(document).ready(function(){
  validate_form_custom("#form-signin_v1",".act_submit_save");
  var list_internet = "<?=$tpl->list_internet;?>"; //adsl,wifi,optical_fiber
  var list_furniture = "<?=$tpl->list_furniture;?>";
  var list_foutside = "<?=$tpl->list_foutside;?>";
  var list_utilities = "<?=$tpl->list_utilities;?>";
  var list_type = "<?=$tpl->product_option;?>";

  list_internet = list_internet.split(",");
  for(var x in list_internet)
  {
    $("#"+list_internet[x]).prop("checked", true);
  }

  list_furniture = list_furniture.split(",");
  for(var x in list_furniture)
  {
    $("#"+list_furniture[x]).prop("checked", true);
  }

  list_foutside = list_foutside.split(",");
  for(var x in list_foutside)
  {
    $("#"+list_foutside[x]).prop("checked", true);
  }

  list_utilities = list_utilities.split(",");
  for(var x in list_utilities)
  {
    $("#"+list_utilities[x]).prop("checked", true);
  }
  // Checked type real
  var list_id = list_type.split(",");
  for(var x in list_id)
  {
    $("input[name='p_product_option[]'][value='"+list_id[x]+"']").prop("checked", true);
  }

  // check check_all
  // Internet
    check_checkall("internet", 1);
  // furniture
    check_checkall("furniture", 1);
  // foutside
    check_checkall("foutside", 1);
  // utilities
    check_checkall("utilities", 1);

  // Checked all
  $(".internet_all").click(function(){
      var check = $(this).attr("check");
      if(check==0)
      {
          $(".internet").prop("checked", true);
          $(this).attr("check", 1);
      }else
      {
          $(".internet").prop("checked", false);
          $(this).attr("check", 0);
      }
  });

  $(".furniture_all").click(function(){
      var check = $(this).attr("check");
      if(check==0)
      {
          $(".furniture").prop("checked", true);
          $(this).attr("check", 1);
      }else
      {
          $(".furniture").prop("checked", false);
          $(this).attr("check", 0);
      }
  });

  $(".foutside_all").click(function(){
      var check = $(this).attr("check");
      if(check==0)
      {
          $(".foutside").prop("checked", true);
          $(this).attr("check", 1);
      }else
      {
          $(".foutside").prop("checked", false);
          $(this).attr("check", 0);
      }
  });

  $(".utilities_all").click(function(){
      var check = $(this).attr("check");
      if(check==0)
      {
          $(".utilities").prop("checked", true);
          $(this).attr("check", 1);
      }else
      {
          $(".utilities").prop("checked", false);
          $(this).attr("check", 0);
      }
  });

  $(".internet").click(function(){
      check_checkall("internet");
  });
  $(".furniture").click(function(){
      check_checkall("furniture");
  });
  $(".foutside").click(function(){
      check_checkall("foutside");
  });
  $(".utilities").click(function(){
      check_checkall("utilities");
  });


  // Date Time Picker
  $('.datepicker').datetimepicker({
      format: dateFormatBooking,
      minDate: checktimebooking == 1 ? new Date().setHours(0,0,0,0) : new Date(),//new Date(new Date().setDate(new Date().getDate()-1)),
  });
});
  
  function del_img_box(link_image, obj)
  {
    swal({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
        }).then(function () {
          // Ajax del image
          $(obj).parent(".img_list").remove();
          var id = $(obj).attr("id");
          waitingDialog.show(cms_lang.waiting_dialog_msg);
          $.ajax({
                type: "post",
                url: site_root_domain+ "/?site=product&subact=unlink_img",
                data: {link_image:link_image, id:id},
                success: function(responsive)
                {
                  if(responsive == 1)
                  {
                    waitingDialog.hide();
                    swal( 'Deleted!', 'Your file has been deleted.', 'success');
                  }else
                  {
                    waitingDialog.hide();
                    swal( 'Delete False!', 'Error when you delete this file!', 'warning');
                  }
                }
              });

        });
  }

  // function change_wards(obj)
  // {
  //   var district_id = $(obj).val();
  //   $("#product_wards").html("");
  //   $.ajax({
  //       type: "post",
  //       url: "/acp/?site=product&subact=getwards",
  //       data: {district_id: district_id},
  //       success: function(response)
  //       {
  //           $("#product_wards").html(response);
  //       }
  //   });
  // }

  function check_checkall(elm, type)
  {
      // check 
      var uncheck = $("."+elm).length;
      var checked = $("."+elm+":checked").length;
      if(uncheck == checked) 
      { 
          if(type)
          {
            $("."+elm).prop("checked", true); 
          }
          $("."+elm+"_all").attr("check", 1);
          $("."+elm+"_all").prop("checked", true);
      }else
      {
          $("."+elm+"_all").attr("check", 0);
          $("."+elm+"_all").prop("checked", false);
      }
  }

  function change_district(obj)
  {
      var city_id = $(obj).val();
      $("#product_district").html("");
      $.ajax({
          type: "post",
          url: "/acp/?site=product&subact=getdistrict",
          data: {city_id: city_id},
          success: function(response)
          {
              $("#product_district").html(response);
          }
      });
  }

  function apply_price(elm)
  {
      var price = $("input[name='p_price_show']").val();
      var unit = $("select[name='p_up']").val();

      if(unit == "billion" || unit == "billion_m2" || unit == "billion_apartment")
      {
        $("input[name='p_price_sell']").val(price * 1000000000);
      }else if(unit == "million" || unit == "million_m2" || unit == "million_apartment" || unit == "million_month")
      {
        $("input[name='p_price_sell']").val(price * 1000000);
      }else
      {
        $("input[name='p_price_sell']").val(price);
      }
  }

  var module_name = "product_module"; 
  var lang_supplier_add = "<?=$CMS->lang['supplier_add'];?>";
  var lang_supplier_edit = "<?=$CMS->lang['supplier_edit'];?>";

  var lang_manufacture_edit  = "<?=$CMS->lang['manufacture_edit'];?>";
  var lang_manufacture_add = "<?=$CMS->lang['manufacture_add'];?>";
</script>
 <script src="<?=$CMS->vars['js_acp'];?>/product.js"></script>
 
    <?=\core\ezy::render("add_supplier", "product");?>
    <?=\core\ezy::render("add_manufacture", "product");?>
    <?=\core\ezy::render("add_product_group", "product");?>

