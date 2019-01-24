<?php
use \core\ezy;

if (!defined('IN_ROOT')) exit();
ezy::load_model("logos");
ezy::load_model("logo_positions");

new logos;

class logos
{
    public $html;

    /**
     * logos constructor.
     */
    public function __construct()
    {
        global $CMS, $DB, $member, $tpl;

        $CMS->class->language->load("logos");

        $tpl->dataPositions = \models\logo_positions::getAll();

        // get key by id position

        $posis=array();
        foreach ($tpl->dataPositions as $posi){
            $posis[$posi['pos_id']]=$posi;
        }
        $tpl->dataPositions = $posis;

        // end

        switch ($CMS->input['act'])
        {
            case 'html_script':
              $this->htmlScript();
            break;

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
                if(\lib\input::get('subact') == 'clear_cache')
                {
                    $CMS->class->cache->mdelete('logos');
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

        $tpl->quickSearchDisplay = $CMS->input['act'] == 'search_do' ? '' : 'display:none';

        $tpl->data = \models\logos::listing();

        $tpl->posSelected[$CMS->input['logo_position']] = 'selected';

        // Output data
        $CMS->output .= ezy::html("main");
    }

    /**
     * Add form
     */
    public function add()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['add_banner'];

        $tpl->header_title = $CMS->lang['add_banner'];
        $tpl->act = 'add_do';

        if($CMS->input['act'] == 'add_do')
        {

            if($newData = \models\logos::add($CMS->input))
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

        $tpl->data['logo_upload_option'] = intval($CMS->input['logo_upload_option']);

        $tpl->data = $CMS->input;
        $tpl->posSelected[$tpl->data['logo_position']] = 'selected';
        $out .=<<<EOF
            <textarea type="text" id='designtext' name='designtext[]' placeholder='Text' data-id="1" class='form-control  designtext designtext1' style="margin-bottom:5px">Add text</textarea>
EOF;
     
  
        $tpl->data['designtext'] = $out;

        $out_2 .=<<<EOF
                        <div class='text text1 t designtext1 no-delete' style="" data-id="1">
                            <i class="icon-remove action text-error" data-action = 'remove'></i>
                            <p style="border: 1px dashed #fff;">Add text</p>
                          
                            <i class="icon-edit action" data-action = 'fsontSize'></i>
                             <i class="icon-rotation action fa fa-rotate-left" id="rotation" style="position:absolute;bottom: 5;right: 5;height: 10;width: 10;z-index:21;margin-left:4px" data-action = 'rotation'></i>
                        </div> 
EOF;
        $tpl->data['designstyle'] = $out_2;
        // Output data
        $CMS->output .= ezy::html("form");
    }

    /**
     * Edit form
     */
    public function edit()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['edit_banner'];

        $tpl->header_title = $CMS->lang['edit_banner'];
        $tpl->act = 'edit_do';

        if($CMS->input['act'] == 'edit_do')
        {
            if(\models\logos::edit($CMS->input))
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

        $tpl->data = \models\logos::getInfo($CMS->input['id']);
        $tpl->data = \models\logos::editValue($tpl->data);

        $tpl->data = array_merge($tpl->data, $CMS->input);
        $tpl->posSelected[$tpl->data['logo_position']] = 'selected';

         $product_style = json_decode($tpl->data['logo_style_content'],true);
         $product_style = array_reverse($product_style);
        $stt = count($product_style); 
        if($stt > 0)
        {
            foreach($product_style as $key => $value ) {
            if($stt == 1){ $first_el = " id='designtext' ";  }
            else { $first_el = " ";  }

                if($value['text'] != "")
                {
                 
                 $out .=<<<EOF
                   <textarea type="text"  {$first_el} name='designtext[]' placeholder='Text' data-id="{$stt}" class='form-control  designtext designtext{$stt}' style="margin-bottom:5px">{$value['text']}</textarea>
EOF;
                          $stt--;
                }  
            }                                 

         }
        else
        { 
            $out .=<<<EOF
            <textarea type="text" id='designtext' name='designtext[]' placeholder='Text' data-id="1" class='form-control  designtext designtext1' style="margin-bottom:5px">Add text</textarea>
EOF;
        }  
  
        $tpl->data['designtext'] = $out;

        //Check image original
        if($tpl->data['logo_image_original'] != "")
        {
            $tpl->data['logo_image_original_src'] = "{$CMS->vars['upload_url']}/{$tpl->data['logo_image_original']}";
        }

        $stt = count($product_style); 
        if(count($product_style) > 0)
        {
            foreach ($product_style as $key => $value) {
               if($key == 0){ $no_del = "no-delete";}
               else{  $no_del = " "; }
               // Get font
               //
                if($stt == 1){ $first_text = " text ";  }
                else {$first_text = " ";}
               $style_p_push = explode("; ", $value['style_p_push']);
               
               foreach ($style_p_push as $key_s => $value_s) {

                 $s_2 = explode(":", $value_s);
                 if($s_2[0] == "font-family" )
                 {     
                    $s_2[1] = str_replace('&quot;', "", $s_2[1]);
                    $s_2[1] = rtrim($s_2[1],";"); 
                    $s_2[1] = trim($s_2[1]);
                    $font .=  $s_2[1].",";
                 }
               }
              if($value['text'] != "")
              {
                 
               $out_2 .=<<<EOF
                <div class='{$first_text} text{$stt} t designtext1 {$no_del}' style="{$value['style_push']}" data-id="{$stt}">
                    <i class="icon-remove action text-error" data-action = 'remove'></i>
                    <p style="border: 1px dashed #fff;{$value['style_p_push']}">{$value['text']}</p>
                  
                    <i class="icon-edit action" data-action = 'fsontSize'></i>
                     <i class="icon-rotation action fa fa-rotate-left" id="rotation" style="position:absolute;bottom: 5;right: 5;height: 10;width: 10;z-index:21;margin-left:4px" data-action = 'rotation'></i>
                </div> 
EOF;
                        $stt--;
                       } 
                    }
                    
                   
                  
                     $out_2 .=<<<EOF

                    <script>
                        $('#printable .t').draggable({ containment: "#printable" })
                         $('#printable .t').find('p').resizable();
                       
                         $('#printable .t').find('.icon-rotation').draggable({ 
                             opacity: 0.01, 
                              helper: 'clone',
                            drag: function(event, ui){
                                 console.log(ui.position.left);
                                var rotateCSS = 'rotate(' + ui.position.left + 'deg)';

                                $(this).parent().css({
                                    '-moz-transform': rotateCSS,
                                    '-webkit-transform': rotateCSS
                                });
                          }      
                       }); 
                        
                    </script>
                    
                  
EOF;
                   $font_nk = explode(",", $font);
                    foreach ($font_nk as $key_f => $value_f) {
                        if($value_f != "")
                        {
                          $new_font_f .= "'".$value_f."',";
                        }
                    }
               
                  $new_font_f = rtrim($new_font_f,",");
                  if($new_font_f != "")
                  {
                    $new_font_f = trim($new_font_f);  
                    $out_2 .=<<<EOF

                    <script src="http://ajax.googleapis.com/ajax/libs/webfont/1.4.7/webfont.js"></script> 
                  <script> 
                        WebFont.load({
                                    google: { 
                                           families: [ {$new_font_f}] 
                                     } 
                         }); 
                   </script>    

EOF;
                  }
            }//End if count
            else
            {
               $out_2 .=<<<EOF
                        <div class='text text1 t designtext1 no-delete' style="" data-id="1">
                            <i class="icon-remove action text-error" data-action = 'remove'></i>
                            <p style="border: 1px dashed #fff;">Add text</p>
                          
                            <i class="icon-edit action" data-action = 'fsontSize'></i>
                             <i class="icon-rotation action fa fa-rotate-left" id="rotation" style="position:absolute;bottom: 5;right: 5;height: 10;width: 10;z-index:21;margin-left:4px" data-action = 'rotation'></i>
                        </div> 
EOF;
            }

            $tpl->data['designstyle'] = $out_2;


        $CMS->output .= ezy::html("form");
    }

    /**
     * Delete route
     */

    public function delete()
    {
        global $CMS;

        // Delete
        \models\logos::delete();

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
        \models\logos::mdelete();

        // Redirect
        $CMS->global->redirectReferer();
    }

    /**
     * Show detail
     */
    public function show()
    {
        global $CMS, $tpl;

        $data = \models\logos::getInfo();

        if(!$data)
        {
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            $CMS->global->redirectReferer("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
        }

        $tpl->data = models\logos::convertValue($data);

        // Output data
        $CMS->output .= ezy::html("show");
    }

    /**
     * Views html script for ad
     */
    public function htmlScript()
    {
        global $CMS, $tpl;

        // is ajax
        $is_ajax = intval($CMS->input['is_ajax']);

        // Get data
        $data = \models\logos::getInfo();
        if( ! $data )
        {
            if( $is_ajax )
            {
                echo $CMS->lang['data_not_found']; exit;
            }
            
            $_SESSION['msg'] = $CMS->lang['data_not_found'];
            $CMS->global->redirectReferer("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
        }
        $tpl->data = models\logos::convertValue($data);

        if( $is_ajax )
        {
            echo \core\ezy::render('html_script', 'logos'); exit;
        }
        
        // Output data
        $CMS->output .= ezy::html("html_script");
    }
}