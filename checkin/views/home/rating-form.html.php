<div class="modal fade feedback-modal" id="order-rating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class=" text-center">
                                <h6 class="modal-shop-name">Cửa hàng: <?= \lib\input::arrayValue($tpl->data->store, 'name') ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container" id="FeedbackChecksWrap">
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
                                <? if ($tpl->data->ratings) { ?>
                                    <? foreach ($tpl->data->ratings as $rating) { ?>
                                        <div class="col-md">
                                            <div class="feebback-wrap" onclick="SocketIOClient.chooseRatingValue($(this)); SocketIOClient.rate();" value="<?= $rating['rating_value'] ?>" rating-id=<?= $rating['rating_id'] ?> >
                                                <?= html_entity_decode($rating['rating_label']) ?>
                                            </div>
                                        </div>
                                    <? } ?>
                                <? } ?>
                            </div>
                        </div>
                        <div class="col-lg-1">
                        </div>
                        <input type="hidden" id="order_rating_value"
                               value="">
                        <div class="col-md-12">
                            <input type="hidden" name="store_id" id="store_id" value="<?= \lib\input::arrayValue($tpl->data->store, 'id') ?>">
                            <input type="hidden" name="ord-id" id="ord-id" value="0">
                            <input type="hidden" name="rating-value" id="rating-value" value="100">
                            <input type="hidden" name="rating-id" id="rating-id" value="1">
                        </div>
                    </div>

                </div>
                <div id="FeedbackthankyouWrap" class="container thankyou-wrap">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="feebback-wrap checked-ico"></div>
                        </div>
                        <div class="col-md-12">
                            <h1 class="modal-lg-title">Cảm ơn quý khách đã đánh giá</h1>
                            <p class="">Nhũng nhận xét chân thành của bạn sẽ giúp chũng tôi hoàn thiện dịch vụ được
                                tốt hơn<br>Hạn gặp lại lần sau</p>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>