<?php
namespace controller;

// use
use core\ezy;
use models\themes;

// load models
ezy::load_model("themes");

class templates_travel
{
//*********************
//**Start Class
//*********************
	/*
	* Auto run
	*/
	static public function auto_run() 
	{
		global $CMS, $tpl;

		// Load Lang
		$CMS->class->language->load("templates_travel");

		// Get Regions follow act
		$tpl->regions = self::getRegions(ezy::$act);

        // Check act is same regions
        if ( !empty($tpl->regions['act']) ) 
        {
            ezy::$act = $tpl->regions['act'];
        }
		
		// Check act
		switch (ezy::$act) 
		{
            case "detail":
                self::detail();
                break;

            default:
                self::main();
				break;
        }
	}

    /*
	* Deatails page
	*/
    static public function detail()
    {
        global $CMS, $tpl;

        // Set Seo Page
        self::setSeo($tpl->regions['views']);

        echo ezy::html($tpl->regions['views']);
    }

    /*
    * Default page
    */
    static public function main() 
    {
		global $CMS, $tpl;

		// Set Seo Page
		self::setSeo();
		
        // Load theme
        $tpl->themesCategoryCodeList = themes::getCategoryCodeList('du-lich'); // Get Code List
        $tpl->themesCategoryIdList = themes::getCategoryIdList($tpl->themesCategoryCodeList); // Get Id List
        $tpl->themesIsEmptyData = themes::getThemesByCategory($tpl->themesCategoryIdList, 1) ? 0 : 1; // Check data exits
        $tpl->themesLimit = 4; // Limit

        // Layouts
		echo ezy::html();
    }

