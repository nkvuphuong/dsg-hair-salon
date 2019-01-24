<?php

class skin_config_gallery {

//===========================================================================
//  HTML HEADER
//===========================================================================

public function header()
{
	global $CMS, $DB, $member;
	
	$output = "";      

$output .= <<<EOF

  <section class="add_table main_form">
    <figure class="heading">
      <h3>List gallery</h3>
      <figure class="pull-right right">        
EOF;
          if($CMS->permit['config_gallery_add'])
          {
$output .=<<<EOF

        <a href="{$CMS->vars['root_domain']}/?site=config_gallery&act=add" title="" class="add_bill">{$CMS->lang['add_form']}</a>
EOF;
          }
$output .=<<<EOF
            <a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a>
      </figure>
    </figure>
        
<section class="tabs-section" style="margin-bottom:15px">
    <div class="tabs-section-nav tabs-section-nav-inline">
        <ul class="nav" role="tablist">
            <li class="nav-item">
                <a class="nav-link" href="{$CMS->vars['root_domain']}/?site=gallery">
                    {$CMS->lang['gallery_header']}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{$CMS->vars['root_domain']}/?site=config_gallery">
                    {$CMS->lang['title_list_category_gallery']}
                </a>
            </li>
        </ul>
    </div><!--.tabs-section-nav-->
</section><!--.tabs-section--> 
{$CMS->global->languageTab('langTab')}                   
<form method="post" name="config_gallery" id="config_gallery" action="{$CMS->vars['root_domain']}/?site=config_gallery" onSubmit="return check_form(this.id);">
    <section class="add_table"> 
        <div class="table-responsive">
          <table class="table_cus">
            <tr>
              <thead>
                <th width="7%" id="order_cat_id">{$CMS->lang['cat_id']}</th>
                <th width="20%" id="order_cat_name">{$CMS->lang['cat_name']}</th>
                <th width="30%">{$CMS->lang['cat_description']}</th>
                <th width="20%" id="order_cat_status">{$CMS->lang['cat_status']}</th>
                <th width="20%" id="order_cat_time">{$CMS->lang['cat_time']}</th>
                <th width="5%"></th>
              </thead>
            </tr>


EOF;

	return $output;
}


//===========================================================================
//  HTML MIDDLE
//===========================================================================

public function middle($result)
{
	global $CMS, $DB, $member;
	
	$output = "";
  $btn_control = "";
  if($CMS->permit['config_gallery_edit'])
  {
    $btn_control .=<<<EOF
      <a class="edit" href="{$CMS->vars['root_domain']}/?site=config_gallery&act=edit&id={$result['cat_id']}"><i class="fa fa-edit"></i></a>
EOF;

  }

  if($CMS->permit['config_gallery_delete'])
  {
    $btn_control .=<<<EOF
      <a class="edit" onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=config_gallery&act=delete&id={$result['cat_id']}')"><i class="fa fa-trash"></i></a>
EOF;

  }

$output .= <<<EOF
  <tr bgcolor="{$result['bgcolor']}">

    <td>
    	<select class="form-control" name="order_{$result['cat_id']}">
			<script language="javascript">
            
                    for ( var i = 1; i <= {$CMS->config_gallery->pages_cnt}; i ++ )
                    {
                        if ( i == {$result['cat_order']} )
                        {
                            document.writeln("<option name='option_"+i+"' value='"+i+"' selected>"+i+"</option>");
                        }
                        else
                        {
                            document.writeln("<option name='option_"+i+"' value='"+i+"'>"+i+"</option>");
                        }
                    }
            
                </script>
          </select>
    </td>
EOF;

        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $output .= <<<EOF
                <td class="langTab" lang="{$langCode}">{$result['cat_name_bk'][$langCode]}</td>
                <td class="langTab" lang="{$langCode}">{$result['cat_description'][$langCode]}</td>
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
                <td>{$result['cat_name']}</td>
                <td>{$result['cat_description']}</td>
EOF;
        }
$output .= <<<EOF

    <td>{$result['cat_status']}</td>
    <td>{$result['cat_time']}</td>
    <td align="center">{$btn_control}</td>
  </tr>
EOF;
	
