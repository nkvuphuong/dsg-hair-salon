<section class="box-typical scrollable">
  <div class="box-typical-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover table-zp">
        <thead>
          <tr>
            <th class="text-center" rowspan="2" colspan="2"><?=$CMS->lang['ship_fee_service'];?></th>
            <th class="text-center" colspan="4"><?=$CMS->lang['ship_fee_rates'];?></th>
          </tr>
          <tr>
            <td class="text-center"><?=$CMS->lang['ship_fee_domestics'];?></td>
              <td class="text-center"><?=$CMS->lang['ship_fee_extra'];?></td>
              <td class="text-center"><?=$CMS->lang['ship_fee_international'];?></td>
              <td class="text-center"><?=$CMS->lang['ship_fee_extra'];?></td>
          </tr>
        </thead>
        <tbody id="shipping_services">

          <?
          if( !empty($tpl->dataShipFee['ship_type_service']) AND is_array($tpl->dataShipFee['ship_type_service']) )
          {
            foreach( $tpl->dataShipFee['ship_type_service'] as $key => $ship_type_service )
            {
          ?>
          <tr class="row-grid ship_fee_row">
            <td width="30">
              <a class="text-danger delline_service" onclick="deleteShipFeeLine(this);"><i class="fa fa-trash"></i>
            </td>
            <td>
              <select class="form-control" name="ship_type_service[]" style="min-width: 100px;">
                <?
                foreach( $tpl->shipping_type_service as $type_key => $type_value )
                {
                  $selected = $ship_type_service == $type_key ? 'selected="selected"' : '';
                ?>
                <option value="<?=$type_key;?>" <?=$selected;?>><?=$type_value;?></option>
                <?}?>
              </select>
            </td>
            <td>
              <input class="form-control" type="text" name="ship_price_domestics[]" value="<?=isset($tpl->dataShipFee['ship_price_domestics'][$key]) ? $tpl->dataShipFee['ship_price_domestics'][$key] : '';?>" maxlength="7" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
            </td>
            <td>
              <input class="form-control" type="text" name="ship_extra_domestics[]" value="<?=isset($tpl->dataShipFee['ship_extra_domestics'][$key]) ? $tpl->dataShipFee['ship_extra_domestics'][$key] : '';?>"  maxlength="7" onkeypress="return check_enter_number(event,this);"   onfocusout="check_enter_number_2(this,0);" >
            </td>
            <td>
              <input class="form-control" type="text" name="ship_price_intl[]" value="<?=isset($tpl->dataShipFee['ship_price_intl'][$key]) ? $tpl->dataShipFee['ship_price_intl'][$key] : '';?>" maxlength="7" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
            </td>
            <td>
              <input class="form-control" type="text" name="ship_extra_intl[]" value="<?=isset($tpl->dataShipFee['ship_extra_intl'][$key]) ? $tpl->dataShipFee['ship_extra_intl'][$key] : '';?>" maxlength="7" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);">
            </td>
          </tr>

          <?}}else{?>
          <tr class="row-grid ship_fee_row">
            <td width="30">
              <a class="text-danger delline_service" onclick="deleteShipFeeLine(this);"><i class="fa fa-trash"></i>
            </td>
            <td>
              <select class="form-control defaultvalue_0" name="ship_type_service[]" style="min-width: 100px;">
                <?=$tpl->option_shipping_service;?>
              </select>
            </td>
            <td>
              <input class="form-control" type="text" name="ship_price_domestics[]" value="" maxlength="7" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
            </td>
            <td>
              <input class="form-control" type="text" name="ship_extra_domestics[]" value=""  maxlength="7" onkeypress="return check_enter_number(event,this);"   onfocusout="check_enter_number_2(this,0);" >
            </td>
            <td><input class="form-control" type="text" name="ship_price_intl[]" maxlength="7" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" ></td>
            <td><input class="form-control" maxlength="7" type="text" name="ship_extra_intl[]" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);"></td>
          </tr>
          <tr class="row-grid ship_fee_row">
            <td width="30">
              <a class="text-danger delline_service" onclick="deleteShipFeeLine(this);"><i class="fa fa-trash"></i>
            </td>
            <td>
              <select class="form-control defaultvalue_1" name="ship_type_service[]" style="min-width: 100px;"  defaultvalue="1">
                <?=$tpl->option_shipping_service;?>
              </select>
            </td>
            <td>
              <input class="form-control" type="text" name="ship_price_domestics[]" value="" maxlength="7" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
            </td>
            <td>
              <input class="form-control" type="text" name="ship_extra_domestics[]" value=""  maxlength="7" onkeypress="return check_enter_number(event,this);"   onfocusout="check_enter_number_2(this,0);" >
            </td>
            <td><input class="form-control" type="text" name="ship_price_intl[]" maxlength="7" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" ></td>
            <td><input class="form-control" maxlength="7" type="text" name="ship_extra_intl[]" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);"></td>
          </tr>
          <?}?>
        </tbody>
      </table>
      <div class="table-pagination">
        <div class="paging-left">
          <div class="d-flex paging-ver">
            <button type="button" class="btn btn-green" id="newelem_shipping" style="margin: 15px;"><?=$CMS->lang['ship_fee_btn_more'];?></button>                            
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">
/*
* Delete Ship Fee Line
*/
if( typeof deleteShipFeeLine != 'function' )
{
  function deleteShipFeeLine(objThis)
  {
    if( $('.ship_fee_row').length > 1 )
    {
      $(objThis).closest('.ship_fee_row').remove();
    }
  }
}
$(document).ready(function(){
  // Add Ship Fee Line
  $("#newelem_shipping").click(function(){
    $('.ship_fee_row').last().clone().appendTo("#shipping_services");
  });

  $('.defaultvalue_0 option[value="0"]').prop('selected', true);
  $('.defaultvalue_1 option[value="1"]').prop('selected', true);
});
</script>