function setSiteChartSelect(obj, value, func = 'siteChartByUsers', runEvent = 1) {
    obj.val(value);
    if (runEvent) {
        if (func == 'siteChartByUsers') {
            siteChartByUsers(highChartDefaultOptions, $("#chartSiteByUsersBlock #setAssignType").val(), $("#chartSiteByUsersBlock #setTimeType").val(), $("#chartSiteByUsers #setTimeField").val());
        }
        else if (func == 'siteChartByThemes') {
            siteChartByThemes(highChartDefaultOptions, $("#chartSiteByThemesBlock #setTimeType").val(), $("#chartSiteByThemesBlock #setTimeField").val());
        }
        else if (func == 'themeChartByUsers') {
            themeChartByUsers(highChartDefaultOptions, $("#chartThemeByUsersBlock #setAssignType").val(), $("#chartThemeByUsersBlock #setTimeType").val(), $("#chartThemeByUsersBlock #setTimeField").val());
        }
    }
}

function setEventChartDropdown() {
    $('[event]').each(function (index) {
        $(this).attr($(this).attr('event'), $(this).attr('func'));
    });
}

function siteChartDropdownInit() {
    dropdownInit($(".sites-assign-chart-dropdown"), 0);
    dropdownInit($(".sites-time-chart-dropdown"), 1);
    dropdownInit($(".sites-time-field-chart-dropdown"), 0);
}

function siteChartByUserInit() {
    setSiteChartSelect($('#chartSiteByUsersBlock #setAssignType'), 'site_editor', 'siteChartByUsers', 0);
    setSiteChartSelect($('#chartSiteByUsersBlock #setTimeType'), '2', 'siteChartByUsers', 0);
    setSiteChartSelect($('#chartSiteByUsersBlock #setTimeField'), 'site_time', 'siteChartByUsers', 0);
    siteChartByUsers(highChartDefaultOptions, $("#chartSiteByUsersBlock #setAssignType").val(), $("#chartSiteByUsersBlock #setTimeType").val(), $("#chartSiteByUsersBlock #setTimeField").val());
    setEventChartDropdown();
}

function siteChartByUsers(options, assignType = 'site_editor', timeType = '2', timeField = 'site_time', blockLoadingObj = $("#chartByUser")) {
    blockLoading(blockLoadingObj);
    $.getJSON('/?site=dashboard&subact=get_chart_data&type=sites_by_users&assign_type=' + assignType + '&time_type=' + timeType + '&time_field=' + timeField, function (res) {
        unblockLoading(blockLoadingObj);
        options.title.text = res.title;
        options.chart.renderTo = "chartByUser";
        options.series[0].data = res.data;
        new Highcharts.Chart(options);
    });
}

function siteChartByThemeInit() {
    setSiteChartSelect($('#chartSiteByThemesBlock #setTimeType'), '2', 'siteChartByUsers', 0);
    setSiteChartSelect($('#chartSiteByThemesBlock #setTimeField'), 'site_time', 'siteChartByUsers', 0);
    siteChartByThemes(highChartDefaultOptions, $("#chartSiteByThemesBlock #setTimeType").val(), $("#chartSiteByThemesBlock #setTimeField").val());
    setEventChartDropdown();
}

function siteChartByThemes(options, timeType = '2', timeField = 'site_time', blockLoadingObj = $("#chartByTheme")) {
    blockLoading(blockLoadingObj);
    $.getJSON('/?site=dashboard&subact=get_chart_data&type=sites_by_themes&time_type=' + timeType + '&time_field=' + timeField, function (res) {
        unblockLoading(blockLoadingObj);
        options.title.text = res.title;
        options.chart.renderTo = "chartByTheme";
        options.series[0].data = res.data;
        new Highcharts.Chart(options);
    });
}

function themeChartByUserInit() {
    setSiteChartSelect($('#chartThemeByUsersBlock #setAssignType'), 'theme_layout_designer', 'siteChartByUsers', 0);
    setSiteChartSelect($('#chartThemeByUsersBlock #setTimeType'), '2', 'siteChartByUsers', 0);
    setSiteChartSelect($('#chartThemeByUsersBlock #setTimeField'), 'theme_time', 'siteChartByUsers', 0);
    themeChartByUsers(highChartDefaultOptions, $("#chartThemeByUsersBlock #setAssignType").val(), $("#chartThemeByUsersBlock #setTimeType").val(), $("#chartThemeByUsersBlock #setTimeField").val());
    setEventChartDropdown();
}

