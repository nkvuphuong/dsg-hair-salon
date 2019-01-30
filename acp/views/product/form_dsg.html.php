<form id="form-signin_v1" name="form-signin_v1" action="<?=$tpl->action_form;?>" method="POST" enctype="multipart/form-data">

<section class="add_form main_form">
  <figure class="heading">
    <h3><?=$CMS->lang['p_'.$CMS->input['act']];?></h3>
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
                  <label class="form-label" ><?=$CMS->lang['p_name'];?> <span style="color:red">(*)</span></label>
                   <div class="form-control-wrapper">
                    <input class="form-control " type="text" name="p_name[<?=$langCode;?>]" id="p_name[<?=$langCode;?>]" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['p_name_err'];?>" value="<?=$tpl->p_name[$langCode];?>">
                  </div>
                  </fieldset>
              </div>
        <? } }else {?>

                <div class="col-xl-6">
                  <fieldset class="form-group">
                  <label class="form-label" ><?=$CMS->lang['p_name'];?> <span style="color:red">(*)</span></label>
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
                <label class="form-label" ><?=$CMS->lang['p_estimated_time'];?></label>
                <input class="form-control" type="number" min="0" name="p_estimated_time" id="p_estimated_time" value="<?=$tpl->p_estimated_time;?>">
            </fieldset>

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
               
                <div class="drop-zone fileinput-button drop-zone-wrap <?=$tpl->src_image_upload ? 'exist-preview' : '';?>" style="height: 100% !important;padding: 5px;">
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
            <label class="form-label pull-left" ><?=$CMS->lang['p_product_group'];?> <span style="color:red">(*)</span></label>
        <? if ($CMS->permit["product_group_add"] == 1) { ?>
              <a   data-size="s" class="add_new_product_group pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline">
                <i class="fa fa-plus" aria-hidden="true"></i> 
              </a>
        <? } ?>

        <? if ($CMS->permit["product_group_edit"] == 1) { ?>
            <span class="box_product_group pull-right" style="margin-left:10px"></span>
        <? } ?>
            <div class="form-control-wrapper">
              <select class="form-control select2 select_product_group" name="p_product_group" defaultvalue="<?=$tpl->p_product_group;?>" id="p_product_group" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['p_product_group_err'];?>"  >
                <?=$tpl->option_p_product_group;?>
              </select>
            </div>
          </fieldset>

          <fieldset class="form-group">
            <label class="form-label pull-left" ><?=$CMS->lang['p_product_option'];?></label>
            <div class="form-control-wrapper">
            
                <div class='checkbox'>
                    <input type='checkbox' name='p_product_option[]' value='1' id='check-1'  >
                    <label for='check-1'><?=$CMS->lang['product_option_1'];?></label>
                </div>
                <div class='checkbox'>
                    <input type='checkbox' name='p_product_option[]' value='4' id='check-4'  >
                    <label for='check-4'><?=$CMS->lang['product_option_4'];?></label>
                </div>
                <div class='checkbox'>
                    <input type='checkbox' name='p_product_option[]' value='6' id='check-6'  >
                    <label for='check-6'><?=$CMS->lang['product_option_6'];?></label>
                </div>
            </div>
          </fieldset>
        </div>
      </div>

      <div class="row">
          <div class="col-xl-12"><h5 class="m-t-lg with-border"><?=$CMS->lang['title_info_transaction'];?></h5></div>
          
          <div class="col-xl-6">
            <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['p_cycle'];?></label>
                <select class="form-control auto_select" name="p_cycle" id="p_cycle" defaultvalue="<?=$tpl->p_cycle;?>" >
                  <option value='0'><?=$CMS->lang['p_cycle_00'];?></option>
                  <option value='1'><?=$CMS->lang['p_cycle_01'];?></option>
                  <option value='2'><?=$CMS->lang['p_cycle_02'];?></option>
                </select>
            </fieldset>   
           <fieldset class="form-group mb0">
              <div class="row">
                <div class="col-lg-6">
                  <label class="form-label" ><?=$CMS->lang['p_show'];?></label>
                  <?=$tpl->option_p_show;?>
                </div>
              </div><!-- row -->

            </fieldset>

          </div>

          <div class="col-xl-6">
            <fieldset class="form-group">
              <label class="form-label" ><?=$CMS->lang['p_price'];?></label>
                <input class="form-control " type="text" name="p_price" id="p_price" value="<?=$tpl->p_price;?>"  onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
            </fieldset>  
            <fieldset class="form-group">
              <label class="form-label" ><?=$CMS->lang['p_price_sell'];?></label>
                <input class="form-control " type="text" name="p_price_sell" id="p_price_sell" value="<?=$tpl->p_price_sell;?>"  onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
            </fieldset>  

            <fieldset class="form-group">
                <label class="form-label" ><?=$CMS->lang['staff_id'];?> <br>
                    <span class="btn btn-primary btn-sm" onclick="select2ChooseAllToggle(true)"><i class="fa fa-check-square" aria-hidden="true"></i> <?=\lib\input::lang('check_all')?></span>
                    <span class="btn btn-secondary btn-sm" onclick="select2ChooseAllToggle(false)"><i class="fa fa-square-o" aria-hidden="true"></i> <?=\lib\input::lang('uncheck_all')?></span>
                </label>
                <select id="staff-select2" class="select2" multiple="multiple" name="staff_id[]">
                  <?=$tpl->option_staff;?>
                </select>
            </fieldset>
              <script>
                  function select2ChooseAllToggle(selected) {
                      $("#staff-select2 option").prop("selected", selected);
                      $("#staff-select2").trigger("change");
                  }
              </script>

          </div>

         
         
  
        </div>
        
    </div><!-- col-md-6 -->
    
    
    <div class="col-md-6" >
       
</div>
   
    <div class="col-md-6" >
        <? if($CMS->vars['translations']) {
            foreach ($CMS->vars['translations'] as $langCode => $langName) { ?>
                <fieldset class="form-group langTab" lang="<?=$langCode;?>">
                  <label class="form-label" ><?=$CMS->lang['p_description'];?></label>
                    <textarea name="p_description[<?=$langCode;?>]" rows="5" class="form-control"><?=$tpl->p_description[$langCode];?></textarea>
                </fieldset>
        <? } } else {?>
                <fieldset class="form-group">
                  <label class="form-label" ><?=$CMS->lang['p_description'];?></label>
                    <textarea name="p_description" rows="5" class="form-control"><?=$tpl->p_description;?></textarea>
                </fieldset>
        <? } ?>

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
                        <? if($tpl->list_gallery) { ?>
                            <? foreach ($tpl->list_gallery as $image) { ?>
                                <p class="img_list"><a class="del_img" id="<?=$CMS->input['id'];?>" onclick="del_img_box('<?=$image;?>', this);"><i class="fa fa-trash" aria-hidden="true"></i></a><img src="<?=$CMS->vars['upload_url'];?>/<?=$image;?>" class="img_item" />
                                    <input type="hidden" name="old_gallery[]" value="<?=$image;?>"/>
                                </p>
                            <? } ?>
                        <? } ?>
                    </div>
                    <h6 class="uploading-list-title title_upload" style="display: none;"><?=$CMS->lang['title_note_uploading'];?></h6>
                    <ul class="uploading-list list_upload">
                        
                    </ul>
                </div>
        </fieldset>
        
 
        
    </div><!-- col-md-6 -->
  </div><!-- row -->

   

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


</section>
  
<!--Subitem-->
<section class="add_table">
  <figure class="box-typical border col-xl-12" style="clear: both; overflow: hidden;">
    <h5 class="m-t-lg with-border"><?=$CMS->lang['title_sub_product'];?></h5>
    <div class="box_choose_type" style="display:none">
      <div class="col-xl-3">
        <div class="radio">
          <input type="radio" name="type_form" id="type_form_1" value="1" pid="<?=$CMS->input['id'];?>" checked="checked">
          <label for="type_form_1"><?=$CMS->lang['title_product_child'];?> <a data-toggle="tooltip" title="Các sản phẩm được gõ dưới đây sẽ giống như là mô tả chi tiết thêm cho sản phẩm gốc!"><i class="fa fa-question-circle" aria-hidden="true"></i></a></label>
        </div>
      </div>
      <div class="col-xl-3">
        <div class="radio">
          <input type="radio" name="type_form" id="type_form_2" pid="<?=$CMS->input['id'];?>" value="2">
          <label for="type_form_2"><?=$CMS->lang['title_similar_products'];?> <a data-toggle="tooltip" title="Các sản phẩm được gõ dưới đây sẽ dùng thông tin sản phẩm gốc để tạo ra hàng loạt sản phẩm có mô tả, giá hoặc số tiền khác nhau mà không phải gõ lại nhiều lần. Thích hợp để tạo hàng loạt sản phẩm tương đồng"><i class="fa fa-question-circle" aria-hidden="true"></i></a></label>
        </div>
      </div>
    </div>
     <!-- responsive-table -->
    <? if ($_SESSION['is_mobile'] == true) { ?>
        <table id="table_item" class="table_cus">
    <? } else { ?>
        <table class="responsive-table table_cus">
    <?  } ?>
                <thead>
                  <tr>
                    <th  scope="col" width="2%"></th>
    <? if ($_SESSION['is_mobile'] == false) { ?>
                    <th  scope="col" width="3%"><?=$CMS->lang['stt'];?></th>
    <? } ?>
                    <th  scope="col" width="15%"><?=$CMS->lang['product_service'];?></th>
                    <th  scope="col" width="12%"><?=$CMS->lang['p_sku'];?></th>
                    
                    <th  scope="col" width="25%"><?=$CMS->lang['pg_description'];?></th>
                    <th  scope="col" width="7%" class="class_qty"><?=$CMS->lang['stock_store_quantity'];?></th>
                    <th  scope="col" width="8%" class="class_color"><?=$CMS->lang['title_color'];?></th>
                    <th  scope="col" width="10%"><?=$CMS->lang['price_no_vat'];?></th>
                    <th  scope="col" width="7%"><?=$CMS->lang['vat_percent'];?></th>
                    <th  scope="col" width="3%"></th>
                  </tr>
                </thead>  
                <tbody id="data_table">
      <?  $total_row = count($tpl->un_product_subitem);
          if ($total_row > 0) {
            foreach ($tpl->un_product_subitem as $key => $value) { $key = $key + 1; ?>
                <tr class="row-grid" rowtr="">
                   <td scope="row"  ><i class="hidden-sm-down fa fa-th handle"></i></td>  
                <? if ($_SESSION['is_mobile'] == false) { ?>
                     <td scope="row" data-title="<?=$CMS->lang['stt'];?>" class="grid-td" for="dgrid-1" check="chtd"><span class="number">#<?=$key;?></span></td>
                <? } ?>
                  <td data-title="<?=$CMS->lang['product_service'];?>"  class="grid-td" for="dgrid-2"> 
                     <div class="box_container">  
                      <figure class="text_r"> 
                        <input type="text"  for="dgrid-2"  check="chtd"  class="form-control find_product " autocomplete="off"  name ="sub_product_name[]" value="<?=$value['product_name'];?>" onfocusout="return convert_sku_code(this);" /> 
                      </figure> 
                        <div class="box_result_find" style="display:none" ></div>
                     </div> 
                      <input type="hidden" class="product_id" name="sub_product_id[]" value=""/>
                  </td>
                  <td data-title="<?=$CMS->lang['p_sku'];?>"  class="grid-td" for="dgrid-3" >
                    <figure class="text_r">
                       <input name ="sub_product_sku[]" value="<?=$value['product_sku'];?>" class="form-control" for="dgrid-3" /> 
                    </figure>  
                  </td>

                  <td  data-title="<?=$CMS->lang['pg_description'];?>" class="grid-td" for="dgrid-4" check="chtd" >
                    <figure class="text_r">
                       <textarea rows="4" for="dgrid-4" name ="sub_product_description[]" class="form-control" placeholder="<?=$CMS->lang['pg_description'];?>"><?=$value['product_description'];?></textarea>
                    </figure>  
                  </td>
                  <td data-title="<?=$CMS->lang['stock_store_quantity'];?>"  class="grid-td class_qty" for="dgrid-5" >
                    <figure class="text_r">
                       <input name ="sub_product_quantity[]" value="<?=$value['product_quantity'];?>"   onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this);"  class="form-control " type="number" for="dgrid-5" /> 
                    </figure>  
                  </td>

                  <td data-title="<?=$CMS->lang['title_color'];?>" class="grid-td class_color" for="dgrid-15" >
                    <figure class="text_r">
                       <input name ="sub_color[]" value="" class="form-control" onclick="choose_color(this);" type="text" for="dgrid-15" autocomplete="off" /> 
                       
                    </figure>  
                  </td>

                  <td data-title="<?=$CMS->lang['price_no_vat'];?>"  class="grid-td" for="dgrid-6" >
                    <figure class="text_r">
                       <input name ="sub_product_price[]" value="<?=$value['product_price'];?>" class="form-control" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" for="dgrid-6" /> 
                    </figure>  
                  </td>
                  <td data-title="<?=$CMS->lang['vat_percent'];?>" class="grid-td" for="dgrid-7" >  
                    <figure class="text_r"> 
                      <input for="dgrid-7" class="form-control" value="<?=$value['product_tax'];?>" name ="sub_product_tax[]"/>
                    </figure>  
                  </td>
                  <td class="trash" for="dgrid-8"><span class="del_row_product text_r"><i class="fa fa-trash" aria-hidden="true"></i></span></td>
              </tr>

              <? if ($key == $total_row) { $key = $total_row + 1; ?>
                 <tr class="row-grid" rowtr="last-row">
                    <td scope="row"><i class="hidden-sm-down fa fa-th handle"></i></td> 
                    <? if ($_SESSION['is_mobile'] == false) { ?>
                     <td scope="row" data-title="<?=$CMS->lang['stt'];?>" class="grid-td" for="dgrid-1" check="chtd"><span class="number">#<?=$key;?></span></td>
                    <? } ?>
                    <td data-title="<?=$CMS->lang['product_service'];?>" class="grid-td" for="dgrid-2">
                       <div class="box_container">  
                        <figure class="text_r">
                            <input type="text"  for="dgrid-2"  check="chtd"  class="form-control find_product " autocomplete="off"  name ="sub_product_name[]" onfocusout="return convert_sku_code(this);" value=""/> 
                           </figure>  
                            <div class="box_result_find" style="display:none" ></div>
                         </div><!-- box_container-->   
                        <input type="hidden" name="sub_product_id[]"  />
                    </td>
                    <td data-title="<?=$CMS->lang['p_sku'];?>" class="grid-td" for="dgrid-3" >
                      <figure class="text_r">
                         <input name ="sub_product_sku[]" class="form-control" for="dgrid-3" /> 
                      </figure>  
                    </td>
                    <td  data-title="<?=$CMS->lang['pg_description'];?>" class="grid-td" for="dgrid-4" check="chtd" >
                      <figure class="text_r">
                         <textarea rows="4" for="dgrid-4" name ="sub_product_description[]" class="form-control" placeholder="<?=$CMS->lang['pg_description'];?>"> </textarea>
                      </figure>  
                    </td>
                    <td data-title="<?=$CMS->lang['stock_store_quantity'];?>"  class="grid-td class_qty" for="dgrid-5" > <input name ="sub_product_quantity[]"  onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this);" value="1" class="form-control quan_list" type="number" for="dgrid-5" /> 
                    </td>
                    <td data-title="<?=$CMS->lang['title_color'];?>"  class="grid-td class_color" for="dgrid-15" >
                      <figure class="text_r">
                         <input name ="sub_color[]" value="" class="form-control" onclick="choose_color(this);" type="text" for="dgrid-15" autocomplete="off" /> 
                      </figure>  
                    </td>
                    <td data-title="<?=$CMS->lang['price_no_vat'];?>" class="grid-td" for="dgrid-6" > 
                      <figure class="text_r">
                        <input name ="sub_product_price[]" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);"  value="" class="form-control" for="dgrid-6" /> 
                         </figure>  
                    </td>
                    <td data-title="<?=$CMS->lang['vat_percent'];?>" class="grid-td" for="dgrid-7" >  
                      <figure class="text_r">
                        <input for="dgrid-7"  class="form-control" name ="sub_product_tax[]" />
                      </figure>    
                    </td>
                    <td class="trash"   for="dgrid-8"><span class="del_row_product text_r"><i class="fa fa-trash" aria-hidden="true"></i></span></td>
                </tr>
                <? }}}else { $key = 1;?>
                 <tr class="row-grid" rowtr="last-row">
                    <td scope="row"><i class="hidden-sm-down fa fa-th handle"></i></td>
                  <? if ($_SESSION['is_mobile'] == false) { ?>
                    <td data-title="<?=$CMS->lang['stt'];?>" class="grid-td" for="dgrid-1" check="chtd"><span class="number">#<?=$key;?></span></td>
                  <? } ?>
                  <td data-title="<?=$CMS->lang['product_service'];?>" class="grid-td" for="dgrid-2">
                    <div class="box_container"> 
                      <figure class="text_r">
                          <input type="text" for="dgrid-2" check="chtd" class="form-control find_product " autocomplete="off" name ="sub_product_name[]" onfocusout="return convert_sku_code(this);" value=""/> 
                      </figure> 
                      <div class="box_result_find"  style="display:none" ></div>
                    </div><!-- box_container-->   
                    <input type="hidden" name="sub_product_id[]"  />
                  </td>
                  <td data-title="<?=$CMS->lang['p_sku'];?>"  class="grid-td" for="dgrid-3" >
                    <figure class="text_r">
                       <input name ="sub_product_sku[]" value="<?=$value['product_sku'];?>" class="form-control"    for="dgrid-3" /> 
                    </figure>  
                  </td>
                  <td data-title="<?=$CMS->lang['pg_description'];?>" class="grid-td" for="dgrid-4" check="chtd" > 
                    <figure class="text_r"> 
                       <textarea rows="4" for="dgrid-4" name ="sub_product_description[]" class="form-control" placeholder="<?=$CMS->lang['pg_description'];?>"></textarea>
                    </figure> 
                  </td>
                  <td data-title="<?=$CMS->lang['stock_store_quantity'];?>" class="grid-td class_qty" for="dgrid-5" >
                    <figure class="text_r"> 
                       <input name ="sub_product_quantity[]"  onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this);" value="1" class="form-control quan_list" type="number" for="dgrid-5" /> 
                    </figure>  
                  </td>
                  <td data-title="<?=$CMS->lang['title_color'];?>"  class="grid-td class_color" for="dgrid-15" >
                    <figure class="text_r">
                       <input name ="sub_color[]" value="" class="form-control" onclick="choose_color(this);" type="text" for="dgrid-15" autocomplete="off" /> 
                    </figure>  
                  </td>
                  <td data-title="<?=$CMS->lang['price_no_vat'];?>" class="grid-td" for="dgrid-6" > 
                    <figure class="text_r">
                      <input name ="sub_product_price[]" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);"  value="" class="form-control" for="dgrid-6" /> 
                    </figure>   
                  </td>
                  <td data-title="<?=$CMS->lang['vat_percent'];?>" class="grid-td" for="dgrid-7" >  
                    <figure class="text_r">
                      <input type="text" for="dgrid-7" class="form-control" name ="sub_product_tax[]"   >
                    </figure>    
                  </td>
                  <td class="trash" for="dgrid-8"><span class="del_row_product text_r"><i class="fa fa-trash" aria-hidden="true"></i></span></td>
                </tr>
        <? } ?>
                </tbody>
            </table>
          <div class="col-xl-12" style="margin-top: 20px;">
            <!--div class="checkbox">
              <input type="checkbox" id="create_other" class="internet" value="1" name="create_other">
              <label for="create_other"><?=$CMS->lang['title_create_similar_products'];?></label>
            </div-->
          </div>
      </figure>
   </section> 

  <section class="add_cart_footer">
