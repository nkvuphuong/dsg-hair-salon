<?php
use \core\ezy;

if (!defined('IN_ROOT')) exit();
ezy::load_model("user_group");

new user_group;

class user_group
{
    public $html;

    /**
     * user_group constructor.
     */
    public function __construct()
    {
        global $CMS, $DB, $member, $tpl;

        $CMS->class->language->load("user_group");

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
                    $CMS->class->cache->mdelete('user_group');
                    $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                }
                else if(\lib\input::get('subact') == 'load_commission')
                {
                    $this->load_commission();
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

        $tpl->data = \models\user_group::listing();

        // Output data
        $CMS->output .= ezy::html("main");
    }

    /**
     * Add form
     */
    public function add()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['group_add'];

        $tpl->header_title = $CMS->lang['group_add'];
        $tpl->act = 'add_do';

        if($CMS->input['act'] == 'add_do')
        {
            if($newData = \models\user_group::add($CMS->input))
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

            $CMS->input['userg_permission'] = $CMS->user->get_permission();
        }
        else
        {
            unset($CMS->input['userg_permission']);
        }

        $tpl->data = $CMS->input;
        $tpl->isRootChecked = $tpl->data['userg_is_root'] ? 'checked' : '';
        $tpl->isAdminChecked = $tpl->data['userg_is_admin'] ? 'checked' : '';

        $tpl->commissionData = \models\user_group::getCommission($_POST);

        // Output data
        $CMS->output .= ezy::html("form");
    }

    /**
     * Edit form
     */
    public function edit()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['group_edit'];

        $tpl->header_title = $CMS->lang['group_edit'];
        $tpl->act = 'edit_do';

        if($CMS->input['act'] == 'edit_do')
        {
            if(\models\user_group::edit($CMS->input))
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

            $CMS->input['userg_permission'] = $CMS->user->get_permission();
        }
        else
        {
            unset($CMS->input['userg_permission']);
        }

        $tpl->data = \models\user_group::getInfo($CMS->input['id']);
        $tpl->data = \models\user_group::editValue($tpl->data);

        $tpl->data = array_merge($tpl->data, $CMS->input);
        $tpl->isRootChecked = $tpl->data['userg_is_root'] ? 'checked' : '';
        $tpl->isAdminChecked = $tpl->data['userg_is_admin'] ? 'checked' : '';

        $tpl->commissionData = \models\user_group::getCommission(!empty($_POST) ? $_POST : $CMS->input['id']);

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
        \models\user_group::delete();

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
        \models\user_group::mdelete();

        // Redirect
        $CMS->global->redirectReferer();
    }

    /**
     * Show detail
     */
    public function show()
    {
        global $CMS, $tpl;

        $data = \models\user_group::getInfo();

        if(!$data)
        {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            $CMS->global->redirectReferer("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
        }

        $tpl->data = models\user_group::convertValue($data);

        // Output data
        $CMS->output .= ezy::html("show");
    }

    public function load_commission()
    {
        global $CMS;

        $id = intval($CMS->input['id']);

        $data = \models\user_group::getInfo();

        if($data['userg_commission_data'])
        {
            echo $data['userg_commission_data']; exit;
        }

        echo '[]'; exit;
    }

}