<header class="page-content-header">
    <div class="container-fluid">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h3>
                        Thống kê nhân viên
                    </h3>
                    <hr>
                    <div class="row">
                        <div class="col-xl-12">
                            <h5 class="">Cửa tiệm: </h5>
                            <button type="button" class="btn btn-primary change_store"
                                    store="0" onclick="UsersReport.storeOptionClick($(this))">Tất cả
                            </button>
                            <? if ($tpl->stores) { ?>
                                <? foreach ($tpl->stores as $store) { ?>
                                    <button type="button" class="btn btn-success change_store"
                                            store="<?= $store['store_id'] ?>"
                                            onclick="UsersReport.storeOptionClick($(this))"><?= $store['store_name'] ?>
                                    </button>
                                <? } ?>
                            <? } ?>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-xl-12">
                            <h5 class="">Thời gian: </h5>
                            <button type="button" class="btn btn-primary change_time"
                                    start="<?= $tpl->today ?>" end="<?= $tpl->today ?>" date-type="day"
                                    onclick="UsersReport.timeOptionClick($(this))">Hôm nay
                            </button>
                            <button type="button" class="btn btn-success change_time"
                                    start="<?= $tpl->yesterday ?>" end="<?= $tpl->yesterday ?>" date-type="day"
                                    onclick="UsersReport.timeOptionClick($(this))">Hôm qua
                            </button>
                            <button type="button" class="btn btn-success change_time"
                                    start="<?= $tpl->this_week[0] ?>" end="<?= $tpl->this_week[1] ?>" date-type="week"
                                    onclick="UsersReport.timeOptionClick($(this))">Tuần này
                            </button>
                            <button type="button" class="btn btn-success change_time"
                                    start="<?= $tpl->last_week[0] ?>" end="<?= $tpl->last_week[1] ?>" date-type="week"
                                    onclick="UsersReport.timeOptionClick($(this))">Tuần trước
                            </button>
                            <button type="button" class="btn btn-success change_time"
                                    start="<?= $tpl->this_month[0] ?>" end="<?= $tpl->this_month[1] ?>"
                                    date-type="month" onclick="UsersReport.timeOptionClick($(this))">Tháng này
                            </button>
                            <button type="button" class="btn btn-success change_time"
                                    start="<?= $tpl->last_month[0] ?>" end="<?= $tpl->last_month[1] ?>"
                                    date-type="month" onclick="UsersReport.timeOptionClick($(this))">Tháng trước
                            </button>
                            <button type="button" class="btn btn-success change_time"
                                    start="<?= $tpl->this_quarter[0] ?>" end="<?= $tpl->this_quarter[1] ?>"
                                    date-type="quarter" onclick="UsersReport.timeOptionClick($(this))">Quý này
                            </button>
                            <button type="button" class="btn btn-success change_time"
                                    start="<?= $tpl->last_quarter[0] ?>" end="<?= $tpl->last_quarter[1] ?>"
                                    date-type="quarter" onclick="UsersReport.timeOptionClick($(this))">Quý trước
                            </button>
                            <button type="button" class="btn btn-success change_time"
                                    start="<?= $tpl->this_year[0] ?>" end="<?= $tpl->this_year[1] ?>" date-type="year"
                                    onclick="UsersReport.timeOptionClick($(this))">Năm này
                            </button>
                            <button type="button" class="btn btn-success change_time"
                                    start="<?= $tpl->last_year[0] ?>" end="<?= $tpl->last_year[1] ?>" date-type="year"
                                    onclick="UsersReport.timeOptionClick($(this))">Năm trước
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header><!--.page-content-header-->

<div class="row">
    <div class="col-md-4">
        <section class="card">
            <header class="card-header">
                Doanh thu
            </header>
            <div class="card-block">
                <canvas id="salesChart"></canvas>
            </div>
        </section>
    </div>
    <div class="col-md-4">
        <section class="card">
            <header class="card-header">
                Đơn hàng
            </header>
            <div class="card-block">
                <canvas id="ordersChart"></canvas>
            </div>
        </section>
    </div>
    <div class="col-md-4">
        <section class="card">
            <header class="card-header">
                Đánh giá
            </header>
            <div class="card-block">
                <canvas id="ratingsChart"></canvas>
            </div>
        </section>
    </div>
</div>

<form id="chartOptions">
    <input type="hidden" name="start" value="<?= $tpl->today ?>">
    <input type="hidden" name="end" value="<?= $tpl->today ?>">
    <input type="hidden" name="store" value="0">
</form>


<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>
<script type="text/javascript"
        src="<?= $CMS->vars['parent_domain'] ?>/acp/assets/js/chartjs-plugin-colorschemes.min.js"></script>
<script type="text/javascript" src="<?= $CMS->vars['parent_domain'] ?>/acp/jsacp/reports.js"></script>
<script>
    $(".container-fluid.messenger").removeClass("container-fluid").removeClass("messenger");
</script>
<script>
    UsersReport.init();
</script>