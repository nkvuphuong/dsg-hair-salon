<?php
use core\ezy;
class skin_config_parent_news {

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
      <h3>{$CMS->lang['header']}</h3>
      <figure class="pull-right right">
EOF;
      if($CMS->permit['config_parent_news_add'])
      {

$output .=<<<EOF

        <a href="{$CMS->vars['root_domain']}/?site=config_parent_news&act=add" title="" class="add_bill">{$CMS->lang['add_form']}</a>
EOF;
      }

$output .=<<<EOF
        <a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a>
      </figure>
    </figure>

<section class="tabs-section">
    <div class="tabs-section-nav tabs-section-nav-inline">
        <ul class="nav" role="tablist">
            <li class="nav-item">
                <a class="nav-link" href="{$CMS->vars['root_domain']}/?site=news">
                    {$CMS->lang['list_news_header']}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active">
                    {$CMS->lang['list_category_header']}
                </a>
            </li>
        </ul>
    </div><!--.tabs-section-nav-->
</section><!--.tabs-section-->
{$CMS->global->languageTab('langTab')}

<form method="post" name="config_parent_news" id="config_parent_news" action="{$CMS->vars['root_domain']}/?site=config_parent_news" onSubmit="return check_form(this.id);">
    
        <section class="add_table">
          <div class="table-responsive">
            <table class="table table_cus table-hover">
              <thead>
                <tr>
                 <th width="5%" class="table-check">
                    <div class="checkbox checkbox-only" onclick="javascript:form_checkall('config_parent_news');" id="checkall">
                   <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
                   <label for="id_{$result['record_cnt']}"></label>
                    </div>
                  </th>
                  <th width="6%" style="text-align:center" id="order_cat_id">{$CMS->lang['cat_id']}</th>
                  <th width="20%" id="order_cat_name">{$CMS->lang['cat_name']}</th>
                  <th width="15%">{$CMS->lang['title_parent_menu']}</th>
                  <th width="15%" style="text-align:center" id="order_cat_status">{$CMS->lang['cat_status']}</th>
                  
                  <th width="17%" style="text-align:center" id="order_cat_time">{$CMS->lang['cat_time']}</th>
                  <th width="5%" style="text-align:center"></th>
                </tr>
              </thead>
              <tbody>

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
 // print "<pre>";print_r($result);exit;
  $btn_control = "";
  if($CMS->permit['config_parent_news_read'])
  {
    $btn_control .=<<<EOF
           <a href="{$CMS->vars['root_domain']}/?site=config_parent_news&act=edit&id={$result['cat_id']}" title""="" class="edit"><i class="fa fa-edit"></i></a>
EOF;

  }

  if($CMS->permit['config_parent_news_edit'])
  {
    $btn_control .=<<<EOF
        <a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=config_parent_news&act=delete&id={$result['cat_id']}');"   class="edit"><i class="fa fa-trash-o"></i></a>
EOF;

  }
$output .= <<<EOF
 <tr bgcolor="{$result['bgcolor']}">
 <td class="table-check">
 <div class="checkbox checkbox-only">
                      <input type="checkbox"  name="id_{$result['record_cnt']}" id="id_{$result['record_cnt']}" value="{$result['cat_id']}"/>
                      <label for="id_{$result['record_cnt']}"></label>
                    </div>
      </td>

    <td class="table-check">
                    <select class="form-control" name="order_{$result['cat_id_bk']}">
  <script language="javascript">

    for ( var i = 1; i <= {$CMS->config_parent_news->pages_cnt}; i ++ )
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
                <td class="langTab" lang="{$langCode}">{$result['parent_category'][$langCode]}</td>
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
                <td>{$result['cat_name_bk']}</td>
                <td>{$result['parent_category']}</td>
EOF;
        }