function themeChartByUsers(options, assignType = 'site_editor', timeType = '2', timeField = 'site_time', blockLoadingObj = $("#chartThemesByUser")) {
    blockLoading(blockLoadingObj);
    $.getJSON('/?site=dashboard&subact=get_chart_data&type=themes_by_users&assign_type=' + assignType + '&time_type=' + timeType + '&time_field=' + timeField, function (res) {
        unblockLoading(blockLoadingObj);
        options.title.text = res.title;
        options.chart.renderTo = "chartThemesByUser";
        options.series[0].data = res.data;
        new Highcharts.Chart(options);
    });
}

function loadDetailCalendar(data)
{
    let field_time = typeof data.field_time ==  'undefined' ? '' : data.field_time;
    let site_regtype = $('#calendar-filter [name=site_regtype]:first').val();

    $('#calendarModal .calendarModalContent').html("");

    blockLoading($("#calendar-container"));

    $.ajax({
        url: `/?site=dashboard&subact=load_detail_calendar&type=${data.type}&date=${data.dateSearch}&site_field_time=${field_time}&site_regtype=${site_regtype}`,
        dataType: 'json',
        success: function(res){
            unblockLoading($("#calendar-container"));
            $('#calendarModal .calendarModalContent').html("");
            if(res.items)
            {
                $.each(res.items, function(key, item){
                    if(data.type == 'theme')
                    {
                        $('#calendarModal .model_panel').text('Themes');

                        $('#calendarModal .calendarModalContent').append(
                            `<p class="card-text"><a target="_blank" href="${item.href}">${item.text}</a></p>`
                        );
                    }
                    else if (data.type == 'site')
                    {
                        if(field_time == 'site_time')
                        {
                            $('#calendarModal .model_panel').text(cms_lang.created_websites);
                        }
                        else
                        {
                            $('#calendarModal .model_panel').text(cms_lang.done_websites);
                        }

                        $('#calendarModal .calendarModalContent').append(
                            `<p class="card-text"><a href="/?site=sites&type=${item.site_regtype}&${field_time}=${data.dateSearch}&${field_time}_to=${data.dateSearch}">${item.text}: ${item.cnt}</a></p>`
                        );
                    }
                });
            }

            $('#calendarModal').modal('show');

        }
    })
}

let calendarOnInit = 1;
let dashboardCalendar = null;

