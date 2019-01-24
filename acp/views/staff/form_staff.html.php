<style type="text/css">
  .drop-zone img { display: block !important; }
</style>
<section class="main_form">
    <figure class="heading">
        <h3><?=$tpl->input['title_staff'];?></h3>
        <a href="<?=$CMS->vars['root_domain'];?>/?site=staff" title=""><span class="font-icon font-icon-del"></span></a>
    </figure>
    <form method="post" name="form_staff" id="form_staff" action="<?=$tpl->input['link_act'];?>" enctype="multipart/form-data">
        <section class="box-typical box-typical-padding border">
            <div class="row">
                <div class="col-xl-12"><h5 class="m-t-lg with-border"><?=$CMS->lang['title_info_staff'];?></h5></div>
            </div>
            <div class="row">
              <div class="col-lg-8 col-md-8">    
                <div class="col-xl-6">
                    <fieldset class="form-group">
                      <label class="form-label" ><?=$CMS->lang['staff_name'];?> <span style="color:red">(*)</span></label>
                      <div class="form-control-wrapper">
                          <input class="form-control" type="text" name="user_display_name" maxlength="50" value="<?=$tpl->input['user_display_name'];?>" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['title_full_name_incomplete'];?>">
                      </div>
                    </fieldset>
                </div>
                <div class="col-xl-6">
                    <fieldset class="form-group">
                      <label class="form-label" >Email <span style="color:red">(*)</span></label>
                      <div class="form-control-wrapper">
                          <input class="form-control" type="text" name="user_email" value="<?=$tpl->input['user_email'];?>" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_email'];?>" data-validation-regex="/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/" data-validation-regex-message="<?=$CMS->lang['invalid_email'];?>">
                      </div>
                    </fieldset>
                </div>
                <div class="col-xl-12">
                    <fieldset class="form-group">
                      <label class="form-label" ><?=$CMS->lang['staff_description'];?></label>
                      <div class="form-control-wrapper">
                          <textarea name="user_note" class="form-control" rows="7" placeholder="Description"><?=$tpl->input['user_note'];?></textarea>
                      </div>
                    </fieldset>
                </div>
                <div class="col-xl-3">
                  <fieldset class="form-group">
                    <label class="form-label" ><?=$CMS->lang['staff_status'];?></label>
                    <p class="typeahead-field">
                      <span class="typeahead-query">
                        <select class="form-control auto_select" name="user_status" defaultvalue="<?=$tpl->input['user_status'];?>">
                          <option value="0"><?=$CMS->lang['suspended'];?></option>
                          <option value="1"><?=$CMS->lang['active'];?></option>
                        </select>
                      </span>
                    </p>
                  </fieldset>
                </div>

                <div class="col-xl-3">
                  <fieldset class="form-group">
                    <label class="form-label" ><?=$CMS->lang['staff_range'];?></label>
                    <p class="typeahead-field">
                      <span class="typeahead-query">
                        <input class="form-control" type="text" name="user_range" maxlength="2" value="<?=$tpl->input['user_range'];?>">
                      </span>
                    </p>
                  </fieldset>
                </div>
              </div>
              <div class="col-lg-4 col-md-4">
                  <fieldset class="form-group">
                    <label class="form-label pull-left" ><?=$CMS->lang['staff_avatar'];?></label>
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
                     
                      <div class="drop-zone fileinput-button" style="height: 100% !important;padding: 5px; min-height: 205px;">
                        <img id="upload_img_show" width="100%" src="<?=$tpl->input['src_image'];?>" />
                            <i class="font-icon font-icon-cloud-upload-2"></i>
                            <div class="drop-zone-caption">Drag file to upload</div>
                            <input type="file" name="user_avatar" id="ufile" accept="image/*">
                        </div><!--.drop-zone-->
                </fieldset>
              </div>

            </div>
            
            <div class="row">
              <div class="col-xl-3"><button type="submit" class="btn btn_primary btn_submit"><?=$tpl->input['title_staff'];?></button></div>
            </div>
        </section>
        <section class="add_cart_footer">
          <?=$CMS->global->footer_back($tpl->input['url_back']);?>
        </section>
    </form>
    <script>
           $(document).ready(function(){
              validate_form_custom("#form_staff",".btn_submit");
          ;});

    </script>
</section>

