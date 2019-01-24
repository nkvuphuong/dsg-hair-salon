<?php

use core\ezy;
use \views\layouts;
use \lib\language;
use \models\attribute;

ezy::load_model("attribute");
class acp_global
{
    public $html = array();

    public function error_html($msg)
    {
        global $CMS;
        $output = "";
        if($msg)
        {
            $output .= <<<EOF
 
<div class="alert alert-danger alert-border-left alert-close alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            <strong>{$CMS->lang['gnotice']}!</strong><br/>{$msg}
 </div>
EOF;
        }
        return $output;
    }

    public function success_html($msg)
    {
        global $CMS;
        $output = "";
        if($msg)
        {
            $output .= <<<EOF
<div class="alert alert-aquamarine alert-border-left alert-close alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            <strong>{$CMS->lang['gnotice']}!</strong><br/>{$msg}
 </div>
EOF;
        }
        return $output;
    }

    public function init_error()
    {
        global $CMS;

        $html = "";

        if (!empty($_SESSION['msg'])  OR !empty($_SESSION['error_msg']) )
        {
            if ( !empty($_SESSION['msg']) )
            {
                $msg = $_SESSION['msg'];
                $_SESSION['msg'] = "";
                $html .= $this->success_html($msg);
            }

            if( !empty($_SESSION['error_msg']) )
            {
                $error_msg = $_SESSION['error_msg'];
                $_SESSION['error_msg'] = "";
                $html .= $this->error_html($error_msg);
            }

            if ($CMS->errormsg == $_SESSION['msg'])
            {
                $_SESSION['msg']="";
                $html .= "";
            }
        }

        return $html;
    }

//=====================================================================================================
//  DISPLAY SEARCH
//=====================================================================================================

    public function init_search($msg)
    {
        global $CMS;

        if ($msg)
        {

        }

        /*$output = <<<EOF

<div class="searchpage">
  <p class="btn_align_right" style="padding: 4px;"><input class="input_submit" type="button" name="search_form_button" id="search_form_button" value="{$CMS->lang['search_continue']}" onclick="javascript:redirect('{$CMS->vars['root_domain']}/?site={$CMS->class->search->mod_name}&act=search{$CMS->class->search->url_return}');"/></p>
    <div><ul>{$msg}</ul></div>
</div>
EOF;*/

        return "";
    }

//=====================================================================================================
//  USER and GROUP PERMISSION Element
//=====================================================================================================

    public function permission($name = "", $key = "", $css_class = "permission_inactive", $value = 0, $show_only = 0)
    {
        $output = "";

        $value = $value == 1 ? "checked" : "";

        if ($key) {
            if ($show_only == 1) {
                if ($value == "") {
                    $output .= <<<EOF
    <li class="permission_disable"> {$name}</li>\n
EOF;
                } else {
                    $output .= <<<EOF
    <li class="permission_enable"> {$name}</li>\n
EOF;
                }
            } else {
                $output .= <<<EOF
    <li onclick="checkbox(this, '{$key}');" class="{$css_class}"><input type="checkbox" style="display: none;" name="{$key}" id="{$key}" value="1" onmouseover="on_mouse=1;" onmouseout="on_mouse=0;" {$value}> {$name}</li>\n
EOF;
            }

        } else if ($name) {

            $output .= <<<EOF
    <li class="permission_title">{$name}</li>\n
EOF;

        }

        return $output;
    }

//=====================================================================================================
//  REDIRECT
//=====================================================================================================

    public function redirect($url)
    {
        global $CMS;

        $page = intval($CMS->input["page"]);

        if ($page > 1 AND $CMS->is_error == 1) {
            $page = $page - 1;
        }

        $page = $page > 1 ? "&page={$page}" : "";

        $url_return = "";
        if (isset($_SESSION["url_return"]) && $_SESSION["url_return"]) {
            $url_return = "&act=search_do{$_SESSION["url_return"]}";

            unset($_SESSION["url_return"]);
        }

        header("location: {$url}{$page}{$url_return}");
        exit;
    }

//=====================================================================================================
//  WYSIWYG
//=====================================================================================================

    public function wysiwyg($mode= "")
    {
        $output = "";

        // $output .= <<<EOF

// <script language="javascript">wysiwyg_init($mode);</script>

// EOF;

        return $output;
    }

//===========================================================================
//  HTML Comment
//===========================================================================

public function comment()
{
     global $CMS, $DB;
     $module_name = $CMS->input['site'];
     $module_id = $CMS->input['id'];

    $sql = $DB->query("SELECT * FROM ".root_table."comment WHERE module_name='{$module_name}' AND module_id='{$module_id}' AND comment_deleted IN (0,2) AND  1=1 ORDER BY comment_time DESC LIMIT 10");
    $sql_total = $DB->query("SELECT 0 FROM ".root_table."comment WHERE module_name='{$module_name}' AND module_id='{$module_id}' AND comment_deleted IN (0,2) AND  1=1 ORDER BY comment_time DESC ");
   
    $total_rows = $DB->num_rows($sql_total);
    $output =<<<EOF
    <script>
         var lang_empty_comment = "{$CMS->lang['comment_empty']}";//
    </script>
<div class="container-fluid">
<section class="add_table">
    <h4 class="heading" style="font-size: 16px;cursor:pointer" id="expand_comment" active="0"  ><i class="fa fa-caret-down" style="cursor:pointer;margin-right: 10px;"></i><span style="font-family: robob;">{$CMS->lang['gcomment_header']} ({$total_rows})</span></h4>

    <section  id="expand_comment_container" style="display:none" >
        <section class="proj-page-add-txt" id="box_send_comment">
                            <input type="text" class="form-control">
                            <button type="button" onclick="action_send_comment('box_send_comment')" maxlength="500" class="btn">{$CMS->lang['comment_send']}</button>
                            <button type="button" class="proj-page-del" onclick="action_draf_comment('box_send_comment')">
                                <i class="font-icon font-icon-trash"></i>
                            </button>
                            <p id="plugin_msg" style="color:red;display:none"></p>
                            <input type="hidden" name="plugin_comment_name" value="{$module_name}"/>
                            <input type="hidden" name="plugin_comment_id" value="{$module_id}"/>
         </section>
 
   <section class="activity-line">
EOF;

                        if ( $DB->num_rows( $sql ) > 0 )
                        {
                            while( $result = $DB->fetch_array( $sql) )
                            {
                                // Convert info
                                $result = $CMS->comment->convertvalue($result);
                                    $user = $CMS->user->get_info($result['user_id']);

                                /*{$user['userg_title']}
                                <div class="activity-line-action-list">
                                        <section class="activity-line-action">

                                                <div class="cont">
                                                    <div class="cont-in">
                                                            <p>{$result['comment_content']} </p>
                                                     </div>
                                                </div>
                                            </section><!--.activity-line-action-->
                                    </div> <!-- activity-line-action-list-->   */

                                $output .=<<<EOF

                                 <article class="activity-line-item box-typical" style="border-radius: 0px !important;" comment_id='{$result['comment_id']}'>
                                    <div class="activity-line-date">
                                                {$result['comment_time']}<br/>
                                               
                                     </div>
                                    <header class="activity-line-item-header">
                                        <div class="activity-line-item-user">
                                            <div class="activity-line-item-user-photo">
                                                 {$result['user_avatar']}
                                                 
                                            </div>
                                            <div class="activity-line-item-user-name">{$result['comment_name']}</div>
                                            <div class="activity-line-item-user-status">{$result['comment_content']}</div>
                                        </div>
                                    </header>
                                </article><!-- activity-line-item box-typical-->
EOF;
                                }
                            }

                            $output .=<<<EOF
 
            </section><!--.proj-page-attach-section-->
EOF;
      if($total_rows > 10)
        {
$output .= <<<EOF

           <div class="activity-line-more" style="text-align:center">
                            <a onclick="seemore_comment('{$module_name}','{$module_id}')">Xem thêm</a>
           </div>
EOF;
        }

$output .= <<<EOF
    </section>        
   <!-- End comment -->

</section>
</div>
EOF;

    return $output;     
}

//===========================================================================
//  HTML logs
//===========================================================================

    public function logs($log_key = "", $log_key2 = "")
    {
        global $CMS, $DB, $member;
        if($log_key == "config_price")
        {
            $is_active = 0;
            $display_block_logs = "display:none";
        }
        else
        {
            $is_active = 1;
            $display_block_logs = "display:block";
        }
        $output = "";
        switch ($log_key) {
            default:
                if($log_key == "config_price")
                {
                    $sql = "SELECT * FROM " . root_table . "logs".($log_key?" WHERE log_key LIKE '%{$log_key}%' ":"").($log_key2 ? "OR log_key LIKE '%{$log_key2}%'" : "")." ORDER BY log_time DESC";
                }
                else
                {
                    $sql = "SELECT * FROM " . root_table . "logs".($log_key?" WHERE log_key='{$log_key}' ":"").($log_key2 ? "OR log_key='{$log_key2}'" : "")." ORDER BY log_time DESC";
                }

                break;
            case 'myself':
                $sql = "SELECT * FROM " . root_table . "logs WHERE user_id='{$member['user_id']}' ORDER BY log_time DESC";
                break;
        }
 
        $sql_query = $DB->query($sql);
        $total_rows = $DB->num_rows($sql_query);
 
        list($CMS->show_page, $sql) = $CMS->class->page->create($sql, 10);

        if ($log_key == "myself") {
            unset($CMS->show_page);
        }

        if ($DB->num_rows($sql) == 0) {
            return false;
        }

        $output .= <<<EOF
<div class="container-fluid">
<section class="add_table">

EOF;
    if($is_active == 1)
    {
           $output .= <<<EOF
 <h4 class="heading" style="font-size: 16px;cursor:pointer" id="expand_logs" active="{$is_active}"  ><i class="fa fa-caret-down" style="cursor:pointer;margin-right: 10px;"></i><span style="font-family: robob;">{$CMS->lang['log_header']}</span></h4>
EOF;
    }
    else
    {
         $output .= <<<EOF
 <h4 class="heading" style="font-size: 16px;cursor:pointer" id="expand_logs" active="{$is_active}"  ><i class="fa fa-caret-right" style="cursor:pointer;margin-right: 10px;"></i><span style="font-family: robob;">{$CMS->lang['log_header']}</span></h4>
EOF;
    }

    $output .= <<<EOF

    <div  id="expand_logs_container" style="{$display_block_logs}">

    <section class="activity-line">
        
EOF;

        $i = 0;

        while ($data = $DB->fetch_array($sql)) {
            $user = $CMS->user->get_info($data['user_id']);
            $detail = "";
            $data['log_ftime'] = $CMS->class->date->date_format($data['log_time'], 1);
            $data['log_time'] = $CMS->class->date->date_format($data['log_time'], 1);
            $data['log_name'] = $data['log_name'];

            if ($data['log_content']) {
                $detail = "<span onclick='show_popup_detail({$data['log_id']});' log_id='{$data['log_id']}'><a>[{$CMS->lang['btn_detail']}]</a></span>";
            }
            if ($user['user_avatar'] != "") {

                $user_avatar = <<<EOF
                                                            
                                <img src="{$CMS->vars['upload_url']}/avatar/thumbnail/{$user['user_avatar']}" alt="user-img" class="img-circle user-img" >
                                
EOF;
            } else {

                $user_avatar = <<<EOF
                                                            
                                <img src="/acp/assets/img/avatar-2-64.png" alt="user-img" class="img-circle user-img">
                                
EOF;
            }


            /*
             Hide some code
             {$user['userg_title']}
             <div class="activity-line-action-list">
                <section class="activity-line-action">

                        <div class="cont">
                            <div class="cont-in">
                                    <p>{$data['log_name']} {$detail} </p>
                             </div>
                        </div>
                    </section><!--.activity-line-action-->
            </div> <!-- activity-line-action-list-->
             */

            $output .= <<<EOF

        <article class="activity-line-item box-typical" style="   border-radius: 0px !important;">
            <div class="activity-line-date">
                        {$data['log_time']}<br/>
                       
             </div>
            <header class="activity-line-item-header">
                <div class="activity-line-item-user">
                    <div class="activity-line-item-user-photo">
                        <a href="{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}" class="pull-left">{$user_avatar}</a>
                    </div>
                    <div class="activity-line-item-user-name">{$user['user_display_name']} </div>
                    <div class="activity-line-item-user-status">{$data['log_name']} {$detail}</div>
                </div>
            </header>

        </article><!-- activity-line-item box-typical-->

EOF;

        }


         $output .= <<<EOF

          
    </section>  <!-- activity-line-->

EOF;
      if($total_rows > 10)
        {
            $output .= <<<EOF

           <div class="activity-line-more" style="text-align:center">
                            <a onclick="seemore_logs('{$log_key}')">Xem thêm</a>
           </div>    

EOF;
        }
         $output .= <<<EOF
       </div>  
</section>   


    <div id="big_logs" name="big_logs" class="zoom-anim-dialog mfp-hide">
        <div id="detail_logs" name"detail_logs" style="padding: 25px;">
        </div>
    </div>


   

<style>
  div#big_logs {
    background: #fff;
    width: 800px;
    margin: 0 auto;
    position: relative;
}

</style>
 
</div>
EOF;


        return $output;
    }

    /**
     * Subscription logs
     * @param string $log_key
     * @param string $log_key2
     * @return string
     * */
    public function subLogs($log_key = "", $log_key2 = "")
    {
        global $CMS, $DB, $member;
        $output = "";
        $sql = "SELECT * FROM " . root_table . "subscription_logs".($log_key?" WHERE logs_key='{$log_key}' ":"").($log_key2 ? "OR logs_key='{$log_key2}'" : "")." ORDER BY sublog_request_time DESC";

        $sql_query = $DB->query($sql);
        $total_rows = $DB->num_rows($sql_query);

        list($CMS->show_page, $sql) = $CMS->class->page->create($sql, 10);

        if ($DB->num_rows($sql) == 0) {
            return false;
        }

        $output .= <<<EOF
<div class="container-fluid">
<section class="add_table">

EOF;

            $output .= <<<EOF
 <h4 class="heading" style="font-size: 16px;cursor:pointer" id="expand_logs" active="1"  ><i class="fa fa-caret-right" style="cursor:pointer;margin-right: 10px;"></i><span style="font-family: robob;">{$CMS->lang['sublog_header']}</span></h4>
EOF;


        $output .= <<<EOF

    <div  id="expand_logs_container">
        <section class="activity-line">
        
EOF;

        while ($data = $DB->fetch_assoc($sql)) {
            $detail = "";
            $data['log_ftime'] = $CMS->class->date->date_format($data['sublog_request_time'], 1);
            $data['log_time'] = $CMS->class->date->date_format($data['sublog_request_time'], 1);

            $request = @json_decode($data['sublog_request'],1 );
            $response = @json_decode($data['sublog_response'],1);

            if($data['sublog_gateway'] == 'paypal')
            {
                $amount = $response['transactions']['0']['amount']['total'] ? ' '.$response['transactions']['0']['amount']['total'].' '.$response['transactions']['0']['amount']['currency'] : '';
                $ref = $response['id'] ? ". REF: <strong>{$response['id']}</strong>" : "";

                $data['log_name'] = "Payment {$amount} via paypal{$ref}";
            }
            else if($data['sublog_gateway'] == 'ngan_luong')
            {
                $amount = ' '.$CMS->class->input->currency($request['total_amount']);
                $bank = $request['bank_code'] ? " (bank {$request['bank_code']})" : "";
                $ref = $request['order_code'] ? " .REF: <strong>{$request['order_code']}</strong>" : "";
                $data['log_name'] = "Payment{$amount}{$bank} via Ngân Lượng{$ref}";
            }else if($data['sublog_gateway'] == 'bao_kim')
            {
                $transaction_id = $request['transaction_id'] ? "for transaction #".$request['transaction_id'] : "";
                $data['log_name'] = "Payment {$transaction_id} via Bảo Kim";
            }

            if ($data['log_content']) {
                $detail = "<span onclick='show_popup_detail({$data['log_id']});' log_id='{$data['log_id']}'><a>[{$CMS->lang['btn_detail']}]</a></span>";
            }


            $output .= <<<EOF

        <article class="activity-line-item box-typical" style="   border-radius: 0px !important;">
            <div class="activity-line-date">
                        {$data['log_time']}<br/>
                       
             </div>
            <header class="activity-line-item-header">
                <div class="activity-line-item-user">
                    <div class="activity-line-item-user-status">{$data['log_name']} {$detail}</div>
                </div>
            </header>

        </article><!-- activity-line-item box-typical-->
EOF;
        }


        $output .= <<<EOF
    </section>  <!-- activity-line-->

EOF;
        if($total_rows > 10)
        {
            $output .= <<<EOF

           <div class="activity-line-more" style="text-align:center">
                            <a onclick="seemore_logs('{$log_key}')">Xem thêm</a>
           </div>    

EOF;
        }
        $output .= <<<EOF
       </div>  
</section>   


    <div id="big_logs" name="big_logs" class="zoom-anim-dialog mfp-hide">
        <div id="detail_logs" name"detail_logs" style="padding: 25px;">
        </div>
    </div>
<style>
  div#big_logs {
    background: #fff;
    width: 800px;
    margin: 0 auto;
    position: relative;
}

</style>
 
