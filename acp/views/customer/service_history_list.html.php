<section class="add_table main_form have_tab">
    <figure class="box-typical box-typical-padding border">
        <div class="row">
            <div class="form-group col-xl-12">
                <div class="form-control-wrapper">
                    <button type="button" class="btn btn-inline btn-success change_time"
                            start="<?=$tpl->yesterday?>" end="<?=$tpl->yesterday?>" onclick="LibExt.setDatetimeStartEndInput($(this), $('#time_from'), $('#time_to'))">Hôm qua
                    </button>
                    <button type="button" class="btn btn-inline btn-success change_time"
                            start="<?=$tpl->today?>" end="<?=$tpl->today?>" onclick="LibExt.setDatetimeStartEndInput($(this), $('#time_from'), $('#time_to'))">Hôm nay
                    </button>
                    <button type="button" class="btn btn-inline btn-success change_time"
                            start="<?=$tpl->last_week[0]?>" end="<?=$tpl->last_week[1]?>" onclick="LibExt.setDatetimeStartEndInput($(this), $('#time_from'), $('#time_to'))">Tuần trước
                    </button>
                    <button type="button" class="btn btn-inline btn-success change_time"
                            start="<?=$tpl->this_week[0]?>" end="<?=$tpl->this_week[1]?>" onclick="LibExt.setDatetimeStartEndInput($(this), $('#time_from'), $('#time_to'))">Tuần này
                    </button>
                    <button type="button" class="btn btn-inline btn-success change_time"
                            start="<?=$tpl->last_month[0]?>" end="<?=$tpl->last_month[1]?>" onclick="LibExt.setDatetimeStartEndInput($(this), $('#time_from'), $('#time_to'))">Tháng trước
                    </button>
                    <button type="button" class="btn btn-inline btn-success change_time"
                            start="<?=$tpl->this_month[0]?>" end="<?=$tpl->this_month[1]?>" onclick="LibExt.setDatetimeStartEndInput($(this), $('#time_from'), $('#time_to'))">Tháng này
                    </button>
                    <button type="button" class="btn btn-inline btn-success change_time"
                            start="<?=$tpl->last_year[0]?>" end="<?=$tpl->last_year[1]?>" onclick="LibExt.setDatetimeStartEndInput($(this), $('#time_from'), $('#time_to'))">Năm trước
                    </button>
                    <button type="button" class="btn btn-inline btn-success change_time"
                            start="<?=$tpl->this_year[0]?>" end="<?=$tpl->this_year[1]?>" onclick="LibExt.setDatetimeStartEndInput($(this), $('#time_from'), $('#time_to'))">Năm này
                    </button>
                </div>
            </div>

            <form method="get" action="<?=$CMS->vars['root_domain']?>/?site=<?=\lib\input::get('site')?>&act=service_history">
                <div class="form-group col-xl-12 box_date_change">
                    <div class="row">
                        <div class="col-xl-2">
                            <label class="form-label">Từ ngày</label>
                            <div class="input-group">
                                <input type="text" name="ordi_time_from" id="time_from"
                                       class="form-control date-picker" placeholder="Time from"
                                       value="">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2">
                            <label class="form-label">Đến ngày</label>
                            <div class="input-group">
                                <input type="text" name="ordi_time_to" id="time_to"  class="form-control date-picker"
                                       placeholder="Time to" value="">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2">
                            <label class="form-label" style="height: 18px;"></label>
                            <div class="input-group">
                                <button type="button"
                                        class="btn btn-primary btn_view_report">Lọc
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </figure>

</section>

<section class="add_table main_form have_tab">
    <figure class="heading">
        <h3><span id="detail_title">Thông tin</span>: <span class="time_title"></span></h3>
    </figure>
    <figure class="box-typical border">
        <table id="table-edit" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th width="1">#</th>
                <th>Mã đơn hàng</th>
                <th>Dịch vụ</th>
                <th>Lịch hẹn</th>
                <th>NV phụ trách</th>
                <th>Tổng tiền</th>
                <th>Ngày đặt</th>
            </tr>
            </thead>
            <tbody id="order-items-list">
            <? if($tpl->data) { ?>
                <?
                    $bgcolor = '';
                    $lastOrdId = 0;
                    foreach ($tpl->data as $item) {

                        $item = \models\order_item::convertValue($item);

                        if ($lastOrdId != $item['data_bk']['ord_id']) {
                            $bgcolor = $bgcolor == '#ddd' ? '' : '#ddd';
                        }

                        $lastOrdId = $item['data_bk']['ord_id'];

                ?>
                    <tr class="row-item-<?=$item['data_bk']['ord_id']?>" style="<?=$bgcolor ? "background-color: {$bgcolor}" : ""?>">
                        <td>#<?=$item['ordi_id']?></td>
                        <td class="order_group"><?=$item['ord_id']?></td>
                        <td><?=$item['ordi_name']?></td>
                        <td><?=$item['ordi_booking_time']?></td>
                        <td><?=$item['staff_id']?></td>
                        <td><?=$item['ordi_total']?></td>
                        <td><?=$item['ordi_time']?></td>
                    </tr>
                <? } ?>
            <? } ?>
            </tbody>
        </table>
    </figure>
</section>
<script>
    let rows = $("#order-items-list tr");
    let curClassRow = "";

    $.each( rows, function( key, value ) {
        let nextclassRow = $(value).attr("class");

        if (curClassRow !== nextclassRow) {
            curClassRow = nextclassRow;
            let len = $("." + curClassRow).length;
            if (len > 1) {
                $("." + curClassRow + ":first").find("td.order_group").attr('rowspan', len);
                $("." + curClassRow + ":gt(0)").find("td.order_group").remove();
            }
        }
    });

</script>