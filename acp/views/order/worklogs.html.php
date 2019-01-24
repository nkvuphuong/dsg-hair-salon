<section class="add_table">
    <div class="table-responsive">
        <table class="table_cus">
            <thead class="nowrap">
                <tr>
                    <th width="15%"><?=$CMS->lang['title_date'];?></th>
                    <th width="15%"><?=$CMS->lang['sale_user_id'];?></th>
                    <th width="50%"><?=$CMS->lang['title_comment'];?></th>
                    <th width="10%"><?=$CMS->lang['order_status'];?></th>
                    <th width="10%"><?=$CMS->lang['title_notify_customer'];?></th>
                </tr>
            </thead>
            <tbody>
                <? if(count($tpl->listComment)) { 
                    foreach ($tpl->listComment as $data) {?>
                <tr>
                    <td><div class="nowrap"><?=$data['comment_time_show'];?></div></td>
                    <td class=""><div class="nowrap"><?=$data['user_name'];?></div></td>
                    <td><?=$data['comment_content'];?></td>
                    <td class="table-icon-cell"><?=$data['status'];?></td>
                    <td class="table-icon-cell" style="text-align: center;"><?=$data['is_notify_customer'];?></td>
                </tr>
                <? } }else{ ?>
                <tr>
                    <td colspan="5"><?=$CMS->lang['title_no_comment'];?></td>
                </tr>
                <? } ?>
            </tbody>
        </table>
    </div>

    <div class="box-typical box-typical-padding" style="padding-left: 0; padding-right: 0;border: none;">
        <form class="order-form" action="/acp/?site=order&act=edit_do&subact=updatelog&id=<?=$tpl->data['ord_id'];?>" method="post">
        <div class="row">
            <div class="col-md-5">
                <div class="row">
                    <div class="col-md-6">
                        <fieldset class="form-group">
                            <label class="form-label semibold" for="trackingumber"><?=$CMS->lang['title_carrier'];?></label> 
                            <select class="form-control auto_select" name="ship_deliver" defaultvalue="<?=$tpl->data['ship_deliver'];?>">
                                <?=$CMS->partner_delivery->get_list_partner_delivery($tpl->data['ship_deliver']);?>
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-md-6">
                        <fieldset class="form-group">
                            <label class="form-label semibold" for="trackingumber"><?=$CMS->lang['order_tracking_number'];?></label> 
                            <input type="text" class="form-control" id="trackingumber" name="tracking_code" value="<?=isset($tpl->data['tracking_code']) ? $tpl->data['tracking_code'] : "";?>" />
                            <input type="hidden" name="tab" value="tabs-4-tab-1">
                        </fieldset>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <fieldset class="form-group">
                            <label class="form-label semibold" for="trackingumber"><?=$CMS->lang['order_status'];?></label>
                            <select class="form-control auto_select" name="ord_status" onchange="changeCusStatus(this, '[name=ord_status_custom]')" data-json='<?=$CMS->vars['custom_status'];?>' defaultvalue="<?=$tpl->data['ord_status'];?>">
                                <option value="0"><?=$CMS->lang['ord_status_0'];?></option>
                                <option value="1"><?=$CMS->lang['ord_status_1'];?></option>
                                <option value="2"><?=$CMS->lang['ord_status_2'];?></option>
                                <option value="3"><?=$CMS->lang['ord_status_3'];?></option>
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-md-6">
                        <fieldset class="form-group" id="custom_status">
                            <label class="form-label semibold" for="processing"><?=$CMS->lang['order_extend_status'];?></label>
                            <select class="form-control auto_select2" name="ord_status_custom" defaultvalue="<?=$tpl->data['ord_status_custom'];?>">
                                <option value=""><?=$CMS->lang['title_custom_status'];?></option>
                            </select>
                        </fieldset>
                    </div>
                </div>
                <fieldset class="form-group">
                    <div class="checkbox">
                        <input type="checkbox" name="send_notify" id="check-1" value="1">
                        <label class="form-label semibold" for="check-1"><?=$CMS->lang['title_notify_customer'];?></label>
                    </div>
                </fieldset>
            </div>
            <div class="col-md-7">
                <fieldset class="form-group">
                    <label class="form-label semibold" for="exampleInputEmail1"><?=$CMS->lang['title_comment'];?><font style="margin-left:5px" color="#FF0000">(*)</font></label>
                    <div class="form-wrapper" style="position: relative;">
                        <textarea rows="6" class="form-control" placeholder="Comment" name="comment_content" data-autosize="" style="overflow: hidden; word-wrap: break-word; height: 116px;" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['plz_enter_your_comment'];?>"></textarea>
                    </div>
                </fieldset>
                <button type="submit" class="btn btn-inline btn-secondary btn-secondary-black"><?=$CMS->lang['act_add_log'];?></button>
            </div>
        </form>
    </div>
</section>