$output .= <<<EOF
    
    <td style="text-align:center">{$result['cat_status']}</td>
    <td style="text-align:center">{$result['cat_time']}</td>
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
    <td colspan="8">{$CMS->lang['no_data']}</td>
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
              </tbody>
            </table>
          </div>
          <input type="hidden" name="data_cnt" value="{$CMS->config_parent_news->record_cnt}">
      </section><!--.box-typical-->
{$CMS->config_parent_news->action_control}
<div class="block_bottom pagination pagination-sm">{$CMS->show_page}</div>
 </form> 
</section>
<script language="javascript">rebuild_form("config_parent_news");</script>
<script language="javascript">arrange_setup("{$CMS->config_parent_news->arrange_data}");</script>
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
<div class="block_action">
	
	<div class="block_action_left">
       
    </div>
</div>
EOF;

    return $output;
}

//===========================================================================
//  HTML EDIT
//===========================================================================

public function edit( $data )
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF


<form method="post" id="config_parent_news" name="config_parent_news" action="{$CMS->vars['root_domain']}/?site=config_parent_news&id={$data['cat_id']}&act=edit_do&page={$CMS->input['page']}" onSubmit="return check_form(this.id);">

 <section class="add_form main_form">
  <figure class="heading">
    <h3>{$CMS->lang['edit_form']}</h3>
      <a href="{$CMS->vars['root_domain']}/?site=config_parent_news{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
  </figure>
{$CMS->global->languageTab('langTab')}
<figure class="box-typical box-typical box-typical-padding border">
  <div class="row">
      <div class="col-lg-6">
EOF;
        $cat_name = isset($CMS->input['cat_name']) ? $CMS->input['cat_name'] : $data['cat_name'];
        $cat_description = isset($CMS->input['cat_description']) ? $CMS->input['cat_description'] : $data['cat_description'];

        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $validate_name = $langCode == $CMS->vars['default_language'] ? "data-validation=\"[NOTEMPTY]\" data-validation-message=\"{$CMS->lang['incomplete_name']}\"" : "";

                $output .= <<<EOF
                <div class="form-group row langTab" lang="{$langCode}">
                  <label class="form-control-label">{$CMS->lang['cat_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <input class="form-control" type="text" name="cat_name[{$langCode}]" id="cat_name[{$langCode}]" value="{$cat_name[$langCode]}" {$validate_name}>
                  </div>
                </div>
                <div class="form-group row langTab" lang="{$langCode}">
                  <label class="form-control-label">{$CMS->lang['cat_description']}</label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                      <textarea class="form-control editor_texarea" name="cat_description[{$langCode}]" cols="60" rows="5">{$cat_description[$langCode]}</textarea>
                  </div>
                </div>  
        
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
            
                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['cat_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <input class="form-control" type="text" name="cat_name" value="{$data['cat_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['incomplete_name']}">
                  </div>
                </div>
                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['cat_description']}</label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                      <textarea class="form-control editor_texarea" name="cat_description"  cols="60" rows="5">{$data['cat_description']}</textarea>
                  </div>
                </div>  
EOF;
        }


    $output .= <<<EOF
            <div class="form-group row">
              <label class="form-control-label"></label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                 <button class="btn btn-rounded" type="submit" class="btn">{$CMS->lang['edit_submit']}</button>
              </div>
            </div>
          
      </div><!----end col-lg-6 ----->

      <div class="col-lg-6">
EOF;
          if( in_array(ezy::$theme_key, array("wco", "fco", "nms")) )
          {
$output .=<<<EOF

            <div class="form-group row">
              <label class="form-control-label">{$CMS->lang['title_news_cat_type']}</label>
              <div class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                 <select class="form-control auto_select" name="cat_type" defaultvalue="{$data['cat_type']}">
                    <option value="1">{$CMS->lang['cat_type_1']}</option>
                    <option value="2">{$CMS->lang['cat_type_2']}</option>
                 </select>
              </div>
            </div>
EOF;
          }
