<?php
namespace controller;

// Use
use core\ezy;
use models\themes;

// load models
ezy::load_model("themes");

class templates {

	static public function auto_run() 
	{
		global $CMS, $tpl;

		// Load Lang
		$CMS->class->language->load("templates");

        // Get Industry Follow act
        $tpl->industry = self::getIndustry(ezy::$act);

        // Check act is same industry
        if ( !empty($tpl->industry['act']) ) 
        {
            ezy::$act = $tpl->industry['act'];
        }

		// Check act
		switch (ezy::$act) 
		{
			// Themes by industry
            case "detail":
                self::detail();
				break;

			// Get themes by industry via ajax
			case "getthemesbycategoryviaajax":
                self::getThemesByCategoryViaAjax();
				break;

            // View Template
            case "view-Template":
            case "xem-giao-dien":
                self::viewTemplate();
                break;

            case "collections":
            case "bo-suu-tap":
            default:
                self::main();
				break;
        }
	}

	/*
	* Default page
	*/
    static public function main() 
    {
		global $CMS, $tpl;

		// Set Seo Page
        self::setSeo();

        // Check collections
        if ( ezy::$act == 'bo-suu-tap' OR ezy::$act == 'collections' ) 
        {
            // get collections
            $collections = ezy::$subact ? ezy::$subact : ( !empty(ezy::$input['collections']) ? ezy::$input['collections'] : ( !empty($CMS->input['collections']) ? $CMS->input['collections'] : '' ) );
            $collections = str_replace(".html" ,"",$collections); // trim '.html'

            // Set flag active
            $tpl->collectionsActive = $collections;

            // First: Check and set sort 
            // 1: Latest, 2: Used more 
            $sortList = array(
                "moi-nhat" => '1', 
                "su-dung-nhieu-nhat" => '2'
            );
            $tpl->themesSort = $sortList[$collections];

            // Second: Check and set id list if not sort
            if ( ! $tpl->themesSort ) 
            {
                $tpl->themesCategoryCodeList = themes::getCategoryCodeList($collections);
                $tpl->themesCategoryIdList = themes::getCategoryIdList($tpl->themesCategoryCodeList);

                $tpl->themesSort = 1; // default short latest for collections
            }
        }

		// Layouts
		echo ezy::html();
    }

    /*
    * View Template page
    */
    static public function viewTemplate()
    {
        global $CMS, $tpl;

        // get theme info
        $template_id = ezy::$subact ? ezy::$subact : ( !empty(ezy::$input['template']) ? ezy::$input['template'] : ( !empty($CMS->input['template']) ? $CMS->input['template'] : '' ) );
        $tpl->template = themes::getThemesInfo($template_id);

        // Layouts
        ezy::html('view_template');
        echo $tpl->yield;
    }
    
    /*
	* getThemesByCategoryViaAjax page
	*/
    static public function getThemesByCategoryViaAjax()
    {
        global $CMS;

        // Get Id List
        $id_list = $CMS->input['id_list'];

        // Limit
        $limit = !empty( ezy::$input['limit'] ) ? ezy::$input['limit'] : ( !empty( $CMS->input['limit'] ) ? $CMS->input['limit'] : 12 );
        $limit = intval($limit);

        // Sort
        $sort = !empty( ezy::$input['sort'] ) ? ezy::$input['sort'] : ( !empty( $CMS->input['sort'] ) ? $CMS->input['sort'] : 0 );
        $sort = intval($sort);

        // Page
        ezy::$input['page'] = !empty( ezy::$input['page'] ) ? ezy::$input['page'] : ( !empty( $CMS->input['page'] ) ? $CMS->input['page'] : 1 );
        ezy::$input['page'] = intval(ezy::$input['page']);

        // Load theme
        $themes = themes::getThemesByCategory($id_list, $limit, true, $sort);

        // Total page
        $total_page = 1;
        foreach( ezy::$page['data'] as $index => $value ) 
        {
        	$total_page = $index;
        }

        // Next page
        $next_page = (ezy::$input['page'] < $total_page) ? ezy::$input['page'] + 1 : 0;

        // Output json
        $output = array(
        	'themes' => $themes, 
        	'next_page' => $next_page, 
        );

    	print json_encode($output, JSON_UNESCAPED_UNICODE);
    	exit;
    }

    /*
	* Detail page : Templates By Industry 
	*/
    static public function detail()
    {
        global $CMS, $tpl;

        // Set Seo Page
        self::setSeo( $tpl->industry['seo'] ? $tpl->industry['seo'] : $tpl->industry['views'] );

        // Load themes
        $tpl->themesCategoryCodeList = themes::getCategoryCodeList($tpl->industry['themes']); // Get Code List
        $tpl->themesCategoryIdList = themes::getCategoryIdList($tpl->themesCategoryCodeList); // Get Id List
        $tpl->themesIsEmptyData = themes::getThemesByCategory($tpl->themesCategoryIdList, 1) ? 0 : 1; // Check data exits
        $tpl->themesLimit = 12; // Limit

        // Name
        $tpl->themesCategoryName = $tpl->industry['name'];

        // Layouts
        echo ezy::html($tpl->industry['views']);
    }

