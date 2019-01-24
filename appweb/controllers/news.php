<?php
namespace controller;

use core\ezy;
// use models\tags;

// ezy::load_model("tags");

class news {
    static public function auto_run() {
        global $CMS, $tpl;

        $tpl->dataRecentPosts = \models\news::getListNews(0, 4);
        $tpl->dataListCategory = \models\news::getListCategory();
        // $tpl->dataTags = tags::getData();
        // $tpl->listAuthors = \models\news::getListAuthorNews();
		$CMS->class->language->load("news");

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

		switch (ezy::$act) {
            case "tim-kiem":
            case "search":
                self::search();
                break;
            case "danh-muc":
            case "category":
                self::category();
                break;
            case "chi-tiet":
            case "detail":
                self::detail();
                break;
            case "the":
            case "tags":
                self::tags();
                break;
            default:
                self::main();
                break;
        }
    }

    static private function main() {
        global $CMS, $tpl;
        $tpl->modLink = "/tin-tuc.html";
        $limit = isset($CMS->vars['news_limit']) ? intval($CMS->vars['news_limit']) : 20;
        $tpl->dataListNews = \models\news::getListNews(0, $limit, 0, true);
 
        echo ezy::html('main');
    }
	
    static private function category() {
        global $tpl;
        
		$data = \models\news::getCategory(rtrim(ezy::$subact, '.html'));
        $tpl->dataCategory = $data;

        $tpl->seo['title'] = isset($data['seo_title']) ? $data['seo_title'] : $data['name'];
        $tpl->seo['description'] = isset($data['seo_description']) ? $data['seo_description'] : $data['seo_description'];
        $tpl->seo['seo_description'] = isset($data['seo_description']) ? $data['seo_description'] : $data['seo_description'];

        $tpl->modLink = "/tin-tuc/danh-muc/" . rtrim(ezy::$subact, '.html');

        $tpl->title = $data['name'];

        $tpl->dataListNews = \models\news::getListNews(0, 20, $data['id'], true);

        echo ezy::html('main');
    }

    static private function detail() {
        global $tpl;
 
        $tpl->data = \models\news::getNews(ezy::$subact);
 
        $tpl->title = $tpl->data['name'];

        \models\news::updateViews($tpl->data['id']);

        $tpl->seo['title'] = $tpl->data['seo_title'] ? $tpl->data['seo_title'] : $tpl->data['name'];
        $tpl->seo['keywords'] = $tpl->data['seo_description'];
        $tpl->seo['description'] = $tpl->data['seo_keywords'];
        $tpl->seo['author'] = $tpl->data['seo_keywords'];

        $tpl->seo['og_title'] = $tpl->seo['title'];
        $tpl->seo['og_description'] = $tpl->data['seo_description'];
        $tpl->seo['og_image'] = $tpl->data['image'];

        $tpl->seo['dc_title'] = $tpl->seo['title'];
        $tpl->seo['dc_description'] = $tpl->data['seo_description'];
        $tpl->seo['dc_subject'] = $tpl->data['seo_keywords'];

        echo ezy::html('detail');
    }
	
    static private function search() {
        global $CMS, $tpl;
		
        $keyword = isset($CMS->input['keyword']) ? $CMS->input['keyword'] : rtrim(ezy::$subact, '.html');

        $tpl->keyword = $CMS->class->filter->clean_value($keyword);

        $tpl->modLink = "/tin-tuc/tim-kiem/".$tpl->keyword;

        $tpl->dataListNews = \models\news::getListNews(0, 20, 0, true);

        echo ezy::html();
    }

    static private function tags() {
        global $CMS, $tpl;
        
		$tags = isset($CMS->input['tags']) ? $CMS->input['tags'] : rtrim(ezy::$subact, '.html');

        $tpl->modLink = "/tin-tuc/the/".$tags;

        $tpl->tags = str_replace('-',' ', htmlspecialchars_decode(urldecode($tags)));
	
		$tpl->dataListNews = \models\news::getListNews(0, 20, 0, true);

        echo ezy::html();
    }
}