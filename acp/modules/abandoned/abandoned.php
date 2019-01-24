<?php
use \core\ezy;
use lib\input;
 
if (!defined('IN_ROOT')) exit();
ezy::load_model('abandoned');

new abandoned;
class abandoned
{
    /**
     * abandoned constructor.
     */
    public function __construct()
    {
        global $CMS, $DB, $member, $tpl;

        // Load language
        $CMS->class->language->load("abandoned");

        // controller
        switch ($CMS->input['act'])
        {
            case 'show':
                $this->show();
            break;

            case 'delete':
                $this->delete();
            break;

            case 'delete_all':
                $this->delete_all();
            break;

            default:
                if(\lib\input::get('subact') == "quicksearch")
                {
                    $this->quickSearch();
                }
                else if( \lib\input::get('subact') == 'clear_cache' )
                {
                    $CMS->class->cache->mdelete(\models\abandoned::$cache_prefix);
                    $_SESSION['msg'] .= $CMS->lang['cleared_cache_module'];
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                }
                else
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

        $CMS->core->page_title = $CMS->lang['abandoned_title'];
        $tpl->data = models\abandoned::listing();
        $tpl->record_cnt    = \models\abandoned::$record_cnt;
        $tpl->option_action = \models\abandoned::optionAction();

        // Search
        $tpl->keywords = isset($CMS->input['keywords']) ? urldecode($CMS->input['keywords']) : "";
        $tpl->email    = isset($CMS->input['email']) ? urldecode($CMS->input['email']) : "";
        $tpl->name     = isset($CMS->input['name']) ? urldecode($CMS->input['name']) : "";
        $tpl->ip       = isset($CMS->input['ip']) ? urldecode($CMS->input['ip']) : "";
        $tpl->total    = isset($CMS->input['total']) ? urldecode($CMS->input['total']) : "";
        $tpl->time_from_to  = isset($CMS->input['time_from_to']) ? urldecode($CMS->input['time_from_to']) : "";
        
        $tpl->sent_email = isset($CMS->input['sent_email']) ? urldecode($CMS->input['sent_email']) : "";
        $tpl->option_sent_email = "<option value=''>-----</option>";
        for( $i=0; $i<=1; $i++ )
        {
            $selected = ( is_numeric($tpl->sent_email) AND $tpl->sent_email == $i ) ? "selected='selected'" : '';
            $name     = $CMS->lang["sent_email_{$i}"];
            $tpl->option_sent_email .= "<option value='{$i}' {$selected}>{$name}</option>";
        }

        $tpl->status = isset($CMS->input['status']) ? urldecode($CMS->input['status']) : "";
        $tpl->option_status = "<option value=''>-----</option>";
        for( $i=0; $i<=1; $i++ )
        {
            $selected = ( is_numeric($tpl->status) AND $tpl->status == $i ) ? "selected='selected'" : '';
            $name     = $CMS->lang["status_{$i}"];
            $tpl->option_status .= "<option value='{$i}' {$selected}>{$name}</option>";
        }

        $tpl->count_sent = isset($CMS->input['count_sent']) ? urldecode($CMS->input['count_sent']) : "";

        // Output
        $CMS->output .= ezy::html("main");
    }

    /*
    * Quick search
    */
    public function quickSearch() 
    {
        global $CMS;

        // Data
        $limit = \models\abandoned::$maxPage = 5;
        $dataListAbandoned  = \models\abandoned::listing("", true );
        $dataTotalAbandoned = \models\abandoned::listing("", true, true, 'cnt');

        $output = '<ul class="list-results">';

        if( is_array($dataListAbandoned) AND $dataTotalAbandoned > 0 )
        {
            $cnt = count($dataListAbandoned);
            $i = 0;
            foreach( $dataListAbandoned as $data ) 
            {
                $i++;
                $last = ($cnt == $i) ? 'last' : '';
                $output .= <<<EOF
                <li>
                    <div class="clearfix item {$last}">
                        <a class='pointer inline-block' href='{$CMS->vars['root_domain']}/?site=abandoned&act=show&id={$data['id']}' style="width: 100%;">
                            <h5 style="color: #343434">{$data['email']}
EOF;
                        if( $data['name'] )
                        {
                            $output .= <<<EOF
                            <small class="text-small">({$data['name']})</small>
EOF;
                        }

                        $output .= <<<EOF
                            - {$data['total_c']}
                            </h5>
                            <div>
                                <small class="text-small">{$data['ip']}</small>
                                <small class="text-small"> - </small>
                                <small class="text-small">{$data['time_c']}</small>
                            </div>
                            <div>
                                <small class="text-small">{$data['sent_email_c']}</small>
                                <small class="text-small"> - </small>
                                <small class="text-small">{$data['status_c']}</small>
                            </div>
                        </a>
                    </div>
                </li>
EOF;
            }

            if( $dataTotalAbandoned > $limit )
            {
                $remain = $dataTotalAbandoned - $limit;
                $output .= "<li class='more-results'><a href='{$CMS->vars['root_domain']}/?site=abandoned&keywords={$CMS->input['keywords']}' style='width: 100%;'>{$CMS->lang['view_more']} ({$remain}) {$CMS->lang['result']}</a></li>";
            }
        }
        else
        {
            $output .= "<li>No data...</li>";
        }

        $output .= '</ul>';

        print \lib\input::jsonEncode(array('status' => 'success', 'data' => $output), 0); exit;
    }

    /**
     * Delete
     */

    public function delete()
    {
        global $CMS;

        // Delete
        \models\abandoned::delete();

        // Redirect
        $CMS->global->redirectReferer("{$CMS->vars['root_domain']}/?site=abandoned");
    }

    /**
     * Multi delete
     */
    public function delete_all()
    {
        global $CMS;

        // Delete all
        \models\abandoned::mdelete();

        // Redirect
        $CMS->global->redirectReferer("{$CMS->vars['root_domain']}/?site=abandoned");
    }
    
    /**
     * Show detail
     */
    public function show()
    {
        global $CMS, $tpl;

        $data = \models\abandoned::getInfo();

        if( !$data )
        {
            $_SESSION['errormsg'] = $CMS->lang['data_not_found'];
            $CMS->global->redirectReferer("{$CMS->vars['root_domain']}/?site=abandoned");
        }
        $tpl->data = models\abandoned::convertValue($data);
        $tpl->cart_content = $tpl->data['cart_content_c'];
        $tpl->cart_total = $tpl->data['total_c'];
        $tpl->list_mail  = $tpl->data['list_mail_c'];

        $tpl->footer_html = $CMS->global->footer_back(array('list' => "{$CMS->vars['root_domain']}/?site=abandoned"));
        $tpl->comment = $CMS->global->comment();
        $tpl->logs = $CMS->global->logs(\models\abandoned::$cache_prefix.'_'.$data['id']);

        // Output
        $CMS->output .= ezy::html("show");
    }
 
}