</div>
EOF;


        return $output;
    }


    public function redirectReferer($url = '')
    {
        global $CMS;

        //$referer=strlen($url)>0?$url:$_SESSION['referer'];
        $referer = strlen($url) > 0 ? $url : $_SERVER['HTTP_REFERER'];

        if (isset($_SESSION[__FUNCTION__ . 'time'])) {
            $last_time = $_SESSION[__FUNCTION__ . 'time'] + intval(\lib\input::arrayValue($CMS->vars, 'redirect_delay'));
            $url = time() > $last_time ? $referer : $CMS->vars['root_domain'];
        } else {
            $url = $referer;
        }

        $_SESSION[__FUNCTION__ . 'time'] = time();

        $CMS->global->redirect($url);
    }



    //===========================================================================
    //  CLEAN ALL GUI CACHED FILES
    //===========================================================================
    
    public function clean_cache()
    {
        global $CMS, $DB;

    }
    
    //===========================================================================
    //  HTML CONVERT
    //===========================================================================
        
    public function convert( $text )
    {
        global $CMS, $DB, $member;

        $setting = $CMS->vars;

        $newarray = array();

        // Convert to array
        $setting = array_merge( $setting, $newarray );

        // Merger settings
        $data = array();
        $data = array_merge( $data, $setting );
        
        // Merge HTML
        $data = array_merge( $data, $CMS->global->html );
        
        //---------------------------------------------------------------
        // Rebuild data
        //---------------------------------------------------------------
        
        $text = preg_replace("/\[([a-zA-Z0-9\_]+?)\]/i", "\$data[\\1]", $text);
        
        // Remove slash
        $text = str_replace("\"", "\\\"", $text);
        
        eval("\$text = \"$text\";");

        return $text;
    }
    
    
    public function htmlTableProduct($data=array(), $type = 0, $sub_type=1, $title_html = "")
    {
        global $CMS;

        $disabledDiscount = 0;

        if( !in_array($CMS->input['site'],['transactions', 'order']) ||  ($CMS->input['site']=='transactions' && $CMS->input['type']!=1) )
        {
            $disabledDiscount = 1;
        }

        $CMS->class->language->load("store_request");
        $CMS->class->language->load("supplier");
        $data_product = $this->html_product_tr($data, $sub_type, $disabledDiscount);

        $hiddenTax = !($CMS->input['site'] == 'transactions' && $CMS->input['type'] != 1) ? "" : "display: none";

        $footerColspan = !($CMS->input['site'] == 'transactions' && $CMS->input['type'] != 1) ? 8-$disabledDiscount : 7-$disabledDiscount;

        // $sub_type: Dùng để check bỏ cột cycle
        if($sub_type == 1)
        {
            $out_th = "<th width='5%'>{$CMS->lang['table_cycle']}</th>";
        }else
        {
            $out_th = "<th width='15%'>{$CMS->lang['title_product_supplier']}</th>";
        }
        $output .=<<<EOF
        <section class="add_table">
            <div style="clear:both;overflow: hidden;">
                <h4 class="heading pull-left"><i class="fa fa-caret-down"></i><span>{$CMS->lang['gproduct_header']}</span></h4>
             
             </div>   
            <div class="table_cus">
                <div class="table-responsive" style="overflow-x: initial;">
                    <input type="hidden" id="keyrow_active" value=""/>
                    <input type="hidden" id="product_type_xxx" value="{$type}"/>

                    <table id="tblProduct">
                        <thead>
                            <tr>
                                <th width="2%">#</th>
                                <th width="15%">{$CMS->lang['gproduct_items']}</th>
                                <th width="15%">{$CMS->lang['table_description']}</th>
                                {$out_th}
                                <th width="7%">{$CMS->lang['table_quantity']}</th>
                                <th width="10%">{$CMS->lang['table_price']}</th>
                                <th width="10%" style="{$hiddenTax}">{$CMS->lang['table_tax']}</th>
EOF;

        if(!$disabledDiscount)
        {
            $output .= <<<EOF
                                <th width="20%">{$CMS->lang['table_discount_value']}</th>
EOF;
        }


                            $output .= <<<EOF
                                <th width="15%">{$CMS->lang['table_amount']}</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tbody id="data_table">
                            {$data_product}
                        </tbody>
                            <tr>
                                <td id="tableTotalProduct" colspan="{$footerColspan}" style="text-align: right; font-weight: bold;">{$CMS->lang['table_total']}</td>
                                <td colspan="1" style="text-align: left; font-weight: bold;float:left"><span id="total_show">0</span></td>
                                <td></td>
                            </tr>
                    </table>
                </div>
                <div class="box_line" style="margin-top: 10px">
                    <a class="btn btn_add_line">{$CMS->lang['title_add_line']}</a>
                    <a class="btn btn_del_line">{$CMS->lang['title_delete_line']}</a>
                </div>
            </div>
        </section>
EOF;

 
        return $output;     
    }

    public function htmlTableAsset($data=array(), $disableprice = 0, $title_html = "")
    {
        global $CMS;
        // $disableprice: Dùng để disable price, tax, amount, total
        if($CMS->vars['addon_goods_enable'] == 0)
        {
            return false;
        }
        $CMS->class->language->load("store_request");
        $CMS->class->language->load("supplier");
        $CMS->class->language->load("assets");

        $disabledDiscount = 0;
        if( !in_array($CMS->input['site'],['transactions', 'order']) ||  ($CMS->input['site']=='transactions' && $CMS->input['type']!=1) )
        {
            $disabledDiscount = 1;
        }

        $footerColspan = !($CMS->input['site'] == 'transactions' && $CMS->input['type'] != 1) ? 7-$disabledDiscount : 6-$disabledDiscount;

        $data_asset = $this->html_asset_tr($data, $disableprice, $disabledDiscount);
        // if($disableprice)
        // {
        //  $display = 'style="display: none;"';
        // }else
        // {
        //  $display = '';
        // }
        $output = <<<EOF
            <section class="add_table">
    
             <div style="clear:both;overflow: hidden;">
                <h4 class="heading pull-left"><i class="fa fa-caret-down"></i><span>{$CMS->lang['ggoods_header']}</span></h4>
               
             </div>   

            <div class="table_cus">
                <div class="table-responsive" style="overflow-x: initial;">
                    <input type="hidden" id="keyrow_active_asset" value=""/>
                    <table id="tblAsset">
                        <thead>
                            <tr>
                                <th width="2%">#</th>
                                <th width="15%">{$CMS->lang['ggoods_items']}</th>
                                <th width="15%">{$CMS->lang['ggoods_desc']}</th>
                                <th width="7%">{$CMS->lang['table_quantity']}</th>
                                <th width="10%">{$CMS->lang['table_price']}</th>
EOF;

        if(!($CMS->input['site'] == 'transactions' && $CMS->input['type'] != 1))
        {
            $output .= <<<EOF
                                <th width="10%">{$CMS->lang['table_tax']}</th>
EOF;
        }


        if(!$disabledDiscount)
        {
            $output .= <<<EOF
                                <th width="20%">{$CMS->lang['table_discount_value']}</th>
EOF;
        }


        $output .= <<<EOF
                                <th width="15%">{$CMS->lang['table_amount']}</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tbody id="data_table_asset">
                            {$data_asset}
                        </tbody>
                            <tr>
                                <td id="tableTotalAsset" colspan="{$footerColspan}" style="text-align: right; font-weight: bold;">{$CMS->lang['table_total']}</td>
                                <td colspan="1" style="text-align: left; font-weight: bold;float:left"><span id="total_show_asset">0</span></td>
                                 <td></td>
                            </tr>
                    </table>
                </div>
                <div class="box_line" style="margin-top: 10px">
                    <a class="btn btn_add_line_asset">{$CMS->lang['title_add_line']}</a>
                    <a class="btn btn_del_line_asset">{$CMS->lang['title_delete_line']}</a>
                </div>
            </div>
        </section>
EOF;


        return $output;
    }
    

    public function html_product_tr($data_product = array(), $sub_type = 1, $disabledDiscount = 0)
    {
        global $CMS;

        $hideCommission = $CMS->vars['enabled_commission'] && $CMS->input['site'] == 'order' ? "" : "display:none";
 
        // print "<pre>";
        // print_r($data_product);exit;
        $output = "";
        if($sub_type == 1)
        {
            $disable_select = " readonly ";
        }
        // Check module_site = order, transaction thi gan type = 1 de search ajax edit product

        if($CMS->input['site'] == "order" OR $CMS->input['site'] == "transaction")
        {
            $type_product_edit = " type_product_edit = '1' ";
        }


        if(is_array($data_product))
        {
            $count = count($data_product);
            $i = 1;
            $inc = 0;
            ksort($data_product);
            foreach ($data_product as $key => $value) 
            {
                $tax_fee = $value['product_tax'] ? $value['product_tax'] : "";
                unset($tax_selected);
                $tax_selected[intval($tax_fee)] = "selected";

                $discount_type = intval($value['product_discount_type']);
                unset($discount_type_selected);
                $discount_type_selected[$discount_type] = "selected";

                $quantity = $value['product_quantity'] ? $value['product_quantity'] : 1;
                $sup_name = $CMS->supplier->get_info($value['sup_id'], "supplier_name");
                $option = "";
                $k = 0;
                $disable_select = "";
                if($value['product_cycle'] == 0 )// Chu ky thanh toan 1 lan
                {
                    $disable_select = " readonly ";
                    $option .= "<option value='1' selected>{$CMS->lang['gonce']}</option>";
                }
                else
                {
                    $option_selected[$value['product_cycle_value']] = "selected";

                    if($value['product_cycle'] == 1 )// Chu ky thanh toan hangg thang
                    {


                        for($k = 1; $k <= 12; $k ++)
                        {
                            $option .= "<option value='{$k}' {$option_selected[$k]}>{$k} {$CMS->lang['gmonth']}</option>";
                        }
                    }
                    else if($value['product_cycle'] == 2 )// Chu ky thanh toan hang nam
                    {
                        for($k = 1; $k <= 12; $k ++)
                        {
                            $option .= "<option value='{$k}' {$option_selected[$k]}>{$k} {$CMS->lang['gyear']}</option>";
                        }
                        
                    }

                    unset($option_selected);

                }

                if($sub_type == 1)
                {
                    $out_th = <<<EOF

                    <td class="grid-td" for="dgrid-9" check="chtd">
                        <select  for="dgrid-9" name="product_cycle[]" check="chtd" class="form-control hidden_border"  value="1" onchange="calculate_money()"/>
                        {$option}
                        </select>
                    </td>

EOF;

                }else
                {
                    $out_th = <<<EOF

                    <td class="grid-td" for="dgrid-10" check="chtd">
                        <input type="text" readonly="readonly" for="dgrid-10" name="sup_name[]" check="chtd" class="form-control hidden_border"  value="{$sup_name}" />
                        <input type="hidden" name="sup_id[]" value="{$value['sup_id']}" />
                    </td>
EOF;
                }

                $output .=<<<EOF
                        <tr keyrow="{$i}" class="row-grid" rowtr="" >
                            <td class="grid-td" for="dgrid-1" check="chtd"><span class="number">#{$i}</span></td>
                            <td class="grid-td" for="dgrid-2" check="chtd">
                                <div class="td_dropdown_btn">
                                <input type="text" for="dgrid-2" check="chtd" name="product_name[]" class="form-control search_product hidden_border" value="{$value['product_name']}" autocomplete="off"/>
                                <input type="hidden" class="product_id" name="product_id[]" value="{$value['product_id']}"/>
                                <input type="hidden" class="product_type" name="product_type[]" value="{$value['product_type']}"/>
                                <input type="hidden" class="item_id" name="item_id[]" value="{$inc}"/>
                                <input type="hidden" class="product_cycle_type" name="product_cycle_type[]" value="{$value['product_cycle']}"/>
                                <input type="hidden" class="product_type" name="product_type[]" value="{$value['product_type']}"/>
                                <span class="dropdown_btn" style="display: none;" for="dgrid-2" check="chspan"><i class="fa fa-arrow-down" aria-hidden="true" for="dgrid-2" check="chspan"></i></span>
                                <div class="box_result_search"></div>
                                </div>
                            </td>

                            <td class="grid-td" for="dgrid-3" check="chtd"><textarea for="dgrid-3" name="product_description[]" check="chtd" class="form-control hidden_border" style="resize: none;" rows="3">{$value['product_description']}</textarea></td>
                            {$out_th}
                            <td class="grid-td" for="dgrid-4" check="chtd"><input type="text" for="dgrid-4" name="product_quantity[]" check="chtd" class="form-control hidden_border quan_list" onkeypress="return check_enter_number(event,this);" value="{$quantity}" onkeyup="calculate_money()" onfocusout="check_enter_number_2(this,1);"/></td>

                            <td class="grid-td" for="dgrid-5" check="chtd">
                                <input type="hidden" name="product_old_price[]" value="{$value['product_old_price']}">
                                <input type="text" for="dgrid-5" name="product_price[]" value="{$value['product_price']}" check="chtd" onkeypress="return check_enter_number(event,this);" onkeyup="changePriceItem($(this))" class="form-control hidden_border" onfocusout="check_enter_number_2(this,0);"/>
                                <br>
                                <input type="hidden" name="product_commission_type[]" value="{$value['product_commission_type']}">
                                <input type="hidden" name="product_commission_value[]" value="{$value['product_commission_value']}">
                                <span style="{$hideCommission}; cursor: pointer" title="{$CMS->lang['update_commission']}" class="label label-success" onclick="customCommissionItem($(this))">{$CMS->lang['gcommission']}: <span class="commission_show">{$CMS->product->commission_format($value['product_commission_value'], $value['product_commission_type'])}</span></span>

                                <input type="hidden" name="var_id[]" value="{$value['var_id']}">
                            </td>
EOF;
                if( !($CMS->input['site'] == 'transactions' && $CMS->input['type'] != 1) )
                {
                    $output .= <<<EOF
                            <td class="grid-td" for="dgrid-7" check="chtd">
                                <select for="dgrid-7" name="product_tax[]" defaultvalue="{$tax_fee}" class="form-control hidden_border auto_select" onchange="calculate_money()" check="chtd">
                                        <option {$tax_selected[0]} value="0">0%</option>
                                        <option {$tax_selected[10]} value="10">10%</option>
                                </select>   
                            </td>
EOF;
                }

                if(!$disabledDiscount)
                {
                    $output .= <<<EOF
                            <td class="grid-td" for="dgrid-11" check="chtd">
                                <input type="number" name="product_discount_value[]" check="chtd" class="form-control hidden_border" style="width:60%; display: inline-block" value="{$value['product_discount_value']}" onchange="changeDiscountValueItem($(this))"/>
                                <select name="product_discount_type[]" class="form-control hidden_border" check="chtd" style="width:35%; display: inline-block" onchange="changePriceItem($(this));">
                                    <option {$discount_type_selected[0]} value="0">%</option>
                                    <option {$discount_type_selected[1]} value="1">{$CMS->vars['currency_type']}</option>
                                </select>
                            </td>
EOF;
                }

                $output .= <<<EOF
                            <td class="grid-td" for="dgrid-6" check="chtd">
                            <input type="text" for="dgrid-6" name="product_amount[]" value="{$value['product_amount']}" check="chtd" class="form-control hidden_border total_amount" disabled/>
                            </td>

                            <td class="trash" for="dgrid-8">
                                <span class="edit_row_product" id="{$value['product_id']}" item_id='{$inc}' {$type_product_edit} ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                                <span class="clone_row_invoid" id="{$value['product_id']}"><i class="fa fa-clone" aria-hidden="true"></i></span>
                                <span class="del_row_invoid" item_id='{$inc}'><i class="fa fa-trash" aria-hidden="true"></i></span>
                            </td>
                        </tr>
EOF;

                $i++;
                $inc ++;
            }
                $option = "";
                 
                 $option .= "<option value='1' selected>{$CMS->lang['gonce']}</option>";

            if($sub_type == 1)
            {

                $out_th = <<<EOF

                <td class="grid-td" for="dgrid-9" check="chtd">
                    <select  for="dgrid-9" name="product_cycle[]" check="chtd" class="form-control hidden_border"  value="1" onchange="calculate_money()"/>
                    {$option}
                    </select>
                </td>
EOF;

            }else
            { 
                $out_th = <<<EOF

                    <td class="grid-td" for="dgrid-10" check="chtd">
                        <input type="text" readonly="readonly" for="dgrid-10" name="sup_name[]" check="chtd" class="form-control hidden_border"  value="" >
                        <input type="hidden" name="sup_id[]" value="" />

                    </td>
EOF;

            }

            $output .=<<<EOF
                    <tr keyrow="{$i}" class="row-grid" rowtr="last-row"  >
                        <td class="grid-td" for="dgrid-1" check="chtd"><span class="number">#{$i}</span></td>
                        <td class="grid-td" for="dgrid-2" check="chtd">
                            <div class="td_dropdown_btn">
                                <input type="text" for="dgrid-2" check="chtd" name="product_name[]" class="form-control search_product hidden_border" autocomplete="off"  />
                                <input type="hidden" class="product_id" name="product_id[]" value=""/>
                                <input type="hidden" class="item_id" name="item_id[]" value="{$inc}"/>
                                <input type="hidden" class="product_cycle_type" name="product_cycle_type[]" value="0"/>
                                <input type="hidden" class="product_type" name="product_type[]" value="0"/>

                                <span class="dropdown_btn" style="display: none;" for="dgrid-2" check="chspan"><i class="fa fa-arrow-down" aria-hidden="true" for="dgrid-2" check="chspan"></i></span>
                                <div class="box_result_search"></div>
                            </div>
                        </td>

                        <td class="grid-td" for="dgrid-3" check="chtd"><textarea for="dgrid-3" name="product_description[]" check="chtd" class="form-control hidden_border" style="resize: none;"></textarea></td>
                        
                         {$out_th}
                        <td class="grid-td" for="dgrid-4" check="chtd"><input type="text" for="dgrid-4" name="product_quantity[]" check="chtd" value="1" class="form-control hidden_border quan_list" onkeypress="return check_enter_number(event,this);"  onkeyup="calculate_money()" onfocusout="check_enter_number_2(this,1);"/></td>

                        <td class="grid-td" for="dgrid-5" check="chtd">
                            <input type="hidden" name="product_old_price[]" value="0">
                            <input type="text" for="dgrid-5" name="product_price[]" check="chtd" onkeypress="return check_enter_number(event,this);" onkeyup="changePriceItem($(this))" class="form-control hidden_border" onfocusout="check_enter_number_2(this,0);" value="0"/>
                            <br>
                            <input type="hidden" name="product_commission_type[]" value="0">
                            <input type="hidden" name="product_commission_value[]" value="0">
                            <span style="{$hideCommission}; cursor: pointer" title="{$CMS->lang['update_commission']}" class="label label-success" onclick="customCommissionItem($(this))">{$CMS->lang['gcommission']}: <span class="commission_show">0%</span></span>

                            <input type="hidden" name="var_id[]" value="0">
                        </td>
EOF;

            if( !($CMS->input['site'] == 'transactions' && $CMS->input['type'] != 1) )
            {
                $output .= <<<EOF
                        <td class="grid-td" for="dgrid-7" check="chtd">
                                <select for="dgrid-7" name="product_tax[]" class="form-control hidden_border tax_list" onchange="calculate_money()" check="chtd">
                                        <option value="0">0%</option>
                                        <option value="10" selected="selected">10%</option>
                                </select>
                        </td>
EOF;
            }

            if(!$disabledDiscount)
            {
                $output .= <<<EOF
                        <td class="grid-td" for="dgrid-11" check="chtd">
                            <input type="number" name="product_discount_value[]" check="chtd" class="form-control hidden_border" style="width:60%; display: inline-block" value="0" onchange="changeDiscountValueItem($(this))"/>
                            <select name="product_discount_type[]" class="form-control hidden_border" check="chtd" style="width:35%; display: inline-block" onchange="changePriceItem($(this))">
                                <option value="0">%</option>
                                <option value="1">{$CMS->vars['currency_type']}</option>
                            </select>
                        </td>
EOF;
            }


            $output .= <<<EOF
                        <td class="grid-td" for="dgrid-6" check="chtd">
                            <input type="text" for="dgrid-6" name="product_amount[]" check="chtd" class="form-control hidden_border total_amount" disabled/>
                        </td>
                        <td class="trash" for="dgrid-8">
                            <span class="clone_row_invoid"><i class="fa fa-clone" aria-hidden="true"></i></span>
                            <span class="del_row_invoid" item_id='{$inc}'><i class="fa fa-trash" aria-hidden="true"></i></span>
                        </td>
                    </tr>
EOF;

        }else
        {
            $option = "";
                 
            $option .= "<option value='1'>{$CMS->lang['gonce']}</option>";
            if($sub_type == 1)
            {
                $out_th = <<<EOF

                <td class="grid-td" for="dgrid-9" check="chtd">
                    <select  for="dgrid-9" name="product_cycle[]" check="chtd" class="form-control hidden_border"  value="1" onchange="calculate_money()"/>
                    {$option}
                    </select>
                </td>
EOF;

            }else
            {
                $out_th = <<<EOF

                    <td class="grid-td" for="dgrid-10" check="chtd">
                        <input type="text" readonly="readonly" for="dgrid-10" name="sup_name[]" check="chtd" class="form-control hidden_border"  value="" >
                        <input type="hidden" name="sup_id[]" value="" />
                    </td>
EOF;

            }

            $output .=<<<EOF
                    <tr class="row-grid" rowtr="last-row"  >
                        <td class="grid-td" for="dgrid-1" check="chtd"><span class="number">#1</span></td>
                        <td class="grid-td" for="dgrid-2">
                            <div class="td_dropdown_btn">
                                <input type="text" for="dgrid-2" check="chtd" name="product_name[]" class="form-control search_product hidden_border" autocomplete="off" />
                                <input type="hidden" class="product_id" name="product_id[]" value=""/>
                                <input type="hidden" class="item_id" name="item_id[]" value="0"/>
                                <input type="hidden" class="product_cycle_type" name="product_cycle_type[]" value="0"/>
                                <input type="hidden" class="product_type" name="product_type[]" value="0"/>

                                <span class="dropdown_btn" style="display: none;" for="dgrid-2" check="chspan"><i class="fa fa-arrow-down" aria-hidden="true" for="dgrid-2" check="chspan"></i></span>
                                <div class="box_result_search"></div>
                            </div>
                        </td>

                        <td class="grid-td" for="dgrid-3" check="chtd"><textarea for="dgrid-3" name="product_description[]" check="chtd" class="form-control hidden_border" style="resize: none;"></textarea></td>
                        {$out_th}
                        <td class="grid-td" for="dgrid-4" check="chtd"><input type="text" for="dgrid-4" name="product_quantity[]" check="chtd" value="1" class="form-control hidden_border quan_list" onkeypress="return check_enter_number(event,this);"  onkeyup="calculate_money()" onfocusout="check_enter_number_2(this,1);"/></td>

                        <td class="grid-td" for="dgrid-5" check="chtd">
                            <input type="hidden" name="product_old_price[]" value="0">
                            <input type="text" for="dgrid-5" name="product_price[]" check="chtd" onkeypress="return check_enter_number(event,this);" onkeyup="changePriceItem($(this))" class="form-control hidden_border" onfocusout="check_enter_number_2(this,0);" value="0"/>
                            <br>
                            <input type="hidden" name="product_commission_type[]" value="0">
                            <input type="hidden" name="product_commission_value[]" value="0">
                            <span style="{$hideCommission}; cursor: pointer" title="{$CMS->lang['update_commission']}" class="label label-success" onclick="customCommissionItem($(this))">{$CMS->lang['gcommission']}: <span class="commission_show">0%</span></span>

                            <input type="hidden" name="var_id[]" value="0">
                        </td>
EOF;

            if( !($CMS->input['site'] == 'transactions' && $CMS->input['type'] != 1) )
        {
            $output .= <<<EOF
                <td class="grid-td" for="dgrid-7" check="chtd">
                        <select for="dgrid-7" name="product_tax[]" class="form-control hidden_border tax_list" onchange="calculate_money()" check="chtd">
                                <option value="0">0%</option>
                                <option value="10" selected="selected">10%</option>
                        </select>
                </td>   
EOF;
        }

            if(!$disabledDiscount)
            {
                $output .= <<<EOF
                        <td class="grid-td" for="dgrid-11" check="chtd">
                            <input type="number" name="product_discount_value[]" check="chtd" class="form-control hidden_border" style="width:60%; display: inline-block" value="0" onchange="changeDiscountValueItem($(this))"/>
                            <select name="product_discount_type[]" class="form-control hidden_border" check="chtd" style="width:35%; display: inline-block" onchange="changePriceItem($(this))">
                                <option value="0">%</option>
                                <option value="1">{$CMS->vars['currency_type']}</option>
                            </select>
                        </td>
EOF;
            }


            $output .= <<<EOF
                        <td class="grid-td" for="dgrid-6" check="chtd">
                            <input type="text" for="dgrid-6" name="product_amount[]" check="chtd" class="form-control hidden_border total_amount" disabled/>
                        </td>
                        <td class="trash" for="dgrid-8"><span class="del_row_invoid" item_id='0'><i class="fa fa-trash" aria-hidden="true"></i></span></td>
                    </tr>
EOF;

        }

        return $output;

    }

    public function html_asset_tr($data_asset = array(), $disableprice = 0, $disabledDiscount=0)
    {
        global $CMS;
//        print_r($data_asset); exit;

        $output = "";
        if(is_array($data_asset))
        {
            $count = count($data_asset);
            $i = 1;
            $inc = 0;
            ksort($data_asset);

            foreach ($data_asset as $key => $value)
            {
                // print "<pre>";
                // print_r($value);exit;
                $tax_fee = $value['ass_tax'] ? $value['ass_tax'] : "";
                $quantity = $value['ass_quantity'] ? $value['ass_quantity'] : 1;
                $disable = "";

                $discount_type = intval($value['ass_discount_type']);
                unset($discount_type_selected);
                $discount_type_selected[$discount_type] = "selected";

                if($disableprice)
                {
                    $disable = " disabled='disabled' ";
                    
                    if($disableprice == 2)
                    {
                        $disable_all = " disabled='disabled' ";
                        $btn_search = "";
                    }else
                    {
                        $disable_all = "";
                        $btn_search =<<<EOF
                            <span class="dropdown_btn" style="display: none;" for="dgrid-2" check="chspan" ><i class="fa fa-arrow-down" aria-hidden="true" for="dgrid-2" check="chspan"></i></span>
                            <div class="box_result_search"></div>

EOF;
                        $btn_action =<<<EOF

                        <span class="edit_row_product" id="{$value['ass_id']}" item_id='{$inc}'><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                        <span class="clone_row_invoid"><i class="fa fa-clone" aria-hidden="true"></i></span>
                        <span class="del_row_invoid" item_id='{$inc}'><i class="fa fa-trash" aria-hidden="true"></i></span>

EOF;
                    }
                    $tax_fee = "";
                    $value['ass_amount'] = "";
                    $value['ass_price'] = "";
                }else
                {
                    $btn_search =<<<EOF
                            <span class="dropdown_btn" style="display: none;" for="dgrid-2" check="chspan" ><i class="fa fa-arrow-down" aria-hidden="true" for="dgrid-2" check="chspan"></i></span>
                            <div class="box_result_search"></div>

EOF;
                    $btn_action =<<<EOF

                        <span class="edit_row_product" id="{$value['ass_id']}" item_id='{$inc}'><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                        <span class="clone_row_invoid"><i class="fa fa-clone" aria-hidden="true"></i></span>
                        <span class="del_row_invoid" item_id='{$inc}'><i class="fa fa-trash" aria-hidden="true"></i></span>

EOF;

                }

                unset($optionSeleted);
                $optionSeleted[intval($tax_fee)] = "selected";

                $output .=<<<EOF
                        <tr keyrow="{$i}" class="row-grid-asset" rowtr="">
                            <td class="grid-td-asset" for="dgrid-1" check="chtd"><span class="number">#{$i}</span></td>
                            <td class="grid-td-asset" for="dgrid-2" check="chtd">
                                <div class="td_dropdown_btn">
                                <input type="text" for="dgrid-2" check="chtd" name="ass_name[]" class="form-control search_asset hidden_border" value="{$value['ass_name']}" autocomplete="off"/>
                                <input type="hidden" class="ass_id" name="ass_id[]" value="{$value['ass_key']}"/>
                                <input type="hidden" class="ass_key" name="ass_key[]" value="{$value['ass_key']}"/>
                                <input type="hidden" class="item_id" name="item_id[]" value="{$inc}"/>
                                {$btn_search}
                                </div>
                            </td>

                            <td class="grid-td-asset" for="dgrid-3" check="chtd"><textarea for="dgrid-3" name="ass_code[]" check="chtd" class="form-control hidden_border" style="resize: none;" rows="3">{$value['ass_code']}</textarea></td>

                            <td class="grid-td-asset" for="dgrid-4" check="chtd"><input type="text" for="dgrid-4" name="ass_quantity[]" check="chtd" class="form-control hidden_border quan_list" onkeypress="return check_enter_number(event, this);" value="{$quantity}" onkeyup="calculate_money_asset()" onfocusout="check_enter_number_2(this,1);"/></td>

                            <td class="grid-td-asset" for="dgrid-5" check="chtd">
                                <input type="hidden" name="ass_old_price[]" value="{$value['ass_old_price']}" class="">
                                <input type="text" for="dgrid-5" name="ass_price[]" value="{$value['ass_price']}" check="chtd" onkeypress="return check_enter_number(event, this);" onkeyup="calculate_money_asset()" class="form-control hidden_border" onfocusout="check_enter_number_2(this,0);"/>
                            </td>
EOF;
                if( !($CMS->input['site'] == 'transactions' && $CMS->input['type'] != 1) )
                {
                    $output .= <<<EOF
                            <td class="grid-td-asset" for="dgrid-7" check="chtd">
                                <select for="dgrid-7" name="ass_tax[]" defaultvalue="{$tax_fee}"  class="form-control hidden_border auto_select" onchange="calculate_money_asset()" check="chtd">
                                        <option value="0" {$optionSeleted[0]}>0%</option>
                                        <option value="10" {$optionSeleted[10]}>10%</option>
                                </select>   
                            </td>
EOF;
                }


                if(!$disabledDiscount)
                {
                    $output .= <<<EOF
                            <td class="grid-td-asset" for="dgrid-11" check="chtd">
                                <input type="number" name="ass_discount_value[]" check="chtd" class="form-control hidden_border" style="width:60%; display: inline-block" value="{$value['ass_discount_value']}" onchange="calculate_money_asset();"/>
                                <select name="ass_discount_type[]" class="form-control hidden_border" check="chtd" style="width:35%; display: inline-block" onchange="calculate_money_asset();">
                                    <option {$discount_type_selected[0]} value="0">%</option>
                                    <option {$discount_type_selected[1]} value="1">{$CMS->vars['currency_type']}</option>
                                </select>
                            </td>
EOF;
                }


                $output .= <<<EOF
                            <td class="grid-td-asset" for="dgrid-6" check="chtd"><input type="text" for="dgrid-6" name="ass_amount[]" value="{$value['ass_amount']}" check="chtd" class="form-control hidden_border total_amount_asset" disabled="disabled"/></td>

                            <td class="trash" for="dgrid-8">
                                <span class="edit_row_asset" id="{$value['ass_key']}" item_id='{$inc}'><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                                <span class="clone_row_invoid"><i class="fa fa-clone" aria-hidden="true"></i></span>
                                <span class="del_row_invoid" item_id='{$inc}'><i class="fa fa-trash" aria-hidden="true"></i></span>
                            </td>
                        </tr>
EOF;

                $i++;
                $inc ++;
            }
                $disable = "";
                $selected = ' selected="selected" ';
                if($disableprice)
                {
                    $disable = " disabled='disabled' ";
                    $selected = '';
                }

            // if($disableprice != 2)
            // {
            $output .=<<<EOF
                    <tr keyrow="{$i}" class="row-grid-asset" rowtr="last-row">
                        <td class="grid-td-asset" for="dgrid-1" check="chtd"><span class="number">#{$i}</span></td>
                        <td class="grid-td-asset" for="dgrid-2" check="chtd">
                            <div class="td_dropdown_btn">
                                <input type="text" for="dgrid-2" check="chtd" name="ass_name[]" class="form-control search_asset hidden_border" autocomplete="off"/>
                                <input type="hidden" class="ass_id" name="ass_id[]" value=""/>
                                <input type="hidden" class="ass_key" name="ass_key[]" value=""/>
                                <input type="hidden" class="item_id" name="item_id[]" value="{$inc}"/>
                                <span class="dropdown_btn" style="display: none;" for="dgrid-2" check="chspan"><i class="fa fa-arrow-down" aria-hidden="true" for="dgrid-2" check="chspan"></i></span>
                                <div class="box_result_search"></div>
                            </div>
                        </td>

                        <td class="grid-td-asset" for="dgrid-3" check="chtd"><textarea for="dgrid-3" name="ass_code[]" check="chtd" class="form-control hidden_border" style="resize: none;"></textarea></td>

                        <td class="grid-td-asset" for="dgrid-4" check="chtd"><input type="text" for="dgrid-4" name="ass_quantity[]" check="chtd" value="1" class="form-control hidden_border quan_list" onkeypress="return check_enter_number(event, this);"  onkeyup="calculate_money_asset()" onfocusout="check_enter_number_2(this,1);"/></td>

                        <td class="grid-td-asset" for="dgrid-5" check="chtd">
                            <input type="hidden" name="ass_old_price[]" value="" class="">
                            <input type="text" for="dgrid-5" name="ass_price[]" check="chtd" onkeypress="return check_enter_number(event, this);" onkeyup="calculate_money_asset()" class="form-control hidden_border" onfocusout="check_enter_number_2(this,0);" />
                        </td>
EOF;

            if( !($CMS->input['site'] == 'transactions' && $CMS->input['type'] != 1) )
                {
                    $output .=<<<EOF
                        <td class="grid-td-asset" for="dgrid-7" check="chtd">
                                <select for="dgrid-7" name="ass_tax[]" class="form-control hidden_border tax_list" onchange="calculate_money_asset()" check="chtd" >
                                        <option value="0">0%</option>
                                        <option value="10" {$selected}>10%</option>
                                </select>
                        </td>
EOF;
                }

            if(!$disabledDiscount)
            {
                $output .= <<<EOF
                        <td class="grid-td-asset" for="dgrid-11" check="chtd">
                            <input type="number" name="ass_discount_value[]" check="chtd" class="form-control hidden_border" style="width:60%; display: inline-block" value="0" onchange="calculate_money_asset();"/>
                            <select name="ass_discount_type[]" class="form-control hidden_border" check="chtd" style="width:35%; display: inline-block" onchange="calculate_money_asset();">
                                <option value="0">%</option>
                                <option value="1">{$CMS->vars['currency_type']}</option>
                            </select>
                        </td>
EOF;
            }


                $output .= <<<EOF
                        <td class="grid-td-asset" for="dgrid-6" check="chtd"><input type="text" for="dgrid-6" name="ass_amount[]" check="chtd" class="form-control hidden_border total_amount_asset" disabled="disabled"/></td>

                        <td class="trash" for="dgrid-8">
                            <span class="clone_row_invoid"><i class="fa fa-clone" aria-hidden="true"></i></span>
                            <span class="del_row_invoid" item_id='{$inc}'><i class="fa fa-trash" aria-hidden="true"></i></span>
                        </td>
                    </tr>
EOF;
            // }

        }else
        {
            $disable = "";
            $selected = ' selected="selected" ';
            if($disableprice)
            {
                $disable = " disabled='disabled' ";
                $selected = '';
            }
            // if($disableprice != 2)
            // {
            $output .=<<<EOF
                    <tr class="row-grid-asset" rowtr="last-row">
                        <td class="grid-td-asset" for="dgrid-1" check="chtd"><span class="number">#1</span></td>
                        <td class="grid-td-asset" for="dgrid-2">
                            <div class="td_dropdown_btn">
                                <input type="text" for="dgrid-2" check="chtd" name="ass_name[]" class="form-control search_asset hidden_border" autocomplete="off"/>
                                <input type="hidden" class="ass_id" name="ass_id[]" value=""/>
                                <input type="hidden" class="ass_key" name="ass_key[]" value=""/>
                                <input type="hidden" class="item_id" name="item_id[]" value="0"/>
                                <span class="dropdown_btn" style="display: none;" for="dgrid-2" check="chspan"><i class="fa fa-arrow-down" aria-hidden="true" for="dgrid-2" check="chspan"></i></span>
                                <div class="box_result_search"></div>
                            </div>
                        </td>

                        <td class="grid-td-asset" for="dgrid-3" check="chtd"><textarea for="dgrid-3" name="ass_code[]" check="chtd" class="form-control hidden_border" style="resize: none;"></textarea></td>

                        <td class="grid-td-asset" for="dgrid-4" check="chtd"><input type="text" for="dgrid-4" name="ass_quantity[]" check="chtd" value="1" class="form-control hidden_border quan_list" onkeypress="return check_enter_number(event, this);"  onkeyup="calculate_money_asset()" onfocusout="check_enter_number_2(this,1);"/></td>

                        <td class="grid-td-asset" for="dgrid-5" check="chtd">
                            <input type="hidden" name="ass_old_price[]" value="" class="">
                            <input type="text" for="dgrid-5" name="ass_price[]" check="chtd" onkeypress="return check_enter_number(event, this);" onkeyup="calculate_money_asset()" class="form-control hidden_border" onfocusout="check_enter_number_2(this,0);"/>
                        </td>
EOF;

            if( !($CMS->input['site'] == 'transactions' && $CMS->input['type'] != 1) )
            {
                $output .= <<<EOF
                <td class="grid-td-asset" for="dgrid-7" check="chtd">
                        <select for="dgrid-7" name="ass_tax[]" class="form-control hidden_border tax_list" onchange="calculate_money_asset()" check="chtd" >
                                <option value="0">0%</option>
                                <option value="10" {$selected}>10%</option>
                        </select>
                </td>
EOF;
            }


            if(!$disabledDiscount)
            {
                $output .= <<<EOF
                        <td class="grid-td-asset" for="dgrid-11" check="chtd">
                            <input type="number" name="ass_discount_value[]" check="chtd" class="form-control hidden_border" style="width:60%; display: inline-block" value="0" onchange="calculate_money_asset();"/>
                            <select name="ass_discount_type[]" class="form-control hidden_border" check="chtd" style="width:35%; display: inline-block" onchange="calculate_money_asset();">
                                <option value="0">%</option>
                                <option value="1">{$CMS->vars['currency_type']}</option>
                            </select>
                        </td>
EOF;
            }


            $output .= <<<EOF
                        <td class="grid-td-asset" for="dgrid-6" check="chtd"><input type="text" for="dgrid-6" name="ass_amount[]" check="chtd" class="form-control hidden_border total_amount_asset" disabled="disabled"/></td>

                        

                        <td class="trash" for="dgrid-8">
                            <span class="clone_row_invoid"><i class="fa fa-clone" aria-hidden="true"></i></span>
                            <span class="del_row_invoid" item_id='0'><i class="fa fa-trash" aria-hidden="true"></i></span>
                        </td>
                    </tr>
EOF;
            // }

        }

        return $output;

    }
    
    
    public function fullFormHtml()
    {
        global $CMS;

        $CMS->class->language->load("store_request");
        $CMS->class->language->load("supplier");
        $CMS->class->language->load("assets");
        $output =<<<EOF
        <script>
            var lang_supplier_add = "{$CMS->lang['supplier_add']}";
            var lang_supplier_edit = "{$CMS->lang['supplier_edit']}";

            var lang_shipment_add = "{$CMS->lang['title_shipment_add']}";
            var lang_shipment_edit = "{$CMS->lang['title_shipment_edit']}";

            var lang_manufacture_add = "{$CMS->lang['title_add_manufacture']}";
            var lang_manufacture_edit = "{$CMS->lang['title_edit_manufacture']}";

            var lang_pg_add = "{$CMS->lang['title_add_ass_group']}";
            var lang_pg_edit = "{$CMS->lang['title_edit_product_group']}";

            var lang_product_add = "{$CMS->lang['title_add_product']}";
            var lang_service_add = "{$CMS->lang['title_add_service']}";
            var lang_asset_add = "{$CMS->lang['title_add_asset']}";
            var lang_product_edit = "{$CMS->lang['title_edit_product']}";
            var lang_asset_edit = "{$CMS->lang['title_edit_asset']}";

            var lang_btn_add_save = "{$CMS->lang['btn_edit_save_request']}";
            var lang_btn_save_request = "{$CMS->lang['btn_save_request']}";
            var lang_btn_edit_save = "{$CMS->lang['btn_edit_save']}";

            var lang_confirm_approve = "{$CMS->lang['confirm_approve_bill']}";

            var lang_cus_add = "{$CMS->lang['title_cus_add']}";
            var lang_cus_edit = "{$CMS->lang['title_cus_edit']}";

        </script>
            <!------- Product -------->
            {$this->formProduct()}
            <!------- ASSET -------->
            {$this->formAsset()}
            <!--------- Add NCC ---------->
            {$this->formAddSupplier()}
            <!--------- shipment ---------->
            {$this->formshipment()}
            <!--------- Manufacture ---------->
            {$this->formAddManufacture()}
            <!--------- Product group ---------->
            {$this->formProductgroup()}
            <!--------- Customer ---------->
            {$this->formCustomer()}
EOF;

        return $output;     
    }

    public function assetFullFormHtml()
    {
        global $CMS;

        $CMS->class->language->load("assets");
        // $CMS->class->language->load("supplier");
        $output .=<<<EOF
        <script>
            var lang_supplier_add = "{$CMS->lang['supplier_add']}";
            var lang_supplier_edit = "{$CMS->lang['supplier_edit']}";

            var lang_shipment_add = "{$CMS->lang['title_shipment_add']}";
            var lang_shipment_edit = "{$CMS->lang['title_shipment_edit']}";

            var lang_manufacture_add = "{$CMS->lang['title_add_manufacture']}";
            var lang_manufacture_edit = "{$CMS->lang['title_edit_manufacture']}";

            var lang_pg_add = "{$CMS->lang['title_add_ass_group']}";
            var lang_pg_edit = "{$CMS->lang['title_edit_ass_group']}";

            var lang_ass_add = "{$CMS->lang['title_add_product']}";
            var lang_ass_edit = "{$CMS->lang['title_edit_product']}";

            var lang_btn_add_save = "{$CMS->lang['btn_edit_save_request']}";
            var lang_btn_save_request = "{$CMS->lang['btn_save_request']}";
            var lang_btn_edit_save = "{$CMS->lang['btn_edit_save']}";

            var lang_confirm_approve = "{$CMS->lang['confirm_approve_bill']}";

        </script>
        
        
            <!------- ASSET -------->
            {$this->formAsset()}
        
            
EOF;

        return $output;
    }


    public function formAddSupplier()
    {
        global $CMS;

        $output =<<<EOF
        <div id="box_add_supplier" class="popup_add_supplier mfp-hide" style="clear: both; overflow: hidden;">
            <p class="title_add change_title" style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;">{$CMS->lang['supplier_add']}</p>
            <form id="add_supplier_form" name="add_supplier_form">
                <p class="error_msg" style="display:none;"></p>
                <input type="hidden" name="checkReturn" value="" />
                <div class="col-md-6">
                    <ul class="list_field_supplier">
                        <li>
                            <fieldset class="form-group">
                                
                                <div class="fl-flex-label fl-collapsed fl-background">
                                    <input type="text" class="form-control" id="supplier_name" name="supplier_name" id="supplier_name" size="45" type="text" value="{$data['supplier_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['please_insert_supplier_name']}"  >
                                <label class="fl-label" for="supplier_name" style="left: 13px; right: 12px;">{$CMS->lang['supplier_name']}<span class="fl-required">*</span></label></div>

                                 <input name="supplier_id" type="hidden" value="" />

                               
                            </fieldset>
                        </li>
                        <li>
                            <fieldset class="form-group">
                            
                                  <div class="fl-flex-label fl-collapsed fl-background">
                                    <input type="text" class="form-control" name="supplier_code" id="supplier_code" size="45"   value="{$data['supplier_code']}"  >
                                <label class="fl-label" for="supplier_code" style="left: 13px; right: 12px;">{$CMS->lang['supplier_code']} </label></div>

                            </fieldset>
                        </li>
                    </ul>
                </div>

                <div class="col-md-6">
                    <ul class="list_field_supplier">    
                        <li>
                            <fieldset class="form-group">
                           
                                <div class="fl-flex-label fl-collapsed fl-background">
                                    <input   class="form-control"   onkeypress="return check_phone(event);" name="supplier_phone" id="supplier_phone" size="45" type="text" value="{$data['supplier_phone']}" >
                                <label class="fl-label" for="supplier_phone" style="left: 13px; right: 12px;">{$CMS->lang['supplier_phone']} </label></div>


                            </fieldset>
                        </li>

                        <li>
                            <fieldset class="form-group">
                             
                                 <div class="fl-flex-label fl-collapsed fl-background">
                                    <input   class="form-control" name="supplier_email" id="supplier_email" size="45" type="text" value="{$data['supplier_email']}"  >
                                <label class="fl-label" for="supplier_email" style="left: 13px; right: 12px;">{$CMS->lang['supplier_email']} </label></div>
 
                            </fieldset>
                        </li>
                    </ul>
                </div>
            <span class="show_more_info col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="cursor:pointer" check="0"><span class="btn_check"><i class="fa fa-plus-square-o" aria-hidden="true"></i></span> {$CMS->lang['tilte_info_more_supplier']}</span>
            <div class="box_show" style="display: none;">
                <ul class="list_field_supplier">
                    <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                        <fieldset class="form-group">
 
                            <label class="form-label" for="supplier_type">{$CMS->lang['supplier_taxcode']}</label>
                              <input   conkeypress="return check_enter_number(event,this);" name="supplier_taxcode" id="supplier_taxcode" size="45" type="text" value="{$data['supplier_taxcode']}" class="form-control">
            
                        </fieldset>
                    </li>
                    
                    <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                        <fieldset class="form-group">
                            <label class="form-label" for="supplier_get_invoice">{$CMS->lang['supplier_get_invoice']}</label>
                            <div class="typeahead-field"> 
                                <span class="typeahead-query">
                                    <select name="supplier_get_invoice" id="supplier_get_invoice" class="form-control" defaultvalue="{$data['supplier_get_invoice']}" style="width: 100%">
                                        <option value="-1">{$CMS->lang['plz_choose']}</option>
                                        <option value="0">{$CMS->lang['no']}</option>
                                        <option value="1">{$CMS->lang['yes']}</option>
                                    </select>
                                </span>
                            </div>
                        </fieldset>
                    </li>

                    <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                        <fieldset class="form-group">
                            <label class="form-label" for="supplier_type">{$CMS->lang['supplier_type']}</label>
                            <div class="typeahead-field"> 
                                <span class="typeahead-query">
                                    <select name="supplier_type" id="supplier_type" class="form-control" onchange="change_supplier_type(this.value)" defaultvalue="{$data['supplier_type']}" style="width: 100%">
                                        <option value="-1">{$CMS->lang['plz_choose']}</option>
                                        <option value="1">{$CMS->lang['supplier_type_1']}</option>
                                        <option value="2">{$CMS->lang['supplier_type_2']}</option>
                                    </select>
                                </span>
                            </div>
                        </fieldset>
                    </li>

                    <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                        <fieldset class="form-group change_supplier">
                            <label class="form-label" for="supplier_type">{$CMS->lang['supplier_idcard_number']}</label>
                             <input  onkeypress="return check_enter_number(event,this);" name="supplier_idcard_number" id="supplier_idcard_number" size="45" type="text" value="{$data['supplier_idcard_number']}" class="form-control">

 
                        </fieldset>
                    </li>

                    <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                        <fieldset class="form-group">
                            <label class="form-label" for="city_id">{$CMS->lang['city_id']}</label>
                            <div class="typeahead-field"> 
                                <span class="typeahead-query">
                                    <select name="city_id" id="city_id" class="form-control select2" onchange="change_city(this.value)" defaultvalue="{$data['city_id']}" style="width: 100%">
                                    {$CMS->country->getOptionCity(238)}
                                    </select>
                                </span>
                            </div>
                        </fieldset>
                    </li>
                    
                    <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                        <fieldset class="form-group">
                            <label class="form-label" for="district_id">{$CMS->lang['district_id']}</label>
                            <div class="typeahead-field"> 
                                <span class="typeahead-query">
                                    <select name="district_id" id="district_id" class="form-control select2" style="width: 100%">
                                        <option value="">{$CMS->lang['select_district']}</option>
                                    </select>
                                </span>
                            </div>
                        </fieldset>
                    </li>

                    <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                        <fieldset class="form-group">
                          
                   
                             <label class="form-label" for="supplier_type">{$CMS->lang['supplier_address']}</label>
                           <input name="supplier_address" id="supplier_address" size="45" type="text" value="{$data['supplier_address']}" class="form-control" >

                        </fieldset>
                    </li>

                    <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                        <fieldset class="form-group">
 

                             <label class="form-label" for="supplier_type">{$CMS->lang['supplier_bank']}</label>
                             <input name="supplier_bank" id="supplier_bank" size="45" type="text" value="{$data['supplier_bank']}" class="form-control >

                        </fieldset>
                    </li>

                    <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                        <fieldset class="form-group">
 
                              <label class="form-label" for="supplier_type">{$CMS->lang['supplier_branch']}</label>
                              <input name="supplier_branch" id="supplier_branch" size="45" type="text" value="{$data['supplier_branch']}" class="form-control"  >

                        </fieldset>
                    </li>

                    <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                        <fieldset class="form-group">
                     
                             
                              <label class="form-label" for="supplier_type">{$CMS->lang['supplier_bank_number']}</label>
                             <input onkeypress="return check_enter_number(event,this);" name="supplier_bank_number" id="supplier_bank_number" size="45" type="text" value="{$data['supplier_bank_number']}" class="form-control"  >


                        </fieldset>
                    </li>

                    <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                        <fieldset class="form-group">
                       
                             <label class="form-label" for="supplier_type">{$CMS->lang['supplier_bank_owner']}</label>
                            <input name="supplier_bank_owner" id="supplier_bank_owner" size="45" type="text" value="{$data['supplier_bank_owner']}" class="form-control" >
                        </fieldset>
                    </li>
                    
                    <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                        <fieldset class="form-group">
                         
                            <div class="fl-flex-label fl-collapsed fl-background">
                                    <textarea class="form-control" placeholder="" id="supplier_note"  name="supplier_note" id="supplier_note" rows="5" cols="50" class="form-control" ></textarea>
                                <label class="fl-label" for="supplier_note" style="left: 13px; right: 12px;">{$CMS->lang['supplier_note']}</label></div>


                        </fieldset>
                    </li>

                    <!--li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                        <fieldset class="form-group">
                            <label class="form-label" for="supplier_status" style="margin-top: 10px;">{$CMS->lang['supplier_status']}</label>
                            <div class="typeahead-field"> 
                                <span class="typeahead-query">
                                    <select name="supplier_status" id="supplier_status" class="form-control" defaultvalue="{$data['supplier_status']}" style="width: 100%">
                                        <option value="0">{$CMS->lang['supplier_status_0']}</option>
                                        <option value="1" selected='selected'>{$CMS->lang['supplier_status_1']}</option>
                                    </select>
                                </span>
                            </div>
                        </fieldset>
                    </li-->
                </ul>   
            </div>

                <ul class="list_field_supplier">
                    <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="min-height: auto;">
                        <fieldset class="form-group">
                            <label class="form-control-label"></label>
                            <div class="typeahead-field"> 
                                <span class="typeahead-query change_action">
                                    <input class="btn act_popup_ncc_btn" aclass=" btn_add_supplier" type="button" value="{$CMS->lang['supplier_add']}"/>
                                </span>
                            </div>
                        </fieldset>
                    </li>
                
                </ul>
                   <a class="btn_add_supplier" style="display:none">btn_add_supplier</a>
                   <a class="btn_edit_do" style="display:none">btn_edit_do</a>
            </form>
        </div>
    <script>
                 $(document).ready(function(){

                    validate_form_custom("#add_supplier_form",".act_popup_btn_validate","box_custom");
                });

     </script>
EOF;

        return $output;     
    }


    public function formProduct()
    {
        global $CMS;

        $CMS->class->language->load("product");

        $option_category = $CMS->product_group->getMultiOptionCategory(1);
        $option_supplier = $CMS->supplier->get_list_supplier();
        // $option_item = $CMS->product->getOptionProduct();
        $option_manufacture = $CMS->manufacture->getOptionManufacture();
        $form_name = 'edit';

        if($CMS->permit['supplier_add'] == 1)
        {
            $btn_add_ncc =<<<EOF
                <a class="btn_gen add_new_supplier pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i></a>
EOF;
            
        }else
        {
            $btn_add_ncc = "";
        }

        if($CMS->permit['manufacture_add'] == 1)
        {
            $btn_add_sx =<<<EOF
                <a class="btn_gen add_new_manufacture pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i></a>
EOF;
            
        }else
        {
            $btn_add_sx = "";
        }


        if($CMS->permit['product_group_add'] == 1)
        {
            $btn_add_pg =<<<EOF
                <a class="btn_gen add_product_group pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i></a>
EOF;
            
        }else
        {
            $btn_add_pg = "";
        }

$output =<<<EOF

        <div id="box_product" class="popup_edit_product mfp-hide" style="clear: both; overflow: hidden;">
            <p class="title_add change_title_product" style="font-weight: bold; text-transform: uppercase; font-size: 18px; text-align: center;">{$CMS->lang['title_product_edit']}</p>
            <form id="edit_product_form" name="edit_product_form" class="form_data" for="edit">
                 
                 <p class="error_msg" style="display:none;"></p>
                 <input type="hidden" name="is_add_success" id="is_add_success" value='' />
               

                <div class="col-md-4">
                     <div class="row">
                        <label class="col-md-12" style="margin-left:-15px;font-weight:bold"> <i class="fa fa-caret-down"></i> {$CMS->lang['product_service_infomation']}</label>
                    </div>
                    <div class="row">
                        <ul class="list_field_product">
                            <li>
                                <fieldset class="form-group">
                                    <div class="fl-flex-label fl-background">
                                    <input type="text" class="form-control ks-rounded"  name="product_name" placeholder="{$CMS->lang['title_product_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['please_insert_product_name']}" value=" " /> 
                                    <label class="fl-label" for="ship_receive_name" style="left: 13px; right: 12px;">{$CMS->lang['title_product_name']}<span class="fl-required">*</span></label>
                                    </div>
                                      <input type="hidden" name="product_id" value="" />
                                            <input type="hidden" class="item_id" name="item_id" value="0"/>
                                            <input type="hidden" class="product_type" name="product_type" value="0"/>

                                </fieldset>
                            </li>
                        
                            <li>
                                <fieldset class="form-group">
                                     
                                     <div class="fl-flex-label fl-collapsed fl-background">
                                        <input type="text" class="form-control ks-rounded"  name="product_sku" placeholder="{$CMS->lang['title_product_code']}" value=" " /> 
                                         <label class="fl-label" for="product_sku" style="left: 13px; right: 12px;">{$CMS->lang['title_product_code']} </label>
                                    </div>

                                </fieldset>
                            </li>
                             <li>
                                <fieldset class="form-group">
                                     <div class="fl-flex-label fl-collapsed fl-background">
                                        <input type="text" class="form-control ks-rounded"  name="product_barcode" placeholder="{$CMS->lang['title_product_barcode']}"  value=" " /> 
                                         <label class="fl-label" for="product_barcode" style="left: 13px; right: 12px;">{$CMS->lang['title_product_barcode']} </label>
                                    </div>

 
                                </fieldset>
                            </li>
                           
EOF;
                        if($CMS->input['site'] != "store_request")
                        {
                            // Neu form store_request se k co phan chon loai san pham / dich vu
                            $output .=<<<EOF
                            <li>
                                <fieldset class="form-group">
                                      <label class="form-label pull-left" for="product_group">{$CMS->lang['title_product_service_type']}</label>
                                         <select name="product_service_type" class="form-control" >
                                                 <option value="0">{$CMS->lang['product_service_type_0']}</option> 
                                                 <option value="1">{$CMS->lang['product_service_type_1']}</option>   
                                         </select>
                                </fieldset>
                            </li>
EOF;
                        }
                           $output .=<<<EOF
                            <li>
                                <fieldset class="form-group action_pg">
                                   
                                    <label class="form-label pull-left" for="product_group">{$CMS->lang['title_product_group_choose']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                                    <div class="box_action pull-right" checkreturn="#box_product">
                                        {$btn_add_pg}
                                        <span class="box_edit box_act_pg pull-right" style="margin-left: 10px;"></span>
                                    </div>
                                    <div class="typeahead-field"> 
                                        <span class="typeahead-query">
                                             <div class="form-control-wrapper">   
                                                <select name="product_group" class="form-control select_pg select2" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['please_select_product_service_cate']}"  >
                                                    {$option_category}
                                                </select>
                                              </div>  
                                        </span>
                                    </div>
                                </fieldset>
                            </li>
                          
                             <li>
                                <fieldset class="form-group">
                                    <label class="form-label pull-left" for="sup_id">{$CMS->lang['title_product_supplier']}</label>
                                    <div class="box_action pull-right" checkreturn="#box_product">
                                        {$btn_add_ncc}
                                        <span class="box_edit_supplier pull-right" style="margin-left: 10px;"></span>
                                    </div>
                                    <div class="typeahead-field"> 
                                        <span class="typeahead-query">
                                            <select name="sup_id" class="form-control select_supplier select2" for="nchange">
                                                {$option_supplier}
                                            </select>
                                        </span>
                                    </div>
                                </fieldset>
                            </li>

                             
                        </ul>
                    </div>
                </div>


                <div class="col-md-8">
                    <div class="row">
                     
                            <div class="col-xl-6">
                                <fieldset class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label pull-left">{$CMS->lang['title_upload_image']}</label>
                                        <div class="actionButtons pull-right">
                                            <ul>
                                                <li onclick="return performClick('ufile');">
                                                    <i tabindex="0" class="fa fa-pencil"></i>
                                                </li>
                                                <li>
                                                    <span class="text-left">|</span>
                                                </li>
                                                <li onclick="return delete_fileToAttach();">
                                                    <i class="fa fa-trash-o"></i>
                                                </li>
                                            </ul>
                                            <input type="hidden" id="ufile_output_b64" name="base64_image" value="">
                                        </div>
                                         
                                        <div class="drop-zone fileinput-button" style="height: 110px !important; padding-top: 0;">
                                                <img id="upload_img_show" style="max-width: 100%; max-height: 110px" src="">
                                                <i class="font-icon font-icon-cloud-upload-2"></i>
                                                <div class="drop-zone-caption">Drag file to upload</div>
                                                <input type="file" name="product_image" id="ufile" accept="image/*">
                                        </div><!--.drop-zone-->
                                </fieldset>
                                
                            </div>
                            <div class="col-xl-6">
                                <div class="row">

                                    <label class="col-md-12" style="margin-right:0;font-weight:bold"> <i class="fa fa-caret-down"></i> {$CMS->lang['sales_infomation']}</label>
                                </div>
                                
                                <ul class="list_field_product">
                                       

                                <li id="display_product_cycle" style="display:none">
                                    <fieldset class="form-group action_pg">
                                        <label class="form-label pull-left" for="product_group">{$CMS->lang['gpayment_cycle']}</label>
                                        <div class="typeahead-field"> 
                                            <span class="typeahead-query">
                                                <select name="product_cycle" id="product_cycle" class="form-control">
                                                </select>
                                            </span>
                                        </div>
                                    </fieldset>
                                </li>
                                       <li>
                                            <fieldset class="form-group">
                                               
                                                <div class="fl-flex-label fl-collapsed fl-background">
                                                    <input type="text" class="form-control ks-rounded"  name="product_price" onkeypress="return check_enter_number(event,this)"  onkeyup="calculate_subitem('{$form_name}');" onfocusout="rebuild_product_price(this,'#box_product');"  onfocusin="rebuild_field_empty(this);"  placeholder="{$CMS->lang['title_product_price']}" value=" "/> 
                                                     <label class="fl-label" for="product_price" style="left: 13px; right: 12px;">{$CMS->lang['title_product_price']} </label>
                                                </div>
                 
                                            </fieldset>
                                        </li>
                                        <li>
                                            <fieldset class="form-group">
                                               
                                                <div class="fl-flex-label fl-collapsed fl-background">
                                                    <input type="text" class="form-control ks-rounded"  name="product_price_original" onkeypress="return check_enter_number(event,this)"  onkeyup="calculate_subitem('{$form_name}');"  onfocusout="rebuild_product_price(this,'#box_product');" onfocusin="rebuild_field_empty(this);"    placeholder="{$CMS->lang['title_product_price_original']}" value=" "/> 
                                                     <label class="fl-label" for="product_price_original" style="left: 13px; right: 12px;">{$CMS->lang['title_product_price_original']} </label>
                                                </div>
                 
                                            </fieldset>
                                        </li>
                                        <li>
                                            <fieldset class="form-group">
                                               
                                                <div class="fl-flex-label fl-collapsed fl-background">
                                                    <input type="text" class="form-control ks-rounded"  name="product_price_sell" onkeypress="return check_enter_number(event,this)"  onkeyup="calculate_subitem('{$form_name}');" onfocusout="rebuild_product_price(this,'#box_product');" onfocusin="rebuild_field_empty(this);"    placeholder="{$CMS->lang['title_product_price_sell']}" value=" "/> 
                                                     <label class="fl-label" for="product_price_sell" style="left: 13px; right: 12px;">{$CMS->lang['title_product_price_sell']} </label>
                                                </div>
                 
                                            </fieldset>
                                        </li>
                                
                                        <li>
                                            <fieldset class="form-group">
                                                <label class="form-label" for="product_tax">{$CMS->lang['table_tax']}</label>
                                                <div class="typeahead-field"> 
                                                     
                                                        <select class="form-control" name="product_tax" onchange="calculate_subitem('{$form_name}');">
                                                            <option value="0">0%</option>
                                                            <option value="10" selected='selected'>10%</option>
                                                        </select>
                                                     
                                                </div>
                                            </fieldset>
                                        </li>

                                        <li>
                                            <fieldset class="form-group">
                                                <label class="form-label" for="product_total">{$CMS->lang['table_amount']}</label>
                                                <div class="typeahead-field"> 
                                                    <span id="product_total" style="font-weight:bold"></span>
                                                </div>
                                            </fieldset>
                                        </li>





                                </ul>
                            </div>
                     
                   

                    </div>
                    
                </div>
                  <div class="row" style="clear:both">
                        <div class="col-xl-8">
                             <fieldset class="form-group" style="margin-bottom: 0;">
                                        <label class="form-label" for="product_description">{$CMS->lang['title_product_description']}</label>
                                        <div class="typeahead-field"> 
                                            <span class="typeahead-query">
                                                <textarea class="form-control" rows="3" cols="30" name="product_description" placeholder="{$CMS->lang['title_product_description']}" ></textarea>
                                            </span>
                                        </div>
                                </fieldset>
                             </div>    
                     </div>
                    
            <div class="row">

                        <label class="col-md-12" style="margin-right:0;margin-top:10px; margin-bottom:5px; font-weight:bold"> <i class="fa fa-caret-down"></i> {$CMS->lang['items_infomation']}</label>
            </div>
           
                <ul class="list_field_product">
                    <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                       
                        

                        <div class="row">
                            <label class="form-label" for="product_group"> </label>
                            <div class="add_table">
                                <div class="table_cus">
                                    <div class="table-responsive" style="overflow-x: initial;">
                                        <table class="">
                                            <thead>
                                                <tr>
                                                    <th width="2%">#</th>
                                                    <th width="20%">{$CMS->lang['table_product_service']}</th>
                                                    <th width="25%">{$CMS->lang['table_description']}</th>
                                                    <th width="15%">{$CMS->lang['table_quantity']}</th>
                                                    <th width="20%">{$CMS->lang['table_price']}</th>
                                                    <th width="10%">{$CMS->lang['table_tax']}</th>
                                                    <th width="5%"></th>
                                                </tr>
                                            </thead>
                                            <tbody class="data_subitem" for="edit">
                                                <tr class="row-item" rowtr="last-row">
                                                    <td class="item-td" for="item-1" check="chtd"><span class="number">#1</span></td>
                                                    <td class="item-td" for="item-2" check="chtd">
                                                        <input type="text" for="item-2" check="chtd" name="sub_product_name[]" class="form-control find_product hidden_border" autocomplete="off"/>
                                                        <input type="hidden" class="product_id" name="sub_product_id[]" value=""/>
                                                        <div class="box_container">
                                                            <div class="box_result_find" style="position: relative; background: #ccc;z-index: 3;top: 0; border: none; margin: 0;"></div>
                                                        </div>
                                                    </td>

                                                    <td class="item-td" for="item-3" check="chtd"><textarea for="item-3" name="sub_product_description[]" check="chtd" class="form-control hidden_border" rows="1" style="resize: none;"></textarea></td>

                                                    <td class="item-td" for="item-4" check="chtd"><input type="text" for="item-4" name="sub_product_quantity[]" value="1" check="chtd" class="form-control hidden_border" onkeyup="calculate_money_subitem('{$form_name}');" onkeypress="return check_enter_number(event,this);"/></td>

                                                    <td class="item-td" for="item-5" check="chtd"><input type="text" for="item-5" name="sub_product_price[]" check="chtd" onkeyup="calculate_money_subitem('{$form_name}');" onkeypress="return check_enter_number(event,this);" class="form-control hidden_border"/></td>
                
                                                    <td class="item-td" for="item-6" check="chtd">
                                                        <select for="item-6" name="sub_product_tax[]" class="form-control hidden_border tax_list" onchange="calculate_money()" check="chtd">
                                                                <option value="0">0%</option>
                                                                <option value="10" selected="selected">10%</option>
                                                        </select>
                                                    </td>


                                                    <td class="trash" for="item-7">
                                                        <span class="del_row_invoid"><i class="fa fa-trash" aria-hidden="true"></i></span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>

                    
                    <!--li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                        <fieldset class="form-group">
                            <label class="form-label" for="product_image">{$CMS->lang['title_upload_image']}</label>
                            <div class="typeahead-field"> 
                                <span class="typeahead-query" style="display: block;">
                                    <input class="form-control" type="file" name="product_image" />
                                </span>
                                <div class="box_img" style="display: none;margin: 10px 0;"></div>
                            </div>
                        </fieldset>
                    </li-->
                    
                    <!--li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                        <fieldset class="form-group">
                            <label class="form-label" for="product_description">{$CMS->lang['title_product_description']}</label>
                            <div class="typeahead-field"> 
                                <span class="typeahead-query">
                                    <textarea class="form-control" rows="7" cols="30" name="product_description" placeholder="{$CMS->lang['title_product_description']}" ></textarea>
                                </span>
                            </div>
                        </fieldset>
                    </li-->

                 
EOF;
           
            if($_SESSION['is_mobile'] == true)
            {
             

            }else
            {
                 

                $output .=<<<EOF
                <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                     <div class="box_control" style="margin-top: 15px;">
                        <input class="btn   act_popup_product_btn" aclass="btn_edit_product_0" type="button" etype="0" value="{$CMS->lang['btn_edit_save']}"/>
                        <input class="btn   act_popup_product_btn" aclass="btn_add_to_request"  type="button" value="{$CMS->lang['btn_save_request']}"/>
                        <input class="btn  act_popup_product_btn" aclass="btn_edit_product_1"  type="button" etype="1" value="{$CMS->lang['btn_edit_save_request']}"/>
                       </div>
                    </li>    
EOF;

            }
 
$output .=<<<EOF


                    
                       <a class="btn_add_product" style="display:none">btn_add_product</a>
                       <a class="btn_edit_product_0 btn_edit_product" etype="0"  style="display:none">btn_edit_product</a>
                       <a class="btn_add_to_request" style="display:none">btn_add_to_request</a>
                       <a class="btn_edit_product_1 btn_edit_product"  etype="1"  style="display:none">btn_add_product</a>
           

                </ul>
EOF;

           if($_SESSION['is_mobile'] == true)
            {
                 
                $output .=<<<EOF
                 <section class="add_cart_footer_popup"> 
                    <div class="btn-group dropup pull-right hidden-xl-up">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-save"></i>{$CMS->lang['gaction']}
                      </button>
                      <div class="dropdown-menu">
                        <ul>
                            <li>
                                <a class="  act_popup_product_btn"  aclass="btn_edit_product_0"  etype="0" ><i class="fa fa-save"></i>{$CMS->lang['btn_edit_save']}</a>
                            </li>
                            <li>
                                <a class="act_popup_product_btn"  aclass="btn_add_to_request"   ><i class="fa fa-save"></i>{$CMS->lang['btn_save_request']}</a>
                            </li>
                            <li>
                                <a class="act_popup_product_btn"  aclass="btn_edit_product_1"  etype="1" ><i class="fa fa-save"></i>{$CMS->lang['btn_edit_save_request']}</a>
                            </li>           

                        </ul>
                      </div>
                    </div>
                </section>

EOF;

            }
                $output .=<<<EOF

            </form>

             <script>
                 $(document).ready(function(){

                    validate_form_custom("#edit_product_form",".act_popup_product_btn","box_custom");
                });

                </script>
 



        </div>

EOF;
        // <li class="col-xl-3 col-lg-3 col-sm-6 col-xs-12">
        //              <fieldset class="form-group">
        //                  <label class="form-label" for="product_price">{$CMS->lang['p_price_sell']}</label>
        //                  <div class="typeahead-field"> 
        //                      <span class="typeahead-query">
        //                          <input class="form-control" name="product_price_sell" onkeypress="return check_enter_number(event,this)" onkeyup="calculate_subitem('{$form_name}');" placeholder="{$CMS->lang['title_product_price']}" />
        //                      </span>
        //                  </div>
        //              </fieldset>
        //          </li>

        return $output;     
    }

    public function formAsset($data = [])
    {
        global $CMS;

        list($row,$list) = $CMS->store->get_list_store($data['store_id']);

        $option_p_product_group = "<option value=''>{$CMS->lang['select']}</option>";
        $group = $CMS->product_group->getAll();
        foreach ($group as $g)
        {
            if ($data['pgroup_id'] == $g['product_group_id']) {
                $option_p_product_group .= "<option value='{$g['product_group_id']}' selected>{$g['product_group_name']}</option>";
            } else {
                $option_p_product_group .= "<option value='{$g['product_group_id']}'>{$g['product_group_name']}</option>";
            }
        }

        $option_ass_show = "";
        $ass_status = !empty($CMS->input['ass_status']) ? $CMS->input['ass_status'] : 1;
        for ($i = 1; $i >= 0; $i--) {
            if ($i == $ass_status) {
                $option_ass_show .= "   <div class='radio w25'><input type='radio' checked  name='ass_status' id='radio-show-{$i}' value='{$i}'><label for='radio-show-{$i}'>{$CMS->lang['ass_status_'.$i]}</label></div>";
            } else {
                $option_ass_show .= "   <div class='radio w25'><input type='radio'    name='ass_status' id='radio-show-{$i}' value='{$i}'><label for='radio-show-{$i}'>{$CMS->lang['ass_status_'.$i]}</label></div>";
            }
        }

        $supplier = $CMS->supplier->get_list_supplier(1);

        $option_p_supplier = "";

        foreach ($supplier as $s) {

            if ($s['supplier_id'] == $data['supplier_id']) {
                $option_p_supplier .= "<option value='{$s['supplier_id']}' selected>{$s['supplier_name']}</option>";
            } else {

                $option_p_supplier .= "<option value='{$s['supplier_id']}'>{$s['supplier_name']}</option>";
            }
        }

        $output =<<<EOF

        <div id="box_asset" class="popup_edit_product mfp-hide" style="clear: both; overflow: hidden;">
            <p class="title_add change_title_product" style="font-weight: bold; text-transform: uppercase; font-size: 18px; text-align: center;">{$CMS->lang['ass_title']}</p>
            <form id="edit_asset_form" name="edit_asset_form" class="form_data" for="edit">
                <input type="hidden" id="asset_submit_type" value="add">
                <input type="hidden" id="ass_key" name="ass_key" value="">
                <input type="hidden" id="item_id" name="item_id" value="">
                
                <p class="error_msg" style="display:none;"></p>
                <div class="col-md-4">
                     <div class="row">
                        <label class="col-md-12" style="margin-left:-15px;font-weight:bold"> <i class="fa fa-caret-down"></i> {$CMS->lang['assets_infomation']}</label>
                    </div>

                    <div class="row">
                        <ul class="list_field_product">
                            <li>
                                <fieldset class="form-group">
                                    <label class="form-label" for="store_id">{$CMS->lang['store_id']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                                    <div class="typeahead-field"> 
                                        <span class="typeahead-query">
EOF;

        if($row < 3)
        {
            $output .=<<<EOF
            {$list}
EOF;
        }
        else
        {
            $output .=<<<EOF
                       
                <div class="form-control-wrapper">  
                       
EOF;
                
                if($row == 0)
                {
                        $output .=<<<EOF
                        <a class="btn btn-inline btn-primary btn-sm " href="{$CMS->vars['root_domain']}/?site=store&act=add">{$CMS->lang['store_new']}</a>

                       
                       
EOF;

                }
                else
                {
                    $output .=<<<EOF
                       
                 <select name="store_id" id="store_id" defaultvalue="{$data['store_id']}" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['ass_err_store']}">
                            {$list}
                </select>
                       
EOF;
                }
                      $output .=<<<EOF
                 </div>       

EOF;
        }

        $output .= <<<EOF
                                        </span>
                                    </div>
                                </fieldset>
                            </li>
                        
                            <li>
                                <fieldset class="form-group">
                                    <label class="form-label" for="pgroup_id">{$CMS->lang['cat_id']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                                    <div class="typeahead-field"> 
                                        <span class="typeahead-query">
                                              <div class="form-control-wrapper">  
                                                 <select name="pgroup_id" id="pgroup_id" defaultvalue="{$data['pgroup_id']}" class="form-control select_pg" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['ass_err_cat']}">                      
                                                      {$option_p_product_group}
                                                 </select>
                                              </div>   
                                        </span>
                                    </div>

                                     <input type="hidden" name="product_id" id="product_id" value="{$data['product_id']}" />  
                                     <div id="ass_list" style="display:none" ></div>

                                </fieldset>
                            </li>

                            <li>
                                <fieldset class="form-group action_pg">
                                    <label class="form-label pull-left" for="ass_name">{$CMS->lang['ass_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                                    <div class="typeahead-field"> 
                                        <span class="typeahead-query">
                                           <div class="form-control-wrapper">  
                                            <input name="ass_name" id="ass_name" size="45" type="text" value="{$data['ass_name']}" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['ass_err_name']}" autocomplete="off">
                                           </div> 
                                           
                                        </span>
                                    </div>



                                </fieldset>
                            </li>
                            
                            <li>
                                <fieldset class="form-group action_pg">
                                    <label class="form-label pull-left" for="ass_quantity">{$CMS->lang['ass_quantity']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                                    <div class="typeahead-field"> 
                                        <span class="typeahead-query">
                                              <div class="form-control-wrapper">  
                                                    <input name="ass_quantity" id="ass_quantity" size="45" type="text" value="{$data['ass_quantity']}" maxlength="10" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['ass_err_quantity']}" onkeypress="return check_enter_number(event, this);"  onfocusout="check_enter_number_2(this,1);">
                                                </div>    
                                        </span>
                                    </div>

                                 

                                </fieldset>
                            </li>
                            
                            <li>
                                <fieldset class="form-group action_pg">
                                    <label class="form-label pull-left" for="supplier_id">{$CMS->lang['ass_supplier_id']}</label>
                                    <div class="box_action pull-right" checkreturn="#box_asset">
EOF;

        if($CMS->permit["supplier_add"] == 1)
        {
            $output .=<<<EOF
                        <a data-size="s" class="add_new_supplier pull-right btn btn-inline btn-primary btn-sm ladda-button btn-inline"><i class="fa fa-plus" aria-hidden="true"></i>    </a>
                        <span class="box_edit_supplier pull-right" style="margin-left: 10px;"></span>
EOF;

        }

        $output .= <<<EOF
                                    </div>
                                    <div class="typeahead-field"> 
                                        <span class="typeahead-query">
                                            <select name="supplier_id" id="supplier_id" value="{$data['supplier_id']}" class="form-control select_supplier_asset"  for="nchange">
                                                <option value=''>{$CMS->lang['select']}</option>
                                                {$option_p_supplier}
                                           </select>
                                        </span>
                                    </div>
                                </fieldset>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="row">
                         <div class="row">
                            <label class="col-md-12" style="margin-left:10px;font-weight:bold"> <i class="fa fa-caret-down"></i> {$CMS->lang['sales_infomation']}</label>
                        </div>
                        <div class="col-xl-6">
                            <ul class="list_field_product">
                                <li>
                                    <fieldset class="form-group">
                                        <label class="form-label pull-left" for="ass_purchase_price">{$CMS->lang['ass_purchase_price']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                                        <div class="typeahead-field"> 
                                            <span class="typeahead-query">
                                                   <div class="form-control-wrapper">
                                                       <input name="ass_purchase_price" id="ass_purchase_price" size="45" type="text" value="{$data['ass_purchase_price']}" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['ass_err_pprice']}" onfocusout="rebuild_asset_price(this,'#box_asset');" >
                                                     </div>  
                                            </span>
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>
                        
                         <div class="col-xl-6">
                            <ul class="list_field_product">
                                <li>
                                    <fieldset class="form-group">
                                        <label class="form-label pull-left" for="ass_original_price">{$CMS->lang['ass_original_price']}</label>
                                        <div class="typeahead-field"> 
                                            <span class="typeahead-query">
                                                <input name="ass_original_price" id="ass_original_price" size="45" type="text" value="{$data['ass_original_price']}" class="form-control" onfocusout="rebuild_asset_price(this,'#box_asset');" >
                                            </span>
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>

                        <div class="col-xl-6">
                            <ul class="list_field_product">
                                <li>
                                    <fieldset class="form-group">
                                        <label class="form-label pull-left" for="ass_price">{$CMS->lang['ass_price']}</label>
                                        <div class="typeahead-field"> 
                                            <span class="typeahead-query">
                                                <input name="ass_price" id="ass_price" size="45" type="text" value="{$data['ass_price']}" class="form-control" onfocusout="rebuild_asset_price(this,'#box_asset');">
                                            </span>
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>
                        <div class="col-xl-6">
                            <ul class="list_field_product">
                                <li>
                                    <fieldset class="form-group">
                                        <label class="form-label pull-left" for="ass_price">{$CMS->lang['ass_tax']}</label>
                                        <div class="typeahead-field"> 
                                          
                                                <select name="ass_tax" class="form-control ">
                                            <option value="0">0%</option>
                                            <option value="10" selected>10%</option>
                                                </select>
                                           
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>
                        <div class="col-xl-6">
                            <ul class="list_field_product">
                                <li>
                                    <fieldset class="form-group">
                                        <label class="form-label pull-left" for="shi_name">{$CMS->lang['shi_id']}</label>
                                        <div class="typeahead-field"> 
                                            <span class="typeahead-query">
                                                <input name="shi_name" id="shi_name" value="{$data['shi_name']}" class="form-control" autocomplete="off">
                                               <input type="hidden" name="shi_id" id="shi_id" />
                                               <div id="shi_list" style="display:none"></div>
                                            </span>
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>
                        <div class="col-xl-6">
                            <ul class="list_field_product">
                                <li>
                                    <fieldset class="form-group">
                                        <label class="form-label pull-left" for="ass_price">{$CMS->lang['ass_code']}</label>
                                        <div class="typeahead-field"> 
                                            <span class="typeahead-query">
                                                <input name="ass_code" id="ass_code" type="text" value="{$data['ass_code']}" class="form-control" autocomplete="off">
                                            </span>
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>
                        <div class="col-xl-6">
                            <ul class="list_field_product">
                                <li>
                                    <fieldset class="form-group">
                                        <label class="form-label pull-left" for="ass_warranty">{$CMS->lang['ass_warranty']}</label>
                                        <div class="typeahead-field"> 
                                            <div class='input-group'>
                                                <input name="ass_warranty" id="ass_warranty" type="text" value="{$data['ass_warranty']}" class="form-control datetimepicker-1">
                                                                        
                                                 <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>
                        <div class="col-xl-6">
                            <ul class="list_field_product">
                                <li>
                                    <fieldset class="form-group">
                                        <label class="form-label pull-left" for="ass_status">{$CMS->lang['ass_status']}</label>
                                        <div class="typeahead-field"> 
                                            {$option_ass_show}
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <fieldset class="form-group" style="margin-bottom: 0;">
                            

                                <div class="fl-flex-label">
                                    <textarea name="ass_desc" id="ass_desc" rows="4" cols="50" class="form-control">{$data['ass_desc']}</textarea>
                                <label class="fl-label" for="ass_desc" style="left: 13px; right: 12px;">{$CMS->lang['ass_desc']}</label></div>

                            </fieldset>


                        </div>
                    </div>
                </div>
                 <div class="row">
                        <label class="col-md-12" style="margin-top:10px;margin-bottom:5px;font-weight:bold"> <i class="fa fa-caret-down"></i> {$CMS->lang['items_infomation']}</label>
                    </div>
                <ul class="list_field_product">
                    <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                        <label class="form-label" for="product_group">{$CMS->lang['ass_add']}</label>
                        <div class="table-responsive" style="overflow-x: initial;">
                            <table class="table">
                                <tr>
                                    <th width="2%">#</th>
                                    <th width="20%">{$CMS->lang['ass_name']}</th>
                                    <th width="10%">{$CMS->lang['ass_quantity']}</th>
                                    <th width="15%">{$CMS->lang['ass_purchase_price']}</th>
                                    <th width="15%">{$CMS->lang['ass_price']}</th>
                                    <th width="15%">{$CMS->lang['ass_tax']}</th>
                                    <th width="20%">{$CMS->lang['ass_warranty']}</th>
                                    <th width="5%"></th>
                                </tr>
                                <tbody class="data_subitem_asset" for="edit">
EOF;

        if(empty($data['sub_name']))
        {
            $output .=<<<EOF
                                <tr class="row-item" rowtr="last-row">
                                    <td class="item-td" for="dgrid-1"  check="chtd"><span class="number">#1</span></td>
                                    <td class="item-td" for="dgrid-2"  check="chtd">
                                        <div class="box_container">
                                            <figure class="text_r">
                                                <input type="text" for="dgrid-2" name="sub_name[]" check="chtd" class="form-control find_product tr_name hidden_border" autocomplete="off"/>
                                                <div class="box_result_find_asset" style="display:none" ></div>
                                            </figure>                                    
                                        </div>
                                        <input type="hidden" class="product_id tr_id" name="sub_id[]" value=""/>
                                    </td>

                                <td class="item-td" for="dgrid-3"  check="chtd">
                                    <input type="text" for="dgrid-3" name="sub_quantity[]" value="1" class="form-control quan_list tr_quantity hidden_border" onkeypress="return check_enter_number(event,this);" />
                                </td>

                                <td class="item-td" for="dgrid-4"  check="chtd">
                                    <input type="text" for="dgrid-4" name="sub_purchase_price[]" class="form-control tr_pprice hidden_border" onkeypress="return check_enter_number(event,this);""/>
                                </td>
                                                        
                                <td class="item-td" for="dgrid-5" check="chtd">
                                    <input type="text" for="dgrid-5" name="sub_price[]" class="form-control tr_price hidden_border" onkeypress="return check_enter_number(event,this);"/>
                                </td>
                                
                                <td class="item-td" for="dgrid-6" check="chtd">
                                    <select for="dgrid-6" name="sub_tax[]" class="form-control tr_tax hidden_border">
                                            <option value="0">0%</option>
                                            <option value="10" selected>10%</option>
                                    </select>
                                </td>

                                <td class="item-td" for="dgrid-7" check="chtd">
                                    <input for="dgrid-7" name="sub_warranty[]" id="sub_warranty" type="text" class="form-control tr_warranty datetimepicker-1 hidden_border">
                                </td>

                                <td class="trash" for="dgrid-8" check="chtd">
                                    <span class="del_row_invoid"><i class="fa fa-trash" aria-hidden="true"></i></span>
                                </td>
                            </tr>
EOF;
        }
        else
        {
            for($i=0;$i<count($data['sub_name']);$i++)
            {
                $key = $i+1;
                $last_row = $i == count($data['sub_name'])-1 ? "last-row" : "";
                $chtd = $i == count($data['sub_name'])-1 ? "chtd" : "";

                unset($tax_selected);
                $tax_selected[$data['sub_tax'][$i]] = 'selected';

                $output .= <<<EOF
                            <tr class="row-item" rowtr="{$last_row}">
                                <td class="item-td" for="dgrid-1" check="{$chtd}"><span class="number">#{$key}</span></td>
                                <td class="item-td" for="dgrid-2"  check="{$chtd}">
                                    <div class="box_container">
                                        <figure class="text_r">
                                            <input type="text" for="dgrid-2" name="sub_name[]" value="{$data['sub_name'][$i]}" check="chtd" class="form-control find_product tr_name hidden_border" autocomplete="off"/>
                                            <div class="box_result_find_asset" style="display:none" ></div>
                                        </figure>
                                    </div>
                                    <input type="hidden" class="product_id" name="sub_id[]" value=""/>
                                </td>

                                <td class="item-td" for="dgrid-3"  check="{$chtd}">
                                    <input type="text" for="dgrid-3" name="sub_quantity[]" value="{$data['sub_quantity'][$i]}" class="form-control quan_list tr_quantity hidden_border" onkeypress="return check_enter_number(event,this);" />
                                </td>

                                <td class="item-td" for="dgrid-4"  check="{$chtd}">
                                    <input type="text" for="dgrid-4" name="sub_purchase_price[]" value="{$data['sub_purchase_price'][$i]}" class="form-control tr_pprice hidden_border" onkeypress="return check_enter_number(event,this);""/>
                                </td>
                                                        
                                <td class="item-td" for="dgrid-5"  check="{$chtd}">
                                    <input type="text" for="dgrid-5" name="sub_price[]" value="{$data['sub_price'][$i]}" class="form-control tr_price hidden_border" onkeypress="return check_enter_number(event,this);"/>
                                </td>
                                
                                <td class="item-td" for="dgrid-6" check="{$chtd}">
                                    <select for="dgrid-6" name="sub_tax[]" class="form-control tr_tax hidden_border">
                                            <option {$tax_selected[0]} value="0">0%</option>
                                            <option {$tax_selected[10]} value="10">10%</option>
                                    </select>
                                </td>

                                <td class="item-td" for="dgrid-7" check="{$chtd}">
                                    <input for="dgrid-7" name="sub_warranty[]" id="sub_warranty" value="{$data['sub_warranty'][$i]}" type="text" class="form-control tr_warranty datetimepicker-1 hidden_border">
                                </td>

                                <td class="trash" for="dgrid-8">
                                    <span class="del_row_invoid"><i class="fa fa-trash" aria-hidden="true"></i></span>
                                </td>
                            </tr>        
EOF;
            }
        }


        $output .= <<<EOF
                                </tbody>
                            </table>
                        </div>
                    </li>
                    <!--li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                        <fieldset class="form-group">
                            <label class="form-label" for="product_image">{$CMS->lang['title_upload_image']}</label>
                            <div class="typeahead-field"> 
                                <span class="typeahead-query" style="display: block;">
                                    <input class="form-control" type="file" name="product_image" />
                                </span>
                                <div class="box_img" style="display: none;margin: 10px 0;"></div>
                            </div>
                        </fieldset>
                    </li-->
                    <!--li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                        <fieldset class="form-group">
                            <label class="form-label" for="product_description">{$CMS->lang['title_product_description']}</label>
                            <div class="typeahead-field"> 
                                <span class="typeahead-query">
                                    <textarea class="form-control" rows="7" cols="30" name="product_description" placeholder="{$CMS->lang['title_product_description']}" ></textarea>
                                </span>
                            </div>
                        </fieldset>
                    </li-->

EOF;

                if($_SESSION['is_mobile'] == false)
                {
                    $output .=<<<EOF
                     <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                        <div class="box_control" style="margin-top: 15px;">
                            <input class="btn act_popup_btn_validate"  aclass="btn_edit_asset_0"   type="button" etype="0" value="{$CMS->lang['btn_edit_save']}"/>
                            <input class="btn act_popup_btn_validate" aclass="btn_add_to_asset"  type="button" value="{$CMS->lang['btn_save_request']}"/>
                            <input class="btn act_popup_btn_validate"  aclass="btn_edit_asset_1"  type="button" etype="1" value="{$CMS->lang['btn_edit_save_request']}"/>
                        </div>
                    </li>


EOF;

                }

                   
                 $output .=<<<EOF


                </ul>

EOF;

                
                if($_SESSION['is_mobile'] == true)
                {
                    $output .=<<<EOF
                <section class="add_cart_footer_popup"> 
                    <div class="btn-group dropup pull-right hidden-xl-up">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-save"></i>{$CMS->lang['gaction']}
                      </button>
                      <div class="dropdown-menu">
                        <ul>
                            <li>
                                <a class="act_popup_btn_validate"  aclass="btn_edit_asset_0" etype="0" ><i class="fa fa-save"></i>{$CMS->lang['btn_edit_save']}</a>
                            </li>
                            <li>
                                <a class="act_popup_btn_validate" aclass="btn_add_to_asset" ><i class="fa fa-save"></i>{$CMS->lang['btn_save_request']}</a>
                            </li>
                            <li>
                                <a class="act_popup_btn_validate" class="btn_edit_asset_1" etype="1" ><i class="fa fa-save"></i>{$CMS->lang['btn_edit_save_request']}</a>
                            </li>           

                        </ul>
                      </div>
                    </div>
                </section>
EOF;
                }

                  $output .=<<<EOF
                    <a class="btn_add_to_asset" style="display:none">btn_add_to_asset</a>
                      <a class="btn_edit_asset_1 btn_edit_asset" etype="1" style="display:none">btn_add_product</a>
                        <a class="btn_edit_asset_0 btn_edit_asset " etype="0"  style="display:none">btn_add_product</a>
            </form>
        </div>



             <script>
                 $(document).ready(function(){

                    validate_form_custom("#edit_asset_form",".act_popup_btn_validate","box_custom");
                });

             </script> 

EOF;

        return $output;
    }


    public function formshipment()
    {
        global $CMS;

        $output =<<<EOF

        <div id="box_shipment" class="popup_shipment mfp-hide">
            <p class="title_add change_title_shipment" style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;">{$CMS->lang['title_shipment_add']}</p>
            <form id="shipment_form" name="shipment_form">
                <ul class="list_field_supplier">
                    <li style="min-height: 0px;"><p class="error_shipment" style="display:none;"></p></li>
                    <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                        <fieldset class="form-group">
                            <label class="form-label" for="shipment_title">{$CMS->lang['shi_name']}<font style="margin-left:5px;" color="#FF0000">(*)</font></label>
                            <div class="typeahead-field"> 
                                <span class="typeahead-query">
                                    <input name="shi_name" id="shi_name" size="45" type="text" value="" class="form-control">
                                    <input name="shi_id" id="shi_id" size="45" type="hidden" value="" class="form-control">
                                </span>
                            </div>
                        </fieldset>
                    </li>
                    <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                        <fieldset class="form-group">
                            <label class="form-label" for="shi_description">{$CMS->lang['shi_description']}</label>
                            <div class="typeahead-field"> 
                                <span class="typeahead-query">
                                    <textarea name="shi_description" id="shi_description" rows="5" class="form-control"></textarea>
                                </span>
                            </div>
                        </fieldset>
                    </li>

                    <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="min-height: auto;">
                        <fieldset class="form-group">
                            <label class="form-control-label"></label>
                            <div class="typeahead-field"> 
                                <span class="typeahead-query change_action_shipment">
                                    <input class="btn btn_reset_attr btn_add_shipment" type="button" value="{$CMS->lang['title_shipment_add']}"/>
                                </span>
                            </div>
                        </fieldset>
                    </li>
                
                </ul>
            
            </form>
        </div>  
EOF;

        return $output;
    }

    public function formAddManufacture()
    {
        global $CMS;

        $output =<<<EOF
        <div id="box_add_manufacture" class="popup_manufacture mfp-hide">
            <p class="title_add title_add_manufacture" style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;">{$CMS->lang['title_add_manufacture']}</p>
            <form id="add_manufacture_form" name="form_manufacture">
                <p class="manu_error_msg" style="display:none;"></p>
                <input type="hidden" name="checkReturn" value="" />
                <div class="col-md-6">
                    <ul class="list_field_supplier">
                        <li>
                            <fieldset class="form-group">
                                <label class="form-label" for="manufacture_name">{$CMS->lang['manufacture_name']}<font style="margin-left:5px;" color="#FF0000">(*)</font></label>
                                <div class="form-control-wrapper">  
                                 <input name="m_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['please_insert_manufacture_name']}" type="text" class="form-control" value="" />
                                </div>

                                <input name="m_manufacture_id" type="hidden" class="form-control" value="" />
                            </fieldset>
                        </li>
                        <li>
                            <fieldset class="form-group">
                                <label class="form-label" for="manufacture_code">{$CMS->lang['manufacture_code']}</label>
                                <input name="m_code" class="form-control" value="" />
                            </fieldset>
                        </li>
                        <li>
                            <fieldset class="form-group">
                                <label class="form-label" for="manufacture_description">{$CMS->lang['manufacture_description']}</label>
                                <textarea rows="4" class="form-control" name="m_description"></textarea>
                            </fieldset>
                        </li>
                        
                    </ul>
                </div>

                <div class="col-md-6">
                    <ul class="list_field_supplier">    
                        
                        <li>
                            <fieldset class="form-group">
                                <label class="form-label" for="manufacture_avartar">{$CMS->lang['manufacture_avartar']}</label>
                                <div class="box_img" style="display: none;margin: 10px 0;"></div>
                                
                                <div class="drop-zone fileinput-button">
                                      <img id="upload_img_show" width="205">

                                      <i class="font-icon font-icon-cloud-upload-2"></i>
                                       <div class="drop-zone-caption">Drag file to upload</div>
                                         <input type="file" name="m_avartar" id="ufile" accept="image/*">
                                 </div><!--.drop-zone-->
                                 <div class="actionButtons">
                                    <ul>
                                        <li onclick="return performClick('ufile');">
                                            <i tabindex="0" class="fa fa-pencil"></i>
                                        </li>
                                        <li>
                                            <span class="text-left">|</span>
                                        </li>
                                        <li onclick="return delete_fileToAttach();">
                                            <i class="fa fa-trash-o"></i>
                                        </li>
                                    </ul>
                                     <input type="hidden" id="ufile_output_b64" name="base64_image">
                                </div>
                                
                            </fieldset>
                        </li>
                    </ul>
                </div>
            

                <ul class="list_field_supplier">
                    <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="min-height: auto;">
                        <fieldset class="form-group">
                            <label class="form-control-label"></label>
                            <span class="change_action_manufacture">
                                <input class="btn btn_add_manufacture" type="button" value="{$CMS->lang['title_add_manufacture']}"/>
                            </span>
                        </fieldset>
                    </li>
                
                </ul>
                 <a class="btn_add_manufacture" style="display:none">btn_add_manufacture</a>
                  <a class="btn_edit_do_manufacture" style="display:none">btn_edit_do_manufacture</a>
          
            </form>
        </div>
         <script>
                 $(document).ready(function(){

                    validate_form_custom("#box_add_manufacture",".act_popup_btn_validate","box_custom");
                });

          </script>  

EOF;

        return $output;     
    }

    /**
     * Add product group
     * @param string $url_redirect
     * @return string
     */

    public function formProductgroup($url_redirect = "")
    {
        global $CMS;
        $option_product_group = $CMS->product_group->getMultiOptionCategory(1);
        $optionCatGallery = $CMS->gallery->load_cate_gallery();
        $optionAttrGroup = attribute::getOptionAttrGroup();
        $output =<<<EOF
        <div id="box_product_group" class="popup_product_group mfp-hide" style="clear: both; overflow: hidden;">
            <p class="title_add title_change_pg" style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;">{$CMS->lang['title_add_product_group']}</p>
            <form id="form_product_group" name="form_product_group">
                <p class="pg_error_msg" style="display:none;"></p>
                <input type="hidden" name="checkReturn" value="" />
                <input type="hidden" name="url_redirect" value="{$url_redirect}" />
                
                <div class="col-md-6">
                    <fieldset class="form-group">
                        <label class="form-label">{$CMS->lang['title_product_group']}</label>
                        <select class="form-control" name="pg_parent" id="pg_parent">
                            {$option_product_group}
                        </select>
                        <input type="hidden" name="pg_id" value='' />
                    </fieldset>

                    <fieldset class="form-group">
                           
                            <div class="fl-flex-label fl-collapsed  fl-background">
                                    <input type="text" onfocusout="ger_pgcode(this,'#form_product_group #pg_code');" class="form-control" name="pg_name" id="pg_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['enter_group_product_name']}" value=" " >
                            <label class="fl-label" for="pg_name" style="left: 13px; right: 12px;">{$CMS->lang['title_product_group_name']}<span class="fl-required">*</span></label></div>

                    </fieldset>
 


                    <fieldset class="form-group">
                        
                           <div class="fl-flex-label fl-collapsed fl-background">
                                    <input type="text" class="form-control ks-rounded" name="pg_code" id="pg_code" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['enter_group_product_code']}" value=" ">
                            <label class="fl-label" for="pg_code" style="left: 13px; right: 12px;">{$CMS->lang['title_product_group_code']}<span class="fl-required">*</span></label></div>
   

                    </fieldset>

                   <fieldset class="form-group">
                        <div class="fl-flex-label fl-collapsed fl-background">
                             <textarea class="form-control" placeholder="" id="fl-label-577056637" rows="7"  name="pg_description" id="pg_description"></textarea>
                            <label class="fl-label" for="fl-label-577056637" style="left: 13px; right: 12px;">{$CMS->lang['title_group_description']}</label>
                        </div>
                    </fieldset>

                    <fieldset class="form-group">
                        <div class="fl-flex-label fl-collapsed fl-background">
                            <input type="number" class="form-control ks-rounded" name="pg_order" id="pg_order" value="1">
                            <label class="fl-label" for="pg_order" style="left: 13px; right: 12px;">{$CMS->lang['title_product_group_order']}</label>
                        </div>
                    </fieldset>
                </div>

                <div class="col-md-6">
                    <fieldset class="form-group">
                        <label class="form-label">{$CMS->lang['title_gallery_cate']}</label>
                        <select class="form-control" name="cat_gallery_id" id="cat_gallery_id">
                            <option value="">{$CMS->lang['title_plz_choose_cat_gallery']}</option>
                            {$optionCatGallery}
                        </select>
                    </fieldset>
EOF;
      //isset($CMS->vars['type_web']) and  => LHL-2018-07-19: Temporary hide, and only use commerce as default
                if($CMS->vars['type_web'] == "ecommerce")
                {
    $output .=<<<EOF
                    <fieldset class="form-group">
                        <label class="form-label">{$CMS->lang['title_attribute_group']}</label>
                        <select class="form-control" name="attr_group" id="attr_group">
                            <option value="">{$CMS->lang['title_choose_plz']}</option>
                            {$optionAttrGroup}
                        </select>
                    </fieldset>
EOF;
                }
    $output .=<<<EOF
                    <fieldset class="form-group">
                        <label class="form-label">{$CMS->lang['title_pro_group_type']}</label>
                        <div class="radio w25">
                            <input type="radio" checked="" name="pg_type" id="radio-0" value="0"><label for="radio-0">{$CMS->lang['title_product_group_type_0']}</label>
                        </div>  

                        <div class="radio w25">
                            <input type="radio" name="pg_type" id="radio-1" value="1"><label for="radio-1">{$CMS->lang['title_product_group_type_1']}</label>
                        </div>  
                    </fieldset>
                    <fieldset class="form-group">
                        
                        <div class="checkbox-toggle">
                                <input type="checkbox"  value="1" name="pg_status" id="pg_status" checked="">
                                <label for="pg_status">{$CMS->lang['title_product_group_status']}</label>
                        </div>

                    </fieldset>
  

                    <fieldset class="form-group">
                        <label class="form-label">{$CMS->lang['manufacture_avartar']}</label>
                        <div class="col-sm-12">
                            <div class="drop-zone fileinput-button">
                                  <img id="upload_img_show" width="100%">
                                  <i class="font-icon font-icon-cloud-upload-2"></i>
                                   <div class="drop-zone-caption">Drag file to upload</div>
                                     <input type="file" name="pg_avartar" id="ufile" accept="image/*">
                            </div><!--.drop-zone-->
                            <div class="row actionButtons">
                                <ul>
                                    <li onclick="return performClick('ufile');">
                                        <i tabindex="0" class="fa fa-pencil"></i>
                                    </li>
                                    <li>
                                        <span class="text-left">|</span>
                                    </li>
                                    <li class="del_file_img" onclick="return delete_fileToAttach();">
                                        <i class="fa fa-trash-o"></i>
                                    </li>
                                </ul>
                                <input type="hidden" id="ufile_output_b64" name="base64_image">
                            </div>
                        </div>  
                    </fieldset>
                </div>
                <div class="col-md-12">
                    <fieldset class="form-group change_action_pg">
  
                        <button class="btn btn-inline btn-primary ladda-button btn_add_product_group" data-style="expand-left" aclass="btn_add_pg" ><span class="ladda-label">expand-left</span> </button>


                    </fieldset>
                </div>
                   <a class="btn_add_pg" style="display:none">btn_add_pg</a>
                    <a class="btn_edit_pg" style="display:none">btn_edit_pg</a>
            </form>
        </div>
 <script>
         $(document).ready(function(){
            validate_form_custom("#form_product_group",".act_popup_btn_validate","box_custom");
        });
  </script>
EOF;

        return $output;     
    }


    public function formCustomer()
    {
        global $CMS;

        $CMS->class->language->load('customer');

        $output =<<<EOF
        <div id="box_customer" class="popup_add_supplier mfp-hide" style="clear: both; overflow: hidden;">
            <p class="title_add title_change_cus" style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;">{$CMS->lang['supplier_add']}</p>
            <form id="form_customer" name="form_customer">
                <p class="error_msg" style="display:none;"></p>
                <input type="hidden" name="checkReturn" value="" />
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list_field_supplier">
                            <li>
                                <fieldset class="form-group">
                                    <label class="form-label" for="cus_full_name">{$CMS->lang['cus_full_name']}<font style="margin-left:5px;" color="#FF0000">(*)</font></label>
                                    <div class="typeahead-field"> 
                                        <span class="typeahead-query">
                                            <input name="cus_full_name" id="cus_full_name" size="45" type="text" value="{$data['cus_full_name']}" class="form-control">
                                            <input name="cus_id" type="hidden" value="" />
                                        </span>
                                    </div>
                                </fieldset>
                            </li>
                            <li>
                                <fieldset class="form-group">
                                    <label class="form-label" for="cus_address">{$CMS->lang['cus_address']}</label>
                                    <div class="typeahead-field"> 
                                        <span class="typeahead-query">
                                            <input name="cus_address" id="cus_address" size="45" type="text" value="{$data['cus_address']}" class="form-control">
                                        </span>
                                    </div>
                                </fieldset>
                            </li>
                        </ul>
                    </div>

                    <div class="col-md-6">
                        <ul class="list_field_supplier">    
                            <li>
                                <fieldset class="form-group">
                                    <label class="form-label" for="cus_phone">{$CMS->lang['cus_phone']}</label>
                                    <div class="typeahead-field"> 
                                        <span class="typeahead-query">
                                            <input onkeypress="return check_phone(event);" name="cus_phone" id="cus_phone" size="45" type="text" value="{$data['cus_phone']}" class="form-control">
                                        </span>
                                    </div>
                                </fieldset>
                            </li>
                            <li>
                                <fieldset class="form-group">
                                    <label class="form-label" for="cus_email">{$CMS->lang['cus_email']}<font style="margin-left:5px;" color="#FF0000">(*)</font></label>
                                    <div class="typeahead-field"> 
                                        <span class="typeahead-query">
                                            <input name="cus_email" id="cus_email" size="45" type="text" value="{$data['cus_email']}" class="form-control">
                                        </span>
                                    </div>
                                </fieldset>
                            </li>
                        </ul>
                    </div>
                </div>
                    
                
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list_field_supplier">    
                            <li>
                                <fieldset class="form-group">
                                    <label class="form-label" for="cus_email_invoice">{$CMS->lang['cus_email_invoice']}</label>
                                    <div class="typeahead-field"> 
                                        <span class="typeahead-query">
                                            <input name="cus_email_invoice" id="cus_email_invoice" size="45" type="text" value="{$data['cus_email_invoice']}" class="form-control">
                                        </span>
                                    </div>
                                </fieldset>
                            </li>
                        </ul>
                    </div>
                </div>
                
            
                <ul class="list_field_supplier">
                    <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="min-height: auto;">
                        <fieldset class="form-group">
                            <label class="form-control-label"></label>
                            <div class="typeahead-field"> 
                                <span class="typeahead-query change_action_cus">
                                    <input class="btn btn_add_supplier" type="button" value="{$CMS->lang['title_change_cus']}"/>
                                </span>
                            </div>
                        </fieldset>
                    </li>
                
                </ul>
            
            </form>
        </div>

EOF;

        return $output; 
    }


    public function get_optioncity()
    {
        global $CMS, $DB;

       $country_id_default = $CMS->country->idCountry($CMS->vars['default_country']);
       $optioncity = $CMS->country->getOptionCity($country_id_default);
       return $optioncity;
    }

    public function get_list_city()
    {
        global $CMS, $DB;
        // if($CMS->vars['default_country'] == "VN")
        // {

        // }
        $sql = $DB->query("SELECT * FROM ".root_table."city");
       
        $output = "<option value=''>{$CMS->lang['cus_select_city']}</option>";
        
        if($DB->num_rows($sql) == 0)
        {
            return 0;   
        }
        
        while($data = $DB->fetch_array($sql))
        {
            
            $output .= "<option value='".($data['city_id'])."'>{$data['city_name']}</option>";
        }
        
        return $output;
        
    }

    /**
     * @param $city_id
     * @param int $cache
     * @return int|string
     */
    public function get_list_district($city_id, $cache=1)
    {
        global $CMS, $DB;

        if($city_id)
        {
            $sql_add = " C.city_id='{$city_id}' AND ";
        }

        $sql = "SELECT * FROM ".root_table."district as D left join ".root_table."city AS C ON D.city_id=C.city_id WHERE {$sql_add} district_deleted=0";

        $cacheData = $DB->fetch_data($sql, 'district.city', $cache);

        $output = "<option value=''>{$CMS->lang['select_district']}</option>";

        if(!$cacheData)
        {
            return 0;
        }

        foreach($cacheData as $data)
        {
            if($data['dv_name'])
            {
                $output .= "<option value='".($data['district_id'])."'>{$data['district_name']}</option>";
            }
        }

        return $output;
        
    }


    public function footer_back($url = array())
    {

        global $CMS, $DB;


        $output = "";

        if(count($url) > 1  AND $url['list'] != "" AND $url['detail_id'] != "")
        {
            if($_SESSION['is_mobile'] == true)
            {
            $output .=<<<EOF
     

                 <div class="btn-group dropup pull-left hidden-xl-up">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-mail-reply"></i>{$CMS->lang['back']}
                      </button>
                      <div class="dropdown-menu">
                        <ul>
                            <li><a  href="{$url['list']}" title=""><i class="fa  fa-mail-reply"></i>{$CMS->lang['list']}</a></li>
                            <li><a   title=""  href="{$url['list']}&act=show&id={$url['detail_id']}"  ><i class="fa   fa-file-text-o"></i>{$CMS->lang['back_detail']}</a></li>
                        </ul>
                      </div>
                    </div>

EOF;
            }
             else
             {
                 $output .=<<<EOF
                    <a  href="{$url['list']}""  class="pull-left cancel">{$CMS->lang['back_list']}</a>
                     <a   href="{$url['list']}&act=show&id={$url['detail_id']}"  class="pull-left cancel">{$CMS->lang['back_detail']}</a>
                    
EOF;
            }    //End if type  = 1
        }
        else
        {

            $output .=<<<EOF
                <a  href="{$url['list']}"  class="pull-left cancel">{$CMS->lang['back_list']}</a>          
EOF;

         }  //End type

        return $output;

    }//End footer_back


      public function footer_show($url = array())
    {

        global $CMS, $DB;

        // $url[0]['key'] = "save_and_list"; $url[0]['icon'] = "fa-list-ol";  $url[0]['redirect'] = "list"; 
        // $url[1]['key'] = "save_and_add"; $url[1]['icon'] = "fa-plus";  $url[1]['redirect'] = "add"; 
        // $url[2]['key'] = "save_and_detail"; $url[2]['icon'] = "fa-file-text-o"; $url[2]['redirect'] = "detail";


        if(count($url) > 1  AND $url['list'] != "" AND $url['detail_id'] != "")
        {
            if($_SESSION['is_mobile'] == true)
            {
            $output .=<<<EOF
     

                 <div class="btn-group dropup pull-left hidden-xl-up">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-mail-reply"></i>{$CMS->lang['gaction']}
                      </button>
                      <div class="dropdown-menu">
                        <ul>
                            <li><a onclick="delete_confirm('{$url['list']}&act=delete&id={$url['detail_id']}');"    title=""><i class="fa  fa-mail-reply"></i>{$CMS->lang['delete']}</a></li>
                            <li><a     href="{$url['list']}&act=edit&id={$url['detail_id']}"  ><i class="fa   fa-file-text-o"></i>{$CMS->lang['edit']}</a></li>
                        </ul>
                      </div>
                    </div>

EOF;
            }
             else
             {
                 $output .=<<<EOF
                    <a  onclick="delete_confirm('{$url['list']}&act=delete&id={$url['detail_id']}');" class="btn btn-inline btn-primary ladda-button pull-right add_cart" >{$CMS->lang['delete']}</a>
                     <a   href="{$url['list']}&act=edit&id={$url['detail_id']}"  class="btn btn-inline btn-primary ladda-button pull-right add_cart">{$CMS->lang['edit']}</a>

                  

                    
EOF;
            }    //End if type  = 1
        }
         

        return $output;

    }//End footer_back





    public function footer_save($module = "", $url_input = array())
    {
        global $CMS, $DB;
        if($module == "product")
        {
            $url[0]['key'] = "save_and_list"; $url[0]['icon'] = "fa-list-ol"; $url[0]['js'] = " onclick='product_submit();' ";  $url[0]['redirect'] = "list"; 
            $url[1]['key'] = "save_and_add"; $url[1]['icon'] = "fa-plus"; $url[1]['js'] = " onclick='product_submit();' ";   $url[1]['redirect'] = "add"; 
            $url[2]['key'] = "save_and_detail"; $url[2]['icon'] = "fa-file-text-o"; $url[2]['js'] = " onclick='product_submit();' ";   $url[2]['redirect'] = "detail";  
        }
        else
        {
            $url[0]['key'] = "save_and_list"; $url[0]['icon'] = "fa-list-ol";  $url[0]['redirect'] = "list"; 
            $url[1]['key'] = "save_and_add"; $url[1]['icon'] = "fa-plus";  $url[1]['redirect'] = "add"; 
            $url[2]['key'] = "save_and_detail"; $url[2]['icon'] = "fa-file-text-o"; $url[2]['redirect'] = "detail";
        }

        if(count($url_input) > 0)
        {
              $url = array_merge($url,$url_input);
        }


        $output = "";
        if(count($url) > 1 )
        {
            if($_SESSION['is_mobile'] == true)
            {
            $output .=<<<EOF
     

               <div class="btn-group dropup pull-right hidden-xl-up">
                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa"></i>{$CMS->lang['save_option']}
                  </button>
                  <div class="dropdown-menu">
                    <ul>

EOF;
                    
                    foreach ($url as $key => $value) {
                        # code...
                        $lang_key = $value['key'];

                        $output .=<<<EOF
                         <li><a class="act_submit_save" value="{$value['redirect']}" title=""><i class="fa  {$value['icon']}"></i>{$CMS->lang["{$lang_key}"]}</a></li>
EOF;


                    }
                     $output .=<<<EOF
                       
                    </ul>
                  </div>
                </div>
                <button type="submit" id="footer_trigger_submit" style="display:none"/>
EOF;
            }
             else
             {
                 $output .=<<<EOF
                
                 <div class="btn-group dropup pull-right">
                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-save"></i>{$CMS->lang['save_option']}
                  </button>
                  <div class="dropdown-menu">
                    <ul>

EOF;
                    
                    foreach ($url as $key => $value) {
                        # code...
                      
                        $lang_key = $value['key'];
                        $value['js'] = \lib\input::arrayValue($value, 'js');

                        $output .=<<<EOF
                         <li><a class="act_submit_save" {$value['js']}  value="{$value['redirect']}" title=""><i class="fa  {$value['icon']}"></i>{$CMS->lang["{$lang_key}"]}</a></li>
EOF;


                    }
                     $output .=<<<EOF
                       
                       
                    </ul>
                  </div>
                </div>

                <button type="submit" class="btn btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs"><span class="ladda-label">{$CMS->lang['save_and_list']}</span><span class="ladda-spinner"></span></button>
               
EOF;
            }    //End if type  = 1
        }
        else
        {

            $output .=<<<EOF
               <button type="submit" class="btn btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs"><span class="ladda-label">{$CMS->lang['save_and_list']}</span><span class="ladda-spinner"></span></button>
               
EOF;

         }  //End type

         $output .=<<<EOF
                <input type="hidden" name="action_redirect" id="footer_action_redirect" value="" />
         <script>
            $(".act_submit_save").click(function(){
               
                $("#footer_action_redirect").val($(this).attr("value"));
            });
         </script>
EOF;

        return $output;

    }//End footer_back


      public function footer_edit(  $module = "", $url_input = array())
    {

        global $CMS, $DB;
        
        if($module == "product")
        {
            
            $url[0]['key'] = "edit_and_list"; $url[0]['icon'] = "fa-list-ol";  $url[0]['redirect'] = "list"; $url[0]['js'] = " onclick='product_submit();' ";
            $url[1]['key'] = "edit_and_edit"; $url[1]['icon'] = "fa-files-o";  $url[1]['redirect'] = "edit"; $url[1]['js'] = " onclick='product_submit();' ";

        }
        else
        {
            $url[0]['key'] = "edit_and_list"; $url[0]['icon'] = "fa-list-ol";  $url[0]['redirect'] = "list"; 
            $url[1]['key'] = "edit_and_edit"; $url[1]['icon'] = "fa-files-o";  $url[1]['redirect'] = "edit"; 
        }
      
        if(count($url_input) > 0)
        {
              $url = array_merge($url,$url_input);
        }
   

        
        $output = "";
  
        if(count($url) > 1 )
        {
            if($_SESSION['is_mobile'] == true)
            {
            $output .=<<<EOF
     

               <div class="btn-group dropup pull-right hidden-xl-up">
                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-mail-reply"></i>{$CMS->lang['edit_option']}
                  </button>
                  <div class="dropdown-menu">
                    <ul>

EOF;
                    
                    foreach ($url as $key => $value) {
                        # code...
                        $lang_key = $value['key'];

                        $output .=<<<EOF
                         <li><a class="act_submit_save"  {$value['js']} value="{$value['redirect']}" title=""><i class="fa  {$value['icon']}"></i>{$CMS->lang["{$lang_key}"]}</a></li>
EOF;


                    }
                     $output .=<<<EOF
                       
                    </ul>
                  </div>
                </div>
       
EOF;
            }
             else
             {
                 $output .=<<<EOF
                

EOF;
                    
                    foreach ($url as $key => $value) {
                        # code...
                      
                        $lang_key = $value['key'];
                        $value['js'] = isset($value['js']) ? $value['js'] : null;

                        $output .=<<<EOF
                         

                           <button  {$value['js']} class="btn btn-inline btn-primary ladda-button pull-right add_cart act_submit_save" data-style="expand-right" data-size="xs" value="{$value['redirect']}"><span class="ladda-label" >{$CMS->lang["{$lang_key}"]}</span><span class="ladda-spinner"></span></button>

EOF;


                    }
                     $output .=<<<EOF
                       
                  

              
               
EOF;
            }    //End if type  = 1
        }
        else
        {

            $output .=<<<EOF
               <button type="submit" class="btn btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs"><span class="ladda-label">{$CMS->lang['save_and_list']}</span><span class="ladda-spinner"></span></button>
               
EOF;

         }  //End type

         $output .=<<<EOF
                <input type="hidden" name="action_redirect" id="footer_action_redirect" value="" />
         <script>
            $(".act_submit_save").click(function(){
                $("#footer_action_redirect").val($(this).attr("value"));
            });
         </script>
EOF;

        return $output;

    }//End footer_back

    function uploadMulti($input_file_name ="file_upload[]", $li_html = "")
    {
        global $CMS;
        $CMS->class->language->load("global");
        $display = $li_html != "" ? "block" : "none";
        $output =<<<EOF
            <div class="form-group row"> 
              <label class="form-control-label pull-left">{$CMS->lang['upload_image']}</label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    
                    <div class="drop-zone fileinput-button" style="height:100px;">
                        <i class="font-icon font-icon-cloud-upload-2"></i>
                        <div class="drop-zone-caption">Drag file to upload</div>

                        <input type="file" multiple name="{$input_file_name}" id="multi_upload" class="multiple_upload" accept="image/*">
                    </div><!--.drop-zone-->
                    <p class='note_post'>{$CMS->lang["note_ext_image"]}</p> 
                    <div class="box_list_image" style="display: {$display};">
                      <ul class="list_image">
                        {$li_html}
                      </ul>
                    </div>
                  
              </div>
            </div>
EOF;
        return $output;        
    }

    public function sendEmailPopup($type = "")
    {
        global $CMS;

        if($type != "")
        {
            $input_type = $type;
        }
        else
        {
            $input_type = $CMS->input['type'];
        }
        $styleForm = $_SESSION['is_mobile'] ? "width: 90% !important;" : "";

        $output =<<<EOF
        <!-- Modal -->
<div class="modal fade" id="sendEmailPopup1" tabindex="-1" role="dialog" 
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:90%">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" 
                   data-dismiss="modal" style="float:right">
                       <span aria-hidden="true">&times;</span>
                       <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    {$CMS->lang['send_email']}
                </h4>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body">
                
                <form id="send_email_popup" name="sendEmailPopup form-horizontal" class="form_data" for="edit" method="post">
                    <input type="hidden" name="type" id="type" value="{$input_type}"/>
                    <input type="hidden" name="act" id="sub" value="send"/>
                    <input type="hidden" name="cus_id" id="cus_id"/>
                    <input type="hidden" name="email_from" id="email_from"/>
                    <input type="hidden" name="email_from_name" id="email_from_name"/>
                     <p class="error_msg" style="display:none;"></p>
                        <div class="row">
                            <fieldset class="form-group">
                                <label class="col-sm-3 col-md-2 control-label" for="email_send_to">{$CMS->lang['recipients']}</label>
                                <div class="col-sm-9 col-md-6">
                                    <select class="form-control tag_email" multiple="multiple" id="email_send_to" name="email_send_to[]"></select>
                                </div>
                            </fieldset>
                        </div>
                        <div class="row">
                            <fieldset class="form-group">
                                <label class="col-sm-3 col-md-2 control-label" for="email_cc">CC</label>
                                <div class="col-sm-9 col-md-6">
                                    <select class="form-control tag_email" multiple="multiple" id="email_cc" name="email_cc[]"></select>
                                </div>
                            </fieldset>
                        </div>
                        <div class="row">
                            <fieldset class="form-group">
                                <label class="col-sm-3 col-md-2 control-label" for="email_bcc">BCC</label>
                                <div class="col-sm-9 col-md-6">
                                    <select class="form-control tag_email" multiple="multiple" id="email_bcc" name="email_bcc[]"></select>
                                </div>
                            </fieldset>
                        </div>
                        <script>
                            $(document).ready(function(){
                                $("#send_email_popup .tag_email").select2({
                                  tags: true,
                                  width: '100%',
                                  createTag: function(term, data) {
                                        var value = term.term;
                                        var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                                        if(re.test(value)) {
                                            return {
                                              id: value,
                                              text: value
                                            };
                                        }
                                        return null;    
                                    } 
                                });  
                            })
                        </script>
                        <div class="row">
                            <fieldset class="form-group">
                                <label class="col-sm-3 col-md-2 control-label" for="email_title">{$CMS->lang['subject']}</label>
                                <div class="col-sm-9 col-md-6"> 
                                    <input type="text" name="email_title" id="email_title" class="form-control"  data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['trx_incomplete_title']}"/>
                                </div>
                            </fieldset>
                        </div>
                        <div class="row">
                            <fieldset class="form-group col-md-12">
                                <label class="control-label" for="email_content">{$CMS->lang['content']}</label>
                                <textarea class="form-control editor_texarea" rows="9" cols="30" name="email_content" id="email_content"></textarea>
                            </fieldset>
                        </div>
                        <div class="row">
                            <fieldset class="form-group col-xs-12" style="margin-bottom: 0">
                                <button type="submit" class="btn btn-primary" id="btn-send-email">{$CMS->lang['send']}</button>
                                <button type="button" class="btn btn-default"
                                        data-dismiss="modal">
                                            {$CMS->lang['close']}
                                </button>
                            </fieldset>
                        </div>
                </form>
                <script>
                 $(document).ready(function(){
                    validate_form_custom("#send_email_popup","#btn-send-email","send_email");
                });
                </script>
            </div>
            
            <!-- Modal Footer -->
            <div class="modal-footer">
                
                
            </div>
        </div>
    </div>