<?=$CMS->global->footer_back($tpl->url_back);?>
<?=$tpl->footer_button;?>
    </section>
    <input type="hidden" value="0" name="add_product_option"  />
 </form>
 <? if ($_SESSION['is_mobile'] == true) { ?>
    <script>
     var table_item = $('#table_item').DataTable({
          'columnDefs': [
             {
                'targets': [1, 2, 3, 4, 5],
                'render': function(data, type, row, meta){
                   if(type === 'display'){
                      var api = new $.fn.dataTable.Api(meta.settings);

                      var el = $('input, select, textarea', api.cell({ row: meta.row, column: meta.col }).node());
                      var _html = $(data).wrap('<div/>').parent();
                    if(el.prop('tagName') === 'INPUT'){
                         $('input', _html).attr('value', el.val());
                       
                         if(el.prop('checked')){
                            $('input', _html).attr('checked', 'checked');
                         }
                      } else if (el.prop('tagName') === 'TEXTAREA'){
                         
                         $('textarea', _html).html(el.val());

                      } else if (el.prop('tagName') === 'SELECT'){
                        
                        // $('option:selected', _html).removeAttr('selected');
                         $('option', _html).filter(function(){
                            return ($(this).attr('value') === el.val());
                         }).attr('selected', 'selected');
                      }
                      data = _html.html();
                   }

                   return data;
                }
             }
          ],
          'responsive': true,
           order: [],
           paging: false,
                  searching: false,
                  info: false

       });

       $('#table_item tbody').on('keyup change', '.child input, .child select, .child textarea', function(){
           var el = $(this);
           var rowIdx = el.closest('ul').data('dtr-index');
           var colIdx = el.closest('li').data('dtr-index');
           var cell = table_item.cell({ row: rowIdx, column: colIdx }).node();
          //   $('input, select, textarea', cell).val(el.val());
           $(cell).children().children().val(el.val());
           if(el.is(':selected')){ $ (cell).children().children().find("option[value='"+el.val()+"']").prop('selected', true); }
             calculate_money_subitem_product();
       });

</script>
      <? } ?> 

