<header class="page-content-header">
    <div class="container-fluid">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h3>Thống kê doanh số</h3>
                    <hr>
                    <div class="row">
                        <div class="form-group col-xl-12">
                            <div class="form-control-wrapper">
                                <button type="button" class="btn btn-inline btn-success change_time"
                                        start="<?=$tpl->last_week[0]?>" end="<?=$tpl->last_week[1]?>" date-type="week" onclick="SalesReport.timeOptionClick($(this))">Tuần trước
                                </button>
                                <button type="button" class="btn btn-inline btn-success change_time"
                                        start="<?=$tpl->this_week[0]?>" end="<?=$tpl->this_week[1]?>" date-type="week" onclick="SalesReport.timeOptionClick($(this))">Tuần này
                                </button>
                                <button type="button" class="btn btn-inline btn-success change_time"
                                        start="<?=$tpl->last_month[0]?>" end="<?=$tpl->last_month[1]?>" date-type="month" onclick="SalesReport.timeOptionClick($(this))">Tháng trước
                                </button>
                                <button type="button" class="btn btn-inline btn-success change_time"
                                        start="<?=$tpl->this_month[0]?>" end="<?=$tpl->this_month[1]?>" date-type="month" onclick="SalesReport.timeOptionClick($(this))">Tháng này
                                </button>
                                <button type="button" class="btn btn-inline btn-success change_time"
                                        start="<?=$tpl->last_quarter[0]?>" end="<?=$tpl->last_quarter[1]?>" date-type="quarter" onclick="SalesReport.timeOptionClick($(this))">Quý trước
                                </button>
                                <button type="button" class="btn btn-inline btn-success change_time"
                                        start="<?=$tpl->this_quarter[0]?>" end="<?=$tpl->this_quarter[1]?>" date-type="quarter" onclick="SalesReport.timeOptionClick($(this))">Quý này
                                </button>
                                <button type="button" class="btn btn-inline btn-success change_time"
                                        start="<?=$tpl->last_year[0]?>" end="<?=$tpl->last_year[1]?>" date-type="year" onclick="SalesReport.timeOptionClick($(this))">Năm trước
                                </button>
                                <button type="button" class="btn btn-inline btn-success change_time"
                                        start="<?=$tpl->this_year[0]?>" end="<?=$tpl->this_year[1]?>" date-type="year" onclick="SalesReport.timeOptionClick($(this))">Năm này
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header><!--.page-content-header-->

<div class="row">
    <div class="col-md-7">
        <section class="card">
            <header class="card-header">
                Theo thời gian
            </header>
            <div class="card-block">
                <canvas id="reportByTime"></canvas>
            </div>
        </section>
    </div>
    <div class="col-md-5">
        <section class="card">
            <header class="card-header">
                Tổng quan
            </header>
            <div class="card-block">
                <canvas id="salesGeneralReportChart"></canvas>
            </div>
        </section>
    </div>
</div>


<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>
<script type="text/javascript" src="<?=$CMS->vars['parent_domain']?>/acp/assets/js/chartjs-plugin-colorschemes.min.js"></script>
<script type="text/javascript" src="<?=$CMS->vars['parent_domain']?>/acp/jsacp/reports.js"></script>
<script>
    $(".container-fluid.messenger").removeClass("container-fluid").removeClass("messenger");
</script>
<script>
    SalesReport.salesGeneralReportChart('<?=$tpl->this_week[0]?>', '<?=$tpl->this_week[1]?>');
    SalesReport.salesReportByTimeChart('<?=$tpl->this_week[0]?>', '<?=$tpl->this_week[1]?>', 'week');
</script>