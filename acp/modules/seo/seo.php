<?php
use \core\ezy;

if (!defined('IN_ROOT')) exit();
ezy::load_model("seo");

new seo;

class seo
{
    public $html;

    /**
     * seo constructor.
     */
    public function __construct()
    {
        global $CMS, $DB, $member, $tpl;

        $CMS->class->language->load("seo");

        switch ($CMS->input['act'])
        {
            case 'add':
            case 'add_do':
                $this->add();
                break;
            case 'edit':
            case 'edit_do':
                $this->edit();
                break;
            case 'delete':
                $this->delete();
                break;
            case 'delete_all':
                $this->delete_all();
                break;
            case 'show':
                $this->show();
                break;
            default:

                if(\lib\input::get('subact') == 'clear_cache')
                {
                    $CMS->class->cache->mdelete('seo');
                    $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                }

                $this->default_page();
                break;
        }
    }

    /**
     * Main page
     */
    public function default_page()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['title'];

        $tpl->quickSearchDisplay = $CMS->input['act'] == 'search_do' ? '' : 'display:none';

        $tpl->data = \models\seo::listing();

        // Output data
        $CMS->output .= ezy::html("main");
    }

    /**
     * Add form
     */
    public function add()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['add_new_seo'];

        $tpl->header_title = $CMS->lang['add_new_seo'];
        $tpl->act = 'add_do';

        if($CMS->input['act'] == 'add_do')
        {
            if($newData = \models\seo::add($CMS->input))
            {
                if($CMS->input['action_redirect'] == 'add')
                {
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=add");
                }
                else if($CMS->input['action_redirect'] == 'detail')
                {
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=show&id={$newData['logo_id']}");
                }
                else
                {
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                }

            }
        }

        $tpl->data = $CMS->input;

        // Output data
        $CMS->output .= ezy::html("form");
    }

    /**
     * Edit form
     */
    public function edit()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['edit_seo'];

        $tpl->header_title = $CMS->lang['edit_seo'];
        $tpl->act = 'edit_do';

        if($CMS->input['act'] == 'edit_do')
        {
            if(\models\seo::edit($CMS->input))
            {
                if($CMS->input['action_redirect'] == 'edit')
                {
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=edit&id={$CMS->input['id']}");
                }
                else
                {
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                }
            }
        }

        $tpl->data = \models\seo::getInfo($CMS->input['id']);
        $tpl->data = \models\seo::editValue($tpl->data);

        $tpl->data = array_merge($tpl->data, $CMS->input);
        $tpl->modeSelected[$tpl->data['seo_mode']] = 'selected';
        $tpl->typeSelected[$tpl->data['seo_type']] = 'selected';

        // Output data
        $CMS->output .= ezy::html("form");
    }

    /**
     * Delete route
     */

    public function delete()
    {
        global $CMS;

        // Delete
        \models\seo::delete();

        // Redirect
        $CMS->global->redirectReferer();
    }

    /**
     * Multi delete route
     */
    public function delete_all()
    {
        global $CMS;
        // Delete all
        \models\seo::mdelete();

        // Redirect
        $CMS->global->redirectReferer();
    }

    /**
     * Show detail
     */
    public function show()
    {
        global $CMS, $tpl;

        $data = \models\seo::getInfo();

        if(!$data)
        {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            $CMS->global->redirectReferer("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
        }

        $tpl->data = models\seo::convertValue($data);

        // Output data
        $CMS->output .= ezy::html("show");
    }
}