</div>
EOF;

        return $output;
    }

    /**
     * Footer Buttons
     * @param array $inputs
     * @return string
     */

    public function footer_details($inputs = array())
    {
        global $CMS, $DB;

        // Mobile detected
        if ( $inputs['module'] )
        {
            $inputs['act_show'] = $inputs['act_show'] ? $inputs['act_show'] : 'show';
            $inputs['act_edit'] = $inputs['act_edit'] ? $inputs['act_edit'] : 'edit';
            $inputs['act_deleted'] = $inputs['act_deleted'] ? $inputs['act_deleted'] : 'deleted';
            $output = '<section class="add_cart_footer">';
            $output .= <<<EOF
            <a href="{$CMS->vars['root_domain']}/?site={$inputs['module']}"  class="pull-left cancel hidden-sm-down">{$CMS->lang['back_list']}</a>
EOF;
            if ( $inputs['type'] == 'edit' )
            {
                $output .= <<<EOF
                <a href="{$CMS->vars['root_domain']}/?site={$inputs['module']}&act={$inputs['act_show']}&id={$inputs['detail_id']}"  class="pull-left cancel hidden-sm-down">{$CMS->lang['back_detail']}</a>
EOF;
            }
            $output .= <<<EOF
            <!-- mobile -->
            <div class="btn-group dropup pull-left hidden-md-up">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-mail-reply"></i>{$CMS->lang['back']}</button>
                <div class="dropdown-menu">
                    <ul>
                        <li><a class="hidden-md-up" href="{$CMS->vars['root_domain']}/?site={$inputs['module']}" title=""><i class="fa fa-mail-reply"></i>{$CMS->lang['back_list']}</a></li>
EOF;
            if ( $inputs['type'] == 'edit' )
            {
                $output .= <<<EOF
                <li><a class="hidden-md-up" href="{$CMS->vars['root_domain']}/?site={$inputs['module']}&act={$inputs['act_show']}&id={$inputs['detail_id']}" title=""><i class="fa fa-file-text-o"></i>{$CMS->lang['back_detail']}</a></li>
EOF;
            }
            $output .= <<<EOF
                    </ul>
                </div>
            </div>
EOF;
            if ( $inputs['type'] == 'add' )
            {
                $output .= <<<EOF
                <a class="btn btn-inline btn-primary ladda-button pull-right add_cart act_submit_save hidden-sm-down"value="detail" ><span class="ladda-label" >{$CMS->lang["save_and_detail"]}</span></a>
                <a class="btn btn-inline btn-primary ladda-button pull-right add_cart act_submit_save hidden-sm-down"value="list" ><span class="ladda-label" >{$CMS->lang["save_and_list"]}</span></a>
                <a class="btn btn-inline btn-primary ladda-button pull-right add_cart act_submit_save hidden-sm-down"value="add" ><span class="ladda-label" >{$CMS->lang["save_and_add"]}</span></a>
                <!-- mobile -->
                <div class="btn-group dropup pull-right hidden-md-up">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-save"></i>{$CMS->lang['save_option']}</button>
                    <div class="dropdown-menu">
                        <ul>
                            <li class="hidden-md-up"><a class="act_submit_save" value="detail" title=""><i class="fa fa-file-text-o"></i>{$CMS->lang["save_and_detail"]}</a></li>
                            <li class="hidden-md-up"><a class="act_submit_save" value="list" title=""><i class="fa fa-list-ol"></i>{$CMS->lang["save_and_list"]}</a></li>
                            <li class="hidden-md-up"><a class="act_submit_save" value="add" title=""><i class="fa fa-plus"></i>{$CMS->lang["save_and_add"]}</a></li>
                        </ul>
                    </div>
                </div>
                <input type="hidden" name="action_redirect" id="action_redirect" value="list" />
                <script>
                    $(".act_submit_save").click(function(){
                        $("#action_redirect").val($(this).attr("value"));
                    });
                </script>
EOF;
            }
            elseif ( $inputs['type'] == 'edit' )
            {
                $output .= <<<EOF
                <a class="btn btn-inline btn-primary ladda-button pull-right add_cart act_submit_save hidden-sm-down"value="detail" ><span class="ladda-label" >{$CMS->lang["edit_and_show"]}</span></a>
                <a class="btn btn-inline btn-primary ladda-button pull-right add_cart act_submit_save hidden-sm-down"value="list" ><span class="ladda-label" >{$CMS->lang["edit_and_list"]}</span></a>
                <a class="btn btn-inline btn-primary ladda-button pull-right add_cart act_submit_save hidden-sm-down"value="edit" ><span class="ladda-label" >{$CMS->lang["edit_and_edit"]}</span></a>
                <!-- mobile -->
                <div class="btn-group dropup pull-right hidden-md-up">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-save"></i>{$CMS->lang['edit_option']}</button>
                    <div class="dropdown-menu">
                        <ul>
                            <li class="hidden-md-up"><a class="act_submit_save" value="detail" title=""><i class="fa fa-file-text-o"></i>{$CMS->lang["edit_and_show"]}</a></li>
                            <li class="hidden-md-up"><a class="act_submit_save" value="list" title=""><i class="fa fa-list-ol"></i>{$CMS->lang["edit_and_list"]}</a></li>
                            <li class="hidden-md-up"><a class="act_submit_save" value="edit" title=""><i class="fa fa-plus"></i>{$CMS->lang["edit_and_edit"]}</a></li>
                        </ul>
                    </div>
                </div>
                <input type="hidden" name="action_redirect" id="action_redirect" value="list" />
                <script>
                    $(".act_submit_save").click(function(){
                        $("#action_redirect").val($(this).attr("value"));
                    });
                </script>
EOF;
            }
            // Desktop
            else
            {
                $output .= <<<EOF
                <a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site={$inputs['module']}&act={$inputs['act_deleted']}&id={$inputs['detail_id']}');" class="btn btn-inline btn-primary ladda-button pull-right add_cart hidden-sm-down">{$CMS->lang['gdelete']}</a>
                <a href="{$CMS->vars['root_domain']}/?site={$inputs['module']}&act={$inputs['act_edit']}&id={$inputs['detail_id']}" class="btn btn-inline btn-primary ladda-button pull-right add_cart hidden-sm-down">{$CMS->lang['gedit']}</a>
                <!-- mobile -->
                <div class="btn-group dropup pull-right hidden-md-up">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-save"></i>{$CMS->lang['gaction']}</button>
                    <div class="dropdown-menu">
                        <ul>
                            <li class="hidden-md-up"><a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site={$inputs['module']}&act={$inputs['act_deleted']}&id={$inputs['detail_id']}');" title=""><i class="fa fa-trash-o"></i>{$CMS->lang['gdelete']}</a></li>
                            <li class="hidden-md-up"><a href="{$CMS->vars['root_domain']}/?site={$inputs['module']}&act={$inputs['act_edit']}&id={$inputs['detail_id']}" title=""><i class="fa fa-edit"></i>{$CMS->lang['gedit']}</a></li>
                        </ul>
                    </div>
                </div>
EOF;
            }
            // End html

            $output .= '</section>';
        }

        return $output;

    }

    /**
     * @param $className
     * @param string $defaultLang
     * @return string
     */
    public function languageTab($className, $defaultLang = "")
    {
        global $CMS;

        if($CMS->vars['translations'])
        {
            $defaultLang = $defaultLang ? $defaultLang : $CMS->vars['default_language'];

            /**
             * Re-arrange translations by default language
             */
            $tmpTranslations[$defaultLang] = $CMS->vars['translations'][$defaultLang];

            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                if($langCode == $defaultLang) continue;
                $tmpTranslations[$langCode] = $langName;
            }
            $CMS->vars['translations'] = $tmpTranslations;


            $active[$defaultLang] = 'active';
            $defaultLabel[$defaultLang] = "<span class=\"label label-pill label-sm label-default\">Default</span>";

            $output = <<<EOF
        <section id="{$className}" class="tabs-section-nav tabs-section-nav-inline language-tabs-section-nav">
				<div class="tabs-section-nav tabs-section-nav-inline col-xl-12">
					<ul class="nav" role="tablist">
EOF;

            foreach ($CMS->vars['translations'] as $code => $name)
            {
                $output .= <<<EOF
                        <li class="nav-item">
							<a onclick="changeLangTab('{$className}', '{$code}')" class="nav-link {$active[$code]}" lang="{$code}">{$name} {$defaultLabel[$code]}</a>
						</li>
EOF;
            }

            $output .= <<<EOF
					</ul>
				</div><!--.tabs-section-nav-->
				<input name="lang_code" type="hidden" value="{$CMS->vars['default_language']}">
        </section>

        <script>
        $(document).ready(function(){
            $("#{$className} .nav-link.active").trigger('click');
        })
        </script>
EOF;
        }

        return $output;
    }

    public function importExportData($site='', $extend="", $isOverWrite=0, $link_download = null, $disabledExport=0, $disabledImport=0)
    {
        global $CMS;

        $link_download = $link_download ? $link_download : "/acp/assets/data/import_{$site}_example.xls";

        $action = $_SERVER['REQUEST_URI'];
        $actionData = explode('&',$action);

        foreach($actionData as $actKey => $actParam)
        {
            if(preg_match('/^act=(.*)$/',$actParam))
            {
                unset($actionData[$actKey]);
            }
        }

        $actionData[] = 'act=export';

        $actionUrl = implode('&',$actionData); //"{$CMS->vars['root_domain']}/?site={$site}&act=export{$extend}"

        $disabledExport = $disabledExport ? 'display:none' : '';
        $disabledImport = $disabledImport ? 'display:none' : '';

        $output = <<<EOF
        <script type="text/javascript" src="/acp/jsacp/download.js"></script>
        <form style="display: none" id="downloadFrm" method="post" action="{$actionUrl}">
            <input type="hidden" id="downloadToken" name="downloadToken" value="">
        </form>
        <a href="#" title="" class="bd-left btn_white downloadBtn" style="{$disabledExport}"><img id="loading_export" src="/acp/images/fb-loading.gif" style=" position: inherit; float: none; display: none" />{$CMS->lang['export_data']}</a>
        <a href="#form_import" title="" class="open_popup_customer mg-a bd-right btn_white" style="{$disabledImport}">{$CMS->lang['import_data']}</a>
        
        <div class="popup_importwhite-popup-block mfp-hide">
		<form id="form_import" action="{$CMS->vars['root_domain']}/?site={$site}&act=import{$extend}" method="post" enctype="multipart/form-data">
			<h3>{$CMS->lang['import_file']}</h3>
			<fieldset style="border:0;">
				<ul class="list_import">
					<li class="form-group">
						<div style="position:relative">
							<label for="name">{$CMS->lang['choose_file_upload']} {$CMS->lang['import_max_size_note']}</label>
							<input name="upload_file" type="file" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['emsg_not_empty_file']}" />
						</div>
