<?php
use \core\ezy;
use lib\input;

if (!defined('IN_ROOT')) exit();
ezy::load_model('rating');

new rating;

class rating
{
    public $html;

    /**
     * rating constructor.
     */
    public function __construct()
    {
        global $CMS, $DB, $member, $tpl;

        //url decode
        foreach($CMS->input as $k => $v)
        {
            $CMS->input[$k] = !is_array($v) ? urldecode($v) : $v;
        }

        $CMS->class->language->load("rating");

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
            case 'export':
                $this->export();
                break;
            default:
                switch (input::get('subact'))
                {
                    case 'autocomplete':
                        $this->autocomplete();
                        break;
                    default:
                        if(input::get('subact') == 'clear_cache')
                        {
                            $CMS->class->cache->mdelete('rating');
                            $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                            $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                        }
                        else if(input::get('subact') == "sort")
                        {
                            $this->sort();
                        }
                        $this->default_page();
                        break;
                }
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

        $tpl->data = models\rating::listing();
//        $tpl->status_selected[input::get('rating_status')] = 'selected';
//        $tpl->status_options = ezy::render("status_options");
//        $tpl->search_input = ezy::render('search_inputs');

        // Output data
        $CMS->output .= ezy::html("main");
    }

    /**
     * Add form
     */
    public function add()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['add_new_rating'];

        $tpl->header_title = $CMS->lang['add_new_rating'];
        $tpl->act = 'add_do';

        if($CMS->input['act'] == 'add_do')
        {
            $checkValid = \models\rating::validate($CMS->input);
            if($checkValid['valid'])
            {
                $data = $CMS->input;

                if($newData = \models\rating::add($data))
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
            else
            {
                $_SESSION['msg'] = $checkValid['msg'];
            }
        }
        $tpl->data = \models\rating::addValue($CMS->input);
        $tpl->status_selected[$tpl->data['rating_status']] = 'selected';
        $tpl->status_options = ezy::render("status_options");

        // Output data
        $CMS->output .= ezy::html("form");
    }

    /**
     * Edit form
     */
    public function edit()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['edit_rating'];

        $tpl->header_title = $CMS->lang['edit_rating'];
        $tpl->act = 'edit_do';

        $tpl->data = $oldData = \models\rating::getInfo($CMS->input['id']);
        $tpl->data = \models\rating::editValue($tpl->data);

        $tpl->data = array_merge($tpl->data, $CMS->input);

        $tpl->status_selected[$tpl->data['rating_status']] = 'selected';
        $tpl->status_options = ezy::render("status_options");

        if($CMS->input['act'] == 'edit_do')
        {
            $checkValid = \models\rating::validate($CMS->input, $oldData);

            if($checkValid['valid'])
            {
                $data = $CMS->input;
                $data['rating_id'] = intval($CMS->input['id']);

                if(\models\rating::edit($data))
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
            else
            {
                $_SESSION['msg'] = $checkValid['msg'];
            }
        }

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
        \models\rating::delete();

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
        \models\rating::mdelete();

        // Redirect
        $CMS->global->redirectReferer();
    }

    /**
     * Show detail
     */
    public function show()
    {
        global $CMS, $tpl;

        $data = \models\rating::getInfo();

        if(!$data)
        {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            $CMS->global->redirectReferer("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
        }

        $tpl->data = models\rating::convertValue($data);
        $tpl->logs =$CMS->global->logs("{$CMS->input['site']}_{$CMS->input['id']}");

        // Output data
        $CMS->output .= ezy::html("show");
    }

    function load_info()
    {
        global $CMS;

        $return  = [];

        $info = \models\rating::getInfo($CMS->input['id']);

        if($info)
        {
            $info=  \models\rating::convertValue($info);
            $return = [
                'status' => 'ok',
                'data' => $info,
            ];
        }
        else
        {
            $return = [
                'status' => 'fail',
                'msg' => $CMS->lang['data_not_found']
            ];
        }

        input::jsonEncode($return);
    }

    public function autocomplete()
    {
        global $CMS,$tpl;

        $tpl->data = \models\rating::listing();

        $response = [];

        foreach($tpl->data as $data)
        {
            $response[] = [
                'id' => $data['rating_id'],
                'label' => $data['rating_name'],
                'text' => $data['rating_name'],
                'redirect' => "{$CMS->vars['root_domain']}/?site=rating&act=show&id={$data['rating_id']}"
            ];
        }

        input::jsonEncode($response);
    }

    /**
     * Export excel
     */
    public function export()
    {
        global $CMS;
        ezy::load_model("report");
        $link = \models\rating::exportToExcel();
//        header("location: {$link}");

        ezy::load_model("download");
        \models\download::sendFile($link);
    }

    function sort() {
        global $CMS;


        $result = \models\rating::quickUpdateSortOrder($CMS->input['id'], $CMS->input['sort_value']);

        input::jsonEncode($result);
    }
}