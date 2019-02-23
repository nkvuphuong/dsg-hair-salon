var SalesReportByStore = {
    salesGeneralReport: null,
    salesGeneralReportID: "salesGeneralReportChart",
    salesGeneralReportChart: (start, end) => {
        var ctx = document.getElementById(SalesReportByStore.salesGeneralReportID);
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
        SalesReportByStore.salesGeneralReportChartUpdate(start, end);
    },
    salesGeneralReportChartUpdate: (start, end) => {
        let _this = this;
        blockLoading($("#" + SalesReportByStore.salesGeneralReportID).parent());
        $.ajax({
            url: site_root_domain + `/?site=report&act=sales&chart=general&data_type=json&start=${start}&end=${end}`,
            dataType: 'json',
            success: (data) => {
                SalesReportByStore.setData(_this.salesGeneralReport, data)
            },
            complete: () => {
                unblockLoading($("#" + SalesReportByStore.salesGeneralReportID).parent());
            }
        });
    },
    SalesReportByStoreByTime: null,
    SalesReportByStoreByTimeID: "reportByTime",
    SalesReportByStoreByTimeChart: (start, end, date_type) => {
        var ctx = document.getElementById(SalesReportByStore.SalesReportByStoreByTimeID);
        this.SalesReportByStoreByTime = new Chart(ctx, {
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
        SalesReportByStore.SalesReportByStoreByTimeChartUpdate(start, end, date_type);
    },
    SalesReportByStoreByTimeChartUpdate: (start, end, date_type) => {
        let _this = this;
        blockLoading($("#" + SalesReportByStore.SalesReportByStoreByTimeID).parent());
        $.ajax({
            url: site_root_domain + `/?site=report&act=sales&chart=by_time&data_type=json&start=${start}&end=${end}&date_type=${date_type}`,
            dataType: 'json',
            success: (data) => {
                SalesReportByStore.setData(_this.SalesReportByStoreByTime, data)
                $("#" + SalesReportByStore.SalesReportByStoreByTimeID).css("height", data.labels.length * data.datasets.length * 15)
            },
            complete: () => {
                unblockLoading($("#" + SalesReportByStore.SalesReportByStoreByTimeID).parent());
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

        SalesReportByStore.SalesReportByStoreByTimeChartUpdate(start, end, dateType);
        SalesReportByStore.salesGeneralReportChartUpdate(start, end);

        $(".change_time").removeClass("btn-primary").addClass("btn-success");
        obj.removeClass("btn-success").addClass("btn-primary");
    },
};


var SalesReportByStaff = {
    setData: function (chart, data) {
        chart.clear();
        chart.data = data;
        chart.update();
    },
    frmID: "chartOptions",
    eleID: "reportChart",
    chart: null,
    chartInit: function () {
        let ctx = document.getElementById(this.eleID);
        this.chart = new Chart(ctx, {
            type: 'horizontalBar',
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
    },
    chartInitUpdate: function () {
        let _this = this;
        let data = $("#" + _this.frmID).serialize();
        blockLoading($("#" + _this.eleID).parent());
        $.ajax({
            url: site_root_domain + `/?site=report&act=sales&subact=by_staffs&data_type=json`,
            data: data,
            dataType: 'json',
            success: (data) => {
                _this.setData(_this.chart, data);
                let h = data.labels.length * data.datasets.length * 50;
                h = h > 100 ? h : 100;
                $("#" + _this.eleID).css("height", h);
            },
            complete: () => {
                unblockLoading($("#" + _this.eleID).parent());
            }
        });
    },
    timeOptionClick: function (obj) {

        $("#" + this.frmID + " [name=start]").val(obj.attr('start'));
        $("#" + this.frmID + " [name=end]").val(obj.attr('end'));

        this.chartInitUpdate();

        $(".change_time").removeClass("btn-primary").addClass("btn-success");
        obj.removeClass("btn-success").addClass("btn-primary");
    },
    storeOptionClick: function (obj) {

        $("#" + this.frmID + " [name=store]").val(obj.attr('store'));

        this.chartInitUpdate();

        $(".change_store").removeClass("btn-primary").addClass("btn-success");
        obj.removeClass("btn-success").addClass("btn-primary");
    },
};

var SalesReportByService = {
    setData: function (chart, data) {
        chart.clear();
        chart.data = data;
        chart.update();
    },
    frmID: "chartOptions",
    eleID: "reportChart",
    chart: null,
    chartInit: function () {
        let ctx = document.getElementById(this.eleID);
        this.chart = new Chart(ctx, {
            type: 'horizontalBar',
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
    },
    chartInitUpdate: function () {
        let _this = this;
        let data = $("#" + _this.frmID).serialize();
        blockLoading($("#" + _this.eleID).parent());
        $.ajax({
            url: site_root_domain + `/?site=report&act=sales&subact=by_services&data_type=json`,
            data: data,
            dataType: 'json',
            success: (data) => {
                _this.setData(_this.chart, data);
                let h = data.labels.length * data.datasets.length * 50;
                h = h > 100 ? h : 100;
                $("#" + _this.eleID).css("height", h);
            },
            complete: () => {
                unblockLoading($("#" + _this.eleID).parent());
            }
        });
    },
    timeOptionClick: function (obj) {

        $("#" + this.frmID + " [name=start]").val(obj.attr('start'));
        $("#" + this.frmID + " [name=end]").val(obj.attr('end'));

        this.chartInitUpdate();

        $(".change_time").removeClass("btn-primary").addClass("btn-success");
        obj.removeClass("btn-success").addClass("btn-primary");
    },
    storeOptionClick: function (obj) {

        $("#" + this.frmID + " [name=store]").val(obj.attr('store'));

        this.chartInitUpdate();

        $(".change_store").removeClass("btn-primary").addClass("btn-success");
        obj.removeClass("btn-success").addClass("btn-primary");
    },
};


var UsersReport = {
    frmID: "chartOptions",
    init: function () {
        this.sales.init();
        this.orders.init();
        this.ratings.init();
    },
    update: function () {
        this.sales.update();
        this.orders.update();
        this.ratings.update();
    },
    chartOption: {
        type: 'horizontalBar',
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
    },
    sales: {
        eleID: 'salesChart',
        chart: null,
        init: function () {
            let ctx = document.getElementById(this.eleID);
            this.chart = new Chart(ctx, Object.assign({}, UsersReport.chartOption));
            this.update();
        },
        update: function () {
            let _this = this;
            let data = $("#" + UsersReport.frmID).serialize();
            blockLoading($("#" + _this.eleID).parent());
            $.ajax({
                url: site_root_domain + `/?site=report&act=users&chart=sales&data_type=json`,
                data: data,
                dataType: 'json',
                success: (data) => {
                    ;
                    UsersReport.setData(_this.chart, data);
                    let h = data.labels.length * data.datasets.length * 50;
                    h = h > 100 ? h : 100;
                    $("#" + _this.eleID).css("height", h);
                },
                complete: () => {
                    unblockLoading($("#" + _this.eleID).parent());
                }
            });
        }
    },
    orders: {
        eleID: 'ordersChart',
        chart: null,
        init: function () {
            let ctx = document.getElementById(this.eleID);
            this.chart = new Chart(ctx, Object.assign({}, UsersReport.chartOption));
            this.update();
        },
        update: function () {
            let _this = this;
            let data = $("#" + UsersReport.frmID).serialize();
            blockLoading($("#" + _this.eleID).parent());
            $.ajax({
                url: site_root_domain + `/?site=report&act=users&chart=orders&data_type=json`,
                data: data,
                dataType: 'json',
                success: (data) => {
                    UsersReport.setData(_this.chart, data);
                    let h = data.labels.length * data.datasets.length * 50;
                    h = h > 100 ? h : 100;
                    $("#" + _this.eleID).css("height", h);
                },
                complete: () => {
                    unblockLoading($("#" + _this.eleID).parent());
                }
            });
        }
    },
    ratings: {
        eleID: 'ratingsChart',
        chart: null,
        init: function () {
            let ctx = document.getElementById(this.eleID);
            this.chart = new Chart(ctx, {
                type: 'horizontalBar',
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    },
                }
            });

            this.update();
        },
        update: function () {
            let _this = this;
            let data = $("#" + UsersReport.frmID).serialize();
            blockLoading($("#" + _this.eleID).parent());
            $.ajax({
                url: site_root_domain + `/?site=report&act=users&chart=ratings&data_type=json`,
                data: data,
                dataType: 'json',
                success: (data) => {
                    UsersReport.setData(_this.chart, data);
                    let h = data.labels.length * data.datasets.length * 20;
                    h = h > 100 ? h : 100;
                    $("#" + _this.eleID).css("height", h);
                },
                complete: () => {
                    unblockLoading($("#" + _this.eleID).parent());
                }
            });
        }
    },
    setData: function (chart, data) {
        chart.clear();
        chart.data = data;
        chart.update();
    },
    timeOptionClick: function (obj) {

        $("#" + this.frmID + " [name=start]").val(obj.attr('start'));
        $("#" + this.frmID + " [name=end]").val(obj.attr('end'));

        this.update();

        $(".change_time").removeClass("btn-primary").addClass("btn-success");
        obj.removeClass("btn-success").addClass("btn-primary");
    },
    storeOptionClick: function (obj) {

        $("#" + this.frmID + " [name=store]").val(obj.attr('store'));

        this.update();

        $(".change_store").removeClass("btn-primary").addClass("btn-success");
        obj.removeClass("btn-success").addClass("btn-primary");
    },
};