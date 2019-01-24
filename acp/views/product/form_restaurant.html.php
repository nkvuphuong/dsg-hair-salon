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
               
                <div class="drop-zone fileinput-button drop-zone-wrap <?=$tpl->src_image_upload ? 'exist-preview' : '';?>">
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
                    <label for='check-1'><?=$CMS->lang['food_option_1'];?></label>
                </div>
                <div class='checkbox'>
                    <input type='checkbox' name='p_product_option[]' value='4' id='check-4'  >
                    <label for='check-4'><?=$CMS->lang['food_option_4'];?></label>
                </div>
                <div class='checkbox'>
                    <input type='checkbox' name='p_product_option[]' value='5' id='check-5'  >
                    <label for='check-5'><?=$CMS->lang['food_option_5'];?></label>
                </div>
            </div>
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
          <div class="col-xl-6">
            <fieldset class="form-group mb0">
              <label class="form-label" ><?=$CMS->lang['title_suffix_price'];?></label>
              <input name="p_up" id='p_up' class="form-control" value="<?=$tpl->p_up;?>" />
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
</script>
 <script src="<?=$CMS->vars['js_acp'];?>/product.js"></script>
 
    <?=\core\ezy::render("add_supplier", "product");?>
    <?=\core\ezy::render("add_manufacture", "product");?>
    <?=\core\ezy::render("add_product_group", "product");?>

