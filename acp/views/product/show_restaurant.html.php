<section class="add_form main_form">
  <!-- header -->
  <figure class="heading">
    <h3><?=$CMS->lang['p_info'];?></h3>
    <a class="btn_backlist" href="/acp/?site=<?=$CMS->input['site'];?>" title=""><span class="font-icon font-icon-del"></span></a>
  </figure>

  <!-- Lang tab -->
  <?=$CMS->global->languageTab('langTab');?>

  <!--  -->
  <figure class="box-typical box-typical box-typical-padding border">
    <div class="row">
      <div class="col-md-6">
        <div class="row">
          <div class="col-xl-4">
            <fieldset class="form-group">
              <label class="form-control-label2"><?=$CMS->lang['p_avartar'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold">
                  <img src="<?=$tpl->data['product_image_c'];?>" width="150" />
                </div>
              </div>
            </fieldset>
          </div>
          <div class="col-xl-8">
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
            <fieldset class="form-group display-flex">
              <label class="form-control-label2"><?=$CMS->lang['p_show'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold" style="margin: 0;">
                  <span><?=$tpl->data['product_show_c'];?> </span>
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
              <label class="form-control-label2"><?=$CMS->lang['p_price_old'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold" style="margin: 0;">
                  <span><?=$tpl->data['product_price_old_c'];?> </span>
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
              <label class="form-control-label2"><?=$CMS->lang['title_suffix_price'];?>: </label>
              <div class="form-control-span2"> 
                <div class="form-label semibold" style="margin: 0;">
                  <span><?=$tpl->data['product_up'];?></span>
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

<!-- Logs -->
<?=$tpl->logs;?>