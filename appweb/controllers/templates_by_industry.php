<?php
namespace controller;

// Use
use core\ezy;
use models\themes;

// load models
ezy::load_model("themes");

class templates_by_industry 
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
		$CMS->class->language->load("templates_by_industry");

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
        self::setSeo($tpl->industry['views']);

        // Load themes
        $tpl->themesCategoryCodeList = themes::getCategoryCodeList($tpl->industry['themes']); // Get Code List
        $tpl->themesCategoryIdList = themes::getCategoryIdList($tpl->themesCategoryCodeList); // Get Id List
        $tpl->themesIsEmptyData = themes::getThemesByCategory($tpl->themesCategoryIdList, 1) ? 0 : 1; // Check data exits
        $tpl->themesLimit = 8; // Limit

        // Layouts
        echo ezy::html($tpl->industry['views']);
    }

	/*
	* Default page
	*/
    static public function main() 
    {
		global $CMS, $tpl;

		// Set Seo Page
		self::setSeo();

		// Layouts
		echo ezy::html();
    }

    /*
	* Set seo
	*/
    static public function setSeo($page = 'main') 
    {
		global $CMS, $tpl;

		// Config data
		$tpl->set_seo = array();

		// Main
		$tpl->set_seo["main"] = array(
			"titles"		=>	"Kho website bán hàng - doanh nghiệp - bất động sản giá rẻ", 
			"descriptions"	=>	"Web4s - Kho website bán hàng, website doanh nghiệp, website bất động sản giá rẻ, đạt chuẩn SEO, tích hợp quản lý sản phẩm, thanh toán online", 
			"keywords"		=>	"thiết kế website, website bán hàng, website doanh nghiệp, website bất động sản, website giá rẻ, seo website, website trọn gói", 
			"alt"			=>	"Giải pháp cho Website bán hàng chuyên nghiệp", 
		);

		// Phụ kiện điện thoại
		$tpl->set_seo["phone_accessories"] = array(
			"titles"		=>	"Thiết kế website bán Phụ kiện điện thoại - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Phụ kiện điện thoại giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Phụ kiện điện thoại", 
			"alt"			=>	"Thiết kế website bán Phụ kiện điện thoại", 
		);

		// Đồng hồ
		$tpl->set_seo["clock"] = array(
			"titles"		=>	"Thiết kế website bán Đồng hồ - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Đồng hồ giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Đồng hồ", 
			"alt"			=>	"Thiết kế website bán Đồng hồ", 
		);

		// Túi xách
		$tpl->set_seo["carrier_bag"] = array(
			"titles"		=>	"Thiết kế website bán Túi xách - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Túi xách giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Túi xách", 
			"alt"			=>	"Thiết kế website bán Túi xách", 
		);

		// Giầy dép
		$tpl->set_seo["footwear"] = array( 
			"titles"		=>	"Thiết kế website bán Giầy dép - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Giầy dép giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Giầy dép", 
			"alt"			=>	"Thiết kế website bán Giầy dép", 
		);

		// Đồ trang sức
		$tpl->set_seo["jewelry"] = array(
			"titles"		=>	"Thiết kế website bán Đồ trang sức - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Đồ trang sức giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Đồ trang sức", 
			"alt"			=>	"Thiết kế website bán Đồ trang sức", 
		);

		// Đồ chơi
		$tpl->set_seo["toy"] = array(
			"titles"		=>	"Thiết kế website bán Đồ chơi - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Đồ chơi giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Đồ chơi", 
			"alt"			=>	"Thiết kế website bán Đồ chơi", 
		);

		// Đồ ăn vặt
		$tpl->set_seo["snacks"] = array(
			"titles"		=>	"Thiết kế website bán Đồ ăn vặt - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Đồ ăn vặt giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Đồ ăn vặt", 
			"alt"			=>	"Thiết kế website bán Đồ ăn vặt", 
		);

		// Hoa
		$tpl->set_seo["flower"] = array(
			"titles"		=>	"Thiết kế website bán Hoa - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Hoa giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Hoa", 
			"alt"			=>	"Thiết kế website bán Hoa", 
		);

		// Váy cưới
		$tpl->set_seo["wedding_dress"] = array(
			"titles"		=>	"Thiết kế website bán Váy cưới - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Váy cưới giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Váy cưới", 
			"alt"			=>	"Thiết kế website bán Váy cưới", 
		);

		// Đồ bà bầu
		$tpl->set_seo["maternity_dress"] = array(
			"titles"		=>	"Thiết kế website bán Đồ bà bầu - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Đồ bà bầu giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Đồ bà bầu", 
			"alt"			=>	"Thiết kế website bán Đồ bà bầu", 
		);

		// Đồ lưu niệm
		$tpl->set_seo["souvenir"] = array(
			"titles"		=>	"Thiết kế website bán Đồ lưu niệm - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Đồ lưu niệm giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Đồ lưu niệm", 
			"alt"			=>	"Thiết kế website bán Đồ lưu niệm", 
		);

		// Thú cưng
		$tpl->set_seo["pets"] = array(
			"titles"		=>	"Thiết kế website bán Thú cưng - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Thú cưng giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Thú cưng", 
			"alt"			=>	"Thiết kế website bán Thú cưng", 
		);

		// Tranh
		$tpl->set_seo["picture"] = array(
			"titles"		=>	"Thiết kế website bán Tranh - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Tranh giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Tranh", 
			"alt"			=>	"Thiết kế website bán Tranh", 
		);

		// Vé máy bay
		$tpl->set_seo["airline_tickets"] = array(
			"titles"		=>	"Thiết kế website bán Vé máy bay - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Vé máy bay giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Vé máy bay", 
			"alt"			=>	"Thiết kế website bán Vé máy bay", 
		);

		// Tuyển dụng
		$tpl->set_seo["employment"] = array(
			"titles"		=>	"Thiết kế website Tuyển dụng - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website Tuyển dụng giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website Tuyển dụng", 
			"alt"			=>	"Thiết kế website Tuyển dụng", 
		);

		// Bất động sản
		$tpl->set_seo["realtor"] = array(
			"titles"		=>	"Thiết kế website Bất động sản - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website Bất động sản giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website Bất động sản", 
			"alt"			=>	"Thiết kế website Bất động sản", 
		);

		// Khách sạn
		$tpl->set_seo["hotel"] = array(
			"titles"		=>	"Thiết kế website Khách sạn - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website Khách sạn giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website Khách sạn", 
			"alt"			=>	"", 
		);

		// Website giá rẻ
		$tpl->set_seo["website_cheat"] = array(
			"titles"		=>	"Thiết kế website bán Website giá rẻ - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Website giá rẻ giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Website giá rẻ", 
			"alt"			=>	"Thiết kế website bán Website giá rẻ", 
		);

		// website Doanh nghiệp
		$tpl->set_seo["website_bussiness"] = array(
			"titles"		=>	"Thiết kế website Doanh nghiệp - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website Doanh nghiệp giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website Doanh nghiệp", 
			"alt"			=>	"Thiết kế website Doanh nghiệp", 
		);

		// Nhà hàng
		$tpl->set_seo["restaurant"] = array(
			"titles"		=>	"Thiết kế website Nhà hàng - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website Nhà hàng giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website Nhà hàng", 
			"alt"			=>	"Thiết kế website Nhà hàng", 
		);

		// Nội thất
		$tpl->set_seo["interior"] = array(
			"titles"		=>	"Thiết kế website bán Nội thất - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Nội thất giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Nội thất", 
			"alt"			=>	"Thiết kế website bán Nội thất", 
		);

		// Du lịch
		$tpl->set_seo["travel"] = array(
			"titles"		=>	"Thiết kế website Du lịch - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website Du lịch giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website Du lịch", 
			"alt"			=>	"Thiết kế website Du lịch", 
		);

		// Dược phẩm
		$tpl->set_seo["medicine"] = array(
			"titles"		=>	"Thiết kế website bán Dược phẩm - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Dược phẩm giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Dược phẩm", 
			"alt"			=>	"Thiết kế website bán Dược phẩm", 
		);

		// Ô tô
		$tpl->set_seo["car"] = array(
			"titles"		=>	"Thiết kế website bán Ô tô - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Ô tô giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Ô tô", 
			"alt"			=>	"Thiết kế website bán Ô tô", 
		);

		// Văn phòng phẩm
		$tpl->set_seo["stationery"] = array(
			"titles"		=>	"Thiết kế website bán Văn phòng phẩm - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Văn phòng phẩm giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Văn phòng phẩm", 
			"alt"			=>	"Thiết kế website bán Văn phòng phẩm", 
		);

		// thủ công mỹ nghệ
		$tpl->set_seo["crafts"] = array(
			"titles"		=>	"Thiết kế website bán đồ thủ công mỹ nghệ - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán đồ thủ công mỹ nghệ giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán đồ thủ công mỹ nghệ", 
			"alt"			=>	"Thiết kế website bán đồ thủ công mỹ nghệ", 
		);

		// Thực phẩm sạch
		$tpl->set_seo["fresh_food"] = array(
			"titles"		=>	"Thiết kế website bán Thực phẩm sạch - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Thực phẩm sạch giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Thực phẩm sạch", 
			"alt"			=>	"Thiết kế website bán Thực phẩm sạch", 
		);

		// Mỹ phẩm
		$tpl->set_seo["cosmetic"] = array(
			"titles"		=>	"Thiết kế website bán Mỹ phẩm - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Mỹ phẩm giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Mỹ phẩm", 
			"alt"			=>	"Thiết kế website bán Mỹ phẩm", 
		);

		// Cửa hàng điện thoại
		$tpl->set_seo["phone_store"] = array(
			"titles"		=>	"Thiết kế website Cửa hàng điện thoại - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website Cửa hàng điện thoại giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website Cửa hàng điện thoại", 
			"alt"			=>	"Thiết kế website Cửa hàng điện thoại", 
		);

		// Đồ gia dụng
		$tpl->set_seo["houseware"] = array(
			"titles"		=>	"Thiết kế website bán Đồ gia dụng - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Đồ gia dụng giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Đồ gia dụng", 
			"alt"			=>	"Thiết kế website bán Đồ gia dụng", 
		);

		// Đồ thể thao
		$tpl->set_seo["sportswear"] = array(
			"titles"		=>	"Thiết kế website bán Đồ thể thao - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Đồ thể thao giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Đồ thể thao", 
			"alt"			=>	"Thiết kế website bán Đồ thể thao", 
		);

		// Thiết bị âm thanh
		$tpl->set_seo["audio_equipments"] = array(
			"titles"		=>	"Thiết kế website bán Thiết bị âm thanh - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Thiết bị âm thanh giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Thiết bị âm thanh", 
			"alt"			=>	"Thiết kế website bán Thiết bị âm thanh", 
		);

		// Thương mại điện tử
		$tpl->set_seo["e_commerce"] = array(
			"titles"		=>	"Thiết kế website Thương mại điện tử - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website Thương mại điện tử giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website Thương mại điện tử", 
			"alt"			=>	"Thiết kế website Thương mại điện tử", 
		);

		// Spa
		$tpl->set_seo["spa"] = array(
			"titles"		=>	"Thiết kế website Spa - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website Spa giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website Spa", 
			"alt"			=>	"Thiết kế website Spa", 
		);

		// Thời trang
		$tpl->set_seo["fashion"] = array(
			"titles"		=>	"Thiết kế website Thời trang - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website Thời trang giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website Thời trang", 
			"alt"			=>	"Thiết kế website Thời trang", 
		);

		// Hải sản tươi sống
		$tpl->set_seo["fresh_seafood"] = array(
			"titles"		=>	"Thiết kế website bán Hải sản tươi sống - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Hải sản tươi sống giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Hải sản tươi sống", 
			"alt"			=>	"Thiết kế website bán Hải sản tươi sống", 
		);

		// Nước hoa
		$tpl->set_seo["perfume"] = array(
			"titles"		=>	"Thiết kế website bán Nước hoa - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Nước hoa giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Nước hoa", 
			"alt"			=>	"Thiết kế website bán Nước hoa", 
		);

		// Ba lô
		$tpl->set_seo["backpack"] = array(
			"titles"		=>	"Thiết kế website bán Ba lô - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Ba lô giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Ba lô", 
			"alt"			=>	"Thiết kế website bán Ba lô", 
		);

		// Phụ tùng xe
		$tpl->set_seo["spare_parts_all"] = array(
			"titles"		=>	"Thiết kế website bán Phụ tùng xe - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Phụ tùng xe giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Phụ tùng xe", 
			"alt"			=>	"Thiết kế website bán Phụ tùng xe", 
		);

		// Trà sữa
		$tpl->set_seo["milk_tea"] = array(
			"titles"		=>	"Thiết kế website bán Trà sữa - Web4s", 
			"descriptions"	=>	"Web4s - Dịch vụ Thiết kế website bán Trà sữa giá rẻ, tốt ưu SEO, đăng ký nhanh trong 4 bước", 
			"keywords"		=>	"Thiết kế website bán Trà sữa", 
			"alt"			=>	"Thiết kế website bán Trà sữa", 
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
	* Get industry
	* Return Array
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
	* Custom set data industry list format: parrent => array (act, views, themes)
	* Return Array
	*/
	static public function getIndustryList ()
	{
		global $CMS;

		// industry list
		$industryList = [];

		// Phụ kiện điện thoại
		$industryList["phu-kien-dien-thoai"] = array(
			"act" => "detail", 
			"views" => "phone_accessories", 
			"themes" => "phu-kien-dien-thoai", 
		);

		// Đồng hồ
		$industryList["dong-ho"] = array(
			"act" => "detail", 
			"views" => "clock", 
			"themes" => "dong-ho", 
		);

		// Túi xách
		$industryList["tui-xach"] = array(
			"act" => "detail", 
			"views" => "carrier_bag", 
			"themes" => "tui-xach", 
		);

		// Giày dép
		$industryList["giay-dep"] = array(
			"act" => "detail", 
			"views" => "footwear", 
			"themes" => "giay-dep", 
		);

		// Đồ trang xuất
		$industryList["do-trang-suc"] = array(
			"act" => "detail", 
			"views" => "jewelry", 
			"themes" => "do-trang-suc", 
		);

		// Đồ chơi
		$industryList["do-choi"] = array(
			"act" => "detail", 
			"views" => "toy", 
			"themes" => "do-choi", 
		);

		// Đồ ăn vặt
		$industryList["do-an-vat"] = array(
			"act" => "detail", 
			"views" => "snacks", 
			"themes" => "do-an-vat", 
		);

		// Hoa
		$industryList["hoa"] = array(
			"act" => "detail", 
			"views" => "flower", 
			"themes" => "hoa", 
		);

		// Nội thất
		$industryList["noi-that"] = array(
			"act" => "detail", 
			"views" => "interior", 
			"themes" => "noi-that", 
		);

		// Mỹ phẩm
		$industryList["my-pham"] = array(
			"act" => "detail", 
			"views" => "cosmetic", 
			"themes" => "my-pham", 
		);

		// Ô tô
		$industryList["oto"] = array(
			"act" => "detail", 
			"views" => "car", 
			"themes" => "oto", 
		);

		// views key
		// Váy cưới: "vay-cuoi" => "wedding_dress", 
		// Đồ bà bầu: "do-ba-bau" => "maternity_dress",
		// Đồ lưu niệm: "do-luu-niem" => "souvenir",
		// Thú cưng: "thu-cung" => "pets",
		// Tranh: "tranh" => "picture", 
		// Vé máy bay: "ve-may-bay" => "airline_tickets", 
		// Tuyển dụng: "tuyen-dung" => "employment", 
		// Bất động sản: "bat-dong-san" => "realtor", 
		// Khách sạn: "khach-san" => "hotel", 
		// Website giá rẻ: "website-gia-re" => "website_cheat", 
		// Website doanh nghiệp: "website-doanh-nghiep"	=>	"website_bussiness", 
		// Nhà hàng: "nha-hang" => "restaurant", 
		// Du lịch: "du-lich" => "travel", 
		// Dược phẩm: "duoc-pham" => "medicine", 
		// Văn phòng phẩm: "van-phong-pham" =>	"stationery", 
		// Thủ công mỹ nghệ: "thu-cong-my-nghe" => "crafts", 
		// Thực phẩm sạch: "thu-pham-sach" => "fresh_food", 
		// Cửa hàng điện thoại: "cua-hang-dien-thoai" =>	"phone_store", 
		// Đồ gia dụng: "do-gia-dung" => "houseware", 
		// Đồ thể thao: "do-the-thao" => "sportswear", 
		// Thiết bị âm thanh: "thiet-bi-am-thanh" => "audio_equipments", 
		// Thương mại điện tử: "thuong-mai-dien-tu"	=> "e_commerce", 
		// Spa: "spa" => "spa", 
		// Fashion: "thoi-trang" =>	"fashion", 
		// Hải sản tươi sống: "hai-san-tuoi-song" => "fresh_seafood", 
		// Nước hoa: "nuoc-hoa" => "perfume", 
		// Balo: "balo" => "backpack", 
		// Phụ tùng xe: "phu-tung-xe" => "spare_parts_all",
		// Trà sửa: "tra-sua" => "milk_tea",

		return $industryList;
	}
//*********************
//**End Class
//*********************
}