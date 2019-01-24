<?php

namespace controller;

use core\ezy;
use models\service;

// Models
ezy::load_model("service");

class coupons
{
    static public function auto_run()
    {
    	global $tpl;

    	$tpl->dataListCoupons = \models\coupons::getListCoupons();

    	// Get Services & Service Categories.
    	$tpl->dataListServices = service::getListServiceCategory();

    	//Fix warning ezy::$page['data']
        \core\ezy::$page['data'] = isset(\core\ezy::$page['data']) && is_array(\core\ezy::$page['data']) ? \core\ezy::$page['data'] : [];

        echo ezy::html();

    }
}