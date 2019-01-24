<!-- Modal vertically centered-->
<div class="modal fade" id="editShipFeeModalCenter" tabindex="-1" role="dialog" aria-labelledby="editShipFeeModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title pull-left" id="editShipFeeModalCenterTitle"><?=$CMS->lang['title_edit_ship_fee'];?></h5>
        <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" id="form_edit_ship_fee" name="form_edit_ship_fee" action="<?=$CMS->vars['root_domain'];?>/?site=product&subact=edit_ship_id&ship_id=<?=$tpl->data['ship_id'];?>">
      <div class="modal-body">
        <div class="clearfix loading_content" style="display: none;"><img src="/acp/images/fb-loading.gif"> Loading...</div>
        <section class="box-typical scrollable main_content">
          <div class="box-typical-body">
            <div class="table-responsive">
              <table class="table table-bordered table-hover table-zp">
                <thead>
                  <tr>
                    <th class="text-center" rowspan="2"><?=$CMS->lang['ship_fee_service'];?></th>
                    <th class="text-center" colspan="3"><?=$CMS->lang['ship_fee_rates'];?></th>
                  </tr>
                  <tr>
                    <td class="text-center"><?=$CMS->lang['ship_fee_location'];?></td>
                      <td class="text-center"><?=$CMS->lang['ship_fee_price'];?></td>
                      <td class="text-center"><?=$CMS->lang['ship_fee_extra'];?></td>
                  </tr>
                </thead>
                <tbody id="shipping_services">
                  <tr class="row-grid" >
                    <td>
                      <span id="ship_type_service"></span>
                    </td>
                    <td>
                      <span id="ship_location"></span>
                    </td>
                    <td>
                      <input type="text" class="form-control" id="ship_price" name="ship_price" value="" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
                    </td>
                    <td>
                      <input type="text" class="form-control" id="ship_price_extra" name="ship_price_extra" value="" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </section>
      </div>
      <div class="modal-footer">
        <input id="ship_id" name="ship_id" value="" type="hidden">
        <input id="product_id" name="product_id" value="" type="hidden">
        <button type="button" class="btn btn-primary btn_edit_shipfee" onclick="doEditShipFee();"><?=$CMS->lang['ship_fee_btn_edit'];?></button>
      </div>
      </form>
    </div>
  </div>
</div>