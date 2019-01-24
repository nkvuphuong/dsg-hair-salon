<link rel="stylesheet" type="text/css" href='<?=$CMS->vars['root_domain']?>/assets/css/orders.css'>
<figure class="heading">
    <div class="row">
        <div class="col-md-6">
            <h3><?=$tpl->header_title;?></h3>
        </div>
        <div class="col-md-6">
            <a class="pull-right pointer btn btn-inline btn-primary btn_link_tab" onclick="scrollJumpto('#worklogs', '.site-header');"><?=$CMS->lang['act_worklogs'];?></a>
        </div>
    </div>
</figure>
<div class="manage-container">
    <section class="tabs-section">
        <div class="tabs-section-nav tabs-section-nav-inline order-tabs-section-nav">
            <ul class="nav" role="tablist">
                <li class="nav-item"><a class="nav-link active" onclick="renderUrl(this);" href="#tabs-4-tab-1" role="tab" data-toggle="tab" aria-expanded="true"><?=$CMS->lang['title_overview_order'];?></a></li>
                <li class="nav-item"><a class="nav-link" onclick="renderUrl(this);" href="#tabs-4-tab-2" role="tab" data-toggle="tab" aria-expanded="false"><?=$CMS->lang['title_payment_order'];?></a></li>
                <li class="nav-item"><a class="nav-link" onclick="renderUrl(this);" href="#tabs-4-tab-3" role="tab" data-toggle="tab" aria-expanded="false"><?=$CMS->lang['title_shipping_order'];?></a></li>
            </ul>
        </div>
        <!--.tabs-section-nav-->
        <div class="tab-content order-tab-content">
            <div role="tabpanel" class="tab-pane fade in active show" id="tabs-4-tab-1" aria-expanded="true">
                <div class="box-typical box-typical-info" style="padding: 15px;margin-bottom: 25px;">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['order_status_ls'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9">
                                    <?=$tpl->data['ord_status_n'];?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['service_type'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9">
                                    <p class="semibold"><?=$tpl->data['service_type_bk'];?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['cus_id'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9"><?=$tpl->data['cus_name_bk'];?></div>
                            </div>
                            <? if ($tpl->data['data_bk']['service_type']) { ?>
                                <div class="row">
                                    <div class="col-lg-4 col-md-3">
                                        <p><?=$CMS->lang['ord_booking_phone'];?></p>
                                    </div>
                                    <div class="col-lg-8 col-md-9"><?=$tpl->data['ord_booking_phone'];?></div>
                                </div>
                            <? } ?>

                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['sale_user_id'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9"><?=$tpl->data['user_name_bk'];?></div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['store_name'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9">
                                    <p class="semibold"><?=$tpl->data['store_name'];?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['order_note'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9">
                                    <p class="semibold"><?=$tpl->data['ord_note'];?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['order_date_created'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9">
                                    <p class="semibold"><?=$tpl->data['ord_time_bk'];?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['order_date_updated'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9">
                                    <p class="semibold"><?=$tpl->data['ord_time_update_bk'];?></p>
                                </div>
                            </div>
                        </div>
                        <?=\core\ezy::render("html_total");?>
                    </div>
                </div>

                <!-- Order items -->
                <?=$tpl->order_item;?>

                <!-- Transaction item -->
                <?=$tpl->list_transaction;?>
            </div>
            <!--.tab-pane-->
            <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-2" aria-expanded="false">
                <div class="box-typical box-typical-info" style="padding: 15px;margin-bottom: 25px;">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['order_payment_status'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9">
                                    <?=$tpl->data['payment_status_bk'];?>
                                    
                                    <!-- button paid -->
                                    <?if( $tpl->data['payment_status'] != 1 AND $tpl->data['ord_status'] != 3 ){?>
                                    <a onclick="delete_confirm('/acp/?site=order&act=edit_do&sub_act=approve_paid&id=<?=$tpl->data['ord_id'];?>', '<?=$CMS->lang['confirm_mark_as_paid'];?>', '<?=$CMS->lang['confirm_mark_as_paid_note'];?>');" class="label label-primary">
                                        <span style="color: #fff;"><i class="fa fa-credit-card"></i> <?=$CMS->lang['act_mark_as_paid'];?></span>
                                    </a>
                                    <?}?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['payment_method'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9">
                                    <p class="semibold"><?=$tpl->data['payment_mothod_bk'];?></p>
                                </div>
                            </div>
                            <? if($tpl->data['payment_method'] == 1 and $tpl->data['account_id'] > 0) { ?>
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['account_id'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9">
                                    <p class="semibold"><?=$tpl->data['account_name'];?></p>
                                </div>
                            </div>
                            <? } ?>
                            <!--Information Bill-->
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['payment_full_name'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9">
                                    <p class="semibold"><?=$tpl->bill['full_name'];?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['payment_email'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9">
                                    <p class="semibold"><?=$tpl->bill['email'];?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['payment_phone'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9">
                                    <p class="semibold"><?=$tpl->bill['phone'];?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['payment_company'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9">
                                    <p class="semibold"><?=$tpl->bill['company'];?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['payment_address'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9">
                                    <p class="semibold"><?=$tpl->bill['address_full_us'];?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['payment_country'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9">
                                    <p class="semibold"><?=$tpl->bill['country_name'];?></p>
                                </div>
                            </div>
                        </div>
                        <?=\core\ezy::render("html_total");?>
                    </div>
                </div>
            </div>
            <!--.tab-pane-->
            <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-3" aria-expanded="false">
                <div class="box-typical box-typical-info"  style="padding: 15px;margin-bottom: 25px;">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['order_user'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9"><?=$tpl->data['cus_name_bk'];?></div>
                            </div>

                            <!--Information Bill-->
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['shipping_full_name'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9">
                                    <p class="semibold"><?=$tpl->ship['full_name'];?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['shipping_email'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9">
                                    <p class="semibold"><?=$tpl->ship['email'];?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['shipping_phone'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9">
                                    <p class="semibold"><?=$tpl->ship['phone'];?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['shipping_company'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9">
                                    <p class="semibold"><?=$tpl->ship['company'];?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['shipping_address'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9">
                                    <p class="semibold"><?=$tpl->ship['address_full_us'];?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['payment_country'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9">
                                    <p class="semibold"><?=$tpl->ship['country_name'];?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-3">
                                    <p><?=$CMS->lang['ord_shipping_method'];?></p>
                                </div>
                                <div class="col-lg-8 col-md-9">
                                    <p class="semibold"><?=$tpl->data['ord_shipping_method_n'];?> (<?=$tpl->data['ord_shipping_location_n'];?>)</p>
                                </div>
                            </div>
                        </div>
                        <?=\core\ezy::render("html_total");?>
                    </div>
                </div>
            </div><!--.tab-pane-->
        </div><!--.tab-content-->
    </section>
</div>

<? if (!$tpl->data['data_bk']['service_type']) { ?>
    <!-- Worklogs -->
    <div class="container-fluid">
        <section class="add_table" id="worklogs">
            <h4 class="heading" style="margin: 30px 0 10px;"><i class="fa fa-caret-down"></i><span><?=$CMS->lang['title_work_logs_order'];?> (<?=$tpl->numberComment;?>)</span></h4>
            <div class="row manage-container"><?=\core\ezy::render('worklogs', 'order');?></div>
        </section>
    </div>
<? } ?>


<?=$tpl->logs;?>
<script type="text/javascript">
    $(document).ready(function(){
        validate_form_custom(".order-form");
        $("[name='ord_status']").change(function(){
            var val = parseInt($(this).val());
            if(val == 1)
            {
                $('#shipping_status').show();
            }else
            {
                $('#shipping_status').hide();
            }
        });

        var active_tab = "#<?=$CMS->input['tab'];?>";
        $("li.nav-item a[href='"+active_tab+"']").trigger("click");
    });


</script>

<!-- Action footer -->
<?=\core\ezy::render('action_show_footer', 'order');?>

<!-- html preview -->
<?=\core\ezy::render('preview', 'order');?>

<script>
    // Scroll to position
    <?if( !empty($CMS->input['position']) ){?>
    $(document).ready(function(){
        $(window).load(function() {
            scrollJumpto('#<?=$CMS->input['position'];?>', '.site-header');
        });
    });
    <?}?>
</script>
<!-- END MODAL HTML -->