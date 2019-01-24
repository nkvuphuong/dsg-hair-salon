<form id="form-signin_v1" name="form-signin_v1" action="<?=$tpl->action_form;?>" method="POST" enctype="multipart/form-data">

<section class="add_form main_form">
  <figure class="heading">
    <h3><?=$CMS->lang['real_'.$CMS->input['act']];?></h3>
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
                  <label class="form-label" ><?=$CMS->lang['real_name'];?> <span style="color:red">(*)</span></label>
                   <div class="form-control-wrapper">
                    <input class="form-control " type="text" name="p_name[<?=$langCode;?>]" id="p_name[<?=$langCode;?>]" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['p_name_err'];?>" value="<?=$tpl->p_name[$langCode];?>">
                  </div>
                  </fieldset>
              </div>
        <? } }else {?>

                <div class="col-xl-6">
                  <fieldset class="form-group">
                  <label class="form-label" ><?=$CMS->lang['real_name'];?> <span style="color:red">(*)</span></label>
                  <div class="form-control-wrapper">
                    <input class="form-control " type="text" name="p_name" id="p_name" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['p_name_err'];?>" value="<?=$tpl->p_name;?>">
                  </div>
                  </fieldset>
                </div>
        <? } ?>
        
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
            <label class="form-label pull-left" ><?=$CMS->lang['real_product_group'];?> <span style="color:red">(*)</span></label>
        <? if ($CMS->permit["product_group_add"] == 1) { ?>
              <a   data-size="s" class="add_new_product_group pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline">
                <i class="fa fa-plus" aria-hidden="true"></i> 
              </a>
        <? } ?>

        <? if ($CMS->permit["product_group_edit"] == 1) { ?>
            <span class="box_product_group pull-right" style="margin-left:10px"></span>
        <? } ?>
            <div class="form-control-wrapper">
              <select class="form-control select2 select_product_group" name="p_product_group" defaultvalue="<?=$tpl->p_product_group;?>" id="p_product_group" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['real_product_group_err'];?>"  >
                <?=$tpl->option_p_product_group;?>
              </select>
            </div>
          </fieldset>

          <fieldset class="form-group">
            <label class="form-label pull-left" ><?=$CMS->lang['real_option'];?></label>
            <div class="form-control-wrapper">
                <div class='checkbox'>
                    <input type='checkbox' name='p_product_option[]' value='1' id='check-1'  >
                    <label for='check-1'><?=$CMS->lang['real_option_1'];?></label>
                </div>
                <div class='checkbox'>
                    <input type='checkbox' name='p_product_option[]' value='2' id='check-2'  >
                    <label for='check-2'><?=$CMS->lang['real_option_2'];?></label>
                </div>
                <div class='checkbox'>
                    <input type='checkbox' name='p_product_option[]' value='5' id='check-5'  >
                    <label for='check-5'><?=$CMS->lang['real_option_5'];?></label>
                </div>
            </div>
          </fieldset>
          <div class="form-control-wrapper">
            <label class="form-label pull-left" ><?=$CMS->lang['real_option_type'];?></label>
            <select class="form-control auto_select" name="product_real_type" defaultvalue="<?=$tpl->product_real_type;?>" id="product_real_type">
              <option value=""><?=$CMS->lang['real_choose_type'];?></option>
              <option value="1"><?=$CMS->lang['real_type_1'];?></option>
              <option value="2"><?=$CMS->lang['real_type_2'];?></option>
            </select>
          </div>
        </div>


      </div>

      <div class="row">
          <div class="col-xl-12"><h5 class="m-t-lg with-border"><?=$CMS->lang['title_info_transaction'];?></h5></div>
          
          <div class="col-xl-12">
            <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['real_acreage'];?></label>
              <input class="form-control" onkeypress="return check_enter_number(event,this);" type="text" name="acreage" id="product_acreage" value="<?=$tpl->product_acreage;?>" />
            </fieldset>     
          </div>

          <div class="col-xl-6">
            <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['p_price_sell'];?></label>
              <input class="form-control " type="text" name="p_price_show" id="p_price_show" value="<?=$tpl->p_price_show;?>" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" onkeyup="apply_price('p_price_sell');" >
              <input class="form-control " type="hidden" name="p_price_sell" id="p_price_sell" value="<?=$tpl->p_price_sell;?>" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
            </fieldset> 
          </div>
          <div class="col-xl-6">
            <fieldset class="form-group">
              <label class="form-label" ><?=$CMS->lang['real_suffix_price'];?></label>
              <select name="p_up" id='p_up' onchange="apply_price('p_price_sell');" class="form-control auto_select" defaultvalue="<?=$tpl->p_up;?>">
                <option value="price_agreed"><?=$CMS->lang['title_unit_price_0'];?></option>
                <option value="billion"><?=$CMS->lang['title_unit_price_1'];?></option>
                <option value="million"><?=$CMS->lang['title_unit_price_2'];?></option>
                <option value="million_m2"><?=$CMS->lang['title_unit_price_3'];?></option>
                <option value="billion_m2"><?=$CMS->lang['title_unit_price_4'];?></option>
                <option value="billion_apartment"><?=$CMS->lang['title_unit_price_5'];?></option>
                <option value="million_apartment"><?=$CMS->lang['title_unit_price_6'];?></option>
                <option value="million_month"><?=$CMS->lang['title_unit_price_7'];?></option>
              </select>
            </fieldset>
          </div>
      </div>

        <div class="row">
          <div class="col-xl-6">
            <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['p_mobile'];?></label>
              <input class="form-control" type="text" name="real_phone" id="real_phone" value="<?=$tpl->real_phone;?>" />
            </fieldset>     
          </div>
          <div class="col-xl-6">
            <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['p_order'];?></label>
              <input class="form-control" min="0" type="number" name="p_order" id="p_order" value="<?=$tpl->p_order;?>" />
            </fieldset>     
          </div>
        </div>


    </div><!-- col-md-6 -->
    
    <div class="col-md-6" >
      <div class="row">
        <div class="col-xl-6">
          <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['real_city'];?></label>
            <select name="city" id='product_city' onchange="change_district(this);" class="form-control auto_select" defaultvalue="<?=$tpl->product_city;?>">
              <?=$tpl->city;?>
            </select>
          </fieldset>
        </div>
      
        <div class="col-xl-6">
          <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['real_district'];?></label>
            <select name="district" id='product_district' class="form-control auto_select" defaultvalue="<?=$tpl->product_district;?>">
              <option value=""><?=$CMS->lang['select_district'];?></option>
              <?=$tpl->district;?>
            </select>
          </fieldset>
        </div>

        <div class="col-xl-6">
          <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['real_Wards'];?></label>
            <input type="text" class="form-control" name="wards" value="<?=$tpl->wards;?>" autocomplete="off">
            <!--select name="product_wards" id='product_wards' class="form-control" defaultvalue="<?=$tpl->p_up;?>">
              <option value=""><?=$CMS->lang['cus_select_town'];?></option>
            </select-->
          </fieldset>
        </div>

        <div class="col-xl-6">
          <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['real_street'];?></label>
            <input type="text" class="form-control" name="street" value="<?=$tpl->street;?>" autocomplete="off">
          </fieldset>
        </div>

        <div class="col-xl-12">
          <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['real_map'];?></label>
            <textarea class="form-control" name="map" rows="5"><?=$tpl->map;?></textarea>
          </fieldset>
        </div>

      </div>
    </div>
      <div class="col-xl-6">
        <? if($CMS->vars['translations']) {
            foreach ($CMS->vars['translations'] as $langCode => $langName) { ?>
                <fieldset class="form-group langTab" lang="<?=$langCode;?>">
                  <label class="form-label" ><?=$CMS->lang['real_description'];?></label>
                    <textarea name="p_description[<?=$langCode;?>]" rows="9" class="form-control"><?=$tpl->p_description[$langCode];?></textarea>
                </fieldset>
        <? } } else {?>
                <fieldset class="form-group">
                  <label class="form-label" ><?=$CMS->lang['real_description'];?></label>
                    <textarea name="p_description" rows="9" class="form-control"><?=$tpl->p_description;?></textarea>
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
                                    <?=$CMS->lang['p_content'];?>
                                </a>
                            </li>
                        </ul>
                    </div><!--.tabs-section-nav-->

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade in active" id="p_information_1_<?=$langCode;?>">
                        <textarea name="p_information_1[<?=$langCode;?>]" rows="5" class="editor_texarea"><?=$tpl->p_information_1[$langCode];?></textarea>
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
                                    <?=$CMS->lang['p_content'];?>
                                </a>
                            </li>
                        </ul>
                    </div><!--.tabs-section-nav-->
            
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade in active" id="p_information_1">
                        <textarea name="p_information_1" rows="5" class="editor_texarea"><?=$tpl->p_information_1;?></textarea>
                    </div><!--.tab-pane-->
                    
                </div><!--.tab-content-->
            </section>
  <? } ?>
  
    </div>
    <!-- Thuoc tinh bds -->
    <div class="col-xl-12">
      <h5 class="m-t-lg with-border"><?=$CMS->lang['title_attribute_realestate'];?></h5>

      <div class="col-xl-3">
        <fieldset class="form-group">
          <label class="form-label title_label_attr" ><?=$CMS->lang['real_facade'];?></label>
          <input type="text" class="form-control" name="facade" value="<?=$tpl->facade;?>" autocomplete="off">
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group">
          <label class="form-label title_label_attr" ><?=$CMS->lang['real_entrance'];?></label>
          <input type="text" class="form-control" name="entrance" value="<?=$tpl->entrance;?>" autocomplete="off">
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group">
          <label class="form-label title_label_attr" ><?=$CMS->lang['real_direction'];?></label>
          <select class="form-control auto_select" name="direction" defaultvalue="<?=$tpl->direction;?>">
              <option value=""><?=$CMS->lang['real_please_choose'];?></option>
              <option value="east"><?=$CMS->lang['real_direction_1'];?></option>
              <option value="west"><?=$CMS->lang['real_direction_2'];?></option>
              <option value="south"><?=$CMS->lang['real_direction_3'];?></option>
              <option value="north"><?=$CMS->lang['real_direction_4'];?></option>
              <option value="northeast"><?=$CMS->lang['real_direction_5'];?></option>
              <option value="northwest"><?=$CMS->lang['real_direction_6'];?></option>
              <option value="southeast"><?=$CMS->lang['real_direction_7'];?></option>
              <option value="southwest"><?=$CMS->lang['real_direction_8'];?></option>
              <option value="unknown"><?=$CMS->lang['real_direction_0'];?></option>
          </select>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group">
          <label class="form-label title_label_attr" ><?=$CMS->lang['real_direction_of_balcony'];?></label>
          <select class="form-control auto_select" name="dbalcon" defaultvalue="<?=$tpl->dbalcon;?>">
              <option value=""><?=$CMS->lang['real_please_choose'];?></option>
              <option value="east"><?=$CMS->lang['real_direction_1'];?></option>
              <option value="west"><?=$CMS->lang['real_direction_2'];?></option>
              <option value="south"><?=$CMS->lang['real_direction_3'];?></option>
              <option value="north"><?=$CMS->lang['real_direction_4'];?></option>
              <option value="northeast"><?=$CMS->lang['real_direction_5'];?></option>
              <option value="northwest"><?=$CMS->lang['real_direction_6'];?></option>
              <option value="southeast"><?=$CMS->lang['real_direction_7'];?></option>
              <option value="southwest"><?=$CMS->lang['real_direction_8'];?></option>
              <option value="unknown"><?=$CMS->lang['real_direction_0'];?></option>
          </select>
        </fieldset>
      </div>

      <div class="col-xl-3">
        <fieldset class="form-group">
          <label class="form-label title_label_attr" ><?=$CMS->lang['real_number_of_floors'];?></label>
          <input type="text" class="form-control" name="nfloors" value="<?=$tpl->nfloors;?>" autocomplete="off">
        </fieldset>
      </div>

      <div class="col-xl-3">
        <fieldset class="form-group">
          <label class="form-label title_label_attr" ><?=$CMS->lang['real_number_of_room'];?></label>
          <input type="text" class="form-control" name="nroom" value="<?=$tpl->nroom;?>" autocomplete="off">
        </fieldset>
      </div>

      <div class="col-xl-3">
        <fieldset class="form-group">
          <label class="form-label title_label_attr" ><?=$CMS->lang['real_number_of_bedroom'];?></label>
          <input type="text" class="form-control" name="nbedroom" value="<?=$tpl->nbedroom;?>" autocomplete="off">
        </fieldset>
      </div>

      <div class="col-xl-3">
        <fieldset class="form-group">
          <label class="form-label title_label_attr" ><?=$CMS->lang['real_number_of_bathrooms'];?></label>
          <input type="text" class="form-control" name="nbathrooms" value="<?=$tpl->nbathrooms;?>" autocomplete="off">
        </fieldset>
      </div>

    </div>


    <div class="col-xl-12">
      <label class="form-label title_label_attr ml-15" ><?=$CMS->lang['real_internet'];?>
        <input type="checkbox" class="sinput internet_all" id="internet">
      </label>
      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="optical_fiber" class="internet" value="optical_fiber" name="internet[]">
          <label for="optical_fiber"><?=$CMS->lang['attr_internet_1'];?></label>
        </div>
      </div>
      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="adsl" class="internet" value="adsl" name="internet[]">
          <label for="adsl"><?=$CMS->lang['attr_internet_2'];?></label>
        </div>
      </div>
      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="wifi" class="internet" value="wifi" name="internet[]">
          <label for="wifi"><?=$CMS->lang['attr_internet_3'];?></label>
        </div>
      </div>
    </div>

    <div class="col-xl-12">
      <label class="form-label title_label_attr ml-15" ><?=$CMS->lang['real_furniture'];?><input type="checkbox" class="sinput furniture_all" id="furniture"></label>
      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="air_conditioner" value="air_conditioner" class="furniture" name="furniture[]">
          <label for="air_conditioner"><?=$CMS->lang['attr_furniture_1'];?></label>
        </div>
      </div>
      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="washing_machine" value="washing_machine" class="furniture" name="furniture[]">
          <label for="washing_machine"><?=$CMS->lang['attr_furniture_2'];?></label>
        </div>
      </div>
      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="salon" class="furniture" value="salon" name="furniture[]">
          <label for="salon"><?=$CMS->lang['attr_furniture_3'];?></label>
        </div>
      </div>

      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="wall_cabinets" value="wall_cabinets" class="furniture" name="furniture[]">
          <label for="wall_cabinets"><?=$CMS->lang['attr_furniture_4'];?></label>
        </div>
      </div>

      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="kitchen" value="kitchen" class="furniture" name="furniture[]">
          <label for="kitchen"><?=$CMS->lang['attr_furniture_5'];?></label>
        </div>
      </div>

      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="fridge" value="fridge" class="furniture" name="furniture[]">
          <label for="fridge"><?=$CMS->lang['attr_furniture_6'];?></label>
        </div>
      </div>

      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="bed" value="bed" class="furniture" name="furniture[]">
          <label for="bed"><?=$CMS->lang['attr_furniture_7'];?></label>
        </div>
      </div>
    </div>


    <div class="col-xl-12">
      <label class="form-label title_label_attr ml-15" ><?=$CMS->lang['real_facilities_outside'];?><input type="checkbox" class="sinput foutside_all" id="foutside"></label>
      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="pool" value="pool" class="foutside" name="foutside[]">
          <label for="pool"><?=$CMS->lang['attr_foutside_1'];?></label>
        </div>
      </div>
      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="balcony" value="balcony" class="foutside" name="foutside[]">
          <label for="balcony"><?=$CMS->lang['attr_foutside_2'];?></label>
        </div>
      </div>

      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="terrace" value="terrace" class="foutside" name="foutside[]">
          <label for="terrace"><?=$CMS->lang['attr_foutside_3'];?></label>
        </div>
      </div>

      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="bbq" value="bbq" class="foutside" name="foutside[]">
          <label for="bbq"><?=$CMS->lang['attr_foutside_4'];?></label>
        </div>
      </div>

      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="gym" value="gym" class="foutside" name="foutside[]">
          <label for="gym"><?=$CMS->lang['attr_foutside_5'];?></label>
        </div>
      </div>

      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="sport_area" value="sport_area" class="foutside" name="foutside[]">
          <label for="sport_area"><?=$CMS->lang['attr_foutside_6'];?></label>
        </div>
      </div>

      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="cplay_area" value="cplay_area" class="foutside" name="foutside[]">
          <label for="cplay_area"><?=$CMS->lang['attr_foutside_7'];?></label>
        </div>
      </div>

      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="gara" value="gara" class="foutside" name="foutside[]">
          <label for="gara"><?=$CMS->lang['attr_foutside_8'];?></label>
        </div>
      </div>

      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="parking" value="parking" class="foutside" name="foutside[]">
          <label for="parking"><?=$CMS->lang['attr_foutside_9'];?></label>
        </div>
      </div>

    </div>


    <div class="col-xl-12">
      <label class="form-label title_label_attr ml-15" ><?=$CMS->lang['real_utilities_area'];?><input type="checkbox" class="sinput utilities_all" id="utilities"></label>
      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="park" value="park" class="utilities" name="utilities[]">
          <label for="park"><?=$CMS->lang['attr_uarea_1'];?></label>
        </div>
      </div>
      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="hospital" value="hospital" class="utilities" name="utilities[]">
          <label for="hospital"><?=$CMS->lang['attr_uarea_2'];?></label>
        </div>
      </div>
      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="bank" value="bank" class="utilities" name="utilities[]">
          <label for="bank"><?=$CMS->lang['attr_uarea_3'];?></label>
        </div>
      </div>

      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="school" value="school" class="utilities" name="utilities[]">
          <label for="school"><?=$CMS->lang['attr_uarea_4'];?></label>
        </div>
      </div>

      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="atm" value="atm" class="utilities" name="utilities[]">
          <label for="atm"><?=$CMS->lang['attr_uarea_5'];?></label>
        </div>
      </div>

      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="coffee" value="coffee" class="utilities" name="utilities[]">
          <label for="coffee"><?=$CMS->lang['attr_uarea_6'];?></label>
        </div>
      </div>

      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="dentistry" value="dentistry" class="utilities" name="utilities[]">
          <label for="dentistry"><?=$CMS->lang['attr_uarea_7'];?></label>
        </div>
      </div>

      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="police_office" value="police_office" class="utilities" name="utilities[]">
          <label for="police_office"><?=$CMS->lang['attr_uarea_8'];?></label>
        </div>
      </div>

      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="diner" value="diner" class="utilities" name="utilities[]">
          <label for="diner"><?=$CMS->lang['attr_uarea_9'];?></label>
        </div>
      </div>

      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="restaurant" value="restaurant" class="utilities" name="utilities[]">
          <label for="restaurant"><?=$CMS->lang['attr_uarea_10'];?></label>
        </div>
      </div>


      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="grocery" value="grocery" class="utilities" name="utilities[]">
          <label for="grocery"><?=$CMS->lang['attr_uarea_11'];?></label>
        </div>
      </div>

      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="hotel" value="hotel" class="utilities" name="utilities[]">
          <label for="hotel"><?=$CMS->lang['attr_uarea_12'];?></label>
        </div>
      </div>

      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="market" value="market" class="utilities" name="utilities[]">
          <label for="market"><?=$CMS->lang['attr_uarea_13'];?></label>
        </div>
      </div>

      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="supermarket" value="supermarket" class="utilities" name="utilities[]">
          <label for="supermarket"><?=$CMS->lang['attr_uarea_14'];?></label>
        </div>
      </div>

      <div class="col-xl-2">
        <div class="checkbox">
          <input type="checkbox" id="bus_stop" value="bus_stop" class="utilities" name="utilities[]">
          <label for="bus_stop"><?=$CMS->lang['attr_uarea_15'];?></label>
        </div>
      </div>
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

