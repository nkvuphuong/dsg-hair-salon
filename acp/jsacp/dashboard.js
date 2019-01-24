function trxStatsDashBoard(timeType = 2, chartOptions = {})
{
    //prevent many request in 1 second
    if(getCookie('DashboardChartInit') == 1)
    {
        return false;
    }
    else
    {
        setCookie('DashboardChartInit',1,1);
    }

    blockLoading($(".dashboard-trx-sales-block"));
    $.ajax({
        url: `${site_root_domain}/?time_type=${timeType}&data_type=json&mod=trx`,
        dataType: 'json',
        success: function(res){
            unblockLoading($(".dashboard-trx-sales-block"));
            $("#dashboard-trx-sales-total").text(res.income_f);

            $.each(res.stats, function(key, data){
                $("#dashboard-trx-sales-" + key).text(data.data.total_f);
            });
        }
    })
}

function trxRevenueCostsChartDashBoard(timeType = 2, chartOptions = {})
{
    //prevent many request in 1 second
    if(getCookie('DashboardRevenueCostsChartInit') == 1)
    {
        return false;
    }
    else
    {
        setCookie('DashboardRevenueCostsChartInit',1,1);
    }

    blockLoading($(".dashboard-trx-revenue-costs-block"));
    $.ajax({
        url: `${site_root_domain}/?time_type=${timeType}&data_type=json&mod=trx&mod_type=revenue_costs`,
        dataType: 'json',
        success: function(res){
            unblockLoading($(".dashboard-trx-revenue-costs-block"));
            trxDashboardRevenueCostsChartInit(res.chart ,chartOptions);
        }
    })
}

function trxDashboardRevenueCostsChartInit(dataRow = [], options = {})
{
    dataRow = JSON.parse(dataRow);
    google.charts.load('current', {packages: ['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawMultSeries);

    function drawMultSeries() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Date');
        data.addColumn('number', cms_lang.title_report_sales);
        data.addColumn('number', cms_lang.title_report_expenses);
        data.addRows(dataRow);
        var chart = new google.visualization.ColumnChart(document.getElementById('chart_revenue_div'));
        chart.draw(data, options);

        google.visualization.events.addListener(chart, 'select', function(){
            var selection = chart.getSelection();
            var row = selection[0].row;
            if(row != null)
            {
                var col = selection[0].column;
                var date = data.getValue(row, 0);
                window.location.href = `${site_root_domain}/?site=transactions&type=${col}&dashboard_date=${date}`;
            }
        });
    }

    $(window).resize(function () {
        drawMultSeries();
    });
}

function orderSalesChartDashBoard(timeType = 2, chartOptions = {})
{
    //prevent many request in 1 second
    if(getCookie('DashboardOrderSalesChartInit') == 1)
    {
        return false;
    }
    else
    {
        setCookie('DashboardOrderSalesChartInit',1,1);
    }

    blockLoading($(".dashboard-order-sales-block"));
    $.ajax({
        url: `${site_root_domain}/?time_type=${timeType}&data_type=json&mod=order`,
        dataType: 'json',
        success: function(res){
            unblockLoading($(".dashboard-order-sales-block"));
            ordDashboardSalesChartInit(res.chart ,chartOptions);
        }
    })
}

function ordDashboardSalesChartInit(dataRow = [], options = {})
{
    dataRow = JSON.parse(dataRow);
    google.charts.load('current', {packages: ['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawMultSeries);

    function drawMultSeries() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Date');
        data.addColumn('number', cms_lang.title_report_sales);
        data.addRows(dataRow);
        var chart = new google.visualization.ColumnChart(document.getElementById('chart_ord_sales_div'));
        chart.draw(data, options);

        google.visualization.events.addListener(chart, 'select', function(){
            var selection = chart.getSelection();
            var row = selection[0].row;
            if(row != null)
            {
                var col = selection[0].column;
                var date = data.getValue(row, 0);
                window.location.href = `${site_root_domain}/?site=order&dashboard_date=${date}`;
            }
        });
    }

    $(window).resize(function () {
        drawMultSeries();
    });
}