<?php
use \core\ezy;
use lib\input;

if (!defined('IN_ROOT')) exit();
ezy::load_model("discount");

new discount_items;

class discount_items
{
    public $html;

    /**
     * discount_items constructor.
     */
    public function __construct()
    {
        global $CMS, $DB, $member, $tpl;

        //url decode
        foreach($CMS->input as $k => $v)
        {
            $CMS->input[$k] = urldecode($v);
        }

        $CMS->class->language->load("discount");
        $CMS->class->language->load("discount_items");

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
                switch (\lib\input::get('subact'))
                {
                    case 'generate_random_code':
                        $this->generate_random_code();
                        break;
                    case 'autocomplete':
                        $this->autocomplete();
                        break;
                    default:
                        if(\lib\input::get('subact') == 'clear_cache')
                        {
                            $CMS->class->cache->mdelete('discount_items');
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

        $tpl->data = \models\discount_items::listing();

        $tpl->apply_for_selected[$CMS->input['di_apply_for']] = 'selected';
        $tpl->apply_for_options = ezy::render('apply_for_options');
        $tpl->selected['di_type'][$CMS->input['di_type']] = 'selected';
        $tpl->type_options = ezy::render('type_options');

        // Output data
        $CMS->output .= ezy::html("main");
    }

    public function autocomplete()
    {
        global $CMS,$tpl;
        $tpl->data = \models\discount_items::listing();

        $response = [];

        foreach($tpl->data as $data)
        {
            $response[] = [
                'id' => $data['di_id'],
                'label' => $data['di_code'],
                'text' => $data['di_code'],
                'redirect' => "{$CMS->vars['root_domain']}/?site=discount_items&act=show&id={$data['di_id']}"
            ];
        }

        input::jsonEncode($response);
    }

    /**
     * Add form
     */
    public function add()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['add_new_discount_items'];

        $tpl->header_title = $CMS->lang['add_new_discount_items'];
        $tpl->act = 'add_do';

        $tpl->selected['di_type'][$CMS->input['di_type']] = 'selected';

        if($CMS->input['act'] == 'add_do')
        {
            $checkValid = \models\discount_items::validate($CMS->input);

            if($checkValid['valid'])
            {
                if($newData = \models\discount_items::add($CMS->input))
                {
                    if(isset($CMS->input['ajax']) && $CMS->input['ajax'] == 1)
                    {
                        $msg = $_SESSION['msg'];
                        unset($_SESSION['msg']);

                        $discountData = \models\discount::getInfo($newData['discount_id']);
                        input::jsonEncode(['status' => 'ok', 'data' => $discountData, 'msg' => $msg]);
                    }
                    else
                    {
                        if($CMS->input['action_redirect'] == 'add')
                        {
                            $redirect = "{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=add";
                        }
                        else if($CMS->input['action_redirect'] == 'detail')
                        {
                            $redirect = "{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=show&id={$newData['di_id']}";
                        }
                        else
                        {
                            $redirect = "{$CMS->vars['root_domain']}/?site={$CMS->input['site']}";
                        }
                        $CMS->global->redirect($redirect);
                    }
                }
                else
                {
                    if(isset($CMS->input['ajax']) && $CMS->input['ajax'] == 1)
                    {
                        input::jsonEncode(['status' => 'fail', 'msg' => $CMS->lang['added_di_failed']]);
                    }
                    else
                    {
                        $_SESSION['msg'] = $CMS->lang['added_di_failed'];
                    }
                }
            }
            else
            {
                if(isset($CMS->input['ajax']) && $CMS->input['ajax'] == 1)
                {
                    $return = ['status' => 'fail', 'msg' => $checkValid['msg']];
                    input::jsonEncode($return);
                }
                else
                {
                    $_SESSION['msg'] = $checkValid['msg'];
                }
            }
        }

        $tpl->data = \models\discount_items::addValue($CMS->input);

        $apply_for = $tpl->data['di_apply_for'];
        if($tpl->data['di_apply_rules'][$apply_for])
        {
            $CMS->input['ids'] = implode(',',$tpl->data['di_apply_rules'][$apply_for]);

            if($apply_for == 'product')
            {
                $tpl->product_data = $CMS->product->listing();
            }
            else if($apply_for == 'product_group')
            {
                $tpl->product_data = $CMS->product_group->listing();
            }
        }

        $tpl->discount_data = \models\discount::getInfo($tpl->data['discount_id']);
        $tpl->checked_unlimited = $tpl->data['unlimited'] == 'on' ? 'checked' : '';
        $tpl->apply_for_selected[$tpl->data['di_apply_for']] = 'selected';

        $tpl->type_options = ezy::render('type_options');
        $tpl->apply_for_options = ezy::render('apply_for_options');
        $tpl->form_body = ezy::render('form_body');

        // Output data
        $CMS->output .= ezy::html("form");
    }

    /**
     * Edit form
     */
    public function edit()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['edit_discount_items'];

        $tpl->header_title = $CMS->lang['edit_discount_items'];
        $tpl->act = 'edit_do';

        $oldData = $tpl->data = \models\discount_items::getInfo($CMS->input['id']);
        $tpl->data = \models\discount_items::editValue($tpl->data);

        $tpl->data = array_merge($tpl->data, $CMS->input);

        if($CMS->input['act'] == 'edit_do')
        {
            $checkValid = \models\discount_items::validate($CMS->input, $oldData['di_code']);

            if($checkValid['valid'])
            {
                if(\models\discount_items::edit($CMS->input))
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

        $apply_for = $tpl->data['di_apply_for'];

        if($tpl->data['di_apply_rules'][$apply_for])
        {
            $CMS->input['ids'] = implode(',',$tpl->data['di_apply_rules'][$apply_for]);

            if($apply_for == 'product')
            {
                $tpl->product_data = $CMS->product->listing();
            }
            else if($apply_for == 'product_group')
            {
                $tpl->product_group_data = $CMS->product_group->listing();
            }
        }

        $tpl->discount_data = \models\discount::getInfo($tpl->data['discount_id']);
        $tpl->checked_unlimited = $tpl->data['unlimited'] == 'on' ? 'checked' : '';
        $tpl->apply_for_selected[$tpl->data['di_apply_for']] = 'selected';

        $tpl->selected['di_type'][$tpl->data['di_type']] = 'selected';
        $tpl->type_options = ezy::render('type_options');
        $tpl->apply_for_options = ezy::render('apply_for_options');
        $tpl->form_body = ezy::render('form_body');

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
        \models\discount_items::delete();

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
        \models\discount_items::mdelete();

        // Redirect
        $CMS->global->redirectReferer();
    }

    /**
     * Show detail
     */
    public function show()
    {
        global $CMS, $tpl;

        $data = \models\discount_items::getInfo();

        if(!$data)
        {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            $CMS->global->redirectReferer("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
        }

        $tpl->data = models\discount_items::convertValue($data);

        // Output data
        $CMS->output .= ezy::html("show");
    }

    function generate_random_code()
    {
        global $CMS;

        echo \models\discount_items::generateCode(intval($CMS->input['number_of_character'])); exit;
    }

    /**
     * Export excel
     */
    public function export()
    {
        global $CMS;
        ezy::load_model("report");
        $link = \models\discount_items::exportToExcel();
//        header("location: {$link}");

        ezy::load_model("download");
        \models\download::sendFile($link);
    }
}