var chart;

$(document).ready(function(){

    $("input[name='time_from'], input[name='report_start'], input[name='report_end'], input[name='report_day']").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "dd/mm/yy",
    });

    loadReportData();

});

function thisMonth(obj){
    var m = getFisrtLastInCurrentMonth();
    $("input[name='report_start']" ).datepicker().datepicker("setDate", m[0]);
    $( "input[name='report_end']" ).datepicker().datepicker("setDate", m[1]);

    obj.parents("ul:first").find("li.active").removeClass('active');
    obj.parents("li:first").addClass("active");

    loadReportData();
}

function lastMonth(obj){
    var m = getFisrtLastInCurrentMonth();
    $("input[name='report_start']" ).datepicker().datepicker("setDate", m[0].addMonths(-1));
    $("input[name='report_end']" ).datepicker().datepicker("setDate", m[1].addMonths(-1));

    obj.parents("ul:first").find("li.active").removeClass('active');
    obj.parents("li:first").addClass("active");

    loadReportData();
}

function thisWeek(obj){
    var w = getFisrtLastInCurrentWeek();
    $("input[name='report_start']" ).datepicker().datepicker("setDate", w[0]);
    $("input[name='report_end']" ).datepicker().datepicker("setDate", w[1]);

    obj.parents("ul:first").find("li.active").removeClass('active');
    obj.parents("li:first").addClass("active");

    loadReportData();
}

function lastWeek(obj){
    var w = getFisrtLastInCurrentWeek();
    $("input[name='report_start']" ).datepicker().datepicker("setDate", w[0].addDays(-7));
    $("input[name='report_end']" ).datepicker().datepicker("setDate", w[1].addDays(-7));

    obj.parents("ul:first").find("li.active").removeClass('active');
    obj.parents("li:first").addClass("active");

    loadReportData();
}

function allTime(obj){
    $("input[name='report_start']" ).val("");
    $("input[name='report_end']" ).val("");

    obj.parents("ul:first").find("li.active").removeClass('active');
    obj.parents("li:first").addClass("active");

    loadReportData();
}

function chartInit()
{
    var data = $("form#report_transaction").serialize();

    return c3.generate({
        data: {
            x: 'x',
            mimeType: 'json',
            url: '?site=report&act=report_transaction&ajax=1&type=chart&'+data
        },
        axis: {
            x: {
                type: 'timeseries',
                tick: {
                    format: '%d-%m-%Y'
                }
            }
        },
        tooltip: {
            format: {
                value: function (value, ratio, id) {
                    var format = d3.format("$.,2f");
                    return format(value);
                }
            }
        }
    });
}

function tableInit()
{
    var data = $("form#report_transaction").serialize();

    $("#transaction_report_table").find("tbody tr td").html("0");

    $.ajax({
        url: '?site=report&act=report_transaction&ajax=1&type=table&'+data,
        success: function(data){
            $.each(data, function(k,v){
                $("#"+k).html(v);
            })
        }
    })
}

function loadReportData()
{
    if(chart)
    {
        chart.load({unload: true});
    }

    chart = chartInit();
    tableInit();
}
