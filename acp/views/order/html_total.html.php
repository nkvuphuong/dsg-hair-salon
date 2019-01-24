<div class="col-md-5">
  <div class="total-wrap">
      <h5 class="green-title"><?=$CMS->lang['title_total_summary'];?></h5>
      <div class="row">
          <div class="col-lg-6">
              <p><?=$CMS->lang['gsubtotal'];?></p>
          </div>
          <div class="col-lg-6 text-right">
              <p class="semibold"><?=$tpl->data['ord_amount_n'];?></p>
          </div>
      </div>
      <? if($tpl->data['ord_total_discount'] > 0) {?>
      <div class="row">
          <div class="col-lg-6">
              <p><?=$CMS->lang['gdiscount'];?></p>
          </div>
          <div class="col-lg-6 text-right">
              <p class="semibold"><?=$tpl->data['ord_total_discount_n'];?></p>
          </div>
      </div>
      <? } ?>
      <? if($tpl->data['ord_tax'] > 0) {?>
      <div class="row">
          <div class="col-lg-6">
              <p><?=$CMS->lang['gtax'];?></p>
          </div>
          <div class="col-lg-6 text-right">
              <p class="semibold"><?=$tpl->data['ord_tax_n'];?></p>
          </div>
      </div>
      <? } ?>
      
      <?if( $tpl->data['ord_fee_shipping'] > 0 ){?>
      <div class="row">
          <div class="col-lg-6">
              <p><?=$CMS->lang['ord_fee_shipping'];?></p>
          </div>
          <div class="col-lg-6 text-right">
              <p class="semibold"><?=$tpl->data['ord_fee_shipping_n'];?></p>
          </div>
      </div>
      <?}?>

      <?if( $tpl->data['ord_fee_shipping'] > 0 ){?>
          <div class="row">
              <div class="col-lg-6">
                  <p><?=$CMS->lang['ord_fee_shipping'];?></p>
              </div>
              <div class="col-lg-6 text-right">
                  <p class="semibold"><?=$tpl->data['ord_fee_shipping_n'];?></p>
              </div>
          </div>
      <?}?>

      <?if( $tpl->data['data_bk']['ord_rating_status'] == 1 ){?>
          <div class="row">
              <div class="col-lg-6">
                  <p><?=$CMS->lang['gcommission'];?></p>
              </div>
              <div class="col-lg-6 text-right">
                  <p class="semibold"><?=$tpl->data['ord_commission'];?> (<?=$tpl->data['ord_commission_rating']?>)</p>
              </div>
          </div>
      <?}?>
      
      <hr class="total-hr" />
      <div class="row">
          <div class="col-lg-6">
              <p><?=$CMS->lang['gtotal'];?></p>
              <p class="text-gray mb-0"><?=$tpl->data['payment_mothod_bk'];?></p>
          </div>
          <div class="col-lg-6 text-right">
              <p class="red-total"><?=$tpl->data['ord_total_n'];?></p>
          </div>
      </div>
