  <div id="box_add_supplier" class="popup_add_supplier mfp-hide">
      <p class="title_add change_title" style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;"><?=$CMS->lang['supplier_add'];?></p>
      <form id="add_supplier_form" name="add_supplier_form">
          <input type="hidden" name="checkReturn" value="" />
        <ul class="list_field_supplier">
            <li style="min-height: 0px;"><p class="error_msg" style="display:none;"></p></li>

          <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
            <fieldset class="form-group">
              <label class="form-control-label row" for="supplier_title"><?=$CMS->lang['supplier_name'];?><font style="margin-left:5px;" color="#FF0000">(*)</font></label>
              <div class="typeahead-field"> 
                <span class="typeahead-query">
                  <input name="supplier_name" id="supplier_name" size="45" type="text" value="<?=$data['supplier_name'];?>" class="form-control">
                  <input name="supplier_id" type="hidden" value="" />

                </span>
              </div>
            </fieldset>
          </li>
          <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
            <fieldset class="form-group">
              <label class="form-control-label row" for="supplier_code"><?=$CMS->lang['supplier_code'];?></label>
              <div class="typeahead-field"> 
                <span class="typeahead-query">
                  <input name="supplier_code" id="supplier_code" size="45" type="text" value="<?=$data['supplier_code'];?>" class="form-control">
                </span>
              </div>
            </fieldset>
          </li>

          <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
            <fieldset class="form-group">
              <label class="form-control-label row" for="supplier_phone"><?=$CMS->lang['supplier_phone'];?></label>
              <div class="typeahead-field"> 
                <span class="typeahead-query">
                  <input onkeypress="return check_phone(event);" name="supplier_phone" id="supplier_phone" size="45" type="text" value="<?=$data['supplier_phone'];?>" class="form-control">
                </span>
              </div>
            </fieldset>
          </li>

          <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
            <fieldset class="form-group">
              <label class="form-control-label row" for="supplier_email"><?=$CMS->lang['supplier_email'];?></label>
              <div class="typeahead-field"> 
                <span class="typeahead-query">
                  <input name="supplier_email" id="supplier_email" size="45" type="text" value="<?=$data['supplier_email'];?>" class="form-control">
                </span>
              </div>
            </fieldset>
          </li>
        </ul>
      <span class="show_more_info col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="cursor:pointer" check="0"><span class="btn_check"><i class="fa fa-plus-square-o" aria-hidden="true"></i></span> <?=$CMS->lang['tilte_info_more_supplier'];?></span>
      <div class="box_show" style="display: none;">
        <ul class="list_field_supplier">
          <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
            <fieldset class="form-group">
              <label class="form-control-label row" for="supplier_taxcode"><?=$CMS->lang['supplier_taxcode'];?></label>
              <div class="typeahead-field"> 
                <span class="typeahead-query">
                  <input onkeypress="return check_enter_number(event,this);" name="supplier_taxcode" id="supplier_taxcode" size="45" type="text" value="<?=$data['supplier_taxcode'];?>" class="form-control" >
                </span>
              </div>
            </fieldset>
          </li>
          
          <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
            <fieldset class="form-group">
              <label class="form-control-label row" for="supplier_get_invoice"><?=$CMS->lang['supplier_get_invoice'];?></label>
              <div class="typeahead-field"> 
                <span class="typeahead-query">
                  <select name="supplier_get_invoice" id="supplier_get_invoice" class="form-control select2" defaultvalue="<?=$data['supplier_get_invoice'];?>" style="width: 100%">
                    <option value="-1"><?=$CMS->lang['plz_choose'];?></option>
                                  <option value="0"><?=$CMS->lang['no'];?></option>
                                  <option value="1"><?=$CMS->lang['yes'];?></option>
                  </select>
                </span>
              </div>
            </fieldset>
          </li>

          <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
            <fieldset class="form-group">
              <label class="form-control-label row" for="supplier_type"><?=$CMS->lang['supplier_type'];?></label>
              <div class="typeahead-field"> 
                <span class="typeahead-query">
                  <select name="supplier_type" id="supplier_type" class="form-control select2" onchange="change_supplier_type(this.value)" defaultvalue="<?=$data['supplier_type'];?>" style="width: 100%">
                    <option value="-1"><?=$CMS->lang['plz_choose'];?></option>
                                  <option value="1"><?=$CMS->lang['supplier_type_1'];?></option>
                                  <option value="2"><?=$CMS->lang['supplier_type_2'];?></option>
                  </select>
                </span>
              </div>
            </fieldset>
          </li>

          <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
            <fieldset class="form-group change_supplier">
              <label class="form-control-label row" for="supplier_idcard_number"><?=$CMS->lang['supplier_idcard_number'];?></label>
              <div class="typeahead-field"> 
                <span class="typeahead-query">
                  <input onkeypress="return check_enter_number(event,this);" name="supplier_idcard_number" id="supplier_idcard_number" size="45" type="text" value="<?=$data['supplier_idcard_number'];?>" class="form-control" >
                </span>
              </div>
            </fieldset>
          </li>

          <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
            <fieldset class="form-group">
              <label class="form-control-label row" for="city_id"><?=$CMS->lang['city_id'];?></label>
              <div class="typeahead-field"> 
                <span class="typeahead-query">
                  <select name="city_id" id="city_id" class="form-control select2" onchange="change_city(this.value)" defaultvalue="<?=$data['city_id'];?>" style="width: 100%">
                              <?=$CMS->country->getOptionCity(238);?>
                  </select>
                </span>
              </div>
            </fieldset>
          </li>
          
          <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
            <fieldset class="form-group">
              <label class="form-control-label row" for="district_id"><?=$CMS->lang['district_id'];?></label>
              <div class="typeahead-field"> 
                <span class="typeahead-query">
                  <select name="district_id" id="district_id" class="form-control select2" style="width: 100%">
                                <option value=""><?=$CMS->lang['select_district'];?></option>
                              </select>
                </span>
              </div>
            </fieldset>
          </li>

          <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
            <fieldset class="form-group">
              <label class="form-control-label row" for="supplier_address"><?=$CMS->lang['supplier_address'];?></label>
              <div class="typeahead-field"> 
                <span class="typeahead-query">
                  <input name="supplier_address" id="supplier_address" size="45" type="text" value="<?=$data['supplier_address'];?>" class="form-control" >
                </span>
              </div>
            </fieldset>
          </li>

          <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
            <fieldset class="form-group">
              <label class="form-control-label row" for="supplier_bank"><?=$CMS->lang['supplier_bank'];?></label>
              <div class="typeahead-field"> 
                <span class="typeahead-query">
                  <input name="supplier_bank" id="supplier_bank" size="45" type="text" value="<?=$data['supplier_bank'];?>" class="form-control" >
                </span>
              </div>
            </fieldset>
          </li>

          <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
            <fieldset class="form-group">
              <label class="form-control-label row" for="supplier_branch"><?=$CMS->lang['supplier_branch'];?></label>
              <div class="typeahead-field"> 
                <span class="typeahead-query">
                  <input name="supplier_branch" id="supplier_branch" size="45" type="text" value="<?=$data['supplier_branch'];?>" class="form-control" >
                </span>
              </div>
            </fieldset>
          </li>

          <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
            <fieldset class="form-group">
              <label class="form-control-label row" for="supplier_bank_number"><?=$CMS->lang['supplier_bank_number'];?></label>
              <div class="typeahead-field"> 
                <span class="typeahead-query">
                  <input onkeypress="return check_enter_number(event,this);" name="supplier_bank_number" id="supplier_bank_number" size="45" type="text" value="<?=$data['supplier_bank_number'];?>" class="form-control" >
                </span>
              </div>
            </fieldset>
          </li>

          <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
            <fieldset class="form-group">
              <label class="form-control-label row" for="supplier_bank_owner"><?=$CMS->lang['supplier_bank_owner'];?></label>
              <div class="typeahead-field"> 
                <span class="typeahead-query">
                  <input name="supplier_bank_owner" id="supplier_bank_owner" size="45" type="text" value="<?=$data['supplier_bank_owner'];?>" class="form-control">
                </span>
              </div>
            </fieldset>
          </li>
          
          <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
            <fieldset class="form-group">
              <label class="form-control-label row" for="supplier_note"><?=$CMS->lang['supplier_note'];?></label>
              <div class="typeahead-field"> 
                <span class="typeahead-query">
                  <textarea name="supplier_note" id="supplier_note" rows="5" cols="50" class="form-control"><?=$data['supplier_note'];?></textarea>
                </span>
              </div>
            </fieldset>
          </li>
        </ul> 
      </div>

        <ul class="list_field_supplier">
          <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="min-height: auto;">
            <fieldset class="form-group">
              <label class="form-control-label"></label>
              <div class="typeahead-field"> 
                <span class="typeahead-query change_action">
                  <input class="btn btn_add_supplier" type="button" value="<?=$CMS->lang['supplier_add'];?>"/>
                </span>
              </div>
            </fieldset>
          </li>
        
        </ul>
          <a class="btn_add_supplier" style="display:none">btn_add_supplier</a>
                  <a class="btn_edit_do_supplier" style="display:none">btn_edit_do_supplier</a>
      </form>
    </div>
    <script>
           $(document).ready(function(){

              validate_form_custom("#box_add_supplier",".act_popup_btn_validate","box_custom");
          ;});

    </script>