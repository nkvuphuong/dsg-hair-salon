<?php
use \core\ezy;

ezy::load_model("staff");
ezy::load_model("addons");

new staff;

class staff
{
    public $html;

    public function __construct()
    {
        global $CMS, $DB, $member, $tpl;

        $CMS->class->language->load("staff");

        switch ($CMS->input['act'])
        {
            case 'add':
                self::add();
                break;
            case 'add_do':
                self::add_do();
                break;
            case 'edit':
                self::edit();
                break;
            case 'edit_do':
                self::edit_do();
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
                    $CMS->class->cache->mdelete('user');
                    $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                }

                $this->default_page();
                break;
        }
    }

    public function default_page()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['title'];
        // Check mobile
        $tpl->tableMobile = \models\addons::checkMobile();
        $tpl->control = \models\staff::control();
        $tpl->data = \models\staff::listing();

        // Output data
        $CMS->output .= ezy::html();
    }

    
    static function edit()
    {
        global $CMS, $tpl;

        $tpl->input = \models\staff::getdataForm($CMS->input['id']);
        // Output data
        $CMS->output .= ezy::html("form_staff");
    }

    static function edit_do()
    {
        global $CMS, $tpl;

        if(\models\staff::edit())
        {
            $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=staff");
        }else
        {
            $tpl->input = \models\staff::getdataForm($CMS->input['id']);
            // Output data
            $CMS->output .= ezy::html("form_staff");
        }
        

    }

    static function searchAjax()
    {
        global $CMS;

        $data = \models\staff::searchAjax();
        print json_encode($data, JSON_UNESCAPED_UNICODE);exit;
    }

    static function add()
    {
        global $CMS, $tpl;

        $tpl->input = \models\staff::getdataForm();
        // Output data
        $CMS->output .= ezy::html("form_staff");
    }

    static function add_do()
    {
        global $CMS, $tpl;

        if(\models\staff::add())
        {
            $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=staff");
        }else
        {
            $tpl->input = \models\staff::getdataForm();
            // $tpl->input = $CMS->input;
            // Output data
            $CMS->output .= ezy::html("form_staff");
        }
        

    }

    static function delete_all()
    {
        global $CMS;
        \models\staff::delete_all();
        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=staff");
    }

    static function delete()
    {
        global $CMS;
        \models\staff::delete();
        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=staff");
    }

    static function sendmail()
    {
        global $CMS;
        \models\staff::sendMailAgain();
        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=staff");
    }

    static function show()
    {
        global $CMS, $tpl;

        $tpl->data = \models\staff::getInfo($CMS->input['id']);
        $tpl->data['image'] = $CMS->vars['upload_url']."/giftcards/".$tpl->data['gitem_code'].".png";
        $tpl->data['amount'] = $CMS->class->input->currency($tpl->data['gitem_amount']);
        $tpl->data['amount_remain'] = $CMS->class->input->currency($tpl->data['gitem_amount_remain']);
        $tpl->logs = $CMS->global->logs("staff_{$CMS->input['id']}");
        // Output data
        $CMS->output .= ezy::html("giftcard_detail");
    }

    static function edit_amount()
    {
        global $CMS;

        \models\staff::edit_amount();

        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=staff");
    }
}