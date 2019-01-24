<?php
use \core\ezy;
use lib\input;

if (!defined('IN_ROOT')) exit();
ezy::load_model("discount");

new discount;

class discount
{
    public $html;

    /**
     * discount constructor.
     */
    public function __construct()
    {
        global $CMS, $DB, $member, $tpl;

        //url decode
        foreach($CMS->input as $k => $v)
        {
            $CMS->input[$k] = urldecode($v);
        }

        $CMS->class->language->load("discount_items");
        $CMS->class->language->load("discount");

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
                    case 'load_info':
                       $this->load_info();
                        break;
                    case 'load_discount':
                        $this->load_discount();
                        break;
                    case 'load_product':
                        $this->load_product();
                        break;
                    case 'load_product_group':
                        $this->load_product_group();
                        break;
                    case 'load_customer_group':
                        $this->load_customer_group();
                        break;
                    case 'autocomplete':
                        $this->autocomplete();
                        break;
                    default:
                        if(\lib\input::get('subact') == 'clear_cache')
                        {
                            $CMS->class->cache->mdelete('discount');
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

        $tpl->data = \models\discount::listing();


        $tpl->selected['discount_type'][$CMS->input['discount_type']] = 'selected';
        $tpl->type_options = ezy::render('type_options');
        $tpl->selected['discount_status'][$CMS->input['discount_status']] = 'selected';
        $tpl->status_options = ezy::render('status_options');

        $tpl->apply_for_options = ezy::render('apply_for_options', 'discount_items');
        $tpl->form_items_popup_body = ezy::render('form_body', 'discount_items');

        // Output data
        $CMS->output .= ezy::html("main");
        $CMS->output .= ezy::render("form_items_popup");
    }

    /**
     * Add form
     */
    public function add()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['add_new_discount'];

        $tpl->header_title = $CMS->lang['add_new_discount'];
        $tpl->act = 'add_do';

        $tpl->selected['discount_type'][$CMS->input['discount_type']] = 'selected';
        $tpl->selected['discount_status'][$CMS->input['discount_status']] = 'selected';
        $tpl->type_options = ezy::render('type_options');

        if($CMS->input['act'] == 'add_do')
        {
            $checkValid = \models\discount::validate($CMS->input);
            if($checkValid['valid'])
            {
                if($newData = \models\discount::add($CMS->input))
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
        $tpl->data = \models\discount::addValue($CMS->input);
        // Output data
        $CMS->output .= ezy::html("form");
    }

    /**
     * Edit form
     */
    public function edit()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['edit_discount'];

        $tpl->header_title = $CMS->lang['edit_discount'];
        $tpl->act = 'edit_do';

        $tpl->data = \models\discount::getInfo($CMS->input['id']);
        $tpl->data = \models\discount::editValue($tpl->data);

        $tpl->data = array_merge($tpl->data, $CMS->input);
        $tpl->selected['discount_type'][$tpl->data['discount_type']] = 'selected';
        $tpl->selected['discount_status'][$tpl->data['discount_status']] = 'selected';
        $tpl->type_options = ezy::render('type_options');

        if($CMS->input['act'] == 'edit_do')
        {
            $checkValid = \models\discount::validate($CMS->input);

            if($checkValid['valid'])
            {
                if(\models\discount::edit($CMS->input))
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
        \models\discount::delete();

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
        \models\discount::mdelete();

        // Redirect
        $CMS->global->redirectReferer();
    }

    /**
     * Show detail
     */
    public function show()
    {
        global $CMS, $tpl;

        $data = \models\discount::getInfo();

        if(!$data)
        {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            $CMS->global->redirectReferer("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
        }

        $tpl->data = models\discount::convertValue($data);

        $tpl->type_options = ezy::render('type_options');
        $tpl->apply_for_options = ezy::render('apply_for_options', 'discount_items');
        $tpl->form_items_popup_body = ezy::render('form_body', 'discount_items');

        // Output data
        $CMS->output .= ezy::html("show");
        $CMS->output .= ezy::render("form_items_popup");
    }

    function load_info()
    {
        global $CMS;

        $return  = [];

        $info = \models\discount::getInfo($CMS->input['id']);

        if($info)
        {
            $info=  \models\discount::convertValue($info);
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

    function load_product()
    {
        global $CMS;
        $result = $CMS->product->searchKey($CMS->input['q'], 1);

        $return = [];

        foreach ($result as $re)
        {
            $return[] = ['id' => $re['product_id'],'text' => $re['product_name']];
        }

        input::jsonEncode($return);
    }

    function load_product_group()
    {
        global $CMS;

        $CMS->input['pg_name'] = $CMS->input['q'] ;

        $result = $CMS->product_group->listing();

        $return = [];

        foreach ($result as $re)
        {
            $return[] = ['id' => $re['product_group_id'],'text' => $re['product_group_name']];
        }

        input::jsonEncode($return);
    }

    function load_customer_group()
    {
        global $CMS;

        $CMS->input['gc_name'] = $CMS->input['q'] ;

        $result = $CMS->group_customer->listing();

        $return = [];

        foreach ($result as $re)
        {
            $return[] = ['id' => $re['gc_id'],'text' => $re['gc_name']];
        }

        input::jsonEncode($return);
    }

    function load_discount()
    {
        global $CMS;
        $CMS->input['discount_name'] = $CMS->input['q'];

        $result = \models\discount::listing();

        $return = [];

        foreach ($result as $re)
        {
            $return[] = ['id' => $re['discount_id'],'text' => $re['discount_name']];
        }

        input::jsonEncode($return);
    }

    public function autocomplete()
    {
        global $CMS,$tpl;

        $tpl->data = \models\discount::listing();

        $response = [];

        foreach($tpl->data as $data)
        {
            $response[] = [
                'id' => $data['discount_id'],
                'label' => $data['discount_name'],
                'text' => $data['discount_name'],
                'redirect' => "{$CMS->vars['root_domain']}/?site=discount&act=show&id={$data['discount_id']}"
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
        $link = \models\discount::exportToExcel();
//        header("location: {$link}");

        ezy::load_model("download");
        \models\download::sendFile($link);
    }
}