<?php
namespace controller;

// Use
use core\ezy;
use models\themes;

// load models
ezy::load_model("themes");

class pricing {
    static public function auto_run() {
		global $CMS, $tpl;

		// Load lang
		$CMS->class->language->load("pricing");

		// SEO
        $CMS->lang['website_title'] = 'Thiết kế website bán hàng - doanh nghiệp - bất động sản giá rẻ';
        $CMS->lang['seo_description'] = 'Web4s - Dịch vụ thiết kế website bán hàng, website doanh nghiệp, website bất động sản giá rẻ, đạt chuẩn SEO, tích hợp quản lý sản phẩm, thanh toán online';
        $CMS->lang['seo_keyword'] = 'thiết kế website, website bán hàng, website doanh nghiệp, website bất động sản, website giá rẻ, seo website, website trọn gói';
        $CMS->lang['seo_author'] = 'Công ty TNHH phần mềm Nhân Hòa';
        
        $CMS->vars['website_title'] = $CMS->lang['website_title'];
        $CMS->vars['seo_description'] = $CMS->lang['seo_description'];
        $CMS->vars['seo_keyword'] = $CMS->lang['seo_keyword'];
        $CMS->vars['seo_author'] = $CMS->lang['seo_author'];

        $CMS->vars['og_title'] = $CMS->lang['website_title'];
        $CMS->vars['og_description'] = $CMS->lang['seo_description'];

        $CMS->vars['dc_title'] = $CMS->lang['website_title'];
        $CMS->vars['dc_description'] = $CMS->lang['seo_description'];
        $CMS->vars['dc_subject'] = $CMS->lang['website_title'];
        // END SEO

		// Check act
		switch (ezy::$act) {
			case 'warehouse':
				self::warehouse();
                break;

            case 'website-ban-hang':
            case 'website-ban-hang.html':
            case 'ecommerce_website':
				self::ecommerceWebsite();
                break;

            case 'website-bat-dong-san':
            case 'website-bat-dong-san.html':
            case 'real_estate_website':
				self::realEstateWebsite();
                break;

            case 'website-doanh-nghiep':
            case 'website-doanh-nghiep.html':
            case 'business_website':
				self::businessWebsite();
                break;

            default:
                self::main();
				break;
        }
    }
	static function main() {
		global $CMS, $tpl;
		
		// SEO
        $CMS->lang['website_title'] = 'Báo giá thiết kế website giá rẻ - web4s';
        $CMS->lang['seo_description'] = 'Web4s - Báo giá thiết kế website bán hàng, website doanh nghiệp, website bất động sản giá rẻ, đạt chuẩn SEO, tích hợp quản lý sản phẩm, thanh toán online.';
        $CMS->lang['seo_keyword'] = 'thiết kế website, website bán hàng, website doanh nghiệp, website bất động sản, website giá rẻ, seo website, website trọn gói';
        $CMS->lang['seo_author'] = 'Công ty TNHH phần mềm Nhân Hòa';

		$CMS->vars['website_title'] = $CMS->lang['website_title'];
		$CMS->vars['seo_description'] = $CMS->lang['seo_description'];
		$CMS->vars['seo_keyword'] = $CMS->lang['seo_keyword'];
		$CMS->vars['seo_author'] = $CMS->lang['seo_author'];

		$CMS->vars['og_title'] = $CMS->lang['website_title'];
		$CMS->vars['og_description'] = $CMS->lang['seo_description'];

		$CMS->vars['dc_title'] = $CMS->lang['website_title'];
		$CMS->vars['dc_description'] = $CMS->lang['seo_description'];
		$CMS->vars['dc_subject'] = $CMS->lang['website_title'];
		// END SEO

		// Layouts
		echo ezy::html('main');
	}
    
	static function warehouse() {
		echo ezy::html('warehouse');
	}

	/*
     * Ecommerce website
     */
    static private function ecommerceWebsite()
    {
        global $CMS, $tpl;

        // SEO
        $CMS->lang['website_title'] = 'Thiết kế website bán hàng - giúp tăng doanh số bán hàng';
        $CMS->lang['seo_description'] = 'Web4s - Dịch vụ thiết kế website bán hàng, giúp tăng doanh số bán hàng, tăng tỉ lệ chốt đơn hàng thành công, hỗ trợ thanh toán trực tuyến';
        $CMS->lang['seo_keyword'] = 'thiết kế website, website bán hàng, website doanh nghiệp, website bất động sản, website giá rẻ, seo website, website trọn gói';
        $CMS->lang['seo_author'] = 'Công ty TNHH phần mềm Nhân Hòa';

		$CMS->vars['website_title'] = $CMS->lang['website_title'];
		$CMS->vars['seo_description'] = $CMS->lang['seo_description'];
		$CMS->vars['seo_keyword'] = $CMS->lang['seo_keyword'];
		$CMS->vars['seo_author'] = $CMS->lang['seo_author'];

		$CMS->vars['og_title'] = $CMS->lang['website_title'];
		$CMS->vars['og_description'] = $CMS->lang['seo_description'];

		$CMS->vars['dc_title'] = $CMS->lang['website_title'];
		$CMS->vars['dc_description'] = $CMS->lang['seo_description'];
		$CMS->vars['dc_subject'] = $CMS->lang['website_title'];
		// END SEO

		// Load theme
        $tpl->themesCategoryCodeList = themes::getCategoryCodeList('ban-hang'); // Get Code List
        $tpl->themesCategoryIdList = themes::getCategoryIdList($tpl->themesCategoryCodeList); // Get Id List
        $tpl->themesIsEmptyData = themes::getThemesByCategory($tpl->themesCategoryIdList, 1) ? 0 : 1; // Check data exits
        $tpl->themesLimit = 8; // Limit

        // Title price
        $tpl->pricingTitle = "WEBSITE BÁN HÀNG";

		// Layouts
        echo ezy::html('ecommerce_website');
    }