EOF;

        if($isOverWrite)
        {
            $output.=<<<EOF
						<div class="checkbox">
							<input type="checkbox" name="is_overwrite" value="1" id="check-overwrite">
							<label for="check-overwrite">{$CMS->lang['note_overwrite_data']}</label>
						</div>
EOF;
        }


        $output .=<<<EOF
						<a href="{$link_download}">{$CMS->lang['download_file_sample']}</a>
EOF;

        if($site == 'config_general')
        {
            $output .= <<<EOF
						<!-- For import config -->
						{$CMS->lang['import_config_note']}
					</li>
EOF;
        }

        if($site == 'transactions')
        {
            $output .= <<<EOF
						<!-- For import transaction -->
						{$CMS->lang['import_transaction_note']}
					</li>
EOF;
        }


        if($isOverWrite)
        {
            $output .= <<<EOF
					<li><p class="warning_note">{$CMS->lang['warning_overwrite_data']}</p></li>
EOF;
        }


        $output .= <<<EOF
					<li>
						<div class="pull-right">
							<button type="button" class="btn btn-default btn_cancel" >{$CMS->lang['title_cancel_button']}</button>
							<button onclick="$('#loading_import').show()" type="submit" class="btn btn-primary"><img id="loading_import" src="/acp/images/fb-loading.gif" style=" position: inherit; float: none; display: none" />{$CMS->lang['import']}</button>
						</div>
					</li>
				</ul>
			</fieldset>
		</form>
	</div>

     <script>
        $('.open_popup_customer').magnificPopup({
				type: 'inline',
				preloader: false,
				focus: '#name',
				callbacks: {
					beforeOpen: function() {
						if($(window).width() < 700) {
							this.st.focus = false;
						} else {
							this.st.focus = '#name';
						}
					}
				}
			});

			$(".btn_cancel").click(function(){
				$.magnificPopup.close();
			});
    </script>