function calendarInit(dateFormat = 'YYYY-MM-DD', start, end) {
        /* ==========================================================================
            Fullcalendar
            ========================================================================== */

        $start = start;
        $end = end;

        let $defaultDate = start ? start : (end ? end : null);
        let $defaultView = $('#calendarViewType').val();

        dashboardCalendar = $('#calendar').fullCalendar({
            titleFormat: {
                month: `(${dateFormat})`,                             // September 2009
                week: `(${dateFormat})`,
                day: dateFormat                  // Tuesday, Sep 8, 2009
            },
            defaultDate: $defaultDate,
            defaultView: $defaultView,
            header: {
                left: '',
                center: 'prev, title, next',
                right: 'today agendaDay,agendaTwoDay,agendaWeek,month'
            },
            buttonIcons: {
                prev: 'font-icon font-icon-arrow-left',
                next: 'font-icon font-icon-arrow-right',
                prevYear: 'font-icon font-icon-arrow-left',
                nextYear: 'font-icon font-icon-arrow-right'
            },
            editable: false,
            selectable: true,
            eventLimit: false, // allow "more" link when too many events
            /*events: {
                url: '/?subact=load_calendar_data&return_for=calendar',
                data: function(e) { // a function that returns an object
                    let view = $('#calendar').fullCalendar('getView');
                    return {'startD': view.start.format(dateFormat), 'endD': view.end.add(-1,'d').format(dateFormat)};
                },
                cache: true
            },*/
            events: function(start, end, timezone, callback) {

                $start = $('#calendar-filter').find('[name=startD]:first').val();
                $end = $('#calendar-filter').find('[name=endD]:first').val();

                /*if(calendarOnInit)
                {
                    calendarOnInit = 0;
                }
                else
                {
                    let $view = $('#calendar').fullCalendar('getView');
                    $start = $view.start.format(dateFormat);
                    $end = $view.end.add(-1,'d').format(dateFormat);
                }*/

                let $view = $('#calendar').fullCalendar('getView');
                $startCalendar = $view.start.format(dateFormat);
                $endCalendar = $view.end.add(-1,'d').format(dateFormat);

                let $site_regtype = $('#calendar-filter [name=site_regtype]:first').val();

                $.ajax({
                    url: '/?subact=load_calendar_data&return_for=calendar',
                    dataType: 'json',
                    data: {
                        'startD': $start,
                        'endD': $end,
                        'startC': $startCalendar,
                        'endC': $endCalendar,
                        'site_regtype': $site_regtype,
                    },
                    success: function(res) {
                        callback(res);
                    }
                });
            },
            viewRender: function (view, element) {
                if (!("ontouchstart" in document.documentElement)) {
                    $('.fc-scroller').jScrollPane({
                        autoReinitialise: true,
                        autoReinitialiseDelay: 100
                    });
                }

                if(calendarOnInit == 1)
                {
                    calendarOnInit = 0;
                }
                else
                {
                    let $startEnd = view.title.split(" — ");
                    let $start = $startEnd[0];
                    let $end = $startEnd[1];

                    $("#calendar-filter").find('[name=start]').val($start);
                    $("#calendar-filter").find('[name=end]').val($end);
                }


                $("#calendarViewType").val(view.type);

                $('.fc-popover.click').remove();
            },
            loading: function( isLoading, view ) {
                if(isLoading) {// isLoading gives boolean value
                    blockLoading($('#calendar-container'));
                } else {
                    unblockLoading($('#calendar-container'));
                    // $('#calendar-filter').find(':text[name="start"]').val(view.start.format(dateFormat));
                    // $('#calendar-filter').find(':text[name="end"]').val(view.end.format(dateFormat));
                }
            },
            eventClick: function (calEvent, jsEvent, view) {
                var eventEl = $(this);

                loadDetailCalendar(calEvent);

                // Add and remove event border class
                if (!$(this).hasClass('event-clicked')) {
                    $('.fc-event').removeClass('event-clicked');

                    $(this).addClass('event-clicked');
                }

                // Datepicker init
                $('.fc-popover.click .datetimepicker').datetimepicker({
                    widgetPositioning: {
                        horizontal: 'right'
                    }
                });

                $('.fc-popover.click .datetimepicker-2').datetimepicker({
                    widgetPositioning: {
                        horizontal: 'right'
                    },
                    format: 'LT',
                    debug: true
                });


                // Position popover
                function posPopover() {
                    $('.fc-popover.click').css({
                        left: eventEl.offset().left + eventEl.outerWidth() / 2,
                        top: eventEl.offset().top + eventEl.outerHeight()
                    });
                }

                posPopover();

                $('.fc-scroller, .calendar-page-content, body').scroll(function () {
                    posPopover();
                });

                $(window).resize(function () {
                    posPopover();
                });


                // Remove old popover
                if ($('.fc-popover.click').length > 1) {
                    for (var i = 0; i < ($('.fc-popover.click').length - 1); i++) {
                        $('.fc-popover.click').eq(i).remove();
                    }
                }

                // Close buttons
                $('.fc-popover.click .cl, .fc-popover.click .remove-popover').click(function () {
                    $('.fc-popover.click').remove();
                    $('.fc-event').removeClass('event-clicked');
                });

                // Actions link
                $('.fc-event-action-edit').click(function (e) {
                    e.preventDefault();

                    $('.fc-popover.click .main-screen').hide();
                    $('.fc-popover.click .edit-event').show();
                });

                $('.fc-event-action-remove').click(function (e) {
                    e.preventDefault();

                    $('.fc-popover.click .main-screen').hide();
                    $('.fc-popover.click .remove-confirm').show();
                });
            }
        });


    /* ==========================================================================
        Calendar page grid
        ========================================================================== */

    (function ($, viewport) {
        $(document).ready(function () {

            if (viewport.is('>=lg')) {
                $('.calendar-page-content, .calendar-page-side').matchHeight();
            }

            // Execute code each time window size changes
            $(window).resize(
                viewport.changed(function () {
                    if (viewport.is('<lg')) {
                        $('.calendar-page-content, .calendar-page-side').matchHeight({remove: true});
                    }
                })
            );
        });
    })(jQuery, ResponsiveBootstrapToolkit);


}

