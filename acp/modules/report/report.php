<?php

use \core\ezy;

if (!defined('IN_ROOT')) exit();
ezy::load_model("report");
ezy::load_model("staff");

new report;

class report
{
    public $html;

    /**
     * report constructor.
     */
    public function __construct()
    {
        global $CMS, $DB, $member, $tpl;

        $CMS->class->language->load("report");

        $tpl->today = \lib\date::format(time());
        $tpl->yesterday = \lib\date::format(time() - (3600 * 24));
        $tpl->this_week = $CMS->class->date->getFisrtLastInCurrentWeek('timestamp');
        $tpl->last_week = $CMS->class->date->getFisrtLastInLastWeek('timestamp');
        $tpl->this_month = $CMS->class->date->getFisrtLastInCurrentMonth('timestamp');
        $tpl->last_month = $CMS->class->date->getFisrtLastInLastMonth('timestamp');
        $tpl->this_year = $CMS->class->date->getFisrtLastInThisYear('timestamp');
        $tpl->last_year = $CMS->class->date->getFisrtLastInLastYear('timestamp');

        $quarter = \lib\date::getQuarter(time());
        $tpl->this_quarter = \lib\date::getFirstLastOfQuarter($quarter['quarter'], $quarter['year'], 'timestamp');
        $tpl->last_quarter = \lib\date::getFirstLastOfQuarter($quarter['quarter']-1, $quarter['year'], 'timestamp');

        switch ($CMS->input['act']) {
            case 'sales':
                $this->sales();
                break;
            default:
                $this->sales();
                break;
        }
    }

    /**
     * Report sales
     */
    public function sales()
    {
        global $CMS, $tpl;

        switch (\lib\input::get('subact')) {
            case 'by_staffs':
                $this->salesByStaffs();
                break;
            default:
                $this->salesByStores();
        }
    }

    /**
     * Report sales by store
     */
    public function salesByStores()
    {
        global $CMS, $tpl;

        $start = $CMS->class->date->date2time(urldecode(\lib\input::get('start')));
        $end = $CMS->class->date->date2time(urldecode(\lib\input::get('end'))) + (3600*24);
        $date_type = \lib\input::get('date_type');

        //Thống kê doanh thu theo cửa hàng
        if (\lib\input::get('data_type') == 'json') {
            if (\lib\input::get('chart') == 'general') {
                \lib\input::jsonEncode(\models\report::salesGeneralReportChart($start, $end));
            } elseif (\lib\input::get('chart') == 'by_time') {
                \lib\input::jsonEncode(\models\report::SalesReportByStoreByTimeChart($start, $end, $date_type));
            } else {
                \lib\input::jsonEncode(null);
            }
        }

        // Output data
        $CMS->output .= ezy::html("sales_by_stores");
    }

    /**
     * Report sales by staff
     */
    public function salesByStaffs()
    {
        global $CMS, $tpl;

        \models\report::reportSalesByStaffChart();

        $start = $CMS->class->date->date2time(urldecode(\lib\input::get('start')));
        $end = $CMS->class->date->date2time(urldecode(\lib\input::get('end'))) + (3600*24);

        $tpl->stores = \models\store::getStores();

        if (\lib\input::get('data_type') == 'json') {
            \lib\input::jsonEncode(\models\report::reportSalesByStaffChart($start, $end));
        }

        // Output data
        $CMS->output .= ezy::html("sales_by_staffs");
    }


}