<?php
/**
 * Created by PhpStorm.
 * User: HLoi
 * Date: 4/4/2017
 * Time: 10:42 AM
 */

namespace controller;

use core\ezy;
use models\tags;
use lib\language;

ezy::load_model("tags");

class news
{
    static public function auto_run()
    {
        global $CMS, $tpl;

        //Load lang global
        language::$default = $CMS->vars['default_language'];
        $CMS->class->language->load("news");
        // og:type facebook
        $CMS->vars['og_type'] = "article";

        $tpl->dataRecentPosts = \models\news::getListNews(0, 4);
        $tpl->dataListCategory = \models\news::getListCategory();
        $tpl->dataTags = tags::getData();
        // $tpl->listAuthors = \models\news::getListAuthorNews();

        switch ( ezy::$act )
        {
            case "search":
                self::search();
                break;
            case "category":
                self::category();
                break;
            case "detail":
                self::detail();
                break;
            case "tags":
                self::tags();
                break;
            default:
                self::main();
                break;
        }
    }

    /**
     * Default page
     */

    static private function main()
    {
        global $CMS, $tpl;

        // Use for paging
        $tpl->modLink = "/news";

        $limit = isset($CMS->vars['news_limit']) ? intval($CMS->vars['news_limit']) : 6;
        // Load data
        $tpl->dataListNews = \models\news::getListNews(0, $limit, 0, true);

        echo ezy::html();
    }

    /**
     * Load news via category
     */

    static private function category()
    {
        global $tpl;

        // Get category info
        $data = \models\news::getCategory(ezy::$subact);
        // Check exsit name category
        if(empty($data['name']) and ezy::checkFile404()) 
        {
            echo ezy::html("tpl.error_404", "layouts");exit;
        }
        
        $tpl->dataCategory = $data;
        // Get list cat child
        $tpl->listCatChild = \models\news::getListCategory($data['id']);
        $tpl->dataCategory['classs_full_page'] = count($tpl->listCatChild) > 0 ? "col-md-9" : "col-md-12"; 
        // Update SEO
        $tpl->seo['title'] = isset($data['seo_title']) ? $data['seo_title'] : $data['name'];
        $tpl->seo['description'] = isset($data['seo_description']) ? $data['seo_description'] : $data['seo_description'];
        $tpl->seo['seo_description'] = isset($data['seo_description']) ? $data['seo_description'] : $data['seo_description'];

        // Use for paging
        $tpl->modLink = $data['cat_url'];

        // Set breadcrumb
        $tpl->title = $data['name'];

        // Load list
        $tpl->dataListNews = \models\news::getListNews(0, 6, $data['id'], true);

        echo ezy::html();
    }

    /**
     * Get detail
     */

    static private function detail()
    {
        global $tpl;

        // Get news info
        $tpl->data = \models\news::getNews(ezy::$subact);
        if(empty($tpl->data['name']) and ezy::checkFile404()) 
        {
            echo ezy::html("tpl.error_404", "layouts");exit;
        }
        // Set breadcrumb
        $tpl->title = $tpl->data['name'];
        // Update views
        \models\news::updateViews($tpl->data['id']);

        // Update SEO
        $tpl->seo['title'] = $tpl->data['seo_title'] ? $tpl->data['seo_title'] : $tpl->data['name'];
        $tpl->seo['keywords'] = $tpl->data['seo_description'];
        $tpl->seo['description'] = $tpl->data['seo_keywords'];
        $tpl->seo['author'] = $tpl->data['seo_keywords'];

        $tpl->seo['og_title'] = $tpl->seo['title'];
        $tpl->seo['og_description'] = $tpl->data['seo_description'];
        $tpl->seo['og_image'] = $tpl->data['image_M'];

        $tpl->seo['dc_title'] = $tpl->seo['title'];
        $tpl->seo['dc_description'] = $tpl->data['seo_description'];
        $tpl->seo['dc_subject'] = $tpl->data['seo_keywords'];

        echo ezy::html("detail");
    }

    /**
     * Search
     */

    static private function search()
    {
        global $CMS, $tpl;

        // Keyword
        $keyword = isset($CMS->input['keyword']) ? $CMS->input['keyword'] : ezy::$subact;

        $tpl->keyword = $CMS->class->filter->clean_value($keyword);

        // Use for paging
        $tpl->modLink = "/news/search/".$tpl->keyword;

        // Load data
        $tpl->dataListNews = \models\news::getListNews(0, 6, 0, true);

        echo ezy::html();
    }

    /**
     * Tags
     */

    static private function tags()
    {
        global $CMS, $tpl;
        // Keyword
        $tags = isset($CMS->input['tags']) ? $CMS->input['tags'] : ezy::$subact;

        // Use for paging
        $tpl->modLink = "/news/tags/".$tags;

        $tpl->tags = str_replace('-',' ', htmlspecialchars_decode(urldecode($tags)));


        // Load data
        $tpl->dataListNews = \models\news::getListNews(0, 6, 0, true);

        echo ezy::html();
    }
}