$output .=<<<EOF
    <div class="row">
        <div class="col-lg-6">
           <div class="form-group row">
            <label class="form-control-label">key</label>
            <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
              <input type="text" name="cat_key" class="form-control" value="{$data['cat_key']}">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label class="form-control-label">{$CMS->lang['cat_cate']}</label>
                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <select class="form-control auto_select" name="parent_id" defaultvalue="{$data['parent_id']}">
                    <option value="">{$CMS->lang['select_category']}</option>
                    {$CMS->config_parent_news->load_all_cate($data['cat_id'])}
                  </select>
                  
                </div>
              </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group row">
                  <label class="form-control-label" >{$CMS->lang['cat_display']}</label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                      <select class="form-control" name="cat_status" defaultvalue="{$data['cat_status_bk']}">{$CMS->vars['display_status']}</select>
                </div>
              </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
              <label class="form-control-label">{$CMS->lang['cat_order']}</label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                <input class="form-control" type="number" name="cat_order" value="{$data['cat_order']}" />
              </div>
            </div>
        </div>
      </div>

            <div class="form-group row">
              <label class="form-control-label">{$CMS->lang['meta_title']}</label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                 <textarea class="form-control content_textarea" name="meta_title">{$data['meta_title']}</textarea>
              </div>
             
            </div>


            <div class="form-group row">
              <label class="form-control-label">{$CMS->lang['meta_description']}</label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                 <textarea class="form-control content_textarea" name="meta_description">{$data['meta_description']}</textarea>
              </div>
              
            </div>

            <div class="form-group row">
              <label class="form-control-label">{$CMS->lang['meta_keywords']}</label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                 <textarea class="form-control content_textarea" name="meta_keywords">{$data['meta_keywords']}</textarea>
              </div>
            </div>
      </div><!----end col-lg-6 ----->
    
      </div> 
    </figure>
 </section><!--card -->     

                      
</form>
<script language="javascript">rebuild_form("config_parent_news",1);</script>
<script>
  $(document).ready(function(){
        validate_form_custom("#config_parent_news");
  });
</script>
EOF;
	
	return $output;	
}

//===========================================================================
//  HTML ADD
//===========================================================================

