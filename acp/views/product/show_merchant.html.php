<link rel="stylesheet" type="text/css" href='/acp/assets/css/orders.css'>
<div class="container-fluid order">
  <!-- Page Content -->
  <div class="manage-container main_form">
    <section class="tabs-section">
      
      <!-- header -->
      <h5 class="top-section-title col-xl-12"><?=$CMS->lang['p_info'];?></h5>
      <a class="btn_backlist" href="/acp/?site=<?=$CMS->input['site'];?>" title=""><span class="font-icon font-icon-del"></span></a>
      
      <!-- Tabs nav-->
      <div class="tabs-section-nav tabs-section-nav-inline order-tabs-section-nav">
        <ul class="nav" role="tablist">
          <li class="nav-item"><a class="nav-link active" href="#tabs-4-tab-1" role="tab" data-toggle="tab" aria-expanded="true"> <?=$CMS->lang['title_overview_product'];?> </a></li>
          <li class="nav-item"><a class="nav-link" href="#tabs-4-tab-2" role="tab" data-toggle="tab" aria-expanded="false"> <?=$CMS->lang['title_info_product'];?> </a></li>
          <li class="nav-item"><a class="nav-link" href="#tabs-4-tab-4" role="tab" data-toggle="tab" aria-expanded="false"> <?=$CMS->lang['p_gallery'];?> </a></li>
          
          <!-- <li class="nav-item"><a class="nav-link" href="#tabs-4-tab-3" role="tab" data-toggle="tab" aria-expanded="false"> <?=$CMS->lang['title_attribute'];?> & <?=$CMS->lang['title_shipping'];?> </a></li>
          <li class="nav-item"><a class="nav-link" href="#tabs-4-tab-6" role="tab" data-toggle="tab" aria-expanded="false"> Variants </a></li> -->

          <li class="nav-item"><a class="nav-link" href="#tabs_shipping_fee" role="tab" data-toggle="tab" aria-expanded="false"><?=$CMS->lang['title_ship_fee'];?></a></li>
        </ul>
      </div>

      <!-- Tabs content -->
      <div class="tab-content order-tab-content">

        <!-- tabs-4-tab-1 -->
        <div role="tabpanel" class="tab-pane fade in active show" id="tabs-4-tab-1" aria-expanded="true">
          <div class="row"><?=$CMS->global->languageTab('langTab');?></div>
          <div class="box-typical box-typical-info" style="min-height: 500px;padding: 0 15px 15px;">
            
            <h5 class="m-t-md with-border"><?=$CMS->lang['title_overview_product'];?></h5>
            <div class="row">
              <div class="col-md-5">
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
                  <label class="form-control-label2"><?=$CMS->lang['title_choose_product_parent'];?>: </label>
                  <div class="form-control-span2"> 
                    <div class="form-label semibold">
                      <span><?=$tpl->data['parent_id_c'];?> </span>
                    </div>
                  </div>
                </fieldset>
              </div>

              <div class="col-md-7">
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
              </div>
            </div>

            <h5 class="m-t-md with-border"><?=$CMS->lang['title_info_transaction'];?></h5>
            <div class="row">
              <div class="col-xl-5 col-md-5">
                <div class="row">
                  <div class="col-xl-6 col-md-6">
                    <fieldset class="form-group display-flex">
                      <label class="form-control-label2"><?=$CMS->lang['p_cycle'];?>: </label>
                      <div class="form-control-span2"> 
                        <div class="form-label semibold" style="margin: 0;">
                          <span><?=$tpl->data['product_cycle_c'];?> </span>
                        </div>
                      </div>
                    </fieldset>
                  </div>
                  <div class="col-xl-6 col-md-6">
                    <div class="clearfix" style="display: <?=$tpl->css_hide;?>">
                      <fieldset class="form-group display-flex">
                        <label class="form-control-label2"><?=$CMS->lang['p_tax'];?>: </label>
                        <div class="form-control-span2"> 
                          <div class="form-label semibold" style="margin: 0;">
                            <span><?=$tpl->data['product_tax']*1;?> %</span>
                          </div>
                        </div>
                      </fieldset>
                    </div>
                  </div>
                </div>

                <?if($CMS->vars['enabled_commission']) {?>
                <fieldset class="form-group display-flex">
                  <label class="form-control-label2"><?=$CMS->lang['gcommission'];?>: </label>
                  <div class="form-control-span2"> 
                    <div class="form-label semibold" style="margin: 0;">
                      <span><?=$tpl->data['product_commission']*1;?></span>
                    </div>
                  </div>
                </fieldset>
                <?}?>
              </div>
              <div class="col-xl-7 col-md-7">
                <?if( $CMS->vars['addon_goods_enable'] == 1 ){?>
                <div class="row">
                  <div class="col-xl-6 col-md-6">
                    <fieldset class="form-group">
                      <div class="display-flex">
                        <label class="form-label" >
                          <span class="form-control-label2"><?=$CMS->lang['p_price'];?></span>
                          <span class="question">
                            <i class="fa fa-question-circle" aria-hidden="true"></i>
                            <p class="box_answer" style="display: none;"><?=$CMS->lang['p_price_note'];?></p>
                          </span>
                        </label>
                        <div class="form-control-span2"> 
                          <div class="form-label semibold" style="margin: 0;">
                            <span><?=$tpl->data['product_price_c'];?> </span>
                          </div>
                        </div>
                      </div>
                    </fieldset>
                  </div>
                  <div class="col-xl-6 col-md-6">
                    <fieldset class="form-group">
                      <div class="display-flex">
                        <label class="form-label" >
                          <span class="form-control-label2"><?=$CMS->lang['p_price_original'];?></span>
                          <span class="question">
                            <i class="fa fa-question-circle" aria-hidden="true"></i>
                            <p class="box_answer" style="display: none;"><?=$CMS->lang['p_price_original_note'];?></p>
                          </span>
                        </label>
                        <div class="form-control-span2"> 
                          <div class="form-label semibold" style="margin: 0;">
                            <span><?=$tpl->data['product_price_original_c'];?> </span>
                          </div>
                        </div>
                      </div>
                    </fieldset>
                  </div>
                </div>
                <?}?>
                <div class="row">
                  <div class="col-xl-6 col-md-6">
                    <fieldset class="form-group display-flex">
                      <label class="form-label" >
                        <span class="form-control-label2"><?=$CMS->lang['p_price_sell'];?></span>
                        <span class="question">
                          <i class="fa fa-question-circle" aria-hidden="true"></i>
                          <p class="box_answer" style="display: none;"><?=$CMS->lang['p_price_sell_note'];?></p>
                        </span>
                      </label>
                      <div class="form-control-span2">
                        <div class="form-label semibold" style="margin: 0;">
                          <span><?=$tpl->data['product_price_sell_c'];?> </span>
                        </div>
                      </div>
                    </fieldset>
                  </div>
                  <div class="col-xl-6 col-md-6">
                    <fieldset class="form-group display-flex">
                      <label class="form-label" >
                        <span class="form-control-label2"><?=$CMS->lang['p_price_sale'];?></span>
                        <span class="question">
                          <i class="fa fa-question-circle" aria-hidden="true"></i>
                          <p class="box_answer" style="display: none;"><?=$CMS->lang['p_price_sale_note'];?></p>
                        </span>
                      </label>
                      <div class="form-control-span2"> 
                        <div class="form-label semibold" style="margin: 0;">
                          <span><?=$tpl->data['product_price_sale_c'];?> </span>
                        </div>
                      </div>
                    </fieldset>
                  </div>
                </div>
              </div>
            </div>

            <?if( $CMS->vars['addon_goods_enable'] == 1 AND $tpl->p_type == 0 ){?>
            <h5 class="m-t-md with-border"><?=$CMS->lang['title_store_config'];?></h5>
            <div class="row">
              <div class="col-md-12">
                <fieldset class="form-group display-flex">
                  <label class="form-control-label2"><?=$CMS->lang['store_id'];?>: </label>
                  <div class="form-control-span2"> 
                    <div class="form-label semibold" style="margin: 0;">
                      <span><?=$tpl->data['store_name'];?> </span>
                    </div>
                  </div>
                </fieldset>
              </div>
              <div class="col-md-4">
                <fieldset class="form-group display-flex">
                  <label class="form-control-label2"><?=$CMS->lang['p_first_remain'];?>: </label>
                  <div class="form-control-span2"> 
                    <div class="form-label semibold" style="margin: 0;">
                      <span><?=$tpl->data['product_first_remain'];?> </span>
                      <?if($CMS->permit['inventory_add'] == true){?>
                      <a href='<?=$CMS->vars['root_domain'];?>/?site=inventory'><?=$CMS->lang['ajust_inventory'];?></a>
                      <?}?>
                    </div>
                  </div>
                </fieldset>
              </div>
              <div class="col-md-4">
                <fieldset class="form-group display-flex">
                  <label class="form-control-label2"><?=$CMS->lang['p_stock_available'];?>: </label>
                  <div class="form-control-span2"> 
                    <div class="form-label semibold" style="margin: 0;">
                      <span><?=$tpl->data['product_stock_available_c'];?> </span>
                    </div>
                  </div>
                </fieldset>
              </div>  
              <div class="col-md-4">
                <fieldset class="form-group display-flex">
                  <label class="form-control-label2"><?=$CMS->lang['p_show_instock'];?>: </label>
                  <div class="form-control-span2"> 
                    <div class="form-label semibold" style="margin: 0;">
                      <span><?=$tpl->data['product_show_instock_c'];?> </span>
                    </div>
                  </div>
                </fieldset>
              </div>  
            </div>
            <?}?>
          </div>
        </div> <!-- End tabs-4-tab-1 -->

        <!-- tabs-4-tab-2 -->
        <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-2" aria-expanded="false">
          <div class="box-typical box-typical-info" style="min-height: 500px;padding: 0 15px 15px;">
            
            <h5 class="m-t-md with-border"><?=$CMS->lang['title_information_product'];?></h5>
            <div class="row">
              <div class="col-md-8 col-lg-9">
                <div class="row">
                  <div class="col-md-6">
                    <fieldset class="form-group display-flex">
                      <label class="form-control-label2"><?=$CMS->lang['p_manufacture'];?>: </label>
                      <div class="form-control-span2"> 
                        <div class="form-label semibold" style="margin: 0;">
                          <span><?=$tpl->data['product_manufacture_c'];?> </span>
                        </div>
                      </div>
                    </fieldset>
                  </div>
                  <div class="col-md-6">
                    <fieldset class="form-group display-flex">
                      <label class="form-control-label2"><?=$CMS->lang['p_supplier'];?>: </label>
                      <div class="form-control-span2"> 
                        <div class="form-label semibold" style="margin: 0;">
                          <span><?=$tpl->data['product_supplier_c'];?> </span>
                        </div>
                      </div>
                    </fieldset>
                  </div>
                  <div class="col-md-6">
                    <fieldset class="form-group display-flex">
                      <label class="form-control-label2"><?=$CMS->lang['p_sku'];?>: </label>
                      <div class="form-control-span2"> 
                        <div class="form-label semibold" style="margin: 0;">
                          <span><?=$tpl->data['product_sku'];?> </span>
                        </div>
                      </div>
                    </fieldset>
                  </div>
                  <div class="col-md-6">
                    <fieldset class="form-group display-flex">
                      <label class="form-control-label2"><?=$CMS->lang['p_guarantee'];?>: </label>
                      <div class="form-control-span2"> 
                        <div class="form-label semibold" style="margin: 0;">
                          <span><?=$tpl->data['product_guarantee_default_c'];?> </span>
                        </div>
                      </div>
                    </fieldset>
                  </div>
                  <div class="col-md-6">
                    <fieldset class="form-group display-flex">
                      <label class="form-control-label2"><?=$CMS->lang['p_barcode'];?>: </label>
                      <div class="form-control-span2"> 
                        <div class="form-label semibold" style="margin: 0;">
                          <span><?=$tpl->data['product_barcode'];?> </span>
                        </div>
                      </div>
                    </fieldset>
                  </div>
                  <div class="col-md-6">
                    <fieldset class="form-group display-flex">
                      <label class="form-control-label2"><?=$CMS->lang['p_show'];?>: </label>
                      <div class="form-control-span2"> 
                        <div class="form-label semibold" style="margin: 0;">
                          <span><?=$tpl->data['product_show_c'];?> </span>
                        </div>
                      </div>
                    </fieldset>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
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
              </div>
              <div class="col-md-4 col-lg-3">
                <fieldset class="form-group">
                  <label class="form-control-label2"><?=$CMS->lang['p_avartar'];?>: </label>
                  <div class="form-control-span2"> 
                    <div class="form-label semibold">
                      <img src="<?=$tpl->data['product_image_c'];?>" width="150" />
                    </div>
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

            <h5 class="m-t-md with-border"><?=$CMS->lang['title_seo_product'];?></h5>
            <div class="row">
              <div class="col-md-6">
                <fieldset class="form-group display-flex">
                  <label class="form-control-label2">Meta Title: </label>
                  <div class="form-control-span2"> 
                    <div class="form-label semibold" style="margin: 0;">
                      <span><?=$tpl->data['meta_title'];?> </span>
                    </div>
                  </div>
                </fieldset>
              </div>
              <div class="col-md-6">
                <fieldset class="form-group display-flex">
                  <label class="form-control-label2">Meta Keywords: </label>
                  <div class="form-control-span2"> 
                    <div class="form-label semibold" style="margin: 0;">
                      <span><?=$tpl->data['meta_keywords'];?> </span>
                    </div>
                  </div>
                </fieldset>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <fieldset class="form-group display-flex">
                  <label class="form-control-label2">Meta Description: </label>
                  <div class="form-control-span2"> 
                    <div class="form-label semibold" style="margin: 0;">
                      <span><?=$tpl->data['meta_description'];?> </span>
                    </div>
                  </div>
                </fieldset>
              </div>
            </div>
          </div>
        </div><!-- End tabs-4-tab-2 -->

        <!-- tabs-4-tab-3 -->
        <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-3" aria-expanded="false">
          <div class="box-typical box-typical-info" style="min-height: 240px;padding: 0 15px 15px;">
            <div class="row">
              <div class="col-xl-12">
                <h5 class="section-title no-pt"><?=$CMS->lang['title_attr_list'];?></h5>
                <div class="result_attribute">
                  <?if( ! empty($tpl->data['product_attribute_custom_c']) AND is_array($tpl->data['product_attribute_custom_c']) ){?>
                  <div class="row">
                    <?foreach( $tpl->data['product_attribute_custom_c'] as $attr_name => $attr_options ){?>
                    <div class="col-md-3 col-sm-4">
                      <fieldset class="form-group display-flex">
                        <label class="form-control-label2"><?=$attr_name;?>: </label>
                        <div class="form-control-span2"> 
                          <div class="form-label semibold" style="margin: 0;">
                            <span><?=$attr_options;?></span>
                          </div>
                        </div>
                      </fieldset>
                    </div>
                    <?}?>
                  </div>
                  <?}else{?>
                  <span>...</span>
                  <?}?>
                </div>
              </div>
              <div class="col-xl-12">
                <h5 class="m-t-md with-border"><?=$CMS->lang['title_information_for_shipping'];?></h5>
                <div class="col-xl-3">
                  <fieldset class="form-group display-flex">
                    <label class="form-control-label2">
                      <span><?=$CMS->lang['title_weight'];?></span>
                      <span class="question">
                        <i class="fa fa-question-circle" aria-hidden="true"></i>
                        <p class="box_answer" style="display: none;"><?=$CMS->lang['description_weight'];?></p>
                      </span>
                      <span>: </span>
                    </label>
                    <div class="form-control-span2"> 
                      <div class="form-label semibold" style="margin: 0;">
                        <span><?=$tpl->data['product_attribute_c']['weight'];?> </span>
                      </div>
                    </div>
                  </fieldset>
                </div>
                <div class="col-xl-3">
                  <fieldset class="form-group display-flex">
                    <label class="form-control-label2"><?=$CMS->lang['title_length'];?>: </label>
                    <div class="form-control-span2"> 
                      <div class="form-label semibold" style="margin: 0;">
                        <span><?=$tpl->data['product_attribute_c']['length'];?> </span>
                      </div>
                    </div>
                  </fieldset>
                </div>
                <div class="col-xl-3">
                  <fieldset class="form-group display-flex">
                    <label class="form-control-label2"><?=$CMS->lang['title_width'];?>: </label>
                    <div class="form-control-span2"> 
                      <div class="form-label semibold" style="margin: 0;">
                        <span><?=$tpl->data['product_attribute_c']['width'];?> </span>
                      </div>
                    </div>
                  </fieldset>
                </div>
                <div class="col-xl-3">
                  <fieldset class="form-group display-flex">
                    <label class="form-control-label2"><?=$CMS->lang['title_height'];?>: </label>
                    <div class="form-control-span2"> 
                      <div class="form-label semibold" style="margin: 0;">
                        <span><?=$tpl->data['product_attribute_c']['height'];?> </span>
                      </div>
                    </div>
                  </fieldset>
                </div>
              </div>
            </div>
          </div>
        </div><!-- End tabs-4-tab-3 -->

        <!-- tabs-4-tab-4 -->
        <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-4" aria-expanded="false">
          <section class="box-typical box-typical-margin-top box-typical-full-height-with-header" style="min-height: 240px;padding: 0 15px 15px;">
            <header class="box-typical-header box-typical-header-bordered">
              <div class="tbl-row">
                <div class="tbl-cell tbl-cell-title">
                  <h3>Gallery Listing</h3>
                </div>
              </div>
            </header>
            <div class="box-typical-body-1">
              <div class="gallery-grid">
                <div class="box_img_upload">
                  <?
                  if( ! empty($tpl->data['product_gallery_c']) AND is_array($tpl->data['product_gallery_c']) )
                  {
                    foreach ( $tpl->data['product_gallery_c'] as $image ) 
                    { 
                  ?>
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
                  </div>
                  
                  <?}}else{?>
                  <span>...</span>
                  <?}?>
                </div>
              </div>
            </div>
          </section>
        </div>

        <!-- Tabs-4-tab-6 -->
        <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-6" aria-expanded="false">
          <section class="box-typical box-typical-margin-top box-typical-full-height-with-header" style="min-height: 240px;padding: 0 15px 15px;">
            <header class="box-typical-header box-typical-header-bordered">
              <div class="tbl-row">
                <div class="tbl-cell tbl-cell-title">
                  <h3>Variants Listing</h3>
                </div>
              </div>
            </header>

            <?if( is_array($tpl->data['var_content_c']) ){?>
            <section class="box-typical scrollable">
              <div class="box-typical-body-2">
                <div class="table-responsive">
                  <table class="table table-hover table-zp table-zp-top">
                    <thead>
                      <tr>
                        <th width="5%" class="three-dots">ID</th>
                        <th width="20%" class="three-dots"><?=$CMS->lang['var_title'];?></th>
                        <th width="15%" class="three-dots"><?=$CMS->lang['var_sku'];?></th>
                        <th width="10%" class="three-dots"><?=$CMS->lang['var_option'];?> 1</th>
                        <th width="10%" class="three-dots"><?=$CMS->lang['var_option'];?> 2</th>
                        <th width="10%" class="three-dots"><?=$CMS->lang['var_option'];?> 3</th>
                        <th width="10%" class="three-dots"><?=$CMS->lang['var_price'];?></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?foreach ($tpl->data['var_content_c'] as $var){?>
                      <tr>
                        <td>#<?=$var['var_id'];?></td>
                        <td class="threedots"><?=$var['var_title'];?></td>
                        <td class="threedots"><?=$var['var_sku'];?></td>
                        <td class="threedots"><?=$var['var_option1'];?></td>
                        <td class="threedots"><?=$var['var_option2'];?></td>
                        <td class="threedots"><?=$var['var_option3'];?></td>
                        <td class="threedots"><?=$CMS->class->input->currency($var['var_price']);?></td>
                      </tr>
                      <?}?>
                    </tbody>
                  </table>            
                </div>
              </div><!--.box-typical-body-->
            </section>
            
            <?}else{?>
            <span>...</span>
            <?}?>

          </section>
        </div><!-- End tabs-4-tab-6 -->

        <!-- Shipping & fee -->
        <div role="tabpanel" class="tab-pane fade" id="tabs_shipping_fee" aria-expanded="false">
          <?=\core\ezy::render('show_shipping_fee', 'product');?>
        </div><!-- End tab shipping & fee -->

      </div><!--.tab-content-->
    </section>
  </div>
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
</div>
<script type="text/javascript">
  $(document).ready(function(){
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

    <?if(isset($CMS->input['tab'])){?>
    // Active tab
    $('.nav-item a[href="#<?=$CMS->input['tab'];?>"]').trigger('click');
    <?}?>
  });

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
</script>

<!-- Logs -->
<?=$tpl->logs;?>

<!-- modal shipping fee add/edit -->
<?=\core\ezy::render('add_shipping_fee', 'product');?>
<?=\core\ezy::render('edit_shipping_fee', 'product');?>