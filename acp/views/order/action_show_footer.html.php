<section class="add_cart_footer">
    <?=$tpl->footer_html;?>

    <!-- Mobile -->
    <?if( $_SESSION['is_mobile'] == true ){?>
    <div class="btn-group dropup pull-right hidden-xl-up">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa"></i><?=$CMS->lang['title_action'];?></button>
        <div class="dropdown-menu">
            <ul>
                <!-- Delete -->
                <?if( $CMS->permit['order_delete'] == 1 AND $tpl->data['ord_status'] == 3 ){?>
                <li>
                    <a onclick="delete_confirm('/acp/?site=order&act=delete&id=<?=$tpl->data['ord_id'];?>', '<?=$CMS->lang['confirm_delete'];?>', '<?=$CMS->lang['confirm_delete_note'];?>');"><i class="fa fa-trash-o"></i> <?=$CMS->lang['act_delete_order'];?></a>
                </li>
                <?}?>

                <!-- Copy -->
                <?if( $CMS->permit['order_add'] == 1 ){?>
                <li>
                    <a href="/acp/?site=order&act=add&sub_act=copy&id=<?=$tpl->data['ord_id'];?>"><i class="fa fa-copy"></i> <?=$CMS->lang['act_copy_order'];?></a>
                </li>
                <?}?>

                <!-- Edit --> 
                <?if( $CMS->permit['order_edit'] == 1 AND $tpl->data['payment_status'] == 0 AND !in_array($tpl->data['ord_status'], array('2', '3')) ){?>
                <li>
                    <a href="/acp/?site=order&act=edit&id=<?=$tpl->data['ord_id'];?>"><i class="fa fa-edit"></i> <?=$CMS->lang['act_edit_order'];?></a>
                </li>
                <?}?>

                <!-- Print -->
                <li>
                    <a onclick="openPreviewPrint('<?=$CMS->vars['root_domain'];?>/?site=order&subact=preview_invoice&id=<?=$tpl->data['ord_id'];?>')"><i class="fa fa-eye" aria-hidden="true"></i><?=$CMS->lang['title_print_invoice'];?></a>
                </li>
                <li>
                    <a onclick="openPreviewPrint('<?=$CMS->vars['root_domain'];?>/?site=order&subact=preview_label&id=<?=$tpl->data['ord_id'];?>')"><i class="fa fa-eye" aria-hidden="true"></i><?=$CMS->lang['title_print_label'];?></a>
                </li>

                <!-- Pending -->
                <?if( $CMS->permit['order_edit'] == 1 AND !in_array($tpl->data['ord_status'], array('2', '3', '0')) ){?>
                <li>
                    <a onclick="alert_confirm_custom('/acp/?site=order&act=edit_do&sub_act=pending&id=<?=$tpl->data['ord_id'];?>', '<?=$CMS->lang['confirm_pending_order'];?>', '<?=$CMS->lang['confirm_pending_order_note'];?>');"><i class="fa fa-pause-circle"></i> <?=$CMS->lang['act_pending_order'];?></a>
                </li>
                <?}?>

                <!-- Proccessing -->
                <?if( $CMS->permit['order_edit'] == 1 AND !in_array($tpl->data['ord_status'], array('2', '3', '1')) ){?>
                <li>
                    <a onclick="alert_confirm_custom('/acp/?site=order&act=edit_do&sub_act=process&id=<?=$tpl->data['ord_id'];?>', '<?=$CMS->lang['confirm_process_order'];?>', '<?=$CMS->lang['confirm_process_order_note'];?>');"><i class="fa fa-play-circle"></i> <?=($tpl->data['service_type'] == 1) ? $CMS->lang['act_process_booking'] : $CMS->lang['act_process_order'];?></a>
                </li>
                <?}?>

                <!-- Cancel -->
                <?if( $CMS->permit['order_edit'] == 1 AND !in_array($tpl->data['ord_status'], array('2', '3')) ){?>
                <li>
                    <a onclick="alert_confirm_custom('/acp/?site=order&act=edit_do&sub_act=cancel&id=<?=$tpl->data['ord_id'];?>', '<?=$CMS->lang['confirm_cancel_order'];?>', '<?=$CMS->lang['confirm_cancel_order_note'];?>');"><i class="fa fa-stop-circle-o"></i> <?=$CMS->lang['act_cancel_order'];?></a>
                </li>
                <?}?>

                <!-- Completed -->
                <?
                if( $CMS->permit['order_edit'] == 1 AND in_array($tpl->data['payment_status'], array('1', '2')) AND !in_array($tpl->data['ord_status'], array('2', '3')) )
                {
                    if( $CMS->vars['negative_sale'] == 1 AND $tpl->stock > 0 )
                    {
                ?>
                <li>
                    <a onclick="alertText('<?=$CMS->lang['store_not_enought'];?>','warning');"><i class="fa fa-check-circle"></i> <?=$CMS->lang['update_status_done'];?></a>
                </li>
                
                <?}elseif( $CMS->vars['negative_sale'] == 0 AND $tpl->stock == 0 ){?>
                <li>
                    <a onclick="alert_confirm_stockcustom('/acp/?site=order&act=edit_do&sub_act=success&id=<?=$tpl->data['ord_id'];?>&type=all&stock=<?=$tpl->stock;?>', '<?=$tpl->stock;?>', '<?=$CMS->lang['confirm_complete_order'];?>', '<?=$CMS->lang['confirm_complete_order_note'];?>');"><i class="fa fa-check-circle"></i> <?=$CMS->lang['update_status_done'];?></a>
                </li>
                
                <?}else{?>
                <li>
                    <a onclick="alert_confirm_stockcustom('/acp/?site=order&act=edit_do&sub_act=success&id=<?=$tpl->data['ord_id'];?>&type=all&stock=<?=$tpl->stock;?>', '<?=$tpl->stock;?>', '<?=$CMS->lang['confirm_complete_order'];?>', '<p><b><?=$CMS->lang['store_not_enought'];?>.</b></p><p><?=$CMS->lang['confirm_complete_order_note'];?></p>');"><i class="fa fa-check-circle"></i> <?=$CMS->lang['update_status_done'];?></a>
                </li>
                <?}}?>

                <!-- Unpaid -->
                <?if( $CMS->permit['order_edit'] == 1 AND $tpl->data['payment_status'] == 0 AND $tpl->data['ord_status'] != 3 ){?>
                <li>
                    <a onclick="delete_confirm('/acp/?site=order&act=edit_do&sub_act=approve_unpaid&id=<?=$tpl->data['ord_id'];?>', '<?=$CMS->lang['confirm_mark_as_debit'];?>', '<?=$CMS->lang['confirm_mark_as_debit_note'];?>');"><i class="fa fa-credit-card-alt"></i> <?=$CMS->lang['act_mark_as_debit'];?></a>
                </li>
                <?}?>

                <!-- Paid -->
                <?if( $CMS->permit['order_edit'] == 1 AND $tpl->data['payment_status'] != 1 AND $tpl->data['ord_status'] != 3 ){?>
                <li>
                    <a onclick="delete_confirm('/acp/?site=order&act=edit_do&sub_act=approve_paid&id=<?=$tpl->data['ord_id'];?>', '<?=$CMS->lang['confirm_mark_as_paid'];?>', '<?=$CMS->lang['confirm_mark_as_paid_note'];?>');"><i class="fa fa-credit-card"></i> <?=$CMS->lang['act_mark_as_paid'];?></a>
                </li>
                <?}?>
            </ul>
        </div>
    </div>

    <!-- Desktop -->
    <?}else{?>

    <!-- Action block -->
    <div class="btn-group dropup pull-right add_cart_3">
        <button type="button" class="btn btn-default-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=$CMS->lang['title_action'];?><span class="sr-only">Toggle Dropdown</span></button>
        <div class="dropdown-menu">
            <!-- Delete -->
            <?if( $CMS->permit['order_delete'] == 1 AND $tpl->data['ord_status'] == 3 ){?>
            <a onclick="delete_confirm('/acp/?site=order&act=delete&id=<?=$tpl->data['ord_id'];?>', '<?=$CMS->lang['confirm_delete'];?>', '<?=$CMS->lang['confirm_delete_note'];?>');"  class="dropdown-item"><i class="fa fa-trash-o"></i> <?=$CMS->lang['act_delete_order'];?></a>
            <?}?>

            <!-- Copy -->
            <?if( $CMS->permit['order_add'] == 1 ){?>
            <div class="dropdown-divider"></div>
            <a href="/acp/?site=order&act=add&sub_act=copy&id=<?=$tpl->data['ord_id'];?>" class="dropdown-item"><i class="fa fa-copy"></i> <?=$CMS->lang['act_copy_order'];?></a>
            <?}?>

            <!-- Edit --> 
            <?if( $CMS->permit['order_edit'] == 1 AND $tpl->data['payment_status'] == 0 AND !in_array($tpl->data['ord_status'], array('2', '3')) ){?>
            <div class="dropdown-divider"></div>
            <a href="/acp/?site=order&act=edit&id=<?=$tpl->data['ord_id'];?>" class="dropdown-item"><i class="fa fa-edit"></i> <?=$CMS->lang['act_edit_order'];?></a>
            <?}?>

            <!-- Pending -->
            <?if( $CMS->permit['order_edit'] == 1 AND !in_array($tpl->data['ord_status'], array('2', '3', '0')) ){?>
            <div class="dropdown-divider"></div>
            <a onclick="alert_confirm_custom('/acp/?site=order&act=edit_do&sub_act=pending&id=<?=$tpl->data['ord_id'];?>', '<?=$CMS->lang['confirm_pending_order'];?>', '<?=$CMS->lang['confirm_pending_order_note'];?>');" class="dropdown-item"><i class="fa fa-pause-circle"></i> <?=$CMS->lang['act_pending_order'];?></a>
            <?}?>

            <!-- Proccessing -->
            <?if( $CMS->permit['order_edit'] == 1 AND !in_array($tpl->data['ord_status'], array('2', '3', '1')) ){?>
            <div class="dropdown-divider"></div>
            <a onclick="alert_confirm_custom('/acp/?site=order&act=edit_do&sub_act=process&id=<?=$tpl->data['ord_id'];?>', '<?=$CMS->lang['confirm_process_order'];?>', '<?=$CMS->lang['confirm_process_order_note'];?>');" class="dropdown-item"><i class="fa fa-play-circle"></i> <?=($tpl->data['service_type'] == 1) ? $CMS->lang['act_process_booking'] : $CMS->lang['act_process_order'];?></a>
            <?}?>

            <!-- Cancel -->
            <?if( $CMS->permit['order_edit'] == 1 AND !in_array($tpl->data['ord_status'], array('2', '3')) ){?>
            <div class="dropdown-divider"></div>
            <a onclick="alert_confirm_custom('/acp/?site=order&act=edit_do&sub_act=cancel&id=<?=$tpl->data['ord_id'];?>', '<?=$CMS->lang['confirm_cancel_order'];?>', '<?=$CMS->lang['confirm_cancel_order_note'];?>');" class="dropdown-item"><i class="fa fa-stop-circle-o"></i> <?=$CMS->lang['act_cancel_order'];?></a>
            <?}?>

            <!-- Completed -->
            <?
            if( $CMS->permit['order_edit'] == 1 AND in_array($tpl->data['payment_status'], array('1', '2')) AND !in_array($tpl->data['ord_status'], array('2', '3')) )
            {
                if( $CMS->vars['negative_sale'] == 1 AND $tpl->stock > 0 )
                {
            ?>
            <div class="dropdown-divider"></div>
            <a onclick="alertText('<?=$CMS->lang['store_not_enought'];?>','warning');" class="dropdown-item"><i class="fa fa-check-circle"></i> <?=$CMS->lang['update_status_done'];?></a>
            
            <?}elseif( $CMS->vars['negative_sale'] == 0 AND $tpl->stock == 0 ){?>
            <div class="dropdown-divider"></div>
            <a onclick="alert_confirm_stockcustom('/acp/?site=order&act=edit_do&sub_act=success&id=<?=$tpl->data['ord_id'];?>&type=all&stock=<?=$tpl->stock;?>', '<?=$tpl->stock;?>', '<?=$CMS->lang['confirm_complete_order'];?>', '<?=$CMS->lang['confirm_complete_order_note'];?>');" class="dropdown-item"><i class="fa fa-check-circle"></i> <?=$CMS->lang['update_status_done'];?></a>
            
            <?}else{?>
            <div class="dropdown-divider"></div>
            <a onclick="alert_confirm_stockcustom('/acp/?site=order&act=edit_do&sub_act=success&id=<?=$tpl->data['ord_id'];?>&type=all&stock=<?=$tpl->stock;?>', '<?=$tpl->stock;?>', '<?=$CMS->lang['confirm_complete_order'];?>', '<p><b><?=$CMS->lang['store_not_enought'];?>.</b></p><p><?=$CMS->lang['confirm_complete_order_note'];?></p>');" class="dropdown-item"><i class="fa fa-check-circle"></i> <?=$CMS->lang['update_status_done'];?></a>
            <?}}?>

            <? if($tpl->data['ord_rating_status'] == 0 && \lib\input::vars('checkin_enabled')) { ?>
            <div class="dropdown-divider rating-action-<?=$tpl->data['ord_id']?>"></div>
            <a href="#" class="dropdown-item rating-action-<?=$tpl->data['ord_id']?>" onclick="SocketIOClient.openRating('<?=$tpl->data['ord_id']?>')"><i class="fa fa-star"></i> Bật đánh giá</a>
            <? } ?>
        </div>
    </div>

    <!-- Print -->
    <a class="pull-right add_cart_2" onclick="openPreviewPrint('<?=$CMS->vars['root_domain'];?>/?site=order&subact=preview_invoice&id=<?=$tpl->data['ord_id'];?>')"><i class="fa fa-eye" aria-hidden="true"></i><?=$CMS->lang['title_print_invoice'];?></a>
    <a class="pull-right add_cart_2" onclick="openPreviewPrint('<?=$CMS->vars['root_domain'];?>/?site=order&subact=preview_label&id=<?=$tpl->data['ord_id'];?>')"><i class="fa fa-eye" aria-hidden="true"></i><?=$CMS->lang['title_print_label'];?></a>

    <!-- Payment block -->
    <div class="btn-group dropup pull-right add_cart_3">
        <!-- Paid -->
        <?if( $CMS->permit['order_edit'] == 1 AND $tpl->data['payment_status'] != 1 AND $tpl->data['ord_status'] != 3 ){?>
        <button type="button" class="btn btn-default-outline" onclick="delete_confirm('/acp/?site=order&act=edit_do&sub_act=approve_paid&id=<?=$tpl->data['ord_id'];?>', '<?=$CMS->lang['confirm_mark_as_paid'];?>', '<?=$CMS->lang['confirm_mark_as_paid_note'];?>');"><i class="fa fa-credit-card"></i> <?=$CMS->lang['act_mark_as_paid'];?></button>
        <?}?>

        <!-- Unpaid -->
        <?if( $CMS->permit['order_edit'] == 1 AND $tpl->data['payment_status'] == 0 AND $tpl->data['ord_status'] != 3 ){?>
        <button type="button" class="btn btn-default-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="sr-only">Toggle Dropdown</span></button>
        <div class="dropdown-menu">
            <a onclick="delete_confirm('/acp/?site=order&act=edit_do&sub_act=approve_unpaid&id=<?=$tpl->data['ord_id'];?>', '<?=$CMS->lang['confirm_mark_as_debit'];?>', '<?=$CMS->lang['confirm_mark_as_debit_note'];?>');" class="dropdown-item"><i class="fa fa-credit-card-alt"></i> <?=$CMS->lang['act_mark_as_debit'];?></a>
        </div>
        <?}?>
    </div>

    <?}?><!-- End desktop -->
</section>