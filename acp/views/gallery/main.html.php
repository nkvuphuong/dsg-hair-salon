<section class="main_form">
    <figure class="heading">
        <h3>Add photo</h3>
        <a href="<?=$CMS->vars['root_domain'];?>/?site=gallery" title=""><span class="font-icon font-icon-del"></span></a>
    </figure>
    <section class="box-typical box-typical-padding border">
      <div class="row">
          <p style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;"><?=$CMS->lang['gallery_add_submit'];?></p>
          <?=$CMS->global->languageTab('langTab');?>
          <form id="gallery_form" method="post" action="<?=$CMS->vars['root_domain'];?>/?site=gallery&act=add_do" name="gallery_form" enctype="multipart/form-data">
            <noscript><input type="hidden" name="redirect" value="https://blueimp.github.io/jQuery-File-Upload/"></noscript>
            <div class="col-xl-6 col-lg-6 col-sm-12 col-xs-12">
              <div class="row">
                <ul class="list_field_gallery">
                    <li class="col-xl-6 col-lg-6 col-sm-12 col-xs-12">
                        <div class="form-group row">
                          <label class="form-control-label"><?=$CMS->lang['cat_id'];?><font style="margin-left:5px" color="#FF0000">(*)</font></label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <select class="form-control select2" name="cat_id" defaultvalue="<?=$tpl->data['cat_id'];?>" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['gallery_incomplete_category'];?>">
                                <option value=""><?=$CMS->lang['select_category'];?></option> 
                                <?=$CMS->gallery->load_cate_gallery();?>
                               </select> 
                          </div>
                        </div>
                    </li>

                  <? if($CMS->vars['translations']) {
                  foreach ($CMS->vars['translations'] as $langCode => $langName) {?>
                    <li class="col-xl-6 col-lg-6 col-sm-12 col-xs-12 langTab" lang="<?=$langCode;?>">
                        <div class="form-group row">
                          <label class="form-control-label"><?=$CMS->lang['gallery_name'];?><font style="margin-left:5px" color="#FF0000">(*)</font></label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <input class="form-control" type="text" name="gallery_name[<?=$langCode;?>]" id="gallery_name[<?=$langCode;?>]" value="<?=$tpl->data['gallery_name'];?>" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['gallery_incomplete_name'];?>">
                          </div>
                        </div>
                    </li>

                    <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12 langTab" lang="<?=$langCode;?>">
                      <div class="form-group row">
                        <label class="form-control-label"><?=$CMS->lang['gallery_note'];?></label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                          <textarea class="form-control" name="gallery_description[<?=$langCode;?>]" id="gallery_description[<?=$langCode;?>]" cols="60" rows="5" ><?=$tpl->data['gallery_description'];?></textarea>
                        </div>
                      </div>
                    </li>
                  <?} } else {?>
                  <li class="col-xl-6 col-lg-6 col-sm-12 col-xs-12">
                      <div class="form-group row">
                        <label class="form-control-label"><?=$CMS->lang['gallery_name'];?><font style="margin-left:5px" color="#FF0000">(*)</font></label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                          <input class="form-control" type="text" name="gallery_name" value="<?=$tpl->data['gallery_name'];?>" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['gallery_incomplete_name'];?>">
                        </div>
                      </div>
                  </li>
                  <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <div class="form-group row">
                      <label class="form-control-label"><?=$CMS->lang['gallery_note'];?></label>
                      <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                        <textarea class="form-control" name="gallery_description" cols="60" rows="5" ><?=$tpl->data['gallery_description'];?></textarea>
                      </div>
                    </div>
                  </li>
                <? } ?>
                 <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                      <?=$tpl->formhair_custom;?>
                </li>  
                <li class="col-sm-8 col-xs-12">
                    <div class="form-group row">
                      <label class="form-control-label"><?=$CMS->lang['gallery_image_alt'];?></label>
                      <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="gallery_image_alt" value="<?=$tpl->data['gallery_image_alt'];?>">
                      </div>
                    </div>
                </li>
                    <li class="col-sm-4 col-xs-6">
                        <div class="form-group row">
                            <label class="form-control-label"><?=$CMS->lang['gallery_sort_order'];?></label>
                            <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                                <input class="form-control" type="number" name="gallery_sort_order" value="<?=$tpl->data['gallery_sort_order'];?>"/>
                            </div>
                        </div>
                    </li>
              
              </ul>
            </div>
          </div>
          <div class="col-xl-6 col-lg-6 col-sm-12 col-xs-12">
            <label class="form-control-label" style="padding: 10px 0"><?=$CMS->lang['gallery_image'];?></label>
            <div>
                <label style="display: inline;" for="upload_type_file"><input <?=$tpl->upload_type_selected['file'];?> type="radio" name="upload_type" id="upload_type_file" value="file"> Upload file</label>
                <label style="display: inline;" for="upload_type_url"><input <?=$tpl->upload_type_selected['url'];?> type="radio" name="upload_type" id="upload_type_url" value="url"> URL</label>
            </div>
            <div class="box-typical-upload box-typical-upload-in box_upload_img upload_type_wrap upload_type_file">
                <div class="drop-zone fileinput-button" style="width: 100%">
                    <i class="font-icon font-icon-cloud-upload-2"></i>
                    <div class="drop-zone-caption">Drag file to upload</div>
                    <input type="file" multiple name="list_image[]" id="list_image" class="multiple_upload" accept="image/*">
                </div><!--.drop-zone-->
                <p class="box_error" style="display: none;"></p>
                <h6 class="uploading-list-title title_upload" style="display: none;"><?=$CMS->lang['title_note_uploading'];?><span class="num_files"></span></h6>
                <ul class="uploading-list list_upload list_image"></ul>
            </div>
              <div class="upload_type_wrap upload_type_url">
                  <?=\core\ezy::render("url_items","gallery");?>
              </div>
          </div>
          <div class="col-lg-12">
            <div class="form-group">
              <label class="form-control-label"></label>
              <button class="btn btn_run" type="submit"><?=$CMS->lang['gallery_add_submit'];?></button>
            </div>
          </div>
        </form>
    </div>
  </section>
  <section class="add_cart_footer">
    <?=$CMS->global->footer_back($tpl->url_back);?>
  </section>
</section>

<script type="text/javascript">
  $(document).ready(function(){
    validate_form_custom("#gallery_form", "button[type='submit']", "gallery_form");
  });
</script>

