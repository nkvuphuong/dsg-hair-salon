<? $pg_type = 0;
        $category = $CMS->product_group->getParent_bytype($pg_type);
        $option_pg_parent = "<option value=''>{$CMS->lang['select']}</option>";
        foreach ($category as $c) {
            if ($c['product_group_id'] == $pg_parent) {
                $option_pg_parent .= "<option value='{$c['product_group_id']}' selected>{$c['product_group_name']}</option>";
            } else {
                $option_pg_parent .= "<option value='{$c['product_group_id']}' >{$c['product_group_name']}</option>";
            }
        }

        $pg_type = 0;
        for ($i = 0; $i <= 1; $i++) {
            if ($i == $pg_type) {
                $option_pg_type .= "
     <div class='radio w25'><input type='radio' checked  name='pg_type' id='radio-{$i}' value='{$i}'><label for='radio-{$i}'>{$CMS->lang['pg_type_'.$i]}</label></div>  

        ";
            } else {
                $option_pg_type .= "  <div class='radio w25'><input type='radio' name='pg_type' id='radio-{$i}' value='{$i}'><label for='radio-{$i}'>{$CMS->lang['pg_type_'.$i]}</label></div>  ";
            }
        }


        $pg_status = 1;
        for ($i = 1; $i >= 0; $i--) {
            if ($i == $pg_status) {
                $option_pg_status .= "   <div class='radio w25'><input type='radio' checked  name='pg_status' id='radio-status-{$i}' value='{$i}'><label for='radio-status-{$i}'>{$CMS->lang['pg_statupg_0'.$i]}</label></div>";
            } else {
                $option_pg_status .= "   <div class='radio w25'><input type='radio'    name='pg_status' id='radio-status-{$i}' value='{$i}'><label for='radio-status-{$i}'>{$CMS->lang['pg_statupg_0'.$i]}</label></div>";
            }
        }
?>        

    <div id="box_add_group_product" class="popup_add_group_product mfp-hide">
      <p class="title_group_product  " style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;"><?=$CMS->lang['group_product_add'];?>...</p>
      <p style="color:red" class="pgmanu_error_msg"></p>
      <form id="add_group_product_form" name="add_group_product_form">
          <input type="hidden" name="checkReturn" value="">
        <ul class="list_field_supplier">    
           <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
              <fieldset class="form-group">
                <label class="form-label" ><?=$CMS->lang['pg_parent'];?></label>
                    <select class="form-control select2" name="pg_parent" id="pg_parent" >
                      <?=$option_pg_parent;?>
                    </select>
                  
              </fieldset>
            </li>
            <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12"> 
              <fieldset class="form-group">
                <label class="form-label" ><?=$CMS->lang['pg_name'];?> <span style="color:red">(*)</span></label>
                    <input class="form-control" type="text" name="pg_name" id="pg_name" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['pg_name_err'];?>" value="<?=$pg_name;?>">
                  
              </fieldset>
            </li>
            <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12"> 
              <fieldset class="form-group">
                <label class="form-label" ><?=$CMS->lang['pg_code'];?></label>
              
                    <input class="form-control " type="text" name="pg_code" id="pg_code" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['pg_code_err'];?>" value="<?=$pg_code;?>">
                  
              </fieldset>
            </li>
            <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12"> 
               <fieldset class="form-group">
                <label class="form-label" ><?=$CMS->lang['pg_description'];?></label>
          
                    <textarea rows="3" class="form-control" name="pg_description" id="pg_description" ><?=$pg_description;?></textarea> 
                 
              </fieldset>
            </li>
            <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
               <fieldset class="form-group">
                <label class="form-label" ><?=$CMS->lang['pg_type'];?></label>
                     
                      <?=$option_pg_type;?>

              </fieldset>
            </li>
            <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12"> 
              <fieldset class="form-group">
                <label class="form-label" ><?=$CMS->lang['pg_status'];?></label>   
                      <?=$option_pg_status;?>
                
              </fieldset>
            </li>
            <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12"> 
              <fieldset class="form-group">
                <label class="form-label" ><?=$CMS->lang['pg_avartar'];?></label>

                   
                        <div class="drop-zone fileinput-button">
                                        <img id="upload_img_show" width="205" />

                                        <i class="font-icon font-icon-cloud-upload-2"></i>
                                         <div class="drop-zone-caption">Drag file to upload</div>
                                           <input type="file"  name="pg_avartar" id="ufile" accept="image/*">
                                   </div><!--.drop-zone-->
                                 <div class="actionButtons">
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
                        <input  type="hidden" id="ufile_output_b64" name="base64_image" />
                        </div>    
                                  
                           

              </fieldset>
              </li>
              <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="min-height: auto;">
                <fieldset class="form-group">
                  <div class="typeahead-field"> 
                    <span class="typeahead-query change_action_product_group"><input class="btn btn_add_product_group" type="button" value="Thêm nhóm sản phẩm"></span>
                  </div>
                </fieldset>
              </li> 

        </ul>
          
        <input name="product_group_id" type="hidden" value="" />

      </form>
    </div>  

     <script src="<?=$CMS->vars['js_acp'];?>/product_group.js"></script>