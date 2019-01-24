<?php

namespace controller;
use \core\ezy;
use lib\date;
use \lib\input;
use models\download;

new dashboard;

class dashboard {
	
	public function __construct()
	{
		global $CMS, $DB, $tpl;

		// Title
		$CMS->core->page_title = "-> {$CMS->lang['header_home']}";
		// Assets?
		switch( \lib\input::get('subact') )
		{
            case "search_asset":
                \models\dashboard::search_asset();
                break;
            case "update_asset_to_list":
                \models\dashboard::update_asset_to_list();
                break;
            case "save_to_list_asset":
                \models\dashboard::save_to_list_asset();
                break;
            case "file_management":
                \models\dashboard::fileManagement();exit;
                break;
            case "savefile":
                \models\dashboard::saveFile();exit;
                break;
            case "delfile":
                \models\dashboard::delFile();exit;
                break;
            case 'load_city_ajax':
                self::loadCityAjax();
                break;
            case 'load_district_ajax':
                self::loadDistrictAjax();
                break;
            case 'download':
                self::download();
                break;
            case 'crawling_seo':
                self::crawlingSEO();
                break;
            case 'create_token':
                self::createToken();
                break;
		}

        // Main switch
        switch ( $CMS->input['act'] )
        {
            case 'change_color_theme':
                self::change_color_theme();
                break;
            default:
                self::default_page();
                break;
        }

	}

    /**
     * Default page
     */

	static private function default_page()
    {
        global $CMS, $tpl;

        $dateFormat = $CMS->vars['dateformat_php'][$CMS->vars['date_format']];

        // URL order chat
        $time_from = strtotime('monday this week'); //strtotime('monday this week')+ $CMS->vars['timezone']
        $time_to = strtotime('sunday this week'); //strtotime('sunday this week')+ $CMS->vars['timezone']
        $tpl->url_order_chart = "{$CMS->vars['root_domain']}/?site=order&time_from={$time_from}&time_to={$time_to}";

        // Group of url :D
//        $time_from = strtotime('today');
//        $time_to = $time_from + 24*3600;
        list($time_from,$time_to) = \models\dashboard::getTimeByType(1);
        $date_from =  date::format($time_from); //date::format($time_from-$CMS->vars['timezone']*3600);
        $date_to =  date::format($time_to); //date::format($time_to-$CMS->vars['timezone']*3600)

        $tpl->url_order = "{$CMS->vars['root_domain']}/?site=order&ord_time_from={$date_from}&ord_time_to={$date_to}";
        $tpl->url_revenue = "{$CMS->vars['root_domain']}/?site=transactions&stats=paid&type=1&date_from={$date_from}&date_to={$date_to}";
        $tpl->url_costs = "{$CMS->vars['root_domain']}/?site=transactions&stats=paid&type=2&date_from={$date_from}&date_to={$date_to}";
        $tpl->url_customer = "{$CMS->vars['root_domain']}/?site=customer&cus_time_from={$date_from}&cus_time_to={$date_to}";

        $time_from_month = strtotime('first day of this month'); //strtotime('first day of this month')+$CMS->vars['timezone'];
        $time_to_month = strtotime('last day of this month'); //strtotime('last day of this month') + $CMS->vars['timezone']
        $date_from_month = urlencode(date($dateFormat, $time_from_month));
        $date_to_month = urlencode(date($dateFormat, $time_to_month));
        $tpl->url_revenue_month = "{$CMS->vars['root_domain']}/?site=transactions&stats=paid&type=1&date_from={$date_from_month}&date_to={$date_to_month}";
        $tpl->url_costs_month = "{$CMS->vars['root_domain']}/?site=transactions&stats=paid&type=2&date_from={$date_from_month}&date_to={$date_to_month}";

        \models\dashboard::init();
        
        $CMS->output .= ezy::html();
    }

    static private function loadCityAjax()
    {
        global $CMS;

        $id = intval($CMS->input['id']) ? intval($CMS->input['id']) : -1;
        $result = $CMS->country->city($id);
        header('Content-Type: application/json; charset=utf-8');
        echo @json_encode($result, JSON_UNESCAPED_UNICODE); exit;
    }

    static private function loadDistrictAjax()
    {
        global $CMS;

        $id = intval($CMS->input['id']) ? intval($CMS->input['id']) : -1;
        $result = $CMS->country->district($id);
        header('Content-Type: application/json; charset=utf-8');
        echo @json_encode($result, JSON_UNESCAPED_UNICODE); exit;
    }

    static private function download()
    {
        global $CMS;
        switch ($CMS->input['mod'])
        {
            case 'order':
                $CMS->order->download();
            break;

            case 'invoice':
                $CMS->transactions->downloadInvoice();
                break;
            case 'export_sample':
                ezy::load_model("download");
                download::downloadSample($CMS->input['module'],'xls',$CMS->vars['default_language']);
                break;
            default:
                $CMS->output .= ezy::html();
        }
    }

    static private function createToken()
    {
        $token = \lib\security::create_token();
        print $token; exit;
    }

    static function crawlingSEO()
    {
        global $CMS, $tpl;

        \core\ezy::load_model("crawling");

        $data = \models\crawling::getSEO(urldecode($_POST['src']), $CMS->input['type']);

        \lib\input::jsonEncode($data);
    }

    static function change_color_theme()
    {
        global $CMS, $tpl;

        // $_SESSION['color_theme'] = $CMS->input['colr'];
        \models\dashboard::change_color_theme($CMS->input['colr']);
        header("location: /acp");exit;
    }
}
	
?>