public function add( $data )
{
	global $CMS, $DB, $member;
	
	$output = "";
$data['cat_status'] = $data['cat_status'] ? $data['cat_status'] : 1;
$data['parent_id'] = $data['parent_id'] ? $data['parent_id'] : 0;
$output .= <<<EOF

<form method="post" id="config_parent_news" name="config_parent_news" action="{$CMS->vars['root_domain']}/?site=config_parent_news&act=add_do" onSubmit="return check_form(this.id);">

<section class="add_form main_form">
  <figure class="heading">
    <h3>{$CMS->lang['add_form']}</h3>
      <a href="{$CMS->vars['root_domain']}/?site=config_parent_news{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
  </figure>
{$CMS->global->languageTab('langTab')}
<figure class="box-typical box-typical box-typical-padding border">
  <div class="row">
      <div class="col-lg-6">
EOF;
        $cat_name = isset($CMS->input['cat_name']) ? $CMS->input['cat_name'] : $data['cat_name_bk'];
        $cat_description = isset($CMS->input['cat_description']) ? $CMS->input['cat_description'] : $data['cat_description'];

        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $validate_name = $langCode == $CMS->vars['default_language'] ? "data-validation=\"[NOTEMPTY]\" data-validation-message=\"{$CMS->lang['incomplete_name']}\"" : "";

                $output .= <<<EOF
                <div class="form-group row langTab" lang="{$langCode}">
                  <label class="form-control-label">{$CMS->lang['cat_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <input class="form-control" type="text" name="cat_name[{$langCode}]" id="cat_name[{$langCode}]" value="{$cat_name[$langCode]}" {$validate_name}>
                  </div>
                </div>
                <div class="form-group row langTab" lang="{$langCode}">
                  <label class="form-control-label">{$CMS->lang['cat_description']}</label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                      <textarea class="form-control editor_texarea" name="cat_description[{$langCode}]" cols="60" rows="5">{$cat_description[$langCode]}</textarea>
                  </div>
                </div>  
        
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
            
                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['cat_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <input class="form-control" type="text" name="cat_name" value="{$data['cat_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['incomplete_name']}">
                  </div>
                </div>
                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['cat_description']}</label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                      <textarea class="form-control editor_texarea" name="cat_description"  cols="60" rows="5">{$data['cat_description']}</textarea>
                  </div>
                </div>  
EOF;
        }


    $output .= <<<EOF
            <div class="form-group row">
              <label class="form-control-label"></label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                 <button class="btn btn-rounded" type="submit" class="btn">{$CMS->lang['add_submit']}</button>
              </div>
            </div>
          
      </div><!----end col-lg-6 ----->

      <div class="col-lg-6">
EOF;
          if( in_array(ezy::$theme_key, array("wco", "fco", "nms")) )
          {
$output .=<<<EOF

            <div class="form-group row">
              <label class="form-control-label">{$CMS->lang['title_news_cat_type']}</label>
              <div class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                 <select class="form-control auto_select" name="cat_type" defaultvalue="{$data['cat_type']}">
                    <option value="1">{$CMS->lang['cat_type_1']}</option>
                    <option value="2">{$CMS->lang['cat_type_2']}</option>
                 </select>
              </div>
            </div>
EOF;
          }
$output .=<<<EOF
    <div class="row">
        <div class="col-lg-6">
          <div class="form-group row">
            <label class="form-control-label">Key</label>
            <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
              <input type="text" class="form-control" name="cat_key" value="{$data['cat_key']}">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group row">
            <label class="form-control-label">{$CMS->lang['cat_cate']}</label>
            <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
              <select class="form-control auto_select" name="parent_id" defaultvalue="{$data['parent_id']}">
                <option value="">{$CMS->lang['select_category']}</option>
                {$CMS->config_parent_news->load_all_cate()}
              </select>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group row">
                  <label class="form-control-label" >{$CMS->lang['cat_display']}</label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                      <select class="form-control" name="cat_status" defaultvalue="{$data['cat_status']}">{$CMS->vars['display_status']}</select>
                </div>
              </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
              <label class="form-control-label">{$CMS->lang['cat_order']}</label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                <input class="form-control" type="number" name="cat_order" value="{$data['cat_order']}" />
              </div>
            </div>
        </div>
    </div>


            <div class="form-group row">
              <label class="form-control-label">{$CMS->lang['meta_title']}</label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                 <textarea class="form-control content_textarea" name="meta_title">{$data['meta_title']}</textarea>
              </div>
            </div>


            <div class="form-group row">
              <label class="form-control-label">{$CMS->lang['meta_description']}</label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                 <textarea class="form-control content_textarea" name="meta_description">{$data['meta_description']}</textarea>
              </div>
              
            </div>

            <div class="form-group row">
              <label class="form-control-label">{$CMS->lang['meta_keywords']}</label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                 <textarea class="form-control content_textarea" name="meta_keywords">{$data['meta_keywords']}</textarea>
              </div>
            </div>
      </div><!----end col-lg-6 ----->
    
      </div> 
    </figure>
 </section><!--card -->     


</form>
<script language="javascript">rebuild_form("config_parent_news",1);</script>
<script>
  $(document).ready(function(){
        validate_form_custom("#config_parent_news");
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
    
$output .= <<<EOF
<header class="section-header">
        <div class="tbl">
          <div class="tbl-row">
            <div class="tbl-cell">
              <h3>{$CMS->lang['title_header_show']}</h3>            
            </div>
          </div>
        </div>
      </header>
  {$CMS->global->languageTab('langTab')}
 <section class="card">

      <section class="box-typical">
        <header class="box-typical-header">
            <div class="tbl-row">
                <div class="tbl-cell tbl-cell-title">
                    <h3>{$CMS->lang['show_form']} 
EOF;

        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $output .= <<<EOF
                <strong class="langTab" lang="{$langCode}">{$data['cat_name_bk'][$langCode]}</strong>
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
                <strong>{$data['cat_name_bk']}</strong>
EOF;
        }
$output .= <<<EOF

                    </h3>
                </div>
                <div class="tbl-cell tbl-cell-action-bordered">
                    <a href="{$CMS->vars['root_domain']}/?site=config_parent_news{$CMS->class->search->url_return}">
                    <button type="button" class="action-btn"><i class="fa fa-mail-reply"></i></button>
                    </a>
                </div>       
            </div>
        </header>
	</section>



          <div class="card-block">
            <h5 class="with-border">{$CMS->lang['required_info']}</h5>
              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['cat_name']}</label>
                <div class="col-sm-9">
                <p class="form-control-static">
EOF;

        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $output .= <<<EOF
                <span class="langTab" lang="{$langCode}">{$data['cat_name_bk'][$langCode]}</span>
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
                <span>{$data['cat_name_bk']}</span>
EOF;
        }

