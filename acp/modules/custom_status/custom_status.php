<?php
use \core\ezy;

if (!defined('IN_ROOT')) exit();
ezy::load_model("custom_status");

new custom_status;

class custom_status
{
    public $html;

    /**
     * logo_positions constructor.
     */
    public function __construct()
    {
        global $CMS, $DB, $member, $tpl;

        $CMS->class->language->load("custom_status");
        $CMS->class->language->load("order");

        $tpl->modes = \models\custom_status::$modes;
        $tpl->types = \models\custom_status::$types;

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

        $tpl->data = \models\custom_status::listing();


        // Output data
        $CMS->output .= ezy::html("main");
    }

    /**
     * Add form
     */
    public function add()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['add_new_custom_status'];

        $tpl->header_title = $CMS->lang['add_new_custom_status'];
        $tpl->act = 'add_do';

        if($CMS->input['act'] == 'add_do')
        {
            if($newData = \models\custom_status::add($CMS->input))
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

        $tpl->data = \models\custom_status::editValue($CMS->input);

        // Output data
        $CMS->output .= ezy::html("form");
    }

    /**
     * Edit form
     */
    public function edit()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['edit_custom_status'];

        $tpl->header_title = $CMS->lang['edit_custom_status'];
        $tpl->act = 'edit_do';

        if($CMS->input['act'] == 'edit_do')
        {
            if(\models\custom_status::edit($CMS->input))
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

        $tpl->data = \models\custom_status::getInfo($CMS->input['id']);
        $tpl->data = \models\custom_status::editValue($tpl->data);

        $tpl->data = array_merge($tpl->data, $CMS->input);

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
        \models\custom_status::delete();

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
        \models\custom_status::mdelete();

        // Redirect
        $CMS->global->redirectReferer();
    }

    /**
     * Show detail
     */
    public function show()
    {
        global $CMS, $tpl;

        $data = \models\custom_status::getInfo();

        if(!$data)
        {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            $CMS->global->redirectReferer("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
        }

        $tpl->data = models\custom_status::convertValue($data);

        // Output data
        $CMS->output .= ezy::html("show");
    }
}