function filterCalendarData(start='', end = '')
{
    let container = $("#calendar-filter-result");
    container.find('.result_item').html('');
    blockLoading($('#calendar-filter-result'));

    let $site_regtype = $('#calendar-filter [name=site_regtype]:first').val();

    $.ajax({
        url: '/?subact=load_calendar_data&return_for=filter',
        data: {
            'startD': start,
            'endD': end,
            'site_regtype': $site_regtype
        },
        success: function(res){

            unblockLoading($('#calendar-filter-result'));

            container.find('.board_stats_item').attr('start',res.startSearch);
            container.find('.board_stats_item').attr('end',res.endSearch);

            $('#calendar-filter').find('[name=startD]').val(start);
            $('#calendar-filter').find('[name=endD]').val(end);

            if(dashboardCalendar)
            {
                $('#calendar').fullCalendar('destroy');
                dashboardCalendar = null;
            }

            calendarOnInit=1;

            calendarInit(calendarDateFormat, start, end);

            let from_to_date = '';
            let site_regtype_text = typeof cms_lang['site_regtype_'+$site_regtype] != 'undefined' ? cms_lang['site_regtype_'+$site_regtype]+': ' : '';

            if(res.startSearch != null && res.endSearch != null)
            {
                from_to_date += `${res.startSearch} ${cms_lang.to} ${res.endSearch}`;
            }
            else if(res.startSearch != null)
            {
                from_to_date += `${cms_lang.from} ${res.startSearch}`;
            }
            else if(res.endSearch != null)
            {
                from_to_date += `${cms_lang.to} ${res.endSearch}`;
            }
            else
            {
                from_to_date += 'All time';
            }

            if(from_to_date!='' || site_regtype_text!='')
            {
                from_to_date = ` (${site_regtype_text}${from_to_date})`;
            }

            $('.from_to_date_title').html(from_to_date);

            if(res.items)
            {
                $.each(res.items, function(i, item){
                    container.find('.' + item.type).html(`(${item.cnt})`);

                    if(item.type=='theme')
                    {
                        container.find('.' + item.type).parents('a:first').attr("href","/?site=themes&theme_time=" + (res.startSearch == null ? '' : res.startSearch) +"&theme_time_to="+(res.endSearch == null ? '' : res.endSearch));
                    }
                    else
                    {
                        let url = "";
                        if(item.type == 'site_created')
                        {
                            url += "site_time=" + (res.startSearch == null ? '' : res.startSearch) +"&site_time_to="+(res.endSearch == null ? '' : res.endSearch);
                        }
                        else if(item.type == 'site_done')
                        {
                            url += "change_domain_time=" + (res.startSearch == null ? '' : res.startSearch) +"&change_domain_time_to="+(res.endSearch == null ? '' : res.endSearch);
                        }

                        container.find('.' + item.type).parents('a:first').attr("href","/?site=sites&" + url);
                        container.find('.' + item.type).trigger('dblclick');
                    }
                })
            }
        }
    })
}

function loadDetailCalendarFilter(obj)
{
    let field_time = typeof obj.attr('filter_time') ==  'undefined' ? '' : obj.attr('filter_time');
    let type = typeof obj.attr('type') ==  'undefined' ? '' : obj.attr('type');
    let data_target = typeof obj.attr('data-target') ==  'undefined' ? '' : obj.attr('data-target');
    let start = typeof obj.attr('start') ==  'undefined' ? '' : obj.attr('start');
    let end = typeof obj.attr('end') ==  'undefined' ? '' : obj.attr('end');
    let site_regtype = $('#calendar-filter [name=site_regtype]:first').val();

    $(data_target).html("Loading...");

    $.ajax({
        url: `/?site=dashboard&subact=load_detail_calendar&type=${type}&date_from=${start}&date_to=${end}&site_field_time=${field_time}&return_for=filter&site_regtype=${site_regtype}`,
        dataType: 'json',
        success: function(res){
            $(data_target).html("");
            if(res.items)
            {
                $.each(res.items, function(i, item){
                    $(data_target).append(`<p style="margin-bottom:0px"><a href="/?site=sites&type=${item.site_regtype}&${field_time}=${start}&${field_time}_to=${end}">${item.text}: ${item.cnt}</a></p>`);
                })
            }
            else
            {
                $(data_target).html(cms_lang.no_data);
            }
        }
    })
}