$output .= <<<EOF
                   ({$data['parent_category'][$CMS->vars['default_language']]})
                  &nbsp; <script type="text/javascript">permission_btn("edit", "config_parent_news", "{$CMS->vars['root_domain']}/?site=config_parent_news&act=edit&id={$data['data_bk']['cat_id']}");</script>
                    &nbsp; <script type="text/javascript">permission_btn("delete", "config_parent_news", "{$CMS->vars['root_domain']}/?site=config_parent_news&act=delete&id={$data['data_bk']['cat_id']}");</script>
                </p>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-3 form-control-label">Link</label>
                <div class="col-sm-9">
                <p class="form-control-static">
                     {$data['cat_url']}
                </p>
                </div>
              </div>
EOF;

        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
              $data['cat_description'][$langCode] = html_entity_decode($data['cat_description'][$langCode]);
                $output .= <<<EOF
                <div class="form-group row langTab" lang="{$langCode}">
                  <label class="col-sm-3 form-control-label">{$CMS->lang['cat_description']}</label>
                  <div class="col-sm-9">
                  <p class="form-control-static">
                       {$data['cat_description'][$langCode]}
                  </p>
                  </div>
                </div>
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
                <div class="form-group row">
                  <label class="col-sm-3 form-control-label">{$CMS->lang['cat_description']}</label>
                  <div class="col-sm-9">
                  <p class="form-control-static">
                       {$data['cat_description']}
                  </p>
                  </div>
                </div>
EOF;
        }
$output .= <<<EOF
              


               <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['cat_limit_aa']}</label>
                <div class="col-sm-9">
                <p class="form-control-static">
                     {$data['cat_limit']} tin
                </p>
                </div>
              </div>


              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['cat_sql']}</label>
                <div class="col-sm-9">
                <p class="form-control-static">
                     {$data['cat_sql']} tin
                </p>
                </div>
              </div>



              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['list_sub_menu']}</label>
                <div class="col-sm-9">
                <p class="form-control-static">
                     {$data['list_submenu']}
                </p>
                </div>
              </div>


              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['cat_time']}</label>
                <div class="col-sm-9">
                <p class="form-control-static">
                     {$data['cat_time']}
                </p>
                </div>
              </div>

            <h5 class="with-border">{$CMS->lang['add_info']}</h5>

             <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['cat_status']}</label>
                <div class="col-sm-9">
                <p class="form-control-static">
                     {$data['cat_status']}
                </p>
                </div>
              </div>



              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['cat_display_home']}</label>
                <div class="col-sm-9">
                <p class="form-control-static">
                     {$data['cat_display_home']}
                </p>
                </div>
              </div>



              <div class="form-group row">
                  <label class="col-sm-3 form-control-label">{$CMS->lang['meta_title']}</label>
                  <div class="col-sm-9">
                  <p class="form-control-static">
                       {$data['meta_title']}
                  </p>
                  </div>
                </div>


              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['meta_description']}</label>
                <div class="col-sm-9">
                <p class="form-control-static">
                     {$data['meta_description']}
                </p>
                </div>
              </div>


              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['meta_keywords']}</label>
                <div class="col-sm-9">
                <p class="form-control-static">
                     {$data['meta_keywords']}
                </p>
                </div>
              </div>



             </div><!-- end card-block -->

  </section><!-- end section card -->


EOF;
	
	return $output;	
}





}

?>