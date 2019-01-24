<?php

namespace controller;

use core\ezy;
use lib\cookie;
use lib\input;

class main
{
    static function get_global_variables()
    {
        global $CMS;

        $cms_lang = $CMS->lang;

        $CMS->class->language->load("transactions", "admin_");

        $return = [];

        $keys = [
            'upload_url' => 'uploadURL',
            'logo_website' => 'logoWebsite',
            'print_company_name' => 'printingCompanyName',
            'print_website' => 'printingWebsite',
            'print_company_address' => 'printingCompanyAddress',
            'print_company_phone' => 'printingCompanyPhone',
            'app_printing_bill' => 'appPrintingBill',
            'app_refresh_data_period' => 'appRefreshDataPeriod',
            'app_theme' => 'theme',
            'pos_enabled' => 'posEnabled',
            'checkin_enabled' => 'checkinEnabled',
            'pos_order_type_default' => 'productTypeDefault'
        ];

        $trxStatus = [
            ['id' => 0, 'name' => $CMS->lang['trx_status_00']],
            ['id' => 1, 'name' => $CMS->lang['trx_status_01']],
            ['id' => 2, 'name' => $CMS->lang['trx_status_02']],
            ['id' => 3, 'name' => $CMS->lang['trx_status_03']],
            ['id' => 4, 'name' => $CMS->lang['trx_status_04']],
            ['id' => 5, 'name' => $CMS->lang['trx_status_05']],
        ];

        foreach($keys as $old => $new) {
            $return[$new] = input::vars($old);
        }

        $return['trxStatuses'] = $trxStatus;
        $return['appRefreshDataPeriod'] *= 1;
        $return['posEnabled'] *= 1;
        $return['checkinEnabled'] *= 1;
        $return['productTypeDefault'] *= 1;
        $return['appPrintingBill'] = input::jsonDecode($return['appPrintingBill']);

        $return['logoWebsite'] = "{$return['uploadURL']}/attach/{$return['logoWebsite']}";

        $CMS->lang = $cms_lang;

        input::jsonEncode($return);
    }
}