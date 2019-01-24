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
                    <input class="form-control" autocomplete="off" type="text" name="p_name[<?=$langCode;?>]" id="p_name_<?=$langCode;?>" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['p_name_err'];?>" value="<?=$tpl->p_name[$langCode];?>">
                  </div>
                  </fieldset>
                </div>
            <script>
                $.ajax({
                    type: "get",
                    url: site_root_domain + "/?site=product&subact=autocomplete_data&lang=<?=$langCode;?>",
                    dataType: "json",
                    success: function(data){
                        $("#p_name_<?=$langCode;?>").typeahead({ source:data, autoSelect:false });
                    }
                })
            </script>
        <? } }else {?>

                <div class="col-xl-6">
                  <fieldset class="form-group">
                  <label class="form-label" ><?=$CMS->lang['p_name'];?> <span style="color:red">(*)</span></label>
                  <div class="form-control-wrapper">
                    <input class="form-control" autocomplete="off" type="text" name="p_name" id="p_name" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['p_name_err'];?>" value="<?=$tpl->p_name;?>">
                  </div>
                  </fieldset>
                </div>
                <script>
                    $.ajax({
                        type: "get",
                        url: site_root_domain + "/?site=product&subact=autocomplete_data",
                        dataType: "json",
                        success: function(data){
                            $("#p_name").typeahead({ source:data, autoSelect:false });
                        }
                    })
                </script>
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
               
                <div class="drop-zone fileinput-button drop-zone-wrap <?=$tpl->src_image_upload ? 'exist-preview' : '';?>" style="height: 100% !important">
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
        </div>
        <div class="col-xl-6">
          <fieldset class="form-group">
          <label class="form-label" ><?=$CMS->lang['p_order'];?></label>
            <input class="form-control" min="0" type="number" name="p_order" id="p_order" value="<?=$tpl->p_order;?>" />
          </fieldset>     
        </div>
      </div>

      <div class="row">
          <div class="col-xl-12"><h5 class="m-t-lg with-border"><?=$CMS->lang['title_info_transaction'];?></h5></div>

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
          
          <div class="col-xl-6" style="display: <?=$tpl->css_show;?>">
            <fieldset class="form-group mb0">
              <label class="form-label" ><?=$CMS->lang['title_suffix_price'];?></label>
              <input name="p_up" id='p_up' class="form-control" value="<?=$tpl->p_up;?>" />
            </fieldset>
          </div>
        </div>
        <div class="row" style="display: <?=$tpl->css_show;?>">
          <div class="col-xl-6">
            <fieldset class="form-group">
                <label class="form-label" ><?=$CMS->lang['staff_id'];?></label>
                <div class="form-control-wrapper">
                    <select class="form-control select2" name="staff_id[]" id="staff_id" multiple="multiple">
                        <?=$tpl->option_staff;?>
                    </select>
                </div>
            </fieldset> 
          </div>
        </div>
    </div><!-- col-md-6 -->

    <script type="text/javascript">
      $(document).ready(function(){
        var setting = [];
        tinymce.init({
            height: 200,
            selector: ".texarea_des",
            plugins: "code",
            toolbar: "newdocument | bold | italic | underline | strikethrough | alignleft | aligncenter | alignright | alignjustify | styleselect | formatselect | fontselect | fontsizeselect | cut | copy | paste | bullist | numlist | outdent | indent | blockquote | undo | redo | removeformat | subscript | superscript | code",
            fontsize_formats: "8px 10px 12px 14px 16px 18px 20px 22px 24px 26px 28px 30px 32px 34px 36px 40px",
            menubar: false,
            convert_urls: true,
            relative_urls: false,
        });
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
        
       <fieldset class="form-group mb0">
        <div class="row">
          <div class="col-lg-6">
            <label class="form-label" ><?=$CMS->lang['p_show'];?></label>
            <?=$tpl->option_p_show;?>
          </div>
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

  <section class="add_cart_footer">
<?=$CMS->global->footer_back($tpl->url_back);?>
<?=$tpl->footer_button;?>
    </section>
    <input type="hidden" value="0" name="add_product_option"  />
 </form>
  

<script>  
$(document).ready(function(){
  validate_form_custom("#form-signin_v1",".act_submit_save");
});
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

