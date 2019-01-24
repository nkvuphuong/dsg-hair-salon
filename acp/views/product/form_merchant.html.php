<link rel="stylesheet" type="text/css" href='/acp/assets/css/orders.css'>
<div class="container-fluid order"><!-- Page Content -->
  <form id="form-signin_v1" name="form-signin_v1" action="<?=$tpl->action_form;?>" method="POST" enctype="multipart/form-data">
  <div class="manage-container main_form">
    <section class="tabs-section">
      <h5 class="top-section-title col-xl-12"><?=$CMS->lang['p_'.$CMS->input['act']];?></h5>
      <a class="btn_backlist" href="/acp/?site=<?=$CMS->input['site'];?>" title=""><span class="font-icon font-icon-del"></span></a>
      <div class="tabs-section-nav tabs-section-nav-inline order-tabs-section-nav">
        <ul class="nav" role="tablist">
          <li class="nav-item"><a class="nav-link active" href="#tabs-4-tab-1" role="tab" data-toggle="tab" aria-expanded="true"> <?=$CMS->lang['title_overview_product'];?> </a></li>
          <li class="nav-item"><a class="nav-link" href="#tabs-4-tab-2" role="tab" data-toggle="tab" aria-expanded="false"> <?=$CMS->lang['title_info_product'];?> </a></li>
          <li class="nav-item"><a class="nav-link" href="#tabs-4-tab-4" role="tab" data-toggle="tab" aria-expanded="false"> <?=$CMS->lang['p_gallery'];?> </a></li>

          <!-- <li class="nav-item"><a class="nav-link" href="#tabs-4-tab-3" role="tab" data-toggle="tab" aria-expanded="false"> <?=$CMS->lang['title_attribute'];?> & <?=$CMS->lang['title_shipping'];?> </a></li>
          <li class="nav-item"><a class="nav-link" href="#tabs-4-tab-5" <?=$tpl->hidden_create_child_product;?> role="tab" data-toggle="tab" aria-expanded="false"> <?=$CMS->lang['title_create_child_product'];?> </a></li>
          <li class="nav-item"><a class="nav-link" href="#tabs-4-tab-6" <?=$tpl->hidden_create_child_product;?> role="tab" data-toggle="tab" aria-expanded="false"> Variants </a></li> -->
          
          <li class="nav-item"><a class="nav-link" href="#tabs_shipping_fee" role="tab" data-toggle="tab" aria-expanded="false"><?=$CMS->lang['title_ship_fee'];?></a></li>
        </ul>
      </div>
      <!--.tabs-section-nav-->
      <div class="tab-content order-tab-content">

        <!-- tabs-4-tab-1 -->
        <div role="tabpanel" class="tab-pane fade in active show" id="tabs-4-tab-1" aria-expanded="true">
          <div class="row"><?=$CMS->global->languageTab('langTab');?></div>
          <!--.tabs-section-nav-->
              <div class="box-typical box-typical-info" style="min-height: 500px;padding: 0 15px 15px;">
                <h5 class="m-t-md with-border"><?=$CMS->lang['title_overview_product'];?></h5>
                <div class="row">
                  <div class="col-md-4">
                    <? if($CMS->vars['translations']) { ?>
                      <? foreach ($CMS->vars['translations'] as $langCode => $langName) { ?>
                          <fieldset class="form-group langTab" lang="<?=$langCode;?>">
                            <label class="form-label" ><?=$CMS->lang['p_name'];?> <span style="color:red">(*)</span></label>
                            <div class="form-control-wrapper">
                              <input class="form-control " type="text" name="p_name[<?=$langCode;?>]" id="p_name[<?=$langCode;?>]" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['p_name_err'];?>" value="<?=$tpl->p_name[$langCode];?>">
                            </div>
                          </fieldset>
                    <? } }else {?>
                          <fieldset class="form-group">
                            <label class="form-label"><?=$CMS->lang['p_name'];?> <span style="color:red">(*)</span></label>
                            <div class="form-control-wrapper">
                              <input class="form-control " type="text" name="p_name" id="p_name" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['p_name_err'];?>" value="<?=$tpl->p_name;?>">
                            </div>
                          </fieldset>
                    <? } ?>
                  </div>
                  <div class="col-md-4">
                    <fieldset class="form-group">
                      <label class="form-label"><?=$CMS->lang['p_product_group'];?> <span style="color:red">(*)</span></label>
                      <div class="form-control-wrapper">
                        <select class="form-control auto_select select_pg" onchange="changelistAttribute(this);" defaultvalue="<?=$tpl->p_product_group;?>" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['p_product_group_err'];?>" aria-hidden="true" tabindex="-1" name="p_product_group">
                          <?=$tpl->option_p_product_group;?>
                        </select>
                      </div>
                    </fieldset>
                  </div>
                  <div class="col-md-4">
                    <fieldset class="form-group">
                      <label class="form-label"><?=$CMS->lang['title_choose_product_parent'];?></label>
                      <input class="form-control" id="parent_id" onkeyup="autoProductSearch('#parent_id', '/acp/?site=product&subact=search_product', '[name=parent_id]');" type="text" name="parent_name" value="<?=$tpl->parent_name;?>" autocomplete="off">
                      <input class="form-control" type="hidden" id="<?=$tpl->parent_id;?>" name="parent_id">
                    </fieldset>
                  </div>
                </div>

                <div class="row">
                <div class="col-md-12">
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
                      });
                    })
                  </script>
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
                  </div>
                </div>

            <h5 class="m-t-md with-border"><?=$CMS->lang['title_info_transaction'];?></h5>
            <div class="row">
              <div class="col-xl-5 col-md-5">
                <div class="row">
                  <div class="col-xl-6 col-md-6">
                    <fieldset class="form-group">
                      <label class="form-label" ><?=$CMS->lang['p_cycle'];?></label>
                      <select class="form-control auto_select" name="p_cycle" id="p_cycle" defaultvalue="<?=$tpl->p_cycle;?>" >
                        <option value='0'><?=$CMS->lang['p_cycle_00'];?></option>
                        <option value='1'><?=$CMS->lang['p_cycle_01'];?></option>
                        <option value='2'><?=$CMS->lang['p_cycle_02'];?></option>
                      </select>
                    </fieldset>
                  </div>
                  <div class="col-xl-6 col-md-6">
                    <fieldset class="form-group mb0" style="display: <?=$tpl->css_hide;?>">
                      <label class="form-label" ><?=$CMS->lang['p_tax'];?></label>
                      <input type="text" onkeypress="return check_enter_number(event,this);" name="p_tax" class="form-control" placeholder="Unit: %" value="<?=$tpl->p_tax;?>" onfocusout="check_enter_number_max(this);" maxvalue="100">
                    </fieldset>
                  </div>
                </div>

                <?if( $CMS->vars['enabled_commission'] ){?>
                <fieldset class="form-group">
                  <label class="form-label" ><?=$CMS->lang['gcommission'];?></label>
                    <div class="input-group">
                      <div class="active_discount">
                          <input id="product_commission_value" name="product_commission_value" step="0.01" type="number" class="form-control product_commission_value inp_dis" value="<?=$tpl->product_commission_value;?>" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_max(this);" maxvalue="0">
                          <div onclick="changeType(this, '.b_gen', '#product_commission_type');changeAttr('#product_commission_value', 'maxvalue', '100');check_enter_number_max('#product_commission_value');" class="b_gen b_percent" value="0">%</div>
                          <div onclick="changeType(this, '.b_gen', '#product_commission_type');changeAttr('#product_commission_value', 'maxvalue', '0');check_enter_number_max('#product_commission_value');" class="b_gen b_currency" value="1"><?=$CMS->vars['currency_type'];?></div>
                      </div>
                      <input type="hidden" name="product_commission_type" value="<?=$tpl->product_commission_type;?>" id="product_commission_type">
                    </div>
                </fieldset> 
                <script type="text/javascript">
                  $(document).ready(function(){
                      var active_discount = isNaN(parseInt("<?=$tpl->product_commission_type;?>")) ? 0 : parseInt("<?=$tpl->product_commission_type;?>");
                      $(".b_gen[value='"+active_discount+"']").addClass("active").trigger('click');
                  });
                </script>
                <?}?>
              </div>
              <div class="col-xl-7 col-md-7">

                <?if( $CMS->vars['addon_goods_enable'] == 1 ){?>
                <div class="row">
                  <div class="col-xl-6 col-md-6">
                    <fieldset class="form-group">
                      <label class="form-label" >
                        <span><?=$CMS->lang['p_price'];?></span>
                        <span class="question">
                          <i class="fa fa-question-circle" aria-hidden="true"></i>
                          <p class="box_answer" style="display: none;"><?=$CMS->lang['p_price_note'];?></p>
                        </span>
                      </label>
                      <input class="form-control " type="text" name="p_price" id="p_price" value="<?=$tpl->p_price;?>"  onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
                    </fieldset>     
                  </div>
                  <div class="col-xl-6 col-md-6">
                    <fieldset class="form-group">
                      <label class="form-label" >
                        <span><?=$CMS->lang['p_price_original'];?></span>
                        <span class="question">
                          <i class="fa fa-question-circle" aria-hidden="true"></i>
                          <p class="box_answer" style="display: none;"><?=$CMS->lang['p_price_original_note'];?></p>
                        </span>
                      </label>
                      <input class="form-control" type="text" name="p_price_original" id="p_price_original" value="<?=$tpl->p_price_original;?>"  onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
                    </fieldset> 
                  </div>
                </div>
                <?}?>

                <div class="row">
                  <div class="col-xl-6 col-md-6">
                    <fieldset class="form-group">
                        <label class="form-label" >
                            <span><?=$CMS->lang['p_price_sell'];?></span>
                            <span class="question">
                          <i class="fa fa-question-circle" aria-hidden="true"></i>
                          <p class="box_answer" style="display: none;"><?=$CMS->lang['p_price_sell_note'];?></p>
                        </span>
                        </label>
                      <input class="form-control " type="text" name="p_price_sell" id="p_price_sell" value="<?=$tpl->p_price_sell;?>" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
                    </fieldset> 
                  </div>
                  <div class="col-xl-6 col-md-6">
                    <fieldset class="form-group">
                      <label class="form-label" >
                        <span><?=$CMS->lang['p_price_sale'];?></span>
                        <span class="question">
                          <i class="fa fa-question-circle" aria-hidden="true"></i>
                          <p class="box_answer" style="display: none;"><?=$CMS->lang['p_price_sale_note'];?></p>
                        </span>
                      </label>
                      <input class="form-control" type="text" name="p_price_sale" id="p_price_sale" value="<?=$tpl->p_price_sale;?>" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
                    </fieldset>     
                  </div>
                </div>
              </div>
            </div>

            <?if($CMS->vars['addon_goods_enable'] == 1 AND $tpl->p_type == 0) { ?>
              <h5 class="m-t-md with-border"><?=$CMS->lang['title_store_config'];?></h5>
                  <div class="row">
              
                   <div class="col-md-4">
                      <fieldset class="form-group">
                        <div style="display: flex;">
                          <label class="form-label" for="store_id" style="flex: 1;"><?=$CMS->lang['store_id'];?></label>
                          
                          <?if( $CMS->permit["store_add"] == 1 ){?>
                          <a data-size="s" class="add_new_store pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline pointer"><i class="fa fa-plus" aria-hidden="true"></i></a>
                          <?}?>

                          <?if( $CMS->permit["store_edit"] == 1 ){?>
                          <span class="box_edit_store pull-right pointer" style="margin-left:10px"></span>
                          <?}?>
                        </div>

                       <?if( $tpl->row_store < 3){
                            echo $tpl->list_store;
                        }else{?>
                         <select name="store_id" id="store_id" defaultvalue="<?=$tpl->store_id;?>" class="form-control select_store" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['ass_err_store'];?>">      
                           <?=$tpl->list_store;?>
                          </select>
                         <? } ?>
                       </fieldset>
                    </div>
                  <?if($CMS->input['act']== "add" OR $CMS->input['act']== "add_do"){?>
                    <div class="col-md-4">
                      <fieldset class="form-group">
                        <label class="form-label"><?=$CMS->lang['p_first_remain'];?></label>
                        <input class="form-control " type="text" name="p_first_remain" id="p_first_remain" value="<?=$tpl->p_first_remain;?>">
                      </fieldset>
                    </div>
                  <? } // End if check action ?>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <label class="form-label" ><?=$CMS->lang['p_stock_available'];?></label>
                      <div class="checkbox"><?=$tpl->option_p_stock_available;?></div>
                    </div>  
                    <div class="col-md-4">
                      <label class="form-label" ><?=$CMS->lang['p_show_instock'];?></label>
                      <div class="checkbox"><?=$tpl->option_p_show_instock;?></div>
                    </div>  
                  </div>
              <? } ?>
          </div>
        </div> <!-- End tabs-4-tab-1 -->

        <!-- tabs-4-tab-2 -->
        <!--.tab-pane-->
        <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-2" aria-expanded="false">
          <div class="box-typical box-typical-info" style="min-height: 500px;padding: 0 15px 15px;">
            <h5 class="m-t-md with-border"><?=$CMS->lang['title_information_product'];?></h5>
            <div class="row">
              <div class="col-md-8 col-lg-9">
                <div class="row">

                  <div class="col-md-6">
                    <fieldset class="form-group">
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
                    </fieldset>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-label pull-left" ><?=$CMS->lang['p_supplier'];?></label>
                      <? if ($CMS->permit["supplier_add"] == 1) { ?>
                        <a data-size="s" class="add_new_supplier pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i></a>
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
                  </div>
                  <div class="col-md-6">
                    <fieldset class="form-group">
                      <label class="form-label" ><?=$CMS->lang['p_sku'];?></label>
                      <input class="form-control" type="text" name="p_sku" id="p_sku" value="<?=$tpl->p_sku;?>">
                    </fieldset>

                    <fieldset class="form-group">
                      <label class="form-label" ><?=$CMS->lang['p_guarantee'];?></label>
                      <input class="form-control" maxlength="3" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" type="text" name="p_guarantee" id="p_guarantee" value="<?=$tpl->p_guarantee;?>"/>
                    </fieldset>
                  </div>
                  <div class="col-md-6">
                    <fieldset class="form-group">
                      <label class="form-label" ><?=$CMS->lang['p_barcode'];?></label>
                      <input class="form-control " type="text" name="p_barcode" id="p_barcode" value="<?=$tpl->p_barcode;?>">
                    </fieldset>
                    <fieldset class="form-group">
                      <label class="form-label" ><?=$CMS->lang['p_img_alt'];?></label>
                        <input class="form-control" type="text" name="p_img_alt" id="p_img_alt" value="<?=$tpl->p_img_alt;?>">
                    </fieldset>
                  </div>


                </div>
              </div>
              <div class="col-md-4 col-lg-3">
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
                   
                    <div class="drop-zone fileinput-button" style="height: 100% !important;padding: 5px;">
                      <img id="upload_img_show"  width="205" src="<?=$tpl->src_image_upload;?>" <?=$tpl->style_display;?>  />
                          <i class="font-icon font-icon-cloud-upload-2"></i>
                          <div class="drop-zone-caption">Drag file to upload</div>
                          <input type="file"  name="p_image" id="ufile" accept="image/*">
                      </div><!--.drop-zone-->
                    <img class="img-responsive" src="<?=$tpl->pg_avartar;?>" style="max-width: 100%;" alt="<?=$tpl->product_group_name;?>"> 
              </fieldset>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <fieldset class="form-group semibold">
                  <label class="form-label" ><?=$CMS->lang['p_show'];?></label>
                  <div class="checkbox"><?=$tpl->option_p_show;?></div>
                </fieldset>
              </div>
              <div class="col-md-12">
                <fieldset class="form-group box_check">
                  <label class="form-label semibold" ><?=$CMS->lang['p_product_option'];?></label>
                  <div class='checkbox checkbox-inline'>
                      <input type='checkbox' name='p_product_option[]' value='1' id='check-1'  >
                      <label for='check-1'><?=$CMS->lang['product_option_1'];?></label>
                  </div>
                  <div class='checkbox checkbox-inline'>
                      <input type='checkbox' name='p_product_option[]' value='2' id='check-2'  >
                      <label for='check-2'><?=$CMS->lang['product_option_2'];?></label>
                  </div>
                  <div class='checkbox checkbox-inline'>
                      <input type='checkbox' name='p_product_option[]' value='3' id='check-3'  >
                      <label for='check-3'><?=$CMS->lang['product_option_3'];?></label>
                  </div>
                  <div class='checkbox checkbox-inline'>
                      <input type='checkbox' name='p_product_option[]' value='4' id='check-4'  >
                      <label for='check-4'><?=$CMS->lang['product_option_4'];?></label>
                  </div>
                  <div class='checkbox checkbox-inline'>
                      <input type='checkbox' name='p_product_option[]' value='5' id='check-5'  >
                      <label for='check-5'><?=$CMS->lang['product_option_5'];?></label>
                  </div>
                </fieldset>
              </div>
            </div>

            <div class="row">
                  <div class="col-md-12">
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

                      <div class="tab-content" style="border: none;padding: 0;padding-top: 5px;">
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
                                    <a class="nav-link active" href="#p_information_1" role="tab" data-toggle="tab"><?=$CMS->lang['p_information_1'];?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#p_information_2" role="tab" data-toggle="tab">
                                        <?=$CMS->lang['p_information_2'];?>
                                    </a>
                                </li>
                            </ul>
                        </div><!--.tabs-section-nav-->
                
                    <div class="tab-content" style="border: none;padding: 0;padding-top: 5px;">
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
              </div>

            <h5 class="m-t-md with-border"><?=$CMS->lang['title_seo_product'];?></h5>
            <div class="row">
              <div class="col-md-6">
                <fieldset class="form-group">
                  <label class="form-label" for="exampleInputEmail1">Meta Title</label> <textarea rows="6" class="form-control" placeholder="" data-autosize="" style="overflow: hidden; word-wrap: break-word; height: 90px;" name="meta_title"><?=$tpl->meta_title;?></textarea>
                </fieldset>
              </div>

              <div class="col-md-6">
                <fieldset class="form-group">
                  <label class="form-label" for="exampleInputEmail1">Meta Keywords</label> 
                  <textarea rows="6" class="form-control" placeholder="" data-autosize="" style="overflow: hidden; word-wrap: break-word; height: 90px;" name="meta_keywords"><?=$tpl->meta_keywords;?></textarea>
                </fieldset>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <fieldset class="form-group">
                  <label class="form-label" for="exampleInputEmail1">Meta Description</label> 
                  <textarea rows="6" class="form-control" placeholder="" data-autosize="" style="overflow: hidden; word-wrap: break-word; height: 90px;" name="meta_description"><?=$tpl->meta_description;?></textarea>
                </fieldset>
              </div>
            </div>

          </div>
        </div>
        <!--.tab-pane-->


        <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-3" aria-expanded="false">
          <div class="box-typical box-typical-info" style="min-height: 500px;padding: 0 15px 15px;">
            <div class="row">
              
              <!--Product attribute-->
              <div class="col-xl-8">
                <h5 class="m-t-md with-border"><?=$CMS->lang['title_attribute_product'];?></h5>
                <div class="alert_attribute">
                  <div class="alert box_alert" role="alert" style="border-color: #cccccc; background-color: #e7e7e7; color: #ff5200;">
                    <i class="font-icon font-icon-warning"></i>
                    <span><?=$CMS->lang['notes_choose_attribute'];?>.</span>
                    <span><?=str_replace('[link]', 'onclick="activaTab(\'tabs-4-tab-1\')" style="font-size:14px;cursor:pointer;"', $CMS->lang['title_click_here_to_update']);?>.</span>
                  </div>
                </div>
                <h5 class="section-title no-pt"><?=$CMS->lang['title_attr_list'];?></h5>
                <div class="result_attribute">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xl-12">
                <h5 class="m-t-md with-border"><?=$CMS->lang['title_information_for_shipping'];?></h5>
                <div class="col-xl-3">
                  <fieldset class="form-group">
                    <label class="form-label" >
                      <?=$CMS->lang['title_weight'];?>
                      <span class="question">
                        <i class="fa fa-question-circle" aria-hidden="true"></i>
                        <p class="box_answer" style="display: none;"><?=$CMS->lang['description_weight'];?></p>
                      </span>

                    </label>
                    <input type="text" class="form-control" name="weight" value="<?=$tpl->weight;?>" autocomplete="off">
                  </fieldset>
                </div>
                <div class="col-xl-3">
                  <fieldset class="form-group">
                    <label class="form-label" ><?=$CMS->lang['title_length'];?></label>
                    <input type="text" class="form-control" name="length" value="<?=$tpl->length;?>" autocomplete="off">
                  </fieldset>
                </div>
                <div class="col-xl-3">
                  <fieldset class="form-group">
                    <label class="form-label" ><?=$CMS->lang['title_width'];?></label>
                    <input type="text" class="form-control" name="width" value="<?=$tpl->width;?>" autocomplete="off">
                  </fieldset>
                </div>
                <div class="col-xl-3">
                  <fieldset class="form-group">
                    <label class="form-label" ><?=$CMS->lang['title_height'];?></label>
                    <input type="text" class="form-control" name="height" value="<?=$tpl->height;?>" autocomplete="off">
                  </fieldset>
                </div>
              </div>
            </div>
        </div>
      </div>
        <!--.tab-pane-->


        <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-4" aria-expanded="false">
          <fieldset class="box-typical box-typical-info">
            <div class="box-typical-upload box-typical-upload-in">
                <div class="drop-zone fileinput-button drop-zone-no-border" style="width: 100%; height: 110px;">
                    <i class="font-icon font-icon-cloud-upload-2"></i>
                    <div class="drop-zone-caption">Drag file to upload</div>
                    <input type="file" multiple name="p_gallery[]" id="list_image" class="multiple_upload" accept="image/*">
                </div><!--.drop-zone-->
                <p class="box_error" style="display: none;"></p>
                <div class="col-md-12">
                  <h6 class="uploading-list-title title_upload" style="display: none;"><?=$CMS->lang['title_note_uploading'];?></h6>
                  <ul class="uploading-list list_upload"></ul>
                </div>
            </div>
          </fieldset>
                
          <section class="box-typical box-typical-margin-top box-typical-full-height-with-header">
            <header class="box-typical-header box-typical-header-bordered">
            <div class="tbl-row">
              <div class="tbl-cell tbl-cell-title">
                <h3>Gallery Listing</h3>
              </div>
              <!--div class="tbl-cell tbl-cell-actions"><button type="button" class="action-btn view active"> <i class="font-icon font-icon-view-grid"></i> </button> <button type="button" class="action-btn view"> <i class="font-icon font-icon-view-rows"></i> </button> <button type="button" class="action-btn view"> <i class="font-icon font-icon-view-cascade"></i> </button></div-->
            </div>
            </header>
            <div class="box-typical-body">
              <div class="gallery-grid">
                <div class="box_img_upload">
                  <?
                  if( is_array($tpl->list_gallery) ){
                   foreach ($tpl->list_gallery as $image) { ?>
                    <div class="gallery-col">
                      <article class="gallery-item" style="height: 158px;">
                          <img class="gallery-picture" src="<?=$CMS->vars['upload_url'];?>/<?=$image;?>" alt="" height="158">
                          <div class="gallery-hover-layout">
                              <div class="gallery-hover-layout-in">
                                  <div class="btn-group">
                                      <button href="<?=$CMS->vars['upload_url'];?>/<?=$image;?>" type="button" class="btn popup-gallery">
                                          <i class="font-icon font-icon-eye"></i>
                                      </button>
                                      <button id="<?=$CMS->input['id'];?>" onclick="del_img_box('<?=$image;?>', this);" type="button" class="btn">
                                          <i class="font-icon font-icon-trash"></i>
                                      </button>
                                      <input type="hidden" name="old_gallery[]" value="<?=$image;?>"/>
                                  </div>
                              </div>
                          </div>
                      </article>
                    </div><!--.gallery-col-->
                  <? } }?>
                </div>
              </div>
            </div>
          </section>
      </div>

      <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-5" aria-expanded="false">
        <fieldset class="box-typical box-typical-info" style="min-height: 500px;">
          <div class="form-wrapper">
            <div class="col-xl-12">
              <h5 class="m-t-md with-border"><?=$CMS->lang['title_create_child_product'];?></h5>
              <div class="alert box_alert" role="alert" style="border-color: #f29824; background-color: #fdf4e6;color: #f29824;"><i class="font-icon font-icon-warning"></i>
                <span><?=$CMS->lang['notes_create_product_attribute'];?></span>
              </div>
            </div>
            <div class="col-xl-4">
              <h5 class="section-title no-pt"><?=$CMS->lang['title_attr_group'];?></h5>
              <fieldset class="form-group">
                <select name="attr_group" onchange="renderAttribute(this, '.box_attribute');" class="form-control">
                  <option value=""><?=$CMS->lang['title_choose_attribute_group'];?></option>
                  <?=$tpl->optionAttributeGroup;?>
                </select>
              </fieldset>
              <h5 class="section-title no-pt"><?=$CMS->lang['title_attr_list'];?></h5>
              <div class="box_attribute">
              </div>
            </div>
            

            <div class="col-xl-8">
                <h5 class="section-title no-pt"><?=$CMS->lang['title_combination'];?></h5>
                <div class="bs-example" data-example-id="bordered-table" style="margin-bottom: 10px;">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th><?=$CMS->lang['title_attribute_name'];?></th>
                                <th><?=$CMS->lang['title_name_extension'];?></th>
                                <th><?=$CMS->lang['title_code_extension'];?></th>
                                <th><?=$CMS->lang['title_quantity'];?></th>
                            </tr>
                        </thead>
                        <tbody id="combination"></tbody>
                    </table>
                </div>
            </div>


            <!--div class="col-xl-3 row">
              <div class="col-xl-8">
                <fieldset class="form-group">
                  <label class="form-label semibold" ><?=$CMS->lang['title_choose_color'];?></label>
                  <input type="text" class="form-control" name="color" value="<?=$tpl->color;?>" autocomplete="off">
                </fieldset>
              </div>
              <div class="col-xl-4">
                <fieldset class="form-group">
                    <label class="form-label semibold" ><?=$CMS->lang['title_color'];?></label>
                    <div id="colorSelector"><div style="background-color: #0000ff"></div></div>
                </fieldset>
              </div>
            </div-->

            
          </div>

        </fieldset>
      </div>
      <!--.tab-pane-->


      <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-6" aria-expanded="false">
        <div class="box-typical box-typical-info" style="min-height: 500px;padding: 0 15px 15px;">
          <h5 class="m-t-md with-border">Variants options</h5>
          <div class="row">
            <div class="col-lg-6 col-md-6">
              <div class="box_option">
                <div class="form-group line_option row html_clone" style="display: none;">
                  <div class="col-lg-3 col-md-3">
                    <input type="text" name="option1" maxlength="40" class="form-control option_name">
                  </div>
                  <div class="col-lg-8 col-md-8 select2-zp">
                    <select name="option_value1[]" onchange="changeCombinationOption();" data-placeholder="Separate options with a comma" class="form-control option_value" multiple="multiple">
                    </select>
                  </div>
                  <div class="col-lg-1 col-md-1 box_deloption"></div>
                </div>
                <div class="content_option">
                  <div class="line_option_header row">
                      <div class="col-lg-3 col-md-3">
                        <label class="form-label">Option name</label>
                      </div>
                      <div class="col-lg-8 col-md-8">
                        <label class="form-label">Option values</label>
                      </div>
                      <div class="col-lg-1 col-md-1"></div>
                  </div>
                  <div class="form-group line_option row">
                    <div class="col-lg-3 col-md-3">
                      <input type="text" name="option1" value="Color" id="option1" maxlength="40" class="form-control option_name">
                    </div>
                    <div class="col-lg-8 col-md-8 select2-zp">
                      <select name="option_value1[]" id="option_value1" onchange="changeCombinationOption();" data-placeholder="Separate options with a comma" inc="1" class="form-control select2_tag option_value" multiple="multiple">
                      </select>
                    </div>
                    <div class="col-lg-1 col-md-1 box_deloption"></div>
                  </div>
                </div>
                <div class="row form-group">
                    <div class="col-lg-12 col-md-12 control_option"><a class="btn btn-primary btn_addoption" onclick="OpVariants.addOption('html_clone', 'content_option', this);">Add another option</a></div>
                  </div>
              </div>
            </div>
            <!--End Block left-->
            <div class="col-lg-12 col-md-12">
              <section class="box-typical scrollable" <?=$tpl->display;?>>
                <div class="box-typical-body">
                  <div class="table-responsive">
                    
                    <table class="table table-bordered table-hover table-zp table-zp-top">
                      <thead>
                        <tr>          
                          <th>Variant name</th>
                          <th>SKU</th>
                          <th>Price</th> 
                          <th class="th_option1">Color</th>  
                          <th class="th_option2" style="display: none;">Size</th> 
                          <th class="th_option3" style="display: none;">Material</th> 
                          <th></th>          
                        </tr>
                      </thead>
                      <tbody id="combination_option">
                        <tr><td colspan="13">No variant</td></tr>
                      </tbody>
                    </table>            
                  </div>
                </div><!--.box-typical-body-->
              </section><!--.box-typical-->
            </div>
            <!--End Block right-->
          </div>
        </div>
      </div>
      <!--.tab-pane-->

      <!-- Shipping & fee -->
      <div role="tabpanel" class="tab-pane fade" id="tabs_shipping_fee" aria-expanded="false">
        <div class="box-typical box-typical-info" style="min-height: 500px;padding: 0 15px 15px;">
          <h5 class="m-t-md with-border"><?=$CMS->lang['title_manage_ship_fee'];?></h5>
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <?=\core\ezy::render('form_shipping_fee', 'product');?>
            </div>
          </div>
        </div>
      </div><!--.tab-pane-->

    </div><!--.tab-content-->
  </section>
</div>
<!--.chat-container-->
<!-- </div> -->
<!--.container-fluid-->





<script type="text/javascript">
  function choose_color(ele) {
      // Choose color
      $(ele).ColorPicker({
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
        $(ele).val('#' + hex);
      },
      onSubmit: function(hsb, hex, rgb, el) {
        $(el).val('#' + hex);
        $(el).ColorPickerHide();
      },
    });
  }