EOF;
        return $output;
    }

    public function print_barcode_popup()
    {
        global $CMS;

        $output = <<<EOF
        <div id="print_barcode_popup" class="popup_edit_product mfp-hide" style="clear: both; overflow: hidden;">
            <p style="font-weight: bold; text-transform: uppercase; font-size: 18px; text-align: center;">Chọn loại giấy in mã vạch</p>
                <p class="error_msg" style="display:none;"></p>
                <div class="row">
                    <div class="col-md-4">
                        <fieldset class="form-group">
                            <a href="" id="print_to_excel_button"><button class="btn btn-icon"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Xuất file excel</button></a>
                        </fieldset>
                        <fieldset class="form-group">
                            Lưu ý: File Exel xuất ra để sử dụng thiết kế mẫu in mã vạch trên các phần mềm chuyên nghiệp khác
                        </fieldset>
                        <hr />
                        <fieldset class="form-group">
                            Lưu ý: Trong trường hợp Mã vạch được in không đọc được, vui lòng sử dụng Mẫu giấy lớn hơn hoặc rút ngắn mã hàng.
                        </fieldset>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-3">
                                <img width="100%" src="{$CMS->vars['img_url']}/honeywell-pc-42t-printer-1881561.jpg">
                            </div>
                            <div class="col-md-9">
                                <p>Honeywell PC 42t printer (Khổ giấy in nhãn 110mm/104.1mm)</p>
                                <button class="btn btn-primary" id="preview_barcode_button"><i class="fa fa-barcode" aria-hidden="true"></i> Xem bản in</button>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 20px">
                            <div class="col-md-3">
                                <img width="100%" src="{$CMS->vars['img_url']}/honeywell-pc-42t-printer-1881561.jpg">
                            </div>
                            <div class="col-md-9">
                                <p>In khổ lớn kèm QR code</p>
                                <button class="btn btn-primary" id="preview_barcode_button1"><i class="fa fa-barcode" aria-hidden="true"></i> Xem bản in</button>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
EOF;
        return $output;
    }

    public function preview_barcode_popup()
    {
        global $CMS;

        $output  = <<<EOF
        <div id="preview_barcode_popup" class="mfp-hide" style="clear: both; overflow: hidden;height:50%"  >
  <p class="title_add title_change_cus" style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;">{$CMS->lang['print_barcode']}</p>

  	<div class="pdfContent" data-dojo-attach-point="_pdfContent" style="width:100%;" height="100%">
  		<iframe src="" width="100%" height="400px" frameborder="0"></iframe>

  		</div>

  		<ul class="list_field_supplier">
                    <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="min-height: auto;">
                        <fieldset class="form-group">
                            <label class="form-control-label"></label>
                            <div class="typeahead-field"> 
                                <span style="float:right; margin-left: 5px"><a target="_blank" id="print_barcode" href="" width="100%" height="400px" frameborder="0" class="btn btn_add_cus" type="button" style="float:right" >{$CMS->lang['print_barcode']}</a></span>
                                <span style="float:right"><a target="_blank" id="export_barcode_to_excel" href="" width="100%" height="400px" frameborder="0" class="btn btn_add_cus" type="button" style="float:right" >{$CMS->lang['export_excel']}</a></span>
                            </div>
                        </fieldset>
                    </li>
                
                </ul>
  </div>
EOF;
        return $output;
    }

    function headerQuickSearch($remoteUrl = "")
    {
        global $CMS;

        $keyword = urldecode($CMS->input['keyword']);

        $output = <<<EOF
        <div class="search">
                <form method="get" id="formquicksearch_adv"  action="{$CMS->vars['root_domain']}/" style="display:inline-block">
                    <input type="hidden" name="site" value="{$CMS->input['site']}">
                    <div class="input-group">
                        <span class="input-group-addon"><input type="submit" class="fa-input" value="&#xf002;"></span>
                        <input name="keyword" id="header-quick-search" type="text" value="{$keyword}" autocomplete="off" minlength="2" maxlength="64" placeholder="{$CMS->lang['gsearch_quick']}">
                    </div>
                    <div class="b-loader header-quick-search-loader" id="loader-quick-search" style="display:none"></div>
                </form>
                <a id="expand_formsearch" title="">{$CMS->lang['gsearch_advance']}<i class="fa fa-angle-double-right"></i></a>
        </div>
        <script>
            headerQuickSearch($("#header-quick-search"), "{$remoteUrl}", $("#loader-quick-search"));
        </script>
EOF;

        return $output;

    }

    /**
     * Form để cấu hình huê hồng dùng chung
     * @param $data
     * @return string
     * NKVP -
     */
    function show_commission($data, $checked_input = [])
    {
        global $CMS;

        $output = <<<EOF
        <section class="add_table">
            <div class="data_table">
                <div class="table-responsive">
                    <table id="example" class="display table table_cus table_commission" cellspacing="0" width="100%">
						<thead>
						    <tr>
						        <th width="5%">ID</th>
						        <th>{$CMS->lang['name']}</th>
						        <th>{$CMS->lang['gcommission']}</th>
						    </tr>
						</thead>
						<tbody>
EOF;

        foreach($data as $item)
        {

            $checked = $checked_input['product_id_'.$item['product_id']] ? 'checked' : '';

            $detail = $item['product_type'] ? "{$CMS->vars['root_domain']}/?site=service&act=show&id={$item['product_id']}" : "{$CMS->vars['root_domain']}/?site=product&act=show&id={$item['product_id']}";

            $output .= <<<EOF
						    <tr>
						        <td>#{$item['product_id']}</td>
						        <td style="white-space: normal;"><a href="{$detail}">{$item['product_name']}</a></td>
						        <td>
						            <div class="input-group">
						              <input commission_name="product_type" commission_id="{$item['product_id']}" type="hidden" value="{$item['product_type']}" id="product_type_{$item['product_id']}">
                                      <input onchange="commissionMergeInputs();" commission_name="product_commission_value" commission_id="{$item['product_id']}" value="{$item['product_commission_value']}" id="product_commission_value_{$item['product_id']}" type="number" step="0.01" class="form-control" aria-label="Text input with dropdown button">
                                      <input onchange="commissionMergeInputs();" commission_name="product_commission_type" commission_id="{$item['product_id']}" value="{$item['product_commission_type']}" id="product_commission_type_{$item['product_id']}" type="hidden">
                                      <div id="product_commission_dropdown_{$item['product_id']}" class="input-group-btn">
                                        <button name="btn_action[{$item['product_id']}]" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #bebebe; border-color: #bebebe; color: #6c7a86; padding: 7px 10px;">%</button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                          <a class="dropdown-item" value="0">%</a>
                                          <a class="dropdown-item" value="1">$</a>
                                        </div>
                                      </div>
                                        <script>
                                            dropdownInput($('#product_commission_dropdown_{$item['product_id']}'), $('#product_commission_type_{$item['product_id']}'));
                                        </script>
                                    </div>
                                </td>
                            </tr>
EOF;
        }


        $output .= <<<EOF
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        <input type="hidden" id="commission_data" name="commission_data" value="" />
        <script >
            commissionMergeInputs();
        </script>
EOF;
        return $output;
    }

} //END Class
