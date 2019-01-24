<?php
use \core\ezy;
use lib\input;

if (!defined('IN_ROOT')) exit();
ezy::load_model('contact');

new contact;

class contact
{
    public $html;

    /**
     * contact constructor.
     */
    public function __construct()
    {
        global $CMS, $DB, $member, $tpl;

        //url decode
        foreach($CMS->input as $k => $v)
        {
            $CMS->input[$k] = urldecode($v);
        }

        $CMS->class->language->load("contact");

        $tpl->mirror_theme = "3024-night"; // theme for layouts

        switch ($CMS->input['act'])
        {
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
                if( \lib\input::get('subact') == 'clear_cache' )
                {
                    $CMS->class->cache->mdelete('contact');
                    $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
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

        $tpl->data = models\contact::listing();

        // Output data
        $CMS->output .= ezy::html("main");
    }

    /**
     * Delete route contact
     */

    public function delete()
    {
        global $CMS;

        // Delete
        \models\contact::delete();

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
        \models\contact::mdelete();

        // Redirect
        $CMS->global->redirectReferer();
    }

    /**
     * Show detail
     * 
     */
    public function show()
    {
        global $CMS, $tpl;

        $data = \models\contact::getInfo();

        if( !$data )
        {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            $CMS->global->redirectReferer("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
        }

        // Update viewed
        if ( $data['con_status'] != 1 ) {\models\contact::updateViewed( $data['con_id'] );}

        $tpl->data = models\contact::convertValue($data);
        $tpl->logs = $CMS->global->logs("{$CMS->input['site']}_{$CMS->input['id']}");

        $tpl->comment = $CMS->global->comment();

        // Output data
        $CMS->output .= ezy::html("show");
    }
}