    return $output;
}



//===========================================================================
//  NO DATA
//===========================================================================

public function none()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
  <tr>
    <td colspan="9">{$CMS->lang['no_data']}</td>
  </tr>
EOF;

	return $output;
}

//===========================================================================
//  HTML FOOTER
//===========================================================================

public function footer()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
</table>
    <div class="fuction_table">

      <div class="pull-left">
        <p class="form-control-static ">
            {$CMS->config_gallery->action_control}
        </p>
      </div>
      <nav class="pull-right">
         {$CMS->show_page}
      </nav>
   </div>
</form>

<script language="javascript">arrange_setup("{$CMS->config_gallery->arrange_data}");</script>
EOF;

	return $output;
}

//===========================================================================
//  USER CONTROL
//===========================================================================

public function control()
{
	global $CMS, $DB, $member;
    
	$output = "";

$output .= <<<EOF

  <select class="form-control" name="act" onchange="this.form.submit()">
    {$CMS->vars['action_controller']}
  </select>
  
EOF;

    return $output;
}

//===========================================================================
//  HTML EDIT
//===========================================================================

public function edit( $data , $tplformhair_custom = "" )
{
	global $CMS, $DB, $member;
	
	$output = "";
$CMS->config_gallery->load_parent_category();
$src_image_upload = $CMS->vars['upload_url']."/gallery/{$data['cat_image']}";

$output .= <<<EOF
<form method="post" id="config_gallery" name="config_gallery" action="{$CMS->vars['root_domain']}/?site=config_gallery&id={$data['cat_id']}&act=edit_do&page={$CMS->input['page']}" onSubmit="return check_form(this.id);" enctype="multipart/form-data">
<section class="add_form main_form">
      <figure class="heading">
        <h3>{$CMS->lang['edit_form']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=config_gallery{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>
      
      {$CMS->global->languageTab('langTab')}
      <figure class="box-typical box-typical box-typical-padding border">
        <div class="row">
          <div class="col-lg-4">
EOF;

        $cat_name = isset($CMS->input['cat_name']) ? $CMS->input['cat_name'] : $data['cat_name'];
        $cat_description = isset($CMS->input['cat_description']) ? $CMS->input['cat_description'] : $data['cat_description'];
        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $output .= <<<EOF
          <fieldset class="form-group row langTab" lang="{$langCode}">
              <label class="form-control-label">{$CMS->lang['cat_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <input class="form-control" type="text" name="cat_name[{$langCode}]" value="{$cat_name[$langCode]}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['incomplete_name']}"/>
              </div>
          </fieldset>
        
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['cat_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                      <input class="form-control" type="text" name="cat_name" value="{$data['cat_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['incomplete_name']}"/>
                  </div>
                </div>
EOF;
        }    

$output .=<<<EOF

                <div class="form-group">
                  <label class="form-label pull-left" >{$CMS->lang['gallery_image']}</label>
                      <div class="actionButtons pull-right">
                          <ul>
                              <li onclick="return delete_fileToAttach();">
                                  <i class="fa fa-trash-o"></i>
                              </li>
                          </ul>
                          <input  type="hidden" id="ufile_output_b64" name="base64_image" value="" />
                      </div>
                     
                      <div class="drop-zone fileinput-button" style="height: 150px !important">
                          <img id="upload_img_show" width="205" src="{$src_image_upload}" {$style_display} style="margin: auto;max-height: 130px;max-width: 200px; display: block;" />
                          <i class="font-icon font-icon-cloud-upload-2"></i>
                          <div class="drop-zone-caption">Drag file to upload</div>
                              <input type="file" name="cat_image" id="ufile" accept="image/*">
                      </div><!--.drop-zone-->
                </div>
EOF;
        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $output .= <<<EOF
          <fieldset class="form-group row langTab" lang="{$langCode}">
              <label class="form-control-label">{$CMS->lang['cat_description']}</label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <textarea class="form-control" rows="5" name="cat_description[{$langCode}]">{$cat_description[$langCode]}</textarea>
              </div>
          </fieldset>
        
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['cat_description']}</label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <textarea class="form-control" rows="5" name="cat_description">{$data['cat_description']}</textarea>
                  </div>
                </div>
EOF;
        }    

$output .=<<<EOF


                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['cat_is_hot']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <select class="form-control auto_select" name="cat_is_hot" defaultvalue="{$data['cat_is_hot']}">
                      <option value="0">{$CMS->lang['no']}</option>
                      <option value="1">{$CMS->lang['yes']}</option>
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['cat_status']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <select class="form-control auto_select" name="cat_status" defaultvalue="{$data['cat_status_bk']}"data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['incomplete_status']}">{$CMS->vars['display_status']}</select>
                  </div>
                </div>
EOF;

                if($tplformhair_custom != "")
                {
                  $output .=<<<EOF
                  {$tplformhair_custom}
EOF;
                }

                 $output .=<<<EOF
                <div class="form-group row">
                  <label class="form-control-label"></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <button class="btn" type="submit">{$CMS->lang['edit_submit']}</button>
                  </div>
                </div>

          </div> <!--End col-lg-4-->

          <div class="col-lg-8">
                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['meta_title']}</label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <textarea class="form-control" rows="4" name="meta_title">{$data['meta_title']}</textarea>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['cat_description']}</label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <textarea class="form-control" rows="4" name="meta_description">{$data['meta_description']}</textarea>
                  </div>
                </div>


                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['cat_description']}</label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <textarea class="form-control" rows="4" name="meta_keywords">{$data['meta_keywords']}</textarea>
                  </div>
                </div>
          </div><!--End col-lg-8-->
        </div>
      </figure>
    </section>