    /*
	* Default set SEO
	*/
    static public function setSeo($page = 'main') 
    {
		global $CMS, $tpl;

		// Config data
		$tpl->set_seo = array();

		// Main
		$tpl->set_seo["main"] = array(
			"titles"		=>	"Kho website du lịch sản giá rẻ", 
			"descriptions"	=>	"Web4s - Kho website du lịch giá rẻ, đạt chuẩn SEO, tích hợp quản lý sản phẩm, thanh toán online", 
			"keywords"		=>	"thiết kế website du lịch giá rẻ, seo website, website trọn gói", 
			"alt"			=>	"Giải pháp cho website du lịch chuyên nghiệp", 
		);

        // Nha Trang
        $tpl->set_seo["nha_trang"] = array(
            "titles"		=>	"Thiết kế website du lịch Nha Trang - Web4s",
            "descriptions"	=>	"Web4s - Dịch vụ Thiết kế website du lịch Nha Trang giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước",
            "keywords"		=>	"Thiết kế website du lịch Nha Trang",
            "alt"			=>	"Thiết kế website du lịch Nha Trang",
        );

        // Hà Nội
        $tpl->set_seo["hoi_an"] = array(
            "titles"		=>	"Thiết kế website du lịch Hà Nội - Web4s",
            "descriptions"	=>	"Web4s - Dịch vụ Thiết kế website du lịch Hà Nội giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước",
            "keywords"		=>	"Thiết kế website du lịch Hà Nội",
            "alt"			=>	"Thiết kế website du lịch Hà Nội",
        );

        // Hạ Long
        $tpl->set_seo["ha_long"] = array(
            "titles"		=>	"Thiết kế website du lịch Hạ Long - Web4s",
            "descriptions"	=>	"Web4s - Dịch vụ Thiết kế website du lịch Hạ Long giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước",
            "keywords"		=>	"Thiết kế website du lịch Hạ Long",
            "alt"			=>	"Thiết kế website du lịch Hạ Long",
        );

        // Sapa
        $tpl->set_seo["sa_pa"] = array(
            "titles"		=>	"Thiết kế website du lịch Sapa - Web4s",
            "descriptions"	=>	"Web4s - Dịch vụ Thiết kế website du lịch Sapa giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước",
            "keywords"		=>	"Thiết kế website du lịch Sapa",
            "alt"			=>	"Thiết kế website du lịch Sapa",
        );

        // Ninh Bình
        $tpl->set_seo["ninh_binh"] = array(
            "titles"		=>	"Thiết kế website du lịch Ninh Bình - Web4s",
            "descriptions"	=>	"Web4s - Dịch vụ Thiết kế website du lịch Ninh Bình giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước",
            "keywords"		=>	"Thiết kế website du lịch Ninh Bình",
            "alt"			=>	"Thiết kế website du lịch Ninh Bình",
        );

        // Đà Nẵng
        $tpl->set_seo["da_nang"] = array(
            "titles"		=>	"Thiết kế website du lịch Đà Nẵng - Web4s",
            "descriptions"	=>	"Web4s - Dịch vụ Thiết kế website du lịch Đà Nẵng giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước",
            "keywords"		=>	"Thiết kế website du lịch Đà Nẵng",
            "alt"			=>	"Thiết kế website du lịch Đà Nẵng",
        );

        // Hội An
		$tpl->set_seo["hoi_an"] = array(
			"titles"		=>	"Thiết kế website du lịch Hội An - Web4s",
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website du lịch Hội An giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước",
			"keywords"		=>	"Thiết kế website du lịch Hội An",
			"alt"			=>	"Thiết kế website du lịch Hội An",
		);

        // Huế
        $tpl->set_seo["hue"] = array(
            "titles"		=>	"Thiết kế website du lịch Huế - Web4s",
            "descriptions"	=>	"Web4s - Dịch vụ Thiết kế website du lịch Huế giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước",
            "keywords"		=>	"Thiết kế website du lịch Huế",
            "alt"			=>	"Thiết kế website du lịch Huế",
        );

        // Phong Nha - Kẽ Bàng
        $tpl->set_seo["phong_nha_ke_bang"] = array(
            "titles"		=>	"Thiết kế website du lịch Phong Nha - Kẽ Bàng - Web4s",
            "descriptions"	=>	"Web4s - Dịch vụ Thiết kế website du lịch Phong Nha - Kẽ Bàng giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước",
            "keywords"		=>	"Thiết kế website du lịch Phong Nha - Kẽ Bàng",
            "alt"			=>	"Thiết kế website du lịch Phong Nha - Kẽ Bàng",
        );

        // Phú Quốc
        $tpl->set_seo["phu_quoc"] = array(
            "titles"		=>	"Thiết kế website du lịch Phú Quốc - Web4s",
            "descriptions"	=>	"Web4s - Dịch vụ Thiết kế website du lịch Phú Quốc giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước",
            "keywords"		=>	"Thiết kế website du lịch Phú Quốc",
            "alt"			=>	"Thiết kế website du lịch Phú Quốc",
        );

        // Đà Lạt
        $tpl->set_seo["da_lat"] = array(
            "titles"		=>	"Thiết kế website du lịch Đà Lạt - Web4s",
            "descriptions"	=>	"Web4s - Dịch vụ Thiết kế website du lịch Đà Lạt giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước",
            "keywords"		=>	"Thiết kế website du lịch Đà Lạt",
            "alt"			=>	"Thiết kế website du lịch Đà Lạt",
        );

        // Phan Thiết
        $tpl->set_seo["phan_thiet"] = array(
            "titles"		=>	"Thiết kế website du lịch Phan Thiết - Web4s",
            "descriptions"	=>	"Web4s - Dịch vụ Thiết kế website du lịch Phan Thiết giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước",
            "keywords"		=>	"Thiết kế website du lịch Phan Thiết",
            "alt"			=>	"Thiết kế website du lịch Phan Thiết",
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

    /*
	* Get regions
    * Return array
	*/
	static public function getRegions ( $stringKey = '' ) 
	{
		global $CMS;

        // Clean
        $stringKey = str_replace(".html" ,"",$stringKey); // trim '.html'

        // get data regions
        $regionsList = self::getRegionsList();

        // Output
        $output = empty($regionsList[$stringKey]) ? [] : $regionsList[$stringKey];

        return $output;
	}

	/*
	* Custom set data regions list format: parrent => array (act, views, themes)
    * Return Array
	*/
	static public function getRegionsList ()
	{
		global $CMS;

		// regions list
        $industryList = [];
        
        // Nha Trang
        $regionsList["nha-trang"] = array(
            "act" => "detail", 
            "views" => "nha_trang", 
            "themes" => "nha-trang", 
        );

        // Hà Nội
        $regionsList["ha-noi"] = array(
            "act" => "detail", 
            "views" => "ha_noi", 
            "themes" => "ha-noi", 
        );

        // Hạ Long
        $regionsList["ha-long"] = array(
            "act" => "detail", 
            "views" => "ha_long", 
            "themes" => "ha-long", 
        );

        // Sapa
        $regionsList["sa-pa"] = array(
            "act" => "detail", 
            "views" => "sa_pa", 
            "themes" => "sa-pa", 
        );

        // Ninh Bình
        $regionsList["ninh-binh"] = array(
            "act" => "detail", 
            "views" => "ninh_binh", 
            "themes" => "ninh-binh", 
        );

        // Đà Nẵng
        $regionsList["da-nang"] = array(
            "act" => "detail", 
            "views" => "da_nang", 
            "themes" => "da-nang", 
        );

        // Hội An
        $regionsList["hoi-an"] = array(
            "act" => "detail", 
            "views" => "hoi_an", 
            "themes" => "hoi-an", 
        );

        // Hue
        $regionsList["hue"] = array(
            "act" => "detail", 
            "views" => "hue", 
            "themes" => "hue", 
        );

        // Phong Nha - Kẽ Bàng
        $regionsList["phong-nha-ke-bang"] = array(
            "act" => "detail", 
            "views" => "phong_nha_ke_bang", 
            "themes" => "phong-nha-ke-bang", 
        );

        // Phú Quốc
        $regionsList["phu-quoc"] = array(
            "act" => "detail", 
            "views" => "phu_quoc", 
            "themes" => "phu-quoc", 
        );

        // Đà Lạt
        $regionsList["da-lat"] = array(
            "act" => "detail", 
            "views" => "da_lat", 
            "themes" => "da-lat", 
        );

        // Phan Thiết
        $regionsList["phan-thiet"] = array(
            "act" => "detail", 
            "views" => "phan_thiet", 
            "themes" => "phan-thiet", 
        );

        return $regionsList;
	}
//*********************
//**End Class
//*********************
}