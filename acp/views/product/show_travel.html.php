<section class="add_form main_form">
  
  <!-- header -->
  <figure class="heading">
    <h3><?=$CMS->lang['p_info'];?></h3>
    <a class="btn_backlist" href="/acp/?site=<?=$CMS->input['site'];?>" title=""><span class="font-icon font-icon-del"></span></a>
  </figure>

  <!-- Lang tabs -->
  <?=$CMS->global->languageTab('langTab');?>
  
  <figure class="box-typical box-typical box-typical-padding border">
    <div class="row">
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-12">
            <?
            if( $CMS->vars['translations'] )
            {
              foreach( $CMS->vars['translations'] as $langCode => $langName ) 
              { 
            ?>
            <fieldset class="form-group display-flex" lang="<?=$langCode;?>">
              <label class="form-control-label2"><?=$CMS->lang['tra_name'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold">
                  <span><?=$tpl->data['product_name'][$langCode];?></span>
                  <span><?=$tpl->data['search_p_name'];?></span>
                </div>
              </div>
            </fieldset>
            
            <?}}else{?>
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['tra_name'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold">
                  <span><?=$tpl->data['product_name'];?></span>
                </div>
              </div>
            </fieldset>
            <?}?>
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['tra_barcode'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold" style="margin: 0;">
                  <span><?=$tpl->data['product_barcode'];?> </span>
                </div>
              </div>
            </fieldset>
          </div>
          <div class="col-md-4">
            <fieldset class="form-group">
              <label class="form-control-label2"><?=$CMS->lang['p_avartar'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold">
                  <img src="<?=$tpl->data['product_image_c'];?>" width="150" />
                </div>
              </div>
            </fieldset>
          </div>
          <div class="col-md-8">
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['tra_product_group'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold">
                  <span><?=$tpl->data['product_group_bk'];?> </span>
                </div>
              </div>
            </fieldset>
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['tra_option_type'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold" style="margin: 0;">
                  <span><?=$tpl->data['product_tra_type_c'];?> </span>
                </div>
              </div>
            </fieldset>
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['tra_option'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold" style="margin: 0;">
                  <span><?=$tpl->data['product_option_c'];?> </span>
                </div>
              </div>
            </fieldset>
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['p_order'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold" style="margin: 0;">
                  <span><?=$tpl->data['product_order'];?></span>
                </div>
              </div>
            </fieldset>
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['p_img_alt'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold">
                  <span><?=$tpl->data['product_image_alt'];?> </span>
                </div>
              </div>
            </fieldset>
        </div>
      </div>

      <div class="row">
        <div class="col-xl-12"><h5 class="m-t-lg with-border"><?=$CMS->lang['title_info_transaction'];?></h5></div>
        <div class="col-xl-6">
          <fieldset class="form-group display-flex">
            <label class="form-control-label2"><?=$CMS->lang['tra_price_old'];?>: </label>
            <div class="form-control-span2"> 
              <div class="form-label semibold" style="margin: 0;">
                <span><?=$tpl->data['product_price_old_c'];?> </span>
              </div>
            </div>
          </fieldset>   
        </div>
        <div class="col-xl-6">
          <fieldset class="form-group display-flex">
            <label class="form-control-label2"><?=$CMS->lang['tra_price_sell'];?>: </label>
            <div class="form-control-span2"> 
              <div class="form-label semibold" style="margin: 0;">
                <span><?=$tpl->data['product_price_sell_c'];?> </span>
              </div>
            </div>
          </fieldset>
        </div>
        <div class="col-xl-6">
          <fieldset class="form-group display-flex">
            <label class="form-control-label2"><?=$CMS->lang['tra_suffix_price'];?>: </label>
            <div class="form-control-span2"> 
              <div class="form-label semibold" style="margin: 0;">
                <span><?=$tpl->data['product_up'];?></span>
              </div>
            </div>
          </fieldset>
        </div>
      </div>

      <div class="row">
        <div class="col-xl-12"><h5 class="m-t-lg with-border"><?=$CMS->lang['title_info_tour'];?></h5></div>
        <div class="col-xl-12">
          <fieldset class="form-group display-flex">
            <label class="form-control-label2"><?=$CMS->lang['tra_time'];?>: </label>
            <div class="form-control-span2"> 
              <div class="form-label semibold">
                <span><?=$tpl->data['product_attribute_c']['tra_number_day'];?> </span><span><?=$CMS->lang['tra_time_day'];?></span>
                <span> </span>
                <span><?=$tpl->data['product_attribute_c']['tra_number_night'];?> </span><span><?=$CMS->lang['tra_time_night'];?></span>
              </div>
            </div>
          </fieldset>
        </div>
        <div class="col-xl-6">
          <fieldset class="form-group display-flex">
            <label class="form-control-label2"><?=$CMS->lang['tra_time_start'];?>: </label>
            <div class="form-control-span2"> 
              <div class="form-label semibold">
                <?=$tpl->data['product_attribute_c']['tra_time_start_c'];?>
              </div>
            </div>
          </fieldset>
        </div>
        <div class="col-xl-6">
          <fieldset class="form-group display-flex">
            <label class="form-control-label2"><?=$CMS->lang['tra_time_end'];?>: </label>
            <div class="form-control-span2"> 
              <div class="form-label semibold">
                <?=$tpl->data['product_attribute_c']['tra_time_end_c'];?>
              </div>
            </div>
          </fieldset>
        </div>
        <div class="col-xl-6">
          <fieldset class="form-group display-flex">
            <label class="form-control-label2"><?=$CMS->lang['tra_vehicle_start'];?>: </label>
            <div class="form-control-span2"> 
              <div class="form-label semibold">
                <?=$tpl->data['product_attribute_c']['tra_vehicle_start'];?>
              </div>
            </div>
          </fieldset>
        </div>
        <div class="col-xl-6">
          <fieldset class="form-group display-flex">
            <label class="form-control-label2"><?=$CMS->lang['tra_vehicle_end'];?>: </label>
            <div class="form-control-span2"> 
              <div class="form-label semibold">
                <?=$tpl->data['product_attribute_c']['tra_vehicle_end'];?>
              </div>
            </div>
          </fieldset> 
        </div>
        <div class="col-xl-6">
          <fieldset class="form-group display-flex">
            <label class="form-control-label2"><?=$CMS->lang['tra_minimum_seat'];?>: </label>
            <div class="form-control-span2"> 
              <div class="form-label semibold">
                <?=$tpl->data['product_attribute_c']['tra_minimum_seat'];?>
              </div>
            </div>
          </fieldset> 
        </div>
        <div class="col-xl-6">
          <fieldset class="form-group display-flex">
            <label class="form-control-label2"><?=$CMS->lang['tra_maximum_seat'];?>: </label>
            <div class="form-control-span2"> 
              <div class="form-label semibold">
                <?=$tpl->data['product_attribute_c']['tra_maximum_seat'];?>
              </div>
            </div>
          </fieldset> 
        </div>
        <div class="col-xl-6">
          <fieldset class="form-group display-flex">
            <label class="form-control-label2"><?=$CMS->lang['tra_departure_city'];?>: </label>
            <div class="form-control-span2"> 
              <div class="form-label semibold">
                <?=$tpl->data['product_attribute_c']['city'];?>
              </div>
            </div>
          </fieldset>
        </div>
        <div class="col-xl-6">
          <fieldset class="form-group display-flex">
            <label class="form-control-label2"><?=$CMS->lang['tra_departure_district'];?>: </label>
            <div class="form-control-span2"> 
              <div class="form-label semibold">
                <?=$tpl->data['product_attribute_c']['district'];?>
              </div>
            </div>
          </fieldset>
        </div>
      </div>
    </div>
    <div class="col-md-6" >
      <div class="row">
        <div class="col-xl-12">
          <fieldset class="form-group">
            <label class="form-control-label2"><?=$CMS->lang['tra_map'];?>: </label>
            <div class="form-control-span2"> 
              <div class="form-label semibold" style="border: 1px solid rgb(236, 236, 236);border-radius: 3px;padding: 15px;">
                <divtext class="clearfix"><?=$tpl->data['product_attribute_c']['map'];?></divtext>
              </div>
            </div>
          </fieldset>
        </div>
        <div class="col-xl-12">
          <fieldset class="form-group">
            <label class="form-control-label2"><?=$CMS->lang['tra_summary_travel'];?>: </label>
            <div class="form-control-span2"> 
              <div class="form-label semibold" style="border: 1px solid rgb(236, 236, 236);border-radius: 3px;padding: 15px;">
                <divtext class="clearfix"><?=$tpl->data['product_attribute_c']['tra_summary_travel'];?></divtext>
              </div>
            </div>
          </fieldset>
        </div>
      </div>
    </div>
    <div class="col-xl-6">
      <?
      if( $CMS->vars['translations'] )
      {
        foreach( $CMS->vars['translations'] as $langCode => $langName ) 
        { 
      ?>
      <fieldset class="form-group" lang="<?=$langCode;?>">
        <label class="form-control-label2"><?=$CMS->lang['tra_description'];?>: </label>
        <div class="form-control-span2"> 
          <div class="form-label semibold">
            <divtext class="clearfix"><?=$tpl->data['product_description'][$langCode];?></divtext>
          </div>
        </div>
      </fieldset>
      
      <?}}else{?>
      <fieldset class="form-group">
        <label class="form-control-label2"><?=$CMS->lang['tra_description'];?>: </label>
        <div class="form-control-span2"> 
          <div class="form-label semibold" style="border: 1px solid rgb(236, 236, 236);border-radius: 3px;padding: 15px;">
            <divtext class="clearfix"><?=$tpl->data['product_description'];?></divtext>
          </div>
        </div>
      </fieldset>
      <?}?>
    </div>
    <div class="col-xl-6">
      <fieldset class="form-group">
        <label class="form-label" ><?=$CMS->lang['p_gallery'];?></label>
        <div class="box_img_upload">
          <?
          if( ! empty($tpl->data['product_gallery_c']) AND is_array($tpl->data['product_gallery_c']) )
          {
            foreach ( $tpl->data['product_gallery_c'] as $image ) 
            { 
          ?>
          <p class="img_list">
            <a class="del_img" id="<?=$CMS->input['id'];?>" onclick="del_img_box('<?=$image;?>', this);">
              <i class="fa fa-trash" aria-hidden="true"></i>
            </a>
            <img src="<?=$CMS->vars['upload_url'];?>/<?=$image;?>" class="img_item" />
          </p>

          <?}}else{?>
          <span>...</span>
          <?}?>
        </div>
      </fieldset>
    </div>

    <div class="col-xl-12">
      <?
      if( $CMS->vars['translations'] )
      {
        foreach( $CMS->vars['translations'] as $langCode => $langName ) 
        { 
      ?>
      <div class="langTab" lang="<?=$langCode;?>">                
        <section class="tabs-section">
          <div class="tabs-section-nav tabs-section-nav-inline">
              <ul class="nav" role="tablist">
                  <li class="nav-item"><a class="nav-link active" href="#p_information_1_<?=$langCode;?>" role="tab" data-toggle="tab"><?=$CMS->lang['tra_overview'];?></a></li>
                  <li class="nav-item"><a class="nav-link" href="#p_information_2_<?=$langCode;?>" role="tab" data-toggle="tab"><?=$CMS->lang['tra_tour_schedule'];?></a></li>
              </ul>
          </div>
          <div class="tab-content" style="border: 1px solid rgb(236, 236, 236);border-radius: 0 0 3px 3pxinherit;padding: 15px;border-top: none;">
            <div role="tabpanel" class="tab-pane fade in active" id="p_information_1_<?=$langCode;?>">
              <divtext class="clearfix"><?=$tpl->data['product_information_1'][$langCode];?></divtext>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="p_information_2_<?=$langCode;?>">
              <divtext class="clearfix"><?=$tpl->data['product_information_2'][$langCode];?></divtext>
            </div>
          </div>
        </section>
      </div>

      <?}}else{?>
      <section class="tabs-section">
        <div class="tabs-section-nav tabs-section-nav-inline">
            <ul class="nav" role="tablist">
                <li class="nav-item"><a class="nav-link active" href="#p_information_1" role="tab" data-toggle="tab"><?=$CMS->lang['tra_overview'];?></a></li>
                <li class="nav-item"><a class="nav-link" href="#p_information_2" role="tab" data-toggle="tab"><?=$CMS->lang['tra_tour_schedule'];?></a></li>
            </ul>
        </div>
        <div class="tab-content" style="border: 1px solid rgb(236, 236, 236);border-radius: 0 0 3px 3pxinherit;padding: 15px;border-top: none;">
          <div role="tabpanel" class="tab-pane fade in active" id="p_information_1">
            <divtext class="clearfix"><?=$tpl->data['product_information_1'];?></divtext>
          </div>
          <div role="tabpanel" class="tab-pane fade" id="p_information_2">
            <divtext class="clearfix"><?=$tpl->data['product_information_2'];?></divtext>
          </div>
        </div>
      </section>
      <?}?>
    </div>
  </div><!-- row -->
</section>
  
<section class="add_cart_footer">
  <?=$CMS->global->footer_back(array('list' => "{$CMS->vars['root_domain']}/?site={$CMS->input['site']}"));?>
  <?if( $CMS->permit['product_delete'] == 1 ){?>
  <a onclick="delete_confirm('<?="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=delete&id={$tpl->data['product_id']}";?>');" class="pull-right add_cart_2 hidden-sm-down"><?=$CMS->lang['gdelete'];?></a>
  <?}?>

  <?if( $CMS->permit['product_edit'] == 1 ){?>
  <a href="<?="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=edit&id={$tpl->data['product_id']}";?>" class="pull-right add_cart_2 hidden-sm-down"><?=$CMS->lang['gedit'];?></a>
  <?}?>

  <!-- mobile -->
  <div class="btn-group dropup pull-right hidden-md-up">
    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <i class="fa fa-save"></i><?=$CMS->lang['gaction'];?>
    </button>
    <div class="dropdown-menu">
      <ul>
        <?if( $CMS->permit['product_delete'] == 1 ){?>
        <li><a onclick="delete_confirm('<?="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=delete&id={$tpl->data['product_id']}";?>');" id="form_submit_mobile" title=""><i class="fa fa-trash-o"></i><?=$CMS->lang['gdelete'];?></a></li>
        <?}?>

        <?if( $CMS->permit['product_edit'] == 1 ){?>
        <li><a href="<?="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=edit&id={$tpl->data['product_id']}";?>" id="form_submit_option_mobile" title=""><i class="fa fa-edit"></i><?=$CMS->lang['gedit'];?></a></li>
        <?}?>
      </ul>
    </div>
  </div>
</section>
<script>  
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

<!-- Logs -->
<?=$tpl->logs;?>