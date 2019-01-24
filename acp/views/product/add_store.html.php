<div id="box_add_store" class="popup_add_store mfp-hide">
    <p class="title_add change_title" style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;"><?=$CMS->lang['store_add'];?></p>

    <form id="add_store_form" name="add_store_form">
        <input type="hidden" name="checkReturn" value="" />
      <ul class="list_field_supplier">
        <!-- error msg -->
        <li style="min-height: 0px;"><p class="error_msg" style="display:none;color: red;"></p></li>

        <li class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <h5 class="section-title no-pt"><?=$CMS->lang['title_basic'];?></h5>
            <fieldset class="form-group" style="position: relative;">
              <label class="form-label" ><?=$CMS->lang['store_text_name'];?><span style="color:red"> (*)</span></label>
              <input  class="form-control" name="store_name" id="store_name" size="45" type="text" value="" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['store_err_title'];?>"> 
              <input name="store_id" type="hidden" value="" />
            </fieldset>
            <div class="row">
              <fieldset class="form-group">
                <div class="col-md-12">
                  <label class="form-label" ><?=$CMS->lang['store_address'];?></label>
                  <input  class="form-control" name="store_address" id="store_address" type="text" value="">  
                </div>
              </fieldset>
            </div>
            <div class="row">
              <fieldset class="form-group">
                <div class="col-md-6">
                  <label class="form-label" ><?=$CMS->lang['city_id'];?></label>
                  <select name="city_id" id="city_id" placeholder="<?=$CMS->lang['city_id'];?>" defaultvalue="" class="form-control auto_select" ><?=$CMS->global->get_optioncity();?></select>
                </div>
                <div class="col-md-6">
                  <label class="form-label" ><?=$CMS->lang['store_phone'];?></label>
                  <input  class="form-control inputPhone" name="store_phone" id="store_phone" size="45" type="text" value=""> 
                </div>
              </fieldset>
            </div>
            <fieldset class="form-group">
              <label class="form-label"><?=$CMS->lang['googlemap_code'];?></label>
              <p class="typeahead-field">
                <span class="typeahead-query">
                  <textarea name="googlemap_code" id="googlemap_code" rows="3" cols="50" class="form-control" style="height: 120px;"></textarea>
                </span>
              </p>
            </fieldset>
            <fieldset class="form-group">
              <label class="form-label" ><?=$CMS->lang['store_type'];?></label>
              <div class="clearfix">
                <div class='radio w25'><input type="radio" class="input_radio" name="store_type" value="1" id='radio-1' checked="checked" onclick="update_type_store(1)" defaultvalue="<?=$data['store_type'];?>"><label for='radio-1'><?=$CMS->lang['store_type_1'];?></label></div>
                <div class='radio w25'> <input type="radio" id='radio-2'   class="input_radio" name="store_type" value="2" onclick="update_type_store(2)" defaultvalue=""> <label for='radio-2'><?=$CMS->lang['store_type_2'];?></label></div>
              </div>
              <div name="store_type_des_1_div" id="store_type_des_1_div" style="display: none;color: red; font-size: 12px;"><?=$CMS->lang['store_type_des_1'];?></div>
              <div name="store_type_des_2_div" id="store_type_des_2_div" style="display: none;color: red; font-size: 12px;"><?=$CMS->lang['store_type_des_2'];?></div>
            </fieldset>
        </li>
        <li class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <h5 class="section-title no-pt"><?=$CMS->lang['title_advanced'];?></h5> 
          <div class="row">
            <fieldset class="form-group">
              <div class="col-md-6">
                <label class="form-label" ><?=$CMS->lang['store_url'];?></label>
                <input  class="form-control" name="store_backend_url" id="store_backend_url" type="text" value="" placeholder="Ex: abc.shopify.com">
              </div>
              <div class="col-md-6">
              <label class="form-label" ><?=$CMS->lang['store_backend_type'];?></label>
              <select name="store_backend_type" defaultvalue="" class="form-control auto_select">
                <option value=""><?=$CMS->lang['title_choose_plz'];?></option>
                <?foreach ($tpl->store_backend_type as $type){?>
                <option value="<?=$type;?>"><?=$type;?></option>
                <?}?>
              </select>
            </div>
          </fieldset>
        </div>
        <div class="row">
          <fieldset class="form-group">
            <div class="col-md-12">
              <label class="form-label" ><?=$CMS->lang['store_backend_key'];?></label>
              <input  class="form-control" name="store_backend_key" id="store_backend_key" type="text" value="">
            </div>
          </fieldset>
          <fieldset class="form-group">
            <div class="col-md-12">
              <label class="form-label" ><?=$CMS->lang['store_backend_secret'];?></label>
              <input  class="form-control" name="store_backend_secret" id="store_backend_secret" type="text" value="">
            </div>
          </fieldset>
        </div>
        <div class="row">
          <fieldset class="form-group">
            <div class="col-md-6">
              <label class="form-label" ><?=$CMS->lang['store_use_is_sync'];?></label>
              <div class="radio" style="display: inline-block; margin-right: 20px;">
                <input type="radio" name="store_backend_sync" id="sync-1" value="1">
                <label for="sync-1"><?=$CMS->lang['yes'];?></label>
              </div>
              <div class="radio" style="display: inline-block;">
                <input type="radio" name="store_backend_sync" id="sync-2" value="0" checked="checked">
                <label for="sync-2"><?=$CMS->lang['no'];?></label>
              </div>
            </div>
          </fieldset>
        </div>
        </li>
        <li class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12" style="min-height: auto;">
          <fieldset class="form-group">
            <label class="form-control-label"></label>
            <div class="typeahead-field"> 
              <span class="typeahead-query change_action">
                <input class="btn btn_add_supplier" type="button" value="<?=$CMS->lang['store_add'];?>"/>
              </span>
            </div>
          </fieldset>
        </li>
      </ul>

      <a class="btn_add_store" onclick="call_ajax_add_store();" style="display:none"><?=$CMS->lang['store_add'];?></a>
      <a class="btn_edit_do_store" onclick="call_ajax_edit_store();" style="display:none"><?=$CMS->lang['store_update'];?></a>
    </form>
  </div>

<script>
  function update_type_store(type)
  {
      if( type == 1 )
      {
          $('#store_type_des_1_div').show();                                
          $('#store_type_des_2_div').hide();                                
      }
      else
      {
          $('#store_type_des_1_div').hide();                                
          $('#store_type_des_2_div').show();                                
      }
  }
</script>