    /*
     * Real Estate Website website
     */
    static private function realEstateWebsite()
    {
        global $CMS, $tpl;

        // SEO
        $CMS->lang['website_title'] = 'Thiết kế website bất động sản - dành cho nhà môi giới chuyên nghiệp';
        $CMS->lang['seo_description'] = 'Web4s - Dịch vụ thiết kế website bất động sản, dành cho những nhà môi giới bất động sản chuyên nghiệp';
        $CMS->lang['seo_keyword'] = 'thiết kế website, website bán hàng, website doanh nghiệp, website bất động sản, website giá rẻ, seo website, website trọn gói';
        $CMS->lang['seo_author'] = 'Công ty TNHH phần mềm Nhân Hòa';
        
		$CMS->vars['website_title'] = $CMS->lang['website_title'];
		$CMS->vars['seo_description'] = $CMS->lang['seo_description'];
		$CMS->vars['seo_keyword'] = $CMS->lang['seo_keyword'];
		$CMS->vars['seo_author'] = $CMS->lang['seo_author'];

		$CMS->vars['og_title'] = $CMS->lang['website_title'];
		$CMS->vars['og_description'] = $CMS->lang['seo_description'];

		$CMS->vars['dc_title'] = $CMS->lang['website_title'];
		$CMS->vars['dc_description'] = $CMS->lang['seo_description'];
		$CMS->vars['dc_subject'] = $CMS->lang['website_title'];
		// END SEO
		
		// Load theme
        $tpl->themesCategoryCodeList = themes::getCategoryCodeList('bat-dong-san'); // Get Code List
        $tpl->themesCategoryIdList = themes::getCategoryIdList($tpl->themesCategoryCodeList); // Get Id List
        $tpl->themesIsEmptyData = themes::getThemesByCategory($tpl->themesCategoryIdList, 1) ? 0 : 1; // Check data exits
        $tpl->themesLimit = 8; // Limit

        // Title price
        $tpl->pricingTitle = "WEBSITE BẤT ĐỘNG SẢN";

		// Layouts
        echo ezy::html('real_estate_website');
    }

    /*
     * Business Website website
     */
    static private function businessWebsite()
    {
        global $CMS, $tpl;

        // SEO
        $CMS->lang['website_title'] = 'Thiết kế website doanh nghiệp - khẳng định thương hiệu trên internet';
        $CMS->lang['seo_description'] = 'Web4s - Dịch vụ thiết kế website doanh nghiệp, hỗ trợ xây dựng thương hiệu doanh nghiệp trên internet';
        $CMS->lang['seo_keyword'] = 'thiết kế website, website bán hàng, website doanh nghiệp, website bất động sản, website giá rẻ, seo website, website trọn gói';
        $CMS->lang['seo_author'] = 'Công ty TNHH phần mềm Nhân Hòa';
        
		$CMS->vars['website_title'] = $CMS->lang['website_title'];
		$CMS->vars['seo_description'] = $CMS->lang['seo_description'];
		$CMS->vars['seo_keyword'] = $CMS->lang['seo_keyword'];
		$CMS->vars['seo_author'] = $CMS->lang['seo_author'];

		$CMS->vars['og_title'] = $CMS->lang['website_title'];
		$CMS->vars['og_description'] = $CMS->lang['seo_description'];

		$CMS->vars['dc_title'] = $CMS->lang['website_title'];
		$CMS->vars['dc_description'] = $CMS->lang['seo_description'];
		$CMS->vars['dc_subject'] = $CMS->lang['website_title'];
		// END SEO

		// Load theme
        $tpl->themesCategoryCodeList = themes::getCategoryCodeList('doanh-nghiep'); // Get Code List
        $tpl->themesCategoryIdList = themes::getCategoryIdList($tpl->themesCategoryCodeList); // Get Id List
        $tpl->themesIsEmptyData = themes::getThemesByCategory($tpl->themesCategoryIdList, 1) ? 0 : 1; // Check data exits
        $tpl->themesLimit = 8; // Limit

        // Title price
        $tpl->pricingTitle = "WEBSITE DOANH NGHIỆP";

		// Layouts
        echo ezy::html('business_website');
    }
}