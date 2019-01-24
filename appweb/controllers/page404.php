<?php
namespace controller;

use core\ezy;

class page404 
{
//*********************
//**Start Class
//*********************
    static public function auto_run() 
    {
		global $CMS, $tpl;

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
		
		echo ezy::html('main');
    }
//*********************
//**End Class
//*********************
}