    /*
	* Get industry
    * Return array
	*/
	static public function getIndustry ( $stringKey = '' ) 
    {
        global $CMS;

        // Clean
        $stringKey = str_replace(".html" ,"",$stringKey); // trim '.html'

        // get data industry
        $industryList = self::getIndustryList();

        // Output
        $output = empty($industryList[$stringKey]) ? [] : $industryList[$stringKey];

        return $output;
    }

    /*
    * Custom set data industry list format: parrent => array (act, views, themes, seo)
    * Return Array
    */
    static public function getIndustryList ()
    {
        global $CMS;

        // industry list
        $industryList = [];

        // Thời trang
        $industryList["thoi-trang"] = array(
            "act" => "detail", 
            "views" => "detail", 
            "themes" => "thoi-trang", 
            "seo" => "fashion", 
            "name" => "Thời trang", 
        );

        // Điện tử điện máy
        $industryList["dien-tu-dien-may"] = array(
            "act" => "detail", 
            "views" => "detail", 
            "themes" => "dien-tu-dien-may", 
            "seo" => "electronic_device", 
            "name" => "Điện tử điện máy", 
        );
        $industryList["dien-thoai-dien-may"] = $industryList["dien-tu-dien-may"];

        // Bất động sản
        $industryList["bat-dong-san"] = array(
            "act" => "detail", 
            "views" => "detail", 
            "themes" => "bat-dong-san", 
            "seo" => "realtor", 
            "name" => "Bất động sản", 
        );

        // Làm đẹp
        $industryList["lam-dep"] = array(
            "act" => "detail", 
            "views" => "detail", 
            "themes" => "lam-dep", 
            "seo" => "spa", 
            "name" => "Làm đẹp", 
        );

        // Du lịch
        $industryList["du-lich"] = array(
            "act" => "detail", 
            "views" => "detail", 
            "themes" => "du-lich", 
            "seo" => "travel", 
            "name" => "Du lịch", 
        );

        // Nhà hàng
        $industryList["nha-hang"] = array(
            "act" => "detail", 
            "views" => "detail", 
            "themes" => "nha-hang", 
            "seo" => "restaurant", 
            "name" => "Nhà hàng", 
        );
        $industryList["nha-hang-thuc-pham"] = $industryList["nha-hang"]; 

		return $industryList;
	}

	/*
	* Default page
	*/
    static public function setSeo($page = 'main') 
    {
		global $CMS, $tpl;

		// Config data
		$tpl->set_seo = array();

		// Main, deail
		$tpl->set_seo["main"] = array(
			"titles"		=>	"Kho website bán hàng - doanh nghiệp - bất động sản giá rẻ", 
			"descriptions"	=>	"Web4s - Kho website bán hàng, website doanh nghiệp, website bất động sản giá rẻ, đạt chuẩn SEO, tích hợp quản lý sản phẩm, thanh toán online", 
			"keywords"		=>	"thiết kế website, website bán hàng, website doanh nghiệp, website bất động sản, website giá rẻ, seo website, website trọn gói", 
			"alt"			=>	"Kho website bán hàng - doanh nghiệp - bất động sản giá rẻ", 
		);
        $tpl->set_seo["detail"] = $tpl->set_seo["main"];

        // Thời trang
        $tpl->set_seo["fashion"] = array(
            "titles"        =>  "Thiết kế website Thời trang - Web4s", 
            "descriptions"  =>  "Web4s - Dịch vụ Thiết kế website thời trang giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
            "keywords"      =>  "Thiết kế website thời trang", 
            "alt"           =>  "Thiết kế website thời trang", 
        );      

		// Điện tử điện máy
		$tpl->set_seo["electronic_device"] = array(
			"titles"		=>	"Thiết kế website Điện tử điện máy - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website điện tử điện máy giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán điện tử điện máy", 
			"alt"			=>	"Thiết kế website bán điện tử điện máy", 
		);

		// Bất động sản
		$tpl->set_seo["realtor"] = array(
			"titles"		=>	"Thiết kế website Bất động sản - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bất động sản giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bất động sản", 
			"alt"			=>	"Thiết kế website bất động sản", 
		);

		// Làm đẹp
		$tpl->set_seo["spa"] = array(
			"titles"		=>	"Thiết kế website Làm đẹp - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website làm đẹp giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website làm đẹp", 
			"alt"			=>	"Thiết kế website làm đẹp", 
		);

		// Du lịch
		$tpl->set_seo["travel"] = array(
			"titles"		=>	"Thiết kế website Du lịch - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website du lịch giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website du lịch", 
			"alt"			=>	"Thiết kế website du lịch", 
		);

		// Nhà hàng
		$tpl->set_seo["restaurant"] = array(
			"titles"		=>	"Thiết kế website Nhà hàng - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website nhà hàng giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán nhà hàng", 
			"alt"			=>	"Thiết kế website bán nhà hàng", 
		);

		// SEO
		$tpl->alt = $tpl->set_seo[$page]["alt"];
        $CMS->lang['website_title'] = $tpl->set_seo[$page]["titles"];
        $CMS->lang['seo_description'] = $tpl->set_seo[$page]["descriptions"];
        $CMS->lang['seo_keyword'] = $tpl->set_seo[$page]["keywords"];
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
    }
}