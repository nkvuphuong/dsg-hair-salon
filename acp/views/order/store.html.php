          <div class="col-lg-6 block_service_type_0">
              <fieldset class="form-group">
                <label class="form-label semibold"  ><?=$CMS->lang['store_id'];?> </label>
                <? if($tpl->row_store >= 3) { ?>
                <div class="form-control-wrapper choose_store">  
                    <select class="form-control"  name="store_id" id="store_id"  onchange="setStoreId(this, '.store_id_param');">
                        <?=$tpl->option_store;?>
                    </select>         
                </div>
                <? }else{ ?>
                 <?=$tpl->option_store;?>
                <? } ?>
              </fieldset>
          </div>