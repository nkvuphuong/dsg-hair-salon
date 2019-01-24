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
              <label class="form-label" ><?=$CMS->lang['p_sku'];?></label>
              <input class="form-control" type="text" name="p_sku" id="p_sku" value="<?=$tpl->p_sku;?>">
            </fieldset>
          </div>
          <div class="col-xl-6">
              <fieldset class="form-group">
                <label class="form-label" ><?=$CMS->lang['p_img_alt'];?></label>
                  <input class="form-control" type="text" name="p_img_alt" id="p_img_alt" value="<?=$tpl->p_img_alt;?>">
              </fieldset>
          </div>
          <div class="col-xl-6">
             <fieldset class="form-group">
              <label class="form-label" ><?=$CMS->lang['p_barcode'];?></label>
              <input class="form-control " type="text" name="p_barcode" id="p_barcode" value="<?=$tpl->p_barcode;?>">
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
                <?if(\core\ezy::$theme_key == "nms") {?>
                <div class='checkbox'>
                  <input type='checkbox' name='p_product_option[]' value='7' id='check-7'  >
                  <label for='check-7'><?=$CMS->lang['product_option_7'];?></label>
                </div>
                <?}?>
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
          </div>

          <div class="col-xl-6">
            <fieldset class="form-group">
              <label class="form-label" ><?=$CMS->lang['p_price'];?></label>
                <input class="form-control " type="text" name="p_price" id="p_price" value="<?=$tpl->p_price;?>"  onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
            </fieldset>     
          </div>

          <div class="col-xl-6">
            <fieldset class="form-group">
              <label class="form-label" ><?=$CMS->lang['p_price_old'];?></label>
              <input class="form-control " type="text" name="p_price_old" id="p_price_old" value="<?=$tpl->p_price_old;?>"  onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
            </fieldset>     
          </div>

          <div class="col-xl-6">
            <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['p_price_original'];?></label>
              <input class="form-control" type="text" name="p_price_original" id="p_price_original" value="<?=$tpl->p_price_original;?>"  onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
            </fieldset>       

          </div>
          <div class="col-xl-6">
            <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['p_price_sell'];?></label>
              <input class="form-control " type="text" name="p_price_sell" id="p_price_sell" value="<?=$tpl->p_price_sell;?>" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
            </fieldset> 
          </div>
          <div class="col-xl-6" style="display: <?=$tpl->css_hide;?>">
            <fieldset class="form-group mb0">
              <label class="form-label" ><?=$CMS->lang['p_tax'];?></label>
                <input type="text" onkeypress="return check_enter_number(event,this);" name="p_tax" class="form-control" placeholder="Unit: %" value="<?=$tpl->p_tax;?>">
            </fieldset>           
          </div>
         
        </div>
        
    </div><!-- col-md-6 -->
    
    
    <div class="col-md-6" >
       <fieldset class="form-group">
        <div class="row">
          <div class="col-lg-6">
            <label class="form-label pull-left" ><?=$CMS->lang['p_manufacture'];?></label> 
            <? if($CMS->permit["manufacture_add"] == 1) { ?>
                <a   data-size="s" class="add_new_manufacture pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline">
                  <i class="fa fa-plus" aria-hidden="true"></i> 
                </a>
            <? } ?>
            <? if($CMS->permit["manufacture_edit"] == 1) { ?>
            <span class="box_edit_manufacture pull-right" style="margin-left:10px"></span>
            <? } ?>
              <select class="form-control select_manufacture auto_select" name="p_manufacture" id="p_manufacture" defaultvalue="<?=$tpl->p_manufacture;?>" >
                <option value=''><?=$CMS->lang['select'];?></option>
              <? foreach ($tpl->manufacture as $m) { ?>
                <option value='<?=$m['manufacture_id'];?>'><?=$m['manufacture_name'];?></option>
              <? } ?>
              </select>
              
          </div><!-- col-lg-6-->

          <?if(\core\ezy::$theme_key != "nms") {?>
          <div class="col-xl-6 col-lg-12">
            <label class="form-label pull-left" ><?=$CMS->lang['p_supplier'];?></label>
            <? if ($CMS->permit["supplier_add"] == 1) { ?>
              <a   data-size="s" class="add_new_supplier pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i>  </a>
            <? } ?>
            <? if ($CMS->permit["supplier_edit"] == 1) { ?>
            <span class="box_edit_supplier pull-right" style="margin-left:10px"></span>
            <? } ?>
            <select class="form-control select_supplier auto_select" name="p_supplier" id="p_supplier" for="change" defaultvalue="<?=$tpl->p_supplier;?>" >
                <option value=''><?=$CMS->lang['select'];?></option>
                <? foreach ($tpl->supplier as $s) { ?>
                    <option value='<?=$s['supplier_id'];?>'><?=$s['supplier_name'];?></option>
                <? } ?>
            </select>
          </div> 
          <?}?>
      </div>  <!-- col-lg-6-->
          <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['p_guarantee'];?></label>
            <div class="form-control-wrapper">  
              <input class="form-control" maxlength="3" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" type="text" name="p_guarantee" id="p_guarantee" value="<?=$tpl->p_guarantee;?>"/>
            </div> 
        </fieldset>
    </fieldset>
  </div>
  <script type="text/javascript">
    $(document).ready(function(){
      var setting = [];
      setting['height'] = 200;
      setting['selector'] = ".texarea_des";
      tinyMCEInit(setting);
    })
  </script>
    <div class="col-md-6" >
        <? if($CMS->vars['translations']) {
            foreach ($CMS->vars['translations'] as $langCode => $langName) { ?>
                <fieldset class="form-group langTab" lang="<?=$langCode;?>">
                  <label class="form-label" ><?=$CMS->lang['p_description'];?></label>
                    <textarea name="p_description[<?=$langCode;?>]" rows="5" class="form-control texarea_des"><?=$tpl->p_description[$langCode];?></textarea>
                </fieldset>
        <? } } else {?>
                <fieldset class="form-group">
                  <label class="form-label" ><?=$CMS->lang['p_description'];?></label>
                    <textarea name="p_description" rows="5" class="form-control texarea_des"><?=$tpl->p_description;?></textarea>
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
        
       <fieldset class="form-group mb0">
        <div class="row">
          <div class="col-lg-5">
            <label class="form-label" ><?=$CMS->lang['p_show'];?></label>
            <?=$tpl->option_p_show;?>
          </div>
          <?if(\core\ezy::$theme_key == "nms") {?>
          <div class="col-lg-7">
            <label class="form-label" ><?=$CMS->lang['p_stock_available'];?></label>
            <?=$tpl->option_p_stock_available;?>
          </div>
          <?}?>
        </div><!-- row -->
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
                                    <?=$CMS->lang['p_information_1'];?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#p_information_2_<?=$langCode;?>" role="tab" data-toggle="tab">
                                    <?=$CMS->lang['p_information_2'];?>
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
                                    <?=$CMS->lang['p_information_1'];?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#p_information_2" role="tab" data-toggle="tab">
                                    <?=$CMS->lang['p_information_2'];?>
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
  var list_type = "<?=$tpl->product_option;?>";
  // Checked type real
  var list_id = list_type.split(",");
  var check_pc = "<?=$tpl->check_is_pc;?>";

  for(var x in list_id)
  {
    $("input[name='p_product_option[]'][value='"+list_id[x]+"']").prop("checked", true);
  }


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
</script>
<script src="<?=$CMS->vars['js_acp'];?>/product.js"></script>
 
    <?=\core\ezy::render("add_supplier", "product");?>
    <?=\core\ezy::render("add_manufacture", "product");?>
    <?=\core\ezy::render("add_product_group", "product");?>

