<?php
use \core\ezy;

if (!defined('IN_ROOT')) exit();
ezy::load_model("logo_positions");

new logo_positions;

class logo_positions
{
    public $html;

    /**
     * logo_positions constructor.
     */
    public function __construct()
    {
        global $CMS, $DB, $member, $tpl;

        $CMS->class->language->load("logo_positions");

        $tpl->modes = \models\logo_positions::$modes;
        $tpl->types = \models\logo_positions::$types;

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

        $tpl->data = \models\logo_positions::listing();

        $tpl->modeSelected[$CMS->input['pos_mode']] = 'selected';
        $tpl->typeSelected[$CMS->input['pos_type']] = 'selected';

        // Output data
        $CMS->output .= ezy::html("main");
    }

    /**
     * Add form
     */
    public function add()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['add_new_pos'];

        $tpl->header_title = $CMS->lang['add_new_pos'];
        $tpl->act = 'add_do';

        if($CMS->input['act'] == 'add_do')
        {
            if($newData = \models\logo_positions::add($CMS->input))
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
        $tpl->modeSelected[$tpl->data['pos_mode']] = 'selected';
        $tpl->typeSelected[$tpl->data['pos_type']] = 'selected';

        // Output data
        $CMS->output .= ezy::html("form");
    }

    /**
     * Edit form
     */
    public function edit()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['edit_pos'];

        $tpl->header_title = $CMS->lang['edit_pos'];
        $tpl->act = 'edit_do';

        if($CMS->input['act'] == 'edit_do')
        {
            if(\models\logo_positions::edit($CMS->input))
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

        $tpl->data = \models\logo_positions::getInfo($CMS->input['id']);
        $tpl->data = \models\logo_positions::editValue($tpl->data);

        $tpl->data = array_merge($tpl->data, $CMS->input);
        $tpl->modeSelected[$tpl->data['pos_mode']] = 'selected';
        $tpl->typeSelected[$tpl->data['pos_type']] = 'selected';

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
        \models\logo_positions::delete();

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
        \models\logo_positions::mdelete();

        // Redirect
        $CMS->global->redirectReferer();
    }

    /**
     * Show detail
     */
    public function show()
    {
        global $CMS, $tpl;

        $data = \models\logo_positions::getInfo();

        if(!$data)
        {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            $CMS->global->redirectReferer("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
        }

        $tpl->data = models\logo_positions::convertValue($data);

        // Output data
        $CMS->output .= ezy::html("show");
    }
}