</form>
<script language="javascript">
  $(document).ready(function(){
    validate_form_custom("#config_gallery");


  });
</script>
EOF;
	
	return $output;	
}

//===========================================================================
//  HTML ADD
//===========================================================================

public function add( $data  , $tplformhair_custom = "")
{
	global $CMS, $DB, $member, $tpl;

	$CMS->config_gallery->load_parent_category();
  $src_image_upload = $CMS->vars['upload_url']."/gallery/{$data['cat_image']}";
	
$output = <<<EOF
<form method="post" id="config_gallery" name="config_gallery" action="{$CMS->vars['root_domain']}/?site=config_gallery&act=add_do" onSubmit="return check_form(this.id);" enctype="multipart/form-data">

  <section class="add_form main_form">
      <figure class="heading">
        <h3>{$CMS->lang['add_form']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=config_gallery{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>

      {$CMS->global->languageTab('langTab')}
      <figure class="box-typical box-typical-padding border">
        <div class="row">
          <div class="col-lg-4">
EOF;
        $cat_name = isset($CMS->input['cat_name']) ? $CMS->input['cat_name'] : $data['cat_name'];
        $cat_description = isset($CMS->input['cat_description']) ? $CMS->input['cat_description'] : $data['cat_description'];

        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $output .= <<<EOF
          <fieldset class="form-group row langTab" lang="{$langCode}">
              <label class="form-control-label">{$CMS->lang['cat_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <input class="form-control" type="text" name="cat_name[{$langCode}]" value="{$cat_name[$langCode]}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['incomplete_name']}"/>
              </div>
          </fieldset>
        
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['cat_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                      <input class="form-control" type="text" name="cat_name" value="{$data['cat_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['incomplete_name']}"/>
                  </div>
                </div>
EOF;
        }    

$output .=<<<EOF


                <div class="form-group">
                  <label class="form-label pull-left" >{$CMS->lang['gallery_image']}</label>
                      <div class="actionButtons pull-right">
                          <ul>
                              <li onclick="return delete_fileToAttach();">
                                  <i class="fa fa-trash-o"></i>
                              </li>
                          </ul>
                          <input  type="hidden" id="ufile_output_b64" name="base64_image" value="" />
                      </div>
                     
                      <div class="drop-zone fileinput-button" style="height: 150px !important">
                          <img id="upload_img_show" width="205" src="{$src_image_upload}" {$style_display} style="margin: auto;max-height: 130px;max-width: 200px;" />
                          <i class="font-icon font-icon-cloud-upload-2"></i>
                          <div class="drop-zone-caption">Drag file to upload</div>
                              <input type="file" name="cat_image" id="ufile" accept="image/*">
                      </div><!--.drop-zone-->
                </div>
EOF;
        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $output .= <<<EOF
          <fieldset class="form-group row langTab" lang="{$langCode}">
              <label class="form-control-label">{$CMS->lang['cat_description']}</label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <textarea class="form-control" rows="5" name="cat_description[{$langCode}]">{$cat_description[$langCode]}</textarea>
              </div>
          </fieldset>
        
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['cat_description']}</label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <textarea class="form-control" rows="5" name="cat_description">{$data['cat_description']}</textarea>
                  </div>
                </div>
EOF;
        }    

$output .=<<<EOF


                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['cat_is_hot']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <select class="form-control auto_select" name="cat_is_hot" defaultvalue="{$data['cat_is_hot']}">
                      <option value="0">{$CMS->lang['no']}</option>
                      <option value="1">{$CMS->lang['yes']}</option>
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['cat_status']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <select class="form-control auto_select" name="cat_status" defaultvalue="{$data['cat_status']}"data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['incomplete_status']}">{$CMS->vars['display_status']}</select>
                  </div>
                </div>
EOF;

                if($tplformhair_custom != "")
                {
                  $output .=<<<EOF
                  {$tplformhair_custom}
EOF;
                }

                 $output .=<<<EOF
                <div class="form-group row">
                  <label class="form-control-label"></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <button class="btn" type="submit">{$CMS->lang['add_submit']}</button>
                  </div>
                </div>

          </div> <!--End col-lg-4-->

          <div class="col-lg-8">
                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['meta_title']}</label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <textarea class="form-control" rows="4" name="meta_title">{$data['meta_title']}</textarea>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['cat_description']}</label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <textarea class="form-control" rows="4" name="meta_description">{$data['meta_description']}</textarea>
                  </div>
                </div>


                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['cat_description']}</label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <textarea class="form-control" rows="4" name="meta_keywords">{$data['meta_keywords']}</textarea>
                  </div>
                </div>
          </div><!--End col-lg-8-->
        </div>
      </figure>
    </section>

