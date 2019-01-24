<? if($CMS->permit['order_rating'] == 1  ) {?>
<a data-toggle="modal" data-target="#order-rating" class="pull-right add_cart_2"><i class="fa fa-star"></i> <?=$CMS->lang['act_rating'];?></a>
<? } ?>
<!-- MODAL HTML -->
<link rel="stylesheet" type="text/css" href='<?=$CMS->vars['root_domain']?>/css/bootstrap.min.css'>
<div class="modal fade feedback-modal" id="order-rating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body text-center">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">

                            <div class=" text-center">
                                <h6 class="modal-shop-name">Cửa hàng: <?=$tpl->data['store_name'];?></h6>
                                <button type="button" class="modal-close" data-dismiss="modal" aria-label="Close">
                                    <i class="font-icon-close-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="FeedbackChecksWrap" class="container">

                    <div class="row">
                        <div class="col-md-12">

                            <h1 class="modal-lg-title">Chỉ với 3 giây đánh giá</h1>
                            <h3 class="modal-md-title">bạn sẽ giúp chúng tôi cải thiện chất lượng dịch vụ tốt hơn</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-1">
                        </div>
                        <div class="col-lg-10">
                            <div class="row" id="choose_rating">
                                <?if($tpl->ratings) { ?>
                                    <?foreach ($tpl->ratings as $rating) { ?>
                                        <div class="col-md-4" onclick="orderChooseRating(<?=$rating['rating_value']?>)">
                                            <div class="feebback-wrap rate-val-<?=$rating['rating_value']?> <?=$rating['rating_value'] == $tpl->data['data_bk']['ord_commission_rating'] ? 'active' : ''?>">
                                                <?=html_entity_decode($rating['rating_label'])?>
                                            </div>
                                        </div>
                                    <?}?>
                                <?}?>
                            </div>
                        </div>
                        <div class="col-lg-1">
                        </div>
                        <input type="hidden" id="order_rating_value" value="<?=$tpl->data['data_bk']['ord_commission_rating']?>">
                        <div class="col-md-12"><button id="confirmFeedbackBtn" class="btn btn-lg btn-modal btn-rounded btn-primary" onclick="orderChooseRatingDo(<?=$tpl->data['data_bk']['ord_id']?>)">Xác nhận Đánh giá</button></div>
                    </div>

                </div>
                <div id="FeedbackthankyouWrap" class="container thankyou-wrap">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="feebback-wrap checked-ico"></div>
                        </div>
                        <div class="col-md-12">
                            <h1 class="modal-lg-title">Cảm ơn quý khách đã đánh giá</h1>
                            <p class="">Nhưng nhận xét chân thành của bạn sẽ giúp chũng tôi hoàn thiện dịch vụ được tốt hơn<br>Hạn gặp lại lần sau</p>

                        </div>
                    </div>
                </div>




            </div>
        </div>
    </div>
</div>

<script>
    $('#order-rating').on('shown.bs.modal', function () {
        $("#FeedbackthankyouWrap").hide();
        $("#FeedbackChecksWrap").show();
        $(".feedback-modal.in").removeClass("in").addClass("show");
    });
</script>