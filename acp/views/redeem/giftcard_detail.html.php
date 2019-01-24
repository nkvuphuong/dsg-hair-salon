<section class="main_form">
    <figure class="heading">
        <h3>History used gift cards</h3>
        <a href="<?=$CMS->vars['root_domain'];?>/?site=redeem" title=""><span class="font-icon font-icon-del"></span></a>
    </figure>
    <section class="box-typical box-typical-padding border">
      <div class="col-xl-12"><h5 class="m-t-lg with-border"><?=$CMS->lang['title_info_giftcard'];?></h5></div>
      <div class="row">
        <div class="col-xl-4 col-md-6 col-sm-5 col-xs-6">
          <fieldset class="form-group">
            <label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" ><?=$CMS->lang['title_giftcard_code'];?></label>
            <div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2"><?=$tpl->data['giftcard_code'];?></div>
          </fieldset>

          <fieldset class="form-group">
            <label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" ><?=$CMS->lang['title_giftcard_image'];?></label>
            <div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2"><img src="<?=$tpl->data['image'];?>" style="max-width: 200px; padding: 5px;"/></div>
          </fieldset>

          <fieldset class="form-group">
            <label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" ><?=$CMS->lang['giftcard_amount'];?></label>
            <div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2">$ <?=$tpl->data['gitem_amount'];?></div>
          </fieldset>

          <fieldset class="form-group">
            <label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" ><?=$CMS->lang['giftcard_amount_remain'];?></label>
            <div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2">$ <?=$tpl->data['gitem_amount_remain'];?></div>
          </fieldset>

          <fieldset class="form-group">
            <label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" ><?=$CMS->lang['title_time_create'];?></label>
            <div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2"><?=\lib\date::format($tpl->data['gitem_time'],"M d, Y g:i a");?></div>
          </fieldset>

          <fieldset class="form-group">
            <label class="col-xl-5 col-md-5 col-sm-5 col-xs-12 form-control-label2" ><?=$CMS->lang['title_last_used'];?></label>
            <div class="col-xl-7 col-sm-7 col-sm-7 col-xs-12 form-control-span2"><?= $tpl->data['gitem_time_update'] == $tpl->data['gitem_time'] ? "N/A" : \lib\date::format($tpl->data['gitem_time_update'],"M d, Y g:i a");?></div>
          </fieldset>

        </div>
      </div>

    </section>
    <?=$tpl->logs;?>
</section>