<script>  
$(document).ready(function(){
  validate_form_custom("#form-signin_v1",".act_submit_save");
  var list_type = "<?=$tpl->product_option;?>";
  // Checked type real
  var list_id = list_type.split(",");
  var check_pc = "<?=$tpl->check_is_pc;?>";

  for(var x in list_id)
  {
    $("input[name='p_product_option[]'][value='"+list_id[x]+"']").prop("checked", true);
  }

  // Choose color
  $("input[name='color']").ColorPicker({
  color: '#0000ff',
  onShow: function (colpkr) {
    $(colpkr).fadeIn(500);
    return false;
  },
  onHide: function (colpkr) {
    $(colpkr).fadeOut(500);
    return false;
  },
  onChange: function (hsb, hex, rgb) {
    $('#colorSelector div').css('backgroundColor', '#' + hex);
    $("input[name='color']").val('#' + hex);
  },onSubmit: function(hsb, hex, rgb, el) {
    $(el).val('#' + hex);
    $(el).ColorPickerHide();
  },
});


  $(".class_color").hide();
  $("#type_form_2").click(function(){
      $(".class_qty").hide();
      $(".class_color").show();
      $(".box_container").find("input[for='dgrid-2']").removeClass("find_product");
      var pid= $(this).attr("pid");
      loadListProduct(pid, 2);
  });

  $("#type_form_1").click(function(){
      $(".class_qty").show();
      $(".class_color").hide();
      $(".box_container").find("input[for='dgrid-2']").addClass("find_product");
      var pid= $(this).attr("pid");
      loadListProduct(pid, 1);
  });

  // Check has product child 
  if(parseInt(check_pc) > 0)
  {
    $("#type_form_2").trigger("click");
  }

  // Xoá row
  $("#data_table").on("click",".del_row_product",function(){
    var onthis = $(this);
    swal({
          title: 'Bạn chắc chắn?',
          text: "Bạn chắc chắn muốn loại bỏ sản phẩm con này?",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Ok'
        }).then(function () {
            waitingDialog.show(cms_lang.waiting_dialog_msg);

            var obj = onthis.parent().parent();
            var count_del = $("#data_table tr").length;
            var id_product = onthis.attr("id_product");
            var parent_id = "<?=$CMS->input['id'];?>";
            if(count_del > 1)
            {
              onthis.parent().parent().remove();
              // Update product
              if(typeof(id_product) != "undefined" && parent_id != "")
              {
                $.ajax({
                    type: "post",
                    url: site_root_domain+ "/?site=product&subact=rm_parent_id",
                    data: {product_id: id_product, parent_id: parent_id},
                    success: function(response) {
                      
                    }
                  });
              }
              // insert number
              var count_check = 1;
              var inc_item = 0;
              $("#data_table tr").each(function()
              {
                  onthis.find(".number").html("#"+count_check);
                  onthis.find(".item_id").val(inc_item);
                  onthis.find(".del_row_product").attr("item_id",inc_item);
                  count_check ++;
                  inc_item ++;
                  if(count_check == count_del)
                  {
                    onthis.attr("rowtr","last-row");
                  }
                  
              });

            }else
            {
              var row_c = onthis.parents(".row-grid");
              row_c.find("input[for='dgrid-2']").val("");
              row_c.find("input[for='dgrid-3']").val("");
              row_c.find("textarea[for='dgrid-4']").val("");
              row_c.find("input[for='dgrid-5']").val(1);
              row_c.find("input[for='dgrid-6']").val("");
              row_c.find("input[for='dgrid-7']").val("");
              row_c.find("input[for='dgrid-15']").val("");

            }
            
            waitingDialog.hide();
            swal( 'Thành công!', 'Bạn đã loại bỏ sản phẩm con này', 'success');
        });
  });// end del_row_product

});
  var module_name = "product_module"; 
  var lang_supplier_add = "<?=$CMS->lang['supplier_add'];?>";
  var lang_supplier_edit = "<?=$CMS->lang['supplier_edit'];?>";

  var lang_manufacture_edit  = "<?=$CMS->lang['manufacture_edit'];?>";
  var lang_manufacture_add = "<?=$CMS->lang['manufacture_add'];?>";
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

  function loadListProduct(pid, type) 
  {
    $.ajax({
          type: "post",
          url: site_root_domain + "/?site=product&subact=get_product_child",
          data: {product_id: pid, type: type},
          success: function(response)
          {
              var obj = JSON.parse(response);
              // console.log(obj);

              var html_load = "";
              var key = 1;
              var p_id = "";
              var display_qty = "display: table-cell";
              var display_color = "display: none";
              var lang = "<?=$CMS->vars['default_language'];?>";
              var class_findp = "find_product";
              var count_arr = obj.length;
              var class_lastrow= "";
              for(var x in obj)
              {
                var attr = JSON.parse(obj[x].product_attribute);
                // console.log(obj[x].product_attribute);
                // console.log(attr);
                if(type==2) { p_id=obj[x].product_id; display_color="display: table-cell"; display_qty="display: none"; class_findp=""; }
                if(key == count_arr) { class_lastrow = "last-row"; }
              html_load += `
                <tr class="row-grid" rowtr="`+class_lastrow+`" checkrow="normal">
                    <td scope="row"><i class="hidden-sm-down fa fa-th handle"></i></td> 
                    <? if ($_SESSION['is_mobile'] == false) { ?>
                     <td scope="row" data-title="<?=$CMS->lang['stt'];?>" class="grid-td" for="dgrid-1" check="chtd"><span class="number">#`+key+`</span></td>
                    <? } ?>
                    <td data-title="<?=$CMS->lang['product_service'];?>" class="grid-td" for="dgrid-2">
                       <div class="box_container">  
                        <figure class="text_r">
                            <input type="text"  for="dgrid-2"  check="chtd"  class="form-control `+class_findp+` " autocomplete="off"  name ="sub_product_name[]" onfocusout="return convert_sku_code(this);" value="`+obj[x].product_name[lang]+`"/> 
                           </figure>  
                            <div class="box_result_find" style="display:none" ></div>
                         </div><!-- box_container-->   
                        <input type="hidden" name="sub_product_id[]"  />
                    </td>
                    <td data-title="<?=$CMS->lang['p_sku'];?>" class="grid-td" for="dgrid-3" >
                      <figure class="text_r">
                         <input name ="sub_product_sku[]" class="form-control" for="dgrid-3" value="`+obj[x].product_sku+`" /> 
                      </figure>  
                    </td>
                    <td  data-title="<?=$CMS->lang['pg_description'];?>" class="grid-td" for="dgrid-4" check="chtd" >
                      <figure class="text_r">
                         <textarea rows="4" for="dgrid-4" name ="sub_product_description[]" class="form-control" placeholder="<?=$CMS->lang['pg_description'];?>">`+obj[x].product_description[lang]+`</textarea>
                      </figure>  
                    </td>
                    <td data-title="<?=$CMS->lang['stock_store_quantity'];?>" class="grid-td class_qty" for="dgrid-5" style="`+display_qty+`" > <input name ="sub_product_quantity[]" onkeypress="return check_enter_number(event,this);" value="`+obj[x].product_quantity+`" class="form-control quan_list" type="number" for="dgrid-5" /> 
                    </td>
                    <td data-title="<?=$CMS->lang['title_color'];?>" style="`+display_color+`" class="grid-td class_color" for="dgrid-15" >
                      <figure class="text_r">
                         <input name ="sub_color[]" value="`+attr.color+`" class="form-control" onclick="choose_color(this);" type="text" for="dgrid-15" autocomplete="off" /> 
                      </figure>  
                    </td>
                    <td data-title="<?=$CMS->lang['price_no_vat'];?>" class="grid-td" for="dgrid-6" > 
                      <figure class="text_r">
                        <input name ="sub_product_price[]" onkeypress="return check_enter_number(event,this);"  value="`+obj[x].product_price_sell+`" class="form-control" for="dgrid-6" /> 
                         </figure>  
                    </td>
                    <td data-title="<?=$CMS->lang['vat_percent'];?>" class="grid-td" for="dgrid-7" >  
                      <figure class="text_r">
                        <input for="" class="form-control" name ="sub_product_tax[]" value="`+obj[x].product_tax+`" />
                      </figure>    
                    </td>
                    <td class="trash" for="dgrid-8"><span id_product="`+p_id+`" class="del_row_product text_r"><i class="fa fa-trash" aria-hidden="true"></i></span></td>
                </tr>
              `;
              key ++;
              }

              if(html_load)
              {
                $("table tbody#data_table").html(html_load);
              }else
              {
                var row_c = $("table tbody#data_table");
                row_c.find("input[for='dgrid-2']").val("");
                row_c.find("input[for='dgrid-3']").val("");
                row_c.find("textarea[for='dgrid-4']").val("");
                row_c.find("input[for='dgrid-5']").val(1);
                row_c.find("input[for='dgrid-6']").val("");
                row_c.find("input[for='dgrid-7']").val("");
                row_c.find("input[for='dgrid-15']").val("");
              }
          }
      });
  }


</script>
 <script src="<?=$CMS->vars['js_acp'];?>/product.js"></script>
 
    <?=\core\ezy::render("add_supplier", "product");?>
    <?=\core\ezy::render("add_manufacture", "product");?>
    <?=\core\ezy::render("add_product_group", "product");?>