</form>
<script language="javascript">
  $(document).ready(function(){
    validate_form_custom("#config_gallery");


  });
</script>
EOF;
	
	return $output;	
}

//===========================================================================
//  HTML SHOW
//===========================================================================

public function show( $data )
{
	global $CMS, $DB, $member;
	
	$output = "";
    // print "<pre>"; print_r($data);exit;
$output .= <<<EOF
<section class="add_form main_form">
  <figure class="heading">
EOF;

        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $output .= <<<EOF
                <h3 class="langTab" lang="{$langCode}">{$CMS->lang["show_form"]}<b> {$data['cat_name'][$langCode]}</b></h3>
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
               <h3>{$CMS->lang["show_form"]}<b> {$data['cat_name']}</b></h3>
EOF;
        }
$output .=<<<EOF
    
      <a href="{$CMS->vars['root_domain']}/?site=config_gallery" title=""><span class="font-icon font-icon-del"></span></a>
  </figure>
  
  {$CMS->global->languageTab('langTab')}
  <div class="box-typical box-typical-padding border">
    <div class="row">
      <div class="col-xl-4 col-md-6 col-sm-6 col-xs-12">
EOF;

        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $output .= <<<EOF
                <fieldset class="row langTab" lang="{$langCode}">
                  <div class="form-control-label2" >
                      <div class="title_label title-label-125 pull-left">{$CMS->lang['cat_name']}</div>
                      <div class="form-control-span2">{$data['cat_name_bk'][$langCode]}</div>
                  </div>
                </fieldset>

                <fieldset class="row langTab" lang="{$langCode}">
                  <div class="form-control-label2" >
                      <div class="title_label title-label-125 pull-left">{$CMS->lang['cat_description']}</div>
                      <div class="form-control-span2">{$data['cat_description'][$langCode]}</div>
                  </div>
                </fieldset>
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
                <fieldset class="row">
                  <div class="form-control-label2" >
                      <div class="title_label title-label-125 pull-left">{$CMS->lang['cat_name']}</div>
                      <div class="form-control-span2">{$data['cat_name']}</div>
                  </div>
                </fieldset>
                <fieldset class="row">
                  <div class="form-control-label2" >
                      <div class="title_label title-label-125 pull-left">{$CMS->lang['cat_description']}</div>
                      <div class="form-control-span2">{$data['cat_description']}</div>
                  </div>
                </fieldset>
