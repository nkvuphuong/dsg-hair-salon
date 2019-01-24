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
              <label class="form-control-label2"><?=$CMS->lang['real_name'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold">
                  <span><?=$tpl->data['product_name'][$langCode];?></span>
                  <span><?=$tpl->data['search_p_name'];?></span>
                </div>
              </div>
            </fieldset>
            
            <?}}else{?>
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['real_name'];?>: </label>
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
          </div>
          <div class="col-md-8">
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['real_product_group'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold">
                  <span><?=$tpl->data['product_group_bk'];?> </span>
                </div>
              </div>
            </fieldset>
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['real_option_type'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold" style="margin: 0;">
                  <span><?=$tpl->data['product_real_type_c'];?> </span>
                </div>
              </div>
            </fieldset>
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['real_option'];?>: </label>
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
              <label class="form-control-label2"><?=$CMS->lang['real_acreage'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold">
                  <?=$tpl->data['product_attribute_c']['acreage'];?>
                </div>
              </div>
            </fieldset>
          </div>
          <div class="col-xl-6">
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['p_mobile'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold">
                  <?=$tpl->data['product_attribute_c']['real_phone'];?>
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
          <div class="col-xl-6">
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['real_suffix_price'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold" style="margin: 0;">
                  <span><?=$CMS->lang["title_unit_{$tpl->data['product_up']}"];?></span>
                </div>
              </div>
            </fieldset>
          </div>
        </div>
      </div><!-- col-md-6 -->
    
      <div class="col-md-6" >
        <div class="row">
          <div class="col-xl-6">
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['real_city'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold">
                  <?=$tpl->data['product_attribute_c']['city'];?>
                </div>
              </div>
            </fieldset>
          </div>
          <div class="col-xl-6">
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['real_district'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold">
                  <?=$tpl->data['product_attribute_c']['district'];?>
                </div>
              </div>
            </fieldset>
          </div>
          <div class="col-xl-6">
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['real_Wards'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold">
                  <?=$tpl->data['product_attribute_c']['wards'];?>
                </div>
              </div>
            </fieldset>
          </div>
          <div class="col-xl-6">
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['real_street'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold">
                  <?=$tpl->data['product_attribute_c']['street'];?>
                </div>
              </div>
            </fieldset>
          </div>
          <div class="col-xl-12">
            <fieldset class="form-group">
              <label class="form-control-label2"><?=$CMS->lang['real_map'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold" style="border: 1px solid rgb(236, 236, 236);border-radius: 3px;padding: 15px;">
                  <divtext class="clearfix"><?=$tpl->data['product_attribute_c']['map'];?></divtext>
                </div>
              </div>
            </fieldset>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <?
        if( $CMS->vars['translations'] )
        {
          foreach( $CMS->vars['translations'] as $langCode => $langName ) 
          { 
        ?>
        <fieldset class="form-group" lang="<?=$langCode;?>">
          <label class="form-control-label2"><?=$CMS->lang['real_description'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <divtext class="clearfix"><?=$tpl->data['product_description'][$langCode];?></divtext>
            </div>
          </div>
        </fieldset>
        
        <?}}else{?>
        <fieldset class="form-group">
          <label class="form-control-label2"><?=$CMS->lang['real_description'];?>: </label>
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
                    <li class="nav-item"><a class="nav-link active" href="#p_information_1_<?=$langCode;?>" role="tab" data-toggle="tab"><?=$CMS->lang['p_content'];?></a></li>
                </ul>
            </div>
            <div class="tab-content" style="border: 1px solid rgb(236, 236, 236);border-radius: 0 0 3px 3pxinherit;padding: 15px;border-top: none;">
              <div role="tabpanel" class="tab-pane fade in active" id="p_information_1_<?=$langCode;?>">
                <divtext class="clearfix"><?=$tpl->data['product_information_1'][$langCode];?></divtext>
              </div>
            </div>
          </section>
        </div>

        <?}}else{?>
        <section class="tabs-section">
          <div class="tabs-section-nav tabs-section-nav-inline">
              <ul class="nav" role="tablist">
                  <li class="nav-item"><a class="nav-link active" href="#p_information_1" role="tab" data-toggle="tab"><?=$CMS->lang['p_content'];?></a></li>
              </ul>
          </div>
          <div class="tab-content" style="border: 1px solid rgb(236, 236, 236);border-radius: 0 0 3px 3pxinherit;padding: 15px;border-top: none;">
            <div role="tabpanel" class="tab-pane fade in active" id="p_information_1">
              <divtext class="clearfix"><?=$tpl->data['product_information_1'];?></divtext>
            </div>
          </div>
        </section>
        <?}?>
      </div>
      
      <!-- Thuoc tinh bds -->
      <div class="col-xl-12">
        <h5 class="m-t-lg with-border"><?=$CMS->lang['title_attribute_realestate'];?></h5>
        <div class="col-xl-3">
          <fieldset class="form-group display-flex">
            <label class="form-control-label2"><?=$CMS->lang['real_facade'];?>: </label>
            <div class="form-control-span2"> 
              <div class="form-label semibold">
                <?=$tpl->data['product_attribute_c']['facade'];?>
              </div>
            </div>
          </fieldset>
        </div>
        <div class="col-xl-3">
          <fieldset class="form-group display-flex">
            <label class="form-control-label2"><?=$CMS->lang['real_entrance'];?>: </label>
            <div class="form-control-span2"> 
              <div class="form-label semibold">
                <?=$tpl->data['product_attribute_c']['entrance'];?>
              </div>
            </div>
          </fieldset>
        </div>
        <div class="col-xl-3">
          <fieldset class="form-group display-flex">
            <label class="form-control-label2"><?=$CMS->lang['real_direction'];?>: </label>
            <div class="form-control-span2"> 
              <div class="form-label semibold">
                <?=$tpl->data['product_attribute_c']['direction'];?>
              </div>
            </div>
          </fieldset>
        </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['real_direction_of_balcony'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=$tpl->data['product_attribute_c']['dbalcon'];?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['real_number_of_floors'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=$tpl->data['product_attribute_c']['nfloors'];?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['real_number_of_room'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=$tpl->data['product_attribute_c']['nroom'];?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['real_number_of_bedroom'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=$tpl->data['product_attribute_c']['nbedroom'];?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['real_number_of_bathrooms'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=$tpl->data['product_attribute_c']['nbathrooms'];?>
            </div>
          </div>
        </fieldset>
      </div>
    </div>
    <div class="col-xl-12">
      <label class="form-label title_label_attr ml-15"><?=$CMS->lang['real_internet'];?></label>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_internet_1'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('optical_fiber', $tpl->data['product_attribute_c']['internet']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_internet_2'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('adsl', $tpl->data['product_attribute_c']['internet']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_internet_3'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('wifi', $tpl->data['product_attribute_c']['internet']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
    </div>

    <div class="col-xl-12">
      <label class="form-label title_label_attr ml-15" ><?=$CMS->lang['real_furniture'];?></label>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_furniture_1'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('air_conditioner', $tpl->data['product_attribute_c']['furniture']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_furniture_2'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('washing_machine', $tpl->data['product_attribute_c']['furniture']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_furniture_3'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('salon', $tpl->data['product_attribute_c']['furniture']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_furniture_4'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('wall_cabinets', $tpl->data['product_attribute_c']['furniture']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_furniture_5'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('kitchen', $tpl->data['product_attribute_c']['furniture']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_furniture_6'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('fridge', $tpl->data['product_attribute_c']['furniture']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_furniture_7'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('bed', $tpl->data['product_attribute_c']['furniture']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
    </div>

    <div class="col-xl-12">
      <label class="form-label title_label_attr ml-15" ><?=$CMS->lang['real_facilities_outside'];?></label>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_foutside_1'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('pool', $tpl->data['product_attribute_c']['foutside']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_foutside_2'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('balcony', $tpl->data['product_attribute_c']['foutside']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_foutside_3'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('terrace', $tpl->data['product_attribute_c']['foutside']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_foutside_4'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('bbq', $tpl->data['product_attribute_c']['foutside']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_foutside_5'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('gym', $tpl->data['product_attribute_c']['foutside']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_foutside_6'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('sport_area', $tpl->data['product_attribute_c']['foutside']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_foutside_7'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('cplay_area', $tpl->data['product_attribute_c']['foutside']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_foutside_8'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('gara', $tpl->data['product_attribute_c']['foutside']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_foutside_9'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('parking', $tpl->data['product_attribute_c']['foutside']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
    </div>

    <div class="col-xl-12">
      <label class="form-label title_label_attr ml-15" ><?=$CMS->lang['real_utilities_area'];?></label>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_uarea_1'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('park', $tpl->data['product_attribute_c']['utilities']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_uarea_2'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('hospital', $tpl->data['product_attribute_c']['utilities']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_uarea_3'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('bank', $tpl->data['product_attribute_c']['utilities']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_uarea_4'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('school', $tpl->data['product_attribute_c']['utilities']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_uarea_5'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('atm', $tpl->data['product_attribute_c']['utilities']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_uarea_6'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('coffee', $tpl->data['product_attribute_c']['utilities']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_uarea_7'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('dentistry', $tpl->data['product_attribute_c']['utilities']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_uarea_8'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('police_office', $tpl->data['product_attribute_c']['utilities']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_uarea_9'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('diner', $tpl->data['product_attribute_c']['utilities']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_uarea_10'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('restaurant', $tpl->data['product_attribute_c']['utilities']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_uarea_11'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('grocery', $tpl->data['product_attribute_c']['utilities']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_uarea_12'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('hotel', $tpl->data['product_attribute_c']['utilities']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_uarea_13'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('market', $tpl->data['product_attribute_c']['utilities']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_uarea_14'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('supermarket', $tpl->data['product_attribute_c']['utilities']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-3">
        <fieldset class="form-group display-flex">
          <label class="form-control-label2"><?=$CMS->lang['attr_uarea_15'];?>: </label>
          <div class="form-control-span2"> 
            <div class="form-label semibold">
              <?=in_array('bus_stop', $tpl->data['product_attribute_c']['utilities']) ? $CMS->lang['yes'] : $CMS->lang['no'];;?>
            </div>
          </div>
        </fieldset>
      </div>
    </div>
  </div>
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