<?php
use \core\ezy;
use lib\input;

if (!defined('IN_ROOT')) exit();
ezy::load_model("attribute");

new attribute;

class attribute
{
    public $html;

    public function __construct()
    {
        global $CMS, $DB, $member, $tpl;

        $CMS->class->language->load("attribute");

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
                if(\lib\input::get('subact') == "add_attr")
                {
                    $this->add_attr();
                }elseif(\lib\input::get('subact') == "edit_attr")
                {
                    $this->edit_attr();
                }elseif(\lib\input::get('subact') == "getinfo_attribute")
                {
                    $this->getinfo_attribute();
                }elseif(\lib\input::get('subact') == "search_attr")
                {
                    $this->search_attr();
                }elseif(\lib\input::get('subact') == "delvalueoption")
                {
                    $this->delvalueoption();
                }elseif(\lib\input::get('subact') == "delattr")
                {
                    $this->delattr();
                }elseif(\lib\input::get('subact') == "listattribute")
                {
                    $this->listattribute();
                }else    
                {
                    $this->default_page();
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

        $tpl->data = models\attribute::listing();


        // Output data
        $CMS->output .= ezy::html("main");
    }

    /**
     * Add form
     */
    public function add()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['add_new_attribute_group'];

        $tpl->header_title = $CMS->lang['add_new_attribute_group'];
        $tpl->act = 'add_do';

        if($CMS->input['act'] == 'add_do')
        {
            if($newData = \models\attribute::add($CMS->input))
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
        $tpl->data = \models\attribute::editValue($tpl->data);

        // Output data
        $CMS->output .= ezy::html("form");
    }

    /**
     * Edit form
     */
    public function edit()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['title_edit_attribute'];

        $tpl->header_title = $CMS->lang['title_edit_attribute'];
        $tpl->act = 'edit_do';

        if($CMS->input['act'] == 'edit_do')
        {
            if(\models\attribute::edit($CMS->input))
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

        $tpl->data = \models\attribute::getInfo_group($CMS->input['id']);
        $tpl->data = \models\attribute::editValue($tpl->data);

        $tpl->data = array_merge($tpl->data, $CMS->input);
        // tpl attribute 
        $tpl->attribute_list = \models\attribute::getListAttribute($tpl->data);
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
        \models\attribute::delete();

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
        \models\attribute::mdelete();

        // Redirect
        $CMS->global->redirectReferer();
    }

    /**
     * Show detail
     */
    public function show()
    {
        global $CMS, $tpl;

        $data = \models\attribute::getInfo_group();

        if(!$data)
        {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            $CMS->global->redirectReferer("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
        }

        $tpl->data = \models\attribute::convertValue($data);
        // p($tpl->data);exit;
        $tpl->attribute_list = \models\attribute::getListAttribute($data);

        // Output data
        $CMS->output .= ezy::html("show");
    }


    public function add_attr()
    {
        global $CMS;

        $data = \models\attribute::add_attr();
        if($data)
        {
            print json_encode(array("status" => "success", "msg" => $CMS->lang['msg_add_attribute_success'], "data" => $data));
            exit;
        }else
        {
            print json_encode(array("status" => "error", "msg" => $CMS->lang['msg_add_attribute_error']));
            exit;
        }

    }

    public function edit_attr()
    {
        global $CMS;

        $data = \models\attribute::edit_attr();
        if($data)
        {
            print json_encode(array("status" => "success", "msg" => $CMS->lang['msg_edit_attribute_success'], "data" => $data));
            exit;
        }else
        {
            print json_encode(array("status" => "error", "msg" => $CMS->lang['msg_edit_attribute_error']));
            exit;
        }

    }

    public function getinfo_attribute()
    {
        global $CMS;

        $id = intval($CMS->input['id']);
        $data = \models\attribute::getInfo($id);
        list($data['options_count'], $data['attribute_options']) = \models\attribute::countOptionsAttribute($id, 1);

        print json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function search_attr()
    {
        global $CMS;

        $data = \models\attribute::searchAttr();
        input::jsonEncode($data);
        exit;
    }

    public function delvalueoption()
    {
        global $CMS;
        $id = intval($CMS->input['id']);
        \models\attribute::delvalueoption($id);
        print 1;
        exit;
    }

    public function delattr()
    {
        global $CMS;
        $id = intval($CMS->input['id']);
        \models\attribute::delattr($id);
        print 1;
        exit;
    }

    public function listattribute()
    {
        global $CMS;

        $data = \models\attribute::listtingAttribute();
        print $data ? json_encode($data, JSON_UNESCAPED_UNICODE) : "";exit;
    }
}