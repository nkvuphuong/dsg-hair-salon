<?php
/**
 * Created by PhpStorm.
 * User: HLoi
 * Date: 4/4/2017
 * Time: 10:42 AM
 */

namespace controller;

// Use
use core\ezy;
use lib\language;
use models\tags;

// load models
ezy::load_model("tags");

class project
{
// ********************
// *** START CLASS
// ********************
    /**
     * Auto controller
     */
    static public function auto_run()
    {
        global $CMS, $tpl;

        //Load lang global
        language::$default = $CMS->vars['default_language'];
        $CMS->class->language->load("news");

        // Get list category
        $tpl->dataListCategory = \models\project::getListCategory();

        // Get Project Hot
        $tpl->dataListProjectHot = \models\project::getListProject(1, 4);

        // Tags
        // $tpl->dataTags = tags::getData();

        switch ( ezy::$act )
        {
            case "search":
                self::search();
                break;

            case "detail":
                self::detail();
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

        // Get list project
        $tpl->dataListProject = \models\project::getListProject(0, isset($CMS->vars['project_limit']) ? intval($CMS->vars['project_limit']) : 12, 0, true);

        // Use for paging
        $tpl->modLink = "/project";

        echo ezy::html();
    }

    /**
     * Details page
     */
    static private function detail()
    {
        global $tpl;

        // Get project info
        $tpl->data = \models\project::getProject(ezy::$subact);
        
        // Set breadcrumb
        $tpl->title = $tpl->data['name'];
        
        // Update project
        \models\project::updateViews($tpl->data['id']);

        // Update SEO
        $tpl->seo['title'] = $tpl->data['seo_title'] ? $tpl->data['seo_title'] : $tpl->data['name'];
        $tpl->seo['keywords'] = $tpl->data['seo_description'];
        $tpl->seo['description'] = $tpl->data['seo_keywords'];
        $tpl->seo['author'] = $tpl->data['seo_keywords'];

        $tpl->seo['og_title'] = $tpl->seo['title'];
        $tpl->seo['og_description'] = $tpl->data['seo_description'];
        $data['pathUpload'] = isset($data['pathUpload']) ? $data['pathUpload'] : '';
        $tpl->seo['og_image'] = \lib\input::getThumb($data['pathUpload'],450);

        $tpl->seo['dc_title'] = $tpl->seo['title'];
        $tpl->seo['dc_description'] = $tpl->data['seo_description'];
        $tpl->seo['dc_subject'] = $tpl->data['seo_keywords'];

        echo ezy::html("detail");
    }

    /**
     * Search page
     */
    static private function search()
    {
        global $CMS, $tpl;

        // Keyword
        $keyword = isset($CMS->input['keyword']) ? $CMS->input['keyword'] : ezy::$subact;

        $tpl->keyword = $CMS->class->filter->clean_value($keyword);

        // Use for paging
        $tpl->modLink = "/project/search/".$tpl->keyword;
        
        // Get list project
        $tpl->dataListProject = \models\project::getListProject(0, isset($CMS->vars['project_limit']) ? intval($CMS->vars['project_limit']) : 12, 0, true);

        echo ezy::html();
    }
// ********************
// *** END CLASS
// ********************
}