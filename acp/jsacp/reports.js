var SalesReport = {
    salesGeneralReport: null,
    salesGeneralReportID: "salesGeneralReportChart",
    salesGeneralReportChart: (start, end) => {
        var ctx = document.getElementById(SalesReport.salesGeneralReportID);
        this.salesGeneralReport = new Chart(ctx, {
            type: 'pie',
            // data: data,
            options: {
                tooltips: {
                    callbacks: {
                        label: function (tooltipItem, data) {
                            var label = data.labels[tooltipItem.index] || '';
                            if (label) {
                                label += ': ';
                            }
                            label += data.extra[tooltipItem.index];
                            return label;
                        }
                    }
                }
            }
        });
        SalesReport.salesGeneralReportChartUpdate(start, end);
    },
    salesGeneralReportChartUpdate: (start, end) => {
        let _this = this;
        blockLoading($("#" + SalesReport.salesGeneralReportID).parent());
        $.ajax({
            url: site_root_domain + `/?site=report&act=sales&chart=general&data_type=json&start=${start}&end=${end}`,
            dataType: 'json',
            success: (data) => {
                SalesReport.setData(_this.salesGeneralReport, data)
            },
            complete: () => {
                unblockLoading($("#" + SalesReport.salesGeneralReportID).parent());
            }
        });
    },
    salesReportByTime: null,
    salesReportByTimeID: "reportByTime",
    salesReportByTimeChart: (start, end, date_type) => {
        var ctx = document.getElementById(SalesReport.salesReportByTimeID);
        this.salesReportByTime = new Chart(ctx, {
            type: 'horizontalBar',
            // data: data,
            options: {
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                tooltips: {
                    callbacks: {
                        label: function (tooltipItem, data) {
                            let dataItem = data.datasets[tooltipItem.datasetIndex];
                            let label = '';

                            if (dataItem) {
                                label = dataItem.label || '';
                            }

                            if (label) {
                                label += ': ';
                            }

                            label += dataItem.extra[tooltipItem.index] || '';
                            return label;
                        }
                    }
                }
            }
        });
        SalesReport.salesReportByTimeChartUpdate(start, end, date_type);
    },
    salesReportByTimeChartUpdate: (start, end, date_type) => {
        let _this = this;
        blockLoading($("#" + SalesReport.salesReportByTimeID).parent());
        $.ajax({
            url: site_root_domain + `/?site=report&act=sales&chart=by_time&data_type=json&start=${start}&end=${end}&date_type=${date_type}`,
            dataType: 'json',
            success: (data) => {
                SalesReport.setData(_this.salesReportByTime, data)
                $("#" + SalesReport.salesReportByTimeID).css("height", data.labels.length * data.datasets.length * 15)
            },
            complete: () => {
                unblockLoading($("#" + SalesReport.salesReportByTimeID).parent());
            }
        });
    },
    setData: (chart, data) => {
        chart.clear();
        chart.data = data;
        chart.update();
    },
    timeOptionClick: (obj) => {
        let start = obj.attr('start');
        let end = obj.attr('end');
        let dateType = obj.attr('date-type');

        SalesReport.salesReportByTimeChartUpdate(start, end, dateType);
        SalesReport.salesGeneralReportChartUpdate(start, end);
    }
};