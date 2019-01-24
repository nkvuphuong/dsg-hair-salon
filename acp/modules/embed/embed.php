<?php
use \core\ezy;
use lib\input;

if (!defined('IN_ROOT')) exit();
ezy::load_model('embed');

new embed;

class embed
{
    public $html;

    /**
     * embed constructor.
     */
    public function __construct()
    {
        global $CMS, $DB, $member, $tpl;

        //url decode
        foreach($CMS->input as $k => $v)
        {
            $CMS->input[$k] = urldecode($v);
        }

        $CMS->class->language->load("embed");

        $tpl->mirror_theme = "3024-night";

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
                            $CMS->class->cache->mdelete('embed');
                            $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                            $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
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

        $tpl->data = models\embed::listing();
        $tpl->status_selected[input::get('embed_status')] = 'selected';
        $tpl->status_options = ezy::render("status_options");

        $tpl->search_input = ezy::render('search_inputs');

        // Output data
        $CMS->output .= ezy::html("main");
    }

    /**
     * Add form
     */
    public function add()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['add_new_embed'];

        $tpl->header_title = $CMS->lang['add_new_embed'];
        $tpl->act = 'add_do';

        if($CMS->input['act'] == 'add_do')
        {
            $checkValid = \models\embed::validate($_POST);
            if($checkValid['valid'])
            {
                $data = $_POST;

                $data['embed_start_date'] = $data['embed_start_date'] ? $CMS->class->date->date2time($data['embed_start_date'],1) : 0;
                $data['embed_end_date'] = $data['embed_end_date'] ? $CMS->class->date->date2time($data['embed_end_date'],1) : 0;

                if($newData = \models\embed::add($data))
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
        $tpl->data = \models\embed::addValue($_POST);
        $tpl->status_selected[input::arrayValue($tpl->data, 'embed_status')] = 'selected';
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

        $CMS->core->page_title = $CMS->lang['edit_embed'];

        $tpl->header_title = $CMS->lang['edit_embed'];
        $tpl->act = 'edit_do';

        $tpl->data = $oldData = \models\embed::getInfo($CMS->input['id']);
        $tpl->data = \models\embed::editValue($tpl->data);

        $tpl->data = array_merge($tpl->data, $_POST);

        $tpl->status_selected[$tpl->data['embed_status']] = 'selected';
        $tpl->status_options = ezy::render("status_options");

        if($CMS->input['act'] == 'edit_do')
        {
            $checkValid = \models\embed::validate($_POST, $oldData);

            if($checkValid['valid'])
            {
                $data = $_POST;
                $data['embed_id'] = intval($CMS->input['id']);
                $data['embed_start_date'] = $data['embed_start_date'] ? $CMS->class->date->date2time($data['embed_start_date'],1) : 0;
                $data['embed_end_date'] = $data['embed_end_date'] ? $CMS->class->date->date2time($data['embed_end_date'],1) : 0;

                if(\models\embed::edit($data))
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
        \models\embed::delete();

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
        \models\embed::mdelete();

        // Redirect
        $CMS->global->redirectReferer();
    }

    /**
     * Show detail
     */
    public function show()
    {
        global $CMS, $tpl;

        $data = \models\embed::getInfo();

        if(!$data)
        {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            $CMS->global->redirectReferer("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
        }

        $tpl->data = models\embed::convertValue($data);
        $tpl->logs =$CMS->global->logs("{$CMS->input['site']}_{$CMS->input['id']}");

        // Output data
        $CMS->output .= ezy::html("show");
    }

    function load_info()
    {
        global $CMS;

        $return  = [];

        $info = \models\embed::getInfo($CMS->input['id']);

        if($info)
        {
            $info=  \models\embed::convertValue($info);
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

        $tpl->data = \models\embed::listing();

        $response = [];

        foreach($tpl->data as $data)
        {
            $response[] = [
                'id' => $data['embed_id'],
                'label' => $data['embed_name'],
                'text' => $data['embed_name'],
                'redirect' => "{$CMS->vars['root_domain']}/?site=embed&act=show&id={$data['embed_id']}"
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
        $link = \models\embed::exportToExcel();
//        header("location: {$link}");

        ezy::load_model("download");
        \models\download::sendFile($link);
    }

    public function sync()
    {
        global $CMS;

        \models\embed::syncData();

        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");

        exit;
    }
}