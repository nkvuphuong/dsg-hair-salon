<?php
use \core\ezy;
use lib\input;

if (!defined('IN_ROOT')) exit();
ezy::load_model('shift_work');

new shift_work;

class shift_work
{
    public $html;

    /**
     * shift_work constructor.
     */
    public function __construct()
    {
        global $CMS, $DB, $member, $tpl;

        //url decode
        foreach($CMS->input as $k => $v)
        {
            $CMS->input[$k] = is_array($v) ? $v : urldecode($v);
        }

        $CMS->class->language->load("shift_work");

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
                    default:
                        if(input::get('subact') == 'clear_cache')
                        {
                            $CMS->class->cache->mdelete('shift_work');
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

        $tpl->data = models\shift_work::listing();
//        $tpl->status_selected[input::get('shift_work_status')] = 'selected';
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

        $CMS->core->page_title = $CMS->lang['add_new_shift_work'];

        $tpl->header_title = $CMS->lang['add_new_shift_work'];
        $tpl->act = 'add_do';

        if($CMS->input['act'] == 'add_do')
        {
            $checkValid = \models\shift_work::validate($CMS->input);
            if($checkValid['valid'])
            {
                $data = $CMS->input;

                $data['shift_work_start_date'] = isset($data['shift_work_start_date']) ? $CMS->class->date->date2time($data['shift_work_start_date'],1) : 0;
                $data['shift_work_end_date'] = isset($data['shift_work_end_date']) ? $CMS->class->date->date2time($data['shift_work_end_date'],1) : 0;

                if($newData = \models\shift_work::add($data))
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
        $tpl->data = \models\shift_work::addValue($CMS->input);

        // Output data
        $CMS->output .= ezy::html("form");
    }

    /**
     * Edit form
     */
    public function edit()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['edit_shift_work'];

        $tpl->header_title = $CMS->lang['edit_shift_work'];
        $tpl->act = 'edit_do';

        $tpl->data = $oldData = \models\shift_work::getInfo($CMS->input['id']);
        $tpl->data = \models\shift_work::editValue($tpl->data);

        $tpl->data = array_merge($tpl->data, $CMS->input);

        if($CMS->input['act'] == 'edit_do')
        {
            $checkValid = \models\shift_work::validate($CMS->input, $oldData);

            if($checkValid['valid'])
            {
                $data = $CMS->input;
                $data['shift_work_id'] = intval($CMS->input['id']);
                $data['shift_work_start_date'] = isset($data['shift_work_start_date']) ? $CMS->class->date->date2time($data['shift_work_start_date'],1) : 0;
                $data['shift_work_end_date'] = isset($data['shift_work_end_date']) ? $CMS->class->date->date2time($data['shift_work_end_date'],1) : 0;

                if(\models\shift_work::edit($data))
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
        \models\shift_work::delete();

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
        \models\shift_work::mdelete();

        // Redirect
        $CMS->global->redirectReferer();
    }

    /**
     * Show detail
     */
    public function show()
    {
        global $CMS, $tpl;

        $data = \models\shift_work::getInfo();

        if(!$data)
        {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            $CMS->global->redirectReferer("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
        }

        $tpl->data = models\shift_work::convertValue($data);
        $tpl->logs =$CMS->global->logs("{$CMS->input['site']}_{$CMS->input['id']}");

        // Output data
        $CMS->output .= ezy::html("show");
    }

    function load_info()
    {
        global $CMS;

        $return  = [];

        $info = \models\shift_work::getInfo($CMS->input['id']);

        if($info)
        {
            $info=  \models\shift_work::convertValue($info);
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

    /**
     * Export excel
     */
    public function export()
    {
        global $CMS;
        ezy::load_model("report");
        $link = \models\shift_work::exportToExcel();
//        header("location: {$link}");

        ezy::load_model("download");
        \models\download::sendFile($link);
    }

    function sort() {
        global $CMS;


        $result = \models\shift_work::quickUpdateSortOrder($CMS->input['id'], $CMS->input['sort_value']);

        input::jsonEncode($result);
    }
}