EOF;
        }
$output .=<<<EOF

        
        <fieldset class="row">
          <div class="form-control-label2" >
              <div class="title_label title-label-125 pull-left">{$CMS->lang['cat_status']}</div>
              <div class="form-control-span2">{$data['cat_status']}</div>
          </div>
        </fieldset>

        <fieldset class="row">
          <div class="form-control-label2" >
              <div class="title_label title-label-125 pull-left">{$CMS->lang['cat_time']}</div>
              <div class="form-control-span2">{$data['cat_time']}</div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-4 col-md-6 col-sm-6 col-xs-12">

        <fieldset class="row">
          <div class="form-control-label2" >
              <div class="title_label title-label-125 pull-left">{$CMS->lang['meta_title']}</div>
              <div class="form-control-span2">{$data['meta_title']}</div>
          </div>
        </fieldset>
        
        <fieldset class="row">
          <div class="form-control-label2" >
              <div class="title_label title-label-125 pull-left">{$CMS->lang['meta_description']}</div>
              <div class="form-control-span2">{$data['meta_description']}</div>
          </div>
        </fieldset>
        <fieldset class="row">
          <div class="form-control-label2" >
              <div class="title_label title-label-125 pull-left">{$CMS->lang['meta_keywords']}</div>
              <div class="form-control-span2">{$data['meta_keywords']}</div>
          </div>
        </fieldset>

        

      </div>
    </div>
  </div>
</section>

EOF;
    $btn_control = "";
    $btn_control_mobile = "";

    if($CMS->permit['config_gallery_delete'])
    {
$btn_control .=<<<EOF

      <a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=config_gallery&act=delete&id={$data['cat_id']}');" class="pull-right add_cart_2 hidden-sm-down">{$CMS->lang['delete']}</a>
EOF;

$btn_control_mobile .=<<<EOF

      <li class="hidden-xl-up"><a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=config_gallery&act=delete&id={$data['cat_id']}');" title=""><i class="fa fa-trash-o"></i>{$CMS->lang['delete']}</a></li>
EOF;
    }

    if($CMS->permit['config_gallery_edit'])
    {
$btn_control .=<<<EOF

      <a href="{$CMS->vars['root_domain']}/?site=config_gallery&act=edit&id={$data['cat_id']}" class="pull-right add_cart_2 hidden-sm-down">{$CMS->lang['edit']}</a>
EOF;

$btn_control_mobile .=<<<EOF

      <li class="hidden-xl-up"><a href="{$CMS->vars['root_domain']}/?site=config_gallery&act=edit&id={$data['cat_id']}" title=""><i class="fa fa-edit"></i>{$CMS->lang['edit']}</a></li>  
EOF;
    }

$output .=<<<EOF

<section class="add_cart_footer">
  <a href="{$CMS->vars['root_domain']}/?site=config_gallery" class="pull-left cancel">{$CMS->lang['header_back']}</a>
  {$btn_control}

  <div class="btn-group dropup pull-right hidden-xl-up">
    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <i class="fa fa-save"></i>{$CMS->lang['title_action']}
    </button>
    <div class="dropdown-menu">
    <ul>
      {$btn_control_mobile}
    </ul>
    </div>
  </div>


</section>

EOF;
	
	return $output;	
}


  public function list_hair_category($cat_default = "")
  {
    global $CMS;
    $cat_default = intval($cat_default);
    $output .=<<<EOF

  <div class="form-group row">
    <label class="form-control-label">{$CMS->lang['hair_category']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
    <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
      <select class="form-control auto_select" name="cat_cate" defaultvalue="{$cat_default}">
        <option value="0">{$CMS->lang['hair_category_1']}</option>
        <option value="1">{$CMS->lang['hair_category_0']}</option>
      </select>
    </div>
  </div>
EOF;
    return $output;
  }



}

?>