</script>


  <section class="add_cart_footer">
<?=$CMS->global->footer_back($tpl->url_back);?>
<?=$tpl->footer_button;?>
    </section>
    <input type="hidden" value="0" name="add_product_option"  />
 </form>
</div>
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
// Auto select attribute
var list_attribute = JSON.parse('<?=isset($tpl->attribute) ? $tpl->attribute : '{}';?>');  
$(document).ready(function(){
  // Validate form
  validate_form_custom("form#form-signin_v1", ".act_submit_save, button[type='submit']");
  $(".select2_tag").select2({tags: true, placeholder: function(){
        $(this).data('placeholder');
  }});

  // Magnific popup
  $('.popup-gallery').magnificPopup({
    // delegate: 'button',
    type: 'image',
    tLoading: 'Loading image #%curr%...',
    mainClass: 'mfp-img-mobile',
    gallery: {
      enabled: true,
      navigateByImgClick: true,
      preload: [0,1] // Will preload 0 - before current, and 1 after the current image
    },
    image: {
      tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
      titleSrc: function(item) {
        // return item.el.attr('title') + '<small>by Marsel Van Oosten</small>';
      }
    }
  });

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
          title: 'Are you sure?',
          text: "Are you sure to remove this product?",
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
            swal( 'Success!', 'You removed this child product', 'success');
        });
  });// end del_row_product

});
  var module_name = "product_module"; 
  var lang_supplier_add = "<?=$CMS->lang['supplier_add'];?>";
  var lang_supplier_edit = "<?=$CMS->lang['supplier_edit'];?>";

  var lang_manufacture_edit  = "<?=$CMS->lang['manufacture_edit'];?>";
  var lang_manufacture_add = "<?=$CMS->lang['manufacture_add'];?>";

  var lang_store_add  = "<?=$CMS->lang['store_add'];?>";
  var lang_store_update = "<?=$CMS->lang['store_update'];?>";

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
          $(obj).parents(".gallery-col").remove();
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
<script>
  var lang_supplier_add = "<?=$CMS->lang['supplier_add'];?>";
  var lang_supplier_edit = "<?=$CMS->lang['supplier_edit'];?>";

  var lang_shipment_add = "<?=$CMS->lang['title_shipment_add'];?>";
  var lang_shipment_edit = "<?=$CMS->lang['title_shipment_edit'];?>";

  var lang_manufacture_add = "<?=$CMS->lang['title_add_manufacture'];?>";
  var lang_manufacture_edit = "<?=$CMS->lang['title_edit_manufacture'];?>";

  var lang_pg_add = "<?=$CMS->lang['title_add_ass_group'];?>";
  var lang_pg_edit = "<?=$CMS->lang['title_edit_product_group'];?>";

  var lang_product_add = "<?=$CMS->lang['title_add_product'];?>";
  var lang_service_add = "<?=$CMS->lang['title_add_service'];?>";
  var lang_asset_add = "<?=$CMS->lang['title_add_asset'];?>";
  var lang_product_edit = "<?=$CMS->lang['title_edit_product'];?>";
  var lang_asset_edit = "<?=$CMS->lang['title_edit_asset'];?>";

  var lang_btn_add_save = "<?=$CMS->lang['btn_edit_save_request'];?>";
  var lang_btn_save_request = "<?=$CMS->lang['btn_save_request'];?>";
  var lang_btn_edit_save = "<?=$CMS->lang['btn_edit_save'];?>";

  var lang_confirm_approve = "<?=$CMS->lang['confirm_approve_bill'];?>";

  var lang_cus_add = "<?=$CMS->lang['title_cus_add'];?>";
  var lang_cus_edit = "<?=$CMS->lang['title_cus_edit'];?>";
</script>

<?=\core\ezy::render("add_supplier", "product");?>
<?=\core\ezy::render("add_manufacture", "product");?>

<?=$CMS->global->formProductgroup();?>
<script src="<?=$CMS->vars['js_acp'];?>/product_group.js"></script>
<?=\core\ezy::render("add_store", "product");?>

