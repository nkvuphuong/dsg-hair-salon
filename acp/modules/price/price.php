<?php

use \core\ezy;
use lib\input;

if (!defined('IN_ROOT')) exit();
ezy::load_model('price');
ezy::load_model('product');
ezy::load_model('store');

new price;

class price
{
    public $html;

    /**
     * price constructor.
     */
    public function __construct()
    {
        global $CMS, $DB, $member, $tpl;

        if(!input::vars('price_book_enabled')) {
            $CMS->global->redirectReferer($CMS->vars['root_domain']);
            exit;
        }

        //url decode
        foreach ($CMS->input as $k => $v) {
            $CMS->input[$k] = is_array($v) ? $v : urldecode($v);
        }

        $CMS->class->language->load("price");

        switch ($CMS->input['act']) {
            case 'add':
            case 'add_do':
                $this->add();
                break;
            case 'edit':
            case 'edit_do':
                if (input::get('subact') == 'update_price_id') {
                    $this->update_price_id();
                } else {
                    $this->edit();
                }
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
            case 'set_default':
                $this->set_default();
                break;
            case 'export':
                $this->export();
                break;
            default:
                switch (input::get('subact')) {
                    default:
                        if (input::get('subact') == 'clear_cache') {
                            $CMS->class->cache->mdelete('price');
                            $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                            $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                        } else if (input::get('subact') == "sort") {
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

        $tpl->data = models\price::listing();

        // Output data
        $CMS->output .= ezy::html("main");
    }

    /**
     * Add form
     */
    public function add()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['add_new_price'];

        $tpl->header_title = $CMS->lang['add_new_price'];
        $tpl->act = 'add_do';

        $tpl->data = \models\price::addValue($CMS->input);

        if ($CMS->input['act'] == 'add_do') {
            $checkValid = \models\price::validate($CMS->input);
            if ($checkValid['valid']) {
                $data = $CMS->input;

                $data['price_start_time'] = isset($data['price_start_time']) ? $CMS->class->date->date2time($data['price_start_time'], 1) : 0;
                $data['price_end_time'] = isset($data['price_end_time']) ? $CMS->class->date->date2time($data['price_end_time'], 1) : 0;
                $data['price_stores'] = $data['price_stores'] ? input::jsonEncode($data['price_stores'], 0) : null;
                $data['price_cus_groups'] = $data['price_cus_groups'] ? input::jsonEncode($data['price_cus_groups'], 0) : null;

                if ($newData = \models\price::add($data)) {
                    if ($CMS->input['action_redirect'] == 'add') {
                        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=add");
                    } else if ($CMS->input['action_redirect'] == 'detail') {
                        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=show&id={$newData['logo_id']}");
                    } else {
                        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                    }
                }
            } else {
                $tpl->items = models\price::getItemsByInputs(input::get('items'));
                $_SESSION['msg'] = $checkValid['msg'];
            }
        }

        $tpl->stores = models\store::getStoresJoinCity();
        $tpl->products = models\product::getAll();
        $tpl->group_customers = $CMS->group_customer->getAll();

        // Output data
        $CMS->output .= ezy::html("form");
    }

    /**
     * Edit form
     */
    public function edit()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['edit_price'];

        $tpl->header_title = $CMS->lang['edit_price'];
        $tpl->act = 'edit_do';

        $tpl->data = $oldData = \models\price::getInfo($CMS->input['id']);
        $tpl->data = \models\price::editValue($tpl->data);

        $tpl->data = array_merge($tpl->data, $CMS->input);

        if ($CMS->input['act'] == 'edit_do') {
            $checkValid = \models\price::validate($CMS->input, $oldData);

            if ($checkValid['valid']) {
                $data = $CMS->input;
                $data['price_id'] = intval($CMS->input['id']);
                $data['price_start_time'] = isset($data['price_start_time']) ? $CMS->class->date->date2time($data['price_start_time'], 1) : 0;
                $data['price_end_time'] = isset($data['price_end_time']) ? $CMS->class->date->date2time($data['price_end_time'], 1) : 0;
                $data['price_stores'] = $data['price_stores'] ? input::jsonEncode($data['price_stores'], 0) : null;
                $data['price_cus_groups'] = $data['price_cus_groups'] ? input::jsonEncode($data['price_cus_groups'], 0) : null;

                if (\models\price::edit($data)) {
                    if ($CMS->input['action_redirect'] == 'edit') {
                        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=edit&id={$CMS->input['id']}");
                    } else {
                        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                    }
                }
            } else {
                $tpl->items = models\price::getItemsByInputs(input::get('items'));
                $_SESSION['msg'] = $checkValid['msg'];
            }
        } else {
            $tpl->items = models\price::getItems($CMS->input['id']);
        }

        $tpl->stores = models\store::getStoresJoinCity();
        $tpl->products = models\product::getAll();
        $tpl->group_customers = $CMS->group_customer->getAll();

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
        \models\price::delete();

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
        \models\price::mdelete();

        // Redirect
        $CMS->global->redirectReferer();
    }

    /**
     * Show detail
     */
    public function show()
    {
        global $CMS, $tpl;

        $data = \models\price::getInfo();

        if (!$data) {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            $CMS->global->redirectReferer("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
        }

        $tpl->data = models\price::convertValue($data);

        $tpl->store_tpl_data = \models\price::convertStores($tpl->data['price_stores']);
        $tpl->data['price_stores'] = \core\ezy::render("store_tpl");

        $tpl->cus_group_tpl_data = \models\price::convertCusGroups($tpl->data['price_cus_groups']);
        $tpl->data['price_cus_groups'] = \core\ezy::render("cus_group_tpl");

        $tpl->items = \models\price::getItems(input::get('id'));

        $tpl->logs = $CMS->global->logs("{$CMS->input['site']}_{$CMS->input['id']}");

        // Output data
        $CMS->output .= ezy::html("show");
    }

    function load_info()
    {
        global $CMS;

        $return = [];

        $info = \models\price::getInfo($CMS->input['id']);

        if ($info) {
            $info = \models\price::convertValue($info);
            $return = [
                'status' => 'ok',
                'data' => $info,
            ];
        } else {
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
        $link = \models\price::exportToExcel();
//        header("location: {$link}");

        ezy::load_model("download");
        \models\download::sendFile($link);
    }

    /**
     * Update sort data
     */
    function sort()
    {
        global $CMS;
        $result = \models\price::quickUpdateSortOrder($CMS->input['id'], $CMS->input['sort_value']);
        input::jsonEncode($result);
    }

    function set_default()
    {
        global $CMS, $tpl;

        $tpl->prices = \models\price::getAll();

        foreach ($tpl->prices as $k => $price) {
            $price['price_stores'] = input::jsonDecode($price['price_stores']);
            $price['price_cus_groups'] = input::jsonDecode($price['price_cus_groups']);

            $price['price_stores'] = is_array($price['price_stores']) ? array_values($price['price_stores']) : [];
            $price['price_cus_groups'] = is_array($price['price_cus_groups']) ? array_values($price['price_cus_groups']) : [];

            $tpl->prices[$k] = $price;
        }

        $tpl->stores = \models\store::getStores();
        $tpl->cusgroups = $CMS->group_customer->getAll();

        // Output data
        $CMS->output .= ezy::html("set_default");
    }

    function update_price_id()
    {
        global $DB;

        if(input::get('group_id')) {
            $data = [
                'price_id' => input::get('price_id') * 1,
                'gc_id' => input::get('group_id') * 1,
            ];

            $rs = $DB->update('group_customer', $data, 'gc_id');
        } else {
            $data = [
                'price_id' => input::get('price_id') * 1,
                'store_id' => input::get('store_id') * 1,
            ];

            $rs = $DB->update('store', $data, 'store_id');
        }

        if ($rs) {
            $rs = [
                'status' => 'success',
                'msg' => 'Saved !',
            ];
        } else {
            $rs = [
                'status' => 'success',
                'msg' => 'Updated fail !',
            ];
        }

        return input::jsonEncode($rs);
    }
}