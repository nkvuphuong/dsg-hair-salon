<?php
use \core\ezy;

ezy::load_model("redeem");
ezy::load_model("addons");

new redeem;

class redeem
{
    public $html;

    public function __construct()
    {
        global $CMS, $DB, $member, $tpl;

        $CMS->class->language->load("redeem");

        switch ($CMS->input['act'])
        {
            case 'add':
                self::add();
                break;
            case 'add_do':
                self::add_do();
                break;
            case 'edit_do':
                if(\lib\input::get('subact')=="edit_amount")
                {
                    self::edit_amount();
                }else
                {
                    self::edit();
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
            default:
                if(\lib\input::get('subact') == "getdata")
                {
                    self::getData();
                }elseif(\lib\input::get('subact') == "searchajax")
                {
                    self::searchAjax();
                }elseif(\lib\input::get('subact') == "sendmail")
                {
                    self::sendmail();
                }else
                {
                    if(\lib\input::get('subact') == 'clear_cache')
                    {
                        $CMS->class->cache->mdelete('giftcard_items');
                        $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                    }

                    $this->default_page();
                }
                break;
        }
    }

    public function default_page()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['title'];
        // Check mobile
        $tpl->tableMobile = \models\addons::checkMobile();
        $tpl->control = \models\redeem::control();
        $tpl->data = \models\redeem::listing();

        // Output data
        $CMS->output .= ezy::html();
    }

    
    static function edit()
    {
        global $CMS, $tpl;

        \models\redeem::edit();

        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=redeem");
        // header("location: ");
    }

    static function searchAjax()
    {
        global $CMS;

        $data = \models\redeem::searchAjax();
        print json_encode($data, JSON_UNESCAPED_UNICODE);exit;
    }

    static function add()
    {
        global $CMS, $tpl;

        $tpl->dataGiftCard = \models\redeem::getDataGiftcard();
        // Output data
        $CMS->output .= ezy::html("form_giftcard_item");
    }

    static function add_do()
    {
        global $CMS, $tpl;

        if(\models\redeem::add())
        {
            $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=redeem");
        }else
        {
            $tpl->dataGiftCard = \models\redeem::getDataGiftcard();
            $tpl->input = $CMS->input;
            // Output data
            $CMS->output .= ezy::html("form_giftcard_item");
        }
        

    }

    static function delete_all()
    {
        global $CMS;
        \models\redeem::delete_all();
        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=redeem");
    }

    static function delete()
    {
        global $CMS;
        \models\redeem::delete();
        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=redeem");
    }

    static function sendmail()
    {
        global $CMS;
        \models\redeem::sendMailAgain();
        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=redeem");
    }

    static function show()
    {
        global $CMS, $tpl;

        $tpl->data = \models\redeem::getInfo($CMS->input['id']);
        $tpl->data['image'] = $CMS->vars['upload_url']."/giftcards/".$tpl->data['gitem_code'].".png";
        $tpl->data['amount'] = $CMS->class->input->currency($tpl->data['gitem_amount']);
        $tpl->data['amount_remain'] = $CMS->class->input->currency($tpl->data['gitem_amount_remain']);
        $tpl->logs = $CMS->global->logs("redeem_{$CMS->input['id']}");
        // Output data
        $CMS->output .= ezy::html("giftcard_detail");
    }

    static function edit_amount()
    {
        global $CMS;

        \models\redeem::edit_amount();

        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=redeem");
    }
}