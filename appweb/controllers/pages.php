<?php
namespace controller;

use core\ezy;

class pages
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
		$CMS->class->language->load("pages");

		// Get page name
		$tpl->page_name = $tpl->page_name ? $tpl->page_name : self::getPageName();

		// Get page name List
		$tpl->page_name_list = $tpl->page_name_list? $tpl->page_name_list : self::getPageNameList();

        // Check act is for same detail page with name
		if ( $tpl->page_name AND $tpl->page_name_list AND in_array($tpl->page_name, $tpl->page_name_list) ) 
		{
			ezy::$act = $tpl->page_name;
		}
		
		// Check act
		switch (ezy::$act) 
		{
            case "content_services": // content services: Dịch vụ nội dung cho website
                self::detail();
                break;

            default:
                self::main();
				break;
        }
	}

    /*
	* Page detail
	*/
    static public function detail()
    {
        global $CMS, $tpl;

        // Set Seo Page
        self::setSeo($tpl->page_name);

        // Layouts
        echo ezy::html($tpl->page_name);
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
	* Default set SEO
	*/
    static public function setSeo( $page = 'main' ) 
    {
		global $CMS, $tpl;

		// Config data
		$tpl->set_seo = [];

		// Main
		$tpl->set_seo["main"] = array(
			"titles"		=>	"Kho website giá rẻ", 
			"descriptions"	=>	"Web4s - Kho website giá rẻ, đạt chuẩn SEO, tích hợp quản lý sản phẩm, thanh toán online", 
			"keywords"		=>	"thiết kế website giá rẻ, seo website, website trọn gói", 
			"alt"			=>	"Giải pháp cho website chuyên nghiệp", 
		);

        // Phan Thiết
        $tpl->set_seo["content_services"] = array(
            "titles"		=>	"Dịch vụ xây dựng nội dung cho website - Web4s",
            "descriptions"	=>	"Web4s - Dịch vụ xây dựng nội dung cho website",
            "keywords"		=>	"Dịch vụ xây dựng nội dung cho website",
            "alt"			=>	"Dịch vụ xây dựng nội dung cho website",
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
	* Get page name
	*/
	static public function getPageName () 
	{
		global $CMS, $tpl;

		$output = "";

		// Get page name follow act
        $page_name = ezy::$act;
        if ( substr($page_name, -5) == '.html' ) // rtrim '.html'
        {
            $page_name = substr_replace($page_name ,"",-5);
        }

        // get data page name
		$tpl->data_page_name = $tpl->data_page_name ? $tpl->data_page_name : self::getDataPageName();

        // Convert page name
        $output = trim($tpl->data_page_name[$page_name]);

        return $output;
	}

	/*
	* Get list page name
	*/
	static public function getPageNameList ()
	{
		global $CMS, $tpl;

		$output = array();

		// get data page name key
		$tpl->data_page_name = $tpl->data_page_name ? $tpl->data_page_name : self::getDataPageName();
		foreach ( $tpl->data_page_name as $value ) 
		{
			$value = trim($value);
			if ( $value ) 
			{
				$output[$value] = $value; // deny duplicate value
			}
		}

		// only get values to new array
		$output = array_values($output);

		return $output;
	}

	/*
	* Custom set data page name format: parrent => original
	*/
	static public function getDataPageName ()
	{
		global $CMS, $tpl;

		// page name list : parrent => original
		$page_name_list = array(

		    // content services: Dịch vụ nội dung cho website
            "dich-vu-xay-dung-noi-dung-website" 	=>	"content_services",

        );
        
		return $page_name_list;
	}
//*********************
//**End Class
//*********************
}