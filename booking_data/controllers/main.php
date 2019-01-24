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

        $return = [];

        $keys = [
            'upload_url' => 'uploadURL',
            'logo_website' => 'logoWebsite',
            'app_refresh_data_period' => 'appRefreshDataPeriod',
            'step_time_booking' => 'bookingStep',
            'app_theme' => 'theme',
            'booking_page' => 'bookingEnabled',
            'app_enabled_multi_booking' => 'multiBookingEnabed',
            'checkin_enabled' => 'checkinEnabled'
        ];

        foreach($keys as $old => $new) {
            $return[$new] = input::vars($old, null);
        }

        $return['appRefreshDataPeriod'] *= 1;
        $return['bookingStep'] *= 1;
        $return['bookingEnabled'] *= 1;
        $return['checkinEnabled'] *= 1;
        $return['multiBookingEnabed'] *= 1;
        $return['logoWebsite'] = "{$return['uploadURL']}/attach/{$return['logoWebsite']}";
        $return['noAvatar'] = "{$CMS->vars['root_domain']}/acp/images/avatar-2-64.png";

        input::jsonEncode($return);
    }
}