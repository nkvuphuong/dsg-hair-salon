<!-- Modal vertically centered-->
<div class="modal fade" id="addShipFeeModalCenter" tabindex="-1" role="dialog" aria-labelledby="addShipFeeModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title pull-left" id="addShipFeeModalCenterTitle"><?=$CMS->lang['title_add_ship_fee'];?></h5>
        <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" id="form_add_ship_fee" name="form_add_ship_fee" action="<?=$CMS->vars['root_domain'];?>/?site=product&subact=add_ship_id&product_id=<?=$tpl->data['product_id'];?>">
      <div class="modal-body">
        <?=\core\ezy::render('form_shipping_fee', 'product');?>
      </div>
      <div class="modal-footer">
        <input id="product_id" name="product_id" value="" type="hidden">
        <button type="button" class="btn btn-primary btn_add_shipfee" onclick="doAddShipFee();"><?=$CMS->lang['ship_fee_btn_add'];?></button>
      </div>
      </form>
    </div>
  </div>
</div>