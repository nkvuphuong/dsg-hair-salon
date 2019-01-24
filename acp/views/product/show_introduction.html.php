<section class="add_form main_form">
  
  <!-- header -->
  <figure class="heading">
    <h3><?=$CMS->lang['p_info'];?></h3>
    <a class="btn_backlist" href="/acp/?site=<?=$CMS->input['site'];?>" title=""><span class="font-icon font-icon-del"></span></a>
  </figure>

  <!-- lang tab -->
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
              <label class="form-control-label2"><?=$CMS->lang['p_name'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold">
                  <span><?=$tpl->data['product_name'][$langCode];?></span>
                  <span><?=$tpl->data['search_p_name'];?></span>
                </div>
              </div>
            </fieldset>
            
            <?}}else{?>
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['p_name'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold">
                  <span><?=$tpl->data['product_name'];?></span>
                </div>
              </div>
            </fieldset>
            <?}?>
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
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['p_img_alt'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold">
                  <span><?=$tpl->data['product_image_alt'];?> </span>
                </div>
              </div>
            </fieldset>
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['p_show'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold" style="margin: 0;">
                  <span><?=$tpl->data['product_show_c'];?> </span>
                </div>
              </div>
            </fieldset>
            <?if(\core\ezy::$theme_key == "nms") {?>
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['p_stock_available'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold" style="margin: 0;">
                  <span><?=$tpl->data['product_stock_available_c'];?> </span>
                </div>
              </div>
            </fieldset>
            <?}?>
          </div>
          <div class="col-md-8">
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['p_product_group'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold">
                  <span><?=$tpl->data['product_group_bk'];?> </span>
                  <span> <?=$tpl->data['search_pg_name'];?></span>
                </div>
              </div>
            </fieldset>
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['p_manufacture'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold" style="margin: 0;">
                  <span><?=$tpl->data['product_manufacture_c'];?> </span>
                </div>
              </div>
            </fieldset>
            <?if(\core\ezy::$theme_key != "nms") {?>
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['p_supplier'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold" style="margin: 0;">
                  <span><?=$tpl->data['product_supplier_c'];?> </span>
                </div>
              </div>
            </fieldset>
            <?}?>
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['p_sku'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold" style="margin: 0;">
                  <span><?=$tpl->data['product_sku'];?> </span>
                </div>
              </div>
            </fieldset>
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['p_barcode'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold" style="margin: 0;">
                  <span><?=$tpl->data['product_barcode'];?> </span>
                </div>
              </div>
            </fieldset>
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['p_guarantee'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold" style="margin: 0;">
                  <span><?=$tpl->data['product_guarantee_default_c'];?> </span>
                </div>
              </div>
            </fieldset>
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['p_product_option'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold" style="margin: 0;">
                  <span><?=$tpl->data['product_option_c'];?> </span>
                </div>
              </div>
            </fieldset>
          </div>
        </div>

        <div class="row">
          <div class="col-xl-12"><h5 class="m-t-lg with-border"><?=$CMS->lang['title_info_transaction'];?></h5></div>
          <div class="col-xl-6">
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['p_cycle'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold" style="margin: 0;">
                  <span><?=$tpl->data['product_cycle_c'];?> </span>
                </div>
              </div>
            </fieldset>
          </div>
          <div class="col-xl-6" style="display: <?=$tpl->css_hide;?>">
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['p_tax'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold" style="margin: 0;">
                  <span><?=$tpl->data['product_tax'];?> %</span>
                </div>
              </div>
            </fieldset>   
          </div>
          <div class="col-xl-6">
            <fieldset class="form-group">
              <div class="display-flex">
                <label class="form-control-label2"><?=$CMS->lang['p_price'];?>: </label>
                <div class="form-control-span2"> 
                  <div class="form-label semibold" style="margin: 0;">
                    <span><?=$tpl->data['product_price_c'];?> </span>
                  </div>
                </div>
              </div>
            </fieldset>
          </div>
          <div class="col-xl-6">
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['p_price_old'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold" style="margin: 0;">
                  <span><?=$tpl->data['product_price_old_c'];?> </span>
                </div>
              </div>
            </fieldset>
          </div>
          <div class="col-xl-6">
            <fieldset class="form-group">
              <div class="display-flex">
                <label class="form-control-label2"><?=$CMS->lang['p_price_original'];?>: </label>
                <div class="form-control-span2"> 
                  <div class="form-label semibold" style="margin: 0;">
                    <span><?=$tpl->data['product_price_original_c'];?> </span>
                  </div>
                </div>
              </div>
            </fieldset>
          </div>
          <div class="col-xl-6">
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['p_price_sell'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold" style="margin: 0;">
                  <span><?=$tpl->data['product_price_sell_c'];?> </span>
                </div>
              </div>
            </fieldset>
          </div>
        </div>
      </div>
      <div class="col-md-6" >
        <?
        if( $CMS->vars['translations'] )
        {
          foreach( $CMS->vars['translations'] as $langCode => $langName ) 
          { 
        ?>
        <fieldset class="form-group" lang="<?=$langCode;?>">
          <label class="form-control-label2"><?=$CMS->lang['p_description'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <divtext class="clearfix"><?=$tpl->data['product_description'][$langCode];?></divtext>
            </div>
          </div>
        </fieldset>
        
        <?}}else{?>
        <fieldset class="form-group">
          <label class="form-control-label2"><?=$CMS->lang['p_description'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold" style="border: 1px solid rgb(236, 236, 236);border-radius: 3px;padding: 15px;">
              <divtext class="clearfix"><?=$tpl->data['product_description'];?></divtext>
            </div>
          </div>
        </fieldset>
        <?}?>

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
    </div>

    <div class="row">
      <div class="col-md-12">
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
                    <li class="nav-item"><a class="nav-link active" href="#p_information_1_<?=$langCode;?>" role="tab" data-toggle="tab"><?=$CMS->lang['p_information_1'];?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#p_information_2_<?=$langCode;?>" role="tab" data-toggle="tab"><?=$CMS->lang['p_information_2'];?></a></li>
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
                  <li class="nav-item"><a class="nav-link active" href="#p_information_1" role="tab" data-toggle="tab"><?=$CMS->lang['p_information_1'];?></a></li>
                  <li class="nav-item"><a class="nav-link" href="#p_information_2" role="tab" data-toggle="tab"><?=$CMS->lang['p_information_2'];?></a></li>
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
    </div>
  </figure>
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
 
<script type="text/javascript">
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